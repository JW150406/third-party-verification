<?php

namespace App\Http\Controllers\AgentPanel\TPVAgent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\TpvagentController;
use App\Http\Controllers\Admin\TelesalesVerificationController;
use App\Http\Controllers\TPVAgent\RecordingController;
use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use Twilio\Jwt\TaskRouter\WorkspaceCapability;
use Twilio\Jwt\TaskRouter\WorkerCapability;
use App\models\ClientTwilioNumbers;
use App\models\Telesales;
use Twilio\Jwt\ClientToken;
use Twilio\TwiML\VoiceResponse;
use App\models\FormField;
use App\models\CallAnswers;
use Illuminate\Support\Facades\Auth;
use App\models\Role;
use Illuminate\Support\Facades\DB;
use App\models\Telesalesdata;
use App\models\Ticket;
use Hash;
use App\models\UserTwilioId;
use App\models\ClientWorkspasce;
use App\models\ScriptQuestions;
use App\models\UserAssignedForms;
use App\models\FormScripts;
use App\Services\SegmentService;
use App\Traits\CriticalLogsTrait;
use App\Jobs\GenerateReceiptPdf;
use App\models\TwilioLeadCallDetails;
use Carbon\Carbon;
use PhpParser\Node\Expr\New_;

class TPVIVRController extends Controller
{
    use CriticalLogsTrait;

    public function __construct()
    {
        $this->segmentService = new SegmentService;
    }

    /**
     * This method used for save and update details of child lead
     */
    public function index(Request $request)
    {
        //This method is Redirects request to validate the lead id
        //Redirects to handleWrongInput() method
        // $telesales = Telesales::where('refrence_id',$request->leadId)->where('status','pending')->first();
       
        // if(isset($telesales) && !empty($telesales))
        // {
        //     $callAns = CallAnswers::where('lead_id',$telesales->id)->get();
        //     if($callAns->count() > 0){
                
        //         $deletedLeads = (new CallAnswers)->deleteAnswers($telesales->id);
        //         Log::info('Deleted lead data'.$deletedLeads);
        //     }
        // }
        $inputs = $request->all();
        \Log::debug("IVR Index function");
        \Log::debug(print_r($inputs, true));
        $voiceMessage = new VoiceResponse();
        $leadId = $request->leadId;
        $this->deleteLeadDetails($leadId);
        if (isset($inputs['CallSid'])) {
            \Log::info('Call id found');
            $leadDetails = Telesales::where('refrence_id',$leadId)
            ->where('multiple_parent_id',0)
            ->whereIn('status',[config('constants.LEAD_TYPE_PENDING'),config('constants.LEAD_TYPE_DISCONNECTED')])->first();
            if(!empty($leadDetails)){
                // if(in_array($leadDetails->status,['pending','hangup'])){
                    \Log::info('Lead in pending or hangup state');
                    $fetchClient = ClientTwilioNumbers::where('phonenumber',$inputs['To'])->select('client_id','type')->first();
                    if(!empty($fetchClient)){
                        $clientId = $fetchClient->client_id;
                    }
                    $callDetailsTwilio = new  TwilioLeadCallDetails();
                    $callDetailsTwilio->call_id = $inputs['CallSid'];
                    $callDetailsTwilio->client_id = $clientId;
                    $callDetailsTwilio->call_type = config()->get('constants.VERIFICATION_METHOD_FOR_REPORT.IVR Inbound');;
                    $callDetailsTwilio->lead_id = $leadDetails->id;
                    $callDetailsTwilio->previous_status = $leadDetails->status;
                    $callDetailsTwilio->save();
                // }
                $updateLead = Telesales::where('id', $leadId)->update([
                    'call_id' => $inputs['CallSid'],
                    'language' => $inputs['language']
                ]);
                //Check whether this lead has child leads or not
                $isChildExist = (new Telesales())->getChildLeads($leadDetails->id);
                if(isset($isChildExist) && $isChildExist->count() > 0){
                    $data['call_id'] = $inputs['CallSid'];
                    $data['language'] = $inputs['language'];
                    foreach($isChildExist as $key => $val){
                        (new Telesales())->updateChildLeads($val->id,$data);
                        \Log::info('Child lead details are successfully updated for child lead '.$val->id);
                    }
                }
            }
        }
        $route = route('twilio.tpv-ivr-handle-wrong-input',['leadId'=>$leadId,'language'=>$request->language,'position'=> 0,'lastPos'=>0,'wCount'=>0,'emptyCount'=>-1]);
        $voiceMessage->redirect($route,['method'=>'GET']);
        return $voiceMessage;
    }

