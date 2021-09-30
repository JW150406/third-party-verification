<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class TwilioStatisticsWorkflow extends Model
{
    protected $table = 'twilio_statistics_workflow';    
    protected $fillable = ['workspace_id', 'workflow_id','account_sid','cumulative_avg_task_acceptance_time','cumulative_reservations_accepted','cumulative_reservations_rejected','cumulative_reservations_timed_out','cumulative_tasks_canceled','cumulative_tasks_entered','cumulative_tasks_moved','cumulative_tasks_timed_out_in_workflow','realtime_longest_task_waiting_age','realtime_longest_task_waiting_sid','realtime_task_assigned','realtime_task_pending','realtime_task_reserved','realtime_task_wrapping','realtime_total_tasks'];

    public function saveWorkflowStatistics($twilioDate,$workflow_statistics,$update = false)
    {
        if($update == true)
        {
            $workflosStatistics = TwilioStatisticsWorkflow::where('workspace_id',$workflow_statistics->workspaceSid)->where('workflow_id',$workflow_statistics->workflowSid)->where('created_at','>=',$twilioDate.' 00:00:00')->first();
        }
        else
            $workflosStatistics = new TwilioStatisticsWorkflow();
        $workflosStatistics->workspace_id = $workflow_statistics->workspaceSid;
        $workflosStatistics->workflow_id = $workflow_statistics->workflowSid;
        $workflosStatistics->account_sid = $workflow_statistics->accountSid;
        $workflosStatistics->cumulative_avg_task_acceptance_time = $workflow_statistics->cumulative['avg_task_acceptance_time'];
        $workflosStatistics->cumulative_reservations_accepted = $workflow_statistics->cumulative['reservations_accepted'];
        $workflosStatistics->cumulative_reservations_rejected = $workflow_statistics->cumulative['reservations_rejected'];
        $workflosStatistics->cumulative_reservations_completed = $workflow_statistics->cumulative['reservations_completed'];
        $workflosStatistics->cumulative_reservations_timed_out = $workflow_statistics->cumulative['reservations_timed_out'];
        $workflosStatistics->cumulative_tasks_canceled = $workflow_statistics->cumulative['tasks_canceled'];
        $workflosStatistics->cumulative_tasks_entered = $workflow_statistics->cumulative['tasks_entered'];
        $workflosStatistics->cumulative_tasks_moved = $workflow_statistics->cumulative['tasks_moved'];
        $workflosStatistics->cumulative_tasks_timed_out_in_workflow = $workflow_statistics->cumulative['tasks_timed_out_in_workflow'];
        $workflosStatistics->realtime_longest_task_waiting_age = $workflow_statistics->realtime['longest_task_waiting_age'];
        $workflosStatistics->realtime_longest_task_waiting_sid = $workflow_statistics->realtime['longest_task_waiting_sid'];
        $workflosStatistics->realtime_task_assigned = $workflow_statistics->realtime["tasks_by_status"]['assigned'];
        $workflosStatistics->realtime_task_pending = $workflow_statistics->realtime["tasks_by_status"]['pending'];
        $workflosStatistics->realtime_task_reserved = $workflow_statistics->realtime["tasks_by_status"]['reserved'];
        $workflosStatistics->realtime_task_completed = $workflow_statistics->realtime["tasks_by_status"]['completed'];
        $workflosStatistics->realtime_task_wrapping = $workflow_statistics->realtime["tasks_by_status"]['wrapping'];
        $workflosStatistics->realtime_total_tasks = $workflow_statistics->realtime['total_tasks'];
        $workflosStatistics->save();
    }
}

