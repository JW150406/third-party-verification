<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\models\TwilioCurrentActivityOfWorker;
use App\models\TwilioLeadCallDetails;
use App\models\TwilioActivityOfWorker;
use Carbon\Carbon;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Log;
use Maatwebsite\Excel\Facades\Excel;


class AgentDashboardController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$user = Auth::user();
		return $this->get_agent_dashboard($request, $user);
	}

	public function get_agent_dashboard($request, $user, $identifier = "web", $token = "")
	{

		return view('admin.dashboard.agent.index');
	}

	/**
	 * For get activity wise workers details
	 *
	 * @param $request
	 */
	public function getCounts(Request $request)
	{
		try {
			Log::info("In getCounts method of AgentDashboardController");
			$availableWorkers = TwilioCurrentActivityOfWorker::leftJoin('user_twilio_id', 'user_twilio_id.twilio_id', '=', 'twilio_current_activity_of_workers.worker_id')
				->leftJoin('users', 'users.id', '=', 'user_twilio_id.user_id')
				->leftJoin('client_twilio_workflowids', 'user_twilio_id.workflow_id', '=', 'client_twilio_workflowids.workflow_id')

				->leftJoin('clients', 'clients.id', '=', 'client_twilio_workflowids.client_id')
				->whereNotNull('user_twilio_id.user_id')
				->select(
					'users.first_name as first_name',
					'users.last_name as last_name',
					DB::raw("( select GROUP_CONCAT(name  SEPARATOR ', ') from clients where id IN (select client_id from client_twilio_workflowids where workflow_id IN (select workflow_id from user_twilio_id where user_twilio_id.user_id = users.id)) ) as client_name"),
					'twilio_current_activity_of_workers.worker_activity_name as activity_name',
					'twilio_current_activity_of_workers.updated_at as last_updated_time'
				)
				->where('worker_activity_name', 'Available')
				->groupBy(DB::raw('twilio_current_activity_of_workers.worker_id'))
				->get();
			// dd($availableWorkers);
			$onCallWorkers = TwilioCurrentActivityOfWorker::leftJoin('user_twilio_id', 'user_twilio_id.twilio_id', '=', 'twilio_current_activity_of_workers.worker_id')
				->leftJoin('users', 'users.id', '=', 'user_twilio_id.user_id')
				->leftJoin('client_twilio_workflowids', 'user_twilio_id.workflow_id', '=', 'client_twilio_workflowids.workflow_id')
				->leftJoin('clients', 'clients.id', '=', 'client_twilio_workflowids.client_id')
				->whereNotNull('user_twilio_id.user_id')
				->select(
					'users.first_name as first_name',
					'users.last_name as last_name',
					DB::raw("( select GROUP_CONCAT(name  SEPARATOR ', ') from clients where id IN (select client_id from client_twilio_workflowids where workflow_id IN (select workflow_id from user_twilio_id where user_twilio_id.user_id = users.id)) ) as client_name"),
					'twilio_current_activity_of_workers.worker_activity_name as activity_name',
					'twilio_current_activity_of_workers.updated_at as last_updated_time'
				)
				->where('worker_activity_name', 'Unavailable')
				->groupBy(DB::raw('twilio_current_activity_of_workers.worker_id'))
				->get();

			$wrapUpWorkers = TwilioCurrentActivityOfWorker::leftJoin('user_twilio_id', 'user_twilio_id.twilio_id', '=', 'twilio_current_activity_of_workers.worker_id')
				->leftJoin('users', 'users.id', '=', 'user_twilio_id.user_id')
				->leftJoin('client_twilio_workflowids', 'user_twilio_id.workflow_id', '=', 'client_twilio_workflowids.workflow_id')
				->leftJoin('clients', 'clients.id', '=', 'client_twilio_workflowids.client_id')
				->whereNotNull('user_twilio_id.user_id')
				->select(
					'users.first_name as first_name',
					'users.last_name as last_name',
					DB::raw("( select GROUP_CONCAT(name  SEPARATOR ', ') from clients where id IN (select client_id from client_twilio_workflowids where workflow_id IN (select workflow_id from user_twilio_id where user_twilio_id.user_id = users.id)) ) as client_name"),
					'twilio_current_activity_of_workers.worker_activity_name as activity_name',
					'twilio_current_activity_of_workers.updated_at as last_updated_time'
				)
				->where('worker_activity_name', 'WrapUp')
				->groupBy(DB::raw('twilio_current_activity_of_workers.worker_id'))
				->get();

			$notAvailableWorkers = TwilioCurrentActivityOfWorker::leftJoin('user_twilio_id', 'user_twilio_id.twilio_id', '=', 'twilio_current_activity_of_workers.worker_id')
				->leftJoin('users', 'users.id', '=', 'user_twilio_id.user_id')
				->leftJoin('client_twilio_workflowids', 'user_twilio_id.workflow_id', '=', 'client_twilio_workflowids.workflow_id')
				->leftJoin('clients', 'clients.id', '=', 'client_twilio_workflowids.client_id')
				->whereNotNull('user_twilio_id.user_id')
				->select(
					'users.first_name as first_name',
					'users.last_name as last_name',
					DB::raw("( select GROUP_CONCAT(name  SEPARATOR ', ') from clients where id IN (select client_id from client_twilio_workflowids where workflow_id IN (select workflow_id from user_twilio_id where user_twilio_id.user_id = users.id)) ) as client_name"),
					'twilio_current_activity_of_workers.worker_activity_name as activity_name',
					'twilio_current_activity_of_workers.updated_at as last_updated_time'
				)
				->whereNotIn('worker_activity_name', ['Available', 'Unavailable', 'WrapUp'])
				->groupBy(DB::raw('twilio_current_activity_of_workers.worker_id'))
				->get();
					
			// For check tab in ajax call of refresh button
			\Log::info('request->tabId'.$request->tabId);
			$tabId = (isset($request->tabId) && $request->tabId != 'undefined') ? $request->tabId : 'tab-1';
			Log::info("Successfully return all the data.");
			return view('admin.dashboard.agent._ajax_agent_details', compact('availableWorkers', 'onCallWorkers', 'wrapUpWorkers', 'notAvailableWorkers','tabId'));

		} catch (\Exception $e) {
			Log::error("getCounts method of AgentDashboardController : Something went wrong!");
		}
	}

	/**
	 * For get activity wise workers details
	 *
	 * @param $request
	 */
	public function getAgentDetailsClientWise(Request $request)
	{
		try {
			Log::info("In getAgentDetailsClientWise method of AgentDashboardController");

			$workerData = TwilioCurrentActivityOfWorker::leftJoin('user_twilio_id', 'user_twilio_id.twilio_id', '=', 'twilio_current_activity_of_workers.worker_id')
				->leftJoin('users', 'users.id', '=', 'user_twilio_id.user_id')
				->leftJoin('client_twilio_workflowids', 'user_twilio_id.workflow_id', '=', 'client_twilio_workflowids.workflow_id')
				->leftJoin('clients', 'clients.id', '=', 'client_twilio_workflowids.client_id')
				->leftJoin('twilio_lead_call_details', 'clients.id', '=', 'twilio_lead_call_details.client_id')
				->whereNotNull('user_twilio_id.user_id')
				->select(
					'twilio_current_activity_of_workers.id',					
					'clients.name as client_name',
					'twilio_current_activity_of_workers.worker_activity_name as activity_name',
					'twilio_current_activity_of_workers.updated_at as last_updated_time',
					'twilio_current_activity_of_workers.worker_id',
					
					DB::raw("count(distinct (case when twilio_current_activity_of_workers.worker_activity_name = 'Available' then twilio_current_activity_of_workers.worker_id end)) as availableWorkerCount"),
					
					DB::raw("count(distinct (case when twilio_current_activity_of_workers.worker_activity_name = 'Unavailable' and twilio_lead_call_details.client_id = clients.id and twilio_lead_call_details.worker_id = twilio_current_activity_of_workers.worker_id and twilio_lead_call_details.current_task_status = 'reserved' then twilio_current_activity_of_workers.worker_id end)) as unavailableWorkerCount"),
					
					DB::raw("count(distinct (case when twilio_current_activity_of_workers.worker_activity_name = 'WrapUp' and twilio_lead_call_details.client_id = clients.id and twilio_lead_call_details.worker_id = twilio_current_activity_of_workers.worker_id and twilio_lead_call_details.current_task_status = 'wrapping' then twilio_current_activity_of_workers.worker_id end)) as wrapUpWorkerCount"),

					DB::raw("count(distinct (case when twilio_current_activity_of_workers.worker_activity_name NOT IN ('Available', 'Unavailable', 'WrapUp')  then twilio_current_activity_of_workers.worker_id end)) as notAvailableWorkerCount")				
				
				)
				->groupBy('clients.id');
			
				if ($request->ajax()) {
					return DataTables::of($workerData)->make(true);						
				}															

		} catch (\Exception $e) {
			Log::error("getCounts method of AgentDashboardController : Something went wrong!");
		}
	}
	


	/**
	 * get twilio leads task report
	 *
	 * @param $request
	 * @return array
	 */
	public function getTwilioLeadTaskReport(Request $request)
	{
		$from_time = Carbon::now('UTC')->subMinutes(5)->toDateTimeString();
		$to_time = Carbon::now('UTC')->toDateTimeString();

		$taskDetailsReport = TwilioLeadCallDetails::leftJoin('clients', 'clients.id', '=', 'twilio_lead_call_details.client_id')
			->leftJoin('telesales', 'telesales.id', '=', 'twilio_lead_call_details.lead_id')
			->select(
				'twilio_lead_call_details.id',
				'telesales.refrence_id',
				DB::raw("(SELECT CONCAT(first_name, ' ', last_name) FROM users WHERE id = (SELECT user_id FROM user_twilio_id WHERE twilio_id = `twilio_lead_call_details`.`worker_id` LIMIT 1)) as TPVAgent"),
				DB::raw("CASE
		            WHEN  twilio_lead_call_details.call_type = '1' THEN 'Customer Inbound'
		            WHEN  twilio_lead_call_details.call_type = '2' THEN 'Agent Inbound'
		            WHEN  twilio_lead_call_details.call_type = '3' THEN 'Self Verification(Email)'
		            WHEN  twilio_lead_call_details.call_type = '4' THEN 'Self Verification(SMS)'
		            WHEN  twilio_lead_call_details.call_type = '5' THEN 'IVR Inbound'
		            WHEN  twilio_lead_call_details.call_type = '6' THEN 'TPV Now Outbound'
		            ELSE '' 
		            END as 'Method'"),
				'twilio_lead_call_details.current_task_status',
				'twilio_lead_call_details.task_created_time',
				'twilio_lead_call_details.task_assigned_time',
				'twilio_lead_call_details.task_wrapup_start_time',
				'twilio_lead_call_details.task_completed_time',
				'twilio_lead_call_details.task_canceled_time',
				'clients.name as Client'
			);

		$taskDetailsReport->where(function ($query) use ($request) {
			$query->where("twilio_lead_call_details.task_created_time", ">=", Carbon::now('UTC')->subHours(12)->toDateTimeString())
				->whereNull('twilio_lead_call_details.task_completed_time')
				->whereNull('twilio_lead_call_details.task_canceled_time');
		});

		$taskDetailsReport->orWhere(function ($query) use ($request, $from_time, $to_time) {
			$query->whereNotNull('twilio_lead_call_details.task_created_time')
				->WhereBetween('twilio_lead_call_details.task_completed_time', [$from_time, $to_time])
				->orWhereBetween('twilio_lead_call_details.task_canceled_time', [$from_time, $to_time]);
		});

//		$taskDetailsReport->whereNotNull('twilio_lead_call_details.task_created_time');
//		$taskDetailsReport->where("twilio_lead_call_details.task_created_time", ">=", Carbon::now('UTC')->subDay()->toDateTimeString());
//		$taskDetailsReport->whereNull('twilio_lead_call_details.task_completed_time');
//		$taskDetailsReport->whereNull('twilio_lead_call_details.task_canceled_time');
//		$taskDetailsReport->orWhereBetween('twilio_lead_call_details.task_completed_time', [$from_time, $to_time]);
//		$taskDetailsReport->orWhereBetween('twilio_lead_call_details.task_canceled_time', [$from_time, $to_time]);
		$taskDetailsReport->orderBy('twilio_lead_call_details.task_created_time', 'desc');
		// $taskDetailsReport->take(5);

		if ($request->ajax()) {
			return DataTables::of($taskDetailsReport)
				->editColumn('task_progress', function ($taskDetailsReport) {
					$task_status = $taskDetailsReport->current_task_status;
					$task_created_time = $taskDetailsReport->task_created_time;
					$task_assigned_time = $taskDetailsReport->task_assigned_time;
					$task_wrapup_start_time = $taskDetailsReport->task_wrapup_start_time;
					$task_completed_time = $taskDetailsReport->task_completed_time;
					$task_canceled_time = $taskDetailsReport->task_canceled_time;
					$progress_bar = view("admin.dashboard.agent.task-progress-bar", compact('task_status', 'task_created_time', 'task_assigned_time', 'task_wrapup_start_time', 'task_completed_time', 'task_canceled_time'))->render();

					return $progress_bar;
				})
				->rawColumns(['task_progress'])
				->make(true);
		}

	}

	// function for Dashboard Agent Report task.
    public function agentActivityReport(Request $request)
    {
        $date = "";
        $startDate = "";
        $endDate = "";
        $timeZone = getClientSpecificTimeZone();
        
        if (isset($request->submitDate) && !empty($request->submitDate)) {
            
            $date = $request->submitDate;
            $startDate = Carbon::parse(explode(' - ', $date)[0],$timeZone)->setTimezone('UTC');
            $endDate = Carbon::parse(explode(' - ', $date)[1],$timeZone)->setTimezone('UTC')->addDays(1);            
            
        }

      

        $activityDetails = TwilioActivityOfWorker::select(
             'twilio_activity_of_workers.created_at',
             'twilio_activity_of_workers.worker_activity_name'
        );
        if(isset($request->id) && $request->id != ""){            
            $activityDetails = $activityDetails->where(function($query) use($request) {
            $query->whereRaw("(SELECT id FROM users WHERE id =(SELECT user_id FROM user_twilio_id WHERE twilio_id = `twilio_activity_of_workers`.`worker_id` LIMIT 1 ) AND users.id = ".$request->id.")");
            });
        }
        $activityDetails->whereBetween('twilio_activity_of_workers.created_at', [$startDate, $endDate])->whereNotNull('twilio_activity_of_workers.worker_id');

        $activityDetails = $activityDetails->get();
        $activity_time = array_column($activityDetails->toArray(), 'created_at');
        foreach($activityDetails as $i => $item) {	
          $start_time = new Carbon($item->created_at);
          if(isset($activity_time[$i+1]))
          {
          	$finish_time = new Carbon($activity_time[$i+1]);	
          }
          else
          {
          	$finish_time = Carbon::now();
          }
          $item->duration = $start_time->diffInSeconds($finish_time);
	    }
        if($request->ajax()) {
            return DataTables::of($activityDetails)
                                ->editColumn('created_at', function ($activityDetails) use($timeZone)  {
                                    return $activityDetails->created_at->setTimezone($timeZone)->format(getDateFormat()." ".getTimeFormat());
                                })
                                ->editColumn('worker_activity_name', function ($activityDetails)  {    
                                    return $activityDetails->worker_activity_name;
                                })
                                ->addColumn('duration', function ($activityDetails)  { 
                                    return getConvertedTime($activityDetails->duration);
                                })
                                ->make(true);
        }        
    }

    /** get verfication status report based on dates
     * @param $client_id
     * @return array
     */
    public function exportAgentActivityReport(Request $request)
    {

        $date = "";
        $startDate = "";
        $endDate = "";
        $timeZone = getClientSpecificTimeZone();
        
        if (isset($request->submitDate) && !empty($request->submitDate)) {
            
            $date = $request->submitDate;
            $startDate = Carbon::parse(explode(' - ', $date)[0],$timeZone)->setTimezone('UTC');
            $endDate = Carbon::parse(explode(' - ', $date)[1],$timeZone)->setTimezone('UTC')->addDays(1);            
            
        }

        $activityDetails = TwilioActivityOfWorker::select(
             'twilio_activity_of_workers.created_at',
             'twilio_activity_of_workers.worker_activity_name'
        );
        if(isset($request->id) && $request->id != ""){            
            $activityDetails = $activityDetails->where(function($query) use($request) {
            $query->whereRaw("(SELECT id FROM users WHERE id =(SELECT user_id FROM user_twilio_id WHERE twilio_id = `twilio_activity_of_workers`.`worker_id` LIMIT 1 ) AND users.id = ".$request->id.")");
            });
        }
        $activityDetails->whereBetween('twilio_activity_of_workers.created_at', [$startDate, $endDate])->whereNotNull('twilio_activity_of_workers.worker_id');

        $activityDetails = $activityDetails->get();
        $activity_time = array_column($activityDetails->toArray(), 'created_at');   
        
        $ActivityData = [];
        $i = 0;
        foreach($activityDetails as $key => $item) {	
			$ActivityData[$i]['Time(EST)'] = $item->created_at->setTimezone($timeZone)->format(getDateFormat()." ".getTimeFormat());
			$ActivityData[$i]['Activity'] = $item->worker_activity_name;
			$start_time = new Carbon($item->created_at);
			if(isset($activity_time[$key+1]))
			{
				$finish_time = new Carbon($activity_time[$key+1]);	
			}
			else
			{
				$finish_time = Carbon::now();
			}
			$ActivityData[$i]['Duration'] = gmdate('H:i:s', $start_time->diffInSeconds($finish_time));            
            $i++;
        }
		$sheetName = "sheet1";
		$sheetTitle = "Activity Duration Report";
        Excel::create($sheetTitle, function ($excel) use ($ActivityData, $sheetName) {

            // Set the title
            $excel->setTitle($sheetName);

            // Chain the setters
            $excel->setCreator('Me')->setCompany('Our Code World');

            $excel->setDescription('Agent Activity Report');

            $excel->sheet($sheetName, function ($sheet) use ($ActivityData) {
                $sheet->setOrientation('landscape');
                $sheet->fromArray($ActivityData, NULL, 'A3');
            });
        })->download('xlsx');
    }

}
