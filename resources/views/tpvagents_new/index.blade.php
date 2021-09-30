@extends('layouts.admin')
@section('content')

    <?php
    $breadcrum = array(
        array('link' => "#", 'text' => 'Agents'),
        array('link' => "", 'text' => 'TPV Agents')
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
                                    <div class="col-xs-3">
                                        <h1>TPV Agents</h1>
                                    </div>
                                    @if(auth()->user()->can('add-tpv-agents'))
                                        <div class="col-xs-9">
                                            <a href="#" class="btn btn-green pull-right mr15 tpv-agent-modal"
                                               data-toggle="modal" data-original-title="Add TPV Agent" data-type="new">Add
                                                TPV Agent</a>
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
                                    @endif
                                </div>

                                <div class="users-outer-tab mt30">
                                    <div class="clearfix"></div>
                                    <div class="message">
                                        @if ($message = Session::get('success'))
                                            <div class="alert alert-success alert-dismissable">
                                                {{ $message }}
                                                <button type="button" class="close" data-dismiss="alert"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="sales_tablebx mt30">

                                        <div class="table-responsive">
                                            <table class="table" id="tpv-agent-table">
                                                <thead>
                                                <tr class="acjin">
                                                    <th>Sr.No.</th>
                                                    <th></th>
                                                    <th>ID</th>
                                                    <th>Full Name</th>
                                                    <th>Email</th>
                                                    <th>Clients</th>
                                                    <th class="action-width" style="min-width:102px;">Action</th>
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
    </div>

    @include('tpvagents_new.addnewtpvagentpopup')
    @include('client.users_new.change_status')
@endsection

@push('scripts')

    <script>
        $(document).ready(function () {
            var recordingTable = $('#tpv-agent-table').DataTable( {
                processing: true,
                serverSide: true,
                searching: true,
                lengthChange: true,
                dom: 'tr<"bottom"lip>',
                ajax: {
                    'url':"{{ route('tpvagents.index') }}",
                    'data': function(data){
                        data.status =$('#status').val();
                    }
                },
                {{--ajax: "{{ route('tpvagents.index') }}",--}}
                aaSorting: [[7, 'desc']],
                columns: [
                    {data: null},
                    {data: 'profile_picture',orderable:false,searchable:false},
                    {data: 'userid', name: 'userid'},
                    {data: 'full_name', name: 'first_name'},
                    {data: 'email', name: 'email'},
                    {data: 'clients_name', name: 'clients_name'},
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
                    var table = $('#tpv-agent-table').DataTable();
                    var info = table.page.info();
                    if (info.pages > 1) {
                        $('#tpv-agent-table_info')[0].style.display = 'block';
                        $('#tpv-agent-table_paginate')[0].style.display = 'block';
                    } else {
                        $('#tpv-agent-table_info')[0].style.display = 'none';
                        $('#tpv-agent-table_paginate')[0].style.display = 'none';
                    }
                    if (info.recordsTotal < 10) {
                        $('#tpv-agent-table_length')[0].style.display = 'none';
                    } else {
                        $('#tpv-agent-table_length')[0].style.display = 'block';
                    }
                },
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    var table = $('#tpv-agent-table').DataTable();
                    var info = table.page.info();
                    $("td:nth-child(1)", nRow).html(iDisplayIndex + 1 + info.start);
                    return nRow;
                }
            }).on( 'processing.dt', function ( e, settings, processing ) {
                $(".tooltip").tooltip("hide");
            });

            $('#search_recordings').change(function() {
                recordingTable.search($(this).val()).draw();
            });

            // To after filter load datatable
            $('#status').change(function() {
                recordingTable.draw();
            });

            // this is for ajax datatable clicking on pagination button
            $('body').on('click','.dataTables_paginate .paginate_button',function(){     
                $('html, body').animate({
                    scrollTop: $(".container").offset().top
                }, 400);
            });
        });
    </script>
@endpush
