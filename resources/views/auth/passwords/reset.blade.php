@extends('layouts.login')

@section('content')



<div class="login-container">
    <div class="login-form">
        <div class="login-content">
            <form id="form_reset" method="POST" action="{{ route('password.request') }}">
                <a href="/" class="logo">
                    <img src="{{asset('images/login-logo.png')}}" alt="{{ config('app.name', 'Laravel') }}" />
                </a>
                <p class="description">Please reset your passowrd.</p>

                @csrf
                @if(isset($client_id) && !empty($client_id) )
                <input type="hidden" name="client_id" value="{{$client_id}}">
                @endif
                @if(isset($salescenter_id) && !empty($salescenter_id) )
                <input type="hidden" name="salescenter_id" value="{{$salescenter_id}}">
                @endif
                <div class="login-heading"><?php if (isset($login_title)) echo $login_title; ?> Login</div>
                @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
                @endif
                @if($errors->any())
                <p class="alert alert-danger">{{$errors->first()}}</p>
                @endif

                <input type="hidden" name="token" value="{{ $token }}">


                <div class="form-group">

                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="entypo-user"></i>
                        </div>

                        <input type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" required="required" id="username" placeholder="E-Mail Address" autocomplete="off" name="email" value="{{ old('email') }}"  autofocus />
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
                <div class="form-group">

                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="entypo-key"></i>
                        </div>

                        <input type="password" class="form-control {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" autocomplete="off" />
                    </div>
                    <div>
                        <label id="password_confirmation-error" class="error validate-has-error float-left login-error-label" for="password_confirmation"></label>
                    </div>
                </div>
                <!-- <div class="form-group"> -->
                    <button type="submit" class="btn btn btn-green" style="margin-top:10px;margin-bottom:10px;">
                        Reset Password
                    </button>
                <!-- </div> -->

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

<script type="text/javascript">
   

var neonLogin = neonLogin || {};

;(function($, window, undefined)
{
    "use strict";
    
    $(document).ready(function()
    {
        neonLogin.$container = $("#form_reset");
        
        
        // Reset Form  Validation
        neonLogin.$container.validate({
            rules: {
                email: {
                    required: true,
                    email: true  
                },
                
                password: {
                    required: true,
                    minlength: 6
                },
                password_confirmation: {
                    required: true,
                    equalTo: "#password" 
                },
                
            },
            messages: {
                email: {
                    email: "Please enter valid email Id"
                },
                password: {
                    minlength: "The password must be at least 6 characters."
                },
                password_confirmation:{
                    equalTo: "Passwords must match"    
                }
            },
            
            highlight: function(element){
                $(element).closest('.input-group').addClass('validate-has-error');
            },
            
            
            unhighlight: function(element)
            {
                $(element).closest('.input-group').removeClass('validate-has-error');
            }
        });        
    });
    
})(jQuery, window);
</script>
@endsection