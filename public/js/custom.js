jQuery(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    /*------------modal-animation-----*/

    // function testAnim(x) {
    //     $('.modal .modal-dialog').attr('class', 'modal-dialog  ' + x + '  animated');
    // };
    // $('#myModal').on('show.bs.modal', function (e) {
    //     var anim = "bouncein";
    //       testAnim(anim);
    // })
    // $('#myModal').on('hide.bs.modal', function (e) {
    //   var anim = $('#exit').val();
    //       testAnim(anim);
    // })


    // $('.modal-dialog').addClass("zoomIn animated");
    

/*------------modal-animation-----*/

    $('.dataTables_paginate').click(function () {
        $('html, body').animate({
            scrollTop: $(".container").offset().top
        }, 3000);
    });

    $(" select:disabled").each(function () {
        $(this).addClass("hide-down");
    });
   
    $(".modal").on('hidden.bs.modal', function(){
        $('#select2-drop').select2("close");
    });
    
    // $('body').on('click', function () {
    //     $('#select2-drop').select2("close");
    // });

    // $('.select2').on('click', function () {
    //     $('#select2-drop').select2("close");
    // });

    $(document).ready(function() {
        $('.select2').select2();
    });

    jQuery('.select2').on('select2:open', function (e) {
   setTimeout(function(){ jQuery('.select2-dropdown').slideDown("slow", "easeInOutQuint"); }, 200);
});

    


    if (jQuery(window).width() > 991) {
        var get_container_width = jQuery('.main-content .container').width();
        var get_difference = (get_container_width - 150);
        jQuery('.main-header').addClass('open');
        jQuery('.main-content').css(
            {
                "margin-left": "250px",
                "width": "calc(100% - 250px)",
            }
        );

        jQuery('#mySidenav').css('width', '250px');
        jQuery('.header-menu-toggler').css('left', '250px');


    } else {

        var get_container_width = get_difference = "100%";
        jQuery('.header-menu-toggler').css('left', '0');
    }

    jQuery(window).on('load', function () {
        setTimeout(function () {
            jQuery('.preloader').hide();
        }, 1000);

    });

    jQuery('.main-content .container').css('width', get_difference);
    jQuery('body').on('click', '.header-menu-toggler', function () {


        if (jQuery('.main-header').hasClass('open')) {

            jQuery('#mySidenav').css('width', '0');
            // jQuery('#main').css('margin-left','0');
            jQuery('.main-content').css('margin-left', '0');
            jQuery('.main-content .container').css('width', get_container_width);
            jQuery('.header-menu-toggler').css('left', '0');
            jQuery('.main-content').css('width', '100%');

        } else {

            jQuery('#mySidenav').css('width', '250px');
            // jQuery('#main').css('margin-left','250px');

            if (jQuery(window).width() > 991) {
                jQuery('.header-menu-toggler').css('left', '250px');
                jQuery('.main-content').css('margin-left', '250px');
                jQuery('.main-content').css('width', 'calc( 100% - 250px)');
                jQuery('.main-content .container').css('width', get_difference);

            } else {
                jQuery('.header-menu-toggler').css('left', '230px');
            }
        }
        jQuery('.main-header').toggleClass('open');
        setTimeout(function () {
            $('div[_echarts_instance_]').each(function () {
                let id = $(this).attr('_echarts_instance_');
                let echart = window.echarts.getInstanceById(id)
                echart.resize();
            });
        }, 1000);
    });

    window.setTimeout(function () {
        $(".alert-success,.alert-danger").fadeTo(500, 0).slideUp(500, function () {
            $(this).remove();
        });
    }, 8000);

    $('.close').click(function () {
        $(".alert-danger").css("display", "none");
    });
});

