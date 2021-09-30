<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ClientAgentNotFoundScripts extends Model
{
    protected $table = 'client_agent_not_found_scripts';//    


    public function getQuestions($client_id){
        return   $this->where('client_id', '=', $client_id)->orderBy('position', 'asc')->get();      
    }
    public function getQuestionsByClientIdAndLanguage($client_id,$language){
        return   $this->select('question')->where('client_id', '=', $client_id)->where('language', '=', $language)->orderBy('position', 'asc')->get();      
    }
    public function createQuestion($data)
    {   
       return $this->insertGetId(
           [ 
               'client_id' => $data['client_id'], 
               'created_by' => $data['created_by'],
               'question' =>  $data['question'],
               'language' =>  $data['language'],
               'position' =>  $data['position'],
               'created_at' =>  date('Y-m-d H:i:s'),
          ]
       );
    } 
    public function updateQuestion($id,$data)
    {   
       return $this->where('id',$id)
                    ->update( array(                   
                        'question' =>  $data['question'],
                        'updated_at' =>  date('Y-m-d H:i:s'),
                        )            
                    );
    } 

    public function getNewPosition($client_id)
    {     
       return $this->select('position')
       ->where([
             ['client_id', '=', $client_id],
            ])->orderBy('position', 'desc')
            ->first();
    } 
    public function deleteQuestions($client_id)
    {   
       return $this->where('client_id', '=', $client_id)->delete();
    } 

}
