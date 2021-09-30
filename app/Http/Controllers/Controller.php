<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\models\TextEmailStatistics;
use App\models\SalescentersBrands;
use App\models\Programs;
use App\models\Zipcodes;
use Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function success($status, $message = "success", $data = "")
    {
        if (!empty($data)) {
            return response()->json([
                'status' => $status,
                'message' => $message,
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => $status,
                'message' => $message
            ], 200);
        }
    }

    public function error($status, $message = "error", $statusCode)
    {
        return response()->json([
            'status' => $status,
            'message' => $message
        ], $statusCode);
    }

    public function validateJsonResponse($request, $rules)
    {
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->error("error", implode(',', $validator->messages()->all()), 422);
        }
        return true;
    }


    public function defaultErrorView() {
        return view('errors.403');
    }

    /**
     * for send verification email to user
     * @param $user
     */
    public function sendVerificationEmail($user)
    {
        try {
            if (!empty($user)) {
                $url = $this->getVerificationUrl($user);

                $to      = $user->email;
                $subject = 'Welcome to TPV360';
                $greeting= 'Hello '.$user->first_name.',';

                if ($user->hasAccessLevels('salesagent')) {
                    $message = 'You have been added to TPV360 as a '.$user->client->name.' sales agent.<br/>';
                } else if ($user->hasAccessLevels('tpvagent')) {
                    $message = 'You have been added to TPV360 as a TPV agent.<br>';
                } else {
                    $message = 'You have been added to TPV360.<br>';
                }
                //if Sales agent's email is blank then username would be user id
                $userName = $user->email;
                if(empty($user->email)){
                   $userName = $user->userid; 
                }
                
                $message .= 'Your username is: '.$userName.'. ';
                if (!$user->hasAccessLevels('salesagent')) {
                    $message .= 'Please <a href="'.$url .'">click here</a> to generate your password. ';
                }

                if ($user->hasAccessLevels('salesagent')) {
                    $message .="<br/>Agent Id: ".$user->userid; 
                    $message .="<br/>Client Id: ".$user->client_id; 
                }
                \Mail::send('emails.common', ['greeting' => $greeting, 'msg' => $message], function($mail) use ($to, $subject) {
                    $mail->to($to);
                    $mail->subject($subject);
                });

                Log::info('verification email sent.');

                $textEmailStatistics = new TextEmailStatistics();
                $textEmailStatistics->type = 1;
                $textEmailStatistics->save();
            } else {
                Log::error('verification email failed.');
            }
                    
        } catch (\Exception $e) {
            Log::error('Error while send verification email:- '.$e);
        }
    }

    /**
     * for get verification url
     * @param $user
     * @return \Illuminate\Contracts\Routing\UrlGenerator|\Illuminate\Foundation\Application|string
     */
    public function getVerificationUrl($user)
    {
        try{
            if ($user->hasAccessLevels(['tpv','tpvagent'])) {
                $url = url('/verify', ['code'=>$user->verification_code]);
            } else if ($user->hasAccessLevels(['salesagent','salescenter'])) {
                // $url = url('/'.$user->salescenter_id.'/verify', ['code'=>$user->verification_code]);
                $url = url('/'.$user->salescenter_id.'/verify', ['code'=>$user->verification_code]) . '?t=' . base64_encode("salescenter");
            } else {
                // $url = url('/'.$user->client_id.'/verify', ['code'=>$user->verification_code]);
                $url = url('/'.$user->client_id.'/verify', ['code'=>$user->verification_code]) . '?t=' . base64_encode("client");
            }
            return $url;
        } catch (\Exception $e) {
            Log::error('Error while get verification url:- '.$e);
            return '#';
        }
    }

    /**
     * for get states has utility by commodity for client
     * @param array $commodityIds
     * @return array
     */
    public function getUtilityStates($commodityIds = [])
    {
        $states = [];
        try{
            $agentDetail = auth()->user()->salesAgentDetails;
            $clientId = auth()->user()->client_id;
            if (!empty($agentDetail)) {
                $restrictState = $agentDetail->restrict_state;
                $programIds = $this->getProgramIds();
                $brands = SalescentersBrands::where('salescenter_id',auth()->user()->salescenter_id)->pluck('brand_id');
                $zipcodes = Zipcodes::select('id','state')->whereHas('utilities', function ($query) use($clientId,$commodityIds,$brands,$programIds) {
                    $query->whereIn('brand_id',$brands);
                    $query->where('client_id', $clientId);
                    $query->whereIn('commodity_id', $commodityIds);
                    $query->whereHas('programs', function($q) use($programIds){
                        $q->where('status','active');
                        if (!empty($programIds)) {
                            $q->whereIn('id',$programIds);
                        }
                    });
                });

                // for check agent is restricted state
                if (!empty($restrictState)) {
                    $zipcodes->whereIn('state',explode(',', $restrictState));
                }
                $states = $zipcodes->groupBy('state')->orderBy('state')->get();
            }
            return $states;
        } catch (\Exception $e) {
            Log::error('Error while getting utility state:- '.$e);
            return $states;
        }
    }

    /**
     * for get program ids if any brand restricted for program id
     */
    public function getProgramIds()
    {
        $programIds = [];
        try {
            $salescenterId = auth()->user()->salescenter_id;
            $salesCenterBrands = SalescentersBrands::where('salescenter_id',$salescenterId)->has('restrictProg');
            if($salesCenterBrands->exists()) {
                // merge restricted brand programs
                foreach($salesCenterBrands->get() as $brand) {
                    $programIds = array_merge($programIds,$brand->restrictProg->pluck('program_id')->toArray());
                }
                // get not restricted brand programs
                $brandIds = SalescentersBrands::where('salescenter_id',$salescenterId)->doesntHave('restrictProg')->pluck('brand_id');
                $newProgIds = Programs::where('status','active')->whereHas('utility', function($q) use ($brandIds) {
                    $q->whereIn('brand_id', $brandIds);
                })->pluck('id')->toArray();
                $programIds = array_merge($programIds,$newProgIds);
            }
            return $programIds;            
        } catch (\Exception $e) {
            Log::error('Error while getting restricted programs ids:- '.$e);
            return $programIds;
        }
    }
}
