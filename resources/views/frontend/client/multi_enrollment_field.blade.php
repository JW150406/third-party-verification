@php 
    $input = request()->all();
@endphp 

<div class="multienrollment-form{{$multienrollmentIncrement}}">
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

<div style="height:4px;margin-top:10px;width:100%;background:black;border: 1px solid silver;">

</div>
<div class="input-group mb10">
    <div>
        <h2>Enrollment <span class="htmlTextForEnrollmentNO"> {{ $current_enrollment_number }} </span> <span class="close-script" id="multienrollmentdeletebutton-{{$multienrollmentIncrement}}" onclick="deleteEnrollmentForm({{$multienrollmentIncrement}})" style="background-color: #f05a5a;position: relative;float:right;border-radius: 50px;height: 13px;width: 13px;cursor: pointer;"><i class="fa fa-close"></i></span></h2>
    </div>
    <!-- <input type="text" autocomplete="off" {{--onfocus="this.setAttribute('autocomplete', 'new-password')"--}} class="form-control zipcodefield typeahead" name="zipcode" id="zipcode" placeholder="Please enter zipcode" value="{{ old('zipcode') ? old('zipcode') : $zipcode }}" data-parsley-trigger="focusout" data-parsley-pattern="[0-9]{5}" data-parsley-pattern-message="Please enter 5 digit zipcode" data-parsley-required='true' > -->
    <div class="multi-lead-section">
        @if(isset($commodities) && !empty($commodities))
        <input type="hidden" name="" id="firstComId" value="{{$commodities[0]->id}}">
            @foreach($commodities as $commodity)
                <div class="form-group">
                    <label class="control-label"
                        for="utilityoptions_{{ $commodity->id }}_{{ $multienrollmentIncrement }}">Select {{ $commodity->name }}
                        Utility </label>
                    <select class="select2 form-control validate required utilityoptions"
                            id="utilityoptions_{{ $commodity->id }}_{{ $multienrollmentIncrement }}"
                            name="utility[{{ $multienrollmentIncrement }}][{{$commodity->id}}]"
                            title="Please select utility"
                            data-parsley-trigger="focusout"
                            data-parsley-required='true'
                            data-parsley-errors-container="#select2-utilityform-error-message-{{ $multienrollmentIncrement }}-{{$commodity->id}}"
                            >
                        <option value="">Select</option>
                        @if(count($commodity->utilities) > 0)
                            @foreach($commodity->utilities as $utility )
                                <option value="{{$utility->utid}}" data-mapped-util= "{{$utility->mapped_utility}}" {{ isset($clonedData[$commodity->name]) && !empty($clonedData[$commodity->name]) ? $utility->utid == $clonedData[$commodity->name]['utility_selected_id'] ? 'selected' : '' : '' }}>
                                {{$utility->fullname}}
                            @endforeach
                        @endif
                    </select>
                    <span id="select2-utilityform-error-message-{{ $multienrollmentIncrement }}-{{$commodity->id}}"></span>
                </div>

                <!--new--design---->
                <div class="form-group lead-select-parent">
                    <label class="control-label" for="programOption_{{ $commodity->id }}">Select {{ $commodity->name }}
                        Program </label>
                    
                    <div class="form-control lead-select lead-select-custom">
                        <span class="lead-select-arrow"></span>
                        <div class="sel-value"></div>
                        <div class="set-program" style="display: none">@if(isset($clonedData[$commodity->name]['programs']) && !empty($clonedData[$commodity->name]['programs']))
                                <input type="hidden" name="program[{{ $multienrollmentIncrement }}][]" value="{{ $clonedData[$commodity->name]['program_selected_id']}}">
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
                    function setProgramsChildInDropdown(programId,commodityId){
                        console.log("In child set program function");
                        let res = $(programId).closest("ul").data("programs");
                        if(res !=undefined){
                            var html = '<li>Select</li>';
                            var programs = res.data;

                            $(programId).closest(".lead-select").find(".sel-value").html('');
                            $(programId).closest(".lead-select").find(".set-program").html('');
                            let comId = $('#firstComId').val();
                            let parentProgramCode = $('#programOption_'+comId+'_{{ $multienrollmentIncrement }}').closest(".lead-select").find('.sel-value').find(".program-code-text").html();
                            console.log('parent program Code '+parentProgramCode);
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
                                //for rrh programs filtering for dual fual
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
                            console.log(programId);
                            $(programId).html(html);
                            @if(count($commodities) > 1)
                            $(".AC-No-{{ $multienrollmentIncrement }}-"+res.commodity).attr('data-parsley-pattern',res.regex);
                            $(".AC-No-{{ $multienrollmentIncrement }}-"+res.commodity).attr('data-parsley-pattern-message',res.regex_message);
                            // For Text customization of account number placeholder
                            if(res.act_num_verbiage != ''){
                                $(".AC-No-{{ $multienrollmentIncrement }}-"+res.commodity).attr('placeholder',res.act_num_verbiage);
                            }
                            @else 
                            $(".AC-No-{{ $multienrollmentIncrement }}").attr('data-parsley-pattern',res.regex);
                            $(".AC-No-{{ $multienrollmentIncrement }}").attr('data-parsley-pattern-message',res.regex_message);
                            // For Text customization of account number placeholder
                            if(res.act_num_verbiage != ''){
                                $(".AC-No-{{ $multienrollmentIncrement }}").attr('placeholder',res.act_num_verbiage);
                            }
                            @endif
                            
                            $.each(res.utility_validations, function( key, value ) {
                                console.log(value);
                                $(".form-field-"+value.field_id+"-{{ $multienrollmentIncrement }}").attr('data-parsley-pattern',value.regex);
                                $(".form-field-"+value.field_id+"-{{ $multienrollmentIncrement }}").attr('data-parsley-pattern-message',value.regex_message);
                            });
                        }
                    }
                    $(document).ready(function () {
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
                                    // async:false,
                                    type: "POST",
                                    data: {
                                        'utility_id': com_id ,form_id : '{{$form->id}}'
                                    },
                                    success: function (res) {

                                        if (res.status === 'success') {
                                            $('#programOption_{{ $commodity->id }}_{{ $multienrollmentIncrement }}').closest("ul").data("programs",res);
                                            setProgramsChildInDropdown('#programOption_{{ $commodity->id }}_{{ $multienrollmentIncrement }}','{{ $commodity->id }}');
                                        
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

                                // console.log('mapped_utilities', mapped_utilities);
                                mapped_utilities = JSON.parse(mapped_utilities);
                            }
                            if(Array.isArray(mapped_utilities)){
                                // console.log('mapped_utilities', mapped_utilities);
                                html = '<option value="">Select</option>';
                                if (mapped_utilities.length){
                                    let commodity_utility = [];
                                    let commodity_list = [];
                                    var i = 0;
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
                                        if ( typeof commodity_utility[commodity_id][1] == 'undefined') {
                                         commodity_utility[commodity_id][1] = '<option value="">Select</option>';
                                        }
                                        commodity_utility[commodity_id][1]  +='<option value="'+utility.utid+'" ';/*data-mapped-util= "'+utility.mapped_utility+'" '; */                                          
                                        commodity_utility[commodity_id][1]  +='>';
                                        commodity_utility[commodity_id][1]  += utility.fullname;
                                        commodity_utility[commodity_id][1]  +='</option>';
                                    });
                                        // console.log('commodity_utility ',commodity_utility);
                                        // console.log('commodity_list ',commodity_list);
                                    $.each(commodity_list, function( key, c_id ) {
                                        // console.log('in commodity_utility ',commodity_utility[c_id], c_id);
                                        $(commodity_utility[c_id][0]).html(commodity_utility[c_id][1]);

                                    });
                                }

                            }
                           console.log ('inn change', util_id, mapped_utilities);
                            //update next utility for within same group if parent is selected end
                        });
                    })
                    function getUtilityValidationsChild(){
                        var com_id = $("#utilityoptions_{{ $commodity->id }}_{{ $multienrollmentIncrement }}").val();
                        if (com_id !== '') {
                                $.ajax({
                                    url: "{{ route('ajax.getprograms') }}",
                                    async:false,
                                    type: "POST",
                                    data: {
                                        'utility_id': com_id ,form_id : '{{$form->id}}'
                                    },
                                    success: function (res) {

                                        if (res.status === 'success') {
                                            var html = '';                                       
                                            @if(count($commodities) > 1)
                                            $(".AC-No-{{ $multienrollmentIncrement }}-"+res.commodity).attr('data-parsley-pattern',res.regex);
                                            $(".AC-No-{{ $multienrollmentIncrement }}-"+res.commodity).attr('data-parsley-pattern-message',res.regex_message);
                                            // For Text customization of account number placeholder
                                            if(res.act_num_verbiage != ''){
                                                $(".AC-No-{{ $multienrollmentIncrement }}-"+res.commodity).attr('placeholder',res.act_num_verbiage);
                                            }
                                            @else 
                                            $(".AC-No-{{ $multienrollmentIncrement }}").attr('data-parsley-pattern',res.regex);
                                            $(".AC-No-{{ $multienrollmentIncrement }}").attr('data-parsley-pattern-message',res.regex_message);
                                            // For Text customization of account number placeholder
                                            if(res.act_num_verbiage != ''){
                                                $(".AC-No-{{ $multienrollmentIncrement }}").attr('placeholder',res.act_num_verbiage);
                                            }
                                            @endif
                                            
                                            $.each(res.utility_validations, function( key, value ) {
                                                $(".form-field-"+value.field_id+"-{{ $multienrollmentIncrement }}").attr('data-parsley-pattern',value.regex);
                                                $(".form-field-"+value.field_id+"-{{ $multienrollmentIncrement }}").attr('data-parsley-pattern-message',value.regex_message);
                                            });
                                            // $('.lead-select').addClass("open");
                                            $('#programOption_{{ $commodity->id }}_{{ $multienrollmentIncrement }}').closest(".lead-select").addClass("open");

                                        } else {
                                            // var html = '<li>Select</li>';
                                            // $('#programOption_{{ $commodity->id }}_{{ $multienrollmentIncrement }}').html(html);
                                            alert(res.message)
                                        }
                                    }
                                    
                                })
                            } else {
                                var html = '<li>Select</li>';
                                $('#programOption_{{ $commodity->id }}_{{ $multienrollmentIncrement }}').html(html);
                            }
                    }
                </script>

            @endforeach
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
                        <label class="control-label {{$field->type}}-label" copy-from="{{$field->type.$i.$multienrollmentIncrement}}">{{ ucfirst(array_get($field, 'label')) }}</label>
                        
                        @if($field->is_allow_copy == 1)
                            @include('frontend.user.copy_from',['label'=> $field->type.'-label' , 'copyTo' => $field->type.$i.$multienrollmentIncrement])
                        @endif
                        <div class="row {{$field->type.$i.$multienrollmentIncrement}}">
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

                        <label class="control-label {{$field->type}}-label" copy-from="{{$field->type.$i.$multienrollmentIncrement}}">{{ $label }}</label>
                        
                        @if($field->is_allow_copy == 1)
                            @include('frontend.user.copy_from',['label'=> $field->type.'-label' , 'copyTo' => $field->type.$i.$multienrollmentIncrement])
                        @endif
                        <div class="{{$field->type.$i.$multienrollmentIncrement}}">
                        
                            <input type="text"
                                autocomplete="off"
                                class="form-control form-field-{{$field->id}}-{{$multienrollmentIncrement}} @if($accountLabelMatch) AC-No-{{$multienrollmentIncrement}} @else form-fields @endif @if(isset($labels[1]) && !empty($labels[1])) {{ 'AC-No-'.$multienrollmentIncrement .'-'.trim($labels[1]) }} @endif"
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
                        <label class="control-label {{$field->type}}-label" copy-from="{{$field->type.$i.$multienrollmentIncrement}}">{{ ucfirst(array_get($field, 'label')) }}</label>
                        @if($field->is_allow_copy == 1)
                            @include('frontend.user.copy_from',['label'=> $field->type.'-label' , 'copyTo' => $field->type.$i.$multienrollmentIncrement])
                        @endif
                        
                        <div class="{{$field->type.$i.$multienrollmentIncrement}}">
                            <?php $textAreaValue = '';?>
                            @if(isset($input['fields'][$i]['value']['value']))
                            <?php $textAreaValue = $input['fields'][$i]['value']['value'];?>
                            @else 
                                @if(isset($clonedData[$field->id]['value']) && !empty($clonedData[$field->id]['value']) )                                                                                    
                                    <?php $textAreaValue = $clonedData[$field->id]['value'];?>
                                @endif
                            @endif

                            <textarea
                                class="form-control form-fields form-field-{{$field->id}}-{{$multienrollmentIncrement}}"
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
                            <label class="control-label {{$field->type}}-label" copy-from="{{$field->type.$i.$multienrollmentIncrement}}">{{ ucfirst(array_get($field, 'label')) }}</label>
                            @if($field->is_allow_copy == 1)
                                @include('frontend.user.copy_from',['label'=> $field->type.'-label' , 'copyTo' => $field->type.$i.$multienrollmentIncrement])
                            @endif
                            <div class="row {{$field->type.$i.$multienrollmentIncrement}}">
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
                                    id="address_latitude_{{$i}}_{{$multienrollmentIncrement}}"
                                    value="{{ isset($clonedData[$field->id]['lat']) && !empty($clonedData[$field->id]['lat']) ? $clonedData[$field->id]['lat'] : '' }}">
                                    <input type="hidden"
                                    name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][lng]"
                                    id="address_longitude_{{$i}}_{{$multienrollmentIncrement}}"
                                    value="{{ isset($clonedData[$field->id]['lng']) && !empty($clonedData[$field->id]['lng']) ? $clonedData[$field->id]['lng'] : '' }}">
                                    <div class="col-sm-12 address-field">
                                        <span class="zipcode-error address-zipcode-error-{{$i}}-{{$multienrollmentIncrement}}" style="color:red;"></span>
                                    </div>
                                </div>
                                <script>
                                    var input = document.getElementById('address_line_1_{{$i}}_{{$multienrollmentIncrement}}');
                                    var addressPostalCode = '';
                                    var addressState = '';
                                    var autocomplete{{$i}}{{$multienrollmentIncrement}} = new google.maps.places.Autocomplete(input, {
                                        types: [],
                                        componentRestrictions: {country: "us"}
                                    });
                                    google.maps.event.addListener(autocomplete{{$i}}{{$multienrollmentIncrement}}, 'place_changed', function () {
                                        // alert("here in 1");
                                        var place = autocomplete{{$i}}{{$multienrollmentIncrement}}.getPlace();
                                        $('.address-zipcode-error-{{$i}}-{{$multienrollmentIncrement}}').text("");
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
                                                    $('.address-zipcode-error-{{$i}}-{{$multienrollmentIncrement}}').text("");
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
                                                    resetServiceBillingFieldsChild('address','','{{$i}}','{{$multienrollmentIncrement}}');
                                                    printStateErrorMessage('address-zipcode-error-{{$i}}-{{$multienrollmentIncrement}}');
                                                    $("#zipcode").val(addressPostalCode);
                                                    console.log('address zipcode :'+addressPostalCode);
                                                }
                                            @else
                                                if(addressPostalCode != $('#zipcode').attr('value'))
                                                {
                                                    resetServiceBillingFieldsChild('address','','{{$i}}','{{$multienrollmentIncrement}}');
                                                    printZipcodeErrorMessage('address-zipcode-error-{{$i}}-{{$multienrollmentIncrement}}');
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
                                <label class="{{$address}}-label" copy-from="{{$address.$i.$multienrollmentIncrement}}">Service
                                    Address</label>

                                @if($field->is_allow_copy == 1)
                                    @include('frontend.user.copy_from',['label'=> $address.'-label' , 'copyTo' => $address.$i.$multienrollmentIncrement])
                                @endif
                            </div>
                            <div class="row">
                                <div class="address-block {{$address.$i.$multienrollmentIncrement}}">
                                <div class="col-sm-12 address-field">
                                    <input class="form-control autocompletestreet address"
                                            type="text" 
                                        Placeholder="Address Line 1"
                                        onfocus="this.setAttribute('autocomplete', 'new-password')"
                                        autocapitalize="none"
                                        spellcheck="false"
                                        id="service_and_billing_address_service_address_1_{{$i}}_{{$multienrollmentIncrement}}"
                                        name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][service_address_1]" 

                                            @if(isset($input['fields'][$i]['value']['service_address_1']))
                                                value="{{ $input['fields'][$i]['value']['service_address_1']}}" 
                                            @else
                                                value="{{ isset($clonedData[$field->id]['service_address_1']) && !empty($clonedData[$field->id]['service_address_1']) ? $clonedData[$field->id]['service_address_1'] : '' }}"
                                            @endif
                                        @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                        data-parsley-trigger="focusout"

                                        @endif
                                        onkeyup="changeVal('service_and_billing_address_service_address_1_', 'service_and_billing_address_billing_address_1_', '{{$i}}_{{$multienrollmentIncrement}}');"
                                        {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                        >
                                </div>
                                <div class="col-sm-12 address-field">
                                    <input autocomplete="new-password"
                                            type="text" 
                                        class="form-control autocompletestreet address"
                                        Placeholder="Apartment, suite, unit, building, floor, etc. (optional)"
                                        id="service_and_billing_address_service_address_2_{{$i}}_{{$multienrollmentIncrement}}"
                                        name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][service_address_2]" 
                                        @if(isset($input['fields'][$i]['value']['service_address_2']))
                                                value="{{ $input['fields'][$i]['value']['service_address_2']}}" 
                                            @else
                                                value="{{ isset($clonedData[$field->id]['service_address_2']) && !empty($clonedData[$field->id]['service_address_2']) ? $clonedData[$field->id]['service_address_2'] : '' }}"
                                            @endif
                                        onkeyup="changeVal('service_and_billing_address_service_address_2_', 'service_and_billing_address_billing_address_2_', '{{$i}}_{{$multienrollmentIncrement}}');"
                                        {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                    >
                                </div>
                                <!-- Code changes by : Remove unit number div -->                                
                                <div class="col-sm-3 address-field inline-block">
                                    <input autocomplete="new-password" 
                                            type="text" 
                                        class="form-control"
                                        placeholder="City"
                                        id="service_and_billing_address_service_city_{{$i}}_{{$multienrollmentIncrement}}"
                                        name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][service_city]"
                                            @if(isset($input['fields'][$i]['value']['service_city']))
                                                value="{{ $input['fields'][$i]['value']['service_city']}}" 
                                            @else
                                                value="{{ isset($clonedData[$field->id]['service_city']) && !empty($clonedData[$field->id]['service_city']) ? $clonedData[$field->id]['service_city'] : '' }}"
                                            @endif
                                        onkeyup="changeVal('service_and_billing_address_service_city_', 'service_and_billing_address_billing_city_', '{{$i}}_{{$multienrollmentIncrement}}');"
                                        {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                    >
                                </div>
                                <!-- Start : Div for service_county -->
                                <div class="col-sm-3 address-field inline-block">
                                    <input autocomplete="new-password" 
                                            type="text" 
                                        class="form-control"
                                        placeholder="County"
                                        id="service_and_billing_address_service_county_{{$i}}_{{$multienrollmentIncrement}}"
                                        name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][service_county]"
                                            @if(isset($input['fields'][$i]['value']['service_county']))
                                                value="{{ $input['fields'][$i]['value']['service_county']}}" 
                                            @else
                                                value="{{ isset($clonedData[$field->id]['service_county']) && !empty($clonedData[$field->id]['service_county']) ? $clonedData[$field->id]['service_county'] : '' }}"
                                            @endif
                                        onkeyup="changeVal('service_and_billing_address_service_county_', 'service_and_billing_address_billing_county_', '{{$i}}_{{$multienrollmentIncrement}}');"
                                        {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!} 
                                        >
                                </div>
                                <!-- End  -->
                                <div class="col-sm-3 address-field inline-block">
                                    <input id="service_and_billing_address_service_state_{{$i}}_{{$multienrollmentIncrement}}"
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
                                        onkeyup="changeVal('service_and_billing_address_service_state_', 'service_and_billing_address_billing_state_', '{{$i}}_{{$multienrollmentIncrement}}');"
                                            short-name="@if(isset($input['fields'][$i]['value']['short_service_state'])) {{ $input['fields'][$i]['value']['short_service_state']}} @endif"
                                            {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                            >
                                </div>
                                <div class="col-sm-3 address-field inline-block">
                                    <input class="form-control @if(array_get($field, 'is_primary')) zipcode-field @endif" 
                                            type="text" 
                                        id="service_and_billing_address_service_zipcode_{{$i}}_{{$multienrollmentIncrement}}"
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
                                        onkeyup="changeVal('service_and_billing_address_service_zipcode_', 'service_and_billing_address_billing_zipcode_', '{{$i}}_{{$multienrollmentIncrement}}');">
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
                                    <input type="hidden" name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][short_service_state]" id="service_and_billing_address_service_state_short_{{$i}}_{{$multienrollmentIncrement}}">
                                    <input type="hidden"
                                    name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][service_lat]"
                                    id="service_and_billing_address_service_latitude_{{$i}}_{{$multienrollmentIncrement}}"
                                    value="{{ isset($clonedData[$field->id]['service_lat']) && !empty($clonedData[$field->id]['service_lat']) ? $clonedData[$field->id]['service_lat'] : '' }}">
                                    <input type="hidden"
                                    name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][service_lng]"
                                    id="service_and_billing_address_service_longitude_{{$i}}_{{$multienrollmentIncrement}}"
                                    value="{{ isset($clonedData[$field->id]['service_lng']) && !empty($clonedData[$field->id]['service_lng']) ? $clonedData[$field->id]['service_lng'] : '' }}">
                                    <div class="col-sm-12">
                                        <span class="zipcode-error  service-address-zipcode-error-{{$i}}-{{$multienrollmentIncrement}}" style="color:red;"></span>
                                    </div>
                                </div>
                                <script type="text/javascript">
                                    var input = document.getElementById('service_and_billing_address_service_address_1_{{$i}}_{{$multienrollmentIncrement}}');
                                    var servicePostalCode = "";
                                    var serviceState = "";
                                    var autocompleteService{{$i}}{{$multienrollmentIncrement}} = new google.maps.places.Autocomplete(input, {
                                        types: [],
                                        // bounds: zipBounds,
                                        // strictBounds:true,
                                        componentRestrictions: {country: "us"}
                                    });
                                    // autocompleteService{{$i}}.setBounds(zipBounds);
                                    google.maps.event.addListener(autocompleteService{{$i}}{{$multienrollmentIncrement}}, 'place_changed', function () {
                                        // alert("here in 2");
                                        $('.service-address-zipcode-error-{{$i}}-{{$multienrollmentIncrement}}').text("");
                                        var place = autocompleteService{{$i}}{{$multienrollmentIncrement}}.getPlace();
                                        $('#service_and_billing_address_service_latitude_{{$i}}_{{$multienrollmentIncrement}}').val(place.geometry.location.lat());
                                        $('#service_and_billing_address_service_longitude_{{$i}}_{{$multienrollmentIncrement}}').val(place.geometry.location.lng());
                                        @if(array_get($field, 'is_auto_caps') == 1)
                                            $('#service_and_billing_address_service_address_1_{{$i}}_{{$multienrollmentIncrement}}').val(place.name.toUpperCase());
                                        @else
                                            $('#service_and_billing_address_service_address_1_{{$i}}_{{$multienrollmentIncrement}}').val(place.name);
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
                                                $('.service-address-zipcode-error-{{$i}}-{{$multienrollmentIncrement}}').text("");
                                                $('#service_and_billing_address_service_zipcode_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name);
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
                                                    $('#service_and_billing_address_service_city_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name.toUpperCase());
                                                @else
                                                    $('#service_and_billing_address_service_city_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name);
                                                @endif  
                                            }
                                            // code for service_county
                                            if (addressType === "administrative_area_level_2") {
                                            @if(array_get($field, 'is_auto_caps') == 1)
                                                    $('#service_and_billing_address_service_county_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name.toUpperCase());
                                                @else
                                                    $('#service_and_billing_address_service_county_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name);
                                                @endif  
                                                
                                            }
                                            // End
                                            if (addressType === "administrative_area_level_1") {
                                                @if(array_get($field, 'is_auto_caps') == 1)
                                                    $('#service_and_billing_address_service_state_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name.toUpperCase());
                                                    serviceState = place.address_components[i].short_name.toUpperCase();
                                                    $('#service_and_billing_address_service_state_{{$i}}_{{$multienrollmentIncrement}}').attr('short-name',serviceState.toUpperCase())
                                                    $('#service_and_billing_address_service_state_short_{{$i}}_{{$multienrollmentIncrement}}').val(serviceState.toUpperCase());
                                                @else
                                                    $('#service_and_billing_address_service_state_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name);
                                                    serviceState = place.address_components[i].short_name;
                                                    $('#service_and_billing_address_service_state_{{$i}}_{{$multienrollmentIncrement}}').attr('short-name',serviceState)
                                                    $('#service_and_billing_address_service_state_short_{{$i}}_{{$multienrollmentIncrement}}').val(serviceState);
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
                                            $('#service_and_billing_address_service_city_{{$i}}_{{$multienrollmentIncrement}}').val(city.toUpperCase());
                                        @else
                                            $('#service_and_billing_address_service_city_{{$i}}_{{$multienrollmentIncrement}}').val(city);
                                        @endif
                                        if ($('input[name="is_service_address_same_as_billing_address_{{$i}}_{{$multienrollmentIncrement}}"]:checked').val() == "yes") {
                                            console.log("ues");
                                            copy_address_child_("{{$i}}", 'yes',"{{$multienrollmentIncrement}}")
                                        }
                                        if('{{array_get($field, 'is_primary') == 1}}'){

                                            @if(isset($input['state']) && !empty($input['state']))
                                                if(serviceState != $('#state').val())
                                                {
                                                    resetServiceBillingFieldsChild('service_and_billing_address','service','{{$i}}','{{$multienrollmentIncrement}}');
                                                    printStateErrorMessage('service-address-zipcode-error-{{$i}}-{{$multienrollmentIncrement}}');
                                                }
                                            @else
                                                if(servicePostalCode != $('#zipcode').attr('value'))
                                                {
                                                    resetServiceBillingFieldsChild('service_and_billing_address','service','{{$i}}','{{$multienrollmentIncrement}}');
                                                    printZipcodeErrorMessage('service-address-zipcode-error-{{$i}}-{{$multienrollmentIncrement}}');
                                                }
                                            @endif
                                        }
                                    })
                                </script>
                                <div class="col-sm-12">
                                    <!-- <span class="service-address-zipcode-error-{{$i}}" style="color:red;"></span><br/> -->
                                    <span class="bill-address-title">Is the billing address same as service address?</span>
                                    &nbsp;

                                    <div class="form-group radio-btns pdt0">
                                        <label class="radio-inline">
                                            <input type="radio"
                                                name="is_service_address_same_as_billing_address_{{$i}}_{{$multienrollmentIncrement}}"
                                                onclick='copy_address_child_("{{$i}}", "yes","{{$multienrollmentIncrement}}")'
                                                value="yes">
                                            Yes
                                        </label>
                                        <?php 
                                            $checkIsAutoCaps = (array_get($field, 'is_auto_caps') == 1 ? 1 : 0);
                                        ?>
                                        <label class="radio-inline">
                                            <input type="radio"
                                                name="is_service_address_same_as_billing_address_{{$i}}_{{$multienrollmentIncrement}}"
                                                onclick='copy_address_child_("{{$i}}", "no", "{{$multienrollmentIncrement}}","{{$checkIsAutoCaps}}")'
                                                value="no">
                                            No
                                        </label>
                                    </div>

                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="{{$address}}-label" copy-from="{{$address.$i.$multienrollmentIncrement}}-billing">Billing Address</label>
                                        @if($field->is_allow_copy == 1)
                                            @include('frontend.user.copy_from',['label'=> $address.'-label' , 'copyTo' => $address.$i.$multienrollmentIncrement.'-billing'])
                                        @endif
                                    </div>
                                </div>
                                <div class="address-block {{$address.$i.$multienrollmentIncrement}}-billing">
                                <div class="col-sm-12 address-field">
                                    <input autocomplete="off" 
                                            type="text" 
                                        class="form-control autocompletestreet address {{ array_get($field, 'is_required' )==1 ? 'required' : '' }}"
                                        Placeholder="Address Line 1"
                                        onfocus="this.setAttribute('autocomplete', 'new-password')"
                                        id="service_and_billing_address_billing_address_1_{{$i}}_{{$multienrollmentIncrement}}"
                                        name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][billing_address_1]"
                                            @if(isset($input['fields'][$i]['value']['billing_address_1']))
                                                value="{{ $input['fields'][$i]['value']['billing_address_1']}}" 
                                            @else
                                                value="{{ isset($clonedData[$field->id]['billing_address_1']) && !empty($clonedData[$field->id]['billing_address_1']) ? $clonedData[$field->id]['billing_address_1'] : '' }}"
                                            @endif
                                        data-parsley-trigger="focusout"
                                        data-parsley-errors-container="#error_service_and_billing_address_billing_address_1_{{$i}}_{{$multienrollmentIncrement}}"
                                        @if(array_get($field, 'is_required') == 1) data-parsley-required='true'
                                            @endif
                                            {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                            >
                                        <div id="error_service_and_billing_address_billing_address_1_{{$i}}_{{$multienrollmentIncrement}}"></div>
                                </div>
                                <div class="col-sm-12 address-field">
                                    <input autocomplete="new-password" 
                                            type="text" 
                                        class="form-control autocompletestreet address"
                                        Placeholder="Apartment, suite, unit, building, floor, etc. (optional)"
                                        id="service_and_billing_address_billing_address_2_{{$i}}_{{$multienrollmentIncrement}}"
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
                                        id="service_and_billing_address_billing_city_{{$i}}_{{$multienrollmentIncrement}}"
                                        name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][billing_city]"
                                            @if(isset($input['fields'][$i]['value']['billing_city']))
                                                value="{{ $input['fields'][$i]['value']['billing_city']}}" 
                                            @else
                                                value="{{ isset($clonedData[$field->id]['billing_city']) && !empty($clonedData[$field->id]['billing_city']) ? $clonedData[$field->id]['billing_city'] : '' }}"
                                            @endif
                                            {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!}
                                            >
                                </div>
                                <div class="col-sm-3 address-field inline-block">
                                    <input autocomplete="new-password" 
                                            type="text" 
                                        class="form-control"
                                        placeholder="County"
                                        id="service_and_billing_address_billing_county_{{$i}}_{{$multienrollmentIncrement}}"
                                        name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][billing_county]"
                                            @if(isset($input['fields'][$i]['value']['billing_county']))
                                                value="{{ $input['fields'][$i]['value']['billing_county']}}" 
                                            @else
                                                value="{{ isset($clonedData[$field->id]['billing_county']) && !empty($clonedData[$field->id]['billing_county']) ? $clonedData[$field->id]['billing_county'] : '' }}"
                                            @endif
                                            {!! array_get($field, 'is_auto_caps') == 1 ? 'oninput="this.value = this.value.toUpperCase()"' : '' !!} 
                                            >
                                </div>
                                <div class="col-sm-3 address-field inline-block">
                                    <input id="service_and_billing_address_billing_state_{{$i}}_{{$multienrollmentIncrement}}"    type="text" 
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
                                        id="service_and_billing_address_billing_zipcode_{{$i}}_{{$multienrollmentIncrement}}"
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
                                    id="service_and_billing_address_billing_latitude_{{$i}}_{{$multienrollmentIncrement}}"
                                    value="{{ isset($clonedData[$field->id]) && !empty($clonedData[$field->id]) ? $clonedData[$field->id]['billing_lat'] : '' }}">
                                    <input type="hidden"
                                    name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][billing_lng]"
                                    id="service_and_billing_address_billing_longitude_{{$i}}_{{$multienrollmentIncrement}}"
                                    value="{{ isset($clonedData[$field->id]) && !empty($clonedData[$field->id]) ? $clonedData[$field->id]['billing_lng'] : '' }}">
                                </div>
                            </div>

                            <script type="text/javascript">
                                var input = document.getElementById('service_and_billing_address_billing_address_1_{{$i}}_{{$multienrollmentIncrement}}');
                                var autocompleteBilling{{$i}}{{$multienrollmentIncrement}} = new google.maps.places.Autocomplete(input, {
                                    types: [],
                                    componentRestrictions: {country: "us"}
                                });
                                google.maps.event.addListener(autocompleteBilling{{$i}}{{$multienrollmentIncrement}}, 'place_changed', function () {
                                    // alert("here in 4");
                                    var place = autocompleteBilling{{$i}}{{$multienrollmentIncrement}}.getPlace();
                                    console.log(place);
                                    $('#service_and_billing_address_billing_latitude_{{$i}}_{{$multienrollmentIncrement}}').val(place.geometry.location.lat());
                                    $('#service_and_billing_address_billing_longitude_{{$i}}_{{$multienrollmentIncrement}}').val(place.geometry.location.lng());
                                    @if(array_get($field, 'is_auto_caps') == 1)
                                        $('#service_and_billing_address_billing_address_1_{{$i}}_{{$multienrollmentIncrement}}').val(place.name.toUpperCase());
                                    @else
                                        $('#service_and_billing_address_billing_address_1_{{$i}}_{{$multienrollmentIncrement}}').val(place.name);
                                    @endif

                                    var address2 = '';
                                    for (var i = 0; i < place.address_components.length; i++) {
                                        var addressType = place.address_components[i].types[0];
                                        let index = place.address_components.length-1;
                                        let zipcodeValue = place.address_components[index].types[0];

                                        if (addressType === 'postal_code') {

                                            $('#service_and_billing_address_billing_zipcode_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name);                                                                                     
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
                                                $('#service_and_billing_address_billing_city_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name.toUpperCase());
                                            @else
                                                $('#service_and_billing_address_billing_city_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name);
                                            @endif
                                        }
                                        // Code billing_county
                                        if (addressType === "administrative_area_level_2") {
                                            @if(array_get($field, 'is_auto_caps') == 1)
                                            $('#service_and_billing_address_billing_county_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name.toUpperCase());
                                            @else
                                            $('#service_and_billing_address_billing_county_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name);
                                            @endif
                                            
                                        }
                                        // End
                                        if (addressType === "administrative_area_level_1") {
                                            @if(array_get($field, 'is_auto_caps') == 1)
                                                $('#service_and_billing_address_billing_state_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name.toUpperCase());
                                            @else
                                                $('#service_and_billing_address_billing_state_{{$i}}_{{$multienrollmentIncrement}}').val(place.address_components[i].long_name);
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
                                        $('#service_and_billing_address_billing_city_{{$i}}_{{$multienrollmentIncrement}}').val(city);
                                        $('#service_and_billing_address_billing_address_2_{{$i}}_{{$multienrollmentIncrement}}').val(address2);
                                    @else
                                        $('#service_and_billing_address_billing_city_{{$i}}_{{$multienrollmentIncrement}}').val(city);
                                        $('#service_and_billing_address_billing_address_2_{{$i}}_{{$multienrollmentIncrement}}').val(address2);
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
                        <label class="control-label {{$field->type}}-label" copy-from="{{$field->type.$i.$multienrollmentIncrement}}">{{ ucfirst(array_get($field, 'label')) }}</label>
                        @if($field->is_allow_copy == 1)
                            @include('frontend.user.copy_from',['label'=> $field->type.'-label' , 'copyTo' => $field->type.$i.$multienrollmentIncrement])
                        @endif
                        <div class="input-group w100 {{$field->type.$i.$multienrollmentIncrement}}"
                            @if(array_get($field, 'is_verify') != 1) style="width: 100%" @endif>
                            <input type="text"
                                autocomplete="new-password"
                                id-attr='{{$multienrollmentIncrement}}'
                                class="form-control mobile {{ array_get($field, 'is_verify' )==1 ? 'verifyPhoneChildKey' : '' }}"
                                id="verifyPhoneId-{{$multienrollmentIncrement}}"
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
                                                    id-attr = "{{$multienrollmentIncrement}}"
                                                    class="btn btn-default searchzipcode verifyPhonechild" disabled
                                                    id="verifyPhonechild-{{$multienrollmentIncrement}}">Verify</button>
                                        </span>
                                    @endif
                                <input type="hidden" name="fields[{{$multienrollmentIncrement}}][{{$i}}][value][is_primary]" value="{{$field->is_primary}}" />
                        </div>

                    @elseif ($field->type == 'email')

                        <label class="control-label {{$field->type}}-label" copy-from="{{$field->type.$i.$multienrollmentIncrement}}">{{ ucfirst(array_get($field, 'label')) }}</label>
                        @if($field->is_allow_copy == 1)
                            @include('frontend.user.copy_from',['label'=> $field->type.'-label' , 'copyTo' => $field->type.$i.$multienrollmentIncrement])
                        @endif
                        <div class="input-group w100 {{$field->type.$i.$multienrollmentIncrement}}" 
                            @if(array_get($field, 'is_verify') != 1) style="width: 100%" @endif>
                            <input type="email"
                                autocomplete="new-password"
                                id-attr='{{$multienrollmentIncrement}}'
                                class="form-control email {{ array_get($field, 'is_required' )==1 ? 'required' : '' }} {{ array_get($field, 'is_verify' )==1 ? 'verifyEmailChildKey' : '' }}"
                                id='verifyEmailId-{{$multienrollmentIncrement}}'
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
                                    id-attr="{{$multienrollmentIncrement}}"
                                            class="btn btn-default searchzipcode verifyEmailChild" disabled
                                            id="verifyEmailChildBtn-{{$multienrollmentIncrement}}">Verify</button>
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
                                    data-parsley-errors-container="#select2-clientform-error-message-{{$multienrollmentIncrement}}-{{$i}}"
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
                            <span id="select2-clientform-error-message-{{$multienrollmentIncrement}}-{{$i}}"></span>
                        </div>
                    @endif
                </div>
                <?php $i++; ?>
            @endforeach
            <div class="form-group mt60 ">
            
            </div>
        @endif
    </div>
</div>
    <script type="text/javascript">
        @if(!empty(request('lid')))
        $(function () {
            $( "input[name='program[{{ $multienrollmentIncrement }}][]']" ).each(function() {
                console.log($(this).val());
                $( "input[value='"+$(this).val()+"']" ).closest("li").trigger('click');
                getUtilityValidationsChild();
                $( "input[value='"+$(this).val()+"']" ).closest(".lead-select").trigger('click');
                $("input[value='"+$(this).val()+"']" ).closest(".lead-select").removeClass("open");
                $('body').trigger('click');
            });
        });
        @endif
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

           

            $('#zipcode').on('change', function (e) {
                $(this).parsley().validate();
            });

            

            $('.verifyEmailChildKey').keyup(function () {
                console.log('change child');
                let id = $(this).attr('id-attr');
                var current_email = $('.verifyEmailChildKey').val();
                
                var email = $('#verifyToEmail-'+id).val();
                let emailChildId = $('#verifyEmailChildBtn-'+id).attr('id-attr');
                console.log(id +"and "+emailChildId);
                if(id == emailChildId){
                    $('#childLeadCountEmail').val(id);
                    if(!$(this).parsley().isValid()){
                        console.log("empty child");
                        $('#verifyEmailChildBtn-'+id).attr('disabled', 'disabled');
                    } else {
                        if(email == current_email && email != "") {
                            $('#verifyEmailChildBtn-'+id).html('Verified');
                            $('#verifyEmailChildBtn-'+id).attr('disabled', 'disabled');
                        } else {
                            $('#verifyEmailChildBtn-'+id).html('Verify');
                            $('#verifyEmailChildBtn-'+id).removeAttr('disabled');
                        }
                    }
                }

            })
      
            $('.verifyPhonechild').on('click', function (e) {
                
                $("#select-otp-type-modal").modal("show");
            });


            $('.verifyPhoneChildKey').keyup(function () {
                
                var current_Phone = $(this).val();
                var childid = $(this).attr('id-attr');
                var Phone = $('#verifyToPhone-'+childid).val();
                $('.childLeadValue').val('1');
                var id = $('#verifyPhonechild-'+childid).attr('id-attr');
                if(id == childid ){
                    $('#childLeadCount').val(childid);
                    if(!$(this).parsley().isValid()){
                        console.log("empty")
                        $('#verifyPhonechild-'+childid).attr('disabled', 'disabled');
                    } else {
                        if(Phone == current_Phone && Phone != "") {
                            $('#verifyPhonechild-'+childid).html('Verified');
                            $('#verifyPhonechild-'+childid).attr('disabled', 'disabled');
                        } else {
                            $('#verifyPhonechild-'+childid).html('Verify');
                            $('#verifyPhonechild-'+childid).removeAttr('disabled');
                        }
                    }
                }

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
                
                $('.popover').each(function() {
                    $(this).popover('hide');
                    $(this).data("bs.popover").inState.click = false;
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

        // function unloadPage() {
        //     if (unsaved) {
        //         return "Changes that you made may not be saved.";
        //     }
        // }
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


    @if(isset($input['state']) && !empty($input['state']))
        validateState();
    @elseif(isset($input))
        validateZipcode();
    @endif
    /*-----end-new-design-selectbox--*/
    // window.onbeforeunload = unloadPage;

    function printZipcodeErrorMessage(className)
    {
        $('.'+className).text(zipcodeErrorMessage);
    }

    function printStateErrorMessage(className)
    {
        $('.'+className).text(stateErrorMessage);
    }

    // for show ecogold option field on depend program code
    // function setEcogoldProgramOption(code) {
    //     var springGaurd = 'spring guard';
    //     var ecogoldClass = '.ecogold-program';
    //     $(ecogoldClass+" option").prop('disabled',true);
    //     if (code == ECOGOLD_PROGRAM_CODE) {
    //         $(ecogoldClass+" option:icontains("+springGaurd+")").prop('disabled',false);
    //         $(ecogoldClass).val(springGaurd).trigger('change.select2');
    //     } else if($.inArray(code, ECOGOLD_CODE_WITHOUT_SG) !== -1) {
    //         let cashBack = '3% cash back';
    //         let rewards = '5% ecogold rewards';
    //         $(ecogoldClass+" option:icontains("+cashBack+")").prop('disabled',false);
    //         $(ecogoldClass+" option:icontains("+rewards+")").prop('disabled',false);
    //     } else {
    //         $(ecogoldClass+" option").prop('disabled',false);
    //     }
    //     $(ecogoldClass).not(":disabled").prop("selectedIndex", 0).trigger('change.select2');
    //     $(ecogoldClass).select2();
    // }

    // for show promo code option field on depend program code
    // function setPromoCodeFieldOption(code) {
    //     console.log(code);
    //     var gift = '$25 gift card 3mo';
    //     var energy = '$200 energy efficiency';
    //     var NA = 'not applicable';
    //     var promoClass = '.promo-code-field';

    //     $(promoClass+" option").prop('disabled',true);
    //     if ($.inArray(code, PROMO_CODE_GIFT) !== -1) {
    //         $(promoClass+" option:icontains("+gift+")").prop('disabled',false);
    //         $(promoClass).val(gift).trigger('change.select2');
    //     } else if ($.inArray(code, PROMO_CODE_ALL) !== -1) {
    //         $(promoClass+" option:icontains("+gift+")").prop('disabled',false);
    //         $(promoClass+" option:icontains("+energy+")").prop('disabled',false);
    //     } else {
    //         $(promoClass+" option").prop('disabled',false);
    //     }
    //     $(promoClass+" option:icontains("+NA+")").prop('disabled',false);
    //     $(promoClass).not(":disabled").prop("selectedIndex", 0).trigger('change.select2');
    //     $(promoClass).select2();
    // }
</script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
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
</div>