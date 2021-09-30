<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class TelesalesTmp extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'client_id', 'form_id', 'created_at','updated_at','user_id','reviewed_by','decline_reason','status','parent_id','cloned_by','verification_number', 'call_duration', 'is_multiple', 'multiple_parent_id', 'refrence_id','gps_location_image','program','zipcode','is_proceed', 'is_enrollment_by_state', 'salesagent_lat', 'salesagent_lng'
    ];

    protected $primarykey = 'id';
    protected $table = 'telesales_tmp';

    public function teleSalesData()
    {
        return $this->hasMany('App\models\TelesalesdataTmp', 'telesaletmp_id');
    }
    public function getLatestRefrenceIdByClient($clientId){
        return $this->select('refrence_id','id')->where('client_id',$clientId)->latest()->withTrashed()->first();
    }
    public function getLeadID($refrence_id)
    {
        $params =array(
              array(
                'refrence_id', '=', $refrence_id
              )
         );
       return $this->select('id')->where($params)->first();
    }
    public function generateReferenceId() {
        $newId = (new TelesalesTmp)->nextAutoID();

        if ($newId != "") {
            $referenceId = str_pad($newId, 10, 0, STR_PAD_LEFT);
        } else {
            $referenceId = time();
        }

        return  $referenceId;
    }

    //This function generates a new lead refrence id by adding client prefix from telesales tmp table
    public function generateNewReferenceId($clientId,$prefix) {
        //if client prefix is not null then generate refrence id as per client prefix
        if($prefix != null){
            $newId = (new TelesalesTmp())->getLatestRefrenceIdByClient($clientId);
            $len = strlen($prefix); 
            if(!empty($newId)){
                $newId = $newId->refrence_id;
                //check if last refrence id's prefix is same as current client prefix if yes then increment last refrence id and generate a new lead refrence id for client
                if(substr($newId, 0, $len) == $prefix){
                    // $newId = $newId+1;
                    $referenceId = str_pad($newId+1, 10, 0, STR_PAD_LEFT);
                }
                //if last refrence id's prefix is not same as current client prefix then    generate a refrence id by adding new client prefix.
                else{
                    $newId = substr($newId,$len);
                    $referenceId = $prefix.str_pad($newId+1, 10, 0, STR_PAD_LEFT);
                }
            }
            //if client has no previous leads created then generate a new refrence id starting from 1 for that client
            else{
                $newId = 1;
                $referenceId = $prefix.str_pad($newId, 10, 0, STR_PAD_LEFT);
            }          
            //check whether newly generated refrence id is exist in database or not
            $existsRefrenceId = TelesalesTmp::where('refrence_id',$referenceId)->withTrashed()->exists();
            if($existsRefrenceId){
                $referenceId = $prefix.$this->generateReferenceId();
            }          
        }
        //else generate refrence id as per previously generated
        else{
            $referenceId = $this->generateReferenceId();
        }
        
        return  $referenceId;
    }

    public function validateConfirmationNumber($verification_number){
        return $this->select('id' )
        ->where('verification_number', '=',$verification_number)
        ->first();
    }

    public function nextAutoID()
    {
        $statement = DB::select("SHOW TABLE STATUS LIKE '".$this->table."'");
        $nextId = $statement[0]->Auto_increment;
        return $nextId;
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function($telesales)
        {
            \Log::info("Deleting telesales tmp related data...");
            $telesales->teleSalesData()->delete();
        });
    }
}
