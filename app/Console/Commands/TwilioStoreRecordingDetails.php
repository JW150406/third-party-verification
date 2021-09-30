<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\TPVAgent\RecordingController;
use App\models\ClientWorkflow;
use App\models\TwilioStatisticsWorkers;
use App\models\TwilioStatisticsWorkersActivityduration;
use App\models\TwilioStatisticsWorkflow;
use App\models\TwilioStatisticsTaskqueue;
use App\models\TwilioStatisticsWorkspace;
use App\models\TwilioWorkspaceActivityStatistics;
use App\models\TwilioStatisticsSpecificWorker;
use App\models\TwilioStatisticsSpecificWorkerActivity;
use App\models\TwilioStatisticsCallLogs;
use App\models\TwilioStatisticsUsageRecords;
use App\models\UserTwilioId;
use Twilio\Exceptions\TwilioException;
use Auth;
use Log;
use Twilio\Rest\Client as TwilioClient;
use App\Services\TwilioService;
use Twilio\Rest\Client;
use App\models\TwilioLeadCallDetails;


class TwilioStoreRecordingDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twilio-recordings:store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will store all twilio call recordings details based on call id';

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
            Log::info("Twilio Missing Recording fetch Command started");
        
            $sId  = config('services.twilio')['accountSid'];
            $token  = config('services.twilio')['authToken'];
            $twilioClient  = new Client($sId, $token);

            // Fetch records with twilio recording url is null
            $callLists = TwilioLeadCallDetails::
            whereNull('twilio_recording_url')
            ->get();

            foreach($callLists as $key => $val){
                \Log::info("Id of twilio call details::".$val->id);
                if($val->call_id != null){
                    $calls = $twilioClient->recordings->read(['callSid'=> $val->call_id]);
                    if(!empty($calls)){
                        $val->twilio_recording_id = $calls[0]->sid;
                        $val->twilio_recording_url = 'https://api.twilio.com'.explode('.',$calls[0]->uri)[0];   
                        $val->save();
                        (new RecordingController())->downloadRecordingNew($val->task_id,false);
                    }
                    else{
                        \Log::info('No call recording is available for call id : '.$val->call_id);
                    }
                }
                else{
                    \Log::info('No call id is available');
                }
            }
            Log::info("Twilio Missing Recording fetch Command  end .");

        } catch(TwilioException $e) {
            Log::error($e->getMessage());
        }
    }
}
