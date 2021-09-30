<template id='email-template'>
	<div class="col-xs-12 col-sm-12 col-md-12 cloned-email">
		<div class="col-md-3">
			<div class="form-group">
				<input type="email" class="form-control email-text-class" name="email[]" data-id="id" data-parsley-required="true"  data-parsley-email data-parsley-email-message="Please enter valid email Id">
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<select class="select2 form-control1-select1 alert-select-class" name="email_alert_level[]" id="email-alert-level" data-parsley-required="true" >
				@foreach(config()->get('constants.alert_level') as $i => $val)
					<option value="{{$i}}">{{$val}}</option>
				@endforeach
				</select>
			</div>
		</div>
		<!-- sales-location-enabled : this class is used for set design of selected data to display proper in multiselect dropdown  -->
		<div class="col-md-2">
			<div class="form-group sales-location-enabled">
				<select class="form-control location-select-class" name="locations[]" multiple="multiple" data-parsley-required="true" >
					<option value="{{$client->id}}" selected>{{$client->name}}</option>
				</select>
				<span class="locations_err"></span>
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group sales-location-enabled">
				<select class="form-control email-alert-for-class" name="email_alert_for[]" multiple="multiple" data-parsley-required="true">
					@foreach(config()->get('constants.alert_for') as $i => $val)
						<option value="{{$i}}">{{$val}}</option>
					@endforeach
				</select>
				<span class="alert_for_err"></span>
			</div>
		</div>
		<div class="col-md-2 col-xs-12 col-sm-12">
			<div class="form-group">
				<a class="remove_field1" style="color:red; cursor:pointer;"><i class="fa fa-remove"></i></a>
			</div>
		</div>
	</div>
