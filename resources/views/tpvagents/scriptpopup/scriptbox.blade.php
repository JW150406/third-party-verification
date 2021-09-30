@if(Auth::user()->access_level=='tpvagent' && $twilio_id != '' )


<div id="agent-script-box" style="display:none">		
<header class="clearfix">			
			<h4 >Script</h4>

		 
		</header>
		<div class="chat" style="display:none">			
			<div class="user-chat-history">				
				<div class="chat-message clearfix">					
				  <div class="chat-message-content clearfix">						
                   <!-- <section class="log">
                      <textarea id="log" readonly="true"></textarea>
                    </section> -->
                      <div class="row" id="content_to_read">
                       
                     </div>
                     <div class="row" >
                       <button class="script_for_confirmation btn btn-success">Next</button>
                     </div>
                                  

					</div> <!-- end chat-message-content -->
				</div> <!-- end chat-message -->    
				 
				 
			</div> <!-- end user-chat-history -->
			 	 
		</div> <!-- end chat -->
	</div> <!-- end user-status-box -->
  
 @endif