$(function () {
    $_this = $;
    if ($_this.fn.daterangepicker) {
        $_this('.singledate').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
        });
    }




    function json_response_display(jsonresponse, responseclass, formobject) {

        try {

            var res = jsonresponse;
            if (res.status == 'success') {
                var responsemessage = "<div class='alert alert-success'>" + res.message + "</div>";
                formobject.reset();

                if (typeof res.url === 'undefined') {
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                }
                else {
                    setTimeout(function () {
                        location.href = res.url;
                    }, 2000);
                }



            } else if (res.status == 'error') {

                var responsemessage = "<div class='alert alert-danger'>";
                for (var i = 0; i < res.errors.length; i++) {
                    responsemessage = responsemessage + "<p>" + res.errors[i] + "</p>";
                }
                responsemessage = responsemessage + "</div>";
            } else {
                responsemessage = "<div class='alert alert-danger'>Something went wrong please try again.</div>";
            }
            $_this('.' + responseclass, formobject).html(responsemessage);

        } catch (err) {
            /* Check using parsing json */
            try {

                var res = jQuery.parseJSON(jsonresponse);
                if (res.status == 'success') {
                    var responsemessage = "<div class='alert alert-success'>" + res.message + "</div>";
                    formobject.reset();

                    if (typeof res.url === 'undefined') {
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    }
                    else {
                        setTimeout(function () {
                            location.href = res.url;
                        }, 2000);
                    }



                } else if (res.status == 'error') {

                    var responsemessage = "<div class='alert alert-danger'>";
                    for (var i = 0; i < res.errors.length; i++) {
                        responsemessage = responsemessage + "<p>" + res.errors[i] + "</p>";
                    }
                    responsemessage = responsemessage + "</div>";
                } else {
                    responsemessage = "<div class='alert alert-danger'>Something went wrong please try again.</div>";
                }
                $_this('.' + responseclass, formobject).html(responsemessage);

            } catch (err) {
                $_this('.' + responseclass, formobject).html("<div class='alert alert-danger'>Something went wrong please try again.</div>");
            }


        }

    }
    function makeemptyresponse(responseclass, formobject) {
        $_this('.' + responseclass, formobject).html('');
    }
    function dataInProcess(spinnerclass, formobject) {
        $_this('[type="submit"]', formobject).attr('disabled', 'disabled');
        $_this('.' + spinnerclass, formobject).html('<i class="fa fa-spinner fa-spin"></i>');
    }
    function dataIntProcessed(spinnerclass, formobject) {
        $_this('[type="submit"]', formobject).removeAttr('disabled');
        $_this('.' + spinnerclass, formobject).html('Save');
    }

    function validateformfields(formid) {
        var error = 0;
        $_this('#' + formid + ' .error-required').removeClass('error-required');
        $_this('form#' + formid + ' .required').each(function () {
            // alert($_this(this).val());
            //   console.log($_this(this));
            if ($_this(this).val() == "") {
                error = 1;
                $_this(this).addClass('error-required');
            }
        });
        if (error == 1) {
            return false;
        } else {
            return true;
        }

    }
    function hidealert() {
        setTimeout(function () {
            $_this('.alert').remove();
        }, 5000);

    }


    /* TPV Team member save */
    $_this('body').on('submit', '#addnewteammember', function (e) {

        e.preventDefault();
        var formobj = this;
        var check_validation = validateformfields('addnewteammember');
        if (check_validation === false) {
            return false;
        }

        makeemptyresponse('ajax-response', formobj);
        dataInProcess('save-text', formobj);
        $_this.ajax({
            type: "POST",
            url: $_this(this).attr('action'),
            data: $_this(this).serialize(),
            success: function (response) {
                //$_this('save-text',formobj).html('Saved');  
                dataIntProcessed('save-text', formobj);
                json_response_display(response, 'ajax-response', formobj);
                hidealert();

            }, error: function (response) {
                json_response_display(response, 'ajax-response', formobj);
                hidealert();
            }
        });

    });
    /* TPV Team member save */
    $_this('body').on('submit', '#addnewtpvagent', function (e) {

        e.preventDefault();
        var formobj = this;
        var check_validation = validateformfields('addnewtpvagent');
        if (check_validation === false) {
            return false;
        }

        makeemptyresponse('ajax-response', formobj);
        dataInProcess('save-text', formobj);
        $_this.ajax({
            type: "POST",
            url: $_this(this).attr('action'),
            data: $_this(this).serialize(),
            success: function (response) {
                //$_this('save-text',formobj).html('Saved');  
                dataIntProcessed('save-text', formobj);
                json_response_display(response, 'ajax-response', formobj);
                hidealert();

            }, error: function (response) {
                json_response_display(response, 'ajax-response', formobj);
                hidealert();
            }
        });

    });
    /* TPV client save */
    $_this('body').on('submit', '#addnewclient', function (e) {

        e.preventDefault();
        var formobj = this;
        var check_validation = validateformfields('addnewtpvagent');
        if (check_validation === false) {
            return false;
        }
        var formData = new FormData(this);

        makeemptyresponse('ajax-response', formobj);
        dataInProcess('save-text', formobj);

        $_this.ajax({
            type: "POST",
            url: $_this(this).attr('action'),
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {

                //$_this('save-text',formobj).html('Saved');  
                dataIntProcessed('save-text', formobj);
                json_response_display(response, 'ajax-response', formobj);
                hidealert();

            }, error: function (response) {

                json_response_display(response, 'ajax-response', formobj);
                hidealert();
            }
        });

    });
    /* TPV client user save */
    $_this('body').on('submit', '#addnewclient_user', function (e) {

        e.preventDefault();
        var formobj = this;
        var check_validation = validateformfields('addnewclient_user');
        if (check_validation === false) {
            return false;
        }

        makeemptyresponse('ajax-response', formobj);
        dataInProcess('save-text', formobj);
        $_this.ajax({
            type: "POST",
            url: $_this(this).attr('action'),
            data: $_this(this).serialize(),
            success: function (response) {
                //$_this('save-text',formobj).html('Saved');  
                dataIntProcessed('save-text', formobj);
                json_response_display(response, 'ajax-response', formobj);
                hidealert();

            }, error: function (response) {
                json_response_display(response, 'ajax-response', formobj);
                hidealert();
            }
        });

    });
    /* TPV client salescenter */
    $_this('body').on('submit', '#addnewclient_salescenter', function (e) {

        e.preventDefault();
        var formobj = this;
        var check_validation = validateformfields('addnewclient_salescenter');
        if (check_validation === false) {
            return false;
        }

        makeemptyresponse('ajax-response', formobj);
        dataInProcess('save-text', formobj);
        $_this.ajax({
            type: "POST",
            url: $_this(this).attr('action'),
            data: $_this(this).serialize(),
            success: function (response) {
                //$_this('save-text',formobj).html('Saved');  
                dataIntProcessed('save-text', formobj);
                json_response_display(response, 'ajax-response', formobj);
                hidealert();

            }, error: function (response) {
                json_response_display(response, 'ajax-response', formobj);
                hidealert();
            }
        });

    });

    /* TPV locations */
    $_this('body').on('submit', '#addnewlocation', function (e) {
        e.preventDefault();
        var formobj = this;
        var check_validation = validateformfields('addnewlocation');
        if (check_validation === false) {
            return false;
        }
        makeemptyresponse('ajax-response', formobj);
        dataInProcess('save-text', formobj);
        $_this.ajax({
            type: "POST",
            url: $_this(this).attr('action'),
            data: $_this(this).serialize(),
            success: function (response) {
                //$_this('save-text',formobj).html('Saved');  
                dataIntProcessed('save-text', formobj);
                json_response_display(response, 'ajax-response', formobj);
                hidealert();

            }, error: function (response) {
                json_response_display(response, 'ajax-response', formobj);
                hidealert();
            }
        });

    });
    /* TPV Sales agent */
    $_this('body').on('submit', '#addnewagentform', function (e) {
        e.preventDefault();
        var formobj = this;
        var check_validation = validateformfields('addnewagentform');
        if (check_validation === false) {
            return false;
        }
        makeemptyresponse('ajax-response', formobj);
        dataInProcess('save-text', formobj);
        $_this.ajax({
            type: "POST",
            url: $_this(this).attr('action'),
            data: $_this(this).serialize(),
            success: function (response) {
                //$_this('save-text',formobj).html('Saved');  
                dataIntProcessed('save-text', formobj);
                json_response_display(response, 'ajax-response', formobj);
                hidealert();

            }, error: function (response) {
                json_response_display(response, 'ajax-response', formobj);
                hidealert();
            }
        });

    });
    /* TPV add new script */
    $_this('body').on('submit', '#addnewscriptform', function (e) {
        e.preventDefault();
        var formobj = this;
        var check_validation = validateformfields('addnewscriptform');
        if (check_validation === false) {
            return false;
        }
        makeemptyresponse('ajax-response', formobj);
        dataInProcess('save-text', formobj);
        $_this.ajax({
            type: "POST",
            url: $_this(this).attr('action'),
            data: $_this(this).serialize(),
            success: function (response) {
                //$_this('save-text',formobj).html('Saved');  
                dataIntProcessed('save-text', formobj);
                json_response_display(response, 'ajax-response', formobj);
                hidealert();

            }, error: function (response) {
                json_response_display(response, 'ajax-response', formobj);
                hidealert();
            }
        });

    });
    /* TPV add new script */
    $_this('body').on('submit', '#addnewdisposition', function (e) {
        e.preventDefault();
        var formobj = this;
        var check_validation = validateformfields('addnewdisposition');
        if (check_validation === false) {
            return false;
        }
        makeemptyresponse('ajax-response', formobj);
        dataInProcess('save-text', formobj);
        $_this.ajax({
            type: "POST",
            url: $_this(this).attr('action'),
            data: $_this(this).serialize(),
            success: function (response) {
                //$_this('save-text',formobj).html('Saved');  
                dataIntProcessed('save-text', formobj);
                json_response_display(response, 'ajax-response', formobj);
                hidealert();

            }, error: function (response) {
                json_response_display(response, 'ajax-response', formobj);
                hidealert();
            }
        });

    });

    /* TPV add new utility */
    $_this('body').on('submit', '#addnewutility', function (e) {
        e.preventDefault();
        var formobj = this;
        var check_validation = validateformfields('addnewutility');
        if (check_validation === false) {
            return false;
        }
        makeemptyresponse('ajax-response', formobj);
        dataInProcess('save-text', formobj);
        $_this.ajax({
            type: "POST",
            url: $_this(this).attr('action'),
            data: $_this(this).serialize(),
            success: function (response) {
                //$_this('save-text',formobj).html('Saved');  
                dataIntProcessed('save-text', formobj);
                json_response_display(response, 'ajax-response', formobj);
                hidealert();

            }, error: function (response) {
                json_response_display(response, 'ajax-response', formobj);
                hidealert();
            }
        });

    });

    /* TPV add new utility */
    $_this('body').on('submit', '#addnewtemplateform', function (e) {
        e.preventDefault();
        var formobj = this;
        var check_validation = validateformfields('addnewtemplateform');
        if (check_validation === false) {
            return false;
        }
        makeemptyresponse('ajax-response', formobj);
        dataInProcess('save-text', formobj);
        $_this.ajax({
            type: "POST",
            url: $_this(this).attr('action'),
            data: $_this(this).serialize(),
            success: function (response) {
                //$_this('save-text',formobj).html('Saved');  
                dataIntProcessed('save-text', formobj);
                json_response_display(response, 'ajax-response', formobj);
                hidealert();

            }, error: function (response) {
                json_response_display(response, 'ajax-response', formobj);
                hidealert();
            }
        });

    });

    /* TPV salesagent cancel lead */
    $_this('body').on('submit', '#cancel_lead_from', function (e) {
        e.preventDefault();
        var formobj = this;
        var check_validation = validateformfields('cancel_lead_from');

        if (check_validation === false) {
            return false;
        }
        makeemptyresponse('ajax-response', formobj);
        dataInProcess('save-text', formobj);
        $_this.ajax({
            type: "POST",
            url: $_this(this).attr('action'),
            data: $_this(this).serialize(),
            success: function (response) {
                //$_this('save-text',formobj).html('Saved');  
                dataIntProcessed('save-text', formobj);
                json_response_display(response, 'ajax-response', formobj);
                hidealert();

            }, error: function (response) {
                json_response_display(response, 'ajax-response', formobj);
                hidealert();
            }
        });

    });
    $_this('body').on('click', '.reports-tabs li a', function () {
        $_this('.report-tabs-result').hide();
        if ($_this(this).attr('href') == '#agentdetail') {
            $_this('.report-main-tab').show();
        }
        if ($_this(this).attr('href') == '#twilioseting') {
            $_this('.report-compliance-tab').show();
        }
    });
    $_this('body').on('click', '.cancel_lead', function (e) {

        var lead_id = $_this(this).data('lid');
        var ref_id = $_this(this).data('refid');
        if (lead_id == "" && ref_id == "") {
            e.preventDefault();
            return false;
        } else {
            $_this('#lead_to_cancel').val(lead_id);
            $_this('#ref_id_digit').html(ref_id);
        }
    });

    // if ($_this('#cloneleadajax').length > 0) {
    //     $_this("#cloneleadajax").autocomplete({
    //         source: function (request, response) {
    //             $_this.ajax({
    //                 url: "/ajax/getleads",
    //                 dataType: "jsonp",
    //                 data: {
    //                     term: request.term
    //                 },
    //                 success: function (data) {
    //                     if (data.length == 0) {
    //                         if ($_this("#clonelead .invalid-feedback").length == 0)
    //                             $_this("#cloneleadajax").after('<span class="invalid-feedback validation-error">  <strong>No record found</strong>     </span>');
    //                     } else {
    //                         $_this("#clonelead .invalid-feedback").remove();
    //                     }
    //                     response(data)
    //                 }
    //             });
    //         },
    //         minLength: 0,
    //         select: function (event, ui) {
    //             // log( "Selected: " + ui.item.value + " aka " + ui.item.id );\
    //             window.location.href = ui.item.url;
    //         }
    //     });
    // }



    $_this('body').on('click', '[name="call_immediately"]', function () {
        if ($_this(this).val() == 'no') {
            $_this('.schedule_call').show();
        } else {
            $_this('.schedule_call').hide();
        }
    });

    /* TPV salesagent lead schedule */
    $_this('body').on('submit', '#schedule_call_form', function (e) {
        e.preventDefault();
        var formobj = this;
        var check_validation = validateformfields('schedule_call_form');
        if (check_validation === false) {
            return false;
        }
        makeemptyresponse('ajax-response', formobj);
        dataInProcess('save-text', formobj);
        $_this.ajax({
            type: "POST",
            url: $_this(this).attr('action'),
            data: $_this(this).serialize(),
            success: function (response) {
                //$_this('save-text',formobj).html('Saved');  
                dataIntProcessed('save-text', formobj);
                var res = response;
                if (res.status == 'success') {
                    var responsemessage = "<div class='alert alert-success'>" + res.message + "</div>";
                    formobj.reset();

                    if (typeof res.url === 'undefined') {

                    }
                    else {
                        setTimeout(function () {
                            location.href = res.url;
                        }, 2000);
                    }

                    setTimeout(function () {
                        $_this('#schedule_call_form').hide();
                    }, 1000);

                } else if (res.status == 'error') {

                    var responsemessage = "<div class='alert alert-danger'>";
                    for (var i = 0; i < res.errors.length; i++) {
                        responsemessage = responsemessage + "<p>" + res.errors[i] + "</p>";
                    }
                    responsemessage = responsemessage + "</div>";
                } else {
                    responsemessage = "<div class='alert alert-danger'>Something went wrong please try again.</div>";
                }
                $_this('.ajax-response', formobj).html(responsemessage);

                hidealert();

            }, error: function (response) {
                json_response_display(response, 'ajax-response', formobj);
                hidealert();
            }
        });

    });

    $_this('body').on('click', '[type="radio"]', function () {
        if (typeof $_this(this).data('copybillingaddress') != "undefined") {
            var parentelement = $_this(this).data('parentelement');
            //  console.log(parentelement);
            if ($_this(this).val() == 'Yes') {

                var zip = $_this('#' + parentelement + ' [data-cname="[ServiceZip]"]').val();
                var city = $_this('#' + parentelement + ' [data-cname="[ServiceCity]"]').val();
                var state = $_this('#' + parentelement + ' [data-cname="[ServiceState]"]').val();
                var address1 = $_this('#' + parentelement + ' [data-cname="[ServiceAddress]"]').val();
                var address2 = $_this('#' + parentelement + ' [data-cname="[ServiceAddress2]"]').val();

                $_this('#' + parentelement + ' [data-cname="[BillingAddress]"]').val(address1);
                $_this('#' + parentelement + ' [data-cname="[BillingAddress2]"]').val(address2);
                $_this('#' + parentelement + ' [data-cname="[BillingZip]"]').val(zip);
                $_this('#' + parentelement + ' [data-cname="[BillingCity]"]').val(city);
                $_this('#' + parentelement + ' [data-cname="[BillingState]"]').val(state);

                var zip = $_this('#' + parentelement + ' [data-cname="[GasServiceZip]"]').val();
                var city = $_this('#' + parentelement + ' [data-cname="[GasServiceCity]"]').val();
                var state = $_this('#' + parentelement + ' [data-cname="[GasServiceState]"]').val();
                var address1 = $_this('#' + parentelement + ' [data-cname="[GasServiceAddress]"]').val();
                var address2 = $_this('#' + parentelement + ' [data-cname="[GasServiceAddress2]"]').val();

                $_this('#' + parentelement + ' [data-cname="[GasBillingAddress]"]').val(address1);
                $_this('#' + parentelement + ' [data-cname="[GasBillingAddress2]"]').val(address2);
                $_this('#' + parentelement + ' [data-cname="[GasBillingZip]"]').val(zip);
                $_this('#' + parentelement + ' [data-cname="[GasBillingCity]"]').val(city);
                $_this('#' + parentelement + ' [data-cname="[GasBillingState]"]').val(state);

                if ($_this('#' + parentelement + ' [data-cname="[ServiceZip]"]').length > 0) {
                    var zip = $_this('#' + parentelement + ' [data-cname="[ServiceZip]"]').val();
                    var city = $_this('#' + parentelement + ' [data-cname="[ServiceCity]"]').val();
                    var state = $_this('#' + parentelement + ' [data-cname="[ServiceState]"]').val();
                    var address1 = $_this('#' + parentelement + ' [data-cname="[ServiceAddress]"]').val();
                    var address2 = $_this('#' + parentelement + ' [data-cname="[ServiceAddress2]"]').val();
                    $_this('#' + parentelement + ' [data-cname="[ElectricBillingZip]"]').val(zip);
                    $_this('#' + parentelement + ' [data-cname="[ElectricBillingCity]"]').val(city);
                    $_this('#' + parentelement + ' [data-cname="[ElectricBillingState]"]').val(state);
                    $_this('#' + parentelement + ' [data-cname="[ElectricBillingAddress]"]').val(address1);
                    $_this('#' + parentelement + ' [data-cname="[ElectricBillingAddress2]"]').val(address2);
                }


            } else {
                $_this('#' + parentelement + ' [data-cname="[BillingAddress]"]').val('');
                $_this('#' + parentelement + ' [data-cname="[BillingAddress2]"]').val('');
                $_this('#' + parentelement + ' [data-cname="[BillingZip]"]').val('');
                $_this('#' + parentelement + ' [data-cname="[BillingCity]"]').val('');
                $_this('#' + parentelement + ' [data-cname="[BillingState]"]').val('');
                $_this('#' + parentelement + ' [data-cname="[ElectricBillingZip]"]').val('');

                $_this('#' + parentelement + ' [data-cname="[ElectricBillingCity]"]').val('');
                $_this('#' + parentelement + ' [data-cname="[ElectricBillingState]"]').val('');
                $_this('#' + parentelement + ' [data-cname="[ElectricBillingAddress]"]').val('');
                $_this('#' + parentelement + ' [data-cname="[ElectricBillingAddress2]"]').val('');

                $_this('#' + parentelement + ' [data-cname="[GasBillingAddress]"]').val('');
                $_this('#' + parentelement + ' [data-cname="[GasBillingAddress2]"]').val('');
                $_this('#' + parentelement + ' [data-cname="[GasBillingZip]"]').val('');
                $_this('#' + parentelement + ' [data-cname="[GasBillingCity]"]').val('');
                $_this('#' + parentelement + ' [data-cname="[GasBillingState]"]').val('');
            }
        }

        if (typeof $_this(this).data('copyauthorizename') != "undefined") {
            var parentelement = $_this(this).data('parentelement');
            updateAuthNameToBillingName(parentelement, $_this(this).val());
        }
        if (typeof $_this(this).data('copygasauthtobillname') != "undefined") {
            var parentelement = $_this(this).data('parentelement');
            updateAuthNameToGasBillingName(parentelement, $_this(this).val());
        }
        if (typeof $_this(this).data('copyelectricauthtobillname') != "undefined") {
            var parentelement = $_this(this).data('parentelement');
            updateAuthNameToElectricBillingName(parentelement, $_this(this).val());
        }
        if (typeof $_this(this).data('copyelectricserviceaddress') != "undefined") {
            var parentelement = $_this(this).data('parentelement');
            copyelectricserviceaddress(parentelement, $_this(this).val());
        }


    });

    function updateAuthNameToBillingName(parentelement, sameOrNot) {

        var AuthorizedFirstName = "";
        var AuthorizedMiddleName = "";
        var AuthorizedLastName = "";
        if (sameOrNot == 'Yes') {
            AuthorizedFirstName = $_this('#' + parentelement + ' [data-cname="[Authorized First name]').val();
            AuthorizedMiddleName = $_this('#' + parentelement + ' [data-cname="[Authorized Middle initial]').val();
            AuthorizedLastName = $_this('#' + parentelement + ' [data-cname="[Authorized Last name]').val();
        }
        $_this('#' + parentelement + ' [data-cname="[Billing first name]"]').val(AuthorizedFirstName);
        $_this('#' + parentelement + ' [data-cname="[Billing middle name]"]').val(AuthorizedMiddleName);
        $_this('#' + parentelement + ' [data-cname="[Billing last name]"]').val(AuthorizedLastName);

    }
    function updateAuthNameToGasBillingName(parentelement, sameOrNot) {

        var AuthorizedFirstName = "";
        var AuthorizedMiddleName = "";
        var AuthorizedLastName = "";
        if (sameOrNot == 'Yes') {
            AuthorizedFirstName = $_this('#' + parentelement + ' [data-cname="[Authorized First name]').val();
            AuthorizedMiddleName = $_this('#' + parentelement + ' [data-cname="[Authorized Middle initial]').val();
            AuthorizedLastName = $_this('#' + parentelement + ' [data-cname="[Authorized Last name]').val();

        }
        $_this('#' + parentelement + ' [data-cname="[Gas Billing first name]"]').val(AuthorizedFirstName);
        $_this('#' + parentelement + ' [data-cname="[Gas Billing middle name]"]').val(AuthorizedMiddleName);
        $_this('#' + parentelement + ' [data-cname="[Gas Billing last name]"]').val(AuthorizedLastName);
    }
    function updateAuthNameToElectricBillingName(parentelement, sameOrNot) {

        var AuthorizedFirstName = "";
        var AuthorizedMiddleName = "";
        var AuthorizedLastName = "";
        if (sameOrNot == 'Yes') {
            AuthorizedFirstName = $_this('#' + parentelement + ' [data-cname="[Authorized First name]').val();
            AuthorizedMiddleName = $_this('#' + parentelement + ' [data-cname="[Authorized Middle initial]').val();
            AuthorizedLastName = $_this('#' + parentelement + ' [data-cname="[Authorized Last name]').val();

        }
        $_this('#' + parentelement + ' [data-cname="[Electric Billing first name]"]').val(AuthorizedFirstName);
        $_this('#' + parentelement + ' [data-cname="[Electric Billing middle name]"]').val(AuthorizedMiddleName);
        $_this('#' + parentelement + ' [data-cname="[Electric Billing last name]"]').val(AuthorizedLastName);
    }
    function copyelectricserviceaddress(parentelement, sameOrNot) {

        var zip = city = state = address1 = address2 = "";
        if (sameOrNot == 'Yes') {
            var zip = $_this('#' + parentelement + ' [data-cname="[GasServiceZip]"]').val();
            var city = $_this('#' + parentelement + ' [data-cname="[GasServiceCity]"]').val();
            var state = $_this('#' + parentelement + ' [data-cname="[GasServiceState]"]').val();
            var address1 = $_this('#' + parentelement + ' [data-cname="[GasServiceAddress]"]').val();
            var address2 = $_this('#' + parentelement + ' [data-cname="[GasServiceAddress2]"]').val();

        }
        $_this('#' + parentelement + ' [data-cname="[ServiceZip]"]').val(zip);
        $_this('#' + parentelement + ' [data-cname="[ServiceCity]"]').val(city);
        $_this('#' + parentelement + ' [data-cname="[ServiceState]"]').val(state);
        $_this('#' + parentelement + ' [data-cname="[ServiceAddress]"]').val(address1);
        $_this('#' + parentelement + ' [data-cname="[ServiceAddress2]"]').val(address2);
    }


    //  $_this( ".zipcodefield" ).autocomplete({
    //   source: function( request, response ) {
    //   $_this.ajax( {
    //       url: "/ajax/getzipcodeslist",
    //       dataType: "jsonp",
    //       data: {
    //         term: request.term
    //       },
    //       success: function( data ) {

    //           response( data )
    //       }
    //     } );
    //   },
    //   minLength: 2,
    //   select: function( event, ui ) {
    //     //   console.log(ui.item);
    //       $_this('.zipcodefield').val(ui.item.zipcode);
    //    // log( "Selected: " + ui.item.value + " aka " + ui.item.id );\

    //    //window.location.href= ui.item.url;
    //   }
    // } );

    $_this('body').on('click', '.add-another-account', function () {
        //alert( $_this('.agent-main-data-wrapper').length );
        var first_form_options = $_this(".agent-main-data-wrapper").html();
        var number_of_elements_appended = $_this(".new_appended_element").length + 1;
        var div_id = "new_appended_" + number_of_elements_appended;
        var remove_option = "<div class='remove-account-wrapper'><a href='javascript:void(0)' class='remove-added-account' data-ref='" + div_id + "'><img src='/images/cancel.png'></a></div>";
        var div_content = "<div class='new_appended_element' id='" + div_id + "'>" + remove_option + first_form_options + "</div>";
        $_this(".addedaccountnumbers").append(div_content);


        $_this('#' + div_id + " [data-cname]").each(function () {
            var cname = $_this(this).data('cname');
            $_this(this).data('parentelement', div_id);
            // console.log( $_this(this) );

            $_this(this).attr('name', "fields[multiple][" + number_of_elements_appended + "]" + cname);

        });
        $_this('#' + div_id + " [data-changeid]").each(function () {
            var random_id = Math.floor(Math.random() * 100000000);
            $_this(this).attr('for', random_id);
            $_this(this).children('input').attr('id', random_id);
        });
        var zipcity = $_this('#zipcodeCity').val();
        var zipcodestate = $_this('#zipcodestate').val();
        var zipcodefield = $_this('.zipcodefield').val();
        $_this('#' + div_id + " .cityall").val(zipcity);
        $_this('#' + div_id + " .stateall").val(zipcodestate);
        $_this('#' + div_id + " .zipcodeall").val(zipcodefield);





        //  $_this('select').select2();

    });
    $_this('body').on('click', '.remove-added-account', function () {
        if (confirm('Are you want to remove this account')) {
            var removeid = $_this(this).data('ref');
            $_this('#' + removeid).remove();
        }
    });



    var lastClick;
    function updatelasactivity() {
        var lastclick_difference = ((new Date()).getTime() - lastClick);

        if (lastclick_difference > 60000 || isNaN(lastclick_difference) === true) {
            $.post('/activityupdate', {}, function (data) { });
            lastClick = (new Date()).getTime();
        }


    }
    $('body').on('keyup', function () {
        updatelasactivity()
    });
    $('body').on('click', function () {
        updatelasactivity()
    });


});





