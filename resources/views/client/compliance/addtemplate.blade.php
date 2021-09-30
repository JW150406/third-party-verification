<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><span><img src="/images/info-modal.png"/></span>Add New Template</h4>
			  </div>
			  <div class="modal-body">
				
				  <div class="col-xs-12 col-sm-12 col-md-12">
				  		<div class="arrow-up"></div>
					  <div class="modal-form">
						<div class="col-xs-12 col-sm-12 col-md-12">
							
                        <form  role="form" method="POST"  action="{{ route('utility.compliance-add-templates',['client_id' => $client->id, 'utility_id' => $utility->id]) }}" id="addnewtemplateform">
                            {{ csrf_field() }}
                            {{ method_field('POST') }}
                            <input type="hidden" class="clientid" name="client_id" value="{{$client_id}}">
                            <input type="hidden" class="utility_id" name="utility_id" value="{{$utility->id}}">
                            <div class="ajax-response"></div>  

								<div class="col-xs-12 col-sm-12 col-md-12">
									<div class="form-group">
										<label for="templatename">Name</label>
                                        <input class="form-control" required name="name" id="templatename" value="" type="Text" placeholder="Name"> 
										 
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-12">
									<label for="form_id">Select Form</label>
									<div class="dropdown form-group">
                                          <select class="form-control selectmenu" id="selectform4mapping" required name="form_id" >
                                                <option value="">Select</option>
                                                @foreach($forms as   $form)
                                                <option value="{{$form->id}}">{{$form->formname}}</option>
                                                @endforeach
                                        </select>
                                        <span class="invalid-feedback validation-error text-danger">
                                          <strong style="font-weight:normal;"></strong>
                                          </span>
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-12">
									<div class="form-group compliance">
										<label for="Notes">Add Fields</label>
										<textarea class="form-control" id="texttoaddforfields" rows="4" placeholder="Add Fields" style="min-height:200px"></textarea>
										<button class="addnew_workspace validate_fields" type="button"><span class="add" style="position:relative;right: 0;"><img src="/images/add.png"></span></button>
									</div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="content4maping"></div>
                                </div>
								
								<div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
									<div class="btn-group">
										
										<button type="submit" class="btn btn-green savefield" style="display:none;"  ><span class="save-text">Save</span><span class="add"><img src="/images/add.png"/></span></button>
										
										<button type="button" class="btn btn-red" data-dismiss="modal">Cancel<span class="del"><img src="/images/cancel_w.png"/></span></button>
									
									</div>
								</div>
						  </form>
							
					   </div>
					</div>
				  </div>
				  
			  </div>
			  <div class="modal-footer"></div>

<script>
 $('#selectform4mapping').select2();
  window.mapwithform = "{{ route('client.compliance-mapoptions',$client_id)}}";
 </script>
 
