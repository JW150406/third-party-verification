<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;
class ClientTwilioNumbers extends Model
{
    use SoftDeletes;
    protected $table = 'client_twilio_numbers';
    protected $fillable = [
        'phonenumber', 'client_workflowid', 'client_id','added_by','type'
    ];
    public $timestamps = false;

    public function workflow()
    {
      return $this->belongsTo('App\models\ClientWorkflow', 'client_workflowid');
    }
    public function deleteNumbers($client_id){
        return $this->where([
            ['client_id', '=', $client_id],
        ])->delete();
    }
    public function createNumber($data)
    {
       return $this->insertGetId(
           [
               'client_id' => $data['client_id'],
               'added_by' => $data['added_by'],
               'phonenumber' => $data['phonenumber'],
               'client_workflowid' => $data['client_workflowid'],
          ]
       );
    }
    public function getNumbers($client_id){
        return $this->select('client_twilio_numbers.phonenumber','client_twilio_numbers.id','client_twilio_workflowids.workflow_id','client_twilio_workflowids.workflow_name')
             ->leftJoin('client_twilio_workflowids', 'client_twilio_workflowids.id', '=', 'client_twilio_numbers.client_workflowid')
              ->where([
                ['client_twilio_numbers.client_id', '=', $client_id],
            ])->get();
     }
    public function getNumberAndWorkflow($client_id){
        return $this->select('client_twilio_numbers.phonenumber','client_twilio_numbers.id','client_twilio_workflowids.workflow_id','client_twilio_workflowids.workflow_name')
             ->leftJoin('client_twilio_workflowids', 'client_twilio_workflowids.id', '=', 'client_twilio_numbers.client_workflowid')
              ->where([
                ['client_twilio_numbers.client_id', '=', $client_id],
            ])->first();
    }
     public function getWorkflowNumbers($client_id,$workflowid){
        return $this->select('phonenumber')->where([
            ['client_id', '=', $client_id],
            ['client_workflowid', '=', $workflowid],
            ['type', '=', 'customer_verification'],
        ])->get();
     }
     public function getWorkflowNumbersbyworkflowid($workflowid){
        return DB::table('client_twilio_numbers')
        ->leftJoin('client_twilio_workflowids', 'client_twilio_workflowids.id', '=', 'client_twilio_numbers.client_workflowid')

        ->select('client_twilio_numbers.phonenumber' )
        ->where('client_twilio_workflowids.workflow_id', '=', $workflowid)
        ->get()->toArray();
     }
    public function getNumber($client_id){
        return $this->select('client_twilio_numbers.phonenumber')
            ->where([['client_twilio_numbers.client_id', '=', $client_id],
            ])->where('client_twilio_numbers.type','customer_call_in_verification')->first();
    }


}
