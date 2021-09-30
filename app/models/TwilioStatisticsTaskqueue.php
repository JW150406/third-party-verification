<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class TwilioStatisticsTaskqueue extends Model
{
    protected $table = 'twilio_statistics_taskqueue';    
    protected $fillable = ['workspace_id',
    'account_sid',
    'task_queue_sid',
    'reservations_accepted',
    'reservations_created',
    'reservations_rejected',
    'reservations_timed_out',
    'tasks_moved',
    'tasks_deleted',
    'reservations_rescinded',
    'avg_task_acceptance_time',
    'wait_duration_until_canceled_avg',
    'wait_duration_until_canceled_min',
    'wait_duration_until_canceled_max',
    'wait_duration_until_canceled_total',
    'wait_duration_until_accepted_avg',
    'wait_duration_until_accepted_min',
    'wait_duration_until_accepted_max',
    'wait_duration_until_accepted_total',
    'reservations_canceled',
    'tasks_completed',
    'tasks_entered',
    'tasks_canceled'];

    public function saveTaskQueue($twilioDate,$taskQueueStatistics,$update = false)
    {
        if($update == true)
        {
            $taskQueueData = TwilioStatisticsTaskqueue::where('workspace_id',$taskQueueStatistics->workspaceSid)->where('task_queue_sid',$taskQueueStatistics->taskQueueSid)->where('created_at','>=',$twilioDate.' 00:00:00')->first();
            $twilioTaskQueue = $taskQueueData;
        }
        else
            $twilioTaskQueue = new TwilioStatisticsTaskqueue();
        $twilioTaskQueue->workspace_id = $taskQueueStatistics->workspaceSid;
        $twilioTaskQueue->account_sid = $taskQueueStatistics->accountSid;
        $twilioTaskQueue->task_queue_sid = $taskQueueStatistics->taskQueueSid;
        $twilioTaskQueue->reservations_accepted = $taskQueueStatistics->reservationsAccepted;
        $twilioTaskQueue->reservations_created = $taskQueueStatistics->reservationsCreated;
        $twilioTaskQueue->reservations_rejected = $taskQueueStatistics->reservationsRejected;
        $twilioTaskQueue->reservations_timed_out = $taskQueueStatistics->reservationsTimedOut;
        $twilioTaskQueue->tasks_moved = $taskQueueStatistics->tasksMoved;
        $twilioTaskQueue->tasks_deleted = $taskQueueStatistics->tasksDeleted;
        $twilioTaskQueue->reservations_rescinded = $taskQueueStatistics->reservationsRescinded;
        $twilioTaskQueue->avg_task_acceptance_time = $taskQueueStatistics->avgTaskAcceptanceTime;
        $twilioTaskQueue->wait_duration_until_canceled_avg = $taskQueueStatistics->waitDurationUntilCanceled['avg'];
        $twilioTaskQueue->wait_duration_until_canceled_min = $taskQueueStatistics->waitDurationUntilCanceled['min'];
        $twilioTaskQueue->wait_duration_until_canceled_max = $taskQueueStatistics->waitDurationUntilCanceled['max'];
        $twilioTaskQueue->wait_duration_until_canceled_total = $taskQueueStatistics->waitDurationUntilCanceled['total'];
        $twilioTaskQueue->wait_duration_until_accepted_avg = $taskQueueStatistics->waitDurationUntilAccepted['avg'];
        $twilioTaskQueue->wait_duration_until_accepted_max = $taskQueueStatistics->waitDurationUntilAccepted['max'];
        $twilioTaskQueue->wait_duration_until_accepted_min = $taskQueueStatistics->waitDurationUntilAccepted['min'];
        $twilioTaskQueue->wait_duration_until_accepted_total = $taskQueueStatistics->waitDurationUntilAccepted['total'];
        $twilioTaskQueue->reservations_canceled = $taskQueueStatistics->reservationsCanceled;
        $twilioTaskQueue->tasks_completed = $taskQueueStatistics->tasksCompleted;
        $twilioTaskQueue->tasks_entered = $taskQueueStatistics->tasksEntered;
        $twilioTaskQueue->tasks_canceled = $taskQueueStatistics->tasksCanceled;
        $twilioTaskQueue->save();
    }
}
