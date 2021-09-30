<div class="customer-detail-wrapper" style="display: none">
    <input type="hidden" name="_token" id="customer-token" value="{{ csrf_token() }}">
    <div class="col-sm-12 question-text salesagentintro customer-verify-lead-data-1" style="display: none;">
        <h2 class="agent-title">Customer Verification</h2>
        <P class="customer-welcome-message"></P>
        <P class="customer-telesale-verification-qus"></P>
        <span>Enter Lead ID</span>
        <div class="customer_telesale_verification_id_wrapper">
                  <span class="inline-block">
                    <input id="lead_id" class="form-control verify-lead-ID question-input" value="" name="question[Lead ID]" autocomplete="off">
                  </span>
            <span class="inline-block">
                    <button type="button" id="customer-check-telesale-button" class="customerCheckTelesaleId btn btn-primary">Verify</button>
                <!-- <button type="button" id="telesala-prive" class="btn btn-red">Previous</button> -->
                  </span>
            <div class="customer-verify-status">
                <span id="customer-tele-message"></span>
                <div>
                    <span class="inline-block">
                        <button type="button" class="btn btn-green" id="CustomerLeadNext" style="display: none;">Next</button>
                      </span>
                    <span class="inline-block">

                        <button type="button" class="btn btn-red customer_tele_cannot_verify" id="LeadError" style="display: none;">Cannot Verify</button>
                      </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 question-text salesagentintro customer-verify-lead-data-2" style="display: none;">
        <h2 class="agent-title">Customer Verification</h2>
        <P class="authorized-name-qus"> </P>
        <span class="show-auth-name"></span>
        <div class="client_id_verification_wrapper">
                  {{--<span class="inline-block">
                    <input value="" class="form-control verify-auth-name question-input" name="question[Authorized Name]" autocomplete="off">
                  </span>--}}
            {{--<span class="inline-block">
                    <button type="button" id="checkcleint_button" class="customer-check-auth btn btn-primary">Verify</button>
                  </span>--}}
            <div class="customer-auth-verify-status">
                    <span id="customer-client-message" class="inline-block">
                    </span>
                <div>
                    <span class="inline-block">
                        <button type="button" class="btn btn-red" id="authPre">Previous</button>
                      </span>
                      <span class="inline-block">
                        <button type="button" class="btn btn-green" id="AuthorizedNext">Next</button>
                      </span>
                    <span class="inline-block">
                        <button type="button" class="btn btn-red customer-auth-decline-verify">Decline</button>
                      </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 question-text salesagentintro customer-verify-lead-data-3" style="display: none;">
        <h2 class="agent-title">Customer Verification</h2>
        <P class="account-number-qus"></P>
        <span class="show-account-number"></span>
        <div class="agent_verification_id_wrapper">

            <div class="customer-agent-verify-status">
                <span id="agent-message"></span>
                <div>
                      <span class="inline-block">
                        <button type="button" class="btn btn-red" id="accountPre">Previous</button>
                      </span>
                    <span class="inline-block">
                        <button type="button" class="btn btn-green" id="accountNext">Next</button>
                      </span>
                    <span class="inline-block">
                        <button type="button" class="btn btn-red customer-account-decline-verify">Decline</button></span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 question-text salesagentintro customer-verify-lead-data-4" style="display: none;">
        <h2 class="agent-title">Customer Verification</h2>
        <P class="zipcode-qus"></P>
        <span class="show-zip-code"></span>
        <div class="agent_verification_id_wrapper">
            {{--<input class="form-control verify-zipcode question-input" value="" name="question[zipcode]" autocomplete="off"></span>--}}
            {{-- <span class="inline-block">
                     <button type="button" id="checkcagent_button" class="checkagentid btn btn-primary">Verify</button>
                 <!-- <button type="button" id="checkagent-prive" class="btn btn-red">Previous</button> -->
                   </span>--}}
            <div class="customer-agent-verify-status">
                <span id="agent-message"></span>
                <div>
                    <span class="inline-block">
                            <button type="button" class="btn btn-red" id="zipPre">Previous</button>
                          </span>
                    <span class="inline-block">
                            <button type="button" class="btn btn-green" id="zipcodeNext">Next</button>
                          </span>
                    <span class="inline-block">
                            <button type="button" class="btn btn-red customer-zip-decline-verify">Decline</button></span>
                </div>
            </div>
        </div>
    </div>
</div>
