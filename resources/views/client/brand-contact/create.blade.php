<div class="team-addnewmodal">
    <div class="modal fade" id="brand-contact-create-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Brand Contact</h4>
                </div>
                <div class="modal-body">
                    <div class="ajax-error-message">
                    </div>
                    <form class="row" action="{{ route('brand-contact.store')}}" method="POST" id="brand-contact-create-form" data-parsley-validate>
                        @csrf
                        <input type="hidden" name="id" id="brand-contact-id">
                        <input type="hidden" name="client_id" value="{{$client_id}}">
                        <div class="col-md-12 col-sm-12">
                            <div class="form-group">
                            <label>Brand Name</label>
                            <input id="brand-contact_name" autocomplete="off" type="text" class="form-control required brand-contact_name_class" name="name" data-parsley-required='true' data-parsley-required-message="Please enter brand name" >
                               
                                <span id="select2-utility1-error-message"></span>
                                <div id="other_name" style="margin-top: 5px"></div>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12">
                            <div class="form-group">
                                <label>Contact Number</label>
                                <input type="text" name="contact" class="form-control" id="brand-contact_number" data-parsley-required='true' data-parsley-type="digits" data-parsley-type-message="This field must only contain numbers" data-parsley-length="[10,10]" data-parsley-length-message="This field must be exactly 10 characters long" >
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
                            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                <div class="btn-group">
                                    <button type="submit" id="contact-sbt-btn" class="btn btn-green">Save</button>
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
    $('body').on('click', '.brand-contact-create-modal', function(e) {
        $(".ajax-error-message").html('');
        $(".help-block").remove('');
        $("#brand-contact-create-form label").addClass('yesstar');
        var action_type = $(this).data('type');

        var title = $(this).data('original-title');
        $('#brand-contact-create-modal .modal-title').html(title);


        $("#brand-contact-create-form")[0].reset();
        $('#brand-contact-create-form').parsley().reset();

        if (action_type == 'new') {
            $('#brand-contact-id').val('');
            $('.brand-contact_name_class').attr('value','');
        } else {
            var id = $(this).data('id');
            var name = $(this).closest('tr').find('td:eq(1)').html();
            var number = $(this).closest('tr').find('td:eq(2)').html();
            
            $('#brand-contact-id').val(id);
            $('.brand-contact_name_class').attr('value',name);
            // if ($('#brand-contact_name').val() == null) {
            //     $('#brand-contact_name').val('other').trigger('change');   
            //     setOtherContactName(name);              
            // }
            $('#brand-contact_number').val(number);
        }
        $('#brand-contact-create-modal').modal();

    });
    $(document).ready(function() {
        $("#brand-contact-create-form").submit(function(e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.

            var form = $(this);
            var url = form.attr('action');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(response) {
                    $('#brand-contact-create-modal').modal("hide");
                    if (response.status == 'success') {
                        printAjaxSuccessMsg(response.message);
                    } else {
                        printAjaxErrorMsg(response.message);
                    }
                    $('#brand-contact-table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    if (xhr.status == 422) {
                        printErrorMsgNew(form,xhr.responseJSON.errors);
                    }
                }
            });
        });

        // $("#brand-contact_name").change(function(e) {
        //     if($(this).val() == 'other') {
        //         $(this).attr('name','');
        //         setOtherContactName();
        //     } else {
        //         $(this).attr('name','name');
        //         $("#other_name").html('');
        //     }
        // });

        $("#contact-sbt-btn").click(function(e) {
            $(".help-block").remove('');
        });
    });

    function setOtherContactName(name=null)
    {
        $("#other_name").html('');
        if(name == null ){
            $("#other_name").append('<label class="yesstar">Enter brand name</label><input type="text" name="name" class="form-control" data-parsley-required="true">');    
        } else {
            $("#other_name").append('<label class="yesstar">Enter brand name</label><input type="text" name="name" class="form-control" data-parsley-required="true" value="'+name+'">'); 
        }
    }
</script>
@endpush
