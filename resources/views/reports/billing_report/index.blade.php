@extends('layouts.admin')

@section('content')
<?php
$breadcrum = array();
$breadcrum[] = array('link' => '', 'text' => "Analytics");
breadcrum($breadcrum);
?>


<?php
$request = Request::all();

?>

<style>
    .report-date-range{
    display: block;
}
</style>

<div class="tpv-contbx">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3">

                    <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx client-new-tabs">
                        <div class="client-bg-white">
                            <div class="row">
                                <div class="col-md-8 col-sm-8">
                                    <h1> Billing Duration Report</h1>
                                </div>
                            </div>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <!--report generate form -->
                                <div id="ShowFilter" class="panel-collapse collapse in ">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12">

                                            <div class="agent-detailform1">
                                                <div class="col-xs-12">
                                                
                                                    <form id="enrollment-filter-form" role="form" action="{{route('billing.export')}}">
                                                        <input type="hidden" class="export-type" name="export">
                                                        <input type="hidden" class="lead-date-class"  name="date_type" value="submission">
                                                        <div class="row">
                                                            <div class="col-sm-12 col-md-12 mb15 mt15 mr15 pd0">
                                                                @include('reports.filters.reset')
                                                                @include('reports.filters.verification_method')
                                                                @include('reports.filters.state')
                                                                @include('reports.filters.status')
                                                                @include('reports.filters.channel')
                                                                @include('reports.filters.locations')
                                                                @include('reports.filters.sales-centers')
                                                                @include('reports.filters.brands')
                                                                @include('reports.filters.clients')
                                                                <div class="sor_fil utility-btn-group">
                                                                    <div class="search mr15">
                                                                        <div class="search-container margin-bottom-for-filters" style="width: 130px">
                                                                            <button type="button">{!! getimage('images/search.png') !!}</button>
                                                                            <input placeholder="Lead Number" id="filter_refrence_id" type="text" value="" name="leadId">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @include('reports.filters.created-date')

                                                            <!--end--col-12------->
                                                            <div class="col-sm-12 col-md-12 pd0  mb15">
                                                                                                                               
                                                                <div class="btnintable bottom_btns exportBtn pd0 pull-right">

                                                                    <button type="button" class="btn btn-green dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                                        Export <span class="caret" style="margin-left:4px;"></span>
                                                                    </button>
                                                                    <ul class="dropdown-menu upload-script-menu" role="menu" style="right:30px; min-width:87px;">

                                                                        <li><a href="javascript:void(0)" type="button" name="csv" class="export" id="csv"> CSV</a>
                                                                        </li>
                                                                        <li><a href="javascript:void(0)" type="button" class="export" id="xlsx" name="xlsx"> XLSX</a>
                                                                        </li>
                                                                    </ul>
                                                                    <!-- <a href="" class="toggleColumns" type='minus'><i class="fa fa-minus-circle"></i></a> -->
                                                                    <!-- <div class="btn-group mb15">
                                                                        <button  type="button"  class="btn btn-green" id="export">Export
                                                                        </button>
                                                                    </div> -->
                                                                </div>
                                                                @include('reports.filters.hidden')
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--report generate form ends-->

                                <div class="table-responsive">
                                    <table class="table table-scroll " id="billing-report">
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--end--bg-white-area-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
        //var now = moment(today).subtract(1, 'months').format('MM/DD/YYYY');
        $('#date_start').daterangepicker({
            autoUpdateInput: true,
            startDate: firstDay,
            endDate: today,
            maxDate: today
        });
        resetFilterDate(firstDay, today);
        loadStateData();
        $(".export").click(function() {
            $('.export-type').val($(this).attr('id'));
            $("#enrollment-filter-form").submit();
        });

        $("input[name='lead-date-type']").change(function(){
            // alert($(this).val());
            $('.lead-date-class').attr('value',$(this).val());
        });
        getSalesCenterAndCommodities($("#client").val());

        var enrollmentTable = $('#billing-report').DataTable({
            dom: 'Rtr<"bottom"lip>',
            processing: true,
            serverSide: true,
            lengthChange: true,
            searchDelay: 1000,
            ordering: true,
            searching: true,
            // 'scrollX':true,
            scroller: true,
            colReorder: {
                allowReorder: false
            },
            ajax: {
                url: "{{ route('show.billing.report') }}",
                method: "post",
                data: function(d) {
                    d._token = '{{csrf_token()}}';
                    d.submitDate = $('#date_start').val();
                    d.client = $('#client').val();
                    d.brand = $('#brand').val();
                    d.salesCenter = $('#sales_center').val();
                    d.status = $('#status').val();
                    d.location = $('#location').val();
                    d.channel = $('#channel').val();
                    d.leadDateType = $('.lead-date-class').val();
                    d.state = $('#state').val();
                    d.method = $('#verification_method').val();
                    d.hidden = $('#hidden').prop("checked") ? "true" : "" ;
                }
            },
            aaSorting: [[9, 'asc']],
            columns: [
                {
                    data: 'Client',
                    name: 'clients.name',
                    title: 'Client',
                    searchable: true
                },
                {
                    data: 'Brand',
                    title: 'Brand',
                    searchable: false
                },
                {
                    data: 'SalesCenter',
                    name: 'salescenters.name',
                    title: 'Sales Center',
                    searchable: true
                },
                {
                    data: 'SalesCenterLocation',
                    name: 'salescenterslocations.name',
                    title: 'Sales Center Location',
                    searchable: true
                },
                {
                    data: 'SalesAgent',
                    name: 'users.first_name',
                    title: 'Sales Agent',
                    searchable: true
                },
                {
                    data: 'Channel',
                    title: 'Channel',
                    searchable: false
                },
                {
                    data: 'State',
                    name: 'zip_codes.state',
                    title: 'State',
                    searchable: true
                },
                {
                    data: 'Status',
                    name: 'telesales.status',
                    title: 'Status',
                    searchable: true
                },
                {
                    data: 'SoldDateTime',
                    name: 'telesales.created_at',
                    title: 'Sold Date & Time',
                    searchable: false
                },
                {
                    data: 'CallDateTime',
                    name: 'twilio_lead_call_details.created_at',
                    title: 'Call Date & Time',
                    searchable: false
                },
                {
                    data: 'LeadID',
                    name: 'telesales.refrence_id',
                    title: 'Lead ID',
                    searchable: true
                },
                {
                    data: 'Method',
                    title: 'Method',
                    searchable: false
                },
                {
                    data: 'TPVDate',
                    title: 'TPV Date',
                    searchable: false
                },
                {
                    data: 'WorkerCallId',
                    title: 'TPV Call Id',
                    name:'twilio_lead_call_details.worker_call_id',
                    searchable: false
                },
                {
                    data: 'TPVAgent',
                    title: 'TPV Agent',
                    name: 'users.first_name',
                    searchable: true
                },
                
                {
                    data: 'CallDuration',
                    title: 'TPV Call Duration (Seconds)',
                    name:'twilio_lead_call_details.call_duration',
                    searchable: true
                },
                {
                    data: 'RecordingUrl',
                    title: 'TPV Recording Link',
                    name:'twilio_lead_call_details.recording_url',
                    searchable: true
                },
            ],
            'fnDrawCallback': function() {
                var table = $('#billing-report').DataTable();
                var info = table.page.info();
                if (info.pages > 1) {
                    $('#billing-report_info')[0].style.display = 'block';
                    $('#billing-report_paginate')[0].style.display = 'block';
                } else {
                    $('#billing-report_info')[0].style.display = 'none';
                    $('#billing-report_paginate')[0].style.display = 'none';
                }
                if (info.recordsTotal < 10) {
                    $('#billing-report_length')[0].style.display = 'none';
                } else {
                    $('#billing-report_length')[0].style.display = 'block';
                }

                if (info.recordsTotal > 0) {
                    $('.exportBtn').show();
                } else {
                    $('.exportBtn').hide();
                }
            }
        });
        $("#client,#sales_center,#commodity,#status,#location,#channel,#filter_refrence_id,#state,#brand,#verification_method").change(function() {
            enrollmentTable.ajax.reload();
        });
        $("#hidden").click(function() {
            enrollmentTable.ajax.reload();
        });
        $("#client,#sales_center,#commodity,#status,#location,#channel,#filter_refrence_id,#brand,#verification_method").change(function() {
            loadStateData();
        });
        $('#filter_refrence_id').change(function() {
            enrollmentTable.search($(this).val()).draw();
        })

        $('#date_start').on('apply.daterangepicker', function(ev, picker) {
            loadStateData();
            enrollmentTable.ajax.reload();
        });

        $('#date_start').on('cancel.daterangepicker', function() {

            loadStateData();
            resetFilterDate(firstDay, today);
            enrollmentTable.ajax.reload();
        });

        // this is for ajax datatable clicking on pagination button
        $('body').on('click', '.dataTables_paginate .paginate_button', function() {
            $('html, body').animate({
                scrollTop: $(".container").offset().top
            }, 400);
        });

        // $('.toggleColumns').on('click', function(e) {
        //     if ($(this).attr('type') == 'plus') {
        //         $(this).attr('type', 'minus');
        //         $(this).html('<i class="fa fa-minus-circle"></i>');
        //     } else {
        //         $(this).attr('type', 'plus');
        //         $(this).html('<i class="fa fa-plus-circle"></i>');
        //     }
        //     var table = $('#billing-report').DataTable();

        //     e.preventDefault();
        //     // Get the column API object
        //     for (i = 13; i < table.columns().header().length; i++) {
        //         var column = table.column(i);
        //         column.visible(!column.visible());
        //     }

        //     // Toggle the visibility
        // });
    });

    function loadStateData() {
        $.ajax({
            type: "GET",
            url: "{{ route('reports.ajax.state') }}",
            data: {
                'submitDate': $('#date_start').val(),
                'client': $('#client').val(),
                'salesCenter': $('#sales_center').val(),
                'status': $('#status').val(),
                'location': $('#location').val(),
                'channel': $('#channel').val(),
                'state': $('#state').val(),
                'leadDateType' : $('.lead-date-class').val()

            },

            success: function(data) {
                option = "<option value='' selected>All States</option>";
                $.each(data.data, function(k, v) {
                    if (v != null) {
                        option += "<option value=" + v + ">" + v + "</option>";
                    }
                });
                $('#state').html(option);
            }
        });
    }
    $('#client').change(function(){
        brandReportFilters($(this).val());
        $('#brand').val(null).trigger('change');
    })
</script>
@include('reports.filters.scripts')
@endpush