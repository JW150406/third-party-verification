<!--Address block-->
<li id="form_field_wrapper_{{$elementNum}}" class="form-field-wrapper open">

    <div class="single-field-wrapper ">
        <div class="edit-delete-settings">
            <span class="pull-left">Address</span>
            <a href="javascript:void(0)" class="collapsed_icon" rel="{{$elementNum}}">
                <span class="fa fa-fw fa-chevron-up  collapse-setting"></span>
                <span class="fa fa-fw fa-chevron-down expand-setting"></span>
            </a>
            <a class="remove-element" href="javascript:void(0)" rel="{{$elementNum}}">
                <span class="fa fa-fw fa-times"></span>
            </a>
        </div>
        <div class="form-group field-settings">
            <div class="clearfix"></div>
            <div class="field-settings">
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-xs-12 col-sm-7 col-md-7 col-lg-7">
                        <div class="settings-wrapper">
                            <div class="single-setting-wrapper">
                                <label class="">Label</label>
                                <input type="hidden" name="field[{{$elementNum}}][address][id]" value="{{ array_get($field, 'id', '') }}">
                                <input class="form-control fieldlabel" attr = "address" id ="{{ array_get($field, 'id',"$elementNum") }}" name="field[{{$elementNum}}][address][label]" type="Text" value="{{ !empty($field) ? array_get($field, 'label', '') : 'Address Label' }}">
                            </div>
                        </div>
                
                        <div class="single-setting-wrapper">
                            <label class="">Settings</label>
                            <div class="form-group checkbx">
                                <label class="checkbx-style">Required
                                    <input autocomplete="off" type="checkbox" name="field[{{$elementNum}}][address][is_required]" @if(  array_get($field, 'is_required') ) checked @endif > 
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="form-group checkbx">
                                <label class="checkbx-style">Primary
                                    <input autocomplete="off" type="checkbox" name="field[{{$elementNum}}][address][is_primary]" @if(  array_get($field, 'is_primary') ) checked @endif class="is_primary_add" > 
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="form-group checkbx">
                                <label class="checkbx-style">Allow Copying
                                    <input autocomplete="off" type="checkbox" name="field[{{$elementNum}}][address][is_allow_copy]" @if(  array_get($field, 'is_allow_copy') ) checked @endif value="1"> 
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="form-group checkbx">
                                <label class="checkbx-style">Auto Caps
                                    <input autocomplete="off" type="checkbox" name="field[{{$elementNum}}][address][is_auto_caps]" @if(  array_get($field, 'is_auto_caps') ) checked @endif value="1"> 
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="form-group checkbx">
                                <label class="checkbx-style">Allow Multiple
                                    <input autocomplete="off" type="checkbox" class="multienrollmentcheckbox[]" name="field[{{$elementNum}}][address][is_multienrollment]" @if(  array_get($field, 'is_multienrollment') ) checked @endif value="1"> 
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-5 col-md-5 col-lg-5">
                        <div class="tags-display" id ="address-{{ array_get($field, 'id', "$elementNum") }}-tags">
                            <label class="tag-label">Tags</label>
                            <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Address Label' }}</span></p>
                            <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Address Label' }}-> AddressLine1</span></p>
                            <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Address Label' }}-> AddressLine2</span></p>
                            <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Address Label' }}->UnitNumber</span></p>
                            <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Address Label' }}-> ZipCode</span></p>
                            <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Address Label' }}-> City</span></p>
                            <!--  For address county tag -->
                            <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Address Label' }}-> County</span></p>
                            <!-- End -->
                            <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Address Label' }}-> State</span></p>
                            <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Address Label' }}-> Country</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</li>
<!--Address block-->