<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\API\ClientsController;
use App\Jobs\SendContractPDF;
use App\models\EmailVerification;
use App\models\Phonenumberverification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Role;
use App\models\Client;
use App\models\Commodity;
use App\models\Clientsforms;
use App\models\Telesales;
use App\models\Telesalesdata;
use App\models\ScriptQuestions;
use App\models\TextEmailStatistics;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\models\Salescenter;
use Hash;
use Illuminate\Support\Facades\Validator;
use Mail;
use Session;
use App\models\Utilities;
use App\models\Brandcontacts;
use App\models\Salescenterslocations;
use App\models\ClientWorkspace;
use App\models\ClientWorkflow;
use App\models\UserTwilioId;
use App\models\Programs;
use App\models\CustomerType;
use App\models\UserAssignedForms;
use App\models\ClientAgentNotFoundScripts;
use App\models\SelfVerificationAllowedZipcode;
use App\models\TelesalesSelfVerifyExpTime;
use App\models\FormScripts;
use App\models\UtilityZipcodes;
use App\models\ClientTwilioNumbers;
use App\models\Zipcodes;
use App\models\FormField;
use App\models\TelesalesTmp;
use App\models\TelesalesdataTmp;
use App\models\CriticalLogsHistory;
use App\models\Salesagentdetail;
use App\models\Settings;
use DB;
use DataTables;
use App\Services\SegmentService;
use App\Services\StorageService;
use App\Jobs\CriticalAlertMailJob;
use App\models\Dispositions;
use App\models\DoNotEnroll;
use App\Services\TwilioService;
use App\Traits\LeadTrait;
use App\models\SettingTPVnowRestrictedTimeZone;
use App\models\TelesalesZipcode;


class ClientController extends Controller
{
    use LeadTrait;

    public function __construct()
    {
        //$this->middleware('auth');
        $this->segmentService = new SegmentService;
        $this->storageService = new StorageService;
        $this->ClientTwilioNumbers = new ClientTwilioNumbers;
        $this->twilioService = new TwilioService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request){

        if ($request->ajax()) {
            $clients = Client::query();
            //dd($salesCenters);
            return DataTables::of($clients)
                ->editColumn('street', function($client){
                    $address = $client->street.', '.$client->city.', '.$client->state.', '.$client->country.', '.$client->zip;
                    return $address;
                })
                ->editColumn('code', function($client){
                    $code = $client->code;
                    if (!empty($code)) {
                        $code= strtoupper($code);
                    }
                    return $code;
                })
                    ->addColumn('icon', function($client){

                        if (array_get($client, 'logo') && Storage::disk('s3')->exists(config()->get('constants.aws_folder') . $client->logo)) {
                            $logo = '<a href="' . route("client.show", array($client->id)) . '" ><img src="' . Storage::disk('s3')->url($client->logo) . '" class="list-logo" alt="' . $client->name . '"></a>';
                        } else {
                            $logo = '<a href="' . route("client.show", array($client->id)) . '" ><img src="' . asset("images/PlaceholderLogo.png") . '" class="list-logo" alt="' . $client->name . '"></a>';
                        }

                        /*if(!empty($client->logo)){
                            $logo = '<a href="' . route("client.show", array($client->id)) . '" ><img src="' . Storage::url($client->logo) . '" class="list-logo" alt="' . $client->name . '"></a>';
                        }else{
                            $logo = '<a href="' . route("client.show", array($client->id)) . '" ><img src="' . asset("images/PlaceholderLogo.png") . '" class="list-logo" alt="' . $client->name . '"></a>';
                        }*/
                        return $logo;
                    })
                ->addColumn('action', function($client){
                    $editBtn = $statusBtn = $deleteBtn ='';


                    if (Auth::user()->hasPermissionTo('view-client-info')) {
                        $viewBtn = '<a href="'.route("client.show", array($client->id)) .'"  data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="View Client" class="btn purple" role="button">'.getimage("images/view.png").'</a>';
                    }else{
                        $viewBtn = getDisabledBtn();
                    }

                    if (Auth::user()->hasPermissionTo('edit-client-info') && $client->isActive()) {
                        $editBtn = '<a href="'.route("client.edit", array($client->id)) .'"" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Edit Client" class="btn green" role="button">'.getimage("images/edit.png").'</a>';
                    }else{
                        $editBtn = getDisabledBtn('edit');
                    }

                    if (Auth::user()->can(['deactivate-client'])) {

                        if($client->isActive()) {
                            $statusBtn = '<button
                                                class="deactivate-client btn" role="button"  data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Deactivate Client" data-cid="'.$client->id.'"
                                                id="delete-client-'.$client->id.'"
                                                data-clientname="'.$client->name.'"  >'.getimage("images/activate_new.png").'</button>';
                        } else {
                            $statusBtn = '<button
                                                class="activate-client btn" role="button" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Activate Client" data-cid="'.$client->id.'"
                                                id="delete-client-'.$client->id.'"
                                                data-clientname="'.$client->name.'"  >'.getimage("images/deactivate_new.png").'</button>';
                        }
                    }else{
                        $statusBtn = getDisabledBtn('status');
                    }

                    if (Auth::user()->can(['delete-client'])) {
                        $class = 'delete-client';

                        $attributes = [
                            "data-original-title" => "Delete Client",
                            "data-cid" => $client->id,
                            "data-clientname" => $client->name
                        ];
                        $deleteBtn = getDeleteBtn($class, $attributes);
                    }

                    return '<div class="btn-group">'.$viewBtn.$editBtn.$statusBtn.$deleteBtn.'<div>';
                })
                ->rawColumns(['icon','action'])
                ->make(true);
        }

        $clients = (new Client)->getClientDetails();

        return view('client.list',compact('clients'))
            ->with('i', ($request->input('page', 1) - 1) * 20);

    }

