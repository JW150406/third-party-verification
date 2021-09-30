(function($) {
    "use strict"; // Start of use strict
    // disable show form
    $("#client-form :input").prop("disabled", true);
    $("#client-save-btn").hide();
    $("#client-cancel-btn").hide();
    $("#client-edit-btn").click(function(e) {
        $("#dropzone-outer").show();
        e.preventDefault();        
        $("#client-form :input").prop("disabled", false);
        $("#client-edit-btn").hide();
        $("#client-back-btn").hide();
        $("#client-save-btn").show();
        $("#client-cancel-btn").show();
    });
    $("#client-cancel-btn").click(function(e) {
        $("#dropzone-outer").hide();
        e.preventDefault();
        $('#client-form').parsley().reset();
        $("#client-form :input").prop("disabled", true);
        $("#client-edit-btn").show();
        $("#client-back-btn").show();
        $("#client-save-btn").hide();
        $("#client-cancel-btn").hide();
    });

})(jQuery); // End of use strict
