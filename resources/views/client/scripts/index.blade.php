@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array();
$breadcrum[] = array('link' => '', 'text' => 'Scripts');
breadcrum($breadcrum);

?>



<div class="tpv-contbx">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="client-bg-white">

                            <h4>Script Name</h4>
                            <h4>Description</h4>

                            <div class="row add-script-form">
                                <div class="col-md-9 col-sm-9 w80">
                                    <div class="table-responsive">
                                        <table class="table script-table  mt30">
                                            <thead>
                                                <tr>
                                                    <td class="w60">Sr.No.</td>
                                                    <td class="w430">Question</td>
                                                    <td>Option</td>
                                                    <td class="w170">Verification Criteria</td>
                                                    <td class="w105">TPV Agent?</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>
                                                        <div class="cust-txtarea">
                                                            <textarea rows='1' id="questiontext" placeholder="Write Question"></textarea>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <select class="select2 no-search" id="fieldselector">
                                                            <option value="yes_no">Yes/No</option>
                                                            <option value="yes_no">Agree/ Disagree</option>
                                                            <option value="yes_no">Understand/ Don't Understand</option>
                                                            <option value="data_field">Data Field</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <div id="yes_no" class="cust-hide form-group text-left radio-btns flex">
                                                            <label class="radio-inline">
                                                                <input type="radio" id="" name="1" value="1">Yes
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" id="" name="1" value="0">No
                                                            </label>
                                                        </div>
                                                        <div id="data_field" class="cust-hide text-area display-none">
                                                            <textarea rows='1' id="TextArea1" class="ui-droppable" placeholder="Write here"></textarea>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group checkbx">
                                                            <label class="checkbx-style">
                                                                <input autocomplete="off" type="checkbox" name="">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>2</td>
                                                    <td><textarea rows='1' id="questiontext" placeholder="Write Question"></textarea></td>
                                                    <td>
                                                        <select class="select2 no-search" data-width="100%" data-minimum-results-for-search="Infinity">
                                                            <option value="">Yes/No</option>
                                                            <option value="">Agree/ Disagree</option>
                                                            <option value="">Understand/ Don't Understand</option>
                                                            <option value="">Data Field</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <div class="form-group text-left radio-btns flex">
                                                            <label class="radio-inline">
                                                                <input type="radio" id="" name="2" value="1">Yes
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" id="" name="2" value="0">No
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group checkbx">
                                                            <label class="checkbx-style">
                                                                <input autocomplete="off" type="checkbox" name="">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!--end--col-8-->
                                <div class="col-md-3 col-sm-3 w20 sticky-panel">
                                    <div class="right-datafield">
                                        <h4 class="script-df">Data Field</h4>

                                        <div id="allfields" class="name-wrapper script-tags scrollbar-inner">

                                            <div class="corpo">
                                                <p class="tag-title">Title 1</p>
                                                <span class="addtag grid-item ui-draggable"> <strong>Program</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Account Number</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Authorized First name</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Authorized Middle initial</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Authorized Last name</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Authorized Name</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Phone Number</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Email</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>What is the service address?</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Is the billing address the same as the service address</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Billing Address</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Billing full name</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Relationship</strong> </span>
                                            </div>

                                            <div class="corpo">
                                                <p class="tag-title">Title 2</p>
                                                <span class="addtag grid-item ui-draggable"> <strong>Program</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Account Number</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Authorized First name</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Authorized Middle initial</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Authorized Last name</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Authorized Name</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Phone Number</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Email</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>What is the service address?</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Is the billing address the same as the service address</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Billing Address</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Billing full name</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Relationship</strong> </span>
                                            </div>

                                            <div class="corpo">
                                                <p class="tag-title">Title 3</p>
                                                <span class="addtag grid-item ui-draggable"> <strong>Program</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Account Number</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Authorized First name</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Authorized Middle initial</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Authorized Last name</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Authorized Name</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Phone Number</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Email</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>What is the service address?</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Is the billing address the same as the service address</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Billing Address</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Billing full name</strong> </span>
                                                <span class="addtag grid-item ui-draggable"> <strong>Relationship</strong> </span>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                                <!--end-col-4-->
                            </div>
                            <!--button-area--->
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="btn-group script-btns">
                                        <a href="{{ route('admin.clients.scripts.review', 102) }}" class="btn btn-green">Review</a>
                                        <a href="javascript:void(0)" class="btn btn-green">Save</a>
                                        <a href="javascript:void(0)" class="btn btn-red">Cancel</a>
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


<script>
    var textarea = document.querySelector('textarea');

    textarea.addEventListener('keydown', autosize);

    function autosize() {
        var el = this;
        setTimeout(function() {
            el.style.cssText = 'height:auto; padding:0';
            // for box-sizing other than "content-box" use:
            // el.style.cssText = '-moz-box-sizing:content-box';
            el.style.cssText = 'height:' + el.scrollHeight + 'px';
        }, 0);
    }
</script>



<!--script-for--select field set and display text-area---->
<script>
    $(function() {
        $('#fieldselector').change(function() {
            $('.cust-hide').hide();
            $('#' + $(this).val()).show();
        });
    });
</script>

<script language="javascript" type="text/javascript">
    $(function() {
        $("#allfields .addtag").draggable({
            appendTo: "body",
            helper: "clone",
            cursor: "select",
            revert: "invalid"
        });
        initDroppable($("#TextArea1"));

        function initDroppable($elements) {
            $elements.droppable({
                hoverClass: "textarea",
                accept: ":not(.ui-sortable-helper)",
                drop: function(event, ui) {
                    var $this = $(this);

                    var tempid = ui.draggable.text();
                    var dropText;
                    dropText = " [" + tempid + "] ";
                    var droparea = document.getElementById('TextArea1');
                    var range1 = droparea.selectionStart;
                    var range2 = droparea.selectionEnd;
                    var val = droparea.value;
                    var str1 = val.substring(0, range1);
                    var str3 = val.substring(range1, val.length);
                    droparea.value = str1 + dropText + str3;
                }
            });
        }
    });
</script>



@endsection