<div class="team-addnewmodal">
    <div class="modal fade" id="customer-type-create-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add New Customer Type</h4>
                </div>
                <div class="modal-body">
                    <div class="ajax-error-message">
                    </div>
                    <form class="row" action="{{ route('customerType.store')}}" method="POST" id="customer-type-create-form" data-parsley-validate>
                        @csrf
                        <input type="hidden" name="id" id="client-customer-type-id">
                        <input type="hidden" name="client_id" value="{{$client_id}}">
                       
                        <div class="clearfix"></div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label for="customer-type-name" class="yesstar">Name</label>
                                <input id="customer-type-name" autocomplete="off" type="text" class="form-control required" name="name" data-parsley-required='true'  data-parsley-minlength-message='This field must contain at least 2 characters'  data-parsley-minlength="2" data-parsley-minlength="255">
                            </div>
                        </div>
                        
                        <div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
                            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-green">Save</button>
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
    $('body').on('click', '.customer-type-create-modal', function(e) {
        $(".ajax-error-message").html('');
        $(".help-block").remove('');
        $(".help-block").remove('');
        var action_type = $(this).data('type');
        
        var title = $(this).data('original-title');
        $('#customer-type-create-modal .modal-title').html(title);

        if (action_type == 'new') {
            $('#client-customer-type-id').val('');
            $("#customer-type-create-form")[0].reset();
        } else {
            var id = $(this).data('id');
            var name = $(this).closest('tr').find('td:eq(1)').html();
            $('#client-customer-type-id').val(id);           
            $('#customer-type-name').val(name);
        }
        $('#customer-type-create-modal').modal();

    });
    $(document).ready(function() {
        $("#customer-type-create-form").submit(function(e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.

            var form = $(this);
            var url = form.attr('action');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(response) {
                    $('#customer-type-create-modal').modal("hide");
                    if (response.status == 'success') {
                        printAjaxSuccessMsg(response.message);    
                    } else {
                        printAjaxErrorMsg(response.message);
                    }
                    $('#customer-type-table').DataTable().ajax.reload();
                    getCustomerType();
                },
                error: function(xhr) {
                    if (xhr.status == 422) {
                        printErrorMsgNew(form,xhr.responseJSON.errors);
                    }
                }
            });
        });
    });
</script>
@endpush

