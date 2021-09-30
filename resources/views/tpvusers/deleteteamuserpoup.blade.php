<div class="modal fade confirmation-model" id="DeleteTeamUser">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="delete-tpv-user" action="{{ route('teammember.remove') }}" method="POST">
        <input type="hidden" value="" name="userid" id="userid">
        {{ csrf_field() }}
        {{ method_field('POST') }}

        <!-- <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"> User Action</h4>
        </div> -->

        <div class="modal-body text-center">
          <div class="mt15 text-center mb15">
				<?php echo getimage('/images/alert-danger.png') ?>
				<p class="logout-title">Are you sure?</p>
			</div>
          Are you sure you want to delete <strong class="status-change-teamuser"></strong>?
        </div>

        <div class="modal-footer">
          <div class="btnintable bottom_btns pd0">
            <div class="btn-group">
              <button type="submit" class="btn btn-green">Confirm</button>
              <button type="button" class="btn btn-red" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  $('body').on('click', '.deleteuser', function(e) {
    $("#DeleteTeamUser").modal()
    var uid = $(this).data('uid');
    var teamuser = $(this).data('teamuser');
    $('#DeleteTeamUser #userid').val(uid);
    $('.status-change-teamuser').html(teamuser);
  });
  $(document).ready(function() {
    $("#delete-tpv-user").submit(function(e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.
            var form = $(this);
            var url = form.attr('action');
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(response) {
                    $('#DeleteTeamUser').modal("hide");
                    if (response.status == 'success') {
                        printAjaxSuccessMsg(response.message);
                    } else {
                        printAjaxErrorMsg(response.message);
                    }
                    $('#tpv-user-table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    if (xhr.status == 422) {
                        printErrorMsgNew(form, xhr.responseJSON.errors);
                    }
                }
            });
        
    });
});
</script>
