<?php

namespace App\Jobs;

use App\models\Telesales;
use PDF;
use LynX39\LaraPdfMerger\Facades\PdfMerger;

use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mail;
use App\models\ClientTwilioNumbers;
use App\models\TextEmailStatistics;
use App\Services\StorageService;
use Carbon\Carbon;
use App\models\Settings;

class SendContractPDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $leadid;
    public $lat;
    public $lng;
    public $pdfChild = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($leadid,$lat=null,$lng=null)
    {
        $this->leadid = $leadid;
        $this->lat = $lat;
        $this->lng = $lng;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $leadid = $this->leadid;

        $lead = Telesales::findOrFail($leadid);

        // for check self TPV tele is enable or not
        if ($lead->type == 'tele' && !isOnSettings($lead->client_id, 'is_enable_contract_tele')) {
            $msg = "Contracts: Tele switched off by administrator.";
            Log::error($msg);
            return ;
        }

        // for check self TPV d2d is enable or not
        if ($lead->type == 'd2d' && !isOnSettings($lead->client_id, 'is_enable_contract_d2d')) {
            $msg = "Contracts: D2D switched off by administrator.";
            Log::error($msg);
            return ;
        }

        foreach ($lead->childLeads as $key => $childLead) {
            $this->generateContract($childLead, $childLead->id, 'child');
        }
        $this->generateContract($lead,$leadid);         
    }

    public function generateContract($lead, $leadid, $leadType='parent')
    {
        try {
            $lat = $this->lat;
            $lng = $this->lng;
            $data = (new Telesales)->getDetailsForPdf($leadid);
            $electricData = [];
            $gasData = [];

            $dataNew = (new Telesales)->getDualFualData($leadid,$lead->client_id);
            foreach($dataNew as $key => $val)
            {
                if(strtolower($val['commodity_name']) == 'electric'){
                    
                    $electricData['rate']  = $val['rate'];
                    $electricData['unit']  = $val['unit'];
                    $electricData['msf']  = $val['msf'];
                    $electricData['etf']  = $val['etf'];
                    $electricData['term']  = $val['term'];
                    $electricData['utility_name']  = $val['utility_name'];
                    $electricData['program_name']  = $val['program_name'];
                    $electricData['custom_field_1']  = ($val['custom_field_1']) ? $val['custom_field_1'] : '';
                    $electricData['custom_field_2']  = ($val['custom_field_2']) ? $val['custom_field_2'] : '';
                    $electricData['custom_field_3']  = ($val['custom_field_3']) ? $val['custom_field_3'] : '';
                    $electricData['custom_field_4']  = ($val['custom_field_4']) ? $val['custom_field_4'] : '';
                    $electricData['custom_field_5']  = ($val['custom_field_5']) ? $val['custom_field_5'] : '';
                    $electricData['UtilityName']  = $val['UtilityName'];
                    $electricData['account_number']  = $val['account_number'];
                    $electricData['act_num_verbiage']  = $val['act_num_verbiage'];
                    $electricData['service_addr_line_1']  = $val['service_addr_line_1'];
                    $electricData['service_addr_line_2']  = $val['service_addr_line_2'];
                    $electricData['service_addr_city']  = $val['service_addr_city'];
                    $electricData['service_addr_county']  = $val['service_addr_county'];
                    $electricData['cust_first_name']  = $val['cust_first_name'];
                    $electricData['service_addr_zipcode']  = $val['service_addr_zipcode'];
                    $electricData['service_addr_state']  = $val['service_addr_state'];
                    $electricData['cust_middle_initial']  = $val['cust_middle_initial'];
                    $electricData['cust_last_name']  = $val['cust_last_name'];
                    $electricData['contact_first_name']  = $val['contact_first_name'];
                    $electricData['contact_middle_initial']  = $val['contact_middle_initial'];
                    $electricData['contact_last_name']  = $val['contact_last_name'];
                    $electricData['product_type'] = '';
                }
                if(strtolower($val['commodity_name']) == 'gas'){
                    $gasData['rate']  = $val['rate'];
                    $gasData['unit']  = $val['unit'];
                    $gasData['msf']  = $val['msf'];
                    $gasData['etf']  = $val['etf'];
                    $gasData['term']  = $val['term'];
                    $gasData['utility_name']  = $val['utility_name'];
                    $gasData['program_name']  = $val['program_name'];
                    $gasData['custom_field_1']  = ($val['custom_field_1'] != null) ? $val['custom_field_1'] : '';
                    $gasData['custom_field_2']  = ($val['custom_field_2'] != null) ? $val['custom_field_2'] : '';
                    $gasData['custom_field_3']  = ($val['custom_field_3'] != null) ? $val['custom_field_3'] : '';
                    $gasData['custom_field_4']  = ($val['custom_field_4'] != null) ? $val['custom_field_4'] : '';
                    $gasData['custom_field_5']  = ($val['custom_field_5'] != null) ? $val['custom_field_5'] : '';
                    $gasData['UtilityName']  = $val['UtilityName'];
                    $gasData['account_number']  = $val['account_number'];
                    $gasData['act_num_verbiage']  = $val['act_num_verbiage'];
                    $gasData['service_addr_line_1']  = $val['service_addr_line_1'];
                    $gasData['service_addr_line_2']  = $val['service_addr_line_2'];
                    $gasData['service_addr_city']  = $val['service_addr_city'];
                    $gasData['service_addr_county']  = $val['service_addr_county'];
                    $gasData['cust_first_name']  = $val['cust_first_name'];
                    $gasData['service_addr_zipcode']  = $val['service_addr_zipcode'];
                    $gasData['service_addr_state']  = $val['service_addr_state'];
                    $gasData['cust_middle_initial']  = $val['cust_middle_initial'];
                    $gasData['cust_last_name']  = $val['cust_last_name'];
                    $gasData['contact_first_name']  = $val['contact_first_name'];
                    $gasData['contact_middle_initial']  = $val['contact_middle_initial'];
                    $gasData['contact_last_name']  = $val['contact_last_name'];
                    $gasData['product_type'] = '';
                }
            }     
            
            $gps_image = "";
            $gps_image = $data[0]->gps_location_image;
            $est_address = '';
            $customFields = getEnableCustomFields($lead->client_id);

            if($lat != '' && $lat != '') {  

                $key = config()->get('constants.GOOGLE_MAP_API_KEY');
                $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lng . '&key=' . $key;
                $rep = file_get_contents($url);
                $address = json_decode($rep, true);
                if ($address['status'] == 'OK') {
                    $est_address = $address['results'][0]['formatted_address'];
                } else {
                    \Log::info('else');
                    $est_address = 'N/A';
                }
            }
            if (!empty($data->toArray())) {
                $leadData = $data[0];
                $signature = "";
                if (isset($leadData->signature) && !empty($leadData->signature)) {
                    $signature = url(Storage::disk('s3')->url($leadData->signature));
                }
                
                $phNum = (strlen($leadData->Phone) == 11) ? $leadData->Phone : "1" . $leadData->Phone;

                $arrPhoneNum = preg_replace(config()->get('constants.DISPLAY_PHONE_NUMBER_FORMAT'), config()->get('constants.PHONE_NUMBER_REPLACEMENT'), $phNum);

                $customer_info = array(
                    'firstname' => $leadData->FirstName,
                    'middlename' => isset($leadData->MiddleName) ? $leadData->MiddleName : "",
                    'lastname' => $leadData->LastName,
                    'email' => $leadData->Email,
                    'Phone' => $arrPhoneNum,
                    'signature' => $signature,
                    'client_name' => $leadData->client_name,
                    'client_contactNo' => $leadData->client_contactNo,
                    'client_logo' => Storage::disk('s3')->url($leadData->client_logo),
                    'Lat' => $lat,
                    'Lng' => $lng,
                    'date' => Carbon::parse($leadData->created_at)->setTimezone(getClientSpecificTimeZone())->format(getDateFormat())
                );
                $program_info = array(
                    'Msf' => $leadData->Msf,
                    'Etf' => $leadData->Etf,
                    'Brand' => isset($leadData->Brand) && !empty($leadData->Brand) ? $leadData->Brand : '',
                    'ProductName' => $leadData->ProductName,
                    'Rate' =>$leadData->Rate,
                    'Term' =>$leadData->Term,
                    'BillingAddress' => $leadData->BillingAddress,
                    'BillingAddress2' => $leadData->BillingAddress2,
                    'BillingCity' => $leadData->BillingCity,
                    'BillingState' => $leadData->BillingState,
                    'BillingZip' => $leadData->BillingZip,
                    'BillingCounty' => $leadData->BillingCounty,
                    'BillingFirstName' => isset($leadData->BillingFirstName) ? $leadData->BillingFirstName : $customer_info['firstname'],
                    'BillingMiddleName' => isset($leadData->BillingMiddleName) ? $leadData->BillingMiddleName : "",
                    'BillingLastName' => isset($leadData->BillingLastName) ? $leadData->BillingLastName : $customer_info['lastname'],
                    'ServiceFirstName' => isset($leadData->ServiceFirstName) ? $leadData->ServiceFirstName : $customer_info['firstname'],
                    'ServiceMiddleName' => isset($leadData->ServiceMiddleName) ? $leadData->ServiceMiddleName : "",
                    'ServiceLastName' => isset($leadData->ServiceLastName) ? $leadData->ServiceLastName : $customer_info['lastname'],
                    'Utility' => $leadData->Utility,
                    'Market' =>$leadData->Market,
                    'ProgramCode' => $leadData->ProgramCode,
                    'ServiceAddress1' => $leadData->ServiceAddress1,
                    'ServiceAddress2' => isset($leadData->ServiceAddress2) && !empty($leadData->ServiceAddress2) ? $leadData->ServiceAddress2 : "",
                    'ServiceCity' => $leadData->ServiceCity,
                    'ServiceState' => $leadData->ServiceState,
                    'ServiceZip' => $leadData->ServiceZip,
                    'ServiceCounty' => $leadData->ServiceCounty,
                    'Relationship' => $leadData->Relationship,
                    'Language' => $leadData->Language,
                    'AccountNumber' => $leadData->AccountNumber,
                    'custom_field_1' => $leadData->custom_field_1,
                    'custom_field_2' => $leadData->custom_field_2,
                    'custom_field_3' => $leadData->custom_field_3,
                    'custom_field_4' => $leadData->custom_field_4,
                    'custom_field_5' => $leadData->custom_field_5
                );
                $program_info['product_type'] = '';

                $program_info['Rate2'] = '';

                // for calculate rate 2
                foreach ($customFields as $key => $value) {
                    if (preg_match("/{$value}/i", config()->get('constants.RATE_2_LABEL'))) {
                        $program_info['Rate2'] = $leadData->{$key};
                        if(isset($gasData[$key]))
                                $gasData['Rate2'] = $gasData[$key];
                            if(isset($electricData[$key]))
                                $electricData['Rate2'] = $electricData[$key];
                    }
                    if (preg_match("/{$value}/i", config()->get('constants.PRODUCT_TYPE_LABEL'))) {
                        $program_info['product_type'] = $leadData->{$key};
                            if(isset($gasData[$key]))
                                $gasData['product_type'] = $gasData[$key];
                            if(isset($electricData[$key]))
                                $electricData['product_type'] = $electricData[$key];
                    }
                }
                
                if(!empty($program_info['product_type'])){
                    $program_info['product_type'] = explode(',',$program_info['product_type'])[0];
                }            
                
                $salesagents['name'] = $lead->user->full_name;
                $salesagents['id'] = isset($lead->user->salesAgentDetails) ? $lead->user->salesAgentDetails->external_id : '';
                if (!empty($lead->user->salesAgentDetails->phone_number)) {
                    $saleAgentPhoneNum = $lead->user->salesAgentDetails->phone_number;
                    $format = config('constants.DISPLAY_PHONE_NUMBER_FORMAT_10_DIGIT');
                    $replacement = config('constants.PHONE_NUMBER_REPLACEMENT_10_DIGIT');
                    $saleAgentPhoneNum = preg_replace($format, $replacement,$saleAgentPhoneNum);
                    info("phone: ".$saleAgentPhoneNum);
                }else if ($lead->client_id == config()->get('constants.CLIENT_LE_CLIENT_ID')) {
                    $saleAgentPhoneNum = config('constants.AGENT_DEFAULT_NUMBER');
                }else {
                    $saleAgentPhoneNum = '';
                }

                $salesagents['phone'] = $saleAgentPhoneNum;
                $commodities = explode(',',strtolower($leadData->Commodity));

                /****** start temporary code *******
                    Note: This code is temporary for some time

                    for copy address if service address available common for duel fuel
                ***/
                if($lead->client_id == config()->get('constants.CLIENT_LE_CLIENT_ID')){

                    if (count($commodities) > 1) {
                        if(isset($gasData['service_addr_line_1']) && !empty($gasData['service_addr_line_1']) && (!isset($electricData['service_addr_line_1']) || empty($electricData['service_addr_line_1']))) {

                            info('Service address is copy from gas data to electric data...');
                            $electricData['service_addr_line_1'] = $gasData['service_addr_line_1'] ;
                            $electricData['service_addr_line_2']  = $gasData['service_addr_line_2'];
                            $electricData['service_addr_city']  = $gasData['service_addr_city'];
                            $electricData['service_addr_county']  = $gasData['service_addr_county'];
                            $electricData['service_addr_zipcode']  = $gasData['service_addr_zipcode'];
                            $electricData['service_addr_state']  = $gasData['service_addr_state'];

                        } else if(isset($electricData['service_addr_line_1']) && !empty($electricData['service_addr_line_1']) && (!isset($gasData['service_addr_line_1']) || empty($gasData['service_addr_line_1']))) {

                            info('Service address is copy from electric data to gas data...');
                            $gasData['service_addr_line_1'] = $electricData['service_addr_line_1'] ;
                            $gasData['service_addr_line_2']  = $electricData['service_addr_line_2'];
                            $gasData['service_addr_city']  = $electricData['service_addr_city'];
                            $gasData['service_addr_county']  = $electricData['service_addr_county'];
                            $gasData['service_addr_zipcode']  = $electricData['service_addr_zipcode'];
                            $gasData['service_addr_state']  = $electricData['service_addr_state'];

                        }
                    }
                }
                /****** end temporary code *******/

                $data = [
                    'title' => 'TPV Contract',
                    'heading' => 'TPV Contract',
                    'customer_info' => $customer_info,
                    'program_info' => $program_info,
                    'gps_image' => $gps_image,
                    'estimated_address' =>$est_address,
                    'custom_fields' =>$customFields,
                    'salesAgents' => $salesagents,
                    'commodities' => $commodities,
                    'electricData' => $electricData,
                    'gasData' => $gasData
                ];
                Log::info($data);
                $pdfGas = '';
                $pdfElectric = '';
                $pdf = '';
                $phone = ClientTwilioNumbers::where('client_id', $lead->client_id)
                                                ->where('type','customer_call_in_verification')
                                                ->first();

                $tpvNumber = '';
                $mngFlag = $ugpFlag = 0;
                if(!empty($phone)) {
                    $tpvNumber = preg_replace(config()->get('constants.DISPLAY_PHONE_NUMBER_FORMAT'), config()->get('constants.PHONE_NUMBER_REPLACEMENT'), str_replace("+", "", $phone->phonenumber));
                }
                if(strtolower($leadData->Language) == 'english')
                    $leadData->Language = 'en';
                if(strtolower($leadData->Language) == 'spanish')
                    $leadData->Language = 'es';
                if(!isset($leadData->Language))
                    $leadData->Language = 'en';
                $otherDocs = [];
               

                // For LE energy client's pdfs
                if($lead->client_id == config()->get('constants.CLIENT_LE_CLIENT_ID')){

                    if($leadData->BrandName == 'Michigan Gas & Power' && strtoupper($leadData->State) == "MD" && count($commodities) == 1 && in_array('gas',$commodities) && strtolower($program_info['product_type']) == 'variable' && strtolower($leadData->customerTypes) == 'residential')
                    {
                        $mngFlag = 1;
                        $pdfGas = PDF::loadView('contractpdf/LE_Client/mng_contract', $data);
                    }
                    else if(strtoupper($leadData->State) == "OH")
                    {
                        $ugpFlag = 1;
                        if(in_array('gas',$commodities)){
                            $pdfGas = PDF::loadView('contractpdf/LE_Client/ugp_contract_gas', $data);
                        }
                        if(in_array('electric',$commodities)){
                            $pdfElectric = PDF::loadView('contractpdf/LE_Client/ugp_contract_electric', $data);
                        }
                        else{
                            $pdf = PDF::loadView('contractpdf/contractpdfnew', $data);
                        }
                    }
                    else
                        $pdf = PDF::loadView('contractpdf/contractpdfnew', $data);
                }

                //for Sunrise client contract pdf condition
                else if($lead->client_id == config()->get('constants.CLIENT_SUNRISE_CLIENT_ID')){
                    if(isset($electricData) && !empty($electricData)){
                        $pdfElectric = PDF::loadView('contractpdf/SUNRISE_Client/contractSummary', $data);
                    }
                    else {
                        $pdf = PDF::loadView('contractpdf/contractpdfnew', $data); 
                    }
                }
                
                //for RRH client contract pdf condition
                else if($lead->client_id == config()->get('constants.CLIENT_RRH_CLIENT_ID')){
                    //This all condition is for state MD
                    if(strtoupper($leadData->State) == "MD"){
                        if(in_array('electric',$commodities)){
                            \Log::info('In MD Electric');
                            if(strtolower($electricData['product_type']) == 'fixed' && strtolower($leadData->Language) == 'en')
                            {
                                $pdfElectric = PDF::loadView('contractpdf/RRH_Client/md_electric_fixed_contract', $data,$electricData);
                                $otherDocs[] = asset('client_pdfs/RRH_client/Terms&conditions/Spring-MD-Fixed-Contract-TCs-v07112019-English.pdf');
                            }
                            else if(strtolower($electricData['product_type']) == 'variable' && strtolower($leadData->Language) == 'en')
                            {
                                $pdfElectric = PDF::loadView('contractpdf/RRH_Client/md_electric_variable_contract', $data,$electricData);
                                $otherDocs[] = asset('client_pdfs/RRH_client/Terms&conditions/Spring-MD-Variable-Contract-TCs-v07112019-English.pdf');
                            }
                            else if(strtolower($electricData['product_type']) == 'fixed' && strtolower($leadData->Language) == 'es')
                            {
                                $otherDocs[] = asset('client_pdfs/RRH_client/Terms&conditions/Spring-MD-Fixed-Contract-TCs-v.07112019-Spanish.pdf');
                            }   
                            else if(strtolower($electricData['product_type']) == 'variable' && strtolower($leadData->Language) == 'es')
                            {
                                $pdfElectric = PDF::loadView('contractpdf/RRH_Client/md_electric_variable_spanish', $data,$electricData);
                                $otherDocs[] = asset('client_pdfs/RRH_client/Terms&conditions/Spring-MD-Variable-Contract-TCs-v.07112019-Spanish.pdf');
                            }  
                            else {
                                $pdf = PDF::loadView('contractpdf/contractpdfnew', $data); 
                            }
                        }
                        if(in_array('gas',$commodities)){
                            \Log::info('In MD gas');
                            if(strtolower($gasData['product_type']) == 'fixed' && strtolower($leadData->Language) == 'en')
                            {
                                \Log::info('In MD gas fixed product');
                                $pdfGas = PDF::loadView('contractpdf/RRH_Client/md_gas_fixed_contract', $data,$gasData);
                                $otherDocs[] = asset('client_pdfs/RRH_client/Terms&conditions/Spring-MD-Fixed-Contract-TCs-v07112019-English.pdf');   
                            }
                            
                            else if(strtolower($gasData['product_type']) == 'variable' && strtolower($leadData->Language) == 'en')
                            {
                                \Log::info('In MD En gas variable product');
                                $pdfGas = PDF::loadView('contractpdf/RRH_Client/md_gas_variable_contract', $data,$gasData);
                                $otherDocs[] = asset('client_pdfs/RRH_client/Terms&conditions/Spring-MD-Variable-Contract-TCs-v07112019-English.pdf');
                            }       
                            
                            else if(strtolower($gasData['product_type']) == 'fixed' && strtolower($leadData->Language) == 'es')
                            {
                                \Log::info('In MD ES gas fixed product');
                                $otherDocs[] = asset('client_pdfs/RRH_client/Terms&conditions/Spring-MD-Fixed-Contract-TCs-v.07112019-Spanish.pdf');   
                            }
                            
                            else if(strtolower($gasData['product_type']) == 'variable' && strtolower($leadData->Language) == 'es')
                            {
                                \Log::info('In MD ES gas variable product');
                                $pdfGas = PDF::loadView('contractpdf/RRH_Client/md_gas_variable_spanish', $data,$gasData);
                                $otherDocs[] = asset('client_pdfs/RRH_client/Terms&conditions/Spring-MD-Variable-Contract-TCs-v.07112019-Spanish.pdf');
                            } 
                            else {
                                $pdf = PDF::loadView('contractpdf/contractpdfnew', $data); 
                            }
                        }
                        elseif(!in_array('gas',$commodities) && !in_array('electric',$commodities) ) {
                            $pdf = PDF::loadView('contractpdf/contractpdfnew', $data); 
                        }
                    }

                    // This Condition is for state NJ
                    else if(strtoupper($leadData->State) == "NJ"){
                        if(in_array('electric',$commodities)){
                            if(strtolower($electricData['product_type']) == 'variable' && strtolower($leadData->Language) == 'en')
                            {
                                \Log::info('In NJ EN electric variable product');
                                $pdfElectric = PDF::loadView('contractpdf/RRH_Client/nj_electric_english', $data,$electricData);
                                $otherDocs[] = asset('client_pdfs/RRH_client/Terms&conditions/Spring-NJ-Contract-TCs-English.pdf');
                            }
                            else if(strtolower($electricData['product_type']) == 'variable' && strtolower($leadData->Language) == 'es')
                            {
                                // send pdf which accordingly this condition
                                $otherDocs[] = asset('client_pdfs/RRH_client/Terms&conditions/Spring-NJ-Contract-TCs-Spanish.pdf');
                            }
                            else {
                                $pdf = PDF::loadView('contractpdf/contractpdfnew', $data); 
                            }
                        }
                        if(in_array('gas',$commodities)){
                            if(strtolower($gasData['product_type']) == 'variable' && strtolower($leadData->Language) == 'en')
                            {
                                \Log::info('In NJ EN gas variable product');
                                $pdfGas = PDF::loadView('contractpdf/RRH_Client/nj_gas_english', $data,$gasData);
                                $otherDocs[] = asset('client_pdfs/RRH_client/Terms&conditions/Spring-NJ-Contract-TCs-English.pdf');
                            }
                            
                            else if(strtolower($gasData['product_type']) == 'variable' && strtolower($leadData->Language) == 'es')
                            {
                                // send pdf which accordingly this condition
                                $otherDocs[] = asset('client_pdfs/RRH_client/Terms&conditions/Spring-NJ-Contract-TCs-Spanish.pdf');
                            }
                            else {
                                $pdf = PDF::loadView('contractpdf/contractpdfnew', $data); 
                            }
                        }
                        else if(!in_array('gas',$commodities) && !in_array('electric',$commodities) ) {
                            $pdf = PDF::loadView('contractpdf/contractpdfnew', $data); 
                        }
                    }
                    //This condition is for state PA
                    else if (strtoupper($leadData->State) == "PA"){
                        
                        \Log::info($program_info['product_type']);
                        if(in_array('electric',$commodities)){
                            if(strtolower($electricData['product_type']) == 'variable' && strtolower($leadData->Language) == 'en')
                            {
                                \Log::info('In PA EN electric variable product');
                                $pdfElectric = PDF::loadView('contractpdf/RRH_Client/pa_electric_english', $data,$electricData);
                                $otherDocs[] = asset('client_pdfs/RRH_client/Terms&conditions/Spring-PA-Contract-TCs-v.08012020-English.pdf');
                            }
                            elseif(strtolower($electricData['product_type']) == 'variable' && strtolower($leadData->Language) == 'es'){
                                \Log::info('In PA ES electric variable product');
                                $pdfElectric = PDF::loadView('contractpdf/RRH_Client/pa_electric_spanish', $data,$electricData);
                                $otherDocs[] = asset('client_pdfs/RRH_client/Terms&conditions/Spring-PA-Contract-TCsv.08012020-Spanish.pdf');
                            }
                            else
                            {
                                $pdf = PDF::loadView('contractpdf/contractpdfnew', $data); 
                            }
                        }
                        if(in_array('gas',$commodities)){
                            
                            if(strtolower($gasData['product_type']) == 'variable' && strtolower($leadData->Language) == 'en')
                            {
                                \Log::info('In PA EN gas variable product');
                                $pdfGas = PDF::loadView('contractpdf/RRH_Client/pa_gas_english', $data,$gasData);
                                $otherDocs[] = asset('client_pdfs/RRH_client/Terms&conditions/Spring-PA-Contract-TCs-v.08012020-English.pdf');
                            }
                            elseif(strtolower($gasData['product_type']) == 'variable' && strtolower($leadData->Language) == 'es'){
                                \Log::info('In PA ES gas variable product');
                                $pdfGas = PDF::loadView('contractpdf/RRH_Client/pa_gas_spanish', $data,$gasData);
                                $otherDocs[] = asset('client_pdfs/RRH_client/Terms&conditions/Spring-PA-Contract-TCsv.08012020-Spanish.pdf');
                            }
                            else
                            {
                                $pdf = PDF::loadView('contractpdf/contractpdfnew', $data); 
                            }
                        }
                        elseif(!in_array('gas',$commodities) && !in_array('electric',$commodities) ) {
                            $pdf = PDF::loadView('contractpdf/contractpdfnew', $data); 
                        }
                    }
                    else {
                        $pdf = PDF::loadView('contractpdf/contractpdfnew', $data); 
                    }
                    
                    if((strtoupper($leadData->State) == "MD" || strtoupper($leadData->State) == "NJ" || strtoupper($leadData->State) == "PA")  && (strtolower($program_info['product_type']) == 'variable' || strtolower($program_info['product_type']) == 'fixed')) {
                        // for brochure attachemnet
                        if (strtolower($leadData->Language) == 'es') {
                            $otherDocs[] = asset('client_pdfs/RRH_client/Brochure/SPRING_5Reasons-Spanish.pdf');
                        } else {
                            $otherDocs[] = asset('client_pdfs/RRH_client/Brochure/SPRING_5Reasons-English.pdf');
                        }

                        // for cancellation notice 
                        if ((strtoupper($leadData->State) != "NJ") && (strtolower($program_info['product_type']) == 'variable' || strtolower($program_info['product_type']) == 'fixed')) {
                            $otherDocs[] = asset('client_pdfs/RRH_client/Cancellation_Notice/SpringNoticeofCancellation.pdf');
                        }

                        // for other documents
                        if (in_array('gas',$commodities) && strtolower($leadData->Language) == 'en' && (strtolower($gasData['product_type']) == 'variable' || strtolower($gasData['product_type']) == 'fixed')) {
                            $otherDocs[] = asset('client_pdfs/RRH_client/Other_document/CarbonOffsetLabel.pdf');
                        }if(in_array('electric',$commodities) && strtolower($leadData->Language) == 'en' && (strtolower($electricData['product_type']) == 'variable' || strtolower($electricData['product_type']) == 'fixed')) {
                            $otherDocs[] = asset('client_pdfs/RRH_client/Other_document/RECLabel2020.pdf');
                        }if(in_array('electric',$commodities) && strtolower($leadData->Language) == 'es' && (strtolower($electricData['product_type']) == 'variable' || strtolower($electricData['product_type']) == 'fixed')) {
                            $otherDocs[] = asset('client_pdfs/RRH_client/Other_document/RECLabel2020-Spanish.pdf');
                        }if(in_array('gas',$commodities) && strtolower($leadData->Language) == 'es' && (strtolower($gasData['product_type']) == 'variable' || strtolower($gasData['product_type']) == 'fixed')) {
                            $otherDocs[] = asset('client_pdfs/RRH_client/Other_document/CarbonOffsetLabel-Spanish.pdf');
                        }
                    }
                }
                // This is default contract for other clients
                else {
                    $pdf = PDF::loadView('contractpdf/contractpdfnew', $data); 
                }
           
                if(!empty($pdfGas)) {
                    $pdf = $pdfGas;
                }elseif(!empty($pdfElectric)) {
                    $pdf = $pdfElectric;
                }
                
                $awsFolderPath = config()->get('constants.aws_folder');
                // $filePath = config()->get('constants.CONTRACT_PDF_UPLOAD_PATH');
                $filePath = 'clients_data/' . $lead->client_id . '/' . config()->get('constants.CLIENT_CONTRACTS_PATH');
                $path = '';
                $fileName = $leadid . '.pdf';

                $objStorageService = new StorageService;
                if(!empty($pdfGas) && !empty($pdfElectric)) {
                    info('pdf merging...');
                    // for merge gas and electric contract pdf
                    $storage = Storage::disk('local');
                    $tempPath = "temp/";
                    if (!$storage->exists($tempPath)) {
                        $storage->makeDirectory($tempPath);
                    }
                    $electricPath = $tempPath .time().'_electric.pdf';
                    $storage->put($electricPath, $pdfElectric->output(), 'public');
                    $gasPath = $tempPath .time().'_gas.pdf';
                    $storage->put($gasPath, $pdfGas->output(), 'public');
                    $merger = PdfMerger::init();
                    $merger->addPDF($storage->path($gasPath));
                    $merger->addPDF($storage->path($electricPath));
                    $merger->merge();

                    $path = $objStorageService->uploadFileToStorage($merger->save('contract.pdf','string'), $awsFolderPath, $filePath, $fileName);
                    $storage->delete([$gasPath, $electricPath]);

                    // for store child lead contracts
                    if ($leadType == 'child') {
                        $this->pdfChild[]['electric']=$pdfElectric->output();
                        $this->pdfChild[]['gas']=$pdfGas->output();
                    }
                } 
                else if($lead->client_id == config()->get('constants.CLIENT_SUNRISE_CLIENT_ID')){
                    // for merge gas and electric contract pdf
                    if(!empty($pdfElectric)){
                        $storage = Storage::disk('local');
                        $tempPath = "temp/";
                        if (!$storage->exists($tempPath)) {
                            $storage->makeDirectory($tempPath);
                        }
                        $electricPath = $tempPath .time().'_electric.pdf';
                        $storage->put($electricPath, $pdfElectric->output(), 'public');
                        $merger = PdfMerger::init();
                        $merger->addPDF($storage->path($electricPath));
                        $merger->addPDF(public_path('client_pdfs/Sunrise_client/Contract_TOS.pdf'));
                        $merger->merge();
        
                        $path = $objStorageService->uploadFileToStorage($merger->save('contract.pdf','string'), $awsFolderPath, $filePath, $fileName);
                        $storage->delete($electricPath);

                        // for store child lead contracts
                        if ($leadType == 'child') {
                            $this->pdfChild[]['electric']=$pdfElectric->output();
                        }
                    }
                }
                else {
                    $path = $objStorageService->uploadFileToStorage($pdf->output(), $awsFolderPath, $filePath, $fileName);

                    // for store child lead contracts
                    if ($leadType == 'child') {
                        $this->pdfChild[]['']=$pdf->output();
                    }
                }
                \Log::info($path);

                //set email for sending contract
                $toEmail = (isset($leadData->Email)) ? $leadData->Email : "";
                                
                if ($path !== false) {
                    $lead->update(['contract_pdf'=>$path]);
                    $updatedLead = Telesales::find($leadid);
                    Log::info('Contract pdf saved' . Storage::disk('s3')->url(array_get($updatedLead, 'contract_pdf')));
                    // for default client's message
                    $greeting = " Dear $leadData->FirstName,";
                    $mainMessage = "Welcome to ".$leadData->client_name."'s energy program, thank you for enrolling. ";
                    $mainMessage .= "Attached you will find a signed copy of your contract including the full terms and conditions.<br><br>";
                    $mainMessage .= "If you have any further questions, please contact ".$leadData->client_name."'s Customer Service Team at ".$tpvNumber.".";
                    //for Sunrise Clients email template
                    if($lead->client_id == config()->get('constants.CLIENT_SUNRISE_CLIENT_ID')){
                        
                        $customerFUllName = $customer_info['firstname']."&nbsp;".$customer_info ['middlename']."&nbsp;".$customer_info['lastname'];

                        $subject = "Residential Enrollment Successful";
                        $to = $toEmail;
                        $salutation = $leadData->client_name;
                        if(isset($electricData) && !empty($electricData)){
                            $contractPath = Storage::disk('s3')->url(array_get($updatedLead, 'contract_pdf'));
                            $mainMessage = "<html><body>";
                            $mainMessage .= "<center> <img src=".$customer_info['client_logo']."  height='150px' width='200px'></center><br/><br/>";
                            $mainMessage .= "<div style='padding-left:200px;padding-right:200px;'>";
                            $mainMessage .= "<div>";
                            $mainMessage .= "<p>Thank you for your enrollment with Sunrise Power & Gas.     We have
                                            submitted your enrollment to your local utility and 
                                            your service is expected to be switched to Sunrise Power &  Gas in <b>3 to 5 business days,
                                            unless you specified a future date for your enrollment.</b>     If, for some reason there is an issue in 
                                            processing your order with your utility, we will update you     and work with you and the utility to 
                                            try and resolve the issue in an expeditious manner.</p>";
                            $mainMessage .= "</div><br/><br/>";
                            $mainMessage .= "<div><center><a href='$contractPath' style=' text-decoration:none;background:#0a4e8f;padding:5px; color:white;'>View Contract Documents</a></center><br/><br/></div>";
                            $mainMessage .= "<div>";
                            $mainMessage .= "<p style='border-bottom:1px solid black;color:#261a55; font-size:24px;'><b>ACCOUNT INFORMATION</b></p>";
                            $mainMessage .= "<p style='margin-bottom: 4px;'><b>Reference Number</b></p>";
                            $mainMessage .= "<p style='margin-top: 0px;'>$lead->refrence_id</p>";
                            $mainMessage .= "<p style='margin-bottom: 4px;'><b>Contact Name</b></p>";
                            $mainMessage .= "<p style='margin-top: 0px;'>$customerFUllName</p>";
                            $mainMessage .= "<p style='margin-bottom: 4px;'><b>Email Address</b></p>";
                            $mainMessage .= "<p style='margin-top: 0px;'>$leadData->Email</p>";
                            $mainMessage .= "</div>";
                            $mainMessage .= "</div>";
                            $mainMessage .= "<div style='padding-left:200px;padding-right:200px;'><p    style='margin-bottom: 4px;'><b>Phone Number</b></p>
                                            <p style='margin-top: 0px;'>".$customer_info['Phone']."<p>";
                            $mainMessage .= "<p style='border-bottom:1px solid black;color:#261a55; font-size:24px;'><b>Service Details</b></p>";
                            $mainMessage .= "<p style='margin-bottom: 4px;'><b>Service Address</b></p>";
                            $mainMessage .= "<p style='margin-top: 0px;margin-bottom: 0px;'>".  $electricData['service_addr_line_1'].$electricData['service_addr_line_2'].",".$electricData['service_addr_city'].",".$electricData['service_addr_county'].",".$electricData['service_addr_state'].",".$electricData['service_addr_zipcode']."</p>";
                            $mainMessage .= "<p style='margin-bottom: 4px;'><b>Utility</b></p>";
                            $mainMessage .= "<p style='margin-top: 0px;'>".$electricData['UtilityName'].   "</p>";
                            $mainMessage .= "<p style='margin-bottom: 4px;'><b>Utility Account Number</ b></p>";
                            $mainMessage .= "<p style='margin-top: 0px;'>".$electricData['account_number']  ."</p>";
                            $mainMessage .= "<p style='border-bottom:1px solid black;margin-top:30px;   color:#261a55;font-size:24px;'><b>PLAN INFORMATION</b></p>";
                            $mainMessage .= "<p style='margin-bottom: 4px;'><b>Plan Name</b></p>";
                            $mainMessage .= "<p style='margin-top: 0px;'>".$electricData['program_name'].   "</p>";
                            $mainMessage .= "<p style='margin-bottom: 4px;'><b>Energy Charge</b></p>";
                            $mainMessage .= "<p style='margin-top: 0px;'>".$electricData['rate']." <span>&#162;</span> per ".   $electricData['unit'] ."</p>";
                            $mainMessage .= "<p style='margin-bottom: 4px;'><b>Product Type</b></p>";
                            $mainMessage .= "<p style='margin-top: 0px;'>Fixed</p>";
                            $mainMessage .= "<p style='margin-bottom: 4px;'><b>Term</b></p>";
                            $mainMessage .= "<p style='margin-top: 0px;'>".$electricData['term']."  months</p>";
                            $mainMessage .= "<p style='margin-bottom: 4px;'><b>Early Termination Fee</b></  p>";
                            $mainMessage .= "<p style='margin-top: 0px;'>$".$electricData['etf']." (One-time Fee)</p>";
                            $mainMessage .= "<p style='margin-top: 40px;'>If you have any questions     regarding your enrollment, please contact 
                                                        us via email at enroll@sunriseenergy.com or by  phone at 1-888-538-7001.
                                            </p>";
                            $mainMessage .= "<p>Business Hours: Monday- Friday 9AM-6PM EST</p>";
                            $mainMessage .= "<p>PA License # A-2017-2618194</p>";
                            $mainMessage .= "</div></body></html>";
                        }
                    }
                    else{
                        //Sending Contract package Mail to client
                        $subject = "TPV360 contract";
                        $to = $toEmail;
                        $salutation = $leadData->client_name;

                        if($mngFlag == 1 || $ugpFlag == 1)
                        {
                            if($mngFlag == 1){
                                $contractName = "Michigan Natural Gas";
                                $contractEmail = "info@michigannaturalgasllc.com";
                                $contractPhone = "(888) 988-6424";
                            }
                            if($ugpFlag == 1){
                                $contractName = "Utility Gas and Power";
                                $contractEmail = "Info@utilitygasandpower.com";
                                $contractPhone = "(855) 747-4931";
                            }
                            $mainMessage = " Hello $leadData->FirstName,<br/><br/>";
                            $mainMessage .= "Thank you for Choosing $contractName as your alternative energy supplier! If you have any
                            further questions you may reach us by email at $contractEmail or to speak to a
                            friendly account manager you may call: $contractPhone. <br/><br/>";
                            $mainMessage .= 'Monday-Friday 8:00 AM - 5:00 PM EST.<br/><br/>';
                            $mainMessage .= 'Your confirmation Number is: '.array_get($updatedLead, 'refrence_id').'.<br/><br/><br/>';
                            $mainMessage .= 'Regards,<br/>'.$salutation.'.<br/>';
                        }
                    }
                    if ($to != "") {
                        if($leadType == 'parent'){
                            $pdfChild = $this->pdfChild;
                            Mail::send([], [], function($message) use ($subject,$lead, $to, $pdf,$pdfGas,$pdfElectric,$updatedLead, $otherDocs,$mainMessage,$pdfChild) {

                                if($lead->client_id == config()->get('constants.CLIENT_SUNRISE_CLIENT_ID')){
                                    $file = Storage::disk('s3')->url(array_get($updatedLead, 'contract_pdf'));
                                    $message->attach($file,['as' =>'Contract.pdf']);
                                    if(count($pdfChild) > 0){
                                        foreach ($pdfChild as $key => $value) {
                                            if(isset($value['electric'])){
                                                $message->attachData($value['electric'],'Contract Child Electric '.($key+1).'.pdf');
                                            }
                                            else if(isset($value['gas'])){
                                                $message->attachData($value['gas'],'Contract Child Gas '.($key+1).'.pdf');
                                            }
                                            else{
                                                $message->attachData($value[''],'Contract Child '.($key+1).'.pdf');
                                            }
                                        }
                                    }
                                }
                                else{
                                    if(!empty($pdfElectric)) {
                                        $message->attachData($pdfElectric->output(),'Contract Electric.pdf');
                                    }
                                    if(!empty($pdfGas)) {
                                        $message->attachData($pdfGas->output(),'Contract Gas.pdf');
                                    }
                                    if(empty($pdfElectric) && empty($pdfGas)) {
                                        $message->attachData($pdf->output(),'Contract.pdf');
                                    }

                                    if(!empty($otherDocs)){
                                        foreach ($otherDocs as $key => $doc) {
                                            $message->attach($doc);
                                        }
                                    }
                                }                                
                                if(count($pdfChild) > 0){
                                    foreach ($pdfChild as $key => $value) {
                                        if(isset($value['electric'])){
                                            $message->attachData($value['electric'],'Contract Child Electric '.($key+1).'.pdf');
                                        }
                                        else if(isset($value['gas'])){
                                            $message->attachData($value['gas'],'Contract Child Gas '.($key+1).'.pdf');
                                        }
                                        else{
                                            $message->attachData($value[''],'Contract Child '.($key+1).'.pdf');
                                        }
                                    }
                                }
                                $message->to($to);
                                $message->subject($subject);
                                $message->setBody($mainMessage, 'text/html');
                            });
                    
                            TextEmailStatistics::create(['type'=>1]);
                            Log::info("Contract package send to mail address: " . $to . " for lead id: " . $leadid);
                        }
                    } else {
                        Log::error("No email address found to send contract package with lead id: " . $leadid);
                    } 
                    
                } 
                else {
                    Log::error("Unable to generate pdf for lead with id: " . $leadid);
                }
            }
            else {
                Log::error("No data found for lead");
            }
        } catch (\Exception $e) {
            Log::error("Getting error while generate contract".$e);
        }
    }
}
