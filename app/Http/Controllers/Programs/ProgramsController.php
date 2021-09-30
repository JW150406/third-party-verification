<?php

namespace App\Http\Controllers\Programs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Utilities;
use App\models\Client;
use App\models\CsvData;
use App\models\Programs;
use App\models\Commodity;
use App\models\FormField;
use App\models\CustomerType;
use App\models\Settings;
use App\models\SalescentersBrands;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CsvImportRequest;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;
use DataTables;
use Validator;
use DB;

class ProgramsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {


        if(Auth::user()->access_level =='client'){
            $client_id = Auth::user()->client_id;
            HelperCheckClientUser($client_id);

        }

        if ($request->ajax()) {
            //$programs = Programs::with('utility')->where('client_id',$request->client_id);
            $programs = Programs::select(['programs.*','commodities.name as commodity','brand_contacts.name as utilityname',DB::raw('concat( fullname, " (", market, ")") as provider'),'customer_types.name as customer_type_name'])
                ->leftJoin('utilities','programs.utility_id','=', 'utilities.id')
                ->leftJoin('brand_contacts','brand_contacts.id','utilities.brand_id')
                ->leftJoin('commodities','utilities.commodity_id','=', 'commodities.id')
                ->leftJoin('customer_types','programs.customer_type_id','=', 'customer_types.id')
                ->where('programs.client_id',$request->client_id);
            $client = Client::find($request->client_id);
            if(!empty($request->status)) {
                $programs->where('programs.status',$request->status); 
            }
            return DataTables::of($programs) 
                ->addColumn('rate',function($program){
                    return is_numeric($program->rate) ? number_format(floatval($program->rate), 4, '.', '') : $program->rate;
                })                            
                ->addColumn('action', function($program) use($client) {
                    $editBtn = $statusBtn= $deleteBtn ='';
                    
                    if (auth()->user()->hasPermissionTo('edit-program')) {
                        $editBtn = '<button 
                        data-toggle="tooltip" 
                        data-placement="top" data-container="body" 
                        title="Edit Program" 
                        data-original-title="Edit Program" 
                        role="button" 
                        data-type="edit"
                        data-id="' . $program->id . '" 
                        class="btn add-program-btn"
                        id="add-program-btn">'
                            . getimage("images/edit.png") . '</button>';
                    }

                    if (\auth()->user()->hasPermissionTo('deactivate-program') && $client->isActive()) {
                        if ($program->status == 'active') {
                            $statusBtn = '<button 
                              class=" btn change-status-program"  data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Deactivate Program" data-id="' . $program->id . '" data-programname="' . $program->name . '"  
                                data-status="inactive" data-text-status="deactivated"  role="button" >' . getimage("images/activate_new.png") . '</button>';
                        } else {
                            $statusBtn = '<button 
                              class="btn change-status-program"  data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Activate Program" data-id="' . $program->id . '" data-programname="' . $program->name . '"  
                                  data-status="active" data-text-status="activated"  role="button"  >' . getimage("images/deactivate_new.png") . '</button>';
                        }
                    } else {
                        $statusBtn = getDisabledBtn('status');
                    }
                    if (Auth::user()->hasPermissionTo('delete-program')) {
                        $class = 'delete-program';

                        $attributes = [
                            "data-original-title" => "Delete Program",
                            "data-id" => $program->id,
                            "data-programname" => $program->name
                        ];
                        $deleteBtn = getDeleteBtn($class, $attributes);
                    }

                    return '<div class="btn-group">'.$editBtn.$statusBtn.$deleteBtn.'<div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $utilities = array();
        $programs = array();
        $client_id = "";
        $uid = "";
        if(isset($request->client)){
            $client_id =  $request->client;
        }
        if(!empty($client_id)){
            $condition =  array( array('client_id','=',$client_id));
            $utilities = (New Utilities)->getUtilities($condition);
           
        }
        if(isset($request->utility)){
            $uid =  $request->utility;
        }
        if(!empty($client_id) || !empty($uid) ){
           $programs = (new Programs)->getPrograms($client_id,$uid);
        }

        $clients = (new Client)->getClientsList();
        return view('client.utilities.programs.programs',compact('utilities','client_id','clients','programs','uid'))
            ->with('i', ($request->input('page', 1) - 1) * 20);
    }
    public function clientUtilities(Request $request){
         $client_id = $request->client_id;
         $condition =  array( array('client_id','=',$client_id));
         $utilities = (New Utilities)->getUtilities($condition);
         $res_options = "";
         foreach($utilities as $utility){
             $res_options.="<option value=\"{$utility->id}\">{$utility->utilityname}</option>";
         }
         $response = array(
             'status' => 'success',
             'options' =>  $res_options,
         );
         return \Response::json($response);
    }

    /**
     * This method is used to add new program
     */
    public function addnewprogram(Request $request){
       $invalid_request = "";
       $client_id = "";
       $uid = "";

      if( (!isset($request->client)  ) || ( empty($request->client)  )   ){
        $invalid_request = "Missing Required Parameters";
      }
      else{
        $client_id  = $request->client;

      }
      return view('client.utilities.programs.addnewprogram',compact('invalid_request','client_id','uid'))
        ->with('i', ($request->input('page', 1) - 1) * 20);
    }

    /**
     * This method is used to import program fields
     */
    public function parseImport(CsvImportRequest $request)
    {

        $path = $request->file('csv_file')->getRealPath();
        $extension = $request->file('csv_file')->extension();
        if ($request->has('header')) {
            $data = Excel::load($path, function($reader) {})->ignoreEmpty()->get()->toArray();
        } else {
              if($extension == 'xlsx'){
                  $data = Excel::load($path, function($reader) {})->ignoreEmpty()->get()->toArray();
              }else{
                  $data = array_map('str_getcsv', file($path));
              }
        }
        $sheet_counts = Excel::load($path)->getSheetCount();


        if (count($data) > 0) {
          $csv_header_fields = [];
            if ($request->has('header')) {

                foreach ($data[0] as $key => $value) {
                    $csv_header_fields[] = $key;
                }
            }else{
              $i = 0;
               if($sheet_counts>1){

                 foreach ($data[0]   as   $first_sheet) {
                    if($i==0){
                      foreach ($first_sheet as $column_name => $column_value) {
                        $csv_header_fields[] = $column_name;
                      }
                    }

                    $i++;
                 }
                   $i++;
                   $csv_data = array_slice($data[0], 0, 2);
               }else{

                 foreach ($data   as  $column_name => $column_value) {
                        $csv_header_fields[] = $column_name;
                 }
               }
               $csv_data = array_slice($data, 0, 2);

            }


            $csv_data_file = CsvData::create([
                'csv_filename' => $request->file('csv_file')->getClientOriginalName(),
                'csv_header' => $request->has('header'),
                'csv_data' => json_encode($data)
            ]);
        } else {
            return redirect()->back();
        }
        $database_fields = (new Programs)->tableFields;
        $client_id  = $request->client;
        $utility_id  = $request->utility;

        return view('client.utilities.programs.import_fields', compact( 'csv_header_fields', 'csv_data', 'csv_data_file','database_fields','client_id','utility_id'));

    }
    public function processImport(Request $request)
    {
        $data = CsvData::find($request->csv_data_file_id);
        $database_fields = (new Programs)->tableFields;
        $csv_data = json_decode($data->csv_data, true);
        $client_id  = $request->client;
        $utility_id  = $request->utility;
        $i = 0;

    //     echo "<pre>";
    //    print_r($database_fields);
    //    print_r($request->fields);
        foreach ($csv_data as $row) {

            if($i>0){

                $data_array = array();
                $program = new Programs();
                foreach ($request->fields as $index => $field) {

                    if ($data->csv_header) {
                       $data_array[$field] = $row[$request->fields[$field]];
                    } else {
                          $db_filed  =  $request->fields[$index];
                          if(in_array($db_filed,$database_fields)){
                             if(isset($row[$index])){
                              // $program->$db_filed = $row[$index];
                               $data_array[$db_filed] = $row[$index];
                             }
                          }

                    }

                }

              //  print_r($data_array);
              if(isset($data_array['name']) && !empty($data_array['name']) ){
                $data_array['client_id'] = $client_id;
                $program =  (new Programs)::firstOrCreate( $data_array );
                $program->created_by = Auth::user()->id;
                $program->save();  
              }


            }

            $i++;
        }


        $data = CsvData::find($request->csv_data_file_id);
        $data->delete();

        return redirect()->route('utility.programs',['client' => $client_id, 'utility' => $utility_id ])
                ->with('success','Program successfully imported.');
    }

    /**
     * This method is used to remove program
     */
    public function delete(Request $request){
        try {
            Programs::destroy($request->id);
            return response()->json([ 'status' => 'success',  'message'=>'Program successfully deleted.']);
        } catch(\Exception $e) {
            \Log::error($e);
            return response()->json([ 'status' => 'error',  'message'=>'Something went wrong, please try again.']);
        }
    }

    /**
     * This method is used to change status
     */
    public function changeStatus(Request $request) {
        Programs::where('id',$request->id)->update(['status'=>$request->status]);
        return response()->json([ 'status' => 'success',  'message'=>'Program successfully updated.']);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        /* Start Validation rule */
        $request->validate(
          [
            'commodity'=>'required|max:255',
            'utilityname'=>'required|max:255',
            'fullname'=>'required|max:255',
            'name'=>'required|max:255',
            'code'=>'required',
            'rate'=>'required',
            'etf'=>'required',
            'msf'=>'required',
            'term'=>'required',
            'customer_type_id'=>'required|max:255',
            'unit_of_measure'=>'required|max:255',

          ],[
            'utilityname.required' => 'This field is required',
            'fullname.required' => 'This field is required', 
            'customer_type_id.required' => 'This field is required', 
            'unit_of_measure.required' => 'This field is required', 
          ]
        );
        /* End Validation rule */

        $utility = Utilities::where('client_id',$request->client_id)
            ->where('commodity_id',$request->commodity)
            // ->where('utilityname',$request->utilityname)
            ->where('fullname',$request->fullname)
            ->whereHas('brandContacts',function($q) use($request){
                $q->where('name',$request->utilityname);
            })
          //  ->where('market',$request->market)
            ->first();

        $data = $request->except(['utilityname','commodity','fullname','market']);
        if(!empty($utility)){
            $data['utility_id'] =$utility->id;
        }
        $data['created_by'] = Auth::id();
        
        if(isset($request->id) && !empty($request->id)){
            $editProgram = Programs::find($request->id);
            $program = $editProgram->update($data);
        }else{
            $program = Programs::create($data);
        }
                
        if (!empty($program)) {      
            $successMsg = '';
            if(empty($request->id)){
                $successMsg = 'Program successfully created.';
            }else{
                $successMsg = 'Program successfully updated.';
            }
            return response()->json([ 'status' => 'success',  'message'=>$successMsg]);
        } else {
            return response()->json([ 'status' => 'error',  'message'=>'Something went wrong, please try again.']); 
        }
    }

    /**
     * This method is used to get program form utility
     */
    public function getProgramsFormUtility(Request $request){
      if(isset($request->utility_id)){
        $utility = Utilities::with('utilityCommodity')->find($request->utility_id);
        $brand_name = $utility->brandContacts->name;
        $salescenterBrand = SalescentersBrands::where('salescenter_id',auth()->user()->salescenter_id)->where('brand_id',array_get($utility,'brand_id'))->with('restrictProg')->first();
        $formId = $request->form_id;
        // for get restrcted program
        if (!empty($salescenterBrand->restrictProg)) {
            $restrictProg = $salescenterBrand->restrictProg->pluck('program_id')->toArray();
        } else {
            $restrictProg = [];
        }
            
        $programs = (new Programs)->getAllProgramsByUtility($request->utility_id, $restrictProg);
        if(count($programs) > 0) {
            $commodityName = !empty($utility->utilityCommodity) ? $utility->utilityCommodity->name : '';
            /* Get enable custom fields for program */
            $customFields = getEnableCustomFields($utility->client_id);

            $utilityValidations = [];
            if (!$utility->validations->isEmpty()) {
                foreach ($utility->validations as $validation) {                    
                    $field = FormField::select('id','label')->where('form_id',$formId)->whereIn('type',['textbox','textarea'])->where('label',$validation->label)->first();
                    if($field) {                                        
                        $utilityValidations[] = [
                            'field_id' => $field->id,
                            'regex' => isset($validation->regex) ? $validation->regex : "",
                            'regex_message' => isset($validation->regex_message) ? $validation->regex_message : "",
                        ];
                    }
                }
            }

            $response  = array(
                'status' => 'success',
                'totalrecords' => count($programs),
                'data' => $programs,
                'commodity' => $commodityName,
                'regex' => $utility->regex,
                'regex_message' => $utility->regex_message,
                'custom_fields' => $customFields,
                'act_num_verbiage' => $utility->act_num_verbiage ? $utility->act_num_verbiage : '',
                'utility_validations' => $utilityValidations,
                'brand_name' => $brand_name
             );
        }else{
            $response  = array(
                'status' => 'error',
                'message' => 'Program is not available under selected utility.' 
             );
         }
    }else{
        $response  = array(
            'status' => 'error',
            'message' => "Invalid zip code."
         );
      }
     return \Response::json($response);

    }

    /**
     * This method is used to export program
     */
    public function exportProgram(Request $request) {

        try {
            $client = Client::findOrFail($request->client_id);

            $clientName = str_replace(" ", "-", array_get($client, 'name'));
            
            if ($clientName != "") {
                $fileName = $clientName . "-" . "programs";
            } else {
                $fileName = "programs";
            }

            $fileName .= "-" . date('d_M_Y_H_i_A');

            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=". $fileName .".csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );

            $programs = Programs::select(['programs.*','utilities.commodity','brand_contacts.name as utilityname','utilities.fullname as provider','utilities.market as abbreviation','customer_types.name as customer_type_name',DB::raw("(select GROUP_CONCAT(zip_codes.zipcode SEPARATOR ', ') from utility_zipcodes left join zip_codes on utility_zipcodes.zipcode_id = zip_codes.id where utility_zipcodes.utility_id = programs.utility_id) as zipcodes")])
                ->leftJoin('utilities','programs.utility_id','=', 'utilities.id')
                ->leftJoin('brand_contacts','brand_contacts.id','=','utilities.brand_id')
                ->leftJoin('customer_types','programs.customer_type_id','=', 'customer_types.id')
                ->where('programs.client_id',$request->client_id)->get();
            $columns = array('Commodity', 'Brand Name', 'Utility Provider', 'Abbreviation','Customer Type', 'Program Name', 'Program Code', 'Rate ($)','Unit','ETF ($)', 'MSF ($)', 'Term (Months)', 'Zipcodes');

            /* get enable custom fields for program */
            $customFields = Settings::getEnableFields($request->client_id);
            $fields = array_values($customFields);

            // added custom field enable program
            if (!empty($fields)) {
                $columns = array_merge($columns, $fields);
            }

            $callback = function() use ($programs, $columns, $customFields) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                foreach($programs as $program) {
                    $data = array($program->commodity, $program->utilityname, $program->provider, $program->abbreviation, $program->customer_type_name,$program->name, $program->code, $program->rate,$program->unit_of_measure, $program->etf, $program->msf, $program->term, $program->zipcodes);

                    // export custom field enable value
                    foreach($customFields as $key => $value) {
                        array_push($data, $program[$key]);
                    }

                    fputcsv($file, $data);
                }
                fclose($file);
            };

            return \Response::stream($callback, 200, $headers);
        } catch(\Exception $e) {
            \Log::error($e);
            return redirect()->back()->with('error','Something went wrong, please try again.');
        }
    }

