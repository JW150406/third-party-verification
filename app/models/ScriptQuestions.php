<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class ScriptQuestions extends Model
{
    use SoftDeletes;
    
    protected $table = 'script_questions';

    public function createQuestion($data)
    {
       return $this->insertGetId(
           [
               'client_id' => $data['client_id'],
               'form_id' => $data['form_id'],
               'created_by' => $data['created_by'],
               'question' =>  $data['question'],
               'script_id' =>  $data['script_id'],
               'position' =>  $data['position'],
               'positive_ans' =>  $data['positive_ans'],
               'negative_ans' =>  $data['negative_ans'],
               'is_customizable' =>  $data['is_customizable'],
               'answer' =>  $data['answer'],
               'state' =>  $data['state'],
               'commodity' =>  $data['commodity'],
               'created_at' =>  date('Y-m-d H:i:s'),
          ]
       );
    }
    public function updateQuestion($id,$data)
    {
       return $this->where('id',$id)
                    ->update( array(
                        'question' =>  $data['question'],
                        'positive_ans' =>  $data['positive_ans'],
                        'negative_ans' =>  $data['negative_ans'],
                        'answer' =>  $data['answer'],
                        'is_customizable' =>  $data['is_customizable'],
                        'updated_at' =>  date('Y-m-d H:i:s'),
                        )
                    );
    }
    public function updatePosition($id,$position)
    {
       return $this->where('id',$id)
                    ->update( array(
                        'position' =>  $position
                        )
                    );
    }
    public function deleteQuestion($id)
    {
       return $this->where('id', '=', $id)->delete();
    }

    function Questionslist($client_id, $form_id, $script_id,$state,$commodity){
        return $this->where([
            ['script_id', '=', $script_id],
            ['client_id', '=', $client_id],
            ['form_id', '=', $form_id],
            ['state', '=', $state],
            ['commodity', '=', $commodity],
        ])->orderBy('position', 'asc')->paginate(50);
    }
    function QuestionslistWithoutState($client_id, $form_id, $script_id){
        return $this->where([
            ['script_id', '=', $script_id],
            ['client_id', '=', $client_id],
            ['form_id', '=', $form_id]
        ])->orderBy('position', 'asc')->paginate(50);
    }

    public function getQuestionDetail($id)
    {
       return $this->where([
             ['id', '=', $id],
         ])->get();
    }
    public function getQuestion($client_id,$form_id,$script_id,$id)
    {
       return $this->where([
             ['client_id', '=', $client_id],
             ['form_id', '=', $form_id],
             ['script_id', '=', $script_id],
             ['id', '=', $id],
         ])->firstOrFail();
    }

    public function getNewPosition($client_id,$form_id,$script_id)
    {
       return $this->select('position')
       ->where([
             ['client_id', '=', $client_id],
             ['form_id', '=', $form_id],
             ['script_id', '=', $script_id],
            ])->orderBy('position', 'desc')
            ->first();
    }

    public function getScripts($workspace_id, $workflow_id, $language,$script_type = []){
        // echo DB::table('form_scripts')
        // ->join('clientsforms', 'clientsforms.id', '=', 'form_scripts.form_id')
        // ->select('form_scripts.id','form_scripts.scriptfor','form_scripts.form_id')
        // ->where('clientsforms.workspace_id', '=', $workspace_id)
        // ->where('clientsforms.workflow_id', '=', $workflow_id)
        // ->where('form_scripts.language', '=', $language)
        // ->when($script_type, function ($query) use ($script_type) {
        //     return $query->where('form_scripts.scriptfor', $script_type);
        // })
        // ->toSql();
        // return DB::table('form_scripts')
        // ->join('clientsforms', 'clientsforms.id', '=', 'form_scripts.form_id')
        // ->select('form_scripts.id','form_scripts.scriptfor','form_scripts.form_id')
        // ->where('clientsforms.workspace_id', '=', $workspace_id)
        // ->where('clientsforms.workflow_id', '=', $workflow_id)
        // ->where('form_scripts.language', '=', $language)
        // ->when($script_type, function ($query) use ($script_type) {
        //     return $query->where('form_scripts.scriptfor', $script_type);
        // })
        // ->get();

        if (!empty($script_type) && !is_array($script_type)) {
            $script_type = (array)$script_type;
        }

        $clientWorkflow = ClientWorkflow::where('client_twilio_workflowids.workspace_id', $workspace_id)->where('client_twilio_workflowids.workflow_id', $workflow_id)->first();

        return DB::table('form_scripts')
            ->select('form_scripts.id','form_scripts.scriptfor','form_scripts.form_id')
            ->join('clients', 'clients.id', '=', 'form_scripts.client_id')
            ->where('form_scripts.language', $language)
            ->whereIn('form_scripts.scriptfor', $script_type)
            ->where('form_scripts.form_id', 0)
            ->where('form_scripts.client_id', array_get($clientWorkflow, 'client_id'))
            ->get();

    }

    public function getSalesAgentScripts($language, $script_type = null){
        return DB::table('form_scripts')
        ->select('form_scripts.id','form_scripts.scriptfor','form_scripts.form_id')
        ->where('form_scripts.language', '=', $language)
        ->when($script_type, function ($query) use ($script_type) {
            return $query->where('form_scripts.scriptfor', $script_type);
        })
        ->get();

    }

    public function getIdentityVerificationScripts($language, $script_type = null, $clientId){
        return DB::table('form_scripts')
            ->select('form_scripts.id','form_scripts.scriptfor','form_scripts.form_id')
            ->where('form_scripts.language', '=', $language)
            ->where('form_scripts.form_id', '=', 0)
            ->where('form_scripts.client_id', $clientId)
            ->when($script_type, function ($query) use ($script_type) {
                return $query->where('form_scripts.scriptfor', $script_type);
            })
            ->get();

    }


    public function getScriptsUsingFormIDandLanguage($form_id, $language,$script_type = null, $state = null,$clientId=null){
        if ($script_type == "after_lead_decline" || $script_type == "closing") {
            $formId = 0;
        } else {
            $formId = $form_id;
        }
        
        $script = DB::table('form_scripts')
        ->select('form_scripts.id','form_scripts.scriptfor','form_scripts.form_id', 'form_scripts.language', 'form_scripts.state', 'form_scripts.client_id')
        ->where('form_scripts.language', '=', $language)
        ->where('form_scripts.form_id', '=', $formId)
        ->where('deleted_at','=',null)
        ->when($script_type, function ($query) use ($script_type) {
            return $query->where('form_scripts.scriptfor', '=', $script_type);
        })
        ->where(function ($que) use($state, $language, $formId, $script_type, $clientId) {
            $scriptTypeCon = "";
            if ($script_type != null) {
                $scriptTypeCon = " and scriptfor = '" . $script_type . "'";
            }

            $clientCon = "";
            if (!empty($clientId)) {
                $clientCon = " and client_id = " . $clientId;
            }

            $que->whereRaw(DB::raw("CASE WHEN(select count(id) from form_scripts where state = '".$state."' and language = '".$language."' and form_id = ".$formId." " . $scriptTypeCon . $clientCon . " ) > 0 then form_scripts.state ='".$state."' else form_scripts.state='ALL' end"));
            // $que->where('state', 'LIKE', "%$state%")
                // ->orWhere('state', 'ALL');
        });
        
        if (!empty($clientId)) {
            $script->where('form_scripts.client_id',$clientId);
        }
        return $script->get();

    }
    function scriptQuestions($script_id){
        return $this->where([
            ['script_id', '=', $script_id]
        ])
        ->select('id','question','positive_ans','negative_ans','answer','is_customizable','position')
        ->orderBy('position', 'asc')
        ->get();
    }
    
    function scriptQuestionsWithStateCommodity($script_id, $state, $commodity, $formId = null){
        return $this->where([
            ['script_id', '=', $script_id]
        ])
        ->when($formId, function ($query) use ($formId) {
            return $query->where('form_id', '=', $formId);
        })
        ->with(['script' => function($q) use($state, $formId) {
                if($state != 'ALL' && $state != null) {
                    $q->where('state', $state);
                }
        }])
        ->with(['questionConditions' => function($qu) {
            $qu->where('condition_type','question');
        }])
        ->select('id','question','positive_ans','negative_ans','answer','is_customizable','is_multiple', 'is_introductionary')
        ->orderBy('position', 'asc')
        ->get();
    }

    function clientUtilityProgramTagData($formid){
        return DB::table('clientsforms')
        ->leftJoin('utilities', 'utilities.id', '=', 'clientsforms.utility_id')
        ->leftJoin('clients', 'clients.id', '=', 'clientsforms.client_id')
        // ->leftJoin('programs', 'programs.id', '=', 'clientsforms.program_id','clients.name')
        ->select('utilities.utilityname as [Utility]','clients.name as [Client]','utilities.commodity as [Utility Type]', 'utilities.market as [MarketCode]' )
        ->where('clientsforms.id', '=', $formid)
        ->get()->toArray();
        /*,'programs.name as [Program]'*/
    }

    public function script() {
        return $this->belongsTo('App\models\FormScripts', 'script_id');
    }

    public function questionConditions()
    {
        return $this->hasMany('App\models\Scriptquestionsconditions', 'question_id');
    }


}
