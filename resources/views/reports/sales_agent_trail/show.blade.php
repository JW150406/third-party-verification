@extends('layouts.admin')
@push('styles')
<style type="text/css">
    .activty-label
    {
        text-transform:capitalize;
    }
</style>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.min.css')}}" />
@endpush
@section('content')

    <?php
$breadcrum = array(
    array('link' => '', 'text' => "Analytics"),
    //array('link' => "", 'text' => 'Lead Details Report')
);
breadcrum($breadcrum);
?>
    <div class="tpv-contbx">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="cont_bx3">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="client-bg-white">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h1 class="mt10">Sales Agent Trail</h1>
                                    </div>

                                </div>
                                <div class="message">
                                    @if ($message = Session::get('success'))
                                        <div class="alert alert-success alert-dismissable">
                                            {{ $message }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif
                                    @if ($message = Session::get('error'))
                                        <div class="alert alert-danger alert-dismissable">
                                            {{ $message }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div class="sales_tablebx mt30">
                                    <div class="row mb15" @if(!Auth::user()->hasPermissionTo('filter-sales-agent-trail'))  style="display: none" @endif>
                                        <div class="btn-group pull-right btn-sales-all">
                                            <select  id="filter_sales_agent" name="agent_id" class="select2 btn btn-green dropdown-toggle mr15 " role="menu">
                                            </select>
                                        </div>
                                        <div class="btn-group pull-right btn-sales-all">

                                            <select  id="filter_sales_center_location" name="locaton_id" class="select2 btn btn-green dropdown-toggle mr15 " role="menu" @if(Auth::user()->isLocationRestriction())) disabled @endif>


                                            </select>
                                        </div>
                                        <div class="btn-group pull-right btn-sales-all">

                                            <select  id="filter_sales_center" name="salescenter_id" class="select2 btn btn-green dropdown-toggle mr15 " role="menu" @if(Auth::user()->hasAccessLevels(['salescenter']))) disabled @endif>


                                            </select>
                                        </div>
                                        <div class="btn-group pull-right btn-sales-all">

                                            <select  id="filter_client" name="client_id" class="select2 btn btn-green dropdown-toggle mr15 " role="menu" @if(Auth::user()->isAccessLevelToClient())) disabled @endif>

                                                @foreach($clients as $client)
                                                    <option value="{{$client->id}}" @if($loop->index == 0) selected @endif>{{$client->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="btn-group pull-right btn-sales-all">
                                            <button type="button" id="filter_date" class="btn mr15">{!! getimage('images/calender.png') !!}</button>

                                        </div>
                                    </div>


                                    <div class="row">
                                        <div id="expandPane" class="col-xs-8 col-sm-8 col-md-8"
                                        style="height:100%; position:relative;">
                                              <div>
                                                <div class="date-sec" style="width: auto; margin-right:15px;"><h4 id="map-title"></h4></div>
                                                <div class="working-time"  style="margin-right:14px;">
                                                    <table>
                                                        <tr>
                                                            <th>
                                                                Working
                                                            </th>
                                                            <th>
                                                                Break
                                                            </th>
                                                            <th>
                                                                In Transit
                                                            </th>
                                                            <th>
                                                                Total
                                                            </th>
                                                        </tr>
                                                        <tr id="working-time">
                                                            <td>
                                                                00:00:00
                                                            </td>
                                                            <td>
                                                                00:00:00
                                                            </td>
                                                            <td>
                                                                00:00:00
                                                            </td>
                                                            <td>
                                                                00:00:00
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div id="map" class="salesagent-trail-map"></div>
                                              </div>
                                              <div id="collapseButton">
                                                <span>&#8250;</span>
                                            </div>

                                        </div>
                                        <div id="collapsePane" class="col-xs-4 col-sm-4 col-md-4" style="height:100%;">
                                            <div class="detail-sec client-bg-white border-line">
                                                <div class="date-sec">
                                                    <h4 id="date-title"></h4>
                                                </div>

                                                <div class="table-detail-sec">
                                                    <table id="all-locations">
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                 <div class="list-loader" style="display: none;"><img src="{{asset('images/table-loader.svg')}}" alt="{{ config('app.name', 'Laravel') }}" style="height: 30px ;"  />
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
    </div>
    <div class="modal fade custom-date-picker-modal " id="date-picker-modal">
        <div class="modal-dialog" style="width: 25%">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row date-row">
                        <div class="col-sm-12 col-md-12">
                            <div class="sor_fil">
                                <div class="search">

                                    <label for="date">Date</label>
                                    <div class="search-container date-search-container">
                                        <button type="button">{!! getimage('images/calender.png') !!}</button>
                                        <input placeholder="Date" name="activity_date"  id="activity_date" type="text" readonly >

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="row time-row">
                        <div class="col-sm-6 col-md-6">
                            <div class="sor_fil from-sec">
                                <div class="search time-picker">
                                    <label for="date">From</label>
                                    <div class="search-container date-search-container">

                                        <button type="button"><img src="{{asset('images/clock.svg')}}" style="height: 20px"></button>
                                        <input type="text" id="activity_from_time" class=" floating-label" placeholder="From" disabled>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <div class="sor_fil to-sec">
                                <div class="search time-picker">
                                    <label for="date">To</label>
                                    <div class="search-container date-search-container">

                                        <button type="button"><img src="{{asset('images/clock.svg')}}" style="height: 20px"></button>
                                        <input type="text" id="activity_to_time" class="floating-label" placeholder="To" disabled>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="row time-row">
                        <div class="col-sm-12 col-md-6">
                            <div class="sor_fil to-sec">
                                <div class="search">
                                    <label for="date">To</label>
                                    <div class="search-container date-search-container">

                                        <button type="button"><img src="{{asset('images/clock.svg')}}" style="height: 20px"></button>
                                        <input type="text" id="activity_to_time" class="floating-label" placeholder="To" disabled>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <div class="checkbox-row">
                        <ul class="form-group">
                            <li>
                                <input class="styled-checkbox" id="custom"  type="radio" name="selected_activity" value="custom">
                                <label for="custom">Custom</label>
                            </li>
                            <li>
                                <input class="styled-checkbox" id="last_3_hours"  type="radio" name="selected_activity" value="last_3_hours">
                                <label for="last_3_hours">Last 3 hours</label>
                            </li>
                            <li>
                                <input class="styled-checkbox" id="last_6_hours"  type="radio" name="selected_activity" value="last_6_hours">
                                <label for="last_6_hours">Last 6 hours </label>
                            </li>
                            <li>
                                <input class="styled-checkbox" id="today"  type="radio" name="selected_activity" value="today" checked="">
                                <label for="today">Today</label>
                            </li>
                            <li>
                                <input class="styled-checkbox" id="yesterday"  type="radio" name="selected_activity" value="yesterday">
                                <label for="yesterday">Yesterday</label>
                            </li>


                        </ul>
                    </div>


                </div>

                <div class="modal-footer pd0">
                    <div class="btnintable bottom_btns pd0">
                        <div class="btn-group">
                            <button id="show-trail-btn" type="button" class="btn btn-green" data-dismiss="modal">Show Trail</button>
                            <button type="button" class="btn btn-red" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script>
        var map = mainPolyline = breakPolyline =  null;
        var markers = [];
        const colors = {clock_in: 'FE7569',clock_out: '4D94FF',break_in: 'B366FF',break_out: 'FF0080',arrival_in: 'FF9900',arrival_out: '33CCCC',lead_submitted: '00CC00',multiple:'408000'};
        $(document).ready(function () {
            var today = new Date();
            usaTime = today.toLocaleString("en-US", {timeZone: "{{Auth::user()->timezone}}"});
            today = new Date(usaTime);
             $("#activity_from_time").datetimepicker({
                format: "HH:mm",
                // format: "LT",
                // keepOpen: true,
                // debug: true,
                icons: {
                  up: "fa fa-chevron-up",
                  down: "fa fa-chevron-down"
                }
              });
            $("#activity_to_time").datetimepicker({
                format: "HH:mm",
                icons: {
                  up: "fa fa-chevron-up",
                  down: "fa fa-chevron-down"
                }
              });
              $('#activity_date').datepicker({
                  endDate: today,
                  autoclose: true
              }).on('show', function(e) {
                  if($(this).val().length > 0) {
                      $(this).datepicker('update', new Date($(this).val()));
                  }
              });

            $('#activity_from_time').datetimepicker().on('dp.change', function (event) {
                $('#activity_to_time').datetimepicker('minDate', $(this).val());
            });

            $('#activity_to_time').datetimepicker().on('dp.change', function (event) {
                $('#activity_from_time').datetimepicker('maxDate', $(this).val());
            });

            // $('#activity_date').on('changeDate', function(e) {
            //     getSalseAgents();
            //     setDateTitle();
            // });

            $('#show-trail-btn').on('click', function(e) {
                getSalseAgentsActivityLocation();
            });

            $('#filter_client').on('change', function(e) {
                getSalesCenter();
            });
            $('#filter_sales_center').on('change', function(e) {
                getSalseCenterLocations();
            });

            $('#filter_sales_center_location').on('change', function(e) {
                getSalseAgents();
            });

            $('#filter_sales_agent').on('change', function(e) {
                getSalseAgentsActivityLocation();
            });

            $('#filter_date').on('click', function(e) {
                $("#date-picker-modal").modal();
            });

            $('#activity_date').on('focus', function(e) {
                resetDateFilter();
                $("#custom").prop('checked','checked');
            });

            $('input[name=selected_activity]').on('click', function(){
                if($('input[name=selected_activity]:checked').val() == "custom"){
                    resetDateFilter();
                } else {
                    $('#activity_from_time,#activity_to_time,#activity_date').val('');
                    $('#activity_from_time,#activity_to_time').attr('disabled',true);
                }
            });

            $(document).on('click','.location-list', function(e) {
                var lat = $(this).data('marker-lat');
                var lng = $(this).data('marker-lng');
                var latLng = new google.maps.LatLng(lat,lng);
                map.setCenter(latLng)
                //var marker = markers[index];
                //map.setCenter(marker.getPosition());
                map.setZoom(15);

            });
            getSalesCenter();
            setDateTitle();
        });

        function resetDateFilter() {
            var today = new Date();
            usaTime = today.toLocaleString("en-US", {timeZone: "{{Auth::user()->timezone}}"});
            today = new Date(usaTime);
            if($('#activity_date').val() == '') {
                $('#activity_from_time,#activity_to_time').attr('disabled',false);
                $('#activity_date').datepicker("setDate", today);
                $('#activity_from_time').data('DateTimePicker').date('00:00');
                $('#activity_to_time').data('DateTimePicker').date('23:59');
            }            
        }

        function setDateTitle(activity_date='') {
            console.log(activity_date);
            if( activity_date == '') {
                <?php $date = Carbon\Carbon::now()->setTimezone(Auth::user()->timezone)->format('l, F jS Y');?>
                $("#date-title").html("{{$date}}");
            } else {
                $("#date-title").html(activity_date);
            }
        }

        function getSalesCenter(salescenter_id = '') {
            var client_id = $("#filter_client").val();
            if (client_id > 0) {
                $.ajax({
                    url: "{{route('getSalesCenterByClientId')}}",
                    type: "POST",
                    data: {
                        client_id: client_id,
                        _token: "{{csrf_token()}}"
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $("#filter_sales_center").html(response.options);
                            $("#filter_sales_center").prop("selectedIndex", 0).trigger('change');

                            if(response.options == '') {
                                clearMap();
                                $('#filter_sales_center').html('<option value="">None</option>');
                            }

                        }
                    },
                    error: function(xhr) {
                        console.log(xhr);
                    }
                });
            }
        }


        function getSalseCenterLocations() {
            var client_id =$("#filter_client").val();
            var salescenter_id =$("#filter_sales_center").val();
            if(client_id > 0) {
                $.ajax({
                    url: "{{route('salescenter.getSalesCenterLocations')}}",
                    data: {
                        client_id: client_id,
                        salescenter_id: salescenter_id
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $("#filter_sales_center_location").html(response.options);
                            $("#filter_sales_center_location").prop("selectedIndex", 0).trigger('change');
                            if(response.options == '') {
                                clearMap();
                                $('#filter_sales_center_location').html('<option value="">None</option>');
                                $('#filter_sales_agent').html('<option value="">None</option>');
                            }
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr);
                    }
                });
            }
        }

        function getSalseAgents() {

            var client_id =$("#filter_client").val();
            var location_id =$("#filter_sales_center_location").val();
            var activity_date = $("#activity_date").val();
            if(client_id > 0 && location_id > 0) {
                $('.list-loader').show();
                $.ajax({
                    url: "{{route('ajax.getSalesCenterAgentsOption')}}",
                    data: {
                        client_id: client_id,
                        location_id: location_id,
                        activity_date: activity_date
                    },
                    success: function(response) {
                        $('.list-loader').hide();
                        if (response.status == 'success') {
                            if(response.options == '') {
                                clearMap();
                                $('#filter_sales_agent').html('<option value="">None</option>');
                            } else {
                                $("#filter_sales_agent").html(response.options);
                                $("#filter_sales_agent").prop("selectedIndex", 0).trigger('change');
                            }
                        }
                    },
                    error: function(xhr) {
                        $('.list-loader').hide();
                        console.log(xhr);
                    }
                });
            }
        }

        function getSalseAgentsActivityLocation() {
            clearMap();
            var agent_id = $("#filter_sales_agent").val();
            var activity_date = $("#activity_date").val();
            var activity_from = $("#activity_from_time").val();
            var activity_to = $("#activity_to_time").val();
            var selected_activity = $('input[name=selected_activity]:checked').val();
           // if(agent_id > 0) {
                $('.list-loader').show();
                $.ajax({
                    url: "{{route('getSalesAgentActivityLocations')}}",
                    data: {
                        agent_id: agent_id,
                        selected_activity: selected_activity,
                        activity_date: activity_date,
                        activity_from: activity_from,
                        activity_to: activity_to,
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            setDateTitle(response.activity_date);
                            setActivityTime(response.activity_time);
                            setPolylines(response.polylines);
                            setBreak(response.break_polylines);
                            setMarkers(response.markers);
                            setAddress(response.locations);
                            $("#map-title").html(response.title);
                        }
                        $('.list-loader').hide();
                    },
                    error: function(xhr) {
                        console.log(xhr);
                        $('.list-loader').hide();
                    }
                });
           // }
        }

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 3,
                center: {lat: 47.751569, lng: 1.675063},
                mapTypeId: 'terrain',
            });
            setPolylines();
        }

        // set polylines on sales agent route
        function setPolylines(pathCoordinates=[]) {
            var lineSymbol = getLineSymbol();

            mainPolyline = new google.maps.Polyline({
                path: pathCoordinates,
                geodesic: true,
                strokeColor: '#1c5997',
                strokeOpacity: 0.8,
                strokeWeight: 5,
                icons: [{
                    icon: lineSymbol,
                    offset: '0%',
                    repeat: '30px'
                }],
            });

            mainPolyline.setMap(map);
            setMapBounds(pathCoordinates);
        }

        function setBreak(markerCoordinates=[]) {

            $.each( markerCoordinates, function( key, value ) {
                setBreakPolylines(value);

            });
        }

        // set break polylines on sales agent route
        function setBreakPolylines(pathCoordinates=[]) {

            var lineSymbol = getLineSymbol();
            breakPolyline = new google.maps.Polyline({
                path: pathCoordinates,
                geodesic: true,
                strokeColor: '#808080',
                strokeWeight: 5,
                icons: [{
                    icon: lineSymbol,
                    offset: '0%',
                    repeat: '30px'
                }],
            });

            breakPolyline.setMap(map);
        }

        function setMapBounds(pathCoordinates=[]) {
            if (pathCoordinates.length > 0) {
                var bounds = new google.maps.LatLngBounds();
                $.each( pathCoordinates, function( key, value ) {
                    var latLng = new google.maps.LatLng(value.lat, value.lng);
                    bounds.extend(latLng);
                });
                map.fitBounds(bounds);
            }
        }

        function getLineSymbol() {
            var lineSymbol = {
                path: 'M 6.785156 0 L 4.214844 2.566406 L 12.648438 11 L 4.214844 19.433594 L 6.785156 22 L 17.785156 11 Z M 6.785156 0',
                strokeColor: '#FFF',
                fillColor: '#FFF',
                fillOpacity: 1,
                scale: 0.25,
                rotation: -90,
                anchor: new google.maps.Point(0, 10),
                strokeWeight: 2
            };
            return lineSymbol;

        }

        function clearPolylines() {
            if (mainPolyline != null) {
                mainPolyline.setMap(null);
            }

            if (breakPolyline != null) {
                breakPolyline.setMap(null);
            }
            mainPolyline = null;
            breakPolyline = null;
        }

        // set marker of that location , created a lead by sales agent
        function setMarkers(markerCoordinates=[]) {
            var label = 0;
            var color;
            $.each( markerCoordinates, function( key, value ) {
                if(value.activity_type == 'multiple') {
                    label = '...';
                } else {
                    label = value.label;
                }
                color = colors[value.activity_type];
                addMarker(value.lat,value.lng,label.toString(),color,value.time,value.activity_type,value.activity);

            });
        }
        function addMarker(lat,lng,label,color='FE7569',time=null,activity_type=null,activities=[]) {
            if(lat && lng) {
                
                color = '#'+color;
                var square = {
                    path: 'M -2,-2 2,-2 2,2 -2,2 z', // 'M -2,0 0,-2 2,0 0,2 z',
                    strokeColor: color,
                    fillColor: color,
                    fillOpacity: 1,
                    scale: 4
                };
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(lat, lng),
                    label: {text: label, color: "white",fontSize: "12px", fontWeight: "bold" },
                    map: map,
                    icon:square
                });
                markers.push(marker);
                setMarkerInfo(marker,time,activity_type,activities);                
            }
        }

        function getMarkerInfoContent(time,activity_type,activities) {

            if(activity_type == 'multiple') {
                var data='';
                var color;
                $.each( activities, function( key, value ) {
                    color = colors[value.activity_type];
                    activity_type = getActivityLabel(value.activity_type);
                    if( key > 0) {
                        data += '<div class="marker-info-divider"></div>';
                    }
                    data += '<p><span class="numbers" style="background-color:#'+color+'">'+value.label+'</span> Time: '+value.time+'</p><p class="activty-label">Activity: '+activity_type+'</p>';

                });
            } else {
                activity_type = getActivityLabel(activity_type);
                var data = '<p>Time: '+time+'</p><p class="activty-label">Activity: '+activity_type+'</p>';
            }

            return data;
        }

        function setMarkerInfo(marker,time,activity_type,activities=[]) {

            var data = getMarkerInfoContent(time,activity_type,activities);
            var infowindow = new google.maps.InfoWindow({
                content: data
            });

            google.maps.event.addListener(marker, 'click', function() {
                infowindow.open(map,marker);
            });

        }
        function clearMarkers() {
            $.each( markers, function( key, value ) {
                value.setMap(null);
            });
            markers=[];
        }

        function clearMap() {
            clearPolylines();
            clearMarkers();
            $("#map-title").html('');
            $("#all-locations tbody").html('<tr><td colspan="3" style="text-align:center"><strong>Data not found.</strong></td></tr>');
        }

        function setAddress(latLngs = []) {

            if(latLngs.length > 0) {
                $("#all-locations tbody").html('');
                var background ;
                var activity_type ;
                $.each( latLngs, function( key, latLng ) {
                    var index = key+1;

                    activity_type = getActivityLabel(latLng.activity_type);

                    background = 'style="background-color:#'+colors[latLng.activity_type]+'"';
                    $("#all-locations tbody").append('<tr><td><span class="numbers" '+background+'>'+index+'</span></td><td class="activty-label"><li><strong  class="location-list" data-marker-lat="'+latLng.lat+'" data-marker-lng="'+latLng.lng+'">' +activity_type +'</strong></li>'+latLng.address+'</td><td><p>'+latLng.time + '</p></td></tr>');
                });
            }
        }

        function getActivityLabel(activity_type) {
            activity_type = activity_type.replace("_", " ");
            if (activity_type == 'arrival out') {
                activity_type = 'Departure';
            } else if(activity_type == 'arrival in') {
                activity_type = 'Arrival';
            } else if(activity_type == 'break in') {
                activity_type = 'Start Break';
            } else if(activity_type == 'break out') {
                activity_type = 'End Break';
            }

            return activity_type;
        }

        function setActivityTime(activity) {
            var data = "<td>"+activity.working_time+"</td><td>"+activity.break_time+"</td><td>"+activity.transit_time+"</td><td>"+activity.total_time+"</td>";
            $("#working-time").html(data);
        }
        $("#collapseButton").on('click',function(e) {
          var currentState = $("#collapsePane").data("currentState");
          var class12 = "col-xs-12 col-sm-12 col-md-12";
          var class8 = "col-xs-8 col-sm-8 col-md-8";
          var class4 = "col-xs-4 col-sm-4 col-md-4";
          var leftArrow = "&#8249;";
          var rightArrow = "&#8250;";

        if (currentState == "expanded") {
            $("#collapsePane").removeClass(class4).removeClass("hide").addClass(class4);
            $("#expandPane").removeClass(class12).addClass(class8);
            $("#collapseButton span").html(rightArrow);
            $("#collapseButton").removeClass("collapseButtonExpanded");
            $("#collapsePane").data("currentState", "collapsed");
          } else {

            $("#collapsePane").removeClass(class4).addClass("hide");
            $("#expandPane").removeClass(class8).addClass(class12);
            $("#collapseButton span").html(leftArrow);
            $("#collapseButton").addClass("collapseButtonExpanded");
            $("#collapsePane").data("currentState", "expanded");
          }
        });
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{config()->get('constants.GOOGLE_MAP_API_KEY')}}&callback=initMap">
    </script>
@endpush
