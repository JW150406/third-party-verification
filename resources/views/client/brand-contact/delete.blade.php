<div class="modal fade confirmation-model" id="delete-brand-contact-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form  id="delete-brand-contact-form" method="POST">                
                {{ csrf_field() }}
                {{ method_field('DELETE') }}

                <div class="modal-body">
                    <div class="mt15 text-center mb15">
				<?php echo getimage('/images/alert-danger.png') ?>
				<p class="logout-title">Are you sure?</p>
			</div>
                    <div class="mt20 text-center">
                        Brand contact - <strong class="delete-brand-contact-name"></strong> will be deleted.
                    </div>
                </div>
                <div class="modal-footer">

                    <div class="btnintable bottom_btns pd0">
                        <button type="submit" class="btn btn-green">Confirm</button>
                        <button type="button" class="btn btn-red" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script type="text/javascript">
	$('body').on('click','.delete-brand-contact',function(e){
		var name = $(this).closest('tr').find('td:eq(1)').html();
		$('.delete-brand-contact-name').html(name);
		$("#delete-brand-contact-form").attr('action',$(this).data('url'));
		$('#delete-brand-contact-modal').modal();
	});
	
    $(document).ready(function() {
    	$("#delete-brand-contact-form").submit(function(e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.
            var form = $(this);
            var url = form.attr('action');

            $.ajax({
                type: "POST",
                url: url,
    	        data: form.serialize(),
    	        success: function(response)
    	        {
    	            $('#delete-brand-contact-modal').modal("hide");
    	            if (response.status == 'success') {
    	            	printAjaxSuccessMsg(response.message);	
    	            } else {
    	            	printAjaxErrorMsg(response.message);	
    	            }
    	            
    	            $('#brand-contact-table').DataTable().ajax.reload();
    	        }
            });
        });
    });
</script>
@endpush('scripts')