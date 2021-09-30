 <!-- Modal Starts -->

 <div class="team-addnewmodal">
 	<div class="modal fade" id="addclientnew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
 		<div class="modal-dialog" role="document">
 			<div class="modal-content">
 				<div class="modal-header">
 					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
 					<h4 class="modal-title" id="myModalLabel">New Client Info</h4>
 				</div>
 				<div class="modal-body">

 					<div class="col-xs-12 col-sm-12 col-md-12">
 						<div class="arrow-up"></div>
 						<div class="modal-form">
 							<div class="col-xs-12 col-sm-12 col-md-12">

 								<form class="" id="addnewclient" enctype="multipart/form-data" role="form" method="POST" action="{{ route('client.index') }}">
 									{{ csrf_field() }}
 									<div class="ajax-response"></div>
 									<div class="col-xs-12 col-sm-12 col-md-12">
 										<div class="form-group">
 											<label for="name">Client Name</label>
 											<input id="name" autocomplete="off" type="text" class="form-control required" name="name" value="{{ old('name') }}" autofocus placeholder="Name">
 											<?php echo getFormIconImage("images/form-name.png"); ?>
 										</div>
 									</div>
 									<div class="col-xs-12 col-sm-12 col-md-12">
 										<div class="form-group">
 											<label for="clientcode">Client Code</label>
 											<input id="clientcode" autocomplete="off" type="text" class="form-control required" name="code" value="{{ old('code') }}" placeholder="Code">
 											<?php echo getFormIconImage("images/code.png"); ?>

 										</div>
 									</div>
 									<div class="col-xs-12 col-sm-6 col-md-6">
 										<div class="form-group">
 											<label for="workspace_id">Twilio Workspace ID</label>
 											<input id="workspace_id" autocomplete="off" type="text" name="workspace_id[]" value="" required placeholder="Workspace ID">
 										</div>
 									</div>
 									<div class="col-xs-12 col-sm-6 col-md-6">
 										<div class="form-group">
 											<label for="workspace_name">Twilio Workspace Name</label>
 											<input id="workspace_name" autocomplete="off" type="text" name="workspace_name[]" value="" required placeholder="Workspace Name">
 											<button class="addnew_workspace add-client-workspace" type="button">
 												<span class="add"><?php echo getimage("images/add.png"); ?></span>
 											</button>
 										</div>
 									</div>
 									<div class="append-client-workspace-id">
 									</div>
 									<div class="col-xs-12 col-sm-12 col-md-12">
 										<div class="form-group">
 											<label for="exampleInputName6">Client Address</label>
 											<input id="street" autocomplete="off" type="text" class="form-control required" name="street" value="{{ old('street') }}" placeholder="Street">

 											<?php echo getFormIconImage("images/location.png"); ?>
 										</div>
 									</div>
 									<div class="col-xs-12 col-sm-6 col-md-6">
 										<div class="form-group">
 											<label for="city"></label>
 											<input id="city" autocomplete="off" type="text" class="form-control required" name="city" value="{{ old('city') }}" placeholder="City">
 										</div>
 									</div>
 									<div class="col-xs-12 col-sm-6 col-md-6">
 										<div class="form-group">
 											<label for="state"></label>
 											<input id="state" autocomplete="off" type="text" class="form-control required" name="state" value="{{ old('state') }}" placeholder="State">

 										</div>
 									</div>
 									<div class="col-xs-12 col-sm-6 col-md-6">
 										<div class="form-group">
 											<label for="country"></label>
 											<input id="country" autocomplete="off" type="text" class="form-control required" name="country" value="{{ old('country') }}" placeholder="Country">
 										</div>
 									</div>
 									<div class="col-xs-12 col-sm-6 col-md-6">
 										<div class="form-group">
 											<label for="zip"></label>
 											<input id="zip" autocomplete="off" type="text" class="form-control required" name="zip" value="{{ old('zip') }}" placeholder="Zipcode" maxlength="7">

 										</div>
 									</div>

 									<div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
 										<div class="col-xs-12 col-sm-12 col-md-12 browselogo">
 											<p class="browselogo">Client Logo</p>
 											<div class="btn-group">
 												<input id="clientlogo" class="file2 inline btn btn-purple" data-label='Browse<span class="browse"><?php echo getimage("images/browse_w.png"); ?></span>' name="clientlogo" required type="file">
 											</div>
 										</div>
 									</div>
 									<div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
 										<div class="col-xs-12 col-sm-12 col-md-12 text-center">
 											<div class="btn-group">
 												<button type="submit" class="btn btn-green"><span class="save-text">Save</span></button>
 												<button type="button" class="btn btn-red" data-dismiss="modal">Cancel</button>

 											</div>
 										</div>
 									</div>
 								</form>
 							</div>
 						</div>
 					</div>

 				</div>
 				<div class="modal-footer"></div>
 			</div>
 		</div>
 	</div>
 </div>

 <!-- Modal ends -->
