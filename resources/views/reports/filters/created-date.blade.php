<div class="sor_fil utility-btn-group mr15 margin-bottom-for-filters">
    <div class="search">
        <div class="search-container date-search-container { $errors->has('date_start') ? ' has-error' : '' }}">
            <button for="date_start" type="button">{!! getimage('images/calender.png') !!}</button>
            <input id="date_start" autocomplete="off" type="text" class="form-control" name="date_start">
        </div>
    </div>
</div>