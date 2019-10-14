$(document).on('click', '.load-message-modal', function () {
    var thiss = $(this);
    var vendor_id = $(this).data('id');
    var load_attached = $(this).data('attached');

    $.ajax({
        type: "GET",
        url: "/chat-messages/vendor/" + vendor_id + "/loadMoreMessages",
        data: {
            limit: 1000,
            load_attached: load_attached
        },
        beforeSend: function () {
            //$(thiss).text('Loading...');
        }
    }).done(function (response) {
        var li = '<div class="speech-wrapper">';
        (response.messages).forEach(function (message) {
            // Set empty image var
            var media = '';
            var imgSrc = '';

            // Check for attached media (ERP attached media)
            if (load_attached == 1 && message.media.length > 0) {
                for (i = 0; i < message.media.length; i++) {
                    // Set image type
                    var imageType = message.media[i].substr(-4).toLowerCase();

                    // Set correct icon/image
                    if (imageType == '.jpg' || imageType == 'jpeg') {
                        imgSrc = message.media[i];
                    } else if (imageType == '.png') {
                        imgSrc = message.media[i];
                    } else if (imageType == '.gif') {
                        imgSrc = message.media[i];
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
                media = '<a href="' + message.media_url + '" target="_blank"><img src="' + message.media_url + '" style="max-width: 100%;"></a>'; // + media;
            }


            if (message.inout == 'in') {
                li += '<div class="bubble"><div class="txt"><p class="name"></p><p class="message">' + media + message.message + '</p><br/><span class="timestamp">' + message.datetime.date.substr(0, 19) + '</span></div><div class="bubble-arrow"></div></div>';
            } else if (message.inout == 'out') {
                li += '<div class="bubble alt"><div class="txt"><p class="name alt"></p><p class="message">' + media + message.message + '</p><br/><span class="timestamp">' + message.datetime.date.substr(0, 19) + '</span></div> <div class="bubble-arrow alt"></div></div>';
            } else {
                li += '<div>' + index + '</div>';
            }
        });

        li += '</div>';

        $("#chat-list-history").find(".modal-body").html(li);
        $(thiss).html("<img src='/images/chat.png' alt=''>");
        $("#chat-list-history").modal("show");

    }).fail(function (response) {
        $(thiss).text('Load More');

        alert('Could not load more messages');

        console.log(response);
    });
});