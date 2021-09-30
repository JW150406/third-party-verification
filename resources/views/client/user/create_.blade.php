@extends('layouts.admin')
@section('content')
<ol class="breadcrumb bc-3">
   <li>
      <a href="{{route('dashboard')}}"><i class="fa fa-home"></i>Home</a>
   </li>
   @if( Auth::user()->access_level =='tpv')
     <li>
        <a href="{{route('client.index')}}">Clients</a>
     </li>
     <li>
          <a href="{{ route('client.show',$client->id) }}">{{$client->name}}</a>
     </li>
     <li>
           <a href="{{ url()->previous() }}">Users</a>
     </li>
     @endif
     @if( Auth::user()->access_level !='tpv')
       <li>  
         <a href="{{ url()->previous() }}">Users</a>
       </li>
    @endif
   <li class="active">
      <strong>Add New User</strong>
   </li>
</ol>
<div class="clearfix"></div>
<h2>New User Info</h2>
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
                    </div>
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('client.storeuser',$client_id) }}">
                            {{ csrf_field() }}
                            
                            <div class="form-group{{  $errors->has('firstname')  ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">First Name</label>
                                <div class="col-md-6">
                                        <input id="firstname" autocomplete="off" type="text" class="form-control" name="first_name" value="{{ old('city') }}"
                                            required placeholder="First Name" >

                                        @if ($errors->has('firstname'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('firstname') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                             </div>
                             <div class="form-group{{  $errors->has('lastname')  ? ' has-error' : '' }}">
                             <label for="name" class="col-md-4 control-label">Last Name</label>
                                <div class="col-md-6">
                                        <input id="lastname" autocomplete="off" type="text" class="form-control" name="last_name" value="{{ old('state') }}"
                                             placeholder="Last Name" >

                                        @if ($errors->has('lastname'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('lastname') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                             </div>
                             <div class="clearfix"></div>
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-4 control-label">Title</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control" name="title" value="{{ old('title') }}"
                                            placeholder="Title">

                                    @if ($errors->has('title'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">Email</label>

                                <div class="col-md-6">
                                    <input id="email" type="text" class="form-control" name="email" value="{{ old('email') }}"
                                           required  placeholder="Email">

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                              <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Save
                                    </button>

                                    <a class="btn btn-link" href="{{ route('client.users',$client_id) }}">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                </div>
            </div>
        </div>         
 
@endsection