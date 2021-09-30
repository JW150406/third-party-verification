@push('styles')
<style>
   
</style>
@endpush
<div class="team-addnewmodal v-star">
    <div class="modal fade" id="view_alert_description" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span><?php echo getimage("images/info-modal.png"); ?></span>View Alert Description</h4>
                </div>
                <div class="ajax-error-message"></div>
                <div class="modal-body">
                    <div class="modal-form row">
                        <div class="alert_description_table form-field-wrapper" >
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                     <tbody>
                                        <tr class=""></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $(document).on('click', '.view-alert-description', function(e) {
            var telesales_id = $(this).data('id');
            $('#view_alert_description').modal();
            var alert_data = $(this).data('description');
            $('.alert_description_table tbody tr').html(alert_data);

        });       
    });

</script>
<!-- @include('client.utility_new.auto-suggest-zipcode') -->
@endpush
