<?php

namespace App\Http\Controllers\Conference;

use App\Http\Controllers\Calls\CallsController;
use App\Http\Controllers\TPVAgent\RecordingController;
use App\models\Phonenumberverification;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\ActiveCall;
use App\Http\Controllers\Controller;
use Twilio\Rest\Client;
use Twilio\Jwt\TaskRouter\WorkspaceCapability;
use Twilio\Jwt\TaskRouter\WorkerCapability;
use Twilio\Twiml;
use Twilio\Jwt\ClientToken;
use Auth;
use App\models\UserTwilioId;
use App\models\TwilioConnectedDevice;
use App\models\Telesales;
use App\models\Telesalesdata;
use App\models\Salescenter;
use App\models\Salescenterslocations;
use App\models\TwilioLeadCallDetails;
use AWS;
use App\User;
use Twilio\Exceptions\TwilioException;
use Log;
use App\models\ClientTwilioNumbers;
use App\Services\TwilioService;
use App\Traits\TwilioTrait;


class ConferenceController extends Controller
{
    use TwilioTrait;

     private $twilio_client   = array();
     private $workflowSid = "WWd18dc5feed687a8cd139e60c466e167a";
     public  $workspaceSid = 'WS773b86b9fe21ec7b213eb54af1019f6e';
     private $sid = "";
     private $token = "";
     private $client_token_number = "";
    public $ClientTwilioNumbers = array();

      function __construct(){
        $this->sid    = config('services.twilio')['accountSid'];
        $this->token  = config('services.twilio')['authToken'];
        $this->twilio_client  = new Client($this->sid, $this->token);
          $this->ClientTwilioNumbers = (new ClientTwilioNumbers);
          $this->twilioService = new TwilioService;

      }

    //   function downloadrecordings(){

    //       $recordings_to_download = (new Telesales)::select('call_id','twilio_recording_url')->where('recording_downloaded', 0)->whereNotNull('twilio_recording_url')->whereNotNull('call_id')->get();
    //      //  print_r($recordings_to_download);
    //      echo "started \n";
    //     if(count($recordings_to_download)> 0){
    //         foreach($recordings_to_download as $recordings_data){

    //               $download_file = $this->downloadfile($recordings_data['twilio_recording_url']);

    //               if($download_file['status']== 'success'){

    //                  (new Telesales)::where('call_id', $recordings_data['call_id'])
    //                  ->update([
    //                          's3_recording_url' => $download_file['url'],
    //                          'recording_downloaded' => 1,
    //                          ]);
    //               }
    //         }
    //     }
    //     echo "end";
    //   }




    //    function downloadfile($url_to_download){
    //      $url =  $url_to_download ;
    //      $file_name = rand().".WAV";
    //     if($url_to_download !="" ){
    //        $get_name =  explode('/',$url_to_download);
    //        if(count($get_name)>0){
    //         $file_name = $get_name[count($get_name)-1].".WAV";
    //        }

    //     }else{
    //        return $resposne = array('status' => 'error','message' => "Invalid request" );
    //     }

    //     $ch = curl_init($url);
    //     curl_setopt($ch, CURLOPT_HEADER, 0);
    //     curl_setopt($ch, CURLOPT_NOBODY, 0);
    //      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    //     $output = curl_exec($ch);
    //     $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //     print_r( curl_getinfo($ch) );
    //     curl_close($ch);
    //     if ($status == 200) {

    //         try {
    //             $s3 = AWS::createClient('s3');

    //           $obj =   $s3->putObject(array(
    //             'Bucket'     => env("BUCKET_NAME", "tpvmatrixm") ,
    //             'Key'        => $file_name,
    //             'Body'   =>  $output,
    //             'ACL'    =>  'public-read',

    //         ));
    //         $url = "https://s3.amazonaws.com/".env("BUCKET_NAME", "tpvmatrixm")."/".$file_name;
    //         $resposne = array('status' => 'success','url' =>$url );

    //     } catch (Aws\S3\Exception\S3Exception $e) {

    //           $resposne = array('status' => 'error','message' => $e->getMessage() );
    //      }
    //     }else{
    //         $resposne = array('status' => 'error','message' => "something went wrong. Please try again." );
    //     }

