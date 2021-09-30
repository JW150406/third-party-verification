<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\ClientWorkspace;
use App\models\ClientWorkflow;
use App\models\ClientTwilioNumbers;
use App\models\Client;
use App\models\UserTwilioId;
use DataTables;
use Auth;

class TwilioController extends Controller
{
    
    public function getWorkSpaceByClient(Request $request)
    {
        $workspaces=ClientWorkspace::where('client_id',$request->client_id)->get();
        return response()->json(['status' => 'success', 'data' => $workspaces]);

    }

    /**
     * This function is used to store client workspace
     */
    public function saveWorkSpace(Request $request)
    {
        /* Start Validation rule */
    	$request->validate([
    		'workspace_id' => 'required',
    		'workspace_name' => 'required',
    	]);
        /* End Validation rule */

    	try {
        	$workspaces=ClientWorkspace::updateOrCreate([
        		'client_id'=>$request->client_id,
        		'workspace_id'=>$request->workspace_id,
        	],$request->all());
        	return response()->json(['status' => 'success', 'message' => "Workspace successfully saved."]);
    	}catch(Exception $e) {
    		return response()->json(['status' => 'error', 'message' => 'Something went wrong . Please try again !!']);
    	}
        
    }

    /**
     * This method is used to remove client workspace
     */
    public function deleteWorkSpace(Request $request)
    {
        /* Start Validation rule */
    	$request->validate([
    		'workspace_id' => 'required',
    	]);
        /* End Validation rule */

    	try {
        	$workspaces=ClientWorkspace::where('client_id',$request->client_id)->where('workspace_id',$request->workspace_id)->delete();
        	return response()->json(['status' => 'success', 'message' => "Workspace successfully deleted."]);
    	}catch(Exception $e) {
    		return response()->json(['status' => 'error', 'message' => 'Something went wrong . Please try again !!']);
    	}
        
    }

    /**
     * This function is used to get workflow by client
     */
    public function getWorkflowByClient(Request $request)
    {
        $workflows=ClientWorkflow::where('client_id',$request->client_id)->get();
        return response()->json(['status' => 'success', 'data' => $workflows]);

    }

    /**
     * This function is used to store client workflow
     */
    public function saveWorkflow($clientId, Request $request)
    {
        $workflow = [];
        if ($request->has('id') && $request->get('id')) {
            $workflow = ClientWorkflow::find($request->get('id'));
        }
        if(empty($workflow)){
            $request->validate([
                // 'workspace_id' => 'required',
                'workflow_id' => 'required|unique:client_twilio_workflowids,workflow_id',
                'workflow_name' => 'required',
            ],['workflow_id.unique' => 'This workflow ID is taken']);
        }else{
            $request->validate([
                // 'workspace_id' => 'required',
                'workflow_id' => 'required|unique:client_twilio_workflowids,workflow_id,'.$request->id,
                'workflow_name' => 'required',
            ],['workflow_id.unique' => 'This workflow ID is taken']);
        }

    	try {
            $client = Client::find($clientId);

            if (empty($client)) {
                return response()->json(['status' => 'error', 'message' => "Client not found. "]);
            }

            $workspace = ClientWorkspace::where('client_id', array_get($client, 'id'))->first();
            if (empty($workspace)) {
                return response()->json(['status' => 'error', 'message' => "Workspace not found."]);
            }

            if (empty($workflow)) {
                ClientWorkflow::create([
                    'client_id' => array_get($client, 'id'),
                    'workspace_id' => array_get($workspace, 'workspace_id'), 
                    'workflow_id' => $request->workflow_id,
                    'workflow_name' => $request->workflow_name
                ]);
            } else {
                $oldWorkflow = array_get($workflow, 'workflow_id');
                
                $isUpdated = $workflow->update([
                    'workflow_id' => $request->workflow_id,
                    'workflow_name' => $request->workflow_name
                ]);

                if ($isUpdated && $oldWorkflow != "") {
                    UserTwilioId::where('workflow_id', $oldWorkflow)->update(['workflow_id' => $request->workflow_id]);
                }
            }

        	return response()->json(['status' => 'success', 'message' => "Workflow successfully saved."]);

    	}catch(\Exception $e) {
    		return response()->json(['status' => 'error', 'message' => 'Something went wrong, please try again.']);
    	}
        
    }

