$(document).on('click', '.load-communication-modal', function () {
    var thiss = $(this);
    var object_type = $(this).data('object');
    var object_id = $(this).data('id');
    var load_attached = $(this).data('attached');

    $.ajax({
        type: "GET",
        url: "/chat-messages/" + object_type + "/" + object_id + "/loadMoreMessages",
        data: {
            limit: 1000,
            load_attached: load_attached
        },
        beforeSend: function () {
            //$(thiss).text('Loading...');
        }
    }).done(function (response) {
        var j = 0;
        var li = '<div class="speech-wrapper">';
        (response.messages).forEach(function (message) {
            // Set empty image var
            var media = '';
            var imgSrc = '';

            // Check for attached media (ERP attached media)
            if (load_attached == 1 && message.mediaWithDetails && message.mediaWithDetails.length > 0) {
                for (var i = 0; i < message.mediaWithDetails.length; i++) {
                    // Get image to display
                    imgSrc = getImageToDisplay(message.mediaWithDetails[i].image);
                    var productId = message.mediaWithDetails[i].product_id;

                    // Set media
                    if (imgSrc != '') {
                        media = media + '<div class="col-4"><a href="' + message.mediaWithDetails[i].image + '" target="_blank"><input type="checkbox" name="product" value="' + productId + '" id="cb1_' + i + '" /><label class="label-attached-img" for="cb1_' + i + '"><img src="' + imgSrc + '" style="max-width: 100%;"></label></a></div>';
                    }
                }
            }

            // check for media with details
            if (load_attached == 1 && message.media && message.media.length > 0) {
                for (var i = 0; i < message.media.length; i++) {
                    // Get image to display
                    imgSrc = getImageToDisplay(message.media[i]);

                    // Set media
                    if (imgSrc != '') {
                        media = media + '<div class="col-4"><a href="' + message.media[i] + '" target="_blank"><img src="' + imgSrc + '" style="max-width: 100%;"></a></div>';
                    }
                }
            }


            // Do we have media sent with the message?
            if (media != '') {
                media = '<div style="max-width: 100%; margin-bottom: 10px;"><div class="row">' + media + '</div></div>';
            }

            // Check for media URL
            if (message.media_url != null) {
                // Get image to display
                imgSrc = getImageToDisplay(message.media_url);

                // Display media in chat
                if (message.type == "supplier") {
                    media = '<input type="checkbox" name="checkbox[]" value="' + imgSrc + '" id="cb1_m_' + j + '" style="border: 3px solid black;"/><a href="' + message.media_url + '" target="_blank"><img src="' + imgSrc + '" style="max-width: 100%;"></a>';
                    j++;
                } else {
                    media = '<a href="' + message.media_url + '" target="_blank"><img src="' + imgSrc + '" style="max-width: 100%;"></a>'; // + media;
                }

            }

            // Set empty button var
            var button = "";
            if (message.type == "task") {
                if (message.status == 0 || message.status == 5 || message.status == 6) {
                } else if (message.status == 4) {
                } else {
                    button += "<a href='#' class='btn btn-xs btn-secondary ml-1 resend-message' data-id='" + message.id + "'>Resend (" + message.resent + ")</a>";
                    button += "<a href='#' class='btn btn-image ml-1 reminder-message' data-id='" + message.id + "' data-toggle='modal' data-target='#reminderMessageModal'><img src='/images/reminder.png' /></a>";
                }
            }

            if (message.inout == 'in') {
                li += '<div class="bubble"><div class="txt"><p class="name"></p><p class="message">' + media + message.message + button + '</p><br/><span class="timestamp">' + message.datetime.date.substr(0, 19) + '</span></div><div class="bubble-arrow"></div></div>';
            } else if (message.inout == 'out') {
                li += '<div class="bubble alt"><div class="txt"><p class="name alt"></p><p class="message">' + media + message.message + button + '</p><br/><span class="timestamp">' + message.datetime.date.substr(0, 19) + '</span></div> <div class="bubble-arrow alt"></div></div>';
            } else {
                li += '<div>' + index + '</div>';
            }
        });

        li += '</div>';

        if ($('#chat-list-history').length > 0) {
            $("#chat-list-history").find(".modal-body").html(li);
            $(thiss).html("<img src='/images/chat.png' alt=''>");
            $("#chat-list-history").modal("show");
        } else {
            $("#chat-history").html(li);
        }

    }).fail(function (response) {
        $(thiss).text('Load More');

        alert('Could not load more messages');

        console.log(response);
    });
});

function getImageToDisplay(imageUrl) {
    // Trim imageUrl
    imageUrl = imageUrl.trim();

    // Set empty imgSrc
    var imgSrc = '';

    // Set image type
    var imageType = imageUrl.substr(imageUrl.length - 4).toLowerCase();
    console.log(imageUrl);
    console.log(imageUrl.length);
    console.log(imageType);

    // Set correct icon/image
    if (imageType == '.jpg' || imageType == 'jpeg') {
        imgSrc = imageUrl;
    } else if (imageType == '.png') {
        imgSrc = imageUrl;
    } else if (imageType == '.gif') {
        imgSrc = imageUrl;
    } else if (imageType == 'docx' || imageType == '.doc') {
        imgSrc = '/images/icon-word.svg';
    } else if (imageType == '.xlsx' || imageType == '.xls' || imageType == '.csv') {
        imgSrc = '/images/icon-excel.svg';
    } else if (imageType == '.pdf') {
        imgSrc = '/images/icon-pdf.svg';
    } else if (imageType == '.zip' || imageType == '.tgz' || imageType == 'r.gz') {
        imgSrc = '/images/icon-zip.svg';
    } else {
        imgSrc = '/images/icon-file-unknown.svg';
    }

    // Return imgSrc
    return imgSrc;
}