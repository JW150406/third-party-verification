<!-- Add Dispositions Modal Starts -->

<div class="team-addnewmodal v-star">
	<div class="modal fade" id="addDisposition" tabindex="-1" role="dialog" aria-labelledby="clientUserModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="clientUserModalLabel">Add Dispositions</h4>
				</div>
				<div class="ajax-error-message">
				</div>
				<div class="modal-body">
					<div class="modal-form row">
						<div class="col-xs-12 col-sm-12 col-md-12">

							<form class="" id="disposition-create-form" role="form" method="POST" action="{{route('client.dispositioncreate',['client_id'=>$client_id])}}" data-parsley-validate >
								@csrf
								<input type="hidden" name="id" id="disposition-id">
								<div class="col-xs-12 col-sm-12 col-md-12">
									<div class="form-group">
										<label for="all_disp_clients">Client</label>
										<div class="dropdown select-dropdown">
											<select id="all_disp_clients" class="select2 form-control" disabled="true">
												<option value="" selected="">{{$client->name}}</option>
											</select>
										</div>
									</div>
								</div>

								<div class="col-xs-12 col-sm-12 col-md-12">
									<div class="form-group">
										<label for="selectdispositiontype">Category</label>
										<div class="dropdown select-dropdown">
											<select name="type" id="selectdispositiontype" class="select2 form-control selectmenudisposition" disabled="true" data-parsley-required='true' data-parsley-errors-container="#select2-dispositioncategory-error-message">
												<option value="">Select</option>
												<option value="decline">Declined</option>
												<option value="customerhangup">Call Disconnected</option>
												<option value="esignature_cancel">E-signature Cancel</option>
												<option value="verified">Verified</option>
												<option value="do_not_enroll">Do Not Enroll</option>
											</select>
											<span id="select2-dispositioncategory-error-message"></span>
											@if ($errors->has('type'))
												<span class="help-block">
                                        <strong>{{ $errors->first('type') }}</strong>
                                    </span>
											@endif
										</div>
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-12">
									<div class="form-group">
										<label for="name">Description</label>
										<input id="description" autocomplete="off" type="text" class="form-control" name="description" value="{{ old('description') }}"  data-parsley-required='true'>

										@if ($errors->has('description'))
											<span class="help-block">
                                            <strong>{{ $errors->first('description') }}</strong>
                                        </span>
										@endif
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-12">
									<div class="form-group">
										<label for="all_clients">Disposition Group</label>
										<div class="dropdown select-dropdown">
											<select id="disposition_group" name="disposition_group" class="select2 form-control" data-parsley-errors-container="#select2-disposition-error-message" data-parsley-required='true'>
												<option value="" >Please select</option>
												<option value="customer" >Customer</option>
												<option value="sales_agent" >Sales Agent</option>
												<option value="lead_detail" >Lead Detail</option>
												<option value="other" >Other</option>
											</select>
											<span id="select2-disposition-error-message"></span>
										</div>
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-12">
									<div class="form-group">
										<!-- <label for="all_clients">Send Alert</label> -->
										<!-- <div class="dropdown select-dropdown"> -->
										<div class="form-group">
											<label class="checkbx-style"> Send Alert
												<input autocomplete="off" type="checkbox" name="email_alert_disposition" class="email-alert-disp">
												<span class="checkmark"></span>
											</label>
										</div>
										<!-- </div> -->
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-12">
									{{--<div class="form-group radio-btns checkbx">
										<label class="checkbx-style">Allow
											<input id="allow_cloning" autocomplete="off" type="checkbox" name="allow_cloning"  >
											<span class="checkmark"></span>
											@if ($errors->has('allow_cloning'))
												<span class="help-block">
                                                    <strong>{{ $errors->first('allow_cloning') }}</strong>
                                                </span>
											@endif

										</label>
									</div>--}}
										<div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
									<div class="col-xs-12 col-sm-12 col-md-12 text-center">
										<div class="btn-group">
											<button type="submit" class="btn btn-green"><span class="save-text">Save</span></button>
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

<!-- add client user Modal ends -->
@push('scripts')
	<script>
		$(document).ready(function() {
			$(document).on('click', '.disposition-modal', function(e) {
				$('#addDisposition .btn-green').show();
				$("#disposition-create-form")[0].reset();
				$('.select2').val('').trigger("change.select2");

				var action_type = $(this).data('type');
				var title = $(this).data('original-title');
				$('#addDisposition .modal-title').html(title);
				$("#disposition-create-form :input").prop("disabled", false);
				if (action_type == 'new') {
					$('#disposition-id').val('');
					$('#addDisposition').modal();
				} else {

					var id = $(this).data('id');
					$('#disposition-id').val(id);

					$.ajax({
						url: "{{route('client.dispositionsdata',['client_id'=>$client_id])}}",
						data: {
							disposition_id: id	
						},
						success: function(response) {
							if (response.status == 'success') {
								console.log(response.data);
								$('#selectdispositiontype').val(response.data.type).trigger('change');
								$('#description').val(response.data.description);
								$('#disposition_group').val(response.data.disposition_group).trigger('change.select2');
								if(response.data.allow_cloning == "true"){
									$("#allow_cloning").prop("checked", true);
								}else{
									$("#allow_cloning").prop("checked", false);
								}
								if(response.data.email_alert == 1){
									$(".email-alert-disp").prop("checked", true);
								}else{
									$(".email-alert-disp").prop("checked", false);
								}
							}

							$('#addDisposition').modal();
						},
						error: function(xhr) {
							console.log(xhr);
						}
					});

					if (action_type == 'view') {
						$("#disposition-create-form :input").prop("disabled", true);
						$('#addDisposition .btn-green').hide();
						$(".cancel-btn").prop("disabled", false);
					}
				}

				$("#all_clients").prop("disabled", true);
			});

			$('#addDisposition').on('hidden.bs.modal', function () {
				$('#disposition-create-form').parsley().reset();
			});

			$("#disposition-create-form").submit(function(e) {
				e.preventDefault(); // avoid to execute the actual submit of the form.

				var form = $(this);
				var url = form.attr('action');

				$.ajax({
					type: "POST",
					url: url,
					data: form.serialize(), // serializes the form's elements.
					success: function(response) {
						$('#addDisposition').modal("hide");
						if (response.status == 'success') {
							printAjaxSuccessMsg(response.message);
							
						} else {
							printAjaxErrorMsg(response.message);
						}
						$('#disposition-table').DataTable().ajax.reload();
					},
					error: function(xhr) {
						if (xhr.status == 422) {
							printErrorMsgNew(form,xhr.responseJSON.errors);
						}
					}
				});
			});
		});
	</script>
@endpush
