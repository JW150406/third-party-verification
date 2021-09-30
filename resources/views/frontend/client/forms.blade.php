@extends('layouts.app')
@section('content')
<style>
    .space-none {
        margin-top: 15px;
    }

    .cont_bx3 .pdlr0 {
        padding-left: 0px;
        padding-right: 0px;
    }

    .popover{
        width:250px;
        left: auto !important;
        right: 0;
        transition: none;
    }
    .popover-content {
        /* overflow: scroll; */
        max-height: 140px; 
        padding: 0;
    }
    .popover.bottom .arrow {
        left:90% !important;
    }
    .pointer {
        cursor: pointer;
    }
    .copy-from {
        margin-top: 8px;
        float: right;
    }
    .copy-container {
        padding: 0px;
        margin-bottom: 0px;
    }
    .copy-container li {
        border-bottom: 1px solid #c7babac4;
        padding: 3px;
        /* min-height: 40px; */
    }
    .copy-container li:last-child{
        border-bottom: none;
    }
    .copy-container li:hover {
        background-color: #f4f5f9;
    }
    .copy-container h5 {
        color: #000;
        padding: 0 3px;
    }
    .copy-container span{
        padding: 0 3px;
    }
    .separator {
        display: flex;
        align-items: center;
        text-align: center;
    }
    .separator::before, .separator::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #d7d7d7;
    }
    .separator::before {
        margin-right: .25em;
    }
    .separator::after {
        margin-left: .25em;
    }
    .mt32 {
        margin-top: 32px;
    }
</style>
<script>
    var serviceAndBillingElements = [];

    function changeVal(sourceElement, destElement, i) {
        if (serviceAndBillingElements.indexOf(parseInt(i)) != -1) {
            $("#" + destElement + i).val($("#" + sourceElement + i).val());
        }
    }
</script>
{{--<script src="{{ asset('/js/parsley.min.js') }}"></script>--}}
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC49bXfihl4zZqjG2-iRLUmcWO_PVcDehM&libraries=places"></script>
@php 
    $input = request()->all();
    $multienrollmentIncrement = 0;
