@extends('layouts.admin')
@section('content')

    <?php
    $breadcrum = array(
        array('link' => "#", 'text' => 'Agents'),
        array('link' => "", 'text' => 'Sales Agents')
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
                                        <h1 class="mt10">All Sales Agents</h1>
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
                                </div>
                                <div class="sales_tablebx mt30">
                                    <div class="row mb15">
                                        @if(auth()->user()->hasPermissionTo('add-sales-agents'))
                                            <div class="btn-group pull-right btn-sales-all">
                                                <button type="button" class="btn btn-green pull-right mr15 dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                    More <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu employee-dropdown" role="menu">
                                                    <li>
                                                        <a href="{{ route('admin.sales.agents.bulk-upload') }}" type="button">Bulk Upload</a>
                                                    </li>
                                                </ul> 

                                                <a href="javascript:void(0)"
                                                   class="btn btn-green pull-right mr15 salesagent-modal" type="button"
                                                   data-type="new" data-title="Add New  Sales Agent">Add Sales Agent</a>  
                                            </div>
                                        @endif
                                        <div class="btn-group pull-right btn-sales-all">
                                            <select  id="filter_sales_center_location" class="select2 btn btn-green dropdown-toggle mr15 " role="menu" @if($isLevelSalesCenter) disabled @endif>
                                                @if(!$isLevelSalesCenter)
                                                    <option value="" selected>All Locations</option>
                                                @endif
                                                
                                            </select>
                                        </div> 
                                        <div class="btn-group pull-right btn-sales-all">
                                            <select  id="filter_sales_center" class="select2 btn btn-green dropdown-toggle mr15 " role="menu" @if($isLevelSalesCenter) disabled @endif>
                                                @if(!$isLevelSalesCenter)
                                                    <option value="" selected>All Sales Centers</option>
                                                @endif
                                                @foreach($salesCenters as $salesCenter)
                                                    <option value="{{$salesCenter->id}}">{{$salesCenter->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="btn-group pull-right btn-sales-all">

                                            <select  id="filter_client" name="client_id" class="select2 btn btn-green dropdown-toggle mr15 " role="menu" @if($isLevelClientAndSalesCenter) disabled @endif>
                                                @if(!$isLevelClientAndSalesCenter)

                                                    <option value="" selected>All Clients</option>
                                                @endif
                                                @foreach($clients as $client)
                                                    <option value="{{$client->id}}">{{$client->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="btn-group pull-right btn-sales-all">
                                            <div class="update_client_by_location">
                                                <select class="select2 auto-submit" name="status" id="status">
                                                    <option value="active">Active Agents</option>
                                                    <option value="inactive">Deactivated Agents</option>
                                                    <option value="all">All Agents</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="sor_fil utility-btn-group mr15">
                                            <div class="search">
                                                <div class="search-container ">

                                                    <button type="button">{!! getimage('images/search.png') !!}</button>
                                                    <input placeholder="Search" id="search_recordings" type="text" value="">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                                <div class="sales_tablebx mt30">
                                    <div class="table-responsive">
                                        <table class="table" id="agent-table">
                                            <thead>
                                            <tr class="acjin">
                                                <th>Sr.No.</th>
                                                <th></th>
                                                <th>Client</th>
                                                <th>Sales Center</th>
                                                <th>Full Name</th>
                                                <th>Location</th>
                                                <th>ID</th>
                                                <th>External ID</th>
                                                <th>Type</th>
                                                <th>Certified</th>
                                                <th>Email</th>
                                                <th class="action-width" style="min-width:100px;">Action</th>
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
    @include('client.salescenter.salesagent.salesagentspoup_new')
    @include('client.salescenter.salesagent.addsalesagentpopup')
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
                agentTable = $('#agent-table').DataTable( {
                dom: 'tr<"bottom"lip>',
                processing: true,
                serverSide: true,
                searching: true,
                lengthChange: true,
                searchDelay: 2000,
                ajax: {
                    url: "{{ route('admin.sales.agents') }}",
                    data: function(d) {
                        d.client_id= $('#filter_client').val();
                        d.salescenter_id= $('#filter_sales_center').val();
                        d.location_id= $('#filter_sales_center_location').val();
                        d.status = $('#status').val();
                    }
                },
                aaSorting: [[11, 'desc']],
                columns: [
                    {data: null},
                    {data: 'profile_picture',orderable:false,searchable:false},
                    {data: 'client_name', name: 'client.name'},
                    {data: 'salescenter_name', name: 'salescenter.name'},
                    {data: 'full_name', name: 'first_name'},
                    {data: 'location', name: 'salescenterslocations.name'},
                    {data: 'userid', name: 'userid'},
                    {data: 'external_id', name: 'salesAgentDetails.external_id'},
                    {data: 'agent_type', name: 'agent_type'},
                    {data: 'certified', name: 'certified'},
                    {data: 'email', name: 'email'},
                    {data: 'action', orderable: false, searchable: false},
                    {data: 'created_at', searchable: false, visible: false},
                    {data: 'last_name',name:'last_name',visible: false,orderable: false},
                ],
                columnDefs: [
                    {
                        "searchable": false,
                        "orderable": false,
                        "width": "5%",
                        "targets": 0
                    }],
                'fnDrawCallback': function () {
                    var table = $('#agent-table').DataTable();
                    var info = table.page.info();
                    if (info.pages > 1) {
                        $('#agent-table_info')[0].style.display = 'block';
                        $('#agent-table_paginate')[0].style.display = 'block';
                    } else {
                        $('#agent-table_info')[0].style.display = 'none';
                        $('#agent-table_paginate')[0].style.display = 'none';
                    }
                    if (info.recordsTotal < 10) {
                        $('#agent-table_length')[0].style.display = 'none';
                    } else {
                        $('#agent-table_length')[0].style.display = 'block';
                    }
                },
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    var table = $('#agent-table').DataTable();
                    var info = table.page.info();
                    $("td:nth-child(1)", nRow).html(iDisplayIndex + 1 + info.start);
                    return nRow;
                }
            }).on( 'processing.dt', function ( e, settings, processing ) {
                $(".tooltip").tooltip("hide");
            });
            $('#search_recordings').change(function() {
                agentTable.search($(this).val()).draw();
            });

            $( "#filter_client" ).change(function() {
                getSalesCenters($(this).val());
            });
            $( "#filter_sales_center" ).change(function() {
                getSalesCenterLocations();
            });
            $( "#filter_sales_center,#filter_client,#filter_sales_center_location" ).change(function() {
                agentTable.ajax.reload();
            });

            // To after filter load datatable
            $('#status').change(function() {
                agentTable.draw();
            });

            function getSalesCenters(client_id=null) {
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
                            @if(!$isLevelSalesCenter)
                                shtml = '<option value="" selected>All Sales Centers</option>';
                            @endif
                            
                            var sales_center = res.data.sales_centers;
                            for (i = 0; i < sales_center.length; i++) {
                                shtml += '<option value="' + sales_center[i].id + '">' + sales_center[i].name + '</option>'
                            }
                            $('#filter_sales_center').html(shtml);
                            
                        } else {
                            console.log(res.message);
                        }
                    }
                })
                
            }
           
            function getSalesCenterLocations() {
                let clientId = $('#filter_client').val();
                let salesCenterId = $('#filter_sales_center').val();
                $.ajax({
                    url: "{{ route('salescenter.getSalesCenterLocations') }}",
                    type: "get",
                    data: {
                        'client_id': clientId,
                        'salescenter_id': salesCenterId,
                    },
                    success: function(res) {
                        if (res.status === 'success') {

                            var shtml = "";
                            @if(!$isLevelSalesCenter)
                                shtml = '<option value="" selected>All Locations</option>';
                            @endif

                            shtml += res.options                            
                            $('#filter_sales_center_location').html(shtml);
                            
                        }
                    }
                })
                
            }

            getSalesCenterLocations();
           // this is for ajax datatable clicking on pagination button
           $('body').on('click','.dataTables_paginate .paginate_button',function(){     
                
                $('html, body').animate({
                    scrollTop: $(".container").offset().top
                }, 400);
            });
        });
    </script>

@endpush
