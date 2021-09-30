<!-- Add client user Modal Starts -->
<?php
if(isset($client_id) && !empty($client_id)){
  $client_data = $client_id;
}else{
  $client_data = 0;
}?>

<div class="team-addnewmodal v-star">
  <div class="modal fade" id="tpvAgentModal" tabindex="-1" role="dialog" aria-labelledby="TpvAgentModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="TpvAgentModalLabel">Add TPV Agent</h4>
        </div>
        <div class="ajax-error-message">
        </div>
        <div class="modal-body">
          <div class="modal-form row">
            <div class="col-xs-12 col-sm-12 col-md-12">
              <form class="" id="tpv-agent-create-form" role="form" method="POST" action="{{route('allagents.store')}}" data-parsley-validate >

                @csrf
                <input type="hidden" name="id" id="client-user-id">
                <div class="row"> 
                    <div class="col-xs-12 col-sm-12 col-md-12 user-id-section">
                      <div class="form-group">
                        <label for="tpv_agent_userid">ID</label>
                        <input id="tpv_agent_userid"  type="text" class="form-control" >
                      </div>
                    </div>
                </div>
                <div class="row"> 
                    <div class="col-xs-12 col-sm-12 col-md-12">
                      <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input id="first_name" data-parsley-required='true'  maxlength="255" autocomplete="off" type="text" class="form-control required" name="first_name" value="">
                      </div>
                    </div>
                </div>
                <div class="row"> 
                    <div class="col-xs-12 col-sm-12 col-md-12">
                      <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input id="last_name" autocomplete="off" type="text" class="form-control required" name="last_name" value="" data-parsley-required='true'  maxlength="255">
                      </div>
                    </div>
                </div>
                <img src="{{asset('images/table-loader.svg') }}" alt="loader1" style="display:none;height:30px; width:auto;" id="loaderImg" class="img-responsive center-block">
                <div class="row"> 
                    <div class="col-xs-12 col-sm-12 col-md-12">
                      <div class="form-group">
                        <label for="user_email">Email</label>
                        <input id="user_email" type="email" autocomplete="new-email" class="form-control required" name="email" value=""  data-parsley-required='true'    data-parsley-trigger="keyup" data-parsley-type="email" data-parsley-type-message="Please enter a valid email" data-parsley-trigger="change">
                      </div>
                    </div>
                </div>
                <div class="row">                
                    <div class="col-xs-12 col-sm-12 col-md-12" id="password-row">
                        <div class="form-group">
                            <span toggle="#agent-password" class="fa fa-fw fa-eye add-sls-show-password toggle-password"></span>
                            <label for="agent-password">Password</label>

                            <input id="agent-password" autocomplete="new-password" type="password" class="form-control required autofill" name="password" data-parsley-minlength="6" data-parsley-minlength-message="The password must be at least 6 characters.">

                        </div>
                    </div>
                </div>

                <div class="row">
                  <div class="appendnewtwilioids">
                  </div>
                </div>
                <div class="row"> 
                    <div class="col-xs-12 col-sm-12 col-md-12 twilio-worker-id" disabled="none">
                      <div class="mb10">
                        <label for="twilio_worker_id">Worker ID</label>
                        <input type="text" id="twilio_worker_id" class="form-control" name="worker_id" value="" readonly>
                      </div>
                    </div>
                </div>
                <div class="row"> 
                    <div class="col-xs-12 col-sm-12 col-md-12 twilio-worker-id" disabled="none">
                      <div class="mb10">
                        <label for="client_name">Clients</label>
                        <input type="text" id="client_name" class="form-control" name="client_name" value="" readonly disabled>
                      </div>
                    </div>
                </div>
                <div class="row">    
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <label for="styled-checkbox-1">Language</label>
                        <div class="form-group nostar">
                            <input class="styled-checkbox" id="lang_english" type="checkbox" name="languages[]" value="en" data-parsley-required='true' data-parsley-errors-container="#language-errors" data-parsley-required-message="Please select at least one language">
                            <label for="lang_english" class="mr15">English</label>
                            <input class="styled-checkbox" id="lang_spanish" type="checkbox" name="languages[]" value="es" data-parsley-required='true' data-parsley-errors-container="#language-errors" data-parsley-required-message="Please select at least one language">
                            <label for="lang_spanish">Spanish</label>
                        </div>
                        <div id="language-errors"></div>
                    </div>
                </div>
                <div class="row"> 
                  <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group twi-flow" style="margin-top: 5px">
                      <label for="twilio_workflow">Twilio Workflow</label>
                      <div class="twi-work-list scrollbar-inner nostar">
                          @foreach(getWorkflows() as $workflow)
                          @if(!empty($workflow->client))
                          <label class="multi-select-menuitem custom-checkbox all-label-clients {{$workflow->client->status}}-clients">
                            <input type="checkbox" name="twilio_workflows[]" value="{{$workflow->workflow_id}}" data-parsley-required='true' data-parsley-errors-container="#twilio-workflows-errors" data-parsley-required-message="Please select at least one twilio workflow" ><span class="lb-txt">{{$workflow->workflow_name}} @if(!empty($workflow->client)) ({{$workflow->client->name}}) @endif</span></input> 
                            <span class="checkmark"></span>
                          </label>
                          @endif
                          @endforeach
                      </div>
                  </div>
                </div>
                </div>
                <div class="row"> 
                  <div class="col-xs-12 col-sm-12 col-md-12 agent-status-section read-only-blocked nostar">
                    <label for="styled-checkbox-1">Status</label>
                    <div class="form-group">
                        <input class="styled-checkbox" id="status_active_tpv" type="radio" name="status" value="active" disabled>
                        <label for="status_active_tpv" class="mr15">Active</label>
                        <input class="styled-checkbox" id="status_inactive_tpv" type="radio" name="status" value="inactive" disabled>
                        <label for="status_inactive_tpv">Inactive</label>
                    </div>
                  </div>
                </div>
                <div class="row"> 
                    <div class="col-xs-12 col-sm-12 col-md-12 agent-status-section read-only-blocked nostar">
                        <div class="form-group">
                            <input class="styled-checkbox" id="is_block_edit_tpv" type="checkbox" name="is_block" value="1" disabled>
                            <label for="is_block_edit_tpv">Blacklisted</label>
                        </div>
                    </div>
                </div>
                <div class="row"> 
                    <div class="col-xs-12 col-sm-12 col-md-12 agent-status-section read-only-blocked reason-for-deactivation nostar">
                        <div class="form-group">
                            <label for="edit-comment-tpv">Reason for deactivation</label>
                            <textarea class="form-control" rows="5" name="comment" id="edit-comment-tpv" disabled></textarea>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-xs-12 col-sm-12 col-md-12 deactivated-reason">
                    <div class="form-group">
                        <label for="deactivated_reason">Reason of Deactivated/Blacklisted</label>
                        <textarea id="deactivated_reason" class="form-control" disabled> </textarea>
                    </div>
                </div> -->
                <div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
                  <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                    <div class="btn-group">
                      <button type="submit" class="btn btn-green" id="btn_save"><span class="save-text">Save</span></button>
                      <button type="button" class="btn btn-red cancel-btn" data-dismiss="modal">Cancel</button>

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
    $(document).ready(function() {     

      //$('#twilio_workflow').multiSelect();


      $(document).on('click', '.tpv-agent-modal', function(e) {
        
        $(".ajax-error-message").html('');
        $('#password-row').hide();
        $(".help-block").remove('');
        $('.agent-status-section,.all-label-clients').show();
        $('.deactivated-reason,.user-id-section').hide();
        $('.multi-select-button').html([]);
        $('#twilio_workflow').prop('selectedIndex',-1);
        $('#tpvAgentModal .btn-green').show();
        $("#tpv-agent-create-form")[0].reset();
        $(".active-clients").css('cursor','');
        $(".inactive-clients").css('cursor','not-allowed');
        $('#tpv-agent-create-form').parsley().reset();
        $("#tpv-agent-create-form label").addClass('yesstar');
        var action_type = $(this).data('type');
        var title = $(this).data('original-title');
        $('#tpvAgentModal .modal-title').html(title);
        $("#tpv-agent-create-form :input").prop("disabled", false);
        $(".nostar").find('label').removeClass('yesstar');
        if (action_type == 'new') {
          $('.twilio-worker-id').hide();
          $('.agent-status-section,.inactive-clients').hide();
          $('#client-user-id').val('');
          $('#tpvAgentModal').modal();
        } else {
          var id = $(this).data('id');
          $('#client-user-id').val(id);

          if (action_type == 'edit') {
            $('.twilio-worker-id').show();
            $('#password-row').show();
          }

          var url = "{{ route('admin.tpv.gettpvagent') }}";
          $.ajax({
            url: url,
            data: {
              user_id: id
            },
            success: function(response) {
              if (response.status == 'success') {
                $('#tpv_agent_userid').val(response.data.userid);
                $('#first_name').val(response.data.first_name);
                $('#last_name').val(response.data.last_name);
                $('#user_title').val(response.data.title);
                $('#user_email').val(response.data.email);
                $('#client_name').val(response.clients_name);

                if (response.data.languages != null) {
                    if (response.data.languages.english) {
                        $("#lang_english").prop('checked', true);
                    }
                    if (response.data.languages.spanish) {
                        $("#lang_spanish").prop('checked', true);
                    }
                }
                if(response.twilio_ids.length > 0){
                    if(typeof response.twilio_ids[0].twilio_id !== 'undefined') {
                        $('.twilio-worker-id').show();
                        $('#twilio_worker_id').val(response.twilio_ids[0].twilio_id);
                    }

                    var workflows = response.twilio_ids;
                    if (workflows != null && workflows != '') {
                        // var workflow = workflows.split(',');
                        // $('.multi-select-button').html(workflow);
                        $(".inactive-clients").find('input').attr("onclick","");
                        for(var i in workflows) {
                            var optionVal = workflows[i];
                            // $("#twilio_workflow").find("option[value="+optionVal.workflow_id+"]").prop("selected", "selected");
                            $("input[value='"+optionVal.workflow_id+"']").trigger('click');
                        }
                        $(".inactive-clients").find('input').attr("onclick","return false;");
                    }
                }
                if(response.data.status == 'inactive') {
                    $('#edit-comment-tpv').val(response.data.deactivationreason);  
                    $("#status_inactive_tpv").prop('checked', true);  
                    $('#is_block_edit_tpv,#edit-comment-tpv').prop('disabled', false);                  
                } else {
                    $("#status_active_tpv").prop('checked', true);
                    $('#is_block_edit_tpv,#edit-comment-tpv').prop('disabled', true);
                    $('#edit-comment-tpv').val(''); 
                    
                }  
                if (response.data.is_block == 1) {
                    $('#is_block_edit_tpv').prop("checked", "checked");
                    $('#is_block_edit_tpv').attr('disabled', true);
                }  else {
                    $('#is_block_edit_tpv').prop("checked", false);
                }
                if (action_type == 'view') {
                  $('.twilio-worker-id,.user-id-section').show();
                  $("#tpv-agent-create-form :input").prop("disabled", true);
                  $(".all-label-clients").css('cursor','not-allowed');
                  $('#tpvAgentModal .btn-green').hide();
                  $(".cancel-btn").prop("disabled", false);
                  $("#tpv-agent-create-form label").removeClass('yesstar');
                  if(response.data.status == 'active') {
                        $('.reason-for-deactivation').hide();                        
                  }
                  $("#tpvAgentModal .modal-body").addClass("view-mode");
                }

              }

              $('#tpvAgentModal').modal();
            },
            error: function(xhr) {
              console.log(xhr);
            }
          });

          
        }

        // if(typeof client_id != undefined){
        //     $("#all_clients").prop("disabled", true);
        // }
      });

      $("#tpv-agent-create-form").submit(function(e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.

        var form = $(this);
        var url = form.attr('action');
        $('#btn_save').prop("disabled", true);
        $('#loaderImg').show(); 
        $.ajax({
          type: "POST",
          url: url,
          data: form.serialize(), // serializes the form's elements.
          success: function(response) {
            $('#loaderImg').hide(); 
            $("#btn_save").prop("disabled", false);
            $('#tpvAgentModal').modal("hide");
            if (response.status == 'success') {
              printAjaxSuccessMsg(response.message); 
            } else {
              printAjaxErrorMsg(response.message); 
            }
            $('#tpv-agent-table,#all-agent-table').DataTable().ajax.reload();
          },
          error: function(xhr) {
            if (xhr.status == 422) {
              $('#loaderImg').hide(); 
              $("#btn_save").prop("disabled", false);
              printErrorMsgNew(form,xhr.responseJSON.errors);
            }
          }
        });
      });

      $("input[name='status']").click(function() {
          var status = $("input[name='status']:checked", "#tpv-agent-create-form").val();
          if (status == 'active') {
              $('#edit-comment-tpv').val('');
              $('#is_block_edit_tpv,#edit-comment-tpv').prop('disabled', true);
              $('#is_block_edit_tpv').prop("checked", false);
          } else {
              $('#is_block_edit_tpv,#edit-comment-tpv').prop('disabled', false);
          }
      });
    });

    $('#tpvAgentModal').on('hidden.bs.modal', function () {
			$('#tpv-agent-create-form').parsley().reset();
			$("#tpvAgentModal .modal-body").removeClass("view-mode");
		});
  </script>
@endpush