</template>
{{--
<template id='sms-template'>
	<div class="col-xs-12 col-sm-12 col-md-12 cloned-sms">
		<div class="col-md-3">
			<div class="form-group">
				<input type="text" class="form-control sms-text-class" name="sms[]" data-parsley-required="true" data-parsley-pattern="[0-9]{10}" data-parsley-pattern-message="Please enter 10 digit number">
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				<select class="select2 form-control1-select1 alert-sms-select-class" name="sms_alert_level[]">
				@foreach(config()->get('constants.alert_level') as $i => $val)
					<option value="{{$i}}">{{$val}}</option>
				@endforeach
				</select>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group sales-location-enabled">
				<select class="form-control location-sms-select-class" name="locations-sms[]" multiple="multiple">
					<option value="{{$client->id}}" selected>{{$client->name}}</option>
				</select>
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group sales-location-enabled">
				<select class="select2 form-control1-select1 sms-alert-for-class" name="sms_alert_for[]" id="sms-alert-for">
					@foreach(config()->get('constants.alert_for') as $i => $val)
						<option value="{{$i}}">{{$val}}</option>
					@endforeach
				</select>
			</div>
		</div>
		<div class="col-md-2 col-xs-12 col-sm-12">
			<div class="form-group">
				<a class="remove_field1_sms" style="color:red; cursor:pointer;"><i class="fa fa-remove"></i></a>
			</div>
		</div>
	</div>
</template>
--}}
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12">
		<div class="mt20">
			<div class="col-md-10 col-sm-10">
				<h4>Email Alerts</h4>
			</div>
			<div class="col-md-2 col-sm-2">
				<div class="form-group">
					@if(auth()->user()->hasPermissionTo('edit-alerts'))
					<button class="btn btn-green emailAdd" data-id="1" onClick="showMoreEmailDiv()" style="min-width: 1px;">Add</button>
					@endif
				</div>
			</div>
		</div>
	</div>
	<form method="POST" action="{{route('admin.fruadalert.store')}}" data-parsley-validate>
		{{csrf_field()}}
		<input type="hidden" name="tab" value="fraud_alerts">
		<input type="hidden" value="{{$client->id}}" name="clientId">
		<div class="col-xs-12 col-sm-12 col-md-12 cloned-email" >
			<div class="col-md-3">
				<div class="form-group">
					<label class="yesstar">Email</label>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="yesstar">Alert Level</label>
				</div>
			</div>
			<div class="col-md-2">				
				<div class="form-group">
					<label class="yesstar">Select</label>
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label class="yesstar">Alert For</label>
				</div>
			</div>
		</div>
		@if(count($emailAlert) >= 1)
		@foreach($emailAlert as $key => $value)
			<div class="col-xs-12 col-sm-12 col-md-12" id="removed_email-{{$key+1}}">
				<input type="hidden" value="{{$value->id}}" name="fid-{{$key+1}}[]" id="fid-{{$key+1}}">
				<div class="col-md-3">
					<div class="form-group">
						<input type="email" class="form-control email-text-class" value="{{$value->email}}" name="edit_email[]" data-id="id" data-parsley-required="true"  data-parsley-email data-parsley-email-message="Please enter valid email Id" @if(!auth()->user()->hasPermissionTo('edit-alerts')) readonly @endif>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<select class="select2 form-control1-select1 edit-alert-select-class" name="edit_email_alert_level[]" id="edit-level-{{$key+1}}" data-parsley-required="true">
						@foreach(config()->get('constants.edit_alert_level') as $k => $val)
							<option value="{{$val['key']}}" @if($value->alert_level == $val['key']) selected @endif @if(!auth()->user()->hasPermissionTo('edit-alerts')) disabled @endif>{{$val['name']}}</option>
						@endforeach
						</select>
					</div>
				</div>
				@php
					$selectedCenters = [];
					if($value->alert_level == "salescenter"){
						$locations = \App\models\Salescenter::select('id','name')->where([
						['status', '=', 'active'] ,['client_id', '=', $client->id]
						])->get();
						$selectedCenters = explode(",", $value->salescenter_id);
					}
					elseif($value->alert_level == "sclocation"){
						$locations = \App\models\Salescenterslocations::select('id','name')->where([
						['status', '=', 'active'] ,['client_id', '=', $client->id ]
						])->get();
						$selectedCenters = explode(",", $value->location_id);
					}
					elseif($value->alert_level == "client"){
						$locations = \App\models\Client::select('id','name')->where([
						['status', '=', 'active'],['id','=',$client->id]                                
						])->get();
						$selectedCenters = array($value->client_id);
					}
				@endphp
				<div class="col-md-2">
					<div class="form-group sales-location-enabled">
						<select class="form-control edit-location-select-class" name="edit_locations-{{$key+1}}[]" id="edit-location-{{$key+1}}" multiple="multiple" data-parsley-required="true" data-parsley-errors-container="#locations_err-{{$key+1}}" >
							@foreach($locations as $k => $val)
								<option value="{{$val->id}}" <?php if (in_array($val->id, $selectedCenters)) { ?> selected <?php } ?> @if(!auth()->user()->hasPermissionTo('edit-alerts')) disabled @endif>{{$val->name}}</option>
							@endforeach
						</select>
						<span id="locations_err-{{$key+1}}"></span>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group sales-location-enabled">
						@php
						$alertsFor = explode(",", $value->alert_for);
						@endphp
						<select class="form-control1-select1 edit-alert-for" name="edit_alerts_for[{{$key}}][]" id="edit-email-alert-for-{{$key+1}}" multiple="multiple" data-parsley-required="true" data-parsley-errors-container="#alert-for-err-{{$key+1}}">
							@foreach(config()->get('constants.alert_for') as $i => $val)
								<option value="{{$i}}" @if (in_array($i, $alertsFor)) selected @endif @if(!auth()->user()->hasPermissionTo('edit-alerts')) disabled @endif>{{$val}}</option>
							@endforeach
						</select>
						<span id="alert-for-err-{{$key+1}}"></span>
					</div>
				</div>
				@if(auth()->user()->hasPermissionTo('edit-alerts'))
				<div class="col-md-2 col-xs-12 col-sm-12">
					<div class="form-group">
						<a href="javascript:void(0)" class="remove_field1-email" 
						data-toggle="modal" data-target="#delete-fraudalert-email" 
						title="Delete FraudAlert Email" id="remove-email-{{$key+1}}" style="color:red; cursor:pointer;"><i class="fa fa-remove"></i></a>
					</div>
				</div>
				@endif
			</div>
		@endforeach
		@else
		<!-- <div class="col-md-12" style="text-align: center;">No records found</div> -->
		@endif
		<div class="input_fields_container" id="email-container">
		</div>
		{{--
			<div class="col-xs-12 col-sm-12 col-md-12">
				<div class="mt20">
					<div class="col-md-10 col-sm-10">
						<h4>SMS Alerts</h4>
					</div>
					<div class="col-md-2 col-sm-2">
						<div class="form-group">
							<button class="btn btn-green smsAdd" type="button" data-id="1" onClick="showMoreSmsDiv()" style="min-width: 1px;">Add</button>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 cloned-sms">
					<div class="col-md-3">
						<div class="form-group">
							<label class="yesstar">SMS</label>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="yesstar">Alert Level</label>
						</div>
					</div>
					<div class="col-md-3">				
						<div class="form-group">
							<label class="yesstar">Select</label>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="yesstar">Alert For</label>
						</div>
					</div>
				</div>
			</div>
			@if(count($smsAlert) >= 1)
			@foreach($smsAlert as $j => $values)
				<div class="col-xs-12 col-sm-12 col-md-12" id="removed_sms-{{$j+1}}">
					<input type="hidden" value="{{$values->id}}" name="sms_fid-{{$j+1}}[]" id="sms-fid-{{$j+1}}">
					<div class="col-md-4">
						<div class="form-group">
							<input type="text" class="form-control" name="edit_sms[]" value="{{$values->phone}}" data-parsley-required="true" data-parsley-pattern="[0-9]{10}" data-parsley-pattern-message="Please enter 10 digit number">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<select class="select2 form-control1-select1 edit-alert-sms-select-class" name="edit_sms_alert_level[]" id="edit-smslevel-{{$j+1}}">
								@foreach(config()->get('constants.edit_alert_level') as $k => $val)
								<option value="{{$val['key']}}" @if($values->alert_level == $val['key']) selected @endif >{{$val['name']}}</option>
							@endforeach
							</select>
						</div>
					</div>
					@php 
						if($values->alert_level == "salescenter"){
							$locations = \App\models\Salescenter::select('id','name')->where([
							['status', '=', 'active'] ,['client_id', '=', $client->id]
							])->get();
						}
						elseif($values->alert_level == "sclocation"){
							$locations = \App\models\Salescenterslocations::select('id','name')->where([
							['status', '=', 'active'] ,['client_id', '=', $client->id ]
							])->get();
						}
						elseif($values->alert_level == "client"){
							$locations = \App\models\Client::select('id','name')->where([
							['status', '=', 'active'],['id','=',$client->id]                                
							])->get();
						}
					@endphp
					<div class="col-md-3">
					<div class="form-group sales-location-enabled">
							<select class="form-control edit-location-sms-select-class" name="edit_locations_sms-{{$j+1}}[]" id="edit-smslocation-{{$j+1}}" multiple="multiple">
								@foreach($locations as $k => $val)
									@php
										if($values->salescenter_id)
											$salesArray = explode(",",$values->salescenter_id);
										elseif($values->location_id)
											$salesArray = explode(",",$values->location_id);
									@endphp
									@if(is_array($salesArray) && in_array($val->id,$salesArray))
										<option value="{{$val->id}}" selected>{{$val->name}}</option>
									@elseif($val->id == $client->id)
										<option value="{{$client->id}}" selected>{{$client->name}}</option>
									@else
										<option value="{{$val->id}}">{{$val->name}}</option>
									@endif
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-2 col-xs-12 col-sm-12">
						<div class="form-group">
							<a href="javascript:void(0)" class="remove_field1-sms" 
								data-toggle="modal" data-target="#delete-fraudalert-sms"
								title="Delete FraudAlert sms" id="remove-sms-{{$j+1}}" style="color:red; cursor:pointer;"><i class="fa fa-remove"></i></a>
						</div>
					</div>
				</div>
			@endforeach
			@endif
			<div class="input_fields_container" id="sms-container">
			</div>
		--}}
		@if(auth()->user()->hasPermissionTo('edit-alerts'))
		<div class="col-xs-12 col-sm-12 col-md-12 text-right mt-20" style="padding-right: 100px;">
			<button type="submit" class="btn btn-green"  id="saveAfterDelete" >Save</button>
		</div>
		@endif
	</form>
