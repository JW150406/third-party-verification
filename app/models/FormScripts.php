<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormScripts extends Model
{
    use SoftDeletes;
    
    protected $table = 'form_scripts';

    public $languages = array(
        'en' => 'English',
        'es' => 'Spanish',
    );
    public $script_for = array(
        'salesagentintro' => 'Sales agent intro/verification',
        'leadcreation' => 'Lead creation',
        'customer_verification' => 'Customer verification',
        // 'agent_not_found' => 'Agent not found',
        'closing' => 'Closing',
        'after_lead_decline' => 'After lead decline'
    );

    public function createScript($data)
    {
       return $this->insertGetId(
           [
               'client_id' => $data['client_id'],
               'form_id' => $data['form_id'],
               'created_by' => $data['created_by'],
               'title' =>  $data['title'],
               'language' =>  $data['language'],
               'scriptfor' =>  $data['scriptfor'],
               'created_at' =>  date('Y-m-d H:i:s'),
          ]
       );
    }
    public function updateScript($id,$data)
    {
       return $this->where('id',$id)
                    ->update( array(
                        'title' =>  $data['title'],
                        'scriptfor' =>  $data['scriptfor'],
                        'updated_at' =>  date('Y-m-d H:i:s'),
                        )
                    );
    }
    public function deleteScript($id)
    {
       return $this->where('id', '=', $id)->delete();
    }

    function scripts_list($formid,$language= null){
        return $this->where([
            ['form_id', '=', $formid],
        ])->when($language, function ($query) use ($language) {
            return $query->where('language', $language);
        })->paginate(20);
    }
    function get_script_id($formid,$language= 'en'){
        return $this->where([
            ['form_id', '=', $formid],
            ['scriptfor', '=', 'leadcreation'],
        ])->when($language, function ($query) use ($language) {
            return $query->where('language', $language);
        })->first();
    }

    public function getScriptDetail($id)
    {
       return $this->where([
             ['id', '=', $id],
         ])->get();
    }
    public function getScript($client_id,$form_id,$id)
    {
       return $this->where([
             ['client_id', '=', $client_id],
             ['form_id', '=', $form_id],
             ['id', '=', $id],
         ])->firstOrFail();
    }

    public function form()
    {
      return $this->belongsTo('App\models\Clientsforms', 'form_id');
    }

    public function questions()
    {
      return $this->hasMany('App\models\ScriptQuestions', 'script_id');
    }
}
