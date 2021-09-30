<div class="mt30">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="cont_bx3">

                @if (auth()->user()->hasPermissionTo('add-new-commodity') && $client->isActive())
                <div class="col-xs-12 col-sm-12 col-md-12 top_sales">
                    <a href="javascript:void(0)" class="btn btn-green pull-right commodity-create-modal"  data-type="new" data-original-title="Add New Commodity"  type="button">Add New Commodity</a>
                </div>
                @endif
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx mt30">
                <div class="table-responsive">
                    <table class="table" id="commodity-table">
                        <thead>
                            <tr class="heading acjin">
                                <th>Sr. No.</th>
                                <th>Name</th>
                                <th>Units</th>
                                <th class="action-width">Action</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('client.commodities.create')
@include('client.commodities.delete')
@push('scripts')
<script>
    $(document).ready(function() {
        $('#commodity-table').DataTable( {
            processing: true,
            serverSide: true,
            searching: false,
            dom: 'tr<"bottom"lip>',
            lengthChange: true,
            hideEmptyCols: ['extn', 3],
            ajax: { url : "{{ route('commodity.index') }}",data:{client_id:"{{$client_id}}"}},
            aaSorting: [[4, 'desc']],
            columns: [
                {data: null},
                {data: 'name', name: 'name'},
                {data: 'units', name: 'units.unit',orderable:false},
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
                var table = $('#commodity-table').DataTable();
                var info = table.page.info();
                if(info.pages > 1){
                    $('#commodity-table_info')[0].style.display = 'block';
                    $('#commodity-table_paginate')[0].style.display = 'block';
                } else {
                    $('#commodity-table_info')[0].style.display = 'none';
                    $('#commodity-table_paginate')[0].style.display = 'none';
                }
                if(info.recordsTotal < 10) {
                    $('#commodity-table_length')[0].style.display = 'none';
                } else {
                    $('#commodity-table_length')[0].style.display = 'block';
                }
            },
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                var table = $('#commodity-table').DataTable();
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

    $('#commodity-create-modal').on('hidden.bs.modal', function () {
        $('#commodity-create-form').parsley().reset();
    });
</script>
@endpush
