<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class UserTwilioId extends Model
{
    use SoftDeletes;
    
    protected $table = 'user_twilio_id';//    
    protected $fillable = ['user_id', 'twilio_id','workspace_id','workflow_id'];
    public $timestamps = false;
  
     public function getTwilioIds($userid){
        return    DB::table('user_twilio_id')
        ->join('client_twilio_workflowids', 'client_twilio_workflowids.workflow_id', '=', 'user_twilio_id.workflow_id')
        ->where([
            ['user_twilio_id.user_id', '=', $userid],
        ])

        ->select('user_twilio_id.*', 'client_twilio_workflowids.workflow_name')
        ->get();

      
     }
     public function getAssignedClients($userid){
        // return    DB::table('user_twilio_id')
        // ->join('client_twilio_workspace', 'client_twilio_workspace.workspace_id', '=', 'user_twilio_id.workspace_id')
        // ->join('clients', 'clients.id', '=', 'client_twilio_workspace.client_id')
        // ->where([
        //     ['user_twilio_id.user_id', '=', $userid],
        // ])

        // ->select('user_twilio_id.workspace_id','clients.name','client_twilio_workspace.client_id')
        // ->distinct('user_twilio_id.workspace_id')
        // ->orderBy('clients.name', 'asc')
        // ->get();

        return    DB::table('user_twilio_id')
        ->join('client_twilio_workflowids', 'client_twilio_workflowids.workflow_id', '=', 'user_twilio_id.workflow_id')
        ->join('clients', 'clients.id', '=', 'client_twilio_workflowids.client_id')
        ->join('client_twilio_workspace', 'client_twilio_workspace.workspace_id', '=', 'client_twilio_workflowids.workspace_id')
        ->where([
            ['user_twilio_id.user_id', '=', $userid],
        ])
        ->whereNull('user_twilio_id.deleted_at')
        ->select('user_twilio_id.workflow_id','clients.name','client_twilio_workflowids.client_id', 'client_twilio_workspace.workspace_id')
        ->distinct('user_twilio_id.workflow_id')
        ->orderBy('clients.name', 'asc')
        ->get();
        
     }
     public function deleteIds($userid){
        return $this->where([
            ['user_id', '=', $userid],
        ])->delete();
    }
    public function addnew($userid,$workspaceid,$workerid  ){
         return $this->create([
            'user_id' => $userid,
            'twilio_id' => $workerid ,
            'workspace_id' => $workspaceid
            ]        
        );
    }

}
