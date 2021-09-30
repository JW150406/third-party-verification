<?php

namespace App\Http\Controllers\Salesagent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\User;
use Mail;
use Hash;
use Session;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Log;
use App\models\Salescenter;
use App\models\Client;
use App\models\Salescenterslocations;
use App\models\Clientsforms;
use App\models\UserAssignedForms;
use App\models\Telesales;
use App\models\TelesaleScheduleCall;
use App\models\UserDocuments;
use App\models\Salesagentdetail;
use App\models\Salesagentlocation;
use DB;
use Illuminate\Http\File;
use DataTables;
use App\models\TextEmailStatistics;
use App\Services\StorageService;

class SalesagentController extends Controller
{
    private $client = array();
    private $salescenter = array();


    public function __construct(Request $request)
    {
        $this->middleware('auth');
         if($request->client_id){
            $this->client = (new Client )->getClientinfo($request->client_id);
         }
         if($request->salescenter_id){
            $this->salescenter = (new Salescenter)->getSalescenterinfo($request->salescenter_id);
        }
        $this->storageService = new StorageService;
    }

    /**
     * This method is used to show salesagent userlist
     */
    public function salesagents($client_id, $salescenter_id,Request $request)
    {
        //dump($request->status);
        if ($request->ajax()) {
            if($request->status == 'active'){
                $users=User::select('users.*')->where("client_id",$client_id)->where('status','active')->where("salescenter_id",$salescenter_id)->where('access_level', 'salesagent')->with('salesAgentDetails');
            }elseif ($request->status == 'inactive'){
                $users=User::select('users.*')->where("client_id",$client_id)->where('status','inactive')->where("salescenter_id",$salescenter_id)->where('access_level', 'salesagent')->with('salesAgentDetails');
            }else{
                $users=User::select('users.*')->where("client_id",$client_id)->where("salescenter_id",$salescenter_id)->where('access_level', 'salesagent')->with('salesAgentDetails');
            }
            /* check user has multiple locations */
            if (auth()->user()->hasMultiLocations()) {
                $locationIds = auth()->user()->locations->pluck('id');
                $users->whereHas('salesAgentDetails', function ($query) use ($locationIds) {
                    $query->whereIn('location_id', $locationIds);
                });
            }

            /* check location level restriction */
            if(Auth::user()->isLocationRestriction()) {
                $locationId = Auth::user()->location_id;
                $users->whereHas('salesAgentDetails', function (Builder $query) use ($locationId) {
                    $query->where('location_id', $locationId);
                });
            }

            $isOnD2Dapp = isOnSettings($client_id,'is_enable_d2d_app');

            return DataTables::of($users)
                ->editColumn('profile_picture', function($user){
                    $icon = getProfileIcon($user);
                    return $icon;
                })
                ->addColumn('agent_type', function ($user){

                    if (!empty($user->salesAgentDetails->agent_type)) {
                        return $user->salesAgentDetails->agent_type == 'd2d' ? 'D2D' : ucfirst($user->salesAgentDetails->agent_type);
                    } else {
                       return 'N/A';
                    }
                })
                ->addColumn('external_id', function ($user){
                    
                    if (!empty($user->salesAgentDetails->external_id)) {
                        return $user->salesAgentDetails->external_id;
                    } else {
                       return '-';
                    }

                })
            ->addColumn('action', function($user) use ($isOnD2Dapp) {
                $viewBtn = $editBtn = $statusBtn = $deleteBtn = '';
                $isEnableEditBtn = true;
                if (!empty($user->salesAgentDetails->agent_type) && $user->salesAgentDetails->agent_type == 'd2d'  && !$isOnD2Dapp) {
                    $isEnableEditBtn = false;
                }
                if (\auth()->user()->hasPermissionTo('view-sales-agents')) {
                    $viewBtn = '<button  data-toggle="tooltip" 
                        data-placement="top" 
                        data-type="view"
                        data-container="body" 
                        data-original-title="View Sales Agent"   
                        data-title="View Sales Agent"
                        class="btn salesagent-modal" 
                        data-status="' . $user->status . '" 
                        data-reason="' . $user->deactivationreason . '" 
                        data-id="' . $user->id . '" 
                        data-userid="' . $user->userid . '" 
                        data-client-id="' . $user->client_id . '" 
                        data-salescenter-id="' . $user->salescenter_id . '" 
                        data-salescenter-name="' . $user->salescenter->name . '"  
                        >' . getimage("images/view.png") . '</button>';
                } else {
                    $viewBtn = getDisabledBtn();
                }
                if ($user->salescenter->isActive() && $user->salescenter->isActiveClient() && auth()->user()->hasPermissionTo('edit-sales-agents')  && $user->is_block != 1 && $isEnableEditBtn) {
                    $editBtn = '<button 
                        class="btn salesagent-modal" 
                        data-type="edit" 
                        data-toggle="tooltip" 
                        data-container="body"
                        data-placement="top" 
                        data-original-title="Edit Sales Agent" 
                        data-title="Edit Sales Agent"  
                        data-status="' . $user->status . '" 
                        data-reason="' . $user->deactivationreason . '" 
                        data-is-block="' . $user->is_block . '" 
                        data-id="' . $user->id . '" 
                        data-userid="' . $user->userid . '" 
                        data-client-id="' . $user->client_id . '" 
                        data-salescenter-id="' . $user->salescenter_id . '" 
                        data-salescenter-name="' . $user->salescenter->name . '"  
                        >' . getimage("images/edit.png") . '</button>';
                } else {
                    $editBtn = getDisabledBtn('edit');
                }
                if($user->salescenter->isActive() && $user->salescenter->isActiveClient() && auth()->user()->hasPermissionTo('deactivate-sales-agent')) {
                    if ($user->status == 'active') {
                        $statusBtn = '<button
                        class="deactivate-salescentersaleuser btn" 
                        data-toggle="tooltip"
                        data-placement="top" 
                        data-container="body" 
                        data-original-title="Deactivate Sales Agent"
                        data-id="' . $user->id . '"
                        data-name="' . $user->full_name . '"
                        data-sid="' . $user->salescenter_id . '" >' . getimage("images/activate_new.png") . '</button>';
                    } else {
                        $editBtn = getDisabledBtn('edit');
                        
                        $statusBtn = '<button
                        class="activate-salescentersaleuser btn" 
                        data-toggle="tooltip" 
                        data-placement="top"  
                        data-container="body" 
                        data-original-title="Activate Sales Agent"
                        data-id="' . $user->id . '"
                        data-is-block="' . $user->is_block . '"
                        data-name="' . $user->full_name . '"
                        data-sid="' . $user->salescenter_id . '" >' . getimage("images/deactivate_new.png") . '</button>';
                    }
                    if(Auth::user()->hasPermissionTo('delete-sales-agent'))
                    {
                        $class = 'delete_sales_agent';
                        $attributes = [
                            "data-original-title" => "Delete Sales Agent",
                            "data-id" => $user->id,
                            "data-name"=> $user->full_name,
                            "data-status" =>"delete",
                        ];
                        $deleteBtn = getDeleteBtn($class, $attributes);
                    }
                } else {
                    $statusBtn = getDisabledBtn('status');
                }
                return '<div class="btn-group">'.$viewBtn.$editBtn.$statusBtn.$deleteBtn.'<div>';
            })
            ->rawColumns(['profile_picture','action'])
            ->make(true);
        }

        $agent_users = (new User)->getSalesagents($client_id,$salescenter_id);
        $salecenter_id = $salescenter_id;
        
        return view('client.salescenter.salesagent.salesuserslist',compact('agent_users','client_id','salescenter_id','salecenter_id'))
             ->with('i', ($request->input('page', 1) - 1) * 20);
    }

