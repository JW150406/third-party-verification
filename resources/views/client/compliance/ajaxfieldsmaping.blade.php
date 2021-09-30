<table class="table table-striped">
  <tr>
    <th>Header</th>
    <th>Values</th>
    <th>Custom</th>
    <th></th>
  </tr>
  @if(count($header_options) > 0)
   <?php $i = 0;?>
    @foreach ($header_options as   $headeroption )
     @if(!empty($headeroption))
        <tr class="options_row options_row_{{$i}}">
          <td valign="middle" class="valign-middle"> {{$headeroption}}
             <input type="hidden" value="{{$headeroption}}" name="header_column[header][]">
          </td>
          <td valign="middle" class="valign-middle">
            <select class="options_values_for_compliance select_form_options_{{$i}}" name='header_column[values][]'><?php echo $elements_in_form; ?></select>
            <input type="text" class="form-control  input_form_options_{{$i}}" name="header_column[custom_value][]" value="" style="display:none;">

          </td>
          <td valign="middle" class="valign-middle">
            <input type="hidden" class="allow_custom_check_{{$i}}" name="header_column[allow_custom][]" value="">
            <label class="checkbx-style">
            <input type="checkbox" name="checkforcustom" class="checkforcustomvalue" value="" data-ref="{{$i}}" style="width: auto;">
																		  <span class="checkmark"></span>
																	</label>
           

          </td>
          <td valign="middle" class="valign-middle">
            <a href="javascript:void(0);" class="remove_compliance_option" data-rel="options_row_{{$i}}"><?php echo getimage('images/cancel.png'); ?></a></td>
        </tr>
        <?php $i++;?>
        @endif
    @endforeach
  @endif
</table>
