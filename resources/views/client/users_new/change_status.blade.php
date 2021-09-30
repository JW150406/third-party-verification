<div class="modal fade confirmation-model" id="change-status-users-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            @if(Request::route()->getName() == 'admin.all.agents')
            <form action="{{ route('agent.user.changeUserStatusForAllAgent')}}" method="POST" id="change-status-users-form">
            @else
            <form action="{{ route('client.user.changeUserStatus')}}" method="POST" id="change-status-users-form">
            @endif
                <input type="hidden" value="" name="status" id="status_to_change_user">
                <input type="hidden" value="" name="id" id="client_user_id">
                <input type="hidden" value="" name="name" id="client_user_name">
                {{ csrf_field() }}

                <!-- <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title main-title"> Deactivate </h4> 
                </div> -->


                <div class="modal-body">

                    <div class="mt15 text-center mb15">
				<?php echo getimage('/images/alert-danger.png') ?>
				<p class="logout-title">Are you sure?</p>
			</div>
                    <div class="mt20 text-center">
                        @if(Request::route()->getName() == 'admin.all.agents')
                            Agent 
                        @elseif(Request::route()->getName() == 'tpvagents.index')
                            TPV agent 
                        @else 
                            User
                        @endif 
                        <strong class="status-change-clientuser"></strong> will be <span class="status-to-change-text"></span>.
                    </div>
                    
                    <div class="form-group deactivate-reason mt10">
                        <label for="styled-checkbox-1">Reason for deactivation</label>
                        <textarea class="form-control" rows="5" name="comment" id="comment"></textarea>

                    </div>
                    <div class="ajax-error-message"></div>

                </div>


                <div class="modal-footer pd0">

                    <div class="btnintable bottom_btns pd0">
                        <div class="bl-btn deactivate-reason pull-left">
                            <input class="styled-checkbox" id="is_block" type="checkbox" name="is_block" value="1">
                            <label for="is_block">Blacklist</label>
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
<div class="modal fade confirmation-model" id="blacklist-info-users-modal">
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
@push('scripts')
<script>
    $('body').on('click', '.deactivate-client-user', function(e) {
        $(".ajax-error-message").html('');
        $(".help-block").remove('');
        $("#change-status-users-form")[0].reset();
        var id = $(this).data('id');
        var user_name = $(this).data('name');
        $('#client_user_id').val(id);
        $('.status-change-clientuser').html(user_name);
        $('.main-title').html('deactivate : ' + user_name);
        $('.status-to-change-text').html('deactivated');
        $('#status_to_change_user').val('inactive');
        $('.deactivate-reason').show();
        $('#change-status-users-modal').modal();

    });

    $('body').on('click', '.activate-client-user', function(e) {
        $(".ajax-error-message").html('');
        $(".help-block").remove('');
        $("#change-status-users-form")[0].reset();
        var is_block = $(this).data('is-block');
        if (is_block == 1) {
            $('#blacklist-info-users-modal').modal();
            return;
        }
        var id = $(this).data('id');
        var user_name = $(this).data('name');
        $('#client_user_id').val(id);
        $('.status-change-clientuser').html(user_name);
        $('.main-title').html('activate : ' + user_name);
        $('.status-to-change-text').html('activated');
        $('#status_to_change_user').val('active');
        $('.deactivate-reason').hide();
        $('#change-status-users-modal').modal();
    });

    $('body').on('click', '.delete_tpv_agent', function(e) {
        $(".ajax-error-message").html('');
        $(".help-block").remove('');
        $("#change-status-users-form")[0].reset();
        var id = $(this).data('id');
        var user_name = $(this).data('name');
        $('#client_user_id').val(id);
        $('#client_user_name').val(user_name);
        $('.status-change-clientuser').html(user_name);
        $('.status-to-change-text').html('deleted');
        $('#status_to_change_user').val('delete');
        $('.deactivate-reason').hide();
        $('#change-status-users-modal').modal();
    });

    $(document).ready(function() {
        $("#change-status-users-form").submit(function(e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.
            var form = $(this);
            var url = form.attr('action');
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(response) {
                    $('#change-status-users-modal').modal("hide");
                    if (response.status == 'success') {
                        printAjaxSuccessMsg(response.message);
                    } else {
                        printAjaxErrorMsg(response.message);
                    }
                    $('#client-user-table,#all-user-table,#tpv-user-table,#all-agent-table,#tpv-agent-table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    if (xhr.status == 422) {
                        printErrorMsgNew(form, xhr.responseJSON.errors);
                    }
                }
            });
        });
    });
</script>
    @endpush
