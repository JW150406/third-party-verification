<!-- Modal Starts -->

<div class="team-addnewmodal">
  <div class="modal fade" id="addtpvagent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel"><span><img src="{{ asset('images/info-modal.png') }}" /></span>New Agent Info</h4>
        </div>
        <div class="modal-body">

          <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="arrow-up"></div>
            <div class="modal-form">
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="col-xs-12 col-sm-12 col-md-12">

                  <form class="form-horizontal" id="addnewtpvagent" role="form" method="POST" action="{{ route('tpvagents.index') }}">
                    {{ csrf_field() }}
                    <div class="ajax-response"></div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                      <div class="form-group {{  $errors->has('first_name')  ? ' has-error' : '' }}">
                        <label for="first_name">First Name</label>
                        <input id="first_name" autocomplete="off" placeholder="First Name" type="text" class="form-control required" name="first_name" value="{{ old('first_name') }}">
                        <span class="form-icon"><img src="{{ asset('images/form-name.png') }}" /></span>
                        @if ($errors->has('first_name'))
                        <span class="help-block">
                          <strong>{{ $errors->first('first_name') }}</strong>
                        </span>
                        @endif
                      </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                      <div class="form-group {{  $errors->has('last_name')  ? ' has-error' : '' }}">
                        <label for="last_name">Last Name</label>
                        <input id="last_name" autocomplete="off" type="text" class="form-control" name="last_name" value="{{ old('state') }}" placeholder="Last Name" value="{{ old('last_name')}}">
                        <span class="form-icon"><img src="{{ asset('images/form-name.png') }}" /></span>
                        @if ($errors->has('last_name'))
                        <span class="help-block">
                          <strong>{{ $errors->first('last_name') }}</strong>
                        </span>
                        @endif
                      </div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-12">
                      <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" placeholder="Email" autocomplete="off" type="text" class="form-control required" name="email" value="{{ old('email') }}" required>
                        <span class="form-icon"><img src="{{ asset('images/form-email.png') }}" /></span>
                        @if ($errors->has('email'))
                        <span class="help-block">
                          <strong>{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                      </div>
                    </div>

                    <div class="col-xs-12 col-sm-6 col-md-6">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Twilio Workspace</label>
                        <select name="twilio_ids[workspace_id][]" id="workspace_select" class="selectmenu form-control">
                          <option value="">Select</option>
                          @if(count($client_workspaces) > 0)
                          @foreach($client_workspaces as $clientworkspace)
                          <option value="{{ $clientworkspace->workspace_id }}">{{ $clientworkspace->workspace_name }}</option>
                          @endforeach
                          @endif
                        </select>
                      </div>
                    </div>

                    <div class="col-xs-12 col-sm-6 col-md-6">
                      <label for="twilio_worker_id">Worker Id</label>
                      <input type="text" id="twilio_worker_id" class="form-control" name="twilio_ids[worker_id][]" value="" placeholder="Worker ID">
                      <button class="addnew_workspace  create-twilio-record" type="button">
                        <span class="add"> <?php echo getimage("images/add_green.png"); ?></span>
                      </button>
                    </div>

                    <div class="row">
                      <div class="appendnewtwilioids">
                      </div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
                      <div class="btn-group mt30">
                        <button type="submit" class="btn btn-green"><span class="save-text">Save</span> </button>
                        <button type="button" class="btn btn-red" data-dismiss="modal">Cancel</button>
                      </div>
                    </div>
                  </form>

                </div>
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer"></div>
      </div>
    </div>
  </div>
</div>

<!-- Modal ends -->