<div class="mt30">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="cont_bx3">
                <div class="btn-group pull-right">
                    
                    @if (auth()->user()->hasPermissionTo('add-do-not-enroll'))
                    <a href="#" class="btn btn-green  mr15 do-not-enroll-modal" data-toggle="modal" data-type="new">Add Account Number</a>
                    @endif

                    @if (auth()->user()->hasPermissionTo('bulk-upload-do-not-enroll') || auth()->user()->hasPermissionTo('export-do-not-enroll'))
                    <button type="button" class="btn btn-green dropdown-toggle  mr15" data-toggle="dropdown"
                            aria-expanded="false">
                        More <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu employee-dropdown" role="menu">
                        @if (auth()->user()->hasPermissionTo('bulk-upload-do-not-enroll'))
                        <li><a href="{{ route('do-not-enroll.bulkupload',['client_id' => $client_id]) }}"
                                type="button">Bulk Upload</a></li>
                        @endif
                        @if (auth()->user()->hasPermissionTo('export-do-not-enroll'))
                        <li><a href="{{ route('do-not-enroll.export',['client_id' => $client_id]) }}"
                                type="button">Export</a></li>
                        @endif
                    </ul>
                    @endif
                </div>
                <div class="sor_fil utility-btn-group">
                    <div class="search mr15">

                        <div class="search-container margin-bottom-for-filters">

                            <button type="button">{!! getimage('images/search.png') !!}</button>
                            <input placeholder="Account Number" id="filterAccountNumber" type="text" name="search_field" value="">
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx mt30">
                <div class="table-responsive">
                    <table class="table" id="doNotEnroll-table">
                        <thead>
                            <tr class="heading acjin">
                                <th>Sr. No.</th>
                                <th>Account Number</th>
                                <th>Date</th>
                                @if (auth()->user()->hasPermissionTo('delete-do-not-enroll'))
                                <th class="action-width">Action</th>
                                @endif
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('client.do-not-enroll.create')
@include('client.do-not-enroll.delete')

@push('scripts')
<script>
    $(document).ready(function() {
			$('#doNotEnroll-table').DataTable( {
				dom: 'tr<"bottom"lip>',
				processing: true,
				serverSide: true,
				lengthChange: true,
				ajax: {
				    url: "{{ route('do-not-enroll.index', $client_id) }}",
                },
				aaSorting: [[4, 'desc']],
				columns: [
                    {data: null},
					{
                        data: 'account_number', 
                        name: 'account_number',
                        searchable: true,
                    },
                    {
                        data: 'created_at', 
                        name: 'created_at'
                    },
					{
                        data: 'action',
                        orderable:false,
                        searchable:false
                    },
                    {
						data: 'id',
						searchable: false,
						visible: false
					},
				],
				columnDefs: [
					{
						"searchable": false,
						"orderable": false,
						"width": "5%",
						"targets": 0
					}],
                'fnDrawCallback': function(){
					var table = $('#doNotEnroll-table').DataTable();
					var info = table.page.info();
					if(info.pages > 1){
						$('#doNotEnroll-table_info')[0].style.display = 'block';
						$('#doNotEnroll-table_paginate')[0].style.display = 'block';
					} else {
						$('#doNotEnroll-table_info')[0].style.display = 'none';
						$('#doNotEnroll-table_paginate')[0].style.display = 'none';
					}
					if(info.recordsTotal < 10) {
	                    $('#doNotEnroll-table_length')[0].style.display = 'none';
	                } else {
	                    $('#doNotEnroll-table_length')[0].style.display = 'block';
	                }
				},
				"fnRowCallback": function(nRow, aData, iDisplayIndex) {
					var table = $('#doNotEnroll-table').DataTable();
					var info = table.page.info();
					$("td:nth-child(1)", nRow).html(iDisplayIndex + 1 + info.start);
					return nRow;
				}
			});
		});
        
        $('#filterAccountNumber').change(function() {
            $('#doNotEnroll-table').DataTable().search($(this).val()).draw();
        });
        $("#status").change(function() {
            $('#doNotEnroll-table-table').DataTable().ajax.reload();
        });
</script>
@endpush
