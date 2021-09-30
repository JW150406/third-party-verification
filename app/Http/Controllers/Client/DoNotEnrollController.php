<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\models\DoNotEnroll;
use Illuminate\Http\Request;
use DataTables;
use App\models\Client;
use Log;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use Illuminate\Validation\Rule;
use Validator;


class DoNotEnrollController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id,Request $request)
    {
        try {
            
            // For fetch client wise account number 
            $getDetails = DoNotEnroll::where('client_id', $id);
        
            if ($request->ajax()) {
                return DataTables::of($getDetails)
                    ->editColumn('created_at', function ($getDetails){
                        return $getDetails->created_at ? $getDetails->created_at->format(getDateFormat().' '.getTimeFormat()) : '';
                    })
                    ->addColumn('action', function($getDetails){
                        $class = 'delete-do-not-enroll-data';
                        $attributes = [
                            "data-original-title" => "Delete",
                            "data-did" => $getDetails->id,
                            "id" => "do-not-enroll-{$getDetails->id}"
                        ];
                        $deleteBtn = getDeleteBtn($class, $attributes);

                        return '<div class="btn-group">'.$deleteBtn.'<div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }

        } catch (\Exception $e) {
            Log::error('DoNotEnrollController - index : Something went wrong, please try again.');
            Log::error($e);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $clientId = $request->client_id;
            $accountNumbers = array_filter($request->account_number);

            $validator = Validator::make(['account_number'=> $accountNumbers], [
                "account_number"    => "required|array|min:1",
                "account_number.*"  => "string|distinct|unique:do_not_enrolls,account_number,NULL,id,client_id,".$clientId,
            ],[
                "account_number.*.distinct" => "This account number field has a duplicate value.",
                "account_number.*.unique" => "This account number is taken.",
            ]);
            if ($validator->fails()) {
                return response()->json([ 'status' => 'validation_error',  'message'=>$validator->errors()]);
            }
            
            foreach ($accountNumbers as $accountNumber) {
                $details = new DoNotEnroll;
                $details->client_id = $request->client_id;
                $details->account_number = $accountNumber;
                $details->save();
            }
            Log::info("Account number successfully created.");
            return response()->json([ 'status' => 'success',  'message'=>'Account number successfully created.']);
        } catch (\Exception $e) {
            Log::error("Getting error while storing do not enroll list: ".$e);
            return response()->json([ 'status' => 'error',  'message'=>'Something went wrong, please try again.']);
        }
    }    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\models\DoNotEnroll  $doNotEnroll
     * @return \Illuminate\Http\Response
     */
    public function destroy(DoNotEnroll $doNotEnroll, Request $request)
    {
        try {
            // Fetch id and delete from the db
            DoNotEnroll::where('id', $request->doNotEnrollId)->delete();
            Log::info("Account number successfully deleted");
            return response()->json([ 'status' => 'success',  'message'=>'Account number successfully deleted.']);

        } catch (\Exception $e) {
            Log::error('DoNotEnrollController - destroy : Something went wrong, please try again.');
            Log::error($e);
            return response()->json(['status' => 'success',  'message'=> 'Something went wrong, please try again.']);
        }
        
    }

    /**
     * For return view page of bulk upload  
     */
    public function DoNotEnrollBulkUpload($clientId)
    {
        $client = Client::active()->findOrFail($clientId);
        return view('client.do-not-enroll.bulkupload',compact('client'));
    }

    /**
     * For download sample sheet for bulk upload Do Not Enroll
     * 
     */
    public function downloadDoNotEnrollSampleSheet(Request $request) {
        $data =[[
            "Account Number" => "123456789012"
        ],[
            "Account Number" => "123456789013"
        ]];
        
        Log::info("Create sample sheet with above dummy data for Do Not Enroll bulk upload option.");

        return Excel::create('donotenroll_sample', function($excel) use ($data) {
            $excel->sheet('sheet1', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download("xlsx");
    }

    /**
     * For validate and store imported sheet data in database
     * 
     * @param $request  
     */
    public function saveDNEBulkUpload(Request $request)
    {
        
        // For check extension of uploaded file is correct or not.
        $validator = \Validator::make(
            [
                'upload_file' => $request->hasFile('upload_file')? strtolower($request->file('upload_file')->getClientOriginalExtension()) : null,
            ],
            [
                'upload_file'      => 'required|in:csv,xlsx,xls',
            ]
        );
        //if validation fails then it return with errors.
        if ($validator->fails()) {
            Log::info("Uploaded file is in wrong extension.");
            return response()->json(['status' => 'error',  'errors' => $validator->errors()->messages()], 422);
        }
        Log::info("Uploaded file is in correct extension.");

        try {

            Log::info("Load Excel from it's real path.");
            $path = $request->file('upload_file')->getRealPath();
            
            $data = Excel::load($path, function($reader) {
                $columns= [
                        "account_number"
                    ];
                $reader->select($columns);
            })
            ->ignoreEmpty()
            ->get()
            ->toArray();

            
            $errors = $valid_data = array();
            if (empty($data)) {
                $errors[1][] = 'The file is empty or invalid data.';
                Log::info("The file is empty or invalid data.");
                $view = view('client.errors.file-errors', compact('errors'))->render();
                return response()->json(['status' => 'dataErrors',  'errors' => $view], 422);
            }

            $accountNumbers = array_column($data, 'account_number');
            $duplicateAccounts = array();
            foreach($accountNumbers as $number){
                if(count(array_keys($accountNumbers, $number)) > 1){
                    $duplicateAccounts[] = $number;
                }
            }
            if (!empty($duplicateAccounts)) {
                $errors[1][] = $log = 'Duplicate error :- Please use an account number only once, Following account(s) are used more than one times : '.implode(', ', array_unique($duplicateAccounts));
                Log::info($log);
                $view = view('client.errors.file-errors', compact('errors'))->render();
                return response()->json(['status' => 'dataErrors',  'errors' => $view], 422);
            }

            $checkClientWiseEntry = DoNotEnroll::where('client_id',$request->client_id)->pluck('account_number')->toArray();
            Log::info("For fetch and validate sheet data.");
            foreach ($data as $key => $agent_data) {
                // Data validation
                $dataValidator = Validator::make($agent_data,
                    [
                        'account_number' => 'required|max:255'
                    ]
                );
                if ($dataValidator->fails()) {
                    foreach ($dataValidator->messages()->all() as  $value) {
                        $errors[$key + 1][] = $value;
                    }
                }
                //FOr check account number unique validation
                else if(isset($checkClientWiseEntry) && count($checkClientWiseEntry) > 0 && in_array($agent_data['account_number'],$checkClientWiseEntry)){
                    $errors[$key + 1][] = 'This Account number already exists.';
                    
                }
                else {
                    $valid_data[$key]['client_id'] = $request->client_id;
                    $valid_data[$key]['account_number'] = $agent_data['account_number'];
                }
                
            }
            
            
            if (!empty($errors)) {
                Log::info("Bulk Upload is failed,");
                Log::info($errors);
                $view = view('client.errors.file-errors', compact('errors'))->render();
                return response()->json(['status' => 'dataErrors',  'errors' => $view], 422);
            } else {

                Log::info("For get valid data and store in the database.");
                foreach ($valid_data as $key => $value) {

                    $data['client_id'] = $value['client_id'];
                    $data['account_number'] = $value['account_number'];                    
                    // Store data in salesagent_details table
                    DoNotEnroll::create($data);
                }
                
                // Redirect to the sales agent listing page
                $url = \URL::route('do-not-enroll.bulkupload',['client_id' => $request->client_id]);
                // session()->put('success', 'Sales agents successfully imported.');
                Log::info("Do Not Enroll successfully imported.");
                return response()->json(['status' => 'success',  'message' =>'Do Not Enroll List successfully imported.', 'url' =>$url], 200);
            }
        } catch(\Exception $e) {
            Log::error($e);
            return response()->json(['status' => 'error',  'message' => $e->getMessage()], 500);
        }
    }

    //This method is used to export DNE account number list client wise
    public function exportDNEList(Request $request)
    {
        try {
            
            $client = Client::findOrFail($request->client_id);

            $clientName = str_replace(" ", "-", array_get($client, 'name'));

            if ($clientName != "") {
                $fileName = $clientName . "-" . "Do-not-Enroll-List";
            } else {
                $fileName = "Do-not-Enroll-List";
            }

            $fileName .= "-" . date('d_M_Y_H_i_A');
            $dneAccountList = (new DoNotEnroll)->getClientWiseList($request->client_id)->pluck('account_number')->toArray();
            $acctArray = [];
            
            foreach($dneAccountList as $k=> $v){
                $acctArray[]['Account Number'] = $v;
            }
            Excel::create($fileName, function ($excel) use ($acctArray) {
                $excel->sheet('Report', function ($sheet) use ($acctArray) {
                    $column_name = 'A';
                foreach ($acctArray as $key1 => $value12) {
                    if ($key1 == 0) {
                        foreach ($value12 as $cname => $cvalue) {

                            $sheet->cell($column_name . '1', $cname, function ($cell, $cellvalue) {
                                $cell->setValue($cellvalue);
                            });
                            // $sheet->row($sheet->getHighestRow(), function ($row) {
                            //     $row->setFontWeight('bold');
                            // });
                            $column_name++;
                        }
                    } else {
                        continue;
                    }
                }
                if (!empty($acctArray)) {
                    $g = 0;
                    foreach ($acctArray as $key => $value) {
                        
                        $columnname = 'A';
                        if ($key == 0) {
                            $i = $key + 2;
                        }
                        // $sheet->setCellValueExplicit($columnname . $i, $value, \PHPExcel_Cell_DataType::TYPE_STRING);
                        
                                // $sheet->setCellValue($columnname . $i, $value[$cnam]);
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
            })->download('xlsx');

            // $headers = array(
            //     "Content-type" => "text/csv",
            //     "Column-type" => "text",
            //     "Content-Disposition" => "attachment; filename=". $fileName .".csv",
            //     "Pragma" => "no-cache",
            //     "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            //     "Expires" => "0"
            // );

            // $dneAccountList = (new DoNotEnroll)->getClientWiseList($request->client_id);
            // $columns = array('Account Number');
            // $callback = function() use ($dneAccountList, $columns) {
            //             $file = fopen('php://output', 'w');
            //             fputcsv($file, $columns);

            //             foreach($dneAccountList as $list) {
                            
            //                     $accountNumber = $list->account_number;

            //                 fputcsv($file, array($accountNumber));
            //             }
            //             fclose($file);
            //         };

            // return \Response::stream($callback, 200, $headers);
        } catch(\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error','Something went wrong, please try again.');
        }
    }
}
