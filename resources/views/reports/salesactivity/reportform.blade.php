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
                                    <h1 data-toggle="collapse" href="#ShowFilter"> Sales Activity Report</h1>
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
                                                    
                                                    <form id="filter-form" role="form" action="{{route('reports.sales.activity')}}" >
                                                        <input type="hidden" name="export" value="1">
                                                        <div class="row">
                                                            <div class="col-sm-12 col-md-12 mb15 mt15 pd0" @if(!Auth::user()->can('filter-sales-activity-report'))  style="display: none" @endif>
                                                                @include('reports.filters.reset')
                                                                @include('reports.filters.status')
                                                                @include('reports.filters.commodity')
                                                                @include('reports.filters.locations')
                                                                @include('reports.filters.sales-centers')
                                                                @include('reports.filters.brands')
                                                                @include('reports.filters.clients')
                                                                @include('reports.filters.created-date')
                                                            </div>
                                                            <!--end--col-12------->
                                                            @if(Auth::user()->can('export-sales-activity-report'))
                                                            <div class="col-sm-12 col-md-12 pd0">
                                                                <div class="btnintable bottom_btns pd0 pull-right">
                                                                    
                                                                    <div class="btn-group mb15">

                                                                        <button type="button"  class="btn btn-green" id="export">Export

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
                                    <table class="table table-scroll" id="report-table">
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
            startDate: firstDay,
            endDate: today,
            maxDate: today
        });
        resetFilterDate(firstDay,today);
        
        // function getSalesCenterAndCommodities(client_id, changeEvent = false) {

           
        //     $.ajax({
        //         url: "{{ route('ajax.getSalesCenterAndCommodity') }}",
        //         type: "POST",
        //         data: {
        //             '_token': "{{ csrf_token() }}",
        //             'client_id': client_id
        //         },
        //         success: function(res) {
        //             if (res.status === true) {
        //                 var html = '<option value="" selected>All Commodity</option>';
        //                 var commodities = res.data.commodity;
        //                 for (i = 0; i < commodities.length; i++) {
        //                     html += '<option value="' + commodities[i].id + '">' + commodities[i].name + '</option>'
        //                 }
        //                 $('#commodity').html(html);

        //                 var shtml = "";
        //                 @if(!(Auth::user()->hasAccessLevels('salescenter')))
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

        // $("#client").change(function() {
        //     var client_id = $("#client").val();
        //     getSalesCenterAndCommodities(client_id, true);
        // });

        // $("#sales_center").change(function() {
        //     setLocations();
        // });

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

        var enrollmentTable = $('#report-table').DataTable({
            dom: 'tr<"bottom"lip>',
            processing: true,
            serverSide: true,
            lengthChange: true,
            ordering : true,
            ajax: {
                url: "{{ route('reports.sales.activity') }}",
                data: function(d) {
                    d.date_start= $('#date_start').val();
                    d.client= $('#client').val();
                    d.brand= $('#brand').val();
                    d.sales_center= $('#sales_center').val();
                    d.commodity= $('#commodity').val();
                    d.status= $('#status').val();
                    d.location= $('#location').val();
                }
            },
            aaSorting: [[5, 'desc']],
            columns: [
                { data: 'Brand',title: 'Brand' },
                { data: 'VendorName',title: 'VendorName' },
                { data: 'VendorNumber',title: 'VendorNumber' },
                { data: 'AgentId',title: 'AgentId' },
                { data: 'FirstName',title: 'FirstName' },
                { data: 'LastName',title: 'LastName' },
                { data: 'ReferenceId',title: 'ReferenceId' },
                { data: 'LeadDate',title: 'LeadDate' },
                { data: 'CommodityType',title: 'CommodityType' },
                { data: 'ProgramName',title: 'ProgramName' },
                { data: 'ProgramCode',title: 'ProgramCode' },
                { data: 'VerificationStatus',title: 'VerificationStatus' },
                { data: 'Channel',title: 'Channel' },
                { data: 'TpvAgentId',title: 'TpvAgentId' },
                { data: 'TpvAgentName',title: 'TpvAgentName' },
                { data: 'CallDateTime',title: 'CallDateTime' },
                { data: 'TotalCallTime',title: 'TotalCallTime' },
                { data: 'Language',title: 'Language' },
                { data: 'Disposition',title: 'Disposition' },
                { data: 'ExternalSalesId',title: 'ExternalSalesId' },
            ],
            'fnDrawCallback': function () {
                var table = $('#report-table').DataTable();
                var info = table.page.info();
                if (info.pages > 1) {
                    $('#report-table_info')[0].style.display = 'block';
                    $('#report-table_paginate')[0].style.display = 'block';
                } else {
                    $('#report-table_info')[0].style.display = 'none';
                    $('#report-table_paginate')[0].style.display = 'none';
                }
                if (info.recordsTotal < 10) {
                    $('#report-table_length')[0].style.display = 'none';
                } else {
                    $('#report-table_length')[0].style.display = 'block';
                }

                if (info.recordsTotal > 0) {
                    $('#export').show();
                } else {
                     $('#export').hide();
                }
            }
        });
        $("#client,#sales_center,#commodity,#status,#location,#brand").change(function() {
            enrollmentTable.ajax.reload();
        });

        $('#date_start').on('apply.daterangepicker', function(ev, picker) {
                enrollmentTable.ajax.reload();
        });

        $('#date_start').on('cancel.daterangepicker', function() {

            resetFilterDate(firstDay,today);
            enrollmentTable.ajax.reload();
        });

        function resetFilterDate(startDate,endDate) 
        {
            $('#date_start').data('daterangepicker').setStartDate(startDate);
            $('#date_start').data('daterangepicker').setEndDate(endDate); 
        }

        // this is for ajax datatable clicking on pagination button
        $('body').on('click','.dataTables_paginate .paginate_button',function(){     
            $('html, body').animate({
                scrollTop: $(".container").offset().top
            }, 400);
        });
        $('#client').change(function(){
          brandReportFilters($(this).val());
          $('#brand').val(null).trigger('change');
        })
    })
    
</script>
@include('reports.filters.scripts')
@endpush