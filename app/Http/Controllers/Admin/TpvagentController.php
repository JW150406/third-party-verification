<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Calls\CallsController;
use App\Http\Controllers\AgentPanel\TPVAgent\TPVIVRController;
use App\models\ClientWorkflow;
use App\models\FormField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\User;
use App\models\Role;
use Illuminate\Support\Facades\DB;
use App\models\Telesales;
use App\models\Telesalesdata;
use App\models\Dispositions;
use App\models\Ticket;
use Hash;
use App\models\UserTwilioId;
use App\models\Client;
use App\models\ClientWorkspace;
use App\models\ScriptQuestions;
use App\models\Clientsforms;
use App\models\UserAssignedForms;
use App\models\FormScripts;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Admin\TelesalesVerificationController;
use App\models\Programs;
use App\models\Utilities;
use App\models\Brandcontacts;
use App\models\TpvAgentLanguage;
use App\models\TelesaleScheduleCall;
use Illuminate\Support\Facades\Log;
use Storage;
use DataTables;
use Mail;
use Twilio\Rest\Client as TwilioClient;
use Twilio\Jwt\TaskRouter\WorkspaceCapability;
use Twilio\Jwt\TaskRouter\WorkerCapability;
use Twilio\Jwt\ClientToken;
use Twilio\Exceptions\TwilioException;
use App\Services\SegmentService;
use App\models\TextEmailStatistics;
use App\Services\StorageService;
use App\models\CriticalLogsHistory;
use App\Traits\SelfverifyDetailTrait;
use App\Jobs\GenerateReceiptPdf;
use App\Traits\ScheduleCallTrait;
use App\Mail\NotifySalesAgentForVerifiedLead;
use App\Mail\LeadVerifyFailedEmail;
use App\Traits\TwilioTrait;
use App\Services\TwilioService;
use App\Traits\LeadTrait;
use App\Jobs\CreateLeadWebhookJob;
use App\Http\Controllers\Client\FormsController;
use App\models\Salesagentdetail;
use App\models\WorkerReservationDetails;
use App\models\TwilioLeadCallDetails;
use App\models\Zipcodes;
use App\Jobs\CheckTPVAlerts;


class TpvagentController extends Controller
{
    use SelfverifyDetailTrait, ScheduleCallTrait, TwilioTrait;

    public $telesalesdataobj = array();
    private $twilio_client   = array();
    private $workflowSid = "WWd18dc5feed687a8cd139e60c466e167a";
    public  $workspaceSid = 'WS773b86b9fe21ec7b213eb54af1019f6e';
    private $sid = "";
    private $token = "";
    private $client_token_number = "";
    private $twilioService;

