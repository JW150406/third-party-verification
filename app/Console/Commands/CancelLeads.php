<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\LeadTrait;
use App\models\Zipcodes;
use App\models\Programs;
use App\models\TelesalesTmp;
use Carbon\Carbon;
use Log;

class CancelLeads extends Command
{
    use LeadTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command create cancelled lead if temp lead not proceed or cancel in 10 minute.';

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
        $date = \Carbon\Carbon::now()->subMinutes(60)->toDateTimeString();
        $tmpLeads = TelesalesTmp::where('created_at','<',$date)->where('is_proceed',0)->get();
        foreach ($tmpLeads as $key => $tmpLead) {
            $this->cancel($tmpLead);
        }
    }

    /**
     * For cancel lead from corn job
     * @param $tmpLead
     */
    public function cancel($tmpLead)
    {
        try {
            // Check for temp data is available or not
            if($tmpLead){

                $zipcode = isset($tmpLead->zipcode) ? $tmpLead->zipcode : null;
                $zipcodeData = Zipcodes::where('zipcode', $zipcode)->first();

                if (empty($zipcodeData)) {
                    Log::error("Invalid zipcode: ".$zipcode);
                    return ;
                }
                $reqPrograms = explode(',',$tmpLead->program);
                if (is_array($reqPrograms)) {
                    foreach ($reqPrograms as $pId) {
                        $program = Programs::find($pId);
                        if (empty($program)) {
                            Log::error("This program was not found.");
                            return ;
                        }
                    }
                } else {
                    Log::error("Program not found.");
                    return ;
                }
                $telesale = $this->createLead($tmpLead,$zipcodeData,$reqPrograms,'cancel', true);
                $isValid = $this->validateSalesAgentEmailAndPhone($telesale,$tmpLead);
                $this->sendCriticalAlertMail($telesale); 
                Log::info('Cancelled lead created from corn job.');
            }else{
                Log::error('TelesalesdataTmp not found.');
                return ;
            }
        }catch(\Exception $e) {
            Log::error($e);
            return ;
        }
    }
}
