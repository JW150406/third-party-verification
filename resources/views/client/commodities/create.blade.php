<div class="team-addnewmodal">
    <div class="modal fade" id="commodity-create-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add New Commodity</h4>
                </div>
                <div class="modal-body">
                    <div class="ajax-error-message">
                    </div>
                    <form class="row" action="{{ route('commodity.store')}}" method="POST" id="commodity-create-form" data-parsley-validate>
                        @csrf
                        <input type="hidden" name="id" id="client-commodity-id">
                        <input type="hidden" name="client_id" value="{{$client_id}}">
                       
                        <div class="clearfix"></div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label for="commodity_name" class="yesstar">Name</label>
                                <input id="commodity_name" autocomplete="off" type="text" class="form-control required" name="name" data-parsley-required='true' data-parsley-required-message="Please enter a name" data-parsley-minlength-message='This field must contain at least 2 characters'  data-parsley-minlength="2" data-parsley-mixlength="255">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 mt10">
                            <div class="form-group">
                                <label for="all-unit" class="yesstar">Unit</label> <span>(Note: Please type below and press enter to add the unit)</span>
                                <div class="zipcode-all scrollbar-inner" id="all-unit">

                                </div>
                            </div>
                            <span id="unit-error" class="error"></span>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group autocomplete">
                                <input id="auto_suggest_uint" type="text" class="form-control"  data-parsley-mixlength="5" maxlength="6">
                            </div>
                        </div>
                        
                        <div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
                            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                <div class="btn-group">
                                    <button type="submit" id="save-commodity-btn" class="btn btn-green">Save</button>
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
@push('scripts')
<script>    
    $(document).ready(function() {

        $(document).on('click', '.commodity-create-modal', function(e) {            
            setUnitErrorMsg("");
            $(".ajax-error-message").html('');
            $(".help-block").remove('');
            $("#all-unit").html('');
            $("#commodity-create-form")[0].reset();
            var action_type = $(this).data('type');
            
            var title = $(this).data('original-title');
            $('#commodity-create-modal .modal-title').html(title);

            if (action_type == 'new') {
                $('#client-commodity-id').val('');
                $('#commodity-create-modal').modal();
            } else {
                var id = $(this).data('id');
                var name = $(this).closest('tr').find('td:eq(1)').html();
                $('#client-commodity-id').val(id);           
                $('#commodity_name').val(name);
                $.ajax({
                    url: "{{route('commodity.edit')}}",
                    data: {
                        commodity_id: id
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $.each(response.data, function(key, value) {
                                setUnit(value.unit);
                            });
                        } else {
                            console.log(response.message);    
                        }

                        $('#commodity-create-modal').modal();
                    },
                    error: function(xhr) {
                        console.log(xhr);
                    }
                });
            }
            

        });
        $("#commodity-create-form").submit(function(e) {
            var totalUnits = $('#all-unit .alert-defualt').length;
            if (totalUnits <= 0) {
                setUnitErrorMsg("Please add at least one unit");
                return false;
            }

            e.preventDefault(); // avoid to execute the actual submit of the form.

            var form = $(this);
            var url = form.attr('action');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(response) {
                    $('#commodity-create-modal').modal("hide");
                    if (response.status == 'success') {
                        printAjaxSuccessMsg(response.message);    
                    } else {
                        printAjaxErrorMsg(response.message);
                    }
                    $('#commodity-table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    if (xhr.status == 422) {
                        printErrorMsgNew(form,xhr.responseJSON.errors);
                    }
                }
            });
        });

        $('#save-commodity-btn').click(function(e) {
            var totalUnits = $('#all-unit .alert-defualt').length;
            if (totalUnits <= 0) {
                setUnitErrorMsg("Please add at least one unit");
            }
        });

        $('#auto_suggest_uint').keypress(function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            var unit = $(this).val();
            if(keycode == '13' && unit != '') {
                event.preventDefault();
                var units = $("input[name='units[]']")
                    .map(function(){return $(this).val();}).get();

                if(!(units.includes(unit))) {
                    setUnit(unit);
                    setUnitErrorMsg("");
                    $(this).val("");
                } else {
                    setUnitErrorMsg("This unit is taken");     
                }
            }

        });

        function setUnitErrorMsg(message)
        {
            $("#unit-error").html("<span class='help-block' >"+message+"</span>");
        }

        function setUnit(unit)
        {
            $("#all-unit").append('<div class="alert alert-defualt alert-dismissible"><input type="hidden" name="units[]" value="' + unit + '" /><a href="javascript:void(0)" class="close" data-parsley-required=\'true\' data-parsley-required-message="Please Find & Add" data-dismiss="alert" aria-label="close">×</a><unit><p>' + unit + '</p></div>');
        }
    });
</script>
@endpush

