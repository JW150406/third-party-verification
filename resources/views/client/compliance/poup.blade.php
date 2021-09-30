<div class="modal fade confirmation-model" id="DeleteComplianceTemplate">
		<div class="modal-dialog">
			<div class="modal-content">
            <form action="{{ route('utility.delete-compliance-template',['client_id' => $client_id, 'utility_id' => $utility->id]) }}" method="POST"  >
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
$('body').on('click','.delete-compliance-template',function(e){
    $('#DeleteComplianceTemplate').modal();
     var cid = $(this).data('cid');
     var id = $(this).data('id');

     var formname = $(this).data('formname');
     $('#DeleteComplianceTemplate #client_id').val(cid);
     $('#DeleteComplianceTemplate #formtodelete').val(id);
     $('.status-change-formname').html(formname);

});

</script>
