<?php

namespace App\Http\Controllers\Client;

use App\models\Client;
use App\models\Clientsforms;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Client\FormsController;
use App\models\FormScripts;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Log;
use DB;
use App\models\Zipcodes;
use App\models\ScriptQuestions;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\models\Scriptquestionsconditions;

class ScriptsController extends Controller
{
    public $scriptImportTags = array();

    public function list($clientId, $formId, Request $request) {
        $client = Client::find($clientId);

        if (empty($client)) {
            return redirect()->route('client.index')->withErrors('Client not found !!');
        }

        $form = Clientsforms::find($formId);

        if (empty($form)) {
            return redirect()->back()->with('error', 'Form not found !!');
        }

        return view('client.scripts.list', compact('client', 'form'));
    }

     /**
     * Display the specified resource.
     *
     * @param  $clientId, $formId, $scriptId
     * @return \Illuminate\Http\Response
     */
    public function show($clientId, $formId, $scriptId) {
        try {
          
            $client = Client::find($clientId);

            if (empty($client)) {
                Log::error('Error while retrieving script with id: ' . $scriptId . " => Client not found with id: " . $clientId);
                return redirect()->back()->with('error', 'Script client not found !!');
            }

            $form = [];
            if ($formId != 0) {
                $form = Clientsforms::find($formId);
            }

            // if (empty($form)) {
            //     Log::error('Error while retrieving script with id: ' . $scriptId . " => Client form not found with id: " . $formId);
            //     return redirect()->back()->with('error', 'Script form not found !!');
            // }

            $script = FormScripts::with('form', 'form.client')->find($scriptId);

            if (empty($script)) {
                Log::error('Error while retrieving script with id: ' . $scriptId . " => Script not found with id: " . $scriptId);
                return redirect()->back()->with('error', 'Script not found !!');
            }


            Log::info('Script retrieved with id: ' . $scriptId);
            return view('client.scripts.review', compact('script', 'form', 'client'));
        } catch (\Exception $e) {
            Log::error('Error while retrieving script with id: ' . $scriptId);
            return redirect()->back()->with('error', 'Something went wrong while retrieving review script screen !!');
        }
    }

    public function questions($clientId, $formId, $scriptId, Request $request)
    {

        $script = FormScripts::find($scriptId);

        $generalScripts = array_keys(array_except(config()->get('constants.scripts'), array('customer_verification', 'self_verification','ivr_tpv_verification')));


        if (in_array(array_get($script, 'scriptfor'), $generalScripts)) {
            $questions = ScriptQuestions::with(['script' => function($q) use($request) {
                if($request->state != 'ALL' && $request->state != null) {
                    $q->where('state', $request->state);
                }
            }])->where('script_id', $scriptId)->where('client_id', $clientId)->where('form_id', 0);
        } else {
            $questions = ScriptQuestions::with(['script' => function($q) use($request) {
                if($request->state != 'ALL' && $request->state != null) {
                    $q->where('state', $request->state);
                }
            }])->where('script_id', $scriptId)->where('client_id', $clientId)->where('form_id', $formId);
        }
        
        $questionsArray = $questions;
        $questionIds = array_column($questionsArray->get()->toArray(),'id');
        $scriptConditions = Scriptquestionsconditions::select(DB::raw('count(id) as count'),'question_id')->whereIn('question_id',$questionIds)->groupBy('question_id')->get()->toArray();
        $questionArr = [];
        foreach($scriptConditions as $k => $v)
        {
            $questionArr[$v['question_id']] = $v['count'];
        }
        $questions = $questions->orderBy('position');
        // dd($questions->get());
        // if (array_get($script, 'scriptfor') == "customer_verification") {
            return DataTables::of($questions)
            ->editColumn('question',function($questions){
                return nl2br(e($questions->question));
            })
            ->editColumn('is_introductionary',function($questions){
                return ($questions->is_introductionary == 1) ? 'Yes' : '';
            })
            ->addColumn('multiple_enrollments',function($question){
                if ($question->is_multiple == '0') {
                    $isMultiple = 'No';
                } else {
                    $isMultiple = 'Yes';
                }
                return $isMultiple;
            })
            ->addColumn('action',function($question){
                return '<a data-toggle="tooltip"
                data-placement="top"
                data-type="view"
                data-original-title="Add Condition"
                class="btn add_condition_class theme-color"  
                data-id="'.$question->id.'"              
                >' . getimage("images/add_blue.png") . '</a>';
            })
            ->addColumn('condition',function($question) use($questionArr){
                if(isset($questionArr[$question->id]))
                    $count = $questionArr[$question->id];
                else
                    $count = 0;
                if($count == 0)
                    return '';
                else
                    return '<span data-toggle="tooltip"
                    data-placement="top"
                    data-type="view"
                    data-original-title="'.$count.' Conditions" class="question-tag">'.$count.'</span>';
            })
            ->addColumn('negative_answer_action',function($question) use($questionArr){
               return ($question->negative_answer_action == '1') ? 'Yes' : 'No';
            })
            ->addColumn('is_customizable',function($question) use($questionArr){
                return ($question->is_customizable == '1') ? 'Yes' : 'No';
             })
            ->rawColumns(['question','action','condition'])
            ->make(true);
        // } else {
        //     return DataTables::of($questions)
        //     ->addColumn('action',function($question){
        //         return '<a data-toggle="tooltip"
        //         data-placement="top"
        //         data-type="view"
        //         data-original-title="Add Condition"
        //         class="btn  theme-color"                
        //         >' . getimage("images/add_green.png") . '</a>';
        //     })
        //     ->rawColumns(['action'])
        //     ->make(true);
        // }
    }

    /**
     * This function is used to show import script question list
     */
    public function getImportQuestions(Request $request) {

        
        $clients = (new Client())->getClientsListByStatus('active');
        $client = Client::active()->findOrFail($request->client_id);
        $form = Clientsforms::find($request->form_id);
        return view('client.forms.scriptquestions.import', ['clients' => $clients,'client'=>$client,'form'=>$form,"upload_id"=>$request->script_upload_id]);
    }

