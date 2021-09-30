@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array();
if (Auth::user()->access_level == 'tpv') {
	$breadcrum[] = array('link' => route('client.index'), 'text' =>  'Clients');
	$breadcrum[] = array('link' => route("client.show", array($client->id)), 'text' =>  $client->name);
	$breadcrum[] = array('link' => route("client.show", array($client->id))."#SalesCenter", 'text' =>  "Sales Centers");
} else {
	// $breadcrum[] = array('link' => route('client.salescenters',$salescenter->client_id), 'text' =>  'Sales Centers' );
}
$breadcrum[] = array('link' => '', 'text' =>  'Add Sales Center');
breadcrum($breadcrum);
?>
<div class="tpv-contbx">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12">
				<div class="cont_bx3">
					<!--tab-new-design-start-->
					<div class="tpvbtn message"></div>
					<div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx client-new-tabs">
						<div class="client-bg-white">
							<div class="row">
								<div class="col-md-6 col-sm-6">
									<h1>Add Sales Center</h1>
								</div>
								<div class="col-md-6 col-sm-6">
									<div class="new-info pull-right">
										<img src="@empty($client->logo) {{asset('images/PlaceholderLogo.png')}} @else {{Storage::disk('s3')->url($client->logo)}} @endempty">
										<span>{{$client->name}}</span>
									</div>
								</div>
							</div>

							<!-- Tab panes -->

							<div class="tab-content">
								<div role="tabpanel" class="tab-pane active" id="SalesAbout" aria-labelledby="about-tab">
									<form id="salescenter-form" action="{{ route('client.salescenter.storeNew',$client_id)}}" enctype="multipart/form-data" role="form" method="POST" onsubmit="return false;" data-parsley-validate>
										<div class="row">
											<div class="col-xs-12 col-sm-12 col-md-12">
												<div class="agent-detailform v-star">
													<div class="alert-message-wrapper" style="width:80%; margin: 0 auto;">
													</div>
													@csrf

													<input type="hidden" name="id" value="{{$client->id}}">

													<div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3">

														<div class="form-group mt30 {{ $errors->has('name')   ? ' has-error' : '' }}">
															<label for="name" class="yesstar">Sales center name</label>
															<span class="form-icon"><img src="{{ asset('images/form-name.png')}}"></span>
															<input autocomplete="off" id="name" type="text" class="form-control required" name="name" value="{{ old('name') }}"  data-parsley-required='true' data-parsley-minlength-message='This field must contain at least 2 characters'  data-parsley-minlength="2" data-parsley-minlength="255">
															@if ($errors->has('name'))
															<span class="help-block">
																{{ $errors->first('name') }}
															</span>
															@endif
														</div>
														<div class="form-group {{ $errors->has('code')   ? ' has-error' : '' }}">
															<label for="clientcode" class="yesstar">Sales center code</label>
															<span class="form-icon"><img src="{{ asset('images/code.png')}}"></span>
															<input id="centercode" autocomplete="off" type="text" value="{{ old('code') }}" class="cursor-none form-control required" name="code" data-parsley-required='true' onfocus="this.blur();" >
															@if ($errors->has('code'))
															<span class="help-block">
																{{ $errors->first('code') }}
															</span>
															@endif
														</div>
														<div class="form-group {{ $errors->has('street')   ? ' has-error' : '' }}">
															<label for="street" class="">Address</label>
															<span class="form-icon"><img src="{{ asset('images/location.png')}}"></span>
															<input id="street" type="text" class="form-control" name="street" value="{{ old('street') }}" data-parsley-required='false'>
															@if ($errors->has('street'))
															<span class="help-block">
																{{ $errors->first('street') }}
															</span>
															@endif
														</div>

														<div class="row">
															<div class="col-xs-12 col-sm-6 col-md-6">
																<div class="form-group {{ $errors->has('city')   ? ' has-error' : '' }}">
																	<label for="city" class="" >City</label>
																	<input id="city" type="text" class="form-control required" name="city" value="{{ old('city') }}" data-parsley-required='false' >
																	@if ($errors->has('city'))
																	<span class="help-block">
																		{{ $errors->first('city') }}
																	</span>
																	@endif
																</div>
															</div>

															<div class="col-xs-12 col-sm-6 col-md-6">
																<div class="form-group {{ $errors->has('state')   ? ' has-error' : '' }}">
																	<label for="state" class="" >State</label>
																	<input id="state" type="text" class="form-control required" name="state" value="{{ old('state') }}" data-parsley-required='false' >
																	@if ($errors->has('state'))
																	<span class="help-block">
																		{{ $errors->first('state') }}
																	</span>
																	@endif

																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-xs-12 col-sm-6 col-md-6">
																<div class="form-group {{ $errors->has('country')   ? ' has-error' : '' }}">
																	<label for="country" class="" >Country</label>
																	<input id="country" type="text" class="form-control" name="country" value="{{ old('country') }}" data-parsley-required='false' >
																	@if ($errors->has('country'))
																	<span class="help-block">
																		{{ $errors->first('country') }}
																	</span>
																	@endif
																</div>
															</div>
															<div class="col-xs-12 col-sm-6 col-md-6">
																<div class="form-group {{ $errors->has('zip')   ? ' has-error' : '' }}">
																	<label for="zip" class="" >Zipcode</label>
																	<input id="zip" type="text" class="form-control" name="zip" value="{{ old('zip') }}" data-parsley-required='false' data-parsley-type="digits" data-parsley-length="[5,5]"   data-parsley-type-message="This field must only contain numbers"  data-parsley-length-message="Zip code must be exactly 5 digits">
																	@if ($errors->has('zip'))
																	<span class="help-block">
																		{{ $errors->first('zip') }}
																	</span>
																	@endif
																</div>
															</div>
															<div class="col-xs-12 col-sm-12 col-md-12">
																<div class="form-group {{ $errors->has('contact')   ? ' has-error' : '' }}">
																	<label for="contact">Contact</label>
																	<input id="contact" type="text" class="form-control" name="contact" value="{{ old('contact') }}" autofocus="" data-parsley-type="digits" data-parsley-length="[10,10]"   data-parsley-type-message="This field must only contain numbers"  data-parsley-length-message="You must enter exactly 10 digits">
																	@if ($errors->has('contact'))
																	<span class="help-block">
																		{{ $errors->first('contact') }}
																	</span>
																	@endif
																</div>
															</div>
															{{--<div class="col-xs-12 col-sm-12 col-md-12 text-left">
																<div class="pro-pic">

																	<h5 class="nostar">Sales center logo</h5>

																	<div class="img-outer">
																		<img id="imagePreview" src="{{asset('images/PlaceholderLogo.png')}}">
																		<div class="image-upload">
																			<label for="file-input">
																				<!-- <img src="{{asset('images/upload.png')}}" /> -->
																				Browse
																			</label>
																			<input id="file-input" name="logo" type="file" accept="image/*"  />
																		</div>
																	</div>
																	@if ($errors->has('logo'))
																	<span class="help-block">
																		{{ $errors->first('logo') }}
																	</span>
																	@endif
																</div>
															</div>--}}
															<div class="col-xs-12 col-sm-12 col-md-12 text-left">
																<div class="form-group mt15" id="dropzone-outer">
																	<label>Sales Center Logo</label>
																	<div class="img-outer" style="min-height: 150px; text-align: center;">
																		<img id="imagePreview" src="{{asset('images/default_profile_photo.png')}}" style="width: 200px;">
																	</div>
																	<div class="dropzone files-container " id="salescenter-logo">
																		<div class="fallback">
																			<input name="salescenter_logo" type="file" />
																		</div>
																	</div>
																	@include('preview-dropzone')
																</div>
															</div>
														</div>
														<div class="col-xs-12 col-sm-12 col-md-12 bottom_btns">
															<div class="col-xs-12 col-sm-12 col-md-12 text-center">
																<div class="btn-group mt30 mb30">
																	<button type="button" id="btnSave" class="btn btn-green">Save</button>
																	<a href="{{route('client.show',['id' => $client->id])}}#SalesCenter" class="btn  btn-red">Cancel</a>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<!--tab-new-design-end-->
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

