@extends('layouts.admin')
@section('content')

    <!-- dashboar heading text with range selector -->

<div class="cont_bx1  dashboard-bg">
    <div class="container">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="row">
                <div class="col-xs-12 col-sm-2 col-md-2">
                    <div id="welcome">
                        <p>Dashboard</p>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-10 col-md-10">
                    <div class="pull-right">
                            <form role="form" id="deshbordForm">
                                @csrf
                                <div class="form-group dash-filter">
                                    <select class="select2 auto-submit" name="client_id">
                                        @forelse($clients as $client)
                                            <option value="{{ $client->id }}" {{ $client->id == 102 || $client->id == auth()->user()->client_id ? 'selected' : '' }}>{{ $client->name }}</option>
                                        @empty
                                            <option value="" selected>Select Client</option>
                                        @endforelse
                                    </select>
                                </div>
                                <div class="form-group dash-filter dash-from">
                                    From
                                </div> 

                                {{--<div class="form-group dash-filter">
                                                <select class="select2" id="supervisor" name="supervisor" multiple="multiple">
                                                    <option value="Sale Center 1">Sale Center 1</option>
                                                    <option value="Sale Center 2">Sale Center 2</option>
                                                    <option value="Sale Center 3">Sale Center 3</option>
                                                    <option value="Sale Center 4">Sale Center 4</option>
                                                    <option value="Sale Center 5">Sale Center 5</option>
                                                    <option value="Sale Center 6">Sale Center 6</option>
                                                </select>
                                            </div>--}}


                            <div id="custom_date" class="form-group dash-filter start-date">
                                <input id="dateclose" type="text" name="start_date" class="form-control datepicker auto-submit" placeholder="Start Date" autocomplete="off" value="{{ \Carbon\Carbon::now()->subMonth(3)->format('m/d/Y') }}" readonly>
                            </div>
                            <span class="date-dash">To</span>
                            <div id="custom_date" class="form-group dash-filter end-date">
                                <input id="dateclose2" type="text" name="end_date" class="form-control datepicker auto-submit" placeholder="End Date" autocomplete="off" value="{{ \Carbon\Carbon::now()->format('m/d/Y') }}" readonly>
                            </div>
                            <div class="charthiddenfield">
                                <input type="hidden" id="verification_status" name="verification_status" value="">
                                <input type="hidden" id="sheet_name" name="sheet_name" value="">
                                <input type="hidden" id="sheet_title" name="sheet_title" value="">
                                <input type="hidden" id="agent_id" name="agent_id" value="">
                                <input type="hidden" id="sales_center_id" name="sales_center_id" value="">
                                <input type="hidden" id="channel_type" name="channel_type" value="">
                                <input type="hidden" id="commodity_type" name="commodity_type" value="">
                                <input type="hidden" id="verification_method" name="verification_method" value="">
                                <input type="hidden" id="sales_type" name="sales_type" value="">
                                <input type="hidden" id="agent_type" name="agent_type" value="">
                            </div>
                        </form>
                    </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- dashboard records section 1 -->
    <div class="cont_bx2 dashboard-bg">
        <div class="container">
            <div class="col-xs-12 col-sm-12 col-md-12" id="dashboard-warp">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="dashboard-box">
                            <h4 class="dash-hd-title">Overview</h4>
                            <div class="sales_tablebx mt30 dash-tb-1">
                                <!-- <div class="table-responsive">
                                    <table class="table" id="client-table">
                                        <thead>
                                            <tr class="list-users">
                                                <th>Status</th>
                                                <th>Today</th>
                                                <th>WTD</th>
                                                <th>MTD</th>
                                                <th>YTD</th>
                                            </tr>
                                        </thead>
                                        <tbody id="telesales-table-report">
                                        </tbody>
                                    </table>
                                </div> -->
                                <!---------------->

                                <div class="table-responsive">
                                    <table class="table table-bordered overview-table" id="client-table">
                                        <thead>
                                        <tr class="list-users">
                                            <th colspan="1">Status</th>
                                            <th colspan="2">Today</th>
                                            <th colspan="2">WTD</th>
                                            <th colspan="2">MTD</th>
                                            <th colspan="2">YTD</th>
                                        </tr>
                                        <thead class="inner-tr">
                                        <tr class="sub-tr-th">
                                            <th></th>

                                            <th>#</th>
                                            <th>%</th>

                                            <th>#</th>
                                            <th>%</th>

                                            <th>#</th>
                                            <th>%</th>

                                            <th>#</th>
                                            <th>%</th>
                                        </tr>
                                        <tbody id="telesales-table-report">

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="dashboard-box">
                            <h4 class="dash-hd-title">Top Bad Sales Dispositions</h4>

                            <div class="sales_tablebx dash-ld-table scrollbar-inner scroll-content scroll-scrolly_visible dash-tb-1">
                                <div class="table-responsive">
                                    <!-- <table class="table">
                                        <thead>
                                            <tr class="list-users">
                                                <th>Vendor</th>
                                                <th>Disposition</th>
                                                <th>%</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lead-decline-report">
                                        </tbody>
                                    </table> -->

                                    <table class="table table-bordered bad-sales-table">
                                        <thead>
                                        <tr class="list-users">
                                            <th>Vendor</th>
                                            <th>Disposition</th>
                                            <th>%</th>
                                        </tr>
                                    </thead>
                                    <tbody id="lead-decline-report">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end--row-->
            <div class="row">
                <div class="col-xs-12 col-sm-4 col-md-4">
                    <div class="dashboard-box">
                        <h4 class="dash-hd-title">Overall Verification Status</h4>
                        <div id="telesaleschartnew" style="width: 100%; height: 250px"></div>

                    </div>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-4">
                    <div class="dashboard-box">
                        <h4 class="dash-hd-title">Verification Status by Sales Centers</h4>
                        <div id="vendorsleadschart" style="width: 100%; height: 250px"></div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-4">
                    <div class="dashboard-box">
                        <h4 class="dash-hd-title">Verified Leads by Sales Centers</h4>
                        <div id="verifiedVendorsPieCharts" style="width: 100%; height: 250px"></div>
                    </div>
                </div>
            </div>
            <!--end--row-->

                <div class="row">

                    <!-- <h4 class="dash-hd-title" style="margin-bottom : 30px; color: #000;">Vendors Leads As Per Verification Status</h4> -->
                    <div id="vendorsPieCharts"></div>

                    <div class="col-xs-12 col-sm-4 col-md-4">

                    </div>
                </div>
                <!--end--row-->

                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="dashboard-box">
                            <h4 class="dash-hd-title">Verification Status by Tele Sales</h4>
                            <div id="teleChannelsLeadsChart" style="width: 100%; height: 250px;"></div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="dashboard-box">
                            <h4 class="dash-hd-title">Verification Status by D2D sales</h4>
                            <div id="d2dChannelsLeadsChart" style="width: 100%; height: 250px;"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-sm-4 col-md-4">
                        <div class="dashboard-box">
                            <h4 class="dash-hd-title">Verification Status by Electric Commodity</h4>
                            <div id="electricCommodityLeadsChart" style="width: 100%; height: 250px;"></div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-4">
                        <div class="dashboard-box">
                            <h4 class="dash-hd-title">Verification Status by Gas Commodity</h4>
                            <div id="gasCommodityLeadsChart" style="width: 100%; height: 250px;"></div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-4">
                        <div class="dashboard-box">
                            <h4 class="dash-hd-title">Verification Status by Both Commodity</h4>
                            <div id="bothCommodityLeadsChart" style="width: 100%; height: 250px;"></div>
                        </div>
                    </div>
                </div>
                <!--end--row-->

            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <h4 class="dash-hd-title">D2D</h4>
                    <div class="row">
                        <div class="col-sm-6 col-md-6">
                            <div class="dashboard-box">
                                <h4 class="dash-hd-title">Good Sales</h4>
                                <div id="d2dgoodsaleschart" style="width: 100%; height:25vh;"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <div class="dashboard-box">
                                <h4 class="dash-hd-title">Bad Sales</h4>
                                <div id="d2dbadsaleschart" style="width: 100%; height: 25vh;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <h4 class="dash-hd-title">Tele</h4>
                    <div class="row">
                        <div class="col-sm-6 col-md-6">
                            <div class="dashboard-box">
                                <h4 class="dash-hd-title">Good Sales</h4>
                                <div id="telegoodsaleschart" style="width: 100%; height:25vh;"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <div class="dashboard-box">
                                <h4 class="dash-hd-title">Bad Sales</h4>
                                <div id="telebadsaleschart" style="width: 100%; height: 25vh;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="dashboard-box " style="height:250px">
                        <div class="map_alt_text1 text-center" style ="padding:25% 0;"><h4> Loading... </h4></div>
                          <div class="map1"></div>
                    </div>


                </div>
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="dashboard-box" style = "height:250px;">
                    <div class="map_alt_text2 text-center" style ="padding:25% 0;"><h4> Loading...</h4></div>
                    <div class="map2"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                    <div class="dashboard-box" style="height:27vh">
                        <h4 class="dash-hd-title">Show Details of Text messages , Emails and TPV Time</h4>
                        <div class="sales_tablebx mt30">
                            <div class="table-responsive">
                                <table class="table" id="client-table">
                                    <thead>
                                        <tr class="list-users">
                                            <th>Status</th>
                                            <th>Today</th>
                                            <th>WTD</th>
                                            <th>MTD</th>
                                            <th>YTD</th>
                                        </tr>
                                        </thead>
                                        <tbody id="total-email-text-call-report">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- <div class="modal fade" id="verification-status-report-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Verification Status Report</h4>
                                </div>
                                <div class="ajax-error-message"></div>
                                <div class="modal-body">
                                    <div class="modal-form row">
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                            <div id="verificationstatuspopupchart" style="width: 400px; height: 250px;"></div>
                                            <div class="sales_tablebx mt30">
                                                <div class="table-responsive">
                                                    <table class="table" id="client-table">
                                                        <thead>
                                                            <tr class="list-users">
                                                                <th>Status</th>
                                                                <th>Count</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="verificationstatusreporttable"></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <div class="modal fade status-lead-modal" id="telesales-status-leads-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document" style="width: 82%">
                            <div class="modal-content font-12">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                    <a href="javascript:void(0)" id="exportLeads" class="btn btn-green pull-right" data-type="new" type="button">Export</a>
                                    <h4 class="modal-title">Leads Report</h4>
                                </div>
                                <div class="ajax-error-message"></div>
                                <div class="modal-body dash-m-scroll ">
                                    <div class="scrollbar-inner">
                                        <div class="modal-form row">
                                            <div class="col-xs-12 col-sm-12 col-md-12">
                                                <div class="sales_tablebx dash-lead-report ft1">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped" id="telesales-status-leads">
                                                            <thead>
                                                            <tr class="list-users">
                                                                <th>Sr.No.</th>
                                                                <th>Status</th>
                                                                <th>Lead#</th>
                                                                <th>Date</th>
                                                                <th>Channel</th>
                                                                <th>Sales Agent</th>
                                                                <th>Sales Center</th>
                                                                <th>Commodity</th>
                                                                <th>Zipcode</th>
                                                                <th>TPV Agent</th>
                                                                <th>Verification Method</th>
                                                                <!-- <th style="display: none;">Verification Method</th> -->
                                                            </tr>
                                                        </thead>
                                                        <tbody id="telesalesstatusleadsreporttable"></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12" id="empty-warp">
            <div class="text-center">
                <div id = "legend_container">
                    <div id="map_legend">
                    </div>
                </div>
                <h3 class="text-danger">Error to getting the dashboard report data.</h3>
            </div>
        </div>
    </div>
