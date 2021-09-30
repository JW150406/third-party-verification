<!--Phone block-->
<li id="form_field_wrapper_{{$elementNum}}" class="form-field-wrapper open wid_phone_num">
    <div class="single-field-wrapper ">
        <div class="edit-delete-settings">
            <span class="pull-left">Phone Number</span>
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
                <div class="row">
                    <div class="col-xs-12 col-sm-7 col-md-7 col-lg-7">
                        <div class="settings-wrapper">
                            <div class="single-setting-wrapper">
                                <label class="">Label</label>
                                <input type="hidden" name="field[{{$elementNum}}][phone_number][id]" value="{{ array_get($field, 'id', '') }}">
                                <input class="form-control fieldlabel" attr="phonenumber" id="{{ array_get($field, 'id', "$elementNum") }}" type="Text" rel="5" name="field[{{$elementNum}}][phone_number][label]" value="{{ !empty($field) ? array_get($field, 'label', '') : 'Phone Number' }}">
                            </div>
                            <div class="single-setting-wrapper">
                                <label class="">Settings</label>
                                <div class="form-group checkbx">
                                    <label class="checkbx-style">Required
                                        <input autocomplete="off" type="checkbox" name="field[{{$elementNum}}][phone_number][is_required]"  @if(  array_get($field, 'is_required') ) checked @endif > 
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="form-group checkbx">
                                    <label class="checkbx-style">Primary
                                        <input autocomplete="off" type="checkbox" name="field[{{$elementNum}}][phone_number][is_primary]" @if(  array_get($field, 'is_primary') ) checked @endif class="is_primary_phone" > 
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="form-group checkbx">
                                    <label class="checkbx-style">Authenticate
                                        <input autocomplete="off" type="checkbox" name="field[{{$elementNum}}][phone_number][is_verify]" @if(  array_get($field, 'is_verify') ) checked @endif  > 
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="form-group checkbx">
                                    <label class="checkbx-style">Allow Copying
                                        <input autocomplete="off" type="checkbox" name="field[{{$elementNum}}][phone_number][is_allow_copy]" @if(  array_get($field, 'is_allow_copy') ) checked @endif value="1"> 
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="form-group checkbx">
                                    <label class="checkbx-style">Allow Multiple
                                        <input autocomplete="off" type="checkbox" class="multienrollmentcheckbox[]" name="field[{{$elementNum}}][phone_number][is_multienrollment]" @if(  array_get($field, 'is_multienrollment') ) checked @endif value="1"> 
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-5 col-md-5 col-lg-5">
                        <div class="tags-display" id="phonenumber-{{ array_get($field, 'id', "$elementNum") }}-tags">
                            <label class="tag-label">Tags</label>
                            <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Phone Number' }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</li>
<!--Phone block-->