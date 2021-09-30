<table class="table table-bordered datatable" id="table-1">
			<thead>
				<tr>
                    <th>ID</th>
					<th>Name</th>
					<th>Email</th>				
					<th></th>				
				</tr>
			</thead>
			<tbody>
            @foreach($salesagents as $saleagent)
				<tr>
					<td><a href="{{ route('tpvagent.agentsales',['uid' =>  $saleagent->id ])}}" class="getagentsales">{{ $saleagent->userid }}</a></td>
					<td>{{ $saleagent->first_name }} {{ $saleagent->last_name }}</td>
					<td>{{ $saleagent->email }}</td>					
					<td><a href="{{ route('tpvagent.agentsales',['uid' =>  $saleagent->id ])}}" class="getagentsales">View sales</a></td>					
				</tr>
            @endforeach			 
			</tbody>
			<tfoot>
				<tr>
                  <th>ID</th>
                  <th>Name</th>
			      <th>Email</th>
			      <th></th>
				</tr>
			</tfoot>
</table>