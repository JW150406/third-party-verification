
var currentAgentId;
var currentConnection;
var workerToken;
var $callStatus = $('#call-status');
var $connectAgent1Button = $("#connect-agent1-button");
var $connectAgent2Button = $("#connect-agent2-button");

var answerCallButton_element =  "#answer-call-button";
var declineCallButton_element =  "#decline-call-button";
var mutecall_element =  ".mute-call";
var unmutecall_element =  ".unmute-call";
var hangupCallButton =  "#hangup-call-button";
var  clientErrorButton = "#clientError";



   $connectAgent1Button.on('click', { agentId: $(this).data('rel') }, agentClickHandler);
    // $connectAgent1Button.on('click',function(){
    //     alert($(this).data('rel'));
    // });
    $connectAgent2Button.on('click', { agentId: 'agent2' }, agentClickHandler);
    $('body').on('click',hangupCallButton, hangUp);
    // $('body').on('click',clientErrorButton, hangUp);

// This function not in use
function fetchToken(agentId=null) {
    $.post('/conference/token', {}, function(data) {
        currentAgentId = data.agentId;
        workerToken = data.token;
        connectClient(data.token)
    }, 'json');
}

// get new token
function fetchNewToken() {
    $.post(tokenurl, {}, function(data) {
        window.device_unique_number = data.device_unique_number;
        Twilio.Device.setup(data.token);
    }, 'json');
}

function connectClient(token) {
    Twilio.Device.setup(token, {
        audioConstraints: {
            mandatory: {
                googAutoGainControl: false
            }
        }
    });
}

Twilio.Device.ready(function (device) {
    console.log("Twilio device is ready for use.");
    updateCallStatus("Ready");
    agentConnectedHandler(device._clientName);
    Twilio.Device.sounds.incoming(false);
});

    // Callback for when Twilio Client receives a new incoming call
    Twilio.Device.incoming(function(connection) {
        call_end_by_customer = false;
        lead_verified = false;
        decline_lead_success = false;
        identity_question_decline = false;
        decline_reason_stored = false;
        leadVerifiedStored = false;
        ansArray = [];
        $(".call-disconnected-inner .btn").removeClass("active");
        $('#call_customer_lead_verify').val("");
      console.log('==== connection ====',connection);
      //$('#incall').modal('show');
        /*$('#connected-agent-row').removeClass('hidden');*/
        $(answerCallButton_element).removeClass('hidden');
        $(answerCallButton_element).removeAttr('disabled');
        $("#clientNext").hide();
        //$("#telesale_not_found").hide();

      currentConnection = connection;

      updateCallStatus("Incoming support call");

      // Set a callback to be executed when the connection is accepted
      connection.accept(function() {

        $(document).on("keydown", disableF5);

        /*  window.history.pushState(null, "", window.location.href);
          window.onpopstate = function() {
              window.history.pushState(null, "", window.location.href);
          };
      */

        updateCallStatus("In call with customer");
        /*$(answerCallButton_element).addClass('hidden');
        $(answerCallButton_element).attr('disabled',true);*/
        $(hangupCallButton).removeClass('hidden');
        $(hangupCallButton).removeAttr('disabled');
        $('.audio-controls').removeClass('hidden');

        // connection.audio.on('inputVolume', function(){
        //   console.log(volume);
        // });
       // console.log('workers activity  ==== ',activitySids["Busy"]);

      });

    });

    function incomingCallHandler() {
          $('#incall').modal('hide');
          $('#connected-agent-row').removeClass('hidden');
          $('#user-status-box .chat').show();
          $(hangupCallButton).removeClass('hidden');
          $(hangupCallButton).removeAttr('disabled');
          $('.audio-controls').removeClass('hidden');
      }


    // Set a callback on the answer button and enable it
    $('body').on('click', answerCallButton_element,function(e) {
        var connection = Twilio.Device.activeConnection();
        console.log("Answer button clicked.... ");
        console.log(connection);
        connection.accept();
        incomingCallHandler();
    });

    $('body').on('click',declineCallButton_element,function() {
      var connection = Twilio.Device.activeConnection();
      console.log("Reject button clicked.... ");
      connection.reject();

      // Reject the reservation
      try {
          combined_all_worker_activites.forEach((item, i) => {
            let worker = new Twilio.TaskRouter.Worker(window['workerToken' + i]);
            // get all reservations and reject it
            var queryParams = { "ReservationStatus": "pending" };
            worker.fetchReservations(
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
                        window['activitySids0']['Unavailable'],
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
          });
      } catch (e) {
        console.error("Failed rejecting the reservation", e);
      } finally {

      }

    });

    $('body').on('click',mutecall_element,function() {
      var connection = Twilio.Device.activeConnection();
      connection.mute(false);
      $(mutecall_element).addClass('hidden');
      $(unmutecall_element).removeClass('hidden');


    });
    $('body').on('click',unmutecall_element,function() {
      var connection = Twilio.Device.activeConnection();
      connection.mute(true);
      $(unmutecall_element).addClass('hidden');
      $(mutecall_element).removeClass('hidden');


    });

    // Check Twillio Device status
    setInterval(function(){ 
        let status = Twilio.Device.status();
        // console.log(status +" : "+Date());
        if (status == 'offline') {
            console.log(status +" : "+Date());
            setTimeout(function(){
                if (Twilio.Device.status() == 'offline') {
                    console.log(status +" after 10 sec : "+Date());
                    fetchNewToken();
                }
            },10000);
        }
    }, 5000);

    /* Report any errors to the call status display */
    Twilio.Device.error(function (error) {
        console.error(error);
        if (typeof error.code != 'undefined' && error.code == 31205) {
            fetchNewToken();
        }
        updateCallStatus("ERROR: " + error.message);
        disableConnectButtons(false);
    });

    // Callback for when the call finalizes
    Twilio.Device.disconnect(function(connection) {
        unblockanchor();
      $(document).off("keydown", disableF5);
      $('a').removeAttr('disabled');
      callEndedHandler();
      $('.audio-controls').addClass('hidden');
      if (disconnected_by_agent == false) {
          console.log("call end by cutomer");
          call_end_by_customer = true;
      }
      callEndByCustomerOrSalesAgent();
    });


    /* End a call */
    function hangUp() {
      console.log("hangup !!");
      clearTimeout(t);
      $('.questions-progress').hide();
      window.disconnected_by_agent = true;
      Twilio.Device.disconnectAll();
      callEndByCustomerOrSalesAgent();
      // console.log('workerSid====' , workerSid);
        // fetchToken(workerSid);
    }

    function agentClickHandler(e) {

     //   var agentId = e.data.agentId;
        var agentId = e.delegateTarget.dataset.rel;


      disableConnectButtons(true);
      fetchToken(agentId);
    }

    function agentConnectedHandler(agentId) {
     $('#connect-agent-row').addClass('hidden');
    //  $('#connected-agent-row').removeClass('hidden');
     // updateCallStatus("Connected as: " + agentId);

    }

    function callEndedHandler() {

      $(hangupCallButton).attr('disabled',true);
      //$(answerCallButton_element).attr('disabled',true);

     // updateCallStatus("Connected as: " + agentId);
    }

    function disableConnectButtons(disable) {
      $connectAgent1Button.prop('disabled', disable);
      $connectAgent2Button.prop('disabled', disable);
    }

    function updateCallStatus(status) {
      $callStatus.text(status);
    }
    function disableF5(e) {
       if ((e.which || e.keyCode) == 116) e.preventDefault();
      };
    

    