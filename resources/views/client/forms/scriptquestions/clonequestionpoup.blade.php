<div class="modal fade confirmation-model" id="clonequestionpopup">
		<div class="modal-dialog">
			<div class="modal-content">
            <form action="{{ route('client.clone-script-question',['client_id' => $client_id, 'form_id' => $form_id,'script_id' => $script_id ]) }}" method="POST"  >
              <input type="hidden" value="" name="question_id" id="question_id" >
              <input type="hidden" value="{{ $state }}" name="clonedfromstate" id="clonedfromstate" >
              <input type="hidden" value="{{ $commodity }}" name="clonedfromcommodity" id="clonedfromcommodity" >
              <input type="hidden" value="{{ $script_id }}" name="scripttoclone" id="scripttoclone" >
  
             
              
               {{ csrf_field() }}
               {{ method_field('POST') }}
                                                 
                                                 
				
				
				<div class="modal-body">
                <div class="mt15 text-center mb15">
				<?php echo getimage('/images/alert-danger.png') ?>
				<p class="logout-title">Are you sure?</p>
			</div>
                <p class="sure-text"> Are you sure you want to clone this question?</p>
                  <div class="clearfix"> </div>
                    <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-6">
                                    <p>Select State to clone</p>
                                    <div class="dropdown {{ $errors->has('name') ? ' has-error' : '' }}">
                                        <select class="selectsearch select-box-admin" name="state" id="state" >
                                                <option value="">Select</option>
                                                @if(count($states)>0)
                                                @foreach($states as $state_data)
                                                    <option value="{{$state_data->state}}"  <?php if($state_data->state == $state) echo "selected='selected'"; ?>>{{$state_data->state}}</option>
                                                @endforeach
                                                @endif
                                        </select>
                                        @if ($errors->has('name'))
                                                <span class="help-block">
                                                <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                            </div>
                            @if($form_detail->commodity_type == 'DualFuel')
                            <input type="hidden" name="commodity" value="Dual Fuel">
                            @else
                            <div class="col-xs-12 col-sm-6 col-md-6">
                                    <p>Select Commodity</p>
                                    <div class="dropdown {{ $errors->has('state') ? ' has-error' : '' }}">
                                        <select class="selectsearch select-box-admin" name="commodity" id="commodity" >
                                             
                                            <option value="Electric"  <?php if('Electric' == $commodity) echo "selected='selected'"; ?>>Electric</option>
                                            <option value="Gas"  <?php if('Gas' == $commodity) echo "selected='selected'"; ?>>Gas</option>
                                            
                                        </select>
                                        @if ($errors->has('commodity'))
                                                <span class="help-block">
                                                <strong>{{ $errors->first('commodity') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                            </div>
                            @endif
                    </div>
                
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
$('body').on('click','.clone-script',function(e){
    $('#clonequestionpopup').modal();
     var id = $(this).data('id');
    
     $('#clonequestionpopup #question_id').val(id);
       
});
 
</script>
 