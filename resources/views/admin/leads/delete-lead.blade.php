<div class="modal fade confirmation-model" id="delete-lead-report-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <input type="hidden" value="{{ route('telesales.deleteLead') }}" id="form-leaddelete-url">
            <form id="delete-leadreport-form" method="POST">
                {{ csrf_field() }}
                
                <input type="hidden" value="" name="id" id="leadid">

                <div class="modal-body">
                    <div class="mt15 text-center mb15">
				<?php echo getimage('/images/alert-danger.png') ?>
				<p class="logout-title">Are you sure?</p>
			</div>
                    <div class="mt20 text-center">
                        This lead will be deleted.
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
    
    $('body').on('click', '.delete-lead-report', function(e) {
        
        var id = $(this).data('id');
        $('#leadid').val(id);
        $("#delete-leadreport-form").attr('action', $('#form-leaddelete-url').val());
        $('#delete-lead-report-modal').modal();
    });

    $(document).ready(function() {
        $("#delete-leadreport-form").submit(function(e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.

            var form = $(this);
            var url = form.attr('action');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(),
                success: function(response) {
                    $('#delete-lead-report-modal').modal("hide");
                    if (response.status == 'success') {
                        printAjaxSuccessMsg(response.message);
                    } else {
                        printAjaxErrorMsg(response.message);
                    }
                    $('#lead-table').DataTable().ajax.reload();
                }
            });
        });
    });
</script>
@endpush('scripts')