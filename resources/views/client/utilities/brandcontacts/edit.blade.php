@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array();

$breadcrum[] =  array('link' => route('utility.brandcontacts'), 'text' =>  'Brand contacts');
$breadcrum[] =  array('link' => '', 'text' =>  $detail->name);
breadcrum($breadcrum);
?>

<div class="tpv-contbx edit-agentinfo">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12">
				<div class="cont_bx3">

					<div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
						<div class="client-bg-white">
							<h1>Edit Detail</h1>




							<div class=" sales_tablebx">
								<!-- Nav tabs -->


								<!-- Tab panes -->
								<div class="tab-content">

									<!--agent details starts-->

									<div role="tabpanel" class="tab-pane active" id="utility-detail">
										<div class="row">
											<div class="col-xs-12 col-sm-12 col-md-12">

												<div class="agent-detailform utility-detail">
													@if (count($errors) > 0)
													<div class="tpvbtn">
														<div class="col-xs-12 col-sm-12 col-md-12">
															<div class="alert alert-danger">
																<strong>Whoops!</strong> There were some problems with your input.<br><br>
																<ul>
																	@foreach ($errors->all() as $error)
																	<li>{{ $error }}</li>
																	@endforeach
																</ul>
															</div>
														</div>
													</div>

													@endif
													@if ($message = Session::get('success'))
													<div class="tpvbtn">
														<div class="col-xs-12 col-sm-12 col-md-12">
															<div class="alert alert-success">
																<p>{{ $message }}</p>
															</div>
														</div>
													</div>
													@endif
													<div class="col-xs-12 col-sm-6 col-md-6 col-md-offset-3">



														<form enctype="multipart/form-data" role="form" method="POST" action="">
															{{ csrf_field() }}
															{{ method_field('POST') }}

															<div class="col-xs-12 col-sm-12 col-md-12">
																<div class=" form-group {{ $errors->has('name') ? ' has-error' : '' }}">
																	<label for="name">Brand</label>
																	<input id="name" autocomplete="off" type="text" class="form-control" name="name" value="{{ $detail->name }}" required placeholder="Brand Name">

																	@if ($errors->has('commodity'))
																	<span class="help-block">
																		<strong>{{ $errors->first('commodity') }}</strong>
																	</span>
																	@endif
																</div>
															</div>
															<div class="col-xs-12 col-sm-12 col-md-12">
																<div class="form-group {{ $errors->has('contact') ? ' has-error' : '' }}">
																	<label for="contact">Contact</label>
																	<input id="contact" autocomplete="off" type="text" class="form-control" name="contact" value="{{ $detail->contact }}" required placeholder="contact">

																	@if ($errors->has('contact'))
																	<span class="help-block">
																		<strong>{{ $errors->first('contact') }}</strong>
																	</span>
																	@endif
																</div>
															</div>

															<div class="clearfix"></div>




															<div class="col-xs-12 col-sm-12 col-md-12">
																<div class="btnintable bottom_btns">
																	<div class="btn-group">
																		<button class="btn btn-green" type="submit">Save</button>
																	</div>
																</div>
															</div>
														</form>
													</div>
												</div>

											</div>
										</div>

									</div>
								</div>
							</div>

							<!--agent details ends-->



						</div>

					</div>

				</div>
			</div>
		</div>
	</div>
</div>


@endsection