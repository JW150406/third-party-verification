<div class="btn-group pull-right btn-sales-all margin-bottom-for-filters">
    <select class="select2 btn btn-green dropdown-toggle mr15 " id="sales_center" name="sales_center" @if(Auth::user()->hasAccessLevels('salescenter')) disabled @endif>
    @if(Auth::user()->hasAccessLevels('salescenter')) 
    <option value=""></option>
    @else
        <option value="" selected>All Sales Centers</option>
    @endif

    </select>
</div>