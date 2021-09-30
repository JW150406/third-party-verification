<?php

if (Auth::user()->isAccessLevelToClient()) {
    $client_id = Auth::user()->client_id;
    $salescenter_id = Auth::user()->salescenter_id;
}
?>

<div class="team-addnewmodal v-star">
    <div class="modal fade " id="salesagent-modal" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close main_modal"  data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Sales Person Action</h4>
                </div>
                <div class="modal-body">
                    <form action="{{ route('salesagent.save')}}" method="post" id="sales-agent-form" enctype="multipart/form-data" files="true" data-parsley-validate>
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="post" >
                        <input type="hidden" name="id" id="agent-user-id">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label for="all_agent_clients">Client</label>
                                    @if(Auth::user()->isAccessLevelToClient())
                                        @foreach(getAllClients() as $client)
                                        <input type="text" value="{{$client->name}}"  class="form-control" readonly="">
                                        <input type="hidden" id="all_agent_clients" name="client" value="{{$client->id}}">
                                        @endforeach
                                    @elseif(isset($client) && !empty($client) && Request::route()->getName() != 'admin.sales.agents')
                                    <div class="dropdown select-dropdown">
                                            <select id="all_agent_clients" name="client" class="select2 form-control" data-parsley-errors-container="#select2-salesagent-error-message" data-parsley-required='true'>
                                                <option value="{{$client->id}}" class="all-clients {{$client->status}}-clients" selected> {{$client->name}}</option>
                                            </select>
                                            <span id='select2-salesagent-error-message'></span>
                                        </div>
                                        <input type="hidden" id="all_agent_clients" name="client" value="{{$client->id}}" >
                                    @else
                                        <div class="dropdown select-dropdown">
                                            <select id="all_agent_clients" name="client" class="select2 form-control" data-parsley-errors-container="#select2-salesagent-error-message" data-parsley-required='true'>
                                                <option value="" selected>Select</option>
                                                @foreach(getAllClients() as $client)
                                                <option value="{{$client->id}}" class="all-clients {{$client->status}}-clients" @if($client->id == $client_id) selected @endif> {{$client->name}}</option>
                                                @endforeach
                                            </select>
                                            <span id='select2-salesagent-error-message'></span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label for="all_agent_salescenter">Sales center</label>
                                    <div class="dropdown select-dropdown">
                                        <select id="all_agent_salescenter" name="sales_center" class="select2 form-control"  data-parsley-errors-container="#select2-addsalesagent-error-message" data-parsley-required='true'>
                                            <option value="" selected>Select</option>
                                        </select>
                                        <span id="select2-addsalesagent-error-message"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label for="agent-location">Location</label>
                                    <select class="select2 form-control" id="agent-location" name="location" data-parsley-errors-container="#select2-addsalesagentlocation-error-message" data-parsley-required='true'>
                                        <option value='' selected>Select</option>
                                    </select>
                                    <span id="select2-addsalesagentlocation-error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group nostar">
                                    <label for="agent-external-id" class="nostar">External ID</label>
                                    <input id="agent-external-id" autocomplete="new-external-id" type="text" class="form-control" name="external_id" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 agent-id-div">
                                <div class="form-group">
                                    <label for="agent-id">ID</label>
                                    <input id="agent-id" autocomplete="off" type="text" class="form-control required" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-6">
                                <div class="form-group">
                                    <label for="agent-name">First name</label>
                                    <input id="agent-name" autocomplete="off" type="text" class="form-control required" name="first_name" disabled data-parsley-required='true'>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-6">
                                <div class="form-group">
                                    <label for="name">Last name</label>
                                    <input autocomplete="off" id="agent-last-name" type="text" class="form-control required" name="last_name" disabled data-parsley-required='true'>
                                </div>
                            </div>
                        </div>
                        <img src="{{asset('images/table-loader.svg') }}" style="display:none;height:30px; width:auto;" id="loaderImg" class="img-responsive center-block">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group nostar">
                                    <label for="agent-email">Email</label>
                                    <input id="agent-email" autocomplete="new-email" type="text" class="form-control autofill" name="email" disabled data-parsley-trigger="change" data-parsley-trigger="keyup" data-parsley-email>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <span toggle="#agent-password" class="fa fa-fw fa-eye add-sls-show-password toggle-password"></span>
                                    <label for="agent-password">Password</label>

                                    <input id="agent-password" autocomplete="new-password" type="password" class="form-control required autofill" name="password" data-parsley-minlength="6" data-parsley-minlength-message="The password must be at least 6 characters.">

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-6">
                                <div class="form-group">
                                    <label for="agent-type">Type</label>
                                    <select class="select2 form-control required" id="agent-type" name="agent_type" data-parsley-errors-container="#select2-channeltype-error-message" data-parsley-required='true'>
                                        <option value="tele" selected>Tele</option>
                                        <option value="d2d">D2D</option>
                                    </select>
                                    <span id="select2-channeltype-error-message"></span>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-6 nostar">
                                <div class="form-group">
                                    <label for="agent-certified">Certified</label>
                                    <select class="select2 form-control required" name="certified" id="agent-certified" data-parsley-errors-container="#select2-ceritified-error-message" data-parsley-required='true'>
                                        <option value="1">Yes</option>
                                        <option value="0" selected>No</option>
                                    </select>
                                    <span id="select2-ceritified-error-message"></span>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-6">
                                <div class="form-group date-outer">
                                    <label class="certified-Date" for="certified-date">Certification Date</label>
                                    <label for="certified-date" class="form-icon nostar">{!! getimage('images/calender.png') !!}</label>
                                    <input name="certification_date" required autocomplete="new-password" type="text" class="form-control datepicker" required id="certified-date">

                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-6">
                                <div class="form-group date-outer">
                                    <label class="Expiry-Date" for="expiry-date">Expiry Date</label>
                                    <label for="expiry-date" class="form-icon nostar">{!! getimage('images/calender.png') !!}</label>
                                    <input name="expiry_date" required autocomplete="new-password" type="text" class="form-control datepicker" id="expiry-date">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-6 nostar">
                                <div class="form-group">
                                    <label for="state-test">State Test</label>
                                    <select class="select2 form-control required" name="passed_state_test" id="state-test" data-parsley-errors-container="#select2-state-error-message" data-parsley-required='true'>
                                        <option value="1">Yes</option>
                                        <option value="0" selected>No</option>
                                    </select>
                                    <span id="select2-state-error-message"></span>

                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-6">

                                <div class="form-group state-enabled agent-state">
                                    <label for="agent-state" id="agent-state-label">State</label>
                                    <select class="select2 form-control" id="agent-state" name="state[]" multiple="multiple" data-parsley-required='true' data-parsley-errors-container="#agent-state-error">
                                        @foreach(getStates() as $state)
                                        <option value="{{$state->state}}">{{$state->state}} </option>
                                        @endforeach
                                    </select>
                                    <div id="agent-state-error"></div>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-6 nostar">
                                <div class="form-group">
                                    <label for="agent-background">Background check</label>
                                    <select class="select2 form-control required" name="backgroundcheck" id="agent-background" data-parsley-errors-container="#select2-background-error-message">
                                        <option value="1">Yes</option>
                                        <option value="0" selected>No</option>
                                        <option value="2">N/A</option>
                                    </select>
                                    <span id="select2-background-error-message"></span>

                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-6 nostar">
                                <div class="form-group">
                                    <label for="agent-drugtest">Drug Test</label>
                                    <select class="select2 form-control required" name="drugtest" id="drugtest"data-parsley-errors-container="#select2-drugtest-error-message" disabled>
                                        <option value="1">Yes</option>
                                        <option value="0" selected>No</option>
                                        <option value="2">N/A</option>
                                    </select>
                                    <span id="select2-drugtest-error-message"></span>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-6">
                                <div class="form-group nostar">
                                    <label for="phone_number" class="nostar">Phone Number</label>
                                    <input id="phone_number" type="text" class="form-control" name="phone_number" value="" autofocus="" data-parsley-type="digits" data-parsley-length="[10,10]" data-parsley-type-message="Please enter only numeric value" data-parsley-length-message="You must enter exactly 10 digits">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-6">                                
                                <div class="form-group nostar state-enabled agent-restrict-state">
                                    <label for="agent-restrict-state" id="agent-restrict-state-label">Restrict State</label>
                                    <select class="select2 form-control" id="agent-restrict-state" name="restrict_state[]" multiple="multiple" data-parsley-errors-container="#agent-restrict-state-error">
                                        @foreach(getStates() as $state)                            
                                        <option value="{{$state->state}}">{{$state->state}} </option>
                                        @endforeach
                                    </select>
                                    <div id="agent-restrict-state-error"></div>

                                </div>
                            </div>
                        </div>
                        <!---only show on deactive agent-start-->
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 agent-status-section read-only-blocked nostar">
                                <label for="styled-checkbox-1">Status</label>
                                <div class="form-group">
                                    <input class="styled-checkbox" id="status_active" type="radio" name="status" value="active" disabled>
                                    <label for="status_active">Active</label>
                                    <input class="styled-checkbox" id="status_inactive" type="radio" name="status" value="inactive" disabled>
                                    <label for="status_inactive">Inactive</label>
                                </div>
                            </div>
                        
                            <div class="col-xs-12 col-sm-12 col-md-12 agent-status-section read-only-blocked nostar">
                                <div class="form-group">
                                    <input class="styled-checkbox" id="is_block_edit_agent" type="checkbox" name="is_block" value="1" disabled>
                                    <label for="is_block_edit_agent">Blacklisted</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 agent-status-section read-only-blocked reason-for-deactivation nostar">
                                <div class="form-group">
                                    <label for="agent-edit-comment">Reason for deactivation</label>
                                    <textarea class="form-control" rows="5" name="comment" id="agent-edit-comment" disabled></textarea>
                                </div>
                            </div>
                        </div>

                        <!---only show on deactive agent-end-->
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 nostar">
                                <label>Upload documents</label>
                                <div class="form-group mt15" id="doc-upload-outer">
                                    <div class="dropzone files-container " id="bulkDocs">
                                        <div class="fallback">
                                            <input name="file" type="file" multiple />

                                        </div>
                                    </div>
                                    @include('preview-dropzone')
                                    <span class = "dropzone-error" style="color:red; font-size: 11px;"></span>
                                </div>
                                <div id="upload_prev" class="list-filename"></div>
                                <div class = "iframe_preview" style= "display:none;"></div>
                            </div>
                        </div>
                        <!--end--scroller-->
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
                                <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                    <div class="btn-group">

                                        <button type="button" class="btn btn-green btn-save" id="btn_save">Save</button>
                                        <button type="button" class="btn btn-red btn-cancel main_modal"  data-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="preview-modal" data-backdrop="false">
        <div class="modal-dialog">
    
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close ifrm_close" >&times;</button>
                    <h4 class="modal-title">Preview</h4>
                </div>
                <div class="modal-body">
                    <div class = "iframe_preview">
                        <iframe src="" id= "ifrm" width="100%" height="400px" frameBorder = "1"></iframe>
                    </div>
                </div>
                <div class="text-center">
{{--                    <button type="button" class="btn btn-green btn-cancel btn-download">Download</button>--}}
                    <button type="button" class="btn btn-red btn-cancel ifrm_close">Cancel</button>
                </div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>
