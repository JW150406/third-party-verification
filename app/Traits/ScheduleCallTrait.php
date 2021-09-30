<?php

namespace App\Traits;

use Log;
use App\models\TelesaleScheduleCall;
use App\models\Telesales;
use App\Traits\LeadTrait;
use App\models\Settings;
use Carbon\Carbon;
use App\models\SettingTPVnowRestrictedTimeZone;
use App\models\TwilioLeadCallDetails;

trait ScheduleCallTrait {
  use LeadTrait;

  /**
   * This trait method is used for Reschedule a call
   * @param $leadId
   */
  public function rescheduleCall($leadId) {
    \Log::info('in Reschedule Call Function');
    $lead = Telesales::find($leadId);
    $maxNoOfAttempt = $maxNoOfAttemptOfSelfTpv = config()->get('constants.MAX_RESCHEDULE_CALL_COUNT');
    $clientSettings = Settings::select('tpv_now_max_no_of_call_attempt','tpv_now_call_delay','self_tpv_max_no_of_call_attempt','self_tpv_call_delay')->where('client_id',$lead->client_id)->first();
    $callDelay = config()->get('constants.SCHEDULE_CALL_DELAY');
    if(!empty($clientSettings))
    {
      $maxNoOfAttempt = $clientSettings->tpv_now_max_no_of_call_attempt;
      $callDelay = explode(',',$clientSettings->tpv_now_call_delay);
      $maxNoOfAttemptOfSelfTpv = $clientSettings->self_tpv_max_no_of_call_attempt;
    }
    if (empty($lead)) {
      \Log::error("Reschedule call: Lead not found with id: " . $leadId);
      return false;
    }

    if (in_array($lead->status, config()->get('constants.TELESALES_STATUS_TO_RESCHEDULE_CALL'))) {
      \Log::debug("Lead with id:" . $leadId);
      $scheduleCall = TelesaleScheduleCall::where('telesale_id', $leadId)->orderBy('attempt_no', 'desc')->first();
      \Log::info('Schedule call data:');
      \Log::debug($scheduleCall);

      //Store lead status in twilio call details for tpv now request
      $twilioCalls = TwilioLeadCallDetails::where('task_id',$scheduleCall->task_id)->first();
      if(!empty($twilioCalls)){
        $twilioCalls->lead_status = $lead->status;
        $twilioCalls->save();
      }
    \Log::info('successfully stored lead status for tpv now in twilio call details table');

      if (empty($scheduleCall)) {
        \Log::error("Scheduled call not found for lead with id: " . $leadId);
        return false;
      } else if (in_array(array_get($scheduleCall,'schedule_status'),['pending','task-created'])) {
        \Log::error("Scheduled call: Cannot reschedule as there is already pending/in progress task" . $scheduleCall);
        return false;
      }
      \Log::info($scheduleCall);
        // if (array_get($scheduleCall, 'call_type') == config('constants.SCHEDULE_CALL_TYPE_OUTBOUND_DISCONNECT') && config('constants.CLIENT_SUNRISE_CLIENT_ID') == $lead->client_id) {
        //     $maxNoOfAttempt = config('constants.MAX_RESCHEDULE_CALL_SUNRISE');
        //     $callDelay = config('constants.SCHEDULE_CALL_DELAY_SUNRISE');
        // }


        if (array_get($scheduleCall, 'call_type') == config('constants.SCHEDULE_CALL_TYPE_OUTBOUND_DISCONNECT') && isOnSettings($lead->client_id, 'is_outbound_disconnect')) {
          $maxNoOfAttempt = getSettingValue($lead->client_id,'outbound_disconnect_max_reschedule_call_attempt',null);
          $callDelay = getSettingValue($lead->client_id,'outbound_disconnect_schedule_call_delay',null);
          $callDelay = !empty($callDelay) ? explode(",",$callDelay) : null;
      }

      // if (array_get($scheduleCall, 'disposition')) {
      //   \Log::error("Reschedule call: Can not reschedule this call because this call & lead is already declined. Schedule call id: " . array_get($scheduleCall, 'id'));
      //   return false;
      // }

      if (array_get($scheduleCall, 'call_type') == config('constants.SCHEDULE_CALL_TYPE_SELF_TPV_CALLBACK')) {
          $maxNoOfAttempt = $maxNoOfAttemptOfSelfTpv;
      }

      if (array_get($scheduleCall, 'attempt_no') >= $maxNoOfAttempt) {
        \Log::error("Call with id " . array_get($scheduleCall, 'id') . " already resheduled for " . array_get($scheduleCall, 'attempt_no') . " times. So, Can not reshedule call more than " . $maxNoOfAttempt . " time");

        $cancelLead = $this->expiredLeadWithoutSendingMails($lead->id);

        if ($cancelLead) {
          \Log::error("rescheduleCall: Lead registered as a cancel lead for id: " . $lead->id);
        } else {
          \Log::error("rescheduleCall: Unable to register lead as a cancel lead for id: " . $lead->id);
        }
        return false;
      }

      //Check for call type, if it's type is self tpv call back then retriev its configurations
      if (array_get($scheduleCall, 'call_type') == config()->get('constants.SCHEDULE_CALL_TYPE_SELF_TPV_CALLBACK')) {
        if (!empty($clientSettings->self_tpv_call_delay)) {
            $callDelay = explode(',',$clientSettings->self_tpv_call_delay);
        } else {
            $callDelay = explode(',',config()->get('constants.SCHEDULE_CALL_DELAY'));
        }

        // switch (array_get($scheduleCall, 'attempt_no')) {
        //     case 1:
        //         $callDelay = config()->get('constants.SELF_TPV_CALLBACK_SECOND_ATTEMPT_DELAY');
        //         break;
            
        //     case 2:
        //         $callDelay = config()->get('constants.SELF_TPV_CALLBACK_THIRD_ATTEMPT_DELAY');
        //         break;

        //     default:
        //         $callDelay = 0;
        //         break;
        // }

        $scheduleTime = $this->getScheduledTime(array_get($scheduleCall, 'attempt_no'), $callDelay);
      } else {
        $scheduleTime = $this->getScheduledTime(array_get($scheduleCall, 'attempt_no'), $callDelay);
      }

      $scheduleTimeStore = $scheduleTime;
    $leadState = $lead->zipcodes()->first();
    \Log::debug("leadState: " . $leadState);
	$clientStateRestriction = SettingTPVnowRestrictedTimeZone::where('client_id',$lead->client_id)->where('state',strtoupper($leadState->state))->first();
	if(isset($clientStateRestriction) && !empty($clientStateRestriction) && array_get($scheduleCall, 'call_type') == 'outbound'){
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
			return false;
		}
		
	}
        $rescheduleCall = TelesaleScheduleCall::create([
        "telesale_id" => $lead->id,
        "call_immediately" => array_get($scheduleCall, 'call_immediately'),
        "call_time" => $scheduleTimeStore,
        "call_lang" => array_get($scheduleCall, 'call_lang'),
        "call_type" => array_get($scheduleCall, 'call_type'),
        "attempt_no" => $this->getAttemptNumber(array_get($scheduleCall, 'attempt_no')),
        "schedule_status" => config()->get('constants.SCHEDULE_PENDING_STATUS'),
        'call_type' => array_get($scheduleCall, 'call_type')
        ]);

        if ($rescheduleCall) {
            \Log::info("Call has been rescheduled with id: " . array_get($rescheduleCall, 'id'));

            //Register logs for schedule call
            $this->registerLogsForRescheduleCall($lead, $scheduleTime);
            return true;
        } else {
            \Log::error("Call not rescheduled for id: " . array_get($lead, 'id'));
            return false;
        }	
    } else {
      \Log::error("Reschedule call: Can only reschedule a call for lead status pending or hangup & current lead has status: " . $lead->status . " & id: " . $lead->id);
      return false;
    }
  }

  /**
   * This method is used to get new attept number from the previous number
   * @param $currentAttempt
   */
  public function getAttemptNumber($currentAttempt) {
    return $currentAttempt + 1;
  }

  /**
   * This trait method is used for get scheduled time from current time and call delay
   * @param $currentAttempt, $callDelayArr
   */
  public function getScheduledTime($currentAttempt,$callDelayArr) {
    // if(empty($callDelay))
    // {
    //   $callDelayArr = config()->get('constants.SCHEDULE_CALL_DELAY');
    // }
    // else
    //   $callDelayArr = explode(',',$callDelay);

    $nextAttempt = $currentAttempt + 1;
    $nextAttemptScheduleTime = $callDelayArr[$nextAttempt-1];
    // switch ($nextAttempt) {
    //   case 2:
    //     $nextAttemptScheduleTime = config()->get("constants.SCHEDULE_CALL_SECOND_ATTEMPT_TIME_MINS");
    //     break;
    //   case 3:
    //     $nextAttemptScheduleTime = config()->get("constants.SCHEDULE_CALL_THIRD_ATTEMPT_TIME_MINS");
    //     break;
    //   default:
    //     $nextAttemptScheduleTime = config()->get("constants.SCHEDULE_CALL_DEFAULT_ATTEMPT_TIME_MINS");
    //     break;
    // }

    return date('Y-m-d H:i:s', strtotime('+'.$nextAttemptScheduleTime.' minute'));
  }

  /**
   * For Perform operations on schedule call table after lead decline or disconnects
   * @param $referenceId, $dispositionId
   */
  public function postScheduleCallHandler($referenceId, $dispositionId) {
    $lead = Telesales::where('refrence_id', $referenceId)->first();

    if (empty($lead)) {
      \Log::error("Post Schedule Call Handler: Lead not found with id: " . $referenceId);
      return false;
    }

    $scheduleCall = TelesaleScheduleCall::where('telesale_id', $lead->id)->orderBy('attempt_no', 'desc')->first();

    if (empty(array_get($scheduleCall, 'id'))) {
      \Log::error("Post Schedule Call Handler: Schedule call not found for lead with id: " . $lead->id);
      return false;
    }

    $updateRecord = TelesaleScheduleCall::where('id', array_get($scheduleCall, 'id'))->update(['disposition' => $dispositionId]);

    if ($updateRecord) {
      \Log::error("Post Schedule Call Handler: Disposition updated to schedule call with id: " . array_get($scheduleCall, 'id'));

      //Check if lead status is disconnected then reschedule a call
      if ($lead->status == config()->get('constants.LEAD_TYPE_DISCONNECTED')) {
          //Register non critical logs after lead getting disconnected
          $this->registerLogsForOutboundCompletion($lead, 'Event_Type_25', $dispositionId);

          // if($scheduleCall->call_type != config()->get('constants.SCHEDULE_CALL_TYPE_SELF_TPV_CALLBACK')){
            //Call function to reschedule call
            $this->rescheduleCall($lead->id);
          // }
          // else{
          //   \Log::info('For self verified leads no call rescheduled on disconnect');
          // }
      } else {
          //Register non critical logs after lead getting declined
          $this->registerLogsForOutboundCompletion($lead, 'Event_Type_24', $dispositionId);

          //Register self verification expire log after lead declined
          $this->registerLogsForSelfVerificationExpire($lead);

          \Log::info("Lead status is not disconnected. So, we can not reschedule this call");
      }

      return true;
    } else {
      \Log::error("Post Schedule Call Handler: Unable to update dispositions !!");
      return false;
    }

  }


    /**
     * This method is used to Prepare an array of outbound task call
     * @param $workflowId, $language, $toNumber, $fromNumber, $leadId, $custNum, $status
     */
    public function outboundTwilioTaskData($workflowId, $language, $toNumber, $fromNumber, $leadId, $custNum, $status) {
      
      \Log::info('In OUtbound twilio task data');
        if($toNumber == "" || $fromNumber == "") {
            \Log::error("To or from number is not available.");
            return false;
        } else {
            $toArr = [];
            $attrArr = [];
            $attrArr['selected_language'] = $language;
            $attrArr['from'] = $fromNumber;
            $attrArr['to'] = $toNumber;
            $scheduleCall = TelesaleScheduleCall::where('telesale_id', $leadId)->orderBy('attempt_no', 'desc')->first();
            \Log::info($scheduleCall);
            if ($status == config()->get('constants.LEAD_STATUS_SELF_VERIFIED') || ($scheduleCall && array_get($scheduleCall,'call_type') == config('constants.SCHEDULE_CALL_TYPE_SELF_TPV_CALLBACK'))) {
              \Log::info('When call type is self tpv call back');
                $attrArr['type'] = config()->get('constants.TWILIO_CALL_TYPE_SELFVERIFIED_CALLBACK');
            }
            else if ($scheduleCall && array_get($scheduleCall,'call_type') == config('constants.SCHEDULE_CALL_TYPE_OUTBOUND_DISCONNECT')) {
              \Log::info('When call type is outbound disconnect');
                $attrArr['type'] = array_get($scheduleCall,'call_type');
            } else {
              \Log::info('When call type is tpv now outbound ');
                $attrArr['type'] = config()->get('constants.TWILIO_CALL_TYPE_OUTBOUND');
            }
            $leadData = Telesales::find($leadId);
            \Log::info($leadData);
            $attrArr['lead_id'] = $leadData->refrence_id;
            $attrArr['cust_num'] = "+1" . $custNum;
            $toArr['attributes'] = json_encode($attrArr);
            $toArr['taskChannel'] = config()->get('constants.TWILIO_TASK_VOICE_CHANNEL');
            $toArr['workflowSid'] = $workflowId;
            $toArr['timeout'] = config()->get('constants.TASK_TIMEOUT_ON_TWILIO');
            return $toArr;
        }
    }
}
