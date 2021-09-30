<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\models\Telesales;
use App\models\Client;
use DB;

class UpdateClientId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:client-id {oldId} {newId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        try{
            $oldId = $this->argument('oldId');
            $newId = $this->argument('newId');
            $isExistClient = Client::find($newId);
            $isExistClientOld = Client::find($oldId);
            if(!empty($isExistClient)){
                $msg = 'New client Id '.$newId.' is already exist';
                \Log::info($msg);
                $this->error($msg);
                return false;
            }
            if(empty($isExistClientOld)){
                $msg = 'Old client Id '.$oldId.' is not exist';
                \Log::info($msg);
                $this->error($msg);
                return false;
            }
            // Begins the transaction for updating client id
            DB::beginTransaction(); 

            // Update client id Required tables
            $answer = DB::update('update clients set id='.$newId.' where id='.$oldId.'');
            \Log::info('successfully updated clients table '.$answer);
            $answer = DB::update('update brand_contacts set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated brand_contacts table '.$answer);
            $answer = DB::update('update call_answers set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated call_answers table '.$answer);
            $answer = DB::update('update clientsforms set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated clientsforms table '.$answer);
            $answer = DB::update('update client_agent_not_found_scripts set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated client_agent_not_found_scripts table '.$answer);
            $answer = DB::update('update client_twilio_numbers set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated client_twilio_numbers table '.$answer);
            $answer = DB::update('update client_twilio_workflowids set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated client_twilio_workflowids table '.$answer);
            $answer = DB::update('update client_twilio_workspace set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated client_twilio_workspace table '.$answer);
            $answer = DB::update('update commodities set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated commodities table '.$answer);
            $answer = DB::update('update compliance_templates set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated compliance_templates table '.$answer);
            $answer = DB::update('update customer_types set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated customer_types table '.$answer);
            $answer = DB::update('update dispositions set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated dispositions table '.$answer);
            $answer = DB::update('update form_scripts set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated form_scripts table '.$answer);
            $answer = DB::update('update fraud_alerts set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated fraud_alerts table '.$answer);
            $answer = DB::update('update programs set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated programs table '.$answer);
            $answer = DB::update('update salescenters set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated salescenters table '.$answer);
            $answer = DB::update('update salescenterslocations set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated salescenterslocations table '.$answer);
            $answer = DB::update('update script_questions set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated script_questions table '.$answer);
            $answer = DB::update('update settings set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated settings table '.$answer);
            $answer = DB::update('update settings_tpv_now_restricted_timezones set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated settings_tpv_now_restricted_timezones table '.$answer);
            $answer = DB::update('update telesales set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated telesales table '.$answer);
            $answer = DB::update('update telesales_tmp set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated telesales_tmp table '.$answer);
            $answer = DB::update('update twilio_lead_call_details set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated twilio_lead_call_details table '.$answer);
            $answer = DB::update('update users set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated users table '.$answer);
            $answer = DB::update('update user_assigned_forms set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated user_assigned_forms table '.$answer);
            $answer = DB::update('update utilities set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated utilities table '.$answer);
            $answer = DB::update('update utility_validations set client_id='.$newId.' where client_id='.$oldId.'');
            \Log::info('successfully updated utility_validations table '.$answer);

            DB::commit();
            // Update maximum client id to auto increment in clients table
            $maxClientId = Client::max('id');
            DB::statement("ALTER TABLE clients AUTO_INCREMENT = ".$maxClientId.";");
            \Log::info('Successfully set auto increment value .'.$maxClientId);
            \Log::info('Successfully updated client id in required tables');
            $this->info('Successfully updated client Id');

        }catch (\Exception $e) {
            \Log::error($e);
            DB::rollback();
            $this->error($e->getMessage());
        }
    }
}
