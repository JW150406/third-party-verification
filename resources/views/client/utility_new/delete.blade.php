<div class="modal fade confirmation-model" id="Deleteutility">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="{{ route('utility.delete') }}" method="POST" id="delete-utility-form">
				<input type="hidden" value="" name="id" id="utilityid">
				{{ csrf_field() }}
				{{ method_field('POST') }}

				<!-- <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Utility Action</h4>
				</div> -->

				<div class="modal-body text-center">
				<div class="mt15">
						<?php echo getimage('/images/alert-danger.png') ?>
						<p class="logout-title">Are you sure?</p>
					</div>
					<div class="mt20">
						Utility - <strong class="status-change-utilityname"></strong> will be deleted.
					</div>
				</div>

				<div class="modal-footer">
					<div class="btnintable bottom_btns pd0">
						<div class="btn-group">
							<button type="submit" class="btn btn-green">Confirm</button>
							<button type="button" class="btn btn-red" data-dismiss="modal">Cancel</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('body').on('click', '.delete-utility', function(e) {
		$('#Deleteutility').modal();
		var id = $(this).data('id');
		$('#utilityid').val(id);
		$('.status-change-utilityname').html($(this).data('utilityname'));
	});

	$("#delete-utility-form").submit(function(e) {
		e.preventDefault(); // avoid to execute the actual submit of the form.

		var form = $(this);
		var url = form.attr('action');

		$.ajax({
			type: "POST",
			url: url,
			data: form.serialize(),
			success: function(response) {
				$('#Deleteutility').modal("hide");
				if (response.status == 'success') {
					printAjaxSuccessMsg(response.message);
				} else {
					printAjaxErrorMsg(response.message);
				}

				$('#utility-table').DataTable().ajax.reload();
			}
		});
	});

	// for delete utility validations
	$('body').on('click', '.delete-utility-validation', function(e) {
		$(this).closest('tr').hide();
		var utilityValidationId = $(this).data('id');
		$.ajax({
			type: "POST",
			url: "{{route('utility.validation.delete')}}",
			data: {id: utilityValidationId },
			success: function(response) {
				if (response.status == 'success') {
					printAjaxSuccessMsg(response.message);
					$('#utility-table').DataTable().ajax.reload();
				} else {
					printAjaxErrorMsg(response.message);
				}
			}
		});
	});
</script>
