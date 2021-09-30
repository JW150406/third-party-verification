@extends('layouts.selfverify')
@section('content')
    <?php
    // echo "<pre>";
    // print_r($telesales_lead);
    // print_r($lead_Data);
    //    echo "</pre>";
    \Log::info($lead_Data);
    $commodity = "";
    $state_code = "";
    foreach ($lead_Data as $metadata) {
        
        if ($metadata->meta_key == 'Commodity') {
            $commodity = $metadata->meta_value;
            \Log::info('self Verify Commodity::'.$commodity);
        }
        if ($metadata->meta_key == 'zipcodeState') {
            $state_code = $metadata->meta_value;
        \Log::info('self Verify Stateee::'.$state_code);
        }
    }

    ?>

    <div class="tpv-contbx edit-agentinfo selfverify-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="cont_bx3">
                        <!-- <div class="col-xs-12 col-sm-6 col-md-6 tpv_heading">
                          <h1> </h1>
                        </div> -->

                        <div class=" sales_tablebx">
                            <div class="tpvbtn message"></div>
                            <!-- Nav tabs -->
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <!--agent details starts-->
                                <form class="" action="<?php echo route('telesaleverifylead') ?>" id="selfverifyform">
                                    <input type="hidden" name="telesale_form_id" id="telesale_form_id"
                                           value="<?php echo $telesales_lead->form_id ?>">
                                    <input type="hidden" name="telesale_id" id="telesale_id"
                                           value="<?php echo $telesales_lead->id ?>">
                                    <input type="hidden" name="current_lang" id="current_lang" value="">
                                    <input type="hidden" name="leadcommodity" value="<?php echo $commodity; ?>">
                                    <input type="hidden" name="leadzipcodestate" value="<?php echo $state_code; ?>">
                                    <input type="hidden" name="agent_user_id" id="agent_user_id"
                                           value="<?php echo $telesales_lead->user_id; ?>">
                                    <input type="hidden" name="agent_client_id" id="agent_client_id"
                                           value="<?php echo $telesales_lead->client_id; ?>">
                                    <input type="hidden" name="telesale_reference_id" id="telesale_reference_id"
                                           value="<?php echo $telesales_lead->id; ?>">
                                    <input type="hidden" name="mode" value="<?php echo $mode; ?>">
                                    <input type="hidden" name="vtype" value="1">

                                    <div class="select-lang-center">
                                        <div class="row">
                                            <h3 class="text-center script-language-selfverify">Select Language</h3>
                                            <div class="col-sm-3 col-md-3"></div>
                                            <div class="col-sm-3 col-md-3 script-language-selfverify">
                                                <a href="#" rel="en" class="d">
                                                    <div class="tile-stats tile-red">
                                                        <div class="num">en</div>
                                                        <h3>English</h3>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-sm-3 col-md-3 script-language-selfverify">
                                                <a href="#" rel="es" class="d">
                                                    <div class="tile-stats tile-red">
                                                        <div class="num">es</div>
                                                        <h3>Spanish</h3>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <!-- <div class="col-sm-12"> -->
                                            <div class="lead-sucess-img" style="display: none;">
                                                <img src="{{ asset('images/lead-success.png') }}" style="width: 10%;">
                                            </div>
                                            <div class="sale-detail-wrapper client-bg-white col-md-12" style="display: none">
                                            </div>
                                            <div class="text-center">
{{--                                                <a style="display: none;" data-target="#confirmreview1"--}}
{{--                                                   data-toggle="modal" class="mt20 btn btn-green verify-sale"--}}
{{--                                                   data-title="Are you sure you want to verify this sale?">Verify</a>--}}

                                                <a style="display: none;" class="mt20 btn btn-green verify-sale" >Complete</a>

                                            </div>
                                        <!-- </div> -->
                                    </div>

                                </form>
                                <!--agent details ends-->

 <div class="row">
    <div class="col-sm-12">
        <div class="identity-verification-detail-wrapper identity_wrapper" style="display: none">
            <div class="col-sm-12 question-text identity-verify-lead-data-1 verification-question-text identity-verification-opicity">
                <h2 class="agent-title">Identity Verification</h2>
                <p class="welcome-identity-verification"></p>
                <P class="first-name-identity-qus"></P>
                <div>
                    <span class="inline-block">
                    <input class="form-control verify-first-name question-input" id="first-name-data" oninput="changeFirstName()" value=""
                           name="question[Authorized name]" autocomplete="off">
                        <input type="hidden" name="_token" id="first-name-token" value="{{ csrf_token() }}">
                    </span>

                    <div class="first-name-identity-verify-status">
                        <span id="first-name-message"></span>
                    <div class="mt20">
                      <span class="inline-block">
                        <button type="button" class="btn btn-green checkfirstnametverify" id="FirstNameNextToNext">Next</button>
                      </span>
                        <span class="inline-block">
                            <a class="btn btn-red verified_no" data-toggle="modal" data-target="#decline-lead-modal" id="FirstNamePre" data-currentelement="0" data-nextelement="1">Cancel</a>
                      </span>
                    </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 question-text identity-verify-lead-data-2 verification-question-text identity-verification-opicity" style="display: none">
                <P class="middle-name-identity-qus"></P>
                <div>
                  <span class="inline-block">
                    <input class="form-control verify-middel-name question-input" oninput="changeMiddleName()" value="" name="question[Middle Name]"
                           autocomplete="off">
                  </span>
                    <div class="middel-name-identity-verify-status">
                        <span id="middel-name-message"></span>
                    <div class="mt20">
                    <span class="inline-block">
                        <button type="button" class="btn btn-green checkmiddelnametid" id="middelNameNext">Next</button>
                    </span>
                    <span class="inline-block">
                        <a class="btn btn-red verified_no" data-toggle="modal" data-target="#decline-lead-modal" id="middelNamePre" data-currentelement="1" data-nextelement="2">Cancel</a>
                    </span>
                    </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 question-text identity-verify-lead-data-3 verification-question-text identity-verification-opicity">
                <P class="last-name-identity-qus"></P>
                <div>
                    <span class="inline-block">
                        <input class="form-control verify-last-name question-input" oninput="changeLastName()" value="" name="question[Last Name]"
                           autocomplete="off">
                    </span>
                    <div class="last-name-identity-verify-status">
                        <span id="last-name-message"></span>
                    <div class="mt20">
                        <span class="inline-block">
                        <button type="button" class="btn btn-green checklastnamet" id="lastNameNext">Next</button>
                    </span>
                    <span class="inline-block">
                        <a class="btn btn-red verified_no" data-toggle="modal" data-target="#decline-lead-modal" id="lastNamePre" data-currentelement="2" data-nextelement="3">Cancel</a>
                    </span>
                    </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 question-text identity-verify-lead-data-4 verification-question-text identity-verification-opicity">
                    <P class="zip-code-identity-qus"></P>
                    <div>
                    <span class="inline-block">
                        <input class="form-control verify-zip-code question-input" oninput="changeZipCode()" value="" name="question[Zip Code]"
                               autocomplete="off">
                    </span>
                        <div class="zip-code-identity-verify-status">
                            <span id="zip-code-message"></span>
                            <div class="mt20">
                                 <span class="inline-block">
                        <button type="button" class="btn btn-green checkzipcode" id="zipCodeNext">Next</button>
                    </span>
                    <span class="inline-block">
                         <a class="btn btn-red verified_no" data-toggle="modal" data-target="#decline-lead-modal" id="zipCodePre" data-currentelement="3" data-nextelement="4">Cancel</a>
                    </span>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 question-text identity-verify-lead-data-5 verification-question-text identity-verification-opicity">
                    <P class="phone-identity-qus"></P>
                    <div>
                    <span class="inline-block">
                        <input class="form-control verify-phone-number question-input" oninput="changePhoneNumber()" value="" name="question[Phome Number]"
                               autocomplete="off">
                    </span>

                        <div class="phone-identity-verify-status">
                            <span id="phone-number-message"></span>
                        <div class="mt20">
                    <span class="inline-block">
                        <button type="button" class="btn btn-green checkphonenumber" id="phoneNext">Next</button>
                    </span>
                    <span class="inline-block">
                        <a class="btn btn-red verified_no" data-toggle="modal" data-target="#decline-lead-modal" id="phonePre" data-currentelement="4" data-nextelement="5">Cancel</a>
                    </span>

                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
 </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade confirmation-model" id="confirmreview1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('tpvagents.sales.update')}}" method="get" id="updatetelesalestatus">
                    <input type="hidden" name="ref" value="<?php echo $telesales_lead->refrence_id; ?>"
        
                           id="reference_id_to_update">
                    <input type="hidden" name="v" value="1" id="verification_code">
                    
                    <input type="hidden" value="1" name="userid" id="userid">
                    <input type="hidden" value="<?php echo $telesales_lead->form_id ?>" name="form_id"
                           id="script_form_id">
                    <input type="hidden" value="0" name="is_multiple" id="is_multiple">
                    <input type="hidden" value="0" name="multiple_parent_id" id="multiple_parent_id">
                    <input type="hidden" value="" name="form_worksid" class="form_worksid">
                    <input type="hidden" value="" name="form_workflid" class="form_workflid">
                    <input type="hidden" value="" name="current_lang" id="script_current_lang">
                    <input type="hidden" value="<?php echo $state_code; ?>" name="leadzipcodestate"
                           class="leadzipcodestate">
                    <input type="hidden" value="<?php echo $commodity; ?>" name="leadcommodity" class="leadcommodity">
                    <input type="hidden" value="" name="disposition_id" id="disposition_id">
                    <input type="hidden" name="mode" value="<?php echo $mode; ?>">
                    <input type="hidden" name="vtype" value="1">
                    <input type="hidden" name="user_latitude" value="" id="user_latitude">
                    <input type="hidden" name="user_longitude" value="" id="user_longitude">
                    {{ csrf_field() }}
                    {{ method_field('GET') }}

                    <input type="hidden" name="decline_reason" value="Incomplete information"
                           class="form-control decline_reason">


                    <div class="modal-body text-center">
                        <div class="mt15 text-center mb15">
				<?php echo getimage('/images/alert-danger.png') ?>
				<p class="logout-title">Are you sure?</p>
			</div>
                        <div id='confirm-message'>Are you sure you want to decline this sale?</div>
                    </div>

                    <div class="modal-footer">
                        <div class="btnintable bottom_btns pd0">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-green delete_twilio_row">Confirm</button>
                                <button type="button" class="btn btn-red" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC49bXfihl4zZqjG2-iRLUmcWO_PVcDehM&sensor=false&v=3"></script>
    <script src="{{ asset('js/agent.js') }}?v=9"></script>

    <script type="text/javascript">
        var telesaleForm = false;
        function getLocation(isFormSubmit=false) {
            telesaleForm = isFormSubmit;
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
                setLatLng(); 
                setLatLangError("Geolocation is not supported by this browser.");
            }            
        }

        function showPosition(position) {
            setLatLng(position.coords.latitude,position.coords.longitude);            
        }

        function showError(error) {
            setLatLng();
            switch(error.code) {
                case error.PERMISSION_DENIED:
                setLatLangError("User denied the request for Geolocation.");
                break;
                case error.POSITION_UNAVAILABLE:
                setLatLangError("Location information is unavailable.");
                break;
                case error.TIMEOUT:
                setLatLangError("The request to get user location timed out.");
                break;
                case error.UNKNOWN_ERROR:
                setLatLangError("An unknown error occurred.");
                break;
            }
        }

        function setLatLng(latitude=null,longitude=null) {
            $("#user_latitude").val(latitude);
            $("#user_longitude").val(longitude);
            if(telesaleForm) {
                $('#updatetelesalestatus').submit();
            }
        }

        function setLatLangError(error) {
            console.log(error);
        }

        function changeFirstName() {
            $('#FirstNameNextToNext').removeAttr('disabled');
            $("#first-name-message").html('');
        }

        function changeMiddleName() {
            $('#middelNameNext').removeAttr('disabled');
            $("#middel-name-message").html('');
        }

        function changeLastName() {
            $('#lastNameNext').removeAttr('disabled');
            $("#last-name-message").html('');
        }

        function changeZipCode() {
            $('#zipCodeNext').removeAttr('disabled');
            $("#zip-code-message").html('');
        }

        function changePhoneNumber() {
            $('#phoneNext').removeAttr('disabled');
            $("#phone-number-message").html('');
        }



        $(function () {
            $_this = $;

            $_this("#updatetelesalestatus").on("submit", function (event) {
                event.preventDefault();
                $('.verify-sale').hide();
                var url = $_this(this).attr('action');
                $('.sale-detail-wrapper').html('<div class="text-center"><i class="fa fa-spin fa-spinner" style="font-size:3em"></i></div>');
                $('.sale-detail-wrapper').show();
                var client_id = $_this(this).val();
                $_this.ajax({
                    type: "POST",
                    url: url,
                    data: $_this(this).serialize(),
                    success: function (response) {

                        $_this('.decline-sale-form').hide();
                        $_this('.decline-form').hide();
                        $_this('.verify-sale').hide();
                        $_this('.identity_wrapper').hide();

                        if (response.declineType == true) {
                            $_this('#confirmreview1').modal('toggle');
                        }


                        if (typeof response.verification_all_done === 'undefined') {
                            $_this('.verify-sale').hide();
                            if (response.status == 'success') {

                                $('#reference_id_to_update').val(response.ref);
                                $('.sale-detail-wrapper').append('<h3 class="verification-question-text lead-verification-title">Lead verification</h3>');
                                $('.sale-detail-wrapper').append('<div class="verifications-questions-wrapper"></div>');
                                for (var i = 0; i < response.data.length; i++) {
                                    console.log(response.data[i]['question']);
                                    var question_html = single_question_with_answer(response.data[i]['question'], i, response.data[i]['positive_ans'], response.data[i]['negative_ans'], response.data[i]['answer_option'], response.data[i]['is_customizable'], response.data[i]['is_introductionary'], response.data[i]['intro_questions']);
                                    $('.verifications-questions-wrapper').append(question_html);
                                }

                                $('.verification-0').addClass('active');
                                $('.sale-detail-wrapper').show();
                                scrollTop();
                                $('.lead-verification-title').show();
                                $('.salesagentintro').hide();

                            } else {
                                $('.script_for_confirmation').hide();
                                printAjaxErrorMsg('Lead not Found');
                            }

                        } else {
                            var alertclass = "success";
                            if (response.status == 'error') {
                                var alertclass = "danger";


                            } else {
                                window.leadverify = true;
                                $_this('.sale-detail-wrapper').html('');
                                for (var i = 0; i < response.questions.length; i++) {

                                    var question_html = single_question_closing(response.questions[i]);
                                    $_this('.sale-detail-wrapper').append(question_html);
                                }
                                $('.sale-detail-wrapper').show();
                            }
                            $(".lead-sucess-img").show();
                            // printAjaxSuccessMsg(response.message);
                        }
                    },
                    error: function (response) {
                        console.log(response);

                    }
                });
            });

            $(".verify-sale").click(function () {
                getLocation(true);
                $("#confirm-message").html($(this).data('title'));
            });
        });

        function printAjaxSuccessMsg(message) {
            $(".message").html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><p>' + message + '</p></div>');
            timemessage();
        }

        function printAjaxErrorMsg(message) {
            $(".message").html('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><p>' + message + '</p></div>');
            timemessage();
        }

        function timemessage() {

            setTimeout(() => {
                $('.alert').hide();
            }, 3000);

        }
    </script>

    <script type="text/javascript">
        window.identityVerification = "{{ route('identityverification') }}";
        jQuery(document).ready(function () {
            $_this = jQuery;
            $_this('body').on('click', '.script-language-selfverify a', function (e) {
                var current_lang = $_this(this).attr('rel');
                $('.sale-detail-wrapper').hide();
                $_this('#current_lang').val(current_lang);
                $_this('#script_current_lang').val(current_lang);
                var form_id = $('#telesale_form_id').val();
                var telesale_id = $('#telesale_id').val();
                var client_id = $('#agent_client_id').val();

                $.ajax({
                    type: "get",
                    url: identityVerification,
                    data: {
                        'form_id': form_id,
                        'telesale_id': telesale_id,
                        'language': current_lang,
                        'client_id': client_id,
                        'selected_script': 'identity_verification'
                    },
                    success: function (data) {
                        //console.log(data.question[0]['question']);
                        if (data.status == 'success') {
                            if(data.middleName == null){
                                //$("#FirstNameNextToNext").hide();
                                $('.identity-verify-lead-data-2').hide();
                                $(".lastNamepreData").attr("id","lastNamePreToPre");
                            }else{
                                $('.identity-verify-lead-data-2').show();
                                $(".lastNamepreData").attr("id","lastNamePre");
                            }
                            if(current_lang == 'es'){
                                $('.welcome-identity-verification').html('Bienvenido a TPV360, su verificador de terceros para sus servicios con XYZ Energy. Antes de comenzar el proceso de verificación, necesitaremos la siguiente información:');
                            }else{
                                $('.welcome-identity-verification').html('Welcome to TPV360, your third party verifier for your services with XYZ Energy. Before we begin the verification process, we will need the following information:');
                            }
                            //console.log('-----------identity verification -------' + data.question);
                            $_this('.script-language-selfverify').hide();
                            $('.first-name-identity-qus').html(data.question[0]['question']);
                            $('#FirstNameNextToNext').html(data.question[0]['positive_ans']);
                            $('#FirstNamePre').html(data.question[0]['negative_ans']);

                            $('.middle-name-identity-qus').html(data.question[1]['question']);
                            $('#middelNameNext').html(data.question[1]['positive_ans']);
                            $('#middelNamePre').html(data.question[1]['negative_ans']);

                            $('.last-name-identity-qus').html(data.question[2]['question']);
                            $('#lastNameNext').html(data.question[2]['positive_ans']);
                            $('#lastNamePre').html(data.question[2]['negative_ans']);

                            $('.zip-code-identity-qus').html(data.question[3]['question']);
                            $('#zipCodeNext').html(data.question[3]['positive_ans']);
                            $('#zipCodePre').html(data.question[3]['negative_ans']);

                            $('.phone-identity-qus').html(data.question[4]['question']);
                            $('#phoneNext').html(data.question[4]['positive_ans']);
                            $('#phonePre').html(data.question[4]['negative_ans']);

                            //$_this('.verification-0').addClass('active');
                            $('.identity-verify-lead-data-1').addClass('active');
                            $('.identity-verification-detail-wrapper').show();
                        }
                    },
                    error: function (err) {
                        console.log(err.message);
                    }
                });

            });
        });

        /*First name verify*/
        $('body').on('click', '.checkfirstnametverify', function () {

            window.firstNameVerification = "{{ route('first-name-verify') }}";
            var firstName = $('.verify-first-name').val();
            var telesale_id = $('#telesale_id').val();
            var current_lang = $('#current_lang').val();
            var form_id = $('#telesale_form_id').val();

          if (firstName != "") {
                $.ajax({
                    type: "POST",
                    url: firstNameVerification,
                    data: {
                        '_token': $('#first-name-token').val(),
                        'firstname': firstName,
                        'form_id': form_id,
                        'telesaleid': telesale_id,
                        'current_lang': current_lang,
                        'scriptTypr' : 'firstname',
                        'question': $('.first-name-identity-qus').html()
                    },
                    success: function (response) {
                        if (response.status == 'success') {
                        if(response.data == null){
                            $('.identity-verify-lead-data-1').removeClass('active');
                            $('.identity-verify-lead-data-3').addClass('active');
                            $(".lastNamepreData").attr("id","lastNamePreToPre");
                        }else{
                            $('.identity-verify-lead-data-1').removeClass('active');
                            $('.identity-verify-lead-data-2').addClass('active');
                        }
                            $('.first-name-identity-verify-status').show();

                        } else {
                            $('#FirstNameNextToNext').attr('disabled', true);
                            $("#first-name-message").html('<p class="text-danger">Please enter a correct First Name or else the verification can not proceed.</p>');
                            $("#LeadError").show();
                            $('.first-name-identity-verify-status').show();
                        }
                    },
                    fail: function () {
                        $('.first-name-identity-verify-status').show();
                    }
                });
            }

        });


