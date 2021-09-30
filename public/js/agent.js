$ = jQuery;
window.disconnected_by_agent = false;
window.disconnectedIfError = false;
window.call_end_by_customer = false;
window.lead_verified = false;
window.decline_lead_success = false;
window.identity_question_decline = false;
window.decline_reason_stored = false;
window.leadVerifiedStored = false;
window.ansArray = {};

// window.alertSound = new Audio(window.incomingSound);
// alertSound.loop = true;

function registerTaskRouterCallbacks(worker, activitySids) {
  // setTimeout(function () {

    //Worker on ready function
    worker.on('ready', function (worker) {
        
        $("#status-to-change").html("");
        $("#status-to-change-not-ready").html("");
        var ordered = {};
        var notReadyActivities = {};
        Object.keys(activitySids).sort().forEach(function (key) {
            if (jQuery.inArray(key, ['Available', 'Unavailable', 'WrapUp']) == -1) {
                notReadyActivities[key] = activitySids[key];
            }
            ordered[key] = activitySids[key];
        });

        //Design Agent Activity Dropdown
        agentActivitiesComponent("status-to-change", ordered, "status-select",["Unavailable", "WrapUp"]);

        // Design Not ready button Activity Dropdown
        agentActivitiesComponent("status-to-change-not-ready", notReadyActivities, "status-select-not-ready", []);

        //change button stuff on click
        $('#status-select li').click(function () {
            var img = $(this).find('img').attr("src");
            var value = $(this).find('img').attr('value');
            var text = this.innerText;
            var item = '<li><img src="' + img + '" alt="" /><span>' + text + '</span></li>';
            $('.btn-select').html(item);

            $('.btn-select').attr('value', value);
            $(".status-block").slideUp();

            for (var j = 0; j < combined_all_worker_activites.length; j++) {
                combined_all_worker_activites[j]['worker'].update("ActivitySid", combined_all_worker_activites[j]['activity'][text]);
            }
        });

        //Not ready activity dropdown click event 
        $('#status-select-not-ready li').click(function () {
            var img = $(this).find('img').attr("src");
            var value = $(this).find('img').attr('value');
            var text = this.innerText;
            completetask($("#complete-task-button").data('rel'));
            unblockanchor();
            $('.new_telesale_reference').html('');
            $('.agent-detail-main').hide('');
            $('.online-agent-name').html('');
            $('.agent-detail-wrapper').hide();
            $('.assigned_workspace_nav li').removeClass('active');
            $('.assigned_workspace_nav').show();
            $('.active-client-call').hide();
            $('#user-status-box  .chat').hide();
            $('.online-status').removeAttr('disabled');
            $('#complete-task-row').addClass('hidden');
            $('.script-important-buttons').hide();
            $('.decline-sale-form').hide();
            $('.decline-form').hide();
            $('.waiting-for-call').show();
            $('.call_duration').html("00:00:00");
            $('#decline-sale-form')[0].reset();
            $('#verify_agent_lead')[0].reset();

            $(".call_hangup_or_dropped").hide();
            $('#verifylead').hide();
            hideVerificationSection();
            $('.client-verify-status').hide();
            $('.telesale-verify-status').hide();
            $('.agent-verify-status').hide();
            $('.verification-complete-block').hide();


            $('.new-status-box .status-outer .btn-select').removeAttr('disabled');
            $('#helpDeskSupportBtn').removeAttr('disabled');

            $(".dispositions-outer").html("");

            seconds = 0;
            minutes = 0;
            hours = 0;

            for (var j = 0; j < combined_all_worker_activites.length; j++) {
                combined_all_worker_activites[j]['worker'].update("ActivitySid", combined_all_worker_activites[j]['activity'][text]);
            }
            $(".status-select-not-ready").hide();
        });

        $('#user-status-box').show();

        // try {
        //   agentActivityChanged(worker.activityName);
        //   worker.update("ActivitySid", activitySids["Available"]);
        //   if(worker["activitySid"] == activitySids["Available"]) {
        //     onActivityUpdate(worker);
        //   }
        // } catch (e) {
        //   console.error(e);
        // }

        let myWorker = worker;
        let MyActivitySids = activitySids;
        $('.ReadyBtn').on('click',function(){
            
            $('.waiting-for-call-status').html('Please wait for a call');
            // var ordered = {};
            // var notReadyActivities = {};
            // Object.keys(activitySids).sort().forEach(function (key) {
            //     if (jQuery.inArray(key, ['Available', 'Unavailable', 'WrapUp']) == -1) {
            //         notReadyActivities[key] = activitySids[key];
            //     }
            //     ordered[key] = activitySids[key];
            // });
    
            // //Design Agent Activity Dropdown
            // agentActivitiesComponent("status-to-change", ordered, "status-select",["Unavailable", "WrapUp"]);
    
            // // Design Not ready button Activity Dropdown
            // agentActivitiesComponent("status-to-change-not-ready", notReadyActivities, "status-select-not-ready", []);

            console.log('Ready BTN clicked');
            try {
                agentActivityChanged(myWorker.activityName);
                myWorker.update("ActivitySid", MyActivitySids["Available"]);
                if(myWorker["activitySid"] == MyActivitySids["Available"]) {
                  onActivityUpdate(myWorker);
                }
              } catch (e) {
                console.error(e);
              }
        });

        $('.go-offine').removeClass('hidden');
        $('.go-online').addClass('hidden');
        $('.online-agent-name').removeClass('offline wrapup').addClass('online');
        logger("Successfully registered as: " + worker.friendlyName)
        logger("Current activity is: " + worker.activityName);
    });

    //Dropdown design for agent activity
    function agentActivitiesComponent(sourceElement, orderedActivities, destinationElement, hideActivities) {
        console.log(sourceElement);
        console.log(orderedActivities);
        for (var key in orderedActivities) {
            if (orderedActivities.hasOwnProperty(key)) {
                if (key == "Available") {
                    var activityImg = 'https://' + window.location.host + '/images/1.png';
                } else {
                    var activityImg = 'https://' + window.location.host + '/images/6.png';
                }

                if (jQuery.inArray(key, hideActivities) == -1) {
                    $("#"+sourceElement).append($("<option></option>").attr('class', 'test').attr('value', orderedActivities[key].toString()).text(key).attr('data-thumbnail', activityImg));
                } else {
                    $("#"+sourceElement).append($("<option></option>").attr('class', 'test').attr('value', orderedActivities[key].toString()).text(key).attr('data-thumbnail', activityImg)).css('display', 'none');
                }
            }
        }

        //Append html design to activity dropdown
        var activityArray = [];
        $("#"+ sourceElement  + " option").each(function () {
            var img = $(this).attr("data-thumbnail");
            var text = this.innerText;
            var optVal = $(this).val();
            if ((hideActivities.length > 0 && jQuery.inArray(text, hideActivities) == -1) || hideActivities.length == 0) {
                var item = '<li><img src="' + img + '" alt="" value="' + optVal + '"/><span>' + text + '</span></li>';
                activityArray.push(item);
            } else {
                var item = '<li style="display: none;"><img src="' + img + '" alt="" value="' + optVal + '"/><span>' + text + '</span></li>';
                activityArray.push(item);
            }
        })

        $("#" + destinationElement).html(activityArray);
    }

    //Agent panel home page timer intialize
    var activityUpdateTimer = new easytimer.Timer();

    worker.on('activity.update', onActivityUpdate);

    //Function call when tpv agent (twilio worker) change his activity
    function onActivityUpdate(worker) {
        $(".status-block").slideUp();
        $(".status-select-not-ready").slideUp();
        console.log("Activity updated to: " + worker.activityName);
        $('.script_for_confirmation').hide();
        var customerHangupData = $('#call_customer_hangs_up').val();
        if(customerHangupData == '') {
            $('.sale-detail-wrapper-Qus').html('');
        }

        let activityName = worker.activityName;

        activityUpdateTimer.stop();
        activityUpdateTimer = new easytimer.Timer();
        activityUpdateTimer.addEventListener('secondsUpdated', function (e) {
            updateActivityDetails(activityName,activityUpdateTimer);
        });
        updateActivityDetails(activityName,activityUpdateTimer);
        activityUpdateTimer.start();

        if (worker.activityName == 'Offline') {
            $('.go-online').removeClass('hidden');
            $('.go-offine').addClass('hidden');
            //$('.waiting-for-call .waiting-for-call-status').html('You are currently offline');
            $('.online-status').removeAttr('disabled');
            $('.online-agent-name').removeClass('online wrapup').addClass('offline');
            $('.available-call-time').show();
        }

        if (worker.activityName == 'Available') {
            $('.online-status').removeAttr('disabled');
            $('.go-offine').removeClass('hidden');
            $('.go-online').addClass('hidden');
            // $('.waiting-for-call-status').hide();
            // $('.waiting-for-call .waiting-for-call-status').html('Please wait for a call');
            $('.online-agent-name').removeClass('offline wrapup').addClass('online');
            $('#user-status-box  .chat').hide();
        }
        if (worker.activityName == 'WrapUp' || worker.activityName == 'Unavailable' || worker.activityName == 'Break') {
            $('.online-status').attr('disabled', true);
            //$(".sale-detail-wrapper-Qus").hide();
            //$('.waiting-for-call .waiting-for-call-status').html('You are currently '+worker.activityName.toLowerCase());
        }

        if (worker.activityName == 'WrapUp' || worker.activityName == 'Unavailable') {
            console.log('new activity log2');
            console.log('worker.activityName' + worker.activityName);
            console.log('worker.activityName test' + $('.dropdown-menu li a').length);
            // $('.dropdown-toggle').attr('disabled','disabled');
            $('.dropdown-profile-data').removeAttr('data-toggle');
            $('.dropdown-menu a').css('pointer-events','none');

        }else{
          console.log('new activity log else2');
          console.log('worker.activityName' + worker.activityName);
          // $('.dropdown-toggle').removeAttr('disabled');
          $('.dropdown-profile-data').attr('data-toggle', 'modal');
          $('.dropdown-menu a').css('pointer-events','auto');
        }

        var selImg = $("li:contains('" + worker.activityName + "')").find('img').attr("src");
        var selText = worker.activityName;
        var selItem = '<li><img src="' + selImg + '" alt="" /><span>' + selText + '</span></li>';
        $('.btn-select').html(selItem);
        agentActivityChanged(worker.activityName);
        logger("Worker activity changed to: " + worker.activityName);
    }

    //Function call when reservation creates
    worker.on("reservation.created", function (reservation) {

        //Set incoming tune while rings
        if (reservation.task.taskChannelUniqueName === 'voice') {
            console.log('Reservation created and playing sound');
            $('#incomingAudio').trigger('play');
            // alertSound.play();
        };
        
        //Check for created task type & call functions for each condition to display info on popup box
        if (reservation.task.attributes.type && reservation.task.attributes.type == selfVerifiedCallbackType) {
            selfVerifiedCallbackCreation(reservation.task.attributes);
        } else if (reservation.task.attributes.type && reservation.task.attributes.type == "outbound") {
          outboundResevationCreation(reservation.task.attributes);
        }else if (reservation.task.attributes.type && reservation.task.attributes.type == OUTBOUND_DISCONNECT) {
          outboundDisconnectResevationCreation(reservation.task.attributes);
        } else {
          inboundResevationCreation();
        }
        window.reservation_id = reservation.sid;
        logger("-----");
        logger("You have been reserved to handle a call!");
        logger("Call from: " + reservation.task.attributes.from);
        $('.assigned_workspace_nav li').each(function () {
            if ($(this).text().trim().toLowerCase() == htmlDecode(workspace_name[reservation.task.workflowSid]).trim().toLowerCase()) {
                $(this).addClass('active');
            }
        });
        $('.active-client-call').show();
        $('.incoming_call_number').text();
        $('.incoming_call_number').text(phonenumber_format(reservation.task.attributes.from));
        $('.incoming_client_name').text();
        $('.incoming_client_name').text(htmlDecode(workspace_name[reservation.task.workflowSid]));
        $('.call_at_number').text();
        $('.call_at_number').text(phonenumber_format(reservation.task.attributes.to));
        $('#incall').modal('show');
        $('#agent_client_id').val(workspace_client[reservation.task.workflowSid]);
        $('.call_from_data').html("<h2> Call from: " + phonenumber_format(reservation.task.attributes.from) + " </h2>");
        $('.call_from_data').append("<h2> Call At: " + phonenumber_format(reservation.task.attributes.to) + " </h2>");
        $('.online-agent-name').html(htmlDecode(workspace_name[reservation.task.workflowSid]));
        logger("Selected language: " + reservation.task.attributes.selected_language);
        logger("-----");
    });

    var activeCallTimer = new easytimer.Timer();
    
    //Function call when TPV agent accept call
    worker.on("reservation.accepted", function (reservation) {
        console.log("Reservation accepted: ", reservation);
        window.activeworkercall = worker;
        window.activeactivitySids = activitySids;
        window.current_workspaceID = reservation.workspaceSid;
        window.current_workflowID = reservation.task.workflowSid;
        window.current_language = reservation.task.attributes.selected_language;
        window.disconnected_by_agent = false;
        window.leadverify = false;
        window.selected_script = reservation.task.attributes.selected_script;

        console.log(reservation.task.attributes);
        window.current_task_id = reservation.taskSid;
        console.log('--------------------------------');
        console.log(current_task_id);

        //Check for task type retrieve its related script
        if (reservation.task.attributes.type && reservation.task.attributes.type == selfVerifiedCallbackType) {
            console.log("Self Verified leads callback");
            window.call_type = selfVerifiedCallbackType;
            window.lead_id = reservation.task.attributes.lead_id;
            $('.new-status-box .status-outer .btn-select').attr('disabled', true);
            $('#helpDeskSupportBtn').attr('disabled', true);
            getQuestions(selfVerifiedCallbackType, window.lead_id);
            $('.dropdown-profile-data').attr('data-toggle', '');
        }
        else if (reservation.task.attributes.type && reservation.task.attributes.type == "outbound" || reservation.task.attributes.type == OUTBOUND_DISCONNECT) {
          console.log("outbound call");
          window.call_type = reservation.task.attributes.type;
          window.lead_id = reservation.task.attributes.lead_id;
          $('.new-status-box .status-outer .btn-select').attr('disabled', true);
          $('#helpDeskSupportBtn').attr('disabled', true);
          window.selected_script = "customer_call_in_verification";
          getCustomerformScripts(reservation.workspaceSid, reservation.task.workflowSid, reservation.task.attributes.selected_language, "customer_call_in_verification");
          $('.dropdown-profile-data').attr('data-toggle', '');
        } else {
          console.log("Inbound call");
          window.call_type = "inbound";
          window.lead_id = "";
          if (reservation.task.attributes.to != null) {
              $.ajax({
                  type: "get",
                  url: getTwillioNumber,
                  data: {
                      'to': reservation.task.attributes.to
                  },
                  success: function(res) {
                      console.log('-----TwillioData',res.data);
                      if (res.status == 'success') {
                          $('.new-status-box .status-outer .btn-select').attr('disabled', true);
                          $('#helpDeskSupportBtn').attr('disabled', true);
                          if(res.data.type === "customer_call_in_verification"){
                              console.log("-----------Customer");
                              window.selected_script = "customer_call_in_verification";
                              getCustomerformScripts(reservation.workspaceSid, reservation.task.workflowSid, reservation.task.attributes.selected_language, res.data.type);
                          }else{
                              window.selected_script = "lead_verification";
                              console.log("-----------Lead");
                              getClientformScripts(reservation.workspaceSid, reservation.task.workflowSid, reservation.task.attributes.selected_language);
                              getClientAgents(reservation.workspaceSid);
                          }
                          $('.dropdown-profile-data').attr('data-toggle', '');
                      } else {
                          console.log(res.message);
                      }
                  },
                  error: function(err) {
                      console.log(err.message);
                      return "false";
                  }
              });
          } else {
              console.log("-------", "Error to get to number");
              return false;
          }
      }

        getAgentNotFoundScript(reservation.workspaceSid, reservation.task.workflowSid, reservation.task.attributes.selected_language);
        getLeadNotFoundScript(reservation.workspaceSid, reservation.task.workflowSid, reservation.task.attributes.selected_language);

        $('.waiting-for-call').hide();

        $("a").each(function () {
            if ($(this).attr('href') != "javascript:;") {
                $(this).attr("rel", $(this).attr("href"));
            }
            if ($(this).attr('onclick') != "javascript:;") {
                $(this).attr("clickref", $(this).attr("onclick"));
            }
            $(this).attr("href", "javascript:;");
            $(this).attr("onclick", "javascript:;");
        });
        //$('.script_for_confirmation').show();
        $('.script-important-buttons').show();
        $('.create-telesale').hide();

        logger("Reservation " + reservation.sid + " accepted!");

        for (var j = 0; j < combined_all_worker_activites.length; j++) {
            combined_all_worker_activites[j]['worker'].update("ActivitySid", combined_all_worker_activites[j]['activity']["Unavailable"]);
        }
        $('.call_duration').show();

        activeCallTimer.stop();
        activeCallTimer = new easytimer.Timer();
        activeCallTimer.addEventListener('secondsUpdated', function (e) {
            $('.call_duration').html(activeCallTimer.getTimeValues().toString());
        });
        $('.call_duration').html(activeCallTimer.getTimeValues().toString());
        activeCallTimer.start();

    });

    //Function call when TPV agent rejects reservation
    worker.on("reservation.rejected", function (reservation) {
        logger("Reservation " + reservation.sid + " rejected!");
    });

    //Function call when reservation timeout
    worker.on("reservation.timeout", function (reservation) {
        $('#incall').modal('hide');
      logger("Reservation " + reservation.sid + " timed out!");
    });

    //Function call when reservation cancelled
    worker.on("reservation.canceled", function (reservation) {
        //console.log('-----',reservation );
        $('.salesagentintro').hide();
        $('.mute-call').addClass('hidden');
        $('.unmute-call').removeClass('hidden');
        $('.online-agent-name').html('');
        $('.assigned_workspace_nav li').removeClass('active');
        $('#incall').modal('hide');

        Twilio.Device.activeConnection().reject();
        unblockanchor();
        $('.active-client-call').hide();
        logger("Reservation " + reservation.sid + " canceled!");
    });

    // Pause the ring once reservation status is updated
    //Function call when reservation creates
    function pauseAlertSoundOnReservationStatusChanged(reservation) {
        $('#incomingAudio').trigger('pause');
    }
    worker.on("reservation.accepted", pauseAlertSoundOnReservationStatusChanged);
    worker.on("reservation.rejected", pauseAlertSoundOnReservationStatusChanged);
    worker.on("reservation.timeout", pauseAlertSoundOnReservationStatusChanged);
    worker.on("reservation.canceled", pauseAlertSoundOnReservationStatusChanged);
    worker.on("reservation.rescinded", pauseAlertSoundOnReservationStatusChanged);

    //Function call when task ends
    worker.on("task.wrapup", function (task) {
        if ($("#call_customer_hangs_up").val() == 1) {
            $("#lead-decline-hangup").hide();
        }
        $('.salesagentintro').hide();
        $('.mute-call').addClass('hidden');
        $('.unmute-call').removeClass('hidden');
        var call_customer_lead = $('#call_customer_lead_verify').val();
        if (disconnected_by_agent === false && leadverify == false) {
            var call_customer_lead = $('#call_customer_lead_verify').val();
            if(call_customer_lead != 1){
                $("#verifylead").hide();
                // getDispositions(0);
            }
            // $('.call_hangup_or_dropped').show();
            // // $('.salesagentintro').hide();
            // $('.create-telesale').hide();
            // if ($('#telesale_reference_id').val() != "") {
            //     $('.decline-sale-form').show();
            //     $('.hangup_dispositions').show();
            //     $('.decline_dispositions').hide();

            //     var element = $(".hangup_dispositions").first();

            //     $('.decline_reason').val($('input[type="radio"]', element).val());
            //     //  $('input[type="radio"]',element).prop('checked', true);
            //     //  $('.iradio_minimal',element).addClass('checked');

            //     $('#updatetelesalestatus #verification_code').val('3');
            // }
        }

        //Task ends means call was disconnected, checking for each conditions whether lead is verified, disconnected or declined and display screen according to it.
        var showDisconnectedScreen = false;
        if (call_type == "outbound" || call_type == OUTBOUND_DISCONNECT || call_type == selfVerifiedCallbackType) {
            if (leadVerifiedStored == false) {
                if (decline_lead_success == true) {
                    var referenceId = getLeadReferenceId();
                    retrieveDispositionsHandler("decline", referenceId);
                } else {
                    showDisconnectedScreen = true;
                }
            }
        } else {
          if (lead_verified == true && $('#call_customer_lead_verify').val() == "") {
            if (decline_lead_success == true) {
                var referenceId = getLeadReferenceId();
                retrieveDispositionsHandler("decline", referenceId);
              } else {
                showDisconnectedScreen = true;
              }
          }
        }

        if (showDisconnectedScreen == true) {
                callDisconnectedHandler();
                disableReadyNotReadyButtons();
        } else {
            if (call_end_by_customer == true) {
                console.log('call hangup or dropped 3');
                $('.call_hangup_or_dropped').show();
                hangUp();
            }
        }




        for (var j = 0; j < combined_all_worker_activites.length; j++) {
            combined_all_worker_activites[j]['worker'].update("ActivitySid", combined_all_worker_activites[j]['activity']["WrapUp"]);
        }
        if(call_customer_lead != 1) {
            $('#connected-agent-row').addClass('hidden');
        }else{
            $('#complete-task-row').removeClass('hidden');
        }
        $('#complete-task-button').data('rel', task.sid);

    });
    worker.on("token.expired", function () {
        var token = refreshJWT();
        console.log('refresh token: '+token);
        worker.updateToken(token);
    });
  // }, 2000);
}

