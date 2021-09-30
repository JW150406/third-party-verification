<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Storage;
use Illuminate\Http\File;
use App\models\Telesales;
use App\models\TwilioLeadCallDetails;
use Carbon\Carbon;
use Log;
use DB;
use App\Services\StorageService;

class RrhEnrollmentReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'makereport:rrhenrollment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used for generate RRH Enrollment Report on daily basis.';

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
        Log::info("In the handle function of command to generate RRH Enrollment Report");
        Log::info("Start to fetching telesales data");
        
        // Startdate and enddate as per clint specific timezone (currently timezone is America/Toronto)
        $startDate = Carbon::now(getClientSpecificTimeZone())->subDays(1)->setTimezone('UTC');
        $endDate = Carbon::now(getClientSpecificTimeZone())->setTimezone('UTC');
        Log::info("Telesales data fetch from: ".$startDate." To: ".$endDate);
        // Query to retrieve all the leads data created today
        $leads = Telesales::leftjoin('telesales_programs', 'telesales.id', 'telesales_programs.telesale_id')
                            ->leftjoin('programs', 'telesales_programs.program_id', '=', 'programs.id')
                            ->leftjoin('utilities', 'programs.utility_id', '=', 'utilities.id')
                            ->leftjoin('commodities', 'commodities.id', '=', 'utilities.commodity_id')
                            ->leftjoin('users', 'telesales.user_id', '=', 'users.id')
                            ->leftjoin('salesagent_detail', 'telesales.user_id', '=', 'salesagent_detail.user_id')
                            ->leftjoin('telesales_zipcodes','telesales_zipcodes.telesale_id','=','telesales.id')
                            ->leftjoin('zip_codes','zip_codes.id','=','telesales_zipcodes.zipcode_id')
                            ->select('telesales.id as id', 'telesales_programs.program_id', 'programs.name as program_name','zip_codes.state as ServiceState', 
                                'utilities.id as utility_id', 'utilities.utilityname as UtilityName', 'utilities.fullname as UtilityFullname','utilities.market as UtilityAbbrivation', 'commodities.id as commodity_id', 
                                'commodities.name as commodity_name', 'users.userid as salesperson_code','salesagent_detail.external_id as salesperson_external_code', 'users.first_name as salesperson_first_name', 'users.last_name as salesperson_last_name',
                                'telesales.refrence_id as voice_verif_code', 'telesales.refrence_id as ext_customer_id', 'telesales.created_at as date_of_sale', 
                                'programs.code as product_code', 'telesales.s3_recording_url',
                                \DB::raw("(select count(commodity_id) from form_commodities where form_id = telesales.form_id) as commodities_count"),
                                \DB::raw("(select count(id) from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null) as service_address_count"),
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and deleted_at is null and is_primary = true LIMIT 1) and  meta_key = 'first_name' and telesale_id =telesales.id LIMIT 1) as cust_first_name"),
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and deleted_at is null and is_primary = true LIMIT 1) and  meta_key = 'middle_initial' and telesale_id =telesales.id LIMIT 1) as cust_middle_initial"),
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and deleted_at is null and is_primary = true LIMIT 1) and  meta_key = 'last_name' and telesale_id =telesales.id LIMIT 1) as cust_last_name"),
                                
                                // service_addr_line_1
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_address_1' and telesale_id =telesales.id LIMIT 1) as service_addr_line_1"),
                                
                                // service_addr_apart_no
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_unit' and telesale_id =telesales.id LIMIT 1) as service_addr_apart_no"),

                                // service_addr_line_2
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_address_2' and telesale_id =telesales.id LIMIT 1) as service_addr_line_2"),
                                
                                // service_addr_city
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_city' and telesale_id =telesales.id LIMIT 1) as service_addr_city"),
                                
                                // service_addr_zipcode
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_zipcode' and telesale_id =telesales.id LIMIT 1) as service_addr_zipcode"),
                                
                                // postal_addr_line_1
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'billing_address_1' and telesale_id =telesales.id LIMIT 1) as postal_addr_line_1"),

                                // postal_addr_line_2
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'billing_address_2' and telesale_id =telesales.id LIMIT 1) as postal_addr_line_2"),

                                // postal_addr_city
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'billing_city' and telesale_id =telesales.id LIMIT 1) as postal_addr_city"),
                                
                                // postal_addr_zipcode
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'billing_zipcode' and telesale_id =telesales.id LIMIT 1) as postal_addr_zipcode"),

                                // contact_first_name
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 THEN
                                        (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and deleted_at is null and label LIKE 'Billing Name' LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and deleted_at is null and label LIKE 'Billing Name' LIMIT 1)
                                    END )  
                                    and  meta_key = 'first_name' and telesale_id =telesales.id LIMIT 1) as contact_first_name"),
                                
                                // contact_middle_initial
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                        CASE WHEN commodities_count > 1 THEN
                                            (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and deleted_at is null and label LIKE 'Billing Name' LIMIT 1)
                                            ELSE 
                                            (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and deleted_at is null and label LIKE 'Billing Name' LIMIT 1)
                                        END )  
                                        and  meta_key = 'middle_initial' and telesale_id =telesales.id LIMIT 1) as contact_middle_initial"),
                                
                                // contact_last_name
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                        CASE WHEN commodities_count > 1 THEN
                                            (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and deleted_at is null and label LIKE 'Billing Name' LIMIT 1)
                                            ELSE 
                                            (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and deleted_at is null and label LIKE 'Billing Name' LIMIT 1)
                                        END )  
                                        and  meta_key = 'last_name' and telesale_id =telesales.id LIMIT 1) as contact_last_name"),

                                // Account Number (ESI no)
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 THEN
                                        (select id from form_fields where type = 'textbox' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Account number%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'textbox' and form_id = telesales.form_id and deleted_at is null and label LIKE 'Account Number' LIMIT 1)
                                    END )  
                                    and  meta_key = 'value' and telesale_id =telesales.id LIMIT 1) as account_number"),
                                
                                // Customer Number
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'textbox' and form_id = telesales.form_id and deleted_at is null and label LIKE 'Customer Number' LIMIT 1) and  meta_key = 'value' and telesale_id = telesales.id LIMIT 1) as customer_number"),
                                
                                // phone number
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'phone_number' and form_id = telesales.form_id and deleted_at is null and is_primary = true LIMIT 1) and  meta_key = 'value' and telesale_id = telesales.id LIMIT 1) as contact_phone_no"),
                                
                                // email address
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'email' and form_id = telesales.form_id and deleted_at is null and is_primary = true LIMIT 1) and  meta_key = 'value' and telesale_id = telesales.id LIMIT 1) as contact_email_address"),
                                
                                // preffered language
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'radio' and form_id = telesales.form_id and deleted_at is null LIMIT 1) and  meta_key = 'value' and telesale_id = telesales.id LIMIT 1) as preffered_language"),

                                // state
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_state' and telesale_id =telesales.id LIMIT 1) as state"),

                                // commodity (service type)
                                \DB::raw("UPPER(LEFT(commodities.name , 1)) as service_type"),

                                // Ecgold Program Code
                                \DB::raw("(select 
                                            (CASE WHEN telesalesdata.meta_value = '3% Cash Back' THEN '12MECGCASH' 
                                            WHEN telesalesdata.meta_value = '5% Ecogold Rewards' THEN 'ECGREWARD' 
                                            WHEN telesalesdata.meta_value = '5% Rewards' THEN 'ECGREWARD' 
                                            WHEN telesalesdata.meta_value = 'Spring Guard' THEN 'ECGHGUARDSPR' 
                                            WHEN telesalesdata.meta_value = 'Kiwi Guard' THEN 'ECGHGUARD' 
                                            WHEN telesalesdata.meta_value = 'EcoGold Base' THEN 'ECGBASE' 
                                            ELSE telesalesdata.meta_value = '' END)
                                    from telesalesdata where field_id = (
                                    select id from form_fields where type = 'selectbox' and form_id = telesales.form_id and deleted_at is null and label LIKE '%Ecogold Program%' LIMIT 1) 
                                    and  meta_key = 'value' and telesale_id = telesales.id LIMIT 1)  
                                    as ecgold_program_code"),

                                // Promo Code
                                \DB::raw("(select 
                                            (CASE WHEN telesalesdata.meta_value = '$25 Gift Card 3mo' THEN 'TELEGC25' 
                                            WHEN telesalesdata.meta_value = '$200 Energy Efficiency' THEN 'RETMULTI200'
                                            WHEN telesalesdata.meta_value = '$500 Energy Efficiency' THEN '36MULTI'
                                            WHEN telesalesdata.meta_value = 'Not applicable' THEN ' ' 
                                            ELSE telesalesdata.meta_value = '' END)
                                    from telesalesdata where field_id = (
                                    select id from form_fields where type = 'selectbox' and form_id = telesales.form_id and deleted_at is null and label LIKE '%Promo Code%' LIMIT 1) 
                                    and  meta_key = 'value' and telesale_id = telesales.id LIMIT 1)  
                                    as promo_code")
                            )
                            ->where('telesales.status', '=', config('constants.LEAD_TYPE_VERIFIED'))       
                            ->where('telesales.client_id', '=', config('constants.CLIENT_RRH_CLIENT_ID'))       
                            ->whereBetween('telesales.created_at',[$startDate,$endDate])
                            ->get();
                            Log::info("Successfully fetched telesales data from database, Now set columns as per our requirements.");
                            foreach ($leads as $key => $lead) {

                                $data['action_code'] = 'New';
                                $data['salesperson_code'] = $lead['salesperson_code'];
                                $data['salesperson_name'] = $lead['salesperson_first_name']." ".$lead['salesperson_last_name'];
                                $data['voice_verif_code'] = $lead['voice_verif_code'];
                                $data['ext_customer_id'] = $lead['ext_customer_id'];
                                $data['date_of_sale'] = Carbon::parse($lead['date_of_sale'])->setTimezone(getClientSpecificTimeZone())->format(getDateFormat() .' '.getTimeFormat());
                                $data['cust_first_name'] = $lead['cust_first_name'];
                                $data['cust_middle_initial'] = isset($lead['cust_middle_initial']) ? $lead['cust_middle_initial'] : '';
                                $data['cust_last_name'] = $lead['cust_last_name'];
                                $data['ESI_id'] = $lead['account_number'];
                                $data['service_addr_type'] = $lead['service_addr_type'];
                                $data['service_addr_line_1'] = $lead['service_addr_line_1'];
                                $data['service_addr_apart_no'] = $lead['service_addr_apart_no'];
                                $data['service_addr_line_2'] = $lead['service_addr_line_2'];
                                $data['service_addr_city'] = $lead['service_addr_city'];
                                $data['service_addr_zipcode'] = $lead['service_addr_zipcode'];
                                $data['service_addr_property_name'] = '';
                                $data['switch_move_in_type'] = 'SWITCH';
                                $data['out_of_cycle_switch_yn'] = '';
                                $data['out_of_cycle_switch_date'] = '';
                                $data['move_in_date'] = '';
                                $data['priority_move_in_yn'] = '';
                                $data['product_code'] = $lead['product_code'];
                                $data['contact_first_name'] = $lead['contact_first_name'];
                                $data['contact_middle_initial'] = $lead['contact_middle_initial'];
                                $data['contact_last_name'] = $lead['contact_last_name'];
                                $data['postal_addr_line_1'] = $lead['postal_addr_line_1'];
                                $data['postal_addr_line_2'] = $lead['postal_addr_line_2'];
                                $data['postal_addr_city'] = $lead['postal_addr_city'];
                                $data['postal_addr_zipcode'] = $lead['postal_addr_zipcode'];
                                $data['contact_phone_no'] = $lead['contact_phone_no'];
                                $data['contact_alt_phone_no'] = '';
                                $data['contact_mobile_no'] = '';
                                $data['contact_email_address'] = $lead['contact_email_address'];
                                $data['contact_password'] = '';
                                $data['date_of_birth'] = '';
                                $data['preffered_contact_method'] = 'PHONE';
                                $data['preffered_language'] = $lead['preffered_language'];
                                $data['SSN'] = '';
                                $data['drivers_license'] = '';
                                $data['alt_identification'] = '';
                                $data['state'] = $lead['state'];
                                $data['service_type'] = $lead['service_type'];
                                $data['LOA'] = '';
                                $data['UtilityName'] = $lead['UtilityAbbrivation'];
                                $data['ecgold_program_code'] = $lead['ecgold_program_code'] ? $lead['ecgold_program_code'] : '';
                                $data['promo_code'] = $lead['promo_code'] ? $lead['promo_code'] : '';
                                $data['rate_promo'] = '';
                                $data['customer_no'] = $lead['customer_number'] ? $lead['customer_number'] : '';
                                $data['network_account_reference'] = '';
                                    
                                // Push particular key data in final sheetData array
                                $sheetData[$key] = $data;
                                
                            }
        
            
        if (empty($sheetData)) {
            $sheetData[0] = 'Leads are not available';
        }
        // For create filename with today's date
        // $datetime = Carbon::now();
        $date = $startDate->format("yymd");
        $fileName = $date."_RRH_TPV360_Sales_Report.xlsx";

        // Create and store excel sheet in storage
        $rrhExcelSheet  =  Excel::create($fileName, function($excel) use ($sheetData) {
                            $excel->sheet('sheet1', function($sheet) use ($sheetData)
                            {
                                $sheet->fromArray($sheetData);
                            });
                        })->string('xlsx');
        Log::info("rrhEnrollmentReport is generated");
        // Code for store report in aws path
        $clientId = config('constants.CLIENT_RRH_CLIENT_ID');
        $awsFolderPath = config()->get('constants.aws_folder');
        $filePath = "clients_data/".$clientId."/reports/enrollment_reports/";
        $objStorageService = new StorageService;
        $path = $objStorageService->uploadFileToStorage($rrhExcelSheet, $awsFolderPath, $filePath, $fileName);
        Log::info("rrhEnrollmentReport uploaded to s3 bucket on this path: " .$path);
        // Code to store report in client's FTP
        if(config('constants.CLIENT_RRH_FTP_TRANSFER_ENROLLMENT_REPORT_ENABLED'))
        {
            Log::info("rrhEnrollmentReport ftp transfer is enabled.");
            $disk_ftp = "rrh_ftp";
            $filePath = config('constants.CLIENT_RRH_FTP_TRANSFER_ENROLLMENT_REPORT_FOLDER');
            Log::info("rrhEnrollmentReport will upload in ftp path: " .$filePath);
            $objStorageService = new StorageService;
            $path = $objStorageService->uploadFileToFTP($rrhExcelSheet,$filePath, $fileName,$disk_ftp);
            Log::info("rrhEnrollmentReport uploaded on path: ".$path);
        }
        else
        {
            Log::info("rrhEnrollmentReport ftp transfer is disabled.");
        }


        // Code to store call recording in client's FTP
        if(config('constants.CLIENT_RRH_FTP_TRANSFER_RECORDINGS_ENABLED'))
        {
            $disk_ftp = "rrh_ftp";
            $filePath = config('constants.CLIENT_RRH_FTP_TRANSFER_RECORDINGS_FOLDER');
            Log::info('Start fetching recording from Client ID: '.config('constants.CLIENT_RRH_CLIENT_ID'));
            $recordings = TwilioLeadCallDetails::leftjoin('telesales','twilio_lead_call_details.lead_id','telesales.id')
                ->where('twilio_lead_call_details.client_id', '=', config('constants.CLIENT_RRH_CLIENT_ID'))
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
                        $recordingName = Carbon::parse($recording->created_at)->format('yy-m-d-h-i-s')."-".$recording->id;
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

        $dateFormat = $startDate->format("yy-m-d");
        Log::info("Succefully created and stored excel sheet ".$fileName." for RRH Enrollment Data, date : ".$dateFormat);
        echo "\n\n Succefully created and stored excel sheet $fileName for RRH Enrollment Data, date : $dateFormat \n\n";
    }
}
