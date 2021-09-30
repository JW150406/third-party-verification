<div class="mt30">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="cont_bx3">
                @if(auth()->user()->hasPermissionTo('add-customer-type'))
                <div class="col-xs-12 col-sm-12 col-md-12 top_sales">
                    <a href="javascript:void(0)" class="btn btn-green pull-right customer-type-create-modal"  data-type="new" data-original-title="Add New Customer Type"  type="button">Add New Customer Type</a>
                </div>
                @endif
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx mt30">
                <div class="table-responsive">
                    <table class="table" id="customer-type-table">
                        <thead>
                            <tr class="heading acjin">
                                <th>Sr. No.</th>
                                <th>Name</th>
                                <th class="action-width">Action</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@permission('add-customer-type','edit-customer-type')
    @include('client.customer_type.create')
@endpermission
@permission('delete-customer-type')
    @include('client.customer_type.delete')
@endpermission
@push('scripts')
<script>
    $(document).ready(function() {
        $('#customer-type-table').DataTable( {
            processing: true,
            serverSide: true,
            searching: false,
            dom: 'tr<"bottom"lip>',
            lengthChange: true,
            ajax: { url : "{{ route('customerType.index') }}",data:{client_id:"{{$client_id}}"}},
            aaSorting: [[3, 'desc']],
            columns: [
                {data: null},
                {data: 'name', name: 'name'},
                {data: 'action',orderable:false,searchable:false},
                {data: 'created_at',searchable:false,visible: false},
            ],
            columnDefs: [
            {
                "searchable": false,
                "orderable": false,
                "width": "50",
                "targets": 0
            }],
            'fnDrawCallback': function(){
                var table = $('#customer-type-table').DataTable();
                var info = table.page.info();
                if(info.pages > 1){
                    $('#customer-type-table_info')[0].style.display = 'block';
                    $('#customer-type-table_paginate')[0].style.display = 'block';
                } else {
                    $('#customer-type-table_info')[0].style.display = 'none';
                    $('#customer-type-table_paginate')[0].style.display = 'none';
                }
                if(info.recordsTotal < 10) {
                    $('#customer-type-table_length')[0].style.display = 'none';
                } else {
                    $('#customer-type-table_length')[0].style.display = 'block';
                }
            },
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                var table = $('#customer-type-table').DataTable();
                var info = table.page.info();
                $("td:nth-child(1)", nRow).html(iDisplayIndex + 1 + info.start);                
                return nRow;
            }
        }).on( 'processing.dt', function ( e, settings, processing ) {
            $(".tooltip").tooltip("hide");
        });
        // this is for ajax datatable clicking on pagination button
        $('body').on('click','.dataTables_paginate .paginate_button',function(){     
                $('html, body').animate({
                    scrollTop: $(".container").offset().top
                }, 400);
            });
    });

    $('#customer-type-create-modal').on('hidden.bs.modal', function () {
        $('#customer-type-create-form').parsley().reset();
    });
</script>
@endpush