//On click of Ready button
$('body').on('click', '#complete-task-button', function () {
    completetask($(this).data('rel'));
    unblockanchor();
    $('.new_telesale_reference').html('');
    $('.agent-detail-main').hide('');
    $('.online-agent-name').html('');
    $('.agent-detail-wrapper').hide();
    $('.assigned_workspace_nav li').removeClass('active');
    $('.assigned_workspace_nav').show();
    $('.active-client-call').hide();
    $('#user-status-box  .chat').hide();
    $('.online-status').removeAttr('disabled');
    $('#complete-task-row').addClass('hidden');
    $('.script-important-buttons').hide();
    $('.decline-sale-form').hide();
    $('.decline-form').hide();
    $('.waiting-for-call').show();
    $('.call_duration').html("00:00:00");
    $('#decline-sale-form')[0].reset();
    $('#verify_agent_lead')[0].reset();

    $(".call_hangup_or_dropped").hide();
    $('#verifylead').hide();
    hideVerificationSection();
    $('.client-verify-status').hide();
    $('.telesale-verify-status').hide();
    $('.agent-verify-status').hide();
    $('.verification-complete-block').hide();

    $(".dispositions-outer").html("");

    $('.new-status-box .status-outer .btn-select').removeAttr('disabled');
    $('#helpDeskSupportBtn').removeAttr('disabled');

    seconds = 0;
    minutes = 0;
    hours = 0;


    for (var j = 0; j < combined_all_worker_activites.length; j++) {
        combined_all_worker_activites[j]['worker'].update("ActivitySid", combined_all_worker_activites[j]['activity']["Available"]);
    }

})

//On click of not ready button
$('body').on('click', '#not-ready-button', function () {
    $(".status-select-not-ready").show();
});
$('body').on('click', '.online-status', function () {
    var statustochange = $(this).data('status');
    for (var j = 0; j < combined_all_worker_activites.length; j++) {
        if (statustochange == 'offline') {
            combined_all_worker_activites[j]['worker'].update("ActivitySid", combined_all_worker_activites[j]['activity']["Offline"]);
        } else {
            combined_all_worker_activites[j]['worker'].update("ActivitySid", combined_all_worker_activites[j]['activity']["Available"]);
        }

    }

});

// $('body').on('change','#status-to-change',function(){
//     var activityStatus = $("#status-to-change option:selected").text();;
//     for(var j = 0; j < combined_all_worker_activites.length; j++ ){
//       combined_all_worker_activites[j]['worker'].update("ActivitySid", combined_all_worker_activites[j]['activity'][activityStatus]);
//     }

// });

function htmlDecode(input) {
    var doc = new DOMParser().parseFromString(input, "text/html");
    return doc.documentElement.textContent;
}

function completetask(taskSid) {
    activeworkercall.completeTask(taskSid,
        function (error, completedTask) {
            if (error) {
                console.log(error.code);
                console.log(error.message);
                return;
            }
            console.log("Completed Task: " + completedTask.assignmentStatus);
        }
    );
}

/* Hook up the agent Activity buttons to Worker.js */

function bindAgentActivityButtons(worker, activitySids) {
    worker.activities.fetch(function (error, activityList) {
        var activities = activityList.data;
        var i = activities.length;
        while (i--) {
            activitySids[activities[i].friendlyName] = activities[i].sid;
        }
    });


}

/* Update the UI to reflect a change in Activity */

function agentActivityChanged(activity) {
    hideAgentActivities();
    showAgentActivity(activity);
}

function hideAgentActivities() {
    var elements = document.getElementsByClassName('agent-activity');
    var i = elements.length;
    while (i--) {
        elements[i].style.display = 'none';
    }
}

function showAgentActivity(activity) {
    activity = activity.toLowerCase();
    var elements = document.getElementsByClassName(('agent-activity ' + activity));
    // elements.item(0).style.display = 'block';
}

function updateActivityDetails(activityName,activityUpdateTimer) {
  var str = "";
  var str2 = "";
  switch(activityName.toLowerCase()) {
    case 'break':
      str = "On break for ";
      str2 = "You are currently on break";
      break;
    case 'coaching':
      str = "In a coaching session for ";
      str2 = "You are currently in a coaching session";
      break;
    case 'lunch':
      str = "On lunch for ";
      str2 = "You are currently on lunch";
      break;
    case 'meeting':
      str = "In a meeting for ";
      str2 = "You are currently in a meeting";
      break;
    case 'other':
      str = "Unavailable for ";
      str2 = "You are currently unavailable";
      break;
    case 'technical difficulty':
      str = "Unavailable for ";
      str2 = "You are currently unavailable";
      break;
    case 'training':
      str = "In training for ";
      str2 = "You are currently in a training session";
      break;
    case 'offline':
      str = "Offline for ";
      str2 = "You are currently offline";
      break;
    case 'unavailable':
      str = "Unavailable for ";
      str2 = "You are currently Unavailable";
      break;
    case 'wrapup':
      str = "Wrapup for ";
      str2 = "You are currently WrapUp";
      break;
    case 'available':
      str = "Available for ";
      str2 = "Please wait for a call";
      break;

    default:
      str = activityName + " for ";
      str2 = "You are currently " + activityName;
  }

    $('.available-call-time').html(str + ' <span class="monospace">'
          + activityUpdateTimer.getTimeValues().toString() + '</span>');

    $('.waiting-for-call .waiting-for-call-status').html(str2);
}

/* Other stuff */

function logger(message) {
    // var log = document.getElementById('log');
    // log.value += "\n> " + message;
    // log.scrollTop = log.scrollHeight;
}
// window.close = function() {
// alert('test');
// return false;

// }


