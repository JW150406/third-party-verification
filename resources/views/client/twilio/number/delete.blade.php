<div class="modal fade confirmation-model" id="number-delete-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="{{route('twilio.deleteNumber')}}" method="POST" id="number-delete-form">
				<input type="hidden" name="id" id="delete_number_id">
				{{ csrf_field() }}

				<div class="modal-body text-center">
					<div class="mt15"><?php echo getimage('/images/alert-danger.png') ?></div>
					<div class="mt20">
						Phone number - <strong class="number-name"></strong> will be deleted.
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

<script>
	$('body').on('click', '.delete-number', function(e) {
		$('#number-delete-modal').modal();
		var id = $(this).data('id');
		$('#delete_number_id').val(id);
		$('.number-name').html($(this).data('number'));
	});
</script>