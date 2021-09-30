<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Salesagentdetail extends Model
{
    use SoftDeletes;
    
    protected $table = 'salesagent_detail';
    protected $fillable = [
        'user_id',
        'external_id',
        'passed_state_test',
        'state', 
        'certified',
        'codeofconduct',
        'backgroundcheck',
        'drugtest',
        'certification_date',
        'certification_exp_date',
        'added_by',
        'location_id',
        'agent_type',
        'created_at',
        'updated_at',
        'phone_number',
        'restrict_state'
    ];

    protected $dates = ['deleted_at'];

    public function getUserDetail($user_id){
        return $this->where('user_id',$user_id)->first();
    }
    public function createorupdate($user_id,$data){
     $checkuser =  $this->getUserDetail($user_id);
     if( $checkuser ){
        return $this -> where('user_id',$user_id)
        ->update( $data  );
     }else{
         $data['user_id'] = $user_id;
        return $this->insertGetId(
            [ 
                $data
                ]
        );
     }

      return   $this->updateOrCreate($data , ['user_id' => $user_id] );

    }

    public function stateTraining($params = array()){

        $whereArray = array();
        $export = 0;
        
        $whereArray[] = array(
                'salesagent.access_level','=','salesagent'
        );

         

        if( isset($params['client_id'])){
            $whereArray[] = array(
                'salesagent.client_id','=',$params['client_id']
            );

        } 
        if( isset($params['salecenter'])){
            $whereArray[] = array(
                'salesagent.salescenter_id','=',$params['salecenter']
            );

        } 
        if( isset($params['location'])){
            $whereArray[] = array(
                'salesagent.location_id','=',$params['location']
            );

        } 
        if( isset($params['certified'])){
            $whereArray[] = array(
                'salesagent_detail.certified','=',$params['certified']
            );

        } 
        if( isset($params['passed_state_test'])){
            $whereArray[] = array(
                'salesagent_detail.passed_state_test','=',$params['passed_state_test']
            );

        } 
        if( isset($params['state'])){
            $whereArray[] = array(
                'salesagent_detail.state','=',$params['state']
            );

        }
        if( isset($params['export'])){
            $export = 1;

        }


        $query =  DB::table('users as salesagent')->select(         
        DB::raw( "'Intersoft' as VendorName") ,
        DB::raw( "salescenterslocations.name as OfficeName") , 
        DB::raw( "'' as SparkAgentId") ,
        DB::raw( "salesagent.userid as AgentId") ,
        DB::raw( "salesagent.first_name as FirstName") ,
        DB::raw( "salesagent.last_name as LastName") ,
        DB::raw( "CASE 
        WHEN  salesagent_detail.certified = '1' THEN 'Yes' 
        WHEN  salesagent_detail.certified = '0' THEN 'No' 
        else
        salesagent_detail.certified end as Certified ")  ,
        DB::raw( "salesagent_detail.state as State") ,
        DB::raw( "salesagent_detail.codeofconduct as CodeofConduct") ,
        DB::raw( "CASE 
        WHEN  salesagent_detail.backgroundcheck = '1' THEN 'Yes' 
        WHEN  salesagent_detail.backgroundcheck = '0' THEN 'No' 
        else
        salesagent_detail.backgroundcheck end as BackgroundCheck ") ,
        DB::raw( "CASE 
        WHEN  salesagent_detail.drugtest = '1' THEN 'Yes' 
        WHEN  salesagent_detail.drugtest = '0' THEN 'No' 
        else
        salesagent_detail.drugtest end as DrugTest ") ,
        DB::raw( "DATE_FORMAT(salesagent_detail.certification_date,'%m/%d/%Y') as CertificationDate")  
         
 
        )
        ->leftJoin('salesagent_detail', 'salesagent.id', '=', 'salesagent_detail.user_id')
        ->leftJoin('clients', 'clients.id', '=', 'salesagent.client_id')
        ->leftJoin('salescenterslocations', 'salescenterslocations.id', '=', 'salesagent.location_id') ;
 
         $query->where($whereArray);
        
         $query->orderBy('salesagent.first_name', 'asc');
        if($export==1){
           return  $results = $query->get();  
         }else{
           $results = $query->paginate(20);  
         }
        return $results;
    }

    public function location() {
        return $this->belongsTo('App\models\Salescenterslocations', 'location_id');
    }

    public function locationWithTrashed() {
        return $this->belongsTo('App\models\Salescenterslocations', 'location_id')->withTrashed();
    }

}
