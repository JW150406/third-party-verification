<!-- Add Location Modal Starts -->

<div class="team-addnewmodal v-star">
    <div id="addSalesCenterLocation" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="">Add Sales Center Location</h4>
                </div>
                <div class="modal-body">
                    <div class="ajax-error-message">
                    </div>

                    <form class="row" id="salescenter-location-create-form" role="form" method="POST" action="{{route('salescenter.location.createOrUpdate',['client_id'=>$client_id,'salescenter_id'=>$salecenter_id])}}" data-parsley-validate>
                        @csrf
                        <input type="hidden" name="id" id="salescenter-location-id">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label for="location_name">Location Name</label>
                                <input id="location_name" name="name" autocomplete="off" type="text" class="form-control required" data-parsley-required='true' data-parsley-required-message="Please enter location name" >
                            </div>
                        </div>

                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="form-group">
                                <label for="location_code">Code</label>
                                <input autocomplete="off" id="location_code" name="code" type="text" class="form-control cursor-none" onfocus="this.blur();" >
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6" >
                            <div class="form-group loc-channel-select">
                                <label for="location_channel">Channel</label>
                                <select class="form-control" id="location_channel" name="channels[]" data-parsley-required='true'  multiple="multiple" data-parsley-errors-container="#location-channel-error">
                                    {!! getChannelOption($client_id) !!}
                                    <!-- <option value="tele">Tele</option>
                                    <option value="d2d">D2D</option> -->
                                    <!-- <option value="retail">Retail</option> -->
                                </select>
                                <div id="location-channel-error"></div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12">
                        <h5>Address</h5>
                        </div>

                        {{--<div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label for="location_street" class="nostar">Street</label>
                                <input id="location_street" name="street" autocomplete="off" type="text" class="form-control required" data-parsley-required='false' data-parsley-required-message="Please enter street">
                            </div>
                        </div>--}}
                        <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group {{ $errors->has('street')   ? ' has-error' : '' }}">
                            <label for="street" class="nostar">Street</label>
                            <span class="form-icon"><img src="{{ asset('images/location.png')}}"></span>
                            <input id="location_street" type="text" class="form-control" name="street" value="{{ old('street') }}" data-parsley-required='false' data-parsley-required-message="Please enter address">

                            @if ($errors->has('street'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('street') }}</strong>
                                </span>
                            @endif

                        </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6">
                            <div class="form-group">
                                <label for="location_city" class="nostar">City</label>
                                <input id="location_city" name="city" autocomplete="off" type="text" class="form-control required" data-parsley-required='false' data-parsley-required-message="Please enter city">
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6">
                            <div class="form-group">
                                <label for="location_state" class="nostar">State</label>
                                <input id="location_state" name="state" autocomplete="off" type="text" class="form-control required" data-parsley-required='false' data-parsley-required-message="Please enter state">
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6">
                            <div class="form-group">
                                <label for="location_country" class="nostar">Country</label>
                                <input id="location_country" name="country" autocomplete="off" type="text" class="form-control required" data-parsley-required='false' data-parsley-required-message="Please enter country">
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6">
                            <div class="form-group">
                                <label for="location_zip" class="nostar">Zipcode</label>
                                <input id="location_zip" name="zipcode" autocomplete="off" type="text" class="form-control required" data-parsley-required='false' data-parsley-type="digits" data-parsley-length="[5,5]" data-parsley-required-message="Please enter zipcode" data-parsley-length-message = "The Zipcode must be 5 digits">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                        <h5>Contact</h5>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label for="contact_name" class="nostar">Name</label>
                                <input id="contact_name" name="contact_name" autocomplete="off" type="text" class="form-control" data-parsley-required='false' data-parsley-required-message="Please enter name">
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label for="contact_number" class="nostar">Contact No.</label>
                                <input id="contact_number" name="contact_number" autocomplete="off" type="text" class="form-control" data-parsley-required='false' data-parsley-type="digits" data-parsley-length="[10,10]" data-parsley-required-message="Please enter contact number"
                                data-parsley-length-message = "The Contact No must be 10 digits">
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
                            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                <div class="btn-group">
                                    <button type="submit" id="location-sub-btn" class="btn btn-green">Save</button>
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

<!-- add client user Modal ends -->

@push('scripts')