window.onload = function () {
    // let ansArray = [];
    logger("Initializing...");

    //Call twilio's token url
    if (typeof tokenurl != 'undefined') {
        $.post(tokenurl, {}, function (data) {
            var workers_with_activites = [];
            if (data.workersdata.length > 0) {
                for (var i = 0; i < data.workersdata.length; i++) {
                    let workerToken = data.workersdata[i]['workerToken'];

                    //Delete all accepted reservations on page reload
                    var queryParams = { "ReservationStatus": "accepted" };
                    var workerIns = new Twilio.TaskRouter.Worker(workerToken);
                    workerIns.fetchReservations(
                        function (error, reservations) {
                            if (error) {
                                console.log(error.code);
                                console.log(error.message);
                                return;
                            }

                            var data = reservations.data;
                            for (i = 0; i < data.length; i++) {
                                console.log(data[i]);

                                workerIns.completeTask(data[i].taskSid,
                                    function (error, completedTask) {
                                        if (error) {
                                            console.log(error.code);
                                            console.log(error.message);
                                            return;
                                        }
                                        console.log("Completed Task: " + completedTask.assignmentStatus);
                                    }
                                );
                            }
                        }, queryParams);

                        //Delete all pending reservations on page load
                        var queryParams = { "ReservationStatus": "pending" };
                        workerIns.fetchReservations(
                            function(error, reservations) {
                                if(error) {
                                    console.log(error.code);
                                    console.log(error.message);
                                    return;
                                }
                                console.log(reservations);
                                var data = reservations.data;
                                for(i=0; i<data.length; i++) {
                                  // now reject the reservation
                                  data[i].reject(
                                    function(error, reservation) {
                                        if(error) {
                                            console.log(error.code);
                                            console.log(error.message);
                                            return;
                                        }
                                        console.log("reservation rejected");
                                    }
                                  );

                                }
                            },
                            queryParams
                        );
                }

                window.number_of_accounts_assigned = data.workersdata.length;
                for (var i = 0; i < data.workersdata.length; i++) {
                    window['activitySids' + i] = {};
                    window['workerToken' + i] = data.workersdata[i]['workerToken'];
                    window['clientToken' + i] = data.workersdata[i]['token'];
                    window['workerSid' + i] = data.workersdata[i]['workerSid'];
                    window['worker' + i] = new Twilio.TaskRouter.Worker(window['workerToken' + i]);

                    registerTaskRouterCallbacks(window['worker' + i], window['activitySids' + i]);

                    bindAgentActivityButtons(window['worker' + i], window['activitySids' + i]);
                    workers_with_activites.push({
                        'worker': window['worker' + i],
                        'activity': window['activitySids' + i]
                    });
                }

                Twilio.Device.setup(data.token);
                window.device_unique_number = data.device_unique_number;
                window.combined_all_worker_activites = workers_with_activites;


            }

        }, 'json');

    }



    // window.current_workspaceID = "WS773b86b9fe21ec7b213eb54af1019f6e";
    // window.current_workflowID = "WWd18dc5feed687a8cd139e60c466e167a";
    // window.current_language = 'en';
    // getClientformScripts("WS773b86b9fe21ec7b213eb54af1019f6e","WWd18dc5feed687a8cd139e60c466e167a",'en');


    $('.getreason_for_decline').on('click', function (event) {
        if ($(this).hasClass('clone_lead')) {
            $('.save-and-clone').show();
        } else {
            $('.save-and-clone').hide();
        }
        if ($(this).val() == 'other') {

            $('#decline_reason').attr('type', 'text');
            $('.decline_reason').val('');
        } else {
            $('#decline_reason').attr('type', 'hidden');
            $('.decline_reason').val($(this).val());
            $('#allow_cloning').val($(this).val());

        }


        $('#disposition_id').val($(this).val());
    });


    $('body').on('blur', '#decline_reason', function () {
        $('.decline_reason').val($(this).val());
    })


};


function getClientAgents(workspaceSid) {
    // $('.sale-detail-wrapper').html(loadingspinner());
    // $.post(getclientagents, {'workspace_id' : workspaceSid}, function(data) {
    //    $('.sale-detail-wrapper').html(data);
    //     tableIntialize();
    // });
}

//Retrives Sales agent identity verification script
function getClientformScripts(workspaceSid, workflowid, language) {
    $.post(getformscript, {
        'workspace_id': workspaceSid,
        'workflow_id': workflowid,
        'language': language
    }, function (data) {
        if (data.status == 'success' && Twilio.Device.activeConnection() != undefined) {
            window.allquestions = data.question;
            $('.customer-detail-wrapper').hide();
            $(".sale-detail-wrapper").show();
            $('.alesagentintro-qus').html(data.question.salesagentintro[0]['question']);
            $('.salesagentin-qus').html(data.question.salesagentintro[1]['question']);
            $('.telesale-verification-qus').html(data.question.salesagentintro[2]['question']);
            if ( typeof data.question.salesagentintro[3] != 'undefined') {
                $('.procced-can-not-trans-qus').html(data.question.salesagentintro[3]['question']);
            }
            $("#client-message").removeClass("text-success");
            $('#client-message').html('');
            $('.salesagentintro').show();
            $('.verify-lead-data-2').hide();
            $('.verify-lead-data-3,.verify-lead-data-4').hide();
        }
    });
}

$('body').on('click', '#telePre', function () {
    $('.verify-lead-data-1').hide();
    $('.verify-lead-data-2').show();
    $('.verify-lead-data-3,.verify-lead-data-4').hide();
    clearErrMessage();
});

$('body').on('click', '#agentPre', function () {
    $('.verify-lead-data-1').show();
    $('.verify-lead-data-2').hide();
    $('.verify-lead-data-3,.verify-lead-data-4').hide();
    clearErrMessage();
});

function single_question(question_text) {

    if (question_text.indexOf('telesale_verification_id_wrapper') != -1) {
        var lead_input_class = "verify_lead_data";
    } else {
        var lead_input_class = "";
    }
    return '<div class="col-sm-12 question-text salesagentintro ' + lead_input_class + '">' + question_text + '</div>';

}

function single_question_closing(question_text) {
    return '<div class="col-sm-12 question-text closing-script-question">' + question_text + '</div>';

}

function single_question_with_answer(question_text, index, positive_text, negative_text, answeroption, is_customizable = 0, count_qus = 1, is_introductionary = 0, intro_questions = 0) {
    var html = '';

    html += '<div class="questions-progress question-tab verification-question-text verification-' + index + '">';


    count_qus -= intro_questions;

    if (is_introductionary === 1) {

        html += '<div class="question"><div class="text-center">';
        html += '<p class="question_wrapper"><span class="q-text text-center"><b>' + question_text + '</b></span> </p>';

    } else {
        console.log(question_text);
        html += '<div class="question"><div class="text-left">';
        html += '<p class="question_wrapper"><span class="q-id"><b>Q' + count_qus + '.</b> </span><span class="q-text">' + question_text + '</span> </p>';
        console.log(html);
    }
    //html+='<a href="javascript:void(0)" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Edit Question" role="button" class="btn"><img src="https://newdev.tpv.plus/images/edit.png"></a></div>';
    html += show_answer_option(answeroption);
    //html += custom_option_text(is_customizable);
    html += answer_options(question_text, index, positive_text, negative_text);
    html += '</div></div>';
    return html;



    /*return '<div class="col-sm-12 verification-question-text verification-' + index + '" >' +
        '<div class="question_wrapper">' +
        question_text +
        '</div>' +
        show_answer_option(answeroption) +
        custom_option_text(is_customizable) +
        answer_options(question_text, index, positive_text, negative_text) +
        '</div>';*/

}

function single_question_for_create_lead(question_text, index) {
    return '<div class="col-sm-12 create-lead-question-text create-lead-' + index + '" >' +
        '<div class="question_wrapper">' +
        question_text +
        '</div>' +
        '</div>';

}

function show_answer_option(answertext) {
     return "<div class='answer-text ml42'>" + answertext + "</div>";
}

function custom_option_text(is_customizable) {
    if (is_customizable == 1) {
        var display = 'block';
    } else {
        var display = 'none';
    }
    return '<div class="custom-option" style="display:' + display + '">' +
        '<div class="checkbox ">' +
        '<label class="checkbx-style">Change answer<input class="custom-answer-checkbox" type="checkbox" value="Yes"><span class="checkmark"></span></label>' +
        '</div>' +
        '<div  class="custom-option-inner-wrapper"><input type="text"  class="custom-answer-value" Placeholder="Enter new answer"></span>' +
        ' </div>' +
        ' </div>';
}

