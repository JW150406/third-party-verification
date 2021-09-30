<?php

namespace App\Http\Controllers\FraudAlert;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\FraudAlert;
use App\models\Salescenter;
use App\models\Salescenterslocations;
use Auth;

class FraudAlertController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $emailAlert = FraudAlert::where('type','=','email')->get();
        $smsAlert = FraudAlert::where('type','=','phone')->get();
        return view('fraud_alerts.index',compact('emailAlert','smsAlert'));
    }

    /**
     * This method is used to find salescenter
     */
    public function findSalesCenter(Request $request)
    {
        $data = [];
        if($request->alertLevel == 'salescenter' || $request->alertLevel == 1)
        {
            $data["salescenters"] = (new Salescenter)->getSalesCentersListByClientID($request->clientId);
            $data["selectedsalescenters"] = FraudAlert::whereId($request->fid)->pluck('salescenter_id');
        }
        elseif($request->alertLevel == 'sclocation' || $request->alertLevel == 2)
        {
            $data["salescenters"] = (new Salescenterslocations)->getLocationsList($request->clientId);
            $data["selectedsalescenters"] = FraudAlert::whereId($request->fid)->pluck('location_id');
        }
        elseif($request->alertLevel == 'client' || $request->alertLevel == 0){
            $data["salescenters"] = $request->clientId;
            $data["selectedsalescenters"] = FraudAlert::whereId($request->fid)->pluck('client_id');
        }
        return $this->success('success','success',$data);
    }

    //Store Fraud-alert Email & Sms Data
    public function store(Request $request)
    {
        try
        {
            // dd($request->emailId);
            $requestData = $request->all();
            //Check Email is empty or not and also check count email is greater then zero
            if(!empty($requestData['email']) && count($requestData['email']) > 0){
                foreach($requestData['email'] as $i => $list){
                    $data = array();
                    $all = ['fraudalert','disposition'];
                    //Create seperate array for email
                    $rowdata = array();
                    $rowdata['email']=$requestData['email'][$i];
                    $rowdata['phone'] = NULL;
                    switch($requestData['email_alert_level'][$i]){
                        case 'client':
                            $rowdata['alert_level'] = "client";
                            $rowdata['client_id'] = (!empty($requestData['emaillocations-'.($i+1)])) ? implode("," ,$requestData['emaillocations-'.($i+1)]) : NULL;
                            $rowdata['salescenter_id'] = NULL;
                            $rowdata['location_id'] = NULL;
                            break;
                        case 'salescenter':
                            $rowdata['alert_level'] = "salescenter";
                            $rowdata['salescenter_id'] = (!empty($requestData['emaillocations-'.($i+1)])) ? implode("," ,$requestData['emaillocations-'.($i+1)]) : NULL;
                            $rowdata['client_id'] = NULL;
                            $rowdata['location_id'] = NULL;
                            break;
                        case 'sclocation':
                            $rowdata['alert_level'] = "sclocation";
                            $rowdata['location_id'] = (!empty($requestData['emaillocations-'.($i+1)])) ? implode("," ,$requestData['emaillocations-'.($i+1)]) : NULL;
                            $rowdata['salescenter_id'] = NULL;
                            $rowdata['client_id'] = NULL;
                            break;
                        default:
                            break;
                    }
                    // if(in_array("all",$requestData['email_alert_for'])){
                    //     $rowdata['alert_for'] = implode(',' , $all);
                    // }else{
                    //     $rowdata['alert_for'] = $requestData['email_alert_for'];
                    // }
                    $rowdata['added_by'] = Auth::Id();
                    $rowdata['added_for_client'] = $requestData['clientId'];
                    $rowdata['type'] = "email";
                    $rowdata['created_at']= date('Y-m-d H:i:s');
                    $rowdata['updated_at']= date('Y-m-d H:i:s');
                    array_push($data,$rowdata);
                    FraudAlert::insert($data);
                }
            }
            elseif(!empty($requestData['edit_email']) && count($requestData['edit_email']) > 0){
                foreach($requestData['edit_email'] as $i => $list){
                    $ids = array();
                    $ids = $requestData['fid-'.($i+1)]; //current id
                    $editrow['email']=$requestData['edit_email'][$i];
                    $editrow['phone'] = NULL;
                    switch($requestData['edit_email_alert_level'][$i]){
                        case 'client':
                            $editrow['alert_level'] = "client";
                            $editrow['client_id'] = (!empty($requestData['edit_locations-'.($i+1)])) ? implode("," ,$requestData['edit_locations-'.($i+1)]) : NULL;
                            $editrow['salescenter_id'] = NULL;
                            $editrow['location_id'] = NULL; 
                            break;
                        case 'salescenter':
                            $editrow['alert_level'] = "salescenter";
                            $editrow['client_id'] = NULL;
                            $editrow['salescenter_id'] = (!empty($requestData['edit_locations-'.($i+1)])) ? implode("," ,$requestData['edit_locations-'.($i+1)]) : NULL;
                            $editrow['location_id'] = NULL;
                            break;
                        case 'sclocation':
                            $editrow['alert_level'] = "sclocation";
                            $editrow['location_id'] = (!empty($requestData['edit_locations-'.($i+1)])) ? implode("," ,$requestData['edit_locations-'.($i+1)]) : NULL; 
                            $editrow['client_id'] = NULL;
                            $editrow['salescenter_id'] = NULL;                            
                            break;
                        default:
                            break;
                    }
                    FraudAlert::where('id',$ids)->update($editrow);
                }
            }
            //Check sms is empty or not and also check count sms is greater then zero
            if(!empty($requestData['sms']) && count($requestData['sms']) > 0){
                foreach($requestData['sms'] as $i => $list){
                    $data = array();
                    //Create seperate array for sms
                    $editrow = array();
                    $editrow['phone']=$requestData['sms'][$i];
                    $editrow['email']=NULL;
                    switch($requestData['sms_alert_level'][$i]){
                        case 'client':
                            $editrow['alert_level'] = "client";
                            $editrow['client_id'] = (!empty($requestData['smslocations-'.($i+1)])) ? implode("," ,$requestData['smslocations-'.($i+1)]) : NULL;
                            $editrow['salescenter_id'] = NULL;
                            $editrow['location_id'] = NULL;
                            break;
                        case 'salescenter':
                            $editrow['alert_level'] = "salescenter";
                            $editrow['salescenter_id'] = (!empty($requestData['smslocations-'.($i+1)])) ? implode("," ,$requestData['smslocations-'.($i+1)]) : NULL;
                            $editrow['location_id'] = NULL;
                            $editrow['client_id'] = NULL;
                            break;
                        case 'sclocation':
                            $editrow['alert_level'] = "sclocation";
                            $editrow['location_id'] = (!empty($requestData['smslocations-'.($i+1)])) ? implode("," ,$requestData['smslocations-'.($i+1)]) : NULL;
                            $editrow['salescenter_id'] = NULL;
                            $editrow['client_id'] = NULL;
                            break;
                        default:
                            break;
                    }
                    $editrow['added_by'] = Auth::Id();
                    $editrow['added_for_client'] = $requestData['clientId'];
                    $editrow['type'] = "phone";
                    $editrow['created_at']= date('Y-m-d H:i:s');
                    $editrow['updated_at']= date('Y-m-d H:i:s');
                    array_push($data,$editrow);
                    FraudAlert::insert($data);
                }
            }
            elseif(!empty($requestData['edit_sms']) && count($requestData['edit_sms']) > 0){
                foreach($requestData['edit_sms'] as $i => $list){
                    $ids = array();
                    $ids = $requestData['sms_fid-'.($i+1)]; //current id
                    $editrow['email']= NULL;
                    $editrow['phone'] = $requestData['edit_sms'][$i];                    
                    switch($requestData['edit_sms_alert_level'][$i]){
                        case 'client':
                            $editrow['alert_level'] = "client";
                            $editrow['client_id'] = (!empty($requestData['edit_locations_sms-'.($i+1)])) ? implode("," ,$requestData['edit_locations_sms-'.($i+1)]) : NULL;
                            $editrow['salescenter_id'] = NULL;
                            $editrow['location_id'] = NULL;
                            break;
                        case 'salescenter':
                            $editrow['alert_level'] = "salescenter";
                            $editrow['client_id'] = NULL;
                            $editrow['salescenter_id'] = (!empty($requestData['edit_locations_sms-'.($i+1)])) ? implode("," ,$requestData['edit_locations_sms-'.($i+1)]) : NULL;
                            $editrow['location_id'] = NULL;
                            break;
                        case 'sclocation':
                            $editrow['alert_level'] = "sclocation";
                            $editrow['location_id'] = (!empty($requestData['edit_locations_sms-'.($i+1)])) ? implode("," ,$requestData['edit_locations_sms-'.($i+1)]) : NULL;
                            $editrow['client_id'] = NULL;
                            $editrow['salescenter_id'] = NULL;
                            break;
                        default:
                            break;
                    }
                    FraudAlert::where('id',$ids)->update($editrow);
                }              
            }
            if($request->emailId){  // delete for email
                FraudAlert::where('id',$request->emailId)->delete();
                return response()->json([
                'status' => 200,
                'message' => "Delete Successfully",
                ],200);
            }
            elseif($request->smsId){  // delete for sms
                FraudAlert::where('id',$request->smsId)->delete();
                return response()->json([
                'status' => 200,
                'message' => "Delete Successfully",
                ],200);
            }
            $tab = $request->get('tab');
            return back()->withInput(['tab'=>$tab]);
        }
        catch(\Exception $exception){
            return back()->with('error',$exception->getMessage());
        }
    }
}
