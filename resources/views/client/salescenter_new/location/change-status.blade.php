<div class="modal fade confirmation-model" id="deactivate-location">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('salescenters.locations.change-status',['client_id' => $client_id, 'salescenter_id' => $salecenter_id ])}}" method="POST" id="status-change-location-form">
                <input type="hidden" value="" name="status" id="status_to_change">
                <input type="hidden" value="" name="locationid" id="locationid">
                {{ csrf_field() }}
                {{ method_field('POST') }}

                <!-- <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Sales Center Action</h4>
                </div> -->

                <div class="modal-body text-center">
                    <div class="mt15"><?php echo getimage('/images/alert-danger.png') ?></div>
                    <div class="mt20">
                        Location - <strong class="status-change-location"> </strong> will be <span class="status-to-change-text"></span>.
                    </div>
                </div>

                <div class="modal-footer pd0">
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
@push('scripts')
<script>
    $('body').on('click', '.deactivate-location', function(e) {
        $('#deactivate-location').modal();
        var cid = $(this).data('cid');
        var locationName = $(this).data('locationname');
        $('#locationid').val(cid);
        $('#status_to_change').val('inactive');
        $('.status-to-change-text').html('deactivated');
        $('.status-change-location').html(locationName);


    });

    $('body').on('click', '.activate-location', function(e) {
        $('#deactivate-location').modal();
        var cid = $(this).data('cid');
        var locationname = $(this).data('locationname');
        $('#locationid').val(cid);
        $('#status_to_change').val('active');
        $('.status-to-change-text').html('activated');
        $('.status-change-location').html(locationname);
    });

    $(document).ready(function() {
        $("#status-change-location-form").submit(function(e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.

            var form = $(this);
            var url = form.attr('action');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(response) {
                    $('#deactivate-location').modal("hide");
                    if (response.status == "success") {
                        printAjaxSuccessMsg(response.message);
                    } else {
                        printAjaxErrorMsg(response.message);
                    }
                    $('#sales-center-location-table').DataTable().ajax.reload();
                }
            });
        });
    });
</script>
@endpush