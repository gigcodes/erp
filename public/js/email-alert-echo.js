if(config.pusher.key) {

    let email_data_alert = null;

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: config.pusher.key,
        cluster: config.pusher.cluster,
        forceTLS: true,
        wsHost: window.location.hostname,
        wsPort: 6001,
        forceTLS: false,
        disableStats: true,
    });
    
        // Subscribe to the public channel called "public-channel"
    window.Echo.private('emails')
    .listen('.email.received', (e) => {
        // Display the "message" in an alert box
        if(!($("#emailAlertModal").data('bs.modal')?.isShown)) {
            email_data_alert = e.email;
            var myFrame = $("#emailAlert-modal-body-myframe").contents().find('body'); 
            var textareaValue = e.email.message;
            myFrame.html(textareaValue); 
            $("#emailAlert-modal-subject").html(e.email.subject);
            $('#emailAlertModal').modal('show'); 
        }
    });

    window.Echo.private('emails')
    .listenForWhisper('alert-modal-closed', (e) => {
        if($("#emailAlertModal").data('bs.modal')?.isShown) {
            $('#emailAlertModal').modal('hide'); 
        }
      });

    $("#emailAlertModal").on("hidden.bs.modal", function () {
        window.Echo.private('emails')
        .whisper('alert-modal-closed', {});
    });

    $("#emailAlert-reply").on("click",function(){
        openQuickMsg(email_data_alert);
        $("#view-quick-email").modal('show');
    });


   
}
