<!--Service and billing address block-->
<li id="form_field_wrapper_{{$elementNum}}" class="form-field-wrapper open">
    <div class="single-field-wrapper ">
        <div class="edit-delete-settings">
            <span class="pull-left">Service and Billing Address</span>
            <a href="javascript:void(0)" class="collapsed_icon" rel="{{$elementNum}}">
                <span class="fa fa-fw fa-chevron-up  collapse-setting"></span>
                <span class="fa fa-fw fa-chevron-down expand-setting"></span>
            </a>
            <a class="remove-element" href="javascript:void(0)" rel="{{$elementNum}}">
                <span class="fa fa-fw fa-times"></span>
            </a>
        </div>
        <div class="form-group field-settings">
            <div class="field-settings">
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-xs-12 col-sm-7 col-md-7 col-lg-7">
                        <div class="settings-wrapper">
                            <div class="single-setting-wrapper">
                                <label class="">Label</label>
                                <input type="hidden" name="field[{{$elementNum}}][service_and_billing_address][id]" value="{{ array_get($field, 'id', '') }}">
                                <input  class="form-control fieldlabel" attr="service-billing-address" id="{{ array_get($field, 'id', "$elementNum") }}" name="field[{{$elementNum}}][service_and_billing_address][label]" type="Text" value="{{ !empty($field) ? array_get($field, 'label', '') : 'Service and Billing address' }}" rel="{{$elementNum}}" tag ="service_and_billing_address" required >
                            </div>
                            <div class="single-setting-wrapper">
                                <label class="">Settings</label>
                                <div class="form-group checkbx">
                                    <label class="checkbx-style">Required
                                        <input autocomplete="off" type="checkbox" name="field[{{$elementNum}}][service_and_billing_address][is_required]"  @if(  array_get($field, 'is_required') ) checked @endif > 
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="form-group checkbx">
                                    <label class="checkbx-style">Primary
                                        <input autocomplete="off" type="checkbox" name="field[{{$elementNum}}][service_and_billing_address][is_primary]" @if(  array_get($field, 'is_primary') ) checked @endif class="is_primary_add" >
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="form-group checkbx">
                                    <label class="checkbx-style">Allow Copying
                                        <input autocomplete="off" type="checkbox" name="field[{{$elementNum}}][service_and_billing_address][is_allow_copy]" @if(  array_get($field, 'is_allow_copy') ) checked @endif value="1"> 
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="form-group checkbx">
                                    <label class="checkbx-style">Auto Caps
                                        <input autocomplete="off" type="checkbox" name="field[{{$elementNum}}][service_and_billing_address][is_auto_caps]" @if(  array_get($field, 'is_auto_caps') ) checked @endif value="1"> 
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="form-group checkbx">
                                    <label class="checkbx-style">Allow Multiple
                                        <input autocomplete="off" type="checkbox" class="multienrollmentcheckbox[]" name="field[{{$elementNum}}][service_and_billing_address][is_multienrollment]" @if(  array_get($field, 'is_multienrollment') ) checked @endif value="1"> 
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-5 col-md-5 col-lg-5">
                        <div class="tags-display serviceBillingTagDivId" id ="service-billing-address-{{ array_get($field, 'id',"$elementNum") }}-tags">
                            <label class="tag-label">Tags</label>
                            <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Service and Billing address' }}</span></p>
                            <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Service and Billing address' }}->Service Address</span></p>
                            <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Service and Billing address' }}->ServiceAddressLine1</span></p>
                            <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Service and Billing address' }}->ServiceAddressLine2</span></p>
                            <div class ="tagExpandSpan-{{ array_get($field, 'id',"$elementNum") }}" style = "display:none;">
                                <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Service and Billing address' }}->ServiceUnitNumber</span></p>
                                <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Service and Billing address' }}->ServiceZipCode</span></p>
                                <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Service and Billing address' }}->ServiceCity</span></p>
                                <!-- For service address county tag -->
                                <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Service and Billing address' }}->ServiceCounty</span></p>
                                <!-- End -->
                                <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Service and Billing address' }}->ServiceState</span></p>
                                <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Service and Billing address' }}->ServiceCountry</span></p>

                                <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Service and Billing address' }}</span></p>
                                <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Service and Billing address' }}->Billing Address</span></p>
                                <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Service and Billing address' }}->BillingAddressLine1</span></p>
                                <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Service and Billing address' }}->BillingAddressLine2</span></p>
                                <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Service and Billing address' }}->BillingUnitNumber</span></p>
                                <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Service and Billing address' }}->BillingZipCode</span></p>
                                <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Service and Billing address' }}->BillingCity</span></p>
                                <!-- For service address county tag -->
                                <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Service and Billing address' }}->BillingCounty</span></p>
                                <!-- End -->
                                <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Service and Billing address' }}->BillingState</span></p>
                                <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Service and Billing address' }}->BillingCountry</span></p>
                            </div>
                            <a class = "theme-color tagExpandButton  expandTagId-{{ array_get($field, 'id',"$elementNum") }} pull-right mt20"  id="{{ array_get($field, 'id',"$elementNum") }}" name = "" toggle = "more" style = "cursor: pointer; text-align: center; font-size: 12px; font-weight: 500;">Show More <span id="arrow-{{ array_get($field, 'id',"$elementNum") }}" class=" expandSpanIcon fa fa-fw fa-chevron-right"></span></a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr />
    </div>
</li>
<!--Service and billing address block-->