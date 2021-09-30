<!-- Add client user Modal Starts -->
<?php
if(isset($client_id) && !empty($client_id)){
    $client_data = $client_id;
}else{
    $client_data = 0;
}?>

<div class="team-addnewmodal v-star">
    <div class="modal fade" id="addclientuser" tabindex="-1" role="dialog" aria-labelledby="clientUserModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add Client User</h4>
                </div>
                <div class="ajax-error-message">
                </div>
                <div class="modal-body">
                    <div class="modal-form row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            @if(isset($client_id) && !empty($client_id))   
                                <form class="" id="client-user-create-form" role="form" method="POST" action="{{route('client.user.createOrUpdate',['client_id'=>$client_id])}}" data-parsley-validate >
                            @else
                                <form class="" id="client-user-create-form" role="form" method="POST" action="{{route('admin.client.users.StoreOrEdit')}}" data-parsley-validate >
                            @endif
                                @csrf
                                <input type="hidden" name="id" id="client-user-id">
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="all_clients">Client</label>
                                        @if(Auth::user()->isAccessLevelToClient())
                                            @foreach(getAllClients() as $client_data)
                                                <input  type="text" value="{{$client_data->name}}" readonly=""  class="form-control">
                                                <input type="hidden" id="all_clients" name="client_id" value="{{$client_data->id}}" >
                                            @endforeach
                                        @else
                                        <div class="dropdown select-dropdown">

                                            <select id="all_clients" name="client_id" data-parsley-required='true'   class="select2 form-control client_data required" <?php if (isset($client->name)){ ?> disabled="true" <?php } ?>>

                                                @if(isset($client->name) && !empty($client->name))
                                                  <option value="{{ $client_id }}" selected="">{{$client->name}}</option>
                                                @else
                                                    <option value="">Select</option>
                                                    @foreach(getAllClients() as $client_data)
                                                        <option value="{{$client_data->id}}" class="all-clients {{$client_data->status}}-clients" >{{$client_data->name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        @endif
                                    </div> 
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="first_name">First Name</label>
                                        <input id="first_name" data-parsley-required='true'  maxlength="255" autocomplete="off" type="text" class="form-control required" name="first_name" value="">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="last_name">Last Name</label>
                                        <input id="last_name" autocomplete="off" type="text" class="form-control required" name="last_name" value="" data-parsley-required='true'  maxlength="255">
                                    </div>
                                </div>
                                <img src="{{asset('images/table-loader.svg') }}" alt="loader1" style="display:none;height:30px; width:auto;" id="loaderImg" class="img-responsive center-block">
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="user_title">Title</label>
                                        <input id="user_title" autocomplete="off" type="text" class="form-control required" name="title" value="" data-parsley-required='true'  maxlength="255">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="user_email">Email</label>
                                        <input id="user_email" type="email" class="form-control required" name="email" value="" data-parsley-required="true"  data-parsley-trigger="change" data-parsley-trigger="keyup" data-parsley-email data-parsley-email-message="Please enter valid email Id" autocomplete="new-email">
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="vendorstatus">Role</label>
                                        <div class="dropdown select-dropdown">
                                            <select class="select2 selectsearch form-control vendorstatus" id="user_role" name="role" data-parsley-required='true' data-parsley-errors-container="#select2-role-error-message">
                                                <option value="">Select</option>
                                                @foreach($roles as $role)
                                                    <option value="{{$role->id}}">{{$role->display_name}}</option>
                                                @endforeach
                                            </select>
                                            <span id="select2-role-error-message"></span>
                                        </div>
                                    </div> 
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 deactivated-reason">
                                    <div class="form-group">
                                        <label for="deactivated_reason">Reason of Deactivated/Blacklisted</label>
                                        <textarea id="deactivated_reason" class="form-control" disabled> </textarea>
                                    </div>
                                </div>
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
        $(document).on('click', '.client-user-modal', function(e) {
            $(".ajax-error-message").html('');
            $(".help-block").remove('');
            $('.deactivated-reason').hide();
            $('#addclientuser .btn-green').show();
            $('#client-user-create-form').parsley().reset();
            $("#client-user-create-form")[0].reset();
            $(".select2").not(":disabled").prop("selectedIndex", 0).trigger('change.select2');

            var action_type = $(this).data('type');            
            var title = $(this).data('original-title');
            $('#addclientuser .modal-title').html(title);
            $("#client-user-create-form :input").prop("disabled", false);
            $(".all-clients").prop('disabled',false);
            
            if (action_type == 'new') {
                $('#client-user-id').val('');
                $(".inactive-clients").prop('disabled',true);
                $('#addclientuser').modal();
                $("#client-user-create-form label").addClass('yesstar');
            } else {
                var id = $(this).data('id');
                $('#client-user-id').val(id);
                var client_id = "{{$client_data}}";
                if(client_id == 0){
                    var url = "{{route('client.getUser',['client_id'=>$client_data])}}";
                }else{
                    var url = "{{route('admin.client.getUsers')}}";
                }
                $.ajax({
                    url: url,
                    data: {
                        user_id: id
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#first_name').val(response.data.first_name);
                            $('#last_name').val(response.data.last_name);           
                            $('#user_title').val(response.data.title);           
                            $('#user_email').val(response.data.email);
                            if(response.data.roles.length > 0) {
                                $('#user_role').val(response.data.roles[0].id).trigger('change.select2');
                            }
                            $("#all_clients").val(response.data.client_id).trigger('change.select2');
                            if(response.data.status == 'inactive' && action_type == 'view') {
                                $('.deactivated-reason').show();
                                $('#deactivated_reason').val(response.data.deactivationreason);  
                            }
                        }

                        $('#addclientuser').modal();
                    },
                    error: function(xhr) {
                        console.log(xhr);
                    }
                }); 

                if (action_type == 'view') {
                    $("#client-user-create-form label").removeClass('yesstar');
                    $("#client-user-create-form :input").prop("disabled", true);
                    $('#addclientuser .btn-green').hide();
                    $(".cancel-btn").prop("disabled", false);
                    $("#addclientuser .modal-body").addClass("view-mode");
                } else {
                    $('#client-user-create-form').parsley().reset();
                    //$("#client-user-create-form")[0].reset();
                    $("#client-user-create-form label").addClass('yesstar');
                }
            }
            $("#all_clients select").select2();
            @if(Request::route()->getName() == 'client.show' || Request::route()->getName() == 'client.edit')
                   $("#all_clients").prop("disabled", true);
            @endif
        });
    
        $("#client-user-create-form").submit(function(e) {
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
                    
                    $('#addclientuser').modal("hide");
                    $('#loaderImg').hide(); 
                    $("#btn_save").prop("disabled", false);
                    if (response.status == 'success') {
                        printAjaxSuccessMsg(response.message);    
                    } else {
                        printAjaxErrorMsg(response.message); 
                    }
                    $('#client-user-table,#all-user-table').DataTable().ajax.reload();
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
    });

    $('#addclientuser').on('hidden.bs.modal', function () {
        $('#client-user-create-form').parsley().reset();
        $("#addclientuser .modal-body").removeClass("view-mode");
    });

</script>
@endpush
