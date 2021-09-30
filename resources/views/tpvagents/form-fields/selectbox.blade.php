<div class="form-group" rel="label">
    <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
    <select name="fields_{{$teleSalesDataId['value'] ?? ''}}"
            class="select2 form-control"
            title="Please enter {{strtolower(array_get($field, 'label'))}}"
            data-parsley-trigger="focusout"
            data-parsley-errors-container="#error_{{array_get($field, 'id')}}" 
            data-parsley-required='true' >
        @foreach($field->meta as $mVal)
            @foreach($mVal as $option)
                <option value="{{$option['option']}}" 
                @if(isset($teleSalesData['value']) && $teleSalesData['value'] == $option['option']) 
                    selected
                @endif
                >{{ $option['option'] }}</option>
            @endforeach
        @endforeach
    </select>
    <div id="error_{{array_get($field, 'id')}}"></div>
</div>