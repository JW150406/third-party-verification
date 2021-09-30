@extends('layouts.login')

@section('content')
<!-- <div class="preloader"><img src="{{asset('images/loader.svg')}}" alt="{{ config('app.name', 'Laravel') }}" /></div> -->
<div class="login-container">

	<!-- <div class="login-header login-caret">
		<div class="login-content">
		</div>
	</div> -->


	<!-- <div class="login-progressbar">
		<div></div>
	</div> -->

	<div class="login-form">

		<div class="login-content">

			<div class="form-login-error">
				<h3>Invalid login</h3>
				<!-- <p>Enter <strong>demo</strong>/<strong>demo</strong> as login and password.</p> -->
			</div>

			<form method="post" role="form" id="form_login" action="{{ route('login') }}">

				<a href="/" class="logo">
					<img src="{{asset('images/login-logo.png')}}" alt="{{ config('app.name', 'Laravel') }}" />
				</a>
				<h3 class="description" style="text-align: left;">Log in</h3>


				@csrf

				@if(isset($client_id) && !empty($client_id) )
				<input type="hidden" name="client_id" value="{{$client_id}}">
				@endif
				@if(isset($salescenter_id) && !empty($salescenter_id) )
				<input type="hidden" name="salescenter_id" value="{{$salescenter_id}}">
				@endif
				<!-- <div class="login-heading"><//?php if (isset($login_title)) echo $login_title; ?> Login</div> -->
				@if ($message = Session::get('success'))
				<div class="alert alert-success">
					{{ $message }}
				</div>
				@endif
				
				@if($errors->any())
				<div class="alert alert-danger">
				  {{$errors->first()}}
				</div>
				@endif


				<div class="form-group">

					<div class="input-group">
						<div class="input-group-addon">
							<i class="entypo-user"></i>
						</div>

						<input type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" required="required" id="username" placeholder="Username" autocomplete="off" name="email" value="{{ old('email') }}" autofocus />
					</div>
					<div>
						<label id="username-error" class="error validate-has-error float-left login-error-label" for="username"></label>
					</div>
				</div>

				<div class="form-group">

					<div class="input-group">
						<div class="input-group-addon">
							<i class="entypo-key"></i>
						</div>

						<input type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" id="password" placeholder="Password" autocomplete="off" />
					</div>
					<div>
						<label id="password-error" class="error validate-has-error float-left login-error-label" for="password"></label>
					</div>
				</div>


				<button type="submit" class="btn btn-green">
					Submit
				</button>

			</form>


			<div class="login-bottom-links">

				<a href="{{ route('password.request') }}" class="link">Forgot your password?</a>

				<br />

				<!-- <a href="#">ToS</a>  - <a href="#">Privacy Policy</a> -->

			</div>

		</div>

	</div>

</div>
<script type="text/javascript">
	window.setTimeout(function () {
        $(".alert-success,.alert-danger").fadeTo(500, 0).slideUp(500, function () {
            $(this).remove();
        });
    }, 8000);
</script>
@endsection