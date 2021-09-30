

 
		 <!-- Modal Starts -->
 
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><span><?php echo getimage('images/info-modal.png'); ?></span>New Location Info</h4>
			  </div>
			  <div class="modal-body">
				
				  <div class="col-xs-12 col-sm-12 col-md-12">
				  		<div class="arrow-up"></div>
					  <div class="modal-form">
						<div class="col-xs-12 col-sm-12 col-md-12">
							
						<form class="" id="addnewlocation" enctype="multipart/form-data" role="form" method="POST" action="{{route('client.salescenter.addlocation',['client_id' => $client_id, 'salescenter_id' =>$salescenter_id ])}}">
						       {{ csrf_field() }}
                            <div class="ajax-response"></div>  
								<div class="col-xs-12 col-sm-12 col-md-12">
									<div class="form-group">
                                        <label for="name">Name</label>  
                                        <input id="name" autocomplete="off" type="text" class="form-control required" name="name" value="{{ old('name') }}"    autofocus placeholder="Name">
                                        <?php  echo getFormIconImage("images/form-name.png"); ?> 
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-12">
									<div class="form-group">
                                    <label for="clientcode">Code</label>
                                    <input id="clientcode" autocomplete="off" type="text" class="form-control required" name="code" value="{{ old('code') }}"    placeholder="Code">
                                    <?php  echo getFormIconImage("images/code.png"); ?> 
						 
									</div>
								</div>
						 
								<div class="col-xs-12 col-sm-12 col-md-12">
									<div class="form-group">
                                    <label for="exampleInputName6">Address</label>  
                                    <input id="street" autocomplete="off"  type="text" class="form-control required" name="street" value="{{ old('street') }}"  placeholder="Street" >
                                    
                                    <?php  echo getFormIconImage("images/location.png"); ?>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-6">
									<div class="form-group">
                                    <label for="city"></label> 
                                    <input id="city" autocomplete="off" type="text" class="form-control required" name="city" value="{{ old('city') }}" placeholder="City" >
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-6">
									<div class="form-group">
                                    <label for="state"></label>
                                    <input id="state" autocomplete="off" type="text" class="form-control required" name="state" value="{{ old('state') }}"  placeholder="State" >
									 
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-6">
									<div class="form-group">
                                    <label for="country"></label>
                                    <input id="country" autocomplete="off" type="text" class="form-control required" name="country" value="{{ old('country') }}" placeholder="Country" >
									</div>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-6">
									<div class="form-group">
                                    <label for="zip"></label>
                                    <input id="zip" autocomplete="off" type="text" class="form-control required" name="zip" value="{{ old('zip') }}" placeholder="Zipcode" maxlength="7" >
									 
									</div>
								</div>
								
							 
                                <div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
									<div class="col-xs-12 col-sm-12 col-md-12 text-center"> 
										<div class="btn-group"> 
                                        <button type="submit" class="btn btn-green" ><span class="save-text">Save</span> <span class="add"><?php  echo getimage("images/save.png"); ?></span></button>
									    <button type="button" class="btn btn-red" data-dismiss="modal">Cancel<span class="del"><?php  echo getimage("images/cancel_w.png"); ?></span></button>
										
										</div>
									</div>
								</div>
						  </form>
							
					   </div>
					</div>
				  </div>
				  
			  </div>
			  <div class="modal-footer"></div>
			 
	  
	  <!-- Modal ends -->
  