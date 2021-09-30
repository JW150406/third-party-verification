<div class="dashboard-bg subtab-content daseboard-update">
    @if ($identifier == "web")
        @include('admin.dashboard.salescenter-tab.sub-tabs', ['user' => $user, 'salesCenters' => $salesCenters, 'identifier' => $identifier])
    @else
        @include('admin.dashboard.salescenter-tab.mobile-sub-tabs', ['user' => $user, 'salesCenters' => $salesCenters, 'identifier' => $identifier])
    @endif

    <div class="container">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-4">
                    @include('admin.dashboard.salescenter-tab.leads_by_sclocation')
                </div>
                <div class="col-xs-12 col-sm-12 col-md-5">
                    @include('admin.dashboard.salescenter-tab.leads_status_table_by_sclocation')
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3">
                    @include('admin.dashboard.client-tab.client_logo', ['height' => 100])
                    @include('admin.dashboard.date_range')
                    @include('admin.dashboard.client-tab.brands-filter-dashboard')
                </div>
            </div>
            <!-- Modal -->
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
                    @include('admin.dashboard.client-tab.salescenter_lead_detail_pie')
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    @include('admin.dashboard.salescenter-tab.leads_by_state_map')
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    @include('admin.dashboard.salescenter-tab.locations_leads_by_commodity')
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    @include('admin.dashboard.salescenter-tab.salescenter_location_wise_channel_leads')
                </div>
                
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    @include('admin.dashboard.client-tab.status_by_state')
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    @include('admin.dashboard.salescenter-tab.leads_by_top_programs')
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    @include('admin.dashboard.salescenter-tab.leads_by_top_providers')
                </div>
            </div>
            <!-- <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                   {{-- @include('admin.dashboard.client-tab.zipcode_lead_wise_map')--}}
                </div>
            </div> -->
        </div>
            @include('admin.dashboard.export-modal')
           {{-- <div class='row'>
                @include('admin.dashboard.chart-color-form')
            </div>--}}
    </div>
</div>


