<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TwilioStatisticsWorkersActivityduration extends Model
{
    protected $table = 'twilio_statistics_workers_activity_duration';    
    protected $fillable = ['workspaces_id','workspace_id', 'cumulative_activity_name','cumulative_maxtime','cumulative_mintime','cumulative_totaltime','cumulative_avgtime','cumulative_sid','realtime_workers','realtime_friendly_name','realtime_sid'];

    public function updateTwilioWorkersActivityDuration($wid,$twilioDate,$activityStatistics,$activityDurations,$workspaceSid)
    {
        for($i=0;$i<count($activityStatistics);$i++)
        {
            $twilioWorkerDurations = TwilioStatisticsWorkersActivityduration::where('workspaces_id',$wid)->where('cumulative_sid',$activityDurations[$i]['sid'])->where('created_at','>=',$twilioDate.' 00:00:00')->get();
            if(isset($twilioWorkerDurations) && $twilioWorkerDurations->count() > 0)
            {
                $workerActivityDuration = $twilioWorkerDurations[0];
            }
            else
                $workerActivityDuration = new TwilioStatisticsWorkersActivityduration();
            
            $workerActivityDuration->workspaces_id = $wid;    
            $workerActivityDuration->workspace_id = $workspaceSid;
            $workerActivityDuration->realtime_workers = $activityStatistics[$i]['workers'];
            $workerActivityDuration->cumulative_activity_name = $activityDurations[$i]['friendly_name'];
            $workerActivityDuration->cumulative_maxtime = $activityDurations[$i]['max'];
            $workerActivityDuration->cumulative_mintime = $activityDurations[$i]['min'];
            $workerActivityDuration->cumulative_totaltime = $activityDurations[$i]['total'];
            $workerActivityDuration->cumulative_avgtime = $activityDurations[$i]['avg'];
            $workerActivityDuration->cumulative_sid = $activityDurations[$i]['sid'];
            $workerActivityDuration->save();
            // echo "<pre>";
            // print_r($workerActivityDuration);
        }
        // dd();
    }
}
