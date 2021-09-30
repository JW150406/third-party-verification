<div class="modal fade confirmation-model" id="Deletbrandcontact">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="{{ route('brandcontact.delete') }}" method="POST">
				<input type="hidden" value="" name="id" id="contactid">
				{{ csrf_field() }}
				{{ method_field('POST') }}

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Brand Contact Action</h4>
				</div>

				<div class="modal-body">
					Are you sure you want to delete contacts of <strong class="status-change-cname"></strong>.
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