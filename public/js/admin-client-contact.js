function sticky_relocate() {
    var window_top = $(window).scrollTop();
    var div_top = $('#sticky-anchor').offset().top;
    if (window_top > div_top) {
        $('#sticky').addClass('stick');
    } else {
        $('#sticky').removeClass('stick');
    }
}

$(function() {
    $(window).scroll(sticky_relocate);
    sticky_relocate();
});

$( function() {

    

    function scrolltodiv(element){
        $('html, body').animate({
            scrollTop: $("#"+element).offset().top
        }, 2000);
    }

    function remove_class(){
        setTimeout(() => {
            $('.new-added').removeClass('new-added');
        }, 5000);
    }
        
    function random_number() {
      return  Math.floor(Math.random()*(100-10+1)+10);
    }

    $('body').on('click','.collapsed_icon',function(){
       var refid = $(this).attr('rel');
       $('#form_field_wrapper_'+refid).toggleClass('open');
    });

    

    $('body').on('click','.add_radio_option',function(){
       var refid = $(this).attr('rel');
       var newoption =  radioButtonHtml(refid, '');
       $('#form_field_wrapper_'+refid+' .options').append(newoption) ;
    });

    function radioButtonHtml(elementNumber, optionValue, checked = false) {
        var option_numbers  =  random_number();
        var removeoptionlink = '<span><a href="javascript:void(0);" class="remove_options" rel="'+elementNumber+'" ref="radio_option_'+elementNumber+'_'+option_numbers+'"><i class="fa fa-fw fa-times"></i></a></span>';
        var radio_option =   '<li class="radio_option_'+elementNumber+'_'+option_numbers+'">'+
                              '<span class="middle-span-field-wrapper"><input type="text" value="'+optionValue+'" name="field['+elementNumber+'][radio][meta][options][]" class="form-control option_value_alter" data-parsley-required="true" ref="radio_option_'+elementNumber+'_'+option_numbers+'"></span>'+
                              removeoptionlink +
                           '</li>';
        return radio_option;
    }

    $('body').on('click','.add_checkbox_option',function(){
       var refid = $(this).attr('rel');
       var newoption =  checkboxHtml(refid, '');
       $('#form_field_wrapper_'+refid+' .options').append(newoption) ;
    });

    function checkboxHtml(elementNumber, optionValue, checked = false){
        var option_numbers  =  random_number();
        var removeoptionlink = '<span><a href="javascript:void(0);" class="remove_options" rel="'+elementNumber+'" ref="radio_option_'+elementNumber+'_'+option_numbers+'"><i class="fa fa-fw fa-times"></i></a></span>';
        var radio_option =   '<li class="radio_option_'+elementNumber+'_'+option_numbers+'">'+
                              '<span class="middle-span-field-wrapper"><input type="text" value="'+optionValue+'" name="field['+elementNumber+'][checkbox][meta][options][]" class="form-control checkbox_option_value_alter" data-parsley-required="true" ref="radio_option_'+elementNumber+'_'+option_numbers+'"></span>'+
                              removeoptionlink +
                           '</li>';
        return radio_option;
    }

    $('body').on('click','.add_selectbox_option',function(){
       var refid = $(this).attr('rel');
       var newoption =  selectboxHtml(refid, '');
       $('#form_field_wrapper_'+refid+' .options').append(newoption) ;
    });

    function selectboxHtml(elementNumber, value) {
        var option_numbers  =  random_number();
        var removeOptionLink = '<span><a href="javascript:void(0);" class="remove_options" rel="'+elementNumber+'" ref="select_option_'+elementNumber+'_'+option_numbers+'"><i class="fa fa-fw fa-times"></i></a></span>';
        var radio_option =   '<li class="select_option_'+elementNumber+'_'+option_numbers+'">'+
                          '<span class="middle-span-field-wrapper"><input type="text" value="'+value+'" name="field['+elementNumber+'][selectbox][meta][options][]" data-parsley-required="true" class="form-control option_value_alter" ref="select_option_'+elementNumber+'_'+option_numbers+'"></span>'+
            removeOptionLink +
                       '</li>';
        return radio_option;
    }

    
    
    
});

        
  
        