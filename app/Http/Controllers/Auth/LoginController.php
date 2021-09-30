<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\User;
use Hash;
use Illuminate\Support\Facades\Log;
use Mail;
use Session;
use App\Http\Controllers\Company;
use Auth;
use App\models\TwilioConnectedDevice;
use App\Http\Controllers\Conference\ConferenceController;
use Illuminate\Foundation\Auth\ThrottlesLogins;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    public $decayMinutes = 1; // minutes to lockout
    public $maxAttempts = 3; // number of attempts before lockout


    use AuthenticatesUsers {
        logout as performLogout;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    //protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated($request, $user)
    {
        if($user->status == 'inactive') {
            $this->sessionClearAndLogout();
            return redirect()->route('login')->withErrors('Your account is deactivated/blacklisted - please contact your administrator for assistance.');
        } else if($user->isAccessLevelToClient() && isset($user->client) && $user->client->status != 'active') {
            $this->sessionClearAndLogout();
            return redirect()->route('login')->withErrors('Your client is deactivated. Please contact your administrator for assistance.');
        } else if($user->hasAccessLevels(['salescenter','salesagent']) && isset($user->salescenter) && $user->salescenter->status != 'active') {
            $this->sessionClearAndLogout();
            return redirect()->route('login')->withErrors('Your sales center is deactivated. Please contact your administrator for assistance.');
        } else if($user->isLocationRestriction() && isset($user->location) && $user->location->status != 'active') {
            $this->sessionClearAndLogout();
            return redirect()->route('login')->withErrors('Your location is deactivated. Please contact your administrator for assistance.');
        }
        (new User)->updateUser(
            $user->id,
            array(
                'last_activity' => date('Y-m-d H:i:s')
            )
        );
        if($user->access_level == 'salesagent'){
            if($user->status == 'inactive') {
              $this->sessionClearAndLogout();
              return redirect()->route('login')->withErrors('Your account is deactivated/blacklisted, Please contact your administrator.');
            }else if(empty($user->salesAgentDetails) || $user->salesAgentDetails->agent_type !='tele') {
                $this->sessionClearAndLogout();
                return redirect()->route('login')->withErrors('You are not authorized.');
            }
            return redirect()->intended('my-account');
        }else if($user->access_level=='tpvagent'){
            return redirect()->route('tpvagents.sales');
        }
        else if(Auth::user()->access_level == 'salescenter')
			{
				return redirect()->route('dashboard',['type'=>base64_encode("salescenter"),'sid'=>base64_encode(Auth::user()->salescenter_id),'cid'=>base64_encode(Auth::user()->client_id)]);
			}
        else{
            
            return redirect()->intended('/admin/dashboard');
        }

    }

    protected function credentials(Request $request)
    {

        //$params['status'] =  'active';
        $params=array();
        $value = $request->get($this->username());

        // code to enable user id in login process        
        $field = filter_var($request->get($this->username()), FILTER_VALIDATE_EMAIL)
            ? $this->username()
            : 'userid';
        $params[$field] = $value;   
        // code to enable user id in login process end

        if(isset($request->client_id) && !empty($request->client_id)){
            $params['client_id'] =  $request->client_id;
        }
        if(isset($request->salescenter_id) && !empty($request->salescenter_id)){
            $params['salescenter_id'] =  $request->salescenter_id;
        }
        // code to enable user id in login process  (altered)
        return array_merge($request->only('password'),$params );
    }

   /* Generate password and verify  */

   public function verify($id,$verification_code,Request $request)
   {
       try{
        $type = $request->has('t') ? base64_decode($request->get('t')) : "";
    Session::flash('generatepass_message', 'Please generate your password.');
    if($type == "client"){
           $company_id = $id;
           $user = 	User::where([
               ['client_id', '=', $id],
               ['verification_code', '=', $verification_code],
               ['is_block', '=', '0'],
               ['status', '=', 'active'],
             ])->first();

            if(isset($user) && !empty($user)){
                return view('auth.verify',compact('id','verification_code','user'));
            }else{
                return redirect()->route('login')->withErrors('Your account is deactivated/blacklisted - please contact your administrator for assistance. ');
            }
        }else if($type == "salescenter"){
        $user = User::where([
            ['salescenter_id', '=', $id],
            ['verification_code', '=', $verification_code],
            ['is_block', '=', '0'],
            ['status', '=', 'active'],
          ])->first();
            if(isset($user) && !empty($user)){
                return view('auth.verify',compact('id','verification_code','user'));
            }else{
                return redirect()->route('login')->withErrors('Your account is deactivated/blacklisted - please contact your administrator for assistance. ');
            }
       }else{
        return abort(404);
       }
        } catch(\Exception $e){
           // Log Message
           \Log::error("Error while verifying user login: " . $e->getMessage());
           return redirect()->route('login')->withErrors(trans('auth.DEFAULT_ERROR_MESSAGE'));
       }
   }
   public function verifytpvuser($verification_code,Request $request)
   {
       try{
           Session::flash('generatepass_message', 'Please generate your password.');
            $id = 0;
            $user = 	User::where([
                      ['verification_code', '=', $verification_code],
                      ['is_block', '=', '0'],
                      ['status', '=', 'active'],
             ])->first();
           if(isset($user) && !empty($user)){
               return view('auth.verify',compact('id','verification_code','user'));
           }else{
               return redirect()->route('login')->withErrors('Your account is deactivated/blacklisted - please contact your administrator for assisstance.');
           }
       } catch(\Exception $e){
           // Log Message
           Log::error(strtr(trans('auth.DEFAULT_ERROR_MESSAGE'), [
               '<Message>' => $e->getMessage(),
           ]));
           return redirect()->route('login')->withErrors(trans('auth.DEFAULT_ERROR_MESSAGE'));
       }
   }


    public function verification($id,$verification_code,Request $request)
    {
        $this->validate($request, [
           'password' => 'required|confirmed|min:6',
        ]);

        $user = 	User::where([
            ['verification_code', '=', $verification_code],
        ])->firstOrFail();
        $user->password = Hash::make($request->password);
        $user->verification_code = '';
        $user->save();
        return redirect()->to('/login')
        ->with('success','Password successfully saved. Please login with your credentials for further access.');
    //    if(strlen($id)==1){
    //     $client_id = $id;
    //     $user = 	User::where([
    //            ['verification_code', '=', $verification_code],
    //       ])->firstOrFail();
    //     $user->password = Hash::make($request->password);
    //     $user->verification_code = '';
    //     $user->save();
    //     return redirect()->to('/login')
    //        ->with('success','Password successfully saved. Please login with your credentials for further access.');

    //    }  else  if(strlen($id)==3){
    //     $client_id = $id;
    //     $user = 	User::where([
    //         ['client_id', '=', $id],
    //         ['verification_code', '=', $verification_code],
    //       ])->firstOrFail();
    //     $user->password = Hash::make($request->password);
    //     $user->verification_code = '';
    //     $user->save();
    //     return redirect()->to(route('user.login',$client_id))
    //        ->with('success','Password successfully saved. Please login with your credentials for further access.');

    //    }else if(strlen($id)==4){
    //     $salescenter_id = $id;
    //     $user = 	User::where([
    //         ['salescenter_id', '=', $id],
    //         ['verification_code', '=', $verification_code],
    //       ])->firstOrFail();
    //     $user->password = Hash::make($request->password);
    //     $user->verification_code = '';
    //     $user->save();
    //     return redirect()->to(route('user.login',$salescenter_id))
    //     ->with('success','Password successfully saved. Please login with your credentials for further access.');
    //    }else{
    //     return abort(404);
    //    }
    }

    public function userlogin($id)
    {
        $login_title = "";
        $client_id = "";
        $salescenter_id = "";
        if(strlen($id)==3){
            $login_title = "Client";
            $client_id = $id;
        }
        if(strlen($id)==4){
            $login_title = "Sales Center";
            $salescenter_id = $id;
        }
        return view('auth.login',compact('login_title','client_id','salescenter_id'));
    }
    public function logout(Request $request)
    {
      try {
          if (Auth::check()) {
              $previous_session = Auth::User()->session_id;

              if (Auth::User()->access_level == config('constants.TPVAGENT_ACCESS_LEVEL')) {
                (new ConferenceController)->MakeUserOffline($request, Auth::user()->id);
                (new TwilioConnectedDevice)->disconnect(Auth::user()->id);
              }
              if ($previous_session) {
                  Session::getHandler()->destroy($previous_session);
                  Auth::user()->session_id = "";
                  Auth::user()->last_activity = "";
                  Auth::user()->save();
              }

              $this->performLogout($request);
          }
        return redirect()->route('login');
      } catch(\Exception $e) {
        if(Auth::check()) {
          Auth::user()->session_id = "";
          Auth::user()->last_activity = "";
          Auth::user()->save();
        }
        $this->performLogout($request);
        return redirect()->route('login');
      }
    }

    /**
     * Send the response after the user was authenticated.
     * Remove the other sessions of this user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();
        $previous_session = Auth::User()->session_id;
        if ($previous_session) {
            (new User)->updateUser(
                Auth::user()->id,
                array(
                    'session_id' => '',
                    'last_activity' => ''
                )
            );
            Session::getHandler()->destroy($previous_session);
        }

        Auth::user()->session_id = Session::getId();
        Auth::user()->save();
        Session::put('uniq_user_session_generated_id', Auth::user()->session_id);
        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath());
    }

    protected function hasTooManyLoginAttempts(Request $request)
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),2, 30
        );
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ],['email.required' => 'Please enter your Email address or User Id']);
    }

    public function sessionClearAndLogout()
    {
          Auth::user()->session_id = "";
          Auth::user()->last_activity = "";
          Auth::user()->save();
          Auth::logout();
    }
}
