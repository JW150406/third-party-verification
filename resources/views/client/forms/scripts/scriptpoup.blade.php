<div class="modal fade confirmation-model" id="DeleteclientFormScript">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('client.delete-forms-script',['client_id' => $client_id, 'form_id' => $form_id ]) }}" method="POST">
        <input type="hidden" value="" name="script_id" id="script_id">

        {{ csrf_field() }}
        {{ method_field('POST') }}


        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"> Script Action</h4>
        </div>

        <div class="modal-body">
          <div class="mt15 text-center mb15">
				<?php echo getimage('/images/alert-danger.png') ?>
				<p class="logout-title">Are you sure?</p>
			</div>
          Are you sure you want to delete <strong class="status-change-scriptname"></strong>?
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Confirm</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

        </div>
      </form>
    </div>
  </div>
</div>
<script>
  $('body').on('click', '.delete-script', function(e) {
    $('#DeleteclientFormScript').modal();
    var id = $(this).data('id');

    var formname = $(this).data('scriptname');
    $('#DeleteclientFormScript #script_id').val(id);
    $('.status-change-scriptname').html(formname);

  });
</script>