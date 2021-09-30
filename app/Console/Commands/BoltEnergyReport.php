<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Storage;
use Illuminate\Http\File;
use App\models\Telesales;
use App\models\TwilioLeadCallDetails;
use App\models\Leadmedia;
use Carbon\Carbon;
use Log;
use DB;
use App\Services\StorageService;
use App\models\Zipcodes;

class BoltEnergyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'makereport:boltenegry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used for generate Bolt Enegry Report.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info("In the handle function of command to generate Bolt Enegry Report");
        
        Log::info("Start to fetching telesales data");
        
        /* Startdate and enddate as per client specific timezone (currently timezone is America/Toronto) */
        $startDate = Carbon::now(getClientSpecificTimeZone())->subDays(1)->setTimezone('UTC');
        $endDate = Carbon::now(getClientSpecificTimeZone())->setTimezone('UTC');
        Log::info("Telesales data fetch from: ".$startDate." To: ".$endDate);
        /* Query to retrieve all the leads data created between time range */
        $leads = Telesales::leftjoin('telesales_programs', 'telesales.id', 'telesales_programs.telesale_id')
                            ->leftjoin('programs', 'telesales_programs.program_id', '=', 'programs.id')
                            ->leftjoin('utilities', 'programs.utility_id', '=', 'utilities.id')
                            ->leftjoin('commodities', 'commodities.id', '=', 'utilities.commodity_id')
                            ->leftjoin('users', 'telesales.user_id', '=', 'users.id')
                            ->leftjoin('salesagent_detail', 'users.id', '=', 'salesagent_detail.user_id')
                            ->leftjoin('salescenters','users.salescenter_id','=','salescenters.id')
                            
                            ->select('telesales.id as id', 'telesales_programs.program_id', 'programs.name as program_name', 
                                'utilities.id as utility_id', 'utilities.utilityname as UtilityName', 'commodities.id as commodity_id','utilities.market as Abbreviation', 
                                'commodities.name as commodity_name', 'users.userid as salesperson_code', 'users.first_name as salesperson_first_name', 'users.last_name as salesperson_last_name',
                                'telesales.refrence_id as voice_verif_code', 'telesales.refrence_id as ext_customer_id', 'telesales.created_at as date_of_verified','telesales.status as status', 
                                'programs.code as product_code', 'telesales.s3_recording_url',
                                'salesagent_detail.external_id as external_id','programs.rate as rate_plan','salescenters.name as salescenter_name','telesales.verification_method',
                                // 'client_twilio_numbers.type as verification_type',
                                \DB::raw("(select count(commodity_id) from form_commodities where form_id = telesales.form_id) as commodities_count"),
                                \DB::raw("(select count(id) from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null) as service_address_count"),
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and deleted_at is null and is_primary = true LIMIT 1) and  meta_key = 'first_name' and telesale_id =telesales.id LIMIT 1) as cust_first_name"),
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and deleted_at is null and is_primary = true LIMIT 1) and  meta_key = 'middle_initial' and telesale_id =telesales.id LIMIT 1) as cust_middle_initial"),
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and deleted_at is null and is_primary = true LIMIT 1) and  meta_key = 'last_name' and telesale_id =telesales.id LIMIT 1) as cust_last_name"),
                                /* Query to retrieve Service Address City */
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_city' and telesale_id =telesales.id LIMIT 1) as service_addr_city"),
                                
                                /* Query to retrieve Service Address Zipcode */
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_zipcode' and telesale_id =telesales.id LIMIT 1) as service_addr_zipcode"),                                

                                /* Query to retrieve Account Number */
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 THEN
                                        (select id from form_fields where type = 'textbox' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Account number%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'textbox' and form_id = telesales.form_id and deleted_at is null and LOWER(label) LIKE '%account number%' LIMIT 1)
                                    END )  
                                    and  meta_key = 'value' and telesale_id =telesales.id LIMIT 1) as account_number"),
                                
                                /* Query to retrieve Phone Number */
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'phone_number' and form_id = telesales.form_id and deleted_at is null and is_primary = true LIMIT 1) and  meta_key = 'value' and telesale_id = telesales.id LIMIT 1) as contact_phone_no"),
                                
                                /* Query to retrieve State */
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_state' and telesale_id =telesales.id LIMIT 1) as state"),
                                
                                /* Query to retrieve Meter Number */
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'textbox' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Meter Number%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'textbox' and form_id = telesales.form_id and deleted_at is null and label LIKE 'Meter Number%' LIMIT 1)
                                    END )  
                                    and  meta_key = 'value' and telesale_id =telesales.id LIMIT 1) as meter_number"),

                                /* Query to retrieve Service Address Line 1 */
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_address_1' and telesale_id =telesales.id LIMIT 1) as service_addr_line_1"),

                                /* Query to retrieve Service Address Line 2 */
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_address_2' and telesale_id =telesales.id LIMIT 1) as service_addr_line_2")
                            )
                            ->where('telesales.status', '=', config('constants.LEAD_TYPE_VERIFIED'))       
                            ->where('telesales.client_id', '=', config('constants.CLIENT_BOLT_ENEGRY_CLIENT_ID'))       
                            ->whereBetween('telesales.created_at',[$startDate,$endDate])
                            ->get();

                            Log::info("Successfully fetched telesales data from database, Now set columns as per our requirements.");
                            foreach ($leads as $key => $lead) {

                                /* For get short state name */
                                    // $shortState = array_search($lead['state'], config('constants.USA_STATE_ABBR'));
                                if (isset($lead['service_addr_zipcode']) && !empty($lead['service_addr_zipcode'])) {
                                    $stateDetails = Zipcodes::where('zipcode', $lead['service_addr_zipcode'])->first();    
                                } else {
                                    $stateDetails = null;
                                }
                                
                                $data['SL'] = ($key + 1);
                                $data['VerifiedTime'] = Carbon::parse($lead['date_of_verified'])->setTimezone(getClientSpecificTimeZone())->format('m/d/y'.' '.'H:m'.' '.'A');
                                $data['Customer_First_Name'] = $lead['cust_first_name'];
                                $data['Middle_Name'] = isset($lead['cust_middle_initial']) ? $lead['cust_middle_initial'] : '';                                
                                $data['Last_Name'] = $lead['cust_last_name'];
                                $data['House_Number'] = '';
                                $data['Street_Prefix'] = '';
                                $data['Address'] = (isset($lead['service_addr_line_1']) ? $lead['service_addr_line_1'] : '') .' ' .(isset($lead['service_addr_line_2']) ? $lead['service_addr_line_2'] : '');
                                $data['Street_Suffix'] = '';
                                $data['CityName'] = $lead['service_addr_city'];
                                $data['state'] = isset($stateDetails) ? $stateDetails->state : '';
                                $data['ZipCode'] = $lead['service_addr_zipcode'];
                                $data['Bolt_Tracking#'] = $lead['ext_customer_id'];
                                $data['Phone'] = isset($lead['contact_phone_no']) ? $lead['contact_phone_no'] : '';
                                $data['Agent_Id'] = isset($lead['external_id']) ? $lead['external_id'] : '';
                                $data['Estimated_Income_code'] = '';
                                $data['Account#'] = $lead['account_number'];
                                $data['Account_Type'] = ''; 
                                $data['comments'] = '';
                                $data['Gas/Electricity'] = $lead['commodity_name'];
                                $data['COMPLETE'] = 'yes';
                                $data['PASSED_REVIEW'] = '';
                                $data['CANCELED_REASON'] = '';
                                $data['Marketer ID'] = $lead['salescenter_name'];
                                $data['ESCO'] = 'bolt';
                                $data['Rate_Plan'] = $lead['product_code'];
                                $data['Residential_Indicator'] = '';
                                $data['Meter_Number'] = isset($lead['meter_number']) ? $lead['meter_number'] : '';
                                $data['Utility'] = $lead['Abbreviation'];
                                if((config()->get('constants.VERIFICATION_METHOD_FOR_DISPLAY.'.$lead['verification_method']) == 'Agent Inbound') 
                                    || (config()->get('constants.VERIFICATION_METHOD_FOR_DISPLAY.'.$lead['verification_method']) == 'TPV Now Outbound')){
                                    $data['Verification_Type'] = 'Live TPV';
                                }
                                else if(config()->get('constants.VERIFICATION_METHOD_FOR_DISPLAY.'.$lead['verification_method']) == 'IVR Inbound'){
                                    $data['Verification_Type'] = 'Automated TPV';    
                                }
                                else{
                                    $data['Verification_Type'] = '';    
                                }
                                
                                /* Push particular key data in final sheetData array */
                                $sheetData[$key] = $data;                                
                            }

                            if (empty($sheetData)) {
                                $sheetData[0] = ' ';
                            }        
        
        /* For create filename with date */
        // $datetime = Carbon::now();
        $date = $startDate->format("ymd");
        $fileName = $date."_Bolt_Energy_TPV360_Sales_Report.xlsx";
        
        /* Generate excel sheet */
        $boltEnegryExcelSheet  =  Excel::create($fileName, function($excel) use ($sheetData) {
                            $excel->sheet('sheet1', function($sheet) use ($sheetData)
                            {
                                $sheet->fromArray($sheetData);
                            });
                        })->string('xlsx');

        Log::info("BoltEnegryReport is generated");
        /* Code for store report in aws path */
        $clientId = config('constants.CLIENT_BOLT_ENEGRY_CLIENT_ID');
        $awsFolderPath = config()->get('constants.aws_folder');
        $filePath = "clients_data/".$clientId."/reports/enrollment_reports/";
        $objStorageService = new StorageService;
        $path = $objStorageService->uploadFileToStorage($boltEnegryExcelSheet, $awsFolderPath, $filePath, $fileName);
        Log::info("BoltEnegryReport uploaded to s3 bucket on this path: " .$path);

        /* Code to store report in client's FTP */
        if(config('constants.CLIENT_BOLT_ENEGRY_FTP_TRANSFER_ENROLLMENT_REPORT_ENABLED'))
        {
            Log::info("BoltEnegryReport ftp transfer is enabled.");
            $disk_ftp = "boltenegry_ftp";
            $filePath = config('constants.CLIENT_BOLT_ENEGRY_FTP_TRANSFER_ENROLLMENT_REPORT_FOLDER');
            Log::info("BoltEnegryReport will upload in ftp path: " .$filePath);
            $objStorageService = new StorageService;
            $path = $objStorageService->uploadFileToFTP($boltEnegryExcelSheet,$filePath, $fileName,$disk_ftp);
            Log::info("BoltEnegryReport uploaded on path: ".$path);
        }
        else
        {
            Log::info("BoltEnegryReport ftp transfer is disabled.");
        }

        /* Code to store call recording in client's FTP */
        if(config('constants.CLIENT_BOLT_ENEGRY_FTP_TRANSFER_RECORDINGS_ENABLED'))
        {
            $disk_ftp = "boltenegry_ftp";
            $filePath = config('constants.CLIENT_BOLT_ENEGRY_FTP_TRANSFER_RECORDINGS_FOLDER');
            Log::info('Start fetching recording from Client ID: '.config('constants.CLIENT_BOLT_ENEGRY_CLIENT_ID'));
            $recordings = TwilioLeadCallDetails::leftjoin('telesales','twilio_lead_call_details.lead_id','telesales.id')
                ->where('twilio_lead_call_details.client_id', '=', config('constants.CLIENT_BOLT_ENEGRY_CLIENT_ID'))
                ->whereBetween('twilio_lead_call_details.created_at',[$startDate,$endDate])
                ->select('twilio_lead_call_details.id','twilio_lead_call_details.lead_id',
                    'twilio_lead_call_details.client_id',
                    'twilio_lead_call_details.recording_url',
                    'twilio_lead_call_details.created_at',
                    'telesales.refrence_id')
                ->get();
            Log::info('Client has '.$recordings->count()." recorded calls.");
            foreach ($recordings as $recording) {
                if (!empty($recording->recording_url)) {
                    $s3FilePath = $awsFolderPath .'clients_data/'.$recording->client_id . '/'.config()->get('constants.TPV_RECORDING_UPLOAD_PATH').basename($recording->recording_url);
                    Log::info("Recording is store to this path: ".$s3FilePath);
                    $exists = Storage::disk('s3')->exists($s3FilePath);
                    if($exists) {
                        Log::info("Recording is exists on this path");
                        $fileContents = Storage::disk('s3')->get($s3FilePath);
                        $recordingName = Carbon::parse($recording->created_at)->format('y-m-d-h-i-s')."-".$recording->id;
                        if($recording->refrence_id)
                        {
                            $recordingName .= "-".$recording->refrence_id;
                        }
                        $recordingName .= ".".pathinfo($s3FilePath, PATHINFO_EXTENSION);
                        $objStorageService = new StorageService;
                        $path = $objStorageService->uploadFileToFTP($fileContents,$filePath, $recordingName,$disk_ftp);
                        Log::info("Recording is transferd on this path: ".$path);

                    }
                    else
                    {
                        Log::info("Recording isn't exists on this path");
                    }
                }
            }
        }
        else
        {
            Log::info("Recording upload ftp transfer is disabled.");
            
        }

        /* Code to store acknowledgement pdf and Signature in client's FTP */
        Log::info('Start fetching signature from Client ID: '.config('constants.CLIENT_BOLT_ENEGRY_CLIENT_ID'));
        $signatures = Leadmedia::leftjoin('telesales','leadmedia.telesales_id','telesales.id')->where('telesales.client_id', '=', config('constants.CLIENT_BOLT_ENEGRY_CLIENT_ID'))
            ->whereIn('leadmedia.type',['acknowledgement'])
            //->whereIn('leadmedia.type',['acknowledgement','signature2','image'])
            ->whereBetween('leadmedia.created_at',[$startDate,$endDate])
            ->select('telesales.refrence_id','leadmedia.telesales_id','telesales.client_id',
                'leadmedia.url','leadmedia.created_at','leadmedia.type','leadmedia.media_type')
            ->get();
        Log::info('Client has '.$signatures->count()." signature.");
        foreach ($signatures as $signature) {
            $disk_ftp = "boltenegry_ftp";
            $filePath = config('constants.CLIENT_BOLT_ENEGRY_FTP_TRANSFER_E_SIGNATURE_FOLDER');
            if (!empty($signature->url)) {
                $s3FilePath = $awsFolderPath .''.$signature->url;
                Log::info("Signature is store to this path: ".$s3FilePath);
                $exists = Storage::disk('s3')->exists($s3FilePath);
                if($exists) {
                    Log::info("Signature is exists on this path");
                    $fileContents = Storage::disk('s3')->get($s3FilePath);
                    $signatureName = "";
                    if($signature->refrence_id)
                    {
                        $signatureName .= $signature->refrence_id;
                    }
                    $signatureName .= "-".Carbon::parse($signature->created_at)->format('Y-m-d-h-i-s');
                    // if($signature->type == "signature2"){
                    //     $signatureName .= "-ACK";
                    // }
                    $signatureName .= ".".pathinfo($s3FilePath, PATHINFO_EXTENSION);
                    if($signature->type == "acknowledgement"){
                        $filePath = config('constants.CLIENT_BOLT_ENEGRY_FTP_TRANSFER_ACKNOWLEDGE_FOLDER');
                    }
                    $objStorageService = new StorageService;
                    $path = $objStorageService->uploadFileToFTP($fileContents,$filePath, $signatureName,$disk_ftp);
                    Log::info("Signature is transferd on this path: ".$path);

                }
                else
                {
                    Log::info("Signature isn't exists on this path");
                }
            }
        }

        // For store contracts pdf in client's FTP

        $disk_ftp = "boltenegry_ftp";
        $filePath = config('constants.CLIENT_BOLT_ENEGRY_FTP_TRANSFER_CONTRACTS_FOLDER');
        $failedLeadIds = [];
        $objStorageService = new StorageService;
        $telesales = Telesales::where('client_id',$clientId)->whereBetween('created_at',[$startDate,$endDate])->get();
        foreach($telesales as $telesale) {
            if(!empty($telesale->contract_pdf)) {
                $s3FilePath = $awsFolderPath .$telesale->contract_pdf;
                $exists = Storage::disk('s3')->exists($s3FilePath);
                if($exists) {
                    $fileContents = Storage::disk('s3')->get($s3FilePath);
                    $contractName = $telesale->refrence_id."-".Carbon::parse($telesale->created_at)->format('Y-m-d-h-i-s').'.pdf';
                    $path = $objStorageService->uploadFileToFTP($fileContents,$filePath, $contractName,$disk_ftp);
                    Log::info("Contracts is transferd on this path: ".$path);

                    if ($path === false || empty($path)) {
                        $failedLeadIds[] = $telesale->id;
                    }
                }

            } else {
                Log::info("Contracts not found for this Lead Id: ".$telesale->id);
            }
        }

        if (!empty($failedLeadIds)) {
            $this->restoreFailedContracts($failedLeadIds);
        }

        $dateFormat = $startDate->format("yy-m-d");
        Log::info("Succefully created and stored excel sheet ".$fileName." for Bolt Enegry Data, date : ".$dateFormat);
        echo "\n\n Succefully created and stored excel sheet $fileName for Bolt Enegry Data, date : $dateFormat \n\n";
    }

    /**
     * Restore contracts on FTP server if failed upload on FTP server
     * @param $leadIds
     */
    public function restoreFailedContracts($leadIds) {
        try {
            Log::info("Start restore failed contracts...");
            Log::info("Failed Lead Ids: ".print_r($leadIds));
            if (!empty($leadIds)) {
                \Artisan::call('contracts:generate',['--l' => $leadIds]);
            }
            Log::info("End restore failed contracts...");
        } catch (\Exception $e) {
            Log::error("Getting error while restore contract: ".$e);
        }
    }
}
