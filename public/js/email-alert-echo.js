function processMinMax(module) {

    $modalCon = module.closest(".mymodal").attr("id");

    $apnData = module.closest(".mymodal");

    $modal = "#" + $modalCon;

    $(".modal-backdrop").addClass("d-none");

    $($modal).toggleClass("min");

    var htmal_data = $apnData.find("iframe").contents().find('body').html();
    if ($($modal).hasClass("min")) {

        $(".minmaxCon").append($apnData);
        $(".minmaxCon").find("iframe").contents().find('body').html(htmal_data);
        module.find("i").toggleClass('fa-minus').toggleClass('fa-clone');

    } else {
        $(".container").append($apnData);
        $(".container").find("iframe").contents().find('body').html(htmal_data);
        module.find("i").toggleClass('fa-clone').toggleClass('fa-minus');

    };

}

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
      })
      .listenForWhisper('alert-modal-min_max', (e) => {
        // Minimize / maximize All modals
        if(e.id) {
            // $("#"+e.id+" .modalMinimize").trigger("click");
            let module_ = $("#"+e.id+" .modalMinimize");
            processMinMax(module_);
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

$(document).ready(function() {


    var $content, $modal, $apnData, $modalCon;
    
    $content = $(".min");
    
    
    //To fire modal
    $(".mdlFire").click(function(e) {
    
        e.preventDefault();
    
        var $id = $(this).attr("data-target");
    
        $($id).modal({
            backdrop: false,
            keyboard: false
        });
    
    });


    
    
    $(".modalMinimize").on("click", function() {
        $modalCon = $(this).closest(".mymodal").attr("id");
        processMinMax($(this));
        window.Echo.private('emails')
            .whisper('alert-modal-min_max', {"id":$modalCon});
    });
    
});