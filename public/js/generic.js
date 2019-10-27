$(document).on('click', '.load-communication-modal', function () {
    var thiss = $(this);
    var object_type = $(this).data('object');
    var object_id = $(this).data('id');
    var load_attached = $(this).data('attached');
    var load_all = $(this).data('all');

    $.ajax({
        type: "GET",
        url: "/chat-messages/" + object_type + "/" + object_id + "/loadMoreMessages",
        data: {
            limit: 1000,
            load_all: load_all,
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

                    console.log(message.media[i], message.product_id);

                    // Set media
                    if (imgSrc != '') {
                        media = media + '<div class="col-12">';
                        media = media + '<a href="' + message.media[i] + '" target="_blank"><img src="' + imgSrc + '" style="max-width: 100%;"></a>';
                        if (message.product_id > 0) {
                            media = media + '<br />';
                            media = media + '<a href="#" class="btn btn-xs btn-secondary ml-1 create-product-lead-dimension" data-id="' + message.product_id + '">+ Dimensions</a>';
                            media = media + '<a href="#" class="btn btn-xs btn-secondary ml-1 create-product-lead" data-id="' + message.product_id + '">+ Lead</a>';
                            media = media + '<a href="#" class="btn btn-xs btn-secondary ml-1 create-detail_image" data-id="' + message.product_id + '">Detailed Images</a>';
                            media = media + '<a href="#" class="btn btn-xs btn-secondary ml-1 create-product-order" data-id="' + message.product_id + '">+ Order</a>';
                        }
                        media = media + '</div>';
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
                    media = '<input type="checkbox" class="form-control" name="checkbox[]" value="' + imgSrc + '" id="cb1_m_' + j + '" style="border: 3px solid black;"/><a href="' + message.media_url + '" target="_blank"><img src="' + imgSrc + '" style="max-width: 100%;"></a>';
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

        alert('Could not load messages');

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

$(document).on('click', '.complete-call', function (e) {
    e.preventDefault();

    var thiss = $(this);
    var token = $('meta[name="csrf-token"]').attr('content');
    var url = route.instruction_complete;
    var id = $(this).data('id');
    var assigned_from = $(this).data('assignedfrom');
    var current_user = current_user;

    $.ajax({
        type: 'POST',
        url: url,
        data: {
            _token: token,
            id: id
        },
        beforeSend: function () {
            $(thiss).text('Loading');
        }
    }).done(function (response) {
        // $(thiss).parent().html(moment(response.time).format('DD-MM HH:mm'));
        $(thiss).parent().html('Completed');
        location.reload();
    }).fail(function (errObj) {
        console.log(errObj);
        alert("Could not mark as completed");
    });
});

$(document).on('click', '.pending-call', function (e) {
    e.preventDefault();

    var thiss = $(this);
    var url = route.instruction_pending;
    var id = $(this).data('id');

    $.ajax({
        type: 'POST',
        url: url,
        data: {
            _token: token,
            id: id
        },
        beforeSend: function () {
            $(thiss).text('Loading');
        }
    }).done(function (response) {
        $(thiss).parent().html('Pending');
        $(thiss).remove();
        location.reload();
    }).fail(function (errObj) {
        console.log(errObj);
        alert("Could not mark as completed");
    });
});

$(document).on('click', '.create-product-lead', function (e) {
    e.preventDefault();

    var thiss = $(this);
    var selected_products = [];
    var product_id = $(this).data('id');

    if (product_id > 0) {
        selected_products.push(product_id);
    }

    console.log(selected_products);

    if ( selected_products.length > 0 ) {
        var created_at = moment().format('YYYY-MM-DD HH:mm');

        $.ajax({
            type: 'POST',
            url: route.leads_store,
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                customer_id: customer_id,
                rating: 1,
                status: 3,
                assigned_user: 6,
                selected_product: selected_products,
                type: "product-lead",
                created_at: created_at
            },
            beforeSend: function () {
                $(thiss).text('Creating...');
            },
            success: function (response) {
                $.ajax({
                    type: "POST",
                    url: route.leads_send_prices,
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        customer_id: customer_id,
                        lead_id: response.lead.id,
                        selected_product: selected_products,
                        auto_approve: true
                    }
                }).done(function () {
                    location.reload();
                }).fail(function (response) {
                    console.log(response);
                    alert('Could not send product prices to customer!');
                });
            }
        }).fail(function (error) {
            console.log(error);
            alert('There was an error creating a lead');
        });
    } else {
        alert('Please select at least 1 product first');
    }
});

$('#addRemarkButton').on('click', function() {
    var id = $('#add-remark input[name="id"]').val();
    var remark = $('#add-remark textarea[name="remark"]').val();

    $.ajax({
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        },
        url: route.task_add_remark,
        data: {
        id:id,
            remark:remark,
            module_type: 'instruction'
    },
}).done(response => {
        alert('Remark Added Success!')
        window.location.reload();
    }).fail(function(response) {
        console.log(response);
    });
});

$(".view-remark").click(function () {
    var id = $(this).attr('data-id');

    $.ajax({
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        },
        url: route.task_get_remark,
        data: {
        id:id,
            module_type: "instruction"
    },
}).done(response => {
        var html='';

        $.each(response, function( index, value ) {
            html+=' <p> '+value.remark+' <br> <small>By ' + value.user_name + ' updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
            html+"<hr>";
        });
        $("#viewRemarkModal").find('#remark-list').html(html);
    });
});

var token = $('meta[name="csrf-token"]').attr('content');