</div>


<script>
    var action_type = '';
    $(document).ready(function() {
        $("#all_agent_clients").val('{{$client_id}}');
        $(document).on('click', '.salesagent-modal', function(e) {
            $(".help-block").remove('');
            $('#password-row').hide();
            $('#sales-agent-form').parsley().reset();
            $("#upload_prev").html('');
            $("#all_agent_salescenter").html('');
            $("#agent-location").html('');
            $("#agent-location").append('<option value="" selected>Select </option>');
            $("#sales-agent-form")[0].reset();
            @if(Request::route()->getName() == 'admin.sales.agents')
                @if(!Auth::user()->isAccessLevelToClient())
                    $('#all_agent_clients').val('').trigger('change.select2'); // for reset select2 drop-down
                @endif
            @endif
            
            $(".read-only-blocked").show();
            $('.agent-status-section').show();
            $('.agent-id-div').show();
            $('.multi-select-button').html([]);
            $('#agent-state').prop('selectedIndex',-1);
            $('#agent-restrict-state').prop('selectedIndex',-1);
            $("#sales-agent-form :input").prop("disabled", false);
            $(".all-clients,.all-salescenters").prop('disabled',false);
            $('#agent-edit-comment').attr('readonly', false);
            $('#salesagent-modal .btn-save').show();            
            $('#agent-id').prop("disabled", true);
            var agent_state = $('#agent-state').closest(".form-group").find('.scroll-wrapper.multi-select-menu.scrollbar-inner');
            agent_state.css("visibility", "visible");
            var agent_restrict_state = $('#agent-restrict-state').closest(".form-group").find('.scroll-wrapper.multi-select-menu.scrollbar-inner');
            agent_restrict_state.css("visibility", "visible");
            $("#sales-agent-form label").addClass('yesstar');
            $(".nostar").find('label').removeClass('yesstar');            
            $(".multi-select-container").find('label').removeClass('yesstar');
            action_type = $(this).data('type');
            @if(Request::route()->getName() == 'client.salescenter.show' || Request::route()->getName() == 'client.salescenters.edit')
            $("#all_agent_salescenter").append('<option value="{{$salescenter->id}}" selected>{{$salescenter->name}} </option>');
            $("#all_agent_clients,#all_agent_salescenter").prop("disabled", true);
            if (action_type == 'new') {
                getSalseCenterLocations(action_type);
            }
            @else
            $("#all_agent_salescenter").append('<option value="" selected>Select </option>');
            @endif

            $('#agent-certified,#state-test,#agent-background,#drugtest').trigger('change');
            var title = $(this).data('title');
            $('#salesagent-modal .modal-title').html(title);
            if (action_type == 'new') {
                $(".iframe_preview").css('display','none');
                $(".inactive-clients,.inactive-salescenters").prop('disabled',true);
                $('#agent-user-id').val('');
                $('.agent-status-section').hide();
                $('.agent-id-div').hide();
                @if(Request::route()->getName() == 'admin.sales.agents')
                    @if(Auth::user()->isAccessLevelToClient())
                        getSalesCenter(action_type);
                    @endif
                @endif
            } else {

                var status = $(this).data('status');
                var reason = $(this).data('reason');
                var agent_id = $(this).data('id');
                var agent_userid = $(this).data('userid');
                var client_id = $(this).data('client-id');
                var clientName = $(this).data('client-name');
                var salescenter_id = $(this).data('salescenter-id');
                var salescenter_name = $(this).data('salescenter-name');

                $('#agent-id').val(agent_userid); // only for show
                $('#agent-user-id').val(agent_id);
                $('#all_agent_clients').val(client_id).trigger('change.select2');
                $('#phone_number').val(phone_number);
                $("#all_agent_salescenter").append('<option value="'+salescenter_id+'" selected> '+salescenter_name+'</option>');

                $("#agent-edit-comment").val(reason);
                $(".read-only-blocked").show();
                $("#agent-edit-comment").closest('div').find('label').addClass('yesstar');
                $("#all_agent_clients,#all_agent_salescenter").prop("disabled", true);
                if (status == 'active') {
                    $("#agent-edit-comment").val('');
                    $("#status_active").prop('checked', true);
                    $('#is_block_edit_agent').prop("checked", false);
                    $('#is_block_edit_agent,#agent-edit-comment').prop('disabled', true);
                    $(".reason-for-deactivation").find('label').removeClass('yesstar');

                } else {
                    $("#status_inactive").prop('checked', true);
                    $("#agent-edit-comment").val(reason);
                    //$('#status_inactive').attr('disabled', true);
                    $(".reason-for-deactivation").find('label').addClass('yesstar');
                }

                if (action_type == 'view') {
                    $(".iframe_preview").css('display','none');
                    $("#sales-agent-form :input").prop("disabled", true);
                    $(".btn-cancel").prop("disabled", false);
                    $("#sales-agent-form label").removeClass('yesstar');
                    $('#salesagent-modal .btn-save').hide();
                    $(' label').addClass("nostar");
                    $("#doc-upload-outer").hide();
                    if (status == 'active') {
                        $('.reason-for-deactivation').hide();
                    }
                    $("#salesagent-modal .modal-body").addClass("view-mode");
                } else {
                    $("#doc-upload-outer").show();
                }
                if (action_type == 'edit') {
                    $('#password-row').show();
                    $('label').removeClass("nostar");
                    $(".iframe_preview").css('display','block');                    
                }

                getSalseCenterLocations(action_type);

            }
            $("#all_agent_clients select,#all_agent_salescenter select").select2();
            $('#salesagent-modal').modal('show');
        });

        $(document).on('click', '.show-document', function(e) {
            var id = $(this).attr('id');
            
            if (id > 0) {
                var url = "{{url('admin/client/show-document')}}/" + id;
            }
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    '_token': "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.status == 'success') {
                        //$('#ifrm').attr('src'," ");
                        ext = response.data.split('.');
                        
                        // $('#ifrm').attr('src', response.data);
                        // $('#ifrm').attr('data-id',id);
                        // if(ext[ext.length -1] == "xlsx" || ext[ext.length -1] == "docx" ||ext[ext.length -1] == "doc" || ext[ext.length -1] == "odt" || ext[ext.length -1] == "ods")
                        // {
                            // var x = document.getElementById("ifrm");
                            // $('#ifrm').attr('srcdoc',"<h3>Sorry no preview available.</h3>");
                        // }

                        if (ext[ext.length -1] == "pdf" || ext[ext.length -1] == "jpeg" || ext[ext.length -1] == "png" || ext[ext.length -1] == "jpg")
                        {
                            $('#ifrm').removeAttr('srcdoc');
                            $('#ifrm').attr('src', response.data);
                            $('#ifrm').attr('data-id',id);
                            $('#salesagent-modal').modal().hide();
                            $('#preview-modal').modal().show();
                        } else {
                            window.location.href = response.data;
                        }


                    }
                },
                error: function(xhr) {
                    console.log(xhr);
                }
            });
        });

        $('.ifrm_close').click(function(){
            $('#preview-modal').modal().hide();
            $('#salesagent-modal').modal().show();
        });
        $(".main_modal").click(function(){
            $('.modal-backdrop').remove();
        });

        $('#all_agent_clients').on('change', function(e) {
            getSalesCenter();
        });
        $('#all_agent_salescenter').on('change', function(e) {
            getSalseCenterLocations();
        });

        $('#agent-location').on('change', function(e,data) {
            var locationId = $(this).val();
            var selectedChannel = '';
            if (typeof data != 'undefined') {
                selectedChannel = data.selectedChannel;
            }
            setLocationChannelOptions('agent-type', locationId, selectedChannel);
        });
        $("input[name='status']").click(function() {
            var status = $("input[name='status']:checked", "#sales-agent-form").val();
            if (status == 'active') {
                $(".reason-for-deactivation").find('label').removeClass('yesstar');
                $('#agent-edit-comment').val('');
                $('#is_block_edit_agent,#agent-edit-comment').prop('disabled', true);
                $('#is_block_edit_agent').prop("checked", false);
            } else {
                $('#is_block_edit_agent,#agent-edit-comment').prop('disabled', false);
                $(".reason-for-deactivation").find('label').addClass('yesstar');
            }
        });

        $('.datepicker').datepicker();
    });

    function getSalesCenter(action_type = null) {

        var clientId = $("#all_agent_clients").val();
        setSalesCenterOptions("all_agent_salescenter",clientId);
        getSalseCenterLocations(action_type);
    }


    function getSalseCenterLocations(action_type=null)
    {

        $("#agent-location").html('<option value="" selected>Select </option>');
        var client_id =$("#all_agent_clients").val();
        var salescenter_id =$("#all_agent_salescenter").val();
        @if(Auth::user()->isAccessLevelToClient() && $salescenter_id!= null)
            salescenter_id ='{{$salescenter_id}}';
        @else
            salescenter_id =$("#all_agent_salescenter").val();
        @endif;
        if(client_id > 0) {
            $.ajax({
                url: "{{route('salescenter.getSalesCenterLocations')}}",
                data: {
                    client_id: client_id,
                    salescenter_id: salescenter_id
                },
                success: function(response) {
                    $("#agent-location").html('');
                    @if(!Auth::user()->isLocationRestriction())
                        $("#agent-location").append('<option value="" selected>Select </option>');
                    @endif

                    if (response.status == 'success') {
                        $("#agent-location").append(response.options);
                        @if(Auth::user()->isLocationRestriction())
                            $("#agent-location").val("{{ Auth::user()->location_id }}");
                            if (action_type =='new') {
                                $("#agent-location").trigger('change');   
                            }
                        @endif
                    }
                    if (action_type =='edit' || action_type =='view') {
                        setSalesAgent(action_type);
                    }
                },
                error: function(xhr) {
                    console.log(xhr);
                }
            });
        }
    }

    function setSalesAgent(action_type = null) {
        var agent_id = $('#agent-user-id').val();
        if (agent_id > 0) {
            $.ajax({
                url: "{{route('salesagent.edit')}}",
                data: {
                    id: agent_id
                },
                success: function(response) {
                    if (response.status == 'success') {
                        console.log(response.data);
                        $('#agent-background').val(response.data.backgroundcheck).trigger('change');
                        $('#drugtest').val(response.data.drugtest).trigger('change');
                        $('#agent-name').val(response.data.first_name);
                        $('#agent-last-name').val(response.data.last_name);
                        $('#agent-email').val(response.data.email);
                        if (response.data.sales_agent_details != null) {                            
                            //$('#agent-type').val(response.data.sales_agent_details.agent_type).trigger('change');
                            $('#agent-certified').val(response.data.sales_agent_details.certified).trigger('change');
                            $('#state-test').val(response.data.sales_agent_details.passed_state_test).trigger('change');
                            $('#agent-background').val(response.data.sales_agent_details.backgroundcheck).trigger('change');
                            $('#drugtest').val(response.data.sales_agent_details.drugtest).trigger('change');

                            $('#agent-location').val(response.data.sales_agent_details.location_id).trigger('change.select2');
                            $('#phone_number').val(response.data.sales_agent_details.phone_number);

                            var agentType = response.data.sales_agent_details.agent_type;
                            $('#agent-location').val(response.data.sales_agent_details.location_id);
                            $('#agent-location').trigger('change',[{selectedChannel: agentType}]);
                            $('#agent-external-id').val(response.data.sales_agent_details.external_id);

                            var certification_date = response.data.sales_agent_details.certification_date;
                            var certification_exp_date = response.data.sales_agent_details.certification_exp_date;
                            if (certification_date != null) {
                                $('#certified-date').datepicker("setDate", new Date(certification_date));
                            }

                            if (certification_exp_date != null) {
                                $('#expiry-date').datepicker("setDate", new Date(certification_exp_date));
                            }

                            var state = response.data.sales_agent_details.state;
                            if (state != null && state != '') {
                                var states = state.split(',');
                                $('.multi-select-button-state').html(state);
                                for (var i in states) {
                                    var optionVal = states[i];
                                    $("#agent-state").find("option[value=" + optionVal + "]").prop("selected", "selected");
                                    $(".agent-state input[value=" + optionVal + "]").trigger('click');
                                }
                            }   

                            /* fetch mutliselect restrict agent states */
                            var restrict_state = response.data.sales_agent_details.restrict_state;
                            if (restrict_state != null && restrict_state != '') {
                                var restrict_state1 = restrict_state.split(',');
                                $('.multi-select-button-restrict').html(restrict_state);
                                for (var j in restrict_state1) {
                                    var optionVal1 = restrict_state1[j];
                                    $("#agent-restrict-state").find("option[value=" + optionVal1 + "]").prop("selected", "selected");
                                     $(".agent-restrict-state input[value=" + optionVal1 + "]").trigger('click');
                                }
                            }
                        }
                        if (action_type == 'view') {
                            var agent_state = $('#agent-state').closest(".form-group").find('.scroll-wrapper.multi-select-menu.scrollbar-inner');
                            agent_state.css("visibility", "hidden");
                            $('#certified-date,#expiry-date').prop("disabled", true);
                        }

                        var files = response.documents;
                        var length = files.length;
                        for (var i = 0; i < length; i++) {
                            var closeBtn = '';
                            if (action_type == 'edit') {
                                closeBtn = ' <p class="close delete-documents" data-id="' + files[i].id + '">X</p> ';
                               
                            }
                            $("#upload_prev").append('<span id="doc_outer_' + files[i].id + '">' + '<div class="filenameupload" id="file_' + i + '"><a href="javascript:void(0)" data-target="#preview-modal" class="show-document" id="'+files[i].id+'"><i class="fa fa-file" style = "color:blue;"></i>' + files[i].name + '</a></div>' + closeBtn + '</span>');
                        }
                        if (response.data.is_block == 1) {
                            $('#is_block_edit_agent').prop("checked", "checked");

                            $('#status_inactive,#status_active,#is_block_edit_agent').attr('disabled', true);
                            $('#agent-edit-comment').attr('readonly', true);
                        }
                        $('.show-document').css('cursor','pointer');
                        if($('#upload_prev').children().length > 0)
                        {
                            $(".iframe_preview").css('display','block');
                            $("#ifrm").attr('src','');
                        }
                        else
                        {
                            $(".iframe_preview").css('display','none');
                        }
                    }

                    $('#salesagent-modal').modal();
                    // $('#salesagent-modal').modal().hide();

                    
                },
                error: function(xhr) {
                    console.log(xhr);
                }
            });
        }
    }
