@extends('layouts.tpvagent')

@section('content')

<style>
/*--this css added temparory for now----*/
.select2 {
    visibility: visible;
}
</style>

<div class="tpv-contbx ">
    <div class="">
        <div class="">
            <div class="">
                <div class="cont_bx3">
                    <div class="tpv_heading">
                        <div class="client-bg-white">
                            <h1>Profile</h1>
                            <div class="sales_tablebx">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12  ">

                                        <div class="agent-detailform tab-content">
                                            <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3">

                                                <div class="message">
                                                    @if ($message = Session::get('success'))
                                                    <div class="alert alert-success">
                                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                        <p>{{ $message }} </p>
                                                        
                                                    </div>
                                                    @endif
                                                </div>

                                                <form method="POST" id="edit-profile" action="{{ route('tpvagents.edit-profile') }}" enctype="multipart/form-data" data-parsley-validate>
                                                    {{ csrf_field() }}
                                                    {{ method_field('post') }}

                                                    <div class="form-group{{  $errors->has('first_name')  ? ' has-error' : '' }}">
                                                        <label for="first_name">Name</label>
                                                        <?php echo getFormIconImage('images/form-name.png') ?>
                                                        <input id="name" disabled="disabled" autocomplete="off" type="text" class="form-control" name="name" value="{{$user->first_name}} {{$user->last_name}} ">

                                                    </div>

                                                    <div class="form-group{{  $errors->has('last_name')  ? ' has-error' : '' }}">
                                                        <label for="email">Email</label>
                                                        <?php echo getFormIconImage('images/form-email.png') ?>
                                                        <input id="email" disabled="disabled" type="text" class="form-control" name="email" value="{{$user->email}}" required>
                                                    </div>


                                                    @if($user->access_level == 'company')
                                                    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                                        <label for="title">Title</label>
                                                        <?php echo getFormIconImage('images/title.png') ?>
                                                        <input id="title" type="text" class="form-control" name="title" value="{{$user->title}}" autocomplete="off" placeholder="Title">
                                                        @if ($errors->has('title'))
                                                        <span class="help-block">
                                                            {{ $errors->first('title') }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                    @endif

                                                    <div class="form-group">
                                                        <?php $clients = ''; ?>
                                                        @foreach($assignedclients as $assignedclient)
                                                        <?php $clients .= $assignedclient->name . ', '; ?>
                                                        @endforeach
                                                        <?php $clients = rtrim($clients, ', '); ?>
                                                        <label for="password">Clients</label>
                                                        <input id="title" disabled="disabled" type="text" class="form-control" name="clients" value="{{$clients}}" >
                                                    </div>

                                                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                                        <label for="password">Update Password</label>
                                                        <?php echo getFormIconImage('images/form-pass.png') ?>
                                                        <input id="password" type="password" class="form-control" name="password" autocomplete="new-password" data-parsley-required-message="Please enter password" data-parsley-minlength="6" data-parsley-minlength-message="The password must be at least 6 characters.">
                                                        @if ($errors->has('password'))
                                                        <span class="help-block">
                                                            {{ $errors->first('password') }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                                        <label for="password_confirmation">Confirm Password</label>
                                                        <?php echo getFormIconImage('images/form-pass.png') ?>
                                                        <input id="password_confirmation" autocomplete="new-confirm-password" type="password" class="form-control" data-parsley-trigger="keyup" data-parsley-required-message="Passwords must match" data-parsley-equalto-message="Passwords must match" name="password_confirmation" data-parsley-equalto="#password"  >
                                                        @if ($errors->has('password_confirmation'))
                                                        <span class="help-block">
                                                            {{ $errors->first('password_confirmation') }}
                                                        </span>
                                                        @endif

                                                    </div>
                                                    <div class="form-group profile-timezone {{ $errors->has('timezone') ? ' has-error' : '' }}">
                                                        <label for="timezone">Set Timezone</label>
                                                        <?php //echo getFormIconImage('images/form-pass.png') ?>
                                                        <?php $timeZones = getTimeZoneList(); ?>
                                                        <select class="select2 form-control timezone-select" name="timezone" id="timezone-select-id">
                                                            
                                                            @foreach($timeZones as $key => $val)
                                                                <?php 
                                                                    $k = trim(substr($val,0,strpos($val,'(')))
                                                                ?>
                                                                <option value="{{$k}}" @if($k== $user->timezone) selected @endif>{{$val}} </option>
                                                            @endforeach
                                                       </select>
                                                        
                                                        @if ($errors->has('timezone'))
                                                        <span class="help-block">
                                                            {{ $errors->first('timezone') }}
                                                        </span>
                                                        @endif

                                                    </div>

                                                    <!--new--file-uploader-->
                                                    <div class="form-group mt15">
                                                        <label>Profile Photo</label>
                                                        <div class="img-outer" style="text-align: center;">
                                                            @empty($user->profile_picture && Storage::disk('s3')->exists(config()->get('constants.aws_folder') . $user->profile_picture))
                                                                <img id="imagePreview" src="{{asset('images/default_profile_photo.png')}}" >
                                                            @else
                                                                <img id="imagePreview" src="{{Storage::disk('s3')->url($user->profile_picture)}}" >
                                                                <input type="hidden" name="old_url" value="{{$user->profile_picture}}">
                                                            @endempty
                                                        </div>
                                                        <div class="dropzone files-container" id="profilePicture" style="margin-top: 16px;">
                                                            <div class="fallback">
                                                                <input name="file" type="file" />
                                                            </div>
                                                        </div>
                                                        @include('preview-dropzone')
                                                    </div>
                                                    <!--End--new--file-uploader-->

                                                    <div class="clearfix"></div>
                                                    <div class="btnintable bottom_btns" style="padding-top: 0px;">
                                                        <div class="btn-group">
                                                            <button type="button" id="update-profile" class="btn btn-green"> Update</button>
                                                            <a href="{{route('tpvagents.sales') }}" id="agent-back-btn" class="btn  btn-red">Back </a>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    @include('admin.twiliosettingspoup')
@endsection
@push('scripts')
    <script>
        window.Parsley.addValidator('maxFileSize', {
            validateString: function(_value, maxSize, parsleyInstance) {
                if (!window.FormData) {
                    alert('You are making all developpers in the world cringe. Upgrade your browser!');
                    return true;
                }
                var files = parsleyInstance.$element[0].files;
                return files.length != 1  || files[0].size <= 5000000;
            },
            requirementType: 'integer',
            messages: {
                en: 'This file should not be larger than %s Mb',
                fr: 'Ce fichier est plus grand que %s mb.'
            }
        });


            window.ParsleyValidator
                .addValidator('fileextension', function (value, requirement) {
                    var tagslistarr = requirement.split(',');
                    var fileExtension = value.split('.').pop();
                    var arr=[];
                    $.each(tagslistarr,function(i,val){
                        arr.push(val);
                    });
                    if(jQuery.inArray(fileExtension, arr)!='-1') {
                        console.log("is in array");
                        return true;
                    } else {
                        console.log("is NOT in array");
                        return false;
                    }
                }, 32)
                .addMessage('en', 'fileextension', 'Please upload JPG,JPEG,PNG and gif extension file');





        /** print ajax respose success message **/
        function printAjaxSuccessMsg(message) {
            $(".message").html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><p>' + message + '</p></div>');
            fadeAlert();
        }
        /** print ajax respose error message **/
        function printAjaxErrorMsg(message) {
            $(".message").html('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><p>' + message + '</p></div>');
            fadeAlert();
        }

        /** for hide response **/
        function fadeAlert() {
            window.setTimeout(function () {
                $(".alert-success,.alert-danger").fadeTo(1500, 0).slideUp(500, function () {
                    $(this).remove();
                });
            }, 3000);
        }
       

    </script>

    <script>
        function submitProfileForm() {
            $("#update-profile").prop("disabled", true);
            let formData = $('#edit-profile').serializeArray();
            $.ajax({
                url: '{{route("tpvagents.update-profile")}}',
                method:'POST',
                data: formData,
                success:function(res) {
                    profileAfterUpdate();
                },
                error: function(response) {
                    profileErrorHandler(response);
                }
            });
        }

        Dropzone.autoDiscover = false;
        //Dropzone related code
        var target = "#profilePicture";

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
        var insDropzone = new Dropzone("div#profilePicture", {
            url: "{{ route('tpvagents.update-profile') }}",
            autoProcessQueue: false,
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
                document.getElementById("update-profile").addEventListener("click", function(e) {
                    //Make sure that the form isn't actually being sent.
                    if (objDropzone.getUploadingFiles().length === 0 && objDropzone.getQueuedFiles().length === 0) {
                        // alert("if");
                        submitProfileForm();
                    } else {
                        // alert("else");
                        e.preventDefault();
                        e.stopPropagation();
                        objDropzone.processQueue();
                    }
                });
            }
        });

        insDropzone.on('sending', function(file, xhr, formData) {
            $("#update-profile").prop("disabled", true);
            let data = $('#edit-profile').serializeArray();
            $.each(data, function (key, el) {
                if (el.name == "_token") {
                    formData.append("_token", CSRF_TOKEN);
                } else {
                    formData.append(el.name, el.value);
                }
            });
        });

        insDropzone.on('success', function (file, res) {
            // alert("drp success");
            // console.log(res);
            this.emit("complete", file);
            profileAfterUpdate(res);
        });

        insDropzone.on('error', function(file, err) {
            // alert("drp error");
            profileErrorHandler(err);
        });

        insDropzone.on('complete', function(file) {
            // alert("complete");
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
            // Remove no files notice
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

        function profileAfterUpdate() {
            $("#update-profile").prop("disabled", false);
            window.location.href = "{{ route('tpvagents.edit-profile') }}";
        }

        function profileSuccessHandler(res) {
            $(window).scrollTop( $(".agent-detailform").offset().top );
            if (res.status == "success") {
                printAjaxSuccessMsg(res.message);
            } else {
                printAjaxErrorMsg(res.message);
            }
        }

        function profileErrorHandler(xhr) {
            $(window).scrollTop( $(".agent-detailform").offset().top );
            if (typeof xhr != 'undefined' && xhr.status == 422) {
                $("#update-profile").prop("disabled", false);
                printErrorMsgNew($("#edit-profile"), xhr.responseJSON.errors);
            } else {
                printAjaxErrorMsg(xhr);
            }
        }
    </script>
    @include('admin.manage_profile_photo')
@endpush