function answer_options(question, index, positive_text, negative_text) {
    var identifier = Math.floor(Math.random() * 20);
    question = question.replace(/'/g, "\\'");
    var next_element = Number(index) + Number(1);

    return '<div class="text-center question-btn">' +
        '<a type="button" class="mr15 btn btn-green yes verified_yes" id="options-' + identifier + '-1"  data-currentelement="' + index + '" data-nextelement="' + next_element + '" >' +
        positive_text +
        '</a>' +
        '<a class="btn btn-red verified_no" data-toggle="modal" data-target="#decline-lead-modal" id="options-' + identifier + '-2" data-currentelement="' + index + '" data-nextelement="' + next_element + '" >' +
        negative_text +
        '</a>' +
        '</div>';
    /*'<input class="icheck verified_yes" type="radio" id="options-'+identifier+'-1" name="'+question+'" data-currentelement="'+index+'" data-nextelement="'+next_element+'">'+
     '<label for="options-'+identifier+'-1">'+positive_text+'</label>  '+
     '<input class="icheck verified_no" type="radio" id="options-'+identifier+'-2" name="'+question+'" data-currentelement="'+index+'" data-nextelement="'+next_element+'">'+
     '<label for="options-'+identifier+'-2">'+negative_text+'</label>'+*/
}

function getFormScript(workspaceSid) {
    $.post(getclientagents, {
        'workspace_id': workspaceSid
    }, function (data) {
        /* $('.sale-detail-wrapper').html(data);*/
        $('.sale-detail-wrapper-Qus').html(data);
        $('.sale-detail-wrapper-Qus').show();
        $('.verification-complete-block').hide();
        $('.call_hangup_or_dropped').hide();
        tableIntialize();
    });
}



$('body').on('click', '.getagentsales', function (e) {
    e.preventDefault();
    $('#telesales-search input[name="ref"]').val("");
    $('#confirmreview #userid').val("");
    $('.duringcallelements').remove();
    $('.sale-detail-wrapper').html(loadingspinner());
    $('.sale-detail-wrapper').load($(this).attr('href'));

});
$('body').on('click', '.openleaddetail', function (e) {
    e.preventDefault();
    $('#telesales-search input[name="ref"]').val($(this).data('ref'));
    $('#telesales-search').append('<input type="hidden" class="duringcallelements" name="from_call" value="1">');
    $('#confirmreview #userid').val($(this).data('uid'));
    $('#telesales-search').submit();
});
$('body').on('click', '.back-to-clientsagents', function (e) {
    getClientAgents(current_workspaceID);
});



function tableIntialize() {
    $(document).ready(function () {
        var $table1 = jQuery('#table-1');

        // Initialize DataTable
        $table1.DataTable({
            "aLengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            "bStateSave": true
        });

        // Initalize Select Dropdown after DataTables is created
        $table1.closest('.dataTables_wrapper').find('select').select2({
            minimumResultsForSearch: -1
        });
    });
}

//Enable profile and logout anchors
function unblockanchor() {
    $("a").each(function () {
        $(this).attr("href", $(this).attr("rel"));
        $(this).attr("onclick", $(this).attr("clickref"));
        $(this).removeAttr("rel");
        $(this).removeAttr("clickref");
        // $('.sale-detail-wrapper').html('');
    });
}

function loadingspinner() {
    return '<div class="text-center"><i class="fa fa-spin fa-spinner" style="font-size:3em"></i></div>';
}

//Check whether entered sales agent id is exists in system or not
$('body').on('click', '.checkagentid', function () {
    var agentid = $('.verify-agent-ID').val();
    if (agentid != "") {
        $('#checkcagent_button').attr('disabled', true);
        var agent_client_id = $('#agent_client_id').val();
        $.ajax({
            type: "POST",
            url: telesaleverifyagent,
            data: {
                'agentid': agentid,
                workspace_id: current_workspaceID,
                lang: current_language,
                client_id: agent_client_id
            },
            success: function (response) {
                $('.agent_not_found_script').remove();
                if (response.status == 'success') {
                    var agent = response.data[0];
                    $('.agent-detail-main').show();
                    $('.agent-userid').text(agent.userid);
                    $('.agent-first-name').text(agent.first_name);
                    $('.agent-last-name').text(agent.last_name);
                    $('.agent-email').text(agent.email);
                    $('.agent-client-name').text(agent.client_name);
                    $('.agent-salescenter-name').text(agent.salescenter_name);
                    $('.agent-location-name').text(agent.location_name);
                    $('.agent-userid').text(agent.userid);
                    $('.agent-detail-wrapper').show();
                    $('#checkcagent_button').removeAttr('disabled');
                    $('.agent_not_found').hide();
                    //$('.script_for_confirmation').show();
                    $('#agent_client_id').val(response.data[0].client_id);
                    $('#agent_user_id').val(response.data[0].id);
                    $('.verify_lead_data').show();
                    $('.telesale_verification_id_wrapper').show();
                    $('.telesale-verify-status .tele-message').html('');
                    $('.telesale-verify-status').show();
                    //$('.verify-telesale-ID').val(response.data[0].client_id + '-' + response.data[0].salescenter_id + '-' + response.data[0].location_id + '-');
                    $('.agent-verify-status').show();
                    // $('.verify-lead-data-2').hide();
                    // $('.verify-lead-data-3').show();
                    // $('.agent-verify-status').html('<span class="text-success"><i class="fa fa-check"></i> Agent Found</span><button type="button" id="agentNext" class="btn btn-red">Next</button></div>');

                    $('#agent-message').html('<p class="text-success"><i class="fa fa-check"></i>Record Found</p>');

                    /* $("#checkagentid").attr('disabled', 'disabled');*/
                    $("#agentNext").trigger('click');
                    $("#agentError").hide();



                } else {

                    $('#agent_user_id').val('');
                    //$('.script_for_confirmation').hide();
                    $('.agent_not_found').show();
                    $('.agent-detail-wrapper').hide();
                    $('.telesale_verification_id_wrapper').hide();
                    $('.verify_lead_data').hide();
                    $('.verify-telesale-ID').val('');

                    $('#agent-message').html('<p class="text-danger">Please enter a correct Agent ID or the verification cannot proceed.</p>');
                    $("#agentNext").hide();
                    $("#agentError").show();
                    $('.agent-verify-status').show();
                    $('#checkcagent_button').removeAttr('disabled');


                }
            },
            fail: function () {
                $('.agent-verify-status').hide();
                $('.agent-detail-wrapper').hide();
                $('#checkcagent_button').removeAttr('disabled');
            }
        });
    }
});

$('body').on('click', '#agentNext', function () {
    $('.verify-lead-data-2').hide();
    $('#teleNext').hide();
    $('.verify-lead-data-3').show();
    clearErrMessage();
});

//Check for entered lead id is exists in system or not
$('body').on('click', '.checktelesaleid', function () {
    //console.log(currentConnection.parameters.CallSid);

    var telesaleid = $('.verify-telesale-ID').val();
    var agentid = $('#agent_user_id').val();
    if (agentid == "") {
        $('.agent-verify-status .agent-message').html('<span class="text-danger">Verify your ID first</span>');
        return false;
    }
    if (telesaleid != "") {
        $('#check-telesale-button').attr('disabled', true);
        //const leadId = telesaleid.substring(telesaleid.lastIndexOf('-') + 1);
        $('#reference_id_to_update').val(telesaleid);

        /*$('.telesale-verify-status').html('<i class="fa fa-spin fa-spinner"></i>');*/
        $.ajax({
            type: "POST",
            url: telesaleverifysaleid,
            data: {
                'telesaleid': telesaleid,
                'agentid': agentid,
                'callid': currentConnection.parameters.CallSid
            },
            success: function (response) {
                if (response.status == 'success') {
                    console.log(response);
                    //$('.script_for_confirmation').show();
                    //$('.telesale_not_found').hide();
                    $('#telesale_reference_id').val(response.data.refrence_id);
                    $('#telesale_form_id').val(response.data.form_id);
                    $('#script_form_id').val(response.data.form_id);
                    $('#is_multiple').val(response.data.is_multiple);
                    $('#multiple_parent_id').val(response.data.id);
                    $('#check-telesale-button').removeAttr('disabled');
                    console.log('--------check telesale id 1');
                        $("#complete-task-button").prop("disabled", true);
                        $("#not-ready-button").prop("disabled", true);
                       // $("#hangup-call-button").prop("disabled", true);
                        $("#hangup-call-button").removeAttr("disabled");

                    $('#leadzipcodestate').val(response.state);
                    $('.leadzipcodestate').val(response.state);
                    $('#leadcommodity').val(response.commodity);
                    $('.leadcommodity').val(response.commodity);

                    // $('.telesale-verify-status').html('<span class="text-success"><i class="fa fa-check"></i> Lead Found</span>');

                    $("#tele-message").html('<p class="text-success"><i class="fa fa-check"></i> Lead Found</p>');
                    //$("#teleNext").trigger('click');
                    $("#teleError").hide();
                    $('.telesale-verify-status').show();
                    $('.verify-lead-data-3').hide();
                    $('.verify-lead-data-4').show();

                    lead_verified = true;

                } else {
                    lead_verified = false;
                    $('.script_for_confirmation').hide();
                    /*    if (agentid != "") {
                            $('.telesale_not_found').show();
                        } else {
                            $('.telesale_not_found').hide();
                        }*/
                    $('#check-telesale-button').removeAttr('disabled');
                    $("#tele-message").html('<p class="text-danger">Please enter a valid lead ID or the verification cannot proceed.</p>');
                    $("#teleError").show();
                    $("#teleNext").hide();
                    $('.telesale-verify-status').show();
                    /*$('.telesale-verify-status').html('<span class="text-danger"><i class="fa fa-times"></i> Lead not Found</span>');
                      $('.telesale-verify-error').show();*/
                }
            },
            fail: function () {
                $('.telesale-verify-status').hide();
            }
        });
    }

});

$('body').on('click', '.script_for_confirmation', function () {
    var telesaleid = $('.verify-telesale-ID').val();

    var agentid = $('.verify-agent-ID').val();
    if (agentid == "") {
        $('.agent-verify-status .agent-message').html('<span class="text-danger">Please enter agent ID</span>');
        return false;
    }
    if (telesaleid == "") {
        $('.telesale-verify-status').html('<span class="text-danger">Please enter telesale ID</span>');
        return false;
    }

    $('#form_worksid').val(current_workspaceID);
    $('.form_worksid').val(current_workspaceID);
    $('#form_workflid').val(current_workflowID);
    $('.form_workflid').val(current_workflowID);
    $('#current_lang').val(current_language);
    $('#script_current_lang').val(current_language);

    $.ajax({
        type: "POST",
        url: telesaleverifylead,
        data: $('#verify_agent_lead').serialize(),
        success: function (response) {
            if (response.status == 'success') {
                $('.sale-detail-wrapper-Qus').append('<h3 class="verification-question-text lead-verification-title">Lead verification</h3>');
                $('.sale-detail-wrapper-Qus').append('<div class="verifications-questions-wrapper"></div>');
                $('.sale-detail-wrapper-Qus').show();
                for (var i = 0; i < response.data.length; i++) {
                    var qus_num = i + 1;
                    var question_html = single_question_with_answer(response.data[i]['question'], i, response.data[i]['positive_ans'], response.data[i]['negative_ans'], response.data[i]['answer_option'], response.data[i]['is_customizable'], qus_num);
                    $('.verifications-questions-wrapper').append(question_html);
                }
                $('.verification-0').addClass('active-que');
                $('.lead-verification-title').show();
                $('.salesagentintro').hide();

            } else {
                $('.script_for_confirmation').hide();
                $('.telesale-verify-status').html('<span class="text-danger"><i class="fa fa-times"></i> Lead not Found</span>');
            }
        },
        fail: function () {
            $('.telesale-verify-status').html('');
        }
    });

});

function getanswerdata($obj, negative_positive_answer) {
    var question = $('.question_wrapper', $obj).text();
    var answer = $('.answer-text', $obj).text();
    var custom_answer = $('.custom-answer-checkbox', $obj).prop('checked');
    var new_answer = $('.custom-answer-value', $obj).val();
    var telesale_form_id = $('#telesale_form_id').val();
    var current_lang = $('#current_lang').val();
    var agent_user_id = $('#agent_user_id').val();
    var agent_client_id = $('#agent_client_id').val();
    var telesale_reference_id = $('#telesale_reference_id').val();

    if(question != '') {

        $.ajax({
            type: "POST",
            url: '/ajax/saveuseranswer',
            data: {
                'question': question,
                'answer': answer,
                'custom_answer': custom_answer,
                'new_answer': new_answer,
                'telesale_form_id': telesale_form_id,
                'current_lang': current_lang,
                'agent_user_id': agent_user_id,
                'agent_client_id': agent_client_id,
                'negative_positive_answer': negative_positive_answer,
                'telesale_reference_id': telesale_reference_id
            },
            success: function (response) {
                if (response.status == 'success') {
                    console.log(response.message);
                } else {
                    alert(response.message);
                }
            }
        });
    }


}

$('body').on('click', '.verification-question-text.active .custom-answer-checkbox', function () {
    var $obj = $('.verification-question-text.active-que');
    $('.custom-option-inner-wrapper', $obj).toggle();
});

/*$('body').on('click', '.verification-question-text.active .verified_yes', function() {*/
$('body').on('click', '.verifications-questions-wrapper .active .verified_yes', function () {
    var $obj = $('.verification-question-text.active');
    getanswerdata($obj, $(this).text());
    var next_node = $(this).data('nextelement');
    var currentelement = $(this).data('currentelement');

    var check_element = $('.verification-' + next_node);

    $('.verification-question-text').removeClass('active');
    if (check_element.length > 0) {
        check_element.addClass('active');
        var scrolldiv = $('.verification-' + next_node + ' .question-btn').offset().top - ($(window).height() / 1.5);
        // var scrolldiv = $('#button_id').offset().top;

        $('html,body').animate({
            scrollTop: scrolldiv
        }, "slow");
    } else {
        if (currentelement == (Number(next_node) - Number(1))) {
            $('.verify-sale').show();
        }
    }
});
$('body').on('click', '.verification-question-text.active .verified_no', function () {
    getLocation();
    var $obj = $('.verification-question-text.active');
    getanswerdata($obj, $(this).text());
    $("#confirm-message").html("Are you sure you want to decline this sale?");

    if ($('.selfverify-wrapper').length > 0) {
        $_this('#confirmreview1').modal('toggle');
        $_this('#verification_code').val('2')
        $_this('#disposition_id').val('32');
        $('.verify-sale').hide();
    } else {
        $('.decline-form').show();
        $('.decline-sale-form').show();

        $('.hangup_dispositions').hide();
        $('.decline_dispositions').show();

        var element = $(".decline_dispositions").first();


        $('.decline_reason').val($('input[type="radio"]', element).val());
        // $('input[type="radio"]',element).prop('checked', true);
        // $('.iradio_minimal',element).addClass('checked');



        $('.verification-question-text').hide();
        $('.identity-verification-detail-wrapper').hide();

    }


});

//Retrieve lead not found script and display on screen
$('body').on('click', '.telesale_not_found', function (e) {

    var token = $('#verify_agent_lead input[name="_token"]').val();
    var agent_user_id = $('#agent_user_id').val();
    $.ajax({
        type: "POST",
        url: leadquestions,
        data: {
            '_token': token,
            'uid': agent_user_id,
            'language': current_language
        },
        success: function (response) {
            if (response.status == 'success') {
                $('.salesagentintro').hide();
                $('.telesale_not_found').hide();
                $('.create-telesale').show();
                $('#current_form_id').val(response.formid);
                $('#script_form_id').val(response.formid);

                $('.sale-detail-wrapper').append('<h3 class="create-lead-question-text">New Lead</h3>');
                for (var i = 0; i < response.question.length; i++) {
                    var question_html = single_question_for_create_lead(response.question[i]['question'], i);
                    $('.sale-detail-wrapper').append(question_html);
                }



            } else {
                $('.script_for_confirmation').hide();
                $('.telesale-verify-status').html('<span class="text-danger"><i class="fa fa-times"></i> Lead not Found</span>');
            }
        },
        fail: function () {
            $('.telesale-verify-status').html('');
        }
    });

});


$('body').on('click', '.create-telesale', function () {
    $('#form_worksid').val(current_workspaceID);
    $('#form_workflid').val(current_workflowID);
    $('#current_lang').val(current_language);
    $('#script_current_lang').val(current_language);
    $('.error-field').removeClass('error-field');
    var error = 0;
    $('.sale-detail-wrapper .required').each(function () {

        error = validate_lead_required(this);
        if (error == 1) {
            return false;
        }

    });

    if (error == 1) {
        var scrolldiv = $('.error-field').offset().top - 50;
        $('html,body').animate({
            scrollTop: scrolldiv
        }, "slow");
        return false;
    }






    $.ajax({
        type: "POST",
        url: createlead,
        data: $('#verify_agent_lead').serialize(),
        success: function (response) {
            if (response.status == 'success') {
                $('.new_telesale_reference').html('New telesale created successfully. Here is new reference Id ' + response.reference_id);
                $('#telesale_reference_id').val(response.reference_id);
                $('#reference_id_to_update').val(response.reference_id);

                $('#telesale_form_id').val(response.formid);
                $('#script_form_id').val(response.formid);
                $('.sale-detail-wrapper-Qus').append('<h3 class="verification-question-text lead-verification-title">Lead verification</h3>');
                $('.sale-detail-wrapper-Qus').append('<div class="verifications-questions-wrapper"></div>');
                for (var i = 0; i < response.data.length; i++) {
                    var qus_num = i + 1;
                    var question_html = single_question_with_answer(response.data[i]['question'], i, response.data[i]['positive_ans'], response.data[i]['negative_ans']);
                    $('.verifications-questions-wrapper').append(question_html);
                }
                $('.verification-0').addClass('active');
                $('.lead-verification-title').show();


                $('.create-lead-question-text').hide();
                $('.create-telesale').hide();
                $('.salesagentintro').hide();

            } else {
                $('.script_for_confirmation').hide();
                $('.telesale-verify-status').html('<span class="text-danger"><i class="fa fa-times"></i> Lead not Found</span>');
            }
        },
        fail: function () {
            $('.telesale-verify-status').html('');
        }
    });

});

function validate_lead_required(element) {

    var error = 0;
    var validate_option = $(element).data('reltype');
    if (validate_option == 'radio') {
        var validate_radio = 1;
        $("input[type='radio']", element).each(function () {
            if (($(this).is(':checked'))) {
                validate_radio = 0
            }
        });

        if (validate_radio == 1) {
            error = 1;
            $(element).addClass('error-field');
        }

    }
    if (validate_option == 'checkbox') {
        var validate_checkbox = 1;
        $("input[type='checkbox']", element).each(function () {
            if ($(this).is(':checked')) {
                validate_checkbox = 0;
            }
        });
        if (validate_checkbox == 1) {
            error = 1;
            $(element).addClass('error-field');
        }
    }
    if (validate_option == 'text' || validate_option == 'phonenumber') {
        if ($("input[type='text']", element).val() == '') {
            error = 1;
            $("input[type='text']", element).addClass('error-field');
        }
    }
    if (validate_option == 'selectbox') {
        if ($("select", element).val() == '') {
            error = 1;
            $("select", element).addClass('error-field');
        }
    }

    return error;
}

$("body").on('keydown', '.contact-number-format', function () {
    var curchr = this.value.length;
    var curval = $(this).val();
    if (curchr == 3) {
        $(this).val("(" + curval + ")" + "-");
    } else if (curchr == 9) {
        $(this).val(curval + "-");
    }
});

//Change phone number format which are displaying on accept / reject popup
function phonenumber_format(number) {
    number = number.replace('+', '');
    return number.replace(window.phoneNumDisplayFormat, window.phoneNumReplacement);
}


$('body').on('click', '.save-and-clone', function () {

    $.ajax({
        type: "POST",
        url: replicate,
        data: $('#verify_agent_lead').serialize(),
        success: function (response) {
            if (response.status == 'success') {
                $('.decline-sale-form').hide();
                $('#telesale_reference_id').val(response.newleadref);
                $('#reference_id_to_update').val(response.newleadref);


                window.edit_options_fields = response.options;
                $('.verifications-questions-wrapper').remove();
                $('.lead-verification-title').remove();
                $('.sale-detail-wrapper-Qus').append('<h3 class="verification-question-text lead-verification-title">Lead verification/Creation</h3>');
                $('.sale-detail-wrapper-Qus').append('<div class="verifications-questions-wrapper"></div>');
                for (var i = 0; i < response.data.length; i++) {
                    var qus_num=i+1;
                    var question_html = single_question_with_answer(response.data[i]['question'], i, response.data[i]['positive_ans'], response.data[i]['negative_ans'], response.data[i]['answer_option'], response.data[i]['is_customizable'],qus_num);
                    // var question_html = single_question_with_answer(response.data[i]['question'],i,response.data[i]['positive_ans'],response.data[i]['negative_ans']);
                    $('.verifications-questions-wrapper').append(question_html);
                }
                $('.verification-0').addClass('active-que');
                $('.lead-verification-title').show();
                $('.salesagentintro').hide();

            } else {
                $('.script_for_confirmation').hide();
                $('.telesale-verify-status').html('<span class="text-danger"><i class="fa fa-times"></i> Lead not Found</span>');
            }
        },
        fail: function () {

        }
    });

});

/* Edit question options */
$('body').on('click', '.verification-question-text.active-que .edit-cloned-option', function () {
    var element_ref = $(this).data('ref');
    var element_type = $(this).data('edittype');
    var labeltext = $(this).data('labeltext');
    $('.modal-body.edit-options').html('');

    $('.select_values_of_new_option').attr('data-ref', element_ref);
    $('.select_values_of_new_option').attr('data-edittype', element_type);
    if (element_type == 'selectbox') {

        $('#editoptions_label_header').html(labeltext);
        $('.modal-body.edit-options').append('<select  class="options_to_select "><option value="">Select</select>');
        for (var j = 0; j < edit_options_fields[labeltext].options.label.length; j++) {
            $('.options_to_select').append("<option value='" + edit_options_fields[labeltext].options.label[j] + "'  data-ref='" + element_ref + "'>" + edit_options_fields[labeltext].options.label[j] + " </option>");
        }
        $('.options_to_select').select2({
            minimumResultsForSearch: 1
        });
        $('#editoptionsmodal').modal('show');

    } else if (element_type == 'radio') {

        $('#editoptions_label_header').html(labeltext);
        $('.modal-body.edit-options').append('<ul class="options_to_select nav"></ul>');
        for (var j = 0; j < edit_options_fields[labeltext].options.label.length; j++) {
            $('.options_to_select').append("<li><input type='radio' name='editoption' class='selectnewoption' value='" + edit_options_fields[labeltext].options.label[j] + "'  data-ref='" + element_ref + "'>" + edit_options_fields[labeltext].options.label[j] + " </li>");
        }


        $('#editoptionsmodal').modal('show');
    } else if (element_type == 'checkbox') {
        $('#editoptions_label_header').html(labeltext);
        $('.modal-body.edit-options').append('<ul class="options_to_select nav"></ul>');
        for (var j = 0; j < edit_options_fields[labeltext].options.label.length; j++) {
            $('.options_to_select').append("<li><input type='checkbox' class='selectnewoption' value='" + edit_options_fields[labeltext].options.label[j] + "'  data-ref='" + element_ref + "'>" + edit_options_fields[labeltext].options.label[j] + " </li>");
        }

        $('#editoptionsmodal').modal('show');
    } else {
        $('.hide-display-option-' + element_ref).hide();
        $(this).hide();
        $('.show-edit-option-' + element_ref).show();
        $(this).next('.save-cloned-option').show();

    }


});

$('body').on('click', '.select_values_of_new_option', function () {
    var element_ref = $(this).data('ref');
    var element_type = $(this).data('edittype');
    var span_text = $('.hide-display-option-' + element_ref);
    var input_text = $('.show-edit-option-' + element_ref + ' .new_lead_data_to_update');
    var selected_value = "";
    if (element_type == 'radio') {

        $('.options_to_select input[type="radio"]').each(function (e) {
            if ($(this).prop('checked') == true) {
                selected_value = $(this).val();
                span_text.html(selected_value);
                input_text.val(selected_value);
            }
        });
    }
    if (element_type == 'selectbox') {
        selected_value = $('.select2-chosen').html();
        if (selected_value != "") {

            span_text.html(selected_value);
            input_text.val(selected_value);
        }
    }
    if (element_type == 'checkbox') {
        var selected_value = "";
        $('.options_to_select input[type="checkbox"]').each(function (e) {
            if ($(this).prop('checked') == true) {
                if (selected_value != '') {
                    selected_value = selected_value + ', ' + $(this).val();
                } else {
                    selected_value = $(this).val();
                }


            }
        });
        span_text.html(selected_value);
        input_text.val(selected_value);
    }
    $('#editoptionsmodal').modal('hide');
});
$('body').on('click', '.save-cloned-option', function () {
    var element_ref = $(this).data('ref');
    var new_val = $('.show-edit-option-' + element_ref + ' .new_lead_data_to_update').val();
    $('.hide-display-option-' + element_ref).html(new_val);
    $('.hide-display-option-' + element_ref).show();
    $(this).hide();
    $('.show-edit-option-' + element_ref).hide();
    //$(this).prev('.edit-cloned-option').show();
});

$('body').on('click', '.verify-sale', function () {
    var formdata = new FormData();
    formdata.append('leadid', $('#telesale_reference_id').val());
    var options_array = [];
    if ($('.new_lead_data_to_update').length > 0) {
        $('.new_lead_data_to_update').each(function () {
            var element_opt = $(this).attr('name');
            var element_val = $(this).val();
            // var new_values = {
            //           element_opt : element_val
            //       };
            // options_array.push(new_values);
            formdata.append('options[' + element_opt + ']', element_val);
        });
        // fd.append( 'options', options_array);

        $.ajax({
            type: "POST",
            url: updatelead,
            data: formdata,
            contentType: false,
            processData: false,
            success: function (response) { },
            fail: function () {

            }
        });


    }

});
//

$('body').on('click', '.decline-confirm', function (e) {
    var selected_val = "";
    $('.decline_dispositions .getreason_for_decline').each(function () {
        if ($(this).prop('checked') === true) {
            selected_val = 1;
        }

    });
    $('.hangup_dispositions .getreason_for_decline').each(function () {
        if ($(this).prop('checked') === true) {
            selected_val = 1;
        }

    });
    if (selected_val == "") {
        $('.reason-decline-msg').html('<p class="text-danger"> Please select reason for decline</p>');
        e.preventDefault();
    } else {
        $('.reason-decline-msg').html('');
        $('#confirmreview').modal();
    }
})
var seconds = 0,
    minutes = 0,
    hours = 0,
    t;

function add() {
    seconds++;
    if (seconds >= 60) {
        seconds = 0;
        minutes++;
        if (minutes >= 60) {
            minutes = 0;
            hours++;
        }
    }

    $('.call_duration').html((hours ? (hours > 9 ? hours : "0" + hours) : "00") + ":" + (minutes ? (minutes > 9 ? minutes : "0" + minutes) : "00") + ":" + (seconds > 9 ? seconds : "0" + seconds));

    timer();
}

function timer() {
    var seconds = 0,
        minutes = 0,
        hours = 0,
        t;
    t = setTimeout(add, 1000);
}

//Check for entered client id is valid or not
$('body').on('click', '.checkcleint_id', function () {
    var client_id = $('.verify-client-ID').val();

    if (client_id == "") {
        $('#client-message').html('<span class="text-danger">Please enter a valid ID</span>');
        $('.client-verify-status').show();
        return false;
    }
    if (client_id != "") {
        $('#checkcleint_button').attr('disabled', true);
        //$('.client-verify-status').html('<i class="fa fa-spin fa-spinner"></i>');
        $.ajax({
            type: "POST",
            url: "/ajax/validateclient",
            data: {
                'client_id': client_id,
            },
            success: function (response) {
                if (response.status == 'success') {
                    //$('.script_for_confirmation').show();
                    $("#client-message").addClass("text-success");
                    $('#client-message').html('<p><i class="fa fa-check"></i>Record Found</p>');
                    $('.client-verify-status').show();
                    //$("#checkcleint_id").attr('disabled', 'disabled');
                    $('#checkcleint_button').removeAttr('disabled');
                    // $("#clientNext").show();
                    $('#clientNext').trigger('click');
                    $('#client-message').html('');
                    $("#clientError").hide();
                    $('.agent-verify-status').show();

                    // $('.verify-lead-data-1').hide();
                    // $('.verify-lead-data-2').show();
                } else {
                    //$('.script_for_confirmation').hide();
                    $("#client-message").html('<p class="text-danger">Please enter a correct client id or the verification cannot proceed.</p>');
                    $('.client-verify-status').show();
                    $("#clientNext").hide();
                    $("#clientError").show();
                    $('#checkcleint_button').removeAttr('disabled');
                }
            },
            fail: function () {
                $('.client-verify-status').hide();
                $('#checkcleint_button').removeAttr('disabled');
            }
        });
    }
});

$('body').on('click', '#clientNext', function () {
    $('.verify-lead-data-1').hide();
    $('.verify-lead-data-2').show();
    clearErrMessage();
});

$('body').on('click', '#telePre', function () {
    $('.verify-lead-data-3').hide();
    $('.verify-lead-data-2').show();
    clearErrMessage();
});

var $_this = $;
// $_this('body').on('change','.selectprogram',function(){

$_this(".selectprogram ").on("change", function () {
    //alert('sdss');
    var check_refrence_number = $_this(this).data('ref');
    var code = $_this('option:selected', this).data('code');
    var rate = $_this('option:selected', this).data('rate');
    var etf = $_this('option:selected', this).data('etf');
    var msf = $_this('option:selected', this).data('msf');
    var account_number_length = $_this('option:selected', this).data('accountlength');
    var account_number_type = $_this('option:selected', this).data('accountnumbertype');
    $_this('.program_code').val(code);
    $_this('.account_number_length').val(account_number_length);
    $_this('.account_number_type').val(account_number_type);


    $_this('.programdetail_' + check_refrence_number + ' .green-text').html("Utility:" + code + ", Rate:" + rate + ", ETF:" + etf + ",  MSF:" + msf);


});

function clearUseronclose() {

    for (var j = 0; j < combined_all_worker_activites.length; j++) {

        combined_all_worker_activites[j]['worker'].update("ActivitySid", combined_all_worker_activites[j]['activity']["Offline"]);


    }

    return true;
}


/*--------------------------new questions-js---------------------------------*/







$(document).ready(function () {
    $('.yes').click(function (event) {
        //remove all pre-existing active classes
        $(this).closest('.question-tab').removeClass('active-que');

        //add the active class to the link we clicked
        $(this).closest('.question-tab').next('.question-tab').addClass('active-que');
        event.preventDefault();
    });
});

$(document).ready(function () {
    $('.back-btn').click(function (event) {
        //remove all pre-existing active classes
        $(this).closest('.question-tab').removeClass('active-que');

        //add the active class to the link we clicked
        $(this).closest('.question-tab').prev('.question-tab').addClass('active-que');
        event.preventDefault();
    });
});

function clearErrMessage() {
    $("#client-message").html("");
    $("#agent-message").html("");
    $("#tele-message").html("");
}

//identity verification last question and retrieves customer verification script questions
$('body').on('click', '#teleNext', function () {
    var teleRefId = $('.verify-telesale-ID').val();
    const leadId = teleRefId.substring(teleRefId.lastIndexOf('-') + 1);
    var agentId = $('.verify-agent-ID').val();
    if (agentId == "") {
        $('.agent-verify-status .agent-message').html('<span class="text-danger">Please enter agent ID</span>');
        return false;
    }
    if (leadId == "") {
        $('.telesale-verify-status').html('<span class="text-danger">Please enter telesale ID</span>');
        return false;
    }

    getQuestions('customer_verification', leadId);
    clearErrMessage();
});

//Retrieve all questions according to script type and display one by one
function getQuestions(scriptType, teleSaleId) {
    $.ajax({
        type: "get",
        url: getQuestionsUrl,
        data: {
            'scriptType': scriptType,
            'teleSaleId': teleSaleId,
            'current_language' : current_language
        },
        success: function (response) {
            $(".salesagentintro").hide();
            $(".sale-detail-wrapper-Qus").html(response.html);
            $(".customer-name").html(response.customer_name);
            $(".sale-detail-wrapper-Qus").show();
            toggleQuestions(1);
            addEditIcon();
        },
        error: function (err) {
            console.log(err.message);
        }
    });
}

function addEditIcon() {
    $(".edit-tag").filter(function() {
        return $(this).attr("data-field-id") != '';
    }).append('<i class="fa fa-pencil edit-tag-icon" aria-hidden="true"></i>');
    $(".edit-tag").filter(function() {
        return $(this).attr("data-field-id") == '';
    }).css('cursor','auto');
}

//On click of Positive button from Lead verification question
$('body').on('click', '.question_yes', function () {
    var iteration = parseInt($(this).data('iteration'));
    var qusId = parseInt($(this).data('id'));
    // var isIntroQuestion = parseInt($(this).data('is_intro_question'));
    var isIntroQuestion = $('#is_intro_question_'+iteration).val();
    $('#edit_field_container').html('')
    saveAnswerData(qusId, iteration, isIntroQuestion, 1);
});

//Update progress bar and its tooltip calculation
function updateProgressBar(iteration, isIntroQuestion) {
    if(isIntroQuestion == 1){
        var questions_total = $("#questions_count").html();
        var questions_count = eval(questions_total);
        var tooltip_text = '0/'+questions_count+'  (0%)';

        $(".progress-bar").each(function () {
            $(this).width('0%');
        });

        $('.progress-tooltip').attr('data-original-title', tooltip_text)
            .tooltip({
                trigger: 'manual'
            })
            .tooltip('show');
        toggleQuestions(iteration + 1);
        // setTimeout(function(){
        //     toggleQuestions(iteration + 1);
        //     }, 1000);
    }else{
        var intro_que = $("#intro_questions_count").html();
        var questions_total = $("#questions_count").html();
        var questions_count = eval(questions_total);
        // var question_number = eval(iteration) + 1 - intro_que;
        var question_number = eval(iteration) - intro_que;
        console.log('test_question_number'+question_number);
        var progress_percentage = parseInt((eval(question_number) * 100) / eval(questions_total));
        console.log('test_progress_percentage'+progress_percentage);
        //var progress_percentage = parseInt((eval(iteration) * 100)/ eval(questions_total));
        var tooltip_text = question_number + '/' + questions_total + ' (' + progress_percentage + '%)';
        //var tooltip_text = iteration+'/'+questions_total+' ('+progress_percentage+'%)';
        // if (eval(iteration) != eval(questions_count)) {

        $(".progress-bar").each(function () {
            $(this).width(progress_percentage + '%');
        });

        $('.progress-tooltip').attr('data-original-title', tooltip_text)
            .tooltip({
                trigger: 'manual'
            })
            .tooltip('show');
        // }
        // if (eval(iteration) != eval(questions_count)) {
        //
        //     $(".progress-bar").each(function () {
        //         $(this).width(progress_percentage + '%');
        //     });
        //
        //     $('.progress-tooltip').attr('data-original-title', tooltip_text)
        //         .tooltip({
        //             trigger: 'manual'
        //         })
        //         .tooltip('show');
        // }

        console.log('iteration' + iteration + ',question_number' + question_number + ',questions_count' + questions_count);
        if (eval(question_number) == eval(questions_count)) {
            // $('#verify-lead').show();
            //   $('#confirmreleadview').show()
            $(".progress-bar").each(function () {
                $(this).width(progress_percentage + '%');
            });

            $('.progress-tooltip').attr('data-original-title', tooltip_text)
                .tooltip({
                    trigger: 'manual'
                })
                .tooltip('show');


          // toggleQuestions(iteration + 1);
          $("#last-iteration").val(iteration+1);
          $("#question_main_div_" + iteration).hide();
          $("#verifylead").show();
      } else {

        toggleQuestions(iteration + 1);
          // setTimeout(function(){
          //     toggleQuestions(iteration + 1);
          // }, 1000);
      }
    }

}

//Last question on click function
$('body').on('click', '#lastQuestionsShow', function () {
    var iterationId = $('#last-iteration').val();
    reduceProgressBar(iterationId, 0);
    // toggleQuestions(iterationId);
    $("#verifylead").hide();
});


function checkConditionsForQuestion(conditions) {
    var isQuestionShow = true;
    $.each( conditions, function( key, value ) {
        let selectedAnswer = ansArray[value.id];
        if (selectedAnswer == value.comp_val) {
            isQuestionShow = true;
        } else {
            isQuestionShow = false;
            return false;
        }
    });
    return isQuestionShow;
}

//Toggle questions for Lead verification script
function toggleQuestions(iteration, toggleState = "next") {
    let queDisplay = true;
    let conditions = $("#question_main_div_" + iteration).data('conditions');

    if (conditions.length > 0) {
        queDisplay = checkConditionsForQuestion(conditions);
    }

    $(".question_div,.tip").hide();
    if (queDisplay == true) {
        $("#question_main_div_" + iteration).show();    
        $("#question_tip_" + iteration).show();
    } else {
        if (toggleState == "previous") {
            toggleQuestions(iteration - 1, 'previous');
        } else {
            toggleQuestions(iteration + 1);
        }
    }
}

//Lead verification question's negative answer button's on click event
$('body').on('click', '.question_no', function () {
    console.log($(this).data('id'));
    let declineAction = $(this).data('dec-action');
    if (declineAction == 0) {
        $("#decline-confirmation").attr('data-id', $(this).data('id'));
        $("#decline-lead-modal").modal('toggle');
    } else {
        var iteration = parseInt($(this).data('iteration'));
        var qusId = parseInt($(this).data('id'));
        var isIntroQuestion = $('#is_intro_question_'+iteration).val();
        saveAnswerData(qusId, iteration, isIntroQuestion, 2);
    }
    $('#edit_field_container').html('');
});

//Lead verification script -> Previous button's on click event
$('body').on('click', '.previous_btn', function () {
    console.log($(this).data('iteration'));
    var iteration = parseInt($(this).data('iteration'));
    var isIntroQuestion = $('#is_intro_question_'+iteration).val();
    $('#edit_field_container').html('')
    //toggleQuestions(iteration-1);
    reduceProgressBar(iteration, isIntroQuestion);
});

//Reduce progress bar and its tooltip's calculations
function reduceProgressBar(iteration, isIntroQuestion) {
    console.log("Iteration: " + iteration)
    var questions_total = $("#questions_count").html();
    var intro_que = $("#intro_questions_count").html();
    // var questions_count = eval(questions_total) - 1;
    var question_number = eval(iteration) - 2 - intro_que;
    // var question_number = eval(iteration) - 1 - intro_que;
    var progress_percentage = parseInt((eval(question_number) * 100) / eval(questions_total));
    var tooltip_text = question_number + '/' + questions_total + ' (' + progress_percentage + '%)';


    console.log('isIntroQuestionR'+isIntroQuestion);

    var isIntroNum = iteration - 1;

    console.log("isIntroNum" + isIntroNum);

    if($('#is_intro_question_'+isIntroNum).val() == 1){

      $(".progress-bar").each(function () {
          $(this).width('0%');
      });
      $('.progress-tooltip')
          .attr('data-original-title',"")
          .tooltip({
              trigger: 'manual'
          })
          .tooltip('hide');


    }else{
        $(".progress-bar").each(function () {
            $(this).width(progress_percentage + '%');
        });
        $('.progress-tooltip')
            .attr('data-original-title', tooltip_text)
            .tooltip({
                trigger: 'manual'
            })
            .tooltip('show');

    }

    console.log("Before toggle questions: " + (eval(iteration) - 1));
    

    toggleQuestions(iteration - 1, 'previous');
}

//Confirm decline from popup
$('body').on('click', '#decline-confirmation', function () {
    $("#decline-lead-modal").modal("hide");
    console.log('-------'+$(this).data('id'));
    var question_id = $(this).data('id');
    var reference_id = '';
    declineLead(question_id);

});

//Decline lead ajax call
function declineLead(question_id) {
    // if (window.selected_script == "customer_call_in_verification") {
    //     if ($('.verify-lead-ID').val() != '') {
    //         var reference_id = $('.verify-lead-ID').val();
    //     }
    // } else {
    //     if ($('.verify-telesale-ID').val() != '') {
    //         var reference_id = $('.verify-telesale-ID').val();
    //     }
    // }
    var reference_id = getLeadReferenceId();
    console.log(reference_id);
    if(reference_id != '') {
        $.ajax({
            type: "get",
            url: leadConformDeclineUrl,
            data: {
                'reference_id': reference_id,
                'verification_method' : window.selected_script,
                'current_language': window.current_language,
            },
            success: function(response) {
                console.log(response);
                decline_lead_success = true;
                if (response.status == "success") {
                    // getDispositions(question_id);
                    $(".sale-detail-wrapper-Qus").html(response.html);
                    $('.sale-detail-wrapper-Qus').show();
                } else {
                    // getDispositions(question_id);
                }
            },
            error: function (err) {
                console.log(err.message);
            }
        });
    } else {
        getDispositions(question_id);
    }
}

$('body').on('change', '.disposition_radio', function () {


    if (!$("input[name='disposition_id']:checked").val()) {
        console.log('Nothing is checked!');
        $("#final_decline").prop("disabled", true);
        $("#hangup_decline").prop("disabled", true);
        $("#verified_reason").prop("disabled", true);
    }
    else {
        console.log('One of the radio buttons is checked!');
        $("#final_decline").removeAttr("disabled");
        $("#hangup_decline").removeAttr("disabled");
        $("#verified_reason").removeAttr("disabled");
    }


});

// $('body').on('click', '#final_decline', function() {
//     var disposition_id = $("input[name='disposition_id']:checked").val();
//     var teleRefId = $('.verify-telesale-ID').val();
//     var lead_id = teleRefId.substring(teleRefId.lastIndexOf('-') + 1);
//     console.log(lead_id);
//     console.log(disposition_id);
// });

//Submission of decline disposition
$('body').on('click', '#final_decline', function () {
    console.log(current_language);
    var reference_id = '';

    if (call_type && (call_type == "outbound" || call_type == OUTBOUND_DISCONNECT || call_type == selfVerifiedCallbackType)) {
      var reference_id = getLeadReferenceId();
    } else {
      if (window.selected_script == "customer_call_in_verification") {
          if ($('.verify-lead-ID').val() != '') {
              var reference_id = $('.verify-lead-ID').val();
          }
      } else {
          if ($('.verify-telesale-ID').val() != '') {
              var reference_id = $('.verify-telesale-ID').val();
          }
      }
    }

    if ($("input[name='disposition_id']:checked").val() != '') {
        var disposition_id = $("input[name='disposition_id']:checked").val();
    } else {
        var disposition_id = 0;
    }
    if(reference_id != '' && disposition_id > 0) {
    //var lead_id = teleRefId.substring(teleRefId.lastIndexOf('-') + 1);
    $.ajax({
        type: "get",
        url: leadDeclineUrl,
        data: {
            'reference_id': reference_id,
            'disposition_id' : disposition_id,
            'current_language' : current_language,
            'verification_method' : window.selected_script,
            'call_type': call_type,
            'taskId' : current_task_id
        },
        success: function(response) {
            if (response.status == "success") {
                console.log('-----final_decline');
                if (call_end_by_customer == true || disconnected_by_agent == true) {
                    $("#lead-decline-hangup").hide();
                    $(".sale-detail-wrapper-Qus").html("<p></p>");
                    $('.sale-detail-wrapper-Qus').hide();
                    $('#connected-agent-row').addClass('hidden');
                    $('#complete-task-row').removeClass('hidden');
                    $('.dropdown-profile-data').attr('data-toggle', 'modal');
                    $(".call_hangup_or_dropped").show();
                    postDispositionStoreHandler("lead-declined");
                } else {
                    // $(".sale-detail-wrapper-Qus").html(response.html);
                    // $('.sale-detail-wrapper-Qus').show();
                }
                $("#hangup_call_button").removeAttr("disabled");
                // $("#complete-task-button").removeAttr("disabled");
                // $("#not-ready-button").removeAttr("disabled");
                $("#call_customer_hangs_up").val("");
                $("#call_customer_lead_verify").val("");
                $("#hangup-call-button").removeAttr("disabled");

                decline_reason_stored = true;
                } else {
                    console.log(response.message);
                }
            },
            error: function (err) {
                console.log(err.message);
            }
        });
    } else {
        hangUp();
    }
});

//Submission of disconnect lead disposition
$('body').on('click', '#hangup_decline', function () {
  if (call_type && (call_type == "outbound" || call_type == OUTBOUND_DISCONNECT || call_type == selfVerifiedCallbackType)) {
    var reference_id = getLeadReferenceId();
  } else {
    if ($('.verify-telesale-ID').val() != '') {
        var reference_id = $('.verify-telesale-ID').val();
    } else if($('.verify-lead-ID').val() != ''){
        var reference_id = $('.verify-lead-ID').val();
    }else{
        var reference_id = '';
    }
  }
    console.log('hang----'+reference_id);
    if ($("input[name='disposition_id']:checked").val() != '') {
        var disposition_id = $("input[name='disposition_id']:checked").val();
    } else {
        var disposition_id = 0;
    }
    $.ajax({
        type: "get",
        url: leadDeclineUrl,
        data: {
            'disposition_id': disposition_id,
            'reference_id': reference_id,
            'call_dropped': 'yes',
            'verification_method' : window.selected_script,
            'current_language': window.current_language,
            'call_type': call_type,
            'taskId' : current_task_id
        },
        success: function (response) {
            if (response.status == "success") {
                console.log('success hangup----'+reference_id);
                console.log('call hangup or dropped 1');
                if (call_end_by_customer == true) {
                    console.log('hangup decline----');
                    callEndByCustomerOrSalesAgent();
                    $("#lead-decline-hangup").hide();
                } else {
                    hangUp();
                }
                $(".declined_lead-wrapper").hide();
                $('.verification-complete-block').hide();
                // $('.call_hangup_or_dropped').show();
                $("#hangup_call_button").removeAttr("disabled");
                // $("#complete-task-button").removeAttr("disabled");
                // $("#not-ready-button").removeAttr("disabled");
                $("#call_customer_hangs_up").val("");
                $("#call_customer_lead_verify").val("");
                postDispositionStoreHandler("call-disconnected");
            } else {
                console.log(response.message);
            }
        },
        error: function (err) {
            console.log(err.message);
        }
    });

});

$('body').on('click', '#lead-decline-hangup', function () {
    hangUp();
    console.log('call hangup or dropped 4');
    $('.verification-complete-block').hide();
    enableReadyNotReadyButtons();
    // setTimeout(function () { $('.call_hangup_or_dropped').show(); }, 1000);
});

//Retrieve dispositions
function getDispositions(queId) {
    console.log('------question id----'+queId);
    if($('.verify-telesale-ID').val() > 0){
        var reference_id = $('.verify-telesale-ID').val();
    }else{
        var reference_id = $('.verify-lead-ID').val();
    }
   var customerHangupData = $('#call_customer_hangs_up').val();
    console.log('------reference id----'+reference_id);
    if (reference_id != '' || queId != 0) {
        console.log('------reference id----'+reference_id);
    if(customerHangupData == ''){
        console.log('------customer hangs----'+customerHangupData);
        $.ajax({
            type: "get",
            url: getDispositionsUrl,
            data: {
                'queId': queId,
                'reference_id': reference_id,
                'identity_question_decline': identity_question_decline
            },
            success: function (response) {
                if (response.status == "success") {

                    var lead_telesale = $('.verify-telesale-ID').val();
                    if ((typeof queId == 'undefined' || !queId || queId.length === 0 || queId === "") && reference_id == '')
                    {
                        $("#decline-lead-modal").modal('toggle');
                    }
                    console.log('response html'+response.html);
                  //  setTimeout(function () { $(".sale-detail-wrapper-Qus").html(response.html); }, 1000);
                    $(".sale-detail-wrapper-Qus").html(response.html);

                    if (call_end_by_customer == true) {
                        callEndByCustomerOrSalesAgent();
                        disableReadyNotReadyButtons();
                    }

                    $("#customer-auth-decline-lead-modal").modal('hide');
                    $("#customer-zip-decline-lead-modal").modal('hide');
                    $("#customer-account-decline-lead-modal").modal('hide');
                    console.log('call hangup or dropped 2');
                    $('.verification-complete-block').hide();
                    // $('.call_hangup_or_dropped').hide();
                    $('.modal-backdrop').modal('hide');
                    $(".sale-detail-wrapper-Qus").show();
                    $("#hangup-call-button").prop("disabled", true);
                    $('#call_customer_hangs_up').val(1);
                    console.log('call hangup or dropped test 2');




                } else {
                    console.log(response.message);
                }
            },
            error: function (err) {
                $('#call_customer_hangs_up').val('');
                console.log(err.message);
            }
        });
    }

    } else {
      console.log("In else of get dispositions");
        if (call_end_by_customer == true) {
            console.log('call hangup or dropped 3');
            $('.call_hangup_or_dropped').show();
            hangUp();
        }
    }
}

//Save selected answers for Lead verification questions
function saveAnswerData(qusId, iteration, isIntroQuestion, selAnswer) {
    //var current_lang = $('#current_lang').val();
    console.log("Question Id: " + qusId);
    var agent_user_id = $('#agent_user_id').val();
    var agent_client_id = $('#agent_client_id').val();
    var telesale_reference_id = $('#telesale_reference_id').val();
    $('.ajax-loader').show();

    ansArray[qusId] = selAnswer;

    $.ajax({
        type: "POST",
        url: saveCustomerVerification,
        data: {
            '_token': $('#token').val(),
            'agent_user_id': agent_user_id,
            'telesale_reference_id': telesale_reference_id,
            'qusId': qusId,
            'answer': selAnswer
        },
        success: function (response) {
            $('.ajax-loader').hide();
            updateProgressBar(iteration, isIntroQuestion);
        },
        error: function (err) {
            $('.ajax-loader').hide();
        },
        statusCode: {
            403: function (xhr) {
                $('.ajax-loader').hide();
            }
        },
    });
}

// Verify lead
$('body').on('click', '#verify-lead', function () {
    // var reference_id = $('#reference_id_to_update').val();
    // console.log('---------reference id'+reference_id);
    var reference_id = getLeadReferenceId();
    $('#verify-lead').prop('disabled',true);
    $.ajax({
        type: "get",
        url: leadsaleupdate,
        data: {
            'reference_id': reference_id,
            'current_language' : current_language,
            'verification_method' : window.selected_script,
            'call_type': call_type,
            'taskId' : current_task_id
        },
        success: function (response) {
            $('#verify-lead').prop('disabled',false);
            if (response.status == "success") {
                leadVerifiedStored = true;
                $("#hangup-call-button").removeAttr("disabled");
                $("#complete-task-button").removeAttr("disabled");
                $("#not-ready-button").removeAttr("disabled");
                $('.verification_complete').html(response.data);
                $('#verifylead').hide();
                $('.progress-bar').hide();
                $(".sale-detail-wrapper-Qus").hide();
                $('.call_hangup_or_dropped').hide();
                $('.verification-complete-block').show();
                $('.questions-progress').hide();
                $('#call_customer_lead_verify').val(1);
            } else {
                //disconnectedIfError = true;
                console.log(response.message);
                alert(response.message);
                // printAjaxErrorMsg(response.message);
                //hangUp();
            }
        },
        error: function (err) {
            $('#verify-lead').prop('disabled',false);
            console.log(err.message);
            if (err.message == undefined){
                alert('Something went wrong');
            } else {
                alert(err.message);
            }
            // printAjaxErrorMsg(err.message);
        }
    });
});



$('body').on('click', '.client_cannot_verify', function () {
    $('.verify-lead-data-1').hide();
    $('.client-verification-verify-data').show();
});

$('body').on('click', '.agent_cannot_verify', function () {
    $('.verify-lead-data-2').hide();
    $('.agent-verification-verify-data').show();
});

$('body').on('click', '.tele_cannot_verify', function () {
    //$('.telesale_not_found').hide();
    $('.verify-lead-data-3').hide();
    $('.tele-verification-verify-data').show();
    lead_verified = false;
});

$('body').on('click', '#proceed-btn', function () {
    $("#teleNext").trigger('click');
});

$('body').on('click', '#cannot-transfer-btn', function () {
    $('.verify-lead-data-4').hide();
    $('.can-not-transfer').show();
    lead_verified = false;
});

$(window).on("beforeunload", function () {
    for (var j = 0; j < combined_all_worker_activites.length; j++) {
        combined_all_worker_activites[j]['worker'].update("ActivitySid", combined_all_worker_activites[j]['activity']['Offline']);
    }
});

//Retrieves customer call in script questions
function getCustomerformScripts(workspaceSid, workflowid, language, selected_script) {
    console.log(selected_script);
    $.ajax({
        type: "get",
        url: getCustomerQuestionsUrl,
        data: {
            'workspace_id': workspaceSid,
            'workflow_id': workflowid,
            'language': language,
            'selected_script' : selected_script
        },
        success: function(data) {
            console.log('-----questiondata'+data);
            console.log('--------is------'+data.question.customer_call_in_verification);
            if (data.status == 'success' && Twilio.Device.activeConnection() != undefined) {
                $('.verify-lead-ID').val('');
                $('#customer-tele-message').html('');
                $('#CustomerLeadNext').hide();
                $('.customer_tele_cannot_verify').hide();
                //$(".sale-detail-wrapper").hide();
                $(".customer-detail-wrapper").show();
                window.allquestions = data.question;
                $('.customer-welcome-message').html(data.question.customer_call_in_verification[0]['question']);
                $('.customer-telesale-verification-qus').html(data.question.customer_call_in_verification[1]['question']);

                $('.authorized-name-qus').html(data.question.customer_call_in_verification[2]['question']);
                $('#customer-auth-decline').attr('data-id',data.question.customer_call_in_verification[2]['id']);

                $('.account-number-qus').html(data.question.customer_call_in_verification[3]['question']);
                $('#customer-account-decline').attr('data-id',data.question.customer_call_in_verification[3]['id']);

                $('.zipcode-qus').html(data.question.customer_call_in_verification[4]['question']);
                $('#customer-zip-decline').attr('data-id',data.question.customer_call_in_verification[4]['id']);
                $("#client-message").removeClass("text-success");
                $('#client-message').html('');
                $('.customer-verify-lead-data-1').show();
                $('.customer-verify-lead-data-2').hide();
                $('.customer-verify-lead-data-3').hide();
                $('.customer-verify-lead-data-4').hide();
                if(call_type == 'outbound' || call_type == OUTBOUND_DISCONNECT){
                    $('.verify-lead-ID').val(lead_id);
                    $('.customerCheckTelesaleId').trigger('click');
                }
            }
        },
        error: function(err) {
            console.log(err.message);
        }
    });
}


$('body').on('click', '#CustomerLeadNext', function () {
    $('.customer-verify-lead-data-1').hide();
    $('.customer-verify-lead-data-3').hide();
    // $('.customer-verify-lead-data-2').show();
    $('.customer-verify-lead-data-4').hide();
    var leadId = $('.verify-lead-ID').val();
    console.log('---------------lead id -------'+leadId);
    const telesaleId = leadId.substring(leadId.lastIndexOf('-') + 1);

    if (telesaleId == "") {
        $('.telesale-verify-status').html('<span class="text-danger">Please enter telesale ID</span>');
        return false;
    }

    getQuestions('customer_verification', telesaleId);
    clearErrMessage();
});


$('body').on('click', '#AuthorizedNext', function () {
    $('.customer-verify-lead-data-1').hide();
    $('.customer-verify-lead-data-2').hide();
    $('.customer-verify-lead-data-3').show();
    $('.customer-verify-lead-data-4').hide();
});


$('body').on('click', '#authPre', function () {
    $('.customer-verify-lead-data-1').show();
    $('.customer-verify-lead-data-2').hide();
    $('.customer-verify-lead-data-3').hide();
    $('.customer-verify-lead-data-4').hide();
    clearErrMessage();
});

$('body').on('click', '#zipPre', function () {
    $('.customer-verify-lead-data-1').hide();
    $('.customer-verify-lead-data-2').hide();
    $('.customer-verify-lead-data-3').show();
    $('.customer-verify-lead-data-4').hide();
    clearErrMessage();
});

$('body').on('click', '#accountPre', function () {
    $('.customer-verify-lead-data-1').hide();
    $('.customer-verify-lead-data-2').show();
    $('.customer-verify-lead-data-3').hide();
    $('.customer-verify-lead-data-4').hide();
    clearErrMessage();
});

$('body').on('click', '#accountNext', function () {
    $('.customer-verify-lead-data-1').hide();
    $('.customer-verify-lead-data-2').hide();
    $('.customer-verify-lead-data-3').hide();
    $('.customer-verify-lead-data-4').show();
    clearErrMessage();
});

$("body").on('keypress', ".verify-lead-ID", function() {
  $("#customer-tele-message").html("");
});

//Check for given telesale id is valid or not for customer call in script
$('body').on('click', '.customerCheckTelesaleId', function () {
    var telesaleid = $('.verify-lead-ID').val();
    if (call_type == "outbound" || call_type == OUTBOUND_DISCONNECT) {
      console.log("Lead Refernce Id from task attributes: " + lead_id);
      console.log("Enetered Refernce Id in Textbox: " + $(".verify-lead-ID").val());
      if (lead_id != telesaleid) {
        $("#customer-tele-message").show();
        $("#customer-tele-message").html('<p class="text-danger">Please enter a valid lead ID or the verification cannot proceed.</p>');
        $("#LeadError").show();
        return false;
      } else {
        $("#customer-tele-message").hide();
        $("#LeadError").hide();
      }
    }
    console.log(telesaleid);
    if (telesaleid != "") {
        $('#customer-check-telesale-button').attr('disabled', true);
        $('#reference_id_to_update').val(telesaleid);
        $.ajax({
            type: "POST",
            url: customerLeadVerify,
            data: {
                '_token': $('#customer-token').val(),
                'telesaleid': telesaleid,
                'callid': currentConnection.parameters.CallSid
            },
            success: function (response) {
                if (response.status == 'success') {
                    lead_verified = true;
                    console.log('---------------auth name'+response.authname);
                    console.log('---------------account'+response.account_detail);
                    $('#telesale_reference_id').val(response.data.refrence_id);
                    $('#telesale_form_id').val(response.data.form_id);
                    $('#script_form_id').val(response.data.form_id);
                    $('#is_multiple').val(response.data.is_multiple);
                    $('#multiple_parent_id').val(response.data.id);
                    $('.show-auth-name').html(response.authname);
                    $('.show-zip-code').html(response.state);
                    $('.show-account-number').html(response.account_detail);

                    console.log('--------check telesale id 2');
                    $('#customer-check-telesale-button').removeAttr('disabled');
                    $("#complete-task-button").prop("disabled", true);
                    $("#not-ready-button").prop("disabled", true);

                    $("#hangup-call-button").removeAttr("disabled");

                    $("#customer-tele-message").html('<p class="text-success"><i class="fa fa-check"></i> Lead Found</p>');
                    // $("#CustomerLeadNext").show();
                    $("#CustomerLeadNext").trigger('click');
                    $("#LeadError").hide();
                    $('.customer-verify-status').show();

                } else {
                    lead_verified = false;
                    $('#customer-check-telesale-button').removeAttr('disabled');
                    $("#customer-tele-message").html('<p class="text-danger">Please enter a valid lead ID or the verification cannot proceed.</p>');
                    $("#LeadError").show();
                    $("#CustomerLeadNext").hide();
                    $('.customer-verify-status').show();
                }
            },
            fail: function () {
                $('.customer-verify-status').hide();
                $('.customer-check-telesale-button').removeAttr('disabled')
            }
        });
    }

});





$('body').on('click', '.customer-check-auth', function () {
    $(".customer-auth-verify-status").show();
    $("#customer-client-message").addClass("text-success");
    $('#customer-client-message').html('<p><i class="fa fa-check"></i>Record Found</p>');
    $("#AuthorizedNext").show();
    $("#clientError").show();
});


$('body').on('click', '#zipcodeNext', function () {
    $('.customer-verify-lead-data-4').hide();
    $('.customer-verify-lead-data-3').hide();
    $('.customer-verify-lead-data-2').hide();
    var leadId = $('.verify-lead-ID').val();
    console.log('---------------lead id -------'+leadId);
    const telesaleId = leadId.substring(leadId.lastIndexOf('-') + 1);

    if (telesaleId == "") {
        $('.telesale-verify-status').html('<span class="text-danger">Please enter telesale ID</span>');
        return false;
    }

    getQuestions('customer_verification', telesaleId);
    clearErrMessage();
});


$('body').on('click', '.customer-auth-decline-verify', function () {
    $("#customer-auth-decline-lead-modal").modal('show');
});

$('body').on('click', '#customer-auth-decline', function () {
    console.log($(this).data('id'));
    $("#customer-auth-decline-lead-modal").modal('hide');
    $('.customer-detail-wrapper').hide();
    identity_question_decline = true;
    // getDispositions($(this).data('id'));
    declineLead(0);
});

$('body').on('click', '.customer-zip-decline-verify', function () {
    $("#customer-zip-decline-lead-modal").modal('show');
});


$('body').on('click', '.customer-account-decline-verify', function () {
    $("#customer-account-decline-lead-modal").modal('show');
});

$('body').on('click', '#customer-account-decline', function () {
    console.log($(this).data('id'));
    $('.customer-detail-wrapper').hide();
    $("#customer-account-decline-lead-modal").modal('hide');
    // getDispositions($(this).data('id'));
    identity_question_decline = true;
    declineLead(0);
});

$('body').on('click', '#customer-zip-decline', function () {
    console.log($(this).data('id'));
    $('.customer-detail-wrapper').hide();
    $("#customer-zip-decline-lead-modal").modal('hide');
    // getDispositions($(this).data('id'));
    identity_question_decline = true;
    declineLead(0);
});

//Retrieve agent not found script
function getAgentNotFoundScript(workspaceSid, workflowid, language) {
    $.ajax({
        type: "get",
        url: agentNotFoundData,
        data: {
            'workspace_id': workspaceSid,
            'workflow_id': workflowid,
            'language': language,
            'selected_script' : 'agent_not_found'
        },
        success: function(data) {
            if (data.status == 'success') {
                console.log('-----------agent not found -------'+data.question);
                window.allquestions = data.question;
                $('.client-agent-not-found-data').html(data.question.agent_not_found[0]['question']);
                $('.telesale-agent-not-found-data').html(data.question.agent_not_found[0]['question']);
                $('.agent-agent-not-found-data').html(data.question.agent_not_found[0]['question']);
            }
        },
        error: function(err) {
            console.log(err.message);
        }
    });
}

//Retrieve lead not found script
function getLeadNotFoundScript(workspaceSid, workflowid, language) {
    $('.can-not-transfer-data,.tele-agent-not-found-data').html("");
    $.ajax({
        type: "get",
        url: leadNotFoundData,
        data: {
            'workspace_id': workspaceSid,
            'workflow_id': workflowid,
            'language': language,
            'selected_script' : ['lead_not_found','can_not_transfer']
        },
        success: function(data) {
            if (data.status == 'success') {
                console.log('-----------lead not found -------'+data.question);
                window.allquestions = data.question;
                $('.tele-agent-not-found-data').html(data.question.lead_not_found[0]['question']);

                if (typeof data.question.can_not_transfer != 'undefined') {
                    $('.can-not-transfer-data').html(data.question.can_not_transfer[0]['question']);
                }
            }
        },
        error: function(err) {
            console.log(err.message);
        }
    });
}


$('body').on('click', '.customer_tele_cannot_verify', function () {
    $('.customer-detail-wrapper').hide();
    $('.tele-verification-verify-data').show();
   /* $('.customer-verify-status').show();*/
});

// hide can not verified scripts
function hideVerificationSection() {
    $(".client-verification-verify-data").hide();
    $(".tele-verification-verify-data").hide();
    $(".agent-verification-verify-data").hide();
    $(".telesale-verification-verify-data").hide();
    $(".can-not-transfer").hide();
}
//Store call hangup by details in table
function storeCallHangupDetail(callHangup){
    console.log("-------------------------------------");
    console.log(callHangup);
    $.ajax({
        type:'get',
        url: hangupDetails,
        data: {
            'call_hangup': callHangup,
            'reservation_id':reservation_id
        },
        success:function(data){

        }
        
    })
}

function callEndByCustomerOrSalesAgent() {
    console.log('Call disconnected: call end by customer');
    $('#connected-agent-row').addClass('hidden');
    $('#complete-task-row').removeClass('hidden');
    $('.dropdown-profile-data').attr('data-toggle', 'modal');
    hideVerificationSection();
    $('#verifylead').hide();
    $(".verification-complete-block").hide();
    $(".customer-detail-wrapper").hide();

    if (disconnectedIfError) {
        disconnected_by_agent = false;
        enableReadyNotReadyButtons();
        return true;
    }
    if (decline_reason_stored == true) {
        updateConfirmationMessage("lead-declined");
    }

    if (decline_reason_stored == true || lead_verified == false) {
        enableReadyNotReadyButtons();
    }

    if (leadVerifiedStored == true) {
        // updateConfirmationMessage("lead-verified");
        var referenceId = getLeadReferenceId();
        // retrieveDispositionsHandler("customerhangup", referenceId);
        retrieveDispositionsHandler("verified", referenceId);
    }

    //Store who hangup the call agent or customer
    let call_hanugup_by;
    if(disconnected_by_agent == true){
        call_hanugup_by = 'agent';
    }
    else if(call_end_by_customer == true){
        call_hanugup_by = 'customer';
    }
    storeCallHangupDetail(call_hanugup_by);
    return true;
}

//Enable ready - not ready buttons
function enableReadyNotReadyButtons() {
    console.log("within enableReadyNotReadyButtons");
    $("#complete-task-button").removeAttr("disabled");
    $("#not-ready-button").removeAttr("disabled");
}

//Disable ready - not ready buttons
function disableReadyNotReadyButtons () {
    $("#complete-task-button").prop("disabled", true);
    $("#not-ready-button").prop("disabled", true);
}

//Returns language by its short form
function getLanguage(lang) {
    if (lang == "en") {
        return "English";
    } else if (lang == "es") {
        return "Spanish";
    } else {
        return "";
    }
}

//Function executes when outbound reservation create and show requires info on accept / reject popup
function outboundResevationCreation(attributes) {
  $("#call-type").text("Outbound Call");
  if (attributes.selected_language) {
      $('.call_language').text("Language: " + getLanguage(attributes.selected_language));
      $('.call_language').show();
  }
  if (attributes.cust_num) {
      $('.call_cust').text("Customer Contact: " + phonenumber_format(attributes.cust_num));
      $('.call_cust').show();
  }
}

//Function executes when self tpv outbound reservation create and show requires info on accept / reject popup
function selfVerifiedCallbackCreation(attributes) {
    $("#call-type").text("Self TPV Verification Call");
    if (attributes.selected_language) {
        $('.call_language').text("Language: " + getLanguage(attributes.selected_language));
        $('.call_language').show();
    }
    if (attributes.cust_num) {
        $('.call_cust').text("Customer Contact: " + phonenumber_format(attributes.cust_num));
        $('.call_cust').show();
    }
    $('#telesale_reference_id').val(attributes.lead_id);
}
function outboundDisconnectResevationCreation(attributes) {
    $("#call-type").text("Outbound Call After Disconnected");
    if (attributes.selected_language) {
        $('.call_language').text("Language: " + getLanguage(attributes.selected_language));
        $('.call_language').show();
    }
    if (attributes.cust_num) {
        $('.call_cust').text("Customer Contact: " + phonenumber_format(attributes.cust_num));
        $('.call_cust').show();
    }
    $('#telesale_reference_id').val(attributes.lead_id);
}

//Function executes when inbound reservation create and show requires info on accept / reject popup
function inboundResevationCreation() {
  $("#call-type").text("Incoming Call");
    $('.call_language').text("");
    $('.call_language').hide();
    $('.call_cust').text("");
    $('.call_cust').hide();
}

//Hide reschedule button for inbound calls
function hideInboundCallComponents() {
  $("#reschedule-call").hide();
}

//Hide call disconnect / decline view
function hideCallDisconnectedView() {
  $(".call-disconnected-view").hide();
}

//Show call disconnect / decline view
function showCallDisconnectedView() {
  console.log("call_type" + call_type);
  if (call_type == "inbound") {
    hideInboundCallComponents();
  }
  console.log("showCallDisconnectedView end");
  $(".call-disconnected-view").show();
}

//Returns lead reference id
function getLeadReferenceId() {
  if ((call_type == "outbound" || call_type == OUTBOUND_DISCONNECT || call_type == selfVerifiedCallbackType) && window.lead_id) {
    var referenceId = lead_id;
  } else if($('.verify-telesale-ID').val() != "") {
      var referenceId = $('.verify-telesale-ID').val();
  } else {
      var referenceId = $('.verify-lead-ID').val();
  }
  return referenceId;
}

//Function call when call disconnects
function callDisconnectedHandler() {
  console.log("callDisconnectedHandler");
  if (window.call_type == "inbound") {
    var referenceId = getLeadReferenceId();
    console.log("callDisconnectedHandler ref id: " + referenceId);
    if (referenceId == "") {
      console.log("Call disconnected");
      return false;
    }
  }
  console.log("callDisconnectedHandler end");
  showCallDisconnectedView();
}

//On click of Disconnect button and retrieves call disconnected dispositions
$('body').on('click', "#disconnect-call", function() {
    setButtonActive("disconnect-call");
  console.log("Disconnected call function");
  var referenceId = getLeadReferenceId();
  retrieveDispositionsHandler("customerhangup", referenceId);
});

//On click of Decline button and retrieves call declined dispositions
$('body').on('click', "#decline-call", function() {
    setButtonActive("decline-call");
  console.log("Declined call function");
  var referenceId = getLeadReferenceId();
  retrieveDispositionsHandler("decline", referenceId);
});

//Retrieve Dispositions by disposition's type and reference id
function retrieveDispositionsHandler(disType, referenceId) {
  console.log("Retrieve Dispositions");
  $.ajax({
      type: "POST",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: retrieveDispositions,
      data: {
          'disType': disType,
          'referenceId': referenceId
      },
      success: function(res) {
        if (res.status == "success") {
            $(".dispositions-outer").html(res.data.view);
            if (res.data.totalReasons && res.data.totalReasons > 0) {
                disableReadyNotReadyButtons();
            } else {
                enableReadyNotReadyButtons();
            }
        } else {
          console.log(res.message);
        }
      }, error: function(err) {
        console.log(err);
      }
  });
}

//Operations perform after submitting dispositions
function postDispositionStoreHandler(callEndType) {
    hideCallDisconnectedView();
    enableReadyNotReadyButtons();
    $(".dispositions-outer").html("");
    updateConfirmationMessage(callEndType);
}

//Reschedule call
$("body").on('click', '#reschedule-call', function() {
    setButtonActive("reschedule-call");
  $("#reschedule-call").attr("disabled", true);
  var referenceId = getLeadReferenceId();
  $.ajax({
      method: "POST",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: rescheduleCall,
      data: {
          'referenceId': referenceId
      },
      success: function(res) {
        if (res.status == "success") {
          console.log(res.message);
        } else {
          console.log(res.message);
        }
        $("#reschedule-call").attr("disabled", false);
        postDispositionStoreHandler("call-rescheduled");
      }, error: function(err) {
        console.log(err);
      }
  });
});

//Store verified disposition for lead
$("body").on('click', '#verified_reason', function() {
    $("#verified_reason").attr("disabled", true);
    var referenceId = getLeadReferenceId();
    var dispositionId = $("input[name='disposition_id']:checked").val();
    $.ajax({
        method: "POST",
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: storeVerifiedDisposition,
        data: {
            'referenceId': referenceId,
            'dispositionId': dispositionId,
            'taskId' : current_task_id
        },
        success: function(res) {
        if (res.status == "success") {
            console.log(res.message);
        } else {
            console.log(res.message);
        }
        // $("#reschedule-call").attr("disabled", false);
        postDispositionStoreHandler("lead-verified");
        }, error: function(err) {
            console.log(err);
        }
    });
});

//Update message for call
function updateConfirmationMessage(newConfirmation) {
    var str = "";
    switch(newConfirmation.toLowerCase()) {
        case 'lead-verified':
            str = "Lead Verified";
            break;
        case 'lead-declined':
            str = "Lead Declined";
            break;
        case 'call-disconnected':
            str = "Call Disconnected";
            break;
        case 'call-rescheduled':
            str = "Call Rescheduled";
            break;

            default:
            str = "Call Disconnected";
            break;
    }
    $('.call_hangup_or_dropped').html(str);
    setTimeout(function () { $('.call_hangup_or_dropped').show(); }, 500);
}

//Set active disconnected screen buttons 
function setButtonActive(buttonId) {
    $(".call-disconnected-inner .btn").removeClass("active");
    $("#"+buttonId).addClass("active");
}


