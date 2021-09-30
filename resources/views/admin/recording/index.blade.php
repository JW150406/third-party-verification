@extends('layouts.admin')
@section('content')

    <?php
    $breadcrum = array(
        array('link' => '', 'text' => "Analytics")
    );
    breadcrum($breadcrum);
    ?>
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissable">
            {{ $message }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-error alert-dismissable">
            {{ $message }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="tpv-contbx">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="cont_bx3">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="client-bg-white">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h1 class="mt10">TPV Recordings</h1>
                                    </div>
                                </div>
                                <div class="message"></div>

                                <div class="sales_tablebx mt30">
                                     <div class="row mb15 " @if(!Auth::user()->hasPermissionTo('filter-recordings-report'))  style="display: none" @endif>
                                        <form>
                                        @include('reports.filters.reset')
                                        <div class="btn-group pull-right btn-sales-all">
                                            <select  id="filter_tpv_agent" class="select2 btn btn-green dropdown-toggle mr15 " role="menu">
                                                <option value="" selected>All TPV Agents</option>

                                                @foreach($tpvAgents as $tpvAgent)
                                                    <option value="{{$tpvAgent->id}}">{{$tpvAgent->full_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @include('reports.filters.brands')
                                        <div class="btn-group pull-right btn-sales-all">
                                            <select  id="filter_clients" class="select2 btn btn-green dropdown-toggle mr15 " role="menu" @if(Auth::user()->isAccessLevelToClient()) disabled @endif>
                                                @if(!Auth::user()->isAccessLevelToClient())
                                                <option value="" selected>All Clients</option>
                                                @endif
                                                @foreach(getAllClients() as $client)
                                                    @if(Auth::user()->isAccessLevelToClient())
                                                    <option value="{{$client->id}}"  selected>{{$client->name}}</option>
                                                    @else 
                                                    <option value="{{$client->id}}">{{$client->name}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="sor_fil utility-btn-group mr15">
                                            <div class="search">
                                                <div class="search-container ">
                                                    <button type="button">{!! getimage('images/search.png') !!}</button>
                                                    <input placeholder="Search" id="search_recordings" type="text" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="sor_fil utility-btn-group mr15">
                                            <div class="search">
                                                <div class="search-container date-search-container">

                                                    <button type="button">{!! getimage('images/calender.png') !!}</button>
                                                    <input placeholder="Date" id="filter_date" type="text" value="" class="" readonly>

                                                </div>
                                            </div>
                                        </div>
                                        </form>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table" id="recording-table">
                                            <thead>
                                            <tr class="list-users">
                                                <th class="sr-width">Sr. No.</th>
                                                <th>Reference ID</th>
                                                <th>Customer Name</th>
                                                <th>Customer Phone Number</th>
                                                <th>Account Number</th>
                                                <th>Client</th>
                                                <th>Brand</th>
                                                <th>Agent</th>
                                                <th>TPV Agent</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Download Status</th>
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

            $('#filter_date').daterangepicker({
                autoUpdateInput: true,
                startDate: firstDay,
                endDate: today,
                maxDate: today
            });
            resetFilterDate(firstDay,today);
            var isClientVisible = true ;
            @if(Auth::user()->hasAccessLevels(['salescenter']))
                isClientVisible = false;
            @endif

            var recordingTable = $('#recording-table').DataTable( {
                dom: 'tr<"bottom"lip>',
                processing: true,
                serverSide: true,
               // searching: false,
                searchDelay: 1000,
                lengthChange: true,
                pageLength: 10,
                ajax: {
                    url: "{{ route('admin.tpv_recording.ajax') }}",
                    data: function(d) {
                        d.client_id= $('#filter_clients').val();
                        d.brand= $('#brand').val();
                        d.tpv_agent_id= $('#filter_tpv_agent').val();
                        d.date= $('#filter_date').val();
                    }
                },
                aaSorting: [[1, 'desc']],
                columns: [
                    {data: null},
                    {data: 'refrence_id', name: 'refrence_id'},
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
                        data: 'AccountNumber',
                        title: 'Account Number',
                        name: 'telesalesdata.meta_value',
                        searchable: true
                    },
                    {data: 'clients_name', name: 'clients.name', visible: isClientVisible},
                    {data: 'brand', name: 'brand',searchable:false},
                    {data: 'sales_agent_name', name: 'sales_agent.first_name'},
                    {data: 'tpv_agent_name', name: 'tpv_agent.first_name'},
                    {data: 'date', name: 'created_at'},
                    {data: 'time',name: 'created_at'},
                    {data: 'recording_downloaded', name: 'recording_downloaded'},
                    {data: 'action',orderable:false,searchable:false},
                ],
                columnDefs: [
                    {
                        "searchable": false,
                        "orderable": false,
                        "width": "5%",
                        "targets": 0
                    }],
                'fnDrawCallback': function(){
                    var table = $('#recording-table').DataTable();
                    var info = table.page.info();
                    if(info.pages > 1){
                        $('#recording-table_info')[0].style.display = 'block';
                        $('#recording-table_paginate')[0].style.display = 'block';
                    } else {
                        $('#recording-table_info')[0].style.display = 'none';
                        $('#recording-table_paginate')[0].style.display = 'none';
                    }
                    if(info.recordsTotal < 10) {
                        $('#recording-table_length')[0].style.display = 'none';
                    } else {
                        $('#recording-table_length')[0].style.display = 'block';
                    }

                },
                "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                    var table = $('#recording-table').DataTable();
                    var info = table.page.info();
                    $("td:nth-child(1)", nRow).html(iDisplayIndex + 1 + info.start);
                    return nRow;
                }
            });
            $('#search_recordings').change(function() {
                recordingTable.search($(this).val()).draw();
            })

            $( "#filter_clients,#filter_tpv_agent,#brand" ).change(function() {
                recordingTable.ajax.reload();
            });

            
            $('#filter_date').on('apply.daterangepicker', function(ev, picker) {                
                
                recordingTable.ajax.reload();
            });
            $('#filter_date').on('cancel.daterangepicker', function(ev, picker) {
                resetFilterDate(firstDay,today);
                recordingTable.ajax.reload();
            });

            

            function resetFilterDate(startDate,endDate) 
            {
                
                $('#filter_date').data('daterangepicker').setStartDate(startDate);
                $('#filter_date').data('daterangepicker').setEndDate(endDate); 
            }
            // this is for ajax datatable clicking on pagination button
            $('body').on('click','.dataTables_paginate .paginate_button',function(){     
                
                $('html, body').animate({
                    scrollTop: $(".container").offset().top
                }, 400);
            });

            $("input,select").change(function() {
                $("#reset-filter").show();
            });

            $("#reset-filter").click(function() {
                brandReportFilters('');
                $(this).closest('form').trigger('reset');
                $(".select2").not(":disabled").prop("selectedIndex", 0).trigger('change.select2');
                
                resetFilterDate(firstDay,today);
                $('#recording-table').DataTable().ajax.reload();
                $("#reset-filter").hide();
            });
            $("#reset-filter").hide();
            $('#filter_clients').change(function(){
                brandReportFilters($(this).val())
            })

        });

        function brandReportFilters(clientId){
		$.ajax({
			type: "POST",
			url: "{{ route('reports.ajax.brands') }}",
			data: {'_token':'{{csrf_token()}}','clientId':clientId},
			success:function(data)
			{
				let brandOp = '<option value="" selected>All Brands</option>';
				let brands = data.data;
				$.each(brands,function(k,v){
					brandOp += '<option value='+v.id+'>'+v.name+'</option>';
				})
				$('#brand').html(brandOp);
			}
		})
	}

    </script>
@endpush