/*middel name verify*/
        $('body').on('click', '.checkmiddelnametid', function () {

            window.firstNameVerification = "{{ route('first-name-verify') }}";
            var middelName = $('.verify-middel-name').val();
            var telesale_id = $('#telesale_id').val();
            var current_lang = $('#current_lang').val();
            var form_id = $('#telesale_form_id').val();

            if (middelName != "") {
                $.ajax({
                    type: "POST",
                    url: firstNameVerification,
                    data: {
                        '_token': $('#first-name-token').val(),
                        'middelName':middelName,
                        'form_id':form_id,
                        'telesaleid': telesale_id,
                        'current_lang':current_lang,
                        'scriptTypr' :'middelname',
                        'question': $('.middle-name-identity-qus').html()
                    },
                    success: function (response) {
                        if (response.status == 'success') {
                            $('.middel-name-identity-verify-status').show();
                            $('.identity-verify-lead-data-2').removeClass('active');
                            $('.identity-verify-lead-data-3').addClass('active');

                        } else {
                            $('#middelNameNext').attr('disabled', true);
                            //$('#middelNameNext').removeAttr('disabled');
                            $("#middel-name-message").html('<p class="text-danger">Please enter a correct Middle Name or else the verification can not proceed</p>');
                            $('.middel-name-identity-verify-status').show();
                        }
                    },
                    fail: function () {
                        $('.middel-name-identity-verify-status').hide();

                    }
                });
            }

        });

        /*Last name verify*/
        $('body').on('click', '.checklastnamet', function () {

            window.firstNameVerification = "{{ route('first-name-verify') }}";
            var lastName = $('.verify-last-name').val();
            var telesale_id = $('#telesale_id').val();
            var current_lang = $('#current_lang').val();
            var form_id = $('#telesale_form_id').val();

            if (lastName != "") {
                $.ajax({
                    type: "POST",
                    url: firstNameVerification,
                    data: {
                        '_token': $('#first-name-token').val(),
                        'lastName':lastName,
                        'form_id':form_id,
                        'telesaleid': telesale_id,
                        'current_lang':current_lang,
                        'scriptTypr' :'lastname',
                        'question': $('.last-name-identity-qus').html()
                    },
                    success: function (response) {
                        if (response.status == 'success') {
                            $('#lastNameNext').attr('disabled', true);
                            $('.identity-verify-lead-data-3').removeClass('active');
                            $('.identity-verify-lead-data-4').addClass('active');
                            /*if(response.data == null){
                                $("#FirstNameNextToNext").show();
                            }
*/                          $('.last-name-identity-verify-status').show();

                        } else {
                            $('#lastNameNext').attr('disabled', true);
                            $("#last-name-message").html('<p class="text-danger">Please enter a correct Last Name or else the verification can not proceed.</p>');
                            $('.last-name-identity-verify-status').show();
                        }
                    },
                    fail: function () {
                        $('.last-name-identity-verify-status').hide();
                    }
                });
            }
        });

        /*Zip code verify*/
        $('body').on('click', '.checkzipcode', function () {

            window.firstNameVerification = "{{ route('first-name-verify') }}";
            var zipCode = $('.verify-zip-code').val();
            var telesale_id = $('#telesale_id').val();
            var current_lang = $('#current_lang').val();
            var form_id = $('#telesale_form_id').val();

            if (zipCode != "") {
                $('#zip_code_button').attr('disabled', true);
                $.ajax({
                    type: "POST",
                    url: firstNameVerification,
                    data: {
                        '_token': $('#first-name-token').val(),
                        'zipCode':zipCode,
                        'form_id':form_id,
                        'telesaleid': telesale_id,
                        'current_lang':current_lang,
                        'scriptTypr' :'zipcode',
                        'question': $('.zip-code-identity-qus').html()
                    },
                    success: function (response) {
                        if (response.status == 'success') {
                            $('.identity-verify-lead-data-4').removeClass('active');
                            $('.identity-verify-lead-data-5').addClass('active');
                            $('.zip-code-identity-verify-status').show();
                        } else {
                            $('#zipCodeNext').attr('disabled', true);
                            $("#zip-code-message").html('<p class="text-danger">Please enter a correct Zip Code or else the verification can not proceed.</p>');
                            $('.zip-code-identity-verify-status').show();
                        }
                    },
                    fail: function () {
                        $('.zip-code-identity-verify-status').hide();
                    }
                });
            }
        });


        /*Phone number verify*/
        $('body').on('click', '.checkphonenumber', function () {

            window.firstNameVerification = "{{ route('first-name-verify') }}";
            var phoneNumber = $('.verify-phone-number').val();
            var telesale_id = $('#telesale_id').val();
            var current_lang = $('#current_lang').val();
            var form_id = $('#telesale_form_id').val();

            if (phoneNumber != "") {
                $.ajax({
                    type: "POST",
                    url: firstNameVerification,
                    data: {
                        '_token': $('#first-name-token').val(),
                        'phoneNumber':phoneNumber,
                        'form_id':form_id,
                        'telesaleid': telesale_id,
                        'current_lang':current_lang,
                        'scriptTypr' :'phonenumber',
                        'question': $('.phone-identity-qus').html()
                    },
                    success: function (response) {
                        if (response.status == 'success') {
                            identity_verify_question();
                        } else {
                            $('#phoneNext').attr('disabled', true);
                            $("#phone-number-message").html('<p class="text-danger">Please enter a correct Phone Number or else the verification can not proceed</p>');
                            $('.phone-identity-verify-status').show();
                        }
                    },
                    fail: function () {
                        $('.phone-identity-verify-status').show();
                    }
                });
            }
        });

        function identity_verify_question(){
            $('.identity-verification-detail-wrapper').hide();
            $('.identity-verify-lead-data-5').removeClass('active');
            //e.preventDefault();
            /*var current_lang = $_this(this).attr('rel');
            $_this('#current_lang').val(current_lang);
            $_this('#script_current_lang').val(current_lang);
            */
            $_this.ajax({
                type: "POST",
                url: $_this('#selfverifyform').attr('action'),
                data: $_this('#selfverifyform').serialize(),
                success: function(response) {

                    if (response.status == 'success') {
                        $_this('.script-language-selfverify').hide();
                        window.edit_options_fields = response.options;
                        $_this('.sale-detail-wrapper').append('<div class="verifications-questions-wrapper"></div>');
                        for (var i = 0; i < response.data.length; i++) {
                            var qus_num = i + 1;
                            var question_html = single_question_with_answer(response.data[i]['question'], i, response.data[i]['positive_ans'], response.data[i]['negative_ans'], response.data[i]['answer_option'], response.data[i]['is_customizable'],qus_num, response.data[i]['is_introductionary'], response.data[i]['intro_questions']);
                            $_this('.verifications-questions-wrapper').append(question_html);
                        }
                        $('.sale-detail-wrapper').show();
                        $_this('.verification-0').addClass('active');
                        $_this('.lead-verification-title').show();
                        scrollTop();
                    } else {
                        $_this('.script_for_confirmation').hide();
                        printAjaxErrorMsg(response.message);                        
                    }

                },
                error: function(response) {
                }
            });
        }

        function scrollTop()
        {
            var scrolldiv = $('.verification-0').offset().top - ($(window).height() / 2);
            $('html,body').animate({
                scrollTop: scrolldiv
            }, "slow");
        }
    </script>

@endsection