    //     return  $resposne;


    //    }

        public function wait()
        {
            return $this->generateWaitTwiml();
        }

        public function incomingcall(Request $request){
            return response('<Response>
            <Gather action="enqueue-call?wid='.$request->wid.'" numDigits="1" timeout="15">
              <Say voice="woman" language="en">For English, please press one.</Say>
              <Say voice="woman" language="es">Para español, pulse dos.</Say>

            </Gather>
          </Response>', 200)
                ->header('Content-Type', 'application/xml; charset=utf-8');

         }


         public function enqueuecall(Request $request){

            $digit_pressed = $request->Digits;

            if ($digit_pressed == '2') {
            $language = "es";
            } else {
            $language = "en";
            }
          //  $language = "en";
          //  .$this->workflowSid"
            return response('<Response>
            <Enqueue workflowSid="'.$request->wid.'">
            <Task>{"selected_language": "'.$language.'"}</Task>
            </Enqueue>
            </Response>', 200)
                ->header('Content-Type', 'application/xml; charset=utf-8');
         }

         public function createtask(){

            $twilio_client =  $this->twilio_client;
            // create a new task
            $task = $twilio_client->taskrouter
                ->workspaces($this->workspaceSid)
                ->tasks
                ->create(array(
                  'attributes' => '{"selected_language": "eS"}',
                  'workflowSid' => $this->workflowSid,
                ));


            // display a confirmation message on the screen
            echo "Created a new task";

         }

        //Receive Callbacks from twilio for all events
        public function assignment(Request $request) {
            $input = $request->all();

            if (isset($input['EventType'])) {
                \Log::info("assignment:" . $input['EventType']);
            } else {
                \Log::info("assignment: Not get any event type in request !!");
            }

            \Log::info("1.3  Declines");
            if (isset($input['EventType']) && $input['EventType'] == "reservation.completed" && isset($input['TaskAssignmentStatus']) && $input['TaskAssignmentStatus'] == "completed") {
                $telesales = Telesales::whereNotNull('call_id')->whereNotNull('twilio_recording_url')->whereNull('s3_recording_url')->where('recording_downloaded', 0)->get();
                foreach ($telesales as $telesale) {
                    // (new CallsController())->downloadRecording($telesale->id);
                }
                // for store each recording in twilio call details table
                // $twilioCalls = TwilioLeadCallDetails::where('task_id',$inputs['taskSid'])->first();
                // $twilioCalls->recording_url = $url;
                // $twilioCalls->save();
                $this->GetCallDuration();
            }

            return response('<Response></Response>', 200)->header('Content-Type', 'application/xml; charset=utf-8');
        }

        public function workflowAssignment(Request $request) {
            $input = $request->all();

            if (isset($input['EventType'])) {
                \Log::info("workflowAssignment:" . $input['EventType']);
            } else {
                \Log::info("workflowAssignment: Not get any event type in request !!");
            }

            $fromNumber =  config('services.twilio')['number'];
            $attributes = [];
            if (isset($input['TaskAttributes'])) {
                $attributes = json_decode($input['TaskAttributes']);
                if (isset($attributes->from)) {
                    $fromNumber = $attributes->from;
                }
            }

            $assignedWorker = $request->input("WorkerSid");
            \Log::info("1.2".$assignedWorker);
            $connectedDevices =   (new TwilioConnectedDevice)->connectedDevice();
            $callToDeviceId = "";
            if(count($connectedDevices) > 0) {
                foreach($connectedDevices as $singleDevice) {
                    $singleDevice->device_id;
                    $workersOnline = json_decode($singleDevice->workers_online);
                    if(in_array($assignedWorker,$workersOnline)){
                        $callToDeviceId = $singleDevice->device_id;
                        break;
                    }
                }
            }

            $assignmentInstruction  = [
                'instruction' => 'call',
                'from' => $fromNumber,
                'to' => 'client:'.$callToDeviceId,
                'url' => route('conference.dailClientNumber',['rid' => $request->input('ReservationSid'),'task_id' => $input['TaskSid'],'workspace_id' =>$request->input('WorkspaceSid')  ]) ,
            ];

            return response($assignmentInstruction, 200)->header('Content-Type', 'application/json');
        }

        public function dailClientNumber (Request $request){
             $inputs = $request->all();
                return response('<Response>
                              <Dial record="record-from-answer-dual" recordingStatusCallback="'.route('recordingstatus').'?call_id='.$inputs['CallSid'].'&amp;tid='.$request->input("task_id").'&amp;workspace_id='.$request->input("workspace_id").'" >
                                    <Queue reservationSid="'.$request->input("rid").'"/>
                              </Dial>
                          </Response>', 200)
                ->header('Content-Type', 'application/xml; charset=utf-8');
        }


        public function assignment_redirect(Request $request){

           return response('<Response>
                            <Dial>
                                <Client>'.$request->input("WorkerSid").'</Client>
                            </Dial>
                         </Response>', 200)
                ->header('Content-Type', 'application/xml; charset=utf-8');

        }

        /**
         * This is inbound call recording status callback
         */ 
        public function recordingstatus(Request $request)
        {
            \Log::info("In Recording status");
            $inputs = $request->all();
            \Log::info("In Recording status args: " . json_encode($inputs, JSON_PRETTY_PRINT));
            $recordingUrl = '';
            if (isset($inputs['call_id'])) {
                \Log::info("Call recording url: " . $inputs['RecordingUrl']);
                if($inputs['RecordingStatus'] == 'completed') {
                    \Log::info("If recording status is completed then.");
                    //fetch twilio call details for save recording
                    $callDetails = TwilioLeadCallDetails::where('task_id', $inputs['tid'])->first();
                    if (!empty($callDetails)) {
                        //Save call recording to twilio lead call details for billing report
                        $callDetails->twilio_recording_url = $inputs['RecordingUrl'];
                        $callDetails->twilio_recording_id = $inputs['RecordingSid'];
                        $callDetails->save();
                        \Log::info($callDetails);
                    }
    
                    (new RecordingController())->downloadRecordingNew($inputs['tid']);
    
                }
                $lead_data   =   (new Telesales)::where('call_id', $inputs['call_id'])->get();

                
                // (new Telesales)::where('call_id', $inputs['call_id'])
                // ->update([
                //     'recording_id' => $inputs['RecordingSid'],
                //     'twilio_recording_url' => $inputs['RecordingUrl'],
                // ]);

                //  if (count($lead_data) > 0) {
                //     (new CallsController())->downloadRecording($lead_data[0]->id);
                //     $leadUrl = Telesales::find($lead_data[0]->id);
                //     $recordingUrl = $leadUrl->s3_recording_url;
                //   }                              


                //  if(!empty($callDetails))
                //  {
                //     if(count($lead_data) == 0){
                //       $recordingUrl = (new CallsController())->downloadRecordingWithoutLead($inputs['RecordingUrl'],$callDetails->client_id);
                //     }

                //     \Log::info($callDetails);
                //  }
                \Log::info("Call recording updated to: " . $inputs['call_id']);

                $task =  $this->twilio_client->taskrouter->v1->workspaces($inputs['workspace_id'])
                ->tasks($inputs['tid'])
                    ->fetch();

                $attributes = json_decode($task->attributes);
                $conversation_data = array();
                $conversation_data['segment_link'] =   array($inputs['RecordingUrl']);
                $conversation_data['abandoned'] =   'No';
                $conversation_data['abandoned_phase'] =   'No';
                $conversation_data['communication_channel'] =   'Call';
                $conversation_data['conversation_id'] =   $inputs['tid'];
                $conversation_data['external_contact'] =   $attributes->from;
                $agent_data = [];
                if (count($lead_data) > 0) {
                    $lead_id = $lead_data[0]->id;
                    $program = (new Telesalesdata)->leadMetakeyData($lead_id, 'Program');
                    if ($program) {
                        $conversation_data['campaign'] =   $program;
                    }
                    $client = (new Telesalesdata)->leadMetakeyData($lead_id, 'client');
                    if ($client) {
                        $conversation_data['client'] =   $client;
                    }
                    $utility = (new Telesalesdata)->leadMetakeyData($lead_id, 'utility');
                    if ($client) {
                        $conversation_data['utility'] =   $utility;
                    }

                    $billing_name = (new Telesalesdata)->leadMetakeyData($lead_id, 'Billing Name');
                    if ($billing_name) {
                        $attributes->customers['name'] =   $billing_name;
                    }

                    $phone_number = (new Telesalesdata)->leadMetakeyData($lead_id, 'Phone Number');
                    if ($phone_number) {
                        $attributes->customers['phone'] =   $phone_number;
                    }


                    $sales_agent_data =  (new User)->getUser($lead_data[0]->user_id);
                    $agent_data = array();
                    if ($sales_agent_data) {
                        $conversation_data['sales_agent'] =   $sales_agent_data->first_name . " " . $sales_agent_data->last_name;
                        $conversation_data['sales_agent_email'] =   $sales_agent_data->email;

                        $salescenter =  (new Salescenter)->getSalescenterinfo($sales_agent_data->salescenter_id);
                        $location =  (new Salescenterslocations)->getLocationDetail($sales_agent_data->location_id);
                        $conversation_data['handling_team_name_in_hierarchy'] = $salescenter->name . " ▸ " . $location->name . " ▸ " . $sales_agent_data->first_name . " " . $sales_agent_data->last_name;
                    }

                    $tpv_agent =  (new User)->getUser($lead_data[0]->reviewed_by);

                    if ($tpv_agent) {
                        $conversation_data['tpv_agent'] =   $tpv_agent->first_name . " " . $tpv_agent->last_name;
                        $conversation_data['tpv_agent_email'] =   $sales_agent_data->email;
                        $agent_data['agent_id'] = $sales_agent_data->userid;
                        $agent_data['email'] = $sales_agent_data->email;
                        $agent_data['team'] = "TPV";
                    }
                }
                $attributes->conversations = $conversation_data;
                $attributes->agents = $agent_data;
                $updated_task =   $this->twilio_client->taskrouter->v1->workspaces($inputs['workspace_id'])
                ->tasks($inputs['tid'])
                    ->update(array(
                        "attributes" => json_encode($attributes)
                    ));
                //     print_r($attributes);
                //print_r($updated_task->attributes);

            }


            

            // \Log::info("NEW If started..");
            // $telesales = Telesales::whereNotNull('call_id')->whereNotNull('twilio_recording_url')->whereNull('s3_recording_url')->where('recording_downloaded', 0)->get();
            // foreach ($telesales as $telesale) {
            //     \Log::info("Telesale update start for telesale: " . $telesale->id);
            //     (new CallsController())->downloadRecording($telesale->id);
            //     \Log::info("Telesale update end for telesale: " . $telesale->id);
            // }
            // \Log::info("NEW If end..");
        }



        public function connectClient(Request $request)
        {
            $conferenceId = $request->input('CallSid');
            $agent_id = $request->input('WorkerSid');
            $twilioNumber = config('services.twilio')['number'];
            $client = $this->twilio_client ;
            $this->createCall($agent_id, $conferenceId, $client, $request);

            $activeCall = ActiveCall::firstOrNew(['agent_id' => $agent_id]);
            $activeCall->conference_id = $conferenceId;
            $activeCall->save();

            return $this->generateConferenceTwiml($conferenceId, false, true, '/conference/wait');
        }

        public function connectAgent1($conferenceId)
        {
            return $this->generateConferenceTwiml($conferenceId, true, false);
        }

        public function agentToken(Request $request,$userid = null){
            $accountSid = $this->sid;
            $authToken = $this->token;
            $workspaceDetails = $this->getWorkspaceDetails();

            $workspaceSid = $this->workspaceSid;

            if (!empty($workspaceDetails)) {
                $workspaceSid = $workspaceDetails->workspace_id;
            }
            
            $twilio_id = "";
            if( $userid == null) {
              Log::info("Agent Token -> User Id: " . $userid);
                $uid = Auth::user()->id;
            }else{
                $uid = $userid;
              Log::info("Agent Token -> User Id: " . $uid);

            }
            $save_twilio_ids = UserTwilioId::select('user_twilio_id.*', 'client_twilio_workflowids.workspace_id')->join('client_twilio_workflowids', 'client_twilio_workflowids.workflow_id', '=', 'user_twilio_id.workflow_id')->where('user_id', $uid )->where('user_twilio_id.deleted_at', NULL)->groupBy('user_twilio_id.twilio_id','user_twilio_id.workspace_id')->get();
	        
            $workerdata = array();
            $added_twilio_ids_to_single_user = array();

            $ttl = 36000;

             if( count($save_twilio_ids) > 0 ){
              Log::info("Agent Token -> In Save twilio ids ");

                 foreach($save_twilio_ids as $agent_twilio_account){
                    $workerSid = $agent_twilio_account->twilio_id;
                    $added_twilio_ids_to_single_user[] = $workerSid ;



                     $workspaceSid = $agent_twilio_account->workspace_id;
                    // $workerSid = Auth::user()->twilio_id;


                    $wsCapability = new WorkspaceCapability($accountSid, $authToken, $workspaceSid);
                    $wsCapability->allowFetchSubresources();
                    $wsCapability->allowUpdatesSubresources();
                    $wsCapability->allowDeleteSubresources();
                    $wsToken = $wsCapability->generateToken($ttl);


                    $workerCapability = new WorkerCapability(
                    $accountSid, $authToken, $workspaceSid, $workerSid);
                    $workerCapability->allowActivityUpdates();
                    $workerCapability->allowReservationUpdates();
                    $workerToken = $workerCapability->generateToken($ttl);



                    $workerdata[] = array(
                        'workerSid' => $workerSid,
                        'workspace_id' => $workspaceSid,
                        'workerToken' => $workerToken,
                        'workspaceToken' => $wsToken
                        // 'token' => $token
                    );
                 }

             } else {
              Log::info("Agent Token -> doesn't have saved twilio ids");

             }

             $capability = new ClientToken($accountSid, $authToken);
             Log::info("Agent Token -> Client token: " . print_r($capability, true));

             if (Auth::check()) {
                $device_unique_number = "tpv".Auth::id();
             } else {
                $device_unique_number = "tpv".time().rand();
             }

             info("Device unique number: ".$device_unique_number);
              (new TwilioConnectedDevice )->connect($uid,$device_unique_number,json_encode($added_twilio_ids_to_single_user));



             session(['client_token_number' => $device_unique_number]);
              session(['added_twilio_ids_to_single_user' => $added_twilio_ids_to_single_user]);


             $capability->allowClientIncoming($device_unique_number);
             $token = $capability->generateToken($ttl);




            // $response = array(
            //     'workerSid' => $worker_ids,
            //     'workerToken' => $worker_tokens,
            //     'token' => $tokens
            // );
            $response = array(
                'workersdata' => $workerdata,
                'token' => $token,
                'device_unique_number' => $device_unique_number
            );

            Log::info("Agent Token -> response: " . print_r($response, true));

            if($userid == null){
                return response($response, 200)
           ->header('Content-Type', 'application/json');
            }else{
                return $response;
            }




        }





        private function createCall($agentId, $conferenceId, $client, $request)
        {
            $destinationNumber = 'client:' . $agentId;
            $twilioNumber = config('services.twilio')['number'];
            $path = str_replace($request->path(), '', $request->url()) . 'conference/connect/' . $conferenceId . '/' . $agentId;
            try {
                $client->calls->create(
                    'client:' . $agentId, // The agent_id that will receive the call
                    $twilioNumber, // The number of the phone initiating the call
                    [
                        'url' => $path // The URL Twilio will request when the call is answered
                    ]
                );
            } catch (Exception $e) {
                return 'Error: ' . $e->getMessage();
            }
            return 'ok';
        }

        private function generateConferenceTwiml($conferenceId, $startOnEnter, $endOnExit, $waitUrl = null)
        {
            if ($waitUrl === null){
                $waitUrl = 'http://twimlets.com/holdmusic?Bucket=com.twilio.music.classical';
            }
            $response = new Twiml();
            $dial = $response->dial();
            $dial->conference(
                $conferenceId,
                ['startConferenceOnEnter' => $startOnEnter,
                'endConferenceOnExit' => $endOnExit,
                'waitUrl' => $waitUrl]
            );
            return response($response)->header('Content-Type', 'application/xml');
        }

        private function generateWaitTwiml(){
            $response = new Twiml();
            $response->say(
                'Thank you for calling. Please wait in line for a few seconds. An agent will be with you shortly.',
                ['voice' => 'alice', 'language' => 'en']
            );
            $response->play('http://com.twilio.music.classical.s3.amazonaws.com/BusyStrings.mp3');
            return response($response)->header('Content-Type', 'application/xml');
        }


        public function GetCallDuration(){

            $recordings_to_download = (new Telesales)::select('call_id','twilio_recording_url','recording_id')
            ->whereNotNull('twilio_recording_url')->whereNotNull('call_id')
            ->where('call_duration', '=', '')
            ->orWhereNull('call_duration')
            ->get();
               //print_r($recordings_to_download);
               echo "started \n";
              if(count($recordings_to_download)> 0){
                  foreach($recordings_to_download as $recordings_data){


                          $recording =  $this->twilio_client->recordings($recordings_data['recording_id'])
                                              ->fetch();
                                              if($recording) {
                                                $seconds = $recording->duration;
                                                // $hours = floor($seconds / 3600);
                                                // if( $hours < 10){
                                                //     $hours = "0".$hours;
                                                // }
                                                // $minutes = floor(($seconds / 60) % 60);
                                                // if( $minutes < 10){
                                                //     $minutes = "0".$minutes;
                                                // }
                                                // $seconds = $seconds % 60;
                                                // if( $seconds < 10){
                                                //     $seconds = "0".$seconds;
                                                // }
                                                // $duration = $hours.":".$minutes.":".$seconds;
                                                (new Telesales)->where('call_id', $recordings_data['call_id'])
                                                ->update([
                                                         'call_duration' => $seconds,
                                                         ]);
                                              }



                  }
              }
              echo "end";

        }
        public function sendmessage($tonumber,$message){

            $user = Auth::user();
            $phones = $this->ClientTwilioNumbers->getNumber($user->client_id);
            $tpvNumber = '';
            if(!empty($phones) && $phones != null) {
                $tpvNumber = $phones->phonenumber;
            }

            try{
                $createmessage =  $this->twilio_client->messages->create(
                    $tonumber, // Text this number
                    array(
                      'from' => $tpvNumber, // From a valid Twilio number
                      'body' => $message
                    )
                  );

                 // print_r($createmessage);
                  return true;
            }catch(TwilioException $e){
                \Log::error("Error while sending message: " . $e->getMessage());
                return false;
            }



        }

        public function MakeUserOffline(Request $request,$userid = ""){
           Log::info("In make user offline ");
           $tokens =  $this->agentToken($request,$userid);
           Log::info("In make user offline -> tokens: " . print_r($tokens, true));
           if(  count($tokens) > 0 ){
              if( count($tokens['workersdata']) > 0) {
                foreach ($tokens['workersdata'] as $workersdata) {
                    Log::info("In make user offline -> worker: " . print_r($workersdata, true));

                    $workspace_id = $workersdata['workspace_id'];
                    $activities = $this->twilio_client->taskrouter->v1->workspaces($workspace_id)->activities->read(array('friendlyName' => 'Offline'), 20);

                    Log::info("In make user offline -> activities: " . print_r($activities, true));

                                   //  dd($activities);
                                     $offline_activity = "";
                       if(count($activities) > 0 ){
                            foreach ($activities as $activity) {
                                //print($activity->sid);
                                if( $activity->friendlyName == 'Offline'){
                                    $offline_activity = $activity->sid;
                                }

                            }
                       }
                       if($offline_activity != ''){
                         $worker = $this->twilio_client->taskrouter->v1->workspaces($workspace_id)
                                 ->workers($workersdata['workerSid'])
                                 ->update(array(
                                              "activitySid" => $offline_activity
                                          )
                                 );
                       }





                }
              }
           }
            Log::info("In make user offline end !!");

           return true;

        }

    public function generateVoiceOTPMessage(Request $request,$id) {

          $verificationDetails = Phonenumberverification::find($id);
        //   $otpCode = $verificationDetails->otp;
        Log::info($request->all());
        Log::info("OTP");
        $digits = ($request->has('Digits')) ? $request->get('Digits'): '';
        return $this->twilioService->generateTwiMLForVoiceCall($verificationDetails->otp,$id,$digits);
    }

    }
