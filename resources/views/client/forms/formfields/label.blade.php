<!--label block-->
<li id="form_field_wrapper_{{$elementNum}}" class="form-field-wrapper open">
    <div class="single-field-wrapper ">
        <div class="edit-delete-settings">
            <span class="pull-left">Label</span>
            <a href="javascript:void(0)" class="collapsed_icon" rel="{{$elementNum}}">
                <span class="fa fa-fw fa-chevron-up  collapse-setting"></span>
                <span class="fa fa-fw fa-chevron-down expand-setting"></span>
            </a>
            <a class="remove-element" href="javascript:void(0)" rel="{{$elementNum}}">
                <span class="fa fa-fw fa-times"></span>
            </a>
        </div>
        <div class="row" style="margin-top:15px;">
            <div class="col-md-7">
                <div class="settings-wrapper">
                    <div class="single-setting-wrapper">
                        <div class="form-group field-settings">
                            <div class="clearfix"></div>
                            <div class="field-settings">
                                <div class="settings-wrapper">
                                    <div class="single-setting-wrapper">
                                        <label class="">Label</label>

                                        <input type="hidden" name="field[{{$elementNum}}][label][id]" value="{{ array_get($field, 'id', '') }}">
                                        <input class="form-control fieldlabel" attr="label" id="{{ array_get($field, 'id', "$elementNum") }}" name="field[{{$elementNum}}][label][label]" type="Text" value="{{ !empty($field) ? array_get($field, 'label', '') : 'Label' }}">

                                    </div>
                                    

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="tags-display" id="label-{{ array_get($field, 'id', "$elementNum") }}-tags"> 
                <label class="tag-label">Tags</label>
                    <p><span class="question-tag">{{ !empty($field) ? array_get($field, 'label', '') : 'Label' }}</span></p>
                </div>
            </div>
        </div>
    </div>
</li>
<!--label block-->