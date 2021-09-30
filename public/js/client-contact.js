$( function() {
    var geocoder, map, marker;
    
   $_this = $;

  $_this( "body").on( "change", ".selectprogram",function( event  ) {
    var commodity_type =  $_this('#commodityselector').val();
    var parentelement =  $_this(this).data('parentelement');
    var dataref =  $_this(this).data('ref');
   var   code = $_this(this)[0].selectedOptions[0].attributes['data-code'].nodeValue;
   var   rate =$_this(this)[0].selectedOptions[0].attributes['data-rate'].nodeValue;
   var   etf = $_this(this)[0].selectedOptions[0].attributes['data-etf'].nodeValue;
   var   msf = $_this(this)[0].selectedOptions[0].attributes['data-msf'].nodeValue;
   var   term = $_this(this)[0].selectedOptions[0].attributes['data-term'].nodeValue;
   var   nofdigit = $_this(this)[0].selectedOptions[0].attributes['data-accountlength'].nodeValue;
   var   accountnumbertype = $_this(this)[0].selectedOptions[0].attributes['data-accountnumbertype'].nodeValue;
   var   id = $_this(this)[0].selectedOptions[0].attributes['data-id'].nodeValue;
    
   

var rate_field = $_this(this).data('rate');
var term_field = $_this(this).data('term');
var msf_field = $_this(this).data('msf');
var etf_field = $_this(this).data('etf'); 
var id_field = $_this(this).data('idfield');
var prodetail = $_this(this).data('prodetail');
var account = $_this(this).data('account');

 $('#'+parentelement+' .'+account+' .account_number_field').data('nofdigit',nofdigit);

$_this('#'+parentelement+' .program_code').val(code);
   $_this('#'+parentelement+' #'+rate_field).val(rate);
   $_this('#'+parentelement+' #'+term_field).val(term);
   $_this('#'+parentelement+' #'+msf_field).val(msf);
   $_this('#'+parentelement+' #'+etf_field).val(etf);
   //$_this('#udcaccountcode').val(code);
   var target_udc_account_field = $_this(this).data('ref');
   //var commodity_type =  $_this('#commodityselector').val();
   if( dataref == 'Electric' ){
    $_this('#'+parentelement+' .electric_program_code').val(code);
    $_this('#'+parentelement+' .programaccountcode').val(code);
   }
   if( dataref == 'Gas' ){
    $_this('#'+parentelement+' .programgasaccountcode').val(code);
    $_this('#'+parentelement+' .gas_program_code').val(code);
   }

  

    
   // $_this('#'+parentelement+' .accountnametype').val(accountnumbertype);
   // $_this('#'+parentelement+' .account_number_type').val(accountnumbertype);
   // //$_this('#'+parentelement+' .account_number_length').val(nofdigit);
   // $_this('#'+parentelement+' .account_number_type_view').html('('+accountnumbertype+')');
   
   
   $_this('#'+parentelement+' .udcaccountname').val($_this(this).val());
   $_this('#'+parentelement+' #'+id_field).val(id);
   
   
   
   
   



     $_this('#'+parentelement+' .'+prodetail+' .program_detail').html('Utility:   '+code +',  Rate:'+rate+' ETF:'+etf+', MSF:'+msf);
 
   });

//$_this(".contact-number-format").inputmask({"mask": "999-999-9999"});  
   $_this('body').on('click','.contact-next-step',function(e){
        var target_div = $_this(this).data('ref');
        $_this(".validation-error").css('visibility','hidden');
        var current_div = "form-section-"+$_this(this).data('rel');
        var checkerror = 0;
        $_this('.'+current_div+' .required').each(function(){
           var  error = validator(this);
           if(error>0){
            checkerror = 1;
           }

        });
        if(checkerror==0){
            $_this('.'+current_div).addClass('hide-section');
            $_this('.'+target_div).removeClass('hide-section');

          }



   });

   $_this('body').on('click','.contact-previous-step',function(e){
        var current_div = $_this(this).data('ref');
        var target_div = "form-section-"+$_this(this).data('rel');
        $_this('.'+current_div).addClass('hide-section');
        $_this('.'+target_div).removeClass('hide-section');
  });


   $_this('body').on('submit','.company-contact-form',function(e){
    // return true; 
 
       $_this('#agent-main-data-wrapper .validation-error').remove();
      // $_this(".validation-error", this).css('visibility','hidden');
       var have_error = 0;
       
       $_this('.company-contact-form input').each(function(){
        
         if( typeof $_this(this).attr('data-validator') !== 'undefined' ){
           var error = validateSingleField( $_this(this).attr('data-validator') ,$_this(this));
         
           if(error != true){
              have_error = 1;
           }
         }
       });

       $_this('.company-contact-form select').each(function(){
        
         if( typeof $_this(this).attr('data-validator') !== 'undefined' ){
         
          if( $_this(this).attr('data-validator') == 'program'){
                if( $_this(this).val() == "" ){
                   console.log($_this(this));
                    var pdiv = $_this(this).parent('.form-group');
                    if( $_this('.validation-error',pdiv).length == 0 ){
                       $_this(this).after('<span class="invalid-feedback validation-error error-validation" >Please select a program</span>');
                    }
                      
                     have_error = 1;
                }
          }
          
         }
       });


      // $_this('.company-contact-form .required').each(function(){

      //   var  error = validator(this);
      //   if(error==1){
      //       have_error = 1;
      //       e.preventDefault();
      //   }

      // });
 if(have_error == 0) {
    $_this.ajax({
            type: "POST",
            url: '/validateLeadData',
            data: $_this(this).serialize(),
            success: function( response ) {
              console.log(response);
              if( typeof response.messages != 'undefined') {
                  if( response.messages.length > 0 ){
                     have_error = 1;
                     for( var i = 0; i< response.messages.length; i++){
                         // console.log();
                          var field = response.messages[i].field;
                          var message = response.messages[i].message;
                          var commodity = response.messages[i].commodity;
                           $_this('[data-validator='+field+'][data-commodity='+commodity+']').after('<span class="invalid-feedback validation-error error-validation" >'+message+'</span>');
                     }
                  }
              }
            
             }
        });
  }

     // return false;

      if(have_error > 0) {
        $_this('#agent-main-data-wrapper .validation-error').each(function(){
 
            if($(this).is(":visible") ){
             
                $('html, body').animate({
                    scrollTop: $(this).offset().top - 100
                }, 1000);
                return false;
            }
        });
        return false;   
         
     }
      
        // $_this('.company-contact-form .zipautocomplete').each(function(){
        //   var parentelement =  $_this(this).parent('.address-field').parent('.row').parent('.address-fields-wrapper');
        //   if($_this(".statefield",parentelement ).val() == ""){
        //     e.preventDefault();
        //     alert("Invalid Zipcode");
        //   }

        // });



   });

   function validator(element){
    var error = 0;
    var validate_option = $_this(element).attr('rel');

    if(validate_option == 'phonenumber' ){
       var phoneno = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;
        if($_this(".phonenumber",element).val() == ""){
            error = 1;
            $_this(".validation-error", element).css('visibility','visible');
            $_this(".validation-error strong", element).html('This field is required.');
        }
        if( $_this(".phonenumber",element).val().match(phoneno) == null){
            error = 1;
            $_this(".validation-error", element).css('visibility','visible');
            $_this(".validation-error strong", element).html('Enter enter your number like (999)-999-9999.');
        }



    }
    if(validate_option == 'text' ){
        if($_this(".validate", element).val() == ''){
            error = 1;
            $_this(".validation-error", element).css('visibility','visible');
           $_this(".validation-error strong", element).html('This field is required.');
        }
    }
    if(validate_option == 'account_number' ){
        var ref = $_this(".validate", element).data('ref');
        var accountlength =    $_this(".validate", element).data('nofdigit');
        //alert(accountlength);
        if($_this(".validate", element).val() == ''){
            error = 1;
            $_this(".validation-error", element).css('visibility','visible');
           $_this(".validation-error strong", element).html('This field is required.');
        }else if($_this(".validate", element).val().length != accountlength  && accountlength > 0 ){
            error = 1;
            $_this(".validation-error", element).css('visibility','visible');
           $_this(".validation-error strong", element).html('Account must have '+accountlength+' characters');
        }
    }


    if(validate_option == 'selectbox' || validate_option == 'utility' ||  validate_option == 'utility_program'  ){
        if($_this(".validate", element).val() !=""){
            var checkval = $_this(".validate", element).val();
        }else{
            var checkval = $_this(".validate", element).val();
        }
       


       if(checkval == ''){
           error = 1;
           $_this(".validation-error", element).css('visibility','visible');
           $_this(".validation-error strong", element).html('This field is required.');
       }
   }
   if(validate_option == 'textarea' ){
       if($_this(".validate", element).val() == ''){
           error = 1;
           $_this(".validation-error", element).css('visibility','visible');
           $_this(".validation-error strong", element).html('This field is required.');
       }
   }
   if(validate_option == 'checkbox' ){
       var validate_checkbox = 1;
       $_this("input[type='checkbox']",element).each(function(){
           if ($_this(this).is(':checked')) {
               validate_checkbox = 0;
           }
       });
       if(validate_checkbox == 1){
           error = 1;
           $_this(".validation-error", element).css('visibility','visible');
           $_this(".validation-error strong", element).html('This field is required.');
       }
   }
   if(validate_option == 'radio' ){
       var validate_radio = 1;
       $_this("input[type='radio']",element).each(function(){
           if (($_this(this).is(':checked'))) {
               validate_radio = 0
           }
       });

       if(validate_radio == 1){
           error = 1;
           $_this(".validation-error", element).css('visibility','visible');
           $_this(".validation-error strong", element).html('This field is required.');
       }
   }
   if(validate_option == 'address' ){
        var validate_address= 0;
        $_this(".addressrequired",element).each(function(){
            if ($_this(this).val() =="" ) {
                validate_address = 1
            }
        });

        if(validate_address == 1){
            error = 1;
            $_this(".validation-error", element).css('visibility','visible');
            $_this(".validation-error strong", element).html('This field is required.');
        }

    }
    if(validate_option == 'name' ){
        var validate_address= 0;
//$_this(".lastname",element).val() == "" ||  
        if($_this(".firstname",element).val() == ""){
            error = 1;
            $_this(".validation-error", element).css('visibility','visible');
            $_this(".validation-error strong", element).html('This field is required.');
        }


    }

   return error;
  }

  $_this('body').on('change','.utilityselect',function(){
      var token = $_this('.company-contact-form input[name="_token"]').val();
      var check_refrence_number = $_this(this).data('ref');
            $_this.ajax({
            type: "POST",
            url: '/getutility',
            data:{'_token':token },
            success: function( response ) {
               $_this('.programselect_'+check_refrence_number+ ' select').html("<option value=''>Select</option>"+response.options);
             }
        });
  });
//   $_this('body').on('change','.selectprogram',function(){
//     var check_refrence_number = $_this(this).data('ref');
//     var code = $_this('option:selected', this).data('code');
//     var rate = $_this('option:selected', this).data('rate');
//     var etf = $_this('option:selected', this).data('etf');
//     var msf = $_this('option:selected', this).data('msf');
//     var account_number_length = $_this('option:selected', this).data('accountlength');
//     var account_number_type = $_this('option:selected', this).data('accountnumbertype');
//     $_this('.program_code').val(code);
//     $_this('.account_number_length').val(account_number_length);
//     $_this('.account_number_type').val(account_number_type);


//     $_this('.programdetail_'+check_refrence_number+' .green-text').html("Utility:"+code+", Rate:"+rate+", ETF:"+etf+",  MSF:"+msf);


// });

// $_this('body').on('keyup','.zipautocomplete',function(){
//     var check_zip = $_this(this).val();
//     var parentelement =  $_this(this).parent('.address-field').parent('.row').parent('.address-fields-wrapper');
//     if(all_zipcodes.includes(check_zip) === true ){
//            zip_detail[check_zip];
        
//          $_this(".statefield",parentelement ).val(zip_detail[check_zip].state);
//          $_this(".cityfield",parentelement ).val(zip_detail[check_zip].name);
//          var element_number = $_this(".google_map",parentelement ).data('ref');
         
// geocoder = new google.maps.Geocoder();
//     geocoder.geocode({
//         'address': check_zip
//     }, function(results, status) {
         
//         if (status == google.maps.GeocoderStatus.OK) {
 
//             window['marker'+element_number].setMap(null);
         
//          window['marker'+element_number] =  new google.maps.Marker({
//                 map: window['map'+element_number],
//                 position: results[0].geometry.location
//             });
         
            
//             loc = new google.maps.LatLng( window['marker'+element_number].position.lat(),  window['marker'+element_number].position.lng());
//             window['marker'+element_number].setVisible(false);
//             var bounds = new google.maps.LatLngBounds();
//             bounds.extend(loc);
//             window['map'+element_number].fitBounds(bounds);
//             window['map'+element_number].setZoom(10);
//         }
//     });

          

//     }else{
//        $_this(".statefield",parentelement ).val('');
//        $_this(".cityfield",parentelement ).val('');
//     }
//  }); 

//  $_this('body').on('blur','.zipautocomplete',function(){
   
//     var parentelement =  $_this(this).parent('.address-field').parent('.row').parent('.address-fields-wrapper');
    
//     if($_this(".statefield",parentelement ).val() == ""){
      
//       alert("Invalid Zipcode");
//     }

  
// });
$_this('body').on('click','.searchzipcode',function(){   
    validateSearchZip();  
});
$_this('body').on('blur','.zipcodefield',function(){ 
    if($_this('.agent-main-form').is(":visible")) {
        validateSearchZip();  
    } 
   
}); 
$_this( "body").on( "change", ".utilityoptions",function( event  ) {
  if($_this(this).val() != "" ){
     $_this('.error-validation').remove();
  }
   var parentelement =  $_this(this).data('parentelement');
 
  
    var   market = $_this(this)[0].selectedOptions[0].attributes['data-market'].nodeValue;
    var   utility_id = $_this(this)[0].selectedOptions[0].attributes['data-id'].nodeValue;
    var   idfield = $_this(this).data('idfield');
    var   program_rel = $_this(this).data('rel');
    var   ref = $_this(this).data('ref');
   // console.log(market.toLowerCase());
    if ( market.toLowerCase() == 'nstar'){
        $_this('.nstar-fields').show();
        $_this('.nstar-fields').addClass('required');
    }else{
        $_this('.nstar-fields').hide();
        $_this('.nstar-fields').removeClass('required');
    }
    
    var options = "<option value=''>Select</option>";
    
    var errorelement = $_this('.selectutilitywrapper .validation-error strong');
     $_this('#'+parentelement+' #MarketCode').val(market);
     $_this('#'+parentelement+' #UDCAccountCode').val(market);
     $_this('#'+parentelement+' #udccompanyname').val($_this(this).val());
     $_this('#'+parentelement+' .account_number_type_view').html('');
     $_this('#'+parentelement+' #'+idfield).val(utility_id);

   //  var commodity_type =  $_this('#commodityselector').val();
     if( ref == 'Electric' || ref == 'electric' ){
      $_this('#'+parentelement+' .programaccountcode').val(market);
           $_this('#'+parentelement+' #electric_MarketCode').val(market);
            if ( market.toLowerCase() == 'nstar'){
                $_this('.electric-nstar-fields').show();
                $_this('.electric-nstar-fields').addClass('required');
            }else{
                $_this('.electric-nstar-fields').hide();
                $_this('.electric-nstar-fields').removeClass('required');
            }
     }
     if( ref == 'Gas' || ref == 'gas' ){
      $_this('#'+parentelement+' .programgasaccountcode').val(market);
      $_this('#'+parentelement+' #gas_MarketCode').val(market);
            if ( market.toLowerCase() == 'nstar'){
                $_this('.gas-nstar-fields').show();
                $_this('.gas-nstar-fields').addClass('required');
            }else{
                $_this('.gas-nstar-fields').hide();
                $_this('.gas-nstar-fields').removeClass('required');
            }
     }

     
     $_this.ajax({
        type: "POST",
        url: '/ajax/getprograms',
        data:{ utility_id : utility_id },
        success: function( response ) {             

            try {
                
                if(response.status == 'error'){
                    errorelement.html(response.message);
                   
                  
                }else{
                  
                    if( response.totalrecords > 0 ){
                       for(var i = 0 ; i < response.totalrecords; i++ ){
                        var single_record =  response.data[i];
                        var program_name = single_record.name;
                      //  alert(single_record.etf);
                        var etf = (single_record.etf != "" && single_record.etf != null ) ? single_record.etf : '0';
                        var rate = (single_record.rate != "" &&  single_record.rate != null) ? single_record.rate : '0';
                        var msf = (single_record.msf != "" && single_record.msf != null) ? single_record.msf : '0';
                        var term = (single_record.term != "" && single_record.term != null ) ? single_record.term : '0';


                        options = options + '<option ' +
                           ' value="'+program_name+'" '+
                           'data-programname="'+program_name+'" '+
                           'data-code="'+single_record.code+'" '+
                           'data-rate="'+single_record.rate+'" '+
                           'data-etf="'+etf+'" '+
                           'data-msf="'+ msf+'" '+
                           'data-term="'+ term+'" '+
                           'data-accounttype="'+single_record.accounttype+'" '+
                           'data-customertype="'+single_record.customertype+'" '+
                           'data-termtype="'+single_record.termtype+'" '+
                           'data-unitofmeasure="'+single_record.unit_of_measure+'" '+
                           'data-accountlength="'+single_record.accountnumberlength+'" '+
                           'data-accountnumbertype="'+single_record.accountnumbertype+'" '+
                           'data-id="'+single_record.id+'" > '+

                           program_name + ' (  code: '+single_record.code+', '+
                           'rate: ' + rate +', '+
                           'etf: ' + etf +', '+
                           'msf: ' + msf+', '+
                           'term: ' + term +') </option>';
                       }
                     
                    
                       $_this('#'+parentelement+' #'+program_rel).html( options );
                    }
                    

                    // $_this('.agent-main-form').show();
                }
            }
            catch (err) {
                     errorelement.html('Something went wrong! Please refresh the page and try again.');
            }
        }
    });
     
    });



function validateSearchZip(){
    var zipcode =  $_this('.validatezipcode .zipcodefield').val();
    var errorelement = $_this('.validatezipcode .validation-error strong');
    var commodity = $_this('#commodityselector').val();
    var client_id = $_this('#client_id').val();
    var options = "<option value=''>Select</option>";
    $_this('.utilityoptions').html(options);
  
    errorelement.html('');
    if(zipcode == ""){
     errorelement.html('Please enter zipcode');
     
    }else if(commodity == ""){
     errorelement.html('Please select commodity');
     
    } else{
        
   $_this('.searchzipcode').attr('disabled','disabled');
         $_this.ajax({
             type: "POST",
             url: '/ajax/validatezip',
             data:{ zipcode : zipcode, commodity : commodity, client_id: client_id },
             success: function( response ) {
                $_this('.searchzipcode').removeAttr('disabled');
                 try {
                     
                     if(response.status == 'error'){
                         errorelement.html(response.message);
                        
                         $_this('.agent-main-form').hide();
                     }else{
                        if( response.totalrecords > 0 ){   //var options = "";
                                    if(commodity == 'Dual Fuel'){
                                        var gas_commodity_options = options;
                                        var electric_commodity_options = options;
                                        for(var i = 0 ; i < response.totalrecords; i++ ){
                                            var market = ( response.data[i].market != "" ) ? " ("+response.data[i].market+")" : "";
                                            var fullname = ( response.data[i].fullname != "" ) ? response.data[i].fullname : "";

                                            if(fullname == null || fullname == ""){
                                                fullname = response.data[i].utilityname + market;
                                            }
                                            if( response.data[i].commodity =='Gas'){
                                                gas_commodity_options = gas_commodity_options + "<option  data-id='"+response.data[i].utid+"' data-market='"+response.data[i].market+"'  value='"+response.data[i].utilityname+"'>"+fullname +"</option>";                               
                                            }
                                            if( response.data[i].commodity =='Electric'){
                                                electric_commodity_options = electric_commodity_options + "<option  data-id='"+response.data[i].utid+"' data-market='"+response.data[i].market+"'  value='"+response.data[i].utilityname +"'>"+ fullname +"</option>";                               
                                            }
                                          
                                            
                                        }
                                        $_this('#gasutilityoptions').html( gas_commodity_options );
                                        $_this('#electricutilityoptions').html( electric_commodity_options );
                                        
                                    }else{
                                           
                
                                            
                                            
                
                
                                            for(var i = 0 ; i < response.totalrecords; i++ ){
                                                var market = ( response.data[i].market != "" ) ? " ("+response.data[i].market+")" : "";
                                                var fullname = ( response.data[i].fullname != "" ) ? response.data[i].fullname : "";
                                                if(fullname == null || fullname == ""){
                                                    fullname = response.data[i].utilityname + market ;
                                                }
                                                options = options + "<option  data-id='"+response.data[i].utid+"' data-market='"+response.data[i].market+"'  value='"+response.data[i].utilityname +"'>"+  fullname +"</option>";                               
                                            }
                                            $_this('#utilityoptions').html( options );
                                        }
                                        $_this('.zipcodeall').val(response.zipcode);
                                        $_this('.cityall').val(response.city);
                                        $_this('#zipcodeCity').val(response.city);
                                        $_this('.stateall').val(response.state);
                                        $_this('#zipcodestate').val(response.state);
                                        $_this('[data-cname="[ServiceState]"]').val(response.state);
                                        $_this('[data-cname="[ServiceCity]"]').val(response.city);
                                        $_this('#service_addresscity').val(response.city);
                                        $_this('#service_addressstate').val(response.state);
                                        $_this('[data-cname="[ServiceZip]"]').val(response.zipcode);
                        }
                         
                         
                      

                          $_this('.agent-main-form').show();
                     }
                 }
                 catch (err) {
                     console.log(err);
                          errorelement.html('Something went wrong! Please refresh the page and try again.');
                 }
             }
         });
    }
}

//$_this('body').on('focus','.account_number_field',function(){

     
    // var accountlength =    $_this(this).data('nofdigit');
    // var parentelement =    $_this(this).data('parentelement');
    // var element = $_this(this).next(".validation-error");
    
    // element.css('visibility','visible');
    // if($_this('#'+parentelement+' #programoptions').val() !==""){
    //     $_this(this).attr('maxlength',accountlength )
    //     $_this("strong", element).html('Account must have '+accountlength+' characters');
    // }else{
    //     $_this("strong", element).html('Please select a utility and a program');
    // }
    //alert(accountlength);
   
      
    
//});

function validateSingleField(fieldType,element){
   
  var commodity = $_this('#commodityselector').val();
  var market = $_this('#utilityoptions').find(':selected').attr('data-market');
      // console.log(commodity,"=d=d=d=d=");

  if(commodity  == 'Dual Fuel'){
      // console.log(element);
         
        
        if( typeof element.data( 'commodity') !== 'undefined' ){
          commodity =  element.data( 'commodity');

          if(commodity == 'Gas'){
            var market = $_this('#gasutilityoptions').find(':selected').attr('data-market'); 
          }else{

             var market = $_this('#electricutilityoptions').find(':selected').attr('data-market'); 
          }
           //console.log('--ss--',commodity,market );
           if( element.is(":visible")){
              return  validateElement(fieldType,commodity,market,element);
           }else{
              return true;
           }
           

        }
    return false;
  }else{ 
        if( element.is(":visible")){
              return  validateElement(fieldType,commodity,market,element);
           }else{
              return true;
           }
  }
  
  return true;
}

function validateElement(fieldType,commodity,market,element){
    

      
        var id = fieldType+'-'+commodity+'-error';
        if( $_this('#'+id).length == 0  &&  typeof market == "undefined"){
           element.after('<span class="invalid-feedback validation-error error-validation" id="'+id+'">Please select Utility first</span>');  
           return false;
        }
        if( typeof validations_rule[commodity][market][fieldType] !== 'undefined' && typeof validations_rule[commodity] !== 'undefined' && typeof validations_rule[commodity][market] !== 'undefined'  && market != ""){
         var validationRules  = validations_rule[commodity][market][fieldType]; 

           if( typeof element.data('validator') != 'undefined' && element.data('validator') == 'UtilityAccountNumber' ){
            var parentelement =  element.data('parentelement');
            console.log(validationRules.length,'account number length');
              var nofdigit = validationRules.length;
              var accountnumbertype = validationRules.displayname;
            $_this('#'+parentelement+' .account_number_length').val(nofdigit);
            $_this('#'+parentelement+' .accountnametype').val(accountnumbertype);
            $_this('#'+parentelement+' .account_number_type').val(accountnumbertype); 
            $_this('#'+parentelement+' .account_number_type_view').html('('+accountnumbertype+')');
          } 

         if( validationRules.required == 1 ){
             //element.attr('required','required');
             if( element.val() == ""){
                   if( $_this('#'+id).length == 0 ){
                    if(validationRules.length > 0){
                      element.attr('maxlength',validationRules.length);
                    }
                         
                         element.after('<span class="invalid-feedback validation-error" id="'+id+'">'+validationRules.message+'</span>');   
                    }
                 return false;
             }else  if(!element.val().match(validationRules.regx)  && element.val() != "" ){
                    if( $_this('#'+id).length == 0 ){
                      if(validationRules.length > 0){
                          element.attr('maxlength',validationRules.length);
                        }
                         //element.attr('maxlength',validationRules.length);
                         element.after('<span class="invalid-feedback validation-error" id="'+id+'">'+validationRules.message+'</span>');   
                    }
                 return false;
              }else{
                element.next('.validation-error').remove();
               }
           }else if(validationRules.required == 0 && element.val()!="" ){

                   if( element.val() == ""){
                       if( $_this('#'+id).length == 0 ){
                             if(validationRules.length > 0){
                          element.attr('maxlength',validationRules.length);
                        }
                             element.after('<span class="invalid-feedback validation-error" id="'+id+'">'+validationRules.message+'</span>');   
                        }
                    return false;
                  }else if(!element.val().match(validationRules.regx) && element.val() != ""  ){
                        if( $_this('#'+id).length == 0 ){
                              if(validationRules.length > 0){
                                element.attr('maxlength',validationRules.length);
                              }
                             element.after('<span class="invalid-feedback validation-error" id="'+id+'">'+validationRules.message+'</span>');   
                        }
                    return false;
                  }else{
                    element.next('.validation-error').remove();
                   }

           } 
          

                
        }
      return true;
}


$_this('body').on('focusout','input',function(){

 if( typeof $_this(this).attr('data-validator') !== 'undefined' ){
   var error = validateSingleField( $_this(this).attr('data-validator') ,$_this(this));
  
 }

    

    // var accountlength =    $_this(this).data('nofdigit');
    // var element = $_this(this).next(".validation-error");
    
    // element.css('visibility','visible');
    // if($_this(this).val() !==""){
    //     $_this(this).attr('maxlength',accountlength );
    //     var remaninig =  accountlength - $_this(this).val().length;
    //     if(remaninig >= 0){
    //         $_this("strong", element).html('Account must have '+accountlength+' characters. '+remaninig+ " remaining");
    //     }else{
    //         $_this("strong", element).html("Please select utility and program.");
    //     }
       
    // }else{
    //     $_this("strong", element).html('');
    // }
});

$_this('body').on('change','.relationship-selectbox',function(){
  
  //console.log($_this(this).val());
  if($_this(this).val() == 'Other'){
    $_this('#addnewoption').modal('show');
    $_this('#reference_field_id').val($_this(this).attr('name')); 
  }

});
$_this('body').on('click','.add-new-option',function(){
 var value_to_add_in = $_this('#reference_field_id').val();
 var new_option =  $_this('#new-option-to-add').val();
 $_this('.added-new-option-message').html('');

     if(value_to_add_in != ""){

        $_this('#new-option-to-add').val('');
        $_this('select[name="'+value_to_add_in+'"]').append('<option selected="selected"> '+
                                       new_option
                                  +'</option>'); 
        $_this('#new-option-to-add').val('');
         $_this('.added-new-option-message').html('<p class="alert alert-success">Option added successfully</p>');
        setTimeout(function(){
     $_this('.added-new-option-message').html('');
        },3000);


     }
    
});



} );
var input,autocomplete;
var componentForm = {
       street_number: 'short_name',
       route: 'long_name',
       locality: 'long_name',
       // administrative_area_level_1: 'short_name',
       // country: 'long_name',
       // postal_code: 'short_name'
     };

    function getLocation() {
        if(navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(geoSuccess, geoError);
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }
    function geoSuccess(position) {
          window.lat = position.coords.latitude;
          window.lng = position.coords.longitude;         
        //  initMap();
     
    }

    function geoError() {
    //    console.log("Geocoder failed.");
    }
function initMap() {
     if(window.lat){
             var latlongs = {lat: window.lat, lng: window.lng };
     }else{
        var latlongs = {lat: 41.850033, lng: -87.6500523};
     }
   
  $('.autocompletestreet').each(function(){
  var id_number =  $(this).data('ref');
    var input = "element_"+id_number;

     window['map'+id_number] =  map = new google.maps.Map(document.getElementById('google_map_'+id_number), {
        center: latlongs,
        zoom: 6,
        mapTypeId: 'roadmap'
      });
       window.marker =    new google.maps.Marker({
        position: latlongs,
        map: map, 
      });
      window['marker'+id_number] =  window.marker;
      
    var input = document.getElementById('autocompletestreet_'+id_number);
    var new_id = "autocomplete_"+id_number;
    var options = { 
           types: ['geocode']   

        };
   
        
    new_id = new google.maps.places.Autocomplete(input,options);
    new_id.bindTo('bounds', map);
    new_id.addListener('place_changed',  function(){
          window.marker.setVisible(false);
          var place = new_id.getPlace();
          if (!place.geometry) {
            
            window.alert("No details available for input: '" + place.name + "'");
            return;
          }
 
          if (place.geometry.viewport) {
            window.map.fitBounds(place.geometry.viewport);
          } else {
            window.map.setCenter(place.geometry.location);
            window.map.setZoom(17);  
          }
          window.marker.setPosition(place.geometry.location);
          window.marker.setVisible(true);

    }   );
    new_id.setOptions({strictBounds: true});



    new_id.setFields(['address_components', 'geometry', 'icon', 'name']);
    new_id.addListener('place_changed', function() {
     fill_street(new_id,input);

    });

  });



}


 
  
function fill_street(new_id,input){
  var place = new_id.getPlace();
  var get_value = "";
   for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (componentForm[addressType]) {
              var val = place.address_components[i][componentForm[addressType]];
              if(get_value ==""){
                get_value =   val;
              }else{
                get_value = get_value + " " + val;
              }

            }
          }
          if(get_value !=""){
           input.value = get_value;
          }
}
