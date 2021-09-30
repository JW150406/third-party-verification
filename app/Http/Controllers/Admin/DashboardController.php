<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AgentPanel\TPVAgent\TPVIVRController;
use App\Http\Controllers\Controller;
use App\models\Client;
use App\models\Commodity;
use App\models\Dispositions;
use App\models\TwilioCurrentActivityOfWorker;
use App\models\Reports;
use App\models\Salescenter;
use App\models\Salesagentdetail;
use App\models\Salescenterslocations;
use App\models\Telesales;
use App\models\Telesalesdata;
use App\models\UserDocuments;
use App\models\UserTwilioId;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use FarhanWazir\GoogleMaps\GMaps;
use App\models\Salesagentlocation;
use App\models\TextEmailStatistics;
use App\models\Zipcodes;
use App\models\UserLocation;
use App\Services\StorageService;
use App\Traits\DashboardTrait;
use App\models\FormScripts;
use App\models\ScriptQuestions;
use Twilio\Rest\Client as TwilioClient;
use DataTables;
use App\models\Utilities;
use App\models\Brandcontacts;
use App\Traits\LeadTrait;
use App\models\Settings;
use App\models\TwilioLeadCallDetails;
use App\Services\WebhookService;
use App\models\TwilioStatisticsSpecificWorkerActivity;


class DashboardController extends Controller
{
    use DashboardTrait, LeadTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
//        $this->middleware('auth:api');
        $this->storageService = new StorageService;
        //$this->middleware('permission:edit', ['only' => ['edit', 'update']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index(Request $request)
    {   
        $user = Auth::user();

        if ($user->hasRole(['tpv_admin', 'tpv_qa'])) {
            return redirect()->route("agentdashboard");
        } else if ($user->access_level == 'salesagent' && $user->roles->isEmpty()) {
            return redirect()->route("my-account");
        } else if ($user->access_level == 'tpvagent') {
            return redirect()->route('tpvagents.sales');
        } else if($user->access_level == 'salescenter'){
            return $this->get_admin_dashboard($request, $user);
        }
        else {
            return $this->get_admin_dashboard($request, $user);
        }

    }

     /**
     * For get profile details for edit
     */
    public function editprofile()
    {
        $user = User::where([
            ['id', '=', Auth::user()->id],
        ])->firstOrFail();
        $twilio_ids = (new UserTwilioId)->getTwilioIds(Auth::user()->id);
        return view('/admin/editprofile', compact('user', 'twilio_ids'));
    }

    /**
     * This method is used for update user profile details
     * @param $request
     * 
     */
    public function updateprofile(Request $request)
    {
        try {
            $userid = Auth::user()->id;
            /* Start Validation rule */
            $validator = \Validator::make($request->all(), [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|email|unique:users,email,' . $userid,
                'password' => $request->password != null ? 'min:6' : '',
                'password_confirmation' => 'same:password',
                'profile_picture' => 'image|mimes:jpg,jpeg,png|max:5120',
            ]);
            Log::info($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => 'error',  'errors' => $validator->errors()->messages()], 422);
            }
            /* End Validation rule */

            $user = User::find($userid);
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;

            if (isset($request->title))
                $user->title = $request->title;

            if (isset($request->twilio_id))
                $user->twilio_id = $request->twilio_id;


            if (!empty($request->password)) {
                $user->password = Hash::make($request->password); //update the password
            }
            
            
            if ($request->hasFile('file')) {
                // Storage::disk('s3')->delete($request->old_url);
                $file = $request->file('file');
                $awsFolderPath = config()->get('constants.aws_folder');
                $filePath = config()->get('constants.USER_PROFILE_PICTURE_UPLOAD_PATH');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $this->storageService->uploadFileToStorage($file, $awsFolderPath, $filePath, $fileName);
                if ($path !== false) {
                    $user->profile_picture = $path;
                }
            }
            if ($request->has('timezone')) {
                $user->timezone = $request->timezone;
            }

            $user->save();

