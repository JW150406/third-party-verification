@push('styles')
<style>
   
</style>
@endpush
<div class="team-addnewmodal v-star">
    <div class="modal fade" id="view_utility_mapping" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span><?php echo getimage("images/info-modal.png"); ?></span>Utility Mappings</h4>
                </div>
                <div class="ajax-error-message"></div>
                <div class="modal-body">
                    <div class="modal-form row">
                        <div class="utility_mapping_table" >
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="acjin">
                                            <th>Commodity</th>
                                            <th>Brand Name</th>
                                            <th>Utility Provider</th>
                                            <th>Abbreviation</th>
                                            <th>Activate Mapping</th>
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
        $(document).on('click', '.view-utility-mapping', function(e) {
            $(".ajax-error-message").html('');
            $(".help-block").remove('');
            var action_type = $(this).data('type');
            var title = $(this).data('original-title');
            $('#addnew_utility .modal-title').html(title);
            var utility_id = $(this).data('id');

            $('#view_utility_mapping').modal();

            $.ajax({
                url: "{{route('client.utility.getmappings')}}",
                data: {
                    utility_id: utility_id
                },
                success: function(response) {
                    $('.utility_mapping_table tbody').html(response.data);
                    $('.utility_mapping_table').attr('data-mainUtitlity',utility_id);
                },
                error: function(response) {
                    $('.utility_mapping_table tbody').html(response.message);
                }
            });

        });

       
    });
        function uticheckr(e){
        // var utility_idn = $('.utility_mapping_table').data('mainUtitlity');
        var utility_idn = $(e).data('utility-id');
        var mapped_utility_id = $(e).data('id');
        var checked = $(e).is(":checked");
        $.ajax({
                url: "{{route('utility.mapping.update')}}",
                type: "POST",
                data: {
                    utility_id: utility_idn,
                    mapped_utility_id : mapped_utility_id,
                    checked : checked
                },
                success: function(response) {
                    console.log(response);
                    printAjaxSuccessMsg(response.message);
                },
                error: function(response) {
                    console.log(response);
                    printAjaxErrorMsg(response.message);
                }
            });

        }

    




</script>
@endpush
