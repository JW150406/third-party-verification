@extends('layouts.login')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Generate Password</div>

                <div class="card-body">
                  <div class="panel-body">
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
                           <form method="POST" action="{{ url($user->company_id.'/verification/'.$user->verification_code) }}">
                            @csrf
                          
                            @if($errors->any())
                               <p class="alert alert-danger">{{$errors->first()}}</p>
                            @endif
                              <div class="form-group">
                                <label for="password">Password</label>
                                 <input id="password" autocomplete="off" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" value="" required autofocus>
                                 @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif  
                              </div>
                              <div class="form-group">
                                <label for="password_confirmation">Confirm Password</label>
                                <input id="password_confirmation" autocomplete="off" type="password" class="form-control{{ $errors->has('cpassword') ? ' is-invalid' : '' }}" name="password_confirmation" required>

                                 @if ($errors->has('password_confirmation'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif 
                              </div>
                           <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        </form>
                      </div>
                   



                     
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
