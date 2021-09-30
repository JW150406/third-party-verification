<div class="modal fade confirmation-model" id="DeleteclientUser">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('client.salescenter.delete',['id' => $client_id])}}" method="POST">
                <input type="hidden" value="" name="status" id="status_to_change">
                <input type="hidden" value="" name="salescenterid" id="salescenterid">
                {{ csrf_field() }}
                {{ method_field('POST') }}

                <div class="modal-body">
                    <div class="mt15 text-center mb15">
                        <?php echo getimage('/images/alert-danger.png') ?>
                        <p class="logout-title">Are you sure?</p>
                    </div>
                    Are you sure you want to <span class="status-to-change-text"></span> <strong class="status-change-clientsalescenter"></strong>?
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
    $('body').on('click', '.deactivate-clientuser', function(e) {
        $('#DeleteclientUser').modal();
        var vid = $(this).data('vid');
        var clientsalescenter = $(this).data('clientsalescenter');
        $('#salescenterid').val(vid);
        $('#status_to_change').val('inactive');
        $('.status-to-change-text').html('deactivate');
        $('.status-change-clientsalescenter').html(clientsalescenter);


    });

    $('body').on('click', '.activate-clientuser', function(e) {
        $('#DeleteclientUser').modal();
        var vid = $(this).data('vid');
        var clientsalescenter = $(this).data('clientsalescenter');
        $('#salescenterid').val(vid);
        $('#status_to_change').val('active');
        $('.status-to-change-text').html('activate');
        $('.status-change-clientsalescenter').html(clientsalescenter);
    });
</script>