/** print ajax validation errors **/
function printErrorMsg(msg) {
    $(".ajax-error-message").html('');
    var errors = '';
    $.each(msg, function (key, value) {
        errors += '<p>' + value[0] + '</p>';
    });
    $(".ajax-error-message").append('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><p>' + errors + '</p></div>');

}

/** print ajax validation errors **/
function printErrorMsgNew(form, msg) {
    $(".help-block").remove('');
    var errors = '';
    $.each(msg, function (key, value) {
        if (key == 'twilio_workflows') {
            $("#twilio-workflows-errors").html("<span class='help-block' >" + value[0] + "</span>");
        } else {
            $(form).find("[name='" + key + "'],[name='" + key + "[]']").parent().append("<span class='help-block' >" + value[0] + "</span>");
        }


    });


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
    }, 5000);
}

$.extend(true, $.fn.dataTable.defaults, {
    lengthChange: true,
    searchDelay: 1000,
    language: {
        emptyTable: "No records found",
        zeroRecords: "No records found"
    }

});

$(".toggle-password").click(function () {

    $(this).toggleClass("fa-eye fa-eye-slash");
    var input = $($(this).attr("toggle"));
    if (input.attr("type") == "password") {
        input.attr("type", "text");
    } else {
        input.attr("type", "password");
    }
});


$(document).ready(function () {
    $(".modal").modal({
        show: false,
        backdrop: 'static'
    });
});

/**
 * set sales center drop down option
 * @param input
 * @param clientId
 * @param selected
 */
function setSalesCenterOptions(input,clientId,selected = '')
{
    var inputId = "#"+input;
    $(inputId).html('');
    $(inputId).append('<option value="" selected>Select </option>');
    if (clientId > 0) {
        $.ajax({
            url: getSalesCenterOptionsUrl,
            type: "POST",
            data: {
                client_id: clientId
            },
            success: function(response) {
                if (response.status == 'success') {
                    $(inputId).append(response.options);
                    if (selected != '') {
                        $(inputId).val(selected);
                    }
                }
            },
            error: function(xhr) {
                console.log(xhr);
            }
        });
    }
}

/**
 *  set sales center location drop down option
 * @param input
 * @param clientId
 * @param salescenterId
 * @param selected
 * @param isAllSalesCenter
 * @param defaultOption
 */
