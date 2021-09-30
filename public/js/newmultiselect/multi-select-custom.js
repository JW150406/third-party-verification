 $(function (e) {
    $('#supervisor').multiSelect();
    $('label.multi-select-menuitem').append('<span class="checkmark"></span>');
    $('label.multi-select-menuitem').addClass('custom-checkbox');
    $('.multi-select-menu').addClass('scrollbar-inner');
    $('.multi-select-menu').addClass('scrollbar-dynamic');
    

    // $('#twilio_workflow').multiSelect();
    // $('label.multi-select-menuitem').append('<span class="checkmark"></span>');
    // $('label.multi-select-menuitem').addClass('custom-checkbox');
    // $('.multi-select-menu').addClass('scrollbar-inner');
    // $('.multi-select-menu').addClass('scrollbar-dynamic');
});