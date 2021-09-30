<!-- Add client user Modal Starts -->
<?php 

if(Auth::user()->isAccessLevelToClient()) {
    $client_id = Auth::user()->client_id;
}
?>
<div class="team-addnewmodal v-star">
    <div class="modal fade" id="addSalesCenterUser" tabindex="-1" role="dialog" aria-labelledby="SalesCenterLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="SalesCenterLabel">Add Sales Center User</h4>
                </div>
                <div class="modal-body">                    
                    <div class="modal-form row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <form  id="salescenter-user-create-form" role="form" method="POST" action="{{route('salescenter.users.createOrUpdate')}}" data-parsley-validate="false">
                                @csrf
                                <input type="hidden" name="id" id="salescenter-user-id">
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="all_clients1">Client</label>
                                        
                                        @if(Auth::user()->isAccessLevelToClient())
                                        
                                            @foreach(getAllClients() as $client)
                                                <input  type="text" value="{{$client->name}}" readonly=""  class="form-control">
                                                <input type="hidden" id="all_clients1" name="client" value="{{$client->id}}" >
                                            @endforeach
                                       @elseif(isset($client) && !empty($client))
                                       <div class="dropdown select-dropdown">
                                            <select id="all_clients1" name="client" class="select2 form-control" data-parsley-required='true'>
                                                <option value="{{$client->id}}" class="all-clients {{$client->status}}-clients" selected > {{$client->name}}</option>
                                            </select>
                                        </div>
                                            <input type="hidden" id="all_clients1" name="client" value="{{$client->id}}">
                                        @else
                                        
                                        <div class="dropdown select-dropdown">
                                            <select id="all_clients1" name="client" class="select2 form-control" data-parsley-required='true'>
                                                <option value="" selected>Select</option>
                                                @foreach(getAllClients() as $client)
                                                <option value="{{$client->id}}" class="all-clients {{$client->status}}-clients" @if($client->id == $client_id) selected @endif> {{$client->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="all_salescenter">Sales center</label>
                                        <div class="dropdown select-dropdown">
                                            <select id="all_salescenter" name="sales_center" class="select2 form-control" data-parsley-required='true'>

                                                <option value="" selected>Select</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="vendorstatus">Role</label>
                                        <div class="dropdown select-dropdown">
                                            <select class="select2 selectsearch form-control vendorstatus" id="user_role1" name="role" data-parsley-required='true'  >
                                                <option value="">Select</option>
                                                @foreach($roles as $role)
                                                    <option value="{{$role->id}}">{{$role->display_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 location-section" >
                                    <div class="form-group">
                                        <label for="vendorstatus">Location</label>
                                        <div class="dropdown select-dropdown">
                                            <select class="form-control select2 vendorstatus" id="user_location" name="location_id[]" data-parsley-required='true'>
                                                <!-- <option value="">Select</option>
                                                <option class="locations-opt salescenter-2026" value="12" client="102" salescenter="2026">L1</option><option class="locations-opt salescenter-2026" value="11" client="102" salescenter="2026">L2</option><option class="locations-opt salescenter-2026" value="13" client="102" salescenter="2026">L3</option> -->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="first_name1">First Name</label>
                                        <input id="first_name1" autocomplete="off" type="text" class="form-control required" name="first_name" data-parsley-required='true'  maxlength="255">
                                    </div>
                                </div>
                                <img src="{{asset('images/table-loader.svg') }}" alt="loader1" style="display:none;height:30px; width:auto;" id="loaderImg" class="img-responsive center-block">
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="last_name1">Last Name</label>
                                        <input id="last_name1" autocomplete="off" type="text" class="form-control required" name="last_name" data-parsley-required='true'  maxlength="255">
                                    </div>
                                </div>
                                
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input id="email" autocomplete="off" type="text" class="form-control required" name="email" 
                                        data-parsley-required='true'
                                          data-parsley-trigger="change" data-parsley-trigger="keyup" data-parsley-email>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 deactivated-reason">
                                    <div class="form-group">
                                        <label for="deactivated_reason_sales">Reason of Deactivated/Blacklisted</label>
                                        <textarea id="deactivated_reason_sales" class="form-control" disabled> </textarea>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
                                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                        <div class="btn-group">
                                            <button type="submit" class="btn btn-green save-btn" id="btn_save_user"><span class="save-text">Save</span></button>
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
    var action_type;
    $(document).ready(function() {

        $("#all_clients1").val('{{$client_id}}');
        $(document).on('click', '.salescenter-user-modal', function(e) {
            $('#user_location').prop('multiple','');
            $(".help-block").remove('');
            $("#all_salescenter").html('');
            $(".location-section").show();
            $('.deactivated-reason').hide();
            $('.ms-options').css("visibility", "visible");
            $('#addSalesCenterUser .save-btn').show();
            $("#salescenter-user-create-form")[0].reset();
            $('#salescenter-user-create-form').parsley().reset();
            var location = '';
            action_type = $(this).data('type');            
            var title = $(this).data('original-title');
            $('#addSalesCenterUser .modal-title').html(title);
            $("#salescenter-user-create-form :input").prop("disabled", false);
            $(".all-clients,.all-salescenters").prop('disabled',false);
            
            @if(Request::route()->getName() == 'client.salescenter.show' || Request::route()->getName() == 'client.salescenters.edit')
                $("#all_salescenter").append('<option value="{{$salescenter->id}}" selected>{{$salescenter->name}} </option>');
                $("#all_clients1,#all_salescenter").prop("disabled", true);
            @else
                $("#all_salescenter").append('<option value="" selected>Select </option>');
            @endif
            if (action_type == 'new') {
                $('#salescenter-user-id').val('');
                $("#salescenter-user-create-form label").addClass('yesstar');
                $('form .select2').not(":disabled").val('').trigger("change.select2");
                $(".inactive-clients,.inactive-salescenters").prop('disabled',true);
                @if(Request::route()->getName() == 'admin.sales.users')
                @if(Auth::user()->isAccessLevelToClient())
                    getSalesCenter();
                @endif
                @endif

            } else {
                var id = $(this).data('id');
                var client_id = $(this).data('client-id');
                var salescenter_id = $(this).data('salescenter-id');
                var salescenter_name = $(this).data('salescenter-name');
                var first_name = $(this).data('first-name');
                var last_name = $(this).data('last-name');
                var email = $(this).data('email');
                var role = $(this).data('role');
                var roleName = $(this).data('role-name');
                location = $(this).data('location');
                if (roleName == 'sales_center_qa') {
                    location = location.toString().split(',');
                }
                console.log(location);
                $('#salescenter-user-id').val(id);
                $('#first_name1').val(first_name);           
                $('#last_name1').val(last_name);           
                $('#email').val(email);   
                $('#all_clients1').val(client_id).trigger("change.select2");   
                $("#all_salescenter").append('<option value="'+salescenter_id+'" selected> '+salescenter_name+'</option>');

                $('#user_role1').val(role).trigger('change');


                if (action_type == 'view') {
                    $("#salescenter-user-create-form label").removeClass('yesstar');
                    $("#salescenter-user-create-form :input").prop("disabled", true);
                    $('#addSalesCenterUser .save-btn').hide();
                    $(".cancel-btn").prop("disabled", false);
                    var status = $(this).data('status');
                    var deactivationreason = $(this).data('reason');
                    if(status == 'inactive') {
                        $('.deactivated-reason').show();
                        $('#deactivated_reason_sales').val(deactivationreason);  
                    }
                    $("#addSalesCenterUser .modal-body").addClass("view-mode");
                    
                } else {
                    $("#salescenter-user-create-form label").addClass('yesstar');                    
                    $("#all_clients1,#all_salescenter").prop("disabled", true);  
                } 
            }
            setLocations(location);
            $("#all_clients1 select,#all_salescenter select").select2();
            $('#addSalesCenterUser').modal();

        });
    
        $("#salescenter-user-create-form").submit(function(e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.
            $("#all_clients1,#all_salescenter").prop("disabled", false);

            var form = $(this);
            var url = form.attr('action');
            $('#btn_save_user').prop("disabled", true);
            $('#loaderImg').show();
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(response) {
                    $('#loaderImg').hide(); 
                    $("#btn_save_user").prop("disabled", false);
                    $('#addSalesCenterUser').modal("hide");
                    if (response.status == 'success') {
                        printAjaxSuccessMsg(response.message);    
                    } else {
                        printAjaxErrorMsg(response.message); 
                    }
                    $('#sales-center-user-table,#all-user-table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    if (xhr.status == 422) {
                        $('#loaderImg').hide(); 
                        $("#btn_save_user").prop("disabled", false);
                        printErrorMsgNew(form,xhr.responseJSON.errors);
                    }
                }
            });
        });

        $('#all_clients1').on('change', function(e) {
            getSalesCenter();
        });

        $('#all_salescenter').on('change', function(e) {
            setLocations();
        });

        $('#user_role1').on('change', function(e) {
            const role = $("#user_role1 option:selected").text();
            console.log(role);
            @if(!Auth::user()->isLocationRestriction()) 
                $("#user_location").val("");
            @endif
            if (role == 'Sales Center Admin') {
                $(".location-section").hide();
                $('#user_location').attr('data-parsley-required', 'false');
            } else {
                if (role == 'Sales Center QA') {
                    $('#user_location').prop('multiple','multiple');
                    if ($('#user_location').hasClass('select2')) {
                        $('#user_location').select2('destroy');
                        $('#user_location').removeClass('select2');
                    }
                    $('#user_location').multiselect({placeholder: 'Select'});
                    $('.ms-options label').append('<span class="checkmark" style="left:10px;top:10px"></span>');
                    $('.ms-options label').addClass('custom-checkbox').css('cssText','margin-bottom: 0px !important;');

                    //$('#user_location').find('option[value=""]').prop('selected', false).prop('disabled', true);
                } else {
                    $('#user_location').prop('multiple','');
                    $('#user_location').multiselect('unload');
                    $('#user_location').addClass('select2');
                    $('#user_location').select2();
                    
                    //$('#user_location').find('option[value=""]').prop('selected', true).prop('disabled', false);
                }
                $(".location-section").show();
                $('#user_location').attr('data-parsley-required', 'true');
            }
        });

        // $('#user_location').multiselect({
        //     columns: 1,
        //     placeholder: 'Select',
        // });
        function getSalesCenter()
        {
            var clientId = $("#all_clients1").val();
            setSalesCenterOptions("all_salescenter",clientId);
        }

        function setLocations(location='')
        {
            @if(Auth::user()->isLocationRestriction()) 
                location = "{{ Auth::user()->location_id }}";
            @endif
            var clientId = $("#all_clients1").val();
            var salescenterId = $("#all_salescenter").val();
            const role = $("#user_role1 option:selected").text();
            let isMultiSelect = false;
            if (role == 'Sales Center QA') {
                isMultiSelect = true;
            }
            setSalesCenterLocationOptions("user_location",clientId,salescenterId,location,false,'',isMultiSelect,action_type);
        }
    });

    $('#addSalesCenterUser').on('hidden.bs.modal', function () {
        $('#salescenter-user-create-form').parsley().reset();
        $("#addSalesCenterUser .modal-body").removeClass("view-mode");
    });

</script>
@endpush
