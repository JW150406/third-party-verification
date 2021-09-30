<!-- Add tpv user Modal Starts -->


<div class="team-addnewmodal v-star">
	<div class="modal fade" id="addtpvuser" tabindex="-1" role="dialog" aria-labelledby="TpvUserModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="TpvUserModalLabel">Add TPV User</h4>
				</div>
				<div class="ajax-error-message">
				</div>
				<div class="modal-body">
					<div class="modal-form row">
						<div class="col-xs-12 col-sm-12 col-md-12">
									<form class="" id="tpv-user-create-form" role="form" method="POST" action="{{route('teammembers.store')}}" data-parsley-validate >
							@csrf
										<input type="hidden" name="id" id="tpv-user-id">
										<div class="col-xs-12 col-sm-12 col-md-12">
											<div class="form-group">
												<label for="tpv_first_name">First Name</label>
												<input id="tpv_first_name" data-parsley-required='true' maxlength="255" autocomplete="off" type="text" class="form-control required" name="first_name" value="">
											</div>
										</div>
										<div class="col-xs-12 col-sm-12 col-md-12">
											<div class="form-group">
												<label for="tpv_last_name">Last Name</label>
												<input id="tpv_last_name" autocomplete="off" type="text" class="form-control required" name="last_name" value="" data-parsley-required='true'  maxlength="255">
											</div>
										</div>
										<div class="col-xs-12 col-sm-12 col-md-12">
											<div class="form-group">
												<label for="tpv_email">Email</label>
												<input id="tpv_email" type="email" class="form-control required" name="email" value="" data-parsley-required='true' data-parsley-trigger="change" data-parsley-trigger="keyup" data-parsley-email data-parsley-email-message="Please enter a valid email" autocomplete="new-email">
											</div>
										</div>
										<img src="{{asset('images/table-loader.svg') }}" alt="loader1" style="display:none;height:30px; width:auto;" id="loaderImg" class="img-responsive center-block">
										
										<div class="col-xs-12 col-sm-12 col-md-12">
											<div class="form-group">
												<label for="tpv_role">Role</label>
														<select class="select2 form-control" id="tpv_role" name="roles"  data-parsley-errors-container="#select2-tpvrole-error-message" data-parsley-required='true'>
														<option value="">Select</option>
														@foreach ($roles as $key => $role)
															<option value="{{$key}}">{{$role}}</option>
														@endforeach
													</select>
													<span id="select2-tpvrole-error-message"></span>
											</div>
										</div>
										<div class="col-xs-12 col-sm-12 col-md-12 deactivated-reason">
		                                    <div class="form-group">
		                                        <label for="deactivated_reason">Reason of Deactivated/Blacklisted</label>
		                                        <textarea id="deactivated_reason_tpv" class="form-control" disabled> </textarea>
		                                    </div>
		                                </div>
										<div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
											<div class="col-xs-12 col-sm-12 col-md-12 text-center">
												<div class="btn-group">
													<button type="submit" class="btn btn-green" id="btn_save"><span class="save-text">Save</span></button>
													<button type="button" class="btn btn-red cancel-btn" data-dismiss="modal">Cancel</button>

												</div>
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

<!-- add tpv user Modal ends -->
@push('scripts')
	<script>
		$(document).ready(function() {
			
			$(document).on('click', '.tpv-user-modal', function(e) {
				$(".ajax-error-message").html('');
				$(".help-block").remove('');
				$('.deactivated-reason').hide();
				$('#addtpvuser .btn-green').show();
				$("#tpv-user-create-form")[0].reset();
				$('#tpv-user-create-form').parsley().reset();
				$("#tpv-user-create-form label").addClass('yesstar');
				var action_type = $(this).data('type');
				var title = $(this).data('original-title');
				$('#addtpvuser .modal-title').html(title);
				$("#tpv-user-create-form :input").prop("disabled", false);

				if (action_type == 'new') {
					$('#tpv-user-id').val('');
					$('#addtpvuser').modal();
				} else {
					var id = $(this).data('id');
					$('#tpv-user-id').val(id);

					var url = "{{route('admin.tpv.getTpvUser')}}";
					$.ajax({
						url: url,
						data: {
							user_id: id
						},
						success: function(response) {
							if (response.status == 'success') {
								$('#tpv_first_name').val(response.data.first_name);
								$('#tpv_last_name').val(response.data.last_name);
								$('#tpv_email').val(response.data.email);

								if(response.data.status == 'inactive' && action_type == 'view') {
	                                $('.deactivated-reason').show();
	                                $('#deactivated_reason_tpv').val(response.data.deactivationreason);  
	                            }
								if(response.userrole != null) {
									$('#tpv_role').val(response.userrole.id).trigger('change.select2');
								}

							}

							$('#addtpvuser').modal();
						},
						error: function(xhr) {
							console.log(xhr);
						}
					});

					if (action_type == 'view') {
						$("#tpv-user-create-form :input").prop("disabled", true);
						$('#addtpvuser .btn-green').hide();
						$(".cancel-btn").prop("disabled", false);
						$("#tpv-user-create-form label").removeClass('yesstar');
						$("#addtpvuser .modal-body").addClass("view-mode");
					}
				}
			});

			$("#tpv-user-create-form").submit(function(e) {
				e.preventDefault(); // avoid to execute the actual submit of the form.

				var form = $(this);
				var url = form.attr('action');
				$('#btn_save').prop("disabled", true);
                $('#loaderImg').show(); 
				$.ajax({
					type: "POST",
					url: url,
					data: form.serialize(), // serializes the form's elements.
					success: function(response) {
						$('#loaderImg').hide(); 
                        $("#btn_save").prop("disabled", false);
						$('#addtpvuser').modal("hide");
						if (response.status == 'success') {
							printAjaxSuccessMsg(response.message); 
						} else {
							printAjaxErrorMsg(response.message); 
						}
						$('#tpv-user-table,#all-user-table').DataTable().ajax.reload();
					},
					error: function(xhr) {
						if (xhr.status == 422) {
							$('#loaderImg').hide(); 
                        	$("#btn_save").prop("disabled", false);
							printErrorMsgNew(form,xhr.responseJSON.errors);
						}
					}
				});
			});
		});

		$('#addtpvuser').on('hidden.bs.modal', function () {
			$('#tpv-user-create-form').parsley().reset();
			$("#addtpvuser .modal-body").removeClass("view-mode");
		});
	</script>
@endpush
