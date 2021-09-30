<div class="col-xs-12 col-sm-12 col-md-12">
    @php
        $star = "";
        $noStar = "nostar"
    @endphp
    @if(Request::route()->getName() == 'client.create' || Request::route()->getName() == 'client.edit')
        @php
            $star = "yesstar";
            $noStar = ""
        @endphp
    @endif
    <div class="agent-detailform">
        <div class="alert-message-wrapper" style="width:80%; margin: 0 auto;">
        </div>

        <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3">
            <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="client_name" class="{{ $star }}">Client Name</label>
                <span class="form-icon"><img src="{{asset('images/form-name.png')}}"></span>
                <input id="client_name" maxlength="100" type="text" value="{{ old('name',array_get($client, 'name'))}}"
                       class="form-control required" name="name" autofocus="" data-parsley-required="true"  minlength="2" maxlength="255">
                @if ($errors->has('name'))
                    <span class="help-block">
                    {{ $errors->first('name') }}
                </span>
                @endif

            </div>
            <div class="form-group {{ $errors->has('code') ? ' has-error' : '' }}">
                <label for="clientcode" class="{{ $star }}">Client Code</label>
                <span class="form-icon"><img src="{{asset('images/code.png')}}"></span>
                <input id="clientcode" autocomplete="off" type="text" value="{{ old('code',array_get($client, 'code'))}}"
                       class=" form-control required" name="code" data-parsley-required='true'
                        data-parsley-client-code-exist="{{array_get($client, 'id')}}" data-parsley-trigger="change" >
                @if ($errors->has('code'))
                    <span class="help-block">
                    {{ $errors->first('code') }}
                    </span>
                @endif
            </div>
            <div class="form-group {{ $errors->has('code') ? ' has-error' : '' }} clientIdDiv" >
                <label for="clientid" >Client ID</label>
                <!-- <span class="form-icon"><img src="{{asset('images/code.png')}}"></span> -->
                <input id="clientid" autocomplete="off" type="text" value="{{ old('id',array_get($client, 'id'))}}" disabled>
            </div>
            <div class="form-group {{ $errors->has('prefix') ? ' has-error' : '' }}">
                <label for="clientcode" class="{{ $star }}">Client Prefix</label>
                {{-- <span class="form-icon"><img src="{{asset('images/code.png')}}"></span> --}}
                <input id="clientcode" autocomplete="off" type="text" value="{{ old('prefix',array_get($client, 'prefix'))}}"
                       class=" form-control required" name="prefix" data-parsley-required='true' data-parsley-type="digits"
                       data-parsley-pattern = '^(?!0*(\.0+)?$)(\d+|\d*)$'
                       data-parsley-pattern-message = 'Please enter digits between 1 to 9'
                        data-parsley-client-prefix-exist="{{array_get($client, 'id')}}" data-parsley-trigger="change" >
                @if ($errors->has('prefix'))
                    <span class="help-block">
                    {{ $errors->first('prefix') }}
                    </span>
                @endif
            </div>
            <div class="form-group {{ $errors->has('street')   ? ' has-error' : '' }}">
                <label for="street" class="{{ $star }}">Address</label>
                <span class="form-icon"><img src="{{asset('images/location.png')}}"></span>
                <input id="street" type="text" class="form-control"
                       value="{{old('street',array_get($client, 'street'))}}" name="street" data-parsley-required='true'>
                @if ($errors->has('street'))
                    <span class="help-block">
                    {{ $errors->first('street') }}
                </span>
                @endif
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <div class="form-group {{ $errors->has('city')   ? ' has-error' : '' }}">
                        <label for="city" class="{{ $star }}">City</label>
                        <input id="city" type="text" value="{{old('city',array_get($client, 'city'))}}"
                               class="form-control required" name="city" data-parsley-required='true'>
                        @if ($errors->has('city'))
                            <span class="help-block">
                            {{ $errors->first('city') }}
                        </span>
                        @endif
                    </div>
                </div>


                <div class="col-xs-12 col-sm-6 col-md-6">
                    <div class="form-group {{ $errors->has('state')   ? ' has-error' : '' }}">
                        <label for="state" class="{{ $star }}">State</label>
                        <input id="state" type="text" value="{{old('state',array_get($client, 'state'))}}"
                               class="form-control required" name="state" data-parsley-required='true'
                               >
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
                        <label for="country" class="{{ $star }}">Country</label>
                        <input id="country" type="text" value="{{old('country',array_get($client, 'country'))}}"
                               class="form-control" name="country" data-parsley-required='true'>
                        @if ($errors->has('country'))
                            <span class="help-block">
                            {{ $errors->first('country') }}
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <div class="form-group {{ $errors->has('zip')   ? ' has-error' : '' }}">
                        <label for="zip" class="{{ $star }}">Zipcode</label>
                        <input id="zip" type="text" value="{{old('zip',array_get($client, 'zip'))}}"
                               class="form-control" name="zip" data-parsley-required='true' data-parsley-type="digits"
                               data-parsley-length="[5,5]"  data-parsley-type-message="Please enter only numeric values"  data-parsley-length-message="You must enter exactly 5 digits">
                        @if ($errors->has('zip'))
                            <span class="help-block">
                            {{ $errors->first('zip') }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group {{ $errors->has('contact_info')   ? ' has-error' : '' }}">
                <label for="contact" id="contact_label">Contact</label>
                <input id="contact" type="text" class="form-control"
                       value="{{old('contact_info',array_get($client, 'contact_info'))}}" name="contact_info"
                       data-parsley-type="digits" data-parsley-type-message="Please enter only numeric values" data-parsley-length="[10,10]" data-parsley-length-message="You must enter exactly 10 digits">
                @if ($errors->has('contact_info'))
                    <span class="help-block">
                    {{ $errors->first('contact_info') }}
                </span>
                @endif
            </div>

{{--            <div class="text-left {{ $errors->has('clientlogo') ? ' has-error' : '' }}">--}}
{{--                <div class="pro-pic">--}}
{{--                    <h5 class="{{ $noStar }}">Client logo</h5>--}}
{{--                    <div class="img-outer">--}}
{{--                        <img id="imagePreview"--}}
{{--                             src="@empty($client->logo) {{asset('images/PlaceholderLogo.png')}} @else {{Storage::disk('s3')->url($client->logo)}} @endempty">--}}

{{--                        <div class="image-upload">--}}
{{--                            <label for="file-input">--}}
{{--                            <!-- <img src="{{asset('images/upload.png')}}" /> -->--}}
{{--                                Browse--}}
{{--                            </label>--}}
{{--                            <input id="file-input" name="clientlogo" type="file" accept=".jpg, .png, .jpeg" data-parsley-trigger="change" data-parsley-fileextension="jpg,jpeg,png"--}}
{{--                                   @if(Request::route()->getName() == 'client.create') data-parsley-required='true'--}}
{{--                                   data-parsley-required-message="This field is required" @endif />--}}
{{--                        </div>--}}

{{--                        <span class="ext-type-error" style="color:red; font-weight:bold; top: 100px; position: absolute;"></span>--}}

{{--                        <span style="color:red; font-weight:bold; display:none; top: 100px; position: absolute;"--}}
{{--                              class="imgError">This field is required</span>--}}
{{--                        @if ($errors->has('clientlogo'))--}}
{{--                            <span style="color:red; font-weight:bold; top: 100px; position: absolute;">{{ $errors->first('clientlogo') }}--}}
{{--                        </span>--}}
{{--                        @endif--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
            <div class="form-group mt15" id="dropzone-outer">
                <label class="yesstar">Client Logo</label>
                <div class="img-outer" style="text-align: center;">
                    @if(empty($client) || empty($client->logo) || !Storage::disk('s3')->exists(config()->get('constants.aws_folder') . $client->logo))
                        <img id="imagePreview" src="{{asset('images/default_profile_photo.png')}}" >
                    @else
                        <img id="imagePreview" src="{{Storage::disk('s3')->url($client->logo)}}" >
                        <input type="hidden" name="old_url" value="{{$client->logo}}">
                    @endif
                </div>
                <div class="dropzone files-container mt15" id="client-logo">
                    <div class="fallback">
                        <input name="file" type="file" />
                    </div>
                </div>
                @include('preview-dropzone')
                <input name="client_logo" type="hidden" />
            </div>

                           
                        


            <div class="col-xs-12 col-sm-12 col-md-12 bottom_btns text-center mt30">

                <div class="btn-group mt30 mb30">

                    @if(!isset($client->id))
                        <button id="client-save-btn" type="button" class="btn btn-green">Save
                        </button>
                        <a href="{{route('client.index') }}" id="client-cancel-btn" class="btn  btn-red">Cancel </a>
                    @else
                        @if(auth()->user()->hasPermissionTo('edit-client-info'))
                            @if($client->status == "active")
                                                                                    
                                <a href="{{ route('client.edit', array_get($client, 'id')) }}" type="button"
                                id="client-edit-btn"
                                class="btn btn-green">Edit</a>
                            @else
                                <a class="btn btn-green cursor-none" disabled>Edit</a>    
                            @endif  
                            @permission('all-clients')                                       
                            <a href="{{route('client.index') }}" id="client-back-btn" class="btn  btn-red">Back </a>
                            @endpermission
                            <button id="client-save-btn" type="button" class="btn btn-green">Save
                            </button>
                            @if(auth()->user()->can(['all-clients']))
                            <a href="{{route('client.index') }}" id="client-cancel-btn" class="btn  btn-red">Cancel </a>
                            @else
                            <a href="{{ route('client.show', array_get($client, 'id')) }}" id="client-cancel-btn" class="btn  btn-red">Cancel </a>
                            @endif
                        @endif
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
    <!-- Do not removed this googleapi , This is using in lead form  -->
    <script type="text/javascript" 
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC49bXfihl4zZqjG2-iRLUmcWO_PVcDehM&libraries=places">
                    
    </script>
    @if(Request::route()->getName() == 'client.create' || Request::route()->getName() == 'client.edit'  || Request::route()->getName() == 'client.show')
        
        <script>
            $(document).ready(function() {
                @if (Request::route()->getName() == "client.edit" || Request::route()->getName() == "client.create")
                    $("#dropzone-outer").show();
                @else
                    $("#dropzone-outer").hide();
                @endif
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

                @if($errors->any() && Request::route()->getName() != 'client.create')
                    $("#client-edit-btn").trigger('click');
                @endif

                window.ParsleyValidator.addValidator('clientCodeExist', function(value, clientId){
                        var res = false;
                        $.ajax({
                            url: '{{route("client.check-client-code")}}',
                            data: {
                                code: value,
                                id : clientId
                            },
                            async: false,
                            success: function(response) {
                                if(response.valid === true) {
                                    res = true;
                                } else {
                                    res = false;
                                }
                            }
                        });
                        return res;       
                }, 34)
                .addMessage('en', 'clientCodeExist', 'This code is taken');
                window.ParsleyValidator
                    .addValidator('fileextension', function (value, requirement) {
                        var tagslistarr = requirement.split(',');
                        var fileExtension = value.split('.').pop();
                        var arr=[];
                        $.each(tagslistarr,function(i,val){
                            arr.push(val);
                        });
                        if(jQuery.inArray(fileExtension, arr)!='-1') {
                            return true;
                        } else {
                            return false;
                        }
                    }, 32)
                    .addMessage('en', 'fileextension', 'Please upload jpg, png and jpeg extension file');
            });
        </script>
    @endif
    @if(!array_get($client, 'id'))
        <script>
            // $("#client_name").keyup(function () {
            //     var string = $(this).val();
            //     if (string) {
            //         var matches = string.match(/\b(\w)/g);
            //         var finalString = matches.join('').toUpperCase();
            //         // check the client id is exists in DB
            //         $.post(
            //             "{{ route('client.check-client-id') }}", {
            //                 "_token": "{{ csrf_token() }}",
            //                 "code": finalString
            //             },
            //             function (result) {
            //                 $("#clientcode").val(result.code);
            //             }
            //         );
            //     } else {
            //         $("#clientcode").val("");
            //     }
            //     $("#clientcode").trigger('input');
            // });

            // $("#clientcode").keyup(function () {
            //     return false;
            // });

        </script>
    @endif

    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#imagePreview').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#file-input").change(function () {
            readURL(this);
        });
    </script>

    <script>
        function submitClientForm() {
            $("#client-save-btn").prop("disabled", true);   
            let formData = $('#client-form').serializeArray();
            @empty($client)
                var url = "{{ route('client.store') }}";
            @else
                var url = "{{ route('client.updateNew') }}";
            @endempty

            $.ajax({
                url: url,
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
        var target = "#client-logo";

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
        var insDropzone = new Dropzone("div#client-logo", {
            @empty($client)
            url: "{{ route('client.store') }}",
            @else
            url: "{{ route('client.updateNew') }}",
            @endempty
            autoProcessQueue: false,
            paramName: "client_logo",
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
                document.getElementById("client-save-btn").addEventListener("click", function(e) {
                    //Make sure that the form isn't actually being sent.
                    if (objDropzone.getUploadingFiles().length === 0 && objDropzone.getQueuedFiles().length === 0) {
                        // var isValid = true;
                        // $('input').each( function() {
                        //     if ($("#client-form").parsley().validate() !== true) {
                        //         isValid = false;
                        //     }
                        // });
                        if ($("#client-form").parsley().isValid()) {
                            submitClientForm();
                        } else {
                            $("#client-form").parsley().validate();
                            return false;
                        }

                    } else {
                        e.preventDefault();
                        e.stopPropagation();
                        objDropzone.processQueue();
                    }
                });
            }
        });

        insDropzone.on('sending', function(file, xhr, formData) {
            $("#client-save-btn").prop("disabled", true);
            let data = $('#client-form').serializeArray();
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
            $("#client-save-btn").prop("disabled", false);
            if (typeof xhr != 'undefined' && xhr.status == 422) {
                printErrorMsgNew($("#client-form"), err.errors);
            } else if(typeof xhr != 'undefined' && xhr.status == 500) {
                profileErrorHandler(err.message);
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
            $("#client-save-btn").prop("disabled", false);
            @empty($client)
                window.location.href = "{{ route('client.index') }}";
            @else
                window.location.href = "{{ route('client.show', ['id' => $client->id]) }}";
            @endempty
        }

        function errorHandler(xhr) {
            $(window).scrollTop( $(".agent-detailform").offset().top );
            if (typeof xhr != 'undefined' && xhr.status == 422) {
                $("#client-save-btn").prop("disabled", false);
                printErrorMsgNew($("#client-form"), xhr.responseJSON.errors);
            } else {
                printAjaxErrorMsg(xhr);
            }
        }
    </script>
@endpush
