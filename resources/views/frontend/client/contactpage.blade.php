@extends('layouts.app')
@section('content')

<?php $fields = 0;
$formid = 0;
?>

<style>
    .space-none {
        margin-top: 15px;
    }
    .cont_bx3 .pdlr0 {
        padding-left: 0px;
        padding-right: 0px;
    }
</style>


<div class="tpv-contbx edit-agentinfo">
   <div class="container">
      <div class="row">
         <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="cont_bx3">
               <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx client-new-tabs pdlr0">
                  <div class="client-bg-white">

                     <div class="col-xs-12 col-sm-6 col-md-6 tpv_heading">
                        <h1>{{$client->name}}</h1>
                     </div>
                     
                     <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">

                        <!-- Nav tabs -->
                        <!-- Tab panes -->
                        <div class="tab-content">
                           <!--agent details starts-->
                           <div class="row">
                              <div class="col-xs-12 col-sm-12 col-md-12">
                                 <div class="agent-detailform">

                                    @if($commodity_type =="" )
                                    <div class="center-content">
                                       <a class="btn btn-green text-center commodity_selection" href="?c=GasOrElectric">Electric or Gas</a>
                                       <a class="btn btn-green text-center commodity_selection" href="?c=DualFuel">Dual Fuel</a>
                                    </div>
                                    @endif
                                    <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2">
                                       <input type="hidden" value="{{$client_id}}" id="client_id">
                                       <form method="POST" class="company-contact-form" action="{{ route('client.contactaction',$client_id) }}">
                                          {{ csrf_field() }}
                                          {{ method_field('POST') }}
                                          <div class="row">
                                             <div class="col-xs-12 col-sm-12 col-md-12 ">
                                                @if ($message = Session::get('success'))
                                                <div class="alert alert-success">
                                                   <p>{{ $message }}</p>
                                                </div>
                                                @endif
                                                @if($errors->any())
                                                <div class="alert alert-danger">
                                                   @foreach ($errors->all() as $error)
                                                   <p class="error-single">{{$error}}</p>
                                                   @endforeach
                                                </div>
                                                @endif
                                                <?php
                                                $default_value_with_clone = "";

                                                if (isset($enterd_data['Commodity'])) {

                                                   $default_value_with_clone = $enterd_data['Commodity'];
                                                }

                                                if ($commodity_type == 'DualFuel') {
                                                ?>
                                                   <input type="hidden" id="commodityselector" name="fields[Commodity]" value="Dual Fuel">
                                                <?php
                                                } else if ($commodity_type != "") { ?>
                                                   <div class="form-group required col-xs-12" rel="selectbox">
                                                      <label class="control-label">Commodity </label>
                                                      <select class="form-control validate commodityselector" id="commodityselector" name="fields[Commodity]">
                                                         <option value="">Select</option>
                                                         <option value="Electric" <?php echo ($default_value_with_clone == 'Electric') ? "selected='selected'" : ''  ?>>Electric</option>
                                                         <option value="Gas" <?php echo ($default_value_with_clone == 'Gas') ? "selected='selected'" : ''  ?>>Gas</option>
                                                         <!-- <option value="Dual Fuel" <?php echo ($default_value_with_clone == 'Dual Fuel') ? "selected='selected'" : ''  ?>>Dual Fuel</option>    -->
                                                      </select>
                                                      <span class="invalid-feedback validation-error">
                                                         <strong></strong>
                                                      </span>
                                                   </div>
                                                <?php }
                                                ?>
                                                <div class="rowdd">
                                                   <?php
                                                   $default_value_with_clone = "";

                                                   if (isset($enterd_data['zipcode'])) {

                                                      $default_value_with_clone = $enterd_data['zipcode'];
                                                   }
                                                   if ($commodity_type != "") {
                                                   ?>
                                                      <div class="form-group required col-xs-12 col-md-6 validatezipcode " rel="text">
                                                         <label class="control-label">Enter Zipcode</label>
                                                         <div class="form-group">
                                                            <input type="text" class="form-control zipcodefield" name="fields[zipcode]" placeholder="Please enter zipcode" value="{{$default_value_with_clone}}">
                                                            <span class="form-group-btn">
                                                               <button class="btn btn-default searchzipcode" type="button">Next</button>
                                                            </span>
                                                         </div>
                                                         <!-- /form-group -->
                                                         <span class="invalid-feedback validation-error" style="width: 150%">
                                                            <strong></strong>
                                                         </span>
                                                      </div>
                                                </div>
                                                <?php
                                                      $zipcodestate = "";
                                                      if (isset($enterd_data['zipcodeState'])) {
                                                         $zipcodestate = $enterd_data['zipcodeState'];
                                                      }
                                                ?>
                                                <input type="hidden" name="fields[zipcodeState]" id="zipcodestate" value="{{$zipcodestate}}">
                                                <?php
                                                      $zipcodeCity = "";
                                                      if (isset($enterd_data['zipcodeCity'])) {
                                                         $zipcodestate = $enterd_data['zipcodeCity'];
                                                      }
                                                ?>
                                                <input type="hidden" name="fields[zipcodeCity]" id="zipcodeCity" value="{{$zipcodeCity}}">
                                             <?php
                                                   }

                                                   $filed_name_prefix =  "fields[multiple][0]"; //"fields";

                                                   $display_data = ($default_value_with_clone != "") ? 'block' : 'none'; ?>
                                             <div class="agent-main-form" style=" display: {{$display_data }}; ">
                                                <div class="agent-main-data-wrapper" id="agent-main-data-wrapper">
                                                   <section class="form-section-1">
                                                      @if($commodity_type != 'DualFuel')
                                                      <div class="form-group required col-xs-12 selectutilitywrapper" rel="selectbox">
                                                         <label class="control-label">Select Utility </label>
                                                         <select class="form-control validate required utilityoptions" rel="programoptions" id="utilityoptions" data-ref="ElectricOrGas" data-rel="programoptions" data-idfield="utility_id" name="{{$filed_name_prefix}}[utility]" data-parentelement="agent-main-data-wrapper" data-cname="[utility]">
                                                            <option value="">Select</option>
                                                            @if(count($utilities) > 0)
                                                            @foreach($utilities as $single_utility )
                                                            <option data-id='{{$single_utility->utid}}' data-market='{{$single_utility->market}}' value='{{$single_utility->utilityname}} {{$single_utility->market}}' @if($utility_id !=="" && $utility_id==$single_utility->utid ) selected @endif
                                                               >
                                                               {{$single_utility->utilityname}} {{$single_utility->market}}
                                                               @endforeach
                                                               @endif
                                                         </select>
                                                         <!-- <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                      </div>
                                                      <input type="hidden" name="{{$filed_name_prefix}}[_utilityID]" data-parentelement="agent-main-data-wrapper" data-cname="[_utilityID]" id="utility_id" value="{{$utility_id}}">
                                                      @endif
                                                      <?php
                                                      $default_value_with_clone = "";
                                                      if (isset($enterd_data['Call type'])) {

                                                         $default_value_with_clone = $enterd_data['Call type'];
                                                      }
                                                      ?>
                                                      <input type="hidden" name="calltype" value="inbound" id="Inbound" @if($default_value_with_clone=='inbound' ) checked @endif>
                                                      <!-- <div class="form-group text-center radio-btns flex required form-field-wrapper col-xs-12" rel="radio">
                                                      <label></label>
                                                      <span class="">  
                                                              <label for="Inbound" class="radio-inline" >
                                                              <input type="radio" name="calltype"  value="inbound" id="Inbound" @if($default_value_with_clone == 'inbound' ) checked @endif > <span>Inbound</span></label> 
                                                           
                                                       </span>
                                                       <span class="">  
                                                              <label for="schedule_a_call" class="radio-inline" >
                                                              <input type="radio" name="calltype"  value="schedule_a_call" id="schedule_a_call" @if($default_value_with_clone == 'schedule_a_call' ) checked @endif > <span>Schedule a call</span></label> 
                                                           
                                                       </span>
                                                      <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span>
                                                      </div> -->
                                                      <?php
                                                      if (count($form_all_fields) > 0) {
                                                         $enterd_data = $form_all_fields;
                                                      }

                                                      ?>


                                                      @if(count($ClientFields)> 0)
                                                      <?php  //echo "<pre>"; print_r($ClientFields);  echo "</pre>";
                                                      ?>
                                                      @foreach($ClientFields as $data_fields)
                                                      <?php

                                                      $formid =  $data_fields->id;
                                                      $section = 1;
                                                      $field_count = 1;
                                                      if (!empty($data_fields->form_fields) &&  $data_fields->form_fields != 'null') {
                                                         $fields_array = json_decode($data_fields->form_fields);
                                                         foreach ($fields_array as $single_field) {
                                                            if ($single_field->type == 'separator') {

                                                               $section++;

                                                               echo "
                                                                  <div class='clearfix'></div>
                                                                  <div class='next-button col-sm-12 btnintable bottom_btns' ><div class='btn-group'>";
                                                               if (($section - 1) > 1) {
                                                                  echo "<button type='button' class='contact-previous-step btn btn-purple' data-ref='form-section-" . ($section - 1) . "' data-rel='" . ($section - 2) . "'>Previous <span class='browse'>" . getimage('images/previous_w.png') . "</span> </button> ";
                                                               }

                                                               echo "<button type='button' class='contact-next-step btn btn-green' data-ref='form-section-" . $section . "' data-rel='" . ($section - 1) . "'>Next <span class='add'>" . getimage('images/update_w.png') . "</span></button></div></div>
                                                                  </section><section class='form-section-" . $section . " hide-section'>";
                                                            }

                                                            if (isset($single_field->width)) {
                                                               $width = $single_field->width;
                                                            } else {
                                                               $width = 12;
                                                            }
                                                            $requiredchecked = "";
                                                            $multiselect = "";
                                                            $common_class = " form-field-wrapper col-xs-12 col-sm-{$width}";
                                                            $fields++;
                                                            if (isset($single_field->required)) {
                                                               $requiredchecked = "required";
                                                            }
                                                            if (isset($single_field->multiselect)) {
                                                               $multiselect = "multiple";
                                                            }
                                                            $default_value_with_clone = "";

                                                            if (isset($enterd_data[$single_field->label_text])) {

                                                               $default_value_with_clone = $enterd_data[$single_field->label_text];
                                                            }
                                                            if ($single_field->type == 'heading') {
                                                      ?>
                                                               <h1 class="form-heading-text">{{ $single_field->label_text }}</h1>
                                                               <div class="clearfix"></div>
                                                            <?php
                                                            }
                                                            if ($single_field->type == 'text') {
                                                               $data_validator = "";
                                                               if (strtolower($single_field->label_text) == 'email' || strtolower($single_field->label_text) == 'email for reward programs') {
                                                                  $data_validator = 'data-validator=ServiceEmail';
                                                               }
                                                               if ($commodity_type == 'DualFuel') {
                                                                  $data_validator .= " data-commodity=Electric";
                                                               }

                                                            ?>
                                                               <div class="form-group {{ $requiredchecked }} {{$common_class}} " rel="text">
                                                                  <label class="control-label">{{ $single_field->label_text }}</label>
                                                                  <input type="text" class="form-control validate" autocomplete="off" name="{{$filed_name_prefix}}[{{ $single_field->label_text }}]" data-parentelement="agent-main-data-wrapper" data-cname="[{{ $single_field->label_text }}]" value="{{$default_value_with_clone}}" placeholder="{{$single_field->placeholder_text}}" {{$data_validator}}>
                                                                  <!--  <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                            <?php }
                                                            if ($single_field->type == 'textarea') {
                                                            ?>
                                                               <div class="form-group {{ $requiredchecked }} {{$common_class}}" rel="textarea">
                                                                  <label class="control-label">{{ $single_field->label_text }}</label>
                                                                  <textarea class="form-control validate" name="{{$filed_name_prefix}}[{{ $single_field->label_text }}]" placeholder="{{$single_field->placeholder_text}}" data-parentelement="agent-main-data-wrapper" data-cname="[{{ $single_field->label_text }}]">{{$default_value_with_clone}}</textarea>
                                                                  <!-- <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                            <?php }
                                                            if ($single_field->type == 'checkbox') {
                                                            ?>
                                                               <div class="form-group {{ $requiredchecked }} {{$common_class}}" rel="checkbox">
                                                                  <label class="control-label">{{ $single_field->label_text }}</label>
                                                                  <?php
                                                                  $selected_checkbox = (isset($single_field->options->selected)) ? $single_field->options->selected : array();
                                                                  foreach ($single_field->options->label as $checkboxoptions) {
                                                                     if (in_array($checkboxoptions, $selected_checkbox)) {
                                                                        $checked = "checked";
                                                                     } else {
                                                                        $checked = "";
                                                                     }
                                                                     if ($default_value_with_clone == $checkboxoptions) {
                                                                        $checked = "checked";
                                                                     }
                                                                  ?>
                                                                     <div class="checkbox ">
                                                                        <label class="checkbx-style">{{$checkboxoptions}}<input type="checkbox" name="{{$filed_name_prefix}}[{{ $single_field->label_text }}][]" {{$checked}} value="{{$checkboxoptions}}" data-parentelement="agent-main-data-wrapper" data-cname="[{{ $single_field->label_text }}][]"><span class="checkmark"></span></label>
                                                                     </div>
                                                                  <?php }
                                                                  ?>
                                                                  <!--   <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                            <?php }
                                                            if ($single_field->type == 'radio') { ?>
                                                               <div class="form-group text-center radio-btns flex {{ $requiredchecked }} {{$common_class}} <?php if (isset($single_field->button_css)) {
                                                                                                                                                               echo "button-radio-wrapper";
                                                                                                                                                            } ?>" rel="radio">
                                                                  <label>{{ $single_field->label_text }}</label>
                                                                  <span class="<?php if (isset($single_field->button_css)) {
                                                                                    echo "bussiness_btn";
                                                                                 } ?>">
                                                                     <?php
                                                                     $selected_checkbox = (isset($single_field->options->selected)) ? $single_field->options->selected : "";
                                                                     foreach ($single_field->options->label as $checkboxoptions) {
                                                                        if ($checkboxoptions == $selected_checkbox) {
                                                                           $checked = "checked";
                                                                        } else {
                                                                           $checked = "";
                                                                        }
                                                                        if ($default_value_with_clone == $checkboxoptions) {
                                                                           $checked = "checked";
                                                                        }
                                                                     ?>
                                                                        <?php $copy_address = "";
                                                                        $change_id = ""; ?>
                                                                        @if('Is the billing address the same as the service address' == $single_field->label_text )
                                                                        <?php $copy_address = 'data-copybillingaddress="copyaddress"';
                                                                        $change_id = 'data-changeid="change"'; ?>
                                                                        @endif

                                                                        @if('Is billing name same as authorized name?' == $single_field->label_text )
                                                                        <?php $copy_address = 'data-copyauthorizename="copyauthtobillname"';
                                                                        $change_id = 'data-changeid="change"'; ?>
                                                                        @endif
                                                                        @if('Is gas billing name same as authorized name?' == $single_field->label_text )
                                                                        <?php $copy_address = 'data-copygasauthtobillname="copygasauthtobillname"';
                                                                        $change_id = 'data-changeid="change"'; ?>
                                                                        @endif
                                                                        @if('Is electric billing name same as authorized name?' == $single_field->label_text )
                                                                        <?php $copy_address = 'data-copyelectricauthtobillname="copyelectricauthtobillname"';
                                                                        $change_id = 'data-changeid="change"'; ?>
                                                                        @endif
                                                                        @if('Electric service address same as Gas service address?' == $single_field->label_text )
                                                                        <?php $copy_address = 'data-copyelectricserviceaddress="copyelectricserviceaddress"';
                                                                        $change_id = 'data-changeid="change"'; ?>
                                                                        @endif


                                                                        <label for="{{$checkboxoptions}}" {{$change_id}} class="radio-inline">
                                                                           <input type="radio" name="{{$filed_name_prefix}}[{{ $single_field->label_text }}]" {{$checked}} data-parentelement="agent-main-data-wrapper" data-cname="[{{ $single_field->label_text }}]" {{$copy_address}} value="{{$checkboxoptions}}" id="{{$checkboxoptions}}"> <span>{{$checkboxoptions}}</span></label>
                                                                     <?php } ?>
                                                                  </span>
                                                                  <!--   <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                            <?php }
                                                            if ($single_field->type == 'selectbox') {
                                                               $relationship_class = "";
                                                               if (strtolower($single_field->label_text) == 'relationship') {
                                                                  $relationship_class = " relationship-selectbox";
                                                               }
                                                            ?>
                                                               <div class="form-group {{ $requiredchecked }} {{$common_class}}" rel="selectbox">
                                                                  <label class="control-label">{{ $single_field->label_text }}</label>
                                                                  <select class="form-control validate {{$relationship_class}}" {{$multiselect}} name="{{$filed_name_prefix}}[{{ $single_field->label_text }}]" data-parentelement="agent-main-data-wrapper" data-cname="[{{ $single_field->label_text }}]">
                                                                     <option value="">Select</option>
                                                                     <?php
                                                                     foreach ($single_field->options->label as $selectoptions) { ?>
                                                                        <option @if($default_value_with_clone==$selectoptions) selected @endif><?php echo $selectoptions; ?></option>
                                                                     <?php }
                                                                     ?>
                                                                  </select>
                                                                  <!-- <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                            <?php }
                                                            if ($single_field->type == 'serviceaddress') {
                                                               $disabled = "";
                                                               if (count($zipcodes) > 0) {
                                                                  $disabled = " disabled";
                                                               }
                                                               $address_values = explode(',', $default_value_with_clone);
                                                               $state = "";
                                                               $city = "";
                                                               $zip = "";
                                                               $addr1 = "";
                                                               $addr2 = "";
                                                               if (count($address_values) > 0) {
                                                                  if (isset($enterd_data['ServiceState']))
                                                                     $state = $enterd_data['ServiceState'];
                                                                  if (isset($enterd_data['ServiceCity']))
                                                                     $city = $enterd_data['ServiceCity'];
                                                                  if (isset($enterd_data['ServiceZip']))
                                                                     $zip = $enterd_data['ServiceZip'];

                                                                  if (isset($enterd_data['ServiceAddress']))
                                                                     $addr1 =  $enterd_data['ServiceAddress'];
                                                                  if (isset($enterd_data['ServiceAddress2']))
                                                                     $addr2 =  $enterd_data['ServiceAddress2'];
                                                               }


                                                            ?>
                                                               <div class="form-group address-fields-wrapper {{ $requiredchecked }} {{$common_class}}" rel="address">
                                                                  <label class="control-label">{{ $single_field->label_text }}</label>
                                                                  <div class="row">
                                                                     <div class="col-sm-4 address-field inline-block">
                                                                        <input class="form-control zipautocomplete zipcodeall address{{ $requiredchecked }}" autocomplete="off" value="{{$zip}}" Placeholder="Zipcode" name="{{$filed_name_prefix}}[ServiceZip]" data-parentelement="agent-main-data-wrapper" data-cname="[ServiceZip]" type="text" data-validator="ServiceZip" data-commodity="Electric">
                                                                     </div>
                                                                     <div class="col-sm-4 address-field inline-block">
                                                                        <input autocomplete="off" {{$disabled}} class="form-control cityall address{{ $requiredchecked }} cityfield" @if($disabled=="" ) name="{{$filed_name_prefix}}[ServiceCity]" data-parentelement="agent-main-data-wrapper" data-cname="[ServiceCity]" @endif Placeholder="City" type="text" value="{{$city}}" id="service_addresscity" autocomplete="off" data-validator="ServiceCity" data-commodity="Electric">
                                                                        @if($disabled !="")
                                                                        <input type="hidden" value="{{$city}}" name="{{$filed_name_prefix}}[ServiceCity]" class="cityfield cityall" data-parentelement="agent-main-data-wrapper" data-cname="[ServiceCity]" data-validator="ServiceCity">
                                                                        @endif
                                                                     </div>
                                                                     <div class="col-sm-4 address-field inline-block pull-right">
                                                                        <input {{$disabled}} {{ $requiredchecked }} id="service_addressstate" class="form-control stateall statefield address{{ $requiredchecked }}" Placeholder="State" value="{{$state}}" @if($disabled=="" ) name="{{$filed_name_prefix}}[ServiceState]" data-parentelement="agent-main-data-wrapper" data-cname="[ServiceState]" @endif type="text" autocomplete="off" data-validator="ServiceState" data-commodity="Electric">
                                                                        @if($disabled !="")
                                                                        <input type="hidden" name="{{$filed_name_prefix}}[ServiceState]" value="{{$state}}" class="statefield stateall" data-parentelement="agent-main-data-wrapper" data-cname="[ServiceState]" data-validator="ServiceState">
                                                                        @endif
                                                                     </div>
                                                                     <div class="col-sm-12 address-field">
                                                                        <input autocomplete="off" id="autocompletestreet_{{$field_count}}" data-ref="{{$field_count}}" class="form-control autocompletestreet address{{ $requiredchecked }}" Placeholder="Address 1" name="{{$filed_name_prefix}}[ServiceAddress]" type="text" value="{{$addr1}}" data-parentelement="agent-main-data-wrapper" data-cname="[ServiceAddress]" data-validator="ServiceAddress1" data-commodity="Electric">
                                                                     </div>
                                                                     <div class="col-sm-12 address-field">
                                                                        <input autocomplete="off" id="autocompletestreet2_{{$field_count}}" data-ref="{{$field_count}}" class="form-control autocompletestreet address" Placeholder="Address 2" name="{{$filed_name_prefix}}[ServiceAddress2]" type="text" value="{{$addr2}}" data-parentelement="agent-main-data-wrapper" data-cname="[ServiceAddress2]" data-validator="ServiceAddress2" data-commodity="Electric">
                                                                     </div>
                                                                     <!-- <div class="col-sm-12 address-field">
                                                            <input autocomplete="off" class="form-control" Placeholder="Address 2" name="fields[{{ $single_field->label_text }}][1]" type="text" value="{{$addr2}}">
                                                            </div> -->
                                                                  </div>
                                                                  <!-- <div class="google_map" id="google_map_{{$field_count}}" data-ref="{{$field_count}}" style="height:200px;width:100%"></div> -->
                                                                  <!--  <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                            <?php }
                                                            if ($single_field->type == 'gasserviceaddress') {
                                                               $disabled = "";
                                                               if (count($zipcodes) > 0) {
                                                                  $disabled = " disabled";
                                                               }
                                                               $address_values = explode(',', $default_value_with_clone);
                                                               $state = "";
                                                               $city = "";
                                                               $zip = "";
                                                               $addr1 = "";
                                                               $addr2 = "";
                                                               if (count($address_values) > 0) {
                                                                  if (isset($enterd_data['GasServiceState']))
                                                                     $state = $enterd_data['GasServiceState'];
                                                                  if (isset($enterd_data['GasServiceCity']))
                                                                     $city = $enterd_data['GasServiceCity'];
                                                                  if (isset($enterd_data['GasServiceZip']))
                                                                     $zip = $enterd_data['GasServiceZip'];

                                                                  if (isset($enterd_data['GasServiceAddress']))
                                                                     $addr1 =  $enterd_data['GasServiceAddress'];
                                                                  if (isset($enterd_data['GasServiceAddress2']))
                                                                     $addr2 =  $enterd_data['GasServiceAddress2'];
                                                               }


                                                            ?>
                                                               <div class="form-group address-fields-wrapper {{ $requiredchecked }} {{$common_class}}" rel="address">
                                                                  <label class="control-label">{{ $single_field->label_text }}</label>
                                                                  <div class="row">
                                                                     <div class="col-sm-4 address-field inline-block">
                                                                        <input class="form-control zipautocomplete zipcodeall address{{ $requiredchecked }}" autocomplete="off" value="{{$zip}}" Placeholder="Zipcode" name="{{$filed_name_prefix}}[GasServiceZip]" data-parentelement="agent-main-data-wrapper" data-cname="[GasServiceZip]" type="text" data-validator="ServiceZip" data-commodity="Gas">
                                                                     </div>
                                                                     <div class="col-sm-4 address-field inline-block">
                                                                        <input autocomplete="off" {{$disabled}} class="form-control address{{ $requiredchecked }} cityfield cityall" @if($disabled=="" ) name="{{$filed_name_prefix}}[GasServiceCity]" data-parentelement="agent-main-data-wrapper" data-cname="[GasServiceCity]" @endif Placeholder="City" type="text" value="{{$city}}" id="Gasservice_addresscity" autocomplete="off" data-validator="ServiceCity" data-commodity="Gas">
                                                                        @if($disabled !="")
                                                                        <input type="hidden" value="{{$city}}" name="{{$filed_name_prefix}}[GasServiceCity]" class="cityfield" data-parentelement="agent-main-data-wrapper" data-cname="[GasServiceCity]" data-commodity="Gas">
                                                                        @endif
                                                                     </div>
                                                                     <div class="col-sm-4 address-field inline-block pull-right">
                                                                        <input {{$disabled}} {{ $requiredchecked }} id="Gasservice_addressstate" class="form-control statefield stateall address{{ $requiredchecked }}" Placeholder="State" value="{{$state}}" @if($disabled=="" ) name="{{$filed_name_prefix}}[GasServiceState]" data-parentelement="agent-main-data-wrapper" data-cname="[GasServiceState]" @endif type="text" autocomplete="off" data-validator="ServiceState" data-commodity="Gas">
                                                                        @if($disabled !="")
                                                                        <input type="hidden" name="{{$filed_name_prefix}}[GasServiceState]" value="{{$state}}" class="statefield stateall" data-parentelement="agent-main-data-wrapper" data-cname="[GasServiceState]" data-commodity="Gas">
                                                                        @endif
                                                                     </div>
                                                                     <div class="col-sm-12 address-field">
                                                                        <input autocomplete="off" id="autocompletestreet_{{$field_count}}" data-ref="{{$field_count}}" class="form-control autocompletestreet address{{ $requiredchecked }}" Placeholder="Address 1" name="{{$filed_name_prefix}}[GasServiceAddress]" data-parentelement="agent-main-data-wrapper" data-cname="[GasServiceAddress]" type="text" value="{{$addr1}}" data-validator="ServiceAddress1" data-commodity="Gas">
                                                                     </div>
                                                                     <div class="col-sm-12 address-field">
                                                                        <input autocomplete="off" id="autocompletestreet2_{{$field_count}}" data-ref="{{$field_count}}" class="form-control autocompletestreet address" Placeholder="Address 2" name="{{$filed_name_prefix}}[GasServiceAddress2]" data-parentelement="agent-main-data-wrapper" data-cname="[GasServiceAddress2]" type="text" value="{{$addr2}}" data-validator="ServiceAddress2" data-commodity="Gas">
                                                                     </div>
                                                                  </div>
                                                                  <!--   <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                            <?php }
                                                            if ($single_field->type == 'electricserviceaddress') {
                                                               $disabled = "";
                                                               if (count($zipcodes) > 0) {
                                                                  $disabled = " disabled";
                                                               }
                                                               $address_values = explode(',', $default_value_with_clone);
                                                               $state = "";
                                                               $city = "";
                                                               $zip = "";
                                                               $addr1 = "";
                                                               $addr2 = "";
                                                               if (count($address_values) > 0) {
                                                                  if (isset($enterd_data['electricServiceState']))
                                                                     $state = $enterd_data['electricServiceState'];
                                                                  if (isset($enterd_data['electricServiceCity']))
                                                                     $city = $enterd_data['electricServiceCity'];
                                                                  if (isset($enterd_data['electricServiceZip']))
                                                                     $zip = $enterd_data['electricServiceZip'];

                                                                  if (isset($enterd_data['electricServiceAddress']))
                                                                     $addr1 =  $enterd_data['electricServiceAddress'];
                                                                  if (isset($enterd_data['electricServiceAddress2']))
                                                                     $addr2 =  $enterd_data['electricServiceAddress2'];
                                                               }


                                                            ?>
                                                               <div class="form-group address-fields-wrapper {{ $requiredchecked }} {{$common_class}}" rel="address">
                                                                  <label class="control-label">{{ $single_field->label_text }}</label>
                                                                  <div class="row">
                                                                     <div class="col-sm-4 address-field inline-block">
                                                                        <input class="form-control zipautocomplete zipcodeall address{{ $requiredchecked }}" autocomplete="off" value="{{$zip}}" Placeholder="Zipcode" name="{{$filed_name_prefix}}[electricServiceZip]" data-parentelement="agent-main-data-wrapper" data-cname="[electricServiceZip]" type="text" data-validator="ServiceZip" data-commodity="Electric">
                                                                     </div>
                                                                     <div class="col-sm-4 address-field inline-block">
                                                                        <input autocomplete="off" {{$disabled}} class="form-control cityall address{{ $requiredchecked }} cityfield" @if($disabled=="" ) name="{{$filed_name_prefix}}[electricServiceCity]" @endif Placeholder="City" type="text" value="{{$city}}" id="electricservice_addresscity" autocomplete="off" data-parentelement="agent-main-data-wrapper" data-cname="[electricServiceCity]" data-validator="ServiceCity" data-commodity="Electric">
                                                                        @if($disabled !="")
                                                                        <input type="hidden" value="{{$city}}" name="{{$filed_name_prefix}}[electricServiceCity]" class="cityfield cityall" data-parentelement="agent-main-data-wrapper" data-cname="[electricServiceCity]">
                                                                        @endif
                                                                     </div>
                                                                     <div class="col-sm-4 address-field inline-block pull-right">
                                                                        <input {{$disabled}} {{ $requiredchecked }} id="electricservice_addressstate" class="form-control statefield stateall address{{ $requiredchecked }}" Placeholder="State" value="{{$state}}" @if($disabled=="" ) name="{{$filed_name_prefix}}[ServiceState]" data-parentelement="agent-main-data-wrapper" data-cname="[ServiceState]" @endif type="text" autocomplete="off" data-validator="ServiceState" data-commodity="Electric">
                                                                        @if($disabled !="")
                                                                        <input type="hidden" name="{{$filed_name_prefix}}[electricServiceState]" value="{{$state}}" class="statefield stateall" data-parentelement="agent-main-data-wrapper" data-cname="[electricServiceState]">
                                                                        @endif
                                                                     </div>
                                                                     <div class="col-sm-12 address-field">
                                                                        <input autocomplete="off" id="autocompletestreet_{{$field_count}}" data-ref="{{$field_count}}" class="form-control autocompletestreet address{{ $requiredchecked }}" Placeholder="Address 1" name="{{$filed_name_prefix}}[electricServiceAddress]" type="text" value="{{$addr1}}" data-parentelement="agent-main-data-wrapper" data-cname="[electricServiceAddress]" data-validator="ServiceAddress1" data-commodity="Electric">
                                                                     </div>
                                                                     <div class="col-sm-12 address-field">
                                                                        <input autocomplete="off" id="autocompletestreet2_{{$field_count}}" data-ref="{{$field_count}}" class="form-control autocompletestreet address" Placeholder="Address 2" name="{{$filed_name_prefix}}[electricServiceAddress2]" type="text" value="{{$addr2}}" data-parentelement="agent-main-data-wrapper" data-cname="[electricServiceAddress2]" data-validator="ServiceAddress2" data-commodity="Electric">
                                                                     </div>
                                                                  </div>
                                                                  <!-- <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                            <?php }
                                                            if ($single_field->type == 'billingaddress') {
                                                               //  print_r($default_value_with_clone);

                                                               $address_values = explode(',', $default_value_with_clone);
                                                               $state = "";
                                                               $city = "";
                                                               $zip = "";
                                                               $addr1 = "";
                                                               $addr2 = "";
                                                               if (count($address_values) > 0) {
                                                                  if (isset($enterd_data['BillingState']))
                                                                     $state = $enterd_data['BillingState'];
                                                                  if (isset($enterd_data['BillingCity']))
                                                                     $city = $enterd_data['BillingCity'];
                                                                  if (isset($enterd_data['BillingZip']))
                                                                     $zip = $enterd_data['BillingZip'];

                                                                  if (isset($enterd_data['BillingAddress']))
                                                                     $addr1 =  $enterd_data['BillingAddress'];
                                                                  if (isset($enterd_data['BillingAddress2']))
                                                                     $addr2 =  $enterd_data['BillingAddress2'];
                                                               }
                                                            ?>
                                                               <div class="form-group address-fields-wrapper {{ $requiredchecked }} {{$common_class}}" rel="address">
                                                                  <label class="control-label">{{ $single_field->label_text }}</label>
                                                                  <div class="row">
                                                                     <div class="col-sm-12 address-field">
                                                                        <input class="form-control address{{ $requiredchecked }}" Placeholder="Address 1" name="{{$filed_name_prefix}}[BillingAddress]" data-parentelement="agent-main-data-wrapper" data-cname="[BillingAddress]" value="{{$addr1}}" type="text" autocomplete="off" data-validator="BillingAddress1">
                                                                     </div>
                                                                     <div class="col-sm-12 address-field">
                                                                        <input class="form-control address" Placeholder="Address 2" name="{{$filed_name_prefix}}[BillingAddress2]" data-parentelement="agent-main-data-wrapper" data-cname="[BillingAddress2]" value="{{$addr2}}" type="text" autocomplete="off" data-validator="BillingAddress2">
                                                                     </div>
                                                                     <div class="col-sm-4 address-field inline-block">
                                                                        <input class="form-control zipcodeall1 address{{ $requiredchecked }}" Placeholder="Zipcode" name="{{$filed_name_prefix}}[BillingZip]" data-parentelement="agent-main-data-wrapper" data-cname="[BillingZip]" type="text" value="{{$zip}}" autocomplete="off" data-validator="BillingZip">
                                                                     </div>
                                                                     <div class="col-sm-3 address-field inline-block">
                                                                        <input class="form-control cityall1 address{{ $requiredchecked }}" Placeholder="City" name="{{$filed_name_prefix}}[BillingCity]" data-parentelement="agent-main-data-wrapper" data-cname="[BillingCity]" type="text" value="{{$city}}" autocomplete="off" data-validator="BillingCity">
                                                                     </div>
                                                                     <div class="col-sm-5 address-field inline-block pull-right">
                                                                        <input class="form-control stateall1 address{{ $requiredchecked }}" Placeholder="State" name="{{$filed_name_prefix}}[BillingState]" data-parentelement="agent-main-data-wrapper" data-cname="[BillingState]" type="text" value="{{$state}}" autocomplete="off" data-validator="BillingState">
                                                                     </div>
                                                                  </div>
                                                                  <!--  <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                            <?php }
                                                            if ($single_field->type == 'gasbillingaddress') {
                                                               //  print_r($default_value_with_clone);

                                                               $address_values = explode(',', $default_value_with_clone);
                                                               $state = "";
                                                               $city = "";
                                                               $zip = "";
                                                               $addr1 = "";
                                                               $addr2 = "";
                                                               if (count($address_values) > 0) {
                                                                  if (isset($enterd_data['GasBillingState']))
                                                                     $state = $enterd_data['GasBillingState'];
                                                                  if (isset($enterd_data['GasBillingCity']))
                                                                     $city = $enterd_data['GasBillingCity'];
                                                                  if (isset($enterd_data['GasBillingZip']))
                                                                     $zip = $enterd_data['GasBillingZip'];

                                                                  if (isset($enterd_data['GasBillingAddress']))
                                                                     $addr1 =  $enterd_data['GasBillingAddress'];
                                                                  if (isset($enterd_data['GasBillingAddress2']))
                                                                     $addr2 =  $enterd_data['GasBillingAddress2'];
                                                               }
                                                            ?>
                                                               <div class="form-group address-fields-wrapper {{ $requiredchecked }} {{$common_class}}" rel="address">
                                                                  <label class="control-label">{{ $single_field->label_text }}</label>
                                                                  <div class="row">
                                                                     <div class="col-sm-12 address-field">
                                                                        <input class="form-control address{{ $requiredchecked }}" Placeholder="Address 1" name="{{$filed_name_prefix}}[GasBillingAddress]" data-parentelement="agent-main-data-wrapper" data-cname="[GasBillingAddress]" value="{{$addr1}}" type="text" autocomplete="off" data-validator="BillingAddress1" data-commodity="Gas">
                                                                     </div>
                                                                     <div class="col-sm-12 address-field">
                                                                        <input class="form-control address" Placeholder="Address 2" name="{{$filed_name_prefix}}[GasBillingAddress2]" data-parentelement="agent-main-data-wrapper" data-cname="[GasBillingAddress2]" value="{{$addr2}}" type="text" autocomplete="off" data-validator="BillingAddress2" data-commodity="Gas">
                                                                     </div>
                                                                     <div class="col-sm-4 address-field inline-block">
                                                                        <input class="form-control zipcodeall1 address{{ $requiredchecked }}" Placeholder="Zipcode" name="{{$filed_name_prefix}}[GasBillingZip]" data-parentelement="agent-main-data-wrapper" data-cname="[GasBillingZip]" type="text" value="{{$zip}}" autocomplete="off" data-validator="BillingZip" data-commodity="Gas">
                                                                     </div>
                                                                     <div class="col-sm-3 address-field inline-block">
                                                                        <input class="form-control cityall1 address{{ $requiredchecked }}" Placeholder="City" name="{{$filed_name_prefix}}[GasBillingCity]" data-parentelement="agent-main-data-wrapper" data-cname="[GasBillingCity]" type="text" value="{{$city}}" autocomplete="off" data-validator="BillingCity" data-commodity="Gas">
                                                                     </div>
                                                                     <div class="col-sm-5 address-field inline-block pull-right">
                                                                        <input class="form-control stateall1 address{{ $requiredchecked }}" Placeholder="State" name="{{$filed_name_prefix}}[GasBillingState]" data-parentelement="agent-main-data-wrapper" data-cname="[GasBillingState]" type="text" value="{{$state}}" autocomplete="off" data-validator="BillingState" data-commodity="Gas">
                                                                     </div>
                                                                  </div>
                                                                  <!--  <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                            <?php }
                                                            if ($single_field->type == 'electricbillingaddress') {
                                                               //  print_r($default_value_with_clone);

                                                               $address_values = explode(',', $default_value_with_clone);
                                                               $state = "";
                                                               $city = "";
                                                               $zip = "";
                                                               $addr1 = "";
                                                               $addr2 = "";
                                                               if (count($address_values) > 0) {
                                                                  if (isset($enterd_data['ElectricBillingState']))
                                                                     $state = $enterd_data['ElectricBillingState'];
                                                                  if (isset($enterd_data['ElectricBillingCity']))
                                                                     $city = $enterd_data['ElectricBillingCity'];
                                                                  if (isset($enterd_data['ElectricBillingZip']))
                                                                     $zip = $enterd_data['ElectricBillingZip'];

                                                                  if (isset($enterd_data['ElectricBillingAddress']))
                                                                     $addr1 =  $enterd_data['ElectricBillingAddress'];
                                                                  if (isset($enterd_data['ElectricBillingAddress2']))
                                                                     $addr2 =  $enterd_data['ElectricBillingAddres2'];
                                                               }
                                                            ?>
                                                               <div class="form-group address-fields-wrapper {{ $requiredchecked }} {{$common_class}}" rel="address">
                                                                  <label class="control-label">{{ $single_field->label_text }}</label>
                                                                  <div class="row">
                                                                     <div class="col-sm-12 address-field">
                                                                        <input class="form-control address{{ $requiredchecked }}" Placeholder="Address 1" name="{{$filed_name_prefix}}[ElectricBillingAddress]" data-parentelement="agent-main-data-wrapper" data-cname="[ElectricBillingAddress]" value="{{$addr1}}" type="text" autocomplete="off" data-commodity="Electric" data-validator="BillingAddress1">
                                                                     </div>
                                                                     <div class="col-sm-12 address-field">
                                                                        <input class="form-control address" Placeholder="Address 2" name="{{$filed_name_prefix}}[ElectricBillingAddress2]" data-parentelement="agent-main-data-wrapper" data-cname="[ElectricBillingAddress2]" value="{{$addr2}}" type="text" autocomplete="off" data-commodity="Electric" data-validator="BillingAddress2">
                                                                     </div>
                                                                     <div class="col-sm-4 address-field inline-block">
                                                                        <input class="form-control zipcodeall1 address{{ $requiredchecked }}" Placeholder="Zipcode" name="{{$filed_name_prefix}}[ElectricBillingZip]" data-parentelement="agent-main-data-wrapper" data-cname="[ElectricBillingZip]" type="text" value="{{$zip}}" autocomplete="off" data-commodity="Electric" data-validator="BillingZip">
                                                                     </div>
                                                                     <div class="col-sm-3 address-field inline-block">
                                                                        <input class="form-control cityall1 address{{ $requiredchecked }}" Placeholder="City" name="{{$filed_name_prefix}}[ElectricBillingCity]" data-parentelement="agent-main-data-wrapper" data-cname="[ElectricBillingCity]" type="text" value="{{$city}}" autocomplete="off" data-validator="BillingCity" data-commodity="Electric">
                                                                     </div>
                                                                     <div class="col-sm-5 address-field inline-block pull-right">
                                                                        <input class="form-control stateall1 address{{ $requiredchecked }}" Placeholder="State" name="{{$filed_name_prefix}}[ElectricBillingState]" data-parentelement="agent-main-data-wrapper" data-cname="[ElectricBillingState]" type="text" value="{{$state}}" autocomplete="off" data-commodity="Electric" data-validator="BillingState">
                                                                     </div>
                                                                  </div>
                                                                  <!-- <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                            <?php }
                                                            if ($single_field->type == 'address') {
                                                               $address_values = explode(',', $default_value_with_clone);
                                                               $state = "";
                                                               $city = "";
                                                               $zip = "";
                                                               $addr1 = "";
                                                               $addr2 = "";
                                                               if (count($address_values) > 0) {
                                                                  if (isset($address_values[count($address_values) - 1]))
                                                                     $state = $address_values[count($address_values) - 1];
                                                                  if (isset($address_values[count($address_values) - 2]))
                                                                     $city = $address_values[count($address_values) - 2];
                                                                  if (isset($address_values[count($address_values) - 3]))
                                                                     $zip = $address_values[count($address_values) - 3];
                                                                  if (isset($address_values[count($address_values) - 4]))
                                                                     $addr2 = $address_values[count($address_values) - 4];
                                                                  if (isset($address_values[count($address_values) - 5]))
                                                                     $addr1 = $address_values[count($address_values) - 5];
                                                               }
                                                            ?>
                                                               <div class="form-group address-fields-wrapper {{ $requiredchecked }} {{$common_class}}" rel="address">
                                                                  <label class="control-label">{{ $single_field->label_text }}</label>
                                                                  <div class="row">
                                                                     <div class="col-sm-12 address-field">
                                                                        <input class="form-control address{{ $requiredchecked }}" Placeholder="Address 1" name="{{$filed_name_prefix}}[{{ $single_field->label_text }}][]" data-parentelement="agent-main-data-wrapper" data-cname="[{{ $single_field->label_text }}][]" value="{{$addr1}}" type="text" autocomplete="off">
                                                                     </div>
                                                                     <div class="col-sm-12 address-field">
                                                                        <input class="form-control" Placeholder="Address 2" name="{{$filed_name_prefix}}[{{ $single_field->label_text }}][]" data-parentelement="agent-main-data-wrapper" data-cname="[{{ $single_field->label_text }}][]" type="text" value="{{$addr2}}" autocomplete="off">
                                                                     </div>
                                                                     <div class="col-sm-4 address-field inline-block">
                                                                        <input class="form-control address{{ $requiredchecked }}" Placeholder="Zipcode" name="{{$filed_name_prefix}}[{{ $single_field->label_text }}][]" data-parentelement="agent-main-data-wrapper" data-cname="[{{ $single_field->label_text }}][]" type="text" value="{{$zip}}" autocomplete="off">
                                                                     </div>
                                                                     <div class="col-sm-3 address-field inline-block">
                                                                        <input class="form-control address{{ $requiredchecked }}" Placeholder="City" name="{{$filed_name_prefix}}[{{ $single_field->label_text }}][]" data-parentelement="agent-main-data-wrapper" data-cname="[{{ $single_field->label_text }}][]" type="text" value="{{$city}}" autocomplete="off">
                                                                     </div>
                                                                     <div class="col-sm-5 address-field inline-block pull-right">
                                                                        <input class="form-control address{{ $requiredchecked }}" Placeholder="State" name="{{$filed_name_prefix}}[{{ $single_field->label_text }}][]" data-parentelement="agent-main-data-wrapper" data-cname="[{{ $single_field->label_text }}][]" type="text" value="{{$state}}" autocomplete="off">
                                                                     </div>
                                                                  </div>
                                                                  <!-- <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                            <?php }
                                                            if ($single_field->type == 'name') {
                                                            ?>
                                                               <?php
                                                               $default_value_with_clone = "";

                                                               if (isset($enterd_data['First name']) &&  $enterd_data['First name'] != "") {

                                                                  $default_value_with_clone = $enterd_data['First name'];
                                                               }
                                                               if (isset($enterd_data['Authorized First name']) && $enterd_data['Authorized First name'] != "") {

                                                                  $default_value_with_clone = $enterd_data['Authorized First name'];
                                                               }
                                                               ?>
                                                               <div class="form-group name-wrapper {{ $requiredchecked }} {{$common_class}}" rel="name">
                                                                  <!-- <label class="control-label">Full name</label> -->
                                                                  <div class="clearfix"></div>
                                                                  <div class="row">
                                                                     <div class="col-sm-4   name-field">
                                                                        <label class="control-label">Authorized First name</label>
                                                                        <input value="{{$default_value_with_clone}}" class="form-control firstname" Placeholder="First name" name="{{$filed_name_prefix}}[Authorized First name]" data-parentelement="agent-main-data-wrapper" data-cname="[Authorized First name]" type="text" autocomplete="off" data-validator="ServiceFirstName" data-commodity="Electric">
                                                                     </div>
                                                                     <?php
                                                                     $default_value_with_clone = "";

                                                                     if (isset($enterd_data['Middle initial']) && $enterd_data['Middle initial'] != "") {

                                                                        $default_value_with_clone = $enterd_data['Middle initial'];
                                                                     }
                                                                     if (isset($enterd_data['Authorized Middle initial']) && $enterd_data['Authorized Middle initial'] != "") {

                                                                        $default_value_with_clone = $enterd_data['Authorized Middle initial'];
                                                                     }
                                                                     ?>
                                                                     <div class="col-sm-4   name-field">
                                                                        <label class="control-label">Authorized Middle initial</label>
                                                                        <input maxlength="1" value="{{$default_value_with_clone}}" class="form-control firstname" Placeholder="Middle initial" name="{{$filed_name_prefix}}[Authorized Middle initial]" data-parentelement="agent-main-data-wrapper" data-cname="[Authorized Middle initial]" type="text" autocomplete="off">
                                                                     </div>
                                                                     <?php
                                                                     $default_value_with_clone = "";

                                                                     if (isset($enterd_data['Last name']) && $enterd_data['Last name'] != "") {

                                                                        $default_value_with_clone = $enterd_data['Last name'];
                                                                     }
                                                                     if (isset($enterd_data['Authorized Last name']) && $enterd_data['Authorized Last name'] != "") {

                                                                        $default_value_with_clone = $enterd_data['Authorized Last name'];
                                                                     }
                                                                     ?>
                                                                     <div class="col-sm-4   name-field">
                                                                        <label class="control-label">Authorized Last name</label>
                                                                        <input value="{{$default_value_with_clone}}" class="form-control firstname" Placeholder="Last name" name="{{$filed_name_prefix}}[Authorized Last name]" data-parentelement="agent-main-data-wrapper" data-cname="[Authorized Last name]" type="text" autocomplete="off" data-validator="ServiceLastName" data-commodity="Electric">
                                                                     </div>
                                                                  </div>
                                                                  <!-- <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                            <?php }
                                                            if ($single_field->type == 'billingname') {
                                                               $default_value_with_clone = "";

                                                               if (isset($enterd_data['Billing first name'])) {

                                                                  $default_value_with_clone = $enterd_data['Billing first name'];
                                                               }
                                                            ?>
                                                               <div class="form-group name-wrapper {{ $requiredchecked }} {{$common_class}}" rel="name">
                                                                  <!-- <label class="control-label">Billing Full name</label>   -->
                                                                  <div class="clearfix"></div>
                                                                  <div class="row">
                                                                     <div class="col-sm-5 no-padding name-field">
                                                                        <label class="control-label">Billing First name</label>
                                                                        <input value="{{$default_value_with_clone}}" class="form-control firstname" Placeholder="Billing first name" name="{{$filed_name_prefix}}[Billing first name]" data-parentelement="agent-main-data-wrapper" data-cname="[Billing first name]" type="text" autocomplete="off" data-validator="BillingFirstName" data-commodity="Electric">
                                                                     </div>
                                                                     <?php
                                                                     $default_value_with_clone = "";

                                                                     if (isset($enterd_data['Billing middle name'])) {

                                                                        $default_value_with_clone = $enterd_data['Billing middle name'];
                                                                     }
                                                                     ?>
                                                                     <div class="col-sm-2 no-padding name-field">
                                                                        <label class="control-label">Middle initial</label>
                                                                        <input maxlength="1" value="{{$default_value_with_clone}}" class="form-control firstname" Placeholder="Middle initial" name="{{$filed_name_prefix}}[Billing middle name]" data-parentelement="agent-main-data-wrapper" data-cname="[Billing middle name]" type="text" autocomplete="off">
                                                                     </div>
                                                                     <?php
                                                                     $default_value_with_clone = "";

                                                                     if (isset($enterd_data['Billing last name'])) {

                                                                        $default_value_with_clone = $enterd_data['Billing last name'];
                                                                     }
                                                                     ?>
                                                                     <div class="col-sm-5 no-padding name-field">
                                                                        <label class="control-label">Billing Last name</label>
                                                                        <input value="{{$default_value_with_clone}}" class="form-control firstname" Placeholder="Billing last name" name="{{$filed_name_prefix}}[Billing last name]" data-parentelement="agent-main-data-wrapper" data-cname="[Billing last name]" type="text" autocomplete="off" data-validator="BillingLastName" data-commodity="Electric">
                                                                     </div>
                                                                  </div>
                                                                  <span class="invalid-feedback validation-error">
                                                                     <strong></strong>
                                                                  </span>
                                                               </div>
                                                            <?php }
                                                            if ($single_field->type == 'gasbillingname') {


                                                            ?>
                                                               <?php
                                                               $default_value_with_clone = "";

                                                               if (isset($enterd_data['Gas Billing first name'])) {

                                                                  $default_value_with_clone = $enterd_data['Gas Billing first name'];
                                                               }
                                                               ?>
                                                               <div class="form-group name-wrapper {{ $requiredchecked }} {{$common_class}}" rel="name">
                                                                  <!-- <label class="control-label">Billing Full name</label>   -->
                                                                  <div class="clearfix"></div>
                                                                  <div class="row">
                                                                     <div class="col-sm-5 no-padding name-field">
                                                                        <label class="control-label">Gas Billing First name</label>
                                                                        <input value="{{$default_value_with_clone}}" class="form-control firstname" Placeholder="Billing first name" name="{{$filed_name_prefix}}[Gas Billing first name]" data-parentelement="agent-main-data-wrapper" data-cname="[Gas Billing first name]" type="text" autocomplete="off" data-validator="BillingFirstName" data-commodity="Gas">
                                                                     </div>
                                                                     <?php
                                                                     $default_value_with_clone = "";

                                                                     if (isset($enterd_data['Gas Billing middle name'])) {

                                                                        $default_value_with_clone = $enterd_data['Gas Billing middle name'];
                                                                     }
                                                                     ?>
                                                                     <div class="col-sm-2 no-padding name-field">
                                                                        <label class="control-label">Middle initial</label>
                                                                        <input maxlength="1" value="{{$default_value_with_clone}}" class="form-control firstname" Placeholder="Middle initial" name="{{$filed_name_prefix}}[Gas Billing middle name]" data-parentelement="agent-main-data-wrapper" data-cname="[Gas Billing middle name]" type="text" autocomplete="off">
                                                                     </div>
                                                                     <?php
                                                                     $default_value_with_clone = "";

                                                                     if (isset($enterd_data['Gas Billing last name'])) {

                                                                        $default_value_with_clone = $enterd_data['Gas Billing last name'];
                                                                     }
                                                                     ?>
                                                                     <div class="col-sm-5 no-padding name-field">
                                                                        <label class="control-label">Gas Billing Last name</label>
                                                                        <input value="{{$default_value_with_clone}}" class="form-control firstname" Placeholder="Billing last name" name="{{$filed_name_prefix}}[Gas Billing last name]" data-parentelement="agent-main-data-wrapper" data-cname="[Gas Billing last name]" type="text" autocomplete="off" data-validator="BillingLastName" data-commodity="Gas">
                                                                     </div>
                                                                  </div>
                                                                  <!-- <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                            <?php }
                                                            if ($single_field->type == 'electricbillingname') {


                                                            ?>
                                                               <?php
                                                               $default_value_with_clone = "";

                                                               if (isset($enterd_data['Electric Billing first name'])) {

                                                                  $default_value_with_clone = $enterd_data['Electric Billing first name'];
                                                               }
                                                               ?>
                                                               <div class="form-group name-wrapper {{ $requiredchecked }} {{$common_class}}" rel="name">
                                                                  <!-- <label class="control-label">Billing Full name</label>   -->
                                                                  <div class="clearfix"></div>
                                                                  <div class="row">
                                                                     <div class="col-sm-5 no-padding name-field">
                                                                        <label class="control-label">Electric Billing First name</label>
                                                                        <input value="{{$default_value_with_clone}}" class="form-control firstname" Placeholder="Billing first name" name="{{$filed_name_prefix}}[Electric Billing first name]" data-parentelement="agent-main-data-wrapper" data-cname="[Electric Billing first name]" type="text" autocomplete="off" data-validator="BillingFirstName" data-commodity="Electric">
                                                                     </div>
                                                                     <?php
                                                                     $default_value_with_clone = "";

                                                                     if (isset($enterd_data['Electric Billing middle name'])) {

                                                                        $default_value_with_clone = $enterd_data['Electric Billing middle name'];
                                                                     }
                                                                     ?>
                                                                     <div class="col-sm-2 no-padding name-field">
                                                                        <label class="control-label">Middle initial</label>
                                                                        <input maxlength="1" value="{{$default_value_with_clone}}" class="form-control firstname" Placeholder="Middle initial" name="{{$filed_name_prefix}}[Electric Billing middle name]" data-parentelement="agent-main-data-wrapper" data-cname="[Electric Billing middle name]" type="text" autocomplete="off">
                                                                     </div>
                                                                     <?php
                                                                     $default_value_with_clone = "";

                                                                     if (isset($enterd_data['Electric Billing last name'])) {

                                                                        $default_value_with_clone = $enterd_data['Electric Billing last name'];
                                                                     }
                                                                     ?>
                                                                     <div class="col-sm-5 no-padding name-field">
                                                                        <label class="control-label">Electric Billing Last name</label>
                                                                        <input value="{{$default_value_with_clone}}" class="form-control firstname" Placeholder="Billing last name" name="{{$filed_name_prefix}}[Electric Billing last name]" data-parentelement="agent-main-data-wrapper" data-cname="[Electric Billing last name]" type="text" autocomplete="off" data-validator="BillingLastName" data-commodity="Electric">
                                                                     </div>
                                                                  </div>
                                                                  <!-- <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                            <?php }
                                                            if ($single_field->type == 'authname') {
                                                            ?>
                                                               <div class="form-group name-wrapper {{ $requiredchecked }} {{$common_class}}" rel="name">
                                                                  <label class="control-label">{{ $single_field->label_text }}</label>
                                                                  <div class="clearfix"></div>
                                                                  <div class="row">
                                                                     <div class="col-sm-12 name-field no-padding">
                                                                        <input class="form-control" Placeholder="{{ $single_field->label_text }}" name="{{$filed_name_prefix}}[{{ $single_field->label_text }}]" data-parentelement="agent-main-data-wrapper" data-cname="[{{ $single_field->label_text }}]" value="{{$default_value_with_clone}}" type="text" autocomplete="off">
                                                                     </div>
                                                                  </div>
                                                                  <!-- <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                            <?php }
                                                            if ($single_field->type == 'phonenumber') {
                                                            ?>
                                                               <div class="form-group {{ $requiredchecked }} {{$common_class}}" rel="phonenumber">
                                                                  <label class="control-label">{{ $single_field->label_text }}</label>
                                                                  <div class="clearfix"></div>
                                                                  <div class="row">
                                                                     <div class="col-sm-12 no-padding ">
                                                                        <input value="{{$default_value_with_clone}}" class="form-control phonenumber contact-number-format" Placeholder="" name="{{$filed_name_prefix}}[{{$single_field->label_text}}]" data-parentelement="agent-main-data-wrapper" data-cname="[{{$single_field->label_text}}]" type="text" maxlength="14" autocomplete="off" data-validator="ServicePhone" data-commodity="Electric">
                                                                     </div>
                                                                  </div>
                                                                  <!--    <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                            <?php }
                                                            if ($single_field->type == 'electricutility') {
                                                            ?>
                                                               <div class="electricity_utility col-sm-12">
                                                                  <div class="form-group required electricity_utility_wrapper" rel="selectbox">
                                                                     <label class="control-label">Select Electric Utility</label>
                                                                     <select class="select2 form-control validate required utilityoptions" data-parsley-errors-container="#select2-electricutility-error-message" id="electricutilityoptions" data-ref="Electric" data-rel="electricprogramoptions{{$field_count}}" name="{{$filed_name_prefix}}[electricutility]" data-parentelement="agent-main-data-wrapper" data-cname="[electricutility]" data-idfield="electricutility_id" data-validator="electricutility" data-commodity='Electric'>
                                                                        <option value="">Select</option>
                                                                        @if(count($electricutilities) > 0)
                                                                        @foreach($electricutilities as $single_utility )
                                                                        <option data-id='{{$single_utility->utid}}' data-market='{{$single_utility->market}}' value='{{$single_utility->utilityname}} {{$single_utility->market}}' @if($electricutility_id !=="" && $electricutility_id==$single_utility->utid ) selected @endif
                                                                           >
                                                                           {{$single_utility->utilityname}} {{$single_utility->market}}
                                                                           @endforeach
                                                                           @endif
                                                                     </select>
                                                                     <span id="select2-electricutility-error-message"></span>
                                                                     <!--   <span class="invalid-feedback validation-error">
                                                         <strong></strong>
                                                         </span> -->
                                                                  </div>
                                                                  <input type="hidden" name="{{$filed_name_prefix}}[_electricutilityID]" data-parentelement="agent-main-data-wrapper" data-cname="[_electricutilityID]" id="electricutility_id" value="{{$electricutility_id}}">
                                                               </div>
                                                               <?php $account_reference_number = rand(); ?>
                                                               <div class="form-group required col-xs-12 " rel="selectbox">
                                                                  <label class="control-label ">Select Electric Program</label>
                                                                  <select name="{{$filed_name_prefix}}[ElectricProgram]" data-parentelement="agent-main-data-wrapper" data-cname="[ElectricProgram]" id="electricprogramoptions{{$field_count}}" data-ref="Electric" class=" selectprogram validate form-control  form-field-wrapper col-xs-12" rel="selectbox" data-idfield="electricprogramid" data-rate="electric_program_rate" data-term="electric_program_term" data-msf="electric_program_msf" data-etf="electric_program_etf" data-prodetail="programdetail_{{$account_reference_number}}" data-account="accountnumber_{{$account_reference_number}}" data-validator="electricprogram" data-commodity='Electric'>
                                                                     <option value="">Select</option>
                                                                     @if( count($electricprograms) > 0 )
                                                                     @foreach( $electricprograms as $program)
                                                                     <option value="{{$program['name']}}" data-programname="{{$program['name']}}" data-code="{{$program['code']}}" data-rate="{{$program['rate']}}" data-etf="{{$program['etf']}}" data-msf="{{$program['msf']}}" data-term="{{$program['term']}}" data-accounttype="{{$program['accountnumbertype']}}" data-customertype="{{$program['customer_type']}}" data-termtype="{{$program['termtype']}}" data-unitofmeasure="{{$program['unit_of_measure']}}" data-id="{{$program['id']}}" data-accountlength="{{$program['accountnumberlength']}}" data-accountnumbertype="{{$program['accountnumbertype']}}" @if($electricprogram_id!="" && $electricprogram_id==$program['id'] ) selected @endif>
                                                                        {{$program['name']}} ( code: {{$program['code']}}, rate:{{$program['rate']}}, etf: {{$program['etf']}}, msf:{{$program['msf']}}, term:{{$program['term']}})
                                                                     </option>
                                                                     @endforeach
                                                                     @endif
                                                                  </select>
                                                                  <!--  <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                               <input type="hidden" name="{{$filed_name_prefix}}[_electricprogramID]" data-parentelement="agent-main-data-wrapper" data-cname="[_electricprogramID]" value="{{$electricprogram_id}}" class="electricprogramid" id="electricprogramid">
                                                               <input type="hidden" name="{{$filed_name_prefix}}[electric_MarketCode]" data-parentelement="agent-main-data-wrapper" data-cname="[electric_MarketCode]" value="{{ getDataEnteredValue($enterd_data, 'electric_MarketCode')}}" id="electric_MarketCode">
                                                               <input type="hidden" name="{{$filed_name_prefix}}[client]" data-parentelement="agent-main-data-wrapper" data-cname="[client]" value="{{$client->name}}">
                                                               <input type="hidden" name="{{$filed_name_prefix}}[electric_rate]" data-parentelement="agent-main-data-wrapper" data-cname="[electric_rate]" value="{{ getDataEnteredValue($enterd_data, 'electric_rate')}}" id="electric_program_rate">
                                                               <input type="hidden" name="{{$filed_name_prefix}}[electric_term]" data-parentelement="agent-main-data-wrapper" data-cname="[electric_term]" value="{{ getDataEnteredValue($enterd_data, 'electric_term')}}" id="electric_program_term">
                                                               <input type="hidden" name="{{$filed_name_prefix}}[electric_msf]" data-parentelement="agent-main-data-wrapper" data-cname="[electric_msf]" value="{{ getDataEnteredValue($enterd_data, 'electric_msf')}}" id="electric_program_msf">
                                                               <input type="hidden" name="{{$filed_name_prefix}}[electric_etf]" data-parentelement="agent-main-data-wrapper" data-cname="[electric_etf]" value="{{ getDataEnteredValue($enterd_data, 'electric_etf')}}" id="electric_program_etf">
                                                               <div class="form-group name-wrapper programdetail_{{$account_reference_number}} {{$common_class}}" rel="utility_program_detail">
                                                                  <label class="control-label">Program Details:</label>
                                                                  <?php
                                                                  if ($electricprogram_detail) {
                                                                     $account_number_length = $electricprogram_detail->accountnumberlength ?>
                                                                     <div class="green-text program_detail">
                                                                        Utility:{{$electricprogram_detail->code}}, Rate:{{$electricprogram_detail->rate}},ETF: {{$electricprogram_detail->etf}} , MSF:{{$electricprogram_detail->msf}}
                                                                     </div>
                                                                  <?php } else {
                                                                     $account_number_length = "";
                                                                  ?>
                                                                     <div class="green-text program_detail">
                                                                        Utility:, Rate:,ETF: , MSF:
                                                                     </div>
                                                                  <?php } ?>
                                                                  <span class="invalid-feedback validation-error">
                                                                     <strong></strong>
                                                                  </span>
                                                               </div>
                                                               <div class="form-group name-wrapper required accountnumber_{{$account_reference_number}} {{$common_class}}" rel="account_number">
                                                                  <?php
                                                                  $account_number = "";
                                                                  if (isset($enterd_data['Electric Account Number'])) {

                                                                     $account_number = $enterd_data['Electric Account Number'];
                                                                  }

                                                                  ?>
                                                                  <label class="control-label"><span class="account_number">Account Number <span class="account_number_type_view"></span> </span></label>
                                                                  <input type="text" class="form-control validate account_number_field" name="{{$filed_name_prefix}}[Electric Account Number]" data-parentelement="agent-main-data-wrapper" data-cname="[Electric Account Number]" value="{{$account_number}}" data-ref="<?php echo $account_reference_number; ?>" data-nofdigit="{{$account_number_length}}" autocomplete="off" data-validator="UtilityAccountNumber" data-commodity="Electric">
                                                                  <!-- <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                               <div class="form-group name-wrapper  electric-nstar-fields {{$common_class}}" rel="agent_name_key">
                                                                  <?php
                                                                  $rand = rand();
                                                                  $AgentNameKey = "";
                                                                  if (isset($enterd_data['Electric Agent Name Key'])) {

                                                                     $AgentNameKey = $enterd_data['Electric Agent Name Key'];
                                                                  }

                                                                  ?>
                                                                  <label class="control-label"><span class="agentnamekey">Agent Name Key</span></label>
                                                                  <input type="text" class="form-control validate" name="{{$filed_name_prefix}}[Electric Agent Name Key]" data-parentelement="agent-main-data-wrapper" data-cname="[Electric Agent Name Key]" value="{{$AgentNameKey}}" data-ref="<?php echo $rand; ?>" maxlength="4" data-nofdigit="4" autocomplete="off">
                                                                  <span class="invalid-feedback validation-error">
                                                                     <strong></strong>
                                                                  </span>
                                                               </div>
                                                               <div class="form-group name-wrapper required gas-nstar-fields {{$common_class}}" rel="service_reference_id">
                                                                  <?php
                                                                  $rand = rand();
                                                                  $agentreferencename = "";
                                                                  if (isset($enterd_data['Electric Service Reference Id'])) {

                                                                     $agentreferencename = $enterd_data['Electric Service Reference Id'];
                                                                  }  ?>
                                                                  <label class="control-label"><span class="agentnamekey">Service Reference Id<span class="service_reference_id"></span></span></label>
                                                                  <input type="text" class="form-control validate" name="{{$filed_name_prefix}}[Electric Service Reference Id]" data-parentelement="agent-main-data-wrapper" data-cname="[Electric Service Reference Id]" value="{{$agentreferencename}}" data-ref="<?php echo $rand; ?>" maxlength="9" data-nofdigit="9" autocomplete="off">
                                                                  <!-- <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                            <?php
                                                            }
                                                            if ($single_field->type == 'gasutility') {
                                                            ?>
                                                               <?php $account_reference_number = rand(); ?>
                                                               <div class="gasutility col-sm-12">
                                                                  <div class="form-group required gasutility_wrapper" rel="selectbox">
                                                                     <label class="control-label">Select Gas Utility </label>
                                                                     <select class="form-control validate required utilityoptions" id="gasutilityoptions" name="{{$filed_name_prefix}}[gasutility]" data-parentelement="agent-main-data-wrapper" data-cname="[gasutility]" data-ref="Gas" data-rel="gasprogramoptions{{$field_count}}" data-idfield="gasutility_id" data-validator="gasutility" data-commodity='Gas'>
                                                                        <option value="">Select</option>
                                                                        @if(count($gasutilities) > 0)
                                                                        @foreach($gasutilities as $single_utility )
                                                                        <option data-id='{{$single_utility->utid}}' data-market='{{$single_utility->market}}' value='{{$single_utility->utilityname}} {{$single_utility->market}}' @if($gasutility_id !=="" && $gasutility_id==$single_utility->utid ) selected @endif
                                                                           >
                                                                           {{$single_utility->utilityname}} {{$single_utility->market}}
                                                                           @endforeach
                                                                           @endif
                                                                     </select>
                                                                     <!-- <span class="invalid-feedback validation-error">
                                                         <strong></strong>
                                                         </span> -->
                                                                  </div>
                                                                  <input type="hidden" name="{{$filed_name_prefix}}[_gasutilityID]" data-parentelement="agent-main-data-wrapper" data-cname="[_gasutilityID]" id="gasutility_id" value="{{$utility_id}}">
                                                                  <input type="hidden" name="{{$filed_name_prefix}}[gas_MarketCode]" data-parentelement="agent-main-data-wrapper" data-cname="[gas_MarketCode]" value="" id="gas_MarketCode">
                                                                  <input type="hidden" name="{{$filed_name_prefix}}[client]" data-parentelement="agent-main-data-wrapper" data-cname="[client]" value="{{$client->name}}">
                                                                  <input type="hidden" name="{{$filed_name_prefix}}[gas_rate]" data-parentelement="agent-main-data-wrapper" data-cname="[gas_rate]" value="{{ getDataEnteredValue($enterd_data, 'gas_rate')}}" id="gas_program_rate">
                                                                  <input type="hidden" name="{{$filed_name_prefix}}[gas_term]" data-parentelement="agent-main-data-wrapper" data-cname="[gas_term]" value="{{ getDataEnteredValue($enterd_data, 'gas_term')}}" id="gas_program_term">
                                                                  <input type="hidden" name="{{$filed_name_prefix}}[gas_msf]" data-parentelement="agent-main-data-wrapper" data-cname="[gas_msf]" value="{{ getDataEnteredValue($enterd_data, 'gas_msf')}}" id="gas_program_msf">
                                                                  <input type="hidden" name="{{$filed_name_prefix}}[gas_etf]" data-parentelement="agent-main-data-wrapper" data-cname="[gas_etf]" value="{{ getDataEnteredValue($enterd_data, 'gas_etf')}}" id="gas_program_etf">
                                                               </div>
                                                               <div class="form-group required col-xs-12" rel="selectbox">
                                                                  <label class="control-label">Select Gas Program</label>
                                                                  <select name="{{$filed_name_prefix}}[GasProgram]" data-parentelement="agent-main-data-wrapper" data-cname="[GasProgram]" id="gasprogramoptions{{$field_count}}" data-ref="Gas" class=" selectprogram validate  form-field-wrapper form-control col-xs-12" rel="selectbox" data-idfield="gasprogramid" data-rate="gas_program_rate" data-term="gas_program_term" data-msf="gas_program_msf" data-etf="gas_program_etf" data-prodetail="programdetail_{{$account_reference_number}}" data-account="accountnumber_{{$account_reference_number}}" data-validator="gasprogram" data-commodity='Gas'>
                                                                     <option value="">Select</option>
                                                                     @if( count($gasprograms) > 0 )
                                                                     @foreach( $gasprograms as $program)
                                                                     <option value="{{$program['name']}}" data-programname="{{$program['name']}}" data-code="{{$program['code']}}" data-rate="{{$program['rate']}}" data-etf="{{$program['etf']}}" data-msf="{{$program['msf']}}" data-term="{{$program['term']}}" data-accounttype="{{$program['accountnumbertype']}}" data-customertype="{{$program['customer_type']}}" data-termtype="{{$program['termtype']}}" data-unitofmeasure="{{$program['unit_of_measure']}}" data-id="{{$program['id']}}" data-accountlength="{{$program['accountnumberlength']}}" data-accountnumbertype="{{$program['accountnumbertype']}}" @if($gasprogram_id!="" && $gasprogram_id==$program['id'] ) selected @endif>
                                                                        {{$program['name']}} ( code: {{$program['code']}}, rate:{{$program['rate']}}, etf: {{$program['etf']}}, msf:{{$program['msf']}}, term:{{$program['term']}})
                                                                     </option>
                                                                     @endforeach
                                                                     @endif
                                                                  </select>
                                                                  <!--  <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                               <input type="hidden" name="{{$filed_name_prefix}}[_gasprogramID]" data-parentelement="agent-main-data-wrapper" data-cname="[_gasprogramID]" value="{{$gasprogram_id}}" class="gasprogramid" id="gasprogramid">
                                                               <div class="form-group name-wrapper programdetail_{{$account_reference_number}} {{$common_class}}" rel="utility_program_detail">
                                                                  <label class="control-label">Program Details:</label>
                                                                  <?php
                                                                  if ($gasprogram_detail) {
                                                                     $account_number_length = $gasprogram_detail->accountnumberlength ?>
                                                                     <div class="green-text program_detail">
                                                                        Utility:{{$gasprogram_detail->code}}, Rate:{{$gasprogram_detail->rate}},ETF: {{$gasprogram_detail->etf}} , MSF:{{$gasprogram_detail->msf}}
                                                                     </div>
                                                                  <?php } else {
                                                                     $account_number_length = "";
                                                                  ?>
                                                                     <div class="green-text program_detail">
                                                                        Utility:, Rate:,ETF: , MSF:
                                                                     </div>
                                                                  <?php } ?>
                                                                  <span class="invalid-feedback validation-error">
                                                                     <strong></strong>
                                                                  </span>
                                                               </div>
                                                               <div class="form-group name-wrapper required accountnumber_{{$account_reference_number}} {{$common_class}}" rel="account_number">
                                                                  <?php
                                                                  $account_number = "";
                                                                  if (isset($enterd_data['Gas Account Number'])) {

                                                                     $account_number = $enterd_data['Gas Account Number'];
                                                                  }

                                                                  ?>
                                                                  <label class="control-label"><span class="account_number">Account Number <span class="account_number_type_view"></span></span></label>
                                                                  <input type="text" class="form-control validate account_number_field" name="{{$filed_name_prefix}}[Gas Account Number]" data-parentelement="agent-main-data-wrapper" data-cname="[Gas Account Number]" value="{{$account_number}}" data-ref="<?php echo $account_reference_number; ?>" data-nofdigit="{{$account_number_length}}" autocomplete="off" data-validator="UtilityAccountNumber" data-commodity="Gas">
                                                                  <!-- <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                               <div class="form-group name-wrapper  gas-nstar-fields {{$common_class}}" rel="agent_name_key">
                                                                  <?php
                                                                  $rand = rand();
                                                                  $AgentNameKey = "";
                                                                  if (isset($enterd_data['Gas Agent Name Key'])) {

                                                                     $AgentNameKey = $enterd_data['Gas Agent Name Key'];
                                                                  }

                                                                  ?>
                                                                  <label class="control-label"><span class="agentnamekey">Agent Name Key</span></label>
                                                                  <input type="text" class="form-control validate" name="{{$filed_name_prefix}}[Gas Agent Name Key]" data-parentelement="agent-main-data-wrapper" data-cname="[Gas Agent Name Key]" value="{{$AgentNameKey}}" data-ref="<?php echo $rand; ?>" maxlength="4" data-nofdigit="4" autocomplete="off">
                                                                  <span class="invalid-feedback validation-error">
                                                                     <strong></strong>
                                                                  </span>
                                                               </div>
                                                               <div class="form-group name-wrapper  gas-nstar-fields {{$common_class}}" rel="service_reference_id">
                                                                  <?php
                                                                  $rand = rand();
                                                                  $agentreferencename = "";
                                                                  if (isset($enterd_data['Service Reference Id'])) {

                                                                     $agentreferencename = $enterd_data['Service Reference Id'];
                                                                  }  ?>
                                                                  <label class="control-label"><span class="agentnamekey">Service Reference Id<span class="service_reference_id"></span></span></label>
                                                                  <input type="text" class="form-control validate" name="{{$filed_name_prefix}}[Gas Service Reference Id]" data-parentelement="agent-main-data-wrapper" data-cname="[Gas Service Reference Id]" value="{{$agentreferencename}}" data-ref="<?php echo $rand; ?>" maxlength="9" data-nofdigit="9" autocomplete="off">
                                                                  <!-- <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                               </div>
                                                            <?php } ?>
                                                      <?php
                                                            $field_count++;
                                                         }
                                                      }

                                                      ?>
                                                      @endforeach
                                                      @endif
                                                      @if($commodity_type != 'DualFuel')
                                                      <?php
                                                      $default_value_with_clone = "";
                                                      if (isset($enterd_data['program'])) {

                                                         $default_value_with_clone = $enterd_data['program'];
                                                      }
                                                      ?>
                                                      <?php $account_reference_number = rand(); ?>
                                                      <div class="clearfix"></div>
                                                      <div class="form-group required col-xs-12" rel="selectbox">
                                                         <label class="control-label">Select Program</label>
                                                         <select name="{{$filed_name_prefix}}[Program]" data-parentelement="agent-main-data-wrapper" data-cname="[Program]" id="programoptions" data-ref="ElectricOrGas" class=" selectprogram form-control validate  form-field-wrapper fullwidth " rel="selectbox" data-idfield="programid" data-rate="program_rate" data-term="program_term" data-msf="program_msf" data-etf="program_etf" data-prodetail="programdetail_{{$account_reference_number}}" data-account="accountnumber_{{$account_reference_number}}" data-validator="program">
                                                            <option value="">Select</option>
                                                            @if( count($programs) > 0 )
                                                            @foreach( $programs as $program)
                                                            <option value="{{$program['name']}}" data-programname="{{$program['name']}}" data-code="{{$program['code']}}" data-rate="{{$program['rate']}}" data-etf="{{$program['etf']}}" data-msf="{{$program['msf']}}" data-term="{{$program['term']}}" data-accounttype="{{$program['accountnumbertype']}}" data-customertype="{{$program['customer_type']}}" data-termtype="{{$program['termtype']}}" data-unitofmeasure="{{$program['unit_of_measure']}}" data-id="{{$program['id']}}" data-accountlength="{{$program['accountnumberlength']}}" data-accountnumbertype="{{$program['accountnumbertype']}}" @if($program_id!="" && $program_id==$program['id'] ) selected @endif>
                                                               {{$program['name']}} ( code: {{$program['code']}}, rate:{{$program['rate']}}, etf: {{$program['etf']}}, msf:{{$program['msf']}}, term:{{$program['term']}})
                                                            </option>
                                                            @endforeach
                                                            @endif
                                                         </select>
                                                         <!--  <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                      </div>
                                                      <input type="hidden" name="{{$filed_name_prefix}}[_programID]" data-parentelement="agent-main-data-wrapper" data-cname="[_programID]" value="{{$program_id}}" class="programid" id="programid">
                                                      <input type="hidden" name="{{$filed_name_prefix}}[Account Number Length]" data-parentelement="agent-main-data-wrapper" data-cname="[Account Number Length]" value="{{ getDataEnteredValue($enterd_data, 'Account Number Length')}}" class="account_number_length">
                                                      <input type="hidden" name="{{$filed_name_prefix}}[Account Number Type]" data-parentelement="agent-main-data-wrapper" data-cname="[Account Number Type]" value="{{ getDataEnteredValue($enterd_data, 'Account Number Type')}}" class="account_number_type">
                                                      <input type="hidden" name="{{$filed_name_prefix}}[Program Code]" data-parentelement="agent-main-data-wrapper" data-cname="[Program Code]" value="{{ getDataEnteredValue($enterd_data, 'Program Code')}}" class="program_code">

                                                      <input type="hidden" name="{{$filed_name_prefix}}[Gas Program Code]" data-parentelement="agent-main-data-wrapper" data-cname="[Gas Program Code]" value="{{ getDataEnteredValue($enterd_data, 'Gas Program Code')}}" class="gas_program_code">

                                                      <input type="hidden" name="{{$filed_name_prefix}}[Electric Program Code]" data-parentelement="agent-main-data-wrapper" data-cname="[Electric Program Code]" value="{{ getDataEnteredValue($enterd_data, 'Electric Program Code')}}" class="electric_program_code">

                                                      <input type="hidden" name="{{$filed_name_prefix}}[UDCAccountCode]" data-parentelement="agent-main-data-wrapper" data-cname="[UDCAccountCode]" value="{{ getDataEnteredValue($enterd_data, 'UDCAccountCode')}}" id="UDCAccountCode">
                                                      <input type="hidden" name="{{$filed_name_prefix}}[UDC Company name]" data-parentelement="agent-main-data-wrapper" data-cname="[UDC Company name]" value="{{ getDataEnteredValue($enterd_data, 'UDC Company name')}}" class="udccompanyname">
                                                      <input type="hidden" name="{{$filed_name_prefix}}[UDC account name]" data-parentelement="agent-main-data-wrapper" data-cname="[UDC account name]" value="{{ getDataEnteredValue($enterd_data, 'UDC account name')}}" class="udcaccountname">
                                                      <input type="hidden" name="{{$filed_name_prefix}}[MarketCode]" data-parentelement="agent-main-data-wrapper" data-cname="[MarketCode]" value="{{ getDataEnteredValue($enterd_data, 'MarketCode')}}" id="MarketCode">
                                                      <input type="hidden" name="{{$filed_name_prefix}}[client]" data-parentelement="agent-main-data-wrapper" data-cname="[client]" value="{{ getDataEnteredValue($enterd_data, 'client')}}{{$client->name}}">
                                                      <input type="hidden" name="{{$filed_name_prefix}}[rate]" data-parentelement="agent-main-data-wrapper" data-cname="[rate]" value="{{ getDataEnteredValue($enterd_data, 'rate')}}" id="program_rate">
                                                      <input type="hidden" name="{{$filed_name_prefix}}[term]" data-parentelement="agent-main-data-wrapper" data-cname="[term]" value="{{ getDataEnteredValue($enterd_data, 'term')}}" id="program_term">
                                                      <input type="hidden" name="{{$filed_name_prefix}}[msf]" data-parentelement="agent-main-data-wrapper" data-cname="[msf]" value="{{ getDataEnteredValue($enterd_data, 'msf')}}" id="program_msf">
                                                      <input type="hidden" name="{{$filed_name_prefix}}[etf]" data-parentelement="agent-main-data-wrapper" data-cname="[etf]" value="{{ getDataEnteredValue($enterd_data, 'etf')}}" id="program_etf">
                                                      <?php $common_class = " form-field-wrapper col-xs-12 col-sm-12"; ?>
                                                      <div class="form-group name-wrapper programdetail_{{$account_reference_number}} {{$common_class}}" rel="utility_program_detail">
                                                         <label class="control-label">Program Details:</label>
                                                         <?php
                                                         if ($program_detail) {
                                                            $account_number_length = $program_detail->accountnumberlength ?>
                                                            <div class="green-text program_detail">
                                                               Utility:{{$program_detail->code}}, Rate:{{$program_detail->rate}},ETF: {{$program_detail->etf}} , MSF:{{$program_detail->msf}}
                                                            </div>
                                                         <?php } else {
                                                            $account_number_length = "";
                                                         ?>
                                                            <div class="green-text program_detail">
                                                               Utility:, Rate:,ETF: , MSF:
                                                            </div>
                                                         <?php } ?>
                                                         <!-- <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                      </div>
                                                      <div class="form-group name-wrapper required accountnumber_{{$account_reference_number}} {{$common_class}}" rel="account_number">
                                                         <?php
                                                         $account_number = "";
                                                         if (isset($enterd_data['Account Number'])) {

                                                            $account_number = $enterd_data['Account Number'];
                                                         }

                                                         ?>
                                                         <label class="control-label"><span class="account_number">Account Number <span class="account_number_type_view"></span></span></label>
                                                         <input type="text" class="form-control validate account_number_field" name="{{$filed_name_prefix}}[Account Number]" data-parentelement="agent-main-data-wrapper" data-cname="[Account Number]" value="{{$account_number}}" data-ref="<?php echo $account_reference_number; ?>" data-nofdigit="{{$account_number_length}}" data-validator="UtilityAccountNumber" data-commodity="Electric" autocomplete="off">
                                                         <!--  <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                      </div>

                                                      <!-- NSTAR fields -->

                                                      <div class="form-group name-wrapper  nstar-fields {{$common_class}}" rel="agent_name_key">
                                                         <?php
                                                         $rand = rand();
                                                         $AgentNameKey = "";
                                                         if (isset($enterd_data['Agent Name Key'])) {

                                                            $AgentNameKey = $enterd_data['Agent Name Key'];
                                                         }

                                                         ?>
                                                         <label class="control-label"><span class="agentnamekey">Agent Name Key</span></label>
                                                         <input type="text" class="form-control validate" name="{{$filed_name_prefix}}[Agent Name Key]" data-parentelement="agent-main-data-wrapper" data-cname="[Agent Name Key]" value="{{$AgentNameKey}}" data-ref="<?php echo $rand; ?>" maxlength="4" data-nofdigit="4" autocomplete="off">
                                                         <!-- <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                      </div>
                                                      <div class="form-group name-wrapper  nstar-fields {{$common_class}}" rel="service_reference_id">
                                                         <?php
                                                         $rand = rand();
                                                         $agentreferencename = "";
                                                         if (isset($enterd_data['Service Reference Id'])) {

                                                            $agentreferencename = $enterd_data['Service Reference Id'];
                                                         }  ?>
                                                         <label class="control-label"><span class="agentnamekey">Service Reference Id<span class="service_reference_id"></span></span></label>
                                                         <input type="text" class="form-control validate" name="{{$filed_name_prefix}}[Service Reference Id]" data-parentelement="agent-main-data-wrapper" data-cname="[Service Reference Id]" value="{{$agentreferencename}}" data-ref="<?php echo $rand; ?>" maxlength="9" data-nofdigit="9" autocomplete="off">
                                                         <!-- <span class="invalid-feedback validation-error">
                                                      <strong></strong>
                                                      </span> -->
                                                      </div>


                                                      @endif
                                                      <input type="hidden" name="{{$filed_name_prefix}}[accountnumbertypename]" data-parentelement="agent-main-data-wrapper" data-cname="[accountnumbertypename]" value="" class="accountnametype">
                                                      <input type="hidden" name="{{$filed_name_prefix}}[ElectricUDCAccountCode]" data-parentelement="agent-main-data-wrapper" data-cname="[ElectricUDCAccountCode]" value="" class="programaccountcode">
                                                      <input type="hidden" name="{{$filed_name_prefix}}[GasUDCAccountCode]" data-parentelement="agent-main-data-wrapper" data-cname="[GasUDCAccountCode]" value="" class="programgasaccountcode">
                                                      <input type="hidden" name="formid" value="<?php echo $formid ?>">
                                                   </section>
                                                </div>
                                                <div class="addedaccountnumbers">
                                                </div>
                                                @if($fields>0)
                                                <div class="add_account_number">
                                                   <button type="button" class="btn btn-green add-another-account">Add another account <span class='add' style="visibility:hidden"><?php echo getimage('images/update_w.png'); ?></span> </button>
                                                </div>
                                                <div class='next-button col-sm-12 btnintable bottom_btns'>
                                                   <div class='btn-group'>
                                                      <?php
                                                      if (($section) > 1) {
                                                         echo "<button type='button' class='contact-previous-step btn btn-purple' data-ref='form-section-" . ($section) . "' data-rel='" . ($section - 1) . "'>Previous<span class='browse'>" . getimage('images/previous_w.png') . "</span></button> ";
                                                      }
                                                      ?>
                                                      <button class="savefield btn btn-green">Submit</button>
                                                   </div>
                                                </div>
                                                @endif
                                             </div>
                                             </div>
                                          </div>
                                       </form>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <!--agent details ends-->
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>


