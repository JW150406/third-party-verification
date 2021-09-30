
<div class="modal-dialog" role="document">
    <div class="modal-content modal-md lead-modal-height">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
            <h4 class="modal-title" id="exampleModalLabel">Form @if(isset($view) && $view) View @else Review @endif</h4>
        </div>

        <div class="modal-body">

         <!-- Nav tabs -->
         <ul class="lead-web-mobile-view nav nav-tabs " role="tablist" id="myTab">
            <li role="presentation" class="active"><a href="#WebView" aria-controls="web" role="tab" data-toggle="tab" aria-expanded="true">Web View</a></li>
              <li role="presentation"><a href="#MobileView" aria-controls="mobile" role="tab" data-toggle="tab" aria-expanded="false">Mobile View</a></li>
        </ul>
        <!-- Tab panes -->

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active" id="WebView">
            <div class="preview-modal-scroll scrollbar-inner">
                <form method="post" id="leadForm" role="form"
                      action=""
                      autocomplete="on" data-parsley-validate>
                    @csrf
                    @if(isset($fields) && !empty($fields))
                        <?php $i = 0; 
                        $mobiles = [];
                        $separator = 0;
                        ?>
                        @foreach($fields as $field)
                            <?php
                            if($field['type'] == 'separator') {                                
                                $separator++;
                            }
                            else {
                                $mobiles[$separator][] = $field;
                            }
                            
                            ?>
                            <div class="form-group fg30">
                                @if($field['type'] == 'fullname')
                                    <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <input class="form-control"
                                                   type="text"
                                                   name="fields[{{$i}}][value][first_name]"
                                                   placeholder="First Name"
                                                   autocomplete="new-password"
                                                   data-parsley-trigger="focusout"
                                                   @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                                   data-parsley-required-message="Please enter first name"
                                                   @endif
                                                   value="">
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="text"
                                                   class="form-control"
                                                   name="fields[{{$i}}][value][middle_initial]"
                                                   placeholder="Middle Name"
                                                   autocomplete="new-password"
                                                   value="">
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="text"
                                                   class="form-control"
                                                   name="fields[{{$i}}][value][last_name]"
                                                   value=""
                                                   placeholder="Last Name"
                                                   autocomplete="new-password"
                                                   data-parsley-trigger="focusout"
                                                   @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                                   data-parsley-required-message="Please enter last name" @endif>
                                        </div>
                                    </div>


                                @elseif($field['type'] == 'separator')
                                    <br>
                                    <hr class="separator-hr">
                                    <br>

                                @elseif($field['type'] == 'textbox')
                                    <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                                    <input type="text"
                                           autocomplete="off"
                                           class="form-control"
                                           name="fields[{{$i}}][value][value]"
                                           placeholder="{{ $field['meta']['placeholder']  }}"
                                           data-parsley-trigger="focusout"
                                           @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                           data-parsley-required-message="Please enter {{strtolower(array_get($field, 'label'))}}"
                                           @endif
                                           @if(!empty($field->regex))
                                           data-parsley-pattern="{{$field->regex}}"
                                           @endif
                                           @if(!empty($field->regex_message))
                                           data-parsley-pattern-message="{{$field->regex_message}}" @endif>

                                @elseif($field['type'] == 'textarea')
                                    <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                                    <textarea
                                            class="form-control"
                                            name="fields[{{$i}}][value][value]"
                                            placeholder="{{ $field['label'] }}"
                                            data-parsley-trigger="focusout"
                                            @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                            data-parsley-required-message="Please enter {{strtolower(array_get($field, 'label'))}}"
                                            @endif
                                            @if(!empty($field->regex))
                                            data-parsley-pattern="{{$field->regex}}"
                                            @endif
                                            @if(!empty($field->regex_message))
                                            data-parsley-pattern-message="{{$field->regex_message}}" @endif
                                                                                ></textarea>


                                @elseif($field['type'] == 'radio')
                                    <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                                    @if (isset($field['meta']['options']) && !empty($field['meta']['options']))
                                    @foreach($field['meta']['options'] as $option)


                                        <div class="form-group radio-btns pdt0">
                                            <label class="radio-inline">
                                                <input type="radio"
                                                       name="fields[{{$i}}][value][value]"
                                                       data-parsley-trigger="focusout"
                                                       @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                                       data-parsley-required-message="Please enter {{strtolower(array_get($field, 'label'))}}"
                                                       @endif
                                                       value="{{ $option }}"> {{ $option }}
                                            </label>

                                        </div>




                                        <div id="radio_button_{{array_get($field, 'id')}}"></div>
                                    @endforeach
                                    @else
                                        <div class="form-group"><p>No options available for {{ ucfirst(array_get($field, 'label')) }} field</p></div>
                                    @endif

                                @elseif($field['type'] == 'checkbox')
                                    <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                                    @if (isset($field['meta']['options']) && !empty($field['meta']['options']))
                                    @foreach($field['meta']['options'] as $option)

                                            <div class="form-group checkbx">
                                                <label class="checkbx-style">
                                                    <input type="checkbox"
                                                           class="{{ array_get($field, 'is_required' )==1 ? 'required' : '' }}"
                                                           min="1"
                                                           name="fields[{{$i}}][value][]"
                                                           value="{{ $option }}"
                                                           data-parsley-trigger="focusout"
                                                           @if(array_get($field, 'is_required') == 1) data-parsley-mincheck="1" @endif> {{ $option }}
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>

                                        <div id="checkbox_error_{{array_get($field, 'id')}}"></div>
                                    @endforeach
                                    @else
                                        <div class="form-group"><p>No options available for {{ ucfirst(array_get($field, 'label')) }} field</p></div>
                                    @endif

                                @elseif($field['type'] == 'address')
                                    <div class="form-group" rel="address">
                                        <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                                        <div class="row">
                                            <div class="col-sm-12 address-field">
                                                <input autocomplete="off"
                                                       id="address_line_1_{{$i}}"
                                                       onfocus="this.setAttribute('autocomplete', 'new-password')"
                                                       class="form-control autocompletestreet address"
                                                       Placeholder="Address line 1"
                                                       name="fields[{{$i}}][value][address_1]"
                                                       data-parsley-trigger="focusout"
                                                       @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                                       data-parsley-required-message="Please enter address" @endif>
                                            </div>
                                            <div class="col-sm-12 address-field">
                                                <input autocomplete="new-password"
                                                       id="address_line_2_{{$i}}"
                                                       class="form-control autocompletestreet address"
                                                       Placeholder="Apartment, suite, unit, building, floor, etc. (optional)"
                                                       name="fields[{{$i}}][value][address_2]">
                                            </div>
                                            <!-- <div class="col-sm-4 address-field">
                                                <input autocomplete="new-password"
                                                       id="address_unit_{{$i}}"
                                                       class="form-control autocompletestreet address"
                                                       Placeholder="Unit Number"
                                                       name="fields[{{$i}}][value][unit]">
                                            </div> -->
                                            <div class="col-sm-3 address-field inline-block">
                                                <input autocomplete="new-password"
                                                       id="address_city_{{$i}}"
                                                       class="form-control"
                                                       placeholder="City"
                                                       name="fields[{{$i}}][value][city]">
                                            </div>
                                            <!-- Div tag for address county -->
                                            <div class="col-sm-3 address-field inline-block">
                                                <input autocomplete="new-password"
                                                       id="address_county_{{$i}}"
                                                       class="form-control"
                                                       placeholder="County"
                                                       name="fields[{{$i}}][value][county]">
                                            </div>
                                            <!-- End -->
                                            <div class="col-sm-3 address-field inline-block">
                                                <input id="address_state_{{$i}}"
                                                       class="form-control stateall statefield address"
                                                       Placeholder="State"
                                                       autocomplete="new-password"
                                                       name="fields[{{$i}}][value][state]">
                                            </div>
                                            <div class="col-sm-3 address-field inline-block">
                                                <input class="form-control"
                                                       id="address_zipcode_{{$i}}"
                                                       name="fields[{{$i}}][value][zipcode]"
                                                       autocomplete="new-password"
                                                       Placeholder="Zipcode"
                                                       data-parsley-trigger="focusout"
                                                       data-parsley-pattern="[0-9]{5}"
                                                       data-parsley-pattern-message="Please enter 5 digit zipcode">
                                            </div>
                                            <div class="col-sm-3 address-field inline-block">
                                                <input id="address_country_{{$i}}" style="display: none;"
                                                       class="form-control stateall statefield address"
                                                       Placeholder="Country"
                                                       autocomplete="new-password"
                                                       name="fields[{{$i}}][value][country]">
                                            </div>
                                            <input type="hidden"
                                                   name="fields[{{$i}}][value][lat]"
                                                   id="address_latitude_{{$i}}">
                                            <input type="hidden"
                                                   name="fields[{{$i}}][value][lng]"
                                                   id="address_longitude_{{$i}}">

                                            <script>
                                                var input = document.getElementById('address_line_1_{{$i}}');
                                                var autocomplete{{$i}} = new google.maps.places.Autocomplete(input, {
                                                    types: [],
                                                    componentRestrictions: {country: "us"}
                                                });
                                                google.maps.event.addListener(autocomplete{{$i}}, 'place_changed', function () {
                                                    var place = autocomplete{{$i}}.getPlace();
                                                    $('#address_latitude_{{$i}}').val(place.geometry.location.lat());
                                                    $('#address_longitude_{{$i}}').val(place.geometry.location.lng());
                                                    $('#address_line_1_{{$i}}').val(place.name);
                                                    var address2 = '';
                                                    for (var i = 0; i < place.address_components.length; i++) {
                                                        var addressType = place.address_components[i].types[0];
                                                        if (addressType === 'postal_code') {
                                                            $('#address_zipcode_{{$i}}').val(place.address_components[i].long_name);
                                                        }
                                                        if (addressType === "neighborhood" || addressType === "sublocality_level_1") {
                                                            address2 += place.address_components[i].long_name;
                                                            address2 += ' '
                                                        }
                                                        if (addressType === "locality" || addressType === "administrative_area_level_2") {
                                                            $('#address_city_{{$i}}').val(place.address_components[i].long_name);
                                                        }
                                                        // For address county
                                                        if (addressType === "administrative_area_level_2") {
                                                            $('#address_county_{{$i}}').val(place.address_components[i].long_name);
                                                        }
                                                        // End
                                                        if (addressType === "administrative_area_level_1") {
                                                            $('#address_state_{{$i}}').val(place.address_components[i].long_name);
                                                        }
                                                        if (addressType === "country") {
                                                            $('#address_country_{{$i}}').val(place.address_components[i].long_name);
                                                        }
                                                    }
                                                    $('#address_line_2_{{$i}}').val(address2);
                                                })
                                            </script>

                                        </div>
                                    </div>
                                @elseif($field['type'] == 'service_and_billing_address')
                                    <div class="form-group"
                                         rel="service_and_billing_address">
                                        <label class="control-label title-lable">{{ getLabel(ucfirst(array_get($field, 'label'))) }}</label>
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
                                                       id="service_and_billing_address_service_address_1_{{$i}}"
                                                       name="fields[{{$i}}][value][service_address_1]"
                                                       @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                                       data-parsley-trigger="focusout"
                                                       data-parsley-required-message="Please enter service address"
                                                       @endif
                                                       onkeyup="changeVal('service_and_billing_address_service_address_1_', 'service_and_billing_address_billing_address_1_', '{{$i}}');">
                                            </div>
                                            <div class="col-sm-12 address-field">
                                                <input autocomplete="new-password"
                                                       class="form-control autocompletestreet address"
                                                       Placeholder="Apartment, suite, unit, building, floor, etc. (optional)"
                                                       id="service_and_billing_address_service_address_2_{{$i}}"
                                                       name="fields[{{$i}}][value][service_address_2]"
                                                       onkeyup="changeVal('service_and_billing_address_service_address_2_', 'service_and_billing_address_billing_address_2_', '{{$i}}');">
                                            </div>
                                            <!-- <div class="col-sm-4 address-field">
                                                <input autocomplete="new-password"
                                                       class="form-control autocompletestreet address"
                                                       Placeholder="Unit Number"
                                                       id="service_and_billing_address_service_unit_{{$i}}"
                                                       name="fields[{{$i}}][value][service_unit]"
                                                       onkeyup="changeVal('service_and_billing_address_service_unit_', 'service_and_billing_address_billing_unit_', '{{$i}}');">
                                            </div> -->
                                            <div class="col-sm-3 address-field inline-block">
                                                <input autocomplete="new-password"
                                                       class="form-control"
                                                       placeholder="City"
                                                       id="service_and_billing_address_service_city_{{$i}}"
                                                       name="fields[{{$i}}][value][service_city]"
                                                       onkeyup="changeVal('service_and_billing_address_service_city_', 'service_and_billing_address_billing_city_', '{{$i}}');">
                                            </div>
                                            <!-- Div tag for service_county -->
                                            <div class="col-sm-3 address-field inline-block">
                                                <input autocomplete="new-password"
                                                       class="form-control"
                                                       placeholder="County"
                                                       id="service_and_billing_address_service_county_{{$i}}"
                                                       name="fields[{{$i}}][value][service_county]"
                                                       onkeyup="changeVal('service_and_billing_address_service_county_', 'service_and_billing_address_billing_county_', '{{$i}}');">
                                            </div>
                                            <!-- End -->
                                            <div class="col-sm-3 address-field inline-block">
                                                <input id="service_and_billing_address_service_state_{{$i}}"
                                                       class="form-control stateall statefield address "
                                                       Placeholder="State"
                                                       autocomplete="new-password"
                                                       name="fields[{{$i}}][value][service_state]"
                                                       onkeyup="changeVal('service_and_billing_address_service_state_', 'service_and_billing_address_billing_state_', '{{$i}}');">
                                            </div>
                                            <div class="col-sm-3 address-field inline-block">
                                                <input class="form-control"
                                                       id="service_and_billing_address_service_zipcode_{{$i}}"
                                                       name="fields[{{$i}}][value][service_zipcode]"
                                                       autocomplete="new-password"
                                                       data-parsley-trigger="focusout"
                                                       data-parsley-pattern="[0-9]{5}"
                                                       data-parsley-pattern-message="Please enter 5 digit zipcode"
                                                       Placeholder="Zipcode"
                                                       onkeyup="changeVal('service_and_billing_address_service_zipcode_', 'service_and_billing_address_billing_zipcode_', '{{$i}}');">
                                            </div>
                                            <div class="col-sm-3 address-field inline-block">
                                                <input id="service_and_billing_address_service_country_{{$i}}" style="display: none;"
                                                       class="form-control stateall statefield address "
                                                       Placeholder="Country"
                                                       autocomplete="new-password"
                                                       name="fields[{{$i}}][value][service_country]"
                                                       onkeyup="changeVal('service_and_billing_address_service_country_', 'service_and_billing_address_billing_country_', '{{$i}}');">
                                            </div>
                                            <input type="hidden"
                                                   name="fields[{{$i}}][value][service_lat]"
                                                   id="service_and_billing_address_service_latitude_{{$i}}">
                                            <input type="hidden"
                                                   name="fields[{{$i}}][value][service_lng]"
                                                   id="service_and_billing_address_service_longitude_{{$i}}">
                                            <script>

                                                var input = document.getElementById('service_and_billing_address_service_address_1_{{$i}}');
                                                var autocompleteService{{$i}} = new google.maps.places.Autocomplete(input, {
                                                    types: [],
                                                    componentRestrictions: {country: "us"}
                                                });
                                                google.maps.event.addListener(autocompleteService{{$i}}, 'place_changed', function () {
                                                    var place = autocompleteService{{$i}}.getPlace();
                                                    $('#service_and_billing_address_service_latitude_{{$i}}').val(place.geometry.location.lat());
                                                    $('#service_and_billing_address_service_longitude_{{$i}}').val(place.geometry.location.lng());
                                                    $('#service_and_billing_address_service_address_1_{{$i}}').val(place.name);
                                                    var address2 = '';
                                                    for (var i = 0; i < place.address_components.length; i++) {
                                                        var addressType = place.address_components[i].types[0];
                                                        if (addressType === 'postal_code') {
                                                            $('#service_and_billing_address_service_zipcode_{{$i}}').val(place.address_components[i].long_name);
                                                        }
                                                        if (addressType === "neighborhood" || addressType === "sublocality_level_1") {
                                                            address2 += place.address_components[i].long_name;
                                                            address2 += ' '
                                                        }
                                                        if (addressType === "locality" || addressType === "administrative_area_level_2") {
                                                            $('#service_and_billing_address_service_city_{{$i}}').val(place.address_components[i].long_name);
                                                        }
                                                        // For service_county
                                                        if (addressType === "administrative_area_level_2") {
                                                            $('#service_and_billing_address_service_county_{{$i}}').val(place.address_components[i].long_name);
                                                        }
                                                        // End
                                                        if (addressType === "administrative_area_level_1") {
                                                            $('#service_and_billing_address_service_state_{{$i}}').val(place.address_components[i].long_name);
                                                        }
                                                        if (addressType === "country") {
                                                            $('#service_and_billing_address_service_country_{{$i}}').val(place.address_components[i].long_name);
                                                        }
                                                    }
                                                    if ($('input[name="is_service_address_same_as_billing_address_{{$i}}"]:checked').val() == "yes") {
                                                        console.log("ues");
                                                        copy_address_("{{$i}}", 'yes')
                                                    }
                                                })
                                            </script>
                                            <script>
                                                function copy_address_($i, state) {
                                                    if (state == "yes") {
                                                        serviceAndBillingElements.push(parseInt($i));
                                                        $("#service_and_billing_address_billing_unit_" + $i).val($('#service_and_billing_address_service_unit_' + $i).val()).attr('readonly', 'readonly');
                                                        $("#service_and_billing_address_billing_address_1_" + $i).val($('#service_and_billing_address_service_address_1_' + $i).val()).attr('readonly', 'readonly');
                                                        $("#service_and_billing_address_billing_address_2_" + $i).val($('#service_and_billing_address_service_address_2_' + $i).val()).attr('readonly', 'readonly');
                                                        $("#service_and_billing_address_billing_zipcode_" + $i).val($('#service_and_billing_address_service_zipcode_' + $i).val()).attr('readonly', 'readonly');
                                                        $("#service_and_billing_address_billing_city_" + $i).val($('#service_and_billing_address_service_city_' + $i).val()).attr('readonly', 'readonly');
                                                        // Code for copy county for service and billing address county 
                                                        $("#service_and_billing_address_billing_county_" + $i).val($('#service_and_billing_address_service_county_' + $i).val()).attr('readonly', 'readonly');
                                                        // End
                                                        $("#service_and_billing_address_billing_state_" + $i).val($('#service_and_billing_address_service_state_' + $i).val()).attr('readonly', 'readonly');
                                                        $("#service_and_billing_address_billing_country_" + $i).val($('#service_and_billing_address_service_country_' + $i).val()).attr('readonly', 'readonly');
                                                        $("#service_and_billing_address_billing_latitude_" + $i).val($('#service_and_billing_address_service_latitude_' + $i).val()).attr('readonly', 'readonly');
                                                        $("#service_and_billing_address_billing_longitude_" + $i).val($('#service_and_billing_address_service_longitude_' + $i).val()).attr('readonly', 'readonly');
                                                    } else {
                                                        $("#service_and_billing_address_billing_unit_" + $i).val('');
                                                        $("#service_and_billing_address_billing_unit_" + $i).removeAttr('readonly');

                                                        $("#service_and_billing_address_billing_address_1_" + $i).val('');
                                                        $("#service_and_billing_address_billing_address_1_" + $i).removeAttr('readonly');

                                                        $("#service_and_billing_address_billing_address_2_" + $i).val('');
                                                        $("#service_and_billing_address_billing_address_2_" + $i).removeAttr('readonly');

                                                        $("#service_and_billing_address_billing_city_" + $i).val('');
                                                        $("#service_and_billing_address_billing_city_" + $i).val('').removeAttr('readonly');

                                                        // Code for remove value of county for service and billing address county 
                                                        $("#service_and_billing_address_billing_county_" + $i).val('');
                                                        $("#service_and_billing_address_billing_county_" + $i).val('').removeAttr('readonly');
                                                        // End

                                                        $("#service_and_billing_address_billing_zipcode_" + $i).val('');
                                                        $("#service_and_billing_address_billing_zipcode_" + $i).val('').removeAttr('readonly');

                                                        $("#service_and_billing_address_billing_state_" + $i).val('');
                                                        $("#service_and_billing_address_billing_state_" + $i).removeAttr('readonly');

                                                        $("#service_and_billing_address_billing_country_" + $i).val('');
                                                        $("#service_and_billing_address_billing_country_" + $i).removeAttr('readonly');
                                                        serviceAndBillingElements.pop(parseInt($i));
                                                    }

                                                }
                                            </script>
                                            <div class="col-sm-12">
                                                <span class="bill-address-title">Is the billing address same as service address?</span>
                                                &nbsp;

                                                <div class="form-group radio-btns pdt0">
                                                    <label class="radio-inline">
                                                        <input type="radio"
                                                               name="is_service_address_same_as_billing_address_{{$i}}"
                                                               onclick='copy_address_("{{$i}}", "yes")'
                                                               value="yes">
                                                        Yes
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio"
                                                               name="is_service_address_same_as_billing_address_{{$i}}"
                                                               onclick='copy_address_("{{$i}}", "no")'
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
                                                       id="service_and_billing_address_billing_address_1_{{$i}}"
                                                       name="fields[{{$i}}][value][billing_address_1]"
                                                       data-parsley-trigger="focusout"
                                                       @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                                       data-parsley-required-message="Please enter billing address" @endif>
                                            </div>
                                            <div class="col-sm-12 address-field">
                                                <input autocomplete="new-password"
                                                       class="form-control autocompletestreet address"
                                                       Placeholder="Apartment, suite, unit, building, floor, etc. (optional)"
                                                       id="service_and_billing_address_billing_address_2_{{$i}}"
                                                       name="fields[{{$i}}][value][billing_address_2]">
                                            </div>
                                            <!-- <div class="col-sm-4 address-field">
                                                <input autocomplete="new-password"
                                                       class="form-control autocompletestreet address"
                                                       Placeholder="Unit Number"
                                                       id="service_and_billing_address_billing_unit_{{$i}}"
                                                       name="fields[{{$i}}][value][billing_unit]">
                                            </div> -->

                                            <div class="col-sm-3 address-field inline-block">
                                                <input autocomplete="new-password"
                                                       class="form-control"
                                                       placeholder="City"
                                                       id="service_and_billing_address_billing_city_{{$i}}"
                                                       name="fields[{{$i}}][value][billing_city]">
                                            </div>

                                            
                                            <div class="col-sm-3 address-field inline-block">
                                                <input autocomplete="new-password"
                                                       class="form-control"
                                                       placeholder="County"
                                                       id="service_and_billing_address_billing_county_{{$i}}"
                                                       name="fields[{{$i}}][value][billing_county]">
                                            </div>
                                            

                                            <div class="col-sm-3 address-field inline-block">
                                                <input id="service_and_billing_address_billing_state_{{$i}}"
                                                       class="form-control stateall statefield address"
                                                       Placeholder="State"
                                                       autocomplete="new-password"
                                                       name="fields[{{$i}}][value][billing_state]">
                                            </div>

                                            <div class="col-sm-3 address-field inline-block">
                                                <input class="form-control"
                                                       name="fields[{{$i}}][value][billing_zipcode]"
                                                       id="service_and_billing_address_billing_zipcode_{{$i}}"
                                                       autocomplete="new-password"
                                                       data-parsley-trigger="focusout"
                                                       data-parsley-pattern="[0-9]{5}"
                                                       data-parsley-pattern-message="Please enter 5 digit zipcode"
                                                       Placeholder="Zipcode">
                                            </div>

                                            <div class="col-sm-3 address-field inline-block">
                                                <input id="service_and_billing_address_billing_country_{{$i}}" style="display: none;"
                                                       class="form-control stateall statefield address "
                                                       Placeholder="Country"
                                                       autocomplete="new-password"
                                                       name="fields[{{$i}}][value][billing_country]">
                                            </div>
                                            <input type="hidden"
                                                   name="fields[{{$i}}][value][billing_lat]"
                                                   id="service_and_billing_address_billing_latitude_{{$i}}">
                                            <input type="hidden"
                                                   name="fields[{{$i}}][value][billing_lng]"
                                                   id="service_and_billing_address_billing_longitude_{{$i}}">

                                        </div>

                                        <script>
                                            var input = document.getElementById('service_and_billing_address_billing_address_1_{{$i}}');
                                            var autocompleteBilling{{$i}} = new google.maps.places.Autocomplete(input, {
                                                types: [],
                                                componentRestrictions: {country: "us"}
                                            });
                                            google.maps.event.addListener(autocompleteBilling{{$i}}, 'place_changed', function () {
                                                var place = autocompleteBilling{{$i}}.getPlace();
                                                console.log(place);
                                                $('#service_and_billing_address_billing_latitude_{{$i}}').val(place.geometry.location.lat());
                                                $('#service_and_billing_address_billing_longitude_{{$i}}').val(place.geometry.location.lng());
                                                $('#service_and_billing_address_billing_address_1_{{$i}}').val(place.name);
                                                var address2 = '';
                                                for (var i = 0; i < place.address_components.length; i++) {
                                                    var addressType = place.address_components[i].types[0];
                                                    if (addressType === 'postal_code') {
                                                        $('#service_and_billing_address_billing_zipcode_{{$i}}').val(place.address_components[i].long_name);
                                                    }
                                                    if (addressType === "neighborhood" || addressType === "sublocality_level_1") {
                                                        address2 += place.address_components[i].long_name;
                                                        address2 += ' '
                                                    }
                                                    if (addressType === "locality" || addressType === "administrative_area_level_2") {
                                                        $('#service_and_billing_address_billing_city_{{$i}}').val(place.address_components[i].long_name);
                                                    }
                                                    // For billing_county
                                                    if (addressType === "administrative_area_level_2") {
                                                        $('#service_and_billing_address_billing_county_{{$i}}').val(place.address_components[i].long_name);
                                                    }
                                                    // End
                                                    if (addressType === "administrative_area_level_1") {
                                                        $('#service_and_billing_address_billing_state_{{$i}}').val(place.address_components[i].long_name);
                                                    }
                                                    if (addressType === "country") {
                                                        $('#service_and_billing_address_billing_country_{{$i}}').val(place.address_components[i].long_name);
                                                    }
                                                }
                                                $('#service_and_billing_address_billing_address_2_{{$i}}').val(address2);
                                            });
                                        </script>
                                    </div>


                                @elseif ($field['type'] == 'label')
                                    <div class="form-group" rel="label">
                                        <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                                    </div>

                                @elseif ($field['type'] == 'heading')
                                    <div class="form-group" rel="label">
                                        <h3>{{ ucfirst(array_get($field, 'label')) }}</h3>
                                    </div>

                                @elseif ($field['type'] == 'phone_number')
                                    <div class="form-group" rel="label">
                                        <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                                        <input type="text"
                                               autocomplete="new-password"
                                               class="form-control mobile"
                                               name="fields[{{$i}}][value][value]"
                                               placeholder="{{ $field['label'] }}"
                                               data-parsley-trigger="focusout"
                                               data-parsley-pattern="[0-9]{10}"
                                               data-parsley-pattern-message="Please enter 10 digit mobile number"
                                               @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                               data-parsley-required-message="Please enter phone number" @endif>
                                    </div>

                                @elseif ($field['type'] == 'email')
                                    <div class="form-group" rel="label">
                                        <label class="control-label">{{ ucfirst(ucfirst(array_get($field, 'label'))) }}</label>
                                        <input type="email"
                                               autocomplete="new-password"
                                               class="form-control email {{ array_get($field, 'is_required' )==1 ? 'required' : '' }}"
                                               name="fields[{{$i}}][value][value]"
                                               placeholder="{{ $field['label'] }}"
                                               data-parsley-trigger="focusout"
                                               data-parsley-pattern="/\S+@\S+\.\S+/"
                                               data-parsley-pattern-message="Please enter valid email address"
                                               @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                               data-parsley-required-message="Please enter email" @endif>
                                    </div>

                                @elseif($field['type'] == 'selectbox')
                                    <div class="form-group" rel="label">
                                        <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                                        @if (isset($field['meta']['options']) && !empty($field['meta']['options']))
                                            <select name="fields[{{$i}}][value][value]"
                                                class="select2 form-control"
                                                title="Please enter {{strtolower(array_get($field, 'label'))}}"
                                                data-parsley-trigger="focusout"
                                                @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                                data-parsley-required-message="Please enter {{strtolower(array_get($field, 'label'))}}" @endif>
                                                <option value="">Select</option>
                                            @foreach($field['meta']['options'] as $mVal)
                                                <option value="{{$mVal}}">{{ $mVal }}</option>
                                            @endforeach
                                            </select>
                                        @else
                                            <div class="form-group"><p>No options available for {{ ucfirst(array_get($field, 'label')) }} field</p></div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <?php $i++; ?>
                        @endforeach
                    @endif
                </form>
            </div>                  
            </div><!--end-web-view--->

            <div role="tabpanel" class="tab-pane fade" id="MobileView">
               <section class="mobilewizard">        
                    <div class="wizard">
                        @if(isset($mobiles) && !empty($mobiles))
                            <div class="tab-content">
                                <div class="tab-pane active" role="tabpanel" id="step1">
                                    <div class="preview-modal-scroll scrollbar-inner">
                                        <!--new--two--new-screen--->
                                        <div class="lead-zip-img text-center mb30">
                                           <?php echo getimage('/images/lead-zip.png') ?>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Enter Zip Code</label>
                                            <input type="text" autocomplete="new-password" class="form-control" placeholder="Zip Code">
                                        </div>
                                        @foreach($commodities as $commodity)
                                        <div class="form-group" rel="label">
                                            <label class="control-label">{{$commodity->name}} Utility</label>
                                            <select class="select2 form-control"title="Please select">
                                               <option value="" selected>one</option>
                                               <option value="">two</option>
                                               <option value="">three</option>
                                            </select>
                                        </div>
                                        @endforeach
                                    </div>
                                    <ul class="list-inline text-center">
                                        <li><button type="button" class="btn btn-green btn-block next-step">Next</button></li>
                                    </ul>
                                </div>
                                <div class="tab-pane" role="tabpanel" id="step2">
                                    <div class="preview-modal-scroll scrollbar-inner">
                                        <!---second--new--screen---->
                                        @foreach($commodities as $commodity)
                                        <label class="control-label title-lable">{{$commodity->name}} Utility</label>
                                        <ul class="mobile-programlist">
                                            <li>
                                                <div class="utility-outer">
                                                    <h5>Commercial</h5>
                                                    <p class="utility-sub-t">Blue 12 </p>
                                                    <div class="row">
                                                        <div class="col-md-3 col-sm-3 col-xs-4 br2">
                                                            <p>Code</p><span>20736</span>
                                                        </div>
                                                        <div class="col-md-3 col-sm-3 col-xs-4 text-center">
                                                            <p>Rate</p> <span>$0.599 (THM)</span>
                                                        </div>
                                                        <div class="col-md-2 col-sm-2 col-xs-4 text-center">
                                                            <p>Term</p><span>12</span>
                                                        </div>
                                                        <div class="col-md-2 col-sm-2 col-xs-4 text-center">
                                                            <p>MSF</p><span>$4.95</span>
                                                        </div>
                                                        <div class="col-md-2 col-sm-2 col-xs-4 text-center">
                                                            <p>ETF</p><span>$50</span>
                                                        </div>
                                                    </div>
                                                </div><input class="program-id" type="hidden" value="13">
                                            </li>
                                            <li>
                                                <div class="utility-outer">
                                                    <h5>Residential</h5>
                                                    <p class="utility-sub-t">Blue 12 </p>
                                                    <div class="row">
                                                        <div class="col-md-3 col-sm-3 col-xs-4 br2">
                                                            <p>Code</p><span>20736</span>
                                                        </div>
                                                        <div class="col-md-3 col-sm-3 col-xs-4 text-center">
                                                            <p>Rate</p> <span>$0.599 (THM)</span>
                                                        </div>
                                                        <div class="col-md-2 col-sm-2 col-xs-4 text-center">
                                                            <p>Term</p><span>12</span>
                                                        </div>
                                                        <div class="col-md-2 col-sm-2 col-xs-4 text-center">
                                                            <p>MSF</p><span>$4.95</span>
                                                        </div>
                                                        <div class="col-md-2 col-sm-2 col-xs-4 text-center">
                                                            <p>ETF</p><span>$50</span>
                                                        </div>
                                                    </div>
                                                </div><input class="program-id" type="hidden" value="13">
                                            </li>
                                        </ul>
                                        @endforeach
                                        <!--end--new--two--new-screen--->
                                    </div>
                                    <ul class="list-inline text-center">
                                        <li><button type="button" class="btn btn-green btn-block next-step">Next</button></li>
                                    </ul>
                                </div>
                                @foreach($mobiles as $key => $fields)
                                    <div class="tab-pane" role="tabpanel" id="step{{$key+3}}">
                                        <div class="preview-modal-scroll scrollbar-inner">
                                        @foreach($fields as $k => $field)
                                            @if($field['type'] == 'fullname')
                                            <label class="control-label title-lable">{{ ucfirst(array_get($field, 'label')) }}</label>
                                            <div class="form-group">
                                                <label class="control-label inner-lbl">First Name</label>
                                                <input type="text" autocomplete="new-password" class="form-control" placeholder="First Name">
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label inner-lbl">Middle Name</label>
                                                <input type="text" autocomplete="new-password" class="form-control" placeholder="Last Name">
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label inner-lbl">Last Name</label>
                                                <input type="text" autocomplete="new-password" class="form-control" placeholder="Last Name">
                                            </div>
                                            @elseif($field['type'] == 'textbox')
                                            <div class="form-group">
                                                <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                                                <input type="text"
                                                   autocomplete="off"
                                                   class="form-control"
                                                   name="fields[{{$key.$k}}][value][value]"
                                                   placeholder="{{ $field['meta']['placeholder'] }}">
                                            </div>
                                            @elseif($field['type'] == 'textarea')
                                            <div class="form-group">
                                                <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                                                <textarea
                                                    class="form-control"
                                                    name="fields[{{$key.$k}}][value][value]"
                                                    placeholder="{{ $field['label'] }}">
                                                    
                                                </textarea>
                                            </div>
                                            @elseif($field['type'] == 'radio')
                                            <div class="form-group">
                                                <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                                                @if (isset($field['meta']['options']) && !empty($field['meta']['options']))
                                                    @foreach($field['meta']['options'] as $option)
                                                        <div class="form-group radio-btns pdt0">
                                                            <label class="radio-inline">
                                                                <input type="radio"
                                                                       name="fields[{{$key.$k}}][value][value]"
                                                                       value="{{ $option }}"> {{ $option }}
                                                            </label>
                                                        </div>
                                                        <div id="radio_button_{{array_get($field, 'id')}}"></div>
                                                    @endforeach
                                                @else
                                                    <div class="form-group"><p>No options available for {{ ucfirst(array_get($field, 'label')) }} field</p></div>
                                                @endif
                                            </div>
                                            @elseif($field['type'] == 'checkbox')
                                            <div class="form-group">
                                                <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                                                @if (isset($field['meta']['options']) && !empty($field['meta']['options']))
                                                    @foreach($field['meta']['options'] as $option)
                                                    <div class="form-group checkbx">
                                                        <label class="checkbx-style">
                                                            <input type="checkbox"
                                                                   class="{{ array_get($field, 'is_required' )==1 ? 'required' : '' }}"
                                                                   min="1"
                                                                   name="fields[{{$i}}][value][]"
                                                                   value="{{ $option }}"
                                                                   data-parsley-trigger="focusout"
                                                                   @if(array_get($field, 'is_required') == 1) data-parsley-mincheck="1" @endif> {{ $option }}
                                                            <span class="checkmark"></span>
                                                        </label>
                                                    </div>
                                                    <div id="checkbox_error_{{array_get($field, 'id')}}"></div>
                                                    @endforeach
                                                @else
                                                    <div class="form-group"><p>No options available for {{ ucfirst(array_get($field, 'label')) }} field</p></div>
                                                @endif
                                            </div>
                                            @elseif($field['type'] == 'address')
                                                <div class="form-group" rel="{{$field['type']}}">
                                                    <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                                                    <div class="row">
                                                        <div class="col-sm-12 address-field">
                                                            <input autocomplete="off"
                                                                   id="m_address_line_1_{{$key.$k}}"
                                                                   onfocus="this.setAttribute('autocomplete', 'new-password')"
                                                                   class="form-control autocompletestreet address"
                                                                   Placeholder="Address line 1"
                                                                   name="fields[{{$key.$k}}][value][m_address_1]">
                                                        </div>
                                                        <div class="col-sm-12 address-field">
                                                            <input autocomplete="new-password"
                                                                   id="m_address_line_2_{{$key.$k}}"
                                                                   class="form-control autocompletestreet address"
                                                                   Placeholder="Apartment, suite, unit, building, floor, etc. (optional)"
                                                                   name="fields[{{$key.$k}}][value][m_address_2]">
                                                        </div>
                                                        <!--<div class="col-sm-4 address-field">
                                                            <input autocomplete="new-password"
                                                                   id="m_address_unit_{{$key.$k}}"
                                                                   class="form-control autocompletestreet address"
                                                                   Placeholder="Unit Number"
                                                                   name="fields[{{$key.$k}}][value][unit]">
                                                        </div> -->
                                                        <div class="col-sm-3 address-field inline-block">
                                                            <input autocomplete="new-password"
                                                                   id="m_address_city_{{$key.$k}}"
                                                                   class="form-control"
                                                                   placeholder="City"
                                                                   name="fields[{{$key.$k}}][value][city]">
                                                        </div>
                                                        <div class="col-sm-3 address-field inline-block">
                                                            <input autocomplete="new-password"
                                                                   id="m_address_county_{{$key.$k}}"
                                                                   class="form-control"
                                                                   placeholder="County"
                                                                   name="fields[{{$key.$k}}][value][county]">
                                                        </div>
                                                        <div class="col-sm-3 address-field inline-block">
                                                            <input id="m_address_state_{{$key.$k}}"
                                                                   class="form-control stateall statefield address"
                                                                   Placeholder="State"
                                                                   autocomplete="new-password"
                                                                   name="fields[{{$key.$k}}][value][state]">
                                                        </div>
                                                        <div class="col-sm-3 address-field inline-block">
                                                            <input class="form-control"
                                                                   id="m_address_zipcode_{{$key.$k}}"
                                                                   name="fields[{{$key.$k}}][value][zipcode]"
                                                                   autocomplete="new-password"
                                                                   Placeholder="Zipcode"
                                                                   data-parsley-trigger="focusout"
                                                                   data-parsley-pattern="[0-9]{5}"
                                                                   data-parsley-pattern-message="Please enter 5 digit zipcode">
                                                        </div>
                                                        <div class="col-sm-3 address-field inline-block">
                                                            <input id="m_address_country_{{$key.$k}}" style="display: none;"
                                                                   class="form-control stateall statefield address"
                                                                   Placeholder="Country"
                                                                   autocomplete="new-password"
                                                                   name="fields[{{$key.$k}}][value][country]">
                                                        </div>
                                                        <input type="hidden"
                                                               name="fields[{{$key.$k}}][value][lat]"
                                                               id="m_address_latitude_{{$key.$k}}">
                                                        <input type="hidden"
                                                               name="fields[{{$key.$k}}][value][lng]"
                                                               id="m_address_longitude_{{$key.$k}}">

                                                        <script>
                                                            var input = document.getElementById('m_address_line_1_{{$key.$k}}');
                                                            var autocomplete{{$key.$k}} = new google.maps.places.Autocomplete(input, {
                                                                types: [],
                                                                componentRestrictions: {country: "us"}
                                                            });
                                                            google.maps.event.addListener(autocomplete{{$key.$k}}, 'place_changed', function () {
                                                                var place = autocomplete{{$key.$k}}.getPlace();
                                                                $('#m_address_latitude_{{$key.$k}}').val(place.geometry.location.lat());
                                                                $('#m_address_longitude_{{$key.$k}}').val(place.geometry.location.lng());
                                                                $('#m_address_line_1_{{$key.$k}}').val(place.name);
                                                                var address2 = '';
                                                                for (var i = 0; i < place.address_components.length; i++) {
                                                                    var addressType = place.address_components[i].types[0];
                                                                    if (addressType === 'postal_code') {
                                                                        $('#m_address_zipcode_{{$key.$k}}').val(place.address_components[i].long_name);
                                                                    }
                                                                    if (addressType === "neighborhood" || addressType === "sublocality_level_1") {
                                                                        address2 += place.address_components[i].long_name;
                                                                        address2 += ' '
                                                                    }
                                                                    if (addressType === "locality" || addressType === "administrative_area_level_2") {
                                                                        $('#m_address_city_{{$key.$k}}').val(place.address_components[i].long_name);
                                                                    }
                                                                    // For address_county mobile view
                                                                    if (addressType === "administrative_area_level_2") {
                                                                        $('#m_address_county_{{$key.$k}}').val(place.address_components[i].long_name);
                                                                    }
                                                                    // End
                                                                    if (addressType === "administrative_area_level_1") {
                                                                        $('#m_address_state_{{$key.$k}}').val(place.address_components[i].long_name);
                                                                    }
                                                                    if (addressType === "country") {
                                                                        $('#m_address_country_{{$key.$k}}').val(place.address_components[i].long_name);
                                                                    }
                                                                }
                                                                $('#m_address_line_2_{{$key.$k}}').val(address2);
                                                            });
                                                        </script>

                                                    </div>
                                                </div>
                                            @elseif($field['type'] == 'service_and_billing_address')
                                                <div class="form-group"
                                                     rel="service_and_billing_address">
                                                    <label class="control-label title-lable">{{ getLabel(ucfirst(array_get($field, 'label'))) }}</label>
                                                    <div class="form-group mb0">
                                                        <label class="mt15 ">Service
                                                            Address</label>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-12 address-field">
                                                            <input class="form-control autocompletestreet address"
                                                                   Placeholder="Address Line 1"
                                                                   onfocus="this.setAttribute('autocomplete', 'new-password')"
                                                                   autocapitalize="none"
                                                                   spellcheck="false"
                                                                   id="service_and_billing_address_service_address_1_{{$key.$k}}"
                                                                   name="fields[{{$key.$k}}][value][service_address_1]"
                                                                   onkeyup="changeVal('service_and_billing_address_service_address_1_', 'service_and_billing_address_billing_address_1_', '{{$key.$k}}');">
                                                        </div>
                                                        <div class="col-sm-12 address-field">
                                                            <input autocomplete="new-password"
                                                                   class="form-control autocompletestreet address"
                                                                   Placeholder="Apartment, suite, unit, building, floor, etc. (optional)"
                                                                   id="service_and_billing_address_service_address_2_{{$key.$k}}"
                                                                   name="fields[{{$key.$k}}][value][service_address_2]"
                                                                   onkeyup="changeVal('service_and_billing_address_service_address_2_', 'service_and_billing_address_billing_address_2_', '{{$key.$k}}');">
                                                        </div>
                                                        <!-- <div class="col-sm-4 address-field">
                                                            <input autocomplete="new-password"
                                                                   class="form-control autocompletestreet address"
                                                                   Placeholder="Unit Number"
                                                                   id="service_and_billing_address_service_unit_{{$key.$k}}"
                                                                   name="fields[{{$key.$k}}][value][service_unit]"
                                                                   onkeyup="changeVal('service_and_billing_address_service_unit_', 'service_and_billing_address_billing_unit_', '{{$key.$k}}');">
                                                        </div> -->
                                                        <div class="col-sm-3 address-field inline-block">
                                                            <input autocomplete="new-password"
                                                                   class="form-control"
                                                                   placeholder="City"
                                                                   id="service_and_billing_address_service_city_{{$key.$k}}"
                                                                   name="fields[{{$key.$k}}][value][service_city]"
                                                                   onkeyup="changeVal('service_and_billing_address_service_city_', 'service_and_billing_address_billing_city_', '{{$key.$k}}');">
                                                        </div>
                                                        <div class="col-sm-3 address-field inline-block">
                                                            <input autocomplete="new-password"
                                                                   class="form-control"
                                                                   placeholder="County"
                                                                   id="service_and_billing_address_service_county_{{$key.$k}}"
                                                                   name="fields[{{$key.$k}}][value][service_county]"
                                                                   onkeyup="changeVal('service_and_billing_address_service_county_', 'service_and_billing_address_billing_county_', '{{$key.$k}}');">
                                                        </div>
                                                        <div class="col-sm-3 address-field inline-block">
                                                            <input id="service_and_billing_address_service_state_{{$key.$k}}"
                                                                   class="form-control stateall statefield address "
                                                                   Placeholder="State"
                                                                   autocomplete="new-password"
                                                                   name="fields[{{$key.$k}}][value][service_state]"
                                                                   onkeyup="changeVal('service_and_billing_address_service_state_', 'service_and_billing_address_billing_state_', '{{$key.$k}}');">
                                                        </div>
                                                        <div class="col-sm-3 address-field inline-block">
                                                            <input class="form-control"
                                                                   id="service_and_billing_address_service_zipcode_{{$key.$k}}"
                                                                   name="fields[{{$key.$k}}][value][service_zipcode]"
                                                                   autocomplete="new-password"
                                                                   data-parsley-trigger="focusout"
                                                                   data-parsley-pattern="[0-9]{5}"
                                                                   data-parsley-pattern-message="Please enter 5 digit zipcode"
                                                                   Placeholder="Zipcode"
                                                                   onkeyup="changeVal('service_and_billing_address_service_zipcode_', 'service_and_billing_address_billing_zipcode_', '{{$key.$k}}');">
                                                        </div>
                                                        <div class="col-sm-3 address-field inline-block">
                                                            <input id="service_and_billing_address_service_country_{{$key.$k}}" style="display: none;"
                                                                   class="form-control stateall statefield address "
                                                                   Placeholder="Country"
                                                                   autocomplete="new-password"
                                                                   name="fields[{{$key.$k}}][value][service_country]"
                                                                   onkeyup="changeVal('service_and_billing_address_service_country_', 'service_and_billing_address_billing_country_', '{{$key.$k}}');">
                                                        </div>
                                                        <input type="hidden"
                                                               name="fields[{{$key.$k}}][value][service_lat]"
                                                               id="service_and_billing_address_service_latitude_{{$key.$k}}">
                                                        <input type="hidden"
                                                               name="fields[{{$key.$k}}][value][service_lng]"
                                                               id="service_and_billing_address_service_longitude_{{$key.$k}}">
                                                        <script>

                                                            var input = document.getElementById('service_and_billing_address_service_address_1_{{$key.$k}}');
                                                            var autocompleteService{{$key.$k}} = new google.maps.places.Autocomplete(input, {
                                                                types: [],
                                                                componentRestrictions: {country: "us"}
                                                            });
                                                            google.maps.event.addListener(autocompleteService{{$key.$k}}, 'place_changed', function () {
                                                                var place = autocompleteService{{$key.$k}}.getPlace();
                                                                $('#service_and_billing_address_service_latitude_{{$key.$k}}').val(place.geometry.location.lat());
                                                                $('#service_and_billing_address_service_longitude_{{$key.$k}}').val(place.geometry.location.lng());
                                                                $('#service_and_billing_address_service_address_1_{{$key.$k}}').val(place.name);
                                                                var address2 = '';
                                                                for (var i = 0; i < place.address_components.length; i++) {
                                                                    var addressType = place.address_components[i].types[0];
                                                                    if (addressType === 'postal_code') {
                                                                        $('#service_and_billing_address_service_zipcode_{{$key.$k}}').val(place.address_components[i].long_name);
                                                                    }
                                                                    if (addressType === "neighborhood" || addressType === "sublocality_level_1") {
                                                                        address2 += place.address_components[i].long_name;
                                                                        address2 += ' '
                                                                    }
                                                                    if (addressType === "locality" || addressType === "administrative_area_level_2") {
                                                                        $('#service_and_billing_address_service_city_{{$key.$k}}').val(place.address_components[i].long_name);
                                                                    }
                                                                    if (addressType === "administrative_area_level_2") {
                                                                        $('#service_and_billing_address_service_county_{{$key.$k}}').val(place.address_components[i].long_name);
                                                                    }
                                                                    if (addressType === "administrative_area_level_1") {
                                                                        $('#service_and_billing_address_service_state_{{$key.$k}}').val(place.address_components[i].long_name);
                                                                    }
                                                                    if (addressType === "country") {
                                                                        $('#service_and_billing_address_service_country_{{$key.$k}}').val(place.address_components[i].long_name);
                                                                    }
                                                                }
                                                                if ($('input[name="is_service_address_same_as_billing_address_{{$key.$k}}"]:checked').val() == "yes") {
                                                                    console.log("ues");
                                                                    copy_address_("{{$key.$k}}", 'yes')
                                                                }
                                                            })
                                                        </script>
                                                        
                                                        <div class="col-sm-12">
                                                            <span class="bill-address-title">Is the billing address same as service address?</span>
                                                            &nbsp;

                                                            <div class="form-group radio-btns pdt0">
                                                                <label class="radio-inline">
                                                                    <input type="radio"
                                                                           name="is_service_address_same_as_billing_address_{{$key.$k}}"
                                                                           onclick='copy_address_("{{$key.$k}}", "yes")'
                                                                           value="yes">
                                                                    Yes
                                                                </label>
                                                                <label class="radio-inline">
                                                                    <input type="radio"
                                                                           name="is_service_address_same_as_billing_address_{{$key.$k}}"
                                                                           onclick='copy_address_("{{$key.$k}}", "no")'
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
                                                                   id="service_and_billing_address_billing_address_1_{{$key.$k}}"
                                                                   name="fields[{{$key.$k}}][value][billing_address_1]"
                                                                   data-parsley-trigger="focusout"
                                                                   @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                                                   data-parsley-required-message="Please enter billing address" @endif>
                                                        </div>
                                                        <div class="col-sm-12 address-field">
                                                            <input autocomplete="new-password"
                                                                   class="form-control autocompletestreet address"
                                                                   Placeholder="Apartment, suite, unit, building, floor, etc. (optional)"
                                                                   id="service_and_billing_address_billing_address_2_{{$key.$k}}"
                                                                   name="fields[{{$key.$k}}][value][billing_address_2]">
                                                        </div>
                                                        <!-- <div class="col-sm-4 address-field">
                                                            <input autocomplete="new-password"
                                                                   class="form-control autocompletestreet address"
                                                                   Placeholder="Unit Number"
                                                                   id="service_and_billing_address_billing_unit_{{$key.$k}}"
                                                                   name="fields[{{$key.$k}}][value][billing_unit]">
                                                        </div> -->

                                                        <div class="col-sm-3 address-field inline-block">
                                                            <input autocomplete="new-password"
                                                                   class="form-control"
                                                                   placeholder="City"
                                                                   id="service_and_billing_address_billing_city_{{$key.$k}}"
                                                                   name="fields[{{$key.$k}}][value][billing_city]">
                                                        </div>

                                                        <div class="col-sm-3 address-field inline-block">
                                                            <input autocomplete="new-password"
                                                                   class="form-control"
                                                                   placeholder="County"
                                                                   id="service_and_billing_address_billing_county_{{$key.$k}}"
                                                                   name="fields[{{$key.$k}}][value][billing_county]">
                                                        </div>

                                                        <div class="col-sm-3 address-field inline-block">
                                                            <input id="service_and_billing_address_billing_state_{{$key.$k}}"
                                                                   class="form-control stateall statefield address"
                                                                   Placeholder="State"
                                                                   autocomplete="new-password"
                                                                   name="fields[{{$key.$k}}][value][billing_state]">
                                                        </div>

                                                        <div class="col-sm-3 address-field inline-block">
                                                            <input class="form-control"
                                                                   name="fields[{{$key.$k}}][value][billing_zipcode]"
                                                                   id="service_and_billing_address_billing_zipcode_{{$key.$k}}"
                                                                   autocomplete="new-password"
                                                                   data-parsley-trigger="focusout"
                                                                   data-parsley-pattern="[0-9]{5}"
                                                                   data-parsley-pattern-message="Please enter 5 digit zipcode"
                                                                   Placeholder="Zipcode">
                                                        </div>

                                                        <div class="col-sm-3 address-field inline-block">
                                                            <input id="service_and_billing_address_billing_country_{{$key.$k}}" style="display: none;"
                                                                   class="form-control stateall statefield address "
                                                                   Placeholder="Country"
                                                                   autocomplete="new-password"
                                                                   name="fields[{{$key.$k}}][value][billing_country]">
                                                        </div>
                                                        <input type="hidden"
                                                               name="fields[{{$key.$k}}][value][billing_lat]"
                                                               id="service_and_billing_address_billing_latitude_{{$key.$k}}">
                                                        <input type="hidden"
                                                               name="fields[{{$key.$k}}][value][billing_lng]"
                                                               id="service_and_billing_address_billing_longitude_{{$key.$k}}">

                                                    </div>

                                                    <script>
                                                        var input = document.getElementById('service_and_billing_address_billing_address_1_{{$key.$k}}');
                                                        var autocompleteBilling{{$key.$k}} = new google.maps.places.Autocomplete(input, {
                                                            types: [],
                                                            componentRestrictions: {country: "us"}
                                                        });
                                                        google.maps.event.addListener(autocompleteBilling{{$key.$k}}, 'place_changed', function () {
                                                            var place = autocompleteBilling{{$key.$k}}.getPlace();
                                                            console.log(place);
                                                            $('#service_and_billing_address_billing_latitude_{{$key.$k}}').val(place.geometry.location.lat());
                                                            $('#service_and_billing_address_billing_longitude_{{$key.$k}}').val(place.geometry.location.lng());
                                                            $('#service_and_billing_address_billing_address_1_{{$key.$k}}').val(place.name);
                                                            var address2 = '';
                                                            for (var i = 0; i < place.address_components.length; i++) {
                                                                var addressType = place.address_components[i].types[0];
                                                                if (addressType === 'postal_code') {
                                                                    $('#service_and_billing_address_billing_zipcode_{{$key.$k}}').val(place.address_components[i].long_name);
                                                                }
                                                                if (addressType === "neighborhood" || addressType === "sublocality_level_1") {
                                                                    address2 += place.address_components[i].long_name;
                                                                    address2 += ' '
                                                                }
                                                                if (addressType === "locality" || addressType === "administrative_area_level_2") {
                                                                    $('#service_and_billing_address_billing_city_{{$key.$k}}').val(place.address_components[i].long_name);
                                                                }
                                                                // For billing_county mobile view
                                                                if (addressType === "administrative_area_level_2") {
                                                                    $('#service_and_billing_address_billing_county_{{$key.$k}}').val(place.address_components[i].long_name);
                                                                }
                                                                // End
                                                                if (addressType === "administrative_area_level_1") {
                                                                    $('#service_and_billing_address_billing_state_{{$key.$k}}').val(place.address_components[i].long_name);
                                                                }
                                                                if (addressType === "country") {
                                                                    $('#service_and_billing_address_billing_country_{{$key.$k}}').val(place.address_components[i].long_name);
                                                                }
                                                            }
                                                            $('#service_and_billing_address_billing_address_2_{{$key.$k}}').val(address2);
                                                        });
                                                    </script>
                                                </div>
                                            @elseif ($field['type'] == 'label')
                                                <div class="form-group" rel="label">
                                                    <label class="control-label title-lable">{{ ucfirst(array_get($field, 'label')) }}</label>
                                                </div>
                                            @elseif ($field['type'] == 'heading')
                                                <div class="form-group title-lable" rel="label">
                                                    <h3>{{ ucfirst(array_get($field, 'label')) }}</h3>
                                                </div>
                                            @elseif ($field['type'] == 'phone_number')
                                                <div class="form-group" rel="label">
                                                    <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                                                    <input type="text"
                                                           autocomplete="new-password"
                                                           class="form-control mobile"
                                                           name="fields[{{$key.$k}}][value][value]"
                                                           placeholder="{{ $field['label'] }}">
                                                </div>
                                            @elseif ($field['type'] == 'email')
                                                <div class="form-group" rel="label">
                                                    <label class="control-label">{{ ucfirst(ucfirst(array_get($field, 'label'))) }}</label>
                                                    <input type="email"
                                                           autocomplete="new-password"
                                                           class="form-control email"
                                                           name="fields[{{$key.$k}}][value][value]"
                                                           placeholder="{{ $field['label'] }}">
                                                </div>
                                            @elseif($field['type'] == 'selectbox')
                                                <div class="form-group" rel="label">
                                                    <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                                                    @if (isset($field['meta']['options']) && !empty($field['meta']['options']))
                                                        <select name="fields[{{$key.$k}}][value][value]"
                                                            class="select2 form-control"
                                                            title="Please enter {{strtolower(array_get($field, 'label'))}}">
                                                            <option value="">Select</option>
                                                        @foreach($field['meta']['options'] as $mVal)
                                                            <option value="{{$mVal}}">{{ $mVal }}</option>
                                                        @endforeach
                                                        </select>
                                                    @else
                                                        <div class="form-group"><p>No options available for {{ ucfirst(array_get($field, 'label')) }} field</p></div>
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                        </div><!--end--preview-modal-scroll--->
                                        <!--bottom-button-next--->
                                        <ul class="list-inline text-center">
                                            <li><button type="button" class="btn btn-green btn-block next-step">Next</button></li>
                                        </ul>
                                    </div>
                                @endforeach
                                <!-- <div class="tab-pane active" role="tabpanel" id="step1">
                                    <div class="preview-modal-scroll scrollbar-inner">
                                        <div class="form-group">
                                            <label class="control-label">First Name</label>
                                            <input type="text" autocomplete="off" class="form-control" placeholder="First Name">
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Last Name</label>
                                            <input type="text" autocomplete="off" class="form-control" placeholder="Last Name">
                                        </div>
                                    </div><!--end--preview-modal-scroll--->
                                    <!--bottom-button-next--->
                                    <!-- <ul class="list-inline text-center">
                                        <li><button type="button" class="btn btn-green next-step">Next</button></li>
                                    </ul>
                                </div> --> 

                                <!-- <div class="tab-pane" role="tabpanel" id="step2">
                                <div class="preview-modal-scroll scrollbar-inner">
                                    <h3>Step 2</h3>
                                    <p>This is step 2</p>
                                    </div><!--end--preview-modal-scroll--->
                                    <!-- <ul class="list-inline text-center"> -->
                                        <!-- <li><button type="button" class="btn btn-default prev-step">Previous</button></li> -->
                                        <!-- <li><button type="button" class="btn btn-green next-step">Next</button></li>
                                    </ul>
                                </div>  -->
                                            
                                        

                                <!--start-bottom-step-forword-button--->
                                <div class="wizard-inner">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li role="presentation" class="active">
                                            <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab">
                                                <span class="round-tab"></span>
                                            </a>
                                        </li>

                                        <li role="presentation" class="disabled">
                                            <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab">
                                                <span class="round-tab"></span>
                                            </a>
                                        </li>
                                        @foreach($mobiles as $key => $fields)                                                    
                                        <li role="presentation" class="disabled">
                                            <a href="#step{{$key+3}}" data-toggle="tab" aria-controls="step{{$key+2}}" role="tab">
                                                <span class="round-tab"></span>
                                            </a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="clearfix"></div>
                            </div><!--end-tab-content-->
                        @endif
                    </div><!--end-wizard-content-->
                </section>
            </div><!--end-mobile-view--->
        </div>

        </div>

        <!-- <div class="text-center mt10">
            <button type="button" class="btn btn-red mb15" data-dismiss="modal">Close</button>
        </div> -->
    </div>
