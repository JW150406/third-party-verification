<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Client;
use App\models\Clientsforms;
use Illuminate\Support\Facades\Auth;
use App\User; 
use Illuminate\Routing\UrlGenerator;
use App\models\FormScripts;
use App\models\ScriptQuestions;
use App\models\Zipcodes;
use DB;

class ScriptQuestionController extends Controller
{
    public $client_id = "";
    public $form_id = "";
    public $script_id = "";
    public $client_detail = "";
    public $form_detail = "";
    public $script_detail = "";
    public $client_obj = array();
    public $FormScripts_obj = array();
    public $ScriptQuestions_obj = array();
    public $tags = array(); 
   

    public function __construct(Request $request){
        $this->client_id = $request->client_id;
        $this->form_id = $request->form_id;
        $this->script_id = $request->script_id;
        $this->tags = Config('constants.Common_fields_for_script');
        
        if( empty($this->client_id) || empty($this->form_id ) || empty($this->script_id) ){             
            abort(403);
        }else{
            Client::findOrFail($this->client_id);
            ClientsForms::findOrFail($this->form_id);
            FormScripts::findOrFail($this->script_id);
        }
        $this->client_obj = (new Client);          
        $this->client_detail = $this->client_obj->getClientinfo( $this->client_id );
        $this->form_detail = (new ClientsForms)->getClientFormFields($this->form_id);
        $this->FormScripts_obj =  (new FormScripts);
        $this->ScriptQuestions_obj =  (new ScriptQuestions);        
        $this->script_detail =  $this->FormScripts_obj->getScript($this->client_id,$this->form_id,$this->script_id);
        $form_detail = $this->form_detail[0];
      
      
          
         $this->tags =  array_merge($this->tags,Config('constants.'.$form_detail->commodity_type));
          
         
        $fields = json_decode($form_detail->form_fields);
        foreach($fields as $field){
            if( !empty($field->label_text)){
          
                    if($field->type != 'heading')
                    $this->tags[] = "[".$field->label_text."]";
                

                 
            }
            
        }   
        
    }

    /**
     * This function is used to show formscript question list
     */
    public function questionsList(Request $request){        
        $script_detail = $this->script_detail;
        $client_id = $this->client_id;
        $client = $this->client_detail;
        $form_id = $this->form_id;
        $script_id = $this->script_id;
        $form_detail = $this->form_detail[0];
        $language = $script_detail->language;
        $states = (new Zipcodes)->getStates($client_id);
       
        if( isset($request->state) && isset($request->commodity) ){
            $state = $request->state;
            $commodity = $request->commodity;
            $questions = $this->ScriptQuestions_obj->Questionslist($this->client_id, $this->form_id, $this->script_id,$state, $commodity);
        }else{
            $state = "";
            $commodity = "";
             
            if( $script_detail->scriptfor =='salesagentintro'){
                $questions = $this->ScriptQuestions_obj->QuestionslistWithoutState($this->client_id, $this->form_id, $this->script_id);
            }else{
                $questions = array();
            }

            
        }
        
        return view('client.forms.scriptquestions.questionslist',compact('client_id','questions','client','form_id','script_id','form_detail','script_detail','language','states','state','commodity'))
        ->with('i', ($request->input('page', 1) - 1) * 20);        
    }

    /**
     * This function is used to add formscript questions
     */
    public function addQuestion(Request $request){
        $script_detail = $this->script_detail;
        $client_id = $this->client_id;
        $client = $this->client_detail;
        $form_id = $this->form_id;
        $script_id = $this->script_id;
        $form_detail = $this->form_detail[0];
        $tags  = $this->tags;
        $language = $script_detail->language;
        if( isset($request->state) && $request->commodity ){
            $state = $request->state;
            $commodity = $request->commodity; 
          }else{
            $state = "";
            $commodity = ""; 
        }
        
        return view('client.forms.scriptquestions.createquestion',compact('client_id','client','form_id','form_detail','script_detail','script_id','tags' ,'language','state','commodity'  ));
    }

