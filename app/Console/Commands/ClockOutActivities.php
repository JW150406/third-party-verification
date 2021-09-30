<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\models\SalesAgentActivity;
use App\User;
use App\Traits\CustomTrait;
use Carbon\Carbon;
use Log;

class ClockOutActivities extends Command
{
    use CustomTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity:clockout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command to, if there is no activity in 1 hour, will give it a cloak out.';

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
        try {
            // Get time and last activity before 1 hour
            $time = now()->subHours(config('constants.SALES_AGENT_ACTIVITY_HOURS'));
            $last_activity = now()->subHours(config('constants.SALES_AGENT_LAST_ACTIVITY_HOURS'));
            
            // Get activity details of sales agent
            $activities = SalesAgentActivity::where('activity_type','clock_in')
                ->whereDate('created_at',today())
                ->where('created_at','<',$time)
                ->whereHas('agent', function ($query) use ($last_activity) {
                    $query->where('last_activity','<', $last_activity);
                })
                ->groupBy('agent_id')->get();
            if ($activities->isNotEmpty()) {
                foreach ($activities as $key => $activity) {
                    $this->updatePendingActivity($activity->agent_id);
                    $activity->out_time = now();
                    $activity->activity_type = 'clock_out';
                    $activity->save();

                    Log::info('Clock out due to in-activity, Agent id: '.$activity->agent_id);    
                }            
            }
        } catch (\Exception $e) {
            Log::error($e);            
        } 
    }
}
