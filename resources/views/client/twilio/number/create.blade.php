<!-- Add client user Modal Starts -->

<div class="team-addnewmodal v-star">
    <div class="modal fade" id="number-create-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add Phone Number</h4>
                </div>
                <div class="ajax-error-message"></div>
                <div class="modal-body">
                    <div class="modal-form row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <form id="number-create-form" role="form" method="POST" action="{{route('twilio.saveNumber',$client_id)}}" data-parsley-validate >
                                @csrf
                                <input type="hidden" name="id" id="number_id">
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="twilio_type" class="yesstar">Type</label>
                                        <select class="select2 form-control" id="twilio_type" name="type"
                                                data-parsley-required='true' data-parsley-errors-container="#select2-twiliotype-error-message">
                                            <option value="" selected>Select</option>
                                            @if(isOnSettings($client_id,'is_enable_agent_tpv_num'))
                                            <option value="customer_verification" >Live TPV - Telesales Warm Transfer</option>
                                            @endif
                                            @if(isOnSettings($client_id,'is_enable_cust_call_num'))
                                            <option value="customer_call_in_verification" >Live TPV - Customer Call In</option>
                                            @endif
                                            @if(isOnSettings($client_id,'is_enable_ivr'))
                                            <option value="ivr_tpv_verification" >IVR TPV</option>
                                            @endif
                                        </select>
                                        <span id="select2-twiliotype-error-message"></span>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="twillio_number" class="yesstar">Phone  Number</label>
                                        <input type="text" class="form-control" name="phone_number" id="twilio_number"
                                        placeholder="Please enter phone number."
                                       data-parsley-trigger="focusout" data-parsley-required='true' 
                                       data-parsley-pattern="{{ config()->get('constants.PHONE_NUMBER_VALIDATION_REGEX') }}"
                                               data-parsley-pattern-message="Invalid phone number, Please make sure that number start with 1 and contains at least 10 digits."
                                       >
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12" id="workflow_outer">
                                    <div class="form-group">
                                        <label for="workflow_name" class="yesstar">Workflow</label>
                                        <select class="select2 form-control" id="twilio_workflow" name="workflow"
                                                data-parsley-trigger="focusout" data-parsley-required='true' data-parsley-errors-container="#select2-twilioworkflow-error-message">
                                            <option value="" selected></option>
                                        </select>
                                        <span id="select2-twilioworkflow-error-message"></span>

                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
                                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                        <div class="btn-group">
                                            <button type="submit" class="btn btn-green"><span class="save-text">Save</span></button>
                                            <button type="button" class="btn btn-red number-cancel" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- add client user Modal ends -->


@push('scripts')
<script>
    $('#number-create-modal').on('hidden.bs.modal', function () {
        $('#number-create-form').parsley().reset();
    });
    
    $(document.body).on("change", "#twilio_type", function(){
        if ($(this).val() == "ivr_tpv_verification") {
            $("#workflow_outer").hide();
            $("#twilio_workflow").attr('data-parsley-required', false);
        } else {
            $("#workflow_outer").show();
            $("#twilio_workflow").attr('data-parsley-required', true);
        }
    });
    
</script>
@endpush
