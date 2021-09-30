<div class="btn-group pull-right btn-sales-all margin-bottom-for-filters">
    <select class="select2 btn btn-green dropdown-toggle mr15 " id="brand" name="brand" data-parsley-required='true'  data-parsley-errors-container="#select2-filterbrand-error-message" data-parsley-required-message="Please select brand">
        <option value="" selected>All Brands</option>
        @foreach($brands as $brand)
            <option value="{{$brand->id}}">{{$brand->name}}</option>
        @endforeach
    </select>
    <span id="select2-filterbrand-error-message"></span>
</div>