<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use App\models\CustomerType;

class Programs extends Model
{
    use SoftDeletes;
    
     protected $table = 'programs';
     protected $fillable = ['name', 'code', 'rate',  'etf',  'msf',  'term', 'utility_id', 'client_id', 'created_by', 'created_at', 'updated_at', 'dailyrate', 'producttype', 'termtype', 'customer_type','customer_type_id',  'unit_of_measure', 'saleschannels', 'webbroker', 'product_filters','cis_system','isdefaulttieredeverGreen','istierpricing','isshell','current_selling_product','product_id','state','accountnumberlength','accountnumbertype','custom_field_1','custom_field_2','custom_field_3','custom_field_4','custom_field_5'];
     
     public $tableFields =  ['name', 'code', 'rate',  'etf',  'msf',  'term', 'dailyrate', 'producttype', 'termtype', 'unit_of_measure', 'customer_type', 'saleschannels', 'product_filters','webbroker','cis_system','isdefaulttieredeverGreen','istierpricing','isshell','current_selling_product','product_id','state','accountnumberlength','accountnumbertype','commodity_type' ];

    public function utility()
    {
        return $this->belongsTo('App\models\Utilities','utility_id');
    }
     public function getPrograms($client_id = null,$utility_id=null){
        $params =array();
        if(!empty($client_id)){
            $params[] = array(
                  'client_id', '=', $client_id
            );
        }
        if(!empty($utility_id)){
            $params[] = array(
                  'utility_id', '=', $utility_id
            );
        }
        return $this->where($params)->orderBy('name','asc')
             ->paginate(20);
     }

     public function deleteProgram($id){
        $this -> where('id',$id)
        ->delete();
    }

