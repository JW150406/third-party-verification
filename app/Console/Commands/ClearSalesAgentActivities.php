<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\models\SalesAgentActivity;
use App\models\Salesagentlocation;
use Carbon\Carbon;
use Log;

class ClearSalesAgentActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command to, the sales agent before 3 months will delete the activities.';

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
            // For get 3 months before date from current date
            $date = Carbon::now()->startOfMonth()->subMonth(3);
            SalesAgentActivity::whereDate('created_at','<',$date)->delete();
            Salesagentlocation::whereDate('created_at','<',$date)->delete();

            Log::info('Sales agent activities cleared before '.$date->toDateString());
        } catch (\Exception $e) {
            Log::error($e);
        }
    }
}