    /**
     * This function is used to store formscript questions
     */
    public function saveQuestion(Request $request){
        $script_detail = $this->script_detail;
        $client_id = $this->client_id;
        $client = $this->client_detail;
        $form_id = $this->form_id;
        $script_id = $this->script_id;
        
        $position = $this->ScriptQuestions_obj->getNewPosition($client_id,$form_id,$script_id);
        if(isset($position->position) > 0){
            $position = $position->position + 1;
        }else{
            $position =  1;
        }
        

        $data = $request->only('question','client_id','form_id','positive_ans','negative_ans','answer','is_customizable','state','commodity');
        $data['created_by'] =  Auth::user()->id;
        $data['position'] =  $position;
        $data['script_id'] =  $script_id;
      
        
        $added = $this->ScriptQuestions_obj->createQuestion($data);
        if( $added > 0 ) {
            return redirect()->back()
            ->with('success','Question created successfully.');
           }else{
            return redirect()->back()
            ->withErrors( 'Something went wrong! Please try again.');
           } 
        
    }

    /**
     * This function is used to remove formscript question
     */
    public function deleteQuestion(Request $request){
        $script_id = $request->script_id;
        $question_id = $request->question_id;
        $this->ScriptQuestions_obj->getQuestion($this->client_id, $this->form_id, $script_id,$question_id );
        $this->ScriptQuestions_obj->deleteQuestion($question_id);
        return redirect()->back()
            ->with('success','Question deleted successfully.'); 
     }

     /**
      * This function is used to show edit question form
      */
     public function editQuestion(Request $request){
        $script_detail = $this->script_detail;
        $client_id = $this->client_id;
        $client = $this->client_detail;
        $form_id = $this->form_id;
        $script_id = $this->script_id;
        $question_id = $request->question_id;
        $form_detail = $this->form_detail[0];
        $tags  = $this->tags;
        $question_detail = $this->ScriptQuestions_obj->getQuestion($this->client_id, $this->form_id, $script_id,$question_id );
        $language = $script_detail->language;
        $state = $request->state;
        $commodity = $request->commodity;
        return view('client.forms.scriptquestions.editquestion',compact('client_id','client','form_id','form_detail','script_detail','script_id','tags','question_detail','language' ,'state','commodity' ));
    }

    /**
     * This method is used to update formscript question
     */
    public function updateQuestion(Request $request){
        $script_detail = $this->script_detail;
        $client_id = $this->client_id;
        $client = $this->client_detail;
        $form_id = $this->form_id;
        $script_id = $this->script_id;
        $question_id = $request->question_id;
        $this->ScriptQuestions_obj->getQuestion($this->client_id, $this->form_id, $script_id,$question_id );
        $data = $request->only('question','positive_ans','negative_ans','answer','is_customizable');
        $added = $this->ScriptQuestions_obj->updateQuestion($question_id,$data);
 
        return redirect()->back()
            ->with('success','Question updated successfully.');         
        
    }
    public function updatePositions(Request $request){
       
        if(isset($request->question_fields) && count($request->question_fields) > 0){
            foreach($request->question_fields as $quesion_id => $position){
                $this->ScriptQuestions_obj->updatePosition($quesion_id,$position);
            }
        }
    }
    
    /**
     * This function is used to clone formscript question
     */
    public function cloneQuestion(Request $request){
        /* Start Validation rule */
        $validator = \Validator::make($request->all(), [ 
            'state' => 'required',
            'commodity' => 'required' 
             
        ]);
        /* End Validation rule */
      
 
        if( isset($request->state ) ){
            
            
            $where = " ";
            $clone_single = 0;
            if( isset($request->question_id ) &&  $request->question_id  != ""){
                $where = "where id ='".$request->question_id."'";
                $clone_single = 1;
            } 
            if( isset($request->clonedfromstate ) &&  $request->clonedfromstate  != "" && $clone_single == 0){
                $commodity = $request->clonedfromcommodity;
                
                $script_id = $request->scripttoclone;
                $client_id =  $this->client_id;
                $form_id =  $this->form_id;
                
                
                $where = "where state ='".$request->clonedfromstate."' and commodity ='".$commodity."' and script_id = '".$script_id."' and client_id ='".$client_id."' and form_id= '". $form_id."'";
            }
   
            DB::insert("
            INSERT INTO script_questions
            ( 
                 
                client_id,
                form_id,
                script_id,
                created_by,
                question,
                position,
                created_at,
                updated_at,
                positive_ans,
                negative_ans,
                answer,
                is_customizable,
                state,
                commodity
            )
            SELECT  
                client_id,
                form_id,
                script_id,
                '".Auth::user()->id."',
                question,
                position,
                now(),
                now(),
                positive_ans,
                negative_ans,
                answer,
                is_customizable,
                '".$request->state."',
                '".$request->commodity."'
               FROM script_questions  $where
            ");

        }
   return redirect()->back()
            ->with('success','Question cloned successfully.'); 

        
 


     
     }
     
    
}
