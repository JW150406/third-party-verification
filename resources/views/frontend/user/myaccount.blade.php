@extends('layouts.app')
@section('content')


<div class="salesagent-wrapper mt-40">
	<div class="cont_bx1">
		<div class="container">
			<div class="col-xs-12 col-sm-12 col-md-12">
					@if ($success = Session::get('success'))
                        <div class="alert alert-success">
                            {{ $success }}
                        </div>
					@endif
					@if ($error = Session::get('error'))
                        <div class="alert alert-danger">
                           {{ $error }}
                        </div>
					@endif
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-md-6">
						<div class="mt20 text-left" id="welcome">
						{{--<p>Dashboard</p>--}}
						</div>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-6">
						<div class="text-right">
						<a href="{{ route('client.contact',Auth::user()->client_id) }}" class="btn btn-green btn-new-enroll" type="button"><?php echo getimage("images/newenroll.png"); ?></a>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
	<div class="cont_bx2 pdt0">
		<div class="container">
			<div class="col-xs-12 col-sm-12 col-md-12">

				<div class="row agent-five-block">

					<div class="col-xs-12 col-sm-4  col-md-4">
						<div class="verify_today bg1">
							<div class="col-xs-12 col-sm-12 col-md-12">
								<?php echo getimage("images/dash-6.png"); ?>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-12">
								<p class="pending-leads"><span>{{$pending_leads}}</span> Pending Leads</p>
								<div class="viewmore-btn">
									<a href="{{route('profile.leads',['status' => 'pending'])}}" class="btn" type="button">View more</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-4">
						<div class="verify_today bg2">
							<div class="col-xs-12 col-sm-12 col-md-12">
								<?php echo getimage("images/dash-1.png"); ?>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-12">
								<p class="verified-leads" style="margin: 4px !important;"><span>{{$verified_leads}}</span> Verified Leads</p>
								<div class="viewmore-btn">
									<a href="{{route('profile.leads',['status' => 'verified'])}}" class="btn" type="button">View more</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-4">
						<div class="verify_today bg3">
							<div class="col-xs-12 col-sm-12 col-md-12">
								<?php echo getimage("images/dash-3.png"); ?>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-12">
								<p class="Declined"><span>{{$decline_leads}}</span> Declined Leads</p>
								<div class="viewmore-btn">
									<a href="{{route('profile.leads',['status' => 'decline'])}}" class="btn" type="button">View more</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-4">
						<div class="verify_today bg4 hangupcall">
							<div class="col-xs-12 col-sm-12 col-md-12">
								<?php echo getimage("images/dash-2.png"); ?>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-12">
								<p class="Hangup-Calls"><span>{{$hanged_leads}}</span> Disconnected Calls</p>
								<div class="viewmore-btn">
									<a href="{{route('profile.leads',['status' => 'hangup'])}}" class="btn" type="button">View more</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-4">
						<div class="verify_today bg5 hangupcall">
							<div class="col-xs-12 col-sm-12 col-md-12">
								<?php echo getimage("images/dash-5.png"); ?>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-12">
								<p class="Hangup-Calls"><span>{{$cancel_leads}}</span> Cancelled Leads </p>
								<div class="viewmore-btn">
									<a href="{{route('profile.leads',['status' => 'cancel'])}}" class="btn" type="button">View more</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4  col-md-4">
						<div class="verify_today bg6">
							<div class="col-xs-12 col-sm-12 col-md-12">
								<?php echo getimage("images/dash-7.png"); ?>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-12">
								<p class="pending-leads"><span>{{$expired_leads}}</span> Expired Leads</p>
								<div class="viewmore-btn">
									<a href="{{route('profile.leads',['status' => 'expired'])}}" class="btn" type="button">View more</a>
								</div>
							</div>
						</div>
					</div>
					@if(isOnSettings(Auth::user()->client_id, 'is_enable_self_tpv_welcome_call'))
						<div class="col-xs-12 col-sm-4 col-md-4">
							<div class="verify_today bg7">
								<div class="col-xs-12 col-sm-12 col-md-12">
									<?php echo getimage("images/dash-8.png"); ?>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-12">
									<p class="pending-leads"><span>{{$self_verified_leads}}</span> Self Verified Leads</p>
									<div class="viewmore-btn">
										<a href="{{route('profile.leads',['status' => 'self-verified'])}}" class="btn" type="button">View more</a>
									</div>
								</div>
							</div>
						</div>
					@endif
				</div>
				<div class="row">

				</div>
			</div>
		</div>
	</div>
</div>

@endsection
