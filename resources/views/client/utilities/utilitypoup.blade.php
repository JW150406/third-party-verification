<div class="modal fade confirmation-model" id="Deleteutility">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="{{ route('utility.delete') }}" method="POST">
				<input type="hidden" value="" name="id" id="utilityid">
				{{ csrf_field() }}
				{{ method_field('POST') }}

				<!-- <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"></h4>
				</div> -->

				<div class="modal-body text-center">
					<div class="mt15">
						<?php echo getimage('/images/alert-danger.png') ?>
						<p class="logout-title">Are you sure?</p>
					</div>
					<div class="mt20">
						Are you sure you want to delete <strong class="status-change-utilityname"></strong>?
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