<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\models\Salescenterslocations;

class ChannelAssign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'locations:channel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command assign channel (tele,d2d) for all locations';

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

            // For get data of location of salescenters
            $locations = Salescenterslocations::all();
            
            foreach ($locations as $key => $location) {
                $location->channels()->delete();
                $location->channels()->create(['channel'=>'d2d']);
                $location->channels()->create(['channel'=>'tele']);
            }
        } catch (\Exception $e) {
            \Log::error($e);
        }
        
    }
}
