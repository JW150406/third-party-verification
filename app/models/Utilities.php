<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Utilities extends Model
{
    use SoftDeletes;
    
    protected $primarykey = 'id';
    protected $table = 'utilities';
    protected $fillable = ['company', 'commodity','utilityname','client_id', 'created_by', 'created_at', 'updated_at','market', 'commodity_type','fullname' ,'commodity_id','regex','regex_message','brand_id', 'act_num_verbiage'];

    protected $dates = ['deleted_at'];

    public $tableFields =  ['company', 'commodity','utilityname','market', 'commodity_type','fullname'];

    public function utilityZipcodes()
    {
        return $this->hasMany('App\models\UtilityZipcodes','utility_id');
    }

    public function validations()
    {
        return $this->hasMany('App\models\UtilityValidation','utility_id');
    }

    public function utilityCommodity()
    {
        return $this->belongsTo('App\models\Commodity','commodity_id');
    }

    public function getUtilities($params = array())
    {
       return $this->leftjoin('brand_contacts','brand_contacts.id','=','utilities.brand_id')->select('id','brand_contacts.name as utilityname','commodity','market','fullname')->where($params)->orderBy('brand_contacts.name','asc')->get();
    }

    public function scopeGetUtilityByCommodity($query, $commodityId, $zipcodeIds, $programIds=[])
    {
        $utility = $query->leftjoin('brand_contacts','brand_contacts.id','=','utilities.brand_id')
                ->leftjoin('salescenters_brands','salescenters_brands.brand_id','=','brand_contacts.id')
                ->select('utilities.id as utid','utilityname','market','brand_contacts.name','commodity_id', 
                    DB::raw('concat( CASE 
                        WHEN fullname IS NULL  
                        THEN market
                        ELSE fullname
                        END , " (", name, ")")as fullname'))
                ->where('salescenters_brands.salescenter_id',auth()->user()->salescenter_id)
                ->where('commodity_id',$commodityId)
                ->whereHas('utilityZipcodes', function($q) use ($zipcodeIds) {
                    $q->whereIn('zipcode_id', $zipcodeIds);
                })->whereHas('programs', function($q) use ($programIds){
                    $q->where('status','active');
                    if (!empty($programIds)) {
                        $q->whereIn('id',$programIds);
                    }
                })->get();
        return $utility;
    }

    public function scopeGetUtilityByCommodityAndMapping($query, $commodityId, $zipcodeIds, $programIds=[])
    {
        $mapped_query = clone $query;
        $utility = $query->leftjoin('brand_contacts','brand_contacts.id','=','utilities.brand_id')
                ->leftjoin('salescenters_brands','salescenters_brands.brand_id','=','brand_contacts.id')
                ->select('utilities.id as utid','utilityname','market','brand_contacts.name','commodity_id', 
                    DB::raw('concat( CASE 
                        WHEN fullname IS NULL  
                        THEN market
                        ELSE fullname
                        END , " (", name, ")")as fullname'))
                ->where('salescenters_brands.salescenter_id',auth()->user()->salescenter_id)
                ->where('commodity_id',$commodityId)
                ->whereHas('utilityZipcodes', function($q) use ($zipcodeIds) {
                    $q->whereIn('zipcode_id', $zipcodeIds);
                })->whereHas('programs', function($q) use ($programIds){
                    $q->where('status','active');
                    if (!empty($programIds)) {
                        $q->whereIn('id',$programIds);
                    }
                })->get();

        if (!empty($utility)/* && is_array($utility)*/){
            foreach ($utility as $key => $each_utility) {
                $mapped_queryed = clone $mapped_query;
                $mapped_utility = $mapped_queryed
                ->leftjoin('brand_contacts','brand_contacts.id','=','utilities.brand_id')
                ->leftjoin('salescenters_brands','salescenters_brands.brand_id','=','brand_contacts.id')
                ->leftjoin('utility_mappings','utility_mappings.utility_id','=','utilities.id')
                ->select('utilities.id as utid','utilities.utilityname','utilities.market','brand_contacts.name','utilities.commodity_id', 
                    DB::raw('concat( CASE 
                        WHEN fullname IS NULL  
                        THEN market
                        ELSE fullname
                        END , " (", name, ")")as fullname'))
                ->where('salescenters_brands.salescenter_id',auth()->user()->salescenter_id)
                // ->where('utility_mappings.mapped_utility_id',$each_utility->utid)
                ->whereRaw("find_in_set('".$each_utility->utid."',utility_mappings.mapped_utility_id)")
                ->whereHas('utilityZipcodes', function($q) use ($zipcodeIds) {
                    $q->whereIn('zipcode_id', $zipcodeIds);
                })->whereHas('programs', function($q) use ($programIds){
                    $q->where('status','active');
                    if (!empty($programIds)) {
                        $q->whereIn('id',$programIds);
                    }
                })->get();

                $utility[$key]['mapped_utility']= $mapped_utility;
            }

        }
        return $utility;
    }
    public function getClientUtilities($client_id = null, $search_text = null )
    {
        if(!empty($client_id)){
            $params =array(
                array(
                  'client_id', '=', $client_id
                )
           );
        }else{
            $params =array();
        }
        return $this->select('utilities.*', DB::raw('(SELECT GROUP_CONCAT(zc.zipcode SEPARATOR ", ") FROM utility_zipcodes as uz inner join  zip_codes as zc on zc.id = uz.zipcode_id
        where  uz.utility_id = utilities.id) as zip'))
        ->leftjoin('brand_contacts','brand_contacts.id','=','utilities.brand_id')
        ->leftJoin('utility_zipcodes', 'utility_zipcodes.utility_id', '=', 'utilities.id')
        ->leftJoin('zip_codes', 'zip_codes.id', '=', 'utility_zipcodes.zipcode_id')
        ->where($params)
           ->when($search_text, function ($query, $search_text) {
            $query->whereRaw(" ( zip_codes.zipcode like '%$search_text%' or  brand_contacts.name like '%$search_text%'  or  utilities.market like '%$search_text%'  or zip_codes.city like '%$search_text%' or utilities.company like '%$search_text%' ) ");          
             
          })
          ->orderBy('brand_contacts.name','asc')->groupBy('utilities.id')
             ->paginate(20);
    }
    public function getClientAllUtilities($client_id = null)
    {
        if(!empty($client_id)){
            $params =array(
                array(
                  'utilities.client_id', '=', $client_id
                )
           );
        }else{
            $params =array();
        }
       
        return $this->leftjoin('brand_contacts','brand_contacts.id','=','utilities.brand_id')->select('utilities.id','brand_contacts.name as utilityname','utilities.commodity', 'utilities.market', DB::raw('(SELECT group_concat(distinct zc.zipcode ," ")  FROM utility_zipcodes as uz inner join  zip_codes as zc on zc.id = uz.zipcode_id
where  uz.utility_id = utilities.id ) as zip'), DB::raw('(SELECT group_concat( distinct zc.state ," ") FROM utility_zipcodes as us inner join  zip_codes as zc on zc.id = us.zipcode_id
where  us.utility_id = utilities.id ) as zipstate'))->where($params)->orderBy('brand_contacts.name','asc')
             ->get();
    }

    public function saveUtility($data){
        return $this->insertGetId($data);
     }
     
     public function getUtilityByAttributes($utility_data)
     {
      if( isset($utility_data['commodity'])  && $utility_data['commodity']!="" && strtolower($utility_data['commodity']) == strtolower('NaturalGas')){
         $utility_data['commodity'] =   'Gas';
      }
         $wheredata = array();
         if( isset($utility_data['commodity'])  && $utility_data['commodity']!=""){
            $wheredata['commodity'] =   $utility_data['commodity'];
         }
         if( isset($utility_data['utilityname'])  && $utility_data['utilityname']!=""){
            $wheredata['utilityname'] =  $utility_data['utilityname'];
         }
         if( isset($utility_data['client_id'])  && $utility_data['client_id']!=""){
            $wheredata['client_id'] =    $utility_data['client_id'] ;
         }
         if( isset($utility_data['market'])  && $utility_data['market']!=""){
            $wheredata['market'] =   $utility_data['market'] ;
         }
         if( isset($utility_data['commodity_type'])  && $utility_data['commodity_type']!=""){
            $wheredata['commodity_type'] =   $utility_data['commodity_type'] ;
         }
         
          $response = $this->where($wheredata)->get();
          if(count($response) > 0){
             $id =  $response[0]->id;
          }else{
            $id = $this->saveUtility($utility_data); 
          } 
          return $id;
     }


     public function getUtility($id)
     {
          $response = $this->where([ ['id', '=', $id]])->get();
          return $response[0];
     }
     public function updateUtility($id,$input){
        return $this -> where('id',$id)
        ->update( $input );
    }
    public function deleteUtility($id){
        $this -> where('id',$id)
        ->delete();
    }
    public function GetDistinctNames(){
       return   $this->distinct()->select('utilityname')->get();
      }

    public function programs()
    {
        return $this->hasMany(Programs::class,'utility_id');
    }

    public function brandContacts()
    {
        return $this->belongsTo('App\models\Brandcontacts','brand_id');
    }

    public function mapppings()
    {
        return $this->hasMany('App\models\UtilityMapping','utility_id');
    }


}
