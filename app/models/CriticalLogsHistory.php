<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CriticalLogsHistory extends Model
{
    use SoftDeletes;
    
    protected $table = 'critical_logs_history';
    protected $fillable = [
        'lead_id',
        'tmp_lead_id',
        'user_type',
        'sales_agent_id',
        'tpv_agent_id',
        'reason',
        'related_lead_ids',
        'event_type',
        'error_type',
        'lead_status',
        'email_alert_message'
    ];

    public function createCriticalLogs($sales_agent_id,$reason,$lead_id,$tmp_lead_id,$related_lead_ids = null,$lead_status=null,$event_type,$error_type = 0,$user_type = null,$tpv_agent_id = null,$email_alert_message=null)
    {
        $leadData['lead_id'] = $lead_id;
        $leadData['tmp_lead_id'] = $tmp_lead_id;
        $leadData['user_type'] = $user_type;
        $leadData['sales_agent_id'] = $sales_agent_id;
        $leadData['tpv_agent_id'] = $tpv_agent_id;
        $leadData['reason'] = $reason;
        $leadData['related_lead_ids'] = $related_lead_ids;
        $leadData['error_type'] = $error_type;
        $leadData['event_type'] = $event_type;
        $leadData['lead_status'] = $lead_status;
        $leadData['email_alert_message'] = $email_alert_message;
        $critical = $this::create($leadData);
        \Log::info('---------critical logs--------');
        \Log::info($critical);
    }

    public function updateId($oid,$newid,$status)
    {
        $data['lead_id'] = $newid;
        $data['tmp_lead_id'] = null;
        if(!empty($status)) {
            $data['lead_status'] = $status;
        }
        $this::where('tmp_lead_id', $oid)
            ->update($data);
    }

    public static function getEmailAlertMessage($lead_id=null)
    {        
        $instance = new static;
        return $instance->select('email_alert_message')->where('lead_id',$lead_id)->whereNotNull('email_alert_message')->pluck('email_alert_message')->toArray();
    }

    public static function boot()
    {
        parent::boot();
        static::created (function($log)
        {
            $events = [13,14,17,18,19,20,21,22,23,24,25,26,27,28,29,30,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47];
            // create logs for child lead
            if ($log->lead_id > 0 && in_array($log->event_type,$events)) {
                $leads = Telesales::select('id')->where('multiple_parent_id',$log->lead_id)->get();
                $data = $log->toArray();
                foreach ($leads as $key => $lead) {
                    info('creating child logs for lead Id: '.$lead->id);
                    $data['lead_id'] = $lead->id;
                    static::create($data);
                }
            }
        });
    }
}
