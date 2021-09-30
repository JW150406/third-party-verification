@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array(
   array('link' => route('teammembers.index'), 'text' =>  'Team Members'),
   array('link' => "", 'text' =>  $user->first_name),
);
breadcrum($breadcrum);
?>
<div class="tpv-contbx edit-agentinfo">
   <div class="container">
      <div class="row">
         <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="cont_bx3">
               <div class="col-xs-12 col-sm-12 col-md-12">
                  <div class="client-bg-white">
                     <h1>Edit Agent Info</h1>
                     <div class="sales_tablebx">
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
                        <div class="row agent-detailform">
                           <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3">
                              <form class="form-horizontal" role="form" method="POST" action="{{ route('teammember.update', $user->id) }}">
                                 {{ csrf_field() }}
                                 {{ method_field('PATCH') }}
                                 <div class="form-group{{  $errors->has('first_name')  ? ' has-error' : '' }}">
                                    <label for="first_name"></label>
                                    <input id="first_name" autocomplete="off" type="text" class="form-control" name="first_name" required placeholder="First Name" value="{{$user->first_name}}">
                                    <span class="form-icon"><img src="{{ asset('images/form-name.png')}}" /></span>
                                    @if ($errors->has('first_name'))
                                    <span class="help-block">
                                       <strong>{{ $errors->first('first_name') }}</strong>
                                    </span>
                                    @endif
                                 </div>
                                 <div class="form-group{{  $errors->has('last_name')  ? ' has-error' : '' }}">
                                    <label for="last_name"></label>
                                    <input id="last_name" autocomplete="off" type="text" class="form-control" name="last_name" placeholder="Last Name" value="{{$user->last_name}}">
                                    <span class="form-icon"><img src="{{ asset('images/form-name.png')}}" /></span>
                                    @if ($errors->has('last_name'))
                                    <span class="help-block">
                                       <strong>{{ $errors->first('last_name') }}</strong>
                                    </span>
                                    @endif
                                 </div>
                                 <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email"></label>
                                    <input id="email" type="text" class="form-control" placeholder="Email" name="email" value="{{$user->email}}" required>
                                    <span class="form-icon"><img src="{{ asset('images/form-email.png')}}" /></span>
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                       <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                 </div>
                                 <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password"></label>
                                    <input id="password" type="password" class="form-control" name="password" placeholder="Password">
                                    <span class="form-icon"><img src="{{ asset('images/form-pass.png')}}" /></span>
                                    @if ($errors->has('password'))
                                    <span class="help-block">
                                       <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                 </div>
                                 <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                    <label for="password_confirmation"></label>
                                    <input id="password_confirmation" placeholder="Confirm Password" type="password" class="form-control" name="password_confirmation">
                                    <span class="form-icon"><img src="{{ asset('images/form-pass.png')}}" /></span>
                                    @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                       <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                    @endif
                                 </div>
                                 <div class="form-group{{ $errors->has('roles') ? ' has-error' : '' }}">
                                    <label for="roles">Roles</label>
                                    <select id="role" name="roles[]" multiple style="width:100%">
                                       @foreach ($roles as $role)
                                       <option value="{{$role->id}}" {{in_array($role->id, $userRoles) ? "selected" : null}}>
                                          {{$role->display_name}}
                                       </option>
                                       @endforeach
                                    </select>
                                    @if ($errors->has('roles'))
                                    <span class="help-block">
                                       <strong>{{ $errors->first('roles') }}</strong>
                                    </span>
                                    @endif
                                 </div>
                                 <div class="btnintable bottom_btns">
                                    <div class="btn-group">
                                       <button class="btn btn-green" type="submit">Update</button>
                                       <a class="btn btn-red" href="{{ route('teammembers.index') }}" type="submit">Cancel</a>
                                    </div>
                                 </div>
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection