<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientWorkflow extends Model
{
    use SoftDeletes;

    protected $table = 'client_twilio_workflowids';    
    protected $fillable = ['client_id','workspace_id','workflow_id','workflow_name'];
    public $timestamps = false;

    public function client()
    {
      return $this->belongsTo('App\models\Client', 'client_id');
    }

    public function getClientWorkflowIds($client_id){
        return $this->where([
                ['client_id', '=', $client_id],
            ])->get();
     }
     public function getid($client_id,$workflow_id){
        return $this->select('id')->where([
                ['client_id', '=', $client_id],
                ['workflow_id', '=', $workflow_id],
            ])->get();
     }
     public function getClientWorkflowIdsUsingWorkspaceID($workspace_id){
        return $this->where([
                ['workspace_id', '=', $workspace_id],
            ])->get();
     }
     public function deleteIds($client_id){
        return $this->where([
            ['client_id', '=', $client_id],
        ])->delete();
    }
    public function addnew($client_id,$workspaceid,$workflow_id,$workflow_name  ){
         return $this->create([
            'client_id' => $client_id,
            'workspace_id' => $workspaceid,
            'workflow_id' => $workflow_id,
            'workflow_name' => $workflow_name,
            ]        
        );
    }
    public function getClientAndWorkspaceIDUsingWorkflowID($workflow_id){
        $info =  $this->where([
          ['workflow_id', '=', $workflow_id]                                
        ])->get();
        return  $info[0];
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function($workflow)
        {
            \Log::info("Deleting workflow related data...");
            UserTwilioId::where('workflow_id',$workflow->workflow_id)->delete();
        });
    }
      

}
