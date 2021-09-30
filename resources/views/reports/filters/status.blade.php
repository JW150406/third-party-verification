<div class="btn-group pull-right btn-sales-all margin-bottom-for-filters">
    <div class="update_client_by_location">
        <select class="select2 btn btn-green dropdown-toggle selectclientlocations_report" id="status" name="status">
           <option value="" selected>All status</option>
            <option value="cancel">{{config()->get('constants.VERIFICATION_STATUS_CHART.'.ucfirst("cancel"))}}</option>
            <option value="decline">{{config()->get('constants.VERIFICATION_STATUS_CHART.'.ucfirst("decline"))}}</option>
            <option value="hangup">{{config()->get('constants.VERIFICATION_STATUS_CHART.'.ucfirst("hangup"))}}</option>
            <option value="expired">{{config()->get('constants.VERIFICATION_STATUS_CHART.'.ucfirst("expired"))}}</option>
            <option value="pending">{{config()->get('constants.VERIFICATION_STATUS_CHART.'.ucfirst("pending"))}}</option>
            <option value="self-verified">{{config()->get('constants.VERIFICATION_STATUS_CHART.Self-verified')}}</option>
            <option value="verified">{{config()->get('constants.VERIFICATION_STATUS_CHART.'.ucfirst("verified"))}}</option>
        </select>
    </div>
</div>