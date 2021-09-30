<div class="form-group" rel="label">
    <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
    @foreach($field->meta as $meta)
        @foreach($meta as $option)
            <div class="form-group radio-btns pdt0">
                <label class="radio-inline">
                    <input type="radio"
                        name="fields_{{$teleSalesDataId['value'] ?? ''}}"
                        value="{{ $option['option'] }}"
                        data-parsley-trigger="focusout" 
                        data-parsley-required='true'
                        data-parsley-errors-container="#error_{{array_get($field, 'id')}}" 
                        @if(isset($teleSalesData['value']) && $teleSalesData['value'] == $option['option'])
                            checked 
                        @endif
                    > {{ $option['option'] }}
                </label>
            </div>
        @endforeach
        <div id="error_{{array_get($field, 'id')}}"></div>
    @endforeach
</div>