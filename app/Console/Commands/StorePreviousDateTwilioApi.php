<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use FarhanWazir\GoogleMaps\GMaps;
use App\models\Zipcodes;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\AgentPanel\TPVAgent\TwilioController;
use Twilio\Exceptions\TwilioException;
use Auth;
use Log;
use Twilio\Rest\Client as TwilioClient;
use App\Services\TwilioService;
use Twilio\Rest\Client;

class StorePreviousDateTwilioApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twilio-previous-date:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command stores twilio api records ofprevious date.';

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
        $sDate = date_create(Carbon::yesterday(), timezone_open(getClientSpecificTimeZone()));
        $eDate = $sDate;
        $toronto_timezone = date_format($sDate, 'P');
        $sDate = $sDate->format('Y-m-d');
        $eDate = $eDate->modify('+1 day')->format('Y-m-d');
        $startDate = date($sDate.'\T00:00:00'.$toronto_timezone);
        $endDate = date($eDate.'\T00:00:00'.$toronto_timezone);
        \Log::info('Start Date  '. $startDate);
        \Log::info('End Date  '. $endDate);
        try{
            Log::info("Store Twilio previous record APi Command started");
        
                $sId  = config('services.twilio')['accountSid'];
                $token  = config('services.twilio')['authToken'];
                $twilioClient  = new Client($sId, $token);
                $workspace = DB::table('client_twilio_workspace')->pluck('workspace_id')->unique('workspace_id');
                $workspaceId = $workspace[0];
                \Log::info('Start Date '.$startDate);
                \Log::info('End Date '.$endDate);

                //Call function that stores twilio statistics api record into database
                (new TwilioController)->fetchTwilioStatisticsApiRecord($twilioClient,$startDate,$endDate,$workspaceId);
            
            Log::info("Store Twilio previous record APi Command end");
        }
        catch(TwilioException $e)
        {
            Log::error($e->getMessage());
        }
    }
}
