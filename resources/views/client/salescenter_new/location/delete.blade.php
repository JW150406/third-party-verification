<div class="modal fade confirmation-model" id="delete-location-model">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('salescenters.locations.delete')}}" method="POST" id="delete-location-form">
                <input type="hidden" value="" name="location_id" id="delete-location-id">
                {{ csrf_field() }}
                {{ method_field('POST') }}

                <div class="modal-body text-center">
                    <div class="mt15"><?php echo getimage('/images/alert-danger.png') ?></div>
                    <div class="mt20">
                        Sales center - <strong class="delete-name"> </strong> will be deleted.
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

    $('body').on('click', '.delete-location', function(e) {
        var id = $(this).data('id');
        var locationname = $(this).data('locationname');
        $('#delete-location-id').val(id);
        $('.delete-name').html(locationname);
        $('#delete-location-model').modal();
    });

    $(document).ready(function() {
        $("#delete-location-form").submit(function(e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.

            $('#delete-location-model').modal("hide");
            var form = $(this);
            var url = form.attr('action');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(response) {
                    if (response.status == "success") {
                        printAjaxSuccessMsg(response.message);
                    } else {
                        printAjaxErrorMsg(response.message);
                    }
                    $('#sales-center-location-table').DataTable().ajax.reload();
                    $('#sales-center-user-table').DataTable().ajax.reload();
                    $('#agent-table').DataTable().ajax.reload();
                }
            });
        });
    });
</script>
@endpush