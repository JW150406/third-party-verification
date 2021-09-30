@push('styles')
<style>
   
</style>
@endpush
<div class="team-addnewmodal v-star">
    <div class="modal fade" id="view_status_update" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span><?php echo getimage("images/info-modal.png"); ?></span>Lead Updates Audit Trail</h4>
                </div>
                <div class="ajax-error-message"></div>
                <div class="modal-body">
                    <div class="modal-form row">
                        <div class="status_update_table" >
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="acjin">
                                            <th>Updated By</th>
                                            <th>Updated On</th>
                                            <th>Assigned Date</th>
                                            <th>Assigned KW</th>
                                            <th>Status Update From</th>
                                            <th>Status Update To</th>
                                        </tr>
                                    </thead>
                                    <tbody>
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
        $(document).on('click', '.view-status-update', function(e) {
            $(".ajax-error-message").html('');
            $(".help-block").remove('');
            var action_type = $(this).data('type');
            var title = $(this).data('original-title');
            $('#addnew_utility .modal-title').html(title);
            var enrollment_id = $(this).data('id');

            $('#view_status_update').modal();

            $.ajax({
                url: "{{route('reports.ptm-enrollment-report.getupdates')}}",
                data: {
                    enrollment_id: enrollment_id
                },
                success: function(response) {
                    $('.status_update_table tbody').html(response.data);
                    $('.status_update_table').attr('data-mainUtitlity',enrollment_id);
                },
                error: function(response) {
                    $('.status_update_table tbody').html(response.message);
                }
            });

        });
       
    });

</script>
@endpush
