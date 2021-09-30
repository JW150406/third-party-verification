<div class="modal fade confirmation-model" id="DeleteclientUser">
		<div class="modal-dialog">
			<div class="modal-content">
            <form action="{{ route('client.user.update', $client_id)}}" method="POST"  >
             <input type="hidden" value="" name="status" id="status_to_change" >
             <input type="hidden" value="" name="userid" id="userid" >
            {{ csrf_field() }}
            {{ method_field('POST') }}
                                                 
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Client User Action</h4>
				</div>
				
				<div class="modal-body">
                   Are you sure you want to <span class="status-to-change-text"></span> <strong class="status-change-clientusername"></strong>.
                 </div>
				
				<div class="modal-footer">	
                    <button type="submit" class="btn btn-success">Confirm</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				
                </div>
                </form>
			</div>
		</div>
    </div>
<script>
$('body').on('click','.deactivate-clientuser',function(e){
    $('#DeleteclientUser').modal();
     var uid = $(this).data('uid');
     var clientusername = $(this).data('clientusername');
     $('#userid').val(uid);
     $('#status_to_change').val('inactive');
     $('.status-to-change-text').html('deactivate');
     $('.status-change-clientusername').html(clientusername);
     
 
});
$('body').on('click','.activate-clientuser',function(e){
    $('#DeleteclientUser').modal();
     var uid = $(this).data('uid');
     var clientusername = $(this).data('clientusername');
     $('#userid').val(uid);
     $('#status_to_change').val('active');
     $('.status-to-change-text').html('activate');
     $('.status-change-clientusername').html(clientusername);
});
</script>
 