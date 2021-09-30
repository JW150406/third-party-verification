@extends('layouts.admin')
@section('content')

{{breadcrum ($breadcrum)}}


<div class="tpv-contbx">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12">
				<div class="cont_bx3">
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="client-bg-white">
							<div class="clearfix"></div>
							@if ($message = Session::get('success'))
							<div class="tpvbtn">
								<div class="alert alert-success">
									<p>{{ $message }}</p>
								</div>
							</div>

							@endif

							<div class="clearfix"></div>
							<div class="row">
								<div class="col-12 top_sales">
									<a href="{{ route('roles.create') }}" class="btn btn-green pull-right mr15" type="submit">Add New Role</a>
								</div>
							</div>
							<div class="sales_tablebx mt30">
								<div class="table-responsive">
									<table class="table">
										<thead>
											<tr class="acjin">
												<th>No</th>
												<th>Name</th>
												<th>Address</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											@foreach ($roles as $key => $role)
											<tr class="list-users">
												<td class="light_c">{{ ++$i }}</td>
												<td class="white_c">{{ $role->display_name }}</td>
												<td class="white_c">{{ $role->description }}</td>
												<td class="light_c">
													<div class="btn-group">
														<a class="btn" href="{{ route('roles.show',$role->id) }}" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="View Role" role="button"><img src="{{ asset('images/view.png') }}" /></a>
														<a class="btn" href="{{ route('roles.edit',$role->id) }}" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Edit Role" role="button"><img src="{{ asset('images/edit.png') }}" /></a>
														<a class="btn deleterole" href="javascript:void(0)" data-roleid="{{ $role->id }}" id="delete-role-{{ $role->id }}" data-toggle="tooltip" data-target="#DeleteRole" data-role="{{ $role->display_name }}" data-placement="top" data-container="body" title="" data-original-title="Delete Role" role="button"><img src="{{ asset('images/cancel.png') }}" /></a>
													</div>
												</td>
											</tr>
											@endforeach
											@if(count($roles)==0)
											<tr class="list-users">
												<td colspan="5" class="text-center">No Record Found</td>
											</tr>
											@endif
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>










		@include('roles.deletepopup')
		@endsection