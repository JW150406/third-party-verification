<ol class="twilio-progress" data-steps="4">
    @if( $task_status !='canceled')
        <li title="{{ $task_created_time }}" class="{{ ($task_status == 'reserved' and $task_assigned_time == null) ? 'active' : ''}} {{ (in_array($task_status, ['reserved', 'wrapping','completed']) and $task_assigned_time != null) ? 'done' : 'active'}}">
            <span class="name">Waiting</span>
            <span class="step"><span>1</span></span>
        </li>
        <li title="{{ $task_assigned_time }}" class="{{ ($task_status == 'reserved' and $task_assigned_time != null) ? 'active' : ''}} {{ in_array($task_status, ['wrapping','completed']) ? 'done' : ''}}">
            <span class="name">Accepted</span>
            <span class="step"><span>2</span></span>
        </li>
        <li title="{{ $task_wrapup_start_time }}" class="{{ $task_status == 'wrapping' ? 'active' : ''}} {{ in_array($task_status, ['completed']) ? 'done' : ''}}">
            <span class="name">Wrap</span>
            <span class="step"><span>3</span></span>
        </li>
        <li title="{{ $task_completed_time }}" class="{{ $task_status == 'completed' ? 'active' : ''}}">
            <span class="name">Completed</span>
            <span class="step"><span>4</span></span>
        </li>
    @endif
    @if( $task_status =='canceled')
        <li title="{{ $task_created_time }}" class="done">
            <span class="name">Waiting</span>
            <span class="step"><span>1</span></span>
        </li>
        <li title="{{ $task_canceled_time }}" class="{{ $task_status == 'canceled' ? 'active' : ''}}">
            <span class="name">Cancel</span>
            <span class="step"><span>2</span></span>
        </li>
    @endif
</ol>