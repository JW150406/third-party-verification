<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class UserAssignedForms extends Model
{
    protected $table = 'user_assigned_forms';    
    protected $fillable = ['client_id','user_id','form_id'];
    public $timestamps = false;

    
     public function getAssignedForm($userid){
        return $this->where([
                ['user_id', '=', $userid],
            ])->first();
     }
     public function deleteIds($userid){
        return $this->where([
            ['user_id', '=', $userid],
        ])->delete();
    }
    public function addnew($client_id,$user_id,$form_id){
         return $this->create([
            'client_id' => $client_id,
            'user_id' => $user_id,
            'form_id' => $form_id,         
            ]        
        );
    }
     
}
