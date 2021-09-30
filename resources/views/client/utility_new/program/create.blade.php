<!-- Modal add program Starts -->

<div class="team-addnewmodal">
    <div class="modal fade" id="add_program" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Program</h4>
                </div>
                <div class="ajax-error-message">
                </div>
                <div class="modal-body v-star">
                    <div class="modal-form row">
                        <div class="col-xs-12 col-sm-12 col-md-12">

                            <form class="" id="program-create-form" role="form" method="POST" action="{{ route('utility.program.store') }}" data-parsley-validate   >
                                @csrf
                                @method('put')
                                <input type="hidden" name="id" id="program-id">
                                <input type="hidden" name="client_id" value="{{$client_id}}">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <label for="programm_commodity" class="yesstar">Commodity</label>
                                            <div class="dropdown select-dropdown">
                                                <select data-parsley-required='true' data-parsley-errors-container="#select2-program-error-message" data-parsley-required-message="Please select commodity" class="select2 selectsearch form-control vendorstatus" id="programm_commodity" name="commodity">
                                                    <option value="">Select</option>
                                                    
                                                </select>
                                                <span id="select2-program-error-message"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <label for="product_brandname" class="yesstar" >Brand Name</label>
                                            <div class="dropdown select-dropdown">
                                                <select data-parsley-required='true' data-parsley-errors-container="#select2-brandname-error-message" data-parsley-required-message="Please select brand name" class="select2 selectsearch form-control vendorstatus" id="programm_brandname" name="utilityname">
                                                    <option value="">Select</option>               
                                                </select>
                                                <span id="select2-brandname-error-message"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <label for="program_provider" class="yesstar" >Utility</label>
                                            <div class="dropdown select-dropdown">
                                                <select data-parsley-required='true' data-parsley-errors-container="#select2-utility-error-message" data-parsley-required-message="Please select utility" class="select2 selectsearch form-control vendorstatus" id="program_provider" name="fullname">
                                                    <option value="">Select</option>               
                                                </select>
                                                <span id="select2-utility-error-message"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <label for="program_customer_type" class="yesstar" >Customer Type</label>
                                            <div class="dropdown select-dropdown">
                                                <select data-parsley-required='true' data-parsley-errors-container="#select2-customer-error-message" class="select2 form-control" id="program_customer_type" name="customer_type_id">
                                                    <option value="">Select</option>               
                                                </select>
                                                <span id= "select2-customer-error-message"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="program_name" class="yesstar" >Program Name</label>
                                            <input id="program_name" autocomplete="off" type="text" class="form-control required" name="name" value="" data-parsley-required='true' minlength="2" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="program_code" class="yesstar" >Code</label>
                                            <input id="program_code" autocomplete="off" type="text" class="form-control required" name="code" value="" data-parsley-required='true' maxlength="255">
                                        </div>
                                    </div>
                                </div>
                                <div class="row"> 
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="program_rate" class="yesstar" >Rate</label>
                                            <input id="program_rate" autocomplete="off" type="text" class="form-control required" name="rate" value=""  data-parsley-required='true' maxlength="255">

                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="program_unit" class="yesstar" >Unit</label>
                                            <div class="dropdown select-dropdown">
                                                <select data-parsley-required='true' data-parsley-errors-container="#select2-unit-error-message" class="select2 form-control" id="program_unit" name="unit_of_measure">
                                                    <option value="">Select</option>               
                                                </select>
                                                <span id="select2-unit-error-message"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-4 col-md-4">
                                        <div class="form-group"  >
                                            <label for="program_etf" class="yesstar" >ETF</label>
                                            <input id="program_etf" autocomplete="off" type="text" class="form-control required" name="etf" maxlength="255" data-parsley-required='true'>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-4 col-md-4">
                                        <div class="form-group">
                                            <label for="program_msf" class="yesstar" >MSF</label>
                                            <input id="program_msf" autocomplete="off" type="text" class="form-control required" name="msf" data-parsley-required='true'>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-4 col-md-4">
                                        <div class="form-group">
                                            <label for="program_term" class="yesstar" >Term</label>
                                            <input id="program_term" autocomplete="off" type="text" class="form-control required" name="term" data-parsley-required='true'>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="custom-field-row">
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
                                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                        <div class="btn-group">
                                            <button type="submit" class="btn btn-green"><span class="save-text">Save</span></button>
                                            <button type="button" class="btn btn-red" data-dismiss="modal">Cancel</button>

                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal ends -->

        
