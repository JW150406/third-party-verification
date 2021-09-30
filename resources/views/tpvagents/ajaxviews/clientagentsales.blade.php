<a href="javascript:void(0)" class="back-to-clientsagents"><i class="fa fa-arrow-left"></i> Back</a>
<div class="clearfix"></div>
<table class="table table-bordered datatable" id="table-1">
			<thead>
				<tr>
                    <th>Reference ID</th>
					<th>Created At</th>
				</tr>
			</thead>
			<tbody>
            @foreach($all_leads as $lead)
				<tr>
					<td><a href="javascript:void(0)" data-ref="{{ $lead->refrence_id }}" data-uid="{{ $lead->user_id }}" class="openleaddetail">{{ $lead->refrence_id }}</a></td>
					<td><a href="javascript:void(0)" data-ref="{{ $lead->refrence_id }}" class="openleaddetail">{{ $lead->create_time }}</a></td>					 			
				</tr>
            @endforeach			 
			</tbody>
			<tfoot>
				<tr>
				    <th>Reference ID</th>
					<th>Created At</th>
				</tr>
			</tfoot>
</table>
<script>
 tableIntialize();
</script>