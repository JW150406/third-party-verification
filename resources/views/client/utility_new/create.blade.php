@push('styles')
<style>
   
</style>
@endpush
<div class="team-addnewmodal v-star">
    <div class="modal fade" id="addnew_utility" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span><?php echo getimage("images/info-modal.png"); ?></span>Utility Detail</h4>
                </div>
                <div class="ajax-error-message"></div>
                <div class="modal-body">
                    <div class="modal-form row">
                        <div class="">

                            <form enctype="multipart/form-data" id="addnewutility-form" role="form" method="POST" action="{{ route('client.utility.store',['client' => $client_id]) }}" data-parsley-validate>
                                {{ csrf_field() }}
                                <input type="hidden" name="id" id="utility_id">
                                <div class="ajax-response"></div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <label for="utility_commodity">Commodity</label>
                                    <div class="form-group">
                                        <select class="select2 form-control" id="utility_commodity" name="commodity" data-parsley-required='true' data-parsley-required-message="Please select commodity" data-parsley-errors-container="#select2-commodity-error-message">
                                            <option value="">Select</option>
                                        </select>
                                        <span id="select2-commodity-error-message"></span>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="utilityname">Brand Name</label>
                                        <!-- <input id="utilityname" autocomplete="off" type="text" class="form-control required" name="utilityname" value="" data-parsley-required='true' data-parsley-required-message="Please enter brand name"> -->
                                        <select class="select2 form-control" name="brand_id" id="utilityname" data-parsley-required='true'  data-parsley-errors-container="#select2-utility1-error-message" data-parsley-required-message="Please select brand name">
                                            <option value="" selected>Select</option>
                                            
                                            @foreach($brands as $brand)
                                                <option value="{{$brand->id}}">{{$brand->name}}</option>
                                            @endforeach
                                            <!-- <option value="other" >Other</option> -->
                                        </select>
                                        <span id="select2-utility1-error-message"></span>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="provider">Utility</label>
                                        <input id="provider" autocomplete="off" type="text" class="form-control required" name="fullname" value="" data-parsley-required='true' data-parsley-required-message="Please enter utility provider">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="abbreviation">Abbreviation</label>
                                        <input id="abbreviation" autocomplete="off" type="text" class="form-control required" name="market" value="" data-parsley-required='true' data-parsley-required-message="Please enter abbreviation">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="regex">RegEx</label>
                                        <input id="regex" autocomplete="off" type="text" class="form-control required" name="regex" value="" data-parsley-required='true' data-parsley-required-message="Please enter RegEx">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="regex-message">RegEx Error Message</label>
                                        <input id="regex-message" autocomplete="off" type="text" class="form-control required" name="regex_message" value="" data-parsley-required='true' data-parsley-required-message="Please enter RegEex error message">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="all-zip">Zipcodes</label>
                                        <div class="zipcode-all scrollbar-inner" id="all-zip">

                                        </div>
                                    </div>
                                    <span id="zip-error" class="error"></span>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group autocomplete">
                                        <input id="auto_suggest_zip" type="text" class="form-control" placeholder="Find & Add">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="act_num_verbiage" class="nostar">Account Number Type</label>
                                        <input id="act_num_verbiage" autocomplete="off" type="text" class="form-control" name="act_num_verbiage">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $(document).on('click', '.view-utility,.edit-utility', function(e) {
            $("#zip-error").text("");
            getCommodities();
            getBrandNames();
            $(".ajax-error-message").html('');
            $(".help-block").remove('');
            $("#all-zip").html('');
            var action_type = $(this).data('type');
            var title = $(this).data('original-title');
            $('#addnew_utility .modal-title').html(title);
            var utility_id = $(this).data('id');
            $('.close-zipcode').removeClass('vs-hidden');

            $.ajax({
                url: "{{route('utility.edit')}}",
                data: {
                    id: utility_id
                },
                success: function(response) {
                    if (response.status == 'success') {
                        $('#utility_commodity').val(response.data.commodity_id).trigger('change');
                        $('#utilityname').val(response.data.brand_id).trigger('change');
                        $('#provider').val(response.data.fullname);
                        $('#abbreviation').val(response.data.market);
                        $('#regex').val(response.data.regex);
                        $('#regex-message').val(response.data.regex_message);
                        $('#act_num_verbiage').val(response.data.act_num_verbiage);

                        $.each(response.data.utility_zipcodes, function(key, value) {
                            $("#all-zip").append('<div class="alert alert-defualt alert-dismissible"><input type="hidden" name="zipcode[]" data-parsley-required=\'true\' data-parsley-required-message="Please Find & Add"  value="' + value.zip_code.zipcode + '" /><a href="javascript:void(0)" class="close close-zipcode" data-dismiss="alert" aria-label="close">×</a>' + value.zip_code.zipcode + '</div>');
                        });

                        if (action_type == 'view') {
                            $("#utility_commodity").removeClass("show-down");
                            $("#utility_commodity").addClass('hide-down');
                            $("#utilityname").removeClass("show-down");
                            $("#utilityname").addClass('hide-down');
                            $("#addnewutility-form label").removeClass('yesstar');
                            $('.close-zipcode').hide();
                            $('.close-zipcode').addClass('vs-hidden');
                        } else {
                            $("#utility_commodity").removeClass("hide-down");
                            $("#utility_commodity").addClass("show-down");
                            $("#utilityname").removeClass("hide-down");
                            $("#utilityname").addClass("show-down");
                            $("#addnewutility-form label").addClass('yesstar');
                            $('.close-zipcode').show();
                        }
                    }

                    $('#addnew_utility').modal();
                },
                error: function(xhr) {
                    console.log(xhr);
                }
            });


            if (action_type == 'view') {
                $("#utility_commodity").removeClass("show-down");
                $("#utility_commodity").addClass('hide-down');
                $("#utilityname").removeClass("show-down");
                $("#utilityname").addClass('hide-down');
                $("#addnewutility-form :input").prop("disabled", true);
                $('#addnew_utility .modalbtns .saveDisable').hide();
                $('#addnew_utility .modalbtns .cancel-btn').html('Close').removeAttr('disabled');
                $('.autocomplete').hide();
                $("#utility_id").val(null);
                $("#addnew_utility .modal-body").addClass("view-mode");

            } else {
                $("#utility_commodity").removeClass("hide-down");
                $("#utility_commodity").addClass("show-down");
                $("#utilityname").removeClass("hide-down");
                $("#utilityname").addClass("show-down");
                $("#addnewutility-form :input").prop("disabled", false);
                $('#addnew_utility .modalbtns .saveDisable').show();
                $('#addnew_utility .modalbtns .cancel-btn').html('Cancel');
                $('.autocomplete').show();
                $("#utility_id").val(utility_id);
            }


        });


        $('.selectmenucomodity').select2();
        
        $('#addnew_utility-btn').click(function(e) {
            $("#utility_commodity").removeClass("hide-down");
            $("#utility_commodity").addClass("show-down");
            $("#utilityname").removeClass("hide-down");
            $("#utilityname").addClass("show-down");
            $("#utilityname").val(null).trigger('change');
           // $('.ajax-loader').show();
            getCommodities();
            getBrandNames();
            $(".ajax-error-message").html('');
            $(".help-block").remove('');
            $("#all-zip").html('');
            $("#addnewutility-form :input").prop("disabled", false);
            $("#addnewutility-form")[0].reset();
            $("#utility_id").val(null);
            $('.autocomplete').show();
            $('#addnew_utility .modalbtns .saveDisable').show();
            $('#addnew_utility .modal-title').html("Add Utility");
            $('#addnew_utility .modalbtns .cancel-btn').html('Cancel');
            $("#zip-error").text("");
            $("#addnewutility-form label").addClass('yesstar');
        });
        $('#save-utility-btn').click(function(e) {
            var totalZips = $('#all-zip .alert-defualt').length;
            if (totalZips <= 0) {
                $("#zip-error").html("<span class='help-block' >Please find and add at least one zipcode</span>");
            }
        });
        $("#addnewutility-form").submit(function(e) {
            var totalZips = $('#all-zip .alert-defualt').length;
            if (totalZips <= 0) {
                $("#zip-error").html("<span class='help-block' >Please find and add at least one zipcode</span>");
                return false;
            }
            e.preventDefault(); // avoid to execute the actual submit of the form.

            var form = $(this);
            var url = form.attr('action');
            $('.ajax-loader').show(); 
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(response) {
                    $('.ajax-loader').hide(); 
                    $('#addnew_utility').modal("hide");
                    if (response.status == 'success') {
                        printAjaxSuccessMsg(response.message);
                    } else {
                        printAjaxErrorMsg(response.message);
                    }
                    $("#utility-table").DataTable().ajax.reload();
                },
                error: function(xhr) {
                    $('.ajax-loader').hide(); 
                    if (xhr.status == 422) {
                        printUtilityErrorMsg(form,xhr.responseJSON.errors);
                    }
                }
            });
        });

        function getCommodities(){
            $('#utility_commodity').html('');
            $.ajax({
                url: "{{route('client.getCommodities',$client_id)}}",
                
                success: function(response) {
                    if (response.status == 'success') {
                        $('#utility_commodity').append("<option value=''>Select</option>");
                        $.each(response.data, function(key, value) {
                            $('#utility_commodity').append("<option value='"+value.id+"'>"+value.name+"</option>");
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr);
                }
            });       
        }

        function getBrandNames(){
            $('#utilityname').html('');
            $.ajax({
                url: "{{route('client.getBrands',$client_id)}}",
                
                success: function(response) {
                    if (response.status == 'success') {
                        $('#utilityname').append("<option value=''>Select</option>");
                        $.each(response.data, function(key, value) {
                            $('#utilityname').append("<option value='"+value.id+"'>"+value.name+"</option>");
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr);
                }
            });       
        }

        function printUtilityErrorMsg(form,msg) {
            $(".help-block").remove('');
            var errors='';
            $.each( msg, function( key, value ) {
                $(form).find("[name='"+key+"']").after("<span class='help-block' >"+value[0]+"</span>");
                if(key == 'zipcode') {
                    $("#auto_suggest_zip").after("<span class='help-block' >"+value[0]+"</span>");
                }
            });
        }

        $('#addnew_utility').on('hidden.bs.modal', function () {
            $("#utility_commodity").addClass("show-down");
            $("#utilityname").addClass("show-down");
            $('#addnewutility-form').parsley().reset();
            $("#addnew_utility .modal-body").removeClass("view-mode");
        });

        // $(document).ajaxStart(function(){ 
        //     $('.ajax-loader').show(); 
        // });
        // $(document).ajaxStop(function(){ 
        //     $('.ajax-loader').hide(); 
        // });
    });
</script>
@include('client.utility_new.auto-suggest-zipcode')
@endpush
