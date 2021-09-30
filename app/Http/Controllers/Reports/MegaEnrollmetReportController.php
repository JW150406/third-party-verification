<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Telesales;
use DB;
use Auth;
use Carbon\Carbon;
use App\User;
use DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Log;

class MegaEnrollmetReportController extends Controller
{
    /**
     * This method is used for showing data in datatable or view(blade) page
     * @param $request
     * 
     */
    public function index(Request $request)
    {
        Log::info("In index method of MegaEnrollmentReportController");

        $clientId = "";
        $timeZone = Auth::user()->timezone;
        if(Auth::user()->isAccessLevelToClient()) {
            $clientId =  auth()->user()->client_id;
        } else if (isset($request->client) && !empty($request->client)) {
            $clientId = $request->client;
        }
        $clientId = config('constants.CLIENT_MEGA_ENERGY_ID');

        $date = "";
        $startDate = Carbon::today()->startOfMonth(); 
        $endDate = Carbon::now();
        if (isset($request->submitDate) && !empty($request->submitDate)) {
            $date = $request->submitDate;
            $startDate = Carbon::parse(explode(' - ', $date)[0], $timeZone)->setTimezone('UTC');
            $endDate = Carbon::parse(explode(' - ', $date)[1], $timeZone)->setTimezone('UTC')->addDays(1);
        }

        // Get data for filter
        $filters = [];
        $filters['salesAgent'] = ($request->has('salesAgent')) ? $request->get('salesAgent') : "";
        $filters['salesCenter'] = ($request->has('salesCenter')) ? $request->get('salesCenter') : "";
        $filters['location'] = ($request->has('location')) ? $request->get('location') : "";
        $filters['leadId'] = ($request->has('leadId')) ? $request->get('leadId') : "";
        $filters['state'] = ($request->has('state')) ? $request->get('state') : "";
        $filters['startDate'] = $startDate;
        $filters['endDate'] = $endDate;

        if(Auth::user()->hasAccessLevels('salescenter')) {
            $filters['salesCenter'] = Auth::user()->salescenter_id;
        }
        if(Auth::user()->isLocationRestriction()) {
            $filters['location'] = Auth::user()->location_id;
        }
        if (auth()->user()->hasMultiLocations()) {
            $locationIds = auth()->user()->locations->pluck('id');
        }
        if ($request->leadDateType == null) {
            $leadType = "submission";
        } else {
            $leadType = $request->leadDateType;
        }
        $filters['leadType'] = $leadType;

        // Pass all data in db query
        $enrollmentData = $this->getEnrollmentData($clientId, $startDate, $endDate);
        
        // Apply filter in query or laravel data collection
        $enrollment = $this->dataFilter($enrollmentData, $filters);
        
        // If this method access by ajax then return data in datatable otherwise in view (blade) file
        if($request->ajax()) {
            
            return DataTables::of($enrollment)
            ->editColumn('Date',function($enrollment) use($timeZone){  
                $soldDate = Carbon::parse($enrollment->SoldDateTime)->setTimezone(getClientSpecificTimeZone())->format("m/d/Y");
                return $soldDate;
            })
            ->editColumn('Time',function($enrollment) use($timeZone){  
                $soldTime = Carbon::parse($enrollment->SoldDateTime)->setTimezone(getClientSpecificTimeZone())->format("H:i:s");
                return $soldTime;
            })
            // ->editColumn('First Name',function($enrollment) {  
            //     $Authorisedname = array_filter(explode(" ",$enrollment->AuthorizedName));
            //     return $Authorisedname;
            // })
            // ->editColumn('Last Name',function($enrollment) {  
            //     $ServiceLastName = array_filter(explode(" ",$enrollment->ServiceLastName));
            //     return $ServiceLastName;
            // })
            // ->editColumn('Acct Number',function($enrollment) {  
            //     return $enrollment->AccountNumber;
            // })
            ->editColumn('Service Address',function($enrollment) {  
                $ServiceAddress = implode(", ",array_filter([$enrollment->ServiceAddress1,$enrollment->ServiceAddress2]));
                return $ServiceAddress;
            })
            // ->editColumn('Agent Code',function($enrollment) {  
            //     return $enrollment->SalesAgentID;
            // })
            // ->editColumn('Agent Name',function($enrollment) {  
            //     return $enrollment->SalesAgent;
            // })
            ->editColumn('Commodity',function($enrollment) {  
                return $enrollment->Commodity;
            })
            // ->editColumn('Term',function($enrollment) {  
            //     return $enrollment->Term;
            // })
            // ->editColumn('Rate',function($enrollment) {  
            //     return $enrollment->Rate;
            // })
            // ->editColumn('Status',function($enrollment) {  
            //     return $enrollment->Status;
            // })
            // ->editColumn('Vendor',function($enrollment) {  
            //     return $enrollment->SalesCenter;
            // })
            // ->editColumn('Utility',function($enrollment) {  
            //     return $enrollment->Utility;
            // })
            // ->editColumn('Customer Phone Number',function($enrollment) {  
            //     return $enrollment->Phone;
            // })
            ->editColumn('Customer Class',function($enrollment) {  
                return "R";
            })
            // ->editColumn('Product Type',function($enrollment) {  
            //     return $enrollment->Programs;
            // })
            ->editColumn('Confirmation Number',function($enrollment) {  
                return $enrollment->LeadID;
            })
            ->editColumn('Complete',function($enrollment) {  
                return "Y";
            })
            ->editColumn('Verified',function($enrollment) {  
                return "Y";
            })
            ->editColumn('Canceled',function($enrollment) {  
                return "";
            })
            ->editColumn('Comments',function($enrollment) {  
                return "";
            })
            // ->editColumn('Customer Email',function($enrollment) {  
            //     return $enrollment->Email;
            // })
            ->editColumn('Service Address 1',function($enrollment) {  
                $ServiceAddress1 = implode(", ",array_filter([$enrollment->ServiceAddress1,$enrollment->ServiceAddress2]));
                return $ServiceAddress1;
            })
            // ->editColumn('Service City',function($enrollment) {  
            //     return $enrollment->ServiceCity;
            // })
            // ->editColumn('Service State',function($enrollment) {  
            //     return $enrollment->ServiceState;
            // })
            ->editColumn('Service Zip',function($enrollment) {  
                return $enrollment->ServiceZipcode;
            })
            // ->editColumn('Service County',function($enrollment) {  
            //     return $enrollment->ServiceCounty;
            // })
            ->editColumn('Billing Address',function($enrollment) {  
                $BillingAddress = implode(", ",array_filter([$enrollment->BillingAddress1,$enrollment->BillingAddress2]));
                return $BillingAddress;
            })
            // ->editColumn('Billing City',function($enrollment) {  
            //     return $enrollment->BillingCity;
            // })
            // ->editColumn('Billing State',function($enrollment) {  
            //     return $enrollment->BillingState;
            // })
            ->editColumn('Billing Zip',function($enrollment) {  
                return $enrollment->BillingZipcode;
            })
            // ->editColumn('Billing County',function($enrollment) {  
            //     return $enrollment->BillingCounty;
            // })
            // ->editColumn('Name Key',function($enrollment) {  
            //     return $enrollment->NameKey;
            // })
            // ->editColumn('Billing Cycle',function($enrollment) {  
            //     return $enrollment->BillingCycleNumber;
            // })
            ->editColumn('Date Of Birth',function($enrollment) {  
                return "";
            })
            ->make(true);
        }
        
        $salesAgents = User::where('access_level', '=', 'salesagent')
                        ->where('client_id', '=', $clientId);
                        if(Auth::user()->hasAccessLevels('salescenter')) {
                            $salesAgents = $salesAgents->where('salescenter_id',Auth::user()->salescenter_id);
                        }
                        $salesAgents = $salesAgents->orderBy('first_name')->get();
        $results = '';
        return view('reports.mega_enrollment_report.index',compact('results', 'salesAgents'));
    }

