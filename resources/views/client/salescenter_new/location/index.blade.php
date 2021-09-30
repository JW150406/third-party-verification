<div class="users-outer-tab mt30">
    <div class="row">
        @if($salescenter->isActive() && $salescenter->isActiveClient())
        <div class="col-12 top_sales">
            @if(!auth()->user()->isLocationRestriction())
            <a href="#" class="btn btn-green pull-right mr15 salescenter-location-modal" data-toggle="modal"   data-original-title="Add Sales Center Location" data-type="new"  type="button">Add Sales Center Location</a>
            @endif
        </div>
        @endif
    </div>
    <div class="sales_tablebx with-delete-action mt30">
        <div class="table-responsive">
            <table class="table" id="sales-center-location-table">
                <thead>
                    <tr class="acjin">
                        <th>Sr.No.</th>
                        <th>Name</th>
                        <th>Code</th>                       
                        <th>Address</th>                       
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@include('client.salescenter_new.location.create')
@include('client.salescenter_new.location.change-status')
@include('client.salescenter_new.location.delete')
@push('scripts')
<script>
    $(document).ready(function() {
        $('#sales-center-location-table').DataTable( {
            processing: true,
            serverSide: true,
            searching: false,
            lengthChange: true,
            searchDelay: 2000,
            dom: 'tr<"bottom"lip>',
            ajax: "{{ route('salescenter.location.index',['client_id'=>$client_id,'salescenter_id'=>$salecenter_id]) }}",
            aaSorting: [[1, 'asc']],
            columns: [
                {data: null},
                {data: 'name', name: 'name'},
                {data: 'code', name: 'code'},
                {data: 'street', name: 'street'},
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
                var table = $('#sales-center-location-table').DataTable();
                var info = table.page.info();
                if(info.pages > 1){
                    $('#sales-center-location-table_info')[0].style.display = 'block';
                    $('#sales-center-location-table_paginate')[0].style.display = 'block';
                } else {
                    $('#sales-center-location-table_info')[0].style.display = 'none';
                    $('#sales-center-location-table_paginate')[0].style.display = 'none';
                }
                if(info.recordsTotal < 10) {
                    $('#sales-center-location-table_length')[0].style.display = 'none';
                } else {
                    $('#sales-center-location-table_length')[0].style.display = 'block';
                }
            },
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                var table = $('#sales-center-location-table').DataTable();
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