    /**
     * This function is used to remove client workflow
     */
    public function deleteWorkflow(Request $request)
    {    	
    	try {
        	$workflows=ClientWorkflow::where('id',$request->id)->delete();
        	return response()->json(['status' => 'success', 'message' => "Workflow successfully deleted."]);
    	}catch(Exception $e) {
    		return response()->json(['status' => 'error', 'message' => 'Something went wrong, please try again.']);
    	}
        
    }

    /**
     * This function is used to remove client twilio number
     */
    public function deleteNumber(Request $request)
    {       
        try {
            $workflows=ClientTwilioNumbers::where('id',$request->id)->delete();
            return response()->json(['status' => 'success', 'message' => "Phone Number successfully deleted."]);
        }catch(\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong, please try again.']);
        }
        
    }
    
    /**
     * This function is used to get client twilionumber
     */
    public function getNumber(Request $request)
    {
        $number = ClientTwilioNumbers::find($request->id);

        if (empty($number)) {
            return response()->json(['status' => 'error', 'message' => 'Number not found', 'data' => []]);
        }

        $number->phonenumber = str_replace("+", "", $number->phonenumber);
        return response()->json(['status' => 'success', 'message' => 'success', 'data' => $number]);

    }

    /**
     * This method is used store client twilio number
     */
    public function saveNumber(Request $request)
    {
        if ($request->get('type') == config()->get('constants.IVR_TPV_VERIFICATION_KEY')) {
            $request->validate([
                'phone_number' => 'required|regex:' . config()->get('constants.PHONE_NUMBER_VALIDATION_REGEX'),
                'type' => 'required',
            ]);    
        } else {
            $request->validate([
                'workflow' => 'required',
                'phone_number' => 'required|regex:' . config()->get('constants.PHONE_NUMBER_VALIDATION_REGEX'),
                'type' => 'required',
            ]);
        }
    	

    	try {
            $query=[
                'id' =>$request->id,
            ];
            $phNum = str_replace(array('(',')','-', " "), '', $request->phone_number);
            $arrangedNum = ($phNum[0] == "+") ? $phNum : "+" . $phNum;
    		$data= [
    			'phonenumber' => $arrangedNum,
    			'client_workflowid' =>$request->workflow,
    			'client_id' =>$request->client_id,
                'type' =>$request->type,
    			'added_by' =>\Auth::id(),
    		];
        	ClientTwilioNumbers::updateOrCreate($query,$data);
        	return response()->json(['status' => 'success', 'message' => "Phone Number successfully saved."]);
    	}catch(\Exception $e) {
    		return response()->json(['status' => 'error', 'message' => 'Something went wrong, please try again.']);
    	}
        
    }

