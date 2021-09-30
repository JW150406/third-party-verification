<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use App\User;
use App\models\PasswordReset;
use App\Http\Requests\API\ForgotPasswordRequest;
use App\Notifications\ResetPassword;
use App\models\TextEmailStatistics;

class PasswordController extends Controller
{
	public function __construct()
	{
	}

	/**
	 * This method is used for send reset password link
	 */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
    	try {
    		$user = User::where('email', $request->get('email'))->first();

            if (!$user)
        		return $this->error("error", "We can't find a user with that e-mail address.", 404);
            
            $passwordReset = PasswordReset::firstOrCreate(['email' => $user->email],
				            [
				                'email' => $user->email,
				                'token' => str_random(60)
				            ]);

            if ($user && $passwordReset)
	            $user->notify(
	                new ResetPassword($passwordReset->token)
	            );

			$textEmailStatistics = new TextEmailStatistics();
			$textEmailStatistics->type = 1;
			$textEmailStatistics->save();
			
			Log::info("Forgot password link send to email address: " . $user->email);
    		return $this->success("success", "We have e-mailed your password reset link!");
    	} catch (\Exception $e) {
    		Log::error("Error while executing forgot passsword API: " . $e->getMessage());
    		return $this->error("error", "Something went wrong, Please try again later !!", 500);
    	}
    }
}
