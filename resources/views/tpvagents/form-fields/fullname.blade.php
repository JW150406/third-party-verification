<div class="form-group" rel="label">
    <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
    <div class="row">
        <div class="col-sm-4">
            <input class="form-control"
            type="text"
            name="fields_{{$teleSalesDataId['first_name'] ?? ''}}"
            placeholder="First Name"
            autocomplete="new-password"
            data-parsley-trigger="focusout"
            data-parsley-required='true'
            data-parsley-required-message="Please enter first name" 
            value="{{$teleSalesData['first_name'] ?? ''}}">
        </div>
        <div class="col-sm-4">
            <input type="text"
            class="form-control"
            name="fields_{{$teleSalesDataId['middle_initial'] ?? ''}}"
            placeholder="Middle Name"
            autocomplete="new-password"
            value="{{$teleSalesData['middle_initial'] ?? ''}}">
        </div>
        <div class="col-sm-4">
            <input type="text"
            class="form-control"
            name="fields_{{$teleSalesDataId['last_name'] ?? ''}}"
            placeholder="Last Name"
            autocomplete="new-password"
            data-parsley-trigger="focusout" 
            data-parsley-required='true'
            data-parsley-required-message="Please enter last name" 
            value="{{$teleSalesData['last_name'] ?? ''}}" 
            >
        </div>
    </div>
</div>