    /**
     * This method is used for give input from number pad as per caller's suggesation and from the backed it will give voice messages
     */
    public function gatherInput(Request $request)
    {
        //This method check all the digits that user pressed if user press 1 then it will get script questions and if user press 2 then it will allowed to decline tha sale and if user press any digit istead of 1 or 2 then he will get message according to that.
        
        // Log::info($request->all());
        $leadDetail = Telesales::where('refrence_id',$request->leadId)->first();
        $getChild = 0;
        //current number of question
        $position = $request->position; 
        $currentQuestionLocal = $request->currentQues; 
        $pos = $request->position; 
        $childPositions = [];
        $lastpos = $request->lastPos; 
        $voiceMessage = new VoiceResponse();

        if($request->isChild == 1){        
            $getChilds = $leadDetail->childLeads()->get();
            if($request->leadChildCount < $getChilds->count()){
                $getChild = $getChilds[$request->leadChildCount]->refrence_id;
            }
        }
        //If user press continue or yes then this condition becomes true and in this questions are asked to user and lastly verify the sale
        //If user press 1 then and then count of quesiton will be incremented and next question is asked to user.
        if($request->Digits == 1)
        {
            $questions = '';
            //if this lead has child leads then isChild is become 1 to retrive child leads questions
            if($request->isChild == 1 && $request->leadChildCount < $getChilds->count()){
                Log::info('isChild is 1');
                Log::info('child count is '.$request->leadChildCount);
                $childPositions = $this->getChildLeadQuestionDetails($request->leadId,$request->language);
                if($childPositions[0] != -1){
    
                    if($request->childPos == -1){   
                        $request->childPos = 0;
                      
                        $request->totalQues = $request->totalQues + count($childPositions) - $getChilds->count();
                        
                        Log::info('Total Questions ' .$request->totalQues);
                    }
                    else{
                        $request->childPos++;
                    }
                    
                    if(isset($childPositions[$request->childPos]) && $childPositions[$request->childPos] != -1){
                        $request->position = $childPositions[$request->childPos];
                    }
                    if(isset($childPositions[$request->childPos]) && $childPositions[$request->childPos] == -1){
                        $request->leadChildCount++;
                        $request->childPos++;
                        if(isset($childPositions[$request->childPos] )){
                            $request->position = $childPositions[$request->childPos];
                        }
                        Log::info('Child Count ' . $request->leadChildCount);
                    }
                    if($request->leadChildCount < $getChilds->count()){
                        $getChild = $getChilds[$request->leadChildCount]->refrence_id;
                    }
                }   
            }
            
            while(true)
            {
                $questions = $this->getScriptQuestions($request->leadId,$request->language,$request->position,$request->lastPos,'ivr_tpv_verification',$request->isChild,$request->currentQues,$request->totalQues,$getChild);
                    if($request->isChild == 0){
                        $request->totalQues = $questions['count'];
                    }
                $request->isChild = $questions['isChild'];
                if($questions['tagMatch'] === true)
                    break;
                else
                {
                    // $request->lastPos = $request->position;
                    $request->position = $request->position+1;
                    // $request->currentQues = $request->currentQues+1;
                    $currentQuestionLocal++;
                    if($request->isChild == 1){
                        if(isset($childPositions[$request->childPos]) && $childPositions[$request->childPos] != -1){
                            $request->childPos++;
                            $request->position = $childPositions[$request->childPos];
                        }
                    }
                }   
            }
            Log::info('Current Question ' . $request->currentQues);
            if($position != 0)
            {
                // Save User's response into database
                $questionsPrev =  $this->getScriptQuestions($request->leadId,$request->language,$request->lastPos,$request->lastPos,'ivr_tpv_verification',$request->isChild,$request->lastCurrQues,$request->totalQues,$getChild);
                $this->saveCustAnswer($questionsPrev,$request->language,$questionsPrev['positive']);
            }
            if($questions['status'] == 'success')
            { 
                if($questions['currentQuestion'] >= $questions['count'] && $position != 0)
                {
                    $this->getScriptQuestions($request->leadId,$request->language,$request->lastPos,$request->lastPos,'ivr_tpv_verification',$request->isChild,$request->currentQues,$request->totalQues,$getChild);
                    //Run verified script
                    $route = route('twilio.tpv-ivr-verify-lead',['leadId'=>$request->leadId,'language'=>$request->language,'position'=>$request->position,'lastPos'=>$request->lastPos,'wCount'=>0,'emptyCount'=>'verify']);
                    $gather = $voiceMessage->gather(['timeout' => 5,'action' => $route,'numDigits' => 1,'method' => 'GET']);
                    $gather->say('Press one to Confirm Verification.');   
                    $voiceMessage->redirect($route,['method'=>'GET']);
                }
                else{
                    $questions = $this->getScriptQuestions($request->leadId,$request->language,$request->position,$request->lastPos,'ivr_tpv_verification',$request->isChild,$request->currentQues,$request->totalQues,$getChild);
                }
                Log::info('questions'.$questions['ques']);
                $voiceMessage->say($questions['ques']);
                // the position of current quesition  will be postion of last question
                $request->lastPos = $request->position;
                $request->lastCurrQues = $currentQuestionLocal;
                if( $request->isChild == 0){
                    //The position of current question will be incremented.
                    $request->position++;
                    // $request->currentQues = $request->currentQues+1;
                }
                // $request->currentQues = $request->currentQues+1;
                $currentQuestionLocal++;

                $route = route('twilio.tpv-ivr-gather',['leadId'=>$request->leadId,'language'=>$request->language,'position'=>$request->position,'lastPos'=>$request->lastPos,'wCount'=>0,'emptyCount'=>0,'isChild'=>$questions['isChild'],'childPos'=>$request->childPos,'totalQues'=>$request->totalQues,'currentQues'=>$currentQuestionLocal,'lastCurrQues'=>$request->lastCurrQues,'leadChildCount'=>$request->leadChildCount]);
                $gather = $voiceMessage->gather(['timeout' => 5,'action' => $route,'numDigits' => 1,'method' => 'POST']);
                $gather->say('Press 1 for '.$questions['positive'].', press 2 for '.$questions['negative'].', or press 3 to repeat the question.');   
                
                $voiceMessage->redirect($route,['method'=>'POST']);
            }
            //if there is any error regarding to form not found or script not found then this condition will become true.
            else
            {
                $voiceMessage->say($questions['ques']);
                return $voiceMessage;
            }
        }
        //If user want to cancel the verification process then this condition become true.
        elseif($request->Digits == 2)
        {
            //If user press cancel after lead vefication
            if($request->emptyCount == -1)
            {
                // $refrenceIdLength = $this->getLeadRefrenceIdLength($request->leadId);
                $this->deleteLeadDetails($request->leadId);
                $gather = $voiceMessage->gather(['timeout' => 15,'action' => route('twilio.tpv-ivr-handle-wrong-input',['leadId'=>$request->leadId,'language'=>$request->language,'position'=> 0,'lastPos'=>0,'wCount'=>++$request->wCount,'emptyCount'=>0]),
                'finishOnKey' => '#','method' => 'GET']);
                $gather->say('Please Enter Your Lead Reference Number Followed by #.');
                return $voiceMessage;
            }
            //from this if user conform the decline then this redirected to declineSale() method for decline the sale.
            $route = route('twilio.tpv-ivr-decline',['leadId'=>$request->leadId,'language'=>$request->language,'position'=>$request->position,'lastPos'=>$request->lastPos,'wCount'=>0,'emptyCount'=>0,'isChild'=>$request->isChild,'childPos'=>$request->childPos,'totalQues'=>$request->totalQues,'currentQues'=>$currentQuestionLocal,'lastCurrQues'=>$request->lastCurrQues,'leadChildCount'=>$request->leadChildCount]);
            $gather = $voiceMessage->gather(['timeout' => 5,'action' =>$route,'numDigits' => 1,'method' => 'GET']);
            $gather->say('Press one to confirm Decline or two to Cancel.');
            $voiceMessage->redirect($route,['method'=>'GET']);
        }
        //If user want to repeat the question then this condition will become true.
        elseif($request->Digits == 3 && $request->position != 0)
        {
            // $request->currentQues = $request->currentQues;
            // $currentQuestionLocal = $lastQuestion;
            if($request->isChild == 1){
                $request->lastPos = $request->position;
            }
            if($request->isChild == 1){        
                $getChilds = $leadDetail->childLeads()->get();
                if($request->leadChildCount < $getChilds->count()){
                    $getChild = $getChilds[$request->leadChildCount]->refrence_id;
                }
            }

            Log::info('Last pos '.$request->lastCurrQues);
            $questions =  $this->getScriptQuestions($request->leadId,$request->language,$request->lastPos,$request->lastPos,'ivr_tpv_verification',$request->isChild,$request->lastCurrQues,$request->totalQues,$getChild);
            // Log::info($questions);
            if($questions['status'] == 'success')
            {
                Log::info('questions '.$questions['ques']);
                $voiceMessage->say($questions['ques']);
                $route = route('twilio.tpv-ivr-gather',['leadId'=>$request->leadId,'language'=>$request->language,'position'=>$request->position,'lastPos'=>$request->lastPos,'wCount'=>0,'emptyCount'=>0,'isChild'=>$questions['isChild'],'childPos'=>$request->childPos,'totalQues'=>$request->totalQues,'currentQues'=>$currentQuestionLocal,'lastCurrQues'=>$request->lastCurrQues,'leadChildCount'=>$request->leadChildCount]);
                $gather = $voiceMessage->gather(['timeout' => 5,'action' => $route,'numDigits' => 1,'method' => 'POST']);
                $gather->say('Press 1 for '.$questions['positive'].', press 2 for '.$questions['negative'].', or press 3 to repeat the question.');   
                $voiceMessage->redirect($route,['method'=>'POST']);
            }
            else
            {
                $voiceMessage->say($questions['ques']);
                return $voiceMessage;
            }
        }
        //If user donot press any digit then this condition will become true and redirected to this url.
        elseif($request->Digits == '')
        {
            //here if user do not press any digit then this statement will executed user will allowed to not press digit olny for 3 times after that call will automatically declined.
            if($request->emptyCount >=3)
            {
                $voiceMessage->say('No Input Detected Please try again. Good Bye.');
                return $voiceMessage;
            }
            $route = route('twilio.tpv-ivr-gather',['leadId'=>$request->leadId,'language'=>$request->language,'position'=> $request->position,'lastPos'=>$request->lastPos,'wCount'=>0,'emptyCount'=>++$request->emptyCount,'isChild'=>$request->isChild,'childPos'=>$request->childPos,'totalQues'=>$request->totalQues,'currentQues'=>$currentQuestionLocal,'lastCurrQues'=>$request->lastCurrQues,'leadChildCount'=>$request->leadChildCount]);
            $gather = $voiceMessage->gather(['timeout' => 5,'action' => $route,
            'numDigits' => 1,'method' => 'POST']);
            if($request->position == 0)
            {
                $gather->say('Sorry No Input Detected. Press One for continue or Two for cancel.');
            }
            else    
                $gather->say('Sorry No Input Detected. Press One for continue , press two for  cancel, or press three to repeat the question.');
            $voiceMessage->redirect($route,['method'=>'POST']);   
        }
        //If user presses Wrong input then this condition will become true.
        else
        {   
            if($request->wCount <=2)
            {
                $route = route('twilio.tpv-ivr-gather',['leadId'=>$request->leadId,'language'=>$request->language,'position'=> $request->position,'lastPos'=>$request->lastPos,'wCount'=>++$request->wCount,'emptyCount'=>0,'isChild'=>$request->isChild,'childPos'=>$request->childPos,'totalQues'=>$request->totalQues,'currentQues'=>$currentQuestionLocal,'lastCurrQues'=>$request->lastCurrQues,'leadChildCount'=>$request->leadChildCount]);
                $gather = $voiceMessage->gather(['timeout' => 5,'action' => $route,
                'numDigits' => 1,'method' => 'POST']);
                if($request->position == 0)
                {
                    $gather->say('Sorry You entered a wrong Input. Press 1 to continue or  Two to cancel.');   
                }
                $gather->say('Sorry You entered a wrong Input. Press 1 for continue, press two for cancel,or press three to Repeat the question');   
                $voiceMessage->redirect($route,['method'=>'POST']);
            }
            //if user reaches maximum count of wrong input then this condtion become true.
            else
            {
                $voiceMessage->say("You have reached maximum attempt of wrong input. Please  try again later. Good Bye!!!");
            }
        }
        $request->currentQues = $currentQuestionLocal;
        Log::info('----------------------------------');
        Log::info('Actuall CurrentQuestion '. $currentQuestionLocal);
        Log::info('Position  is '.$request->position);
        Log::info('Last is '.$request->lastPos);
        Log::info('current question is '.$request->currentQues);
        Log::info('Last Current question is '.$request->lastCurrQues);
        Log::info('total question is '.$request->totalQues);
        Log::info('----------------------------------');

        return $voiceMessage;
    }

