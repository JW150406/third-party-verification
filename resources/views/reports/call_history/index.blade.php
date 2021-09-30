@extends('layouts.admin')
@section('content')

    <?php
    $breadcrum = array(
        array('link' => '', 'text' => "Analytics")
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
                                        <h1 class="mt10">Call History Report</h1>
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
                                    
                                    <div class="row mb15" @if(!Auth::user()->can('filter-lead-detail-report'))  style="display: none" @endif>
                                    
                                        <div class="btn-group pull-right btn-sales-all">
                                            <select  id="call_filter_sales_center" class="select2 btn btn-green dropdown-toggle mr15 " role="menu" @if(Auth::user()->hasAccessLevels(['salescenter'])) disabled @endif>
                                                @if(!Auth::user()->hasAccessLevels(['salescenter']))
                                                  <option value="" selected>All Sales Centers</option>
                                                @endif
                                                @foreach($salesCenters as $salesCenter)
                                                    <option value="{{$salesCenter->id}}">{{$salesCenter->name}}</option>
                                                @endforeach
                                            </select>
                                        </div> 
                                        <div class="btn-group pull-right btn-sales-all">
                                            <select  id="call_filter_client" name="client_id" class="select2 btn btn-green dropdown-toggle mr15 " role="menu" @if(Auth::user()->isAccessLevelToClient()) disabled @endif>
                                                @if(!Auth::user()->isAccessLevelToClient())
                                                    <option value="" selected>All Clients</option>
                                                @endif
                                                @foreach($clients as $client)
                                                    <option value="{{$client->id}}">{{$client->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="sor_fil utility-btn-group mr15">
                                            <div class="search">
                                                <div class="search-container">

                                                    <button type="button">{!! getimage('images/search.png') !!}</button>
                                                    <input placeholder="Lead Number" id="call_filter_search" type="text" value="">

                                                </div>
                                            </div>
                                        </div>
                                       {{-- <div class="sor_fil utility-btn-group mr15">
                                            <div class="search">
                                                <div class="search-container date-search-container">

                                                    <button type="button">{!! getimage('images/calender.png') !!}</button>
                                                    <input placeholder="Date" name="filter_date_lead" id="filter_date" type="text" value="" class="" readonly>

                                                </div>
                                            </div>
                                        </div>--}}
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table " id="call-history">
                                            <thead>
                                            <tr class="acjin">
                                                <th>Sr.No.</th>
                                                <th>Lead Id</th>
                                                <th>Scheduled Type</th>
                                                <th>Call Time</th>
                                                <th>Type</th>
                                                <th>Language</th>
                                                <th>Call Type</th>
                                                <th>Attempt No.</th>
                                                <th>{{config('constants.CALL_DISPLAY_COLUMN_NAME.dial_status')}} (Twilio)</th>
                                                <th>{{config('constants.CALL_DISPLAY_COLUMN_NAME.schedule_status')}}</th>
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
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            // var today = new Date();
            // var dd = String(today.getDate()).padStart(2, '0');
            // var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            // var yyyy = today.getFullYear();
            // var firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            // today = mm + '/' + dd + '/' + yyyy;

            // $('#filter_date').daterangepicker({
            //     autoUpdateInput: true,
            //     startDate: firstDay,
            //     endDate: today,
            //     maxDate: new Date()
            // });
            // resetFilterDate(firstDay,today);

            // var isClientVisible = true ;
            // @role(['sales_center_admin','sales_center_qa'])
            //     isClientVisible = false;
            // @endrole

            var leadTable =    $('#call-history').DataTable({
                dom: 'tr<"bottom"lip>',
                processing: true,
                serverSide: true,
                searchDelay: 1000,
                lengthChange: true,
                ajax: {
                    url: "{{ route('reports.call.history') }}",
                    data: function(d) {
                        d.refrence_id= $('#call_filter_search').val();
                        d.client_id= $('#call_filter_client').val();
                        d.salescenter_id= $('#call_filter_sales_center').val();
                    }
                },
                aaSorting: [[9, 'desc']],
                columns: [
                    {data: null},
                    {data: 'reference_id', name: 'reference_id'},
                    {data: 'call_immediately', name: 'call_immediately'},
                    {data: 'call_time', name: 'call_time'},
                    {data: 'call_type', name: 'call_type'},
                    {data: 'call_lang', name: 'call_lang'},
                    {data: 'call_type', name: 'call_type'},
                    {data: 'attempt_no', name: 'attempt_no'},
                    {data: 'dial_status', name: 'dial_status'},
                    {data: 'schedule_status', name: 'schedule_status'},
                    {data: 'id',name:'id', searchable: false, visible: false},
                    
                ],
                columnDefs: [
                    {
                        "searchable": false,
                        "orderable": false,
                        "width": "5%",
                        "targets": 0
                    }],
                'fnDrawCallback': function () {
                    var table = $('#call-history').DataTable();
                    var info = table.page.info();
                    if (info.pages > 1) {
                        $('#call-history_info')[0].style.display = 'block';
                        $('#call-history_paginate')[0].style.display = 'block';
                    } else {
                        $('#call-history_info')[0].style.display = 'none';
                        $('#call-history_paginate')[0].style.display = 'none';
                    }
                    if (info.recordsTotal < 10) {
                        $('#call-history_length')[0].style.display = 'none';
                    } else {
                        $('#call-history_length')[0].style.display = 'block';
                    }
                },
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    var table = $('#call-history').DataTable();
                    var info = table.page.info();
                    $("td:nth-child(1)", nRow).html(iDisplayIndex + 1 + info.start);
                    return nRow;
                }
            });

            $('#call_filter_search,#call_filter_client,#call_filter_sales_center').change(function() {
                leadTable.ajax.reload();
            })
            // $( "#filter_sales_center,#filter_refrence_id,#filter_channel" ).change(function() {
            //     leadTable.ajax.reload();
            // });

            // $('input[name="filter_date_lead"]').on('apply.daterangepicker', function(ev, picker) {
            //     leadTable.ajax.reload();
            // });

            // $('#filter_date').on('cancel.daterangepicker', function() {
            //     resetFilterDate(firstDay,today);
            //     leadTable.ajax.reload();
            // });

            // function resetFilterDate(startDate,endDate) 
            // {
            //     $('#filter_date').data('daterangepicker').setStartDate(startDate);
            //     $('#filter_date').data('daterangepicker').setEndDate(endDate); 
            // }

                $('#call_filter_client').change(function(){
                    client_id = $(this).val();
                    $.ajax({
                url: "{{ route('ajax.getSalesCenterAndCommodity') }}",
                type: "POST",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'client_id': client_id
                },
                success: function(res) {
                    if (res.status === true) {
                        
                        var shtml = "";
                        @if(Auth::user()->hasAccessLevels(['salescenter']))
                            shtml = '<option value="" selected>All Sales Centers</option>';
                        @endif
                        
                        var sales_center = res.data.sales_centers;
                        for (i = 0; i < sales_center.length; i++) {
                            shtml += '<option value="' + sales_center[i].id + '">' + sales_center[i].name + '</option>'
                        }
                        $('#call_filter_sales_center').html(shtml);
                    } else {
                        console.log(res.message);
                    }
                }
            })
                });
        });
    </script>

@endpush
