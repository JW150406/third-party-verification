
@extends('layouts.admin')
@section('content')

<?php
$breadcrum = array(
    array('link' => "#", 'text' =>  'Users'),
    array('link' => "", 'text' =>  'All  Users')
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
                                <div class="col-md-6">
                                    <h1 class="mt10">All Users</h1>
                                </div>
                                <div class="col-md-6">
                                    <div class="btn-group pull-right btn-sales-all">
                                        <div class="update_client_by_location w130">
                                            <select class="select2 auto-submit" name="status" id="status">
                                                <option value="active">Active Users</option>
                                                <option value="inactive">Deactivated Users</option>
                                                <option value="all">All Users</option>
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
                                <div class="table-responsive">
                                    <table class="table" id="all-user-table">
                                        <thead>
                                            <tr class="acjin">
                                                <th>Sr.No.</th>
                                                <th></th>
                                                <th>Full Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
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

@include('client.users_new.create',['roles'=>$clientUserRoles])
@include('client.users_new.change_status')
@include('client.salescenter_new.user.create',['roles'=>$salesCenterUserroles])
@include('client.salescenter_new.user.change_status')
@include('tpvusers.addnewpopup')
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        var recordingTable = $('#all-user-table').DataTable( {
            dom: 'tr<"bottom"lip>',
            processing: true,
            serverSide: true,
            searching: true,
            lengthChange: true,
            ajax: {
                'url':"{{ route('admin.all.users') }}",
                'data': function(data){
                    data.status =  $('#status').val();
                }
            },
            {{--ajax: "{{ route('admin.all.users') }}",--}}
            aaSorting: [[6, 'desc']],
            columns: [
                {data: null},
                {data: 'profile_picture',orderable:false,searchable:false},
                {data: 'full_name', name: 'first_name'},
                {data: 'email', name: 'email'},
                {data: 'display_name', name: 'roles.display_name'},
                {data: 'action',orderable:false,searchable:false},
                {data: 'created_at',searchable:false,visible: false},
                {data: 'last_name',name:'last_name',visible: false,orderable: false},
            ],
            columnDefs: [
            {
                "searchable": false,
                "orderable": false,
                "width": "5%",
                "targets": 0
            }],
            'fnDrawCallback': function(){
                var table = $('#all-user-table').DataTable();
                var info = table.page.info();
                if(info.pages > 1){
                    $('#all-user-table_info')[0].style.display = 'block';
                    $('#all-user-table_paginate')[0].style.display = 'block';
                } else {
                    $('#all-user-table_info')[0].style.display = 'none';
                    $('#all-user-table_paginate')[0].style.display = 'none';
                }
                if(info.recordsTotal < 10) {
                    $('#all-user-table_length')[0].style.display = 'none';
                } else {
                    $('#all-user-table_length')[0].style.display = 'block';
                }
            },
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                var table = $('#all-user-table').DataTable();
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
