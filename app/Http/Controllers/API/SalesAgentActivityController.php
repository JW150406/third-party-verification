<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\SalesAgentActivity;
use App\models\Salesagentlocation;
use App\Traits\CustomTrait;
use Validator;
use Auth;
use Log;

class SalesAgentActivityController extends Controller
{
    use CustomTrait;
    /**
     * This method is used for store agent's activity
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
   	{
     	$validator = Validator::make($request->all(), [
				'activity_type' => 'required',
            ]);
             
     	if ($validator->fails()) {
       		return $this->error("error", implode(',',$validator->messages()->all()), 422);
       	}

       	try{ 
            $activityTypes = config('constants.SALES_AGENT_ACTIVITY_TYPE'); 

            if(!in_array($request->activity_type,$activityTypes)) {
                return $this->error("error", "Invalid activity type value.", 422);
            }   	    
     	    
            if ($request->activity_type == 'clock_in' || $request->activity_type == 'break_in' || $request->activity_type == 'arrival_in') {

                $activityCount = SalesAgentActivity::where('agent_id',Auth::id())
                    ->where('activity_type',$request->activity_type)
                    ->whereDate('created_at',today())
                    ->count();
                if ($activityCount > 0) {
                    $errorMsg = __("activity.error.messages.".$request->activity_type);
                    return $this->error("error", $errorMsg,422);
                } else {
                    $data = [
                        'agent_id' => Auth::id(),
                        'activity_type' => $request->activity_type,
                        'in_time' => now(),
                        'start_lat' => $request->lat,
                        'start_lng' => $request->lng,
                    ];
                    SalesAgentActivity::create($data);
                    $successMsg = __("activity.success.messages.".$request->activity_type);
                    return $this->success("success", $successMsg);
                }            	
            }

            if ($request->activity_type == 'clock_out') {
            	$activityType = 'clock_in';            	
            } else if($request->activity_type == 'break_out') {
            	$activityType = 'break_in';
            } else {
            	$activityType = 'arrival_in';
            }

            if ($request->activity_type == 'clock_out' || $request->activity_type == 'break_out' || $request->activity_type == 'arrival_out') {
            	$successMsg = __("activity.success.messages.".$request->activity_type);
            	$errorMsg = __("activity.error.messages.".$request->activity_type);

                $activity = SalesAgentActivity::where('agent_id',Auth::id())
                	->where('activity_type',$activityType)
                	->whereDate('created_at',today())
                	->latest()
                	->first();
                /**
                 * update last activity
                 */
                if(!empty($activity)) {
                    if($request->activity_type == 'clock_out') {
                        $this->updatePendingActivity(Auth::id(), $request->lat, $request->lng); // for update pending activity, if agents do clock out before break out
                    }
                	$activity->out_time = now();
			    	$activity->activity_type = $request->activity_type;
                    $activity->end_lat = $request->lat;
                    $activity->end_lng = $request->lng;
                    $activity->save();
                	return $this->success("success", $successMsg);

                } else {
                    return $this->error("error", $errorMsg,422);
                }
            }

       	} catch (\Exception $e) {
       		Log::error($e);
       		return $this->error("error", "Something went wrong !!!", 500);
       	}
   	}
    
    /**
     * For store agent's location
     */
   	public function saveAgentLocation(Request $request)
   	{
   		$validator = Validator::make($request->all(), [
				'lat' => 'required',
				'lng' => 'required',
            ]);
             
     	if ($validator->fails()) {
       		return $this->error("error", implode(',',$validator->messages()->all()), 422);
       	}

       	try {
       		$locationCount = Salesagentlocation::where('salesagent_id',Auth::Id())
       			->where('lat',$request->lat)
       			->where('lng',$request->lng)
       			->whereDate('created_at',today())
       			->count();

          $isBreakIn = SalesAgentActivity::where('agent_id',Auth::Id())
            ->where('activity_type','break_in')
            ->whereDate('created_at',today())
            ->count();
       		/**
             *  check location already save or not
             */
       		if($locationCount == 0 && $isBreakIn == 0) {
       			$data = [
       				'salesagent_id' => Auth::id(),
       				'lat' => $request->lat,
       				'lng' => $request->lng,
       			];
       			Salesagentlocation::create($data);
       			return $this->success("success", "Sales agent location created successfully.");
       		} else {
       			return $this->error("error", "Sales agent location already saved.",422);
       		}
       	} catch (\Exception $e) {
       		Log::error($e);
       		return $this->error("error", "Something went wrong !!!", 500);
       	}
   	}

    /**
     * This method is used for get current activity of user
     */
   	public function getCurrentActivity(Request $request)
   	{
   		try {
   			$activity = SalesAgentActivity::select(['agent_id','in_time','out_time','total_time','activity_type'])
   				->where('agent_id',Auth::id())
   				->whereDate('created_at',today())
          ->orderBy('created_at')
   				->get();
            $transit_time = $break_time = $clock_time = $total_time = $arrival_time = $current_time_in_seconds = 0;
            $current_break_time = $currentActivityTime = 0;
            $clock_in = $break_in = $arrival_in = $isSessionStart = false;
            $current_time = now();
            foreach ($activity as $key => $value) {
                if ($value->activity_type == 'clock_out') {
                    $clock_time += $value->total_time;
                    $clock_in = false;
                } else if($value->activity_type == 'break_out') {
                    $break_time += $value->total_time;
                    $break_in = false;
                    if ($isSessionStart) {
                        $current_break_time += $value->total_time;
                    }
                } else if ($value->activity_type == 'arrival_out') {
                    $arrival_time += $value->total_time;
                    $arrival_in = false;
                }

                if ($value->activity_type == 'clock_in') {
                    $current_time_in_seconds = $current_time->diffInSeconds($value->in_time);
                    $clock_time += $current_time_in_seconds;
                    $clock_in = $isSessionStart = true;
                } else if($value->activity_type == 'break_in') {
                    $break_time += $current_time->diffInSeconds($value->in_time);
                    $current_break_time += $current_time->diffInSeconds($value->in_time);
                    $break_in = true;
                } else if ($value->activity_type == 'arrival_in') {
                    $arrival_time += $current_time->diffInSeconds($value->in_time);
                    $arrival_in = true;
                }

            }

            $transit_time = $clock_time - $break_time - $arrival_time;
            
            $total_time = $clock_time;

            $inTimes = $activity->pluck('in_time');
            $outTimes = $activity->pluck('out_time');
            $allTimes = $inTimes->merge($outTimes)->toArray();
            Log::info('before sort:'.print_r($allTimes,true));
            rsort($allTimes);
            Log::info('after sort:'.print_r($allTimes,true));
    
            if ($break_in) {
                $currentStatus = 'Break';
            } elseif ($arrival_in) {
                $currentStatus = 'Working';
            } elseif ($clock_in) {
                $currentStatus = 'Transit';
            } else {
                $currentStatus = '';
            }

            if ($currentStatus != '' && !empty($allTimes)) {
                $currentActivityTime = $current_time->diffInSeconds($allTimes[0]);
            }
            $data = [
                'working_time' => gmdate('H:i:s', $arrival_time),
                'break_time' => gmdate('H:i:s', $break_time),
                'transit_time' => gmdate('H:i:s', $transit_time),
                'total_time' => gmdate('H:i:s', $total_time),
                'clock_in' => $clock_in,
                'break_in' => $break_in,
                'arrival_in' => $arrival_in,
                'current_time' => $currentActivityTime,
                'current_status' => $currentStatus,
            ];

   			if(!empty($data)) {
   				return $this->success("success","Sales agent activity retrieved !!",$data);
   			} else {
   				return $this->error("error", "Sales agent activity data not found.",422);
   			}
   		} catch (\Exception $e) {
   			Log::error($e);
       		return $this->error("error", "Something went wrong !!!", 500);
   		}
   	}
}
