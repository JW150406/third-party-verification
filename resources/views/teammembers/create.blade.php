@extends('layouts.admin')
@section('content')
<ol class="breadcrumb bc-3">
   <li>
      <a href="{{route('dashboard')}}"><i class="fa fa-home"></i>Home</a>
   </li>
   <li>
      <a href="{{ url()->previous() }}">Team Members</a>
   </li>
    <li class="active">
      <strong>Add New User</strong>
   </li>
</ol>

<div class="clearfix"></div>
<h2>Add User Info</h2>
<br />
 
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                <div class="panel-heading">
							<div class="panel-title">
								User Detail
							</div>
				 	</div>

                    <div class="panel-body">
                        <!-- Display Validation Errors -->
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form class="form-horizontal" role="form" method="POST" action="{{ route('teammembers.index') }}">
                            {{ csrf_field() }}
                            <div class="form-group{{  $errors->has('first_name')  ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">First Name</label>
                                <div class="col-md-6">
                                        <input id="first_name" autocomplete="off" type="text" class="form-control" name="first_name" value="{{ old('city') }}"
                                            required placeholder="First Name"  value="{{ old('first_name')}}" >

                                        @if ($errors->has('first_name'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('first_name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                             </div>
                             <div class="form-group{{  $errors->has('last_name')  ? ' has-error' : '' }}">
                             <label for="name" class="col-md-4 control-label">Last Name</label>
                                <div class="col-md-6">
                                        <input id="last_name" autocomplete="off" type="text" class="form-control" name="last_name" value="{{ old('state') }}"
                                             placeholder="Last Name" value="{{ old('last_name')}}" >

                                        @if ($errors->has('last_name'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('last_name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                             </div>
                             <div class="clearfix"></div>
                            
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">Email:</label>

                                <div class="col-md-6">
                                    <input id="email" placeholder="Email"   type="text" class="form-control" name="email" value="{{ old('email') }}"
                                           required >

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('roles') ? ' has-error' : '' }}">
                                <label for="roles" class="col-md-4 control-label">Roles</label>

                                <div class="col-md-6">
                                    <select id="role" name="roles[]" multiple>
                                    @foreach ($roles as $key => $role)
                                            <option value="{{$key}}">{{$role}}</option>
                                    @endforeach
                                    </select>


                                    @if ($errors->has('roles'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('roles') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>



                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Save
                                    </button>

                                    <a class="btn btn-link" href="{{ route('teammembers.index') }}">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
 
@endsection