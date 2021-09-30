@extends('layouts.tpvagent')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <?php
    $disposition_id = "";
    ?>
    <audio id="incomingAudio" src="{{asset('twilio/incoming.mp3')}}" preload="auto" loop>
    </audio>
    <div class="container-wrapper">

        <div class="row">
            <div class="col-md-9 col-sm-9">
                <div class="panel panel-default">


                    <div class="panel-body marginbtm270">

                        <!-- Display Validation Errors -->
                        <div class="saleupdatenotification"></div>
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        <div class="message"></div>

                        <form class="form-horizontal" id="verify_agent_lead" enctype="multipart/form-data" role="form" method="GET" action="">
                            <div class="new_telesale_reference text-success"></div>

                            {{ csrf_field() }}
                            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                            <input type="hidden" value="" name="agent_client_id" id="agent_client_id">
                            <input type="hidden" value="" name="agent_user_id" id="agent_user_id">
                            <input type="hidden" value="" name="telesale_id" id="telesale_reference_id">
                            <input type="hidden" value="" name="telesale_form_id" id="telesale_form_id">
                            <input type="hidden" value="" name="form_worksid" id="form_worksid">
                            <input type="hidden" value="" name="form_workflid" id="form_workflid">
                            <input type="hidden" value="" name="current_lang" id="current_lang">
                            <input type="hidden" value="" name="form_id" id="current_form_id">
                            <input type="hidden" value="" name="leadzipcodestate" id="leadzipcodestate">
                            <input type="hidden" value="" name="leadcommodity" id="leadcommodity">
                            <input type="hidden" value="" name="cloned" id="allow_cloning">
                            <input type="hidden" value="" name="call_customer_hangs_up" id="call_customer_hangs_up">
                            <input type="hidden" value="" name="call_customer_lead_verify" id="call_customer_lead_verify">
                            <div class="text-center waiting-for-call">
                                <h3 class="text-center available-call-time"> </h3>
                                <h3 class="waiting-for-call-status"><Button type = "button" class="btn btn-green ReadyBtn">I am Ready!</Button></h3>
                            </div>
                            <div class="customer-detail-wrapper-Qus">
                            </div>
                            <div class="sale-detail-wrapper-Qus">
                            </div>                            

                            <div class="client-verification-verify-data text-center" style="display: none">
                                <h3 class="agent-title mt30"> Client cannot be verified</h3>
                                <p class="client-agent-not-found-data can-not-verify-script"></p>
                            </div>

                            <div class="tele-verification-verify-data text-center" style="display: none">
                                <h3 class="agent-title mt30"> Lead ID cannot be verified</h3>
                                <p class="tele-agent-not-found-data can-not-verify-script"></p>
                            </div>

                            <div class="agent-verification-verify-data text-center" style="display: none">
                                <h3 class="agent-title mt30"> Agent cannot be verified</h3>
                                <p class="agent-agent-not-found-data can-not-verify-script"></p>
                            </div>

                            <!-- <div class="telesale-verification-verify-data text-center" style="display: none">
                                <h3 class="agent-title mt30"> Lead ID cannot be verified</h3>
                                <p class="telesale-agent-not-found-data"></p>
                            </div> -->
                            <div class="can-not-transfer text-center" style="display: none">
                                <h3 class="agent-title mt30"> Can’t Transfer to Customer</h3>
                                <p class="can-not-transfer-data can-not-verify-script"></p>
                            </div>

                            <!---Verification Complete--block------>
                            <div class="verification-complete-block text-center" style="display: none">
                            <!-- <div class="verify-done-img mt20"><?php //echo getimage('/images/lead-success.png') ?></div> -->
                                <h3 class="agent-title mt30">Verification complete</h3>
                                <p class="verification_complete"></p>
                                <!-- <button type="button" class="btn btn-green mt30">Go Online</button> -->
                            </div>
                            <!----end--- Verification Complete---block---->

                            <div class="sale-detail-wrapper">
                                <h2 class="call_hangup_or_dropped" style="display: none;">Call disconnected</h2>
                                <div class="col-sm-12 question-text salesagentintro verify-lead-data-1" style="display: none;">
                                    <h2 class="agent-title">Sales Agent Verification</h2>
                                    <P class="alesagentintro-qus"> </P>
                                    <span>Enter Client ID</span>
                                    <div class="client_id_verification_wrapper">
                  <span class="inline-block">
                    <input value="" class="form-control verify-client-ID question-input" name="question[Client ID]" autocomplete="off">
                  </span>
                                        <span class="inline-block">
                    <button type="button" id="checkcleint_button" class="checkcleint_id btn btn-primary">Verify</button>
                  </span>
                                        <div class="client-verify-status">
                    <span id="client-message" class="inline-block">
                    </span>
                                            <div>
                      <span class="inline-block">
                        <button type="button" class="btn btn-green" id="clientNext" style="display: none;">Next</button>
                      </span>
                                                <span class="inline-block">
                        <button type="button" class="btn btn-red client_cannot_verify" id="clientError" style="display: none;">Cannot Verify</button>
                      </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12 question-text salesagentintro verify-lead-data-2" style="display: none;">
                                    <h2 class="agent-title">Sales Agent Verification</h2>
                                    <P class="salesagentin-qus"></P>
                                    <span>Enter Agent ID</span>
                                    <div class="agent_verification_id_wrapper">
                  <span class="inline-block">
                    <input class="form-control verify-agent-ID question-input" value="" name="question[Agent ID]" autocomplete="off"></span>
                                        <span class="inline-block">
                    <button type="button" id="checkcagent_button" class="checkagentid btn btn-primary">Verify</button>
                                            <!-- <button type="button" id="checkagent-prive" class="btn btn-red">Previous</button> -->
                  </span>
                                        <div class="agent-verify-status">
                                            <!-- <span class="text-danger">Agent not found</span>
                                            <div class="verify-btn agent-verify-error">
                                              <p class="text-danger">Please enter a correct agent ID or the verification cannot proceed.</p>
                                              <button type="button" class="btn btn-red">Cannot Verify</button>
                                            </div> -->
                                            <span id="agent-message"></span>
                                            <div>
                      <span class="inline-block">
                        <button type="button" class="btn btn-red" id="agentPre">Previous</button>
                      </span>
                                                <span class="inline-block">
                        <button type="button" class="btn btn-green" id="agentNext" style="display: none;">Next</button>
                      </span>
                                                <span class="inline-block">
                        <button type="button" class="btn btn-red agent_cannot_verify" id="agentError" style="display: none;">Cannot Verify</button>
                      </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12 question-text salesagentintro verify-lead-data-3" style="display: none;">
                                    <h2 class="agent-title">Sales Agent Verification</h2>
                                    <P class="telesale-verification-qus"></P>
                                    <span>Enter Lead ID</span>
                                    <div class="telesale_verification_id_wrapper">
                                        <span class="inline-block">
                                            <input id="telesale_id" class="form-control verify-telesale-ID question-input" value="" name="question[Telesale ID]" autocomplete="off">
                                        </span>
                                        <span class="inline-block">
                                            <button type="button" id="check-telesale-button" class="checktelesaleid btn btn-primary">Verify</button>
                                            <!-- <button type="button" id="telesala-prive" class="btn btn-red">Previous</button> -->
                                        </span>
                                        <div class="telesale-verify-status">
                                            <span id="tele-message"></span>
                                            <div>
                                                <span class="inline-block">
                                                    <button type="button" class="btn btn-red" id="telePre">Previous</button>
                                                </span>
                                                <span class="inline-block">
                                                    <button type="button" class="btn btn-green" id="teleNext" style="display: none;">Next</button>
                                                </span>
                                                <span class="inline-block">

                                                    <button type="button" class="btn btn-red tele_cannot_verify" id="teleError" style="display: none;">Cannot Verify</button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 question-text salesagentintro verify-lead-data-4" style="display: none;">
                                    <h2 class="agent-title">Sales Agent Verification</h2>
                                    <P class="procced-can-not-trans-qus"></P>
                                    <div class="telesale_verification_id_wrapper">
                                        <span class="inline-block">
                                            <button type="button" id="proceed-btn" class="btn btn-primary">Proceed</button>
                                        </span>
                                        <span class="inline-block">
                                            <button type="button" id="cannot-transfer-btn" class="btn btn-red">Can’t Transfer</button>
                                        </span>
                                        <!-- <div class="telesale-verify-status">
                                            <div>
                                                <span class="inline-block">
                                                    <button type="button" class="btn btn-red" id="telePre">Previous</button>
                                                </span>
                                                <span class="inline-block">
                                                    <button type="button" class="btn btn-green" id="teleNext" style="display: none;">Next</button>
                                                </span>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="col-sm-3 agent-detail-wrapper scrollbar-inner">

                            </div> -->

                            <div class="script-important-buttons ">
                                <!-- <button type="button" style="display:none" class="agent_not_found btn btn-danger" >Agent Not Found</button> -->
                                {{--<button type="button" style="display:none" class="telesale_not_found btn btn-danger">Telesale Not Found Create New</button>--}}
                                <button type="button" style="display:none" class="create-telesale btn btn-success">Submit new lead</button>
                                <!-- <button type="button" style="display:none" class="btn btn-danger decline-form">Decline</button> -->
                                {{--<button type="button" class="script_for_confirmation btn btn-success salesagentintro" style="display:none;">Next</button>--}}

                                <a style="display:none" data-target="#confirmreview" data-toggle="modal" href="javascript:void(0);" class="btn btn-success verify-sale">Verify</a>&nbsp;&nbsp;
                            </div>
                        </form>

                        <form class="form-horizontal decline-sale-form" id="decline-sale-form" enctype="multipart/form-data" role="form" method="GET" action="" onSubmit="return false;" style="display:none;margin-top:30px">
                            <br />
                            <br />
                            <div class="reason-decline-msg">
                            </div>
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="name" class="control-label inline-block blk-reason" style="padding-top: 2px;    float: left;    padding-left: 15px;"> Reason</label>
                                <div class="col-md-6">
                                    <ul class="list-inline blk-reason">

                                        @if(count($dispositions) > 0)

                                            @foreach($dispositions as $singledisposition)
                                                <li class="decline_dispositions">
                                                    <span class="radio-inline"><input type="radio" name="reason" class="getreason_for_decline @if($singledisposition->allow_cloning == 'true') clone_lead @endif" value="{{$singledisposition->id}}"></span>
                                                    <span> {{$singledisposition->description}}</span>
                                                </li>
                                            @endforeach
                                        @endif
                                        @if(count($hangup_dispositions) > 0)
                                            <?php $j = 0 ?>
                                            @foreach($hangup_dispositions as $single_hangup_disposition)
                                                <li class="hangup_dispositions ">
                                                    <span class="radio-inline"><input type="radio" name="reason" class="getreason_for_decline" value="{{$single_hangup_disposition->id}}"></span>
                                                    <span> {{$single_hangup_disposition->description}}</span>
                                                </li>
                                                <?php $j++ ?>
                                            @endforeach
                                        @endif

                                        <li>
                                            <span class="radio-inline"><input type="radio" name="reason" class="getreason_for_decline" value="other"></span>
                                            <span>Other</span>
                                        </li>
                                    </ul>
                                    <input type="hidden" id="decline_reason" name="decline_reason" value="Incomplete information" class="form-control decline_reason">

                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group col-md-9">

                                    <button type="submit" class="btn btn-danger decline-confirm" style="margin-top:10px; margin-left:15px;">
                                        Decline
                                    </button>
                                    <button type="button" class="btn btn-danger save-and-clone" style="margin-top:10px; margin-left:15px;display:none">
                                        Save and Clone
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="modal fade confirmation-model incoming-call" id="incall">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body text-center">
                                        <div class="mt15 text-center mb15">
                                            <span class="income-call-img"><?php echo getimage('/images/incoming-call.gif') ?></span>
                                            <h4 id="call-type"></h4>
                                            <h4 class="incoming_call_number"></h4>
                                            <p class="incoming_client_name"></p>
                                            <p class="call_at_number"></p>
                                            <p class="call_language" style="display: none;"></p>
                                            <p class="call_cust" style="display: none;"></p>
                                        </div>

                                        <div class="modal-footer">
                                            <div class="btnintable bottom_btns pd0">
                                                <div class="btn-group">
                                                    <button type="button" id="decline-call-button" class="btn btn-red mr15" data-dismiss="modal">Decline</button>
                                                    {{--                        <button type="button" id="answer-call-button" class="btn btn-green btn-success"> Accept </button>--}}
                                                    <a href="javascript:void(0);" id="answer-call-button" class="btn btn-green btn-success"> Accept </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @include('frontend.tpvagent.declinelead')
                        @include('frontend.tpvagent.verifylead')
                        @include('frontend.tpvagent.customer_questions')
                        @include('frontend.tpvagent.call-disconnected-view')
                        <div class="dispositions-outer row" style="margin-top: 20px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-3 sticky-panel">
            @include('layouts.chatbox')

            <!--agent-info-box-->
                <div class="agent-detail-wrapper">
                    <div class="agent-detail-main scrollbar-inner">
                        <h4 class="text-center">Agent Info</h4>
                        <table class="table table-striped table-border">
                            <tr>
                                <th>ID</th>
                                <td class="agent-userid"></td>
                            </tr>
                            <tr>
                                <th>First Name</th>
                                <td class="agent-first-name"></td>
                            </tr>
                            <tr>
                                <th>Last Name</th>
                                <td class="agent-last-name"></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td class="agent-email"></td>
                            </tr>
                            <tr>
                                <th>Client</th>
                                <td class="agent-client-name"></td>
                            </tr>
                            <tr>
                                <th>Sales Center</th>
                                <td class="agent-salescenter-name"></td>
                            </tr>
                            <tr>
                                <th>Location</th>
                                <td class="agent-location-name"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!--end-agent-info-box-->

            </div>
            <!--end-col-md-3-->
        </div>
    </div>

    @include('tpvagents.scriptpopup.scriptbox')




    <!-- Modal -->
    <div class="modal fade" id="confirmreview" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('tpvagents.sales.update')}}" method="get" id="updatetelesalestatus">
                    <input type="hidden" name="ref" value="{{$reference_id}}" id="reference_id_to_update">
                    <input type="hidden" name="v" value="2" id="verification_code">
                    <input type="hidden" value="" name="userid" id="userid">
                    <input type="hidden" value="" name="form_id" id="script_form_id">
                    <input type="hidden" value="0" name="is_multiple" id="is_multiple">
                    <input type="hidden" value="0" name="multiple_parent_id" id="multiple_parent_id">
                    <input type="hidden" value="" name="form_worksid" class="form_worksid">
                    <input type="hidden" value="" name="form_workflid" class="form_workflid">
                    <input type="hidden" value="" name="current_lang" id="script_current_lang">
                    <input type="hidden" value="" name="leadzipcodestate" class="leadzipcodestate">
                    <input type="hidden" value="" name="leadcommodity" class="leadcommodity">
                    <input type="hidden" value="{{$disposition_id}}" name="disposition_id" id="disposition_id">
                    {{ csrf_field() }}
                    {{ method_field('GET') }}

                    <input type="hidden" name="decline_reason" value="Incomplete information" class="form-control decline_reason">
                    <!-- <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Alert!</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div> -->
                    <div class="modal-body text-center">
                        <div class="mt15 text-center mb30"><?php echo getimage('/images/alert-danger.png') ?></div>
                        <div class="mt15">
                            Are you sure you want to <strong class="status-change-to"></strong> this sale?
                        </div>
                    </div>
                    <div class="modal-footer pd0">
                        <div class="btnintable bottom_btns pd0">
                            <div class="text-center sale-red-btn">
                                <button type="submit" class="btn btn-green">Confirm</button>
                                <button type="button" class="btn btn-red" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editoptionsmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" onsubmit="javascript:void(0);" method="get" id="editoptionsmodalform">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editoptions_label_header"> </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body edit-options">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success select_values_of_new_option">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit-tag-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit-tag-header">Edit </h5>
                </div>
                <form action="{{route('tpvagents.updateTagField')}}" method="post" id="edit-tag-form" data-parsley-validate>
                    @csrf
                    <div class="modal-body edit-tag-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        window.telesaleverifyagent = "{{route('telesaleverifyagent')}}";
        window.telesaleverifysaleid = "{{route('telesaleverifysaleid')}}";
        window.telesaleverifylead = "{{route('telesaleverifylead')}}";
        window.replicate = "{{route('telesale_clone_lead')}}";
        window.updatelead = "{{route('telesale_update_lead')}}";
        window.getQuestionsUrl = "{{ route('tpvagents.questions') }}";
        window.getDispositionsUrl = "{{ route('tpvagents.dispositions') }}";
        window.saveCustomerVerification = "{{ route('tpvagents.save-customer-verification') }}";
        window.leadsaleupdate = "{{ route('tpvagents.lead.sales.update') }}";
        window.leadDeclineUrl = "{{ route('tpvagents.lead-decline') }}";
        window.leadConformDeclineUrl = "{{ route('tpvagents.lead-conform-decline') }}";
        window.getCustomerQuestionsUrl = "{{ route('tpvagents.customer.questions') }}";
        window.customerLeadVerify = "{{ route('tpvagents.customer.lead.verify') }}";
        window.agentNotFoundData = "{{ route('tpvagents.agent.not.found') }}";
        window.leadNotFoundData = "{{ route('tpvagents.lead.not.found') }}";
        window.getTwillioNumber = "{{ route('tpvagents.twillio_number') }}";
        window.retrieveDispositions = "{{ route('tpvagents.retrieve-dispositions') }}";
        window.rescheduleCall = "{{ route('tpvagents.reschedule-call') }}";
        window.phoneNumDisplayFormat = "{{ config()->get('constants.DISPLAY_PHONE_NUMBER_FORMAT') }}";
        window.phoneNumReplacement = "{{ config()->get('constants.PHONE_NUMBER_REPLACEMENT') }}";
        window.storeVerifiedDisposition = "{{ route('tpvagents.store-verified-reason') }}";
        window.OUTBOUND_DISCONNECT = "{{ config('constants.SCHEDULE_CALL_TYPE_OUTBOUND_DISCONNECT') }}";
        window.getTagFieldUrl = "{{ route('tpvagents.getTagField') }}";
        window.selfVerifiedCallbackType = "{{ config()->get('constants.TWILIO_CALL_TYPE_SELFVERIFIED_CALLBACK') }}";
        window.hangupDetails = "{{route('store.call.hangup.details')}}";
        window.incomingSound = "{{asset('twilio/incoming.mp3')}}";



        $('body').on('click', '.verify-sale', function() {
            $('#verification_code').val('1');
            $('.status-change-to').html('Verify');
            $('.decline-sale-form').hide();

        });
        $('body').on('click', '.decline-confirm', function() {
            if (disconnected_by_agent === true) {
                $('#verification_code').val('2');

            }
            $('.status-change-to').html('Decline');

        });

        $('body').on('click', '.decline-form', function() {
            $('.decline-sale-form').toggle();
        });

        // $('#decline-lead-modal').modal({
        //     backdrop: 'static',
        //     keyboard: false // to prevent closing with Esc button (if you want this too)
        // })
    </script>    
@endsection
