<!-- Add doNotEnroll Modal Starts -->

<div class="team-addnewmodal v-star">
	<div class="modal fade" id="addDoNotEnroll" tabindex="-1" role="dialog" aria-labelledby="clientUserModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="clientUserModalLabel">Add account number</h4>
				</div>
				<div class="modal-body">
					<div class="modal-form row">
						<div class="col-xs-12 col-sm-12 col-md-12">
							
							<form id="do-not-enroll-create-form" role="form" method="POST" action="{{route('do-not-enroll.create')}}" data-parsley-validate >
								@csrf
								
                                <input type="hidden" name="client_id" id="client_id" value="{{$client_id}}">
								
								<div class="col-xs-12 col-sm-12 col-md-12">
									<div class="form-group add-more-field" id="account-number-fields">
										
										<input autocomplete="off" type="text" class="form-control" name="account_number[]" value="{{ old('account_number') }}"  data-parsley-required='true' placeholder="Account Number">
										
										<input autocomplete="off" type="text" class="form-control mt15" name="account_number[]" value="{{ old('account_number') }}" placeholder="Account Number">
										<input autocomplete="off" type="text" class="form-control mt15" name="account_number[]" value="{{ old('account_number') }}" placeholder="Account Number">
										<input autocomplete="off" type="text" class="form-control mt15" name="account_number[]" value="{{ old('account_number') }}" placeholder="Account Number">
										<input autocomplete="off" type="text" class="form-control mt15" name="account_number[]" value="{{ old('account_number') }}" placeholder="Account Number">
									</div>
									
								</div>
								
								<div class="col-xs-12 col-sm-12 col-md-12" style="text-align:center; margin-bottom: -10px">
									<button type="button" class="btn btn-green" id="addMoreBtn" style="min-width: 10px;"><span class="fa fa-plus"></span></button>
								</div>

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

@push('scripts')
	<script>
		$(document).ready(function() {
			// For open modal
			$(document).on('click', '.do-not-enroll-modal', function(e) {
				$('#addDoNotEnroll .btn-green').show();
				$('#addDoNotEnroll').modal();
			});

			// For close modal
			$('#addDoNotEnroll').on('hidden.bs.modal', function () {
				$('#do-not-enroll-create-form').parsley().reset();
				$('#do-not-enroll-create-form')[0].reset();
				$('.account-number-field').remove();
				$(".help-block").remove('');
			});

			// For submit form
			$("#do-not-enroll-create-form").submit(function(e) {
				e.preventDefault(); // avoid to execute the actual submit of the form.

				var form = $(this);
				var url = form.attr('action');

				$.ajax({
					type: "POST",
					url: url,
					data: form.serialize(), // serializes the form's elements.
					success: function(response) {
						
						if (response.status == 'success') {
							$('#addDoNotEnroll').modal("hide");
							printAjaxSuccessMsg(response.message);
							
						} else if(response.status == 'validation_error') {
							console.log("response : ", response.message);
							var msg = response.message;
							$(".help-block").remove('');
							$.each(msg, function (key, value) {
								let index = key.replace("account_number.","");
								if(index == "account_number") {
									index = 0;
								}
								$('#account-number-fields :input').eq(index).after("<span class='help-block' >" + value[0] + "</span>");
							});
						} else {
							$('#addDoNotEnroll').modal("hide");
							printAjaxErrorMsg(response.message);
						}
						$('#doNotEnroll-table').DataTable().ajax.reload();
					},
					error: function(xhr) {
						if (xhr.status == 422) {
							printErrorMsgNew(form,xhr.responseJSON.errors);
						}else {
							$('#addDoNotEnroll').modal("hide");
						}
					}
				});
			});
		});


		// For append input tag on Add button
        $('#addMoreBtn').click(function () {
            $('.add-more-field').append('<input autocomplete="off" type="text" class="form-control mt15 account-number-field" name="account_number[]" placeholder="Account Number">');
        });

	</script>
@endpush