</div>

<script>
    jQuery(document).ready(function () {
        jQuery('.scrollbar-inner').scrollbar();
    });
</script>

<script>
    var serviceAndBillingElements = [];

    function changeVal(sourceElement, destElement, i) {
        if (serviceAndBillingElements.indexOf(parseInt(i)) != -1) {
            $("#" + destElement + i).val($("#" + sourceElement + i).val());
        }
    }
</script>

<!-----------------view-form-step----->
<script>
    $(document).ready(function () {
    //Initialize tooltips
    $('.nav-tabs > li a[title]').tooltip();
    
    //Wizard
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {

        var $target = $(e.target);
    
        // if ($target.parent().hasClass('disabled')) {
        //     return false;
        // }
    });

    $(".next-step").click(function (e) {

        var $active = $('.wizard .nav-tabs li.active');
        $active.next().removeClass('disabled');
        nextTab($active);

    });
    $(".prev-step").click(function (e) {

        var $active = $('.wizard .nav-tabs li.active');
        prevTab($active);

    });
});

function nextTab(elem) {
    $(elem).next().find('a[data-toggle="tab"]').click();
}
function prevTab(elem) {
    $(elem).prev().find('a[data-toggle="tab"]').click();
}

$(document).ready(function() {
        $('.select2').select2();
    });

</script>
