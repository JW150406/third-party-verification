<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppVersionController extends Controller
{
    /**
     * This index method is use for check app version in adroid and ios both
     * If version is not latest than send update app message 
     * If platform is not android or ios than it gives invalid platform error 
     */
    public function index(Request $request) {
        $validator = \Validator::make($request->all(), [
            'app_version' => 'required',
            'platform' => 'required|in:android,ios'
        ]);

        if ($validator->fails()) {
            return $this->error("error", implode(',',$validator->messages()->all()), 500); 
        }

        if ($request->get('platform') == "android") {
            if (config()->get('constants.ANDROID_MIN_REQUIRED_VERSION') > $request->get('app_version')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please update app'
                ], 500);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => 'success'
                ], 200);
            }
        } else if ($request->get('platform') == "ios") {
            if (config()->get('constants.IOS_MIN_REQUIRED_VERSION') > $request->get('app_version')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please update app'
                ], 500);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => 'success'
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid platform'
            ], 500);
        }

    }
}
