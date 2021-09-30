<?php

namespace App\Http\Controllers\AgentPanel\TPVAgent;

use App\Http\Controllers\Calls\CallsController;
use App\Http\Controllers\TPVAgent\RecordingController;
use App\models\Salescenter;
use App\models\Salescenterslocations;
use App\models\Telesales;
use App\models\Telesalesdata;
use App\models\TwilioConnectedDevice;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Client;
use App\models\ClientTwilioNumbers;
use App\Traits\LeadDataTrait;
use App\Services\TwilioService;
use App\models\TelesaleScheduleCall;
use App\Traits\ScheduleCallTrait;
use App\Mail\SendEmailNotVerify;
use Mail;
use Auth;
use App\Jobs\DisconnectLeadUpdate;
use Carbon\Carbon;
use DB;
use App\models\TwilioLeadCallDetails;
use App\models\WorkerReservationDetails;
use App\models\TwilioCurrentActivityOfWorker;
// use App\models\ClientWorkflow;
use App\models\TwilioStatisticsWorkers;
use App\models\TwilioStatisticsWorkersActivityduration;
use App\models\TwilioStatisticsWorkflow;
use App\models\TwilioStatisticsTaskqueue;
use App\models\TwilioStatisticsWorkspace;
use App\models\TwilioWorkspaceActivityStatistics;
use App\models\TwilioStatisticsSpecificWorker;
use App\models\TwilioStatisticsSpecificWorkerActivity;
use App\models\TwilioStatisticsCallLogs;
use App\models\TwilioStatisticsUsageRecords;
use App\models\UserTwilioId;
use App\models\TwilioActivityOfWorker;

class TwilioController extends Controller
{
    use LeadDataTrait, ScheduleCallTrait;

    public function __construct() {
      $this->twilioService = new TwilioService;
    }

    /**
     * For Receive callback from Twilio workflow
     */
    public function workflowAssignment(Request $request) {
        $input = $request->all();

        if (isset($input['EventType'])) {
            \Log::info("workflowAssignment:" . $input['EventType']);
        } else {
            \Log::info("workflowAssignment: Not get any event type in request !!");
        }

        $fromNumber =  config('services.twilio')['number'];
        $attributes = [];
        if (isset($input['TaskAttributes'])) {
            $attributes = json_decode($input['TaskAttributes']);
            if (isset($attributes->to)) {
                $fromNumber = $attributes->to;
            }
        }

        $assignedWorker = $request->input("WorkerSid");

        $connectedDevices =   (new TwilioConnectedDevice)->connectedDevice();
        $callToDeviceId = "";
        if(count($connectedDevices) > 0) {
            foreach($connectedDevices as $singleDevice) {
                $singleDevice->device_id;
                $workersOnline = json_decode($singleDevice->workers_online);
                if(in_array($assignedWorker,$workersOnline)){
                    $callToDeviceId = $singleDevice->device_id;
                    break;
                }
            }
        }

       $isReject = false;
        $url = "twilio.inbound-call-twiml";
        $params = [
          'rid' => $input['ReservationSid'],
          'task_id' => $input['TaskSid'],
          'workspace_id' =>$request->input('WorkspaceSid')
        ];
        \Log::debug("Assignment: 1.0:" . print_r($input['TaskAttributes'], true));
        if (isset($input['TaskAttributes'])) {
            $attributes = json_decode($input['TaskAttributes']);
            if (isset($attributes->type) && ($attributes->type == config()->get('constants.TWILIO_CALL_TYPE_OUTBOUND') || $attributes->type == config()->get('constants.SCHEDULE_CALL_TYPE_OUTBOUND_DISCONNECT') || $attributes->type == config()->get('constants.TWILIO_CALL_TYPE_SELFVERIFIED_CALLBACK'))) {
              $url = "twilio.outbound-call-twiml";
              if (isset($attributes->lead_id)) {
                $params['lead_id'] = $attributes->lead_id;

                // Check if lead is exists for given lead id
               $lead = Telesales::where('refrence_id', $attributes->lead_id)->first();
               if (empty($lead)) {
                   \Log::error("Lead not found with reference id: " . $attributes->lead_id);
               }

                // Check if lead is in pending or disconnected state or not, if not then reject call instruction
               if (in_array(array_get($lead, 'status'), array(config()->get('constants.LEAD_TYPE_PENDING'), config()->get('constants.LEAD_TYPE_DISCONNECTED'), config()->get('constants.LEAD_STATUS_SELF_VERIFIED')))) {
                   $isReject = false;
               } else {
                   $isReject = true;
                   \Log::error("Reservation rejected flag true due to lead status: " . array_get($lead, 'status') . " with id " . $lead->id);
               }


               \Log::debug("2.0:" . print_r($params, true));
              } else {
                \Log::error("Lead id not available in outbound call task attributes.");
              }
            }
        }

       if ($isReject) {
           $deleteTask = $this->twilioService->deleteTask($request->input('WorkspaceSid'), $input['TaskSid']);

           // Updating the task id to skipped
           TelesaleScheduleCall::where('task_id', $input['TaskSid'])->update([
                  'schedule_status' => config()->get('constants.SCHEDULE_TASK_SKIP_STATUS')
           ]);

           $assignmentInstruction  = [
               'instruction' => 'reject'
           ];

           \Log::info(print_r($deleteTask, true));
       } else {
          
            $assignmentInstruction  = [
                'instruction' => 'call',
                'from' => $fromNumber,
                'to' => 'client:'.$callToDeviceId,
                'timeout' => 45,
                'url' => route($url, $params),
            ];
       }
       \Log::info("Assignment Instruction...");
       \Log::info($assignmentInstruction);
        return response($assignmentInstruction, 200)->header('Content-Type', 'application/json');
    }

