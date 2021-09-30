<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use Twilio\TwiML\VoiceResponse;
use Carbon\Carbon;

class TwilioService
{
    public function __construct() {
        $this->sId    = config('services.twilio')['accountSid'];
        $this->token  = config('services.twilio')['authToken'];
        $this->twilioClient  = new Client($this->sId, $this->token);
    }

    /**
     * For make twilio voice call 
     * @param $tpvNumber, $toNumber, $id
     */
    public function makeVoiceCall($tpvNumber, $toNumber, $id) {
        try{
            $twimlUrl = route('generate-voice-otp-message', $id);
            $this->twilioClient->calls->create(
                "+1".$toNumber, // Call this number
                $tpvNumber, // From a valid Twilio number
                ["url" => $twimlUrl]
             );
            return true;
        } catch(TwilioException $e){
            \Log::error("Error while sending voice OTP:" . $e->getMessage());
            return false;
        }
    }

    /**
     * This method is used for generate voice call for otp
     * @param $otpCode, $id, $digits
     */
    public function generateTwiMLForVoiceCall($otpCode,$id,$digits) {
        $otpCode = implode('. ', str_split($otpCode));
        Log::info($otpCode);
        $voiceMessage = new VoiceResponse();
        
        if($digits == 2)
        {   
            $voiceMessage->say('GoodBye.');
        }
        elseif($digits == 1 || $digits == '')
        {
            if($digits == '')
                $voiceMessage->say('This is an automated call providing you your OTP from the TPV360.');
            $voiceMessage->say('Your one time password is ' . $otpCode);
            $gather = $voiceMessage->gather(['action' => route('generate-voice-otp-message',['id'=>$id]),
            'method' => 'POST','timeout'=>5,'numDigits'=>1]);
            $gather->say('Press 1 to Repeat or 2 to exit.');
            $voiceMessage->say('No Input Detected GoodBye');
        }
        else
        {
            $voiceMessage->say('You have entered Wrong Input. GoodBye');   
        }

        return $voiceMessage;
    }

    /**
     * Create task on twilio
     * @param $workspaceId, $toData
     */
    public function createTask($workspaceId, $toData)
    {
        try {
            $task = $this->twilioClient->taskrouter->v1->workspaces($workspaceId)
                    ->tasks
                    ->create($toData);
            return $task;
        } catch(TwilioException $e) {
            \Log::error("Error while creating task on twilio :" . $e->getMessage());
        }
    }

    /**
     * Retrieve task from twilio by workspace and task id
     * @param $workspaceId, $taskId
     */
    public function retrieveTask($workspaceId, $taskId) {
      $task = $this->twilioClient->taskrouter->v1->workspaces($workspaceId)
              ->tasks($taskId)
              ->fetch();
      return $task;
    }

    /**
     * Retrieve task from twilio by workspace and task id
     * @param $workspaceId, $taskId, $toTask
     */
    public function updateTask($workspaceId, $taskId, $toTask) {
      $task = $this->twilioClient->taskrouter->v1->workspaces($workspaceId)
              ->tasks($taskId)
              ->update($toTask);
      return $task;
    }

    /**
     * Retrieve recording from twilio
     * @param $recordingId
     */
    public function retrieveRecordings($recordingId) {
      return $this->twilioClient->recordings($recordingId)->fetch();
    }

    /**
     * Make an API call to twilio for delete a task
     * @param $workspaceId, $taskId
     */
    public function deleteTask($workspaceId, $taskId) {
        $task = $this->twilioClient->taskrouter->v1->workspaces($workspaceId)
            ->tasks($taskId)
            ->delete();
        return $task;
    }

    /**
     * Make an API call on twilio to retrieve workers
     * @param $workspaceId, $toData
     */
    public function workers($workspaceId, $toData)
    {
        return $this->twilioClient->taskrouter->v1->workspaces($workspaceId)
            ->workers
            ->read($toData, 1);
    }

