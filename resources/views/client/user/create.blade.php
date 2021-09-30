<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><span><img src="{{ asset('images/info-modal.png') }}"/></span>New User Info</h4>
			  </div>
			  <div class="modal-body">
				
				  <div class="col-xs-12 col-sm-12 col-md-12">
				  		<div class="arrow-up"></div>
					  <div class="modal-form">
						<div class="col-xs-12 col-sm-12 col-md-12">
							
						<form  role="form" id="addnewclient_user" method="POST" action="{{ route('client.storeuser',$client_id) }}">
                           
                            {{ csrf_field() }}
                            <div class="ajax-response"></div>  
							   <div class="col-xs-12 col-sm-12 col-md-12">
									<div class="form-group {{  $errors->has('first_name')  ? ' has-error' : '' }}">
										<label for="first_name">First Name</label>
                                        <input id="first_name" autocomplete="off" placeholder="First Name" type="text" class="form-control required" name="first_name" value="{{ old('first_name') }}">
                                         
                                        <?php echo getFormIconImage('images/form-name.png') ?>
                                        @if ($errors->has('first_name'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('first_name') }}</strong>
                                        </span>
                                        @endif
									</div>
                                </div>   
                             <div class="col-xs-12 col-sm-12 col-md-12">
									<div class="form-group {{  $errors->has('last_name')  ? ' has-error' : '' }}">
									<label for="last_name">Last Name</label>
                                     <input id="last_name" autocomplete="off" type="text" class="form-control" name="last_name" value="{{ old('state') }}"
                                             placeholder="Last Name" value="{{ old('last_name')}}" >
                                        
                                       <?php echo getFormIconImage('images/form-name.png') ?>
                                       @if ($errors->has('last_name'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('last_name') }}</strong>
                                        </span>
                                        @endif
									</div>
                                </div>
                                 <div class="col-xs-12 col-sm-12 col-md-12">
									<div class="form-group {{  $errors->has('title')  ? ' has-error' : '' }}">
									<label for="last_name">Title</label>
                                      <input id="name" type="text" class="form-control" name="title" value="{{ old('title') }}" autocomplete="off"  placeholder="Title">

                                       <?php echo getFormIconImage('images/title.png') ?>
                                       @if ($errors->has('title'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('title') }}</strong>
                                        </span>
                                        @endif
									</div>
                                </div>
       
                               
								<div class="col-xs-12 col-sm-12 col-md-12">
									<div class="form-group">
                                        <label for="email">Email</label>
                                        <input id="email" autocomplete="off"  type="text" class="form-control required" name="email" value="{{ old('email') }}"
                                            required >
                                        <span class="form-icon"><img src="{{ asset('images/form-email.png') }}"/></span>
                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                           </span>
                                        @endif
									</div>
                                </div>
                   
                                
						  
								<div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
									<div class="btn-group"> 
										<button type="submit" class="btn btn-green" ><span class="save-text">Save</span> <span class="add"><img src="{{ asset('images/save.png')}}"/></span></button> 
										<button type="button" class="btn btn-red" data-dismiss="modal">Cancel<span class="del"><img src="{{ asset('images/cancel_w.png')}}"/></span></button>
									
									</div>
								</div>
						  </form>
							
					   </div>
					</div>
				  </div>
				  
			  </div>
			  <div class="modal-footer"></div>
			</div>
