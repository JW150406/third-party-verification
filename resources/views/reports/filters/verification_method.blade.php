<div class="btn-group pull-right btn-sales-all margin-bottom-for-filters">
    <select class="select2 btn btn-green dropdown-toggle mr15 " id="verification_method" name="verification_method" data-parsley-required='true'  data-parsley-errors-container="#select2-filterbrand-error-message" data-parsley-required-message="Please select Method">
        <option value="" selected>All Methods</option>
        @foreach(config()->get('constants.VERIFICATION_METHOD_FOR_DISPLAY') as $k => $v)
        @if($k == 3)
        @php $v = 'Self Verification (Email)'; @endphp
        @endif
        @if($k == 4)
        @php $v = 'Self Verification (SMS)'; @endphp
        @endif
            <option value="{{$k}}">{{$v}}</option>
        @endforeach
    </select>
    <span id="select2-filtermethod-error-message"></span>
</div>