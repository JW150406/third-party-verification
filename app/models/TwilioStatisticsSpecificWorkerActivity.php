<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;

class TwilioStatisticsSpecificWorkerActivity extends Model
{
    protected $table = 'twilio_statistics_specific_Worker_activity_duration';    
    protected $fillable = ['workers_id','Worker_id',
    'cumulative_activity_name',
    'cumulative_maxtime',
    'cumulative_mintime',
    'cumulative_totaltime',
    'cumulative_avgtime',
    'cumulative_sid'];

    public function saveSpecificWorkerActivityData($workerId,$twilioDate,$workerStatistics,$val,$update = false,$startDate = '')
    {
        for($i=0;$i<count($workerStatistics->cumulative['activity_durations']);$i++)
        {
            $twilioWorker = TwilioStatisticsSpecificWorkerActivity::where('workers_id',$workerId)->where('cumulative_sid',$workerStatistics->cumulative['activity_durations'][$i]['sid'])->where('created_at','>=',$twilioDate.' 00:00:00')->get();
            if($twilioWorker->count() > 0)
            {  
                $twilioSpecificWorkerActivity = $twilioWorker[0];
                \Log::info('Update....');
            }
            else
            {   
                $twilioSpecificWorkerActivity = new TwilioStatisticsSpecificWorkerActivity();
            }
            $twilioSpecificWorkerActivity->workers_id = $workerId;
            $twilioSpecificWorkerActivity->Worker_id = $workerStatistics->workerSid;
            $twilioSpecificWorkerActivity->cumulative_activity_name = $workerStatistics->cumulative['activity_durations'][$i]['friendly_name'];
            $twilioSpecificWorkerActivity->cumulative_maxtime = $workerStatistics->cumulative['activity_durations'][$i]['max'];
            $twilioSpecificWorkerActivity->cumulative_mintime = $workerStatistics->cumulative['activity_durations'][$i]['min'];
            $twilioSpecificWorkerActivity->cumulative_totaltime = $workerStatistics->cumulative['activity_durations'][$i]['total'];
            $twilioSpecificWorkerActivity->cumulative_avgtime = $workerStatistics->cumulative['activity_durations'][$i]['avg'];
            $twilioSpecificWorkerActivity->cumulative_sid = $workerStatistics->cumulative['activity_durations'][$i]['sid'];
            $twilioSpecificWorkerActivity->created_at = Carbon::parse($startDate);
            $twilioSpecificWorkerActivity->save();
        }
    }

    public function getWorkerSpeficActivityTotalTime($workerId,$startDate,$endDate,$type)
    {
        $twilioWorker = TwilioStatisticsSpecificWorkerActivity::select(DB::raw('SUM(cumulative_totaltime) as totalTime'))
                            ->where('Worker_id',$workerId)
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->where('cumulative_activity_name',$type)
                            ->groupBy('Worker_id')
                            ->first();
        return $twilioWorker;
        
    }
}
