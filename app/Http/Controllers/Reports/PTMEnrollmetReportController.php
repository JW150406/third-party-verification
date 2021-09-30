<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
// use PhpOffice\PhpSpreadsheet\Cell\DataType;
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
use App\models\TelesalesSolarUpdate;

class PTMEnrollmetReportController extends Controller
{
    /**
     * This method is used for showing data in datatable or view(blade) page
     * @param $request
     */
    public function index(Request $request)
    {
        Log::info("In index method of PTMEnrollmetReportController");

        $clientId = "";
        $timeZone = Auth::user()->timezone;
        

        $status = "";
        if (isset($request->status) && $request->status != "") {
            $status = $request->status;
        }
        $clientId = ($request->has('client')) ? $request->get('client') : "";
        $brand = ($request->has('brand')) ? $request->get('brand') : "";
        $salesCenter = ($request->has('salesCenter')) ? $request->get('salesCenter') : "";
        $location = ($request->has('location')) ? $request->get('location') : "";
        $channel = ($request->has('channel')) ? $request->get('channel') : "";
        $status = ($request->has('status')) ? $request->get('status') : "";
        $leadId = ($request->has('leadId')) ? $request->get('leadId') : "";
        $state = ($request->has('state')) ? $request->get('state') : "";
        if(Auth::user()->hasAccessLevels('salescenter')) {
            $salesCenter = Auth::user()->salescenter_id;
        }
        if(Auth::user()->isAccessLevelToClient()) {
            $clientId =  auth()->user()->client_id;
        } else if (isset($request->client) && !empty($request->client)) {
            $clientId = $request->client;
        }

        if(Auth::user()->isLocationRestriction()) {
            $location = Auth::user()->location_id;
        }
        if (auth()->user()->hasMultiLocations()) {
            $locationIds = auth()->user()->locations->pluck('id');
        }
        if ($request->leadDateType == null) {
            $leadType = "submission";
        } else {
            $leadType = $request->leadDateType;
        }

        $date = "";
        $startDate = Carbon::today()->startOfMonth(); 
        $endDate = Carbon::now();
        
        if (isset($request->submitDate) && !empty($request->submitDate)) {
            $date = $request->submitDate;
            $startDate = Carbon::parse(explode(' - ', $date)[0], $timeZone)->setTimezone('UTC');
            $endDate = Carbon::parse(explode(' - ', $date)[1], $timeZone)->setTimezone('UTC')->addDays(1);
        }
        
      
        //Added subquery for full name searching 

        $subquery = "";
        $customerFirstNameQuery  = "(select (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'first_name' and telesale_id =telesales.id  LIMIT 1 ) as 'CustomerFirstName')";
        $customerMiddleNameQuery  = "(select (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'middle_initial' and telesale_id =telesales.id  LIMIT 1 ) as 'CustomerMiddleName')";
        $customerLastNameQuery  = "(select (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'last_name' and telesale_id =telesales.id  LIMIT 1 ) as 'CustomerLastName')";
        $accountSubQuery = "(select (select GROUP_CONCAT(meta_value SEPARATOR ', ') from telesalesdata left join form_fields on form_fields.id = telesalesdata.field_id  where telesalesdata.field_id and  LOWER(form_fields.label) LIKE 'account number%' and form_fields.form_id = telesales.form_id and telesalesdata.meta_key = 'value' and telesalesdata.telesale_id = telesales.id LIMIT 1) as AccountNumber)";

        $phoneSubQuery = "(select (select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'Phone Number' and form_id = telesales.form_id LIMIT 1) and telesale_id =telesales.id LIMIT 1 ) as 'Phone' )";

        // Pass all data in query as per filter
        $enrollment = $this->getEnrollmentData($clientId, $salesCenter, $location, $startDate,$endDate, $channel, $status, $leadId, $state, $leadType, $brand,$subquery);
        

        // If this method access by ajax then return data in datatable otherwise in view (blade) file
        if($request->ajax()) {
            return DataTables::of($enrollment)
            ->editColumn('SoldDateTime',function($enrollment) use($timeZone){  
                return Carbon::parse($enrollment->SoldDateTime)->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat());
            })
            ->editColumn('Programs',function($enrollment){
                
                $programs = explode(', ',$enrollment->Programs);
                $programIds = explode(', ',$enrollment->pid);
                
                $program  = [];
                for($i=0;$i< count($programs);$i++)
                {
                    $program[$i] = '<a href="javacript:void(0)" class="program-code" data-toggle="modal" data-target="#programCodeModal" p-id ='.$programIds[$i].'>'.$programs[$i].'</a>';
                }
                return implode(', ',$program);
            })
            ->filterColumn('AccountNumber',function($enrollment,$keyword) use($accountSubQuery){  
                return  $enrollment->whereRaw($accountSubQuery .' LIKE "%'.$keyword.'%"');
            })
            ->editColumn('Esignature',function($enrollment){ 
                if(!empty($enrollment->Esignature)){ 
                    $url = Storage::disk('s3')->url($enrollment->Esignature);
                    return '<img src="'.$url.'" style="object-fit: cover;height:40px;"/>';
                }
            })
            ->editColumn('IPAddress',function($enrollment){ 
                if(!empty($enrollment->Esignature)){ 
                    // print_r($enrollment->toArray()); die('test');
                    return ($enrollment->IPAddress);
                }
                return "";
             // return "<div class='loginimg'><img src='$url' class='listing-profile-pic' /></div>";
            })->editColumn('IPAddress_tmp',function($enrollment){ 
                if(!empty($enrollment->Esignature)){ 
                    // print_r($enrollment->toArray()); die('test');
                    return ($enrollment->IPAddress_tmp);
                }
                return "";
             // return "<div class='loginimg'><img src='$url' class='listing-profile-pic' /></div>";
            })
            ->filterColumn('CustomerFirstName',function($enrollment,$keyword) use($customerFirstNameQuery){  
                return  $enrollment->whereRaw($customerFirstNameQuery .' LIKE "%'.$keyword.'%"');
            })
            ->filterColumn('CustomerMiddleName',function($enrollment,$keyword) use($customerMiddleNameQuery){  
                return  $enrollment->whereRaw($customerMiddleNameQuery .' LIKE "%'.$keyword.'%"');
            })
            ->filterColumn('CustomerLastName',function($enrollment,$keyword) use($customerLastNameQuery){  
                return  $enrollment->whereRaw($customerLastNameQuery .' LIKE "%'.$keyword.'%"');
            })
            ->filterColumn('Phone',function($enrollment,$keyword) use($phoneSubQuery){  
                return  $enrollment->whereRaw($phoneSubQuery .' LIKE "%'.$keyword.'%"');
            })
            ->filterColumn('Assigned_kw',function($enrollment){  
                return  $enrollment->Assigned_kw;
            })
            ->filterColumn('Assigned_date',function($enrollment){  
                return  $enrollment->Assigned_date;
            })
            ->filterColumn('Update_by',function($enrollment){  
                return  $enrollment->Update_by;
            })
            ->filterColumn('Updated_from_status',function($enrollment){  
                return  $enrollment->Updated_from_status;
            })
            ->filterColumn('Updated_to_status',function($enrollment){  
                return  $enrollment->Updated_to_status;
            })
            ->filterColumn('Updated_at',function($enrollment){  
                return  $enrollment->Updated_at;
            })
            
            // ->editColumn('BillingName',function($enrollment) use($timeZone){  
            //     return $enrollment->BillingName .' '.$enrollment->BillingLastName;
            // }) 
           ->addColumn('action_status', function($enrollment){

                $viewmappingBtn = '<button
                    data-toggle="tooltip"
                    data-placement="top" data-container="body"
                    data-original-title="View Status Update"
                    role="button"
                    class="btn view-status-update"'.
                    'data-id="' . $enrollment->id . '"'.
                    'data-type="view-status-update"                        
                    ><b>+ View </b></button>';
                return $viewmappingBtn;
            })
            ->addColumn('action', function($enrollment){
                $route = ''; //route('critical-logs.show',$telesale->id);
                $viewBtn = '<a  data-toggle="tooltip"
                    data-placement="top"
                    data-type="view"
                    data-original-title="View"
                    data-title="View Critical Logs"
                    class="btn  theme-color"
                    href="'.$route.'"
                    >' . getimage("images/view.png") . '</a>';
                
                return '<div class="btn-group">'.$viewBtn .'<div>';
            })
            ->rawColumns(['action','action_status', 'alert_description', 'Programs', 'Esignature'])
            ->make(true);
        }
        $clients = getAllClients();
        $brands = (new Brandcontacts)->getBrandsByClient($clientId);
        $results = '';
        return view('reports.ptm_enrollment_report.index', compact('results', 'clients', 'salesCenter','brands'));
    }

    /**
     * This method is used for export data in csv or xlsx file
     * @param $request
     */
    public function exportReport(Request $request)
    {
        Log::info("In exportReport method of PTMEnrollmentReportController");

        $exportType = $request->get('export');
        $clientId = "";
        $timeZone = Auth::user()->timezone;
        
        //Added subquery for full name searching 
        $subquery = "";

        $status = "";
        if (isset($request->status) && $request->status != "") {
            $status = $request->status;
        }
        $clientId = ($request->has('client')) ? $request->get('client') : "";
        $salesCenter = ($request->has('sales_center')) ? $request->get('sales_center') : "";
        $location = ($request->has('location')) ? $request->get('location') : "";
        $channel = ($request->has('channel')) ? $request->get('channel') : "";
        $status = ($request->has('status')) ? $request->get('status') : "";
        $leadId = ($request->has('leadId')) ? $request->get('leadId') : "";
        $state = ($request->has('state')) ? $request->get('state') : "";
        $brand = ($request->has('brand')) ? $request->get('brand') : "";
        if(Auth::user()->hasAccessLevels('salescenter')) {
            $salesCenter = Auth::user()->salescenter_id;
        }
        
        if(Auth::user()->isAccessLevelToClient()) {
            $clientId =  auth()->user()->client_id;
        } else if (isset($request->client) && !empty($request->client)) {
            $clientId = $request->client;
        }

        if(Auth::user()->isLocationRestriction()) {
            $location = Auth::user()->location_id;
        }
        $date = "";
        $startDate = "";
        $endDate = "";
        if (isset($request->date_start) && !empty($request->date_start)) {
            $date = $request->date_start;
            $startDate = Carbon::parse(explode(' - ', $date)[0],$timeZone)->setTimezone('UTC');
            $endDate = Carbon::parse(explode(' - ', $date)[1],$timeZone)->setTimezone('UTC')->addDays(1);
        }

        $results = null;
        // Get result from query as per above data
        if (!empty($startDate) && !empty($endDate)) {
            $results = $this->getEnrollmentData($clientId, $salesCenter, $location, $startDate, $endDate, $channel, $status, $leadId, $state, $request->date_type, $brand,$subquery);
        }
        $results = $results->get()->toArray();
        
        // For remove columns from results array
        foreach ($results as $key => $value) {
            unset($value['type']);
            unset($value['user_with_trashed']);
            unset($value['Esignature']);
            $results[$key] = $value;
        }

        // Mapping the result data
        $results = collect($results)->map(function ($x) {
            $x['BillingFirstName'] = $x['BillingFirstName'];
            $x['BillingMiddleName'] = $x['BillingMiddleName'];
            $x['BillingLastName'] = $x['BillingLastName'];
            $x['SoldDateTime'] = Carbon::parse($x['SoldDateTime'])->setTimezone(Auth::user()
            ->timezone)->format(getDateFormat().' '.getTimeFormat());
            if(!empty($x['TPVDate']) && ($x['Status'] != config('constants.VERIFICATION_STATUS_CHART.'.ucfirst('pending'))) && ($x['Status'] != config('constants.VERIFICATION_STATUS_CHART.'.ucfirst('expired'))) && (($x['Method'] != 'Self Verification(SMS)') && ($x['Method'] != 'Self Verification(Email)'))){
                $x['TPVDate'] = Carbon::parse($x['TPVDate'])->setTimezone(Auth::user()->timezone)->format(getDateFormat().' '.getTimeFormat());
            }
            else{
                $x['TPVDate'] = '';    
            }
            if(!empty($x['TPVAgent']) && ($x['Status'] != config('constants.VERIFICATION_STATUS_CHART.'.ucfirst('pending'))) && ($x['Status'] != config('constants.VERIFICATION_STATUS_CHART.'.ucfirst('expired'))) && (($x['Method'] != 'Self Verification(SMS)') && ($x['Method'] != 'Self Verification(Email)'))){
                $x['TPVAgent'] = $x['TPVAgent'];
            }
            else{
                $x['TPVAgent'] = '';    
            }

            unset($x['ServiceLastName']);
            unset($x['pid']);
            unset($x['id']);
            return (array)$x;
        })->toArray();
        
        // Create filename with date
        $filename = "ENROLLMENT-REPORT-".date('y-m-d');

        // Use Excel for create xlsx or csv file
        Excel::create($filename, function ($excel) use ($results) {
            $excel->sheet('Report', function ($sheet) use ($results) {

                // $sheet->setColumnFormat(array(
                //     'H' => \PHPExcel_Style_NumberFormat::FORMAT_TEXT,
                //     'AS' => \PHPExcel_Style_NumberFormat::FORMAT_TEXT,
                // ));
        
                $column_name = 'A';
                foreach ($results as $key1 => $value12) {
                    if ($key1 == 0) {
                        foreach ($value12 as $cname => $cvalue) {

                            $sheet->cell($column_name . '1', $cname, function ($cell, $cellvalue) {
                                $cell->setValue($cellvalue);
                            });
                            $sheet->row($sheet->getHighestRow(), function ($row) {
                                $row->setFontWeight('bold');
                            });
                            $column_name++;
                        }
                    } else {
                        continue;
                    }
                }
                if (!empty($results)) {
                    $g = 0;
                    foreach ($results as $key => $value) {
                        $columnname = 'A';
                        if ($key == 0) {
                            $i = $key + 2;
                        }
                        foreach ($value as $cnam => $cval) {
                            if (is_numeric($value[$cnam])) {
                                $convertedValue = strval($value[$cnam]);
                                $sheet->setCellValueExplicit($columnname . $i, $convertedValue, \PHPExcel_Cell_DataType::TYPE_STRING);
                            } else {
                                $sheet->setCellValueExplicit($columnname . $i, $value[$cnam], \PHPExcel_Cell_DataType::TYPE_STRING);
                                // $sheet->setCellValue($columnname . $i, $value[$cnam]);
                            }
                            $columnname++;
                        }
                        $i++;
                    }
                }

            });
        })->download($exportType);
    }

    /**
     * Query for get lead enrollment data from database as per requirements 
     * This method is called from index and exportReport method of this controller
     * 
     * @param $clientId, $salesCenter, $location, $startDate, $endDate, $channel, $status, $leadId, $state, $leadType, $brand 
     * 
     */
    public function getEnrollmentData($clientId, $salesCenter, $location, $startDate, $endDate, $channel, $status, $leadId, $state, $leadType, $brand,$subquery)
    {
        Log::info("Start to write query for get data from various table");
        $enrollment = Telesales::leftJoin('users','users.id','=','telesales.user_id')
        ->leftJoin('clients','clients.id','=','telesales.client_id')
        ->leftJoin('salescenters','users.salescenter_id','=','salescenters.id')
        ->leftJoin('salesagent_detail','users.id','=','salesagent_detail.user_id')
        ->leftJoin('salescenterslocations','salescenterslocations.id','=','salesagent_detail.location_id')
        ->leftjoin('telesales_zipcodes','telesales_zipcodes.telesale_id','=','telesales.id')
        ->leftjoin('zip_codes','zip_codes.id','=','telesales_zipcodes.zipcode_id')
        ->leftjoin('dispositions','dispositions.id','=','telesales.disposition_id')
        // ->leftjoin('telesalesdata','telesalesdata.telesale_id','=','telesales.id')
        ->leftjoin('users as tpvagents','tpvagents.id','=','telesales.reviewed_by')
      
        ->select('telesales.id',
            DB::raw("CASE
            WHEN  telesales.status = 'verified' THEN 'Enrollment'
            ELSE 'Non-Enrollment' 
            END as 'EnrollmentType'"),
            'clients.name as Client',
            DB::raw("( select GROUP_CONCAT(name  SEPARATOR ', ') from brand_contacts where id IN(select brand_id from utilities where id IN (select utility_id from programs where id IN (select program_id from telesales_programs where telesale_id = telesales.id)))) as Brand"),
            'salescenters.name as SalesCenter','salescenterslocations.name as SalesCenterLocation',
            DB::raw("concat( users.first_name, ' ',users.last_name ) as SalesAgent"),
            'users.userid as Sales Agent ID',DB::raw("CASE
            WHEN  salesagent_detail.agent_type = 'd2d' THEN 'Door-to-Door'
            WHEN  salesagent_detail.agent_type = 'tele' THEN 'Telemarketing'
            ELSE '' 
            END as 'Channel'"),'telesales.created_at as SoldDateTime','telesales.reviewed_at as TPVDate','telesales.refrence_id as LeadID','zip_codes.state as State',DB::raw("(CASE WHEN telesales.status = 'verified' THEN 'Verified' WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' WHEN telesales.status = 'expired' THEN 'Expired' WHEN telesales.status = 'self-verified' THEN 'Self verified' ELSE 'Pending' END) as Status"),
            DB::raw("CASE
            WHEN telesales.status = 'cancel' THEN telesales.cancel_reason
            WHEN telesales.status = 'decline' THEN dispositions.description 
            WHEN telesales.status = 'hangup' THEN dispositions.description 
            ELSE ''
            END  as 'Reason'"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'first_name' and telesale_id =telesales.id  LIMIT 1 ) as 'CustomerFirstName'"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'middle_initial' and telesale_id =telesales.id  LIMIT 1 ) as 'CustomerMiddleName'"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'last_name' and telesale_id =telesales.id  LIMIT 1 ) as 'CustomerLastName'"),
            DB::raw("(select GROUP_CONCAT(meta_value SEPARATOR ', ') from telesalesdata left join form_fields on form_fields.id = telesalesdata.field_id  where telesalesdata.field_id and  LOWER(form_fields.label) LIKE 'account number%' and form_fields.form_id = telesales.form_id and telesalesdata.meta_key = 'value' and telesalesdata.telesale_id = telesales.id LIMIT 1) as AccountNumber"),
            
            DB::raw("( select GROUP_CONCAT(market  SEPARATOR ', ') from utilities where id IN (select utility_id from programs where id IN (select program_id from telesales_programs where telesale_id = telesales.id)) ) as Utility"),
            DB::raw("(select GROUP_CONCAT(commodities.name) from commodities left join form_commodities on form_commodities.commodity_id = commodities.id where form_commodities.form_id = telesales.form_id) as Commodity"),
            // DB::raw("UPPER((select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and  meta_key = 'service_address_1' and telesale_id =telesales.id LIMIT 1)) as ServiceAddress1"),
            // DB::raw("UPPER((select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_address_2' and telesale_id =telesales.id LIMIT 1)) as ServiceAddress2"),
            // DB::raw("UPPER((select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_city' and telesale_id =telesales.id LIMIT 1)) as ServiceCity"),
            // DB::raw("(select meta_value from telesalesdata where meta_key = 'service_state' and telesale_id =telesales.id LIMIT 1) as ServiceState"),
            // DB::raw("concat((select meta_value from telesalesdata where  field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_zipcode' and telesale_id =telesales.id LIMIT 1), ' ') as ServiceZipcode"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'email'  and is_primary  = 1 and form_id = telesales.form_id LIMIT 1) and telesale_id =telesales.id LIMIT 1) as Email"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'phone_number' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and telesale_id =telesales.id LIMIT 1) as Phone"),
            

            // DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'Billing Name' and form_id = telesales.form_id LIMIT 1) and meta_key = 'first_name' and telesale_id =telesales.id ) as 'BillingFirstName'"),
            // DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'Billing Name' and form_id = telesales.form_id LIMIT 1) and meta_key = 'middle_initial' and telesale_id =telesales.id ) as 'BillingMiddleName'"),
            // DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'Billing Name' and form_id = telesales.form_id LIMIT 1) and meta_key = 'last_name' and telesale_id =telesales.id ) as 'BillingLastName'"),
            // DB::raw("UPPER((select meta_value from telesalesdata  where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'billing_address_1' and telesale_id =telesales.id LIMIT 1)) as BillingAddress1"),
            // DB::raw("UPPER((select meta_value from telesalesdata  where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and  meta_key = 'billing_address_2' and telesale_id =telesales.id LIMIT 1)) as BillingAddress2"),
            // DB::raw("UPPER((select meta_value from telesalesdata  where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'billing_city' and telesale_id =telesales.id LIMIT 1)) as BillingCity"),
            // DB::raw("UPPER((select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'billing_state' and telesale_id =telesales.id LIMIT 1)) as BillingState"),
            // DB::raw("concat((select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and form_id = telesales.form_id LIMIT 1) and meta_key = 'billing_zipcode' and telesale_id = telesales.id LIMIT 1), ' ') as BillingZipcode"),
            DB::raw("( select GROUP_CONCAT(code  SEPARATOR ', ') from programs where id IN (select program_id from telesales_programs where telesale_id = telesales.id) ) as Programs"),
            DB::raw("( select GROUP_CONCAT(id  SEPARATOR ', ') from programs where id IN (select program_id from telesales_programs where telesale_id = telesales.id) ) as pid"),
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
            DB::raw("(SELECT leadmedia.url FROM leadmedia
             where leadmedia.type = 'image' and telesales_id = telesales.id ORDER BY `leadmedia`.`id` DESC LIMIT 1 ) as Esignature"),
            DB::raw("(SELECT leadmedia.ip_address FROM leadmedia
             where telesales_id = telesales.id ORDER BY `leadmedia`.`id` DESC LIMIT 1 ) as IPAddress"),
            DB::raw("(SELECT leadmedia_temps.ip_address FROM leadmedia_temps
             where telesales_tmp_id = telesales.id ORDER BY `leadmedia_temps`.`id` DESC LIMIT 1 ) as IPAddress_tmp"),
            DB::raw("(SELECT telesales_solar_update.assigned_kw FROM telesales_solar_update
             where telesales_id = telesales.id ORDER BY `telesales_solar_update`.`id` DESC LIMIT 1 ) as Assigned_kw"),
            DB::raw("(SELECT telesales_solar_update.assigned_date FROM telesales_solar_update
             where telesales_id = telesales.id ORDER BY `telesales_solar_update`.`id` DESC LIMIT 1 ) as Assigned_date"),
            DB::raw("(SELECT telesales_solar_update.update_by FROM telesales_solar_update
             where telesales_id = telesales.id ORDER BY `telesales_solar_update`.`id` DESC LIMIT 1 ) as Update_by"),
            DB::raw("(SELECT telesales_solar_update.updated_from_status FROM telesales_solar_update
             where telesales_id = telesales.id ORDER BY `telesales_solar_update`.`id` DESC LIMIT 1 ) as Updated_from_status"),
            DB::raw("(SELECT telesales_solar_update.updated_to_status FROM telesales_solar_update
             where telesales_id = telesales.id ORDER BY `telesales_solar_update`.`id` DESC LIMIT 1 ) as Updated_to_status"),
            DB::raw("(SELECT telesales_solar_update.updated_at FROM telesales_solar_update
             where telesales_id = telesales.id ORDER BY `telesales_solar_update`.`id` DESC LIMIT 1 ) as Updated_at")
            
        );
        
        // Below are the filter options for data
        if($leadType == 'submission'){
            $enrollment = $enrollment->whereBetween('telesales.created_at', [$startDate, $endDate]);
        }
        if ($leadType == 'verification') {
            $enrollment = $enrollment->whereBetween('telesales.reviewed_at', [$startDate,$endDate])
            ->whereNotIn('telesales.status', ['cancel', 'pending'])->whereIn('verification_method', ['1', '2', '6']);
        }
        if($clientId != ''){
            $enrollment = $enrollment->where('telesales.client_id', $clientId);
        }
        if($salesCenter != ''){
            $enrollment = $enrollment->where('salescenters.id', $salesCenter);
        }
        if($location != '')
        {
            $enrollment = $enrollment->where('salescenterslocations.id', $location);
        }
        if($channel != ''){
            $enrollment = $enrollment->where('salesagent_detail.agent_type', $channel);
        }
        if($status != ''){
            $enrollment = $enrollment->where('telesales.status', $status);
        }
        if($leadId != ''){
            $enrollment = $enrollment->where('telesales.refrence_id', $leadId);
        }
        if($state != ''){
            $enrollment = $enrollment->where('zip_codes.state', $state);
        }
        if ($brand != '') {
             $enrollment = $enrollment->whereHas('programs.utility.brandContacts', function (Builder $query) use ($brand) {
                $query->where('id', $brand);
            });
        }
        // $enrollment = $enrollment->groupBy('telesales.refrence_id');
        
       
        Log::info("Successfully fetched data from query.");
        return $enrollment;
    }

    /**
     * This method is used for get state from ajax call
     * @param $request
     */
    public function getStateAjax(Request $request)
    {
        Log::info("In getStateAjax method of EnrollmentReportController");

        $timeZone = Auth::user()->timezone;
        if(Auth::user()->isAccessLevelToClient()) {
            $client_id =  auth()->user()->client_id;
        } else if (isset($request->client) && !empty($request->client)) {
            $client_id = $request->client;
        }

        $status = "";
        if (isset($request->status) && $request->status != "") {
            $status = $request->status;
        }
        $clientId = ($request->has('client')) ? $request->get('client') : "";
        $salesCenter = ($request->has('sales_center')) ? $request->get('sales_center') : "";
        $location = ($request->has('location')) ? $request->get('location') : "";
        $channel = ($request->has('channel')) ? $request->get('channel') : "";
        $status = ($request->has('status')) ? $request->get('status') : "";
        $leadId = ($request->has('leadId')) ? $request->get('leadId') : "";
        $state = ($request->has('state')) ? $request->get('state') : "";
        if(Auth::user()->hasAccessLevels('salescenter')) {
            $salesCenter = Auth::user()->salescenter_id;
        }
        if(Auth::user()->isLocationRestriction()) {
            $location = Auth::user()->location_id;
        }
        $date = "";
        $startDate = "";
        $endDate = "";
        if (isset($request->submitDate) && !empty($request->submitDate)) {
            $date = $request->submitDate;
            $startDate = Carbon::parse(explode(' - ', $date)[0], $timeZone)->setTimezone('UTC');
            $endDate = Carbon::parse(explode(' - ', $date)[1], $timeZone)->setTimezone('UTC')->addDays(1);
        }

        // Query for fetch data
        $states = Telesales::leftjoin('telesales_zipcodes','telesales.id','=','telesales_zipcodes.telesale_id')
        ->leftJoin('users','users.id','=','telesales.user_id')
        ->leftjoin('zip_codes','zip_codes.id','=','telesales_zipcodes.zipcode_id')
        ->leftJoin('salescenters','users.salescenter_id','=','salescenters.id')
        ->leftJoin('salesagent_detail','users.id','=','salesagent_detail.user_id')
        ->leftJoin('salescenterslocations','salescenterslocations.id','=','users.location_id');
        if($request->leadDateType == 'submission'){
            $states = $states->whereBetween('telesales.created_at', [$startDate, $endDate]);
        }
        if($request->leadDateType == 'verification')
        {
            $states = $states->whereBetween('telesales.reviewed_at', [$startDate, $endDate])
            ->whereNotIn('telesales.status', ['cancel','pending'])->whereIn('verification_method', ['1', '2', '6']);
        }
        $states = $states->groupBy('zip_codes.state');
        
        if($clientId != ''){
            $states = $states->where('telesales.client_id', $clientId);
        }
        if($salesCenter != ''){
            $states = $states->where('salescenters.id', $salesCenter);
        }
        if($location != ''){
            $states = $states->where('salescenterslocations.id', $location);
        }
        if($channel != ''){
            $states = $states->where('salesagent_detail.agent_type', $channel);
        }
        if($status != ''){
            $states = $states->where('telesales.status', $status);
        }
        if($leadId != ''){
            $states = $states->where('telesales.refrence_id', $leadId);
        }
        if($state != ''){
            $states = $states->where('zip_codes.state', $state);
        }
        $states = $states->pluck('zip_codes.state');

        // Return states details 
        return $this->success('success', "success", $states);
    }

    /**
     * This method is used for get program details
     * @param $request
     */
    public function getProgramDetails(Request $request)
    {
        Log::info("In getProgramDetails of EnrollmentReportController");
        
        // Fetch program and setting data from database
        $program = Programs::leftjoin('customer_types','customer_types.id','=','programs.customer_type_id')->where('programs.id',$request->pId)->with('utility')->select('programs.client_id','programs.name','code','rate','term','msf','etf','unit_of_measure','utility_id','customer_types.name as customer_type','custom_field_1','custom_field_2','custom_field_3','custom_field_4','custom_field_5')->withTrashed()->first();
        $customFields = Settings::getEnableFields(array_get($program,'client_id'));
        $view = view('reports.enrollment_report.program', compact('program','customFields'))->render();
        
        // Return json repose of fetched data
        return response()->json(['status' => 'success', 'data' => $view]);
    }

    /**
     * This method is used for get brand from ajax call
     * @param $request
     */
    public function getBrandsAjax(Request $request)
    {
        // For fetch and return brandcontacts details from db
        $brands = (new Brandcontacts)->getBrandsByClient($request->clientId);
        return $this->success('success','success', $brands);
    }

    /**
     * This method is used for get brand from ajax call
     * @param $request
     */
    public function getSolarUpdates(Request $request)
    {
        try {
            $listenrollments =  (New TelesalesSolarUpdate)->getUpdates($request->enrollment_id);
            // print_r($currentUtility->toArray());
           
            $listenrollmentsHtml = view("reports.ptm_enrollment_report.list-updates", compact('listenrollments'))->render();
            // print_r($listUtility->toArray());
            return response()->json(['status' => 'success', 'data' => $listenrollmentsHtml], 200);
        } catch(\Exception $e) {
            \Log::error('Error while listing PTM solar updates :-'.$e);
            return response()->json(['status' => 'error',  'message' => $e->getMessage()], 500);
        }

    }

    public function getUpdates(){
        $listenrollments =  (New TelesalesSolarUpdate)->getUpdates($request->enrollment_id);
       return view('client.utilities.view',compact('listenrollments'));
    }    

}
