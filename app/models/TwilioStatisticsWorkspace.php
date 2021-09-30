<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class TwilioStatisticsWorkspace extends Model
{
    protected $table = 'twilio_statistics_workspace';    
    protected $fillable = ['workspace_id',
    'account_sid',
    'cumulative_avg_task_acceptance_time',
    'cumulative_reservations_accepted',
    'cumulative_reservations_rejected',
    'cumulative_reservations_created',
    'cumulative_reservations_timed_out',
    'cumulative_reservations_rescinded',
    'cumulative_tasks_canceled',
    'cumulative_tasks_entered',
    'cumulative_tasks_deleted',
    'cumulative_task_reserved',
    'cumulative_tasks_moved',
    'cumulative_tasks_timed_out_in_workflow',
    'longest_task_waiting_age',
    'longest_task_waiting_sid',
    'task_assigned',
    'task_pending',
    'task_reserved',
    'task_wrapping'];

    public function saveWorkspaceData($twilioDate,$workspaceStatistics,$update = false)
    {
        $twilioWorkspaceData = TwilioStatisticsWorkspace::where('workspace_id',$workspaceStatistics->workspaceSid)->where('created_at','>=',$twilioDate.' 00:00:00')->first();
        if($update == true)
        {
            $twilioWorkspace = $twilioWorkspaceData;
        }
        else
        {
            $twilioWorkspace = new TwilioStatisticsWorkspace();
        }
        $twilioWorkspace->workspace_id = $workspaceStatistics->workspaceSid;
        $twilioWorkspace->account_sid = $workspaceStatistics->accountSid;
        $twilioWorkspace->task_wrapping = $workspaceStatistics->realtime['tasks_by_status']['wrapping'];
        $twilioWorkspace->cumulative_avg_task_acceptance_time = $workspaceStatistics->cumulative['avg_task_acceptance_time'];
        $twilioWorkspace->cumulative_reservations_accepted = $workspaceStatistics->cumulative['reservations_accepted'];
        $twilioWorkspace->cumulative_reservations_rejected = $workspaceStatistics->cumulative['reservations_rejected'];
        $twilioWorkspace->cumulative_reservations_created = $workspaceStatistics->cumulative['reservations_created'];
        $twilioWorkspace->cumulative_reservations_timed_out = $workspaceStatistics->cumulative['reservations_timed_out'];
        $twilioWorkspace->cumulative_reservations_rescinded = $workspaceStatistics->cumulative['reservations_rescinded'];
        $twilioWorkspace->cumulative_tasks_canceled = $workspaceStatistics->cumulative['tasks_canceled'];
        $twilioWorkspace->cumulative_tasks_deleted = $workspaceStatistics->cumulative['tasks_deleted'];
        $twilioWorkspace->cumulative_tasks_moved = $workspaceStatistics->cumulative['tasks_moved'];
        $twilioWorkspace->cumulative_tasks_timed_out_in_workflow = $workspaceStatistics->cumulative['tasks_timed_out_in_workflow'];
        $twilioWorkspace->longest_task_waiting_age = $workspaceStatistics->realtime['longest_task_waiting_age'];
        $twilioWorkspace->longest_task_waiting_sid = $workspaceStatistics->realtime['longest_task_waiting_sid'];
        $twilioWorkspace->task_assigned = $workspaceStatistics->cumulative['tasks_assigned'];
        $twilioWorkspace->task_pending = $workspaceStatistics->realtime['tasks_by_status']['pending'];
        $twilioWorkspace->task_reserved = $workspaceStatistics->realtime['tasks_by_status']['reserved'];
        $twilioWorkspace->save();
        return $twilioWorkspace->id;
        
    }
}
