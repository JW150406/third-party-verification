
/************************************************************

	Main Scripts

*************************************************************/

!function ($) {

	"use strict";

	// Global Dropz object
	var Dropz = Dropz || {};


	Dropz = {

	    /**
		 * Fire all functions
		 */
		init: function() {
			var self = this,
				obj;

			for (obj in self) {
				if ( self.hasOwnProperty(obj)) {
					var _method =  self[obj];
					if ( _method.selector !== undefined && _method.init !== undefined ) {
						if ( $(_method.selector).length > 0 ) {
							_method.init();
						}
					}
				}
			}
		},


		/**
		 * Files upload
		 */
		userFilesDropzone: {
			// selector: '.dropzone',
			// init: function() {
			// 	var base = this,
			// 		container = $(base.selector);

				// base.initFileUploader(base, '.dropzone');
			// },
			// initFileUploader: function(base, target) {
			// 	var previewNode = document.querySelector("#cust-dropzone-template"), // Dropzone template holder
			// 		warningsHolder = $("#warnings"); // Warning messages' holder
            //
			// 	previewNode.id = "";
            //
			// 	var previewTemplate = previewNode.parentNode.innerHTML;
			// 	previewNode.parentNode.removeChild(previewNode);
            //
			// 	var custDropzone = new Dropzone(target, {
			// 		url: ($(target).attr("action")) ? $(target).attr("action") : " ", // Check that our form has an action attr and if not, set one here
	        //         maxFiles: 1,
			// 		maxFilesize: 20,
			// 		acceptedFiles: "image/*,application/pdf,.doc,.docx,.xls,.xlsx,.csv,.tsv,.ppt,.pptx,.pages,.odt,.rtf",
			// 		previewTemplate: previewTemplate,
			// 		previewsContainer: "#previews",
			// 		clickable: true,
            //
			// 		createImageThumbnails: true,
			// 		dictDefaultMessage: "Drop files here to upload, Or Browse", // Default: Drop files here to upload
			//
			// 		dictFallbackMessage: "Your browser does not support drag'n'drop file uploads.", // Default: Your browser does not support drag'n'drop file uploads.
			// 		dictFileTooBig: "File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.", // Default: File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.
			// 		dictInvalidFileType: "You can't upload files of this type.", // Default: You can't upload files of this type.
			// 		dictResponseError: "Server responded with {{statusCode}} code.", // Default: Server responded with {{statusCode}} code.
			// 		dictCancelUpload: "Cancel upload.", // Default: Cancel upload
			// 		dictUploadCanceled: "Upload canceled.", // Default: Upload canceled.
			// 		dictCancelUploadConfirmation: "Are you sure you want to cancel this upload?", // Default: Are you sure you want to cancel this upload?
			// 		dictRemoveFile: "Remove file", // Default: Remove file
			// 		dictRemoveFileConfirmation: null, // Default: null
			// 		dictMaxFilesExceeded: "You can not upload any more files.", // Default: You can not upload any more files.
			// 		dictFileSizeUnits: {tb: "TB", gb: "GB", mb: "MB", kb: "KB", b: "b"},
            //
			// 	});
            //
			// 	Dropzone.autoDiscover = false;
            //
			// 	custDropzone.on("addedfile", function(file) {
			// 		$('.preview-container').css('visibility', 'visible');
			// 		file.previewElement.classList.add('type-' + base.fileType(file.name)); // Add type class for this element's preview
			// 	});
            //
			// 	custDropzone.on("totaluploadprogress", function (progress) {
            //
			// 		var progr = document.querySelector(".progress .determinate");
            //
			// 		if (progr === undefined || progr === null) return;
            //
			// 		progr.style.width = progress + "%";
			// 	});
            //
			// 	custDropzone.on('dragenter', function () {
			// 		$(target).addClass("hover");
			// 	});
            //
			// 	custDropzone.on('dragleave', function () {
			// 		$(target).removeClass("hover");
			// 	});
            //
			// 	custDropzone.on('drop', function () {
			// 		$(target).removeClass("hover");
			// 	});
            //
			// 	custDropzone.on('addedfile', function () {
            //
			// 		// Remove no files notice
			// 		$(".no-files-uploaded").slideUp("easeInExpo");
            //
			// 	});
            //
			// 	custDropzone.on('removedfile', function (file) {
            //
			// 		$.ajax({
			// 			type: "POST",
			// 			url: ($(target).attr("action")) ? $(target).attr("action") : " ",
			// 			data: {
			// 				target_file: file.upload_ticket,
			// 				delete_file: 1
			// 			}
			// 		});
            //
			// 		// Show no files notice
			// 		if ( base.dropzoneCount() == 0 ) {
			// 			$(".no-files-uploaded").slideDown("easeInExpo");
			// 			$(".uploaded-files-count").html(base.dropzoneCount());
			// 		}
            //
			// 	});
            //
			// 	custDropzone.on("success", function(file, response) {
			// 		let parsedResponse = JSON.parse(response);
			// 		file.upload_ticket = parsedResponse.file_link;
            //
            //
			// 		// Make it wait a little bit to take the new element
			// 		setTimeout(function(){
			// 			$(".uploaded-files-count").html(base.dropzoneCount());
			// 			console.log('Files count: ' + base.dropzoneCount());
			// 		}, 350);
            //
            //
			// 		// Something to happen when file is uploaded, like showing a message
			// 		if ( typeof parsedResponse.info !== 'undefined' ) {
			// 			console.log(parsedResponse.info);
			// 			warningsHolder.children('span').html(parsedResponse.info);
			// 			warningsHolder.slideDown("easeInExpo");
			// 		}
			// 	});
			// },

			// dropzoneCount: function() {
			// 	var filesCount = $("#previews > .dz-success.dz-complete").length;
			// 	return filesCount;
			// },
			// fileType: function(fileName) {
			// 	var fileType = (/[.]/.exec(fileName)) ? /[^.]+$/.exec(fileName) : undefined;
			// 	return fileType[0];
			// }
		}
	}
	
	$(document).ready(function() {
		Dropz.init();
	});

}(jQuery);


