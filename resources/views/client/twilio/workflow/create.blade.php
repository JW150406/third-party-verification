<!-- Add client user Modal Starts -->

<div class="team-addnewmodal v-star">
    <div class="modal fade" id="workflow-create-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add Workflow</h4>
                </div>
                <div class="ajax-error-message"></div>
                <div class="modal-body">
                    <div class="modal-form row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <form id="workflow-create-form" role="form" method="POST" action="{{route('twilio.saveWorkflow',$client_id)}}" data-parsley-validate >
                                @csrf
                                <input type="hidden" name="id" id="workflow_unique_id">
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="workflow_id" class="yesstar">Workflow ID</label>
                                        <input id="workflow_id" autocomplete="off" type="text" class="form-control required" name="workflow_id" value="" data-parsley-required='true' data-parsley-required-message="Please enter a workflow ID">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="workflow_name" class="yesstar">Workflow name</label>
                                        <input id="workflow_name" autocomplete="off" type="text" class="form-control required" name="workflow_name" value="" data-parsley-required='true' data-parsley-required-message="Please enter a workflow name" >
                                    </div>
                                </div>
                                
                                <div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
                                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                        <div class="btn-group">
                                            <button type="submit" class="btn btn-green"><span class="save-text">Save</span></button>
                                            <button type="button" class="btn btn-red workflow-cancel" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- add client user Modal ends -->


@push('scripts')
<script>
    $('#workflow-create-modal').on('hidden.bs.modal', function () {
        $('#workflow-create-form').parsley().reset();
    });
</script>
@endpush