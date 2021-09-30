@extends('layouts.admin')
@section('content')

<?php
if(Auth::user()->access_level == 'client' || Auth::user()->access_level == 'salescenter') {
    $breadcrum = array(
        array('link' => route("client.show", array($client->id)), 'text' =>  $client->name),
        array('link' => route("client.show", array($client->id))."#Dispositions", 'text' =>  "Dispositions"),
        array('link' => "", 'text' =>  'Bulk Upload'),
    );
} else {
    $breadcrum = array(
        array('link' => route('client.index'), 'text' =>  'Clients'),
        array('link' => route("client.show", array($client->id)), 'text' =>  $client->name),
        array('link' => route("client.show", array($client->id))."#Dispositions", 'text' =>  "Dispositions"),
        array('link' => "", 'text' =>  'Bulk Upload'),
    );
}
 
breadcrum($breadcrum);
?>
<div class="tpv-contbx">
    <div class="container ui-droppable" style="width: 1723px;">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3">
                    <div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
                        <div class="client-bg-white min-height-solve">
                            <div class="message"></div>
                            @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif
                            @if ($message = Session::get('error'))
                            <div class="alert alert-danger">
                                <p>{{ $message }}</p>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif
                            <div class="row">
                                <div class="col-md-6">
                                    <h1>Bulk Upload</h1>
                                </div>

                                <div class="col-md-6">
                                    <a href="{{ route('disposition.downloadsample') }}" class="btn btn-green pull-right">Download Sample File</a>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="sales_tablebx mb30 mt30">
                                <p>File Description :</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="acjin">
                                                <th>Column name</th>
                                                <th>Description</th>
                                                <th>Data type (length)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="list-users">
                                                <td class="dark_c">Category</td>
                                                <td class="grey_c">You can enter the category name in this field. (<b>Accepted value:-</b> Declined, Call Disconnected, Verified, E-signature Cancel, Do Not Enroll)</td>
                                                <td class="grey_c">string 255 bytes</td>
                                            </tr>
                                            <tr class="list-users">
                                                <td class="dark_c">Description</td>
                                                <td class="grey_c">You can enter description in this field.</td>
                                                <td class="grey_c">string 255 bytes</td>
                                            </tr>
                                            <tr class="list-users">
                                                <td class="dark_c">Disposition Group</td>
                                                <td class="grey_c">You can enter disposition group in this field. (<b>Accepted value:-</b> Customer, Sales Agent, Lead Detail, Other)</td>
                                                <td class="grey_c">string 255 bytes</td>
                                            </tr>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @if ($messages = Session::get('dataErrors'))                        
                            <div class="alert alert-warning" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <div class="scrollbar-inner bk-ht">
                                    <p style="color: red; font-weight: bold;"> Bulk Upload Failed.</p>
                                    <?php    array_walk($messages,'printDataErrors'); ?>
                                </div>
                            </div>
                        @endif
                        <div id="data-errors"></div>

                        <div class="client-bg-white  min-height-solve mt30">

                            <form id="import-form" action="#" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="bulk-bottom-area">
                                    
                                      <!---new file-uploader------>
                                    <div class="col-md-3 col-md-offset-4">
                                        <div class="form-group mt15 mb30 text-left">
                                            <label class="text-left">Upload File</label>
                                            <div class="dropzone files-container " id="upload-file">
                                                <div class="fallback">
                                                    <input name="file" type="file"/>
                                                </div>
                                            </div>                                        
                                            @include('preview-dropzone')
                                        </div>
                                        <input type="hidden" name="upload_file">
                                    </div> 
                                    <!--end--new-file-uploader--->                                    
                                    <div class="row mt30">
                                        <div class="col-xs-12 col-md-12">
                                            <button class="btn btn-green mr15" id="upload-btn" type="button">Upload</button>
                                            <a href="{{ route('client.show', array($client->id))}}#Dispositions"><button class="btn btn-red" type="button">Cancel</button></a>
                                        </div>
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
@endsection
@push('scripts')
    <script>
        
        Dropzone.autoDiscover = false;
        //Dropzone related code
        var target = "#upload-file";

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
        var insDropzone = new Dropzone("div#upload-file", {
            url: "{{ route('disposition.import',$client->id) }}",
            autoProcessQueue: false,
            paramName: "upload_file",
            parallelUploads: 1,
            maxFiles: 1,
            uploadMultiple: false,
            //acceptedFiles: 'image/*',
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
                document.getElementById("upload-btn").addEventListener("click", function(e) {
                    //Make sure that the form isn't actually being sent.
                    if (objDropzone.getUploadingFiles().length === 0 && objDropzone.getQueuedFiles().length === 0) {
                        //submitClientForm();
                    } else {
                        e.preventDefault();
                        e.stopPropagation();
                        objDropzone.processQueue();
                    }
                });
            }
        });

        insDropzone.on('sending', function(file, xhr, formData) {
            $("#upload-btn").prop("disabled", true);
            formData.append("_token", CSRF_TOKEN);
        });

        insDropzone.on('success', function (file, res) {
            this.emit("complete", file);
            successHandler(res);
        });

        insDropzone.on('error', function(file, err, xhr) {
            $("#upload-btn").prop("disabled", false);
            if (typeof xhr != 'undefined' && xhr.status == 422) {
                if (err.status == 'dataErrors') {
                    $('#data-errors').html(err.errors);
                } else {
                    printErrorMsgNew($("#import-form"), err.errors);
                }
            } else if(typeof xhr != 'undefined' && xhr.status == 500) {
                errorHandler(err.message);
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

        function successHandler(res) {
            $("#upload-btn").prop("disabled", false);
            window.location.href = "{{ route('client.show', array($client->id))}}#Dispositions";
            $(".help-block").remove('');
            $("#upload-btn").prop("disabled", false);
            printAjaxSuccessMsg(res.message);
                $(".alert-warning").fadeTo(500, 0).slideUp(500, function () {
                    $(this).remove();
                });
            $(window).scrollTop(0);
        }

        function errorHandler(xhr) {
            $("#upload-btn").prop("disabled", false);
            $(window).scrollTop( $(".ui-droppable").offset().top );
            if (typeof xhr != 'undefined' && xhr.status == 422) {
                printErrorMsgNew($("#import-form"), xhr.responseJSON.errors);
            } else {
                printAjaxErrorMsg(xhr);
            }
        }
    </script>
@endpush