    /**
     * This method is used for export data in csv or xlsx file
     * @param $request
     * 
     */
    public function exportReport(Request $request)
    {
        Log::info("In exportReport method of MegaEnrollmentReportController");

        $exportType = $request->get('export');
        $client_id = "";
        $timeZone = Auth::user()->timezone;
        if(Auth::user()->isAccessLevelToClient()) {
            $client_id =  auth()->user()->client_id;
        } else if (isset($request->client) && !empty($request->client)) {
            $client_id = $request->client;
        }
        $clientId = config('constants.CLIENT_MEGA_ENERGY_ID');
        
        $date = "";
        $startDate = Carbon::today()->startOfMonth(); 
        $endDate = Carbon::now();
        if (isset($request->date_start) && !empty($request->date_start)) {
            $date = $request->date_start;
            $startDate = Carbon::parse(explode(' - ', $date)[0], $timeZone)->setTimezone('UTC');
            $endDate = Carbon::parse(explode(' - ', $date)[1], $timeZone)->setTimezone('UTC')->addDays(1);
        }

        $filters = [];
        $filters['salesAgent'] = ($request->has('sales_agent')) ? $request->get('sales_agent') : "";
        $filters['salesCenter'] = ($request->has('sales_center')) ? $request->get('sales_center') : "";
        $filters['location'] = ($request->has('location')) ? $request->get('location') : "";
        $filters['leadId'] = ($request->has('leadId')) ? $request->get('leadId') : "";
        $filters['state'] = ($request->has('state')) ? $request->get('state') : "";
        $filters['startDate'] = $startDate;
        $filters['endDate'] = $endDate;
        
        if(Auth::user()->hasAccessLevels('salescenter')) {
            $filters['salesCenter'] = Auth::user()->salescenter_id;
        }
        if(Auth::user()->isLocationRestriction()) {
            $filters['location'] = Auth::user()->location_id;
        }
        if (auth()->user()->hasMultiLocations()) {
            $locationIds = auth()->user()->locations->pluck('id');
        }
        if ($request->leadDateType == null) {
            $leadType = "submission";
        } else {
            $leadType = $request->leadDateType;
        }
        $filters['leadType'] = $leadType;
    
        
        $results = null;
        // Pass data in db query and apply filter data
        $enrollmentData = $this->getEnrollmentData($clientId, $startDate, $endDate);
        $results = $this->dataFilter($enrollmentData, $filters);
        $results = $results->get()->toArray();

        // Create filename with date
        $filename = "ENROLLMENT-REPORT-".date('y-m-d');
        
        foreach($results as $key => $enrollMent){
            $soldDate = Carbon::parse($enrollMent['SoldDateTime'])->setTimezone(getClientSpecificTimeZone())->format("m/d/Y");
            $soldTime = Carbon::parse($enrollMent['SoldDateTime'])->setTimezone(getClientSpecificTimeZone())->format("H:i:s");
            $checkServiceCounty = $enrollMent['ServiceCounty'] ? str_contains($enrollMent['ServiceCounty'], 'County') : '';
            $checkBillingCounty = $enrollMent['BillingCounty'] ? str_contains($enrollMent['BillingCounty'], 'County') : '';
            
            $data['Date'] = $soldDate;
            $data['Time'] = $soldTime;
            $data['First Name'] = $enrollMent['AuthorizedName'];
            $data['Last Name'] = $enrollMent['ServiceLastName'];
            $data['Acct Number'] = $enrollMent['AccountNumber'];
            $data['Service Address'] = implode(", ",array_filter([$enrollMent['ServiceAddress1'],$enrollMent['ServiceAddress2']]));
            $data['Agent Code'] = $enrollMent['SalesAgentID'];
            $data['Agent Name'] = $enrollMent['SalesAgent'];
            $data['Commodity'] = $enrollMent['Commodity'];
            $data['Term'] = $enrollMent['Term'];
            $data['Rate'] = $enrollMent['Rate'];
            $data['Status'] = $enrollMent['Status'];
            $data['Vendor'] = $enrollMent['SalesCenter'];
            $data['Utility'] = $enrollMent['Utility'];
            $data['Customer Phone Number'] = $enrollMent['Phone'];
            $data['Customer Class'] = "R";
            $data['Product Type'] = $enrollMent['Programs'];
            $data['Confirmation Number'] = $enrollMent['LeadID'];
            $data['Complete'] = "Y";
            $data['Verified'] = "Y";
            $data['Canceled'] = "";
            $data['Comments'] = "";
            $data['Customer Email'] = $enrollMent['Email'];
            $data['Service Address '] = $enrollMent['ServiceAddress1']; // take space in key after "Address" because we alredy have one key as same name, So for differetiate space will be added
            $data['Service Address Line 2'] = $enrollMent['ServiceAddress2'];
            $data['Service City'] = $enrollMent['ServiceCity'];
            $data['Service State'] = $enrollMent['ServiceStateAbbrivated'];
            $data['Service Zip'] = $enrollMent['ServiceZipcode'];
            $data['Service County'] = $checkServiceCounty ? $enrollMent['ServiceCounty'] : $enrollMent['ServiceCounty']." County";
            $data['Billing Address'] = $enrollMent['BillingAddress1'];
            $data['Billing Address Line 2'] = $enrollMent['BillingAddress2'];
            $data['Billing City'] = $enrollMent['BillingCity'];
            $data['Billing State'] = $enrollMent['BillingStateAbbrivated'];
            $data['Billing Zip'] = $enrollMent['BillingZipcode'];
            $data['Billing County'] = $checkBillingCounty ? $enrollMent['BillingCounty'] : $enrollMent['BillingCounty']." County";
            $data['Name Key'] = $enrollMent['NameKey'];
            $data['Billing Cycle'] = $enrollMent['BillingCycleNumber'];
            $data['Date Of Birth'] = "";
            $data['Meter Number'] = $enrollMent['MeterNumber']; 
        
            // Push particular key data in final sheetData array
            $sheetData[$key] = $data;
        }
        
        if (empty($sheetData)) {
            $sheetData[0] = 'Data not available';
        }

        // For generate excel sheet
        $excelFile = Excel::create($filename, function($excel) use ($sheetData) {
            $excel->sheet('sheet1', function($sheet) use ($sheetData)
            {
                $sheet->fromArray($sheetData);
            });
        })->download($exportType);
        
    }

