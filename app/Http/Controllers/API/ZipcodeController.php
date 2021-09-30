<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Zipcodes;
use App\models\UtilityZipcodes;
use App\models\Utilities;
use App\models\Client;
use App\models\Clientsforms;
use App\models\Programs;
use App\models\Salesagentdetail;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CsvImportRequest;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use App\models\CsvData;

class ZipcodeController extends Controller
{
    /**
     * This method is used for check entered zipcode is correct or not
     * @param $request
     */
    public function validatezip(Request $request){
        $response  = array();
        $commodities = $request->get('commodity');
        if(is_array($commodities)) {
            $commodities = $commodities;  
        } else {
            $commodities = explode(',', $commodities);
        }

        if(isset($request->zipcode) && !empty($request->zipcode)){
            $response =   (new Zipcodes)->FindZip($request->zipcode);
            if($response ){

                $zipcodeId = array($response->id);
                $getutilites_by_state_from_programs = $this->getUtility($commodities,$zipcodeId);
                $total = count($getutilites_by_state_from_programs);
                if( $total> 0) {
                    $response  = array(
                        'status' => 'success',
                        'message' => 'success',
                        'totalrecords' => $total,
                        'state' => $response->state,
                        'city' => $response->city,
                        'county' => $response->county,
                        'zipcode' => $response->zipcode,
                        'data' => $getutilites_by_state_from_programs
                    );

                }else{
                    $response  = array(
                        'status' => 'error',
                        'message' => 'Utiltiy not found! Please try another zipcode' 
                    );
                }
            }else{
                $response  = array(
                    'status' => 'error',
                    'message' => "Invalid zipcode"
                );
            }
        }else if (isset($request->state) && !empty($request->state)) {
            $state = $request->state;
            $zipcodeIds = Zipcodes::where('state',$state)->pluck('id');

            $utilities = $this->getUtility($commodities, $zipcodeIds);
            $total = count($utilities);
            if($total > 0) {
                $response  = array(
                    'status' => 'success',
                    'message' => 'success',
                    'totalrecords' => $total,
                    'state' => $state,
                    'city' => null,
                    'county' => null,
                    'zipcode' => null,
                    'data' => $utilities
                );

            }else{
                $response  = array(
                    'status' => 'error',
                    'message' => 'Utiltiy not found! Please try another state' 
                );
            }            
        } else {
            $response  = array(
                'status' => 'error',
                'message' => "Invalid zipcode"
            );
        }
        return \Response::json($response);
    }

    /**
     * For get utility by commodity and zipcode
     */
    public function getUtility($commodities, $zipcodeIds) {
        // $utility = Utilities::select('id as utid','utilityname','market','commodity','commodity_id', 
        //     \DB::raw('concat( CASE 
        //         WHEN fullname IS NULL  
        //         THEN market
        //         ELSE fullname
        //         END , " (", utilityname, ")")as fullname'))
        // ->whereIn('commodity_id',$commodities)
        // ->whereHas('utilityZipcodes', function($q) use ($zipcodeIds) {
        //     $q->whereIn('zipcode_id', $zipcodeIds);
        // })->whereHas('programs', function($q){
        //     $q->where('status','active');
        // })->get();
        $utility = Utilities::leftjoin('brand_contacts','brand_contacts.id','=','utilities.brand_id')->leftjoin('salescenters_brands','salescenters_brands.brand_id','=','brand_contacts.id')->select('utilities.id as utid','brand_contacts.name as utilityname','market','commodity_id', 
                    \DB::raw('concat( CASE 
                        WHEN fullname IS NULL  
                        THEN market
                        ELSE fullname
                        END , " (", name, ")")as fullname'),
                        \DB::raw('(select name from commodities where id = commodity_id) as commodity'))
        ->whereIn('commodity_id',$commodities)
        ->where('salescenters_brands.salescenter_id',Auth::user()->salescenter_id)
        ->whereHas('utilityZipcodes', function($q) use ($zipcodeIds) {
            $q->whereIn('zipcode_id', $zipcodeIds);
        })->whereHas('programs', function($q){
            $q->where('status','active');
        })->get();

        return $utility;
    }

    /**
     * Ajax method for find and check zipcode
     */
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

    /**
     * This method is used for het list of zipcode
     */
     public function getzipcodeslist(Request $request){
        if(isset($request->zipcode) ) {
            $salesagentdetails = Salesagentdetail::where('user_id',\Auth::user()->id)->first();            
            // $explodeData = [];
            // if(strlen($salesagentdetails->restrict_state) > 0)
            // {
            //     $explodeData = explode(",",$salesagentdetails->restrict_state);
            // }
            // $explodeData = explode(",",$salesagentdetails->restrict_state);
            // $data = (new Zipcodes)->searchzip($request->zipcode,$explodeData);
            $data = Zipcodes::where('zipcode','LIKE',"$request->zipcode%");
            if(strlen($salesagentdetails->restrict_state) > 0)
            {
                $explodeData = explode(",",$salesagentdetails->restrict_state);
                $data = $data->whereIn('state',$explodeData);
            }
            $data = $data->limit(10)->get(['zipcode','city','state']);
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
           //$res = json_encode($response);
           echo  json_encode(
               array ('status' => 'success',
               'message' => 'success',
               'data' => $response )
           );
        } else{
            echo  json_encode( array(
                'status' => 'error',
                'message' =>"Invalid Request"
                )
                
            );
        }
           
    }

    /**
     * for get utility states
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStates(Request $request) {
        try {
            $validator = \Validator::make($request->all(), [
                'form_id' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->error("error", implode(',',$validator->messages()->all()), 500); 
            }

            $form = Clientsforms::find($request->form_id);

            if (empty($form)) {
                return $this->error("error", "Form not found !!",400);
            }

            $data = $this->getUtilityStates($form->commodities->pluck('id'));

            return $this->success("success", "success", $data);            
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->error("error", "Something went wrong, Please try again later !!", 500);
        }
    }
}