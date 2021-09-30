<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\models\Client;
use App\models\Clientsforms;
use App\models\Telesales;
use App\models\UserTwilioId;
use App\models\Zipcodes;
use App\models\TelesalesZipcode;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;
use Storage;
use App\Services\StorageService;
use App\models\CriticalLogsHistory;

class UserController extends Controller
{
    use \App\Traits\LeadTrait;
    public function __construct()
    {
        $this->storageService = new StorageService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index()
    {        
        if(auth()->user()->access_level == 'salesagent' && auth()->user()->roles->isEmpty()) {
            $pending_leads = (new Telesales)->getUserLeadsCount(Auth::user()->id, 'pending');
            $decline_leads = (new Telesales)->getUserLeadsCount(Auth::user()->id, 'decline');
            $verified_leads = (new Telesales)->getUserLeadsCount(Auth::user()->id, 'verified');
            $hanged_leads = (new Telesales)->getUserLeadsCount(Auth::user()->id, 'hangup');
            $cancel_leads = (new Telesales)->getUserLeadsCount(Auth::user()->id, 'cancel');
            $expired_leads = (new Telesales)->getUserLeadsCount(Auth::user()->id, 'expired');
            $self_verified_leads = (new Telesales)->getUserLeadsCount(Auth::user()->id, 'self-verified');

            return view('frontend.user.myaccount', compact('pending_leads', 'decline_leads', 'verified_leads', 'hanged_leads','cancel_leads','expired_leads','self_verified_leads'));
        }else if(auth()->user()->access_level == 'tpvagent'){
            return redirect()->route('tpvagents.sales');
        }else{
            return redirect()->route('dashboard');
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @return \Illuminate\Http\Response
     */
    public function editprofile()
    {
        $user = (new User)->getUser(Auth::user()->id);
        $client_id = $user->client_id;
        $client_image_url = "";
        if ($client_id > 0) {
            $client = (new Client)->getClientinfo($client_id);
            if ($client) {
                $client_image_url = $client->logo;
            }

        }
        return view('frontend.user.editprofile', compact('user', 'client_image_url'));
    }


    /**
     * This method is used to get salesuser profile
     */
    public function getSalesUserProfile()
    {

        $user_data = User::leftJoin('salescenters', 'salescenters.id', '=', 'users.salescenter_id')
            ->leftJoin('salesagent_detail', 'salesagent_detail.user_id', '=', 'users.id')
            ->leftJoin('salescenterslocations', 'salescenterslocations.id', '=', 'salesagent_detail.location_id')
            ->leftJoin('clients', 'clients.id', '=', 'users.client_id')

            ->where('users.id', Auth::user()->id)
            ->get(['users.*', 'clients.name as client_name','salescenters.name as sales_centers_name','salescenterslocations.name as location_name','salesagent_detail.certified','salesagent_detail.certification_date','salesagent_detail.certification_exp_date']);

        $user=$user_data[0];
        $twilio_ids = (new UserTwilioId)->getTwilioIds(Auth::user()->id);
        return view('frontend.user.salesAgentProfile', compact('user', 'twilio_ids'));
    }


    /**
     * This method is used to update user password
     */
    public function updatePassword(Request $request)
    {
        try {
            $userid = Auth::user()->id;
            /* Start Validation rule */
            $validator = \Validator::make($request->all(), [
                'password' => $request->password != null ? 'min:6' : '',
                'password_confirmation' => 'same:password',
                'timezone'=>'required',
                'file' => 'mimes:jpg,jpeg,png|max:5120',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error',  'errors' => $validator->errors()->messages()], 422);
            }
            /* End Validation rule */
            $input =array();
            $msg='Profile successfully updated.';
            if (!empty($request->password)) {
                $input['password'] = Hash::make($request->password); //update the password
                $msg = 'Your password successfully updated.';
            }
            if(!empty($request->timezone))
            {
                $input['timezone'] = $request->timezone;
                $msg = 'Your timezone was successfully updated.';
            }

            if ($request->hasFile('file')) {
                Storage::disk('s3')->delete($request->old_url);
                $file = $request->file('file');
                $awsFolderPath = config()->get('constants.aws_folder');
                //Used TPVAGENT_PROFILE_PICTURE_UPLOAD_PATH to store sales agent profile picture because as per existing code it storing sales agent profile picture to tpvagent's folder
                $filePath = config()->get('constants.TPVAGENT_PROFILE_PICTURE_UPLOAD_PATH');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $this->storageService->uploadFileToStorage($file, $awsFolderPath, $filePath, $fileName);
                if ($path !== false) {
                    $input['profile_picture'] = $path;
            }
                $msg = 'Your profile photo was successfully updated.';
            }
            if (!empty($request->password) && $request->hasFile('file') && !empty($request->timezone)) {
                $msg = 'Password , Timezone and profile photo successfully updated.';
            }

            if(!empty($input)) {
                $user = (new User)->updateUser($userid, $input);
            }
            session()->put('message', $msg);
            return response()->json(['status' => 'success', 'message' => $msg], 200);
        } catch(\Exception $e) {
            \Log::error('Error while sales agent update profile:-');
            \Log::error($e);
            session()->put('error', $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * This method is used to update user profile
     */
    public function updateprofile(Request $request)
    {
        $userid = Auth::user()->id;
        /* Start Validation rule */
        $this->validate($request, [
            // 'first_name' => 'required|max:255',
            // 'email' => 'required|email|unique:users,email,'.$userid,
            'password' => 'confirmed',
        ]);
        /* End Validation rule */
        $input = $request->only('first_name', 'last_name', 'email');

        if (!empty($request->password)) {
            $input['password'] = Hash::make($request->password); //update the password
        }
        if (!empty($request->title)) {
            $input['title'] = $request->title;
        }
        $user = (new User)->updateUser($userid, $input);

        return redirect()->to(route('editprofile'))
            ->with('success', 'Profile successfully updated.');
    }

    public function myleads(Request $request)
    {   
        if(auth()->user()->access_level == 'salesagent' && auth()->user()->roles->isEmpty()) {
            $leadIds = Telesales::where('user_id',auth()->user()->id)->pluck('id')->toArray();
            $zipCodes = TelesalesZipcode::whereIn('telesale_id',$leadIds)->groupBy('zipcode_id')->pluck('zipcode_id')->toArray();
            $states = Zipcodes::whereIn('id',$zipCodes)->groupBy('state')->get(['state']);
            return view('frontend.user.leads', compact('states'));
        }else if(auth()->user()->access_level == 'tpvagent'){
            return redirect()->route('tpvagents.sales');
        }else{
            return redirect()->route('dashboard');
        }
    }

    public function myLeadsAjax(Request $request)
    {
        $leads = Telesales::select(
            'telesales.*',
            'zip_codes.city',
            'zip_codes.state',
            DB::raw("(select GROUP_CONCAT(commodities.name SEPARATOR ', ') from commodities left join form_commodities on form_commodities.commodity_id = commodities.id where form_commodities.form_id = telesales.form_id) as commodities"),
            DB::raw("(select GROUP_CONCAT(market  SEPARATOR ', ') from utilities where id IN (select utility_id from programs where id IN (select program_id from telesales_programs where telesale_id = telesales.id))) as utility")
        )
            ->leftJoin('telesales_zipcodes', 'telesales.id', '=', 'telesales_zipcodes.telesale_id')
            ->leftJoin('zip_codes', 'zip_codes.id', '=', 'telesales_zipcodes.zipcode_id')
            ->where('telesales.user_id', Auth::user()->id)
            ->orderBy('telesales.id','desc');


        if (isset($request->state) && !empty($request->state)){
            $leads = $leads->where('zip_codes.state', $request->state);
        }

        if (isset($request->status) && !empty($request->status)) {
            $leads = $leads->where('telesales.status', $request->status);
        }

        if (isset($request->date_range) && !empty($request->date_range)) {
            $date = $request->date_range;
            $start = Carbon::parse(explode(' - ', $date)[0])->toDateString();
            $end = Carbon::parse(explode(' - ', $date)[1])->toDateString();
            $leads = $leads->where('telesales.created_at', '>', $start)->where('telesales.created_at', '<=', $end);
        }
        $timeZone = Auth::user()->timezone;
        $search = $request->input('search.value'); 
            $multipleParentId = [];
            if (!empty($search)) {
                $childLead = Telesales::where('refrence_id',$search)->first();
                if(isset($childLead) && !empty($childLead)){
                    $multipleParentId[] = array_get($childLead,'multiple_parent_id');
                    $isParent = $childLead->childLeads()->get();
                    
                    if(isset($isParent) && !empty($isParent)){
                        foreach($isParent as $k => $v){
                            $multipleParentId[] = array_get($v,'id');
                        }
                    }
                }
            }
        return DataTables::of($leads)
            ->editColumn('status', function ($lead) {
                switch ($lead->status) {
                    case 'hangup':
                        return 'Disconnected';
                    case 'cancel':
                        return 'Cancelled';
                    case 'decline':
                        return 'Declined';
                    case 'self-verified':
                        return 'Self verified';
                    default:
                        return ucfirst($lead->status);
                }
            })
            ->editColumn('refrence_id', function($lead){     
                
                $isChild = $lead->childLeads()->get();
                
                $html = 'Associated Leads ' ;
                if(isset($isChild) && $isChild->count() > 0){
                    foreach($isChild as $k =>  $v){
                        $html .= "<br/>".$v->refrence_id ;
                    }
                    
                    return  '<a data-toggle="tooltip" data-placement="top" data-container="body" data-html="true" style="color: black;" data-original-title="'.$html.'">'.$lead->refrence_id.'</a>';
                }
                else{
                    return $lead->refrence_id;
                }
                
            })
            ->addColumn('date', function ($lead) use($timeZone) {
                return Carbon::parse($lead->created_at)->setTimezone($timeZone)->format(getDateFormat());
            })
            ->addColumn('time', function ($lead) use($timeZone){
                
                return Carbon::parse($lead->created_at)->setTimezone($timeZone)->format('h:i:s A');
            })
            ->filterColumn('multiple_parent_id',function($lead,$keyword) use($multipleParentId) {
                return $lead->orWhereIn('telesales.id',$multipleParentId);
            })
            ->addColumn('multiple_parent_id', function($lead){
                $parent_id = '';
                if(!empty($lead->parentLead)) {
                    $parent_id = $lead->parentLead->refrence_id;
                }
                return $parent_id;
            })
            ->editColumn('city', function ($lead) {                
                //return ucfirst(strtolower(array_get($lead,'city')));
                return ucwords(strtolower(array_get($lead,'city')));
            })
            ->addColumn('action', function ($lead) {
                $viewBtn = $deleteBtn = $cloneBtn = '';

                if (isOnSettings($lead->client_id,'is_enable_lead_view_page')) {                    
                    $viewBtn = '<a title="View Lead" href="' . route('profile.leaddetail', $lead->refrence_id) . '" class="btn my-lead-action-btns">' . getimage("images/view.png") . '</a>';
                } else {
                    $viewBtn = getDisabledBtn();
                }

                if ($lead->status == 'pending' || $lead->status == 'decline' || $lead->status == 'expired') {
                    $parentLead = Telesales::find($lead->parent_id);
                    if ($parentLead != null && $parentLead->status == 'decline'){
                        $cloneBtn = getDisabledBtn('clone');
                    } else {
                        $activeForm = Clientsforms::find($lead->form_id);
                        if(isset($activeForm) && !empty($activeForm) && isOnSettings($lead->client_id, 'is_enable_clone_lead'))
                        {
                            $state = '';
                            if ($lead->is_enrollment_by_state) {                                
                                $state = $this->getLeadState($lead->id, $lead->form_id);
                            }
                            $cloneBtn = '<button title="Clone Lead" class="btn my-lead-action-btns clone_lead" data-url="' . route('client.contact.from', ['id' => $lead->client_id, 'form_id' => $lead->form_id, 'lid' => base64_encode($lead->id), 'state'=>$state]) . '" data-refid="' . $lead->refrence_id . '" data-toggle="modal" data-target="#clone_lead"> ' . getimage("images/copy.png") . ' </button>';
                        }
                        else
                        {
                            $cloneBtn = getDisabledBtn('clone');
                        }
                    }
                    
                    if($lead->status == 'pending' ) {
                        $deleteBtn = '<button title="Cancel Lead" class="btn my-lead-action-btns cancel_lead" data-lid="' . $lead->id . '" data-refid="' . $lead->refrence_id . '" data-toggle="modal" data-target="#cancel_lead">' . getimage("images/cancel.png") . '</button>';
                    }else{
                        $deleteBtn = getDisabledBtn('delete');
                    }
                } else {
                    
                    $cloneBtn = getDisabledBtn('clone');
                    $deleteBtn = getDisabledBtn('delete');
                }
                return '<div class="btn-group">' . $viewBtn. $cloneBtn. $deleteBtn . '<div>';
            })
            ->rawColumns(['action', 'date', 'time', 'status','refrence_id'])
            ->make(true);
    }

    /**
     * This method is used to show lead detail
     */
    public function leaddetail($reference_id)
    {
        $lead = (new Telesales)->getLeadID($reference_id, Auth::user()->company_id);
        $form = Clientsforms::withTrashed()->findorFail($lead->form_id);
        
        // for check lead view page settings is on or off
        if (!isOnSettings(array_get($form,'client_id'), 'is_enable_lead_view_page')) {
            return back()->with('error','Lead view page settings is switch off. Please contact your administrator for assistance.');
        }
        
        $telesale_id = $lead->id;
        
        $programs = $lead->programs()->withTrashed()->with('utility')->get();
        $lead_detail = $form->fields()->with(['telesalesData' => function ($query) use ($telesale_id) {
            $query->where('telesale_id', $telesale_id);
        }])->orderBy('position','asc')->get()->toArray();

        $dispositions = $this->getDispositions($telesale_id,$lead->status);
        $verificationCode = '';
        if($lead->status == 'verified'){
            $verificationCode = $lead->verification_number;
        }

        // For get parent Or child lead numbers
        $additionalDetails = Telesales::where('id', $lead->id);
        if ($additionalDetails->first()->is_multiple == '1') {
            $additionalDetails = $additionalDetails->with('childLeads')->first();
        } else {
            $additionalDetails = $additionalDetails->with('parentLead')->first();
        }

        return view('frontend.user.leadDetail', compact('lead_detail', 'reference_id', 'programs','dispositions','verificationCode', 'additionalDetails'));
    }

    public function getrange($range = "")
    {
        $rangedates = array('start_date' => '', 'end_date' => '');
        if ($range != "") {
            if ($range == 'today') {
                $rangedates['end_date'] = date('Y-m-d');
                $rangedates['start_date'] = date('Y-m-d');
            }
            if ($range == 'week') {
                $rangedates['end_date'] = date('Y-m-d');
                $rangedates['start_date'] = date('Y-m-d', strtotime('-6 days'));
            }
            if ($range == 'month') {
                $rangedates['end_date'] = date('Y-m-d');
                $rangedates['start_date'] = date('Y-m-d', strtotime('-1 Month'));
            }

        }
        return $rangedates;
    }

    /**
     * This method is used to deactivate salesagent account
     */
    public function deactivatesalesagentaccount()
    {
        $start_Date = date('Y-m-d', strtotime('-30 days'));
        $end_Date = date('Y-m-d');

        $agents_list = (new User)->getinactivesalesagents($start_Date, $end_Date);
        if (count($agents_list) > 0) {
            $user_ids = array();
            foreach ($agents_list as $single_user) {
                $user_ids[] = $single_user->user_id;
            }
            if (count($user_ids) > 0) {
                (new User)->makeinactive($user_ids);
            }

        }
        echo "done";
    }

    public function getLog(Request $request) {

        if ($request->delete == true) {
            $file = storage_path('logs/laravel.log');
            unlink($file);
        } else {
            $file = storage_path('logs/laravel.log');

            return response()->download($file);
        }
    }

    /**
     * This method is used to get dispositions
     */
    public function getDispositions($id,$status)
    {
        $disp = [];
        if($status == 'cancel')
        {
            $criticalAlerts = CriticalLogsHistory::leftjoin('telesales','telesales.id','=','critical_logs_history.lead_id')
			  ->where('error_type',config()->get('constants.ERROR_TYPE_CRITICAL_LOGS.Critical'))
			->where('telesales.status','cancel')
            ->where('lead_id',$id)->pluck('reason')->toArray();
            $disp = $criticalAlerts;
        }
        elseif(in_array($status,['decline','hangup']))
        {
            $dispositions = Telesales::leftJoin('dispositions', 'dispositions.id', '=', 'telesales.disposition_id')
                ->select('dispositions.description','telesales.status','telesales.cancel_reason')
                ->whereIn('telesales.status',['decline','hangup'])
                ->where('telesales.id',$id)
                ->first();

            if (!empty($dispositions['description'])) {
                $disp[] = $dispositions['description'];
            }
        }
        return $disp;
        
			  
    }


}
