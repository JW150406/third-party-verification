<!-- Add client user Modal Starts -->

<div class="team-addnewmodal v-star">
    <div class="modal fade" id="workspace-create-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add Workspace</h4>
                </div>
                <div class="ajax-error-message"></div>
                <div class="modal-body">
                    <div class="modal-form row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <form  id="workspace-create-form" role="form" method="POST" action="{{route('twilio.saveWorkSpace',$client_id)}}">
                                @csrf
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="workspace_id">Workspace ID</label>
                                        <input id="workspace_id" autocomplete="off" type="text" class="form-control required" name="workspace_id" value="" >
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="workspace_name">Workspace name</label>
                                        <input id="workspace_name" autocomplete="off" type="text" class="form-control required" name="workspace_name" value="" >
                                    </div>
                                </div>
                                
                                <div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
                                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                        <div class="btn-group">
                                            <button type="submit" class="btn btn-green"><span class="save-text">Save</span></button>
                                            <button type="button" class="btn btn-red" data-dismiss="modal">Cancel</button>

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