<script>
    var isFormSubmit = false;
    function init(){
        var CAutocomplete = new google.maps.places.Autocomplete(document.getElementById('location_street'), {
            types: [],
            componentRestrictions: {country: "us"}
        });
        google.maps.event.addListener(CAutocomplete, 'place_changed', function () {
            var place = CAutocomplete.getPlace();
            $('#location_street').val(place.name);
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (addressType === 'postal_code') {
                    $('#location_zip').val(place.address_components[i].long_name);
                }
                if (addressType === "locality" || addressType === "administrative_area_level_2") {
                    $('#location_city').val(place.address_components[i].long_name);
                }
                if (addressType === "administrative_area_level_1") {
                    $('#location_state').val(place.address_components[i].long_name);
                }
                if (addressType === "country") {
                    $('#location_country').val(place.address_components[i].long_name);
                }
            }

        });
    }

    $(document).ready(function() {        
        $('#location_channel').multiSelect();
        $('label.multi-select-menuitem').append('<span class="checkmark"></span>');
        $('label.multi-select-menuitem').addClass('custom-checkbox');
        $('.multi-select-menu').addClass('scrollbar-inner');
        $('.multi-select-menu').addClass('scrollbar-dynamic');
        $("#salescenter-location-create-form").find('.multi-select-menu').addClass("multi-selecet-mini");
        $("#salescenter-location-create-form").find('.multi-select-menu').css('top','34px');

        $(document).on('click', '.salescenter-location-modal', function(e) {
            $('#location_channel').prop('selectedIndex',-1);
            $(".ajax-error-message").html('');
            $('.multi-select-button').html([]);
            $(".help-block").remove('');
            init();
            $('#addSalesCenterLocation .btn-green').show();
            $("#salescenter-location-create-form")[0].reset();

            var location_channel = $('#location_channel').closest(".form-group").find('.multi-select-menu.scrollbar-inner');
            location_channel.css("visibility", "visible");
            
            var action_type = $(this).data('type');
            var title = $(this).data('original-title');
            $('#addSalesCenterLocation .modal-title').html(title);
            if (action_type == 'new') {                
                $('#salescenter-location-id').val('');
            } else {
                var id = $(this).data('id');
                $('#salescenter-location-id').val(id);
                $.ajax({
                    url: "{{route('salescenter.location.show')}}",
                    data: {id:id},
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#location_name').val(response.data.name);
                            $('#location_code').val(response.data.code);
                            $('#location_street').val(response.data.street);
                            $('#location_city').val(response.data.city);
                            $('#location_state').val(response.data.state);
                            $('#location_country').val(response.data.country);
                            $('#location_zip').val(response.data.zip);
                            $('#contact_name').val(response.data.contact_name);
                            $('#contact_number').val(response.data.contact_number);

                            var channels = response.channels;
                            if (channels.length > 0) {
                                $(".multi-select-menu :input").prop("disabled", false);
                                for (var i in channels) {
                                    var optionVal = channels[i];
                                    $("#location_channel").find("option[value=" + optionVal + "]").prop("selected", "selected");
                                    $("input[value=" + optionVal + "]").trigger('click');
                                }
                            }
                            disableD2Doption();
                            $('#addSalesCenterLocation').modal();
                        } else {
                            printAjaxErrorMsg(response.message);
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr);
                    }
                });
            }

            if (action_type == 'view') {
                $("#salescenter-location-create-form label").removeClass('yesstar');
                $("#salescenter-location-create-form :input").prop("disabled", true);
                $('#addSalesCenterLocation .btn-green').hide();
                $(".btn-red").prop("disabled", false);
                location_channel.css("visibility", "hidden");
                $("#addSalesCenterLocation .modal-body").addClass("view-mode");
            } else {
                $("#salescenter-location-create-form label").addClass('yesstar');
                $("#salescenter-location-create-form :input").prop("disabled", false);

            }
            $(".multi-select-container").find('label').removeClass('yesstar');
            disableD2Doption();
            $('#addSalesCenterLocation').modal();
        });

        $("#salescenter-location-create-form").submit(function(e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.

            var form = $(this);
            var url = form.attr('action');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(response) {
                    $('#addSalesCenterLocation').modal("hide");
                    if (response.status == 'success') {
                        printAjaxSuccessMsg(response.message);
                    } else {
                        printAjaxErrorMsg(response.message);
                    }
                    getSalseCenterLocations();
                    $('#sales-center-location-table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    if (xhr.status == 422) {
                        printErrorMsgNew(form,xhr.responseJSON.errors);
                    }
                }
            });
        });

        //Sales center location code auto generate script
        $("#location_name").keyup(function() {
            var string = $(this).val();
            if (string) {
                var matches = string.match(/\b(\w)/g);
                var finalString = matches.join('');
                $.post(
                    "{{ route('client.sales-centers-locations.check-code', array_get($client, 'id')) }}", {
                        "_token": "{{ csrf_token() }}",
                        "code": finalString
                    },
                    function(result) {
                        if (result.status) {
                            $("#location_code").val(result.code.toUpperCase());
                        }
                    }
                );
            } else {
                $("#location_code").val("");
            }
            $("#location_code").trigger('input');
        });
        //End auto generate script

        $(document).on('click', '#location-sub-btn', function(e) {
            isFormSubmit=true;
        });
        $(document).on('change', '#location_channel', function(e) {
            if (isFormSubmit) {
                $(this).parsley().validate();
            }
        });
    });

    function disableD2Doption() {
        $("#location_channel option:disabled" ).each(function() {
            console.log(this.value);
            var input = $('#location_channel').closest(".form-group").find("input[value=" + this.value + "]");
            input.attr('disabled',true);
            input.parent().addClass("cursor-none");
        });
    }

    $("#location_code").keypress(function() {
        return false;
    });

    // To reset error messages
    $("#location_street").keypress(function() {
        $('#salescenter-location-create-form').parsley().reset();
    });

    $('#addSalesCenterLocation').on('hidden.bs.modal', function () {
        $('#salescenter-location-create-form').parsley().reset();
        $("#addSalesCenterLocation .modal-body").removeClass("view-mode");
    });
</script>
@endpush
