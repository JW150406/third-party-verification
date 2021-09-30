@extends('layouts.myaccount')

@section('content')
<div class="tpv-contbx edit-agentinfo">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
					  <div class="cont_bx3">
							
						  	<div class="col-xs-12 col-sm-6 col-md-6 tpv_heading">
								<h1>Edit Profile ({{$user->userid}})</h1>
							</div>
							
						  	<div class="edit_twilio">
								
								
							</div>
							
						  	<div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
							<!-- Nav tabs -->
							  <!-- Tab panes -->
							  <div class="tab-content">
								
							  <!--agent details starts-->
								  
								  <div class="row">
									 <div class="col-xs-12 col-sm-12 col-md-12">
										
										<div class="agent-detailform">
                                           
                                              @if($user->access_level == 'salesagent')
                                                @if( $client_image_url != "")
                                                <div class="col-sm-3 col-xs-6">
                                                   <img src="<?php echo $url = Storage::url($client_image_url); ?>" style="margin-top: 5px;" class="img-responsive">
                                                </div>
                                                @endif
                                                <div class="col-sm-3 col-xs-6 pull-right edit-profile-problems">
                                                    <span>Problems?</span>
                                                <ul>
                                                    <li><a href="mailto:kiral.desai@contactpoint360.com" alt="Feel free to contact us" title="Feel free to contact us"><i class="fa fa-envelope-open"></i></a></li>
                                                    <!-- <li><a href="#"><i class="fa fa-phone"></i></a></li> -->
                                                </ul>

                                            </div>	
                                           
                                            <div class="clearfix"></div>
                                            @endif
											<div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2  @if($user->access_level == 'salesagent') salesagenteditprofile @endif">
												<!-- Display Validation Errors -->
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
                                                
                                                <form   role="form" method="POST" action="">
                                                    {{ csrf_field() }}
                                                    {{ method_field('post') }}
												  <div class="form-group {{  $errors->has('first_name')  ? ' has-error' : '' }}">
												    <label for="first_name">Name</label> 
                                                    <input id="first_name" autocomplete="off" type="text" class="form-control" name="first_name"  placeholder="First Name"  value="{{$user->first_name}} {{$user->last_name}}" disabled>

                                                    @if ($errors->has('first_name'))
                                                        <span class="help-block">
                                                        <strong>{{ $errors->first('first_name') }}</strong>
                                                    </span>
                                                    @endif
												  </div>
												  <div class="form-group">
                                                    <label for="email">E-Mail</label>
                                                    <input id="email" type="text" class="form-control" name="email"  value="{{$user->email}}" disabled > 
                                                        @if ($errors->has('email'))
                                                            <span class="help-block">
                                                            <strong>{{ $errors->first('email') }}</strong>
                                                        </span>
                                                        @endif 
                                                  </div>
                                                  @if($user->access_level == 'company')
                                                    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                                        <label for="title" >Title</label>
                                                            <input id="title" type="text" class="form-control" name="title"   value="{{$user->title}}"  >
                                                            @if ($errors->has('title'))
                                                                <span class="help-block">
                                                                <strong>{{ $errors->first('title') }}</strong>
                                                            </span>
                                                            @endif
                                                    </div>
                                                    @endif
                                                  


												  <div class="form-group password">
												    <label for="password">Password</label>
                                                    <input id="password" type="password" class="form-control" name="password" >
                                                    @if ($errors->has('password'))
                                                        <span class="help-block">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                    @endif
												  </div>
												  <div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
												    <label for="password_confirmation">Confirm Password</label> 
                                                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" autofocus>
                                                    @if ($errors->has('password_confirmation'))
                                                        <span class="help-block">
                                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                    </span>
                                                    @endif
												  </div>
												  <div class="btnintable bottom_btns">
													<div class="btn-group">
														<button class="btn btn-green" type="submit">Update</button>
													</div>
												  </div>
												</form>
                                            </div>
                                            
										</div>
										
									</div>
								  </div>
								  
							  <!--agent details ends-->
								
							 </div>

						</div>
						  
					</div>
				</div>
			</div>
		</div>
	</div>
 
                   
<script>
setTimeout(() => {
$('.alert').hide();
}, 3000);
</script>
@endsection
