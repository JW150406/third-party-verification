<div class="users-outer-tab mt30">
    <div class="row">
        @if($salescenter->isActive() && $salescenter->isActiveClient())
        <div class="col-12 btn-group pull-right mr15">
            @if(auth()->user()->hasPermissionTo('add-sales-users'))
                <a href="#" class="btn btn-green mr15  salescenter-user-modal" data-toggle="modal" data-target="#addSalesCenterUser" data-original-title="Add Sales Center User" data-type="new"  type="button">Add Sales Center User</a>
            @endif
            @if(auth()->user()->hasPermissionTo('bulk-upload-sales-users') || auth()->user()->hasPermissionTo('export-sales-users'))
            <button type="button" class="btn btn-green dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                More <span class="caret"></span>
            </button>
            <ul class="dropdown-menu employee-dropdown" role="menu">
                @if(auth()->user()->hasPermissionTo('bulk-upload-sales-users'))
                    <li>
                        <a href="{{ route('salescenter.user.bulkupload', array($client_id, $salecenter_id)) }}" type="button">Bulk Upload</a>
                    </li>
                @endif
                @if(auth()->user()->hasPermissionTo('export-sales-users'))
                <li>
                    <a href="{{ route('salescenter.user.export', array($client_id, $salecenter_id)) }}" type="button">Export</a>
                </li>
                @endif
            </ul>
            @endif
        </div>
        @endif
    </div>
    <div class="sales_tablebx mt30">
        <div class="table-responsive">
            <table class="table" id="sales-center-user-table">
                <thead>
                    <tr class="acjin">
                        <th>Sr.No.</th>
                        <th></th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Location</th>                       
                        <th>Role</th>                       
                        <th class="action-width">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@include('client.salescenter_new.user.create')
@include('client.salescenter_new.user.change_status')
@push('scripts')
<script>
    $(document).ready(function() {
        $('#sales-center-user-table').DataTable( {
            processing: true,
            serverSide: true,
            searching: false,
            lengthChange: true,
            searchDelay: 2000,
            dom: 'tr<"bottom"lip>',
            ajax: "{{ route('salescenter.users.index',['client_id'=>$client_id,'salescenter_id'=>$salecenter_id]) }}",
            aaSorting: [[7, 'desc']],
            columns: [
                {data: null},
                {data: 'profile_picture',orderable:false,searchable:false},
                {data: 'full_name', name: 'first_name'},
                {data: 'email', name: 'email'},
                {data: 'location_name', name: 'location_name'},
                {data: 'role_name', name: 'role_name'},
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
                var table = $('#sales-center-user-table').DataTable();
                var info = table.page.info();
                if(info.pages > 1){
                    $('#sales-center-user-table_info')[0].style.display = 'block';
                    $('#sales-center-user-table_paginate')[0].style.display = 'block';
                } else {
                    $('#sales-center-user-table_info')[0].style.display = 'none';
                    $('#sales-center-user-table_paginate')[0].style.display = 'none';
                }
                if(info.recordsTotal < 10) {
                    $('#sales-center-user-table_length')[0].style.display = 'none';
                } else {
                    $('#sales-center-user-table_length')[0].style.display = 'block';
                }
            },
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                var table = $('#sales-center-user-table').DataTable();
                var info = table.page.info();
                $("td:nth-child(1)", nRow).html(iDisplayIndex + 1 + info.start);
                return nRow;
            }
        }).on( 'processing.dt', function ( e, settings, processing ) {
            $(".tooltip").tooltip("hide");
        });
        
    });
</script>

@endpush
