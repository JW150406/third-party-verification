<?php

namespace App\Jobs;

use App\models\Telesales;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\models\CriticalLogsHistory;
use App\Traits\LeadTrait;
use Exception;

class CheckTPVAlerts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels , LeadTrait;

    public $leadId;
    public $count;
    public $key;
    // public $userId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($leadId,$count,$key)
    {
        $this->leadId = $leadId;
        $this->count = $count;
        $this->key = $key;
        // $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{

            $leadId = $this->leadId;
            $count = $this->count;
            $key = $this->key;
            $leadObj = Telesales::find($leadId);

            //Check whether lead is empty or not
            if(!empty($leadObj)){

                $maxCounts = getSettingValue($leadObj->client_id,$key);
                \Log::info('TPV attempt current count is .' . $count);
                if($count == $maxCounts){
                    \Log::info('Tpv attempts alert is triggered for lead id: '.$leadObj);
                    
                    //Tpv max alert is generated
                    $critical_message =  __('critical_logs.messages.Event_Type_48',['count'=>$maxCounts]);
                    $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                    $user_type = config('constants.USER_TYPE_CRITICAL_LOGS.2');
                    $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_48');
                    $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.'.config()->get('constants.VERIFICATION_STATUS_CHART.'.ucfirst($leadObj->status)));
                    $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(null,$critical_message,$leadId,null,null,$lead_status,$event_type,$error_type,$user_type,null,$critical_message);
                    $alerts[] = $critical_message;
    
                    //Send critical alert mail to client
                    $this->sendCriticalAlertMail($leadObj,$alerts); 
                }
                else{
                    \Log::info('tpv max alert is not generated for lead id: '.$leadId);
                }
            }
            else{
                \Log::info('Lead is not found with id '.$leadId);
            }
        }catch(Exception $e){
            \Log::error($e);
        }
    }
}
