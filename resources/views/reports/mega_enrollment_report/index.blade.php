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
                                    <h1 data-toggle="collapse"> Daily Verified Calls Report - Mega Energy</h1>
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
                                                    <input type="hidden" id="client" value="{{config('constants.CLIENT_MEGA_ENERGY_ID')}}">
                                                    <form id="enrollment-filter-form" role="form" action="{{route('megaenrollment.export')}}">
                                                        <div class="row">
                                                            <div class="col-sm-12 col-md-12 mb15 mt15 mr15 pd0" @if(!Auth::user()->hasPermissionTo('filter-enrollment-report')) style="display: none" @endif>
                                                                @include('reports.filters.reset')
                                                                <!-- <div class="btn-group pull-right btn-sales-all">
                                                                    <div class="update_client_by_location">
                                                                        <select class="select2 btn btn-green dropdown-toggle selectclientlocations_report" id="status" name="status">
                                                                            <option value="" selected>Enrollment Status</option>
                                                                            <option value="verified">Enrollment</option>
                                                                            <option value="non_enrollment">Non Enrollment</option>
                                                                            <option value="pending">Pending</option>
                                                                        </select>
                                                                    </div>
                                                                </div> -->

                                                                @include('reports.filters.state')
                                                                @include('reports.filters.sales-agent')
                                                                @include('reports.filters.locations')
                                                                @include('reports.filters.sales-centers')
                                                                
                                                                
                                                                <div class="sor_fil utility-btn-group">
                                                                    <div class="search mr15">

                                                                        <div class="search-container margin-bottom-for-filters" style="width: 130px">

                                                                            <button type="button">{!! getimage('images/search.png') !!}</button>
                                                                            <input placeholder="Lead Number" id="filter_refrence_id" type="text" value="" name="leadId">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @include('reports.filters.created-date')

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
                                                                    <!-- <a href="" class="toggleColumns" type='minus'><i class="fa fa-minus-circle"></i></a> -->
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
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
        getSalesCenterAndCommodities("{{config()->get('constants.CLIENT_MEGA_ENERGY_ID')}}");  

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
                url: "{{ route('reports.mega-enrollment-report-data') }}",
                method: "post",
                data: function(d) {
                    d._token = '{{csrf_token()}}';
                    d.submitDate = $('#date_start').val();
                    d.salesCenter = $('#sales_center').val();
                    d.location = $('#location').val();
                    d.leadDateType = $('.lead-date-class').val();
                    d.state = $('#state').val();
                    d.salesAgent = $('#sales_agent').val();
                    d.leadId = $('#filter_refrence_id').val();
                }
            },
            // aaSorting: [[23, 'desc']],
            columns: [{
                    data: 'Date',
                    name: 'SoldDateTime',
                    title: 'Date',
                    searchable: false,
                },
                {
                    data: 'Time',
                    name: 'SoldDateTime',
                    title: 'Time',
                    searchable: true,
                },
                {
                    data: 'AuthorizedName',
                    name: 'AuthorizedName',
                    title: 'First Name',
                    searchable: true
                },
                {
                    data: 'ServiceLastName',
                    name: 'ServiceLastName',
                    title: 'Last Name',
                    searchable: true
                },
                {
                    data: 'AccountNumber',
                    name: 'AccountNumber',
                    title: 'Acct Number',
                    searchable: true
                },
                {
                    data: 'Service Address',
                    name: 'ServiceAddress1, ServiceAddress2',
                    title: 'Service Address',
                    searchable: true
                },
                {
                    data: 'SalesAgentID',
                    name: 'SalesAgentID',
                    title: 'Agent Code',
                    searchable: true
                },
                {
                    data: 'SalesAgent',
                    name: 'SalesAgent',
                    title: 'Agent Name',
                    searchable: true
                },
                {
                    data: 'Commodity',
                    name: 'Commodity',
                    title: 'Commodity',
                    searchable: true,
                    sortable: false
                },
                {
                    data: 'Term',
                    name: 'Term',
                    title: 'Term',
                    searchable: true
                },
                {
                    data: 'Rate',
                    name: 'Rate',
                    title: 'Rate',
                    searchable: true
                },
                {
                    data: 'Status',
                    name: 'Status',
                    title: 'Status',
                    searchable: true
                },
                {
                    data: 'SalesCenter',
                    name: 'SalesCenter',
                    title: 'Vendor',
                    searchable: true
                },
                {
                    data: 'Utility',
                    name: 'Utility',
                    title: 'Utility',
                    searchable: true
                },
                {
                    data: 'Phone',
                    name: 'Phone',
                    title: 'Customer Phone Number',
                    searchable: true
                },
                {
                    data: 'Customer Class',
                    title: 'Customer Class',
                    searchable: true,
                    sortable: false,
                },
                {
                    data: 'Programs',
                    name: 'Programs',
                    title: 'Product Type',
                    searchable: true
                },
                {
                    data: 'Confirmation Number',
                    name: 'LeadID',
                    title: 'Confirmation Number',
                    searchable: true
                },
                {
                    data: 'Complete',
                    title: 'Complete',
                    searchable: true,
                    sortable: false,
                },
                {
                    data: 'Verified',
                    title: 'Verified',
                    searchable: true,
                    sortable: false,
                },
                {
                    data: 'Canceled',
                    title: 'Canceled',
                    searchable: true,
                    sortable: false,
                },
                {
                    data: 'Comments',
                    title: 'Comments',
                    searchable: true,
                    sortable: false,
                },
                {
                    data: 'Email',
                    name: 'Email',
                    title: 'Customer Email',
                    searchable: true
                },
                {
                    data: 'ServiceAddress1',
                    name: 'ServiceAddress1',
                    title: 'Service Address',
                    searchable: true
                },
                {
                    data: 'ServiceAddress2',
                    name: 'ServiceAddress2',
                    title: 'Service Address Line 2',
                    searchable: true
                },
                {
                    data: 'ServiceCity',
                    name: 'ServiceCity',
                    title: 'Service City',
                    searchable: true
                },
                {
                    data: 'ServiceState',
                    name: 'ServiceState',
                    title: 'Service State',
                    searchable: true
                },
                {
                    data: 'Service Zip',
                    name: 'ServiceZip',
                    title: 'Service Zip',
                    searchable: true
                },
                {
                    data: 'ServiceCounty',
                    name: 'ServiceCounty',
                    title: 'Service County',
                    searchable: true
                },
                {
                    data: 'BillingAddress1',
                    name: 'BillingAddress1',
                    title: 'Billing Address',
                    searchable: true
                },{
                    data: 'BillingAddress2',
                    name: 'BillingAddress2',
                    title: 'Billing Address Line 2',
                    searchable: true
                },
                {
                    data: 'BillingCity',
                    name: 'BillingCity',
                    title: 'Billing City',
                    searchable: true
                },
                {
                    data: 'BillingState',
                    name: 'BillingState',
                    title: 'Billing State',
                    searchable: true
                },
                {
                    data: 'Billing Zip',
                    name: 'BillingZip',
                    title: 'Billing Zip',
                    searchable: true
                },
                {
                    data: 'BillingCounty',
                    name: 'BillingCounty',
                    title: 'Billing County',
                    searchable: true
                },
                {
                    data: 'NameKey',
                    name: 'NameKey',
                    title: 'Name Key',
                    searchable: true
                },
                {
                    data: 'BillingCycleNumber',
                    name: 'BillingCycleNumber',
                    title: 'Billing Cycle',
                    searchable: true
                },
                {
                    data: 'Date Of Birth',
                    title: 'Date Of Birth',
                    searchable: true,
                    sortable: false,
                },
                {
                    data: 'MeterNumber',
                    name: 'MeterNumber',
                    title: 'Meter Number',
                    searchable: true,
                    sortable: true,
                },
            ],
            'fnDrawCallback': function() {
                $.fn.dataTable.ext.errMode = 'none';
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
        $("#sales_center,#location,#filter_refrence_id,#state,#sales_agent").change(function() {
            enrollmentTable.ajax.reload();
        });
        $("#sales_center,#location,#filter_refrence_id,#sales_agent").change(function() {
            loadStateData();
        });
        // $('#filter_refrence_id').change(function() {
        //     enrollmentTable.search($(this).val()).draw();
        // })

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
        //     var table = $('#enrollment-report').DataTable();

        //     e.preventDefault();
        //     // Get the column API object
        //     for (i = 13; i < table.columns().header().length; i++) {
        //         var column = table.column(i);
        //         column.visible(!column.visible());
        //     }

        //     // Toggle the visibility
        // });
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
                'salesCenter': $('#sales_center').val(),
                'location': $('#location').val(),
                'state': $('#state').val(),
                'leadDateType' : $('.lead-date-class').val(),
                'salesAgent' : $('#sales-agent').val()

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
    // $('#sales-agent').change(function(){
    //     brandReportFilters($(this).val())
    // })
</script>
@include('reports.filters.scripts')
@endpush