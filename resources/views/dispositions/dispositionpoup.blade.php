<div class="modal fade confirmation-model" id="DeleteDisposition">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('admin.disposition-delete-data') }}" method="POST" id="delete-disposition-form">
          <input type="hidden" value="" name="status" id="status_to_change">
        <input type="hidden" value="" name="disposition_id" id="disposition_id">
        {{ csrf_field() }}
        {{ method_field('POST') }}

        <div class="modal-body">
            <div class="mt15 text-center mb15">
              <?php echo getimage('/images/alert-danger.png') ?>
              <p class="logout-title">Are you sure?</p>
            </div>
            <div class="mt20 text-center">
              This disposition will be <span class="status-to-change-text"></span>.
            </div>
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
    $('body').on('click', '.deactive-disposition', function(e) {
        $(".ajax-error-message").html('');
        // $("#delete-disposition-form")[0].reset();
        var id = $(this).data('did');
        $('#disposition_id').val(id);
        $('.main-title').html('deactivate disposition');
        $('.status-to-change-text').html('deactivated');
        $('#status_to_change').val('inactive');
        $('#DeleteDisposition').modal();
    });

    $('body').on('click', '.active-disposition', function(e) {
        $(".ajax-error-message").html('');
        // $("#delete-disposition-form")[0].reset();
        var id = $(this).data('did');
        $('#disposition_id').val(id);
        $('.main-title').html('deactivate disposition');
        $('.status-to-change-text').html('activated');
        $('#status_to_change').val('active');
        $('#DeleteDisposition').modal();
    });

    $('body').on('click', '.delete-desposition-data', function(e) {
      // $("#delete-disposition-data-form").attr('action', $(this).data('url'));
      $(".ajax-error-message").html('');
        // $("#delete-disposition-form")[0].reset();
        var id = $(this).data('did');
        $('#disposition_id').val(id);
        $('.main-title').html('deactivate disposition');
        $('.status-to-change-text').html('deleted');
        $('#status_to_change').val('delete');
        $('#DeleteDisposition').modal();
  });

  // $('body').on('click', '.delete-desposition', function(e) {
  //   $('#DeleteDisposition').modal();
  //   var did = $(this).data('did');
  //   $('#disposition_id').val(did);
  // });
</script>


<script type="text/javascript">
  $('body').on('click', '.delete-desposition', function(e) {
    $('#DeleteDisposition').modal();
    var did = $(this).data('did');
    $('#disposition_id').val(did);
  });

  $("#delete-disposition-form").submit(function(e) {
    e.preventDefault(); // avoid to execute the actual submit of the form.

    var form = $(this);
    var url = form.attr('action');

    $.ajax({
      type: "POST",
      url: url,
      data: form.serialize(),
      success: function(response) {
        $('#DeleteDisposition').modal("hide");
        if (response.status == 'success') {
          printAjaxSuccessMsg(response.message);
          
        } else {
          printAjaxErrorMsg(response.message);
          
        }

        $('#disposition-table').DataTable().ajax.reload();
      }
    });
  });
</script>
