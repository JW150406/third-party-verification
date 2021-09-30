<div class="modal fade confirmation-model" id="DeleteclientUser">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('client.salescenter.delete',['id' => $client_id])}}" method="POST" id="status-change-salescenter-form">
                <input type="hidden" value="" name="status" id="status_to_change_salescenter">
                <input type="hidden" value="" name="salescenterid" id="salescenterid">
                {{ csrf_field() }}
                {{ method_field('POST') }}

                <div class="modal-body text-center">
                <div class="mt15 text-center mb15">
                        <?php echo getimage('/images/alert-danger.png') ?>
                        <p class="logout-title">Are you sure?</p>
                    </div>
                    <div class="mt20">
                        Sales center -  <strong class="status-change-clientsalescenter"> </strong> will be <span class="status-to-change-text"></span>.
                    </div>
                </div>

                <div class="modal-footer pd0">
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
@push('scripts')
<script>
    $('body').on('click', '.deactivate-clientuser', function(e) {
        $('#DeleteclientUser').modal();
        var id = $(this).data('id');
        var clientsalescenter = $(this).data('clientsalescenter');
        $('#salescenterid').val(id);
        $('#status_to_change_salescenter').val('inactive');
        $('.status-to-change-text').html('deactivated');
        $('.status-change-clientsalescenter').html(clientsalescenter);


    });

    $('body').on('click', '.activate-clientuser', function(e) {
        $('#DeleteclientUser').modal();
        var id = $(this).data('id');
        var clientsalescenter = $(this).data('clientsalescenter');
        $('#salescenterid').val(id);
        $('#status_to_change_salescenter').val('active');
        $('.status-to-change-text').html('activated');
        $('.status-change-clientsalescenter').html(clientsalescenter);
    });

    $('body').on('click', '.delete-sales-center', function(e) {
        $('#DeleteclientUser').modal();
        var id = $(this).data('id');
        var clientsalescenter = $(this).data('clientsalescenter');
        $('#salescenterid').val(id);
        $('#status_to_change_salescenter').val('delete');
        $('.status-to-change-text').html('deleted');
        $('.status-change-clientsalescenter').html(clientsalescenter);
    });
    $(document).ready(function() {
        $("#status-change-salescenter-form").submit(function(e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.
            $('#DeleteclientUser').modal("hide");
            var form = $(this);
            var url = form.attr('action');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(response) {
                    if (response.status == 'success') {
                        printAjaxSuccessMsg(response.message);
                    } else {
                        printAjaxErrorMsg(response.message);
                    }
                    $('#sales-center-table').DataTable().ajax.reload();
                }
            });
        });
    });
</script>
@endpush