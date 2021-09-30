<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\TelesaleScheduleCall;
use App\models\Telesales;
use Validator;
use Carbon\Carbon;
use DB;
use App\Jobs\CreateTwilioTask;
use App\models\ClientTwilioNumbers;
use App\models\ClientWorkflow;
use App\Services\TwilioService;
use Auth;
use App\models\TwilioConnectedDevice;
// use Mail;
// use App\Mail\SendEmailNotVerify;
use App\Traits\ScheduleCallTrait;
use App\models\Settings;
use App\models\SettingTPVnowRestrictedTimeZone;


class ScheduleCallController extends Controller
{

    use ScheduleCallTrait;

    /**
     * This API method used to store data of tpv outbound schedule call
     */
    public function scheduleCall(Request $request)
    {
        try
        {
            $call_date_err = "";
            $validator = Validator::make($request->all(), [
                'call_now' => 'required',
                'call_lang' => 'required',
                'telesale_id' => 'required',
                'call_date' => 'date_format:m/d/Y H:i:s'

            ]);
            
            if ($validator->fails()) {
                if($request->call_now == false && !isset($request->call_date))
                {
                    $call_date_err = "The call date field is required";
                }
                $validation_error_msg =  implode(',', $validator->messages()->all());
                $validation_error_msg .= ",".$call_date_err;

                return $this->error("error",$validation_error_msg, 400);

            }

            //For checking call_date field is null or not
            else if(!isset($request->call_date) && $request->call_now == false)
            {
                return $this->error("error", "The call date field is required.", 400);

             }

            else
            {

                $scheduleCall = TelesaleScheduleCall::where('telesale_id', $request->telesale_id)->where('call_immediately', "yes")->orderBy('attempt_no', 'desc')->first();

                // if (!empty($scheduleCall)) {
                //   $attemptNo = $scheduleCall->attempt_no + 1;
                // } else {
                //   $attemptNo = 1;
                // }
                if (!empty($scheduleCall)) {
                    \Log::info('<pre>');
                    \Log::info(TelesaleScheduleCall::where('telesale_id', $request->telesale_id)->get()->toArray());
                  // $rescheduleCall = $this->rescheduleCall($request->telesale_id);
                  // if ($rescheduleCall !== false) {
                  //   \Log::info("Your call has been rescheduled for lead: " . $request->telesale_id);
                  //   return $this->success("success", "Your call has been rescheduled. You will get verification call soon from TPV360.");
                  // } else {
                  //   \Log::error("This call can not be rescheduled.");
                  //   return $this->error("error", "This call can not be rescheduled.", 500);
                  // }

                  return $this->error("error", "You have already registered for TPV Now !!", 500);

                } else {
                  $lead = Telesales::find($request->telesale_id);

                    if(!isOnSettings(array_get($lead,'client_id'),'is_enable_outbound_tpv')) {
                        $msg = "Outbound TPV is switched off. Please contact your administrator for assistance.";
                        return $this->error("error", $msg, 400);
                    }

                  // check if lead is in pending state
                  if($lead->status != config()->get('constants.LEAD_TYPE_PENDING')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Lead is not in pending state. Can't accept the TPV Now request."
                    ], 400);
                  }

                  if($request->call_now == true)
                  {
                    $delayTime = Settings::where('client_id',$lead->client_id)->get(['tpv_now_call_delay']);
                    $delayTime = explode(',',$delayTime[0]->tpv_now_call_delay);
                    // $telesaleScheduleCall->call_time = Carbon::now()->addMinutes($delayTime[0]);
                    $scheduleTime = Carbon::now()->addMinutes($delayTime[0]);
                  }
                  else
                  {
                      $scheduleTime = Carbon::parse($request->call_date);
                    //   $telesaleScheduleCall->call_time = Carbon::parse($request->call_date);//->format('d-m-Y H:i:s');
                  }

                $leadState = $lead->zipcodes()->first();
                \Log::debug("leadState: " . $leadState);
	            $clientStateRestriction = SettingTPVnowRestrictedTimeZone::where('client_id',$lead->client_id)->where('state',strtoupper($leadState->state))->first();
	            if(isset($clientStateRestriction) && !empty($clientStateRestriction)) {
		            \Log::info('Client state restriction for tpv now on lead '.$lead->id);
		            $scheduleTime = Carbon::parse($scheduleTime)->setTimezone($clientStateRestriction->timezone);
		            $startTime = Carbon::parse($clientStateRestriction->start_time,$clientStateRestriction->timezone);
                    $endTime = Carbon::parse($clientStateRestriction->end_time,$clientStateRestriction->timezone);
                    \Log::info("Schedule time ".$scheduleTime);
                    \Log::info("Start time ".$startTime);
                    \Log::info("End time ".$endTime);
		            if(!($scheduleTime >= $startTime && $scheduleTime <= $endTime))
		            {
			            \Log::info("Rescheduled time is more than client's Restricted state's start time and end time.");
			            $cancelLead = $this->expiredLeadWithoutSendingMails($lead->id);
			            if ($cancelLead) {
				            \Log::error("rescheduleCall: Lead registered as a cancel lead for id: " . $lead->id);
			            } else {
				            \Log::error("rescheduleCall: Unable to register lead as a cancel lead for id: " . $lead->id);
			            }
			            return response()->json([
                            'status' => 'error',
                            'message' => "Lead is not allowed to schedule at this time."
                        ], 400);
		            }
		        }

                  $attemptNo = 1;
                  $telesaleScheduleCall = new TelesaleScheduleCall();
                  $telesaleScheduleCall->telesale_id = $request->telesale_id;
                  $telesaleScheduleCall->call_immediately = ($request->call_now == true) ? "yes": "no";
                  $telesaleScheduleCall->call_lang = $request->call_lang;
                  $telesaleScheduleCall->call_type = 'outbound';
                  $telesaleScheduleCall->schedule_status = "pending";
                  $telesaleScheduleCall->attempt_no = $attemptNo;
                  $telesaleScheduleCall->call_time = $scheduleTime;
                  if($request->call_now == true)
                  {
                    $delayTime = Settings::where('client_id',$lead->client_id)->get(['tpv_now_call_delay']);
                    $delayTime = explode(',',$delayTime[0]->tpv_now_call_delay);
                    // $telesaleScheduleCall->call_time = Carbon::now()->addMinutes($delayTime[0]);
                  }
                  else
                  {
                    //   $telesaleScheduleCall->call_time = Carbon::parse($request->call_date);//->format('d-m-Y H:i:s');
                  }
                  $insertFlag =  $telesaleScheduleCall->save();
                  
                  if($insertFlag)
                  {
                    \Log::info("Successfully data inserted for tpv schedule call.");
                    \Log::info($delayTime[0]);
                    if($request->call_now == true)
                    {
                        if($delayTime[0] == 0){
                            \Log::info($delayTime[0]);
                            //Register non-critical logs for TPV Now.
                            $this->registerLogsForTPVNow($lead);

                            $scheduleCallId = $telesaleScheduleCall->id;

                            // Removed the below as this may create a race condition between 
                            // reschedule call handler and this queue
                            //CreateTwilioTask::dispatch($scheduleCallId,$lead->client_id,$request->call_lang, $lead->refrence_id, $lead->id);
                        }
                        // $workers = TwilioConnectedDevice::where('workers_online','!=','[]')->get();
                        //
                        // $onlineWorker = $workers->count();
                        // $calls = TelesaleScheduleCall::where('schedule_status','pending')
                        //             ->where('call_immediately','yes')->get();
                        // if($onlineWorker != 0)
                        // {
                        //
                        //     $totalCalls = $calls->count();
                        //     if($totalCalls != 0)
                        //     {
                        //         $delay  = floor($totalCalls/$onlineWorker);
                        //     }
                        //     else
                        //     {
                        //         $delay = 0;
                        //     }
                        //
                        //     $call_estimation = $delay * config('constants.CALL_DURATION');
                        //
                        //     if($call_estimation == 0)
                        //     {
                        //         return $this->success("success","You will receive  call in few minutes");
                        //     }
                        //     else
                        //     {
                        //         \Log::info("call estimaton time: ".$call_estimation);
                        //         return $this->success("success","You will receive call after ".$call_estimation ." to ".($call_estimation + config('constants.CALL_DURATION'))." minutes");
                        //     }
                        // }
                        // else
                        // {
                        //     \Log::info("No workers are online");
                        //     return $this->success("success","Currently no Agents are available.");
                        // }

                    }

                    \Log::info("schedule call details stored");
                    return $this->success("success", "Your TPV Now request was submitted successfully. The customer will receive a call shortly.");
                }
                else
                {
                    \Log::error("Error while saving data of schedule call. ");
                    return $this->error("error", "Something went wrong while saving data of schedule call.", 400);
                }
            }
          }
        }catch (Exception $e) {

            \Log::error("Internal server error: " . $e->getMessage());
            return $this->error("error", "Something went wrong.", 500);
        }
    }


}