<!--script for url-show-->
<!-- <script>
	$(document).ready(() => {
		let url = location.href.replace(/\/$/, "");

		if (location.hash) {
			const hash = url.split("#");
			$('#myTab a[href="#' + hash[1] + '"]').tab("show");
			url = location.href.replace(/\/#/, "#");
			history.replaceState(null, null, url);
			setTimeout(() => {
				$(window).scrollTop(0);
			}, 400);
		}

		$('a[data-toggle="tab"]').on("click", function() {
			let newUrl;
			const hash = $(this).attr("href");
			// if(hash == "#SalesAbout") {
			//   newUrl = url.split("#")[0];
			// } else {
			newUrl = url.split("#")[0] + hash;
			// }
			//newUrl += "";
			history.replaceState(null, null, newUrl);
		});
	});
</script> -->

@push('scripts')
 <script type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC49bXfihl4zZqjG2-iRLUmcWO_PVcDehM&libraries=places"></script>
<script>
    var CAutocomplete = new google.maps.places.Autocomplete(document.getElementById('street'), {
        types: [],
        componentRestrictions: {country: "us"}
    });
    google.maps.event.addListener(CAutocomplete, 'place_changed', function () {
        var place = CAutocomplete.getPlace();
        $('#street').val(place.name);
        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (addressType === 'postal_code') {
                $('#zip').val(place.address_components[i].long_name);
            }
            if (addressType === "locality" || addressType === "administrative_area_level_2") {
                $('#city').val(place.address_components[i].long_name);
            }
            if (addressType === "administrative_area_level_1") {
                $('#state').val(place.address_components[i].long_name);
            }
            if (addressType === "country") {
                $('#country').val(place.address_components[i].long_name);
            }
        }
    });
