@push('styles')
<style type="text/css">
    /*----------custom-switch---css-----------*/


    .custom-switch .switch {
        position: relative;
        display: inline-block;
        vertical-align: top;
        width: 60px;
        height: 20px;
        padding: 3px;
        border-radius: 18px;
        box-shadow: 0px 0px 1px 1px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        box-sizing: content-box;
    }
    .custom-switch label {
        font-weight: inherit;
    }
    .custom-switch input[type=checkbox], .custom-switch input[type=radio] {
        margin: 4px 0 0;
        line-height: normal;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        padding: 0;
    }
    .custom-switch .switch-input {
        position: absolute;
        top: 0;
        left: 0;
        opacity: 0;
        box-sizing: content-box;
    }
    .custom-switch .switch-left-right .custom-switch .switch-input:checked ~ .switch-label {
        background: inherit;
    }
    .custom-switch .switch-input:checked ~ .switch-label {
        background: #E1B42B;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), inset 0 0 3px rgba(0, 0, 0, 0.2);
    }
    .custom-switch .switch-left-right .switch-label {
        overflow: hidden;
    }
    .custom-switch .switch-label, .custom-switch .switch-handle {
        transition: All 0.3s ease;
        -webkit-transition: All 0.3s ease;
        -moz-transition: All 0.3s ease;
        -o-transition: All 0.3s ease;
    }
    .custom-switch .switch-label {
        position: relative;
        display: block;
        height: inherit;
        font-size: 10px;
        text-transform: uppercase;
        background: #eceeef;
        border-radius: inherit;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.12), inset 0 0 2px rgba(0, 0, 0, 0.15);
        box-sizing: content-box;
    }
    .custom-switch .switch-left-right .switch-input:checked ~ .switch-label:before {
        opacity: 1;
        left: 60px;
    }
    .custom-switch .switch-input:checked ~ .switch-label:before {
        opacity: 0;
    }
    .custom-switch .switch-left-right .switch-label:before {
        background: #eceeef;
        text-align: left;
        padding-left: 40px!important;
    }
    .custom-switch .switch-left-right .switch-label:before, .custom-switch .switch-left-right .switch-label:after {
        width: 15px;
        height: 15px;
        top: 4px;
        left: 0;
        right: 0;
        bottom: 0;
        padding: 11px 0 0 0;
        text-indent: -6px;
        border-radius: 20px;
        box-shadow: inset 0 1px 4px rgba(0, 0, 0, 0.2), inset 0 0 3px rgba(0, 0, 0, 0.1);
    }
    .custom-switch .switch-label:before {
        content: attr(data-off);
        right: 11px;
        color: #aaaaaa;
        text-shadow: 0 1px rgba(255, 255, 255, 0.5);
    }

    .custom-switch span.switch-label:after {
        content: attr(data-on);
        left: 11px;
        color: #FFFFFF;
        text-shadow: 0 1px rgba(0, 0, 0, 0.2);
        position: absolute;
      
    }

    .custom-switch .switch-label:before, .custom-switch .switch-label:after {
        position: absolute;
        top: 50%;
        margin-top: -5px;
        line-height: 1;
        -webkit-transition: inherit;
        -moz-transition: inherit;
        -o-transition: inherit;
        transition: inherit;
        box-sizing: content-box;
    }

    .custom-switch .switch-left-right .switch-input:checked ~ .switch-label:after {
        left: 0!important;
        opacity: 1;
        padding-left: 25px;
    }

    .custom-switch .switch-input:checked ~ .switch-label:after {
        opacity: 1;
    }

    .custom-switch .switch-left-right .switch-label:after {
        text-align: left;
        text-indent: 9px;
        background: #20497c!important;
        left: -60px!important;
        opacity: 1;
        width: 100%!important;
     
    }
    .custom-switch .switch-left-right .switch-label:before, .custom-switch .switch-left-right .switch-label:after {
        width: 20px;
        height: 20px;
        top: 3.5px;
        left: 0;
        right: 0;
        bottom: 0;
        padding: 7px 0 0 0;
        text-indent: -11px;
        border-radius: 20px;
        box-shadow: inset 0 1px 4px rgba(0, 0, 0, 0.2), inset 0 0 3px rgba(0, 0, 0, 0.1);
    }
    .custom-switch .switch-input:checked ~ .switch-handle {
        left: 44px;
        box-shadow: -1px 1px 5px rgba(0, 0, 0, 0.2);
    }
    .custom-switch .switch-label, .custom-switch .switch-handle {
        transition: All 0.3s ease;
        -webkit-transition: All 0.3s ease;
        -moz-transition: All 0.3s ease;
        -o-transition: All 0.3s ease;
    }

    .custom-switch .switch-handle {
        position: absolute;
        top: 3.5px;
        left: 4px;
        width: 18px;
        height: 18px;
        background: linear-gradient(to bottom, #FFFFFF 40%, #f0f0f0);
        background-image: -webkit-linear-gradient(top, #FFFFFF 40%, #f0f0f0);
        border-radius: 100%;
        box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
    }

    .custom-switch .switch-handle:before {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        margin: -6px 0 0 -6px;
        width: 12px;
        height: 12px;
        background: linear-gradient(to bottom, #eeeeee, #FFFFFF);
        background-image: -webkit-linear-gradient(top, #eeeeee, #FFFFFF);
        border-radius: 6px;
        box-shadow: inset 0 1px rgba(0, 0, 0, 0.02);
    }
    /********* added extra *********/
    .custom-switch .switch-input:disabled ~ .switch-label {
        cursor: not-allowed;
    }

    .custom-switch .switch-input:disabled ~ .switch-handle {
        cursor: not-allowed;
    }

    .alert-label .form-group {
        color: #000f
    }

    .form-group {
        font-size: 13px;
    }

    .form-group label {
        font-size: 14px;
    }

    .seprator {
        border-bottom: 2px solid;
        margin: 10px;
    }
    .alert-label tbody tr td:first-child{
        width:70%;
    }

    .alert-label tbody tr td:not(:first-child) {
        width: 10%;
        text-align: center;
    }

    .alert-input {
        width: 55px;
        min-width: 25px;
        max-width: 55px;
        text-align: center;
    }

    .interval-input {
        width: 35px;
        max-width: 45px;
        text-align: center;
    }

    .type-number {
        -webkit-appearance: none;
        -moz-appearance: textfield;
        appearance: none;
        margin: 0; 
    }
    
</style>
@endpush
@php 
    $colWidth='col-xs-7 col-sm-7 col-md-7';
    $swtichWidth='col-xs-1 col-sm-1 col-md-1';
@endphp
<div class="row">
    <div class="col-md-12">
        <div class="cont_bx3 mt30 sor_fil">
            <div class="btn-group pull-right">
                               
            </div>
        </div>
    </div>
</div>
<div class="row mt30">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <form class="" id="program-settings-form" role="form" method="POST" action="{{ route('customFieldProgram.store') }}" data-parsley-validate>
            <div class="row">
                <div class="col-xs-8 col-sm-8 col-md-8">
                    <div class="new-info">
                        <span style="margin-left: 0px;">Programs - Custom Fields</span>
                    </div>
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="btn-group pull-right">
                            @if(auth()->user()->can('edit-client-settings'))
                                <button type="button" class="btn btn-green" id="setting-edit-btn">Edit</button>
                            @endif
                        </div>
                        <div class="btn-group pull-right" style="display: none;" id="setting-save-btn">
                            <button type="submit" class="btn btn-green" style="margin-right: 10px;">Save</button>
                            <button type="button" class="btn btn-red" id="setting-cancel-btn">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
            </br>
            @csrf
            <input type="hidden" name="client_id" value="{{$client_id}}" id="settings-client-id">            
            
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" id="is_enable_field_1" name="is_enable_field_1" value="1" >
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <input id="label_custom_field_1" autocomplete="off" type="text" class="form-control" name="label_custom_field_1"    maxlength="255" data-parsley-validate-if-empty="true" data-parsley-required-if="#is_enable_field_1">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" id="is_enable_field_2" name="is_enable_field_2" value="1" >
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <input id="label_custom_field_2" autocomplete="off" type="text" class="form-control" name="label_custom_field_2"    maxlength="255"  data-parsley-validate-if-empty="true" data-parsley-required-if="#is_enable_field_2">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" id="is_enable_field_3" name="is_enable_field_3" value="1" >
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <input id="label_custom_field_3" autocomplete="off" type="text" class="form-control" name="label_custom_field_3"    maxlength="255"  data-parsley-validate-if-empty="true" data-parsley-required-if="#is_enable_field_3">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" id="is_enable_field_4" name="is_enable_field_4" value="1" >
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <input id="label_custom_field_4" autocomplete="off" type="text" class="form-control" name="label_custom_field_4"    maxlength="255"  data-parsley-validate-if-empty="true" data-parsley-required-if="#is_enable_field_4">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" id="is_enable_field_5" name="is_enable_field_5" value="1" >
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <input id="label_custom_field_5" autocomplete="off" type="text" class="form-control" name="label_custom_field_5"    maxlength="255"  data-parsley-validate-if-empty="true" data-parsley-required-if="#is_enable_field_5">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-8 col-sm-8 col-md-8 seprator"></div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" name="is_enable_enroll_by_state" value="1"  checked>
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <label for="">Allow Enrollment By State</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" name="is_enable_d2d_app" value="1"  checked>
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <label for="">D2D App</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" name="is_enable_ivr" value="1"  checked>
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <label for="">IVR</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" name="is_enable_cust_call_num" value="1"  checked>
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <label for="">Customer Call In Number</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" name="is_enable_agent_tpv_num" value="1"  checked>
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <label for="">Live Agent TPV Number</label>
                    </div>
                </div>
            </div>

            <!-- <input type="hidden" name="id[]" id="id" value="0"> -->
            <template  id="restrictionMainDiv" style="display:none;">
                <div class="row restrictionSubDiv">
                    <input type="hidden" name="id[]" id="id" value="0">
                        <div class="col-xs-12 col-sm-2 col-md-2">
                        <div class="form-group dropdown select-dropdown state-tpv-now-restrict">
                            <label for="state" class="yesstar">State :</label>
                            <select class="form-control state state-tpv-now-restrict-select" name="state[]" data-parsley-required='true' data-parsley-required-message="Please select state" data-parsley-errors-container="#select2-state-error-message">
                                <option value="">Select any one</option>
                                @foreach($states as $state)
                                <option value="{{ $state->state }}">{{ $state->state }}</option>
                                @endforeach
                            </select>
                            <span class= "select2-state-error-class"></span>
                        </div>
                    </div>
                    
                    
                        <div class="col-xs-6 col-sm-2 col-md-2">
                        <div class="form-group start-time-class">
                            <label for="start_time" class="yesstar">Start Time :</label>
                            <select class="form-control start_time" name="start_time[]" data-parsley-required='true' data-parsley-required-message="Please select start time" data-parsley-errors-container="#select2-start_time-error-message">
                                <option value="">Select any one</option>
                                @foreach(config("constants.TIME_ARRAY") as $time)
                                <option value="{{ $time }}">{{ $time }}</option>
                                @endforeach
                            </select>
                            <span class="select2-start-error-class"></span>
                        </div>
                    </div>

                   
                        <div class="col-xs-6 col-sm-2 col-md-2">
                        <div class="form-group end-time-class">
                            <label for="end_time" class="yesstar">End Time :</label>
                            <select class="form-control end_time" name="end_time[]" data-parsley-required='true' data-parsley-required-message="Please select end time" data-parsley-errors-container="#select2-end_time-error-message">
                                <option value="">Select any one</option>
                                @foreach(config("constants.TIME_ARRAY") as $time)
                                <option value="{{ $time }}">{{ $time }}</option>
                                @endforeach
                            </select>
                            <span class="select2-end-error-class"></span>
                        </div>
                    </div>

                    
                        <div class="col-xs-12 col-sm-3 col-md-3">
                            <div class="form-group profile-timezone {{ $errors->has('timezone') ? ' has-error' : '' }}">
                                <label for="timezone" class="yesstar">Set Timezone :</label>
                                <?php //echo getFormIconImage('images/form-pass.png') ?>
                                <?php $timeZones = getTimeZoneList(); ?>
                                <select class="timezone-select" name="timezone[]" data-parsley-required="true">
                                    
                                    @foreach($timeZones as $key => $val)
                                        <?php
                                            $k = trim(substr($val,0,strpos($val,'(')))
                                        ?>
                                        <option value="{{$k}}">{{$val}} </option>
                                    @endforeach
                                </select>
                                <span id="select2-timezone-error-message"></span>
                            </div>
                        </div>
                                        
                        <div class="col-xs-12 col-sm-3 col-md-3">
                            <div class="form-group">
                            <button class="timeZoneDeleteBtn" onClick="removeselect(this)" id="removetimeZoneRestriction"><i class="fa fa-times" aria-hidden="true"></i></button>
                            </div>
                        </div>                
                </div>  
            </template>   
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" name="is_enable_self_tpv_tele" value="1" checked>
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="row">
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <label for="">Self TPV: Tele</label>
                            </div>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4">
                            <div class="form-group select-dropdown multiselect-with-checkbox">
                                <label >States </label>
                                <select class="form-control multiselect-drop-down" id="restrict_states_self_tpv_tele" name="restrict_states_self_tpv_tele[]" multiple='multiple'>
                                    @foreach($states as $state)
                                        <option value="{{ $state->state }}">{{ $state->state }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>     
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" name="is_enable_self_tpv_d2d" value="1"  checked>
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="row">
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <label for="">Self TPV: D2D</label>
                            </div>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4">
                            <div class="form-group select-dropdown multiselect-with-checkbox">
                                <label >States </label>
                                <select class="form-control multiselect-drop-down" id="restrict_states_self_tpv_d2d" name="restrict_states_self_tpv_d2d[]" multiple='multiple'>
                                    @foreach($states as $state)
                                        <option value="{{ $state->state }}">{{ $state->state }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" name="is_enable_contract_tele" value="1" checked>
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <label for="">Customer Contracts: Tele</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" name="is_enable_contract_d2d" value="1" checked>
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <label for="">Customer Contracts: D2D</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" name="is_enable_send_contract_after_lead_verify_tele" value="1">
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <label for="">Send contracts after lead verification: Tele</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" name="is_enable_send_contract_after_lead_verify_d2d" value="1">
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <label for="">Send contracts after lead verification: D2D</label>
                    </div>
                </div>
            </div>             
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" name="is_enable_lead_view_page" value="1" checked>
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <label for="">Lead View Page: Tele</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" name="is_enable_lead_view_page_d2d" value="1" checked>
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <label for="">Lead View Page: D2D</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" name="is_enable_clone_lead" value="1" checked>
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <label for="">Clone Lead</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox"  name="is_enable_hunt_group" value="1" checked>
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <label for="">Hunt Group/Notify360</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" name="is_enable_recording" value="1" checked>
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <label for="">In App Recording</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" name="is_enable_agent_time_clock" value="1" checked>
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <label for="">D2d Agent Time Clock</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox"  name="is_enable_alert_tele" value="1" checked>
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group alert-label">
                        <table><tr><td>
                            <label for="">Alerts: Tele</label>
                            </td><td><label for="">Auto Cancel </label></td>
                                <td><label for="">Show to agents</label></td>
                                <td><label for="">Interval (days) </label></td>
                            </tr>
                            <tr><td>
                            <div class="form-group">
                                <input autocomplete="off" id="is_enable_alert1_tele" type="checkbox" class="styled-checkbox" name="is_enable_alert1_tele" value="1" checked>
                                <label for="is_enable_alert1_tele"></label>
                                Alert 1: Account number has been used <input class="alert-input type-number" type="number" name="max_times_alert1_tele" data-parsley-type="digits" data-parsley-required="true" min="1" data-parsley-errors-container="#max_times_alert1_tele_error" onkeypress="this.style.width = ((this.value.length+1) * 18) + 'px';"> time in any lead in the database
                                <span id="max_times_alert1_tele_error"></span>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert1_tele" type="checkbox" class="styled-checkbox" name="is_critical_alert1_tele" value="1" checked>
                                <label for="is_critical_alert1_tele"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert1_tele" type="checkbox" class="styled-checkbox" name="is_show_agent_alert1_tele" value="1" checked>
                                <label for="is_show_agent_alert1_tele"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" type="text" name="interval_days_alert1_tele" class="interval-input" data-parsley-type="digits">
                            </div></td>
                            </tr>
                            <tr><td><div class="form-group">
                                <input autocomplete="off" id="is_enable_alert2_tele" type="checkbox" class="styled-checkbox" name="is_enable_alert2_tele" value="1" checked>
                                <label for="is_enable_alert2_tele"></label>
                                Alert 2: Authorized name and account number matches any lead in the database
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert2_tele" type="checkbox" class="styled-checkbox" name="is_critical_alert2_tele" value="1" checked>
                                <label for="is_critical_alert2_tele"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert2_tele" type="checkbox" class="styled-checkbox" name="is_show_agent_alert2_tele" value="1" checked>
                                <label for="is_show_agent_alert2_tele"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" type="text" name="interval_days_alert2_tele" class="interval-input" data-parsley-type="digits">
                            </div></td>
                            </tr>
                            <tr><td><div class="form-group">
                                <input autocomplete="off" id="is_enable_alert3_tele" type="checkbox" class="styled-checkbox" name="is_enable_alert3_tele" value="1" checked>
                                <label for="is_enable_alert3_tele"></label>
                                Alert 3: Email used has appeared <input class="alert-input type-number" type="number" name="max_times_alert3_tele" data-parsley-type="digits" data-parsley-required="true" min="1" data-parsley-errors-container="#max_times_alert3_tele_error" onkeypress="this.style.width = ((this.value.length+1) * 18) + 'px';"> or more times in the database 
                                <span id="max_times_alert3_tele_error"></span>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert3_tele" type="checkbox" class="styled-checkbox" name="is_critical_alert3_tele" value="1" checked>
                                <label for="is_critical_alert3_tele"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert3_tele" type="checkbox" class="styled-checkbox" name="is_show_agent_alert3_tele" value="1" checked>
                                <label for="is_show_agent_alert3_tele"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" type="text" name="interval_days_alert3_tele" class="interval-input" data-parsley-type="digits">
                            </div></td>
                            </tr>
                            <tr><td><div class="form-group">
                                <input autocomplete="off" id="is_enable_alert4_tele" type="checkbox" class="styled-checkbox" name="is_enable_alert4_tele" value="1" checked>
                                <label for="is_enable_alert4_tele"></label>
                                Alert 4: Phone number used has appeared <input class="alert-input type-number" type="number" name="max_times_alert4_tele" data-parsley-type="digits" data-parsley-required="true" min="1" data-parsley-errors-container="#max_times_alert4_tele_error" onkeypress="this.style.width = ((this.value.length+1) * 18) + 'px';"> or more times in the database
                                <span id="max_times_alert4_tele_error"></span>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert4_tele" type="checkbox" class="styled-checkbox" name="is_critical_alert4_tele" value="1" checked >
                                <label for="is_critical_alert4_tele"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert4_tele" type="checkbox" class="styled-checkbox" name="is_show_agent_alert4_tele" value="1" checked>
                                <label for="is_show_agent_alert4_tele"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" type="text" name="interval_days_alert4_tele" class="interval-input" data-parsley-type="digits">
                            </div></td>
                            </tr>
                            <tr><td><div class="form-group">
                                <input autocomplete="off" id="is_enable_alert5_tele" type="checkbox" class="styled-checkbox" name="is_enable_alert5_tele" value="1" checked>
                                <label for="is_enable_alert5_tele"></label>
                                Alert 5: Email used belongs to any sales agent of the same client
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert5_tele" type="checkbox" class="styled-checkbox" name="is_critical_alert5_tele" value="1" checked >
                                <label for="is_critical_alert5_tele"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert5_tele" type="checkbox" class="styled-checkbox" name="is_show_agent_alert5_tele" value="1" checked>
                                <label for="is_show_agent_alert5_tele"></label>
                            </div></td>
                            </tr>
                            <tr><td><div class="form-group">
                                <input autocomplete="off" id="is_enable_alert6_tele" type="checkbox" class="styled-checkbox" name="is_enable_alert6_tele" value="1" checked>
                                <label for="is_enable_alert6_tele"></label>
                                Alert 6: Phone number used belongs to any sales agent of the same client
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert6_tele" type="checkbox" class="styled-checkbox" name="is_critical_alert6_tele" value="1" checked>
                                <label for="is_critical_alert6_tele"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert6_tele" type="checkbox" class="styled-checkbox" name="is_show_agent_alert6_tele" value="1" checked>
                                <label for="is_show_agent_alert6_tele"></label>
                            </div></td>
                            </tr>
                            <tr><td><div class="form-group">
                                <input autocomplete="off" id="is_enable_alert7_tele" type="checkbox" class="styled-checkbox" name="is_enable_alert7_tele" value="1" checked>
                                <label for="is_enable_alert7_tele"></label>
                                Alert 7: Phone number and/or email belongs to the sales agent who is submitting the lead 
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert7_tele" type="checkbox" class="styled-checkbox" name="is_critical_alert7_tele" value="1" checked>
                                <label for="is_critical_alert7_tele"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert7_tele" type="checkbox" class="styled-checkbox" name="is_show_agent_alert7_tele" value="1" checked>
                                <label for="is_show_agent_alert7_tele"></label>
                            </div></td>
                            </tr>
                            <tr><td><div class="form-group">
                                <input autocomplete="off" id="is_enable_alert8_tele" type="checkbox" class="styled-checkbox" name="is_enable_alert8_tele" value="1" checked>
                                <label for="is_enable_alert8_tele"></label>
                                Alert 8: Account number(s) used in previous verified lead in the database
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert8_tele" type="checkbox" class="styled-checkbox" name="is_critical_alert8_tele" value="1" checked>
                                <label for="is_critical_alert8_tele"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert8_tele" type="checkbox" class="styled-checkbox" name="is_show_agent_alert8_tele" value="1" checked>
                                <label for="is_show_agent_alert8_tele"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" type="text" name="interval_days_alert8_tele" class="interval-input" data-parsley-type="digits">
                            </div></td>
                            </tr>
                            <tr><td><div class="form-group">
                                <input autocomplete="off" id="is_enable_alert9_tele" type="checkbox" class="styled-checkbox" name="is_enable_alert9_tele" value="1" checked>
                                <label for="is_enable_alert9_tele"></label>
                                Alert 9: Primary number used in previous verified lead in the database
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert9_tele" type="checkbox" class="styled-checkbox" name="is_critical_alert9_tele" value="1" checked >
                                <label for="is_critical_alert9_tele"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert9_tele" type="checkbox" class="styled-checkbox" name="is_show_agent_alert9_tele" value="1" checked>
                                <label for="is_show_agent_alert9_tele"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" type="text" name="interval_days_alert9_tele" class="interval-input" data-parsley-type="digits">
                            </div></td>
                            </tr>
                            <tr><td><div class="form-group">
                                <input autocomplete="off" id="is_enable_alert10_tele" type="checkbox" class="styled-checkbox" name="is_enable_alert10_tele" value="1" checked>
                                <label for="is_enable_alert10_tele"></label>
                                Alert 10: TPV attempt for lead has reached <input class="alert-input type-number" type="number" name="max_times_alert10_tele" data-parsley-type="digits" data-parsley-required="true" min="1" data-parsley-errors-container="#max_times_alert10_tele_error" onkeypress="this.style.width = ((this.value.length+1) * 18) + 'px';"> attempts
                                <span id="max_times_alert10_tele_error"></span>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert10_tele" type="checkbox" class="styled-checkbox" name="is_critical_alert10_tele" value="1" checked >
                                <label for="is_critical_alert10_tele"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert10_tele" type="checkbox" class="styled-checkbox" name="is_show_agent_alert10_tele" value="1" checked>
                                <label for="is_show_agent_alert10_tele"></label>
                            </div></td>
                            </tr>
                            <tr><td><div class="form-group">
                                <input autocomplete="off" id="is_enable_alert11_tele" type="checkbox" class="styled-checkbox" name="is_enable_alert11_tele" value="1" checked>
                                <label for="is_enable_alert11_tele"></label>
                                Alert 11: Customer name & primary number used in previous verified lead in the database 
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert11_tele" type="checkbox" class="styled-checkbox" name="is_critical_alert11_tele" value="1" checked >
                                <label for="is_critical_alert11_tele"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert11_tele" type="checkbox" class="styled-checkbox" name="is_show_agent_alert11_tele" value="1" checked>
                                <label for="is_show_agent_alert11_tele"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" type="text" name="interval_days_alert11_tele" class="interval-input" data-parsley-type="digits">
                            </div></td>
                            </tr>
                            <tr><td><div class="form-group">
                                <input autocomplete="off" id="is_enable_alert12_tele" type="checkbox" class="styled-checkbox" name="is_enable_alert12_tele" value="1" checked>
                                <label for="is_enable_alert12_tele"></label>
                                Alert 12: Service address used in a verified lead in the database
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert12_tele" type="checkbox" class="styled-checkbox" name="is_critical_alert12_tele" value="1" checked >
                                <label for="is_critical_alert12_tele"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert12_tele" type="checkbox" class="styled-checkbox" name="is_show_agent_alert12_tele" value="1" checked>
                                <label for="is_show_agent_alert12_tele"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" type="text" name="interval_days_alert12_tele" class="interval-input" data-parsley-type="digits">
                            </div></td>
                            </tr>                            
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input class="switch-input" type="checkbox" name="is_enable_alert_d2d" value="1" checked>
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group alert-label">
                        <table><tr><td>
                        <label for="">Alerts: D2D</label></td>
                        <td><label for="">Auto Cancel </label></td>
                        <td><label for="">Show to agents</label></td>
                        <td><label for="">Interval (days) </label></td>
                        </tr>
                        <tr><td><div class="form-group">
                            <input autocomplete="off" id="is_enable_alert1_d2d" type="checkbox" class="styled-checkbox" name="is_enable_alert1_d2d" value="1" checked>
                            <label for="is_enable_alert1_d2d"></label>
                            Alert 1:  Account number has been used <input class="alert-input type-number" type="number" name="max_times_alert1_d2d" data-parsley-type="digits" data-parsley-required="true" min="1" data-parsley-errors-container="#max_times_alert1_d2d_error" onkeypress="this.style.width = ((this.value.length+1) * 18) + 'px';"> time in any lead in the database
                            <span id="max_times_alert1_d2d_error"></span>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert1_d2d" type="checkbox" class="styled-checkbox" name="is_critical_alert1_d2d" value="1" checked>
                                <label for="is_critical_alert1_d2d"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert1_d2d" type="checkbox" class="styled-checkbox" name="is_show_agent_alert1_d2d" value="1" checked>
                                <label for="is_show_agent_alert1_d2d"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" type="text" name="interval_days_alert1_d2d" class="interval-input" data-parsley-type="digits">
                            </div>
                            </td>
                        </tr>
                        <tr><td><div class="form-group">
                            <input autocomplete="off" id="is_enable_alert2_d2d" type="checkbox" class="styled-checkbox" name="is_enable_alert2_d2d" value="1" checked>
                            <label for="is_enable_alert2_d2d"></label>
                            Alert 2: Authorized name and account number matches any lead in the database
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert2_d2d" type="checkbox" class="styled-checkbox" name="is_critical_alert2_d2d" value="1" checked>
                                <label for="is_critical_alert2_d2d"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert2_d2d" type="checkbox" class="styled-checkbox" name="is_show_agent_alert2_d2d" value="1" checked>
                                <label for="is_show_agent_alert2_d2d"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" type="text" name="interval_days_alert2_d2d" class="interval-input" data-parsley-type="digits">
                            </div>
                            </td>
                        </tr>
                        <tr><td><div class="form-group">
                            <input autocomplete="off" id="is_enable_alert3_d2d" type="checkbox" class="styled-checkbox" name="is_enable_alert3_d2d" value="1" checked>
                            <label for="is_enable_alert3_d2d"></label>
                            Alert 3: Email used has appeared <input class="alert-input type-number" type="number" name="max_times_alert3_d2d" data-parsley-type="digits" data-parsley-required="true" min="1" data-parsley-errors-container="#max_times_alert3_d2d_error" onkeypress="this.style.width = ((this.value.length+1) * 18) + 'px';"> or more times in the database
                            <span id="max_times_alert3_d2d_error"></span> 
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert3_d2d" type="checkbox" class="styled-checkbox" name="is_critical_alert3_d2d" value="1" checked>
                                <label for="is_critical_alert3_d2d"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert3_d2d" type="checkbox" class="styled-checkbox" name="is_show_agent_alert3_d2d" value="1" checked>
                                <label for="is_show_agent_alert3_d2d"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" type="text" name="interval_days_alert3_d2d" class="interval-input" data-parsley-type="digits">
                            </div>
                            </td>
                        </tr>
                        <tr><td><div class="form-group">
                            <input autocomplete="off" id="is_enable_alert4_d2d" type="checkbox" class="styled-checkbox" name="is_enable_alert4_d2d" value="1" checked>
                            <label for="is_enable_alert4_d2d"></label>
                            Alert 4: Phone number used has appeared <input class="alert-input type-number" type="number" name="max_times_alert4_d2d" data-parsley-type="digits" data-parsley-required="true" min="1" data-parsley-errors-container="#max_times_alert4_d2d_error" onkeypress="this.style.width = ((this.value.length+1) * 18) + 'px';"> or more times in the database
                            <span id="max_times_alert4_d2d_error"></span>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert4_d2d" type="checkbox" class="styled-checkbox" name="is_critical_alert4_d2d" value="1" checked>
                                <label for="is_critical_alert4_d2d"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert4_d2d" type="checkbox" class="styled-checkbox" name="is_show_agent_alert4_d2d" value="1" checked>
                                <label for="is_show_agent_alert4_d2d"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" type="text" name="interval_days_alert4_d2d" class="interval-input" data-parsley-type="digits">
                            </div>
                            </td>
                        </tr>
                        <tr><td><div class="form-group">
                            <input autocomplete="off" id="is_enable_alert5_d2d" type="checkbox" class="styled-checkbox" name="is_enable_alert5_d2d" value="1" checked>
                            <label for="is_enable_alert5_d2d"></label>
                            Alert 5: Email used belongs to any sales agent of the same client
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert5_d2d" type="checkbox" class="styled-checkbox" name="is_critical_alert5_d2d" value="1" checked>
                                <label for="is_critical_alert5_d2d"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert5_d2d" type="checkbox" class="styled-checkbox" name="is_show_agent_alert5_d2d" value="1" checked>
                                <label for="is_show_agent_alert5_d2d"></label>
                            </div></td>
                        </tr>
                        <tr><td><div class="form-group">
                            <input autocomplete="off" id="is_enable_alert6_d2d" type="checkbox" class="styled-checkbox" name="is_enable_alert6_d2d" value="1" checked>
                            <label for="is_enable_alert6_d2d"></label>
                            Alert 6: Phone number used belongs to any sales agent of the same client
                        </div></td>
                        <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert6_d2d" type="checkbox" class="styled-checkbox" name="is_critical_alert6_d2d" value="1" checked>
                                <label for="is_critical_alert6_d2d"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert6_d2d" type="checkbox" class="styled-checkbox" name="is_show_agent_alert6_d2d" value="1" checked>
                                <label for="is_show_agent_alert6_d2d"></label>
                            </div></td>
                        </tr>
                        <tr><td><div class="form-group">
                            <input autocomplete="off" id="is_enable_alert7_d2d" type="checkbox" class="styled-checkbox" name="is_enable_alert7_d2d" value="1" checked>
                            <label for="is_enable_alert7_d2d"></label>
                            Alert 7: Phone number and/or email belongs to the sales agent who is submitting the lead 
                        </div></td>
                        <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert7_d2d" type="checkbox" class="styled-checkbox" name="is_critical_alert7_d2d" value="1" checked>
                                <label for="is_critical_alert7_d2d"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert7_d2d" type="checkbox" class="styled-checkbox" name="is_show_agent_alert7_d2d" value="1" checked>
                                <label for="is_show_agent_alert7_d2d"></label>
                            </div></td>
                        </tr>
                        <tr><td><div class="form-group">
                            <input autocomplete="off" id="is_enable_alert8_d2d" type="checkbox" class="styled-checkbox" name="is_enable_alert8_d2d" value="1" checked>
                            <label for="is_enable_alert8_d2d"></label>
                            Alert 8: Geomapping [Radius <input class="alert-input type-number" type="number" name="geomapping_radius" data-parsley-type="digits" data-parsley-required="true" min="1" data-parsley-errors-container="#geomapping_radius" onkeypress="this.style.width = ((this.value.length+1) * 43) + 'px';"> Meters]
                            <span id="geomapping_radius"></span> 
                        </div></td>
                        <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert8_d2d" type="checkbox" class="styled-checkbox" name="is_critical_alert8_d2d" value="1" checked>
                                <label for="is_critical_alert8_d2d"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert8_d2d" type="checkbox" class="styled-checkbox" name="is_show_agent_alert8_d2d" value="1" checked>
                                <label for="is_show_agent_alert8_d2d"></label>
                            </div></td>
                        </tr>
                        <tr><td><div class="form-group">
                            <input autocomplete="off" id="is_enable_alert9_d2d" type="checkbox" class="styled-checkbox" name="is_enable_alert9_d2d" value="1" checked>
                            <label for="is_enable_alert9_d2d"></label>
                            Alert 9: Account number(s) used in previous verified lead in the database
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert9_d2d" type="checkbox" class="styled-checkbox" name="is_critical_alert9_d2d" value="1" checked>
                                <label for="is_critical_alert9_d2d"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert9_d2d" type="checkbox" class="styled-checkbox" name="is_show_agent_alert9_d2d" value="1" checked>
                                <label for="is_show_agent_alert9_d2d"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" type="text" name="interval_days_alert9_d2d" class="interval-input" data-parsley-type="digits">
                            </div>
                            </td>
                        </tr>
                        <tr><td><div class="form-group">
                            <input autocomplete="off" id="is_enable_alert10_d2d" type="checkbox" class="styled-checkbox" name="is_enable_alert10_d2d" value="1" checked>
                            <label for="is_enable_alert10_d2d"></label>
                            Alert 10: Primary number used in previous verified lead in the database
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert10_d2d" type="checkbox" class="styled-checkbox" name="is_critical_alert10_d2d" value="1" checked>
                                <label for="is_critical_alert10_d2d"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert10_d2d" type="checkbox" class="styled-checkbox" name="is_show_agent_alert10_d2d" value="1" checked>
                                <label for="is_show_agent_alert10_d2d"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" type="text" name="interval_days_alert10_d2d" class="interval-input" data-parsley-type="digits">
                            </div>
                            </td>
                        </tr>
                        <tr><td><div class="form-group">
                            <input autocomplete="off" id="is_enable_alert11_d2d" type="checkbox" class="styled-checkbox" name="is_enable_alert11_d2d" value="1" checked>
                            <label for="is_enable_alert11_d2d"></label>
                            Alert 11: TPV attempt for lead has reached <input class="alert-input type-number" type="number" name="max_times_alert11_d2d" data-parsley-type="digits" data-parsley-required="true" min="1" data-parsley-errors-container="#max_times_alert11_d2d_error" onkeypress="this.style.width = ((this.value.length+1) * 18) + 'px';"> attempts
                            <span id="max_times_alert11_d2d_error"></span>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert11_d2d" type="checkbox" class="styled-checkbox" name="is_critical_alert11_d2d" value="1" checked>
                                <label for="is_critical_alert11_d2d"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert11_d2d" type="checkbox" class="styled-checkbox" name="is_show_agent_alert11_d2d" value="1" checked>
                                <label for="is_show_agent_alert11_d2d"></label>
                            </div></td>
                            </td>
                        </tr>
                        <tr><td><div class="form-group">
                            <input autocomplete="off" id="is_enable_alert12_d2d" type="checkbox" class="styled-checkbox" name="is_enable_alert12_d2d" value="1" checked>
                            <label for="is_enable_alert12_d2d"></label>
                            Alert 12: Customer name & primary number used in previous verified lead in the database
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert12_d2d" type="checkbox" class="styled-checkbox" name="is_critical_alert12_d2d" value="1" checked>
                                <label for="is_critical_alert12_d2d"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert12_d2d" type="checkbox" class="styled-checkbox" name="is_show_agent_alert12_d2d" value="1" checked>
                                <label for="is_show_agent_alert12_d2d"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" type="text" name="interval_days_alert12_d2d" class="interval-input" data-parsley-type="digits">
                            </div>
                            </td>
                        </tr>
                        <tr><td><div class="form-group">
                            <input autocomplete="off" id="is_enable_alert13_d2d" type="checkbox" class="styled-checkbox" name="is_enable_alert13_d2d" value="1" checked>
                            <label for="is_enable_alert13_d2d"></label>
                            Alert 13: Service address used in a verified lead in the database
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_critical_alert13_d2d" type="checkbox" class="styled-checkbox" name="is_critical_alert13_d2d" value="1" checked>
                                <label for="is_critical_alert13_d2d"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" id="is_show_agent_alert13_d2d" type="checkbox" class="styled-checkbox" name="is_show_agent_alert13_d2d" value="1" checked>
                                <label for="is_show_agent_alert13_d2d"></label>
                            </div></td>
                            <td><div class="form-group">
                                <input autocomplete="off" type="text" name="interval_days_alert13_d2d" class="interval-input" data-parsley-type="digits">
                            </div>
                            </td>
                        </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input id="is_enable_self_tpv_welcome_call" class="switch-input" type="checkbox" name="is_enable_self_tpv_welcome_call" value="1"  checked>
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <label for="">Self Verify Welcome Call</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-3 col-md-3">
                    <div class="form-group">
                        <label for="self_tpv_max_no_of_call_attempt" class="yesstar">Self TPV maximum call attempt :</label>
                        <select class="select2 form-control" id="self_tpv_max_no_of_call_attempt" name="self_tpv_max_no_of_call_attempt" data-parsley-validate-if-empty="true" data-parsley-required-if="#is_enable_self_tpv_welcome_call" data-parsley-required-if-message="Please select self TPV maximum call attempt" data-parsley-errors-container="#self_tpv_max_no_of_call_attempt_error">
                            <option value="">Select</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                        <span id="self_tpv_max_no_of_call_attempt_error"></span>
                    </div>
                </div>
                <div id="self_tpv_delay_section"></div>  
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input id="is_enable_outbound_tpv" class="switch-input" type="checkbox" name="is_enable_outbound_tpv" value="1"  checked>
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <label for="">Outbound TPV</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-3 col-md-3">
                    <div class="form-group">
                        <label for="tpv_now_max_no_of_call_attempt" class="yesstar">TPV now maximum call attempt :</label>
                        <select class="select2 form-control" id="tpv_now_max_no_of_call_attempt" name="tpv_now_max_no_of_call_attempt" data-parsley-validate-if-empty="true" data-parsley-required-if="#is_enable_outbound_tpv" data-parsley-required-if-message="Please select TPV now maximum call attempt" data-parsley-errors-container="#select2-max_call_attempt-error-message">
                            <option value="">Select</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                        <span id="select2-max_call_attempt-error-message"></span>
                    </div>
                </div>
                <div id="listOfTextBox" text-count='0'></div>  
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <div class="form-group">
                        <label for="add_time_zone_restriction">Add time zone restriction : </label>
                        <button type="button" class="btn btn-green" id="timeZoneRestrictionBtn" >Add</button>
                    </div>
                </div>
            </div>
            <div class="">
            <div id="structureDiv">
                <?php //echo "<pre>"; print_r($restrictionFields->toArray()); exit; ?>
                @forelse($restrictionFields as $key => $value)
                <div class="row restrictionSubDiv">
                    <input type="hidden" name="id[]" id="id" value="{{ $value->id }}">
                   
                        <div class="col-xs-12 col-sm-2 col-md-2 ">
                        <div class="form-group dropdown select-dropdown">
                            <label for="state" class="yesstar">State :</label>
                            <select class="select2 form-control state" class="state-tpv-now-restrict-select" name="state[]" data-parsley-required='true' data-parsley-required-message="Please select state" data-parsley-errors-container="#select2-state-error-message-{{$key}}">
                                <option value="">Select any one</option>
                                @foreach($states as $state)
                                @if($state->state == $value->state)
                                    <option value="{{ $state->state }}" selected>{{ $state->state }}</option>
                                @else
                                <option value="{{ $state->state }}">{{ $state->state }}</option>
                                @endif
                                @endforeach
                            </select>
                            <span id="select2-state-error-message-{{$key}}"></span>
                        </div>
                    </div>
                    
                   
                        <div class="col-xs-6 col-sm-2 col-md-2">
                        <div class="form-group">
                            <label for="start_time" class="yesstar">Start Time :</label>
                            <select class="select2 form-control start_time" name="start_time[]" data-parsley-required='true' data-parsley-required-message="Please select start time" data-parsley-errors-container="#select2-start_time-error-message-{{$key}}">
                                <option value="">Select any one</option>
                                
                                @foreach(config("constants.TIME_ARRAY") as $time)
                                @if($time == $value->start_time)
                                    <option value="{{ $time }}" selected>{{ $time }}</option>
                                @else
                                    <option value="{{ $time }}">{{ $time }}</option>
                                @endif
                                @endforeach
                            </select>
                            <span id="select2-start_time-error-message-{{$key}}"></span>
                        </div>
                    </div>
                    
                   
                        <div class="col-xs-6 col-sm-2 col-md-2">
                        <div class="form-group">
                            <label for="end_time" class="yesstar">End Time :</label>
                            <select class="select2 form-control end_time" name="end_time[]" data-parsley-required='true' data-parsley-required-message="Please select end time" data-parsley-errors-container="#select2-end_time-error-message-{{$key}}">
                                <option value="">Select any one</option>
                                
                                @foreach(config("constants.TIME_ARRAY") as $time)
                                
                                @if($time == $value->end_time)
                                    <option value="{{ $time }}" selected>{{ $time }}</option>
                                @else
                                    <option value="{{ $time }}">{{ $time }}</option>
                                @endif
                                @endforeach
                            </select>
                            <span id="select2-end_time-error-message-{{$key}}"></span>
                        </div>
                    </div>

                        <div class="col-xs-12 col-sm-3 col-md-3">
                       
                            <div class="form-group profile-timezone {{ $errors->has('timezone') ? ' has-error' : '' }}">
                                <label for="timezone" class="yesstar">Set Timezone :</label>
                                <?php //echo getFormIconImage('images/form-pass.png') ?>
                                <?php $timeZones = getTimeZoneList(); ?>
                                <select class="select2 timezone-select" name="timezone[]" data-parsley-required="true" data-parsley-errors-container="#select2-timezone-error-message-{{$key}}">
                                    
                                    @foreach($timeZones as $key => $val)
                                    
                                        <?php
                                            $k = trim(substr($val,0,strpos($val,'(')))
                                        ?>
                                        @if($k == $value->timezone)
                                            <option value="{{$k}}" selected>{{$val}} </option>
                                        @else
                                        <option value="{{$k}}">{{$val}} </option>
                                        @endif
                                    @endforeach
                                </select>
                                <span id="select2-timezone-error-message-{{$key}}"></span>
                            </div>
                        
                    </div>
                  
                        <div class="col-xs-12 col-sm-3 col-md-3">
                            <div class="form-group">
                                <button class="timeZoneDeleteBtn" data-id = "{{$value->id}}"onClick="removeselect(this)" id="removetimeZoneRestriction"><i class="fa fa-times" aria-hidden="true"></i></button>
                            </div>
                        </div>
                </div> 
                @empty
                @endforelse
            </div>
            </div>
            <div class="row">
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        {{-- <div class="col-md-6"> --}}
                            <label for="" class="yesstar">Lead Expiry Time(In hours)</label>
                        {{-- </div> --}}
                        {{-- <div class="col-md-6"> --}}
                            {{-- <input class="form-control"  type="text" name="lead_expiry_time" data-parsley-type="digits" data-parsley-required="true" data-parsley-errors-container="#lead_expiry_time"> --}}
                            <input class="alert-input type-number" type="number" name="lead_expiry_time" data-parsley-type="digits" data-parsley-required="true" min="1" data-parsley-errors-container="#lead_expiry_time" onkeypress="this.style.width = ((this.value.length+1) * 100) + 'px';">
                        {{-- </div> --}}
                        <span id="lead_expiry_time"></span> 
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="{{$swtichWidth}}">
                    <div class="form-group">
                        <div class="custom-switch">
                            <label class="switch switch-left-right">
                                <input id="is_outbound_disconnect" class="switch-input" type="checkbox" name="is_outbound_disconnect" value="1" checked>
                                <span class="switch-label" data-on="On" data-off="Off"></span> <span class="switch-handle"></span> </label>
                        </div>
                    </div>
                </div>
                <div class="{{$colWidth}}">
                    <div class="form-group">
                        <label for="">Outbound Disconnect</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-3 col-md-3">
                    <div class="form-group">
                        <label for="outbound_disconnect_max_reschedule_call_attempt" class="yesstar">Outbound Disconnect Max Reschedule call Attempt :</label>
                        <select class="select2 form-control" id="outbound_disconnect_max_reschedule_call_attempt" name="outbound_disconnect_max_reschedule_call_attempt" data-parsley-validate-if-empty="true"  data-parsley-required-if="#is_outbound_disconnect" data-parsley-required-if-message="Please select outbound disconnect maximum call attempt" data-parsley-errors-container="#select2-max_call_outbound_disconnect_attempt-error-message">
                            <option value="">Select</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                        <span id="select2-max_call_outbound_disconnect_attempt-error-message"></span>
                    </div>
                </div>
                <div id="listOfTextBoxOutboundDisconnect"></div>  
            </div>
            
            <!-- <input type="hidden" name="id[]" id="id" value="0"> -->
            <input type="hidden" name="deleteId[]" id="delete-id">
            <template  id="restrictionMainDiv">
                <div class="row restrictionSubDiv">
                    
                   
                        <div class="col-xs-12 col-sm-2 col-md-2">
                        <div class="form-group dropdown select-dropdown state-tpv-now-restrict">
                            <label for="state" class="yesstar">State :</label>
                            <select class="form-control state state-tpv-now-restrict-select" name="state[]" data-parsley-required='true' data-parsley-required-message="Please select state" data-parsley-errors-container="#select2-state-error-message">
                                <option value="">Select any one</option>
                                @foreach($states as $state)
                                <option value="{{ $state->state }}">{{ $state->state }}</option>
                                @endforeach
                            </select>
                            <span class= "select2-state-error-class"></span>
                        </div>
                    </div>
                    
                        <div class="col-xs-6 col-sm-2 col-md-2">
                        <div class="form-group start-time-class">
                            <label for="start_time" class="yesstar">Start Time :</label>
                            <select class="form-control start_time" name="start_time[]" data-parsley-required='true' data-parsley-required-message="Please select start time" data-parsley-errors-container="#select2-start_time-error-message">
                                <option value="">Select any one</option>
                                @foreach(config("constants.TIME_ARRAY") as $time)
                                <option value="{{ $time }}">{{ $time }}</option>
                                @endforeach
                            </select>
                            <span class="select2-start-error-class"></span>
                        </div>
                    </div>

                   
                        <div class="col-xs-6 col-sm-2 col-md-2">
                        <div class="form-group end-time-class">
                            <label for="end_time" class="yesstar">End Time :</label>
                            <select class="form-control end_time" name="end_time[]" data-parsley-required='true' data-parsley-required-message="Please select end time" data-parsley-errors-container="#select2-end_time-error-message">
                                <option value="">Select any one</option>
                                @foreach(config("constants.TIME_ARRAY") as $time)
                                <option value="{{ $time }}">{{ $time }}</option>
                                @endforeach
                            </select>
                            <span class="select2-end-error-class"></span>
                        </div>
                    </div>

                    
                        <div class="col-xs-12 col-sm-3 col-md-3">
                            <div class="form-group profile-timezone {{ $errors->has('timezone') ? ' has-error' : '' }}">
                                <label for="timezone" class="yesstar">Set Timezone :</label>
                                <?php //echo getFormIconImage('images/form-pass.png') ?>
                                <?php $timeZones = getTimeZoneList(); ?>
                                <select class="timezone-select" name="timezone[]" data-parsley-required="true">
                                    
                                    @foreach($timeZones as $key => $val)
                                        <?php
                                            $k = trim(substr($val,0,strpos($val,'(')))
                                        ?>
                                        <option value="{{$k}}">{{$val}} </option>
                                    @endforeach
                                </select>
                                <span id="select2-timezone-error-message"></span>
                            </div>
                        </div>
                                        
                        <div class="col-xs-12 col-sm-3 col-md-3">
                            <div class="form-group">
                            <button class="timeZoneDeleteBtn" onClick="removeselect(this)" id="removetimeZoneRestriction"><i class="fa fa-times" aria-hidden="true"></i></button>
                            </div>
                        </div>                
                </div>  
            </template>   
        </form>
    </div>
</div>

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function(){
            var idArray = [];
            $('.multiselect-drop-down').multiselect({placeholder: 'ALL'});
            resetMultiSelectDesign();

            $(document).on('click','.timeZoneDeleteBtn',function(){
                if($(this).attr('data-id') != null)
                    idArray.push($(this).attr('data-id'));
                $('#delete-id').attr('value',idArray);
                $(this).parents('.restrictionSubDiv').remove();
            })
        //     function removeselect(e){
        //     div = document.getElementById('id');
        //     let idArray = div.getAttribute('value');
        //     // idArray[] = ;
        //     idArray.push(e.getAttribute('data-id'));
        //     console.log(idArray);
        //     div.setAttribute('value',idArray);

            
        //     return false;
        // }
            $("#timeZoneRestrictionBtn").click(function() {
                let divLength = $('#structureDiv').children().length;
                var temp = document.getElementById("restrictionMainDiv");
		        var clon = temp.content.cloneNode(true);
                var div = document.getElementById('structureDiv');
                
                var divId = clon.querySelector('.state-tpv-now-restrict');
                
                let startTimeDiv = clon.querySelector('.start-time-class');
                let endTimeDiv = clon.querySelector('.end-time-class');
                
                var state = divId.getElementsByClassName('state-tpv-now-restrict-select')[0];
                let startTime = startTimeDiv.getElementsByClassName('start_time')[0];
                let endTime = endTimeDiv.getElementsByClassName('end_time')[0];
                state.setAttribute('data-parsley-errors-container','#select2-state-error-'+divLength);
                startTime.setAttribute('data-parsley-errors-container','#select2-start-time-error-'+divLength);
                endTime.setAttribute('data-parsley-errors-container','#select2-end-time-error-'+divLength);
                
                spanState = divId.getElementsByClassName('select2-state-error-class')[0];
                spanState.setAttribute('id','select2-state-error-'+divLength);

                spanStartTime = startTimeDiv.getElementsByClassName('select2-start-error-class')[0];
                spanStartTime.setAttribute('id','select2-start-time-error-'+divLength);

                spanEndTime = endTimeDiv.getElementsByClassName('select2-end-error-class')[0];
                spanEndTime.setAttribute('id','select2-end-time-error-'+divLength);

                // let idField = "<input name='id[]' value='0' />";
                // temp.appendChild(idField);
                div.appendChild(clon);
            
                // $("#structureDiv").append($("#restrictionMainDiv").html());
                // $('.state-tpv-now-restrict').next().attr('id','select2-state-error-message-'+divLength);
                

                $("#structureDiv select").select2();
                $("#program-settings-form").parsley().destroy();
                $("#program-settings-form").parsley();
            });
        });
        
        

        window.Parsley.addValidator("requiredIf", {
            validateString : function(value, requirement) {
                if ($(requirement).is(':checked')){
                    return !!value;
                } 

                return true;
            },
            messages: {en: 'This field is rquired'},
            priority: 33
        });
        $(document).ready(function () {
            // alert($("#tpv_now_max_no_of_call_attempt"));
            $("#timeZoneRestrictionBtn").click(function () {
                $("#timeZoneRestrictionDiv").show();
            });
            $("#removetimeZoneRestriction").click(function () {
                $("#timeZoneRestrictionDiv").hide();
            });
            
            $("#tpv_now_max_no_of_call_attempt").change(function() {
                var selVal = $(this).val();
                var maxVal = $('#listOfTextBox').attr('text-count');
                $("#listOfTextBox").html('');
                if(selVal > 0) {
                    for(var i = 1; i<= selVal; i++) {
                        $("#listOfTextBox").append('<div class="col-xs-12 col-sm-3 col-md-3"><div class="form-group"><label class="yesstar">Delay for attempt : '+ i +' (In minutes)</label><input type="text" id="'+i+'" class="form-control delay-textbox" name="textBoxVal[]" data-parsley-required="true"></div></div>');
                    }
                }
            });

            $("#outbound_disconnect_max_reschedule_call_attempt").change(function() {
                var selVal = $(this).val();
                $("#listOfTextBoxOutboundDisconnect").html('');
                if(selVal > 0) {
                    for(var i = 1; i<= selVal; i++) {
                        $("#listOfTextBoxOutboundDisconnect").append('<div class="col-xs-12 col-sm-3 col-md-3"><div class="form-group"><label class="yesstar">Outbound disconnect delay for attempt : '+ i +' (In minutes)</label><input type="text" id="outbound_disconnect_schedule_call_delay'+i+'" class="form-control" name="outbound_disconnect_schedule_call_delay['+i+']" data-parsley-required="true"></div></div>');
                    }
                }
            });

            $("#self_tpv_max_no_of_call_attempt").change(function() {
                $("#self_tpv_delay_section").html('');
                var attempt = $(this).val();
                if(attempt > 0) {
                    for(var i = 1; i<= attempt; i++) {
                        $("#self_tpv_delay_section").append('<div class="col-xs-12 col-sm-3 col-md-3"><div class="form-group"><label class="yesstar">Delay for attempt : '+ i +' (In minutes)</label><input type="text" id="self_tpv_call_delay'+i+'" class="form-control" name="self_tpv_call_delay[]" data-parsley-required="true"></div></div>');
                    }
                }
            });


            // For disable some end time options after selecting start time 
            $(".start_time").change(function() {

                var startTime = $(this).val();
                var endTimes = [
                                "09:00 AM", "09:15 AM", "09:30 AM", "09:45 AM",
                                "10:00 AM", "10:15 AM", "10:30 AM", "10:45 AM",
                                "11:00 AM", "11:15 AM", "11:30 AM", "11:45 AM",
                                "12:00 PM", "12:15 PM", "12:30 PM", "12:45 PM",
                                "01:00 PM", "01:15 PM", "01:30 PM", "01:45 PM",
                                "01:00 PM", "01:15 PM", "01:30 PM", "01:45 PM",
                                "02:00 PM", "02:15 PM", "02:30 PM", "02:45 PM",
                                "03:00 PM", "03:15 PM", "03:30 PM", "03:45 PM",
                                "04:00 PM", "04:15 PM", "04:30 PM", "04:45 PM",
                                "05:00 PM", "05:15 PM", "05:30 PM", "05:45 PM",
                                "06:00 PM", "06:15 PM", "06:30 PM", "06:45 PM",
                                "07:00 PM", "07:15 PM", "07:30 PM", "07:45 PM",
                                "08:00 PM"
                                ];
                var d = new Date();
                var startDate = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate() + " " + startTime;

                endTimes.forEach(endTime => {
                    var endDate = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate() + " " + endTime;
                    var newStartDate = new Date(Date.parse(startDate));
                    var newEndDate = new Date(Date.parse(endDate));
                    if (newStartDate >= newEndDate) {
                        // console.log("if: " + endDate);
                        $(".end_time option[value='"+endTime+"']").attr("disabled", "disabled");
                    } else {

                    }
                });
                $('.end_time').trigger('change.select2');
                
            });

            $("#program-settings-form").submit(function (e) {
                e.preventDefault(); // avoid to execute the actual submit of the form.
                $("#program-settings-form").parsley().validate();
                if($("#program-settings-form").parsley().isValid()){
                    var form = $(this);
                    var url = form.attr('action');
                    var formData = form.serializeArray();

                    // append off switch (unchecked) key and default value
                    form.find(':checkbox:not(:checked)').map(function () { 
                    formData.push({ name: this.name, value: this.checked ? this.value : 0 }); 
                    });

                    $.ajax({
                        type: "POST",
                        url: url,
                        data: formData,
                        success: function (response) {
                            location.reload();
                            if (response.status == 'success') {
                                printAjaxSuccessMsg(response.message);
                            } else {
                                printAjaxErrorMsg(response.message);
                            }
                        },
                        error: function (xhr) {
                            if (xhr.status == 422) {
                                printErrorMsgNew(form, xhr.responseJSON.errors);
                            }
                        }
                    });
                }
            });

            function setSettingsForm() {
                $("#program-settings-form")[0].reset();
                $("#program-settings-form").parsley().reset();
                $.ajax({
                    url: "{{route('customFieldProgram.index')}}",
                    data: {client_id : $("#settings-client-id").val()} ,
                    success: function (response) {
                        if (response.status == 'success') {
                            if (response.data != null) {
                                console.log(response.data);
                                let $options = response.data[0];
                                let $keys = Object.keys($options);
                                $keys.forEach(function (item,index, array) {
                                    $('#program-settings-form input[type=text][name="' + item +'"]').val($options[item]);
                                    $('#program-settings-form input[type=number][name="' + item +'"]').val($options[item]);
                                    $('#program-settings-form input[type=checkbox][name="' + item +'"]').prop("checked", $options[item]);
                                })

                                // For TPV now maximum call attempt 
                                $('#tpv_now_max_no_of_call_attempt').val(response.data[0].tpv_now_max_no_of_call_attempt).trigger('change');

                                if (response.data[0].tpv_now_call_delay != null ) {
                                    // open above number of textbox and also fill the value as per db
                                    let delayArray = response.data[0].tpv_now_call_delay.split(',');

                                    $.each(delayArray,function(k,v){
                                        $('#'+(k+1)).attr('value',v);
                                        $('#listOfTextBox').attr('text-count',(k+1));
                                        $('#'+(k+1)).attr('disabled','disabled');
                                    })
                                }

                                // For Outbound Disconnect maximum call attempt
                                $('#outbound_disconnect_max_reschedule_call_attempt').val(response.data[0].outbound_disconnect_max_reschedule_call_attempt).trigger('change');

                                if (response.data[0].outbound_disconnect_schedule_call_delay != null ) {
                                    // open above number of textbox and also fill the value as per db
                                    let delayArray = response.data[0].outbound_disconnect_schedule_call_delay.split(',');

                                    $.each(delayArray,function(ok,ov){
                                        $('#outbound_disconnect_schedule_call_delay'+(ok+1)).val(ov);
                                        $('#outbound_disconnect_schedule_call_delay'+(ok+1)).attr('disabled','disabled');
                                    })
                                }

                               

                                // For Self TPV maximum call attempt 
                                $('#self_tpv_max_no_of_call_attempt').val(response.data[0].self_tpv_max_no_of_call_attempt).trigger('change');

                                if (response.data[0].self_tpv_call_delay != null ) {

                                    let delays = response.data[0].self_tpv_call_delay.split(',');
                                    $.each(delays,function(key,value){
                                        $('#self_tpv_call_delay'+(key+1)).val(value);
                                        $('#self_tpv_call_delay'+(key+1)).attr('disabled','disabled');
                                    })
                                }

                                if (response.data[0].restrict_states_self_tpv_tele != null ) {
                                    let states = response.data[0].restrict_states_self_tpv_tele.split(',');
                                    $('#restrict_states_self_tpv_tele').val(states);
                                    $('#restrict_states_self_tpv_tele').multiselect('reload');
                                    resetMultiSelectDesign();
                                } else {
                                    $('#restrict_states_self_tpv_tele').val([]);
                                }

                                if (response.data[0].restrict_states_self_tpv_d2d != null ) {
                                    let states = response.data[0].restrict_states_self_tpv_d2d.split(',');
                                    $('#restrict_states_self_tpv_d2d').val(states);
                                    $('#restrict_states_self_tpv_d2d').multiselect('reload');
                                    resetMultiSelectDesign();
                                } else {
                                    $('#restrict_states_self_tpv_d2d').val([]);
                                }
                            }
                        } else {
                            printAjaxErrorMsg(response.message);
                        }
                    }
                });
            }

            setSettingsForm();
            $("#program-settings-form :input").not(":button").prop("disabled", true);
            $("#timeZoneRestrictionBtn").prop("disabled", true);
            $(".timeZoneDeleteBtn").prop("disabled", true);
            $("#setting-edit-btn").click(function (e) {
                $(this).hide();
                $("#program-settings-form :input").prop("disabled", false);
                $('.multiselect-with-checkbox .ms-options').css("visibility", "visible");
                $("#setting-save-btn").show();
            });
            $("#setting-cancel-btn").click(function (e) {
                setSettingsForm();
                $("#program-settings-form :input").not(":button").prop("disabled", true);
                $("#timeZoneRestrictionBtn").prop("disabled", true);
                $(".timeZoneDeleteBtn").prop("disabled", true)
                // $("#removetimeZoneRestriction").removeAttr('href');
                $("#setting-save-btn").hide();
                $("#setting-edit-btn").show();
            });

        });

        function resetMultiSelectDesign() {
            $('.multiselect-with-checkbox .ms-options label').append('<span class="checkmark" style="left:10px;top:10px"></span>');
            $('.multiselect-with-checkbox .ms-options label').addClass('custom-checkbox').css('cssText','margin-bottom: 0px !important;');
            if($("#setting-edit-btn").is(":visible")) {
                $('.multiselect-with-checkbox .ms-options').css("visibility", "hidden");
            }
        }
    </script>
@endpush