</div>

<!-- end dashboard records section 1 -->

<!--script-for--select field set and display text-area---->
<script src="{{ asset('js/canvasjs.min.js') }}"></script>
<script src="{{ asset('js/echarts/echarts.min.js') }}"></script>
<!-- <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC49bXfihl4zZqjG2-iRLUmcWO_PVcDehM&sensor=true&sensor=true&v=3"></script> -->


<script>
    $(function() {
        $('#fieldselector').change(function() {
            $('.cust-hide').hide();
            $('#' + $(this).val()).show();
        });
    });
    $(function() {
        $(".auto-submit").change(function() {
            var data = $("#deshbordForm").serializeArray();
            // console.log(data);
            dashboard(data);
            loadVerificationStatusData(data);
            loadVendorsLeadsData(data);
            loadVendorsLeadsPieChartsData(data);
            loadChannelsLeadsData(data);
            loadCommodityLeadsData(data);
            leadDeclinedReport(data);
            loadsd2dGoodSalesDataPieChart(data);
            loadsd2dBadSalesDataPieChart(data);
            loadsteleGoodSalesDataPieChart(data);
            loadsteleBadSalesDataPieChart(data);
            loadMapBasedOnZipcode(data);
            loadMapBasedOnSalesAgent(data);
            $('#map_zipcode').html("");
            $('#map_salesagent').html("");
            $('.map_alt_text1').css('display','block');
            $('.map_alt_text2').css('display','block');

            var legend = document.createElement('div');
                var div = document.createElement('div');

                div.innerHTML = '<p class = "legend_text"><img src="{{asset("images/marker_image/pins/pin-pending.png")}}" height = "10" width = "10">  Pending</p>';
                div.innerHTML += '<p class = "legend_text"><img src="{{asset("images/marker_image/pins/pin-disconnected.png")}}" height = "10" width = "10">  Disconnected</p>';
                div.innerHTML += '<p class = "legend_text"><img src="{{asset("images/marker_image/pins/pin-cancelled.png")}}" height = "10" width = "10">  Cancelled</p>';
                div.innerHTML += '<p class = "legend_text"><img src="{{asset("images/marker_image/pins/pin-verified.png")}}" height = "10" width = "10">  Verified</p>';
                div.innerHTML += '<p class = "legend_text"><img src="{{asset("images/marker_image/pins/pin-declined.png")}}" height = "10" width = "10">  Declined</p>';
                legend.appendChild(div);
                $('#legend_container').html(legend);
                $('#legend_container').children().attr('id','map_legend');


        });

    });
    window.onload = function() {
        var data = $("#deshbordForm").serializeArray();
        dashboard(data);
        loadVerificationStatusData(data);
        loadVendorsLeadsData(data);
        loadVendorsLeadsPieChartsData(data);
        loadChannelsLeadsData(data);
        loadCommodityLeadsData(data);
        leadDeclinedReport(data);
        loadsd2dGoodSalesDataPieChart(data);
        loadsd2dBadSalesDataPieChart(data);
        loadsteleGoodSalesDataPieChart(data);
        loadsteleBadSalesDataPieChart(data);
        loadMapBasedOnZipcode(data);
        loadMapBasedOnSalesAgent(data);
        loadTextEmailStatusReport(data);
        
    };
    window.setInterval(function(){
        var data = $("#deshbordForm").serializeArray();
        loadMapBasedOnSalesAgent(data);
    },600000);

    $(window).resize(function() {
        $('div[_echarts_instance_]').each(function(){
            var id = $(this).attr('_echarts_instance_');
            window.echarts.getInstanceById(id).resize();
        });
    });


    function loadMapBasedOnZipcode(data)
    {
        $.ajax({
            url: "{{route('admin.dashboard.loadMap1')}}",
            type: "GET",
            data: data,
            success: function(res)
            {
                $('.map_alt_text1').css('display','none');
                $('.map1').html(res.html);

                var legend = document.createElement('div');
                var div = document.createElement('div');

                div.innerHTML = '<p class = "legend_text"><img src="{{asset("images/marker_image/pins/pin-pending.png")}}" height = "10" width = "10">  Pending</p>';
                div.innerHTML += '<p class = "legend_text"><img src="{{asset("images/marker_image/pins/pin-disconnected.png")}}" height = "10" width = "10">  Disconnected</p>';
                div.innerHTML += '<p class = "legend_text"><img src="{{asset("images/marker_image/pins/pin-cancelled.png")}}" height = "10" width = "10">  Cancelled</p>';
                div.innerHTML += '<p class = "legend_text"><img src="{{asset("images/marker_image/pins/pin-verified.png")}}" height = "10" width = "10">  Verified</p>';
                div.innerHTML += '<p class = "legend_text"><img src="{{asset("images/marker_image/pins/pin-declined.png")}}" height = "10" width = "10">  Declined</p>';
                legend.appendChild(div);

                $('#legend_container').html(legend);
                $('#legend_container').children().attr('id','map_legend');
                var scriptData = res.js.split('<script type="text/javascript">');
                var newdata =  scriptData[1].trim();
                var appendData = '<script type="text/javascript" id="google_script_zipcode">'+newdata;

                var replaceData = String('function updateBounds() {var bounds = new google.maps.LatLngBounds();  bounds.extend(new google.maps.LatLng(49.38, -66.94));bounds.extend(new google.maps.LatLng(25.82, -124.39));map.fitBounds(bounds);\n map.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(document.getElementById("map_legend"));}//]]>');
                var appendData = appendData.replace('//]]>',replaceData);
                // console.log(appendData);
                $('#google_script_zipcode').remove();

                $('body').append(appendData);
                initialize_map();
                updateBounds();

                // google.maps.event.addListener(markerCluster, "clusterclick", function(cluster) { var clickedMakrers = cluster.getMarkers();var content = "";for(i = 0 ;i<clickedMakrers.length;i++){content += clickedMakrers[i]["title"] +"<br/>";}iw_map = new google.maps.InfoWindow();iw_map.setPosition(cluster.getCenter());iw_map.setContent(content);iw_map.open(map,this);});google.maps.event.addListener(map, "click", function() {iw_map.close();});
            }
        });
    }

    function loadMapBasedOnSalesAgent(data)
    {
        $.ajax({
            url: "{{route('admin.dashboard.loadMap2')}}",
            type: "GET",
            data: data,
            success: function(res)
            {
                $('.map_alt_text2').css('display','none');
                $('.map2').html(res.html);

                var scriptData = res.js.split('<script type="text/javascript">');
                var newdata =  scriptData[1].trim();
                var appendData = '<script type="text/javascript" id="google_script">'+newdata;
                var replaceData = String('function updateBounds() {var bounds = new google.maps.LatLngBounds();  bounds.extend(new google.maps.LatLng(49.38, -66.94));bounds.extend(new google.maps.LatLng(25.82, -124.39));map.fitBounds(bounds);}\n//]]>');
                var appendData = appendData.replace('//]]>',replaceData);
                $('#google_script').remove();
                $('body').append(appendData);
                initialize_map();
                updateBounds();

            }
        });
    }

        //text email status count table
        function loadTextEmailStatusReport(data)
        {
            $.ajax({
                url: "{{route('admin.dashboard.textEmailCount')}}",
                type: "GET",
                data: data,
                success: function(res)
                {
                    var tableData = res.data.email;
                    textEmailData = '';
                    $.each(tableData,function(k,v){

                        textEmailData += "<tr><td>"+k+"</td>";
                        textEmailData += "<td>"+ v.today +"</td>";
                        textEmailData += "<td>"+ v.WTD +"</td>";
                        textEmailData += "<td>"+ v.MTD +"</td>";
                        textEmailData += "<td>"+ v.YTD +"</td></tr>";
                    });
                    $('#total-email-text-call-report').html(textEmailData);

                }
            });
        }

    //Lead declined report ajax
    function leadDeclinedReport(data) {
        $.ajax({
            url: "{{route('admin.dashboard.leadDeclinedData')}}",
            type: "POST",
            data: data,
            success: function(res) {
                if (res.status == true) {
                    $('#lead-decline-report').html(res.data);
                }
            }
        });
    }

    function dashboard(data) {

        $.ajax({
            url: "{{ route('admin.dashboard.data') }}",
            type: "POST",
            data: data,
            success: function(res) {
                if (res.status === true) {

                    $('#empty-warp')[0].style.display = 'none';
                    $('#legend_container')[0].style.display = 'block';
                    $('#dashboard-warp')[0].style.display = 'block';

                    var teleSalesHtml = '';

                    var getTeleSalesStatusData = res.data.teleSalesStatusReport;
                    $.each(getTeleSalesStatusData, function(key, val) {
                        var splitDataToday = val.Today.split(',');
                        var splitDataWtd = val.WTD.split(',');
                        var splitDataMtd = val.MTD.split(',');
                        var splitDataYtd = val.YTD.split(',');
                        teleSalesHtml += '<tr>' +
                            '<td>' + val.status + '</td>' +
                            '<td>' + splitDataToday[0] + '</td>' +
                            '<td class="td-clr">' + splitDataToday[1] + '</td>' +
                            '<td>' + splitDataWtd[0] + '</td>' +
                            '<td class="td-clr">' + splitDataWtd[1] + '</td>' +
                            '<td>' + splitDataMtd[0] + '</td>' +
                            '<td class="td-clr">' + splitDataMtd[1] + '</td>' +
                            '<td>' + splitDataYtd[0] + '</td>' +
                            '<td class="td-clr">' + splitDataYtd[1] + '</td>' +
                            '</tr>';
                    });

                    $('#telesales-table-report').html(teleSalesHtml);

                } else {
                    $('#empty-warp')[0].style.display = 'block';
                    $('#legend_container').style.display = 'none';
                    $('#dashboard-warp')[0].style.display = 'none';
                }
            }
        })
    }

        function loadVerificationStatusData(data) {
            $.ajax({
                url: "{{ route('admin.dashboard.verificationstatuschart') }}",
                type: "POST",
                data: data,
                success: function(res) {
                    if (jQuery.isEmptyObject(res.data.reportData)) {
                        return false;
                    } else {
                        var getVerficationStatusData = jQuery.parseJSON(res.data.reportData)
                        loadVerificationStatusChart('telesaleschartnew', res.data.statusList, getVerficationStatusData);
                    }
                }
            });
        }

    function loadVendorsLeadsData(data) {
        $.ajax({
            url: "{{ route('admin.dashboard.vendorsleadsbarchart') }}",
            type: "POST",
            data: data,
            success: function(res) {
                if (jQuery.isEmptyObject(res)) {
                    document.getElementById("vendorsleadschart").innerHTML = "";
                    $("#vendorsleadschart").removeAttr("_echarts_instance_");
                    return false;
                    
                }
                else
                {
                    loadVendorsLeadsChart('vendorsleadschart', res);
                }
            }
        });
    }

        function loadsd2dGoodSalesDataPieChart(data) {
            $.ajax({
                url: "{{ route('admin.dashboard.d2dgoodsalespiechart') }}",
                type: "GET",
                data: data,
                success: function(res) {

                    if (jQuery.isEmptyObject(res)) {
                        return false;
                    } else {
                        loadsd2dGoodSalesPieChart('d2dgoodsaleschart', res);
                    }
                }
            });
        }

        function loadsd2dBadSalesDataPieChart(data) {
            $.ajax({
                url: "{{ route('admin.dashboard.d2dbadsalespiechart') }}",
                type: "GET",
                data: data,
                success: function(res) {
                    if (res.length == 0) {
                        return false;
                    } else {
                        loadsd2dBadSalesPieChart('d2dbadsaleschart', res);

                    }
                }


            });
        }

        function loadsteleGoodSalesDataPieChart(data) {

            $.ajax({
                url: "{{ route('admin.dashboard.telegoodsalespiechart') }}",
                type: "GET",
                data: data,
                success: function(res) {
                    if (jQuery.isEmptyObject(res)) {
                        return false;
                    } else {
                        loadsteleGoodSalesPieChart('telegoodsaleschart', res);

                    }
                }


            });
        }

        function loadsteleBadSalesDataPieChart(data) {
            $.ajax({
                url: "{{ route('admin.dashboard.telebadsalespiechart') }}",
                type: "GET",
                data: data,
                success: function(res) {
                    if (res.length == 0) {
                        return false;
                    } else {
                        loadsteleBadSalesPieChart('telebadsaleschart', res);
                    }
                }


            });
        }

    function loadVendorsLeadsPieChartsData(data) {
        $.ajax({
            url: "{{ route('admin.dashboard.vendorsleadspiechart') }}",
            type: "GET",
            data: data,
            success: function(res) {
                if (jQuery.isEmptyObject(res)) {
                    return false;
                } else {
                    $('#vendorsPieCharts').html('');
                    var statuschartHtml = '';
                    $.each(res, function(key, val) {
                        var getVerficationStatusData = jQuery.parseJSON(val);
                        if (key == 'Verified') {
                            loadVendorsLeadsPieChart('verifiedVendorsPieCharts', key, getVerficationStatusData);
                        } else {
                            if(key == "Disconnected")
                            {
                                title = "Disconnected Calls by Sales Centers";
                            }
                            else
                            title = key + " Leads by Sales Centers";
                            statuschartHtml = '<div class="col-xs-12 col-sm-3 col-md-3">' +
                                '<div class="dashboard-box">' +
                                '<h4 class="dash-hd-title" > ' + title + '</h4>' +
                                '<div id="' + key + 'vendorschart" style="width: 100%; height: 25vh;"></div>' +
                                '</div>' +
                                '</div>';
                            $('#vendorsPieCharts').append(statuschartHtml);
                            loadVendorsLeadsPieChart(key + 'vendorschart', key, getVerficationStatusData);
                        }
                    });
                }
            }
        })
    }

    function loadChannelsLeadsData(data) {
        $.ajax({
            url: "{{ route('admin.dashboard.channelsleadsbarchart') }}",
            type: "GET",
            data: data,
            success: function(res) {
                if (jQuery.isEmptyObject(res.leads)) {
                    document.getElementById("teleChannelsLeadsChart").innerHTML = "";
                    $("#teleChannelsLeadsChart").removeAttr("_echarts_instance_");
                    document.getElementById("d2dChannelsLeadsChart").innerHTML = "";
                    $("#d2dChannelsLeadsChart").removeAttr("_echarts_instance_");
                    return false;
                } else {
                    loadChannelsLeadsChart('teleChannelsLeadsChart', res.vendorList.tele, res.leads.tele, res.status, 'tele');
                    loadChannelsLeadsChart('d2dChannelsLeadsChart', res.vendorList.d2d, res.leads.d2d, res.status, 'd2d');
                }
            }
        })
    }

    function loadCommodityLeadsData(data) {
        $.ajax({
            url: "{{ route('admin.dashboard.commodityleadsbarchart') }}",
            type: "GET",
            data: data,
            success: function(res) {
                if (jQuery.isEmptyObject(res.leads)) {
                    document.getElementById("electricCommodityLeadsChart").innerHTML = "";
                    $("#electricCommodityLeadsChart").removeAttr("_echarts_instance_");
                    document.getElementById("gasCommodityLeadsChart").innerHTML = "";
                    $("#gasCommodityLeadsChart").removeAttr("_echarts_instance_");
                    document.getElementById("bothCommodityLeadsChart").innerHTML = "";
                    $("#bothCommodityLeadsChart").removeAttr("_echarts_instance_");
                    return false;
                } else {
                    loadCommodityLeadsChart('electricCommodityLeadsChart', res.vendorList.electric, res.leads.electric, res.status, 'electric');
                    loadCommodityLeadsChart('gasCommodityLeadsChart', res.vendorList.gas, res.leads.gas, res.status, 'gas');
                    loadCommodityLeadsChart('bothCommodityLeadsChart', res.vendorList.gas, res.leads.both, res.status, 'both');
                }
            }
        })
    }

    function loadVerificationStatusChart(chartId, statusList, statusData) {
        var chart = echarts.init(document.getElementById(chartId));

        var radius = [40, 55];
        chart.setOption({
            tooltip: {
                trigger: 'item',
                formatter: function(x) {
                    return x.seriesName+ "<br/>"+x.name+": " + x.value +' (' + parseFloat(x.percent).toFixed(2)+ '%)';
                }
                // formatter: '{a} <br/>{b}: {c} ({d}%)'
            },
            legend: {
                orient: 'horizontal',
                left: 10,
                data: statusList

            },
            toolbox: {
                show: true,
                feature: {
                    mark: {
                        show: false
                    },
                    saveAsImage: {
                        show: true,
                        title: "Save"
                    },
                    // myTool1: {
                    //     show: true,
                    //     title: 'custom extension method 1',
                    //     icon: 'path://M432.45,595.444c0,2.177-4.661,6.82-11.305,6.82c-6.475,0-11.306-4.567-11.306-6.82s4.852-6.812,11.306-6.812C427.841,588.632,432.452,593.191,432.45,595.444L432.45,595.444z M421.155,589.876c-3.009,0-5.448,2.495-5.448,5.572s2.439,5.572,5.448,5.572c3.01,0,5.449-2.495,5.449-5.572C426.604,592.371,424.165,589.876,421.155,589.876L421.155,589.876z M421.146,591.891c-1.916,0-3.47,1.589-3.47,3.549c0,1.959,1.554,3.548,3.47,3.548s3.469-1.589,3.469-3.548C424.614,593.479,423.062,591.891,421.146,591.891L421.146,591.891zM421.146,591.891',
                    //     onclick: function() {
                    //         alert('myToolHandler1')
                    //     }
                    // },
                    // myViewReportsDataFilter: {
                    //     show: true,
                    //     title: 'View Data',
                    //     icon: 'image://{{ asset("images/view.png")}}',
                    //     onclick: function() {
                    //         viewVerificattionDataPopup();
                    //     }
                    // },
                    // myExportReportsDataFilter: {
                    //     show: true,
                    //     title: 'Export',
                    //     icon: 'image://{{ asset("images/save.png")}}',
                    //     onclick: function() {
                    //         exportVerificattionData();
                    //     }
                    // }
                }
            },
            calculable: true,
            series: [{
                name: 'Status',
                type: 'pie',
                radius: ['45%', '75%'],
                avoidLabelOverlap: true,
                rotate:true,
                rotate: 45,
                top:50,
                color: ['#cd8423', '#679dae', '#344351', '#c0a297', '#be362e'],
                label: {
                    show: true,
                    position: "outside",
                    rotate: true,
                    fontSize: 10,
                    // formatter: '{b|{b}} ',
                    rotate: 0,
                    // backgroundColor: '#eee',
                    // borderColor: '#aaa',
                    // borderWidth: 1,
                    // borderRadius: 4,
                    // padding: [0, 7],
                    // rich: {
                    //     b: {
                    //         fontSize: 16,
                    //         lineHeight: 33
                    //     }
                    // }
                },
                data: statusData
            }]

        });
        // console.log(chart);
        chart.on('click', function(params) {

            $('#telesales-status-leads-modal .modal-title').html(params.name + ' Leads Report');
            $('.charthiddenfield #verification_status').val(params.name);
            $('.charthiddenfield #sheet_name').val(params.name + " Leads Report");
            $('.charthiddenfield #sheet_title').val(params.name + " Leads Report");

            // getTelesalesLeadsByStatus(22,params.name, '', '', '', '', '', '');
        });

    }

    function getTelesalesLeadsByStatus(brand,seriesName, seriesValue, salesCenterId, channel_type, commodity_type, verificaiton_method, sales_type) {
        
        $('#telesales-status-leads-modal').modal();
        var filterData = $("#deshbordForm").serializeArray();

        var leadTable = $('#telesales-status-leads').DataTable({
            dom: 'tr<"bottom"lip>',
            //dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            bDestroy: true,
            searchDelay: 1000,
            lengthChange: true,
            ajax: {
                url: "{{ route('admin.dashboard.telesalesleadslist') }}",
                data: function(data) {
                    data.client_id = filterData[1].value;
                    data.start_date = filterData[2].value;
                    data.end_date = filterData[3].value;
                    data.status = seriesName;
                    data.agent_id = seriesValue;
                    data.sales_center_id = salesCenterId;
                    data.channeltype = channel_type;
                    data.commoditytype = commodity_type;
                    data.verificaiton_method = verificaiton_method;
                    data.sales_type = sales_type;
                    data.brand = brand;
                }
            },
            aaSorting: [
                [10 , 'desc']
            ],
            columns: [{
                    data: null
                },
                {
                    data: 'statusname',
                    name: 'telesales.statusname',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'refrence_id',
                    name: 'telesales.refrence_id',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'created_at',
                    name: 'telesales.created_at',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'channel',
                    name: 'user.salesAgentDetails.agent_type',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'sales_agent',
                    name: 'user.first_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'salescenter_name',
                    name: 'user.salescenter.name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'commodity_name',
                    name: 'telesales.commodity_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'zipcode',
                    name: 'telesales.service_zipcode',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'tpv_agent',
                    name: 'tpv_agent',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'verification_method',
                    name: '',
                    orderable: false,
                    searchable: false
                },
                // {
                //     data: 'id',
                //     name: 'telesales.id',
                //     searchable: false,
                //     visible: false
                // },
            ],
            //buttons: ['csv'],
            columnDefs: [{
                    "searchable": false,
                    "orderable": false,
                    "width": "5%",
                    "targets": 0
                }
                // {
                //     "visible": false,
                //     "targets": 11
                // }
            ],
            'fnDrawCallback': function() {
                var table = $('#telesales-status-leads').DataTable();
                var info = table.page.info();
                if (info.pages > 1) {
                    $('#telesales-status-leads_info')[0].style.display = 'block';
                    $('#telesales-status-leads_paginate')[0].style.display = 'block';
                } else {
                    $('#telesales-status-leads_info')[0].style.display = 'none';
                    $('#telesales-status-leads_paginate')[0].style.display = 'none';
                }
                if (info.recordsTotal < 10) {
                    $('#telesales-status-leads_length')[0].style.display = 'none';
                } else {
                    $('#telesales-status-leads_length')[0].style.display = 'block';
                }
            },
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                var table = $('#telesales-status-leads').DataTable();
                var info = table.page.info();
                $("td:nth-child(1)", nRow).html(iDisplayIndex + 1 + info.start);
                return nRow;
            }
        });
    }

    // function viewVerificattionDataPopup() {
    //     $('#verification-status-report-modal').modal();
    // }

    function loadVendorsLeadsChart(chartId, getChartData) {
            var setSeriesData = [];
            var salesCenterName;
            var setLegend = jQuery.parseJSON(getChartData.status)
            var chart = echarts.init(document.getElementById(chartId));
            var labelOption = {
                normal: {
                    show: true,
                    position: 'insideBottom',
                    rotate: 90,
                    textStyle: {
                        align: 'left',
                        verticalAlign: 'middle'
                    }
                }
            };
            $.each(getChartData.leads, function(index, value) {
                setSeriesData.push({
                    name: index,
                    type: 'bar',
                    stack: true,
                    label: labelOption,
                    data: value,
                });
            });
            option = {
                color: ['#cd8423', '#679dae', '#344351', '#c0a297', '#be362e'],
                tooltip: {
                    trigger: 'item',

                    axisPointer: {
                        type: 'shadow'
                    },

                    formatter: function(itemList, callback) {
                        salesCenterName = itemList.name.split('&');
                        var getTooltip = salesCenterName[0] + '<br/>';
                            getTooltip += itemList.marker + ' ' + itemList.seriesName + ': ' + itemList.value + '<br/>';
                        return getTooltip;
                    }

                },
                legend: {
                    data: setLegend,
                    align: 'left',
                    left :'10px'

                },
                toolbox: {
                    show: true,
                    feature: {
                        mark: {
                            show: false
                        },
                        saveAsImage: {
                            show: true,
                            title: "Save"
                        },
                        // myExportReportsDataFilter: {
                        //     show: true,
                        //     title: 'Export',
                        //     icon: 'image://{{ asset("images/save.png")}}',
                        //     onclick: function() {
                        //         exportVerificattionData();
                        //     }
                        // }
                    }
                },
                calculable: true,
                xAxis: [{
                    type: 'category',
                    axisLabel: {
                        interval: 0,
                        rotate: 20,
                        formatter: function(d) {
                            var xAxisLable = d.split('&');
                            return xAxisLable[0];
                        }
                    },
                    data: getChartData.vendorList
                }],
                yAxis: [{
                    type: 'value'
                }],
                series: setSeriesData
            }

            chart.setOption(option);

            chart.on('click', function(params) {
                
                $('#telesales-status-leads-modal .modal-title').html(params.seriesName + ' by Sales Centers');
                // var salesCenterId = params.name.split('&');
                $('.charthiddenfield #verification_status').val(params.seriesName);
                $('.charthiddenfield #sheet_name').val(params.seriesName + " Leads Report");
                $('.charthiddenfield #sheet_title').val(params.seriesName + ' Leads by '+salesCenterId[0]);
                $('.charthiddenfield #sales_center_id').val(salesCenterId[1]);
                brand = $('.charthiddenfield #brand').val($('.hidden-brand').val());
                getTelesalesLeadsByStatus(brand,params.seriesName, '', 2002, '', '', '', '');
            });

    }

    function loadsd2dGoodSalesPieChart(chartId, statusData) {
        var setSeriesData = [];
        var chart = echarts.init(document.getElementById(chartId));
        chart.setOption({
            tooltip: {
                trigger: 'item',
                formatter: function(x) {
                    return x.name+": " + x.value +' (' + parseFloat(x.percent).toFixed(2)+ '%)';
                }
                // formatter: '{b} :{c} ({d}%)'
            },
            toolbox: {
                show: true,
                rotate:45,
                feature: {
                    mark: {
                        show: false
                    },
                    saveAsImage: {
                        show: true,
                        title: "Save"
                    }
                }
            },
            color: ['#d48265', '#91c7ae', '#749f83', '#ca8622'],
            legend: {
                orient: 'vertical',
                left: 'left',
                data: []
            },
            series: [{
                name: 'D2D Good Sales',
                type: 'pie',
                radius: '60%',
                top:20,
                center: ['50%', '45%'],
                rotate:45,
                data: statusData,
                label: {
                    show: true,
                    rotate:true,
                    rotate:0,
                    fontSize: 9,
                    formatter: function(x) {
                        var method_name = x.name.replace(' ','\n');
                        return method_name;
                    }
                }

            }]
        });

        chart.on('click', function(params) {
            $('#telesales-status-leads-modal .modal-title').html(params.seriesName + ' Leads Report');
            $('.charthiddenfield #verification_method').val(params.data.name);
            $('.charthiddenfield #sheet_name').val(params.seriesName + ' Leads Report');
            $('.charthiddenfield #sheet_title').val(params.seriesName + ' Leads Report ');
            $('.charthiddenfield #sales_type').val("good");
            $('.charthiddenfield #agent_type').val("d2d");
            brand = $('.charthiddenfield #brand').val($('.hidden-brand').val());
            getTelesalesLeadsByStatus(brand,'', '', '', 'd2d', '', params.data.name, 'good');
        });
    }

    function loadsd2dBadSalesPieChart(chartId, statusData) {
        var setSeriesData = [];
        var chart = echarts.init(document.getElementById(chartId));

        chart.setOption({
            tooltip: {
                trigger: 'item',
                formatter: function(x) {
                    return x.name+": " + x.value +' (' + parseFloat(x.percent).toFixed(2)+ '%)';
                }
                // formatter: '{b} :{c} ({d}%)'
            },
            toolbox: {
                show: true,
                feature: {
                    mark: {
                        show: false
                    },
                    saveAsImage: {
                        show: true,
                        title: "Save"
                    }
                }
            },
            color: ['#d48265', '#91c7ae', '#749f83', '#ca8622'],
            legend: {
                orient: 'vertical',
                left: 'left',
                data: []
            },
            series: [{
                name: 'D2D Bad Sales',
                type: 'pie',
                radius: '60%',
                top:20,
                center: ['50%', '45%'],

                data: statusData,
                label: {
                    show: true,
                    fontSize: 9,
                    formatter: function(x) {
                        var method_name = x.name.replace(' ','\n');
                        return method_name;
                    }
                }

            }]
        });

        chart.on('click', function(params) {
            $('#telesales-status-leads-modal .modal-title').html(params.seriesName + ' Leads Report');
            $('.charthiddenfield #verification_method').val(params.data.name);
            $('.charthiddenfield #sheet_name').val(params.seriesName + ' Leads Report');
            $('.charthiddenfield #sheet_title').val(params.seriesName + ' Leads Report ');
            $('.charthiddenfield #sales_type').val("bad");
            $('.charthiddenfield #agent_type').val("d2d");
            brand = $('.charthiddenfield #brand').val($('.hidden-brand').val());
            getTelesalesLeadsByStatus(brand,'', '', '', 'd2d', '', params.data.name, 'bad');
        });

    }

    function loadsteleGoodSalesPieChart(chartId, statusData) {
        var setSeriesData = [];
        var chart = echarts.init(document.getElementById(chartId));

        chart.setOption({
            tooltip: {
                trigger: 'item',
                formatter: function(x) {
                    return x.name+": " + x.value +' (' + parseFloat(x.percent).toFixed(2)+ '%)';
                }
                // formatter: '{b} :{c} ({d}%)'
            },
            toolbox: {
                show: true,
                feature: {
                    mark: {
                        show: false
                    },
                    saveAsImage: {
                        show: true,
                        title: "Save"
                    }
                }
            },
            color: ['#d48265', '#91c7ae', '#749f83', '#ca8622'],
            legend: {
                orient: 'vertical',
                left: 'left',
                data: []
            },
            series: [{
                name: 'Tele Good Sales',
                type: 'pie',
                radius: '60%',
                center: ['50%', '45%'],
                top:20,
                data: statusData,
                label: {
                    show: true,
                    fontSize: 9,
                    formatter: function(x) {
                        var method_name = x.name.replace(' ','\n');
                        return method_name;
                    }
                }

            }]
        });

        chart.on('click', function(params) {
            $('#telesales-status-leads-modal .modal-title').html(params.seriesName + ' Leads Report');
            $('.charthiddenfield #verification_method').val(params.data.name);
            $('.charthiddenfield #sheet_name').val(params.seriesName + ' Leads Report');
            $('.charthiddenfield #sheet_title').val(params.seriesName + ' Leads Report ');
            $('.charthiddenfield #sales_type').val("good");
            $('.charthiddenfield #agent_type').val("tele");
            brand = $('.charthiddenfield #brand').val($('.hidden-brand').val());
            getTelesalesLeadsByStatus(brand,'', '', '', 'tele', '', params.data.name, 'good');
        });

    }

    function loadsteleBadSalesPieChart(chartId, statusData) {
        var setSeriesData = [];
        var chart = echarts.init(document.getElementById(chartId));

        chart.setOption({
            tooltip: {
                trigger: 'item',
                formatter: function(x) {
                    return x.name+": " + x.value +' (' + parseFloat(x.percent).toFixed(2)+ '%)';
                }
                // formatter: '{b} :{c} ({d}%)'
            },
            toolbox: {
                show: true,
                feature: {
                    mark: {
                        show: false
                    },
                    saveAsImage: {
                        show: true,
                        title: "Save"
                    }
                }
            },
            color: ['#d48265', '#91c7ae', '#749f83', '#ca8622'],
            legend: {
                orient: 'vertical',
                left: 'left',
                data: []
            },
            series: [{
                name: 'Tele Bad Sales',
                type: 'pie',
                rotate:true,
                top:20,
                radius: '60%',
                center: ['50%', '45%'],
                data: statusData,
                label: {
                    show: true,
                    rotate:true,
                    rotate:0,
                    fontSize: 9,
                    formatter: function(x) {
                        var method_name = x.name.replace(' ','\n');
                        return method_name;
                    }
                }

            }]
        });

        chart.on('click', function(params) {
            $('#telesales-status-leads-modal .modal-title').html(params.seriesName + ' Leads Report');
            $('.charthiddenfield #verification_method').val(params.data.name);
            $('.charthiddenfield #sheet_name').val(params.seriesName + ' Leads Report');
            $('.charthiddenfield #sheet_title').val(params.seriesName + ' Leads Report ');
            $('.charthiddenfield #sales_type').val("bad");
            $('.charthiddenfield #agent_type').val("tele");
            brand = $('.charthiddenfield #brand').val($('.hidden-brand').val());
            getTelesalesLeadsByStatus(brand,'', '', '', 'tele', '', params.data.name, 'bad');
        });

    }

    function loadVendorsLeadsPieChart(chartId, statusName, statusData) {

        var setSeriesData = [];
        var chart = echarts.init(document.getElementById(chartId));
        var salesCenterName;

        chart.setOption({
            // title: {
            //     text: statusName + ' Leads',
            //     left: 'center'
            // },
            tooltip: {
                trigger: 'item',
                formatter: function(x) {
                     salesCenterName = x.name.split('&');
                    return x.seriesName + '<br/>' + salesCenterName[0] + ':' + x.value + '(' + parseFloat(x.percent).toFixed(2)+ '%)'
                }
            },
            toolbox: {
                show: true,
                top: 0,
                feature: {
                    mark: {
                        show: false
                    },
                    saveAsImage: {
                        show: true,
                        title: "Save"
                    }
                }
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                data: []
            },
            series: [{
                name: statusName,
                type: 'pie',
                radius: '70%',
                center: ['50%', '45%'],
                top:20,
                data: statusData,
                color: ['#d48265', '#91c7ae', '#749f83', '#ca8622'],
                label: {
                    show: true,
                    //position: "inside",
                    rotate: 0,
                    fontSize:10,
                    align: 'center',
                    // padding: 3,
                    //color: "#fff",
                    formatter: function(d) {
                        var axisLable = d.name.replace(/ /g,"\n");
                        return axisLable;
                    }
                }

            }]
        });

        chart.on('click', function(params) {
            $('#telesales-status-leads-modal .modal-title').html(params.seriesName + ' Leads by Sales Centers');
            var salesCenterId = params.data.name1;

            $('.charthiddenfield #verification_status').val(params.seriesName);
            $('.charthiddenfield #sheet_name').val(params.seriesName + ' Leads Report' );
            $('.charthiddenfield #sheet_title').val(params.seriesName + ' Leads by '+params.name);
            $('.charthiddenfield #sales_center_id').val(salesCenterId);
            brand = $('.charthiddenfield #brand').val($('.hidden-brand').val());
            getTelesalesLeadsByStatus(brand,params.seriesName, '', salesCenterId, '', '', '', '');
        });

    }

    function loadChannelsLeadsChart(chartId, venderList, getChartData, statusList, channelName) {
        var setSeriesData = [];
        var setLegend = jQuery.parseJSON(statusList)

        var chart = echarts.init(document.getElementById(chartId));
        var labelOption = {
            normal: {
                show: true,
                position: 'insideBottom',
                rotate:true,
                rotate: 90,

                textStyle: {
                    align: 'left',
                    verticalAlign: 'middle'
                }
            }
        };
        $.each(getChartData, function(index, value) {
            setSeriesData.push({
                name: index,
                type: 'bar',
                stack: true,
                label: labelOption,
                data: value,
            });
        });
        option = {
            color: ['#cd8423', '#679dae', '#344351', '#c0a297', '#be362e'],
            tooltip: {
                trigger: 'item',
                axisPointer: {
                    type: 'shadow'
                },
                formatter: function(itemList,callback) {
                    var salesCenterName = itemList.name.split('&');
                    var getTooltip = salesCenterName[0] + '<br/>';
                    getTooltip += itemList.marker + ' ' + itemList.seriesName + ': ' + itemList.value + '<br/>';
                    return getTooltip;
                }
            },
            legend: {
                data: setLegend,
                left : '10px',
                right: '40px',


            },
            toolbox: {
                show: true,
                feature: {
                    mark: {
                        show: false
                    },
                    saveAsImage: {
                        show: true,
                        // top:'30px',
                        title: "Save"
                    },
                    // myExportReportsDataFilter: {
                    //     show: true,
                    //     title: 'Export',
                    //     icon: 'image://{{ asset("images/save.png")}}',
                    //     onclick: function() {
                    //         exportVerificattionData();
                    //     }
                    // }
                }
            },
            calculable: true,
            xAxis: [{
                type: 'category',
                axisLabel: {
                    interval: 0,
                    rotate: 20,
                    formatter: function(d) {
                        var xAxisLable = d.split('&');
                        return xAxisLable[0];
                    }
                },
                data: venderList
            }],
            yAxis: [{
                type: 'value'
            }],
            series: setSeriesData
        }
        chart.setOption(option);

        chart.on('click', function(params) {
            var salesCenterId = params.name.split('&');
            $('#telesales-status-leads-modal .modal-title').html(params.seriesName +' Verification Status by '+ channelName + ' Sales');
            $('.charthiddenfield #verification_status').val(params.seriesName);
            $('.charthiddenfield #sales_center_id').val(salesCenterId[1]);
            $('.charthiddenfield #sheet_name').val(params.seriesName +' Leads Report');
            $('.charthiddenfield #sheet_title').val(params.seriesName +' Verification Status by '+ channelName + ' Sales');
            $('.charthiddenfield #channel_type').val(channelName);
            brand = $('.charthiddenfield #brand').val($('.hidden-brand').val());
            getTelesalesLeadsByStatus(brand,params.seriesName, '', salesCenterId[1], channelName, '', '', '');
        });

    }

    function loadCommodityLeadsChart(chartId, venderList, getChartData, statusList, commodityName) {

        var setSeriesData = [];
        var setLegend = jQuery.parseJSON(statusList)
        var chart = echarts.init(document.getElementById(chartId));
        var labelOption = {
            normal: {
                show: true,
                position: 'insideBottom',
                rotate: 90,
                textStyle: {
                    align: 'left',
                    verticalAlign: 'middle'
                }
            }
        };
        $.each(getChartData, function(index, value) {
            setSeriesData.push({
                name: index,
                type: 'bar',
                stack: true,
                label: labelOption,
                data: value,
            });
        });
        option = {
            color: ['#cd8423', '#679dae', '#344351', '#c0a297', '#be362e'],
            tooltip: {

                trigger: 'item',
                axisPointer: {
                    type: 'shadow'
                },
                formatter: function(itemList, callback) {
                    var salesCenterName = itemList.name.split('&');
                    var getTooltip = salesCenterName[0] + '<br/>';

                        getTooltip += itemList.marker + ' ' + itemList.seriesName + ': ' + itemList.value + '<br/>';

                    return getTooltip;
                }
            },
            legend: {
                data: setLegend,
                left: '10px'
            },
            toolbox: {
                show: true,
                feature: {
                    mark: {
                        show: false
                    },
                    saveAsImage: {
                        show: true,
                        title: "Save"
                    },
                    // myExportReportsDataFilter: {
                    //     show: true,
                    //     title: 'Export',
                    //     icon: 'image://{{ asset("images/save.png")}}',
                    //     onclick: function() {
                    //         exportVerificattionData();
                    //     }
                    // }
                }
            },
            calculable: true,
            xAxis: [{
                type: 'category',
                axisLabel: {
                    interval: 0,
                    rotate: 20,
                    formatter: function(d) {
                        var xAxisLable = d.split('&');
                        return xAxisLable[0];
                    }
                },
                data: venderList
            }],
            yAxis: [{
                type: 'value'
            }],
            series: setSeriesData
        }
        chart.setOption(option);

        chart.on('click', function(params) {
            $('#telesales-status-leads-modal .modal-title').html(params.seriesName +' Verification Status by '+ commodityName +' Commodity');
            var salesCenterId = params.name.split('&');
            $('.charthiddenfield #verification_status').val(params.seriesName);
            $('.charthiddenfield #sheet_name').val(params.seriesName +' Leads Report');
            $('.charthiddenfield #sheet_title').val(params.seriesName +' Verification Status by '+commodityName +' Commodity');
            $('.charthiddenfield #sales_center_id').val(salesCenterId[1]);
            $('.charthiddenfield #commodity_type').val(commodityName);
            brand = $('.charthiddenfield #brand').val($('.hidden-brand').val());
            getTelesalesLeadsByStatus(brand,params.seriesName, '', salesCenterId[1], '', commodityName, '', '');
        });
    }

    function loadEmptyBarChart(chartId) {
        var chart = echarts.init(document.getElementById(chartId));
        var labelOption = {
            normal: {
                show: true,
                position: 'insideBottom',
                rotate: 90,
                textStyle: {
                    align: 'left',
                    verticalAlign: 'middle'
                }
            }
        };
        option = {
            color: ['#cd8423', '#679dae', '#344351', '#c0a297', '#be362e'],
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                }
            },
            legend: {
                data: []
            },
            calculable: true,
            xAxis: [{
                type: 'category',
                data: []
            }],
            yAxis: [{
                type: 'value'
            }],
            series:  [{
                data: [],
                type: 'bar'
            }]
        }
        chart.setOption(option);
    }

    $(document).ready(function() {
        var legend = document.createElement('div');
                var div = document.createElement('div');

                div.innerHTML = '<p class = "legend_text"><img src="{{asset("images/marker_image/pins/pin-pending.png")}}" height = "10" width = "10">  Pending</p>';
                div.innerHTML += '<p class = "legend_text"><img src="{{asset("images/marker_image/pins/pin-disconnected.png")}}" height = "10" width = "10">  Disconnected</p>';
                div.innerHTML += '<p class = "legend_text"><img src="{{asset("images/marker_image/pins/pin-cancelled.png")}}" height = "10" width = "10">  Cancelled</p>';
                div.innerHTML += '<p class = "legend_text"><img src="{{asset("images/marker_image/pins/pin-verified.png")}}" height = "10" width = "10">  Verified</p>';
                div.innerHTML += '<p class = "legend_text"><img src="{{asset("images/marker_image/pins/pin-declined.png")}}" height = "10" width = "10">  Declined</p>';
                legend.appendChild(div);

                $('#legend_container').html(legend);
                $('#legend_container').children().attr('id','map_legend');

        $("#exportLeads").on('click', function() {
            var data = $("#deshbordForm").serialize();
            var start_date = moment(data[3].value).format('DD-MM-YYYY');
            var end_date = moment(data[4].value).format('DD-MM-YYYY');
            var brand = data[5].value;
            var url = "{{ route('admin.dashboard.export.verificationstatusreport', ['data']) }}";
            urlcleintid = url.replace('data', '?' + data);
            console.log(urlcleintid);
            //window.open(urlcleintid, '_blank');
        });


        $('#dateclose').datepicker({
            format: "mm/dd/yyyy",
            autoclose: true,
            endDate: "today"
        });
        $('#dateclose2').datepicker({
            format: "mm/dd/yyyy",
            autoclose: true,
            endDate: "today"
        });

        $('#example2').datepicker({
            format: "mm/dd/yyyy"
        }).on('change', function() {
            $('.datepicker').hide();
        });

        $("#telesales-status-leads-modal").on('hide.bs.modal', function() {
            $('.charthiddenfield #verification_status').val('');
            $('.charthiddenfield #sheet_name').val('');
            $('.charthiddenfield #sheet_title').val('');
            $('.charthiddenfield #agent_id').val('');
            $('.charthiddenfield #sales_center_id').val('');
            $('.charthiddenfield #channel_type').val('');
            $('.charthiddenfield #commodity_type').val('');
            $('.charthiddenfield #verification_method').val('');
            $('.charthiddenfield #sales_type').val('');
            $('.charthiddenfield #agent_type').val('');
            $("#telesales-status-leads").dataTable().fnDestroy();
        });
    });


</script>

@endsection
