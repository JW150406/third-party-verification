$(document).ready(function() {
    var oldTagValue,address;
    $(document).on('click','.edit-tag',function() {
        oldTagValue = this;
        let fieldId =  $(this).data('field-id');
        address =  $(this).data('address');
        let leadId =  $(this).data('lead-id');
        console.log('click on field: '+fieldId);
        getTagFields(fieldId, address,leadId);
    });


    // update tag value after tag edit
    $("#edit-tag-form").submit(function(e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.

        var form = $(this);
        var formData = [];
        var checkboxData = [];
        form.find(":input:not([type=hidden])").not('input:radio:unchecked').map(function () {
            if (this.value != '') {
                formData.push(this.value); 
            } 
        });

        form.find("input:checkbox:checked").map(function () {
            if (this.value != '') {
                checkboxData.push(this.value); 
            } 
        });

        if (checkboxData.length > 0) {
            var newTagValue = checkboxData.join(', ');
        } else if (address == '') {
            var newTagValue = formData.join(' ');
        } else {
            var newTagValue = formData.join(', ');
        }
        console.log(newTagValue);
        $(oldTagValue).html(newTagValue);

        var url = form.attr('action');
        $('.ajax-loader').show(); 
        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(), // serializes the form's elements.
            success: function(response) {
                $('.ajax-loader').hide(); 
                $('#edit-tag-modal').modal("hide");
                if (response.status) {
                    printAjaxSuccessMsg(response.message); 
                } else {
                    printAjaxErrorMsg(response.message); 
                }
            }
        });
    });
});

// get tag field form for edit
function getTagFields(fieldId = '', addressType= '',leadId= '') {
    let telesaleId = leadId;
    if(leadId == ''){
        telesaleId = $("#telesale_reference_id").val();
    }

    if (fieldId > 0) {        
        $.ajax({
            url: getTagFieldUrl,
            data: {field_id : fieldId,telesale_id : telesaleId, address_type: addressType},
            success: function (response) {
                if (response.status) {
                    $(".edit-tag-body").html(response.data);
                    $("#edit-tag-modal").modal('show');
                } else {
                    printAjaxErrorMsg(response.message);
                }
            }
        });
    }
}

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
    }, 4000);
}