    /**
     * This method is used to show bulkupload
     */
    public function bulkUpload($clientId)
    {
        $client = Client::active()->findOrFail($clientId);
        $customFields = Settings::getEnableFields($clientId);
        return view('client.utility_new.program.bulkupload',compact('client','customFields'));
    }

    /**
     * This method is used to dowload sample file
     */
    public function downloadSample($clientId) {
        $data =[[
            'Commodity' => 'Gas',
            'Brand Name' => 'Always Energy',
            'Utility Provider' => 'Consolidated Edison',
            'Customer Type'=>'Commercial',
            'Program Name'=>'Clean Gas 12',
            'Program Code'=>'123456',
            'Rate ($)'=>'0.756',
            'Unit'=>'THM',
            'ETF ($)'=>'50',
            'MSF ($)'=>'0',
            'Term (Months)'=>'12',
        ],[
            'Commodity' => 'Electric',
            'Brand Name' => 'Green Clean',
            'Utility Provider' => 'Consolidated Edison',
            'Customer Type' => 'Residential',
            'Program Name'=>'Green Program 12',
            'Program Code'=>'123457',
            'Rate ($)'=>'0.0856',
            'Unit'=>'kWh',
            'ETF ($)'=>'100',
            'MSF ($)'=>'50',
            'Term (Months)'=>'12',
        ],[
            'Commodity' => 'Electric',
            'Brand Name' => 'Always Energy',
            'Utility Provider' => 'PPL Electric Utilities Corp',
            'Customer Type' => 'Residential',
            'Program Name'=>'Always Clean 6',
            'Program Code'=>'123458',
            'Rate ($)'=>'0.0998',
            'Unit'=>'kWh',
            'ETF ($)'=>'0',
            'MSF ($)'=>'75',
            'Term (Months)'=>'6',
        ]];

        $customFields = Settings::getEnableFields($clientId);
        $fields = [];
        foreach ($customFields as $key => $value) {
            $fields[$value] = '';
        }
        if (!empty($fields)) {            
            foreach ($data as $key => $value) {
                $data[$key] = array_merge($value, $fields);
            }
        }
        return Excel::create('programs_sample', function($excel) use ($data) {
            $excel->sheet('sheet1', function($sheet) use ($data)
            {                 
                $sheet->fromArray($data);
            });
        })->download("csv");
    }

