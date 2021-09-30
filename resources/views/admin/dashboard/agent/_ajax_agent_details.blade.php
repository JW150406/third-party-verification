<ul class="tabs">
    <li class="tab-link {{ $tabId == "tab-1" ? 'current' : ''  }}" data-tab="tab-1">Available ({{ $availableWorkers->count() }})</li>
    <li class="tab-link {{ $tabId == "tab-2" ? 'current' : ''  }}" data-tab="tab-2">On Call ({{ $onCallWorkers->count() }})</li>
    <li class="tab-link {{ $tabId == "tab-3" ? 'current' : ''  }}" data-tab="tab-3">Wrap Up ({{ $wrapUpWorkers->count() }})</li>
    <li class="tab-link {{ $tabId == "tab-4" ? 'current' : ''  }}" data-tab="tab-4">Not Available ({{ $notAvailableWorkers->count() }})</li>
</ul>

<div id="tab-1" class="tab-content {{ $tabId == "tab-1" ? 'current' : ''  }}" style="background-color: white;">
    <table class="table agent-details-table" id="twilio-agent-available" style="width: 100%;">
        <thead class="">
        <tr>
            <th class="dashboard-table-color">Agent Name</th>
            <th class="dashboard-table-color">Assigned Clients</th>
            <th class="dashboard-table-color">Activity</th>
            <th class="dashboard-table-color">Last Updated Time</th>                
        </tr>
        </thead>
        <tbody class="scroller">
            @foreach($availableWorkers as $availableWorker)
                <tr style="text-align: center;">
                    <td>{{ $availableWorker->first_name .' '.$availableWorker->last_name }}</td>
                    <td>{{ $availableWorker->client_name }}</td>
                    <td>{{ $availableWorker->activity_name }}</td>
                    <?php 
                        $last_time = Carbon\Carbon::parse($availableWorker->last_updated_time);                        
                        $current_time = Carbon\Carbon::now();
                        $seconds = $current_time->diffInSeconds($last_time, true);
                    ?>
                    <td>{{ getConvertedTime($seconds) }}</td>                                
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div id="tab-2" class="tab-content {{ $tabId == "tab-2" ? 'current' : ''  }}" style="background-color: white;">
<table class="table agent-details-table" id="twilio-agent-oncall" style="width: 100%;">
        <thead class="">
        <tr>
            <th class="dashboard-table-color">Agent Name</th>
            <th class="dashboard-table-color">Assigned Clients</th>
            <th class="dashboard-table-color">Activity</th>
            <th class="dashboard-table-color">Last Updated Time</th>                
        </tr>
        </thead>
        <tbody class="scroller">
            @foreach($onCallWorkers as $onCallWorker)
                <tr style="text-align: center;">
                    <td>{{ $onCallWorker->first_name .' '.$onCallWorker->last_name }}</td>
                    <td>{{ $onCallWorker->client_name }}</td>
                    <td>{{ $onCallWorker->activity_name }}</td>   
                    <?php 
                        $last_time = Carbon\Carbon::parse($onCallWorker->last_updated_time);
                        $current_time = Carbon\Carbon::now();
                        $seconds = $current_time->diffInSeconds($last_time, true);                        
                    ?>
                    <td>{{ getConvertedTime($seconds) }}</td>                                
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div id="tab-3" class="tab-content {{ $tabId == "tab-3" ? 'current' : ''  }}" style="background-color: white;">
    <table class="table agent-details-table" id="twilio-agent-wrapup" style="width: 100%;">
        <thead class="">
        <tr>
            <th class="dashboard-table-color">Agent Name</th>
            <th class="dashboard-table-color">Assigned Clients</th>
            <th class="dashboard-table-color">Activity</th>
            <th class="dashboard-table-color">Last Updated Time</th>                
        </tr>
        </thead>
        <tbody class="scroller">
            @foreach($wrapUpWorkers as $wrapUpWorker)
                <tr style="text-align: center;">
                    <td>{{ $wrapUpWorker->first_name .' '.$wrapUpWorker->last_name }}</td>
                    <td>{{ $wrapUpWorker->client_name }}</td>
                    <td>{{ $wrapUpWorker->activity_name }}</td>    
                    <?php 
                        $last_time = Carbon\Carbon::parse($wrapUpWorker->last_updated_time);
                        $current_time = Carbon\Carbon::now();
                        $seconds = $current_time->diffInSeconds($last_time, true);                        
                    ?>
                    <td>{{ getConvertedTime($seconds) }}</td>                                
                </tr>
            @endforeach            
        </tbody>
    </table>
</div>

<div id="tab-4" class="tab-content {{ $tabId == "tab-4" ? 'current' : ''  }}" style="background-color: white;">
    <table class="table agent-details-table" id="twilio-agent-notavailable" style="width: 100%;">
        <thead class="">
        <tr>
            <th class="dashboard-table-color">Agent Name</th>
            <th class="dashboard-table-color">Assigned Clients</th>
            <th class="dashboard-table-color">Activity</th>
            <th class="dashboard-table-color">Last Updated Time</th>                
        </tr>
        </thead>
        <tbody class="scroller">
            @foreach($notAvailableWorkers as $notAvailableWorker)
                <tr style="text-align: center;">
                    <td>{{ $notAvailableWorker->first_name .' '.$notAvailableWorker->last_name }}</td>
                    <td>{{ $notAvailableWorker->client_name }}</td>
                    <td>{{ $notAvailableWorker->activity_name }}</td> 
                    <?php 
                        $last_time = Carbon\Carbon::parse($notAvailableWorker->last_updated_time);
                        $current_time = Carbon\Carbon::now();
                        $seconds = $current_time->diffInSeconds($last_time, true);                        
                    ?>
                    <td>{{ getConvertedTime($seconds) }}</td>                                
                </tr>
            @endforeach    
        </tbody>
    </table>
</div>   