    public function __construct()
    {
        $this->telesalesdataobj = (new Telesalesdata);
        /* if (!isset($request->vtype)) {
             $this->middleware('auth');
         }*/

        $this->sid    = config('services.twilio')['accountSid'];
        $this->token  = config('services.twilio')['authToken'];
        $this->twilio_client  = new TwilioClient($this->sid, $this->token);
        $this->segmentService = new SegmentService;
        $this->storageService = new StorageService;
        $this->twilioService = new TwilioService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $subQuery = "( select GROUP_CONCAT(name  SEPARATOR ', ') from clients where id IN (select client_id from client_twilio_workflowids where workflow_id IN (select workflow_id from user_twilio_id where user_twilio_id.user_id = users.id)) )";
            $tpv_users = User::select(['users.*'])
                ->addSelect(DB::raw($subQuery ." as clients_name"))
                //->leftJoin('user_twilio_id','users.id','=', 'user_twilio_id.user_id')
                ->whereNotIn('users.id', [Auth::user()->id])->where('users.access_level','tpvagent');
            //$tpv_users = User::whereNotIn('id', [Auth::user()->id])->where('access_level','tpvagent')->get();

            // To get clients name associated with tpv agent
            // foreach ($tpv_users as $user){
            //     $workfolwIds = UserTwilioId::where('user_id',$user->id)->pluck('workflow_id')->toArray();
            //     $clientIds = ClientWorkflow::whereIn('workflow_id',$workfolwIds)->pluck('client_id')->toArray();
            //     $clientNames = (!empty($clientIds)) ? Client::whereIn('id',$clientIds)->pluck('name')->toArray() : Null;
            //     $user->clients_name = $clientNames;
            // }

            // To filter by status
            if($request->status == "all"){
                // return both users (active/inactive)
            }elseif($request->status == "active"){
                $tpv_users->where('users.status','=',"active");
            }elseif($request->status == "inactive"){
                $tpv_users->where('users.status','=',"inactive");
            }

            return DataTables::of($tpv_users)
                ->editColumn('profile_picture', function($user){
                    $icon = getProfileIcon($user);
                    return $icon;
                })
                ->filterColumn('clients_name', function($query, $keyword) use ($subQuery){
                    $query->whereRaw($subQuery .' LIKE "%'.$keyword.'%"');
                    \Log::info($query->toSql());
                })
                ->addColumn('action', function ($user) {
                    $viewBtn = $editBtn = $statusBtn = $deleteBtn = '';
                    if (\auth()->user()->can('view-tpv-agents')) {
                        $viewBtn = '<a
                        class="tpv-agent-modal btn"
                        href="javascript:void(0)"
                        data-toggle="tooltip"
                        data-placement="top"
                        data-container="body"
                        data-type="view"
                        data-original-title="View TPV Agent"
                        data-id="' . $user->id . '"
                        >' . getimage("images/view.png") . '</a>';
                    }else{
                        $viewBtn = '<a
                        class="btn cursor-none"
                        data-type="view"
                        title="View TPV Agent"
                        >' . getimage("images/view-no.png") . '</a>';
                    }

                    if (\auth()->user()->can('edit-tpv-agents') && $user->is_block != 1 && $user->status == "active") {
                        $editBtn = '<a
                        class="tpv-agent-modal btn"
                        href="javascript:void(0)"
                        data-toggle="tooltip"
                        data-placement="top"
                        data-container="body"
                        data-type="edit"
                        data-original-title="Edit TPV Agent"
                        data-id="' . $user->id . '"
                        >' . getimage("images/edit.png") . '</a>';
                    }else{
                        $editBtn = '<a
                        class="btn cursor-none"
                        data-type="edit"
                        title="Edit TPV Agent"
                        >' . getimage("images/edit-no.png") . '</a>';
                    }

                    if (\auth()->user()->can('deactivate-tpv-agent')) {
                        if ($user->status == 'active') {
                            $statusBtn = '<a
                            class="deactivate-client-user btn"
                            href="javascript:void(0)"
                            data-toggle="tooltip"
                            data-placement="top"
                            data-container="body"
                            data-original-title="Deactivate TPV Agent"
                            data-id="' . $user->id . '"
                            data-name="' . $user->full_name . '">'
                                . getimage("images/activate_new.png") . '</a>';
                        } else {
                            $statusBtn = '<a
                            class="activate-client-user btn"
                            href="javascript:void(0)"
                            data-toggle="tooltip"
                            data-placement="top"
                            data-container="body"
                            data-original-title="Activate TPV Agent"
                            data-id="' . $user->id . '"
                            data-is-block="' . $user->is_block . '"
                            data-name="' . $user->full_name . '">'
                                . getimage("images/deactivate_new.png") . '</a>';
                        }
                    }else{
                        $statusBtn = '<a
                            class="btn cursor-none"
                            title="Activate TPV Agent">'
                            . getimage("images/deactivate_new-no.png") . '</a>';
                    }

                    if(Auth::user()->can('delete-tpv-agent'))
                    {
                        $class = 'delete_tpv_agent';
                        $attributes = [
                            "data-original-title" => "Delete TPV Agent",
                            "data-id" => $user->id,
                            "data-name"=> $user->full_name,
                            "data-status" =>"delete",
                        ];
                        $deleteBtn = getDeleteBtn($class, $attributes);
                    }

                    return '<div class="btn-group">' . $viewBtn . $editBtn . $statusBtn . $deleteBtn .'<div>';
                })
                ->rawColumns(['profile_picture','action'])
                ->make(true);
        }
        $client_workspaces = (new ClientWorkspace)->getallWorkspaceIds();
        $params = array(['access_level', '=', 'tpvagent'], ['status', '=', 'active']);
        $tpv_users = User::whereNotIn('id', [Auth::user()->id])->where($params)->orderBy('id', 'DESC')->get();
        return view('tpvagents_new.index', compact('tpv_users', 'client_workspaces'))
            ->with('i', ($request->input('page', 1) - 1) * 20);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $client_workspaces = (new ClientWorkspace)->getallWorkspaceIds();
        return view('tpvagents.create', compact('client_workspaces')); //return the view with the list of roles passed as an array
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $id = $request->id;
        /* Start Validation rule */
        $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id.',id,deleted_at,NULL',
            'twilio_workflows'    => 'required|array|min:1',
            'languages'    => 'required|array|min:1',
            'comment'=>'required_if:status,==,inactive',
        ],[
            'twilio_workflows.required'=>'This field is required',
            'languages.required'=>'This field is required',
            'comment.required_if' => 'This field is required',
        ]);
        /* End Validation rule */

        try {
            $workerid = $request->worker_id;
            $languages = $request->input('languages', []);
            if (empty($id)) {
                // if (count($request->twilio_ids['workspace_id']) > 0) {
                //     foreach ($request->twilio_ids['workspace_id'] as $key => $added_data) {
                //         if (isset($request->twilio_ids['worker_id'][$key]) && $request->twilio_ids['workspace_id'][$key]) {
                //             $twilo_id = $request->twilio_ids['worker_id'][$key];
                //             $workspace_id = $request->twilio_ids['workspace_id'][$key];
                //             if (!empty($workspace_id) && !empty($twilo_id)) {
                //                 if (UserTwilioId::where('twilio_id', '=', $twilo_id)
                //                         ->where('workspace_id', '=', $workspace_id)->count() > 0) {
                //                     return response()->json(['status' => 'error', 'errors' => ['Combination of Twilio Workspace ID and Worker ID already exists.']]);
                //                     // return redirect()->back()
                //                     // ->withErrors('Combination of Twilio Workspace ID and Worker ID already exists.');
                //                 }
                //             }
                //         }
                //     }
                // }


                $input = $request->only('first_name', 'last_name', 'email');
                $next_user_id = (new User)->nextAutoID();
                $input['access_level'] = 'tpvagent';

                $input['parent_id'] = Auth::user()->id;

                $input['verification_code'] = str_random(20);
                $input['password'] = Hash::make(rand()); //Hash password
                $uid = "";
                $numberofcharchters = strlen($next_user_id);
                if ($numberofcharchters >= 4) {
                    $uid = $next_user_id;
                } else {
                    $restofZeros = 4 - $numberofcharchters;
                    $zeros = "";
                    for ($i = 0; $i < $restofZeros; $i++) {
                        $zeros .= "0";
                    }
                    $uid = $zeros . $next_user_id;
                }
                $input['userid'] = strtolower($request->first_name[0]) . $uid;
                $user = User::create($input);
                $userid = $user->id;
                $workerid = $request->worker_id;

                if (!empty($request->languages)) {
                    (new TpvAgentLanguage)->store($userid, $request->languages);
                }

                if (!empty($request->twilio_workflows)) {

                    //Retrieve workspace details
                    $workspace = $this->getWorkspaceDetails();

                    if (empty($workspace) || empty($workspace->workspace_id)) {
                        \Log::error("No workspace details found!!");
                        return false;
                    }

                    //Prepare array for workers data
                    $toWorker = [];
                    $toWorker['friendlyName'] = $request->first_name . ' '. $request->last_name .' ('. $user->userid.')';

                    //Create worker on twilio
                    $worker = $this->twilioService->createWorker($workspace->workspace_id, $toWorker);

                    //Update worker's attributes to twilio (passing attributes with create api is not working)
                    $toUpdateWorker = [];
                    $toUpdateWorker['attributes'] = json_encode(array('selected_workflow' => $request->twilio_workflows, 'languages' => $languages,'last_call_time' => now()->timestamp));
                    $res = $this->twilioService->updateWorker($workspace->workspace_id, $worker->sid, $toUpdateWorker);

                    //Check response of worker resource is created or not on twilio
                    if (!empty($worker)) {
                        foreach ($request->twilio_workflows as $workflowId) {
                            UserTwilioId::create([
                                'user_id' => $userid,
                                'twilio_id' => $worker->sid,
                                'workflow_id' => $workflowId
                            ]);
                        }
                    } else {
                        \Log::error("Unable to create worker on twilio for user with id: " . $user->id);
                    }

                }
                // for send verification email
                $this->sendVerificationEmail($user); 

                Log::info("Successfully created new TPV agent.");
                return response()->json(['status' => 'success', 'message' => 'TPV Agent successfully created.']);
            } else {
                $input = $request->only('email', 'first_name', 'last_name','status');
                
                $input['deactivationreason'] = $request->comment;
                if($request->input('is_block')) {
                    $input['is_block'] = $request->input('is_block');
                }
                if (!empty($request->password)) {
                    $input['password'] = Hash::make($request->password);
                }
                $user = User::find($id);
                $userid = $user->id;
                $user->update($input); //update the user info

                if (!empty($request->languages)) {
                    (new TpvAgentLanguage)->store($userid, $request->languages);
                }

                UserTwilioId::where('user_id',$userid)->forceDelete();
                if (!empty($request->twilio_workflows)) {
                    foreach ($request->twilio_workflows as $key => $workflow_id) {
                        $WorkspaceID = (new ClientWorkflow)->getClientAndWorkspaceIDUsingWorkflowID($workflow_id);
                        $workspace_id = $WorkspaceID->workspace_id;
                        if (!empty($workflow_id) && !empty($workerid)) {
                            $data =[
                                'user_id' => $userid,
                                'twilio_id' => $workerid,
                                'workflow_id' => $workflow_id
                            ];
                            UserTwilioId::create($data);
                        }
                    }

                    \Log::info(json_encode(array('selected_workflow' => $request->twilio_workflows)));

                    $worker = $this->twilioService->getWorker($workspace_id, $workerid);

                    $attributes = json_decode($worker->attributes, true);
                    \Log::info("Previous worker attributes: ".print_r($attributes,true));
                    $attributes['selected_workflow'] = $request->twilio_workflows;
                    $attributes['languages'] = $languages;
                    $toUpdateWorker = json_encode($attributes);
                    \Log::info("New worker attributes: ".print_r($attributes,true));

                    $updatedResponse = $this->twilio_client->taskrouter->v1->workspaces($workspace_id)->workers($workerid)->update(['attributes' => $toUpdateWorker]);
                    //$updatedResponse = $this->twilio_client->taskrouter->v1->workspaces($workspace_id)->workers($workerid)->update(['attributes' => json_encode(array('selected_workflow' => $request->twilio_workflows, 'languages' => $languages))]);
                    \Log::info("Updated worker: ");
                }


                Log::info("Successfully updated details of TPV agent, id : ".$id);
                return response()->json(['status' => 'success', 'message' => 'TPV Agent successfully updated.']);
            }

        } catch (\Exception $e) {
            \Log::error($e);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }

        //  return redirect()->route('tpvagents.index')->with('success','User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $user = User::find($id);
        $twilio_id = "";
        $save_twilio_id = UserTwilioId::where('user_id', $id)->first();

        if (!empty($save_twilio_id)) {
            $twilio_id = $save_twilio_id->twilio_id;
        }
        return view('tpvagents.show', compact('user', 'twilio_id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::get(); //get all roles
        $userRoles = $user->roles->pluck('id')->toArray();
        $twilio_id = "";
        $twilio_ids = (new UserTwilioId)->getTwilioIds($id);
        $client_workspaces = (new ClientWorkspace)->getallWorkspaceIds();

        if (!empty($save_twilio_id)) {
            $twilio_id = $save_twilio_id->twilio_id;
        }

        return view('tpvagents.edit', compact('user', 'roles', 'userRoles', 'twilio_ids', 'client_workspaces'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function getTpvAgent(Request $request)
    {
        $user = User::with('client','languages')->find($request->user_id);
        $roles = Role::get(); //get all roles
        $userRoles = $user->roles->pluck('id')->toArray();
        $twilio_ids =UserTwilioId::where('user_id',$request->user_id)->get();

        // To get clients name associated with tpv agent
        $workfolwIds = UserTwilioId::where('user_id',$user->id)->pluck('workflow_id')->toArray();
        $clientIds = ClientWorkflow::whereIn('workflow_id',$workfolwIds)->pluck('client_id')->toArray();
        $clientNames = (!empty($clientIds)) ? Client::whereIn('id',$clientIds)->pluck('name')->implode(', ') : Null;

        return response()->json(['status' => 'success', 'data' => $user, 'userrole' => $userRoles, 'roles' => $roles, 'twilio_ids' => $twilio_ids, 'clients_name'=>  $clientNames]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return Response
     */
    public function update($id, Request $request)
    {
        /* Start Validation rule */
        $this->validate($request, [
            'first_name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'confirmed',
        ]);
        /* End Validation rule */
        $input = $request->only('email', 'password', 'first_name', 'last_name');
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']); //update the password
        } else {
            $input = array_except($input, array('password')); //remove password from the input array
        }
        $user = User::find($id);
        $user->update($input); //update the user info


        if ($request->twilio_id) {
            $save_twilio_id = UserTwilioId::firstOrNew(array('user_id' => $id));
            $save_twilio_id->twilio_id = $request->twilio_id;
            $save_twilio_id->save();
        }

        return redirect()->route('tpvagents.index')
            ->with('success', 'User successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request)
    {
        (new User)->updateUserStatus($request->userid, 'inactive');

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'TPV Agent successfully deleted.']);
        }
        return redirect()->route('tpvagents.index')->with('success', 'Agent successfully deleted.');
    }

    /* Check Sale */

    public function sales(Request $request)
    {
        if(auth()->user()->access_level == 'tpvagent'){
            $sale_detail = array();
            $client_id = "";
            $reference_id = "";
            $sale_info = array();
            $dispositions = array();
            $reviewedby = "";
            $twilio_id = "";
            $save_twilio_id = UserTwilioId::where('user_id', Auth::user()->id)->first();

            if (!empty($save_twilio_id)) {
                $twilio_id = $save_twilio_id->twilio_id;
            }
            if (isset($request->ref)) {
                $reference_id = $request->ref;
                $sale_info = (new Telesales)->getLeadInfo($reference_id);

                if (isset($sale_info->reviewed_by) && $sale_info->reviewed_by > 0) {
                    $user = (new User)->getUser($sale_info->reviewed_by);
                    $reviewedby = $user->first_name;
                }


                if (!empty($sale_info)) {
                    $sale_id = $sale_info->id;
                    $sale_detail = (new Telesalesdata)->leadDetail($sale_id);
                }
            }
            $twilio_ids = (new UserTwilioId)->getTwilioIds(Auth::user()->id);
            $assignedclients = (new UserTwilioId)->getAssignedClients(Auth::user()->id);
            \Log::info("Assigned Clients: " . print_r($assignedclients, true));

            $dispositions = (new Dispositions)->getDispositionList('decline');
            $hangup_dispositions = (new Dispositions)->getDispositionList('customerhangup');

            return view('tpvagents.sale', compact('client_id', 'sale_detail', 'reference_id', 'sale_info', 'reviewedby', 'dispositions', 'twilio_id', 'twilio_ids', 'assignedclients', 'hangup_dispositions'));
        } else {
            return redirect()->back();
        }
    }

    /**
     * This function is used for find sales details
     */
    public function findsales(Request $request)
    {
        $sale_detail = array();
        $client_id = "";
        $reference_id = "";
        $sale_info = array();
        $dispositions = array();
        $disposition_id = "";
        $reviewedby = "";
        $from_call = "";
        if (isset($request->from_call)) {
            $from_call = $request->from_call;
        }
        if (isset($request->ref)) {
            $reference_id = $request->ref;
            $sale_info = (new Telesales)->getLeadInfo($reference_id);

            if (isset($sale_info->reviewed_by) && $sale_info->reviewed_by > 0) {
                $user = (new User)->getUser($sale_info->reviewed_by);
                $reviewedby = $user->first_name;
            }


            if (!empty($sale_info)) {
                $sale_id = $sale_info->id;
                $sale_detail = (new Telesalesdata)->leadDetail($sale_id);
                $dispositions = (new Dispositions)->getDispositionList();


            }
        }
        return view('tpvagents.searchajaxresult', compact('client_id', 'sale_detail', 'reference_id', 'sale_info', 'reviewedby', 'dispositions', 'disposition_id', 'from_call'));
    }


    /**
     * This function is used for sale details update
     */
    public function saleupdate(Request $request)
    {
        $reference_id = $request->ref;
        $status_code = $request->v;
        $url = "";
        $leadzipcodestate = "";
        $leadcommodity = "";
        $clientId = null;
        $is_multiple = $request->is_multiple;
        $multiple_parent_id = $request->multiple_parent_id;

        $language = "English";
        if ($request->current_lang == 'es') {
            $language = "Spanish";
        }
        if (isset($request->leadzipcodestate)) {
            $leadzipcodestate = $request->leadzipcodestate;
        }
        if (isset($request->leadcommodity)) {
            $leadcommodity = $request->leadcommodity;
        }

        if (isset($request->userid)) {

            $url = '<a href="' . route('tpvagent.agentsales', ['uid' => $request->userid]) . '"  class="getagentsales"><i class="fa fa-arrow-left"></i> Back</a>';
        }
        $reviewedby_id = null;
        $first_name = "";
        $timeZone = 'America/New_York';
        if (Auth::check()) {
            $first_name = Auth::user()->first_name;
            $reviewedby_id = Auth::user()->id;          
        }
        $data = array('reviewed_by' => $reviewedby_id, 'updated_at' => date('Y-m-d H:i:s'), 'reviewed_at' => date('Y-m-d H:i:s'));

        $leadData = (new Telesales)->getLeadInfo($reference_id);

        //TODO: Retrieve welcome call for self tpv is enabled from settings after implemention of this switch under clients page
        $isSelfVerifiedCallBackEnable = isOnSettings($leadData->client_id, 'is_enable_self_tpv_welcome_call');

        $disposition_Text = "";
        $mail_message_text = "";
        if ($status_code == '1') {
            if ($isSelfVerifiedCallBackEnable) {
                $status = config()->get('constants.LEAD_STATUS_SELF_VERIFIED');
            } else {
                $status = 'verified';
            }
            $mail_message_text = "Your lead with reference ID <b>" . $reference_id . "</b> is successfully verified.";
        } else
            if ($status_code == '2') {
                $status = 'decline';

                if ($request->disposition_id == 'other') {
                    $disposition_id = (new Dispositions)->saveDisposition(
                        array(
                            'description' => $request->decline_reason,
                            'created_by' => $reviewedby_id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        )
                    );

                    $data['disposition_id'] = $disposition_id;
                } else {
                    $data['disposition_id'] = $request->disposition_id;
                }
                
                $disposition = Dispositions::where('type','decline')->where('client_id',$leadData->client_id)->where('disposition_group','lead_detail')->first();

                if (!empty($disposition)) {
                    //$disposition = Dispositions::findOrFail($data['disposition_id']);
                    $mail_message_text = "Your lead with reference ID <b>" . $reference_id . "</b> is declined. Reason for decline is <b>" . $disposition->description . "</b>";
                } else {
                    $mail_message_text = "Your lead with reference ID <b>" . $reference_id . "</b> is declined.";
                }


            } else if ($status_code == '3') {
                $status = 'hangup';
                $data['disposition_id'] = $request->disposition_id;
            } else {
                $status = 'pending';
            }

        $data['status'] = $status;
        $data['language'] = $request->current_lang;
        if($request->mode == 'phone'){
            $data['verification_method'] = config('constants.VERIFICATION_METHOD.TEXT');
            $mode = 'SMS';
        }else if($request->mode == 'email'){
            $data['verification_method'] = config('constants.VERIFICATION_METHOD.EMAIL');
            $mode = 'Email';
        }else{
            $data['verification_method'] = '';
            $mode = '';
        }
        $message = "";
        $user_type = config('constants.USER_TYPE_CRITICAL_LOGS.1');
        $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Non-critical');
        $leadOldStatus = Telesales::where('refrence_id', $reference_id)->first();
        $leadId =null ;
        if(!empty($leadOldStatus)) {
            $leadId = $leadOldStatus->id;
        }
        if($status == 'verified') {
            $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_13');
            $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Verified');
            $message = __('critical_logs.messages.Event_Type_13',['mode'=>$mode]);
        } else if ($status == config()->get('constants.LEAD_STATUS_SELF_VERIFIED')) {
            $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_43');
            $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.self-verified');
            $message = __('critical_logs.messages.Event_Type_43',['mode'=>$mode]);
            $data['reviewed_by'] = null;

            //TODO: Assign following values when get new events logs from client for self verify leads
            // $event_type = "";
            // $message = "";

        } else {
            $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_14');
            $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Declined');
            $message = __('critical_logs.messages.Event_Type_14',['mode'=>$mode]);
        }
        (new CriticalLogsHistory)->createCriticalLogs(null,$message,$leadId,null,null,$lead_status,$event_type,$error_type,$user_type);
        if(!empty($leadOldStatus) && !$leadOldStatus->selfVerifyModes->isEmpty()) {
            $user_type = config('constants.USER_TYPE_CRITICAL_LOGS.2');

            $link = '';
            $encoded_leadid = base64_encode($leadId);
            $lastKey = $leadOldStatus->selfVerifyModes->keys()->last();
            foreach ($leadOldStatus->selfVerifyModes as $key => $selfVerifyMode) {
                $url= route('sendverificationlink',[$encoded_leadid,$selfVerifyMode->verification_mode]);
                // $link .= ucfirst($selfVerifyMode->verification_mode) . " Link: ".$url."\n";
            }
            $reason = __('critical_logs.messages.Event_Type_15');
            $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Non-critical');
            $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_15');
            (new CriticalLogsHistory)->createCriticalLogs(null,$reason,$leadId,null,null,$lead_status,$event_type,$error_type,$user_type);
        }

        //$event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_11');
        // $id = null;
        // if(Auth::user())
        //     $id = Auth::user()->id;
        //(new CriticalLogsHistory)->createCriticalLogs($id,null,$leadId,null,null,$lead_status,$event_type,$error_type,null);
        \Log::info($data);
        $leadupdated = (new Telesales)->updatesale($reference_id, $data);
        
        //Check for child leads are exist or not
        $isChildExist = (new Telesales())->getChildLeads($leadData->id);
        if(isset($isChildExist) && $isChildExist->count() > 0){
            foreach($isChildExist as $key => $val){
                (new Telesales())->updateChildLeads($val->id,$data);
                \Log::info('Child lead details are successfully updated with lead id '.$val->id);
            }
        }
        //Update child details block end
        if ($leadupdated == 0) {
            $response = array(
                'status' => 'error',
                'message' => "Something went wrong, please try again.",
                'url' => $url

            );

        } else {
            $lead = Telesales::where('refrence_id', $reference_id)->first();
            $this->saveSelfverifyDetail($lead,$request->user_latitude,$request->user_longitude);
            $this->segmentService->createLeadStatusUpdatedTrack($lead, array_get($leadOldStatus, 'status'), array_get($lead, 'status'));
            if(!empty($lead)) {                
                $timezone = getClientSpecificTimeZone();
                GenerateReceiptPdf::dispatch($lead->id, $timezone);
                $clientId = $lead->client_id;
            }

            //TODO: Retrieve 1st attempt call delay for self tpv welcome call and schdule call according to stored delay under settings page (When it will implement)
            if (array_get($lead, 'status') == config()->get('constants.LEAD_STATUS_SELF_VERIFIED')) {
                $delays = getSelfTpvDelayTime($clientId);
                $delayTime = date('Y-m-d H:i:s');
                if (!empty($delays)) {
                    $delayTime = now()->addMinutes($delays[0]);
                }
                TelesaleScheduleCall::create([
                    "telesale_id" => $lead->id,
                    "call_immediately" => "yes",
                    "call_time" => $delayTime,
                    "call_lang" => array_get($lead, 'language'),
                    "attempt_no" => 1,
                    "call_type" => config()->get('constants.SCHEDULE_CALL_TYPE_SELF_TPV_CALLBACK'),
                    "schedule_status" => config()->get('constants.SCHEDULE_PENDING_STATUS')
                ]);
            }

            if (array_get($lead, 'status') == config()->get('constants.LEAD_TYPE_VERIFIED')) {
                //Call Webhook for LE energy Client for successful verification of lead.
                $leClientId = config()->get('constants.CLIENT_LE_CLIENT_ID');
                $leWebhookFlag = config()->get('constants.CLIENT_LE_CLIENT_WEBHOOK_FLAG');
                $leWebhookURL = config()->get('constants.CLIENT_LE_CLIENT_LEAD_WEBHOOK_URL');
                if ($leWebhookFlag == true && !empty($lead) && $leClientId == $lead->client_id) {
                    $client = Client::find($lead->client_id);
                    //Dispatch queue to call client webhook api 
                    if (!empty($client)) {
                        CreateLeadWebhookJob::dispatch($lead->id, $leWebhookURL)->delay(now()->addMinutes(5));
                    } else {
                        \Log::error("Client record not found for LE energy with id: " . $leClientId);
                    }
                }
            }
            

            if ($mail_message_text != "") {
                $leadData = (new Telesales)->getLeadInfo($reference_id);
                if ($leadData) {
                    $userDetail = (new user)->getUser($leadData->user_id);
                    
                    $greeting = 'Hello '.$userDetail->first_name.',';
                    $to = $userDetail->email;
                    $subject = 'Lead updated at TPV';
                    Mail::send('emails.common', ['greeting' => $greeting, 'msg' => $mail_message_text], function($mail) use ($to, $subject) {
                        $mail->to($to);
                        $mail->subject($subject);
                    });

                    $textEmailStatistics = new TextEmailStatistics();
                    $textEmailStatistics->type = 1;
                    $textEmailStatistics->save();
                }

            }
            $getlead_id = (new Telesales)->getLeadID($reference_id);

            if ($getlead_id) {

                $check_language = (new Telesalesdata)->leadMetakeyData($getlead_id->id, 'Language');
                if (!$check_language) {
                    $single_lead_Data = array(
                        'telesale_id' => $getlead_id->id,
                        'meta_key' => 'Language',
                        'meta_value' => $language,
                    );

                    (new Telesalesdata)->createLeadDetail($single_lead_Data);
                }

            }

            // Not sure if this is being used or not with the existing functionality
            if ($is_multiple == 1) {
                $nextleads = (new Telesales)->getNextLeadToVerify($multiple_parent_id);
                if ($getlead_id) {

                    $single_lead_Data = array(
                        'telesale_id' => $getlead_id->id,
                        'meta_key' => 'Language',
                        'meta_value' => $language,
                    );

                    (new Telesalesdata)->createLeadDetail($single_lead_Data);
                }

                if ($nextleads) {


                    $postdata = array();
                    $postdata['workspace_id'] = $request->form_worksid;
                    $postdata['workflow_id'] = $request->form_workflid;
                    $postdata['language'] = $request->current_lang;
                    $postdata['telesale_id'] = $nextleads->id;
                    $postdata['telesale_form_id'] = $request->form_id;
                    $request->merge($postdata);
                    $respons_Data = (new TelesalesVerificationController)->verifyLead($request);
                    $respons_Data['ref'] = $nextleads->refrence_id;
                    return $respons_Data;
                } else {
                    $calls_data = (new Telesales)->getSingleLead($multiple_parent_id);
                    if ($calls_data) {
                        $all_multiple_child = (new Telesales)->getAllMultipleChilds($multiple_parent_id);
                        if (count($all_multiple_child) > 0) {
                            foreach ($all_multiple_child as $sinle_lead) {
                                $update_data = array(
                                    'call_id' => $calls_data->call_id,
                                    'twilio_recording_url' => $calls_data->twilio_recording_url,
                                    'recording_id' => $calls_data->recording_id
                                );
                                (new Telesales)->updatesale($sinle_lead->refrence_id, $update_data);
                                /* TODO -> Add queue url here to save recording */
                                Log::info('Download the script to storage');
                                //(new CallsController())->downloadRecording($sinle_lead->id);
                            }
                        }

                    }
                }


            }

            $name_checking = $without_authorized = array();
            $questions_array = array();
            if (isset($request->form_id) && isset($request->current_lang)) {
                if ($status_code == 2) {
                    $next_questions_for = "after_lead_decline";
                } else {
                    $next_questions_for = 'closing';
                }


                $scripts = (new ScriptQuestions)->getScriptsUsingFormIDandLanguage($request->form_id, $request->current_lang, $next_questions_for,null,$clientId);
                Log::info('self verify script: '.print_r($scripts,true));
                if (count($scripts) > 0) {
                    $questions = array();

                    $get_lead_id = explode('-', $reference_id);
                    $telesale_id = $get_lead_id[count($get_lead_id) - 1];
                    if (isset($multiple_parent_id) && $multiple_parent_id != "") {
                        $telesale_id = $multiple_parent_id;
                    }

                    $options_to_fill = array(
                        "[Tpvagent]" => $this->highlight_tag($first_name),
                        "[Date]" => $this->highlight_tag(date('m-d-Y')),
                        "[Time]" => $this->highlight_tag(date('H:i:s')),

                    );
                    $formsFields = FormField::where('form_id', $request->form_id)->get();
                    foreach ($formsFields as $field) {
                        $fieldValue = Telesalesdata::where('field_id', $field->id)->where('telesale_id', $telesale_id)->get();
                        if (count($fieldValue) > 0) {
                            if (count($fieldValue) === 1) {
                                $options_to_fill["[$field->label]"] = $this->highlight_tag($fieldValue->first()->meta_value);
                            } else {
                                if ($field->type == 'fullname') {
                                    $full_name = [];
                                    foreach ($fieldValue as $value) {
                                        $UKey = str_replace('_', ' ', ucwords($value->meta_key, '_'));
                                        $full_name[$UKey] = $value->meta_value;
                                        $options_to_fill["[$field->label" . ' -> ' . "$UKey]"] = $this->highlight_tag($value->meta_value);
                                    }
                                    $options_to_fill["[$field->label]"] = $this->highlight_tag($full_name['First Name'] . ' ' . $full_name['Middle Initial'] . ' ' . $full_name['Last Name']);
                                } else if ($field->type == 'address') {
                                    $SAddress = [];
                                    foreach ($fieldValue as $value) {
                                        $UKey = str_replace('_', '', ucwords($value->meta_key, '_'));
                                        $SAddress[$UKey] = $value->meta_value;
                                        $options_to_fill["[$field->label" . ' -> ' . "$UKey]"] = $this->highlight_tag($value->meta_value);
                                    }
                                    $options_to_fill["[$field->label]"] = $this->highlight_tag($SAddress['Unit'] . ', ' . $SAddress['Address1'] . ', ' . $SAddress['Address2'] . ', ' . $SAddress['City'] . ', ' . $SAddress['Country'] . ', ' . $SAddress['Zipcode']);

                                } else if ($field->type == 'service_and_billing_address') {
                                    $SAddress = [];
                                    foreach ($fieldValue as $value) {
                                        $BUKey = str_replace('_', '', ucwords($value->meta_key, '_'));
                                        $options_to_fill["[$field->label" . ' -> ' . "$BUKey]"] = $this->highlight_tag($value->meta_value);
                                        $SAddress[$BUKey] = $value->meta_value;
                                    }
                                    $options_to_fill["[$field->label" . ' -> Service Address' . "]"] = $this->highlight_tag($SAddress['ServiceUnit'] . ', ' . $SAddress['ServiceAddress1'] . ', ' . $SAddress['ServiceAddress2'] . ', ' . $SAddress['ServiceCity'] . ', ' . $SAddress['ServiceCountry'] . ', ' . $SAddress['ServiceZipcode']);
                                    $options_to_fill["[$field->label" . ' -> Billing Address' . "]"] = $this->highlight_tag($SAddress['BillingUnit'] . ', ' . $SAddress['BillingAddress1'] . ', ' . $SAddress['BillingAddress2'] . ', ' . $SAddress['BillingCity'] . ', ' . $SAddress['BillingCountry'] . ', ' . $SAddress['BillingZipcode']);
                                } else {
                                    foreach ($fieldValue as $value) {
                                        $UKey = str_replace('_', '', ucwords($value->meta_key, '_'));
                                        $options_to_fill["[$field->label" . ' -> ' . "$UKey]"] = $this->highlight_tag($value->meta_value);
                                    }
                                }
                            }
                        }
                    }

                    $clientUtilityProgramTagData = (new ScriptQuestions)->clientUtilityProgramTagData($request->form_id);
                    if (count($clientUtilityProgramTagData) > 0) {
                        $array = (array)$clientUtilityProgramTagData[0];
                        foreach ($array as $tag_name => $tags_with_value) {
                            $options_to_fill[$tag_name] = $this->highlight_tag($tags_with_value);
                        }
                    }

                    $tagsToReplace = $this->getTagToReplaceForQuestions($leadData->id, $leadData->form_id);
                    Log::info("after tag replace: ".print_r($tagsToReplace,true));
                    foreach ($scripts as $script) {
                        // $get_questions_to_replace_tags  =  (new ScriptQuestions)->scriptQuestions($script->id);
                        $get_questions_to_replace_tags = (new ScriptQuestions)->scriptQuestionsWithStateCommodity($script->id, $leadzipcodestate, $leadcommodity);
                        if (count($get_questions_to_replace_tags) > 0) {
                            $questions_array = array();
                            foreach ($get_questions_to_replace_tags as $single_question) {
                                $actualQuestion=$single_question->question;
                                Log::info("before tag replace ques: ".print_r($actualQuestion,true));
                                $actualQuestion = preg_replace_callback("/\[\s*([^\]]+)\s*\]/", function ($word) {
                                        return "[".trim(strtoupper($word[1]))."]";
                                        }, $actualQuestion);
                                $full_question = strtr($actualQuestion, $tagsToReplace);
                                $questions_array[] = $full_question;
                                Log::info("after tag replace ques: ".print_r($full_question,true));
                            }
                            $questions[$script->scriptfor] = $questions_array;
                        }
                        //=   (new ScriptQuestions)->scriptQuestions($script->id);

                    }
                }

            } else {
                $questions_array = array();
            }
            if ($status_code == 3) {
                $questions_array = array();
            }
            $msg = "";
            if ($status_code == '2') {
                $msg="This sale successfully declined.";
                $declineType = true;
            } else {
                // $msg="This sale successfully verified.";
                $declineType = false;
            }
            $verificationCode = '';
            if($status == config()->get('constants.LEAD_TYPE_VERIFIED')){
                $verificationCode = Telesales::where('refrence_id',$reference_id)->pluck('verification_number');
                $verificationCode = $verificationCode[0];
                 // for check send contract pdf after lead verify in self verify
                if ($lead->type == 'tele' && isOnSettings(array_get($lead, 'client_id'), 'is_enable_send_contract_after_lead_verify_tele',false) || $lead->type == 'd2d' && isOnSettings(array_get($lead, 'client_id'), 'is_enable_send_contract_after_lead_verify_d2d',false)) {
                    Log::info('Save Lead Contract PDF');
                    
                    //check whether this lead has child leads or not
                    // $isChildExist = $lead->childLeads()->get();
                    // //if there are child leads then generate child leads contract 
                    // if(isset($isChildExist) && $isChildExist->count() > 0){
                    //     foreach ($isChildExist as $key => $val) {
                    //         \App\Jobs\SendContractPDF::dispatch($val->id,'','','child');
                    //     }
                    // }
                    //send parent lead contract
                    \App\Jobs\SendContractPDF::dispatch($lead->id);
                }
            }
           

            $response = array(
                'status' => 'success',
                'message' =>$msg ,
                'verification_all_done' => true,
                'questions' => $questions_array,
                'url' => $url,
                'verificationCode' => $verificationCode,
                'declineType' => $declineType
            );
            return $response;

        }
        // return redirect()->back()->with('success','Record Updated');
    }

    /**
     * This method is used for update lead object and store critocal logs
     */
    public function leadSaleUpdate(Request $request)
    {
        $reference_id = $request->get('reference_id');
        $leadData = Telesales::where('refrence_id', $reference_id)->first();
        try {
            if ($leadData->status == 'pending' || $leadData->status == "hangup" || $leadData->status == config()->get('constants.LEAD_STATUS_SELF_VERIFIED')) {
                $reviewedby_id = 1;
                if (Auth::check()) {
                    $reviewedby_id = Auth::user()->id;
                }

                if ($request->get('call_type') == config()->get('constants.OUTBOUND_CALL_TYPE')) {
                    $verificationMethod = config()->get('constants.TPV_NOW_OUTBOUND_METHOD');
                } else {
                    $verificationMethod = ($request->verification_method == 'customer_call_in_verification') ? config('constants.VERIFICATION_METHOD.CUSTOMER_INBOUND') : config('constants.VERIFICATION_METHOD.AGENT_INBOUND');
                }

                $data = array('reviewed_by' => $reviewedby_id, 'updated_at' => date('Y-m-d H:i:s'));
                $data['status'] = 'verified';
                $data['verification_method'] = $verificationMethod;
                $data['language'] = $request->current_language;
                $data['reviewed_at'] = date('Y-m-d H:i:s');
                \Log::info($data);
                $leadupdated = (new Telesales)->updatesale($reference_id, $data);

                //check whether child leads are exist or not
                $leadId = (new Telesales)->getLeadID($reference_id);
                $isChildExist = (new Telesales())->getChildLeads($leadId->id);
                if(isset($isChildExist) && $isChildExist->count() > 0){
                    //update child leads 
                    foreach($isChildExist as $key => $val){
                        (new Telesales())->updateChildLeads($val->id,$data);
                        \Log::info('Child lead details are successfully updated with lead id '.$val->id);
                    }
                }

                //Store lead status and verified disposition id in twilio lead call details table
                $twilioCalls = TwilioLeadCallDetails::where('task_id',$request->taskId)->first();
                if(!empty($twilioCalls)){
                    $twilioCalls->lead_status = 'verified';
                    $twilioCalls->save();
                }

                // for check send contract pdf after lead verify
                if (($leadData->type == 'tele' && isOnSettings(array_get($leadData, 'client_id'), 'is_enable_send_contract_after_lead_verify_tele',false)) || ($leadData->type == 'd2d' && isOnSettings(array_get($leadData, 'client_id'), 'is_enable_send_contract_after_lead_verify_d2d',false))) {
                    Log::info('Save Lead Contract PDF');

                    //check whether this lead has child leads or not
                    // $isChildExist = $leadData->childLeads()->get();
                    // //if there are child leads then generate child leads contract 
                    // if(isset($isChildExist) && $isChildExist->count() > 0){
                    //     foreach ($isChildExist as $key => $val) {
                    //         \App\Jobs\SendContractPDF::dispatch($val->id,'','','child');
                    //     }
                    // }
                    //send parent lead contract
                    \App\Jobs\SendContractPDF::dispatch($leadData->id);
                }


                $newUpdatedLead = Telesales::find($leadData->id);
                // $newUpdatedLead->verification_number = getVerificationNumer();
                // $newUpdatedLead->save();
                //Check for call type & perform different actions for it after lead verification
                if ($request->has('call_type') && ($request->get('call_type') == config()->get('constants.OUTBOUND_CALL_TYPE') || $request->get('call_type') == config()->get('constants.TWILIO_CALL_TYPE_SELFVERIFIED_CALLBACK'))) {

                    \Log::info('When call type is outbound or self verified call back');
                    //Send success mail to sales agent after outbound call lead gets verified
                    $this->sendLeadSuccessMail($newUpdatedLead->id);

                    //Register non critical logs after lead getting verified
                    $this->registerLogsForOutboundCompletion($newUpdatedLead, 'Event_Type_23');
                } else {
                    \Log::info('When call type is inbound');

                    $user_type = config('constants.USER_TYPE_CRITICAL_LOGS.1');
                    $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Non-critical');
                    $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Verified');
                    if($request->verification_method == 'customer_call_in_verification') {
                        $salesAgentId = null;
                        $message = __('critical_logs.messages.Event_Type_18');
                        $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_18');
                    } else {
                        $salesAgentId = $leadData->user_id;
                        $message = __('critical_logs.messages.Event_Type_26');
                        $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_26');
                    }

                    (new CriticalLogsHistory)->createCriticalLogs($salesAgentId,$message,$leadData->id,null,null,$lead_status,$event_type,$error_type,$user_type,$reviewedby_id);
                }

                //----------------- LE WEBHOOK API ------------------------
                 //Call Webhook for LE energy Client for successful verification of lead.
                 $leClientId = config()->get("constants.CLIENT_LE_CLIENT_ID");
                 $leWebhookFlag = config()->get('constants.CLIENT_LE_CLIENT_WEBHOOK_FLAG');
                 $leWebhookURL = config()->get('constants.CLIENT_LE_CLIENT_LEAD_WEBHOOK_URL');
                 if ($leWebhookFlag == true && !empty($newUpdatedLead) && $leClientId == $newUpdatedLead->client_id) {
                     $client = Client::find($newUpdatedLead->client_id);
                     //Dispatch queue to call client webhook api 
                     if (!empty($client)) {
                         CreateLeadWebhookJob::dispatch($newUpdatedLead->id, $leWebhookURL)->delay(now()->addMinutes(5));
                     } else {
                         \Log::error("Client record not found for LE energy with id: " . $leClientId);
                     }
                 }

                //----------------------- LE WEBHOOK API END -----------------------

                //Register lead expire logs after lead verified
                $this->registerLogsForSelfVerificationExpire($newUpdatedLead);

                $this->segmentService->createLeadStatusUpdatedTrack($leadData, array_get($leadData, 'status'), array_get($newUpdatedLead, 'status'));

                $language = "English";
                if ($request->current_language == 'es') {
                    $language = "Spanish";
                }

                $check_language = (new Telesalesdata)->leadMetakeyData($leadData->id, 'Language');
                if (!$check_language) {
                    $single_lead_Data = array(
                        'telesale_id' => $leadData->id,
                        'meta_key' => 'Language',
                        'meta_value' => $language,
                    );

                    (new Telesalesdata)->createLeadDetail($single_lead_Data);
                }

                $fromscriptsData = FormScripts::where('client_id', $leadData->client_id)->where('form_id', 0)->where('language', 'en')->where('scriptfor', 'closing')->first();
                \Log::info(print_r($fromscriptsData, true));
                if (!empty($fromscriptsData)) {
                    $questionsData = ScriptQuestions::where('client_id', $leadData->client_id)->where('form_id', 0)->where('script_id', $fromscriptsData->id)->first();
                    \Log::info("Questions data: " . print_r($questionsData, true));
                    $text = $questionsData->question;
                    $tags = $this->getTagToReplaceForQuestions($leadData->id, $leadData->form_id);
                    \Log::info("Questions data after tag replace: " . print_r($tags, true));
                    /*preg_match_all("/\[([^\]]*)\]/", $text, $matches);
                    $message = '';
                    foreach ($matches[1] as $key => $text_data) {
                        $teleSalesData = Telesalesdata::where('meta_key', $text_data)->where('telesale_id', $reference_id)->first();
                        if (!empty($teleSalesData)) {
                            $message_meta = $teleSalesData->meta_value;
                        } else {
                            $message_meta = "[" . $text_data . "]";
                        }

                        $text = str_replace("[" . $text_data . "]", $message_meta, $text);
                    }*/
                    $text = preg_replace_callback("/\[\s*([^\]]+)\s*\]/", function ($word) {
                        return "[" . trim(strtoupper($word[1])) . "]";
                    }, $text);
                    $text_message = nl2br(htmlspecialchars_decode(strtr($text, $tags)));
                } else {
                    $text_message = " ";
                }

                \Log::info("Questions data out");

                $response = array(
                    'status' => 'success',
                    'message' => "Record Updated.",
                    'data' => $text_message
                );
            } else {
                // for send mail to global admin  when lead is not able to verify
                // $admins = config()->get('constants.TPV360_SUPPORT_EMAIL');
                $admins = explode(',',config()->get('constants.TPV360_SUPPORT_EMAIL'));

                $message = "The lead verification has been completed with errors.<br/><br/>";
                $message .= 'Lead Details:<br/>';
                $message .= "<b>Verification ID is ".$leadData->verification_number."<br/>";
                $message .= "Lead ID is ".$leadData->refrence_id."</b><br/><br/>";
                $message .= "Verification was completed by <b> ".Auth::user()->first_name .' '.Auth::user()->last_name."(".Auth::user()->userid.")</b> <br/><br/>";
                $message .= "<b>Lead Status: ".config()->get('constants.VERIFICATION_STATUS_CHART.'.ucfirst($leadData->status))."</b><br/><br/>";
                $message .= "Error Details:<br/>";
                $message .= "Lead not found or not in pending, disconnected or self-verified status.";
                if(!empty($admins)){
                    foreach($admins as $email) {
                        $greeting = "Hello Team,";
                        Mail::to($email)->send(new LeadVerifyFailedEmail($greeting, $message));    
                    }
                    info("Email sent to global admin for lead verification failed.");
                }
                else{
                    info("Mail address not found.");
                }
                $errMessage = "Verification has been completed with few errors. Verification ID is ".$leadData->verification_number." and Lead is ".$leadData->refrence_id.". An email has been sent to TPV360 Support Team about issue. Please Click ok and then Disconnect the call.";
                $response = array(
                    'status' => 'error',
                    'message' => $errMessage,
                );
            }

        } catch (\Exception $e) {
            \Log::error('Exception in lead status update..');
            \Log::error($e);
            $response = array(
                'status' => $e->getMessage(),
                'message' => "Something went wrong, please try again."
            );
        }
        return $response;
    }


    /**
     * This function is used for show supportdashboard
     */
    public function supportdashboard()
    {

        return view('tpvagents.callcenter.supportDashboard');
    }

    /**
     * This function is used to get client salesagent
     */
    public function getClientSalesAgents(Request $request)
    {
        $ClientWorkspaceobj = (new ClientWorkspace);
        $clientworkspace = $ClientWorkspaceobj->getClientUsingWorkspaceID($request->workspace_id);
        $salesagents = (new User)->getClientSalesagents($clientworkspace->client_id);
        return view('tpvagents.ajaxviews.clientsalesagents', compact('salesagents'));
    }

    /**
     * This function is used to get saleagent
     */
    public function getAgentSales(Request $request)
    {

        $all_leads = (new Telesales)->getUserAllLeads($request->uid);
        return view('tpvagents.ajaxviews.clientagentsales', compact('all_leads'));
    }

    /**
     * This function is used to get formscript
     */
    public function getFormScript(Request $request)
    {
        $workspace_id = $request->workspace_id;
        $workflow_id = $request->workflow_id;
        $language = $request->language;
        \Log::info($workspace_id);
        \Log::info($workflow_id);
        \Log::info($language);

        $scripts = (new ScriptQuestions)->getScripts($workspace_id, $workflow_id, $language, 'salesagentintro');

        \Log::info($scripts);
        if (count($scripts) > 0) {


            $questions = array();
            $repalce_tag_array = $this->getTagsToBeReplaced($scripts[0]->form_id);

            foreach ($scripts as $script) {
                $get_questions_to_replace_tags = (new ScriptQuestions)->scriptQuestions($script->id);
                if (count($get_questions_to_replace_tags) > 0) {
                    $questions_array = array();
                    foreach ($get_questions_to_replace_tags as $single_question) {
                        $formedSingleQuestion = preg_replace_callback("/\[\s*([^\]]+)\s*\]/", function ($word) {
                            return trim(strtoupper($word[1]));
                        }, array_get($single_question, 'question'));
                        $full_question = strtr($formedSingleQuestion, $repalce_tag_array);
                        // $full_question = strtr($single_question->question, $repalce_tag_array);
                        $questions_array[] = ['question' => $full_question, 'id' => $single_question->id, 'position' => $single_question->position];
                    }
                    $questions[$script->scriptfor] = $questions_array;
                }
                //=   (new ScriptQuestions)->scriptQuestions($script->id);

            }
            $response = array('status' => 'success', 'question' => $questions);

        } else {
            $response = array('status' => 'error', "message" => "No script found.");
        }

        return $response;

    }

    /**
     * This function is used to lead question
     */
    public function getLeadQuestion(Request $request)
    {
        /* Start Validation rule */
        $this->validate($request,
            [
                'uid' => "required",
            ]
        );
        /* End Validation rule */
        $assigned_forms = (new UserAssignedForms)->getAssignedForm($request->uid);

        if (!empty($assigned_forms)) {
            $form_id = $assigned_forms->form_id;
            $language = $request->language;
            $script_data = (new FormScripts)->get_script_id($form_id, $language);
            if (empty($script_data)) {
                return array(
                    'status' => 'error',
                    'message' => "No script found."
                );
            } else {

                $questions_for_lead_creation = (new ScriptQuestions)->scriptQuestions($script_data->id);

                if (count($questions_for_lead_creation) > 0) {
                    $repalce_tag_array = $this->LeadCreateTagsToBeReplaced($script_data->form_id);


                    $questions_array = array();
                    foreach ($questions_for_lead_creation as $single_question) {
                        $full_question = strtr($single_question->question, $repalce_tag_array);
                        $questions_array[] = ['question' => $full_question, 'id' => $single_question->id, 'position' => $single_question->position];
                    }

                    return array('status' => 'success', 'formid' => $script_data->form_id, 'question' => $questions_array);
                } else {
                    return array(
                        'status' => 'error',
                        'message' => "No question found."
                    );
                }

            }


        } else {
            return array(
                'status' => 'error',
                'message' => "No form found."
            );
        }


    }


    function getTagsToBeReplaced($form_id)
    {
        $tags_to_replace = array(
            "TPVAGENT" => $this->highlight_tag(Auth::user()->first_name),
            "DATE" => $this->highlight_tag(date('Y-m-d')),
            "TIME" => $this->highlight_tag(date('H:i:s')),
            "Agent ID Verification Box" => "<div class='agent_verification_id_wrapper'>
                                               <span class='inline-block'><input class='form-control verify-agent-ID question-input' autocomplete='off' placeholder='Enter sales agent ID' name='question[Agent ID]' >
                                               </span>
                                                 <span  class='inline-block'> <button type='button' class='checkagentid btn btn-primary'>Verify</button> </span>
                                                 <span class='agent-verify-status inline-block'></span>
                                           </div>",
            "Telesale ID Verification Box" => "<div class='telesale_verification_id_wrapper'>
                                                   <span class='inline-block'>  <input class='form-control verify-telesale-ID question-input' placeholder='Enter telesales ID' name='question[Telesale ID]' autocomplete='off' >
                                                   </span>
                                                   <span  class='inline-block'> <button type='button' class='checktelesaleid btn btn-primary'>Verify</button> </span>
                                                    <span class='telesale-verify-status'></span>
                                             </div>",
            "Client ID Verification Box" => "<div class='client_id_verification_wrapper'>
            <span class='inline-block'>  <input class='form-control verify-client-ID question-input' placeholder='Enter Client ID' name='question[Client ID]' autocomplete='off'>
            </span>
            <span  class='inline-block'> <button type='button' class='checkcleint_id btn btn-primary'>Verify</button> </span>
            <span class='client-verify-status'></span>
        </div>"

        );

        // Create json for using label_text
        $form = Clientsforms::withTrashed()->find($form_id);

        if (array_get($form, 'id')) {
            $fields = $form->fields()->pluck('label')->toArray();

            $clientUtilityProgramTagData = (new ScriptQuestions)->clientUtilityProgramTagData($form_id);

            if (count($clientUtilityProgramTagData) > 0) {
                $array = (array)$clientUtilityProgramTagData[0];
                foreach ($array as $tag_name => $tags_with_value) {
                    $tags_to_replace[$tag_name] = $this->highlight_tag($tags_with_value);
                }
            }

            if (count($fields) > 0) {
                foreach ($fields as $field) {
                    $tags_to_replace["[" . $field . "]"] = $this->highlight_tag($field);
                }
            }
        }
        \Log::info($tags_to_replace);
        return $tags_to_replace;
    }

    function LeadCreateTagsToBeReplaced($form_id)
    {


        $form_detail = (new ClientsForms)->withTrashed()->getClientFormFields($form_id);

        $utility = (New Utilities)->getUtility($form_detail[0]->utility_id);
        $programs = (new Programs)->getAllPrograms_using_utility_shortname($form_detail[0]->client_id, $utility->utilityshortname);
        $program_data = '<input type="hidden" name="fields[Program Code]" class="program_code" value="" ><select name="fields[Program]"  class="form-control selectprogram programselectoncall" rel="selectbox">
                                   <option value="">Select</option>';
        if (count($programs) > 0):

            foreach ($programs as $program):
                $program_data .= '<option
                                       value="' . $program['name'] . '"
                                       data-programname="' . $program['name'] . '"
                                       data-code="' . $program['code'] . '"
                                       data-rate="' . $program['rate'] . '"
                                       data-etf="' . $program['etf'] . '"
                                       data-msf="' . $program['msf'] . '"
                                       data-term="' . $program['term'] . '"
                                       data-accountlength="' . $program['accountnumberlength'] . '"
                                       data-accountnumbertype="' . $program['account_number_type'] . '"

                                       >'
                    . $program['code'] . '
                                  </option>';
            endforeach;
        endif;
        $program_data .= '</select>';

        $tags_to_replace = array(
            "[Tpvagent]" => $this->highlight_tag(Auth::user()->first_name),
            "[Date]" => $this->highlight_tag(date('Y-m-d')),
            "[Time]" => $this->highlight_tag(date('H:i:s')),

            "[Account Number]" => "<span><input name='fields[Account Number]' placeholder='Account Number' ></span>",
            "[Program Code]" => "<span>" . $program_data . "</span>",
            "[Account Number Length]" => "<span><input name='fields[Account Number Length]' placeholder='Account Number Length' class='account_number_length' ></span>",
            "[Account Number Type]" => "<span><input name='fields[Account Number Type]' placeholder='Account Number Type'  class='account_number_type' ></span>",
            "[Agent ID Verification Box]" => "<div class='agent_verification_id_wrapper'>
                                               <span class='inline-block'><input class='form-control verify-agent-ID question-input' autocomplete='off' placeholder='Enter sales agent ID' name='question[Agent ID]' >
                                               </span>
                                                 <span  class='inline-block'> <button type='button' class='checkagentid btn btn-primary'>Verify</button> </span>
                                                 <span class='agent-verify-status inline-block'></span>
                                           </div>",
            "[Telesale ID Verification Box]" => "<div class='telesale_verification_id_wrapper'>
                                                   <span class='inline-block'>  <input class='form-control verify-telesale-ID question-input' placeholder='Enter telesales ID' name='question[Telesale ID]' autocomplete='off'>
                                                   </span>
                                                   <span  class='inline-block'> <button type='button' class='checktelesaleid btn btn-primary'>Verify</button> </span>
                                                    <span class='telesale-verify-status'></span>
                                             </div>",
            "[Client ID Verification Box]" => "<div class='client_id_verification_wrapper'>
            <span class='inline-block'>  <input class='form-control verify-client-ID question-input' placeholder='Enter Client ID' name='question[Client ID]' autocomplete='off'>
            </span>
            <span  class='inline-block'> <button type='button' class='checkcleint_id btn btn-primary'>Verify</button> </span>
            <span class='client-verify-status'></span>
        </div>"

        );


        $clientUtilityProgramTagData = (new ScriptQuestions)->clientUtilityProgramTagData($form_id);

        if (count($clientUtilityProgramTagData) > 0) {
            $array = (array)$clientUtilityProgramTagData[0];
            foreach ($array as $tag_name => $tags_with_value) {
                $tags_to_replace[ucfirst($tag_name)] = "<span><input name='fields[" . ucfirst($tag_name) . "]' placeholder='" . $tags_with_value . "' ></span>";
            }
        }

        $fields = json_decode($form_detail[0]->form_fields);
        foreach ($fields as $field) {

            $multiselect = "";

            $requiredchecked = "field-span  ";
            $fields++;
            if (isset($field->required)) {
                $requiredchecked = "field-span required";
            }
            if (isset($field->multiselect)) {
                $multiselect = "multiple";
            }

            if ($field->type == 'radio') {
                $radio_options = "";
                $selected_checkbox = (isset($field->options->selected)) ? $field->options->selected : "";
                foreach ($field->options->label as $checkboxoptions) {
                    if ($checkboxoptions == $selected_checkbox) {
                        $checked = "checked";
                    } else {
                        $checked = "";
                    }

                    $radio_options .= '<span >
                  <label for="' . $checkboxoptions . '" class="radio-inline"><input type="radio" name="fields[' . $field->label_text . ']"  ' . $checked . '  value="' . $checkboxoptions . '" id="' . $checkboxoptions . '" ><span class="checkbox-radio-label-text">' . $checkboxoptions . '</span></label>
                  </span>';
                }
                $tags_to_replace["[" . $field->label_text . "]"] = "<span class='" . $requiredchecked . " radio_field radio-btns' data-reltype='radio'>" . $field->label_text . $radio_options . "</span>";
            } else if ($field->type == 'checkbox') {
                $checkbox_options = "";
                $selected_checkbox = (isset($field->options->selected)) ? $field->options->selected : array();
                foreach ($field->options->label as $checkboxoptions) {
                    if (in_array($checkboxoptions, $selected_checkbox)) {
                        $checked = "checked";
                    } else {
                        $checked = "";
                    }

                    $checkbox_options .= '<span class="checkbox">

                  <label for="' . $checkboxoptions . '" class="checkbx-style" >  <input type="checkbox" name="fields[' . $field->label_text . ']"  ' . $checked . '  value="' . $checkboxoptions . '" id="' . $checkboxoptions . '" ><span class="checkbox-radio-label-text">' . $checkboxoptions . '</span></label>
                  </span>';
                }
                $tags_to_replace["[" . $field->label_text . "]"] = "<span class='" . $requiredchecked . " checkbox_field' data-reltype='checkbox' >" . $field->label_text . $checkbox_options . "</span>";
            } else if ($field->type == 'selectbox') {
                $selectbox = "";
                $selectbox .= '<select class="form-control maxwidth200 validate" ' . $multiselect . '   name="fields[' . $field->label_text . ']">
            <option value="">Select</option>';

                foreach ($field->options->label as $selectoptions) {
                    $selectbox .= '<option>' . $selectoptions . '</option>';
                }

                $selectbox .= '</select>';


                $tags_to_replace["[" . $field->label_text . "]"] = $field->label_text . "<span class='" . $requiredchecked . " selectbox_field' data-reltype='selectbox' >" . $selectbox . "</span>";
            } else if ($field->type == 'phonenumber') {
                $tags_to_replace["[" . $field->label_text . "]"] = "<span class='" . $requiredchecked . "  text_field' data-reltype='phonenumber' ><input class='form-control contact-number-format maxwidth200' type='text' name='fields[" . $field->label_text . "]' maxlength='14' placeholder='(---) --- ----' ></span>";
            } else {
                $tags_to_replace["[" . $field->label_text . "]"] = "<span class='" . $requiredchecked . " text_field' data-reltype='text' ><input class='form-control maxwidth200' type='text' name='fields[" . $field->label_text . "]' placeholder='" . $field->label_text . "' ></span>";
            }

        }

        return $tags_to_replace;
    }

    function commonTagReplace()
    {
        $tags_to_replace = array(
            "TPVAGENT" => $this->highlight_tag(Auth::user()->first_name),
            "DATE" => $this->highlight_tag(date('Y-m-d')),
            "TIME" => $this->highlight_tag(date('H:i:s'))
        );

        return $tags_to_replace;
    }


    /**
     * This function is used to create lead
     */
    function createlead(Request $request)
    {

        $client_id = $request->agent_client_id;
        $agent_user_id = $request->agent_user_id;
        $userData = (new User)->getUser($agent_user_id);
        $location_id = $userData->location_id;
        $salescenter_id = $userData->salescenter_id;
        $refrence_id = (new ClientController)->get_client_salesceter_location_code($client_id, $salescenter_id, $location_id);

        $lead_data['client_id'] = $client_id;
        $lead_data['form_id'] = $request->form_id;
        $lead_data['parent_id'] = "0";
        $lead_data['cloned_by'] = "0";
        $lead_data['refrence_id'] = $refrence_id;
        $lead_data['user_id'] = $agent_user_id;


        $telesale_id = (new Telesales)->createLead($lead_data);
        foreach ($request->fields as $meta_key => $meta_value) {
            $val = is_array($meta_value) ? implode(',', $meta_value) : $meta_value;
            $single_lead_Data = array(
                'telesale_id' => $telesale_id,
                'meta_key' => $meta_key,
                'meta_value' => $val,
            );
            (new Telesalesdata)->createLeadDetail($single_lead_Data);
        }
        $single_lead_Data = array(
            'telesale_id' => $telesale_id,
            'meta_key' => "Lead Verification ID",
            'meta_value' => $refrence_id,
        );
        (new Telesalesdata)->createLeadDetail($single_lead_Data);

        $number_of_minus = explode('-', $refrence_id);

        $telesaleid = $number_of_minus[count($number_of_minus) - 1];

        $data['workspace_id'] = $request->form_worksid;
        $data['workflow_id'] = $request->form_workflid;
        $data['language'] = $request->current_lang;
        $data['telesale_id'] = $telesaleid;
        $data['telesale_form_id'] = $request->form_id;


        $request->merge($data);
        $respons_Data = (new TelesalesVerificationController)->verifyLead($request);
        $respons_Data['reference_id'] = $refrence_id;
        $respons_Data['telesaleid'] = $telesaleid;
        $respons_Data['formid'] = $request->form_id;

        return $respons_Data;

    }


    function highlight_tag($content, $fieldId='', $addressType = '',$leadId = '')
    {
        return "<span class='question-tag' data-field-id='".$fieldId."' data-address='".$addressType."' data-lead-id='".$leadId."' >" . $content . "</span>";
    }

    /**
     * This function is used to show edit profile detail
     */
    public function editprofile()
    {
        try{
            if(isset(Auth::user()->id) && !empty(Auth::user()->id)){
                $user = User::where([
                    ['id', '=', Auth::user()->id],
                ])->firstOrFail();
                $twilio_ids = (new UserTwilioId)->getTwilioIds(Auth::user()->id);
                $assignedclients = (new UserTwilioId)->getAssignedClients(Auth::user()->id);

                return view('/tpvagents/editprofile', compact('user', 'twilio_ids', 'assignedclients'));
            }else {
                return redirect()->route('login')->withErrors('You are not login as a tpv agent.');
            }
        } catch(\Exception $e){
            // Log Message
            Log::error(strtr(trans('auth.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return redirect()->route('login')->withErrors(trans('auth.DEFAULT_ERROR_MESSAGE'));
        }

    }

    /**
     * This function is used to update profile
     */
    public function updateprofile(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'file' => 'mimes:jpg,jpeg,png|max:5120',
            'password' => $request->password != null ? 'min:6' : '',
            'password_confirmation' => 'same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error',  'errors' => $validator->errors()->messages()], 422);
        }
        try {

            $userid = Auth::user()->id;
            $user = User::find($userid);

            if (!empty($request->password)) {
                $user->password = Hash::make($request->password); //update the password
            }

            if ($request->hasFile('file')) {
                Storage::disk('s3')->delete($request->old_url);
                $file = $request->file('file');
                $awsFolderPath = config()->get('constants.aws_folder');
                $filePath = config()->get('constants.TPVAGENT_PROFILE_PICTURE_UPLOAD_PATH');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $this->storageService->uploadFileToStorage($file, $awsFolderPath, $filePath, $fileName);
                if ($path !== false) {
                    $user->profile_picture = $path;
                }
            }

            if($request->has('timezone'))
            {
                $user->timezone = $request->timezone;
            }
            $user->save();
            session()->put('message', 'Your profile was successfully updated.');
            return response()->json(['status' => 'success', 'message' => 'Your profile was successfully updated.'], 200);
        } catch (\Exception $e) {
            \Log::error($e);
            session()->put('message', $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function questions(Request $request)
    {
        
        try {
            if ($request->has('scriptType') && $request->has('teleSaleId')) {
                $removedTag = [];
                $telesale = Telesales::where('refrence_id', $request->get('teleSaleId'))->first();
                $telesale->verification_number = generateVerificationNumer($telesale);
                $telesale->save();

                //check for tpv attempt alert tele/d2d
                $this->checkAlertTeleD2d($telesale);

                $childLeads =  (new Telesales())->getChildLeads($telesale->id);
                foreach($childLeads as $key => $val){
                    $val->verification_number = $telesale->verification_number;
                    $val->save();
                }
                \Log::info('child leads are..');
                \Log::info($childLeads);
                $teleSale = Telesales::with('form')->where('refrence_id', $request->get('teleSaleId'))->first();
                if (!empty($teleSale)) {
                    $customerName = $this->getCustomerName($teleSale);
                    if (array_get($teleSale, 'form')) {
                        $zipcode = $teleSale->zipcodes()->first();
                        if (!empty($zipcode)) {
                            $form = $teleSale->form;
                            $tags = $this->getTagToReplaceForQuestions($teleSale->id, $teleSale->form_id);
                            $pattern = '/.*?<span.*?>/';
                            foreach($tags as $key => $val)
                            {
                                $pattern = '/.*?<span.*?>(.*?)<\/span>/';
                                if(preg_match($pattern, $val)){
                                    preg_match($pattern, $val, $matches);
                                    if($matches[1] == '')
                                    {
                                        $removedTag[] = $key;
                                    }
                                }
                            }
                            $formId = array_get($form, 'id');
                            $clientId = array_get($teleSale,'client_id');
                            
                            $scriptType = $request->get('scriptType');
                            $language = $request->get('current_language');
                            $formScript = FormScripts::where('scriptfor', $request->get('scriptType'))->where('language', $request->get('current_language'))->where('form_id', array_get($form, 'id'))
                                            ->where(function ($que) use($zipcode,$scriptType,$language,$formId,$clientId) {
                                                $que->whereRaw(DB::raw("CASE WHEN(select count(id) from form_scripts where state = '".$zipcode->state."' and language = '".$language."' and form_id = ".$formId." and client_id = ".$clientId." and scriptfor = '" . $scriptType . "') > 0 then form_scripts.state ='".$zipcode->state."' and language = '".$language."' and form_id = ".$formId." and scriptfor = '" . $scriptType . "'else form_scripts.state='ALL' and language = '".$language."' and form_id = ".$formId." and scriptfor = '" . $scriptType . "' end "));
                                                // $que->where('state', 'LIKE', "%$zipcode->state%")
                                                //     ->orWhere('state', 'ALL');
                                            })->first();
                                
                            if (!empty($formScript)) {
                                $leadId = $teleSale->id;
                                $introQuestions = ScriptQuestions::where('script_id', array_get($formScript, 'id'))->where('form_id', array_get($form, 'id'))->orderBy('position', 'ASC')->where('is_introductionary', 1)->get();
                                Log::info($introQuestions);
                                $introQuestions = ScriptQuestions::where('script_id', array_get($formScript, 'id'))->where('form_id', array_get($form, 'id'))->orderBy('position', 'ASC')->where('is_introductionary', 1)->count();
                                $questions = ScriptQuestions::with(['questionConditions' => function($qu) {
                                    $qu->where('condition_type','question');
                                }])
                                ->where('script_id', array_get($formScript, 'id'))->where('form_id', array_get($form, 'id'))->orderBy('position', 'ASC')->get()->toArray();
                                $questionIds = array_column($questions,'id');
                                $conditions = DB::table('script_questions_conditions')->whereIn('question_id',$questionIds)->where('condition_type','tag')->get()->toArray();
                                \Log::info($conditions);
                                $emptyFlag = false;
                                $skipQue = false;
                                $childItr = 0;
                                $isChild = false;
                                $allQuestions = [];
                                $tagsLocal = $tags;
                                $childQuestions = [];
                                $leadIdLocal = $leadId;
                                $questionsArray = [];
                                $questionCount= $childQuestionCountTotal = 0;
                                //counting total no of iteration in for loop if child leads are not exist then iteration count is 1
                                $itrCount = 1;
                                if($childLeads->count() != 0){
                                    $itrCount = $childLeads->count();
                                }
                                //for check all lead's condition parent lead and child lead question conditons
                                for($itr = 0; $itr < $itrCount;){
                                    $questionCount= 0;
                                    if($childLeads->count() > 0){
                                        $child = $childLeads[$itr];
                                        $childItr++;
                                        if($childItr > 1){
                                            $itr++;
                                            $isChild = true;
                                            $leadIdLocal = $child->id;
                                            // get tag array of particular lead id
                                            $tagsLocal = $this->getTagToReplaceForQuestions($child->id, $child->form_id);;
                                        }                       
                                    }
                                    else{
                                        $itr++;
                                    }
                                    $questionsArray = [];                     
                                    foreach($questions as $k => $v)
                                    {    
                                        //if lead is child then only is multiple questions are to be taken
                                        if($isChild == true){
                                            if($v['is_multiple'] != 1){
                                                continue;
                                            }
                                        }
                                        $ques = $v['question'];
                                        if(count($conditions) > 0)
                                        $skipQue = $this->checkScriptQuestionCondition($conditions,$ques,$v,$tagsLocal);
                                        
                                        if($skipQue == true)
                                        {
                                            continue;
                                        }
                                        
                                        while(strpos($ques,'[') !== false)
                                        {
                                            $string = preg_match('/\[(.*?)\]/',$ques,$matches);
                                            $matchedString = '['.strtoupper(trim($matches[1])) . ']';
                                            if(in_array($matchedString,$removedTag))
                                            {
                                                $emptyFlag = true;
                                            }
                                            else
                                            {
                                                $emptyFlag = false;
                                                break;
                                            }
                                            $ques = substr($ques,strpos($ques,']')+1);
                                        }
                                        if($emptyFlag == true)
                                        { 
                                            continue;
                                        }
                                        $questionsArray[] = $v;
                                        $questionCount++;
                                    }
                                    
                                    //Prepare array with question and tag of particular lead
                                    $allQuestions[$leadIdLocal.'-question'] = $questionsArray;
                                    $allQuestions[$leadIdLocal.'-tag'] = $tagsLocal;
                                    //count total no. of child quesitons 
                                    $childQuestionCountTotal = $childQuestionCountTotal + $questionCount;                                    
                                }
                                \Log::info($allQuestions);

                                //Prepare total question count parent lead and child lead
                                
                                $questions = array_values($questions);
                                $questionCount = $childQuestionCountTotal;

                                \Log::info($questions);
                                \Log::info('Total no of child quesitons: '.$childQuestionCountTotal);
                                //return view('frontend.tpvagent.questions', compact('questions', 'tags', 'leadId'));
                                return response()->json(['status' => 'success', 'message' => 'success', 'customer_name' => $customerName, 'html' => view('frontend.tpvagent.questions', compact('questions', 'introQuestions','allQuestions', 'tags', 'leadId','childLeads','questionCount'))->render()], 200);
                            } else {

                                return response()->json(['status' => 'error', 'message' => 'Script not found.', 'html' => ''], 400);
                            }
                        } else {
                            return response()->json(['status' => 'error', 'message' => 'Script not found.', 'html' => ''], 400);
                        }
                    } else {
                        return response()->json(['status' => 'error', 'message' => 'Form not found for this script.', 'html' => ''], 400);
                    }
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Lead not found.', 'html' => ''], 400);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'Please pass all required parameters.', 'html' => ''], 422);
            }
        } catch (\Exception $e) {
            Log::info("error:" .$e);
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'html' => ''], 500);
        }
    }


    public function getTagToReplaceForQuestions($lead_id, $form_id)
    {
        if (isset($request->vtype)) {
            $tpv_agent_name = "";
        } else {
            if(Auth::check()) {
                $tpv_agent_name = Auth::user()->first_name;
            } else {
                $tpv_agent_name = "";
            }
        }

        // $d = date('Y-m-d H:i:s');
        // $date = new \DateTime($d, new \DateTimeZone('UTC'));
        // $date->setTimezone(new \DateTimeZone('America/New_York'));
        // $cd = $date->format('m-d-Y');
        // $ct = $date->format('H:i:s');

        $d = date('Y-m-d H:i:s');
        $date = new \DateTime($d);

        if (Auth::check()) {
            $date->setTimezone(new \DateTimeZone(Auth::user()->timezone));
        } else {
            $date->setTimezone(new \DateTimeZone('America/New_York'));
        }

//        $cd = $date->format(getDateFormat());
//        $ct = $date->format(getTimeFormat());

        $cd = $date->format('F jS, Y');
        $ct = $date->format('h:i A');
        $telesale = Telesales::find($lead_id);
        $form_id = $telesale->form_id;
        $client_id = $telesale->client_id;
        $salesCenter = (isset($telesale->user) && isset($telesale->user->salescenter)) ? $telesale->user->salescenter->name : '';
        $salesCenterLocation = (isset($telesale->user) && isset($telesale->user->location)) ? $telesale->user->location->name : '';

        if($client_id == config('constants.CLIENT_BOLT_ENEGRY_CLIENT_ID')){
            if(!empty($form_id)){
                $form = Clientsforms::findorFail($form_id);
               
                if(!empty($form)){
                    $address = $form->fields()->where(function ($q) {
                        $q->where('type', '=', 'address')
                        ->orWhere('type', '=', 'service_and_billing_address');
                    })
                    ->where('is_primary', '=', '1')
                    ->with(['telesalesData' => function ($query) use ($telesale) {
                        $query->where(function ($qu) {
                                $qu->where('meta_key', '=', 'zipcode')
                                ->orWhere('meta_key', '=', 'service_zipcode');
                            })
                        ->where('telesale_id', $telesale->id);
                    }])
                    ->first(); 
                    $address = $address->telesalesData->toArray();
                   
                    if(!empty($address)){
                        $zipcode = $address[0]['meta_value'];
                        $state = Zipcodes::where('zipcode',$zipcode)->pluck('state')->first();
                        if($state == "CA"){
                            $date->setTimezone(new \DateTimeZone('America/Los_Angeles')); 
                            $cd = $date->format('F jS, Y');
                            $ct = $date->format('h:i A');
                        }
                        if($state == "IN"){
                            $date->setTimezone(new \DateTimeZone('America/Toronto')); 
                            $cd = $date->format('F jS, Y');
                            $ct = $date->format('h:i A');
                        }
                    }
                }
            }
        }
        $options_to_fill = array(
            "[Tpvagent]" => $this->highlight_tag($tpv_agent_name),
            "[Date]" => $this->highlight_tag($cd),
            "[Time]" => $this->highlight_tag($ct),
            "[Lead Id]" => $this->highlight_tag($telesale->refrence_id),
            "[Verification Code]" => $this->highlight_tag($telesale->verification_number),
            "[Channel]" => $this->highlight_tag($telesale->type),
            "[Sales Center]" => $this->highlight_tag($salesCenter),
            "[Sales Center Location]" => $this->highlight_tag($salesCenterLocation),
        );

        $programs = $telesale->programs()->withTrashed()->get();

        if (count($programs)) {
            /* get enable custom fields for program */
            $customFields =  getEnableCustomFields($telesale->client_id);

            foreach ($programs as $program) {
                $utility = $program->utility;
                $brandContacts = $utility->brandContacts;
                $options_to_fill["[Brand -> " . $utility->utilityCommodity->name . "]"] = $this->highlight_tag($brandContacts->name);
                $options_to_fill["[Brand Contact -> " . $utility->utilityCommodity->name . "]"] = $this->highlight_tag($brandContacts->contact);
                $options_to_fill["[Rate -> " . $utility->utilityCommodity->name . "]"] = $this->highlight_tag($program->rate);
                $options_to_fill["[Rate In Cent -> " . $utility->utilityCommodity->name . "]"] = $this->highlight_tag((is_numeric($program->rate)) ? ($program->rate*config('constants.RATE_IN_CENT')): $program->rate);
                $options_to_fill["[Rate In Text -> " . $utility->utilityCommodity->name . "]"] = $this->highlight_tag((is_numeric($program->rate)) ? getRateText($program->rate) : $program->rate);
                $options_to_fill["[Term -> " . $utility->utilityCommodity->name . "]"] = $this->highlight_tag($program->term);
                $options_to_fill["[MSF -> " . $utility->utilityCommodity->name . "]"] = $this->highlight_tag($program->msf);
                $options_to_fill["[MSF In Text -> " . $utility->utilityCommodity->name . "]"] = $this->highlight_tag((is_numeric($program->msf)) ? getRateText($program->msf) : $program->msf);
                $options_to_fill["[ETF -> " . $utility->utilityCommodity->name . "]"] = $this->highlight_tag($program->etf);
                $options_to_fill["[ETF In Text -> " . $utility->utilityCommodity->name . "]"] = $this->highlight_tag((is_numeric($program->etf)) ? getRateText($program->etf) : $program->etf);
                $options_to_fill["[Utility -> " . $utility->utilityCommodity->name . "]"] = $this->highlight_tag($utility->fullname);
                $options_to_fill["[Utility Abbreviation -> " . $utility->utilityCommodity->name . "]"] = $this->highlight_tag($utility->market);
                $options_to_fill["[Program -> " . $utility->utilityCommodity->name . "]"] = $this->highlight_tag($program->name);
                $options_to_fill["[Plan Name -> " . $utility->utilityCommodity->name . "]"] = $this->highlight_tag($program->name);
                $options_to_fill["[Program Code -> " . $utility->utilityCommodity->name . "]"] = $this->highlight_tag($program->code);
                $options_to_fill["[Account Number Type -> " . $utility->utilityCommodity->name . "]"] = $this->highlight_tag($utility->act_num_verbiage);

                // for add tag of custom field 
                foreach ($customFields as $key => $customField) {
                    $options_to_fill["[".ucwords($customField)." -> " . $utility->utilityCommodity->name . "]"] = $this->highlight_tag($program[$key]);
                }

                $units = $utility->utilityCommodity->units()->pluck('unit')->toArray();
                // $options_to_fill["[Unit -> " . $utility->commodity . "]"] = $this->highlight_tag(implode(", ", $units));
                $options_to_fill["[Unit -> " . $utility->utilityCommodity->name . "]"] = $this->highlight_tag($program->unit_of_measure);

                $customerType = (!empty($program->customerType)) ? $program->customerType->name : "";
                $options_to_fill["[Customer Type -> " . $utility->utilityCommodity->name . "]"] = $this->highlight_tag($customerType);
            }
        }

        $formsFields = FormField::where('form_id', $form_id)->get();
        foreach ($formsFields as $field) {
            $fieldId = $field->id;
            $fieldValue = Telesalesdata::where('field_id', $field->id)->where('telesale_id', $lead_id)->get();
            if (count($fieldValue) > 0) {
                if (count($fieldValue) === 1) {
                    if ($field->type == 'phone_number') {
                        $phnNum = $fieldValue->first()->meta_value;
                        $phnNum = (strlen($phnNum) >= 11) ? $phnNum : "1" . $phnNum;
                        $phnNum = preg_replace(config()->get('constants.DISPLAY_PHONE_NUMBER_FORMAT'), config()->get('constants.PHONE_NUMBER_REPLACEMENT'), $phnNum);
                        $options_to_fill["[$field->label]"] = $this->highlight_tag($phnNum, $fieldId,'',$telesale->refrence_id);
                    } else {
                        $options_to_fill["[$field->label]"] = $this->highlight_tag($fieldValue->first()->meta_value, $fieldId,'',$telesale->refrence_id);
                    }
                } else {
                    if ($field->type == 'fullname') {
                        $full_name = [];
                        $fullName = [];
                        foreach ($fieldValue as $value) {
                            if($value->meta_key != 'is_primary'){
                                if ($value->meta_key === 'middle_initial') {
                                    $UKey = 'Middle Name';
                                    $full_name[$UKey] = $value->meta_value;
                                    $options_to_fill["[$field->label" . ' -> ' . "$UKey]"] = $this->highlight_tag($value->meta_value, $fieldId,'',$telesale->refrence_id);
                                } else {
                                    $UKey = str_replace('_', ' ', ucwords($value->meta_key, '_'));
                                    $full_name[$UKey] = $value->meta_value;
                                    $options_to_fill["[$field->label" . ' -> ' . "$UKey]"] = $this->highlight_tag($value->meta_value, $fieldId,'',$telesale->refrence_id);
                                }
                            }
                        }
                        $name = '';
                        if (isset($full_name['First Name']) && !empty($full_name['First Name'])) {
                            $name = $full_name['First Name'];
                            $fullName['First Name'] = $full_name['First Name'];
                        }

                        if (isset($full_name['Middle Name']) && !empty($full_name['Middle Name'])) {
                            $name = $name . ' ' . $full_name['Middle Name'];
                            $fullName['Middle Name'] = $full_name['Middle Name'];
                        }

                        if (isset($full_name['Last Name']) && !empty($full_name['Last Name'])) {
                            $name = $name . ' ' . $full_name['Last Name'];
                            $fullName['Last Name'] = $full_name['Last Name'];
                        }
                        // \Log::info("full name in tag replace: " . print_r($fullName, true));
                        // $options_to_fill["[$field->label]"] = $this->highlight_tag($full_name['First Name']. ' '. $full_name['Middle Name']. ' '. $full_name['Last Name']);
                        $options_to_fill["[$field->label]"] = $this->highlight_tag(implode(' ', $fullName), $fieldId,'',$telesale->refrence_id);
                        // \Log::info("options to fill in tag replace: " . print_r($options_to_fill["[$field->label]"], true));
                    } else if ($field->type == 'address') {
                        $SAddress = [];
                        foreach ($fieldValue as $value) {
                            $UKey = str_replace('_', '', ucwords($value->meta_key, '_'));
                            $SAddress[$UKey] = $value->meta_value;
                            if($UKey == 'Address1'){
                                $UKey = 'AddressLine1';
                            }
                            if($UKey == 'Address2'){
                                $UKey = 'AddressLine2';
                            }
                            $options_to_fill["[$field->label" . ' -> ' . "$UKey]"] = $this->highlight_tag($value->meta_value, $fieldId,'address',$telesale->refrence_id);
                        }
                        // $options_to_fill["[$field->label]"] = $this->highlight_tag($SAddress['Unit']. ', '.$SAddress['Address1']. ', '.$SAddress['Address2']. ', '.$SAddress['City']. ', '.$SAddress['Country'].', '.$SAddress['Zipcode']);
                        $address = '';
                        if (isset($SAddress['Address1']) && !empty($SAddress['Address1'])) {
                            $address = $SAddress['Address1'];
                        }
                        if (isset($SAddress['Unit']) && !empty($SAddress['Unit'])) {
                            $address = $address . ', ' . $SAddress['Unit'];
                        }
                        if (isset($SAddress['Address2']) && !empty($SAddress['Address2'])) {
                            $address = $address . ', ' . $SAddress['Address2'];
                        }
                        if (isset($SAddress['City']) && !empty($SAddress['City'])) {
                            $address = $address . ', ' . $SAddress['City'];
                        }
                        if (isset($SAddress['County']) && !empty($SAddress['County'])) {
                            $address = $address . ', ' . $SAddress['County'];
                        }
                        if (isset($SAddress['State']) && !empty($SAddress['State'])) {
                            $address = $address . ', ' . $SAddress['State'];
                        }
                        if (isset($SAddress['Zipcode']) && !empty($SAddress['Zipcode'])) {
                            $address = $address . ', ' . $SAddress['Zipcode'];
                        }
                        if (isset($SAddress['Country']) && !empty($SAddress['Country'])) {
                            $address = $address . ', ' . $SAddress['Country'];
                        }
                        $options_to_fill["[$field->label]"] = $this->highlight_tag($address, $fieldId,'address',$telesale->refrence_id);
                    } else if ($field->type == 'service_and_billing_address') {
                        $SAddress = [];
                        $service = config('constants.ADDRESS_TYPE.SERVICE');
                        $billing = config('constants.ADDRESS_TYPE.BILLING');
                        foreach ($fieldValue as $value) {
                            if (explode('_', $value->meta_key)[0] == 'service') {
                                $BUKey = str_replace('_', '', ucwords($value->meta_key, '_'));
                                $SAddress[$BUKey] = $value->meta_value;
                                if($BUKey == 'ServiceAddress1'){
                                    $BUKey = 'ServiceAddressLine1';
                                }
                                if($BUKey == 'ServiceAddress2'){
                                    $BUKey = 'ServiceAddressLine2';
                                }
                                $options_to_fill["[$field->label" . ' -> ' . "$BUKey]"] = $this->highlight_tag($value->meta_value, $fieldId, $service,$telesale->refrence_id);
                            } else {
                                $BUKey = str_replace('_', '', ucwords($value->meta_key, '_'));
                                $BAddress[$BUKey] = $value->meta_value;
                                if($BUKey == 'BillingAddress1'){
                                    $BUKey = 'BillingAddressLine1';
                                }
                                if($BUKey == 'BillingAddress2'){
                                    $BUKey = 'BillingAddressLine2';
                                }
                                $options_to_fill["[$field->label" . ' -> ' . "$BUKey]"] = $this->highlight_tag($value->meta_value, $fieldId, $billing,$telesale->refrence_id);
                            }
                        }
                        $address = '';
                        if (isset($SAddress['ServiceAddress1']) && !empty($SAddress['ServiceAddress1'])) {
                            $address = $SAddress['ServiceAddress1'];
                        }
                        if (isset($SAddress['ServiceUnit']) && !empty($SAddress['ServiceUnit'])) {
                            $address = $address . ', ' . $SAddress['ServiceUnit'];
                        }
                        if (isset($SAddress['ServiceAddress2']) && !empty($SAddress['ServiceAddress2'])) {
                            $address = $address . ', ' . $SAddress['ServiceAddress2'];
                        }
                        if (isset($SAddress['ServiceCity']) && !empty($SAddress['ServiceCity'])) {
                            $address = $address . ', ' . $SAddress['ServiceCity'];
                        }
                        if (isset($SAddress['ServiceCounty']) && !empty($SAddress['ServiceCounty'])) {
                            $address = $address . ', ' . $SAddress['ServiceCounty'];
                        }
                        if (isset($SAddress['ServiceState']) && !empty($SAddress['ServiceState'])) {
                            $address = $address . ', ' . $SAddress['ServiceState'];
                        }
                        if (isset($SAddress['ServiceZipcode']) && !empty($SAddress['ServiceZipcode'])) {
                            $address = $address . ', ' . $SAddress['ServiceZipcode'];
                        }
                        if (isset($SAddress['ServiceCountry']) && !empty($SAddress['ServiceCountry'])) {
                            $address = $address . ', ' . $SAddress['ServiceCountry'];
                        }
                        //$options_to_fill["[$field->label". ' -> Service Address'."]"] = $this->highlight_tag($SAddress['ServiceUnit']. ', '.$SAddress['ServiceAddress1']. ', '.$SAddress['ServiceAddress2']. ', '.$SAddress['ServiceCity']. ', '.$SAddress['ServiceCountry'].', '.$SAddress['ServiceZipcode']);
                        //$options_to_fill["[$field->label". ' -> Billing Address'."]"] = $this->highlight_tag($SAddress['BillingUnit']. ', '.$SAddress['BillingAddress1']. ', '.$SAddress['BillingAddress2']. ', '.$SAddress['BillingCity']. ', '.$SAddress['BillingCountry'].', '.$SAddress['BillingZipcode']);
                        $options_to_fill["[$field->label" . ' -> Service Address' . "]"] = $this->highlight_tag($address, $fieldId, $service,$telesale->refrence_id);
                        $options_to_fill["[$field->label". "]"] = $this->highlight_tag($address, $fieldId, $service,$telesale->refrence_id);
                        $address = '';
                        if (isset($BAddress['BillingAddress1']) && !empty($BAddress['BillingAddress1'])) {
                            $address = $BAddress['BillingAddress1'];
                        }
                        if (isset($BAddress['BillingUnit']) && !empty($BAddress['BillingUnit'])) {
                            $address = $address . ', ' . $BAddress['BillingUnit'];
                        }
                        if (isset($BAddress['BillingAddress2']) && !empty($BAddress['BillingAddress2'])) {
                            $address = $address . ', ' . $BAddress['BillingAddress2'];
                        }
                        if (isset($BAddress['BillingCity']) && !empty($BAddress['BillingCity'])) {
                            $address = $address . ', ' . $BAddress['BillingCity'];
                        }
                        if (isset($BAddress['BillingCounty']) && !empty($BAddress['BillingCounty'])) {
                            $address = $address . ', ' . $BAddress['BillingCounty'];
                        }
                        if (isset($BAddress['BillingState']) && !empty($BAddress['BillingState'])) {
                            $address = $address . ', ' . $BAddress['BillingState'];
                        }
                        if (isset($BAddress['BillingZipcode']) && !empty($BAddress['BillingZipcode'])) {
                            $address = $address . ', ' . $BAddress['BillingZipcode'];
                        }
                        if (isset($BAddress['BillingCountry']) && !empty($BAddress['BillingCountry'])) {
                            $address = $address . ', ' . $BAddress['BillingCountry'];
                        }
                        $options_to_fill["[$field->label" . ' -> Billing Address' . "]"] = $this->highlight_tag($address, $fieldId, $billing,$telesale->refrence_id);
                        // $options_to_fill["[$field->label". "]"] = $this->highlight_tag($address);
                    } elseif ($field->type == 'phone_number') {
                        $phnNum = $fieldValue->first()->meta_value;
                        $phnNum = (strlen($phnNum) >= 11) ? $phnNum : "1" . $phnNum;
                        $phnNum = preg_replace(config()->get('constants.DISPLAY_PHONE_NUMBER_FORMAT'), config()->get('constants.PHONE_NUMBER_REPLACEMENT'), $phnNum);
                        $options_to_fill["[$field->label]"] = $this->highlight_tag($phnNum, $fieldId,'',$telesale->refrence_id);
                    } elseif ($field->type == 'email') {
                        $options_to_fill["[$field->label]"] = $this->highlight_tag($fieldValue->first()->meta_value, $fieldId,'',$telesale->refrence_id);
                    }
                    
                    else {
                        \Log::debug('Fieldvalues:' . $fieldValue);
                        foreach ($fieldValue as $value) {
                            $UKey = str_replace('_', '', ucwords($value->meta_key, '_'));
                            $options_to_fill["[$field->label" . ' -> ' . "$UKey]"] = $this->highlight_tag($value->meta_value, $fieldId,'',$telesale->refrence_id);
                        }
                    }
                }
            }
        }       
        $allTags = (new FormsController)->getAllTags($form_id);
        $allTags = $allTags->original['data'];
        foreach($options_to_fill as $key => $val)
        {
            if (($key = array_search(strtoupper($key), $allTags)) !== false) {
                unset($allTags[$key]);
            }
        }
        foreach($allTags as $k => $v)
        {
            $options_to_fill[$v] = $this->highlight_tag('');      
        }
        // \Log::info($options_to_fill);
        $updateArray = [];

        foreach ($options_to_fill as $key => $value) {
            $updateArray[strtoupper($key)] = $value;
        }

        return $updateArray;
    }

    public function dispositions(Request $request)
    {
        try {
        $queId = $request->get('queId');
        $identityQuestionDecline = $request->get('identity_question_decline');
        if ($request->has('queId') && $request->get('queId') > 0) {
            $question = ScriptQuestions::find($request->get('queId'));
            if (!empty($question)) {
                $client = Client::find(array_get($question, 'client_id'));
                if (!empty($client)) {
                    $dispositions = $client->dispositions()->where('status', 'active')->get();
                    return response()->json(['status' => 'success', 'message' => 'success', 'html' => view('frontend.tpvagent.dispositions', compact('dispositions', 'queId', 'identityQuestionDecline'))->render()], 200);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Client not found for question.', 'html' => ''], 400);
                }
            }
        } elseif ($request->get('queId') == 0 && $request->get('reference_id') != '') {
            $sale_info = (new Telesales)->getLeadInfo($request->reference_id);
            if (!empty($sale_info)) {
                $client = Client::find(array_get($sale_info, 'client_id'));
                if (!empty($client)) {
                    $dispositions = $client->dispositions()->where('status', 'active')->get();
                    return response()->json(['status' => 'success', 'message' => 'success', 'html' => view('frontend.tpvagent.dispositions', compact('dispositions', 'queId', 'identityQuestionDecline'))->render()], 200);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Client not found for question.', 'html' => ''], 400);
                }
            }

        } else {
            return response()->json(['status' => 'error', 'message' => 'Please pass all required parameters.', 'html' => ''], 422);
        }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong while retrieving questions !!', 'html' => ''], 500);
        }
    }

    /**
     * This function is used to decline lead
     */
    public function leadDecline(Request $request)
    {

        try {
            $reviewedby_id = 1;
            if (Auth::check()) {
                $reviewedby_id = Auth::user()->id;
            }
            \Log::debug("Call Type from config file: " . config()->get('constants.OUTBOUND_CALL_TYPE'));
            \Log::debug("Call Type from request params: " . $request->get('call_type'));

            if ($request->has('reference_id') && $request->has('disposition_id')) {
                if ($request->get('reference_id') != '') {
                    $leadBeforeUpdate = Telesales::where('refrence_id', $request->get('reference_id'))->first();

                    $statusArray = [config('constants.LEAD_TYPE_CANCELED'),config('constants.LEAD_TYPE_EXPIRED')];
                    if (in_array(array_get($leadBeforeUpdate,'status'),$statusArray)) {
                        info("The lead has already expired. Lead Id: ".$leadBeforeUpdate->id);
                        return response()->json(['status' => 'success', 'message' => 'The lead has already expired.'], 200);
                    }

                    if ($request->get('call_type') == config()->get('constants.OUTBOUND_CALL_TYPE')) {
                        $verification_method = config()->get('constants.TPV_NOW_OUTBOUND_METHOD');
                    } else {
                        $verification_method = ($request->verification_method == 'customer_call_in_verification') ? config('constants.VERIFICATION_METHOD.CUSTOMER_INBOUND') : config('constants.VERIFICATION_METHOD.AGENT_INBOUND');
                    }

                    //Retrive twilio calls for update lead status and disposition id
                    $twilioCallsDetails = TwilioLeadCallDetails::where('task_id',$request->taskId)->first();
                    \Log::info("Task Id : ".$request->taskId);
                    if ($request->has('call_dropped') && $request->get('call_dropped') == 'yes') {
                        $statusUpdated = "hangup";
                        $update_data = array(
                            'disposition_id' => $request->disposition_id,
                            'reviewed_by' => $reviewedby_id,
                            'status' => $statusUpdated,
                            'reviewed_at' => date('Y-m-d H:i:s'),
                            'language' => $request->current_language,
                            'verification_method' => $verification_method,
                        );
                        if(!empty($twilioCallsDetails)){
                            $twilioCallsDetails->lead_status = $statusUpdated;
                            $twilioCallsDetails->disposition_id = $request->disposition_id;
                            $twilioCallsDetails->save();
                        }

                    } else {
                        $statusUpdated = "decline";
                        $update_data = array(
                            'disposition_id' => $request->disposition_id,
                            'reviewed_by' => $reviewedby_id,
                            'status' => $statusUpdated,
                            'reviewed_at' => date('Y-m-d H:i:s'),
                            'language' => $request->current_language,
                            'verification_method' => $verification_method,
                        );
                        if(!empty($twilioCallsDetails)){
                            $twilioCallsDetails->lead_status = $statusUpdated;
                            $twilioCallsDetails->disposition_id = $request->disposition_id;
                            $twilioCallsDetails->save();
                        }
                    }
                    
                    \Log::info('lead status and disposition updated in twilio call details table');
                    \Log::info($update_data);

                    (new Telesales)->updatesale($request->get('reference_id'), $update_data);
                    //check if this lead has child leads or not
                    $leadId = Telesales::where('refrence_id',$request->get('reference_id'))->first();
                    $isChildExist = (new Telesales())->getChildLeads($leadId->id);
                    
                    if(isset($isChildExist) && $isChildExist->count() > 0){
                        //update child leads 
                        foreach($isChildExist as $key => $val){
                            (new Telesales())->updateChildLeads($val->id,$update_data);
                            \Log::info('Child lead details are successfully updated with lead id '.$val->id);
                        }
                    }
					
					//for Sunrise outbound on disconnect
					// if($leadBeforeUpdate->client_id == config()->get('constants.CLIENT_SUNRISE_CLIENT_ID') && $request->has('call_dropped') && $request->get('call_dropped') == 'yes' && $request->call_type == 'inbound'){
 
                        if(isOnSettings($leadBeforeUpdate->client_id, 'is_outbound_disconnect') && $request->has('call_dropped') && $request->get('call_dropped') == 'yes' && $request->call_type == 'inbound'){
                        $scheduleCall = TelesaleScheduleCall::where('telesale_id', $leadBeforeUpdate->id)->orderBy('attempt_no', 'desc')->first();
                        $callDelay = getSettingValue($leadBeforeUpdate->client_id,'outbound_disconnect_schedule_call_delay',null);
                        $callDelay = !empty($callDelay) ? (explode(",",$callDelay)) : 0;            
                        if($scheduleCall){
                            $attemptNo = $scheduleCall->attempt_no;
                            
                           // if ($attemptNo >= config('constants.MAX_RESCHEDULE_CALL_SUNRISE')) {
                            if ($attemptNo >= getSettingValue($leadBeforeUpdate->client_id,'outbound_disconnect_max_reschedule_call_attempt',null)) {
                                \Log::error("Call with id " . array_get($scheduleCall, 'id') . " already resheduled for " . $attemptNo . " times. So, Can not reshedule call more than " . config('constants.MAX_RESCHEDULE_CALL_SUNRISE') . " time");
                            } else {
                                //$callDelay = config('constants.SCHEDULE_CALL_DELAY_SUNRISE');
                                $nextScheduleTime = isset($callDelay[$attemptNo]) ? $callDelay[$attemptNo] : 2;
                                TelesaleScheduleCall::create([
                                    "telesale_id" => $leadBeforeUpdate->id,
                                    "call_immediately" => array_get($scheduleCall, 'call_immediately'),
                                    "call_time" => date('Y-m-d H:i:s', strtotime('+'.$nextScheduleTime.' minute')),
                                    "call_lang" => array_get($scheduleCall, 'call_lang'),
                                    "attempt_no" => $attemptNo++,
                                    "call_type" => config('constants.SCHEDULE_CALL_TYPE_OUTBOUND_DISCONNECT'),
                                    "schedule_status" => config('constants.SCHEDULE_PENDING_STATUS')
                                  ]);
                            }
                        }else{
                            $telesaleScheduleCall = new TelesaleScheduleCall();
                            $telesaleScheduleCall->telesale_id = $leadBeforeUpdate->id;
                            $telesaleScheduleCall->call_immediately = "yes";
                            $telesaleScheduleCall->call_time = date('Y-m-d H:i:s', strtotime('+'.$callDelay[0].' minute'));
                            $telesaleScheduleCall->call_lang = $request->current_language;
                            $telesaleScheduleCall->call_type = config('constants.SCHEDULE_CALL_TYPE_OUTBOUND_DISCONNECT');
                            $telesaleScheduleCall->attempt_no = 1;
                            $telesaleScheduleCall->disposition = $request->disposition_id;
                            $telesaleScheduleCall->schedule_status = "pending";
                            $telesaleScheduleCall->save();
                            
                        }
					}

                    //Send updated lead status track to segment
                    if (array_get($leadBeforeUpdate, 'status') != $statusUpdated) {
                      $lead = Telesales::find(array_get($leadBeforeUpdate, 'id'));
                      $this->segmentService->createLeadStatusUpdatedTrack($lead, array_get($leadBeforeUpdate, 'status'), array_get($lead, 'status'));
                    }
                    \Log::info($request->get("call_type"));
                    if ($request->has("call_type") && ($request->get("call_type") == config()->get('constants.OUTBOUND_CALL_TYPE') || $request->get("call_type") == config()->get('constants.SCHEDULE_CALL_TYPE_OUTBOUND_DISCONNECT') || $request->get("call_type") == config()->get('constants.TWILIO_CALL_TYPE_SELFVERIFIED_CALLBACK'))) {
                        /* Perform operations on schedule call table after lead decline or disconnects */
                      $this->postScheduleCallHandler($request->get('reference_id'), $request->disposition_id);
                    }

                    $lead = (new Telesales)->getLeadInfo($request->reference_id);
                    if(!empty($lead)) {
                        if ($request->has("call_type") && $request->get("call_type") == config()->get('constants.INBOUND_CALL_TYPE')) {
                            $user_type = config('constants.USER_TYPE_CRITICAL_LOGS.1');
                            $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Non-critical');

                            $disposition = Dispositions::find($request->disposition_id);
                            $disp = '';
                            if(!empty($disposition)) {
                                $disp = $disposition->description;
                            }

                            if (array_get($disposition, 'email_alert') == 1) {
                                /* Send alerts to all registered email adderss for disposition */
                                $this->sendDispositionMail($disposition, $lead);
                            }

                            if($verification_method == '1') {
                                $salesAgentId = null;
                                if ($request->has('call_dropped') && $request->get('call_dropped') == 'yes') {
                                    $isDeclined = false;
                                    $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Disconnected');
                                    $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_17');
                                    $message = __('critical_logs.messages.Event_Type_17',['disposition'=>$disp]);
                                } else {
                                    $isDeclined = true;
                                    $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Declined');
                                    $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_28');
                                    $message = __('critical_logs.messages.Event_Type_19',['disposition'=>$disp]);
                                }
                            } else {
                                $salesAgentId = $lead->user_id;

                                if ($request->has('call_dropped') && $request->get('call_dropped') == 'yes') {
                                    $isDeclined = false;
                                    $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Disconnected');
                                    $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_28');
                                    $message = __('critical_logs.messages.Event_Type_28',['disposition'=>$disp]);
                                } else {
                                    $isDeclined = true;
                                    $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Declined');
                                    $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_27');
                                    $message = __('critical_logs.messages.Event_Type_27',['disposition'=>$disp]);
                                }
                            }

                            (new CriticalLogsHistory)->createCriticalLogs($salesAgentId,$message,$lead->id,null,null,$lead_status,$event_type,$error_type,$user_type,$reviewedby_id);

                            if ($isDeclined) {
                                //Register logs for self verification link expire if leads get declined
                                $this->registerLogsForSelfVerificationExpire($lead);
                            }
                        }
                    }
                }
                $sale_info = (new Telesales)->getLeadInfo($request->reference_id);
                $language = "English";
                if ($request->current_language == 'es') {
                    $language = "Spanish";
                }
                $check_language = (new Telesalesdata)->leadMetakeyData($sale_info->id, 'Language');
                if (!$check_language) {
                    $single_lead_Data = array(
                        'telesale_id' => $sale_info->id,
                        'meta_key' => 'Language',
                        'meta_value' => $language,
                    );
                    (new Telesalesdata)->createLeadDetail($single_lead_Data);
                }

                $tags = $this->getTagToReplaceForQuestions($sale_info->id, $sale_info->form_id);
                if ($request->get('call_dropped') != 'yes') {

                    $script_data = FormScripts::select('id')->where('client_id', $sale_info->client_id)->where('scriptfor', 'after_lead_decline')->where('form_id', 0)->where('language', $request->get('current_language'))->first();

                    if (!empty($script_data) && array_get($script_data, 'id')) {
                        $questions = ScriptQuestions::where('script_id', $script_data->id)->where('client_id', $sale_info->client_id)->orderBy('id', 'desc')->first();
                    }

                    return response()->json(['status' => 'success', 'message' => 'success', 'html' => view('frontend.tpvagent.declinedcall', compact('questions', 'tags'))->render()], 200);
                } else {
                    $questions = array();
                    return response()->json(['status' => 'success', 'message' => 'success', 'html' => view('frontend.tpvagent.declinedcall', compact('questions', 'tags'))->render()], 200);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'Please pass all required parameters !!', 'html' => ''], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'html' => ''], 500);
        }
    }

    public function conformDecline(Request $request)
    {
        try {
            $reviewedby_id = 1;
            if (Auth::check()) {
                $reviewedby_id = Auth::user()->id;
            }
                if ($request->get('reference_id') != '') {
                    $verification_method = (($request->verification_method == 'customer_call_in_verification') ? config('constants.VERIFICATION_METHOD.CUSTOMER_INBOUND') : config('constants.VERIFICATION_METHOD.AGENT_INBOUND'));
                        $update_data = array(
                            'reviewed_by' => $reviewedby_id,
                            'status' => 'decline',
                            'reviewed_at' => date('Y-m-d H:i:s'),
                            'verification_method' => $verification_method,
                            'language' => $request->current_language,
                        );

                    $leadBeforeUpdate = Telesales::where('refrence_id', $request->get('reference_id'))->first();
                        \Log::info($update_data);
                    (new Telesales)->updatesale($request->get('reference_id'), $update_data);
                    
                    //Send updated lead status track to segment
                    $lead = Telesales::find(array_get($leadBeforeUpdate, 'id'));
                    $this->segmentService->createLeadStatusUpdatedTrack($lead, array_get($leadBeforeUpdate, 'status'), array_get($lead, 'status'));
                    
                    $tags = $this->getTagToReplaceForQuestions($lead->id, $lead->form_id);
                    $formScript = FormScripts::select('id')->where('client_id', $lead->client_id)->where('scriptfor', 'after_lead_decline')->where('form_id', 0)->where('language', $request->get('current_language'))->first();
                    \Log::debug("Confirm decline: " . print_r($formScript, true));
                    if (!empty($formScript) && array_get($formScript, 'id')) {
                        $questions = ScriptQuestions::where('script_id', $formScript->id)->where('client_id', $lead->client_id)->orderBy('id', 'desc')->first();
                        \Log::debug("Confirm decline: " . print_r($questions, true));
                    }

                    return response()->json(['status' => 'success', 'message' => 'Lead Decline successfully.', 'html' => view('frontend.tpvagent.declinedcall', compact('questions', 'tags'))->render()], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Please pass all required parameters !!'], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'html' => ''], 500);
        }
    }


    /*
     @Author : Ritesh Rana
     @Desc   : get question for customer data .
     @Input  :
     @Output : Illuminate\Http\Response
     @Date   : 04/02/2020
     */
    public function customerQuestions(Request $request)
    {
        $workspace_id = $request->workspace_id;
        $workflow_id = $request->workflow_id;
        $language = $request->language;
        $selected_script = $request->selected_script;
        \Log::info($workspace_id);
        \Log::info($workflow_id);
        \Log::info($language);

        $scripts = (new ScriptQuestions)->getScripts($workspace_id, $workflow_id, $language, 'customer_call_in_verification');

        \Log::info($scripts);
        if (count($scripts) > 0) {
            $questions = array();
           $repalce_tag_array = $this->commonTagReplace();
             foreach ($scripts as $script) {
                $get_questions_to_replace_tags = (new ScriptQuestions)->scriptQuestions($script->id);
                if (count($get_questions_to_replace_tags) > 0) {
                    $questions_array = array();
                    foreach ($get_questions_to_replace_tags as $single_question) {
//                        $single_question = array_get($single_question, '')
                        $formedSingleQuestion = preg_replace_callback("/\[\s*([^\]]+)\s*\]/", function ($word) {
                            return trim(strtoupper($word[1]));
                        }, array_get($single_question, 'question'));
                        $full_question = strtr($formedSingleQuestion, $repalce_tag_array);

                        $questions_array[] = ['question' => htmlspecialchars_decode($full_question), 'id' => $single_question->id, 'position' => $single_question->position];
                    }
                    $questions[$script->scriptfor] = $questions_array;
                }
                //=   (new ScriptQuestions)->scriptQuestions($script->id);

            }

            $response = array('status' => 'success', 'question' => $questions);

        } else {
            $response = array('status' => 'error', "message" => "No script found.");
        }

        return $response;

    }


    /*
    @Author : Ritesh Rana
    @Desc   : get agent not found question data.
    @Input  :
    @Output : Illuminate\Http\Response
    @Date   : 05/02/2020
    */
    public function agentNotFoundQuestion(Request $request)
    {
        $workspace_id = $request->workspace_id;
        $workflow_id = $request->workflow_id;
        $language = $request->language;
        $selected_script = $request->selected_script;
        \Log::info($workspace_id);
        \Log::info($workflow_id);
        \Log::info($language);

        $scripts = (new ScriptQuestions)->getScripts($workspace_id, $workflow_id, $language, $selected_script);

        \Log::info($scripts);
        if (count($scripts) > 0) {

            $questions = array();
            $repalce_tag_array = $this->getTagsToBeReplaced($scripts[0]->form_id);

            foreach ($scripts as $script) {
                $get_questions_to_replace_tags = (new ScriptQuestions)->scriptQuestions($script->id);
                if (count($get_questions_to_replace_tags) > 0) {
                    $questions_array = array();
                    foreach ($get_questions_to_replace_tags as $single_question) {
                        $full_question = strtr($single_question->question, $repalce_tag_array);
                        $questions_array[] = ['question' => $full_question, 'id' => $single_question->id, 'position' => $single_question->position];
                    }
                    $questions[$script->scriptfor] = $questions_array;
                }
                //=   (new ScriptQuestions)->scriptQuestions($script->id);

            }

            $response = array('status' => 'success', 'question' => $questions);

        } else {
            $response = array('status' => 'error', "message" => "No script found.");
        }

        return $response;

    }


    /*
   @Author : Ritesh Rana
   @Desc   : get lead not found question data.
   @Input  :
   @Output : Illuminate\Http\Response
   @Date   : 01/04/2020
   */
    public function leadNotFoundQuestion(Request $request)
    {
        $workspace_id = $request->workspace_id;
        $workflow_id = $request->workflow_id;
        $language = $request->language;
        $selected_script = $request->selected_script;
        \Log::info($workspace_id);
        \Log::info($workflow_id);
        \Log::info($language);

        $scripts = (new ScriptQuestions)->getScripts($workspace_id, $workflow_id, $language, $selected_script);

        \Log::info($scripts);
        if (count($scripts) > 0) {

            $questions = array();
            $repalce_tag_array = $this->getTagsToBeReplaced($scripts[0]->form_id);

            foreach ($scripts as $script) {
                $get_questions_to_replace_tags = (new ScriptQuestions)->scriptQuestions($script->id);
                if (count($get_questions_to_replace_tags) > 0) {
                    $questions_array = array();
                    foreach ($get_questions_to_replace_tags as $single_question) {
                        $full_question = strtr($single_question->question, $repalce_tag_array);
                        $questions_array[] = ['question' => $full_question, 'id' => $single_question->id, 'position' => $single_question->position];
                    }
                    $questions[$script->scriptfor] = $questions_array;
                }
                //=   (new ScriptQuestions)->scriptQuestions($script->id);

            }

            $response = array('status' => 'success', 'question' => $questions);

        } else {
            $response = array('status' => 'error', "message" => "No script found.");
        }

        return $response;

    }

    /**
     * This function is used to get edit file
     */
    public function getEditFiled(Request $request) {
        $tag = $request->tag;
        $lead_id = $request->lead;

        $lead = Telesales::find($lead_id);

        $field = $this->getFieldFromTag($tag, $lead->form_id);

        if ($field == false) {
            return response(['status' => false, 'message' => 'Field not find']);
        }
        $fieldValues = Telesalesdata::where('field_id', $field->id)->where('telesale_id', $lead_id)->get();

        $values = [];

        foreach ($fieldValues as $value){
            $values[$value->meta_key] = $value->meta_value;
        }


        $view = view('frontend.tpvagent.get_field', ['field' => $field, 'values' => $values, 'lead_id' => $lead_id])->render();

        //return $view;

        return response(['status' => true, 'html' => $view]);
    }

    /**
     * This function is used to save question file
     */
    public function saveFiledQuestion(Request $request) {
        try {
            $field_id = $request->field_id;
            $lead_id = $request->lead_id;

            foreach ($request->field as $key => $value) {
                Telesalesdata::where('field_id', $field_id)->where('telesale_id', $lead_id)->where('meta_key', $key)->update([
                    'meta_value' => is_array($value) ? implode(', ', $value) : $value
                ]);
            }

            return response([
                'status' => true,
                'message' => "Successfully updated."
            ]);
        } catch (\Exception $e) {
            Log::error("Error : ". $e->getMessage());
            return response([
                'status' => false,
                'message' => "Whoops, something went wrong, please try again."
            ]);
        }
    }

    protected function getFieldFromTag($tag, $form_id) {
        $field = FormField::where('label', $tag)->where('form_id', $form_id)->first();
        if (!$field) {
            $tagArray = explode('->', $tag);
            if (count($tagArray) > 0) {
                $tag = trim($tagArray[0]);

                $field = FormField::where('label', $tag)->where('form_id', $form_id)->first();

                if (!$field){
                    return false;
                }
            } else {
                return false;
            }
        }

        return $field;
    }

    /**
     * This function is used to check script question condition
     */
    public function checkScriptQuestionCondition($conditions,$actualQuestion,$single_question,$options_to_fill)
    {
        $compareQuestionId = array_get($single_question, 'id');
        $skipQue = false;
        
        foreach($conditions as $k => $v)
        {
            $skipQue = false;
            if($v->question_id == $compareQuestionId)
            {
                preg_match("/\[\s*([^\]]+)\s*\]/",$actualQuestion,$matches);
                $tagValue = $options_to_fill['['.strtoupper($v->tag).']'];
                preg_match('/.*?<span.*?>(.*?)<\/span>/',$tagValue,$match);
                if($v->comparison_value[0] == '['){
                    $single_question = preg_replace_callback("/\[\s*([^\]]+)\s*\]/", function ($word) {
                        return "[".trim(strtoupper($word[1]))."]";
                        }, $v->comparison_value);
                    $ques = strtr($v->comparison_value, $options_to_fill);
                    
                    $v->comparison_value = (new TPVIVRController())->removeSpan($ques);
                }
               
                switch ($v->operator) {
                    case 'is_equal_to':
                        if($match[1] == $v->comparison_value){
                            $skipQue = false;
                        }
                        else
                            $skipQue = true;
                        break;
                    case 'is_not_equal_to':
                        if($match[1] != $v->comparison_value)
                            $skipQue = false;
                        else
                            $skipQue = true;
                        break;
                    case 'is_greater_than':
                        if($match[1] > $v->comparison_value)
                            $skipQue = false;
                        else
                            $skipQue = true;
                        break;
                    case 'is_less_than':
                        
                        if($match[1] < $v->comparison_value)
                            $skipQue = false;
                        else
                            $skipQue = true;
                        break;
                    case 'exists':
                        if($match[1] != '')
                            $skipQue = false;
                        else
                            $skipQue = true;
                        break;
                    case 'does_not_exists':
                        if($match[1] == '')
                            $skipQue = false;
                        else
                            $skipQue = true;
                        break;
                    case 'string_contains':
                        
                        if(strpos($match[1],$v->comparison_value) !== false)
                            $skipQue = false;
                        else
                            $skipQue = true;
                        break;
                    case 'string_does_not_contains':
                        if(strpos($match[1],$v->comparison_value) === false)
                            $skipQue = false;
                        else
                            $skipQue = true;
                        break;
                    case 'matches_regex':
                        if(preg_match("/".$v->comparison_value."/",$match[1]))
                            $skipQue = false;
                        else
                            $skipQue = true;
                        break;
                }
                if($skipQue === true)
                {
                    break;
                }
            }
            else{
                \Log::info('No question id match.');
            }
        }
        return $skipQue;
    }

    /**
     * This function is used to check empty tagquestion
     */
    public function checkEmptyTagQuestion($checkEmptyTag)
    {
        //This function checks for empty tag if tags are empty then returns ture so that skip that question.
        $emptyFlag = false;
        while(strpos($checkEmptyTag,'<span') > 0)
        {
            $sub = substr($checkEmptyTag,strpos($checkEmptyTag , '<span'));
            $checkEmptyTag = substr($checkEmptyTag,0,strpos($checkEmptyTag , '<span'));
            $pattern = '/.*?<span.*?>(.*?)<\/span>/';
            preg_match($pattern, $sub, $matches);
            if($matches[1] == '')
            {
                $emptyFlag = true;
            }
            else{
                $emptyFlag = false;
                break;
            }
            $end = strpos($sub,'</span>');
            $remaining = substr($sub,$end+7);
            $checkEmptyTag =  $remaining;
        }
        return $emptyFlag;
    }

    public function getQuestionReplaceWithTags($actualQuestion,$options_to_fill)
    {
        
        $actualQuestion = preg_replace_callback("/\[\s*([^\]]+)\s*\]/", function ($word) {
            return "[" . trim(strtoupper($word[1])) . "]";
        }, $actualQuestion);
        $full_question = strtr($actualQuestion, $options_to_fill);

        return $full_question;
    }

    /**
     * This function is used to store hangup details
     */
    public function storeHangupDetails(Request $request)
    {
        $reservationDetails = WorkerReservationDetails::where('reservation_id',$request->reservation_id)->first();
        $reservationDetails->call_hung_up_by = $request->call_hangup;
        $reservationDetails->save();
    }

    //Send Mail to sales agent once lead get verified
    public function sendLeadSuccessMail($leadId) {
      $lead = Telesales::find($leadId);

      if (empty($lead)) {
        \Log::error("Send Lead Success Mail: Lead not found with id: " . $leadId);
      }

      $agent = User::find($lead->user_id);
      if (!empty($agent->email)) {
        Mail::to($agent->email)->send(new NotifySalesAgentForVerifiedLead($lead->refrence_id));
      } else {
        \Log::error("User not found with id: " . $lead->user_id);
      }
      return true;
    }


    public function getTagField(Request $request) {
        try{
            $type =  ['fullname','email','phone_number','textbox','textarea','service_and_billing_address','address','checkbox','radio','selectbox'];
            $field = FormField::whereIn('type',$type)->find($request->field_id);

            if (empty($field)) {                
                return response([
                    'status' => false,
                    'message' => "This tag is not editable."
                ]);
            }
            info("editing tag for field id :".print_r($field,true));
            $leadDetails = (new Telesales())->getLeadId($request->telesale_id);
            $data = Telesalesdata::select('id','meta_key','meta_value')->where('field_id', $field->id)->where('telesale_id', $leadDetails->id);
            $teleSalesData = $data->pluck('meta_value','meta_key');
            $teleSalesDataId = $data->pluck('id','meta_key');

            if ( $field->type == 'service_and_billing_address') {
                $fieldFile = 'tpvagents.form-fields.address';

                $address = [];
                $addressDataId = [];
                foreach ($teleSalesData as $key => $value) {
                    if ($request->address_type == 'service') {
                        $addressKey = trim(str_replace("service_","",$key)); 
                    } else {
                        $addressKey = trim(str_replace("billing_","",$key));
                    }
                    $address[$addressKey] = $value;                       
                }

                foreach ($teleSalesDataId as $key => $value) {
                    if ($request->address_type == 'service') {
                        $addressKey = trim(str_replace("service_","",$key)); 
                    } else {
                        $addressKey = trim(str_replace("billing_","",$key));
                    }
                    $addressDataId[$addressKey] = $value;
                }
                
                $teleSalesData = $address;
                $teleSalesDataId = $addressDataId;
            } else {
                $fieldFile = 'tpvagents.form-fields.'.$field->type;
            }

            $data= view($fieldFile,compact('field','teleSalesData','teleSalesDataId'))->render();

            return response([
                'status' => true,
                'data' => $data
            ]);
        }catch(\Exception $e) {
            Log::error('Error while getting tag field: '.$e);
            return response([
                'status' => false,
                'message' => "Something went wrong, please try again."
            ]);
        }
    }

    public function updateTagField(Request $request) {
        try{
            $inputs = $request->except('_token');
            Log::info('Update Field');
            Log::info($inputs);
            foreach ($inputs as $key => $value) {
                $id = trim(substr($key,7));
                info($id);
                if (is_array($value)) {
                    $value = implode(',', $value);
                }
                Telesalesdata::where('id',$id)->update(['meta_value' => $value ]);
            }
            return response([
                'status' => true,
                'message' => "Value updated successfully."
            ]);
        }catch(\Exception $e) {
            Log::error('Error while updating tag field: '.$e);
            return response([
                'status' => false,
                'message' => "Something went wrong, please try again."
            ]);
        }
    }


    //for check tpv attempts alerts
    public function checkAlertTeleD2d($telesale)
    {
        \Log::info('Current Lead Attempt is: '.$telesale->tpv_attempts);
       $salesAgent = (new Salesagentdetail())->getUserDetail($telesale->user_id);
       $alertName = '';
       $alertMaxCountName = '';
       if($salesAgent->agent_type == 'tele'){
           $alertName = 'is_enable_alert10_tele';
           $alertMaxCountName = 'max_times_alert10_tele';
        
       }else if($salesAgent->agent_type == 'd2d'){
           $alertName = 'is_enable_alert11_d2d';
           $alertMaxCountName = 'max_times_alert11_d2d';
       }
        //Stores tpv now attempts for given lead id for alert
        if(isOnSettings($telesale->client_id, $alertName,false)){
            (new Telesales)->storeTpvNowAttempts($telesale->id,($telesale->tpv_attempts+1));
            $updatedLeadObj = Telesales::find($telesale->id);
            //Dispatch queue for checking tpv now alert and sending a mail
            CheckTPVAlerts::dispatch($updatedLeadObj->id,$updatedLeadObj->tpv_attempts,$alertMaxCountName);
        }
        else{
            \Log::info("alert is switched off for type ".$salesAgent->agent_type);
        }
    }

}

