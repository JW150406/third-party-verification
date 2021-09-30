@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array();
if (Auth::user()->access_level == 'tpv') {
    $breadcrum[] =  array('link' => route('client.index'), 'text' =>  'Clients');
    $breadcrum[] =  array('link' => route('client.show', $client->id), 'text' =>  $client->name);
}
$breadcrum[] =  array('link' => route('client.contact-forms', $client->id), 'text' =>  'Forms');
$breadcrum[] =  array('link' => '', 'text' =>  'Form Layout');

breadcrum($breadcrum);

$added_fields = 0;
$formid = 0;
?>

<script src="{{ asset('js/admin-client-contact.js') }}"></script>
<div class="tpv-contbx edit-lead-form">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3 leadcreation_contbx">

                    <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
                        <div class="client-bg-white">
                            <!-- Nav tabs -->
                            <!-- Tab panes -->
                            <div class="tab-content edit-agentinfo">
                                <!--agent details starts-->

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="agent-detailform">
                                            <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2">
                                                <div class="row">
                                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                                        @if ($message = Session::get('success'))
                                                        <div class="alert alert-success">
                                                            <p>{{ $message }}</p>
                                                        </div>
                                                        @endif
                                                        <input type="hidden" class="clientid" name="clientid" value="{{$client_id}}">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group ">
                                                                <h1>Fields </h1>

                                                                <a href="{{route('client.contact-forms-scripts-langauge',['client_id' => $client_id, 'form_id' => $ClientsFields[0]->id])}}" class="btn btn-green" style="margin-top:10px;"> View Scripts <span class="add"><img src="/images/view_w.png"></span> </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="display-table">

                                                        <div class="display-table-cell form-left-part">

                                                            <form class="form-horizontal company-form-layout" role="form" method="POST" action="">
                                                                {{ csrf_field() }}
                                                                {{ method_field('POST') }}
                                                                <input type="hidden" name="commodity_type" value="{{ $formtype }}">
                                                                <input type="hidden" name="utility" value="0">


                                                                <!--  Hide utilites -->
                                                                <div class="col-md-12">
                                                                    <?php $hide_utility = 1;
                                                                    if ($hide_utility == 0) :
                                                                    ?>
                                                                        <div class="form-group">
                                                                            <label class="">Select Utility</label>
                                                                            <select class="form-control utilityselect selectsearch validate" required name="utility">
                                                                                <option value="">Select</option>
                                                                                @foreach($utilities as $utility)
                                                                                <option @if($utility->id == $ClientsFields[0]->utility_id) selected='selected' @endif
                                                                                    data-nofdigit="{{ $utility->accountnumberlength }}"
                                                                                    value="{{$utility->id}}"
                                                                                    data-utilityname="{{ $utility->utilityname }}"
                                                                                    ><?php echo $utility->utilityname . " (" . $utility->commodity . ")";
                                                                                        if ($utility->market != '') echo "($utility->market)";
                                                                                        if ($utility->zipstate != '') echo "($utility->zipstate)";
                                                                                        if ($utility->zip != '') echo "($utility->zip)";
                                                                                        ?>
                                                                                </option>
                                                                                @endforeach

                                                                            </select>

                                                                            <span class="invalid-feedback validation-error">
                                                                                <strong></strong>
                                                                            </span>
                                                                        </div>
                                                                        <!--  Hide utilites -->
                                                                    <?php endif; ?>
                                                                    <div class="form-group name-wrapper">
                                                                        <label class="">Form Name</label>
                                                                        <input class="form-control fieldlabel" name="formname" id="clientformname" value="{{$ClientsFields[0]->formname}}" type="Text" placeholder="Form Name">

                                                                    </div>
                                                                    <div class="form-group name-wrapper">
                                                                        <label class="">Twilio Workspace</label>
                                                                        <select class="selectmenu select_workspace_add_client" name="workspace_id">
                                                                            <option value="">Select</option>
                                                                            @foreach($workspace_ids as $workspace)
                                                                            <option value="{{$workspace->workspace_id}}" @if($ClientsFields[0]->workspace_id == $workspace->workspace_id ) selected @endif >{{$workspace->workspace_name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group name-wrapper">
                                                                        <label class="">Twilio Workflow</label>
                                                                        <div class="select_workflow_id">
                                                                            <select name="workflow_id" class="selectmenu client_workflow_select">
                                                                                <option value="">Select</option>
                                                                                @foreach($workflow_ids as $workflow)
                                                                                <option value="{{$workflow->workflow_id}}" @if($ClientsFields[0]->workflow_id == $workflow->workflow_id ) selected @endif >{{$workflow->workflow_name}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>


                                                                    <ul id="formfields" class="contact-form-fields-admin">
                                                                        @if(count($ClientsFields)>0)

                                                                        @foreach ($ClientsFields as $key => $Fields)
                                                                        <?php

                                                                        $formid =  $Fields->id;


                                                                        if (!empty($Fields->form_fields) &&  $Fields->form_fields != 'null') {
                                                                            $fields_array = json_decode($Fields->form_fields);
                                                                            foreach ($fields_array as $single_field) {
                                                                                if (!isset($single_field->options)) {
                                                                                    $single_field->options = array();
                                                                                }
                                                                                if (isset($single_field->width)) {
                                                                                    $width = $single_field->width;
                                                                                } else {
                                                                                    $width = 12;
                                                                                }
                                                                                $name_field = "";
                                                                                $functionname = "";

                                                                                $fieldjsondata = array(
                                                                                    'label_text' => (isset($single_field->label_text)) ? $single_field->label_text : "",
                                                                                    'placeholder_text' => (isset($single_field->placeholder_text)) ? $single_field->placeholder_text : "",
                                                                                    'required' => '',
                                                                                    'multiselect' => '',
                                                                                    'width' => $width,
                                                                                    'options' => array(),
                                                                                );
                                                                                if (isset($single_field->required)) {
                                                                                    $fieldjsondata['required'] = "checked";
                                                                                }
                                                                                if (isset($single_field->multiselect)) {
                                                                                    $fieldjsondata['multiselect'] = "checked";
                                                                                }
                                                                                if ($single_field->type == 'text') {
                                                                                    $functionname = 'editTextBox';
                                                                                }
                                                                                if ($single_field->type == 'textarea') {
                                                                                    $functionname = 'editTextArea';
                                                                                }

                                                                                if ($single_field->type == 'radio' &&  $single_field->label_text == 'Is the billing address the same as the service address') {
                                                                                    $functionname = 'copyServiceAddressToBillingAddress';
                                                                                    $fieldjsondata['button_css'] = (isset($single_field->button_css)) ? 'checked' : '';
                                                                                    $fieldjsondata['options'] = array(
                                                                                        'selected' => (isset($single_field->options->selected)) ? $single_field->options->selected : "",
                                                                                        'label' => $single_field->options->label,
                                                                                    );
                                                                                } else 
                                                                                      if ($single_field->type == 'radio' &&  $single_field->label_text == 'Is billing name same as authorized name?') {
                                                                                    $functionname = 'copybillingnametoauthorizedname';
                                                                                    $fieldjsondata['button_css'] = (isset($single_field->button_css)) ? 'checked' : '';
                                                                                    $fieldjsondata['options'] = array(
                                                                                        'selected' => (isset($single_field->options->selected)) ? $single_field->options->selected : "",
                                                                                        'label' => $single_field->options->label,
                                                                                    );
                                                                                } else
                                                                                      if ($single_field->type == 'radio' &&  $single_field->label_text == 'Is gas billing name same as authorized name?') {
                                                                                    $functionname = 'copygasbillingnametoauthorizedname';
                                                                                    $fieldjsondata['button_css'] = (isset($single_field->button_css)) ? 'checked' : '';
                                                                                    $fieldjsondata['options'] = array(
                                                                                        'selected' => (isset($single_field->options->selected)) ? $single_field->options->selected : "",
                                                                                        'label' => $single_field->options->label,
                                                                                    );
                                                                                } else
                                                                                    if ($single_field->type == 'radio' &&  $single_field->label_text == 'Is electric billing name same as authorized name?') {
                                                                                    $functionname = 'copyelectricbillingnametoauthorizedname';
                                                                                    $fieldjsondata['button_css'] = (isset($single_field->button_css)) ? 'checked' : '';
                                                                                    $fieldjsondata['options'] = array(
                                                                                        'selected' => (isset($single_field->options->selected)) ? $single_field->options->selected : "",
                                                                                        'label' => $single_field->options->label,
                                                                                    );
                                                                                } else
                                                                                    if ($single_field->type == 'radio' &&  $single_field->label_text == 'Electric service address same as Gas service address?') {
                                                                                    $functionname = 'sameserviceaddressasgas';
                                                                                    $fieldjsondata['button_css'] = (isset($single_field->button_css)) ? 'checked' : '';
                                                                                    $fieldjsondata['options'] = array(
                                                                                        'selected' => (isset($single_field->options->selected)) ? $single_field->options->selected : "",
                                                                                        'label' => $single_field->options->label,
                                                                                    );
                                                                                } else

                                                                                    if ($single_field->type == 'radio' &&  $single_field->label_text != 'Is the billing address the same as the service address') {
                                                                                    $functionname = 'editRadio';
                                                                                    $fieldjsondata['button_css'] = (isset($single_field->button_css)) ? 'checked' : '';
                                                                                    $fieldjsondata['options'] = array(
                                                                                        'selected' => (isset($single_field->options->selected)) ? $single_field->options->selected : "",
                                                                                        'label' => $single_field->options->label,
                                                                                    );
                                                                                }

                                                                                if ($single_field->type == 'checkbox') {

                                                                                    $functionname = 'editcheckbox';
                                                                                    $fieldjsondata['options'] = array(
                                                                                        'selected' => (isset($single_field->options->selected)) ? $single_field->options->selected : array(),
                                                                                        'label' => (isset($single_field->options->label)) ? $single_field->options->label : array(),
                                                                                    );
                                                                                }
                                                                                if ($single_field->type == 'selectbox') {
                                                                                    $functionname = 'editSelectBox';
                                                                                    $fieldjsondata['options'] = array(
                                                                                        'selected' => (isset($single_field->options->selected)) ? $single_field->options->selected : array(),
                                                                                        'label' => (isset($single_field->options->label)) ? $single_field->options->label : array(),
                                                                                    );
                                                                                }

                                                                                if ($single_field->type == 'address') {
                                                                                    $functionname = 'editAddressFields';
                                                                                    $name_field = 'address';
                                                                                }

                                                                                if ($single_field->type == 'billingaddress') {
                                                                                    $functionname = 'editAddressFields';
                                                                                    $name_field = 'billingaddress';
                                                                                }
                                                                                if ($single_field->type == 'electricbillingaddress') {
                                                                                    $functionname = 'editAddressFields';
                                                                                    $name_field = 'electricbillingaddress';
                                                                                }
                                                                                if ($single_field->type == 'gasbillingaddress') {
                                                                                    $functionname = 'editAddressFields';
                                                                                    $name_field = 'gasbillingaddress';
                                                                                }
                                                                                if ($single_field->type == 'serviceaddress') {
                                                                                    $functionname = 'editAddressFields';
                                                                                    $name_field = 'serviceaddress';
                                                                                }
                                                                                if ($single_field->type == 'gasserviceaddress') {
                                                                                    $functionname = 'editAddressFields';
                                                                                    $name_field = 'gasserviceaddress';
                                                                                }
                                                                                if ($single_field->type == 'electricserviceaddress') {
                                                                                    $functionname = 'editAddressFields';
                                                                                    $name_field = 'electricserviceaddress';
                                                                                }

                                                                                if ($single_field->type == 'separator') {
                                                                                    $functionname = 'editSeparator';
                                                                                }



                                                                                if ($single_field->type == 'phonenumber') {
                                                                                    $functionname = 'editPhonenumber';
                                                                                }
                                                                                if ($single_field->type == 'name') {
                                                                                    $functionname = 'editFullNameBox';
                                                                                    $name_field = 'Authorized Name';
                                                                                }
                                                                                if ($single_field->type == 'authname') {
                                                                                    $functionname = 'editNameBox';
                                                                                    $name_field = 'authname';
                                                                                }
                                                                                if ($single_field->type == 'billingname') {
                                                                                    $functionname = 'editBillingFullNameBox';
                                                                                    $name_field = 'billingname';
                                                                                }
                                                                                if ($single_field->type == 'electricbillingname') {
                                                                                    $functionname = 'editBillingFullNameBox';
                                                                                    $name_field = 'electricbillingname';
                                                                                }
                                                                                if ($single_field->type == 'gasbillingname') {
                                                                                    $functionname = 'editBillingFullNameBox';
                                                                                    $name_field = 'gasbillingname';
                                                                                }
                                                                                // if($single_field->type=='electricutility'){
                                                                                //     $functionname = 'editSeparator';
                                                                                // }
                                                                                // if($single_field->type=='gasutility'){
                                                                                //     $functionname = 'editSeparator';
                                                                                // }
                                                                                if ($single_field->type == 'heading') {
                                                                        ?>
                                                                                    <script>
                                                                                        document.write(getHeading('<?php echo $added_fields ?>', {
                                                                                            label_text: '<?php echo $single_field->label_text; ?>'
                                                                                        }));
                                                                                    </script>
                                                                                <?php
                                                                                } else
                                                                                    if ($single_field->type == 'electricutility') {
                                                                                    $functionname = 'getucommoditytility';
                                                                                ?>
                                                                                    <script>
                                                                                        document.write(<?php echo $functionname ?>('<?php echo $added_fields ?>', 'Electric Utility', 'electricutility'));
                                                                                    </script>
                                                                                <?php
                                                                                } else if ($single_field->type == 'gasutility') {
                                                                                    $functionname = 'getucommoditytility';
                                                                                ?>
                                                                                    <script>
                                                                                        document.write(<?php echo $functionname ?>('<?php echo $added_fields ?>', 'Gas utility', 'gasutility'));
                                                                                    </script>
                                                                                <?php
                                                                                } else if ($single_field->type == 'utility') {
                                                                                    $functionname = 'getUtility';
                                                                                ?>
                                                                                    <script>
                                                                                        document.write(<?php echo $functionname ?>('<?php echo $added_fields ?>', 'utility'));
                                                                                    </script>
                                                                                <?php
                                                                                } else if (!empty($name_field)) {
                                                                                ?>
                                                                                    <script>
                                                                                        var data = '<?php echo json_encode($fieldjsondata); ?>';

                                                                                        document.write(<?php echo $functionname ?>('<?php echo $added_fields ?>', data, "<?php echo $name_field ?>"));
                                                                                    </script>
                                                                                    <?php

                                                                                } else {
                                                                                    if (!empty($functionname)) {
                                                                                    ?>
                                                                                        <script>
                                                                                            var data = '<?php echo json_encode($fieldjsondata); ?>';

                                                                                            document.write(<?php echo $functionname ?>('<?php echo $added_fields ?>', data));
                                                                                        </script>
                                                                        <?php
                                                                                    }
                                                                                }



                                                                                $added_fields++;
                                                                            }
                                                                        }
                                                                        ?>



                                                                        @endforeach
                                                                        @endif
                                                                    </ul>
                                                                    <input type="hidden" class="added_elements" value="<?php echo $added_fields ?>">
                                                                    <input type="hidden" name="formid" value="<?php echo $formid ?>">
                                                                    <div class="form-group ">
                                                                        <button class="savefield btn btn-green">Submit<span class="add"><img src="/images/update_w.png"></span></button>
                                                                    </div>
                                                                </div>
                                                        </div>
                                                        </form>



                                                        <div class="display-table-cell form-right-part ">
                                                            <div class="col-xs-12 col-sm-12 col-md-12   sticky-panel">
                                                                <div class="zip-inputbx">
                                                                    <form class="add-field-form" role="form" method="POST" onsubmit="return false" action="">
                                                                        <div class="dropdown agent-edit">
                                                                            <label for="addnewfield">Add New Field</label>
                                                                            <select class="selectsearch select-box-admin" id="select-box-admin">
                                                                                <option value="">Select</option>
                                                                                <option value="name">Authorized Name</option>
                                                                                <option value="authname">Auth Name</option>
                                                                                <option value="billingname">Billing Name</option>
                                                                                @if($formtype =='DualFuel')
                                                                                <option value="gasbillingname">Gas Billing Name</option>
                                                                                <option value="electricbillingname">Electric Billing Name</option>

                                                                                @endif
                                                                                <option value="address">Address</option>
                                                                                <option value="serviceaddress">Service Address</option>
                                                                                @if($formtype =='DualFuel')
                                                                                <option value="gasserviceaddress">GasService Address</option>
                                                                                <option value="electricserviceaddress">Electric Service Address</option>
                                                                                @endif
                                                                                <option value="billingaddress">Billing Address</option>
                                                                                @if($formtype =='DualFuel')
                                                                                <option value="gasbillingaddress">Gas Billing Address</option>
                                                                                <option value="electricbillingaddress">Electric Billing Address</option>
                                                                                @endif
                                                                                <option value="text">Text Box</option>
                                                                                <option value="textarea">Textarea</option>
                                                                                <option value="radio">Radio</option>
                                                                                <option value="checkbox">Checkbox</option>
                                                                                <option value="selectbox">Select Box</option>
                                                                                <option value="phonenumber">Phone Number</option>
                                                                                <option value="separator">Separator</option>
                                                                                <option value="heading">Heading</option>

                                                                                <option value="copyserviceaddresstobilling">Copy Service Address to Billing Address</option>

                                                                                @if($formtype =='DualFuel')

                                                                                <option value="copygasbillingnametoauthorizedname">Same gas billing name as authorized name</option>
                                                                                <option value="copyelectricbillingnametoauthorizedname">Same electric billing name as authorized name</option>
                                                                                <option value="sameserviceaddressasgas">Same service address as gas</option>

                                                                                <option value="gasutility">Gas Utility</option> @else
                                                                                <option value="copybillingnametoauthorizedname">Same billing name as authorized name</option>
                                                                                @endif
                                                                                @if($formtype =='DualFuel') <option value="electricutility">Electric Utility</option> @endif
                                                                            </select>
                                                                        </div>

                                                                        <div class="leadcreation">
                                                                            <button class="btn btn-green add-new-item" type="button">Add<span class="add"><?php echo getimage("images/add.png"); ?></span></button>
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
                                <!--agent details ends-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.ajaxclientworkflow = "{{route('ajax-client-workflow')}}";
</script>
@endsection