    public function getExportTags($client_id,$form_id,$scriptType)
    {
        // dd("here in getExportTags");
        $tags = $this->getAllTagsWithCategory($form_id);
        
        if($form_id == 0)
        {
            $formName = "N/A";
        }
        else
        {
            $form_name = Clientsforms::find($form_id);
            $formName = $form_name->formname;
            $commodity = $form_name->commodities;
        }
        $data = $tags->original['data'];
        $tagsArray =[];
        $i = 0;
        foreach($data as $key => $val)
        {
            foreach($val as $k => $v){
                    
                $tagsArray[$i]["Sr No."] = $i+1;
                $tagsArray[$i]["Form"] = $formName;
                $tagsArray[$i]["Label"] = $v['label'];
                $tagsArray[$i]["Tag"] = $v['tags'];
                $tagsArray[$i++]["Category"] = $v['category'];
                }
        }
        Excel::create((config('constants.scripts-new-name.'.$scriptType))." Tags", function($excel) use ($tagsArray,$data) {
            $excel->sheet('sheet1', function($sheet) use ($tagsArray,$data)
            { 
                $sheet->fromArray($tagsArray);
                
            });
        })->download("xlsx");
    }

    /**
     * This function is used to download sample filescript
     */
    public function downloadSampleFileScript(Request $request)
    {
        // dd("here");
        try{
            $clientid = $request->clientid;
            $formid = $request->formid;
            $script_id = $request->script;
            $state = $request->state;
            $language = $request->language;
            $single_script =[];
            $question_info = [];
            if($request->state == "ALL"){
                $formid = 0;
            }
            $client = Client::find($request->clientid);
            if(isset($request->language) && ($request->language != null))
            {
                // dd("if");
                $script_det = ($client)->scripts()->where('client_id', $clientid)->where('form_id', $formid)->where('scriptFor', $request->script)->where('language',$language)->where('state',$state)->first();
                if(isset($script_det)){
                    $scriptId = $script_det->id;
                    $script = FormScripts::find($scriptId);

                    $generalScripts = array_keys(array_except(config()->get('constants.scripts'), array('customer_verification', 'self_verification','ivr_tpv_verification')));
                    // dd($generalScripts);
                    if (in_array(array_get($script, 'scriptfor'), $generalScripts)) {
                        $questions = ScriptQuestions::with(['script' => function($q) use($request,$state) {
                            //if($state != 'ALL' && $state != null) {
                                $q->where('state', $state);
                            // }
                        }])->where('script_id', $scriptId)->where('is_introductionary',0)->where('client_id', $clientid)->where('form_id', 0);
                    } else {
                        $questions = ScriptQuestions::with(['questionConditions','script' => function($q) use($request,$state) {
                            // if($state != 'ALL' && $state != null) {
                                $q->where('state', $state);
                            // }
                        }])->where('script_id', $scriptId)->where('client_id', $clientid)->where('form_id', $formid);
                    }
                    if($questions->count() > 0)
                    {
                        $single_script = $questions->get();
                        // dd($single_script);
                    }
                    else
                    {
                        return redirect()->back()->with('error', 'No sample script available for download');
                    }
                }
                else
                {
                    return redirect()->back()->with('error', 'No sample script available for download');
                }
            }
            else
            {
                // dd("else");
                $scripts_formid_0 = array_keys(array_except(config()->get('constants.scripts'), array('customer_verification', 'self_verification','ivr_tpv_verification')));
                if(in_array($request->script,$scripts_formid_0))
                {
                    $script_det = ($client)->scripts()->where('client_id', $clientid)->where('form_id', 0)->where('scriptFor', $request->script)->get();
                }
                else
                {
                    $script_det = ($client)->scripts()->where('client_id', $clientid)->where('form_id', $formid)->where('scriptFor', $request->script)->where('state','!=','ALL')->get();
                }

                if(isset($script_det) && ($script_det->count() > 0)){


                    foreach($script_det as $k => $v)
                    {

                        $scriptId = $v->id;
                        $script = FormScripts::find($scriptId);

                        $generalScripts = array_keys(array_except(config()->get('constants.scripts'), array('customer_verification', 'self_verification','ivr_tpv_verification')));
                        if (in_array(array_get($script, 'scriptfor'), $generalScripts)) {

                            $questions = ScriptQuestions::with(['script' => function($q) use($request,$state) {
                            }]);
                            $questions = $questions->where('script_id', $scriptId)->where('client_id', $clientid)->where('is_introductionary',0)->where('form_id', 0);

                        } else {

                            $questions = ScriptQuestions::with(['questionConditions','script' => function($q) use($request,$state) {
                                // if($state != 'ALL' && $state != null) {
                                    $q->where('state','!=',null);
                                // }
                            }])->where('script_id', $scriptId)->where('client_id', $clientid)->where('form_id', $formid);
                        }
                        $mergeArrbulk = [];
                        $questions = $questions->get();
                        foreach($questions as $key => $val)
                        {
                            $mergeArrbulk = [];
                            $questionArrBulk = [];
                            $tagArrBulk = [];
                            foreach($val->questionConditions as $ke => $va){
                                if($va->condition_type == 'question')
                                {
                                    $position = (new ScriptQuestions)->getQuestionDetail($va->tag);
                                    $position = $position[0]->position;
                                    $tag = 'Q'.$position;
                                    $comparisionVal = config()->get('constants.script_question_condition_value_reverse.'.$va->comparison_value);
                                    $value = "question || ".$tag ." || ".$va->operator ." || ".$comparisionVal."\n&";
                                    $mergeArrbulk[] = $value;
                                } else {
                                    $tag = '['.$va->tag.']';
                                    $comparisionVal = $va->comparison_value;
                                    $value = "tag || ".$tag ." || ".$va->operator ." || ".$comparisionVal."\n&";
                                    $mergeArrbulk[] = $value;
                                }
                            }
                            $finalValue = implode('&', $mergeArrbulk);
                            $mergeArrbulkFinal = str_replace("&", "" ,$finalValue);

                            $val = $val->toArray();
                            $key2 = $val['script']['language'];
                            $key1 = $val['script']['state'];

                            if($val['script']['state'] == null)
                            $key1 = "ALL";
                            if(isset($question_info[$key1." ".$key2]))
                            {
                                $question_info[$key1." ".$key2]['question'][] = $val['question'];
                                $question_info[$key1." ".$key2]['positive'][] = $val['positive_ans'];
                                $question_info[$key1." ".$key2]['negative'][] = $val['negative_ans'];
                                $question_info[$key1." ".$key2]['is_custom'][] = ($val['is_customizable'] == null) ? '0' : '1';
                                $question_info[$key1." ".$key2]['negative_ans_action'][] = ($val['negative_answer_action'] == 0) ? '0' : '1';
                                $question_info[$key1." ".$key2]['is_multiple'][] = ($val['is_multiple'] == 0) ? '0' : '1';
                                $question_info[$key1." ".$key2]['ans'][] = $val['answer'];
                                $question_info[$key1." ".$key2]['Conditions'][] = $mergeArrbulkFinal;
                                if($val['is_introductionary'] == 1 && $val['position'] == 0)
                                {
                                    $question_info[$key1." ".$key2]['introductory'][] = "This question should be consider as intro question ";
                                }
                            }
                            else
                            {
                                $question_info[$key1." ".$key2]['question'][] = $val['question'];
                                $question_info[$key1." ".$key2]['positive'][] = $val['positive_ans'];
                                $question_info[$key1." ".$key2]['negative'][] = $val['negative_ans'];
                                $question_info[$key1." ".$key2]['is_custom'][] = ($val['is_customizable'] == null) ? '0' : '1';
                                $question_info[$key1." ".$key2]['negative_ans_action']
                                [] = ($val['negative_answer_action'] == 0) ? '0' : '1';
                                $question_info[$key1." ".$key2]['is_multiple'][] = ($val['is_multiple'] == 0) ? '0' : '1';
                                $question_info[$key1." ".$key2]['ans'][] = $val['answer'];
                                $question_info[$key1." ".$key2]['Conditions'][] = $mergeArrbulkFinal;
                                if($val['is_introductionary'] == 1 && $val['position'] == 0)
                                {
                                    $question_info[$key1." ".$key2]['introductory'][] = "This question should be consider as intro question ";
                                }
                            }
                        }
                    }
                }
                else
                {
                    return redirect()->back()->with('error', 'No sample script available for download');
                }
            }
            $i = 0;
            $finalArr = [];
            $general_scripts_exception = array_keys(array_except(config()->get('constants.scripts'), array('customer_verification', 'identity_verification','ivr_tpv_verification')));
            
            if(isset($questions)){
                foreach($question_info as $k =>$v)
                {
                    // dd($v);
                    $excelArray = [];
                    for($i = 0;$i<count($v['question']);$i++)
                    {
                        if(!in_array($script_id,$generalScripts))
                        {
                            if(strpos($k,'en') > 0)
                            {
                                if($v['positive'][$i] == null)
                                {
                                    $positive = "Yes";
                                }
                                else
                                    $positive = $v['positive'][$i];
                                if($v['negative'][$i] == null)
                                {
                                    $negative = "No";
                                }
                                else
                                    $negative = $v['negative'][$i];
                            }
                            else
                            {
                                if($v['positive'][$i] == null)
                                {
                                    $positive = "Si";
                                }
                                else
                                    $positive = $v['positive'][$i];
                                if($v['negative'][$i] == null)
                                {
                                    $negative = "Non";
                                }
                                else
                                $negative = $v['negative'][$i];
                            }
                            $excelArray[$i]['Questions'] = $v['question'][$i];
                            $excelArray[$i]['Positive Answer'] = $positive;
                            $excelArray[$i]['Negative Answer'] = $negative;
                            if($script_id == 'customer_verification')
                            {
                                $excelArray[$i]['Verification Criteria'] = $v['ans'][$i];
                                $excelArray[$i]['Editable Tag'] = $v['is_custom'][$i];
                                $excelArray[$i]['Continue On Negative'] = $v['negative_ans_action'][$i];
                            }
                            if($script_id == 'customer_verification' || ($script_id == 'self_verification') || ($script_id == 'ivr_tpv_verification')){

                                $excelArray[$i]['Conditions'] = $v['Conditions'][$i];
                            }
                                if($script_id == 'customer_verification' || $script_id == 'self_verification' || $script_id == 'ivr_tpv_verification'){
                                    $excelArray[$i]['Is Multiple Enrollment'] = $v['is_multiple'][$i];   
                                }
                                if($script_id == 'customer_verification'){
                                    $excelArray[$i]['Conditions'] = $v['Conditions'][$i];   
                                }
                            
                            
                            if(isset($v['introductory'][$i])){
                                $excelArray[$i]['Notes'] = $v['introductory'][$i];
                            }
                        }
                        else if(!in_array($script_id,$general_scripts_exception))
                        {
                            $excelArray[$i]['Questions'] = $v['question'][$i];
                            $excelArray[$i]['Positive Answer'] = $v['positive'][$i];
                            $excelArray[$i]['Negative Answer'] = $v['negative'][$i];
                            $excelArray[$i]['Verification Criteria'] = $v['ans'][$i];
                        }
                        else
                        {
                            $excelArray[$i]['Questions'] = $v['question'][$i];
                        }
                        $finalArr[$k] = $excelArray;
                    }
                }
                
                $excelArray = [];
                $mergeArr = [];
                $i = 0;
                foreach($single_script as $k =>$v)
                {
                   if(!in_array($script_id,$generalScripts))
                    {
                       if(empty($v->positive_ans))
                        {   
                           if($request->language == "en")
                                $positive = "Yes";
                            else
                                $positive = "Si";
                        }
                       else
                       {
                           $positive = $v->positive_ans;
                       }
                       if(empty($v->negative_ans))
                       {
                           if($request->language == "en")
                                $negative = "No";
                            else
                                $negative = "Non";
                       }
                       else
                       {
                           $negative = $v->negative_ans;
                       }
                        $excelArray[$i]['Questions'] = $v->question;
                        $excelArray[$i]['Positive Answer'] = $positive;
                        $excelArray[$i]['Negative Answer'] = $negative;
                        if($script_id == 'customer_verification')
                        {
                            $excelArray[$i]['Verification Criteria'] = $v->answer;
                        }
                        if($script_id == 'customer_verification')
                        {
                            $excelArray[$i]['Editable Tag'] = ($v->is_customizable == 1) ? '1' : '0';
                            $excelArray[$i]['Continue On Negative'] = ($v->negative_answer_action == 0) ? '0' : '1';
                        }
                        if($script_id == 'customer_verification' || $script_id == 'self_verification' || $script_id == 'ivr_tpv_verification'){
                            $excelArray[$i]['Is Multiple Enrollment'] = ($v->is_multiple == 0) ? '0' : '1';
                        }
                        
                        if($script_id == 'customer_verification' || ($script_id == 'self_verification') || ($script_id == 'ivr_tpv_verification')){
                            $mergeArr = [];
                            $questionArr = [];
                            $tagArr = [];
                            if(isset($v->questionConditions) && $v->questionConditions->count() > 0){
                                foreach($v->questionConditions as $ke => $va){
                                    if($va->condition_type == 'question')
                                    {
                                        $position = (new ScriptQuestions)->getQuestionDetail($va->tag);
                                        $position = $position[0]->position;
                                        $tag = 'Q'.$position;
                                        $comparisionVal = config()->get('constants.script_question_condition_value_reverse.'.$va->comparison_value);
                                        $value = "question || ".$tag ." || ".$va->operator ." || ".$comparisionVal."\n&";
                                        $mergeArr[] = $value;
                                    }
                                    else{
                                        $tag = '['.$va->tag.']';
                                        $comparisionVal = $va->comparison_value;
                                        $value = "tag || ".$tag ." || ".$va->operator ." || ".$comparisionVal."\n&";
                                        $mergeArr[] = $value;
                                    }
                                }
                                $finalValue = implode('&', $mergeArr);
                                $excelArray[$i]['Conditions'] = str_replace("&", "" ,$finalValue);
                                
                            } else {
                                $excelArray[$i]['Conditions'] = "";
                            }  
                        }

                        if($v->is_introductionary == 1)
                        {
                            $excelArray[$i]['Notes'] = "This question should be consider as intro question ";
                        }
                        else
                        {
                            $excelArray[$i]['Notes'] = null;
                        }
                    }
                    else if(!in_array($script_id,$general_scripts_exception))
                    {

                        $excelArray[$i]['Questions'] = $v->question;
                        $excelArray[$i]['Positive Answer'] = $v->positive_ans;
                        $excelArray[$i]['Negative Answer'] = $v->negative_ans;
                        $excelArray[$i]['Verification Criteria'] = $v->answer;

                   }else
                   {
                       $excelArray[$i]['Questions'] = $v->question;
                   }

                    $i++;
                }
                $Lan = $request->language;
                $Lan = isset($Lan) ? strtoupper($Lan) : '';
                $timeZone = Auth::user()->timezone;
                $currentDateTime = Carbon::now()->setTimezone($timeZone);
                Excel::create(config('constants.scripts-new-name.'.$script_id).' - '.$client->name.' '.$state.' '.$Lan.' '.$currentDateTime->format('m-d-Y H-i A'), function($excel) use ($excelArray,$state,$language,$finalArr) {
                    if(isset($excelArray) && count($excelArray) >0)
                    {
                        $excel->sheet($state." ".strtoupper($language), function($sheet) use ($excelArray)
                        {
                            $sheet->fromArray($excelArray);
                            $sheet->setWidth(array(
                                'A'     =>  80,
                                'B'     =>  10,
                                'C'     =>  10,
                                'D'     =>  20,
                                'E'     =>  30
                            ));
                        });
                    }
                    foreach($finalArr as $k => $v)
                    {
                        $excel->sheet(strtoupper($k), function($sheet) use ($v)
                        {
                            $sheet->fromArray($v);

                            $sheet->setWidth(array(
                                'A'     =>  80,
                                'B'     =>  20,
                                'C'     =>  10,
                                'D'     =>  20,
                                'E'     =>  30
                            ));
                        });
                    }
                })
                ->download("xlsx");
            }
            else
            {
                return redirect()->back()->with('error', 'Something went wrong');
            }
        }catch(\Exception $e)
        {
            Log::info($e->getMessage());
            Log::error('Error downloading in excel file: ');
            Log::info($e);
            return redirect()->back()->with('error', 'Something went wrong ');
        }
    }

