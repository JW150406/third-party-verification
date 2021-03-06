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
                                    <h1 data-toggle="collapse" href="#ShowFilter"> PTM Enrollment Report</h1>
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

                                                    <form id="enrollment-filter-form" role="form" action="{{route('ptmenrollment.export')}}">
                                                        <div class="row">
                                                            <div class="col-sm-12 col-md-12 mb15 mt15 mr15 pd0" @if(!Auth::user()->hasPermissionTo('filter-enrollment-report')) style="display: none" @endif>
                                                                @include('reports.filters.reset')
                                                                

                                                                {{--@include('reports.filters.commodity')--}}
                                                                @include('reports.filters.state')
                                                                @include('reports.filters.status')
                                                                @include('reports.filters.channel')
                                                                @include('reports.filters.locations')
                                                                @include('reports.filters.sales-centers')
                                                                @include('reports.filters.brands')
                                                                @include('reports.filters.clients')
                                                                @include('reports.filters.created-date')
                                                                <div class="sor_fil utility-btn-group">
                                                                    <div class="search mr15">

                                                                        <div class="search-container margin-bottom-for-filters" style="width: 360px">

                                                                            <button type="button">{!! getimage('images/search.png') !!}</button>
                                                                            <input placeholder="Lead Number, Account Number, Phone, Customer Name" id="filter_refrence_id" type="text" name="search_field" value="">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                
                                                            </div>
                                                            <!--end--col-12------->
                                                            @if(Auth::user()->hasPermissionTo('export-enrollment-report'))

                                                            <div class="col-sm-12 col-md-12 pd0  mb15">
                                                                <input type="hidden" class="export-type" name="export">
                                                                <input type="hidden" class="lead-date-class" name="date_type" value="submission">
                                                                
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
                                                                    <a href="" class="toggleColumns" type='minus'><i class="fa fa-minus-circle"></i></a>
                                                                    <!-- <div class="btn-group mb15">
                                                                        <button  type="button"  class="btn btn-green" id="export">Export
                                                                        </button>
                                                                    </div> -->
                                                                </div>

                                                            </div>
                                                            @endif
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--report generate form ends-->

                                <div class="table-responsive">
                                    <table class="table table-scroll " id="enrollment-report">
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
<div class="team-addnewmodal">
    <div class="modal fade modal-center " id="programCodeModal" tabindex="-1" role="dialog" aria-labelledby="programCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">??</span></button>
                    <h4 class="modal-title" id="myModalLabel">Program Details</h4>
                </div>
                <div class="ajax-error-message"></div>
                <div class="modal-body" id="program-data">
                    <div class="utility-outer">
                    <p class="utility-sub-t" id="commodity-name"></p>
                    <p class="utility-sub-t" id="customer-type"></p>
                        <p class="utility-sub-t" id="program-name"></p>
                        <div class="residential-table">
                            <div class="row">
                                <div class="col-md-3 col-sm-3 br2 border-right">
                                    <p>Code</p><span id="program-code"></span>
                                </div>
                                <div class="col-md-3 col-sm-3">
                                    <p>Rate</p> <span id="program-rate"></span>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <p>Term</p><span id="program-term"></span>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <p>MSF</p><span id="program-msf"></span>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <p>ETF</p><span id="program-etf"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end-modal--->

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

        // $('#enrollment-filter-form').on('keyup keypress', function(e) {
        //   var keyCode = e.keyCode || e.which;
        //   if (keyCode === 13) { 
        //     e.preventDefault();
        //     return false;
        //   }
        // });
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

        var enrollmentTable = $('#enrollment-report').DataTable({
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
                url: "{{ route('reports.ptm-enrollment-report-data') }}",
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
                }
            },
            // aaSorting: [[23, 'desc']],
            columns: [
                /*{
                    data: 'EnrollmentType',
                    title: 'Enrollment Type',
                    searchable: false
                },*/
                {
                    data: 'Client',
                    name: 'clients.name',
                    title: 'Client',
                    searchable: true
                },
                /*{
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
                    data: 'Sales Agent ID',
                    name: 'users.userid',
                    title: 'Sales Agent ID',
                    searchable: true
                },
                {
                    data: 'Channel',
                    title: 'Channel',
                    searchable: false
                },*/
                {
                    data: 'SoldDateTime',
                    name: 'telesales.created_at',
                    title: 'Sold Date & Time',
                    searchable: false
                },
                /*{
                    data: 'TPVDate',
                    title: 'TPV Date',
                    searchable: false
                },*/
                {
                    data: 'LeadID',
                    name: 'telesales.refrence_id',
                    title: 'Lead ID',
                    searchable: true
                },
                /*{
                    data: 'State',
                    name: 'zip_codes.state',
                    title: 'State',
                    searchable: true
                },*/
                {
                    data: 'Status',
                    name: 'telesales.status',
                    title: 'Status',
                    searchable: true
                },
                /*{
                    data: 'Reason',
                    title: 'Reason',
                    searchable: false
                },*/
                {
                    data: 'CustomerFirstName',
                    title: 'Customer First Name',
                    name: 'CustomerFirstName',
                    searchable: true
                },
                {
                    data: 'CustomerMiddleName',
                    title: 'Customer Middle  Name',
                    name: 'CustomerMiddleName',
                    searchable: true
                },
                {
                    data: 'CustomerLastName',
                    title: 'Customer Last Name',
                    name: 'CustomerLastName',
                    searchable: true
                },
                {
                    data: 'Esignature',
                    title: 'E-signature',
                    searchable: false,
                    orderable: false,
                    sortable : false
                },
                {
                    data: 'IPAddress',
                    title: 'IP-Address',
                    searchable: false,
                    orderable: false,
                    sortable : false
                },
                {
                    data: 'IPAddress_tmp',
                    title: 'IP-Address temp',
                    searchable: false,
                    orderable: false,
                    sortable : false
                },
                {
                    data: 'AccountNumber',
                    title: 'Account Number',
                    name: 'AccountNumber',
                    searchable: true
                },
                /*{
                    data: 'Utility',
                    title: 'Utility',
                    searchable: false
                },
                {
                    data: 'Commodity',
                    title: 'Commodity',
                    searchable: false
                },
                {
                    data: 'ServiceAddress1',
                    title: 'Service Address1',
                    searchable: false
                },
                {
                    data: 'ServiceAddress2',
                    title: 'Service Address2',
                    searchable: false
                },
                {
                    data: 'ServiceCity',
                    title: 'Service City',
                    searchable: false
                },
                {
                    data: 'ServiceState',
                    title: 'Service State',
                    searchable: false
                },
                {
                    data: 'ServiceZipcode',
                    title: 'Service Zipcode',
                    searchable: false
                },
                {
                    data: 'Email',
                    title: 'Email',
                    searchable: false
                },*/
                {
                    data: 'Phone',
                    title: 'Phone',
                    name: 'Phone',
                    searchable: true
                },
                /*
                {
                    data: 'BillingFirstName',
                    title: 'Billing First Name',
                    searchable: false
                },
                {
                    data: 'BillingMiddleName',
                    title: 'Billing Middle Name',
                    searchable: false
                },
                {
                    data: 'BillingLastName',
                    title: 'Billing Last Name',
                    searchable: false
                },
                {
                    data: 'BillingAddress1',
                    title: 'Billing Address1',
                    searchable: false
                },
                {
                    data: 'BillingAddress2',
                    title: 'Billing Address2',
                    searchable: false
                },
                {
                    data: 'BillingCity',
                    title: 'Billing City',
                    searchable: false
                },
                {
                    data: 'BillingState',
                    title: 'Billing State',
                    searchable: false
                },
                {
                    data: 'BillingZipcode',
                    title: 'Billing Zipcode',
                    name:'zip_codes.zipcode',
                    searchable: false
                },*/
                {
                    data: 'Programs',
                    title: 'Programs',
                    searchable: false
                },
                /*{
                    data: 'Method',
                    title: 'Method',
                    searchable: false
                },
                {
                    data: 'Language',
                    title: 'Language',
                    searchable: false
                },
                {
                    data: 'TPV Call ID',
                    title: 'TPV Call ID',
                    name:'telesales.call_id',
                    searchable: true
                },
                {
                    data: 'TPVAgent',
                    title: 'TPV Agent',
                    name: 'users.first_name',
                    searchable: true
                },*/
                {
                    data: 'Assigned_date',
                    title: 'Assigned Date',
                    // name: 'assigned_date',
                    searchable: true
                },
                {
                    data: 'Assigned_kw',
                    title: 'Assigned KW',
                    name: 'assigned_kw',
                    searchable: true
                },
                {
                    data: 'Update_by',
                    title: 'Last Updated By',
                    name: 'updated_by',
                    searchable: true
                },
                {
                    data: 'action_status',
                    title: 'View Status Updates',
                    searchable: false
                },
                {
                    data: 'action',
                    title: 'Action',
                    searchable: false
                }
            ],
            'fnDrawCallback': function() {
                var table = $('#enrollment-report').DataTable();
                var info = table.page.info();
                if (info.pages > 1) {
                    $('#enrollment-report_info')[0].style.display = 'block';
                    $('#enrollment-report_paginate')[0].style.display = 'block';
                } else {
                    $('#enrollment-report_info')[0].style.display = 'none';
                    $('#enrollment-report_paginate')[0].style.display = 'none';
                }
                if (info.recordsTotal < 10) {
                    $('#enrollment-report_length')[0].style.display = 'none';
                } else {
                    $('#enrollment-report_length')[0].style.display = 'block';
                }

                if (info.recordsTotal > 0) {
                    $('.exportBtn').show();
                } else {
                    $('.exportBtn').hide();
                }
            }
        });
        $("#client,#sales_center,#commodity,#status,#location,#channel,#filter_refrence_id,#filter_account_number,#state,#brand").change(function() {
            enrollmentTable.ajax.reload();
        });
        $("#client,#sales_center,#commodity,#status,#location,#channel,#filter_refrence_id,#filter_account_number,#brand").change(function() {
            loadStateData();
        });
        $('#filter_refrence_id').change(function() {
            enrollmentTable.search($(this).val()).draw();
        });
        $('#filter_account_number').change(function() {
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

        $('.toggleColumns').on('click', function(e) {
            if ($(this).attr('type') == 'plus') {
                $(this).attr('type', 'minus');
                $(this).html('<i class="fa fa-minus-circle"></i>');
            } else {
                $(this).attr('type', 'plus');
                $(this).html('<i class="fa fa-plus-circle"></i>');
            }
            var table = $('#enrollment-report').DataTable();

            e.preventDefault();
            // Get the column API object
            for (i = 13; i < table.columns().header().length; i++) {
                var column = table.column(i);
                column.visible(!column.visible());
            }

            // Toggle the visibility
        });
        $('#enrollment-report').on('click', 'tbody td a', function() {

            let programId  = $(this).attr('p-id');
            $.ajax({
                type:'GET',
                url: "{{ route('reports.ajax.program-details') }}",
                data: {
                    'pId': programId,
                    },
                success:function(response)
                {
                    $("#program-data").html(response.data);
                }
            });
        })
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
@include('reports.ptm_enrollment_report.view-status-update')
@endpush