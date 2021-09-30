<div class="modal fade confirmation-model" id="Deleteprogram">
    <div class="modal-dialog">
        <div class="modal-content">
            <input type="hidden" value="{{ route('utility.program.delete') }}" id="program-delete-url">
            <input type="hidden" value="{{ route('utility.program.changestatus') }}" id="program-changestatus-url">
            <form id="delete-program-form" method="POST">
                <input type="hidden" value="" name="id" id="programid">
                <input type="hidden" value="" name="status" id="change_status_program">
                {{ csrf_field() }}
                {{ method_field('POST') }}

                <!-- <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Program Action</h4>
                </div> -->

                <div class="modal-body text-center">
                    <div class="mt15"><?php echo getimage('/images/alert-danger.png') ?></div>
                    <div class="mt20">
                        Program - <strong class="delete-program-name"></strong> will be <span class="status-to-change-text"></span>.
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
    $('body').on('click', '.change-status-program', function(e) {
        var id = $(this).data('id');
        $('#programid').val(id);
        $('.delete-program-name').html($(this).data('programname'));
        $('#change_status_program').val($(this).data('status'));
        $('.status-to-change-text').html($(this).data('text-status'));
        $("#delete-program-form").attr('action', $('#program-changestatus-url').val());
        $('#Deleteprogram').modal();


    });

    $('body').on('click', '.delete-program', function(e) {
        var id = $(this).data('id');
        $('#programid').val(id);
        $('.status-to-change-text').html('deleted');
        $('.delete-program-name').html($(this).data('programname'));
        $("#delete-program-form").attr('action', $('#program-delete-url').val());
        $('#Deleteprogram').modal();
    });

    $("#delete-program-form").submit(function(e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.
        $('#Deleteprogram').modal("hide");
        var form = $(this);
        var url = form.attr('action');

        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(),
            success: function(response) {
                if (response.status == 'success') {
                    printAjaxSuccessMsg(response.message);
                } else {
                    printAjaxErrorMsg(response.message);
                }

                $('#program-table').DataTable().ajax.reload();
            }
        });
    });
</script>