@push('styles')
    <style>

    </style>
@endpush
<div class="team-addnewmodal v-star">
    <div class="modal fade" id="view_utility_validation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        <span><?php echo getimage("images/info-modal.png"); ?></span>Utility Validations</h4>
                </div>
                <div class="ajax-error-message"></div>
                <div class="modal-body">
                    <div class="modal-form row">
                        <div class="utility_validation_table">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr class="acjin">
                                        <!-- <th>Utility Name</th> -->
                                        <th>Label</th>
                                        <th>Regex</th>
                                        <th>Regex Message</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if(auth()->user()->hasPermissionTo('add-utility-provider'))
                        <div class="add-utility-validatation" style="margin-top: 30px;">
                            <h5 style="margin-left: 20px; margin-bottom: 10px; font-weight: bold;">Add Validation</h5>
                            <form enctype="multipart/form-data" id="utility-validatation-form" role="form" method="POST"
                                  action="{{ route('client.utility.store.validation',['client' => $client_id]) }}"
                                  data-parsley-validate>
                                {{ csrf_field() }}
                                <input type="hidden" name="utility_id" id="utility_id_val" value="">
                                <input type="hidden" name="client_id" id="client_id" value="{{ $client_id }}">
                                <div class="ajax-response"></div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group autocomplete">
                                        <input id="label" type="text" name="label" class="form-control required"
                                               placeholder="Label" value=""
                                               data-parsley-required="true"
                                               data-parsley-required-message="Please enter label">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group autocomplete">
                                        <input id="regex" type="text" name="regex" class="form-control required"
                                               placeholder="Regex" value=""
                                               data-parsley-required="true"
                                               data-parsley-required-message="Please enter regex">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group autocomplete">
                                        <input id="regex_error_message" name="regex_error_message" type="text"
                                               class="form-control required" value=""
                                               placeholder="Regex Error message" data-parsley-required="true"
                                               data-parsley-required-message="Please enter regex error message">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
                                    <div class="btn-group">
                                        <button type="submit" class="btn btn-green saveDisable"
                                                id="save-utility-validation-btn">
                                            <span class="save-text">Save</span></button>
                                        <button type="button" class="btn btn-red cancel-btn" data-dismiss="modal">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
		$(document).ready(function () {
			$(document).on('click', '.view-utility-validation', function (e) {
				$(".ajax-error-message").html('');
				$(".help-block").remove('');
				var action_type = $(this).data('type');
				var title = $(this).data('original-title');
				$('#addnew_utility .modal-title').html(title);
				var utility_id = $(this).data('id');
				$('#utility_id_val').val(utility_id);
				$('#view_utility_validation').modal();

				getUtilityValidation(utility_id);

			});

            $("#utility-validatation-form").submit(function(e) {
				e.preventDefault();
				var $form = $('#utility-validatation-form');
				if ($form.parsley().isValid()) {
					var utility_id = $('#utility_id_val').val();
					$.ajax({
						type: "POST",
						url: $form.attr('action'),
						data: $form.serialize(), // serializes the form's elements.
						success: function (response) {
							if (response.status == 'success') {
                                $('#utility-validatation-form')[0].reset();
								printAjaxSuccessMsg(response.message);
                                $('#utility-table').DataTable().ajax.reload();
								getUtilityValidation(utility_id);
							} else {
								printAjaxErrorMsg(response.message);
							}
						},
						error: function (xhr) {
							if (xhr.status == 422) {
								printUtilityErrorMsg(form, xhr.responseJSON.errors);
							}
						}
					});
				} else {
					$form.parsley().validate();
					return false;
				}
			});

			$('#view_utility_validation').on('hidden.bs.modal', function () {
                $('#utility-validatation-form')[0].reset();
                $('#utility-validatation-form').parsley().reset();
            });
		});

		function getUtilityValidation(utility_id) {
			$.ajax({
				url: "{{route('client.utility.getvalidations')}}",
				data: {
					utility_id: utility_id
				},
				success: function (response) {
					$('.utility_validation_table tbody').html(response.data);
				},
				error: function (response) {
					$('.utility_validation_table tbody').html(response.message);
				}
			});
		}
    </script>
    @include('client.utility_new.auto-suggest-zipcode')
@endpush
