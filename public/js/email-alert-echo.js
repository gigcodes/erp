var modalContentQueue = [];

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

function loadModal() {
    if (modalContentQueue.length > 0) {
        let firstEvent = modalContentQueue[0];
        email_data_alert = firstEvent.email;
        var myFrame = $("#emailAlert-modal-body-myframe").contents().find('body');
        var textareaValue = firstEvent.email.message;
        myFrame.html(textareaValue);
        $("#emailAlert-modal-subject").html(firstEvent.email.subject);
        $('#emailAlertModal').attr('data-shown', firstEvent.email.id);
        $('#emailAlertModal').modal('show');
        $('#emailAlertModal').css('display', 'block');
    }
}

if (config.pusher.key) {
    let email_data_alert = null;
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: config.pusher.key,
        cluster: config.pusher.cluster,
        // forceTLS: true,
        wsHost: window.location.hostname,
        wsPort: 6001,
        forceTLS: true,
        disableStats: true,
        encrypted: true,
    });

    window.Echo.private('emails')
        .listen('.email.received', (e) => {
            modalContentQueue.push(e);
            if (!($("#emailAlertModal").data('bs.modal')?.isShown)) {
                loadModal();
            }
        });

    window.Echo.private('emails')
        .listenForWhisper('alert-modal-closed', (e) => {
            if ($('#emailAlertModal').attr('data-shown') === e.id) {
                $('#emailAlertModal').modal('hide');
                $('.modal-backdrop').hide();
            }
            modalContentQueue = modalContentQueue.filter(function (item) {
                return item.email.id !== parseInt(e.id);
            });
            loadModal();
        })
        .listenForWhisper('alert-modal-min_max', (e) => {
            if (e.id) {
                let module_ = $("#" + e.id + " .modalMinimize");
                processMinMax(module_);
            }
        });

    $("#emailAlertModal").on("hide.bs.modal", function (e) {
        var id = $('#emailAlertModal').attr('data-shown')
        window.Echo.private('emails')
            .whisper('alert-modal-closed', { id });
    });

    $("#emailAlertModal .btn").on("click", function (e) {
        var id = $('#emailAlertModal').attr('data-shown')
        window.Echo.private('emails')
            .whisper('alert-modal-closed', { id });
    });

    $("#emailAlert-reply").on("click", function () {
        openQuickMsg(email_data_alert);
        $("#view-quick-email").modal('show');
    });
}

$(document).ready(function () {
    var $content, $modal, $apnData, $modalCon;
    $content = $(".min");
    $(".mdlFire").click(function (e) {

        e.preventDefault();
        var $id = $(this).attr("data-target");

        $($id).modal({
            backdrop: false,
            keyboard: false
        });

    });

    $(".modalMinimize").on("click", function () {
        $modalCon = $(this).closest(".mymodal").attr("id");
        processMinMax($(this));
        window.Echo.private('emails')
            .whisper('alert-modal-min_max', { "id": $modalCon });
    });

});