    /**
     * This method is used to add salesperson
     */
    public function adduser($client_id, $salescenter_id,Request $request)
    {
       $locations =  (new Salescenterslocations)->getLocationsInfo($client_id, $salescenter_id);
       $location_id = "";
       if($request->location){
        $location_id = $request->location;
       }
       $clientsforms = (new ClientsForms)->getAllFormUsingClientID($client_id);
        return view('client.salescenter.salesagent.addsalesperson',compact('client_id','salescenter_id','locations','location_id','clientsforms'));
    }

    /**
     * This method is used to store user
     */
    public function saveuser($client_id, $salescenter_id,Request $request)
    {
        /* Start Validation rule */
           $validator = \Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'location' => 'required',
            'formid' => 'required'
        ]);

        if ($validator->fails())
        {
            return response()->json([ 'status' => 'error',  'errors'=>$validator->errors()->all()]);
        }
        /* End Validation rule */
      try{

                $verification_code = str_random(20);
                $input = $request->only('first_name','last_name', 'email');
                $input['parent_id'] = Auth::user()->id;
                $input['client_id'] = $client_id;
                $input['salescenter_id'] = $salescenter_id;
                $input['location_id'] = $request->location;
                $input['access_level'] = 'salesagent';
                $input['status'] = 'inactive';
                $input['userid'] = strtolower($request->first_name[0]);
                $input['verification_code'] = $verification_code ;
                $input['password'] = Hash::make(rand()); //Hash password
                $added_user =  (new User)->createSalesagent($input);
                (new User)->updateUser($added_user,array('userid' => strtolower($request->first_name[0]).$added_user));

                (new UserAssignedForms)->addnew($client_id, $added_user, $request->formid);

                $data['verification_code']  = $verification_code;
                $data['email']  = $input['email'];
                $data['name']  = $input['first_name'];
                $data['addedby_firstname']  = Auth::user()->first_name;
                $salescenter = Salescenter::find($salescenter_id);
                $data['addedby_vendor']  = $salescenter->name;
                $data['client_id']  = $client_id;
                $data['salescenter_id']  = $salescenter_id;
                $message ='Hello '.$input['first_name'].', <br>
                You have been added to TPV360  as a <clientname> sales agent.<br>
                Your username is: '.$input['email'].'
                Please <a href="'.url('/'.$salescenter_id.'/verify', ['code'=>$verification_code]) .'">click here</a> to generate your password.<br><br>Regards,<br><br>The TPV360 Team ';
                $to      = $input['email'];
                $subject = 'Welcome to TPV360';
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                // More headers
                $headers .= 'From: <noreply@tpv.plus>' . "\r\n";
                $headers .= 'Cc: noreply@tpv.plus' . "\r\n";

                mail($to, $subject, $message, $headers);

           return response()->json([ 'status' => 'success',  'message'=>'User created successfully.','url' => route('client.findsalesagents',['client' => $client_id,'salecenter' =>$salescenter_id ]) ]);
        } catch(Exception $e) {
        // echo 'Message: ' .$e->getMessage();
        return response()->json([ 'status' => 'error',  'errors'=> ["Something went wrong!. Please try again."]]);
        }
        // return redirect()->route('client.findsalesagents',['client' => $client_id,'salecenter' =>$salescenter_id ])
        //     ->with('success','User created successfully.');
    }

    /**
     * This method is used to show user details
     */
    public function showuser($client_id,$salescenter_id,$userid)
    {

         $user = 	(new User)->getSalescenterUser($client_id,$salescenter_id,$userid);
         $location_info =  (new Salescenterslocations)->getSingleLocationInfo($user->location_id);
         $location = $location_info[0];
         $salecenter_id = $salescenter_id;

         return view('client.salescenter.salesagent.usershow',compact('user','client_id','salescenter_id','location','salecenter_id'));
    }

    /**
     * This method is used to show edit salesagent user 
     */
    public function edituser($client_id,$salescenter_id, $userid,Request $request)
    {

         $user = 	(new User)->getSalescenterUser($client_id,$salescenter_id,$userid);
         $locations =  (new Salescenterslocations)->getLocationsInfo($client_id, $salescenter_id);
         $documents = (new UserDocuments)->getUserDocuments($userid);

         $user_details = (new Salesagentdetail)->getUserDetail($userid);

         $client = "";
         $salecenter = "";
         $location = "";
         $ref = "";
         $reference_array= array();
         $backurl = route('client.salescenter.salesagents',['client_id' => $user->client_id,'salescenter_id'=>$user->salescenter_id]);
         if(isset($request->ref)){
            $ref = $request->ref;
            $client = $request->client;
            $salecenter = $request->salecenter;
            $location = $request->location;
            $reference_array = array(
                'ref' => $ref,
                'client' => $client,
                'salecenter' => $salecenter,
                'location' => $location,
            );
            $backurl = route('client.findsalesagents',['client' => $request->client,'salecenter'=>$salecenter,'location' => $location]);
         }
         $clientsforms = (new ClientsForms)->getAllFormUsingClientID($client_id);
        $assigned_forms =     (new UserAssignedForms)->getAssignedForm($userid);
        $assignedform = "";
        if(!empty($assigned_forms)){
            $assignedform =   $assigned_forms->form_id;
        }

         return view('client.salescenter.salesagent.useredit',compact('user','client_id','salescenter_id','locations','reference_array','backurl','clientsforms','assignedform' ,'documents','user_details'));
    }

    /**
     * This method is used to update salesagent user
     */
    public function updateuser($client_id,$salescenter_id,$userid,Request $request)
    {
        /* Start Validation rule */
        $this->validate($request, [
            'first_name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,'.$userid,
            'password' => 'confirmed',
            'location' => 'required',
            'formid' => 'required',
           ]);
           /* End Validation rule */
           $input = $request->only('first_name','last_name', 'email','location');

           (new UserAssignedForms)->deleteIds($userid);
           (new UserAssignedForms)->addnew($client_id, $userid, $request->formid);

           if(!empty($request->password)){
            $input['password'] = Hash::make($request->password); //update the password
           }
           if ($request->hasFile('agentdoc')) {
           // Storage::delete($request->old_url);
             $path = $request->file('agentdoc')->store('user/documents');
             $filename =  pathinfo($request->file('agentdoc')->getClientOriginalName(), PATHINFO_FILENAME);

               (new UserDocuments)->addDocument($userid ,$path,$filename,Auth::user()->id);

        }


           $user = 	(new User)->updateSalesagent($userid,$input);
           return redirect()->back()
           ->with('success','User successfully updated.');
    }

    public function save(Request $request)
    {
        $id = $request->id;
        $client_id = $request->client;
        $salescenter_id = $request->sales_center;

        /* Start Valiation rule */
        $this->validate($request, [
            // 'external_id' => 'required',
            'location' => 'required',
            'client' => 'required',
            'sales_center' => 'required',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'nullable|email|unique:users,email,'.$id.',id,deleted_at,NULL',
            'agent_type' => 'required',
            'certified' => 'required',
           // 'certification_date' => 'required',
            'passed_state_test' => 'required',
            //'state' => 'required',
            // 'documents' => 'required_without:id',
            'comment'=>'required_if:status,==,inactive',
            // 'restrict-state' => 'required'

        ],[
            // 'documents.required_without'=>'This field is required',
            'comment.required_if' => 'This field is required',
            'agent_type.required' => 'This field is required',
            'certified.required' => 'This field is required',
            'passed_state_test.required' => 'This field is required',
            'location.required' => 'This field is required'
        ]);
        /* End Validation rule */
        try {          
            

            $data = $request->only('first_name','last_name', 'email');
            $agent_detail = $request->only('external_id','certified','passed_state_test','state','backgroundcheck','drugtest','agent_type');
            $agent_detail['certification_date'] = $agent_detail['certification_exp_date'] = $agent_detail['state'] = null;

            $data['parent_id'] = Auth::user()->id;
            $agent_detail['location_id'] = $data['location_id'] = $request->location;
            $agent_detail['added_by'] = Auth::user()->id;
            if(!empty($request->phone_number)) {
                $agent_detail['phone_number'] = $request->phone_number;
            } else {
                $agent_detail['phone_number'] = null;
            }
            if(!empty($request->state)) {
                $agent_detail['state'] = implode(',', $request->state);
            }
            if(!empty($request->restrict_state)) {
                $agent_detail['restrict_state'] = implode(',', $request->restrict_state);
            } else {
                $agent_detail['restrict_state'] = null;
            }
            if(!empty($request->certification_date)) {
                $agent_detail['certification_date'] = date('Y-m-d',strtotime($request->certification_date));
            }
            if(!empty($request->expiry_date)) {

                $agent_detail['certification_exp_date'] = date('Y-m-d',strtotime($request->expiry_date));
            }
            if (empty($id)) {
                \Log::info("Id is empty, so need to create new Sales agent");
                $verification_code = str_random(20);
                $data['client_id'] = $client_id;
                $data['salescenter_id'] = $salescenter_id;
                $data['access_level'] = 'salesagent';
                $data['status'] = 'active';
                $data['verification_code'] = $verification_code ;
                $data['password'] = ($request->password == null)? Hash::make(str_random(8)) : Hash::make($request->password);
                $user=User::create($data);
                $user->userid = strtolower($request->first_name[0]).$user->id;
                $user->save();
                $agent_detail['user_id']=$user->id;

                Salesagentdetail::create($agent_detail);
                
                if ($request->hasFile('documents')) {

                    $awsFolderPath = config()->get('constants.aws_folder');
                    $filePath = config()->get('constants.USER_DOCUMENTS_UPLOAD_PATH');
                    foreach ($request->file('documents') as $key => $file) {
                        $fileName =  pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $path = $this->storageService->uploadFileToStorage($file, $awsFolderPath, $filePath, $file->getClientOriginalName());

                        if ($path !== false) {
                            (new UserDocuments)->addDocument($user->id, $path, $fileName, Auth::user()->id);
                        }

                    }

                }            

                if (!empty($user))  {

                    // for send verification email
                    $this->sendVerificationEmail($user);
                    Log::info("Successfully created new Sales agent.");
                    return response()->json(['status' => 'success', 'message' => 'Sales center agent successfully created.'], 200);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Something went wrong, please try again.'], 500);
                }

            } else {
                //$data['status']= $request->status;
                $data['deactivationreason'] = $request->comment;
                if($request->input('is_block')) {
                    $data['is_block'] = $request->input('is_block');
                }
                if (!empty($request->password)) {
                    $data['password'] = Hash::make($request->password);
                }
                User::where('id',$id)->update($data);
                $user= User::find($id);
                $user->status = $request->status;
                $user->save();
                Salesagentdetail::updateOrCreate(['user_id'=>$id],$agent_detail);
                //Salesagentdetail::where('user_id',$id)->update($agent_detail);

    //            if ($request->hasFile('documents')) {
    //                foreach ($request->file('documents') as $key => $file) {
    //                    $path = $file->store('user/documents');
    //                    $filename =  pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    //
    //                    (new UserDocuments)->addDocument($id,$path,$filename,Auth::user()->id);
    //                }
    //            }

                if ($request->hasFile('documents')) {
                    $awsFolderPath = config()->get('constants.aws_folder');
                    $filePath = config()->get('constants.USER_DOCUMENTS_UPLOAD_PATH');
                    foreach ($request->file('documents') as $key => $file) {
                        $fileName =  pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $path = $this->storageService->uploadFileToStorage($file, $awsFolderPath, $filePath, $file->getClientOriginalName());

                        if ($path !== false) {
                            (new UserDocuments)->addDocument($user->id, $path, $fileName, Auth::user()->id);
                        }

                    }
                }

                if (!empty($user))  {
                    Log::info("Successfully updated details of Sales agent, id : ".$id);
                    return response()->json(['status' => 'success', 'message' => 'Sales center agent successfully updated.'], 200);
                } else {
                    Log::error("Something went wrong! : At the time of updating detials of Sales agent, id : ".$id);
                    return response()->json(['status' => 'error', 'message' => 'Something went wrong, please try again.'], 500);
                }
            }
        } catch (\Exception $e) {
            Log::error("In Exception of save method : Something went wrong!");
            Log::error("Error while storing sales agent: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something went wrong, please try again.'], 500);
        }
    }

    /**
     * This method is used to update status
     */
    public function updatestatus($company_id,Request $request)
    {
        if( $request->status == 'delete'){

            DB::beginTransaction();

            try {

                DB::delete("delete from telesalesdata where telesale_id in ( select id from telesales where user_id  = '". $request->userid."'  )");
                DB::delete("delete from call_answers where lead_id in ( select id from telesales where user_id  = '". $request->userid."'  )");
                DB::table('telesales')->where('user_id', '=', $request->userid)->delete();
                DB::table('users')->where('id', '=', $request->userid)->delete();
                DB::commit();

                if ($request->ajax()) {
                    return response()->json('Sales center agent successfully updated.');
                }
                return redirect()->back()->with('success','User successfully deleted.');

            } catch (\Exception $e) {
                DB::rollback();
                if ($request->ajax()) {
                    return response()->json('Something went wrong, please try again.');
                }
                return redirect()->back()->withErrors(['error', 'Something went wrong, please try again.']);
            }
        }else{
            $reason_for_deactivate = "";
            $hire_option = "";
            if( isset($request->resonfordeactivate) && $request->resonfordeactivate !="" ){
                $reason_for_deactivate = $request->resonfordeactivate;
            }
            if( isset($request->hireoptions) && $request->hireoptions !="" ){
                $hire_option = $request->hireoptions;
            }


            $user = (new User)->updateUserStatus($request->userid,$request->status,$reason_for_deactivate,$hire_option);

            if ($request->ajax()) {
                return response()->json('Sales center agent successfully updated.');
            }
            return redirect()->back()->with('success','User successfully updated.');
        }

    }

    /**
     * This method is used to get lead
     */
    public function getleads(Request $request){
        if(isset($request->term)) {
            $leads = Telesales::where('id', 'LIKE', "%$request->term%")->orWhere('refrence_id', 'LIKE', "%$request->term%")
                ->get(['id','form_id','refrence_id'])->each(function ($lead){
                    $lead->url = route('client.contact.from', ['id' => Auth::id(), 'form_id' => $lead->form_id, 'lid' => base64_encode($lead->id)]);
                });
            return response()->json($leads);
        } else{
            echo  json_encode(['Invalid request']);
        }

      }
      public function schedulecall(Request $request){
           $requested_data = $request->all();
          if(isset($requested_data['ref']) &&  isset($requested_data['call_immediately']) && isset($requested_data['schedule_date'])  && isset($requested_data['schedule_time']) ){

                 try {
                    $date = date('Y-m-d', strtotime($requested_data['schedule_date'] ));
                    $time  =  date('H:i:s', strtotime($requested_data['schedule_time'] ));
                    $final_date_time =  $date. " ".$time;
                    $TelesaleScheduleCall = new TelesaleScheduleCall();
                    $TelesaleScheduleCall->telesale_id = $requested_data['ref'];
                    $TelesaleScheduleCall->call_immediately = $requested_data['call_immediately'];
                    $TelesaleScheduleCall->call_time = $final_date_time;
                    $TelesaleScheduleCall->save();

                    return response()->json([ 'status' => 'success',  'message'=>'Record updated successfully.' ]);
                } catch(Exception $e) {
                // echo 'Message: ' .$e->getMessage();
                return response()->json([ 'status' => 'error',  'errors'=> ["Something went wrong, please try again."]]);
                }



          }else{
            return response()->json([ 'status' => 'error',  'errors'=> ["Missing required parameters"]]);
          }


      }

      /**
       * This method used to remove userdocument 
       */
      public function deleteFile($client_id,$salescenter_id, $userid,Request $request){

          if( isset( $request->fileid)){

                try {
                    (new UserDocuments)->deletefile($request->fileid);
                    return redirect()->back()
                    ->with('success','File successfully deleted.');

                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->withErrors(['error', 'Something went wrong, please try again.']);
                }
          }else{
            return redirect()->back()->withErrors(['error', 'Something went wrong, please try again.']);
          }



      }


      /**
       * This method used to update userdetail
       */
      public function updateuserdetail($client_id,$salescenter_id,$userid,Request $request)
    {

            $input = $request->only('passed_state_test','state', 'certified','codeofconduct','backgroundcheck','drugtest','certification_date');
            if($input['certification_date'] !=""){
                $input['certification_date'] = date('Y-m-d',strtotime($input['certification_date']));
            }
            $input['added_by'] = Auth::user()->id;
             $user = (new Salesagentdetail)->createorupdate($userid,$input);
           return redirect()->back()
           ->with('success','User successfully updated.');
    }

    /**
     * This method used to update user documents
     */
    public function edit(Request $request) {
        
        $agent_detail=User::where('id',$request->id)->with('salesAgentDetails')->first();
        $documents=UserDocuments::where('user_id',$request->id)->get();
        if(!empty($agent_detail)) {
            return response()->json([ 'status' => 'success',  'data'=>$agent_detail,'documents'=>$documents ]);
        } else {
            return response()->json([ 'status' => 'error',  'message'=>'Something went wrong, please try again.' ]);
        }
    }

    /**
     * This method is used to show userdocuments
     */
    public function showDocuments($id)
    {
        try {
            $document = UserDocuments::find($id);
            if (!empty($document)) {
                $path = \Storage::disk('s3')->url($document->path);
            }
            return response()->json(['status' => 'success',
              'data'=> $path
              ]);

        } catch (\Exception $e) {
            return response()->json([ 'status' => 'error',  'message'=>'Something went wrong, please try again.' ]);
        }
    }

    /**
     * This method is used to remove user documents
     */
    public function deleteDocuments($id)
    {
        try {
            $document = UserDocuments::find($id);
            if (!empty($document)) {
                \Storage::delete($document->path);
                $document->delete();
            }
            return response()->json(['status' => 'success',  'message'=>'Document successfully deleted.']);

        } catch (\Exception $e) {
            return response()->json([ 'status' => 'error',  'message'=>'Something went wrong, please try again.' ]);
        }
    }


    /**
     * This method is used to change user status
     */
    public function changeUserStatus(Request $request) {
        /* Start Validation rule */
        $request->validate(
            [
                'comment'=>'required_if:status,==,inactive',
            ],
            [
                'comment.required_if' => 'The reason for deactivation  field is required.'
            ]
        );
        /* End Validation rule */

        /*$data = [
            'status' => $request->status,
            'deactivationreason' => $request->comment,
            'is_block' => $request->input('is_block', '0')
        ];

        User::where('id',$request->id)->update($data);*/
        

        if($request->status == 'delete')
        {
            Salesagentlocation::where('salesagent_id',$request->id)->delete();
            Salesagentdetail::where('user_id',$request->id)->delete();
            User::find($request->id)->delete();
            if($request->route()->getName() == 'agent.user.changeUserStatusForAllAgent') {
                $message='Agent '.$request->name.' successfully deleted.';
            } else {
                $message='Sales Agent successfully deleted.';
            }
        }
        else
        {
            $user = User::find($request->id);
            $user->status = $request->status;
            $user->deactivationreason = $request->comment;
            $user->is_block = $request->is_block;
            $user->save();
            if ($request->status =='active') {
                if($request->route()->getName() == 'agent.user.changeUserStatusForAllAgent') {
                    $message='Agent '.$user->full_name.' successfully activated.';
                } else {
                    $message='Sales Agent successfully activated.';
                }
            } else {
                if($request->route()->getName() == 'agent.user.changeUserStatusForAllAgent') {
                    $message='Agent '.$user->full_name.' successfully deactivated.';
                } else {
                    $message='Sales Agent successfully deactivated.';
                }
            }
        }
        return response()->json([ 'status' => 'success',  'message'=>$message]);

    }

    // Get sales center agent by location for select option
    public function getSalesCenterAgentsOptionByLocation(Request $request)
    {
        $client_id = $request->client_id;
        $location_id = $request->location_id;
        
        $users = User::withTrashed()->where('client_id',$client_id)
            ->where('status','active')
            ->whereHas('salesAgentDetails', function ($query) use ($location_id) {
                $query->where('agent_type', 'd2d')->where('location_id',$location_id);
            })
            ->orderBy('first_name')
            ->get();
            
        $res_options = "";
        if(!empty($users)){
            foreach($users as $user){
                $res_options.="<option value=\"{$user->id}\" >".$user->full_name."</option>";
            }
        }
        $response = array(
            'status' => 'success',
            'options' =>  $res_options,
        );
        return \Response::json($response);
    }
}
