<div class="modal fade confirmation-model" id="DeleteRole">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ url('admin/roles/destroy') }}" method="POST">
                <input type="hidden" value="" name="id" id="roleid">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"> Role Action</h4>
                </div>

                <div class="modal-body">
                    Are you sure you want to delete Role <strong class="status-change-role"></strong>.
                </div>

                <div class="modal-footer">
                    <div class="btnintable bottom_btns pd0">
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

<script>
    $('body').on('click', '.deleterole', function(e) {
        $("#DeleteRole").modal()
        var roleid = $(this).data('roleid');
        var role = $(this).data('role');
        $('#DeleteRole #roleid').val(roleid);
        $('.status-change-role').html(role);


    });
</script>