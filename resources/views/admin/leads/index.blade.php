@extends('layouts.admin')
@section('content')

    <?php
    $breadcrum = array(
        array('link' => '', 'text' => "Analytics")
        //array('link' => "", 'text' => 'Lead Detail Report')
    );
    breadcrum($breadcrum); 

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
                                        <h1 class="mt10">Lead Detail Report</h1>
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
                                    <form action="#">
                                    <div class="row mb15" style="margin-right: 0px" @if(!Auth::user()->hasPermissionTo('filter-lead-detail-report'))  style="display: none" @endif>
                                        @include('reports.filters.reset')
                                        <div class="btn-group pull-right btn-sales-all">
                                            <select  id="filter_channel" class="select2 btn btn-green dropdown-toggle mr15 " role="menu">

                                                <option value="" selected>All Channels</option>
                                                <option value="tele" >Tele</option>    
                                                <option value="d2d" >D2D</option>    
                                            </select>
                                        </div>

                                        @include('reports.filters.locations')
                                        @include('reports.filters.sales-centers')
                                        @include('reports.filters.brands')
                                        @include('reports.filters.clients')
                                        @include('reports.filters.status')
                                        <div class="sor_fil utility-btn-group">
                                            <div class="search mr15">

                                                <div class="search-container margin-bottom-for-filters" style="width: 150px">

                                                    <button type="button">{!! getimage('images/search.png') !!}</button>
                                                    <input placeholder="Lead Number, Account Number, Customer Name, Phone" id="filter_refrence_id" type="text" value="">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="sor_fil utility-btn-group">
                                            <div class="search mr15">
                                                <div class="search-container date-search-container" style="width: 185px;">

                                                    <button type="button">{!! getimage('images/calender.png') !!}</button>
                                                    <input placeholder="Date" name="filter_date_lead" id="filter_date" type="text" value="" class="" readonly>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    </form>
                                    <div class="table-responsive">
                                        <table class="table ld-report table-scroll" id="lead-table">
                                            <thead>
                                            <tr class="acjin">
                                                <th>Sr.No.</th>
                                                <th>Lead Number</th>
                                                <th>Associated Lead Number</th>
                                                <th>Status</th>
                                                <th>Lead Client</th>
                                                <th>Brand</th>
                                                <th>Sales Center</th>
                                                <th>Sales Center Location</th>
                                                <th>Sales Agent</th>
                                                <th>Agent ID</th>
                                                <th>External ID</th>
                                                <th>Date</th>
                                                <th>Sales Channel</th>
                                                <th class="text-center">Contract Package </th>
                                                <th class="text-center" style="width: 50px;">Consent Recording</th>
                                                <th class="text-center">TPV Recording</th>
                                                <th class="text-center">E-signature</th>
                                                <th class="text-center">TPV Receipt</th>
                                                <th class="text-center">Customer Name</th>
                                                <th class="text-center">Customer Phone Number</th>
                                                <th class="text-center">Customer Email</th>
                                                <th class="text-center">Account Number</th>       
                                                <th style="min-width: 60px;">Action</th>
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
    @include('admin.leads.delete-lead')
@endsection

