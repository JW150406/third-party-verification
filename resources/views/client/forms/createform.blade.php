@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array();
if (Auth::user()->access_level == 'tpv') {
    $breadcrum[] =  array('link' => route('client.index'), 'text' =>  'Clients');
    $breadcrum[] =  array('link' => route('client.show', $client->id), 'text' =>  $client->name);
}

if (array_get($form, 'id')) {
    $breadcrum[] =  array('link' => '', 'text' =>  array_get($form, 'formname'));
} else {
    $breadcrum[] =  array('link' => '', 'text' =>  'Create Enrollment Form');
}

breadcrum($breadcrum);
?>



<?php

$added_fields = 0;
$formid = 0;


?>

<div class="tpv-contbx">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3 leadcreation_contbx">
                    <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
                        <div class="client-bg-white">
                            @if (array_get($form, 'id'))
                                @if (Request::route()->getName() == 'client.contact-page-layout')
                                    <h1>Edit Enrollment Form</h1>
                                @else
                                    <h1>Clone Enrollment Form</h1>
                                @endif
                            @else
                            <h1>Create Enrollment Form</h1>
                            @endif
                            <!-- Nav tabs -->
                            <!-- Tab panes -->
                            <div class="tab-content edit-agentinfo ">

                                <!--agent details starts-->

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="agent-detailform">
                                            <div class="col-xs-12 col-sm-12 col-md-12">
                                                <div class="row">
                                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                                        @if ($message = Session::get('success'))
                                                        <div class="alert alert-success">
                                                            <p>{{ $message }}</p>
                                                        </div>
                                                        @endif
                                                        @if ($errMessage = Session::get('error'))
                                                        <div class="alert alert-error">
                                                            <p>{{ $errMessage }}</p>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="display-table">
                                                    <div class="display-table-cell form-left-part ">

                                                        <form id="create-lead-form" class="company-form-layout v-star" role="form" method="POST" action="{{ route('client.lead-forms.store', $client->id) }}" data-parsley-validate>
                                                            {{ csrf_field() }}
                                                            {{ method_field('POST') }}
                                                            @if (array_get($form, 'id') && Request::route()->getName() == 'client.contact-page-layout')
                                                            <input type="hidden" name="id" value="{{array_get($form, 'id')}}">
                                                            @endif
                                                            @if (Request::route()->getName() == 'client.contact-page-layout.clone')
                                                            <input type="hidden" name="is_clone" value="{{array_get($form, 'id')}}">
                                                            @endif
                                                            <div class="col-xs-12 col-sm-12 col-md-12 pdl0">
                                                                <div class="static-lead-data">
                                                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                                                        <div class="form-group">
                                                                            <label class="pdt0" for="clientformname">Form Name</label>
                                                                            <input class="form-control fieldlabel" data-parsley-required='true' name="formname" id="clientformname" value="{{ old('formname') ? old('formname') : array_get($form, 'formname') }}" type="Text">
                                                                            <div id="showFromNameError"></div>
                                                                            @if ($errors->has('formname'))
                                                                            <span class="help-block error">
                                                                                <strong>{{ $errors->first('formname') }}</strong>
                                                                            </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    

                                                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                                                        <div id="commodities-section" class="dropdown agent-edit form-group form-como">
                                                                            <div class="form-group checkbx-inline">
                                                                                <label for="commodity">Commodity</label>
                                                                            </div>

                                                                            <?php
                                                                            $selectedCommodities = [];
                                                                            if (array_get($form, 'id')) {
                                                                                $selectedCommodities = $form->commodities->pluck('id')->toArray();
                                                                            }
                                                                            ?>
                                                                            @forelse ($commodities as $commodity)
                                                                            <div class="form-group checkbx-inline">
                                                                                <label class="checkbx-style"> {{ $commodity->name }}
                                                                                    <input autocomplete="off" type="checkbox" name="commodities[]" value="{{ $commodity->id }}" class="input-commodities" @if (in_array($commodity->id, $selectedCommodities)) checked @endif>
                                                                                    <span class="checkmark"></span>
                                                                                </label>
                                                                            </div>
                                                                            @empty
                                                                            <p>No Commodity found</p>
                                                                            @endforelse
                                                                            <div id="commodity-error"></div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- code added here for the multienrollment -->
                                                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                                                        <div class="single-setting-wrapper">
                                                                            <label class="">Multiple Enrollment</label>
                                                                            <div class="form-group checkbx-inline">
                                                                                <label class="checkbx-style">Allow
                                                                                    <input autocomplete="off" type="checkbox" id="multienrollmentCheckbox" @if (array_get($form, 'multienrollment')) checked @endif>
                                                                                    @if (array_get($form, 'multienrollment'))
                                                                                    <input type="hidden" name="multienrollment" value="1" id="multienrollment" >
                                                                                    @else
                                                                                    <input type="hidden" name="multienrollment" value="0" id="multienrollment" >
                                                                                    @endif
                                                                                    <span class="checkmark"></span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- endof code for multi enrollment -->

                                                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                                                        <div class="dropdown agent-edit form-group">
                                                                            <label for="description" class="nostar">Description</label>
                                                                            <textarea type="text" class="form-control" name="description" value="{{ array_get($form, 'description') }}"></textarea>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                                                        <div class="dropdown agent-edit form-group">
                                                                            <label for="channel">Channel</label>
                                                                            <select name="channel" class="select2 form-control" data-parsley-required='true' data-parsley-required-message="Please select channel">
                                                                                <option value="both" @if(array_get($form, 'channel' )== config('constants.FORM_CHANNEL_BOTH') ) selected @endif>Both</option>
                                                                                <option value="mobile" @if(array_get($form, 'channel' )== config('constants.FORM_CHANNEL_MOBILE') ) selected @endif>Door-to-Door</option>
                                                                                <option value="web" @if(array_get($form, 'channel' )== config('constants.FORM_CHANNEL_WEB') ) selected @endif>Telemarketing</option>

                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-xs-12 col-sm-12 col-md- pdl0">
                                                                <ul id="formfields" class="contact-form-fields-admin">

                                                                    @if (array_get($form, 'id') && array_get($form, 'fields'))
                                                                    @foreach (array_get($form, 'fields') as $field)
                                                                    <?php




                                                                    $type = array_get($field, 'type');


                                                                    $elementNum = array_get($field, 'id');
                                                                    switch ($type) {
                                                                        case 'fullname':
                                                                            $view = 'client.forms.formfields.full_name';
                                                                            break;

                                                                        case 'address':
                                                                            $view = 'client.forms.formfields.address';
                                                                            break;

                                                                        case 'service_and_billing_address':
                                                                            $view = 'client.forms.formfields.service_and_billing_address';
                                                                            break;

                                                                        case 'textbox':
                                                                            $view = 'client.forms.formfields.text_box';
                                                                            break;

                                                                        case 'textarea':
                                                                            $view = 'client.forms.formfields.text_area';
                                                                            break;

                                                                        case 'radio':
                                                                            $view = 'client.forms.formfields.radio';
                                                                            break;

                                                                        case 'checkbox':
                                                                            $view = 'client.forms.formfields.checkbox';
                                                                            break;

                                                                        case 'selectbox':
                                                                            $view = 'client.forms.formfields.selectbox';
                                                                            break;

                                                                        case 'separator':
                                                                            $view = 'client.forms.formfields.separator';
                                                                            break;

                                                                        case 'heading':
                                                                            $view = 'client.forms.formfields.heading';
                                                                            break;

                                                                        case 'label':
                                                                            $view = 'client.forms.formfields.label';
                                                                            break;

                                                                        case 'phone_number':
                                                                            $view = 'client.forms.formfields.phone_number';
                                                                            break;

                                                                        case 'email':
                                                                            $view = 'client.forms.formfields.email';
                                                                            break;

                                                                        default:
                                                                            # code...
                                                                            break;
                                                                    };

                                                                    ?>
                                                                    @include($view, ['elementNum' => $elementNum, 'field' => $field])
                                                                    @endforeach
                                                                    @endif
                                                                </ul>
                                                            </div>
                                                            <!-- <div class="col-xs-12 col-sm-12 col-md-4 pdl0">
                                                                    hello
                                                            </div> -->
                                                            <div class="btnintable bottom_btns leadcreation ">
                                                                <div class="btn-group mt30 mb30">
                                                                    <input type="hidden" class="added_elements" value="<?php echo $added_fields ?>">
                                                                    <button class="btn btn-green mr15" type="submit">Save</button>
                                                                    <a href="#" class="btn btn-green mr15" id="previewLeadForm">Review</a>
                                                                    <a href="{{route('client.show',['id' => $client->id])}}#EnrollmentForm" class="btn  btn-red">Cancel</a>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="display-table-cell form-right-part ">
                                                        <div class="col-xs-12 col-sm-12 col-md-12   sticky-panel">

                                                            <div class="zip-inputbx">
                                                                <form class="add-field-form" role="form" method="POST" onsubmit="return false" action="">
                                                                    <div id="sticky-anchor"></div>
                                                                    <div class="">
                                                                        <label for="addnewfield">Add New Field</label>

                                                                        <!-- <select class="select2 no-search select-box-admin " id="select-box-admin">
                                                                            <option value="">Select</option>
                                                                            @foreach($formFields as $key => $field)
                                                                            <option value="{{$key}}">{{$field}}</option>
                                                                            @endforeach
                                                                        </select> -->

                                                                        <select class="select-open form-control" size="13" id="select-box-admin" style="display:none">

                                                                            @foreach($formFields as $key => $field)
                                                                            <option class="bg-wt" value="{{$key}}">{{$field}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        <div id="field-error-div"><span id="field-error" class="help-block error"></span></div>
                                                                        
                                                                    </div>

                                                                    <div class="leadcreation">
                                                                        <button class="btn btn-green add-new-item" type="button">Add</button>
                                                                    </div>
                                                                </form>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <!--agent details ends-->

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="team-addnewmodal client-new-tabs">
    <div class="modal fade" id="preview_lead_form">
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('/js/admin-client-contact.js') }}"></script>
<script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC49bXfihl4zZqjG2-iRLUmcWO_PVcDehM&libraries=places"></script>
<script>
    var countElements = 0;
    @if(array_get($form, 'fields'))
    countElements = parseInt("{{ count(array_get($form, 'fields')) }}");
    @endif

    $(document).ready(function() {
    console.log("{{ array_get($form, 'fields') }}");
    
        $('.select-open').toggle();
        $('#multienrollmentCheckbox').change(function() {
            if($(this).is(":checked")) {
                $("#multienrollment").val(1);
            }else{
                $("#multienrollment").val(0);
            }
        });
        $(document).click(function(e) {
            $('.select-open').attr('size', 13);
        });
        window.Parsley.addValidator("requiredIf", {
            validateString: function(value, requirement) {
                if (jQuery(requirement).val()) {
                    return !!value;
                }

                return true;
            },
            messages: {
                en: 'This field is required'
            },
            priority: 33
        })

        function delay(callback, ms) {
            var timer = 0;
            return function() {
                var context = this, args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function () {
                callback.apply(context, args);
                }, ms || 0);
            };
        }
        $('body').on('keyup', '.fieldlabel', delay(function(){
            fieldId = $(this).attr('id');
            attr = $(this).attr('attr');
            var toggle;
            toggle = $('.expandTagId-'+fieldId).attr('toggle');
            
            changeTextId = attr+"-"+fieldId+"-tags";
            var text = $(this).val();
            var div ='';
            if(text.length > 0){
                div += '<label class="tag-label">Tags</label>';
                
                if(attr == "fullname" )
                {
                    div+='<p><span class="question-tag">'+text+'</span></p>';
                    div+='<p><span class="question-tag">'+text+' -> First name </span></p>';
                    div+='<p><span class="question-tag">'+text+' -> Middle name </span></p>';
                    div+='<p><span class="question-tag">'+text+' -> Last name </span></p>';
                }
                else if(attr == "service-billing-address")
                {
                    div+='<p><span class="question-tag">'+text+'</span></p>';
                    div+='<p><span class="question-tag">'+text+'->Service Address</span></p>';
                    div+='<p><span class="question-tag">'+text+' -> ServiceAddressLine1 </span></p>';
                    div+='<p><span class="question-tag">'+text+' -> ServiceAddressLine2 </span></p>';
                    if(toggle == 'more')
                    {
                        div+='<div class ="tagExpandSpan-'+fieldId+'" style = "display:none;">';
                    }
                    else{
                        div+='<div class ="tagExpandSpan-'+fieldId+'" style = "display:block;">';
                    }

                    div+= '<p><span class="question-tag">'+text+' -> ServiceUnitNumber </span></p>';
                    div+='<p><span class="question-tag">'+text+' -> ServiceZipCode </span></p>';
                    div+='<p><span class="question-tag">'+text+' -> ServiceCity </span></p>';
                    // For service address county tag
                    div+='<p><span class="question-tag">'+text+' -> ServiceCounty </span></p>';
                    // End
                    div+='<p><span class="question-tag">'+text+' -> ServiceState </span></p>';
                    div+='<p><span class="question-tag">'+text+' -> ServiceCountry </span></p>';

                    div+='<p><span class="question-tag">'+text+'</span></p>';
                    div+='<p><span class="question-tag">'+text+'->Billing Address</span></p>';
                    div+='<p><span class="question-tag">'+text+' -> BillingAddressLine1 </span></p>';
                    div+='<p><span class="question-tag">'+text+' -> BillingAddressLine2 </span></p>';
                    div+='<p><span class="question-tag">'+text+' -> BillingUnitNumber </span></p>';
                    div+='<p><span class="question-tag">'+text+' -> BillingZipCode </span></p>';
                    div+='<p><span class="question-tag">'+text+' -> BillingCity </span></p>';
                    // For billing address county tag
                    div+='<p><span class="question-tag">'+text+' -> BillingCounty </span></p>';
                    // End
                    div+='<p><span class="question-tag">'+text+' -> BillingState </span></p>';
                    div+='<p><span class="question-tag">'+text+' -> BillingCountry </span></p></div>';
                    if(toggle == 'more')
                    {   
                        div+='<a class = "theme-color tagExpandButton expandTagId-'+fieldId+' pull-right mt20" toggle = "more" id="'+fieldId+'" name = ""  style = "cursor: pointer; text-align: center; font-size: 12px; font-weight: 500;">Show More <span id="arrow-'+fieldId+'" class=" expandSpanIcon fa fa-fw fa-chevron-right"></span></a>';
                    }
                    else
                    {
                        
                        div+='<a class = "theme-color tagExpandButton expandTagId-'+fieldId+' pull-right mt20" toggle = "less" id="'+fieldId+'" name = ""  style = "cursor: pointer; text-align: center; font-size: 12px; font-weight: 500;"><span id="arrow-'+fieldId+'" class=" expandSpanIcon fa fa-fw fa-chevron-left"></span> Show Less</a>';
                    }
                }
                else if(attr == "address")
                {
                    div+='<p><span class="question-tag">'+text+'</span></p>';
                    div+='<p><span class="question-tag">'+text+' -> AddressLine1 </span></p>';
                    div+='<p><span class="question-tag">'+text+' -> AddressLine2 </span></p>';
                    div+='<p><span class="question-tag">'+text+' -> UnitNumber </span></p>';
                    div+='<p><span class="question-tag">'+text+' -> ZipCode </span></p>';
                    div+='<p><span class="question-tag">'+text+' -> City </span></p>';
                    // For address county tag
                    div+='<p><span class="question-tag">'+text+' -> County </span></p>';
                    // End
                    div+='<p><span class="question-tag">'+text+' -> State </span></p>';
                    div+='<p><span class="question-tag">'+text+' -> Country </span></p>';
                }
                else
                {
                    div += '<p><span class="question-tag">'+text+'</span></p>';
                }
            }
            $("#"+changeTextId).html(div);
            
        },500));
        
        $('body').on('click','.tagExpandButton',function(){
            buttonText = $(this).text();
            buttonId = $(this).attr('id');
            expandShrinkType = $(this).attr('toggle');
            // $('.tagExpandSpan-'+buttonId).fadeToggle("slow");
            
            
            if(expandShrinkType == "more")
            {
                $('.tagExpandSpan-'+buttonId).fadeIn("slow");
                $(this).attr('toggle','less');
                $(this).text("");
                $(this).append('<span  id="arrow-"'+buttonId+' class=" expandSpanIcon fa fa-fw fa-chevron-left"></span>');
                $(this).append(' Show Less');
                // $('#arrow-'+buttonId).attr('class','fa fa-fw fa-chevron-right');
            }
            else
            {
                $('.tagExpandSpan-'+buttonId).fadeOut("slow");
                $(this).attr('toggle','more');
                $(this).text('Show More');
                $(this).append('<span  id="arrow-"'+buttonId+' class=" expandSpanIcon fa fa-fw fa-chevron-right"></span>');
            }
            $('html, body').animate({
                    scrollTop: $("#service-billing-address-"+buttonId+"-tags").offset().top 
                }, 1500);
            
        });
    });

    function scrolltodiv(element) {
        $('html, body').animate({
            scrollTop: $("#" + element).offset().top
        }, 2000);
    }
    $('body').on('change', '#workspace_id', function() {
        var selectedWorkspace = $(this).val();
        $.ajax({
            method: "post",
            url: "{{ route('ajax-client-workflow') }}",
            data: {
                "workspaceid": $(this).val(),
                "client_id": "{{$client->id}}"
            },
            success: function(res) {
                if (res.status == "success") {
                    $("#workflow_id").html(res.options);
                } else {
                    alert("Something went wrong while retrieving workflow !!")
                }
            }
        });
    });

    $('#previewLeadForm').click(function(e) {
        e.preventDefault()
        var data = $('#create-lead-form').serializeArray();
        if (parseInt(countElements) > 0) {
            $.ajax({
                method: "POST",
                url: "{{ route('lead_forms.preview') }}",
                data: data,
                success: function(res) {
                    if (res.status == "success") {
                        $('#preview_lead_form').html(res.view).modal('show');
                    } else {
                        console.log(res.message);
                    }
                },
                error: function(res) {
                    alert('Woops, Something went wrong, please try again');
                }
            })
        } else {
            alert("Please add fields to review form.")
        }
    })

    $('body').on('submit', '#create-lead-form', function() {
        $(".help-block").remove();
        if ($('#create-lead-form input[name="commodities[]"]:checked').length <= 0) {
            $("#commodity-error").html("<span class='help-block error'>This field is required</span>");
            $('html, body').animate({
                scrollTop: $("#commodities-section").offset().top
            }, 2000);
            return false;
        }
        if($('#multienrollmentCheckbox').prop('checked') == true){
            if ($('#create-lead-form input[class="multienrollmentcheckbox[]"]:checked').length <= 0) {
                alert("Please select at least one multi enrollment field");
                return false;
            }
        }

        if (countElements <= 0) {
            $("#field-error-div").html("<span id='field-error' class='help-block error'>Please add a field from here</span>");
            return false;
        }


        if ($('#create-lead-form input[class=is_primary_name]').length > 0 && $('#create-lead-form input[class=is_primary_name]:checked').length <= 0) {
            alert("Please select at least one full name field as primary !!");
            return false;
        }

        if ($('#create-lead-form input[class=is_primary_name]').length > 1 && $('#create-lead-form input[class=is_primary_name]:checked').length > 1) {
            alert("Please select only one full name field as primary !!");
            return false;
        }

        if ($('#create-lead-form input[class=is_primary_phone]').length > 0 && $('#create-lead-form input[class=is_primary_phone]:checked').length <= 0) {
            alert("Please select at least one phone number field as primary !!");
            return false;
        }

        if ($('#create-lead-form input[class=is_primary_phone]').length > 1 && $('#create-lead-form input[class=is_primary_phone]:checked').length > 1) {
            alert("Please select only one phone number field as primary !!");
            return false;
        }

        if ($('#create-lead-form input[class=is_primary_multi_add]').length > 0 && $('#create-lead-form input[class=is_primary_multi_add]:checked').length <= 0) {
            alert("Please select at least one service and billing field as primary !!");
            return false;
        }

        if ($('#create-lead-form input[class=is_primary_multi_add]').length > 1 && $('#create-lead-form input[class=is_primary_multi_add]:checked').length > 1) {
            alert("Please select only one service and billing field as primary !!");
            return false;
        }

        if ($('#create-lead-form input[class=is_primary_email]').length > 0 && $('#create-lead-form input[class=is_primary_email]:checked').length <= 0) {
            alert("Please select at least one email field as primary !!");
            return false;
        }

        if ($('#create-lead-form input[class=is_primary_email]').length > 1 && $('#create-lead-form input[class=is_primary_email]:checked').length > 1) {
            alert("Please select only one email field as primary !!");
            return false;
        }

        if ($('#create-lead-form input[class=is_primary_add]').length > 0 && $('#create-lead-form input[class=is_primary_add]:checked').length <= 0) {
            alert("Please select at least one address field as primary !!");
            return false;
        }

        if ($('#create-lead-form input[class=is_primary_add]').length > 1 && $('#create-lead-form input[class=is_primary_add]:checked').length > 1) {
            alert("Please select only one address field as primary !!");
            return false;
        }

        var commodities = [];
        $('#create-lead-form input[name="commodities[]"]:checked').each(function(index,element) {
            let commodity = $(this).closest('label').text().trim();
            commodities.push(commodity);
        });
        console.log(commodities);
        if (commodities.length > 1) { 
            let isValidLabel = true;
            let validLabels = [];
            let addressCount = $("input[tag='service_and_billing_address']").length;

            $('.fieldlabel').each(function(index,element) {
                let label = $(this).val();

                if (label.search(/account number/i) >= 0 || ($(this).attr('tag') == 'service_and_billing_address' && addressCount > 1)) {
                    console.log('find');
                    let labelArray = label.split('- ');
                    console.log(labelArray);
                    
                    if (labelArray.length == 2 && commodities.includes(labelArray[1])) {
                        console.log('account label is valid');
                        if(validLabels.includes(label.toLowerCase())) {
                            console.log('This label already exists.');
                            $(element).after("<span class='help-block error'>This label name already exists.</span>");
                            isValidLabel = false;  
                        }
                        validLabels.push(label.toLowerCase());
                    } else {
                        console.log('This label is Invalid');
                        let forError = 'Account Number'; 
                        if ($(this).attr('tag') == 'service_and_billing_address') {
                            forError = 'Service and Billing Address';
                        }
                        $(element).after("<span class='help-block error'>Please add commodity name after label. (Ex.:- "+forError+" - Gas)</span>");
                        isValidLabel = false;                   
                    }
                }
            });
            if (!isValidLabel) {
                return false;
            }
        }
    });

    $(".input-commodities").change(function() {
        if (this.checked) {
            $("#commodity-error").html("");
        }
    });

    $(".textbox-label").on('change keyup',function() {
        let label = $(this).val();
        let element = $(this).closest('.settings-wrapper').find(".validation-section");
        if (label.search(/account number/i) >= 0) {
            element.hide();
            element.find(':input').val('');
        } else {
            element.show();
        }
    });

    $('body').on('click', '.remove_options', function() {
        $(this).closest('li').remove();
    });

    $('body').on('click', '.add-new-item', function() {
        var addNewField = $('#select-box-admin').val();

        if (addNewField == "") {
            alert('Please select a field');
            return;
        }

        var newElementNumber = parseInt($('.added_elements').val()) + 1;
        $('.added_elements').val(newElementNumber);
        var newHtml = "";

        if (addNewField) {
            $.ajax({
                method: "get",
                url: "{{ url('admin/client') }}/" + "{{$client->id}}/" + "contact-form/create/field?type=" + addNewField,
                data: {
                    "element_num": newElementNumber
                },
                success: function(res) {
                    $("#formfields").append(res.view);
                    scrolltodiv('form_field_wrapper_' + newElementNumber);
                    // remove_class();

                    $('body').on('keyup', '.fieldlabel', function() {
                        var field_to_add = $(this).attr('rel');
                        $('.control_label_' + field_to_add).html($(this).val());
                    });
                    $('body').on('keyup', '.placeholder', function() {
                        var field_to_add = $(this).attr('rel');
                        $('.form_control_' + field_to_add).attr('placeholder', $(this).val());
                    });

                    $('body').on('keyup', '.option_value_alter', function() {
                        var add_value_to = $(this).attr('ref');
                        $('.' + add_value_to + ' input[type=radio]').val($(this).val());
                    });
                    $('body').on('keyup', '.checkbox_option_value_alter', function() {
                        var add_value_to = $(this).attr('ref');
                        $('.' + add_value_to + ' input[type=checkbox]').val($(this).val());
                    });

                    countElements = countElements + 1;
                    $("#field-error").html("");

                },
                error: function(res) {
                    alert("Unable to add field, please try again !!");
                }
            });
        } else {
            alert("Please select appropriate option !!");
        }
    });

    $('body').on('click', '.remove-element', function() {
        var refid = $(this).attr('rel');
        $('#form_field_wrapper_' + refid).remove();
        countElements = countElements - 1;
    });

    $('#clientformname').keyup(function () {
        $('#showFromNameError').html('')
        $.ajax({
            method: "post",
            url: "{{ route('ajax.checkFormNameExist') }}",
            data: {
                "client_id": "{{ $client->id }}",
                "formname": $('#clientformname').val()
            },
            success: function(res) {
                if(res.exists === true) {
                    $('#showFromNameError').html('<span class="help-block error"><strong>This form name is taken</strong></span>')
                } else {
                    $('#showFromNameError').html('')
                }
            },
            error: function(res) {
                $('#showFromNameError').html('')
                alert('Whoops, Something went wrong, please try again.')
            }
        });
    })

    $(document).ready(function() {
        $(".textbox-label").trigger('change');
    });
</script>

<script>
    $(function() {
        $("#formfields").sortable();
        $("#formfields").disableSelection();
    });
</script>



@endpush