    public function getAllPrograms($client_id = null,$utility_id=null){
        $params =array();
        if(!empty($client_id)){
            $params[] = array(
                  'client_id', '=', $client_id
            );
        }
        if(!empty($utility_id)){
            $params[] = array(
                  'utility_id', '=', $utility_id
            );
        }
        return $this->where($params)->where('status','active')->orderBy('name','asc')
             ->get();
     }
    public function getAllProgramsByUtility($utility_id=null, $restrictProg = []){
      $params =array();
      
      if(!empty($utility_id)){
          $params[] = array(
                'programs.utility_id', '=', $utility_id
          );
      }

      $results =$this->select
        (
      'programs.id',
      'programs.utility_id',
        DB::raw("CASE 
        WHEN programs.code IS NULL  
        THEN ''
        ELSE programs.code
        END as ProgramCode"),
        
        DB::raw("CASE 
            WHEN programs.name IS NULL  
            THEN ''
            ELSE programs.name
        END as ProgramName"),
     
        DB::raw("CASE 
            WHEN programs.saleschannels IS NULL  
            THEN ''
            ELSE programs.saleschannels
        END as Saleschannel"),
        
        DB::raw("CASE 
            WHEN programs.msf IS NULL  
            THEN 0
            ELSE programs.msf
        END as monthlysf"),
        DB::raw("CASE 
            WHEN programs.etf IS NULL  
            THEN 0
            ELSE programs.etf
        END as earlyterminationfee"), 
        DB::raw("CASE 
            WHEN programs.rate IS NULL  
            THEN 0
            ELSE programs.rate
        END as Rate"), 
       DB::raw("CASE 
            WHEN programs.term IS NULL  
            THEN 0
            ELSE programs.term
        END as Term"), 
        DB::raw("CASE 
            WHEN programs.state IS NULL  
            THEN ''
            ELSE programs.state
        END as State"), 
        DB::raw("CASE 
            WHEN programs.unit_of_measure IS NULL  
            THEN ''
            ELSE programs.unit_of_measure
        END as UnitOfMeasureName"), 
        DB::raw("CASE 
            WHEN customer_types.name IS NULL  
            THEN ''
            ELSE TRIM(customer_types.name)
        END as PremiseTypeName"), 
        DB::raw("CASE 
            WHEN programs.accountnumbertype IS NULL  
            THEN ''
            ELSE programs.accountnumbertype
        END as AccountNumberTypeName"), 
        DB::raw("CASE 
            WHEN programs.accountnumberlength IS NULL  
            THEN ''
            ELSE programs.accountnumberlength
        END as AccountNumberLength"),
        DB::raw("CASE 
            WHEN programs.custom_field_1 IS NULL  
            THEN ''
            ELSE programs.custom_field_1
        END as custom_field_1"),
        DB::raw("CASE 
            WHEN programs.custom_field_2 IS NULL  
            THEN ''
            ELSE programs.custom_field_2
        END as custom_field_2"),
        DB::raw("CASE 
            WHEN programs.custom_field_3 IS NULL  
            THEN ''
            ELSE programs.custom_field_3
        END as custom_field_3"),
        DB::raw("CASE 
            WHEN programs.custom_field_4 IS NULL  
            THEN ''
            ELSE programs.custom_field_4
        END as custom_field_4"),
        DB::raw("CASE 
            WHEN programs.custom_field_5 IS NULL  
            THEN ''
            ELSE programs.custom_field_5
        END as custom_field_5")        
        )->where($params)
        ->where('programs.status','active')
        ->leftJoin('customer_types','programs.customer_type_id','=', 'customer_types.id')
        ->orderBy('programs.name','asc');

        if (!empty($restrictProg)) {
            $results->whereIn('programs.id',$restrictProg);
        }

        return $results->get();
    }
     public function getAllProgramsForReport($client_id = null,$active_inactive_vendor = null){
        $params =array();
        if(!empty($client_id)){
            $params[] = array(
                  'p.client_id', '=', $client_id
            );
        }
        if(!empty($active_inactive_vendor)){
            $params[] = array(
                  'c.status', '=', $active_inactive_vendor
            );
        }
        return DB::table('programs as p')
        ->Join('utilities as u', 'u.id', '=', 'p.utility_id')      
        ->Join('clients as c', 'c.id', '=', 'p.client_id')      
        ->select('p.id',  DB::raw('p.name as program_name'),'p.code',DB::raw('c.name as client_name') )
        ->where($params) 
        ->orderBy('p.name','asc')
        ->get()->toArray();

         
     }


     public function geUtilities($client_id = null,$state=null, $commodities = null){
        $params =array();
        if(!empty($client_id)){
            $params[] = array(
                  'p.client_id', '=', $client_id
            );
        }
        if(!empty($state)){
            $params[] = array(
                  'p.state', '=', $state
            );
        }
        // if(!empty($state)){
        //     $params[] = array(
        //           'u.utilityname', '=', 'TPV Energy'
        //     );
        // }
        
   
  
       
        $query = DB::table('programs as p')
        ->Join('utilities as u', 'u.id', '=', 'p.utility_id')
        ->Join('brand_contacts','brand_contacts.id','=','u.brand_id')
        ->Join('commodities as c', 'u.commodity_id', '=', 'c.id')     
        ->select(DB::raw('distinct(p.utility_id) as utid'),'brand_contacts.name as utilityname','u.market','u.commodity', 'c.id as commodity_id', 
        DB::raw('concat( CASE 
            WHEN u.fullname IS NULL  
            THEN u.markets
            ELSE u.fullname
        END , " (", brand_contacts.name, ")")as fullname')  )
        ->where($params);


       
        if (is_array(explode(',', $commodities))) {
          $result = $query->whereIn('u.commodity_id', explode(',', $commodities))->get()->toArray();
        } else {
          $result = $query->where('u.commodity_id', $commodities)->get()->toArray();
        }
        
        return $result;
    }
    
    public function getAllPrograms_using_utility_shortname($client_id = null,$shortname=null){
      /* $shortname of utility is equal to ldc code in programs table*/
        $params =array();
        if(!empty($client_id)){
            $params[] = array(
                  'client_id', '=', $client_id
            );
        }
        if(!empty($shortname)){
            $params[] = array(
                  'ldc_code', '=', $shortname
            );
        }
        return $this->where($params)->orderBy('name','asc')
             ->get();
     }


     public function singleProgram($id){

            $params[] = array(
                  'id', '=', $id
            );

        return $this->where($params)->orderBy('name','asc')
             ->get();
     }

     public function programReport($params){
        $whereArray =  array(); 
        $export = "";
        if(isset($params['export']) && !empty($params['export']) ) 
        {
           $export = 1;
        }
        if(isset($params['vendorstatus']) && !empty($params['vendorstatus']) ) 
         {
            $whereArray[] =  array('c.status', '=', $params['vendorstatus']);
         }
         if(isset($params['client']) && !empty($params['client']) ) 
        {
           $whereArray[] =  array('p.client_id', '=', $params['client']);
        }

        $query =  DB::table('programs as p')
        ->Join('utilities as u', 'u.id', '=', 'p.utility_id') 
        ->Join('brand_contacts','brand_contacts.id','=','u.brand_id')     
        ->Join('clients as c', 'c.id', '=', 'p.client_id')      
        ->select( 
              DB::raw('p.code as ProgramCode'),
              DB::raw('p.name as ProgramName'),
              DB::raw('p.saleschannels as Saleschannel'),
              DB::raw('p.msf as Msf'),
              DB::raw('p.etf as Etf'),
              DB::raw('p.rate as Rate'),
              DB::raw('p.term as Term'),
              DB::raw('p.state as State'),
              DB::raw("'true' as IsActive "),
              DB::raw("p.unit_of_measure as UnitOfMeasureName "),
              DB::raw("u.commodity as UtilityTypeName "),
              DB::raw("u.market as UTILITY"),
              DB::raw("p.customer_type as PremiseTypeName"),
              DB::raw("brand_contacts.name as BrandName"),
              DB::raw("p.accountnumbertype as AccountNumberTypeName"),
              DB::raw("p.accountnumberlength as AccountNumberLength"),
              DB::raw("'Intersoft' as VendorName"),
              DB::raw("u.commodity_type as CommodityType") 
              )
        ->where($whereArray) 
        ->orderBy('p.name','asc');

        if($export==1){
            $results = $query->get();  
        }else{
            $results = $query->paginate(20);  
        }
        return $results;


     }


     public function getAllProgramsAPI($client_id = null,$utility_id = null, $restrictProg = []){
        $params =array();
        if(!empty($client_id)){
            $params[] = array(
                  'client_id', '=', $client_id
            );
        }
        if(!empty($utility_id)){
            $params[] = array(
                  'utility_id', '=', $utility_id
            );
        }
        $result = $this->select
        (
      'id',
      'client_id',
      'utility_id', 
        DB::raw("CASE 
        WHEN code IS NULL  
        THEN ''
        ELSE code
    END as ProgramCode"),
        
        DB::raw("CASE 
            WHEN name IS NULL  
            THEN ''
            ELSE name
        END as ProgramName"),
     
        DB::raw("CASE 
            WHEN saleschannels IS NULL  
            THEN ''
            ELSE saleschannels
        END as Saleschannel"),
        
        DB::raw("CASE 
            WHEN msf IS NULL  
            THEN 0
            ELSE msf
        END as monthlysf"),
        DB::raw("CASE 
            WHEN etf IS NULL  
            THEN 0
            ELSE etf
        END as earlyterminationfee"), 
        DB::raw("CASE 
            WHEN rate IS NULL  
            THEN 0
            ELSE rate
        END as Rate"), 
       DB::raw("CASE 
            WHEN term IS NULL  
            THEN 0
            ELSE term
        END as Term"), 
        DB::raw("CASE 
            WHEN state IS NULL  
            THEN ''
            ELSE state
        END as State"), 
        DB::raw("CASE 
            WHEN unit_of_measure IS NULL  
            THEN ''
            ELSE unit_of_measure
        END as UnitOfMeasureName"),
        DB::raw("IFNULL(
            (
            SELECT
                TRIM(customer_types.name)
            FROM
                customer_types
            WHERE
                id = programs.customer_type_id
        ),
        ''
        ) AS PremiseTypeName"), 
        DB::raw("CASE 
            WHEN accountnumbertype IS NULL  
            THEN ''
            ELSE accountnumbertype
        END as AccountNumberTypeName"), 
        DB::raw("CASE 
            WHEN accountnumberlength IS NULL  
            THEN ''
            ELSE accountnumberlength
        END as AccountNumberLength"),
        DB::raw("CASE 
            WHEN programs.custom_field_1 IS NULL  
            THEN ''
            ELSE programs.custom_field_1
        END as custom_field_1"),
        DB::raw("CASE 
            WHEN programs.custom_field_2 IS NULL  
            THEN ''
            ELSE programs.custom_field_2
        END as custom_field_2"),
        DB::raw("CASE 
            WHEN programs.custom_field_3 IS NULL  
            THEN ''
            ELSE programs.custom_field_3
        END as custom_field_3"),
        DB::raw("CASE 
            WHEN programs.custom_field_4 IS NULL  
            THEN ''
            ELSE programs.custom_field_4
        END as custom_field_4"),
        DB::raw("CASE 
            WHEN programs.custom_field_5 IS NULL  
            THEN ''
            ELSE programs.custom_field_5
        END as custom_field_5") 
        )
        ->where('programs.status', config('constants.STATUS_ACTIVE'))
        ->where($params)->orderBy('name','asc');
        if (!empty($restrictProg)) {
            $result->whereIn('programs.id',$restrictProg);
        }
        $result = $result->paginate(100)->toArray();
        return $result;
     }
     public function getSingleProgramAPI($id){
        $params =array();
        if(!empty($id)){
            $params[] = array(
                  'id', '=', $id
            );
        }
       
        $result = $this->select
        (
      'id',
        DB::raw("CASE 
        WHEN code IS NULL  
        THEN ''
        ELSE code
    END as ProgramCode"),
        
        DB::raw("CASE 
            WHEN name IS NULL  
            THEN ''
            ELSE name
        END as ProgramName"),
     
        DB::raw("CASE 
            WHEN saleschannels IS NULL  
            THEN ''
            ELSE saleschannels
        END as Saleschannel"),
        
        DB::raw("CASE 
            WHEN msf IS NULL  
            THEN 0
            ELSE msf
        END as monthlysf"),
        DB::raw("CASE 
            WHEN etf IS NULL  
            THEN 0
            ELSE etf
        END as earlyterminationfee"), 
        DB::raw("CASE 
            WHEN rate IS NULL  
            THEN 0
            ELSE rate
        END as Rate"), 
       DB::raw("CASE 
            WHEN term IS NULL  
            THEN 0
            ELSE term
        END as Term"), 
        DB::raw("CASE 
            WHEN state IS NULL  
            THEN ''
            ELSE state
        END as State"), 
        DB::raw("CASE 
            WHEN unit_of_measure IS NULL  
            THEN ''
            ELSE unit_of_measure
        END as UnitOfMeasureName"), 
        DB::raw("CASE 
            WHEN customer_type IS NULL  
            THEN ''
            ELSE customer_type
        END as PremiseTypeName"), 
        DB::raw("CASE 
            WHEN accountnumbertype IS NULL  
            THEN ''
            ELSE accountnumbertype
        END as AccountNumberTypeName"), 
        DB::raw("CASE 
            WHEN accountnumberlength IS NULL  
            THEN ''
            ELSE accountnumberlength
        END as AccountNumberLength") 
         )
        ->where($params)->orderBy('name','asc')
             ->get();
        $result = $result->toArray();
        return $result;
     }
    //  public function replaceCustomerTypeId()
    //  {
    //     //  $ans = $this->leftjoin('customer_types','programs.customer_type','=','customer_types.name')->update(['programs.customer_type_id'=> 'customer_types.id']);
    //     $customerType = CustomerType::all();
    //     foreach($customerType as $ct)
    //     {
    //         Programs::where('customer_type',$ct->name)->update([
    //             'customer_type_id' => $ct->id
    //             ]);
    //     }
    //  }

    // function scopeGetProgramsByLead($query)
    // {
    //     return $query->whereHas('telesales_programs', function($q){
    //         $q->groupBy('telesales_programs.program_id');
    //     });
    // }

    //Retrieve customer type
    public function customerType() {
        return $this->belongsTo('App\models\CustomerType', 'customer_type_id');
    }

}