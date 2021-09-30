<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\models\Telesales;
use Carbon\Carbon;
use Log;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use App\models\TextEmailStatistics;

class LEenergyAutomatedReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendreport:leautomated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will create report of verified and decline leads and send email on daily basis.';

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
        // Startdate and enddate as per clint specific timezone (currently timezone is America/Toronto)
        $startDate = Carbon::today(getClientSpecificTimeZone())->subDays(1)->setTimezone('UTC');
        $endDate = Carbon::today(getClientSpecificTimeZone())->setTimezone('UTC');

        // Query to retrieve all the leads data created today
        $leads = Telesales::leftjoin('telesales_programs', 'telesales.id', 'telesales_programs.telesale_id')
                            ->leftjoin('programs', 'telesales_programs.program_id', '=', 'programs.id')
                            ->leftjoin('utilities', 'programs.utility_id', '=', 'utilities.id')
                            ->leftjoin('commodities', 'commodities.id', '=', 'utilities.commodity_id')
                            ->leftjoin('users', 'telesales.user_id', '=', 'users.id')
                            ->leftjoin('salesagent_detail', 'telesales.user_id', '=', 'salesagent_detail.user_id')
                            ->leftjoin('dispositions', 'telesales.disposition_id', '=', 'dispositions.id')
                            ->select('telesales.id as id', 
                                'programs.rate as price', 
                                'utilities.market as utility', 
                                'commodities.id as commodity_id', 'commodities.name as commodity_name',
                                'telesales.refrence_id as lead_id', 'telesales.created_at as created_date', 'telesales.status as lead_status',
                                'salesagent_detail.external_id as external_id',
                                'dispositions.description as rejection_reason',

                                // commodity count for check commodity has one or more
                                \DB::raw("(select count(commodity_id) from form_commodities where form_id = telesales.form_id) as commodities_count"),
                                \DB::raw("(select count(id) from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null) as service_address_count"),
                                
                                // customer name
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and deleted_at is null and is_primary = true LIMIT 1) and  meta_key = 'first_name' and telesale_id =telesales.id LIMIT 1) as cust_first_name"),
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and deleted_at is null and is_primary = true LIMIT 1) and  meta_key = 'middle_initial' and telesale_id =telesales.id LIMIT 1) as cust_middle_initial"),
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and form_id = telesales.form_id and deleted_at is null and is_primary = true LIMIT 1) and  meta_key = 'last_name' and telesale_id =telesales.id LIMIT 1) as cust_last_name"),
                                
                                // service address line 1
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_address_1' and telesale_id =telesales.id LIMIT 1) as service_addr_line_1"),

                                // service address line 2
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_address_2' and telesale_id =telesales.id LIMIT 1) as service_addr_line_2"),
                                
                                // service address city
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_city' and telesale_id =telesales.id LIMIT 1) as service_addr_city"),
                                
                                // service address state
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_state' and telesale_id =telesales.id LIMIT 1) as state"),

                                // service address zipcode
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_zipcode' and telesale_id =telesales.id LIMIT 1) as service_addr_zipcode"),
                                
                                // service address county
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                                    END )  
                                    and  meta_key = 'service_county' and telesale_id =telesales.id LIMIT 1) as county"),

                                // Account Number
                                \DB::raw("(select meta_value from telesalesdata where field_id = (
                                    CASE WHEN commodities_count > 1 THEN
                                        (select id from form_fields where type = 'textbox' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Account number%', commodities.name ,'%') LIMIT 1)
                                        ELSE 
                                        (select id from form_fields where type = 'textbox' and form_id = telesales.form_id and deleted_at is null and label LIKE 'Account Number%' LIMIT 1)
                                    END )  
                                    and  meta_key = 'value' and telesale_id =telesales.id LIMIT 1) as account_number"),
                                
                                // phone number
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'phone_number' and form_id = telesales.form_id and deleted_at is null and is_primary = true LIMIT 1) and  meta_key = 'value' and telesale_id = telesales.id LIMIT 1) as phone_no"),
                                
                                // email address
                                \DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'email' and form_id = telesales.form_id and deleted_at is null and is_primary = true LIMIT 1) and  meta_key = 'value' and telesale_id = telesales.id LIMIT 1) as email_address")
                                )
                            ->where(function ($q) {
                                $q->where('telesales.status', '=', config('constants.LEAD_TYPE_VERIFIED'))
                                      ->orWhere('telesales.status', '=', config('constants.LEAD_TYPE_DECLINE'));
                            })      
                            ->where('telesales.client_id', '=', config('constants.CLIENT_LE_CLIENT_ID'))       
                            ->whereBetween('telesales.created_at',[$startDate,$endDate])
                            ->get();
            
            Log::info("Successfully fetched telesales data from database, Now set columns as per our requirements.");
            foreach ($leads as $key => $lead) {
                $data['Date'] = Carbon::parse($lead['created_date'])->setTimezone(getClientSpecificTimeZone())->format('Ymd'.' '.'H:m:s');
                $data['Rep'] = $lead['external_id'];
                $data['Cust Name'] = $lead['cust_first_name']. ' '.(isset($lead['cust_middle_initial']) ? $lead['cust_middle_initial'].' ' : '') . $lead['cust_last_name'];
                $data['Account Number'] = $lead['account_number'];
                $data['Street1'] = $lead['service_addr_line_1'];
                $data['Street2'] = $lead['service_addr_line_2'];
                $data['City'] = $lead['service_addr_city'];
                $data['State'] = $lead['state'];
                $data['Zip'] = $lead['service_addr_zipcode'];
                $data['County'] = $lead['county'];
                $data['Phone'] = $lead['phone_no'];
                $data['AgreementDate'] = '';
                $data['Utility'] = $lead['utility'];
                $data['Price'] = $lead['price'];
                $data['Transaction'] = $lead['lead_id'];
                $data['Verified'] = ($lead['lead_status'] == config('constants.LEAD_TYPE_VERIFIED')) ? 'Y' : 'N';
                $data['Rejection Reason'] = ($lead['lead_status'] == config('constants.LEAD_TYPE_DECLINE')) ? (isset($lead['rejection_reason']) ? $lead['rejection_reason'] : '') : '';
                $data['Reviewer Comments'] = '';
                $data['Commodity'] = $lead['commodity_name'];
                $data['Email'] = $lead['email_address'];
                
                // Push particular key data in final sheetData array
                $sheetData[$key] = $data;
            }
            
            // Check leads are available or not
            if (empty($sheetData)) {
                $yesterday = Carbon::yesterday()->toDateString();
                Log::info("Leads are not available for date : ". $yesterday);
            } else {
                // For generate excel sheet
                $excelFile = Excel::create('export_sample', function($excel) use ($sheetData) {
                    $excel->sheet('sheet1', function($sheet) use ($sheetData)
                    {
                        $sheet->fromArray($sheetData);
                    });
                });
                
                // get emails from constants
                $leEnergyEmails = explode(",", config()->get('constants.CLIENT_LE_ENERGY_ENROLLMENT_EXPORT_EMAILS'));

                // For send mail with excel file
                $this->sendMail($leEnergyEmails, $excelFile);
            }
    }

    /**
     * This method is used for send email
     * @param $leEnergyEmails, $file
     * 
     */
    public function sendMail($leEnergyEmails, $file) {
        foreach($leEnergyEmails as $email){
            // $date = Carbon::now()->toDateString();
            $yesterday = Carbon::yesterday()->toDateString();
            $yesterdayFormated = Carbon::yesterday()->format('d_M_Y_H_i_A');
            $fileName = "LE_Energy_Automated_Report_".$yesterdayFormated;
            $toEmail = $email;
            $greeting = "Hello,";
            $message = "Please find attached the enrollment file for " .$yesterday;
            $subject = "Enrollment File for ".$yesterday." ";
            Mail::send('emails.common', ['greeting' => $greeting, 'msg' => $message], function($mail) use ($toEmail, $subject, $file, $fileName) {
                $mail->to($toEmail);
                $mail->subject($subject);
                $mail->attachData($file->string("xlsx"), $fileName.'.xlsx');
            });

            if (!Mail::failures()) {
                $textEmailStatistics = new TextEmailStatistics();
                $textEmailStatistics->type = 1;
                $textEmailStatistics->save();
                Log::info("Enrollment mail sent on email address: " . $toEmail);
            } else {
                Log::error("Unable to send enrollment mail on email address: " . $toEmail);
            }
        }
        echo "email sent";
    }
}
