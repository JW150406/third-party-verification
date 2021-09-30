<?php

namespace App\Http\Controllers\Utility;

use App\models\UtilityMapping;
use App\models\UtilityValidation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Utilities;
use App\models\Client;
use App\models\Commodity;
use App\models\Programs;
use App\models\CsvData;
use App\models\Zipcodes;
use App\models\UtilityZipcodes;
use App\models\Salesagentdetail;
use App\Http\Requests\utilitiesRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CsvImportRequest;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Arr;
use App\models\Brandcontacts;
use Log;
use Validator;
use DataTables;
use DB;

class UtilityController extends Controller
{


    private  $options = array(
        array(
            "label" => 'Pennsylvania Power and Light Fixed Power 6',
            "code" => "571",
            "rate" => "0.0855/KwH",
            "etf" => "$50.00",
            "msf" => "$0.00",
            "term" => "6"
        ),
        array(
            "label" => 'Pennsylvania Power and Light Spark Advantage Plus 24',
            "code" => "161",
            "rate" => "0.0889/KwH",
            "etf" => "$100.00",
            "msf" => "$4.95",
            "term" => "24"
        ),
        array(
            "label" => 'Pennsylvania Power and Light Spark Advantage Plus 12',
            "code" => "311",
            "rate" => "0.0889/KwH",
            "etf" => "$100.00",
            "msf" => "$4.95",
            "term" => "12"
        )
        ,
        array(
            "label" => 'Pennsylvania Power and Light Spark Green Advantage Plus 24',
            "code" => "265",
            "rate" => "0.0909/KwH",
            "etf" => "$100.00",
            "msf" => "$4.95",
            "term" => "24"
        )
    );

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {

        if ($request->ajax()) {

            
            $utilities = Utilities::leftjoin('brand_contacts','brand_contacts.id','=','utilities.brand_id')
                ->select('utilities.*','brand_contacts.name',DB::raw("(SELECT GROUP_CONCAT(mapped_utility_id) FROM utility_mappings where `utilities`.`id`= utility_mappings.utility_id ) as 
                mapped_utility_id"))
                ->where('utilities.client_id',$request->client_id)
                ->with('utilityZipcodes.zipCode','utilityCommodity', 'validations');
            $client = Client::find($request->client_id);
            return DataTables::of($utilities)
                ->editColumn('fullname', function($utility){
                    $fullname ='';
                    if(!empty($utility->fullname) && !empty($utility->market)) {
                        $fullname = $utility->fullname ." (".$utility->market.")";
                    }
                    return $fullname;
                })
                ->addColumn('states', function($utility){

                    if(!$utility->utilityZipcodes->isEmpty()) {
                        $states = $utility->utilityZipcodes->pluck('zipCode.state')->unique()->implode(', ');
                        if (strlen($states) > 160) {
                            $stringCut = substr($states, 0, 160);
                            $states = $stringCut.'...';
                        }

                    } else {
                        $states ='N/As';
                    }
                    return $states;
                })
                ->addColumn('zipcode', function($utility){

                    if(!$utility->utilityZipcodes->isEmpty()) {
                        $zipcode = $utility->utilityZipcodes->pluck('zipCode.zipcode')->implode(', ');
                        if (strlen($zipcode) > 160) {
                            $stringCut = substr($zipcode, 0, 160);
                            $zipcode = $stringCut.'...';
                        }

                    } else {
                        $zipcode ='N/As';
                    }
                    return $zipcode;
                })
                ->addColumn('action_validation', function($utility){


                    $viewvalidationBtn = '<button
                        data-toggle="tooltip"
                        data-placement="top" data-container="body"
                        data-original-title="View Utility Validations"
                        role="button"
                        title="View Utility Validation"
                        class="btn view-utility-validation"
                        data-id="' . $utility->id . '"
                        data-type="view-validation"                        
                        >View ('.$utility->validations->count().')</button>';
                    return $viewvalidationBtn;
                })
                ->addColumn('action_mapping', function($utility){

                    $viewmappingBtn = '<button
                        data-toggle="tooltip"
                        data-placement="top" data-container="body"
                        data-original-title="View Utility Mapping"
                        role="button"
                        title="View Utility Mapping"
                        class="btn view-utility-mapping"
                        data-id="' . $utility->id . '"
                        data-type="view-mapping"                        
                        ><b>+ View('.count(array_filter(explode(",", $utility->mapped_utility_id))).')</b></button>';
                    return $viewmappingBtn;
                })
                ->addColumn('action', function($utility) use($client) {
                    $editBtn =  $deleteBtn ='';

                    if (\auth()->user()->hasPermissionTo('view-utility')) {
                        $viewBtn = '<button
                        data-toggle="tooltip"
                        data-placement="top" data-container="body"
                        data-original-title="View Utility"
                        role="button"
                        class="btn view-utility"
                        data-id="' . $utility->id . '"
                        data-type="view"
                        >' . getimage("images/view.png") . '</button>';
                    }else{
                        $viewBtn = '<button
                        title="View Utility"
                        class="btn view-utility"
                        >' . getimage("images/view.png") . '</button>';
                    }


                    if (Auth::user()->hasPermissionTo('edit-utility') && $client->isActive()) {
                        $editBtn = '<button
                            data-toggle="tooltip"
                            data-placement="top" data-container="body"
                            data-original-title="Edit Utility"
                            role="button"
                            class="btn edit-utility"
                            data-id="'.$utility->id.'"
                            data-type="edit"
                            >'.getimage("images/edit.png").'</button>';
                    }else{
                        $editBtn = '<button
                            title="Edit Utility"
                            class="btn cursor-none"
                            >'.getimage("images/edit-no.png").'</button>';
                    }

                    if (Auth::user()->hasPermissionTo('delete-utility')  && $client->isActive()) {
                        $deleteBtn = '<button  class="btn delete-utility"  data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Delete Utility" data-id="'.$utility->id.'" id="delete-utility-'.$utility->id.'" data-utilityname="'.$utility->fullname.'"  role="button">'.getimage("images/cancel.png").'</button>';
                    }else{
                        $deleteBtn = '<button  class="btn cursor-none" title="Delete Utility" role="button">'.getimage("images/cancel-no.png").'</button>';
                    }
                    return '<div class="btn-group">'.$viewBtn.$editBtn.$deleteBtn.'<div>';
                })
                ->rawColumns(['zipcode','action' ,'action_validation', 'action_mapping'])
                ->make(true);
        }
        $utilities = array();
        $client_id = "";
        $search_text = "";
        if(isset($request->client)){
            $client_id = $request->client;
            HelperCheckClientUser($client_id);

            if(isset($request->search_text) && $request->search_text !=" "){
                  $search_text = $request->search_text;
            }
            $utilities = (New Utilities)->getClientUtilities($client_id,$search_text );
        }
        $clients = (new Client)->getClientsList();
        return view('client.utilities.index',compact('utilities','client_id','clients','search_text'))
            ->with('i', ($request->input('page', 1) - 1) * 20);
    }
    public function addnew($client_id)
    {
       return view('client.utilities.addnew',compact('client_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $client_id
     * @return \Illuminate\Http\Response
     */
    public function savenew(utilitiesRequest $request, $client_id){
        /* Start Validation rule */
        $validator = \Validator::make($request->all(), [
            'utilityname' => 'required',
            'market' => 'required',
            'commodity' => 'required',
        ]);

        if ($validator->fails())
        {
            return response()->json([ 'status' => 'error',  'errors'=>$validator->errors()->all()]);
        }
        /* End Validation rule */
        try{


            $input = $request->only('commodity','utilityname','market');
            $input['client_id'] = $client_id;
            $input['created_by'] = Auth::user()->id;
            $client_info = (new Client)->getClientinfo($client_id);
            $input['company'] = $client_info->name;
            $utilities = (New Utilities)->saveUtility($input);
            return response()->json([ 'status' => 'success',  'message'=>'Utility successfully added.','url' => route('utilities.index',['client'=>$client_id])]);
        } catch(Exception $e) {
        // echo 'Message: ' .$e->getMessage();
        return response()->json([ 'status' => 'error',  'errors'=> ["Something went wrong, please try again."]]);
        }
        // return redirect()->back()
        // ->with('success','Utility successfully added.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function store(Request $request){
        /* Start Valiation rule */
        $request->validate([
            'commodity' => 'required',
            'brand_id' => 'required',
            'fullname' => 'required',
            'market' => 'required',
            'regex' => 'required',
            'regex_message' => 'required',
            // 'zipcode' => 'required',
        ],[
            'brand_id.required' => 'This field is required',
            'fullname.required' => 'This field is required',
            'market.required' => 'This field is required',
        ]);
        /* End Validation rule */

        try{
            $client_id = $request->client_id;
            $input = $request->except('zipcode','_token');
            $commodity = Commodity::find($request->commodity);
            $input['commodity_id'] =$request->commodity;
            $input['commodity'] =$commodity->name;
            // For act_num_verbiage (placeholder)
            if ($request->act_num_verbiage != null) {
                $act_num_verbiage = $request->act_num_verbiage;
            } else {
                $act_num_verbiage = "";
            }
            $input['act_num_verbiage'] = $act_num_verbiage;
            
            $id=$request->id;
            $isMerge = false;
            
            $utility = Utilities::where('client_id', $client_id)->where('brand_id', $request->brand_id)->where('commodity_id', $request->commodity)->where('fullname', $request->fullname)->where('market', $request->market);

            if (!empty($request->id)) {
                $exist = $utility->where('id','!=',$request->id)->first();
                if ($exist) {
                    return response()->json([ 'status' => 'error',  'message'=> "This utility already exists. frrrrr"]);
                }
                $utility = Utilities::where('id',$request->id)->update($input);
                $message='Utility successfully updated.';

            } else {
                    $utility = $utility->first();
//                $insertData = ['client_id' => $client_id, 'utilityname' => $request->utilityname, 'commodity_id' => $request->commodity, 'fullname' => $request->fullname, 'market' => $request->market];
                
                if (empty($utility)) {
                    $input['client_id'] = $client_id;
                    $input['created_by'] = Auth::user()->id;
                    $client_info = (new Client)->getClientinfo($client_id);
                    $input['company'] = $client_info->name;
                    $input['company'] = $client_info->name; 
                    $input['act_num_verbiage'] = $act_num_verbiage;

                    $createdUtility = Utilities::create($input);
                    $id = $createdUtility->id;
                } else {
                    $id = $utility->id;
                    $isMerge = true;
                    $exist = $this->checkUtilityExistOrNot($id,$request->input('zipcode'));

                    if ($exist) {
                        return response()->json([ 'status' => 'error',  'message'=> "This utility already exists."]);
                    }
                }

//                $utility = Utilities::updateOrCreate($insertData, $insertData);

//                $input['client_id'] = $client_id;
//                $input['created_by'] = Auth::user()->id;
//                $client_info = (new Client)->getClientinfo($client_id);
//                $input['company'] = $client_info->name;
//                $utility =Utilities::create($input);

                $message = 'Utility successfully added.';
            }

            if(!empty($request->input('zipcode'))) {
                if ($isMerge) {
                    $this->mergeUtilityZipCode($id, $request->input('zipcode'));
                } else {
                    $this->storeUtilityZipCode($id, $request->input('zipcode'));
                }
            }
            return response()->json([ 'status' => 'success',  'message'=>$message]);
        } catch(\Exception $e) {
            \Log::error($e);
            return response()->json([ 'status' => 'error',  'message'=> $e->getMessage()]);
        }

    }

    /**
     * This method is used to check utility exist or not
     */
    public function checkUtilityExistOrNot($utilityId,$zipcodes) {
        $zipcodeIds = Zipcodes::whereIn('zipcode',$zipcodes)->pluck('id');
        $utilitiesCount =UtilityZipcodes::where('utility_id',$utilityId)->whereIn('zipcode_id',$zipcodeIds)->count();
        return $utilitiesCount > 0;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $utility_id, $zipcodes
     * @return \Illuminate\Http\Response
     */
    public function storeUtilityZipCode($utility_id,$zipcodes){
        UtilityZipcodes::where('utility_id',$utility_id)->delete();
        $zips = array();
        foreach ($zipcodes as $key => $value) {
            $zipcode = Zipcodes::where('zipcode',$value)->first();
            $zip = new UtilityZipcodes();
            if(!empty($zipcode)) {
                $zip->utility_id = $utility_id;
                $zip->zipcode_id = $zipcode->id;
                $zips[] = $zip;
            }
        }

        if(count($zips)>0)
        {
            $utility = Utilities::find($utility_id);
            $utility->utilityZipcodes()->saveMany($zips);
        }
    }

    /**
     * This method is used to merge utility zipcode
     */
    public function mergeUtilityZipCode($utility_id, $zipcodes){
        $zips = array();
        $utility = Utilities::find($utility_id);
        foreach ($zipcodes as $key => $value) {
            $zipcode = Zipcodes::where('zipcode',$value)->first();
            if(!empty($zipcode) && !empty($utility)) {
                $data = [
                    'utility_id' => $utility_id,
                    'zipcode_id' => $zipcode->id
                ];
                $utility->utilityZipcodes()->updateOrCreate($data,$data);
            }
        }
    }

    /**
     * This method is used to edit utility 
     */
    public function edit(Request $request) {
        $utility=Utilities::with('utilityZipcodes.zipCode')->select('utilities.*','brand_contacts.name')->where('utilities.id',$request->id)->leftjoin('brand_contacts','brand_contacts.id','=','utilities.brand_id')->first();
        if(!empty($utility)) {
            return response()->json([ 'status' => 'success',  'data'=>$utility ]);
        } else {
            return response()->json([ 'status' => 'error',  'message'=>'Something went wrong, please try again.']);
        }

    }

    /**
     * This method is used to show view utility
     */
    public function viewutility($utility_id)
    {
       $utility =  (New Utilities)->getUtility($utility_id);
       return view('client.utilities.view',compact('utility'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $utility_id
     * @return \Illuminate\Http\Response
     */
    public function editutility($utility_id)
    {
       $utility =  (New Utilities)->getUtility($utility_id);
      $zipcodes =  (new  UtilityZipcodes)->getUtilityZipcodes($utility_id);

       return view('client.utilities.editutility',compact('utility','zipcodes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function updateutility($id,utilitiesRequest $request){
        $input = $request->only('commodity','utilityname','market');
        $input['created_by'] = Auth::user()->id;
        $client_id = $request->client_id;
        $client_info = (new Client)->getClientinfo($client_id);
        $input['company'] = $client_info->name;
        $utilities = (New Utilities)->updateUtility($id,$input);
        return redirect()->back()
        ->with('success','Utility successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     * @return \Illuminate\Http\Response
     */
    public function deleteutility(Request $request){
        if(isset($request->id)){

            if ($request->ajax()) {
                $programCount=Programs::where('utility_id',$request->id)->count();
                if($programCount > 0) {
                    return response()->json([ 'status' => 'error',  'message'=>'You cannot delete this utility.']);
                }
                $utilities = (New Utilities)->deleteUtility($request->id);
                return response()->json([ 'status' => 'success',  'message'=>'Utility successfully deleted.']);
            }
            $utilities = (New Utilities)->deleteUtility($request->id);
            return redirect()->back()
            ->with('success','Utility successfully deleted');
        }else{
            if ($request->ajax()) {
                return response()->json([ 'status' => 'error',  'message'=>'Invalid request.']);
            }
            return redirect()->back()
            ->with('error','Invalid Request.');
        }


    }

    /**
     * This method is used to get utility
     */
    public function getutility(Request $request){

        $res_options = "";
        foreach($this->options as $option){
            $res_options.="<option data-code=\"{$option['code']}\" data-rate=\"{$option['rate']}\"  data-etf=\"{$option['etf']}\" data-msf=\"{$option['msf']}\" data-term=\"{$option['term']}\">".$option['label']."( code: {$option['code']}, rate:{$option['rate']}, etf: {$option['etf']}, msf:{$option['msf']}, term:{$option['term']})</option>";
        }
        $response = array(
            'status' => 'success',
            'options' =>  $res_options,
        );
        return \Response::json($response);
    }

    /**
     * This method is used to get client utility
     */
    public function getClientUtility(Request $request){

        if(isset($request->client_id)){
            $utilities = (New Utilities)->getClientAllUtilities($request->client_id);

            $res_options = "";
            foreach($utilities as $utility){
                $res_options.="<option value=\"$utility->id\">".$utility->utilityname ."($utility->commodity) ($utility->zip)</option>";
            }
            $response = array(
                'status' => 'success',
                'options' =>  $res_options,
            );
        }else{
            $response = array(
                'status' => 'error',
                'message' =>  "Invalid Request",
            );
        }

        return \Response::json($response);
    }

    /**
     * This method is used to get admin utility
     */
    public function admingetutility(Request $request){

        $res_options = "";
        $clientid =  $request->clientid;
        $utility_id =  $request->utility_id;
        $utility =  (New Utilities)->getUtility($utility_id);

        $this->options =   (new Programs)->getAllPrograms_using_utility_shortname($clientid,$utility->utilityshortname );
        foreach($this->options as $option){
            $res_options.="<option value=\"{$option['id']}\" data-programname=\"{$option['name']}\" data-code=\"{$option['code']}\" data-rate=\"{$option['rate']}\"  data-etf=\"{$option['etf']}\" data-msf=\"{$option['msf']}\" data-term=\"{$option['term']}\">".$option['name']."( code: {$option['code']}, rate:{$option['rate']}, etf: {$option['etf']}, msf:{$option['msf']}, term:{$option['term']})</option>";
        }
        $response = array(
            'status' => 'success',
            'options' =>  $res_options,
        );
        return \Response::json($response);
    }

    /**
     * This method is used to show import utility
     */
    public function importutility(Request $request){
        $client_id = $request->client_id;
        return view('client.utilities.importnew',compact('client_id'));
    }

    /**
     * This method is used to import utility
     */
    public function parseImport(CsvImportRequest $request)
    {

        /* Start Validation rule */
        $validator = Validator::make(
            [
                'file'      => $request->file('csv_file'),
                'extension' => strtolower($request->file('csv_file')->getClientOriginalExtension()),
            ],
            [
                'file'          => 'required',
                'extension'      => 'required|in:csv,xlsx,xls',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }
        /* End Validation rule */

        $path = $request->file('csv_file')->getRealPath();



             $data = Excel::load($path, function($reader) {})->ignoreEmpty()->get()->toArray();


         if (count($data) > 0) {
           // print_r($data);


                 $csv_header_fields = [];

                 $i = 0;
                 $icount = 0;
                 if(count($data[0]) <= count($data[1]) ){
                    $icount = 1;
                 }
                  foreach ($data   as   $first_sheet) {
                     if($i==$icount){

                       foreach ($first_sheet as $column_name => $column_value) {

                          $csv_header_fields[] = $column_name;
                       }
                     }

                     $i++;
                  }

                $csv_data = array_slice($data, 0, 3);

                $csv_data_file = CsvData::create([
                    'csv_filename' => $request->file('csv_file')->getClientOriginalName(),
                    'csv_header' => $request->has('header'),
                    'csv_data' => json_encode($data)
                ]);
            //$database_fields = (New Utilities)->tableFields;
            $database_fields = (New Programs)->tableFields;
          //  $zipcode_fields = (New Zipcodes)->tableFields;


           $client_id = $request->client_id;
            return view('client.utilities.import_fields', compact( 'csv_header_fields', 'csv_data', 'csv_data_file','database_fields','client_id','icount'));

         } else {
           die('<h1>No Data found</h1>');
         }

    }

    public function processImport(Request $request)
    {

        $client_id  = $request->client_id;


        $data = CsvData::find($request->csv_data_file_id);
        $database_fields = (new Utilities)->tableFields;
        $Programs_database_fields = (new Programs)->tableFields;
        $csv_data = json_decode($data->csv_data, true);


        $i = 0;


       $client_info = (new Client)->getClientinfo($client_id);


        foreach ($csv_data as $row) {

            if($i>=0){

                $data_array = array();
                $zip_data_array = array();
                $utility_data = array();



              $utility_data['client_id'] = $client_id;
              $utility_data['company'] = $client_info->name;
              $utility_data['created_by'] = Auth::user()->id;



                foreach ($request->fields as $index => $field) {

                        if( in_array( $field,$database_fields  ) ){
                            $db_filed  =  $request->fields[$index];
                            if(!empty($db_filed))
                             {
                               // $utility->$db_filed = $row[$index];
                               if(isset($row[$index])){
                                 $utility_data[$db_filed] = $row[$index];
                               }

                             }

                        }else{
                            if($index == 'commodity'){
                                //$utility->$index = $field;
                                $data_array[$index] = $field;
                            }else{
                                $db_filed  =  $request->fields[$index];
                                if(!empty($db_filed))
                                 {
                                   // $utility->$db_filed = $row[$index];
                                   if(isset($row[$index])){
                                     $data_array[$db_filed] = $row[$index];
                                   }

                                 }
                            }
                        }


                   }



                   $utility_data['client_id'] = $client_id;




                   $utility = (New Utilities)->getUtilityByAttributes($utility_data);






                   if(count($data_array) > 1){
                       if( !isset($data_array['code'])) {
                        $data_array['code'] = $utility_data['market'];
                       }
                     $data_array['utility_id'] = $utility ;
                     $data_array['client_id'] = $client_id ;
                     $data_array['created_by'] = Auth::user()->id ;
                     //print_r($data_array);
                   //  die();
                      $Programs =  (new Programs)::firstOrCreate( $data_array );

                 }




            }

            $i++;
        }


        $data = CsvData::find($request->csv_data_file_id);
        $data->delete();

        return redirect()->route('utilities.index',['client' => $client_id])
                ->with('success','Utility successfully imported.');
    }

    /**
     * This method is used to download utility sample file
     */
    public function downloadSample(Request $request) {
        $data =[[
            "Commodity" => "Electric",
            "Brand Name" => "Green Clean",
            "Utility Provider" => "Consolidated Edison",
            "Abbreviation" => "CONED",
            "Regex" => "^[\d]{15}$",
            "Regex Message" => "Utility Account # should be 15 characters long",
            "Zipcodes" => "10001, 10002, 10003, 10004",
            "Account Number Type" => "Account Number",
        ],
        [
            "Commodity" => "Gas",
            "Brand Name" => "Always Energy",
            "Utility Provider" => "Consolidated Edison",
            "Abbreviation" => "CONED",
            "Regex" => "^[\d]{15}$",
            "Regex Message" => "Utility Account # should be 15 characters long",
            "Zipcodes" => "10001, 10005, 10006, 10007, 10008",
            "Account Number Type" => "Utility Account Number",
        ],
        [

            "Commodity" => "Electric",
            "Brand Name" => "Always Energy",
            "Utility Provider" => "PPL Electric Utilities Corp",
            "Abbreviation" => "PPL",
            "Regex" => "^[\d]{10}$",
            "Regex Message" => "Utility Account # should be 10 characters long",
            "Zipcodes" => "18764, 18765, 18766, 18767, 18769",
            "Account Number Type" => "Customer Number",
        ]];

        return Excel::create('utility_sample', function($excel) use ($data) {
            $excel->sheet('sheet1', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download("csv");
    }

    /**
     * This method is used to export utility 
     */
    public function exportUtility(Request $request) {
        try {
            
            $client = Client::findOrFail($request->client_id);

            $clientName = str_replace(" ", "-", array_get($client, 'name'));

            if ($clientName != "") {
                $fileName = $clientName . "-" . "utilities";
            } else {
                $fileName = "utilities";
            }

            $fileName .= "-" . date('d_M_Y_H_i_A');

            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=". $fileName .".csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );

            $utilities = Utilities::leftJoin('brand_contacts','brand_contacts.id','=','utilities.brand_id')->select('utilities.*','brand_contacts.name')->where('utilities.client_id', $request->client_id)->get();
            // dd($utilities);
            $columns = array('Commodity', 'Brand Name', 'Utility Provider','Abbreviation', 'Regex', 'Regex Message', 'Zipcodes', 'Account Number Type');
            $callback = function() use ($utilities, $columns) {
                        $file = fopen('php://output', 'w');
                        fputcsv($file, $columns);

                        foreach($utilities as $utility) {
                            
                            $zipcode ='N/A';
                            if(!$utility->utilityZipcodes->isEmpty()) {
                                $zipcode =$utility->utilityZipcodes->pluck('zipCode.zipcode')->implode(', ');
                            }

                            if (!empty($utility->utilityCommodity())) {
                                $commodityName = $utility->utilityCommodity->name;
                            } else {
                                $commodityName = $utility->commodity;
                            }

                            fputcsv($file, array($commodityName, $utility->name, $utility->fullname, $utility->market, $utility->regex, $utility->regex_message, $zipcode, $utility->act_num_verbiage));
                        }
                        fclose($file);
                    };

            return \Response::stream($callback, 200, $headers);
        } catch(\Exception $e) {
            Log::info($e->getMessage());
            return redirect()->back()->with('error','Something went wrong, please try again.');
        }
    }

    /**
     * This method is used to import utility
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
            $data = Excel::load($path, function($reader) {
                $columns= [
                        "commodity",
                        "brand_name",
                        "utility_provider",
                        "abbreviation",
                        "regex",
                        "regex_message",
                        "zipcodes",
                        "act_num_verbiage",
                    ];
                $reader->select($columns);
            })
            ->ignoreEmpty()
            ->get()
            ->toArray();

            $errors = $validData = $rows =  array();
            if (empty($data)) {
                $errors[1][]='This file does not fit the correct format.';
                $view = view('client.errors.file-errors', compact('errors'))->render();
                return response()->json(['status' => 'dataErrors',  'errors' => $view], 422);
            }

            foreach ($data as $key => $utility_data) {
                if(!empty($utility_data['zipcodes'])) {
                    $utility_data['zipcodes'] = explode(',', str_replace(" ", "", $utility_data['zipcodes']));
                }
                $dataValidator = Validator::make($utility_data,
                    [
                        'commodity'          => 'required|max:255|exists:commodities,name,client_id,'.$client_id,
                        'brand_name'          => 'required|max:255|exists:brand_contacts,name,client_id,'.$client_id,
                        'utility_provider'          => 'required|max:255',
                        'abbreviation'          => 'required|max:255',
                        'regex'          => 'required',
                        'regex_message'          => 'required|max:255',
                        'zipcodes'          => 'required|exists:zip_codes,zipcode',
                        'act_num_verbiage' => 'nullable|max:255',
                    ]
                );
                if ($dataValidator->fails()) {
                    foreach ($dataValidator->messages()->all() as  $value) {
                        $errors[$key+1][] = $value;
                    }
                } else {
                    $row = json_encode($utility_data);
                    // check duplicate row in csv file
                    if (in_array($row, $rows)) {
                        $errors[$key+1][]="Duplicate row found in csv file.";
                    } else {
                        // check utility exist or not in database
                        $exist = $this->isExistUtility($client_id, $utility_data, $utility_data['zipcodes']);

                        if ($exist) {
                            $errors[$key+1][] = "This utility already exist.";
                        } else {
                            $index = $utility_data['commodity'] . "||" . $utility_data['brand_name'] . "||" . $utility_data['utility_provider'] . "||" . $utility_data['abbreviation']. "||" . $utility_data['regex']. "||" . $utility_data['regex_message'] . "||" . ((isset($utility_data['act_num_verbiage']) ? $utility_data['act_num_verbiage'] : ''));
                            if (!empty($validData) && in_array($index, array_keys($validData))) {
                                $validData[$index] = array_merge($validData[$index], $utility_data['zipcodes']);
                            } else {
                                $validData[$index] = $utility_data['zipcodes'];
                            }
                            $rows[] = $row;
                        }
                    }
                }
            }
            if (!empty($errors)) {
                $view = view('client.errors.file-errors', compact('errors'))->render();
                return response()->json(['status' => 'dataErrors',  'errors' => $view], 422);
            }

            if (!empty($validData)) {
                //Update or create utility records
                DB::transaction(function() use ($validData, $client_id){
                    foreach ($validData as $key => $data) {
                        $keyData = explode("||", $key);
                        
                        $commodity = Commodity::select('id')->where('name', $keyData[0])->where('client_id', $client_id)->first();
                        $brand_id = Brandcontacts::select('id')->where('client_id',$client_id)->where('name',$keyData[1])->first();
                        $insertData = ['client_id' => $client_id, 'brand_id' => $brand_id->id, 'commodity_id' => $commodity->id, 'fullname' => $keyData[2], 'market' => $keyData[3]];
                        $query = $insertData;
                        $insertData['regex'] = $keyData[4];
                        $insertData['regex_message'] = $keyData[5];
                        $insertData['created_by'] = Auth::id();
                        $insertData['brand_id'] = $brand_id->id;
                        $insertData['act_num_verbiage'] = isset($keyData[6]) ? $keyData[6] : '';
                        
                        $utility = Utilities::updateOrCreate($query, $insertData);
                        if(!empty($data)) {
                            $this->mergeUtilityZipCode($utility->id, $data);
                        }
                    }
                });
                $url = \URL::route('client.show',['id' => $client_id]). '#Utilities';
                // session()->put('success', 'Utilities successfully imported.');
                return response()->json(['status' => 'success',  'message' =>'Utilities  successfully imported.', 'url' =>$url], 200);
            } else {
                return response()->json(['status' => 'error',  'message' =>'Data not found to import.'], 500);
            }
        } catch(\Exception $e) {
            \Log::error('Error while utilities bulk upload:-'.$e);
            return response()->json(['status' => 'error',  'message' => $e->getMessage()], 500);
        }
    }

    /**
     * This method is used to check is exist utility
     */
    public function isExistUtility($clientId,$data,$zipcodes=[]) {
        try {
            $commodity = Commodity::select('id')->where('name', $data['commodity'])->where('client_id', $clientId)->first();
            $utility = Utilities::where('client_id', $clientId)->where('utilityname', $data['brand_name'])->where('commodity_id', $commodity->id)->where('fullname', $data['utility_provider'])->where('market', $data['abbreviation'])->first();
            if (!empty($utility)) {
                $zipcodeIds = Zipcodes::whereIn('zipcode',$zipcodes)->pluck('id');
                $utilitiesCount =UtilityZipcodes::where('utility_id',$utility->id)->whereIn('zipcode_id',$zipcodeIds)->count();
                return $utilitiesCount > 0;
            }
            return false;
        } catch (\Exception $e) {
            \Log::error($e);
            return false;
        }
    }

    /**
     * This method is used to show utility bulkupload
     */
    public function bulkupload($clientId)
    {
        $client = Client::active()->findOrFail($clientId);
        return view('client.utility_new.bulkupload',compact('client'));
    }

    /**
     * This method is used to get zipcodes
     */
    public function getZipCodes() {
        //$zipcodes =Zipcodes::select('zipcode')->distinct()->get()->pluck('zipcode');
        $zipcodes =Zipcodes::select('zipcode','city','state')->groupBy('zipcode')->get();
        return response()->json(['status'=>'success','data'=>$zipcodes]);
    }

    /**
     * This method is used to get utility by comodity
     */
    public function getUtilityByCommodity(Request $request){
        $utilities = Utilities::leftJoin('brand_contacts','brand_contacts.id','=','utilities.brand_id')->select('name')->where('commodity_id',$request->commodity_id)->distinct()->get();   
        return response()->json(['status'=>'success','data'=>$utilities]);
    }

    /**
     * This method is used to get provider by utilityname
     */
    public function getProviderByUtilityName(Request $request){
        $utilities = Utilities::leftJoin('brand_contacts','brand_contacts.id','=','utilities.brand_id')->select(DB::raw('concat( fullname, " (", market, ")") as fullnameMarket'),'fullname')
                ->where('brand_contacts.name',$request->utilityname)
                ->where('commodity_id',$request->commodity_id)
                ->whereNotNull('fullname')
                ->distinct()->get();
        return response()->json(['status'=>'success','data'=>$utilities]);
    }

    public function getMarketByProvider(Request $request){
        $utilities = Utilities::select('market')->where('fullname',$request->fullname)->whereNotNull('market')->distinct()->get();
        return response()->json(['status'=>'success','data'=>$utilities]);
    }

    /**
     * This method is used to search zipcode
     */
    public function zipcodeSearch(Request $request){
        $salesagentdetails = Salesagentdetail::where('user_id',\Auth::user()->id)->first();
        $zipcodes = Zipcodes::where('zipcode','LIKE',"$request->term%");
        if(strlen($salesagentdetails->restrict_state) > 0)
        {
            $explodeData = explode(",",$salesagentdetails->restrict_state);
            $zipcodes = $zipcodes->whereIn('state',$explodeData);
        }
        $zipcodes = $zipcodes->limit(10)->get(['zipcode','city','state']);
        // $zipcodes = Zipcodes::whereNotIn('state',$explodeData)->limit(10)->get(['zipcode','city','state']);
        return response()->json($zipcodes);
    }

    /**
     * This method is used to show validate bulkupload
     */
	public function validationsBulkUpload($clientId)
    {
        $client = Client::active()->findOrFail($clientId);
        return view('client.utility_new.validation-bulkupload',compact('client'));
    }

    /**
     * This method is used to download validation sample file
     */
    public function downloadValidationSample(Request $request) {
        $data =[[
            "Commodity" => "Electric",
            "Utility Name" => "Utility One",
            "Label" => "Name",
            "Regex" => "^[0-9]{7,14}$",
            "Regex Message" => "This is invalid pattern",
        ],[

            "Commodity" => "Gas",
            "Utility Name" => "Utility One",
            "Label" => "Email",
            "Regex" => "^[0-9]{7,14}$",
            "Regex Message" => "This is invalid pattern",
        ]];

        return Excel::create('utility_validation_sample', function($excel) use ($data) {
            $excel->sheet('sheet1', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download("csv");
    }

    /**
     * This method is used to validate utility import
     */
    public function validationImport(Request $request) {
	    $client_id = $request->client_id;

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

	    try {
	        $path = $request->file('upload_file')->getRealPath();

	        $data = Excel::load($path, function($reader) {
	            $columns= [
	                    "commodity",
	                    "utility_name",
	                    "label",
	                    "regex",
	                    "regex_message",
	                ];
	            $reader->select($columns);
	        })
	        ->ignoreEmpty()
	        ->get()
	        ->toArray();

	        $errors = $validData = $rows =  array();

	        if (empty($data)) {
	            $errors[1][]='This file does not fit the correct format.';
	            $view = view('client.errors.file-errors', compact('errors'))->render();
	            return response()->json(['status' => 'dataErrors',  'errors' => $view], 422);
	        }

	        foreach ($data as $key => $utility_data) {
                /* Start Validation rule */
	            $dataValidator = Validator::make($utility_data,
	                [
	                    'commodity'      => 'required|max:255|exists:commodities,name,client_id,'.$client_id,
	                    'utility_name'   => 'required|max:255',
	                    'label'          => 'required|max:255',
	                    'regex'          => 'required',
	                    'regex_message'  => 'required|max:255',
	                ]
	            );

	            if ($dataValidator->fails()) {
	                foreach ($dataValidator->messages()->all() as  $value) {
	                    $errors[$key+1][] = $value;
	                }
                /* End Validation rule */
	            } else {
	                $row = json_encode($utility_data);

	                // check duplicate row in csv file
	                if (in_array($row, $rows)) {
	                    $errors[$key+1][]="Duplicate row found in csv file.";
	                } else {
	                    // check utility exist or not in database
			            $commodity = Commodity::select('id')->where('name', $utility_data['commodity'])->where('client_id', $client_id)->first();
			            if(!$commodity){
			                $errors[$key+1][] = "This commodity does not exist.";
			                continue;
			            }
			            $utility = Utilities::where('client_id', $client_id)->where('fullname', $utility_data['utility_name'])->where('commodity_id', $commodity->id)->first();

	                    if (!$utility) {
	                        $errors[$key+1][] = "This utility does not exist.";
	                    } else {

	                        //$index = $client_id . "||" . $utility->id . "||" . $utility_data['label'] . "||" . $utility_data['regex']. "||" . $utility_data['regex_message'];
                            $validData[$key] = $utility_data;
                            $validData[$key]['client_id'] = $client_id;
                            $validData[$key]['utility_id'] = $utility->id;
		                    unset($validData[$key]['commodity']);
		                    unset($validData[$key]['utility_name']);
	                        $rows[] = $row;
	                    }
	                }
	            }
	        }
	        if (!empty($errors)) {
	            $view = view('client.errors.file-errors', compact('errors'))->render();
	            return response()->json(['status' => 'dataErrors',  'errors' => $view], 422);
	        }

	        if (!empty($validData)) {
	            //Update or create utility records
		        foreach ($validData as $key => $validation) {

			        $utilityValidations = UtilityValidation::updateOrCreate([
				        'utility_id' => $validation['utility_id'],
				        'label' => $validation['label'],
			        ],[
				        'client_id' => $validation['client_id'],
				        'utility_id' => $validation['utility_id'],
				        'label' => $validation['label'],
				        'regex' => $validation['regex'],
				        'regex_message' => $validation['regex_message'],
			        ]);

		        }
                //$utilityValidations = UtilityValidation::insert($validData);
	            $url = \URL::route('client.show',['id' => $client_id]). '#Utilities';
	            // session()->put('success', 'Utilities validations successfully imported.');
	            return response()->json(['status' => 'success',  'message' =>'Utilities  validations successfully imported.', 'url' =>$url], 200);
	        } else {
	            return response()->json(['status' => 'error',  'message' =>'Data not found to import.'], 500);
	        }
	    } catch(\Exception $e) {
	        \Log::error('Error while utilities validations bulk upload:-'.$e);
	        return response()->json(['status' => 'error',  'message' => $e->getMessage()], 500);
	    }
	}

    /**
     * This method is used to get validate utility list
     */
	public function getValidationsList(Request $request) {
		try {
	        $listUtilityValidations = UtilityValidation::leftjoin('utilities','utilities.id','=','utility_validations.utility_id')->select('utility_validations.*','utilities.fullname')->where('utility_validations.utility_id', $request->utility_id)->get();

			$listValidationHtml = view("client.utility_new.list-validations", compact('listUtilityValidations'))->render();

	        return response()->json(['status' => 'success', 'data' => $listValidationHtml], 200);
		} catch(\Exception $e) {
            \Log::error('Error while list utilities validations :-'.$e);
            return response()->json(['status' => 'error',  'message' => $e->getMessage()], 500);
        }
	}

    /**
     * This method is used to remove utility validation
     */
    public function deleteValidation(Request $request) {
        try {
            UtilityValidation::where('id',$request->id)->delete();
            return response()->json(['status' => 'success',  'message' =>'Utilities  validations successfully deleted.'], 200);
        } catch(\Exception $e) {
            \Log::error('Error while utilities validations deleting:-'.$e);
            return response()->json(['status' => 'error',  'message' => $e->getMessage()], 500);
        }
    }


    /**
     * This method is used to store utility validation
     */
    public function storeValidation(Request $request) {
        try {

			$utilityValidations = UtilityValidation::updateOrCreate([
		        'utility_id' => $request->utility_id,
		        'label' => $request->label,
	        ],[
		        'client_id' => $request->client_id,
		        'utility_id' => $request->utility_id,
		        'label' => $request->label,
		        'regex' => $request->regex,
		        'regex_message' => $request->regex_error_message,
	        ]);

            return response()->json([ 'status' => 'success',  'message'=>'Utility Validation successfully added.']);
        } catch(\Exception $e) {
            \Log::error('Error while utilities validations deleting:-'.$e);
            return response()->json(['status' => 'error',  'message' => $e->getMessage()], 500);
        }
    }
      
    /**
     * This method is used to mapping bulkupload
     */
    public function mappingsBulkUpload($clientId)
    {
        $client = Client::active()->findOrFail($clientId);
        return view('client.utility_new.mapping-bulkupload',compact('client'));
    }

    /**
     * This method is used to download utility mapping sample 
     */
    public function downloadMappingSample(Request $request) {
        $data =[[
            "Commodity" => "Electric",
            "Brand Name" => "Always Energy",
            "Utility Provider" => "PPL Electric Utilities Corp",
            "Abbreviation" => "PPL",
            "Mapped Commodity" => "Gas",
            "Mapped Brand Name" => "Always Energy",
            "Mapped Utility Provider" => "Consolidated Edison",
            "Mapped Abbreviation" => "CONED",
        ],[
            "Commodity" => "Gas",
            "Brand Name" => "Always Energy",
            "Utility Provider" => "Consolidated Edison",
            "Abbreviation" => "CONED",
            "Mapped Commodity" => "Electric",
            "Mapped Brand Name" => "Always Energy",
            "Mapped Utility Provider" => "PPL Electric Utilities Corp",
            "Mapped Abbreviation" => "PPL",
        ]];

        return Excel::create('utility_mapping_sample', function($excel) use ($data) {
            $excel->sheet('sheet1', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download("csv");
    }

    /**
     * This function is used to utility mapping import
     */
    public function mappingImport(Request $request) {
        $client_id = $request->client_id;

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

            $data = Excel::load($path, function($reader) {
                $columns= [
                        "commodity",
                        "brand_name",
                        "utility_provider",
                        "abbreviation",
                        "mapped_commodity",
                        "mapped_brand_name",
                        "mapped_utility_provider",
                        "mapped_abbreviation",
                    ];
                $reader->select($columns);
            })
            ->ignoreEmpty()
            ->get()
            ->toArray();
			
            $errors = $validData = $rows =   array();


            if (empty($data)) {
                $errors[1][]='This file does not fit the correct format.';
                $view = view('client.errors.file-errors', compact('errors'))->render();
                return response()->json(['status' => 'dataErrors',  'errors' => $view], 422);
            }

			$dataToSave = [];
            foreach ($data as $key => $utility_datar) {
				$utility_data=[];
				$utility_data = array_map('trim', $utility_datar);


                $dataValidator = Validator::make($utility_data,
                    [
                        'commodity'         => 'required|max:255|exists:commodities,name,client_id,'.$client_id,
                        'brand_name'        => 'required|max:255',
                        'utility_provider'  => 'required|max:255',
                        'abbreviation'      => 'required|max:255',
                        'mapped_commodity'  => 'required|max:255|exists:commodities,name,client_id,'.$client_id,
                        'mapped_brand_name' => 'required|max:255',
                        'mapped_utility_provider'  => 'required|max:255',
                        'mapped_abbreviation'=> 'required|max:255',
                        
                    ]
                );

                if ($dataValidator->fails()) {
                    foreach ($dataValidator->messages()->all() as  $value) {
                        $errors[$key+1][] = $value;
                    }
                } else {
                    $row = json_encode($utility_data);
					
					
                    // check duplicate row in csv file
                    if (in_array($row, $rows)) {
                        $errors[$key+1][]="Duplicate row found in csv file.";
                    } else {
                        // check utility exist or not in database
                        $commodity = Commodity::select('id')->where('name', $utility_data['commodity'])->where('client_id', $client_id)->first();
						
						if(!$commodity){
                            $errors[$key+1][] = "This commodity does not exist.";
							
                            continue;
                        }
                        /*$utility = Utilities::where('client_id', $client_id)->where('fullname', $utility_data['utility_provider'])->where('commodity_id', $commodity->id)->first();*/
                        //$brand_id = Brandcontacts::select('id')->where('client_id',$client_id)->where('name',$utility_data['brand_name'])->first();
						//if(!$brand_id){
							//$errors[$key+1][] = "This brand does not exist.";
							//continue;
						//}
						
						$utility = Utilities::leftjoin('brand_contacts','brand_contacts.id','=','utilities.brand_id')
							->select('utilities.*')
							->where('utilities.client_id', $client_id)
							->where('brand_contacts.name',$utility_data['brand_name'])
							//->where('brand_id', 'utilities.brand_id')
							->where('commodity', $utility_data['commodity'])
							->where('fullname', $utility_data['utility_provider'])
							->where('market', $utility_data['abbreviation'])
							->first();
						
                        if (!$utility) {
                            $errors[$key+1][] = "This utility does not exist.";
                        } else {
                            //$mapped_brand_id = Brandcontacts::select('id')->where('client_id',$client_id)->where('name',$utility_data['mapped_brand_name'])->first();
                            $mapped_utility = Utilities::leftjoin('brand_contacts','brand_contacts.id','=','utilities.brand_id')
							->select('utilities.*')
							->where('utilities.client_id', $client_id)
							->where('brand_contacts.name',$utility_data['mapped_brand_name'])
							//->where('brand_id', $mapped_brand_id->id)
							->where('commodity', $utility_data['mapped_commodity'])
							->where('fullname', $utility_data['mapped_utility_provider'])
							->where('market', $utility_data['mapped_abbreviation'])
							->first();
                            if (!$mapped_utility) {
                                $errors[$key+1][] = "This mapped utility does not exist.";
                            } else {
                                // create data for submitting
                                // $validData[$key] = $utility_data;
                                
								$validData[]=array(
								'utility_id'=>$utility->id,
								'mapped_utility_id' => $mapped_utility->id
								);
								
								//$utitlityDbcheck=DB::table('utility_mappings')->where('utility_id',$utility->id)->get();
								
								
								if(!empty($dataToSave)){
									if(array_key_exists($utility->id,$dataToSave)){
											$rel=$dataToSave[$utility->id];
											$relarry=explode(",",$rel);
											if(!empty($relarry) && in_array($mapped_utility->id,$relarry)){
												
											}else{
												$relarry[]=$mapped_utility->id;
												$dataToSave[$utility->id]=implode(',',$relarry);
											}
												
									}else{
										$dataToSave[$utility->id]=$mapped_utility->id;
									}
									
									if(array_key_exists($mapped_utility->id,$dataToSave)){
											$rel1=$dataToSave[$mapped_utility->id];
											$relarry1=explode(",",$rel1);
											if(!empty($relarry1) && in_array($utility->id,$relarry1)){
												
											}else{
												$relarry1[]=$utility->id;
												$dataToSave[$mapped_utility->id]=implode(',',$relarry1);
											}
												
									}else{
										$dataToSave[$mapped_utility->id]=$utility->id;
									}
									
									
								}else{
										$dataToSave[$utility->id]=$mapped_utility->id;
										$dataToSave[$mapped_utility->id]=$utility->id;
								}
								
								/*$dataToSave[$utility->id]=$mapped_utility->id;
								$dataToSave[$mapped_utility->id]=$mapped_utility->id;*/
								//print_r($allData);
								
								//die('my side');
									
									
									
								
								
								// cross data
                                $validData[]=array(
								'utility_id'=>$mapped_utility->id,
								'mapped_utility_id' => $utility->id
								);
								
                                /*$validData[$key]['utility_id'] = $utility->id;
                                $validData[$key]['mapped_utility_id'] = $mapped_utility->id;

                                // cross data
                                $validData[$key]['utility_id'] = $mapped_utility->id;
                                $validData[$key]['mapped_utility_id'] = $utility->id; */

                                $rows[] = $row;
                            }
                        }
                    }
                }
            }
            if (!empty($errors)) {
                $view = view('client.errors.file-errors', compact('errors'))->render();
                return response()->json(['status' => 'dataErrors',  'errors' => $view], 422);
            }

            if (!empty($validData)) {
                //Update or create utility records
				
				//print_r($validData);
				//print_r($dataToSave);
					foreach($dataToSave as $exkey=>$exvalue){
						if(DB::table('utility_mappings')->where('utility_id', $exkey)->exists()){
							$mappedRes=DB::table('utility_mappings')->where('utility_id', $exkey)->first();
							$mappedUti=explode(',',$mappedRes->mapped_utility_id);
							$armpped=explode(',',$exvalue);
							$finUpd = array_unique(array_merge($mappedUti,$armpped));
							
							$affected = DB::table('utility_mappings')
							  ->where('utility_id', $exkey)
							  ->update(['mapped_utility_id' => implode(',',$finUpd)]);
							
						}else{
							
							DB::table('utility_mappings')->insert([
							['utility_id' => $exkey, 'mapped_utility_id' => $exvalue]
								]);
							
						}
					
					}
				
				
				//die('Here'); 
                //$utilityMappings = UtilityMapping::insert($validData);
                $url = \URL::route('client.show',['id' => $client_id]). '#Utilities';
                session()->put('success', 'Utilities mapping successfully imported.');
                return response()->json(['status' => 'success',  'message' =>'Utilities  mappings successfully imported.', 'url' =>$url], 200);
            } else {
                return response()->json(['status' => 'error',  'message' =>'Data not found to import.'], 500);
            }
        } catch(\Exception $e) {
            \Log::error('Error while utilities mappings bulk upload:-'.$e);
            return response()->json(['status' => 'error',  'message' => $e->getMessage()], 500);
        }
    }
	


    /**
     * This method is used to get utility mapping list
     */
    public function getMappingsList(Request $request) {
        try {
            $currentUtility =  (New Utilities)->getUtility($request->utility_id);
            // print_r($currentUtility->toArray());
            $listUtility = Utilities::leftjoin('brand_contacts','brand_contacts.id','=','utilities.brand_id')
                ->leftjoin('salescenters_brands','salescenters_brands.brand_id','=','brand_contacts.id')
                ->leftjoin('commodities','utilities.commodity_id','=','commodities.id')
                ->select('utilities.id as utid',
                         'fullname',
                         'market',
                         'brand_contacts.name as brand_name',
                         'commodity_id', 
                         'commodities.name as commodity',
                          DB::raw('"'.$request->utility_id. '" as utility_id'),
                DB::raw('IF((SELECT id FROM utility_mappings WHERE utility_id="'.$request->utility_id.'" AND FIND_IN_SET(utilities.id, utility_mappings.mapped_utility_id)) > 0 , "checked" , "unchecked" ) AS action')
                )
                ->where('utilities.client_id',$currentUtility->client_id)
                ->where('commodity_id','NOT LIKE',$currentUtility->commodity_id)
                // ->whereHas('utilityZipcodes', function($q) use ($zipcodeIds) {
                //     $q->whereIn('zipcode_id', $currentUtility->zipcodeIds);
                // })
                ->distinct()
                ->get()
                ;
                // dd($listUtility->toSql(), $listUtility->getBindings());
                // sdie('My change');
            $listMappingHtml = view("client.utility_new.list-mappings", compact('listUtility'))->render();
            // print_r($listUtility->toArray());
            return response()->json(['status' => 'success', 'data' => $listMappingHtml], 200);
        } catch(\Exception $e) {
            \Log::error('Error while list utilities mapping :-'.$e);
            return response()->json(['status' => 'error',  'message' => $e->getMessage()], 500);
        }
    }

    /**
     * This method is used to update utility mapping
     */
    public function updateMapping(Request $request) {

        try {
            $has_utility = UtilityMapping::where('utility_id',$request->utility_id)->get();
            $has_utility_map = UtilityMapping::where('utility_id',$request->mapped_utility_id)->get();
            $message = '';
            if ($request->checked == 'true') {
                //add mapping
                $input_ut = [];
                $q_ut = [];
                $q_ut['id'] = null;
                $input_ut['utility_id'] = $request->utility_id;
                if(!empty($has_utility->toArray())) {                
                    $has_mapping = $has_utility->toArray();
                    $mapping_list = explode(',',$has_utility[0]['mapped_utility_id']);
                    if (($key = array_search($request->mapped_utility_id, $has_mapping)) !== false) {
                        //record exist
                        $message = "Utilities mapping already exist.";
                    } else {
                        // update record
                        $mapping_list[] = $request->mapped_utility_id;
                        $input_ut['mapped_utility_id'] = implode(',', $mapping_list); 
                        $q_ut['id'] = $has_mapping[0]['id'];
                        UtilityMapping::updateOrCreate($q_ut, $input_ut);                       
                    } 
                    // UtilityMapping::updateOrCreate($q, $input_ut);
                } else {
                    //create record
                    $input_ut['mapped_utility_id'] = $request->mapped_utility_id;
                    $createdUtilityMapping = UtilityMapping::create($input_ut); 
                    if(!$createdUtilityMapping->id){
                        $message = "Utility Mapping cannot be created";
                    }
                }

                // cross record for mapped utility
                $input_mut = [];
                $q_mut = [];
                $q_mut['id'] = null;
                $input_mut['utility_id'] = $request->mapped_utility_id;
                if(!empty($has_utility_map->toArray())) {
                    $has_mapping_sub = $has_utility_map->toArray();
                    $mapping_list = explode(',',$has_utility_map[0]['mapped_utility_id']);
                    if (($key = array_search($request->utility_id, $has_mapping_sub)) !== false) {
                        //record exist
                        $message = "Utilities mapping already exist.";
                    } else {
                        // update record
                        $mapping_list[] = $request->utility_id;
                        $input_mut['mapped_utility_id'] = implode(',', $mapping_list); 
                        $q_mut['id'] = $has_mapping_sub[0]['id'];
                        UtilityMapping::updateOrCreate($q_mut, $input_mut);
                    }
                    
                } else {
                    //create record
                    $input_mut['mapped_utility_id'] = $request->utility_id;
                    $createdUtilityMapping = UtilityMapping::create($input_mut); 
                    if(!$createdUtilityMapping->id){
                        $message = "Utility Mapping cannot be created";
                    }
                }

            } else if ($request->checked == 'false') {
                //remove mapping
                $input_ut = [];
                $q_ut = [];
                $q_ut['id'] = null;
                $input_ut['utility_id'] = $request->utility_id;
                if(!empty($has_utility->toArray())) {
                        
                    $has_mapping = $has_utility->toArray();
                    $mapping_list = explode(',',$has_mapping[0]['mapped_utility_id']);

                    if (($key = array_search($request->mapped_utility_id, $mapping_list)) !== false) {
                        unset($mapping_list[$key]);
                        if (empty($mapping_list)) {
                            //remove if only data
                            UtilityMapping::where('id',$has_mapping[0]['id'])->delete();
                        } else {
                            // update record
                            $input['mapped_utility_id'] = implode(',', $mapping_list); 
                            $q['id'] = $has_mapping[0]['id'];
                            UtilityMapping::updateOrCreate($q, $input);
                        }
                    } else {
                        //record not found
                        $message = "Utilities not mapped.";
                    }
                }

                $input = [];
                $q = [];
                $q['id'] = null;
                $input['utility_id'] = $request->mapped_utility_id;
                if(!empty($has_utility_map->toArray())) {
                        
                    $has_mapping_sub = $has_utility_map->toArray();
                    $mapping_list = explode(',',$has_mapping_sub[0]['mapped_utility_id']);
                    // $mapping_list[] = $request->utility_id;                        
                    if (($key = array_search($request->utility_id, $mapping_list)) !== false) {
                        unset($mapping_list[$key]);
                        if (empty($mapping_list)) {
                            //remove if only data
                            UtilityMapping::where('id',$has_mapping_sub[0]['id'])->delete();
                        } else {
                            // update record
                            $input['mapped_utility_id'] = implode(',', $mapping_list); 
                            $q['id'] = $has_mapping_sub[0]['id'];
                            UtilityMapping::updateOrCreate($q, $input);
                        }
                    } else {
                        //record not found
                        $message = "Utilities not mapped.";
                    }
                }

            } else {
                return response()->json(['status' => 'error',  'message' =>'Utility update status not found.'], 500);
            }
            // die('before return');
            // var_dump($message);
            if ($message == '') {
                $message = 'Utilities mapping successfully updated.';
                return response()->json(['status' => 'success',  'message' =>$message], 200);
            } else {
                return response()->json(['status' => 'error',  'message' =>$message], 200);
            }
        } catch(\Exception $e) {
            \Log::error('Error while listing utilities mapping :-'.$e);
            return response()->json(['status' => 'error',  'message' => $e->getMessage()], 500);
        }
    }

    public function getUtilityForOtherCommodity(Request $request){
        $utilities = Utilities::leftJoin('brand_contacts','brand_contacts.id','=','utilities.brand_id')->select('name')->whereNotIn('commodity_id',[$request->commodity_id])->get();
        // print_r($utilities);
        // die('heeeee');
        return response()->json(['status'=>'success','data'=>$utilities]);
    }


}


