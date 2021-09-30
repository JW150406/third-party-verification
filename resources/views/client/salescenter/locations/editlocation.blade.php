@extends('layouts.admin')
@section('content')
<?php 
$breadcrum = array() ;
if( Auth::user()->access_level =='tpv')
{
    $breadcrum[] = array('link' => route('client.findsalecenter'), 'text' =>  'Find Sales Center' );
    $breadcrum[] = array('link' =>  route('client.findsalecenter',['client' => $client->id]) , 'text' =>  $client->name );
   
}
if(Auth::user()->access_level =='client'){
    $breadcrum[] = array('link' =>  route('client.salescenters',$client->id) , 'text' =>  'Sales Centers' ); 
}
if(Auth::user()->access_level =='tpv' ||   Auth::user()->access_level =='client'){
    $breadcrum[] = array('link' => route('client.salescenter.show',['id' => $client->id, 'salescenter_id' => $salescenter->id ]), 'text' => $salescenter->name);
     
}
$breadcrum[] = array('link' =>  route('client.salescenter.locations',['id' => $client_id, 'salescenter_id' =>$salescenter_id  ]) , 'text' =>  'Locations' );
$breadcrum[] = array('link' => '', 'text' => $location->name);
breadcrum ($breadcrum)
?> 

 <div class="tpv-contbx edit-agentinfo">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
					  <div class="cont_bx3">
							
						  	<div class="col-xs-12 col-sm-6 col-md-6 tpv_heading">
								<h1>Edit Location Info</h1>
							</div>
							 
						  	<div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
							<!-- Nav tabs -->
							  <!-- Tab panes -->
							  <div class="tab-content">
								
							  <!--agent details starts-->
								  
								  <div class="row">
									 <div class="col-xs-12 col-sm-12 col-md-12">
										
										<div class="agent-detailform">
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

                                        @if ($message = Session::get('success'))
                                            <div class="alert alert-success">
                                                <p>{{ $message }}</p>
                                            </div>
                                        @endif
											<div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3">
                                            <?php if(Auth::user()->access_level == 'client' || Auth::user()->access_level == 'salescenter' || Auth::user()->hasPermissionTo('update-sales-center-locations')){ ?>
                                            <form    enctype="multipart/form-data" role="form" method="POST" action="">
                                            <?php }?>
                                                {{ csrf_field() }} 
                                                {{ method_field('POST') }}
                                                <input type="hidden" name="id" value="{{ $location->id }}">
                                                <input type="hidden" name="client_id" value="{{$location->client_id}}">
                                     
												  <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
												    <label for="name"></label> 
                                                    <input id="name" type="text" class="form-control" name="name" value="{{$location->name}}"required  placeholder="Salescenter Name" >
                                                    <?php echo getFormIconImage("images/form-name.png"); ?>
                                                    @if ($errors->has('name'))
                                                        <span class="help-block">
                                                        <strong>{{ $errors->first('name') }}</strong>
                                                    </span>
                                                    @endif
                                                  </div>
                                                  
												  <div class="form-group{{ $errors->has('code') ? ' has-error' : '' }}">
												    <label for="code"></label>
                                                     <input disabled="disabled" id="code" autocomplete="off" type="text" placeholder="Code" class="form-control" name="code" value="{{$location->code}}"
                                           required>
                                                    <?php echo getFormIconImage("images/code.png"); ?>
                                                    @if ($errors->has('code'))
                                                        <span class="help-block">
                                                        <strong>{{ $errors->first('code') }}</strong>
                                                       </span>
                                                    @endif
                                                  </div>
                                
												  <div class="form-group {{ $errors->has('street')   ? ' has-error' : '' }}">
													<label for="street"></label>
													<input id="street" type="text" class="form-control" name="street" value="{{$location->street}}"
                                           required placeholder="Street" >
                                                      <?php echo getFormIconImage("images/location.png"); ?> 
                                                    @if ($errors->has('street'))
                                                        <span class="help-block">
                                                        <strong>{{ $errors->first('street') }}</strong>
                                                    </span>
                                                    @endif
												  </div>
												  <div class="row">
													<div class="col-xs-12 col-sm-6 col-md-6">
														<div class="form-group {{ $errors->has('city')   ? ' has-error' : '' }}">
                                                        <label for="city"></label> 
                                                        <input id="city" type="text" class="form-control" name="city" value="{{$location->city}}" required placeholder="City" >

                                                        @if ($errors->has('city'))
                                                            <span class="help-block">
                                                            <strong>{{ $errors->first('city') }}</strong>
                                                        </span>
                                                        @endif
														</div>	  
                                                    </div>
                                                    
													<div class="col-xs-12 col-sm-6 col-md-6">
														<div class="form-group {{ $errors->has('state')   ? ' has-error' : '' }}">
														<label for="state"></label>
														<input id="state" type="text" class="form-control" name="state" value="{{$location->state}}"
                                                            required placeholder="State" maxlength="7" >

                                                        @if ($errors->has('state'))
                                                            <span class="help-block">
                                                            <strong>{{ $errors->first('state') }}</strong>
                                                        </span>
                                                        @endif
														</div> 
													</div>
												  </div>
												  <div class="row">
													<div class="col-xs-12 col-sm-6 col-md-6">
														<div class="form-group {{ $errors->has('country')   ? ' has-error' : '' }}">
														<label for="country"></label> 
                                                        <input id="country" type="text" class="form-control" name="country" value="{{$location->country}}" required placeholder="Country" >
                                                        @if ($errors->has('country'))
                                                            <span class="help-block">
                                                            <strong>{{ $errors->first('country') }}</strong>
                                                        </span>
                                                        @endif
														</div>	  
													</div>
													<div class="col-xs-12 col-sm-6 col-md-6">
														<div class="form-group {{ $errors->has('zip')   ? ' has-error' : '' }}">
														<label for="zip"></label> 
                                                        <input id="zip" type="text" class="form-control" name="zip" value="{{$location->zip}}" required placeholder="Zipcode" >

                                                        @if ($errors->has('zip'))
                                                            <span class="help-block">
                                                            <strong>{{ $errors->first('zip') }}</strong>
                                                        </span>
                                                        @endif
														</div> 
													</div>
                                                  </div>
                                                  
												  <div class="btnintable bottom_btns">
													<div class="btn-group">
                                                    <?php if(Auth::user()->access_level == 'client' || Auth::user()->access_level == 'salescenter' || Auth::user()->hasPermissionTo('update-sales-center-locations')){ ?>
                                                          <button class="btn btn-green" type="submit">Update<span class="add"><?php echo getimage("images/update_w.png") ?></span></button>
                                                    <?php } ?>
                                                         <a class="btn btn-red" href="{{ route('client.salescenter.locations',['client_id' => $client_id, 'salescenter_id' =>$salescenter_id  ]) }}"> Cancel<span class="del"><?php echo getimage("images/cancel_w.png"); ?></span></a>
                                                        
                                                        
													</div>
                                                  </div>
                                                  <?php if(Auth::user()->access_level == 'client' || Auth::user()->access_level == 'salescenter' || Auth::user()->hasPermissionTo('update-sales-center-locations') ){ ?>
                                                </form>
                                                <?php }?>
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
 
@endsection