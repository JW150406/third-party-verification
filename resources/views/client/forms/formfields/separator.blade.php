<!-- separator block-->
<li id="form_field_wrapper_{{$elementNum}}" class="form-field-wrapper open separator-section">
   <div class="single-field-wrapper"> 
       <div class="edit-delete-settings">
          <span class="pull-left">Separator</span>
           <a href="javascript:void(0)" class="collapsed_icon" rel="{{$elementNum}}">
               <span class="fa fa-fw fa-chevron-up  collapse-setting"></span> 
               <span class="fa fa-fw fa-chevron-down expand-setting"></span>
            </a> 
            <a class="remove-element" href="javascript:void(0)" rel="{{$elementNum}}">
                <span class="fa fa-fw fa-times"></span>
            </a>
        </div>
        <input type="hidden" name="field[{{$elementNum}}][separator][id]" value="{{ array_get($field, 'id', '') }}">
        <input name="field[{{$elementNum}}][separator][label]" type="hidden" value="separator">
        <div class="pagebreak-text-main field-settings">
            <span class="separator-name"> </span>
        </div>
        <div class="clearfix"></div>
        <input type="hidden" class="required_field" name="" value="12">
        <input type="hidden" class="required_field" name="">
        <input class="form-control fieldlabel" name="" type="hidden" value="" rel="3">
        <input class="form-control placeholder" name="" type="hidden" rel="3">
    </div> 
</li>
<!-- separator block-->