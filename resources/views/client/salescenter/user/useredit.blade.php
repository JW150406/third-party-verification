@extends('layouts.admin')
@section('content')

<?php 
$breadcrum = array();
if(Auth::user()->access_level =='tpv'){
    $breadcrum[] =  array('link' => route('client.findsalecenter'), 'text' =>  'Find Sales Center' );
    $breadcrum[] =  array('link' => route('client.findsalecenter',['client' => $client->id]), 'text' =>  $client->name );
}else{
    $breadcrum[] =  array('link' =>  route('client.salescenters',$client->id), 'text' =>  'Sales Centers' );
}
$breadcrum[] = array('link' => route('client.salescenter.show',['id' => $client->id, 'salescenter_id' => $salescenter->id ]), 'text' => $salescenter->name);
$breadcrum[] = array('link' =>  route('client.salescenter.users',['client_id' => $salescenter->client_id, 'salescenterid' => $salescenter->id]), 'text' => 'Users');
$breadcrum[] = array('link' => '', 'text' => $user->first_name);
breadcrum ($breadcrum);
?> 

<div class="tpv-contbx">
   <div class="container">
      <div class="row">
         <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="cont_bx3">
               <div class="col-xs-12 col-sm-6 col-md-6 tpv_heading">
                  <h1>Edit User Info</h1>
               </div>
               <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
                  <div class="row">
                     <div class="col-xs-12 col-sm-12 col-md-12">
                       
                            <div class="agent-detailform">
                                <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3">
                                @if ($message = Session::get('success'))
                                    <div class="alert alert-success">
                                        <p>{{ $message }}</p>
                                    </div>
                                @endif
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
                                <form   role="form" method="POST" action="{{ route('client.salescenter.user.edit',['client_id' => $user->client_id,'salescenter_id'=>$user->salescenter_id, 'userid' => $user->id]) }}">
                                    {{ csrf_field() }}
                                    {{ method_field('post') }} 
                                   <div class="form-group{{  $errors->has('first_name')  ? ' has-error' : '' }}">
                                    <label for="first_name"></label>
                                    <input id="first_name" autocomplete="off" type="text" class="form-control" name="first_name" 
                                       required placeholder="First Name"  value="{{$user->first_name}}" >
                                     <?php echo getFormIconImage('images/form-name.png') ?>
                                    @if ($errors->has('first_name'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('first_name') }}</strong>
                                    </span>
                                    @endif
                                 </div>
                                 <div class="form-group{{  $errors->has('last_name')  ? ' has-error' : '' }}">
                                    <label for="last_name" ></label>
                                    <input id="last_name" autocomplete="off" type="text" class="form-control" name="last_name"   placeholder="Last Name" value="{{$user->last_name}}" >
                                    <?php echo getFormIconImage('images/form-name.png') ?>
                                    @if ($errors->has('last_name'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('last_name') }}</strong>
                                    </span>
                                    @endif
                                 </div>
                                 <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email"  ></label> 
                                    <input id="email" type="text" class="form-control" placeholder="Email"  name="email"  value="{{$user->email}}" required >
                                    <?php echo getFormIconImage('images/form-email.png') ?>
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                 </div> 
                                 <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password"  ></label> 
                                    <input id="password" type="password" class="form-control" placeholder="Password"  name="password"  value=""   >
                                    <?php echo getFormIconImage('images/form-pass.png') ?>
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                 </div>
                                 <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                    <label for="password_confirmation"  ></label> 
                                    <input id="password_confirmation" type="password" class="form-control" placeholder="Confirm Password"  name="password_confirmation"  value=""   >
                                    <?php echo getFormIconImage('images/form-pass.png') ?>
                                    @if ($errors->has('password_confirmation'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                    @endif
                                 </div>
                                 <div class="form-group text-center radio-btns flex">
                                    <label class="radio-inline">
                                    <input type="radio"  id="active"  name="status" value="active"  <?php  if($user->status == 'active') echo 'checked'; ?>>Active
                                    </label>
                                    <label class="radio-inline">
                                    <input type="radio" id="inactive" name="status" value="inactive" <?php  if($user->status=='inactive') {echo 'checked';} ?> >In-active
                                    </label>
                                </div>
                                <div class="btnintable bottom_btns">
                                <div class="btn-group">
                                <button type="submit" class="btn btn-green">
                                                    Update <span class="add"> <?php echo getimage('images/update_w.png') ?></span>
                                                </button>
                                     <a class="btn btn-red" href="{{ route('client.salescenter.users',['client_id' => $user->client_id,'salescenter_id'=>$user->salescenter_id] ) }}">Cancel<span class="del"><?php echo getimage('images/cancel_w.png') ?></span></a>
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