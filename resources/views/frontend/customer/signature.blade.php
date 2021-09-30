@extends('layouts.selfverify')
@push('styles')
<link rel="stylesheet" href="{{ asset('plugin/signature/css/jquery.signaturepad.css') }}">
<style>
    canvas {
        outline: 2px solid #aaa;
        background: #fff;
    }

    .tc-link a {
        text-decoration: underline !important;
        font-weight: 600;
    }
    .signature-hide {
        height: 0; 
        overflow: hidden;
    }
</style>
@endpush
@section('content')
@php $isCalifornia = false; @endphp
<div class="">
    <div class="container signature-outer">
        <div class="row">
            <div class="col-md-6 mt-5">
                <div class="card">
                    <div class="card-header" id="card-header-div">
                        <h3>Signature </h3>
                    </div>
                    <div class="card-body">
                        @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissable" data-auto-dismiss="2000">
                            {{ $message }}
                        </div>
                        @endif
                        @if ($message = Session::get('error'))
                        <div class="alert alert-danger alert-dismissable" data-auto-dismiss="2000">
                            {{ $message }}
                        </div>
                        @endif

                        @foreach($fields as $lead)
                        @if($lead['is_primary'] == 1)
                            @if($lead['type'] == 'phone_number')
                                @php                                
                                $value = (isset($lead['telesales_data_tmp'][0]) && !empty($lead['telesales_data_tmp'][0])) ? $lead['telesales_data_tmp'][0] : [];
                                @endphp
                            @if(!empty($value))
                                @php
                                $value = (strlen($value['meta_value']) == 11) ? $value['meta_value'] : "1" . $value['meta_value'];
                                    @endphp
                                <div class="row" id="phone-number-div">
                                    <div class="col-md-3 col-xs-3">
                                        <p>Phone:</p>
                                    </div>
                                    <div class="col-md-9 col-xs-9">
                                        <p> {{ preg_replace(config()->get('constants.DISPLAY_PHONE_NUMBER_FORMAT'), config('constants.PHONE_NUMBER_REPLACEMENT'), $value) }}</p>
                                    </div>
                                </div>
                            @endif
                        @elseif($lead['type'] == 'email')
                        @php $value = $lead['telesales_data_tmp'][0]; @endphp
                        <div class="row" id="email-div">
                            <div class="col-md-3 col-xs-3">
                                <p>Email:</p>
                            </div>
                            <div class="col-md-9 col-xs-9">
                                <p>{{ $value['meta_value'] }}</p>
                            </div>
                        </div>
                        @elseif($lead['type'] == 'fullname')
                        <div class="row" id="name-div">
                            <div class="col-md-3 col-xs-3">
                                <p> Name:</p>
                            </div>
                            <div class="col-md-9 col-xs-9">
                                <p>{{ $customerName = getNameFromArray($lead['telesales_data_tmp']) }}</p>
                            </div>
                        </div>
                        
                        @endif
                        @endif
                        @if($lead['type'] == 'address')
                        <div class="row" id="address-div2">
                            <div class="col-md-3 col-xs-3">
                                <p>{{$lead['label']}}:</p>
                            </div>
                            <div class="col-md-9 col-xs-9">
                                <p>{{ getAddressFromArray($lead['telesales_data_tmp']) }}</p>
                            </div>
                        </div>
                        @elseif($lead['type'] == 'service_and_billing_address')
                        <div id="address-div">
                        <div class="row">
                        <div class="col-md-12 col-xs-12 address-label-color">
                            <p>{{$lead['label']}} </p>
                        </div>
                        </div>
                        <div  class="row">
                            <div class="col-md-3 col-xs-3">
                                <p> Service Address: </p>
                            </div>
                            <div class="col-md-9 col-xs-9">
                                <p> {{ getServiceAddressFromArray($lead['telesales_data_tmp']) }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-xs-3">
                                <p>  Billing Address:</p>
                            </div>
                            <div class="col-md-9 col-xs-9">
                                <p> {{ getBillingAddressFromArray($lead['telesales_data_tmp']) }}</p>
                            </div>
                        </div>
                        </div>
                        @endif
                        @endforeach
                        @foreach($programs as $program)
                        <div class="program-div">
                            <label class="prog-title">@if(!empty($program->utility->utilityCommodity)){{ $program->utility->utilityCommodity->name }} Utility @endif</label>
                            <div class="row">
                                <div class="col-md-3 col-xs-3">
                                    <p>Program ID:</p>
                                </div>
                                <div class="col-md-9 col-xs-9">
                                    <p>{{ $program->code }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-xs-3">
                                    <p> Program:</p>
                                </div>
                                <div class="col-md-9 col-xs-9">
                                    <p>{{ $program->name }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-xs-3">
                                    <p>Rate:</p>
                                </div>
                                <div class="col-md-9 col-xs-9">
                                    <p>${{ $program->rate }} per {{ $program->unit_of_measure }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-xs-3">
                                    <p> Term (Months)</p>
                                </div>
                                <div class="col-md-9 col-xs-9">
                                    <p>{{ $program->term }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-xs-3">
                                    <p> ETF ($)</p>
                                </div>
                                <div class="col-md-9 col-xs-9">
                                    <p>{{ $program->etf }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-xs-3">
                                    <p>MSF ($)</p>
                                </div>
                                <div class="col-md-9 col-xs-9">
                                    <p> {{ $program->msf }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <label class="prog-title" id="t-and-c-label"> Terms and conditions </label>
                            </div>
                            @if($telesaleTmpClientId == config()->get('constants.CLIENT_LE_CLIENT_ID'))
                            @foreach($programs as $pr)
                                @if(!empty($pr->utility->utilityCommodity))
                                    @php $search = $pr->utility->fullname @endphp
                                    @if(strtolower($pr->utility->utilityCommodity->name) == "electric")
                                        <div class="col-md-12 col-xs-12 tc-link">
                                            @if(preg_match("/{$search}/i", "AEP-Columbus Southern") || preg_match("/{$search}/i", "AEP-Ohio Power"))
                                                <p><strong><a href="https://www.utilitygasandpower.com/electricity-terms-condition-999" target="_blank">AEP Electricity Terms and Conditions</a></strong></p>
                                            @elseif(preg_match("/{$search}/i", "Dayton Power & Light"))
                                                <p><strong><a href="https://www.utilitygasandpower.com/electricity-terms-condition-999" target="_blank">DP&L Electricity Terms and Conditions</a></strong></p>
                                            @elseif(preg_match("/{$search}/i", "Duke Energy Ohio-Electric"))
                                                <p><strong><a href="https://www.utilitygasandpower.com/electricity-terms-condition-999" target="_blank">Duke Energy Electricity Terms and Conditions</a></strong></p>
                                            @elseif(preg_match("/{$search}/i", "The Illuminating Company"))
                                                <p><strong><a href="https://www.utilitygasandpower.com/electricity-terms-condition-999" target="_blank">The Illuminating Co- Cleveland Electricity Terms and Conditions</a></strong></p>
                                            @elseif(preg_match("/{$search}/i", "Ohio Edison"))
                                                <p><strong><a href="https://www.utilitygasandpower.com/electricity-terms-condition-999" target="_blank">Ohio Edison Electricity Terms and Conditions</a></strong></p>
                                            @elseif(preg_match("/{$search}/i", "Toledo Edison"))
                                                <p><strong><a href="https://www.utilitygasandpower.com/electricity-terms-condition-999" target="_blank">Toledo Edison Electricity Terms and Conditions</a></strong></p>
                                            @endif
                                        </div>   
                                    @endif
                                    @if(strtolower($pr->utility->utilityCommodity->name) == "gas")
                                        <div class="col-md-12 col-xs-12 tc-link">
                                            @if(preg_match("/{$search}/i", "Columbia Gas of Ohio"))
                                                <p><strong><a href="https://www.utilitygasandpower.com/cohnatgas-terms" target="_blank">Columbia Gas of Ohio Natural Gas Terms and Conditions</a></strong></p>
                                            @elseif(preg_match("/{$search}/i", "Dominion East Ohio"))
                                                <p><strong><a href="https://www.utilitygasandpower.com/dominion-nat-gas-terms" target="_blank">Dominion East Ohio Natural Gas Terms and Conditions</a></strong></p>
                                            @elseif(preg_match("/{$search}/i", "Duke Energy Ohio-Gas"))
                                                <p><strong><a href="https://www.utilitygasandpower.com/duke-nat-gas-terms" target="_blank">Duke Energy Natural Gas Terms and Conditions</a></strong></p>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                            @elseif($telesaleTmpClientId == config()->get('constants.CLIENT_BOLT_ENEGRY_CLIENT_ID'))
                            
                                <div id="bolt-t-and-c-div">
                                    
                                    @if($state == config()->get('constants.USA_STATE_ABBR.CA'))
                                        @php $isCalifornia = true; @endphp
                                        @if($language == config()->get('constants.LANGUAGES.SPANISH'))
                                            <div class="col-md-12 col-xs-12">
                                                @include('frontend.customer.ca_spanish_t_and_c',['isVisible' => false, 'date' => ''])
                                            </div>
                                        @else
                                            <div class="col-md-12 col-xs-12">
                                            @include('frontend.customer.ca_english_t_and_c',['isVisible' => false, 'date' => '']) 
                                            </div>
                                        @endif
                                    @elseif($state == config()->get('constants.USA_STATE_ABBR.IN'))
                                        <div class="col-md-12 col-xs-12">
                                            @include('frontend.customer.in_t_and_c', ['isVisible' => false, 'date' => '']) 
                                        </div>
                                    @else
                                        <div class="col-md-12 col-xs-12">
                                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="col-md-12 col-xs-12">
                                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>
                                </div>
                            @endif
                        </div>

                        <div class="row">
                            <form method="POST" action="{{route('signature.store',$telesaleTmpId)}}" method="POST" id="form-signature">
                                @csrf
                                <input type="hidden" name="signature" id="signature">
                                <input type="hidden" name="client_id" value="{{ $telesaleTmpClientId }}">
                                <input type="hidden" name="webip" value="{{ Request::ip() }}" >
                                @if($telesaleTmpClientId == config()->get('constants.CLIENT_LE_CLIENT_ID'))
                                @foreach($programs as $k => $pr)
                                @if(!empty($pr->utility->utilityCommodity))
                                    @if(strtolower($pr->utility->utilityCommodity->name) == "gas")
                                    <input type="hidden" name="gas-hidden" class="gas-hidden-class" value="gas">
                                    <div class="col-xs-12 col-sm-12 col-md-12 mt-15">
                                        <div class="form-group">
                                            <input class="styled-checkbox" id="gas-tc-id-1" name="gas-tc-name-1" type="checkbox" value="1">
                                            <label class="agree-text" for="gas-tc-id-1">The representative stated he/she was representing a retail natural gas supplier and was not from the natural gas company?</label>
                                        </div>
                                        <div id="gas-tc-1-error"></div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12 mt-15">
                                        <div class="form-group">
                                            <input class="styled-checkbox" id="gas-tc-id-2" name="gas-tc-name-2" type="checkbox" value="1">
                                            <label class="agree-text" for="gas-tc-id-2">​ The representative explained that by signing the enrollment form you were entering an agreement/contract for retail natural gas supplier to supply your natural gas?</label>
                                        </div>
                                        <div id="gas-tc-2-error"></div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12 mt-15">
                                        <div class="form-group">
                                            <input class="styled-checkbox" id="gas-tc-id-3" name="gas-tc-name-3" type="checkbox" value="1">
                                            <label class="agree-text" for="gas-tc-id-3">​ The representative explained the price for natural gas under the contract you signed is ​ ${{$pr->rate}} per {{$pr->unit_of_measure}} , plus sales tax.</label>
                                        </div>
                                        <div id="gas-tc-3-error"></div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12 mt-15">
                                        <div class="form-group">
                                            <input class="styled-checkbox" id="gas-tc-id-4" name="gas-tc-name-4" type="checkbox" value="1">
                                            <label class="agree-text" for="gas-tc-id-4">The representative explained that the contract term is for ​ {{$pr->term}}​ Months.</label>
                                        </div>
                                        <div id="gas-tc-4-error"></div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12 mt-15">
                                        <div class="form-group">
                                            <input class="styled-checkbox" id="gas-tc-id-5" name="gas-tc-name-5" type="checkbox" value="1">
                                            <label class="agree-text" for="gas-tc-id-5">The representative explained your right to cancel?</label>
                                        </div>
                                        <div id="gas-tc-5-error"></div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12 mt-15">
                                        <div class="form-group">
                                            <input class="styled-checkbox" id="gas-tc-id-6" name="gas-tc-name-6" type="checkbox" value="1">
                                            <label class="agree-text" for="gas-tc-id-6">​ The representative left two completed right to cancel notices with you?</label>
                                        </div>
                                        <div id="gas-tc-6-error"></div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12 mt-15">
                                        <div class="form-group">
                                            <input class="styled-checkbox" id="gas-tc-id-7" name="gas-tc-name-7" type="checkbox" value="1">
                                            <label class="agree-text" for="gas-tc-id-7">​ Did the representative disclose whether an early termination liability fee would apply if you cancel the contract before the expiration of the contract term? If such a fee does apply to your contract, did the representative disclose the amount of the fee?</label>
                                        </div>
                                        <div id="gas-tc-7-error"></div>
                                    </div>
                                @endif
                                @endif
                                @endforeach
                                @endif
                                <div class="col-xs-12 col-sm-12 col-md-12 mt-15">
                                    <div class="form-group" id="t-and-c-checkbox">    
                                        <input class="styled-checkbox" id="agree" name="agree" type="checkbox" value="1">
                                        @if($state == config()->get('constants.USA_STATE_ABBR.CA'))
                                            @if($language == config()->get('constants.LANGUAGES.SPANISH'))
                                                <label class="agree-text" for="agree">Al firmar abajo, reconozco y acepto lo anterior también, que soy el titular de la cuenta y deseo firmar este Acuerdo con Bolt Energy.</label>
                                            @else
                                                <label class="agree-text" for="agree">By signing below, I acknowledge and agree to the above also, that I am the account holder and I desire to enter into this Agreement with Bolt Energy.</label>
                                            @endif
                                        @elseif($state == config()->get('constants.USA_STATE_ABBR.IN'))
                                            <label class="agree-text" for="agree">By signing below, I acknowledge and agree to the above also, that I am the account holder and I desire to enter into this Agreement with Bolt Energy.</label>
                                        @else
                                            <label class="agree-text" for="agree">I agree to the terms and conditions</label>
                                        @endif
                                    </div>
                                    <div id="agree-error"></div>
                                </div>
                                <div class="col-md-12 col-xs-12">
                                <label class="prog-title" id="first_signature_label"> Signature:</label>
                                    <!-- <div class="sigPad" id="smoothed">
                                        <canvas class="pad" id="smoothed"></canvas>
                                    </div> -->

                                    <!--new--sign-pad--->
                                    <div id="first_signature_div">
                                        <div id="signature-pad" class="m-signature-pad--body">
                                            <canvas></canvas>
                                            <div class="signature-error">
                                                @if ($errors->has('signature'))
                                                <span class="help-block">
                                                    {{ $errors->first('signature') }}
                                                </span>
                                                @endif
                                            </div>
                                            
                                            <button class="btn btn-red btn-sm mr15 mt15" type="button" id="clear" data-action="clear">Clear Signature</button>
                                            @if($isCalifornia)
                                            <button class="btn btn-green btn-sm mt15" type="button" id="next">Next</button>
                                            @else
                                            <button class="btn btn-green btn-sm mt15" type="submit">Save</button>
                                            @endif
                                        </div>
                                    </div>
                                    <!--new--sign-pad--end--->

                                    @if($isCalifornia)
                                        
                                        <div id="second_signature_div" class="signature-hide">
                                            <label class="prog-title">Acknowledgement </label>  
                                            <div id="ack_signature_div">
                                                
                                                @if($language == config()->get('constants.LANGUAGES.SPANISH'))
                                                    <p style="font-size: 14px;">
                                                        Reconozco que al firmar este contrato o acuerdo, estoy optando voluntariamente por cambiar la entidad que me suministra el servicio de gas natural.
                                                    </p>
                                                    <!-- <p>Al firmar a continuación, reconozco y acepto lo anterior también, que soy el titular de la cuenta y deseo celebrar este Acuerdo con Bolt Energy. </p> -->
                                                    <p>Nombre del cliente : {{ $customerName ?? ''}}</p>
                                                    <input type="hidden" name="language" value="{{config()->get('constants.LANGUAGES.SPANISH')}}">
                                                @else
                                                    <p style="font-size: 14px;">
                                                        I acknowledge that in signing this contract or agreement, I am voluntarily choosing to change the entity that supplies me with natural gas service.
                                                    </p>
                                                    {{-- <p>By signing below, I acknowledge and agree to the above also, that I am the account holder and I desire to enter into this Agreement with Bolt Energy. </p> --}}
                                                    <p>Customer Name : {{ $customerName ?? ''}}</p>
                                                    <input type="hidden" name="language" value="{{config()->get('constants.LANGUAGES.ENGLISH')}}">
                                                @endif
                                                <!-- <p>Date : {{ $customerName ?? ''}}</p> -->
                                                <input type="hidden" name="client_id" value="{{ $telesaleTmpClientId }}">
                                                <input type="hidden" name="customer_name" value="{{ $customerName ?? ''}}">
                                                <input type="hidden" name="ack_signature" id="ack_signature">
                                                <label class="prog-title"> Signature:</label>
                                                <div id="ack-signature-pad" class="m-signature-pad--body">
                                                    <img  style="outline: 2px solid #aaa;width: 100%" />
                                                    <div class="ack-signature-error">
                                                    </div>
                                                    <!-- <button class="btn btn-red btn-sm mr15 mt15" type="button" data-action="clear">Clear Signature</button> -->
                                                    <button class="btn btn-green btn-sm mt15" type="submit">Save</button>
                                                </div>
                                            </div>
                                        </div>

                                        
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('plugin/signature/js/jquery.signaturepad.js') }}"></script>
<script src="{{ asset('plugin/signature/js/numeric-1.2.6.min.js') }}"></script>
<script src="{{ asset('plugin/signature/js/bezier.js') }}"></script>
<script src="{{ asset('plugin/signature/js/json2.min.js') }}"></script>
<script src="{{ asset('js/jquery.ui.touch-punch.min.js') }}"></script>
<script type="text/javascript">
    (function(window) {
        $(document).ready(function() {
            
            $('#signature-pad').signaturePad({
                drawOnly: true,
                //defaultAction: 'drawIt',
                // validateFields: false,
                lineWidth: 0,
                // output: null,
                // sigNav: null,
                // typed: null,
                //clear: '#reset',
                // typeIt: null,
                // drawIt: null,
                // typeItDesc: null,
                // drawItDesc: null,
                drawBezierCurves: true,
                bezierSkip: 1,
                lineTop: 0,
                onFormError: function(errors, context, settings) {
                    $('.signature-error').html("");
                    console.log(errors);
                    if (errors.drawInvalid) {
                        $('.signature-error').html("<span class='help-block' >Please enter signature</span>");
                    }
                }

            });

            // $('#ack-signature-pad').signaturePad({
            //     drawOnly: true,
            //     //defaultAction: 'drawIt',
            //     // validateFields: false,
            //     lineWidth: 0,
            //     // output: null,
            //     // sigNav: null,
            //     // typed: null,
            //     //clear: '#reset',
            //     // typeIt: null,
            //     // drawIt: null,
            //     // typeItDesc: null,
            //     // drawItDesc: null,
            //     drawBezierCurves: true,
            //     bezierSkip: 1,
            //     lineTop: 0,
            //     onFormError: function(errors, context, settings) {
            //         $('.ack-signature-error').html("");
            //         console.log(errors);
            //         if (errors.drawInvalid) {
            //             $('.ack-signature-error').html("<span class='help-block' >Please enter signature</span>");
            //         }
            //     }

            // });
            // $('#clear').click(function() {
            //     $('#signature-pad').signaturePad().clearCanvas();
            // });
            $('#form-signature').submit(function() {
                $('#agree-error').html("");
                let gas_error_1_flag = false;
                let gas_error_2_flag = false;
                let gas_error_3_flag = false;
                let gas_error_4_flag = false;
                let gas_error_5_flag = false;
                let gas_error_6_flag = false;
                let gas_error_7_flag = false;
                $('#gas-tc-1-error').html("");
                $('#gas-tc-2-error').html("");
                $('#gas-tc-3-error').html("");
                $('#gas-tc-4-error').html("");
                $('#gas-tc-5-error').html("");
                $('#gas-tc-6-error').html("");
                $('#gas-tc-7-error').html("");
                $("#signature").val("");
                @if($telesaleTmpClientId == config()->get('constants.CLIENT_LE_CLIENT_ID'))
                if($('.gas-hidden-class').val() == 'gas'){

                    if (!$("#gas-tc-id-1").is(":checked")) {
                        $('#gas-tc-1-error').html("<span class='help-block' >This fields is required</span>");
                        gas_error_1_flag = true;
                        // return false;
                    }
                    if (!$("#gas-tc-id-2").is(":checked")) {
                        $('#gas-tc-2-error').html("<span class='help-block' >This fields is required</span>");
                        gas_error_2_flag = true;
                        // return false;
                    }
                    if (!$("#gas-tc-id-3").is(":checked")) {
                        $('#gas-tc-3-error').html("<span class='help-block' >This fields is required</span>");
                        gas_error_3_flag = true;
                        // return false;
                    }
                    if (!$("#gas-tc-id-4").is(":checked")) {
                        $('#gas-tc-4-error').html("<span class='help-block' >This fields is required</span>");
                        gas_error_4_flag = true;
                        // return false;
                    }
                    if (!$("#gas-tc-id-5").is(":checked")) {
                        $('#gas-tc-5-error').html("<span class='help-block' >This fields is required</span>");
                        gas_error_5_flag = true;
                        // return false;
                    }
                    if (!$("#gas-tc-id-6").is(":checked")) {
                        $('#gas-tc-6-error').html("<span class='help-block' >This fields is required</span>");
                        gas_error_6_flag = true;
                        // return false;
                    }
                    if (!$("#gas-tc-id-7").is(":checked")) {
                        $('#gas-tc-7-error').html("<span class='help-block' >This fields is required</span>");
                        gas_error_7_flag = true;
                        // return false;
                    }
                }
                @endif
                if (!$("#agree").is(":checked")) {
                    $('#agree-error').html("<span class='help-block' >This fields is required</span>");
                    return false;
                }
                if(gas_error_1_flag == false && gas_error_2_flag == false &&gas_error_3_flag == false && gas_error_4_flag == false && gas_error_5_flag == false && gas_error_6_flag == false && gas_error_7_flag == false){
                }
                else
                {
                    return false;
                }

                //var imageData = $('#signature-pad').signaturePad().getSignatureImage();

                var imageData = signaturePad.toDataURL('image/png');
                $("#signature").val(imageData);

                @if($isCalifornia)
                    // var ackData = signaturePad2.toDataURL('image/png');
                    $("#ack_signature").val(true);
                @endif

                return true; // returning true submits the form. 
            });

            $('#next').on('click', function(event) {
                if (!$("#agree").is(":checked")) {
                    $('#agree-error').html("<span class='help-block' >This fields is required</span>");
                    return false;
                }
                $('#agree-error').html("");       
                if (signaturePad.isEmpty()) {
                    $('.signature-error').html("<span class='help-block' >Please enter signature</span>");
                    return false;
                }
                $("#ack-signature-pad img").attr('src',signaturePad.toDataURL()); 
                $('.signature-error').html("");
                $('#first_signature_div').hide();
                $('#first_signature_label').hide();
                $('#bolt-t-and-c-div').hide();
                $('#t-and-c-label').hide();
                $('#t-and-c-checkbox').hide();
                $('#second_signature_div').removeClass('signature-hide');
                $('#card-header-div').hide();
                $('#name-div').hide();
                $('#email-div').hide();
                $('#address-div,#address-div2').hide();
                $('.program-div').hide();
                $('#phone-number-div').hide();
                $(window).scrollTop(0);
            });
        });
    }(this));
</script>


<!------other--new---signature-pad-------->

<script>
    // MODIFIED version 4

    /*!
     * Signature Pad v1.6.0-beta.6
     * https://github.com/szimek/signature_pad
     *
     * Copyright 2017 Szymon Nowak
     * Released under the MIT license
     *
     * The main idea and some parts of the code (e.g. drawing variable width Bézier curve) are taken from:
     * http://corner.squareup.com/2012/07/smoother-signatures.html
     *
     * Implementation of interpolation using cubic Bézier curves is taken from:
     * http://benknowscode.wordpress.com/2012/09/14/path-interpolation-using-cubic-bezier-and-control-point-estimation-in-javascript
     *
     * Algorithm for approximated length of a Bézier curve is taken from:
     * http://www.lemoda.net/maths/bezier-length/index.html
     *
     */

    (function(global, factory) {
        typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
            typeof define === 'function' && define.amd ? define(factory) :
            (global.SignaturePad = factory());
    }(this, (function() {
        'use strict';

        function Point(x, y, time) {
            this.x = x;
            this.y = y;
            this.time = time || new Date().getTime();
        }

        Point.prototype.velocityFrom = function(start) {
            return this.time !== start.time ? this.distanceTo(start) / (this.time - start.time) : 1;
        };

        Point.prototype.distanceTo = function(start) {
            return Math.sqrt(Math.pow(this.x - start.x, 2) + Math.pow(this.y - start.y, 2));
        };

        function Bezier(startPoint, control1, control2, endPoint) {
            this.startPoint = startPoint;
            this.control1 = control1;
            this.control2 = control2;
            this.endPoint = endPoint;
        }

        // Returns approximated length.
        Bezier.prototype.length = function() {
            var steps = 10;
            var length = 0;
            var px = void 0;
            var py = void 0;

            for (var i = 0; i <= steps; i += 1) {
                var t = i / steps;
                var cx = this._point(t, this.startPoint.x, this.control1.x, this.control2.x, this.endPoint.x);
                var cy = this._point(t, this.startPoint.y, this.control1.y, this.control2.y, this.endPoint.y);
                if (i > 0) {
                    var xdiff = cx - px;
                    var ydiff = cy - py;
                    length += Math.sqrt(xdiff * xdiff + ydiff * ydiff);
                }
                px = cx;
                py = cy;
            }

            return length;
        };

        /* eslint-disable no-multi-spaces, space-in-parens */
        Bezier.prototype._point = function(t, start, c1, c2, end) {
            return start * (1.0 - t) * (1.0 - t) * (1.0 - t) + 3.0 * c1 * (1.0 - t) * (1.0 - t) * t + 3.0 * c2 * (1.0 - t) * t * t + end * t * t * t;
        };

        /* eslint-disable */

        // http://stackoverflow.com/a/27078401/815507
        function throttle(func, wait, options) {
            var context, args, result;
            var timeout = null;
            var previous = 0;
            if (!options) options = {};
            var later = function later() {
                previous = options.leading === false ? 0 : Date.now();
                timeout = null;
                result = func.apply(context, args);
                if (!timeout) context = args = null;
            };
            return function() {
                var now = Date.now();
                if (!previous && options.leading === false) previous = now;
                var remaining = wait - (now - previous);
                context = this;
                args = arguments;
                if (remaining <= 0 || remaining > wait) {
                    if (timeout) {
                        clearTimeout(timeout);
                        timeout = null;
                    }
                    previous = now;
                    result = func.apply(context, args);
                    if (!timeout) context = args = null;
                } else if (!timeout && options.trailing !== false) {
                    timeout = setTimeout(later, remaining);
                }
                return result;
            };
        }

        function SignaturePad(canvas, options) {
            var self = this;
            var opts = options || {};

            this.velocityFilterWeight = opts.velocityFilterWeight || 0.7;
            this.minWidth = opts.minWidth || 0.5;
            this.maxWidth = opts.maxWidth || 2.5;
            this.throttle = opts.throttle || 0;

            if (this.throttle) {
                this._strokeMoveUpdate = throttle(SignaturePad.prototype._strokeUpdate, this.throttle);
            } else {
                this._strokeMoveUpdate = SignaturePad.prototype._strokeUpdate;
            }

            this.dotSize = opts.dotSize || function() {
                return (this.minWidth + this.maxWidth) / 2;
            };

            this.penColor = opts.penColor || 'black';
            this.backgroundColor = opts.backgroundColor || 'rgba(0,0,0,0)';
            this.onBegin = opts.onBegin;
            this.onEnd = opts.onEnd;
            this.lastUpdateTimeStamp = null;

            this._canvas = canvas;
            this._ctx = canvas.getContext('2d');
            this.clear();

            // We need add these inline so they are available to unbind while still having
            // access to 'self' we could use _.bind but it's not worth adding a dependency.
            this._handleMouseDown = function(event) {
                if (event.which === 1) {
                    self._mouseButtonDown = true;
                    self._strokeBegin(event);
                }
            };

            this._handleMouseMove = function(event) {
                if (self._mouseButtonDown) {
                    self._strokeMoveUpdate(event, true);
                }
            };

            this._handleMouseUp = function(event) {
                if (event.which === 1 && self._mouseButtonDown) {
                    self._mouseButtonDown = false;
                    self._strokeEnd(event);
                }
            };

            this._handleTouchStart = function(event) {
                if (event.targetTouches.length === 1) {
                    var touch = event.changedTouches[0];
                    self._strokeBegin(touch);
                }
            };

            this._handleTouchMove = function(event) {
                // Prevent scrolling.
                event.preventDefault();

                var touch = event.targetTouches[0];
                self._strokeMoveUpdate(touch, true);
            };

            this._handleTouchEnd = function(event) {
                var wasCanvasTouched = event.target === self._canvas;
                if (wasCanvasTouched) {
                    event.preventDefault();
                    self._strokeEnd(event);
                }
            };

            // Enable mouse and touch event handlers
            this.on();
        }

        // Public methods
        SignaturePad.prototype.clear = function() {
            var ctx = this._ctx;
            var canvas = this._canvas;

            ctx.fillStyle = this.backgroundColor;
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            this._data = [];
            this._reset();
            this._isEmpty = true;
        };

        SignaturePad.prototype.fromDataURL = function(dataUrl) {
            var _this = this;

            var image = new Image();
            var ratio = window.devicePixelRatio || 1;
            var width = this._canvas.width / ratio;
            var height = this._canvas.height / ratio;

            this._reset();
            image.src = dataUrl;
            image.onload = function() {
                _this._ctx.drawImage(image, 0, 0, width, height);
            };
            this._isEmpty = false;
        };

        SignaturePad.prototype.toDataURL = function(type) {
            var _canvas;

            switch (type) {
                case 'image/svg+xml':
                    return this._toSVG();
                default:
                    for (var _len = arguments.length, options = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
                        options[_key - 1] = arguments[_key];
                    }

                    return (_canvas = this._canvas).toDataURL.apply(_canvas, [type].concat(options));
            }
        };

        SignaturePad.prototype.on = function() {
            this._handleMouseEvents();
            this._handleTouchEvents();
        };

        SignaturePad.prototype.off = function() {
            this._canvas.removeEventListener('mousedown', this._handleMouseDown);
            this._canvas.removeEventListener('mousemove', this._handleMouseMove);
            document.removeEventListener('mouseup', this._handleMouseUp);

            this._canvas.removeEventListener('touchstart', this._handleTouchStart);
            this._canvas.removeEventListener('touchmove', this._handleTouchMove);
            this._canvas.removeEventListener('touchend', this._handleTouchEnd);
        };

        SignaturePad.prototype.isEmpty = function() {
            return this._isEmpty;
        };

        // Private methods
        SignaturePad.prototype._strokeBegin = function(event) {
            this._data.push([]);
            this._reset();
            this._strokeUpdate(event);

            if (typeof this.onBegin === 'function') {
                this.onBegin(event);
            }
        };

        SignaturePad.prototype._strokeUpdate = function(event) {
            var x = event.clientX;
            var y = event.clientY;

            var point = this._createPoint(x, y);

            var _addPoint = this._addPoint(point),
                curve = _addPoint.curve,
                widths = _addPoint.widths;

            if (curve && widths) {
                this._drawCurve(curve, widths.start, widths.end);
            }

            this._data[this._data.length - 1].push({
                x: point.x,
                y: point.y,
                time: point.time
            });
        };

        SignaturePad.prototype._strokeEnd = function(event) {
            var canDrawCurve = this.points.length > 2;
            var point = this.points[0];

            if (!canDrawCurve && point) {
                this._drawDot(point);
            }

            if (typeof this.onEnd === 'function') {
                this.onEnd(event);
            }
        };

        SignaturePad.prototype._handleMouseEvents = function() {
            this._mouseButtonDown = false;

            this._canvas.addEventListener('mousedown', this._handleMouseDown);
            this._canvas.addEventListener('mousemove', this._handleMouseMove);
            document.addEventListener('mouseup', this._handleMouseUp);
        };

        SignaturePad.prototype._handleTouchEvents = function() {
            // Pass touch events to canvas element on mobile IE11 and Edge.
            this._canvas.style.msTouchAction = 'none';
            this._canvas.style.touchAction = 'none';

            this._canvas.addEventListener('touchstart', this._handleTouchStart);
            this._canvas.addEventListener('touchmove', this._handleTouchMove);
            this._canvas.addEventListener('touchend', this._handleTouchEnd);
        };

        SignaturePad.prototype._reset = function() {
            this.points = [];
            this._lastVelocity = 0;
            this._lastWidth = (this.minWidth + this.maxWidth) / 2;
            this._ctx.fillStyle = this.penColor;
        };

        SignaturePad.prototype._createPoint = function(x, y, time) {
            var rect = this._canvas.getBoundingClientRect();

            return new Point(x - rect.left, y - rect.top, time || new Date().getTime());
        };

        SignaturePad.prototype._addPoint = function(point) {
            var points = this.points;
            var tmp = void 0;

            points.push(point);

            if (points.length > 2) {
                // To reduce the initial lag make it work with 3 points
                // by copying the first point to the beginning.
                if (points.length === 3) points.unshift(points[0]);

                tmp = this._calculateCurveControlPoints(points[0], points[1], points[2]);
                var c2 = tmp.c2;
                tmp = this._calculateCurveControlPoints(points[1], points[2], points[3]);
                var c3 = tmp.c1;
                var curve = new Bezier(points[1], c2, c3, points[2]);
                var widths = this._calculateCurveWidths(curve);

                // Remove the first element from the list,
                // so that we always have no more than 4 points in points array.
                points.shift();

                return {
                    curve: curve,
                    widths: widths
                };
            }

            return {};
        };

        SignaturePad.prototype._calculateCurveControlPoints = function(s1, s2, s3) {
            var dx1 = s1.x - s2.x;
            var dy1 = s1.y - s2.y;
            var dx2 = s2.x - s3.x;
            var dy2 = s2.y - s3.y;

            var m1 = {
                x: (s1.x + s2.x) / 2.0,
                y: (s1.y + s2.y) / 2.0
            };
            var m2 = {
                x: (s2.x + s3.x) / 2.0,
                y: (s2.y + s3.y) / 2.0
            };

            var l1 = Math.sqrt(dx1 * dx1 + dy1 * dy1);
            var l2 = Math.sqrt(dx2 * dx2 + dy2 * dy2);

            var dxm = m1.x - m2.x;
            var dym = m1.y - m2.y;

            var k = l2 / (l1 + l2);
            var cm = {
                x: m2.x + dxm * k,
                y: m2.y + dym * k
            };

            var tx = s2.x - cm.x;
            var ty = s2.y - cm.y;

            return {
                c1: new Point(m1.x + tx, m1.y + ty),
                c2: new Point(m2.x + tx, m2.y + ty)
            };
        };

        SignaturePad.prototype._calculateCurveWidths = function(curve) {
            var startPoint = curve.startPoint;
            var endPoint = curve.endPoint;
            var widths = {
                start: null,
                end: null
            };

            var velocity = this.velocityFilterWeight * endPoint.velocityFrom(startPoint) + (1 - this.velocityFilterWeight) * this._lastVelocity;

            var newWidth = this._strokeWidth(velocity);

            widths.start = this._lastWidth;
            widths.end = newWidth;

            this._lastVelocity = velocity;
            this._lastWidth = newWidth;

            return widths;
        };

        SignaturePad.prototype._strokeWidth = function(velocity) {
            return Math.max(this.maxWidth / (velocity + 1), this.minWidth);
        };

        SignaturePad.prototype._drawPoint = function(x, y, size) {
            var ctx = this._ctx;

            ctx.moveTo(x, y);
            ctx.arc(x, y, size, 0, 2 * Math.PI, false);
            this._isEmpty = false;
        };

        // Debug
        SignaturePad.prototype.drawDataAsPoints = function(size, fill) {
            var ctx = this._ctx;
            ctx.save();

            var length = this._data.length;
            var i = void 0;
            var j = void 0;
            var x = void 0;
            var y = void 0;

            if (length) {
                for (i = 0; i < length; i += 1) {
                    for (j = 0; j < this._data[i].length; j += 1) {
                        var point = this._data[i][j];
                        x = point.x;
                        y = point.y;
                        ctx.moveTo(x, y);
                        ctx.arc(x, y, size || 5, 0, 2 * Math.PI, false);
                        ctx.fillStyle = fill || 'rgba(255, 0, 0, 0.2)';
                        ctx.fill();
                    }
                }
            }

            ctx.restore();
        };

        SignaturePad.prototype._drawMark = function(x, y, size, fill) {
            var ctx = this._ctx;
            ctx.save();
            ctx.moveTo(x, y);
            ctx.arc(x, y, size || 5, 0, 2 * Math.PI, false);
            ctx.fillStyle = fill || 'rgba(255, 0, 0, 0.2)';
            ctx.fill();
            ctx.restore();
        };

        SignaturePad.prototype._drawCurve = function(curve, startWidth, endWidth) {
            var ctx = this._ctx;
            var widthDelta = endWidth - startWidth;
            var drawSteps = Math.floor(curve.length());

            ctx.beginPath();

            for (var i = 0; i < drawSteps; i += 1) {
                // Calculate the Bezier (x, y) coordinate for this step.
                var t = i / drawSteps;
                var tt = t * t;
                var ttt = tt * t;
                var u = 1 - t;
                var uu = u * u;
                var uuu = uu * u;

                var x = uuu * curve.startPoint.x;
                x += 3 * uu * t * curve.control1.x;
                x += 3 * u * tt * curve.control2.x;
                x += ttt * curve.endPoint.x;

                var y = uuu * curve.startPoint.y;
                y += 3 * uu * t * curve.control1.y;
                y += 3 * u * tt * curve.control2.y;
                y += ttt * curve.endPoint.y;

                var width = startWidth + ttt * widthDelta;
                this._drawPoint(x, y, width);
            }

            ctx.closePath();
            ctx.fill();
        };

        SignaturePad.prototype._drawDot = function(point) {
            var ctx = this._ctx;
            var width = typeof this.dotSize === 'function' ? this.dotSize() : this.dotSize;

            ctx.beginPath();
            this._drawPoint(point.x, point.y, width);
            ctx.closePath();
            ctx.fill();
        };

        SignaturePad.prototype._fromData = function(pointGroups, drawCurve, drawDot) {
            for (var i = 0; i < pointGroups.length; i += 1) {
                var group = pointGroups[i];

                if (group.length > 1) {
                    for (var j = 0; j < group.length; j += 1) {
                        var rawPoint = group[j];
                        var point = new Point(rawPoint.x, rawPoint.y, rawPoint.time);

                        if (j === 0) {
                            // First point in a group. Nothing to draw yet.
                            this._reset();
                            this._addPoint(point);
                        } else if (j !== group.length - 1) {
                            // Middle point in a group.
                            var _addPoint2 = this._addPoint(point),
                                curve = _addPoint2.curve,
                                widths = _addPoint2.widths;

                            if (curve && widths) {
                                drawCurve(curve, widths);
                            }
                        } else {
                            // Last point in a group. Do nothing.
                        }
                    }
                } else {
                    this._reset();
                    var _rawPoint = group[0];
                    drawDot(_rawPoint);
                }
            }
        };

        SignaturePad.prototype._toSVG = function() {
            var _this2 = this;

            var pointGroups = this._data;
            var canvas = this._canvas;
            var minX = 0;
            var minY = 0;
            var maxX = canvas.width;
            var maxY = canvas.height;
            var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');

            svg.setAttributeNS(null, 'width', canvas.width);
            svg.setAttributeNS(null, 'height', canvas.height);

            this._fromData(pointGroups, function(curve, widths) {
                var path = document.createElementNS('http;//www.w3.org/2000/svg', 'path');

                // Need to check curve for NaN values, these pop up when drawing
                // lines on the canvas that are not continuous. E.g. Sharp corners
                // or stopping mid-stroke and than continuing without lifting mouse.
                if (!isNaN(curve.control1.x) && !isNaN(curve.control1.y) && !isNaN(curve.control2.x) && !isNaN(curve.control2.y)) {
                    var attr = 'M ' + curve.startPoint.x.toFixed(3) + ',' + curve.startPoint.y.toFixed(3) + ' ' + ('C ' + curve.control1.x.toFixed(3) + ',' + curve.control1.y.toFixed(3) + ' ') + (curve.control2.x.toFixed(3) + ',' + curve.control2.y.toFixed(3) + ' ') + (curve.endPoint.x.toFixed(3) + ',' + curve.endPoint.y.toFixed(3));

                    path.setAttribute('d', attr);
                    path.setAttributeNS(null, 'stroke-width', (widths.end * 2.25).toFixed(3));
                    path.setAttributeNS(null, 'stroke', _this2.penColor);
                    path.setAttributeNS(null, 'fill', 'none');
                    path.setAttributeNS(null, 'stroke-linecap', 'round');

                    svg.appendChild(path);
                }
            }, function(rawPoint) {
                var circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                var dotSize = typeof _this2.dotSize === 'function' ? _this2.dotSize() : _this2.dotSize;
                circle.setAttributeNS(null, 'r', dotSize);
                circle.setAttributeNS(null, 'cx', rawPoint.x);
                circle.setAttributeNS(null, 'cy', rawPoint.y);
                circle.setAttributeNS(null, 'fill', _this2.penColor);

                svg.appendChild(circle);
            });

            var prefix = 'data:image/svg+xml;base64,';
            var header = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="' + minX + ' ' + minY + ' ' + maxX + ' ' + maxY + '">';
            var body = svg.innerHTML;
            var footer = '</svg>';
            var data = header + body + footer;

            return prefix + btoa(data);
        };

        SignaturePad.prototype.fromData = function(pointGroups) {
            var _this3 = this;

            this.clear();

            this._fromData(pointGroups, function(curve, widths) {
                return _this3._drawCurve(curve, widths.start, widths.end);
            }, function(rawPoint) {
                return _this3._drawDot(rawPoint);
            });
        };

        SignaturePad.prototype.toData = function() {
            return this._data;
        };

        return SignaturePad;

    })));



    var wrapper = document.getElementById("signature-pad"),
        clearButton = wrapper.querySelector("[data-action=clear]"),
        debugPointsButton = wrapper.querySelector("[data-action=debug-points]"),
        savePNGButton = wrapper.querySelector("[data-action=save-png]"),
        saveSVGButton = wrapper.querySelector("[data-action=save-svg]"),
        canvas = wrapper.querySelector("canvas"),
        signaturePad;

    // Adjust canvas coordinate space taking into account pixel ratio,
    // to make it look crisp on mobile devices.
    // This also causes canvas to be cleared.
    function resizeCanvas() {
        // When zoomed out to less than 100%, for some very strange reason,
        // some browsers report devicePixelRatio as less than 1
        // and only part of the canvas is cleared then.
        var ratio = Math.max(window.devicePixelRatio || 1, 1);
        let offsetWidth = canvas.offsetWidth;
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = ((offsetWidth / 3)) * ratio;
        canvas.getContext("2d").scale(ratio, ratio);

        // Do this for the ack form as well for bolt energy
        // var canvas2 = document.getElementById("ack-signature-pad").querySelector("canvas")
        // var ratio2 = Math.max(window.devicePixelRatio || 1, 1);
        // let offsetWidth2 = canvas2.offsetWidth;
        // canvas2.width = canvas2.offsetWidth * ratio2;
        // canvas2.height = ((offsetWidth2 / 3)) * ratio2;
        // canvas2.getContext("2d").scale(ratio2, ratio2);
    }

    //window.onresize = resizeCanvas;
    setTimeout(() => {
        resizeCanvas();
    }, 1000);

    signaturePad = new SignaturePad(canvas, {
        throttle: 16 // x milli seconds
    });

    clearButton.addEventListener("click", function(event) {
        signaturePad.clear();
        $('#signature-pad').signaturePad().clearCanvas();
    });

    // debugPointsButton.addEventListener("click", function(event) {
    //     signaturePad.drawDataAsPoints();
    // });

    // savePNGButton.addEventListener("click", function(event) {
    //     if (signaturePad.isEmpty()) {
    //         alert("Please provide signature first.");
    //     } else {
    //         window.open(signaturePad.toDataURL());
    //     }
    // });

    // saveSVGButton.addEventListener("click", function(event) {
    //     if (signaturePad.isEmpty()) {
    //         alert("Please provide signature first.");
    //     } else {
    //         window.open(signaturePad.toDataURL('image/svg+xml'));
    //     }
    // });

    /* 
    // no need to acknowledge signature
    var wrapper2 = document.getElementById("ack-signature-pad"),
        clearButton2 = wrapper2.querySelector("[data-action=clear]"),
        canvas2 = wrapper2.querySelector("canvas"),
        signaturePad2;

    // Adjust canvas coordinate space taking into account pixel ratio,
    // to make it look crisp on mobile devices.
    // This also causes canvas to be cleared.
    function resizeCanvas2() {
        // When zoomed out to less than 100%, for some very strange reason,
        // some browsers report devicePixelRatio as less than 1
        // and only part of the canvas is cleared then.
        let ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas2.width = canvas2.offsetWidth * ratio;
        canvas2.height = canvas2.offsetHeight * ratio;
        canvas2.getContext("2d").scale(ratio, ratio);
    }

    //window.onresize = resizeCanvas;
    resizeCanvas2();

    signaturePad2 = new SignaturePad(canvas2, {
        throttle: 16 // x milli seconds
    });

    clearButton2.addEventListener("click", function(event) {
        signaturePad2.clear();
        $('#ack-signature-pad').signaturePad().clearCanvas();
    });*/

    
    // jQuery('#ack_signature_div').toggle('show');

</script>



@endpush