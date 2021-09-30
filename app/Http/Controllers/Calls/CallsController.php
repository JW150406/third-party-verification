<?php

namespace App\Http\Controllers\Calls;

use App\models\Telesales;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Twilio\Rest\Client;
use Twilio\Jwt\TaskRouter\WorkerCapability;
use Twilio\Jwt\TaskRouter\WorkspaceCapability;
use Twilio\Twiml;
use Twilio\Jwt\ClientToken;
use Yajra\DataTables\DataTables;
use App\User;
use DB;
use App\Services\StorageService;
use App\models\Brandcontacts;
use App\models\TwilioLeadCallDetails;


class CallsController extends Controller
{
    public $accountSid = 'AC0daf7f03ffa562e4b3b6da0931d9a60f';
    public $authToken  = 'd733c8e8bcd4e5d3e904e286d6dc381d';

    public $workspaceSid = 'WS773b86b9fe21ec7b213eb54af1019f6e';
    public $workflowSid = 'WWd18dc5feed687a8cd139e60c466e167a';
    private $twilio_client = array();

    function __construct(){
        $this->twilio_client = new Client($this->accountSid, $this->authToken);
        $this->storageService = new StorageService;
    }

     public function assignment(Request $request){
         /*'instruction' => 'accept',*/
        // $assignment_instruction = [
        //       'instruction' => 'call',
        //       'to' => "client:demoagent2@tpvdemosip1.sip.twilio.com",
        //       'from' => config('services.twilio')['number'],
        //       'url' => "https://tpv.matrixmarketers.com/agent?WorkerSid=WK40a6bc318192b64d9a08dd76d4f0c230"
        //       //'post_work_activity_sid' =>'WAb92e86167e6695af9584aac1c5f4d95a',

        //   ];

        $assignment_instruction = [
            'instruction' => 'redirect',
                 'call_sid' =>  $request->input('CallSid'),
                 'url' => url('/')."conference/assignment_redirect?WorkerSid=".$request->input('WorkerSid')
            //'post_work_activity_sid' =>'WAb92e86167e6695af9584aac1c5f4d95a',

        ];


        return response($assignment_instruction, 200)
        ->header('Content-Type', 'application/json');
     }

     public function createtask(){

        $twilio_client =  $this->twilio_client;
        // create a new task
        $task = $twilio_client->taskrouter
            ->workspaces($this->workspaceSid)
            ->tasks
            ->create(array(
              'attributes' => '{"selected_language": "en"}',
              'workflowSid' => $this->workflowSid,
            ));


        // display a confirmation message on the screen
        echo "Created a new task";

     }
     public function acceptreservation(){
        $reservationSid = 'WR091a868b55c16247c27c4a6aa06af6c1';
        $taskSid = 'WT8e1c3a9fec6d985bb0f648e2a4c38132';

        $twilio_client = $this->twilio_client;

        // update the reservation
        $accepted =  $twilio_client->taskrouter
                    ->workspaces($this->workspaceSid)
                    ->tasks($taskSid)
                    ->reservations($reservationSid)
                    ->update(['reservationStatus' => 'accepted']);

       echo "<pre>"; print_r($accepted); echo "</pre>";

     }


