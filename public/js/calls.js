
$(document).ready( function() {
	var notifs = [];	
	var applicationSid = "AP3219d6e242854380b4fa67e6cb7e2305";
	var remotePhoneNumber = "";

	var defaultNotifOpts={
		"delay": 3000 };
 var longNotifOpts = {
  "delay": 9000000 };
 var dialerCallTimeout = (1000)*1;
 var callerId = null;
 var mainCallerId = null;
 var bMute = false;
 var bHold = false;
 var inDialer = false;
 var currentCallSid = null;


  function loadTwilioDevice() {
    Twilio.Device.setup(token, {debug: true});
    Twilio.Device.ready(function () {
    });
    Twilio.Device.error(function (error) {
      console.error("twilio device error ", error);
      showError("Error in Twilio Device");
    });
    Twilio.Device.connect(function (conn) {
      console.log("twilio device connected ", conn);
      currentCallSid = conn.parameters.CallSid;
      showNotifTimer("Call with " +remotePhoneNumber);
    });
    Twilio.Device.disconnect(function (conn) {
      cleanup();
    });
    Twilio.Device.incoming(function (conn) {
    });
    Twilio.Device.offline(function() {
    });
    Twilio.Device.cancel(function() {
    });
  }

  function initializeDialer() {
    $.getJSON("/twilio/token", function( result ) {
      console.log("Received Twilio Token", result);
      var token = result.twilio_token;
      loadTwilioDevice( token );
     });
  }
  function callNumber(number) {
    var conn = Twilio.Device.activeConnection();
    if (conn) {
      alert("Please hangup current call before dialing new number..");
      return;
    }

		remotePhoneNumber=number;
    $.notifyClose();
		var callingText = "<h5>Calling " + remotePhoneNumber+"</h5>";
		callingText += "<br/><button class='btn btn-danger' onclick='Dialer_Hangup()'>Hangup</button>";

		showWarning(callingText, longNotifOpts);
		var params = {"PhoneNumber": number, "CallerId": mainCallerId, "outgoing": "1" };
    console.log("Dialer_StartCall call params", params);
		Twilio.Device.connect(params);
  }
  function showNotif(settings, opts, dontClose) {
		if(notifs.length>0 && !dontClose){
		 	notifs.forEach( function( notif ) {
		 	   notif.close();
		 	} );
		}
		opts['delay']=opts['delay']||99999999;
		var notif = $.notify( settings, opts );
		notifs.push( notif );
		return notif;
	}

	function showWarning(message, opts) {
		opts=opts||defaultNotifOpts;
		opts['type']="warning";
		showNotif({ message: message }, opts);
	}
	function showSuccess(message, opts) {
		opts=opts||defaultNotifOpts;
		opts['type']="success";
		showNotif({ message: message },opts);
	}
	function showError(message, opts) {
		opts=opts||defaultNotifOpts;
		opts['type']="danger";
		showNotif({ message: message }, opts);
	}

	function showNotifTimer(message) {
		var timerInterval=1000;
		var totalSeconds=0;
		var iTime = new Date;
		var myNotif = showNotif({
			message: "" },{
			//onClosed: sipHangUp,
			type: "info",
			delay: 9999999 });
		var main = $("<div></div>");
		var center = $("<center class='c2c-in-call'></center>") ;
		center.appendTo( main );
		var content = $("<div class='content'></div>").appendTo(center);
		$("<span><h2>"+message+"</h2></span>").appendTo(content);
		//timer
		$("<span><h1 class='timer'></h1></span>").appendTo(content);
		$("<hr></hr>").appendTo(center);
		//call control
		var buttons = $("<ul style='list-style: none !important; ' class='buttons'><h4>Call Control</h4></ul>").appendTo(center);
		$("<li><button class='btn btn-danger' onclick='Dialer_Hangup()'>Hangup</button></li>").appendTo(buttons);
		$("<li><button class='btn btn-primary muter' onclick='Dialer_Mute()'></button></li>").appendTo(buttons);
		$("<li><button class='btn btn-primary holder' onclick='Dialer_Hold()'></button></li>").appendTo(buttons);
		function calculateTime()
		{
		    ++totalSeconds;
		    return pad(parseInt(totalSeconds/60))+":"+pad(totalSeconds%60);
		}

		function pad(val)
		{
		    var valString = val + "";
		    if(valString.length < 2)
		    {
			return "0" + valString;
		    }
		    else
		    {
			return valString;
		    }
		}
		function onInterval() {
		 var newTime=calculateTime();
		 var muteText ="", holdText = "";
		 if(bMute){
		    muteText="Unmute";
		 } else {
		    muteText="Mute";
		 }
      if(bHold) {
		    holdText="Unhold";
		 } else {
		    holdText="Hold";
		 }

		 center.find(".timer").text( newTime );
		 center.find(".muter").text( muteText );
		 center.find(".holder").text( holdText );
		 
		 myNotif.update({
			'message':main.html(),
			'type': 'info' });
		 }
		callInterval = setInterval( onInterval, 1000 );
		return myNotif;
	}

  if ( typeof StartTwilio !== 'undefined' && StartTwilio ) {
    initializeDialer();
  }
});
