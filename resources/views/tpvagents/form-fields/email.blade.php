<div class="form-group" rel="label">
    <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
    <input type="email"
        autocomplete="new-password"
        class="form-control email {{ array_get($field, 'is_required' )==1 ? 'required' : '' }}"
        name="fields_{{$teleSalesDataId['value'] ?? ''}}"
        placeholder="{{ $field['label'] }}"
        data-parsley-trigger="focusout"
        data-parsley-pattern="/\S+@\S+\.\S+/"
        data-parsley-pattern-message="Please enter valid email address"
        data-parsley-required='true'
        data-parsley-required-message="Please enter email" 
        value="{{$teleSalesData['value'] ?? ''}}" 
    >
</div>  