     public function incomingcall(){
        return response('<Response>
        <Gather action="enqueue-call" numDigits="1" timeout="5">
          <Say  language="es">Para Español oprime el uno.</Say>
          <Say language="en">For English, please hold or press two.</Say>
        </Gather>
      </Response>', 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
     }

     public function enqueuecall(Request $request){

        $digit_pressed = $request->Digits;

        if ($digit_pressed == '1') {
        $language = "es";
        } else {
        $language = "en";
        }


        return response('<Response>
        <Enqueue workflowSid="'.$this->workflowSid.'">
        <Task>{"selected_language": "'.$language.'"}</Task>
        </Enqueue>
        </Response>', 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
     }

    public function agent(Request $request){
        \Log::info('when call arrived');
        \Log::info($request->all());
        $accountSid = $this->accountSid;
        $authToken = $this->authToken;
        $workspaceSid = $this->workspaceSid;
        $workerSid = $request->WorkerSid;

        $wsCapability = new WorkspaceCapability($accountSid, $authToken, $workspaceSid);
        $wsCapability->allowFetchSubresources();
        $wsCapability->allowUpdatesSubresources();
        $wsCapability->allowDeleteSubresources();
        $wsToken = $wsCapability->generateToken();

        $workerCapability = new WorkerCapability(
            $accountSid, $authToken, $workspaceSid, $workerSid);
        $workerCapability->allowActivityUpdates();
        $workerCapability->allowReservationUpdates();


        $workerToken = $workerCapability->generateToken();

        $capability = new ClientToken($accountSid, $authToken);
        $capability->allowClientIncoming($workerSid);
        $token = $capability->generateToken();

        return view('frontend.tpvagent.agent',['workerSid' => $workerSid, 'workerToken' => $workerToken,'token' => $token, 'workspaceToken' => $wsToken]);



    }
    public function newCall(Request $request)
    {
        $response = new Twiml();
        $callerIdNumber = config('services.twilio')['number'];

        $dial = $response->dial(['callerId' => $callerIdNumber]);

        $phoneNumberToDial = $request->input('phoneNumber');

        if (isset($phoneNumberToDial)) {
            $dial->number($phoneNumberToDial);
        } else {
            $dial->client('support_agent');
        }

        return $response;
    }

    public function getTpvRecording() {
        /* check user access level client or below to client */
        if(Auth::user()->isAccessLevelToClient()) {
            $clientId =  auth()->user()->client_id;
        } else {
            $clientId = '';
        }
        $clients = (new \App\models\Client())->getClientsListByStatus('active');
        $brands = (new Brandcontacts)->getBrandsByClient($clientId);
        $tpvAgents =User::withTrashed()->where('access_level','tpvagent')->get();
        return view('admin.recording.index', ['clients' => $clients,'tpvAgents'=>$tpvAgents, 'results' => [],'brands'=> $brands]);
    }

    public function getTpvRecordingAjax(Request $request) {
        $timeZone = Auth::user()->timezone;

        //Added subquery for full name searching 
        $subquery = "CONCAT((CASE
        WHEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'first_name' and telesale_id =telesales.id LIMIT 1)  != ''
        THEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'first_name' and telesale_id =telesales.id  LIMIT 1 )
        ELSE ''
        END ),' ',(CASE
        WHEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'middle_initial' and telesale_id =telesales.id  LIMIT 1)  != ''
        THEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'middle_initial' and telesale_id =telesales.id  LIMIT 1)
        ELSE ''
        END),' ',(CASE
        WHEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'last_name' and telesale_id =telesales.id  LIMIT 1)  != ''
        THEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'last_name' and telesale_id =telesales.id  LIMIT 1)
        ELSE ''
        END))
        as 'AuthorizedName'";
        $NameSubQuery = "( select ".$subquery." )";
        $recordings = Telesales::leftJoin('users as tpv_agent', 'tpv_agent.id', '=', 'telesales.reviewed_by')
            ->leftJoin('users as sales_agent', 'sales_agent.id', '=', 'telesales.user_id')
            ->leftJoin('salesagent_detail as sd', 'sd.user_id', '=', 'telesales.user_id')
            ->leftJoin('clients', 'clients.id', '=', 'telesales.client_id')
            ->leftjoin('telesalesdata','telesalesdata.telesale_id','=','telesales.id')
            ->whereNotNull('telesales.call_id')
            ->select(['telesales.id','telesales.created_at','telesales.refrence_id', 'telesales.status',
                DB::raw('CONCAT(tpv_agent.first_name ," ",tpv_agent.last_name) as tpv_agent_name'),
                DB::raw('CONCAT(sales_agent.first_name ," ",sales_agent.last_name) as sales_agent_name'),
                DB::raw($subquery),
                DB::raw("(select GROUP_CONCAT(meta_value SEPARATOR ', ') from telesalesdata left join form_fields on form_fields.id = telesalesdata.field_id  where telesalesdata.field_id and  LOWER(form_fields.label) LIKE 'account number%' and form_fields.form_id = telesales.form_id and telesalesdata.meta_key = 'value' and telesalesdata.telesale_id = telesales.id LIMIT 1) as AccountNumber"),
                    DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'Phone Number' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and telesale_id =telesales.id LIMIT 1) as Phone"),
                'clients.name as clients_name', 'telesales.recording_downloaded', 'telesales.s3_recording_url']);

            /* check user access level client or below to client */
            if(Auth::user()->isAccessLevelToClient()) {
                $clientId =  auth()->user()->client_id;
            } else {
                $clientId = $request->client_id;
            }
            if(!empty($clientId)) {
                $recordings->where('clients.id',$clientId);
            }
            if (!empty($request->brand)) {
                $recordings->whereHas('programs.utility.brandContacts', function ($query) use ($request) {
                    $query->where('id',$request->brand);
                });
            }
            /* check user access level */
            if(Auth::user()->hasAccessLevels('salescenter')) {
                $salesCenter = Auth::user()->salescenter_id;
                $recordings->where('sales_agent.salescenter_id',$salesCenter);
            }
            /* check user has multiple locations */
            if (auth()->user()->hasMultiLocations()) {
                $locationIds = auth()->user()->locations->pluck('id');
                $recordings->whereIn('sd.location_id', $locationIds);
            }
            /* check location level restriction */
            if(Auth::user()->isLocationRestriction()) {
                $locationId = Auth::user()->location_id;
                $recordings->where('sd.location_id',$locationId);
            }

            if(!empty($request->tpv_agent_id)){
                $recordings->where('tpv_agent.id',$request->tpv_agent_id);
            }
            if(!empty($request->date)){
                $date = $request->date;
                $start_date = Carbon::parse(explode(' - ', $date)[0],$timeZone)->setTimezone('UTC');
                $end_date = Carbon::parse(explode(' - ', $date)[1],$timeZone)->setTimezone('UTC')->addDays(1);
                $recordings->whereBetween('telesales.created_at',[$start_date,$end_date]);
            }
            $recordings->groupBy('telesales.id');
        return DataTables::of($recordings)
            ->addColumn('date', function ($lead)use($timeZone) {
                return Carbon::parse($lead->created_at)->setTimezone($timeZone)->format(getDateFormat());
            })
            ->filterColumn('AuthorizedName',function($lead,$keyword) use($NameSubQuery){  
                return  $lead->whereRaw($NameSubQuery .' LIKE "%'.$keyword.'%"');
            })
            ->addColumn('brand', function($lead){
                $name = '';
                if(!empty($lead->programs) && !empty($lead->programs[0]->utility) && !empty($lead->programs[0]->utility->brandContacts)) {
                    $name= $lead->programs[0]->utility->brandContacts->name;
                }
                return $name;
            })
            ->addColumn('time', function ($lead)use($timeZone) {
                return Carbon::parse($lead->created_at)->setTimezone($timeZone)->format(getTimeFormat());
            })
            ->editColumn('recording_downloaded', function ($recording){
                return $recording->recording_downloaded == 0 ? 'Pending' : 'Completed';
            })
            ->addColumn('action', function($recording){
                $viewBtn = '';
                if (!empty($recording->s3_recording_url)){
                    $viewBtn = '<a href="'. Storage::disk('s3')->url($recording->s3_recording_url) .'" target="_blank" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Play Recording" class="btn purple">'.getimage("images/play.png").'</a>';
                }
                return '<div class="btn-group">'.$viewBtn.'<div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function downloadRecording($id) {
        $recording = Telesales::whereNotNull('call_id')->whereNotNull('twilio_recording_url')->find($id);

        if (!$recording) {
            return false;
        }

        Log::info('Recording Download start...');
        $url = $this->downloadFile($recording->twilio_recording_url, $recording->client_id);
        Log::info('Recording Download end...');
        Log::info('Results -> ');
        Log::info($url);

        if ($url == false) {
            return false;
        }
        
        $recording->s3_recording_url = $url;
        $recording->recording_downloaded = 1;
        $recording->save();

        // Update in the Twilio Lead Call Details Table
        // $callDetails = TwilioLeadCallDetails::where('lead_id',$id)->orderBy('id','desc')->first();
        // if(!empty($callDetails)){
        //   $callDetails->recording_url = $url;
        //   $callDetails->save();
        // }
        return true;
    }

    //This function calls when call cuts without verified lead id this information needed in billing report.
    public function downloadRecordingWithoutLead($twilioUrl,$clientId){
        Log::info('without lead id Recording Download start...');
        $url = $this->downloadFile($twilioUrl,$clientId);
        Log::info('without lead id Recording Download end...');
        Log::info('Results -> ');
        Log::info($url);

        if ($url == false) {
            return false;
        }
        return $url;
    }

    protected function downloadFile($url, $clientId) {
//        $file_name = str_random(32).'.WAV';
//        $path = config('aws_folder').'/recording/'.$file_name;

        \Log::debug("downloadFile: " . $url);

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
//            Storage::disk('s3')->put($path, $result, 'public');
//            $url = Storage::disk('s3')->url($path);

            $path = $this->storageService->uploadFileToStorage($result, $awsFolderPath, $filePath, $fileName);
            Log::info('Start to save to storage -> end');
            return $path;
        }

        return false;

    }

}
