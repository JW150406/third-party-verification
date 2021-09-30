<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\models\Commodity;
use App\Services\StorageService;
use Carbon\Carbon;
use App\models\Reports;
use App\models\Telesales;
use App\models\Client;
use App\Http\Controllers\Admin\DashboardController;
use App\models\Salescenterslocations;
use App\models\Salesagentlocation;
use App\models\Salescenter;
use App\models\Brandcontacts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use App\models\ComplianceReports;
use App\models\ComplianceTemplates;
use App\models\Utilities;
use App\models\Programs;
use App\models\Salesagentdetail;
use App\models\TextEmailStatistics;
use App\models\CriticalLogsHistory;
use App\models\SalesAgentActivity;
use App\models\TelesalesZipcode;
use App\models\Zipcodes;
use App\models\Settings;
use App\User;
use Illuminate\Support\Arr;
use Zipper;
use DataTables;
use PDF;
use ZipArchive;
use Storage;
use DB;
use Mail;
use Log;
use App\Services\CriticalLogsZipExportService;
use App\Jobs\CriticalLogsZipExportJob;
use App\models\TelesaleScheduleCall;

class SendEnrollmentReportMega extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendEnrollmentReportMega:verified';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will send all the verified enrollment report to the mega Energy';

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

        Log::info("In the handle function of command to send sales Center wise Enrollment Report for Mega Energy");
        $startDate = date("Y-m-d", strtotime(' -1 day'));
        $endDate = date("Y-m-d");
        
        $startDate = Carbon::parse($startDate, getClientSpecificTimeZone())->setTimezone('UTC');
        $endDate = Carbon::parse($endDate, getClientSpecificTimeZone())->setTimezone('UTC');

        $enrolment_templates = "";
        $enrolment_templates=[];

        $megaEnergyClientId = config()->get('constants.CLIENT_MEGA_ENERGY_ID');
        $megaEnergyEmails = explode(",", config()->get('constants.CLIENT_MEGA_ENERGY_ENROLLMENT_EXPORT_EMAILS'));
        
        Log::info("Start to fetching telesales data");
        // Fetch data from query of MegaEnrollmetReportController 
        $enrollmentData = app('App\Http\Controllers\Reports\MegaEnrollmetReportController')->getEnrollmentData($megaEnergyClientId, $startDate, $endDate);
        $enrollmentData = $enrollmentData->get();
        
        // Old Method (This is query is in below getEnrollmentDataForMegaEnergy method in this command)
        // $enrollmentData = $this->getEnrollmentDataForMegaEnergy($megaEnergyClientId, $startDate, $endDate);
        
        if(!empty($enrollmentData[0])){
          
            Log::info("enrollmentData is available.");
            // Groupby the query with SalesCenter
            $allSalesCenters = $enrollmentData->groupBy('SalesCenter');

            foreach ($allSalesCenters as $details) {
                $data = array();
                $enrollmentData = $details;
                
                // Prepare data for excel sheet
                foreach($enrollmentData as $enrollMent){
                    $Authorisedname = array_filter(explode(" ",$enrollMent->AuthorizedName));
                    $soldDate = Carbon::parse($enrollMent->SoldDateTime)->setTimezone(getClientSpecificTimeZone())->format("m/d/Y");
                    $soldTime = Carbon::parse($enrollMent->SoldDateTime)->setTimezone(getClientSpecificTimeZone())->format("H:i:s");
                    $checkServiceCounty = $enrollMent->ServiceCounty ? str_contains($enrollMent->ServiceCounty, 'County') : '';
                    $checkBillingCounty = $enrollMent->BillingCounty ? str_contains($enrollMent->BillingCounty, 'County') : '';
                    $data[] =array(
                        'Date' => $soldDate,
                        'Time' => $soldTime,
                        'First Name' => $enrollMent->AuthorizedName,
                        'Last Name' => $enrollMent->ServiceLastName,
                        'Acct Number' => $enrollMent->AccountNumber,
                        'Service Address' => implode(", ",array_filter([$enrollMent->ServiceAddress1,$enrollMent->ServiceAddress2])),
                        'Agent Code' => $enrollMent->SalesAgentID,
                        'Agent Name' => $enrollMent->SalesAgent,
                        'Commodity' => $enrollMent->Commodity,
                        'Term' => $enrollMent->Term,
                        'Rate' => $enrollMent->Rate,
                        'Status' => $enrollMent->Status,
                        'Vendor' => $enrollMent->SalesCenter,
                        'Utility' => $enrollMent->Utility,
                        'Customer Phone Number' => $enrollMent->Phone,
                        'Customer Class' => "R",
                        'Product Type' => $enrollMent->Programs,
                        'Confirmation Number' => $enrollMent->LeadID,
                        'Complete' => "Y",
                        'Verified' => "Y",
                        'Canceled' => "",
                        'Comments' => "",
                        'Customer Email' => $enrollMent->Email,
                        'Service Address ' => $enrollMent->ServiceAddress1, // Key added as space for a fix with same column name as previous
                        'Service Address Line 2' => $enrollMent->ServiceAddress2,
                        'Service City' => $enrollMent->ServiceCity,
                        'Service State' => $enrollMent->ServiceStateAbbrivated,
                        'Service Zip' => $enrollMent->ServiceZipcode,
                        'Service County' => $checkServiceCounty ? $enrollMent->ServiceCounty : $enrollMent->ServiceCounty." County",
                        'Billing Address' => $enrollMent->BillingAddress1,
                        'Billing Address Line 2' => $enrollMent->BillingAddress2,
                        'Billing City' => $enrollMent->BillingCity,
                        'Billing State' => $enrollMent->BillingStateAbbrivated,
                        'Billing Zip' => $enrollMent->BillingZipcode,
                        'Billing County' => $checkBillingCounty ? $enrollMent->BillingCounty : $enrollMent->BillingCounty." County",
                        'Name Key' => $enrollMent->NameKey,
                        'Billing Cycle' => $enrollMent->BillingCycleNumber,
                        'Date Of Birth' => "",
                        'Meter Number' => $enrollMent->MeterNumber,
                    );
                    
                }
                
                // For generate excel sheet
                $excelFile = Excel::create('export_sample', function($excel) use ($data) {
                    $excel->sheet('sheet1', function($sheet) use ($data)
                    {
                        $sheet->fromArray($data);
                    });
                });
                
                // For send mail with excel file
                $salesCenterName = $enrollMent->SalesCenter;
                $this->sendClientMail($megaEnergyEmails, $excelFile, $salesCenterName);

                Log::info("Succefully email sales center wise excel sheet.");
            }
        }else{
            Log::info("enrollmentData is not available.");
            echo "End Process";die;
        }
    }

    /**
     * Query for fetch Enrollment data for Mega Energy
     * @param $clientId,$startDate,$endDate
     * 
     */
    // public function getEnrollmentDataForMegaEnergy($clientId, $startDate, $endDate)
    // {
    //     Log::info("Query for fetch Enrollment data for Mega Energy");

    //     $enrollment = Telesales::leftJoin('users','users.id','=','telesales.user_id')
    //     ->leftJoin('clients','clients.id','=','telesales.client_id')
    //     ->leftJoin('telesales_programs','telesales_programs.telesale_id','=','telesales.id')
    //     ->leftJoin('programs','programs.id','=','telesales_programs.program_id')
    //     ->leftJoin('utilities','programs.utility_id','=','utilities.id')
    //     ->leftJoin('brand_contacts','utilities.brand_id','=','brand_contacts.id')
    //     ->leftJoin('salescenters','users.salescenter_id','=','salescenters.id')
    //     ->leftJoin('salesagent_detail','users.id','=','salesagent_detail.user_id')
    //     ->leftJoin('salescenterslocations','salescenterslocations.id','=','users.location_id')
    //     ->leftjoin('telesales_zipcodes','telesales_zipcodes.telesale_id','=','telesales.id')
    //     ->leftjoin('zip_codes','zip_codes.id','=','telesales_zipcodes.zipcode_id')
    //     ->leftjoin('dispositions','dispositions.id','=','telesales.disposition_id')
    //     ->leftJoin('users as tpvagents','tpvagents.id','=','telesales.reviewed_by')
    //     ->select(
    //         'clients.name as Client',
    //         DB::raw("( select name from brand_contacts where id IN(select brand_id from utilities where id IN (select utility_id from programs where id IN (select program_id from telesales_programs where telesale_id = telesales.id))) ) as Brand")
    //         ,'salescenters.name as SalesCenter','salescenterslocations.name as SalesCenterLocation','programs.term as Term','programs.rate as Rate',
    //         DB::raw("concat( users.first_name, ' ',users.last_name ) as SalesAgent"),
    //         'users.userid as SalesAgentID',DB::raw("CASE
    //         WHEN  salesagent_detail.agent_type = 'd2d' THEN 'Door-to-Door'
    //         WHEN  salesagent_detail.agent_type = 'tele' THEN 'Telemarketing'
    //         ELSE '' 
    //         END as 'Channel'"),'telesales.created_at as SoldDateTime','telesales.reviewed_at as TPVDate','telesales.refrence_id as LeadID','zip_codes.state as State',DB::raw("(CASE WHEN telesales.status = 'verified' THEN 'Verified' WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' WHEN telesales.status = 'expired' THEN 'Expired' ELSE 'Pending' END) as Status"),
    //         DB::raw("CASE
    //         WHEN telesales.status = 'cancel' THEN telesales.cancel_reason
    //         WHEN telesales.status = 'decline' THEN dispositions.description 
    //         WHEN telesales.status = 'hangup' THEN dispositions.description 
    //         ELSE ''
    //         END  as 'Reason'"),
    //         DB::raw("CASE
    //             WHEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'first_name' and telesale_id =telesales.id LIMIT 1)  != ''
    //             THEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'first_name' and telesale_id =telesales.id  LIMIT 1 )
    //             ELSE ''
    //             END  as 'AuthorizedName'"),
    //         DB::raw("CASE
    //             WHEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'last_name' and telesale_id =telesales.id  LIMIT 1)  != ''
    //             THEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'last_name' and telesale_id =telesales.id  LIMIT 1)
    //             ELSE ''
    //             END  as 'ServiceLastName'"),
    //         DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'Account Number' and form_id = telesales.form_id LIMIT 1) and telesale_id =telesales.id limit 1 ) as AccountNumber"),
    //         DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'NAME KEY' and form_id = telesales.form_id LIMIT 1) and telesale_id =telesales.id limit 1 ) as NameKey"),
    //         DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'BILLING CYCLE NUMBER' and form_id = telesales.form_id LIMIT 1) and telesale_id =telesales.id limit 1 ) as BillingCycleNumber"),
    //         // DB::raw("GROUP_CONCAT(utilities.market  SEPARATOR ', ') Utility"),
    //         DB::raw("( select GROUP_CONCAT(market  SEPARATOR ', ') from utilities where id IN (select utility_id from programs where id IN (select program_id from telesales_programs where telesale_id = telesales.id)) ) as Utility"),
    //         DB::raw("(select GROUP_CONCAT(commodities.name) from commodities left join form_commodities on form_commodities.commodity_id = commodities.id where form_commodities.form_id = telesales.form_id) as Commodity"),
            
    //         DB::raw("UPPER((select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and  meta_key = 'service_address_1' and telesale_id =telesales.id LIMIT 1)) as ServiceAddress1"),
    //         DB::raw("UPPER((select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_address_2' and telesale_id =telesales.id LIMIT 1)) as ServiceAddress2"),
    //         DB::raw("UPPER((select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_city' and telesale_id =telesales.id LIMIT 1)) as ServiceCity"),
    //         DB::raw("(select meta_value from telesalesdata where meta_key = 'service_state' and telesale_id =telesales.id LIMIT 1) as ServiceState"),
    //         DB::raw("(select meta_value from telesalesdata where meta_key = 'service_country' and telesale_id =telesales.id LIMIT 1) as ServiceCountry"),
    //         DB::raw("concat((select meta_value from telesalesdata where  field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_zipcode' and telesale_id =telesales.id LIMIT 1), ' ') as ServiceZipcode"),
    //         DB::raw("(select county from zip_codes WHERE zipcode = (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_zipcode' and telesale_id = telesales.id LIMIT 1)) as ServiceCounty"),
    //         DB::raw("(select state from zip_codes WHERE zipcode = (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_zipcode' and telesale_id = telesales.id LIMIT 1)) as ServiceStateAbbrivated"),
    //         DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'Email' and form_id = telesales.form_id LIMIT 1) and telesale_id =telesales.id LIMIT 1) as Email"),
    //         DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'Phone Number' and form_id = telesales.form_id LIMIT 1) and telesale_id =telesales.id LIMIT 1) as Phone"),
    //         DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'Billing Name' and form_id = telesales.form_id LIMIT 1) and meta_key = 'first_name' and telesale_id =telesales.id ) as 'BillingName'"),
    //         DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'Billing Name' and form_id = telesales.form_id LIMIT 1) and meta_key = 'last_name' and telesale_id =telesales.id ) as 'BillingLastName'"),
    //         DB::raw("UPPER((select meta_value from telesalesdata  where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'billing_address_1' and telesale_id =telesales.id LIMIT 1)) as BillingAddress1"),
    //         DB::raw("UPPER((select meta_value from telesalesdata  where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and  meta_key = 'billing_address_2' and telesale_id =telesales.id LIMIT 1)) as BillingAddress2"),
    //         DB::raw("UPPER((select meta_value from telesalesdata  where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'billing_city' and telesale_id =telesales.id LIMIT 1)) as BillingCity"),
    //         DB::raw("UPPER((select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'billing_state' and telesale_id =telesales.id LIMIT 1)) as BillingState"),
    //         DB::raw("(select state from zip_codes WHERE zipcode = (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'billing_zipcode' and telesale_id = telesales.id LIMIT 1)) as BillingStateAbbrivated"),
    //         DB::raw("UPPER((select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'billing_country' and telesale_id =telesales.id LIMIT 1)) as BillingCountry"),
    //         DB::raw("concat((select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'billing_zipcode' and telesale_id = telesales.id LIMIT 1), ' ') as BillingZipcode"),
	// 		DB::raw("(select county from zip_codes WHERE zipcode = (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'billing_zipcode' and telesale_id = telesales.id LIMIT 1)) as BillingCounty"),
    //         DB::raw("( select GROUP_CONCAT(code  SEPARATOR ', ') from programs where id IN (select program_id from telesales_programs where telesale_id = telesales.id) ) as Programs"),
    //         DB::raw("( select GROUP_CONCAT(id  SEPARATOR ', ') from programs where id IN (select program_id from telesales_programs where telesale_id = telesales.id) ) as pid"),
    //         DB::raw("CASE
    //         WHEN  telesales.verification_method = '1' THEN 'Customer Inbound'
    //         WHEN  telesales.verification_method = '2' THEN 'Agent Inbound'
    //         WHEN  telesales.verification_method = '3' THEN 'Self Verification(Email)'
    //         WHEN  telesales.verification_method = '4' THEN 'Self Verification(SMS)'
    //         WHEN  telesales.verification_method = '5' THEN 'IVR Inbound'
    //         WHEN  telesales.verification_method = '6' THEN 'TPV Now Outbound'
    //         ELSE '' 
    //         END as 'Method'"),
    //         DB::raw("CASE
    //         WHEN telesales.language = 'es' THEN 'Spanish'
    //         WHEN telesales.language = 'en' THEN 'English' 
    //         ELSE ''
    //         END  as 'Language'"),
    //         'telesales.call_id as TPV Call ID',
    //         DB::raw("concat( tpvagents.first_name, ' ',tpvagents.last_name ) as TPVAgent")
    //     );
    //     $enrollment = $enrollment->where('clients.id',$clientId);
    //     $enrollment = $enrollment->where('telesales.status','verified');
    //     $enrollment = $enrollment->whereBetween('telesales.updated_at',[$startDate,$endDate]);
    //     // echo '<pre>';print_r($enrollment->toSql());die;
    //     return $enrollment->get();
    // }

    
    /**
     * This method is used for send email
     * @param $megaEnergyEmails, $file, $salesCenterName
     * 
     */
    public function sendClientMail($megaEnergyEmails, $file, $salesCenterName) {
        foreach($megaEnergyEmails as $email){
            $toEmail = $email;
            $greeting = "Hello,";
            $message = "Please find the enrollment report for ".$salesCenterName.".";
            $subject = "Enrollment Report : ".$salesCenterName;
            Mail::send('emails.common', ['greeting' => $greeting, 'msg' => $message], function($mail) use ($toEmail, $subject, $file, $salesCenterName) {
                $mail->to($toEmail);
                $mail->subject($subject);
                $mail->attachData($file->string("xlsx"), 'Verified_Enrollment_report_'.$salesCenterName.'_'.date('d_M_Y_H_i_A') . '.xlsx');
            });

            if (!Mail::failures()) {
                $textEmailStatistics = new TextEmailStatistics();
                $textEmailStatistics->type = 1;
                $textEmailStatistics->save();
                \Log::info("Enrollment mail sent on email address: " . $toEmail);
            } else {
                \Log::error("Unable to send enrollment mail on email address: " . $toEmail);
            }
        }
        echo "email sent";
    }
}
