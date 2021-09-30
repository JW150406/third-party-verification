<div class="dashboard-bg subtab-content daseboard-update">
    @if ($identifier == "web")
        @include('admin.dashboard.salescenter-tab.sub-tabs', ['identifier' => $identifier])
    @else
        @include('admin.dashboard.salescenter-tab.mobile-sub-tabs', ['identifier' => $identifier])
    @endif
        <div class="container">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                    @include('admin.dashboard.client-tab.conversion_rate')
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    {{--<div class="col-md-12 col-xs-12 col-sm-12">
                        @include('admin.dashboard.client-tab.client_logo', ['height' => 99])
                    </div>
                    <div class="col-md-12 col-xs-12 col-sm-12">
                        @include('admin.dashboard.salescenter-tab.salescenter-name')
                    </div>--}}
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 dash-pad-0">
                        @include('admin.dashboard.date_range')
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 dash-pad-0">
                        @include('admin.dashboard.sales-location-tab.locations-filter')
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 dash-pad-0">
                        @include('admin.dashboard.client-tab.brands-filter-dashboard')
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    @include('admin.dashboard.salescenter-tab.locations_wise_leads')
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    @include('admin.dashboard.client-tab.leads_count_line',['height'=> 205])
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    @include('admin.dashboard.salescenter-tab.leads_by_state_map')
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                @include('admin.dashboard.client-tab.salescenter_channel_wise')
                   {{-- @include('admin.dashboard.salescenter-tab.salescenter_location_wise_channel_leads')--}}
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    @include('admin.dashboard.client-tab.salescenter_commodity_wise')
                   {{-- @include('admin.dashboard.salescenter-tab.locations_leads_by_commodity')--}}
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                @include('admin.dashboard.client-tab.status_by_state')
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    @include('admin.dashboard.client-tab.top_performer',['height' => 220])
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    @include('admin.dashboard.client-tab.bottom_performer',['height' => 220])
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                @include('admin.dashboard.client-tab.calender_pie_chart_client',['height' => 220])
                </div>
                
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                    @include('admin.dashboard.client-tab.zipcode_lead_wise_map')
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                @include('admin.dashboard.client-tab.salesagent_location_wise_map')
                </div>
                
            </div>
            <!-- modal -->
            @include('admin.dashboard.client-tab.salescenter_lead_detail_pie')
            
            @include('admin.dashboard.export-modal')
            <!-- <div class='row'> -->
            {{--@include('admin.dashboard.chart-color-form')--}}
            <!-- </div>   -->
      </div>
  </div>
</div>
