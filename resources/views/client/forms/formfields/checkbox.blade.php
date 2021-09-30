<!--Checkbox block-->
<li id="form_field_wrapper_{{$elementNum}}" class="form-field-wrapper open">
    <div class="single-field-wrapper ">
        <div class="edit-delete-settings">
            <span class="pull-left">Checkbox</span>
            <a href="javascript:void(0)" class="collapsed_icon" rel="{{$elementNum}}">
                <span class="fa fa-fw fa-chevron-up  collapse-setting"></span>
                <span class="fa fa-fw fa-chevron-down expand-setting"></span>
            </a>
            <a class="remove-element" href="javascript:void(0)" rel="{{$elementNum}}">
                <span class="fa fa-fw fa-times"></span>
            </a>
        </div>
        <input name="" type="hidden" value="name">
        <div class="form-group field-settings">
            <div class="field-settings">
                <div class="clearfix"></div>

                <div class="row">
                    <div class="col-xs-12 col-sm-7 col-md-7 col-lg-7">
                        <div class="settings-wrapper">
                            <div class="single-setting-wrapper">
                                <label class="">Label</label>
                                <input class="form-control fieldlabel" attr="checkbox" id="{{ array_get($field, 'id', "$elementNum") }}" name="field[{{$elementNum}}][checkbox][label]" type="Text" value="{{ !empty($field) ? array_get($field, 'label', '') : 'Checkbox Label' }}">
                                <input type="hidden" name="field[{{$elementNum}}][checkbox][id]" value="{{ array_get($field, 'id', '') }}">
                            </div>
                            <div class="single-setting-wrapper">
                                <label class="">Settings</label>
                                <div class="form-group checkbx">
                                    <label class="checkbx-style">Required
                                        <input autocomplete="off" type="checkbox" name="field[{{$elementNum}}][checkbox][is_required]" @if(array_get($field, 'is_required' )) checked @endif>
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="form-group checkbx">
                                    <label class="checkbx-style">Allow Multiple
                                        <input autocomplete="off" type="checkbox" class="multienrollmentcheckbox[]" name="field[{{$elementNum}}][checkbox][is_multienrollment]" @if(array_get($field, 'is_multienrollment' )) checked @endif>
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <label class="">Options</label>
                                <div class="radio-options">
                                    <ul class="options">
                                        @if (array_get($field, 'meta'))
                                        @if (array_get($field->meta, 'options'))
                                        @foreach (array_get($field->meta, 'options') as $option)
                                        <li class="select_option_5_23">
                                            <span class="middle-span-field-wrapper">
                                                <input type="text" value="{{ array_get($option, 'option', '') }}" name="field[{{$elementNum}}][checkbox][meta][options][]" class="form-control option_value_alter">
                                            </span>
                                            <span>
                                                <a href="javascript:void(0);" class="remove_options" ref="select_option_5_23">
                                                    <i class="fa fa-fw fa-times"></i>
                                                </a>
                                            </span>
                                        </li>
                                        @endforeach
                                        @else
                                        <li class="select_option_5_23">
                                            <span class="middle-span-field-wrapper">
                                                <input type="text" name="field[{{$elementNum}}][checkbox][meta][options][]" class="form-control option_value_alter">
                                            </span>
                                            <span>
                                                <a href="javascript:void(0);" class="remove_options" ref="select_option_5_23">
                                                    <i class="fa fa-fw fa-times"></i>
                                                </a>
                                            </span>
                                        </li>
                                        @endif
                                        @else
                                        <li class="select_option_5_23">
                                            <span class="middle-span-field-wrapper">
                                                <input type="text" name="field[{{$elementNum}}][checkbox][meta][options][]" class="form-control option_value_alter">
                                            </span>
                                            <span>
                                                <a href="javascript:void(0);" class="remove_options" ref="select_option_5_23">
                                                    <i class="fa fa-fw fa-times"></i>
                                                </a>
                                            </span>
                                        </li>
                                        @endif
                                    </ul>
                                    <div class="text-center">
                                        <a class="add_checkbox_option btn btn-green" href="javascript:void(0);" rel="{{$elementNum}}">
                                            Add new option
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-5 col-md-5 col-lg-5">
                        <div class="tags-display" id="checkbox-{{ array_get($field, 'id', "$elementNum") }}-tags">
                            <label class="tag-label">Tags</label>
                            <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'Checkbox Label' }}</span></p>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</li>
<!--Checkbox block-->