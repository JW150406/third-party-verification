<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CallAnswers extends Model
{

    use SoftDeletes;

    protected $table = 'call_answers';

    protected $fillable = [
        'lead_id','form_id','client_id','tpv_agent_id','sales_agent_id','language','question','answer','verification_answer','custom_answer_checked','orignal_answer'
    ];
    protected $dates = ['deleted_at'];
    
    public function deleteAnswers($lead_id)
    {
       return $this->where('lead_id', '=', $lead_id)
            ->delete();
    }

    public function InsertAnswer($data)
    {
        
       return $this->insertGetId(
           [
               'client_id' => $data['client_id'],
               'form_id' => $data['form_id'],                
               'lead_id' =>  $data['lead_id'],
               'tpv_agent_id' =>  $data['tpv_agent_id'],
               'sales_agent_id' =>  $data['sales_agent_id'],
               'language' =>  $data['language'],
               'question' =>  $data['question'],
               'answer' =>  $data['answer'],
               'verification_answer' =>  $data['verification_answer'],
               /*'custom_answer_checked' =>  $data['custom_answer_checked'],
               'orignal_answer' =>  $data['orignal_answer'],*/
               'created_at' =>  date('Y-m-d H:i:s'),
               'updated_at' =>  date('Y-m-d H:i:s'),
          ]
       );
    }
}
