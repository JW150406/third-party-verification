<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ClientWorkspace extends Model
{
    protected $table = 'client_twilio_workspace';    
    protected $fillable = ['client_id','workspace_id','workspace_name'];
    public $timestamps = false;

    public function getClientWorkspaceIds($client_id){
        return $this->where([
                ['client_id', '=', $client_id],
            ])->get();
     }
     public function deleteIds($client_id){
        return $this->where([
            ['client_id', '=', $client_id],
        ])->delete();
    }
    public function addnew($client_id,$workspaceid,$workspace_name  ){
         return $this->create([
            'client_id' => $client_id,
            'workspace_id' => $workspaceid,
            'workspace_name' => $workspace_name,
            ]        
        );
    }
    public function getClientUsingWorkspaceID($workspace_id){
        $info =  $this->where([
          ['workspace_id', '=', $workspace_id]                                
        ])->get();
        return  $info[0];
     }
     public function getallWorkspaceIds(){
        return $this->orderBy('workspace_name', 'asc')->groupBy('workspace_id')->get();
     }

}
