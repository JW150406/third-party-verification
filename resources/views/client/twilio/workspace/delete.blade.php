<div class="modal fade confirmation-model" id="workspace-delete-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="{{route('twilio.deleteWorkSpace',$client_id)}}" method="POST" id="workspace-delete-form">
				<input type="hidden" name="workspace_id" id="delete_workspace_id">
				{{ csrf_field() }}
				<div class="modal-body text-center">
					<div class="mt15"><?php echo getimage('/images/alert-danger.png') ?></div>
					<div class="mt20">
						Are you sure you want to delete <strong class="workspace-name"></strong>?
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