</div>
{{-- email delete confirmation-box --}}
<div class="modal fade confirmation-model" id="delete-fraudalert-email">
    <div class="modal-dialog">
        <div class="modal-content">
            <form  id="delete-fraudalert-email-form">
                <div class="modal-body">					
            		<div class="mt15 text-center mb15">
						<?php echo getimage('/images/alert-danger.png') ?>
						<p class="logout-title">Are you sure?</p>
					</div>
                    <div class="mt20 text-center">
                        Are you sure you want to delete <strong class="delete-fraudalert-email-name"></strong>?
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="btnintable bottom_btns pd0">
                        <button type="submit" id="confirm_delete" value="1" class="btn btn-green">Confirm</button>
                        <button type="button" id="cancel_delete" value="2" class="btn btn-red" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- sms delete confirmation-box --}}
<div class="modal fade confirmation-model" id="delete-fraudalert-sms">
    <div class="modal-dialog">
        <div class="modal-content">
            <form  id="delete-fraudalert-sms-form">
                <div class="modal-body">
            		<div class="mt15 text-center mb15">
						<?php echo getimage('/images/alert-danger.png') ?>
						<p class="logout-title">Are you sure?</p>
					</div>
                    <div class="mt20 text-center">
                        Are you sure you want to delete <strong class="delete-fraudalert-sms-name"></strong>?
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="btnintable bottom_btns pd0">
                        <button type="submit" id="sconfirm_delete" value="1" class="btn btn-green">Confirm</button>
                        <button type="button" id="scancel_delete" value="2" class="btn btn-red" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(document).ready(function(){	
		$('.location-select-class').multiSelect();
		$('.location-sms-select-class').multiSelect();
		$('.edit-location-select-class').multiSelect();
		$('.edit-location-sms-select-class').multiSelect();
		$('.edit-alert-for').multiSelect();
		/* Email Location store ajax call */
		$(document).on('change','.alert-select-class',function(){
			var eid = $(this).attr('id');
			var splitData = eid.split("-");
			let alertLevel = $(this).val();

			if(alertLevel != 'client')
			{
				$.ajax({
					type:'POST',
					data:{'_token':'{{csrf_token()}}','alertLevel': alertLevel,'clientId':'{{$client->id}}'},
					url:"{{route('findclientsalescenter')}}",
					success:function(response){
						$("#emaillocation-"+splitData[1]).empty();
						$.each(response.data.salescenters, function(key,value){
							$("#emaillocation-"+splitData[1]).append('<option value='+ value.id +'>'+ value.name +'</option>').trigger('change');
							$("#emaillocation-"+splitData[1]).multiSelect('reload'); // init the select	for Email Single Dropdown
						});
					}
				});
			}
			else
			{
				$("#emaillocation-"+splitData[1]).empty();
				$("#emaillocation-"+splitData[1]).append('<option value="{{$client->id}}" selected> {{$client->name}} </option>').trigger('change');
				$("#emaillocation-"+splitData[1]).multiSelect('reload'); // init the select for Email Single Dropdown
				/* code to print client name suing {{$client->name}} in select dropdown */
			}
		});
		/* sms location store ajax call */
		$(document).on('change','.alert-sms-select-class',function(){
			var eid = $(this).attr('id');
			var splitData = eid.split("-");
			let alertLevel = $(this).val();
			if(alertLevel != 'client')
			{
				$.ajax({
					type:'POST',
					data:{'_token':'{{csrf_token()}}','alertLevel': alertLevel,'clientId':'{{$client->id}}'},
					url:"{{route('findclientsalescenter')}}",
					success:function(response){
						$("#smslocation-"+splitData[1]).empty();
						$.each(response.data.salescenters, function(key,value){
							$("#smslocation-"+splitData[1]).append('<option value="'+ value.id +'">'+ value.name +'</option>').trigger('change');
							$("#smslocation-"+splitData[1]).multiSelect('refresh'); // init the select	for Email Single Dropdown
						});
					}
				});
			}
			else
			{
				$("#smslocation-"+splitData[1]).empty();
				$("#smslocation-"+splitData[1]).append('<option value="{{$client->id}}" selected> {{$client->name}} </option>').trigger('change');
				$("#smslocation-"+splitData[1]).multiSelect('refresh'); // init the select	for Email Single Dropdown
				/* code to print client name suing {{$client->name}} in select dropdown */
			}
		});
		/* Edit email Location store ajax call */
		$(document).on('change','.edit-alert-select-class',function(){
			var eid = $(this).attr('id');
			var splitData = eid.split("-");
			console.log(splitData);
			var fid = $("#fid-"+splitData[2]).val();
			var alertLevel = $( "#edit-level-"+splitData[2]+" option:selected" ).val();
			if(alertLevel != 'client')
			{
				$.ajax({
					type:'POST',
					data:{'_token':'{{csrf_token()}}','alertLevel': alertLevel,'clientId':'{{$client->id}}','fid': fid},
					url:"{{route('findclientsalescenter')}}",
					success:function(response){
						$("#edit-location-"+splitData[2]).empty();
						$.each(response.data.salescenters, function(key,value){
							$("#edit-location-"+splitData[2]).append('<option value='+ value.id +'>'+ value.name +'</option>').trigger('change');
							$("#edit-location-"+splitData[2]).multiSelect('refresh'); // init the select
						});
						/* selected dropdown value response */
						if(response.data.selectedsalescenters){
							$.each(response.data.selectedsalescenters, function(k,val){
								if(val){
									var salesArray = '';
									if(alertLevel == "salescenter"){
										var	salesArray = val.split(',');
									}else if(alertLevel == "sclocation"){
										salesArray = val.split(',');
									}else if(alertLevel == "client"){
										salesArray == val;
									}
								}
								for(var j in salesArray){
									var optionVal = salesArray[j];
									$("#edit-location-"+splitData[2]).find("option[value="+optionVal+"]").prop("selected",true).trigger('change');
									$("#edit-location-"+splitData[2]).multiSelect('refresh');
								}
							});
						}
					}
				});
			}
			else
			{
				$("#edit-location-"+splitData[2]).empty();
				$("#edit-location-"+splitData[2]).append('<option value="{{$client->id}}" selected> {{$client->name}} </option>').prop("selected","selected").trigger('change');
				$("#edit-location-"+splitData[2]).multiSelect('refresh');
			}
		});
		/* Edit sms location store ajax call */
		$(document).on('change','.edit-alert-sms-select-class',function(){
			var eid = $(this).attr('id');
			var splitData = eid.split("-");
			var alertLevel = $( "#edit-smslevel-"+splitData[2]+" option:selected" ).val();
			var fid = $("#sms-fid-"+splitData[2]).val();
			if(alertLevel != 'client')
			{
				$.ajax({
					type:'POST',
					data:{'_token':'{{csrf_token()}}','alertLevel': alertLevel,'clientId':'{{$client->id}}','fid': fid},
					url:"{{route('findclientsalescenter')}}",
					success:function(response){
						$("#edit-smslocation-"+splitData[2]).empty();
						$.each(response.data.salescenters, function(key,value){
							$("#edit-smslocation-"+splitData[2]).append('<option value='+ value.id +'>'+ value.name +'</option>').trigger('change');
							$("#edit-smslocation-"+splitData[2]).multiSelect('refresh'); // init the select	for Email Single Dropdown
						});
						/* selected dropdown value response */
						if(response.data.selectedsalescenters){
							$.each(response.data.selectedsalescenters, function(k,val){
								if(val){
									var salesArray = '';
									if(alertLevel == "salescenter"){
										var	salesArray = val.split(',');
									}else if(alertLevel == "sclocation"){
										salesArray = val.split(',');
									}
								}
								for(var j in salesArray){
									var optionVal = salesArray[j];
									$("#edit-smslocation-"+splitData[2]).find("option[value="+optionVal+"]").prop("selected",true).trigger('change');
									$("#edit-smslocation-"+splitData[2]).multiSelect('refresh');
								}
							});
						}
					}
				});
			}
			else
			{
				$("#edit-smslocation-"+splitData[2]).empty();
				$("#edit-smslocation-"+splitData[2]).append('<option value="{{$client->id}}" selected> {{$client->name}} </option>').prop("disabled",true).prop("selected",false).trigger('change');
				$("#edit-smslocation-"+splitData[2]).multiSelect("refresh");
			}
		});

		/* Remove Cloned Email */
		$(document).on('click','.remove_field1',function(){
			$(this).closest('.cloned-email').remove();
		});
		/* Remove cloned Sms */
		$(document).on('click','.remove_field1_sms', function() {
			$(this).closest('.cloned-sms').remove();
		});
		/* Remove edit cloned email from Database */
		$(document).on('click','.remove_field1-email',function(){
			var eid = $(this).attr('id');
			var splitData = eid.split("-");
			var fid = $('#fid-'+splitData[2]).val();
			$('#confirm_delete').click(function(e){
				e.preventDefault();
				$('#removed_email-'+splitData[2]).remove();
				$('#delete-fraudalert-email').modal("hide");
			});
			$('#saveAfterDelete').click(function(e){
				e.preventDefault();
				$.ajax({
					type:'POST',
					url:"{{route('admin.fruadalert.store')}}",
					data:{'_token':'{{csrf_token()}}','emailId': fid},
					success:function(response){
						if(response.status == 200){
							location.reload();
						}
					}
				});
			});
		});
		/* Remove edit cloned sms from Database */
		$(document).on('click','.remove_field1-sms',function(){
			var eid = $(this).attr('id');
			var splitData = eid.split("-");
			var sfid = $('#sms-fid-'+splitData[2]).val();
			$('#sconfirm_delete').click(function(e){
				e.preventDefault();
				$('#removed_sms-'+splitData[2]).remove();
				$('#delete-fraudalert-sms').modal("hide");
			});
			$('#saveAfterDelete').click(function(e){
				e.preventDefault();
				$.ajax({
					type:'POST',
					url:"{{route('admin.fruadalert.store')}}",
					data:{'_token':'{{csrf_token()}}','smsId': sfid},
					success:function(response){
						if(response.status == 200){
							location.reload();
						}
					}
				});
			});
		});
		$('#myTab a[href="#{{ old('tab') }}"]').tab('show');
	})

	function showMoreEmailDiv() {
		var btnId = $('.emailAdd').attr('data-id');
		var temp = document.getElementById("email-template");
		var clon = temp.content.cloneNode(true);
		var divId = clon.querySelector('.cloned-email');
		divId.id = 'div-'+btnId;
		var location = divId.getElementsByClassName('location-select-class')[0];
		var text = divId.getElementsByClassName('email-text-class')[0];
		var alertField = divId.getElementsByClassName('alert-select-class')[0];
		var alertType = divId.getElementsByClassName('email-alert-for-class')[0];
		var locationErr = divId.getElementsByClassName('locations_err')[0];
		var alertForErr = divId.getElementsByClassName('alert_for_err')[0];


		location.id = 'emaillocation-'+btnId;
		location.name = 'emaillocations-'+btnId+'[]';
		text.id = 'emailtext-'+btnId;
		alertField.id = 'emailalert-'+btnId;
		alertType.id = 'alert-type-'+btnId;
		locationErr.id = 'location-err-' + btnId;
		alertForErr.id = 'alert-for-err-' + btnId;
		var div = document.getElementById('email-container');
		div.appendChild(divId);
		$("#emailalert-" + btnId).select2();
		$("#emaillocation-" + btnId).multiSelect();
		$("#emaillocation-" + btnId).attr("data-parsley-errors-container", '#location-err-' + btnId)
		$("#alert-type-" + btnId).multiSelect();
		$("#alert-type-" + btnId).attr("data-parsley-errors-container", '#alert-for-err-' + btnId)

		btnId++;
		$('.emailAdd').attr('data-id',btnId);
	}

	function showMoreSmsDiv() {
		var btnId = $('.smsAdd').attr('data-id');
		var temp = document.getElementById("sms-template");
		var clon = temp.content.cloneNode(true);
		var divId = clon.querySelector('.cloned-sms');
		divId.id = 'div-'+btnId;
		var location = divId.getElementsByClassName('location-sms-select-class')[0];
		var text = divId.getElementsByClassName('sms-text-class')[0];
		var alert = divId.getElementsByClassName('alert-sms-select-class')[0];
		
		location.id = 'smslocation-'+btnId;
		location.name = 'smslocations-'+btnId+'[]';
		text.id = 'smstext-'+btnId;
		alert.id = 'smsalert-'+btnId;
		var div = document.getElementById('sms-container');
		div.appendChild(divId);
		btnId++;
		$('.smsAdd').attr('data-id',btnId);
	}
</script>
@endpush
