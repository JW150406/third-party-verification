@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array();

$breadcrum[] =  array('link' => route('utilities.index', ['client' => $utility->client_id]), 'text' =>  'Utilities');
$breadcrum[] =  array('link' => '', 'text' =>  'View Utility');
breadcrum($breadcrum);
?>

<div class="tpv-contbx">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12">
				<div class="cont_bx3">
					<div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
						<div class="client-bg-white">
							<div class="row">
								<div class="col-md-6">
							<h1>Utility info</h1>
								</div>
								<div class="col-md-6">
							<?php if (Auth::user()->can(['create-update-utility'])) { ?>
										<div class="bottom_btns utility_btns pull-right">
											<div class="btn-group">

												<a class="btn btn-green" href="{{ route('client.utility.edit',$utility->id) }}">Edit</a>

												<!-- <a class="btn btn-purple"  href=" route('utility.programs',['client' => $utility->client_id,'utility' => $utility->id])">Programs<span class="browse"><?php echo getimage("images/view_w.png"); ?></span></a> -->
											</div>
										</div>
									<?php } ?>
							</div>
							</div>
							<div class="sales_tablebx mt30">
								<div class="table-responsive">
									<table class="table">
										<thead>
											<tr class="acjin">
												<th>Label</th>
												<th>Value</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td class="light_c">Commodity</td>
												<td class="dark_c">{{ $utility->commodity }}</td>
											</tr>
											<tr>
												<td class="white_c">Uility Name</td>
												<td class="grey_c">{{ $utility->utilityname }}</td>
											</tr>
											<tr>
												<td class="white_c">Market</td>
												<td class="grey_c">{{ $utility->market }}</td>
											</tr>


											<tr>
												<td class="white_c">Company</td>
												<td class="grey_c">{{ $utility->company }}</td>
											</tr>

										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	@include('client.utilities.utilitypoup')
	@endsection