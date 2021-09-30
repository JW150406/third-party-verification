<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\TwilioLeadCallDetails;
use Auth;
use Carbon\Carbon;
use DB;
use DataTables;
use Excel;

class CallDetailsReportController extends Controller
{
    /**
     * This method is used for showing data in datatable or view(blade) page
     * @param $request
     */
    public function index(Request $request)
    {
        $clientId  = '';
        $status = "";
        $date = "";
       // $timeZone = Auth::user()->timezone;
        $timeZone = getClientSpecificTimeZone();
        
        $clientId = ($request->has('client')) ? $request->get('client') : "";
                
        if(Auth::user()->isAccessLevelToClient()) {
            $clientId =  auth()->user()->client_id;
        } else if (isset($request->client) && !empty($request->client)) {
            $clientId = $request->client;
        }
        
        
        $startDate = Carbon::today($timeZone)->startOfMonth();
        $endDate = Carbon::now($timeZone);
     
        
        if (isset($request->submitDate) && !empty($request->submitDate)) {
            $date = $request->submitDate;
            $startDate = Carbon::parse(explode(' - ', $date)[0],$timeZone)->setTimezone('UTC');
            $endDate = Carbon::parse(explode(' - ', $date)[1],$timeZone)->addDays(1)->setTimezone('UTC');
            // $startDate = Carbon::parse(explode(' - ', $date)[0],$timeZone);
            // $endDate = Carbon::parse(explode(' - ', $date)[1],$timeZone)->addDays(1);
        }
   
        $details = [];
        $details['clientId'] = $clientId;   
        $details['startDate'] = $startDate;
        $details['endDate'] = $endDate;

        $callDetailsReport = $this->getCallDetailsReportData($details);
        // return $callDetailsReport->get();
        // return task_completed_time
        if($request->ajax()) {

            return DataTables::of($callDetailsReport)
            ->editColumn('created_date',function($callDetailsReport) use ($timeZone){
                return Carbon::parse($callDetailsReport->created_date)->format('d-M-y');
            })           
            ->addColumn('average_call_per_hour',function($callDetailsReport){
                $avg = ( $callDetailsReport->a9AM + $callDetailsReport->a10AM + $callDetailsReport->a11AM + $callDetailsReport->a12PM + $callDetailsReport->a1PM + $callDetailsReport->a2PM + $callDetailsReport->a3PM + $callDetailsReport->a4PM + $callDetailsReport->a5PM + $callDetailsReport->a6PM + $callDetailsReport->a7PM + $callDetailsReport->a8PM + $callDetailsReport->a9PM + $callDetailsReport->a10PM + $callDetailsReport->a11PM) / 15;

                return number_format($avg,2);
            })
            ->make(true);
        }

        $clients = getAllClients();        
        return view('reports.call_report.index',compact('clients'));

    }
    
