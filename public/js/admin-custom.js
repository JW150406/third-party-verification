$( function() {
    $_this = $;
    $_this('.selectmenu').select2({
        minimumResultsForSearch: -1
    });
    $_this('.selectsearch').select2({
        minimumResultsForSearch: 1
    });
    $_this('.timezone-select').select2({
      minimumResultsForSearch: 10
  });
    // $_this('input[type="checkbox"], input[type="radio"]').iCheck({
    //     checkboxClass: 'icheckbox_minimal',
    //     radioClass: 'iradio_minimal',
    //     increaseArea: '20%' // optional
    //   });

    function make_equal_height(classname){
        var max_height = "";
        $_this(classname).css('height','auto');
        $_this(classname).each(function(){
            if( $_this(this).height() > max_height){
            max_height = $_this(this).height();
          }
        });
        $_this(classname).css('height',max_height);

    }
    make_equal_height('.equal-height-content');
    $_this(window).resize(function(){
        make_equal_height('.equal-height-content');
    });
    // $_this( ".selectmenu" ).selectmenu();
    // $_this( ".datepicker" ).datepicker();
    $_this('body').on('click','.deactivate-clientuser',function(e){
        var vid = $_this(this).data('vid');
        var clientsalescenter = $_this(this).data('clientsalescenter');
        $_this('#salescenterid').val(vid);
        $_this('#status_to_change').val('inactive');
        $_this('.status-to-change-text').html('deactivate');
        $_this('.status-change-clientsalescenter').html(clientsalescenter);


    });
    $_this('body').on('click','.activate-clientuser',function(e){
        var vid = $_this(this).data('vid');
        var clientsalescenter = $_this(this).data('clientsalescenter');
        $_this('#salescenterid').val(vid);
        $_this('#status_to_change').val('active');
        $_this('.status-to-change-text').html('activate');
        $_this('.status-change-clientsalescenter').html(clientsalescenter);
    });


    /* Delete utility */
    $_this('body').on('click','.delete-utility',function(e){
        $_this('#Deleteutility').modal();
        var id = $_this(this).data('id');
        $_this('#utilityid').val(id);
        $_this('.status-change-utilityname').html($_this(this).data('utilityname'));
    });

    $_this('body').on('click','.delete-brandcontact',function(e){
      $_this('#Deletbrandcontact').modal();
      var id = $_this(this).data('id');
      $_this('#contactid').val(id);
      $_this('.status-change-cname').html($_this(this).data('cname'));
  });


    /* Find sales agent page  */

   $_this( ".get-salesagents .selectclient " ).on( "change", function( event, ui ) {
         var token = $_this('.get-salesagents input[name="_token"]').val();
         $_this('.select-center-wrapper').html('<i class="fa fa-spin fa-spinner"></i>');
         var client_id = $_this(this).val();
        var check_refrence_number = $_this(this).data('ref');
              $_this.ajax({
              type: "POST",
              url: '/ajaxgetsalescenters',
              data:{'_token':token,'client_id':client_id },
              success: function( response ) {

                $_this('.select-center-wrapper').html(" <select class=\"selectmenu select-box-admin selectcenter\" name=\"salecenter\" id=\"salecenters\"><option value=''>All Sales centers</option>"+response.options+"");
                selectcleintdropdown();
          $_this('.selectcenter').trigger('change');

               }
          });

    });
    $_this( "body" ).on( "change",".get-salesagents .selectcenter", function( event, ui ) {

        var token = $_this('.get-salesagents input[name="_token"]').val();
        $_this('.select-locaion-wrapper').html('<i class="fa fa-spin fa-spinner"></i>');
        console.log($_this('.get-salesagents') );
        var client_id = $_this('.get-salesagents  #selectclient').val();
        var salescenter_id = $_this(this).val();
         var check_refrence_number = $_this(this).data('ref');
             $_this.ajax({
             type: "POST",
             url: '/ajaxgetlocation',
             data:{'_token':token,client_id:client_id,salescenter_id:salescenter_id },
             success: function( response ) {

               $_this('.select-locaion-wrapper').html(" <select class=\"selectmenu select-box-admin selectlocation\" name=\"location\" id=\"location\"><option value=''>All Locations</option>"+response.options+"");
               selectLocationDropdown();
              }
         });
   });
   function selectcleintdropdown(){
    $_this('.selectcenter').select2({
        minimumResultsForSearch: -1
    });
 }
    selectcleintdropdown();

     function selectLocationDropdown(){
        $_this('.selectlocation').select2({
            minimumResultsForSearch: -1
        });
     }


    function change_location(sale_center_id){
       var client_id =  $_this(".get-salesagents .selectclient " ).val()

        var token = $_this('.get-salesagents input[name="_token"]').val();
        $_this('.select-locaion-wrapper').html('<i class="fa fa-spin fa-spinner"></i>');

       var check_refrence_number = $_this(this).data('ref');
             $_this.ajax({
             type: "POST",
             url: '/ajaxgetlocation',
             data:{'_token':token,'client_id':client_id,'salescenter_id':sale_center_id },
             success: function( response ) {

               $_this('.select-locaion-wrapper').html(" <select class=\"selectmenu select-box-admin selectlocation\" name=\"location\" id=\"location\"><option value=''>Select</option>"+response.options+"");
                    $_this('.selectlocation').select2({
                        minimumResultsForSearch: -1
                    });
              }
         });

    }


    /* Admin Dashboard Select date range */


    $_this( ".get-report-data .changedaterange " ).on( "change", function( event, ui ) {
        var token = $_this('.get-report-data input[name="_token"]').val();
        var report_time = $_this(this).val();
        $_this('#dashboard-report-wrapper').html('<div class="text-center"><i class="fa fa-spin fa-spinner fa-4x"></i><br><br><br><br></div>')
        var scrolldiv =   $_this('#dashboard-report-wrapper').offset().top - 50;
        $_this('html,body').animate({scrollTop: scrolldiv}, "slow");
        $_this.ajax({
             type: "POST",
             url: '/ajaxgetdashboardreport',
             data:{'_token':token,'report_time':report_time },
             success: function( response ) {
                $('#dashboard-report-wrapper').hide().html(response).fadeIn();

              }
         });
   });
  /* Client Dashboard */
   $_this( ".get-clientreport-data .changedaterange " ).on( "change", function( event, ui ) {
    var token = $_this('.get-clientreport-data input[name="_token"]').val();
    var report_time = $_this(this).val();
    var reportclientid = $_this('.get-clientreport-data #reportclientid').val();

    $_this('#dashboard-report-wrapper').html('<div class="text-center"><i class="fa fa-spin fa-spinner fa-4x"></i><br><br><br><br></div>')
    $_this.ajax({
         type: "POST",
         url: '/ajaxgetclientdashboardreport',
         data:{'_token':token,'report_time':report_time,'client_id':reportclientid },
         success: function( response ) {
            $('#dashboard-report-wrapper').hide().html(response).fadeIn();

          }
     });
});


 /* Programs list get client utilities */
 $_this( ".getclientutilities #client " ).on( "change", function( event, ui ) {
    var token = $_this('.getclientutilities input[name="_token"]').val();
    $_this('.select-utilities-wrapper').html('<i class="fa fa-spin fa-spinner"></i>');
    var client_id = $_this(this).val();
       $_this.ajax({
          type: "POST",
          url: '/ajaxclientUtilities',
          data:{'_token':token,'client_id':client_id },
          success: function( response ) {
             $_this('.select-utilities-wrapper').html(" <select class=\"utilityselect select-box-admin\" name=\"utility\" id=\"utility\" ><option value=''>Select</option>"+response.options+"");

             $_this( ".select-utilities-wrapper .utilityselect" ).select2({
                 minimumResultsForSearch : -1
             });
          }
     });
});

/* Delete Program */
$_this('body').on('click','.delete-program',function(e){
    $_this('#Deleteprogram').modal();
    var id = $_this(this).data('id');
    $_this('#programid').val(id);
    $_this('.delete-program-name').html($_this(this).data('programname'));
});


/* tele sale ajax */

$_this( "#telesales-search" ).on( "submit", function( event ) {
    event.preventDefault();
    var url  = $_this(this).attr('action');
    $_this('#reference_id_to_update').val($_this('input[name="ref"]',$_this(this)).val());
    $_this('.sale-detail-wrapper').html('<div class="text-center"><i class="fa fa-spin fa-spinner" style="font-size:3em"></i></div>');
    var client_id = $_this(this).val();
       $_this.ajax({
          type: "POST",
          url: url,
          data: $_this(this).serialize() ,
          success: function( response ) {
            $_this('.sale-detail-wrapper').html(response);
          }
     });
});

/* update tele sale */

$_this( "#updatetelesalestatus" ).on( "submit", function( event ) {
    event.preventDefault();
    var url  = $_this(this).attr('action');
    $('.sale-detail-wrapper').html('<div class="text-center"><i class="fa fa-spin fa-spinner" style="font-size:3em"></i></div>');
    var client_id = $_this(this).val();
    $_this.ajax({
          type: "POST",
          url: url,
          data: $_this(this).serialize() ,
          success: function( response ) {

            $_this('.decline-sale-form').hide();
            $_this('.decline-form').hide();
              $_this('.verify-sale').hide();
            $_this('#confirmreview').modal('toggle');

              if(typeof  response.verification_all_done === 'undefined') {
                //$_this('.script_for_confirmation').show();
                $_this('.verify-sale').hide();
                if(response.status == 'success'){

                    $('#reference_id_to_update').val(response.ref);
                    $('.sale-detail-wrapper').append('<h3 class="verification-question-text lead-verification-title">Lead verification</h3>');
                    $('.sale-detail-wrapper').append('<div class="verifications-questions-wrapper"></div>');
                          for(var i=0; i< response.data.length; i++){
                                 var question_html = single_question_with_answer(response.data[i]['question'],i,response.data[i]['positive_ans'],response.data[i]['negative_ans'], response.data[i]['answer_option'], response.data[i]['is_customizable']);
                          $('.verifications-questions-wrapper').append(question_html);
                          }

                          $('.verification-0').addClass('active');
                        var scrolldiv =   $('.verification-0').offset().top - ($(window).height() / 2);
                          $('html,body').animate({scrollTop: scrolldiv}, "slow");
                          $('.lead-verification-title').show();
                          $('.salesagentintro').hide();

                  }else{
                    $('.script_for_confirmation').hide();
                    $('.telesale-verify-status').html('<span class="text-danger"><i class="fa fa-times"></i> Lead not Found</span>');
                  }

              }else{
                        var alertclass= "success";
                        if(response.status == 'error'){
                        var alertclass= "danger";


                        }else{
                        window.leadverify = true;
                        $_this('.sale-detail-wrapper').html('');
                        for(var i=0; i< response.questions.length; i++){

                            var question_html = single_question_closing(response.questions[i]);
                            $_this('.sale-detail-wrapper').append(question_html);
                        }

                        }

                    var message_html = ' <div class="alert alert-'+alertclass+'">'+
                                            '<p>'+response.message+'</p>'+
                                        '</div>';


                    //  $('.sale-detail-wrapper').html(response.url);
                    $_this('.saleupdatenotification').html(message_html);
                    timemessage();
              }



          }
     });
  });

function timemessage(){

    setTimeout(() => {
        $('.alert').hide();
     }, 3000);

}
 /*   Twilio Settings on admin edit profile page  */
 $('body').on('click','.deletetiwilioid',function(){
     $('#DeleteTwilioID').modal('toggle');
     $('.delete_twilio_row').attr('rel',$(this).data('id'));
 });
 $('body').on('click','.delete_twilio_row',function(){
    $('#DeleteTwilioID').modal('toggle');
     $('.setting_'+$(this).attr('rel')).remove();
 });

 /* add new row */

 $('body').on('click','.addnew-twilio-record',function(e){
     e.preventDefault();
     var new_id = Math.floor(Math.random() * 20);
     var selected_workspace_id = $('#workspace_select option:selected').val();
     var selected_workspace_name = $('#workspace_select option:selected').text();
     var twilio_worker_id = $('#twilio_worker_id').val();


     if(selected_workspace_id !="" && twilio_worker_id!=""){
        $('#twilio_worker_id').val('');
        var rows_length = $('.setting_rows').length;
        if(rows_length % 2 == 0){
            var first_last_td_class = "dark_c";
            var second_and_middle_td_class = "grey_c";
          }else{
            var first_last_td_class = "light_c";
           var second_and_middle_td_class = "white_c";
          }
                     $('.twilio-workersid-detail').append('<tr class="setting_'+new_id+' setting_rows">'+
                             '<td class="'+first_last_td_class+'">'+
                             selected_workspace_name+
                                ' <input type="hidden" name="twilio_ids[workspace_id][]" value="'+selected_workspace_id+'">'+
                             '</td>'+
                             '<td class="'+second_and_middle_td_class+'">'+
                                '<input type="hidden"  class="form-control" name="twilio_ids[worker_id][]" value="'+twilio_worker_id+'">' +twilio_worker_id+
                             '</td>' +
                              '<td class="'+first_last_td_class+'">'+
                                '<button class="btn btn-red deletetiwilioid"  type="button" role="button" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Delete Record" data-id="'+new_id+'" id="delete-client-'+new_id+'" >Delete<span class="del"><img src="/images/cancel_w.png"></span> </button>'+
                             '</td>'+
                            '</tr>');
     }
 });


  /* Creadte  new tpv agent add twilio row */
 $('body').on('click','.create-twilio-record',function(e){
    e.preventDefault();
    var selected_workspace_id = $('#workspace_select option:selected').val();
    var selected_workspace_name = $('#workspace_select option:selected').text();
    var twilio_worker_id = $('#twilio_worker_id').val();

    if(selected_workspace_id !="" && twilio_worker_id!=""){
        var new_id = Math.floor(Math.random() * 20);
        $('.appendnewtwilioids').append('<div class="added-twilio-workerid-wrapper remove_'+new_id+'">'+

              '<div class="col-md-6">'+
                 '<input  type="hidden" name="twilio_ids[workspace_id][]" value="'+selected_workspace_id+'">'+
                 '<input  type="text" disabled class="form-control" value="'+selected_workspace_name+'" placeholder="Workspace">'+
              '</div>'+
              '<div class="col-md-6">'+
                 '<input  type="text" required class="form-control" name="twilio_ids[worker_id][]" value="'+twilio_worker_id+'" placeholder="Worker ID">'+
                 '<button class="addnew_workspace remove_create_twilio" data-rel="remove_'+new_id+'"  type="button"><span class="del">  <img src="/images/cancel_sm.png"/></span></button>'+
              '</div>'+

             ' </div>' );
             $('#twilio_worker_id').val('');
    }


 });

 $('body').on('click','.remove_create_twilio',function(e){
    e.preventDefault();
      $('.'+$(this).data('rel')).remove();
 });

 /* Client twilio workspace */
 $('body').on('click','.add-client-workspace',function(e){
    e.preventDefault();
    var new_id = Math.floor(Math.random() * 20);

 $('.append-client-workspace-id').append('<div class=" client_workspace_'+new_id+'">'+
       '<div class="col-xs-12 col-sm-6 col-md-6"><div class="form-group">'+
         '<label for="client_workspace_'+new_id+'"></label> '+
          '  <input id="client_workspace_'+new_id+'" autocomplete="off" type="text" name="workspace_id[]" value=""    required placeholder="Workspace ID" >'+
         '</div></div>'+
       '<div class="col-xs-12 col-sm-6 col-md-6"><div class="form-group"><label for="client_workspaces_'+new_id+'"></label> '+
          '<input  type="text" id="client_workspaces_'+new_id+'" class="form-control inline-block" name="workspace_name[]" value="" placeholder="Workspace Name" required>'+
          '<button class="addnew_workspace remove_client_workspace" data-rel="client_workspace_'+new_id+'"  type="button"><span class="del"><img src="/images/cancel_sm.png"/></span></button>'+
       '</div></div>'+
      '</div>' );
 });

  /* edit twilio workspace */
  $('body').on('click','.add-client-workspace-row',function(e){
    e.preventDefault();
    var new_id = Math.floor(Math.random() * 20);
    var workspace_id = $('#workspace_id').val();
    var workspace_name = $('#workspace_name').val();
    $('#workspace_id').val('');
    $('#workspace_name').val('');
    var addes_rows = $('.added-workspaces').length;
    if(addes_rows % 2 == 0){
        var first_last_td_class = "light_c";
        var second_and_middle_td_class = "white_c";
      }else{
        var first_last_td_class = "dark_c";
        var second_and_middle_td_class = "grey_c";
      }

    if( workspace_name != "" && workspace_name != ""){
        var new_row =  '<tr class="client_workspace_'+new_id+' added-workspaces">'+
        '<td class="'+first_last_td_class+'">'+workspace_id+' <input  type="hidden"  name="workspace_id[]" value="'+workspace_id+'" placeholder="Workspace ID"> </td>'+
        '<td class="'+second_and_middle_td_class+'"> '+workspace_name+' <input  type="hidden"  name="workspace_name[]" value="'+workspace_name+'" placeholder="Workspace Name"> </td>'+
        '<td valign="middle" class="'+first_last_td_class+'"><button class="btn btn-red remove_client_workspace " type="button" data-rel="client_workspace_'+new_id+'" >Delete<span class="del button-del"><img src="/images/cancel_w.png"></span></button></td>'+
      '</tr>';
    }

 $('.append-client-workspace-table').append(new_row );
 });
  /* edit twilio workflow */
  $('body').on('click','.add-client-workflow-row',function(e){
    e.preventDefault();
    var new_id = Number($('.added_workflows').length)+1;
    var workspace_id = $('#Workflow_select_workspace').val();
    var selected_workspace_name = $('#Workflow_select_workspace option:selected').text();
    var workflow_id = $('#workflow_id').val();
    var workflow_name = $('#workflow_name').val();
    $('#workflow_id').val('');
    $('#workflow_name').val('');
    if(new_id % 2 == 0){
        var first_last_td_class = "light_c";
        var second_and_middle_td_class = "white_c";
      }else{
        var first_last_td_class = "dark_c";
        var second_and_middle_td_class = "grey_c";
      }

    if( workspace_name != "" && workspace_name != "" && workflow_id != ""){
        var new_row =  '<tr class="client_workflow_'+new_id+' added_workflows">'+
        '<td class="'+second_and_middle_td_class+'"> <input  type="hidden" name="workflow['+new_id+'][workspace_id]" value="'+workspace_id+'" >'+selected_workspace_name+' </td>'+
        '<td class="'+second_and_middle_td_class+'">'+workflow_id+' <input  type="hidden"  name="workflow['+new_id+'][workflow_id]" value="'+workflow_id+'" placeholder="Workflow ID"> </td>'+
        '<td class="'+second_and_middle_td_class+'">'+workflow_name+' <input  type="hidden" name="workflow['+new_id+'][workflow_name]" value="'+workflow_name+'" placeholder="Workflow Name"> </td>'+
        '<td valign="middle" class="'+first_last_td_class+'"> <button class="btn btn-red remove_client_workspace" type="button"  data-rel="client_workflow_'+new_id+'">Delete<span class="del button-del"><img src="/images/cancel_w.png")></span></button></td>'+
      '</tr>';
    }


 $('.append-client-workflow-table').append(new_row );
 });


 $('body').on('click','.remove_client_workspace',function(e){
    e.preventDefault();
      $('.'+$(this).data('rel')).remove();
 });

 $('body').on('click','.tpvtags .addtag strong',function(e){
    e.preventDefault();
    if (document.activeElement.nodeName == 'TEXTAREA' && document.activeElement.id == 'questiontext') {

        insertAtCaret('questiontext', $(this).text())
    }else{
        $('#questiontext').val( $('#questiontext').val() + " "+ $(this).text());
    }

 });

 $('body').on('click','.positive-tags  strong',function(e){
    e.preventDefault();

       $('#positive_ans').val( $('#positive_ans').val() + " "+ $(this).data('rel'));
 });
 $('body').on('click','.negative-tags  strong',function(e){
    e.preventDefault();

       $('#negative_ans').val( $('#negative_ans').val() + " "+ $(this).data('rel'));
 });




 /* client workspace on change  */

 $_this( ".select_workspace_add_client" ).on( "change", function( event, ui ) {

    $_this('.select_workflow_id').html('<i class="fa fa-spin fa-spinner"></i>');
    var client_id = $_this('#selectclientid').val();
    var workspaceid = $_this(this).val();


         $_this.ajax({
         type: "POST",
         url: ajaxclientworkflow,
         data:{'client_id':client_id,'workspaceid':workspaceid },
         success: function( response ) {

           $_this('.select_workflow_id').html(" <select name=\"workflow_id\" class=\"client_workflow_select\"><option value=''>Select</option>"+response.options+"");
                $_this('.client_workflow_select').select2({
                    minimumResultsForSearch: -1
                });
          }
     });
});

/* Add new question to agent not found script */
$_this('body').on('click','.add-client-agent-not-found-script',function(e){
    e.preventDefault();
    var new_id = Math.floor(Math.random() * 20);

  var element =   $( ".agent_not_found_questions .position" ).last();

  var get_count = $('span',element).html();
 if(typeof get_count == "undefined" ){
     var new_position = 1;
 }else{
    var new_position = Number(get_count) + Number(1);
 }
    var addnewquestion = $('#addnewquestion').val();
    var addnewquestion_language = $('#addnewquestion_language').val();
    var language = $('#addnewquestion_language option:selected').data('rel');


    $_this('#addnewquestion').val('');
    var agentnotfoundaddedquestions = $('.agentnotfound-script-question').length;

    if( addnewquestion != "" && addnewquestion != ""){
        if(agentnotfoundaddedquestions % 2 == 0){
            var first_last_td_class = "dark_c";
            var second_and_middle_td_class = "grey_c";

          }else{
            var first_last_td_class = "light_c";
            var second_and_middle_td_class = "white_c";
          }
        $_this('.no_script_found').remove();
    var new_row =  '<tr class="question_agent_not_found_'+new_id+' agentnotfound-script-question">'+
    '<td class="position '+first_last_td_class+'"><span>'+new_position+'</span><input type="hidden" name="position[]" value="'+new_position+'">  </td>'+
        '<td  class="'+second_and_middle_td_class+'"> <input type="hidden" value="'+addnewquestion_language+'" name="language[]">'+language+' </td>'+
        '<td class="'+second_and_middle_td_class+'"> '+addnewquestion+'<input  type="hidden"   name="agent_not_found_script[]" value="'+addnewquestion+'" placeholder="Question"> </td>'+
        '<td valign="middle" class="'+first_last_td_class+'"> <button class="btn btn-red remove_agent_not_found_question" type="button" data-rel="question_agent_not_found_'+new_id+'" >Delete<span class="del button-del"> <img src="/images/cancel_w.png"></span></button></td>'+
      '</tr>';
    }


    $_this('.agent_not_found_questions').append(new_row );
 });

 $_this('body').on('click','.remove_agent_not_found_question',function(e){
     e.preventDefault();
      var remove_element =  $_this(this).data('rel');
      $_this('.'+remove_element).remove();
 });



$_this( "#sortable" ).sortable({

    stop: function() {
        var actualpositions =   [];

        var sortednumber = [];

        var i = 0;
       $_this('#sortable .question-first-div').each(function(){
           var p = $_this('.current_position',this).data('currentposition');
           console.log(p, sortednumber.indexOf (p) );
            if(sortednumber.indexOf (p)  == -1 )
            {
                sortednumber.push(p);
            }

            actualpositions.push( parseInt( $_this('.new_positions',this).val()) );

           i++;
       });

       sortednumber.sort(function(a, b){
        return parseInt(a)- parseInt(b);
      });
      actualpositions.sort(function(a, b){
        return parseInt(a)- parseInt(b);
      });


       var i = 0;

       $_this('#sortable  .question-first-div').each(function(){


          $_this('.current_position',this).html(sortednumber[i]);
          $_this('.new_positions',this).val($.trim(actualpositions[i]));

        i++;
    });

    var action_url = $_this('#update_position').attr('action');
    $_this.ajax({
        type: "POST",
        url: action_url,
        data:$_this('#update_position').serialize(),
        success: function( response ) {


         }
    });

    }
});

$( "#sortable li" ).disableSelection();

$_this('body').on('click','.validate_fields',function(){
var form_id = $_this('#selectform4mapping').val();

var parent_element = $_this('#selectform4mapping').closest( 'div').find('.validation-error');
if(form_id ==''){
  $_this('strong',parent_element).html('Please select form.');
  return false;
}else{
    $_this('strong',parent_element).html('');
}
var textarea_element = $_this('#texttoaddforfields').closest( 'div').find('.validation-error');
 var columns_header = $_this('#texttoaddforfields').val();
 if(columns_header ==''){
   $_this('strong',textarea_element).html('Please select form.');
   return false;
 }else{
     $_this('strong',textarea_element).html('');
 }

  $_this('.content4maping').html('<div class="text-center"><i class="fa fa-spin fa-spinner" style="font-size:3em"></i></div>');
  $_this.ajax({
      type: "POST",
      url: mapwithform,
      data: { fields : columns_header, fid : form_id},
      success: function( response ) {
             $_this('.content4maping').html(response);
             $_this('.options_values_for_compliance').select2({
                 minimumResultsForSearch: 1
             });
             $_this('.validate_fields').hide();
             $_this('.savefield').show();


       }
  });

});
$_this('body').on('click','.remove_compliance_option',function(){
    $_this('.'+$_this(this).data('rel')).remove();
});
$_this( "#optionssortabletable" ).sortable();

$_this('body').on('click','.add_compliance_option',function(){
  var addnewoption =    $_this('#addnewoption').val();
  var newoptiontoadd =    $_this('#newoptiontoadd').val();
 var get_custom_value_for =    $_this('.get_custom_value_for').val();

  var check_custom_allow = $_this('.checktoallow_custom').prop('checked');

  if(check_custom_allow == true){
    var column_value = get_custom_value_for;
    var allowed_custom = 1;
  }else{
    var column_value = newoptiontoadd;
    var allowed_custom = "";
  }

if(addnewoption !="" ){
  $_this('#addnewoption').val('');
 $_this('.get_custom_value_for').val('');
  var new_element = $_this('.options_row').length + 1;
    var add_element = '<li class="dd-item options_row options_row_'+new_element+'">'+
         '<div class="dd-handle">'+
         '<div  class="valign-middle compliance-first-div">' +
           addnewoption+
          ' <input type="hidden" value="'+addnewoption+'" name="header_column[header][]">'+
         '</div>'+
        ' <div class="valign-middle compliance-second-div">'+
           column_value+
           '<input type="hidden" value="'+newoptiontoadd+'" name="header_column[values][]">'+
           '<input type="hidden" value="'+allowed_custom+'" name="header_column[allow_custom][]">'+
           '<input type="hidden" value="'+get_custom_value_for+'" name="header_column[custom_value][]">'+
         '</div>'+
        '   <div class="valign-middle compliance-third-div">'+
          ' <a href="javascript:void(0);" class="remove_compliance_option " data-rel="options_row_'+new_element+'">'+
          ' <img src="/images/cancel.png"></i>'+
           '</a>'+
           '</div>'+
      '</div>'+
  '</li>';
  $_this('#optionssortabletable').append(add_element);
}
});
$_this('body').on('click','.checkforcustomvalue',function(){
  var ref_number = $_this(this).data('ref');
  if($_this(this).prop('checked') == true){
    $_this('.select2-container.select_form_options_'+ref_number).hide();
    $_this('.input_form_options_'+ref_number).show();
    $_this('.allow_custom_check_'+ref_number).val(1);
  }else{
    $_this('.select2-container.select_form_options_'+ref_number).show();
    $_this('.input_form_options_'+ref_number).hide();
    $_this('.allow_custom_check_'+ref_number).val(0);
  }
});

$_this('input.checktoallow_custom').on('click', function(event){
   var check_custom_allow = $_this('.checktoallow_custom').prop('checked');
     if(check_custom_allow == true){
       $_this('.get_custom_value_for').show();
       $_this('.select2-container.select_option_for_compliance').hide();
     }else{
       $_this('.get_custom_value_for').hide();
       $_this('.get_custom_value_for').val('');
       $_this('.select2-container.select_option_for_compliance').show();
     }


});

$_this('body').on('click','.export-all-templates',function(){

   location.href = exportall_compliance+"?date_start="+$_this('#date_start').val();
});

$_this('body').on('change','.selectclientcompliance_report', function(){
    $_this('.select_utility_report-wrapper').html('<i class="fa fa-spin fa-spinner"></i>');
        $_this.ajax({
            type: "POST",
            url: compliance_report_getutilities,
            data: {client_id : $_this(this).val()},
            success: function( response ) {
            if(response.status == 'success'){
                $_this('.select_utility_report-wrapper').html('<select class="selectmenu form-control select_utility_report" name="utility" multiple>'+
                '<option value="">Select</option>'+
                 response.options +
                 '</select>'
              );
            }else{
                $_this('.select_utility_report-wrapper').html('<select class="selectmenu form-control select_utility_report" name="utility">'+
              '<option value="">Select</option>'+
               '</select>');
            }

              $_this('.select_utility_report').select2({                 minimumResultsForSearch : 1             });;
           },error: function(){
            $_this('.select_utility_report-wrapper').html('<select class="selectmenu form-control select_utility_report" name="utility">'+
            '<option value="">Select</option>'+
             '</select>');
           }
        });
});

$_this('body').on('change','.selectclientlocations_report', function(){
    $_this('.updatelocaton_according_to_client').html('<i class="fa fa-spin fa-spinner"></i>');
        $_this.ajax({
            type: "POST",
            url: '/ajax/ajaxgetlocationbyclient',
            data: {client_id : $_this(this).val()},
            success: function( response ) {
            if(response.status == 'success'){
                $_this('.updatelocaton_according_to_client').html('<select name="locationid" class="selectmenu location_select">'+
                '<option value="">All locations</option>'+
                 response.options +
                 '</select>'
              );
            }else{
                $_this('.updatelocaton_according_to_client').html('<select name="locationid" class="selectmenu location_select">'+
              '<option value="">All locations</option>'+
               '</select>');
            }
            getProgram();
            getsalesagentforprogram();
              $_this('.location_select').select2({
                  minimumResultsForSearch : 1
              });


           },error: function(){
            $_this('.updatelocaton_according_to_client').html('<select name="locationid" class="selectmenu location_select">'+
            '<option value="">All locations</option>'+
             '</select>');
             getProgram();
             getsalesagentforprogram();
             $_this('.location_select').select2({
                minimumResultsForSearch : 1
            });
           }
        });
});
$_this('body').on('change','.vendorstatus', function(){
    $_this('.update_client_by_location').html('<i class="fa fa-spin fa-spinner"></i>');
        $_this.ajax({
            type: "POST",
            url: '/ajax/getclientsbystatus',
            data: {checkstatus : $_this(this).val()},
            success: function( response ) {
            if(response.status == 'success'){
                $_this('.update_client_by_location').html('<select class="selectsearch form-control selectclientlocations_report" id="salesvendor" name="client">'+
                '<option value="">All Vendors</option>'+
                 response.options +
                 '</select>'
              );
            }else{
                $_this('.update_client_by_location').html('<select class="selectsearch form-control selectclientlocations_report" id="salesvendor" name="client">'+
              '<option value="">All Vendors</option>'+
               '</select>');
            }

              $_this('.selectclientlocations_report').select2({                 minimumResultsForSearch : 1             });;
              $_this('.selectclientlocations_report').trigger('change');
              getProgram();
           },error: function(){
            $_this('.update_client_by_location').html('<select class="selectsearch form-control selectclientlocations_report" id="salesvendor" name="client">'+
            '<option value="">All Vendors</option>'+
             '</select>');
             $_this('.selectclientlocations_report').select2({                 minimumResultsForSearch : 1             });;
              $_this('.selectclientlocations_report').trigger('change');
              getProgram();
           }
        });
});
$_this('body').on('change','.userstatus, .location_select ', function(){

  getsalesagentforprogram();
});



function getProgram(){

    $_this('.update_program_by_client').html('<i class="fa fa-spin fa-spinner"></i>');
    $_this.ajax({
        type: "POST",
        url: '/ajax/getprogramsforreport',
        data: {
            vendorstatus : $_this('#vendorstatus').val(),
            client : $_this('#salesvendor').val(),
        },
        success: function( response ) {
        if(response.status == 'success'){
            $_this('.update_program_by_client').html('<select class="selectsearch form-control vendor_programs" id="vendor_programs" name="program">'+
            '<option value="">All Programs</option>'+
             response.options +
             '</select>'
          );
        }else{
            $_this('.update_program_by_client').html('<select class="selectsearch form-control vendor_programs" id="vendor_programs" name="program">'+
          '<option value="">All Programs</option>'+
           '</select>');
        }

          $_this('.vendor_programs').select2({                 minimumResultsForSearch : 1             });;
          //$_this('.selectclientlocations_report').trigger('change');
       },error: function(){
        $_this('.update_program_by_client').html('<select class="selectsearch form-control vendor_programs" id="vendor_programs" name="program">'+
        '<option value="">All Programs</option>'+
         '</select>');
         $_this('.vendor_programs').select2({                 minimumResultsForSearch : 1             });;
         // $_this('.selectclientlocations_report').trigger('change');
       }
    });
}


function getsalesagentforprogram(){

    $_this('.updatsalesagents').html('<i class="fa fa-spin fa-spinner"></i>');
    $_this.ajax({
        type: "POST",
        url: '/ajax/getsalesagentforreport',
        data: {
            vendorstatus : $_this('#vendorstatus').val(),
            client : $_this('#salesvendor').val(),
            userstatus : $_this('#userstatus').val(),
            locationid : $_this('#location_select').val(),
        },
        success: function( response ) {
        if(response.status == 'success'){
            $_this('.updatsalesagents').html(' <select name="salesagent" class="selectsearch salesagentfilter" id="salesbyagent">'+
            '<option value="">All sales agents</option>'+
             response.options +
             '</select>'
          );
        }else{
            $_this('.updatsalesagents').html(' <select name="salesagent" class="selectsearch salesagentfilter" id="salesbyagent">'+
          '<option value="">All sales agents</option>'+
           '</select>');
        }

          $_this('.salesagentfilter').select2({
            minimumResultsForSearch: 1
          });
          //$_this('.selectclientlocations_report').trigger('change');
       },error: function(){
        $_this('.updatsalesagents').html(' <select name="salesagent" class="selectsearch salesagentfilter" id="salesbyagent">'+
        '<option value="">All sales agents</option>'+
         '</select>');
         $_this('.salesagentfilter').select2({
            minimumResultsForSearch: 1
          });
         // $_this('.selectclientlocations_report').trigger('change');
       }
    });
}


window.table_obj = "";
$_this( document ).ready( function( $ ) {
    $table1 = $_this( '.template_lists' );

    // Initialize DataTable
    window.table_obj = $table1.DataTable( {
        "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "bStateSave": true,
        "aoColumnDefs": [
            { 'bSortable': false, 'aTargets': [ 0] }
         ]
    });

    // Initalize Select Dropdown after DataTables is created
    $table1.closest( '.dataTables_wrapper' ).find( 'select' ).select2( {
        minimumResultsForSearch: -1
    });
} );

$_this('body').on('change','#batch_export_date',function(){
     $_this('#export_all_date_range').val($_this(this).val());
     if($_this('#batch_export_date').val() !=""){
        compliance_templates();
     }
});
$_this('body').on('change','.select_utility_report', function(){
    $_this('#select_utility_report_hidden').val($_this(this).val());
    compliance_templates();

});

function compliance_templates(){
    $_this('#showutilities_templates').show();
    $_this('.show_loading').html('<i class="fa fa-spin fa-spinner"></i>');
      $_this.ajax({
            type: "POST",
            url: "/ajax/compliancetemplates",
            data: {client_id: $_this('#selectclientcompliance_report').val(), utility_id : $_this('#select_utility_report_hidden').val(), daterange : $_this('#batch_export_date').val()},
            success: function( response ) {
                $_this('.show_loading').html('');
                window.table_obj.rows().remove().draw();
            if(response.status == 'success'){
                if(response.options.length > 0){
                    $_this('.all-export-zip').show();
                }else{
                    $_this('.all-export-zip').hide();
                }
                $_this.each(response.options , function (index, value){
                   var checkbox = "<input type='checkbox' class='selected_templates' name='selected_templates[]' value='"+value.id+"'  > <input type='hidden' name='all_templates[]' value='"+value.id+"'  >";
                   var view = "<a  href='"+compliancebatchreport +"?ctid="+value.id+"&daterange="+$_this('#batch_export_date').val()+"' target='_blank'><i class='fa fa-eye'></i></a>";
                   window.table_obj.row.add( [
                    checkbox,
                    value.name,
                    view

               ] ).draw( false );
              });


            }else{

            $_this('.new_elements_for_templates_report').html("<tr colspan='3' align='center'> No Record Found </tr>");

            }


           },error: function(){
            $_this('.show_loading').html('');
            $_this('.new_elements_for_templates_report').html("<tr colspan='3' align='center'> No Record Found </tr>");

           }
        });
}
$_this('input.select_all_items').on('click', function(){

     if($_this('.select_all_items').prop('checked') == true){

         $_this('.selected_templates').each(function(){

            $_this(this).prop('checked', true);
        });

     }else{

        $_this('.selected_templates').each(function(){
            $_this(this).prop('checked', false);
        });
     }
});
$_this('.name-wrapper.tpvtags .addtag ').draggable({
    cancel: "a.ui-icon", // clicking an icon won't initiate dragging
    //revert: "invalid", // when not dropped, the item will revert back to its initial position
    revert: true, // bounce back when dropped
    helper: "clone", // create "copy" with original properties, but not a true clone
    cursor: "move"
    , revertDuration: 0 // immediate snap
});


$_this('#questiontext, #answer').droppable({
    accept: ".name-wrapper.tpvtags .addtag ",
    activeClass: "ui-state-highlight",
    drop: function( event, ui ) {

        // clone item to retain in original "list"
        var $item = ui.draggable.clone();


        $_this(this).addClass('has-drop').val(this.value + $item[0].innerText);


    }
});

  /* edit twilio workflow */
  $('body').on('click','.add-client-workflow-number',function(e){
    e.preventDefault();
    var new_id = Number($('.added_phonenumbers').length)+1;
    var workflow_id = $('#Workflow_select_id').val();
    var selected_workflow_name = $('#Workflow_select_id option:selected').text();

    var phone_number = $('#phone_number').val();

    $('#phone_number').val('');

    if(new_id % 2 == 0){
        var first_last_td_class = "dark_c";
        var second_and_middle_td_class = "grey_c";

      }else{
        var first_last_td_class = "light_c";
        var second_and_middle_td_class = "white_c";
      }
    if( workspace_name != "" && workspace_name != "" && workflow_id != ""){
        var new_row =  '<tr class="client_phonenumber_'+new_id+' added_phonenumbers">'+
        '<td style="vertical-align:middle" class="'+first_last_td_class+'"> <input  type="hidden" name="phonenumbers['+new_id+'][workflowid]" value="'+workflow_id+'" >'+selected_workflow_name+' </td>'+
        '<td class="'+second_and_middle_td_class+'"> '+phone_number+' <input  type="hidden"   name="phonenumbers['+new_id+'][phonenumber]" value="'+phone_number+'" placeholder="Phone Number"> </td>'+
        '<td valign="middle" class="'+first_last_td_class+'"><button class="btn btn-red remove_client_workspace"  data-rel="client_phonenumber_'+new_id+'" >Delete<span class="del"><img src="/images/cancel_w.png"></span> </button></td>'+
      '</tr>';
    }


 $('.append-client-phonenumber-table').append(new_row );
 });

 $('body').on('click','.create-lead-question-text [name="fields[Is the billing address the same as the service address]"]',function(){

    if($(this).val() == 'Yes'){
       var value = $('[name="fields[What is the service address?]"]').val();
       $('[name="fields[Billing Address]"]').val(value);
    }else{
       $('[name="fields[Billing Address]"]').val('');
    }



   });

   $('.item').draggable({
        cancel: "a.ui-icon", // clicking an icon won't initiate dragging
        //revert: "invalid", // when not dropped, the item will revert back to its initial position
        revert: true, // bounce back when dropped
        helper: "clone", // create "copy" with original properties, but not a true clone
        cursor: "move"
        , revertDuration: 0 // immediate snap
    });

    var $container
    $('.container').droppable({
        accept: "#items .item",
        activeClass: "ui-state-highlight",
        drop: function( event, ui ) {
            // clone item to retain in original "list"
            var $item = ui.draggable.clone();

            $(this).addClass('has-drop').html($item);

        }
    });


});
function insertAtCaret(areaId, text) {
    var txtarea = document.getElementById(areaId);
    if (!txtarea) {
      return;
    }

    var scrollPos = txtarea.scrollTop;
    var strPos = 0;
    var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
      "ff" : (document.selection ? "ie" : false));
    if (br == "ie") {
      txtarea.focus();
      var range = document.selection.createRange();
      range.moveStart('character', -txtarea.value.length);
      strPos = range.text.length;
    } else if (br == "ff") {
      strPos = txtarea.selectionStart;
    }

    var front = (txtarea.value).substring(0, strPos);
    var back = (txtarea.value).substring(strPos, txtarea.value.length);
    txtarea.value = front + text + back;
    strPos = strPos + text.length;
    if (br == "ie") {
      txtarea.focus();
      var ieRange = document.selection.createRange();
      ieRange.moveStart('character', -txtarea.value.length);
      ieRange.moveStart('character', strPos);
      ieRange.moveEnd('character', 0);
      ieRange.select();
    } else if (br == "ff") {
      txtarea.selectionStart = strPos;
      txtarea.selectionEnd = strPos;
      txtarea.focus();
    }

    txtarea.scrollTop = scrollPos;
  }
