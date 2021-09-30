@extends('layouts.admin')
@section('content')

<?php
$breadcrum = array(
    array('link' => '', 'text' => "Analytics")
    //array('link' => "", 'text' => 'Lead Details Report')
);
breadcrum($breadcrum);

$isLevelClientAndSalesCenter = Auth::user()->isAccessLevelToClient();
$isLevelSalesCenter = Auth::user()->hasAccessLevels('salescenter');
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
                                    <h1 class="mt10">Critical Alert Report</h1>
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
                                <div class="row mb15" @if(!Auth::user()->hasPermissionTo('filter-critical-alert-report')) style="display: none" @endif>
                                    <form id="filter-form" action="{{route('export.critical.alert')}}" method="post">
                                        @csrf
                                        @include('reports.filters.reset')
                                        @include('reports.filters.locations')
                                        <div class="btn-group pull-right btn-sales-all">
                                            <select  id="sales_center" name="salescenter_id" class="select2 btn btn-green dropdown-toggle mr15 " role="menu" @if($isLevelSalesCenter) disabled @endif>
                                                @if(!$isLevelSalesCenter)

                                                <option value="" selected>All Sales Centers</option>
                                                @endif
                                                @foreach($salesCenters as $salesCenter)
                                                <option class="salecenters-opt client-{{ $salesCenter->client_id }}" client="{{ $salesCenter->client_id }}" value="{{$salesCenter->id}}" @if(old("salescenter_id")==$salesCenter->id) selected @endif>{{$salesCenter->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @include('reports.filters.brands')
                                        <div class="btn-group pull-right btn-sales-all">
                                            <select  id="client" name="client_id" class="select2 btn btn-green dropdown-toggle mr15 " role="menu" @if($isLevelClientAndSalesCenter) disabled @endif>
                                                @if(!$isLevelClientAndSalesCenter)

                                                <option value="" selected>All Clients</option>
                                                @endif
                                                @foreach($clients as $client)
                                                <option value="{{$client->id}}" @if(old("client_id")==$client->id) selected @endif>{{$client->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="sor_fil utility-btn-group mr15">
                                            <div class="search">
                                                <div class="search-container" style="width: 115px">

                                                    <button type="button">{!! getimage('images/search.png') !!}</button>
                                                    <input placeholder="Search" name="filter_search" id="filter_search" type="text" value="{{old('filter_search')}}">

                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="sor_fil utility-btn-group mr15">
                                            <div class="search">
                                                <div class="search-container date-search-container" style="width: 185px">

                                                    <button type="button">{!! getimage('images/calender.png') !!}</button>
                                                    <input placeholder="Date of verification" name="verification_date" id="verification_date" type="text" value="{{old('verification_date')}}" class="" readonly>

                                                </div>
                                            </div>
                                        </div> -->
                                        <div class="sor_fil utility-btn-group mr15">
                                            <div class="search">
                                                <div class="search-container date-search-container" style="width: 185px">

                                                    <button type="button">{!! getimage('images/calender.png') !!}</button>
                                                    <input placeholder="Date" name="submission_date" id="submission_date" type="text" value="{{old('submission_date')}}" class="" readonly>

                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                @if(Auth::user()->hasPermissionTo('export-critical-alert-report'))
                                <div class="row mb15">
                                    <div class="btn-group pull-right mr15">
                                        <button type="button" class="btn btn-green" id="export">Export
                                        </button>
                                    </div>
                                </div>
                                @endif
                                <div class="table-responsive">
                                    <table class="table ld-report " id="critical-table">
                                        <thead>
                                            <tr class="acjin">
                                                <th>Lead Number</th>
                                                <th>Customer Name</th>
                                                <th>Alert Discription</th>
                                                
                                                <!-- <th>Alert Status</th> -->
                                                <th>Lead Status</th>
                                                <th>Client</th>
                                                <th>Brand</th>
                                                <th>Sales Center</th>
                                                <th>Sales Center Location</th>
                                                <th>Agent</th>
                                                <th>External ID</th>
                                                <th>Date of Submission</th>
                                                <th>Date of TPV</th>
                                                <th class="action-width">Action</th>
                                            </tr>
                                        </thead>
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
@include('reports.critical_alert.view-alert-discription')
@endsection
@push('scripts')
<script>
    var today, firstDay;
    $(document).ready(function() {
        today = new Date();
        usaTime = today.toLocaleString("en-US", {
            timeZone: "{{Auth::user()->timezone}}"
        });
        today = new Date(usaTime);
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();
        firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        today = mm + '/' + dd + '/' + yyyy;

        $('#submission_date').daterangepicker({
            autoUpdateInput: true,
            startDate: firstDay,
            endDate: today,
            maxDate: today
        });
        @if(!empty(old('submission_date')))
        var oldSubmissionDate = "{{old('submission_date')}}";
        var sumissionDate = oldSubmissionDate.split('-');
        startDate = sumissionDate[0];
        endDate = sumissionDate[1];
        resetFilterDate(startDate, endDate);
        @else
        resetFilterDate(firstDay, today);
        @endif

        $('#verification_date').daterangepicker({
            autoUpdateInput: true,
            startDate: firstDay,
            endDate: today,
            maxDate: today,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        @if(!empty(old('verification_date')))
        var oldVerificationDate = "{{old('verification_date')}}";
        var verificationDate = oldVerificationDate.split('-');
        startDate = verificationDate[0];
        endDate = verificationDate[1];
        $('#verification_date').data('daterangepicker').setStartDate(startDate);
        $('#verification_date').data('daterangepicker').setEndDate(endDate);
        @endif

        var isClientVisible = true;
        @if($isLevelSalesCenter)
        isClientVisible = false;
        @endif
        var criticalTable = $('#critical-table').DataTable({
            dom: 'tr<"bottom"lip>',
            processing: true,
            serverSide: true,
            searchDelay: 1000,
            lengthChange: true,
            ajax: {
                url: "{{ route('reports.critical.alert') }}",
                data: function(d) {
                    d.client_id = $('#client').val();
                    d.brand = $('#brand').val();
                    d.salescenter_id = $('#sales_center').val();
                    d.submission_date = $('#submission_date').val();
                    d.verification_date = $('#verification_date').val();
                    d.location = $('#location').val();
                }
            },
            aaSorting: [
                [11, 'desc']
            ],
            columns: [{
                    data: 'refrence_id',
                    name: 'telesales.refrence_id'
                },
                {
                    data: 'customer_name',
                    name: 'customer_name',
                    orderable: false
                },
                {
                    data: 'alert_description',
                    name: 'alert_description',
                    orderable: false,
                    searchable: false
                },
                /*
                {
                    data: 'alert_status',
                    name: 'alert_status',
                    orderable: false
                },*/
                {
                    data: 'status_new',
                    name: 'status_new'
                },
                {
                    data: 'client_name',
                    name: 'client.name',
                    visible: isClientVisible
                },
                {
                    data: 'brand',
                    name: 'brand',
                },
                {
                    data: 'salescenter_name',
                    name: 'user.salescenter.name'
                },
                {
                    data: 'address',
                    name: 'user.salesAgentDetails.location.name'
                },
                {
                    data: 'agent_name',
                    name: 'user.first_name'
                },
                {
                    data: 'external_id',
                    name: 'external_id',
                    orderable: false
                },
                {
                    data: 'created_at',
                    name: 'telesales.created_at'
                },
                {
                    data: 'reviewed_at',
                    name: 'telesales.reviewed_at'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'id',
                    name: 'telesales.id',
                    searchable: false,
                    visible: false
                },
                {
                    data: 'id',
                    name: 'user.last_name',
                    visible: false
                },
            ],
            'fnDrawCallback': function() {
                var table = $('#critical-table').DataTable();
                var info = table.page.info();
                if (info.pages > 1) {
                    $('#critical-table_info')[0].style.display = 'block';
                    $('#critical-table_paginate')[0].style.display = 'block';
                } else {
                    $('#critical-table_info')[0].style.display = 'none';
                    $('#critical-table_paginate')[0].style.display = 'none';
                }
                if (info.recordsTotal < 10) {
                    $('#critical-table_length')[0].style.display = 'none';
                } else {
                    $('#critical-table_length')[0].style.display = 'block';
                }

                if (info.recordsTotal > 0) {
                    $('#export').show();
                } else {
                    $('#export').hide();
                }
            }
        });
        // $( "#client" ).change(function() {
        //     getSalesCenters($(this).val());
        // });
        // $( "#sales_center" ).change(function() {
        //     setLocations();
        // });
        $("#sales_center,#client,#location,#brand").change(function() {
            criticalTable.ajax.reload();
        });

        $('#filter_search').change(function() {
            criticalTable.search($(this).val()).draw();
        })

        $('#submission_date,#verification_date').on('apply.daterangepicker', function(ev, picker) {
            criticalTable.ajax.reload();
        });

        $('#submission_date').on('cancel.daterangepicker', function() {
            resetFilterDate(firstDay, today);
            criticalTable.ajax.reload();
        });

        $('#verification_date').on('cancel.daterangepicker', function() {
            $(this).val('');
            criticalTable.ajax.reload();
        });

        function resetFilterDate(startDate, endDate) {
            $('#submission_date').data('daterangepicker').setStartDate(startDate);
            $('#submission_date').data('daterangepicker').setEndDate(endDate);
        }

        $("#export").click(function() {
            $("#filter-form").submit();
        });

        // function getSalesCenters(client_id=null) {
        //     $.ajax({
        //         url: "{{ route('ajax.getSalesCenterAndCommodity') }}",
        //         type: "POST",
        //         data: {
        //             '_token': "{{ csrf_token() }}",
        //             'client_id': client_id
        //         },
        //         success: function(res) {
        //             if (res.status === true) {

        //                 var shtml = "";
        //                 @if(!$isLevelSalesCenter)
        //                     shtml = '<option value="" selected>All Sales Centers</option>';
        //                 @endif
        //                 var sales_center = res.data.sales_centers;
        //                 for (i = 0; i < sales_center.length; i++) {
        //                     shtml += '<option value="' + sales_center[i].id + '">' + sales_center[i].name + '</option>'
        //                 }
        //                 $('#sales_center').html(shtml);
        //                 setLocations();
        //             } else {
        //                 console.log(res.message);
        //             }
        //         }
        //     })

        // }

        // // set options of location filter
        // function setLocations(isAllSalesCenter=true)
        // {
        //     var clientId = $("#client").val();
        //     var salescenterId = $("#sales_center").val();
        //     setSalesCenterLocationOptions("location",clientId,salescenterId,'',isAllSalesCenter,"All Locations");
        // }
        setLocations();

        // this is for ajax datatable clicking on pagination button
        $('body').on('click', '.dataTables_paginate .paginate_button', function() {
            $('html, body').animate({
                scrollTop: $(".container").offset().top
            }, 400);
        });
        $('#client').change(function(){
            brandReportFilters($(this).val());
            $('#brand').val(null).trigger('change');
        })
    });
</script>
@include('reports.filters.scripts')
@endpush