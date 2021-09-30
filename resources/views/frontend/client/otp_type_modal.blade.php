<div class="modal fade" id="select-otp-type-modal" data-parsley-validate>
    
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h4>Select Verification Method</h4></div>
                <form type="post" id="frm-otp-type">
                <div class="modal-body" style="text-align: center;">
                    <input type="hidden" name="childLead" class="childLeadValue" value="">
                    <div class="form-group" rel="label">
                        <label class="control-label"></label>
                        <div class="form-group radio-btns pdt0">
                            <label class="radio-inline otp-popup" style="margin-right: 15px;"><input class=""type="radio" name="otp_type" value="sms" checked="checked" data-parsley-required='true' data-parsley-errors-container="#otp-error-container" />SMS</label>
                            <label class="radio-inline otp-popup"><input type="radio" name="otp_type" value="voice"  />Voice</label>
                        </div>
                        <p id="otp-error-container"></p>
                    </div>
                </div>

                <div class="modal-footer remove-top-border">
                    <div class="btnintable bottom_btns pd0">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-green">Send OTP</button>
                            <button type="button" class="btn btn-red" data-dismiss="modal" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
</div>
