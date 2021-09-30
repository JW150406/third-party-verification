<!--heading block-->
<li id="form_field_wrapper_{{$elementNum}}" class="form-field-wrapper open separator-section">
    <div class="single-field-wrapper">
        <div class="edit-delete-settings">
            <span class="pull-left">Heading</span>
            <a href="javascript:void(0)" class="collapsed_icon" rel="{{$elementNum}}">
                <span class="fa fa-fw fa-chevron-up  collapse-setting"></span>
                <span class="fa fa-fw fa-chevron-down expand-setting"></span>
            </a>
            <a class="remove-element" href="javascript:void(0)" rel="{{$elementNum}}">
                <span class="fa fa-fw fa-times"></span>
            </a>
        </div>
        <!-- <div class="pagebreak-text-main">
            <span class="control_label_4 ">heading</span>
        </div> -->
        <div class="clearfix"></div>

        <div class="field-settings">
            <div class="row">
                <div class="col-xs-12 col-sm-7 col-md-7 col-lg-7">
                    <div class="settings-wrapper">
                        <div class="single-setting-wrapper head-text-mt">
                            <div class=" field-settings">
                                <label> Heading text </label>
                                <input type="hidden" name="field[{{$elementNum}}][heading][id]" value="{{ array_get($field, 'id', '') }}">
                                <input class="form-control fieldlabel"  attr ="heading" id ="{{ array_get($field, 'id', "$elementNum") }}" name="field[{{$elementNum}}][heading][label]" type="text" value="{{ !empty($field) ? array_get($field, 'label', '') : 'heading text' }}">
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-5 col-md-5 col-lg-5">
                    <div class="tags-display" id ="heading-{{ array_get($field, 'id', "$elementNum") }}-tags">
                        <label class="tag-label">Tags</label>
                        <p><span class="question-tag">  {{ !empty($field) ? array_get($field, 'label', '') : 'heading text' }}</span></p>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

</li>
<!--heading block-->