    public function numbers($id, Request $request)
    {
        try {
            if ($request->ajax()) {
                $format = config()->get('constants.DISPLAY_PHONE_NUMBER_FORMAT');
                $replacement = config('constants.PHONE_NUMBER_REPLACEMENT');
                $types = [];
                if(isOnSettings($id,'is_enable_agent_tpv_num')) {
                    $types[] = 'customer_verification';
                }
                if(isOnSettings($id,'is_enable_cust_call_num')) {
                    $types[] = 'customer_call_in_verification';
                }
                if(isOnSettings($id,'is_enable_ivr')) {
                    $types[] = 'ivr_tpv_verification';
                }
                $numbers = ClientTwilioNumbers::where('client_twilio_numbers.client_id', $id)->whereIn('type',$types)->with('workflow');
                
                return DataTables::of($numbers)
                    ->editColumn('workflow.workflow_name', function($number) {
                        if (!empty($number->workflow) && $number->workflow->workflow_name != "") {
                            return $number->workflow->workflow_name;
                        }
                        return "-";
                    })
                    ->editColumn('type', function($number){
                        $type='N/A';
                        if($number->type =='customer_verification') {
                            $type=\Config::get('constants.scripts.customer_verification');
                        } else if($number->type =='customer_call_in_verification') {
                            $type=\Config::get('constants.scripts.customer_call_in_verification'); 
                        }
                        else if($number->type =='ivr_tpv_verification')
                        {
                            $type=\Config::get('constants.TWILIO_IVR_TYPE.ivr_tpv_verification');
                        }
                        return $type;
                    })
                    ->addColumn('action', function($number){
                        $editBtn =  $deleteBtn ='';
                        
                        if (Auth::user()->can(['edit-twilio-number'])) {
                            $editBtn = '<button
                                data-toggle="tooltip"
                                data-placement="top" data-container="body"
                                data-original-title="Edit Phone Number"
                                role="button"
                                class="btn edit-number"
                                data-id="'.$number->id.'"
                                data-type="edit"
                                >'.getimage("images/edit.png").'</button>';
                        }

                        if (Auth::user()->can(['delete-twilio-number'])) {
                            $deleteBtn = '<button  class="btn delete-number"  data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Delete Phone Number" data-id="'.$number->id.'"  data-number="'.$number->phonenumber.'"  role="button">'.getimage("images/cancel.png").'</button>';
                        }
                        if(empty($editBtn) && empty($deleteBtn)) {
                            return '';
                        } else {

                            return '<div class="btn-group">'.$editBtn.$deleteBtn.'<div>';
                        }
                    })
                    ->editColumn('phonenumber', function($number) use($format, $replacement) {
                        $phNum = str_replace("+", "", $number->phonenumber);
                        return preg_replace($format, $replacement, $phNum);
//                        return preg_replace('/(.{2})(\d{3})(\d{3})(\d{4})/',
//                            "$1 $2 $3 $4", $number->phonenumber);
                    })
                    ->make(true);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Invalid Request.'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong.'], 500);
        }
    }

    public function workflows($id, Request $request)
    {
        try {
            $client = Client::find($id);

            if (empty($client)) {
                return redirect()->back()->withErrors('Client not found.');
            }

            if ($request->ajax()) {
                $workflows = ClientWorkflow::where('client_id', $id);

                return DataTables::of($workflows)
                    ->addColumn('action', function($workflow){
                        $editBtn =  $deleteBtn ='';
                        
                        if (Auth::user()->can(['edit-workflow'])) {
                            $editBtn = '<button
                                data-toggle="tooltip"
                                data-placement="top" data-container="body"
                                data-original-title="Edit Workflow"
                                role="button"
                                class="btn edit-workflow"
                                data-id="'.$workflow->id.'"
                                data-type="edit"
                                >'.getimage("images/edit.png").'</button>';
                        }

                        if (Auth::user()->can(['delete-workflow'])) {
                            $deleteBtn = '<button  class="btn delete-workflow"  data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Delete Workflow" data-id="'.$workflow->id.'" id="delete-workflow-'.$workflow->id.'" data-workflow_name="'.$workflow->workflow_name.'"  role="button">'.getimage("images/cancel.png").'</button>';
                        }
                        if(empty($editBtn) && empty($deleteBtn)) {
                            return '';
                        } else {
                            return '<div class="btn-group">'.$editBtn.$deleteBtn.'<div>';
                        }
                    })
                    ->make(true);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Invalid Request.'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong.'], 500);
        }
    }

    /**
     * This function is used to update client workflow
     */
    public function editWorkflow(Request $request) {
        try {
            $workflow = ClientWorkflow::find($request->id);
            if (empty($workflow)) {
                return response()->json(['status' => 'error', 'message'=>'Workflow not found.'], 400);
            }
            return response()->json([ 'status' => 'success', 'data' => $workflow]);
        } catch (Exception $e) {
            return response()->json([ 'status' => 'error', 'message' => 'Something went wrong, please try again.' ]);
        }
    }

    /**
     * This function is used to get client twilionumber
     */
    public function getTwilioNumber(Request $request) {
        try {
            if (isset($request->to) && !empty($request->to)) {
                $number = ClientTwilioNumbers::where('phonenumber', $request->to)->first();
                if (empty($number)) {
                    return response()->json(['status' => 'error', 'message'=>'Number not found.'], 400);
                } else {
                    return response()->json([ 'status' => 'success', 'data' => $number]);
                }
            } else {
                return response()->json(['status' => 'error', 'message'=>'Invalid Request.'], 400);
            }
        } catch (Exception $e) {
            return response()->json([ 'status' => 'error', 'message' => 'Something went wrong, please try again.' ]);
        }
    }

}