    /**
     * For creating a new client resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        $client = [];
        return view('client.create', compact('client'));
    }

    /**
     * Show the client details of requested client id.
     *
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $client_id=$id;
        $client = Client::findOrFail($id);
        $brands = Brandcontacts::where('client_id',$id)->distinct()->select('id','name')->get();
        $roles = (new Role)->getRolesForClientUser();
        $customFields = Settings::getEnableFields($id);
        $states = Zipcodes::groupBy('state')->get();
        $restrictionFields = SettingTPVnowRestrictedTimeZone::where('client_id',$client_id)->get();
        // dd($states);
        return view('client.edit',compact('client', 'client_id','brands','roles','customFields', 'states','restrictionFields'));
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        /* Start Validation rule */
        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255|unique:clients',
            'street' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'zip' => 'required|numeric|digits:5',
            'contact_info' => 'nullable|numeric|digits:10',
            'client_logo' => 'required|mimes:jpg,jpeg,png',
            'code' => 'required|unique:clients',
            'prefix' => 'required|numeric|unique:clients',

        ],[
            'name.unique' => 'This name has already been taken',
            'code.unique' => 'This code is taken',
            'prefix.unique' => 'This prefix is taken',
            'client_logo.required' => 'You must upload a logo',
            'street.required' => 'You must enter an address',
            'zip.required' => 'You must enter a zip code',
            'zip.numeric' => 'Zip code only numeric values',
            'zip.digits' => 'Zip code must be exactly 5 digits',

        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error',  'errors' => $validator->errors()->messages()], 422);
        }
        /* End Validation rule */        

        try{

            $file = $request->file('client_logo');
            $awsFolderPath = config()->get('constants.aws_folder');
            $filePath = config()->get('constants.CLIENT_LOGO_UPLOAD_PATH');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $imageUploaded = $this->storageService->uploadFileToStorage($file, $awsFolderPath, $filePath, $fileName);
            $path = "";
            if ($imageUploaded !== false) {
                $path = $imageUploaded;
            }

            $isFirstClient = false;
            $firstClient = Client::first();
            if (empty($firstClient)) {
                $isFirstClient = true;
            }

            $client = new Client();
            $client->name = $request->name;
            $client->street = $request->street;
            $client->city = $request->city;
            $client->state = $request->state;
            $client->country = $request->country;
            $client->zip = $request->zip;
            $client->code = $request->code;
            $client->prefix = $request->prefix;
            $client->contact_info = ($request->has('contact_info')) ? $request->get('contact_info') : NULL;
            $client->logo = $path;
            $client->created_by = Auth::user()->id;
            $client->save();
            $client_id = $client->id;
            $client_permissions = setDefaultClientPermissions($client_id);

            if ($isFirstClient === false) {
                $workspace = ClientWorkspace::where('client_id', $firstClient->id)->first();
                if (empty($workspace)) {
                    $firstWorkSpace = ClientWorkspace::select('clients.id', 'client_twilio_workspace.workspace_id', 'client_twilio_workspace.workspace_name')->join('clients', 'clients.id', '=', 'client_twilio_workspace.client_id')->first();
                    if (empty($firstWorkSpace)) {
                        return response()->json(['status' =>'error', 'message' => 'Unable to update workspace id for created client, please update workspace for existing all clients from config.'], 500);
                    } else {
                        ClientWorkspace::create(['workspace_id' => $firstWorkSpace->workspace_id, 'client_id' => $client->id, 'workspace_name' => $firstWorkSpace->workspace_name ]);
                    }
                } else {
                    ClientWorkspace::create(['workspace_id' => $workspace->workspace_id, 'client_id' => $client->id, 'workspace_name' => $workspace->workspace_name ]);
                }
            } else {
                \Log::info("First client created in a system. So, You need to create its workspace settings from config.");
            }

            if ($isFirstClient == false) {
                session()->put('success', 'Client Successfully Created.');
                return response()->json(['status' => 'success', 'message' => 'Client Successfully Created.'], 200);
            } else {
                session()->put('success', 'Client created. Now you can contact administrator to create their workspace settings from config page.');
                return response()->json(['status' => 'success', 'message', 'Client created. Now you can contact administrator to create their workspace settings from config page.'], 200);
            }

        } catch (\Exception $e) {
            \Log::error($e);
            session()->put('error', $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something went wrong - cannot create client.'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $client_id = $id;
        $client = Client::active()->findOrFail($id);
        $brands = Utilities::where('client_id',$id)->distinct()->select('utilityname')->get();
        $roles = (new Role)->getRolesForClientUser();
        $customFields = Settings::getEnableFields($id);
        $states = Zipcodes::groupBy('state')->get();
        // $customFields = Settings::where('client_id',$request->client_id)->first();
        $restrictionFields = SettingTPVnowRestrictedTimeZone::where('client_id',$client_id)->get();
        return view('client.edit',compact('client','client_id','brands','roles','customFields','restrictionFields','states'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        /* Start Validation rule */
        $this->validate($request, [
            'name' => 'required|max:255|unique:clients,name,'.$request->id,
            'street' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'zip' => 'required',
            'code' => 'required|unique:clients,code,'.$request->id,
           
         ]);
        /* End Validation rule */

         $id = $request->id;
         $inputs = $request->all();

        $clientUpdate = (new Client)->updateClient($id ,$inputs);
        $workspace_ids = array_unique($request->workspace_id);
        $client_workspace_obj = (new ClientWorkspace);
        $client_workspace_obj->deleteIds($id);

        foreach($workspace_ids as $key => $workspaceid){
            if($request->workspace_name[$key]!="" && $workspaceid!="" ){
               $workspace_name =  $request->workspace_name[$key];
               $client_workspace_obj->addnew($id, $workspaceid,$workspace_name);
            }
        }

        $workflow_obj = (new ClientWorkflow);
        $workflow_obj->deleteIds($id);

        if( isset($request->workflow) && count($request->workflow) >0){
            foreach($request->workflow as   $single_workflow_data ){
                if($single_workflow_data['workspace_id'] != "" && $single_workflow_data['workflow_id']!="" && $single_workflow_data['workflow_name']!=""  ){
                    $workspace_id = $single_workflow_data['workspace_id'];
                    $workflow_id = $single_workflow_data['workflow_id'];
                    $workflow_name = $single_workflow_data['workflow_name'];
                    $workflow_obj->addnew($id, $workspace_id,$workflow_id,$workflow_name );

                }

            }
        }

        $agent_not_found_script_obj = (new ClientAgentNotFoundScripts);
        if(isset($inputs['position']) && isset($inputs['language']) ){
            if(count($inputs['position']) == count($inputs['language']) &&  count($inputs['language']) == count($inputs['agent_not_found_script']) )
            {

                $agent_not_found_script_obj->deleteQuestions($id);
                for($i = 0; $i < count($inputs['position']); $i++){
                    if(isset($inputs['agent_not_found_script'][$i]) && $inputs['agent_not_found_script'][$i]!=""){
                        $insert_question = array(
                            'client_id' => $id,
                            'created_by' => Auth::user()->id,
                            'question' =>  $inputs['agent_not_found_script'][$i],
                            'language' =>  $inputs['language'][$i],
                            'position' =>  $inputs['position'][$i],
                        );
                        $agent_not_found_script_obj->createQuestion($insert_question);
                    }

                }
            }
        }else{
            $agent_not_found_script_obj->deleteQuestions($id);
        }

        $clientNumbers =  (new ClientTwilioNumbers);
        $clientNumbers->deleteNumbers($id);
        if(isset($inputs['phonenumbers']) && count($inputs['phonenumbers']) > 0){
            foreach($inputs['phonenumbers'] as $phonedata){
                if( $phonedata['phonenumber'] != "" &&  $phonedata['workflowid'] !=""){
                   $get_id =  $workflow_obj->getid($id,$phonedata['workflowid']);
                   if(count($get_id)>0){
                    $insert_number = array(
                        'client_id' => $id,
                        'added_by' => Auth::user()->id,
                        'phonenumber' =>  $phonedata['phonenumber'],
                        'client_workflowid' =>  $get_id[0]->id,
                    );
                    $clientNumbers->createNumber($insert_number);
                   }
                }
            }
        }

        if ($request->hasFile('clientlogo')) {
            Storage::delete($request->old_url);
            $path = $request->file('clientlogo')->store('client/logo');
            $clientUpdate = (new Client)->updateClientLogo($id ,$path);
        }


        return redirect()->back()
            ->with('success','Client successfully updated.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateNew(Request $request)
    {
        /* Start Validation rule */
        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255|unique:clients,name,'.$request->id,
            'street' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'zip' => 'required|numeric|digits:5',
            'contact_info' => 'nullable|numeric|digits:10',
            'code' => 'required|unique:clients,code,'.$request->id,
            'prefix' => 'required|numeric|unique:clients,prefix,'.$request->id,
            'client_logo' => 'nullable|mimes:jpg,jpeg,png'
        ],[
            'zip.required' => ' The zipcode field is required.',
            'street.required' => ' The address field is required.',
            'zip.numeric' => ' The zipcode must be a number.',
            'zip.digits' => ' The zipcode must be 5 digits.',
            'client_logo.mimes' => 'The client logo must be a file of type: jpg, jpeg, png.'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error',  'errors' => $validator->errors()->messages()], 422);
        }
        /* End Validation rule */

        try {
             $id = $request->id;
             $inputs = $request->all();
             
             $clientUpdate = (new Client)->updateClient($id ,$inputs);
             if ($request->hasFile('client_logo')) {
                 Storage::delete($request->old_url);
                 $file = $request->file('client_logo');
                 $awsFolderPath = config()->get('constants.aws_folder');
                 $filePath = config()->get('constants.CLIENT_LOGO_UPLOAD_PATH');
                 $fileName = time() . '_' . $file->getClientOriginalName();
                 $imageUploaded = $this->storageService->uploadFileToStorage($file, $awsFolderPath, $filePath, $fileName);

                 if ($imageUploaded !== false) {
                     $clientUpdate = (new Client)->updateClientLogo($id, $imageUploaded);
                 }
             }

             session()->put('success', 'Client successfully updated.');
             \Log::error("Client updated with id: " . $id);
             return response(['status' => 'success', 'message' => 'Client successfully updated.'], 200);
        } catch (\Exception $e) {
             session()->put('error', 'Something went wrong, please try again later.');
             \Log::error("Error while updating client: " . $e->getMessage());
             return response(['status' => 'error', 'message' => 'Something went wrong, please try again later.'], 200);
        }
    }

    /**
     * This function is used to update client status
     */
    public function statusupdate(Request $request)
    {
        if( $request->status == 'delete' && auth()->user()->can('delete-client')) {


            try {
                $clientId = $request->cid;
                $client = Client::find($clientId);
                
                if ($client) {

                    $workflowIds = $client->workflows->pluck('workflow_id');
                    $exists = UserTwilioId::whereIn('workflow_id',$workflowIds)->exists();
                    // for check workflow assign to tpv agent
                    if ($exists) {
                        return response()->json(['status' =>'error','message'=>'This client should not be deleted.']);
                    }

                    DB::beginTransaction();
                    
                    $client->workflows->each->delete();
                    $client->salesCenters->each->delete();
                    $client->commodity->each->delete();
                    $client->forms()->delete();
                    $client->dispositions()->delete();
                    $client->scripts()->delete();

                    ClientTwilioNumbers::where('client_id',$clientId)->delete();
                    Utilities::where('client_id',$clientId)->delete();
                    Programs::where('client_id',$clientId)->delete();
                    CustomerType::where('client_id',$clientId)->delete();
                    Brandcontacts::where('client_id',$clientId)->delete();
                    ScriptQuestions::where('client_id',$clientId)->delete();

                    // Will be deleted with related data
                    $users = User::where('client_id',$clientId)->get();
                    foreach ($users as $key => $user) {
                        $user->delete();
                    }

                    // Will be deleted with related data
                    $telesales = Telesales::where('client_id',$clientId)->get();
                    foreach ($telesales as $key => $telesale) {
                        $telesale->delete();
                    }

                    // Will be deleted with related data
                    $telesalesTmp = TelesalesTmp::where('client_id',$clientId)->get();
                    foreach ($telesalesTmp as $key => $telesaleTmp) {
                        $telesaleTmp->delete();
                    }
                    
                    $client->delete();
                    
                    // DB::table('client_agent_not_found_scripts')->where('client_id', '=', $clientid)->delete();
                    // DB::table('client_twilio_workspace')->where('client_id', '=', $clientid)->delete();
                    // DB::table('compliance_templates')->where('client_id', '=', $clientid)->delete();delete();
                    // DB::delete("delete from user_twilio_id where user_id in ( select id from users where client_id  = '". $clientid."'  )");

                    DB::commit();
                    return response()->json(['status' =>'success','message'=>'Client successfully deleted. Refreshing...']);
                } else {
                    return response()->json(['status' =>'error','message'=>'Something went wrong, please try again. Refreshing...']); 
                } 


            } catch (\Exception $e) {
                Log::error($e);
                DB::rollback();
                return response()->json(['status' =>'error','message'=>'Something went wrong, please try again.']);
            }
        }else{
            (new Client)->updateClientStatus($request->cid ,$request->status);

            if ($request->ajax()) {
                if ($request->status == config('constants.STATUS_ACTIVE')) {
                    return response()->json(['status' =>'success','message'=>'Client successfully activated.']);
                } else {
                    return response()->json(['status' =>'success','message'=>'Client successfully deactivated.']);
                }
            }
            return redirect()->back()->with('success','Client successfully updated');
        }
    }

    /***/
    public function createuser($id)
    {
        $client_id = $id;
        $client = (new Client )->getClientinfo($client_id);
        return view('client.user.create',compact('client_id','client'));
    }

    // This function is currently not in use
    public function storeuser($id,Request $request)
    {
        /* Start Validation rule */
           $validator = \Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',

        ]);

        if ($validator->fails())
        {
            return response()->json([ 'status' => 'error',  'errors'=>$validator->errors()->all()]);
        }
        /* End Validation rule */

        try{

        $input = $request->only('first_name','last_name', 'email', 'title');
        $input['parent_id'] = Auth::user()->id;
        $input['client_id'] = $id;
        $input['access_level'] = 'client';
        $input['userid'] = strtolower($request->first_name[0]);
        $input['status'] = 'inactive';
        $input['verification_code'] = str_random(20);
        $input['password'] = Hash::make(rand()); //Hash password
        $user = User::create($input);
        $user->userid = $user->userid.$user->id;
        $user->save();

        $data['verification_code']  = $user->verification_code;
        $data['email']  = $user->email;
        $data['name']  = $user->first_name;
        $data['addedby_firstname']  = Auth::user()->first_name;
        $client = Client::find($id);
        $data['addedby_company']  = $client->name;
        $data['company_id']  = $id;
        $message ='Hello '.$user->first_name.', <br><br>
        You have been added to TPV360.<br>
        Your username is: '.$user->email.'
        Please <a href="'.url('/'.$id.'/verify', ['code'=>$user->verification_code]) .'">click here</a> to generate your password.<br><br>Regards,<br><br>The TPV360 Team ';
        $to      = $user->email;
        $subject = 'Welcome to TPV360';
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        // More headers
        $headers .= 'From: <noreply@tpv.plus>' . "\r\n";
        $headers .= 'Cc: noreply@tpv.plus' . "\r\n";

        Mail::send([], [], function($mail) use ($to, $subject, $message) {
            $mail->to($to);
            $mail->subject($subject);
            $mail->setBody($message, 'text/html');
        });
        //store into database

        $textEmailStatistics = new TextEmailStatistics();
        $textEmailStatistics->type = 1;
        $textEmailStatistics->save();
        
        return response()->json([ 'status' => 'success',  'message'=>'User created successfully','url' => route('client.users',$id)]);
    } catch(Exception $e) {
     // echo 'Message: ' .$e->getMessage();
      return response()->json([ 'status' => 'error',  'errors'=> ["Something went wrong!. Please try again."]]);
    }
        // return redirect()->route('client.users',$id)
        //     ->with('success','User created successfully.');
    }
    public function users($id,Request $request)
    {
        $client_id = $id;
        $this->CheckClientUser($client_id);
        $client = (new Client )->getClientinfo($client_id);
        $client_users = User::where([
            ['client_id', '=' ,$client_id],
            ['salescenter_id', '=' ,0]
          ])->orderBy('id','DESC')->paginate(20);
        return view('client.user.list',compact('client_users','client_id','client'))
             ->with('i', ($request->input('page', 1) - 1) * 20);
    }

    public function usersNew($id=null,Request $request)
    {
        if(!empty($id)){
            $client_id = $id;
            $this->CheckClientUser($client_id);
        }

        if ($request->ajax()) {

            if(!empty($client_id)){
                $client_users = User::select(['users.*','roles.display_name as role'])
                    ->leftJoin('role_user','users.id','=', 'role_user.user_id')
                    ->leftjoin('roles','role_user.role_id','=', 'roles.id')
                    ->with('client')
                    ->where([
                        ['users.client_id', '=' ,$client_id],
                        ['users.salescenter_id', '=' ,0],
                        ['users.access_level', '=' ,'client']
                    ]);
            }else{
                $client_users = User::select(['users.*','roles.display_name as role','clients.name as client_name'])
                    ->leftJoin('role_user','users.id','=', 'role_user.user_id')
                    ->leftjoin('roles','role_user.role_id','=', 'roles.id')
                    ->leftjoin('clients','users.client_id','=', 'clients.id')
                    ->with('client')
                    ->where([
                        ['users.salescenter_id', '=' ,0],
                        ['users.access_level', '=' ,'client'],
                        ['clients.status', '=' ,'active'],
                    ]);
            }

            if(Auth::user()->isAccessLevelToClient()) {
                $client_users->where('users.client_id',Auth::user()->client_id);
            }

            // To filter by status
            if($request->status == "all"){
                // return both users (active/inactive)
            }elseif($request->status == "active"){
                $client_users->where('users.status','=',"active");
            }elseif($request->status == "inactive"){
                $client_users->where('users.status','=',"inactive");
            }


            return DataTables::of($client_users)                
                ->editColumn('profile_picture', function($user){
                    $icon = getProfileIcon($user);
                    return $icon;
                })
                ->addColumn('action', function($user){
                    $viewBtn = $editBtn =  $statusBtn = $deleteBtn = '';
                    if (auth()->user()->hasPermissionTo('view-client-user')) {
                        $viewBtn = '<a
                        class="client-user-modal btn"
                        href="javascript:void(0)"
                        data-toggle="tooltip"
                        data-placement="top" data-container="body"
                        data-type="view"
                        data-original-title="View Client User"
                        data-id="' . $user->id . '"
                        >' . getimage("images/view.png") . '</a>';
                    }else{
                        $viewBtn = getDisabledBtn();
                    }
                    if (auth()->user()->hasPermissionTo('edit-client-user') && $user->is_block != 1 && isset($user->client) && $user->client->isActive()) {
                        $editBtn = '<a
                        class="client-user-modal btn"
                        href="javascript:void(0)"
                        data-toggle="tooltip"
                        data-placement="top" data-container="body"
                        data-type="edit"
                        data-original-title="Edit Client User"
                        data-id="' . $user->id . '"
                        >' . getimage("images/edit.png") . '</a>';
                    }else{
                        $editBtn = getDisabledBtn('edit');
                    }

                    if (auth()->user()->hasPermissionTo('deactivate-client-user') && isset($user->client) && $user->client->isActive()) {
                        if ($user->status == 'active') {
                            $statusBtn = '<a
                            class="deactivate-client-user btn"
                            href="javascript:void(0)"
                            data-toggle="tooltip"
                            data-placement="top" data-container="body"
                            data-original-title="Deactivate Client User"
                            data-id="' . $user->id . '"
                            data-name="' . $user->full_name . '">'
                                . getimage("images/activate_new.png") . '</a>';
                        } else {
                            $editBtn = getDisabledBtn('edit');

                            $statusBtn = '<a
                            class="activate-client-user btn"
                            href="javascript:void(0)"
                            data-toggle="tooltip"
                            data-placement="top" data-container="body"
                            data-original-title="Activate Client User"
                            data-id="' . $user->id . '"
                            data-is-block="' . $user->is_block . '"
                            data-name="' . $user->full_name . '">'
                                . getimage("images/deactivate_new.png") . '</a>';
                        }
                    }else{
                        $statusBtn = getDisabledBtn('status');
                    }
                    if(auth()->user()->hasPermissionTo('delete-client-user')) {
                        $class = 'delete_tpv_agent';
                        $attributes = [
                            "data-original-title" => "Delete Client User",
                            "data-id" => $user->id,
                            "data-name" => $user->full_name,
                            "data-status" =>"delete",
                            "data-text-status"=>"deleted"
                            
                        ];
                        $deleteBtn = getDeleteBtn($class, $attributes);
                    } 

                    return '<div class="btn-group">'.$viewBtn.$editBtn.$statusBtn.$deleteBtn.'<div>';
                })
                ->rawColumns(['profile_picture','action'])
                ->make(true);
        }
        
        $roles = (new Role)->getRolesForClientUser();
        return view('admin.users.client.index',compact('roles'));
    }

    /**
     * This function is used to change user status
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

        try{
            $user=User::find($request->id);
            if($request->status == 'delete')
            {
                if($user->access_level == 'tpvagent'){
                    if($request->route()->getName() == 'agent.user.changeUserStatusForAllAgent') {
                        $message = "Agent ".$request->name ." successfully deleted.";
                    } else {
                        $message = "TPV Agent successfully deleted.";
                    }
                }
                elseif($user->access_level == 'client')
                {
                    $message='Client user successfully deleted.';
                }
                else if($user->access_level == 'tpv') 
                    $message='TPV user successfully deleted.';

                SalesAgentDetail::where('user_id',$request->id)->delete();
                User::where('id',$request->id)->delete();
            }
            else
            {
                if ($user->access_level == 'client' && isset($user->client) && !$user->client->isActive()) {
                    return response()->json(['status' => 'error', 'message' => "You can't change anything due to client is inactive."]);
                }
    
                $user->status = $request->status;
                $user->deactivationreason = $request->comment;
                $user->is_block = $request->input('is_block', 0);
                $user->save();
    
                if($user->access_level == 'tpv') {
                    $userType='TPV user';
                }else if($user->access_level == 'tpvagent') {
                    if($request->route()->getName() == 'agent.user.changeUserStatusForAllAgent') {
                        $userType='Agent '.$user->full_name;
                    } else {
                        $userType='TPV Agent';
                    }
                } else {
                    $userType='Client user';
                }
                if ($request->status =='active') {
                    $message=$userType.' successfully activated.';
                } else {
                    $message=$userType.' successfully deactivated.';
                }
            }
            return response()->json([ 'status' => 'success',  'message'=>$message]);
        } catch(\Exception $e) {
            Log::error($e);
            return response()->json([ 'status' => 'error',  'message'=>'Something went wrong!. Please try again.']);
        }

    }

    /**
      * For add or update model(pop-up) form data of Client users
      * Ajax call of route : client.user.createOrUpdate, admin.client.users.StoreOrEdit
      * 
      * @param $request, $client_id
      * @return Response
      * 
      */
    public function createOrUpdateUser($client_id=null,Request $request)
    {
        $id= $request->id;
        /* Start Validation rule */
        $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'title' => 'required|max:255',
            'role' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$id.',id,deleted_at,NULL',
        ],[
            'email.unique'=>'This email is taken',
            'first_name.required' => 'This field is required',
            'last_name.required' => 'This field is required',
            'title.required' => 'This field is required',
            'role.required' => 'This field is required',

        ]);
        /* End Validation rule */

        try{

            $input = $request->only('first_name','last_name','title', 'email');
            if(!empty($client_id) && $client_id != null ){
                $input['client_id'] = $client_id;
            }else{
                $input['client_id'] = $request->client_id;
            }
            if (empty($id)) {
                $next_user_id = (new User)->nextAutoID();
                $input['parent_id'] = Auth::user()->id;
                $input['access_level'] = 'client';
                $input['userid'] = strtolower($request->first_name[0]).$next_user_id;
                $input['status'] = 'active';
                $input['verification_code'] = str_random(20);
                $input['password'] = Hash::make(str_random(8));
                $user = User::create($input);
                $user->attachRole($request->role);

                // for send verification email
                $this->sendVerificationEmail($user);
                Log::info("Successfully created new Client user.");
                return response()->json([ 'status' => 'success',  'message'=>'User successfully created.']);
            } else {
                
                User::where('id',$id)->update($input);
                $user=User::find($id);
                Log::info("Id is exist, so need to update details of Client user id : ".$id);
                $user->roles()->sync([$request->role]);
                Log::info("Successfully updated details of Client user, id : ".$id);
                return response()->json([ 'status' => 'success',  'message'=>'User successfully updated.']);
            }


        } catch(\Exception $e) {
            Log::error("In Exception of createOrUpdateUser method : Something went wrong!");
            Log::error($e);
            return response()->json([ 'status' => 'error',  'message'=> "Something went wrong, please try again."]);
        }

    }

    /**
     * This function is used to get user
     */
    public function getUser($client_id=null,Request $request)
    {
        if ($client_id != null){
            $user = (new User)->getClientUsers($client_id,$request->user_id);
        }else{
            $user = (new User)->getUser($request->user_id);
        }
        return response()->json([ 'status' => 'success',  'data'=>$user]);
    }

    /**
     * This function is used to show user details
     */
    public function showuser($client_id,$userid)
    {
        $this->CheckClientUser($client_id);
        $client = (new Client )->getClientinfo($client_id);
        $user = (new User)->getClientUsers($client_id,$userid);
        return view('client.user.show',compact('user','client_id','client'));
    }

    /**
     * This function is used to show edit user details
     */
    public function edituser($client_id,$userid)
    {
        $this->CheckClientUser($client_id);
         $user = 	User::where([
            ['client_id', '=', $client_id],
            ['id', '=', $userid],
          ])->firstOrFail();
          $client = (new Client )->getClientinfo($client_id);
         return view('client.user.edit',compact('user','client_id','client'));
    }

    /**
     * This function is used to update user 
     */
    public function updateuser($company_id,$userid,Request $request)
    {
        $this->CheckClientUser($company_id);
        /* Start Validation rule */
        $this->validate($request, [
            'first_name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,'.$userid,
            'password' => 'confirmed',
           ]);
        /* End Validation rule */
           $user = User::find($userid);
           $user->first_name = $request->first_name;
           $user->last_name = $request->last_name;
           $user->email = $request->email;
           $user->title = $request->title;
           $user->status = $request->status;
           if(!empty($request->password)){
            $user->password = Hash::make($request->password); //update the password
           }
           $user->save();
           return redirect()->back()
           ->with('success','User successfully updated');
    }

    /**
     * This function used to update user status
     */
    public function updateuserstatus($client_id,Request $request)
    {
           $this->CheckClientUser($client_id);
           $user = User::find($request->userid);
           $user->status = $request->status;
           $user->save();
           return redirect()->route('client.users',$client_id)
           ->with('success','User successfully updated.');
    }

    /**
     * This function is used to show salescenter list
     */
    public function salescenters($id,Request $request)
    {

        $client_id = $id;
        $this->CheckClientUser($client_id);

        $client_salescenters = Salescenter::where([
            ['client_id', '=' ,$client_id]
        ])->orderBy('id','DESC')->paginate(20);


        return view('client.salescenter.list',compact('client_salescenters','client_id'))
             ->with('i', ($request->input('page', 1) - 1) * 20);
    }

    /**
     * This function is used to check client user
     */
    function CheckClientUser($client_id){
          if(Auth::user()->access_level == 'client' ){
              if(Auth::user()->client_id != $client_id ){
                  abort(403);
              }
          }
    }

    /**
     * This function is used to create contact pagelayout form
     */
    public function contactpagelayout($clientId, $formId)
    {

        
        // $this->CheckClientUser($client_id);
       // $ClientsFields = (new ClientsForms)->getClientFormFields($formid);
       // $client = (new Client )->getClientinfo($client_id);

       // if( isset($ClientsFields[0]->commodity_type) && $ClientsFields[0]->commodity_type !="" ){
       //  $formtype = $ClientsFields[0]->commodity_type;
       // }else{
       //  $formtype = "GasOrElectric";
       // }



       // $utilities = (New Utilities)->getClientAllUtilities($client_id);

       // $programs = array();
       // $utility = array();
    //    if( $ClientsFields[0]->utility_id > 0 ){
    //       $utility =  (New Utilities)->getUtility($ClientsFields[0]->utility_id);

    //      $programs =  (new Programs)->getAllPrograms_using_utility_shortname($client_id,$utility->utilityshortname );
    //    }
       // $workspace_ids = (new ClientWorkspace)->getClientWorkspaceIds($client_id);
       // $workflow_ids =array();
       // if( $ClientsFields[0]->workspace_id ){
       //  $workflow_ids =    (new ClientWorkflow)->getClientWorkflowIdsUsingWorkspaceID($ClientsFields[0]->workspace_id);

       // }

        $this->CheckClientUser($clientId);
        $client = Client::find($clientId);
        $form = Clientsforms::with('commodities', 'fields')->with(['fields' => function($q) {
            $q->orderBy('position', 'asc');
        }])->find($formId);
        $formFields = config('constants.newFormFields');
        $workSpaces = (new ClientWorkspace)->getClientWorkspaceIds($clientId);
        $workflows = ClientWorkflow::where('client_id', $clientId)->where('workspace_id', array_get($form, 'workspace_id'))->get();
        $commodities = Commodity::where('client_id', $clientId)->get();

        return view('client.forms.createform', compact('client', 'formFields', 'workSpaces', 'commodities', 'form', 'workflows'));
    }

    /**
     * This function is used to clone contact page layout
     */
    public function contactpagelayoutClone($clientId, $formId)
    {
        
        $this->CheckClientUser($clientId);
        $client = Client::find($clientId);
        $form = Clientsforms::with('commodities', 'fields')->with(['fields' => function($q) {
            $q->orderBy('position', 'asc');
        }])->find($formId);
        if(!empty($form)){

            $split = explode(" ", $form->formname);
            $lastIndex = count($split)-1;
            $lastWord = $split[$lastIndex];
            $count=2;
            for ($count; $count < 100 ; $count++) {
                $split[$lastIndex]=$lastWord.'_'.$count;
                $cloneFormName=implode(" ", $split);
                $cloneFormNameCount = Clientsforms::where('formname',$cloneFormName)->count();
                if($cloneFormNameCount === 0) {
                    break ;
                }
            }

            $form['formname'] = $cloneFormName;
          // dd($cloneFormName);
        }
        $formFields = config('constants.newFormFields');
        $workSpaces = (new ClientWorkspace)->getClientWorkspaceIds($clientId);
        $workflows = ClientWorkflow::where('client_id', $clientId)->where('workspace_id', array_get($form, 'workspace_id'))->get();
        $commodities = Commodity::where('client_id', $clientId)->get();

        return view('client.forms.createform', compact('client', 'formFields', 'workSpaces', 'commodities', 'form', 'workflows'));
    }

    /**
     * This function is used to create contact form
     */
    public function contactpagecreate($clientId, Request $request)
    {
        $this->CheckClientUser($clientId);
        $client = Client::find($clientId);
        $formFields = config('constants.newFormFields');
        $workSpaces = (new ClientWorkspace)->getClientWorkspaceIds($clientId);
        $form = [];
        $commodities = Commodity::where('client_id', $clientId)->get();
        return view('client.forms.createform', compact('client', 'formFields', 'workSpaces', 'commodities', 'form'));
    }



    /**
     * This function is used to store contact page layout form
     */
    public function savecontactpagelayout($client_id, Request $request)
    {
        /* Start Validation rule */
        $this->validate( $request,[
            'workspace_id' => 'required',
            'workflow_id' => 'required',
            'formname' => 'required',
            'commodity_type' => 'required',
            ]
        );
        /* End Validation rule */

        $input['fields'] = $request->fields;
        $input['client_id'] = $client_id;
        $input['created_by'] = Auth::user()->id;
        $input['created_at'] = date('Y-m-d H:i:s');
        $input['utility_id'] = $request->utility;
        //$input['program_id'] = $request->program_id;
        $input['formname'] = $request->formname;
        $input['workspace_id'] = $request->workspace_id;
        $input['workflow_id'] = $request->workflow_id;
        $input['commodity_type'] = $request->commodity_type;
        $CompaniesForms = (new Clientsforms)->createForm($input);

        $message = 'Form successfully created.';

        return redirect()->route('client.contact-page-layout',['id' => $client_id, 'formid' => $CompaniesForms])
        ->with('success',$message);
    }


    /**
     * This function is used to update contact page layout
     */
    public function updatecontactpagelayout($client_id, $formid, Request $request)
    {

        $input['fields'] = $request->fields;
        $input['client_id'] = $client_id;
        $input['created_by'] = Auth::user()->id;
        $input['created_at'] = date('Y-m-d H:i:s');
        $input['utility_id'] = $request->utility;
        //$input['program_id'] = $request->program_id;
        $input['formname'] = $request->formname;
        $input['workspace_id'] = $request->workspace_id;
        $input['workflow_id'] = $request->workflow_id;

        
       if($request->formid > 0){
        $CompaniesForms = (new Clientsforms)->updateForm($request->formid,$input);
        $message = 'Form successfully updated.';
       }else{
        $CompaniesForms = (new Clientsforms)->createForm($input);
        $message = 'Form successfully created.';
       }
        return redirect()->back()
        ->with('success',$message);
    }

    /**
     * This function is used to remove contact form
     */
    public function deletecontactform($client_id, Request $request){
       if(isset($request->id) && !empty($request->id) ){
          (new Clientsforms)->deleteForm($request->id);
        $message = "Form successfully deleted";
        return redirect()->route('client.contact-forms',$client_id)
        ->with('success',$message);

       }else{
           abort(403);
       }

    }

    public function contactform($client_id,Request $request){
        if(auth()->user()->access_level == 'salesagent' && auth()->user()->roles->isEmpty()) {
            $client = Client::findOrFail($client_id);

            $clientForms = Clientsforms::where('client_id', $client->id)->whereIn('channel', ['WEB', 'BOTH'])->where('status', 'active')->get();

            return view('frontend.client.lead_forms', ['client' => $client, 'forms' => $clientForms]);
        }else if(auth()->user()->access_level == 'tpvagent'){
            return redirect()->route('tpvagents.sales');
        }else{
            return redirect()->route('dashboard');
        }
    }    
    public function designFrom(Request $request,$client_id, $form_id){
        $client = Client::findOrFail($client_id);
        $customFields = getEnableCustomFields($client_id);
        
        // $form = Clientsforms::findOrFail($form_id);
        $form = Clientsforms::with(['fields' => function($q) {
            $q->orderBy('position', 'asc');
        }])->findOrFail($form_id);
        
        $zipcode = '';
        $fields = '';
        $commodities = '';
        $clonedData = '';
        $clonedChildData = array();
        $states = [];
        $childFields = '';

        /* for check is allow enrollment by state or not */
        if (isEnableEnrollByState($client->id)) {
            /* for get states has utility by commodity for client */
            $states = $this->getUtilityStates($form->commodities->pluck('id'));
        }

        if (isset($request->lid) && !empty($request->lid)){
            // for check clone settings is on or off
            if (!isOnSettings($client_id, 'is_enable_clone_lead')) {
                return back()->with('error','Lead clone settings is switch off. Please contact your administrator for assistance.');
            }

            $lead = Telesales::findOrFail(base64_decode($request->lid));
            if($lead->is_multiple == 1 && $lead->multiple_parent_id == 0){
                $getAllChildLead = $lead->getChildLeads($lead->id);
                
            }

            $zipcodeData = $lead->zipcodes()->first();

            if (empty($zipcodeData)){
                return back()->withErrors(['zipcode' => 'Invalid Zip code.'])->withInput($request->all());
            }

            $zipcode = $zipcodeData->zipcode;
            if($lead->is_enrollment_by_state) {
                $state = $this->getLeadState($lead->id, $lead->form_id);
                $zipcodeIds = Zipcodes::where('state',$state)->pluck('id');
            } else {
                $zipcodeIds = array($zipcodeData->id);
            }
            $commodities = $form->commodities;
            $programIds = $this->getProgramIds();
            
            foreach ($commodities as $commodity){
                $commodity->utilities = Utilities::getUtilityByCommodityAndMapping($commodity->id,$zipcodeIds, $programIds);
            }
            
            $fields = $form->fields;
            $childForm = Clientsforms::with(['fields' => function($q) {
                        $q->where('is_multienrollment', '1');
                        $q->orderBy('position', 'asc');
                    }])->findOrFail($form_id);
                $childFields = array_get($childForm, 'fields');
            
            $clonedData = [];
            $clonedChildData = [];
            $clonedData['parent_id'] = $lead->id;

            $selectedUtility = $lead->programs()->pluck('utility_id')->toArray();
            $programsIds = $lead->programs()->pluck('program_id')->toArray();
            if(!empty($getAllChildLead)){
                foreach($getAllChildLead as $childLead){
                    $selectedChildUtility[$childLead->id] = $childLead->programs->pluck('utility_id')->toArray();
                    $selectedChildProgramsIds[$childLead->id] = $childLead->programs->pluck('id')->toArray();
                }
            }
            

            $i = 0;
            foreach ($selectedUtility as $u_id) {
                $utility = Utilities::findOrFail($u_id);
                // Log::info('Utilities details..');
                // Log::info($utility);
                $clonedData[$utility->utilityCommodity->name] = [
                    'utility_selected_id' => $utility->id,
                    'programs' => $utility->programs()->get(),
                    'program_selected_id' => $programsIds[$i],
                    'brand_selected_name' => isset($utility->brandContacts) ? $utility->brandContacts->name : ''
                ];
                $i++;
            }

            $x = 0;
            
            if(!empty($selectedChildUtility)){
                foreach($selectedChildUtility as $key => $value){
                    
                    foreach ($selectedChildUtility[$key] as $u_c_id) {
                        $utility = Utilities::findOrFail($u_c_id);
                        $clonedChildData[$key][$utility->utilityCommodity->name] = [
                            'utility_selected_id' => $utility->id,
                            'programs' => $utility->programs()->get(),
                            'program_selected_id' => $selectedChildProgramsIds[$key][0],
                            'brand_selected_name' => isset($utility->brandContacts) ? $utility->brandContacts->name : ''
                        ];
                        $x++;
                    }
                }
            }
            $leadData = $lead->teleSalesData->groupBy('field_id');
            if(!empty($getAllChildLead)){
                foreach($getAllChildLead as $childLead){
                    $childleadData[$childLead->id] = $childLead->teleSalesData->groupBy('field_id');
                }
            }
            
            foreach ($leadData as $key => $value) {
                if ($value->count() > 0){
                    $array = [];
                    foreach ($value as $_value){
                        $array[$_value->meta_key] = $_value->meta_value;
                    }
                    $clonedData[$key] = $array;
                } else {
                    $clonedData[$key] = $value->first()->meta_value;
                }
            }
            

            if(!empty($childleadData)){
                foreach($childleadData as $keyValue => $childleadDataArray){
                    foreach ($childleadData[$keyValue] as $key => $value) {
                        if ($value->count() > 0){
                            $array = [];
                            foreach ($value as $_value){
                                $array[$_value->meta_key] = $_value->meta_value;
                            }
                            $clonedChildData[$keyValue][$key] = $array;
                        } else {
                            $clonedChildData[$keyValue][$key] = $value->first()->meta_value;
                        }
                    }
                }
            }
        }
        
        \Log::info($clonedData);
        return view('frontend.client.forms', ['client' => $client,'form' => $form, 'zipcode' => $zipcode, 'commodities' => $commodities, 'fields' => $fields, 'clonedData' => $clonedData, 'states' => $states, 'clonedChildData' => $clonedChildData, 'childFields' => $childFields,'customFields' => $customFields]);
    }

    public function designFromPost(Request $request,$client_id, $form_id){
     
        $forms = Clientsforms::find($form_id);
        $clonedChildData = array();
        if(!isset($forms) && empty($forms))
        {
            return redirect()->route('client.contact',$client_id)->with('error',' You cannot create lead of this form,as form is deleted.');
        }
        $client = Client::findOrFail($client_id);
        $form = Clientsforms::with(['fields' => function($q) {
                    $q->orderBy('position', 'asc');
                }])->findOrFail($form_id);

        $zipcode = '';
        $fields = '';
        $commodities = '';
        $requestPhone = '';
        $alerts = null;
        $isEnrollmentByState = 0;
        $states = [];
        $programIds = $this->getProgramIds();
        
        /* check is allow enrollment by state or not */
        if (isEnableEnrollByState($client->id)) {
            /* for get states has utility by commodity for client */
            $states = $this->getUtilityStates($form->commodities->pluck('id'));
        }
        if (isset($request->zipcode) && !empty($request->zipcode)) {
            $zipcode = $request->zipcode;

            // $zipcodeData = Zipcodes::where('zipcode', $zipcode)->first();
            $salesagentdetails = Salesagentdetail::where('user_id',\Auth::user()->id)->first();
                $zipcodeData = Zipcodes::where('zipcode',$zipcode);
                if(strlen($salesagentdetails->restrict_state) > 0)
                {
                    $explodeData = explode(",",$salesagentdetails->restrict_state);
                    $zipcodeData = $zipcodeData->whereIn('state',$explodeData);
                }
                $zipcodeData = $zipcodeData->first();
            if (empty($zipcodeData)){
                return back()->withErrors(['zipcode' => 'Invalid Zip code.'])->withInput($request->all());
            }
            $zipcodeIds = array($zipcodeData->id);
            $commodities = $form->commodities;

            foreach ($commodities as $commodity){
                $commodity->utilities = Utilities::getUtilityByCommodityAndMapping($commodity->id,$zipcodeIds, $programIds);
            }
            $fields = array_get($form, 'fields');
        }

        if (isset($request->state) && !empty($request->state)) {
            $isEnrollmentByState = 1;
            $state = $request->state;
            $zipcodeIds = Zipcodes::where('state',$state)->pluck('id');
            $commodities = $form->commodities;

            foreach ($commodities as $commodity){
                $commodity->utilities = Utilities::getUtilityByCommodityAndMapping($commodity->id,$zipcodeIds, $programIds);

            }
            $fields = array_get($form, 'fields');
        }   
       
        if (isset($request->lead_from) && !empty($request->lead_from)){
            
            try {
                
                if($request->multi_enrollment == 0 && $request->total_enrollment == 1){
                    
                    $zipcode = $request->zipcode;
                    \Log::info('zipcode: '.$zipcode);
                    $zipcodeData = Zipcodes::where('zipcode', $zipcode)->first();

                    \Log::info('zipcodeData: '.$zipcodeData);

                    if (empty($zipcodeData)) {
                        return back()->withErrors(['zipcode' => 'Invalid Zip code.'])->withInput($request->all());
                    }

                    if (is_array($request->program)) {
                        foreach ($request->program as $pId) {
                            $program = Programs::find($pId);
                            if (empty($program)) {
                                return back()->with("error", 'This program was not found.');
                            }
                        }
                    } else {
                        $program = Programs::find($request->program);
                        if (empty($program)) {
                            return back()->with("error", 'Please select a valid program.' . $request->program);
                        }
                    }
                    // dd($request->all());

                    $leadData = [];
                    $leadData['client_id'] = $client_id;
                    $leadData['form_id'] = $form_id;
                    $leadData['user_id'] = Auth::user()->id;
                    $leadData['is_enrollment_by_state'] = $request->is_enrollment_by_state;
                    $leadData['program'] = isset($request->program[0]) ? implode(',',$request->program[0]) : NULL;
                    // $referenceId = (new TelesalesTmp)->generateReferenceId();
                    $clientPrefix = (new Client())->getClientPrefix($client_id);
                    $referenceId = (new TelesalesTmp)->generateNewReferenceId($client_id,$clientPrefix);

                    $isOnAlertTele = isOnSettings($client_id,'is_enable_alert_tele');

                    // $check_verification_number = 2;
                    // $validate_num = $verification_number = "";
                    // while ($check_verification_number > 1) {
                    //     $verification_number = rand(1000000, 9999999);
                    //     $validate_num = (new TelesalesTmp)->validateConfirmationNumber($verification_number);
                    //     if (!$validate_num) {
                    //         $check_verification_number = 0;
                    //     } else {
                    //         $check_verification_number++;
                    //     }
                    // }

                    $leadData['refrence_id'] = $referenceId;
                    $leadData['is_multiple'] = 0;
                    $leadData['multiple_parent_id'] = 0;
                    // $leadData['verification_number'] = $verification_number;

                    if (isset($request->parent_id) && !empty($request->parent_id)){
                        $leadData['parent_id'] = $request->parent_id;
                        $leadData['cloned_by'] = Auth::user()->id;
                    } else {
                        $leadData['parent_id'] = 0;
                        $leadData['cloned_by'] = 0;
                    }
                    $leadData['zipcode'] = $zipcode;

                    \Log::info('$leadData'.print_r($request->all(),true));
                    // \Log::info('$leadData'.print_r($leadData,true));
                    $telesaleTmp = TelesalesTmp::create($leadData);


                    $temp_lead_id = $telesaleTmp->id;
                    $fields = $request->fields[0];
                    if (isset($request->parent_id) && !empty($request->parent_id)){
                        $leadCloneMessage = __('critical_logs.messages.Event_Type_31');
                        $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_31');
                        $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                        $criticalLogs = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$leadCloneMessage,null,$temp_lead_id,$request->parent_id,$lead_status,$event_type);
                    }
                    foreach ($fields as $field) {

                        if($field['field_type'] == 'checkbox') {
                            if (isset($field['value']) && !empty($field['value'])) {
                                $value = implode(', ', $field['value']);
                                $telesaleTmp->teleSalesData()->create([
                                    'meta_key' => 'value',
                                    'meta_value' => $value,
                                    'field_id' => $field['field_id']
                                ]);
                            }
                        } else if ($field['field_type'] != "separator" && $field['field_type'] != "heading" && $field['field_type'] != "label" && $field['field_type'] != 'checkbox') {
                            $values = $field['value'];
                            if ($values != NULL) {

                                if(is_array($values)) {
                                    foreach ($values as $key => $value) {
                                        $telesaleTmp->teleSalesData()->create([
                                            'meta_key' => $key,
                                            'meta_value' => $value,
                                            'field_id' => $field['field_id']
                                        ]);
                                    }
                                } else {
                                    $telesaleTmp->teleSalesData()->create([
                                        'meta_key' => $field['field_type'],
                                        'meta_value' => $values,
                                        'field_id' => $field['field_id']
                                    ]);
                                }

                            }
                        }  else {
                            continue;
                        }
                    }

                    \Log::info('Request All:'.print_r($request->all(),true));
                    $form = Clientsforms::with('fields')->find($form_id);

                    $requestFields = $request->fields[0];

                    $validationData = array();

                    //For resolve undefined variable error
                    $teleSaleId = isset($telesale->id) ? $telesale->id : false;

                    $call = $request->calltype;

                    $ref = $referenceId;
                    $cn = isset($contact_numbers) ? $contact_numbers : false;
                    $telesaleTmpId = isset($telesaleTmp->id) ? $telesaleTmp->id : false;

					// for check account number exists or not in do not enroll list
					$exists = $this->isExistsInDNE($client_id, $form_id, $requestFields);
					if($exists) {
						// Account number is exists in do not enrollment list.
						$alerts = 'This customer cannot be enrolled with '.$client->name.'. Please contact '.$client->name.' for more details.';
						\Log::info("Account number is exists in do not enrollment list.So ".$alerts);

						$disposition = Dispositions::where("client_id",$client_id)->where('type','do_not_enroll')->first();
						$dispositionId = array_get($disposition,'id');
						return redirect()->route('client.cancelnewLead', ['telesaleTmpId' => $telesaleTmpId, 'id' => $client_id, 'call' => $call, 'ref' => $ref, 'cn' => $cn, 'message' => $alerts,'disposition'=>$dispositionId]);
					}

                    $accountNumberLabel = config('constants.ACCOUNT_NUMBER_LABEL').'%';
                    $accountNumField = $form->fields()->where(\DB::raw('LOWER(label)'),'LIKE',$accountNumberLabel)->where('type', 'textbox')->first();
                    // \Log::info('$accountNumField'.print_r($accountNumField,true));
                    
                    $aLeadStatus = array();
                    $aLeadStatus['pending'] = 0;
                    $aLeadStatus['verified'] = 0;
                    $aLeadStatus['decline'] = 0;
                    $aLeadStatus['hangup'] = 0;
                    $aLeadStatus['cancel'] = 0;
                    $aLeadStatus['expired'] = 0;
                    $lead_ids = "";
                    $totCount = 0;
                    $aVerifiedLeadData = array();
                    if (!empty($accountNumField)) {
                        //Check for Duplicate Account number Validation
                        $requestFields = $request->fields[0];
                        $accountIndices = array_keys(array_column($requestFields, 'field_id'),$accountNumField->id);
                        // \Log::info('$accountIndices'.print_r($accountIndices,true));
                        $requestAccountNumber = '';
                        foreach($accountIndices as $index){
                            // \Log::info('$requestFields[$index][value]'.print_r($requestFields[$index]['value'],true));
                            if(isset($requestFields[$index]['value']['value']) && $requestFields[$index]['value']['value']){
                                $requestAccountNumber = $requestFields[$index]['value']['value'];
                            }
                        }
                        \Log::info('$requestAccountNumber'.$requestAccountNumber);
						
                        //Duplicate Account number Validation
                        $fieldIds = FormField::where(\DB::raw('LOWER(label)'),'LIKE',$accountNumberLabel)->where('type','textbox')->pluck('id')->toArray();
                        $teleSalesData = Telesalesdata::where('meta_key','value')->where('meta_value',$requestAccountNumber)->whereIn('field_id',$fieldIds)->pluck('telesale_id')->toArray();
                        /* Get setting value by key */
                        $intervalDays = getSettingValue($client_id,'interval_days_alert1_tele',null);
                        $teleSales = Telesales::whereIn('id',$teleSalesData);
                        if (!empty($intervalDays) && $intervalDays > 0) {
                            $intervalDate = today()->subDays($intervalDays);
                            $teleSales->whereDate('created_at','>=',$intervalDate);
                        }
                        $teleSales = $teleSales->get();
                        // \Log::info('$requestAccountNumberMatch'.print_r($teleSales,true));                   

                        foreach($teleSales AS $teleSale){
                            $lead_ids .= $teleSale->id . ",";
                            if($teleSale->status == 'verified'){
                                $aLeadStatus['verified'] = 'Verified Leads: '.isset($aLeadStatus['verified']) ? $aLeadStatus['verified'] + 1 : 1;
                            }

                            if($teleSale->status == 'pending'){
                                $aLeadStatus['pending'] = isset($aLeadStatus['pending']) ?  $aLeadStatus['pending'] + 1 : 1;
                            }

                            if($teleSale->status == 'decline'){
                                $aLeadStatus['decline'] = isset($aLeadStatus['decline']) ? $aLeadStatus['decline'] + 1 : 1;
                            }

                            if($teleSale->status == 'hangup'){
                                $aLeadStatus['hangup'] = isset($aLeadStatus['hangup']) ? $aLeadStatus['hangup'] + 1 : 1;
                            }

                            if($teleSale->status == 'cancel'){
                                $aLeadStatus['cancel'] = isset($aLeadStatus['cancel']) ? $aLeadStatus['cancel'] + 1 : 1;
                            }

                            if($teleSale->status == 'expired'){
                                $aLeadStatus['expired'] = isset($aLeadStatus['expired']) ? $aLeadStatus['expired'] + 1 : 1;
                            }

                            $totCount = $totCount + 1;
                        }
                        if($totCount >= getSettingValue($client_id,'max_times_alert1_tele') && $isOnAlertTele && isOnSettings($client_id,'is_enable_alert1_tele')){
                            if($totCount > 1) {
                                $time= ' times.';
                            } else {
                                $time= ' time.';
                            }
                            $isAlert1Critical = isOnSettings($client_id, 'is_critical_alert1_tele');
                            $alerts = 'Sales agent used an account number that has been used '.$totCount.$time;
                            $message = __('critical_logs.messages.Event_Type_5',['count'=>$totCount]);
                            $lead_ids = rtrim($lead_ids,","); 
                            $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                            $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_5');
                            $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                            $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$message,null,$temp_lead_id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alerts);
                            
                            //Check if alert is critical or not
                            if ($isAlert1Critical) {
                                return redirect()->route('client.cancelnewLead', ['telesaleTmpId' => $telesaleTmpId, 'id' => $client_id, 'call' => $call, 'ref' => $ref, 'cn' => $cn,'message' => $alerts]);
                            } else if(isOnSettings($client_id,'is_show_agent_alert1_tele')) {
                                $validationData['acc']['title'] = 'This Account number has been used '.$totCount.' times.';
                                $validationData['acc']['msg'] = 'Verified Leads: '.$aLeadStatus['verified'] .' Pending Leads: '.$aLeadStatus['pending'].' Declined Leads: '.$aLeadStatus['decline'].' Disconnected Calls: '.$aLeadStatus['hangup'].' Cancelled Leads: '.$aLeadStatus['cancel'].' Expired Leads: '.$aLeadStatus['expired'];
                            }
                        }

                        // for check settings is on or off
                        if ($isOnAlertTele && isOnSettings($client_id,'is_enable_alert2_tele')) {

                            $intervalDays = getSettingValue($client_id,'interval_days_alert2_tele',null);
                            $teleSales = Telesales::whereIn('id',$teleSalesData)->where('status','verified');
                            if (!empty($intervalDays) && $intervalDays > 0) {
                                $intervalDate = today()->subDays($intervalDays);
                                $teleSales->whereDate('created_at','>=',$intervalDate);
                            }
                            $aVerifiedLeadData = $teleSales->pluck('id');                                        
                        
                            $verifiedTelesalesData = Telesalesdata::where(function($query) use ($aVerifiedLeadData){
                                                        $query->where('meta_key','first_name')
                                                        ->orWhere('meta_key','last_name');
                                                    })
                                                    ->whereIn('telesale_id',$aVerifiedLeadData)
                                                    ->whereHas('formFieldsData',function($query) {
                                                        $query->where('is_primary',1);
                                                    })
                                                    ->get();
                            $firstName = '';
                            $lastName = '';

                            //Check for primary first name anddd last name
                            $requestFields = $request->fields[0];
                            $fullnameIndices = array_keys(array_column($request->fields[0], 'field_type'),'fullname');
                            $requestFullName = '';
                            foreach($fullnameIndices as $index){
                                if(isset($requestFields[$index]['value']['is_primary']) && $requestFields[$index]['value']['is_primary'] == 1){
                                        $requestFullName = (isset($requestFields[$index]['value']['first_name']) && isset($requestFields[$index]['value']['last_name'])) ? $requestFields[$index]['value']['first_name'].' '.$requestFields[$index]['value']['last_name'] : '';
                                }
                            }
                            $lead_ids = "";
                            $critical_message = "";

                            foreach($verifiedTelesalesData AS $verifiedTelesale)
                            {
                                
                                $verifiedTelesalesFirstName = Telesalesdata::where('meta_key','first_name')->where('telesale_id',$verifiedTelesale->telesale_id)->first();
                                $verifiedTelesalesLastName = Telesalesdata::where('meta_key','last_name')->where('telesale_id',$verifiedTelesale->telesale_id)->first();

                                $firstName = $verifiedTelesalesFirstName->meta_value;
                                $lastName = $verifiedTelesalesLastName->meta_value;
                                $fullName = $firstName .' '.$lastName;
                                if($requestFullName == $fullName){
                                    $lead_ids .= $verifiedTelesale->telesale_id .",";   
                                }
                            }
                            $lead_ids = implode(',',array_unique(explode(',',$lead_ids)));
                            foreach($verifiedTelesalesData AS $verifiedTelesale){
                            
                            $verifiedTelesalesFirstName = Telesalesdata::where('meta_key','first_name')->where('telesale_id',$verifiedTelesale->telesale_id)->first();
                            $verifiedTelesalesLastName = Telesalesdata::where('meta_key','last_name')->where('telesale_id',$verifiedTelesale->telesale_id)->first();

                            $firstName = $verifiedTelesalesFirstName->meta_value;
                            $lastName = $verifiedTelesalesLastName->meta_value;
                            $fullName = $firstName .' '.$lastName;
                            if($requestFullName == $fullName){
                                $alerts = 'Sales agent submitted an enrollment for an existing customer.';
                                $critical_message =  __('critical_logs.messages.Event_Type_6');
                                if(isOnSettings($client_id,'is_show_agent_alert2_tele')) {
                                    $validationData['name']['title'] = 'This Customer is already enrolled with '.$client->name;
                                    $validationData['name']['msg'] = 'There is a verified enrollment associated with this customer and account number.';
                                }
                                
                                break;
                            }else{
                                continue;
                            }
                            }
                            
                            if($lead_ids !="")
                                $lead_ids = $lead_ids;
                            if($critical_message != ""){
                                $isTeleAlert2Critical = isOnSettings($client_id, 'is_critical_alert2_tele');
                                $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                                $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_6');
                                $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                                $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$critical_message,null,$temp_lead_id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alerts);
                                
                                //Check if alert is critical or not
                                if ($isTeleAlert2Critical) {
                                    return redirect()->route('client.cancelnewLead', ['telesaleTmpId' => $telesaleTmpId, 'id' => $client_id, 'call' => $call, 'ref' => $ref, 'cn' => $cn,'phone' => $requestPhone, 'message' => $alerts]);
                                }

                            }
                            
                            // \Log::info('$verifiedTelesalesData$validationData'.print_r($validationData,true));
                        }

                        if($isOnAlertTele && isOnSettings($client_id,'is_enable_alert8_tele',false)){
                            $requestFields = $request->fields[0];
                            $accountIndices = array_keys(array_column($request->fields[0], 'field_id'),$accountNumField->id);
                            // \Log::info('$accountIndices'.print_r($accountIndices,true));
                            $requestAccountNumber = '';
                            foreach($accountIndices as $index){
                                // \Log::info('$requestFields[$index][value]'.print_r($requestFields[$index]['value'],true));
                                if(isset($requestFields[$index]['value']['value']) && $requestFields[$index]['value']['value']){
                                    $requestAccountNumber = $requestFields[$index]['value']['value'];
                                }
                            }
                            // \Log::info('$requestAccountNumber'.$requestAccountNumber);
                            //Duplicate Account number Validation
                            $fieldIds = FormField::where(\DB::raw('LOWER(label)'),'LIKE',$accountNumberLabel)->where('type','textbox')->pluck('id')->toArray();
                            $teleSalesData = Telesalesdata::where('meta_key','value')->where('meta_value',$requestAccountNumber)->whereIn('field_id',$fieldIds)->pluck('telesale_id')->toArray();
                            $intervalDays = getSettingValue($client_id,'interval_days_alert8_tele',null);
                            $teleSales = Telesales::whereIn('id',$teleSalesData)->where('status',config('constants.LEAD_TYPE_VERIFIED'));
                            if (!empty($intervalDays) && $intervalDays > 0) {
                                $intervalDate = today()->subDays($intervalDays);
                                $teleSales->whereDate('created_at','>=',$intervalDate);
                            }
                            $teleSales = $teleSales->get();
                            // \Log::info('$requestAccountNumberMatch'.print_r($teleSales,true));
                            $aLeadStatus = array();
                            $aLeadStatus['verified'] = 0;
                            $aVerifiedLeadData = array();
                            $lead_ids = "";
                            if(!empty($teleSales->toArray())){
                            foreach($teleSales AS $teleSale){
                                $lead_ids .= $teleSale->id . ",";
                                if($teleSale->status == 'verified'){
                                    $aLeadStatus['verified'] = 'Verified Leads: '.isset($aLeadStatus['verified']) ? $aLeadStatus['verified'] + 1 : 1;
                                }
                            }               
                            $isAlert8Critical = isOnSettings($client_id, 'is_critical_alert8_tele');
                            $alerts = 'Sales agent used an account number that has been used in previous verified leads';
                            $message = __('critical_logs.messages.Event_Type_44');
                            $lead_ids = rtrim($lead_ids,","); 
                            $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                            $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_44');
                            $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Verified');
                            $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$message,null,$temp_lead_id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alerts);
                            
                            //Check if alert is critical or not
                            if ($isAlert8Critical) {
                                return redirect()->route('client.cancelnewLead', ['telesaleTmpId' => $telesaleTmpId, 'id' => $client_id, 'call' => $call, 'ref' => $ref, 'cn' => $cn,'phone' => $requestPhone, 'message' => $alerts]);
                            } else if(isOnSettings($client_id,'is_show_agent_alert8_tele')) {
                                $validationData['verify_acc']['title'] = 'This account number has been used in previous verified leads';
                                $validationData['verify_acc']['msg'] = 'Verified Leads: '.$aLeadStatus['verified'] ;
                            }
                          }
                        }
                    }

                    // for check settings is on or off
                    if ($isOnAlertTele && isOnSettings($client_id,'is_enable_alert3_tele')) {
                        // Check validation for #3 Duplicate Email check
                        //Check for is primary email
                        $requestFields = $request->fields[0];
                        $emailIndices = array_keys(array_column($request->fields[0], 'field_type'),'email');
                        $requestEmail = '';
                        // \Log::info('$emailIndices'.print_r($emailIndices,true));
                        foreach($emailIndices as $index){
                            if(isset($requestFields[$index]['value']['is_primary']) && $requestFields[$index]['value']['is_primary'] == 1){
                                    $requestEmail = (isset($requestFields[$index]['value']['value'])) ? $requestFields[$index]['value']['value'] : '';
                            }
                        }
                        \Log::info('$requestEmail1'.$requestEmail);
                        if($requestEmail != ''){
                            $forms = Clientsforms::where('client_id',$client_id)->pluck('id');
                            $fieldIds = FormField::where('type','email')->whereIn('form_id',$forms)->pluck('id');
                            $teleSalesData = Telesalesdata::where('meta_value', $requestEmail)->whereIn('field_id',$fieldIds)->pluck('telesale_id');
                            // \Log::info('$requestEmail$teleSalesData'.print_r($teleSalesData,true));

                            $intervalDays = getSettingValue($client_id,'interval_days_alert3_tele',null);
                            $teleSales = Telesales::whereIn('id',$teleSalesData);
                            if (!empty($intervalDays) && $intervalDays > 0) {
                                $intervalDate = today()->subDays($intervalDays);
                                $teleSales->whereDate('created_at','>=',$intervalDate);
                            }
                            $teleSales = $teleSales->get();

                        $aLeadStatus = array();
                        $aLeadStatus['pending'] = 0;
                        $aLeadStatus['verified'] = 0;
                        $aLeadStatus['decline'] = 0;
                        $aLeadStatus['hangup'] = 0;
                        $aLeadStatus['cancel'] = 0;
                        $aLeadStatus['expired'] = 0;
                        // $aLeadStatus['msg'] = '';

                        $aVerifiedLeadData = array();
                        $totCount = 0;
                        $lead_ids = "";

                        foreach($teleSales AS $teleSale){
                            
                            $lead_ids .= $teleSale->id . ",";

                            if($teleSale->status == 'verified'){
                            $aLeadStatus['verified'] = 'Verified Leads: '.isset($aLeadStatus['verified']) ? $aLeadStatus['verified'] + 1 : 1;
                            }

                            if($teleSale->status == 'pending'){
                            $aLeadStatus['pending'] = isset($aLeadStatus['pending']) ?  $aLeadStatus['pending'] + 1 : 1;
                            }

                            if($teleSale->status == 'decline'){
                            $aLeadStatus['decline'] = isset($aLeadStatus['decline']) ? $aLeadStatus['decline'] + 1 : 1;
                            }

                            if($teleSale->status == 'hangup'){
                            $aLeadStatus['hangup'] = isset($aLeadStatus['hangup']) ? $aLeadStatus['hangup'] + 1 : 1;
                            }

                            if($teleSale->status == 'cancel'){
                            $aLeadStatus['cancel'] = isset($aLeadStatus['cancel']) ? $aLeadStatus['cancel'] + 1 : 1;
                            }

                            if($teleSale->status == 'expired'){
                            $aLeadStatus['expired'] = isset($aLeadStatus['expired']) ? $aLeadStatus['expired'] + 1 : 1;
                            }

                            $totCount = $totCount + 1;
                        }

                        if($totCount >= getSettingValue($client_id,'max_times_alert3_tele',1)){
                            $alerts = 'Sales agent used an email address that has been used '.$totCount.' times.';
                            $message = __('critical_logs.messages.Event_Type_7',['count'=>$totCount]);

                            if(isOnSettings($client_id,'is_show_agent_alert3_tele')) {
                                $validationData['emailCheck']['title'] = 'This email address has been used '.$totCount.' times.';
                                $validationData['emailCheck']['msg'] = 'Verified Leads: '.$aLeadStatus['verified'] .' Pending Leads: '.$aLeadStatus['pending'].' Declined Leads: '.$aLeadStatus['decline'].' Disconnected Calls: '.$aLeadStatus['hangup'].' Cancelled Leads: '.$aLeadStatus['cancel'].' Expired Leads: '.$aLeadStatus['expired'];

                            }
                            $lead_ids = rtrim($lead_ids,",");
                            $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                            $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_7');
                            $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                            $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$message,null,$temp_lead_id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alerts);
                            
                            $isTeleAlert3Critical = isOnSettings($client_id, 'is_critical_alert3_tele',false);
                            
                            //Check if alert is critical or not
                            if ($isTeleAlert3Critical) {
                                return redirect()->route('client.cancelnewLead', ['telesaleTmpId' => $telesaleTmpId, 'id' => $client_id, 'call' => $call, 'ref' => $ref, 'cn' => $cn,'phone' => $requestPhone, 'message' => $alerts]);
                            }

                            }
                        }
                    }

                    // for check settings is on or off
                    if ($isOnAlertTele && isOnSettings($client_id,'is_enable_alert4_tele')) {
                        // Check validation for #5 Duplicate Phone check against ALL LEADS in database
                        // Check for is primary phone_number
                        $requestFields = $request->fields[0];
                        $phoneIndices = array_keys(array_column($request->fields[0], 'field_type'),'phone_number');
                        $requestPhone = '';
                        // \Log::info('$phoneIndices'.print_r($phoneIndices,true));
                        foreach($phoneIndices as $index){
                            if(isset($requestFields[$index]['value']['is_primary']) && $requestFields[$index]['value']['is_primary'] == 1){
                                    $requestPhone = (isset($requestFields[$index]['value']['value'])) ? $requestFields[$index]['value']['value'] : '';
                            }
                        }
                        // \Log::info('$requestPhone'.print_r($requestPhone,true));
                        if($requestPhone != ''){
                            $forms = Clientsforms::where('client_id',$client_id)->pluck('id');
                            $fieldIds = FormField::where('type','phone_number')->whereIn('form_id',$forms)->pluck('id');
                            $teleSalesData = Telesalesdata::where('meta_value', $requestPhone)->whereIn('field_id',$fieldIds)->pluck('telesale_id');

                            $intervalDays = getSettingValue($client_id,'interval_days_alert4_tele',null);
                            $teleSales = Telesales::whereIn('id',$teleSalesData);
                            if (!empty($intervalDays) && $intervalDays > 0) {
                                $intervalDate = today()->subDays($intervalDays);
                                $teleSales->whereDate('created_at','>=',$intervalDate);
                            }
                            $teleSales = $teleSales->get();

                        $aLeadStatus = array();
                        $aLeadStatus['pending'] = 0;
                        $aLeadStatus['verified'] = 0;
                        $aLeadStatus['decline'] = 0;
                        $aLeadStatus['hangup'] = 0;
                        $aLeadStatus['cancel'] = 0;
                        $aLeadStatus['expired'] = 0;
                        // $aLeadStatus['msg'] = '';

                        $aVerifiedLeadData = array();
                        $totCount = 0;
                        
                        $lead_ids = "";
                        
                        foreach($teleSales AS $teleSale){
                            $lead_ids .= $teleSale->id .",";
                            if($teleSale->status == 'verified'){
                            $aLeadStatus['verified'] = 'Verified Leads: '.isset($aLeadStatus['verified']) ? $aLeadStatus['verified'] + 1 : 1;
                            }

                            if($teleSale->status == 'pending'){
                            $aLeadStatus['pending'] = isset($aLeadStatus['pending']) ?  $aLeadStatus['pending'] + 1 : 1;
                            }

                            if($teleSale->status == 'decline'){
                            $aLeadStatus['decline'] = isset($aLeadStatus['decline']) ? $aLeadStatus['decline'] + 1 : 1;
                            }

                            if($teleSale->status == 'hangup'){
                            $aLeadStatus['hangup'] = isset($aLeadStatus['hangup']) ? $aLeadStatus['hangup'] + 1 : 1;
                            }

                            if($teleSale->status == 'cancel'){
                            $aLeadStatus['cancel'] = isset($aLeadStatus['cancel']) ? $aLeadStatus['cancel'] + 1 : 1;
                            }

                            if($teleSale->status == 'expired'){
                            $aLeadStatus['expired'] = isset($aLeadStatus['expired']) ? $aLeadStatus['expired'] + 1 : 1;
                            }

                            $totCount = $totCount + 1;
                        }
                        // dd($aLeadStatus);
                        if($totCount >= getSettingValue($client_id,'max_times_alert4_tele')){
                            $alerts = 'Sales agent used a phone number that has been used '.$totCount.' times.';

                            if(isOnSettings($client_id,'is_show_agent_alert4_tele')) {
                                $validationData['phoneCheck']['title'] = 'This phone number has been used '.$totCount.' times.';
                                $validationData['phoneCheck']['msg'] = 'Verified Leads: '.$aLeadStatus['verified'] .' Pending Leads: '.$aLeadStatus['pending'].' Declined Leads: '.$aLeadStatus['decline'].' Disconnected Calls: '.$aLeadStatus['hangup'].' Cancelled Leads: '.$aLeadStatus['cancel'].' Expired Leads: '.$aLeadStatus['expired'];
                            }

                            $isTeleAlert4Critical = isOnSettings($client_id, 'is_critical_alert4_tele');
                            $lead_ids = rtrim($lead_ids,",");
                            $message =  __('critical_logs.messages.Event_Type_9',['count'=>$totCount]);
                            $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                            $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_9');
                            $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                            $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$message,null,$temp_lead_id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alerts);
                            
                            //Check if alert is critical or not
                            if ($isTeleAlert4Critical) {
                                return redirect()->route('client.cancelnewLead', ['telesaleTmpId' => $telesaleTmpId, 'id' => $client_id, 'call' => $call, 'ref' => $ref, 'cn' => $cn,'phone' => $requestPhone, 'message' => $alerts]);
                            }
                        }
                        }
                    }

                    // for check settings is on or off
                    if ($isOnAlertTele && isOnSettings($client_id,'is_enable_alert5_tele')) {

                        // Checkpoint #4: Fraudulent Email check against that client’s sales agents (Tele and D2D both) Emails

                        //Check for is primary email
                        $requestFields = $request->fields[0];
                        $emailIndices = array_keys(array_column($request->fields[0], 'field_type'),'email');
                        $requestEmail = '';

                        foreach($emailIndices as $index){
                            if(isset($requestFields[$index]['value']['is_primary']) && $requestFields[$index]['value']['is_primary'] == 1){
                                $requestEmail = (isset($requestFields[$index]['value']['value'])) ? $requestFields[$index]['value']['value'] : '';
                            }
                        }
                        // \Log::info('$emailIndices'.print_r($emailIndices,true));
                        if($requestEmail != ''){
                        $salesAgents = User::where('client_id',$client_id)
                        ->where('email', $requestEmail)
                        ->where('access_level','salesagent')->where('id', '<>', Auth::user()->id)->first();
                        // \Log::info('$salesAgents'.print_r($salesAgents,true));
                        // \Log::info('emailssss'.print_r($requestEmail,true));
                        
                        if($salesAgents){
                            $isTeleAlert5Critical = isOnSettings($client_id, 'is_critical_alert5_tele');
                            $alerts = 'Sales agent used an email address belonging to another sales agent.';

                            if(isOnSettings($client_id,'is_show_agent_alert5_tele')) {
                                $validationData['salesEmailCheck']['title'] = 'This email address is associated with an existing '. array_get($client, 'name') . ' sales agent ('. array_get($salesAgents,'full_name') . ' - ID: ' . array_get($salesAgents,'userid') . ').';
                                $validationData['salesEmailCheck']['msg'] = 'There is an active '.array_get($client, 'name') .' sales agent associated with this email address.';
                            }
                            $name = array_get($salesAgents,'full_name').'('.array_get($salesAgents,'userid').')';
                            $message =  __('critical_logs.messages.Event_Type_8',['name'=>$name]);
                            $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                            $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_8');
                            $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                            $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$message,null,$temp_lead_id,null,$lead_status,$event_type,$error_type,null,null,$alerts);
                            
                            //Check if alert is critical or not
                            if ($isTeleAlert5Critical) {
                                return redirect()->route('client.cancelnewLead', ['telesaleTmpId' => $telesaleTmpId, 'id' => $client_id, 'call' => $call, 'ref' => $ref, 'cn' => $cn,'phone' => $requestPhone, 'message' => $alerts]);
                            }
                        }

                        }
                        // \Log::info('$validationData'.print_r($validationData,true));
                    }

                    // for check settings is on or off
                    if ($isOnAlertTele && isOnSettings($client_id,'is_enable_alert6_tele')) {
                        // Checkpoint #6: Fraudulent Phone check against that client’s sales agents (Tele and D2D both) Phone
                        //Check for is primary phone
                        $requestFields = $request->fields[0];
                        $phoneIndices = array_keys(array_column($request->fields[0], 'field_type'),'phone_number');
                        $requestphone = '';

                        foreach($phoneIndices as $index){
                            if(isset($requestFields[$index]['value']['is_primary']) && $requestFields[$index]['value']['is_primary'] == 1){
                                $requestphone = (isset($requestFields[$index]['value']['value'])) ? $requestFields[$index]['value']['value'] : '';
                            }
                        }
                        // \Log::info('$phoneIndices6'.print_r($phoneIndices,true));
                        if($requestphone != ''){

                        $salesAgents = User::where('client_id',$client_id)
                                                ->where('access_level','salesagent')
                                                ->whereHas('salesAgentDetails', function($q) use($requestphone) {
                                                    $q->where('phone_number', $requestphone);
                                                })->where('id', '<>', Auth::user()->id)->first();


                        // \Log::info('$salesAgents6'.print_r($salesAgents,true));
                        // \Log::info('Phone6'.print_r($requestphone,true));
                        if($salesAgents){
                            $isTeleAlert6Critical = isOnSettings($client_id, 'is_critical_alert6_tele');
                            $alerts = 'Sales agent used a phone number belonging to another sales agent.';

                            if (isOnSettings($client_id,'is_show_agent_alert6_tele')) {
                                $validationData['salesPhoneCheck']['title'] = 'This phone number is associated with an existing '. array_get($client, 'name') . ' sales agent ('. array_get($salesAgents,'full_name') . ' - ID: ' . array_get($salesAgents,'userid') . ').';


                                $validationData['salesPhoneCheck']['msg'] = 'There is an active '.array_get($client, 'name') .' sales agent associated with this phone number.';
                            }
                            
                            $name = array_get($salesAgents,'full_name').'('.array_get($salesAgents,'userid').')';
                            $message =  __('critical_logs.messages.Event_Type_10',['name'=>$name]);
                            $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                            $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_10');
                            $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                            $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$message,null,$temp_lead_id,null,$lead_status,$event_type,$error_type,null,null,$alerts);
                            
                            //Check if alert is critical or not
                            if ($isTeleAlert6Critical) {
                                return redirect()->route('client.cancelnewLead', ['telesaleTmpId' => $telesaleTmpId, 'id' => $client_id, 'call' => $call, 'ref' => $ref, 'cn' => $cn,'phone' => $requestPhone, 'message' => $alerts]);
                            }
                        }

                        }
                    }

                    // for check settings is on or off
                    if ($isOnAlertTele && isOnSettings($client_id,'is_enable_alert7_tele')) {
                        $emailIndices = array_keys(array_column($request->fields[0], 'field_type'),'email');
                        $requestEmail = '';
                        \Log::info('$emailIndices'.print_r($emailIndices,true));
                        foreach($emailIndices as $index){
                            if(isset($requestFields[$index]['value']['is_primary']) && $requestFields[$index]['value']['is_primary'] == 1){
                                    $requestEmail = (isset($requestFields[$index]['value']['value'])) ? $requestFields[$index]['value']['value'] : '';
                            }
                        }
                        $requestFields = $request->fields[0];
                        $phoneIndices = array_keys(array_column($request->fields[0], 'field_type'),'phone_number');
                        $requestPhone = '';
                        \Log::info('$phoneIndices'.print_r($phoneIndices,true));
                        foreach($phoneIndices as $index){
                            if(isset($requestFields[$index]['value']['is_primary']) && $requestFields[$index]['value']['is_primary'] == 1){
                                    $requestPhone = (isset($requestFields[$index]['value']['value'])) ? $requestFields[$index]['value']['value'] : '';
                            }
                        }

                        // Leads submitted using its own email or phone number
                        if($requestEmail != '' || $requestPhone != "")
                        {
                            $phoneNo = Salesagentdetail::where('phone_number',$requestPhone)
                            ->whereNotNull('phone_number')
                            ->where('user_id',Auth::user()->id)
                            ->get();
                            
                            if((Auth::user()->email == $requestEmail && !empty($requestEmail)) || $phoneNo->count() > 0)
                            {
                                $teleSaleId = isset($telesale->id) ? $telesale->id : false;

                                $call = $request->calltype;

                                $ref = $referenceId;
                                $cn = isset($contact_numbers) ? $contact_numbers : false;
                                
                                $telesaleTmpId = isset($telesaleTmp->id) ? $telesaleTmp->id : false;

                                $message = $log_message = "";
                                $event_type = $alerts = null;

                                if ((Auth::user()->email == $requestEmail && !empty($requestEmail)) && $phoneNo->count() > 0) {
                                    $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_4');
                                    $message = "You can not create lead with your own Email address and Your own Phone number";
                                    $alerts = "Sales agent used their own email and phone number during enrollment.";
                                    $log_message = __('critical_logs.messages.Event_Type_4');
                                } else  {
                                    if (Auth::user()->email == $requestEmail && !empty($requestEmail)) {
                                        $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_2');
                                        $message = "You can not create lead with your own Email address";
                                        $alerts = "Sales agent used their own email during enrollment.";
                                        $log_message = __('critical_logs.messages.Event_Type_2');
                                    } else if ($phoneNo->count() > 0) {
                                        $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_3');
                                        $message = "You cannot create lead with your own Phone Number ";
                                        $alerts = "Sales agent used their own phone number during enrollment.";
                                        $log_message = __('critical_logs.messages.Event_Type_3');
                                    }
                                }

                                $isTeleAlert7Critical = isOnSettings($client_id, 'is_critical_alert7_tele');

                                if ($message != "") {
                                    $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                                    $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Cancelled');
                                    $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$message,null,$temp_lead_id,null,$lead_status,$event_type,$error_type,null,null,$alerts);
                                }


                                if ($isTeleAlert7Critical) {
                                    return redirect()->route('client.cancelnewLead', ['telesaleTmpId' => $telesaleTmpId, 'id' => $client_id, 'call' => $call, 'ref' => $ref, 'cn' => $cn,'phone' => $requestPhone, 'message' => $message]);
                                } else if(isOnSettings($client_id,'is_show_agent_alert7_tele') && !empty($message)) {
                                    $validationData['personalDetails']['title'] = 'Sales Agent used personal details';
                                    $validationData['personalDetails']['msg'] = $message;
                                }
                                    
                            }
            
                        }
                    }

                    if($isOnAlertTele && isOnSettings($client_id,'is_enable_alert9_tele',false)){
                            
                        $requestFields = $request->fields[0];
                        $phoneIndices = array_keys(array_column($request->fields[0], 'field_type'),'phone_number');
                        $requestPhone = '';
                        // \Log::info('$phoneIndices'.print_r($phoneIndices,true));
                        foreach($phoneIndices as $index){
                            if(isset($requestFields[$index]['value']['is_primary']) && $requestFields[$index]['value']['is_primary'] == 1){
                                    $requestPhone = (isset($requestFields[$index]['value']['value'])) ? $requestFields[$index]['value']['value'] : '';
                            }
                        }
                        // \Log::info('$requestPhone'.print_r($requestPhone,true));
                        $aLeadStatus = array();
                        $aLeadStatus['verified'] = 0;
                        $aVerifiedLeadData = array();
                        $lead_ids = "";

                        if($requestPhone != ''){
                            $forms = Clientsforms::where('client_id',$client_id)->pluck('id');
                            $fieldIds = FormField::where('type','phone_number')->whereIn('form_id',$forms)->pluck('id');
                            $teleSalesData = Telesalesdata::where('meta_value', $requestPhone)->whereIn('field_id',$fieldIds)->pluck('telesale_id');

                            $intervalDays = getSettingValue($client_id,'interval_days_alert9_tele',null);
                            $teleSales = Telesales::whereIn('id',$teleSalesData)->where('status',config('constants.LEAD_TYPE_VERIFIED'));
                            if (!empty($intervalDays) && $intervalDays > 0) {
                                $intervalDate = today()->subDays($intervalDays);
                                $teleSales->whereDate('created_at','>=',$intervalDate);
                            }
                            $teleSales = $teleSales->get();
                        }
                        if(!empty($teleSales->toArray())){
                        foreach($teleSales AS $teleSale){
                            $lead_ids .= $teleSale->id . ",";
                            if($teleSale->status == 'verified'){
                                $aLeadStatus['verified'] = 'Verified Leads: '.isset($aLeadStatus['verified']) ? $aLeadStatus['verified'] + 1 : 1;
                            }
                        }                   
                        $isAlert9Critical = isOnSettings($client_id, 'is_critical_alert9_tele');
                        $alerts = 'Sales agent used a phone number that has been used in previous verified leads.';
                        $message = __('critical_logs.messages.Event_Type_45');
                        $lead_ids = rtrim($lead_ids,","); 
                        $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                        $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_45');
                        $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Verified');
                        $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$message,null,$temp_lead_id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alerts);
                        
                        //Check if alert is critical or not
                        if ($isAlert9Critical) {
                            return redirect()->route('client.cancelnewLead', ['telesaleTmpId' => $telesaleTmpId, 'id' => $client_id, 'call' => $call, 'ref' => $ref, 'cn' => $cn,'phone' => $requestPhone, 'message' => $alerts]);
                        } else if(isOnSettings($client_id,'is_show_agent_alert9_tele')) {
                            $validationData['phone_number_verified']['title'] = 'This phone number has been used in previous verified leads';
                            $validationData['phone_number_verified']['msg'] = 'Verified Leads: '.$aLeadStatus['verified'];
                        }
                      }
                    }
                    
                    if($isOnAlertTele && isOnSettings($client_id,'is_enable_alert11_tele',false)){
                            
                        $requestFields = $request->fields[0];
                        $phoneIndices = array_keys(array_column($request->fields[0], 'field_type'),'phone_number');
                        $requestPhone = '';
                        // \Log::info('$phoneIndices'.print_r($phoneIndices,true));
                        foreach($phoneIndices as $index){
                            if(isset($requestFields[$index]['value']['is_primary']) && $requestFields[$index]['value']['is_primary'] == 1){
                                    $requestPhone = (isset($requestFields[$index]['value']['value'])) ? $requestFields[$index]['value']['value'] : '';
                            }
                        }
                        // \Log::info('$requestPhone'.print_r($requestPhone,true));
                        $aLeadStatus = array();
                        $aLeadStatus['verified'] = 0;
                        $aVerifiedLeadData = array();
                        $lead_ids = "";
                        if($requestPhone != ''){
                            $forms = Clientsforms::where('client_id',$client_id)->pluck('id');
                            $fieldIds = FormField::where('type','phone_number')->whereIn('form_id',$forms)->pluck('id');
                            $teleSalesData = Telesalesdata::where('meta_value', $requestPhone)->whereIn('field_id',$fieldIds)->pluck('telesale_id');

                            $intervalDays = getSettingValue($client_id,'interval_days_alert11_tele',null);
                            $teleSales = Telesales::whereIn('id',$teleSalesData)->where('status',config('constants.LEAD_TYPE_VERIFIED'));
                            if (!empty($intervalDays) && $intervalDays > 0) {
                                $intervalDate = today()->subDays($intervalDays);
                                $teleSales->whereDate('created_at','>=',$intervalDate);
                            }
                            $aVerifiedLeadData = $teleSales->pluck('id');                                        
                        
                            $verifiedTelesalesData = Telesalesdata::where(function($query) use ($aVerifiedLeadData){
                                                        $query->where('meta_key','first_name')
                                                        ->orWhere('meta_key','last_name');
                                                    })
                                                    ->whereIn('telesale_id',$aVerifiedLeadData)
                                                    ->whereHas('formFieldsData',function($query) {
                                                        $query->where('is_primary',1);
                                                    })
                                                    ->get();
                            $firstName = '';
                            $lastName = '';

                            //Check for primary first name anddd last name
                            $requestFields = $request->fields[0];
                            $fullnameIndices = array_keys(array_column($request->fields[0], 'field_type'),'fullname');
                            $requestFullName = '';
                            foreach($fullnameIndices as $index){
                                if(isset($requestFields[$index]['value']['is_primary']) && $requestFields[$index]['value']['is_primary'] == 1){
                                        $requestFullName = (isset($requestFields[$index]['value']['first_name']) && isset($requestFields[$index]['value']['last_name'])) ? $requestFields[$index]['value']['first_name'].' '.$requestFields[$index]['value']['last_name'] : '';
                                }
                            }
                            $lead_ids = "";
                            $critical_message = "";

                            foreach($verifiedTelesalesData AS $verifiedTelesale)
                            {
                                if($verifiedTelesale->meta_key == "first_name")
                                $lead_ids .= $verifiedTelesale->telesale_id .",";   
                            }

                            foreach($verifiedTelesalesData AS $verifiedTelesale){
                            
                            $verifiedTelesalesFirstName = Telesalesdata::where('meta_key','first_name')->where('telesale_id',$verifiedTelesale->telesale_id)->first();
                            $verifiedTelesalesLastName = Telesalesdata::where('meta_key','last_name')->where('telesale_id',$verifiedTelesale->telesale_id)->first();

                            $firstName = $verifiedTelesalesFirstName->meta_value;
                            $lastName = $verifiedTelesalesLastName->meta_value;
                            $fullName = $firstName .' '.$lastName;
                            if($requestFullName == $fullName){
                                $alerts = 'Sales agent submitted an enrollment for an existing customer in previous verified leads.';
                                $critical_message =  __('critical_logs.messages.Event_Type_46');
                                if(isOnSettings($client_id,'is_show_agent_alert11_tele')) {
                                    $validationData['name_phone_verified']['title'] = 'This Customer with this phone number is already enrolled with '.$client->name;
                                    $validationData['name_phone_verified']['msg'] = 'There is a verified enrollment associated with this customer and phone number.';
                                }
                                
                                break;
                            }else{
                                continue;
                            }
                            }
                            
                            if($lead_ids !="")
                                $lead_ids = rtrim($lead_ids,",");
                            if($critical_message != ""){
                                $isTeleAlert11Critical = isOnSettings($client_id, 'is_critical_alert11_tele');
                                $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                                $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_46');
                                $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                                $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$critical_message,null,$temp_lead_id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alerts);
                                
                                //Check if alert is critical or not
                                if ($isTeleAlert11Critical) {
                                    $isAutoCancel = true;
                                }

                            }
                            
                            // \Log::info('$verifiedTelesalesData$validationData'.print_r($validationData,true));
                        }
                        
                    }
                     // for check settings is on or off

                     if($isOnAlertTele && isOnSettings($client_id,'is_enable_alert12_tele',false)){
                            
                        $requestFields = $request->fields[0];
                      
                        $addressIndices = array_keys(array_column($request->fields[0], 'field_type'),'address');
                        $field_type = 'address';
                        if(empty($addressIndices)){
                        $addressIndices = array_keys(array_column($request->fields[0], 'field_type'),'service_and_billing_address');
                        $field_type = 'service_and_billing_address';
                        }
                        // \Log::info('$addressIndices'.print_r($field_type,true));
                        $serviceAddress1 = '';
                        $serviceAddress2 = '';
                        $serviceCity = '';
                        $serviceCounty = '';
                        $serviceState = '';
                        $serviceZipcode = '';
                        $serviceCountry = '';
                        // \Log::info('$addressIndices'.print_r($addressIndices,true));
                        foreach($addressIndices as $index){
                            if($field_type == 'service_and_billing_address'){
                                    $serviceAddress1 = strtolower(trim($requestFields[$index]['value']['service_address_1']));
                                    $serviceAddress2 = strtolower(trim($requestFields[$index]['value']['service_address_2']));
                                    $serviceCity = strtolower(trim($requestFields[$index]['value']['service_city']));
                                    $serviceCounty = strtolower(trim($requestFields[$index]['value']['service_county']));
                                    $serviceState = strtolower(trim($requestFields[$index]['value']['service_state']));
                                    $serviceZipcode = strtolower(trim($requestFields[$index]['value']['service_zipcode']));
                                    $serviceCountry = strtolower(trim($requestFields[$index]['value']['service_country']));
                            }
                            if($field_type == 'address'){
                                $serviceAddress1 = strtolower(trim($requestFields[$index]['value']['address_1']));
                                $serviceAddress2 = strtolower(trim($requestFields[$index]['value']['address_2']));
                                $serviceCity = strtolower(trim($requestFields[$index]['value']['city']));
                                $serviceCounty = strtolower(trim($requestFields[$index]['value']['county']));
                                $serviceState = strtolower(trim($requestFields[$index]['value']['state']));
                                $serviceZipcode = strtolower(trim($requestFields[$index]['value']['zipcode']));
                                $serviceCountry = strtolower(trim($requestFields[$index]['value']['country']));
                            }
                            }
                        
                        $aLeadStatus = array();
                        $aLeadStatus['verified'] = 0;
                        $aVerifiedLeadData = array();
                        $lead_ids = "";
                        if($serviceAddress1 != ''){
                            $forms = Clientsforms::where('client_id',$client_id)->pluck('id');
                            $fieldIds = FormField::whereIn('form_id',$forms)->where(function ($q) {
                                $q->where('type', '=', 'address')
                                ->orWhere('type', '=', 'service_and_billing_address');
                            })
                            ->where('is_primary', '=', '1')->pluck('id');
                            $telesalesData = Telesalesdata::whereIn('field_id',$fieldIds)->where(function ($query) {
                                $query->whereIn('meta_key',['service_address_1','service_address_2','service_city','service_county','service_state','service_zipcode','service_country'])
                                ->orWhereIn('meta_key',['address_1','address_2','city','county','state','zipcode','country']);
                            })
                           ->get();
                         
                            $lead_ids = "";
                            $critical_message = "";
                           

                            foreach($telesalesData AS $teleData)
                            {
                                if($teleData->meta_key == "service_address_1")
                                $lead_ids .= $teleData->telesale_id .",";   
                                if($teleData->meta_key == "address_1")
                                $lead_ids .= $teleData->telesale_id .",";   
                                   
                            }
                            $teleSalesIds = explode(",",$lead_ids);
                           
                            $telesales_id = [];
                           
                            foreach($teleSalesIds AS $teleId){
                            if(!empty($teleId)){
                            $verifiedTelesalesServiceAddress1 = Telesalesdata::where('telesale_id',$teleId)->where(function($query){
                                $query->where('meta_key','service_address_1')->orWhere('meta_key','address_1');
                            })->first();
                            $verifiedTelesalesServiceAddress2 = Telesalesdata::where('telesale_id',$teleId)->where(function($query){
                                $query->where('meta_key','service_address_2')->orWhere('meta_key','address_2');
                            })->first();
                            $verifiedTelesalesServiceCity = Telesalesdata::where('telesale_id',$teleId)->where(function($query){
                                $query->where('meta_key','service_city')->orWhere('meta_key','city');
                            })->first();
                            $verifiedTelesalesServiceCounty = Telesalesdata::where('telesale_id',$teleId)->where(function($query){
                                $query->where('meta_key','service_county')->orWhere('meta_key','county');
                            })->first();
                            $verifiedTelesalesServiceState = Telesalesdata::where('telesale_id',$teleId)->where(function($query){
                                $query->where('meta_key','service_state')->orWhere('meta_key','state');
                            })->first();
                            $verifiedTelesalesServiceZipcode = Telesalesdata::where('telesale_id',$teleId)->where(function($query){
                                $query->where('meta_key','service_zipcode')->orWhere('meta_key','zipcode');
                            })->first();
                            $verifiedTelesalesServiceCountry = Telesalesdata::where('telesale_id',$teleId)->where(function($query){
                                $query->where('meta_key','service_country')->orWhere('meta_key','country');
                            })->first();
                           
                            if((strtolower(trim($verifiedTelesalesServiceAddress1->meta_value)) == $serviceAddress1) && (strtolower(trim($verifiedTelesalesServiceAddress2->meta_value)) == $serviceAddress2) && (strtolower(trim($verifiedTelesalesServiceCity['meta_value'])) == $serviceCity)
                            && (strtolower(trim($verifiedTelesalesServiceCounty['meta_value'])) == $serviceCounty) && (strtolower(trim($verifiedTelesalesServiceState->meta_value)) == $serviceState) && (strtolower(trim($verifiedTelesalesServiceZipcode->meta_value)) == $serviceZipcode) && (strtolower(trim($verifiedTelesalesServiceCountry->meta_value)) == $serviceCountry)){
                               
                                $telesales_id[] = $teleId;
                            }
                            else{
                                
                                continue;
                            }
                        }
                        }
                        // \Log::info('$teleSalesIds Array'.print_r($telesales_id,true));  
                        if(!empty($telesales_id)){
                                $intervalDays = getSettingValue($client_id,'interval_days_alert12_tele',null);
                                $teleSales = Telesales::whereIn('id',$telesales_id)->where('status',config('constants.LEAD_TYPE_VERIFIED'));
                                if (!empty($intervalDays) && $intervalDays > 0) {
                                    $intervalDate = today()->subDays($intervalDays);
                                    $teleSales->whereDate('created_at','>=',$intervalDate);
                                }
                                $teleSales = $teleSales->get(); 
                                // \Log::info('$teleSalesin'.print_r($teleSales,true));     
                                if(!empty($teleSales->toArray())){
                                    $lead_ids = "";
                                    foreach($teleSales->toArray() AS $teleSale){
                                        \Log::info('$teleSalesFor'.print_r($teleSales,true));   
                                        $lead_ids .= $teleSale['id'] . ",";
                                        if($teleSale['status'] == 'verified'){
                                            $aLeadStatus['verified'] = 'Verified Leads: '.isset($aLeadStatus['verified']) ? $aLeadStatus['verified'] + 1 : 1;
                                        }
                                        // \Log::info(' $aLeadStatus'.print_r($aLeadStatus['verified'],true));
                                    } 
                                    // \Log::info(' $aLeadStatus total'.print_r($aLeadStatus['verified'],true));                  
                                    $isAlert12Critical = isOnSettings($client_id, 'is_critical_alert12_tele');
                                    $alerts = 'Sales agent used service address that has been used in previous verified leads.';
                                    $message = __('critical_logs.messages.Event_Type_47');
                                    $lead_ids = rtrim($lead_ids,","); 
                                    $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                                    $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_47');
                                    $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Verified');
                                    $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$message,null,$temp_lead_id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alerts);
                                    
                                    //Check if alert is critical or not
                                    if ($isAlert12Critical) {
                                        return redirect()->route('client.cancelnewLead', ['telesaleTmpId' => $telesaleTmpId, 'id' => $client_id, 'call' => $call, 'ref' => $ref, 'cn' => $cn, 'message' => $alerts]);
                                    } else if(isOnSettings($client_id,'is_show_agent_alert12_tele')) {
                                        $validationData['address_verified']['title'] = 'This service address has been used in previous verified leads';
                                        $validationData['address_verified']['msg'] = 'Verified Leads: '.$aLeadStatus['verified'];
                                    }
                                   
                                  }
                                  
                                }
                            }
                        }
                        
                    
                    $teleSaleId = isset($telesale->id) ? $telesale->id : false;

                    $call = $request->calltype;

                    $ref = $referenceId;
                    $cn = isset($contact_numbers) ? $contact_numbers : false;
                    $telesaleTmpId = isset($telesaleTmp->id) ? $telesaleTmp->id : false;

                    if(count($validationData) > 0){
                        return view('client.double-check-validation',compact('validationData','totCount','teleSaleId','client_id','call','ref','cn','telesaleTmpId'));
                    }else{

                    return redirect()->route('client.proceed_lead', ['telesaleTmpId' => $telesaleTmpId, 'id' => $client_id, 'call' => $call, 'ref' => $ref, 'cn' => $cn ]);
                    }

                }else{
                    $zipcode = $request->zipcode;
                    $is_multiple = 1;
                    \Log::info('zipcode: '.$zipcode);
                    $zipcodeData = Zipcodes::where('zipcode', $zipcode)->first();

                    \Log::info('zipcodeData: '.$zipcodeData);

                    if (empty($zipcodeData)) {
                        return back()->withErrors(['zipcode' => 'Invalid Zip code.'])->withInput($request->all());
                    }
                    $parentReferenceId = 0;
                    $parentLeadId = 0;
                    $enrollCounter = 1;
                    $isAutoCancel = false;
                    $isDoNotCancel = false;
                    $dispositionId = null;
                    $allMultipleEnnrollmentId = array();
                    $validationData = array();
                    $allFieldsInsertedForParent = array();
                    $allFieldInsertedForChild = array();

                    foreach ($request->multienrollmentValues as $x){
                        
                        if (isset($request->program[$x]) && is_array($request->program[$x])) {
                            foreach ($request->program[$x] as $pId) {
                                $program = Programs::find($pId);
                                if (empty($program)) {
                                    return back()->with("error", 'This program was not found.');
                                }
                            }
                        } else {
                            $program = Programs::find($request->program);
                            if (empty($program)) {
                                return back()->with("error", 'Please select a valid program.' . $request->program);
                            }
                        }

                        $leadData = [];
                        $leadData['client_id'] = $client_id;
                        $leadData['form_id'] = $form_id;
                        $leadData['user_id'] = Auth::user()->id;
                        $leadData['program'] = isset($request->program[$x]) ? implode(',',$request->program[$x]) : NULL;
                        $leadData['is_enrollment_by_state'] = $request->is_enrollment_by_state;
                        // $referenceId = (new TelesalesTmp)->generateReferenceId();
                        $clientPrefix = (new Client())->getClientPrefix($client_id);
                        /* This function generates a new lead refrence id by adding client prefix */
                        $referenceId = (new TelesalesTmp)->generateNewReferenceId($client_id,$clientPrefix);

                        $isOnAlertTele = isOnSettings($client_id,'is_enable_alert_tele');

                        // $check_verification_number = 2;
                        // $validate_num = $verification_number = "";
                        // while ($check_verification_number > 1) {
                        //     $verification_number = rand(1000000, 9999999);
                        //     $validate_num = (new TelesalesTmp)->validateConfirmationNumber($verification_number);
                        //     if (!$validate_num) {
                        //         $check_verification_number = 0;
                        //     } else {
                        //         $check_verification_number++;
                        //     }
                        // }

                        $leadData['refrence_id'] = $referenceId;
                        $leadData['is_multiple'] = 1;
                        
                        if($x == 0){
                            $parentReferenceId = $referenceId;
                            $leadData['multiple_parent_id'] = 0;
                        }else{
                            $leadData['multiple_parent_id'] = $parentLeadId;
                        }
                        
                        // $leadData['verification_number'] = $verification_number;

                        if (isset($request->parent_id) && !empty($request->parent_id)){
                            $leadData['parent_id'] = $request->parent_id;
                            $leadData['cloned_by'] = Auth::user()->id;
                        } else {
                            $leadData['parent_id'] = 0;
                            $leadData['cloned_by'] = 0;
                        }
                        $leadData['zipcode'] = $zipcode;

                        // \Log::info('$leadData'.print_r($request->all(),true));
                        // \Log::info('$leadData'.print_r($leadData,true));
                        $telesaleTmp = TelesalesTmp::create($leadData);

                        $telesaleTmpId = isset($telesaleTmp->id) ? $telesaleTmp->id : false;
                        $allMultipleEnnrollmentId[] = $telesaleTmpId;

                        $temp_lead_id = $telesaleTmp->id;
                        if($x == 0){
                            $parentLeadId = $temp_lead_id;
                        }
                        $fields = $request->fields[$x];
                        
                        if (isset($request->parent_id) && !empty($request->parent_id)){
                            $leadCloneMessage = __('critical_logs.messages.Event_Type_31');
                            $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_31');
                            $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                            $criticalLogs = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$leadCloneMessage,null,$temp_lead_id,$request->parent_id,$lead_status,$event_type);
                        }
                        if($x == 0){
                            foreach ($fields as $field) {

                                if($field['field_type'] == 'checkbox') {
                                    if (isset($field['value']) && !empty($field['value'])) {
                                        $value = implode(', ', $field['value']);
                                        $telesaleTmp->teleSalesData()->create([
                                            'meta_key' => 'value',
                                            'meta_value' => $value,
                                            'field_id' => $field['field_id']
                                        ]);
                                        $allFieldsInsertedForParent[] = $field['field_id'];
                                    }
                                } else if ($field['field_type'] != "separator" && $field['field_type'] != "heading" && $field['field_type'] != "label" && $field['field_type'] != 'checkbox') {
                                    $values = $field['value'];
                                    if ($values != NULL) {

                                        if(is_array($values)) {
                                            foreach ($values as $key => $value) {
                                                $telesaleTmp->teleSalesData()->create([
                                                    'meta_key' => $key,
                                                    'meta_value' => $value,
                                                    'field_id' => $field['field_id']
                                                ]);
                                                
                                            }
                                            $allFieldsInsertedForParent[] = $field['field_id'];
                                        } else {
                                            $telesaleTmp->teleSalesData()->create([
                                                'meta_key' => $field['field_type'],
                                                'meta_value' => $values,
                                                'field_id' => $field['field_id']
                                            ]);
                                            $allFieldsInsertedForParent[] = $field['field_id'];
                                        }

                                    }
                                }  else {
                                    continue;
                                }
                            }
                        }else{
                            foreach ($fields as $field) {

                                if($field['field_type'] == 'checkbox') {
                                    if (isset($field['value']) && !empty($field['value'])) {
                                        $value = implode(', ', $field['value']);
                                        $telesaleTmp->teleSalesData()->create([
                                            'meta_key' => 'value',
                                            'meta_value' => $value,
                                            'field_id' => $field['field_id']
                                        ]);
                                        $allFieldInsertedForChild[] = $field['field_id'];
                                    }
                                } else if ($field['field_type'] != "separator" && $field['field_type'] != "heading" && $field['field_type'] != "label" && $field['field_type'] != 'checkbox') {
                                    $values = $field['value'];
                                    if ($values != NULL) {

                                        if(is_array($values)) {
                                            foreach ($values as $key => $value) {
                                                $telesaleTmp->teleSalesData()->create([
                                                    'meta_key' => $key,
                                                    'meta_value' => $value,
                                                    'field_id' => $field['field_id']
                                                ]);
                                                
                                            }
                                            $allFieldInsertedForChild[] = $field['field_id'];
                                        } else {
                                            $telesaleTmp->teleSalesData()->create([
                                                'meta_key' => $field['field_type'],
                                                'meta_value' => $values,
                                                'field_id' => $field['field_id']
                                            ]);
                                            $allFieldInsertedForChild[] = $field['field_id'];
                                        }

                                    }
                                }  else {
                                    continue;
                                }
                            }

                            $diffFields = array_diff($allFieldsInsertedForParent,$allFieldInsertedForChild);
                            
                            foreach($diffFields as $fieldLeft){
                                $TelesalesdatasTmp = TelesalesdataTmp::where('telesaletmp_id', $parentLeadId)->where('field_id', $fieldLeft)->get();

                                foreach($TelesalesdatasTmp as $telesalesDataTemp){
                                    $telesaleTmp->teleSalesData()->create([
                                        'meta_key' => $telesalesDataTemp->meta_key,
                                        'meta_value' => $telesalesDataTemp->meta_value,
                                        'field_id' => $fieldLeft
                                    ]);

                                }
                            }
                        }

                        // \Log::info('Request All:'.print_r($request->all(),true));
                        $form = Clientsforms::with('fields')->find($form_id);

                        $requestFields = $request->fields[$x];
                        

                        //For resolve undefined variable error
                        $teleSaleId = isset($telesale->id) ? $telesale->id : false;

                        $call = $request->calltype;

                        $ref = $referenceId;
                        $cn = isset($contact_numbers) ? $contact_numbers : false;

						// for check account number exists or not in do not enroll list
						$exists = $this->isExistsInDNE($client_id, $form_id, $requestFields);
						if($exists) {
							// Account number is exists in do not enrollment list.
							\Log::info("Multi: Account number is exists in do not enrollment list.");
							$isAutoCancel = true;
							$isDoNotCancel = true;
							$disposition = Dispositions::where("client_id",$client_id)->where('type','do_not_enroll')->first();
							$dispositionId = array_get($disposition,'id');
							continue;
						}

                        $accountNumberLabel = config('constants.ACCOUNT_NUMBER_LABEL').'%';
                        $accountNumField = $form->fields()->where(\DB::raw('LOWER(label)'),'LIKE',$accountNumberLabel)->where('type', 'textbox')->first();
                        // \Log::info('$accountNumField'.print_r($accountNumField,true));
                        
                        $aLeadStatus = array();
                        $aLeadStatus['pending'] = 0;
                        $aLeadStatus['verified'] = 0;
                        $aLeadStatus['decline'] = 0;
                        $aLeadStatus['hangup'] = 0;
                        $aLeadStatus['cancel'] = 0;
                        $aLeadStatus['expired'] = 0;
                        $lead_ids = "";
                        $totCount = 0;
                        $aVerifiedLeadData = array();
                        if (!empty($accountNumField)) {
                            //Check for Duplicate Account number Validation
                            $requestFields = $request->fields[$x];
                            $accountIndices = array_keys(array_column($request->fields[$x], 'field_id'),$accountNumField->id);
                            // \Log::info('$accountIndices'.print_r($accountIndices,true));
                            $requestAccountNumber = '';
                            foreach($accountIndices as $index){
                                // \Log::info('$requestFields[$index][value]'.print_r($requestFields[$index]['value'],true));
                                if(isset($requestFields[$index]['value']['value']) && $requestFields[$index]['value']['value']){
                                    $requestAccountNumber = $requestFields[$index]['value']['value'];
                                }
                            }
                            \Log::info('$requestAccountNumber'.$requestAccountNumber);

                            //Duplicate Account number Validation
                            $fieldIds = FormField::where(\DB::raw('LOWER(label)'),'LIKE',$accountNumberLabel)->where('type','textbox')->pluck('id')->toArray();
                            $teleSalesData = Telesalesdata::where('meta_key','value')->where('meta_value',$requestAccountNumber)->whereIn('field_id',$fieldIds)->pluck('telesale_id')->toArray();

                            $intervalDays = getSettingValue($client_id,'interval_days_alert1_tele',null);
                            $teleSales = Telesales::whereIn('id',$teleSalesData);
                            if (!empty($intervalDays) && $intervalDays > 0) {
                                $intervalDate = today()->subDays($intervalDays);
                                $teleSales->whereDate('created_at','>=',$intervalDate);
                            }
                            $teleSales = $teleSales->get();
                            // \Log::info('$requestAccountNumberMatch'.print_r($teleSales,true));                   

                            foreach($teleSales AS $teleSale){
                                $lead_ids .= $teleSale->id . ",";
                                if($teleSale->status == 'verified'){
                                    $aLeadStatus['verified'] = 'Verified Leads: '.isset($aLeadStatus['verified']) ? $aLeadStatus['verified'] + 1 : 1;
                                }

                                if($teleSale->status == 'pending'){
                                    $aLeadStatus['pending'] = isset($aLeadStatus['pending']) ?  $aLeadStatus['pending'] + 1 : 1;
                                }

                                if($teleSale->status == 'decline'){
                                    $aLeadStatus['decline'] = isset($aLeadStatus['decline']) ? $aLeadStatus['decline'] + 1 : 1;
                                }

                                if($teleSale->status == 'hangup'){
                                    $aLeadStatus['hangup'] = isset($aLeadStatus['hangup']) ? $aLeadStatus['hangup'] + 1 : 1;
                                }

                                if($teleSale->status == 'cancel'){
                                    $aLeadStatus['cancel'] = isset($aLeadStatus['cancel']) ? $aLeadStatus['cancel'] + 1 : 1;
                                }

                                if($teleSale->status == 'expired'){
                                    $aLeadStatus['expired'] = isset($aLeadStatus['expired']) ? $aLeadStatus['expired'] + 1 : 1;
                                }

                                $totCount = $totCount + 1;
                            }
                            if($totCount >= getSettingValue($client_id,'max_times_alert1_tele') && $isOnAlertTele && isOnSettings($client_id,'is_enable_alert1_tele')){
                                if($totCount > 1) {
                                    $time= ' times.';
                                } else {
                                    $time= ' time.';
                                }
                                $isAlert1Critical = isOnSettings($client_id, 'is_critical_alert1_tele');
                                $alerts = 'Sales agent used an account number that has been used '.$totCount.$time;
                                $message = __('critical_logs.messages.Event_Type_5',['count'=>$totCount]);
                                $lead_ids = rtrim($lead_ids,","); 
                                $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                                $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_5');
                                $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                                $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$message,null,$temp_lead_id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alerts);
                                
                                //Check if alert is critical or not
                                if ($isAlert1Critical) {
                                    $isAutoCancel = true;
                                } else if(isOnSettings($client_id,'is_show_agent_alert1_tele')) {
                                    $validationData['acc'.$x]['title'] = 'Enrollment '.($enrollCounter).': This Account number has been used '.$totCount.' times.';
                                    $validationData['acc'.$x]['msg'] = 'Verified Leads: '.$aLeadStatus['verified'] .' Pending Leads: '.$aLeadStatus['pending'].' Declined Leads: '.$aLeadStatus['decline'].' Disconnected Calls: '.$aLeadStatus['hangup'].' Cancelled Leads: '.$aLeadStatus['cancel'].' Expired Leads: '.$aLeadStatus['expired'];
                                }
                            }

                            // for check settings is on or off
                            if ($isOnAlertTele && isOnSettings($client_id,'is_enable_alert2_tele')) {

                                $intervalDays = getSettingValue($client_id,'interval_days_alert2_tele',null);
                                $teleSales = Telesales::whereIn('id',$teleSalesData)->where('status','verified');
                                if (!empty($intervalDays) && $intervalDays > 0) {
                                    $intervalDate = today()->subDays($intervalDays);
                                    $teleSales->whereDate('created_at','>=',$intervalDate);
                                }
                                $aVerifiedLeadData = $teleSales->pluck('id');                                        
                            
                                $verifiedTelesalesData = Telesalesdata::where(function($query) use ($aVerifiedLeadData){
                                                            $query->where('meta_key','first_name')
                                                            ->orWhere('meta_key','last_name');
                                                        })
                                                        ->whereIn('telesale_id',$aVerifiedLeadData)
                                                        ->whereHas('formFieldsData',function($query) {
                                                            $query->where('is_primary',1);
                                                        })
                                                        ->get();
                                $firstName = '';
                                $lastName = '';

                                //Check for primary first name anddd last name
                                $requestFields = $request->fields[$x];
                                $fullnameIndices = array_keys(array_column($request->fields[$x], 'field_type'),'fullname');
                                $requestFullName = '';
                                foreach($fullnameIndices as $index){
                                    if(isset($requestFields[$index]['value']['is_primary']) && $requestFields[$index]['value']['is_primary'] == 1){
                                            $requestFullName = (isset($requestFields[$index]['value']['first_name']) && isset($requestFields[$index]['value']['last_name'])) ? $requestFields[$index]['value']['first_name'].' '.$requestFields[$index]['value']['last_name'] : '';
                                    }
                                }
                                $lead_ids = "";
                                $critical_message = "";

                                foreach($verifiedTelesalesData AS $verifiedTelesale)
                                {
                                    if($verifiedTelesale->meta_key == "first_name")
                                    $lead_ids .= $verifiedTelesale->telesale_id .",";   
                                }

                                foreach($verifiedTelesalesData AS $verifiedTelesale){
                                
                                $verifiedTelesalesFirstName = Telesalesdata::where('meta_key','first_name')->where('telesale_id',$verifiedTelesale->telesale_id)->first();
                                $verifiedTelesalesLastName = Telesalesdata::where('meta_key','last_name')->where('telesale_id',$verifiedTelesale->telesale_id)->first();

                                $firstName = $verifiedTelesalesFirstName->meta_value;
                                $lastName = $verifiedTelesalesLastName->meta_value;
                                $fullName = $firstName .' '.$lastName;
                                if($requestFullName == $fullName){
                                    $alerts = 'Sales agent submitted an enrollment for an existing customer.';
                                    $critical_message =  __('critical_logs.messages.Event_Type_6');
                                    if(isOnSettings($client_id,'is_show_agent_alert2_tele')) {
                                        $validationData['name'.$x]['title'] = 'Enrollment '.($enrollCounter).': This Customer is already enrolled with '.$client->name;
                                        $validationData['name'.$x]['msg'] = 'There is a verified enrollment associated with this customer and account number.';
                                    }
                                    
                                    break;
                                }else{
                                    continue;
                                }
                                }
                                
                                if($lead_ids !="")
                                    $lead_ids = rtrim($lead_ids,",");
                                if($critical_message != ""){
                                    $isTeleAlert2Critical = isOnSettings($client_id, 'is_critical_alert2_tele');
                                    $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                                    $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_6');
                                    $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                                    $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$critical_message,null,$temp_lead_id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alerts);
                                    
                                    //Check if alert is critical or not
                                    if ($isTeleAlert2Critical) {
                                        $isAutoCancel = true;
                                    }

                                }
                                
                                // \Log::info('$verifiedTelesalesData$validationData'.print_r($validationData,true));
                            }

                            // for check settings is on or off

                        if($isOnAlertTele && isOnSettings($client_id,'is_enable_alert8_tele',false)){
                            $requestFields = $request->fields[$x];
                            $accountIndices = array_keys(array_column($request->fields[$x], 'field_id'),$accountNumField->id);
                            // \Log::info('$accountIndices'.print_r($accountIndices,true));
                            $requestAccountNumber = '';
                            foreach($accountIndices as $index){
                                // \Log::info('$requestFields[$index][value]'.print_r($requestFields[$index]['value'],true));
                                if(isset($requestFields[$index]['value']['value']) && $requestFields[$index]['value']['value']){
                                    $requestAccountNumber = $requestFields[$index]['value']['value'];
                                }
                            }
                            \Log::info('$requestAccountNumber'.$requestAccountNumber);
                            //Duplicate Account number Validation
                            $fieldIds = FormField::where(\DB::raw('LOWER(label)'),'LIKE',$accountNumberLabel)->where('type','textbox')->pluck('id')->toArray();
                            $teleSalesData = Telesalesdata::where('meta_key','value')->where('meta_value',$requestAccountNumber)->whereIn('field_id',$fieldIds)->pluck('telesale_id')->toArray();
                            $intervalDays = getSettingValue($client_id,'interval_days_alert8_tele',null);
                            $teleSales = Telesales::whereIn('id',$teleSalesData)->where('status',config('constants.LEAD_TYPE_VERIFIED'));
                            if (!empty($intervalDays) && $intervalDays > 0) {
                                $intervalDate = today()->subDays($intervalDays);
                                $teleSales->whereDate('created_at','>=',$intervalDate);
                            }
                            $teleSales = $teleSales->get();
                            // \Log::info('$requestAccountNumberMatch'.print_r($teleSales,true));
                            $aLeadStatus = array();
                            $aLeadStatus['verified'] = 0;
                            $aVerifiedLeadData = array();
                            $lead_ids = "";
                            if(!empty($teleSales->toArray())){ 
                            foreach($teleSales AS $teleSale){
                                $lead_ids .= $teleSale->id . ",";
                                if($teleSale->status == 'verified'){
                                    $aLeadStatus['verified'] = 'Verified Leads: '.isset($aLeadStatus['verified']) ? $aLeadStatus['verified'] + 1 : 1;
                                }
                            }                    
                            $isAlert8Critical = isOnSettings($client_id, 'is_critical_alert8_tele');
                            $alerts = 'Sales agent used an account number that has been used in previous verified leads';
                            $message = __('critical_logs.messages.Event_Type_44');
                            $lead_ids = rtrim($lead_ids,","); 
                            $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                            $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_44');
                            $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Verified');
                            $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$message,null,$temp_lead_id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alerts);
                            
                            //Check if alert is critical or not
                            if ($isAlert8Critical) {
                                $isAutoCancel = true;
                            } else if(isOnSettings($client_id,'is_show_agent_alert8_tele')) {
                                $validationData['verify_acc_multiple'.$x]['title'] = 'Enrollment '.($x+1).': This account number has been used in previous verified leads';
                                $validationData['verify_acc_multiple'.$x]['msg'] = 'Verified Leads: '.$aLeadStatus['verified'];
                            }
                        }
                        }


                        }

                        // for check settings is on or off
                        if ($isOnAlertTele && isOnSettings($client_id,'is_enable_alert3_tele')) {
                            // Check validation for #3 Duplicate Email check
                            //Check for is primary email
                            $requestFields = $request->fields[$x];
                            $emailIndices = array_keys(array_column($request->fields[$x], 'field_type'),'email');
                            $requestEmail = '';
                            // \Log::info('$emailIndices'.print_r($emailIndices,true));
                            foreach($emailIndices as $index){
                                if(isset($requestFields[$index]['value']['is_primary']) && $requestFields[$index]['value']['is_primary'] == 1){
                                        $requestEmail = (isset($requestFields[$index]['value']['value'])) ? $requestFields[$index]['value']['value'] : '';
                                }
                            }
                            // \Log::info('$requestEmail1'.$requestEmail);
                            if($requestEmail != ''){
                                $forms = Clientsforms::where('client_id',$client_id)->pluck('id');
                                $fieldIds = FormField::where('type','email')->whereIn('form_id',$forms)->pluck('id');
                                $teleSalesData = Telesalesdata::where('meta_value', $requestEmail)->whereIn('field_id',$fieldIds)->pluck('telesale_id');
                                // \Log::info('$requestEmail$teleSalesData'.print_r($teleSalesData,true));

                                $intervalDays = getSettingValue($client_id,'interval_days_alert3_tele',null);
                                $teleSales = Telesales::whereIn('id',$teleSalesData);
                                if (!empty($intervalDays) && $intervalDays > 0) {
                                    $intervalDate = today()->subDays($intervalDays);
                                    $teleSales->whereDate('created_at','>=',$intervalDate);
                                }
                                $teleSales = $teleSales->get();

                            $aLeadStatus = array();
                            $aLeadStatus['pending'] = 0;
                            $aLeadStatus['verified'] = 0;
                            $aLeadStatus['decline'] = 0;
                            $aLeadStatus['hangup'] = 0;
                            $aLeadStatus['cancel'] = 0;
                            $aLeadStatus['expired'] = 0;
                            // $aLeadStatus['msg'] = '';

                            $aVerifiedLeadData = array();
                            $totCount = 0;
                            $lead_ids = "";

                            foreach($teleSales AS $teleSale){
                                
                                $lead_ids .= $teleSale->id . ",";

                                if($teleSale->status == 'verified'){
                                $aLeadStatus['verified'] = 'Verified Leads: '.isset($aLeadStatus['verified']) ? $aLeadStatus['verified'] + 1 : 1;
                                }

                                if($teleSale->status == 'pending'){
                                $aLeadStatus['pending'] = isset($aLeadStatus['pending']) ?  $aLeadStatus['pending'] + 1 : 1;
                                }

                                if($teleSale->status == 'decline'){
                                $aLeadStatus['decline'] = isset($aLeadStatus['decline']) ? $aLeadStatus['decline'] + 1 : 1;
                                }

                                if($teleSale->status == 'hangup'){
                                $aLeadStatus['hangup'] = isset($aLeadStatus['hangup']) ? $aLeadStatus['hangup'] + 1 : 1;
                                }

                                if($teleSale->status == 'cancel'){
                                $aLeadStatus['cancel'] = isset($aLeadStatus['cancel']) ? $aLeadStatus['cancel'] + 1 : 1;
                                }

                                if($teleSale->status == 'expired'){
                                $aLeadStatus['expired'] = isset($aLeadStatus['expired']) ? $aLeadStatus['expired'] + 1 : 1;
                                }

                                $totCount = $totCount + 1;
                            }

                            if($totCount >= getSettingValue($client_id,'max_times_alert3_tele',1)){
                                $alerts = 'Sales agent used an email address that has been used '.$totCount.' times.';
                                $message = __('critical_logs.messages.Event_Type_7',['count'=>$totCount]);

                                if(isOnSettings($client_id,'is_show_agent_alert3_tele')) {
                                    $validationData['emailCheck'.$x]['title'] = 'Enrollment '.($enrollCounter).': This email address has been used '.$totCount.' times.';
                                    $validationData['emailCheck'.$x]['msg'] = 'Verified Leads: '.$aLeadStatus['verified'] .' Pending Leads: '.$aLeadStatus['pending'].' Declined Leads: '.$aLeadStatus['decline'].' Disconnected Calls: '.$aLeadStatus['hangup'].' Cancelled Leads: '.$aLeadStatus['cancel'].' Expired Leads: '.$aLeadStatus['expired'];

                                }
                                $lead_ids = rtrim($lead_ids,",");
                                $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                                $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_7');
                                $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                                $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$message,null,$temp_lead_id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alerts);
                                
                                $isTeleAlert3Critical = isOnSettings($client_id, 'is_critical_alert3_tele',false);
                                
                                //Check if alert is critical or not
                                if ($isTeleAlert3Critical) {
                                    $isAutoCancel = true;
                                }

                                }
                            }
                        }

                        // for check settings is on or off
                        if ($isOnAlertTele && isOnSettings($client_id,'is_enable_alert4_tele')) {
                            // Check validation for #5 Duplicate Phone check against ALL LEADS in database
                            // Check for is primary phone_number
                            $requestFields = $request->fields[$x];
                            $phoneIndices = array_keys(array_column($request->fields[$x], 'field_type'),'phone_number');
                            $requestPhone = '';
                            // \Log::info('$phoneIndices'.print_r($phoneIndices,true));
                            foreach($phoneIndices as $index){
                                if(isset($requestFields[$index]['value']['is_primary']) && $requestFields[$index]['value']['is_primary'] == 1){
                                        $requestPhone = (isset($requestFields[$index]['value']['value'])) ? $requestFields[$index]['value']['value'] : '';
                                }
                            }
                            // \Log::info('$requestPhone'.print_r($requestPhone,true));
                            if($requestPhone != ''){
                                $forms = Clientsforms::where('client_id',$client_id)->pluck('id');
                                $fieldIds = FormField::where('type','phone_number')->whereIn('form_id',$forms)->pluck('id');
                                $teleSalesData = Telesalesdata::where('meta_value', $requestPhone)->whereIn('field_id',$fieldIds)->pluck('telesale_id');

                                $intervalDays = getSettingValue($client_id,'interval_days_alert4_tele',null);
                                $teleSales = Telesales::whereIn('id',$teleSalesData);
                                if (!empty($intervalDays) && $intervalDays > 0) {
                                    $intervalDate = today()->subDays($intervalDays);
                                    $teleSales->whereDate('created_at','>=',$intervalDate);
                                }
                                $teleSales = $teleSales->get();

                            $aLeadStatus = array();
                            $aLeadStatus['pending'] = 0;
                            $aLeadStatus['verified'] = 0;
                            $aLeadStatus['decline'] = 0;
                            $aLeadStatus['hangup'] = 0;
                            $aLeadStatus['cancel'] = 0;
                            $aLeadStatus['expired'] = 0;
                            // $aLeadStatus['msg'] = '';

                            $aVerifiedLeadData = array();
                            $totCount = 0;
                            
                            $lead_ids = "";
                            
                            foreach($teleSales AS $teleSale){
                                $lead_ids .= $teleSale->id .",";
                                if($teleSale->status == 'verified'){
                                $aLeadStatus['verified'] = 'Verified Leads: '.isset($aLeadStatus['verified']) ? $aLeadStatus['verified'] + 1 : 1;
                                }

                                if($teleSale->status == 'pending'){
                                $aLeadStatus['pending'] = isset($aLeadStatus['pending']) ?  $aLeadStatus['pending'] + 1 : 1;
                                }

                                if($teleSale->status == 'decline'){
                                $aLeadStatus['decline'] = isset($aLeadStatus['decline']) ? $aLeadStatus['decline'] + 1 : 1;
                                }

                                if($teleSale->status == 'hangup'){
                                $aLeadStatus['hangup'] = isset($aLeadStatus['hangup']) ? $aLeadStatus['hangup'] + 1 : 1;
                                }

                                if($teleSale->status == 'cancel'){
                                $aLeadStatus['cancel'] = isset($aLeadStatus['cancel']) ? $aLeadStatus['cancel'] + 1 : 1;
                                }

                                if($teleSale->status == 'expired'){
                                $aLeadStatus['expired'] = isset($aLeadStatus['expired']) ? $aLeadStatus['expired'] + 1 : 1;
                                }

                                $totCount = $totCount + 1;
                            }
                            // dd($aLeadStatus);
                            if($totCount >= getSettingValue($client_id,'max_times_alert4_tele')){
                                $alerts = 'Sales agent used a phone number that has been used '.$totCount.' times.';

                                if(isOnSettings($client_id,'is_show_agent_alert4_tele')) {
                                    $validationData['phoneCheck'.$x]['title'] = 'Enrollment '.($enrollCounter).': This phone number has been used '.$totCount.' times.';
                                    $validationData['phoneCheck'.$x]['msg'] = 'Verified Leads: '.$aLeadStatus['verified'] .' Pending Leads: '.$aLeadStatus['pending'].' Declined Leads: '.$aLeadStatus['decline'].' Disconnected Calls: '.$aLeadStatus['hangup'].' Cancelled Leads: '.$aLeadStatus['cancel'].' Expired Leads: '.$aLeadStatus['expired'];
                                }

                                $isTeleAlert4Critical = isOnSettings($client_id, 'is_critical_alert4_tele');
                                $lead_ids = rtrim($lead_ids,",");
                                $message =  __('critical_logs.messages.Event_Type_9',['count'=>$totCount]);
                                $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                                $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_9');
                                $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                                $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$message,null,$temp_lead_id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alerts);
                                
                                //Check if alert is critical or not
                                if ($isTeleAlert4Critical) {
                                    $isAutoCancel = true;
                                }
                            }
                            }
                        }

                        // for check settings is on or off
                        if ($isOnAlertTele && isOnSettings($client_id,'is_enable_alert5_tele')) {

                            // Checkpoint #4: Fraudulent Email check against that client’s sales agents (Tele and D2D both) Emails

                            //Check for is primary email
                            $requestFields = $request->fields[$x];
                            $emailIndices = array_keys(array_column($request->fields[$x], 'field_type'),'email');
                            $requestEmail = '';

                            foreach($emailIndices as $index){
                                if(isset($requestFields[$index]['value']['is_primary']) && $requestFields[$index]['value']['is_primary'] == 1){
                                    $requestEmail = (isset($requestFields[$index]['value']['value'])) ? $requestFields[$index]['value']['value'] : '';
                                }
                            }
                            // \Log::info('$emailIndices'.print_r($emailIndices,true));
                            if($requestEmail != ''){
                            $salesAgents = User::where('client_id',$client_id)
                            ->where('email', $requestEmail)
                            ->where('access_level','salesagent')->where('id', '<>', Auth::user()->id)->first();
                            // \Log::info('$salesAgents'.print_r($salesAgents,true));
                            // \Log::info('emailssss'.print_r($requestEmail,true));
                            
                            if($salesAgents){
                                $isTeleAlert5Critical = isOnSettings($client_id, 'is_critical_alert5_tele');
                                $alerts = 'Sales agent used an email address belonging to another sales agent.';

                                if(isOnSettings($client_id,'is_show_agent_alert5_tele')) {
                                    $validationData['salesEmailCheck'.$x]['title'] = 'Enrollment '.($enrollCounter).': This email address is associated with an existing '. array_get($client, 'name') . ' sales agent ('. array_get($salesAgents,'full_name') . ' - ID: ' . array_get($salesAgents,'userid') . ').';
                                    $validationData['salesEmailCheck'.$x]['msg'] = 'There is an active '.array_get($client, 'name') .' sales agent associated with this email address.';
                                }
                                $name = array_get($salesAgents,'full_name').'('.array_get($salesAgents,'userid').')';
                                $message =  __('critical_logs.messages.Event_Type_8',['name'=>$name]);
                                $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                                $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_8');
                                $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                                $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$message,null,$temp_lead_id,null,$lead_status,$event_type,$error_type,null,null,$alerts);
                                
                                //Check if alert is critical or not
                                if ($isTeleAlert5Critical) {
                                    $isAutoCancel = true;
                                }
                            }

                            }
                            // \Log::info('$validationData'.print_r($validationData,true));
                        }

                        

                        // for check settings is on or off
                        if ($isOnAlertTele && isOnSettings($client_id,'is_enable_alert6_tele')) {
                            // Checkpoint #6: Fraudulent Phone check against that client’s sales agents (Tele and D2D both) Phone
                            //Check for is primary phone
                            $requestFields = $request->fields[$x];
                            $phoneIndices = array_keys(array_column($request->fields[$x], 'field_type'),'phone_number');
                            $requestphone = '';

                            foreach($phoneIndices as $index){
                                if(isset($requestFields[$index]['value']['is_primary']) && $requestFields[$index]['value']['is_primary'] == 1){
                                    $requestphone = (isset($requestFields[$index]['value']['value'])) ? $requestFields[$index]['value']['value'] : '';
                                }
                            }
                            // \Log::info('$phoneIndices6'.print_r($phoneIndices,true));
                            if($requestphone != ''){

                            $salesAgents = User::where('client_id',$client_id)
                                                    ->where('access_level','salesagent')
                                                    ->whereHas('salesAgentDetails', function($q) use($requestphone) {
                                                        $q->where('phone_number', $requestphone);
                                                    })->where('id', '<>', Auth::user()->id)->first();


                            // \Log::info('$salesAgents6'.print_r($salesAgents,true));
                            // \Log::info('Phone6'.print_r($requestphone,true));
                            if($salesAgents){
                                $isTeleAlert6Critical = isOnSettings($client_id, 'is_critical_alert6_tele');
                                $alerts = 'Sales agent used a phone number belonging to another sales agent.';

                                if (isOnSettings($client_id,'is_show_agent_alert6_tele')) {
                                    $validationData['salesPhoneCheck'.$x]['title'] = 'Enrollment '.($enrollCounter).': This phone number is associated with an existing '. array_get($client, 'name') . ' sales agent ('. array_get($salesAgents,'full_name') . ' - ID: ' . array_get($salesAgents,'userid') . ').';


                                    $validationData['salesPhoneCheck'.$x]['msg'] = 'There is an active '.array_get($client, 'name') .' sales agent associated with this phone number.';
                                }
                                
                                $name = array_get($salesAgents,'full_name').'('.array_get($salesAgents,'userid').')';
                                $message =  __('critical_logs.messages.Event_Type_10',['name'=>$name]);
                                $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                                $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_10');
                                $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                                $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$message,null,$temp_lead_id,null,$lead_status,$event_type,$error_type,null,null,$alerts);
                                
                                //Check if alert is critical or not
                                if ($isTeleAlert6Critical) {
                                    $isAutoCancel = true;                                    
                                }
                            }

                            }
                        }

                        // for check settings is on or off
                        if ($isOnAlertTele && isOnSettings($client_id,'is_enable_alert7_tele')) {
                            $emailIndices = array_keys(array_column($request->fields[$x], 'field_type'),'email');
                            $requestEmail = '';
                            // \Log::info('$emailIndices'.print_r($emailIndices,true));
                            foreach($emailIndices as $index){
                                if(isset($requestFields[$index]['value']['is_primary']) && $requestFields[$index]['value']['is_primary'] == 1){
                                        $requestEmail = (isset($requestFields[$index]['value']['value'])) ? $requestFields[$index]['value']['value'] : '';
                                }
                            }
                            $requestFields = $request->fields[$x];
                            $phoneIndices = array_keys(array_column($request->fields[$x], 'field_type'),'phone_number');
                            $requestPhone = '';
                            // \Log::info('$phoneIndices'.print_r($phoneIndices,true));
                            foreach($phoneIndices as $index){
                                if(isset($requestFields[$index]['value']['is_primary']) && $requestFields[$index]['value']['is_primary'] == 1){
                                        $requestPhone = (isset($requestFields[$index]['value']['value'])) ? $requestFields[$index]['value']['value'] : '';
                                }
                            }

                            // Leads submitted using its own email or phone number
                            if($requestEmail != '' || $requestPhone != "")
                            {
                                $phoneNo = Salesagentdetail::where('phone_number',$requestPhone)
                                ->whereNotNull('phone_number')
                                ->where('user_id',Auth::user()->id)
                                ->get();
                                
                                if((Auth::user()->email == $requestEmail && !empty($requestEmail)) || $phoneNo->count() > 0)
                                {
                                    $message = $log_message = "";
                                    $event_type = $alerts = null;

                                    if ((Auth::user()->email == $requestEmail  && !empty($requestEmail)) && $phoneNo->count() > 0) {
                                        $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_4');
                                        $message = "You can not create lead with your own Email address and Your own Phone number";
                                        $alerts = "Sales agent used their own email and phone number during enrollment.";
                                        $log_message = __('critical_logs.messages.Event_Type_4');
                                    } else  {
                                        if (Auth::user()->email == $requestEmail  && !empty($requestEmail)) {
                                            $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_2');
                                            $message = "You can not create lead with your own Email address";
                                            $alerts = "Sales agent used their own email during enrollment.";
                                            $log_message = __('critical_logs.messages.Event_Type_2');
                                        } else if ($phoneNo->count() > 0) {
                                            $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_3');
                                            $message = "You cannot create lead with your own Phone Number ";
                                            $alerts = "Sales agent used their own phone number during enrollment.";
                                            $log_message = __('critical_logs.messages.Event_Type_3');
                                        }
                                    }

                                    $isTeleAlert7Critical = isOnSettings($client_id, 'is_critical_alert7_tele');

                                    if ($message != "") {
                                        $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                                        $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Cancelled');
                                        $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$message,null,$temp_lead_id,null,$lead_status,$event_type,$error_type,null,null,$alerts);
                                    }


                                    if ($isTeleAlert7Critical) {
                                        $isAutoCancel = true;
                                        
                                    } else if(isOnSettings($client_id,'is_show_agent_alert7_tele')  && !empty($message)) {
                                        $validationData['personalDetails'.$x]['title'] = 'Enrollment '.($enrollCounter).': Sales Agent used personal details';
                                        $validationData['personalDetails'.$x]['msg'] = $message;
                                    }
                                        
                                }
                
                            }
                        }

                        $enrollCounter++;
                        
                         // for check settings is on or off

                         if($isOnAlertTele && isOnSettings($client_id,'is_enable_alert9_tele',false)){
                            
                            $requestFields = $request->fields[$x];
                            $phoneIndices = array_keys(array_column($request->fields[$x], 'field_type'),'phone_number');
                            $requestPhone = '';
                            \Log::info('$phoneIndices'.print_r($phoneIndices,true));
                            foreach($phoneIndices as $index){
                                if(isset($requestFields[$index]['value']['is_primary']) && $requestFields[$index]['value']['is_primary'] == 1){
                                        $requestPhone = (isset($requestFields[$index]['value']['value'])) ? $requestFields[$index]['value']['value'] : '';
                                }
                            }
                            \Log::info('$requestPhone'.print_r($requestPhone,true));
                            $aLeadStatus = array();
                            $aLeadStatus['verified'] = 0;
                            $aVerifiedLeadData = array();
                            $lead_ids = "";
                            if($requestPhone != ''){
                                $forms = Clientsforms::where('client_id',$client_id)->pluck('id');
                                $fieldIds = FormField::where('type','phone_number')->whereIn('form_id',$forms)->pluck('id');
                                $teleSalesData = Telesalesdata::where('meta_value', $requestPhone)->whereIn('field_id',$fieldIds)->pluck('telesale_id');
    
                                $intervalDays = getSettingValue($client_id,'interval_days_alert9_tele',null);
                                $teleSales = Telesales::whereIn('id',$teleSalesData)->where('status',config('constants.LEAD_TYPE_VERIFIED'));
                                if (!empty($intervalDays) && $intervalDays > 0) {
                                    $intervalDate = today()->subDays($intervalDays);
                                    $teleSales->whereDate('created_at','>=',$intervalDate);
                                }
                                $teleSales = $teleSales->get();
                            }
                            
                            if(!empty($teleSales->toArray())){
                            foreach($teleSales AS $teleSale){
                                $lead_ids .= $teleSale->id . ",";
                                if($teleSale->status == 'verified'){
                                    $aLeadStatus['verified'] = 'Verified Leads: '.isset($aLeadStatus['verified']) ? $aLeadStatus['verified'] + 1 : 1;
                                }
                            }       
                            $isAlert9Critical = isOnSettings($client_id, 'is_critical_alert9_tele');
                            $alerts = 'Sales agent used a phone number that has been used in previous verified leads.';
                            $message = __('critical_logs.messages.Event_Type_45');
                            $lead_ids = rtrim($lead_ids,","); 
                            $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                            $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_45');
                            $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Verified');
                            $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$message,null,$temp_lead_id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alerts);
                            
                            //Check if alert is critical or not
                            if ($isAlert9Critical) {
                                $isAutoCancel = true;
                            } else if(isOnSettings($client_id,'is_show_agent_alert9_tele')) {
                                $validationData['phone_verified'.$x]['title'] = 'Enrollment '.($x+1).': This phone number has been used in previous verified leads';
                                $validationData['phone_verified'.$x]['msg'] = 'Verified Leads: '.$aLeadStatus['verified'];
                            }
                          }
                        }

                        // for check settings is on or off

                        if($isOnAlertTele && isOnSettings($client_id,'is_enable_alert11_tele',false)){
                            
                            $requestFields = $request->fields[$x];
                            $phoneIndices = array_keys(array_column($request->fields[$x], 'field_type'),'phone_number');
                            $requestPhone = '';
                            // \Log::info('$phoneIndices'.print_r($phoneIndices,true));
                            foreach($phoneIndices as $index){
                                if(isset($requestFields[$index]['value']['is_primary']) && $requestFields[$index]['value']['is_primary'] == 1){
                                        $requestPhone = (isset($requestFields[$index]['value']['value'])) ? $requestFields[$index]['value']['value'] : '';
                                }
                            }
                            // \Log::info('$requestPhone'.print_r($requestPhone,true));
                            $aLeadStatus = array();
                            $aLeadStatus['verified'] = 0;
                            $aVerifiedLeadData = array();
                            $lead_ids = "";
                            if($requestPhone != ''){
                                $forms = Clientsforms::where('client_id',$client_id)->pluck('id');
                                $fieldIds = FormField::where('type','phone_number')->whereIn('form_id',$forms)->pluck('id');
                                $teleSalesData = Telesalesdata::where('meta_value', $requestPhone)->whereIn('field_id',$fieldIds)->pluck('telesale_id');
    
                                $intervalDays = getSettingValue($client_id,'interval_days_alert11_tele',null);
                                $teleSales = Telesales::whereIn('id',$teleSalesData)->where('status',config('constants.LEAD_TYPE_VERIFIED'));
                                if (!empty($intervalDays) && $intervalDays > 0) {
                                    $intervalDate = today()->subDays($intervalDays);
                                    $teleSales->whereDate('created_at','>=',$intervalDate);
                                }
                                $aVerifiedLeadData = $teleSales->pluck('id');                                        
                            
                                $verifiedTelesalesData = Telesalesdata::where(function($query) use ($aVerifiedLeadData){
                                                            $query->where('meta_key','first_name')
                                                            ->orWhere('meta_key','last_name');
                                                        })
                                                        ->whereIn('telesale_id',$aVerifiedLeadData)
                                                        ->whereHas('formFieldsData',function($query) {
                                                            $query->where('is_primary',1);
                                                        })
                                                        ->get();
                                $firstName = '';
                                $lastName = '';

                                //Check for primary first name anddd last name
                                $requestFields = $request->fields[$x];
                                $fullnameIndices = array_keys(array_column($request->fields[$x], 'field_type'),'fullname');
                                $requestFullName = '';
                                foreach($fullnameIndices as $index){
                                    if(isset($requestFields[$index]['value']['is_primary']) && $requestFields[$index]['value']['is_primary'] == 1){
                                            $requestFullName = (isset($requestFields[$index]['value']['first_name']) && isset($requestFields[$index]['value']['last_name'])) ? $requestFields[$index]['value']['first_name'].' '.$requestFields[$index]['value']['last_name'] : '';
                                    }
                                }
                                $lead_ids = "";
                                $critical_message = "";

                                foreach($verifiedTelesalesData AS $verifiedTelesale)
                                {
                                    $verifiedTelesalesFirstName = Telesalesdata::where('meta_key','first_name')->where('telesale_id',$verifiedTelesale->telesale_id)->first();
                                    $verifiedTelesalesLastName = Telesalesdata::where('meta_key','last_name')->where('telesale_id',$verifiedTelesale->telesale_id)->first();
    
                                    $firstName = $verifiedTelesalesFirstName->meta_value;
                                    $lastName = $verifiedTelesalesLastName->meta_value;
                                    $fullName = $firstName .' '.$lastName;
                                    if($requestFullName == $fullName){
                                        $lead_ids .= $verifiedTelesale->telesale_id .",";   
                                    }
                                }
                                $lead_ids = implode(',',array_unique(explode(',',$lead_ids)));
                                foreach($verifiedTelesalesData AS $verifiedTelesale){
                                
                                $verifiedTelesalesFirstName = Telesalesdata::where('meta_key','first_name')->where('telesale_id',$verifiedTelesale->telesale_id)->first();
                                $verifiedTelesalesLastName = Telesalesdata::where('meta_key','last_name')->where('telesale_id',$verifiedTelesale->telesale_id)->first();

                                $firstName = $verifiedTelesalesFirstName->meta_value;
                                $lastName = $verifiedTelesalesLastName->meta_value;
                                $fullName = $firstName .' '.$lastName;
                                if($requestFullName == $fullName){
                                    // $lead_ids .= $verifiedTelesale->telesale_id .",";
                                    $alerts = 'Sales agent submitted an enrollment for an existing customer in previous verified leads.';
                                    $critical_message =  __('critical_logs.messages.Event_Type_46');
                                    if(isOnSettings($client_id,'is_show_agent_alert11_tele')) {
                                        $validationData['name_phone_multiple'.$x]['title'] = 'Enrollment '.($x+1).': This Customer with this phone number is already enrolled with '.$client->name;
                                        $validationData['name_phone_multiple'.$x]['msg'] = 'There is a verified enrollment associated with this customer and phone number.';
                                    }
                                    
                                    break;
                                }else{
                                    continue;
                                }
                                }
                                
                                if($lead_ids !="")
                                    $lead_ids = $lead_ids;
                                if($critical_message != ""){
                                    $isTeleAlert11Critical = isOnSettings($client_id, 'is_critical_alert11_tele');
                                    $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                                    $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_46');
                                    $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                                    $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$critical_message,null,$temp_lead_id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alerts);
                                    
                                    //Check if alert is critical or not
                                    if ($isTeleAlert11Critical) {
                                        $isAutoCancel = true;
                                    }

                                }
                                
                                // \Log::info('$verifiedTelesalesData$validationData'.print_r($validationData,true));
                            }
                            
                        }

                        // for check settings is on or off

                        if($isOnAlertTele && isOnSettings($client_id,'is_enable_alert12_tele',false)){
                            
                            $requestFields = $request->fields[$x];
                          
                            $addressIndices = array_keys(array_column($request->fields[$x], 'field_type'),'address');
                            $field_type = 'address';
                            if(empty($addressIndices)){
                            $addressIndices = array_keys(array_column($request->fields[$x], 'field_type'),'service_and_billing_address');
                            $field_type = 'service_and_billing_address';
                            }
                            // \Log::info('$addressIndices'.print_r($field_type,true));
                            $serviceAddress1 = '';
                            $serviceAddress2 = '';
                            $serviceCity = '';
                            $serviceCounty = '';
                            $serviceState = '';
                            $serviceZipcode = '';
                            $serviceCountry = '';
                            // \Log::info('$addressIndices'.print_r($addressIndices,true));
                            foreach($addressIndices as $index){
                                if($field_type == 'service_and_billing_address'){
                                        $serviceAddress1 = strtolower(trim($requestFields[$index]['value']['service_address_1']));
                                        $serviceAddress2 = strtolower(trim($requestFields[$index]['value']['service_address_2']));
                                        $serviceCity = strtolower(trim($requestFields[$index]['value']['service_city']));
                                        $serviceCounty = strtolower(trim($requestFields[$index]['value']['service_county']));
                                        $serviceState = strtolower(trim($requestFields[$index]['value']['service_state']));
                                        $serviceZipcode = strtolower(trim($requestFields[$index]['value']['service_zipcode']));
                                        $serviceCountry = strtolower(trim($requestFields[$index]['value']['service_country']));
                                }
                                if($field_type == 'address'){
                                    $serviceAddress1 = strtolower(trim($requestFields[$index]['value']['address_1']));
                                    $serviceAddress2 = strtolower(trim($requestFields[$index]['value']['address_2']));
                                    $serviceCity = strtolower(trim($requestFields[$index]['value']['city']));
                                    $serviceCounty = strtolower(trim($requestFields[$index]['value']['county']));
                                    $serviceState = strtolower(trim($requestFields[$index]['value']['state']));
                                    $serviceZipcode = strtolower(trim($requestFields[$index]['value']['zipcode']));
                                    $serviceCountry = strtolower(trim($requestFields[$index]['value']['country']));
                                }
                                }
                            
                            $aLeadStatus = array();
                            $aLeadStatus['verified'] = 0;
                            $aVerifiedLeadData = array();
                            $lead_ids = "";
                            if($serviceAddress1 != ''){
                                $forms = Clientsforms::where('client_id',$client_id)->pluck('id');
                                $fieldIds = FormField::whereIn('form_id',$forms)->where(function ($q) {
                                    $q->where('type', '=', 'address')
                                    ->orWhere('type', '=', 'service_and_billing_address');
                                })
                                ->where('is_primary', '=', '1')->pluck('id');
                                $telesalesData = Telesalesdata::whereIn('field_id',$fieldIds)->where(function ($query) {
                                    $query->whereIn('meta_key',['service_address_1','service_address_2','service_city','service_county','service_state','service_zipcode','service_country'])
                                    ->orWhereIn('meta_key',['address_1','address_2','city','county','state','zipcode','country']);
                                })
                               ->get();
                             
                                $lead_ids = "";
                                $critical_message = "";
                               
    
                                foreach($telesalesData AS $teleData)
                                {
                                    if($teleData->meta_key == "service_address_1")
                                    $lead_ids .= $teleData->telesale_id .",";   
                                    if($teleData->meta_key == "address_1")
                                    $lead_ids .= $teleData->telesale_id .",";   
                                       
                                }
                                $teleSalesIds = explode(",",$lead_ids);
                               
                                $telesales_id = [];
                               
                                foreach($teleSalesIds AS $teleId){
                                if(!empty($teleId)){
                                $verifiedTelesalesServiceAddress1 = Telesalesdata::where('telesale_id',$teleId)->where(function($query){
                                    $query->where('meta_key','service_address_1')->orWhere('meta_key','address_1');
                                })->first();
                                $verifiedTelesalesServiceAddress2 = Telesalesdata::where('telesale_id',$teleId)->where(function($query){
                                    $query->where('meta_key','service_address_2')->orWhere('meta_key','address_2');
                                })->first();
                                $verifiedTelesalesServiceCity = Telesalesdata::where('telesale_id',$teleId)->where(function($query){
                                    $query->where('meta_key','service_city')->orWhere('meta_key','city');
                                })->first();
                                $verifiedTelesalesServiceCounty = Telesalesdata::where('telesale_id',$teleId)->where(function($query){
                                    $query->where('meta_key','service_county')->orWhere('meta_key','county');
                                })->first();
                                $verifiedTelesalesServiceState = Telesalesdata::where('telesale_id',$teleId)->where(function($query){
                                    $query->where('meta_key','service_state')->orWhere('meta_key','state');
                                })->first();
                                $verifiedTelesalesServiceZipcode = Telesalesdata::where('telesale_id',$teleId)->where(function($query){
                                    $query->where('meta_key','service_zipcode')->orWhere('meta_key','zipcode');
                                })->first();
                                $verifiedTelesalesServiceCountry = Telesalesdata::where('telesale_id',$teleId)->where(function($query){
                                    $query->where('meta_key','service_country')->orWhere('meta_key','country');
                                })->first();
                               
                                if((strtolower(trim($verifiedTelesalesServiceAddress1->meta_value)) == $serviceAddress1) && (strtolower(trim($verifiedTelesalesServiceAddress2->meta_value)) == $serviceAddress2) && (strtolower(trim($verifiedTelesalesServiceCity['meta_value'])) == $serviceCity)
                                && (strtolower(trim($verifiedTelesalesServiceCounty['meta_value'])) == $serviceCounty) && (strtolower(trim($verifiedTelesalesServiceState->meta_value)) == $serviceState) && (strtolower(trim($verifiedTelesalesServiceZipcode->meta_value)) == $serviceZipcode) && (strtolower(trim($verifiedTelesalesServiceCountry->meta_value)) == $serviceCountry)){
                                   
                                    $telesales_id[] = $teleId;
                                }
                                else{
                                    
                                    continue;
                                }
                            }
                            }
                            // \Log::info('$teleSalesIds Array'.print_r($telesales_id,true));  
                            if(!empty($telesales_id)){
                                    $intervalDays = getSettingValue($client_id,'interval_days_alert12_tele',null);
                                    $teleSales = Telesales::whereIn('id',$telesales_id)->where('status',config('constants.LEAD_TYPE_VERIFIED'));
                                    if (!empty($intervalDays) && $intervalDays > 0) {
                                        $intervalDate = today()->subDays($intervalDays);
                                        $teleSales->whereDate('created_at','>=',$intervalDate);
                                    }
                                    $teleSales = $teleSales->get(); 
                                    // \Log::info('$teleSalesin'.print_r($teleSales,true));     
                                    if(!empty($teleSales->toArray())){
                                        $lead_ids = "";
                                        foreach($teleSales->toArray() AS $teleSale){
                                            // \Log::info('$teleSalesFor'.print_r($teleSales,true));   
                                            $lead_ids .= $teleSale['id'] . ",";
                                            if($teleSale['status'] == 'verified'){
                                                $aLeadStatus['verified'] = 'Verified Leads: '.isset($aLeadStatus['verified']) ? $aLeadStatus['verified'] + 1 : 1;
                                            }
                                            // \Log::info(' $aLeadStatus'.print_r($aLeadStatus['verified'],true));
                                        } 
                                        // \Log::info(' $aLeadStatus total'.print_r($aLeadStatus['verified'],true));                  
                                        $isAlert12Critical = isOnSettings($client_id, 'is_critical_alert12_tele');
                                        $alerts = 'Sales agent used service address that has been used in previous verified leads.';
                                        $message = __('critical_logs.messages.Event_Type_47');
                                        $lead_ids = rtrim($lead_ids,","); 
                                        $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                                        $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_47');
                                        $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Verified');
                                        $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$message,null,$temp_lead_id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alerts);
                                        
                                        //Check if alert is critical or not
                                        if ($isAlert12Critical) {
                                            $isAutoCancel = true;
                                        } else if(isOnSettings($client_id,'is_show_agent_alert12_tele')) {
                                            $validationData['address_verified_multiple'.$x]['title'] = 'Enrollment '.($x+1).': This service address has been used in previous verified leads';
                                            $validationData['address_verified_multiple'.$x]['msg'] = 'Verified Leads: '.$aLeadStatus['verified'];
                                        }
                                       
                                      }
                                      
                                    }
                                }
                            }
                            
                        
    

                        $teleSaleId = isset($telesale->id) ? $telesale->id : false;

                        $call = $request->calltype;

                        $ref = $referenceId;
                        $parentRefId = $parentReferenceId;
                        $cn = isset($contact_numbers) ? $contact_numbers : false;
                    }

                    $AllTemporaryTelesaleTmpId = implode(",",$allMultipleEnnrollmentId);
                    if($isAutoCancel) {
                        if($isDoNotCancel) {
                            $alerts = 'This customer cannot be enrolled with '.$client->name.'. Please contact '.$client->name.' for more details.';
                        }
                        return redirect()->route('client.cancelnewLead', ['telesaleTmpId' => $AllTemporaryTelesaleTmpId, 'id' => $client_id, 'call' => $call, 'ref' => $ref, 'cn' => $cn,'phone' => $requestPhone, 'message' => $alerts,'is_multiple'=>$is_multiple,'disposition'=>$dispositionId]);
                    }
                    if(count($validationData) > 0){
                        $telesaleTmpId = $AllTemporaryTelesaleTmpId;                        
                        return view('client.double-check-validation',compact('validationData','totCount','teleSaleId','client_id','call','ref','cn','telesaleTmpId','is_multiple'));
                    }else{

                        return redirect()->route('client.proceed_lead', ['telesaleTmpId' => $AllTemporaryTelesaleTmpId, 'is_multiple' => '1', 'id' => $client_id, 'call' => $call, 'ref' => $ref, 'cn' => $cn]);
                    }
                    

                }
            } catch (\Exception $exception) {
                \Log::error('error while creating lead : '.$exception);

                return redirect()->back()->with('error', $exception->getMessage())->withInput();
            }
            
        }
        return view('frontend.client.forms', ['client' => $client,'form' => $form, 'zipcode' => $zipcode, 'commodities' => $commodities, 'fields' => $fields, 'states' => $states,'is_enrollment_by_state' => $isEnrollmentByState, 'clonedChildData' => $clonedChildData])->withInput($request->all());

    }

    //Cancel lead
    public function cancelLead($id,$clientId)
    {
      if($id){
        $teleSale = Telesales::find($id);
        if($teleSale){
          $teleSale->status = 'cancel';
          $teleSale->save();
            return redirect()->route('my-account');
        }
      }
      return redirect()->route('my-account');
    }


    //Cancel lead
    public function cancelnewLead($id,Request $request)
    {
        if($request->telesaleTmpId){

            $dispositionId = $request->disposition;
            if($request->is_multiple) {
                $telesaleTmpIds = explode(',', $request->telesaleTmpId);
                $parentId = 0;
                foreach($telesaleTmpIds as $key => $telesaleTmpId) {
                    $teleSaleTmp = TelesalesTmp::find($telesaleTmpId);
                    if($teleSaleTmp){
                        
                        $zipcode = isset($teleSaleTmp->zipcode) ? $teleSaleTmp->zipcode : null;
                        $zipcodeData = Zipcodes::where('zipcode', $zipcode)->first();

                        if (empty($zipcodeData)) {
                            return back()->withErrors(['zipcode' => 'Invalid Zipcode'])->withInput($request->all());
                        }
                        $reqPrograms = explode(',',$teleSaleTmp->program);
                        if (is_array($reqPrograms)) {
                            foreach ($reqPrograms as $pId) {
                                $program = Programs::find($pId);
                                if (empty($program)) {
                                    return back()->with("error", 'This program was not found.');
                                }
                            }
                        } else {
                            return back()->with("error", 'Please select a valid program.');
                        }

                        $lead = $this->createLead($teleSaleTmp,$zipcodeData,$reqPrograms,'cancel', true,'',$parentId,$dispositionId);

                        if($key < 1) {
                            $parentId = $lead->id;
                        }

                    }else{
                        return redirect()->route('client.contact.from',[$id])->with('error','Something went wrong.');
                    }
                }
            } else {

                $teleSaleTmp = TelesalesTmp::find($request->telesaleTmpId);
                if($teleSaleTmp){


                    $zipcode = isset($teleSaleTmp->zipcode) ? $teleSaleTmp->zipcode : null;
                    $zipcodeData = Zipcodes::where('zipcode', $zipcode)->first();


                    if (empty($zipcodeData)) {
                        return back()->withErrors(['zipcode' => 'Invalid Zipcode'])->withInput($request->all());
                    }
                    $reqPrograms = explode(',',$teleSaleTmp->program);
                    \Log::info('$reqPrograms'.print_r($reqPrograms,true));
                    if (is_array($reqPrograms)) {
                        foreach ($reqPrograms as $pId) {
                            $program = Programs::find($pId);
                            if (empty($program)) {
                                return back()->with("error", 'This program was not found.');
                            }
                        }
                    } else {
                        return back()->with("error", 'Please select a valid program.');
                    }
                    $this->createLead($teleSaleTmp,$zipcodeData,$reqPrograms,'cancel', true,'',0,$dispositionId);

                }else{
                    return redirect()->route('client.contact.from',[$id])->with('error','Something went wrong.');
                }
            }
            if (isset($request->message) && !empty($request->message)) {
                $message = $request->message;
                return redirect()->route('my-account')->with("error", $message);
            }
            return redirect()->route('my-account')->with("success","Successfully lead cancelled.");
        }
        return redirect()->route('client.contact.from',[$id])->with('error','Something went wrong.');

    }


    //Proceed lead
    public function proceedLead($id,Request $request)
    {
        if($request->telesaleTmpId){
            // dd($request->all());
            if(isset($request->is_multiple) && $request->is_multiple == 1){
                $AllTemporaryTelesaleTmpId = $request->telesaleTmpId;
                $AllTemporaryTelesalesIdArray = explode(",",$AllTemporaryTelesaleTmpId);
                $tempSalesError = 0;
                $parent_reference_lead = 0;
                $parent_lead_id = 0;
                foreach($AllTemporaryTelesalesIdArray as $TelesalesId){
                    $teleSaleTmp = TelesalesTmp::find($TelesalesId);
                    
                    if($teleSaleTmp){

                        $zipcode = isset($teleSaleTmp->zipcode) ? $teleSaleTmp->zipcode : null;
                        $zipcodeData = Zipcodes::where('zipcode', $zipcode)->first();
    
                        if (empty($zipcodeData)) {
                            return back()->withErrors(['zipcode' => 'Invalid zip code.'])->withInput($request->all());
                        }
                        $reqPrograms = explode(',',$teleSaleTmp->program);
                        \Log::info('$reqPrograms'.print_r($reqPrograms,true));
                        if (is_array($reqPrograms)) {
                            foreach ($reqPrograms as $pId) {
                                $program = Programs::find($pId);
                                if (empty($program)) {
                                    return back()->with("error", 'This program was not found.');
                                }
                            }
                        } else {
                            return back()->with("error", 'Please select a valid program.');
                        }
                        
                        $telesale = $this->createLeadMultiple($teleSaleTmp,$zipcodeData,$reqPrograms,$parent_lead_id);
                        if($teleSaleTmp->is_multiple == 1 && $teleSaleTmp->multiple_parent_id == 0){
                            $parent_reference_lead = $telesale->refrence_id;
                            $parent_lead_id = $telesale->id;
                        }
                        $ClientFields = (new Clientsforms)->getClientFormFields($teleSaleTmp->form_id);
                        $contact_numbers = "";
                        if(count($ClientFields) > 0){
                            $ClientFields[0]->workflow_id;
                            $get_workflow_table_ids = (new ClientWorkflow)->getid($id,$ClientFields[0]->workflow_id);
                            if(count($get_workflow_table_ids) > 0) {
    
                                foreach($get_workflow_table_ids as $get_workflow_table_id){
                                    $numbers = (new ClientTwilioNumbers)->getWorkflowNumbers($id,$get_workflow_table_id->id);
                                    if(count($numbers) > 0){
                                        foreach($numbers as $phonenumber){
                                            if($contact_numbers!=""){
                                                $contact_numbers = $contact_numbers .", ". $phonenumber->phonenumber;
                                            }else{
                                                $contact_numbers = $phonenumber->phonenumber;
                                            }
                                        }
                                    }
    
                                }
                            }
                        }
                        $referenceId ='';
                    
                        // if($teleSaleTmp->is_multiple == 1 && $teleSaleTmp->multiple_parent_id == 0){
                        //     $parent_reference_lead = $telesale->refrence_id;
                        //     $parent_lead_id = $telesale->id;
                        // }
                        if(!empty($telesale)) {
                            $referenceId = $telesale->refrence_id;
                            if($telesale->multiple_parent_id == 0){
                                \Log::info($telesale->refrence_id);
                                if (!isOnSettings(array_get($telesale, 'client_id'), 'is_enable_send_contract_after_lead_verify_tele',false)) {
                                    Log::info('Save Lead Contract PDF');
                                    SendContractPDF::dispatch($telesale->id);
                                }
                            }
                        }
                        else{
                            Log::info('Telesale error');
                        }
                        // Save Lead contract PDF
                    
                    
                    
                        $this->sendCriticalAlertMail($telesale); 
                        $this->proceedToSegment($telesale);
                        $message = "Your request successfully submitted. Your reference id is &#60;strong&#62; {$referenceId} &#60;/strong&#62;";
                        
                    }else{
                        $tempSalesError = 1;
                        //return redirect()->route('client.contact.from',[$id])->with('error','Something went wrong.');
                    }
                }
                //dd($parent_reference_lead);
                if($tempSalesError == 1){
                    return redirect()->route('client.contact.from',[$id])->with('error','Something went wrong.');
                }
                return redirect()->route('client.thank-you', ['id' => $id, 'call' => $request->call, 'ref' => $parent_reference_lead, 'cn' => $contact_numbers])
                            ->with('success', $message);
            }else{
                $teleSaleTmp = TelesalesTmp::find($request->telesaleTmpId);
                // dd($teleSaleTmp);
                // Check for temp data is available or not
                if($teleSaleTmp){

                    $zipcode = isset($teleSaleTmp->zipcode) ? $teleSaleTmp->zipcode : null;
                    $zipcodeData = Zipcodes::where('zipcode', $zipcode)->first();

                    if (empty($zipcodeData)) {
                        return back()->withErrors(['zipcode' => 'Invalid zip code.'])->withInput($request->all());
                    }
                    $reqPrograms = explode(',',$teleSaleTmp->program);
                    \Log::info('$reqPrograms'.print_r($reqPrograms,true));
                    if (is_array($reqPrograms)) {
                        foreach ($reqPrograms as $pId) {
                            $program = Programs::find($pId);
                            if (empty($program)) {
                                return back()->with("error", 'This program was not found.');
                            }
                        }
                    } else {
                        return back()->with("error", 'Please select a valid program.');
                    }
                    $telesale = $this->createLead($teleSaleTmp,$zipcodeData,$reqPrograms);
                
                    $ClientFields = (new Clientsforms)->getClientFormFields($teleSaleTmp->form_id);
                    $contact_numbers = "";
                    if(count($ClientFields) > 0){
                        $ClientFields[0]->workflow_id;
                        $get_workflow_table_ids = (new ClientWorkflow)->getid($id,$ClientFields[0]->workflow_id);
                        if(count($get_workflow_table_ids) > 0) {

                            foreach($get_workflow_table_ids as $get_workflow_table_id){
                                $numbers = (new ClientTwilioNumbers)->getWorkflowNumbers($id,$get_workflow_table_id->id);
                                if(count($numbers) > 0){
                                    foreach($numbers as $phonenumber){
                                        if($contact_numbers!=""){
                                            $contact_numbers = $contact_numbers .", ". $phonenumber->phonenumber;
                                        }else{
                                            $contact_numbers = $phonenumber->phonenumber;
                                        }
                                    }
                                }

                            }
                        }
                    }
                    $referenceId ='';
                
                    if(!empty($telesale)) {
                        $referenceId = $telesale->refrence_id;
                        
                        if (!isOnSettings(array_get($telesale, 'client_id'), 'is_enable_send_contract_after_lead_verify_tele',false)) {
                            Log::info('Save Lead Contract PDF');
                            SendContractPDF::dispatch($telesale->id);
                        }
                    }
                    else{
                        Log::info('Telesale error');
                    }
                
                    $this->sendCriticalAlertMail($telesale); 
                    $this->proceedToSegment($telesale);
                    $message = "Your request successfully submitted. Your reference id is &#60;strong&#62; {$referenceId} &#60;/strong&#62;";
                    return redirect()->route('client.thank-you', ['id' => $id, 'call' => $request->call, 'ref' => $referenceId, 'cn' => $contact_numbers])
                        ->with('success', $message);
                }else{
                    return redirect()->route('client.contact.from',[$id])->with('error','Something went wrong.');
                }
            }
            
        }
        return redirect()->route('client.contact.from',[$id])->with('error','Something went wrong.');

    }

    public function contactformOld($client_id,Request $request){

        $client = Client::findOrFail($client_id);
        $userid = Auth::user()->id;
        $ClientFields = array();

        $form_id = "";
        $commodity_type = "";
        if( isset( $request->c) &&   $request->c != "") {

                $ClientFields = (new Clientsforms)->getClientFormByCommodityType($request->c,$client_id);
                $assignedform = "";

                if(count($ClientFields) == 0 ){
                    return view('frontend.client.nocontactpage');
                } else{
                    $form_id =  $ClientFields[0]->id;
                    $commodity_type =  $ClientFields[0]->commodity_type;
                }


        }


        $cloned_leadid = 0;
        $enterd_data = array();
        $utility_id = "";
        $program_id = "";
        $gasprogram_id = "";
        $gasutility_id = "";
        $electricprogram_id = "";
        $electricutility_id = "";
        $programs = array();
        $enterd_data = $utilities = array();
         $posted_data = session()->getOldInput();
            $state = "";
             $Commodity = "";
         $form_all_fields = array();
        if(isset($request->lid)){
             $cloned_leadid =  base64_decode($request->lid);
             $lead_Data =    (new Telesalesdata)->leadDetail($cloned_leadid);


             $response = "";
            if(count($lead_Data)>0){
                foreach($lead_Data as $leadDetail){
                    $enterd_data[$leadDetail->meta_key]  = $leadDetail->meta_value;


                    if($leadDetail->meta_key == 'Commodity' ){
                          $Commodity =  $leadDetail->meta_value;
                        if($Commodity == 'Dual Fuel'){
                           $commodity_type = 'DualFuel';
                            $Commodity = array('Electric','Gas');
                        }else{
                             $commodity_type = 'GasOrElectric';
                            $Commodity = array($leadDetail->meta_value);
                        }

                        $ClientFields =     (new Clientsforms)->getClientFormByCommodityType($commodity_type,$client_id);
                       if(count($ClientFields) > 0 ){
                        $form_id =  $ClientFields[0]->id;
                        $commodity_type =  $ClientFields[0]->commodity_type;
                       }

                    }
                    if($leadDetail->meta_key == 'zipcodeState' ){
                        $state =  $leadDetail->meta_value;
                    }
                    if($leadDetail->meta_key == '_utilityID' ){
                        $utility_id =  $leadDetail->meta_value;
                    }
                    if($leadDetail->meta_key == '_programID' ){
                        $program_id =  $leadDetail->meta_value;
                    }
                    if($leadDetail->meta_key == '_electricprogramID' ){
                        $electricprogram_id =  $leadDetail->meta_value;
                    }
                    if($leadDetail->meta_key == '_electricutilityID' ){
                        $electricutility_id =  $leadDetail->meta_value;
                    }
                    if($leadDetail->meta_key == '_gasutilityID' ){
                        $gasutility_id =  $leadDetail->meta_value;
                    }
                    if($leadDetail->meta_key == '_gasprogramID' ){
                        $gasprogram_id =  $leadDetail->meta_value;
                    }



                }





        }
        } else if( isset($posted_data['fields']) && count($posted_data['fields']) > 0  ){
                          $enterd_data = $posted_data['fields'];
                           if( isset($enterd_data['zipcodeState']) ){
		                        $state =  $enterd_data['zipcodeState'];
		                    }
		                    if( isset($enterd_data['Commodity']) ){
		                        $Commodity =  $enterd_data['Commodity'];
		                    }

                          $form_all_fields = ( isset($enterd_data['multiple'][0]) ) ? $enterd_data['multiple'][0] : array();
                          $utility_id = ( isset($form_all_fields['_utilityID']) ) ? $form_all_fields['_utilityID'] : '';
                          $program_id = ( isset($form_all_fields['_programID']) ) ? $form_all_fields['_programID'] : '';
                          $electricprogram_id = ( isset($form_all_fields['_electricprogramID']) ) ? $form_all_fields['_electricprogramID'] : '';
                          $electricutility_id = ( isset($form_all_fields['_electricutilityID']) ) ? $form_all_fields['_electricutilityID'] : '';
                          $gasutility_id = ( isset($form_all_fields['_gasutilityID']) ) ? $form_all_fields['_gasutilityID'] : '';
                          $gasprogram_id = ( isset($form_all_fields['_gasprogramID']) ) ? $form_all_fields['_gasprogramID'] : '';




        }
      if($state != "" &&  $Commodity != ""){
      	              if($Commodity == 'Dual Fuel'){
                           $commodity_type = 'DualFuel';
                            $Commodity = array('Electric','Gas');
                        }else{
                             $commodity_type = 'GasOrElectric';
                            $Commodity = array($Commodity);
                        }
           $utilities = (new Programs)->geUtilities($client_id,$state, $Commodity );



              }
              if($utility_id !== ""){
                $programs =   (new Programs)->getAllPrograms($client_id,$utility_id );
              }

    $electricutilities = array();
    $gasutilities = array();
    $electricprograms = array();
    $gasprograms = array();
    $electricprogram_detail = $gasprogram_detail = "";
    if($commodity_type == 'DualFuel')
    {
        if( count( $utilities ) > 0 ){
            foreach($utilities as $utility){
              if( $utility->commodity == 'Gas'){
                $gasutilities[] = $utility;
              }
              if( $utility->commodity == 'Electric'){
                $electricutilities[] = $utility;
              }
            }
        }
        if($electricutility_id !="") {
            $electricprograms =   (new Programs)->getAllPrograms($client_id,$electricutility_id );
        }
        if($gasutility_id !="") {
            $gasprograms =   (new Programs)->getAllPrograms($client_id,$gasutility_id );
        }
        if($electricprogram_id !=""){
            $program_data  =  (new Programs)->singleProgram($electricprogram_id);
            $electricprogram_detail = $program_data[0];
           }
           if($gasprogram_id !=""){
            $program_data  =  (new Programs)->singleProgram($gasprogram_id);
            $gasprogram_detail = $program_data[0];
           }




    }


        $clientUtilityProgramTagData = (new ScriptQuestions)->clientUtilityProgramTagData($form_id);
          $zipcodes =    (new UtilityZipcodes)-> getUtilityZipcodes($utility_id);

          $utility_program =   array();

        $program_detail = array();
		if($program_id !=""){
			$program_data  =  (new Programs)->singleProgram($program_id);
			if(count($program_data) > 0){
			  $program_detail = $program_data[0];
			}

		}



        //$utility_detail = (new Utilities)->getUtility($utility_id);
        return view('frontend.client.contactpage',compact('client_id','ClientFields','client', 'zipcodes','enterd_data','cloned_leadid','programs','utilities','utility_id','program_id', 'program_detail','commodity_type','electricutilities', 'gasutilities','gasprograms','electricprograms','electricprogram_id','gasprogram_id', 'electricprogram_detail','gasprogram_detail', 'electricutility_id','gasutility_id', 'state','form_all_fields' ));
        /*,'utility_program','utility_detail', */
    }
    public function actioncontact($client_id,Request $request){


         if(empty($request->formid)){
             abort(404);
         }
       //  dd($request);
         $validate = $this->validateLeadData($request,true);
         if( count($validate['messages']) > 0 ){
         	foreach ($validate['messages'] as $validate_message) {
         		$message[] = $validate_message['message'];
         	}
         	return redirect()->back()->withInput()->withErrors($message);
         }




        $lead_data['client_id'] = $client_id;
        $lead_data['form_id'] = $request->formid;


        $lead_data['user_id'] = Auth::user()->id;

        if($request->parent_id > 0) {
            $lead_data['cloned_by'] = Auth::user()->id;
            $lead_data['parent_id'] = $request->parent_id;
        }else{
            $lead_data['cloned_by'] = 0;
            $lead_data['parent_id'] = 0;
        }



        $lead_Detail_for_mail = "";


       $number_of_records = 0;
       $parent_lead = 0;
       $parent_reference = 0;
       $multiple_parent_id = 0;
       $is_multiple = 0;
       if(count($request->fields['multiple']) > 1){
         $is_multiple = 1;
       }

    //    $check_verification_number = 2;
    //    $validate_num = $verification_number = "";
    //        while ($check_verification_number > 1){
    //            $verification_number = rand(1000000,9999999);
    //            $validate_num =   (new Telesales)->validateConfirmationNumber($verification_number);
    //            if( !$validate_num ){
    //                $check_verification_number = 0;
    //            }else{
    //                $check_verification_number ++;
    //            }



    //        }

        foreach($request->fields['multiple'] as $single_data){

            $refrence_id =$this->get_client_salesceter_location_code($client_id,Auth::user()->salescenter_id,Auth::user()->location_id);
            $lead_data['refrence_id'] = $refrence_id;
            $lead_data['is_multiple'] = $is_multiple;
            $lead_data['multiple_parent_id'] = $multiple_parent_id;


        //    $lead_data['verification_number'] = $verification_number;

            $telesale_id = (new Telesales)->createLead($lead_data);

            if( $number_of_records == 0 ){
                $parent_lead =  $telesale_id;
                $parent_reference = $refrence_id;
                $multiple_parent_id = $telesale_id;

            }
            if( isset($request->fields['Commodity'])){
                $single_lead_Data= array(
                    'telesale_id' => $telesale_id,
                    'meta_key' => 'Commodity',
                    'meta_value' => $request->fields['Commodity'],
                );
                //$lead_Detail_for_mail.="<tr><td>".ucfirst($meta_key)."</td><td>".$val."</td></tr>";
                  (new Telesalesdata)->createLeadDetail($single_lead_Data);
            }
            if( isset($request->fields['zipcode'])){
                $single_lead_Data= array(
                    'telesale_id' => $telesale_id,
                    'meta_key' => 'zipcode',
                    'meta_value' => $request->fields['zipcode'],
                );
               // $lead_Detail_for_mail.="<tr><td>".ucfirst($meta_key)."</td><td>".$val."</td></tr>";
                  (new Telesalesdata)->createLeadDetail($single_lead_Data);
            }
            if( isset($request->fields['zipcodeState'])){
                $single_lead_Data= array(
                    'telesale_id' => $telesale_id,
                    'meta_key' => 'zipcodeState',
                    'meta_value' => $request->fields['zipcodeState'],
                );
               // $lead_Detail_for_mail.="<tr><td>".ucfirst($meta_key)."</td><td>".$val."</td></tr>";
                  (new Telesalesdata)->createLeadDetail($single_lead_Data);
            }


            foreach( $single_data as  $meta_key=>$meta_value){
                if(is_array($meta_value)){
                    ksort($meta_value);
                    $val =  implode(',',$meta_value);
                }else{
                    $val = $meta_value;
                }

                $single_lead_Data= array(
                    'telesale_id' => $telesale_id,
                    'meta_key' =>$meta_key,
                    'meta_value' => $val,
                );
                $lead_Detail_for_mail.="<tr><td>".ucfirst($meta_key)."</td><td>".$val."</td></tr>";
                  (new Telesalesdata)->createLeadDetail($single_lead_Data);
            }

            $single_lead_Data= array(
                'telesale_id' => $telesale_id,
                'meta_key' =>"Lead Verification ID",
                'meta_value' => $verification_number,
            );
            (new Telesalesdata)->createLeadDetail($single_lead_Data);
            $single_lead_Data= array(
                'telesale_id' => $telesale_id,
                'meta_key' =>"Call type",
                'meta_value' => $request->calltype,
            );
            (new Telesalesdata)->createLeadDetail($single_lead_Data);
            $number_of_records++;

        }








        $ClientFields = (new Clientsforms)->getClientFormFields($request->formid);
        $contact_numbers = "";
            if(count($ClientFields) > 0){
                $ClientFields[0]->workflow_id;
                $get_workflow_table_ids = (new ClientWorkflow)->getid($client_id,$ClientFields[0]->workflow_id);
                if(count($get_workflow_table_ids) > 0) {

                    foreach($get_workflow_table_ids as $get_workflow_table_id){
                        $numbers = (new ClientTwilioNumbers)->getWorkflowNumbers($client_id,$get_workflow_table_id->id);
                        if(count($numbers) > 0){
                            foreach($numbers as $phonenumber){
                                if($contact_numbers!=""){
                                    $contact_numbers = $contact_numbers .", ". $phonenumber->phonenumber;
                                }else{
                                    $contact_numbers = $phonenumber->phonenumber;
                                }

                            }
                        }

                    }
                }
            }
            $message ='Hey <strong>'.Auth::user()->first_name.'</strong> <br>
            Your lead has been created at tpv.plus and your reference ID is <b>'.$parent_reference.'</b>. Please call on '.$contact_numbers.' for verification.
            Here is your lead detail: <table style="width:100%;"><tr><th align="left">Field</th> <th align="left"> Value</th></tr>'.$lead_Detail_for_mail.'</table>';
            $to      = Auth::user()->email;
            $subject = 'Lead Created at TPV';
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            // More headers
            $headers .= 'From: <noreply@tpv.plus>' . "\r\n";
           // $headers .= 'Cc: noreply@tpv.plus' . "\r\n";
          //  mail($to, $subject, $message, $headers);
           // $contact_numbers


        $message = "Your request successfully submitted. Your reference id is &#60;strong&#62; {$parent_reference} &#60;/strong&#62;";
        return redirect()->route('client.thank-you',['id' =>$client_id ,'call'=> $request->calltype,'ref' => $parent_reference,'cn' => $contact_numbers])
        ->with('success',$message);

    }

    function sendmail_to_salesagent($telesale_id){

    }

    public function contactthanks($client_id, Request $request){
        $company = Client::findOrFail($client_id);
       
        $contact_number = null;
        // if ($client_id == config()->get('constants.CLIENT_MEGA_ENERGY_ID') || 
        //     $client_id == config()->get('constants.CLIENT_BOLT_ENEGRY_CLIENT_ID') ||
        //     $client_id == config()->get('constants.CLIENT_SUNRISE_CLIENT_ID')) {
        //     $contactNumberData = ClientTwilioNumbers::where('client_id', $client_id)->where('type', 'customer_verification')->first();
        // } else {
        //     $contactNumberData = ClientTwilioNumbers::where('client_id', $client_id)->where('type', 'customer_call_in_verification')->first();
        // }

        $contactNumberData = ClientTwilioNumbers::where('client_id', $client_id)->where('type', 'customer_verification')->first();
         
        if( !empty($contactNumberData) ){
            $contact_number = array_get($contactNumberData, "phonenumber", "");
            $contact_number = str_replace("+", "", $contact_number);
            $contact_number = preg_replace(config()->get('constants.DISPLAY_PHONE_NUMBER_FORMAT'), config()->get('constants.PHONE_NUMBER_REPLACEMENT'), $contact_number);
         }
        $lead = Telesales::select('id')->where('refrence_id',$request->ref)->firstOrFail();
        $lead_state = TelesalesZipcode::join('zip_codes','zip_codes.id','=','telesales_zipcodes.zipcode_id')->where('telesales_zipcodes.telesale_id',$lead['id'])->select('state')->first();
        if(isOnSettings($client_id, 'is_enable_self_tpv_tele')){
            $states = getSettingValue($client_id,'restrict_states_self_tpv_tele',null);
            $client_states = !empty($states) ? explode(',',$states) : '';
            if(!empty($client_states)){
                $restrict_state = in_array($lead_state['state'],$client_states);
            }else{
                $restrict_state = true;
            }
            
        }
        return view('frontend.client.contact_thankyou',compact('client_id','company','contact_number','lead','restrict_state'));
    }

    public function selfverify($leadId, Request $request){
        $request->validate([
            'verification_mode' => 'required|max:255',
        ]); 
        try {
            /* store self verification mode and send link via mail and text */
            $response = $this->storeSelfVerifyLink($leadId,$request->verification_mode);
            if (isset($response['status']) && $response['status']) {                
                return redirect()->route('my-account')->with('success',$response['message']);
            } else {
                return redirect()->route('my-account')->with('error',$response['message']);
            }
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->route('my-account')->with('error',$e->getMessage());
        }
        return view('frontend.client.contact_thankyou',compact('client_id','company','contact_number'));
    }

    public function  leads($id,Request $request)
    {
        $client_id = $id;
        $this->CheckClientUser($client_id);

        $sale_detail = array();
        $reference_id ="";
        $sale_info  = array();
        $reviewedby = "";
        $image_data = array();
        $formFieldsArr = Config('constants.FormFields');
        if(isset($request->ref)){
            $reference_id = $request->ref;
            $sale_info = (new Telesales)->getLeadInfo($reference_id);

            if(isset($sale_info->reviewed_by) && $sale_info->reviewed_by > 0 ){
                $user = 	(new User)->getUser($sale_info->reviewed_by);
                $reviewedby = $user->first_name;
            }
            if(!empty($sale_info)){
                  $sale_id = $sale_info->id ;
                  $form_id =   $sale_info->form_id;
                   $form_fields_data =   (new Clientsforms)->getClientFormDetail($form_id);

                   if($form_fields_data){
                    $formFields = json_decode($form_fields_data->form_fields);

                    foreach ($formFields as  $field_options) {
                      if( !empty($field_options->label_text))
                       {
                        //$formFieldsArr[] = $field_options->label_text;
                        if($field_options->label_text == 'Full name'){
                          $formFieldsArr[] = "First name";
                          $formFieldsArr[] = "Middle initial";
                          $formFieldsArr[] = "Last name";
                        }else if($field_options->label_text == 'Billing full name'){
                          $formFieldsArr[] = "Billing first name";
                          $formFieldsArr[] = "Billing middle name";
                          $formFieldsArr[] = "Billing last name";
                        }else if($field_options->label_text == 'Billing Address'){
                          $formFieldsArr[] = "BillingAddress";
                          $formFieldsArr[] = "BillingZip";
                          $formFieldsArr[] = "BillingCity";
                          $formFieldsArr[] = "BillingState";
                        }else{
                          $formFieldsArr[] = $field_options->label_text;
                        }
                       }
                    }

                  }




                    $sale_detail = (new Telesalesdata)->leadDetail($sale_id);

                  $media_files =     DB::table('leadmedia')
              ->select('leadmedia.*')
              ->whereRaw("id in (
                select max(id) from leadmedia as b where leadmedia.telesales_id = b.telesales_id and b.type in ('image','audio') group by b.type)")
              ->where('leadmedia.telesales_id',$sale_id)->get();


             if( count($media_files) > 0) {
               foreach($media_files as $mediafile){
                 $file_type = 'Recording';
                 if($mediafile->type == 'image'){
                  $file_type = 'Signature';
                 }
                 $image_data[] = array(
                   'name' => $mediafile->name,
                   'url' => Storage::url($mediafile->url),
                   'file_type' => $file_type,
                 );
               }
             }
             //print_r($image_data);
            }

        }
        $client = (new Client )->getClientinfo($client_id);


        return view('client.lead',compact( 'client_id','sale_detail','reference_id','sale_info','reviewedby','client','image_data','formFieldsArr'));
    }

    /**
     * This method is used to find salescenter
     */
    public function findsalecenter(Request $request){
        $clients = (new Client)->getClientsList();
        $client_id = "";
        $client_salescenters = array();
        if(isset($request->client)){
            $client_id = $request->client;
            $client_salescenters = Salescenter::where([
                ['client_id', '=' ,$client_id]
            ])->orderBy('id','DESC')->paginate(20);
        }
        return view('client.findsalescenter.index',compact( 'clients','client_id','client_salescenters'))
         ->with('i', ($request->input('page', 1) - 1) * 20);
    }

    /**
     * This method is used to find salesagent
     */
    public function findsalesagents(Request $request){

        $clients = (new Client)->getClientsList();
        $client_id = "";
        $users = array();
        $sale_centers = array();
        $salecenter_id = "";
        $location_id = "";
        $locations = array();
        if(isset($request->client)){
            $client_id = $request->client;
            $this->CheckClientUser($client_id);

            if(!empty($request->salecenter)){
                $salecenter_id = $request->salecenter;
                $locations = (new Salescenterslocations)->getLocationsInfo($client_id,$salecenter_id);
            }
            if(!empty($request->location)){
                $location_id = $request->location;
            }
            $users = (New User)->getSalesagents($client_id,$salecenter_id,$location_id );
            $sale_centers = (New Salescenter)->getSalesCentersListByClientID($client_id);

        }

        return view('client.findsalesagents.index',compact( 'clients','client_id','users','sale_centers','salecenter_id','locations','location_id'))
         ->with('i', ($request->input('page', 1) - 1) * 20);
    }

    /**
     * This method is used to get client salescenter location code
     */
    function get_client_salesceter_location_code($client_id,$selescenter_id,$location_id){
          $client_code = (new Client)->getClientCode($client_id);
          $salescenter_code = (new Salescenter)->getSalescenterCode($selescenter_id);
          $salescenter_location_code = (new Salescenterslocations)->getSaleslocationCode($location_id);
          $new_id = (new Telesales)->nextAutoID();
          return $client_code.'-'.$salescenter_code.'-'.$salescenter_location_code.'-'.$new_id ;
    }


    function contactforms($clientId, Request $request){
        $this->CheckClientUser($clientId);
        $client_id = $clientId;
        $client = (new Client )->getClientinfo($clientId);
        $forms_list = (new Clientsforms)->getClientForms($clientId);
        $timeZone = Auth::user()->timezone;
        if ($request->ajax()) {
            $forms = Clientsforms::where('client_id', $clientId)->get();
            return DataTables::of($forms)
                ->editColumn('created_at', function($form) use($timeZone){

                    if(!empty($form->created_at)) {
                        return $form->created_at->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat());
                    }
                    return 'N/A';
                })
                ->editColumn('updated_at', function($form) use($timeZone){

                    if(!empty($form->updated_at)) {
                        return $form->updated_at->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat());
                    }
                    return 'N/A';
                })
                ->addColumn('script', function($form) use ($clientId){

                    if (\auth()->user()->hasPermissionTo('view-scripts')) {
                        return '<a href="' . route('admin.clients.scripts.index', array('client_id' => $clientId, 'form_id' => $form->id)) . '"  data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="View Scripts" role="button" class="btn">View</a>';
                    } else {
                        return '';
                    }
                })
                    ->addColumn('action', function($form) use ($clientId,$client){
                    $viewBtn = $editBtn = $cloneBtn=  $statusBtn = $deleteBtn = '';

                    if (\auth()->user()->hasPermissionTo('view-forms')) {
                        $viewBtn = '<button   data-toggle="tooltip" data-id="'.$form->id.'" data-placement="top" data-container="body" title="" data-original-title="View Form" role="button"  class="btn view_lead_form" >' . getimage("images/view.png") . '</button>';
                    }else{
                        $viewBtn = '<button  title="View Form" role="button"  class="btn view_lead_form cursor-none" >' . getimage("images/view-no.png") . '</button>';
                    }
                    if (\auth()->user()->hasPermissionTo('edit-form') && $client->isActive()) {
                        $editBtn = '<a  href="' . route('client.contact-page-layout', ['id' => $clientId, 'formid' => $form->id]) . '"  data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Edit Form" role="button" class="btn">' . getimage("images/edit.png") . '</a>';
                    }else{
                        $editBtn = '<a data-original-title="Edit Form" role="button" class="btn cursor-none">' . getimage("images/edit-no.png") . '</a>';
                    }

                    if (\auth()->user()->hasPermissionTo('copy-form') && $client->isActive()) {
                        $cloneBtn = '<button  data-url="' . route('client.contact-page-layout.clone', ['id' => $clientId, 'formid' => $form->id]) . '" data-formname="' . $form->formname . '"   data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Clone Form" role="button"  class="btn clone_lead_form">' . getimage("images/copy.png") . '</button>';
                    }else{
                        $cloneBtn = '<button data-original-title="Clone Form" role="button"  class="btn cursor-none">' . getimage("images/copy-no.png") . '</button>';
                    }

                    if (\auth()->user()->hasPermissionTo('deactivate-form') && $client->isActive()) {
                        if (array_get($form, 'status') == 'active') {
                            $statusBtn = '<button
                          class=" btn change-status-form"  data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Deactivate Form" data-id="' . $form->id . '" data-name="' . $form->formname . '"
                            data-status="inactive" data-text-status="deactivated"  role="button" >' . getimage("images/activate_new.png") . '</button>';
                        } else {
                            $editBtn = '<a data-original-title="Edit Form" role="button" class="btn cursor-none">' . getimage("images/edit-no.png") . '</a>';
                            $statusBtn = '<button
                          class="btn change-status-form"  data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Activate Form" data-id="' . $form->id . '" data-name="' . $form->formname . '"
                              data-status="active" data-text-status="activated"  role="button"  >' . getimage("images/deactivate_new.png") . '</button>';
                        }
                    }else{
                        $statusBtn = '<button
                          class="btn cursor-none" role="button"  >' . getimage("images/deactivate_new-no.png") . '</button>';
                    }
                    if(auth()->user()->hasPermissionTo('delete-form')) {
                        $class = 'delete-leadform';
                        $attributes = [
                            "data-original-title" => "Delete Enrollment Form",
                            "data-id" => $form->id,
                            "data-name" => $form->formname,
                            "data-status" =>"delete",
                            "data-text-status"=>"deleted"
                            
                        ];
                        $deleteBtn = getDeleteBtn($class, $attributes);
                    } 

                    if(empty($viewBtn) && empty($editBtn) && empty($cloneBtn) && empty($statusBtn) && empty($deleteBtn) ) {
                        return '';
                    } else {
                        return '<div class="btn-group">'.$viewBtn.$editBtn.$cloneBtn.$statusBtn.$deleteBtn.'<div>';
                    }
                })
                ->rawColumns(['created_at','script', 'action'])
                ->make(true);
        }

        return view('client.forms.formslist',compact( 'forms_list','client_id','client'))
        ->with('i', ($request->input('page', 1) - 1) * 20);

    }

    // public function deleteLeadForm($id)
    // {
    //     // dd($id);
    //     $delete = Clientsforms::where('id',$id)->delete();
    //     if($delete)
    //         return response()->json([ 'status' => 'success',  'message'=>'Lead form successfully deleted.']);
    //     else
    //         return response()->json([ 'status' => 'success',  'message'=>'Something went wrong, Please try again later.']);
    // }

    /**
     * This method is used to get random number
     */
     function random_num($length = 16) {
            $randstr="";
            srand((double) microtime(TRUE) * 1000000);
            //our array add all letters and numbers if you wish
            $chars = array(
                'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'p',
                'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '1', '2', '3', '4', '5',
                '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
                'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

            for ($rand = 0; $rand <= $length; $rand++) {
                $random = rand(0, count($chars) - 1);
                $randstr .= $chars[$random];
            }
            return $randstr;
        }

    public function getworkflows(Request $request){
         $this->validate( $request,[
               'workspaceid' => 'required',
               'client_id' => 'required'
               ]
           );
        $workflows = ClientWorkflow::where('client_id', $request->client_id)->where('workspace_id', $request->workspaceid)->get();
        $options = "";
        if(count($workflows) > 0){
            foreach($workflows as $workflow){
                $options.="<option value='".$workflow->workflow_id."'>".$workflow->workflow_name."</option>";
            }

        }

        return array('status' => 'success' , 'options' => $options);

    }

    function validate_id(Request $request){
        try {
            $inputs = $request->all();
            if(isset($inputs['client_id'])){
                // To get clients associated with tpv agent
                $workfolwIds = UserTwilioId::where('user_id',Auth::id())->pluck('workflow_id')->toArray();
                $clientIds = ClientWorkflow::whereIn('workflow_id',$workfolwIds)->pluck('client_id')->toArray();

                $client = Client::where('status', config()->get('constants.STATUS_ACTIVE'))->where('id', $inputs['client_id'])->first();
                if($client && in_array($inputs['client_id'], $clientIds)){
                    return \Response::json(array('status' => 'success'));
                }else{
                    return \Response::json(array('status' => 'error'));
                }

            }else{
                return \Response::json(array('status' => 'error'));
            }
        } catch (\Exception $e) {
            Log::error($e);
            return \Response::json(array('status' => 'error'));
        }

    }
    function getclientsbystatus(Request $request){
        $inputs = $request->all();
        if(isset($inputs['checkstatus'])){

           $clients = (new Client)->getClientsListByStatus($inputs['checkstatus']);
            if($clients){
                $res_options = "";
                foreach($clients as $single_client){
                    $res_options.="  <option value='".$single_client->id."' >".$single_client->name."</option>";
                }
                $response = array(
                    'status' => 'success',
                    'options' =>  $res_options,
                );
                return \Response::json($response);
              //  return \Response::json(array('status' => 'success'));
            }else{
                return \Response::json(array('status' => 'error'));
            }

        }else{
            return \Response::json(array('status' => 'error'));
        }

    }



 public function selfverification($verificationid = "", Request $request){
    
    try {
        $mode = $request->verification_mode;
        if(empty($verificationid) ||  empty($mode)) {
            return abort(404);
        } else {
            $leadid =  base64_decode($verificationid);
            $validUrl = TelesalesSelfVerifyExpTime::where('telesale_id',$leadid)
                ->where('verification_mode',$mode)
                ->where('expire_time','>=',now())
                ->whereHas('telesales',function($query) {
                    $query->whereIn('status',['pending','hangup']);
                })
                ->count();
           if($validUrl != 1) {
                return redirect()->route('login')->withErrors('Your self verification time expired.');
           }
           $telesales_lead = Telesales::where('id', '=', $leadid)->whereIn('status',['pending','hangup'])->firstOrFail();
           // if($telesales_lead->zipcodes->isEmpty()) {
           //      return redirect()->route('login')->withErrors('Self verification not allowed for this zip code.');
           // } else if(!empty($telesales_lead->zipcodes) && $telesales_lead->zipcodes->count() > 0) {
           //      $telesale_zipcode =  $telesales_lead->zipcodes->first();
           //      $allowed_zipcode =SelfVerificationAllowedZipcode::where('zipcode_id',$telesale_zipcode->id)->count();
           //      if($allowed_zipcode == 0) {
           //          return redirect()->route('login')->withErrors('Self verification not allowed for this zip code.');
           //      }
           // }

            // for check self TPV Tele is enable or not
            if ($telesales_lead->type == 'tele' && !isOnSettings($telesales_lead->client_id, 'is_enable_self_tpv_tele')) {
                $msg = "Tele self verify is switched off. Please contact your administrator for assistance.";
                return redirect()->route('login')->withErrors($msg);
            }

            // for check self TPV d2d is enable or not
            if ($telesales_lead->type == 'd2d' && !isOnSettings($telesales_lead->client_id, 'is_enable_self_tpv_d2d')) {
                $msg = "D2D self verify is switched off. Please contact your administrator for assistance.";
                return redirect()->route('login')->withErrors($msg);
            }

           $lead_Data = (new Telesalesdata)->leadDetail($leadid);
           return view('frontend.client.selfverify', compact('telesales_lead','lead_Data', 'mode'));
        }
    } catch(\Exception $e) {
        return redirect()->route('login')->withErrors('Something went wrong, please try again.');
    }

 }

 /**
  * This function is used to validate lead data
  */
 function validateLeadData(Request $request,$inner_controller = null){
    $GetValidationRules = GetValidationRules();
    $validationMappingArray = validationMappingArray();
    $ErrorMessages = array();

    if( isset($request->fields) && count($request->fields) > 0){
        $all_fields = $request->fields;
         if( isset($all_fields['Commodity']) && ($all_fields['Commodity'] == 'Electric' || $all_fields['Commodity'] == 'Gas') ){
          //  print_r($request->fields)  ;
            $i = 0;
            $MarketCode = "";
            $commodity = $all_fields['Commodity'];
            foreach ($all_fields['multiple'] as $fielddata) {
                 if( isset($all_fields['multiple'][$i]['MarketCode']) ){
                    $MarketCode = $all_fields['multiple'][$i]['MarketCode'];
                }
                foreach ($fielddata as $fieldname => $fieldValue) {

                   // print_r($GetValidationRules[$commodity][$MarketCode]);
                     if( isset($validationMappingArray[$fieldname])){
                         $field_to_check = $validationMappingArray[$fieldname];
                        if( isset($GetValidationRules[$commodity][$MarketCode][$field_to_check]) ){
                           $validtion = $GetValidationRules[$commodity][$MarketCode][$field_to_check];
                           $is_Required = 0;
                           if( $validtion['required'] == 1 && $fieldValue == ""){
                                 $ErrorMessages[] = array(
                                  	   'field' => $field_to_check,
                                  	   'commodity' => $commodity,
                                  	   'message' => $validtion['message']
                                  	);
                           }else if( $fieldValue != ""){
                                 //echo $validtion['regx'];
                              if (!preg_match("'".$validtion['regx']."'",$fieldValue)) {
                                  $ErrorMessages[] = array(
                                  	   'field' => $field_to_check,
                                  	   'commodity' => $commodity,
                                  	   'message' => $validtion['message']
                                  	);
                             }

                           }

                        }

                       // die('ll');
                        }

                        if($fieldname == 'utility' && $fieldValue == ""){
								$ErrorMessages[] = array(
                                  	   'field' => 'utility',
                                  	   'commodity' => $commodity,
                                  	   'message' => "Utility is required."
                                  	);
                        }
                         if($fieldname == 'Program' && $fieldValue == ""){
								$ErrorMessages[] = array(
                                  	   'field' => 'Program',
                                  	   'commodity' => $commodity,
                                  	   'message' => "Program is required."
                                  	);
                        }
                  }


                $i++;


            }
         }else{
         	$dual_fielda_mapping = DualFuelvalidationMappingArray();

            $i = 0;
            $GasMarketCode = "";
            $ElectricMarketCode = "";
            foreach ($all_fields['multiple'] as $fielddata) {
                 if( isset($all_fields['multiple'][$i]['gas_MarketCode']) ){
                     $GasMarketCode = $all_fields['multiple'][$i]['gas_MarketCode'];
                 }
                 if( isset($all_fields['multiple'][$i]['electric_MarketCode']) ){
                     $ElectricMarketCode = $all_fields['multiple'][$i]['electric_MarketCode'];
                 }

                foreach ($fielddata as $fieldname => $fieldValue) {

                   // print_r($GetValidationRules[$commodity][$MarketCode]);
                     if( isset($dual_fielda_mapping[$fieldname])){
                     	$commodity =  $dual_fielda_mapping[$fieldname]['commodity'];
                         $field_to_check = $dual_fielda_mapping[$fieldname]['field'];
                         if( $commodity == 'Electric' ){
                         	$MarketCode = $ElectricMarketCode;
                         }
                          if( $commodity == 'Gas' ){
                           $MarketCode = $GasMarketCode;
                         }

                        if( isset($GetValidationRules[$commodity][$MarketCode][$field_to_check]) ){
                           $validtion = $GetValidationRules[$commodity][$MarketCode][$field_to_check];
                           $is_Required = 0;
                           if( $validtion['required'] == 1 && $fieldValue == ""){
                                 $ErrorMessages[] = array(
                                  	   'field' => $field_to_check,
                                  	   'commodity' => $commodity,
                                  	   'message' => $validtion['message']
                                  	);
                           }else if( $fieldValue != ""){
                                 //echo $validtion['regx'];
                              if (!preg_match("'".$validtion['regx']."'",$fieldValue)) {
                                  $ErrorMessages[] = array(
                                  	   'field' => $field_to_check,
                                  	   'commodity' => $commodity,
                                  	   'message' => $validtion['message']
                                  	);
                             }

                           }

                        }

                       // die('ll');
                        }

                        if($fieldname == 'gasutility' && $fieldValue == ""){
								$ErrorMessages[] = array(
                                  	   'field' => 'gasutility',
                                  	   'commodity' => 'Gas',
                                  	   'message' => "Gas Utility is required"
                                  	);
                        }
                        if($fieldname == 'utility' && $fieldValue == ""){
								$ErrorMessages[] = array(
                                  	   'field' => 'utility',
                                  	   'commodity' => 'Electric',
                                  	   'message' => "Utility is required"
                                  	);
                        }
                        if($fieldname == 'electricutility' && $fieldValue == ""){
								$ErrorMessages[] = array(
                                  	   'field' => 'electricutility',
                                  	   'commodity' =>'Gas',
                                  	   'message' => "Electric Utility is required."
                                  	);
                        }
                         if($fieldname == 'Program' && $fieldValue == ""){
								$ErrorMessages[] = array(
                                  	   'field' => 'Program',
                                  	   'commodity' => 'electric',
                                  	   'message' => "Program is required"
                                  	);
                        }
                        if($fieldname == 'GasProgram' && $fieldValue == ""){
								$ErrorMessages[] = array(
                                  	   'field' => 'gasprogram',
                                  	   'commodity' => 'Gas',
                                  	   'message' => "Gas Program is required"
                                  	);
                        }
                        if($fieldname == 'ElectricProgram' && $fieldValue == ""){
								$ErrorMessages[] = array(
                                  	   'field' => 'electricprogram',
                                  	   'commodity' => 'Electric',
                                  	   'message' => "Electric Program is required"
                                  	);
                        }
                  }


                $i++;


            }
         }
    }
        if( !empty($inner_controller) ){
           return array( 'messages' => $ErrorMessages) ;
        }else{
        	return response()->json(array( 'messages' => $ErrorMessages) );
        }



 }

 public function field(Request $request)
    {
        $type = request()->get('type');
        $elementNum = request()->get('element_num');
        $field = [];
        switch ($type) {
            case 'full_name':
                $view = view('client.forms.formfields.full_name', compact('elementNum', 'field'));
                break;

            case 'address':
                $view = view('client.forms.formfields.address', compact('elementNum', 'field'));
                break;

            case 'service_and_billing_address':
                $view = view('client.forms.formfields.service_and_billing_address', compact('elementNum', 'field'));
                break;

            case 'textbox':
                $view = view('client.forms.formfields.text_box', compact('elementNum', 'field'));
                break;

            case 'textarea':
                $view = view('client.forms.formfields.text_area', compact('elementNum', 'field'));
                break;

            case 'radio':
                $view = view('client.forms.formfields.radio', compact('elementNum', 'field'));
                break;

            case 'checkbox':
                $view = view('client.forms.formfields.checkbox', compact('elementNum', 'field'));
                break;

            case 'selectbox':
                $view = view('client.forms.formfields.selectbox', compact('elementNum', 'field'));
                break;

            case 'separator':
                $view = view('client.forms.formfields.separator', compact('elementNum', 'field'));
                break;

            case 'heading':
                $view = view('client.forms.formfields.heading', compact('elementNum', 'field'));
                break;

            case 'label':
                $view = view('client.forms.formfields.label', compact('elementNum', 'field'));
                break;

            case 'phone_number':
                $view = view('client.forms.formfields.phone_number', compact('elementNum', 'field'));
                break;

            case 'email':
                $view = view('client.forms.formfields.email', compact('elementNum', 'field'));
                break;

            default:
                # code...
                break;
        };

        return response()->json(['status' => 'success', 'view' => $view->render()]);
    }

    /**
     * This function is used to check client id
     */
    public function checkClientId(Request $request) {
        $code = $request->code;
        $exists = Client::where('code', $code)->count();


        $finalCode = $exists > 0 ? $code. strtoupper(str_random(2)) : $code;

        return response()->json(['status' => true, 'code' => $finalCode]);
    }

    /**
     * This method is used to check client code
     */
    public function checkClientCode(Request $request) {
        $code = $request->code;
        $id = $request->id;
        if($id > 0) {
            $exists = Client::where('code', $code)->where('id','!=',$id)->count();
        } else {
            $exists = Client::where('code', $code)->count();
        }

        if($exists > 0 ) {
            return response()->json(['valid' => false]);
        } else {
            return response()->json(['valid' => true ]);
        }
    }

    /**
     * This method is used to generate email otp
     */
    public function generateOtpEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => implode(',', $validator->messages()->all())
            ], 200);
        } else {
            $to = $request->email;
            $six_digit_random_number = mt_rand(100000, 999999);
            $message = "Your TPV360 security code is: {$six_digit_random_number}";

            $message_response = (new ClientsController())->sendOtpEmail($to, $message);
            if ($message_response == true) {
                $emailVerification = EmailVerification::where('email', $to)
                    ->where('status', 'pending')
                    ->first();

                if (!$emailVerification) {
                    $emailVerification = new EmailVerification();
                    $emailVerification->email = $to;
                    $emailVerification->otp = $six_digit_random_number;
                    $emailVerification->status = 'pending';
                } else {
                    $emailVerification->otp = $six_digit_random_number;
                    $emailVerification->status = 'pending';
                }

                $emailVerification->save();

                    $textEmailStatistics = new TextEmailStatistics();
                    $textEmailStatistics->type = 1;
                    $textEmailStatistics->save();

                    $textEmailStatistics = new TextEmailStatistics();
                    $textEmailStatistics->type = 1;
                    $textEmailStatistics->save();

                return response()->json([
                    'status' => 'success',
                    'message' => "OTP successfully sent"
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => "Unable to send email. Please try again."
                ], 200);
            }


        }
    }

    /**
     * This function is used to verify email otp
     */
    public function verifyOtpEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'otp' => 'required',

        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => 'error',
                'message' => implode(',', $validator->messages()->all())
            ], 200);
        } else {

            $email = $request->email;
            $otp = $request->otp;

            $emailVerification = EmailVerification::where('email', $email)
                ->where('otp', $otp)
                ->where('status', 'pending')
                ->first();
            if ($emailVerification) {

                $emailVerification->status = 'verified';
                $emailVerification->verifiedby = Auth::user()->id;
                $emailVerification->save();

                return response()->json([
                    'status' => 'success',
                    'message' => "OTP verified"
                ], 200);

            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => "Invalid OTP"
                ], 200);
            }

        }
    }

    /**
     * This function is used to generate phone otp
     */
    public function generateOtpPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => implode(',', $validator->messages()->all())
            ], 200);
        } else {
            try{
                $to = $request->phone_number;
                $six_digit_random_number = mt_rand(100000, 999999);
                $message = "Your TPV360 security code is: {$six_digit_random_number}";

                $user = Auth::user();
                $phones = $this->ClientTwilioNumbers->getNumber(array_get($user, 'client_id'));
                $tpvNumber = '';
                if(!empty($phones) && $phones != null) {
                    $tpvNumber = $phones->phonenumber;
                }

                if ($verification = Phonenumberverification::where('phonenumber', $to)->where('status', 'pending')->first()) {
                    $response = $verification->update(
                        ['otp' => $six_digit_random_number]);
                } else {
                    $verification = Phonenumberverification::create(
                        ['phonenumber' => $to, 'otp' => $six_digit_random_number, 'status' => 'pending']);
                }

                if (!$request->has('otp_type') || $request->get('otp_type') == "" || ($request->has('otp_type') && in_array(strtolower($request->get('otp_type')), array_values(config()->get('constants.PHONE_NUM_VERIFICATION_OTP_TYPE'))))) {
                    if (!$request->has('otp_type') || $request->get('otp_type') == "" || strtolower($request->get('otp_type')) == config()->get('constants.PHONE_NUM_VERIFICATION_OTP_TYPE.SMS')) {
                        $statisticsType = 2;
                        $messageResponse = app('App\Http\Controllers\Conference\ConferenceController')->sendmessage($to, $message);
                    } else {
                        $statisticsType = 3;
                        $messageResponse = $this->twilioService->makeVoiceCall($tpvNumber, $to, $verification->id);
                    }
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid OTP type.'
                    ], 400);
                }

                if ($messageResponse == true) {
                    $textEmailStatistics = new TextEmailStatistics();
                    $textEmailStatistics->type = $statisticsType;
                    $textEmailStatistics->save();
                    return response()->json([
                        'status' => 'success',
                        'message' => "OTP successfully sent"
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Unable to send message."
                    ], 200);
                }
            }catch(\Exception $e)
            {
              return response()->json([
                        'status' => 'error',
                        'message' => $e->getMessage(),
                    ], 200);  
            }


        }
    }

    /**
     * This method is used to verify phone otp
     */
    public function verifyOtpPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required',
            'otp' => 'required',

        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => 'error',
                'message' => implode(',', $validator->messages()->all())
            ], 200);
        } else {

            $phonenumber = $request->phone_number;
            $otp = $request->otp;

            $getresult = DB::table('phonenumberverification')
                ->where('phonenumber', $phonenumber)
                ->where('otp', $otp)
                ->where('status', 'pending')
                ->first();
            if ($getresult) {

                $Phonenumberverification = (new Phonenumberverification)::find($getresult->id);

                $Phonenumberverification->status = 'verified';
                $Phonenumberverification->verifiedby = Auth::user()->id;

                $Phonenumberverification->save();

                return response()->json([
                    'status' => 'success',
                    'message' => "OTP verified"
                ], 200);

            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => "Invalid OTP"
                ], 200);
            }

        }
    }

    /*
        @Author : Ritesh Rana
        @Desc   : check Utilities data.
        @Input  :
        @Output : Illuminate\Http\Response
        @Date   : 02/03/2020
        @Last Changed Date: 06/01/2021
        @Changed By : Ramashish Nishad
        */
    public function getUtilities(Request $request)
    {
        $form_id = $request->form_id;
        $client_id = $request->client_id;
        $form = Clientsforms::with(['fields' => function($q) {
            $q->orderBy('position', 'asc');
        }])->findOrFail($form_id);
        $isHasUtility=1;
        if (isset($request->zipcode) && !empty($request->zipcode)) {
            $zipcode = $request->zipcode;

            $zipcodeData = Zipcodes::where('zipcode', $zipcode)->first();

            if (empty($zipcodeData)){
                return array(
                    'status' => 'error',
                    'message' => 'Invalid Zipcode'
                );
            }else{
                $zipcodeIds =  array($zipcodeData->id);
                $programIds = $this->getProgramIds();
                $commodities = $form->commodities;
                foreach ($commodities as $commodity){
                    $utilityCount = Utilities::getUtilityByCommodity($commodity->id,$zipcodeIds, $programIds)->count();
                    if($utilityCount === 0) {
                        $isHasUtility = 0;
                    }
                }
            }
        }

        if ($isHasUtility > 0) {
            return array(
                'status' => 'success',
                'message' => 'Utilities Found'
            );
        }else {
            return array(
                'status' => 'error',
                'message' => 'Utilities not found'
            );
        }
    }

    public function proceedToSegment($lead) {
        
        $lead = Telesales::with('teleSalesData')->find($lead->id);
        $this->segmentService->createIdentity($lead);
        $trackCreated = $this->segmentService->createTrack($lead);
        if ($trackCreated) {
            \Log::info("Segment track of lead creation created for lead: " . array_get($lead, 'id'));
        } else {
            \Log::error("Unable to create track of lead creation for lead: " . array_get($lead, 'id'));
        }
    }

    /************ store critical log for event type 16 from webhook **********/
    public function storeCriticalLog(Request $request)
    {
       try{
            Log::info('web hook requests: '.print_r($request->all(),true));
            
            $object_type = config('constants.OBJECT_TYPE_OF_EVENT_TYPE_16');
            $campaign_id = config('constants.CAMPAIGN_ID_OF_EVENT_TYPE_16');

            if(isset($request['data']['customer_id']) && 
                isset($request['data']['campaign_id']) && 
                $campaign_id == $request['data']['campaign_id'] && 
                $object_type == $request['object_type']) {

                $customer_id = $request['data']['customer_id'];

                $customer_id_array = explode('-', $customer_id);
                Log::info('SEGMENT_IDENTIFIER_PREFIX-> '.config("segment.segment_identifier_prefix"));
                if(count($customer_id_array) == 2 && $customer_id_array[0] == config("segment.segment_identifier_prefix")) {
                    $lead_id = $refrence_id = $customer_id_array[1];
                    $telesale =  Telesales::where('status','pending')->where('refrence_id',$refrence_id)->with('user.salesAgentDetails')->first();


                    if (!empty($telesale)) {
                        
                        $lead_id = $telesale->id;                    

                        $encoded_leadid = base64_encode($lead_id);
                        $url= route('sendverificationlink',[$encoded_leadid,'email']);
                        $link = "email.";
                        $message = __('critical_logs.messages.Event_Type_16',['link'=>$link]);
                        $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_16');
                        $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Non-critical');
                        $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                        $user_type = config('constants.USER_TYPE_CRITICAL_LOGS.2');
                        $criticalLogs = (new CriticalLogsHistory)->createCriticalLogs(null,$message,$lead_id,null,null,$lead_status,$event_type,$error_type,$user_type);
                    }
                }
            }
        }catch(\Exception $e) {
            Log::error($e);
        }
    }

    /**
     * This method is used to validate enrollment backend form
     */
    public function enrollmentFormBackendValidations(Request $request)
    {
        $leadData = $request->all();
        $leadFormData = array();
        $valid_first_name = array();
        $valid_middle_initial = array();
        $valid_last_name = array();
        $isMDState = '';

        /* Lable needs to Match */
        $name_array = array_map('strtolower',["Customer Name","Billing Name"]);
        parse_str($request->data, $leadFormData);

        if(!empty($request->zipcode)){
            $isMDState = Zipcodes::where('zipcode', $request->zipcode)->where('state', 'MD')->first();
            $isMDState = $isMDState ? $isMDState->state : '';
        }else{
            $isMDState = $request->state;
        }

        /* Check Client Id is RRH and State is MD */
        if($request->client_id == config('constants.CLIENT_RRH_CLIENT_ID') && $isMDState == 'MD'){
            /* Fetch active fields from Form */
            $check_field_ids = FormField::where('form_id',$request->form_id)
                        ->where('type',"fullname")
                        ->whereNull('deleted_at')
                        ->whereIn(DB::raw('LOWER(label)'),$name_array)
                        ->pluck('id')
                        ->toArray();
            if(count($check_field_ids) > 1 && isset($leadFormData['fields']))
            {

                foreach ($leadFormData['fields'] as $key => $val) {
                    
                    if(isset($val['field_id']) && in_array($val['field_id'], $check_field_ids)) {
                        if (isset($val['value']['first_name'])) {
                            $valid_first_name[] = $val['value']['first_name'];
                        }
                        if(isset($val['value']['middle_initial'])){
                            $valid_middle_initial[] = $val['value']['middle_initial'];
                        }
                        if(isset($val['value']['last_name'])){
                            $valid_last_name[] = $val['value']['last_name'];
                        
                        }
                    }else if(isset($val[0]['field_id']) ) {
                        foreach ($val as $key => $form_val) {
                            if(in_array($form_val['field_id'], $check_field_ids)){
                                if (isset($form_val['value']['first_name'])) {
                                    $valid_first_name[] = $form_val['value']['first_name'];
                                }
                                if(isset($form_val['value']['middle_initial'])){
                                    $valid_middle_initial[] = $form_val['value']['middle_initial'];
                                }
                                if(isset($form_val['value']['last_name'])){
                                    $valid_last_name[] = $form_val['value']['last_name'];
                                
                                }
                            }
                        }
                    }
                }
            }
        
            /* Compare Billing and Customer Name */
            if ((count($valid_first_name) > 1 && count(array_unique($valid_first_name)) > 1) || (count($valid_middle_initial) > 1 && count(array_unique($valid_middle_initial)) > 1) || (count($valid_last_name) > 1 && count(array_unique($valid_last_name)) > 1)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Billing Name must match Customer Name'
                ], 200);
            }else{
                return response()->json([
                    'status' => 'success',
                    'message' => ''
                ], 200);
            }
        }else{
            return response()->json([
                'status' => 'success',
                'message' => ''
            ], 200);
        }
    }

    /**
     * Added function to add fields to the enrollment data
     * 
     * designFrom
     */
    public function addFieldToEnrollmentForm(Request $request){
        $client_id = $request->client_id;
        $form_id = $request->form_id;

        $forms = Clientsforms::find($form_id);

        if(!isset($forms) && empty($forms))
        {
            return redirect()->route('client.contact',$client_id)->with('error',' You cannot create lead of this form,as form is deleted.');
        }
        $client = Client::findOrFail($client_id);
        $form = Clientsforms::with(['fields' => function($q) {
                    $q->where('is_multienrollment', '1');
                    $q->orderBy('position', 'asc');
                }])->findOrFail($form_id);

        $zipcode = '';
        $fields = '';
        $commodities = '';
        $alerts = null;
        $states = [];
        $current_enrollment_number = '';
        if(isset($request->multienrollmentIncrement) && !empty($request->multienrollmentIncrement)){
            $multienrollmentIncrement = $request->multienrollmentIncrement;
        }else{
            $multienrollmentIncrement = 1;
        }
        if(isset($request->current_enrollment_number) && !empty($request->current_enrollment_number)){
            $current_enrollment_number = $request->current_enrollment_number;
        }else{
            $current_enrollment_number = 2;
        }
        $programIds = $this->getProgramIds();
        if (isEnableEnrollByState($client->id)) {
            $states = $this->getUtilityStates($form->commodities->pluck('id'));
        }
        if (isset($request->zipcode) && !empty($request->zipcode)) {
            $zipcode = $request->zipcode;

            // $zipcodeData = Zipcodes::where('zipcode', $zipcode)->first();
            $salesagentdetails = Salesagentdetail::where('user_id',\Auth::user()->id)->first();
                $zipcodeData = Zipcodes::where('zipcode',$zipcode);
                if(strlen($salesagentdetails->restrict_state) > 0)
                {
                    $explodeData = explode(",",$salesagentdetails->restrict_state);
                    $zipcodeData = $zipcodeData->whereIn('state',$explodeData);
                }
                $zipcodeData = $zipcodeData->first();
            if (empty($zipcodeData)){
                return back()->withErrors(['zipcode' => 'Invalid Zip code.'])->withInput($request->all());
            }
            $zipcodeIds = array($zipcodeData->id);
            $commodities = $form->commodities;

            foreach ($commodities as $commodity){
                $commodity->utilities = Utilities::getUtilityByCommodityAndMapping($commodity->id,$zipcodeIds, $programIds);
            }
            $fields = array_get($form, 'fields');
        }

        if (isset($request->state) && !empty($request->state)) {
            $state = $request->state;
            $zipcodeIds = Zipcodes::where('state',$state)->pluck('id');
            $commodities = $form->commodities;

            foreach ($commodities as $commodity){
                $commodity->utilities = Utilities::getUtilityByCommodityAndMapping($commodity->id,$zipcodeIds, $programIds);
            }
            $fields = array_get($form, 'fields');
        }
        
        return view('frontend.client.multi_enrollment_field', ['client' => $client,'form' => $form, 'zipcode' => $zipcode, 'commodities' => $commodities, 'fields' => $fields, 'states' => $states, 'multienrollmentIncrement' => $multienrollmentIncrement,'current_enrollment_number' => $current_enrollment_number])->withInput($request->all());
    }

}