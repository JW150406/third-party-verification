<div class="header my-account-header">
	<div class="container-fluid">
		<div class="col-xs-12 col-sm-12 col-md-12">
			<div class="row">
				<div class="col-xs-12 col-sm-2 col-md-3">
					<a href="{{ url('/') }}"><img src="{{asset('images/logo.png')}}" alt="{{ config('app.name', 'Laravel') }}" /></a>

					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#headermenu" aria-expanded="false">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>
				<div class="col-xs-12 col-sm-10 col-md-9">
					<div class="col-xs-12 col-sm-12 col-md-12 login">

						<div class="collapse navbar-collapse" id="headermenu">
							<ul class="nav agent-header-navigation">
								@guest
								<li class="active"><a href="{{ route('login') }}">Login</a></li>
								@else

								@if(Auth::user()->access_level=='salesagent')

								<li class="{{ (Request::route()->getName() == 'my-account') ? 'active' : '' }}"><a href="{{ route('my-account') }}">Dashboard</a></li>
								<li class="{{ (Request::route()->getName() == 'client.contact') ? 'active' : '' }}"><a href="{{ route('client.contact',Auth::user()->client_id) }}">Create Lead</a></li>
								<li class="{{ (Request::route()->getName() == 'profile.leads') ? 'active' : '' }}"><a href="{{ route('profile.leads') }}">My Leads</a></li>
								<li><a href="javascript:void(0);" data-toggle="modal" data-target="#clonelead">Clone</a></li>

								@endif
								<li>
									<div class="dropdown">
										<button class="btn dropdown-toggle" type="button" data-toggle="dropdown">{{ Auth::user()->full_name }}</button>
										<ul class="dropdown-menu">
											<li><a href="{{ route('editprofile') }}">Edit Profile</a></li>
											<li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
													Logout
												</a></li>
										</ul>
									</div>
								</li>
								@endguest
							</ul>
						</div>

						<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
							@csrf
						</form>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>

