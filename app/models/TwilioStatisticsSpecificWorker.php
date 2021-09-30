<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class TwilioStatisticsSpecificWorker extends Model
{
    protected $table = 'twilio_statistics_specific_Worker';    
    protected $fillable = ['Worker_id',
    'Workspace_id',
    'cumulative_reservations_created',
    'cumulative_reservations_accepted',
    'cumulative_reservations_rejected',
    'cumulative_reservations_timed_out',
    'cumulative_reservations_canceled',
    'cumulative_reservations_rescinded',
    'cumulative_reservations_completed',
    'cumulative_start_time',
    'cumulative_end_time'];

    public function saveSpecificWorkerData($twilioDate,$workerStatistics,$val,$update = false)
    {
        if($update == true)
        {
            $twilioWorkerData = TwilioStatisticsSpecificWorker::where('Worker_id',$val->twilio_id)->where('created_at','>=',$twilioDate.' 00:00:00')->get()->first();
            $twilioWorker = $twilioWorkerData;
        }
        else
            $twilioWorker = new TwilioStatisticsSpecificWorker();
        $twilioWorker->Worker_id = $workerStatistics->workerSid;
        $twilioWorker->Workspace_id = $workerStatistics->workspaceSid;
        $twilioWorker->cumulative_reservations_created = $workerStatistics->cumulative['reservations_created'];
        $twilioWorker->cumulative_reservations_accepted = $workerStatistics->cumulative['reservations_accepted'];
        $twilioWorker->cumulative_reservations_rejected = $workerStatistics->cumulative['reservations_rejected'];
        $twilioWorker->cumulative_reservations_timed_out = $workerStatistics->cumulative['reservations_timed_out'];
        $twilioWorker->cumulative_reservations_canceled = $workerStatistics->cumulative['reservations_canceled'];
        $twilioWorker->cumulative_reservations_completed = $workerStatistics->cumulative['reservations_completed'];
        $twilioWorker->cumulative_reservations_rescinded = $workerStatistics->cumulative['reservations_rescinded'];
        $twilioWorker->cumulative_start_time = $workerStatistics->cumulative['start_time'];
        $twilioWorker->cumulative_end_time = $workerStatistics->cumulative['end_time'];
        $twilioWorker->save();
        return $twilioWorker->id;
    }
}
