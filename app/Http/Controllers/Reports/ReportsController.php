<?php

namespace App\Http\Controllers\Reports;

use App\models\Clientsforms;
use App\models\Commodity;
use App\Services\StorageService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Reports;
use App\models\Telesales;
use App\models\Telesalesdata;
use App\models\Client;
use App\Http\Controllers\Admin\DashboardController;
use App\models\Salescenterslocations;
use App\models\Salesagentlocation;
use App\models\Salescenter;
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
use App\models\Brandcontacts;
use App\models\Leadmedia;


class ReportsController extends Controller
{

    protected $dashboard;
    
    private $reportheading = array(
        'Record ID',
        'Record Time',
        'Recording',
        'TPV Recording',
        'Script',
        'Disposition Code',
        'Auth First Name',
        'Auth Last Name',
        'Billing First Name',
        'Billing Last Name',
        'Account Number',
        'State',
        'Phone Number',
        'Sales Center Name',
        'Sales Center ID',
        'Sales Center Agent Name',
        'Sales Center Agent ID',
        'TPV Agent Name',
        'TPV Agent ID',
        'Verified',
        'Disposition',

    );


    public function __construct(Request $request)
    {
        $this->storageService = new StorageService;
        $this->dashboard = new DashboardController();
        $this->client = new Client();
        $this->criticalLogsZipExportService = new CriticalLogsZipExportService;        
    }

    /**
     * This method is used for showing report data in datatable or view(blade) page
     * @param $request
     */
    public function index(Request $request)
    {
        $client_id = "";
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

        $salesCenter = ($request->has('sales_center')) ? $request->get('sales_center') : "";
        $location = ($request->has('location')) ? $request->get('location') : "";
        $commodity = ($request->has('commodity')) ? $request->get('commodity') : "";
        if(Auth::user()->hasAccessLevels('salescenter')) {
            $salesCenter = Auth::user()->salescenter_id;
        }

        if(Auth::user()->isLocationRestriction()) {
            $location = Auth::user()->location_id;
        }
        $date = "";
        $start_date = "";
        $end_date = "";
        if (isset($request->date_start) && !empty($request->date_start)) {
            $date = $request->date_start;
            $start_date = Carbon::parse(explode(' - ', $date)[0],$timeZone)->setTimezone('UTC');
                $end_date = Carbon::parse(explode(' - ', $date)[1],$timeZone)->setTimezone('UTC')->addDays(1);
        }

        $results = [];

        if (!empty($date)) {
            $results = (new Reports)->sparkexportdailydata($client_id, $status, $start_date, $end_date,$salesCenter,$commodity,$location);
        }

        if($request->ajax()) {
            return DataTables::of($results)
            ->addColumn('SoldDate',function($results) use($timeZone){
                
                $date =  Carbon::parse($results->SoldDate)->setTimezone($timeZone)->format(getDateFormat());
                return $date;
            })
            ->make(true);
        }

        // if (Auth::user()->access_level == 'tpv') {
        //     $clients = Client::where('status', 'active')->get();
        // } else if (Auth::user()->access_level == 'client') {
        //     $clients = Client::where('status', 'active')->where('id', auth()->user()->client_id)->get();
        // } else if (Auth::user()->access_level == 'salescenter') {
        //     $clients = Client::where('status', 'active')->where('id', auth()->user()->client_id)->get();
        // } else {
        //     $clients = Client::where('status', 'active')->get();
        // }
        $clients = getAllClients();
        $export_params['export'] = 1;
        return view('reports.reportform', compact('results', 'clients', 'export_params', 'salesCenter', 'commodity'));
    }
    