</script>
<script>

	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				$('#imagePreview').attr('src', e.target.result);
			}
			reader.readAsDataURL(input.files[0]);
		}
	}
	$("#file-input").change(function() {
		readURL(this);
	});
</script>

<script>
	$("#name").keyup(function() {
		var string = $(this).val();
		var matches = string.match(/\b(\w)/g);
		if(matches != null) {
			var finalString = matches.join('');

			$.post(
	            "{{ route('client.sales-centers.check-code', array_get($client, 'id')) }}", {
	                "_token": "{{ csrf_token() }}",
	                "code": finalString
	            },
	            function(result) {
	            	if (result.status) {
	                	$("#centercode").val(result.code.toUpperCase());
	                }
	            }
	        );
		} else {
			$("#centercode").val('');
		}
    });

	$("#centercode").keypress(function() {
		return false;
	});

</script>
 <script>
	 function submitClientForm() {
		 $("#btnSave").prop("disabled", true);
		 let formData = $('#salescenter-form').serializeArray();
		 $.ajax({
			 url: $("#salescenter-form").attr('action'),
			 method:'POST',
			 data: formData,
			 success:function(res) {
				 successHandler();
			 },
			 error: function(response) {
				 errorHandler(response);
			 }
		 });
	 }

	 Dropzone.autoDiscover = false;
	 //Dropzone related code
	 var target = "#salescenter-logo";

	 function dropzoneCount() {
		 var filesCount = $("#previews > .dz-success.dz-complete").length;
		 return filesCount;
	 }

	 function fileType(fileName) {
		 var fileType = (/[.]/.exec(fileName)) ? /[^.]+$/.exec(fileName) : undefined;
		 return fileType[0];
	 }

	 var previewNode = document.querySelector("#cust-dropzone-template"), // Dropzone template holder
			 warningsHolder = $("#warnings"); // Warning messages' holder

	 previewNode.id = "";

	 var previewTemplate = previewNode.parentNode.innerHTML;
	 previewNode.parentNode.removeChild(previewNode);

	 var CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
	 var insDropzone = new Dropzone("div#salescenter-logo", {
		 url: $("#salescenter-form").attr('action'),
		 autoProcessQueue: false,
		 paramName: "logo",
		 parallelUploads: 1,
		 maxFiles: 1,
		 uploadMultiple: false,
		 acceptedFiles: 'image/*',
		 previewTemplate: previewTemplate,
		 previewsContainer: "#previews",
		 clickable: true,
		 createImageThumbnails: true,
		 dictDefaultMessage: "Drop files here to upload, Or Browse", // Default: Drop files here to upload
		 dictFallbackMessage: "Your browser does not support drag'n'drop file uploads.", // Default: Your browser does not support drag'n'drop file uploads.
		 dictInvalidFileType: "You can't upload files of this type.", // Default: You can't upload files of this type.
		 dictCancelUpload: "Cancel upload.", // Default: Cancel upload
		 dictUploadCanceled: "Upload canceled.", // Default: Upload canceled.
		 dictCancelUploadConfirmation: "Are you sure you want to cancel this upload?", // Default: Are you sure you want to cancel this upload?
		 dictRemoveFile: "Remove file", // Default: Remove file
		 dictRemoveFileConfirmation: null, // Default: null
		 dictMaxFilesExceeded: "You can not upload any more files.", // Default: You can not upload any more files.
		 dictFileSizeUnits: {tb: "TB", gb: "GB", mb: "MB", kb: "KB", b: "b"},
		 init: function () {
			 let objDropzone = this;
			 //for Dropzone to process the queue (instead of default form behavior):
			 document.getElementById("btnSave").addEventListener("click", function(e) {
				 //Make sure that the form isn't actually being sent.
				 if (objDropzone.getUploadingFiles().length === 0 && objDropzone.getQueuedFiles().length === 0) {
					 submitClientForm();
				 } else {
					 e.preventDefault();
					 e.stopPropagation();
					 objDropzone.processQueue();
				 }
			 });
		 }
	 });

	 insDropzone.on('sending', function(file, xhr, formData) {
		 $("#btnSave").prop("disabled", true);
		 let data = $('#salescenter-form').serializeArray();
		 $.each(data, function (key, el) {
			 if (el.name == "_token") {
				 formData.append("_token", CSRF_TOKEN);
			 } else {
				 formData.append(el.name, el.value);
			 }
		 });
	 });

	 insDropzone.on('success', function (file, res) {
		 this.emit("complete", file);
		 successHandler(res);
	 });

	 insDropzone.on('error', function(file, err, xhr) {
	 	$("#btnSave").prop("disabled", false);
	 	if (typeof xhr != 'undefined' && xhr.status == 422) {
		 	printErrorMsgNew($("#salescenter-form"), err.errors);
		} else {
			errorHandler(err);
		}
	 });

	 insDropzone.on('complete', function(file) {
		 this.removeAllFiles();
	 });

	 insDropzone.on("addedfile", function(file) {
		 $('.preview-container').css('visibility', 'visible');
		 file.previewElement.classList.add('type-' + fileType(file.name)); // Add type class for this element's preview
	 });

	 insDropzone.on("totaluploadprogress", function (progress) {
		 var progr = document.querySelector(".progress .determinate");
		 if (progr === undefined || progr === null) return;
		 progr.style.width = progress + "%";
	 });

	 insDropzone.on('dragenter', function () {
		 $(target).addClass("hover");
	 });

	 insDropzone.on('dragleave', function () {
		 $(target).removeClass("hover");
	 });

	 insDropzone.on('drop', function () {
		 $(target).removeClass("hover");
	 });

	 insDropzone.on('addedfile', function () {
		 $(".no-files-uploaded").slideUp("easeInExpo");
	 });

	 insDropzone.on('removedfile', function (file) {
		 // Show no files notice
		 if ( dropzoneCount() == 0 ) {
			 $(".no-files-uploaded").slideDown("easeInExpo");
			 $(".uploaded-files-count").html(dropzoneCount());
		 }
	 });

	 insDropzone.on("maxfilesexceeded", function(file) {
		 this.removeAllFiles();
		 this.addFile(file);
	 });

	 function successHandler() {
		 $("#btnSave").prop("disabled", false);
		window.location.href = "{{route('client.show',['id' => $client->id])}}#SalesCenter";
	 }

	 function errorHandler(xhr) {
		$("#btnSave").prop("disabled", false);
		 $(window).scrollTop( $(".agent-detailform").offset().top );
		 if (typeof xhr != 'undefined' && xhr.status == 422) {
			 printErrorMsgNew($("#salescenter-form"), xhr.responseJSON.errors);
		 } else {
			 printAjaxErrorMsg(xhr);
		 }
	 }
 </script>
@endpush