</script>


<!----script - for - multiple file upload and preview with remove options----->
<script>
    $(document).ready(function(readyEvent) {
        /* select agent-state option from dropdown the single options as checkboxes */
        $('#agent-state').multiSelect();
        /* select agent-restrict-state option from dropdown the single options as checkboxes */
        $('#agent-restrict-state').multiSelect();
        // $(document).on('click', '.close', function(closeEvent) {
        //     $(this).parents('span').remove();
        //     var fileInput = $('#uploadFile')[0];
        //
        //     //if the checkbox is checked, then remove all selected files
        //     if ($('#clearOnDelete:checked').length) {
        //         fileInput.value = '';
        //     }
        //     var files = fileInput.files;
        //     var index = closeEvent.target.id.replace('file_', '');
        // })

        $('#uploadFile').on('change', function(changeEvent) {

            var filename = this.value;
            path = filename;
            console.log(path);
            var lastIndex = filename.lastIndexOf("\\");
            if (lastIndex >= 0) {
                filename = filename.substring(lastIndex + 1);
            }
            var files = changeEvent.target.files;
            
            for (var i = 0; i < files.length; i++) {
                $("#upload_prev").append('<span>' + '<div class="filenameupload" id="file_' + i + '"><i class="fa fa-file" style = "color:blue;"></i>' + files[i].name + '</div>' + '<p class="close" >X</p></span>');
            }
        });
        $('#agent-certified').on('change', function(changeEvent) {
            var certified = $(this).val();
            if (certified == 1) {
                $('.certified-Date').removeClass("nostar");
                $('.certified-Date').addClass("yesstar");
                $('.Expiry-Date').removeClass("nostar");
                $('.Expiry-Date').addClass("yesstar");
                $('#certified-date,#expiry-date').prop("required", true);
                $('#certified-date,#expiry-date').prop("disabled", false);
                // $('#certified-date').parsley().validate();
                // $('#expiry-date').parsley().validate();
            } else {
                $('.certified-Date').addClass("nostar");
                $('.Expiry-Date').addClass("nostar");
                $('#certified-date,#expiry-date').prop("required", false);
                $('#certified-date,#expiry-date').prop("disabled", true);
                $('#certified-date,#expiry-date').val('');
                $('#certified-date').parsley().validate();
                $('#expiry-date').parsley().validate();
            }
        });
        $('#certified-date').on('change', function(changeEvent) {
            if($(this).val() != '') {
                $("#expiry-date").datepicker("setStartDate",$(this).val());            
            }
        });
        $('#expiry-date').on('change', function(changeEvent) {
            if($(this).val() != '') {
                $("#certified-date").datepicker("setEndDate",$(this).val());
            }
        });

        $('#state-test').on('change', function(changeEvent) {
            var state_test = $(this).val();
            var agent_state = $('#agent-state').closest(".form-group").find('.scroll-wrapper.multi-select-menu.scrollbar-inner');
            var state_formgroup = $('#agent-state').closest(".form-group");

            if (state_test == 1) {
                agent_state.css("visibility", "visible");
                state_formgroup.removeClass('state-disabled');
                $('#agent-state').prop("disabled", false);
                $('#agent-state-label').removeClass("nostar");
                $('#agent-state').attr("data-parsley-required", true);
                //$('#agent-state').parsley().validate();
            } else {
                $('#agent-state-label').addClass("nostar");
                $('#agent-state').attr("data-parsley-required", false);
                $('#agent-state').parsley().validate();
                $('.multi-select-button').html([]);
                $('#agent-state').prop('selectedIndex', -1);
                agent_state.css("visibility", "hidden");
                state_formgroup.addClass('state-disabled');
            }
        });


    });

    /** for hide datepicker on model scroll **/
    $("#salesagent-modal").scroll(function() {
        $("#ui-datepicker-div").fadeOut();
        $("#certified-date").blur();
        $("#expiry-date").blur();
    });
    

