<?php

namespace App\Http\Controllers\Utility;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Zipcodes;
use App\models\UtilityZipcodes;
use App\models\Utilities;
use App\models\Client;
use App\models\Programs;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CsvImportRequest;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use App\models\CsvData;

class ZipcodeController extends Controller
{
    /**
     * This method is used to validate zipcode
     */
     public function validatezip(Request $request){

            $response  = array();
            if(isset($request->zipcode)){
                  $response =   (new Zipcodes)->FindZip($request->zipcode);
                  if($response ){
                   if($request->commodity == 'Dual Fuel'){
                       $commodity = array('Gas','Electric');
                   }else{
                    $commodity = array($request->commodity);
                   }
                    $getutilites_by_state_from_programs = (new Programs)->geUtilities($request->client_id,$response->state, $commodity );
                     if(count($getutilites_by_state_from_programs) > 0) {
                        $response  = array(
                            'status' => 'success',
                            'totalrecords' => count($getutilites_by_state_from_programs),
                            'state' => $response->state,
                            'city' => $response->city,
                            'county' => $response->county,
                            'zipcode' => $response->zipcode,
                            'data' => $getutilites_by_state_from_programs
                         );

                     }else{
                        $response  = array(
                            'status' => 'error',
                            'message' => 'Utiltiy not found in '.$response->city.'. Please try another zipcode' 
                         );
                     }

                        
                    
                  }else{
                    $response  = array(
                        'status' => 'error',
                        'message' => "Invalid zipcode"
                     );
                  }
              }else{
                $response  = array(
                    'status' => 'error',
                    'message' => "Invalid zipcode"
                 );
              }
             return \Response::json($response);
     }
     public function ajaxzipcode(Request $request){

        $response  = array();
        if(isset($request->find)){
              $response =   (new Zipcodes)->FindZipAutocomplete($request->find);
              if(count($response)>0){
                    $res = array();
                    foreach($response as $zipcodes){
                          $res[] = $zipcodes->zipcode;
                    }
                    $response = $res;
              }
          }
         return \Response::json($response);
 }

     public function mapUtility(Request $request){
         $inputs = $request->all();
          if(isset($inputs['selectzipcode'])){
                $utility_id = $request->id;
                $zipcodes = explode(',',$inputs['selectzipcode']);
                if(count($zipcodes) > 0){
                       foreach($zipcodes as $zip){
                             $get_zip_id = (new Zipcodes)->FindZipid($zip);
                             if($get_zip_id){
                                     (new UtilityZipcodes)::firstOrCreate(array('zipcode_id' => $get_zip_id->id , 'utility_id' => $utility_id ));
                             }
                       }
                       return redirect()->back()
                      ->with('success','Zipcode successfully added.');

                }else{
                  return redirect()->back()->withErrors(['msg', 'Invalid Request']);
                }
                

          }else{
            return redirect()->back()->withErrors(['msg', 'Invalid Request']);
          }
     }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(Request $request)
     {
         $utilities = array();
         $client_id = "";
         $search_text = "";
         if(isset($request->client)){
             $client_id = $request->client;
             
             if(isset($request->search_text) && $request->search_text !=" "){
                   $search_text = $request->search_text;
             }
              
         }
        if(Auth::user()->access_level =='client'){
            $client_id = Auth::user()->client_id;
        }
         if(  $client_id !=""){
            $utilities = (New Utilities)->getClientUtilities($client_id,$search_text );
         }
        
         $clients = (new Client)->getClientsList();
         return view('client.utilities.zipcodes.index',compact('utilities','client_id','clients','search_text'))
             ->with('i', ($request->input('page', 1) - 1) * 20);
     }

     /**
      * This method is used to import zipcode
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
             ],
             [
                  'client' => 'required'
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
            //  dd($data);

             
 
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
            // $database_fields = (New Programs)->tableFields;
            $database_fields = (New Zipcodes)->tableFields;
            
 
            $client_id = $request->client;
             return view('client.utilities.zipcodes.import_fields', compact( 'csv_header_fields', 'csv_data', 'csv_data_file','database_fields','client_id','icount'));
 
          } else {
            die('<h1>No Data found</h1>');
          }
 
          
     }
     public function processImport(Request $request)
     {
        
         $client_id  = $request->client_id;
  
      
         $data = CsvData::find($request->csv_data_file_id);
         $database_fields = (new Utilities)->tableFields;
         $zip_database_fields = (New Zipcodes)->tableFields;
         $csv_data = json_decode($data->csv_data, true);
         
 
         $i = 0;
    //   echo "<pre>";
   
      // print_r($request->fields);
 //       print_r($request->zipcode_fields);
        //die();
        
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
                    //$utility = (New Utilities)->getUtilityByAttributes($utility_data);

                    
                   
                  
                   
                   
                  
                  

 
                    if(count($data_array) > 1){
                        if( isset($utility_data['commodity_type']) && $utility_data['commodity_type'] == 'Dual'){
                            $utility_data['commodity'] = 'Electric';
                            $utility = (New Utilities)->getUtilityByAttributes($utility_data);
                   
                            $zipid = (New Zipcodes)->getzipcodeId($data_array);
                            (new UtilityZipcodes)::firstOrCreate(array('zipcode_id' => $zipid , 'utility_id' => $utility ));

                            $utility_data['commodity'] = 'Gas';
                            $utility = (New Utilities)->getUtilityByAttributes($utility_data);
                   
                            $zipid = (New Zipcodes)->getzipcodeId($data_array);
                            (new UtilityZipcodes)::firstOrCreate(array('zipcode_id' => $zipid , 'utility_id' => $utility ));

                        }else{
                            $utility_data['commodity_type'] = 'Single';
                            $utility = (New Utilities)->getUtilityByAttributes($utility_data);
                   
                            $zipid = (New Zipcodes)->getzipcodeId($data_array);
                            (new UtilityZipcodes)::firstOrCreate(array('zipcode_id' => $zipid , 'utility_id' => $utility ));
                        }
                      
                      
                       
 
                  }
 
 
 
 
             }
 
             $i++;
         }
 
  
         $data = CsvData::find($request->csv_data_file_id);
         $data->delete();
 
         return redirect()->route('utility.importzip',['client' => $client_id])
                 ->with('success','Zipcode imported successfully');
     }

     /**
      * This method is used to show get zipcode list
      */
     public function getzipcodeslist(Request $request){
        if(isset($request->term) && isset($request->callback)) {
            $data = (new Zipcodes)->searchzip($request->term);
            $response = array();
            if(count($data) > 0 ){
                foreach($data as $zipdata){
                    if($zipdata->city !='NULL'){
                        $city = $zipdata->city." ";
                    }else{
                        $city = "";
                    }
                    $response[] = array('zipcode' => $zipdata->zipcode, 'label' => $zipdata->zipcode." ".$city.$zipdata->state, 'value' => $zipdata->zipcode,  );    
                }
            }
           $res = json_encode($response);
           echo $request->callback."(".$res.")";
        } else{
            echo  json_encode(['Invalid Request']);
        }
           
      }
}
