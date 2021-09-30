<?php

namespace App\Http\Controllers\Admin;

use App\models\FormField;
use App\models\Programs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\models\Telesales;
use App\models\Telesalesdata;
use App\models\TelesalesZipcode;
use App\models\Client;
use App\models\Salescenter;
use App\models\Salescenterslocations;
use App\models\ScriptQuestions;
use App\models\Clientsforms;
use App\models\ClientAgentNotFoundScripts;
use App\models\CallAnswers;
use App\models\Brandcontacts;
use App\models\ClientTwilioNumbers;
use App\models\CriticalLogsHistory;
use App\Http\Controllers\Admin\TpvagentController;
use App\Http\Controllers\Client\ClientController;
use App\models\TelesaleScheduleCall;
use Illuminate\Support\Facades\Auth;
use function GuzzleHttp\Psr7\str;
use Illuminate\Database\Eloquent\Builder;
use DataTables;
use Storage;
use Carbon\Carbon;
use DB;
use App\models\TelesalesSelfVerifyExpTime;
use App\models\SelfverifyDetail;
use App\models\Leadmedia;
use App\models\TwilioLeadCallDetails;
use Log;


class TelesalesVerificationController extends Controller
{
    public $userobj = array();
    public $telesaleobj = array();
    public $telesalesdataobj = array();
    public $CallAnswers = array();
    public $ClientTwilioNumbers = array();

