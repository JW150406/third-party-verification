<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\models\PasswordReset;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Redirect;
use Hash;


class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function guard()
    {
        return Auth::guard('web');
    }

    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password' => bcrypt($password),
            'remember_token' => Str::random(60),
        ])->save();

        //$this->guard()->login($user);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ], $this->validationErrorMessages());

        // To check current password and old password
        $user = User::where('email',$request->email)->first();
        if (!empty($user) && (Hash::check($request->get('password'), $user->password)))
        {
            return redirect()->back()
                ->withInput($request->only('password'))
                ->withErrors(['password' => "Can't set the current password again" ]);
        }

        // To check Token expiry
        $res = PasswordReset::where('email',$request->email)->first();
        if($res && isset($res) && !empty($res)){
            $now = Carbon::now();
            $tokenExpired = $res->created_at->addMinutes(30);
            if ($now > $tokenExpired) {
                return redirect($this->redirectPath())->withErrors('Your password reset link expired!');
            }
        }else{
            return redirect($this->redirectPath())->withErrors('Your password reset link expired!');
        }

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $this->broker()->validator(function ($credentials) {
            return mb_strlen($credentials['password']) >= 6;
        });


        $response = $this->broker()->reset(
            $this->credentials($request),
            function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        // To redirect to login page while token expired
        if($response == "passwords.token"){
            return redirect($this->redirectPath())
                ->withErrors(['email' => trans($response)]);
        }


        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($response)
            : $this->sendResetFailedResponse($request, $response);
    }

    public function sendResetResponse($response) {
        return redirect('login')
            ->with('success', trans("Password changed successfully"));
    }

}
