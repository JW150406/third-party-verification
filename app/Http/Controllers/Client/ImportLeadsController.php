<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Role;
use App\models\Client;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CsvImportRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\models\Clientsforms;
use App\models\CsvData;


class ImportLeadsController extends Controller
{
	public $leadFields = array();
	public $mapping_fields = array( 'utility','MarketCode','Program Code', 'etf', 'msf', 'term', 'rate','Account Number', 'Program','Brand','Commodity','agentID');
    public function __construct()
    {
        $this->middleware('auth');
    }

	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

         $utilities = array();
         $client_id = "";
         $search_text = "";
         if(isset($request->client_id)){
            $client_id = $request->client_id;
             
             if(isset($request->search_text) && $request->search_text !=" "){
                   $search_text = $request->search_text;
             }
              
         }
        if(Auth::user()->access_level =='client'){
            $client_id = Auth::user()->client_id;
        }

         $client = (new Client)->getClientinfo($client_id);
      
        
         $clients = (new Client)->getClientsList();
         return view('client.leadimport.selectfile',compact('client_id','clients','search_text','client'))
             ->with('i', ($request->input('page', 1) - 1) * 20);

    }

    public function mapleadfields(Request $request){
    	if ($request->isMethod('post')) {
			 $client_id = $request->client_id;
             $client = (new Client)::findOrFail($client_id);
             $this->getFormFields($client_id); 
             asort($this->mapping_fields);	
             
             $database_fields = $this->mapping_fields;

     		 
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

                     //   dd($csv_data);

			            $csv_data_file = CsvData::create([
			                'csv_filename' => $request->file('csv_file')->getClientOriginalName(),
			                'csv_header' => $request->has('header'),
			                'csv_data' => json_encode($data)
			            ]);
		 			return view('client.leadimport.import_fields', compact( 'csv_header_fields', 'csv_data', 'csv_data_file','database_fields','client_id'));
			        } else {
			            return redirect()->back();
			        }
    		}else{
    			die('Invalid Request');
    		}

    	 


    }

	/**
	 * This function is used to process import lead
	 */
	public function processImport(Request $request)
	{

	
	    $data = CsvData::find($request->csv_data_file_id);
	    $client_id  = $request->client_id;
	    $this->getFormFields($client_id); 
	   


	    $database_fields = $this->mapping_fields;
	    $csv_data = json_decode($data->csv_data, true);
	    
	    
	    $i = 0;
	    //	dd($request->fields);

	//     echo "<pre>";
	    echo "<pre>";
  //print_r($database_fields);
	//    print_r($request->fields);
	    foreach ($csv_data as $row) {

	        if($i>0){

	            $data_array = array();
	           // $program = new Programs();
	            foreach ($request->fields as $index => $field) {

	                if ($data->csv_header) {
	                   $data_array[$field] = $row[$request->fields[$field]];
	                } else {
	                      $db_filed  =  $request->fields[$index];
	                    //  print_r($db_filed);
	                      if(in_array($db_filed,$database_fields)){
	                      	 ///print_r($row);
	                         if(isset($row[$index])){
	                          // $program->$db_filed = $row[$index];
	                           $data_array[$db_filed] = $row[$index];
	                         }
	                        // $data_array[$db_filed] = $row[$index];
	                      }

	                }

	            }
				dd($data_array);

	          //  print_r($data_array);
	          // if(isset($data_array['name']) && !empty($data_array['name']) ){
	          //   $data_array['client_id'] = $client_id;
	          //   $program =  (new Programs)::firstOrCreate( $data_array );
	          //   $program->created_by = Auth::user()->id;
	          //   $program->save();  
	          // }


	        }

	        $i++;
	    }
	    


	    //$data = CsvData::find($request->csv_data_file_id);
	   // $data->delete();

	    return redirect()->route('utility.programs',['client' => $client_id, 'utility' => $utility_id ])
	            ->with('success','Program imported successfully');
	}



	/**
	 * This function is used to get formfield
	 */
    public function getFormFields($client_id){
    	 $ClientFields =  (new Clientsforms)->getClientFormByCommodityType("GasOrElectric", $client_id);
     		 if($ClientFields){
     		 	foreach ($ClientFields as $single_form) {
     		 		  $fields = json_decode($single_form->form_fields);
     		 		  if(count($fields) > 0) {
     		 		  	foreach ($fields as  $single_field) {
     		 		  		if( !empty($single_field->label_text) &&  $single_field->type != "heading" ){
     		 		  			 if( !in_array($single_field->label_text, $this->mapping_fields)){
     		 		  			 	$fields = getFieldsLableForDisplay($single_field->label_text, $single_field->type);
     		 		  			    // echo "<pre>"; print_r($single_field);
     		 		  			 	$this->mapping_fields = array_merge($this->mapping_fields, $fields);
     		 		  			 }     
                              }
     		 		  	}
     		 		  }

     		 		  
     		 	}
     		 }

    }


}