function setSalesCenterLocationOptions(input,clientId,salescenterId = '',selected = '', isAllSalesCenter = false, defaultOption='Select', isMultiSelect = false, actionType= '')
{
    var inputId = "#"+input;
    $(inputId).html('');
    if (defaultOption != '') {
        $(inputId).append('<option value="" selected>'+defaultOption+'</option>');  
    }
    
    if (clientId > 0 || isAllSalesCenter) {
        $.ajax({
            url: getSalesCenterLocationOptionsUrl,
            type: "POST",
            data: {
                client_id: clientId,
                salescenter_id: salescenterId
            },
            success: function(response) {
                if (response.status == 'success') {
                    $(inputId).append(response.options);
                    if (selected != '') {
                        $(inputId).val(selected);
                    }
                    if (isMultiSelect) {
                        $(inputId).multiselect('reload');
                        $('.ms-options label').append('<span class="checkmark" style="left:10px;top:10px"></span>');
                        $('.ms-options label').addClass('custom-checkbox').css('cssText','margin-bottom: 0px !important;');
                        if (actionType == 'view') {
                            $('.ms-options').css("visibility", "hidden");
                        }
                    }                    
                }
            },
            error: function(xhr) {
                console.log(xhr);
            }
        });
    }
}


/**
 * set location channel drop down option
 * @param input
 * @param locationId
 * @param selected
 */
function setLocationChannelOptions(input,locationId,selected = '')
{
    var inputId = "#"+input;
    $(inputId).html('');
    $(inputId).append('<option value="" selected>Select </option>');
    if (locationId > 0) {
        $.ajax({
            url: getLocationChannelOptionsUrl,
            data: {
                location_id: locationId
            },
            success: function(response) {
                if (response.status == 'success') {
                    $(inputId).append(response.options);
                    if (selected != '') {
                        $(inputId).val(selected);
                    }
                }
            },
            error: function(xhr) {
                console.log(xhr);
            }
        });
    }
}

$(document).ready(function () {

    // function calcWidth() {
    //     var navwidth = 0;
    //     var morewidth = $('#menu_ul .more').outerWidth(true);
    //     $('#menu_ul > li:not(.more)').each(function () {
    //         navwidth += $(this).outerWidth(true);
    //     });
    //
    //     //var availablespace = $('nav').outerWidth(true) - morewidth;
    //     var availablespace = $('.dashboard-submenu').width() - morewidth;
    //
    //     // console.log("navwidth:" + navwidth);
    //     // console.log("morewidth:" + morewidth);
    //     // console.log("availablespace:" + availablespace);
    //
    //     if (navwidth > availablespace) {
    //         var lastItem = $('#menu_ul > li:not(.more)').last();
    //         lastItem.attr('data-width', lastItem.outerWidth(true));
    //         lastItem.prependTo($('#menu_ul .more ul'));
    //         $(".overflow li a").addClass("test");
    //         calcWidth();
    //     } else {
    //         var firstMoreElement = $('#menu_ul li.more li').first();
    //         if (navwidth + firstMoreElement.data('width') < availablespace) {
    //             firstMoreElement.insertBefore($('#menu_ul .more'));
    //             $(".overflow li a").removeClass("test");
    //         }
    //     }
    //
    //     if ($('.more li').length > 0) {
    //         $('.more').css('display', 'block');
    //     } else {
    //         $('.more').css('display', 'none');
    //     }
    // }

    // $(window).on('resize load', function () {
    //     calcWidth();
    // });

    // //SELECT OPTIONS AND HIDE OPTION AFTER SELECTION
    // $(".drop-down .options ul li a").click(function () {
    //     var text = $(this).html();
    //     $(".drop-down .selected a span").html(text);
    //     $(".drop-down .options ul").hide();
    // }); 


    // $('.more').click(function () {
    //     $('.overflow').slideToggle();
    //     // $(".overflow li a").addClass("test");
    // });

    // $('body').on('click', '.test', function () {
    //     // alert("test new");
    //     selectedSubMenu($(this).data('id'));
    // });

    // function selectedSubMenu(dataId) {
    //     var text = $('.overflow a[data-id='+dataId+']').html();
    //     $(".more a.selected").html(text);
    // }

});



/*---------------------horizontal--tabs---scrolling---js---------*/

// var hidWidth;
// var scrollBarWidths = 40;

// var widthOfList = function(){
//   var itemsWidth = 0;
//   $('.hs-list li').each(function(){
//     var itemWidth = $(this).outerWidth();
//     itemsWidth+=itemWidth;
//   });
//   return itemsWidth;
// };

// var widthOfHidden = function(){
//   return (($('.hs-wrapper').outerWidth())-widthOfList()-getLeftPosi())-scrollBarWidths;
// };

// var getLeftPosi = function(){
//   return $('.hs-list').position().left;
// };

// var reAdjust = function(){
//   if (($('.hs-wrapper').outerWidth()) < widthOfList()) {
//     $('.hs-scroller-right').show();
//   }
//   else {
//     $('.hs-scroller-right').hide();
//   }
  
//   if (getLeftPosi()<0) {
//     $('.hs-scroller-left').show();
//   }
//   else {
//     $('.hs-item').animate({left:"-="+getLeftPosi()+"px"},'slow');
//   	$('.hs-scroller-left').hide();
//   }
// }

// reAdjust();

// $(window).on('resize',function(e){  
//   	reAdjust();
// });

// $('.hs-scroller-right').click(function() {
  
//   $('.hs-scroller-left').fadeIn('slow');
//   $('.hs-scroller-right').fadeOut('slow');
  
//   $('.hs-list').animate({left:"+="+widthOfHidden()+"px"},'slow',function(){

//   });
// });

// $('.hs-scroller-left').click(function() {
  
// 	$('.hs-scroller-right').fadeIn('slow');
// 	$('.hs-scroller-left').fadeOut('slow');
  
//   	$('.hs-list').animate({left:"-="+getLeftPosi()+"px"},'slow',function(){
  	
//   	});
// });    


