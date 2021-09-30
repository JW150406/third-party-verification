<div class="btn-group pull-right btn-sales-all margin-bottom-for-filters">
    <select class="select2 btn btn-green dropdown-toggle mr15 " id="channel" name="channel" data-parsley-required='true'  data-parsley-errors-container="#select2-filterchannel-error-message" data-parsley-required-message="Please select client">
        <option value="" selected>All Channels</option>
        <option value="d2d" >{{config()->get('constants.DASHBOARD_CHANNEL_CATEGORIES_FOR_DISPLAY.d2d')}}</option>
        <option value="tele" >{{config()->get('constants.DASHBOARD_CHANNEL_CATEGORIES_FOR_DISPLAY.tele')}}</option>
    </select>
    <span id="select2-filterchannel-error-message"></span>
</div>