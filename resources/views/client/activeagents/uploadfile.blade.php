<div class="modal fade confirmation-model" id="uploadfileforsalesagent">
		<div class="modal-dialog">
			<div class="modal-content">
            <form action="" enctype="multipart/form-data" method="POST" id="action-for-salesagent"  >
               
               <input type="hidden" value="" name="userid" id="userid" >
            {{ csrf_field() }}
            {{ method_field('POST') }}
                                                 
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Add Files</h4>
				</div>
				
				<div class="modal-body">
                You want to upload a document for  <span class="status-to-change-text"></span> <strong class="status-change-salescentersaleuser"></strong>.

                   <div class="form-group " style="margin-top:25px;">
								<label for="agentdoc"> Upload file </label> 
                             <input id="agentdoc" class="file2 inline btn btn-purple" data-label='Browse<span class="browse"><?php  echo getimage("images/browse_w.png"); ?></span>' name="agentdoc"  type="file"> 
                              @if ($errors->has('agentdoc'))
                              <span class="help-block">
                              <strong>{{ $errors->first('agentdoc') }}</strong>
                              </span>
                              @endif  
                   </div>
                 </div>
                 
				
				<div class="modal-footer">
                    	<button type="submit" class="btn btn-success">Upload</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				
                </div>
                </form>
			</div>
		</div>
    </div>
 <script>
  $('body').on('click','.uploaddocument-to-salesagent',function(e){
        $('#uploadfileforsalesagent').modal();
        var uid = $(this).data('uid');
        var salescentersaleuser = $(this).data('salescentersaleuser');
        $('#uploadfileforsalesagent #userid').val(uid); 
        $('.status-change-salescentersaleuser').html(salescentersaleuser);
        
    
   }); 
   </script>   
 