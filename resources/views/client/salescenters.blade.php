<div class="mt30">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            @if(auth()->user()->hasPermissionTo('add-sales-center'))
            <div class="cont_bx3">
                @if($client->isActive())
                <div class="col-xs-12 col-sm-12 col-md-12 top_sales">
                    <a href="{{ url('admin/client') }}/{{$client->id}}/salescenters/create" class="btn btn-green pull-right" type="button">Add Sales Center</a>
                </div>
                @endif
            </div>
            @endif
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx mt30">
                <div class="table-responsive">
                    <table class="table" id="sales-center-table">
                        <thead>
                            <tr class="heading acjin">
                                <th class="sr-width">Sr. No.</th>
                                <th class="all-clogo">Logo</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Address</th>
                                <th>Contact</th>
                                <th class="action-width">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#sales-center-table').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            lengthChange: true,
            searchDelay: 2000,
            dom: 'tr<"bottom"lip>',
            pageLength: 10,
            ajax: "{{ route('client.salescenters.index', array($client_id)) }}",
            aaSorting: [
                [7, 'desc']
            ],
            columns: [{
                    data: null
                },
                {
                    data: 'logo',
                    name: 'logo',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'street',
                    name: 'street'
                },
                {
                    data: 'contact',
                    name: 'contact'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'created_at',
                    searchable: false,
                    visible: false
                },
            ],
            columnDefs: [{
                "searchable": false,
                "orderable": false,
                "width": "5%",
                "targets": 0
            }],
            'fnDrawCallback': function(){
                var table = $('#sales-center-table').DataTable();
                var info = table.page.info();
                if(info.pages > 1){
                    $('#sales-center-table_info')[0].style.display = 'block';
                    $('#sales-center-table_paginate')[0].style.display = 'block';
                } else {
                    $('#sales-center-table_info')[0].style.display = 'none';
                    $('#sales-center-table_paginate')[0].style.display = 'none';
                }
                if(info.recordsTotal < 10) {
                    $('#sales-center-table_length')[0].style.display = 'none';
                } else {
                    $('#sales-center-table_length')[0].style.display = 'block';
                }
            },
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                var table = $('#sales-center-table').DataTable();
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
</script>
@include('client.salescenter.salescenterpoup_new')
@endpush
