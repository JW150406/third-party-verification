<div class="modal fade confirmation-model" id="change-status-users-modal1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('salescenter.users.changeUserStatus')}}" method="POST" id="change-status-users-form1" data-parsley-validate>
                <input type="hidden" value="" name="status" id="status_to_change_user1">
                <input type="hidden" value="" name="id" id="salescenter_user_id">
                {{ csrf_field() }}

                <div class="modal-body">

                    <div class="mt15 text-center mb15">
				<?php echo getimage('/images/alert-danger.png') ?>
				<p class="logout-title">Are you sure?</p>
			</div>
                    <div class="mt20 text-center">
                        User <strong class="status-change-salescenteruser"></strong> will be <span class="status-to-change-text"></span>.
                    </div>
                    
                    <div class="form-group deactivate-reason">
                        <label for="comment">Reason for deactivation</label>
                        <textarea class="form-control required" autocomplete="off"  rows="5" name="comment" id="comment1"  data-parsley-required="true" data-parsley-required-message="Please enter reason for deactivation"></textarea>

                    </div>

                </div>


                <div class="modal-footer pd0">
                    <div class="btnintable bottom_btns pd0 mb30">
                        <div class="bl-btn deactivate-reason pull-left">
                            <input class="styled-checkbox" id="is_block1" type="checkbox" name="is_block" value="1">
                            <label for="is_block1">Blacklist</label>
                        </div>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-green">Confirm</button>
                            <button type="button" class="btn btn-red" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade confirmation-model" id="blacklist-info-users-modal1">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"> Info </h4> 
            </div> -->

            <div class="modal-body">
                <div class="mt15 text-center mb15">
				<?php echo getimage('/images/alert-danger.png') ?>
				<p class="logout-title">Are you sure?</p>
			</div>
                <p class="text-center mt20"><strong>This user has been blacklisted. Please contact your administrator. </strong></p>
            </div>

            <div class="modal-footer pd0">
                <div class="btnintable bottom_btns pd0">
                    <div class="btn-group">
                        <button type="button" class="btn btn-red" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('body').on('click', '.deactivate-salescenter-user', function(e) {
        $(".help-block").remove('');
        $("#change-status-users-form1")[0].reset();
        $('#change-status-users-form1').parsley().reset();
        $('#comment1').attr('data-parsley-required', 'true');
        var id = $(this).data('id');
        var user_name = $(this).data('name');
        $('#salescenter_user_id').val(id);
        $('.status-change-salescenteruser').html(user_name);
        $('.main-title').html('deactivate : ' + user_name);
        $('.status-to-change-text').html('deactivated');
        $('#status_to_change_user1').val('inactive');
        $('.deactivate-reason').show();
        $('#change-status-users-modal1').modal();

    });

    $('body').on('click', '.activate-salescenter-user', function(e) {
        $(".ajax-error-message").html('');
        $(".help-block").remove('');
        $("#change-status-users-form1")[0].reset();
        $('#comment1').attr('data-parsley-required', 'false');
        var is_block = $(this).data('is-block');
        if (is_block == 1) {
            $('#blacklist-info-users-modal1').modal();
            return;
        }
        var id = $(this).data('id');
        var user_name = $(this).data('name');
        $('#salescenter_user_id').val(id);
        $('.status-change-salescenteruser').html(user_name);
        $('.main-title').html('activate : ' + user_name);
        $('.status-to-change-text').html('activated');
        $('#status_to_change_user1').val('active');
        $('.deactivate-reason').hide();
        $('#change-status-users-modal1').modal();
    });

    $('body').on('click', '.delete_salescenter_user', function(e) {
        $(".ajax-error-message").html('');
        $(".help-block").remove('');
        $("#change-status-users-form1")[0].reset();
        $('#comment1').attr('data-parsley-required', 'false');
        var id = $(this).data('id');
        var user_name = $(this).data('name');
        $('#salescenter_user_id').val(id);
        $('.status-change-salescenteruser').html(user_name);
        $('.status-to-change-text').html('deleted');
        $('#status_to_change_user1').val('delete');
        $('.deactivate-reason').hide();
        $('#change-status-users-modal1').modal();
    });

    $(document).ready(function() {
        $("#change-status-users-form1").submit(function(e) {
            console.log($('#change-status-users-form1').parsley().isValid());
            if($('#change-status-users-form1').parsley().isValid()) {
                e.preventDefault(); // avoid to execute the actual submit of the form.
                var form = $(this);
                var url = form.attr('action');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(), // serializes the form's elements.
                    success: function(response) {
                        $('#change-status-users-modal1').modal("hide");
                        if (response.status == 'success') {
                            printAjaxSuccessMsg(response.message);
                        } else {
                            printAjaxErrorMsg(response.message);
                        }
                        $('#sales-center-user-table,#all-user-table').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        if (xhr.status == 422) {
                            printErrorMsgNew(form, xhr.responseJSON.errors);
                        }
                    }
                });
            }
        });
    });
</script>