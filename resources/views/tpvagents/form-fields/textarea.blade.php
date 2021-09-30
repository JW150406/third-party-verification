<div class="form-group" rel="label">
    <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
    <textarea
        class="form-control"
        name="fields_{{$teleSalesDataId['value'] ?? ''}}"
        placeholder="{{ $field['label'] }}"
        data-parsley-trigger="focusout"
        data-parsley-required='true' 
        @if(!empty($field->regex))
            data-parsley-pattern="{{$field->regex}}"
        @endif
        @if(!empty($field->regex_message))
            data-parsley-pattern-message="{{$field->regex_message}}" 
        @endif 
    >
    {{$teleSalesData['value'] ?? ''}}
    </textarea>
</div>