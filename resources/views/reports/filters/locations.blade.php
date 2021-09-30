<div class="btn-group pull-right btn-sales-all margin-bottom-for-filters">
    <div class="update_client_by_location">
        <select class="select2 btn btn-green dropdown-toggle mr15" id="location" name="location" @if(Auth::user()->isLocationRestriction()) disabled @endif>
            <option value="" selected>All Locations</option>
        </select>
    </div>
</div>