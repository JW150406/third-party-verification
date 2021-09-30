<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\models\Telesales;
use App\Services\StorageService;
use App\Services\TwilioService;
use App\models\TwilioLeadCallDetails;

class DownloadTPVRecordings extends Command
{

    public $storageService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tpv-recordings:download';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command download tpv recordings for leads.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->storageService = new StorageService;
        $this->twilioService = new TwilioService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      Log::debug('Recording Download command start...');
      $leads = Telesales::whereNotNull('call_id')->whereNotNull('twilio_recording_url')->whereNull('s3_recording_url')->get();

      foreach ($leads as $lead) {
        $url = $this->downloadFile($lead->twilio_recording_url, $lead->client_id);
        if ($url == false) {
            return false;
        }
        $lead->s3_recording_url = $url;
        $lead->recording_downloaded = $lead->recording_downloaded + 1;
        $lead->save();

        //This function is used to save recordings to twilio calls details table for billing report
        $this->saveRecordingToTwilioCallsDetails($lead,$url);

        Log::info("TPV recording downloaded for lead: " . array_get($lead, 'id'));
      }
      Log::debug('Recording Download command end...');

      Log::debug("Recording delet process start...");

      $leads = Telesales::whereNotNull('call_id')->whereNotNull('recording_id')->whereNotNull('twilio_recording_url')->whereNotNull('s3_recording_url')->where('recording_deleted_on_twilio', 0)->orderBy('id', 'desc')->limit(1)->get();
      foreach ($leads as $lead) {
        Log::debug("Lead Id: " . $lead->id);
        $deletedRecord = $this->twilioService->deleteRecordings(array_get($lead, 'recording_id'));
        if ($deletedRecord) {
            $lead->recording_deleted_on_twilio = 1;
            $lead->save();
            \Log::info("Recording deleted on twilio for lead with id: " . array_get($lead, 'id'));
        } else {
            \Log::info("Unable to delete recording on twilio for lead with id: " . array_get($lead, 'id'));
        }
      }

      Log::debug("Recording delete process end...");

    }

  /**
   * Function that stores recordings to twilio call details for billing report details
   * @param $lead, $url
   */
  public function saveRecordingToTwilioCallsDetails($lead,$url)
    {
      $callDetails = TwilioLeadCallDetails::where('lead_id',$lead->id)->orderBy('id','desc')->first();
          if(!empty($callDetails)){
            $callDetails->recording_url = $url;
            $callDetails->save();
          }
    }

    /**
     * For Retrive file from url and store it to s3 storage
     * @param $url, $clientId
     */
    protected function downloadFile($url, $clientId) {
        \Log::debug("downloadFile: " . $url);

        $awsFolderPath = config()->get('constants.aws_folder');
        $filePath = 'clients_data/' . $clientId . '/'.config()->get('constants.TPV_RECORDING_UPLOAD_PATH');
        $fileName = str_random(32).'.WAV';

        $response = $this->getFileByURL($url);

        if (empty($response)) {
          return false;
        }

        if ($response['status'] == 200) {
            Log::info('Start to save to storage -> start');
            $path = $this->storageService->uploadFileToStorage($response['result'], $awsFolderPath, $filePath, $fileName);
            Log::info('Start to save to storage -> end');
            return $path;
        } else {
          Log::error("Unable to download recording file from twilio url: " . $url);
          return false;
        }
      }

    /**
     * This method is used to Returns file by its url
     * @param $url
     */
    protected function getFileByURL($url) {
      $response = [];
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
      curl_setopt($ch, CURLOPT_USERPWD, config('services.twilio')['accountSid'] . ':' . config('services.twilio')['authToken']);

      $result = curl_exec($ch);
      $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      Log::info('CURL status -> '.$status);
      if (curl_errno($ch)) {
           Log::error('CURL Error:' . curl_error($ch));
           return $response;
      }
      curl_close($ch);
      $response['result'] = $result;
      $response['status'] = $status;
      return $response;
    }
}