    /**
     * Query for get leads data of mega enrollments from database as per requirements 
     * This method is called from index and exportReport method of this controller
     * This query is also called from command - sendEnrollmentReportMega (php artisan sendEnrollmentReportMega:verified)
     * 
     * @param $clientId, $startDate, $endDate
     * 
     */
    public function getEnrollmentData($clientId, $startDate, $endDate)
    {
        Log::info("Start to write query for get data for Mega Energy from various table");

        $enrollment = Telesales::leftJoin('users','users.id','=','telesales.user_id')
        ->leftJoin('clients','clients.id','=','telesales.client_id')
        ->leftJoin('telesales_programs','telesales_programs.telesale_id','=','telesales.id')
        ->leftJoin('programs','programs.id','=','telesales_programs.program_id')
        ->leftJoin('utilities','programs.utility_id','=','utilities.id')
        ->leftjoin('commodities', 'commodities.id', '=', 'utilities.commodity_id') 
        ->leftJoin('brand_contacts','utilities.brand_id','=','brand_contacts.id')
        ->leftJoin('salescenters','users.salescenter_id','=','salescenters.id')
        ->leftJoin('salesagent_detail','users.id','=','salesagent_detail.user_id')
        ->leftJoin('salescenterslocations','salescenterslocations.id','=','users.location_id')
        ->leftjoin('telesales_zipcodes','telesales_zipcodes.telesale_id','=','telesales.id')
        ->leftjoin('zip_codes','zip_codes.id','=','telesales_zipcodes.zipcode_id')
        ->leftjoin('dispositions','dispositions.id','=','telesales.disposition_id')
        ->leftJoin('users as tpvagents','tpvagents.id','=','telesales.reviewed_by')
        ->select(
            'clients.name as Client',
            'commodities.id as commodity_id', 'commodities.name as commodity_name',
            'salescenters.name as SalesCenter','salescenterslocations.name as SalesCenterLocation','programs.term as Term','programs.rate as Rate',
            'telesales.created_at as SoldDateTime','telesales.reviewed_at as TPVDate','telesales.refrence_id as LeadID','zip_codes.state as State',
            'users.userid as SalesAgentID',
            'utilities.market as Utility',
            'programs.code as Programs', 'programs.id as pid',

            // Count of commodities & service address
            DB::raw("(select count(commodity_id) from form_commodities where form_id = telesales.form_id) as commodities_count"),
            DB::raw("(select count(id) from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null) as service_address_count"),

            // ServiceAddress1
            DB::raw("(select meta_value from telesalesdata where field_id = (
                CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                    ELSE 
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                END )  
                and  meta_key = 'service_address_1' and telesale_id =telesales.id LIMIT 1) as ServiceAddress1"),

            // ServiceAddress2
            DB::raw("(select meta_value from telesalesdata where field_id = (
                CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                    ELSE 
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                END )  
                and  meta_key = 'service_address_2' and telesale_id =telesales.id LIMIT 1) as ServiceAddress2"),