    /**
     * For check zipcode is correct or not
     */
    public function askZipcode(Request $request)
    {
        //This method will ask user to enter zipcode.
        $voiceMessage = new VoiceResponse();
        Log::info('lead id '.$request->leadId);
        Log::info('Zipcode '.$request->Digits);
        if($request->Digits == 1)
        {
            //This code retrives the question of asking a zipcode from database and ask user to enter zipcode.
            // $zipcodeQuest =  $this->getScriptQuestions($request->leadId,$request->language,3,$request->lastPos,'customer_call_in_verification');
            // Log::info($zipcodeQuest['ques']);
            $route = route('twilio.tpv-ivr-zipcode',['leadId'=>$request->leadId,'language'=>$request->language,'position'=> $request->position,'lastPos'=>$request->lastPos,'wCount'=>0,'emptyCount'=>0,'isZip'=>1]);
            $gather = $voiceMessage->gather(['timeout' => 10,'action' => $route,'numDigits' => 5,'method' => 'GET']);
            $gather->say('Please Enter Your five digit Zipcode.');
            $voiceMessage->redirect($route,['method'=>'GET']);   
        }
        //If user press 2 means cancel then user have asked to enter 10 digit lead number nd this flow strats from beginning.
        elseif($request->Digits == 2)
        {
            // $refrenceIdLength = $this->getLeadRefrenceIdLength($request->leadId);
            $this->deleteLeadDetails($request->leadId);
            $gather = $voiceMessage->gather(['timeout' => 15,'action' => route('twilio.tpv-ivr-handle-wrong-input',['leadId'=>$request->leadId,'language'=>$request->language,'position'=> 0,'lastPos'=>0,'wCount'=>0,'emptyCount'=>0]),'finishOnKey' => '#','method' => 'GET']);
            $gather->say('Please Enter your  Lead Reference Number Followed by #.');
        }
       //if user enter zipcode then the flow goes here in this condtion users entered zipcode will be matched in database and if zipcode is valid then user will go ahed and if zipcode is not valid then user will asked to enter zipcode again.
        elseif($request->Digits != '')
        {
            //This code Validate the zipcode 
            Log::info('Validate Zipcode');

            $zipcode = DB::table('telesales')
                ->leftJoin('telesalesdata','telesales.id','=','telesalesdata.telesale_id')
                ->join('form_fields','form_fields.id','=','telesalesdata.field_id')
                ->select('meta_value as ServiceZipcode','telesales.client_id','telesales.form_id','telesales.id as telesale_id')
                ->where('meta_key','service_zipcode')
                // ->where('form_fields.type','service_and_billing_address')
                ->where('telesales.refrence_id',$request->leadId)
                ->where('form_fields.is_primary','1')
                ->having('meta_value',$request->Digits)
                ->where(function($q){
                    $q->where('form_fields.type','service_and_billing_address')
                    ->orWhere('form_fields.type','address');
                })->first();
                // ->orWhere('form_fields.type','address')
                
            // $zipcode = DB::table('telesales')
            //     ->select(DB::raw("concat((select meta_value from telesalesdata where  field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_zipcode' and telesale_id =telesales.id LIMIT 1), ' ') as ServiceZipcode"))                
            //     ->having('ServiceZipcode',$request->Digits)
            //     ->where('telesales.refrence_id',$request->leadId)
            //     ->first();
          //if zipcode is valid
            if(isset($zipcode->ServiceZipcode))
            {
                $questions['ques'] = 'Please Enter 5 digit zipcode';
                    $questions['client'] = $zipcode->client_id;
                    $questions['form'] = $zipcode->form_id;
                    $questions['telesaleLeadId'] = $zipcode->telesale_id;
                    $this->saveCustAnswer($questions,$request->language,$zipcode->ServiceZipcode,$zipcode->ServiceZipcode);

                Log::info('DB zipcode'.$zipcode->ServiceZipcode);
                Log::info('Digit zipcode'.$request->Digits);
                $route = route('twilio.tpv-ivr-gather',['leadId'=>$request->leadId,'language'=>$request->language,'position'=> $request->position,'lastPos'=>$request->lastPos,'wCount'=>0,'emptyCount'=>-1,'isChild'=>0,'childPos'=>-1,'totalQues'=>1,'currentQues'=>0,'lastCurrQues'=>0,'leadChildCount'=>0]);
                $gather = $voiceMessage->gather(['timeout' => 5,'action' => $route,'numDigits' => 1,'method' => 'POST']);
                $gather->say('Your Zipcode is valid. Press 1 to continue or Two to cancel.');
                $voiceMessage->redirect($route,['method'=>'POST']);
            }
            //if zipcode is not valid
            else
            {
                $route = route('twilio.tpv-ivr-zipcode',['leadId'=>$request->leadId,'language'=>$request->language,'position'=> $request->position,'lastPos'=>$request->lastPos,'wCount'=>0,'emptyCount'=>-1,'isZip'=>$request->isZip]);
                $gather = $voiceMessage->gather(['timeout' => 10,'action' => $route,'numDigits' => 5,'method' => 'GET']);
                $gather->say('Your Zipcode is Not Valid Enter  5 digit Zipcode Again.');
                $voiceMessage->redirect($route,['method'=>'GET']);   
            }
            
        }
         //If no input detected then this condition will be executed.
        elseif($request->Digits == '')
            {
                $route = route('twilio.tpv-ivr-zipcode',['leadId'=>$request->leadId,'language'=>$request->language,'position'=> $request->position,'lastPos'=>$request->lastPos,'wCount'=>0,'emptyCount'=>-1,'isZip'=>$request->isZip]);
                if($request->isZip == 1)
                {
                    $gather = $voiceMessage->gather(['timeout' => 10,'action' => $route,'numDigits' => 5,'method' => 'GET']);
                    $voiceMessage->say('Sorry No input detected Enter 5 digit Zipcode. ',['voice'=>'woman']);
                }
                else
                {
                    $gather = $voiceMessage->gather(['timeout' => 5,'action' => $route,'numDigits' => 1,'method' => 'GET']);
                    $voiceMessage->say('Sorry
                    No input detected Press 1 to continue or two to cancel. ',['voice'=>'woman']);
                }
                $voiceMessage->redirect($route,['method'=>'GET']);   
            }
        return $voiceMessage;
    }

    /**
     * This method is used for handle wrong inputs
     */
    public function handleWrongInput(Request $request)
    {
        //This method is used for if user enters wrong lead id or no digits are pressed for lead id.
        try{
            Log::info('Validating lead id');
            Log::info($request->all());
            Log::info($request->Digits."Digits");
            $voiceMessage = new VoiceResponse();
            $leadId = ($request->has('Digits')) ? $request->Digits : $request->leadId;
            Log::info($leadId."leadid");    
            //This condition will validate the lead id.
            if(isset($leadId) && !(empty($leadId)))
            {
                // $refrenceIdLength = $this->getLeadRefrenceIdLength($leadId);
                $telesales = Telesales::where('refrence_id',$leadId)
                ->where('multiple_parent_id',0)
                ->whereIn('status', [config('constants.LEAD_TYPE_PENDING'), config('constants.LEAD_TYPE_DISCONNECTED')])->first();
                // Log::info($telesales);
                //if lead id is valid then this condition will be executed.
                if(isset($telesales))
                {
                    $questions['ques'] = 'Please Enter lead reference number Followed by #';
                    $questions['client'] = $telesales->client_id;
                    $questions['form'] = $telesales->form_id;
                    $questions['telesaleLeadId'] = $telesales->id;
                    $this->saveCustAnswer($questions,$request->language,$telesales->refrence_id,$telesales->refrence_id);
                    //check for tpv attempt alert tele/d2d
                    (new TpvagentController)->checkAlertTeleD2d($telesales);
                    $route = route('twilio.tpv-ivr-zipcode',['leadId'=>$leadId,'language'=>$request->language,'position'=> $request->position,'lastPos'=>$request->lastPos,'wCount'=>0,'emptyCount'=>0,'isZip'=>0]);
                    $gather = $voiceMessage->gather(['timeout' => 5,'action' => $route,'numDigits' => 1,'method' => 'GET']);
                    $gather->say('Your lead Reference Number is valid. Press 1 to continue or Two to cancel.');
                    $voiceMessage->redirect($route,['method'=>'GET']);
                }
                // if user reaches max count of entring wrong lead id then this condition will be executed.
                elseif($request->wCount >= 3)
                {
                    $voiceMessage->say("You have reached maximum attempt of wrong input. Please  try again later. Good Bye!!!");
                    return $voiceMessage;
                }
                //if user donot press any digit then this condition will be executed.
                elseif($request->Digits == '' && $request->emptyCount != -1)
                {
                    if($request->emptyCount >= 3)
                    {
                        $voiceMessage->say("You have reached maximum attempt of No input. Please try again later. Good Bye!!!");
                        return $voiceMessage;   
                    }
                    Log::info("No Digits");
                  
                    $route = route('twilio.tpv-ivr-handle-wrong-input',['leadId'=>$leadId,'language'=>$request->language,'position'=> 0,'lastPos'=>0,'wCount'=>0,'emptyCount'=>++$request->emptyCount]);
                    $gather = $voiceMessage->gather(['timeout' => 15,'action' => $route,
                    'finishOnKey' => '#','method' => 'GET']);
                    $gather->say('No input Detected Please Enter your lead reference number Followed by #.');
                    $voiceMessage->redirect($route,['method'=>'GET']);
                }
                //If user enters wrong input then this condition will be executed.
                else
                {
                    $route = route('twilio.tpv-ivr-handle-wrong-input',['leadId'=>$leadId,'language'=>$request->language,'position'=> 0,'lastPos'=>0,'wCount'=>++$request->wCount,'emptyCount'=>0]);
                    $gather = $voiceMessage->gather(['timeout' => 15,'action' => $route,
                    'finishOnKey' => '#','method' => 'GET']);
                    $gather->say('Sorry You entered a wrong  Lead Reference Number Enter Lead Reference Number Again Followed by #.');
                    $voiceMessage->redirect($route,['method'=>'GET']);
                }                
            }
            return $voiceMessage;
        }catch(TwilioException $e)
        {
            Log::error($e->getMessage());
            return $voiceMessage->say('Something went Wrong.');
        }
    }

    /**
     * This method is used for decline the sale
     */
    public function declineSale(Request $request)
    {
        //This Method is used for decline the sale if user press 2 digit then this will executed.
        //Here if user press 1 then lead will be declined and if user presses 1 then user will asked to continue or cancel the verification.
        $voiceMessage = new VoiceResponse();
        //If user press 2 means cancel the decline then hw will asked to press 1 to continue 2 to cancel or 3 to repeat question.
        if($request->Digits == 2)
        {
            $route = route('twilio.tpv-ivr-gather',['leadId'=>$request->leadId,'language'=>$request->language,'position'=>$request->position,'lastPos'=>$request->lastPos,'wCount'=>0,'emptyCount'=>0,'isChild'=>$request->isChild,'childPos'=>$request->childPos,'totalQues'=>$request->totalQues,'currentQues'=>$request->currentQues,'lastCurrQues'=>$request->lastCurrQues,'leadChildCount'=>$request->leadChildCount]);
            $gather = $voiceMessage->gather(['timeout' => 5,'action' => $route,'numDigits' => 1,'method' => 'POST']);
            $gather->say('Press one for continue , press two for Cancel, or press three to Repeat the question');
            $voiceMessage->redirect($route,['method'=>'POST']);
        }
        //If user confirms decline then lead will be declined.
        elseif($request->Digits == 1)
        {
            $leaduniqueId = (new Telesales())->getLeadID($request->leadId);
            $questions =  $this->getScriptQuestions($request->leadId,$request->language,$request->lastPos,$request->lastPos,'ivr_tpv_verification');
            $this->saveCustAnswer($questions,$request->language,$questions['negative']);
            $this->updateLead($leaduniqueId->id, config()->get('constants.LEAD_TYPE_DECLINE'),$request->language);

            //add lead status in twilio calls details for billing report
            $this->updateLeadStatusTwilioCall($leaduniqueId->id,config()->get('constants.LEAD_TYPE_DECLINE'));
            Log::info('DeclineSale');
            //Run decline script
            $declinedScript = $this->getScriptQuestions($request->leadId,$request->language,0,0,'after_lead_decline');
            Log::info($declinedScript['ques']);
            $voiceMessage->say($declinedScript['ques'].'.');
        }
        //This code is for no input detected user will allowed 3 times to remain on call without enter any digit after that call will declined.
        elseif($request->Digits == '')
        {
            if($request->emptyCount >= 2)
            {
                $voiceMessage->say('No Input Detected Please try again. Good Bye.');
                return $voiceMessage;
            }
            $route = route('twilio.tpv-ivr-decline',['leadId'=>$request->leadId,'language'=>$request->language,'position'=>$request->lastPos,'lastPos'=>$request->lastPos,'wCount'=>0,'emptyCount'=>++$request->emptyCount,'isChild'=>$request->isChild,'childPos'=>$request->childPos,'totalQues'=>$request->totalQues,'currentQues'=>$request->currentQues,'lastCurrQues'=>$request->lastCurrQues,'leadChildCount'=>$request->leadChildCount]);
            $gather = $voiceMessage->gather(['timeout' => 5,'action' => $route,'numDigits' => 1,'method' => 'GET']);
            $gather->say('Sorry No Input Detected. Press One to confirm decline or Two to  cancel.'); 
            $voiceMessage->redirect($route,['method'=>'GET']);     
        }
        //If user press digits instead of 1 or 2 then this condition will be executed.
        else
        {
            $route = route('twilio.tpv-ivr-gather',['leadId'=>$request->leadId,'language'=>$request->language,'position'=> $request->lastPos,'lastPos'=>$request->lastPos,'wCount'=>++$request->wCount,'emptyCount'=>0,'isChild'=>$request->isChild,'childPos'=>$request->childPos,'totalQues'=>$request->totalQues,'currentQues'=>$request->currentQues,'lastCurrQues'=>$request->lastCurrQues,'leadChildCount'=>$request->leadChildCount]);
            $gather = $voiceMessage->gather(['timeout' => 5,'action' => $route,'numDigits' => 1,'method' => 'POST']);
            $gather->say('Sorry You entered a wrong Input. Press One to continue or Two to cancel.'); 
            $voiceMessage->redirect($route,['method'=>'POST']);  
        }
        return $voiceMessage;   
    }

    /**
     * This method is used for get script qyestions as given parameters
     * @param $leadId, $language, $position, $lastPos, $scriptFor, $isChild, $currentQues, $totalQues, $getChild
     */
    public function getScriptQuestions($leadId,$language,$position,$lastPos,$scriptFor,$isChild = 0,$currentQues = 0,$totalQues = 1,$getChild = 0)
    {
        
        //this method is for retriving script question based on various parameters like language  scripttype.
        $LeadIdLocal = $leadId;
        if($getChild != 0){
            $LeadIdLocal = $getChild;
        }
        Log::info('Child Lead '.$getChild);
        Log::info('Position in questions '.$position);
        Log::info('Last Position in questions '.$lastPos);
        $teleSale = Telesales::with('form')->where('refrence_id', $LeadIdLocal)->first();
        $removedTag = [];
        if (!empty($teleSale)) {
            if($teleSale->verification_number == ''){
                $teleSale->verification_number = generateVerificationNumer($teleSale);
                $teleSale->save();
            }

            if (array_get($teleSale, 'form')) {
                $zipcode = $teleSale->zipcodes()->first();
                
                if (!empty($zipcode)) {
                    $form = $teleSale->form;
                    $tags = (new TpvagentController)->getTagToReplaceForQuestions($teleSale->id, $teleSale->form_id);
                    $tags['[TPVAGENT]'] = "TPV360";    
                    $tags['[VERIFICATION CODE]'] = implode('. ', str_split($teleSale->verification_number));
                    if($scriptFor == 'ivr_tpv_verification'){
                        $formId =  array_get($form, 'id');
                        $state = $zipcode->state;
                    }
                    else{
                        $formId = 0;
                        $state = 'ALL';
                    }
                    $clientId = $teleSale->client_id;
                    $formScript = DB::table('form_scripts')
                  
                    ->where('form_scripts.language', '=', $language)
                    ->where('form_scripts.form_id', '=', $formId)
                    ->where('form_scripts.client_id', '=', $clientId)
                    ->when($scriptFor, function ($query) use ($scriptFor) {
                        return $query->where('form_scripts.scriptfor', '=', $scriptFor);
                    })
                    ->where(function ($que) use($state, $language, $formId, $scriptFor,$clientId) {
                        $que->where('state', '=', DB::raw("CASE WHEN(select count(id) from form_scripts where state = '".$state."' and language = '".$language."' and client_id = '".$clientId."' and form_id = '".$formId."') > 1 then '".$state."' else 'ALL' end"));
                    })->first();            
                    if (isset($formScript)) {
                        $leadIdLocal = $teleSale->id;
                        if($position == 0 && $scriptFor == 'ivr_tpv_verification')
                        {
                            $questions = ScriptQuestions::where('script_id',$formScript->id)->where('form_id', array_get($form, 'id'))->orderBy('position', 'ASC')->where('is_introductionary', 1)->get();
                        }
                        else
                        {
                            $questions = ScriptQuestions::with(['questionConditions' => function($qu) {
                                $qu->where('condition_type','question');
                            }])->where('script_id', $formScript->id)->where('form_id', $formId)->orderBy('position', 'ASC')->get();
                        }
                        if($totalQues == 1 && $isChild == 0){
                            $totalQues = $questions->count();
                        }
                        // Log::info($questions->count()." total question count");
                        // Log::info($questions->toArray());
                        if($currentQues >= $totalQues && $position != 0)
                        {
                            $ques = "Press one to Verify the sale";
                            $positive_ans = 'Continue';
                            $negative_ans = 'Cancel';
                            $qid = $questions[$lastPos]->id;
                        }
                        else
                        {
                            $single_question = $questions[$position]->question;
                            $positive_ans = $questions[$position]->positive_ans;
                            $negative_ans = $questions[$position]->negative_ans;
                            $qid = $questions[$position]->id;
                            $conditions = DB::table('script_questions_conditions')->where('question_id',$qid)->where('condition_type','tag')->get()->toArray();
                            $single_question = preg_replace_callback("/\[\s*([^\]]+)\s*\]/", function ($word) {
                                return "[".trim(strtoupper($word[1]))."]";
                            }, $single_question);
                            $ques = strtr($single_question, $tags);
                            $skipQue = false;
                            if(count($conditions) > 0){
                                $skipQue = (new TpvagentController)->checkScriptQuestionCondition($conditions,$single_question,$questions[$position],$tags);
                            }
                            // \Log::info("Skip quesion or not :".$skipQue);
                            $emptyFlag = false;
                            $checkEmptyTag = $ques;
                            while(strpos($checkEmptyTag,'<span') > 0)
                            {
                                $sub = substr($checkEmptyTag,strpos($checkEmptyTag , '<span'));
                                $checkEmptyTag = substr($checkEmptyTag,0,strpos($checkEmptyTag , '<span'));
                                $pattern = '/.*?<span.*?>(.*?)<\/span>/';
                                preg_match($pattern, $sub, $matches);
                                if($matches[1] == '')
                                {
                                    $emptyFlag = true;
                                }
                                else{
                                    $emptyFlag = false;
                                    break;
                                }
                                $end = strpos($sub,'</span>');
                                $remaining = substr($sub,$end+7);
                                $checkEmptyTag =  $remaining;
                            }
                            if($emptyFlag == true || $skipQue == true)
                            {
                                $data['tagMatch'] = false;
                            }
                            else{
                                $data['tagMatch'] = true;
                            }
                            $isChildExist = $teleSale->childLeads()->get();
                            if($isChildExist->count() > 0){
                                if($currentQues == $totalQues - 1 && $position != 0){
                                   Log::info('In last question ');
                                   $isChild = 1;
                                }
                            }
                            $ques = $this->removeSpan($ques);
                            
                            $data['status'] = 'success';
                            $data['ques'] = $ques;
                            $data['tags'] = $tags;
                            $data['positive'] = $positive_ans;
                            $data['negative'] = $negative_ans;
                            $data['client'] = $teleSale->client_id;
                            if($isChild == 1){
                                $data['telesaleLeadId'] = $teleSale->multiple_parent_id;
                            }
                            else{
                                $data['telesaleLeadId'] = $teleSale->id;
                            }
                            $data['form'] = $formScript->form_id;
                            $data['count'] = $totalQues;
                            $data['currentQuestion'] = $currentQues;
                            $data['id'] = $qid;
                            $data['code'] = $teleSale->verification_number;
                            $data['isChild'] = $isChild;
                            // $data['tagMatch'] = !$emptyFlag;
                            return $data;
                        }
                        // For removing span tag from quesiton.
                        $ques = $this->removeSpan($ques);
                        // Log::info($ques);
                        $data['status'] = 'success';
                        $data['ques'] = $ques;
                        $data['tags'] = $tags;
                        $data['positive'] = $positive_ans;
                        $data['negative'] = $negative_ans;
                        $data['client'] = $teleSale->client_id;
                        if($isChild == 1){
                            $data['telesaleLeadId'] = $teleSale->multiple_parent_id;
                        }
                        else{
                            $data['telesaleLeadId'] = $teleSale->id;
                        }
                        $data['form'] = $formScript->form_id;
                        $data['count'] = $totalQues;
                        $data['currentQuestion'] = $currentQues;
                        $data['id'] = $qid;
                        $data['tagMatch'] = true;
                        $data['code'] = $teleSale->verification_number;
                        $data['isChild'] = $isChild;
                        return $data;
                    }
                    else
                    {
                        Log::info('Sorry Script Not Found');
                        $data['status'] = 'error';
                        $data['ques'] = 'Sorry Something went wrong please try again later.';
                        $data['tagMatch'] = true;

                        return $data;
                    }
                }
                else
                {
                    Log::info('Sorry Script Not Found');
                    $data['status'] = 'error';
                    $data['ques'] = 'Sorry Something went wrong please try again later.';
                    $data['tagMatch'] = true;
                    return $data;
                }
            }
            else
            {
                Log::info('Sorry Form Not Found for this script');
                $data['status'] = 'error';
                $data['ques'] = 'Sorry Something went wrong please try again later.';
                $data['tagMatch'] = true;
                return $data;
            }
        } 
    }

    /**
     * For Update lead details after completion of verification
     */
    public function updateLead($leadId, $status,$language = null) {
        $lead = Telesales::find($leadId);
        \Log::info("Language: ".$language);
        if (empty($lead)) {
            Log::error("IVR TPV: Lead not found with id: " . $leadId);
            return false;
        }

        if (!empty($status)) {
            $oldStatus = array_get($lead, 'status');

            $storeLanguage = array_get($lead, 'language', "");
            if ($language != null || $language != "") {
                $storeLanguage = $language;
            }
            $updatedLead = $lead->update([
                            'status' => $status,
                            'verification_method' => config()->get('constants.IVR_INBOUND_VERIFICATION'),
                            'language' => $storeLanguage,
                            'reviewed_at' => date('Y-m-d H:i:s')
                        ]);
            //check whether child lead exist or not
            $isChildExist = (new Telesales())->getChildLeads($lead->id);
                if(isset($isChildExist) && $isChildExist->count() > 0){
                    $data['status'] = $status;
                    $data['verification_method'] = config()->get('constants.IVR_INBOUND_VERIFICATION');
                    $data['language'] = $storeLanguage;
                    $data['reviewed_at'] = $lead->reviewed_at;
                    $data['verification_number'] = $lead->verification_number;
                    
                    foreach($isChildExist as $key => $val){
                        (new Telesales())->updateChildLeads($val->id,$data);
                        \Log::info('Child lead details are successfully updated for child lead '.$val->id);
                    }
                }
            //Check if lead record is updated
            if ($updatedLead) {
                
                $newUpdatedLead = Telesales::find($lead->id);

                //Send lead updated track to segment
                $this->segmentService->createLeadStatusUpdatedTrack($lead, $oldStatus, array_get($newUpdatedLead, 'status'));

                if ($status == config()->get('constants.LEAD_TYPE_VERIFIED')) {
                    $eventType = "Event_Type_41";

                    // for check send contract pdf after lead verify
                    if ($newUpdatedLead->type == 'tele' && isOnSettings(array_get($newUpdatedLead, 'client_id'), 'is_enable_send_contract_after_lead_verify_tele',false) || $newUpdatedLead->type == 'd2d' && isOnSettings(array_get($newUpdatedLead, 'client_id'), 'is_enable_send_contract_after_lead_verify_d2d',false)) {
                        Log::info('Save Lead Contract PDF');
                        
                        //check whether this lead has child leads or not
                        // $isChildExist = $newUpdatedLead->childLeads()->get();
                        // //if there are child leads then generate child leads contract 
                        // if(isset($isChildExist) && $isChildExist->count() > 0){
                        //     foreach ($isChildExist as $key => $val) {
                        //         \App\Jobs\SendContractPDF::dispatch($val->id,'','','child');
                        //     }
                        // }
                        //send parent lead contract
                        \App\Jobs\SendContractPDF::dispatch($newUpdatedLead->id);
                    }
                } else if ($status == config()->get('constants.LEAD_TYPE_DECLINE')) {
                    $eventType = "Event_Type_42";
                } else {
                    $eventType = "Event_Type_40";
                }

                //Register IVR TPV call completion logs
                $this->registerIVRTPVCallCompletionlogs($newUpdatedLead, $eventType);

                //Check if lead is verified or decline then register self verification expire logs
                if (in_array($status, array(config()->get('constants.LEAD_TYPE_VERIFIED'), config()->get('constants.LEAD_TYPE_DECLINE')))) {
                    $this->registerLogsForSelfVerificationExpire($newUpdatedLead);
                }

                // for generate tpv receipt pdf
                $timezone = getClientSpecificTimeZone();
                GenerateReceiptPdf::dispatch($lead->id, $timezone);
                Log::info("Generated tpv receipt pdf for tpv ivr");
            }
            
            Log::info("Lead status updated to: " . $status . " for lead id: " . $leadId);
        } else {
            Log::error("No lead status available to update for lead: " . $leadId);
        }
        
        return true;
    }

    /**
     * This method is used for save customer's answers as per given details
     * @param $questions, $language, $verificationAns, $ans
     */
    public function saveCustAnswer($questions,$language,$verificationAns = null,$ans = null)
    {

        $subject = $questions['ques'];
        
        $data = array(
            'client_id' => $questions['client'],
            'form_id' => $questions['form'],
            'lead_id' => $questions['telesaleLeadId'],
            'tpv_agent_id' => null,
            'sales_agent_id' => null,
            'language' => $language,
            'question' => $subject,
            'answer' => $ans,
            'verification_answer' => $verificationAns,
        );
        // \Log::info($data);
        $dbDetails = (new CallAnswers)->InsertAnswer($data);
        $dbRecords = CallAnswers::find($dbDetails);
        Log::info('Answer Successfully saved');
        // Log::info($dbRecords);
    }

    public function deleteLeadDetails($leadId)
    {
        $telesales = Telesales::where('refrence_id',$leadId)->whereIn('status',[config('constants.LEAD_TYPE_PENDING'), config('constants.LEAD_TYPE_DISCONNECTED')])->first();
        
        if(isset($telesales) && !empty($telesales))
        {
            $callAns = CallAnswers::where('lead_id',$telesales->id)->get();
            if($callAns->count() > 0){
                
                $deletedLeads = (new CallAnswers)->deleteAnswers($telesales->id);
                Log::info('Deleted lead data'.$deletedLeads);
            }
        }
    }

    /**
     * For IVR TPV Assignment
     */
    public function ivrTpvAssignment(Request $request) {
        $inputs = $request->all();

        \Log::debug("inputs: " . print_r($inputs, true));
        
                
        if (isset($inputs['CallStatus']) && $inputs['CallStatus'] == config()->get('constants.CALL_COMPLETED_STATUS') && isset($inputs['CallSid'])) {
            $lead = Telesales::where('call_id', $inputs['CallSid'])->first();
            if (!empty($lead) && !in_array(array_get($lead, 'status'), [config('constants.LEAD_TYPE_VERIFIED'), config('constants.LEAD_TYPE_DECLINE'), config('constants.LEAD_TYPE_EXPIRED'), config('constants.LEAD_TYPE_CANCELED')])) {
                $leadUpdated = $this->updateLead($lead->id, config()->get('constants.LEAD_TYPE_DISCONNECTED'));
                //add lead status in twilio calls details for billing report
                $this->updateLeadStatusTwilioCall($lead->id,config()->get('constants.LEAD_TYPE_DISCONNECTED'));

                if ($leadUpdated) {
                    \Log::info("Lead updated as disconnected for lead with id: " . $lead->id);
                } else {
                    \Log::error("Unable to update lead status to disconnected for lead with id: " . $lead->id);
                }
            } else {
                \Log::error("Lead is not found other wise lead is not in verification state.");
            }
        }
    }

    /**
     * This method is used for verify lead details
     */
    public function verifyLead(Request $request)
    {
        \Log::info("lead Verified");
        \Log::info($request->all());
        \Log::info($request->Digits);
        $voiceMessage = new VoiceResponse();
        if($request->Digits == 1)
        {
            if($request->emptyCount == 'verify')
            {   
                $leaduniqueId = (new Telesales())->getLeadID($request->leadId);
                //Update verified lead details
                $this->updateLead($leaduniqueId->id, config()->get('constants.LEAD_TYPE_VERIFIED'));

                //add lead status in twilio calls details for billing report
                $this->updateLeadStatusTwilioCall($leaduniqueId->id,config()->get('constants.LEAD_TYPE_VERIFIED'));
                $vefiedScript = $this->getScriptQuestions($request->leadId,$request->language,0,0,'closing');
                \Log::info($vefiedScript['ques']);
                $voiceMessage->say($vefiedScript['ques'].'.');
                // $voiceMessage->say('Your verification code is '.$vefiedScript['code']);
                return $voiceMessage;
            }
        }
        else    
        {
            $route = route('twilio.tpv-ivr-verify-lead',['leadId'=>$request->leadId,'language'=>$request->language,'position'=>$request->position,'lastPos'=>$request->lastPos,'wCount'=>0,'emptyCount'=>'verify']);
            $gather = $voiceMessage->gather(['timeout' => 5,'action' => $route,'numDigits' => 1,'method' => 'GET']);
            $gather->say('Please enter valid digit. Press one to Verify the sale');   
            $voiceMessage->redirect($route,['method'=>'GET']);
            return $voiceMessage;   
        }
    }
    
    /**
     * This method is used for remove span tag from the questions\
     * @param $ques
     */
    public function removeSpan($ques)
    {
        $pattern = '/.*?<span.*?>(.*?)<\/span>/';
        preg_match($pattern, $ques, $matches);
        while(strpos($ques , '<span') !== false)
        {   
            $sub = substr($ques,strpos($ques , '<span'));
            $ques = substr($ques,0,strpos($ques , '<span'));
            
            $pattern = '/.*?<span.*?>(.*?)<\/span>/';
            preg_match($pattern, $sub, $matches);
                $string = $matches[0];
                $pattern = '/.*?<span.* ?>(.*?)<\/span>/';
                $replacement = $matches[1];
                
                preg_replace($pattern, $replacement, $string);
                
                $end = strpos($sub,'</span>');
                $remaining = substr($sub,$end+7);
                $sub = substr($sub,0,$end+7);
                $ques .= $matches[1]. $remaining;
        }
        return $ques;
    }

    /**
     * This method is used to store lead status for twilio call details
     * @param $leadId, $status
     */
    public function updateLeadStatusTwilioCall($leadId,$status)
    {
        \Log::info('Lead status updated in twilio call details: '.$status);
        \Log::info('Lead id in twilio call details: '.$leadId);
        $callDetailsTwilio = TwilioLeadCallDetails::where('lead_id',$leadId)->orderBy('id','desc')->first();
        if(isset($callDetailsTwilio) && !empty($callDetailsTwilio)){
            $callDetailsTwilio->lead_status = $status;
            $callDetailsTwilio->save();
        }
    }

    /**
     * This method is used to download twilio recordings for tpv ivr
     */
    public function RecordingsCallbackIVR(Request $request)
    {
        \Log::info('In TPV IVR Recordings Function.');
        \Log::info($request->all());
        $inputs = $request->all();
        if($inputs['RecordingStatus'] == 'completed') {
            \Log::info("If recording status is completed then.");
            //fetch twilio call details for save recording
            $callDetails = TwilioLeadCallDetails::where('call_id', $inputs['CallSid'])->first();
            if (!empty($callDetails)) {
                //Save call recording to twilio lead call details for billing report
                $callDetails->twilio_recording_url = $inputs['RecordingUrl'];
                $callDetails->twilio_recording_id = $inputs['RecordingSid'];
                $callDetails->save();
                \Log::info($callDetails);
            }
            //download recordings into s3 bucket
            (new RecordingController())->downloadRecordingNew('',true,$inputs['CallSid']);
        }

    }

    /**
     * For get all questions of child lead details
     * @param $leadId, $language
     */
    public function getChildLeadQuestionDetails($leadId,$language)
    {
        \Log::info('In Get Child Lead Details function');
        $mergedArray = [];
        $teleSale = Telesales::with('form')->where('refrence_id', $leadId)->first();
        
        if (!empty($teleSale)) {
            if (array_get($teleSale, 'form')) {
                $zipcode = $teleSale->zipcodes()->first();
                $scriptFor = 'ivr_tpv_verification';
                if (!empty($zipcode)) {
                    $form = $teleSale->form;
                    if($scriptFor == 'ivr_tpv_verification'){   
                        $formId =  array_get($form, 'id');
                        $state = $zipcode->state;
                    }
                    else{
                        $formId = 0;
                        $state = 'ALL';
                    }
                    $clientId = $teleSale->client_id;
                    $formScript = DB::table('form_scripts')
                  
                    ->where('form_scripts.language', '=', $language)
                    ->where('form_scripts.form_id', '=', $formId)
                    ->where('form_scripts.client_id', '=', $clientId)
                    ->when($scriptFor, function ($query) use ($scriptFor) {
                        return $query->where('form_scripts.scriptfor', '=', $scriptFor);
                    })
                    ->where(function ($que) use($state, $language, $formId, $scriptFor,$clientId) {
                        $que->where('state', '=', DB::raw("CASE WHEN(select count(id) from form_scripts where state = '".$state."' and language = '".$language."' and client_id = '".$clientId."' and form_id = '".$formId."') > 1 then '".$state."' else 'ALL' end"));
                    })->first();            
                    if (isset($formScript)) {
                        $leadId = $teleSale->id;
                        $questions = ScriptQuestions::with(['questionConditions' => function($qu) {
                            $qu->where('condition_type','question');
                        }])->where('script_id', $formScript->id)->where('is_multiple',1)->where('form_id', $formId)->orderBy('position', 'ASC')->get();
                        $positions = array_column($questions->toArray(),'position');
                        $lastIndex = count($positions);
                        $positions[$lastIndex] = -1;
                        $mergedArray = $positions;
                        $childCount = $teleSale->childLeads()->get();
                        if(isset($childCount) && $childCount->count() > 1){
                            for($i = 1 ;$i < $childCount->count();$i++){
                                $mergedArray = array_merge($mergedArray,$positions);
                            }
                        }
                    }
                }
            }
        }
        Log::info('child position array');
        Log::info($mergedArray);
        return $mergedArray;
    }
}

