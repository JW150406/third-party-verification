<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class TwilioStatisticsWorkers extends Model
{
    protected $table = 'twilio_statistics_workers';    
    protected $fillable = ['workspace_id', 'account_id','cumulative_reservations_created','cumulative_reservations_accepted','cumulative_reservations_rejected','cumulative_reservations_timed_out','cumulative_reservations_canceled','cumulative_reservations_rescinded','cumulative_start_time','cumulative_end_time','realtime_total_workers'];

    public function saveWorkersStatistics($twilioDate,$workers_statistics,$update = false)
    {
        
        if($update == true)
        {

            $twilioStatisticsWorkers = TwilioStatisticsWorkers::where('workspace_id',$workers_statistics->workspaceSid)->where('created_at','>=',$twilioDate.' 00:00:00')->first();   
        }
        else
        {
            $twilioStatisticsWorkers = new TwilioStatisticsWorkers();
        }
        $twilioStatisticsWorkers->workspace_id = $workers_statistics->workspaceSid;
        $twilioStatisticsWorkers->account_id = $workers_statistics->accountSid;
        $twilioStatisticsWorkers->cumulative_reservations_created = $workers_statistics->cumulative['reservations_created'];
        $twilioStatisticsWorkers->cumulative_reservations_accepted = $workers_statistics->cumulative['reservations_accepted'];
        $twilioStatisticsWorkers->cumulative_reservations_rejected = $workers_statistics->cumulative['reservations_rejected'];
        $twilioStatisticsWorkers->cumulative_reservations_timed_out = $workers_statistics->cumulative['reservations_timed_out'];
        $twilioStatisticsWorkers->cumulative_reservations_canceled = $workers_statistics->cumulative['reservations_canceled'];
        $twilioStatisticsWorkers->cumulative_reservations_rescinded = $workers_statistics->cumulative['reservations_rescinded'];
        $twilioStatisticsWorkers->cumulative_reservations_completed = $workers_statistics->cumulative['reservations_completed'];
        $twilioStatisticsWorkers->tasks_assigned = $workers_statistics->cumulative['tasks_assigned'];
        $twilioStatisticsWorkers->cumulative_start_time = $workers_statistics->cumulative['start_time'];
        $twilioStatisticsWorkers->cumulative_end_time = $workers_statistics->cumulative['end_time'];
        $twilioStatisticsWorkers->realtime_total_workers = $workers_statistics->realtime['total_workers'];
        $twilioStatisticsWorkers->save();
        return $twilioStatisticsWorkers->id;
    }
}