    /**
     * For Receive Callbacks from Twilio workspace
     */
    public function workspaceAssignment(Request $request) {
		$input = $request->all();
		\Log::debug("Workspace assignment: ---------------------------------------------------------------- ");
		\Log::debug('Workspace assignment: Event: '.$input['EventType']);
		\Log::debug('Workspace assignment: '.json_encode($input, JSON_PRETTY_PRINT));
		\Log::debug("Workspace assignment: Call ID: ");
		if(isset($input['TaskAttributes']) && !empty($input['TaskAttributes'])) {
			$jsonArray = json_decode($input['TaskAttributes']);
			//When reservation is created this condition will become true 
			if (isset($input['EventType']) && $input['EventType'] == "task.created") {
        $fetchClient = ClientTwilioNumbers::where('phonenumber',$jsonArray->to)->select('client_id','type')->first();
        $clientId = '';
        $type = '';
        $leadId = null;
        \Log::info($fetchClient);
        if(!empty($fetchClient)){
          $clientId = $fetchClient->client_id;
          if($jsonArray->type == 'inbound'){
            if($fetchClient->type == config()->get('constants.TWILIO_PHONE_NUMBER_TYPE.AGENT_INBOUND_NUMBER')){
              $type = config()->get('constants.VERIFICATION_METHOD_FOR_REPORT.Agent Inbound');
            }
            if($fetchClient->type == config()->get('constants.TWILIO_PHONE_NUMBER_TYPE.CUSTOMER_INBOUND_NUMBER')){
              $type = config()->get('constants.VERIFICATION_METHOD_FOR_REPORT.Customer Inbound');
            }
          }
        }
        if($jsonArray->type == 'outbound' || $jsonArray->type == config()->get('constants.SCHEDULE_CALL_TYPE_OUTBOUND_DISCONNECT') || $jsonArray->type == config()->get('constants.TWILIO_CALL_TYPE_SELFVERIFIED_CALLBACK') ){
          $type = config()->get('constants.VERIFICATION_METHOD_FOR_REPORT.TPV Now Outbound');
          $leadId = (new Telesales)->getLeadID($jsonArray->lead_id);
          $leadId = $leadId->id;
        }
				\Log::debug("Workspace assignment: Task Created.");
        \Log::info($leadId);
				$date =  new \DateTime();
				$date->setTimestamp($input['Timestamp']);
				$twilioCallDetails = new TwilioLeadCallDetails();
				$twilioCallDetails->task_id = $input['TaskSid'];
        $twilioCallDetails->call_type = $type;
        $twilioCallDetails->lead_id = $leadId;
        $twilioCallDetails->task_created_time = $date;
        $twilioCallDetails->client_id = $clientId;
				$twilioCallDetails->current_task_status = $input['TaskAssignmentStatus'];
				$twilioCallDetails->save();
			}
			//Store wrapup time when task is in wrapup mode
			if(isset($input['EventType']) && $input['EventType'] == "task.wrapup"){
				$twiloDetails = TwilioLeadCallDetails::where('task_id',$input['TaskSid'])->first();
				if(!empty($twiloDetails)){
					$date =  new \DateTime();
					$date->setTimestamp($input['Timestamp']);
					$twiloDetails->task_wrapup_start_time = $date;
					$twiloDetails->current_task_status = $input['TaskAssignmentStatus'];
					$twiloDetails->save();
        }

			}
			//Store completed time when task is completed
			if(isset($input['EventType']) && $input['EventType'] == "task.completed"){
				$twiloDetails = TwilioLeadCallDetails::where('task_id',$input['TaskSid'])->first();
				if(!empty($twiloDetails)){
					$date =  new \DateTime();
					$date->setTimestamp($input['Timestamp']);
					$twiloDetails->task_completed_time = $date;
					$twiloDetails->current_task_status = $input['TaskAssignmentStatus'];
					$twiloDetails->save();
        }
			}
			//Store task assign status when task is updating
			if (isset($input['EventType']) && $input['EventType'] == "task.updated") {
				$twiloDetails = TwilioLeadCallDetails::where('task_id', $input['TaskSid'])->first();
				if (!empty($twiloDetails)) {
					$twiloDetails->current_task_status = $input['TaskAssignmentStatus'];
					$twiloDetails->save();
				}
      }
      
      //Store task assign status when task is deleted
			if (isset($input['EventType']) && $input['EventType'] == "task.deleted") {
				$twiloDetails = TwilioLeadCallDetails::where('task_id', $input['TaskSid'])->first();
				if (!empty($twiloDetails)) {
					$twiloDetails->current_task_status = $input['TaskAssignmentStatus'];
					$twiloDetails->save();
				}
      }
      
			//Store task status status when task is caneled
			if (isset($input['EventType']) && $input['EventType'] == "task.canceled") {
				$twiloDetails = TwilioLeadCallDetails::where('task_id', $input['TaskSid'])->first();
				if (!empty($twiloDetails)) {
					$date =  new \DateTime();
					$date->setTimestamp($input['Timestamp']);
					$twiloDetails->task_canceled_time = $date;
					$twiloDetails->current_task_status = $input['TaskAssignmentStatus'];
					$twiloDetails->save();
				}
			}
			//When reservation is created stores worker id and resercation details
			if(isset($input['EventType']) && $input['EventType'] == "reservation.created"){
				$twiloDetails = TwilioLeadCallDetails::where('task_id',$input['TaskSid'])->first();
				if(!empty($twiloDetails)){
					$twiloDetails->worker_id = $input['WorkerSid'];
					$twiloDetails->save();
				}
				$date =  new \DateTime();
				$date->setTimestamp($input['Timestamp']);
				$reservationDetails = new WorkerReservationDetails();
				$reservationDetails->task_id = $input['TaskSid'];
				$reservationDetails->reservation_id = $input['ReservationSid'];
				$reservationDetails->worker_id = $input['WorkerSid'];
				$reservationDetails->reservation_created_time = $date;
				$reservationDetails->reservation_status = 'created';
				$reservationDetails->save();
			}
			//When reservation is accepted stores call id and resercation details
			if(isset($input['EventType']) && $input['EventType'] == "reservation.accepted"){
				$twiloDetails = TwilioLeadCallDetails::where('task_id',$input['TaskSid'])->first();
				if(!empty($twiloDetails)){
					if($jsonArray->type == 'inbound'){
            $twiloDetails->call_id = $jsonArray->call_sid;
            $twiloDetails->worker_call_id = $jsonArray->worker_call_sid;
					}
					else{
						$twiloDetails->worker_call_id = $jsonArray->worker_call_sid;
					}
					$twiloDetails->worker_id = $input['WorkerSid'];
					$date = new \DateTime();
					$date->setTimestamp($input['Timestamp']);
					$twiloDetails->task_assigned_time = $date;
					$twiloDetails->save();
				}
				//stores reservation details
				$reservationDetails = WorkerReservationDetails::where('reservation_id',$input['ReservationSid'])->first();
				if(!empty($reservationDetails)){
					$reservationDetails->reservation_status = 'accepted';
					$reservationDetails->save();
				}
			}
			//When reservation is timeout stores resercation details
			if(isset($input['EventType']) && $input['EventType'] == "reservation.timeout"){
				//stores reservation details
				$reservationDetails = WorkerReservationDetails::where('reservation_id',$input['ReservationSid'])->first();
				if(!empty($reservationDetails)){
					$reservationDetails->reservation_status = 'timeout';
					$reservationDetails->save();
				}
			}
			//When reservation is rejected stores resercation details
			if(isset($input['EventType']) && $input['EventType'] == "reservation.rejected"){
				$twiloDetails = TwilioLeadCallDetails::where('task_id',$input['TaskSid'])->first();
				if(!empty($twiloDetails)){
					if($jsonArray->type == 'inbound'){
            $twiloDetails->call_id = $jsonArray->call_sid;
            $twiloDetails->worker_call_id = $jsonArray->worker_call_sid;
					}
					else{
						$twiloDetails->worker_call_id = $jsonArray->worker_call_sid;
					}
					$twiloDetails->worker_id = $input['WorkerSid'];
					$twiloDetails->save();
				}
				//stores reservation details
				$reservationDetails = WorkerReservationDetails::where('reservation_id',$input['ReservationSid'])->first();
				if(!empty($reservationDetails)){
					$reservationDetails->reservation_status = 'rejected';
					$reservationDetails->save();
				}
			}
      	}
		if (isset($input['EventType'])) {
			\Log::info("assignment:" . $input['EventType']);
		} else {
			\Log::info("assignment: Not get any event type in request !!");
		}

		if (isset($input['EventType']) && $input['EventType'] == "reservation.completed" && isset($input['TaskAssignmentStatus']) && $input['TaskAssignmentStatus'] == "completed") {
        // try{
            \Log::info('Start updating worker last call time');
            \Log::info('WorkspaceSid: '.$input['WorkspaceSid']);
            \Log::info('WorkspaceSid: '.$input['WorkerSid']);
            \Log::info($input['WorkerAttributes']);
            $WorkerAttributes = json_decode($input['WorkerAttributes'],true);
            $WorkerAttributes['last_call_time'] = Carbon::now()->timestamp;
            $toUpdateWorker['attributes'] = json_encode($WorkerAttributes);
            \Log::info($toUpdateWorker);
            $updateworker = $this->twilioService->updateWorker($input['WorkspaceSid'],$input['WorkerSid'],$toUpdateWorker);
            \Log::info('End updating worker last call time');
          // }
          // catch(Exception $e){
          //   \Log::info("Worker Update Error");
          // }


      // $telesales = Telesales::whereNotNull('call_id')->whereNotNull('twilio_recording_url')->whereNull('s3_recording_url')->where('recording_downloaded', 0)->get();
			// foreach ($telesales as $telesale) {
			//   (new CallsController())->downloadRecording($telesale->id);
			// }
      // $this->GetCallDuration();

		}

		if (isset($input['EventType']) && $input['EventType'] == "task.wrapup" && isset($input['TaskAssignmentStatus']) && $input['TaskAssignmentStatus'] == "wrapping") {
      $this->changeLeadStatus();
    }
    
    // For update worker's activity
    if (isset($input['EventType']) && $input['EventType'] == "worker.activity.update") {  
          
      $activityUpdateDetails = TwilioCurrentActivityOfWorker::firstOrNew(['worker_id' => $input['WorkerSid']]);
      $activityUpdateDetails->worker_id = isset($input['WorkerSid']) ? $input['WorkerSid'] : null;
      $activityUpdateDetails->worker_activity_id = isset($input['WorkerActivitySid']) ? $input['WorkerActivitySid'] : null;
      $activityUpdateDetails->worker_activity_name = isset($input['WorkerActivityName']) ? $input['WorkerActivityName'] : null;
      $activityUpdateDetails->save();

      $activityUpdate = new TwilioActivityOfWorker;
      $activityUpdate->worker_id = isset($input['WorkerSid']) ? $input['WorkerSid'] : null;
      $activityUpdate->worker_activity_id = isset($input['WorkerActivitySid']) ? $input['WorkerActivitySid'] : null;
      $activityUpdate->worker_activity_name = isset($input['WorkerActivityName']) ? $input['WorkerActivityName'] : null;
      $activityUpdate->save();

      \Log::info("Worker's activity updated successfully.");

    }

		return response()->json(null, 204);
		}

