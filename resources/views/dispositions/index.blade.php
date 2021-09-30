<div class="dispositions-outer-tab mt30">
	<div class="row">
		<div class="col-12 top_sales">
		
			<div class="btn-group pull-right">
              

                <?php if (Auth::user()->hasPermissionTo('add-dispositions') && $client->isActive()) { ?>
				    <a href="#" class="btn btn-green  mr15 disposition-modal" data-toggle="modal"  data-type="new" data-original-title="Add Disposition" >Add Disposition</a>
			    <?php } ?>
			@if((auth()->user()->hasPermissionTo('bulk-upload-dispositions') || auth()->user()->hasPermissionTo('export-dispositions')) && $client->isActive())

                <button type="button" class="btn btn-green dropdown-toggle  mr15" data-toggle="dropdown"
                        aria-expanded="false">
                    More <span class="caret"></span>
                </button>
                <ul class="dropdown-menu employee-dropdown" role="menu">
                    @if(auth()->user()->hasPermissionTo('bulk-upload-dispositions'))
                        <li><a href="{{ route('disposition.bulkupload',['client_id' => $client_id]) }}"
                               type="button">Bulk Upload</a></li>
                    @endif
                    @if(auth()->user()->hasPermissionTo('export-dispositions'))
                        <li><a href="{{ route('disposition.export',['client_id' => $client_id]) }}"
                               type="button">Export</a></li>
                    @endif
                </ul>
            @endif
        	</div>
			<div class="btn-group pull-right btn-sales-all">
			<select class="select2 btn btn-green dropdown-toggle mr15" id="status" name="status">
						<option value="" >All</option>
						<option value="active" selected>Active</option>
						<option value="inactive">Inactive</option>
					</select>
			</div>
		</div>
	</div>
	<div class="sales_tablebx mt30">
		<div class="table-responsive">
			<table class="table" id="disposition-table">
				<thead>
				<tr class="acjin">
					<th class="acjin">No</th>
					<th>Category</th>
					<th>Description</th>
					<th>Disposition Group</th>
					<th>Alerts</th>
					{{--<th>Allow Cloning</th>--}}
					<th class="action-width">Action</th>
				</tr>
				</thead>
			</table>
		</div>
	</div>
</div>
				</div>
@include('dispositions.create')
{{--@include('client.users_new.change_status')--}}
@include('dispositions.dispositionpoup')

@push('scripts')
	<script>
		$(document).ready(function() {
			$('#disposition-table').DataTable( {
				dom: 'tr<"bottom"lip>',
				processing: true,
				serverSide: true,
				searching: false,
				lengthChange: true,
				hideEmptyCols: ['extn', 4],
				ajax: {
				    url: "{{ route('client.dispositionslist',$client_id) }}",
                    data: function(d) {
                        d.status = $("#status").val()
                    }
                },
				aaSorting: [[6, 'desc']],
				columns: [
					{data: null},
					{data: 'category', name: 'type'},
					{data: 'description', name: 'description'},
					{data: 'disposition_group', name: 'disposition_group'},					
					{data: 'email_alert', name: 'email_alert',orderable:false,searchable:false},
					{data: 'action',orderable:false,searchable:false},
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
					var table = $('#disposition-table').DataTable();
					var info = table.page.info();
					if(info.pages > 1){
						$('#disposition-table_info')[0].style.display = 'block';
						$('#disposition-table_paginate')[0].style.display = 'block';
					} else {
						$('#disposition-table_info')[0].style.display = 'none';
						$('#disposition-table_paginate')[0].style.display = 'none';
					}
					if(info.recordsTotal < 10) {
	                    $('#disposition-table_length')[0].style.display = 'none';
	                } else {
	                    $('#disposition-table_length')[0].style.display = 'block';
	                }
				},
				"fnRowCallback": function(nRow, aData, iDisplayIndex) {
					var table = $('#disposition-table').DataTable();
					var info = table.page.info();
					$("td:nth-child(1)", nRow).html(iDisplayIndex + 1 + info.start);
					return nRow;
				}
			}).on( 'processing.dt', function ( e, settings, processing ) {
            	$(".tooltip").tooltip("hide");
        	});
		});

        $("#status").change(function() {
            $('#disposition-table').DataTable().ajax.reload();;
        });
	</script>
@endpush

