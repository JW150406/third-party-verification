<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><span><?php echo getimage("images/info-modal.png") ?></span>New Agent Detail</h4>
			  </div>
			  <div class="modal-body">
				
				  <div class="col-xs-12 col-sm-12 col-md-12">
				  		<div class="arrow-up"></div>
					  <div class="modal-form">
						<div class="col-xs-12 col-sm-12 col-md-12">
							
                           <form id="addnewagentform" role="form" method="POST" action="{{ route('client.salescenter.addsalesagent',['client_id' => $client_id, 'salescenter_id' =>$salescenter_id  ])  }}">
                             {{ csrf_field() }}
                              {{ method_field('POST') }}
                              <div class="ajax-response"></div>  
								<div class="col-xs-12 col-sm-12 col-md-12">
									<div class="form-group">
										<label for="firstname">First Name</label>
										<input id="firstname" autocomplete="off" type="text" class="form-control required" name="first_name" value="{{ old('city') }}"
                                            required placeholder="First Name" > 
                                        <?php echo getFormIconImage("images/form-name.png"); ?>
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-12">
									<div class="form-group">
									<label for="lastname">Last Name</label>
									<input id="lastname" autocomplete="off" type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" placeholder="Last Name" >
									<?php echo getFormIconImage("images/form-name.png"); ?>
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-12">
									<div class="form-group">
									<label for="email">Email</label>
                                    <input id="email" type="text" class="form-control required" name="email" value="{{ old('email') }}"   required >
									<?php echo getFormIconImage("images/form-email.png"); ?>
									</div>
                                </div>
                                
								<div class="col-xs-12 col-sm-6 col-md-6">
									 
									<select name="location" class="form-control selectmenu_location"  >
                                        <option value=""> Select Location </option>
                                        @foreach($locations as $location)
                                        <option value="{{$location->id}}"  <?php if($location_id == $location->id){ echo "selected='selected'";} ?>  > {{$location->name}} </option>
                                        @endforeach
                                      </select>
									 
								</div>
								<div class="col-xs-12 col-sm-6 col-md-6">
                                        <select name="formid" class="selectmenu_location" id="" >
                                            <option value="">Select Form</option>
                                            @if(count($clientsforms) > 0)
                                                @foreach($clientsforms as $form)
                                                <option value="{{ $form->id }}" >{{ $form->formname }}</option>
                                                @endforeach
                                            @endif 
                                        </select>
								</div>
							 
								<div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
									<div class="btn-group mt30">
										
										<button type="submit" class="btn btn-green" >Save</button>
										
										<button type="button" class="btn btn-red" data-dismiss="modal">Cancel</button>
									
									</div>
								</div>
						  </form>
							
					   </div>
					</div>
				  </div>
				  
			  </div>
              <div class="modal-footer"></div> 
<script>
 $('.selectmenu_location').select2();
</script>