    /**
     * For Receive TwiML inbound call route and returns required instruction
     */
    public function inboundCallTwiML(Request $request) {
        \Log::info("Inbound call TwiML ReservationId: " . $request->input("rid"));
        $inputs = $request->all();
        return response('<Response>
                              <Dial record="record-from-answer-dual" recordingStatusCallback="'.route('recordingstatus').'?call_id='.$inputs['CallSid'].'&amp;tid='.$request->input("task_id").'&amp;workspace_id='.$request->input("workspace_id").'" >
                                    <Queue reservationSid="'.$request->input("rid").'"/>
                              </Dial>
                          </Response>', 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * For Receive TwiML outbound call route and returns required instruction
     */
    public function outboundCallTwiML(Request $request) {
        \Log::info("Outbound call TwiML function start");
        $inputs = $request->all();
        \Log::info("1.1 Outbound:" . print_r($inputs, true));
        if (isset($inputs['lead_id'])) {
          $callerId = $this->getCallerId($inputs['lead_id']);
          \Log::info("Caller Id:" . $callerId);
          $customerNumber = $this->getCustomerNumber($inputs['lead_id']);
          \Log::info("Customer number:" . $customerNumber);
          if ($callerId !== false && $customerNumber !== false) {
            $arrangeNumber = substr(join('-', str_split($customerNumber, 3)), 0, -2).substr(join('-', str_split($customerNumber, 3)), -1);
            return response('<Response>
                              <Dial>
                                <Queue reservationSid="'.$request->input("rid").'"/>
                              </Dial>
                              <Dial record="true" recordingStatusCallback="'.route('twilio.outbound-voice-recording.callback').'?leadId='.$inputs['lead_id'].'&amp;task_id='.$inputs["task_id"].'&amp;workspace_id='.$inputs["workspace_id"].'" recordingStatusCallbackEvent="completed" action="'. route('twilio.callbacks.outbound-voice-recording').'?leadId='.$inputs['lead_id'].'&amp;task_id='.$inputs["task_id"].'&amp;workspace_id='.$inputs["workspace_id"].'" callerId="'.$callerId.'" timeout="30">'.$arrangeNumber.'</Dial>
                          </Response>', 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
          } else {
            \Log::error("Could not find customer inbound number or customer phone number.");
            return response('<Response></Response>', 200)->header('Content-Type', 'application/xml; charset=utf-8');
          }
        } else {
          \Log::error("Lead id is required to dial call to customer");
          return response('<Response></Response>', 200)->header('Content-Type', 'application/xml; charset=utf-8');
        }
    }
    
    /**
     * This method used for execute when recordings are available for download.
     */
    public function outboundRecordingCallback(Request $request){
        \Log::info($request->all());
        \Log::info('In TPV outbound recording status call back event');
        $inputs = $request->all();
        $lead = Telesales::where('refrence_id',$inputs['leadId'])->first();
        //Update required attributes in telesales table
        $lead->update([
          'recording_id' => $inputs['RecordingSid'],
          'twilio_recording_url' => $inputs['RecordingUrl'],
          'call_duration' => $inputs['RecordingDuration'],
          'call_id' => $inputs['CallSid'],
        ]);

        //we need to store call related information in twilio lead call details table for each call
        $downloadRecording = Telesales::find($lead->id);
        $callDetails = TwilioLeadCallDetails::where('task_id',$inputs['task_id'])->first();
        if(!empty($callDetails)){
          $callDetails->twilio_recording_id = $inputs['RecordingSid'];
          $callDetails->call_duration = $inputs['RecordingDuration'];
          $callDetails->call_id = $inputs['CallSid'];
          // $callDetails->recording_url = $downloadRecording->s3_recording_url;
          $callDetails->twilio_recording_url = $inputs['RecordingUrl'];
          $callDetails->save();
        }

        // Now download the recording
        (new RecordingController())->downloadRecordingNew($inputs['task_id']);
    }
    // function downloadrecordings(){

    //     $recordings_to_download = (new Telesales)::select('call_id','twilio_recording_url')->where('recording_downloaded', 0)->whereNotNull('twilio_recording_url')->whereNotNull('call_id')->get();
    //     //  print_r($recordings_to_download);
    //     echo "started \n";
    //     if(count($recordings_to_download)> 0){
    //         foreach($recordings_to_download as $recordings_data){

    //             $download_file = $this->downloadfile($recordings_data['twilio_recording_url']);

    //             if($download_file['status']== 'success'){

    //                 (new Telesales)::where('call_id', $recordings_data['call_id'])
    //                     ->update([
    //                         's3_recording_url' => $download_file['url'],
    //                         'recording_downloaded' => 1,
    //                     ]);
    //             }
    //         }
    //     }
    //     echo "end";
    // }

    /**
     * For Retrieve call back from twilio after outbound call ends
     */
    public function outboundVoiceRecording(Request $request) {
      $inputs = $request->all();

      $toTask = [];
      $toTask['assignmentStatus'] = "wrapping";
      $updateTask = $this->twilioService->updateTask($inputs['workspace_id'], $inputs['task_id'], $toTask);

      $leadData = Telesales::where('refrence_id', $inputs['leadId'])->first();
      if (empty($leadData)) {
        \Log::error("Lead not found with id: " . $inputs['leadId']);
        return response('<Response></Response>', 200)->header('Content-Type', 'application/xml; charset=utf-8');
      }

      $lead = Telesales::find(array_get($leadData, 'id'));

      if (isset($inputs['DialCallStatus'])) {

        //Update call scheduled status to telesale_schedule_call table
        $scheduledCall = TelesaleScheduleCall::where('telesale_id', array_get($lead, 'id'))->where('call_immediately', 'yes')->where('task_id', $inputs['task_id'])->first();

        if (empty($scheduledCall)) {
          \Log::error("Schedule task not found with id: " . $inputs['task_id']);
          return response('<Response></Response>', 200)->header('Content-Type', 'application/xml; charset=utf-8');
        }

        \Log::debug("TPV now");
        \Log::debug(print_r($inputs, true));
        \Log::debug(print_r($inputs['DialCallStatus'], true));


        TelesaleScheduleCall::where('id', $scheduledCall->id)->update([
          'dial_status' => $inputs['DialCallStatus'],
          'schedule_status' => config()->get('constants.SCHEDULED_STATUS_ATTEMPTED')
        ]);

        // If the call is completed, that means the call was connected successfully
        // to the customer and customer picked up the call
        if ($inputs['DialCallStatus'] == config()->get('constants.CALL_COMPLETED_STATUS')) {

          // NOTE: Recording part is moved to the recording function callback

          //Update required attributes in telesales table
          // $lead->update([
          //   'recording_id' => $inputs['RecordingSid'],
          //   'twilio_recording_url' => $inputs['RecordingUrl'],
          //   'call_duration' => $inputs['DialCallDuration'],
          //   'call_id' => $inputs['DialCallSid'],
          // ]);

          // // (new CallsController())->downloadRecording($lead->id);

          // //we need to store call related information in twilio lead call details table for each call
          // $downloadRecording = Telesales::find($lead->id);
          // $callDetails = TwilioLeadCallDetails::where('task_id',$inputs['task_id'])->first();
          // if(!empty($callDetails)){
          //   $callDetails->twilio_recording_url = $inputs['RecordingUrl'];
          //   $callDetails->twilio_recording_id = $inputs['RecordingSid'];
          //   $callDetails->call_duration = $inputs['DialCallDuration'];
          //   $callDetails->call_id = $inputs['DialCallSid'];
          //   $callDetails->recording_url = $downloadRecording->s3_recording_url;
          //   $callDetails->save();
          // }

        } else {
          // Reschedule call
          // As the call was not successfully connected to the customer
          $rescheduleCall = $this->rescheduleCall(array_get($leadData, 'id'));

          if ($rescheduleCall !== false) {
            \Log::info("This call has been rescheduled due to call status " . $inputs['DialCallStatus'] . " for lead: " . array_get($lead, 'id'));
          } else {
            \Log::info("This call can not rescheduled");
          }
        }

        if ($lead->status != config()->get('constants.LEAD_TYPE_VERIFIED') && $scheduledCall->attempt_no >= config()->get('constants.MAX_RESCHEDULE_CALL_COUNT')) {
          $agent = User::find($lead->user_id);

          if (!empty($agent)) {
            Mail::to($agent->email)->send(new SendEmailNotVerify($lead->refrence_id));
          } else {
            \Log::error("User not found with id: " . $lead->user_id);
          }
        }

        return response('<Response></Response>', 200)->header('Content-Type', 'application/xml; charset=utf-8');

      } else {
        \Log::error("Not get any status for outbound call");
        return response('<Response></Response>', 200)->header('Content-Type', 'application/xml; charset=utf-8');
      }
    }

    /**
     * For Retrieve Customer inbound number by lead reference id
     */
    public function getCallerId($leadId) {
      $lead = Telesales::where('refrence_id', $leadId)->first();
      if (empty($lead)) {
        return false;
      }

      $client = Client::find(array_get($lead, 'client_id'));

      if (empty($client)) {
        return false;
      }

      $number = ClientTwilioNumbers::where('client_id', array_get($client, 'id'))->where('type', config()->get('constants.TWILIO_PHONE_NUMBER_TYPE.CUSTOMER_INBOUND_NUMBER'))->first();

      if (empty($number)) {
        return false;
      }

      return array_get($number, 'phonenumber');
    }

    /**
     * For Retrieve customer's lead phone number
     */
    public function getCustomerNumber($leadId) {
      $lead = Telesales::where('refrence_id', $leadId)->first();

      if (empty($lead)) {
        return false;
      }

      return $this->getPhoneNumber($lead->id);
    }

    /**
     * This method is used for get duration of the call
     */
    public function GetCallDuration(){

        $recordings_to_download = (new Telesales)::select('call_id','twilio_recording_url','recording_id')
        ->whereNotNull('twilio_recording_url')->whereNotNull('call_id')
        ->where('call_duration', '=', '')
        ->orWhereNull('call_duration')
        ->get();

           echo "started \n";
          if(count($recordings_to_download)> 0){
              foreach($recordings_to_download as $recordings_data){


                      $recording =  $this->twilioService->retrieveRecordings($recordings_data['recording_id']);
                                          if($recording) {
                                            $seconds = $recording->duration;
                                            (new Telesales)->where('call_id', $recordings_data['call_id'])
                                            ->update([
                                                     'call_duration' => $seconds,
                                                     ]);
                                          (new TwilioLeadCallDetails)->where('worker_call_id', $recordings_data['call_id'])
                                          ->update([
                                                  'call_duration' => $seconds,
                                                  ]);
                                          }



              }
          }
          echo "end";

    }

    /**
     * This method is used for change status of the lead
     */
    public function changeLeadStatus() {
        $timeBeforeOneHr = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $nowTime = date('Y-m-d H:i:s');
        $telesales = Telesales::whereNotNull('verification_start_date')->whereBetween('verification_start_date', [$timeBeforeOneHr, $nowTime])->where('is_disconnect_queued', 0)->where('status', config('constants.LEAD_TYPE_PENDING'))->get();
        foreach ($telesales as $telesale) {
            try {
                DisconnectLeadUpdate::dispatch($telesale)->delay(Carbon::now()->addMinutes(15));
                $telesale->is_disconnect_queued = 1;
                $telesale->save();
                \Log::info("Dispatched lead disconnected status update queue for lead with id: " . $telesale->id);
            } catch (\Exception $e) {
                \Log::error("Unable to dispatch lead disconnected status update queue for lead with id: " . $telesale->id);
            }
        }
    }

    public function fetchTwilioStatisticsApiRecord($twilioClient,$startDate,$endDate,$workspaceId){
        $twilioWorkFlows = DB::table('client_twilio_workflowids')->pluck('workflow_id');
        // Twiilio workflow statistics
        foreach($twilioWorkFlows as $key => $val)
        {
            $workflow_statistics = (new TwilioService)->checkWorkflow($twilioClient,$val,$workspaceId, [
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
            if($workflow_statistics === false)
                continue;
            
            $twilioDate = Carbon::parse($workflow_statistics->cumulative['start_time'])->toDateString();                                                  
            $workflosStatistics = TwilioStatisticsWorkflow::where('workspace_id',$workflow_statistics->workspaceSid)->where('workflow_id',$workflow_statistics->workflowSid)->get()->where('created_at','>=',$twilioDate.' 00:00:00')->toArray();
            if(isset($workflosStatistics) && !empty($workflosStatistics))
            {
                (new TwilioStatisticsWorkflow)->saveWorkflowStatistics($twilioDate,$workflow_statistics,true);
            }
            else
            {
                (new TwilioStatisticsWorkflow)->saveWorkflowStatistics($twilioDate,$workflow_statistics);
            }
        }
        
        // Twilio Taskqueue Statistics
        $taskQueues = (new TwilioService)->checkTaskqueue($twilioClient,$workspaceId);
        
        if($taskQueues !== false){
            foreach($taskQueues as $key => $val)
            {
                $taskQueueStatistics = $twilioClient->taskrouter->v1->workspaces($workspaceId)
                                                            ->taskQueues($val->sid)
                                                            ->cumulativeStatistics()
                                                            ->fetch([
                                                                'startDate' => $startDate,
                                                                'endDate' => $endDate
                                                            ]);
            
                $twilioDate = Carbon::parse($taskQueueStatistics->startTime->format('Y/m/d'))->toDateString();
                $taskQueueData = TwilioStatisticsTaskqueue::where('workspace_id',$taskQueueStatistics->workspaceSid)->where('task_queue_sid',$taskQueueStatistics->taskQueueSid)->where('created_at','>=',$twilioDate.' 00:00:00')->get()->toArray();
                if(isset($taskQueueData) && !empty($taskQueueData))
                {
                    (new TwilioStatisticsTaskqueue)->saveTaskQueue($twilioDate,$taskQueueStatistics,true);
                }
                else
                    (new TwilioStatisticsTaskqueue)->saveTaskQueue($twilioDate,$taskQueueStatistics);
            }
        }
        

        // Twilio Workspace Statistics

        $workspaceStatistics = (new TwilioService)->checkWorkSpace($twilioClient,$workspaceId,  [
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
        if($workspaceStatistics !==  false){
            $twilioDate = Carbon::parse($workspaceStatistics->cumulative['start_time'])->toDateString();
            $twilioWorkspaceData = TwilioStatisticsWorkspace::where('workspace_id',$workspaceStatistics->workspaceSid)->where('created_at','>=',$twilioDate.' 00:00:00')->get()->toArray();
            if(isset($twilioWorkspaceData) && !empty($twilioWorkspaceData))
            {
                $id = (new TwilioStatisticsWorkspace)->saveWorkspaceData($twilioDate,$workspaceStatistics,true);
            }
            else
            {
                $id = (new TwilioStatisticsWorkspace)->saveWorkspaceData($twilioDate,$workspaceStatistics);
            }
            (new TwilioWorkspaceActivityStatistics)->saveTwilioWorkspaceActivity($id,$twilioDate,$workspaceStatistics);
        }
        //Store Specific workers statistics
        $twilioWorkersList = UserTwilioId::get(['twilio_id']);
        foreach($twilioWorkersList as $key => $val)
        {
            $workerStatistics = (new TwilioService)->checkWorker($twilioClient,$val,$workspaceId, [
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ]);
            if($workerStatistics === false)
                continue;
            
            $twilioDate = Carbon::parse($workerStatistics->cumulative['start_time'])->toDateString();
            $twilioWorker = TwilioStatisticsSpecificWorker::where('Worker_id',$val->twilio_id)->where('created_at','>=',$twilioDate.' 00:00:00')->get()->toArray();
            if(isset($twilioWorker) && !empty($twilioWorker))
                $data = (new TwilioStatisticsSpecificWorker)->saveSpecificWorkerData($twilioDate,$workerStatistics,$val,true);
            else
                $data =  (new TwilioStatisticsSpecificWorker)->saveSpecificWorkerData($twilioDate,$workerStatistics,$val);
            
            (new TwilioStatisticsSpecificWorkerActivity)->saveSpecificWorkerActivityData($data,$twilioDate,$workerStatistics,$val,false,$startDate);
            
        }
        $workers_statistics = (new TwilioService)->checkWorkSpaceWokers($twilioClient,$workspaceId, [
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
        if($workers_statistics !== false){
            $twilioDate = Carbon::parse($workers_statistics->cumulative['start_time'])->toDateString();
            $workers = TwilioStatisticsWorkers::where('workspace_id',$workers_statistics->workspaceSid)->where('created_at','>=',$twilioDate.' 00:00:00')->get()->toArray();
            if(isset($workers) && !empty($workers))
            {
                $id = (new TwilioStatisticsWorkers)->saveWorkersStatistics($twilioDate,$workers_statistics,true);
            }
            else
            {
                $id = (new TwilioStatisticsWorkers)->saveWorkersStatistics($twilioDate,$workers_statistics);
            }
            $activityStatistics = $workers_statistics->realtime['activity_statistics'];
            $activityDurations = $workers_statistics->cumulative['activity_durations'];
            $friendlyName = array_column($activityStatistics,'friendly_name');
            $friendlyNameDuration = array_column($activityDurations,'friendly_name');
            array_multisort($friendlyName, SORT_ASC, $activityStatistics);
            array_multisort($friendlyNameDuration, SORT_ASC, $activityDurations);
            (new TwilioStatisticsWorkersActivityduration)->updateTwilioWorkersActivityDuration($id,$twilioDate,$activityStatistics,$activityDurations,$workers_statistics->workspaceSid);
        }
        
        // Twilio Call History
        $calls = $twilioClient->calls
            ->read(["startTime" => Carbon::today()]);
            
        foreach($calls as $key=>$val)
        {
            $twilioDate = Carbon::parse($val->startTime->format('Y/m/d'))->toDateString();
            
            $twilioCallData = TwilioStatisticsCallLogs::where('call_sid',$val->sid)->where('created_at','>=',$twilioDate.' 00:00:00')->get()->toArray();
            if(isset($twilioCallData) && !empty($twilioCallData))
            {
                (new TwilioStatisticsCallLogs)->saveTwilioCallLogs($twilioDate,$val,true);
            }
            else
                (new TwilioStatisticsCallLogs)->saveTwilioCallLogs($twilioDate,$val);
        }

        // TWILIO statistics for usage records

        foreach ($twilioClient->usage->records->today->read() as $k=>$record) {
            
            $twilioDate = Carbon::parse($record->startDate->format('Y-m-d'))->toDateString();
            $twilioUsageRecordData = TwilioStatisticsUsageRecords::where('account_sid',$record->accountSid)->where('category',$record->category)->where('created_at','>=',$twilioDate.' 00:00:00')->get();
            if(isset($twilioUsageRecordData) && $twilioUsageRecordData->count() > 0)
            {
                (new TwilioStatisticsUsageRecords)->saveTwilioUsageRecords($record,$twilioUsageRecordData[0],true);
            }
            else
            (new TwilioStatisticsUsageRecords)->saveTwilioUsageRecords($record);
        }
    }
}
