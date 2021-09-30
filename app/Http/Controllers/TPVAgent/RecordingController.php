<?php

namespace App\Http\Controllers\TPVAgent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Calls\CallsController;
use App\models\Telesales;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use App\User;
use DB;
use App\Services\StorageService;
use App\models\Brandcontacts;
use App\models\TwilioLeadCallDetails;
use App\Services\TwilioService;

class RecordingController extends Controller
{

    function __construct(){
        $this->storageService = new StorageService;
        $this->twilioService = new TwilioService;
    }

    /**
     * This method is used for download recording
     * @param #taskId, $isDeleteRecording, $callId
     */
    public function downloadRecordingNew($taskId,$isDeleteRecording = true,$callId=''){

        Log::info('In recording download new function...');
        // Update in the Twilio Lead Call Details Table

        // for tpv ivr call details
        if($callId != ''){            
            $callDetails = TwilioLeadCallDetails::where('call_id',$callId)->first();    
        }
        else{
            $callDetails = TwilioLeadCallDetails::where('task_id',$taskId)->first();
        }
        if(empty($callDetails)){
            return false;
        }
        Log::debug($callDetails);
        Log::info('Recording Download start...');
        $url = $this->downloadFile($callDetails->twilio_recording_url, $callDetails->client_id);
        Log::info('Recording Download end...');
        Log::info('Results -> ');
        Log::info($url);
        if ($url == false) {
            return false;
        }
        
        $callDetails->recording_url = $url;
        $callDetails->recording_downloaded = 1;

        // Fetch the call duration to update in the recordings table
        $seconds = 0;
        $recording = $this->twilioService->retrieveRecordings($callDetails->twilio_recording_id);
        if ($recording) {
            $seconds = $recording->duration;
            $callDetails->call_duration = $seconds;
        }

        // Update the record
        $callDetails->save();

        // Update in the leads table
        $leadId = $callDetails->lead_id;

        // Update in telesales table if lead id exist
        $leads = Telesales::find($leadId);
        if (!$leads) {
            \Log::info('No leads available for save recordings');
        } else {
            $leads->s3_recording_url = $url;
            $leads->twilio_recording_url = $callDetails->twilio_recording_url;
            $leads->recording_id = $callDetails->twilio_recording_id;
            $leads->recording_downloaded = 1;
            $leads->call_duration = $seconds;
            $leads->save();
            \Log::info('Successfully updated recordings for lead id' . $leads->id);

            //check if this lead has child leads or not
            $isChildExist = (new Telesales())->getChildLeads($leads->id);
            $data = [];
            //if child leads are exist then update same details same as parent lead 
            if(isset($isChildExist) && $isChildExist->count() > 0){
                
                $data['s3_recording_url'] = $url;
                $data['twilio_recording_url'] = $callDetails->twilio_recording_url;
                $data['recording_id'] = $callDetails->twilio_recording_id;
                $data['recording_downloaded'] = 1;
                $data['call_duration'] = $seconds;
                foreach($isChildExist as $key => $val){
                    \Log::info('Child lead id is '.$val->id);
                    (new Telesales())->updateChildLeads($val->id,$data);
                    \Log::info('Child lead recording details are successfully updated');
                }
            }
        }
        //Now delete the recordings
        if($isDeleteRecording == true){

            \Log::info('Delete recording flag set to true');
            $deletedRecord = $this->twilioService->deleteRecordings($callDetails->twilio_recording_id);
            if ($deletedRecord) {
                // Update the call details table
                $callDetails->recording_deleted_on_twilio = 1;
                $callDetails->save();
    
                // Update the lead details table
                if (!$leads) {
                    \Log::info('No leads available for save recordings - delete');
                } else {
                    $leads->recording_deleted_on_twilio = 1;
                    $leads->save();    
                }
                \Log::info("Recording deleted on twilio for task id: " . $taskId);
            } else {
                \Log::info("Unable to delete recording on twilio for task id: " . $taskId);
            }
            return true;
        }
        else{
            //Delete recording flag set to false
            \Log::info('Delete recording flag set to false');
        }
    }
    
    /**
      * This method is used for download recording files
      * @param $url, $clientId
    */
    public function downloadFile($url, $clientId) {
        \Log::debug("downloadFile In Recordings controller: " . $url);

        $awsFolderPath = config()->get('constants.aws_folder');
        $filePath = 'clients_data/' . $clientId . '/'.config()->get('constants.TPV_RECORDING_UPLOAD_PATH');
        $fileName = str_random(32).'.WAV';

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
        }
        curl_close($ch);

        if ($status == 200) {
            Log::info('Start to save to storage -> start');
            $path = $this->storageService->uploadFileToStorage($result, $awsFolderPath, $filePath, $fileName);
            Log::info('Start to save to storage -> end');
            return $path;
        }

        return false;
      }
}
