<?php
 
namespace App\Traits;

use Log;
use App\models\SalesAgentActivity;

trait CustomTrait {
 
    /**
     * For update pending activity, if agents do clock out before break out
	 * @param $agentId, $lat, $lng
     */
   	public function updatePendingActivity($agentId,$lat=null,$lng=null)
   	{	
   		try{
	   		$pendingActivities = SalesAgentActivity::where('agent_id',$agentId)
	   			->whereIn('activity_type',['break_in','arrival_in'])
	   			->whereDate('created_at',today())
	   			->get();
	    	foreach ($pendingActivities as $key => $pendingActivity) {
	    		$activity_type = ($pendingActivity->activity_type == 'break_in') ? 'break_out' : 'arrival_out';

	    		$pendingActivity->out_time = now();
	    		$pendingActivity->activity_type = $activity_type;
                $pendingActivity->end_lat = $lat;
                $pendingActivity->end_lng = $lng;
	    		$pendingActivity->save();
                sleep(1);
	    	}
    	} catch (\Exception $e) {
       		Log::error($e);
       	}
   	}
	
	/**
	 * For particular agent's clock out activity
	 * @param $agentId, $lat, $lng
	 */
   	public function clockOutActivity($agentId='',$lat=null,$lng=null)
   	{
   		$activity = SalesAgentActivity::where('agent_id',$agentId)
        	->where('activity_type','clock_in')
        	->whereDate('created_at',today())
        	->latest()
        	->first();
        /**
         * update last activity
         */
        if(!empty($activity)) {
        	$this->updatePendingActivity($agentId, $lat, $lng); // for update pending activity, if agents do clock out before break out
        	$activity->out_time = now();
	    	$activity->activity_type = 'clock_out';
            $activity->end_lat = $lat;
            $activity->end_lng = $lng;
	    	$activity->save();
	    }
   	}
}