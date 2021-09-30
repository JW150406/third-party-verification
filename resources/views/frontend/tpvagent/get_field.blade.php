<div id="edit_question_wrapper">
    <form method="post" action="" autocomplete="on" id="editQuestionField" role="form" data-parsley-validate>
        @csrf
        <input type="hidden" name="field_id" value="{{ $field->id }}">
        <input type="hidden" name="lead_id" value="{{ $lead_id }}">
        @if($field->type == 'fullname')
            <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
            <div class="row">
                <div class="col-sm-4">
                    <input class="form-control"
                           type="text"
                           name="field[first_name]"
                           placeholder="First Name"
                           autocomplete="new"
                           data-parsley-trigger="focusout"
                           @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                           data-parsley-required-message="Please enter first name"
                           @endif
                           value="{{ isset($values['first_name']) && !empty($values['first_name']) ? $values['first_name'] : '' }}">
                </div>
                <div class="col-sm-4">
                    <input type="text"
                           class="form-control"
                           name="field[middle_initial]"
                           placeholder="Middle Name"
                           autocomplete="new"
                           value="{{ isset($values['middle_initial']) && !empty($values['middle_initial']) ? $values['middle_initial'] : '' }}">
                </div>
                <div class="col-sm-4">
                    <input type="text"
                           class="form-control"
                           name="field[last_name]"
                           value="{{ isset($values['last_name']) && !empty($values['last_name']) ? $values['last_name'] : '' }}"
                           placeholder="Last Name"
                           autocomplete="new"
                           data-parsley-trigger="focusout"
                           @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                           data-parsley-required-message="Please enter last name" @endif>
                </div>
            </div>

        @elseif($field->type == 'textbox')
            <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
            <input type="text"
                   autocomplete="off"
                   class="form-control"
                   name="field[value]"
                   value="{{ isset($values['value']) && !empty($values['value']) ? $values['value'] : '' }}"
                   placeholder="{{ $field->name }}"
                   data-parsley-trigger="focusout"
                   @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                   data-parsley-required-message="Please enter {{strtolower(array_get($field, 'label'))}}"
                   @endif
                   @if(!empty($field->regex))
                   data-parsley-pattern="{{$field->regex}}"
                   @endif
                   @if(!empty($field->regex_message))
                   data-parsley-pattern-message="{{$field->regex_message}}" @endif>

        @elseif($field->type == 'textarea')
            <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
            <textarea
                    class="form-control"
                    name="field[value]"
                    placeholder="{{ $field->name }}"
                    data-parsley-trigger="focusout"
                    @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                    data-parsley-required-message="Please enter {{strtolower(array_get($field, 'label'))}}"
                    @endif
                    @if(!empty($field->regex))
                    data-parsley-pattern="{{$field->regex}}"
                    @endif
                    @if(!empty($field->regex_message))
                    data-parsley-pattern-message="{{$field->regex_message}}" @endif
    >{{ isset($values['value']) && !empty($values['value']) ? $values['value'] : '' }}</textarea>


        @elseif($field->type == 'radio')
            <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
            @foreach($field->meta as $meta)

                @foreach($meta as $option)

                    <div class="radio-btns pdt0">
                        <label class="radio-inline">
                            <input type="radio"
                                   name="field[value]"
                                   data-parsley-trigger="focusout"
                                   @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                   data-parsley-required-message="Please enter {{strtolower(array_get($field, 'label'))}}"
                                   @endif
                                   value="{{ $option['option'] }}" {{ isset($values['value']) && !empty($values['value']) ? $values['value'] == $option['option'] ? 'checked' : '' : '' }}> {{ $option['option'] }}
                        </label>

                    </div>



                @endforeach
                <div id="radio_button_{{array_get($field, 'id')}}"></div>
            @endforeach

        @elseif($field->type == 'checkbox')
            <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
            @foreach($field->meta as $meta)

                @foreach($meta as $option)

                    <div class="checkbx">
                        <label class="checkbx-style">
                            <input type="checkbox"
                                   class="{{ array_get($field, 'is_required' )==1 ? 'required' : '' }}"
                                   min="1"
                                   name="field[value][]"
                                   value="{{ $option['option'] }}"
                                   data-parsley-trigger="focusout"
                                   @if(array_get($field, 'is_required') == 1) data-parsley-mincheck="1" @endif
                                    {{ (isset($values['value']) && !empty($values['value'])) ? (in_array($option['option'], explode(', ', $values['value']))) ? 'checked' : '' : '' }}> {{ $option['option'] }}
                            <span class="checkmark"></span>
                        </label>
                    </div>

                @endforeach
                <div id="checkbox_error_{{array_get($field, 'id')}}"></div>
            @endforeach

        @elseif($field->type == 'address')
            <div rel="address">
                <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                <div class="row">
                    <div class="col-sm-12 address-field">
                        <input autocomplete="off"
                               id="address_line_1"
                               onfocus="this.setAttribute('autocomplete', 'new-password')"
                               class="form-control autocompletestreet address"
                               Placeholder="Address line 1"
                               name="field[address_1]"
                               value="{{ isset($values['address_1']) && !empty($values['address_1']) ? $values['address_1'] : '' }}"
                               data-parsley-trigger="focusout"
                               @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                               data-parsley-required-message="Please enter address" @endif>
                    </div>
                    <div class="col-sm-8 address-field">
                        <input autocomplete="new-password"
                               id="address_line_2"
                               class="form-control autocompletestreet address"
                               Placeholder="Address line 2"
                               name="field[address_2]"
                               value="{{ isset($values['address_2']) && !empty($values['address_2']) ? $values['address_2'] : '' }}">
                    </div>
                    <div class="col-sm-4 address-field">
                        <input autocomplete="new-password"
                               id="address_unit"
                               class="form-control autocompletestreet address"
                               Placeholder="Unit Number"
                               name="field[unit]"
                               value="{{ isset($values['unit']) && !empty($values['unit']) ? $values['unit'] : '' }}">
                    </div>
                    <div class="col-sm-3 address-field inline-block">
                        <input autocomplete="new-password"
                               id="address_city"
                               class="form-control"
                               placeholder="City"
                               name="field[city]"
                               value="{{ isset($values['city']) && !empty($values['city']) ? $values['city'] : '' }}">
                    </div>
                    <div class="col-sm-3 address-field inline-block">
                        <input id="address_state"
                               class="form-control stateall statefield address"
                               Placeholder="State"
                               autocomplete="new-password"
                               name="field[state]"
                               value="{{ isset($values['state']) && !empty($values['state']) ? $values['state'] : '' }}">
                    </div>
                    <div class="col-sm-3 address-field inline-block">
                        <input class="form-control"
                               id="address_zipcode"
                               name="field[zipcode]"
                               autocomplete="new-password"
                               Placeholder="Zipcode"
                               data-parsley-trigger="focusout"
                               data-parsley-pattern="[0-9]{5}"
                               data-parsley-pattern-message="Please enter 5 digit zipcode"
                               value="{{ isset($values['zipcode']) && !empty($values['zipcode']) ? $values['zipcode'] : '' }}">
                    </div>
                    <div class="col-sm-3 address-field inline-block">
                        <input id="address_country"
                               class="form-control stateall statefield address"
                               Placeholder="Country"
                               autocomplete="new-password"
                               name="field[country]"
                               value="{{ isset($values['country']) && !empty($values['country']) ? $values['country'] : '' }}">
                    </div>
                    <input type="hidden"
                           name="field[lat]"
                           id="address_latitude"
                           value="{{ isset($values['lat']) && !empty($values['lat']) ? $values['lat'] : '' }}">
                    <input type="hidden"
                           name="field[lng]"
                           id="address_longitude"
                           value="{{ isset($values['lng']) && !empty($values['lng']) ? $values['lng'] : '' }}">

                    <script>
                        var input = document.getElementById('address_line_1');
                        var autocomplete = new google.maps.places.Autocomplete(input, {
                            types: [],
                            componentRestrictions: {country: "us"}
                        });
                        google.maps.event.addListener(autocomplete, 'place_changed', function () {
                            var place = autocomplete.getPlace();
                            $('#address_latitude').val(place.geometry.location.lat());
                            $('#address_longitude').val(place.geometry.location.lng());
                            $('#address_line_1').val(place.name);
                            var address2 = '';
                            for (var i = 0; i < place.address_components.length; i++) {
                                var addressType = place.address_components[i].types[0];
                                if (addressType === 'postal_code') {
                                    $('#address_zipcode').val(place.address_components[i].long_name);
                                }
                                if (addressType === "neighborhood" || addressType === "sublocality_level_1") {
                                    address2 += place.address_components[i].long_name;
                                    address2 += ' '
                                }
                                if (addressType === "locality" || addressType === "administrative_area_level_2") {
                                    $('#address_city').val(place.address_components[i].long_name);
                                }
                                if (addressType === "administrative_area_level_1") {
                                    $('#address_state').val(place.address_components[i].long_name);
                                }
                                if (addressType === "country") {
                                    $('#address_country').val(place.address_components[i].long_name);
                                }
                            }
                            $('#address_line_2').val(address2);
                        })
                    </script>

                </div>
            </div>
        @elseif($field->type == 'service_and_billing_address')
            <div
                 rel="service_and_billing_address">
                <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                <div class="form-group mb0">
                    <label class="mt15">Service
                        Address</label>
                </div>
                <div class="row">
                    <div class="col-sm-12 address-field">
                        <input class="form-control autocompletestreet address"
                               Placeholder="Address Line 1"
                               onfocus="this.setAttribute('autocomplete', 'new-password')"
                               autocapitalize="none"
                               spellcheck="false"
                               id="service_and_billing_address_service_address_1"
                               name="field[service_address_1]"
                               value="{{ isset($values['service_address_1']) && !empty($values['service_address_1']) ? $values['service_address_1'] : '' }}"
                               @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                               data-parsley-trigger="focusout"
                               data-parsley-required-message="Please enter service address"
                               @endif
                               onkeyup="changeVal('service_and_billing_address_service_address_1', 'service_and_billing_address_billing_address_1');">
                    </div>
                    <div class="col-sm-8 address-field">
                        <input autocomplete="new-password"
                               class="form-control autocompletestreet address"
                               Placeholder="Address Line 2"
                               id="service_and_billing_address_service_address_2"
                               name="field[service_address_2]"
                               value="{{ isset($values['service_address_2']) && !empty($values['service_address_2']) ? $values['service_address_2'] : '' }}"
                               onkeyup="changeVal('service_and_billing_address_service_address_2', 'service_and_billing_address_billing_address_2');">
                    </div>
                    <div class="col-sm-4 address-field">
                        <input autocomplete="new-password"
                               class="form-control autocompletestreet address"
                               Placeholder="Unit Number"
                               id="service_and_billing_address_service_unit"
                               name="field[service_unit]"
                               value="{{ isset($values['service_unit']) && !empty($values['service_unit']) ? $values['service_unit'] : '' }}"
                               onkeyup="changeVal('service_and_billing_address_service_unit', 'service_and_billing_address_billing_unit');">
                    </div>
                    <div class="col-sm-3 address-field inline-block">
                        <input autocomplete="new-password"
                               class="form-control"
                               placeholder="City"
                               id="service_and_billing_address_service_city"
                               name="field[service_city]"
                               value="{{ isset($values['service_city']) && !empty($values['service_city']) ? $values['service_city'] : '' }}"
                               onkeyup="changeVal('service_and_billing_address_service_city', 'service_and_billing_address_billing_city');">
                    </div>
                    <div class="col-sm-3 address-field inline-block">
                        <input id="service_and_billing_address_service_state"
                               class="form-control stateall statefield address "
                               Placeholder="State"
                               autocomplete="new-password"
                               name="field[service_state]"
                               value="{{ isset($values['service_state']) && !empty($values['service_state']) ? $values['service_state'] : '' }}"
                               onkeyup="changeVal('service_and_billing_address_service_state', 'service_and_billing_address_billing_state');">
                    </div>
                    <div class="col-sm-3 address-field inline-block">
                        <input class="form-control"
                               id="service_and_billing_address_service_zipcode"
                               name="field[service_zipcode]"
                               value="{{ isset($values['service_zipcode']) && !empty($values['service_zipcode']) ? $values['service_zipcode'] : '' }}"
                               autocomplete="new-password"
                               data-parsley-trigger="focusout"
                               data-parsley-pattern="[0-9]{5}"
                               data-parsley-pattern-message="Please enter 5 digit zipcode"
                               Placeholder="Zipcode"
                               onkeyup="changeVal('service_and_billing_address_service_zipcode', 'service_and_billing_address_billing_zipcode', '');">
                    </div>
                    <div class="col-sm-3 address-field inline-block">
                        <input id="service_and_billing_address_service_country"
                               class="form-control stateall statefield address "
                               Placeholder="Country"
                               autocomplete="new-password"
                               name="field[service_country]"
                               value="{{ isset($values['service_country']) && !empty($values['service_country']) ? $values['service_country'] : '' }}"
                               onkeyup="changeVal('service_and_billing_address_service_country', 'service_and_billing_address_billing_country',);">
                    </div>
                    <input type="hidden"
                           name="field[service_lat]"
                           id="service_and_billing_address_service_latitude"
                           value="{{ isset($values['service_lat']) && !empty($values['service_lat']) ? $values['service_lat'] : '' }}">
                    <input type="hidden"
                           name="field[service_lng]"
                           id="service_and_billing_address_service_longitude"
                           value="{{ isset($values['service_lng']) && !empty($values['service_lng']) ? $values['service_lng'] : '' }}">
                    <script>

                        var input = document.getElementById('service_and_billing_address_service_address_1');
                        var autocompleteService = new google.maps.places.Autocomplete(input, {
                            types: [],
                            componentRestrictions: {country: "us"}
                        });
                        google.maps.event.addListener(autocompleteService, 'place_changed', function () {
                            var place = autocompleteService.getPlace();
                            $('#service_and_billing_address_service_latitude').val(place.geometry.location.lat());
                            $('#service_and_billing_address_service_longitude').val(place.geometry.location.lng());
                            $('#service_and_billing_address_service_address_1').val(place.name);
                            var address2 = '';
                            for (var i = 0; i < place.address_components.length; i++) {
                                var addressType = place.address_components[i].types[0];
                                if (addressType === 'postal_code') {
                                    $('#service_and_billing_address_service_zipcode').val(place.address_components[i].long_name);
                                }
                                if (addressType === "neighborhood" || addressType === "sublocality_level_1") {
                                    address2 += place.address_components[i].long_name;
                                    address2 += ' '
                                }
                                if (addressType === "locality" || addressType === "administrative_area_level_2") {
                                    $('#service_and_billing_address_service_city').val(place.address_components[i].long_name);
                                }
                                if (addressType === "administrative_area_level_1") {
                                    $('#service_and_billing_address_service_state').val(place.address_components[i].long_name);
                                }
                                if (addressType === "country") {
                                    $('#service_and_billing_address_service_country').val(place.address_components[i].long_name);
                                }
                            }
                            if ($('input[name="is_service_address_same_as_billing_address"]:checked').val() == "yes") {
                                copy_address_('yes')
                            }
                        })
                    </script>
                    <script>
                        function copy_address_(state) {
                            if (state == "yes") {
                                $("#service_and_billing_address_billing_unit").val($('#service_and_billing_address_service_unit').val()).attr('readonly', 'readonly');
                                $("#service_and_billing_address_billing_address_1").val($('#service_and_billing_address_service_address_1').val()).attr('readonly', 'readonly');
                                $("#service_and_billing_address_billing_address_2").val($('#service_and_billing_address_service_address_2').val()).attr('readonly', 'readonly');
                                $("#service_and_billing_address_billing_zipcode").val($('#service_and_billing_address_service_zipcode').val()).attr('readonly', 'readonly');
                                $("#service_and_billing_address_billing_city").val($('#service_and_billing_address_service_city').val()).attr('readonly', 'readonly');
                                $("#service_and_billing_address_billing_state").val($('#service_and_billing_address_service_state').val()).attr('readonly', 'readonly');
                                $("#service_and_billing_address_billing_country").val($('#service_and_billing_address_service_country').val()).attr('readonly', 'readonly');
                                $("#service_and_billing_address_billing_latitude").val($('#service_and_billing_address_service_latitude').val()).attr('readonly', 'readonly');
                                $("#service_and_billing_address_billing_longitude").val($('#service_and_billing_address_service_longitude').val()).attr('readonly', 'readonly');
                            } else {
                                $("#service_and_billing_address_billing_unit").val('');
                                $("#service_and_billing_address_billing_unit").removeAttr('readonly');

                                $("#service_and_billing_address_billing_address_1").val('');
                                $("#service_and_billing_address_billing_address_1").removeAttr('readonly');

                                $("#service_and_billing_address_billing_address_2").val('');
                                $("#service_and_billing_address_billing_address_2").removeAttr('readonly');

                                $("#service_and_billing_address_billing_city").val('');
                                $("#service_and_billing_address_billing_city").val('').removeAttr('readonly');

                                $("#service_and_billing_address_billing_zipcode").val('');
                                $("#service_and_billing_address_billing_zipcode").val('').removeAttr('readonly');

                                $("#service_and_billing_address_billing_state").val('');
                                $("#service_and_billing_address_billing_state").removeAttr('readonly');

                                $("#service_and_billing_address_billing_country").val('');
                                $("#service_and_billing_address_billing_country").removeAttr('readonly');
                            }

                        }
                    </script>
                    {{--<script>

                        function changeVal(sourceElement, destElement) {
                            if ($('input[name="is_service_address_same_as_billing_address"]:checked').val() == "yes") {
                                $("#" + destElement).val($("#" + sourceElement).val());
                            }
                        }
                    </script>--}}
                    <div class="col-sm-12">
                        <span class="bill-address-title">Is the billing address same as service address?</span>
                        &nbsp;

                        <div class="form-group radio-btns pdt0">
                            <label class="radio-inline">
                                <input type="radio"
                                       name="is_service_address_same_as_billing_address"
                                       onclick='copy_address_("yes")'
                                       value="yes">
                                Yes
                            </label>
                            <label class="radio-inline">
                                <input type="radio"
                                       name="is_service_address_same_as_billing_address"
                                       onclick='copy_address_("no")'
                                       value="no">
                                No
                            </label>
                        </div>

                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Billing Address</label>
                        </div>
                    </div>

                    <div class="col-sm-12 address-field">
                        <input autocomplete="off"
                               class="form-control autocompletestreet address {{ array_get($field, 'is_required' )==1 ? 'required' : '' }}"
                               Placeholder="Address Line 1"
                               onfocus="this.setAttribute('autocomplete', 'new-password')"
                               id="service_and_billing_address_billing_address_1"
                               name="field[billing_address_1]"
                               value="{{ isset($values['billing_address_1']) && !empty($values['billing_address_1']) ? $values['billing_address_1'] : '' }}"
                               data-parsley-trigger="focusout"
                               @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                               data-parsley-required-message="Please enter billing address" @endif>
                    </div>
                    <div class="col-sm-8 address-field">
                        <input autocomplete="new-password"
                               class="form-control autocompletestreet address"
                               Placeholder="Address Line 2"
                               id="service_and_billing_address_billing_address_2"
                               name="field[billing_address_2]"
                               value="{{ isset($values['billing_address_2']) && !empty($values['billing_address_2']) ? $values['billing_address_2'] : '' }}">
                    </div>
                    <div class="col-sm-4 address-field">
                        <input autocomplete="new-password"
                               class="form-control autocompletestreet address"
                               Placeholder="Unit Number"
                               id="service_and_billing_address_billing_unit"
                               name="field[billing_unit]"
                               value="{{ isset($values['billing_unit']) && !empty($values['billing_unit']) ? $values['billing_unit'] : '' }}">
                    </div>

                    <div class="col-sm-3 address-field inline-block">
                        <input autocomplete="new-password"
                               class="form-control"
                               placeholder="City"
                               id="service_and_billing_address_billing_city"
                               name="field[billing_city]"
                               value="{{ isset($values['billing_city']) && !empty($values['billing_city']) ? $values['billing_city'] : '' }}">
                    </div>

                    <div class="col-sm-3 address-field inline-block">
                        <input id="service_and_billing_address_billing_state"
                               class="form-control stateall statefield address"
                               Placeholder="State"
                               autocomplete="new-password"
                               name="field[billing_state]"
                               value="{{ isset($values['billing_state']) && !empty($values['billing_state']) ? $values['billing_state'] : '' }}">
                    </div>

                    <div class="col-sm-3 address-field inline-block">
                        <input class="form-control"
                               name="field[billing_zipcode]"
                               id="service_and_billing_address_billing_zipcode"
                               autocomplete="new-password"
                               data-parsley-trigger="focusout"
                               data-parsley-pattern="[0-9]{5}"
                               data-parsley-pattern-message="Please enter 5 digit zipcode"
                               Placeholder="Zipcode"
                               value="{{ isset($values['billing_zipcode']) && !empty($values['billing_zipcode']) ? $values['billing_zipcode'] : '' }}">
                    </div>

                    <div class="col-sm-3 address-field inline-block">
                        <input id="service_and_billing_address_billing_country"
                               class="form-control stateall statefield address "
                               Placeholder="Country"
                               autocomplete="new-password"
                               name="field[billing_country]"
                               value="{{ isset($values['billing_country']) && !empty($values['billing_country']) ? $values['billing_country'] : '' }}">
                    </div>
                    <input type="hidden"
                           name="field[billing_lat]"
                           id="service_and_billing_address_billing_latitude"
                           value="{{ isset($values['billing_lat']) && !empty($values['billing_lat']) ? $values['billing_lat'] : '' }}">
                    <input type="hidden"
                           name="field[billing_lng]"
                           id="service_and_billing_address_billing_longitude"
                           value="{{ isset($values['billing_lng']) && !empty($values['billing_lng']) ? $values['billing_lng'] : '' }}">

                </div>

                <script>
                    var input = document.getElementById('service_and_billing_address_billing_address_1');
                    var autocompleteBilling = new google.maps.places.Autocomplete(input, {
                        types: [],
                        componentRestrictions: {country: "us"}
                    });
                    google.maps.event.addListener(autocompleteBilling, 'place_changed', function () {
                        var place = autocompleteBilling.getPlace();
                        console.log(place);
                        $('#service_and_billing_address_billing_latitude').val(place.geometry.location.lat());
                        $('#service_and_billing_address_billing_longitude').val(place.geometry.location.lng());
                        $('#service_and_billing_address_billing_address_1').val(place.name);
                        var address2 = '';
                        for (var i = 0; i < place.address_components.length; i++) {
                            var addressType = place.address_components[i].types[0];
                            if (addressType === 'postal_code') {
                                $('#service_and_billing_address_billing_zipcode').val(place.address_components[i].long_name);
                            }
                            if (addressType === "neighborhood" || addressType === "sublocality_level_1") {
                                address2 += place.address_components[i].long_name;
                                address2 += ' '
                            }
                            if (addressType === "locality" || addressType === "administrative_area_level_2") {
                                $('#service_and_billing_address_billing_city').val(place.address_components[i].long_name);
                            }
                            if (addressType === "administrative_area_level_1") {
                                $('#service_and_billing_address_billing_state').val(place.address_components[i].long_name);
                            }
                            if (addressType === "country") {
                                $('#service_and_billing_address_billing_country').val(place.address_components[i].long_name);
                            }
                        }
                        $('#service_and_billing_address_billing_address_2').val(address2);
                    });
                </script>
            </div>

        @elseif ($field->type == 'phone_number')
            <div rel="label">
                <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                <input type="text"
                       autocomplete="new-password"
                       class="form-control mobile"
                       name="field[value]"
                       value="{{ isset($values['value']) && !empty($values['value']) ? $values['value'] : '' }}"
                       placeholder="{{ $field->name }}"
                       data-parsley-trigger="focusout"
                       data-parsley-pattern="[0-9]{10}"
                       data-parsley-pattern-message="Please enter 10 digit mobile number"
                       @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                       data-parsley-required-message="Please enter phone number" @endif>
            </div>

        @elseif ($field->type == 'email')
            <div rel="label">
                <label class="control-label">{{ ucfirst(ucfirst(array_get($field, 'label'))) }}</label>
                <input type="email"
                       autocomplete="new-password"
                       class="form-control email {{ array_get($field, 'is_required' )==1 ? 'required' : '' }}"
                       name="field[value]"
                       value="{{ isset($values['value']) && !empty($values['value']) ? $values['value'] : '' }}"
                       placeholder="{{ $field->name }}"
                       data-parsley-trigger="focusout"
                       data-parsley-pattern="/\S+@\S+\.\S+/"
                       data-parsley-pattern-message="Please enter valid email address"
                       @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                       data-parsley-required-message="Please enter email" @endif>
            </div>

        @elseif($field->type == 'selectbox')
            <div rel="label">
                <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                <select name="field[value]"
                        class="form-control"
                        title="Please enter {{strtolower(array_get($field, 'label'))}}"
                        data-parsley-trigger="focusout"
                        @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                        data-parsley-required-message="Please enter {{strtolower(array_get($field, 'label'))}}" @endif>
                    @foreach($field->meta as $mVal)
                        @foreach($mVal as $option)
                            <option value="{{$option['option']}}" {{ isset($values['value']) && !empty($values['value']) ? $values['value'] == $option['option'] ? 'checked' : '' : '' }}>{{ $option['option'] }}</option>
                        @endforeach
                    @endforeach
                </select>
            </div>
        @endif

        <br>

        <div class="text-center">
            <button type="submit"
                    class="btn btn-green submitBtn">Save
            </button>
            <button class="btn cancelBtn">Cancel</button>
        </div>

    </form>
</div>

<script>
    $('#editQuestionField').on('submit', function (e) {
        e.preventDefault();
        var data = $("#editQuestionField").serializeArray();

        $.ajax({
            type: 'post',
            url: "{{ route('save.field_question') }}",
            data: data,
            success: function (res) {
                if (res.status === true) {
                    $('#edit_question_wrapper').html('<div class="alert alert-success">Question successfully updated</div>')
                } else {
                    $('#edit_question_wrapper').html('<div class="alert alert-danger">' + res.message + '</div>');
                }
            },
            error: function () {
                $('#edit_question_wrapper').html('<div class="alert alert-danger">Whoops, something went wrong please try again</div>');
            }
        });
    })

    $('.cancelBtn').on('click', function () {
        $('#edit_field_container').html('')
    })
</script>
