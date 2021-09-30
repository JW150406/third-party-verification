<div class="form-group" rel="label">
    <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
    @php 
        $selected = explode(',',$teleSalesData['value']);
    @endphp
    @foreach($field->meta as $meta)
        @foreach($meta as $option)
            <div class="form-group checkbx">
                <label class="checkbx-style">
                    <input type="checkbox"
                        name="fields_{{$teleSalesDataId['value'] ?? ''}}[]"
                        value="{{ $option['option'] }}"
                        data-parsley-trigger="focusout" 
                        data-parsley-required='true'
                        data-parsley-errors-container="#error_{{array_get($field, 'id')}}" 
                        @if(is_array($selected) && in_array($option['option'],$selected))
                            checked 
                        @endif
                    > {{ $option['option'] }}
                    <span class="checkmark"></span>
                </label>
            </div>
        @endforeach
        <div id="error_{{array_get($field, 'id')}}"></div>
    @endforeach
</div>