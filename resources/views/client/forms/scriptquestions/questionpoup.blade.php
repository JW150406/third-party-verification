<div class="modal fade confirmation-model" id="DeleteclientFormScriptQuestion">
		<div class="modal-dialog">
			<div class="modal-content">
            <form action="{{ route('client.delete-script-question',['client_id' => $client_id, 'form_id' => $form_id,'script_id' => $script_id ]) }}" method="POST"  >
              <input type="hidden" value="" name="question_id" id="question_id" >
              
               {{ csrf_field() }}
               {{ method_field('POST') }}
                                                 
                
				
				<div class="modal-body">
                <div class="mt15 text-center mb15">
				<?php echo getimage('/images/alert-danger.png') ?>
				<p class="logout-title">Are you sure?</p>
			</div>
                Are you sure you want to delete <strong class="status-change-question"></strong>?
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
$('body').on('click','.delete-question',function(e){
    $('#DeleteclientFormScriptQuestion').modal();
     var id = $(this).data('id');
     var formname = $(this).data('questioname');
     $('#DeleteclientFormScriptQuestion #question_id').val(id);
     $('.status-change-question').html(formname);     
});
 
</script>
 