<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
       <!--  <div class="agent-detailform"> -->
            <div class="ajax-error-message-page"></div>
            
                <!-- <form id="twilio-create-form" role="form" method="POST"
                      action="{{route('twilio.saveNumber',$client_id)}}" data-parsley-validate>
                    @csrf
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <label>Workspace</label>
                                <select class="changedaterange form-control" id="twilio_workspace" name="workspace"
                                        data-parsley-trigger="focusout" data-parsley-required='true'
                                        data-parsley-required-message="Please select workspace.">
                                </select>
                            </div>
                        </div>
                        @if(auth()->user()->hasPermissionTo('add-delete-twilio-info'))
                            <div class="col-md-6 col-sm-6">
                                <div class="btn-group mt30 mb30">
                                    <a href="javascript:void(0)" role="button" class="btn workspace-create">
                                        <img data-toggle="tooltip" data-placement="top" data-container="body" title=""
                                             data-original-title="Add" src="{{asset('images/add_green.png')}}">
                                    </a>
                                    <a href="javascript:void(0)" role="button" class="btn workspace-delete">
                                        <img data-toggle="tooltip" data-placement="top" data-container="body" title=""
                                             data-original-title="Delete" src="{{asset('images/cancel.png')}}">
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <label>Workflow </label>
                                <select class="changedaterange form-control" id="twilio_workflow" name="workflow"
                                        data-parsley-trigger="focusout" data-parsley-required='true'
                                        data-parsley-required-message="Please select workflow.">
                                    <option value="" selected></option>
                                </select>
                            </div>
                        </div>
                        @if(auth()->user()->hasPermissionTo('add-delete-twilio-info'))
                            <div class="col-md-6 col-sm-6">
                                <div class="btn-group mt30 mb30">
                                    <a href="javascript:void(0)" role="button" class="btn workflow-create">
                                        <img data-toggle="tooltip" data-placement="top" data-container="body" title=""
                                             data-original-title="Add" src="{{asset('images/add_green.png')}}">
                                    </a>
                                    <a href="javascript:void(0)" role="button" class="btn workflow-delete">
                                        <img data-toggle="tooltip" data-placement="top" data-container="body" title=""
                                             data-original-title="Delete" src="{{asset('images/cancel.png')}}">
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <label for="twilio_number">Phone No.</label>
                                <input type="text" class="form-control" name="phone_number" id="twilio_number"
                                       data-parsley-trigger="focusout" data-parsley-required='true'
                                       data-parsley-required-message="Please enter phone number."
                                       data-parsley-pattern="[0-9]{10}"
                                       data-parsley-length-message="The phone number must be 10 digits.">
                            </div>
                        </div>
                        @if(auth()->user()->hasPermissionTo('add-delete-twilio-info'))
                            <div class="col-md-6 col-sm-6">
                                <div class="btn-group mt30 mb30">
                                    <button type="submit" class="btn btn-green">Save</button>
                                </div>
                            </div>
                        @endif
                    </div>
                </form> -->
                @if(Auth::user()->hasPermissionTo('view-workflow'))
                <div class="mt20">
                    <div class="col-md-6 col-sm-6">
                         <h4>Workflows</h4>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <div class="cont_bx3 sor_fil pull-right">
                        <div class="btn-group ">
                        <?php if (Auth::user()->hasPermissionTo('add-workflow')) { ?>

                            <a href="javascript:void(0)" class="btn btn-green add_new_workflow" 
                               id="add_new_workflow" data-type="add">Add Workflow</a>

                        <?php } ?>
                    </div>
                        </div>
                    </div>

                </div>
               
                

                <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx mt30">

                    <div class="table-responsive">
                        <table class="table" id="workflow-table">
                            <thead>
                            <tr class="heading acjin">
                                <th>Sr. No.</th>
                                <th class="wf-name">Workflow Name</th>
                                <th class="wf-id">Workflow Id</th>
                                <th class="action-width">Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                @endif
                @if(Auth::user()->hasPermissionTo('view-twilio-number'))
                <div class="mt20">
                    <div class="col-md-6 col-sm-6">
                        <h4>Phone Numbers</h4>
                    </div>

                     <div class="col-md-6 col-sm-6">
                        <div class="cont_bx3 sor_fil pull-right">
                            <div class="btn-group ">
                                <?php if (Auth::user()->hasPermissionTo('add-twilio-number')) { ?>

                                    <a href="javascript:void(0)" class="btn btn-green add_new_number" data-original-title="Add Phone Number" data-type="add">Add Phone Number</a>

                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx mt30">
                    <div class="table-responsive">
                        <table class="table" id="number-table">
                            <thead>
                                <tr class="heading acjin">
                                    <th>Sr. No.</th>
                                    <th class="wf-name">Phone Number</th>
                                    <th class="wf-name">Workflow Name</th>
                                    <th>Type</th>
                                    <th class="action-width">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        <!-- </div> -->
    </div>
<!-- </div> -->

@include('client.twilio.workflow.create')
@include('client.twilio.workflow.delete')
@include('client.twilio.number.create')
@include('client.twilio.number.delete')
@push('scripts')
    <script>
        /*$(function () {
            $('#twilio-create-form').validate({
                rules: {
                    workspace: {
                        required: true,
                    },
                    workflow: {
                        required: true,
                    },
                    phone_number: {
                        required: true,
                        pattern: "(\\+\\d{1,3}[- ]?)?\\d{10}",
                    }
                },
                messages: {
                    workspace: {
                        required: "Please select workspace."
                    },
                    workflow: {
                        required: "Please select workflow."
                    },
                    phone_number: {
                        pattern: "The phone number must be valid format.",
                        required: "Please enter phone number"
                    }
                },
                submitHandler: function(e) {
                    e.preventDefault(); // avoid to execute the actual submit of the form.
                    var form = $(this);
                    var url = form.attr('action');
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: form.serialize(), // serializes the form's elements.
                        success: function(response) {
                            $(".help-block").remove('');
                            if (response.status == 'success') {
                                printAjaxSuccessMsg(response.message);
                            } else {
                                printAjaxErrorMsg(response.message);
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status == 422) {
                                printError(form,xhr.responseJSON.errors);
                            }
                        }
                    });
                }
            });
        });*/
        $(document).ready(function () {
            $("#workspace-create-form").submit(function (e) {
                e.preventDefault(); // avoid to execute the actual submit of the form.
                var form = $(this);
                var url = form.attr('action');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(), // serializes the form's elements.
                    success: function (response) {
                        $('#workspace-create-modal').modal("hide");
                        if (response.status == 'success') {
                            printAjaxSuccessMsg(response.message);
                        } else {
                            printAjaxErrorMsg(response.message);
                        }
                        getWorkSpace();
                    },
                    error: function (xhr) {
                        if (xhr.status == 422) {
                            printErrorMsgNew(form, xhr.responseJSON.errors);
                        }
                    }
                });
            });

            $("#workspace-delete-form").submit(function (e) {
                e.preventDefault(); // avoid to execute the actual submit of the form.
                var form = $(this);
                var url = form.attr('action');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(),
                    success: function (response) {
                        $('#workspace-delete-modal').modal("hide");
                        if (response.status == 'success') {
                            printAjaxSuccessMsg(response.message);
                        } else {
                            printAjaxErrorMsg(response.message);
                        }
                        getWorkSpace();
                    }
                });
            });

            $(document).on('click', '.workspace-create', function (e) {
                $(".ajax-error-message").html('');
                $(".help-block").remove('');
                $("#workspace-create-form")[0].reset();
                $('#workspace-create-modal').modal();
            });
            $('#twilio_workspace').change(function (e) {
                getWorkFlow();
            });
            
            $(document).on('click', '.workspace-delete', function (e) {
                var workspace = $('#twilio_workspace').val();
                if (workspace == '' || workspace == null) {
                    alert('Please select a workspace');

                } else {
                    $('#delete_workspace_id').val(workspace);
                    $('#workspace-delete-modal').modal();
                }
            });
            $("#workflow-create-form").submit(function (e) {
                e.preventDefault(); // avoid to execute the actual submit of the form.
                var form = $(this);
                var url = form.attr('action');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(), // serializes the form's elements.
                    success: function (response) {
                        $('#workflow-create-modal').modal("hide");
                        if (response.status == 'success') {
                            printAjaxSuccessMsg(response.message);
                        } else {
                            printAjaxErrorMsg(response.message);
                        }

                        $("#workflow-table").DataTable().ajax.reload();

                        // getWorkFlow();
                    },
                    error: function (xhr) {
                        if (xhr.status == 422) {
                            printErrorMsgNew(form, xhr.responseJSON.errors);
                        }
                    }
                });
            });

            $("#workflow-delete-form").submit(function (e) {
                e.preventDefault(); // avoid to execute the actual submit of the form.

                var form = $(this);
                var url = form.attr('action');

                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(),
                    success: function (response) {
                        $('#workflow-delete-modal').modal("hide");
                        if (response.status == 'success') {
                            printAjaxSuccessMsg(response.message);
                        } else {
                            printAjaxErrorMsg(response.message);
                        }
                        $("#workflow-table").DataTable().ajax.reload();
                        // getWorkFlow();
                    }
                });
            });

            $("#number-create-form").submit(function (e) {
                e.preventDefault(); // avoid to execute the actual submit of the form.
                var form = $(this);
                var url = form.attr('action');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(), // serializes the form's elements.
                    success: function (response) {
                        $('#number-create-modal').modal("hide");
                        if (response.status == 'success') {
                            printAjaxSuccessMsg(response.message);
                        } else {
                            printAjaxErrorMsg(response.message);
                        }

                        $("#number-table").DataTable().ajax.reload();

                        // getWorkFlow();
                    },
                    error: function (xhr) {
                        if (xhr.status == 422) {
                            printErrorMsgNew(form, xhr.responseJSON.errors);
                        }
                    }
                });
            });

            $("#number-delete-form").submit(function (e) {
                e.preventDefault(); // avoid to execute the actual submit of the form.

                var form = $(this);
                var url = form.attr('action');

                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(),
                    success: function (response) {
                        $('#number-delete-modal').modal("hide");
                        if (response.status == 'success') {
                            printAjaxSuccessMsg(response.message);
                        } else {
                            printAjaxErrorMsg(response.message);
                        }
                        $("#number-table").DataTable().ajax.reload();
                        // getWorkFlow();
                    }
                });
            });


            // $(document).on('click', '.workflow-create', function (e) {
            //     $(".ajax-error-message").html('');
            //     $(".help-block").remove('');
            //     var workspace = $('#twilio_workspace').val();
            //     if (workspace == '' || workspace == null) {
            //         $('#twilio_workflow').after("<span class='help-block' >Please select a workspace</span>");

            //     } else {
            //         $("#workflow-create-form")[0].reset();
            //         $('#worksapce_for_create_workflow').val(workspace);
            //         $('#workflow-create-modal').modal();
            //     }
            // });

            // $(document).on('click', '.workflow-delete', function (e) {
            //     var workflow = $('#twilio_workflow').val();
            //     if (workflow == '' || workflow == null) {
            //         alert('Please select a workflow');

            //     } else {
            //         $('#delete_workflow_id').val(workflow);
            //         $('#workflow-delete-modal').modal();
            //     }
            // });

            $("#twilio-create-form").submit(function (e) {
                e.preventDefault(); // avoid to execute the actual submit of the form.
                var form = $(this);
                var url = form.attr('action');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(), // serializes the form's elements.
                    success: function (response) {
                        $(".help-block").remove('');
                        if (response.status == 'success') {
                            printAjaxSuccessMsg(response.message);
                        } else {
                            printAjaxErrorMsg(response.message);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status == 422) {
                            printError(form, xhr.responseJSON.errors);
                        }
                    }
                });
            });

            function getNumber() {
                $('#twilio_number').val(null);
                var workflow = $('#twilio_workflow').val();
                if (workflow == '' || workflow == null) {

                } else {
                    $.ajax({
                        url: "{{route('twilio.getNumber',$client_id)}}",
                        data: {workflow_id: workflow},
                        success: function (response) {
                            if (response.status == 'success') {
                                $('#twilio_number').val(response.data.phonenumber);
                            }
                        }
                    });
                }
            }

            function getWorkFlow() {
                $('#twilio_workflow').html('');
                $('#twilio_workflow').append("<option value=''>Select</option>");
                $('#twilio_workflow').val('').trigger('change');
                $.ajax({
                    url: "{{route('twilio.getWorkflowByClient',$client_id)}}",
                    success: function (response) {
                        if (response.status == 'success') {

                            $.each(response.data, function (key, value) {
                                $('#twilio_workflow').append("<option value='" + value.id + "'>" + value.workflow_name + "</option>");

                            });
                            $('#twilio_workflow').val('').trigger('change');
                        }
                    }
                });
            }

            function getWorkSpace() {
                $('#twilio_workspace').html('');
                $.ajax({
                    url: "{{route('twilio.getWorkSpaceByClient',$client_id)}}",
                    success: function (response) {
                        if (response.status == 'success') {
                            $('#twilio_workspace').append("<option value=''>Select</option>");
                            $.each(response.data, function (key, value) {
                                $('#twilio_workspace').append("<option value='" + value.workspace_id + "'>" + value.workspace_name + "</option>");
                            });
                            $('#twilio_workspace').val('').trigger('change');
                        }
                    },
                    error: function (xhr) {

                    }
                });
            }

            function printError(form, msg) {
                $(".help-block").remove('');
                var errors = '';
                $.each(msg, function (key, value) {
                    $(form).find("[name='" + key + "']").after("<span class='help-block' >" + value[0] + "</span>");

                });
            }

            getWorkSpace();
            getWorkFlow();
        });

        $(document).ready(function () {
            @if(Auth::user()->hasPermissionTo('view-workflow'))
            var workFlowTable = $('#workflow-table').DataTable({
                dom: 'tr<"bottom"lip>',
                processing: true,
                serverSide: true,
                autoWidth: false,
                lengthChange: true,
                searching: false,
                hideEmptyCols: ['extn', 3],
                ajax: {
                    url: "{{ route('client.workflow.index', array_get($client, 'id')) }}",
                    // data: {
                    //     client_id: "{{$client_id}}"
                    // }
                },
                aaSorting: [[4, 'desc']],
                columns: [{
                        data: null
                    },
                    {
                        data: 'workflow_name',
                        name: 'workflow_name'
                    },
                    {
                        data: 'workflow_id',
                        name: 'workflow_id'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {data: 'id',searchable:false,visible: false},
                ],
                columnDefs: [{
                    "searchable": false,
                    "orderable": false,
                    "width": "5%",
                    "targets": 0,
                }],
                'fnDrawCallback': function () {
                    var table = $('#workflow-table').DataTable();
                    var info = table.page.info();
                    if (info.pages > 1) {
                        $('#workflow-table_info')[0].style.display = 'block';
                        $('#workflow-table_paginate')[0].style.display = 'block';
                    } else {
                        $('#workflow-table_info')[0].style.display = 'none';
                        $('#workflow-table_paginate')[0].style.display = 'none';
                    }

                    if (info.recordsTotal < 10) {
                        $('#workflow-table_length')[0].style.display = 'none';
                    } else {
                        $('#workflow-table_length')[0].style.display = 'block';
                    }
                },
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    var table = $('#workflow-table').DataTable();
                    var info = table.page.info();
                    $("td:nth-child(1)", nRow).html(iDisplayIndex + 1 + info.start);
                    return nRow;
                }
            }).on( 'processing.dt', function ( e, settings, processing ) {
                $(".tooltip").tooltip("hide");
            });
            @endif
            var numberTable = $('#number-table').DataTable({
                dom: 'tr<"bottom"lip>',
                processing: true,
                serverSide: true,
                autoWidth: false,
                lengthChange: true,
                hideEmptyCols: ['extn', 4],
                searching: false,
                ajax: {
                    url: "{{ route('twilio.numbers', array_get($client, 'id')) }}",
                    // data: {
                    //     client_id: "{{$client_id}}"
                    // }
                },
                aaSorting: [[5, 'desc']],
                columns: [{
                        data: null
                    },
                    {
                        data: 'phonenumber',
                        name: 'phonenumber'
                    },
                    {
                        data: 'workflow.workflow_name',
                        name: 'workflow.workflow_name'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {data: 'id',searchable:false,visible: false},
                ],
                columnDefs: [{
                    "searchable": false,
                    "orderable": false,
                    "width": "5%",
                    "targets": 0,
                }],
                'fnDrawCallback': function () {
                    var table = $('#number-table').DataTable();
                    var info = table.page.info();
                    if (info.pages > 1) {
                        $('#number-table_info')[0].style.display = 'block';
                        $('#number-table_paginate')[0].style.display = 'block';
                    } else {
                        $('#number-table_info')[0].style.display = 'none';
                        $('#number-table_paginate')[0].style.display = 'none';
                    }

                    if (info.recordsTotal < 10) {
                        $('#number-table_length')[0].style.display = 'none';
                    } else {
                        $('#number-table_length')[0].style.display = 'block';
                    }
                },
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    var table = $('#number-table').DataTable();
                    var info = table.page.info();
                    $("td:nth-child(1)", nRow).html(iDisplayIndex + 1 + info.start);
                    return nRow;
                }
            }).on( 'processing.dt', function ( e, settings, processing ) {
                $(".tooltip").tooltip("hide");
            });
        });

        $(document).on('click', '.add_new_workflow,.edit-workflow', function(e) {
            $(".ajax-error-message").html('');
            var action_type = $(this).data('type');
            $('#workflow_id').val("");
            $('#workflow_name').val("");
            $("#workflow_unique_id").val("");

            if (action_type == "add") {
                $('#workflow-create-modal').modal();
                return false;
            }

            var title = $(this).data('original-title');
            $('#workflow-create-modal .modal-title').html(title);
            var workflow_id = $(this).data('id');
            $.ajax({
                url: "{{ route('clients.workflow.edit') }}",
                data: {
                    id: workflow_id
                },
                success: function(response) {
                    if (response.status == 'success') {
                        $('#workflow_id').val(response.data.workflow_id);
                        $('#workflow_name').val(response.data.workflow_name);
                        $("#workflow_unique_id").val(response.data.id);
                    }
                    $('#workflow-create-modal').modal();
                },
                error: function(xhr) {
                    console.log(xhr);
                }
            });
        });

        $(document).on('click', '.add_new_number,.edit-number', function(e) {
            $(".ajax-error-message").html('');
            $("#number-create-form")[0].reset();
            $("#number_id").val("");
            $("#number-create-form .select2").val("").trigger('change.select2');
            var action_type = $(this).data('type');
            
            var title = $(this).data('original-title');
            $('#number-create-modal .modal-title').html(title);

            if (action_type == "add") {
                $('#number-create-modal').modal();
                return false;
            }

           
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('twilio.getNumber',$client_id) }}",
                data: {
                    id: id
                },
                success: function(response) {
                    if (response.status == 'success') {
                        $("#number_id").val(response.data.id);
                        $('#twilio_number').val(numFormat(response.data.phonenumber));
                        $('#twilio_workflow').val(response.data.client_workflowid).trigger('change.select2');
                        $('#twilio_type').val(response.data.type).trigger('change.select2');
                    }
                    $('#number-create-modal').modal();
                },
                error: function(xhr) {
                    console.log(xhr);
                }
            });
        });

        function numFormat(number) {
            //return number.replace(/(.{1})(\d{3})(\d{3})(\d{4})/, "$1 $2 $3 $4");
            return number.replace({{ config()->get('constants.DISPLAY_PHONE_NUMBER_FORMAT') }}, "{{ config()->get('constants.PHONE_NUMBER_REPLACEMENT') }}");
        }
    </script>
@endpush