    /**
     * This method is used for export data in csv or xlsx file
     * @param $request
     */
    public function exportCallDetailsReport(Request $request)
    {
        $exportType = $request->get('export');
        $clientId  = '';    
       // $timeZone = Auth::user()->timezone;
       $timeZone = getClientSpecificTimeZone();

        $clientId = ($request->has('client')) ? $request->get('client') : "";
        
        if(Auth::user()->isAccessLevelToClient()) {
            $clientId =  auth()->user()->client_id;
        } else if (isset($request->client) && !empty($request->client)) {
            $clientId = $request->client;
        }                
        
        $startDate = Carbon::today($timeZone)->startOfMonth();
        $endDate = Carbon::now($timeZone);
        
        if (isset($request->date_start) && !empty($request->date_start)) {
            $date = $request->date_start;
            $startDate = Carbon::parse(explode(' - ', $date)[0],$timeZone)->setTimezone('UTC');
            $endDate = Carbon::parse(explode(' - ', $date)[1],$timeZone)->addDays(1)->setTimezone('UTC');

            // $startDate = Carbon::parse(explode(' - ', $date)[0],$timeZone);
            // $endDate = Carbon::parse(explode(' - ', $date)[1],$timeZone)->addDays(1);
        }

        $results = null;
        $details = [];
        $details['clientId'] = $clientId;        
        $details['startDate'] = $startDate;
        $details['endDate'] = $endDate;
        
        $results = $this->getCallDetailsReportData($details);
       
        $results = $results->get()->toArray();
        if(!empty($results)){
            foreach ($results as $k => $v) {
                $v['Date'] = Carbon::parse($v['created_date'])->format('d-M-y');
                // $v['8am']= $v['a8AM'];
                $v['9am']= $v['a9AM'];
                $v['10am']= $v['a10AM'];
                $v['11am']= $v['a11AM'];
                $v['12pm']= $v['a12PM'];
                $v['1pm']= $v['a1PM'];
                $v['2pm']= $v['a2PM'];
                $v['3pm']= $v['a3PM'];
                $v['4pm']= $v['a4PM'];
                $v['5pm']= $v['a5PM'];
                $v['6pm']= $v['a6PM'];
                $v['7pm']= $v['a7PM'];
                $v['8pm']= $v['a8PM'];
                $v['9pm']= $v['a9PM'];
                $v['10pm']= $v['a10PM'];
                $v['11pm']= $v['a11PM'];
               $v['Total Calls']=$v['totalCalls'];
                
                unset($v['a8to12PM']);
                unset($v['a1to10AM']);
                // unset($v['a8AM']);
                unset($v['a9AM']);
                unset($v['a10AM']);
                unset($v['a11AM']);
                unset($v['a12PM']);
                unset($v['a1PM']);
                unset($v['a2PM']);
                unset($v['a3PM']);
                unset($v['a4PM']);
                unset($v['a5PM']);
                unset($v['a6PM']);
                unset($v['a7PM']);
                unset($v['a8PM']);
                unset($v['a9PM']);
                unset($v['a10PM']);
                unset($v['a11PM']);
                unset($v['created_date']);
                unset($v['year']);
                unset($v['month']);
                unset($v['day']);
                unset($v['totalCalls']);
                $results[$k] = $v;
            }   
        }
       
        $filename = "Call-Details-Report-".date('y-m-d');
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

                            $sheet->cell($column_name . '1', 'Average Calls Per Hour', function ($cell, $cellvalue) {
                                $cell->setValue($cellvalue);
                            });
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
                            $avg = 0;
                            foreach ($value as $cnam => $cval) {
                                
                                $sheet->setCellValue($columnname . $i, $value[$cnam]);
                                
                                if($columnname != 'A' && $columnname != 'Q'){
                                    $avg = $avg + $value[$cnam];
                                }
                                
                                $columnname++;                                
                            }
                            $sheet->setCellValue($columnname . $i, number_format($avg/15,2));
                            $i++;
                        }
                        
                        $lastRow = $sheet->getHighestDataRow();
                        