    /**
     * This function is used to download samplescript
     */
    public function downloadSampleScript(Request $request)
    {
        try
        {
            if($request->upload_type == 1)
            {
                $sampleFile = public_path('/scripts/multi_scripts/'.config('constants.scripts-new-name.'.$request->script).'.xlsx');
            }
            else
            {
                $sampleFile = public_path('/scripts/single_scripts/'.config('constants.scripts-new-name.'.$request->script).'.xlsx');
            }
            Excel::load($sampleFile, function($reader){

            })->ignoreEmpty()->download('xlsx');
        }
        catch(\Exception $e)
        {
            Log::error('Error downloading  excel file: '.$e);
            return redirect()->back()->with('error','something went wrong while downloading sample file.');
        }
    }

    /**
     * This method is used to get all states
     */
    public function getAllStates(Request $request)
    {
        $states = Zipcodes::groupBy('state')->get(['state']);
        return response()->json([
            "status" => "success",
            'data' => $states
        ]);
    }

    /**
     * This function is used to get import questions
     */
    public function importQuestions(Request $request) {
        
        try{
            $rule = [
                    'type' => 'required'
                    // 'csv'      => 'required|mimes:xlsx,xls'
            ];
            $messages = array( 
                'type.required' => 'Please select script',
                'csv.required' => 'The XLSX field is required.',
                // 'csv.mimes' => 'Please upload only xlsx file.',
            );

            $validator = Validator::make($request->all(), $rule,$messages);

            if ($validator->fails()) {
                //return back()->withErrors($validator->errors())->withInput($request->all());
                return response()->json(['status' => 'error',  'errors' => $validator->errors()->messages()], 422);
            }
            
            $validations = $this->validateImportFile($request);
            
            if(count($validations) > 0)
            {
                //return redirect()->back()->with(\Session::push('validations', $validations))->withInput($request->all());
                return response()->json(['status' => 'dataErrors',  'errors' => $validations], 422);
            }
            $path = $request->file('csv')->getRealPath();
            $flag = false;
            Excel::load($path, function($reader) use ($request,&$flag) {
                $sheets = $reader->ignoreEmpty()->get();
                foreach ($sheets as $sheet) {
                    // get sheet title
                    if ($request->upload_script == 1) {
                        $sheetTitle = ($sheet->getTitle() != "") ? $sheet->getTitle() : $sheets->getTitle();

                        $name = explode(' ', $sheetTitle);
                        $language = 'en';
                        $state = 'ALL';

                        if (is_array($name)) {
                            $state = strtoupper($name[0]);
                            $language = strtolower($name[1]) == "(en)" || strtolower($name[1]) == "en" ? "en" : "es";
                        }
                    } else {
                        $state = $request->state;
                        $language = $request->language;
                    }
                    $generalScripts = array_keys(array_except(config()->get('constants.scripts'), array('customer_verification', 'self_verification','ivr_tpv_verification')));
                    if (in_array($request->type, $generalScripts)) {
                        $form_id = 0;
                    } else {
                        $form_id = $request->form;
                    }
                    $script = FormScripts::where('client_id', $request->client)
                        ->where('form_id', $form_id)
                        ->where('scriptfor', $request->type)
                        ->where('language', $language);

                    if (!in_array($request->type, $generalScripts)) {
                        $script = $script->where('state', $state);
                    }
                    $script = $script->get();
                    // \Log::info('<pre>');
                    // Log::debug($script->toArray());
                    $createdAt = Carbon::now();
                    if (!empty($script)) {
                        foreach ($script as $scr) {
                            $scriptQuestion = ScriptQuestions::where('script_id',array_get($scr, 'id'))->pluck('id');
                            $scriptQuestionConditions = Scriptquestionsconditions::whereIn('question_id', $scriptQuestion)->forceDelete();
                            $scriptObj = FormScripts::find(array_get($scr, 'id'));
                            $createdAt = $scriptObj->created_at;
                            $scriptObj->forceDelete();
                            $scriptQuestion = ScriptQuestions::where('script_id',array_get($scr, 'id'))->forceDelete();
                            Log::debug("Successfully deleted all the data related to script id : ". array_get($scr, 'id'));
                        }
                    }

                    $script = new FormScripts();
                    $script->client_id = $request->client;
                    if (in_array($request->type, $generalScripts)) {
                        $script->form_id = 0;
                    } else {
                        $script->form_id = ($request->has('form') && $request->get('form') != "") ? $request->get('form') : 0;
                    }
                    $script->state = $state;
                    $script->scriptfor = $request->type;
                    $script->created_by = Auth::id();
                    $script->language = $language;
                    $script->created_at = $createdAt;
                    $script->save();
                    Log::info("Successfully script data is uploaded in form_script table, new script id : ". $script->id);

                    if ($request->upload_script == 2) {
                        $questions = $sheets;
                    } else {
                        $questions = $sheet;
                    }
                    $i = 0;
                    foreach ($questions as $key => $question) {
                        
                        if ((isset($question->questions) && !empty($question->questions))) {
                            $SQuestion = new ScriptQuestions();
                            $SQuestion->client_id = $request->client;
                            if (in_array($request->type, $generalScripts)) {
                                $SQuestion->form_id = 0;
                            } else {
                                $SQuestion->form_id = ($request->has('form') && $request->get('form') != "") ? $request->get('form') : 0;
                            }
                            if ($key == 0) {
                                if (!in_array($request->type, $generalScripts)) {
                                    $SQuestion->position = 0;
                                    $SQuestion->is_introductionary = 1;
                                }
                            } else {
                                $i++;
                                $SQuestion->position = $i;
                            }

                            $SQuestion->script_id = $script->id;
                            $SQuestion->created_by = Auth::id();

                            // $SQuestion->state = $state;
                            $SQuestion->question = isset($question->questions) ? $question->questions : $question->questions;
                            $SQuestion->answer = isset($question->verification_criteria) && !empty($question->verification_criteria) ? $question->verification_criteria : null;

                            if ($language == "en") {
                                $SQuestion->positive_ans = isset($question->positive_answer) && !empty($question->positive_answer) ? $question->positive_answer : "Yes";
                            } else {
                                $SQuestion->positive_ans = isset($question->positive_answer) && !empty($question->positive_answer) ? $question->positive_answer : "Si";
                            }

                            if ($language == "en") {
                                $SQuestion->negative_ans = isset($question->negative_answer) && !empty($question->negative_answer) ? $question->negative_answer : "No";
                            } else {
                                $SQuestion->negative_ans = isset($question->negative_answer) && !empty($question->negative_answer) ? $question->negative_answer : "Non";
                            }
                            $SQuestion->is_customizable = isset($question->editable_tag) && !empty($question->editable_tag) ? $question->editable_tag : 0;
                            $SQuestion->negative_answer_action = ($question->continue_on_negative == '1' ? 1 : 0);  
                            $SQuestion->is_multiple = ($question->is_multiple_enrollment == '1' ? 1 : 0);  
                            $SQuestion->save();
                            Log::info("New question added successfully for script id : ". $script->id ." question id : ".$SQuestion->id);

                            if ($request->type == ('customer_verification') || $request->type == ('self_verification') || $request->type == ('ivr_tpv_verification')) {
                                // For check condition is available or not for particular condition
                                if ((isset($question->conditions) && !empty($question->conditions)) && $question->conditions != null) {
                                    
                                    $allConditions = explode("\n", $question->conditions);
                                    Log::info($allConditions);
                                    foreach ($allConditions as $condition) {
                                        $splitCondition = array_map('trim',array_values(array_filter(explode("||", $condition))));
                                        Log::info($splitCondition);
                                        if (isset($splitCondition[1]) && !empty($splitCondition[1])) {
                                            $check = str_starts_with($splitCondition[1], "Q");
                                        
                                            $queId = $SQuestion->id;
                                            if ($check && ($request->type == 'customer_verification')) {
                                                $quePosition = str_replace("Q", "" ,$splitCondition[1]);
                                                $ques = ScriptQuestions::where('script_id', '=', $script->id)->where('position', '=', $quePosition)->first();
                                                $tag = $ques->id;
                                                $condition_type = 'question';
                                                $comparison_value = (!empty($splitCondition[3])) ? (($splitCondition[3] == 'yes') ? config()->get('constants.script_question_condition_value.yes') : config()->get('constants.script_question_condition_value.no')) : null;

                                            } else {
                                                $tag = preg_replace('/[^a-zA-Z ->]/', '', $splitCondition[1]);
                                                // $tag = str_replace(array('[[',']]'),'',$splitCondition[1]);
                                                $condition_type = 'tag';   
                                                $comparison_value = (!empty($splitCondition[3])) ? $splitCondition[3] : null;
                                            }

                                            $storeCondition = new Scriptquestionsconditions;
                                            $storeCondition->question_id = $queId;
                                            $storeCondition->tag = $tag;
                                            $storeCondition->operator = $splitCondition[2];
                                            $storeCondition->comparison_value = $comparison_value;
                                            $storeCondition->condition_type = $condition_type;
                                            $storeCondition->save();

                                            Log::info("Condition id : ". $storeCondition->id ." added for question id : ". $queId . ", For script : ". $script->id);
                                        } 
                                    }
                                }
                            }
                        }    
                    }
                    if ($request->upload_script == 2) {
                        break;
                    }
                }
            })->ignoreEmpty();
            return response()->json(['status' => 'success',  'message' =>"Script imported successfully"], 200);
            //return redirect()->back()->with(\Session::push('success', "Import Successfullly"));

        }catch(\Exception $e)
        {
            Log::error('Error occured whle import script : ' .$e);
            return response()->json(['status' => 'error',  'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * This function is used to validate importfile that are imported using script import and return all validation error messages
     */
    public function validateImportFile($request)
    {
     try{
            $validations = [];
            $formId = $request->form;
            $generalScripts = array_keys(array_except(config()->get('constants.scripts'), array('customer_verification', 'self_verification','ivr_tpv_verification')));
            if(in_array($request->type,$generalScripts))
            {
                $formId = 0;
            }
            $uploadId = $request->upload_script;
            $allTags = (new FormsController)->getAllTags($formId);
            $tags = $allTags->original['data'];
            $path = $request->file('csv')->getRealPath();
            if(($request->file('csv')->getClientOriginalExtension() != 'xlsx') && ($request->file('csv')->getClientOriginalExtension() != 'xls'))
            {
                $validations[] ="Please upload only xlsx file";
                return $validations;
            }

            Excel::load($path, function($reader) use ($request,&$validations,$tags,$uploadId,$generalScripts) {
                $sheets = $reader->ignoreEmpty()->get();
                $headers = '';
                foreach($sheets as $k => $sheet)
                {
                    if($uploadId == 2)
                    {
                        $sheet = $sheets;
                        if(count(explode(" ",$sheet->getTitle())) > 2)
                        {
                            $validations[] = "Invalid File For single script import";
                            return $validations;
                        }
                    }

                    if($uploadId == 1)
                    {
                        if($sheet->getTitle() == "")
                        {
                            $validations[] = "Atleast 2 sheet title is required";
                            return $validations;
                        }
                    }

                    $headers = $sheet->getHeading()[0];

                    if($headers == null)
                    {
                        $validations[] = "UnExpected value found at header: ".$sheet->heading." Expected value : Questions  ";
                    }
                    else{

                        if($headers != "questions")
                        {
                            $validations[] = "UnExpected value found of header at sheet ".($k+1)." : ".$headers." Expected value : Questions  ";
                        }
                    }

                    $titleArray = $sheet->getTitle();
                    $title = explode(" ",strtolower($titleArray));

                    if( $uploadId == 1 && ($titleArray == " " || count($title) !=2 || ($title[count($title)-1] != "en" && $title[count($title)-1] != "es")))
                    {
                        $validations[] = "UnExpected value found at Sheet ".($k+1)." name: ".$titleArray." Expected value  example[State Language] : ALL EN. Lanaguage can be either EN or ES ";
                    }
                    $generalScriptExcept = array_keys(array_except(config()->get('constants.scripts'), array('customer_verification', 'self_verification','salesagentintro','customer_call_in_verification','identity_verification','ivr_tpv_verification')));
                    // dd($generalScriptExcept);
                    // $generalTags = ["[AUTHORIZED NAME]"
                    // , "[AUTHORIZED NAME -> FIRST NAME]"
                    // , "[AUTHORIZED NAME -> MIDDLE NAME]"
                    // , "[AUTHORIZED NAME -> LAST NAME]"];

                     $sheetCount = 0;
                    foreach($sheet as $key => $val)
                    {
                        if(count($val) > 0)
                        {
                            if(array_key_exists('questions',$val->toArray()))
                            {
                                $sheetCount++;
                                $remainingQue = $val[$headers];
                                while(strpos($remainingQue , '[') > 0)
                                {
                                    if(strpos($remainingQue,'['))
                                    {
                                        $substring = substr($remainingQue,0,strpos($remainingQue,']'));
                                        $substring = substr($substring,strpos($remainingQue,'[')+1);
                                        $substring = trim($substring);

                                        $substring = "[".$substring."]";

                                        if(!(in_array(strtoupper($substring),$tags)) && !(in_array($request->type,$generalScripts)))
                                        {
                                            $validations[] = "Row Number ".($key+2)." -> Invalid tag :".$substring." found at sheet name: ".$sheet->getTitle();
                                        }
                                        if((in_array($request->type,$generalScripts)))
                                        {
                                            if($request->type == 'closing')
                                            {
                                                if(!(preg_match("/name/i", strtolower($substring))) && !(in_array(strtoupper($substring),$tags))) {
                                                    $validations[] = "Row Number ".($key+2)." -> Invalid tag :".$substring." found at sheet name: ".$sheet->getTitle();
                                                }
                                            }
                                            else
                                            {
                                                if(!(in_array(strtoupper($substring),$tags)))
                                                {
                                                    $validations[] = "Row Number ".($key+2)." -> Invalid tag :".$substring." found at sheet name: ".$sheet->getTitle();
                                                }
                                            }

                                        }

                                    }
                                    $remainingQue = substr($remainingQue,strpos($remainingQue,
                                        ']')+1);
                                }
                            }
                            else
                            {
                                $validations[] = "Question field is empty.";
                                return $validations;
                            }
                            
                            if ($request->type == ('customer_verification') || $request->type == ('self_verification') || $request->type == ('ivr_tpv_verification')) {
                                // For validate conditions : START
                                if ($key != 0) {    // Not able to add any type of condtion in first question

                                    // For check "conditions" column is exist or not in uploaded sheet
                                    if (array_key_exists('conditions',$val->toArray())) {
                                        $getAllConditions = array_map('trim',array_values(array_filter(explode("\n", $val->conditions))));
                                        foreach ($getAllConditions as $condition) {
                                            $seprateCondition = array_map('trim',array_values(array_filter(explode("||", trim($condition)))));
                                            
                                            if(isset($seprateCondition[1]) && !empty($seprateCondition[1])){
                                                
                                                $isQuestion = str_starts_with($seprateCondition[1], "Q");
                                                if (isset($condition) && !empty($condition)) {
                                                    
                                                    // Check condition about question or tag
                                                    if ($isQuestion && ($request->type == 'customer_verification')) {
                                                        $questionNumber = str_replace("Q", "", $seprateCondition[1]);

                                                        // For check questionNumber should be always previous question number
                                                        $newKey = $key ;
                                                        if (($newKey > $questionNumber) && ($questionNumber != 0)) {
                                                            if ($seprateCondition[2] == 'is_equal_to') {
                                                                if ((strtolower($seprateCondition[3]) != "yes") && (strtolower($seprateCondition[3]) != "no")) {
                                                                    $validations[] = "Row Number ".($key+2)." -> 'question' : Comparison value of is_equal_to should be either Yes or No";                                                                
                                                                }
                                                            } else {
                                                                $validations[] = "Row Number ".($key+2)." -> 'question' : Incorrect operator";
                                                            }
                                                        } else {
                                                            $validations[] = "Row Number ".($key+2)." -> 'question' : Condition question number is always less than then current question number";
                                                        }  

                                                    } else {
                                                        $separateTag = preg_replace_callback("/\[\s*([^\]]+)\s*\]/", function ($word) {
                                                            return "[".trim(strtoupper($word[1]))."]";
                                                            }, $seprateCondition[1]);
                                                            $separateTag = trim($separateTag);
                                                        // Check tag is exist or not with reletad form tags
                                                        if (in_array($separateTag, $tags)) {       
                                                            // Check about which type of operator it is, Operator which require and not required comparison value                                      
                                                            $operators = config('constants.SCRIPT_QUESTION_CONDITION_OPERATOR_REQUIRE_COMPARISON_VALUE');
                                                            if (in_array($seprateCondition[2], $operators)) {
                                                                if (isset($seprateCondition[3]) && !empty($seprateCondition[3])) {
                                                                    
                                                                    // if (($seprateCondition[2] == $operators[0]) || ($seprateCondition[2] == $operators[1])) {
                                                                    //     if ($seprateCondition[3] != ("Yes" || "No")) {
                                                                    //         $validations[] = "Row Number ".($key+2)." -> 'tag' : Comparison value of is_equal_to or is_not_equal should be either Yes or No";
                                                                    //     }
                                                                    // } 
                                                                    // else {
                                                                    //     $validations[] = "Incorrect condition format of tag";
                                                                    // }

                                                                } else {                                                         
                                                                    $validations[] = "Row Number ".($key+2)." -> 'tag' : Comparison value should be not empty";
                                                                }
                                                            } else {
                                                                if ($seprateCondition[2] != "exists") {
                                                                    $validations[] = "Row Number ".($key+2)." -> 'tag' : Incorrect condition operator";
                                                                }
                                                            }
                                                        } else {
                                                            $validations[] = "Row Number ".($key+2)." -> Invalid tag : ".$seprateCondition[1]." found at sheet name: ".$sheet->getTitle();
                                                        }
                                                    } 
                                                }
                                            }
                                        }
                                    } else {
                                        Log::info("conditions column is not available in uploaded sheet");   
                                    }
                                } else {
                                    if (isset($val['conditions']) && !empty($val['conditions'])) {
                                        $validations[] = "Row Number ".($key+2)." -> You can not add condition in first question";   
                                    }
                                }
                                // For validate conditions : END

                            }

                            if($request->type == 'customer_verification'){

                                if(array_key_exists('editable_tag',$val->toArray())){
                                    
                                    if($val['editable_tag'] != '0' && $val['editable_tag'] != '1' && $val['editable_tag'] != ''){
                                        $validations[] = "Row Number ".($key+2)." -> The value of editable tag should be either 0 or 1";
                                    }
                                }
                                else
                                {
                                    
                                    $validations[] = "Editable Tag field is empty in sheet name ".$sheet->getTitle().".";
                                    return $validations;
                                }
                                
                                if(array_key_exists('continue_on_negative',$val->toArray())){
                                    
                                    if($val['continue_on_negative'] != '0' && $val['continue_on_negative'] != '1' && $val['continue_on_negative'] != ''){
                                        $validations[] = "Row Number ".($key+2)." -> The value of continue on negative question should be either 0 or 1";
                                    }
                                }
                                else{
                                    $validations[] = "Continue On Negative field is empty in sheet name ".$sheet->getTitle().".";
                                    return $validations;
                                }
                                
                            }
                            
                        }
                    }
                    
                    if(in_array($request->type,$generalScriptExcept))
                    {
                        if($sheetCount != 1)
                        {
                            $validations[] = "In ".config('constants.scripts-new-name.'.$request->type)." script in sheet name ".$sheet->getTitle()." total no. of questions must be 1";
                        }
                    }
                     else if($request->type == 'salesagentintro')
                     {
                        if($sheetCount !=  4)
                        {
                            $validations[] = "In ".config('constants.scripts-new-name.'.$request->type)." script in sheet name ".$sheet->getTitle()." total no. of questions must be 4";
                        }
                     }
                     else if($request->type == 'identity_verification' || $request->type == 'customer_call_in_verification')
                     {
                        if($sheetCount !=  5)
                        {
                           $validations[] = "In ".config('constants.scripts-new-name.'.$request->type)." script  in sheet name ".$sheet->getTitle()." total no. of questions must be 5";
                        }
                     }
                     
                    if($uploadId == 2){
                        break;
                    }
                }
                
            })->ignoreEmpty();
            
            return $validations;

        }catch( \Exception $e)
        {
            Log::info($e);
             $validations[] = "Something went wrong.";
             return $validations;

        }
    }

    /**
     * This method is used to check state script
     */
    public function checkStateScript(Request $request)
    {
        $generalScripts = array_keys(array_except(config()->get('constants.scripts'), array('customer_verification', 'self_verification','ivr_tpv_verification')));
        $script = FormScripts::where('client_id', $request->client);
        if(!in_array($request->scriptType,$generalScripts))
        {
           $script =  $script->where('form_id', $request->form);
           if($request->has('state'))
           {
               if($request->state != "ALL")
                    $script =  $script->where('state',$request->state);
                else
                    return 'false';
           }
        }
         $script = $script->where('scriptfor',$request->scriptType);
         if($request->has('language'))
         {
            $script = $script->where('language',$request->language);
         }
                
         $script = $script->get();
     if($script->count() > 0)
     {
         return 'true';
     }
     else
        return 'false';
    }

    //Delete script
    public function delete(Request $request) {
        try {
            $this->validate(
                $request,
                [
                    'script_id' => 'required'
                ]
            );

            $script = FormScripts::find($request->get('script_id'));
            if (empty($script)) {
                return redirect()->back()->withErrors('Script not found.');
            }
            $script->delete();
            \Log::error('Script deleted with id: ' . $script->id);
            return redirect()->route('admin.clients.scripts.index', [$script->client_id, $script->form_id])->with('success', 'Script deleted.');
        } catch(\Exception $e) {
            \Log::error('Error while deleting script: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong, try again later.');
        }

    }

    /**
     * This method is used to get all tag with category
     */
    public function getAllTagsWithCategory($id) {

        try {
            $tags = (new FormsController)->getAllTags($id);
            $tags = $tags->original['data'];
            $category = config()->get('constants.TAGS_CATEGORY');
            
            if($id > 0)
            {
                $comType = 'dual';
                $form = Clientsforms::find($id);
                $commodity = $form->commodities;   
                if($commodity->count() == 1)
                {
                    $comType = $form->formname;
                }
                /* Get enable custom fields for program */
                $customFields =  getEnableCustomFields($form->client_id);
                if (!empty($customFields)) {
                    $category = array_merge($category,array_map('strtoupper',array_values($customFields)));
                }
            }
            
            
            foreach($tags as $key => $val)
            {
                $subtag = trim(substr($val,1,strpos($val,'->')-1));
                $condition = strpos($val,"->");
                
                if(is_numeric($condition))
                    $condition = $condition - 2;
                else
                    $condition = strlen($val);

                if(in_array(strtoupper($subtag),config()->get('constants.GENERAL_TAGS'))){
                    $tagCategory[$key]['tags'] = $val;
                    $tagCategory[$key]['category'] = config()->get('constants.Tags_Category_Names.1');
                    $tagCategory[$key]["label"] = ucfirst(substr(str_replace("]","",str_replace("[","",$val)),0,$condition));
                    unset($tags[$key]);
                }
                else if(in_array(strtoupper($subtag),$category))
                {
                    if($id > 0)
                    {
                        if($commodity->count() == 1)
                        {
                            $comType = strtolower($form->formname);
                        }
                        else
                        {
                            $comType = trim(strtolower(substr(str_replace("]", "",str_replace("[", "",$val)),$condition+3))) . " Enrollment";
                        }
                        $tagCategory[$key]['tags'] = $val;
                        $tagCategory[$key]['category'] = config()->get('constants.Tags_Category_Names.3').' - '.ucwords($comType);
                        $tagCategory[$key]["label"] = ucfirst(substr(str_replace("]","",str_replace("[","",$val)),0,$condition));
                        unset($tags[$key]);
                    }
                }
                else{
                    if(strpos($val,'->') !== false)
                    {
                        $addressTag = trim(substr($val,strpos($val,'->')+2));
                        $addressTag = substr($addressTag,0,strlen($addressTag)-1);
                        if(in_array(strtoupper($addressTag),config()->get('constants.ADDRESS_TAGS')))
                        {
                            $tagCategory[$key]['tags'] = $val;
                            $tagCategory[$key]['category'] = config()->get('constants.Tags_Category_Names.2');
                            $tagCategory[$key]["label"] = ucfirst(substr(str_replace("]","",str_replace("[","",$val)),0,$condition));
                        }
                        else
                        {
                            $tagCategory[$key]['tags'] = $val;
                            $tagCategory[$key]['category'] = config()->get('constants.Tags_Category_Names.4');
                            $tagCategory[$key]["label"] = ucfirst(substr(str_replace("]","",str_replace("[","",$val)),0,$condition));
                        }
                        
                    }   
                    else
                    {
                        if(preg_match('/address/i',$val))
                        {
                            $tagCategory[$key]['tags'] = $val;
                            $tagCategory[$key]['category'] = config()->get('constants.Tags_Category_Names.2');   
                            $tagCategory[$key]["label"] = ucfirst(substr(str_replace("]","",str_replace("[","",$val)),0,$condition));
                        }
                        else
                        {
                            $tagCategory[$key]['tags'] = $val;
                            $tagCategory[$key]['category'] = config()->get('constants.Tags_Category_Names.4');
                            $tagCategory[$key]["label"] = ucfirst(substr(str_replace("]","",str_replace("[","",$val)),0,$condition));
                        }
                    }
                }
            }   
            $tagWithCat = [];
            $i=0;
            foreach($tagCategory as $k => $val)
            {
                $tagWithCat[$val['category']][$i++] = $val;
            }
          return $this->success(true, 'success', $tagWithCat);
        } catch (\Exception $e) {
            \Log::error($e);            
            return response([
                'status' => false,
                'message' => 'Whoops, Something went wrong.'
            ]);
        }
    }

    /**
     * This method is used to store condition
     */
    public function saveCondition(Request $request)
    {
        // dd($request->all());
        $isExist = Scriptquestionsconditions::where('tag',$request->tag)
        ->where('operator',$request->operator)
        ->where('question_id',$request->questionId)
        ->where('condition_type',$request->conditionType)
        ->where('comparison_value',$request->get('compare'))
        ->get();
        if(isset($isExist) && $isExist->count() > 0)
        {
            return $this->success('error','error',400);
        }
        $scriptCondition = new Scriptquestionsconditions();
        $scriptCondition->tag = $request->tag;
        $scriptCondition->operator = $request->operator;
        $scriptCondition->question_id = $request->questionId;
        $scriptCondition->condition_type = $request->conditionType;
        $scriptCondition->comparison_value = ($request->has('compare'))?$request->get('compare'):'';
        $scriptCondition->save();
        return $this->success('success','success');
    }

    /**
     * This method is used to get condition
     */
    public function getCondition(Request $request)
    {
        $questionId = $request->questionId;
        $scriptConditions = Scriptquestionsconditions::leftjoin('script_questions','script_questions.id','=','script_questions_conditions.tag')->where('question_id',$questionId)
        ->select('tag','question_id','comparison_value','script_questions_conditions.id','condition_type','position','positive_ans','negative_ans',
        DB::raw('(case when operator = "is_equal_to" then "is equal to" when operator = "is_not_equal_to" then "isnot equal to" when operator = "is_greater_than" then "is greater than" when operator = "is_less_than" then "is less than" when operator = "exists" then "exists" when operator = "string_contains" then "string contains" when operator = "string_does_not_contains" then "string doesnot contains" when operator = "matches_regex" then "matches regex" end) as operator'))->get();

        return $this->success('success','success',$scriptConditions);
    }

    /**
     * This method is used to remove condition
     */
    public function deleteCondition(Request $request)
    {
        
        $ans = Scriptquestionsconditions::find($request->conditionId)->delete();
        if($ans == 1)
            return $this->success('success','success');
        else
            return $this->success('error','error');
    }

    /**
     * This method is used to get question for condition
     */
    public function getQuestionsForCondition(Request $request)
    {
        
        $questions = ScriptQuestions::with(['script' => function($q) use($request) {
            if($request->state != 'ALL' && $request->state != null) {
                $q->where('state', $request->state);
            }
        }])->where('is_introductionary', 0)->where('script_id', $request->scriptId)->where('client_id', $request->clientId)->where('form_id', $request->formId)->where('id','<',$request->questionId)->get(['id','position','question','positive_ans','negative_ans'])->toArray();
        return $this->success('success','success',$questions);
    }

}
