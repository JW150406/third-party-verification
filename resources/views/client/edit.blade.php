@extends('layouts.admin')
@section('content')
    <?php
    $breadcrum = array(
        array('link' => route('client.index'), 'text' => 'Clients'),
        array('link' => "", 'text' => $client->name),
    );
    if(Auth::user()->can(['all-clients'])) {
        breadcrum($breadcrum);
    }
    ?>
    @push('styles')
        <style>
            .bottom_btns .btn.btn-green {
                margin: 0 10px;
            }
        </style>
    @endpush
    <div class="tpv-contbx">

        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="cont_bx3">

                        <div class="tpvbtn message">
                            <?php $allSessions = session()->all(); ?>
                            @if (isset($allSessions['success']))
                                <div class="alert alert-success alert-dismissable" data-auto-dismiss="2000">
                                    <?php echo $allSessions['success']; ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                @php session()->forget('success') @endphp
                            @endif
                            @if ($error = Session::get('error'))
                                <div class="alert alert-error alert-dismissable" data-auto-dismiss="2000">
                                    {{ $error }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <!--tab-new-design-start-->

                        <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx client-new-tabs">
                            <div class="client-bg-white min-height-solve">
                                <!--client-info-->
                                <div class="new-info">
                                    <img src="@empty($client->logo) {{asset('images/PlaceholderLogo.png')}} @else {{Storage::disk('s3')->url($client->logo)}} @endempty">
                                    <span>{{$client->name}}</span>
                                </div>
                                <!-- Nav tabs -->
                                @include('client.nav-tab')
                                <!-- Tab panes -->

                                <div class="tab-content">

                                    @if(auth()->user()->hasPermissionTo('view-client-info'))

                                        <!--about Details starts-->
                                            <div role="tabpanel" class="tab-pane active" id="About">


                                                <div class="row ">
                                                    <form id="client-form" enctype="multipart/form-data" method="POST"
                                                        action="{{ route('client.updateNew') }}" data-parsley-validate>

                                                        {{ csrf_field() }}
                                                        {{ method_field('PATCH') }}
                                                        <input type="hidden" name="id" value="{{ $client->id }}">
                                                        @include('client.form')
                                                    </form>
                                                </div>
                                            </div>

                                    @endif
                                    @if((auth()->user()->hasPermissionTo('view-workflow') || auth()->user()->hasPermissionTo('view-twilio-number'))  && $client->isActive())
                                        <!--Twilio Details starts-->
                                            <div role="tabpanel" class="tab-pane" id="PhoneNumbers">
                                                @include('client.twilio.index')
                                            </div>
                                            <!--twilio ends-->
                                    @endif
                                    @if(auth()->user()->hasPermissionTo('view-sales-center'))
                                        <!--Sales Center content starts-->
                                            <div role="tabpanel" class="tab-pane tworkspace" id="SalesCenter">
                                                @include('client.salescenters')
                                            </div>
                                            <!--sales content ends-->
                                    @endif
                                    @if(auth()->user()->hasPermissionTo('view-programs') && $client->isActive())
                                        <!--Programs content starts-->
                                            <div role="tabpanel" class="tab-pane tworkflow" id="Programs">
                                                @include('client.utility_new.program.index')
                                            </div>
                                            <!--Programs content ends-->
                                    @endif

                                    @if(auth()->user()->hasPermissionTo('view-client-settings') && $client->isActive())
                                        <!--Settings content starts-->
                                            <div role="tabpanel" class="tab-pane tworkflow" id="Settings">
                                                @include('client.settings.index')
                                            </div>
                                            <!--Settings content ends-->
                                    @endif

                                    @if(auth()->user()->hasPermissionTo('view-utility'))
                                        <!--Utilities content starts-->
                                        
                                            <div role="tabpanel" class="tab-pane" id="Utilities">
                                                @include('client.utility_new.index')
                                            </div>
                                            <!--Utilities content ends-->
                                    @endif

                                    @if(auth()->user()->hasPermissionTo('view-forms'))
                                    <!--Lead Creation Form content starts-->
                                        <div role="tabpanel" class="tab-pane" id="EnrollmentForm">
                                            @include('client.lead-form.list')
                                        </div>
                                    @endif

                                    @if(auth()->user()->hasPermissionTo('view-client-user'))
                                        <div role="tabpanel" class="tab-pane" id="Users">
                                            @include('client.users_new.index')
                                        </div>
                                        <!--Lead Creation Form content ends-->
                                    @endif

                                    @if(Auth::user()->hasPermissionTo('view-commodities')  && $client->isActive())
                                        <div role="tabpanel" class="tab-pane" id="commodities">
                                            @include('client.commodities.index')
                                        </div>
                                    @endif

                                    @if($client->isActive())
                                    @if(Auth::user()->hasPermissionTo('view-customer-type'))
                                        <div role="tabpanel" class="tab-pane" id="customer-types">
                                            @include('client.customer_type.index')
                                        </div>
                                        @endif
                                    @endif

                                    @if($client->isActive())
                                    @if(Auth::user()->hasPermissionTo('view-brand-contcts'))
                                        <!--Brand Contacts Form content starts-->
                                        <div role="tabpanel" class="tab-pane" id="BrandContacts">
                                            @include('client.brand-contact.index')
                                        </div>
                                        @endif
                                    @endif

                                    <!--Brand Contacts Form content ends-->
                                    @if(Auth::user()->hasPermissionTo('view-dispositions')  && $client->isActive())
                                    <div role="tabpanel" class="tab-pane" id="Dispositions">
                                        @include("dispositions.index")
                                    </div>
                                    @endif
                                    <!--Brand Contacts Form content ends-->

                                    @if(auth()->user()->hasPermissionTo('view-alerts') && $client->isActive())
                                    <?php
                                        $emailAlert = \App\models\FraudAlert::where('type','=','email')->where('added_for_client', $client->id)->get();
                                        $smsAlert = \App\models\FraudAlert::where('type','=','phone')->get();
                                    ?>  
                                    <div role="tabpanel" class="tab-pane" id="fraud_alerts">
                                        @include("fraud_alerts.index")
                                    </div>
                                    @endif

                                    @if(Auth::user()->hasPermissionTo('view-do-not-enroll')  && $client->isActive())
                                    <div role="tabpanel" class="tab-pane" id="doNotEnroll">
                                        @include("client.do-not-enroll.index")
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!--tab-new-design-end-->

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- @push('scripts')
    <script src="{{ asset('js/client.js') }}"></script>
@endpush -->

@if(Request::route()->getName() == 'client.show')
    @push('scripts')
        <script>
            (function ($) {
                "use strict"; // Start of use strict
                // disable show form
                $(".image-upload").hide();
                //$("#imagePreview").show();
                $("#client-form :input").prop("disabled", true);
                $("#client-save-btn").hide();
                $("#client-cancel-btn").hide();
                $('#client-form label').removeClass('yesstar');
                $('#client-form h5').addClass('nostar');
                $("#client-edit-btn").click(function (e) {
                    e.preventDefault();
                    $(".image-upload").show();
                    //$("#imagePreview").hide();
                    $('#client-form label').addClass('yesstar');
                    $('#client-form h5').removeClass('nostar');
                    $("#client-form :input").prop("disabled", false);
                    $("#client-edit-btn").hide();
                    $("#client-form .clientIdDiv").hide();
                    $("#client-back-btn").hide();
                    $("#client-save-btn").show();
                    $("#client-cancel-btn").show();
                    $('#contact_label').removeClass('yesstar');
                });
                $("#client-cancel-btn").click(function (e) {
                    e.preventDefault();
                    $(".image-upload").hide();
                    //$("#imagePreview").show();
                    $('#client-form label').removeClass('yesstar');
                    $('#client-form h5').addClass('nostar');
                    $("#client-form :input").prop("disabled", true);
                    $("#client-edit-btn").show();
                    $("#client-form .clientIdDiv").show();
                    $("#client-back-btn").show();
                    $("#client-save-btn").hide();
                    $("#client-cancel-btn").hide();
                    $('#contact_label').removeClass('yesstar');
                });
                $('#contact_label').removeClass('yesstar');

            })(jQuery);
        </script>
    @endpush
@else
    @push('scripts')
        <script>
            (function ($) {
                "use strict"; // Start of use strict
                // disable show form
                $(".image-upload").show();
                //$("#imagePreview").hide();
                $("#client-form :input").prop("disabled", false);
                $("#client-edit-btn").hide();
                $("#client-form .clientIdDiv").hide();
                $("#client-back-btn").hide();
                $("#client-save-btn").show();
                $("#client-cancel-btn").show();
                $('#client-form label').addClass('yesstar');
                $('#client-form h5').removeClass('nostar');
                $("#client-edit-btn").click(function (e) {
                    e.preventDefault();
                    $(".image-upload").show();
                    //$("#imagePreview").hide();
                    $('#client-form label').addClass('yesstar');
                    $('#client-form h5').removeClass('nostar');
                    $("#client-form :input").prop("disabled", false);
                    $("#client-edit-btn").hide();
                    $("#client-form .clientIdDiv").hide();
                    $("#client-back-btn").hide();
                    $("#client-save-btn").show();
                    $("#client-cancel-btn").show();
                    $('#contact_label').removeClass('yesstar');
                });
                $("#client-cancel-btn").click(function (e) {
                    e.preventDefault();
                    $(".image-upload").hide();
                    //$("#imagePreview").show();
                    $('#client-form label').removeClass('yesstar');
                    $('#client-form h5').addClass('nostar');
                    $("#client-form :input").prop("disabled", true);
                    $("#client-edit-btn").show();
                    $("#client-form .clientIdDiv").show();
                    $("#client-back-btn").show();
                    $("#client-save-btn").hide();
                    $("#client-cancel-btn").hide();
                    $('#contact_label').removeClass('yesstar');
                });
                $('#contact_label').removeClass('yesstar');
            })(jQuery);
        </script>
    @endpush
@endif

