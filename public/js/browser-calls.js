/**
 * Twilio Client configuration for the browser-calls-django
 * example application.
 */

// Store some selectors for elements we'll reuse
var $_this = jQuery;
 
var answerButton = $_this(".answer-button");
var answerButton_class = ".answer-button";
var callSupportButton_class = ".call-support-button";
var hangUpButton_class = ".hangup-button";
var callCustomerButtons_class = ".call-customer-button";

/* Helper function to update the call status bar */
function updateCallStatus(status) {
    $_this("#call-status").html(status);
}

/* Get a Twilio Client token with an AJAX request */
jQuery(document).ready(function() {
    jQuery.post("/token", {forPage: window.location.pathname}, function(data) {
        // Set up the Twilio Client Device with the token
        Twilio.Device.setup(data.token);
    });
});

/* Callback to let us know Twilio Client is ready */
Twilio.Device.ready(function (device) {
    updateCallStatus("Ready");
});

/* Report any errors to the call status display */
Twilio.Device.error(function (error) {
    updateCallStatus("ERROR: " + error.message);
});

/* Callback for when Twilio Client initiates a new connection */
Twilio.Device.connect(function (connection) {
    // Enable the hang up button and disable the call buttons
    
    $_this(hangUpButton_class).removeAttr('disabled');
    $_this(callCustomerButtons_class).attr('disabled','disabled');
    $_this(callSupportButton_class).attr('disabled','disabled');
    $_this(answerButton_class).attr('disabled','disabled');
     
   

    // If phoneNumber is part of the connection, this is a call from a
    // support agent to a customer's phone
    if ("phoneNumber" in connection.message) {
        updateCallStatus("In call with " + connection.message.phoneNumber);
    } else {
        // This is a call from a website user to a support agent
        updateCallStatus("In call with support");
    }
});

/* Callback for when a call ends */
Twilio.Device.disconnect(function(connection) {
    // Disable the hangup button and enable the call buttons
  
    $_this(hangUpButton_class).attr('disabled','disabled');
    $_this(callCustomerButtons_class).removeAttr('disabled');
    $_this(callCustomerButtons_class).removeAttr('disabled');
    
    
    updateCallStatus("Ready");
});

/* Callback for when Twilio Client receives a new incoming call */
Twilio.Device.incoming(function(connection) {
    
    updateCallStatus("Incoming support call");

    // Set a callback to be executed when the connection is accepted
    connection.accept(function() {
        updateCallStatus("In call with customer");
    });
    $_this(answerButton_class).removeAttr('disabled');
 
    // Set a callback on the answer button and enable it
     $_this('body').on('click',answerButton_class,function(){
           connection.accept();
     })
     
   
});

/* Call a customer from a support ticket */
function callCustomer(phoneNumber) {
    updateCallStatus("Calling " + phoneNumber + "...");

    var params = {"phoneNumber": phoneNumber};
    Twilio.Device.connect(params);
}

/* Call the support_agent from the home page */
function callSupport() {
    updateCallStatus("Calling support...");

    // Our backend will assume that no params means a call to support_agent
    Twilio.Device.connect();
}

/* End a call */
function hangUp() {
    Twilio.Device.disconnectAll();
}
