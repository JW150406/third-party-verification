<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\models\Salescenterslocations;
use App\User;


class LocationAssign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'locations:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command assign locations for all sales center QA';

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
            
            $users = User::with('roles')->whereHas('roles', function ($query) {
                    $query->where('name','sales_center_qa');
                })->get();
            foreach ($users as $key => $user) {
                $locationIds = Salescenterslocations::where('status','active')->where('salescenter_id',$user->salescenter_id)->pluck('id');
                $user->locations()->sync($locationIds);
            }

        } catch (\Exception $e) {
            \Log::error($e);
        }
    }
}
