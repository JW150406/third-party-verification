@push('styles')
<style>
   
</style>
@endpush
<div class="team-addnewmodal v-star">
    <div class="modal fade" id="add_utility_mapping" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span><?php echo getimage("images/info-modal.png"); ?></span>Utilities Level mappings</h4>
                </div>
                <div class="ajax-error-message"></div>
                <div class="modal-body">
                    <div class="modal-form row">
                        <div class="">

                            <form enctype="multipart/form-data" id="utility-mapping-form" role="form" method="POST" action="{{ route('client.utility.store.mapping',['client' => $client_id]) }}" data-parsley-validate>
                                {{ csrf_field() }}
                                <input type="hidden" name="id" id="utility_id">
                                <div class="ajax-response"></div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group autocomplete">
                                        <input id="label" type="text" class="form-control" placeholder="Label">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group autocomplete">
                                        <input id="market" type="text" class="form-control" placeholder="Regex">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group autocomplete">
                                        <input id="regex_error_message" type="text" class="form-control" placeholder="Regex Error message">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
                                    <div class="btn-group">

                                        <button type="submit" class="btn btn-green saveDisable" id="save-utility-btn"><span class="save-text">Save</span></button>

                                        <button type="button" class="btn btn-red cancel-btn" data-dismiss="modal">Cancel</button>

                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>//
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $(document).on('click', '.add-utility-mapping', function(e) {
            $(".ajax-error-message").html('');
            $(".help-block").remove('');
            $("#all-zip").html('');
            var action_type = $(this).data('type');
            var title = $(this).data('original-title');
            $('#addnew_utility .modal-title').html(title);
            var utility_id = $(this).data('id');
            $('.close-zipcode').removeClass('vs-hidden');

            $('#add_utility_mapping').modal();
        });
    });
</script>
<!-- @include('client.utility_new.auto-suggest-zipcode') -->
@endpush