                        $sheet->setCellValue('A'. $i, 'Average Calls Per Day');
                        $sheet->setCellValue('B'. $i, '=ROUND(AVERAGEA(B2:B'.$lastRow.'),2)');
                        $sheet->setCellValue('C'. $i, '=ROUND(AVERAGEA(C2:C'.$lastRow.'),2)');
                        $sheet->setCellValue('D'. $i, '=ROUND(AVERAGEA(D2:D'.$lastRow.'),2)');
                        $sheet->setCellValue('E'. $i, '=ROUND(AVERAGEA(E2:E'.$lastRow.'),2)');
                        $sheet->setCellValue('F'. $i, '=ROUND(AVERAGEA(F2:F'.$lastRow.'),2)');
                        $sheet->setCellValue('G'. $i, '=ROUND(AVERAGEA(G2:G'.$lastRow.'),2)');
                        $sheet->setCellValue('H'. $i, '=ROUND(AVERAGEA(H2:H'.$lastRow.'),2)');
                        $sheet->setCellValue('I'. $i, '=ROUND(AVERAGEA(I2:I'.$lastRow.'),2)');
                        $sheet->setCellValue('J'. $i, '=ROUND(AVERAGEA(J2:J'.$lastRow.'),2)');
                        $sheet->setCellValue('K'. $i, '=ROUND(AVERAGEA(K2:K'.$lastRow.'),2)');
                        $sheet->setCellValue('L'. $i, '=ROUND(AVERAGEA(L2:L'.$lastRow.'),2)');
                        $sheet->setCellValue('M'. $i, '=ROUND(AVERAGEA(M2:M'.$lastRow.'),2)');
                        $sheet->setCellValue('N'. $i, '=ROUND(AVERAGEA(N2:N'.$lastRow.'),2)');
                        $sheet->setCellValue('O'. $i, '=ROUND(AVERAGEA(O2:O'.$lastRow.'),2)');
                        $sheet->setCellValue('P'. $i, '=ROUND(AVERAGEA(P2:P'.$lastRow.'),2)');
                        $sheet->setCellValue('Q'. $i, '=ROUND(AVERAGEA(Q2:Q'.$lastRow.'),2)');
                        $sheet->setCellValue('R'. $i, '=ROUND(AVERAGEA(R2:R'.$lastRow.'),2)');
                        // $sheet->setCellValue('S'. $i, '=ROUND(AVERAGEA(S2:S'.$lastRow.'),1)');
                    }

                    

                });
            })->download($exportType);
    }
    
    /**
     * Query for get call details report data from database as per requirements
     * This method is called from index and exportCallDetailsReport method of this controller
     * 
     * @param $details
     */
    public function getCallDetailsReportData($details)
    {
        $UTC = config('constants.UTC_TIME');
        $date = date_create(Carbon::now(), timezone_open(getClientSpecificTimeZone()));
        $toronto_time = date_format($date, 'P');
        
        $callDetails = TwilioLeadCallDetails::select(                             
            DB::raw('DATE(twilio_lead_call_details.created_at) as created_date'),                       
            // DB::raw("count(case when HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) >= 8 AND HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) < 9 then 1 end) as a8AM"),
            DB::raw("count(case when HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) >= 9 AND HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) < 10 then 1 end) as a9AM"),
            DB::raw("count(case when HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) >= 10 AND HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) < 11 then 1 end) as a10AM"),
            DB::raw("count(case when HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) >= 11 AND HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) < 12 then 1 end) as a11AM"),
            DB::raw("count(case when HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) >= 12 AND HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) < 13 then 1 end) as a12PM"),
            DB::raw("count(case when HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) >= 13 AND HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) < 14 then 1 end) as a1PM"),
            DB::raw("count(case when HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) >= 14 AND HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) < 15 then 1 end) as a2PM"),
            DB::raw("count(case when HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) >= 15 AND HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) < 16 then 1 end) as a3PM"),
            DB::raw("count(case when HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) >= 16 AND HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) < 17 then 1 end) as a4PM"),
            DB::raw("count(case when HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) >= 17 AND HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) < 18 then 1 end) as a5PM"),
            DB::raw("count(case when HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) >= 18 AND HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) < 19 then 1 end) as a6PM"),
            DB::raw("count(case when HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) >= 19 AND HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) < 20 then 1 end) as a7PM"),
            DB::raw("count(case when HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) >= 20 AND HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) < 21 then 1 end) as a8PM"),
            DB::raw("count(case when HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) >= 21 AND HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) < 22 then 1 end) as a9PM"),
            DB::raw("count(case when HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) >= 22 AND HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) < 23 then 1 end) as a10PM"),
            DB::raw("count(case when HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) >= 23 AND HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) < 24 then 1 end) as a11PM"),
            DB::raw("count(case when HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) >= 20 AND HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) < 24 then 1 end) as a8to12PM"),
            DB::raw("count(case when HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) >= 0 AND HOUR(CONVERT_TZ(twilio_lead_call_details.created_at,'$UTC','$toronto_time')) < 10 then 1 end) as a1to10AM"),
            DB::raw("count(twilio_lead_call_details.created_at) as totalCalls"),
            DB::raw('date_format(CONVERT_TZ(twilio_lead_call_details.created_at,"+00:00","'.$toronto_time.'"),"%m") as month,YEAR(twilio_lead_call_details.created_at) as year'),DB::raw("(date_format(CONVERT_TZ(twilio_lead_call_details.created_at,'+00:00','".$toronto_time."'),'%d'))as day")
                        )
                        // ->leftJoin('twilio_worker_reservation_details',function($join){
                        //     $join->on('twilio_worker_reservation_details.worker_id','=','twilio_lead_call_details.worker_id');
                        //     $join->on('twilio_worker_reservation_details.task_id','=','twilio_lead_call_details.task_id');
                        // })
                        // ->whereNotNull('twilio_lead_call_details.worker_id')
                        // ->where('twilio_worker_reservation_details.reservation_status','accepted')
                        ->whereBetween('twilio_lead_call_details.created_at', [$details['startDate'], $details['endDate']]);
        if(isset($details['clientId']) && $details['clientId'] != ""){
            $callDetails->where('twilio_lead_call_details.client_id', $details['clientId']);
        }
        $callDetails->groupBy('day','month','year');
        // ->having('created_date','>=',$details['startDate'])
        // ->having('created_date','<=',$details['endDate']);
                // dd($callDetails->get()->toArray());
          
        return $callDetails;
    }

    
}
