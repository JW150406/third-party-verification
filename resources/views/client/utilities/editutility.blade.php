@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array();

$breadcrum[] =  array('link' => route('utilities.index', ['client' => $utility->client_id]), 'text' =>  'Utilities');
$breadcrum[] =  array('link' => '', 'text' =>  $utility->utilityname);
breadcrum($breadcrum);
?>

<div class="tpv-contbx edit-agentinfo">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12">
				<div class="cont_bx3">

					<div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
						<div class="client-bg-white">
							<h1>Utility Detail</h1>


							<div class="sales_tablebx client-new-tabs">
								<!-- Nav tabs -->
								<ul class="nav nav-tabs" role="tablist">
									<li role="presentation" class="active"><a href="#utility-detail" aria-controls="home" role="tab" data-toggle="tab">Utility Detail</a></li>
									<li role="presentation"><a href="#utility-zipcode" aria-controls="profile" role="tab" data-toggle="tab">Utility Zipcodes</a></li>
								</ul>

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
													<div class="col-xs-12 col-sm-12 col-md-12">



														<form enctype="multipart/form-data" role="form" method="POST" action="">
															{{ csrf_field() }}
															{{ method_field('POST') }}
															<input type="hidden" name="client_id" value="{{$utility->client_id}}">
															<div class="col-xs-12 col-sm-12 col-md-12">
																<div class=" form-group {{ $errors->has('commodity') ? ' has-error' : '' }}">
																	<label for="exampleInputName1">Commodity</label>
																	<input id="commodity" autocomplete="off" type="text" class="form-control" name="commodity" value="{{ $utility->commodity }}" required placeholder="Commodity">

																	@if ($errors->has('commodity'))
																	<span class="help-block">
																		<strong>{{ $errors->first('commodity') }}</strong>
																	</span>
																	@endif
																</div>
															</div>
															<div class="col-xs-12 col-sm-12 col-md-12">
																<div class="form-group {{ $errors->has('utilityname') ? ' has-error' : '' }}">
																	<label for="utilityname">Name</label>
																	<input id="utilityname" autocomplete="off" type="text" class="form-control" name="utilityname" value="{{ $utility->utilityname }}" required placeholder="Utility Name">

																	@if ($errors->has('utilityname'))
																	<span class="help-block">
																		<strong>{{ $errors->first('utilityname') }}</strong>
																	</span>
																	@endif
																</div>
															</div>
															<div class="col-xs-12 col-sm-12 col-md-12">
																<div class="form-group {{ $errors->has('market') ? ' has-error' : '' }}">
																	<label for="market">Market</label>
																	<input id="market" autocomplete="off" type="text" class="form-control" name="market" value="{{ $utility->market }}" required placeholder="Market">

																	@if ($errors->has('market'))
																	<span class="help-block">
																		<strong>{{ $errors->first('market') }}</strong>
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

									<!--agent details ends-->

									<!--twilio setting content starts-->

									<div role="tabpanel" class="tab-pane" id="utility-zipcode">

										@include('client.utilities.mapzipcode')

									</div>

									<!--twilio setting content ends-->

								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	@endsection




	