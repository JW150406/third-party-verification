<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Reports\EnrollmentReportController;
use Illuminate\Database\Eloquent\Builder;
use App\models\Brandcontacts;
use App\models\TwilioLeadCallDetails;
use App\models\TwilioStatisticsCallLogs;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\models\Telesales;
use Carbon\Carbon;
use Log;
use DB;
use DataTables;
use App\models\UserTwilioId;

class BillingReportController extends Controller
{
    /**
     * This method is used for showing data in datatable or view(blade) page\
     * @param $request
     */
    public function index(Request $request)
    {
        $clientId  = '';
        $status = "";
        $date = "";
        $timeZone = Auth::user()->timezone;
        
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
        $leadDateType = ($request->has('leadDateType')) ? $request->get('leadDateType') : 
        "submission";
        $method = ($request->has('method')) ? $request->get('method') : "";
        $hidden = ($request->has('hidden')) ? $request->get('hidden') : "";

        if(Auth::user()->isAccessLevelToClient()) {
            $clientId =  auth()->user()->client_id;
        } else if (isset($request->client) && !empty($request->client)) {
            $clientId = $request->client;
        }
        if(Auth::user()->hasAccessLevels('salescenter')) {
            $salesCenter = Auth::user()->salescenter_id;
        }
        if(Auth::user()->isLocationRestriction()) {
            $location = Auth::user()->location_id;
        }
        if (auth()->user()->hasMultiLocations()) {
            $locationIds = auth()->user()->locations->pluck('id');
        }
        
        $startDate = Carbon::today()->startOfMonth();
        $endDate = Carbon::now();
        
        if (isset($request->submitDate) && !empty($request->submitDate)) {
            $date = $request->submitDate;
            $startDate = Carbon::parse(explode(' - ', $date)[0],$timeZone)->setTimezone('UTC');
            $endDate = Carbon::parse(explode(' - ', $date)[1],$timeZone)->addDays(1)->setTimezone('UTC');
        }
        $details = [];
        $details['clientId'] = $clientId;
        $details['salesCenter'] = $salesCenter;
        $details['location'] = $location;
        $details['startDate'] = $startDate;
        $details['endDate'] = $endDate;
        $details['channel'] = $channel;
        $details['status'] = $status;
        $details['leadId'] = $leadId;
        $details['leadDateType'] = $leadDateType;
        $details['brand'] = $brand;
        $details['state'] = $state;
        $details['method'] = $method;
        $details['hidden'] = $hidden;
        $billingReport = $this->getBillingReportData($details);

        if($request->ajax()) {

            return DataTables::of($billingReport)
            ->editColumn('SoldDateTime',function($billingReport) use($timeZone){  
                if($billingReport->SoldDateTime != ''){
                    return Carbon::parse($billingReport->SoldDateTime)->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat());
                }
                else{
                    return '';
                }
            })
            ->editColumn('CallDateTime',function($billingReport) use($timeZone){  
                return Carbon::parse($billingReport->CallDateTime)->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat());
            })
            ->editColumn('WorkerCallId',function($billingReport) use($timeZone){  
                $callId = $billingReport->WorkerCallId;
                 if($billingReport->WorkerCallId == ''){
                    $callId = $billingReport->CallId;
                }
                return $callId;
            })   
            ->editColumn('Brand',function($billingReport) use($timeZone){  
                return explode(', ',$billingReport->Brand)[0];
            })           
            ->editColumn('TPVDate', function($billingReport)use($timeZone){
                $date = '';
                if(!empty($billingReport->TPVDate) && ($billingReport->Status != config('constants.VERIFICATION_STATUS_CHART.'.ucfirst('cancel'))) && ($billingReport->Status != config('constants.VERIFICATION_STATUS_CHART.'.ucfirst('pending'))) && (($billingReport->Method != 'Self Verification (SMS)') || ($billingReport->Method != 'Self Verification (Email)'))) {
                    $date= Carbon::parse($billingReport->TPVDate)->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat());
                }
                return $date;
            })
            // ->editColumn('TPVAgent', function($billingReport)use($timeZone){
            //     // $name = '';
            //     // if(!empty($billingReport->TPVAgent) && ($billingReport->Status != config('constants.VERIFICATION_STATUS_CHART.'.ucfirst('cancel'))) && ($billingReport->Status != config('constants.VERIFICATION_STATUS_CHART.'.ucfirst('pending'))) && (($billingReport->Method != 'Self Verification (SMS)') || ($billingReport->Method != 'Self Verification (Email)'))) {
            //         $name= $billingReport->TPVAgent;
            //     // }
            //     return $name;
            // })
            ->editColumn('CallDuration', function($billingReport)use($timeZone){
                if(($billingReport->Method != 'Self Verification (SMS)') || ($billingReport->Method != 'Self Verification (Email)')){
                    return $billingReport->CallDuration;
                }
            })
            ->addColumn('RecordingUrl', function($billingReport){
                $viewBtn = '';
                if(($billingReport->Method != 'Self Verification (SMS)') || ($billingReport->Method != 'Self Verification (Email)')){
                    if (!empty($billingReport->RecordingUrl)){
                        $viewBtn = '<a href="'. Storage::disk('s3')->url($billingReport->RecordingUrl) .'" target="_blank" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Play Recording" class="btn purple">'.getimage("images/play.png").'</a>';
                    }
                }
                return '<div class="btn-group">'.$viewBtn.'<div>';
            })
            ->rawColumns(['RecordingUrl'])
            ->make(true);
        }

        $clients = getAllClients();
        $brands = (new Brandcontacts)->getBrandsByClient($clientId);
        return view('reports.billing_report.index',compact('clients', 'salesCenter','brands'));
    }

    /**
     * This method is used for export data in csv or xlsx file
     * @param $request
     */
    public function exportBillingReport(Request $request)
    {
        $exportType = $request->get('export');
        $clientId  = '';
        $status = "";
        $date = "";
        $timeZone = Auth::user()->timezone;
        
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
        $leadDateType = ($request->has('leadDateType')) ? $request->get('leadDateType') : 
        "submission";
        $method = ($request->has('verification_method')) ? $request->get('verification_method') : "";
        $hidden = ($request->has('hidden')) ? $request->get('hidden') : "";

        if(Auth::user()->isAccessLevelToClient()) {
            $clientId =  auth()->user()->client_id;
        } else if (isset($request->client) && !empty($request->client)) {
            $clientId = $request->client;
        }
        
        if(Auth::user()->hasAccessLevels('salescenter')) {
            $salesCenter = Auth::user()->salescenter_id;
        }
        if(Auth::user()->isLocationRestriction()) {
            $location = Auth::user()->location_id;
        }
        if (auth()->user()->hasMultiLocations()) {
            $locationIds = auth()->user()->locations->pluck('id');
        }
        
        $startDate = Carbon::today()->startOfMonth();
        $endDate = Carbon::now();
        
        if (isset($request->date_start) && !empty($request->date_start)) {
            $date = $request->date_start;
            $startDate = Carbon::parse(explode(' - ', $date)[0],$timeZone)->setTimezone('UTC');
            $endDate = Carbon::parse(explode(' - ', $date)[1],$timeZone)->addDays(1)->setTimezone('UTC');
        }

        $results = null;
        $details = [];
        $details['clientId'] = $clientId;
        $details['salesCenter'] = $salesCenter;
        $details['location'] = $location;
        $details['startDate'] = $startDate;
        $details['endDate'] = $endDate;
        $details['channel'] = $channel;
        $details['status'] = $status;
        $details['leadId'] = $leadId;
        $details['leadDateType'] = $leadDateType;
        $details['brand'] = $brand;
        $details['state'] = $state;
        $details['method'] = $method;
        $details['hidden'] = $hidden;
        $results = $this->getBillingReportData($details);
        $results = $results->get()->toArray();
        
        $results = collect($results)->map(function ($x) use ($timeZone) {
            if($x['WorkerCallId'] == ''){
                $x['WorkerCallId']= $x['CallId'];
            }
            unset($x['workerId']);
            if($x['SoldDateTime'] != ''){
                $x['SoldDateTime'] = Carbon::parse($x['SoldDateTime'])->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat());
            }
            $x['CallDateTime'] = Carbon::parse($x['CallDateTime'])->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat());
            $x['Brand'] = explode(', ',$x['Brand'])[0];
            if($x['TPVDate'] != ''){
                $x['TPVDate'] = Carbon::parse($x['TPVDate'])->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat());
            }
            // if(!empty($x['TPVDate']) && ($x['Status'] != config('constants.VERIFICATION_STATUS_CHART.'.ucfirst('cancel'))) && ($x['Status'] != config('constants.VERIFICATION_STATUS_CHART.'.ucfirst('pending'))) && (($x['Method'] != 'Self Verification (SMS)') || ($x['Method'] != 'Self Verification (Email)'))) {
            //     $x['TPVDate'] = Carbon::parse($x['TPVDate'])->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat());
            // }
            // if(!empty($x['TPVAgent']) && ($x['Status'] != config('constants.VERIFICATION_STATUS_CHART.'.ucfirst('cancel'))) && ($x['Status'] != config('constants.VERIFICATION_STATUS_CHART.'.ucfirst('pending'))) && (($x['Method'] != 'Self Verification (SMS)') || ($x['Method'] != 'Self Verification (Email)'))) {
            // }
            // else{
            //     $x['TPVDate'] =  '';
            // }
            if($x['RecordingUrl'] == null || ($x['Method'] == 'Self Verification (SMS)') || ($x['Method'] == 'Self Verification 
            (Email)')){
                $x['RecordingUrl'] = '';
            }
            else{
                $x['RecordingUrl'] = Storage::disk('s3')->url($x['RecordingUrl']);
            }
            if(($x['Method'] == 'Self Verification (SMS)') || ($x['Method'] == 'Self Verification (Email)')){
                $x['CallDuration'] = '';
            }
            unset($x['CallId']);
            return (array)$x;
        })->toArray();
        // dd($results);
        $filename = "Billing-Duration-Report-".date('y-m-d');
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
                                $sheet->setCellValueExplicit($columnname . $i, strval($value[$cnam]), \PHPExcel_Cell_DataType::TYPE_STRING);
                                // $sheet->setCellValue($columnname . $i, $value[$cnam]);
                                $columnname++;
                            }
                            $i++;


                        }
                    }

                });
            })->download($exportType);
    }

    /**
     * Query for get billing report data from database as per requirements with data filters
     * This method is called from index and exportBillingReport method of this controller
     * 
     * @param $data
     */
    public function getBillingReportData($data)
    {
        $enrollment = TwilioLeadCallDetails::
        leftJoin('telesales','telesales.id','=','twilio_lead_call_details.lead_id')
        ->leftJoin('users','users.id','=','telesales.user_id')
        ->leftJoin('clients','clients.id','=','twilio_lead_call_details.client_id')
        ->leftJoin('salescenters','users.salescenter_id','=','salescenters.id')
        ->leftJoin('salesagent_detail','users.id','=','salesagent_detail.user_id')
        ->leftJoin('salescenterslocations','salescenterslocations.id','=','users.location_id')
        ->leftjoin('telesales_zipcodes','telesales_zipcodes.telesale_id','=','telesales.id')
        ->leftjoin('zip_codes','zip_codes.id','=','telesales_zipcodes.zipcode_id')
        // ->leftJoin('user_twilio_id','user_twilio_id.twilio_id','=','twilio_lead_call_details.worker_id')
        // ->leftJoin('users as tpvagents','tpvagents.id','=','user_twilio_id.user_id')
        ->select(
            'clients.name as Client',
            DB::raw("( select GROUP_CONCAT(name ,', ') from brand_contacts where id IN(select brand_id from utilities where id IN (select utility_id from programs where id IN (select program_id from telesales_programs where telesale_id = telesales.id))) ) as Brand")
            ,'salescenters.name as SalesCenter','salescenterslocations.name as SalesCenterLocation',
            DB::raw("concat( users.first_name, ' ',users.last_name ) as SalesAgent"),
            'users.userid as Sales Agent ID',DB::raw("CASE
            WHEN  salesagent_detail.agent_type = 'd2d' THEN 'Door-to-Door'
            WHEN  salesagent_detail.agent_type = 'tele' THEN 'Telemarketing'
            ELSE '' 
            END as 'Channel'"),'telesales.created_at as SoldDateTime','twilio_lead_call_details.created_at as CallDateTime','telesales.reviewed_at as TPVDate','telesales.refrence_id as LeadID','zip_codes.state as State',DB::raw("(CASE WHEN twilio_lead_call_details.lead_status = 'verified' THEN 'Verified' WHEN twilio_lead_call_details.lead_status = 'cancel' THEN 'Cancelled' WHEN twilio_lead_call_details.lead_status = 'decline' THEN 'Declined' WHEN twilio_lead_call_details.lead_status = 'hangup' THEN 'Disconnected' WHEN twilio_lead_call_details.lead_status = 'expired' THEN 'Expired' WHEN 
            twilio_lead_call_details.lead_status = 'self-verified' THEN 'Self Verified' WHEN twilio_lead_call_details.lead_status = 'pending' THEN 'Pending' ELSE '' END) as Status"),
            DB::raw("CASE
            WHEN  twilio_lead_call_details.call_type = '1' THEN 'Customer Inbound'
            WHEN  twilio_lead_call_details.call_type = '2' THEN 'Agent Inbound'
            WHEN  twilio_lead_call_details.call_type = '3' THEN 'Self Verification(Email)'
            WHEN  twilio_lead_call_details.call_type = '4' THEN 'Self Verification(SMS)'
            WHEN  twilio_lead_call_details.call_type = '5' THEN 'IVR Inbound'
            WHEN  twilio_lead_call_details.call_type = '6' THEN 'TPV Now Outbound'
            ELSE '' 
            END as 'Method'"),
            
            DB::raw("(SELECT CONCAT(first_name, ' ', last_name) FROM users
                WHERE id = (SELECT user_id FROM user_twilio_id WHERE twilio_id = `twilio_lead_call_details`.`worker_id` LIMIT 1))
            as TPVAgent"),
            'twilio_lead_call_details.worker_id as workerId',
            'twilio_lead_call_details.worker_call_id as WorkerCallId','twilio_lead_call_details.call_id as CallId','twilio_lead_call_details.call_duration as CallDuration','recording_url as RecordingUrl');
        
            

        if($data['leadDateType'] == 'submission')
            $enrollment = $enrollment->whereBetween('twilio_lead_call_details.created_at',[$data['startDate'],$data['endDate']]);
        elseif($data['leadDateType'] == 'verification')
        {
            $enrollment = $enrollment->whereBetween('telesales.reviewed_at',$data['startDate'],$data['endDate'])
            ->whereNotIn('telesales.status',['cancel','pending'])->whereIn('verification_method',['1','2','6']);
        }
        if($data['clientId'] != '')
            $enrollment = $enrollment->where('twilio_lead_call_details.client_id',$data['clientId']);
        if($data['salesCenter'] != '')
            $enrollment = $enrollment->where('salescenters.id',$data['salesCenter']);
        
        if($data['location'] != '')
        {
            $enrollment = $enrollment->where('salescenterslocations.id',$data['location']);
        }
        if($data['channel'] != '')
            $enrollment = $enrollment->where('salesagent_detail.agent_type',$data['channel']);
        if($data['status'] != '')
            $enrollment = $enrollment->where('twilio_lead_call_details.lead_status',$data['status']);
        if($data['leadId'] != '')
            $enrollment = $enrollment->where('telesales.refrence_id',$data['leadId']);
        if($data['state'] != '')
            $enrollment = $enrollment->where('zip_codes.state',$data['state']);
        if($data['method'] != '')
            $enrollment = $enrollment->where('twilio_lead_call_details.call_type',$data['method']);
        
        if ($data['brand'] != '') {
             $enrollment = $enrollment->whereHas('telesales.programs.utility.brandContacts', function (Builder $query) use ($data) {
                $query->where('id',$data['brand']);
            });
        }
        if ($data['hidden'] != '') {
            $enrollment = $enrollment->where('twilio_lead_call_details.call_duration','>=',10);
        }

        return $enrollment;
    }
}
