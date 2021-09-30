<?php

namespace App\Http\Controllers\Token;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Twilio\Jwt\ClientToken;

class TokenController extends Controller
{
    /**
     * This method is used to generate token as per request data
     * @param $request
     */
    public function newToken(Request $request)
    {
      
        $forPage = $request->input('forPage');
        $applicationSid = config('services.twilio')['applicationSid'];
        $accountSid = config('services.twilio')['accountSid'];
        $authToken = config('services.twilio')['authToken'];
        $capability = new ClientToken($accountSid, $authToken);
        $capability->allowClientOutgoing($applicationSid);

        if ($forPage === route('tpvagent.support.dashboard', [], false)) {
            $capability->allowClientIncoming('support_agent');
        } else {
            $capability->allowClientIncoming('customer');
        }

        $token = $capability->generateToken();
        return response()->json(['token' => $token]);
    }

    /**
     * This method is used to generate token as per agent id
     * @param $agentId
     */
    public function token($agentId)
    {
        $applicationSid = config('services.twilio')['applicationSid'];
        $accountSid = config('services.twilio')['accountSid'];
        $authToken = config('services.twilio')['authToken'];
        $capability = new ClientToken($accountSid, $authToken);
        
        $capability->allowClientIncoming($agentId);

        $token = $capability->generateToken();
        return response()->json(['token' => $token, 'agentId' => $agentId]);
    }

}
