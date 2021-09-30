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
 
    .table-responsive table tfoot tr th{
        background-color: #20497c;
        padding-top: 5px !important;
        padding-bottom: 5px !important;
        border-top: 0 !important;
    }
    .table-responsive table tfoot tr th,
    .table-responsive table tfoot tr td {
        color: #fff !important;
    }

    .table-responsive table tr th{
        text-align:center !important;
    }
    .table-responsive table tr td{
        text-align:center !important;
    }
    .table-responsive table tfoot tr th:first-child {
        padding-left: 5px !important;
    }
    .table-responsive table tfoot tr.odd {
        background-color: #f4f5f9 !important;
    }
    .table-responsive table {
        border: none !important;
    }
    .table-responsive table tfoot tr td {
        border-bottom: none !important;
    }
    .table-responsive .table tfoot tr:nth-child(even) {
        background-color: #f4f5f9 !important;
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
                                    <h1> TPV Calls Received </h1>
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
                                                
                                                    <form id="calldetails-filter-form" role="form" action="{{route('calldetails.export')}}">
                                                        <input type="hidden" class="export-type" name="export">
                                                        <input type="hidden" class="lead-date-class"  name="date_type" value="submission">
                                                        <div class="row">
                                                            <div class="col-sm-12 col-md-12 mb15 mt15 mr15 pd0">
                                                                @include('reports.filters.reset')                                                                
                                                                @include('reports.filters.clients')
                                                                
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
                                                                </div>
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
                                    <table class="table table-scroll " id="call-details-report">
                                            <tfoot>
                                                    <tr>
                                                        <th>Average Calls Per Day</th>
                                                        {{-- <th></th> --}}
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
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


        today = mm + '/' + dd + '/' + yyyy;
        firstDay = new Date();
        var newdate = new Date(today);
        firstDay.setDate(newdate.getDate() - 14);
        
        //var now = moment(today).subtract(1, 'months').format('MM/DD/YYYY');
        $('#date_start').daterangepicker({
            autoUpdateInput: true,
            startDate: firstDay,
            endDate: today,
            maxDate: today
        });
        resetFilterDate(firstDay, today);
        
        $(".export").click(function() {
            $('.export-type').val($(this).attr('id'));
            $("#calldetails-filter-form").submit();
        });

        $("input[name='lead-date-type']").change(function(){
            // alert($(this).val());
            $('.lead-date-class').attr('value',$(this).val());
        });
        

        var callDetailsTable = $('#call-details-report').DataTable({
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
                url: "{{ route('show.calldetails.report') }}",
                method: "get",
                data: function(d) {
                    d._token = '{{csrf_token()}}';
                    d.submitDate = $('#date_start').val();
                    d.client = $('#client').val();                    
                }
            },
            // aaSorting: [[9, 'asc']],
            columns: [
                {
                    data: 'created_date',
                    name: 'created_date',
                    title: 'Date',
                    searchable: true,
                    width:'25%'
                },
                // {
                //     data: 'a8AM',
                //     name: 'a8AM',
                //     title: '8am',
                //     searchable: true,
                //     width:'8%'
                // },
                {
                    data: 'a9AM',
                    name: 'a9AM',
                    title: '9am',
                    searchable: true,
                    width:'8%'
                },
                {
                    data: 'a10AM',
                    name: 'a10AM',
                    title: '10am',
                    searchable: true,
                    width:'8%'
                },  
                {
                    data: 'a11AM',
                    name: 'a11AM',
                    title: '11am',
                    searchable: true,
                    width:'8%'
                },  
                {
                    data: 'a12PM',
                    name: 'a12PM',
                    title: '12pm',
                    searchable: true,
                    width:'8%'
                },  
                {
                    data: 'a1PM',
                    name: 'a1PM',
                    title: '1pm',
                    searchable: true,
                    width:'8%'
                },  
                {
                    data: 'a2PM',
                    name: 'a2PM',
                    title: '2pm',
                    searchable: true,
                    width:'8%'
                },
                {
                    data: 'a3PM',
                    name: 'a3PM',
                    title: '3pm',
                    searchable: true,
                    width:'8%'
                },
                {
                    data: 'a4PM',
                    name: 'a4PM',
                    title: '4pm',
                    searchable: true,
                    width:'8%'
                },
                {
                    data: 'a5PM',
                    name: 'a5PM',
                    title: '5pm',
                    searchable: true,
                    width:'8%'
                },
                {
                    data: 'a6PM',
                    name: 'a6PM',
                    title: '6pm',
                    searchable: true,
                    width:'8%'
                },
                {
                    data: 'a7PM',
                    name: 'a7PM',
                    title: '7pm',
                    searchable: true,
                    width:'8%'
                },
                {
                    data: 'a8PM',
                    name: 'a8PM',
                    title: '8pm',
                    searchable: true,
                    width:'8%'
                },
                {
                    data: 'a9PM',
                    name: 'a9PM',
                    title: '9pm',
                    searchable: true,
                    width:'8%'
                },
                {
                    data: 'a10PM',
                    name: 'a10PM',
                    title: '10pm',
                    searchable: true,
                    width:'8%'
                },
                {
                    data: 'a11PM',
                    name: 'a11PM',
                    title: '11pm',
                    searchable: true,
                    width:'8%'
                },
                {
                    data: 'totalCalls',
                    name: 'totalCalls',
                    title: 'Total Calls',
                    searchable: true,
                    width:'8%'
                },
                {
                    data: 'average_call_per_hour',
                    name: 'average_call_per_hour',
                    title: 'Average Calls Per Hour',
                    searchable: false,
                    orderable:false,
                    width:'20%'
                },
            ],            
            "footerCallback": function(tfoot, data, start, end, display) {
                var api = this.api();               
                var allT

                for (var i = 0; i < api.columns().count(); i++) {
                    // total column
                    var columnDataTotal = api
                        .column(i)
                        .data();
                    var theColumnTotal = columnDataTotal
                        .reduce(function(a, b) {
                            if (isNaN(a)) {
                                return '';
                            } else {
                                a = parseFloat(a);
                            }
                            if (isNaN(b)) {
                                return '';
                            } else {
                                b = parseFloat(b);
                            }
                            return a + b;
                        }, 0);
                    // view page column
                    var columnData = api
                        .column(i, {
                            page: 'current'
                        })
                        .data();
                    var theColumnPage = columnData
                        .reduce(function(a, b) {
                            if (isNaN(a)) {
                                return '';
                            } else {
                                a = parseFloat(a);
                            }
                            if (isNaN(b)) {
                                return '';
                            } else {
                                b = parseFloat(b);
                            }
                            return a + b;
                        }, 0);
                     console.log(theColumnPage);
                     console.log(columnData);
                     console.log(columnData.count());
                    // Update footer
                    $(api.column(0).footer()).html('Avarage Calls Per Day');
                    $(api.column(i).footer()).html(
                        parseFloat(theColumnPage / columnData.count()).toFixed(2)
                    );
                }
            },
            'fnDrawCallback': function() {
                    var table = $('#call-details-report').DataTable();
                    var info = table.page.info();
                    if (info.pages > 1) {
                        $('#call-details-report_info')[0].style.display = 'block';
                        $('#call-details-report_paginate')[0].style.display = 'block';
                    } else {
                        $('#call-details-report_info')[0].style.display = 'none';
                        $('#call-details-report_paginate')[0].style.display = 'none';
                    }
                    if (info.recordsTotal < 10) {
                        $('#call-details-report_length')[0].style.display = 'none';
                    } else {
                        $('#call-details-report_length')[0].style.display = 'block';
                    }

                    if (info.recordsTotal > 0) {
                        $('.exportBtn').show();
                    } else {
                        $('.exportBtn').hide();
                    }
            }          
        });
        $("#client").change(function() {
            callDetailsTable.ajax.reload();
        });

        $('#date_start').on('apply.daterangepicker', function(ev, picker) {
            callDetailsTable.ajax.reload();
        });

        $('#date_start').on('cancel.daterangepicker', function() {            
            resetFilterDate(firstDay, today);
            callDetailsTable.ajax.reload();
        });

        // this is for ajax datatable clicking on pagination button
        $('body').on('click', '.dataTables_paginate .paginate_button', function() {
            $('html, body').animate({
                scrollTop: $(".container").offset().top
            }, 400);
        });

    });

</script>
@include('reports.filters.scripts')
@endpush