</script>

<script>
    $('#salesagent-modal').scroll(function(){
        $(".datepicker").datepicker("hide");
    });
</script>

@push('scripts')
    <script>
        Dropzone.autoDiscover = false;
        function submitAgentForm() {
            $("#btn_save").prop("disabled", true);
            $("#all_agent_clients,#all_agent_salescenter").prop("disabled", false);
            let formData = $('#sales-agent-form').serializeArray();
            $.ajax({
                url: '{{route("salesagent.save")}}',
                method:'POST',
                data: formData,
                success:function(res) {
                    if (res.status == 'success') {
                        agentUpdateHandler(res);
                    } else {
                        $('#salesagent-modal').modal("hide");
                        agentErrorHandler(res.message); 
                    }
                },
                error: function(err) {
                    agentErrorHandler(err);
                }
            });
            if (action_type == 'edit') {
                $("#all_agent_clients,#all_agent_salescenter").prop("disabled", true);
            }
        }

        var target = "#bulkDocs";

        function dropzoneCount() {
            var filesCount = $("#previews > .dz-success.dz-complete").length;
            return filesCount;
        }

        function fileType(fileName) {
            var fileType = (/[.]/.exec(fileName)) ? /[^.]+$/.exec(fileName) : undefined;
            return fileType[0];
        }

        var previewNode = document.querySelector("#cust-dropzone-template"), // Dropzone template holder
        warningsHolder = $("#warnings"); // Warning messages' holder

        previewNode.id = "";

        var previewTemplate = previewNode.parentNode.innerHTML;
        previewNode.parentNode.removeChild(previewNode);

        var CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

        var bulkUploadDropzone = new Dropzone("#bulkDocs", {
            url: "{{ route('salesagent.save') }}",
            autoProcessQueue: false,
            parallelUploads: 50,
            maxFiles: 50,
            uploadMultiple: true,
            previewTemplate: previewTemplate,
            previewsContainer: "#previews",
            clickable: true,
            paramName: "documents",
            createImageThumbnails: true,
            dictDefaultMessage: "Drop files here to upload, Or Browse", // Default: Drop files here to upload
            dictFallbackMessage: "Your browser does not support drag'n'drop file uploads.", // Default: Your browser does not support drag'n'drop file uploads.
            dictInvalidFileType: "You can't upload files of this type.", // Default: You can't upload files of this type.
            dictCancelUpload: "Cancel upload.", // Default: Cancel upload
            dictUploadCanceled: "Upload canceled.", // Default: Upload canceled.
            dictCancelUploadConfirmation: "Are you sure you want to cancel this upload?", // Default: Are you sure you want to cancel this upload?
            dictRemoveFile: "Remove file", // Default: Remove file
            dictRemoveFileConfirmation: null, // Default: null
            dictMaxFilesExceeded: "You can not upload any more files.", // Default: You can not upload any more files.
            dictFileSizeUnits: {tb: "TB", gb: "GB", mb: "MB", kb: "KB", b: "b"},
            init: function () {
                let userDocsClouser = this;
                //for Dropzone to process the queue (instead of default form behavior):
                document.getElementById("btn_save").addEventListener("click", function(e) {
                    
                    if($('#sales-agent-form').parsley().isValid()) {
                        
                        //Make sure that the form isn't actually being sent.
                        if (userDocsClouser.getUploadingFiles().length === 0 && userDocsClouser.getQueuedFiles().length === 0) {
                            // if($('#upload_prev').children().length == 0)
                            // {
                            //     errorDropzone();
                            // }
                            // else{
                            //     submitAgentForm();
                            // }
                            submitAgentForm();
                            
                        } else {
                            $('.dropzone-error').text('');
                            // submitAgentForm();
                            e.preventDefault();
                            e.stopPropagation();
                            userDocsClouser.processQueue();
                        } 
                    } else {
                        // if (userDocsClouser.getUploadingFiles().length === 0 && userDocsClouser.getQueuedFiles().length === 0) {
                        //     if($('#upload_prev').children().length == 0)
                        //         errorDropzone();
                        // }
                        $('#sales-agent-form').parsley().validate(); 
                    }
                    
                });
            }
        });

        bulkUploadDropzone.on('sending', function(file, xhr, formData) {
            $("#btn_save").prop("disabled", true);
            $("#all_agent_clients,#all_agent_salescenter").prop("disabled", false);
            let data = $('#sales-agent-form').serializeArray();
            $.each(data, function (key, el) {
                if (el.name == "_token") {
                    formData.append("_token", CSRF_TOKEN);
                } else {
                    formData.append(el.name, el.value);
                }
            });
            if (action_type == 'edit') {
                $("#all_agent_clients,#all_agent_salescenter").prop("disabled", true);
            }
        });

        bulkUploadDropzone.on('successmultiple', function (file, res) {
            console.log(res);
            agentUpdateHandler(res);
            this.emit("complete", file);
        });

        bulkUploadDropzone.on('errormultiple', function(file, err) {
            $("#btn_save").prop("disabled", false);
            if (typeof xhr != 'undefined' && xhr.status == 422) {
                printErrorMsgNew($("#import-form"), err.errors);
            } else if(typeof xhr != 'undefined' && xhr.status == 500) {
                agentErrorHandler(err.message);
            } else {
                agentErrorHandler(err);        
            }
        });

        bulkUploadDropzone.on('complete', function(file) {
            this.removeAllFiles();
        });

        bulkUploadDropzone.on("addedfile", function(file) {
            $('.preview-container').css('visibility', 'visible');
            $('.dropzone-error').text('');
            file.previewElement.classList.add('type-' + fileType(file.name)); // Add type class for this element's preview
        });

        bulkUploadDropzone.on("totaluploadprogress", function (progress) {

            var progr = document.querySelector(".progress .determinate");

            if (progr === undefined || progr === null) return;

            progr.style.width = progress + "%";
        });

        bulkUploadDropzone.on('dragenter', function () {
            $(target).addClass("hover");
        });

        bulkUploadDropzone.on('dragleave', function () {
            $(target).removeClass("hover");
        });

        bulkUploadDropzone.on('drop', function () {
            $(target).removeClass("hover");
        });

        bulkUploadDropzone.on('addedfile', function () {

            $('.dropzone-error').text('');
            // Remove no files notice
            $(".no-files-uploaded").slideUp("easeInExpo");

        });

        bulkUploadDropzone.on('removedfile', function (file) {
            // Show no files notice
            if ( dropzoneCount() == 0 ) {
                // errorDropzone();
                $(".no-files-uploaded").slideDown("easeInExpo");
                $(".uploaded-files-count").html(dropzoneCount());
            }

        });

        function agentUpdateHandler(response) {
            $('#loaderImg').hide();
            $("#btn_save").prop("disabled", false);
            $('#salesagent-modal').modal("hide");
            printAjaxSuccessMsg(response.message);
            $('#agent-table,#all-agent-table').DataTable().ajax.reload();
        }

        function agentErrorHandler(error) {
            $('#loaderImg').hide();
            $("#btn_save").prop("disabled", false);
            if (error.status == 422) {
                $(window).scrollTop( $("#sales-agent-form").offset().top );
                printErrorMsgNew($("#sales-agent-form"), error.responseJSON.errors);
            } else {
                printAjaxErrorMsg(error);
            }
        }

        $(document).on('click', '.delete-documents', function(e) {
            var id = $(this).data('id');
            if (id > 0) {
                var url = "{{url('admin/client/delete-document')}}/" + id;
            }

            $.ajax({
                //url: "{{route('salesagent.deleteDocuments',1)}}",
                url: url,
                type: 'POST',
                data: {
                    '_token': "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.status == 'success') {
                        $("#doc_outer_" + id).remove();
                        iframe_id = $('#ifrm').attr('data-id');
                        if(id == iframe_id && iframe_id != null)
                        {
                            $('#ifrm').attr('src','');
                            $('#ifrm').attr('srcdoc','');
                        }
                    }
                },
                error: function(xhr) {
                    console.log(xhr);
                }
            });
        });

        $('#salesagent-modal').on('hidden.bs.modal', function () {
            $('#sales-agent-form').parsley().reset();
            $("#salesagent-modal .modal-body").removeClass("view-mode");
        });
        function errorDropzone()
        {
            $('.dropzone-error').text('This field is required');
        }
</script>
@endpush