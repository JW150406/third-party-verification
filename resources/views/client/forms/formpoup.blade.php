<div class="modal fade confirmation-model" id="DeleteclientForm">
		<div class="modal-dialog">
			<div class="modal-content">
            <form action="{{ route('client.delete-contact-form',$client_id) }}" method="POST"  >
              <input type="hidden" value="" name="id" id="formtodelete" >
              <input type="hidden" value="" name="cid" id="client_id" >
               {{ csrf_field() }}
               {{ method_field('POST') }}
                                                 
                
				
				<div class="modal-body">
                <div class="mt15 text-center mb15">
				<?php echo getimage('/images/alert-danger.png') ?>
				<p class="logout-title">Are you sure?</p>
			</div>
                Are you sure you want to delete <strong class="status-change-formname"></strong>?
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
$('body').on('click','.delete-form',function(e){
    $('#DeleteclientForm').modal();
     var cid = $(this).data('cid');
     var id = $(this).data('id');
     
     var formname = $(this).data('formname');
     $('#DeleteclientForm #client_id').val(cid);
     $('#DeleteclientForm #formtodelete').val(id);
     $('.status-change-formname').html(formname);    
 
});
 
</script>
 