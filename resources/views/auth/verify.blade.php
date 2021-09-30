@extends('layouts.login')

@section('content')

<div class="login-container">
	
	<!-- <div class="login-header login-caret">
		
		<div class="login-content">
			
			<a href="/" class="logo">
          <img src="{{asset('images/login-logo.png')}}" alt="{{ config('app.name', 'Laravel') }}" />
        </a>
			
			<h3 class="description" style="text-align: left;">Generate Password</h3>
			
			<div class="login-progressbar-indicator">
				<h3>43%</h3>
				<span>logging in...</span>
			</div>
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
           
                           <form method="POST" action="{{ url($id.'/verification/'.$user->verification_code) }}">

                          <a href="/" class="logo">
                            <img src="{{asset('images/login-logo.png')}}" alt="{{ config('app.name', 'Laravel') }}" />
                          </a>
                          <h3 class="description" style="text-align: left;">Generate Password</h3>
                           @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                            @endif
                            @if ($message = Session::get('generatepass_message'))
                                <div class="alert alert-danger">
                                    <p>{{ $message }}</p>
                                </div>
                            @endif
                            @csrf
                          
                            @if($errors->any())
                               <p class="alert alert-danger">{{$errors->first()}}</p>
                            @endif
                              <div class="form-group">
                                <label for="password" class="text-left pull-left">Password</label>
                                 <input id="password" autocomplete="off" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" value="" required autofocus>
                                 @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif  
                              </div>
                              <div class="form-group">
                                <label for="password_confirmation" class="text-left pull-left">Confirm Password</label>
                                <input id="password_confirmation" autocomplete="off" type="password" class="form-control{{ $errors->has('cpassword') ? ' is-invalid' : '' }}" name="password_confirmation" required>

                                 @if ($errors->has('password_confirmation'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif 
                              </div>
                              <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block btn-login">
                                                       Submit
                                    </button>
                                </div>
                           
                        </form>
            </div>
		
	</div>
	
</div>
@endsection

  
