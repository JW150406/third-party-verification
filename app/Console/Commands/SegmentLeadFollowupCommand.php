<?php

namespace App\Console\Commands;

use App\models\Telesales;
use App\models\CriticalLogsHistory;
use Illuminate\Console\Command;
use App\Traits\LeadTrait;
use Carbon\Carbon;

class SegmentLeadFollowupCommand extends Command
{
    use LeadTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:followup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command send followup events to Segment.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $leads = Telesales::whereIn('status', config()->get('constants.LEAD_STATUS_FOR_EXPIRED'))->get();
        
        foreach ($leads as $lead) {
            // Get client wise lead expiry time to make lead status expired
            $leadExpiredfield = getSettingValue($lead->client_id,'lead_expiry_time',0);

            // If any client doesnot have this value then take default value as 72 hours for lead expiration
            if($leadExpiredfield == 0){
                $leadExpiredTime = Carbon::now()->subHours(config()->get('constants.LEAD_EXPIRY_DEFAULT_TIME'));
            } else {
                $leadExpiredTime = Carbon::now()->subHours($leadExpiredfield);
            }
            
            // Check if lead is created before $leadExpiredfTime specified in settings tab  then registered it as a cancel lead
            if (date(array_get($lead, 'created_at')) <= $leadExpiredTime) {
                \Log::info('lead expired time limit is '.$leadExpiredfield);
                if (isset($leads) && $leads->count() > 0) {
                    $lead = Telesales::find(array_get($lead, 'id'));
                    // Cancel lead and store its required logs
                    $this->expiredLeadWithoutSendingMails($lead->id);

                    // $lead->update(['status' => 'cancel']);

                    // if(!$lead->selfVerifyModes->isEmpty()) {
                        // $leadId = array_get($lead, 'id');
                        // $user_type = config('constants.USER_TYPE_CRITICAL_LOGS.2');
                        //
                        // $link = '';
                        // $encoded_leadid = base64_encode($leadId);
                        // $lastKey = $lead->selfVerifyModes->keys()->last();
                        // foreach ($lead->selfVerifyModes as $key => $selfVerifyMode) {
                        //     $url= route('sendverificationlink',[$encoded_leadid,$selfVerifyMode->verification_mode]);
                        //     $link .= "Link: ".$url."\n";
                        //
                        // }
                        // $reason = __('critical_logs.messages.Event_Type_15',['link'=>$link]);
                        // $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Non-critical');
                        // $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_15');
                        // $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                        // (new CriticalLogsHistory)->createCriticalLogs(null,$reason,$leadId,null,null,$lead_status,$event_type,$error_type,$user_type);

                    // }
                } else {
                    continue;
                }
            }
        }
    }
}

