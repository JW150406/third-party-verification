<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request; 
use App\Http\Controllers\Controller;
use App\User;
use DB;

use Illuminate\Support\Facades\Auth;

use Validator;
use App\models\Telesales;
use App\models\Client;
use App\models\Salescenter;
use App\Traits\CustomTrait;
use Carbon\Carbon;
use App\Services\StorageService;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller

{
    use CustomTrait;

    public $successStatus = 200;


    
    /**
     * Login API
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request){ 
        
            // code to enable user id in login process  
            $params=array();
            $value = $request->get('email');
            $field = filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)
                ? 'email'
                : 'userid';
            $params[$field] = $value;
            // code to enable user id in login process end

            // code to enable user id in login process  (altered)
            $validator = Validator::make(array_merge($request->only('password'),$params), [
                $field => 'required|string',
                'password' => 'required'
            ]);
             
            if ($validator->fails()) {  
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->messages()->all()
                ], 400); 
            }else{

                $credentials = request(['email', 'password']);

                // code to enable user id in login process  
                if($field != 'email'){
                    $credentials[$field] = $credentials['email'];
                    unset($credentials['email']);
                }

                // code to enable user id in login process  

                if(!Auth::attempt($credentials)){
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid username or password!'
                    ], 400);
                }else if (Auth::user()->status != 'active') {
                    Auth::logout();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Your account is deactivated. Please contact your administrator.'
                    ], 400);

                }else if (Auth::user()->access_level != 'salesagent' && Auth::user()->access_level != 'client') {
                    Auth::logout();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You are not login as a sales agent/client. Please login as a sales agent/client.'
                    ], 400);
                }else if (Auth::user()->isAccessLevelToClient() && isset(Auth::user()->client) && Auth::user()->client->status == 'inactive') {
                    Auth::logout();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Your client is deactivated. Please contact your administrator for assisstance.'
                    ], 400);
                } else if(Auth::user()->hasAccessLevels('salesagent') && isset(Auth::user()->salescenter) && Auth::user()->salescenter->status != 'active') {
                    Auth::logout();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Your sales center is deactivated. Please contact your administrator for assisstance.'
                    ], 400);
                }else if ((empty(Auth::user()->salesAgentDetails) || (!empty(Auth::user()->salesAgentDetails) && Auth::user()->salesAgentDetails->agent_type != 'd2d')) && ( empty(Auth::user()->roles->first()) || (!empty(Auth::user()->roles->first())) && (Auth::user()->roles->first()->name != 'client_admin')) ) {
                    Auth::logout();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You are not login as a d2d sales or client admin agent type.'
                    ], 400);
                }else{
                     $user = Auth::user();

                     $user_type = '';
                     if($user->access_level == 'salesagent'){
                        if(!isOnSettings($user->client_id,'is_enable_d2d_app')) {
                            Auth::logout();
                            return response()->json([
                                'status' => 'error',
                                'message' => 'D2D app settings is switch off. Please contact your administrator for assisstance.'
                            ], 400);
                        }
                        $user_type = 'd2d_agent';
                        $dashboardUrl = "";
                     }else if($user->access_level == 'client'){
                        $user_type = Auth::user()->roles->first()->name;
                        $dashboardUrl = route("mobile-dashboard");
                     }
					 $userDet = $this->getUserDetails();
					
					 $userDet->user_type = $user_type;
					 $userDet->dashboard_url = $dashboardUrl;
					 unset($userDet['client']);
					 unset($userDet['salescenter']);
					 unset($userDet['salesAgentDetails']);
					 if($userDet->access_level == 'client')
					 {
						 unset($userDet['roles']);
					 }
                     $success['status'] = 'success';
					 $success['message'] = 'Login successfully';
					 $success['data'] = $userDet;
                    //  $success['data'] = array(
                    //             'first_name' => $user->first_name,
                    //             'last_name' =>  $user->last_name,
                    //             'email' =>  $user->email,
                    //             'userid' => $user->userid,
					// 			'client_id' => $user->client_id,
					// 			'client_name' =>$userDet->client_name,
					// 			'sales_center_name' => $userDet->sales_center_name,
					// 			'timezone' => $userDet->timezone,
					// 			'profile_picture' =>$userDet->profile_picture,
					// 			'access_level' => $user->access_level,
					// 			'user_type' => $user_type,
					// 			'full_name' =>$userDet->full_name,
					// 			'status'=> $userDet->status,
					// 			'location_id' => $userDet->location_id,
                    //             'dashboard_url' => $dashboardUrl
                    //  );

                     $success['token'] =  $user->createToken('tpv')->accessToken;

                    return response()->json($success, $this->successStatus, [], JSON_UNESCAPED_SLASHES);
                }  

            }    

    } 

    /**
     * This method is used to fetch all the details of auth user
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        // $user = Auth::user();
        // $client = Client::find(array_get($user, 'client_id'));
        // $user->client_name = array_get($client, 'name', NULL);
        // $salesCenter = Salescenter::find(array_get($user, 'salescenter_id'));
        // $user->sales_center_name = array_get($salesCenter, 'name', NULL);
        // $date = Carbon::now()->setTimezone($user->timezone);
        // $user->timezone = $user->timezone.' (GMT '.$date->format('P').')'.(((strpos($date->format('T'),'+') === false) && ((strpos($date->format('T'),'-')) === false))? ' ('.($date->format('T')).')': '').(($date->format('I') == 1)?' DST Applicable': '');
		// $user->profile_picture = Storage::disk('s3')->url($user->profile_picture);
		$user = $this->getUserDetails();
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $user
        ], $this->successStatus);
    }

    /**
     * For get timezone
     */
    public function getTimezone()
    {
        $timeZones = getTimeZoneList();
        $timeZones = array_values($timeZones);
        $timezonekeyValue = [];
        foreach($timeZones as $key => $val)
        {
            $timezonekeyValue[$key]['timezone'] = trim(substr($val,0,strpos($val,'(')));
            $timezonekeyValue[$key]['value'] = $val;
        }
        return  $this->success('success', "success",$timezonekeyValue);
    }
    
    /**
     * For update timezone
     */
    public function updateTimezone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'timezone' => 'required'
        ]);
         
        if ($validator->fails()) {  
            return response()->json([
                'status' => 'error',
                'message' => $validator->messages()->all()
            ], 400); 
        }
        $user = Auth::user();
        if ($request->has('timezone')) {
            $user->timezone = $request->timezone;
        }
        $user->save();
        // $user = Auth::user();
        // $client = Client::find(array_get($user, 'client_id'));
        // $user->client_name = array_get($client, 'name', NULL);
        // $salesCenter = Salescenter::find(array_get($user, 'salescenter_id'));
        // $user->sales_center_name = array_get($salesCenter, 'name', NULL);
        // $date = Carbon::now()->setTimezone($user->timezone);
        // $user->timezone = $user->timezone.' (GMT '.$date->format('P').')'.(((strpos($date->format('T'),'+') === false) && ((strpos($date->format('T'),'-')) === false))? ' ('.($date->format('T')).')': '').(($date->format('I') == 1)?' DST Applicable': '');
		// $user->profile_picture = Storage::disk('s3')->url($user->profile_picture);
		$user = $this->getUserDetails();
        return  $this->success('success', "Timezone is successfully updated.",$user);
    }

    /**
     * This method is used for update profile photo of auth user
     */
    public function updateProfilePhoto(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'file' => 'required' 
        ]);
         
        if ($validator->fails()) {  
            return response()->json([
                'status' => 'error',
                'message' => $validator->messages()->all()
            ], 400); 
        }
        $user = Auth::user();
        $data = '';
        // dd($request->file);
        if ($request->hasFile('file')) {
            $storageService = new StorageService;
            // Storage::disk('s3')->delete($request->old_url);
            $file = $request->file('file');
            \Log::info('File'.$file);
            $awsFolderPath = config()->get('constants.aws_folder');
            $filePath = config()->get('constants.USER_PROFILE_PICTURE_UPLOAD_PATH');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $storageService->uploadFileToStorage($file, $awsFolderPath, $filePath, $fileName);
            \Log::info($path);
            if ($path !== false) {
                $user->profile_picture = $path;
                $data = Storage::disk('s3')->url($user->profile_picture);
                $user->save();
            }
            else
            {
                return  $this->error('error', "Unable to update profile picture.",400);
            }
        }
        else
        {
            return  $this->error('error', "Please provide valid file.",400);
        }
		$user = $this->getUserDetails();
        // $user = Auth::user();
        // $client = Client::find(array_get($user, 'client_id'));
        // $user->client_name = array_get($client, 'name', NULL);
        // $salesCenter = Salescenter::find(array_get($user, 'salescenter_id'));
        // $user->sales_center_name = array_get($salesCenter, 'name', NULL);
        // $date = Carbon::now()->setTimezone($user->timezone);
        // $user->timezone = $user->timezone.' (GMT '.$date->format('P').')'.(((strpos($date->format('T'),'+') === false) && ((strpos($date->format('T'),'-')) === false))? ' ('.($date->format('T')).')': '').(($date->format('I') == 1)?' DST Applicable': '');
        // $user->profile_picture = Storage::disk('s3')->url($user->profile_picture);
        
        return  $this->success('success', "Profile photo successfully updated.",$user);
    }
    /**
     * Logout user (Revoke the token)
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $this->clockOutActivity(Auth::id());

        $request->user()->token()->revoke();        
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get user lead by status
     */
    public  function dashboard(Request $request){
        $pending_leads = (new Telesales)->getUserLeadsCount( Auth::user()->id,'pending');
        $decline_leads = (new Telesales)->getUserLeadsCount( Auth::user()->id,'decline');
        $verified_leads = (new Telesales)->getUserLeadsCount( Auth::user()->id,'verified');
        $hanged_leads = (new Telesales)->getUserLeadsCount( Auth::user()->id,'hangup');
        $canceledleads = (new Telesales)->getUserLeadsCount( Auth::user()->id,'cancel');
        $expiredleads = (new Telesales)->getUserLeadsCount( Auth::user()->id,'expired');
        if(isOnSettings(Auth::user()->client_id, 'is_enable_self_tpv_welcome_call')){
            $selfVerifiedleads = (new Telesales)->getUserLeadsCount( Auth::user()->id,'self-verified');
        }
        $data[0]['status'] = 'pending';
        $data[0]['value'] = $pending_leads;
        $data[1]['status'] = 'decline';
        $data[1]['value'] = $decline_leads;
        $data[2]['status'] = 'verified';
        $data[2]['value'] = $verified_leads;
        $data[3]['status'] = 'hangup';
        $data[3]['value'] = $hanged_leads;
        $data[4]['status'] = 'cancel';
        $data[4]['value'] = $canceledleads;
        $data[5]['status'] = 'expired';
        $data[5]['value'] = $expiredleads;
        if(isOnSettings(Auth::user()->client_id, 'is_enable_self_tpv_welcome_call')){
            $data[6]['status'] = 'self-verified';
            $data[6]['value'] = $selfVerifiedleads;
        }
       
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    //     array(
    //         array(
    //             'status' => 'pending',
    //             'value' => $pending_leads,
    //         ),array(
    //            'status' => 'decline',
    //            'value' => $decline_leads,
    //        ),
    //        array(
    //            'status' => 'verified',
    //            'value' => $verified_leads,
    //        ),
    //        array(
    //            'status' => 'hangup',
    //            'value' => $hanged_leads,
    //        ),
    //        array(
    //            'status' => 'cancel',
    //            'value' => $canceledleads,
    //        ),
    //        array(
    //            'status' => 'expired',
    //            'value' => $expiredleads,
    //        )
    //    )

    }

    /**
     * For get lead list of auth user
     */
    public  function leadlist(Request $request){    
        
        
            $userid = Auth::user()->id;
            $status = "";
            if( isset($request->leadstatus)){
                $status = $request->leadstatus;
            }
            
            $orderby ='telesales.id';
            $sort="desc";
            


          $data =   DB::table('telesales')

                ->select('telesales.id','telesales.refrence_id','telesales.client_id','telesales.status','telesales.form_id','telesales.user_id','zip_codes.city',
                    'zip_codes.state','telesales.created_at',
                    // DB::raw('DATE_FORMAT(telesales.created_at, "%m-%d-%Y %H:%i:%s") as create_time'),
                    DB::raw("(select GROUP_CONCAT(commodities.name SEPARATOR ', ') from commodities left join form_commodities on form_commodities.commodity_id = commodities.id where form_commodities.form_id = telesales.form_id) as commodity"),
                    DB::raw("(select GROUP_CONCAT(market  SEPARATOR ', ') from utilities where id IN (select utility_id from programs where id IN (select program_id from telesales_programs where telesale_id = telesales.id))) as utility"),
                    DB::raw("
                        CASE
                        WHEN telesales.status = 'verified' 
                        THEN 'Verified'
                        WHEN telesales.status = 'decline' 
                        THEN (SELECT description FROM dispositions where id = telesales.disposition_id )
                        ELSE ''
                        END  as disposition")

                )
                ->leftJoin('telesales_zipcodes', 'telesales.id', '=', 'telesales_zipcodes.telesale_id')
                ->leftJoin('zip_codes', 'zip_codes.id', '=', 'telesales_zipcodes.zipcode_id')
                ->when($status, function ($query, $status) {
                    return $query->where('status', $status);
                })
                ->where([
                        ['user_id', '=' ,$userid]
                        
                ])
                ->orderBy($orderby,$sort)
                ->paginate(10); 
                    

                $result_data = array();

                if( count($data) > 0 ) {

                    $isOnSettings = isOnSettings(auth()->user()->client_id,'is_enable_lead_view_page_d2d');
                    foreach($data as $leaddata){
                            
                            $result_data[] = array(
                            'id' => $leaddata->id , 
                            'refrence_id' => $leaddata->refrence_id ,
                            'create_time' => Carbon::parse($leaddata->created_at)->setTimezone(Auth::user()->timezone)->format('m-d-Y H:i:s') ,
                            'status' => $leaddata->status ,
                            'disposition' => $leaddata->disposition ,
                            'utility' => $leaddata->utility ,
                            'commodity' => $leaddata->commodity ,
                            'city' => $leaddata->city ,
                            'state' => $leaddata->state,
                            'is_on_lead_view_page' => $isOnSettings
                            );
                    }
                }
                
                return response()->json([
                     'status' => 'success',
                     'message' => 'success',
                     'number_of_records' => $data->count(),
                     'current_page' => $data->currentPage(),
                     'perpage' => $data->perPage(),
                     'total' => $data->total(), 
                     'lastPage' => $data->lastPage(),
                     'data' =>  $result_data
                ]);

 
	}
    
    /**
     * For fetch details of auth user
     */
	public function getUserDetails()
	{
		$user = Auth::user();
		$client = Client::find(array_get($user, 'client_id'));
		$user->client_name = array_get($client, 'name', NULL);
		$salesCenter = Salescenter::find(array_get($user, 'salescenter_id'));
		$user->sales_center_name = array_get($salesCenter, 'name', NULL);
		$date = Carbon::now()->setTimezone($user->timezone);
        $user->timezone = $user->timezone.' (GMT '.$date->format('P').')'.(((strpos($date->format('T'),'+') === false) && ((strpos($date->format('T'),'-')) === false))? ' ('.($date->format('T')).')': '').(($date->format('I') == 1)?' DST Applicable': '');
        if($user->profile_picture != '')
		    $user->profile_picture = Storage::disk('s3')->url($user->profile_picture);
		return $user;
	}


}
