<div class="modal fade confirmation-model" id="Deleteclient">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('client.statusupdate') }}" method="POST" id="status-change-client-form">
                <input type="hidden" value="" name="status" id="status_to_change">
                <input type="hidden" value="" name="cid" id="client_id">
                {{ csrf_field() }}
                {{ method_field('POST') }}

                <div class="modal-body  text-center">
                    <div class="mt15 text-center mb15">
                        <?php echo getimage('/images/alert-danger.png') ?>
                        <p class="logout-title">Are you sure?</p>
                    </div>
                    Client - <strong class="status-change-clientname"></strong> will be <span class="status-to-change-text"></span>.
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
<script>
    $('body').on('click', '.deactivate-client', function(e) {
        $('#Deleteclient').modal();
        var cid = $(this).data('cid');
        var clientname = $(this).data('clientname');
        $('#client_id').val(cid);
        $('#status_to_change').val('inactive');
        $('.status-to-change-text').html('deactivated');
        $('.status-change-clientname').html(clientname);

    });
    $('body').on('click', '.activate-client', function(e) {
        $('#Deleteclient').modal();
        var cid = $(this).data('cid');
        var clientname = $(this).data('clientname');
        $('#client_id').val(cid);
        $('#status_to_change').val('active');
        $('.status-to-change-text').html('activated');
        $('.status-change-clientname').html(clientname);
    });
    $('body').on('click', '.delete-client', function(e) {
        $('#Deleteclient').modal();
        var cid = $(this).data('cid');
        var clientname = $(this).data('clientname');
        $('#client_id').val(cid);
        $('#status_to_change').val('delete');
        $('.status-to-change-text').html('deleted');
        $('.status-change-clientname').html(clientname);

    });
    $("#status-change-client-form").submit(function(e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.

        var form = $(this);
        var url = form.attr('action');

        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(), // serializes the form's elements.
            success: function(response) {
                $('#Deleteclient').modal("hide");
                if (response.status == 'success') {
                    printAjaxSuccessMsg(response.message);
                } else {
                    printAjaxErrorMsg(response.message);
                }
                // refreshing page
                setTimeout(
                    function() 
                    {
                        location.reload();
                    }, 2000);
            }
        });
    });
</script>