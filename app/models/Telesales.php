<?php

namespace App\models;

use App\User;
use App\models\Client;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\models\Clientsforms;
use App\models\TeleSalesData;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Telesales extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id', 'form_id', 'contract_pdf', 'created_at','updated_at','user_id','reviewed_by','reviewed_at','decline_reason','status','parent_id','cloned_by','verification_number', 'call_duration', 'is_multiple', 'multiple_parent_id', 'refrence_id','gps_location_image','salesagent_lat','salesagent_lng','tpv_receipt_pdf', 'recording_id', 'twilio_recording_url', 'call_id', 's3_recording_url', 'alert_status', 'verification_method','language', 'verification_start_date', 'is_disconnect_queued', 'disposition_id','is_enrollment_by_state','tpv_attempts'
    ];

    protected $primarykey = 'id';
    protected $table = 'telesales';
    protected $dates = ['deleted_at'];

    protected $appends = ['type'];
    
    protected $casts = [
        'salesagent_lat' => 'double',
        'salesagent_lng' => 'double',
    ];

    public function getTypeAttribute()
    {
        if (isset($this->userWithTrashed->salesAgentDetailsWithTrashed->agent_type)) {
            return $this->userWithTrashed->salesAgentDetailsWithTrashed->agent_type;
        }
        return "";
    }

    public function createLead($data)
    {
       return $this->insertGetId(
           [
               'client_id' => $data['client_id'],
               'form_id' => $data['form_id'],
               'refrence_id' => $data['refrence_id'],
               'user_id' => $data['user_id'],
               'parent_id' => $data['parent_id'],
               'cloned_by' => $data['cloned_by'],
               'created_at' => date('Y-m-d H:i:s'),
               'updated_at' => date('Y-m-d H:i:s'),
               'verification_number' => $data['verification_number'],
               'multiple_parent_id' => $data['multiple_parent_id'],
               'is_multiple' => $data['is_multiple'],

               ]
       );
    }
    public function getLeadID($refrence_id,$client_id="")
    {
        $params =array(
              array(
                'refrence_id', '=', $refrence_id
              )
         );
         if(!empty($client_id)){
            $params[] =  array(
                'client_id', '=', $client_id
            );
         }


       return $this->select('id','form_id','status','verification_number')->where($params)->first();
    }

    public function getChildLeads($parentId)
    {
        return $this->select('id','refrence_id','verification_number','status','client_id','form_id')->where('multiple_parent_id',$parentId)->get();
    }

    public function updateChildLeads($leadId,$data)
    {
        return $this->where('id',$leadId)
        ->update( $data );
    }

    public function storeTpvNowAttempts($leadId,$count)
    {
        return $this->where('id',$leadId)
        ->update(['tpv_attempts'=> $count]);
    }

    public function getLeadByIDAndUserid($refrence_id,$userid)
    {
        $params =array(
            array(
                'refrence_id', '=', $refrence_id
            )
        );
        if(!empty($userid)){
            $params[] =  array(
                'user_id', '=', $userid
            );
        }
        return $this->select('id','form_id','refrence_id','is_multiple','multiple_parent_id','status')
            ->where($params)
            ->where('multiple_parent_id',0)
            ->whereIn('status', ['pending','hangup'])
            ->first();
    }
    public function getLeadInfo($refrence_id,$client_id="")
    {
        $params =array(
              array(
                'refrence_id', '=', $refrence_id
              )
         );
         if(!empty($client_id)){
            $params[] =  array(
                'client_id', '=', $client_id
            );
         }
       return $this->where($params)->first();
    }
    public function getUserLeads($userid,$status="", $daterange= array(), $orderby ='telesales.id', $sort="desc")
    {
        $date_start ="";
        $date_end ="";
         if(isset($daterange['start_date'])  && $daterange['start_date'] !=""){
             $date_start = $daterange['start_date'];
         }
         if(isset($daterange['end_date'])  && $daterange['end_date'] !=""){
            $date_end = $daterange['end_date'];
        }

        return $this->select('telesales.*','dispositions.description')
                ->leftJoin('dispositions', 'dispositions.id', '=', 'telesales.disposition_id')
                ->where([
                        ['telesales.user_id', '=' ,$userid],
                        ['telesales.multiple_parent_id', '=' , '0'],
                ])->when($date_start, function ($query, $date_start) {
                    return $query->whereDate('telesales.created_at','>=', $date_start);
                }) ->when($date_end, function ($query, $date_end) {
                    return $query->whereDate('telesales.created_at','<=', $date_end);
                }) ->when($status, function ($query, $status) {
                    return $query->where('telesales.status', $status);
                })
                ->orderBy($orderby,$sort)
             ->paginate(20);
    }

    function checkchildlead_status($parent_id){
        return $this->select('id','status')->where([
            ['parent_id', '=' ,$parent_id]
       ])->get(1);
    }
    public function getUserDeclinedLeads($userid)
    {
        return $this->where([
                ['user_id', '=' ,$userid],
                ['status', '=' ,'decline']
           ])
           ->orderBy('id','DESC')
           ->get();
    }


    public function getUserLeadsCount($userid,$status="")
    {
        return $this->where([
                ['user_id', '=' ,$userid]
           ])->when($status, function ($query, $status) {
                    return $query->where('status', $status);
                })->count();
    }

    public function getUserAllLeads($userid,$status = 'pending' )
    {
        return $this->select('id','refrence_id','client_id','form_id','user_id',DB::raw('DATE_FORMAT(created_at, "%m-%d-%Y %H:%i:%s") as create_time'))
        ->where([
                ['user_id', '=' ,$userid],
                ['status', '=' ,$status],
           ])->orderBy('id','DESC')
             ->get();
    }

    public function updatesale($reference_id,$input){
        return $this -> where('refrence_id',$reference_id)
        ->update( $input );
    }

    public function nextAutoID()
    {
        $statement = DB::select("SHOW TABLE STATUS LIKE '".$this->table."'");
        $nextId = $statement[0]->Auto_increment;
        return $nextId;
    }

    public function getLatestRefrenceIdByClient($clientId){
        return $this->select('refrence_id','id')->where('client_id',$clientId)->latest()->withTrashed()->first();
    }

    public function validate_disposition($disposition_id){
        return $this->where('disposition_id', $disposition_id)->count();
    }
    public function searchlead($userid,$string)
    {

        if (is_numeric($string)){
            return $this->select('id','refrence_id')
            ->where([
                    ['user_id', '=' ,$userid],
                    ['id', 'like' ,'%'.$string.'%'],
               ])->orderBy('id','DESC')
                 ->limit(10)->get();
        }else{
            return $this->select('id','refrence_id')
            ->where([
                    ['user_id', '=' ,$userid],
                    ['refrence_id', 'like' ,'%'.$string.'%'],
               ])->orderBy('id','DESC')
                 ->limit(10)->get();
        }

    }
    public function validateConfirmationNumber($verification_number){
        return $this->select('id' )
        ->where('verification_number', '=',$verification_number)
        ->first();
    }

    // For check any verification number starting with T is avilable or not
    public function checkVerificationNumber($verification_number){
        $return = Telesales::where('verification_number', 'like', '%' . 'T' . '%')
                            ->orderby('verification_number', 'desc')
                            ->first();
        return $return;
    }

    public function getNextLeadToVerify($multiple_lead_parent_id){
        $params = array();
                $params[] =  array(
                    'multiple_parent_id', '=', $multiple_lead_parent_id
                );



        return $this->select('id','form_id','is_multiple','multiple_parent_id','refrence_id')
        ->where($params)
        ->whereIn('status', ['pending','hangup'])
        ->first();
    }
    public function getAllMultipleChilds($multiple_lead_parent_id){
        $params = array();
                $params[] =  array(
                    'multiple_parent_id', '=', $multiple_lead_parent_id
                );



        return $this->select('id','refrence_id')
        ->where($params)
        ->whereIn('status', ['verified'])
        ->get();
    }
    public function getSingleLead($leadid){
           $params = array();
                $params[] =  array(
                    'id', '=', $leadid
                );
        return $this->where($params)
        ->first();
    }
    public function deleteuserleads($userid){
        return $this->where('id', '=',$userid )->delete();
    }

    public function getDetailsForPdf($leadid){

        $query =  DB::table('telesales')->select(
            'telesales.refrence_id','telesales.gps_location_image','telesales.salesagent_lat','telesales.salesagent_lng','telesales.created_at',
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and form_id = telesales.form_id LIMIT 1) and meta_key = 'first_name' and telesale_id = telesales.id LIMIT 1) as FirstName"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and form_id = telesales.form_id LIMIT 1) and meta_key = 'middle_initial' and telesale_id = telesales.id LIMIT 1) as MiddleName"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and form_id = telesales.form_id LIMIT 1) and meta_key = 'last_name' and telesale_id = telesales.id LIMIT 1) as LastName"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'email' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and telesale_id =telesales.id LIMIT 1) as Email"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'phone_number' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and telesale_id =telesales.id LIMIT 1) as Phone"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'billing_address_1' and telesale_id =telesales.id LIMIT 1) as BillingAddress"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'billing_address_2' and telesale_id =telesales.id LIMIT 1) as BillingAddress2"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'billing_city' and telesale_id =telesales.id LIMIT 1) as BillingCity"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'billing_county' and telesale_id =telesales.id LIMIT 1) as BillingCounty"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'billing_state' and telesale_id =telesales.id LIMIT 1) as BillingState"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'billing_zipcode' and telesale_id =telesales.id LIMIT 1) as BillingZip"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'billing_country' and telesale_id =telesales.id LIMIT 1) as BillingCountry"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and label='Billing Name'  and form_id = telesales.form_id LIMIT 1) and meta_key = 'first_name' and telesale_id =telesales.id LIMIT 1) as BillingFirstName"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and label='Billing Name' and form_id = telesales.form_id LIMIT 1) and meta_key = 'middle_initial' and telesale_id =telesales.id LIMIT 1) as BillingMiddleName"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and label='Billing Name' and form_id = telesales.form_id LIMIT 1) and meta_key = 'last_name' and telesale_id =telesales.id LIMIT 1) as BillingLastName"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'first_name' and telesale_id = telesales.id LIMIT 1) as ServiceFirstName"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'middle_initial' and telesale_id = telesales.id LIMIT 1) as ServiceMiddleName"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'last_name' and telesale_id = telesales.id LIMIT 1) as ServiceLastName"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_address_1' and telesale_id =telesales.id LIMIT 1) as ServiceAddress1"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_address_2' and telesale_id =telesales.id LIMIT 1) as ServiceAddress2"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_city' and telesale_id =telesales.id LIMIT 1) as ServiceCity"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_county' and telesale_id =telesales.id LIMIT 1) as ServiceCounty"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_state' and telesale_id =telesales.id LIMIT 1) as ServiceState"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_zipcode' and telesale_id =telesales.id LIMIT 1) as ServiceZip"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_country' and telesale_id =telesales.id LIMIT 1) as ServiceCountry"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'selectbox' and label LIKE '%Language%' and form_id = telesales.form_id LIMIT 1) and  meta_key = 'value' and telesale_id = telesales.id LIMIT 1) as Language"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'selectbox'  and (form_fields.label LIKE '%Relationship%' or form_fields.label LIKE '%Account%' or form_fields.label LIKE '%Holder%') and form_id = telesales.form_id LIMIT 1) and meta_key = 'value' and telesale_id = telesales.id LIMIT 1) as Relationship"),
            //DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'Account Number' and form_id = telesales.form_id LIMIT 1) and meta_key = 'value' and telesale_id = telesales.id LIMIT 1) as AccountNumber"),
            DB::raw("(select GROUP_CONCAT(meta_value,CONCAT(' (',SUBSTRING(form_fields.label,18),')') SEPARATOR ', ') from telesalesdata left join form_fields on form_fields.id = telesalesdata.field_id  where telesalesdata.field_id and  LOWER(form_fields.label) LIKE 'account number%' and form_fields.form_id = telesales.form_id and telesalesdata.meta_key = 'value' and telesalesdata.telesale_id = telesales.id LIMIT 1) as AccountNumber"),
            DB::raw("(select GROUP_CONCAT(programs.name SEPARATOR ', ') from programs left join telesales_programs on telesales_programs.program_id = programs.id where telesales_programs.telesale_id = telesales.id) as ProductName"),
            
            DB::raw("(select GROUP_CONCAT(customer_types.name SEPARATOR ', ') from customer_types left join programs on customer_types.id = programs.customer_type_id left join telesales_programs on telesales_programs.program_id = programs.id where telesales_programs.telesale_id = telesales.id) as customerTypes"),
            
            DB::raw("(select GROUP_CONCAT('$',programs.msf SEPARATOR ', ') from programs left join telesales_programs on telesales_programs.program_id = programs.id where telesales_programs.telesale_id = telesales.id) as Msf"),
            DB::raw("(select GROUP_CONCAT('$',programs.etf SEPARATOR ', ') from programs left join telesales_programs on telesales_programs.program_id = programs.id where telesales_programs.telesale_id = telesales.id) as Etf"),
            DB::raw("(select GROUP_CONCAT('$',programs.rate,' per ',programs.unit_of_measure SEPARATOR ', ') from programs left join telesales_programs on telesales_programs.program_id = programs.id where telesales_programs.telesale_id = telesales.id) as Rate"),
            DB::raw("(select GROUP_CONCAT(programs.term SEPARATOR ', ') from programs left join telesales_programs on telesales_programs.program_id = programs.id where telesales_programs.telesale_id = telesales.id) as Term"),
            DB::raw("(select GROUP_CONCAT(programs.code SEPARATOR ', ') from programs left join telesales_programs on telesales_programs.program_id = programs.id where telesales_programs.telesale_id = telesales.id) as ProgramCode"),
            DB::raw("(select GROUP_CONCAT(programs.custom_field_1 SEPARATOR ', ') from programs left join telesales_programs on telesales_programs.program_id = programs.id where telesales_programs.telesale_id = telesales.id) as custom_field_1"),
            DB::raw("(select GROUP_CONCAT(programs.custom_field_2 SEPARATOR ', ') from programs left join telesales_programs on telesales_programs.program_id = programs.id where telesales_programs.telesale_id = telesales.id) as custom_field_2"),
            DB::raw("(select GROUP_CONCAT(programs.custom_field_3 SEPARATOR ', ') from programs left join telesales_programs on telesales_programs.program_id = programs.id where telesales_programs.telesale_id = telesales.id) as custom_field_3"),
            DB::raw("(select GROUP_CONCAT(programs.custom_field_4 SEPARATOR ', ') from programs left join telesales_programs on telesales_programs.program_id = programs.id where telesales_programs.telesale_id = telesales.id) as custom_field_4"),
            DB::raw("(select GROUP_CONCAT(programs.custom_field_5 SEPARATOR ', ') from programs left join telesales_programs on telesales_programs.program_id = programs.id where telesales_programs.telesale_id = telesales.id) as custom_field_5"),
            DB::raw("( select GROUP_CONCAT(market  SEPARATOR ', ') from utilities where id IN (select utility_id from programs where id IN (select program_id from telesales_programs where telesale_id = telesales.id)) ) as Brand"),
            DB::raw("(select GROUP_CONCAT(name  SEPARATOR ', ') from brand_contacts where id in( select brand_id from utilities where id IN (select utility_id from programs where id IN (select program_id from telesales_programs where telesale_id = telesales.id))) ) as BrandName"),
            DB::raw("( select GROUP_CONCAT(fullname  SEPARATOR ', ') from utilities where id IN (select utility_id from programs where id IN (select program_id from telesales_programs where telesale_id = telesales.id)) ) as Utility"),
            DB::raw("( select GROUP_CONCAT(name  SEPARATOR ',')  from commodities where id IN (select commodity_id from utilities where id IN (select utility_id from programs where id IN (select program_id from telesales_programs where telesale_id = telesales.id))) ) as Commodity"),
            DB::raw("( select GROUP_CONCAT(market SEPARATOR ', ') from utilities where id IN (select utility_id from programs where id IN (select program_id from telesales_programs where telesale_id = telesales.id)) ) as Market"),
            DB::raw("( select url from leadmedia where telesales_id = telesales.id and type = 'image' order by id desc limit 1) as signature"),
            DB::raw("(select logo from clients where id = telesales.client_id limit 1) as client_logo"),
            DB::raw("(select state from zip_codes where id IN (select zipcode_id from telesales_zipcodes where telesale_id = telesales.id) ) as State"),
            DB::raw("( select name from clients where id = telesales.client_id limit 1) as client_name"),
            // DB::raw("(select CONCAT(first_name,' ',last_name) from users left join telesales on telesales.user_id = users.id) as SalesAgentName"),
            // DB::raw("(select userid from users left join telesales on telesales.user_id = users.id) as SalesAgentId"),
            // DB::raw("(select phone_number from salesagent_detail left join telesales on telesales.user_id = salesagent_detail.user_id) as SalesAgentPhone"),
            DB::raw("( select contact_info from clients where id = telesales.client_id limit 1) as client_contactNo")

         )
         
         ->where('telesales.id', '=', $leadid);
      return   $query->get();

    }

    public function teleSalesData()
    {
        return $this->hasMany('App\models\Telesalesdata', 'telesale_id');
    }
    public function leadMedia()
    {
        return $this->hasMany('App\models\Leadmedia', 'telesales_id');
    }
    public function questionAnswers()
    {
        return $this->hasMany('App\models\CallAnswers', 'lead_id');
    }

    public function selfVerifyModes()
    {
        return $this->hasMany('App\models\TelesalesSelfVerifyExpTime', 'telesale_id');
    }

    public function selfverifyDetails()
    {
        return $this->hasOne('App\models\SelfverifyDetail', 'telesale_id');
    }

    public function criticalLogs()
    {
        return $this->hasMany('App\models\CriticalLogsHistory', 'lead_id');
    }

    public function zipcodes()
    {
      return $this->belongsToMany('App\models\Zipcodes', 'telesales_zipcodes', 'telesale_id', 'zipcode_id')->withTimestamps();
    }

    public function programs()
    {
      return $this->belongsToMany('App\models\Programs', 'telesales_programs', 'telesale_id', 'program_id')->withTimestamps();
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function userWithTrashed() {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function client() {
        return $this->belongsTo(Client::class, 'client_id');
    }

    // For parent lead relation
    public function parentLead() {
        return $this->belongsTo('App\models\Telesales', 'multiple_parent_id', 'id');
    }

    // For child lead relation
    public function childLeads() {
        return $this->hasMany('App\models\Telesales', 'multiple_parent_id', 'id');
    }

    public function scopeGetLeadsByClientId($query, $clientId) {
        $ans = $query->where('telesales.client_id', $clientId);
            return $ans;
    }

    public function scopeGetLeadsByRange($query, $start, $end, $field = 'telesales.created_at') {
        return $query->where($field, '>', $start)->where($field, '<=', $end);
    }
    public function scopeGetLeadsByBrand($query,$brandId) {
        if($brandId != ''){
            
            return $query->leftJoin('telesales_programs','telesales_programs.telesale_id','=','telesales.id')
            ->leftJoin('programs','programs.id','=','telesales_programs.program_id')
            ->leftJoin('utilities','programs.utility_id','=','utilities.id')
            ->leftJoin('brand_contacts','utilities.brand_id','=','brand_contacts.id')
            ->where('brand_contacts.id',$brandId);
        }
        else
        {
            return $query;
        }
    }

    public function scopeGetSaleByAgentTypes($query, $type = 'tele', $clientId = ""){
        if ($clientId != "") {
            return $query->whereHas('user', function($query) use ($clientId){
                $query->withTrashed()->where('client_id', $clientId);
            })->whereHas('userWithTrashed.salesAgentDetails', function($query) use ($type){
                $query->withTrashed()->where('agent_type', $type);
            });
        } else {
            return $query->whereHas('userWithTrashed.salesAgentDetails', function($query) use ($type){
                $query->withTrashed()->where('agent_type', $type);
            });
        }
    }

    public function scopeGetSaleByAgents($query, $clientId,$salesCenter=""){
        return $query->whereHas('user', function($query) use ($clientId,$salesCenter){
            $query->where('access_level', 'salesagent')->where('client_id',$clientId);
            if($salesCenter!="")
            {
                $query->where('salescenter_id',$salesCenter);
            }
        });
    }

    public function form() {
      return $this->belongsTo('App\models\Clientsforms', 'form_id');
    }

    public function generateReferenceId() {
        $newId = (new Telesales)->nextAutoID();

        if ($newId != "") {
            $referenceId = str_pad($newId, 10, 0, STR_PAD_LEFT);
        } else {
            $referenceId = time();
        }

        return  $referenceId;
    }

    //This function generates a new lead refrence id by adding client prefix
    public function generateNewReferenceId($clientId,$prefix) {
        //if client prefix is not null then generate refrence id as per client prefix
        if($prefix != null){
            //getting a last refrence id by client id
            $newId = (new Telesales)->getLatestRefrenceIdByClient($clientId);
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
            $existsRefrenceId = Telesales::where('refrence_id',$referenceId)->withTrashed()->exists();
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

    public function scopeGetLeadsStatusList($query)
    {
        return $query->groupBy('status')->get([DB::raw("(CASE WHEN telesales.status = 'verified' THEN 'Verified' WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' ELSE 'Pending' END) as status")]);
    }

    public function scopeGetLeadsByRangeAndStatus($query, $start, $end) {
        return $query->whereBetween('created_at', [$start, $end])->groupBy('status')->get(['status', DB::raw('COUNT(status) as value')])->pluck('value', 'status' );
    }

    public function getLeadDeclineCount($status)
    {

        return Telesales::where('status', $status)
            ->select(DB::raw('count(status) as count '))
            ->get();
    }

    public function getd2dLeadPercentage($taleSalesData,$getAllAgents)
    {
        foreach($taleSalesData as $key => $value)
            {
                $taleSalesData[$key]->percentage = round($taleSalesData[$key]->countByMethod /$getAllAgents[0]->totalCount * 100,2);
            }
            return $taleSalesData;
    }

    public function scopeGetLeadsCountBasedOnLoginUser($query, $start, $end, $clientId) {

        if(!empty($clientId) && $clientId != 0){
            $query->where('telesales.client_id', $clientId);
        }

        return $query->whereBetween('created_at', [$start, $end])->groupBy('status')->get(['status', DB::raw('COUNT(status) as value')])->pluck('value', 'status' );

    }

    //Returns lead count by sales center id
    public function scopeGetTotalLeadCountBySalesCenter($query, $salesCenterId) {
      return $query->whereHas('user', function($q) use ($salesCenterId){
          $q->withTrashed()->where('access_level', 'salesagent')->where('salescenter_id', $salesCenterId);
      })->count();
    }

    //Returns lead by sales center id
    public function scopeGetLeadsBySalesCenter($query, $salesCenterId) {
      return $query->whereHas('user', function($q) use ($salesCenterId){
          $q->withTrashed()->where('access_level', 'salesagent')->where('salescenter_id', $salesCenterId);
      });
    }

    //Retrieve leads by status
    public function scopeGetLeadByStatus($query, $statusArr) {
      return $query->whereIn('telesales.status', $statusArr);
    }

    public function scopeGetSalesCenters($query)
    {
        return $query->whereHas('user.salescenter',function($q){

        })->get();
    }

    //Retrieve leads by status
    public function scopeGetLeadByFormIds($query, $formIds) {
      return $query->whereIn('telesales.form_id', $formIds);
    }

    public function scopeGetLeadsByVerificationMethod($query, $verificationMethod) {
      return $query->where('verification_method', $verificationMethod);
    }

    //Retrieve leads by agent id
    public function scopeGetLeadByAgentId($query, $agentId) {
      return $query->where('telesales.user_id', $agentId);
    }

    //Returns lead by sales center location id
    public function scopeGetLeadsBySCLocation($query, $locationId) {
        return $query->whereHas('user', function($q) use ($locationId){
            $q->withTrashed()->where('access_level', 'salesagent')->where('location_id', $locationId);
        });
    }

    //Returns lead by multiple location
    public function scopeGetLeadsByMultipleLocation($query, $locationIds) {
        return $query->whereHas('user', function($q) use ($locationIds){
            $q->where('access_level', 'salesagent')->whereIn('location_id', $locationIds);
        });
    }

    public function scopeGetLeadsByAllSCLocation($query, $salesCenterId) {
        return $query->whereHas('user', function($q) use ($salesCenterId){
            $q->where('access_level', 'salesagent')->where('salescenter_id', $salesCenterId);
        });
      }

     public function scopeGetProgramsByLead($query)
    {
        return $query->leftjoin('telesales_programs','telesales_programs.telesale_id','=','telesales.id');//->groupBy('telesales_programs.program_id');
    }
    public function scopeGetLeadByProgramId($query,$programId,$brand)
    {
        $ans = $query->leftjoin('telesales_programs','telesales_programs.telesale_id','=','telesales.id')
        ->leftjoin('programs','telesales_programs.program_id','=','programs.id')
        ->leftjoin('utilities','programs.utility_id','=','utilities.id')
        ->where('telesales_programs.program_id',$programId);
        if($brand != '')
            $query->where('utilities.brand_id',$brand);
        $query->groupBy('telesales_programs.program_id');
        return $ans;
    }

    public function scopeGetLeadByUtility($query,$utilityName="",$brand = '')
    {
        $query = $query->leftjoin('telesales_programs','telesales_programs.telesale_id','=','telesales.id')
                    ->leftjoin('programs','telesales_programs.program_id','=','programs.id')
                    ->leftjoin('utilities','programs.utility_id','=','utilities.id');
                    if($utilityName != "")
                    {
                        $query = $query->where('utilities.id',$utilityName);
                    }
                    if($brand != "")
                    {
                        $query = $query->where('utilities.brand_id',$brand);
                    }
                    $query = $query->groupBy('utilities.id');
        return $query;
    }

    public function scopeGetLeadByState($query)
    {
        $query = $query->leftjoin('telesalesdata','telesalesdata.telesale_id','=','telesales.id')
        // ->leftjoin('telesales_zipcodes','telesales_zipcodes.telesale_id','=','telesales.id')
        ->leftjoin('zip_codes','zip_codes.zipcode','=','telesalesdata.meta_value')->where('meta_key','service_zipcode');
        return $query;
        
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function($telesales)
        {
            \Log::info("Deleting telesales related data...");
            $telesales->teleSalesData()->delete();
            $telesales->leadMedia()->delete();
            $telesales->questionAnswers()->delete();
            $telesales->selfVerifyModes()->delete();
            $telesales->selfverifyDetails()->delete();
            $telesales->criticalLogs()->delete();
        });
    }

    public function getDualFualData($leadId,$clientId)
    {
        
        $leads = Telesales::leftjoin('telesales_programs', 'telesales.id', 'telesales_programs.telesale_id')
                            ->leftjoin('programs', 'telesales_programs.program_id', '=', 'programs.id')
                            ->leftjoin('utilities', 'programs.utility_id', '=', 'utilities.id')
                            ->leftjoin('brand_contacts', 'utilities.brand_id', '=', 'brand_contacts.id')
                            ->leftjoin('commodities', 'commodities.id', '=', 'utilities.commodity_id')
                            ->leftjoin('users', 'telesales.user_id', '=', 'users.id')
                            ->select('telesales.id as id', 'telesales_programs.program_id', 'programs.name as program_name', 
                                'utilities.id as utility_id', 'brand_contacts.name as UtilityName','utilities.fullname as utility_name','utilities.act_num_verbiage', 'commodities.id as commodity_id', 
                                'commodities.name as commodity_name', 'users.userid as salesperson_code', 'users.first_name as salesperson_first_name', 'users.last_name as salesperson_last_name',
                                'telesales.refrence_id as voice_verif_code', 'telesales.refrence_id as ext_customer_id', 'telesales.created_at as date_of_sale','rate','unit_of_measure as unit','msf','etf','term',
                                'programs.code as product_code','custom_field_1','custom_field_2','custom_field_3','custom_field_4','custom_field_5',
                                \DB::raw("(select count(commodity_id) from form_commodities where form_id = telesales.form_id) as commodities_count"),
                                \DB::raw("(select count(id) from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id  and deleted_at is null) as service_address_count"),
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and is_primary = true  and deleted_at is null LIMIT 1) and  meta_key = 'first_name' and telesale_id =telesales.id LIMIT 1) as cust_first_name"),
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and is_primary = true and deleted_at is null LIMIT 1) and  meta_key = 'middle_initial' and telesale_id =telesales.id LIMIT 1) as cust_middle_initial"),
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and is_primary = true and deleted_at is null LIMIT 1) and  meta_key = 'last_name' and telesale_id =telesales.id LIMIT 1) as cust_last_name"),
                                
                                // service_addr_line_1
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id  and LOWER(label) LIKE CONCAT('service and billing address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and is_primary = 1  LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_address_1' and telesale_id =telesales.id LIMIT 1) as service_addr_line_1"),
                                
                                // service_addr_apart_no
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and LOWER(label) LIKE CONCAT('service and billing address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_unit' and telesale_id =telesales.id LIMIT 1) as service_addr_apart_no"),

                                // service_addr_line_2
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and LOWER(label) LIKE CONCAT('service and billing address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_address_2' and telesale_id =telesales.id LIMIT 1) as service_addr_line_2"),
                                
                                // service_addr_city
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and LOWER(label) LIKE CONCAT('service and billing address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_city' and telesale_id =telesales.id LIMIT 1) as service_addr_city"),

                                 // service_addr_county
                                 \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and LOWER(label) LIKE CONCAT('service and billing address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_county' and telesale_id =telesales.id LIMIT 1) as service_addr_county"),

                                    // service_addr_state
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1  AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and LOWER(label) LIKE CONCAT('service and billing address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_state' and telesale_id =telesales.id LIMIT 1) as service_addr_state"),
                                
                                // service_addr_zipcode
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1  AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and LOWER(label) LIKE CONCAT('service and billing address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_zipcode' and telesale_id =telesales.id LIMIT 1) as service_addr_zipcode"),
                                
                                // postal_addr_line_1
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1  AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and LOWER(label) LIKE CONCAT('service and billing address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id and deleted_at is null LIMIT 1)
                                    END )  
                                    and  meta_key = 'billing_address_1' and telesale_id =telesales.id LIMIT 1) as postal_addr_line_1"),

                                // postal_addr_line_2
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1  AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and LOWER(label) LIKE CONCAT('service and billing address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'billing_address_2' and telesale_id =telesales.id LIMIT 1) as postal_addr_line_2"),

                                // postal_addr_city
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1  AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and LOWER(label) LIKE CONCAT('service and billing address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'billing_city' and telesale_id =telesales.id LIMIT 1) as postal_addr_city"),
                                    // postal_addr_county
                                    \DB::raw("(select meta_value from telesalesdata where field_id = (
                                        CASE WHEN commodities_count > 1  AND service_address_count > 1 THEN
                                            (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and LOWER(label) LIKE CONCAT('service and billing address%', commodities.name ,'%') LIMIT 1)
                                            ELSE 
                                            (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                        END )  
                                        and  meta_key = 'billing_county' and telesale_id =telesales.id LIMIT 1) as postal_addr_county"),
                                
                                // postal_addr_zipcode
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1  AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and LOWER(label) LIKE CONCAT('service and billing address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'billing_zipcode' and telesale_id =telesales.id LIMIT 1) as postal_addr_zipcode"),
                                // state
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE 'Service and Billing Address' LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_state' and telesale_id =telesales.id LIMIT 1) as state"),

                                // contact_first_name
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 THEN
                                        (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and deleted_at is NULL and label LIKE CONCAT('Billing Name%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and deleted_at is null and label LIKE 'Billing Name' LIMIT 1)
                                    END )  
                                    and  meta_key = 'first_name' and telesale_id =telesales.id LIMIT 1) as contact_first_name"),
                                
                                // contact_middle_initial
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                        CASE WHEN commodities_count > 1 THEN
                                            (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Billing Name%') LIMIT 1)
                                            ELSE 
                                            (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and deleted_at is null and label LIKE 'Billing Name' LIMIT 1)
                                        END )  
                                        and  meta_key = 'middle_initial' and telesale_id =telesales.id LIMIT 1) as contact_middle_initial"),
                                
                                // contact_last_name
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                        CASE WHEN commodities_count > 1 THEN
                                            (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Billing Name%') LIMIT 1)
                                            ELSE 
                                            (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and deleted_at is null and label LIKE 'Billing Name' LIMIT 1)
                                        END )  
                                        and  meta_key = 'last_name' and telesale_id =telesales.id LIMIT 1) as contact_last_name"),

                                // Account Number (ESI no)
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 THEN
                                        (select id from form_fields where type = 'textbox' and form_id = telesales.form_id and deleted_at is null and LOWER(label) LIKE CONCAT('account number%', LOWER(commodities.name) ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'textbox' and form_id = telesales.form_id and deleted_at is null and LOWER(label) LIKE '%account number%' LIMIT 1)
                                    END )  
                                    and  meta_key = 'value' and telesale_id =telesales.id LIMIT 1) as account_number"),
                                
                                // phone number
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'phone_number' and form_id = telesales.form_id and deleted_at is null and is_primary = true LIMIT 1) and  meta_key = 'value' and telesale_id = telesales.id LIMIT 1) as contact_phone_no"),
                                
                                // email address
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'email' and form_id = telesales.form_id and deleted_at is null and is_primary = true LIMIT 1) and  meta_key = 'value' and telesale_id = telesales.id LIMIT 1) as contact_email_address"),
                                
                                // preffered language
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'radio' and form_id = telesales.form_id and deleted_at is null LIMIT 1) and  meta_key = 'value' and telesale_id = telesales.id LIMIT 1) as preffered_language"),

                                // commodity (service type)
                                \DB::raw("UPPER(LEFT(commodities.name , 1)) as service_type")
                            )       
                            ->where('telesales.client_id', '=', $clientId)       
                            ->where('telesales.id',$leadId)
                            ->get()
                            ->toArray();
                            return $leads;
    }
}