@push('scripts')
    <script>
        var today,firstDay;
        $(document).ready(function () {
            today = new Date();
            usaTime = today.toLocaleString("en-US", {timeZone: "{{Auth::user()->timezone}}"});
            today = new Date(usaTime);
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();
            firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            today = mm + '/' + dd + '/' + yyyy;
            
            $('#filter_date').daterangepicker({
                autoUpdateInput: true,
                startDate: firstDay,
                endDate: today,
                maxDate: today
            });
            resetFilterDate(firstDay,today);
            getSalesCenterAndCommodities($("#client").val());
            var isClientVisible = true ;
            @if($isLevelSalesCenter)
                isClientVisible = false;
            @endif
            var leadTable=    $('#lead-table').DataTable({
                dom: 'tr<"bottom"lip>',
                processing: true,
                serverSide: true,
                searchDelay: 1000,
                lengthChange: true,
                ordering: true,
                searching: true,
                ajax: {
                    url: "{{ route('telesales.getLeads') }}",
                    data: function(d) {
                        d.client_id= $('#client').val();
                        d.brand= $('#brand').val();
                        d.salescenter_id= $('#sales_center').val();
                        d.agent_type= $('#filter_channel').val();
                        d.refrence_id= "";
                        d.status = $('#status').val();
                        d.date= $('#filter_date').val();
                        d.agent_type= $('#filter_channel').val();
                        d.location= $('#location').val();
                    }
                },
                aaSorting: [[15, 'desc']],
                columns: [
                    {data: null, searchable: false,orderable: false},
                    {data: 'refrence_id', name: 'telesales.refrence_id',searchable: true},
                    {data: 'multiple_parent_id', name: 'multiple_parent_id', searchable: true},
                    {data: 'status_new', name: 'status_new', searchable: false},
                    {data: 'client_name', name: 'client.name', visible: isClientVisible},
                    {data: 'brand_name', name: 'brand_name'},
                    {data: 'salescenter_name', name: 'user.salescenter.name'},
                    {data: 'location_name', name: 'user.salesAgentDetails.location.name'},
                    {data: 'agent_name', name: 'user.first_name'},
                    {data: 'userid', name: 'user.userid'},
                    {data: 'external_id', name: 'user.salesAgentDetails.external_id'},
                    {data: 'created_at', name: 'telesales.created_at'},
                    {data: 'channel', name: 'user.salesAgentDetails.agent_type',orderable: false},
                    {data: 'contract_pdf', name: 'contract_pdf',orderable: false, searchable: false},
                    {data: 'consent_recording', name: 'consent_recording',orderable: false, searchable: false},
                    {data: 's3_recording_url', name: 's3_recording_url',orderable: false, searchable: false},
                    {data: 'Esignature',title: 'E-signature', name: 'Esignature',orderable: false, searchable: false,shortable: false},
                    {data: 'tpv_receipt_pdf', name: 'tpv_receipt_pdf',orderable: false, searchable: false},
                    {
                        data: 'AuthorizedName',
                        title: 'Customer Name',
                        name: 'AuthorizedName',
                        searchable: true
                    },
                    {
                        data: 'Phone',
                        title: 'Customer Phone Number',
                        name: 'telesalesdata.meta_value',
                        searchable: true
                    },
                    {
                        data: 'CustomerEmail',
                        title: 'Customer Email',
                        name: 'telesalesdata.meta_value',
                        searchable: false
                    },
                    {
                        data: 'AccountNumber',
                        title: 'Account Number',
                        name: 'telesalesdata.meta_value',
                        searchable: true
                    },
                    {data: 'action', orderable: false, searchable: false},
                    {data: 'id',name:'telesales.id', searchable: false, visible: false}
                ],
                
                'fnDrawCallback': function () {
                    var table = $('#lead-table').DataTable();
                    var info = table.page.info();
                    if (info.pages > 1) {
                        $('#lead-table_info')[0].style.display = 'block';
                        $('#lead-table_paginate')[0].style.display = 'block';
                    } else {
                        $('#lead-table_info')[0].style.display = 'none';
                        $('#lead-table_paginate')[0].style.display = 'none';
                    }
                    if (info.recordsTotal < 10) {
                        $('#lead-table_length')[0].style.display = 'none';
                    } else {
                        $('#lead-table_length')[0].style.display = 'block';
                    }
                },
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    var table = $('#lead-table').DataTable();
                    var info = table.page.info();
                    $("td:nth-child(1)", nRow).html(iDisplayIndex + 1 + info.start);
                    return nRow;
                }
            });
            $('#filter_refrence_id').change(function() {
                leadTable.search($(this).val()).draw();
            });
            // $( "#client" ).change(function() {
            //     getSalesCenters($(this).val());
            // });

            // $("#sales_center").change(function() {
            //     setLocations();
            // });
            
            $( "#client,#sales_center,#filter_refrence_id,#filter_channel,#location,#brand,#status").change(function() {
                leadTable.ajax.reload();
            });

            $('input[name="filter_date_lead"]').on('apply.daterangepicker', function(ev, picker) {
                leadTable.ajax.reload();
            });

            $('#filter_date').on('cancel.daterangepicker', function() {
                resetFilterDate(firstDay,today);
                leadTable.ajax.reload();
            });
            
            function resetFilterDate(startDate,endDate) 
            {
                $('#filter_date').data('daterangepicker').setStartDate(startDate);
                $('#filter_date').data('daterangepicker').setEndDate(endDate); 
            }

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
            // setLocations();
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
        });
    </script>
@include('reports.filters.scripts')
@endpush
