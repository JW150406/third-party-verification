<div class="modal fade confirmation-model" id="delete-lead-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <input type="hidden" value="{{ route('leadforms.changestatus') }}" id="form-changestatus-url">
            <form id="delete-lead-form" method="POST">
                {{ csrf_field() }}
                

                <input type="hidden" value="" name="id" id="formid">
                <input type="hidden" value="" name="status" id="change_status_form">

                <div class="modal-body">
                    <div class="mt15 text-center mb15">
				<?php echo getimage('/images/alert-danger.png') ?>
				<p class="logout-title">Are you sure?</p>
			</div>
                    <div class="mt20 text-center">
                        Form - <strong class="delete-form-name"></strong> will be <span class="status-to-change-text"></span>.
                    </div>
                </div>

                <div class="modal-footer">

                    <div class="btnintable bottom_btns pd0">
                        <button type="submit" class="btn btn-green">Confirm</button>
                        <button type="button" class="btn btn-red" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script type="text/javascript">
    
    $('body').on('click', '.change-status-form', function(e) {
        var id = $(this).data('id');
        $('#formid').val(id);
        $('.delete-form-name').html($(this).data('name'));
        $('#change_status_form').val($(this).data('status'));
        $('.status-to-change-text').html($(this).data('text-status'));
        $("#delete-lead-form").attr('action', $('#form-changestatus-url').val());
        $('#delete-lead-modal').modal();
    });

    $('body').on('click', '.delete-leadform', function(e) {
        
        var id = $(this).data('id');
        $('#formid').val(id);
        $('.delete-form-name').html($(this).data('name'));
        $('#change_status_form').val($(this).data('status'));
        $('.status-to-change-text').html($(this).data('text-status'));
        $("#delete-lead-form").attr('action', $('#form-changestatus-url').val());
        $('#delete-lead-modal').modal();
    });

    // $('body').on('click', '.delete-lead', function(e) {
    //     var name = $(this).closest('tr').find('td:eq(1)').html();
    //     $('.delete-lead-name').html(name);
    //     $("#delete-lead-form").attr('action', $(this).data('url'));
    //     $('#delete-lead-modal').modal();
    // });

    $(document).ready(function() {
        $("#delete-lead-form").submit(function(e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.

            var form = $(this);
            var url = form.attr('action');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(),
                success: function(response) {
                    $('#delete-lead-modal').modal("hide");
                    if (response.status == 'success') {
                        printAjaxSuccessMsg(response.message);
                    } else {
                        printAjaxErrorMsg(response.message);
                    }

                    $('#form-table').DataTable().ajax.reload();
                }
            });
        });
    });
</script>
@endpush('scripts')