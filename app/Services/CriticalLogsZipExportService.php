<?php

namespace App\Services;

use App\models\Clientsforms;
use App\models\CriticalLogsHistory;
use App\models\Salesagentdetail;
use App\models\Telesales;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Storage;
use Image;
use PDF;
use ZipArchive;
use Carbon\Carbon;
use Auth;
use App\Services\StorageService;

class CriticalLogsZipExportService
{
    
    public function __construct() {
        $this->storageService = new StorageService;
    }

    /**
     * This method is used for export zip file of all the critical logs
     * @param $data, $timeZone
     */
    public function exportReport($data,$timeZone = "") {
        // $timezone = '';
        $timezone = getClientSpecificTimeZone();
        // if($timeZone!= "")
        //     $timezone = $timeZone;
        
        $results = collect($data)->map(function ($x) use($timezone) {
            
            $x->DateOfSubmission = Carbon::parse($x->DateOfSubmission)->setTimezone($timezone)->format(getDateFormat()." ".getTimeFormat());
            if($x->DateOfTPV != "")
            $x->DateOfTPV = Carbon::parse($x->DateOfTPV)->setTimezone($timezone)->format(getDateFormat()." ".getTimeFormat());
            return (array)$x;
        })->toArray();
        
        Log::info($results);
        $filename = "CRITICAL-ALERT-REPORT-" . date('y-m-d');

        $csvExported = Excel::create($filename, function ($excel) use ($results) {
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
        })->string('csv');


        // Store Exported CSV to storage
        $awsFolderPath = config()->get('constants.aws_folder');
        $filePath = config()->get('constants.CRITICAL_PDF_UPLOAD_PATH');
        $fileName = 'critical_csv_' . time() . '.csv';

        $path = $this->storageService->uploadFileToStorage($csvExported, $awsFolderPath, $filePath, $fileName);

        $fileDetails = [];
        if ($path !== false) {
            $fileDetails[] = $path;
        }
        // Retrieve leads with critical logs and generates its pdf and store it to storage
        foreach ($data as $lead) {
            
            $pdfUploaded = $this->criticalLogsPdf($lead->LeadNumber,$timezone);
            if ($pdfUploaded !== false) {
                $fileDetails[] = $pdfUploaded;
            } else {
                continue;
            }
        }

        // Create Zip file and integrate all zips & csv into it
        $tmpFile = $this->downloadCriticalLogsZip($fileDetails);
        return $tmpFile;
    }

    /**
     * This method is used for generate pdf file for critical logs
     * @param $referenceId, $timeZone
     */
    public function criticalLogsPdf($referenceId,$timeZone='') {
        
        $telesale = Telesales::with('user', 'user.salesAgentDetails')->where('refrence_id', $referenceId)->first();
        
        $form = Clientsforms::withTrashed()->find($telesale->form_id);
        $salesAgent = SalesAgentdetail::withTrashed()->leftjoin("users",'users.id','=','salesagent_detail.user_id')
            ->leftjoin('salescenters','users.salescenter_id','=','salescenters.id')
            ->leftjoin('clients','clients.id','=','users.client_id')
            ->where('user_id',$telesale->user_id)
            ->select('salesagent_detail.id as agent_id','users.first_name','users.last_name','users.email','salesagent_detail.phone_number','salesagent_detail.agent_type','salescenters.*','clients.name as client_name')
            ->first();

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
            ->select('critical_logs_history.*','users.first_name','users.last_name','telesales.status','tpv_agent.first_name as tpv_agent_first_name','tpv_agent.last_name as tpv_agent_last_name');
            
        if (!empty($offAlerts)) {
            $criticalLogs->whereNotIn('event_type',$offAlerts);
        }
        $criticalLogs = $criticalLogs->get();

        $telesale_id = $telesale->id;
        $programs = $telesale->programs()->withTrashed()->with('utility')->get();
        $leadDetail = array();

        if (!empty($form)) {
            $leadDetail = $form->fields()->with(['telesalesData' => function ($query) use ($telesale_id) {
                $query->where('telesale_id', $telesale_id);
            }])->get()->toArray();
        }
            
        $pdf = PDF::loadView('reports/critical-logs/pdf', compact('leadDetail', 'telesale', 'programs','salesAgent','criticalLogs','timeZone'));

        $awsFolderPath = config()->get('constants.aws_folder');
        $filePath = config()->get('constants.CRITICAL_PDF_UPLOAD_PATH');
        $fileName = 'Lead_id_' . $telesale_id . '.pdf';

        $path = $this->storageService->uploadFileToStorage($pdf->output(), $awsFolderPath, $filePath, $fileName);

        return $path;
    }

    /**
     * This method is used for download zip file of critical logs
     * @param $fileDetails
     */
    public function downloadCriticalLogsZip($fileDetails) {
        # create new zip object
        $zip = new ZipArchive();

        # create a temp file & open it
        $tmpFile = tempnam(public_path('uploads/critical-logs/'), '');
        $zip->open($tmpFile, ZipArchive::CREATE);

        # loop through each file
        foreach ($fileDetails as $file) {
            # download file
            $download_file = file_get_contents(Storage::disk('s3')->url($file));

            #add it to the zip
            $zip->addFromString(basename($file), $download_file);
        }

        # close zip
        $zip->close();

        return $tmpFile;
    }
}
