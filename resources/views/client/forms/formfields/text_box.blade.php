<!--textbox block-->
<li id="form_field_wrapper_{{$elementNum}}" class="form-field-wrapper open">
    <div class="single-field-wrapper ">
        <div class="edit-delete-settings">
            <span class="pull-left">Textbox</span>
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
                                <input type="hidden" name="field[{{$elementNum}}][textbox][id]"id="textbox-hidden" value="{{ array_get($field, 'id', '') }}">
                                <input class="form-control fieldlabel textbox-label" attr = "textbox" id="{{ array_get($field, 'id', "$elementNum") }}" name="field[{{$elementNum}}][textbox][label]" type="Text" value="{{ !empty($field) ? array_get($field, 'label', '') : 'Textbox label' }}" >
                            </div>
                            <div class="single-setting-wrapper">
                                <?php $meta = array_get($field, 'meta'); ?>
                                <label class="">Placeholder</label>
                                <input class="form-control placeholder" name="field[{{$elementNum}}][textbox][meta][placeholder]" value="{{ array_get($meta, 'placeholder', 'Textbox Placeholder') }}" type="Text">
                            </div>
                            <div class="single-setting-wrapper">
                                <label class="">Settings</label>
                                <div class="clearfix"></div>
                                <div class="form-group checkbx">
                                    <label class="checkbx-style"  >Required
                                        <input autocomplete="off" type="checkbox" name="field[{{$elementNum}}][textbox][is_required]" @if(array_get($field, 'is_required')) checked @endif > 
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="form-group checkbx">
                                    <label class="checkbx-style">Allow Copying
                                        <input autocomplete="off" type="checkbox" name="field[{{$elementNum}}][textbox][is_allow_copy]" @if(  array_get($field, 'is_allow_copy') ) checked @endif value="1"> 
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="form-group checkbx">
                                    <label class="checkbx-style">Auto Caps
                                        <input autocomplete="off" type="checkbox" name="field[{{$elementNum}}][textbox][is_auto_caps]" @if(  array_get($field, 'is_auto_caps') ) checked @endif value="1"> 
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="form-group checkbx">
                                    <label class="checkbx-style">Allow Multiple
                                        <input autocomplete="off" type="checkbox" class="multienrollmentcheckbox[]" name="field[{{$elementNum}}][textbox][is_multienrollment]" @if(  array_get($field, 'is_multienrollment') ) checked @endif value="1"> 
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="single-setting-wrapper validation-section">
                                <label class="">Validations</label>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label class="" >RegEx (Regular Expression)</label>
                                        <input autocomplete="off" type="text" name="field[{{$elementNum}}][textbox][regex]" id="field_textbox_regex_{{$elementNum}}" value="{{array_get($field, 'regex')}}" >

                                </div>
                                <div class="form-group">
                                    <label class="" >RegEx Error Message</label>
                                    <input autocomplete="off" type="text" name="field[{{$elementNum}}][textbox][regex_message]" value="{{array_get($field, 'regex_message')}}"
                                    data-parsley-validate-if-empty="true" data-parsley-required-if="#field_textbox_regex_{{$elementNum}}" >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-5 col-md-5 col-lg-5">
                    
                        <div class="tags-display" id="textbox-{{ array_get($field, 'id', "$elementNum") }}-tags">
                            <label class="tag-label">Tags</label>
                            <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Textbox label' }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</li>
<!--textbox block-->
