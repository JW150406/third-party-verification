<?php

namespace App\Http\Controllers\Activity;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\models\TwilioConnectedDevice;
use App\Http\Controllers\Conference\ConferenceController;
use Log;
use Session;

class ActivityController extends Controller
{
    /**
     * For Update last activity time
     */
    function updateLastActivity(Request $request){
        if (Auth::check()) {
           (new User)->updateUser(
               Auth::user()->id,
               array(
                   'last_activity' => date('Y-m-d H:i:s')
               )
          );
        }
    }

    /**
     * This method is used for get & clear inactive users from session
     */
    function clearInactiveUsers(Request $request){
        Log::debug("clear Inactive Users started !!");
        $users = (new User)->getInactive();
       // dd($users);
        Log::debug("Inactive users list: " . print_r($users, true));
         foreach($users as $inactiveuser){
            Log::debug("Inactive user: " . $inactiveuser);
            (new TwilioConnectedDevice)->disconnect($inactiveuser->id);
            if( $inactiveuser->access_level == 'tpvagent'){

                (new ConferenceController)->MakeUserOffline($request,$inactiveuser->id);   
            }

            //Update user's session id and last activity to null
            $updateUser = User::where('id', $inactiveuser->id)->update(['last_activity' => "", 'session_id' => ""]);

//            (new User)->updateUser(
//                $inactiveuser->id,
//                array(
//                    'last_activity' => "",
//                    'session_id' => ""
//                )
//           );

             if ($updateUser) {
                 Log::info("User updated with id: " . $inactiveuser->id);
             } else {
                 Log::info("Unable to update user with id: " . $inactiveuser->id);
             }

         }
        Log::debug("clear Inactive Users end !!");
    }

    /**
     * This method is used for get and clear inactive salesagents
     */
    function clearInactivesalesagents(){
        $logout = false;
        if(Auth::check() && Auth::user()->access_level == 'salesagent'){
            $user =  User::find(Auth::id());
    
            if($user->status == 'inactive' || ($user->session_id == "" || $user->session_id == null) || (Auth::user()->hasAccessLevels(['salescenter','salesagent']) && isset(Auth::user()->salescenter) && Auth::user()->salescenter->status != 'active')){
                (new User)->updateUser(
                    Auth::user()->id,
                    array(
                        'last_activity' => "",
                        'session_id' => ""
                    )
                );
                Auth::logout();
                $logout = true;
            }
        }
        
         
   return array('logout' => $logout );
    }

    
    /**
     * This method is used for check for logged in inactive tpv agent and make agent offline and disconnect from twilio
     */
    function checkActive(){
        if (!Auth::check()) {
            return array('logout' => true );
        }
        $users = (new User)->getInactive(Auth::user()->id);
        $logout = false;
        if( Auth::user()->access_level == 'tpvagent'){
            if( count($users) >0 || Auth::user()->status == 'inactive'){
                (new TwilioConnectedDevice)->disconnect(Auth::user()->id);
                (new User)->updateUser(
                    Auth::user()->id,
                    array(
                        'last_activity' => "",
                        'session_id' => ""                    
                    )
               );
                Auth::logout();
                $logout = true;
            }
        }elseif( Auth::user()->access_level == 'tpv'){
            if( count($users) >0){
                (new User)->updateUser(
                    Auth::user()->id,
                    array(
                        'last_activity' => "",
                        'session_id' => ""
                    )
                );
                Auth::logout();
                $logout = true;
            }
        }
       
       return array('logout' => $logout );
    }

    /**
     * For logout salesagents
     */
    function logoutsalesagent(Request $request){
        if( isset($request->agentid)){
            (new User)->updateUser(
                $request->agentid,
                array(
                    'session_id' => "" 
                )
            );
            Auth::logout();
           return redirect()->back()
            ->with('success','Agent logged out');
        }
    }
    
    /**
     * For logout admin
     */
    function logoutAdmin(){
        $userStoredSessionId = Session::get('uniq_user_session_generated_id');
        $users = (new User)->getInactive(Auth::user()->id);
        $logout = false;
        $currentSessionId = Auth::user()->session_id;

        if( Auth::user()->access_level == 'tpv' ||
            Auth::user()->access_level == 'client' ||
            Auth::user()->access_level == 'salescenter'){
            //  user logout if user inactive or client , sales center inactive
            if( count($users) > 0 || Auth::user()->status == 'inactive' || (Auth::user()->isAccessLevelToClient() && isset(Auth::user()->client) && Auth::user()->client->status != 'active') || (Auth::user()->hasAccessLevels(['salescenter','salesagent']) && isset(Auth::user()->salescenter) && Auth::user()->salescenter->status != 'active')){
                (new User)->updateUser(
                    Auth::user()->id,
                    array(
                        'last_activity' => "",
                        'session_id' => ""
                    )
                );
                Auth::logout();
                $logout = true;
            }elseif(Auth::user()->session_id != $userStoredSessionId){
                Auth::logout();
                $logout = true;
            }
        }
        return array('logout' => $logout, 'previous_session' => $userStoredSessionId, 'current_session' =>  $currentSessionId);
    }
}
