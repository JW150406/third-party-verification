<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class TwilioWorkspaceActivityStatistics extends Model
{
    protected $table = 'twilio_statistics_workspace_activity_statistics';    
    protected $fillable = ['workspaces_id','workspace_id',
    'realtime_friendly_name',
    'realtime_sid',
    'realtime_workers'];
    public function saveTwilioWorkspaceActivity($wid,$twilioDate,$workspaceStatistics,$update = false)
    {
        for($i=0;$i<count($workspaceStatistics->realtime['activity_statistics']);$i++)
        {

            $twilioWorkspaceActivityData = TwilioWorkspaceActivityStatistics::where('workspaces_id',$wid)->where('realtime_sid',$workspaceStatistics->realtime['activity_statistics'][$i]['sid'])->where('created_at','>=',$twilioDate.' 00:00:00')->get();
            
            if($twilioWorkspaceActivityData->count() > 0)
                $twilioWorkspceActivity = $twilioWorkspaceActivityData[0];
            else
                $twilioWorkspceActivity = new TwilioWorkspaceActivityStatistics();    
            
            $twilioWorkspceActivity->workspaces_id = $wid;
            $twilioWorkspceActivity->workspace_id = $workspaceStatistics->workspaceSid;
            $twilioWorkspceActivity->realtime_friendly_name = $workspaceStatistics->realtime['activity_statistics'][$i]['friendly_name'];
            $twilioWorkspceActivity->realtime_sid = $workspaceStatistics->realtime['activity_statistics'][$i]['sid'];
            $twilioWorkspceActivity->realtime_workers = $workspaceStatistics->realtime['activity_statistics'][$i]['workers'];
            $twilioWorkspceActivity->save();
            
        }
    }
}