            // ServiceCity
            DB::raw("(select meta_value from telesalesdata where field_id = (
                CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                    ELSE 
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                END )  
                and  meta_key = 'service_city' and telesale_id =telesales.id LIMIT 1) as ServiceCity"),
            
            // ServiceState
            DB::raw("(select meta_value from telesalesdata where field_id = (
                CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                    ELSE 
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                END )  
                and  meta_key = 'service_state' and telesale_id =telesales.id LIMIT 1) as ServiceState"),    
            
            // ServiceCountry
            DB::raw("(select meta_value from telesalesdata where field_id = (
                CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                    ELSE 
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                END )  
                and  meta_key = 'service_country' and telesale_id =telesales.id LIMIT 1) as ServiceCountry"),   
                
            // ServiceZipcode
            DB::raw("(select meta_value from telesalesdata where field_id = (
                CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                    ELSE 
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                END )  
                and  meta_key = 'service_zipcode' and telesale_id =telesales.id LIMIT 1) as ServiceZipcode"),   

            // ServiceCounty
            DB::raw("(select meta_value from telesalesdata where field_id = (
                CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                    ELSE 
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                END )  
                and  meta_key = 'service_county' and telesale_id =telesales.id LIMIT 1) as ServiceCounty"),     
                
            // ServiceStateAbbrivated
            DB::raw("(select state from zip_codes WHERE zipcode = (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_zipcode' and telesale_id = telesales.id LIMIT 1)) as ServiceStateAbbrivated"),
            
            // Phone   
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'phone_number' and form_id = telesales.form_id and deleted_at is null and is_primary = true LIMIT 1) and  meta_key = 'value' and telesale_id = telesales.id LIMIT 1) as Phone"),

            // Email
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'email' and form_id = telesales.form_id and deleted_at is null and is_primary = true LIMIT 1) and  meta_key = 'value' and telesale_id = telesales.id LIMIT 1) as Email"),   

            // Billing Name
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'Billing Name' and form_id = telesales.form_id LIMIT 1) and meta_key = 'first_name' and telesale_id =telesales.id ) as 'BillingName'"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'Billing Name' and form_id = telesales.form_id LIMIT 1) and meta_key = 'last_name' and telesale_id =telesales.id ) as 'BillingLastName'"),
            
            // BillingAddress1
            DB::raw("(select meta_value from telesalesdata where field_id = (
                CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                    ELSE 
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                END )  
                and  meta_key = 'billing_address_1' and telesale_id =telesales.id LIMIT 1) as BillingAddress1"),

            // BillingAddress2
            DB::raw("(select meta_value from telesalesdata where field_id = (
                CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                    ELSE 
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                END )  
                and  meta_key = 'billing_address_2' and telesale_id =telesales.id LIMIT 1) as BillingAddress2"),

            // BillingCity
            DB::raw("(select meta_value from telesalesdata where field_id = (
                CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                    ELSE 
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                END )  
                and  meta_key = 'billing_city' and telesale_id =telesales.id LIMIT 1) as BillingCity"),

            // BillingState
            DB::raw("(select meta_value from telesalesdata where field_id = (
                CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                    ELSE 
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                END )  
                and  meta_key = 'billing_state' and telesale_id =telesales.id LIMIT 1) as BillingState"),

            // BillingCountry
            DB::raw("(select meta_value from telesalesdata where field_id = (
                CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                    ELSE 
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                END )  
                and  meta_key = 'billing_country' and telesale_id =telesales.id LIMIT 1) as BillingCountry"),

            // BillingZipcode
            DB::raw("(select meta_value from telesalesdata where field_id = (
                CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                    ELSE 
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                END )  
                and  meta_key = 'billing_zipcode' and telesale_id =telesales.id LIMIT 1) as BillingZipcode"),
            
            // BillingCounty
            DB::raw("(select meta_value from telesalesdata where field_id = (
                CASE WHEN commodities_count > 1 AND service_address_count > 1 THEN
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Service and Billing Address%', commodities.name ,'%') LIMIT 1)
                    ELSE 
                    (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id and deleted_at is null and is_primary = 1 LIMIT 1)
                END )  
                and  meta_key = 'billing_county' and telesale_id =telesales.id LIMIT 1) as BillingCounty"),

            // BillingStateAbbrivated
            DB::raw("(select state from zip_codes WHERE zipcode = (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'billing_zipcode' and telesale_id = telesales.id LIMIT 1)) as BillingStateAbbrivated"),
            
            DB::raw("concat( users.first_name, ' ',users.last_name ) as SalesAgent"),
            DB::raw("CASE
            WHEN  salesagent_detail.agent_type = 'd2d' THEN 'Door-to-Door'
            WHEN  salesagent_detail.agent_type = 'tele' THEN 'Telemarketing'
            ELSE '' 
            END as 'Channel'"),
            DB::raw("(CASE WHEN telesales.status = 'verified' THEN 'Verified' WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' WHEN telesales.status = 'expired' THEN 'Expired' ELSE 'Pending' END) as Status"),
            DB::raw("CASE
            WHEN telesales.status = 'cancel' THEN telesales.cancel_reason
            WHEN telesales.status = 'decline' THEN dispositions.description 
            WHEN telesales.status = 'hangup' THEN dispositions.description 
            ELSE ''
            END  as 'Reason'"),
            DB::raw("CASE
                WHEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'first_name' and telesale_id =telesales.id LIMIT 1)  != ''
                THEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'first_name' and telesale_id =telesales.id  LIMIT 1 )
                ELSE ''
                END  as 'AuthorizedName'"),
            DB::raw("CASE
                WHEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'last_name' and telesale_id =telesales.id  LIMIT 1)  != ''
                THEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'last_name' and telesale_id =telesales.id  LIMIT 1)
                ELSE ''
                END  as 'ServiceLastName'"),
            // AccountNumber
            DB::raw("(select meta_value from telesalesdata where field_id = (
                CASE WHEN commodities_count > 1 THEN
                    (select id from form_fields where type = 'textbox' and form_id = telesales.form_id and deleted_at is null and label LIKE CONCAT('Account number%', commodities.name ,'%') LIMIT 1)
                    ELSE 
                    (select id from form_fields where type = 'textbox' and form_id = telesales.form_id and deleted_at is null and label LIKE 'Account Number' LIMIT 1)
                END )  
                and  meta_key = 'value' and telesale_id =telesales.id LIMIT 1) as AccountNumber"),

            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'NAME KEY' and form_id = telesales.form_id LIMIT 1) and telesale_id =telesales.id limit 1 ) as NameKey"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'BILLING CYCLE NUMBER' and form_id = telesales.form_id LIMIT 1) and telesale_id =telesales.id limit 1 ) as BillingCycleNumber"),
            
            
            // DB::raw("(select GROUP_CONCAT(commodities.name) from commodities left join form_commodities on form_commodities.commodity_id = commodities.id where form_commodities.form_id = telesales.form_id) as Commodity"),
            // Commodity
            \DB::raw("UPPER(LEFT(commodities.name , 1)) as Commodity"),
            
            // DB::raw("( select GROUP_CONCAT(code  SEPARATOR ', ') from programs where id IN (select program_id from telesales_programs where telesale_id = telesales.id) ) as Programs"),
            // DB::raw("( select GROUP_CONCAT(id  SEPARATOR ', ') from programs where id IN (select program_id from telesales_programs where telesale_id = telesales.id) ) as pid"),
            DB::raw("CASE
            WHEN  telesales.verification_method = '1' THEN 'Customer Inbound'
            WHEN  telesales.verification_method = '2' THEN 'Agent Inbound'
            WHEN  telesales.verification_method = '3' THEN 'Self Verification(Email)'
            WHEN  telesales.verification_method = '4' THEN 'Self Verification(SMS)'
            WHEN  telesales.verification_method = '5' THEN 'IVR Inbound'
            WHEN  telesales.verification_method = '6' THEN 'TPV Now Outbound'
            ELSE '' 
            END as 'Method'"),
            DB::raw("CASE
            WHEN telesales.language = 'es' THEN 'Spanish'
            WHEN telesales.language = 'en' THEN 'English' 
            ELSE ''
            END  as 'Language'"),
            'telesales.call_id as TPV Call ID',
            DB::raw("concat( tpvagents.first_name, ' ',tpvagents.last_name ) as TPVAgent"),
            DB::raw("CASE
            WHEN UPPER(LEFT(commodities.name , 1)) = 'G' THEN (select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'Meter Number' and form_id = telesales.form_id LIMIT 1) and meta_key = 'value' and telesale_id =telesales.id )
            ELSE ''
            END
            as MeterNumber")
            // DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'Meter Number' and form_id = telesales.form_id LIMIT 1) and meta_key = 'value' and telesale_id =telesales.id ) as MeterNumber")
        );
        
        $enrollment = $enrollment->where('telesales.client_id',$clientId);
        $enrollment = $enrollment->where('telesales.status','verified');
        $enrollment = $enrollment->whereBetween('telesales.reviewed_at',[$startDate,$endDate]);
        // echo '<pre>';print_r($enrollment->toSql());die;
        // return $enrollment->get();
        return $enrollment;
    }

    /**
     * Used of this method is to apply all the filters in data collection
     * 
     * @param $enrollment, $filters
     * 
     */
    public function dataFilter($enrollment, $filters)
    {
        Log::info("In dataFilter method of MegaEnrollmentController");

        if($filters['leadType'] == 'submission'){
            $enrollment = $enrollment->whereBetween('telesales.created_at', [$filters['startDate'], $filters['endDate']]);
        }
        if ($filters['leadType'] == 'verification') {
            $enrollment = $enrollment->whereBetween('telesales.reviewed_at', [$filters['startDate'], $filters['endDate']])
            ->whereNotIn('telesales.status', ['cancel', 'pending'])->whereIn('verification_method', ['1', '2', '6']);
        }
        if($filters['salesAgent'] != ''){
            $enrollment = $enrollment->where('users.id', $filters['salesAgent']);
        }
        if($filters['salesCenter'] != ''){
            $enrollment = $enrollment->where('salescenters.id', $filters['salesCenter']);
        }
        if($filters['location'] != ''){
            $enrollment = $enrollment->where('salescenterslocations.id', $filters['location']);
        }
        if($filters['leadId'] != ''){
            $enrollment = $enrollment->where('telesales.refrence_id', $filters['leadId']);
        }
        if($filters['state'] != ''){
            $enrollment = $enrollment->where('zip_codes.state', $filters['state']);
        }

        // return data object or collection after applying filters
        return $enrollment;
    }
}
