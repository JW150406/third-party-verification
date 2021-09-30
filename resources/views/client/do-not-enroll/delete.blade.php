<div class="modal fade confirmation-model" id="deleteDoNotEnroll">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('do-not-enroll.delete') }}" method="POST" id="delete-do-not-enroll-form">
            <input type="hidden" value="" name="doNotEnrollId" id="doNotEnrollId">
            {{ csrf_field() }}
            {{ method_field('POST') }}

            <div class="modal-body">
                <div class="mt15 text-center mb15">
                    <?php echo getimage('/images/alert-danger.png') ?>
                    <p class="logout-title">Are you sure?</p>
                </div>
                <div class="mt20 text-center">
                    This account number will be <span class="status-to-change-text"></span>.
                </div>
            </div>

            <div class="modal-footer">
                <div class="btnintable bottom_btns pd0">
                    <div class="btn-group">
                    <button type="submit" class="btn btn-green">Confirm</button>
                    <button type="button" class="btn btn-red" data-dismiss="modal">Close</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
<script>
    
</script>


<script type="text/javascript">

    $('body').on('click', '.delete-do-not-enroll-data', function(e) {
        $(".ajax-error-message").html('');
        var id = $(this).data('did');
        $('#doNotEnrollId').val(id);
        $('.status-to-change-text').html('deleted');
        $('#status_to_change').val('delete');
        $('#deleteDoNotEnroll').modal();
    });

    $('body').on('click', '.delete-do-not-enroll', function(e) {
        $('#deleteDoNotEnroll').modal();
        var did = $(this).data('did');
        $('#doNotEnrollId').val(did);
    });

    $("#delete-do-not-enroll-form").submit(function(e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.

        var form = $(this);
        var url = form.attr('action');

        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(),
            success: function(response) {
            $('#deleteDoNotEnroll').modal("hide");
            if (response.status == 'success') {
                printAjaxSuccessMsg(response.message);
            } else {
                printAjaxErrorMsg(response.message); 
            }
            $('#doNotEnroll-table').DataTable().ajax.reload();
            }
        });
    });
</script>
