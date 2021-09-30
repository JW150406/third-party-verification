@extends('layouts.admin')
@section('content')
    <?php
    $breadcrum = array();
    if (Auth::user()->access_level == 'tpv') {
        $breadcrum[] = array('link' => route('client.index'), 'text' => 'Clients');
        $breadcrum[] = array('link' => route("client.show", array($client->id)), 'text' => $client->name);
        $breadcrum[] = array('link' => route("client.show", array($client->id)) . "#SalesCenter", 'text' => "Sales Centers");
    } else {
        // $breadcrum[] = array('link' => route('client.salescenters',$salescenter->client_id), 'text' =>  'Sales Centers' );
        $breadcrum[] = array('link' => route("client.show", array($client->id)), 'text' => $client->name);
    }

    $breadcrum[] = array('link' => '', 'text' => $salescenter->name);
    breadcrum($breadcrum);
    ?>

    <div class="tpv-contbx">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="cont_bx3">
                        <!--tab-new-design-start-->

                        <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx client-new-tabs">
                            <div class="client-bg-white">
                                <div class="row">
                                    <div class="col-md-6 col-sm-6">
                                        <div class="new-info">
                                            @empty($salescenter->logo)
                                                <img src="{{asset('images/PlaceholderLogo.png')}}">
                                            @else
                                                <img src="{{Storage::disk('s3')->url($salescenter->logo)}}">
                                            @endempty
                                            <span>{{$salescenter->name}}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <div class="new-info pull-right">
                                            @empty($client->logo)
                                                <img src="{{asset('images/PlaceholderLogo.png')}}">
                                            @else
                                                <img src="{{Storage::disk('s3')->url($client->logo)}}">
                                            @endempty
                                            <span>{{$client->name}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="tpvbtn message">
                                    @if ($message = Session::get('success'))
                                        <div class="alert alert-success alert-dismissable">
                                            {{ $message }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        @php session()->forget('success') @endphp
                                    @endif
                                    @if ($message = Session::get('error'))
                                        <div class="alert alert-danger alert-dismissable">
                                            {{ $message }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        @php session()->forget('error') @endphp
                                    @endif
                                </div>
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist" id="myTab">
                                    <li role="presentation" class="active"><a href="#SalesAbout" aria-controls="home"
                                                                              role="tab" data-toggle="tab"
                                                                              aria-expanded="true">About</a></li>
                                    <li role="presentation"><a href="#SalesCenterLocation" aria-controls="profile"
                                                               role="tab" data-toggle="tab" aria-expanded="false">Locations</a>
                                    </li>
                                    @if(auth()->user()->hasPermissionTo('view-sales-users'))
                                        <li role="presentation"><a href="#SalesCenterUser" aria-controls="profile"
                                                                   role="tab" data-toggle="tab" aria-expanded="false">Sales
                                                Center Users</a></li>
                                    @endif
                                    @if(auth()->user()->hasPermissionTo('view-sales-agents'))
                                        <li role="presentation"><a href="#SalesAgent" aria-controls="profile" role="tab"
                                                                   data-toggle="tab" aria-expanded="false">Sales
                                                Agents</a></li>
                                    @endif
                                    @if(auth()->user()->hasPermissionTo('view-brand-info'))
                                    <li role="presentation"><a href="#Brands" aria-controls="profile" role="tab"
                                                                   data-toggle="tab" aria-expanded="false">Brands</a></li>
                                    @endif
                                </ul>
                                <!-- Tab panes -->

                                <div class="tab-content">

                                    <!--about Details starts-->
                                    <div role="tabpanel" class="tab-pane active" id="SalesAbout">
                                        <div class="row">
                                            @php
                                                $star = "";
                                                $noStar = "nostar"
                                            @endphp
                                            @if (Request::route()->getName() == 'client.salescenters.edit')
                                                @php
                                                    $star = "yesstar";
                                                    $noStar = ""
                                                @endphp
                                            @endif
                                            <form method="post"
                                                  action="{{ route('client.salescenters.update', array($client->id, $salescenter->id)) }}"
                                                  enctype="multipart/form-data" id="salescenter-edit-form"
                                                  data-parsley-validate>
                                                @csrf
                                                <div class="col-xs-12 col-sm-12 col-md-12">
                                                    <div class="agent-detailform">
                                                        <div class="alert-message-wrapper"
                                                             style="width:80%; margin: 0 auto;">
                                                        </div>
                                                        <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3">
                                                            <div class="form-group {{ $errors->has('name')   ? ' has-error' : '' }}">
                                                                <label for="name" class="{{ $star }}">Sales center name</label>
                                                                <span class="form-icon"><img
                                                                            src="{{ asset('images/form-name.png')}}"></span>
                                                                <input autocomplete="off" id="name" type="text"
                                                                       data-parsley-required='true' data-parsley-minlength-message='This field must contain at least 2 characters'  data-parsley-minlength="2" data-parsley-minlength="255" 
                                                                       class="form-control required" name="name"
                                                                       value="{{ $salescenter->name }}">
                                                                
                                                                @if ($errors->has('name'))
                                                                    <span class="help-block">
                                                                {{ $errors->first('name') }}
                                                            </span>
                                                                @endif

                                                            </div>
                                                            <div class="form-group {{ $errors->has('code')   ? ' has-error' : '' }}">
                                                                <label for="clientcode" class="{{ $star }}">Sales center
                                                                    Code</label>
                                                                <span class="form-icon"><img
                                                                            src="{{ asset('images/code.png')}}"></span>
                                                                <input id="clientcode" autocomplete="off" type="text"
                                                                       class="cursor-none form-control required"
                                                                       name="code" placeholder="Sales center code"
                                                                       data-parsley-required='true' 
                                                                       value="{{ $salescenter->code }}" disabled
                                                                       onfocus="this.blur();">
                                                                
                                                                @if ($errors->has('code'))
                                                                    <span class="help-block">
                                                                {{ $errors->first('code') }}
                                                            </span>
                                                                @endif
                                                            </div>
                                                            <div class="form-group {{ $errors->has('street')   ? ' has-error' : '' }}">
                                                                <label for="street" class="">Address</label>
                                                                <span class="form-icon"><img
                                                                            src="{{ asset('images/location.png')}}"></span>
                                                                <input id="street" type="text" class="form-control"
                                                                       name="street" value="{{ $salescenter->street }}">
                                                                @if ($errors->has('street'))
                                                                    <span class="help-block">
                                                                {{ $errors->first('street') }}
                                                            </span>
                                                                @endif

                                                            </div>

                                                            <div class="row">
                                                                <div class="col-xs-12 col-sm-6 col-md-6">
                                                                    <div class="form-group {{ $errors->has('city')   ? ' has-error' : '' }}">
                                                                        <label for="city"
                                                                               class="">City</label>
                                                                        <input id="city" type="text"
                                                                               class="form-control required" name="city"
                                                                               value="{{ $salescenter->city }}">
                                                                        @if ($errors->has('city'))
                                                                            <span class="help-block">
                                                                        {{ $errors->first('city') }}
                                                                    </span>
                                                                        @endif
                                                                    </div>
                                                                </div>


                                                                <div class="col-xs-12 col-sm-6 col-md-6">
                                                                    <div class="form-group {{ $errors->has('state')   ? ' has-error' : '' }}">
                                                                        <label for="state"
                                                                               class="">State</label>
                                                                        <input id="state" type="text"
                                                                               class="form-control required"
                                                                               name="state" value="{{ $salescenter->state }}">
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
                                                                        <label for="country"
                                                                               class="">Country</label>
                                                                        <input id="country" type="text"
                                                                               class="form-control" name="country"
                                                                               value="{{ $salescenter->country }}">
                                                                        @if ($errors->has('country'))
                                                                            <span class="help-block">
                                                                        {{ $errors->first('country') }}
                                                                    </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-xs-12 col-sm-6 col-md-6">
                                                                    <div class="form-group {{ $errors->has('zip')   ? ' has-error' : '' }}">
                                                                        <label for="zip"
                                                                               class="">Zipcode</label>
                                                                        <input id="zip" type="text" class="form-control"
                                                                               name="zip"
                                                                               data-parsley-type="digits"
                                                                               data-parsley-length="[5,5]" data-parsley-type-message="Please enter only numeric value"  data-parsley-length-message="You must enter exactly 5 digits"
                                                                               value="{{ $salescenter->zip }}">

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

                                                                        <input id="contact" type="text"
                                                                               class="form-control required"
                                                                               data-parsley-type="digits"
                                                                               data-parsley-length="[10,10]" data-parsley-type-message="Please enter only numeric value"  data-parsley-length-message="You must enter exactly 10 digits"
                                                                               name="contact"
                                                                               value="{{ $salescenter->contact }}"
                                                                               autofocus="">

                                                                        @if ($errors->has('contact'))
                                                                            <span class="help-block">
                                                                        {{ $errors->first('contact') }}
                                                                    </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            @if(Request::route()->getName() != 'client.salescenter.show')
{{--                                                                <div class="text-left">--}}
{{--                                                                    <div class="pro-pic">--}}
{{--                                                                        <h5 class="nostar">Sales Center Logo</h5>--}}
{{--                                                                        <div class="img-outer">--}}

{{--                                                                            @empty($salescenter->logo)--}}
{{--                                                                                <img id="imagePreview"--}}
{{--                                                                                     src="{{asset('images/PlaceholderLogo.png')}}">--}}
{{--                                                                            @else--}}
{{--                                                                                <img id="imagePreview"--}}
{{--                                                                                     src="{{Storage::disk('s3')->url($salescenter->logo)}}">--}}
{{--                                                                            @endempty--}}

{{--                                                                            <div class="image-upload">--}}
{{--                                                                                <label for="file-input">--}}
{{--                                                                                    Browse--}}
{{--                                                                                </label>--}}
{{--                                                                                <input id="file-input" name="logo"--}}
{{--                                                                                       type="file" accept="image/*"/>--}}
{{--                                                                            </div>--}}
{{--                                                                        </div>--}}
{{--                                                                        @if ($errors->has('logo'))--}}
{{--                                                                        <span class="help-block">--}}
{{--                                                                            {{ $errors->first('logo') }}--}}
{{--                                                                        </span>--}}
{{--                                                                        @endif--}}
{{--                                                                    </div>--}}
{{--                                                                </div>--}}

                                                                <div class="form-group mt15" id="dropzone-outer">
                                                                    <label>Sales Center Logo</label>
                                                                    <div class="img-outer" style="text-align: center;">
                                                                        @empty($salescenter->logo && Storage::disk('s3')->exists(config()->get('constants.aws_folder') . $salescenter->logo))
                                                                            <img id="imagePreview" src="{{asset('images/default_profile_photo.png')}}" >
                                                                        @else
                                                                            <img id="imagePreview" src="{{Storage::disk('s3')->url($salescenter->logo)}}" >
                                                                            <input type="hidden" name="old_url" value="{{$salescenter->logo}}">
                                                                        @endempty
                                                                    </div>
                                                                    <div class="dropzone files-container mt15" id="salescenter-logo">
                                                                        <div class="fallback">
                                                                            <input name="salescenter_logo" type="file" />
                                                                        </div>
                                                                    </div>
                                                                    @include('preview-dropzone')
                                                                </div>
                                                            @endif
                                                            @if(auth()->user()->hasPermissionTo('edit-sales-center'))
                                                                <div class="col-xs-12 col-sm-12 col-md-12 bottom_btns">
                                                                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                                                        <div class="btn-group mt30 mb30">
                                                                            @if(Request::route()->getName() != 'client.salescenter.show')
                                                                                <button type="button"
                                                                                        class="btn btn-green" id="btnSave">
                                                                                    Save
                                                                                </button>
                                                                                <a href="{{ route('client.show',$client->id) }}#SalesCenter"
                                                                                   class="btn btn-red">Cancel</a>
                                                                            @else
                                                                                @if($salescenter->status == "active" && $salescenter->isActiveClient())
                                                                                    <a href='{{route("client.salescenters.edit", array($client_id, $salecenter_id))}}'
                                                                                   class="btn btn-green">Edit</a>
                                                                                @else
                                                                                    <a class="btn btn-green cursor-none" disabled>Edit</a>
                                                                                   
                                                                                @endif
                                                                                <a href="{{ route('client.show',$client->id) }}#SalesCenter"
                                                                                   class="btn btn-red">Back</a>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <!--about ends-->

                                    <div role="tabpanel" class="tab-pane" id="SalesCenterLocation">
                                        @include('client.salescenter_new.location.index')
                                    </div>
                                @if(auth()->user()->hasPermissionTo('view-sales-users'))
                                    <!--Sales center user starts-->
                                        <div role="tabpanel" class="tab-pane" id="SalesCenterUser">
                                            @include('client.salescenter_new.user.index')
                                        </div>

                                @endif

                                @if(auth()->user()->hasPermissionTo('view-sales-agents'))
                                    <!--Sales agent content starts-->
                                        <div role="tabpanel" class="tab-pane" id="SalesAgent">
                                            @include('client.salescenter.salescenteragent')
                                        </div>
                                        <!--Sales agent content ends-->
                                    @endif
                                    <div role="tabpanel" class="tab-pane" id="Brands">
                                            @include('client.salescenter.brands')
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
        $("#name").keyup(function() {
            var string = $(this).val();
            var matches = string.match(/\b(\w)/g);
            console.log(matches);
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
    @if(Request::route()->getName() == 'client.salescenter.show')
        <script>
            $(document).ready(function () {
                $("#salescenter-edit-form :input").prop("disabled", true);
            });
        </script>
    @endif
    <script>
        function submitClientForm() {
            $("#btnSave").prop("disabled", true);
            let formData = $('#salescenter-edit-form').serializeArray();
            $.ajax({
                url: '{{ route('client.salescenters.update', array($client->id, $salescenter->id)) }}',
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
            url: "{{ route('client.salescenters.update', array($client->id, $salescenter->id)) }}",
            autoProcessQueue: false,
            paramName: "salescenter_logo",
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
            let data = $('#salescenter-edit-form').serializeArray();
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

        insDropzone.on('error', function(file, err) {
            errorHandler(err);
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
            window.location.href = "{{ route('client.salescenters.edit', [$client->id, $salescenter->id]) }}";
        }

        function errorHandler(xhr) {
            $(window).scrollTop( $(".agent-detailform").offset().top );
            if (typeof xhr != 'undefined' && xhr.status == 422) {
                $("#btnSave").prop("disabled", false);
                printErrorMsgNew($("#salescenter-edit-form"), xhr.responseJSON.errors);
            } else {
                printAjaxErrorMsg(xhr);
            }
        }
    </script>
@endpush
