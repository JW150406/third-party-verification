<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\models\Client;
use App\models\Commodity;
use App\models\Dispositions;
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
use App\Services\StorageService;

use DataTables;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->storageService = new StorageService;
        //$this->middleware('permission:edit', ['only' => ['edit', 'update']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {
        if(Auth::user()->hasRole(['tpv_admin','tpv_qa']))
        {
            $client = Client::first();
            if(!empty($client)) {
                return redirect()->route('client.show', $client->id);
            } else {
                return redirect()->route("admin.tpv_recording.get");
            }
            //return redirect()->route("client.index");
        }else if(auth()->user()->access_level == 'salesagent' && auth()->user()->roles->isEmpty()) {
            return redirect()->route("my-account");
        }else if(auth()->user()->access_level == 'tpvagent'){
            return redirect()->route('tpvagents.sales');
        }else{
            return $this->get_admin_dashboard();
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
        $userid = Auth::user()->id;
        $this->validate($request, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $userid,
            'password' => $request->password != null ? 'min:6' : '',
            'password_confirmation' => 'same:password',
            'profile_picture' => 'max:5120',
        ]);
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


        if ($request->hasFile('profile_picture')) {
            Storage::disk('s3')->delete($request->old_url);
            $file = $request->file('profile_picture');
            $awsFolderPath = config()->get('constants.aws_folder');
            $filePath = config()->get('constants.USER_PROFILE_PICTURE_UPLOAD_PATH');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $this->storageService->uploadFileToStorage($file, $awsFolderPath, $filePath, $fileName);
            if ($path !== false) {
                $user->profile_picture = $path;
            }
        }

        $user->save();

        return redirect()->to(route('edit-profile'))
            ->with('success', 'Record successfully updated.');
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
                return response()->json(['status' => 'success',  'message' => 'Your profile photo was successfully deleted.']);
            } else {
                return response()->json(['status' => 'error',  'message' => 'Profile photo not found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error',  'message' => 'Something went wrong!. Please try again.']);
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
    public function get_admin_dashboard()
    {
        // dd("hello");
        if (Auth::user()->access_level == 'tpv') {
            $clients = Client::where('status', 'active')->get();
        } else if (Auth::user()->access_level == 'client') {
            $clients = Client::where('status', 'active')->where('id', auth()->user()->client_id)->get();
        } else if (Auth::user()->access_level == 'salescenter') {
            $clients = Client::where('status', 'active')->where('id', auth()->user()->client_id)->get();
        } else {
            $clients = Client::where('status', 'active')->get();
        }

        return view('/admin/dashboard', ['clients' => $clients]);
    }

    /**
     * old admin dashboard method
     */
    public function get_admin_dashboard_old()
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
        $get_records = DB::table('telesalesdata')->select("meta_value", "telesale_id")
            ->where('meta_key', '=', "_programID")
            // ->where('telesale_id','=', "10" )
            ->whereNotNull('meta_key')
            ->get();
        //dd($get_records);
        foreach ($get_records as $singlerow) {
            $program_id = $singlerow->meta_value;
            $leadid = $singlerow->telesale_id;


            try {
                $program = DB::table('programs')->find($program_id);
                if (isset($program->code)) {
                    $code = $program->code;


                    DB::table('telesalesdata')
                        ->where('telesale_id', $leadid)
                        ->where('meta_key', 'Program Code')
                        ->update(['meta_value' => $code]);
                } else {
                    echo $program_id . "<br>";
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
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
        try
        {
            if($request->ajax())
            {
                $totalDecline = (new Telesales)->getLeadDeclineCount('decline');
                $loginClientId =Auth::user()->client_id;

                $telesales = DB::table('telesales')
                    ->leftjoin('users','telesales.user_id','users.id')
                    ->leftjoin('salescenters','salescenters.id','users.salescenter_id')
                    ->leftjoin('dispositions','dispositions.id','telesales.disposition_id')
                    ->where('telesales.status','decline');

                    if(Auth::user()->hasRole(['sales_center_admin','sales_center_qa'])) {
                        $telesales->where('users.salescenter_id',Auth::user()->salescenter_id);
                    }
                    $telesales->where('telesales.client_id', $request->client_id);
                    $telesales->select( DB::raw('count(description) as count')
                    ,DB::raw('count(salescenter_id) as salescentercount ')
                    ,DB::raw("CONCAT(CONCAT(UCASE(LEFT(salescenters.name, 1)), LCASE(SUBSTRING(salescenters.name, 2)))) as name")
                    ,'description','salescenter_id'
                    )
                    ->groupBy('salescenter_id','dispositions.id')
                    ->orderBy('count','desc');

                    $telesales = $telesales->get();


                    $i = 0;
                    $j = 0;
                    $data = [];
                    foreach($telesales as $k => $v)
                    {
                        $telesales[$k]->avg = ($telesales[$i++]->count/$totalDecline[0]->count *100);
                    }
                    $dataHtml = '';
                    foreach($telesales as $key => $value)
                    {
                        $data[$telesales[$key]->salescenter_id] = [];
                        $data[$telesales[$key]->salescenter_id]['name'] = $telesales[$key]->name;
                        $data[$telesales[$key]->salescenter_id]['dispositions'] = [];
                        for($m = 0;$m < $telesales->count() ; $m++)
                        {
                            if(isset($data[$telesales[$key]->salescenter_id]['dispositions']))
                            {
                                if($telesales[$m]->salescenter_id == $telesales[$key]->salescenter_id )
                                {
                                    $data[$telesales[$key]->salescenter_id]['dispositions'][$m] = $telesales[$m]->description.",<span style='text-align:right;'>". number_format((float)$telesales[$m]->avg, 2, '.', '')."%</span>";
                                }
                            }
                        }
                    }


                    $dataHtml = "";
                    foreach ($data as $k => $v)
                    {
                        $dispositions_count = count($v['dispositions']);
                        if($dispositions_count >= 3)
                        {
                            $row = 3;
                        }
                        else
                            $row = $dispositions_count;
                        $count = 0;
                        $dataHtml .="<tr><th rowspan='".$row."'>" .$data[$k]['name']."</th>";

                        foreach($data[$k]['dispositions'] as $value)
                        {
                            $percentage = explode(',',$value);
                            $dataHtml .="<th>" .$percentage[0]."</th><td>".end($percentage)."</td></tr>";
                            if(++$count > 2)
                                 break;
                        }
                    }
                    return response()->json([
                        'status' => true,
                        'data' => $dataHtml
                    ]);
            }
        }
        catch (Exception $e)
        {
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
        foreach($taleSalesStatusList as $sKey => $sValue) {
            foreach($teleSalesStatusData as $rKey => $rValue){
                $leadsTotal = $teleSalesStatusData[$rKey]->sum();
                $teleSalesLeadsData[$sValue->status]['status'] = config('constants.VERIFICATION_STATUS_CHART.'.ucfirst($sValue->status));//ucfirst($sValue->status);
                $teleSalesLeadsData[$sValue->status][$rKey] = (isset($rValue[$sValue->status]) ? $rValue[$sValue->status] .','. number_format((float)$rValue[$sValue->status] / $leadsTotal * 100, 2, '.', '').'%' : '0,0.00%');

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

        $taleSalesStatusCount['statusList'] = Telesales::groupBy('status')->select('status',DB::raw("(CASE WHEN status = 'verified' THEN 'Verified' WHEN status = 'cancel' THEN 'Cancelled' WHEN status = 'decline' THEN 'Declined' WHEN status = 'hangup' THEN 'Disconnected' ELSE 'Pending' END) as status"))->pluck('status');

        $query = Telesales::getLeadsByClientId($client_id)
            ->getLeadsByRange($start_date, $end_date, 'telesales.created_at')
            ->leftJoin('users', 'users.id', '=', 'telesales.user_id')
            ->select(DB::raw('COUNT(telesales.status) as value'),DB::raw("(CASE WHEN telesales.status = 'verified' THEN 'Verified' WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' ELSE 'Pending' END) as name"));
            if(Auth::user()->hasRole(['sales_center_admin','sales_center_qa'])) {
                $query->where('users.salescenter_id',Auth::user()->salescenter_id);
            }
        $taleSalesStatusCount['reportData'] =$query->groupBy('telesales.status')->get()->toJson();
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

        // dd($request->all());
        $start_date = Carbon::parse($request->start_date)->format('Y/m/d');
        $end_date = Carbon::parse($request->end_date)->format('Y/m/d');
        $verification_status =  config('constants.VERIFICATION_STATUS_CHART_LEADS.'.$request->verification_status);


        $telesalesQuery = Telesales::select('telesales.*', DB::raw("(CASE WHEN telesales.status = 'verified' THEN 'Verified' WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' ELSE 'Pending' END) as statusname"), DB::raw("(GROUP_CONCAT(commodities.name SEPARATOR ', ')) as `commodity_name`"))
                    ->leftJoin('form_commodities', 'form_commodities.form_id', '=', 'telesales.form_id')
                    ->leftJoin('commodities', 'commodities.id', '=', 'form_commodities.commodity_id')
                    ->leftJoin('users', 'users.id', '=', 'telesales.user_id')
                    ->leftJoin('salescenters', 'salescenters.id', '=', 'users.salescenter_id')
                    ->leftJoin('salesagent_detail', 'salesagent_detail.user_id', '=', 'users.id');
                // dd($telesalesQuery->get());
        if (!empty($request->channel_type)) {
            // $telesalesQuery->leftJoin('users', 'users.id', '=', 'telesales.user_id');
            //$telesalesQuery->leftJoin('salescenters', 'salescenters.id', '=', 'users.salescenter_id');
            // $telesalesQuery->leftJoin('salesagent_detail', 'salesagent_detail.user_id', '=', 'users.id');
            $status = strtolower(str_replace(' Sales', '', $request->channel_type));
            $telesalesQuery->where('salesagent_detail.agent_type', $status);
        }

        if (!empty($request->client_id)) {
            $telesalesQuery->where('telesales.client_id', $request->client_id);
        }
        if(Auth::user()->hasRole(['sales_center_admin','sales_center_qa'])) {
            $telesalesQuery->where('users.salescenter_id',Auth::user()->salescenter_id);
        }

        if (!empty($request->agent_id)) {
            $telesalesQuery->where('telesales.user_id', $request->agent_id);
        }

        if (!empty($request->sales_center_id)) {
            $getSalesCenterLeads = User::select('id')->where('salescenter_id', $request->sales_center_id)->get()->pluck('id');
            $telesalesQuery->whereIn('telesales.user_id', $getSalesCenterLeads);

        }


        if (!empty($start_date) && !empty($end_date)) {

            $end_date = Carbon::parse($end_date)->addDays(1);
            $telesalesQuery->whereBetween('telesales.created_at', [$start_date, $end_date]);

        }

        if (!empty($verification_status)) {
            $status = strtolower($verification_status);
            $telesalesQuery->where('telesales.status', $status);
        }

        if (!empty($request->commodity_type)) {

            if($request->commodity_type == 'both'){
                $commoditytype = strtolower($request->commodity_type);
                $telesalesQuery->where('commodities.client_id', $request->client_id);
                $telesalesQuery->whereIn('commodities.name', ['Electric', 'Gas']);
            }else{
                $commoditytype = strtolower($request->commodity_type);
                $telesalesQuery->where('commodities.client_id', $request->client_id);
                $telesalesQuery->where('commodities.name', $commoditytype);
            }
        }


        if(!empty($request->sales_type))
            {
                if($request->sales_type == "good"){
                    $telesalesQuery->where('telesales.status','verified');
                }
                if($request->sales_type == "bad")
                    $telesalesQuery->whereIn('telesales.status',['decline','cancel']);
            }
            if(!empty($request->verification_method))
            {
                $verification_method_id = config('constants.VERIFICATION_METHOD_FOR_REPORT.'.$request->verification_method);
                $telesalesQuery->where('verification_method',$verification_method_id);
            }
            if(!empty($request->agent_type))
            {
                    $telesalesQuery->where('salesagent_detail.agent_type',$request->agent_type);
            }

        $telesalesQuery->groupBy('telesales.id', 'form_commodities.form_id');
        $telesalesQuery->orderBy('id','desc');

        $telesalesLeadsData = $telesalesQuery->get();

        $taleSalesStatusData = [];
        $i = 0;
        foreach ($telesalesLeadsData as $key => $value) {
            $channel = '';
            if (!empty($value->user) && !empty($value->user->salesAgentDetails->agent_type)) {
                $channel = strtoupper($value->user->salesAgentDetails->agent_type);
            }

            $zipcode = '';
            $getZipcode = Telesalesdata::where('meta_key', 'service_zipcode')->where('telesale_id', $value->id)->first();
            if ($getZipcode) {
                $zipcode = $getZipcode->meta_value;
            }

            $tpv_agent = 'NA';
            if (!empty($value->reviewed_by)) {
                $tpv_agent_user = User::find($value->reviewed_by);
                if ($tpv_agent_user && ($value->verification_method == 1 || $value->verification_method == 2)) {
                    $tpv_agent = $tpv_agent_user->full_name;
                }
            }

            $taleSalesStatusData[$i]['Status'] = $value->statusname;
            $taleSalesStatusData[$i]['Lead#'] = $value->refrence_id;
            $taleSalesStatusData[$i]['Date'] = $value->created_at;
            $taleSalesStatusData[$i]['Channel'] = $channel;
            $taleSalesStatusData[$i]['Sales Agent'] = $value->user->first_name;
            $taleSalesStatusData[$i]['Sales Center'] = $value->user->salescenter->name;
            $taleSalesStatusData[$i]['Commodity'] = $value->commodity_name;
            $taleSalesStatusData[$i]['Zipcode'] = $zipcode;
            $taleSalesStatusData[$i]['TPV Agent'] = $tpv_agent;
            $verification_method ='';
            if($value->verification_method == 1)
                $verification_method = 'Customer Inbound';
            if($value->verification_method == 2)
                $verification_method = 'Agent Inbound';
            if($value->verification_method == 3)
                $verification_method = 'Email';
            if($value->verification_method == 4)
                $verification_method = 'Text';

            $taleSalesStatusData[$i]['Verification Method'] = $verification_method;
            $i++;
        }

        $sheetName = $request->sheet_name;
        Excel::create($request->sheet_title, function ($excel) use ($taleSalesStatusData,$sheetName) {

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
                if(Auth::user()->hasRole(['sales_center_admin','sales_center_qa'])) {
                    $getVendorList->where('users.salescenter_id',Auth::user()->salescenter_id);
                }
                $getVendorList = $getVendorList->select('salescenters.id', 'salescenters.name',DB::raw("(CASE WHEN telesales.status = 'verified' THEN 'Verified' WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' ELSE 'Pending' END) as status"))
            ->get();

        $agentReports['vendorList'] = [];
        $agentReports['leads'] = [];
        $agentReports['status'] = [];

        //$getAgentsList = $getAgentsList->sortBy('leads');

        foreach ($getVendorList as $vendor) {
            $agentReports['vendorList'][] = ucfirst($vendor->name) . '&' . $vendor->id;
            foreach ($getStatuList as $statusList) {
                $getUpdatedStatusName = config('constants.VERIFICATION_STATUS_CHART_LEADS.'.$statusList->status);

                $getUpdatedStatusName = config('constants.VERIFICATION_STATUS_CHART_LEADS.'.$statusList->status);

                $getVendorCount = Telesales::leftJoin('users', 'users.id', '=', 'telesales.user_id')
                ->leftJoin('salescenters', 'salescenters.id', '=', 'users.salescenter_id')
                ->whereBetween('telesales.created_at', [$start_date, $end_date])
                ->where([['telesales.client_id', $client_id], ['salescenters.id', $vendor->id], ['telesales.status', $getUpdatedStatusName]])
                ->groupBy('telesales.status')
                ->count();

                $agentReports['leads'][$statusList->status][] = $getVendorCount;
            }
        }
        if(count($agentReports['leads']) >0){
            $agentReports['status'] = $getStatuList->pluck('status')->toJson();
            $finalarray = $agentReports;
        }
        else {
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
           $getUpdatedStatusName = config('constants.VERIFICATION_STATUS_CHART_LEADS.'.$status->status);

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

        $status =['verified'];
        $taleSalesData = $this->getData($client_id,$start_date,$end_date,"d2d",$status);

        $getd2dLeads = [];
        $getd2dLeads[0]['name'] = 'Customer Inbound';
        $getd2dLeads[1]['name'] = 'Agent Inbound';
        $getd2dLeads[2]['name'] = 'Email';
        $getd2dLeads[3]['name'] = 'Text';
            $i = 0;
            foreach($taleSalesData as $key => $value)
            {
                if($value->verification_method ==1)
                {
                    $getd2dLeads[0]['value'] = $value->countByMethod;
                }

                else if($value->verification_method ==2)
                {
                    $getd2dLeads[1]['value'] = $value->countByMethod;
                }
                else if($value->verification_method ==3)
                {
                    $getd2dLeads[2]['value'] = $value->countByMethod;
                }
                else if($value->verification_method ==4)
                {
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
    public function getData($client_id,$start_date,$end_date,$agentType,$status)
    {

        $telesales = Telesales::getLeadsByClientId($client_id)
            ->getLeadsByRange($start_date, $end_date)
            ->leftjoin('users','users.id','telesales.user_id');
            if(Auth::user()->hasRole(['sales_center_admin','sales_center_qa'])) {
                $telesales->where('users.salescenter_id',Auth::user()->salescenter_id);
            }
            $telesales = $telesales->whereHas('user.salesAgentDetails', function ($query) use($agentType,$status) {
                $query->where('agent_type',$agentType);
            })->whereIn('telesales.status',$status)->select(
                DB::raw('count(user_id) as countByMethod'),
                'telesales.id','user_id','verification_method','telesales.status')
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
        $status = ['decline','cancel'];
        $taleSalesBadData = $this->getData($client_id,$start_date,$end_date,"d2d",$status);

        $getd2dBadLeads = [];
        $getd2dBadLeads[0]['name'] = 'Customer Inbound';
        $getd2dBadLeads[1]['name'] = 'Agent Inbound';
        $getd2dBadLeads[2]['name'] = 'Email';
        $getd2dBadLeads[3]['name'] = 'Text';
        $i = 0;
        foreach($taleSalesBadData as $key => $value)
        {
            if($value->verification_method == 1)
            {

                $getd2dBadLeads[0]['value'] = $value->countByMethod;

            }
            else if($value->verification_method == 2)
            {
                $getd2dBadLeads[1]['value'] = $value->countByMethod;
            }
            else if($value->verification_method == 3)
            {
                $getd2dBadLeads[2]['value'] = $value->countByMethod;
            }
            else if($value->verification_method == 4)
            {
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
        $status =['verified'];
        $taleSalesData = $this->getData($client_id,$start_date,$end_date,"tele",$status);
        $getteleLeads = [];
        $getteleLeads[0]['name'] = 'Customer Inbound';
        $getteleLeads[1]['name'] = 'Agent Inbound';
        $getteleLeads[2]['name'] = 'Email';
        $getteleLeads[3]['name'] = 'Text';
        $i = 0;

            foreach($taleSalesData as $key => $value)
        {

            if($value->verification_method ==1)
            {

                $getteleLeads[0]['value'] = $value->countByMethod;
            }

            else if($value->verification_method ==2)
            {
                $getteleLeads[1]['value'] = $value->countByMethod;
            }
            else if($value->verification_method ==3)
            {
                $getteleLeads[2]['value'] = $value->countByMethod;
            }
            else if($value->verification_method == 4)
            {
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
        $status =['decline','cancel'];
        $taleSalesData = $this->getData($client_id,$start_date,$end_date,"tele",$status);

        $gettelebadLeads = [];
        $gettelebadLeads[0]['name'] = 'Customer Inbound';
        $gettelebadLeads[1]['name'] = 'Agent Inbound';
        $gettelebadLeads[2]['name'] = 'Email';
        $gettelebadLeads[3]['name'] = 'Text';
        $i = 0;
        foreach($taleSalesData as $key => $value)
        {
            if($value->verification_method ==1)
            {
                $gettelebadLeads[0]['value'] = $value->countByMethod;
            }

            else if($value->verification_method ==2)
            {
                $gettelebadLeads[1]['value'] = $value->countByMethod;
            }
            else if($value->verification_method ==3)
            {
                $gettelebadLeads[2]['value'] = $value->countByMethod;
            }
            else if($value->verification_method ==4)
            {
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

            if(Auth::user()->hasRole(['sales_center_admin','sales_center_qa'])) {
                $getVendorList->where('users.salescenter_id',Auth::user()->salescenter_id);
            }

            $getVendorList = $getVendorList->groupBy('salescenters.id', 'salescenters.name','telesales.status', 'salesagent_detail.agent_type')
            ->get(['salescenters.id', 'salescenters.name',DB::raw("(CASE WHEN telesales.status = 'verified' THEN 'Verified' WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' ELSE 'Pending' END) as status"), 'salesagent_detail.agent_type' , DB::raw('count(telesales.id) as count')]);

        $getVendors = $getVendorList->pluck('name','id')->unique();
        $getVendorsNames = [];
        foreach($getVendors as $k => $v)
        {
            $getVendorsNames[] = $k."|".$v;
        }
        $agentReports = [];
        $televendorList = [];
        $d2dvendorList = [];
        $agentReports['leads'] = [];
        $agentReports['status'] = [];

        foreach($getStatuList as $status) {
            for($i = 0 ; $i < count($getVendors) ;$i++)
            {
                $agentReports['leads']['d2d'][$status->status][$i] = 0;
                $agentReports['leads']['tele'][$status->status][$i] = 0;
            }
        }

        foreach ($getVendorList as $key => $val) {

            $vender_name = $val->name.'&'.$val->id;
            if(!in_array($vender_name, $televendorList, true)){
                $televendorList[] = $vender_name;
            }

            if(!in_array($vender_name, $d2dvendorList, true)){
                $d2dvendorList[] = $vender_name;
            }
            $findIndex = array_search($val->id."|".$val->name,$getVendorsNames,true);
            if($val->agent_type == 'tele'){
                $agentReports['leads']['tele'][$val->status][$findIndex] = $val->count;
            }elseif($val->agent_type == 'd2d'){
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
            ->whereIn('commodities.name', ['Gas','Electric']);

            if(Auth::user()->hasRole(['sales_center_admin','sales_center_qa'])) {
                $getVendorList->where('users.salescenter_id',Auth::user()->salescenter_id);
            }

            $getVendorList = $getVendorList->groupBy('salescenters.id', 'salescenters.name','telesales.status', 'commodities.name')
            ->get(['salescenters.id', 'salescenters.name',DB::raw("(CASE WHEN telesales.status = 'verified' THEN 'Verified' WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' ELSE 'Pending' END) as status"), 'form_commodities.commodity_id', 'commodities.name as commodity_name' , DB::raw('count(telesales.id) as count')]);

        $getVendors = $getVendorList->pluck('name','id')->unique();
        // dd($getVendors);
        $getVendorsNames = [];
        foreach($getVendors as $k => $v)
        {
            $getVendorsNames[] = $k;
        }

        $agentReports = [];
        foreach($getStatuList as $status) {

            for($i = 0 ; $i < count($getVendors) ;$i++)
            {
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
            $vender_name = $val->name.'&'.$val->id;
            if(!in_array($vender_name, $electricvendorList, true)){
                $electricvendorList[] = $val->name.'&'.$val->id;
            }

            if(!in_array($vender_name, $gasvendorList, true)){
                $gasvendorList[] = $val->name.'&'.$val->id;
            }
            $findIndex = array_search($val->id,$getVendorsNames,true);
            if($val->commodity_name == 'Electric'){
                $agentReports['leads']['electric'][$val->status][$findIndex] = $val->count;
            }elseif($val->commodity_name == 'Gas'){
                $agentReports['leads']['gas'][$val->status][$findIndex] = $val->count;
            }else{
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

        if ($request->ajax()) {
            //$telesales = Telesales::select('telesales.*', DB::raw('CONCAT(UCASE(LEFT(telesales.status, 1)), LCASE(SUBSTRING(telesales.status, 2))) as statusname'), DB::raw("(GROUP_CONCAT(commodities.name SEPARATOR ', ')) as `commodity_name`"), 'telesalesdata.meta_value as zipcode');


            $telesales = Telesales::select('telesales.*', DB::raw("(CASE WHEN telesales.status = 'verified' THEN 'Verified' WHEN telesales.status = 'cancel' THEN 'Cancelled' WHEN telesales.status = 'decline' THEN 'Declined' WHEN telesales.status = 'hangup' THEN 'Disconnected' ELSE 'Pending' END) as statusname"), DB::raw("(GROUP_CONCAT(commodities.name SEPARATOR ', ')) as `commodity_name`"));
            $telesales->leftJoin('form_commodities', 'form_commodities.form_id', '=', 'telesales.form_id');
            $telesales->leftJoin('commodities', 'commodities.id', '=', 'form_commodities.commodity_id');
            $telesales->leftJoin('users', 'users.id', '=', 'telesales.user_id');
            $telesales->leftJoin('salescenters', 'salescenters.id', '=', 'users.salescenter_id');
            $telesales->leftJoin('salesagent_detail', 'salesagent_detail.user_id', '=', 'users.id');
            //$telesales->leftJoin('telesalesdata', 'telesalesdata.telesale_id', '=', 'telesales.id');
            //  \Log::info($telesales->get());

            if (!empty($request->channeltype)) {

                $status = strtolower(str_replace(' Sales', '', $request->channeltype));
                $telesales->where('salesagent_detail.agent_type', $status);
            }

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start_date = Carbon::parse($request->start_date)->startOfDay();
                $end_date = Carbon::parse($request->end_date)->endOfDay();
                $telesales->whereBetween('telesales.created_at', [$start_date, $end_date]);
            }

            if (!empty($request->status)) {
                $getStatus = config('constants.VERIFICATION_STATUS_CHART_LEADS.'.$request->status);
                if(!empty($getStatus)){
                    $status = strtolower($getStatus);
                }else{
                    $status = strtolower($request->status);
                }
                $telesales->where('telesales.status', $status);
            }

            if (!empty($request->client_id)) {
                $telesales->where('telesales.client_id', $request->client_id);
            }
            if(Auth::user()->hasRole(['sales_center_admin','sales_center_qa'])) {
                $telesales->where('users.salescenter_id',Auth::user()->salescenter_id);
            }
            if (!empty($request->agent_id)) {
                $telesales->where('telesales.user_id', $request->agent_id);
            }

            if (!empty($request->sales_center_id)) {
                $getSalesCenterLeads = User::select('id')->where('salescenter_id', $request->sales_center_id)->get()->pluck('id');
                $telesales->whereIn('telesales.user_id', $getSalesCenterLeads);
            }

            if (!empty($request->commoditytype)) {
                if($request->commoditytype == 'both'){
                    $commoditytype = strtolower($request->commoditytype);
                    $telesales->where('commodities.client_id', $request->client_id);
                    $telesales->whereIn('commodities.name', ['Electric', 'Gas']);
                }else{
                    $commoditytype = strtolower($request->commoditytype);
                    $telesales->where('commodities.client_id', $request->client_id);
                    $telesales->where('commodities.name', $commoditytype);
                }
            }

            if(!empty($request->sales_type))
            {
                if($request->sales_type == "good"){
                    $telesales->where('telesales.status','verified');
                    Log::info($telesales->get());
                }
                if($request->sales_type == "bad")
                    $telesales->whereIn('telesales.status',['decline','cancel']);
            }
            if(!empty($request->agent_type))
            {
                $telesales->where('salesagent_detail.agent_type',$request->agent_type);
            }
            if(!empty($request->verificaiton_method))
            {
                $verificaiton_method_id = config('constants.VERIFICATION_METHOD_FOR_REPORT.'.$request->verificaiton_method);
                $telesales->where('verification_method',$verificaiton_method_id);
            }

            //$telesales->where('telesalesdata.meta_key', 'service_zipcode');

            $telesales->groupBy('telesales.id', 'form_commodities.form_id');
            //Log::info($telesales->get());

            return DataTables::of($telesales)
                ->addColumn('channel', function ($telesales) {

                    $channel = '';
                    if (!empty($telesales->user) && !empty($telesales->user->salesAgentDetails->agent_type)) {
                        $channel = strtoupper($telesales->user->salesAgentDetails->agent_type);
                    }
                    return $channel;
                })
                ->addColumn('sales_agent', function ($telesales) {
                    $sales_agent = '';
                    if (!empty($telesales->user)) {
                        $sales_agent = $telesales->user->full_name;
                    }
                    return $sales_agent;
                })
                ->addColumn('salescenter_name', function ($telesales) {
                    $salescenter_name = '';
                    if (!empty($telesales->user) && !empty($telesales->user->salescenter)) {
                        $salescenter_name = $telesales->user->salescenter->name;
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
                        $tpv_agent_user = User::find($telesales->reviewed_by);
                        if ($tpv_agent_user && ($telesales->verification_method == 1 || $telesales->verification_method == 2)) {
                            $tpv_agent = $tpv_agent_user->full_name;
                        }
                    }
                    return $tpv_agent;
                })
                ->addColumn('verification_method', function ($telesales) {
                    $verification_method = '';
                    if($telesales->verification_method == 1)
                        $verification_method = "Customer Inbound";
                    if($telesales->verification_method == 2)
                        $verification_method = "Agent Inbound";
                    if($telesales->verification_method == 3)
                        $verification_method = "Email";
                    if($telesales->verification_method == 4)
                        $verification_method = "Text";

                    return $verification_method;
                })
                ->make(true);
        }
    }

    /**
     * This method is used for load map as per user's details amd zipcode
     */
    public function loadMapZipcode(Request $request)
    {

        $client_id = $request->client_id;
        $start_date = Carbon::parse($request->start_date)->startOfDay();
        $end_date = Carbon::parse($request->end_date)->endOfDay();

        $leadsByZipcodes = Telesales::getLeadsByClientId($client_id)
            ->getLeadsByRange($start_date, $end_date)
            // ->leftjoin('users','users.id','=','telesales.user_id')
            ->select('telesales.status','telesales.id as telesale_id','telesales.refrence_id as reference_id',DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_address_1' and telesale_id =telesales.id LIMIT 1) as service_address_1"),
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
            DB::raw("(select userid from users where id = telesales.user_id) as agent_id"))->get();

            $serviceAddress = $leadsByZipcodes->toArray();
            $config['map_div_id'] = "map_zipcode";
            $config['map_height']  = "350px";
            $config['cluster'] = true;
            $config['clusterStyles'] = array(
                array(
                'url'=>asset('images/marker_image/m1.png'),
                "width"=>"55",
                "height"=>"55"
                ));
            $config['clusterMaxZoom'] = '18';
            $gmap = new GMaps();
            $gmap->initialize($config);
            $serviceLatLngMapping = [];
            $leadDetailsMapping = [];


            if(count($serviceAddress) > 0 && isset($serviceAddress))
            {
                foreach($serviceAddress as $key => $val)
                {
                    $leadDetailsMapping[$val['telesale_id']] = $val;
                    if(isset($serviceLatLngMapping[$val['service_lat'].",".$val['service_lng']]))
                    {
                        $serviceLatLngMapping[$val['service_lat'].",".$val['service_lng']][] = $val['telesale_id'];
                    }
                    else
                        $serviceLatLngMapping[$val['service_lat'].",".$val['service_lng']][] = $val['telesale_id'];
                }

                foreach($serviceLatLngMapping as $key => $val)
                {
                    $toolTipText = "<div id = 'infowindow_div'>";
                    $hasMultipleLeads = false;
                    if(count($val) > 1) {
                        $hasMultipleLeads = true;
                    }
                    for($i=0;$i<count($val);$i++)
                    {
                        $status = $leadDetailsMapping[$val[$i]]['status'];
                        $full_name = implode(" ",array_filter(array($leadDetailsMapping[$val[$i]]['first_name'],$leadDetailsMapping[$val[$i]]['middle_name'],$leadDetailsMapping[$val[$i]]['last_name'])));

                        $address = implode(", ",array_filter(array($leadDetailsMapping[$val[$i]]['service_address_1'],$leadDetailsMapping[$val[$i]]['service_address_2'],$leadDetailsMapping[$val[$i]]['service_unit'],$leadDetailsMapping[$val[$i]]['service_city'],$leadDetailsMapping[$val[$i]]['service_state'],$leadDetailsMapping[$val[$i]]['service_zipcode'])));

                        $agent_info = "(".implode(" ",array_filter(array($leadDetailsMapping[$val[$i]]['agent_first_name'],$leadDetailsMapping[$val[$i]]['agent_last_name'])))." - ".$leadDetailsMapping[$val[$i]]['agent_id'].")";

                        $route = route('telesales.show',$val[$i]);
                        $toolTipText .= "<p class = 'full-name-lead'>".$full_name."</p><p class = 'referece-lead'><strong><a href = '".$route."' target='_blank'>" . $leadDetailsMapping[$val[$i]]['reference_id']. "</a></strong></p>";
                        if(!($hasMultipleLeads))
                        {
                            $toolTipText .= "<p class = 'address-lead address-lead-single'>".$address."</p>";
                        }
                        else{
                            $toolTipText .= "<p class = 'address-lead'>".$address."</p>";
                        }
                        $toolTipText .= "<p class = 'agent-lead'><strong>".$agent_info."</strong></p>";
                        $status_new = config('constants.VERIFICATION_STATUS_CHART.'.ucfirst($status));
                        if($hasMultipleLeads) {
                            switch($status) {
                                case 'pending':
                                   $toolTipText .= "<p class = 'label label-primary status-btn pending text-center'>".$status_new." </p>";
                                    break;
                                case 'hangup':
                                    $toolTipText .= "<p class = 'label label-primary status-btn disconnect'>".$status_new." </p>";
                                    break;
                                case 'cancel':
                                    $toolTipText .= "<p class = 'label label-primary status-btn cancel'>".$status_new." </p>";
                                    break;
                                case 'verified':
                                    $toolTipText .= "<p class = 'label label-primary status-btn verify'>".$status_new." </p>";
                                    break;
                                case 'decline':
                                    $toolTipText .= "<p class = 'label label-primary status-btn decline'>".$status_new." </p>";
                                    break;
                                }
                                $toolTipText .= "<div class ='hr'></div>";
                                if($hasMultipleLeads) {
                                    $status = "multiple";
                                }
                        }

                    }
                    $marker['position'] = $key;
                    $marker['id'] = $val[0];
                    $marker['click'] = "addMarkerWithWindow('".$val[0]."')";
                    $marker['label'] =  "";
                    $markerIcon = '';
                    switch($status) {
                    case 'pending':
                        $markerIcon = asset('images/marker_image/pins/pin-pending.png');

                        break;
                    case 'hangup':
                        $markerIcon = asset('images/marker_image/pins/pin-disconnected.png');

                        break;
                    case 'cancel':
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
        $salesAgentLocation = Salesagentlocation::leftjoin('users','users.id','=',  'salesagentlocations.salesagent_id')
            ->leftjoin('salescenters','salescenters.id','=','users.salescenter_id')
            ->select('salesagentlocations.lat','salesagentlocations.lng','salesagentlocations.updated_at','salescenters.name','salesagentlocations.id','users.first_name','users.last_name','salesagentlocations.salesagent_id')
            ->orderBy('salesagentlocations.created_at', 'DESC')
            ->groupBy('salesagentlocations.salesagent_id')
            ->get();
            if($salesAgentLocation->count() > 0 ){
                $config['center'] = $salesAgentLocation[0]->lat.",".$salesAgentLocation[0]->lng;
            }

            $config['map_div_id'] = "map_salesagent";
            $config['map_height']  = "350px";

            $gmap = new GMaps();
            $gmap->initialize($config);

            if($salesAgentLocation->count() > 0 && isset($salesAgentLocation))
            {

            foreach ($salesAgentLocation as $key => $value)
            {
                $tooltipText = '';
                $dateTime = explode(" ",$value->updated_at);
                $date = date_create($dateTime[0]);
                $date = $date->format('d-M-Y');
                $tooltipText .= "Agent ID = ".$value->salesagent_id."<br/>";
                $tooltipText .= "Agent Name = ".$value->first_name." ".$value->last_name."<br/>";
                $tooltipText .= "Sales Center Name = ".$value->name."<br/>";
                $tooltipText .= "Location Date = ".$date."<br/>";
                $tooltipText .= "Location Time = ".$dateTime[1]."<br/>";
                $marker['position'] = $salesAgentLocation[$key]->lat.",".$salesAgentLocation[$key]->lng;
                $marker['infowindow_content'] = $tooltipText;
                $marker['title'] = $value->first_name." ".$value->last_name;
                $marker['id'] = $value->id;
                $marker['click'] = "addMarkerWithWindow('".$value->id."')";
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
            'Email'=> [
                'today' => 0,
                'WTD' => 0,
                'MTD' => 0,
                'YTD' => 0,
            ],
            'TPV Time' =>[
                'today' => 0,
                'WTD' => 0,
                'MTD' => 0,
                'YTD' => 0,
            ]
        ];
        $type = 0;
        foreach($EmailText as $k => $v)
        {
            if($k == 'Text')
                $type = 2;
            else if ($k == 'Email')
                $type = 1;
            else
                $type = 3;
            $EmailText[$k]['today'] = $this->getStatusCount(Carbon::parse($todayDate)->startOfDay(),Carbon::parse($todayDate)->endOfDay(),$type);
            $EmailText[$k]['WTD'] = $this->getStatusCount(Carbon::parse($todayDate)->startOfWeek(),Carbon::parse($todayDate)->endOfWeek(),$type);
            $EmailText[$k]['MTD'] = $this->getStatusCount(Carbon::parse($todayDate)->startOfMonth(),Carbon::parse($todayDate)->endOfMonth(),$type);
            $EmailText[$k]['YTD'] = $this->getStatusCount(Carbon::parse($todayDate)->startOfYear(),Carbon::parse($todayDate)->endOfYear(),$type);
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
    public function getStatusCount($startDate,$endDate,$type)
    {
        if($type == 3)
        {
            $value = Telesales::whereBetween('reviewed_at',array($startDate,$endDate))
                ->sum('call_duration');
            $dt = Carbon::now();
            $hours = $dt->diffInHours($dt->copy()->addSeconds($value));
            $minutes = $dt->diffInMinutes($dt->copy()->addSeconds($value)->subHours($hours));
            $seconds = $dt->diffInSeconds($dt->copy()->addSeconds($value)->subHours($hours)->subMinutes($minutes));

            if(strlen((string)$hours) == 1)
                $hours = "0".$hours;
            if(strlen((string)$minutes) == 1)
                $minutes = "0".$minutes;
            if(strlen((string)$seconds) == 1)
                $seconds = "0".$seconds;

            return $hours." : ".$minutes." : ".$seconds;

        }
        else
        {
            return TextEmailStatistics::where('type',$type)
                ->whereBetween('created_at',array($startDate,$endDate))
                ->groupBy('type')->count();
        }
    }
}