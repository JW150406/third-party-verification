@extends('layouts.admin')
@section('content')

<?php
$breadcrum = array();
if (Auth::user()->access_level == 'tpv') {
	$breadcrum[] =  array('link' => route('client.index'), 'text' =>  'Clients');
	$breadcrum[] =  array('link' => route('client.show', $client->id), 'text' =>  $client->name);
} else {
	$breadcrum[] =  array('link' => url()->previous(), 'text' =>  'Users');
}
$breadcrum[] =  array('link' => '', 'text' =>  $user->first_name);
breadcrum($breadcrum);
?>




<div class="tpv-contbx">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12">
				<div class="cont_bx3">
					<div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
						<h1>Client Users</h1>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12">
						@if ($message = Session::get('success'))
						<div class="alert alert-success">
							<p>{{ $message }}</p>
						</div>
						@endif
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr class="acjin">
										<th>#ID</th>
										<th>First Name</th>
										<th>Last Name</th>
										<th>Email</th>
										<th>Title</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="light_c">{{ $user->userid }}</td>
										<td class="white_c">{{ $user->first_name }}</td>
										<td class="white_c">{{ $user->last_name }}</td>
										<td class="white_c">{{ $user->email }}</td>
										<td class="light_c">{{ $user->title }}</td>
									</tr>

								</tbody>
							</table>

							<div class="btnintable bottom_btns">
								<div class="btn-group">
									<?php edit_btn_on_view_info_page(route('client.user.edit', ['id' => $client_id, 'userid' => $user->id])) ?>
									<?php if ($user->status == 'active') { ?>
										<a class="deactivate-clientuser btn btn-red" href="javascript:void(0)" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Deactivate User" data-uid="{{ $user->id }}" id="delete-clientuser-{{ $user->id }}" data-clientusername="{{ $user->first_name }} {{ $user->last_name }}">Deactivate<span class="del"><?php
																																																																																														echo getimage('images/activate.png') ?></span> </a>
									<?php } else { ?>
										<a class="activate-clientuser  btn btn-green" href="javascript:void(0)" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Activate User" data-uid="{{ $user->id }}" id="delete-clientuser-{{ $user->id }}" data-clientusername="{{ $user->first_name }} {{ $user->last_name }}">Activate<span class="add"><?php echo getimage('images/deactivate.png') ?></span></a>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


@include('client.user.clientuserpoup')

@endsection
