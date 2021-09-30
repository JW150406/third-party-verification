@extends('layouts.admin')
@section('content')

    <?php
    $breadcrum = array(
        array('link' => "", 'text' => 'Clients')
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
                                <div class="users-outer-tab mt30">
                                    <div class="row">
                                        @if(auth()->user()->hasPermissionTo('add-client-user'))
                                            <div class="col-12 top_sales">
                                                <a href="#" class="btn btn-green pull-right mr15 client-user-modal"
                                                   data-toggle="modal" data-type="new">Add Client User</a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="sales_tablebx mt30">
                                        <div class="table-responsive">
                                            <table class="table" id="client-user-table">
                                                <thead>
                                                <tr class="acjin">
                                                    <th>Sr.No.</th>
                                                    <th>Client User</th>
                                                    <th>Email</th>
                                                    <th>Title</th>
                                                    <th>Role</th>
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
    </div>
    @include('client.users_new.create')
    @include('client.users_new.change_status')
@endsection

@push('scripts')

    <script>
        $(document).ready(function () {
            $('#client-user-table').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                lengthChange: true,
                searchDelay: 2000,
                dom: 'tr<"bottom"lip>',
                ajax: "{{ route('admin.client-users') }}",
                aaSorting: [[6, 'desc']],
                columns: [
                    {data: null},
                    {data: 'full_name', name: 'first_name'},
                    {data: 'email', name: 'email'},
                    {data: 'title', name: 'title', defaultContent: 'N/A'},
                    {data: 'role', name: 'role', defaultContent: 'N/A'},
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
                    var table = $('#client-user-table').DataTable();
                    var info = table.page.info();
                    if (info.pages > 1) {
                        $('#client-user-table_info')[0].style.display = 'block';
                        $('#client-user-table_paginate')[0].style.display = 'block';
                    } else {
                        $('#client-user-table_info')[0].style.display = 'none';
                        $('#client-user-table_paginate')[0].style.display = 'none';
                    }
                    if (info.recordsTotal < 10) {
                        $('#client-user-table_length')[0].style.display = 'none';
                    } else {
                        $('#client-user-table_length')[0].style.display = 'block';
                    }
                },
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    var table = $('#client-user-table').DataTable();
                    var info = table.page.info();
                    $("td:nth-child(1)", nRow).html(iDisplayIndex + 1 + info.start);
                    return nRow;
                }
            });
        });
    </script>
@endpush
