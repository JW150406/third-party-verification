<?php

namespace App\Http\Controllers\API;

use App\models\Client;
use App\models\Clientsforms;
use App\models\CriticalLogsHistory;
use App\models\Programs;
use App\models\Salesagentdetail;
use App\models\Telesales;
use Carbon\Carbon;
use hisorange\BrowserDetect\Exceptions\Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Log;
use DB;
use App\models\Salescenter;
use App\Traits\LeadTrait;


class CriticalAlertsReportController extends Controller
{
    use LeadTrait;

    public function __construct()
    {
    }

    /**
     * Get details of Critical alert report
     * @param $request
     */
    public function index(Request $request)
    {
        try {
            $client_id = $request->client_id;
            $salescenter_id = $request->salescenter_id;

            $telesales = Telesales::select(
                DB::raw("telesales.id  as lead_id"),
                DB::raw("telesales.refrence_id  as reference_id"),
                DB::raw("CASE
                WHEN  telesales.status = 'cancel' THEN 'Cancelled'
                else 
                'Proceed' end as alert_status"),
                DB::raw("CASE WHEN telesales.status = 'verified' THEN 'Verified' WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' WHEN telesales.status = '" . config()->get('constants.LEAD_TYPE_EXPIRED') . "' THEN 'Expired' WHEN telesales.status = 'self-verified' THEN 'Self verified' ELSE 'Pending' END as lead_status"),
                DB::raw("clients.name as client_name"),
                DB::raw("salescenters.name as salescenter_name"),
                DB::raw("CONCAT_WS(  ', ',salescenters.street,salescenters.city,salescenters.state,salescenters.country,salescenters.zip) as salesceneter_location_address"),
                DB::raw("CONCAT_WS(  ' ',salesagent.first_name,salesagent.last_name ) as salesagent_name"),
                // DB::raw("(date_format(telesales.created_at,'%m/%d/%Y %H:%i %p')) as date_of_submission"),
                'telesales.created_at as date_of_submission','telesales.reviewed_at as date_of_tpv',
                // DB::raw("CASE
                // WHEN  telesales.status = 'pending' THEN ''
                // WHEN telesales.status = 'cancel' THEN ''
                // else
                // date_format(telesales.reviewed_at,'%m/%d/%Y %H:%i %p') end as date_of_tpv"),
                
                'salescenterslocations.name As salescenter_location_name'
            )
                ->leftJoin('users as salesagent', 'salesagent.id', '=', 'telesales.user_id')
                ->leftJoin('salescenters', 'salescenters.id', '=', 'salesagent.salescenter_id')
                ->leftJoin('clients as clients', 'clients.id', '=', 'telesales.client_id')
                ->leftJoin('salescenterslocations','salescenterslocations.salescenter_id', 'salescenters.id');

            $telesales->whereIn('telesales.id',function($q){
                $q->select('lead_id')->from('critical_logs_history')->where('error_type', config()->get('constants.ERROR_TYPE_CRITICAL_LOGS.Critical'))->distinct('lead_id')->get();
            });

            if(!empty($client_id)) {
                $telesales->where('telesales.client_id',$client_id);
            }
            if (!empty($salescenter_id)) {
               $telesales->where('salescenters.id',$salescenter_id);
            }

            // To  date filter
            if (!empty($request->from_date) && !empty($request->to_date)) {
                $from_date = $request->from_date;
                $to_date = $request->to_date;
                $timezone = Auth::user()->timezone;
                $start_date = Carbon::parse($from_date,$timezone)->setTimezone('UTC');
                $end_date = Carbon::parse($to_date,$timezone)->setTimezone('UTC')->addDays(1);
                $telesales->whereBetween('telesales.created_at',[$start_date,$end_date]);
                // if($start_date == $end_date) {
                //     $telesales->whereDate('telesales.created_at',$start_date);
                // } else{
                //     $telesales->whereBetween('telesales.created_at',[$start_date.' 00:00:00',$end_date.' 23:59:59']);
                // }
                
            }

            // To verification date filter
            if (!empty($request->verification_from_date) && !empty($request->verification_to_date)) {
                $verification_from_date = $request->verification_from_date;
                $verification_to_date = $request->verification_to_date;
                $timezone = Auth::user()->timezone;
                $start_date = Carbon::parse($verification_from_date,$timezone)->setTimezone('UTC');
                $end_date = Carbon::parse($verification_to_date,$timezone)->setTimezone('UTC')->addDays(1);
                $telesales->whereBetween('telesales.reviewed_at',[$start_date,$end_date]);
                // if($start_date == $end_date) {
                //     $telesales->whereDate('telesales.reviewed_at',$start_date);
                // } else {
                //     $telesales->whereBetween('telesales.reviewed_at',[$start_date.' 00:00:00',$end_date.' 23:59:59']);
                // }
            }

            $leadStatusSubQuery = "(CASE WHEN telesales.status = 'verified' THEN 'Verified' WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' WHEN telesales.status = 'self-verified' THEN 'Self verified'  ELSE 'Pending' END)";
            $alertStatusSubQuery = "(CASE WHEN telesales.status = 'cancel' THEN 'Cancelled' ELSE 'Proceed' END)";

            // Search logic
            if($request->has('search_text')){
                $search_text = trim($request->get('search_text'));
                $telesales->where(function ($q) use ($search_text, $alertStatusSubQuery, $leadStatusSubQuery){
                    $q->orWhere('refrence_id','LIKE','%'.$search_text.'%')
                        ->orWhereRaw('telesales.status LIKE "%'.$search_text.'%"')
                        ->orWhereRaw('LOWER('. $alertStatusSubQuery .') LIKE "%'.$search_text.'%"')
                        ->orWhereRaw('LOWER('. $leadStatusSubQuery .') LIKE "%'.$search_text.'%"')
                        ->orWhere('clients.name' ,'Like', '%'.$search_text.'%')
                        ->orWhere('salescenters.name' ,'Like', '%'.$search_text.'%')
                        ->orWhere(DB::raw("CONCAT_WS(  ',',salescenters.street,salescenters.city,salescenters.state,salescenters.country,salescenters.zip)"),'Like', '%'.$search_text.'%')
                        ->orWhere(DB::raw("CONCAT_WS(  ' ',salesagent.first_name,salesagent.last_name )"), 'Like', '%'.$search_text.'%');
                });
            }

            // Sorting
            $sort_by = $request->has('sort_by') ? $request->sort_by : 'telesales.created_at';
            $sort_order = $request->has('sort_order') ? $request->sort_order : 'desc';

            $data = $telesales->orderBy("$sort_by","$sort_order")->groupBy('telesales.id')->paginate(10);
            
            foreach($data->items() as $k => $v)
            {
                
                $v->date_of_submission = Carbon::parse($v->date_of_submission)->setTimezone(Auth::user()->timezone)->format('m/d/Y H:i A');
                if(!in_array($v->lead_status,['Pending','Cancelled']))
                {
                    $v->date_of_tpv = Carbon::parse($v->date_of_tpv)->setTimezone(Auth::user()->timezone)->format('m/d/Y H:i A');
                }
                else
                {
                    $v->date_of_tpv = '';
                }
                // echo $k;
            }
            Log::info("Get critical alert report success: " );
            return response()->json([
                'status' => 'success',
                'message' => 'Crtical alert report retrived successfully',
                'number_of_records' => $data->count(),
                'current_page' => $data->currentPage(),
                'perpage' => $data->perPage(),
                'total' => $data->total(),
                'lastPage' => $data->lastPage(),
                'data' =>  $data->items()
            ]);

        } catch (\Exception $e) {
            Log::error("Error while Get sales center data for client: " . $e->getMessage());
            return $this->error("error", "Something went wrong, Please try again later !!", 500);
        }
    }


    /**
     * View Critical Lead By Id
     * @param $id
     */
    public function show($id) {
        try{
            $telesale = Telesales::select('id','client_id','form_id','user_id','refrence_id As reference_id', \DB::raw("(CASE WHEN telesales.status = 'verified' THEN 'Verified' WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' WHEN telesales.status = '" . config()->get('constants.LEAD_TYPE_EXPIRED') . "' THEN 'Expired' WHEN telesales.status = 'self-verified' THEN 'Self verified'  ELSE 'Pending' END) As status"), 'telesales.created_at as lead_submission_on','telesales.updated_at as last_updated_on')->findOrFail($id);
            $telesale->lead_submission_on = Carbon::parse($telesale->lead_submission_on)->setTimezone(Auth::user()->timezone)->format('m/d/Y H:i A');
            $telesale->last_updated_on = Carbon::parse($telesale->last_updated_on)->setTimezone(Auth::user()->timezone)->format('m/d/Y H:i A');
            $form = Clientsforms::withTrashed()->find($telesale->form_id);
            $salesAgent = SalesAgentdetail::withTrashed()->leftjoin("users",'users.id','=','salesagent_detail.user_id')
                ->leftjoin('salescenters','users.salescenter_id','=','salescenters.id')
                ->leftjoin('clients','clients.id','=','users.client_id')
                ->where('user_id',$telesale->user_id)
                ->select('salesagent_detail.id as id',DB::raw("CONCAT_WS(  ' ',users.first_name,users.last_name ) as salesagent_name"),'users.email','salesagent_detail.phone_number','salesagent_detail.agent_type','salescenters.name As salescenter_name','clients.name as client_name')
                ->first();

            $salesAgent['salescenter_location_name'] = (array_get($telesale, 'userWithTrashed') && array_get($telesale->userWithTrashed, 'salesAgentDetailsWithTrashed') && array_get($telesale->userWithTrashed->salesAgentDetailsWithTrashed, 'location')) ? array_get($telesale->userWithTrashed->salesAgentDetailsWithTrashed->location, 'name'): "";

            unset($telesale->userWithTrashed);
            $customFields = getEnableCustomFields($telesale->client_id);
            $telesale_id = $telesale->id;
            $programs = Programs::withTrashed()->leftjoin('telesales_programs','telesales_programs.program_id','=', 'programs.id')
                        ->leftJoin('utilities','utilities.id','=','programs.utility_id')
                        ->leftJoin('customer_types','programs.customer_type_id','=', 'customer_types.id')
                        ->where('telesales_programs.telesale_id', $telesale_id)
                        ->select('programs.id', DB::raw("CASE WHEN customer_types.name IS NULL THEN '' ELSE TRIM(customer_types.name) END as PremiseTypeName") , 'programs.name As ProgramName','code As ProgramCode','rate As Rate','unit_of_measure As UnitOfMeasureName','term As Term','msf As monthlysf','etf As earlyterminationfee','utilities.commodity',
                            DB::raw("CASE WHEN custom_field_1 IS NULL THEN '' ELSE custom_field_1 END as custom_field_1"),
                            DB::raw("CASE WHEN custom_field_2 IS NULL THEN '' ELSE custom_field_2 END as custom_field_2"),
                            DB::raw("CASE WHEN custom_field_3 IS NULL THEN '' ELSE custom_field_3 END as custom_field_3"),
                            DB::raw("CASE WHEN custom_field_4 IS NULL THEN '' ELSE custom_field_4 END as custom_field_4"),
                            DB::raw("CASE WHEN custom_field_5 IS NULL THEN '' ELSE custom_field_5 END as custom_field_5")
                        )->get();

            $leadDetail = array();

            if (!empty($form)) {
                $leadDetail = $form->fields()->with(['telesalesData' => function ($query) use ($telesale_id) {
                    $query->where('telesale_id', $telesale_id);
                }])->get()->toArray();
            }

            //Retrieve lead details with formatting
            $leadFields = $this->leadsDetailsFormatting($telesale);

            $data = [];
            $data['lead_data'] = $telesale;
            $data['sales_agent'] = $salesAgent;
            $data['lead_details'] = $leadFields;
            $data['programs'] = $programs;
            $data['custom_fields'] = $customFields;

            return $this->success("success", "Detail fetched", $data);
        }catch (Exception $e){
            Log::error("Error while get critical " . $e->getMessage());
            return $this->error("error", "Something went wrong, Please try again later !!", 500);
        }
    }


    /**
     * Get Timeline logs for lead
     * @param $id
     */
    public function histroy($id){
        try {
            // $leads = Telesales::where('id',$id)->get();
            
            // if(isset($leads) && !empty($leads))
            // {
            //     return $this->error("error", "Lead is not found with this id", 400);
            // }
            $criticalLogs = CriticalLogsHistory::leftjoin('telesales', 'telesales.id', 'critical_logs_history.lead_id')
                ->leftjoin('users', 'users.id', '=', 'critical_logs_history.sales_agent_id')
                ->where('lead_id', $id)
                ->where('telesales.deleted_at','=',null)
                ->select('critical_logs_history.id','critical_logs_history.created_at','critical_logs_history.user_type', 'critical_logs_history.lead_status', 'critical_logs_history.reason', DB::raw("CONCAT_WS(  ' ',users.first_name,users.last_name ) as agent"),'telesales.status', 'critical_logs_history.related_lead_ids')
                ->get();

            foreach ($criticalLogs as $k => $v) {
                $criticalLogs[$k]->created_at = $v->created_at->setTimezone(Auth::user()->timezone);
                $leadIds = explode(",", $criticalLogs[$k]->related_lead_ids);
                $criticalLogs[$k]->related_lead_ids = implode(", ", $leadIds);

            }
            Log::info("Success get crtical logs history");

            return $this->success('success', 'Get logs successfully', $criticalLogs);
        }catch (\Exception $e){
            Log::error("Error while get Critical logs history " . $e->getMessage());
            return $this->error("error", "Something went wrong, Please try again later !!", 500);
        }
    }
}