;(function ($, window) {
    'use strict';
  
    var CONSTANTS = {
      CONTINUOUS_SCROLLING_TIMEOUT_INTERVAL: 50, // timeout interval for repeatedly moving the tabs container
                                                  // by one increment while the mouse is held down--decrease to
                                                  // make mousedown continous scrolling faster
      SCROLL_OFFSET_FRACTION: 6, // each click moves the container this fraction of the fixed container--decrease
                                 // to make the tabs scroll farther per click
    
      DATA_KEY_DDMENU_MODIFIED: 'scrtabsddmenumodified',
      DATA_KEY_IS_MOUSEDOWN: 'scrtabsismousedown',
    
      CSS_CLASSES: {
        ALLOW_SCROLLBAR: 'scrtabs-allow-scrollbar',
        SCROLL_ARROW_DISABLE: 'scrtabs-disable'
      },
    
      SLIDE_DIRECTION: {
        LEFT: 1,
        RIGHT: 2
      },
    
      EVENTS: {
        CLICK: 'click.scrtabs',
        DROPDOWN_MENU_HIDE: 'hide.bs.dropdown.scrtabs',
        DROPDOWN_MENU_SHOW: 'show.bs.dropdown.scrtabs',
        FORCE_REFRESH: 'forcerefresh.scrtabs',
        MOUSEDOWN: 'mousedown.scrtabs touchstart.scrtabs',
        MOUSEUP: 'mouseup.scrtabs touchend.scrtabs',
        WINDOW_RESIZE: 'resize.scrtabs',
        TABS_READY: 'ready.scrtabs'
      }
    };
    
    // smartresize from Paul Irish (debounced window resize)
    (function (sr) {
      var debounce = function (func, threshold, execAsap) {
        var timeout;
    
        return function debounced() {
          var obj = this, args = arguments;
          function delayed() {
            if (!execAsap) {
              func.apply(obj, args);
            }
            timeout = null;
          }
    
          if (timeout) {
            clearTimeout(timeout);
          } else if (execAsap) {
            func.apply(obj, args);
          }
    
          timeout = setTimeout(delayed, threshold || 100);
        };
      };
      $.fn[sr] = function (fn) { return fn ? this.bind(CONSTANTS.EVENTS.WINDOW_RESIZE, debounce(fn)) : this.trigger(sr); };
    
    })('smartresize');
    
    /* ***********************************************************************************
     * ElementsHandler - Class that each instance of ScrollingTabsControl will instantiate
     * **********************************************************************************/
    function ElementsHandler(scrollingTabsControl) {
      var ehd = this;
    
      ehd.stc = scrollingTabsControl;
    }
    
    // ElementsHandler prototype methods
    (function (p) {
        p.initElements = function (options) {
          var ehd = this;
    
          ehd.setElementReferences();
          ehd.setEventListeners();
        };
    
        p.refreshAllElementSizes = function () {
          var ehd = this,
              stc = ehd.stc,
              smv = stc.scrollMovement,
              scrollArrowsWereVisible = stc.scrollArrowsVisible,
              actionsTaken = {
                didScrollToActiveTab: false
              },
              isPerformingSlideAnim = false,
              minPos;
    
          ehd.setElementWidths();
          ehd.setScrollArrowVisibility();
    
          // this could have been a window resize or the removal of a
          // dynamic tab, so make sure the movable container is positioned
          // correctly because, if it is far to the left and we increased the
          // window width, it's possible that the tabs will be too far left,
          // beyond the min pos.
          if (stc.scrollArrowsVisible) {
            // make sure container not too far left
            minPos = smv.getMinPos();
    
            isPerformingSlideAnim = smv.scrollToActiveTab({
              isOnWindowResize: true
            });
    
            if (!isPerformingSlideAnim) {
              smv.refreshScrollArrowsDisabledState();
    
              if (stc.movableContainerLeftPos < minPos) {
                smv.incrementMovableContainerRight(minPos);
              }
            }
    
            actionsTaken.didScrollToActiveTab = true;
    
          } else if (scrollArrowsWereVisible) {
            // scroll arrows went away after resize, so position movable container at 0
            stc.movableContainerLeftPos = 0;
            smv.slideMovableContainerToLeftPos();
          }
    
          return actionsTaken;
        };
    
        p.setElementReferences = function () {
          var ehd = this,
              stc = ehd.stc,
              $tabsContainer = stc.$tabsContainer,
              $leftArrow,
              $rightArrow;
    
          stc.isNavPills = false;
    
          stc.$fixedContainer = $tabsContainer.find('.scrtabs-tabs-fixed-container');
          $leftArrow = stc.$fixedContainer.prev();
          $rightArrow = stc.$fixedContainer.next();
    
          stc.$movableContainer = $tabsContainer.find('.scrtabs-tabs-movable-container');
          stc.$tabsUl = $tabsContainer.find('.nav-tabs');
    
          // check for pills
          if (!stc.$tabsUl.length) {
            stc.$tabsUl = $tabsContainer.find('.nav-pills');
    
            if (stc.$tabsUl.length) {
              stc.isNavPills = true;
            }
          }
    
          stc.$tabsLiCollection = stc.$tabsUl.find('> li');
    
          stc.$slideLeftArrow = stc.reverseScroll ? $leftArrow : $rightArrow;
          stc.$slideRightArrow = stc.reverseScroll ? $rightArrow : $leftArrow;
          stc.$scrollArrows = stc.$slideLeftArrow.add(stc.$slideRightArrow);
    
          stc.$win = $(window);
        };
    
        p.setElementWidths = function () {
          var ehd = this,
              stc = ehd.stc;
    
          stc.winWidth = stc.$win.width();
          stc.scrollArrowsCombinedWidth = stc.$slideLeftArrow.outerWidth() + stc.$slideRightArrow.outerWidth();
    
          ehd.setFixedContainerWidth();
          ehd.setMovableContainerWidth();
        };
    
        p.setEventListeners = function () {
          var ehd = this,
              stc = ehd.stc,
              evh = stc.eventHandlers,
              ev = CONSTANTS.EVENTS;
    
          stc.$slideLeftArrow
            .off('.scrtabs')
            .on(ev.MOUSEDOWN, function (e) { evh.handleMousedownOnSlideMovContainerLeftArrow.call(evh, e); })
            .on(ev.MOUSEUP, function (e) { evh.handleMouseupOnSlideMovContainerLeftArrow.call(evh, e); })
            .on(ev.CLICK, function (e) { evh.handleClickOnSlideMovContainerLeftArrow.call(evh, e); });
    
          stc.$slideRightArrow
            .off('.scrtabs')
            .on(ev.MOUSEDOWN, function (e) { evh.handleMousedownOnSlideMovContainerRightArrow.call(evh, e); })
            .on(ev.MOUSEUP, function (e) { evh.handleMouseupOnSlideMovContainerRightArrow.call(evh, e); })
            .on(ev.CLICK, function (e) { evh.handleClickOnSlideMovContainerRightArrow.call(evh, e); });
    
          if (stc.tabClickHandler) {
            stc.$tabsLiCollection
              .find('a[data-toggle="tab"]')
              .off(ev.CLICK)
              .on(ev.CLICK, stc.tabClickHandler);
          }
    
          stc.$win.off('.scrtabs').smartresize(function (e) { evh.handleWindowResize.call(evh, e); });
    
          $('body').on(CONSTANTS.EVENTS.FORCE_REFRESH, stc.elementsHandler.refreshAllElementSizes.bind(stc.elementsHandler));
        };
    
        p.setFixedContainerWidth = function () {
          var ehd = this,
              stc = ehd.stc,
              tabsContainerRect = stc.$tabsContainer.get(0).getBoundingClientRect();
          /**
           * @author poletaew
           * It solves problem with rounding by jQuery.outerWidth
           * If we have real width 100.5 px, jQuery.outerWidth returns us 101 px and we get layout's fail
           */
          stc.fixedContainerWidth = tabsContainerRect.width || (tabsContainerRect.right - tabsContainerRect.left);
          stc.fixedContainerWidth = stc.fixedContainerWidth * stc.widthMultiplier;
    
          stc.$fixedContainer.width(stc.fixedContainerWidth);
        };
    
        p.setFixedContainerWidthForHiddenScrollArrows = function () {
          var ehd = this,
              stc = ehd.stc;
    
          stc.$fixedContainer.width(stc.fixedContainerWidth);
        };
    
        p.setFixedContainerWidthForVisibleScrollArrows = function () {
          var ehd = this,
              stc = ehd.stc;
    
          stc.$fixedContainer.width(stc.fixedContainerWidth - stc.scrollArrowsCombinedWidth);
        };
    
        p.setMovableContainerWidth = function () {
          var ehd = this,
              stc = ehd.stc,
              $tabLi = stc.$tabsUl.find('> li');
    
          stc.movableContainerWidth = 0;
    
          if ($tabLi.length) {
    
            $tabLi.each(function () {
              var $li = $(this),
                  totalMargin = 0;
    
              if (stc.isNavPills) { // pills have a margin-left, tabs have no margin
                totalMargin = parseInt($li.css('margin-left'), 10) + parseInt($li.css('margin-right'), 10);
              }
    
              stc.movableContainerWidth += ($li.outerWidth() + totalMargin);
            });
    
            stc.movableContainerWidth += 1;
    
            // if the tabs don't span the width of the page, force the
            // movable container width to full page width so the bottom
            // border spans the page width instead of just spanning the
            // width of the tabs
            if (stc.movableContainerWidth < stc.fixedContainerWidth) {
              stc.movableContainerWidth = stc.fixedContainerWidth;
            }
          }
    
          stc.$movableContainer.width(stc.movableContainerWidth);
        };
    
        p.setScrollArrowVisibility = function () {
          var ehd = this,
              stc = ehd.stc,
              shouldBeVisible = stc.movableContainerWidth > stc.fixedContainerWidth;
    
          if (shouldBeVisible && !stc.scrollArrowsVisible) {
            stc.$scrollArrows.show();
            stc.scrollArrowsVisible = true;
          } else if (!shouldBeVisible && stc.scrollArrowsVisible) {
            stc.$scrollArrows.hide();
            stc.scrollArrowsVisible = false;
          }
    
          if (stc.scrollArrowsVisible) {
            ehd.setFixedContainerWidthForVisibleScrollArrows();
          } else {
            ehd.setFixedContainerWidthForHiddenScrollArrows();
          }
        };
    
    }(ElementsHandler.prototype));
    
    /* ***********************************************************************************
     * EventHandlers - Class that each instance of ScrollingTabsControl will instantiate
     * **********************************************************************************/
    function EventHandlers(scrollingTabsControl) {
      var evh = this;
    
      evh.stc = scrollingTabsControl;
    }
    
    // prototype methods
    (function (p){
      p.handleClickOnSlideMovContainerLeftArrow = function (e) {
        var evh = this,
            stc = evh.stc;
    
        stc.scrollMovement.incrementMovableContainerLeft();
      };
    
      p.handleClickOnSlideMovContainerRightArrow = function (e) {
        var evh = this,
            stc = evh.stc;
    
        stc.scrollMovement.incrementMovableContainerRight();
      };
    
      p.handleMousedownOnSlideMovContainerLeftArrow = function (e) {
        var evh = this,
            stc = evh.stc;
    
        stc.$slideLeftArrow.data(CONSTANTS.DATA_KEY_IS_MOUSEDOWN, true);
        stc.scrollMovement.continueSlideMovableContainerLeft();
      };
    
      p.handleMousedownOnSlideMovContainerRightArrow = function (e) {
        var evh = this,
            stc = evh.stc;
    
        stc.$slideRightArrow.data(CONSTANTS.DATA_KEY_IS_MOUSEDOWN, true);
        stc.scrollMovement.continueSlideMovableContainerRight();
      };
    
      p.handleMouseupOnSlideMovContainerLeftArrow = function (e) {
        var evh = this,
            stc = evh.stc;
    
        stc.$slideLeftArrow.data(CONSTANTS.DATA_KEY_IS_MOUSEDOWN, false);
      };
    
      p.handleMouseupOnSlideMovContainerRightArrow = function (e) {
        var evh = this,
            stc = evh.stc;
    
        stc.$slideRightArrow.data(CONSTANTS.DATA_KEY_IS_MOUSEDOWN, false);
      };
    
      p.handleWindowResize = function (e) {
        var evh = this,
            stc = evh.stc,
            newWinWidth = stc.$win.width();
    
        if (newWinWidth === stc.winWidth) {
          return false;
        }
    
        stc.winWidth = newWinWidth;
        stc.elementsHandler.refreshAllElementSizes();
      };
    
    }(EventHandlers.prototype));
    
    /* ***********************************************************************************
     * ScrollMovement - Class that each instance of ScrollingTabsControl will instantiate
     * **********************************************************************************/
    function ScrollMovement(scrollingTabsControl) {
      var smv = this;
    
      smv.stc = scrollingTabsControl;
    }
    
    // prototype methods
    (function (p) {
    
      p.continueSlideMovableContainerLeft = function () {
        var smv = this,
            stc = smv.stc;
    
        setTimeout(function() {
          if (stc.movableContainerLeftPos <= smv.getMinPos()  ||
              !stc.$slideLeftArrow.data(CONSTANTS.DATA_KEY_IS_MOUSEDOWN)) {
            return;
          }
    
          if (!smv.incrementMovableContainerLeft()) { // haven't reached max left
            smv.continueSlideMovableContainerLeft();
          }
        }, CONSTANTS.CONTINUOUS_SCROLLING_TIMEOUT_INTERVAL);
      };
    
      p.continueSlideMovableContainerRight = function () {
        var smv = this,
            stc = smv.stc;
    
        setTimeout(function() {
          if (stc.movableContainerLeftPos >= 0  ||
              !stc.$slideRightArrow.data(CONSTANTS.DATA_KEY_IS_MOUSEDOWN)) {
            return;
          }
    
          if (!smv.incrementMovableContainerRight()) { // haven't reached max right
            smv.continueSlideMovableContainerRight();
          }
        }, CONSTANTS.CONTINUOUS_SCROLLING_TIMEOUT_INTERVAL);
      };
    
      p.decrementMovableContainerLeftPos = function (minPos) {
        var smv = this,
            stc = smv.stc;
    
        stc.movableContainerLeftPos -= (stc.fixedContainerWidth / CONSTANTS.SCROLL_OFFSET_FRACTION);
        if (stc.movableContainerLeftPos < minPos) {
          stc.movableContainerLeftPos = minPos;
        } else if (stc.scrollToTabEdge) {
          smv.setMovableContainerLeftPosToTabEdge(CONSTANTS.SLIDE_DIRECTION.LEFT);
    
          if (stc.movableContainerLeftPos < minPos) {
            stc.movableContainerLeftPos = minPos;
          }
        }
      };
    
      p.disableSlideLeftArrow = function () {
        var smv = this,
            stc = smv.stc;
    
        if (!stc.disableScrollArrowsOnFullyScrolled || !stc.scrollArrowsVisible) {
          return;
        }
    
        stc.$slideLeftArrow.addClass(CONSTANTS.CSS_CLASSES.SCROLL_ARROW_DISABLE);
      };
    
      p.disableSlideRightArrow = function () {
        var smv = this,
            stc = smv.stc;
    
        if (!stc.disableScrollArrowsOnFullyScrolled || !stc.scrollArrowsVisible) {
          return;
        }
    
        stc.$slideRightArrow.addClass(CONSTANTS.CSS_CLASSES.SCROLL_ARROW_DISABLE);
      };
    
      p.enableSlideLeftArrow = function () {
        var smv = this,
            stc = smv.stc;
    
        if (!stc.disableScrollArrowsOnFullyScrolled || !stc.scrollArrowsVisible) {
          return;
        }
    
        stc.$slideLeftArrow.removeClass(CONSTANTS.CSS_CLASSES.SCROLL_ARROW_DISABLE);
      };
    
      p.enableSlideRightArrow = function () {
        var smv = this,
            stc = smv.stc;
    
        if (!stc.disableScrollArrowsOnFullyScrolled || !stc.scrollArrowsVisible) {
          return;
        }
    
        stc.$slideRightArrow.removeClass(CONSTANTS.CSS_CLASSES.SCROLL_ARROW_DISABLE);
      };
    
      p.getMinPos = function () {
        var smv = this,
            stc = smv.stc;
    
        return stc.scrollArrowsVisible ? (stc.fixedContainerWidth - stc.movableContainerWidth - stc.scrollArrowsCombinedWidth) : 0;
      };
    
      p.getMovableContainerCssLeftVal = function () {
        var smv = this,
            stc = smv.stc;
    
        return (stc.movableContainerLeftPos === 0) ? '0' : stc.movableContainerLeftPos + 'px';
      };
    
      p.incrementMovableContainerLeft = function () {
        var smv = this,
            stc = smv.stc,
            minPos = smv.getMinPos();
    
        smv.decrementMovableContainerLeftPos(minPos);
        smv.slideMovableContainerToLeftPos();
        smv.enableSlideRightArrow();
    
        // return true if we're fully left, false otherwise
        return (stc.movableContainerLeftPos === minPos);
      };
    
      p.incrementMovableContainerRight = function (minPos) {
        var smv = this,
            stc = smv.stc;
    
        // if minPos passed in, the movable container was beyond the minPos
        if (minPos) {
          stc.movableContainerLeftPos = minPos;
        } else {
          stc.movableContainerLeftPos += (stc.fixedContainerWidth / CONSTANTS.SCROLL_OFFSET_FRACTION);
    
          if (stc.movableContainerLeftPos > 0) {
            stc.movableContainerLeftPos = 0;
          } else if (stc.scrollToTabEdge) {
            smv.setMovableContainerLeftPosToTabEdge(CONSTANTS.SLIDE_DIRECTION.RIGHT);
          }
        }
    
        smv.slideMovableContainerToLeftPos();
        smv.enableSlideLeftArrow();
    
        // return true if we're fully right, false otherwise
        // left pos of 0 is the movable container's max position (farthest right)
        return (stc.movableContainerLeftPos === 0);
      };
    
      p.refreshScrollArrowsDisabledState = function() {
        var smv = this,
            stc = smv.stc;
    
        if (!stc.disableScrollArrowsOnFullyScrolled || !stc.scrollArrowsVisible) {
          return;
        }
    
        if (stc.movableContainerLeftPos >= 0) { // movable container fully right
          smv.disableSlideRightArrow();
          smv.enableSlideLeftArrow();
          return;
        }
    
        if (stc.movableContainerLeftPos <= smv.getMinPos()) { // fully left
          smv.disableSlideLeftArrow();
          smv.enableSlideRightArrow();
          return;
        }
    
        smv.enableSlideLeftArrow();
        smv.enableSlideRightArrow();
      };
    
      p.scrollToActiveTab = function (options) {
        var smv = this,
            stc = smv.stc,
            RIGHT_OFFSET_BUFFER = 20,
            $activeTab,
            activeTabLeftPos,
            activeTabRightPos,
            rightArrowLeftPos,
            leftScrollArrowWidth,
            rightScrollArrowWidth;
    
        if (!stc.scrollArrowsVisible) {
          return;
        }
    
        $activeTab = stc.$tabsUl.find('li.active');
    
        if (!$activeTab.length) {
          return;
        }
    
        /**
         * @author poletaew
         * We need relative offset (depends on $fixedContainer), don't absolute
         */
        activeTabLeftPos = $activeTab.offset().left - stc.$fixedContainer.offset().left;
        activeTabRightPos = activeTabLeftPos + $activeTab.outerWidth();
    
        rightArrowLeftPos = stc.fixedContainerWidth - RIGHT_OFFSET_BUFFER;
    
        if (activeTabRightPos > rightArrowLeftPos) { // active tab off right side
          rightScrollArrowWidth = stc.$slideRightArrow.outerWidth();
          stc.movableContainerLeftPos -= (activeTabRightPos - rightArrowLeftPos + rightScrollArrowWidth);
          smv.slideMovableContainerToLeftPos();
          return true;
        } else {
          leftScrollArrowWidth = stc.$slideLeftArrow.outerWidth();      
          if (activeTabLeftPos < leftScrollArrowWidth) { // active tab off left side
            stc.movableContainerLeftPos += leftScrollArrowWidth - activeTabLeftPos;
            smv.slideMovableContainerToLeftPos();
            return true;
          }
        }
    
        return false;
      };
    
      p.setMovableContainerLeftPosToTabEdge = function (slideDirection) {
        var smv = this,
            stc = smv.stc,
            offscreenWidth = -stc.movableContainerLeftPos,
            totalTabWidth = 0;
    
          // make sure LeftPos is set so that a tab edge will be against the
          // left scroll arrow so we won't have a partial, cut-off tab
          stc.$tabsLiCollection.each(function (index) {
            var tabWidth = $(this).width();
    
            totalTabWidth += tabWidth;
    
            if (totalTabWidth > offscreenWidth) {
              stc.movableContainerLeftPos = (slideDirection === CONSTANTS.SLIDE_DIRECTION.RIGHT) ? -(totalTabWidth - tabWidth) : -totalTabWidth;
              return false; // exit .each() loop
            }
    
          });
      };
    
      p.slideMovableContainerToLeftPos = function () {
        var smv = this,
            stc = smv.stc,
            minPos = smv.getMinPos(),
            leftVal;
    
        if (stc.movableContainerLeftPos > 0) {
          stc.movableContainerLeftPos = 0;
        } else if (stc.movableContainerLeftPos < minPos) {
          stc.movableContainerLeftPos = minPos;
        }
    
        stc.movableContainerLeftPos = stc.movableContainerLeftPos / 1;
        leftVal = smv.getMovableContainerCssLeftVal();
    
        smv.performingSlideAnim = true;
    
        stc.$movableContainer.stop().animate({ left: leftVal }, 'slow', function __slideAnimComplete() {
          var newMinPos = smv.getMinPos();
    
          smv.performingSlideAnim = false;
    
          // if we slid past the min pos--which can happen if you resize the window
          // quickly--move back into position
          if (stc.movableContainerLeftPos < newMinPos) {
            smv.decrementMovableContainerLeftPos(newMinPos);
            stc.$movableContainer.stop().animate({ left: smv.getMovableContainerCssLeftVal() }, 'fast', function() {
              smv.refreshScrollArrowsDisabledState();
            });
          } else {
            smv.refreshScrollArrowsDisabledState();
          }
        });
      };
    
    }(ScrollMovement.prototype));
    
    /* **********************************************************************
     * ScrollingTabsControl - Class that each directive will instantiate
     * **********************************************************************/
    function ScrollingTabsControl($tabsContainer) {
      var stc = this;
    
      stc.$tabsContainer = $tabsContainer;
    
      stc.movableContainerLeftPos = 0;
      stc.scrollArrowsVisible = false;
      stc.scrollToTabEdge = false;
      stc.disableScrollArrowsOnFullyScrolled = false;
      stc.reverseScroll = false;
      stc.widthMultiplier = 1;
    
      stc.scrollMovement = new ScrollMovement(stc);
      stc.eventHandlers = new EventHandlers(stc);
      stc.elementsHandler = new ElementsHandler(stc);
    }
    
    // prototype methods
    (function (p) {
      p.initTabs = function (options, $scroller, readyCallback, attachTabContentToDomCallback) {
        var stc = this,
            elementsHandler = stc.elementsHandler,
            num;
    
        if (options.scrollToTabEdge) {
          stc.scrollToTabEdge = true;
        }
    
        if (options.disableScrollArrowsOnFullyScrolled) {
          stc.disableScrollArrowsOnFullyScrolled = true;
        }
    
        if (options.reverseScroll) {
          stc.reverseScroll = true;
        }
    
        if (options.widthMultiplier !== 1) {
          num = Number(options.widthMultiplier); // handle string value
    
          if (!isNaN(num)) {
            stc.widthMultiplier = num;
          }
        }
    
        setTimeout(initTabsAfterTimeout, 100);
    
        function initTabsAfterTimeout() {
          var actionsTaken;
    
          // if we're just wrapping non-data-driven tabs, the user might
          // have the .nav-tabs hidden to prevent the clunky flash of
          // multi-line tabs on page refresh, so we need to make sure
          // they're visible before trying to wrap them
          $scroller.find('.nav-tabs').show();
    
          elementsHandler.initElements(options);
          actionsTaken = elementsHandler.refreshAllElementSizes();
    
          $scroller.css('visibility', 'visible');
    
          if (attachTabContentToDomCallback) {
            attachTabContentToDomCallback();
          }
    
          if (readyCallback) {
            readyCallback();
          }
        }
      };
    
      p.scrollToActiveTab = function(options) {
        var stc = this,
            smv = stc.scrollMovement;
    
        smv.scrollToActiveTab(options);
      };
    }(ScrollingTabsControl.prototype));
    
  
    var tabElements = (function () {
    
      return {
        getElTabPaneForLi: getElTabPaneForLi,
        getNewElNavTabs: getNewElNavTabs,
        getNewElScrollerElementWrappingNavTabsInstance: getNewElScrollerElementWrappingNavTabsInstance,
        getNewElTabAnchor: getNewElTabAnchor,
        getNewElTabContent: getNewElTabContent,
        getNewElTabLi: getNewElTabLi,
        getNewElTabPane: getNewElTabPane
      };
    
      ///////////////////
    
      // ---- retrieve existing elements from the DOM ----------
      function getElTabPaneForLi($li) {
        return $($li.find('a').attr('href'));
      }
    
    
      // ---- create new elements ----------
      function getNewElNavTabs() {
        return $('<ul class="nav nav-tabs" role="tablist"></ul>');
      }
    
      function getNewElScrollerElementWrappingNavTabsInstance($navTabsInstance, settings) {
        var $tabsContainer = $('<div class="scrtabs-tab-container"></div>'),
            leftArrowContent = settings.leftArrowContent || '<div class="scrtabs-tab-scroll-arrow scrtabs-tab-scroll-arrow-left"><span class="' + settings.cssClassLeftArrow + '"></span></div>',
            $leftArrow = $(leftArrowContent),
            rightArrowContent = settings.rightArrowContent || '<div class="scrtabs-tab-scroll-arrow scrtabs-tab-scroll-arrow-right"><span class="' + settings.cssClassRightArrow + '"></span></div>',
            $rightArrow = $(rightArrowContent),
            $fixedContainer = $('<div class="scrtabs-tabs-fixed-container"></div>'),
            $movableContainer = $('<div class="scrtabs-tabs-movable-container"></div>');
    
        if (settings.disableScrollArrowsOnFullyScrolled) {
          $leftArrow.add($rightArrow).addClass('scrtabs-disable');
        }
    
        return $tabsContainer
                  .append($leftArrow,
                          $fixedContainer.append($movableContainer.append($navTabsInstance)),
                          $rightArrow);
      }
    
      function getNewElTabAnchor(tab, propNames) {
        return $('<a role="tab" data-toggle="tab"></a>')
                .attr('href', '#' + tab[propNames.paneId])
                .html(tab[propNames.title]);
      }
    
      function getNewElTabContent() {
        return $('<div class="tab-content"></div>');
      }
    
      function getNewElTabLi(tab, propNames, forceActiveTab) {
        var $li = $('<li role="presentation" class=""></li>'),
            $a = getNewElTabAnchor(tab, propNames).appendTo($li);
    
        if (tab[propNames.disabled]) {
          $li.addClass('disabled');
          $a.attr('data-toggle', '');
        } else if (forceActiveTab && tab[propNames.active]) {
          $li.addClass('active');
        }
    
        return $li;
      }
    
      function getNewElTabPane(tab, propNames, forceActiveTab) {
        var $pane = $('<div role="tabpanel" class="tab-pane"></div>')
                    .attr('id', tab[propNames.paneId])
                    .html(tab[propNames.content]);
    
        if (forceActiveTab && tab[propNames.active]) {
          $pane.addClass('active');
        }
    
        return $pane;
      }
    
    
    }()); // tabElements
    
    var tabUtils = (function () {
    
      return {
        didTabOrderChange: didTabOrderChange,
        getIndexOfClosestEnabledTab: getIndexOfClosestEnabledTab,
        getTabIndexByPaneId: getTabIndexByPaneId,
        storeDataOnLiEl: storeDataOnLiEl
      };
    
      ///////////////////
    
      function didTabOrderChange($currTabLis, updatedTabs, propNames) {
        var isTabOrderChanged = false;
    
        $currTabLis.each(function (currDomIdx) {
          var newIdx = getTabIndexByPaneId(updatedTabs, propNames.paneId, $(this).data('tab')[propNames.paneId]);
    
          if ((newIdx > -1) && (newIdx !== currDomIdx)) { // tab moved
            isTabOrderChanged = true;
            return false; // exit .each() loop
          }
        });
    
        return isTabOrderChanged;
      }
    
      function getIndexOfClosestEnabledTab($currTabLis, startIndex) {
        var lastIndex = $currTabLis.length - 1,
            closestIdx = -1,
            incrementFromStartIndex = 0,
            testIdx = 0;
    
        // expand out from the current tab looking for an enabled tab;
        // we prefer the tab after us over the tab before
        while ((closestIdx === -1) && (testIdx >= 0)) {
    
          if ( (((testIdx = startIndex + (++incrementFromStartIndex)) <= lastIndex) &&
                !$currTabLis.eq(testIdx).hasClass('disabled')) ||
                (((testIdx = startIndex - incrementFromStartIndex) >= 0) &&
                 !$currTabLis.eq(testIdx).hasClass('disabled')) ) {
    
            closestIdx = testIdx;
    
          }
        }
    
        return closestIdx;
      }
    
      function getTabIndexByPaneId(tabs, paneIdPropName, paneId) {
        var idx = -1;
    
        tabs.some(function (tab, i) {
          if (tab[paneIdPropName] === paneId) {
            idx = i;
            return true; // exit loop
          }
        });
    
        return idx;
      }
    
      function storeDataOnLiEl($li, tabs, index) {
        $li.data({
          tab: $.extend({}, tabs[index]), // store a clone so we can check for changes
          index: index
        });
      }
    
    }()); // tabUtils
    
    function buildNavTabsAndTabContentForTargetElementInstance($targetElInstance, settings, readyCallback) {
      var tabs = settings.tabs,
          propNames = {
            paneId: settings.propPaneId,
            title: settings.propTitle,
            active: settings.propActive,
            disabled: settings.propDisabled,
            content: settings.propContent
          },
          ignoreTabPanes = settings.ignoreTabPanes,
          hasTabContent = tabs.length && tabs[0][propNames.content] !== undefined,
          $navTabs = tabElements.getNewElNavTabs(),
          $tabContent = tabElements.getNewElTabContent(),
          $scroller,
          attachTabContentToDomCallback = ignoreTabPanes ? null : function() {
            $scroller.after($tabContent);
          };
    
      if (!tabs.length) {
        return;
      }
    
      tabs.forEach(function(tab, index) {
        tabElements
          .getNewElTabLi(tab, propNames, true) // true -> forceActiveTab
          .appendTo($navTabs);
    
        // build the tab panes if we weren't told to ignore them and there's
        // tab content data available
        if (!ignoreTabPanes && hasTabContent) {
          tabElements
            .getNewElTabPane(tab, propNames, true) // true -> forceActiveTab
            .appendTo($tabContent);
        }
      });
    
      $scroller = wrapNavTabsInstanceInScroller($navTabs,
                                                settings,
                                                readyCallback,
                                                attachTabContentToDomCallback);
    
      $scroller.appendTo($targetElInstance);
    
      $targetElInstance.data({
        scrtabs: {
          tabs: tabs,
          propNames: propNames,
          ignoreTabPanes: ignoreTabPanes,
          hasTabContent: hasTabContent,
          scroller: $scroller
        }
      });
    
      // once the nav-tabs are wrapped in the scroller, attach each tab's
      // data to it for reference later; we need to wait till they're
      // wrapped in the scroller because we wrap a *clone* of the nav-tabs
      // we built above, not the original nav-tabs
      $scroller.find('.nav-tabs > li').each(function (index) {
        tabUtils.storeDataOnLiEl($(this), tabs, index);
      });
    
      return $targetElInstance;
    }
    
    
    function wrapNavTabsInstanceInScroller($navTabsInstance, settings, readyCallback, attachTabContentToDomCallback) {
      var $scroller = tabElements.getNewElScrollerElementWrappingNavTabsInstance($navTabsInstance.clone(true), settings), // use clone because we replaceWith later
          scrollingTabsControl = new ScrollingTabsControl($scroller),
          navTabsInstanceData = $navTabsInstance.data('scrtabs');
    
      if (!navTabsInstanceData) {
        $navTabsInstance.data('scrtabs', {
          scroller: $scroller
        });
      } else {
        navTabsInstanceData.scroller = $scroller;
      }
    
      $navTabsInstance.replaceWith($scroller.css('visibility', 'hidden'));
    
      if (settings.tabClickHandler && (typeof settings.tabClickHandler === 'function')) {
        $scroller.hasTabClickHandler = true;
        scrollingTabsControl.tabClickHandler = settings.tabClickHandler;
      }
    
      $scroller.initTabs = function () {
        scrollingTabsControl.initTabs(settings,
                                      $scroller,
                                      readyCallback,
                                      attachTabContentToDomCallback);
      };
    
      $scroller.scrollToActiveTab = function() {
        scrollingTabsControl.scrollToActiveTab(settings);
      };
    
      $scroller.initTabs();
    
      listenForDropdownMenuTabs($scroller, scrollingTabsControl);
    
      return $scroller;
    }
    
    function checkForTabAdded(refreshData) {
      var updatedTabsArray = refreshData.updatedTabsArray,
          propNames = refreshData.propNames,
          ignoreTabPanes = refreshData.ignoreTabPanes,
          options = refreshData.options,
          $currTabLis = refreshData.$currTabLis,
          $navTabs = refreshData.$navTabs,
          $currTabContentPanesContainer = ignoreTabPanes ? null : refreshData.$currTabContentPanesContainer,
          $currTabContentPanes = ignoreTabPanes ? null : refreshData.$currTabContentPanes,
          isInitTabsRequired = false;
    
      // make sure each tab in the updated tabs array has a corresponding DOM element
      updatedTabsArray.forEach(function (tab, idx) {
        var $li = $currTabLis.find('a[href="#' + tab[propNames.paneId] + '"]'),
            isTabIdxPastCurrTabs = (idx >= $currTabLis.length),
            $pane;
    
        if (!$li.length) { // new tab
          isInitTabsRequired = true;
    
          // add the tab, add its pane (if necessary), and refresh the scroller
          $li = tabElements.getNewElTabLi(tab, propNames, options.forceActiveTab);
          tabUtils.storeDataOnLiEl($li, updatedTabsArray, idx);
    
          if (isTabIdxPastCurrTabs) { // append to end of current tabs
            $li.appendTo($navTabs);
          } else {                        // insert in middle of current tabs
            $li.insertBefore($currTabLis.eq(idx));
          }
    
          if (!ignoreTabPanes && tab[propNames.content] !== undefined) {
            $pane = tabElements.getNewElTabPane(tab, propNames, options.forceActiveTab);
            if (isTabIdxPastCurrTabs) { // append to end of current tabs
              $pane.appendTo($currTabContentPanesContainer);
            } else {                        // insert in middle of current tabs
              $pane.insertBefore($currTabContentPanes.eq(idx));
            }
          }
    
        }
    
      });
    
      return isInitTabsRequired;
    }
    
    function checkForTabPropertiesUpdated(refreshData) {
      var tabLiData = refreshData.tabLi,
          ignoreTabPanes = refreshData.ignoreTabPanes,
          $li = tabLiData.$li,
          $contentPane = tabLiData.$contentPane,
          origTabData = tabLiData.origTabData,
          newTabData = tabLiData.newTabData,
          propNames = refreshData.propNames,
          isInitTabsRequired = false;
    
      // update tab title if necessary
      if (origTabData[propNames.title] !== newTabData[propNames.title]) {
        $li.find('a[role="tab"]')
            .html(origTabData[propNames.title] = newTabData[propNames.title]);
    
        isInitTabsRequired = true;
      }
    
      // update tab disabled state if necessary
      if (origTabData[propNames.disabled] !== newTabData[propNames.disabled]) {
        if (newTabData[propNames.disabled]) { // enabled -> disabled
          $li.addClass('disabled');
          $li.find('a[role="tab"]').attr('data-toggle', '');
        } else { // disabled -> enabled
          $li.removeClass('disabled');
          $li.find('a[role="tab"]').attr('data-toggle', 'tab');
        }
    
        origTabData[propNames.disabled] = newTabData[propNames.disabled];
        isInitTabsRequired = true;
      }
    
      // update tab active state if necessary
      if (refreshData.options.forceActiveTab) {
        // set the active tab based on the tabs array regardless of the current
        // DOM state, which could have been changed by the user clicking a tab
        // without those changes being reflected back to the tab data
        $li[newTabData[propNames.active] ? 'addClass' : 'removeClass']('active');
    
        $contentPane[newTabData[propNames.active] ? 'addClass' : 'removeClass']('active');
    
        origTabData[propNames.active] = newTabData[propNames.active];
    
        isInitTabsRequired = true;
      }
    
      // update tab content pane if necessary
      if (!ignoreTabPanes && origTabData[propNames.content] !== newTabData[propNames.content]) {
        $contentPane.html(origTabData[propNames.content] = newTabData[propNames.content]);
        isInitTabsRequired = true;
      }
    
      return isInitTabsRequired;
    }
    
    function checkForTabRemoved(refreshData) {
      var tabLiData = refreshData.tabLi,
          ignoreTabPanes = refreshData.ignoreTabPanes,
          $li = tabLiData.$li,
          idxToMakeActive;
    
      if (tabLiData.newIdx !== -1) { // tab was not removed--it has a valid index
        return false;
      }
    
      // if this was the active tab, make the closest enabled tab active
      if ($li.hasClass('active')) {
    
        idxToMakeActive = tabUtils.getIndexOfClosestEnabledTab(refreshData.$currTabLis, tabLiData.currDomIdx);
        if (idxToMakeActive > -1) {
          refreshData.$currTabLis
            .eq(idxToMakeActive)
            .addClass('active');
    
          if (!ignoreTabPanes) {
            refreshData.$currTabContentPanes
              .eq(idxToMakeActive)
              .addClass('active');
          }
        }
      }
    
      $li.remove();
    
      if (!ignoreTabPanes) {
        tabLiData.$contentPane.remove();
      }
    
      return true;
    }
    
    function checkForTabsOrderChanged(refreshData) {
      var $currTabLis = refreshData.$currTabLis,
          updatedTabsArray = refreshData.updatedTabsArray,
          propNames = refreshData.propNames,
          ignoreTabPanes = refreshData.ignoreTabPanes,
          newTabsCollection = [],
          newTabPanesCollection = ignoreTabPanes ? null : [];
    
      if (!tabUtils.didTabOrderChange($currTabLis, updatedTabsArray, propNames)) {
        return false;
      }
    
      // the tab order changed...
      updatedTabsArray.forEach(function (t, i) {
        var paneId = t[propNames.paneId];
    
        newTabsCollection.push(
            $currTabLis
              .find('a[role="tab"][href="#' + paneId + '"]')
              .parent('li')
            );
    
        if (!ignoreTabPanes) {
          newTabPanesCollection.push($('#' + paneId));
        }
      });
    
      refreshData.$navTabs.append(newTabsCollection);
    
      if (!ignoreTabPanes) {
        refreshData.$currTabContentPanesContainer.append(newTabPanesCollection);
      }
    
      return true;
    }
    
    function checkForTabsRemovedOrUpdated(refreshData) {
      var $currTabLis = refreshData.$currTabLis,
          updatedTabsArray = refreshData.updatedTabsArray,
          propNames = refreshData.propNames,
          isInitTabsRequired = false;
    
    
      $currTabLis.each(function (currDomIdx) {
        var $li = $(this),
            origTabData = $li.data('tab'),
            newIdx = tabUtils.getTabIndexByPaneId(updatedTabsArray, propNames.paneId, origTabData[propNames.paneId]),
            newTabData = (newIdx > -1) ? updatedTabsArray[newIdx] : null;
    
        refreshData.tabLi = {
          $li: $li,
          currDomIdx: currDomIdx,
          newIdx: newIdx,
          $contentPane: tabElements.getElTabPaneForLi($li),
          origTabData: origTabData,
          newTabData: newTabData
        };
    
        if (checkForTabRemoved(refreshData)) {
          isInitTabsRequired = true;
          return; // continue to next $li in .each() since we removed this tab
        }
    
        if (checkForTabPropertiesUpdated(refreshData)) {
          isInitTabsRequired = true;
        }
      });
    
      return isInitTabsRequired;
    }
    
    function listenForDropdownMenuTabs($scroller, stc) {
      var $ddMenu;
    
      // for dropdown menus to show, we need to move them out of the
      // scroller and append them to the body
      $scroller
        .on(CONSTANTS.EVENTS.DROPDOWN_MENU_SHOW, handleDropdownShow)
        .on(CONSTANTS.EVENTS.DROPDOWN_MENU_HIDE, handleDropdownHide);
    
      function handleDropdownHide(e) {
        // move the dropdown menu back into its tab
        $(e.target).append($ddMenu.off(CONSTANTS.EVENTS.CLICK));
      }
    
      function handleDropdownShow(e) {
        var $ddParentTabLi = $(e.target),
            ddLiOffset = $ddParentTabLi.offset(),
            $currActiveTab = $scroller.find('li[role="presentation"].active'),
            ddMenuRightX,
            tabsContainerMaxX,
            ddMenuTargetLeft;
    
        $ddMenu = $ddParentTabLi
                    .find('.dropdown-menu')
                    .attr('data-' + CONSTANTS.DATA_KEY_DDMENU_MODIFIED, true);
    
        // if the dropdown's parent tab li isn't already active,
        // we need to deactivate any active menu item in the dropdown
        if ($currActiveTab[0] !== $ddParentTabLi[0]) {
          $ddMenu.find('li.active').removeClass('active');
        }
    
        // we need to do our own click handling because the built-in
        // bootstrap handlers won't work since we moved the dropdown
        // menu outside the tabs container
        $ddMenu.on(CONSTANTS.EVENTS.CLICK, 'a[role="tab"]', handleClickOnDropdownMenuItem);
    
        $('body').append($ddMenu);
    
        // make sure the menu doesn't go off the right side of the page
        ddMenuRightX = $ddMenu.width() + ddLiOffset.left;
        tabsContainerMaxX = $scroller.width() - (stc.$slideRightArrow.outerWidth() + 1);
        ddMenuTargetLeft = ddLiOffset.left;
    
        if (ddMenuRightX > tabsContainerMaxX) {
          ddMenuTargetLeft -= (ddMenuRightX - tabsContainerMaxX);
        }
    
        $ddMenu.css({
          'display': 'block',
          'top': ddLiOffset.top + $ddParentTabLi.outerHeight() - 2,
          'left': ddMenuTargetLeft
        });
    
        function handleClickOnDropdownMenuItem(e) {
          var $selectedMenuItemAnc = $(this),
              $selectedMenuItemLi = $selectedMenuItemAnc.parent('li'),
              $selectedMenuItemDropdownMenu = $selectedMenuItemLi.parent('.dropdown-menu'),
              targetPaneId = $selectedMenuItemAnc.attr('href');
    
          if ($selectedMenuItemLi.hasClass('active')) {
            return;
          }
    
          // once we select a menu item from the dropdown, deactivate
          // the current tab (unless it's our parent tab), deactivate
          // any active dropdown menu item, make our parent tab active
          // (if it's not already), and activate the selected menu item
          $scroller
            .find('li.active')
            .not($ddParentTabLi)
            .add($selectedMenuItemDropdownMenu.find('li.active'))
            .removeClass('active');
    
          $ddParentTabLi
            .add($selectedMenuItemLi)
            .addClass('active');
    
          // manually deactivate current active pane and activate our pane
          $('.tab-content .tab-pane.active').removeClass('active');
          $(targetPaneId).addClass('active');
        }
    
      }
    }
    
    function refreshDataDrivenTabs($container, options) {
      var instanceData = $container.data().scrtabs,
          scroller = instanceData.scroller,
          $navTabs = $container.find('.scrtabs-tab-container .nav-tabs'),
          $currTabContentPanesContainer = $container.find('.tab-content'),
          isInitTabsRequired = false,
          refreshData = {
            options: options,
            updatedTabsArray: instanceData.tabs,
            propNames: instanceData.propNames,
            ignoreTabPanes: instanceData.ignoreTabPanes,
            $navTabs: $navTabs,
            $currTabLis: $navTabs.find('> li'),
            $currTabContentPanesContainer: $currTabContentPanesContainer,
            $currTabContentPanes: $currTabContentPanesContainer.find('.tab-pane')
          };
    
      // to preserve the tab positions if we're just adding or removing
      // a tab, don't completely rebuild the tab structure, but check
      // for differences between the new tabs array and the old
      if (checkForTabAdded(refreshData)) {
        isInitTabsRequired = true;
      }
    
      if (checkForTabsOrderChanged(refreshData)) {
        isInitTabsRequired = true;
      }
    
      if (checkForTabsRemovedOrUpdated(refreshData)) {
        isInitTabsRequired = true;
      }
    
      if (isInitTabsRequired) {
        scroller.initTabs();
      }
    
      return isInitTabsRequired;
    }
    
    function refreshTargetElementInstance($container, options) {
      if (!$container.data('scrtabs')) { // target element doesn't have plugin on it
        return;
      }
    
      // force a refresh if the tabs are static html or they're data-driven
      // but the data didn't change so we didn't call initTabs()
      if ($container.data('scrtabs').isWrapperOnly || !refreshDataDrivenTabs($container, options)) {
        $('body').trigger(CONSTANTS.EVENTS.FORCE_REFRESH);
      }
    }
    
    function scrollToActiveTab() {
      var $targetElInstance = $(this),
          scrtabsData = $targetElInstance.data('scrtabs');
    
      if (!scrtabsData) {
        return;
      }
    
      scrtabsData.scroller.scrollToActiveTab();
    }
    
    var methods = {
      destroy: function() {
        var $targetEls = this;
    
        return $targetEls.each(destroyPlugin);
      },
    
      init: function(options) {
        var $targetEls = this,
            targetElsLastIndex = $targetEls.length - 1,
            settings = $.extend({}, $.fn.scrollingTabs.defaults, options || {});
    
        // ---- tabs NOT data-driven -------------------------
        if (!settings.tabs) {
    
          // just wrap the selected .nav-tabs element(s) in the scroller
          return $targetEls.each(function(index) {
            var dataObj = {
                  isWrapperOnly: true
                },
                $targetEl = $(this).data({ scrtabs: dataObj }),
                readyCallback = (index < targetElsLastIndex) ? null : function() {
                  $targetEls.trigger(CONSTANTS.EVENTS.TABS_READY);
                };
    
            if (settings.enableSwiping) {
              $targetEl.parent().addClass(CONSTANTS.CSS_CLASSES.ALLOW_SCROLLBAR);
              $targetEl.data('scrtabs').enableSwipingElement = 'parent';
            }
    
            wrapNavTabsInstanceInScroller($targetEl, settings, readyCallback);
          });
    
        }
    
        // ---- tabs data-driven -------------------------
        return $targetEls.each(function (index) {
          var $targetEl = $(this),
              readyCallback = (index < targetElsLastIndex) ? null : function() {
                $targetEls.trigger(CONSTANTS.EVENTS.TABS_READY);
              };
    
          var $newTargetEl = buildNavTabsAndTabContentForTargetElementInstance($targetEl, settings, readyCallback);
    
          if (settings.enableSwiping) {
            $newTargetEl.addClass(CONSTANTS.CSS_CLASSES.ALLOW_SCROLLBAR);
            $newTargetEl.data('scrtabs').enableSwipingElement = 'self';
          }
        });
      },
    
      refresh: function(options) {
        var $targetEls = this,
            settings = $.extend({}, $.fn.scrollingTabs.defaults, options || {});
    
        return $targetEls.each(function () {
          refreshTargetElementInstance($(this), settings);
        });
      },
    
      scrollToActiveTab: function() {
        return this.each(scrollToActiveTab);
      }
    };
    
    function destroyPlugin() {
      var $targetElInstance = $(this),
          scrtabsData = $targetElInstance.data('scrtabs'),
          $tabsContainer;
    
      if (!scrtabsData) {
        return;
      }
    
      if (scrtabsData.enableSwipingElement === 'self') {
        $targetElInstance.removeClass(CONSTANTS.CSS_CLASSES.ALLOW_SCROLLBAR);
      } else if (scrtabsData.enableSwipingElement === 'parent') {
        $targetElInstance.closest('.scrtabs-tab-container').parent().removeClass(CONSTANTS.CSS_CLASSES.ALLOW_SCROLLBAR);
      }
    
      scrtabsData.scroller
        .off(CONSTANTS.EVENTS.DROPDOWN_MENU_SHOW)
        .off(CONSTANTS.EVENTS.DROPDOWN_MENU_HIDE);
    
      // if there were any dropdown menus opened, remove the css we added to
      // them so they would display correctly
      scrtabsData.scroller
        .find('[data-' + CONSTANTS.DATA_KEY_DDMENU_MODIFIED + ']')
        .css({
          display: '',
          left: '',
          top: ''
        })
        .off(CONSTANTS.EVENTS.CLICK)
        .removeAttr('data-' + CONSTANTS.DATA_KEY_DDMENU_MODIFIED);
    
      if (scrtabsData.scroller.hasTabClickHandler) {
        $targetElInstance
          .find('a[data-toggle="tab"]')
          .off('.scrtabs');
      }
    
      if (scrtabsData.isWrapperOnly) { // we just wrapped nav-tabs markup, so restore it
        // $targetElInstance is the ul.nav-tabs
        $tabsContainer = $targetElInstance.parents('.scrtabs-tab-container');
    
        if ($tabsContainer.length) {
          $tabsContainer.replaceWith($targetElInstance);
        }
    
      } else { // we generated the tabs from data so destroy everything we created
        if (scrtabsData.scroller && scrtabsData.scroller.initTabs) {
          scrtabsData.scroller.initTabs = null;
        }
    
        // $targetElInstance is the container for the ul.nav-tabs we generated
        $targetElInstance
          .find('.scrtabs-tab-container')
          .add('.tab-content')
          .remove();
      }
    
      $targetElInstance.removeData('scrtabs');
    
      $(window).off(CONSTANTS.EVENTS.WINDOW_RESIZE);
      $('body').off(CONSTANTS.EVENTS.FORCE_REFRESH);
    }
    
    
    $.fn.scrollingTabs = function(methodOrOptions) {
    
      if (methods[methodOrOptions]) {
        return methods[methodOrOptions].apply(this, Array.prototype.slice.call(arguments, 1));
      } else if (!methodOrOptions || (typeof methodOrOptions === 'object')) {
        return methods.init.apply(this, arguments);
      } else {
        $.error('Method ' + methodOrOptions + ' does not exist on $.scrollingTabs.');
      }
    };
    
    $.fn.scrollingTabs.defaults = {
      tabs: null,
      propPaneId: 'paneId',
      propTitle: 'title',
      propActive: 'active',
      propDisabled: 'disabled',
      propContent: 'content',
      ignoreTabPanes: false,
      scrollToTabEdge: false,
      disableScrollArrowsOnFullyScrolled: false,
      forceActiveTab: false,
      reverseScroll: false,
      widthMultiplier: 1,
      tabClickHandler: null,
      cssClassLeftArrow: 'fa fa-chevron-left',
      cssClassRightArrow: 'fa fa-chevron-right',
      leftArrowContent: '',
      rightArrowContent: '',
      enableSwiping: false
    };
    
  
  
  }(jQuery, window));
  
  
  
  
  ;(function() {
      'use strict';
    
    
      $(activate);
    
    
      function activate() {
    
        $('.hs-wrapper .nav-tabs')
          .scrollingTabs({
            enableSwiping: true
          })
          .on('ready.scrtabs', function() {
            $('.tab-content').show();
          });
    
      }
    }());
  /*-------------------end--horizontal--tabs---scrolling---js---------*/