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

<div class="tpv-contbx">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3">

                    <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx client-new-tabs">
                        <div class="client-bg-white">
                            <div class="row">
                                <div class="col-md-8 col-sm-8">
                                    <h1 data-toggle="collapse" href="#ShowFilter"> Enrollment Report</h1>
                                </div>
                                <div class="col-md-4 col-sm-4 report-toggle">
                                    <span data-toggle="collapse" href="#ShowFilter"></span>
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
                                                    
                                                    <form id="filter-form" role="form" action="{{route('reports.exports.enrollment')}}" >
                                                        <div class="row">
                                                            <div class="col-sm-12 col-md-12 mb15 mt15 mr15 pd0" @if(!Auth::user()->can('filter-enrollment-report'))  style="display: none" @endif>
                                                                @include('reports.filters.reset')
                                                                <div class="btn-group pull-right btn-sales-all">
                                                                    <div class="update_client_by_location">
                                                                        <select class="select2 btn btn-green dropdown-toggle selectclientlocations_report" id="status" name="status">
                                                                            <option value="" selected>Enrollment Status</option>
                                                                            <option value="verified">Enrollment</option>
                                                                            <option value="non_enrollment">Non Enrollment</option>
                                                                            <option value="pending">Pending</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                @include('reports.filters.commodity')
                                                                @include('reports.filters.locations')
                                                                @include('reports.filters.sales-centers')
                                                                @include('reports.filters.clients')
                                                                @include('reports.filters.created-date')

                                                            </div>
                                                            <!--end--col-12------->
                                                            @if(Auth::user()->can('export-enrollment-report'))
                                                            <div class="col-sm-12 col-md-12 pd0">
                                                                <div class="btnintable bottom_btns pd0 pull-right">
                                                                    
                                                                    <div class="btn-group mb15">

                                                                        <button  type="button"  class="btn btn-green" id="export">Export

                                                                        </button>
                                                                    </div>
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
                                    <table class="table" id="enrollment-table">
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
    var today,firstDay;
    $(document).ready(function() {

        
        today = new Date();
        usaTime = today.toLocaleString("en-US", {timeZone: "{{Auth::user()->timezone}}"});
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
        resetFilterDate(firstDay,today);

        $('#filter-form').on('keyup keypress', function(e) {
          var keyCode = e.keyCode || e.which;
          if (keyCode === 13) { 
            e.preventDefault();
            return false;
          }
        });
        $("#export").click(function() {
            $("#filter-form").submit();
        });
        
        getSalesCenterAndCommodities($("#client").val());
        
        var enrollmentTable = $('#enrollment-table').DataTable({
            dom: 'tr<"bottom"lip>',
            processing: true,
            serverSide: true,
            lengthChange: true,
            ordering : true,
            ajax: {
                url: "{{ route('reports.reportform') }}",
                data: function(d) {
                    d.date_start= $('#date_start').val();
                    d.client= $('#client').val();
                    d.sales_center= $('#sales_center').val();
                    d.commodity= $('#commodity').val();
                    d.status= $('#status').val();
                    d.location= $('#location').val();
                }
            },
            aaSorting: [[23, 'desc']],
            columns: [
                { data: 'EnrollmentType', title:'EnrollmentType' },
                { data: 'Company', title:'Company' },
                { data: 'Utility', title:'Utility' },
                { data: 'CommodityType', title:'CommodityType' },
                { data: 'ContractPath', title:'ContractPath' },
                { data: 'UtilityAccountNumber', title:'UtilityAccountNumber' },
                { data: 'ServiceFirstName', title:'ServiceFirstName' },
                { data: 'ServiceLastName', title:'ServiceLastName' },
                { data: 'ServiceAddress1', title:'ServiceAddress1' },
                { data: 'ServiceAddress2', title:'ServiceAddress2' },
                { data: 'ServiceCity', title:'ServiceCity' },
                { data: 'ServiceState', title:'ServiceState' },
                { data: 'ServiceZip', title:'ServiceZip' },
                { data: 'ServiceEmail', title:'ServiceEmail' },
                { data: 'ServicePhone', title:'ServicePhone' },
                { data: 'BillingFirstName', title:'BillingFirstName' },
                { data: 'BillingLastName', title:'BillingLastName' },
                { data: 'BillingAddress1', title:'BillingAddress1' },
                { data: 'BillingAddress2', title:'BillingAddress2' },
                { data: 'BillingCity', title:'BillingCity' },
                { data: 'BillingState', title:'BillingState' },
                { data: 'BillingZip', title:'BillingZip' },
                { data: 'SalesCenter', title:'SalesCenter' },
                { data: 'VerificationID', title:'VerificationID' },
                { data: 'ExternalSalesID', title:'ExternalSalesID' },
                { data: 'SalesChannel', title:'SalesChannel' },
                { data: 'SalesAgent', title:'SalesAgent' },
                { data: 'SoldDate', title:'SoldDate' },
                { data: 'TPVCall', title:'TPVCall' },
                { data: 'RateClass', title:'RateClass' },
            ],
            'fnDrawCallback': function () {
                var table = $('#enrollment-table').DataTable();
                var info = table.page.info();
                if (info.pages > 1) {
                    $('#enrollment-table_info')[0].style.display = 'block';
                    $('#enrollment-table_paginate')[0].style.display = 'block';
                } else {
                    $('#enrollment-table_info')[0].style.display = 'none';
                    $('#enrollment-table_paginate')[0].style.display = 'none';
                }
                if (info.recordsTotal < 10) {
                    $('#enrollment-table_length')[0].style.display = 'none';
                } else {
                    $('#enrollment-table_length')[0].style.display = 'block';
                }

                if (info.recordsTotal > 0) {
                    $('#export').show();
                } else {
                     $('#export').hide();
                }
            }
        });
        $("#client,#sales_center,#commodity,#status,#location").change(function() {
            enrollmentTable.ajax.reload();
        });

        $('#date_start').on('apply.daterangepicker', function(ev, picker) {
                enrollmentTable.ajax.reload();
        });

        $('#date_start').on('cancel.daterangepicker', function() {

            resetFilterDate(firstDay,today);
            enrollmentTable.ajax.reload();
        });

        // this is for ajax datatable clicking on pagination button
        $('body').on('click','.dataTables_paginate .paginate_button',function(){     
            $('html, body').animate({
                scrollTop: $(".container").offset().top
            }, 400);
        });
    })
</script>
@include('reports.filters.scripts')
@endpush