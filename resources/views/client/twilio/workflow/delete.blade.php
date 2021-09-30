<div class="modal fade confirmation-model" id="workflow-delete-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="{{route('twilio.deleteWorkflow',$client_id)}}" method="POST" id="workflow-delete-form">
				<input type="hidden" name="id" id="delete_workflow_id">
				{{ csrf_field() }}

				<div class="modal-body text-center">
					<div class="mt15"><?php echo getimage('/images/alert-danger.png') ?></div>
					<div class="mt20">
						Workflow - <strong class="workflow-name"></strong> will be deleted.
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
	$('body').on('click', '.delete-workflow', function(e) {
		$('#workflow-delete-modal').modal();
		var id = $(this).data('id');
		$('#delete_workflow_id').val(id);
		$('.workflow-name').html($(this).data('workflow_name'));
	});
</script>