<?php

namespace App\Traits;

use App\models\Dispositions;
use Log;
use App\models\Telesales;
use App\models\CriticalLogsHistory;
use Carbon\Carbon;
use Auth;

trait CriticalLogsTrait {

    /**
     * Store logs to critical_logs_history table for cancelled leads
     * @param $lead
     */
    public function registerLogsForSelfVerificationExpire($lead) {
      try {
          if (!$lead->selfVerifyModes->isEmpty()) {
              $isExpiredLink = CriticalLogsHistory::where('event_type',config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_15'))->where('lead_id',$lead->id)->exists();
                if($isExpiredLink){
                    \Log::info("Self verification link is already expired  ".$lead->id);
                    return true;
                }

              $userType = config('constants.USER_TYPE_CRITICAL_LOGS.2');
              $link = '';
              $encodedLeadId = base64_encode($lead->id);
              $lastKey = $lead->selfVerifyModes->keys()->last();
              foreach ($lead->selfVerifyModes as $key => $selfVerifyMode) {
                  $url = route('sendverificationlink', [$encodedLeadId, $selfVerifyMode->verification_mode]);
                  // $link .= "Link: ".$url."\n";
              }
              $reason = __('critical_logs.messages.Event_Type_15');
              $errorType = config('constants.ERROR_TYPE_CRITICAL_LOGS.Non-critical');
              $eventType = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_15');
              $leadStatus = $this->getLeadStatusForLog($lead->status);
              (new CriticalLogsHistory)->createCriticalLogs(null, $reason, $lead->id, null, null, $leadStatus, $eventType, $errorType, $userType);

              \Log::info("registerLogsForCancelLead: Logs registered for cancelled lead with id: " . $lead->id);

          } else {
              \Log::info("registerLogsForCancelLead: No self verification link found for lead with id: " . $lead->id);
          }
          return true;
      } catch (\Exception $e) {
        Log::error("registerLogsForExpired: Error while registering logs for expired lead with id " . $lead->id . ": " . $e->getMessage());
        return false;
      }

    }

    /**
     * Insert logs when TPV now selected for callback
     * @param $lead
     */
    public function registerLogsForTPVNow($lead) {
        try {
            $reason = 'Customer selected "TPV Now!"';
            //Retrieve lead status to store in critical logs tables
            $leadStatus = $this->getLeadStatusForLog($lead->status);

            (new CriticalLogsHistory)->createCriticalLogs(array_get($lead, 'user_id'), $reason, array_get($lead, 'id'), null, null, $leadStatus, config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_30'), config('constants.ERROR_TYPE_CRITICAL_LOGS.Non-critical'), null);
            Log::info("Log registered for TPV now for lead with id: " . array_get($lead, 'id'));
            $this->registerLogsForScheduleCall($lead, date('Y-m-d H:i:s'));
        } catch (\Exception $e) {
            Log::error("Error while registering logs for TPV Now for lead with id: " . array_get($lead, 'ids') . " : " . $e->getMessage());
        }
    }

    /**
     * Insert logs when call scheduled
     * @param $lead, $scheduledDate
     */
    public function registerLogsForScheduleCall($lead, $scheduledDate) {
        try {
            $date = new \DateTime($scheduledDate, new \DateTimeZone('UTC'));
            $date->setTimezone(new \DateTimeZone('America/New_York'));
            $scheduledDate = $date->format('m/d/Y h:i A');
            $reason = 'TPV call scheduled for: ' . $scheduledDate;

            //Retrieve lead status to store in critical logs tables
            $leadStatus = $this->getLeadStatusForLog($lead->status);

            (new CriticalLogsHistory)->createCriticalLogs(array_get($lead, 'user_id'), $reason, array_get($lead, 'id'), null, null, $leadStatus, config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_29'), config('constants.ERROR_TYPE_CRITICAL_LOGS.Non-critical'), null);
            Log::info("Log registered for Schedule call lead with id: " . array_get($lead, 'id'));
        } catch (\Exception $e) {
            Log::error("Error while registering logs for scheduled call lead with id: " . array_get($lead, 'id') . " : " . $e->getMessage());
        }
    }

    /**
     * Insert logs when outbound call end
     * @param $lead, $eventType, $dispositionId
     */
    public function registerLogsForOutboundCompletion($lead, $eventType, $dispositionId = "") {
        try {
            $disposition = "";
            $reason = __('critical_logs.messages.' . $eventType);
            if (in_array(array_get($lead, 'status'), array(config()->get('constants.LEAD_TYPE_DISCONNECTED'), config()->get('constants.LEAD_TYPE_DECLINE'))) && $dispositionId != "") {
                $disposition = Dispositions::select('description')->find($dispositionId);
                if (array_get($disposition, 'description')) {
                    $reason = __('critical_logs.messages.' . $eventType, ['disposition' => array_get($disposition, 'description')]);
                }
            }

            //Retrieve lead status to store in critical logs tables
            $leadStatus = $this->getLeadStatusForLog($lead->status);

            (new CriticalLogsHistory)->createCriticalLogs(null, $reason, array_get($lead, 'id'), null, null, $leadStatus, config('constants.EVENT_TYPE_CRITICAL_LOGS.' . $eventType), config('constants.ERROR_TYPE_CRITICAL_LOGS.Non-critical'), config('constants.USER_TYPE_CRITICAL_LOGS.1'), Auth::user()->id);
            Log::info("Log registered for outbound call completion of lead with id: " . array_get($lead, 'id'));
        } catch (\Exception $e) {
            Log::error("Error while registering outbound call completion logs for lead with id: " . array_get($lead, 'id') . " : " . $e->getMessage());
        }
    }

    /**
     * Retrieve lead status according to critical logs
     * @param $status
     */
    public function getLeadStatusForLog($status) {
        switch($status) {
            case config('constants.LEAD_TYPE_DECLINE'):
                $leadStatus = config()->get('constants.LEAD_STATUS_CRITICAL_LOGS.Declined');
                break;

            case config('constants.LEAD_TYPE_DISCONNECTED'):
                $leadStatus = config()->get('constants.LEAD_STATUS_CRITICAL_LOGS.Disconnected');
                break;

            case config('constants.LEAD_TYPE_VERIFIED'):
                $leadStatus = config()->get('constants.LEAD_STATUS_CRITICAL_LOGS.Verified');
                break;

            case config('constants.LEAD_TYPE_PENDING'):
                $leadStatus = config()->get('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                break;

            case config('constants.LEAD_TYPE_CANCELED'):
                $leadStatus = config()->get('constants.LEAD_STATUS_CRITICAL_LOGS.Cancelled');
                break;

            case config('constants.LEAD_TYPE_EXPIRED'):
                $leadStatus = config()->get('constants.LEAD_STATUS_CRITICAL_LOGS.Expired');
                break;

            case config('constants.LEAD_STATUS_SELF_VERIFIED'):
                $leadStatus = config()->get('constants.LEAD_STATUS_CRITICAL_LOGS.self-verified');
                break;

            default:
                $leadStatus = "";
                break;

        };
        return $leadStatus;
    }

    /**
     * Insert logs when call rescheduled
     * @param $lead, $scheduledDate
     */
    public function registerLogsForRescheduleCall($lead, $scheduledDate) {
        try {
            $date = new \DateTime($scheduledDate, new \DateTimeZone('UTC'));
            $date->setTimezone(new \DateTimeZone('America/New_York'));
            $scheduledDate = $date->format('m/d/Y h:i A');
            $reason = __('critical_logs.messages.Event_Type_36', ['date' => $scheduledDate]);

            //Retrieve lead status to store in critical logs tables
            $leadStatus = $this->getLeadStatusForLog($lead->status);

            (new CriticalLogsHistory)->createCriticalLogs(null, $reason, array_get($lead, 'id'), null, null, $leadStatus, config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_36'), config('constants.ERROR_TYPE_CRITICAL_LOGS.Non-critical'), config('constants.USER_TYPE_CRITICAL_LOGS.2'));
            Log::info("Log registered for reschedule call lead with id: " . array_get($lead, 'id'));
        } catch (\Exception $e) {
            Log::error("Error while registering logs for rescheduled call lead with id: " . array_get($lead, 'id') . " : " . $e->getMessage());
        }
    }

    /**
     * Register logs for expired leads
     * @param $lead
     */
    public function registerLeadExpiredLogs($lead) {
        try {
            $reason = __('critical_logs.messages.Event_Type_38');

            //Retrieve lead status to store in critical logs tables
            $leadStatus = $this->getLeadStatusForLog($lead->status);

            (new CriticalLogsHistory)->createCriticalLogs(null, $reason, array_get($lead, 'id'), null, null, $leadStatus, config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_38'), config('constants.ERROR_TYPE_CRITICAL_LOGS.Non-critical'), config('constants.USER_TYPE_CRITICAL_LOGS.2'));
            Log::info("Log registered for expired lead with id: " . array_get($lead, 'id'));
        } catch (\Exception $e) {
            Log::error("Error while registering logs for expired lead with id: " . array_get($lead, 'id') . " : " . $e->getMessage());
        }
    }

    /**
     * Register logs for cancelled leads
     * @param $lead, $reason
     */
    public function registerCancelledLeadsLogs($lead, $reason = "") {
        try {
            if ($reason == "" ) {
                $reason = __('critical_logs.messages.Event_Type_37');
                $eventType = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_38');
            } else {
                $reason = __('critical_logs.messages.Event_Type_39',['disposition' => $reason]);
                $eventType = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_39');
            }

            //Retrieve lead status to store in critical logs tables
            $leadStatus = $this->getLeadStatusForLog($lead->status);

            (new CriticalLogsHistory)->createCriticalLogs(null, $reason, array_get($lead, 'id'), null, null, $leadStatus, $eventType, config('constants.ERROR_TYPE_CRITICAL_LOGS.Non-critical'), config('constants.USER_TYPE_CRITICAL_LOGS.2'));
            Log::info("Log registered for expired lead with id: " . array_get($lead, 'id'));
        } catch (\Exception $e) {
            Log::error("Error while registering logs for expired lead with id: " . array_get($lead, 'id') . " : " . $e->getMessage());
        }
    }

    /**
     * Insert logs when IVR verification call end
     * @param $lead, $eventType
     */
    public function registerIVRTPVCallCompletionlogs($lead, $eventType) {
        try {
            $reason = __('critical_logs.messages.' . $eventType);
            
            //Retrieve lead status to store in critical logs tables
            $leadStatus = $this->getLeadStatusForLog($lead->status);

            (new CriticalLogsHistory)->createCriticalLogs(null, $reason, array_get($lead, 'id'), null, null, $leadStatus, config('constants.EVENT_TYPE_CRITICAL_LOGS.' . $eventType), config('constants.ERROR_TYPE_CRITICAL_LOGS.Non-critical'), config('constants.USER_TYPE_CRITICAL_LOGS.1'));
            Log::info("Log registered for outbound call completion of lead with id: " . array_get($lead, 'id'));
        } catch (\Exception $e) {
            Log::error("Error while registering outbound call completion logs for lead with id: " . array_get($lead, 'id') . " : " . $e->getMessage());
        }
    }
}