@endphp 
<div class="tpv-contbx edit-agentinfo">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3">
                    <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx client-new-tabs pdlr0">
                        @if ($error = Session::get('error'))
                        <div class="alert alert-danger">
                            <strong> {{ $error }}</strong>
                        </div>
                        @endif
                        <div class="client-bg-white">
                            <div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
                                <h1>{{$form->formname}}</h1>
                            </div>
                            <div class="sales_tablebx">
                                <!-- Nav tabs -->
                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <!--agent details starts-->
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6 col-sm-6">
                                            <div class="agent-detailform create-lead-page" id="agent-detailform-id">
                                                <form method="post" id="leadForm" role="form" action="{{ route('client.contact.from_post', [$client->id, $form->id]) }}" autocomplete="on" data-parsley-validate>
                                                    @csrf
                                                    @if(isset($clonedData['parent_id']) && !empty($clonedData['parent_id']))
                                                    <input type="hidden" name="parent_id" value="{{ $clonedData['parent_id'] }}">
                                                    @endif
                                                    <input type="hidden" name="multi_enrollment" value="{{ $form->multienrollment }}">
                                                    <input type="hidden" name="total_enrollment" id="total_enrollment" value="{{ count($clonedChildData)+1 }}">
                                                    <input type="hidden" name="is_enrollment_by_state" value="@if(isset($input['state']) && !empty($input['state'])) 1 @else {{ $is_enrollment_by_state ?? 0 }} @endif">
                                                    
                                                    <div class="input-group mb10">
                                                        <!-- <input type="text" autocomplete="off" {{--onfocus="this.setAttribute('autocomplete', 'new-password')"--}} class="form-control zipcodefield typeahead" name="zipcode" id="zipcode" placeholder="Please enter zipcode" value="{{ old('zipcode') ? old('zipcode') : $zipcode }}" data-parsley-trigger="focusout" data-parsley-pattern="[0-9]{5}" data-parsley-pattern-message="Please enter 5 digit zipcode" data-parsley-required='true' > -->
                                                        @if(isset($fields) && empty($fields))
                                                        <div class="row">
                                                            <div class="col-xs-1 col-md-1 col-sm-1 mt32">
                                                                <div class="form-group radio-btns pdt0">
                                                                    @if(isEnableEnrollByState($client->id))
                                                                    <label class="radio-inline">
                                                                        <input id="search_type_zipcode" type="radio" name="serch_type" value="zipcode" checked>
                                                                    </label>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-xs-10 col-md-10 col-sm-10">
                                                                <label class="control-label">Enter zip code</label>
                                                                <div class="input-group mb10 col-md-12 col-sm-12">
                                                                    <input type="text"
                                                                            autocomplete="off"
                                                                            {{--onfocus="this.setAttribute('autocomplete', 'new-password')"--}}
                                                                            class="form-control zipcodefield"
                                                                            name="zipcode" id="zipcode"
                                                                            placeholder="Please enter zipcode"
                                                                            value="{{ old('zipcode') ? old('zipcode') : $zipcode }}"
                                                                            data-parsley-trigger="change"
                                                                            data-parsley-pattern="[0-9]{5}"
                                                                            data-parsley-pattern-message="Zip code must be 5 digits"
                                                                            data-parsley-required='true'
                                                                            @if(isset($fields) && !empty($fields))
                                                                            readonly
                                                                            @endif
                                                                        >
                                                                    @if($errors->has('zipcode'))
                                                                        <small class="form-text text-muted error">{{ $errors->first('zipcode') }}</small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @else
                                                            @if(isset($fields) && !empty($fields) && isset($input['state']) && !empty($input['state']))
                                                                <label class="control-label">Select state</label>
                                                            @else
                                                                <label class="control-label">Enter zip code</label>
                                                            @endif
                                                            <div class="input-group mb10">
                                                                @if(isset($fields) && !empty($fields) && isset($input['state']) && !empty($input['state']))
                                                                    <div class="input-group mb10 col-md-12 col-sm-12">
                                                                        <select class="select2 form-control typeahead" id="state" name="state" disabled>
                                                                            <option value="">Select</option>
                                                                            @foreach($states as $state)
                                                                                <option value="{{$state->state}}" @if(isset($input['state']) && $input['state'] == $state->state) selected @endif>{{$state->state}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <input type="hidden" id="zipcode" name="zipcode">
                                                                @else                                                                    
                                                                    <input type="text"
                                                                        autocomplete="off"
                                                                        {{--onfocus="this.setAttribute('autocomplete', 'new-password')"--}}
                                                                        class="form-control zipcodefield typeahead"
                                                                        name="zipcode" id="zipcode"
                                                                        placeholder="Please enter zipcode"
                                                                        value="{{ old('zipcode') ? old('zipcode') : $zipcode }}"
                                                                        data-parsley-trigger="change"
                                                                        data-parsley-pattern="[0-9]{5}"
                                                                        data-parsley-pattern-message="Zip code must be 5 digits"
                                                                        data-parsley-required='true'
                                                                        @if(isset($fields) && !empty($fields))
                                                                        readonly
                                                                        @endif
                                                                    >
                                                                @endif
                                                                @if($errors->has('zipcode'))
                                                                    <small class="form-text text-muted error">{{ $errors->first('zipcode') }}</small>
                                                                @endif
                                                                <span class="input-group-btn">
                                                                    @empty(request('lid'))
                                                                        @if(isset($fields) && !empty($fields))
                                                                            <button class="btn btn-default searchzipcode " type="button" id="edit-btn" style="border-radius: 3px;">Edit</button>
                                                                            <button class="btn btn-default searchzipcode" type="button" id="next-btn" style="display: none; border-radius: 3px;">Update</button>
                                                                        @else
                                                                            <!-- <button class="btn btn-default searchzipcode" type="button" id="next-btn">Next</button> -->
                                                                        @endif                                                                    
                                                                    @else
                                                                        <input type="hidden" name="zipcode" value="{{ $zipcode ?? '' }}">
                                                                        <button class="btn btn-default searchzipcode submitBtn" type="button">Next</button>
                                                                    @endempty
                                                                </span>
                                                            </div>
                                                        @endif
                                                        @if(isset($states) && isEnableEnrollByState($client->id) && empty($fields))
                                                            <div class="row">
                                                                <div class="col-xs-1 col-md-1 col-sm-1">
                                                                </div>
                                                                <div class="col-xs-10 col-md-10 col-sm-10">
                                                                    <div class="input-group mb10 separator">OR</div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-xs-1 col-md-1 col-sm-1 mt32">
                                                                    <div class="form-group radio-btns pdt0">
                                                                        <label class="radio-inline">
                                                                            <input id="search_type_state"  type="radio" name="serch_type" value="state">
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-xs-10 col-md-10 col-sm-10">
                                                                    <label class="control-label">Select state</label>
                                                                    <div class="input-group mb10 col-md-12 col-sm-12">
                                                                        <select class="select2 form-control " id="state" name="state">
                                                                            <option value="">Select</option>
                                                                            @foreach($states as $state)
                                                                                <option value="{{$state->state}}">{{$state->state}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if(isset($fields) && empty($fields))
                                                            <div class="row">
                                                                <div class="col-xs-1 col-md-1 col-sm-1">
                                                                </div>
                                                                <div class="col-xs-10 col-md-10 col-sm-10">
                                                                    <div class="input-group mb10">
                                                                        <button class="btn btn-default searchzipcode" type="button" id="next-btn">Next</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="multi-lead-section">
                                                        @if(isset($commodities) && !empty($commodities))
                                                        <!-- START code for repeating utility and program as many commodities selected while creating lead form -->
                                                            @foreach($commodities as $commodity)
															
                                                            <input type="hidden" name="" id="firstComId" value="{{$commodities[0]->id}}">

                                                                <div class="form-group">
                                                                    <label class="control-label"
                                                                           for="utilityoptions_{{ $commodity->id }}_{{ $multienrollmentIncrement }}">Select {{ $commodity->name }}
                                                                        Utility</label>
                                                                    <select class="select2 form-control validate required utilityoptions"
                                                                            id="utilityoptions_{{ $commodity->id }}_{{ $multienrollmentIncrement }}"
                                                                            name="utility[{{ $multienrollmentIncrement }}][{{$commodity->id}}]"
                                                                            title="Please select utility"
                                                                            data-parsley-trigger="focusout"
                                                                            data-parsley-required='true'
                                                                            data-parsley-errors-container="#select2-utilityform-error-message"
                                                                            >                                                                        
                                                                        <option value="">Select</option>
                                                                        @if(count($commodity->utilities) > 0)
                                                                            @foreach($commodity->utilities as $utility )
                                                                                <option value="{{$utility->utid}}" data-mapped-util= "{{$utility->mapped_utility}}"
                                                                                    {{ isset($clonedData[$commodity->name]) && !empty($clonedData[$commodity->name]) ? ($utility->utid == $clonedData[$commodity->name]['utility_selected_id'] ? 'selected' : '') : '' }}
                                                                                    >
                                                                                 {{$utility->fullname}}
                                                                            @endforeach
                                                                        @endif
                                                                    </select>
                                                                    <input type="hidden" id="brand_name" value="{{ (isset($clonedData[$commodity->name]) && isset($clonedData[$commodity->name]['brand_selected_name'])) ? $clonedData[$commodity->name]['brand_selected_name'] : '' }}">
                                                                    <span id="select2-utilityform-error-message"></span>
                                                                </div>

                                                                <!--new--design---->
                                                                <div class="form-group lead-select-parent">
 
                                                                    <label class="control-label" for="programOption_{{ $commodity->id }}_{{ $multienrollmentIncrement }}">Select {{ $commodity->name }}
                                                                        Program</label>

                                                                    <div class="form-control lead-select lead-select-custom">
                                                                        <span class="lead-select-arrow"></span>
                                                                        <div class="sel-value"></div>
                                                                        <div class="set-program" style="display: none">@if(isset($clonedData[$commodity->name]['programs']) && !empty($clonedData[$commodity->name]['programs']))
                                                                                <input type="hidden" name="program[{{ $multienrollmentIncrement }}][]" id="selectedProgramValue" value="{{ $clonedData[$commodity->name]['program_selected_id']}}">
                                                                            @endif</div>
                                                                        <ul class="" id="programOption_{{ $commodity->id }}_{{ $multienrollmentIncrement }}">

                                                                            @if(isset($clonedData[$commodity->name]['programs']) && !empty($clonedData[$commodity->name]['programs']))                                                                                
                                                                                <li class="lead-select-first-li">Select</li>
                                                                                @foreach($clonedData[$commodity->name]['programs'] as $program)

                                                                                <li>
                                                                                   <div class="utility-outer">
                                                                                      <h5>{{$program->customer_type}}</h5>
                                                                                      <p class="utility-sub-t">{{$program->name}} </p>
                                                                                      <div class="row">
                                                                                         <div class="col-md-3 col-sm-3 br2">
                                                                                            <p>Code</p>
                                                                                            <span class="program-code-text" title="{{$program->code}}">{{str_limit($program->code,15)}}</span>
                                                                                         </div>
                                                                                         <div class="col-md-3 col-sm-3 text-center">
                                                                                            <p>Rate</p>
                                                                                            <span>{{$program->rate}} @if(!empty($program->unit_of_measure)) ({{$program->unit_of_measure}}) @endif</span>
                                                                                         </div>
                                                                                         <div class="col-md-2 col-sm-2">
                                                                                            <p>Term</p>
                                                                                            <span>{{$program->term}}</span>
                                                                                         </div>
                                                                                         <div class="col-md-2 col-sm-2">
                                                                                            <p>MSF</p>
                                                                                            <span>${{$program->msf}}</span>
                                                                                         </div>
                                                                                         <div class="col-md-2 col-sm-2">
                                                                                            <p>ETF</p>
                                                                                            <span>${{$program->etf}}</span>
                                                                                         </div>
                                                                                      </div>
                                                                                      <br>
                                                                                    <div class="row" style="border-top:2px solid #1c5997;margin:0px;padding-top:10px;">
                                                                                        
                                                                                    @foreach ($customFields as $key => $field)
                                                                                    <div class="row">
                                                                                        <div class="col-md-2 col-sm-2">
                                                                                            <p>{{$field}}</p>
                                                                                        </div>
                                                                                        <div class="col-md-8 col-sm-8">
                                                                                            <span>{{ $program->$key }}</span>
                                                                                        </div>
                                                                                    </div>    
                                                                                    
                                                                                    @endforeach
                                                                                    </div>
                                                                                   </div>
                                                                                   <input class="program-id" type="hidden" value="{{$program->id}}">
                                                                                   <input class="current_multienrollment_number" type="hidden" value="{{$multienrollmentIncrement}}">
                                                                                </li>

                                                                                @endforeach
                                                                            @else
                                                                            <li class="lead-select-first-li">Select</li>
                                                                            @endif
                                                                        </ul>
                                                                        <div style="margin-top: -8px"> </div>    
                                                                    </div>
                                                                    <div class="program-error" style="margin-top: -8px"> </div>
                                                                </div>

                                                                <script type="text/javascript">
                                                                    function setProgramsInDropdown(programId,commodityId){
                                                                        console.log('In parent set program function');
                                                                        let res = $(programId).closest("ul").data("programs");

                                                                        if(res !=undefined){

                                                                            var programs = res.data;
                                                                            $(programId).closest("ul").data("commodity",res.commodity);

                                                                            var html = '<li>Select</li>';
                                                                            $('#brand_name').val(res.brand_name);
                                                                            $(programId).closest(".lead-select").find(".sel-value").html('');
                                                                            $(programId).closest(".lead-select").find(".set-program").html('');
                                                                            let comId = $('#firstComId').val();
                                                                            let parentProgramCode = $('#programOption_'+comId+'_{{ $multienrollmentIncrement }}').closest(".lead-select").find('.sel-value').find(".program-code-text").html();

                                                                            for (i = 0; i < res.totalrecords; i++) {
                                                                                var input='<input class="program-id" type="hidden" value="'+programs[i].id+'"><input class="current_multienrollment_number" type="hidden" value="{{$multienrollmentIncrement}}">';
                                                                                if(programs[i].UnitOfMeasureName != '') {
                                                                                var unit =' ('+programs[i].UnitOfMeasureName+')';
                                                                                } else {
                                                                                    var unit ='';
                                                                                }
                                                                                var fields = '';
                                                                                $.each(res.custom_fields, function( key, value ) {
                                                                                    fields += '<div class="row"><div class="col-md-2 col-sm-2"><p>'+value+'</p></div><div class="col-md-8 col-sm-8"><span>'+programs[i][key]+'</span></div></div>';
                                                                                });

                                                                                let customFields = '';
                                                                                
                                                                                if (fields != '') {
                                                                                    customFields = '<br><div class="row" style="border-top:2px solid #1c5997;margin:0px;padding-top:10px;">'+fields+'<div>';
                                                                                }

                                                                                var programCode = programs[i].ProgramCode;
                                                                                var shortProgCode = programCode;
                                                                                if (programCode.length > 15) {
                                                                                    shortProgCode = shortProgCode.substring(0,15) + "...";
                                                                                }
                                                                                @if( $client->id == config('constants.CLIENT_RRH_CLIENT_ID') && $commodities->count() > 1)
                                                                                
                                                                                    if(parentProgramCode != undefined){
                                                                                        let programsArray = @json(config()->get("constants.RRH_PROGRAM_MAPPING"));                                                                                
                                                                                        
                                                                                        let programCodeArray = programsArray[parentProgramCode];
                                                                                        if(programCodeArray != undefined){

                                                                                            if(programCodeArray.length > 0){
                                                                                                if(!programCodeArray.includes(programCode)){
                                                                                                    continue;
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                @endif

                                                                                html += '<li data-commodity-id="'+commodityId+'" data-multienrollment="{{$multienrollmentIncrement}}"> <div class="utility-outer"> <h5>'+programs[i].PremiseTypeName+'</h5> <p class="utility-sub-t">'+programs[i].ProgramName+' </p><div class="row"> <div class="col-md-3 col-sm-3 br2"> <p>Code</p><span class="program-code-text" title="'+programCode+'">'+shortProgCode+'</span> </div><div class="col-md-3 col-sm-3 text-center"><p>Rate</p> <span>$'+programs[i].Rate +unit +'</span></div><div class="col-md-2 col-sm-2"> <p>Term</p><span>'+programs[i].Term+'</span> </div><div class="col-md-2 col-sm-2"> <p>MSF</p><span>$'+programs[i].monthlysf+'</span> </div><div class="col-md-2 col-sm-2"> <p>ETF</p><span>$'+programs[i].earlyterminationfee+'</span> </div></div>'+customFields+'</div>'+input+'</li>';
                                                                            }
                                                                            $(programId).html(html);

                                                                            @if(count($commodities) > 1)
                                                                            $(".AC-No-"+res.commodity).attr('data-parsley-pattern',res.regex);
                                                                            $(".AC-No-"+res.commodity).attr('data-parsley-pattern-message',res.regex_message);
                                                                            // For Text customization of account number placeholder
                                                                            if(res.act_num_verbiage != ''){
                                                                                $(".AC-No-"+res.commodity).attr('placeholder',res.act_num_verbiage);
                                                                            }
                                                                            @else 
                                                                            $(".AC-No").attr('data-parsley-pattern',res.regex);
                                                                            $(".AC-No").attr('data-parsley-pattern-message',res.regex_message);
                                                                            // For Text customization of account number placeholder
                                                                            if(res.act_num_verbiage != ''){
                                                                                $(".AC-No").attr('placeholder',res.act_num_verbiage);
                                                                            }
                                                                            @endif
                                                                                
                                                                            $.each(res.utility_validations, function( key, value ) {
                                                                                $(".form-field-"+value.field_id).attr('data-parsley-pattern',value.regex);
                                                                                $(".form-field-"+value.field_id).attr('data-parsley-pattern-message',value.regex_message);
                                                                            });
                                                                        }
                                                                        
                                                                    }
                                                                    $(document).ready(function () {
                                                                        let commodity_utility = [];
                                                                        let commodity_list = [];
                                                                        var i = 0;

                                                                        $("#utilityoptions_{{ $commodity->id }}_{{ $multienrollmentIncrement }}").change(function () {
                                                                            $('#programOption_{{ $commodity->id }}_{{ $multienrollmentIncrement }}').closest(".lead-select").find(".sel-value").html('');
                                                                            $('#programOption_{{ $commodity->id }}_{{ $multienrollmentIncrement }}').closest(".lead-select").find(".set-program").html('');
                                                                            var com_id = $("#utilityoptions_{{ $commodity->id }}_{{ $multienrollmentIncrement }}").val();
                                                                            
                                                                            $('#programOption_{{ $commodity->id }}_{{ $multienrollmentIncrement }}').html('');
                                                                            $('#programOption_{{ $commodity->id }}_{{ $multienrollmentIncrement }}').closest(".lead-select").find(".sel-value").html('');
                                                                            $('#programOption_{{ $commodity->id }}_{{ $multienrollmentIncrement }}').closest(".lead-select").find(".set-program").html('');
                                                                            if (com_id !== '') {
                                                                                $.ajax({
                                                                                    url: "{{ route('ajax.getprograms') }}",
                                                                                    type: "POST",
                                                                                    // async:false,
                                                                                    data: {
                                                                                        'utility_id': com_id, form_id : '{{$form->id}}'
                                                                                    },
                                                                                    success: function (res) {

                                                                                        if (res.status === 'success') {
                                                                                            $('#programOption_{{ $commodity->id }}_{{ $multienrollmentIncrement }}').closest("ul").data("programs",res);
                                                                                            setProgramsInDropdown('#programOption_{{ $commodity->id }}_{{ $multienrollmentIncrement }}','{{ $commodity->id }}');
                                                                                            
                                                                                        } else {
                                                                                            var html = '<li>Select</li>';
                                                                                            $('#programOption_{{ $commodity->id }}_{{ $multienrollmentIncrement }}').html(html);
                                                                                            alert(res.message)
                                                                                        }
                                                                                    }
                                                                                })
                                                                            } else {
                                                                                var html = '<li>Select</li>';
                                                                                $('#programOption_{{ $commodity->id }}_{{ $multienrollmentIncrement }}').html(html);
                                                                            }

                                                                     

                                                                        //update next utility for within same group if parent is selected
                                                                        var util_id = $("#utilityoptions_{{ $commodity->id }}_{{ $multienrollmentIncrement }}").val();
                                                                        let mapped_utilities = [];
                                                                        mapped_utilities = $("#utilityoptions_{{ $commodity->id }}_{{ $multienrollmentIncrement }} option:selected").attr('data-mapped-util');
                                                                        
                                                                        if(typeof mapped_utilities == "string" && mapped_utilities != '') {
                                                                            // console.log('in if');
                                                                            mapped_utilities = JSON.parse(mapped_utilities);
                                                                            if(Array.isArray(mapped_utilities) && mapped_utilities.length) {
                                                                                // console.log('mapped_utilities', mapped_utilities);
                                                                                // html = '<option value="">Select</option>';
                                                                                // commodity_utility = [];
                                                                                // commodity_list = [];
                                                                               
                                                                                $.each(mapped_utilities, function( key, utility ) {
                                                                                    commodity_id = utility.commodity_id;
                                                                                    if (typeof commodity_utility[commodity_id] == 'undefined'){

                                                                                        commodity_utility[commodity_id]= [];
                                                                                    }
                                                                                    if (typeof commodity_list[i] == 'undefined' /*&& !commodity_list.indexOf(commodity_id)*/){
                                                                                        commodity_list[i] = commodity_id;
                                                                                        i++;
                                                                                    }

                                                                                    commodity_utility[commodity_id][0] = "#utilityoptions_"+ commodity_id+"_{{ $multienrollmentIncrement }}"; 
                                                                                    if (typeof commodity_utility[commodity_id][1] == 'undefined' || key == 0) {
                                                                                        commodity_utility[commodity_id][1] = '<option value="">Select</option>';
                                                                                        if (typeof commodity_utility[commodity_id][2] == 'undefined') {
                                                                                            // console.log('overwrite');
                                                                                            commodity_utility[commodity_id][2] = $(commodity_utility[commodity_id][0]).html();
                                                                                        }
                                                                                    }
                                                                                    commodity_utility[commodity_id][1]  +='<option value="'+utility.utid+'" ';
                                                                                    
                                                                                    commodity_utility[commodity_id][1]  +='>';
                                                                                    commodity_utility[commodity_id][1]  += utility.fullname;
                                                                                    commodity_utility[commodity_id][1]  +='</option>';
                                                                                });
                                                                                    // console.log('commodity_utility ',commodity_utility);
                                                                                    // console.log('commodity_list ',commodity_list);
                                                                                if(commodity_list.length){      
                                                                                    $.each(commodity_list, function( key, c_id ) {
                                                                                        // console.log('in commodity_utility ',commodity_utility[c_id], c_id);
                                                                                        $(commodity_utility[c_id][0]).html(commodity_utility[c_id][1]);
                                                                                    });
                                                                                }

                                                                            }
                                                                        } else if (util_id == "" && commodity_utility.length && commodity_list.length ) {
                                                                            // console.log('in else');
                                                                            $.each(commodity_list, function( key, c_id ) {
                                                                                // console.log('in commodity_utility 22 ',commodity_utility[c_id], c_id);
                                                                                $(commodity_utility[c_id][0]).html(commodity_utility[c_id][2]);
                                                                            });

                                                                        }
                                                                       // console.log ('inn change', util_id, mapped_utilities);
                                                                        //update next utility for within same group if parent is selected end
                                                                    });
                                                                });


                                                                    function getutilityValidations(){
                                                                        var com_id = $("#utilityoptions_{{ $commodity->id }}_{{ $multienrollmentIncrement }}").val();
                                                                        if (com_id !== '') {
                                                                            $.ajax({
                                                                                url: "{{ route('ajax.getprograms') }}",
                                                                                type: "POST",
                                                                                async:false,
                                                                                data: {
                                                                                    'utility_id': com_id, form_id : '{{$form->id}}'
                                                                                },
                                                                                success: function (res) {

                                                                                    if (res.status === 'success') {
                                                                                        var html = '';
                                                                                        var programs = res.data;

                                                                                        @if(count($commodities) > 1)
                                                                                        $(".AC-No-"+res.commodity).attr('data-parsley-pattern',res.regex);
                                                                                        $(".AC-No-"+res.commodity).attr('data-parsley-pattern-message',res.regex_message);
                                                                                        // For Text customization of account number placeholder
                                                                                        if(res.act_num_verbiage != ''){
                                                                                            $(".AC-No-"+res.commodity).attr('placeholder',res.act_num_verbiage);
                                                                                        }
                                                                                        @else 
                                                                                        $(".AC-No").attr('data-parsley-pattern',res.regex);
                                                                                        $(".AC-No").attr('data-parsley-pattern-message',res.regex_message);
                                                                                        // For Text customization of account number placeholder
                                                                                        if(res.act_num_verbiage != ''){
                                                                                            $(".AC-No").attr('placeholder',res.act_num_verbiage);
                                                                                        }
                                                                                        @endif
                                                                                         
                                                                                        $.each(res.utility_validations, function( key, value ) {
                                                                                            $(".form-field-"+value.field_id).attr('data-parsley-pattern',value.regex);
                                                                                            $(".form-field-"+value.field_id).attr('data-parsley-pattern-message',value.regex_message);
                                                                                        });
                                                                                        $('.lead-select').addClass("open");


                                                                                    } else {
                                                                                        alert(res.message)
                                                                                    }
                                                                                }
                                                                            });
                                                                        }    
                                                                    }

                                                                </script>

                                                            @endforeach
                                                        <!-- END code for repeating utility and program as many commodities selected while creating lead form -->
                                                        @endif
                                                        
                                                        @if(isset($fields) && !empty($fields))
                                                            <?php $i = 0; ?>
                                                            <input type="hidden" id="lead_from_input" name="lead_from" value="1">
                                                            <input type="hidden" name="multienrollmentValues[]" value="{{$multienrollmentIncrement}}">
                                                            <div class="margin-bottom-18"></div>
                                                            
                                                            @foreach($fields as $field)

                                                                <input type="hidden" name="fields[{{$multienrollmentIncrement}}][{{$i}}][field_type]"
                                                                       value="{{ $field->type }}">
                                                                <input type="hidden" name="fields[{{$multienrollmentIncrement}}][{{$i}}][field_id]"
                                                                       value="{{ $field->id }}">
                                                                <div class="form-group fg30">
                                                                    @if($field->type == 'fullname')
                                                                        <label class="control-label {{$field->type}}-label" copy-from="{{$field->type.$i}}">{{ ucfirst(array_get($field, 'label')) }}</label>
                                                                        
                                                                        @if($field->is_allow_copy == 1)
                                                                            @include('frontend.user.copy_from',['label'=> $field->type.'-label' , 'copyTo' => $field->type.$i])
                                                                        @endif
                                                                        <div class="row {{$field->type.$i}}">
                                                                            <div class="col-sm-4">
                                                                                <input class="form-control"
                                                                                       type="text"
                                                                                       name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][first_name]"
                                                                                       placeholder="First Name"
                                                                                       autocomplete="new"
                                                                                       data-parsley-pattern = "/^[#A-Za-z0-9_'\s.\-,&@$%?!~*;+/\Ã§ Ã‡ Ã£ Ãƒ Ãµ Ã• ÃŠ Ãª Ã‚ Ã¢ Ã” Ã´ Ã¡ Ã  Ã Ã€ Ã© Ã¨ Ãˆ Ã‰ Ã Ã¬ Ã­ ÃŒ Ã® Ã» Ã‘]{1,50}$/"data-parsley-pattern-message="This field must only contain letters"
                                                                                       data-parsley-trigger="focusout"
                                                                                       @if(array_get($field, 'is_required') == 1) data-parsley-required='true'

                                                                                       @endif 
                                                                                       @if(isset($input['fields'][$i]['value']['first_name']))
                                                                                       value = "{{$input['fields'][$i]['value']['first_name']}}"
                                                                                       @else 
                                                                                       value="{{ isset($clonedData[$field->id]['first_name']) && !empty($clonedData[$field->id]['first_name']) ? $clonedData[$field->id]['first_name'] : '' }}"
                                                                                       @endif
                                                                                       {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                                       >
                                                                            </div>
                                                                            <div class="col-sm-4">
                                                                                <input type="text"
                                                                                       name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][middle_initial]"
                                                                                       placeholder="Middle Name"
                                                                                       class="form-control"
                                                                                       autocomplete="new"
                                                                                       data-parsley-trigger="focusout"
                                                                                       data-parsley-pattern = "/^[#A-Za-z0-9_'\s.\-,&@$%?!~*;+/\Ã§ Ã‡ Ã£ Ãƒ Ãµ Ã• ÃŠ Ãª Ã‚ Ã¢ Ã” Ã´ Ã¡ Ã  Ã Ã€ Ã© Ã¨ Ãˆ Ã‰ Ã Ã¬ Ã­ ÃŒ Ã® Ã» Ã‘]{1,50}$/"
                                                                                       {{--data-parsley-pattern="/^[a-zA-Z]+$/"--}}
                                                                                       data-parsley-pattern-message="This field must only contain letters" 
                                                                                       @if(isset($input['fields'][$i]['value']['middle_initial']))
                                                                                       value = "{{$input['fields'][$i]['value']['middle_initial']}}"
                                                                                       @else 
                                                                                       value="{{ isset($clonedData[$field->id]['middle_initial']) && !empty($clonedData[$field->id]['middle_initial']) ? $clonedData[$field->id]['middle_initial'] : '' }}"
                                                                                       @endif
                                                                                       {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                                       >
                                                                            </div>
                                                                            <div class="col-sm-4">
                                                                                <input type="text"
                                                                                       name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][last_name]" 
                                                                                       @if(isset($input['fields'][$i]['value']['last_name']))
                                                                                       value = "{{$input['fields'][$i]['value']['last_name']}}"
                                                                                       @else 
                                                                                       value="{{ isset($clonedData[$field->id]['last_name']) && !empty($clonedData[$field->id]['last_name']) ? $clonedData[$field->id]['last_name'] : '' }}"
                                                                                       @endif
                                                                                       placeholder="Last Name"
                                                                                       class="form-control"
                                                                                       autocomplete="new"
                                                                                       data-parsley-trigger="focusout"
                                                                                       data-parsley-pattern = "/^[#A-Za-z0-9_'\s.\-,&@$%?!~*;+/\Ã§ Ã‡ Ã£ Ãƒ Ãµ Ã• ÃŠ Ãª Ã‚ Ã¢ Ã” Ã´ Ã¡ Ã  Ã Ã€ Ã© Ã¨ Ãˆ Ã‰ Ã Ã¬ Ã­ ÃŒ Ã® Ã» Ã‘]{1,50}$/"
                                                                                       {{--data-parsley-pattern="/^[a-zA-Z]+$/"--}}
                                                                                       data-parsley-pattern-message="This field must only contain letters"
                                                                                       @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                                                                        @endif
                                                                                    {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                                >
                                                                            </div>
                                                                        </div>

                                                                        <input type="hidden" name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][is_primary]" value="{{$field->is_primary}}" />

                                                                    @elseif($field->type == 'separator')
                                                                        <br>
                                                                        <hr class="separator-hr">
                                                                        <br>

                                                                    @elseif($field->type == 'textbox')
                                                                        
                                                                        @php 
                                                                            $search = config('constants.ACCOUNT_NUMBER_LABEL');
                                                                            $label = ucfirst(array_get($field, 'label'));
                                                                            $accountLabelMatch = preg_match("/{$search}/i", $label);
                                                                            if($accountLabelMatch) {
                                                                                $labels = $label;
                                                                                $labels = explode("-",$label);
                                                                                // if(isset($labels[0])) {
                                                                                //     $label = $labels[0];
                                                                                // }
                                                                            }

                                                                        @endphp 
                                            
                                                                        <label class="control-label {{$field->type}}-label" copy-from="{{$field->type.$i}}">{{ $label }}</label>
                                                                        
                                                                        @if($field->is_allow_copy == 1)
                                                                            @include('frontend.user.copy_from',['label'=> $field->type.'-label' , 'copyTo' => $field->type.$i])
                                                                        @endif
                                                                        <div class="{{$field->type.$i}}">
                                                                        
                                                                            <input type="text"
                                                                                autocomplete="off"

                                                                                class="form-control form-field-{{$field->id}} @if($accountLabelMatch) AC-No @else form-fields @endif @if(isset($labels[1]) && !empty($labels[1])) {{ 'AC-No-'.trim($labels[1]) }} @endif"
                                                                                name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][value]" 

                                                                                @if(isset($input['fields'][$i]['value']['value']))
                                                                                    value = "{{$input['fields'][$i]['value']['value']}}"
                                                                                @else 
                                                                                    value="{{ isset($clonedData[$field->id]['value']) && !empty($clonedData[$field->id]['value']) ? $clonedData[$field->id]['value'] : '' }}" 
                                                                                @endif
                                                                                placeholder="{{ $field->meta['placeholder'] }}"
                                                                                data-parsley-trigger="focusout"
                                                                                @if(array_get($field, 'is_required') == 1) data-parsley-required='true'

                                                                                @endif
                                                                                @if(!empty($field->regex))
                                                                                data-parsley-pattern="{{$field->regex}}"
                                                                                @endif
                                                                                @if(!empty($field->regex_message))
                                                                                data-parsley-pattern-message="{{$field->regex_message}}" @endif
                                                                                {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                            >
                                                                        </div>

                                                                    @elseif($field->type == 'textarea')
                                                                        <label class="control-label {{$field->type}}-label" copy-from="{{$field->type.$i}}">{{ ucfirst(array_get($field, 'label')) }}</label>
                                                                        @if($field->is_allow_copy == 1)
                                                                            @include('frontend.user.copy_from',['label'=> $field->type.'-label' , 'copyTo' => $field->type.$i])
                                                                        @endif
                                                                        
                                                                        <div class="{{$field->type.$i}}">
                                                                            <?php $textAreaValue = '';?>
                                                                            @if(isset($input['fields'][$i]['value']['value']))
                                                                            <?php $textAreaValue = $input['fields'][$i]['value']['value'];?>
                                                                            @else 
                                                                                @if(isset($clonedData[$field->id]['value']) && !empty($clonedData[$field->id]['value']) )                                                                                    
                                                                                    <?php $textAreaValue = $clonedData[$field->id]['value'];?>
                                                                                @endif
                                                                            @endif

                                                                            <textarea

                                                                                class="form-control form-fields form-field-{{$field->id}}"
                                                                                name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][value]"
                                                                                placeholder="{{ $field->name }}"
                                                                                data-parsley-trigger="focusout"
                                                                                @if(array_get($field, 'is_required') == 1) data-parsley-required='true'

                                                                                @endif
                                                                                @if(!empty($field->regex))
                                                                                data-parsley-pattern="{{$field->regex}}"
                                                                                @endif
                                                                                @if(!empty($field->regex_message))
                                                                                data-parsley-pattern-message="{{$field->regex_message}}" @endif
                                                                                {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                                >{{ $textAreaValue }}</textarea>
                                                                        </div>

                                                                    @elseif($field->type == 'radio')
                                                                        <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                                                                        @foreach($field->meta as $meta)

                                                                            @foreach($meta as $option)

                                                                                <div class="form-group radio-btns pdt0">
                                                                                    <label class="radio-inline">
                                                                                        <input type="radio"
                                                                                               name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][value]"
                                                                                               data-parsley-trigger="focusout"
                                                                                               @if(array_get($field, 'is_required') == 1) data-parsley-required='true'

                                                                                               @endif
                                                                                               value="{{ $option['option'] }}" 
                                                                                                @if(isset($input['fields'][$i]['value']['value']))
                                                                                                "checked"
                                                                                                @else
                                                                                                {{ isset($clonedData[$field->id]['value']) && !empty($clonedData[$field->id]['value']) ? $clonedData[$field->id]['value'] == $option['option'] ? 'checked' : '' : '' }}
                                                                                                @endif
                                                                                                > {{ $option['option'] }} 
                                                                                    </label>

                                                                                </div>



                                                                            @endforeach
                                                                            <div id="radio_button_{{array_get($field, 'id')}}"></div>
                                                                        @endforeach

                                                                    @elseif($field->type == 'checkbox')
                                                                        <input type="hidden"
                                                                               name="fields[{{$multienrollmentIncrement}}][{{$i}}][field_type]"
                                                                               value="{{ $field->type }}">
                                                                        <input type="hidden"
                                                                               name="fields[{{$multienrollmentIncrement}}][{{$i}}][field_id]"
                                                                               value="{{ $field->id }}">
                                                                        <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                                                                        @foreach($field->meta as $meta)

                                                                            @foreach($meta as $option)

                                                                                <div class="form-group checkbx">
                                                                                    <label class="checkbx-style">
                                                                                        <input type="checkbox"
                                                                                               class="{{ array_get($field, 'is_required' )==1 ? 'required' : '' }}" 
                                                                                               name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][]"
                                                                                               value="{{ $option['option'] }}"
                                                                                               data-parsley-trigger="focusout"
                                                                                               @if(array_get($field, 'is_required') == 1) data-parsley-mincheck="1" @endif
                                                                                               @if(isset($input['fields'][$i]['value'][0]))
                                                                                               "checked"
                                                                                               @else
                                                                                                {{ (isset($clonedData[$field->id]['value']) && !empty($clonedData[$field->id]['value'])) ? (in_array($option['option'], explode(', ', $clonedData[$field->id]['value']))) ? 'checked' : '' : '' }}
                                                                                                @endif
                                                                                                > {{ $option['option'] }}
                                                                                        <span class="checkmark"></span>
                                                                                    </label>
                                                                                </div>

                                                                            @endforeach
                                                                            <div id="checkbox_error_{{array_get($field, 'id')}}"></div>
                                                                        @endforeach

                                                                    @elseif($field->type == 'address')
                                                                        <div class="form-group" rel="address">
                                                                            <label class="control-label {{$field->type}}-label" copy-from="{{$field->type.$i}}">{{ ucfirst(array_get($field, 'label')) }}</label>
                                                                            @if($field->is_allow_copy == 1)
                                                                                @include('frontend.user.copy_from',['label'=> $field->type.'-label' , 'copyTo' => $field->type.$i])
                                                                            @endif
                                                                            <div class="row {{$field->type.$i}}">
                                                                                <div class="address-block">
                                                                                    <div class="col-sm-12 address-field">
                                                                                        <input  type="text" 
                                                                                                autocomplete="off"
                                                                                                id="address_line_1_{{$i}}_{{$multienrollmentIncrement}}"
                                                                                                onfocus="this.setAttribute('autocomplete', 'new-password')"
                                                                                                class="form-control autocompletestreet address"
                                                                                                Placeholder="Address line 1"
                                                                                                name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][address_1]" @if(isset($input['fields'][$i]['value']['address_1']))
                                                                                                value="{{ $input['fields'][$i]['value']['address_1'] }}" 
                                                                                                @else
                                                                                                value="{{ isset($clonedData[$field->id]['address_1']) && !empty($clonedData[$field->id]['address_1']) ? $clonedData[$field->id]['address_1'] : '' }}" 
                                                                                                @endif
                                                                                                data-parsley-trigger="focusout"
                                                                                                @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                                                                                @endif
                                                                                                {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                                        >
                                                                                    </div>
                                                                                    <div class="col-sm-12 address-field">
                                                                                        <input autocomplete="new-password"
                                                                                                type="text"
                                                                                               id="address_line_2_{{$i}}_{{$multienrollmentIncrement}}"
                                                                                               class="form-control autocompletestreet address"
                                                                                               Placeholder="Apartment, suite, unit, building, floor, etc. (optional)"
                                                                                               name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][address_2]" @if(isset($input['fields'][$i]['value']['address_2']))
                                                                                                    value="{{ $input['fields'][$i]['value']['address_2'] }}" 
                                                                                                @else
                                                                                                    value="{{ isset($clonedData[$field->id]['address_2']) && !empty($clonedData[$field->id]['address_2']) ? $clonedData[$field->id]['address_2'] : '' }}"
                                                                                                @endif
                                                                                                {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                                               >
                                                                                    </div>
                                                                                    <!-- <div class="col-sm-4 address-field">
                                                                                        <input autocomplete="new-password"
                                                                                                type="text"
                                                                                               id="address_unit_{{$i}}"
                                                                                               class="form-control autocompletestreet address"
                                                                                               Placeholder="Unit Number"
                                                                                               name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][unit]"
                                                                                                @if(isset($input['fields'][$i]['value']['unit']))
                                                                                                    value="{{ $input['fields'][$i]['value']['unit'] }}" 
                                                                                                @else
                                                                                                    value="{{ isset($clonedData[$field->id]['unit']) && !empty($clonedData[$field->id]['unit']) ? $clonedData[$field->id]['unit'] : '' }}"
                                                                                                @endif
                                                                                                {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                                               >
                                                                                    </div> -->
                                                                                    <div class="col-sm-3 address-field inline-block">
                                                                                        <input autocomplete="new-password" 
                                                                                                type="text"
                                                                                               id="address_city_{{$i}}_{{$multienrollmentIncrement}}"
                                                                                               class="form-control"
                                                                                               placeholder="City"
                                                                                               name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][city]"
                                                                                                @if(isset($input['fields'][$i]['value']['city']))
                                                                                                    value="{{ $input['fields'][$i]['value']['city'] }}" 
                                                                                                @else
                                                                                                    value="{{ isset($clonedData[$field->id]['city']) && !empty($clonedData[$field->id]['city']) ? $clonedData[$field->id]['city'] : '' }}"
                                                                                                @endif 
                                                                                                {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!} 
                                                                                                >
                                                                                    </div>
                                                                                    <!-- Start : Div for county for address tag -->
                                                                                    <div class="col-sm-3 address-field inline-block">
                                                                                        <input autocomplete="new-password" 
                                                                                                type="text"
                                                                                               id="address_county_{{$i}}_{{$multienrollmentIncrement}}"
                                                                                               class="form-control"
                                                                                               placeholder="County"
                                                                                               name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][county]"
                                                                                                @if(isset($input['fields'][$i]['value']['county']))
                                                                                                    value="{{ $input['fields'][$i]['value']['county'] }}" 
                                                                                                @else
                                                                                                    value="{{ isset($clonedData[$field->id]['county']) && !empty($clonedData[$field->id]['county']) ? $clonedData[$field->id]['county'] : '' }}"
                                                                                                @endif
                                                                                                {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}   
                                                                                                >
                                                                                    </div>
                                                                                    <!-- End  -->
                                                                                    <div class="col-sm-3 address-field inline-block">

                                                                                        <input id="address_state_{{$i}}_{{$multienrollmentIncrement}}"
                                                                                               class="form-control stateall statefield address @if(array_get($field, 'is_primary')) state-field @endif"
                                                                                               type="text"
                                                                                               Placeholder="State"
                                                                                               autocomplete="new-password"
                                                                                               name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][state]" 
                                                                                                @if(isset($input['fields'][$i]['value']['state']))
                                                                                                    value="{{ $input['fields'][$i]['value']['state']}}" 
                                                                                                @else
                                                                                                    value="{{ isset($clonedData[$field->id]['state']) && !empty($clonedData[$field->id]['state']) ? $clonedData[$field->id]['state'] : '' }}"
                                                                                                @endif
                                                                                                short-name="@if(isset($input['fields'][$i]['value']['short_state'])) {{ $input['fields'][$i]['value']['short_state']}} @endif"
                                                                                                {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}        
                                                                                        >
                                                                                    </div>
                                                                                    <div class="col-sm-3 address-field inline-block">
                                                                                        <input class="form-control @if(array_get($field, 'is_primary')) zipcode-field @endif"
                                                                                                type="text"
                                                                                               id="address_zipcode_{{$i}}_{{$multienrollmentIncrement}}"
                                                                                               name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][zipcode]"
                                                                                               autocomplete="new-password"
                                                                                               Placeholder="Zipcode"
                                                                                               data-parsley-trigger="focusout"
                                                                                               data-parsley-pattern="[0-9]{5}"
                                                                                               data-parsley-pattern-message="Please enter 5 digit zipcode" 

                                                                                                @if(isset($input['fields'][$i]['value']['zipcode']))
                                                                                                    value="{{ $input['fields'][$i]['value']['zipcode']}}" 
                                                                                                @else
                                                                                                    value="{{ isset($clonedData[$field->id]['zipcode']) && !empty($clonedData[$field->id]['zipcode']) ? $clonedData[$field->id]['zipcode'] : '' }}"
                                                                                                @endif
                                                                                                >
                                                                                    </div>
                                                                                    <div class="col-sm-3 address-field inline-block">
                                                                                        <input id="address_country_{{$i}}_{{$multienrollmentIncrement}}" style="display: none;"
                                                                                                type="text"
                                                                                               class="form-control stateall statefield address"
                                                                                               Placeholder="Country"
                                                                                               autocomplete="new-password"
                                                                                               name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][country]" 
                                                                                                @if(isset($input['fields'][$i]['value']['country']))
                                                                                                    value="{{ $input['fields'][$i]['value']['country']}}" 
                                                                                                @else
                                                                                                    value="{{ isset($clonedData[$field->id]['country']) && !empty($clonedData[$field->id]['country']) ? $clonedData[$field->id]['country'] : '' }}"
                                                                                                @endif
                                                                                                {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                                                >
                                                                                    </div>
                                                                                    <input type="hidden" name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][short_state]" id="address_state_short_{{$i}}_{{$multienrollmentIncrement}}" >
                                                                                    <input type="hidden"
                                                                                       name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][lat]"
                                                                                       id="address_latitude_{{$i}}"
                                                                                       value="{{ isset($clonedData[$field->id]['lat']) && !empty($clonedData[$field->id]['lat']) ? $clonedData[$field->id]['lat'] : '' }}">
                                                                                    <input type="hidden"
                                                                                       name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][lng]"
                                                                                       id="address_longitude_{{$i}}"
                                                                                       value="{{ isset($clonedData[$field->id]['lng']) && !empty($clonedData[$field->id]['lng']) ? $clonedData[$field->id]['lng'] : '' }}">
                                                                                    <div class="col-sm-12 address-field">
                                                                                        <span class="zipcode-error address-zipcode-error-{{$i}}" style="color:red;"></span>
                                                                                    </div>
                                                                                </div>
                                                                                <script>
                                                                                    var input = document.getElementById('address_line_1_{{$i}}_{{$multienrollmentIncrement}}');
                                                                                    var addressPostalCode = '';
                                                                                    var addressState = '';
                                                                                    var autocomplete{{$i}} = new google.maps.places.Autocomplete(input, {
                                                                                        types: [],
                                                                                        componentRestrictions: {country: "us"}
                                                                                    });
                                                                                    google.maps.event.addListener(autocomplete{{$i}}, 'place_changed', function () {
                                                                                        // alert("here in 1");
                                                                                        var place = autocomplete{{$i}}.getPlace();
                                                                                        $('.address-zipcode-error-{{$i}}').text("");
                                                                                        $('#address_latitude_{{$i}}').val(place.geometry.location.lat());
                                                                                        $('#address_longitude_{{$i}}').val(place.geometry.location.lng());
                                                                                        @if(array_get($field, 'is_auto_caps') == 1)
                                                                                            $('#address_line_1_{{$i}}_{{$multienrollmentIncrement}}').val(place.name.toUpperCase());
                                                                                        @else
                                                                                            $('#address_line_1_{{$i}}_{{$multienrollmentIncrement}}').val(place.name);
                                                                                        @endif
                                                                                        var address2 = '';
                                                                                        
                                                                                        for (var i = 0; i < place.address_components.length; i++) {
                                                                                            var addressType = place.address_components[i].types[0];
                                                                                           
                                                                                            if (addressType === 'postal_code') {
                                                                                                
                                                                                                if('{{array_get($field, 'is_primary') == 1}}')
                                                                                                {
                                                                                                    addressPostalCode = place.address_components[i].long_name;
                                                                                                    @if(isset($input['state']) && !empty($input['state']))
                                                                                                        $("#zipcode").val(addressPostalCode);
                                                                                                    @endif
                                                                                                }
                                                                                                    $('.address-zipcode-error-{{$i}}').text("");
                                                                                                    @if(array_get($field, 'is_auto_caps') == 1)
                                                                                                        $('#address_zipcode_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name.toUpperCase());
                                                                                                    @else
                                                                                                        $('#address_zipcode_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name);
                                                                                                    @endif
                                                                                            }
                                                                                            if (addressType === "neighborhood" || addressType === "sublocality_level_1") {
                                                                                                @if(array_get($field, 'is_auto_caps') == 1)
                                                                                                    address2 += place.address_components[i].long_name.toUpperCase();
                                                                                                    address2 += ' '
                                                                                                @else
                                                                                                    address2 += place.address_components[i].long_name;
                                                                                                    address2 += ' '
                                                                                                @endif
                                                                                            }
                                                                                            if (addressType === "locality" || addressType === "administrative_area_level_2") {
                                                                                                @if(array_get($field, 'is_auto_caps') == 1)
                                                                                                    $('#address_city_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name.toUpperCase());
                                                                                                @else
                                                                                                    $('#address_city_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name);
                                                                                                @endif
                                                                                            }
                                                                                            // Code address county
                                                                                            if (addressType === "administrative_area_level_2") {
                                                                                                @if(array_get($field, 'is_auto_caps') == 1)
                                                                                                $('#address_county_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name.toUpperCase());
                                                                                                @else
                                                                                                $('#address_county_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name);
                                                                                                @endif
                                                                                                
                                                                                            }
                                                                                            // End
                                                                                            if (addressType === "administrative_area_level_1") {
                                                                                                @if(array_get($field, 'is_auto_caps') == 1)
                                                                                                    $('#address_state_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name.toUpperCase());
                                                                                                    addressState = place.address_components[i].short_name.toUpperCase();
                                                                                                    $('#address_state_{{$i}}_{{$multienrollmentIncrement}}').attr('short-name',addressState.toUpperCase());
                                                                                                    $('#address_state_short_{{$i}}_{{$multienrollmentIncrement}}').val(addressState.toUpperCase());
                                                                                                @else
                                                                                                    $('#address_state_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name);
                                                                                                    addressState = place.address_components[i].short_name;
                                                                                                    $('#address_state_{{$i}}_{{$multienrollmentIncrement}}').attr('short-name',addressState);
                                                                                                    $('#address_state_short_{{$i}}_{{$multienrollmentIncrement}}').val(addressState);
                                                                                                @endif
                                                                                            }
                                                                                            if (addressType === "country") {
                                                                                                @if(array_get($field, 'is_auto_caps') == 1)
                                                                                                    $('#address_state_{{$i}}_{{$multienrollmentIncrement}}').attr('short-name',addressState.toUpperCase());
                                                                                                    $('#address_country_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name.toUpperCase());
                                                                                                @else
                                                                                                $('#address_state_{{$i}}_{{$multienrollmentIncrement}}').attr('short-name',addressState);
                                                                                                    $('#address_country_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name);
                                                                                                @endif
                                                                                            }
                                                                                        }
                                                                                        let city = place.address_components.find(el =>
                                                                                            el['types'].includes('locality')
                                                                                        ).long_name || ''
                                                                                        
                                                                                        if(city == '') {
                                                                                            city = place.address_components.find(
                                                                                                el => el['types'].includes('administrative_area_level_2')).long_name || ''
                                                                                        }
                                                                                        @if(array_get($field, 'is_auto_caps') == 1)
                                                                                            $('#address_city_{{$i}}_{{$multienrollmentIncrement}}').val(city.toUpperCase());
                                                                                            $('#address_line_2_{{$i}}_{{$multienrollmentIncrement}}').val(address2.toUpperCase());
                                                                                        @else
                                                                                            $('#address_city_{{$i}}_{{$multienrollmentIncrement}}').val(city);
                                                                                            $('#address_line_2_{{$i}}_{{$multienrollmentIncrement}}').val(address2);
                                                                                        @endif
                                                                                        if('{{array_get($field, 'is_primary') == 1}}'){
                                                                                            @if(isset($input['state']) && !empty($input['state']))
                                                                                                if(addressState != $('#state').val())
                                                                                                {
                                                                                                    resetServiceBillingFields('address','','{{$i}}');
                                                                                                    printStateErrorMessage('address-zipcode-error-{{$i}}');
                                                                                                    $("#zipcode").val(addressPostalCode);
                                                                                                    console.log('address zipcode :'+addressPostalCode);
                                                                                                }
                                                                                            @else
                                                                                                if(addressPostalCode != $('#zipcode').attr('value'))
                                                                                                {
                                                                                                    resetServiceBillingFields('address','','{{$i}}');
                                                                                                    printZipcodeErrorMessage('address-zipcode-error-{{$i}}');
                                                                                                }
                                                                                            @endif
                                                                                        }
                                                                                    })
                                                                                </script>
                                                                                <!-- <span class="address-zipcode-error-{{$i}}" style="color:red;"></span> -->
                                                                            </div>
                                                                        </div>
                                                                    @elseif($field->type == 'service_and_billing_address')
                                                                        <div class="form-group"
                                                                             rel="service_and_billing_address">
                                                                            <?php
                                                                            $label = array_get($field, 'label');
                                                                            $labelArray = explode('-',$label);
                                                                            is_array($labelArray) ? $label = $labelArray[0] : '';
                                                                            $address = 'address';
                                                                            ?>
                                                                            <label class="control-label">{{ ucfirst(trim($label)) }}</label>
                                                                            <div class="form-group mb0">
                                                                                <label class="{{$address}}-label" copy-from="{{$address.$i}}">Service
                                                                                    Address</label>

                                                                                @if($field->is_allow_copy == 1)
                                                                                    @include('frontend.user.copy_from',['label'=> $address.'-label' , 'copyTo' => $address.$i])
                                                                                @endif
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="address-block {{$address.$i}}">
                                                                                <div class="col-sm-12 address-field">
                                                                                    <input class="form-control autocompletestreet address"
                                                                                            type="text" 
                                                                                           Placeholder="Address Line 1"
                                                                                           onfocus="this.setAttribute('autocomplete', 'new-password')"
                                                                                           autocapitalize="none"
                                                                                           spellcheck="false"
                                                                                           id="service_and_billing_address_service_address_1_{{$i}}"
                                                                                           name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][service_address_1]" 

                                                                                            @if(isset($input['fields'][$i]['value']['service_address_1']))
                                                                                                value="{{ $input['fields'][$i]['value']['service_address_1']}}" 
                                                                                            @else
                                                                                                value="{{ isset($clonedData[$field->id]['service_address_1']) && !empty($clonedData[$field->id]['service_address_1']) ? $clonedData[$field->id]['service_address_1'] : '' }}"
                                                                                            @endif
                                                                                           @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                                                                           data-parsley-trigger="focusout"

                                                                                           @endif
                                                                                           onkeyup="changeVal('service_and_billing_address_service_address_1_', 'service_and_billing_address_billing_address_1_', '{{$i}}');"
                                                                                           {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                                        >
                                                                                </div>
                                                                                <div class="col-sm-12 address-field">
                                                                                    <input autocomplete="new-password"
                                                                                            type="text" 
                                                                                           class="form-control autocompletestreet address"
                                                                                           Placeholder="Apartment, suite, unit, building, floor, etc. (optional)"
                                                                                           id="service_and_billing_address_service_address_2_{{$i}}"
                                                                                           name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][service_address_2]" 
                                                                                           @if(isset($input['fields'][$i]['value']['service_address_2']))
                                                                                                value="{{ $input['fields'][$i]['value']['service_address_2']}}" 
                                                                                            @else
                                                                                                value="{{ isset($clonedData[$field->id]['service_address_2']) && !empty($clonedData[$field->id]['service_address_2']) ? $clonedData[$field->id]['service_address_2'] : '' }}"
                                                                                            @endif
                                                                                           onkeyup="changeVal('service_and_billing_address_service_address_2_', 'service_and_billing_address_billing_address_2_', '{{$i}}');"
                                                                                           {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                                    >
                                                                                </div>
                                                                                <!-- Code changes by : Remove unit number div -->
                                                                                <!-- <div class="col-sm-4 address-field">
                                                                                    <input autocomplete="new-password" 
                                                                                            type="text" 
                                                                                           class="form-control autocompletestreet address"
                                                                                           Placeholder="Unit Number"
                                                                                           id="service_and_billing_address_service_unit_{{$i}}"
                                                                                           name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][service_unit]" 
                                                                                            @if(isset($input['fields'][$i]['value']['service_unit']))
                                                                                                value="{{ $input['fields'][$i]['value']['service_unit']}}" 
                                                                                            @else
                                                                                                value="{{ isset($clonedData[$field->id]['service_unit']) && !empty($clonedData[$field->id]['service_unit']) ? $clonedData[$field->id]['service_unit'] : '' }}"
                                                                                            @endif


                                                                                            onkeyup="changeVal('service_and_billing_address_service_unit_', 'service_and_billing_address_billing_unit_', '{{$i}}');"
                                                                                            {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                                    >
                                                                                </div>-->

                                                                                <div class="col-sm-3 address-field inline-block">
                                                                                    <input autocomplete="new-password" 
                                                                                            type="text" 
                                                                                           class="form-control"
                                                                                           placeholder="City"
                                                                                           id="service_and_billing_address_service_city_{{$i}}"
                                                                                           name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][service_city]"
                                                                                            @if(isset($input['fields'][$i]['value']['service_city']))
                                                                                                value="{{ $input['fields'][$i]['value']['service_city']}}" 
                                                                                            @else
                                                                                                value="{{ isset($clonedData[$field->id]['service_city']) && !empty($clonedData[$field->id]['service_city']) ? $clonedData[$field->id]['service_city'] : '' }}"
                                                                                            @endif
                                                                                           onkeyup="changeVal('service_and_billing_address_service_city_', 'service_and_billing_address_billing_city_', '{{$i}}');"
                                                                                           {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                                    >
                                                                                </div>
                                                                                <!-- Start : Div for service_county -->
                                                                                <div class="col-sm-3 address-field inline-block">
                                                                                    <input autocomplete="new-password" 
                                                                                            type="text" 
                                                                                           class="form-control"
                                                                                           placeholder="County"
                                                                                           id="service_and_billing_address_service_county_{{$i}}"
                                                                                           name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][service_county]"
                                                                                            @if(isset($input['fields'][$i]['value']['service_county']))
                                                                                                value="{{ $input['fields'][$i]['value']['service_county']}}" 
                                                                                            @else
                                                                                                value="{{ isset($clonedData[$field->id]['service_county']) && !empty($clonedData[$field->id]['service_county']) ? $clonedData[$field->id]['service_county'] : '' }}"
                                                                                            @endif
                                                                                           onkeyup="changeVal('service_and_billing_address_service_county_', 'service_and_billing_address_billing_county_', '{{$i}}');"
                                                                                           {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!} 
                                                                                           >
                                                                                </div>
                                                                                <!-- End  -->
                                                                                <div class="col-sm-3 address-field inline-block">
                                                                                    <input id="service_and_billing_address_service_state_{{$i}}"
                                                                                           class="form-control stateall statefield address @if(array_get($field, 'is_primary')) state-field @endif"
                                                                                             type="text" 
                                                                                           Placeholder="State"
                                                                                           autocomplete="new-password"
                                                                                           name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][service_state]" 
                                                                                            @if(isset($input['fields'][$i]['value']['service_state']))
                                                                                                value="{{ $input['fields'][$i]['value']['service_state']}}" 
                                                                                            @else
                                                                                                value="{{ isset($clonedData[$field->id]['service_state']) && !empty($clonedData[$field->id]['service_state']) ? $clonedData[$field->id]['service_state'] : '' }}"
                                                                                            @endif
                                                                                           onkeyup="changeVal('service_and_billing_address_service_state_', 'service_and_billing_address_billing_state_', '{{$i}}');"
                                                                                            short-name="@if(isset($input['fields'][$i]['value']['short_service_state'])) {{ $input['fields'][$i]['value']['short_service_state']}} @endif"
                                                                                            {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                                            >
                                                                                </div>
                                                                                <div class="col-sm-3 address-field inline-block">
                                                                                    <input class="form-control @if(array_get($field, 'is_primary')) zipcode-field @endif" 
                                                                                            type="text" 
                                                                                           id="service_and_billing_address_service_zipcode_{{$i}}"
                                                                                           name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][service_zipcode]"
                                                                                            @if(isset($input['fields'][$i]['value']['service_zipcode']))
                                                                                                value="{{ $input['fields'][$i]['value']['service_zipcode']}}" 
                                                                                            @else
                                                                                                value="{{ isset($clonedData[$field->id]['service_zipcode']) && !empty($clonedData[$field->id]['service_zipcode']) ? $clonedData[$field->id]['service_zipcode'] : '' }}"
                                                                                            @endif
                                                                                           autocomplete="new-password"
                                                                                           data-parsley-trigger="focusout"
                                                                                           data-parsley-pattern="[0-9]{5}"
                                                                                           data-parsley-pattern-message="Please enter 5 digit zipcode"
                                                                                           Placeholder="Zipcode"
                                                                                           onkeyup="changeVal('service_and_billing_address_service_zipcode_', 'service_and_billing_address_billing_zipcode_', '{{$i}}');">
                                                                                </div>
                                                                                <div class="col-sm-3 address-field inline-block">
                                                                                    <input id="service_and_billing_address_service_country_{{$i}}" style="display: none;"
                                                                                            type="text" 
                                                                                           class="form-control stateall statefield address "
                                                                                           Placeholder="Country"
                                                                                           autocomplete="new-password"
                                                                                           name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][service_country]"
                                                                                            @if(isset($input['fields'][$i]['value']['service_country']))
                                                                                                value="{{ $input['fields'][$i]['value']['service_country']}}" 
                                                                                            @else
                                                                                                value="{{ isset($clonedData[$field->id]['service_country']) && !empty($clonedData[$field->id]['service_country']) ? $clonedData[$field->id]['service_country'] : '' }}"
                                                                                            @endif
                                                                                           onkeyup="changeVal('service_and_billing_address_service_country_', 'service_and_billing_address_billing_country_', '{{$i}}');"
                                                                                           {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                                           >
                                                                                </div>
                                                                                    <input type="hidden" name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][short_service_state]" id="service_and_billing_address_service_state_short_{{$i}}">
                                                                                    <input type="hidden"
                                                                                       name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][service_lat]"
                                                                                       id="service_and_billing_address_service_latitude_{{$i}}"
                                                                                       value="{{ isset($clonedData[$field->id]['service_lat']) && !empty($clonedData[$field->id]['service_lat']) ? $clonedData[$field->id]['service_lat'] : '' }}">
                                                                                    <input type="hidden"
                                                                                       name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][service_lng]"
                                                                                       id="service_and_billing_address_service_longitude_{{$i}}"
                                                                                       value="{{ isset($clonedData[$field->id]['service_lng']) && !empty($clonedData[$field->id]['service_lng']) ? $clonedData[$field->id]['service_lng'] : '' }}">
                                                                                    <div class="col-sm-12">
                                                                                        <span class="zipcode-error  service-address-zipcode-error-{{$i}}" style="color:red;"></span>
                                                                                    </div>
                                                                                </div>
                                                                                <script>
                                                                                    var input = document.getElementById('service_and_billing_address_service_address_1_{{$i}}');
                                                                                    var servicePostalCode = "";
                                                                                    var serviceState = "";
                                                                                    var autocompleteService{{$i}} = new google.maps.places.Autocomplete(input, {
                                                                                        types: [],
                                                                                        // bounds: zipBounds,
                                                                                        // strictBounds:true,
                                                                                        componentRestrictions: {country: "us"}
                                                                                    });
                                                                                    // autocompleteService{{$i}}.setBounds(zipBounds);
                                                                                    google.maps.event.addListener(autocompleteService{{$i}}, 'place_changed', function () {
                                                                                        // alert("here in 2");
                                                                                        $('.service-address-zipcode-error-{{$i}}').text("");
                                                                                        var place = autocompleteService{{$i}}.getPlace();
                                                                                        $('#service_and_billing_address_service_latitude_{{$i}}').val(place.geometry.location.lat());
                                                                                        $('#service_and_billing_address_service_longitude_{{$i}}').val(place.geometry.location.lng());
                                                                                        @if(array_get($field, 'is_auto_caps') == 1)
                                                                                            $('#service_and_billing_address_service_address_1_{{$i}}').val(place.name.toUpperCase());
                                                                                        @else
                                                                                            $('#service_and_billing_address_service_address_1_{{$i}}').val(place.name);
                                                                                        @endif
                                                                                        var address2 = '';

                                                                                        for (var i = 0; i < place.address_components.length; i++) {
                                                                                            var addressType = place.address_components[i].types[0];
                                                                                            console.log(place.address_components);
                                                                                            if (addressType === 'postal_code') {
                                                                                                if('{{array_get($field, 'is_primary') == 1}}')
                                                                                                {
                                                                                                    servicePostalCode = place.address_components[i].long_name;
                                                                                                    @if(isset($input['state']) && !empty($input['state']))
                                                                                                        $("#zipcode").val(servicePostalCode);
                                                                                                    @endif
                                                                                                }
                                                                                                $('.service-address-zipcode-error-{{$i}}').text("");
                                                                                                $('#service_and_billing_address_service_zipcode_{{$i}}').val(place.address_components[i].long_name);
                                                                                            }
                                                                                            if (addressType === "neighborhood" || addressType === "sublocality_level_1") {
                                                                                                @if(array_get($field, 'is_auto_caps') == 1)
                                                                                                    address2 += place.address_components[i].long_name.toUpperCase();
                                                                                                    address2 += ' '
                                                                                                @else
                                                                                                    address2 += place.address_components[i].long_name;
                                                                                                    address2 += ' '
                                                                                                @endif
                                                                                            }
                                                                                            if (addressType === "locality" || addressType === "administrative_area_level_2") {
                                                                                                @if(array_get($field, 'is_auto_caps') == 1)
                                                                                                    $('#service_and_billing_address_service_city_{{$i}}').val(place.address_components[i].long_name.toUpperCase());
                                                                                                @else
                                                                                                    $('#service_and_billing_address_service_city_{{$i}}').val(place.address_components[i].long_name);
                                                                                                @endif  
                                                                                            }
                                                                                            // code for service_county
                                                                                            if (addressType === "administrative_area_level_2") {
                                                                                             @if(array_get($field, 'is_auto_caps') == 1)
                                                                                                    $('#service_and_billing_address_service_county_{{$i}}').val(place.address_components[i].long_name.toUpperCase());
                                                                                                @else
                                                                                                    $('#service_and_billing_address_service_county_{{$i}}').val(place.address_components[i].long_name);
                                                                                                @endif  
                                                                                                
                                                                                            }
                                                                                            // End
                                                                                            if (addressType === "administrative_area_level_1") {
                                                                                                @if(array_get($field, 'is_auto_caps') == 1)
                                                                                                    $('#service_and_billing_address_service_state_{{$i}}').val(place.address_components[i].long_name.toUpperCase());
                                                                                                    serviceState = place.address_components[i].short_name.toUpperCase();
                                                                                                    $('#service_and_billing_address_service_state_{{$i}}').attr('short-name',serviceState.toUpperCase())
                                                                                                    $('#service_and_billing_address_service_state_short_{{$i}}').val(serviceState.toUpperCase());
                                                                                                @else
                                                                                                    $('#service_and_billing_address_service_state_{{$i}}').val(place.address_components[i].long_name);
                                                                                                    serviceState = place.address_components[i].short_name;
                                                                                                    $('#service_and_billing_address_service_state_{{$i}}').attr('short-name',serviceState)
                                                                                                    $('#service_and_billing_address_service_state_short_{{$i}}').val(serviceState);
                                                                                                @endif
                                                                                                
                                                                                            }
                                                                                            if (addressType === "country") {
                                                                                                @if(array_get($field, 'is_auto_caps') == 1)
                                                                                                    $('#service_and_billing_address_service_country_{{$i}}').val(place.address_components[i].long_name.toUpperCase());
                                                                                                @else
                                                                                                    $('#service_and_billing_address_service_country_{{$i}}').val(place.address_components[i].long_name);
                                                                                                @endif
                                                                                            }
                                                                                        }
                                                                                        let city = place.address_components.find(el =>
                                                                                            el['types'].includes('locality')
                                                                                        ).long_name || ''
                                                                                        
                                                                                        if(city == '') {
                                                                                            city = place.address_components.find(
                                                                                                el => el['types'].includes('administrative_area_level_2')).long_name || ''
                                                                                        }
                                                                                        @if(array_get($field, 'is_auto_caps') == 1)
                                                                                            $('#service_and_billing_address_service_city_{{$i}}').val(city.toUpperCase());
                                                                                        @else
                                                                                            $('#service_and_billing_address_service_city_{{$i}}').val(city);
                                                                                        @endif
                                                                                        if ($('input[name="is_service_address_same_as_billing_address_{{$i}}"]:checked').val() == "yes") {
                                                                                            console.log("ues");
                                                                                            copy_address_("{{$i}}", 'yes')
                                                                                        }
                                                                                        if('{{array_get($field, 'is_primary') == 1}}'){

                                                                                            @if(isset($input['state']) && !empty($input['state']))
                                                                                                if(serviceState != $('#state').val())
                                                                                                {
                                                                                                    resetServiceBillingFields('service_and_billing_address','service','{{$i}}');
                                                                                                    printStateErrorMessage('service-address-zipcode-error-{{$i}}');
                                                                                                }
                                                                                            @else
                                                                                                if(servicePostalCode != $('#zipcode').attr('value'))
                                                                                                {
                                                                                                    resetServiceBillingFields('service_and_billing_address','service','{{$i}}');
                                                                                                    printZipcodeErrorMessage('service-address-zipcode-error-{{$i}}');
                                                                                                }
                                                                                            @endif
                                                                                        }
                                                                                    })
                                                                                </script>
                                                                                <script>
                                                                                    function copy_address_($i, state, isAutoCaps = 0) {
                                                                                        $("#error_service_and_billing_address_billing_address_1_" + $i).html('');
                                                                                        if (state == "yes") {
                                                                                            
                                                                                            serviceAndBillingElements.push(parseInt($i));
                                                                                            $("#service_and_billing_address_billing_unit_" + $i).val($('#service_and_billing_address_service_unit_' + $i).val()).attr('readonly', 'readonly');
                                                                                            $("#service_and_billing_address_billing_address_1_" + $i).val($('#service_and_billing_address_service_address_1_' + $i).val()).attr('readonly', 'readonly');
                                                                                            $("#service_and_billing_address_billing_address_2_" + $i).val($('#service_and_billing_address_service_address_2_' + $i).val()).attr('readonly', 'readonly');
                                                                                            $("#service_and_billing_address_billing_zipcode_" + $i).val($('#service_and_billing_address_service_zipcode_' + $i).val()).attr('readonly', 'readonly');
                                                                                            $("#service_and_billing_address_billing_city_" + $i).val($('#service_and_billing_address_service_city_' + $i).val()).attr('readonly', 'readonly');
                                                                                            // Code for copy county for service and billing address county 
                                                                                            $("#service_and_billing_address_billing_county_" + $i).val($('#service_and_billing_address_service_county_' + $i).val()).attr('readonly', 'readonly');
                                                                                            // End
                                                                                            $("#service_and_billing_address_billing_state_" + $i).val($('#service_and_billing_address_service_state_' + $i).val()).attr('readonly', 'readonly');
                                                                                            $("#service_and_billing_address_billing_country_" + $i).val($('#service_and_billing_address_service_country_' + $i).val()).attr('readonly', 'readonly');
                                                                                            $("#service_and_billing_address_billing_latitude_" + $i).val($('#service_and_billing_address_service_latitude_' + $i).val()).attr('readonly', 'readonly');
                                                                                            $("#service_and_billing_address_billing_longitude_" + $i).val($('#service_and_billing_address_service_longitude_' + $i).val()).attr('readonly', 'readonly');
                                                                                            var inputD = document.getElementById('service_and_billing_address_billing_address_1_' + $i);
                                                                                            //inputD.parentNode.replaceChild(inputD.cloneNode(true),input);
                                                                                        } else {
                                                                                            $("#service_and_billing_address_billing_unit_" + $i).val('');
                                                                                            $("#service_and_billing_address_billing_unit_" + $i).removeAttr('readonly');

                                                                                            $("#service_and_billing_address_billing_address_1_" + $i).val('');
                                                                                            $("#service_and_billing_address_billing_address_1_" + $i).removeAttr('readonly');

                                                                                            $("#service_and_billing_address_billing_address_2_" + $i).val('');
                                                                                            $("#service_and_billing_address_billing_address_2_" + $i).removeAttr('readonly');

                                                                                            $("#service_and_billing_address_billing_city_" + $i).val('');
                                                                                            $("#service_and_billing_address_billing_city_" + $i).val('').removeAttr('readonly');

                                                                                            // Code for remove value of county for service and billing address county 
                                                                                            $("#service_and_billing_address_billing_county_" + $i).val('');
                                                                                            $("#service_and_billing_address_billing_county_" + $i).val('').removeAttr('readonly');
                                                                                            // End

                                                                                            $("#service_and_billing_address_billing_zipcode_" + $i).val('');
                                                                                            $("#service_and_billing_address_billing_zipcode_" + $i).val('').removeAttr('readonly');

                                                                                            $("#service_and_billing_address_billing_state_" + $i).val('');
                                                                                            $("#service_and_billing_address_billing_state_" + $i).removeAttr('readonly');

                                                                                            $("#service_and_billing_address_billing_country_" + $i).val('');
                                                                                            $("#service_and_billing_address_billing_country_" + $i).removeAttr('readonly');
                                                                                            serviceAndBillingElements.pop(parseInt($i));

                                                                                            var inputD = document.getElementById('service_and_billing_address_billing_address_1_' + $i);
                                                                                            autocomplete$i = new google.maps.places.Autocomplete(inputD, {
                                                                                                types: [],
                                                                                                componentRestrictions: {country: "us"}
                                                                                            });

                                                                                            google.maps.event.addListener(autocomplete$i, 'place_changed', function () {                
                                                                                                var place = autocomplete$i.getPlace();
                                                                                                $('#service_and_billing_address_billing_latitude_' + $i).val(place.geometry.location.lat());
                                                                                                $('#service_and_billing_address_billing_longitude_' + $i).val(place.geometry.location.lng());
                                                                                                if(isAutoCaps == 1) {
                                                                                                    $('#service_and_billing_address_billing_address_1_'+ $i).val(place.name.toUpperCase());
                                                                                                } else { 
                                                                                                    $('#service_and_billing_address_billing_address_1_'+ $i).val(place.name);
                                                                                                }
                                                                                                var address2 = '';
                                                                                                for (var i = 0; i < place.address_components.length; i++) {
                                                                                                    var addressType = place.address_components[i].types[0];
                                                                                                    let index = place.address_components.length-1;
                                                                                                    let zipcodeValue = place.address_components[index].types[0];
                                                                                                    if(addressType === "postal_code"){
                                                                                                        if(isAutoCaps == 1){
                                                                                                            $('#service_and_billing_address_billing_zipcode_{{$i}}').val(place.address_components[i].long_name.toUpperCase());
                                                                                                        }else{
                                                                                                            $('#service_and_billing_address_billing_zipcode_{{$i}}').val(place.address_components[i].long_name);
                                                                                                        }
                                                                                                    }
                                                                                                    if (addressType === "neighborhood" || addressType === "sublocality_level_1") {
                                                                                                        address2 += place.address_components[i].long_name;
                                                                                                        address2 += ' ';
                                                                                                        if(isAutoCaps == 1){
                                                                                                            $('#service_and_billing_address_billing_address_2_' + $i).val(address2.toUpperCase());
                                                                                                        }else{
                                                                                                            $('#service_and_billing_address_billing_address_2_' + $i).val(address2);
                                                                                                        }
                                                                                                    }
                                                                                                    if (addressType === "locality" || addressType === "administrative_area_level_2") {
                                                                                                        if(isAutoCaps == 1){
                                                                                                            $('#service_and_billing_address_billing_city_' + $i).val(place.address_components[i].long_name.toUpperCase());
                                                                                                        }else{
                                                                                                            $('#service_and_billing_address_billing_city_' + $i).val(place.address_components[i].long_name);
                                                                                                        }
                                                                                                    }
                                                                                                    // Code for billing_county
                                                                                                    if (addressType === "administrative_area_level_2") {
                                                                                                        if(isAutoCaps == 1){
                                                                                                            $('#service_and_billing_address_billing_county_' + $i).val(place.address_components[i].long_name.toUpperCase());
                                                                                                        }else{
                                                                                                            $('#service_and_billing_address_billing_county_' + $i).val(place.address_components[i].long_name);
                                                                                                        }
                                                                                                    }
                                                                                                    // End
                                                                                                    if (addressType === "administrative_area_level_1") {
                                                                                                        if(isAutoCaps == 1){
                                                                                                            $('#service_and_billing_address_billing_state_' + $i).val(place.address_components[i].long_name.toUpperCase());
                                                                                                        }else{
                                                                                                            $('#service_and_billing_address_billing_state_' + $i).val(place.address_components[i].long_name);
                                                                                                        }
                                                                                                    }
                                                                                                    if (addressType === "country") {
                                                                                                        if(isAutoCaps == 1){
                                                                                                            $('#service_and_billing_address_billing_country_{{$i}}').val(place.address_components[i].long_name.toUpperCase());
                                                                                                        }else{
                                                                                                            $('#service_and_billing_address_billing_country_{{$i}}').val(place.address_components[i].long_name);
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                                let city = place.address_components.find(el =>
                                                                                                    el['types'].includes('locality')
                                                                                                ).long_name || ''
                                                                                                
                                                                                                if(city == '') {
                                                                                                    city = place.address_components.find(
                                                                                                        el => el['types'].includes('administrative_area_level_2')).long_name || ''
                                                                                                }
                                                                                                if(isAutoCaps == 1){
                                                                                                    $('#service_and_billing_address_billing_city_{{$i}}').val(city.toUpperCase());
                                                                                                }else{
                                                                                                    $('#service_and_billing_address_billing_city_{{$i}}').val(city);
                                                                                                }
                                                                                            })
                                                                                        }

                                                                                    }

                                                                                    function copy_address_child_($i, state, $multienrollment,isAutoCaps = 0) {
                                                                                        $("#error_service_and_billing_address_billing_address_1_" + $i+'_'+$multienrollment).html('');
                                                                                        if (state == "yes") {
                                                                                            
                                                                                            serviceAndBillingElements.push(parseInt($i));
                                                                                            $("#service_and_billing_address_billing_unit_" + $i+'_'+$multienrollment).val($('#service_and_billing_address_service_unit_' + $i).val()).attr('readonly', 'readonly');
                                                                                            $("#service_and_billing_address_billing_address_1_" + $i+'_'+$multienrollment).val($('#service_and_billing_address_service_address_1_' + $i+'_'+$multienrollment).val()).attr('readonly', 'readonly');
                                                                                            $("#service_and_billing_address_billing_address_2_" + $i+'_'+$multienrollment).val($('#service_and_billing_address_service_address_2_' + $i+'_'+$multienrollment).val()).attr('readonly', 'readonly');
                                                                                            $("#service_and_billing_address_billing_zipcode_" + $i+'_'+$multienrollment).val($('#service_and_billing_address_service_zipcode_' + $i+'_'+$multienrollment).val()).attr('readonly', 'readonly');
                                                                                            $("#service_and_billing_address_billing_city_" + $i+'_'+$multienrollment).val($('#service_and_billing_address_service_city_' + $i+'_'+$multienrollment).val()).attr('readonly', 'readonly');
                                                                                            // Code for copy county for service and billing address county 
                                                                                            $("#service_and_billing_address_billing_county_" + $i+'_'+$multienrollment).val($('#service_and_billing_address_service_county_' + $i+'_'+$multienrollment).val()).attr('readonly', 'readonly');
                                                                                            // End
                                                                                            $("#service_and_billing_address_billing_state_" + $i+'_'+$multienrollment).val($('#service_and_billing_address_service_state_' + $i+'_'+$multienrollment).val()).attr('readonly', 'readonly');
                                                                                            $("#service_and_billing_address_billing_country_" + $i+'_'+$multienrollment).val($('#service_and_billing_address_service_country_' + $i+'_'+$multienrollment).val()).attr('readonly', 'readonly');
                                                                                            $("#service_and_billing_address_billing_latitude_" + $i+'_'+$multienrollment).val($('#service_and_billing_address_service_latitude_' + $i+'_'+$multienrollment).val()).attr('readonly', 'readonly');
                                                                                            $("#service_and_billing_address_billing_longitude_" + $i+'_'+$multienrollment).val($('#service_and_billing_address_service_longitude_' + $i+'_'+$multienrollment).val()).attr('readonly', 'readonly');
                                                                                            var inputD = document.getElementById('service_and_billing_address_billing_address_1_' + $i+'_'+$multienrollment);
                                                                                            //inputD.parentNode.replaceChild(inputD.cloneNode(true),input);
                                                                                        } else {
                                                                                            $("#service_and_billing_address_billing_unit_" + $i+'_'+$multienrollment).val('');
                                                                                            $("#service_and_billing_address_billing_unit_" + $i+'_'+$multienrollment).removeAttr('readonly');

                                                                                            $("#service_and_billing_address_billing_address_1_" + $i+'_'+$multienrollment).val('');
                                                                                            $("#service_and_billing_address_billing_address_1_" + $i+'_'+$multienrollment).removeAttr('readonly');

                                                                                            $("#service_and_billing_address_billing_address_2_" + $i+'_'+$multienrollment).val('');
                                                                                            $("#service_and_billing_address_billing_address_2_" + $i+'_'+$multienrollment).removeAttr('readonly');

                                                                                            $("#service_and_billing_address_billing_city_" + $i+'_'+$multienrollment).val('');
                                                                                            $("#service_and_billing_address_billing_city_" + $i+'_'+$multienrollment).val('').removeAttr('readonly');

                                                                                            // Code for remove value of county for service and billing address county 
                                                                                            $("#service_and_billing_address_billing_county_" + $i+'_'+$multienrollment).val('');
                                                                                            $("#service_and_billing_address_billing_county_" + $i+'_'+$multienrollment).val('').removeAttr('readonly');
                                                                                            // End

                                                                                            $("#service_and_billing_address_billing_zipcode_" + $i+'_'+$multienrollment).val('');
                                                                                            $("#service_and_billing_address_billing_zipcode_" + $i+'_'+$multienrollment).val('').removeAttr('readonly');

                                                                                            $("#service_and_billing_address_billing_state_" + $i+'_'+$multienrollment).val('');
                                                                                            $("#service_and_billing_address_billing_state_" + $i+'_'+$multienrollment).removeAttr('readonly');

                                                                                            $("#service_and_billing_address_billing_country_" + $i+'_'+$multienrollment).val('');
                                                                                            $("#service_and_billing_address_billing_country_" + $i+'_'+$multienrollment).removeAttr('readonly');
                                                                                            serviceAndBillingElements.pop(parseInt($i));

                                                                                            var inputD = document.getElementById('service_and_billing_address_billing_address_1_' + $i+'_'+$multienrollment);
                                                                                            autocomplete$i = new google.maps.places.Autocomplete(inputD, {
                                                                                                types: [],
                                                                                                componentRestrictions: {country: "us"}
                                                                                            });

                                                                                            google.maps.event.addListener(autocomplete$i, 'place_changed', function () {                                                                                                
                                                                                                var place = autocomplete$i.getPlace();
                                                                                                $('#service_and_billing_address_billing_latitude_' + $i).val(place.geometry.location.lat());
                                                                                                $('#service_and_billing_address_billing_longitude_' + $i).val(place.geometry.location.lng());
                                                                                                // $('#service_and_billing_address_billing_address_1_' + $i).val(place.name);
                                                                                                var address2 = '';
                                                                                                for (var i = 0; i < place.address_components.length; i++) {
                                                                                                    var addressType = place.address_components[i].types[0];
                                                                                                    let index = place.address_components.length-1;
                                                                                                    let zipcodeValue = place.address_components[index].types[0];
                                                                                                    if(addressType === "postal_code"){
                                                                                                        if(isAutoCaps == 1){
                                                                                                            $('#service_and_billing_address_billing_zipcode_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name.toUpperCase());
                                                                                                        }else{
                                                                                                            $('#service_and_billing_address_billing_zipcode_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name);
                                                                                                        }
                                                                                                    }
                                                                                                    if (addressType === "neighborhood" || addressType === "sublocality_level_1") {
                                                                                                        address2 += place.address_components[i].long_name;
                                                                                                        address2 += ' ';
                                                                                                        if(isAutoCaps == 1){
                                                                                                            $('#service_and_billing_address_billing_address_2_' + $i+'_'+$multienrollment).val(address2.toUpperCase());
                                                                                                        }else{
                                                                                                            $('#service_and_billing_address_billing_address_2_' + $i+'_'+$multienrollment).val(address2);
                                                                                                        }
                                                                                                    }
                                                                                                    if (addressType === "locality" || addressType === "administrative_area_level_2") {
                                                                                                        if(isAutoCaps == 1){
                                                                                                            $('#service_and_billing_address_billing_city_' + $i+'_'+$multienrollment).val(place.address_components[i].long_name.toUpperCase());
                                                                                                        }else{
                                                                                                            $('#service_and_billing_address_billing_city_' + $i+'_'+$multienrollment).val(place.address_components[i].long_name);
                                                                                                        }
                                                                                                    }
                                                                                                    // Code for billing_county
                                                                                                    if (addressType === "administrative_area_level_2") {
                                                                                                        if(isAutoCaps == 1){
                                                                                                            $('#service_and_billing_address_billing_county_' + $i+'_'+$multienrollment).val(place.address_components[i].long_name.toUpperCase());
                                                                                                        }else{
                                                                                                            $('#service_and_billing_address_billing_county_' + $i+'_'+$multienrollment).val(place.address_components[i].long_name);
                                                                                                        }
                                                                                                    }
                                                                                                    // End
                                                                                                    if (addressType === "administrative_area_level_1") {
                                                                                                        if(isAutoCaps == 1){
                                                                                                            $('#service_and_billing_address_billing_state_' + $i+'_'+$multienrollment).val(place.address_components[i].long_name.toUpperCase());
                                                                                                        }else{
                                                                                                            $('#service_and_billing_address_billing_state_' + $i+'_'+$multienrollment).val(place.address_components[i].long_name);
                                                                                                        }
                                                                                                    }
                                                                                                    if (addressType === "country") {
                                                                                                        if(isAutoCaps == 1){
                                                                                                            $('#service_and_billing_address_billing_country_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name.toUpperCase());
                                                                                                        }else{
                                                                                                            $('#service_and_billing_address_billing_country_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name);
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                                let city = place.address_components.find(el =>
                                                                                                    el['types'].includes('locality')
                                                                                                ).long_name || ''
                                                                                                
                                                                                                if(city == '') {
                                                                                                    city = place.address_components.find(
                                                                                                        el => el['types'].includes('administrative_area_level_2')).long_name || ''
                                                                                                }
                                                                                                if(isAutoCaps == 1){
                                                                                                    $('#service_and_billing_address_billing_city_{{$i}}_{{$multienrollmentIncrement}}').val(city.toUpperCase());
                                                                                                }else{
                                                                                                    $('#service_and_billing_address_billing_city_{{$i}}_{{$multienrollmentIncrement}}').val(city);
                                                                                                }
                                                                                            })
                                                                                        }

                                                                                    }

                                                                                </script>
                                                                                <div class="col-sm-12">
                                                                                    <!-- <span class="service-address-zipcode-error-{{$i}}" style="color:red;"></span><br/> -->
                                                                                    <span class="bill-address-title">Is the billing address same as service address?</span>
                                                                                    &nbsp;

                                                                                    <div class="form-group radio-btns pdt0">
                                                                                        <label class="radio-inline">
                                                                                            <input type="radio"
                                                                                                   name="is_service_address_same_as_billing_address_{{$i}}"
                                                                                                   onclick='copy_address_("{{$i}}", "yes")'
                                                                                                   value="yes">
                                                                                            Yes
                                                                                        </label>
                                                                                        <?php 
                                                                                            $checkIsAutoCaps = (array_get($field, 'is_auto_caps') == 1 ? 1 : 0);
                                                                                        ?>
                                                                                        <label class="radio-inline">
                                                                                            <input type="radio"
                                                                                                   name="is_service_address_same_as_billing_address_{{$i}}"
                                                                                                   onclick='copy_address_("{{$i}}", "no", "{{$checkIsAutoCaps}}")'
                                                                                                   value="no">
                                                                                            No
                                                                                        </label>
                                                                                    </div>

                                                                                </div>
                                                                                <div class="col-sm-12">
                                                                                    <div class="form-group">
                                                                                        <label class="{{$address}}-label" copy-from="{{$address.$i}}-billing">Billing Address</label>
                                                                                        @if($field->is_allow_copy == 1)
                                                                                            @include('frontend.user.copy_from',['label'=> $address.'-label' , 'copyTo' => $address.$i.'-billing'])
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                                <div class="address-block {{$address.$i}}-billing">
                                                                                <div class="col-sm-12 address-field">
                                                                                    <input autocomplete="off" 
                                                                                            type="text" 
                                                                                           class="form-control autocompletestreet address {{ array_get($field, 'is_required' )==1 ? 'required' : '' }}"
                                                                                           Placeholder="Address Line 1"
                                                                                           onfocus="this.setAttribute('autocomplete', 'new-password')"
                                                                                           id="service_and_billing_address_billing_address_1_{{$i}}"
                                                                                           name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][billing_address_1]"
                                                                                            @if(isset($input['fields'][$i]['value']['billing_address_1']))
                                                                                                value="{{ $input['fields'][$i]['value']['billing_address_1']}}" 
                                                                                            @else
                                                                                                value="{{ isset($clonedData[$field->id]['billing_address_1']) && !empty($clonedData[$field->id]['billing_address_1']) ? $clonedData[$field->id]['billing_address_1'] : '' }}"
                                                                                            @endif
                                                                                           data-parsley-trigger="focusout"
                                                                                           data-parsley-errors-container="#error_service_and_billing_address_billing_address_1_{{$i}}"
                                                                                           @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                                                                            @endif
                                                                                            {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                                            >
                                                                                        <div id="error_service_and_billing_address_billing_address_1_{{$i}}"></div>
                                                                                </div>
                                                                                <div class="col-sm-12 address-field">
                                                                                    <input autocomplete="new-password" 
                                                                                            type="text" 
                                                                                           class="form-control autocompletestreet address"
                                                                                           Placeholder="Apartment, suite, unit, building, floor, etc. (optional)"
                                                                                           id="service_and_billing_address_billing_address_2_{{$i}}"
                                                                                           name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][billing_address_2]"
                                                                                           @if(isset($input['fields'][$i]['value']['billing_address_2']))
                                                                                                value="{{ $input['fields'][$i]['value']['billing_address_2']}}" 
                                                                                            @else
                                                                                                value="{{ isset($clonedData[$field->id]['billing_address_2']) && !empty($clonedData[$field->id]['billing_address_2']) ? $clonedData[$field->id]['billing_address_2'] : '' }}"
                                                                                            @endif
                                                                                            {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                                            >
                                                                                </div>
                                                                                {{--<!-- <div class="col-sm-4 address-field">
                                                                                    <input autocomplete="new-password" 
                                                                                            type="text" 
                                                                                           class="form-control autocompletestreet address"
                                                                                           Placeholder="Unit Number"
                                                                                           id="service_and_billing_address_billing_unit_{{$i}}"
                                                                                           name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][billing_unit]"
                                                                                           @if(isset($input['fields'][$i]['value']['billing_unit']))
                                                                                                value="{{ $input['fields'][$i]['value']['billing_unit']}}" 
                                                                                            @else
                                                                                                value="{{ isset($clonedData[$field->id]['billing_unit']) && !empty($clonedData[$field->id]['billing_unit']) ? $clonedData[$field->id]['billing_unit'] : '' }}"
                                                                                            @endif
                                                                                            {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                                            >
                                                                                </div> -->--}}

                                                                                <div class="col-sm-3 address-field inline-block">
                                                                                    <input autocomplete="new-password" 
                                                                                            type="text" 
                                                                                           class="form-control"
                                                                                           placeholder="City"
                                                                                           id="service_and_billing_address_billing_city_{{$i}}"
                                                                                           name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][billing_city]"
                                                                                            @if(isset($input['fields'][$i]['value']['billing_city']))
                                                                                                value="{{ $input['fields'][$i]['value']['billing_city']}}" 
                                                                                            @else
                                                                                                value="{{ isset($clonedData[$field->id]['billing_city']) && !empty($clonedData[$field->id]['billing_city']) ? $clonedData[$field->id]['billing_city'] : '' }}"
                                                                                            @endif
                                                                                            {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                                            >
                                                                                </div>
                                                                                <!-- Start : Div for billing_county -->
                                                                                <div class="col-sm-3 address-field inline-block">
                                                                                    <input autocomplete="new-password" 
                                                                                            type="text" 
                                                                                           class="form-control"
                                                                                           placeholder="County"
                                                                                           id="service_and_billing_address_billing_county_{{$i}}"
                                                                                           name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][billing_county]"
                                                                                            @if(isset($input['fields'][$i]['value']['billing_county']))
                                                                                                value="{{ $input['fields'][$i]['value']['billing_county']}}" 
                                                                                            @else
                                                                                                value="{{ isset($clonedData[$field->id]['billing_county']) && !empty($clonedData[$field->id]['billing_county']) ? $clonedData[$field->id]['billing_county'] : '' }}"
                                                                                            @endif
                                                                                            {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!} 
                                                                                            >
                                                                                </div>
                                                                                <!-- End  -->
                                                                                <div class="col-sm-3 address-field inline-block">
                                                                                    <input id="service_and_billing_address_billing_state_{{$i}}"    type="text" 
                                                                                           class="form-control stateall statefield address"
                                                                                           Placeholder="State"
                                                                                           autocomplete="new-password"
                                                                                           name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][billing_state]"
                                                                                            @if(isset($input['fields'][$i]['value']['billing_state']))
                                                                                                value="{{ $input['fields'][$i]['value']['billing_state']}}" 
                                                                                            @else
                                                                                                value="{{ isset($clonedData[$field->id]['billing_state']) && !empty($clonedData[$field->id]['billing_state']) ? $clonedData[$field->id]['billing_state'] : '' }}"
                                                                                            @endif
                                                                                            {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                                            >
                                                                                </div>

                                                                                <div class="col-sm-3 address-field inline-block">

                                                                                    <input class="form-control @if(array_get($field, 'is_primary')) zipcode-field @endif"
                                                                                           type="text"
                                                                                           name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][billing_zipcode]"
                                                                                           id="service_and_billing_address_billing_zipcode_{{$i}}"
                                                                                           autocomplete="new-password"
                                                                                           data-parsley-trigger="focusout"
                                                                                           data-parsley-pattern="[0-9]{5}"
                                                                                           data-parsley-pattern-message="Please enter 5 digit zipcode"
                                                                                           Placeholder="Zipcode"
                                                                                            @if(isset($input['fields'][$i]['value']['billing_zipcode']))
                                                                                                value="{{ $input['fields'][$i]['value']['billing_zipcode']}}" 
                                                                                            @else
                                                                                                value="{{ isset($clonedData[$field->id]['billing_zipcode']) && !empty($clonedData[$field->id]['billing_zipcode']) ? $clonedData[$field->id]['billing_zipcode'] : '' }}"
                                                                                            @endif
                                                                                            >
                                                                                </div>

                                                                                <div class="col-sm-3 address-field inline-block">
                                                                                    <input id="service_and_billing_address_billing_country_{{$i}}" style="display: none;"
                                                                                            type="text" 
                                                                                           class="form-control stateall statefield address "
                                                                                           Placeholder="Country"
                                                                                           autocomplete="new-password"
                                                                                           name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][billing_country]"
                                                                                           @if(isset($input['fields'][$i]['value']['billing_country']))
                                                                                                value="{{ $input['fields'][$i]['value']['billing_country']}}" 
                                                                                            @else
                                                                                                value="{{ isset($clonedData[$field->id]) && !empty($clonedData[$field->id]) ? $clonedData[$field->id]['billing_country'] : '' }}"
                                                                                            @endif
                                                                                            {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                                            >
                                                                                </div>
                                                                                    <input type="hidden"
                                                                                       name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][billing_lat]"
                                                                                       id="service_and_billing_address_billing_latitude_{{$i}}"
                                                                                       value="{{ isset($clonedData[$field->id]) && !empty($clonedData[$field->id]) ? $clonedData[$field->id]['billing_lat'] : '' }}">
                                                                                    <input type="hidden"
                                                                                       name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][billing_lng]"
                                                                                       id="service_and_billing_address_billing_longitude_{{$i}}"
                                                                                       value="{{ isset($clonedData[$field->id]) && !empty($clonedData[$field->id]) ? $clonedData[$field->id]['billing_lng'] : '' }}">
                                                                                </div>
                                                                            </div>

                                                                            <script>
                                                                                var input = document.getElementById('service_and_billing_address_billing_address_1_{{$i}}');
                                                                                var autocompleteBilling{{$i}} = new google.maps.places.Autocomplete(input, {
                                                                                    types: [],
                                                                                    componentRestrictions: {country: "us"}
                                                                                });
                                                                                google.maps.event.addListener(autocompleteBilling{{$i}}, 'place_changed', function () {
                                                                                    // alert("here in 4");
                                                                                    var place = autocompleteBilling{{$i}}.getPlace();
                                                                                    console.log(place);
                                                                                    $('#service_and_billing_address_billing_latitude_{{$i}}').val(place.geometry.location.lat());
                                                                                    $('#service_and_billing_address_billing_longitude_{{$i}}').val(place.geometry.location.lng());
                                                                                    @if(array_get($field, 'is_auto_caps') == 1)
                                                                                        $('#service_and_billing_address_billing_address_1_{{$i}}').val(place.name.toUpperCase());
                                                                                    @else
                                                                                        $('#service_and_billing_address_billing_address_1_{{$i}}').val(place.name);
                                                                                    @endif

                                                                                    var address2 = '';
                                                                                    for (var i = 0; i < place.address_components.length; i++) {
                                                                                        var addressType = place.address_components[i].types[0];
                                                                                        let index = place.address_components.length-1;
                                                                                        let zipcodeValue = place.address_components[index].types[0];

                                                                                        if (addressType === 'postal_code') {

                                                                                            $('#service_and_billing_address_billing_zipcode_{{$i}}').val(place.address_components[i].long_name);                                                                                     
                                                                                        }
                                                                                        if (addressType === "neighborhood" || addressType === "sublocality_level_1") {
                                                                                            @if(array_get($field, 'is_auto_caps') == 1)
                                                                                                address2 += place.address_components[i].long_name;
                                                                                                address2 += ' '
                                                                                            @else
                                                                                                address2 += place.address_components[i].long_name;
                                                                                                address2 += ' '
                                                                                            @endif
                                                                                        }
                                                                                        if (addressType === "locality" || addressType === "administrative_area_level_2") {
                                                                                            @if(array_get($field, 'is_auto_caps') == 1)
                                                                                                $('#service_and_billing_address_billing_city_{{$i}}').val(place.address_components[i].long_name.toUpperCase());
                                                                                            @else
                                                                                                $('#service_and_billing_address_billing_city_{{$i}}').val(place.address_components[i].long_name);
                                                                                            @endif
                                                                                        }
                                                                                        // Code billing_county
                                                                                        if (addressType === "administrative_area_level_2") {
                                                                                            @if(array_get($field, 'is_auto_caps') == 1)
                                                                                            $('#service_and_billing_address_billing_county_{{$i}}').val(place.address_components[i].long_name.toUpperCase());
                                                                                            @else
                                                                                            $('#service_and_billing_address_billing_county_{{$i}}').val(place.address_components[i].long_name);
                                                                                            @endif
                                                                                            
                                                                                        }
                                                                                        // End
                                                                                        if (addressType === "administrative_area_level_1") {
                                                                                            @if(array_get($field, 'is_auto_caps') == 1)
                                                                                                $('#service_and_billing_address_billing_state_{{$i}}').val(place.address_components[i].long_name.toUpperCase());
                                                                                            @else
                                                                                                $('#service_and_billing_address_billing_state_{{$i}}').val(place.address_components[i].long_name);
                                                                                            @endif
                                                                                        }
                                                                                        if (addressType === "country") {
                                                                                            @if(array_get($field, 'is_auto_caps') == 1)
                                                                                                $('#service_and_billing_address_billing_country_{{$i}}').val(place.address_components[i].long_name.toUpperCase());
                                                                                            @else
                                                                                                $('#service_and_billing_address_billing_country_{{$i}}').val(place.address_components[i].long_name);
                                                                                            @endif
                                                                                        }
                                                                                    }
                                                                                    let city = place.address_components.find(el =>
                                                                                            el['types'].includes('locality')
                                                                                    ).long_name || ''
                                                                                    
                                                                                    if(city == '') {
                                                                                        city = place.address_components.find(
                                                                                            el => el['types'].includes('administrative_area_level_2')).long_name || ''
                                                                                    }
                                                                                    @if(array_get($field, 'is_auto_caps') == 1)
                                                                                        $('#service_and_billing_address_billing_city_{{$i}}').val(city.toUpperCase());
                                                                                        $('#service_and_billing_address_billing_address_2_{{$i}}').val(address2.toUpperCase());
                                                                                    @else
                                                                                        $('#service_and_billing_address_billing_city_{{$i}}').val(city);
                                                                                        $('#service_and_billing_address_billing_address_2_{{$i}}').val(address2);
                                                                                    @endif
                                                                                });
                                                                            </script>
                                                                        </div>

                                                                    @elseif ($field->type == 'label')
                                                                    <div class="form-group" rel="label">
                                                                        <label class="label-font-increase control-label label-margin">{{ ucfirst(array_get($field, 'label')) }}</label>
                                                                    </div>

                                                                    @elseif ($field->type == 'label')
                                                                        <div class="form-group" rel="label">
                                                                            <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
                                                                        </div>

                                                                    @elseif ($field->type == 'phone_number')
                                                                        <label class="control-label {{$field->type}}-label" copy-from="{{$field->type.$i}}">{{ ucfirst(array_get($field, 'label')) }}</label>
                                                                        @if($field->is_allow_copy == 1)
                                                                            @include('frontend.user.copy_from',['label'=> $field->type.'-label' , 'copyTo' => $field->type.$i])
                                                                        @endif
                                                                        <div class="input-group w100 {{$field->type.$i}}"
                                                                             @if(array_get($field, 'is_verify') != 1) style="width: 100%" @endif>
                                                                            <input type="text"
                                                                                   autocomplete="new-password"
                                                                                   class="form-control mobile {{ array_get($field, 'is_verify' )==1 ? 'verifyPhone' : '' }}"
                                                                                   name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][value]" 
                                                                                    @if(isset($input['fields'][$i]['value']['value']))
                                                                                        value="{{ $input['fields'][$i]['value']['value']}}" 
                                                                                    @else
                                                                                        value="{{ isset($clonedData[$field->id]['value']) && !empty($clonedData[$field->id]['value']) ? $clonedData[$field->id]['value'] : '' }}"
                                                                                    @endif
                                                                                   placeholder="{{ $field->name }}"
                                                                                   data-parsley-trigger="focusout"
                                                                                   data-parsley-pattern="[0-9]{10}"
                                                                                   data-parsley-pattern-message="Please enter a 10 digit phone number"
                                                                                   @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                                                                    @endif>
                                                                                    @if(array_get($field, 'is_verify') == 1)
                                                                                        <span class="input-group-btn">
                                                                                            <button type="button"
                                                                                                    class="btn btn-default searchzipcode" disabled
                                                                                                    id="verifyPhone">Verify</button>
                                                                                        </span>
                                                                                    @endif
                                                                                   <input type="hidden" name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][is_primary]" value="{{$field->is_primary}}" />
                                                                        </div>

                                                                    @elseif ($field->type == 'email')

                                                                        <label class="control-label {{$field->type}}-label" copy-from="{{$field->type.$i}}">{{ ucfirst(array_get($field, 'label')) }}</label>
                                                                        @if($field->is_allow_copy == 1)
                                                                            @include('frontend.user.copy_from',['label'=> $field->type.'-label' , 'copyTo' => $field->type.$i])
                                                                        @endif
                                                                        <div class="input-group w100 {{$field->type.$i}}" 
                                                                             @if(array_get($field, 'is_verify') != 1) style="width: 100%" @endif>
                                                                            <input type="email"
                                                                                   autocomplete="new-password"
                                                                                   class="form-control email {{ array_get($field, 'is_required' )==1 ? 'required' : '' }} {{ array_get($field, 'is_verify' )==1 ? 'verifyEmail' : '' }}"
                                                                                   name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][value]"
                                                                                    @if(isset($input['fields'][$i]['value']['value']))
                                                                                        value="{{ $input['fields'][$i]['value']['value']}}" 
                                                                                    @else
                                                                                        value="{{ isset($clonedData[$field->id]['value']) && !empty($clonedData[$field->id]['value']) ? $clonedData[$field->id]['value'] : '' }}"
                                                                                    @endif
                                                                                   placeholder="{{ $field->name }}"
                                                                                   data-parsley-trigger="focusout" data-parsley-type="email" data-parsley-type-message="Please enter a valid email"
                                                                                   data-parsley-pattern="/\S+@\S+\.\S+/"
                                                                                   data-parsley-pattern-message="Please enter a valid email"
                                                                                   @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                                                                    @endif
                                                                                    {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                                                                >
                                                                            @if(array_get($field, 'is_verify') == 1)
                                                                                <span class="input-group-btn">
                                                                                    <button type="button"
                                                                                            class="btn btn-default searchzipcode" disabled
                                                                                            id="verifyEmail">Verify</button>
                                                                                </span>
                                                                            @endif
                                                                            <input type="hidden" name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][is_primary]" value="{{$field->is_primary}}" />
                                                                        </div>



                                                                    @elseif($field->type == 'selectbox')
                                                                        @php 
                                                                            $label = ucfirst(array_get($field, 'label'));
                                                                            $search = config('constants.ECOGOLD_PROGRAM_LABEL');
                                                                            $labelMatch = preg_match("/{$search}/i", $label);

                                                                            $searchPromoCode = config('constants.PROMO_CODE_FIELD_LABEL');
                                                                            $isPromoCode = preg_match("/{$searchPromoCode}/i", $label);

                                                                        @endphp
                                                                        <div class="form-group" rel="label">
                                                                            <label class="control-label">{{$label}}</label>
                                                                            <select name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][value]"
                                                                                    class="select2 form-control @if($labelMatch) ecogold-program @endif @if($isPromoCode) promo-code-field @endif"
                                                                                    title="Please enter {{strtolower(array_get($field, 'label'))}}"
                                                                                    data-parsley-trigger="focusout"
                                                                                    data-parsley-errors-container="#select2-clientform-error-message-{{$i}}"
                                                                                    @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                                                                     @endif>
                                                                                     <option value="">Select</option>
                                                                                @foreach($field->meta as $mVal)
                                                                                    @foreach($mVal as $option)
                                                                                        <option value="{{$option['option']}}" {{ isset($clonedData[$field->id]['value']) && !empty($clonedData[$field->id]['value']) ? $clonedData[$field->id]['value'] == $option['option'] ? 'selected' : '' : '' }}
                                                                                        @if(isset($input['fields'][$i]['value']['value']) && $input['fields'][$i]['value']['value'] == $option['option']) 
                                                                                        selected
                                                                                        @endif
                                                                                        >{{ $option['option'] }}</option>
                                                                                    @endforeach
                                                                                @endforeach
                                                                            </select>
                                                                            <span id="select2-clientform-error-message-{{$i}}"></span>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <?php $i++; ?>
                                                            @endforeach
                                                            </div>
                                                            <div style="clear:both"></div>
                                                            <div class="multienrollmentData">
                                                                @php
                                                                    $x=1;
                                                                    $current_enrollment_number = 2;
                                                                @endphp
                                                                @if(isset($clonedChildData) && !empty($clonedChildData))
                                                                    @foreach($clonedChildData as $childData)
                                                                        @include('frontend.client.multi_enrollment_field',['client' => $client,'form' => $form, 'zipcode' => $zipcode, 'commodities' => $commodities, 'fields' => $childFields, 'states' => $states, 'multienrollmentIncrement' => $x, 'clonedData' => $childData,'current_enrollment_number', $x+1,'customFields' => $customFields])
                                                                        @php
                                                                            $x++;
                                                                            $current_enrollment_number++;
                                                                        @endphp
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                            <div class="form-group mt60 ">
                                                            @if($form->multienrollment == 1)
                                                                <button type="button"
                                                                        class="btn btn-green btn-center addEnrollment">Add Enrollment
                                                                </button>           
                                                            @endif
                                                            <div class="form-group mt60 ">
                                                            
                                                            <button type="button"
                                                                    class="btn btn-green btn-center submitBtn">Submit
                                                            </button>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade confirmation-model" id="verifyEmailPopup">
       
            <div class="modal-dialog modal-custom-fit-content">
                <div class="modal-content">
                    <div class="modal-header"><h4>Verify Email</h4></div>
                    <form role="form" id="verifyOTPEmail" autocomplete="on" data-parsley-validate>
                    <div class="modal-body pd15">
                     @csrf
                        <input type="hidden" name="email" id="verifyToEmail" value="">
                        <div class="form-group" rel="label">
                            <label class="control-label">OTP</label>
                            <div id="OtpDivOuter">
                              <div id="OtpDivInner">
                                <input id="verify-otp-input" type="text"
                                       autocomplete="off"
                                       class="form-control mobile"
                                       name="otp"
                                       data-parsley-trigger="focusout"
                                       data-parsley-pattern="[0-9]{6}"
                                       data-parsley-pattern-message="Please enter 6 digit OTP"
                                       data-parsley-required='true'
                                       maxlength="6"
                                       onkeypress="if(this.value.length==6) return false;"
                                       >
                               </div>
                            </div>
                            <div class="text-right">
                                <a href="#" class="text-right" id="resendEmailOTP">Resend OTP</a>
                            </div>
                        </div>
                    </div>
                   
                    <div class="modal-footer">
                        <div class="btnintable bottom_btns pd0">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-green">Verify</button>
                                <button type="button" class="btn btn-red" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
       
    </div>
    <div class="modal fade confirmation-model" id="verifyPhonePopup">
        
            <div class="modal-dialog modal-custom-fit-content">
                <div class="modal-content">
                    <div class="modal-header"><h4>Verify Phone</h4></div>
                    <form role="form" id="verifyOTPPhone" autocomplete="on" data-parsley-validate>
                    <div class="modal-body pd15">
                        @csrf
                        <input type="hidden" name="phone_number" id="verifyToPhone" value="">
                        <div class="form-group" rel="label">
                            <label class="control-label">OTP</label>
                            <div id="OtpDivOuter">
                              <div id="OtpDivInner">
                                <input id="verify-otp-input" type="text"
                                       autocomplete="off"
                                       class="form-control mobile"
                                       name="otp"
                                       data-parsley-trigger="focusout"
                                       data-parsley-pattern="[0-9]{6}"
                                       data-parsley-pattern-message="Please enter 6 digit OTP"
                                       data-parsley-required='true'
                                       maxlength="6"
                                       onKeyPress="if(this.value.length==6) return false;">
                                </div>
                            </div>
                            <div class="text-right">
                                <a href="#" class="text-right" id="resendPhoneOTP">Resend OTP</a>
                            </div>
                        </div>
                    </div>
                   
                    <div class="modal-footer">
                        <div class="btnintable bottom_btns pd0">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-green">Verify</button>
                                <button type="button" class="btn btn-red" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
       
    </div>

    <div class="modal fade confirmation-model" id="check-utilities-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="mt15 text-center mb15">
                <?php echo getimage('/images/alert-danger.png') ?>
                <p class="logout-title">Are you sure?</p>
            </div>
                    <div class="mt20 text-center">
                        There are currently no services available in this region.
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="btnintable bottom_btns pd0">
                        <button type="button" class="btn btn-red" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade confirmation-model" id="verifyEmailPopupChild">
       
        <div class="modal-dialog modal-custom-fit-content">
            <div class="modal-content">
                <div class="modal-header"><h4>Verify Email</h4></div>
                <form role="form" id="verifyOTPEmailChild" autocomplete="on" data-parsley-validate>
                <div class="modal-body pd15">
                 @csrf
                    <input type="hidden" name="email" class="verifyToEmailhidden" id="verifyToEmail-{{$multienrollmentIncrement}}" value="">
                    <input type="hidden" name="childcountemail" id="childLeadCountEmail" value="{{$multienrollmentIncrement}}">
                    <div class="form-group" rel="label">
                        <label class="control-label">OTP</label>
                        <div id="OtpDivOuter">
                          <div id="OtpDivInner">
                            <input id="verify-otp-input" type="text"
                                   autocomplete="off"
                                   class="form-control mobile"
                                   name="otp"
                                   data-parsley-trigger="focusout"
                                   data-parsley-pattern="[0-9]{6}"
                                   data-parsley-pattern-message="Please enter 6 digit OTP"
                                   data-parsley-required='true'
                                   maxlength="6"
                                   onkeypress="if(this.value.length==6) return false;"
                                   >
                           </div>
                        </div>
                        <div class="text-right">
                            <a href="#" class="text-right" id="resendEmailOTPChild">Resend OTP</a>
                        </div>
                    </div>
                </div>
               
                <div class="modal-footer">
                    <div class="btnintable bottom_btns pd0">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-green">Verify</button>
                            <button type="button" class="btn btn-red" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade confirmation-model" id="verifyPhonePopupChild">
        
        <div class="modal-dialog modal-custom-fit-content">
            <div class="modal-content">
                <div class="modal-header"><h4>Verify Phone</h4></div>
                <form role="form" id="verifyOTPPhoneChild" autocomplete="on" data-parsley-validate>
                <div class="modal-body pd15">
                    @csrf
                    <input type="hidden" name="phone_number" class="verifyToPhone" id="verifyToPhone-{{$multienrollmentIncrement}}" value="">
                    <input type="hidden" name="childcount" id="childLeadCount" value="{{$multienrollmentIncrement}}">
                    <div class="form-group" rel="label">
                        <label class="control-label">OTP</label>
                        <div id="OtpDivOuter">
                          <div id="OtpDivInner">
                            <input id="verify-otp-input" type="text"
                                   autocomplete="off"
                                   class="form-control mobile"
                                   name="otp"
                                   data-parsley-trigger="focusout"
                                   data-parsley-pattern="[0-9]{6}"
                                   data-parsley-pattern-message="Please enter 6 digit OTP"
                                   data-parsley-required='true'
                                   maxlength="6"
                                   onKeyPress="if(this.value.length==6) return false;">
                            </div>
                        </div>
                        <div class="text-right">
                            <a href="#" class="text-right" id="resendPhoneOTPChild">Resend OTP</a>
                        </div>
                    </div>
                </div>
               
                <div class="modal-footer">
                    <div class="btnintable bottom_btns pd0">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-green">Verify</button>
                            <button type="button" class="btn btn-red" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
</div>
@include('frontend.client.otp_type_modal')

    <script type="text/javascript">
        const ECOGOLD_PROGRAM_CODE = "{{config('constants.ECOGOLD_PROGRAM_CODE')}}";
        const ECOGOLD_CODE_WITHOUT_SG = @json(config('constants.ECOGOLD_CODE_WITHOUT_SG'));
        const PROMO_CODE_GIFT = @json(config('constants.PROMO_CODE_PROGRAM.GIFT'));
        const PROMO_CODE_ALL = @json(config('constants.PROMO_CODE_PROGRAM.ALL'));
        const ECOGOLD_PROGRAM_CODE_1_KIWI_ENERGY = "{{config('constants.ECOGOLD_PROGRAM_CODE_1_KIWI_ENERGY')}}";
        const ECOGOLD_PROGRAM_CODE_2_KIWI_ENERGY = "{{config('constants.ECOGOLD_PROGRAM_CODE_2_KIWI_ENERGY')}}";
        const ECOGOLD_PROGRAM_CODE_3_KIWI_ENERGY = "{{config('constants.ECOGOLD_PROGRAM_CODE_3_KIWI_ENERGY')}}";
        const KIWI_BRAND_NAME = "{{config('constants.KIWI_BRAND_NAME')}}";
        var stateErrorMessage = "Your selected address's state doesn't match with the service state you entered earlier. Please choose the correct address with the same state as service state";
        var zipcodeErrorMessage = "Your selected address's zipcode doesn't match with the service zipcode you entered earlier. Please choose the correct address with the same zipcode as service zipcode";
        var utilities = "<?php echo $client->id; ?>";
        $(function () {
            // for check contain case-incentive
            $.extend($.expr[":"], {
                "icontains": function(elem, i, match, array) {
                    return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
                }
            });
            $("#zipcode").autocomplete({
                source: function (request, response) {
                    $.getJSON("{{ route('ajax.zipcodeSearch') }}", {
                        term: request.term
                    }, function (data) {
                        response($.map(data, function (item) {
                            var city=item.city.charAt(0).toUpperCase() + item.city.slice(1).toLowerCase();
                            return {
                                label: item.zipcode + ' ' + city + ' ' + item.state,
                                value: item.zipcode
                            }
                        }));
                    });
                },
                search: function () {
                    // custom minLength
                    var term = this.value;
                    if (term.length < 2) {
                        return false;
                    }
                },
                focus: function () {
                    // prevent value inserted on focus
                    return false;
                },
            });

            function generateEmailOtp(email) {
                alert('parent');
                if (email !== '') {
                    $('#verifyToEmail').val(email);
                    $.ajax({
                        type: "GET",
                        url: "{{ route('ajax.generate-otp-email') }}",
                        data: {
                            'email': email
                        },
                        success: function (res) {
                            console.log(res);
                            if (res.status == 'success') {
                                $('#verifyOTPEmail').trigger("reset");
                                $('#verifyEmailPopup').modal('show')
                            } else {
                                alert(res.message);
                                $('#verifyToEmail').val('');
                            }
                        },
                        error: function (res) {
                            console.log(res);
                            alert(res.message);
                            $('#verifyToEmail').val('');
                        }
                    });
                } else {
                    alert('Please enter email');
                }
            }
            function generateEmailOtpChild(email) {
                console.log(email);
                if (email !== '') {
                    $('.verifyToEmailhidden').val(email);
                    $.ajax({
                        type: "GET",
                        url: "{{ route('ajax.generate-otp-email') }}",
                        data: {
                            'email': email
                        },
                        success: function (res) {
                            console.log(res);
                            if (res.status == 'success') {
                                $('#verifyOTPEmailChild').trigger("reset");
                                $('#verifyEmailPopupChild').modal('show')
                            } else {
                                alert(res.message);
                                $('.verifyToEmailhidden').val('');
                            }
                        },
                        error: function (res) {
                            console.log(res);
                            alert(res.message);
                            $('.verifyToEmailhidden').val('');
                        }
                    });
                } else {
                    alert('Please enter email');
                }
            }
            
            function generatePhoneOtpChild(phone) {
                
                if (phone !== '') {
                    $('.verifyToPhone').val(phone);
                    $.ajax({
                        type: "GET",
                        url: "{{ route('ajax.generate-otp-phone') }}",
                        data: {
                            'phone_number': phone,
                            'otp_type': $("input[name='otp_type']:checked").val()
                        },
                        success: function (res) {
                            console.log(res);
                            if (res.status == 'success') {
                                $('#verifyOTPPhoneChild').trigger("reset");
                                $('#verifyPhonePopupChild').modal('show')
                            } else {
                                alert(res.message);
                                $('.verifyToPhone').val('');
                            }
                        },
                        error: function (err) {
                            console.log(err);
                            // alert(res.message);
                            $('.verifyToPhone').val('');
                        }
                    });
                } else {
                    alert('Please enter phone');
                }
            }
            $('#verifyOTPEmailChild').on('submit', function (e) {
                let childId = $('#childLeadCountEmail').val();
                console.log($('#verifyOTPEmailChild').serialize());
                if($('#verifyOTPEmailChild').parsley().isValid()) {
                    e.preventDefault();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('ajax.verify-otp-email') }}",
                        data: $('#verifyOTPEmailChild').serialize(),
                        success: function (res) {
                            console.log(res);
                            if (res.status == 'success') {
                                $('#verifyEmailPopupChild').modal('hide')
                                // verifyEmailChildBtn-{{$multienrollmentIncrement}}
                                $('#verifyEmailChildBtn-'+childId).html('Verified');
                                $('#verifyEmailChildBtn-'+childId).attr('disabled', true);
                            } else {
                                alert(res.message);
                            }
                        },
                        error: function (res) {
                            console.log(res);
                            alert(res.message);
                        }
                    });
                }
            });
            
            $('#verifyOTPPhoneChild').on('submit', function (e) {
                if($('#verifyOTPPhoneChild').parsley().isValid()) {
                    e.preventDefault();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('ajax.verify-otp-phone') }}",
                        data: $('#verifyOTPPhoneChild').serialize(),
                        success: function (res) {
                            console.log(res);
                            if (res.status == 'success') {
                                $('#verifyPhonePopupChild').modal('hide')
                                $('.verifyPhonechild').html('Verified');
                                $('.verifyPhonechild').attr('disabled', true);
                            } else {
                                alert(res.message);
                            }
                        },
                        error: function (res) {
                            console.log(res);
                            alert(res.message);
                        }
                    });
                }
            });

            $('#zipcode').on('change', function (e) {
                $(this).parsley().validate();
            });
            $('#resendEmailOTP').on('click', function (e) {
                var email = $('#verifyToEmail').val();
                generateEmailOtp(email);
            });

            $('#resendPhoneOTPChild').on('click', function (e) {
                let childId = $('#childLeadCount').val();
                var phone = $('#verifyPhoneId-'+childId).val();
                generatePhoneOtpChild(phone);
            });
            $('#resendEmailOTPChild').on('click', function (e) {
                let childId = $('#childLeadCount').val();
                var email = $('#verifyEmailId-'+childId).val();
                generateEmailOtpChild(email);
            });

            $('.verifyEmailChild').on('click', function (e) {
                console.log('Verify Btn');
                let id = $('#childLeadCountEmail').val();
                var email = $('#verifyEmailId-'+id).val();
                console.log(id);
                generateEmailOtpChild(email);
            });

            $('#verifyEmail').on('click', function (e) {
                console.log('Verify Btn');
                var email = $('.verifyEmail').val();
                generateEmailOtp(email);
            });

            $('#verifyOTPEmail').on('submit', function (e) {


                if($('#verifyOTPEmail').parsley().isValid()) {
                    e.preventDefault();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('ajax.verify-otp-email') }}",
                        data: $('#verifyOTPEmail').serialize(),
                        success: function (res) {
                            console.log(res);
                            if (res.status == 'success') {
                                $('#verifyEmailPopup').modal('hide')
                                $('#verifyEmail').html('Verified');
                                $('#verifyEmail').attr('disabled', true);
                            } else {
                                alert(res.message);
                            }
                        },
                        error: function (res) {
                            console.log(res);
                            alert(res.message);
                        }
                    });
                }
            })

            $('.verifyEmail').keyup(function () {
                console.log('change')
                var current_email = $('.verifyEmail').val();
                var email = $('#verifyToEmail').val();

                if(!$(this).parsley().isValid()){
                    console.log("empty")
                    $('#verifyEmail').attr('disabled', 'disabled');
                } else {
                    if(email == current_email && email != "") {
                        $('#verifyEmail').html('Verified');
                        $('#verifyEmail').attr('disabled', 'disabled');
                    } else {
                        $('#verifyEmail').html('Verify');
                        $('#verifyEmail').removeAttr('disabled');
                    }
                }


            })

            /************** for phone otp ***********/
            function generatePhoneOtp(phone) {
                if (phone !== '') {
                    $('#verifyToPhone').val(phone);
                    $.ajax({
                        type: "GET",
                        url: "{{ route('ajax.generate-otp-phone') }}",
                        data: {
                            'phone_number': phone,
                            'otp_type': $("input[name='otp_type']:checked").val()
                        },
                        success: function (res) {
                            console.log(res);
                            if (res.status == 'success') {
                                $('#verifyOTPPhone').trigger("reset");
                                $('#verifyPhonePopup').modal('show')
                            } else {
                                alert(res.message);
                                $('#verifyToPhone').val('');
                            }
                        },
                        error: function (err) {
                            console.log(err);
                            // alert(res.message);
                            $('#verifyToPhone').val('');
                        }
                    });
                } else {
                    alert('Please enter phone');
                }
            }
            $('#resendPhoneOTP').on('click', function (e) {
                var phone = $('#verifyToPhone').val();
                generatePhoneOtp(phone);
            });

            $('#verifyPhone').on('click', function (e) {
                $("#select-otp-type-modal").modal("show");
            });

            $('#frm-otp-type').on('submit', function (e) {
                let isChild = $('.childLeadValue').val();
                if(isChild == 0){
                    if($('#frm-otp-type').parsley().isValid()) {
                        var phone = $('.verifyPhone').val();
                        generatePhoneOtp(phone);
                        $("#select-otp-type-modal").modal("hide");
                        return false;
                    }
                }
                else if(isChild == 1){
                    if($('#frm-otp-type').parsley().isValid()) {
                        let childId = $('#childLeadCount').val();
                        var phone = $('#verifyPhoneId-'+childId).val();
                        console.log(phone);
                        generatePhoneOtpChild(phone);
                        $("#select-otp-type-modal").modal("hide");
                        return false;
                    }
                }
            });

            $('#verifyOTPPhone').on('submit', function (e) {
                if($('#verifyOTPPhone').parsley().isValid()) {
                    e.preventDefault();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('ajax.verify-otp-phone') }}",
                        data: $('#verifyOTPPhone').serialize(),
                        success: function (res) {
                            console.log(res);
                            if (res.status == 'success') {
                                $('#verifyPhonePopup').modal('hide')
                                $('#verifyPhone').html('Verified');
                                $('#verifyPhone').attr('disabled', true);
                            } else {
                                alert(res.message);
                            }
                        },
                        error: function (res) {
                            console.log(res);
                            alert(res.message);
                        }
                    });
                }
            });

            $('.verifyPhone').keyup(function () {
                var current_Phone = $(this).val();
                var Phone = $('#verifyToPhone').val();
                $('.childLeadValue').val('0');
                if(!$(this).parsley().isValid()){
                    console.log("empty")
                    $('#verifyPhone').attr('disabled', 'disabled');
                } else {
                    if(Phone == current_Phone && Phone != "") {
                        $('#verifyPhone').html('Verified');
                        $('#verifyPhone').attr('disabled', 'disabled');
                    } else {
                        $('#verifyPhone').html('Verify');
                        $('#verifyPhone').removeAttr('disabled');
                    }
                }


            });

            // for copy input from another input
            $(document).on("click",".copy", function (e) {
                let inputType = "input[type='text'],input[type='email'],textarea";
                let copyFrom = $(this).attr('copy-from');
                let copyTo = $(this).attr('copy-to');
                var copyFromInput = $("."+copyFrom).find(inputType);
                var copyToInput = $("."+copyTo).find(inputType);
                for (var i = 0; i < copyFromInput.length; i++) {
                    let value = copyFromInput.eq(i).val();
                    copyToInput.eq(i).val(value);
                    copyToInput.eq(i).parsley().validate();
                }
                console.log('copy...');
                $('.popover').each(function() {
                    $(this).data("bs.popover").inState.click = false;
                    $(this).popover('hide');
                });
            });

            // for open drop down copy from
            $(".copy-from").on("click", function (e) {
                var copyElement = this;
                let labelType = $(copyElement).attr('type');
                let copyTo = $(copyElement).attr('copy-to');
                var data = "<ul class='copy-container'>";
                $("."+labelType).each(function() {
                    let copyFrom = $(this).attr('copy-from');
                    if (copyFrom != copyTo) {
                        let values = $("."+copyFrom).find("input[type='text'],input[type='email'],textarea").map(function(){
                            if (this.value != '') {
                                return this.value.trim();
                            }
                        }).get()
                        if (labelType == 'address-label') {
                            values = values.join(', ');
                        } else {
                            values = values.join(' '); 
                        }
                        let strSize = 30;
                        if (values.length > strSize) {
                            inputValue = values.slice(0, strSize) + ' ...';
                        } else {
                            inputValue = values;
                        }
                        console.log(inputValue);
                        data +="<li class='pointer copy' copy-from='"+copyFrom+"' copy-to='"+copyTo+"'><h5>"+$(this).text()+"</h5><span>"+inputValue+"</span></li>";
                    }
                })
                data +="</ul>";
                $(copyElement).attr('data-content',data);
                $(copyElement).popover('show');
                $('.popover').css('left', '214px');
            });

            $('#next-btn').click(function () {
                updateForm();
            });
            @if(isset($fields) && !empty($fields))
                $('#zipcode,#state').change(function () {
                    updateForm();
                });
            @else
                $('#zipcode').focus(function () {
                    $("#state").val('').trigger('change.select2');
                    $("#search_type_zipcode").prop('checked',true);
                });
                // $('#state').focus(function () {
                //     $("#zipcode").val('');
                //     $("#search_type_state").prop('checked',true);
                // });
                $('#state').on('select2:open', function (e)
                {
                    $("#zipcode").val('');
                    $("#search_type_state").prop('checked',true);
                });
            @endif
        });

        var unsaved = false;

        $(":input").change(function () { //triggers change in all input fields including text type
            if($(this).attr('name') != 'zipcode') {
                unsaved = true;
            }
        });

        $('.searchzipcode').click(function () {
            unsaved = false;
        });

        /* Enrollment Form Submit */
        $('.submitBtn').click(function (e) {
            e.preventDefault();
            var form = $("#leadForm");
            form.parsley().validate();
            /* Check Parsly Validation */
            if (form.parsley().isValid()){
                /* Fire Ajax request validate data */
                var token = $("input[name=_token]").val();
                var zipcode = $("#zipcode").val();
                var state = $("#state").val();
                var form_id = "<?php echo $form->id; ?>";
                var client_id = "<?php echo $client->id; ?>";
                $.ajax({
                    type: "POST",
                    url: "<?php echo e(route('client.lead.validate.customer')); ?>",
                    data: {
                        '_token': token,
                        'form_id': form_id,
                        'client_id': client_id,
                        'data':$('#leadForm').serialize(),
                        'zipcode': zipcode,
                        'state': state
                    },
                    success: function (response) {
                        /* Check Request Response */
                        if (response.status == 'success') {
                            unsaved = false;
                            @if (isset($input['state']) && !empty($input['state']))
                            let isError = validateState();
                            @else
                            let isError = validateZipcode();
                            @endif
                            if(!$(".set-program" ).is(':empty') && !isError){
                                $("#leadForm").submit();
                            } else {
                                printProgramError();
                            }

                        } else {
                            unsaved = false;
                            alert(response.message);
                            @if (isset($input['state']) && !empty($input['state']))
                            let isError = validateState();
                            @else
                            let isError = validateZipcode();
                            @endif
                            if($(".set-program" ).is(':empty') && isError){
                                printProgramError();
                            }
                        }
                    }
                });
            }
        });
        var multienrollmentIncrement = <?php echo count($clonedChildData) ?>;
        var totalEnrollment =  parseInt($("#total_enrollment").val());
        $('.addEnrollment').click(function (e) {
            e.preventDefault();
            multienrollmentIncrement = multienrollmentIncrement+1;
            // /form-group fg30 multi-enroll-0
            var token = $("input[name=_token]").val();
            var form_id = "<?php echo $form->id; ?>";
            var client_id = "<?php echo $client->id; ?>";
            var state = $("#state").val();
            var zipcode = $("#zipcode").val();
            var current_enrollment_number = parseInt($("#current_enrollment_number").val());
            current_enrollment_number = current_enrollment_number+1;
            $.ajax({
                type: "POST",
                url: "<?php echo e(route('client.lead.add.fields')); ?>",
                data: {
                    '_token': token,
                    'form_id':form_id,
                    'client_id':client_id,
                    'state': state,
                    'zipcode' : zipcode,
                    'multienrollmentIncrement' : multienrollmentIncrement,
                    'current_enrollment_number' : current_enrollment_number,
                },
                success: function (response) {
                    $(".multienrollmentData").append(response);
                    $("#current_enrollment_number").val(current_enrollment_number);
                    totalEnrollment = totalEnrollment + 1;
                    $("#total_enrollment").val(totalEnrollment);
                    var x = 2;
                    $(".htmlTextForEnrollmentNO").each(function(){
                        $(this).html(x);
                        x = x+1;
                    })
                },
                fail: function () {
                    // $('#next-btn').removeAttr('disabled')
                }
            });
            $('.select2').select2();
        });

        $('#edit-btn').click(function () {
            $("#zipcode").removeAttr('readonly');
            $("#state").prop('disabled',false);
            $(this).hide();
            $("#next-btn").show();
        });

        function updateForm() {
            var zipcode = $('#zipcode').val();
            // alert(zipcode);
            var token = $("input[name=_token]").val();
            var form_id = "<?php echo $form->id; ?>";
            var client_id = "<?php echo $client->id; ?>";
            window.checkUtilities = "<?php echo e(route('client.get.utilities')); ?>";
            if (zipcode != "" && $("#zipcode").parsley().isValid()) {
                $.ajax({
                    type: "POST",
                    url: checkUtilities,
                    data: {
                        '_token': token,
                        'zipcode': zipcode,
                        'form_id':form_id,
                        'client_id':client_id
                    },
                    success: function (response) {
                        if (response.status == 'success') {
                            formSubmit();
                        } else {
                            
                            //$('#next-btn').removeAttr('disabled')
                            $("#check-utilities-modal").modal('toggle');
                        }
                    },
                    fail: function () {
                       // $('#next-btn').removeAttr('disabled')
                    }
                });
            } else if($("#state").val() != '') {
                formSubmit();
            }
        }

        // for submit lead form
        function formSubmit() {
            $('#lead_from_input').val('');
            $('#leadForm').parsley().destroy();
            $('#leadForm').submit();
        }

        function validateZipcode() {
            var zipcode = $("#zipcode").val();
            var error = false;
            $(".zipcode-error").html('');
            $( ".zipcode-field" ).each(function() {
                if ($(this).val() != '' && $(this).val() != zipcode) {
                    console.log('zipcode not match');
                    error = true;
                    $(this).closest('.address-block').find('.zipcode-error').html(zipcodeErrorMessage);
                }
            });
            return error;
        }

        function validateState() {
            var state = $("#state").val();
            var error = false;
            $(".zipcode-error").html('');
            $( ".state-field" ).each(function() {
                if ($(this).attr('short-name') != '' && $(this).attr('short-name') != state) {
                    console.log('state not match');
                    error = true;
                    $(this).closest('.address-block').find('.zipcode-error').html(stateErrorMessage);
                }
            });
            return error;
        }

        function printProgramError() {
            $( ".set-program" ).each(function() {
                if($(this).is(':empty')){
                    $(this).closest('.lead-select-parent').find('.program-error').html("<span class='help-block' >This field is required</span>");
                }else {
                    $(this).closest('.lead-select-parent').find('.program-error').html('');
                }
            });
        }

        function printProgramErrorOnClick(select_element) {
            if(select_element.find('.set-program').is(':empty')){
                select_element.find('.program-error').html("<span class='help-block' >This field is required</span>");
            }else {
                select_element.find('.program-error').html('');
            }
        }

        function unloadPage() {
            if (unsaved) {
                return "Changes that you made may not be saved.";
            }
        }
        //var zipcode=document.getElementById('zipcode').value;
        // $('#service_and_billing_address_service_address_1_1').onblur(function(){

        // var autocomplete;
        // var countryRestrict = {postalCode: zipcode}
        // function initAutocomplete() {
        //     autocomplete = new google.maps.places.Autocomplete(
        //     (document.getElementById('service_and_billing_address_service_address_1_1')),
        //     {

        //         componentRestrictions: countryRestrict
        //     });
        //   alert("hello");
        // }
        // });

    /*------new-design-selectbox--*/

    $(document).on("click",".lead-select",function() {
        var is_open = $(this).hasClass("open");
        if (is_open) {
            $(this).removeClass("open");
        } else {
            $(this).addClass("open");
            $('.lead-select-arrow').css('display','none');

        }
        $('body').trigger('click');
    });

    $(document).on("click",".lead-select li",function() {

        var selected_value = $(this).html();
        var comodityId = $(this).data('commodity-id');
        var multienrollmentId = $(this).data('multienrollment');
        var select_element =$(this).closest(".lead-select");
        var first_li = select_element.find(".sel-value").html();
        var program_id = $(this).find('.program-id').val();
        var program_code = $(this).find('.program-code-text').text();
        var currentMultienrollmentId = $(this).find('.current_multienrollment_number').val();
        if(program_id > 0) {
            var input_program = "<input type='hidden' value='"+program_id+"' name='program["+currentMultienrollmentId+"][]'>";
        } else {
            var input_program='';
        }
        select_element.find(".sel-value").html(selected_value);
        select_element.find(".set-program").html(input_program);
        
        $(this).html(first_li);
        @if( $client->id == config('constants.CLIENT_RRH_CLIENT_ID'))
            setEcogoldProgramOption(program_code,this);
            setPromoCodeFieldOption(program_code,this);
            @if(isset($commodities) && !empty($commodities))
                let firstComId = '{{ $commodities[0]->id }}';
                let totalComCount = '{{ $commodities->count() }}';
                console.log('{{$multienrollmentIncrement}} Com Id');

                if(totalComCount > 1 && comodityId == firstComId && comodityId != undefined){
                    if(currentMultienrollmentId == 0) {
                        @if($commodities->count() > 1)
                        setProgramsInDropdown('#programOption_{{ $commodities[1]->id }}_{{$multienrollmentIncrement}}','{{ $commodities[1]->id }}');
                        @endif
                    }
                    if(currentMultienrollmentId > 0) {
                        @if($commodities->count() > 1)
                            setProgramsChildInDropdown('#programOption_{{ $commodities[1]->id }}_'+currentMultienrollmentId+'','{{ $commodities[1]->id }}');
                        @endif
                    }
                }
            @endif
        @endif
        var select_ref =$(this).closest(".lead-select-parent");
        printProgramErrorOnClick(select_ref);

    });

    $(document).mouseup(function(event) {

        var target = event.target;
        var select = $(".lead-select");

        if (!select.is(target) && select.has(target).length === 0) {
            select.removeClass("open");
        }

    });
    @if(!empty(request('lid')))
    $(function () {
        $( "input[name='program[{{ $multienrollmentIncrement }}][]']" ).each(function() {
            $( "input[value='"+$(this).val()+"']" ).closest("li").trigger('click');
            getutilityValidations();
            $( "input[value='"+$(this).val()+"']" ).closest(".lead-select").trigger('click');
            
        });
    });
    @endif
    @if(isset($input['state']) && !empty($input['state']))
        validateState();
    @elseif(isset($input))
        validateZipcode();
    @endif
    /*-----end-new-design-selectbox--*/
    window.onbeforeunload = unloadPage;

    function printZipcodeErrorMessage(className)
    {
        $('.'+className).text(zipcodeErrorMessage);
    }

    function printStateErrorMessage(className)
    {
        $('.'+className).text(stateErrorMessage);
    }

    function resetServiceBillingFields(addressType,type,index)
    {
        if(type == '')
        {
            $('#'+addressType+'_line_1_'+index).val('');
            $('#'+addressType+'_line_2_'+index).val('');
            $('#'+addressType+'_city_'+index).val('');
            // For county
            $('#'+addressType+'_county_'+index).val('');
            // End

            $('#'+addressType+'_state_'+index).val('');
            $('#'+addressType+'_country_'+index).val('');
            $('#'+addressType+'_zipcode_'+index).val('');
            $('#'+addressType+'_unit_'+index).val('');
        }
        else
        {    
            
            $('#'+addressType+'_'+type+'_address_1_'+index).val('');
            $('#'+addressType+'_'+type+'_city_'+index).val('');
            // For county
            $('#'+addressType+'_'+type+'_county_'+index).val('');
            // End
            $('#'+addressType+'_'+type+'_state_'+index).val('');
            $('#'+addressType+'_'+type+'_country_'+index).val('');
            $('#'+addressType+'_'+type+'_unit_'+index).val('');
            $('#'+addressType+'_'+type+'_address_2_'+index).val('');
            $('#'+addressType+'_'+type+'_zipcode_'+index).val('');
        }
    }
    //for child lead address
    function resetServiceBillingFieldsChild(addressType,type,index,enrollment)
    {
        if(type == '')
        {
            $('#'+addressType+'_line_1_'+index+'_'+enrollment).val('');
            $('#'+addressType+'_line_2_'+index+'_'+enrollment).val('');
            $('#'+addressType+'_city_'+index+'_'+enrollment).val('');
            // For county
            $('#'+addressType+'_county_'+index+'_'+enrollment).val('');
            // End

            $('#'+addressType+'_state_'+index+'_'+enrollment).val('');
            $('#'+addressType+'_country_'+index+'_'+enrollment).val('');
            $('#'+addressType+'_zipcode_'+index+'_'+enrollment).val('');
            $('#'+addressType+'_unit_'+index+'_'+enrollment).val('');
        }
        else
        {    
            
            $('#'+addressType+'_'+type+'_address_1_'+index+'_'+enrollment).val('');
            $('#'+addressType+'_'+type+'_city_'+index+'_'+enrollment).val('');
            // For county
            $('#'+addressType+'_'+type+'_county_'+index+'_'+enrollment).val('');
            // End
            $('#'+addressType+'_'+type+'_state_'+index+'_'+enrollment).val('');
            $('#'+addressType+'_'+type+'_country_'+index+'_'+enrollment).val('');
            $('#'+addressType+'_'+type+'_unit_'+index+'_'+enrollment).val('');
            $('#'+addressType+'_'+type+'_address_2_'+index+'_'+enrollment).val('');
            $('#'+addressType+'_'+type+'_zipcode_'+index+'_'+enrollment).val('');
        }
    }

    // for show ecogold option field on depend program code
    function setEcogoldProgramOption(code, element) {
        var springGaurd = 'spring guard';
        var ecogoldClass = '.ecogold-program';
        var brand = $("#brand_name").val();
        var selectedElement = $(element).closest(".multi-lead-section");
        selectedElement.find(ecogoldClass+" option").prop('disabled',true);
        if (code == ECOGOLD_PROGRAM_CODE) {
            selectedElement.find(ecogoldClass+" option:icontains("+springGaurd+")").prop('disabled',false);
            selectedElement.find(ecogoldClass).val(springGaurd).trigger('change.select2');
        } else if($.inArray(code, ECOGOLD_CODE_WITHOUT_SG) !== -1) {
            let cashBack = '3% cash back';
            let rewards = '5% ecogold rewards';
            selectedElement.find(ecogoldClass+" option:icontains("+cashBack+")").prop('disabled',false);
            selectedElement.find(ecogoldClass+" option:icontains("+rewards+")").prop('disabled',false);
            selectedElement.find(ecogoldClass).trigger('change.select2');
        } else if (code == ECOGOLD_PROGRAM_CODE_1_KIWI_ENERGY && brand.toLowerCase() == KIWI_BRAND_NAME) {
            let cashBack = '3% cash back';
            let rewards = '5% rewards';
            selectedElement.find(ecogoldClass+" option:icontains("+cashBack+")").prop('disabled',false);
            selectedElement.find(ecogoldClass+" option:icontains("+rewards+")").prop('disabled',false);
            selectedElement.find(ecogoldClass).trigger('change.select2');
        } else if(code == ECOGOLD_PROGRAM_CODE_2_KIWI_ENERGY && brand.toLowerCase() == KIWI_BRAND_NAME) {
            let kiwiGuard = 'kiwi guard';
            selectedElement.find(ecogoldClass+" option:icontains("+kiwiGuard+")").prop('disabled',false);
            //selectedElement.find(ecogoldClass).val(kiwiGuard).trigger('change.select2');
        } else if(code == ECOGOLD_PROGRAM_CODE_3_KIWI_ENERGY && brand.toLowerCase() == KIWI_BRAND_NAME) {
            let ecoGoldBase = 'ecogold base';
            selectedElement.find(ecogoldClass+" option:icontains("+ecoGoldBase+")").prop('disabled',false);
            //selectedElement.find(ecogoldClass).val(ecoGoldBase).trigger('change.select2');
        } else {
            selectedElement.find(ecogoldClass+" option").prop('disabled',false);
        }
        @empty(request('lid'))
        selectedElement.find(ecogoldClass).not(":disabled").prop("selectedIndex", 0).trigger('change.select2');
        @endempty
        selectedElement.find(ecogoldClass).select2();
    }


    // for show promo code option field on depend program code
    function setPromoCodeFieldOption(code, element) {
        console.log(code);
        var gift = '$25 gift card 3mo';
        var energy = '$200 energy efficiency';
        var NA = 'not applicable';
        var promoClass = '.promo-code-field';
        var kiwi_gift = '$25 Gift Card';
        var energy_eff = '$500 energy efficiency';
        var brand = $("#brand_name").val();
        var selectedElement = $(element).closest(".multi-lead-section");
        
        selectedElement.find(promoClass+" option").prop('disabled',true);
        if ($.inArray(code, PROMO_CODE_GIFT) !== -1) {
            selectedElement.find(promoClass+" option:icontains("+gift+")").prop('disabled',false);
            selectedElement.find(promoClass).val(gift).trigger('change.select2');
        } else if ($.inArray(code, PROMO_CODE_ALL) !== -1) {
            selectedElement.find(promoClass+" option:icontains("+gift+")").prop('disabled',false);
            selectedElement.find(promoClass+" option:icontains("+energy+")").prop('disabled',false);
            selectedElement.find(promoClass).trigger('change.select2');
        } else if ((code == ECOGOLD_PROGRAM_CODE_1_KIWI_ENERGY || code == ECOGOLD_PROGRAM_CODE_3_KIWI_ENERGY) && brand.toLowerCase() == KIWI_BRAND_NAME) {
            //$(promoClass+" option:eq("+kiwi_gift+")").prop('disabled',false);
            selectedElement.find(promoClass).find('option[value="'+kiwi_gift+'"]').prop('disabled',false);
            selectedElement.find(promoClass+" option:icontains("+energy+")").prop('disabled',false);
            selectedElement.find(promoClass+" option:icontains("+energy_eff+")").prop('disabled',false);
            selectedElement.find(promoClass).trigger('change.select2');
        } else if (code == ECOGOLD_PROGRAM_CODE_2_KIWI_ENERGY && brand.toLowerCase() == KIWI_BRAND_NAME) {
            selectedElement.find(promoClass+" option:icontains("+NA+")").prop('disabled',false);
            selectedElement.find(promoClass).trigger('change.select2');
        } else {
            selectedElement.find(promoClass+" option").prop('disabled',false);
        }
        
        if(brand.toLowerCase() != KIWI_BRAND_NAME){
            selectedElement.find(promoClass+" option:icontains("+NA+")").prop('disabled',false);
        }
        @empty(request('lid'))
        selectedElement.find(promoClass).not(":disabled").prop("selectedIndex", 0).trigger('change.select2');
        @endempty
        selectedElement.find(promoClass).select2();
        
    }
    function deleteEnrollmentForm(id){
        var x = 2;
        $(".multienrollment-form"+id).html('');
        $(".htmlTextForEnrollmentNO").each(function(){
            $(this).html(x);
            x = x+1;
        })
        // $("#current_enrollment_number").val(current_enrollment_number-1);
        
    }
</script>

<script>
$('body').on('click', function (e) {
    $('[data-toggle="popover"]').each(function () {
        //the 'is' for buttons that trigger popups
        //the 'has' for icons within a button that triggers a popup
        if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
            $(this).popover('hide');
        }
    });
});

</script>

@endsection