    /**
     * Make an API call on twilio to retrieve activities
     * @param $workspaceId, $toData
     */
    public function retrieveActivities($workspaceId, $toData) {
          return $this->twilioClient->taskrouter->v1->workspaces($workspaceId)
                        ->activities
                        ->read($toData, 1);
    }

    /**
     * Make an API call to twilio to update worker
     * @param $workspaceId, $workerId, $toData
     */
    public function updateWorker($workspaceId, $workerId, $toData) {
         return $this->twilioClient->taskrouter->v1->workspaces($workspaceId)
                    ->workers($workerId)
                    ->update($toData);
    }

    /**
     * Make an API call to twilio to create worker
     * @param $workspaceId, $toData
     */
    public function createWorker($workspaceId, $toData) {
        return $this->twilioClient->taskrouter->v1->workspaces($workspaceId)
            ->workers
            ->create($toData);
    }

    /**
     * This method is used to check about worker as per given parameters
     * @param $twilioClient, $val, $workspaceId, $reqParam
     */
    public function checkWorker($twilioClient,$val,$workspaceId, $reqParam)
    {
        try{
            // $minutes = ''
            $workerStatistics = $twilioClient->taskrouter->v1->workspaces($workspaceId)
                ->workers($val->twilio_id)
                ->statistics()
                ->fetch($reqParam);
                return $workerStatistics;
        }catch(TwilioException $e)
        {
            return false;
        }
    }

    /**
     * This method is used for check work flow of client as per given parameters
     * @param $twilioClient, $val, $workspaceId, $reqParam
     */
    public function checkWorkflow($twilioClient,$val,$workspaceId, $reqParam)
    {
        try{
            // $minutes = ''
            $workflow_statistics = $twilioClient->taskrouter->v1->workspaces($workspaceId)
            ->workflows($val)
            ->statistics()
            ->fetch($reqParam);
                return $workflow_statistics;
        }catch(TwilioException $e)
        {
            return false;
        }
    }
    
    /**
     * This methos is used to check about workspace as per values of given parameters
     * @param $twilioClient, $workspaceId, $reqParam
     */
    public function checkWorkSpace($twilioClient,$workspaceId, $reqParam)
    {
        try{
            // $minutes = ''
            $workspaceStatistics = $twilioClient->taskrouter->v1->workspaces($workspaceId)
                                    ->statistics()->fetch($reqParam);
                return $workspaceStatistics;
        }catch(TwilioException $e)
        {
            return false;
        }
    }

    /**
     * This method is used to check workers of particular work space
     * @param $twilioClient, $workspaceId, $reqParam
     */
    public function checkWorkSpaceWokers($twilioClient,$workspaceId, $reqParam)
    {
        try{
            // $minutes = ''
            $workers_statistics = $twilioClient->taskrouter->v1->workspaces($workspaceId)
            ->workers
            ->statistics()
            ->fetch($reqParam);
                return $workers_statistics;
        }catch(TwilioException $e)
        {
            return false;
        }
    }

    /**
     * For check task queue of client and workspace
     * @param $twilioClient, $workspaceId
     */
    public function checkTaskqueue($twilioClient,$workspaceId)
    {
        try{
            
            $taskQueues = $twilioClient->taskrouter->v1->workspaces($workspaceId)
            ->taskQueues
            ->read();

            return $taskQueues;
        }catch(TwilioException $e)
        {
            return false;
        }
    }

    /**
     * Make an API call to twilio to retrieve worker
     * @param $workspaceId, $workerId
     */
    public function getWorker($workspaceId, $workerId) {
        return $this->twilioClient->taskrouter->v1->workspaces($workspaceId)
            ->workers($workerId)
            ->fetch();
    }

    /**
     * Make an API call to delete recordings on twilio
     * @param $recordingId
     */
    public function deleteRecordings($recordingId) {
        try {
            // $recording = $this->twilioClient->recordings($recordingId)->delete();
            return true;
        } catch (TwilioException $e) {
            \Log::error("Error while deleting recording: " . $e->getMessage());
            return false;
        }
    }

}