            //        return redirect()->to(route('edit-profile'))
            //            ->with('success', 'Record successfully updated.');
            \Session::put('message', "Record successfully updated.");
            \Log::info("User profile updated with user id: " . array_get($user, 'id'));
            return response()->json(['status' => 'success', 'message' => 'Record successfully updated.'], 200);
        } catch (\Exception $e) {
            \Session::put('error', "Unable to update profile");
            \Log::error("Error while updating user profile: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Unable to update profile'], 500);
        }
    }

    /**
     * This method is used for remove profile photo of auth user
     */
    public function removeProfilePhoto()
    {
        try {
            $profilePhoto = Auth::user()->profile_picture;
            if (!empty($profilePhoto)) {
                Storage::delete($profilePhoto);
                Auth::user()->profile_picture = null;
                Auth::user()->save();
                return response()->json(['status' => 'success', 'message' => 'Your profile photo was successfully deleted.']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Profile photo not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong!. Please try again.']);
        }
    }
    
    /**
     * This method is used for update twilio settings 
     * @param $request
     */
    public function updatetwiliosettings(Request $request)
    {
        $userid = $request->userid;
        (new UserTwilioId)->deleteIds($userid);
        if (isset($request->twilio_ids) && count($request->twilio_ids) > 0) {
            foreach ($request->twilio_ids['workspace_id'] as $key => $added_data) {
                $twilo_id = $request->twilio_ids['worker_id'][$key];
                $workspace_id = $request->twilio_ids['workspace_id'][$key];
                if (!empty($workspace_id) && !empty($twilo_id)) {
                    (new UserTwilioId)->addnew(
                        $userid,
                        $workspace_id,
                        $twilo_id
                    );
                }
            }
        }
        return redirect()->back()
            ->with('success', 'Record successfully updated');
    }

    /**
     * This method is used to redirect to show dashboard as per user's role(access_level)
     */
    public function get_admin_dashboard($request, $user, $identifier = "web", $token = "")
    {
        if ($user->access_level == 'tpv') {
            $clients = Client::where('status', 'active')->get();
        } else if ($user->access_level == 'client') {
            $clients = Client::where('status', 'active')->where('id', $user->client_id)->get();
        } else if ($user->access_level == 'salescenter') {
            $clients = Client::where('status', 'active')->where('id', $user->client_id)->get();
        } else {
            $clients = Client::where('status', 'active')->get();
        }
        $type = ($request->has('type')) ? base64_decode($request->get('type')) : "";
        $cId = ($request->has('cid')) ? base64_decode($request->get('cid')) : "";
        $sId = ($request->has('sid')) ? base64_decode($request->get('sid')) : "";
        
        // $colors = ($request->has('color')) ? implode(',',$request->get('color')) : (($request->has('colors')) ? $request->get('colors') : implode(',',colorArray()));
        // $calenderColors = ($request->has('calenderColor')) ? implode(',',$request->get('calenderColor')) : (($request->has('calenderColors')) ? $request->get('calenderColors') : implode(',',calenderColorArray()));
        
        if ($user->hasRole(config('constants.ROLE_GLOBAL_ADMIN'))) {
            // Get $cId as per the default selected client in dashboard , currently selected client is first from the client object
            if ($cId == ""){
                $firstClient = $clients->first();
                $cId = $firstClient->id;
            }
        } else {
            $cId = $user->client_id;
        }
        
        $salesCenters = SalesCenter::where('status','active')->where('client_id', $cId);

        if($user->access_level == 'client')
        {
            $salesCenters = $salesCenters->where('client_id', $user->client_id);
        } else if ($user->access_level == 'salescenter') {
            $salesCenters = $salesCenters->where('id', $user->salescenter_id);
            $sId = $user->salescenter_id;
        }
        $salesCenters = $salesCenters->get();
        
        //If sales center id parameter is not null then retrieve all locations for specified sales center
        $locations = [];
        if ($sId != "") {
            if ($user->hasRole(config('constants.ROLE_SALES_CENTER_QA'))) {
                $locations = UserLocation::leftJoin('salescenterslocations','user_locations.location_id','=','salescenterslocations.id')->where('salescenter_id', $sId)->where('client_id', $cId)->where('user_locations.user_id',Auth::user()->id)->groupBy('user_locations.location_id')->get();  
            }
            else
            {
                $locations = Salescenterslocations::select('id', 'name')->where('salescenter_id', $sId)->where('client_id', $cId)->get();
            }
        }
        $brands = (new Brandcontacts)->getBrandsByClient($cId);
        
        if ($identifier == "mobile") {
            return view('admin.dashboard.mobile-index', compact('clients', 'type','salesCenters', 'cId', 'sId', 'locations', 'user', 'token', 'identifier','brands'));
        } else {
            return view('admin.dashboard.index', compact('clients', 'type','salesCenters', 'cId', 'sId', 'locations', 'user', 'identifier','brands'));
        }
    }

    /**
     * old admin dashboard method
     */
    public function old()
    {
        $weekenddate = date('Y-m-d');
        $weekstartdate = $this->get_week_difference($weekenddate);
        $where = array('start_date' => $weekstartdate, 'end_date' => $weekenddate);
        $top_salescenters = (new Reports)->getTopSalesCenters($where);
        $active_salesagents = (new User)->OnlineSalesagents();


        $top_offices = (new Reports)->getTopOffice($where);
        $top_agents = (new Reports)->getTopAgents($where);
        $today_verified = (new Reports)->getSalesCount('verified', date('Y-m-d'), date('Y-m-d'));
        $today_decline = (new Reports)->getSalesCount('decline', date('Y-m-d'), date('Y-m-d'));
        $weekly_verified = $this->weeklySalesCount('verified', $weekenddate);
        $weekly_decline = $this->weeklySalesCount('decline', $weekenddate);
        $monthly_decline = $this->MonthlySalesCount('decline', $weekenddate);
        $monthly_verified = $this->MonthlySalesCount('verified', $weekenddate);
        $yearly_decline = $this->YearlySalesCount('decline', $weekenddate);
        $yearly_verified = $this->YearlySalesCount('verified', $weekenddate);


        return view('/admin/dashboard', compact('today_verified', 'today_decline', 'weekly_verified', 'weekly_decline', 'monthly_decline', 'monthly_verified', 'yearly_decline', 'yearly_verified', 'top_agents', 'top_offices', 'top_salescenters', 'active_salesagents'));
    }

    /**
     * This method is used for get client dashboard
     */
    public function get_client_dashboard()
    {
        $client_id = Auth::user()->client_id;
        $weekenddate = date('Y-m-d');
        $weekstartdate = $this->get_week_difference($weekenddate);
        $where = array('start_date' => $weekstartdate, 'end_date' => $weekenddate, 'client_id' => $client_id);
        $top_offices = (new Reports)->getTopOffice($where);
        $top_agents = (new Reports)->getTopAgents($where);

        $today_verified = (new Reports)->getSalesCount('verified', date('Y-m-d'), date('Y-m-d'), $client_id);
        $today_decline = (new Reports)->getSalesCount('decline', date('Y-m-d'), date('Y-m-d'), $client_id);
        $weekly_verified = $this->weeklySalesCount('verified', $weekenddate, $client_id);
        $weekly_decline = $this->weeklySalesCount('decline', $weekenddate, $client_id);
        $monthly_decline = $this->MonthlySalesCount('decline', $weekenddate, $client_id);
        $monthly_verified = $this->MonthlySalesCount('verified', $weekenddate, $client_id);
        $yearly_decline = $this->YearlySalesCount('decline', $weekenddate, $client_id);
        $yearly_verified = $this->YearlySalesCount('verified', $weekenddate, $client_id);
        return view('/admin/clientdashboard', compact('today_verified', 'today_decline', 'weekly_verified', 'weekly_decline', 'monthly_decline', 'monthly_verified', 'yearly_decline', 'yearly_verified', 'top_agents', 'top_offices'));
    }

    /**
     * For show show salescenter's user dashborad
     */
    public function get_salescenter_dashboard()
    {
        $client_id = Auth::user()->client_id;
        $salescenter_id = Auth::user()->salescenter_id;
        $weekenddate = date('Y-m-d');
        $weekstartdate = $this->get_week_difference($weekenddate);
        $where = array('start_date' => $weekstartdate, 'end_date' => $weekenddate, 'client_id' => $client_id, 'salescenter_id' => $salescenter_id);
        // $top_offices = (new Reports)->getTopOffice($where);
        $top_offices = array();
        $top_agents = (new Reports)->getTopAgents($where);

        $today_verified = (new Reports)->getSalesCount('verified', date('Y-m-d'), date('Y-m-d'), $client_id);
        $today_decline = (new Reports)->getSalesCount('decline', date('Y-m-d'), date('Y-m-d'), $client_id);
        $weekly_verified = $this->weeklySalesCount('verified', $weekenddate);
        $weekly_decline = $this->weeklySalesCount('decline', $weekenddate);
        $monthly_decline = $this->MonthlySalesCount('decline', $weekenddate);
        $monthly_verified = $this->MonthlySalesCount('verified', $weekenddate);
        $yearly_decline = $this->YearlySalesCount('decline', $weekenddate);
        $yearly_verified = $this->YearlySalesCount('verified', $weekenddate);
        return view('/admin/salescenterdashboard', compact('today_verified', 'today_decline', 'weekly_verified', 'weekly_decline', 'monthly_decline', 'monthly_verified', 'yearly_decline', 'yearly_verified', 'top_agents', 'top_offices'));
    }

    /**
     * For get weekly sales count
     * @param $status, $week_end_date, $client_id
     */
    function weeklySalesCount($status, $week_end_date, $client_id = "")
    {
        $startdate = $this->get_week_difference($week_end_date);
        return (new Reports)->getSalesCount($status, $startdate, $week_end_date, $client_id);
    }

    /**
     * For get monthly sales count
     * @param $status, $end_date, $client_id
     */
    function MonthlySalesCount($status, $end_date, $client_id = "")
    {
        $startdate = $this->get_month_difference($end_date);
        return (new Reports)->getSalesCount($status, $startdate, $end_date, $client_id);
    }

    /**
     * For get yearly sales count
     * @param $status, $end_date, $client_id
     */
    function YearlySalesCount($status, $end_date, $client_id = "")
    {
        $startdate = $this->get_year_difference($end_date);
        return (new Reports)->getSalesCount($status, $startdate, $end_date, $client_id);
    }

    /**
     * This method is used for get difference in week
     * @param $startdate
     */
    function get_week_difference($startdate)
    {
        return date('Y-m-d', strtotime('-7 days', strtotime($startdate)));
    }

    /**
     * This method is used for get difference in month
     * @param $startdate
     */
    function get_month_difference($startdate)
    {
        return date('Y-m-d', strtotime('-1 months', strtotime($startdate)));
    }

    /**
     * This method is used for get difference in year
     * @param $startdate
     */
    function get_year_difference($startdate)
    {
        return date('Y-m-d', strtotime('-1 year', strtotime($startdate)));
    }

    /**
     * For get details of inactive sales agents 
     */
    public function getinactivesalesagent(Request $request)
    {
        $client_id = "";
        $clients = (new Client)->getClientsList();
        $sale_centers = array();
        $salecenter_id = "";
        $location_id = "";
        $locations = array();
        $users = array();
        if (isset($request->client)) {
            $client_id = $request->client;
            if (!empty($request->salecenter)) {
                $salecenter_id = $request->salecenter;
                $locations = (new Salescenterslocations)->getLocationsInfo($client_id, $salecenter_id);
            }
            if (!empty($request->location)) {
                $location_id = $request->location;
            }
            $sale_centers = (new Salescenter)->getSalesCentersListByClientID($client_id);
            $users = (new user)->InactiveSalesAgentsList($client_id, $salecenter_id, $location_id);
        }


        return view('client.inactivesalesagent.index', compact('clients', 'client_id', 'users', 'sale_centers', 'salecenter_id', 'location_id', 'locations'))
            ->with('i', ($request->input('page', 1) - 1) * 20);
    }

    /**
     * For get details of onboarding agents
     */
    public function onboarding(Request $request)
    {

        if ($request->hasFile('agentdoc')) {
            $userid = $request->userid;
            // Storage::delete($request->old_url);
            $path = $request->file('agentdoc')->store('user/documents');
            $filename = pathinfo($request->file('agentdoc')->getClientOriginalName(), PATHINFO_FILENAME);

            (new UserDocuments)->addDocument($userid, $path, $filename, Auth::user()->id);
            return redirect()->back()
                ->with('success', 'User successfully updated');
        }
        $client_id = "";
        $clients = (new Client)->getClientsList();
        $sale_centers = array();
        $salecenter_id = "";
        $location_id = "";
        $locations = array();
        $users = array();
        if (isset($request->client)) {
            $client_id = $request->client;
            if (!empty($request->salecenter)) {
                $salecenter_id = $request->salecenter;
                $locations = (new Salescenterslocations)->getLocationsInfo($client_id, $salecenter_id);
            }
            if (!empty($request->location)) {
                $location_id = $request->location;
            }
            $sale_centers = (new Salescenter)->getSalesCentersListByClientID($client_id);
            $users = (new user)->getSalesagents($client_id, $salecenter_id, $location_id);
        }


        return view('client.activeagents.index', compact('clients', 'client_id', 'users', 'sale_centers', 'salecenter_id', 'location_id', 'locations'))
            ->with('i', ($request->input('page', 1) - 1) * 20);
    }


    /**
     * This function is only used for test purpose 
     */
    public function testfunction()
    {
        
        // echo "1.01 ". getRateText('1.01')."<br/>";
        // echo "0.91".  getRateText('0.91')."<br/>";
        // echo "2.0990 ". getRateText('2.0990')."<br/>";
        // echo "0.079 ". getRateText('0.079')."<br/>";
        // echo "5.2 ". getRateText('5.2')."<br/>";
        // echo "15.2 ". getRateText('15.2')."<br/>";
        // echo "115.2 ". getRateText('115.2')."<br/>";
        // echo "5.22 ". getRateText('5.22')."<br/>";
        // echo "15.22 ". getRateText('15.22')."<br/>";
        // echo "115.234 ". getRateText('115.234')."<br/>";
        // echo "115.023 ". getRateText('115.023')."<br/>";
        // echo "5.004 ". getRateText('5.004')."<br/>";
        // echo "5.020 ". getRateText('5.020')."<br/>";        
        // dd(1);
        // $data['customerName'] = "Ashish"; 
        // $data['signature'] = "http://tpv-plus.s3.amazonaws.com/app_storage/local/client/leaddata/602502f47efb6.png"; 
        // $pdf = \PDF::loadView('frontend.customer.in_t_and_c', $data);
        // return $pdf->stream('whateveryourviewname.pdf'); 
        // $get_records = DB::table('telesalesdata')->select("meta_value", "telesale_id")
        //     ->where('meta_key', '=', "_programID")
        //     // ->where('telesale_id','=', "10" )
        //     ->whereNotNull('meta_key')
        //     ->get();
        // //dd($get_records);
        // foreach ($get_records as $singlerow) {
        //     $program_id = $singlerow->meta_value;
        //     $leadid = $singlerow->telesale_id;


        //     try {
        //         $program = DB::table('programs')->find($program_id);
        //         if (isset($program->code)) {
        //             $code = $program->code;


        //             DB::table('telesalesdata')
        //                 ->where('telesale_id', $leadid)
        //                 ->where('meta_key', 'Program Code')
        //                 ->update(['meta_value' => $code]);
        //         } else {
        //             echo $program_id . "<br>";
        //         }
        //     } catch (Exception $e) {
        //         echo $e->getMessage();
        //     }
        // }
        echo "done";
    }

    /**
     * This function is used of get data of dashboard report
     */
    public function dashboardReport(Request $request)
    {
        try {
            $client_id = $request->client_id;
            //$sales_centers = $request->sales_centers;
            $start_date = Carbon::parse($request->start_date)->startOfDay();
            $end_date = Carbon::parse($request->end_date)->endOfDay();

            $client = Client::where('status', 'active')->find($client_id);
            if (empty($client)) {
                return response()->json(['status' => false, 'message' => 'Client data is invalid']);
            }

            // verify per record
            $verifiedPer = [
                'today' => 0,
                'WTD' => 0,
                'MTD' => 0,
                'YTD' => 0,
            ];

            $todayDate = Carbon::now()->toDateString();

            // print_r($this->getTeleSalesDateWiseReport($client_id));
            return response()->json([
                'status' => true,
                'data' => [
                    'teleSalesStatusReport' => $this->getTeleSalesDateWiseReport($client_id)
                ]
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Whoops, Something went wrong, please try later.'
            ]);
        }
    }

    /**
     * This method is used for get report leadData
     */
    public function leadDataReport(Request $request)
    {
        try {
            if ($request->ajax()) {
                $totalDecline = (new Telesales)->getLeadDeclineCount('decline');
                $loginClientId = Auth::user()->client_id;

                $telesales = DB::table('telesales')
                    ->leftjoin('users', 'telesales.user_id', 'users.id')
                    ->leftjoin('salescenters', 'salescenters.id', 'users.salescenter_id')
                    ->leftjoin('dispositions', 'dispositions.id', 'telesales.disposition_id')
                    ->where('telesales.status', 'decline');

                if (Auth::user()->hasRole(['sales_center_admin', 'sales_center_qa'])) {
                    $telesales->where('users.salescenter_id', Auth::user()->salescenter_id);
                }
                $telesales->where('telesales.client_id', $request->client_id);
                $telesales->select(DB::raw('count(description) as count')
                    , DB::raw('count(salescenter_id) as salescentercount ')
                    , DB::raw("CONCAT(CONCAT(UCASE(LEFT(salescenters.name, 1)), LCASE(SUBSTRING(salescenters.name, 2)))) as name")
                    , 'description', 'salescenter_id'
                )
                    ->groupBy('salescenter_id', 'dispositions.id')
                    ->orderBy('count', 'desc');

                $telesales = $telesales->get();


                $i = 0;
                $j = 0;
                $data = [];
                foreach ($telesales as $k => $v) {
                    $telesales[$k]->avg = ($telesales[$i++]->count / $totalDecline[0]->count * 100);
                }
                $dataHtml = '';
                foreach ($telesales as $key => $value) {
                    $data[$telesales[$key]->salescenter_id] = [];
                    $data[$telesales[$key]->salescenter_id]['name'] = $telesales[$key]->name;
                    $data[$telesales[$key]->salescenter_id]['dispositions'] = [];
                    for ($m = 0; $m < $telesales->count(); $m++) {
                        if (isset($data[$telesales[$key]->salescenter_id]['dispositions'])) {
                            if ($telesales[$m]->salescenter_id == $telesales[$key]->salescenter_id) {
                                $data[$telesales[$key]->salescenter_id]['dispositions'][$m] = $telesales[$m]->description . ",<span style='text-align:right;'>" . number_format((float)$telesales[$m]->avg, 2, '.', '') . "%</span>";
                            }
                        }
                    }
                }


                $dataHtml = "";
                foreach ($data as $k => $v) {
                    $dispositions_count = count($v['dispositions']);
                    if ($dispositions_count >= 3) {
                        $row = 3;
                    } else
                        $row = $dispositions_count;
                    $count = 0;
                    $dataHtml .= "<tr><th rowspan='" . $row . "'>" . $data[$k]['name'] . "</th>";

                    foreach ($data[$k]['dispositions'] as $value) {
                        $percentage = explode(',', $value);
                        $dataHtml .= "<th>" . $percentage[0] . "</th><td>" . end($percentage) . "</td></tr>";
                        if (++$count > 2)
                            break;
                    }
                }
                return response()->json([
                    'status' => true,
                    'data' => $dataHtml
                ]);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Whoops, Something went wrong, please try later.'
            ]);
        }
    }


    /**
     * get good vs bad sales
     *
     * @param $client_id
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public function goodVsBadSales($client_id, $start_date, $end_date)
    {
        $good_vs_bad_sales = [];
        $totalSales = Telesales::getLeadsByClientId($client_id)
            ->getLeadsByRange($start_date, $end_date)->count();

        if ($totalSales > 0) {
            $goodVsBadSales = Telesales::getLeadsByClientId($client_id)
                ->getLeadsByRange($start_date, $end_date)
                ->get(['id', 'status'])->groupBy('status');

            foreach ($goodVsBadSales as $key => $sale) {
                $array = [];
                $array['label'] = ucfirst($key);
                $array['y'] = round((count($sale) / $totalSales) * 100, 2);
                $array['color'] = $key == 'pending' ? '#F0BC2D' : ($key == 'verified' ? '#339122 ' : ($key == 'hangup' ? '#2FBFFF' : '#DE4444'));
                $good_vs_bad_sales[] = $array;
            }
        }

        return $good_vs_bad_sales;
    }

    /**
     * get d2d sales
     *
     * @param $client_id
     * @param $start_date
     * @param $end_date
     * @return mixed
     */
    public function d2dSales($client_id, $start_date, $end_date)
    {
        $d2dSales = Telesales::getLeadsByClientId($client_id)
            ->getLeadsByRange($start_date, $end_date)
            ->whereHas('user.salesAgentDetails', function ($query) {
                $query->where('agent_type', 'd2d');
            })->get(['id', 'status'])->groupBy('status');

        $d2d_sales = [];

        foreach ($d2dSales as $key => $sale) {
            $array = [];
            $array['label'] = ucfirst($key);
            $array['y'] = count($sale);
            $d2d_sales[] = $array;
        }

        return $d2d_sales;
    }

    /** get tele sales
     * @param $client_id
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public function teleSales($client_id, $start_date, $end_date)
    {
        $taleSalesData = Telesales::getLeadsByClientId($client_id)
            ->getLeadsByRange($start_date, $end_date)
            ->whereHas('user.salesAgentDetails', function ($query) {
                $query->where('agent_type', 'tele');
            })->get(['id', 'status'])->groupBy('status');

        $tele_sales = [];

        foreach ($taleSalesData as $key => $sale) {
            $array = [];
            $array['label'] = ucfirst($key);
            $array['y'] = count($sale);
            $tele_sales[] = $array;
        }

        return $tele_sales;
    }

    /** get declined dispositions
     * @param $client_id
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public function declinedDispositions($client_id, $start_date, $end_date)
    {
        $declinedDispositions = (new Dispositions)->getDispositionList('decline');
        $totalDeclinedSales = Telesales::getLeadsByClientId($client_id)
            ->whereIn('disposition_id', $declinedDispositions->pluck('id'))
            ->getLeadsByRange($start_date, $end_date, 'updated_at')->count();

        $totalDeclinedSales = $totalDeclinedSales == 0 ? 1 : $totalDeclinedSales;
        $declinedDispositionSales = [];

        foreach ($declinedDispositions as $declinedDisposition) {
            $salesCount = Telesales::getLeadsByClientId($client_id)->where('disposition_id', $declinedDisposition->id)->getLeadsByRange($start_date, $end_date, 'updated_at')->count();
            $sales = ($salesCount * 100) / $totalDeclinedSales;
            $declinedDispositionSales[] = [
                'name' => $declinedDisposition->description,
                'sales' => round($sales, 2)
            ];
        }

        $declinedDispositionSales = collect($declinedDispositionSales)->sortByDesc('sales');
        $updateDDS = [];
        foreach ($declinedDispositionSales as $data) {
            $updateDDS[] = $data;
        }

        return $updateDDS;
    }

    /** get hangup dispositions
     * @param $client_id
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public function hangupDispositions($client_id, $start_date, $end_date)
    {
        $hungupDispositions = (new Dispositions)->getDispositionList('customerhangup');
        $totalHungUpSales = Telesales::getLeadsByClientId($client_id)
            ->whereIn('disposition_id', $hungupDispositions->pluck('id'))
            ->getLeadsByRange($start_date, $end_date, 'updated_at')->count();

        $totalHungupSales = $totalHungUpSales == 0 ? 1 : $totalHungUpSales;
        $hungupDispositionSales = [];

        foreach ($hungupDispositions as $hungupDisposition) {
            $salesCount = Telesales::getLeadsByClientId($client_id)
                ->where('disposition_id', $hungupDisposition->id)
                ->getLeadsByRange($start_date, $end_date, 'updated_at')->count();
            $sales = ($salesCount * 100) / $totalHungupSales;
            $hungupDispositionSales[] = [
                'name' => $hungupDisposition->description,
                'sales' => round($sales, 2)
            ];
        }

        $hungupDispositionSales = collect($hungupDispositionSales)->sortByDesc('sales');
        $updateHDS = [];
        foreach ($hungupDispositionSales as $data) {
            $updateHDS[] = $data;
        }

        return $updateHDS;
    }

    /** get verified Leads
     * @param $client_id
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public function verifiedLeads($client_id, $start_date, $end_date)
    {
        $totalVerifiedLeads = Telesales::getLeadsByClientId($client_id)
            ->getLeadsByRange($start_date, $end_date, 'updated_at')
            ->where('telesales.status', 'verified')->count();

        $verified_leads = [];

        if ($totalVerifiedLeads > 0) {
            $verifiedLeads = Telesales::leftJoin('users', 'users.id', '=', 'telesales.user_id')
                ->leftJoin('salescenters', 'salescenters.id', '=', 'users.salescenter_id')
                ->getLeadsByClientId($client_id)
                ->getLeadsByRange($start_date, $end_date)
                ->where('telesales.status', 'verified')
                ->get(['telesales.id as lead_id', 'salescenters.name as sales_centers_name'])
                ->groupBy('sales_centers_name');

            foreach ($verifiedLeads as $key => $sale) {
                $array = [];
                $array['label'] = ucfirst($key);
                $array['y'] = round((count($sale) / $totalVerifiedLeads) * 100, 2);
                $verified_leads[] = $array;
            }
        }

        return $verified_leads;
    }

    /** get non Verified Leads
     * @param $client_id
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public function nonVerifiedLeads($client_id, $start_date, $end_date)
    {
        $totalNonVerifiedLeads = Telesales::getLeadsByClientId($client_id)
            ->getLeadsByRange($start_date, $end_date, 'updated_at')
            ->where('telesales.status', '!=', 'verified')->count();

        $non_verified_leads = [];

        if ($totalNonVerifiedLeads > 0) {
            $nonVerifiedLeads = Telesales::leftJoin('users', 'users.id', '=', 'telesales.user_id')
                ->leftJoin('salescenters', 'salescenters.id', '=', 'users.salescenter_id')
                ->getLeadsByClientId($client_id)
                ->getLeadsByRange($start_date, $end_date)
                ->where('telesales.status', '!=', 'verified')
                ->get(['telesales.id as lead_id', 'salescenters.name as sales_centers_name'])
                ->groupBy('sales_centers_name');

            foreach ($nonVerifiedLeads as $key => $sale) {
                $array = [];
                $array['label'] = ucfirst($key);
                $array['y'] = round((count($sale) / $totalNonVerifiedLeads) * 100, 2);
                $non_verified_leads[] = $array;
            }
        }

        return $non_verified_leads;
    }

    /** get monthly tracker
     * @param $client_id
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public function monthlyTracker($client_id, $start_date, $end_date)
    {
        $result['start_date'] = Carbon::parse($start_date)->toDateString();
        $result['end_date'] = Carbon::parse($end_date)->toDateString();

        // verified sales report
        $result['vSalesReport'] = Telesales::getLeadsByClientId($client_id)
            ->getLeadsByRange(Carbon::parse($start_date)->toDateString(), Carbon::parse($end_date)->toDateString(), 'updated_at')
            ->groupBy(DB::raw('DATE(updated_at)'))
            ->where('status', 'verified')
            ->get([DB::raw('DATE(updated_at) as date'), DB::raw('COUNT(*) sales')]);

        // hangup sales report
        $result['hSalesReport'] = Telesales::getLeadsByClientId($client_id)
            ->getLeadsByRange(Carbon::parse($start_date)->toDateString(), Carbon::parse($end_date)->toDateString(), 'updated_at')
            ->groupBy(DB::raw('DATE(updated_at)'))
            ->where('status', 'hangup')
            ->get([DB::raw('DATE(updated_at) as date'), DB::raw('COUNT(*) sales')]);

        // decline sales report
        $result['dSalesReport'] = Telesales::getLeadsByClientId($client_id)
            ->getLeadsByRange(Carbon::parse($start_date)->toDateString(), Carbon::parse($end_date)->toDateString(), 'updated_at')
            ->groupBy(DB::raw('DATE(updated_at)'))
            ->where('status', 'decline')
            ->get([DB::raw('DATE(updated_at) as date'), DB::raw('COUNT(*) sales')]);

        return $result;
    }

    /**
     * get top Agent Reports
     *
     * @param $client_id
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public function topAgentReports($client_id, $start_date, $end_date)
    {
        $topAgents = User::leftJoin('telesales', 'telesales.user_id', '=', 'users.id')
            ->where('telesales.created_at', '>', $start_date)
            ->where('telesales.created_at', '<=', $end_date)
            ->where('users.client_id', $client_id)
            ->where('users.access_level', 'salesagent')
            ->groupBy('users.id')->orderByDesc('leads')
            ->limit(5)
            ->get(['users.id as agent_id', 'users.first_name as first_name', 'users.last_name as last_name', DB::raw("COUNT(*) as leads")]);

        $agentReports = [
            "pending" => [],
            "verified" => [],
            "decline" => [],
            "hangup" => []
        ];

        $topAgents = $topAgents->sortBy('leads');

        foreach ($topAgents as $agent) {
            $agentSales = Telesales::getLeadsByClientId($client_id)->where('user_id', $agent->agent_id)->getLeadsByRange($start_date, $end_date)->get(['id', 'status'])->groupBy('status');
            $agentReports['pending'][] = [
                'label' => ucfirst($agent->first_name),
                'y' => isset($agentSales['pending']) ? count($agentSales['pending']) : 0
            ];
            $agentReports['verified'][] = [
                'label' => ucfirst($agent->first_name),
                'y' => isset($agentSales['verified']) ? count($agentSales['verified']) : 0
            ];
            $agentReports['decline'][] = [
                'label' => ucfirst($agent->first_name),
                'y' => isset($agentSales['decline']) ? count($agentSales['decline']) : 0
            ];
            $agentReports['hangup'][] = [
                'label' => ucfirst($agent->first_name),
                'y' => isset($agentSales['hangup']) ? count($agentSales['hangup']) : 0
            ];
        }

        return $agentReports;
    }

    /**
     *  get verified Per Records
     *
     * @param $client_id
     * @param $start
     * @param $end
     * @return false|float|int
     */
    public function verifiedPerRecords($client_id, $start, $end)
    {
        $totalRecords = Telesales::getLeadsByClientId($client_id)
            ->getLeadsByRange($start, $end, 'updated_at')
            ->count();


        $verified = Telesales::getLeadsByClientId($client_id)
            ->getLeadsByRange($start, $end, 'updated_at')
            ->where('status', 'verified')
            ->count();

        return $totalRecords == 0 ? 0 : round($verified / $totalRecords * 100, 2);
    }


    /** get tele sales report based on dates
     * @param $client_id
     * @return array
     */
    public function getTeleSalesDateWiseReport($client_id)
    {


        $teleSalesStatusData = [];
        //$client_id = Auth::user()->client_id;

        $teleSalesStatusData['Today'] = Telesales::GetLeadsCountBasedOnLoginUser(Carbon::now()->startOfDay(), Carbon::now()->endOfDay(), $client_id);
        $teleSalesStatusData['WTD'] = Telesales::GetLeadsCountBasedOnLoginUser(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek(), $client_id);
        $teleSalesStatusData['MTD'] = Telesales::GetLeadsCountBasedOnLoginUser(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth(), $client_id);
        $teleSalesStatusData['YTD'] = Telesales::GetLeadsCountBasedOnLoginUser(Carbon::now()->startOfYear(), Carbon::now()->endOfYear(), $client_id);
        // dd($teleSalesStatusData);
        $taleSalesStatusList = Telesales::select('status')->groupBy('status')->get();

        $teleSalesLeadsData = [];
        foreach ($taleSalesStatusList as $sKey => $sValue) {
            foreach ($teleSalesStatusData as $rKey => $rValue) {
                $leadsTotal = $teleSalesStatusData[$rKey]->sum();
                $teleSalesLeadsData[$sValue->status]['status'] = config('constants.VERIFICATION_STATUS_CHART.' . ucfirst($sValue->status));//ucfirst($sValue->status);
                $teleSalesLeadsData[$sValue->status][$rKey] = (isset($rValue[$sValue->status]) ? $rValue[$sValue->status] . ',' . number_format((float)$rValue[$sValue->status] / $leadsTotal * 100, 2, '.', '') . '%' : '0,0.00%');

            }
        }

        return $teleSalesLeadsData;
    }


    /** get verfication status report based on dates
     * @param $client_id
     * @return array
     */
    public function getVerificationStatusDonutChart(Request $request)
    {
        $client_id = $request->client_id;

        $start_date = Carbon::parse($request->start_date)->startOfDay();
        $end_date = Carbon::parse($request->end_date)->endOfDay();

        $client = Client::where('status', 'active')->find($client_id);
        if (empty($client)) {
            return response()->json(['status' => false, 'message' => 'Client data is invalid']);
        }

        $taleSalesStatusCount['statusList'] = Telesales::groupBy('status')->select('status', DB::raw("(CASE WHEN status = 'verified' THEN 'Verified' WHEN status = 'cancel' THEN 'Cancelled' WHEN status = 'decline' THEN 'Declined' WHEN status = 'hangup' THEN 'Disconnected' ELSE 'Pending' END) as status"))->pluck('status');

        $query = Telesales::getLeadsByClientId($client_id)
            ->getLeadsByRange($start_date, $end_date, 'telesales.created_at')
            ->leftJoin('users', 'users.id', '=', 'telesales.user_id')
            ->select(DB::raw('COUNT(telesales.status) as value'), DB::raw("(CASE WHEN telesales.status = 'verified' THEN 'Verified' WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' ELSE 'Pending' END) as name"));
        if (Auth::user()->hasRole(['sales_center_admin', 'sales_center_qa'])) {
            $query->where('users.salescenter_id', Auth::user()->salescenter_id);
        }
        $taleSalesStatusCount['reportData'] = $query->groupBy('telesales.status')->get()->toJson();
        return response()->json([
            'status' => true,
            'data' => $taleSalesStatusCount
        ]);
    }

    /** get verfication status report based on dates
     * @param $client_id
     * @return array
     */
    public function exportVerificationStatusReport(Request $request)
    {

        if(Auth::check())
        {
            $timeZone = Auth::User()->timezone;
        }
        else
            $timeZone = getClientSpecificTimeZone();
        /* Returns leads statuses by its category */
        $status = $this->retrieveLeadStatus(config()->get('constants.DASHBOARD_LEAD_CATEGORIES_REVERSE.'.$request->status));
        $startDate = Carbon::parse($request->startDate,$timeZone)->setTimezone('UTC');
        $endDate = Carbon::parse($request->endDate,$timeZone)->setTimezone('UTC')->addDays(1);

        $verificationMethod = ($request->has('verificationMethod')) ? config()->get('constants.VERIFICATION_METHOD_FOR_REPORT.'.$request->get('verificationMethod')) : '';

        if($request->has('calender_day') && !empty($request->calender_day))
        {   
            $month = ($request->has('month')) ? $request->get('month') : Carbon::now()->format('m');
            $year = ($request->has('year')) ? $request->get('year') : Carbon::now()->format('Y');

            $startDate = Carbon::parse($year."-".$month,$timeZone)->startOfMonth()->addDays($request->get('calender_day') - 1)->setTimezone('UTC');
            $endDate = Carbon::parse($year."-".$month,$timeZone)->startOfMonth()->addDays($request->get('calender_day'))->setTimezone('UTC');

            // $startDate = Carbon::parse($year."-".$month,$timeZone)->startOfMonth()->addDays($request->get('calenderDay') - 1)->setTimezone('UTC');
            //     $endDate = Carbon::parse($year."-".$month,$timeZone)->startOfMonth()->addDays($request->get('calenderDay'))->setTimezone('UTC');
        }
        $telesales = $this->getLeads($request->clientId,$request->brand,$startDate,$endDate,$request->sales_center_id,$request->location_id,$request->agent_id,$status,$request->channelType,$verificationMethod,$request->program_id,$request->utility_name,$request->state);

        if(isset($request->commodity_type))
        { 
            
            $telesales = $this->commodityExport($request->clientId, $startDate, $endDate,$request->brand,config()->get('constants.DASHBOARD_LEAD_CATEGORIES_REVERSE.'.$request->status),$request->commodity_type,$request->sales_center_id,$request->location_id,$request->locationCommodity);
        }

        $telesales = $telesales->select('telesales.*', DB::raw("(CASE WHEN telesales.status = 'verified' THEN 'Verified' WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' WHEN telesales.status = 'pending' THEN 'Pending' WHEN telesales.status = 'self-verified' THEN 'Self verified' ELSE ' ' END) as statusname"), DB::raw("(GROUP_CONCAT(commodities.name SEPARATOR ', ')) as `commodity_name`"),'salescenterslocations.name as salescenterlocation','zip_codes.state');
        $telesales->leftJoin('form_commodities', 'form_commodities.form_id', '=', 'telesales.form_id');
        $telesales->leftJoin('commodities', 'commodities.id', '=', 'form_commodities.commodity_id');
        $telesales->leftJoin('users', 'users.id', '=', 'telesales.user_id');
        $telesales->leftJoin('salescenterslocations', 'salescenterslocations.id', '=', 'users.location_id');
        // $telesales->leftjoin('telesales_zipcodes','telesales_zipcodes.telesale_id','=','telesales.id');
        // $telesales->leftjoin('zip_codes','zip_codes.id','=','telesales_zipcodes.zipcode_id');
        $telesales->groupBy('telesales.id','form_commodities.form_id');

        $telesales->orderBy('id', 'asc');

        $telesalesLeadsData = $telesales->get();
        
        
        
        $taleSalesStatusData = [];
        $i = 0;
        foreach ($telesalesLeadsData as $key => $value) {
            $channel = '';
            if (!empty($value->userWithTrashed) && !empty($value->userWithTrashed->salesAgentDetailsWithTrashed->agent_type)) {
                $channel = config()->get('constants.DASHBOARD_CHANNEL_CATEGORIES_FOR_DISPLAY.'.$value->userWithTrashed->salesAgentDetailsWithTrashed->agent_type);
            }
            $zipcode = '';
            $getZipcode = Telesalesdata::where('meta_key', 'service_zipcode')->where('telesale_id', $value->id)->first();
            if ($getZipcode) {
                $zipcode = $getZipcode->meta_value;
            }

            $tpv_agent = 'NA';
            if (!empty($value->reviewed_by)) {
                $tpv_agent_user = User::withTrashed()->find($value->reviewed_by);
                if ($tpv_agent_user && ($value->verification_method == 1 || $value->verification_method == 2)) {
                    $tpv_agent = $tpv_agent_user->full_name;
                }
            }
            $date = $value->created_at->setTimezone($timeZone)->format(getDateFormat()." ".getTimeFormat());
            $taleSalesStatusData[$i]['Lead#'] = $value->refrence_id;
            $taleSalesStatusData[$i]['Status'] = $value->statusname;
            $taleSalesStatusData[$i]['Date'] = $date;
            $taleSalesStatusData[$i]['Sales Agent'] = $value->user()->withTrashed()->first()->first_name.' '.$value->user()->withTrashed()->first()->last_name;
            $taleSalesStatusData[$i]['Sales Center'] = $value->userWithTrashed->salescenter->name;
            $taleSalesStatusData[$i]['Sales Center Location'] = $value->salescenterlocation;
            $taleSalesStatusData[$i]['State'] = $value->state;
            $taleSalesStatusData[$i]['Channel'] = $channel;
            $taleSalesStatusData[$i]['Commodity'] = $value->commodity_name;
            // $taleSalesStatusData[$i]['Zipcode'] = $zipcode;
            $taleSalesStatusData[$i]['TPV Agent'] = $tpv_agent;
            $verification_method = '';
            if ($value->verification_method == 1)
                $verification_method = config()->get('constants.VERIFICATION_METHOD_FOR_DISPLAY.1');
            if ($value->verification_method == 2)
                $verification_method = config()->get('constants.VERIFICATION_METHOD_FOR_DISPLAY.2');
            if ($value->verification_method == 3)
                $verification_method = config()->get('constants.VERIFICATION_METHOD_FOR_DISPLAY.3');
            if ($value->verification_method == 4)
                $verification_method = config()->get('constants.VERIFICATION_METHOD_FOR_DISPLAY.4');
            if ($value->verification_method == 6)
                $verification_method = config()->get('constants.VERIFICATION_METHOD_FOR_DISPLAY.6');
            if ($value->verification_method == 5)
                $verification_method = config()->get('constants.VERIFICATION_METHOD_FOR_DISPLAY.5');

            $taleSalesStatusData[$i]['Verification Method'] = $verification_method;
            $i++;
        }
        $sheetName = $request->sheet_name;
        Excel::create($request->sheet_title, function ($excel) use ($taleSalesStatusData, $sheetName) {

            // Set the title
            $excel->setTitle($sheetName);

            // Chain the setters
            $excel->setCreator('Me')->setCompany('Our Code World');

            $excel->setDescription('Leads Report');

            $excel->sheet($sheetName, function ($sheet) use ($taleSalesStatusData) {
                $sheet->setOrientation('landscape');
                $sheet->fromArray($taleSalesStatusData, NULL, 'A3');
            });
        })->download('xlsx');
    }

    /**
     * get top Agent Reports
     *
     * @param $client_id
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public function getVendorsLeadsBarChart(Request $request)
    {
        $client_id = $request->client_id;
        //$sales_centers = $request->sales_centers;
        $start_date = Carbon::parse($request->start_date)->startOfDay();
        $end_date = Carbon::parse($request->end_date)->endOfDay();

        $client = Client::where('status', 'active')->find($client_id);
        if (empty($client)) {
            return response()->json(['status' => false, 'message' => 'Client data is invalid']);
        }

        $getStatuList = Telesales::getLeadsStatusList();

        $getVendorList = Telesales::leftJoin('users', 'users.id', '=', 'telesales.user_id')
            ->leftJoin('salescenters', 'salescenters.id', '=', 'users.salescenter_id')
            ->getLeadsByClientId($client_id)
            ->getLeadsByRange($start_date, $end_date)
            ->groupBy('salescenters.id');
        if (Auth::user()->hasRole(['sales_center_admin', 'sales_center_qa'])) {
            $getVendorList->where('users.salescenter_id', Auth::user()->salescenter_id);
        }
        $getVendorList = $getVendorList->select('salescenters.id', 'salescenters.name', DB::raw("(CASE WHEN telesales.status = 'verified' THEN 'Verified' WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' ELSE 'Pending' END) as status"))
            ->get();

        $agentReports['vendorList'] = [];
        $agentReports['leads'] = [];
        $agentReports['status'] = [];

        //$getAgentsList = $getAgentsList->sortBy('leads');

        foreach ($getVendorList as $vendor) {
            $agentReports['vendorList'][] = ucfirst($vendor->name) . '&' . $vendor->id;
            foreach ($getStatuList as $statusList) {
                $getUpdatedStatusName = config('constants.VERIFICATION_STATUS_CHART_LEADS.' . $statusList->status);

                $getUpdatedStatusName = config('constants.VERIFICATION_STATUS_CHART_LEADS.' . $statusList->status);

                $getVendorCount = Telesales::leftJoin('users', 'users.id', '=', 'telesales.user_id')
                    ->leftJoin('salescenters', 'salescenters.id', '=', 'users.salescenter_id')
                    ->whereBetween('telesales.created_at', [$start_date, $end_date])
                    ->where([['telesales.client_id', $client_id], ['salescenters.id', $vendor->id], ['telesales.status', $getUpdatedStatusName]])
                    ->groupBy('telesales.status')
                    ->count();

                $agentReports['leads'][$statusList->status][] = $getVendorCount;
            }
        }
        if (count($agentReports['leads']) > 0) {
            $agentReports['status'] = $getStatuList->pluck('status')->toJson();
            $finalarray = $agentReports;
        } else {
            $finalarray = [];
        }
        return $finalarray;
    }

    /**
     * get vender leads report
     *
     * @param $client_id
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public function getVendorsLeadsPieChart(Request $request)
    {
        $client_id = $request->client_id;
        //$sales_centers = $request->sales_centers;
        $start_date = Carbon::parse($request->start_date)->startOfDay();
        $end_date = Carbon::parse($request->end_date)->endOfDay();

        $client = Client::where('status', 'active')->find($client_id);

        if (empty($client)) {
            return response()->json(['status' => false, 'message' => 'Client data is invalid']);
        }

        $getStatuList = Telesales::getLeadsStatusList();
        $getLeadsList = [];
        $status_leads = "";

        foreach ($getStatuList as $status) {
            $getUpdatedStatusName = config('constants.VERIFICATION_STATUS_CHART_LEADS.' . $status->status);

            $getLeadsList[$status->status] = Telesales::leftJoin('users', 'users.id', '=', 'telesales.user_id')
                ->leftJoin('salescenters', 'salescenters.id', '=', 'users.salescenter_id')
                ->getLeadsByClientId($client_id)
                ->getLeadsByRange($start_date, $end_date)
                ->where('telesales.status', $getUpdatedStatusName)
                ->groupBy('salescenters.name')
                ->get([DB::raw('COUNT(telesales.id) value'), DB::raw("CONCAT(UCASE(LEFT(salescenters.name, 1)), LCASE(SUBSTRING(salescenters.name, 2))) as name, salescenters.id as name1")])
                ->toJson();

        }

        return $getLeadsList;
    }

    /**
     * This method is used to get details for pie chart of d2d good sales
     */
    public function getD2dGoodSalesPieChart(Request $request)
    {

        $client_id = $request->client_id;
        $start_date = Carbon::parse($request->start_date)->startOfDay();
        $end_date = Carbon::parse($request->end_date)->endOfDay();

        $client = Client::where('status', 'active')->find($client_id);

        if (empty($client)) {
            return response()->json(['status' => false, 'message' => 'Client data is invalid']);
        }

        $status = ['verified'];
        $taleSalesData = $this->getData($client_id, $start_date, $end_date, "d2d", $status);

        $getd2dLeads = [];
        $getd2dLeads[0]['name'] = 'Customer Inbound';
        $getd2dLeads[1]['name'] = 'Agent Inbound';
        $getd2dLeads[2]['name'] = 'Email';
        $getd2dLeads[3]['name'] = 'Text';
        $i = 0;
        foreach ($taleSalesData as $key => $value) {
            if ($value->verification_method == 1) {
                $getd2dLeads[0]['value'] = $value->countByMethod;
            } else if ($value->verification_method == 2) {
                $getd2dLeads[1]['value'] = $value->countByMethod;
            } else if ($value->verification_method == 3) {
                $getd2dLeads[2]['value'] = $value->countByMethod;
            } else if ($value->verification_method == 4) {
                $getd2dLeads[3]['value'] = $value->countByMethod;
            }
            $i++;
        }
        return $getd2dLeads;
    }

    /**
     * For get telesale(lead) datails as per value of given parameters
     * @param $client_id, $start_date, $end_date, $agentType, $status
     * 
     */
    public function getData($client_id, $start_date, $end_date, $agentType, $status)
    {

        $telesales = Telesales::getLeadsByClientId($client_id)
            ->getLeadsByRange($start_date, $end_date)
            ->leftjoin('users', 'users.id', 'telesales.user_id');
        if (Auth::user()->hasRole(['sales_center_admin', 'sales_center_qa'])) {
            $telesales->where('users.salescenter_id', Auth::user()->salescenter_id);
        }
        $telesales = $telesales->whereHas('user.salesAgentDetails', function ($query) use ($agentType, $status) {
            $query->where('agent_type', $agentType);
        })->whereIn('telesales.status', $status)->select(
            DB::raw('count(user_id) as countByMethod'),
            'telesales.id', 'user_id', 'verification_method', 'telesales.status')
            ->groupBy('verification_method')
            ->get();

        return $telesales;
    }

    /**
     * This method is used to get detais for pie chart of d2d bad sales
     */
    public function getD2dBadSalesPieChart(Request $request)
    {

        $client_id = $request->client_id;
        $start_date = Carbon::parse($request->start_date)->startOfDay();
        $end_date = Carbon::parse($request->end_date)->endOfDay();
        $client = Client::where('status', 'active')->find($client_id);

        if (empty($client)) {
            return response()->json(['status' => false, 'message' => 'Client data is invalid']);
        }
        $status = ['decline', 'cancel'];
        $taleSalesBadData = $this->getData($client_id, $start_date, $end_date, "d2d", $status);

        $getd2dBadLeads = [];
        $getd2dBadLeads[0]['name'] = 'Customer Inbound';
        $getd2dBadLeads[1]['name'] = 'Agent Inbound';
        $getd2dBadLeads[2]['name'] = 'Email';
        $getd2dBadLeads[3]['name'] = 'Text';
        $i = 0;
        foreach ($taleSalesBadData as $key => $value) {
            if ($value->verification_method == 1) {

                $getd2dBadLeads[0]['value'] = $value->countByMethod;

            } else if ($value->verification_method == 2) {
                $getd2dBadLeads[1]['value'] = $value->countByMethod;
            } else if ($value->verification_method == 3) {
                $getd2dBadLeads[2]['value'] = $value->countByMethod;
            } else if ($value->verification_method == 4) {
                $getd2dBadLeads[3]['value'] = $value->countByMethod;
            }

            $i++;
        }

        return $getd2dBadLeads;
    }

    /**
     * This method is used to get details of pie chart for tele good sales
     */
    public function getTeleGoodSalesPieChart(Request $request)
    {

        $client_id = $request->client_id;
        $start_date = Carbon::parse($request->start_date)->startOfDay();
        $end_date = Carbon::parse($request->end_date)->endOfDay();

        $client = Client::where('status', 'active')->find($client_id);

        if (empty($client)) {
            return response()->json(['status' => false, 'message' => 'Client data is invalid']);
        }
        $status = ['verified'];
        $taleSalesData = $this->getData($client_id, $start_date, $end_date, "tele", $status);
        $getteleLeads = [];
        $getteleLeads[0]['name'] = 'Customer Inbound';
        $getteleLeads[1]['name'] = 'Agent Inbound';
        $getteleLeads[2]['name'] = 'Email';
        $getteleLeads[3]['name'] = 'Text';
        $i = 0;

        foreach ($taleSalesData as $key => $value) {

            if ($value->verification_method == 1) {

                $getteleLeads[0]['value'] = $value->countByMethod;
            } else if ($value->verification_method == 2) {
                $getteleLeads[1]['value'] = $value->countByMethod;
            } else if ($value->verification_method == 3) {
                $getteleLeads[2]['value'] = $value->countByMethod;
            } else if ($value->verification_method == 4) {
                $getteleLeads[3]['value'] = $value->countByMethod;
            }
            $i++;
        }
        return $getteleLeads;
    }

    /**
     * This method is used to get details of pie chart for tele bad sales
     */
    public function getTeleBadSalesPieChart(Request $request)
    {

        $client_id = $request->client_id;
        $start_date = Carbon::parse($request->start_date)->startOfDay();
        $end_date = Carbon::parse($request->end_date)->endOfDay();

        $client = Client::where('status', 'active')->find($client_id);

        if (empty($client)) {
            return response()->json(['status' => false, 'message' => 'Client data is invalid']);
        }
        $status = ['decline', 'cancel'];
        $taleSalesData = $this->getData($client_id, $start_date, $end_date, "tele", $status);

        $gettelebadLeads = [];
        $gettelebadLeads[0]['name'] = 'Customer Inbound';
        $gettelebadLeads[1]['name'] = 'Agent Inbound';
        $gettelebadLeads[2]['name'] = 'Email';
        $gettelebadLeads[3]['name'] = 'Text';
        $i = 0;
        foreach ($taleSalesData as $key => $value) {
            if ($value->verification_method == 1) {
                $gettelebadLeads[0]['value'] = $value->countByMethod;
            } else if ($value->verification_method == 2) {
                $gettelebadLeads[1]['value'] = $value->countByMethod;
            } else if ($value->verification_method == 3) {
                $gettelebadLeads[2]['value'] = $value->countByMethod;
            } else if ($value->verification_method == 4) {
                $gettelebadLeads[3]['value'] = $value->countByMethod;
            }
            $i++;
        }
        return $gettelebadLeads;
    }

    /**
     * get channels vendors leads report
     *
     * @param $client_id
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public function getChannelsLeadsBarChart(Request $request)
    {
        $client_id = $request->client_id;
        //$sales_centers = $request->sales_centers;
        $start_date = Carbon::parse($request->start_date)->startOfDay();
        $end_date = Carbon::parse($request->end_date)->endOfDay();

        $client = Client::where('status', 'active')->find($client_id);
        if (empty($client)) {
            return response()->json(['status' => false, 'message' => 'Client data is invalid']);
        }

        $getStatuList = Telesales::getLeadsStatusList();

        $getVendorList = Telesales::leftJoin('users', 'users.id', '=', 'telesales.user_id')
            ->leftJoin('salescenters', 'salescenters.id', '=', 'users.salescenter_id')
            ->leftJoin('salesagent_detail', 'salesagent_detail.user_id', '=', 'users.id')
            ->whereBetween('telesales.created_at', [$start_date, $end_date])
            ->where([['telesales.client_id', $client_id]]);

        if (Auth::user()->hasRole(['sales_center_admin', 'sales_center_qa'])) {
            $getVendorList->where('users.salescenter_id', Auth::user()->salescenter_id);
        }

        $getVendorList = $getVendorList->groupBy('salescenters.id', 'salescenters.name', 'telesales.status', 'salesagent_detail.agent_type')
            ->get(['salescenters.id', 'salescenters.name', DB::raw("(CASE WHEN telesales.status = 'verified' THEN 'Verified' WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' ELSE 'Pending' END) as status"), 'salesagent_detail.agent_type', DB::raw('count(telesales.id) as count')]);

        $getVendors = $getVendorList->pluck('name', 'id')->unique();
        $getVendorsNames = [];
        foreach ($getVendors as $k => $v) {
            $getVendorsNames[] = $k . "|" . $v;
        }
        $agentReports = [];
        $televendorList = [];
        $d2dvendorList = [];
        $agentReports['leads'] = [];
        $agentReports['status'] = [];

        foreach ($getStatuList as $status) {
            for ($i = 0; $i < count($getVendors); $i++) {
                $agentReports['leads']['d2d'][$status->status][$i] = 0;
                $agentReports['leads']['tele'][$status->status][$i] = 0;
            }
        }

        foreach ($getVendorList as $key => $val) {

            $vender_name = $val->name . '&' . $val->id;
            if (!in_array($vender_name, $televendorList, true)) {
                $televendorList[] = $vender_name;
            }

            if (!in_array($vender_name, $d2dvendorList, true)) {
                $d2dvendorList[] = $vender_name;
            }
            $findIndex = array_search($val->id . "|" . $val->name, $getVendorsNames, true);
            if ($val->agent_type == 'tele') {
                $agentReports['leads']['tele'][$val->status][$findIndex] = $val->count;
            } elseif ($val->agent_type == 'd2d') {
                $agentReports['leads']['d2d'][$val->status][$findIndex] = $val->count;
            }
        }
        // dd($agentReports);
        $agentReports['vendorList']['tele'] = $televendorList;
        $agentReports['vendorList']['d2d'] = $d2dvendorList;

        $agentReports['status'] = $getStatuList->pluck('status')->toJson();
        return $agentReports;
    }


    /**
     * get channels vendors leads report
     *
     * @param $client_id
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public function getCommodityLeadsBarChart(Request $request)
    {
        $client_id = $request->client_id;
        //$sales_centers = $request->sales_centers;
        $start_date = Carbon::parse($request->start_date)->startOfDay();
        $end_date = Carbon::parse($request->end_date)->endOfDay();

        $client = Client::where('status', 'active')->find($client_id);
        if (empty($client)) {
            return response()->json(['status' => false, 'message' => 'Client data is invalid']);
        }

        $getStatuList = Telesales::getLeadsStatusList();

        $getVendorList = Telesales::leftJoin('users', 'users.id', '=', 'telesales.user_id')
            ->leftJoin('salescenters', 'salescenters.id', '=', 'users.salescenter_id')
            ->leftJoin('salesagent_detail', 'salesagent_detail.user_id', '=', 'users.id')
            ->leftJoin('form_commodities', 'form_commodities.form_id', '=', 'telesales.form_id')
            ->leftJoin('commodities', 'commodities.id', '=', 'form_commodities.commodity_id')
            ->whereBetween('telesales.created_at', [$start_date, $end_date])
            ->where([['telesales.client_id', $client_id]])
            ->whereIn('commodities.name', ['Gas', 'Electric']);

        if (Auth::user()->hasRole(['sales_center_admin', 'sales_center_qa'])) {
            $getVendorList->where('users.salescenter_id', Auth::user()->salescenter_id);
        }

        $getVendorList = $getVendorList->groupBy('salescenters.id', 'salescenters.name', 'telesales.status', 'commodities.name')
            ->get(['salescenters.id', 'salescenters.name', DB::raw("(CASE WHEN telesales.status = 'verified' THEN 'Verified' WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' ELSE 'Pending' END) as status"), 'form_commodities.commodity_id', 'commodities.name as commodity_name', DB::raw('count(telesales.id) as count')]);

        $getVendors = $getVendorList->pluck('name', 'id')->unique();
        // dd($getVendors);
        $getVendorsNames = [];
        foreach ($getVendors as $k => $v) {
            $getVendorsNames[] = $k;
        }

        $agentReports = [];
        foreach ($getStatuList as $status) {

            for ($i = 0; $i < count($getVendors); $i++) {
                $agentReports['leads']['gas'][$status->status][$i] = 0;
                $agentReports['leads']['electric'][$status->status][$i] = 0;
                $agentReports['leads']['both'][$status->status][$i] = 0;
            }
        }

        $electricvendorList = [];
        $gasvendorList = [];
        // $agentReports['leads'] = [];
        // $agentReports['status'] = [];
        $bothCommodities = [];

        foreach ($getVendorList as $key => $val) {
            $vender_name = $val->name . '&' . $val->id;
            if (!in_array($vender_name, $electricvendorList, true)) {
                $electricvendorList[] = $val->name . '&' . $val->id;
            }

            if (!in_array($vender_name, $gasvendorList, true)) {
                $gasvendorList[] = $val->name . '&' . $val->id;
            }
            $findIndex = array_search($val->id, $getVendorsNames, true);
            if ($val->commodity_name == 'Electric') {
                $agentReports['leads']['electric'][$val->status][$findIndex] = $val->count;
            } elseif ($val->commodity_name == 'Gas') {
                $agentReports['leads']['gas'][$val->status][$findIndex] = $val->count;
            } else {
                $agentReports['leads']['electric'][$val->status][$findIndex] = 0;
                $agentReports['leads']['gas'][$val->status][$findIndex] = 0;
            }
            $agentReports['leads']['both'][$val->status][$findIndex] = $agentReports['leads']['electric'][$val->status][$findIndex] + $agentReports['leads']['gas'][$val->status][$findIndex];
            $bothCommodities[$val->id][$val->status][$val->commodity_name] = $val;
        }

        $agentReports['vendorList']['electric'] = $electricvendorList;
        $agentReports['vendorList']['gas'] = $gasvendorList;

        $agentReports['status'] = $getStatuList->pluck('status')->toJson();
        return $agentReports;
    }

    /**
     * This method is used for get leads list by status
     */
    public function getTelesalesLeadsListByStatus(Request $request)
    {
        // if ($request->ajax()) {
            $timeZone = getClientSpecificTimeZone();
            if(Auth::check())
            {
                $timeZone = Auth::User()->timezone;
            }
            /* Returns leads statuses by its category */
            $status = $this->retrieveLeadStatus(config()->get('constants.DASHBOARD_LEAD_CATEGORIES_REVERSE.'.$request->status));
            $startDate = Carbon::parse($request->start_date,$timeZone)->setTimezone('UTC');
            $endDate = Carbon::parse($request->end_date,$timeZone)->addDays(1)->setTimezone('UTC');
            // $startDate = Carbon::parse($request->start_date);
            // $endDate = Carbon::parse($request->end_date)->addDays(1);
            $verificationMethod = config()->get('constants.VERIFICATION_METHOD_FOR_REPORT.'.$request->verificaitonMethod);
            if($request->has('calenderDay') && !empty($request->calenderDay))
            {
                if($request->calenderDay == 0)
                {
                    return false;
                }
                $month = ($request->has('month')) ? $request->get('month') : Carbon::now()->format('m');
                $year = ($request->has('year')) ? $request->get('year') : Carbon::now()->format('Y');

                $startDate = Carbon::parse($year."-".$month,$timeZone)->startOfMonth()->addDays($request->get('calenderDay') - 1)->setTimezone('UTC');
                $endDate = Carbon::parse($year."-".$month,$timeZone)->startOfMonth()->addDays($request->get('calenderDay'))->setTimezone('UTC');
            }
            
            $telesales = $this->getLeads($request->client_id,$request->brand,$startDate,$endDate,$request->sales_center_id,$request->locationId,$request->agent_id,$status,$request->channelType,$verificationMethod,$request->programId,$request->utilityName,$request->state);

            // $telesales = $telesales->select('telesales.*', DB::raw("(CASE WHEN telesales.status = 'verified' THEN 'Verified' WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' WHEN telesales.status = 'expired' THEN 'Expired' ELSE 'Pending' END) as status"), DB::raw("(GROUP_CONCAT(commodities.name SEPARATOR ', ')) as `commodity_name`"));
        
            if(isset($request->commoditytype))
            { 
                $telesales = $this->commodityExport($request->client_id, $startDate, $endDate,$request->brand,config()->get('constants.DASHBOARD_LEAD_CATEGORIES_REVERSE.'.$request->status),$request->commoditytype,$request->sales_center_id,$request->locationId,$request->locationCommodity);
            }
            $leadStatusSubQuery = "(CASE WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' ELSE telesales.status END)";
            
            $telesales->leftJoin('form_commodities', 'form_commodities.form_id', '=', 'telesales.form_id');
            $telesales->leftJoin('commodities', 'commodities.id', '=', 'form_commodities.commodity_id');
            $telesales->leftJoin('users', 'users.id', '=', 'telesales.user_id');
            $telesales->leftJoin('salescenterslocations', 'salescenterslocations.id', '=', 'users.location_id');
            $telesales->leftJoin('salesagent_detail', 'users.id', '=', 'salesagent_detail.user_id');
            $telesales->leftJoin('salescenters', 'salescenters.id', '=', 'users.salescenter_id');
            $telesales = $telesales->select('telesales.*',DB::raw("(GROUP_CONCAT(commodities.name SEPARATOR ', ')) as `commodity_name`"),'salescenterslocations.name as salescenter_location','zip_codes.state','salesagent_detail.agent_type','salescenters.name as salescenter_name');
            $telesales->groupBy('telesales.id','form_commodities.form_id');

            $timeZone = getClientSpecificTimeZone();
            if(Auth::check())
            {
                $timeZone = Auth::User()->timezone;
            }

            return DataTables::of($telesales)
                ->addColumn('created_at', function ($telesales) use($timeZone) {
                    $date = '';
                    $date =  $telesales->created_at->setTimezone($timeZone)->format(getDateFormat()." ".getTimeFormat());
                    return $date;
                })
                ->addColumn('channel', function ($telesales) {
                    $channel = '';
                    if (!empty($telesales->userWithTrashed) && !empty($telesales->userWithTrashed->salesAgentDetailsWithTrashed->agent_type)) {
                        $channel = config()->get('constants.DASHBOARD_CHANNEL_CATEGORIES_FOR_DISPLAY.'.$telesales->userWithTrashed->salesAgentDetailsWithTrashed->agent_type);
                    }
                    return $channel;
                })
                ->addColumn('status', function ($telesales) {
                    return config()->get('constants.VERIFICATION_STATUS_CHART.'.ucfirst($telesales->status));
                })
                ->addColumn('sales_agent', function ($telesales) {
                    $sales_agent = '';
                    if (!empty($telesales->userWithTrashed)) {
                        $sales_agent = $telesales->userWithTrashed->full_name;
                    }
                    return $sales_agent;
                })
                ->addColumn('salescenter_name', function ($telesales) {
                    $salescenter_name = '';
                    if (!empty($telesales->userWithTrashed) && !empty($telesales->userWithTrashed->salescenter)) {
                        $salescenter_name = $telesales->userWithTrashed->salescenter->name;
                    }
                    return $salescenter_name;
                })
                ->addColumn('commodity', function ($telesales) {
                    $commodity = '';
                    if (!empty($telesales->commodity_name)) {
                        $commodity = $telesales->commodity_name;
                    }
                    return $commodity;
                })
                ->addColumn('zipcode', function ($telesales) {
                    $zipcode = '';
                    $getZipcode = Telesalesdata::where('meta_key', 'service_zipcode')->where('telesale_id', $telesales->id)->first();
                    if ($getZipcode) {
                        $zipcode = $getZipcode->meta_value;
                    }
                    return $zipcode;
                })
                ->addColumn('tpv_agent', function ($telesales) {
                    $tpv_agent = 'NA';
                    if (!empty($telesales->reviewed_by)) {
                        $tpv_agent_user = User::withTrashed()->find($telesales->reviewed_by);
                        if ($tpv_agent_user && ($telesales->verification_method == 1 || $telesales->verification_method == 2)) {
                            $tpv_agent = $tpv_agent_user->full_name;
                        }
                    }
                    return $tpv_agent;
                })
                ->addColumn('verification_method', function ($telesales) {
                    $verification_method = '';
                    if ($telesales->verification_method == 1)
                    $verification_method = config()->get('constants.VERIFICATION_METHOD_FOR_DISPLAY.1');
                    if ($telesales->verification_method == 2)
                    $verification_method = config()->get('constants.VERIFICATION_METHOD_FOR_DISPLAY.2');
                    if ($telesales->verification_method == 3)
                    $verification_method = config()->get('constants.VERIFICATION_METHOD_FOR_DISPLAY.3');
                    if ($telesales->verification_method == 4)
                    $verification_method = config()->get('constants.VERIFICATION_METHOD_FOR_DISPLAY.4');
                    if ($telesales->verification_method == 5)
                    $verification_method = config()->get('constants.VERIFICATION_METHOD_FOR_DISPLAY.5');
                    if ($telesales->verification_method == 6)
                    $verification_method = config()->get('constants.VERIFICATION_METHOD_FOR_DISPLAY.6');

                    return $verification_method;
                })
                ->make(true);
    }

    /**
     * This method is used for load map as per user's details amd zipcode
     */
    public function loadMapZipcode(Request $request)
    {

        $client_id = $request->clientId;
        $start_date = Carbon::parse($request->startDate)->startOfDay();
        $end_date = Carbon::parse($request->endDate)->endOfDay();
        $brand = ($request->has('brand')) ? $request->get('brand') : '';
        $salesCenterId = ($request->has('salesCenter')) ? $request->get('salesCenter') : "";
        $salesCenterLocation = ($request->has('salesLocationId')) ? $request->get('salesLocationId') : "";
        $leadsByZipcodes = Telesales::getLeadsByClientId($client_id)
            ->getLeadsByRange($start_date, $end_date)
            ->getLeadsByBrand($brand);

            if($salesCenterLocation != "")
                if($salesCenterLocation == "all")
                {
                    $leadsByZipcodes = $leadsByZipcodes->getLeadsBySalesCenter($salesCenterId);
                }
                else
                {
                    $leadsByZipcodes = $leadsByZipcodes->getLeadsBySCLocation($salesCenterLocation);
                }
            elseif($salesCenterId != "")
            {
                $leadsByZipcodes = $leadsByZipcodes->getLeadsBySalesCenter($salesCenterId);
            }
            $leadsByZipcodes = $leadsByZipcodes->select('telesales.status', 'telesales.id as telesale_id', 'telesales.refrence_id as reference_id', DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_address_1' and telesale_id =telesales.id LIMIT 1) as service_address_1"),
                DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_address_2' and telesale_id =telesales.id LIMIT 1) as service_address_2"),
                DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_city' and telesale_id =telesales.id LIMIT 1) as service_city"),
                DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_state' and telesale_id =telesales.id LIMIT 1) as service_state"),
                DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_zipcode' and telesale_id =telesales.id LIMIT 1) as service_zipcode"),
                DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_unit' and telesale_id =telesales.id LIMIT 1) as service_unit"),
                DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_lat' and telesale_id =telesales.id LIMIT 1) as service_lat"),
                DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_lng' and telesale_id =telesales.id LIMIT 1) as service_lng"),
                DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and form_id = telesales.form_id LIMIT 1) and meta_key = 'first_name' and telesale_id = telesales.id LIMIT 1) as first_name"),
                DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and form_id = telesales.form_id LIMIT 1) and meta_key = 'middle_initial' and telesale_id = telesales.id LIMIT 1) as middle_name"),
                DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and form_id = telesales.form_id LIMIT 1) and meta_key = 'last_name' and telesale_id = telesales.id LIMIT 1) as last_name"),
                DB::raw("(select first_name from users where id = telesales.user_id) as agent_first_name"),
                DB::raw("(select last_name from users where id = telesales.user_id) as agent_last_name"),
                DB::raw("(select userid from users where id = telesales.user_id) as agent_id"));
            
                
              $leadsByZipcodes = $leadsByZipcodes->get();

        $serviceAddress = $leadsByZipcodes->toArray();
        $config['map_div_id'] = "map_zipcode";
        $config['map_height'] = "253px";
        $config['cluster'] = false;
        // $config['clusterStyles'] = array(
        //     array(
        //         'url' => asset('images/marker_image/m1.png'),
        //         "width" => "55",
        //         "height" => "55"
        //     ));
        $config['clusterMaxZoom'] = '18';
        $gmap = new GMaps();
        $gmap->initialize($config);
        $serviceLatLngMapping = [];
        $leadDetailsMapping = [];


        if (count($serviceAddress) > 0 && isset($serviceAddress)) {
            foreach ($serviceAddress as $key => $val) {
                $leadDetailsMapping[$val['telesale_id']] = $val;
                if (isset($serviceLatLngMapping[$val['service_lat'] . "," . $val['service_lng']])) {
                    $serviceLatLngMapping[$val['service_lat'] . "," . $val['service_lng']][] = $val['telesale_id'];
                } else
                    $serviceLatLngMapping[$val['service_lat'] . "," . $val['service_lng']][] = $val['telesale_id'];
            }

            foreach ($serviceLatLngMapping as $key => $val) {
                $toolTipText = "<div id = 'infowindow_div'>";
                $hasMultipleLeads = false;
                if (count($val) > 1) {
                    $hasMultipleLeads = true;
                }
                for ($i = 0; $i < count($val); $i++) {
                    $status = $leadDetailsMapping[$val[$i]]['status'];
                    $full_name = implode(" ", array_filter(array($leadDetailsMapping[$val[$i]]['first_name'], $leadDetailsMapping[$val[$i]]['middle_name'], $leadDetailsMapping[$val[$i]]['last_name'])));

                    $address = implode(", ", array_filter(array($leadDetailsMapping[$val[$i]]['service_address_1'], $leadDetailsMapping[$val[$i]]['service_address_2'], $leadDetailsMapping[$val[$i]]['service_unit'], $leadDetailsMapping[$val[$i]]['service_city'], $leadDetailsMapping[$val[$i]]['service_state'], $leadDetailsMapping[$val[$i]]['service_zipcode'])));

                    $agent_info = "(" . implode(" ", array_filter(array($leadDetailsMapping[$val[$i]]['agent_first_name'], $leadDetailsMapping[$val[$i]]['agent_last_name']))) . " - " . $leadDetailsMapping[$val[$i]]['agent_id'] . ")";

                    $route = route('telesales.show', $val[$i]);
                    $toolTipText .= "<p class = 'full-name-lead'>" . $full_name . "</p><p class = 'referece-lead'><strong><a href = '" . $route . "' target='_blank'>" . $leadDetailsMapping[$val[$i]]['reference_id'] . "</a></strong></p>";
                    if (!($hasMultipleLeads)) {
                        $toolTipText .= "<p class = 'address-lead address-lead-single'>" . $address . "</p>";
                    } else {
                        $toolTipText .= "<p class = 'address-lead'>" . $address . "</p>";
                    }
                    $toolTipText .= "<p class = 'agent-lead'><strong>" . $agent_info . "</strong></p>";
                    $status_new = config('constants.VERIFICATION_STATUS_CHART.' . ucfirst($status));
                    // if ($hasMultipleLeads) {
                        switch ($status) {
                            case 'pending':
                                
                                $toolTipText .= "<p class = 'label label-primary status-btn pending text-center'>" . $status_new . " </p>";
                                break;
                            case 'hangup':
                                
                                $toolTipText .= "<p class = 'label label-primary status-btn pending'>" . $status_new . " </p>";
                                break;
                            case 'cancel':
                                
                                $toolTipText .= "<p class = 'label label-primary status-btn cancel'>" . $status_new . " </p>";
                                break;
                            case 'verified':
                                
                                $toolTipText .= "<p class = 'label label-primary status-btn verify'>" . $status_new . " </p>";
                                break;
                            case 'decline':
                                
                                $toolTipText .= "<p class = 'label label-primary status-btn decline'>" . $status_new . " </p>";
                                break;
                            case 'expired':
                                $toolTipText .= "<p class = 'label label-primary status-btn cancel'>" . $status_new . " </p>";
                                break;
                            case 'self-verified':
                                $toolTipText .= "<p class = 'label label-primary status-btn pending text-center'>" . $status_new . " </p>";
                                break;
                        }

                        $toolTipText .= "<div class ='hr'></div>";
                        if ($hasMultipleLeads) {
                            $status = "multiple";
                        }

                    // }

                }
                $marker['position'] = $key;
                $marker['id'] = $val[0];
                $marker['click'] = "addMarkerWithWindow('" . $val[0] . "')";
                $marker['label'] = '';
                $markerIcon = '';
                switch ($status) {
                    case 'pending':
                        $markerIcon = asset('images/marker_image/pins/pin-pending.png');
                        break;
                    case 'hangup':
                        $markerIcon = asset('images/marker_image/pins/pin-pending.png');
                        break;
                    case 'cancel':
                        $markerIcon = asset('images/marker_image/pins/pin-cancelled.png');
                        break;
                    case 'expired':
                        $markerIcon = asset('images/marker_image/pins/pin-cancelled.png');
                        break;
                    case 'verified':
                        $markerIcon = asset('images/marker_image/pins/pin-verified.png');
                        break;
                    case 'decline':
                        $markerIcon = asset('images/marker_image/pins/pin-declined.png');
                        break;
                    default:
                        $markerIcon = asset('images/marker_image/pins/multiple_lead_marker.png');
                        $marker['label'] = count($val);
                        break;
                }
                $toolTipText .= "</div>";
                $marker['infowindow_content'] = $toolTipText;
                $marker['icon'] = $markerIcon;
                $marker['icon_size'] = '20,20';
                $marker['icon_scaledSize'] = '20,20';
                $gmap->add_marker($marker);
            }
        }
        $map = $gmap->create_map();
        return $map;
    }

    /**
     * This method is used for load map details as per sales agent
     */
    public function loadMapSalesAgent(Request $request)
    {
        $salesCenterId = ($request->has('salesCenter')) ? $request->get('salesCenter') : "";
        $locationId = ($request->has('salesLocationId')) ? $request->get('salesLocationId') : "";
        
        $salesAgentLocation = Salesagentlocation::with('users','users.salescenter','users.salescenter.location');
            if($locationId != "")
            {
                if($locationId == "all")
                {
                    $salesAgentLocation = $salesAgentLocation->whereHas('users.salescenter', function ($query) use($salesCenterId){
                        $query->where('id',$salesCenterId);
                    });
                    // $salesAgentLocation = $salesAgentLocation->where('salescenters.id',$salesCenterId);
                }
                else
                {
                    $salesAgentLocation = $salesAgentLocation->where('salescenterslocations.id',$locationId);
                }
            }
            $salesAgentLocation = $salesAgentLocation
            ->select('id','salesagentlocations.lat', 'salesagentlocations.lng','salesagentlocations.salesagent_id','salesagentlocations.created_at','salesagentlocations.id as locationId')
            ->whereRaw('salesagentlocations.id IN (select max(id) from salesagentlocations GROUP BY salesagentlocations.salesagent_id)')
            ->whereHas('users', function ($query) use($request){
                $query->where('client_id',$request->clientId);
            })
            ->get();
            
            // \Log::info('<pre>');
            // \Log::debug($salesAgentLocation->toArray());
        if ($salesAgentLocation->count() > 0) {
            $config['center'] = $salesAgentLocation[0]->lat . "," . $salesAgentLocation[0]->lng;
        }

        $config['map_div_id'] = "map_salesagent";
        $config['map_height'] = "253px";

        $gmap = new GMaps();
        $gmap->initialize($config);
        if(Auth::check()) {
            $user = Auth::user();
            $timezone = Auth::user()->timezone;
        } else {
            $user = auth('api')->user();
            $timezone = getClientSpecificTimeZone();
        }

        if ($salesAgentLocation->count() > 0 && isset($salesAgentLocation)) {

            foreach ($salesAgentLocation as $key => $value) {
                $tooltipText = '<div class="sales-agent-map-div">';
                $dateTime = $value->created_at->setTimezone($timezone)->format('F d, Y '.getTimeFormat());
                $salescenterName = '-';
                $location = '-';
                if(isset($value->users->salescenter) && !empty($value->users->salescenter))
                {
                    $salescenterName = $value->users->salescenter->name;
                    
                }
                if(isset($value->users->salescenter->location) && !empty($value->users->salescenter->location))
                {
                    $location =   $value->users->salescenter->location->name;
                }
                $tooltipText .= "<h6>".$value->users->first_name . " " . $value->users->last_name. " - " . $value->users->userid . "</h6>";
                $tooltipText .= "<table class='tooltip-show-chart'><tr><td>Sales Center </td><td> " . $salescenterName . "</td></tr>";
                $tooltipText .= "<tr><td>Location </td><td> " . $location . "</td></tr>";
                $tooltipText .= "<tr><td>Updated on </td><td> " . $dateTime . "</td></tr></table></div>";
                $marker['position'] = $salesAgentLocation[$key]->lat . "," . $salesAgentLocation[$key]->lng;

                $marker['infowindow_content'] = $tooltipText;
                $marker['title'] = $value->users->first_name . " " . $value->users->last_name;
                $marker['id'] = $value->id;
                $marker['click'] = "addMarkerWithWindow('" . $value->id . "')";
                $gmap->add_marker($marker);
            }

        }
        $map = $gmap->create_map();

        return $map;

    }

    /**
     * For get count of text email
     */
    public function getTextEmailCount(Request $request)
    {
        $todayDate = Carbon::now()->toDateString();

        $EmailText = [
            'Text' => [
                'today' => 0,
                'WTD' => 0,
                'MTD' => 0,
                'YTD' => 0,
            ],
            'Email' => [
                'today' => 0,
                'WTD' => 0,
                'MTD' => 0,
                'YTD' => 0,
            ],
            'TPV Time' => [
                'today' => 0,
                'WTD' => 0,
                'MTD' => 0,
                'YTD' => 0,
            ]
        ];
        $type = 0;
        foreach ($EmailText as $k => $v) {
            if ($k == 'Text')
                $type = 2;
            else if ($k == 'Email')
                $type = 1;
            else
                $type = 3;
            /* get status wise count of leads or time */
            $EmailText[$k]['today'] = $this->getStatusCount(Carbon::parse($todayDate)->startOfDay(), Carbon::parse($todayDate)->endOfDay(), $type);
            $EmailText[$k]['WTD'] = $this->getStatusCount(Carbon::parse($todayDate)->startOfWeek(), Carbon::parse($todayDate)->endOfWeek(), $type);
            $EmailText[$k]['MTD'] = $this->getStatusCount(Carbon::parse($todayDate)->startOfMonth(), Carbon::parse($todayDate)->endOfMonth(), $type);
            $EmailText[$k]['YTD'] = $this->getStatusCount(Carbon::parse($todayDate)->startOfYear(), Carbon::parse($todayDate)->endOfYear(), $type);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'email' => $EmailText,

            ]
        ]);
    }

    /**
     * For get status wise count of leads or time 
     * @param $startDate, $endDate, $type
     */
    public function getStatusCount($startDate, $endDate, $type)
    {
        if ($type == 3) {
            $value = Telesales::whereBetween('reviewed_at', array($startDate, $endDate))
                ->sum('call_duration');
            $dt = Carbon::now();
            $hours = $dt->diffInHours($dt->copy()->addSeconds($value));
            $minutes = $dt->diffInMinutes($dt->copy()->addSeconds($value)->subHours($hours));
            $seconds = $dt->diffInSeconds($dt->copy()->addSeconds($value)->subHours($hours)->subMinutes($minutes));

            if (strlen((string)$hours) == 1)
                $hours = "0" . $hours;
            if (strlen((string)$minutes) == 1)
                $minutes = "0" . $minutes;
            if (strlen((string)$seconds) == 1)
                $seconds = "0" . $seconds;

            return $hours . " : " . $minutes . " : " . $seconds;

        } else {
            return TextEmailStatistics::where('type', $type)
                ->whereBetween('created_at', array($startDate, $endDate))
                ->groupBy('type')->count();
        }
    }

    //  New Dashboard Feature methods start
    public function getConversionRate(Request $request)
    {
        try {
            if(Auth::check()) {
                $user = Auth::user();
                $timezone = Auth::user()->timezone;
            } else {
                $user = auth('api')->user();
                $timezone = getClientSpecificTimeZone();
            }
            $locationId = ($request->has('salesLocationId')) ? $request->get('salesLocationId') : "";
            if($locationId != ''){
                $locationIds = [];
                if(Auth::check())
                {
                    if ($user->hasRole(config('constants.ROLE_SALES_CENTER_QA'))) {
                        $locationIds = $user->locations()->pluck('id')->toArray();
                    }
                    else
                    {
                        $locationIds = $locationId;
                    }
                }
                else
                {
                    $locationIds = $locationId;
                }

            }
            else
                $locationIds = $locationId;
            $clientId = $request->clientId;
            $salesCenterId = ($request->has('salesCenter')) ? $request->get('salesCenter') : "";
            $brand = ($request->has('brand')) ? $request->get('brand') : "";
            
            // for today
            $todayLeads = $this->getConversionRateData(Carbon::now($timezone)->startOfDay()->setTimezone('UTC'),Carbon::now($timezone)->startOfDay()->addDays(1)->setTimezone('UTC'), $clientId,$salesCenterId, $locationIds,$brand);
            
            // for yesterday
            $yesterdayLeads = $this->getConversionRateData(Carbon::now($timezone)->startOfDay()->subDays(1)->setTimezone('UTC'),Carbon::now($timezone)->startOfDay()->subDays(1)->addDays(1)->setTimezone('UTC'), $clientId,$salesCenterId, $locationIds,$brand);
            
            // for this week
            $thisWeekLeads = $this->getConversionRateData(Carbon::today($timezone)->startOfWeek()->setTimezone('UTC'),Carbon::now($timezone)->endOfWeek()->startOfDay()->addDays(1)->setTimezone('UTC'), $clientId,$salesCenterId, $locationIds,$brand);
            
            // for last week
            $lastWeekLeads = $this->getConversionRateData(Carbon::today($timezone)->startOfWeek()->subWeek(1)->setTimezone('UTC'),Carbon::now($timezone)->endOfWeek()->subWeek(1)->startOfDay()->addDays(1)->setTimezone('UTC'), $clientId,$salesCenterId, $locationIds,$brand);
            
            //for this month
            $thisMonthLeads = $this->getConversionRateData(Carbon::today($timezone)->startOfMonth()->setTimezone('UTC'),Carbon::now($timezone)->endOfMonth()->startOfDay()->setTimezone('UTC'), $clientId,$salesCenterId, $locationIds,$brand);
            
            // for last month
            $lastMonthLeads = $this->getConversionRateData(Carbon::today($timezone)->startOfMonth()->subMonth(1)->setTimezone('UTC'),Carbon::now($timezone)->endOfMonth()->subMonth(1)->startOfDay()->addDays(1)->setTimezone('UTC'), $clientId,$salesCenterId, $locationIds,$brand);

            //for this year
            $thisYearLeads = $this->getConversionRateData(Carbon::today($timezone)->startOfYear()->setTimezone('UTC'),Carbon::now($timezone)->endOfYear()->startOfDay()->addDays(1)->setTimezone('UTC'), $clientId,$salesCenterId, $locationIds,$brand);
            
            //for last year
            $lastYearLeads = $this->getConversionRateData(Carbon::today($timezone)->startOfYear()->subYear(1)->setTimezone('UTC'),Carbon::now($timezone)->endOfYear()->subYear(1)->startOfDay()->addDays(1)->setTimezone('UTC'), $clientId,$salesCenterId, $locationIds,$brand);

            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'todayLeads' => $todayLeads,
                    'yesterdayLeads' => $yesterdayLeads,
                    'thisWeekLeads' => $thisWeekLeads,
                    'lastWeekLeads' => $lastWeekLeads,
                    'thisMonthLeads' => $thisMonthLeads,
                    'lastMonthLeads' => $lastMonthLeads,
                    'thisYearLeads' => $thisYearLeads,
                    'lastYearLeads' => $lastYearLeads
                ]

            ]);
        } catch (Exception $e) {
            Log::error('Error while get conversion rate' . $e->getMessage());
            $this->error('error',"something went wrong", 400);
        }
    }

    /**
     * This method is used for fetch client logo
     */
    public function getClientLogo(Request $request)
    {
        try{
            $clientId = $request->clientId;
            $locationId = $request->salesLocationId;
            $salesCenterId = "";
            if($locationId != "")
                $salesCenterId = $request->salesCenter;
            $data = $this->getClientLogoData($clientId,$salesCenterId);
            return $this->success('success', 'success', $data);
        } catch (Exception $e) {
            Log::error('Error while get client logo' . $e->getMessage());
            $this->error('error',"something went wrong", 400);
        }
    }

    //This function is used for testing purpose only
    public function testDashboard($id)
    {                  
        $data = array();
        // $fromDate = "2020-01-01 00:00:00";
        // $toDate = date("Y-m-d H:i:s");
        // $clientId = 102;
        // $data = $this->getLeadsByStateMapData($clientId,$fromDate,$toDate);
        // $data = $this->getLeadsForLineChart($fromDate,$toDate,$clientId,'','','month');
        // $data = $this->getSalesCentersWithTotalLeads($clientId, $fromDate, $toDate,21);
        $objWebhookService = new WebhookService;
        /* Response of lead details for webhook API */
        $data = $this->getWebhookAPIRequest($id);
        // echo "<pre>";
        echo json_encode($data);
        //exit;
        // print_r($data);
        
        
        $data = $objWebhookService->leadCreateWebhookAPI($id, config()->get('constants.CLIENT_LE_CLIENT_LEAD_WEBHOOK_URL'));

        echo "<pre>";
        print_r($data);
        exit;
    }

    //This function is used for get salescenter wise donut chart visual 2
    public function getSalesCenterWiseDonutChartData(Request $request)
    {
        try{
            if(Auth::check())
                $timezone = Auth::user()->timezone;
            else
                $timezone = getClientSpecificTimeZone();
            $fromDate = Carbon::parse($request->startDate,$timezone)->setTimezone('UTC');
            $toDate = Carbon::parse($request->endDate,$timezone)->addDays(1)->setTimezone('UTC');
            // $fromDate = Carbon::parse($request->startDate);
            // $toDate = Carbon::parse($request->endDate)->addDays(1);
            $clientId = $request->clientId;
            $brand = ($request->has('brand')) ? $request->get('brand') : "";
            // dd($brand);
            /* sales center with total leads (Visual - 2 donut chart) */
            $salesCenterData = $this->getSalesCentersWithTotalLeads($clientId, $fromDate, $toDate,$brand);
            $data['salesCenterNames'] = array_column($salesCenterData,'name');
            $data['salesCenterData'] = json_encode(array_values($salesCenterData));

            return $this->success('success', 'success', $data);
        } catch (Exception $e) {
            Log::error('Error while get salescenter donut chart data' . $e->getMessage());
            $this->error('error',"something went wrong", 400);
        }
    }

    /**
     * This function us used for get salescenter wise pie chart details
    */
    public function getSalesCenterWisePieChartData(Request $request)
    {
        try{
            if(Auth::check())
                $timezone = Auth::user()->timezone;
            else
                $timezone = getClientSpecificTimeZone();
            $fromDate = Carbon::parse($request->startDate,$timezone)->setTimezone('UTC');
            $toDate = Carbon::parse($request->endDate,$timezone)->addDays(1)->setTimezone('UTC');
            $clientId = $request->clientId;
            $salesCenterId = isset($request->salesCenterId) ? $request->salesCenterId : $request->salesCenter;
            $agentId = $request->agentId;
            $locationId = $request->salesLocationId;
            $brand = ($request->has('brand')) ? $request->get('brand'):'';
            $salesCenterData = $this->getLeadStatusCount($clientId, $fromDate, $toDate,$brand,$salesCenterId,$agentId,$locationId);
            $data['salesCenterNames'] = array_column($salesCenterData,'name');
            $data['salesCenterData'] = json_encode($salesCenterData);
            return $this->success('success', 'success', $data);
        } catch (Exception $e) {
            Log::error('Error while get salescenter pie chart data' . $e->getMessage());
            $this->error('error',"something went wrong", 400);
        }
    }

    /**
     * This function is used for get client wise pie chart details
     */
    public function getClientWisePieChartData(Request $request)
    {
        try{
            if(Auth::check())
                $timezone = Auth::user()->timezone;
            else
                $timezone = getClientSpecificTimeZone();
            $fromDate = Carbon::parse($request->startDate,$timezone)->setTimezone('UTC');
            $toDate = Carbon::parse($request->endDate,$timezone)->addDays(1)->setTimezone('UTC');
            $brand = ($request->has('brand')) ? $request->get('brand') : "";
            $clientId = $request->clientId;
            $clientData = $this->getLeadStatusCount($clientId, $fromDate, $toDate,$brand);
            $status = array_column($clientData,'name');
            
            /* sales center with total leads (Visual - 2 donut chart) */
            $salesCenterData = $this->getSalesCentersWithTotalLeads($clientId, $fromDate, $toDate,$brand);
            $salesCenterLeadsData;
            $salesCenterDetails = [];
            foreach ($salesCenterData as $key => $val) {

                $salesCenterId = explode('-', $val['name']);
                $salesCenterLeadsData = $this->getLeadStatusCount($clientId, $fromDate, $toDate,$brand, $salesCenterId[1]);
                $salesCenterDetails[$salesCenterId[0]] = $salesCenterLeadsData;
            }
            $data['status'] = $status;
            $data['clientData'] = json_encode($clientData);
            $data['salesCenterDetails'] = $salesCenterDetails;
            return $this->success('success', 'success', $data);

        } catch (Exception $e) {
            Log::error('Error while get client pie chart data' . $e->getMessage());
            $this->error('error',"something went wrong", 400);
        }
    }

    public function getSalesCenterByChannelData(Request $request)
    {
        try{
            if(Auth::check())
                $timezone = Auth::user()->timezone;
            else
                $timezone = getClientSpecificTimeZone();
            $fromDate = Carbon::parse($request->startDate,$timezone)->setTimezone('UTC');
            $toDate = Carbon::parse($request->endDate,$timezone)->addDays(1)->setTimezone('UTC');
            $clientId = $request->clientId;
            $channelList = config('constants.DASHBOARD_CHANNEL_CATEGORIES');
            $channelList = array_values($channelList);
            $salesCenterId = ($request->has('salesCenter')) ? $request->get('salesCenter'): "";
            $locationId = ($request->has('salesLocationId')) ? $request->get('salesLocationId'): "";
            $brand = ($request->has('brand')) ? $request->get('brand'): "";
            /* Lead status count by channel */
            $salesCenterChannelData = $this->getLeadsCountWithStatusByChannel($clientId, $fromDate, $toDate,$brand,$salesCenterId,$locationId);
            $data['channel'] = $channelList;
            
            $salesCenterList[config('constants.DASHBOARD_CHANNEL_CATEGORIES.d2d_sales')] = array_column($salesCenterChannelData,'d2d');
            $salesCenterList[config('constants.DASHBOARD_CHANNEL_CATEGORIES.tele_sales')] = array_column($salesCenterChannelData,'tele');
            $salesCenterToolTipData = [];
            $salesCenterToolTipData[config('constants.DASHBOARD_CHANNEL_CATEGORIES.d2d_sales')] = $this->getSalesCentersByChannel($clientId, $fromDate, $toDate,$brand,'d2d',$salesCenterId,$locationId);
            $salesCenterToolTipData[config('constants.DASHBOARD_CHANNEL_CATEGORIES.tele_sales')] = $this->getSalesCentersByChannel($clientId, $fromDate, $toDate,$brand,'tele',$salesCenterId,$locationId);
            
            $data['salesCenterToolTipData'] = $salesCenterToolTipData;
            $data['salesCenterChannelData'] = $salesCenterList;

            $data['D2D Sales'] = $this->getConversionRatePercentage($salesCenterList['D2D Sales'][0],array_sum($salesCenterList['D2D Sales']));
            $data['Tele Sales'] = $this->getConversionRatePercentage($salesCenterList['Tele Sales'][0],array_sum($salesCenterList['Tele Sales']));
            if($locationId != '')
            {
               $data['tooltipFalse'] = true;
            }
            else
            {
                $data['tooltipFalse'] = false;   
            }
            
            $data['status'] = array_values(config('constants.DASHBOARD_LEAD_CATEGORIES'));
            return $this->success('success', 'success', $data);
        } catch (Exception $e) {
            Log::error('Error while get salescenter channel data' . $e->getMessage());
            $this->error('error',"something went wrong", 400);
        }
    }

    //  Get Status by state Data
    public function getStatusByState(Request $request)
    {
        try {
            if(Auth::check())
                $timezone = Auth::user()->timezone;
            else
                $timezone = getClientSpecificTimeZone();
            $client_id = $request->clientId;
            $start_date = Carbon::parse($request->startDate,$timezone)->setTimezone('UTC');
            $end_date = Carbon::parse($request->endDate,$timezone)->addDays(1)->setTimezone('UTC');
            // $start_date = Carbon::parse($request->startDate)->startOfDay();
            // $end_date = Carbon::parse($request->endDate)->endOfDay();
            $salesCenterId = $request->salesCenter;
            $salesLocationId = $request->salesLocationId;
            $brand = ($request->has('brand'))?$request->get('brand'):'';
            /* Status By State */
            $data = $this->getStatusByStateData($client_id, $start_date, $end_date,$brand, $salesCenterId, $salesLocationId);
            $overallSales = $data['leadsByStateOverall'];
            $goodSales = $data['leadsByStateGood'];
            $overallSalesData = array();
            $goodSalesData = array();
            $overallSalesName= array();
            // To good Sales
            foreach ($goodSales as $key => $goodSale) {
                if (isset($goodSale['state']) && !empty($goodSale['state'])) {
                    $goodSalesData[$goodSale['name'].'-'.$goodSale['state']] = $goodSale['total'];
                }
            }
            // To overall Sales
            foreach ($overallSales as $key => $overallSale) {
                if (isset($overallSale['state']) && !empty($overallSale['state'])) {
                    $overallSalesData[$overallSale['name'].'-'.$overallSale['state']] = $overallSale['total'];
                    $overallSalesName[$overallSale['name'].'-'.$overallSale['state']] = $overallSale['state'];
                }
            }
            
            // To set conversion rate
            $conversionRate = array();
            foreach ($overallSalesData as $key => $val) {
                if (isset($goodSalesData[$key])) {
                    if ($val != 0) {
                        $average = ($goodSalesData[$key] / $val)*100;
                    } else
                        $average = 0;

                    $conversionRate[$key] = number_format($average, 2, '.', '');
                }
            }
            // To get total
            $overallSalesTotal = array_sum($overallSalesData);
            $goodSalesTotal = array_sum($goodSalesData);
            $conversionRateTotal = number_format(($goodSalesTotal/$overallSalesTotal)*100, 2, '.', '');//array_sum($conversionRate);
            
            return response()->json([
                'status' => 'success',
                'html' => view("admin.dashboard.client-tab.status_by_state_table", compact("overallSalesData", "goodSalesData", "overallSalesTotal", "goodSalesTotal", "conversionRate", "conversionRateTotal"))->render(),
                'data' =>[
                    'overallSale' => $overallSalesData,
                    'goodSale' => $goodSalesData,
                    'conversionRate' => $conversionRate,
                    'overallSalesTotal'=> $overallSalesTotal,
                    'goodSalesTotal'=> $goodSalesTotal,
                    'overallSalesName'=> $overallSalesName,
                    'conversionRateTotal'=>number_format($conversionRateTotal, 2,'.','')
                ]   
            ]);
        } catch (Exception $e) {
            Log::error('Error while get status by status' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'html' => '<p>No records found</p>'
            ]);
        }
    }

    public function getSalesCenterByCommodityData(Request $request)
    {
        try{
            if(Auth::check())
                $timezone = Auth::user()->timezone;
            else
                $timezone = getClientSpecificTimeZone();
            $fromDate = Carbon::parse($request->startDate,$timezone)->setTimezone('UTC');
            $toDate = Carbon::parse($request->endDate,$timezone)->addDays(1)->setTimezone('UTC');
            // $fromDate = Carbon::parse($request->startDate);
            // $toDate = Carbon::parse($request->endDate)->addDays(1);
            $clientId = $request->clientId;
            $salesCenterId = ($request->has('salesCenter')) ? $request->get('salesCenter'): "";
            $locationId = ($request->has('salesLocationId')) ? $request->get('salesLocationId'): "";
            $brand = ($request->has('brand')) ? $request->get('brand'): "";
            /* Returns Lead status count by commodity */
            $salesCenterCommodityData = $this->getLeadsCountWithStatusByCommodity($clientId, $fromDate, $toDate,$brand,$salesCenterId,$locationId);
            $i = 0;
            $salesCenterCommodityData['commoditiesNames'] = array_values($salesCenterCommodityData['commoditiesNames']);
            $channelList = config('constants.DASHBOARD_LEAD_CATEGORIES');
            $array = [];
            $j = 0;
            foreach ($channelList as $k => $v) {
                for ($i = 0; $i < count($salesCenterCommodityData['commoditiesNames']); $i++) {
                    if (isset($array[$salesCenterCommodityData['commoditiesNames'][$i]])) {
                        $array[$salesCenterCommodityData['commoditiesNames'][$i]][] = $salesCenterCommodityData['leadsCount'][$k][$salesCenterCommodityData['commoditiesNames'][$i]];
                    } else {
                        $array[$salesCenterCommodityData['commoditiesNames'][$i]][] = $salesCenterCommodityData['leadsCount'][$k][$salesCenterCommodityData['commoditiesNames'][$i]];
                    }
                }
            }
            $channelList = array_values(config('constants.DASHBOARD_LEAD_CATEGORIES'));
            $data['legendText'] = $salesCenterCommodityData['commoditiesNames'];
            $data['channelList'] = $channelList;
            $data['salesCenterCommodityData'] = $array;
            $data['toolTipSalesCenterData'] = $salesCenterCommodityData['salesCenter'];
            if($locationId != '')
            {
               $data['tooltipFalse'] = true;
            }
            else
            {
                $data['tooltipFalse'] = false;   
            }
            return $this->success('success', 'success', $data);
        } catch (Exception $e) {
            Log::error('Error while get salescenter by commodity data' . $e->getMessage());
            $this->error('error',"something went wrong", 400);
        }
    }

    public function getLeadsCountRateData(Request $request)
    {
        try{
            if(Auth::check())
                $timezone = Auth::user()->timezone;
            else
                $timezone = getClientSpecificTimeZone();
            $fromDate = Carbon::parse($request->startDate,$timezone)->setTimezone('UTC');
            $toDate = Carbon::parse($request->endDate,$timezone)->addDays(1)->setTimezone('UTC');
            $clientId = $request->clientId;
            $salesCenterId = ($request->has('salesCenter')) ? $request->get('salesCenter') : "";
            $locationId = ($request->has('salesLocationId')) ? $request->get('salesLocationId') : "";
            $leadsRate = [];
            $leadsCount = [];
            $leadsVerify = [];
            $xaxisDate = [];
            $difference = $fromDate->diffInDays($toDate);
            $yearMonth = $request->has('lineFilters')?$request->get('lineFilters'):'';
            $brand = $request->has('brand')?$request->get('brand'):'';
            $leadsData = $this->getLeadsForLineChart($fromDate,$toDate,$clientId,$brand,$salesCenterId,$locationId,$yearMonth);
            $k = 0;
            
            foreach($leadsData['leads'] as $key => $val)
            {   
                $verified = 0;
                $leadsCount[] = $val;  
                $leadsVerify[] = isset($leadsData['verified'][$key]) ? $leadsData['verified'][$key] : 0;
                
                $monthyearArr = explode('-',$key);
                
                if($yearMonth == 'day')
                    $xaxisDate[] = Carbon::create($monthyearArr[2],$monthyearArr[1],$monthyearArr[0])->format('d-M-Y');
                else if($yearMonth == 'month')
                {
                    $date = new Carbon($monthyearArr[1].'-'.$monthyearArr[0]);
                    $xaxisDate[] = $date->format('M-Y');
                }
                else
                $xaxisDate[] = Carbon::create($monthyearArr[0])->format('Y');
            }
            foreach($leadsCount as $k => $v)
            {
                /* Returns conversion Rate */
                $leadsRate[] = $this->getConversionRatePercentage($leadsVerify[$k], $v);
            }
            $data['leads'] = $leadsCount;
            $data['rate'] = $leadsRate;    
            $data['xaxisDate'] = $xaxisDate;
            return $this->success('success', 'success', $data);
        } catch (Exception $e) {
            Log::error('Error while get leads count with conversion rate data' . $e->getMessage());
            $this->error('error',"something went wrong", 400);
        }
    }

    public function getLeadsCountByVerificationMethodData(Request $request)
    {
        try{
            if(Auth::check())
                $timezone = Auth::user()->timezone;
            else
                $timezone = getClientSpecificTimeZone();
            $fromDate = Carbon::parse($request->startDate,$timezone)->setTimezone('UTC');
            $toDate = Carbon::parse($request->endDate,$timezone)->addDays(1)->setTimezone('UTC');
            $brand = ($request->has('brand')) ? $request->get('brand'): "";
            $clientId = $request->clientId;
            $channel = $request->salesAgentType;
            $leadType = $request->salesAgentLeadType;
            if($leadType == config('constants.DASHBOARD_LEAD_CATEGORIES.good_sale'))
            {
                $leadType = 'verified';
            }
            else
                $leadType = 'decline';

            /* leads verification method with their counts by channel */
            $agentVerificationMethodData = $this->leadVerificationMethodsCountByChannel($clientId, $fromDate,$toDate,$brand,$leadType,$channel);
            $data['agentVerificationMethodData'] = [];
            if(count($agentVerificationMethodData) > 0)
            {
                // $data['color'] = $agentVerificationMethodData['color'];
                $data['agentVerificationMethodData'] = $agentVerificationMethodData['data'];
            }
            return $this->success('success','success',$data);

        } catch (Exception $e) {
            Log::error('Error while get leads count by verificaiton method' . $e->getMessage());
            $this->error('error',"something went wrong", 400);
        }
    }

    // To get Top Performer
    public function getTopPerformerData(Request $request)
    {
        if(Auth::check())
                $timezone = Auth::user()->timezone;
            else
                $timezone = getClientSpecificTimeZone();
        $fromDate = Carbon::parse($request->startDate,$timezone)->setTimezone('UTC');
        $toDate = Carbon::parse($request->endDate,$timezone)->addDays(1)->setTimezone('UTC');
        $brand = ($request->has('brand'))? ($request->get('brand')):"";
        $clientId = $request->clientId;
        $salesLocationId = $request->salesLocationId;
        $salesCenterId = $request->salesCenter;
        /* sales agent list with their verified leads percentage */
        $data = $this->getConversionRateBySalesAgents($clientId, $fromDate, $toDate,$brand, "top",$salesLocationId,$salesCenterId);
        $response = [];
        $response['names'] = array_column($data, 'name');
        $response['agents'] = $data;
        return $this->success('success', 'success', $response);
    }

    // To get Bottom Performer
    public function getBottomPerformerData(Request $request)
    {
        if(Auth::check())
                $timezone = Auth::user()->timezone;
            else
                $timezone = getClientSpecificTimeZone();
        $fromDate = Carbon::parse($request->startDate,$timezone)->setTimezone('UTC');
        $toDate = Carbon::parse($request->endDate,$timezone)->addDays(1)->setTimezone('UTC');
        $clientId = $request->clientId;
        $salesLocationId = $request->salesLocationId;
        $salesCenterId = $request->salesCenter;
        $brand = ($request->has('brand'))? ($request->get('brand')):"";
        /* sales agent list with their verified leads percentage */
        $data = $this->getConversionRateBySalesAgents($clientId, $fromDate, $toDate,$brand, "bottom",$salesLocationId,$salesCenterId);

        $response = [];
        $response['names'] = array_reverse(array_column($data, 'name'));
        $response['agents'] = array_reverse($data);
        
        return $this->success('success', 'success', $response);
    }

    /**
     * To get calaender pie client details
     */
    public function getCalenderPieClientData(Request $request)
    {
        try{
            $clientId = $request->clientId;
            $salesCenterId = ($request->has('salesCenter')) ? $request->get('salesCenter') : "";
            $locationId = ($request->has('salesLocationId')) ? $request->get('salesLocationId') : "";
            $brand = ($request->has('brand')) ? $request->get('brand') : "";
            $status = config('constants.DASHBOARD_LEAD_CATEGORIES');
            $status = array_values($status);
            $month = ($request->has('monthFilter')) ? $request->get('monthFilter') : Carbon::now()->format('m');
            $year = ($request->has('yearFilter')) ? $request->get('yearFilter') : Carbon::now()->format('Y');
            $date = new Carbon($year."-".$month);
            $noDays = $date->daysInMonth;

            for($i = 0;$i < $noDays; $i++)
            {
                /* lead count with their status by Sales center id */
                $clientData = $this->getLeadStatusCount($clientId, (new Carbon($year."-".$month,Auth::user()->timezone))->startOfMonth()->addDays($i)->setTimezone('UTC'), (new Carbon($year."-".$month,Auth::user()->timezone))->startOfMonth()->addDays($i+1)->setTimezone('UTC'),$brand,$salesCenterId,'',$locationId);
                $data['clientData'][] = $clientData;
            }
            
            $data['status'] = $status;
            $data['startDate'] = (new Carbon($year."-".$month))->startOfMonth();
            $data['endDate'] = (new Carbon($year."-".$month))->endOfMonth()->addDays(1);
            $data['range'] = [$year."-".$date->format('m')];
            return $this->success('success', 'success', $data);
        } catch (Exception $e) {
            Log::error('Error while get calender pie chart of client lead data' . $e->getMessage());
            $this->error('error',"something went wrong", 400);
        }
    }

    // To get Leads By Sales Center Location
    public function getLeadsBySalesCenterLocation(Request $request)
    {
        if(Auth::check())
                $timezone = Auth::user()->timezone;
            else
                $timezone = getClientSpecificTimeZone();
        $fromDate = Carbon::parse($request->startDate,$timezone)->setTimezone('UTC');
        $toDate = Carbon::parse($request->endDate,$timezone)->addDays(1)->setTimezone('UTC');
        $brand = ($request->has('brand'))?$request->get('brand'):'';
        $clientId = $request->clientId;
        /* all sales center locations with their lead count & conversion rate */
        $data = $this->locationsWithLeadCountsAndConverationRate($clientId, $fromDate, $toDate,$brand);
        $response = [];
        $value = array_column($data, 'value');
        array_multisort($value, SORT_DESC, $data);
        $response['data'] = $data;
        $response['name'] = array_column($data, 'name');
        $response['value'] = array_column($data, 'total_leads');
        $response['rates'] = array_column($data, 'conversion_rate');
        return $this->success('success', 'success', $response);
    }

    /**
     * This method is used to get lead by salescenter location 
     */
    public function GetLeadsTableBySalesCenterLocation(Request $request)
    {
        try{
            if(Auth::check())
                $timezone = Auth::user()->timezone;
            else
                $timezone = getClientSpecificTimeZone();
            $fromDate = Carbon::parse($request->startDate,$timezone)->setTimezone('UTC');
            $toDate = Carbon::parse($request->endDate,$timezone)->addDays(1)->setTimezone('UTC');
            $clientId = $request->clientId;
            $brand = ($request->has('brand'))?$request->get('brand'):'';
            /* all sales center locations with their lead's status wise count and total counts */
            $locationData = $this->getLocationsWithLeadsCount($clientId, $fromDate, $toDate,$brand);
            $data['data'] = $locationData;
            // $data['html'] = view('admin.dashboard.salescenter-tab.salescenter_location_lead_table_display',compact('data'))->render();
            return $this->success('success', 'success', $data);
        } catch (Exception $e) {
            Log::error('Error while get lead detail table of salescenter locaiton' . $e->getMessage());
            $this->error('error',"something went wrong", 400);
        }
    }

    /**
     * This method is used to get lead by salescenter location channel
     */
    public function getLeadsBySalesCenterLocationChannel(Request $request)
    {
        try{
            if(Auth::check())
                $timezone = Auth::user()->timezone;
            else
                $timezone = getClientSpecificTimeZone();
            $fromDate = Carbon::parse($request->startDate,$timezone)->setTimezone('UTC');
            $toDate = Carbon::parse($request->endDate,$timezone)->addDays(1)->setTimezone('UTC');
            $clientId = $request->clientId;
            $brand = ($request->has('brand'))?$request->get('brand'):'';
            $salesCenterId = ($request->has('salesCenter')) ? $request->get('salesCenter') : "";
            $locationId = ($request->has('salesLocationId')) ? $request->get('salesLocationId') : "";
            /* Lead status count by channel */
            $data = $this->getLocationsWithLeadCountsByChannel($clientId, $fromDate, $toDate,$brand,$salesCenterId,$locationId);
            $salesCenterList[config('constants.DASHBOARD_CHANNEL_CATEGORIES.d2d_sales')] = array_column($data,'d2d');
            $salesCenterList[config('constants.DASHBOARD_CHANNEL_CATEGORIES.tele_sales')] = array_column($data,'tele');
            $locationNames = array_column($data,'name');
            $channelList = array_values(config('constants.DASHBOARD_CHANNEL_CATEGORIES'));
            $data['data'] = $data;
            $data['channel'] = $channelList;
            $data['locationNames'] = $locationNames;
            $data['salesCenterList'] = $salesCenterList;
            // dd($salesCenterList);
            return $this->success('success', 'success', $data);
        } catch (Exception $e) {
            Log::error('Error while get salescenter location by channel' . $e->getMessage());
            $this->error('error',"something went wrong", 400);
        }
    }

    // Get Leads by commodity - By sales centers tab
    public function getLocationsLeadsByCommodityData(Request $request)
    {
        if(Auth::check())
                $timezone = Auth::user()->timezone;
            else
                $timezone = getClientSpecificTimeZone();
        $fromDate = Carbon::parse($request->startDate,$timezone)->setTimezone('UTC');
        $toDate = Carbon::parse($request->endDate,$timezone)->addDays(1)->setTimezone('UTC');
        $brand = ($request->has('brand'))?$request->get('brand'):'';
        $clientId = $request->clientId;
        $salesCenterId = ($request->has('salesCenter')) ? $request->get('salesCenter') : "";
        $locationId = ($request->has('salesLocationId')) ? $request->get('salesLocationId'):'';
        $data = $this->getLocationsLeadsByCommodity($clientId, $fromDate, $toDate,$brand,$locationId,$salesCenterId);
        // dd($data);
        $leadsCountData = [];
        foreach ($data['commoditiesNames'] as $commodity) {
          $leadsCountData[$commodity] = array_column($data['leadsCount'], $commodity);
        }

        $response = [];
        $response['legendText'] = array_values($data['commoditiesNames']);
        $response['locationsNames'] = array_keys($data['leadsCount']);
        $response['leadsCount'] = $leadsCountData;

        return $this->success('success', 'success', $response);
    }

   // Get Locations Wise Leads - By sales centers tab
    public function getLocationsWiseLeads(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $timezone = Auth::user()->timezone;
        } else {
            $user = auth('api')->user();
            $timezone = getClientSpecificTimeZone();
        }
        // \Log::info($user);
        $fromDate = Carbon::parse($request->startDate,$timezone)->setTimezone('UTC');
        $toDate = Carbon::parse($request->endDate,$timezone)->addDays(1)->setTimezone('UTC');
        $brand = ($request->has('brand'))?$request->get('brand'):'';
        $locationIds = [];
        if(Auth::check())
        {
            
            if ($user->hasRole(config('constants.ROLE_SALES_CENTER_QA'))) {
                $locationIds = $user->locations()->pluck('id')->toArray();
            }
        }

        $clientId = $request->clientId;
        $salesCenterId = ($request->has('salesCenter')) ? $request->get('salesCenter') : "";
        /* locations with lead status & conversion rate */
        $data = $this->locationsWithLeadStatusCounts($clientId, $fromDate, $toDate,$brand,$salesCenterId, $locationIds);
        
        $channelList = array_values(config('constants.DASHBOARD_LEAD_CATEGORIES'));
        $response = [];
        $response['legendText'] = $channelList;
        $response['locationsNames'] = array_column($data,'name');
        $response['good_sales'] = array_column($data, 'good_sale');
        $response['bad_sales'] = array_column($data, 'bad_sale');
        $response['pending_leads'] = array_column($data, 'pending_leads');
        $response['cancelled_leads'] = array_column($data, 'cancelled_leads');
        $response['rates'] = array_column($data, 'conversion_rate');

        return $this->success('success', 'success', $response);
    }

    /**
     * This method is used to get program based lead
     */
    public function getTopProgramsBasedOnLeads(Request $request)
    {   
        if(Auth::check())
                $timezone = Auth::user()->timezone;
            else
                $timezone = getClientSpecificTimeZone();
        $fromDate = Carbon::parse($request->startDate,$timezone)->setTimezone('UTC');
        $toDate = Carbon::parse($request->endDate,$timezone)->addDays(1)->setTimezone('UTC');
        $clientId = $request->clientId;
        $brand = ($request->has('brand'))?$request->get('brand'):'';
        $programs = $this->topProgramsBasedOnLeads($clientId,$fromDate,$toDate,$brand);
        $programLeads = $programs['leads'];
        $data['name'] = array_column($programLeads,'name');
        $data['id'] = array_column($programLeads,'program_id');
        $data['programs'] = $programLeads;
        $data['salesCenters'] = $programs['salescenterArr'];

        return $this->success('success', 'success', $data);
    }

    /**
     * This function is used to get topprovider based on lead
     */
    public function getTopProvidersBasedOnLeads(Request $request)
    {
        if(Auth::check())
                $timezone = Auth::user()->timezone;
            else
                $timezone = getClientSpecificTimeZone();
        $fromDate = Carbon::parse($request->startDate,$timezone)->setTimezone('UTC');
        $toDate = Carbon::parse($request->endDate,$timezone)->setTimezone('UTC')->addDays(1);
        $clientId = $request->clientId;
        $brand = ($request->has('brand'))? $request->get('brand'):'';
        $utilities = $this->topProvidersBasedOnLeads($clientId,$fromDate,$toDate,$brand);
        
        $utilityLeads = $utilities['leads'];
        $data['name'] = array_column($utilityLeads,'name');
        $data['utilities'] = $utilityLeads;
        $data['salesCenters'] = $utilities['salescenterArr'];

        return $this->success('success', 'success', $data);
    }

    /**
     * This method is used to get state wise leadmap
     */
    public function getStateWiseLeadMap(Request $request)
    {
        if(Auth::check())
                $timezone = Auth::user()->timezone;
            else
                $timezone = getClientSpecificTimeZone();
        $fromDate = Carbon::parse($request->startDate,$timezone)->setTimezone('UTC');
        $toDate = Carbon::parse($request->endDate,$timezone)->setTimezone('UTC')->addDays(1);
        $clientId = $request->clientId;
        $brand = ($request->has('brand'))? $request->get('brand'):'';
        $salesCenterId = ($request->has('salesCenter')) ? $request->get('salesCenter') : "";   
        $locationId = $request->salesLocationId;
        /* Status By State */
        $stateData = $this->getStatusByStateData($clientId, $fromDate, $toDate,$brand,$salesCenterId, $locationId,'map');
        // $stateData = $this->getLeadsByStateMapData($clientId,$fromDate,$toDate,$salesCenterId, $locationId);
        foreach($stateData['leadsByStateOverall'] as $key => &$val)
        {
            $val['id'] = $val['state'];
            $val['state'] = config()->get('constants.USA_STATE_ABBR.'.$val['state']);
        }
        $state['state'] = array_column($stateData['leadsByStateOverall'],'state');
        $state['total'] = array_column($stateData['leadsByStateOverall'],'total');
        $state['id'] = array_column($stateData['leadsByStateOverall'],'id');
        $state = $stateData['leadsByStateOverall'];
        
        $states = array_map(function($state) {
            return array(
                'name' => $state['state'],
                'value' => $state['total'],
                'id' => $state['id']
            );
        }, $state);
        if(count($states) > 0 )
            $data['max'] = max(array_column($states,'value'));
        else
       
        $data['max'] = 0;
        $data['data'] = $states;
        return $this->success('success', 'success', $data);
    }

    public function mobileDashboard(Request $request) {
        try {
            $user = auth('api')->user();

            if (empty($user)) {
                return $this->defaultErrorView();
            }

            $token = $request->header('authorization');

            if ($user->hasRole([config('constants.ROLE_CLIENT_ADMIN')])) {
                return $this->get_admin_dashboard($request, $user, "mobile", $token);
            } else {
                return $this->defaultErrorView();
            }
        } catch (\Exception $e) {
            return $this->defaultErrorView();
        }
    }

    public function callApi() {
        $sid = "AC067573cfa73fa25438e1b8f68eb3b473";
        $token = "d633305985c44686b5aa5a1c6cfd027e";
        $client = new TwilioClient($sid, $token);
        // foreach ($client->usage->records->lastMonth->read() as $record) {
        //     echo $record->count;
        // }
        // echo "<pre>"; print_r($client); 
        echo "<pre>"; print_r($client->usage->records->lastMonth->read()); exit;
        exit;
    }

    // function for Dashboard Agent Report task.
    public function agentReport(Request $request)
    {
        $timeZone = Auth::user()->timezone;
        $callDetails = TwilioLeadCallDetails::select('twilio_lead_call_details.worker_id','twilio_lead_call_details.task_id',
        //'users.first_name','users.last_name','users.id as user_id',                                            
                                            // DB::raw("COUNT(*) as totalCalls"),
                                                                                        
                                            // DB::raw("count(case when lead_status = 'decline' then 1 end) as declinedCalls"),
                                            // DB::raw("count(case when lead_status = 'verified' then 1 end) as verifiedCalls"),
                                            // DB::raw("count(case when lead_status = 'hangup' then 1 end) as disconnectedCalls"),
                                            // DB::raw("count(case when lead_status = 'pending' then 1 end) as pendingCalls"),
                                            // DB::raw("count(case when lead_status = 'expired' then 1 end) as expiredCalls"),
                                            DB::raw("(SELECT first_name FROM users WHERE id = (SELECT user_id FROM user_twilio_id WHERE twilio_id = `twilio_lead_call_details`.`worker_id` LIMIT 1)) as first_name"),
                                            DB::raw("(SELECT last_name FROM users WHERE id = (SELECT user_id FROM user_twilio_id WHERE twilio_id = `twilio_lead_call_details`.`worker_id` LIMIT 1)) as last_name"),
                                            DB::raw("(SELECT id FROM users WHERE id = (SELECT user_id FROM user_twilio_id WHERE twilio_id = `twilio_lead_call_details`.`worker_id` LIMIT 1)) as user_id"),
                                            DB::raw("(SELECT CONCAT(first_name, ' ', last_name) FROM users WHERE id = (SELECT user_id FROM user_twilio_id WHERE twilio_id = `twilio_lead_call_details`.`worker_id` LIMIT 1)) as TPVAgent"),
                                            DB::raw("(SELECT created_at FROM users WHERE id = (SELECT user_id FROM user_twilio_id WHERE twilio_id = `twilio_lead_call_details`.`worker_id` LIMIT 1)) as created_at"),                                                   
                                            
                                            DB::raw("count(case when (SELECT COUNT(*) FROM twilio_worker_reservation_details WHERE worker_id = `twilio_lead_call_details`.`worker_id`  AND task_id = `twilio_lead_call_details`.`task_id` AND twilio_worker_reservation_details.reservation_status = 'accepted' ORDER BY id DESC  LIMIT 1) > 0 then 1 end) as acceptedCalls"),
                                            DB::raw("count(case when 
                                                (
                                                    SELECT reservation_status FROM twilio_worker_reservation_details WHERE worker_id = `twilio_lead_call_details`.`worker_id`  AND task_id = `twilio_lead_call_details`.`task_id` ORDER BY id DESC  LIMIT  1) = 'rejected' then 1 end
                                                )
                                                as rejectedCalls
                                             "),
                                            DB::raw("
                                                (SELECT
                                                count(
                                                    case when 
                                                    (
                                                        SELECT COUNT(*) FROM twilio_worker_reservation_details WHERE
                                                        worker_id = `twilio_lead_call_details`.`worker_id`
                                                        AND 
                                                        task_id = `twilio_lead_call_details`.`task_id` AND twilio_worker_reservation_details.reservation_status = 'timeout' ORDER BY id DESC  LIMIT 1
                                                    ) > 0 
                                                    then 1 end
                                                )) as timeoutCalls
                                            "),
                                            DB::raw("
                                                count(case when lead_status = 'verified' 
                                                AND
                                                (SELECT reservation_status FROM twilio_worker_reservation_details WHERE worker_id = `twilio_lead_call_details`.`worker_id`  AND task_id = `twilio_lead_call_details`.`task_id`  ORDER BY id DESC LIMIT 1) = 'accepted'
                                                then 1 end
                                                ) as verifiedCalls
                                            "),
                                            DB::raw("
                                                count(case when lead_status = 'decline' 
                                                AND
                                                (SELECT reservation_status FROM twilio_worker_reservation_details WHERE worker_id = `twilio_lead_call_details`.`worker_id`  AND task_id = `twilio_lead_call_details`.`task_id`   ORDER BY id DESC  LIMIT 1) = 'accepted'
                                                then 1 end
                                                ) as declinedCalls
                                            "),
                                            DB::raw("
                                                count(case when lead_status = 'hangup' 
                                                AND
                                                (SELECT reservation_status FROM twilio_worker_reservation_details WHERE worker_id = `twilio_lead_call_details`.`worker_id`  AND task_id = `twilio_lead_call_details`.`task_id`  ORDER BY id DESC  LIMIT 1) = 'accepted'
                                                then 1 end
                                                ) as disconnectedCalls
                                            "),
                                            DB::raw("
                                                count(case when lead_status = 'pending' 
                                                AND
                                                (SELECT reservation_status FROM twilio_worker_reservation_details WHERE worker_id = `twilio_lead_call_details`.`worker_id`  AND task_id = `twilio_lead_call_details`.`task_id`  ORDER BY id DESC  LIMIT 1) = 'accepted'
                                                then 1 end
                                                ) as pendingCalls
                                            "),
                                            DB::raw("
                                                count(case when lead_status = 'expired' 
                                                AND
                                                (SELECT reservation_status FROM twilio_worker_reservation_details WHERE worker_id = `twilio_lead_call_details`.`worker_id`  AND task_id = `twilio_lead_call_details`.`task_id`  ORDER BY id DESC LIMIT 1) = 'accepted'
                                                then 1 end
                                                ) as expiredCalls
                                            "),
                                            DB::raw("
                                                count(case when lead_status IS NULL 
                                                AND
                                                (SELECT reservation_status FROM twilio_worker_reservation_details WHERE worker_id = `twilio_lead_call_details`.`worker_id`  AND task_id = `twilio_lead_call_details`.`task_id`  ORDER BY id DESC LIMIT 1) = 'accepted'
                                                then 1 end
                                                ) as nullCalls
                                            "),
                                            DB::raw("
                                                count(case when
                                                (SELECT COUNT(*) FROM twilio_worker_reservation_details WHERE worker_id = `twilio_lead_call_details`.`worker_id`  AND task_id = `twilio_lead_call_details`.`task_id` AND twilio_worker_reservation_details.reservation_status = 'accepted' ORDER BY id DESC LIMIT 1) > 0
                                                then 1 end
                                                ) as totalCalls
                                            ")                         
                                                    );
                        // ->leftJoin('user_twilio_id','user_twilio_id.twilio_id','=','twilio_lead_call_details.worker_id')
                        // ->leftJoin('users','users.id','=','user_twilio_id.user_id');                        
        $date = "";
        $startDate = "";
        $endDate = "";
        
        if (isset($request->submitDate) && !empty($request->submitDate)) {
            
            $date = $request->submitDate;
            $startDate = Carbon::parse(explode(' - ', $date)[0],$timeZone)->setTimezone('UTC');//->toDateString();
            $endDate = Carbon::parse(explode(' - ', $date)[1],$timeZone)->setTimezone('UTC')->addDays(1);//->toDateString();
            $callDetails = $callDetails->whereBetween('twilio_lead_call_details.created_at', [$startDate, $endDate]);
        }

        if(isset($request->searchText) && $request->searchText != ""){
            $callDetails = $callDetails->where(function($query) use($request) {
                $query->whereRaw("(SELECT id FROM users WHERE id =(SELECT user_id FROM user_twilio_id WHERE twilio_id = `twilio_lead_call_details`.`worker_id` LIMIT 1 ) AND CONCAT(users.first_name,' ',users.last_name) LIKE '%$request->searchText%')")
                ->orWhereRaw("(SELECT id FROM users WHERE id =(SELECT user_id FROM user_twilio_id WHERE twilio_id = `twilio_lead_call_details`.`worker_id` LIMIT 1 ) AND CONCAT(users.last_name,' ',users.first_name) LIKE '%$request->searchText%')");
            });                
        }

        $callDetails->whereNotNull('twilio_lead_call_details.worker_id')                    
                    ->groupBy('twilio_lead_call_details.worker_id');        
                
        // If this method access by ajax then return data in datatable otherwise in view (blade) file
        if($request->ajax()) {
            
            return DataTables::of($callDetails)
                                ->editColumn('created_at', function ($callDetails) {                                    
                                    $date = $callDetails->created_at->format(getDateFormat());
                                    return $date;
                                })
                                ->addColumn('verifiedCallsPercentage', function ($callDetails) {
                                    $percentage = 0;
                                    try {
                                        $percentage =  ($callDetails->verifiedCalls / $callDetails->totalCalls) * 100;
                                    }
                                    catch (\Exception $e) {
                                        $percentage = 0;
                                    }
                                    return number_format($percentage,2);
                                })
                                ->make(true);            
        }        

        return view('reports.agent-report.index');        
    }
    // function for Dashboard Agent Report task.
    public function agentActivityDurationReport(Request $request)
    {
        $date = "";
        $startDate = "";
        $endDate = "";
        $timeZone = Auth::user()->timezone;
        
        if (isset($request->submitDate) && !empty($request->submitDate)) {
            
            $date = $request->submitDate;
            $startDate = Carbon::parse(explode(' - ', $date)[0]);
            $endDate = Carbon::parse(explode(' - ', $date)[1])->addDays(1)->subSeconds(1);
            \Log::info('Start Date .....'.$startDate);
            \Log::info('End Date .....'.$endDate);
        }

      

        $callDetails = TwilioStatisticsSpecificWorkerActivity::select(
            //  'twilio_statistics_specific_Worker_activity_duration.created_at',             
             DB::raw("(SELECT first_name FROM users WHERE id = (SELECT user_id FROM user_twilio_id WHERE twilio_id = `twilio_statistics_specific_Worker_activity_duration`.`worker_id` LIMIT 1)) as first_name"),
            DB::raw("(SELECT last_name FROM users WHERE id = (SELECT user_id FROM user_twilio_id WHERE twilio_id = `twilio_statistics_specific_Worker_activity_duration`.`worker_id` LIMIT 1)) as last_name"),
            DB::raw("(SELECT id FROM users WHERE id = (SELECT user_id FROM user_twilio_id WHERE twilio_id = `twilio_statistics_specific_Worker_activity_duration`.`worker_id` LIMIT 1)) as user_id"),
            DB::raw("(SELECT CONCAT(first_name, ' ', last_name) FROM users WHERE id = (SELECT user_id FROM user_twilio_id WHERE twilio_id = `twilio_statistics_specific_Worker_activity_duration`.`worker_id` LIMIT 1)) as TPVAgent"),
            DB::raw("( select GROUP_CONCAT(name  SEPARATOR ', ') from clients where id IN (select client_id from client_twilio_workflowids where workflow_id IN (select workflow_id from user_twilio_id where user_twilio_id.user_id IN (SELECT user_id FROM user_twilio_id WHERE twilio_id = `twilio_statistics_specific_Worker_activity_duration`.`worker_id`)))) as client_name"),
              DB::raw("SUM(case when cumulative_activity_name = 'Available' then cumulative_totaltime  end) as onlineDuration
              "),            
              DB::raw("SUM(case when cumulative_activity_name = 'Offline' then cumulative_totaltime end) as offlineDuration
              "),
              DB::raw("SUM(case when cumulative_activity_name = 'Break' then cumulative_totaltime end) as breakDuration
              "),
              DB::raw("SUM(case when cumulative_activity_name = 'Lunch' then cumulative_totaltime end) as lunchDuration
              "),
              DB::raw("SUM(case when cumulative_activity_name = 'Meeting' then cumulative_totaltime end) as meetingDuration
              "),
              DB::raw("SUM(case when cumulative_activity_name = 'WrapUp' then cumulative_totaltime end) as wrapUpDuration
              "),
              DB::raw("SUM(case when cumulative_activity_name = 'Unavailable' then cumulative_totaltime end) as unavailableDuration
              "),
              DB::raw("SUM(case when cumulative_activity_name = 'Training' then cumulative_totaltime end) as trainingDuration
              "),
              DB::raw("SUM(case when cumulative_activity_name = 'Technical Difficulty' then cumulative_totaltime end) as technicalDifficultyDuration
              "),
              DB::raw("SUM(case when cumulative_activity_name = 'Other' then cumulative_totaltime end) as otherDuration
              "),
              DB::raw("SUM(case when cumulative_activity_name = 'Coaching' then cumulative_totaltime end) as coachingDuration
              ")
                       
        );
        // ->leftJoin('user_twilio_id','user_twilio_id.twilio_id','=','twilio_statistics_specific_Worker_activity_duration.Worker_id')
        // ->leftJoin('users','users.id','=','user_twilio_id.user_id'); 

        if(isset($request->searchText) && $request->searchText != ""){            
            $callDetails = $callDetails->where(function($query) use($request) {
                                $query->whereRaw("(SELECT id FROM users WHERE id =(SELECT user_id FROM user_twilio_id WHERE twilio_id = `twilio_statistics_specific_Worker_activity_duration`.`worker_id` LIMIT 1 ) AND CONCAT(users.first_name,' ',users.last_name) LIKE '%$request->searchText%')")
                                ->orWhereRaw("(SELECT id FROM users WHERE id =(SELECT user_id FROM user_twilio_id WHERE twilio_id = `twilio_statistics_specific_Worker_activity_duration`.`worker_id` LIMIT 1 ) AND CONCAT(users.last_name,' ',users.first_name) LIKE '%$request->searchText%')");
                              });
        }

        if(isset($request->id) && $request->id != ""){            
            $callDetails = $callDetails->where(function($query) use($request) {
                                $query->whereRaw("(SELECT id FROM users WHERE id =(SELECT user_id FROM user_twilio_id WHERE twilio_id = `twilio_statistics_specific_Worker_activity_duration`.`worker_id` LIMIT 1 ) AND users.id = ".$request->id.")");
                              });
        }



        $callDetails->whereBetween('twilio_statistics_specific_Worker_activity_duration.created_at', [$startDate, $endDate])
                    ->whereNotNull('twilio_statistics_specific_Worker_activity_duration.worker_id')                                        
                    ->groupBy('twilio_statistics_specific_Worker_activity_duration.worker_id');

        // \Log::info('Call Details Report Activity duration');
        // \Log::info($callDetails->get());
        // If this method access by ajax then return data in datatable otherwise in view (blade) file
        if($request->ajax()) {
            
            return DataTables::of($callDetails)                
                                ->editColumn('DT_RowId', function ($callDetails)  {    
                                    return $callDetails->user_id;
                                })                                
                                ->editColumn('onlineDuration', function ($callDetails)  {     
                                    return getConvertedTime($callDetails->onlineDuration);
                                })
                                ->editColumn('offlineDuration', function ($callDetails)  {
                                    return getConvertedTime($callDetails->offlineDuration);
                                })
                                ->editColumn('breakDuration', function ($callDetails)  {
                                    return getConvertedTime($callDetails->breakDuration);
                                })
                                ->editColumn('lunchDuration', function ($callDetails)  {
                                    return getConvertedTime($callDetails->lunchDuration);
                                })
                                ->editColumn('meetingDuration', function ($callDetails)  {
                                    return getConvertedTime($callDetails->meetingDuration);
                                })
                                ->editColumn('wrapUpDuration', function ($callDetails)  {                                  
                                    return getConvertedTime($callDetails->wrapUpDuration);
                                })
                                ->editColumn('unavailableDuration', function ($callDetails)  {
                                    return getConvertedTime($callDetails->unavailableDuration);
                                })
                                ->editColumn('trainingDuration', function ($callDetails)  {
                                    return getConvertedTime($callDetails->trainingDuration);
                                })
                                ->editColumn('technicalDifficultyDuration', function ($callDetails)  {
                                    return getConvertedTime($callDetails->technicalDifficultyDuration);
                                })
                                ->editColumn('otherDuration', function ($callDetails) use ($startDate,$endDate) {
                                    return getConvertedTime($callDetails->otherDuration);
                                })
                                ->editColumn('coachingDuration', function ($callDetails) use ($startDate,$endDate) {
                                    return getConvertedTime($callDetails->coachingDuration);
                                })
                                ->setRowAttr([
                                    'data-name' => function($callDetails) {
                                        return $callDetails->TPVAgent;
                                    },
                                    'data-client' => function($callDetails) {
                                        return $callDetails->client_name;
                                    },
                                ])
                                ->make(true);            
        }        

        return view('reports.agent-report.index');        
    }

}
