<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Zipcodes extends Model
{
    protected $primarykey = 'id';
    protected $table = 'zip_codes';
    public $timestamps = false;
    protected $fillable =['zipcode', 'city', 'county', 'state','lat','lng'];
    public $tableFields =['zipcode', 'city', 'county', 'state','commodity_type'];


    public function utilities() {
        return $this->belongsToMany("App\models\Utilities","utility_zipcodes","zipcode_id","utility_id");
    }

    public function FindZipAutocomplete($zipcode){
        return  $this->select('zipcode')->where('zipcode', 'like', "%".$zipcode."%") 
        ->limit(5)
        ->get(); 
    }
    public function FindZipid($zipcode){
        return  $this->select('id')->where('zipcode', '=', $zipcode)->first(); 
    }
    public function FindZip($zipcode){
        return  $this->where('zipcode', '=', $zipcode)->first(); 
    }
    public function getzipcodeId($zipdata)
    {
        $wheredata = array();
        if( isset($zipdata['state'])  && $zipdata['state']!=""){
            $wheredata['state'] =   $zipdata['state'];
        }
        if( isset($zipdata['zipcode'])  && $zipdata['zipcode']!=""){
            if( strlen($zipdata['zipcode']) == 4)
            {
                $zipdata['zipcode'] = "0".$zipdata['zipcode'];
            }
            if( strlen($zipdata['zipcode']) == 3)
            {
                $zipdata['zipcode'] = "00".$zipdata['zipcode'];
            }
            $wheredata['zipcode'] =  $zipdata['zipcode'];
        }



        $response = $this->where($wheredata)->get();
        if(count($response) > 0){
            $id =  $response[0]->id;
            if($zipdata['city'] !==  $response[0]->city ){
                $this->where('id', $id )
                ->update([ 'city' => $zipdata['city'] ]);
            }
        }else{
            $id = $this->saveZipcode($zipdata); 
        }




        return $id;
    }

    public function saveZipcode($data){
        return $this->insertGetId($data);
    }

    public function getStates($clientid){
        // echo DB::table('zip_codes')
        // ->join('utility_zipcodes', 'zip_codes.id', '=', 'utility_zipcodes.zipcode_id')
        // ->join('utilities', 'utilities.id', '=', 'utility_zipcodes.utility_id')
        // ->join('programs', 'programs.utility_id', '=', 'utilities.id')
        // ->select(DB::raw('distinct zip_codes.state'))
        // ->where('utilities.client_id', '=', $clientid) 
        // ->orderBy('zip_codes.state', 'asc') 
        // ->toSql();
        return DB::table('zip_codes')
        ->join('utility_zipcodes', 'zip_codes.id', '=', 'utility_zipcodes.zipcode_id')
        ->join('utilities', 'utilities.id', '=', 'utility_zipcodes.utility_id')
        ->join('programs', 'programs.utility_id', '=', 'utilities.id')
        ->select(DB::raw('distinct zip_codes.state'))
        ->where('utilities.client_id', '=', $clientid) 
        ->orderBy('zip_codes.state', 'asc') 
        ->get();

    } 
    public function searchzip($string,$string1)
    {
        return $this->select('zipcode','state', 'city')
        ->where([['zipcode', 'like' ,$string.'%'],
        ])
        ->whereNotIn('state',$string1)->orderBy('zipcode','asc')
        ->limit(10)->get();
    }
}
