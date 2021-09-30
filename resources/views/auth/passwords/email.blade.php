@extends('layouts.login')

@section('content')



<div class="login-container">
	<div class="login-form">
		<div class="login-content">

			<form method="post" role="form" id="form_forgot_password" action="{{ route('password.email') }}">

				<a href="/" class="logo">
					<img src="{{asset('images/login-logo.png')}}" alt="{{ config('app.name', 'Laravel') }}" />
				</a>
				<p class="description">Enter your email, and we will send the reset link.</p>

				@csrf

				@if (session('status'))
				<div class="alert alert-success form-forgot password-success">
					{{ session('status') }}
				</div>
				@endif
				@if ($errors->has('email'))
				<div class="alert alert-danger">
					{{ $errors->first('email') }}
				</div>
				@endif

				@if ($message = Session::get('success'))
				<div class="alert alert-success">
					<p>{{ $message }}</p>
				</div>
				@endif

				<div class="form-steps">

					<div class="step current" id="step-1">

						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="entypo-mail"></i>
								</div>
								<input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="Email" autofocus autocomplete="off">

							</div>
							<div>
								<label id="email-error" class="error validate-has-error float-left login-error-label" for="email"></label>
							</div>
						</div>


						<button type="submit" class="btn btn btn-green">

							Submit
						</button>


					</div>

				</div>

			</form>


			<div class="login-bottom-links">

				<a href="/login" class="link">
					<i class="entypo-lock"></i>
					Return to Login Page
				</a>

				<br />

				<!-- <a href="#">ToS</a>  - <a href="#">Privacy Policy</a> -->

			</div>

		</div>

	</div>

</div>

@endsection
