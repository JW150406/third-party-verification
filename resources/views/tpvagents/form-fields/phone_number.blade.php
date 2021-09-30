<div class="form-group" rel="label">
    <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
    <input type="text"
        autocomplete="new-password"
        class="form-control mobile"
        name="fields_{{$teleSalesDataId['value'] ?? ''}}"
        placeholder="{{ $field['label'] }}"
        data-parsley-trigger="focusout"
        data-parsley-pattern="[0-9]{10}"
        data-parsley-pattern-message="Please enter 10 digit mobile number"
        data-parsley-required='true'
        data-parsley-required-message="Please enter phone number" 
        value="{{$teleSalesData['value'] ?? ''}}" 
    >
</div>