<!-- Modal -->
<div id="addnewoption" class="modal fade" role="dialog">
   <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Add option</h4>
         </div>
         <div class="modal-body">
            <div class="added-new-option-message"></div>
            <div class="form-group appnew-option" style="width: 50%;">
               <label class="control-label">Enter option</label>
               <div class="form-group">
                  <input type="text" class="form-control" id="new-option-to-add" name="" placeholder="" value="">
                  <input type="hidden" name="reference_id" value="" id="reference_field_id">
                  <span class="form-group-btn">
                     <button class="btn btn-default add-new-option" type="button">Add</button>
                  </span>
               </div>
            </div>

         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default close-popup-button" data-dismiss="modal">Close</button>
         </div>
      </div>
   </div>
</div>

<?php
$zip_codes = array();
$zip_detail = array();
if (count($zipcodes) > 0) {
   foreach ($zipcodes as   $single_zip_detail) {
      $zip_codes[] = $single_zip_detail->zipcode;
      $zip_detail[$single_zip_detail->zipcode] = array(
         'name' => $single_zip_detail->city,
         'county_fips' => $single_zip_detail->county_fips,
         'county' => $single_zip_detail->county,
         'state' => $single_zip_detail->state,
      );
   }
}

?>
<script>
   window.all_zipcodes = <?php echo json_encode($zip_codes); ?>;
   window.zip_detail = <?php echo json_encode($zip_detail); ?>;
</script>

<script src="{{ asset('js/inputmask.bundle.js') }}"></script>
<script src="{{ asset('js/client-contact.js') }}"></script>
<!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBWjfFcOHDSm0MpQrJ8DCFX05tYb2lSrnk&libraries=places&callback=getLocation" async defer></script> -->
@endsection
