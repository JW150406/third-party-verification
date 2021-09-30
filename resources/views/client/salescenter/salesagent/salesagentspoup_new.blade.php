<div class="modal fade confirmation-model" id="change-status-agent-modal">
    <div class="modal-dialog">
        <div class="modal-content">

            @if(Request::route()->getName() == 'admin.all.agents')
            <form action="{{ route('agent.user.changeUserStatusForAllAgent')}}" method="POST" id="change-status-agent-form">
            @else
            <form action="{{ route('agent.user.changeUserStatus')}}" method="POST" id="change-status-agent-form">
            @endif
                {{ csrf_field() }}
                <input type="hidden" name="status" id="status_sales_agent_user">
                <input type="hidden" name="id" id="agent_user_id">
                <input type="hidden" name="name" id="agent_user_name">

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
                    @else  
                        Sales agent
                    @endif <strong class="status-change-clientuser"></strong> will be <span class="status-to-change-text"></span>.
                    </div>
                    <div class="form-group deactivate-reason mt10">

                        <p>(Blacklisted agents cannot be reactivated and are not eligible for rehire.)</p>

                        <div class="form-group mt10 deactivate-reason pd15">
                            <label for="styled-checkbox-1">Reason for deactivation</label>
                            <textarea class="form-control"  rows="5" name="comment"></textarea>
                        </div>
                    </div>

                    <div class="ajax-error-message"></div>

                </div>

                <div class="modal-footer pd0">
                    <div class="btnintable bottom_btns pd0">
                    <div class="bl-btn deactivate-reason pull-left">
                        <input class="styled-checkbox" id="is_black" type="checkbox" name="is_block" value="1">
                        <label for="is_black">Blacklist</label>
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

<div class="modal fade confirmation-model" id="blacklist-info-agent-modal">
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
                <p class="text-center mt20"><strong>This user has been blacklisted. Please contact your Administrator for further assistance. </strong></p>
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
    $('body').on('click', '.deactivate-salescentersaleuser', function(e) {
        $(".help-block").remove('');
        $(".ajax-error-message").html('');
        $("#change-status-agent-form")[0].reset();
        var id = $(this).data('id');
        var user_name = $(this).data('name');
        $('#agent_user_id').val(id);
        $('.status-change-clientuser').html(user_name);
        $('.main-title').html('deactivate : ' + user_name);
        $('.status-to-change-text').html('deactivated');
        $('#status_sales_agent_user').val('inactive');
        $('.deactivate-reason').show();
        $('#change-status-agent-modal').modal();

    });

    $('body').on('click', '.activate-salescentersaleuser', function(e) {
        $(".help-block").remove('');
        $(".ajax-error-message").html('');
        $("#change-status-agent-form")[0].reset();
        var is_block = $(this).data('is-block');
        if (is_block == 1) {
            $('#blacklist-info-agent-modal').modal();
            return;
        }
        var id = $(this).data('id');
        var user_name = $(this).data('name');
        $('#agent_user_id').val(id);
        $('.status-change-clientuser').html(user_name);
        $('.main-title').html('activate : ' + user_name);
        $('.status-to-change-text').html('activated');
        $('#status_sales_agent_user').val('active');
        $('.deactivate-reason').hide();
        $('#change-status-agent-modal').modal();
    });

    $('body').on('click', '.delete_sales_agent', function(e) {
        // $(".help-block").remove('');
        // $(".ajax-error-message").html('');
        // $("#change-status-agent-form")[0].reset();
        var id = $(this).data('id');
        var user_name = $(this).data('name');
        $('#agent_user_id').val(id);
        $('.status-change-clientuser').html(user_name);
        $('#agent_user_name').val(user_name);
        
        $('.status-to-change-text').html('deleted');
        $('#status_sales_agent_user').val('delete');
        $('.deactivate-reason').hide();
        $('#change-status-agent-modal').modal();
    });

    $(document).ready(function() {
        $("#change-status-agent-form").submit(function(e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.
            var form = $(this);
            var url = form.attr('action');
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(response) {
                    $('#change-status-agent-modal').modal("hide");
                    if (response.status == 'success') {
                        printAjaxSuccessMsg(response.message);
                    } else {
                        printAjaxErrorMsg(response.message);
                    }
                    $('#agent-table,#all-agent-table').DataTable().ajax.reload();
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