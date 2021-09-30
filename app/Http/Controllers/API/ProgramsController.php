<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\SalescentersBrands;
use App\models\Utilities;
use App\models\Client;
use App\models\CsvData;
use App\models\Programs;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CsvImportRequest;
use Maatwebsite\Excel\Facades\Excel;


class ProgramsController extends Controller
{
    /**
     * For get list of client's utility
     */
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
             'message' => 'success',
             'options' =>  $res_options,
         );
         return \Response::json($response);
    }
   

    /**
     * This method is used for get programs from utilities
     */
 public function getProgramsFormUtility(Request $request){
    if(isset($request->utility_id)){
        $utility = Utilities::with('utilityCommodity')->find($request->utility_id);
        $salescenterBrand = SalescentersBrands::where('salescenter_id',auth()->user()->salescenter_id)->where('brand_id',array_get($utility,'brand_id'))->with('restrictProg')->first();

        // for get restrcted program
        if (!empty($salescenterBrand->restrictProg)) {
            $restrictProg = $salescenterBrand->restrictProg->pluck('program_id')->toArray();
        } else {
            $restrictProg = [];
        }

        $programs = (new Programs)->getAllProgramsAPI("",$request->utility_id, $restrictProg);         

        if(count($programs) > 0) {
            // 'number_of_records' => $data->count(),
            // 'current_page' => $data->currentPage(),
            // 'perpage' => $data->perPage(),
            // 'total' => $data->total(),

            $customFields = $disableFields = [];
            if (isset($programs['data'][0])) {
                $customFields = getEnableCustomFields(array_get($programs['data'][0],'client_id'));
                $disableFields = getDisableCustomFields(array_get($programs['data'][0],'client_id'));
            }

            // added enable custom fields
            if (!empty($customFields)) {
                foreach ($programs['data'] as $key => $program) {
                    $fields = [];
                    foreach ($customFields as $k => $customField) {
                        $fields[] = [
                            'label' => $customField,
                            'value' => $program[$k] ? $program[$k] : ''
                        ];
                        unset($program[$k]);
                    }
                    if (!empty($disableFields)) {
                        foreach ($disableFields as $disableField) {                                                    
                            unset($program[$disableField]);
                        }
                    }
                    $program['custom_fields'] = $fields;
                    $programs['data'][$key]= $program;
                }
            }

            $programs['status'] = 'success';
            $programs['message'] = 'success';
            $programs['totalrecords'] = $programs['total'];

            if(isset($programs['last_page_url'] )){
                unset($programs['last_page_url']);
            }
            if(isset($programs['next_page_url'] )){
                unset($programs['next_page_url']);
            }
            if(isset($programs['path'] )){
                unset($programs['path']);
            } 
            if(isset($programs['prev_page_url'] )){
                unset($programs['prev_page_url']);
            }  
            if(isset($programs['first_page_url'] )){
                unset($programs['first_page_url']);
            } 

            $response  = $programs;

        }else{
            $response  = array(
                'status' => 'error',
                'message' => 'No program found' 
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
}