    /**
     * This method is used for print sales activity data
     * @param $request
     */
    public function salesActivity(Request $request)
    {
        $client_id = "";
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

        $salesCenter = ($request->has('sales_center')) ? $request->get('sales_center') : "";
        $location = ($request->has('location')) ? $request->get('location') : "";
        $brand = ($request->has('brand')) ? $request->get('brand') : "";
        $commodity = ($request->has('commodity')) ? $request->get('commodity') : "";
        if(Auth::user()->hasAccessLevels('salescenter')) {
            $salesCenter = Auth::user()->salescenter_id;
        }
        if(Auth::user()->isLocationRestriction()) {
            $location = Auth::user()->location_id;
        }
        $date = "";
        $start_date = "";
        $end_date = "";
        if (isset($request->date_start) && !empty($request->date_start)) {
            $date = $request->date_start;
            $start_date = Carbon::parse(explode(' - ', $date)[0],$timeZone)->setTimezone('UTC');
                $end_date = Carbon::parse(explode(' - ', $date)[1],$timeZone)->setTimezone('UTC')->addDays(1);
        }

        $results = [];
        
        if (!empty($date)) {
            $query_params = $request->all();
            $query_params['date_start']=$start_date;
            $query_params['date_end']=$end_date;
            $query_params['client']=$client_id;
            $query_params['sales_center']=$salesCenter;
            $query_params['location_id']=$location;
            $query_params['brandId']=$brand;
            $query_params['commodity']=$commodity;
            $query_params['export']=$request->input('export',0);
            $results = (new Reports)->salesagentactivityNew($query_params);
            
            if($request->export) {
                $this->exportSalesActivity($results);
            }
        }
        
        if($request->ajax()) {
            
            return DataTables::of($results)
            ->editColumn('LeadDate',function($results) use($timeZone){
                return Carbon::parse($results->LeadDate)->setTimezone($timeZone)->format(getDateFormat());
            })
            ->editColumn('CallDateTime',function($results)use($timeZone) {
                return Carbon::parse($results->CallDateTime)->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat());
            })->make(true);
        }
        // if (Auth::user()->access_level == 'tpv') {
        //     $clients = Client::where('status', 'active')->get();
        // } else if (Auth::user()->access_level == 'client') {
        //     $clients = Client::where('status', 'active')->where('id', auth()->user()->client_id)->get();
        // } else if (Auth::user()->access_level == 'salescenter') {
        //     $clients = Client::where('status', 'active')->where('id', auth()->user()->client_id)->get();
        // } else {
        //     $clients = Client::where('status', 'active')->get();
        // }
        $clients = getAllClients();
        $brands = (new Brandcontacts)->getBrandsByClient($client_id);
        return view('reports.salesactivity.reportform', compact('results', 'clients',  'salesCenter', 'commodity','brands'));

    }

    /**
     * Old index method 
     */
    public function indexOld(Request $request)
    {
        $client_id = "";
        if (isset($request->client) && !empty($request->client)) {
            $client_id = $request->client;
        }
        $status = "";
        if (isset($request->vendorstatus) && $request->vendorstatus != "") {
            $status = $request->vendorstatus;
        }
        $userstatus = "";
        if (isset($request->userstatus) && $request->userstatus != "") {
            $userstatus = $request->userstatus;
        }

        $locations = (new Salescenterslocations)->getLocationsList($client_id);
        $salesagents = (new User)->getClientSalesagentsForReport($client_id, $status, $userstatus);

        $programs = $this->getAllProgramsForReport($request);


        $query_params = $export_params = array();


        $clients = $this->client->getClientsListByStatus($status);
        $utilities = array();
        $ComplianceReportdata = $query_param = $results = array();
        $templates = array();
        if (isset($request->search)) {
            $this->validate($request, [
                'template' => 'required|numeric',
                'client' => 'required|numeric',
                'utility' => 'required|numeric',
            ]);
            $ComplianceReportdata = $this->getComplianceReport($request);
            $utilities = (New Utilities)->getClientAllUtilities($request->client);
            $templates = (new ComplianceTemplates)->utilitiestemplateslistadmin($request->client, $request->utility);
        }
        if (isset($request['tpvreport'])) {
            $query_params = $export_params = $this->get_query_params($request);
            if ($request['tpvreport'] == 1) {
                $results = (new Reports)->dailyExportResults($query_params);
            }
            if ($request['tpvreport'] == 2) {
                $results = (new Reports)->sparkexportdailydata($query_params);
            }
            if ($request['tpvreport'] == 3) {
                $results = (new Reports)->salesagentactivity($query_params);
            }


            $export_params['export'] = 1;


        }


        return view('reports.reportform', compact('locations', 'clients', 'utilities', 'ComplianceReportdata', 'templates', 'results', 'query_params', 'export_params', 'programs', 'salesagents'));
    }
    
    /**
     * For get data of sales report
     */
    public function salesreport(Request $request)
    {

         $client_id = "";
        if (isset($request->client) && !empty($request->client)) {
            $client_id = $request->client;
        }
        $status = "";
        if (isset($request->status) && $request->status != "") {
            $status = $request->status;
        }

        $salesCenter = ($request->has('sales_center')) ? $request->get('sales_center') : "";
        $commodity = ($request->has('commodity')) ? $request->get('commodity') : "";

        $date = "";
        $start_date = "";
        $end_date = "";
        if (isset($request->date_start) && !empty($request->date_start)) {
            $date = $request->date_start;
            $start_date = Carbon::parse(explode(' - ', $date)[0])->toDateString();
            $end_date = Carbon::parse(explode(' - ', $date)[1])->toDateString();
        }
        $results = null;

        if (!empty($client_id) && !empty($date)) {

            $results = (new Reports)->dailyExportResults($client_id, $status, $start_date, $end_date, $salesCenter, $commodity);
        }

        if (Auth::user()->access_level == 'tpv') {
            $clients = Client::where('status', 'active')->get();
        } else if (Auth::user()->access_level == 'client') {
            $clients = Client::where('status', 'active')->where('id', auth()->user()->client_id)->get();
        } else if (Auth::user()->access_level == 'salescenter') {
            $clients = Client::where('status', 'active')->where('id', auth()->user()->client_id)->get();
        } else {
            $clients = Client::where('status', 'active')->get();
        }
        $export_params['export'] = 1;
        return view('reports.salesreportform', compact('results', 'clients', 'export_params', 'salesCenter', 'commodity'));
    }

    /**
     * For get and display data compliance report
     */
    public function getComplianceReport($request)
    {

        $params = $this->compliance_report_query_params($request);
        $template_id = $params['template'];
        $compliance_templates = (new ComplianceTemplates)->utilitiestemplateslistadmin($params['client_id'], $params['utility_id']);
        $template_detail = (new ComplianceTemplates)->gettemplate($template_id);
        $maped_fields = unserialize($template_detail->fields);
        $params['client_id'] = $template_detail->Client_id;
        die('ss');
        $params['form_id'] = $template_detail->form_id;
        return $compliance_data = (new ComplianceReports)->getTemplateData($maped_fields, $params);
    }

    /**
     * For get and display data of batch report
     */
    public function batchreport(Request $request)
    {
        if (isset($request->ctid)) {
            $params = array();
            if (isset($request->daterange) && $request->daterange != '') {
                list($params['date_start'], $params['date_end']) = explode('-', $request->daterange);

            }
            $compliance_report_template_id = $request->ctid;
            $template_detail = (new ComplianceTemplates)->gettemplate($compliance_report_template_id);
            $maped_fields = unserialize($template_detail->fields);
            $params['client_id'] = $client_id = $template_detail->Client_id;
            $params['form_id'] = $template_detail->form_id;
            $report_data = (new ComplianceReports)->getTemplateData($maped_fields, $params);
            return view('reports.singlecompliance', compact('client_id', 'client', 'report_data', 'request'));

        } else {
            abort(404);
        }


    }

    /**
     * This method is used to export client wise batch data in excel or csv file
     */
    public function exportbatch(Request $request)
    {
        if (isset($request->ctid)) {
            $compliance_report_template_id = $request->ctid;
            $template_detail = (new ComplianceTemplates)->gettemplate($compliance_report_template_id);
            $maped_fields = unserialize($template_detail->fields);
            $params['client_id'] = $template_detail->Client_id;
            $params['form_id'] = $template_detail->form_id;
            if (isset($request->daterange) && $request->daterange != '') {
                list($params['date_start'], $params['date_end']) = explode('-', $request->daterange);

            }
            $compliance_data = (new ComplianceReports)->exportData($maped_fields, $params);
            $filename = "Batch-Export-" . date('y-m-d');
            Excel::create($filename, function ($excel) use ($compliance_data) {

                $excel->sheet('Report', function ($sheet) use ($compliance_data) {


                    $i = 1;
                    foreach ($compliance_data as $data_to_export) {
                        $column_name = 'A';
                        foreach ($data_to_export as $key => $value) {
                            if ($i == 1) {
                                $column_value = $key;
                            } else {
                                $column_value = $value;
                            }
                            $sheet->cell($column_name . $i, $column_value, function ($cell, $cellvalue) {
                                $cell->setValue($cellvalue);
                            });
                            $column_name++;
                            // code...
                        }
                        $i++;

                    }
                });
            })->download();
        } else {
            abort(404);
        }
    }

    /**
     * This method is used to fetch and export all the batch data in excel or csv file
     */
    function batchexportall(Request $request)
    {
        if (isset($request->selected_templates)) {
            $compliance_templates = $request->selected_templates;
        } else {
            $compliance_templates = $request->all_templates;
        }


        if (count($compliance_templates) > 0) {
            $current_folder = time();
            $files = array();

            foreach ($compliance_templates as $template_id) {

                $template_detail = (new ComplianceTemplates)->gettemplate($template_id);
                $maped_fields = unserialize($template_detail->fields);
                $params['client_id'] = $template_detail->Client_id;
                $params['form_id'] = $template_detail->form_id;
                if (isset($request->daterange) && $request->daterange != '') {
                    list($params['date_start'], $params['date_end']) = explode('-', $request->daterange);

                }
                $compliance_data = (new ComplianceReports)->exportData($maped_fields, $params);
                $filename = $template_detail->name . time() . rand();

                Excel::create($filename, function ($excel) use ($compliance_data) {

                    $excel->sheet('Report', function ($sheet) use ($compliance_data) {


                        $i = 1;
                        foreach ($compliance_data as $data_to_export) {
                            $column_name = 'A';
                            foreach ($data_to_export as $key => $value) {
                                if ($i == 1) {
                                    $column_value = $key;
                                } else {
                                    $column_value = $value;
                                }
                                $sheet->cell($column_name . $i, $column_value, function ($cell, $cellvalue) {
                                    $cell->setValue($cellvalue);
                                });
                                $column_name++;
                                // code...
                            }
                            $i++;

                        }
                    });
                })->store('xls', storage_path('excel/exports/' . $current_folder));

                $files[] = storage_path('excel/exports/' . $current_folder) . '/' . $filename . ".xls";
            }
            // Create Zip

            $zipname = ini_get('upload_tmp_dir') . "/ComplianceExport-" . rand() . '.zip';
            if (count($files) > 0) {

                Zipper::make($zipname)->add($files)->close();
            }
            // Delete folder from storage
            $dir = storage_path('excel/exports/' . $current_folder);
            if (is_dir($dir)) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (filetype($dir . "/" . $object) == "dir") rrmdir($dir . "/" . $object); else unlink($dir . "/" . $object);
                    }
                }
                reset($objects);
                rmdir($dir);
            }

            header("Content-type: application/zip");
            header("Content-Disposition: attachment; filename=" . basename($zipname));
            header("Content-length: " . filesize($zipname));
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile("$zipname");
            unlink($zipname);


        }
    }

    /**
     * This method is used for get data of search result
     */
    public function results(Request $request)
    {

        $query_params = $export_params = $this->get_query_params($request);

        $export_params['export'] = 1;
        $results = (new Reports)->searchResults($query_params);

        return view('reports.results', compact('results', 'query_params', 'export_params'));
    }

    /**
     * For get or filter data for export
     */
    public function exportresults(Request $request)
    {

        $query_params = $this->get_query_params($request);

        if (isset($query_params['tpvreport']) && $query_params['tpvreport'] == 1) {
            $this->downloadsalesreport($query_params);
        }
        if (isset($query_params['tpvreport']) && $query_params['tpvreport'] == 2) {
            $this->downloadenrollmentfile($query_params);
        }
        if (isset($query_params['tpvreport']) && $query_params['tpvreport'] == 3) {
            $this->downloadsalesactivity($query_params);
        }


    }

    /**
     * For fetch and export enrollment report data in excel or csv file
     */
    public function exportEnrollmentReport(Request $request) {
        $client_id = "";
        if(Auth::user()->isAccessLevelToClient()) {
            $client_id =  auth()->user()->client_id;
        } else if (isset($request->client) && !empty($request->client)) {
            $client_id = $request->client;
        }
        $status = "";
        if (isset($request->status) && $request->status != "") {
            $status = $request->status;
        }

        $salesCenter = ($request->has('sales_center')) ? $request->get('sales_center') : "";
        $location = ($request->has('location')) ? $request->get('location') : "";
        $commodity = ($request->has('commodity')) ? $request->get('commodity') : "";
        if(Auth::user()->hasAccessLevels('salescenter')) {
            $salesCenter = Auth::user()->salescenter_id;
        }

        if(Auth::user()->isLocationRestriction()) {
            $location = Auth::user()->location_id;
        }
        $date = "";
        $start_date = "";
        $end_date = "";
        if (isset($request->date_start) && !empty($request->date_start)) {
            $date = $request->date_start;
            $start_date = Carbon::parse(explode(' - ', $date)[0])->toDateString();
            $end_date = Carbon::parse(explode(' - ', $date)[1])->toDateString();
        }

        $results = null;
        if (!empty($start_date) && !empty($end_date)) {
            $export = true;
            $results = (new Reports)->sparkexportdailydata($client_id, $status, $start_date, $end_date,$salesCenter,$commodity,$location,$export);
        }

        $results = collect($results)->map(function ($x) {
            return (array)$x;
        })->toArray();

        $filename = "ENROLLMENT-REPORT-".date('y-m-d');
        Excel::create($filename, function ($excel) use ($results) {

                $excel->sheet('Report', function ($sheet) use ($results) {

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
                                $sheet->setCellValue($columnname . $i, $value[$cnam]);
                                $columnname++;
                            }
                            $i++;

                        }
                    }

                });
            })->download();
    }

    /**
     * This method is used to export sales report data in excel or csv file
     */
    public function exportSalesReport(Request $request) {
         $client_id = "";
        if (isset($request->client) && !empty($request->client)) {
            $client_id = $request->client;
        }
        $status = "";
        if (isset($request->status) && $request->status != "") {
            $status = $request->status;
        }

        $salesCenter = ($request->has('sales_center')) ? $request->get('sales_center') : "";
        $commodity = ($request->has('commodity')) ? $request->get('commodity') : "";

        $date = "";
        $start_date = "";
        $end_date = "";
        if (isset($request->date_start) && !empty($request->date_start)) {
            $date = $request->date_start;
            $start_date = Carbon::parse(explode(' - ', $date)[0])->toDateString();
            $end_date = Carbon::parse(explode(' - ', $date)[1])->toDateString();
        }

        $results = null;

        if (!empty($client_id) && !empty($date)) {

            $results = (new Reports)->dailyExportResults($client_id, $status, $start_date, $end_date, $salesCenter, $commodity,1);
        }

        $results = collect($results)->map(function ($x) {
            return (array)$x;
        })->toArray();

        $filename = "Sales-REPORT-".date('y-m-d');
        Excel::create($filename, function ($excel) use ($results) {

                $excel->sheet('Report', function ($sheet) use ($results) {

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
                                $sheet->setCellValue($columnname . $i, $value[$cnam]);
                                $columnname++;
                            }
                            $i++;

                        }
                    }

                });
            })->download();
    }

    /**
     * This method is used to export sales activity data in excel or csv file
     * @param $data
     */
    public function exportSalesActivity($data) {

        $timeZone = Auth::user()->timezone;
        $results = collect($data)->map(function ($x) use($timeZone){
            // dd($x->LeadDate);
            $x->LeadDate =  Carbon::parse($x->LeadDate)->setTimezone($timeZone)->format(getDateFormat());
            $x->CallDateTime = Carbon::parse($x->CallDateTime)->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat());
            
            return (array)$x;
        })->toArray();
        
        $filename = "SALES-ACTIVITY-REPORT-".date('y-m-d');

        Excel::create($filename, function ($excel) use ($results) {

                $excel->sheet('Report', function ($sheet) use ($results) {

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
                                $sheet->setCellValue($columnname . $i, $value[$cnam]);
                                $columnname++;
                            }
                            $i++;

                        }
                    }

                });
            })->download();
    }

    /**
     * This method is used to fetch and export data in excel or csv file and then download that file 
     * @param $query_params
     */
    public function downloadsalesreport($query_params)
    {
        $results = (new Reports)->dailyExportResults($query_params);


        $results = collect($results)->map(function ($x) {
            return (array)$x;
        })->toArray();


        $filename = "DAILY-SALES-REPORT-" . date('y-m-d');
        Excel::create($filename, function ($excel) use ($results) {

            $excel->sheet('Report', function ($sheet) use ($results) {

                $column_name = 'A';


                foreach ($results as $key1 => $value12) {
                    if ($key1 == 0) {

                        foreach ($value12 as $cname => $cvalue) {

                            $sheet->cell($column_name . '1', $cname, function ($cell, $cellvalue) {
                                $cell->setValue($cellvalue);
                            });
                            $sheet->getStyle($column_name . '1')->getAlignment()->applyFromArray(
                                array('horizontal' => 'center')
                            );

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


                        if (isset($value['UtilityTypeName']) && $value['UtilityTypeName'] == 'Dual Fuel') {

                            $new_results = (new Reports)->DualDataElectricCommodity($value['ExternalSalesId']);
                            $new_results = collect($new_results)->map(function ($x) {
                                return (array)$x;
                            })->toArray();

                            if (count($new_results) > 0) {
                                foreach ($new_results[0] as $cnam => $cval) {
                                    $sheet->setCellValue($columnname . $i, strtoupper($new_results[0][$cnam]));
                                    $sheet->getStyle($columnname . $i)->getAlignment()->applyFromArray(
                                        array('horizontal' => 'center')
                                    );
                                    $columnname++;

                                }
                            }

                            $i++;

                            $new_results = (new Reports)->DualDataGasCommodity($value['ExternalSalesId']);
                            $new_results = collect($new_results)->map(function ($x) {
                                return (array)$x;
                            })->toArray();
                            if (count($new_results) > 0) {

                                //     dd($new_results);
                                $columnname = 'A';
                                foreach ($new_results[0] as $cnam => $cval) {
                                    $sheet->setCellValue($columnname . $i, strtoupper($new_results[0][$cnam]));
                                    $sheet->getStyle($columnname . $i)->getAlignment()->applyFromArray(
                                        array('horizontal' => 'center')
                                    );
                                    $columnname++;

                                }

                            }
                            $i++;

                        } else {

                            foreach ($value as $cnam => $cval) {
                                $sheet->setCellValue($columnname . $i, strtoupper($value[$cnam]));
                                $sheet->getStyle($columnname . $i)->getAlignment()->applyFromArray(
                                    array('horizontal' => 'center')
                                );

                                $columnname++;

                            }


                            $i++;
                        }


                    }
                }

            });
        })->download();
    }

    /**
     * This is sub function to get query parameter for compliance report
     * This method is called from getComplianceReport method of this controller
     * 
     * @param $request
     */
    public function compliance_report_query_params($request)
    {
        $params = array();
        if (isset($request->template)) {
            $params['template'] = $request->template;
        }
        if (isset($request->client)) {
            $params['client_id'] = $request->client;
        }
        if (isset($request->utility)) {
            $params['utility_id'] = $request->utility;
        }
        if (isset($request->date_start)) {
            list($params['date_start'], $params['date_end']) = explode('-', $request->date_start);
        }
        if (isset($request->daterange)) {
            list($params['date_start'], $params['date_end']) = explode('-', $request->daterange);
        }
        return $params;

    }

    /**
     * This is sub function to get query parameter
     * This method is called from programreport, programexport, exportresults, indexOld, results method of this controller
     * 
     * @param $request
     */
    function get_query_params($request)
    {
        $query_params = array();
        if (isset($request->referenceid)) {
            $query_params['refrence_id'] = $request->referenceid;
        }
        if (isset($request->refrence_id)) {
            $query_params['refrence_id'] = $request->refrence_id;
        }
        if (isset($request->phonenumber)) {
            $query_params['phonenumber'] = $request->phonenumber;
        }
        if (isset($request->salesagentid)) {
            $query_params['salesagentid'] = $request->salesagentid;
        }
        if (isset($request->tpvagentid)) {
            $query_params['tpvagentid'] = $request->tpvagentid;
        }
        if (isset($request->accountnumber)) {
            $query_params['accountnumber'] = $request->accountnumber;
        }
        if (isset($request->vendorstatus)) {
            $query_params['vendorstatus'] = $request->vendorstatus;
        }
        if (isset($request->userstatus)) {
            $query_params['userstatus'] = $request->userstatus;
        }
        if (isset($request->program)) {
            $query_params['program'] = $request->program;
        }
        if (isset($request->salesagent)) {
            $query_params['salesagent'] = $request->salesagent;
        }


        if (isset($request->date_start)) {
            if (strpos($request->date_start, '-')) {
                list($query_params['date_start'], $query_params['date_end']) = explode('-', $request->date_start);
            } else {
                $query_params['date_start'] = $request->date_start;
            }

            //$query_params['date_start'] = $request->date_start;
        }
        if (isset($request->date_end)) {
            $query_params['date_end'] = $request->date_end;
        }
        if (isset($request->locationid)) {
            $query_params['location_id'] = $request->locationid;
        }
        if (isset($request->status)) {
            $query_params['status'] = $request->status;
        }
        if (isset($request->export)) {
            $query_params['export'] = 1;
        }
        if (isset($request->client)) {
            $query_params['client'] = $request->client;
        }
        if (isset($request->tpvreport)) {
            $query_params['tpvreport'] = $request->tpvreport;
        }


        return $query_params;
    }


    /**
     * Ajax method for get data of dashboard report
     */
    public function ajaxgetdashboardreport(Request $request)
    {
        $enddate = date('Y-m-d');
        if ($request->report_time == 'today') {
            $start_date = $enddate;
        } else if ($request->report_time == 'mtd') {
            $start_date = $this->dashboard->get_month_difference($enddate);
        } else if ($request->report_time == 'ytd') {
            $start_date = $this->dashboard->get_year_difference($enddate);
        } else {
            $start_date = $this->dashboard->get_week_difference($enddate);
        }

        $where = array('start_date' => $start_date, 'end_date' => $enddate);
        $top_salescenters = (new Reports)->getTopSalesCenters($where);
        $top_offices = (new Reports)->getTopOffice($where);
        $top_agents = (new Reports)->getTopAgents($where);
        return view('admin.elements.tpvusers.index', compact('top_clients', 'top_offices', 'top_agents', 'top_salescenters'));

    }

    /**
     * Ajax method for get data of client dashboard report
     */
    public function ajaxgetclientdashboardreport(Request $request)
    {
        $enddate = date('Y-m-d');
        if ($request->report_time == 'today') {
            $start_date = $enddate;
        } else if ($request->report_time == 'mtd') {
            $start_date = $this->dashboard->get_month_difference($enddate);
        } else if ($request->report_time == 'ytd') {
            $start_date = $this->dashboard->get_year_difference($enddate);
        } else {
            $start_date = $this->dashboard->get_week_difference($enddate);
        }
        $where = array('start_date' => $start_date, 'end_date' => $enddate);
        if ($request->client_id) {
            $where['client_id'] = $request->client_id;
        }


        $top_offices = (new Reports)->getTopOffice($where);
        $top_agents = (new Reports)->getTopAgents($where);
        return view('admin.elements.client.index', compact('top_offices', 'top_agents'));

    }

    /**
     * This method is used to store/export file in ftp
     */
    public function exportfiletoftp()
    {
        $results = (new Reports)->exportdailydata();


        $results = collect($results)->map(function ($x) {
            return (array)$x;
        })->toArray();
        // print_r($results);
        // die();


        $filename = "DAILY-SALES-REPORT-" . date('y-m-d');
        Excel::create($filename, function ($excel) use ($results) {

            $excel->sheet('Report', function ($sheet) use ($results) {

                $column_name = 'A';

                foreach ($results as $key1 => $value12) {
                    if ($key1 == 0) {

                        foreach ($value12 as $cname => $cvalue) {

                            $sheet->cell($column_name . '1', $cname, function ($cell, $cellvalue) {
                                $cell->setValue($cellvalue);
                            });
                            $sheet->getStyle($column_name . '1')->getAlignment()->applyFromArray(
                                array('horizontal' => 'center')
                            );

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


                        if (isset($value['UtilityTypeName']) && $value['UtilityTypeName'] == 'Dual Fuel') {

                            $new_results = (new Reports)->DualDataElectricCommodity($value['ExternalSalesId']);
                            $new_results = collect($new_results)->map(function ($x) {
                                return (array)$x;
                            })->toArray();

                            if (count($new_results) > 0) {
                                foreach ($new_results[0] as $cnam => $cval) {
                                    $sheet->setCellValue($columnname . $i, $new_results[0][$cnam]);
                                    $sheet->getStyle($columnname . $i)->getAlignment()->applyFromArray(
                                        array('horizontal' => 'center')
                                    );
                                    $columnname++;

                                }
                                $i++;
                            }


                            $new_results = (new Reports)->DualDataGasCommodity($value['ExternalSalesId']);
                            $new_results = collect($new_results)->map(function ($x) {
                                return (array)$x;
                            })->toArray();
                            if (count($new_results) > 0) {

                                //     dd($new_results);
                                $columnname = 'A';
                                foreach ($new_results[0] as $cnam => $cval) {
                                    $sheet->setCellValue($columnname . $i, $new_results[0][$cnam]);
                                    $sheet->getStyle($columnname . $i)->getAlignment()->applyFromArray(
                                        array('horizontal' => 'center')
                                    );
                                    $columnname++;

                                }
                                $i++;
                            }


                        } else {
                            foreach ($value as $cnam => $cval) {
                                $sheet->setCellValue($columnname . $i, $value[$cnam]);
                                $sheet->getStyle($columnname . $i)->getAlignment()->applyFromArray(
                                    array('horizontal' => 'center')
                                );
                                $columnname++;

                            }
                            $i++;
                        }


                    }
                }

            });
        })->store('xls', getcwd() . '/excel/exports');
        // download()
    }

    /**
     * This method is used to export file to ftp and send mail : only for spark energy
     */
    public function sparkexportfiletoftp()
    {
        $params = array();
        $params['date_start'] = date('Y-m-d', strtotime("-1 days"));
        $params['date_end'] = "";
        $params['export'] = 1;
        $params['save'] = 1;
        $file_path = $this->downloadenrollmentfile($params);

        if (!empty($file_path)) {
            $subject = "Enrollment Report " . date('jS F, Y');
            $mainMessage = "Hi <br>";
            $mainMessage .= "Please find the attached file of Enrollment Report.<br>";
            $mainMessage .= "Regards<br>";
            $send_email_to = 'dalvir@matrixmarketers.com,kiral.desai@contactpoint360.com,rinal.shah@contactpoint360.com,Trinh.Tran@sparkenergy.com';
            $this->sendFileInEmail($send_email_to, $subject, $mainMessage, $file_path);
        }



    }

    /**
     * For download enrollment report file
     * @param $params
     */
    public function downloadenrollmentfile($params)
    {

        $results = (new Reports)->sparkexportdailydata($params);

        $results = collect($results)->map(function ($x) {
            return (array)$x;
        })->toArray();

        //$filename = "ENROLLMENT-REPORT-".date('y-m-d');
        $filename = date('Y_m_d') . "_Spark_TPVPlus_Batch";
        if ((isset($params['export']) && $params['export'] == 1) && !isset($params['save'])) {
            Excel::create($filename, function ($excel) use ($results) {

                $excel->sheet('Report', function ($sheet) use ($results) {

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


                            if (isset($value['CommodityType']) && $value['CommodityType'] == 'Dual Fuel') {

                                $new_results = (new Reports)->SparkDualDataElectricCommodity($value['ExternalSalesID']);
                                $new_results = collect($new_results)->map(function ($x) {
                                    return (array)$x;
                                })->toArray();

                                if (count($new_results) > 0) {
                                    foreach ($new_results[0] as $cnam => $cval) {
                                        $sheet->setCellValue($columnname . $i, $new_results[0][$cnam]);
                                        $columnname++;

                                    }
                                }
                                $i++;

                                $new_results = (new Reports)->SparkDualDataGasCommodity($value['ExternalSalesID']);
                                $new_results = collect($new_results)->map(function ($x) {
                                    return (array)$x;
                                })->toArray();
                                if (count($new_results) > 0) {

                                    //     dd($new_results);
                                    $columnname = 'A';
                                    foreach ($new_results[0] as $cnam => $cval) {
                                        $sheet->setCellValue($columnname . $i, $new_results[0][$cnam]);
                                        $columnname++;

                                    }
                                }
                                $i++;

                            } else {
                                foreach ($value as $cnam => $cval) {
                                    $sheet->setCellValue($columnname . $i, $value[$cnam]);
                                    $columnname++;

                                }
                                $i++;
                            }


                        }
                    }

                });
            })->download();

        } else {
            if (count($results) == 0) {
                return;
            }
            Excel::create($filename, function ($excel) use ($results) {

                $excel->sheet('Report', function ($sheet) use ($results) {

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


                            if (isset($value['CommodityType']) && $value['CommodityType'] == 'Dual Fuel') {

                                $new_results = (new Reports)->SparkDualDataElectricCommodity($value['ExternalSalesID']);
                                $new_results = collect($new_results)->map(function ($x) {
                                    return (array)$x;
                                })->toArray();

                                if (count($new_results) > 0) {
                                    foreach ($new_results[0] as $cnam => $cval) {
                                        $sheet->setCellValue($columnname . $i, $new_results[0][$cnam]);
                                        $columnname++;

                                    }
                                }
                                $i++;

                                $new_results = (new Reports)->SparkDualDataGasCommodity($value['ExternalSalesID']);
                                $new_results = collect($new_results)->map(function ($x) {
                                    return (array)$x;
                                })->toArray();
                                if (count($new_results) > 0) {

                                    //     dd($new_results);
                                    $columnname = 'A';
                                    foreach ($new_results[0] as $cnam => $cval) {
                                        $sheet->setCellValue($columnname . $i, $new_results[0][$cnam]);
                                        $columnname++;

                                    }
                                }
                                $i++;

                            } else {
                                foreach ($value as $cnam => $cval) {
                                    $sheet->setCellValue($columnname . $i, $value[$cnam]);
                                    $columnname++;

                                }
                                $i++;
                            }


                        }
                    }

                });
            })->store('xls', getcwd() . '/excel/exports');

            return getcwd() . '/excel/exports/' . $filename . ".xls";
        }

    }

    /**
     * This method is used for get list of all programs
     */
    public function getAllProgramsForReport(Request $request)
    {
        $client_id = "";
        if (isset($request->client) && !empty($request->client)) {
            $client_id = $request->client;
        }
        $status = "";
        if (isset($request->vendorstatus) && $request->vendorstatus != "") {
            $status = $request->vendorstatus;
        }


        return $programs = (new Programs)->getAllProgramsForReport($client_id, $status);
    }

    /**
     * This is ajax method for get list of all programs
     */
    public function ajaxgetAllProgramsForReport(Request $request)
    {
        $client_id = "";
        if (isset($request->client) && !empty($request->client)) {
            $client_id = $request->client;
        }
        $status = "";
        if (isset($request->vendorstatus) && $request->vendorstatus != "") {
            $status = $request->vendorstatus;
        }


        $programs = (new Programs)->getAllProgramsForReport($client_id, $status);
        $options = "";
        if (count($programs) > 0) {
            foreach ($programs as $program) {
                $options .= "<option value='" . $program->id . "'>" . $program->program_name . "(" . $program->code . ") " . $program->client_name . "</option>";
            }

        }

        return array('status' => 'success', 'options' => $options);


    }

    /**
     * This is ajax method for get list of all sales agents
     */
    public function ajaxgetAllsalesagentsForReport(Request $request)
    {
        $client_id = "";
        $client_status = "";
        $user_status = "";
        if (isset($request->client) && !empty($request->client)) {
            $client_id = $request->client;
        }

        if (isset($request->vendorstatus) && $request->vendorstatus != "") {
            $status = $request->vendorstatus;
        }
        if (isset($request->userstatus) && !empty($request->userstatus)) {
            $user_status = $request->userstatus;
        }
        $salesagents = (new User)->getClientSalesagentsForReport($client_id, $client_status, $user_status);
        $options = "";
        if (count($salesagents) > 0) {
            foreach ($salesagents as $salesagent) {
                $options .= "<option value='" . $salesagent->id . "'>" . $salesagent->first_name . " " . $salesagent->last_name . "(" . $salesagent->userid . ") </option>";
            }

        }

        return array('status' => 'success', 'options' => $options);


    }

    /**
     * This method is used for create office report
     */
    public function officereport(Request $request)
    {
        $exportinactivesalesagents = array();
        $exportinactivesalesagents['export'] = 1;
        $client_id = "";
        $clients = (new Client)->getClientsListByStatus();
        $sale_centers = array();
        $salecenter_id = "";
        $location_id = "";
        $locations = array();
        $users = array();

        if (isset($request->client)) {
            $client_id = $request->client;
            if (!empty($request->salecenter)) {
                $salecenter_id = $request->salecenter;
                $locations = (new Salescenterslocations)->getLocationsInfo($client_id, $salecenter_id);
            }
            if (!empty($request->location)) {
                $location_id = $request->location;
            }
            $sale_centers = (New Salescenter)->getSalesCentersListByClientID($client_id);
            $users = (New user)->InactiveSalesAgentsList($client_id, $salecenter_id, $location_id);
        }
        $exportinactivesalesagents['client_id'] = $client_id;
        $exportinactivesalesagents['salecenter_id'] = $salecenter_id;
        $exportinactivesalesagents['location_id'] = $location_id;


        return view('reports.inactivesalesagent.index', compact('clients', 'client_id', 'users', 'sale_centers', 'salecenter_id', 'location_id', 'locations', 'exportinactivesalesagents'))
            ->with('i', ($request->input('page', 1) - 1) * 20);
    }

    /**
     * This method is used for export inactive sales agents in excel or csv file
     */
    public function inactiveSalesagentsExports(Request $request)
    {
        $salecenter_id = "";
        $location_id = "";
        $client_id = "";
        if (isset($request->client_id)) {
            $client_id = $request->client_id;
            if (!empty($request->salecenter_id)) {
                $salecenter_id = $request->salecenter_id;
            }
            if (!empty($request->location_id)) {
                $location_id = $request->location_id;
            }
            $results = (New user)->InactiveSalesAgentsListForExport($client_id, $salecenter_id, $location_id);

            $filename = "Inactive-Salesagents-" . date('y-m-d');
            $results = collect($results)->map(function ($x) {
                return (array)$x;
            })->toArray();
            Excel::create($filename, function ($excel) use ($results) {

                $excel->sheet('Report', function ($sheet) use ($results) {

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
                                $sheet->setCellValue($columnname . $i, $value[$cnam]);
                                $columnname++;

                            }
                            $i++;


                        }
                    }

                });
            })->download();


        }

    }

    /**
     * For state training
     */
    function statetraining(Request $request)
    {

        $exportinactivesalesagents = array();
        $exportinactivesalesagents['export'] = 1;
        $client_id = "";
        $clients = (new Client)->getClientsListByStatus();
        $sale_centers = array();
        $salecenter_id = "";
        $location_id = "";
        $locations = array();
        $users = array();

        if (isset($request->client)) {
            $client_id = $request->client;
            if (!empty($request->salecenter)) {
                $salecenter_id = $request->salecenter;
                $locations = (new Salescenterslocations)->getLocationsInfo($client_id, $salecenter_id);
            }
            if (!empty($request->location)) {
                $location_id = $request->location;
            }
            $sale_centers = (New Salescenter)->getSalesCentersListByClientID($client_id);
            //$users =   (New user)->InactiveSalesAgentsList($client_id, $salecenter_id, $location_id );
        }

        $exportinactivesalesagents['client_id'] = $client_id;
        $exportinactivesalesagents['salecenter_id'] = $salecenter_id;
        $exportinactivesalesagents['location_id'] = $location_id;


        $params = $export_params = $this->getTrainingReportParams($request);
        $export_params['export'] = 1;


        $users = (new Salesagentdetail)->stateTraining($params);


        return view('reports.statetraining.index', compact('clients', 'client_id', 'users', 'sale_centers', 'salecenter_id', 'location_id', 'locations', 'exportinactivesalesagents', 'export_params'))
            ->with('i', ($request->input('page', 1) - 1) * 20);

    }

    /**
     * For export state results
     */
    function exportstateresults(Request $request)
    {
        $params = $this->getTrainingReportParams($request);
        $results = (new Salesagentdetail)->stateTraining($params);
        $filename = "State-Training-" . date('y-m-d');
        $results = collect($results)->map(function ($x) {
            return (array)$x;
        })->toArray();
        Excel::create($filename, function ($excel) use ($results) {

            $excel->sheet('Report', function ($sheet) use ($results) {

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
                            $sheet->setCellValue($columnname . $i, $value[$cnam]);
                            $columnname++;

                        }
                        $i++;


                    }
                }

            });
        })->download();
    }

    /**
     * This method is used for get traininng report parameter
     * This method is called from statetraining and exportstateresults method of this controller
     * @param $request
     */
    function getTrainingReportParams($request)
    {
        $params = array();
        if (isset($request->client)) {
            $params['client_id'] = $request->client;
        }
        if (isset($request->salecenter)) {
            $params['salecenter'] = $request->salecenter;
        }
        if (isset($request->location)) {
            $params['location'] = $request->location;
        }
        if (isset($request->certified)) {
            $params['certified'] = $request->certified;
        }
        if (isset($request->passed_state_test)) {
            $params['passed_state_test'] = $request->passed_state_test;
        }
        if (isset($request->state)) {
            $params['state'] = $request->state;
        }
        if (isset($request->export)) {
            $params['export'] = $request->export;
        }
        return $params;

    }

    /**
     * This method is used for download excel or csv file for sales activity
     */
    function downloadsalesactivity($params)
    {
        $results = (New Reports)->salesagentactivity($params);

        $filename = "Salesagent-Activity-" . date('y-m-d');
        $results = collect($results)->map(function ($x) {
            return (array)$x;
        })->toArray();
        Excel::create($filename, function ($excel) use ($results) {

            $excel->sheet('Report', function ($sheet) use ($results) {

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
                            $sheet->setCellValue($columnname . $i, $value[$cnam]);
                            $columnname++;

                        }
                        $i++;


                    }
                }

            });
        })->download();
    }

    /**
     * This method is used for get data of program report
     */
    function programreport(Request $request)
    {
        $client_id = "";
        if (isset($request->client) && !empty($request->client)) {
            $client_id = $request->client;
        }
        $status = "";
        if (isset($request->vendorstatus) && $request->vendorstatus != "") {
            $status = $request->vendorstatus;
        }

        $clients = (new Client)->getClientsListByStatus($status);
        $sale_centers = array();
        $results = array();
        $query_params = $export_params = $this->get_query_params($request);

        $export_params['export'] = 1;
        $results = (new Programs)->programReport($query_params);

        return view('reports.programs.index', compact('clients', 'results', 'query_params', 'export_params'));

    }

    /**
     * This method is used to export data of program report
     */
    function programexport(Request $request)
    {

        $query_params = $this->get_query_params($request);


        $results = (new Programs)->programReport($query_params);
        $filename = "Program-list-" . date('y-m-d');
        $results = collect($results)->map(function ($x) {
            return (array)$x;
        })->toArray();
        Excel::create($filename, function ($excel) use ($results) {

            $excel->sheet('Report', function ($sheet) use ($results) {

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
                            $sheet->setCellValue($columnname . $i, $value[$cnam]);
                            $columnname++;

                        }
                        $i++;


                    }
                }

            });
        })->download();


    }

    /**
     * This method is used to send email as per parameter
     * @param $toemail, $subject, $mainMessage, $filepath
     */
    public function sendFileInEmail($toemail, $subject, $mainMessage = "", $filepath = "")
    {

        $email = $toemail;
        $to = $email;
        $from = "TPV <no-reply@spark.tpv.plus>";

        $encoded_content = "";
        $from_name = "TPV";
        $from_mail = "no-reply@spark.tpv.plus";
        $headers = "From: $from";

        $semi_rand = md5(time());


        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

        //headers for attachment
        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";

        //multipart boundary
        $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
            "Content-Transfer-Encoding: 7bit\n\n" . $mainMessage . "\n\n";
        if ($filepath != "") {
            $handle = fopen($filepath, "r");  // set the file handle only for reading the file
            $content = fread($handle, filesize($filepath)); // reading the file
            fclose($handle);
            $encoded_content = chunk_split(base64_encode($content));

            $filename = $fileattname = basename($filepath);

            $message .= "--{$mime_boundary}\n" .
                "Content-Type: application/octet-stream;\n" .
                " name=\"{$fileattname}\"\n" .
                "Content-Disposition: attachment;\n" .
                " filename=\"{$fileattname}\"\n" .
                "Content-Transfer-Encoding: base64\n\n" .
                $encoded_content . "\n\n" .
                "-{$mime_boundary}-\n";

        }

        $mstatus = @mail($to, $subject, $message, $headers);
        $textEmailStatistics = new TextEmailStatistics();
        $textEmailStatistics->type = 1;
        $textEmailStatistics->save();
    }

    /**
     * This method is used to get data of critical report
     */
    public function criticalReport(Request $request)
    {
        if ($request->ajax()) {
            
            $timeZone = Auth::user()->timezone;
            $leadStatusSubQuery = "(CASE WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' WHEN telesales.status = 'self-verified' THEN 'Self verified' ELSE telesales.status END)";
//            $alertStatusSubQuery = "(CASE WHEN telesales.status = 'cancel' THEN 'Cancelled' ELSE 'Proceed' END)";
           

            $telesales = Telesales::select('telesales.*')
                ->addSelect(DB::raw($leadStatusSubQuery . " as status_new"))
                // ->addSelect('brand_contacts.name as brand')
//                ->addSelect(DB::raw($alertStatusSubQuery . " as alert_status"))
                ->with('client','user.salescenter', 'user.salesAgentDetails', 'user.salesAgentDetails.location','programs.utility.brandContacts')
                // ->leftJoin('telesales_programs','telesales_programs.telesale_id','=','telesales.id')
                // ->leftJoin('programs','programs.id','=','telesales_programs.program_id')
                // ->leftJoin('utilities','programs.utility_id','=','utilities.id')
                // ->leftJoin('brand_contacts','utilities.brand_id','=','brand_contacts.id')
                ->whereHas('criticalLogs', function ($query) {
                    $query->where('error_type', 1);
                });



            if(Auth::user()->isAccessLevelToClient()) {
                $client_id = Auth::user()->client_id;
            } else {
                $client_id = $request->client_id;
            }

            if(Auth::user()->hasAccessLevels('salescenter')) {
                $salescenter_id = Auth::user()->salescenter_id;
            } else {
                $salescenter_id = $request->salescenter_id;
            }

            if(Auth::user()->isLocationRestriction()) {
                $locationId = Auth::user()->location_id;
            } else {
                $locationId = $request->location;
            }

            if(!empty($client_id)) {
                $telesales->where('telesales.client_id',$client_id);
            }
            // if(!empty($request->brand)) {
            //     $telesales->where('brand_contacts.id',$request->brand);
            // }
            if (!empty($request->brand)) {
                $telesales->whereHas('programs.utility.brandContacts', function (Builder $query) use ($request) {
                    $query->where('id',$request->brand);
                });
            }
            if (!empty($salescenter_id)) {
                $telesales->whereHas('user', function (Builder $query) use ($salescenter_id) {
                    $query->withTrashed()->where('salescenter_id', $salescenter_id);
                });
            }

            if (auth()->user()->hasMultiLocations()) {
                $locationIds = auth()->user()->locations->pluck('id');
                $telesales->whereHas('userWithTrashed.salesAgentDetails', function (Builder $query) use ($locationIds) {
                    $query->withTrashed()->whereIn('location_id', $locationIds);
                });
            }

            if (!empty($locationId)) {
                $telesales->whereHas('userWithTrashed.salesAgentDetails', function (Builder $query) use ($locationId) {
                    $query->withTrashed()->where('location_id', $locationId);
                });
            }

            if (!empty($request->submission_date)) {
                $date = $request->submission_date;
                $start_date = Carbon::parse(explode(' - ', $date)[0],$timeZone)->setTimezone('UTC');
                $end_date = Carbon::parse(explode(' - ', $date)[1],$timeZone)->setTimezone('UTC')->addDays(1);
                $telesales->whereBetween('telesales.created_at',[$start_date,$end_date]);
            }
            if (!empty($request->verification_date)) {
                $date = $request->verification_date;
                $start_date = Carbon::parse(explode(' - ', $date)[0],$timeZone)->setTimezone('UTC');
                $end_date = Carbon::parse(explode(' - ', $date)[1],$timeZone)->setTimezone('UTC')->addDays(1);
                $telesales->whereBetween('telesales.reviewed_at',[$start_date,$end_date]);
                
            }           


            return DataTables::of($telesales)
                ->filterColumn('status_new', function($query, $keyword) use ($leadStatusSubQuery){
                    $query->whereRaw('LOWER('. $leadStatusSubQuery .') LIKE "%'.$keyword.'%"');
                    \Log::info($query->toSql()); 
                })
//                ->filterColumn('alert_status', function($query, $keyword) use ($alertStatusSubQuery){
//                    $query->whereRaw('LOWER('. $alertStatusSubQuery .') LIKE "%'.$keyword.'%"');
//                    \Log::info($query->toSql());
//                })
                ->addColumn('external_id', function($telesale){
                    $external_id = '';
                    if(!empty($telesale->user()->withTrashed())) {
                        $external_id= $telesale->user()->first()->salesAgentDetails()->first()->external_id;
                    }
                    return $external_id;
                })
                ->addColumn('alert_description', function($telesale) {
                    $description = CriticalLogsHistory::select(
                        'email_alert_message',
                        'lead_id'
                    )
                    ->where('lead_id', '=', $telesale->id)
                    ->where('error_type', '=', '1')
                    ->get();

                    $concatDescription = implode(" <br>", array_column($description->toArray(), 'email_alert_message'));
                    $viewBtn = '<button 
                        data-description="'.$concatDescription.'" 
                        data-toggle="tooltip"
                        data-placement="top" data-container="body"
                        role="button"
                         data-original-title="View Alert Description"
                         class="btn view-alert-description"
                        data-id="' . $telesale->id . '"
                        data-type="view-alert-description">' . getimage("images/view.png") . '</button>';

                    // $shortDescription = strlen($concatDescription);
                    // $shortDescription = (strlen($concatDescription) > 30) ?  (substr($concatDescription,0,25) . ' ... '. $viewBtn ) : $concatDescription ;

                    return $viewBtn;
                })
                ->addColumn('customer_name', function($telesale){ 

                    $customerName = Telesalesdata::select(
                        // 'telesale_id',
                        DB::raw("max(case when meta_key = 'first_name' then meta_value end) as F_name"),
                        DB::raw("max(case when meta_key = 'middle_initial' then meta_value end) as M_name"),
                        DB::raw("max(case when meta_key = 'last_name' then meta_value end) as L_name")
                    )
                    ->where('telesale_id', '=', $telesale->id)
                    ->get();

                    $concatCustomerName = implode(' ',$customerName->first()->toArray());
                    return ucfirst($concatCustomerName);

                })
                ->editColumn('alert_status', function($telesale) {
                    return ucfirst($telesale->alert_status);
                })
                ->addColumn('brand', function($telesale){
                    $name = '';
                    if(!empty($telesale->programs) && !empty($telesale->programs[0]->utility) && !empty($telesale->programs[0]->utility->brandContacts)) {
                        $name= $telesale->programs[0]->utility->brandContacts->name;
                    }
                    return $name;
                })
                ->editColumn('status_new', function($telesale){                    
                    return ucfirst($telesale->status_new);
                })
                ->addColumn('client_name', function($telesale){
                    $name = '';
                    if(!empty($telesale->client)) {
                        $name= $telesale->client->name;
                    }
                    return $name;
                })
                ->addColumn('salescenter_name', function($telesale){
                    $name = '';
                    if(!empty($telesale->user()->withTrashed()->first()) && !empty($telesale->user()->withTrashed()->first()->salescenter)) {
                        $name= $telesale->user()->withTrashed()->first()->salescenter->name;
                    }
                    return $name;
                })
                ->addColumn('address', function($telesale){
                    $locationName = '';
                    if(!empty($telesale->user()->withTrashed()->first()) && !empty($telesale->user()->withTrashed()->first()->salesAgentDetailsWithTrashed) && !empty($telesale->user()->withTrashed()->first()->salesAgentDetailsWithTrashed->locationWithTrashed)) {
                        $locationName = $telesale->user()->withTrashed()->first()->salesAgentDetailsWithTrashed->locationWithTrashed->name;
                    }
                    return $locationName;
                })
                ->addColumn('agent_name', function($telesale){
                    $name = '';
                    if(!empty($telesale->user()->withTrashed()->first())) {
                        $name= $telesale->user()->withTrashed()->first()->full_name;
                    }
                    return $name;
                })
                ->editColumn('created_at', function($telesale) use($timeZone){
                    $date = '';
                    if(!empty($telesale->created_at)) {
                        $date= $telesale->created_at->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat());
                    }
                    return $date;
                })
                ->editColumn('reviewed_at', function($telesale)use($timeZone){
                    $date = '';
                    
                    if(!empty($telesale->reviewed_at) && ($telesale->status != config('constants.LEAD_TYPE_CANCELED')) && ($telesale->status != config('constants.LEAD_TYPE_PENDING')) && ($telesale->verification_method != config('constants.VERIFICATION_METHOD.EMAIL')) && ($telesale->verification_method != config('constants.VERIFICATION_METHOD.TEXT'))) {
                        $date= Carbon::parse($telesale->reviewed_at)->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat());
                    }
                    return $date;
                })
                ->addColumn('action', function($telesale){
                    if(auth()->user()->can('generate-critical-alert-report')) {
                        $route = route('critical-logs.show',$telesale->id);
                        $viewBtn = '<a  data-toggle="tooltip"
                            data-placement="top"
                            data-type="view"
                            data-original-title="View"
                            data-title="View Critical Logs"
                            class="btn  theme-color"
                            href="'.$route.'"
                            >' . getimage("images/view.png") . '</a>';
                    }else{
                        $viewBtn = '<a
                        class="btn cursor-none"
                        title="View Critical Logs" href="#">' . getimage("images/view-no.png") . '</a>';
                    }
                    return '<div class="btn-group">'.$viewBtn .'<div>';
                })
                ->rawColumns(['action', 'alert_description'])
                ->make(true);
        }
        $client_id = '';
        if(Auth::user()->isAccessLevelToClient()) {
            $client_id = Auth::user()->client_id;
        }
        $clients= getAllClients();
        $salesCenters= getAllSalesCenter();
        $brands = (new Brandcontacts)->getBrandsByClient($client_id);
        return view('reports.critical_alert.index',compact('clients','salesCenters','brands'));
    }

    /**
     * This method is used to get all the detail of particular critical lead
     * @param $id
     */
    public function criticalLogsShow($id) {
        
        $telesale = Telesales::findOrFail($id);
        $form = Clientsforms::withTrashed()->find($telesale->form_id);
        $salesAgent = SalesAgentdetail::withTrashed()->leftjoin("users",'users.id','=','salesagent_detail.user_id')
        ->leftjoin('salescenters','users.salescenter_id','=','salescenters.id')
        ->leftjoin('clients','clients.id','=','users.client_id')
        ->where('user_id',$telesale->user_id)
        ->select('salesagent_detail.id as agent_id','users.first_name','users.last_name','users.email','salesagent_detail.phone_number','salesagent_detail.agent_type','salescenters.*','clients.name as client_name')
        ->first();
        $leadMedia = Leadmedia::where('telesales_id',$id)->where('type','image')->select('url')->orderBy('id','DESC')->first();
       
        //check e-signature exists or not

        if(!empty($leadMedia->url)){
            $e_signature = Storage::disk('s3')->url($leadMedia->url);
        }else{
            $e_signature = '';
        }

        $offAlerts = [];
        $clientId =  $telesale->client_id;

        if ($telesale->type == 'tele') {
            if (isOnSettings($clientId,'is_enable_alert_tele')) {                
                $offAlerts = getOffAlertsTele($clientId);
            } else {
                $offAlerts = getAlertsEvent();
            }
        } else if ($telesale->type == 'd2d'){
            if (isOnSettings($clientId,'is_enable_alert_d2d')) {                
                $offAlerts = getOffAlertsD2D($clientId);
            } else {
                $offAlerts = getAlertsEvent();
            }
        }

        $criticalLogs = CriticalLogsHistory::leftjoin('telesales','telesales.id','critical_logs_history.lead_id')
            ->leftjoin('users','users.id','=','critical_logs_history.sales_agent_id')
            ->leftjoin('users as tpv_agent','tpv_agent.id','=','critical_logs_history.tpv_agent_id')
            ->where('lead_id',$telesale->id)
            ->select('critical_logs_history.*','users.first_name','users.last_name','telesales.status','tpv_agent.first_name as tpv_agent_first_name','tpv_agent.last_name as tpv_agent_last_name', 'telesales.verification_method');
            if (!empty($offAlerts)) {
                $criticalLogs->whereNotIn('event_type',$offAlerts);
            }
            $criticalLogs = $criticalLogs->get();
            $timeZone = Auth::user()->timezone;
            foreach($criticalLogs as $k => $v)
            {
                if ($criticalLogs[$k]->tpv_agent_id != "") {
                    $criticalLogs[$k]->tpv_agent_val = $criticalLogs[$k]->tpv_agent_first_name . " " . $criticalLogs[$k]->tpv_agent_last_name;
                } else {
                    if ($criticalLogs[$k]->verification_method == config()->get('constants.IVR_INBOUND_VERIFICATION') && in_array($criticalLogs[$k]->event_type, [config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_40'), config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_41'), config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_42')])) {
                        $criticalLogs[$k]->tpv_agent_val = "IVR";
                    } else {
                        $criticalLogs[$k]->tpv_agent_val = "";
                    }
                }
                
                // $date = new \DateTime($criticalLogs[$k]->created_at, new \DateTimeZone('UTC'));
                // $date->setTimezone(new \DateTimeZone('America/New_York'));
                // $criticalLogs[$k]->formatted_created_at = $date->format('m/d/Y h:i:s A');
                $criticalLogs[$k]->formatted_created_at = $criticalLogs[$k]->created_at->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat());
                
                $leadIds = explode(",", $criticalLogs[$k]->related_lead_ids);
                
                $displayLeadArr = array_map(function($val) {
                    
                    if(Auth::user()->isAccessLevelToClient()) {
                        
                        $lead = Telesales::where('id',$val)->where('client_id',Auth::user()->client_id);
                        $salescenter_id = null;
                        if(Auth::user()->hasAccessLevels('salescenter')) {
                            $salescenter_id = Auth::user()->salescenter_id;
                        }
                        if (!empty($salescenter_id)) {
                            $lead->whereHas('user', function (Builder $query) use ($salescenter_id) {
                                $query->where('salescenter_id', $salescenter_id);
                            });
                        }
                        if (auth()->user()->hasMultiLocations()) {
                            $locationIds = auth()->user()->locations->pluck('id');
                            $lead->whereHas('user.salesAgentDetails', function (Builder $query) use ($locationIds) {
                                $query->whereIn('location_id', $locationIds);
                            });
                        }

                        if(Auth::user()->isLocationRestriction()) {
                            $locationId = Auth::user()->location_id;
                            $lead->whereHas('user.salesAgentDetails', function (Builder $query) use ($locationId) {
                                $query->where('location_id', $locationId);
                            });
                        } 

                        $result =$lead->first();
                        
                        if(!empty($result)) {
                            if($val != '')
                            {
                                return '<a href="' . route('telesales.show', $val) . '">' . $val . '</a>';
                            }
                            else
                                return  '';
                        } else {
                            if($val != '')
                            {
                                return '<a href="#" class="cursor-none">' . $val . '</a>';
                            }
                            else
                                return  '';
                            
                        }
                    } else {
                        if($val != "")
                            return '<a href="' . route('telesales.show', $val) . '">' . $val . '</a>';
                        else
                            return '';
                    }

                } , $leadIds);
                
                $criticalLogs[$k]->related_lead_ids = implode(", ", $displayLeadArr);
            }
        $telesale_id = $telesale->id;
        $programs = $telesale->programs()->withTrashed()->with('utility')->get();

        $leadDetail = array();

        if (!empty($form)) {
            $leadDetail = $form->fields()->with(['telesalesData' => function ($query) use ($telesale_id) {
                $query->where('telesale_id', $telesale_id);
            }])->get()->toArray();
        }
        
        return view('reports.critical-logs.show', compact('leadDetail', 'telesale', 'programs','salesAgent','criticalLogs','e_signature'));
    }

    /**
     * For fetch and export critical report data
     */
    public function exportCriticalReport(Request $request)
    {
        try{
            if(Auth::user()->isAccessLevelToClient()) {
                $client_id = Auth::user()->client_id;
            } else {
                $client_id = $request->client_id;
            }

            if(Auth::user()->hasAccessLevels('salescenter')) {
                $salescenter_id = Auth::user()->salescenter_id;
            } else {
                $salescenter_id = $request->salescenter_id;
            }

            if(Auth::user()->isLocationRestriction()) {
                $locationId = Auth::user()->location_id;
            } else {
                $locationId = $request->location;
            }
            $sub_start_date = $sub_end_date =$verify_start_date =$verify_end_date ='';
            if (!empty($request->submission_date)) {
                $date = $request->submission_date;
                $sub_start_date = Carbon::parse(explode(' - ', $date)[0])->toDateString();
                $sub_end_date = Carbon::parse(explode(' - ', $date)[1])->toDateString();
            }
            if (!empty($request->verification_date)) {
                $date = $request->verification_date;
                $verify_start_date = Carbon::parse(explode(' - ', $date)[0])->toDateString();
                $verify_end_date = Carbon::parse(explode(' - ', $date)[1])->toDateString();
            }

            $params = [
                'client_id' => $client_id,
                'salescenter_id' => $salescenter_id,
                'location_id' => $locationId,
                'search' => $request->filter_search,
                'sub_start_date' => $sub_start_date,
                'sub_end_date' => $sub_end_date,
                'verify_start_date' => $verify_start_date,
                'verify_end_date' => $verify_end_date,
            ];

            $data = (new Reports)->getCriticalAlertReport($params);
            $timezone = getClientSpecificTimeZone();
            if (count($data) > config()->get('constants.CRITICAL_LOG_PDF_COUNT')) {
                
                CriticalLogsZipExportJob::dispatch($data, Auth::user()->email,Auth::user()->first_name,$timezone);
                sleep(1);
                return redirect()->back()->with([
                    'success' => 'Your request has been received, you will soon receive the exported zip on your registered email address.' ])->withInput($request->all());
            } else {
                $tmpFile = $this->criticalLogsZipExportService->exportReport($data);

                header('Content-disposition: attachment; filename="Critical_alert_report_'.date('d_M_Y_H_i_A').'.zip"');
                header('Content-type: application/zip');
                readfile($tmpFile);

                unlink($tmpFile);
            }
        }catch(\Exception $e) {
            \Log::error('Error while generating critical logs report: ' . $e);
            return redirect()->back()->with([
                    'error' => $e->getMessage()]);
        }

    }

    /**
     * This method is used for get all the call history data
     */
    public function callHistoryReport(Request $request)
    {

        if ($request->ajax()) {


            $telesaleScheduleCall = TelesaleScheduleCall::
            leftjoin('telesales','telesales.id','=','telesale_schedule_call.telesale_id')
            ->select('telesale_schedule_call.*',DB::raw("(CASE WHEN schedule_status = 'task-created' THEN 'Task Created' WHEN schedule_status = 'cancelled' THEN 'Cancelled' WHEN schedule_status = 'attempted' THEN 'Attempted' WHEN schedule_status = 'pending' THEN 'Pending' WHEN schedule_status = 'skip' THEN 'Skipped' ELSE '' END) as schedule_status"),

            DB::raw("(CASE WHEN dial_status = 'completed' THEN 'Completed' WHEN dial_status = 'answered' THEN 'Answered' WHEN dial_status = 'busy' THEN 'Busy' WHEN dial_status = 'no-answer' THEN 'No Answer' WHEN dial_status = 'cancelled' THEN 'Cancelled'  WHEN dial_status = 'failed' THEN 'Failed' ELSE '-' END) as dial_status")
            ,DB::raw("(CASE WHEN call_immediately = 'yes' THEN 'Now' ELSE 'Scheduled' END) as call_immediately"),
            DB::raw("(CASE WHEN call_lang = 'en' THEN 'English' ELSE 'Spanish' END) as call_lang"),'telesales.refrence_id as reference_id')
            ->with('Telesale','Telesale.client','Telesale.user.salescenter');


            if(Auth::user()->isAccessLevelToClient()) {
                $client_id = Auth::user()->client_id;
            } else {
                $client_id = $request->client_id;
            }

            if(Auth::user()->hasAccessLevels('salescenter')) {
                $salescenter_id = Auth::user()->salescenter_id;
            } else {
                $salescenter_id = $request->salescenter_id;
            }
            if(!empty($request->refrence_id)) {
                $telesaleScheduleCall->where('telesales.refrence_id',$request->refrence_id);
            }
            if(!empty($client_id)) {
                $telesaleScheduleCall->where('telesales.client_id',$client_id);
            }
            if (!empty($salescenter_id)) {
                $telesaleScheduleCall->whereHas('Telesale.user', function (Builder $query) use ($salescenter_id) {
                    $query->where('salescenter_id', $salescenter_id);
                });
            }
            return DataTables::of($telesaleScheduleCall)

            ->addColumn('call_time', function($telesale){
                $date = '';
                if(!empty($telesale->call_time)) {
                    $date= Carbon::parse($telesale->call_time)->format('d-m-Y H:i:s');
                }
                return $date;

            })->make(true);
        }
        $clients= getAllClients();
        $salesCenters= getAllSalesCenter();

        return view('reports.call_history.index',compact('clients','salesCenters'));
    }

    /**
     * For display all sales agent trail data in report
     */
    public function showSalesAgentTrailReport(Request $request)
    {
        $clients= getAllClients();
        return view('reports.sales_agent_trail.show',compact('clients'));
    }

    /**
     * For get all sales agent activity locations
     */
    public function getSalesAgentActivityLocations(Request $request)
    {
        try {
            $timeZone =Auth::user()->timezone;
            switch ($request->selected_activity) {
                case 'last_3_hours':
                    $activity_date = now()->setTimezone($timeZone)->setTimezone('UTC')->subHours(3);
                    break;
                case 'last_6_hours':
                    $activity_date = now()->setTimezone($timeZone)->setTimezone('UTC')->subHours(6);
                    break;
                case 'yesterday':
                    $activity_date = now()->setTimezone($timeZone)->setTimezone('UTC')->subDays(1);
                    break;
                case 'custom':
                    $activity_date = Carbon::parse($request->activity_date);
                    break;
                default:
                    $activity_date = now()->setTimezone($timeZone);
                    break;
            }

            $dateTitle = $activity_date->format('l, F jS Y');
            
            $timeFormat = "g:i A";

            $locations = Salesagentlocation::where('salesagent_id',$request->agent_id);
            $markers = SalesAgentActivity::where('agent_id',$request->agent_id)->whereIn('activity_type',['clock_in','clock_out','break_in','break_out','arrival_in','arrival_out']);

            $telesales = Telesales::where('user_id',$request->agent_id);

            if ($request->selected_activity == 'last_3_hours' || $request->selected_activity == 'last_6_hours') {
                $locations->where('created_at', '>',$activity_date);
                $markers->where('created_at','>',$activity_date);
                $telesales->where('created_at','>', $activity_date);
                $dateTitle .=' (from '.$activity_date->setTimezone($timeZone)->format($timeFormat). ' to '.now()->setTimezone($timeZone)->format($timeFormat).')';

            } else if($request->selected_activity == 'custom') {
                $date = $activity_date->toDateString();
                $from = $date.' '.$request->input('activity_from', '00:00:00');
                $to = $date.' '.$request->input('activity_to', '23:59:00');

                $from = Carbon::parse($from, $timeZone)->setTimezone('UTC');
                $to = Carbon::parse($to, $timeZone)->setTimezone('UTC');

                $locations->whereBetween('created_at', [$from,$to]);
                $markers->whereBetween('created_at', [$from,$to]);
                $telesales->whereBetween('created_at', [$from,$to]);

                $dateTitle .=' (from '.$from->setTimezone($timeZone)->format($timeFormat). ' to '.$to->setTimezone($timeZone)->format($timeFormat).')';

            } else {
                $date = $activity_date->toDateString();
                $locations->whereDate('created_at', $date);
                $markers->whereDate('created_at', $date);
                $telesales->whereDate('created_at', $date);
            }

           // $polylines = $locations->get(['lat','lng'])->toArray();
            $locations = $locations->get();
            $markers = $markers->get();
            $telesales = $telesales->get();
            $data = array();
            $key = 0;
            foreach ($markers as $value) {

                if(!empty($value['start_lat']) && !empty($value['start_lng'])) {
                    $key++;
                    if ($value['activity_type'] == 'clock_out' || $value['activity_type'] == 'clock_in') {
                        $activity_type = 'clock_in';
                    } else if ($value['activity_type'] == 'break_out' || $value['activity_type'] == 'break_in') {
                        $activity_type = 'break_in';
                    } else {
                        $activity_type = 'arrival_in';
                    }
                    $data[$key]['lat'] = $value['start_lat'];
                    $data[$key]['lng'] = $value['start_lng'];
                    $data[$key]['activity_type'] = $activity_type;
                    $data[$key]['time'] = $value['in_time'];

                }

                if (($value['activity_type'] == 'clock_out' || $value['activity_type'] == 'break_out' || $value['activity_type'] == 'arrival_out') && !empty($value['end_lat'])) {
                    $key++;
                    $data[$key]['lat'] = $value['end_lat'];
                    $data[$key]['lng'] = $value['end_lng'];
                    $data[$key]['activity_type'] = $value['activity_type'];
                    $data[$key]['time'] = $value['out_time'];
                }
            }

            foreach ($telesales as $telesale) {
                if(!empty($telesale['salesagent_lat']) && !empty($telesale['salesagent_lng'])) {
                    $key++;
                    $data[$key]['lat'] = $telesale['salesagent_lat'];
                    $data[$key]['lng'] = $telesale['salesagent_lng'];
                    $data[$key]['activity_type'] = 'lead_submitted';
                    $data[$key]['time'] = $telesale['created_at'];
                }
            }
            $sortdata = collect($data)->sortBy('time')->values()->toArray();

            foreach ($sortdata as $key => $value) {
                $sortdata[$key]['time'] = Carbon::parse($value['time'])->setTimezone($timeZone)->format($timeFormat);
                $sortdata[$key]['address'] = $this->getAddressFromLatLng($value['lat'],$value['lng']);
            }

            $uniqeMarkers = $this->getMarkers($sortdata);
            $polylines = $this->getPolylineLatLng($locations,$data);
            $breaks = $this->getBreaks($markers);
            $activityTime = $this->getActivityTime($request->agent_id,$activity_date->toDateString());

            $user = User::find($request->agent_id);
            $title = '';
            if (!empty($user)) {
                $title = $user->full_name . ' ('.$user->userid.')';
            }
            $response = array(
                'status' => 'success',
                'locations' =>  $sortdata,
                'polylines' =>  $polylines,
                'break_polylines' =>  $breaks,
                'title' =>  $title,
                'activity_date' =>  $dateTitle,
                'activity_time' =>  $activityTime,
                'markers' =>  $uniqeMarkers,
            );
            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error($e);
            $response = array(
                'status' => 'error',
                'message' =>  $e->getMessage(),
            );
            return response()->json($response);
        }
    }

    /**
     * This method is used to get address from latitude and longitude
     * This method is called from getSalesAgentActivityLocations method of this controller
     * @param $lat, $lng
     */
    public function getAddressFromLatLng($lat,$lng)
    {
        try {
            $key = config()->get('constants.GOOGLE_MAP_API_KEY');
            $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&key='.$key;
            $data = file_get_contents($url);
            $address = json_decode($data,true);
            if ($address['status'] == 'OK') {
                return $address['results'][0]['formatted_address'];
            } else {
                return 'N/A';
            }
        } catch (\Exception $e) {
            \Log::error($e);
            return 'N/A';
        }
    }

    /**
     * For get breaks
     * This method is called from getSalesAgentActivityLocations method of this controller
     * @param $activities
     */
    public function getBreaks($activities)
    {
        $breaks = $activities->where('activity_type','break_out')->all();
        $data = array();
        foreach ($breaks as $key => $break) {
            if(!empty($break['start_lat']) && !empty($break['start_lng']) && !empty($break['end_lat']) && !empty($break['end_lng'])) {

                $latLng[0]['lat'] =  $break['start_lat'];
                $latLng[0]['lng'] =  $break['start_lng'];
                $latLng[1]['lat'] =  $break['end_lat'];
                $latLng[1]['lng'] =  $break['end_lng'];

                $data[] = $latLng;

            }
        }

        return $data;
    }

    /**
     * For get poly line latitude and longitude
     * This method is called from getSalesAgentActivityLocations method of this controller
     * @param $locations, $activities
     */
    public function getPolylineLatLng($locations,$activities)
    {
        try{
            $newLocations = $polylines = [];
            foreach ($locations as $key => $location) {
                $newLocations[$key]['lat'] = $location->lat;
                $newLocations[$key]['lng'] = $location->lng;
                $newLocations[$key]['time'] = $location['created_at'];
            }
            $data = collect($newLocations)->merge($activities);

            $uniqueData = $data->unique(function ($item) {
                return $item['lat'].$item['lng'];
            });

            $sortdata = $uniqueData->sortBy('time')->values()->toArray();

            foreach ($sortdata as $key => $value) {
                $polylines[$key]['lat'] = $value['lat'];
                $polylines[$key]['lng'] = $value['lng'];
            }

            return $polylines;
        }catch(\Exception $e) {
            \Log::error($e);
            return [];
        }
    }

    /**
     * This method is used to get activity time 
     * This method is called from getSalesAgentActivityLocations method of this controller
     * @param $agentId, $date
     */
    public function getActivityTime($agentId,$date)
    {
        try {
            $activity = SalesAgentActivity::select(['agent_id','in_time','out_time','total_time','activity_type'])
                ->where('agent_id',$agentId)
                ->whereDate('created_at',$date)
                ->get();
            $transit_time = $break_time = $clock_time = $total_time = $arrival_time = 0;

            $current_time = now()->setTimezone(Auth::user()->timezone);
            foreach ($activity as $key => $value) {
                if ($value->activity_type == 'clock_out') {
                    $clock_time += $value->total_time;

                } else if($value->activity_type == 'break_out') {
                    $break_time += $value->total_time;

                } else if ($value->activity_type == 'arrival_out') {
                    $arrival_time += $value->total_time;
                }

                if ($value->activity_type == 'clock_in') {
                    $clock_time += $current_time->diffInSeconds($value->in_time);
                } else if($value->activity_type == 'break_in') {
                    $break_time += $current_time->diffInSeconds($value->in_time);
                } else if ($value->activity_type == 'arrival_in') {
                    $arrival_time += $current_time->diffInSeconds($value->in_time);
                }

            }

            $transit_time = $clock_time - $break_time - $arrival_time;

            $total_time = $clock_time;
            $data = [
                'working_time' => gmdate('H:i:s', $arrival_time),
                'break_time' => gmdate('H:i:s', $break_time),
                'transit_time' => gmdate('H:i:s', $transit_time),
                'total_time' => gmdate('H:i:s', $total_time),
            ];

            return $data;
        } catch (\Exception $e) {
            Log::error($e);
            $data = [
                'working_time' => 0,
                'break_time' => 0,
                'transit_time' => 0,
                'total_time' => 0,
            ];
            return $data;
        }
    }

    /**
     * For get markers
     * This method is called from getSalesAgentActivityLocations method of this controller
     * @param $agentId, $date
     */
    public function getMarkers($activities)
    {
        try {
            $markers = [];
            $activities = collect($activities);
            $markers = collect($markers);
            foreach ($activities as $key => $value) {
                $lat = $value['lat'] ;
                $lng = $value['lng'];
                $count = $activities->where('lat',$lat)->where('lng',$lng)->count();
                if ($count > 1) {

                    $markerKey = $markers->search(function ($item, $mKey) use ($lat,$lng) {
                        return $item['lat'] == $lat && $item['lng'] == $lng;
                    });
                    
                    $time = [
                            'label' => $key+1,
                            'time' => $value['time'],
                            'activity_type' => $value['activity_type'],
                        ];

                    if ($markerKey === false) {
                        $data = $value;
                        $data['activity_type'] = 'multiple';
                        $data['activity'] = array($time);
                        $markers[$key] = $data;

                    } else {
                        $allTime = $markers[$markerKey]['activity'];
                        $allTime[] = $time;
                        $data = $markers[$markerKey];
                        $data['activity'] = $allTime;
                        $markers[$markerKey] = $data;
                    }

                } else {
                  $data = $value;
                  $data['label'] = $key+1;
                  $markers[] = $data;
                }
            }
            return $markers;
        } catch (\Exception $e) {
            Log::error($e);
            return $activities;
        }
    }
}