    /**
     * This method is used to program import
     */
    public function import(Request $request) {
        $client_id=$request->client_id;
        /* Start Validation rule */
        $validator = Validator::make(
            [
                'upload_file' => $request->hasFile('upload_file')? strtolower($request->file('upload_file')->getClientOriginalExtension()) : null,
            ],
            [
                'upload_file'      => 'required|in:csv,xlsx,xls',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['status' => 'error',  'errors' => $validator->errors()->messages()], 422);
        }
        /* End Validation rule */

        try {
            $path = $request->file('upload_file')->getRealPath();
            $customFields = Settings::getEnableFields($client_id);
            $columns= [
                'commodity',
                'brand_name',
                'utility_provider',
                'customer_type',
                'zipcodes',
                'program_name',
                'program_code',
                'rate',
                'unit',
                'etf',
                'msf',
                'term_months',
            ];

            $customValues= array_values($customFields);

            $customValues = array_map(function($value){
                return str_replace(' ','_', strtolower($value));
            },$customValues);

            // added custom column  for select in file
            if (!empty($customFields)) {
                $columns = array_merge($columns, $customValues);
            }

            $data = Excel::load($path, function($reader) use ($columns) {
                
                $reader->select($columns);
            })
            ->ignoreEmpty()
            ->get()
            ->toArray();
            $errors=$valid_data=array();
            info($data);
            if (empty($data)) {
                $errors[1][]='The file is empty or invalid data.';
                $view = view('client.errors.file-errors', compact('errors'))->render();
                return response()->json(['status' => 'dataErrors',  'errors' => $view], 422);
            }
            foreach ($data as $key => $program_data) {
                
                $commodity = !empty($program_data['commodity']) ? $program_data['commodity'] : null ;
                $brand_name = !empty($program_data['brand_name']) ? $program_data['brand_name'] : null;
                $commodityData = Commodity::select('id')->where('name',$commodity)->where('client_id',$client_id)->first();
                $commodity_id =  !empty($commodityData) ? $commodityData->id : null;
                /* Start Validation rule */
                $dataValidator = Validator::make($program_data,
                    [
                        'commodity'          => 'required|max:255|exists:commodities,name,client_id,'.$client_id,
                        'customer_type'          => 'required|max:255|exists:customer_types,name,client_id,'.$client_id,
                        'brand_name'          => 'required|max:255|exists:brand_contacts,name,client_id,'.$client_id,
                        'utility_provider'  => ['required','max:255',
                            Rule::exists('utilities','fullname')->where(function ($query) use ($commodity_id,$client_id) {
                                $query->where('commodity_id',$commodity_id);
                                $query->where('client_id',$client_id);
                            })],                                              
                        'program_name'=>'required|max:255',
                        //'program_code'=>'required|unique:programs,code,client_id:' . $client_id,
                        'program_code'=>'required',
                        'rate'=>'required',
                        'unit' => ['required','max:255',
                            Rule::exists('commodity_units','unit')->where(function ($query) use ($commodity_id, $client_id) {
                                $query->where('commodity_id', $commodity_id);
                            })
                        ],
                        'etf'=>'required',
                        'msf'=>'required',
                        'term_months'=>'required',
                    ]
                );  
                
                if ($dataValidator->fails()) {
                    foreach ($dataValidator->messages()->all() as  $value) {
                        $errors[$key+1][]=$value;
                    }
                } else {
                    $valid_data[] = $program_data;
                }
                /* End Validation rule */
            }
            if (!empty($errors)) {
                $view = view('client.errors.file-errors', compact('errors'))->render();
                return response()->json(['status' => 'dataErrors',  'errors' => $view], 422);
            } else {

                foreach ($valid_data as $key => $program_data) {
                    $commodity= Commodity::where('name',$program_data['commodity'])->where('client_id',$client_id)->firstOrFail();
                    $customerType= CustomerType::where('name',$program_data['customer_type'])->where('client_id',$client_id)->first();
                    $utility = Utilities::where('client_id',$client_id)
                            ->where('commodity_id',$commodity->id)
                            ->whereHas('brandContacts',function($q) use($program_data){
                                $q->where('name',$program_data['brand_name']);
                            })
                            // ->where('utilityname',$program_data['brand_name'])
                            ->where('fullname',$program_data['utility_provider'])
                            ->first();                
                    $data = Arr::only($program_data, ['rate', 'etf', 'msf']);

                    // added custom column for store in program table
                    foreach ($customFields as $key => $customField) {
                        $value = str_replace(' ','_', strtolower($customField));
                        if (isset($program_data[$value])) {
                            $data[$key] = $program_data[$value];
                        }
                    }
                    $data['name'] = $program_data['program_name'];
                    $data['code'] = $program_data['program_code'];
                    $data['unit_of_measure'] = $program_data['unit'];
                    $data['term'] = $program_data['term_months'];

                    if(!empty($customerType)){
                        $data['customer_type_id'] =$customerType->id;
                    }
                    if(!empty($utility)){
                        $data['utility_id'] =$utility->id;
                        $data['client_id'] =$client_id;
                        $data['created_by'] = Auth::id();
                        $program=Programs::create($data);
                        
                    }      
                }
                $url = \URL::route('client.show',['id' => $client_id]). '#Programs';
                // session()->put('success', 'Programs successfully imported.');
                return response()->json(['status' => 'success',  'message' =>'Programs successfully imported.', 'url' =>$url], 200);
            }
        } catch(\Exception $e) {
            \Log::error('Program import error: '.$e->getMessage());
            return response()->json(['status' => 'error',  'message' => $e->getMessage()], 500);
        }
        
        
    }

    // Edit Program route
    public function edit(Request $request)
    {        
        // $program = Programs::where('id',$request->program_id)->get();
        $program = Programs::select(['programs.*','utilities.commodity_id','commodities.name as commodity','brand_contacts.name as utilityname','brand_contacts.id as utilitynameid','fullname as provider','customer_types.name as customer_type_name','customer_types.id as customer_type_name_id'])
        ->leftJoin('utilities','programs.utility_id','=', 'utilities.id')
        ->leftJoin('brand_contacts','brand_contacts.id','utilities.brand_id')
        ->leftJoin('commodities','utilities.commodity_id','=', 'commodities.id')
        ->leftJoin('customer_types','programs.customer_type_id','=', 'customer_types.id')
        ->where('programs.id',$request->program_id)
        ->first();                

        if(!empty($program)) {
            return response()->json(['status' => 'success', 'data' => $program]);
        } else {
            return response()->json([ 'status' => 'error',  'message'=>'Program not found.']);
        }
    }
}