    public function __construct()
    {
        $this->userobj = (new User);
        $this->telesaleobj = (new Telesales);
        $this->telesalesdataobj = (new Telesalesdata);
        $this->CallAnswers = (new CallAnswers);
        $this->ClientTwilioNumbers = (new ClientTwilioNumbers);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $client_id = '';

        //Added subquery for full name searching 
        $subQuery = "CONCAT((CASE
        WHEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'first_name' and telesale_id =telesales.id LIMIT 1)  != ''
        THEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'first_name' and telesale_id =telesales.id  LIMIT 1 )
        ELSE ''
        END ),' ',(CASE
        WHEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'middle_initial' and telesale_id =telesales.id  LIMIT 1)  != ''
        THEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'middle_initial' and telesale_id =telesales.id  LIMIT 1)
        ELSE ''
        END),' ',(CASE
        WHEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'last_name' and telesale_id =telesales.id  LIMIT 1)  != ''
        THEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'last_name' and telesale_id =telesales.id  LIMIT 1)
        ELSE ''
        END))
        as 'AuthorizedName'";
        $nameSubQuery = "( select ".$subQuery.")";
        

            $timeZone = Auth::user()->timezone;
            $leadStatusSubQuery = "(CASE WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' WHEN telesales.status = 'self-verified' THEN 'Self verified' ELSE telesales.status END)";
            $telesales=Telesales::leftjoin('telesalesdata','telesalesdata.telesale_id','=','telesales.id')
            
                ->select('telesales.*',
            DB::raw($subQuery),
            DB::raw("(select GROUP_CONCAT(meta_value SEPARATOR ', ') from telesalesdata left join form_fields on form_fields.id = telesalesdata.field_id  where telesalesdata.field_id and  LOWER(form_fields.label) LIKE 'account number%' and form_fields.form_id = telesales.form_id and telesalesdata.meta_key = 'value' and telesalesdata.telesale_id = telesales.id LIMIT 1) as AccountNumber"),
                    DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'Phone Number' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and telesale_id =telesales.id LIMIT 1) as Phone"),
                    DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'email'  and is_primary  = 1 and form_id = telesales.form_id LIMIT 1) and telesale_id =telesales.id LIMIT 1) as CustomerEmail"),
                    DB::raw("(SELECT leadmedia.url FROM leadmedia
             where leadmedia.type = 'image' and telesales_id = telesales.id
ORDER BY `leadmedia`.`id` DESC LIMIT 1 ) as Esignature")
                    )
                ->addSelect(DB::raw($leadStatusSubQuery . " as status_new")
                )
                ->with('client','user.salescenter','user.salesAgentDetails.location','programs.utility.brandContacts','parentLead');
                
                /* check user access level */
            if(Auth::user()->hasAccessLevels(['salescenter'])) {
                $salescenter_id = Auth::user()->salescenter_id;
            } else {
                $salescenter_id = $request->salescenter_id;
            }
            /* check location level restriction */
            if(Auth::user()->isLocationRestriction()) {
                $locationId = Auth::user()->location_id;
            } else {
                $locationId = $request->location;
            }

            if (!empty($salescenter_id)) {
                $telesales->whereHas('user', function (Builder $query) use ($salescenter_id) {
                    $query->withTrashed()->where('salescenter_id', $salescenter_id);
                });
            }

            if (!empty($request->agent_type)) {
                $agent_type = $request->agent_type;
                $telesales->whereHas('user.salesAgentDetails', function (Builder $query) use ($agent_type) {
                    $query->withTrashed()->where('agent_type', $agent_type);
                });
            }
            // if (!empty($request->brand)) {
            //     $brand = $request->brand;
            //     $telesales->where('brand_contacts.id',$brand);
            // }
            if (!empty($request->brand)) {
                $telesales->whereHas('programs.utility.brandContacts', function (Builder $query) use ($request) {
                    $query->where('id',$request->brand);
                });
            }
            /* check user has multiple locations */
            if (auth()->user()->hasMultiLocations()) {
                $locationIds = auth()->user()->locations->pluck('id');
                $telesales->whereHas('userWithTrashed.salesAgentDetails', function (Builder $query) use ($locationIds) {
                    $query->withTrashed()->whereIn('location_id', $locationIds);
                });
            }

            if (!empty($locationId)) {                
                $telesales->whereHas('userWithTrashed.salesAgentDetails', function (Builder $query) use ($locationId) {
                    $query->withTrashed()->where('location_id', $locationId);
                });
            }

            if (!empty($request->date)) {
                $date = $request->date;
                
                $start_date = Carbon::parse(explode(' - ', $date)[0],$timeZone)->setTimezone('UTC');//->toDateString();
                $end_date = Carbon::parse(explode(' - ', $date)[1],$timeZone)->setTimezone('UTC')->addDays(1);//->toDateString();
                
                $telesales->whereBetween('telesales.created_at',[$start_date,$end_date]);
                
            }
            // if (!empty($request->refrence_id)) {
            //     $telesales->where('refrence_id',$request->refrence_id);
            // }
            if(Auth::user()->isAccessLevelToClient()) {
                $client_id = Auth::user()->client_id;
            } else {
                $client_id = $request->client_id;
            }
            
            if(!empty($client_id)) {
                $telesales->where('telesales.client_id',$client_id);
            }
            if(!empty($request->status)){
                $requestedStatus = $request->status;
                $telesales->where('telesales.status',"=",$requestedStatus);
            }
            $telesales->groupBy('telesales.id');

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
            
            // dd($telesales->with('parentLead')->orderBy('id', 'desc')->first());
            if ($request->ajax()) {
            return DataTables::of($telesales)
                ->editColumn('status_new', function($telesale){                    
                    return ucfirst($telesale->status_new);
                })
                ->editColumn('refrence_id', function($telesale){     
                    $isChild = $telesale->childLeads()->get();
                    $html = 'Associated Leads ' ;
                    if(isset($isChild) && $isChild->count() > 0){
                        foreach($isChild as $k =>  $v){
                            $html .= "<br/>".$v->refrence_id ;
                        }
                        return '<a data-toggle="tooltip" data-placement="top" data-container="body" data-html="true" style="color: black;" data-original-title="'.$html.'">'.$telesale->refrence_id.'</a>';
                    }
                    else{
                        return $telesale->refrence_id;
                    }
                    
                })
                ->filterColumn('AuthorizedName',function($telesale,$keyword) use($nameSubQuery){  
                    return  $telesale->whereRaw($nameSubQuery .' LIKE "%'.$keyword.'%"');
                })
                // For filter/search parent_id (Parent Lead Id)
                ->filterColumn('multiple_parent_id',function($telesale,$keyword) use($multipleParentId) {
                    return $telesale->orWhereIn('telesales.id',$multipleParentId);
                })
                ->addColumn('brand_name', function($telesale){
                    $name = '';
                    if(!empty($telesale->programs) && !empty($telesale->programs[0]->utility) && !empty($telesale->programs[0]->utility->brandContacts)) {
                        $name= $telesale->programs[0]->utility->brandContacts->name;
                    }
                    return $name;
                })
                // Add refrence id of parent in listing
                ->addColumn('multiple_parent_id', function($telesale){
                    $parent_id = '';
                    if(!empty($telesale->parentLead)) {
                        $parent_id = $telesale->parentLead->refrence_id;
                    }
                    return $parent_id;
                })
                ->addColumn('client_name', function($telesale){
                    $name = '';
                    if(!empty($telesale->client)) {
                        $name= $telesale->client->name;
                    }
                    return $name;
                })
                ->addColumn('salescenter_name', function($telesale){
                    $name = '';
                    if(!empty($telesale->user()->withTrashed()->first()) && !empty($telesale->user()->withTrashed()->first()->salescenter)) {
                        $name= $telesale->user()->withTrashed()->first()->salescenter->name;
                    }
                    return $name;
                })
                ->addColumn('location_name', function($telesale){
                    $name = '';
                    if(!empty($telesale->user()->withTrashed()->first()) && !empty($telesale->user()->withTrashed()->first()->salesAgentDetailsWithTrashed) && !empty($telesale->user()->withTrashed()->first()->salesAgentDetailsWithTrashed->locationWithTrashed)) {
                        $name= $telesale->user()->withTrashed()->first()->salesAgentDetailsWithTrashed->locationWithTrashed->name;
                    }
                    return $name;
                })
                ->addColumn('agent_name', function($telesale){
                    $name = '';
                    if(!empty($telesale->user()->withTrashed()->first())) {
                        $name= $telesale->user()->withTrashed()->first()->full_name;
                    }
                    return $name;
                })
                ->addColumn('userid', function($telesale){
                    $name = '';
                    if(!empty($telesale->user()->withTrashed()->first())) {
                        $name= $telesale->user()->withTrashed()->first()->userid;
                    }
                    return $name;
                })
                ->addColumn('external_id', function($telesale){
                    $name = '';
                    if(!empty($telesale->user()->withTrashed()->first()) && !empty($telesale->user()->withTrashed()->first()->salesAgentDetailsWithTrashed)) {
                        $name= $telesale->user()->withTrashed()->first()->salesAgentDetailsWithTrashed->external_id;
                    }
                    return $name;
                })
                ->addColumn('channel', function($telesale){
                    $channel = '';
                    if(!empty($telesale->user()->withTrashed()->first()) && !empty($telesale->user()->withTrashed()->first()->salesAgentDetailsWithTrashed->agent_type)) {
                        $channel= strtoupper($telesale->user()->withTrashed()->first()->salesAgentDetailsWithTrashed->agent_type);
                    }
                    return $channel;
                })
                ->editColumn('created_at', function($telesale) use($timeZone){
                    $date = '';
                    if(!empty($telesale->created_at)) {

                        $date = $telesale->created_at->setTimezone($timeZone)->format(getDateFormat());
                    }
                    return $date;
                })
                ->editColumn('contract_pdf', function($telesale){
                    $viewBtn = '';
                    if(!empty($telesale->contract_pdf) && ((($telesale->type == 'tele' && isOnSettings($telesale->client_id, 'is_enable_contract_tele')) || ($telesale->type == 'd2d' && isOnSettings($telesale->client_id, 'is_enable_contract_d2d'))) || ($telesale->client_id == config()->get('constants.CLIENT_BOLT_ENEGRY_CLIENT_ID')))) {
                        $exists = Storage::disk('s3')->exists(config()->get('constants.aws_folder') . $telesale->contract_pdf);
                        if($exists) {
                            $viewBtn = '<a  data-toggle="tooltip"
                            data-placement="top"
                            data-original-title="View Contract Package"
                            class="btn"
                            target="_blank"
                            href="' .Storage::disk('s3')->url($telesale->contract_pdf) . '"
                            >View</a>';
                        }
                    }
                    return '<div class="btn-group">'.$viewBtn.'<div>';
                })
                ->addColumn('consent_recording', function($telesale){
                    $viewBtn = '';
                    if(!$telesale->leadMedia->isEmpty()) {
                        $audio = $telesale->leadMedia->where('type','audio')->first();
                        if(!empty($audio)) {
                            $exists = Storage::disk('s3')->exists(config()->get('constants.aws_folder') . $audio->url);
                            if($exists) {
                                $viewBtn = '<a  data-toggle="tooltip"
                                data-placement="top"
                                data-original-title="Play Recording"
                                class="btn purple"
                                target="_blank"
                                href="' .Storage::disk('s3')->url($audio->url) . '"
                                >'.getimage("images/play.png").'</a>';
                            }
                        }
                    }
                    return '<div class="btn-group">'.$viewBtn.'<div>';
                })
                ->editColumn('s3_recording_url', function($telesale){
                    $viewBtn = '';

                    if (!empty($telesale->s3_recording_url)){
                        $exists = Storage::disk('s3')->exists(config()->get('constants.aws_folder') . $telesale->s3_recording_url);
                        if ($exists) {
                          $viewBtn = '<a href="'.Storage::disk('s3')->url($telesale->s3_recording_url).'" target="_blank" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Play Recording" class="btn purple">'.getimage("images/play.png").'</a>';
                        }

                    }
                    return '<div class="btn-group">'.$viewBtn.'<div>';
                })
                ->editColumn('Esignature',function($telesale){ 
                       if(!empty($telesale->Esignature)){ 
                          $url = Storage::disk('s3')->url($telesale->Esignature);
                          return '<img src="'.$url.'" style="object-fit: cover;height:40px;"/>';
                      }
                      })
                ->editColumn('tpv_receipt_pdf', function($telesale){
                    $viewBtn = '';
                    if(!empty($telesale->tpv_receipt_pdf)) {
                        $viewBtn = '<a  data-toggle="tooltip"
                            data-placement="top"
                            data-original-title="View TPV Receipt"
                            class="btn"
                            target="_blank"
                            href="' .Storage::disk('s3')->url($telesale->tpv_receipt_pdf) . '"
                            >View</a>';

                    }
                    return '<div class="btn-group">'.$viewBtn.'<div>';
                })
                ->addColumn('action', function($telesale){
                    $viewBtn = $deleteBtn = '';
                    $viewBtn = '<a  data-toggle="tooltip"
                        data-placement="top"
                        data-type="view"
                        data-original-title="View"
                        data-title="View Leads"
                        class="btn view-leads theme-color"
                        href="' .route("telesales.show",$telesale->id) . '"
                        >' . getimage("images/view.png") . '</a>';

                    if(auth()->user()->hasPermissionTo('delete-lead-detail-report')) {
                        $class = 'delete-lead-report';
                        $attributes = [
                            "data-original-title" => "Delete Lead",
                            "data-id" => $telesale->id,
                            "data-status" =>"delete",
                        ];
                        $deleteBtn = getDeleteBtn($class, $attributes);
                    } 

                    return '<div class="btn-group">'.$viewBtn . $deleteBtn . '<div>';
                })
                ->rawColumns(['contract_pdf','consent_recording','s3_recording_url','tpv_receipt_pdf','action','refrence_id','Esignature'])
                ->make(true);
                
        }
        $clients= getAllClients();
        $brands = (new Brandcontacts)->getBrandsByClient($client_id);        
        $salesCenters= getAllSalesCenter();
        return view('admin.leads.index',compact('clients','salesCenters','brands'));
    }

    /**
     * This method is used for remove lead
     */
    public function deleteLead(Request $request)
    {
        try{
            //forcedelete the lead because of client requirements.
            $telesales = Telesales::find($request->id);
            //check whether this lead has child leads or not
            $isChildExist = (new Telesales())->getChildLeads($telesales->id);
            if(isset($isChildExist) && $isChildExist->count() > 0){
                //delete child leads
                DB::beginTransaction();
                foreach($isChildExist as $key => $val){
                    TelesalesZipcode::where('telesale_id',$val->id)->delete();
                    TelesaleScheduleCall::where('telesale_id',$val->id)->forceDelete();
                    TelesalesSelfVerifyExpTime::where('telesale_id',$val->id)->forceDelete();
                    SelfverifyDetail::where('telesale_id',$val->id)->forceDelete();
                    CriticalLogsHistory::where('lead_id',$val->id)->forceDelete();
                    CallAnswers::where('lead_id',$val->id)->forceDelete();
                    DB::delete("delete from telesales_programs where telesale_id = '".$val->id."'");
                    Telesales::where('id',$val->id)->forceDelete();
                }
                DB::commit();
            }
            if($telesales)
            {
                DB::beginTransaction();

                TelesalesZipcode::where('telesale_id',$request->id)->delete();
                TelesaleScheduleCall::where('telesale_id',$request->id)->forceDelete();
                TelesalesSelfVerifyExpTime::where('telesale_id',$request->id)->forceDelete();
                SelfverifyDetail::where('telesale_id',$request->id)->forceDelete();
                CriticalLogsHistory::where('lead_id',$request->id)->forceDelete();
                CallAnswers::where('lead_id',$request->id)->forceDelete();
                DB::delete("delete from telesales_programs where telesale_id = '".$request->id."'");
                Telesales::where('id',$request->id)->forceDelete();
                
                DB::commit();
                return $this->success('success','Lead successfully deleted.');
            }
            else
            {
                return $this->success('success','Something went wrong, please try again later.');
            }
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollback();
            return response()->json(['status' =>'error','message'=>'Something went wrong, please try again.']);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // dd($request->id);
        $telesale = Telesales::findOrFail($request->id);
        $form = Clientsforms::withTrashed()->find($telesale->form_id);

        $telesale_id = $telesale->id;
        $programs = $telesale->programs()->withTrashed()->with('utility')->get();
        $lead_detail = array();

        // If value of is_multiple is 0 than lead is parent and value is 1 than that lead is childlead
        $telesale_multiple = $telesale->multiple_parent_id; 
        $parentId = '#';
        $parentLeadId = '';
        $childInfo = '';
        // If lead is parent 
        // if ($telesale_multiple == '0') {
        //     $parentInfo = Telesales::where('id', '=', $request->id)->with('parentLead')->get();
        //     if (!empty($parentInfo[0]->parentLead)) {
        //         $parentId = $parentInfo[0]->parentLead->id;
        //         $parentLeadId = $parentInfo[0]->parentLead->refrence_id; 
        //     }    
        // } 
        // // If lead is child 
        // if ($telesale_multiple != '0') {
        //     $childInfo = Telesales::where('id', '=', $request->id)->with('childLeads')->get();
        // }
        
        //For call history details
        $telesaleScheduleCall = TelesaleScheduleCall::where('telesale_id',$telesale_id)->select('telesale_schedule_call.*',DB::raw("(CASE WHEN schedule_status = 'task-created' THEN 'Task Created' WHEN schedule_status = 'cancelled' THEN 'Cancelled' WHEN schedule_status = 'attempted' THEN 'Attempted' WHEN schedule_status = 'pending' THEN 'Pending' WHEN schedule_status = 'skip' THEN 'Skipped' ELSE '' END) as schedule_status"),

        DB::raw("(CASE WHEN dial_status = 'completed' THEN 'Completed' WHEN dial_status = 'answered' THEN 'Answered' WHEN dial_status = 'busy' THEN 'Busy' WHEN dial_status = 'no-answer' THEN 'No Answer' WHEN dial_status = 'cancelled' THEN 'Cancelled'  WHEN dial_status = 'failed' THEN 'Failed' ELSE '-' END) as dial_status")
        ,DB::raw("(CASE WHEN call_immediately = 'yes' THEN 'Now' ELSE 'Scheduled' END) as call_immediately"),
        DB::raw("(CASE WHEN call_lang = 'en' THEN 'English' ELSE 'Spanish' END) as call_lang"))->orderBy('id','DESC')->get();

        if (!empty($form)) {
            $lead_detail = $form->fields()->with(['telesalesData' => function ($query) use ($telesale_id) {
                $query->where('telesale_id', $telesale_id);
            }])->get()->toArray();
        }

        $criticalLogs = CriticalLogsHistory::leftjoin('telesales','telesales.id','critical_logs_history.lead_id')
            ->leftjoin('users','users.id','=','critical_logs_history.sales_agent_id')
            ->leftjoin('users as tpv_agent','tpv_agent.id','=','critical_logs_history.tpv_agent_id')
            ->where('lead_id',$telesale->id)
            ->select('critical_logs_history.*','users.first_name','users.last_name','telesales.status','tpv_agent.first_name as tpv_agent_first_name','tpv_agent.last_name as tpv_agent_last_name', 'telesales.verification_method');
            if (!empty($offAlerts)) {
                $criticalLogs->whereNotIn('event_type',$offAlerts);
            }
            $criticalLogs = $criticalLogs->get();
            $timeZone = Auth::user()->timezone;
            $audio = $telesale->leadMedia->where('type','audio')->first();
            foreach($criticalLogs as $k => $v)
            {
                if ($criticalLogs[$k]->tpv_agent_id != "") {
                    $criticalLogs[$k]->tpv_agent_val = $criticalLogs[$k]->tpv_agent_first_name . " " . $criticalLogs[$k]->tpv_agent_last_name;
                } else {
                    if ($criticalLogs[$k]->verification_method == config()->get('constants.IVR_INBOUND_VERIFICATION') && in_array($criticalLogs[$k]->event_type, [config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_40'), config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_41'), config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_42')])) {
                        $criticalLogs[$k]->tpv_agent_val = "IVR";
                    } else {
                        $criticalLogs[$k]->tpv_agent_val = "";
                    }
                }
                
                // $date = new \DateTime($criticalLogs[$k]->created_at, new \DateTimeZone('UTC'));
                // $date->setTimezone(new \DateTimeZone('America/New_York'));
                // $criticalLogs[$k]->formatted_created_at = $date->format('m/d/Y h:i:s A');
                $criticalLogs[$k]->formatted_created_at = $criticalLogs[$k]->created_at->setTimezone($timeZone)->format(getDateFormat().' '.getTimeFormat());
                
                $leadIds = explode(",", $criticalLogs[$k]->related_lead_ids);
                
                $displayLeadArr = array_map(function($val) {
                    
                    if(Auth::user()->isAccessLevelToClient()) {
                        
                        $lead = Telesales::where('id',$val)->where('client_id',Auth::user()->client_id);
                        $salescenter_id = null;
                        if(Auth::user()->hasAccessLevels('salescenter')) {
                            $salescenter_id = Auth::user()->salescenter_id;
                        }
                        if (!empty($salescenter_id)) {
                            $lead->whereHas('user', function (Builder $query) use ($salescenter_id) {
                                $query->where('salescenter_id', $salescenter_id);
                            });
                        }
                        if (auth()->user()->hasMultiLocations()) {
                            $locationIds = auth()->user()->locations->pluck('id');
                            $lead->whereHas('user.salesAgentDetails', function (Builder $query) use ($locationIds) {
                                $query->whereIn('location_id', $locationIds);
                            });
                        }

                        if(Auth::user()->isLocationRestriction()) {
                            $locationId = Auth::user()->location_id;
                            $lead->whereHas('user.salesAgentDetails', function (Builder $query) use ($locationId) {
                                $query->where('location_id', $locationId);
                            });
                        } 

                        $result =$lead->first();
                        
                        if(!empty($result)) {
                            if($val != '')
                            {
                                return '<a href="' . route('telesales.show', $val) . '">' . $val . '</a>';
                            }
                            else
                                return  '';
                        } else {
                            if($val != '')
                            {
                                return '<a href="#" class="cursor-none">' . $val . '</a>';
                            }
                            else
                                return  '';
                            
                        }
                    } else {
                        if($val != "")
                            return '<a href="' . route('telesales.show', $val) . '">' . $val . '</a>';
                        else
                            return '';
                    }

                } , $leadIds);
                
                $criticalLogs[$k]->related_lead_ids = implode(", ", $displayLeadArr);
            }

            // For get parent Or child lead numbers
            $lead = (new Telesales)->getLeadID($telesale->refrence_id);
            $leadDetails = Telesales::where('id', $lead->id);
            $additionalDetails = [];
            if ($leadDetails->first()->multiple_parent_id == '0') {
                $additionalDetails = $leadDetails->with('childLeads')->first();
            } else if($leadDetails->first()->multiple_parent_id != '0') {
                $additionalDetails = $leadDetails->with('parentLead')->first();
            }
            $leadMedia = Leadmedia::where('telesales_id',$telesale_id)->where('type','image')->select('url')->orderBy('id','DESC')->first();
       
            //check e-signature exists or not

            if(!empty($leadMedia->url)){
                $e_signature = Storage::disk('s3')->url($leadMedia->url);
            }else{
                $e_signature = '';
            }
            // dd($additionalDetails);
            
        return view('admin.leads.show', compact('lead_detail', 'telesale', 'programs','telesaleScheduleCall', 'telesale_multiple','criticalLogs','audio','additionalDetails','e_signature'));
    }

    /**
     * This method is used for verify agent
     */
    public function verifyAgent(Request $request)
    {
        if (isset($request->agentid) && isset($request->workspace_id) && !empty($request->agentid) && !empty($request->workspace_id) && !empty($request->client_id)) {

            $user = $this->userobj->verifyAgent($request->agentid, $request->workspace_id, $request->client_id);
            $questions = array();
            if (count($user) == 0) {
                if (isset($request->client_id) && isset($request->lang)) {
                    $all_questions = (new ClientAgentNotFoundScripts)->getQuestionsByClientIdAndLanguage($request->client_id, $request->lang);
                    $all_questions = $all_questions->toArray();
                    foreach ($all_questions as $ques) {
                        $questions[] = $ques['question'];
                    }

                }
                return array(
                    'status' => 'error',
                    'message' => 'User not found',
                    'questions' => $questions
                );
            } else {

                return array(
                    'status' => 'success',
                    'data' => $user
                );
            }

        } else {
            return array(
                'status' => 'error',
                'message' => 'Invalid Request'
            );
        }
    }

    /**
     * This method is used for verify tele sales details
     */
    public function verifyTelesale(Request $request)
    {
        if ($request->telesaleid) {
            $userid = $request->agentid;
            // $telesale = $this->telesaleobj->getLeadID($request->telesaleid);
            $telesale = $this->telesaleobj->getLeadByIDAndUserid($request->telesaleid, $userid);

            if (empty($telesale)) {
                return array(
                    'status' => 'error',
                    'message' => 'Sale not found'
                );
            } else {
                $telesaleObj = Telesales::find($telesale->id);
                \Log::info('Lead details::');
                \Log::debug($telesaleObj);
                $callDetailsTwilio = TwilioLeadCallDetails::where('worker_call_id',$request->callid)->first();
                if(!empty($callDetailsTwilio)){
                    $callDetailsTwilio->lead_id = $telesale->id;
                    $callDetailsTwilio->previous_status = $telesale->status;
                    $callDetailsTwilio->lead_status = $telesale->status;
                    $callDetailsTwilio->save();
                    \Log::info($callDetailsTwilio);
                }

                if (!empty($telesaleObj)) {
                    $telesaleObj->verification_start_date = date('Y-m-d H:i:s');
                    $telesaleObj->save();
                    CallAnswers::where('lead_id', $telesaleObj->id)->delete();
                }


                if (isset($request->callid)) {
                    $this->telesaleobj->updatesale($request->telesaleid, array('call_id' => $request->callid));
                }

                if ($telesale) {
                    $telesale_id = $telesale->id;
                    $commodity = $this->telesalesdataobj->leadMetakeyData($telesale_id, 'Commodity');
                    $state = $this->telesalesdataobj->leadMetakeyData($telesale_id, 'zipcodeState');
                } else {
                    $commodity = "";
                    $state = "";
                }

                return array(
                    'status' => 'success',
                    'data' => $telesale,
                    'commodity' => $commodity,
                    'state' => $state

                );
            }

        } else {
            return array(
                'status' => 'error',
                'message' => 'Invalid Request'
            );
        }
    }

    /*
    @Author : Ritesh Rana
    @Desc   : customerLeadVerify.
    @Input  :
    @Output : Illuminate\Http\Response
    @Date   : 05/02/2020
    */
    public function customerLeadVerify(Request $request)
    {
        if ($request->telesaleid) {

            //change in query for checking is requested lead id is parent lead or child lead
            $telesale = Telesales::select('id', 'form_id', 'refrence_id','is_multiple', 'multiple_parent_id','status')
                ->where('refrence_id', $request->telesaleid)
                ->where('multiple_parent_id',0)
                ->whereIn('status', ['pending', 'hangup'])->first();
            if (empty($telesale)) {
                return array(
                    'status' => 'error',
                    'message' => 'Sale not found'
                );
            } else {
                $authName = "";
                $zipcode_data = "";
                $account_detail = "";
                // if ($telesale) {
                    $telesale->verification_start_date = date('Y-m-d H:i:s');
                    $telesale->save();

                    $delAns = CallAnswers::where('lead_id', $telesale->id)->delete();
                    \Log::debug("Delete answers: " . print_r($delAns, true));
                    \Log::info('Lead details::');
                    \Log::debug($telesale);
                    if (isset($request->callid)) {
                        $this->telesaleobj->updatesale($request->telesaleid, array('call_id' => $request->callid));
                        $callDetailsTwilio = TwilioLeadCallDetails::where('worker_call_id',$request->callid)->first();
                        if(!empty($callDetailsTwilio)){
                            $callDetailsTwilio->lead_id = $telesale->id;
                            $callDetailsTwilio->previous_status = $telesale->status;
                            $callDetailsTwilio->lead_status = $telesale->status;
                            $callDetailsTwilio->save();
                            \Log::info($callDetailsTwilio);
                        }
                    }

                    $fullname = FormField::where('form_id', $telesale->form_id)->where('is_primary', 1)->where('type', 'fullname')->first();
                    $account_number = FormField::where('form_id', $telesale->form_id)->where('label', 'Account Number')->first();
                    $address = FormField::where('form_id', $telesale->form_id)
                        ->where(function ($que) {
                            $que->where('type', 'address')->orWhere('type', "service_and_billing_address");
                        })
                        ->where('is_primary', 1)->first();


                    if (!empty($account_number)) {
                        $account = Telesalesdata::where('field_id', $account_number->id)->where('meta_key', 'value')->where('telesale_id', $telesale->id)->first();
                        $account_detail = array_get($account, 'meta_value');
                    }

                    if (!empty($fullname)) {
                        $firstName = Telesalesdata::where('field_id', $fullname->id)->where('meta_key', 'first_name')->where('telesale_id', $telesale->id)->first();
                        $middleName = Telesalesdata::where('field_id', $fullname->id)->where('meta_key', 'middle_initial')->where('telesale_id', $telesale->id)->first();
                        $lastName = Telesalesdata::where('field_id', $fullname->id)->where('meta_key', 'last_name')->where('telesale_id', $telesale->id)->first();
                        $authName = implode(" ", array(array_get($firstName, 'meta_value'), array_get($middleName, 'meta_value'), array_get($lastName, 'meta_value')));
                    }
                    if (!empty($address)) {
                        switch (array_get($address, 'type')) {
                            case "address":
                                $zipcode = Telesalesdata::where('field_id', $address->id)->where('meta_key', 'zipcode')->where('telesale_id', $telesale->id)->first();
                                break;
                            case "service_and_billing_address":
                                $zipcode = Telesalesdata::where('field_id', $address->id)->where('meta_key', 'billing_zipcode')->where('telesale_id', $telesale->id)->first();
                                break;
                            default:
                                $zipcode = "";
                                break;
                        }
                    }
                    $zipcode_data = array_get($zipcode, 'meta_value', '');
                // } else {
                //     $authName = "";
                //     $zipcode_data = "";
                //     $account_detail = "";
                // }
                return array(
                    'status' => 'success',
                    'data' => $telesale,
                    'authname' => $authName,
                    'state' => $zipcode_data,
                    'account_detail' => $account_detail
                );

                return array(
                    'status' => 'success',
                    'message' => 'Lead Found'
                );
            }

        } else {
            return array(
                'status' => 'error',
                'message' => 'Invalid Request'
            );
        }
    }

    /**
     * This function is used for verify lead
     */
    public function verifyLead(Request $request)
    {
        \Log::info($request->all());
        
        if ($request->telesale_id > 0) {
            $telesale_id = $request->telesale_id;
            $telesale_form_id = $request->telesale_form_id;
            $current_lang = $request->current_lang;
            $leadcommodity = $request->leadcommodity;
            $leadzipcodestate = $request->leadzipcodestate;
            \Log::info("Lead Zipcode:".$leadzipcodestate);
            if (isset($request->vtype)) {
                $tpv_agent_name = "";
            } else {
                $tpv_agent_name = Auth::user()->first_name;
            }

            $telesale = Telesales::find($telesale_id);

            //For checking whethere this lead has child leads or not
            $isChildExist = (new Telesales())->getChildLeads($telesale->id);
            
            if (!empty($telesale)) {
                $telesale->verification_number = generateVerificationNumer($telesale);
                $telesale->save();

                //check for tpv attempt alert tele/d2d
                (new TpvagentController)->checkAlertTeleD2d($telesale);

                $options_to_fill = (new TpvagentController)->getTagToReplaceForQuestions(array_get($telesale, 'id'), array_get($telesale, 'form_id'));
                
                \Log::debug($options_to_fill);
                $zipcode = $telesale->zipcodes()->first();
                $scripts = (new ScriptQuestions)->getScriptsUsingFormIDandLanguage($telesale_form_id, $current_lang, 'self_verification', $zipcode->state, $telesale->client_id);
                if (count($scripts) > 0) {
                    $script_id = $scripts[0]->id;

                    $get_questions_to_replace_tags = (new ScriptQuestions)->scriptQuestionsWithStateCommodity($script_id, $leadzipcodestate, $leadcommodity, $telesale->form_id);
                    $questionIds = array_column($get_questions_to_replace_tags->toArray(),'id');
                    $conditions = DB::table('script_questions_conditions')->whereIn('question_id',$questionIds)->where('condition_type','tag')->get()->toArray();

                    $introQuestions = ScriptQuestions::where('script_id', $script_id)->with(['script' => function($q) use($leadzipcodestate) {
                        if($leadzipcodestate != 'ALL' && $leadzipcodestate != null) {
                            $q->where('state', $leadzipcodestate);
                        }
                    }])->where('is_introductionary', 1)->count();
                    $questions_array = array();
                    $removedTag = [];
                    if (count($get_questions_to_replace_tags) > 0) {
                        foreach ($get_questions_to_replace_tags as $k => $single_question) {
                            
                            //for replace quesitons with tags
                            $actualQuestion = array_get($single_question, 'question');
                            $full_question = (new TpvagentController)->getQuestionReplaceWithTags($actualQuestion,$options_to_fill);

                            //for skip question based on conditions.
                            $skipQue = (new TpvagentController)->checkScriptQuestionCondition($conditions,$actualQuestion,$single_question,$options_to_fill);
                            if($skipQue == true)
                            {
                                continue;
                            }

                            //for skip empty tag quesiton.
                            $emptyFlag = (new TpvagentController)->checkEmptyTagQuestion($full_question);
                            if($emptyFlag == true)
                            {
                                continue;
                            }
                            
                            $answer_option = strtr($single_question->answer, $options_to_fill);
                            
                            $questions_array[] = ['question' => $full_question,
                                'id' => $single_question->id,
                                'answer_option' => $answer_option,
                                'positive_ans' => $single_question->positive_ans,
                                'negative_ans' => $single_question->negative_ans,
                                'is_customizable' => $single_question->is_customizable,
                                'is_introductionary' => $single_question->is_introductionary,
                                'intro_questions' => $introQuestions,
                                'question_conditions' => $single_question->questionConditions
                            ];
                        }
                    }

                    //For fetching child leads quesiton that is_multiple is true
                    foreach($isChildExist as $key =>$val){
                        // for save  veirification number same as parent lead
                        $val->verification_number = $telesale->verification_number;
                        $val->save();
                        
                        // for fetching tags of child lead id
                        $options_to_fill = (new TpvagentController)->getTagToReplaceForQuestions($val->id, $val->form_id);
                        if (count($get_questions_to_replace_tags) > 0) {
                            foreach ($get_questions_to_replace_tags as $k => $single_question) {
                                if($single_question->is_multiple == 1){

                                    //For replace questions with tags
                                    $actualQuestion = array_get($single_question, 'question');
                                    $full_question = (new TpvagentController)->getQuestionReplaceWithTags($actualQuestion,$options_to_fill);
        
                                    //for skip question based on conditions.
                                    $skipQue = (new TpvagentController)->checkScriptQuestionCondition($conditions,$actualQuestion,$single_question,$options_to_fill);
                                    if($skipQue == true)
                                    {
                                        continue;
                                    }
        
                                    //for skip empty tag quesiton.
                                    $emptyFlag = (new TpvagentController)->checkEmptyTagQuestion($full_question);
                                    if($emptyFlag == true)
                                    {
                                        continue;
                                    }
                                    
                                    $answer_option = strtr($single_question->answer, $options_to_fill);
                                    $questions_array[] = ['question' => $full_question,
                                    'id' => $single_question->id,
                                    'answer_option' => $answer_option,
                                    'positive_ans' => $single_question->positive_ans,
                                    'negative_ans' => $single_question->negative_ans,
                                    'is_customizable' => $single_question->is_customizable,
                                    'is_introductionary' => $single_question->is_introductionary,
                                    'intro_questions' => $introQuestions,
                                    'question_conditions' => $single_question->questionConditions];
                                }
                            }
                        }                
                    }
                    return array(
                        'status' => 'success',
                        'data' => $questions_array
                    );

                } else {
                    return array(
                        'status' => 'error',
                        'message' => 'Script not found'
                    );
                }

            } else {
                return array(
                    'status' => 'error',
                    'message' => 'Lead not found'
                );
            }


        } else {
            return array(
                'status' => 'error',
                'message' => 'Invalid Request'
            );
        }

    }

    /**
     * This method is used for verify old leads
     */
    public function verifyLeadOld(Request $request)
    {

        if ($request->telesale_id > 0) {
            $telesale_id = $request->telesale_id;
            $telesale_form_id = $request->telesale_form_id;
            $workspace_id = $request->form_worksid;
            $workflow_id = $request->form_workflid;
            $current_lang = $request->current_lang;
            $leadcommodity = $request->leadcommodity;
            $leadzipcodestate = $request->leadzipcodestate;
            if (isset($request->vtype)) {
                $tpv_agent_name = "";
            } else {
                $tpv_agent_name = Auth::user()->first_name;
            }

            $this->CallAnswers->deleteAnswers($telesale_id);

            $lead_meta_data = $this->telesalesdataobj->leadDetail($telesale_id);
            $phones = $this->ClientTwilioNumbers->getWorkflowNumbersbyworkflowid($workflow_id);
            $phonenumbers = array();
            if (count($phones) > 0) {
                foreach ($phones as $phone) {
                    $phonenumbers[] = $phone->phonenumber;
                }

            }


            // $d = date('Y-m-d H:i:s');
            // $date = new \DateTime($d, new \DateTimeZone('UTC'));
            // $date->setTimezone(new \DateTimeZone('America/New_York'));
            // $cd = $date->format('m-d-Y');
            // $ct = $date->format('H:i:s');
            
            $d = date('Y-m-d H:i:s');
            $date = new \DateTime($d);
            $date->setTimezone(new \DateTimeZone(Auth::user()->timezone));
            $cd = $date->format(getDateFormat());
            $ct = $date->format(getTimeFormat());

            $options_to_fill = array(
                "[Tpvagent]" => $this->highlight_tag($tpv_agent_name),
                "[Date]" => $this->highlight_tag($cd),
                "[Time]" => $this->highlight_tag($ct),
                "[ClientPhone]" => $this->highlight_tag(implode(',', $phonenumbers)),

            );

            $clientUtilityProgramTagData = (new ScriptQuestions)->clientUtilityProgramTagData($telesale_form_id);
            if (count($clientUtilityProgramTagData) > 0) {
                $array = (array)$clientUtilityProgramTagData[0];
                foreach ($array as $tag_name => $tags_with_value) {
                    $options_to_fill[$tag_name] = $this->highlight_tag($tags_with_value);


                }
            }
            $service_address = array();
            $gasservice_address = array();
            $billing_address = array();
            $electricbilling_address = array();
            $gasbilling_address = array();
            $name_checking = $normal_billing_name = $without_authorized = array();
            foreach ($lead_meta_data as $metadata) {
                if ($metadata->meta_key == 'gasutility' || $metadata->meta_key == 'electricutility' || $metadata->meta_key == 'utility') {
                    $options_to_fill["[Brand]"] = $this->highlight_tag($metadata->meta_value);
                    $contacts = (new Brandcontacts)->getBrandContactByName($metadata->meta_value);
                    if (count($contacts) > 0) {

                        $options_to_fill["[BrandPhone]"] = $this->highlight_tag($contacts[0]->contacts);
                    }


                }

                if ($metadata->meta_key == 'Authorized First name' || $metadata->meta_key == 'Authorized Middle initial' || $metadata->meta_key == 'Authorized Last name') {

                    $options_to_fill["[" . $metadata->meta_key . "]"] = $this->highlight_tag($metadata->meta_value);
                    $withoutauthtitle = str_replace('Authorized ', '', $metadata->meta_key);
                    $options_to_fill["[" . $withoutauthtitle . "]"] = $this->highlight_tag($metadata->meta_value);
                    $without_authorized[$withoutauthtitle] = $this->highlight_tag($metadata->meta_value);
                    $name_checking[$metadata->meta_key] = $this->highlight_tag($metadata->meta_value);
                }
                if ($metadata->meta_key == 'First name' || $metadata->meta_key == 'Middle initial' || $metadata->meta_key == 'Last name') {

                    $options_to_fill["[" . $metadata->meta_key . "]"] = $this->highlight_tag($metadata->meta_value);

                    $options_to_fill["[" . $metadata->meta_key . "]"] = $this->highlight_tag($metadata->meta_value);
                    $without_authorized[$metadata->meta_key] = $this->highlight_tag($metadata->meta_value);
                    $name_checking['Authorized ' . $metadata->meta_key] = $this->highlight_tag($metadata->meta_value);
                }


                if ($metadata->meta_key == 'BillingAddress' || $metadata->meta_key == 'BillingAddress2' || $metadata->meta_key == 'BillingZip' || $metadata->meta_key == 'BillingCity' || $metadata->meta_key == 'BillingState') {

                    $options_to_fill["[" . $metadata->meta_key . "]"] = $this->highlight_tag($metadata->meta_value);

                    $billing_address[$metadata->meta_key] = $this->highlight_tag($metadata->meta_value);

                } else
                    if ($metadata->meta_key == 'ServiceZip' || $metadata->meta_key == 'ServiceCity' || $metadata->meta_key == 'ServiceState' || $metadata->meta_key == 'ServiceAddress' || $metadata->meta_key == 'ServiceAddress2') {


                        $service_address[$metadata->meta_key] = $this->highlight_tag($metadata->meta_value);

                        $options_to_fill["[" . $metadata->meta_key . "]"] = $this->highlight_tag($metadata->meta_value);

                    } else if ($metadata->meta_key == 'Electric Billing first name' || $metadata->meta_key == 'Electric Billing middle name' || $metadata->meta_key == 'Electric Billing last name') {
                        $options_to_fill["[" . $metadata->meta_key . "]"] = $this->highlight_tag($metadata->meta_value);
                        if (isset($options_to_fill["[Electric Billing full name]"]) && $options_to_fill["[Electric Billing full name]"] != "") {
                            $options_to_fill["[Electric Billing full name]"] = $options_to_fill["[Electric Billing full name]"] . ", " . $this->highlight_tag($metadata->meta_value);
                        } else {
                            $options_to_fill["[Electric Billing full name]"] = $this->highlight_tag($metadata->meta_value);
                        }


                    } else if ($metadata->meta_key == 'Gas Billing first name' || $metadata->meta_key == 'Gas Billing middle name' || $metadata->meta_key == 'Electric Billing last name') {
                        $options_to_fill["[" . $metadata->meta_key . "]"] = $this->highlight_tag($metadata->meta_value);
                        if (isset($options_to_fill["[Gas Billing full name]"]) && $options_to_fill["[Gas Billing full name]"] != "") {
                            $options_to_fill["[Gas Billing full name]"] = $options_to_fill["[Gas Billing full name]"] . ", " . $this->highlight_tag($metadata->meta_value);
                        } else {
                            $options_to_fill["[Gas Billing full name]"] = $this->highlight_tag($metadata->meta_value);
                        }


                    } else
                        if ($metadata->meta_key == 'ElectricBillingAddress' || $metadata->meta_key == 'ElectricBillingAddress2' || $metadata->meta_key == 'ElectricBillingZip' || $metadata->meta_key == 'ElectricBillingCity' || $metadata->meta_key == 'ElectricBillingState') {
                            $options_to_fill["[" . $metadata->meta_key . "]"] = $this->highlight_tag($metadata->meta_value);

                            $electricbilling_address[$metadata->meta_key] = $this->highlight_tag($metadata->meta_value);
                        } else
                            if ($metadata->meta_key == 'Billing first name' || $metadata->meta_key == 'Billing middle name' || $metadata->meta_key == 'Billing last name') {
                                $options_to_fill["[" . $metadata->meta_key . "]"] = $this->highlight_tag($metadata->meta_value);

                                $normal_billing_name[$metadata->meta_key] = $this->highlight_tag($metadata->meta_value);
                            } else
                                if ($metadata->meta_key == 'GasServiceZip' || $metadata->meta_key == 'GasServiceCity' || $metadata->meta_key == 'GasServiceState' || $metadata->meta_key == 'GasServiceAddress' || $metadata->meta_key == 'GasServiceAddress2') {
                                    $options_to_fill["[" . $metadata->meta_key . "]"] = $this->highlight_tag($metadata->meta_value);
                                    $gasservice_address[$metadata->meta_key] = $this->highlight_tag($metadata->meta_value);

                                } else
                                    if ($metadata->meta_key == 'GasBillingAddress' || $metadata->meta_key == 'GasBillingAddress2' || $metadata->meta_key == 'GasBillingZip' || $metadata->meta_key == 'GasBillingCity' || $metadata->meta_key == 'GasBillingState') {
                                        $options_to_fill["[" . $metadata->meta_key . "]"] = $this->highlight_tag($metadata->meta_value);

                                        $gasbilling_address[$metadata->meta_key] = $this->highlight_tag($metadata->meta_value);
                                    } else {
                                        $options_to_fill["[" . $metadata->meta_key . "]"] = $this->highlight_tag($metadata->meta_value);
                                    }

            }

            if (count($service_address) > 0) {
                $address2 = "";

                if (isset($service_address['ServiceAddress2'])) {
                    $address2 = ", " . $service_address['ServiceAddress2'];
                }
                $chek_tag_options_values_not_added_during_leads["[What is the service address?]"] = $service_address['ServiceAddress'] . $address2 . ', ' . $service_address['ServiceCity'] . ', ' . $service_address['ServiceState'] . ', ' . $service_address['ServiceZip'];

                $options_to_fill["[What is the service address?]"] = $service_address['ServiceAddress'] . $address2 . ', ' . $service_address['ServiceCity'] . ', ' . $service_address['ServiceState'] . ', ' . $service_address['ServiceZip'];
            }
            if (count($gasservice_address) > 0) {
                $address2 = "";

                if (isset($gasservice_address['GasServiceAddress2'])) {
                    $address2 = ", " . $gasservice_address['GasServiceAddress2'];
                }

                $chek_tag_options_values_not_added_during_leads["[Gas service address?]"] = $gasservice_address['GasServiceAddress'] . $address2 . ', ' . $gasservice_address['GasServiceCity'] . ', ' . $gasservice_address['GasServiceState'] . ', ' . $gasservice_address['GasServiceZip'];
                $options_to_fill["[Gas service address?]"] = $gasservice_address['GasServiceAddress'] . $address2 . ', ' . $gasservice_address['GasServiceCity'] . ', ' . $gasservice_address['GasServiceState'] . ', ' . $gasservice_address['GasServiceZip'];
            }
            if (count($gasbilling_address) > 0) {
                $address2 = "";

                if (isset($gasbilling_address['GasBillingAddress2'])) {
                    $address2 = ", " . $gasbilling_address['GasBillingAddress2'];
                }

                $chek_tag_options_values_not_added_during_leads["[Gas Billing Address]"] = $gasbilling_address['GasBillingAddress'] . $address2 . ', ' . $gasbilling_address['GasBillingCity'] . ', ' . $gasbilling_address['GasBillingState'] . ', ' . $gasbilling_address['GasBillingZip'];
                $options_to_fill["[Gas Billing Address]"] = $gasbilling_address['GasBillingAddress'] . $address2 . ', ' . $gasbilling_address['GasBillingCity'] . ', ' . $gasbilling_address['GasBillingState'] . ', ' . $gasbilling_address['GasBillingZip'];
            }

            if (count($electricbilling_address) > 0) {
                $address2 = "";

                if (isset($electricbilling_address['ElectricBillingAddress2'])) {
                    $address2 = ", " . $electricbilling_address['ElectricBillingAddress2'];
                }

                $chek_tag_options_values_not_added_during_leads["[Electric Billing Address]"] = $electricbilling_address['ElectricBillingAddress'] . $address2 . ', ' . $electricbilling_address['ElectricBillingCity'] . ', ' . $electricbilling_address['ElectricBillingState'] . ', ' . $electricbilling_address['ElectricBillingZip'];

                $options_to_fill["[Electric Billing Address]"] = $electricbilling_address['ElectricBillingAddress'] . $address2 . ', ' . $electricbilling_address['ElectricBillingCity'] . ', ' . $electricbilling_address['ElectricBillingState'] . ', ' . $electricbilling_address['ElectricBillingZip'];
            }
            if (count($billing_address) > 0) {
                $address2 = "";

                if (isset($billing_address['BillingAddress2'])) {
                    $address2 = ", " . $billing_address['BillingAddress2'];
                }

                $chek_tag_options_values_not_added_during_leads["[Billing Address]"] = $billing_address['BillingAddress'] . $address2 . ', ' . $billing_address['BillingCity'] . ', ' . $billing_address['BillingState'] . ', ' . $billing_address['BillingZip'];

                $options_to_fill["[Billing Address]"] = $billing_address['BillingAddress'] . $address2 . ', ' . $billing_address['BillingCity'] . ', ' . $billing_address['BillingState'] . ', ' . $billing_address['BillingZip'];
            }
            if (count($name_checking) > 0) {

                $chek_tag_options_values_not_added_during_leads["[Authorized Name]"] = $name_checking['Authorized First name'] . ' ' . $name_checking['Authorized Middle initial'] . ' ' . $name_checking['Authorized Last name'];


                $options_to_fill["[Authorized Name]"] = $name_checking['Authorized First name'] . ' ' . $name_checking['Authorized Middle initial'] . ' ' . $name_checking['Authorized Last name'];

            }

            if (count($without_authorized) > 0) {
                $chek_tag_options_values_not_added_during_leads["[Auth Name]"] = $without_authorized['First name'] . ' ' . $without_authorized['Middle initial'] . ' ' . $without_authorized['Last name'];
                $chek_tag_options_values_not_added_during_leads["[Full name]"] = $without_authorized['First name'] . ' ' . $without_authorized['Middle initial'] . ' ' . $without_authorized['Last name'];
                $options_to_fill["[Auth Name]"] = $without_authorized['First name'] . ' ' . $without_authorized['Middle initial'] . ' ' . $without_authorized['Last name'];
                $options_to_fill["[Full name]"] = $without_authorized['First name'] . ' ' . $without_authorized['Middle initial'] . ' ' . $without_authorized['Last name'];
                if (count($name_checking) == 0) {
                    $options_to_fill["[Authorized Name]"] = $without_authorized['First name'] . ' ' . $without_authorized['Middle initial'] . ' ' . $without_authorized['Last name'];
                }

            }
            if (count($normal_billing_name) > 0) {
                $chek_tag_options_values_not_added_during_leads["[Billing full name]"] = $normal_billing_name['Billing first name'] . ' ' . $normal_billing_name['Billing middle name'] . ' ' . $normal_billing_name['Billing last name'];

                $options_to_fill["[Billing full name]"] = $normal_billing_name['Billing first name'] . ' ' . $normal_billing_name['Billing middle name'] . ' ' . $normal_billing_name['Billing last name'];
            }
            if (count($normal_billing_name) == 0) {
                if (isset($options_to_fill["[Gas Billing full name]"])) {
                    $options_to_fill["[Billing full name]"] = $options_to_fill["[Gas Billing full name]"];
                }
                if (isset($options_to_fill["[Authorized Name]"])) {
                    $options_to_fill["[Billing full name]"] = $options_to_fill["[Authorized Name]"];
                }

            }


            //   $scripts = (new ScriptQuestions)->getScripts($workspace_id, $workflow_id, $current_lang,'customer_verification' );
            $scripts = (new ScriptQuestions)->getScriptsUsingFormIDandLanguage($telesale_form_id, $current_lang, 'customer_verification');

            if (count($scripts) > 0) {
                $script_id = $scripts[0]->id;

                $get_questions_to_replace_tags = (new ScriptQuestions)->scriptQuestionsWithStateCommodity($script_id, $leadzipcodestate, $leadcommodity);
                $questions_array = array();
                if (count($get_questions_to_replace_tags) > 0) {

                    foreach ($get_questions_to_replace_tags as $single_question) {
                        $full_question = strtr($single_question->question, $options_to_fill);
                        $answer_option = strtr($single_question->answer, $options_to_fill);
                        $questions_array[] = ['question' => $full_question,
                            'id' => $single_question->id,
                            'answer_option' => $answer_option,
                            'positive_ans' => $single_question->positive_ans,
                            'negative_ans' => $single_question->negative_ans,
                            'is_customizable' => $single_question->is_customizable
                        ];
                    }

                }

            }

            return array(
                'status' => 'success',
                'data' => $questions_array
            );


        } else {
            return array(
                'status' => 'error',
                'message' => 'Invalid Request'
            );
        }

    }

    function highlight_tag($content)
    {
        return "<span class='yellow-bg strong-text'>" . $content . "</span>";
    }

    function input_text_value($value, $name, $added_options_in_form)
    {
        if (isset($added_options_in_form->type)) {
            $option_type = $added_options_in_form->type;
        } else {
            $option_type = "";
        }
        $reference_random_number = rand();
        $edit_box_wrapper = '<span class="edit-on-call-options">';
        $edit_box_wrapper .= "<span class='yellow-bg strong-text hide-display-option-$reference_random_number'>" . $value . "</span>";
        $edit_box_wrapper .= "<span class='edit-value-wrapper show-edit-option-$reference_random_number'><input class='new_lead_data_to_update' type='text' name='" . $name . "' value='" . $value . "'></span>";
        $edit_box_wrapper .= "<span class='options-action'><a href='javascript:void(0);' class='edit-cloned-option action-options-for-edit' data-ref='$reference_random_number' data-edittype='$option_type' data-labeltext='$name'><i class='fa fa-pencil'></i></a> <a href='javascript:void(0);' class='save-cloned-option action-options-for-edit' data-ref='$reference_random_number'><i class='fa fa-check'></i></a>";
        $edit_box_wrapper .= "</span>";
        return $edit_box_wrapper;
    }


    /**
     * This method is used for clone lead
     */
    public function clonelead(Request $request)
    {

        $user = $this->userobj->getClientUsers($request->agent_client_id, $request->agent_user_id);
        $new_reference_id = (new ClientController)->get_client_salesceter_location_code($request->agent_client_id, $user->salescenter_id, $user->location_id);
        $get_new_lead_id = explode('-', $new_reference_id);
        $new_lead_id = $get_new_lead_id [count($get_new_lead_id) - 1];


        $leaddata = Telesales::find($request->telesale_id);
        $leaddata->disposition_id = $request->cloned;
        $leaddata->reviewed_by = Auth::user()->id;
        $leaddata->status = 'decline';
        $leaddata->save();

        // $check_verification_number = 2;
        // $validate_num = $verification_number = "";
        //     while ($check_verification_number > 1){
        //         $verification_number = rand(1000000,9999999);
        //         $validate_num =   (new Telesales)->validateConfirmationNumber($verification_number);
        //         if( !$validate_num ){
        //             $check_verification_number = 0;
        //         }else{
        //             $check_verification_number ++;
        //         }


        //     }

        $lead = new Telesales();
        $lead->client_id = $leaddata->client_id;
        $lead->form_id = $leaddata->form_id;
        $lead->refrence_id = $new_reference_id;
        $lead->user_id = $leaddata->user_id;
        $lead->cloned_by = Auth::user()->id;
        $lead->call_id = $leaddata->call_id;
        $lead->twilio_recording_url = $leaddata->twilio_recording_url;
        $lead->recording_id = $leaddata->recording_id;
        // $lead->verification_number = $leaddata->verification_number;


        $lead->created_at = date('Y-m-d H:i:s');
        $lead->updated_at = date('Y-m-d H:i:s');
        $lead->save();
        $sale_detail = $this->telesalesdataobj->leadDetail($request->telesale_id);
        if (count($sale_detail) > 0) {
            foreach ($sale_detail as $meta_value) {
                $single_lead_Data = array(
                    'telesale_id' => $new_lead_id,
                    'meta_key' => $meta_value->meta_key,
                    'meta_value' => $meta_value->meta_value,
                );
                (new Telesalesdata)->createLeadDetail($single_lead_Data);
            }
        }
        $request->telesale_id = $new_lead_id;

        $telesale_id = $new_lead_id;
        $telesale_form_id = $request->telesale_form_id;
        $workspace_id = $request->form_worksid;
        $workflow_id = $request->form_workflid;
        $current_lang = $request->current_lang;
        $form_fields_data = (new Clientsforms)->getClientFormDetail($telesale_form_id);

        $form_options_to_edit = array();
        if ($form_fields_data) {
            foreach (json_decode($form_fields_data->form_fields) as $field_options) {
                if (!empty($field_options->label_text)) {
                    $form_options_to_edit[$field_options->label_text] = $field_options;
                }
            }
        }


        $lead_meta_data = $this->telesalesdataobj->leadDetail($telesale_id);

        $phones = $this->ClientTwilioNumbers->getWorkflowNumbersbyworkflowid($workflow_id);
        $phonenumbers = array();
        if (count($phones) > 0) {
            foreach ($phones as $phone) {
                $phonenumbers[] = $phone->phonenumber;
            }

        }


        $d = date('Y-m-d H:i:s');
        $date = new \DateTime($d, new \DateTimeZone('UTC'));
        $date->setTimezone(new \DateTimeZone('America/New_York'));
        $cd = $date->format('m-d-Y');
        $ct = $date->format('H:i:s');

        $options_to_fill = array(
            "[Tpvagent]" => $this->highlight_tag(Auth::user()->first_name),
            "[Date]" => $this->highlight_tag($cd),
            "[Time]" => $this->highlight_tag($ct),
            "[ClientPhone]" => $this->highlight_tag(implode(',', $phonenumbers)),

        );

        $clientUtilityProgramTagData = (new ScriptQuestions)->clientUtilityProgramTagData($telesale_form_id);
        if (count($clientUtilityProgramTagData) > 0) {
            $array = (array)$clientUtilityProgramTagData[0];
            foreach ($array as $tag_name => $tags_with_value) {
                $options_to_fill[$tag_name] = $this->highlight_tag($tags_with_value);
            }
        }
        $chek_tag_options_values_not_added_during_leads = array();
        $service_address = array();
        foreach ($lead_meta_data as $metadata) {
            if (isset($form_options_to_edit[$metadata->meta_key])) {
                $added_options_in_form = $form_options_to_edit[$metadata->meta_key];
            } else {
                $added_options_in_form = array();
            }


            if ($metadata->meta_key == 'BillingAddress' || $metadata->meta_key == 'BillingZip' || $metadata->meta_key == 'BillingCity' || $metadata->meta_key == 'BillingState') {
                if (isset($options_to_fill["[Billing Address]"]) && $options_to_fill["[Billing Address]"] != "") {
                    $options_to_fill["[Billing Address]"] = $options_to_fill["[Billing Address]"] . ", " . $this->input_text_value($metadata->meta_value, $metadata->meta_key, $added_options_in_form);
                    $chek_tag_options_values_not_added_during_leads["[Billing Address]"] = $chek_tag_options_values_not_added_during_leads["[Billing Address]"] . ", " . $this->input_text_value($metadata->meta_value, $metadata->meta_key, $added_options_in_form);
                } else {
                    $chek_tag_options_values_not_added_during_leads["[Billing Address]"] = $this->input_text_value($metadata->meta_value, $metadata->meta_key, $added_options_in_form);
                    $options_to_fill["[Billing Address]"] = $this->input_text_value($metadata->meta_value, $metadata->meta_key, $added_options_in_form);
                }


            } else
                if ($metadata->meta_key == 'ServiceZip' || $metadata->meta_key == 'ServiceCity' || $metadata->meta_key == 'ServiceState' || $metadata->meta_key == 'ServiceAddress') {
                    $service_address[$metadata->meta_key] = $this->input_text_value($metadata->meta_value, $metadata->meta_key, $added_options_in_form);

                    if (isset($options_to_fill["[What is the service address?]"]) && $options_to_fill["[What is the service address?]"] != "") {
                        $options_to_fill["[What is the service address?]"] = $options_to_fill["[What is the service address?]"] . ", " . $this->input_text_value($metadata->meta_value, $metadata->meta_key, $added_options_in_form);
                        $chek_tag_options_values_not_added_during_leads["[What is the service address?]"] = $chek_tag_options_values_not_added_during_leads["[What is the service address?]"] . ", " . $this->input_text_value($metadata->meta_value, $metadata->meta_key, $added_options_in_form);

                    } else {
                        $chek_tag_options_values_not_added_during_leads["[What is the service address?]"] = $this->input_text_value($metadata->meta_value, $metadata->meta_key, $added_options_in_form);
                        $options_to_fill["[What is the service address?]"] = $this->input_text_value($metadata->meta_value, $metadata->meta_key, $added_options_in_form);
                    }


                } else {
                    $options_to_fill["[" . $metadata->meta_key . "]"] = $this->input_text_value($metadata->meta_value, $metadata->meta_key, $added_options_in_form);
                    //   $options_to_fill["[".$metadata->meta_key."]"] = $this->highlight_tag($metadata->meta_value);
                    $chek_tag_options_values_not_added_during_leads[$metadata->meta_key] = $this->input_text_value($metadata->meta_value, $metadata->meta_key, $added_options_in_form);
                }


        }
        if (count($service_address) > 0) {
            $chek_tag_options_values_not_added_during_leads["[What is the service address?]"] = $service_address['ServiceAddress'] . ', ' . $service_address['ServiceCity'] . ', ' . $service_address['ServiceState'] . ', ' . $service_address['ServiceZip'];
            $options_to_fill["[What is the service address?]"] = $service_address['ServiceAddress'] . ', ' . $service_address['ServiceCity'] . ', ' . $service_address['ServiceState'] . ', ' . $service_address['ServiceZip'];
        }


        foreach ($form_options_to_edit as $key => $values) {

            if (!isset($chek_tag_options_values_not_added_during_leads[$key])) {

                $options_to_fill["[" . $key . "]"] = $this->input_text_value("", $key, $values);
            }
        }
        //  print_r( $options_to_fill);


        $leadcommodity = $request->leadcommodity;
        $leadzipcodestate = $request->leadzipcodestate;

        $scripts = (new ScriptQuestions)->getScriptsUsingFormIDandLanguage($telesale_form_id, $current_lang, 'customer_verification');
        $questions_array = array();
        if (count($scripts) > 0) {
            $script_id = $scripts[0]->id;
            $get_questions_to_replace_tags = (new ScriptQuestions)->scriptQuestionsWithStateCommodity($script_id, $leadzipcodestate, $leadcommodity);
            if (count($get_questions_to_replace_tags) > 0) {

                foreach ($get_questions_to_replace_tags as $single_question) {
                    $full_question = strtr($single_question->question, $options_to_fill);
                    $answer_option = strtr($single_question->answer, $options_to_fill);
                    $questions_array[] = ['question' => $full_question,
                        'id' => $single_question->id,
                        'answer_option' => $answer_option,
                        'positive_ans' => $single_question->positive_ans,
                        'negative_ans' => $single_question->negative_ans,
                        'is_customizable' => $single_question->is_customizable
                    ];
                }

            }

        }

        return array(
            'status' => 'success',
            'newleadref' => $new_reference_id,
            'newleadid' => $new_lead_id,
            'options' => $form_options_to_edit,
            'data' => $questions_array
        );
    }

    /**
     * This method is used for update lead details
     */
    public function updateleaddata(Request $request)
    {
        $inputs = $request->all();
        if (isset($inputs['leadid']) && $inputs['leadid'] != "") {
            if (count($inputs['options'])) {
                foreach ($inputs['options'] as $meta_key => $value) {
                    $leadData = (new Telesales())->getLeadId($inputs['leadid']);
                    $this->telesalesdataobj->UpdateDetail($leadData->id, $meta_key, $value);
                }
            }

        }
    }

    /**
     * This method is used for cancel lead
     */
    public function cancellead(Request $request)
    {
        /* Start Validation rule */
        $validator = \Validator::make($request->all(), [
            'lead_id' => 'required',
            'reason' => 'required',
        ],['reason.required' => 'Please provide a reason for cancellation.']);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()->all()]);
        }
        /* End Validation rule */
        try {
            $inputs = $request->all();
            $return_url = ($inputs['previous_url']) ? $inputs['previous_url'] : route('profile.leads');
            $lead_id = $inputs['lead_id'];
            $reason = $inputs['reason'];
            $find_lead = Telesales::find($lead_id);

            if (is_null($find_lead)) {
                return response()->json(['status' => 'error', 'errors' => ["The record doesn't exist"]]);
            } else {
                $leaddata = Telesales::find($lead_id);
                $leaddata->cancel_reason = $reason;
                $leaddata->status = config('constants.LEAD_TYPE_CANCELED');
                $leaddata->alert_status = config('constants.TELESALES_ALERT_CANCELLED_STATUS');
                $leaddata->save();

                //check if this lead has child leads or not
                $isChildExist = (new Telesales())->getChildLeads($lead_id);
                if(isset($isChildExist) && $isChildExist->count() > 0){
                    $data['cancel_reason'] = $reason;
                    $data['status'] = config('constants.LEAD_TYPE_CANCELED');
                    $data['alert_status'] = config('constants.TELESALES_ALERT_CANCELLED_STATUS');
                    //update child leads 
                    foreach($isChildExist as $key => $val){
                        (new Telesales())->updateChildLeads($val->id,$data);
                        \Log::info('Child lead details are successfully updated with lead id '.$val->id);
                    }
                }
            }
            $cancelMessage = __('critical_logs.messages.Event_Type_39',['disposition'=>$reason]);
            $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_39');
            $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Cancelled');
            $criticalLogs = (new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$cancelMessage,$lead_id,null,null,$lead_status,$event_type);
            return response()->json(['status' => 'success', 'message' => 'Lead successfully cancelled.', 'url' => $return_url]);
        } catch (Exception $e) {

            return response()->json(['status' => 'error', 'errors' => ["Something went wrong!. Please try again."]]);
        }


    }

    /**
     * This function is used for save user answer details
     */
    public function saveuseranswer(Request $request)
    {

        if (isset($request->question) && $request->question != '') {
            if ($request->new_answer != "") {
                $answer = $request->new_answer;
            } else {
                $answer = $request->answer;
            }
            if (Auth::check()) {
                $verifieduser = Auth::user()->id;
            } else {
                $verifieduser = null;
            }

            $data = array(
                'client_id' => $request->agent_client_id,
                'form_id' => $request->telesale_form_id,
                'lead_id' => $request->telesale_reference_id,
                'tpv_agent_id' => $verifieduser,
                'sales_agent_id' => $request->agent_user_id,
                'language' => $request->current_lang,
                'question' => $request->question,
                'answer' => $answer,
                'verification_answer' => $request->negative_positive_answer,
                'custom_answer_checked' => $request->custom_answer,
                'orignal_answer' => $request->answer
            );
            //$this->CallAnswers->InsertAnswer($data);
            $this->saveAnswer($data);
            return response()->json(['status' => 'success', 'message' => 'Answer successfully saved.']);
        } else {
            return response()->json(['status' => 'error', 'message' => "Something went wrong!. Please try again."]);
        }

    }
    public function saveAnswer($data)
    {
        \Log::info($data);
        try{
            $query = [
                'client_id' => $data['client_id'],
                'lead_id' => $data['lead_id'],
                'question' => $data['question'],
            ];
            CallAnswers::updateOrCreate($query,$data);
        }catch(\Exception $e) {
            \Log::error($e);
        }
    }

    /**
     * This function is used for save user lead answer
     */
    public function saveLeadUserAnswer(Request $request)
    {
        \Log::info('In Save lead answer function');
        \Log::info($request->all());
        $teleSaleData = Telesales::where('refrence_id',$request->telesale_reference_id)->first();
        $get_questions = ScriptQuestions::find($request->qusId);
        $answer = $get_questions->positive_ans;
        if (isset($request->answer) && $request->answer == 1) {
            $answer = $get_questions->positive_ans;
        } else {
            $answer = $get_questions->negative_ans;
        }
        $data = array(
            'client_id' => $teleSaleData->client_id,
            'form_id' => $teleSaleData->form_id,
            'lead_id' => $teleSaleData->id,
            'tpv_agent_id' => Auth::user()->id,
            'sales_agent_id' => array_get($teleSaleData, 'user_id'),
            'language' => 'en',
            'question' => $get_questions->question,
            'answer' => null,
            'verification_answer' => $answer
        );
        \Log::info($data);
        $this->CallAnswers->InsertAnswer($data);
    }

    /**
     * This method is used for downlad contract pdf
     */
    public function contractPdfDownload(Request $request)
    {
        $telesale = Telesales::findOrFail($request->id);
        if (!empty($telesale) && !empty($telesale->contract_pdf)) {
            $exists = Storage::disk('s3')->exists(config()->get('constants.aws_folder') . $telesale->contract_pdf);
            if ($exists) {
                $name = 'contract_package_' . $telesale->id . '.pdf';
                return Storage::disk('s3')->download($telesale->contract_pdf, $name);
                //return Storage::download('file.jpg', $name);
            }
        }
        return redirect()->back()->with('error', 'Something went wrong!. Please try again.');
    }


    /*
    @Author : Ritesh Rana
    @Desc   : get identity Verification question data.
    @Input  :
    @Output : Illuminate\Http\Response
    @Date   : 25/02/2020
    */
    public function identityVerification(Request $request)
    {
        if ($request->telesale_id > 0) {
            $telesale_id = $request->telesale_id;
            $telesale_form_id = $request->form_id;
            $current_lang = $request->language;
            $client_id = $request->client_id;
            $telesale = Telesales::find($telesale_id);
            CallAnswers::where('lead_id',$telesale_id)->delete();
            if (!empty($telesale)) {
                $scripts = (new ScriptQuestions)->getIdentityVerificationScripts($current_lang, 'identity_verification', $client_id);
                if (count($scripts) > 0) {
                    $script_id = $scripts[0]->id;
                    $get_questions_to_replace_tags = (new ScriptQuestions)->scriptQuestions($script_id);
                    $questions_array = array();

                    if (count($get_questions_to_replace_tags) > 0) {

                        foreach ($get_questions_to_replace_tags as $single_question) {

                            $actualQuestion = array_get($single_question, 'question');

                            $actualQuestion = preg_replace_callback("/\[\s*([^\]]+)\s*\]/", function ($word) {
                                return "[" . trim(strtoupper($word[1])) . "]";
                            }, $actualQuestion);

                            $full_question = $actualQuestion;
                            $questions_array[] = ['question' => $full_question,
                                'id' => $single_question->id,
                                'positive_ans' => $single_question->positive_ans != "null" & !empty($single_question->positive_ans) ? $single_question->positive_ans : 'Next',
                                'negative_ans' => $single_question->negative_ans != "null" & !empty($single_question->negative_ans) ? $single_question->negative_ans : 'Cancel',

                            ];
                        }

                    }
                    $fullname = FormField::where('form_id', $telesale->form_id)->where('is_primary', 1)->where('type', 'fullname')->first();
                    $middleNameData = Telesalesdata::where('field_id', $fullname->id)->where('meta_key', 'middle_initial')->where('telesale_id', $telesale->id)->first();

                    return array(
                        'status' => 'success',
                        'question' => $questions_array,
                        'middleName' => $middleNameData->meta_value,
                    );

                } else {
                    return array(
                        'status' => 'error',
                        'message' => 'Script not found.'
                    );
                }

            } else {
                return array(
                    'status' => 'error',
                    'message' => 'Lead not found.'
                );
            }


        } else {
            return array(
                'status' => 'error',
                'message' => 'Invalid request.'
            );
        }

    }


    /*
   @Author : Ritesh Rana
   @Desc   : First Name Identity Verification.
   @Input  :
   @Output : Illuminate\Http\Response
   @Date   : 25/02/2020
   */
    public function firstNameVerify(Request $request)
    {
        if ($request->telesaleid) {

            $telesale = Telesales::select('id','client_id','user_id', 'form_id', 'is_multiple', 'multiple_parent_id')
                ->where('id', $request->telesaleid)->first();
            if (empty($telesale)) {
                return array(
                    'status' => 'error',
                    'message' => 'Sale not found'
                );
            } else {
                if ($telesale) {
                    $data = [
                        'client_id' => $telesale->client_id,
                        'lead_id' => $telesale->id,
                        'form_id' => $telesale->form_id,
                        'sales_agent_id' => $telesale->user_id,
                        'question' => $request->question,
                        'language' => $request->current_lang,
                        'verification_answer' => 'Extra',
                        'custom_answer_checked' => 'true',
                    ];

                    if ($request->scriptTypr == 'firstname' || $request->scriptTypr == 'middelname' || $request->scriptTypr == 'lastname') {

                        $fullname = FormField::where('form_id', $telesale->form_id)->where('is_primary', 1)->where('type', 'fullname')->first();

                        if (!empty($fullname)) {
                            if ($request->scriptTypr == 'firstname') {
                                $middleNameData = Telesalesdata::where('field_id', $fullname->id)->where('meta_key', 'middle_initial')->where('telesale_id', $telesale->id)->first();
                                $firstName = Telesalesdata::where('field_id', $fullname->id)->where('meta_key', 'first_name')->where('telesale_id', $telesale->id)->first();
                                if (strtolower($firstName->meta_value) == strtolower($request->firstname)) {
                                    $data['answer'] = $request->firstname;
                                    $this->saveAnswer($data);
                                    return array(
                                        'status' => 'success',
                                        'message' => 'First name verify',
                                        'data' => $middleNameData->meta_value,
                                    );
                                } else {
                                    return array(
                                        'status' => 'error',
                                        'message' => 'Invalid request.'
                                    );
                                }
                            } else if ($request->scriptTypr == 'middelname') {
                                $middleName = Telesalesdata::where('field_id', $fullname->id)->where('meta_key', 'middle_initial')->where('telesale_id', $telesale->id)->first();
                                if (strtolower($middleName->meta_value) == strtolower($request->middelName)) {
                                    $data['answer'] = $request->middelName;
                                    $this->saveAnswer($data);
                                    return array(
                                        'status' => 'success',
                                        'message' => 'Middel name verify '
                                    );
                                } else {
                                    return array(
                                        'status' => 'error',
                                        'message' => 'Invalid request.'
                                    );
                                }
                            } else if ($request->scriptTypr == 'lastname') {
                                $middleNameData = Telesalesdata::where('field_id', $fullname->id)->where('meta_key', 'middle_initial')->where('telesale_id', $telesale->id)->first();
                                $lastName = Telesalesdata::where('field_id', $fullname->id)->where('meta_key', 'last_name')->where('telesale_id', $telesale->id)->first();
                                if (strtolower($lastName->meta_value) == strtolower($request->lastName)) {
                                    $data['answer'] = $request->lastName;
                                    $this->saveAnswer($data);
                                    return array(
                                        'status' => 'success',
                                        'message' => 'Last name verify ',
                                        'data' => $middleNameData->meta_value,
                                    );
                                } else {
                                    return array(
                                        'status' => 'error',
                                        'message' => 'Invalid request.'
                                    );
                                }
                            }
                        } else {
                            return array(
                                'status' => 'error',
                                'message' => 'Invalid request.'
                            );
                        }
                    } else if ($request->scriptTypr == 'zipcode') {
                        $address = FormField::where('form_id', $telesale->form_id)
                            ->where(function ($que) {
                                $que->where('type', 'address')->orWhere('type', "service_and_billing_address");
                            })
                            ->where('is_primary', 1)->first();

                        if (!empty($address)) {

                            if (!empty($address)) {
                                switch (array_get($address, 'type')) {
                                    case "address":
                                        $zipcode = Telesalesdata::where('field_id', $address->id)->where('meta_key', 'zipcode')->where('telesale_id', $telesale->id)->first();
                                        break;
                                    case "service_and_billing_address":
                                        $zipcode = Telesalesdata::where('field_id', $address->id)->where('meta_key', 'service_zipcode')->where('telesale_id', $telesale->id)->first();
                                        break;
                                    default:
                                        $zipcode = "";
                                        break;
                                }
                            }
                                if ($zipcode->meta_value == $request->zipCode) {
                                    $data['answer'] = $request->zipCode;
                                    $this->saveAnswer($data);
                                    return array(
                                        'status' => 'success',
                                        'message' => 'Zip code verify'
                                    );
                                } else {
                                    return array(
                                        'status' => 'error',
                                        'message' => 'Invalid request.'
                                    );
                                }

                        } else {
                            return array(
                                'status' => 'error',
                                'message' => 'form not found'
                            );
                        }
                    } else if ($request->scriptTypr == 'phonenumber') {

                        $phone_number = FormField::where('form_id', $telesale->form_id)
                            ->where(function ($que) {
                                $que->where('type', 'phone_number');
                            })
                            ->where('is_primary', 1)->first();
                        if (!empty($phone_number)) {

                            $number_data = Telesalesdata::where('field_id', $phone_number->id)->where('meta_key', 'value')->where('telesale_id', $telesale->id)->first();
                            if ($number_data->meta_value == $request->phoneNumber) {
                                $data['answer'] = $request->phoneNumber;
                                $this->saveAnswer($data);
                                return array(
                                    'status' => 'success',
                                    'message' => 'Phone number verify'
                                );
                            } else {
                                return array(
                                    'status' => 'error',
                                    'message' => 'Invalid request.'
                                );
                            }

                        } else {
                            return array(
                                'status' => 'error',
                                'message' => 'form not found'
                            );
                        }
                    } else {
                        return array(
                            'status' => 'error',
                            'message' => 'form not found'
                        );
                    }


                } else {
                    return array(
                        'status' => 'error',
                        'message' => 'form not found'
                    );
                }

            }

        } else {
            return array(
                'status' => 'error',
                'message' => 'Invalid request.'
            );
        }
    }
}
