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
    url: $("#import-form").attr('action'),
    autoProcessQueue: false,
    paramName: "upload_file",
    parallelUploads: 1,
    timeout: 0,
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
                printRequiredError();
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
    // printAjaxSuccessMsg("Success");
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
    //$('#data-errors').html("");
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
    $(".help-block").remove('');
    $("#upload-btn").prop("disabled", false);
    printAjaxSuccessMsg(res.message);
        $(".alert-warning").fadeTo(500, 0).slideUp(500, function () {
            $(this).remove();
        });
    $(window).scrollTop(0);
}

function errorHandler(xhr) {
    console.log(xhr);
    $("#upload-btn").prop("disabled", false);
    $(window).scrollTop( $(".ui-droppable").offset().top );
    if (typeof xhr != 'undefined' && xhr.status == 422) {
        printErrorMsgNew($("#import-form"), xhr.responseJSON.errors);
    } else {
        printAjaxErrorMsg(xhr);
    }
}

function printRequiredError() {
    $(".help-block").remove('');
    $("#import-form").find("[name='upload_file']").after("<span class='help-block' >This field is required</span>");
}