<div class="btn-group pull-right btn-sales-all margin-bottom-for-filters">
    <select class="select2 btn btn-green dropdown-toggle mr15 " id="sales_agent" name="sales_agent" data-parsley-required='true'  data-parsley-errors-container="#select2-filtersales_agent-error-message" data-parsley-required-message="Please select Salesagent" >
        <option value="" selected>All Salesagents</option>
        @foreach($salesAgents as $agent)
        
        <option class="sales-agent-opt agent-salescenter-{{$agent->salescenter_id}}" salescenter="{{$agent->salescenter_id}}" value="{{$agent->id}}">{{$agent->first_name}} {{$agent->last_name}}</option>
        
        @endforeach
    </select>
    <span id="select2-filtersales_agent-error-message"></span>
</div>