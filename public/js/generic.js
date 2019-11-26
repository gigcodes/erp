$(document).on('click', '.load-communication-modal', function () {
    var thiss = $(this);
    var object_type = $(this).data('object');
    var object_id = $(this).data('id');
    var load_attached = $(this).data('attached');
    var load_all = $(this).data('all');
    var load_type = $(this).data('load-type');
    var is_admin = $(this).data('is_admin');
    var is_hod_crm = $(this).data('is_hod_crm');


    $.ajax({
        type: "GET",
        url: "/chat-messages/" + object_type + "/" + object_id + "/loadMoreMessages",
        data: {
            limit: 1000,
            load_all: load_all,
            load_attached: load_attached,
            load_type: load_type,
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
                    imgSrc = getImageToDisplay(message.media[i].image);

                    //console.log(message.media[i], message.product_id);

                    // Set media
                    if (imgSrc != '') {
                        media = media + '<div class="col-12">';
                        media = media + '<a href="' + message.media[i].image + '" target="_blank"><img src="' + imgSrc + '" style="max-width: 100%;"></a>';
                        if (message.media[i].product_id > 0 && message.customer_id > 0) {
                            media = media + '<br />';
                            media = media + '<a href="#" class="btn btn-xs btn-default ml-1 create-product-lead-dimension" data-id="' + message.media[i].product_id + '" data-customer-id="'+message.customer_id+'">+ Dimensions</a>';
                            media = media + '<a href="#" class="btn btn-xs btn-default ml-1 create-product-lead" data-id="' + message.media[i].product_id + '" data-customer-id="'+message.customer_id+'">+ Lead</a>';
                            media = media + '<a href="#" class="btn btn-xs btn-default ml-1 create-detail_image" data-id="' + message.media[i].product_id + '" data-customer-id="'+message.customer_id+'">Detailed Images</a>';
                            media = media + '<a href="#" class="btn btn-xs btn-default ml-1 create-product-order" data-id="' + message.media[i].product_id + '" data-customer-id="'+message.customer_id+'">+ Order</a>';
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

            if (message.type == "task" || message.type == "customer") {
                
                if (message.status == 0 || message.status == 5 || message.status == 6) {
                    if (message.status == 0) {
                        button += "<a href='javascript:;' data-url='/whatsapp/updatestatus?status=5&id=" + message.id + "' class='btn btn-xs btn-secondary ml-1 change_message_status'>Mark as Read </a>";
                    }

                    if (message.status == 0 || message.status == 5) {                        
                      button += '<a href="javascript:;" data-url="/whatsapp/updatestatus?status=6&id=' + message.id + '" class="btn btn-xs btn-secondary ml-1 change_message_status">Mark as Replied </a>';
                    }
                    button += '<button class="btn btn-image forward-btn" data-toggle="modal" data-target="#forwardModal" data-id="' + message.id + '"><img src="/images/forward.png" /></button><button data-id="'+message.id+'" class="btn btn-xs btn-secondary resend-message-js">Resend</button>';
                } else if (message.status == 4) {
                } else {
                    if ( message.type == "customer") {
                        if (message.error_status == 1) {
                            button +="<a href='javascript:;' class='btn btn-image fix-message-error' data-id='" + message.id + "'><img src='/images/flagged.png' /></a><a href='#' class='btn btn-xs btn-secondary ml-1 resend-message' data-id='" + message.id + "'>Resend</a>";
                         } else if (message.error_status == 2) {
                           button += "<a href='javascript:;' class='btn btn-image fix-message-error' data-id='" + message.id + "'><img src='/images/flagged.png' /><img src='/images/flagged.png' /></a><a href='#' class='btn btn-xs btn-secondary ml-1 resend-message' data-id='" + message.id + "'>Resend</a>";
                         }
                         
                         if (!message.approved) {
                             if (is_admin || is_hod_crm) {
                                approveBtn = "<button class='btn btn-xs btn-secondary btn-approve ml-3' data-messageid='" + message.id + "'>Approve</button>";
                                button += approveBtn;
                                button += '<textarea name="message_body" rows="8" class="form-control" id="edit-message-textarea' + message.id + '" style="display: none;">' + message.message + '</textarea>';
                                button += ' <a href="#" style="font-size: 9px" class="edit-message whatsapp-message ml-2" data-messageid="' + message.id + '">Edit</a>';
                             }
                         }
                         button += '<button class="btn btn-image forward-btn" data-toggle="modal" data-target="#forwardModal" data-id="' + message.id + '"><img src="/images/forward.png" /></button><button data-id="'+message.id+'" class="btn btn-xs btn-secondary resend-message-js">Resend</button>';
                    }

                    if (message.type == "task") {
                        button += "<a href='#' class='btn btn-xs btn-secondary ml-1 resend-message' data-id='" + message.id + "'>Resend (" + message.resent + ")</a>";
                        button += "<a href='#' class='btn btn-image ml-1 reminder-message' data-id='" + message.id + "' data-toggle='modal' data-target='#reminderMessageModal'><img src='/images/reminder.png' /></a>";
                     }
                }
            }
            if (message.inout == 'in') {
                li += '<div class="bubble"><div class="txt"><p class="name"></p><p class="message" data-message="'+message.message+'">' + media + message.message + button + '</p><br/><span class="timestamp">' + message.datetime.date.substr(0, 19) + '</span></div><div class="bubble-arrow"></div></div>';
            } else if (message.inout == 'out') {
                li += '<div class="bubble alt"><div class="txt"><p class="name alt"></p><p class="message"  data-message="'+message.message+'">' + media + message.message + button + '</p><br/><span class="timestamp">' + message.datetime.date.substr(0, 19) + '</span></div> <div class="bubble-arrow alt"></div></div>';
            } else {
                li += '<div>' + index + '</div>';
            }
        });

        li += '</div>';

        if ($('#chat-list-history').length > 0) {
            $("#chat-list-history").find(".modal-body").html(li);
            //$(thiss).html("<img src='/images/chat.png' alt=''>");
            $("#chat-list-history").modal("show");
        } else {
            $("#chat-history").html(li);
        }

    }).fail(function (response) {
        //$(thiss).text('Load More');

        alert('Could not load messages');

        console.log(response);
    });
});
$(document).on('click', '.btn-approve', function (e) {
   var element = this;
   if (!$(element).attr('disabled')) {
     $.ajax({
       type: "POST",
       url: "/whatsapp/approve/customer",
       data: {
         _token: $('meta[name="csrf-token"]').attr('content'),
         messageId: $(this).data('messageid')
       },
       beforeSend: function() {
         $(element).attr('disabled', true);
         $(element).text('Approving...');
       }
     }).done(function( data ) {
       element.remove();
       console.log(data);
     }).fail(function(response) {
       $(element).attr('disabled', false);
       $(element).text('Approve');

       console.log(response);
       alert(response.responseJSON.message);
     });
   }
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

    createLead (this,{auto_approve: 1}); 
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

function createLead (thiss,dataSending) {
    var selected_product_images = [];
    var product_id = $(thiss).data('id');

    if (product_id > 0) {
        selected_product_images.push(product_id);
    }

    var customer_id = $(thiss).data('customer-id');

    if (!customer_id) {
        alert('customer not found');
        return false;
    }

    var text = $(thiss).text();
    
    if (selected_product_images.length > 0) {
        if ($('#add_lead').length > 0) {
            $('#add_lead').modal('show');
            $('#add_lead').find('input[name="customer_id"]').val(customer_id);
            $('#add_lead').find('input[name="rating"]').val(1);
            $('#add_lead').find('select[name="brand_id"]').val('');
            $('#add_lead').find('select[name="category_id"]').val('1').trigger('change');
            $('#add_lead').find('select[name="brand_segment[]"]').val('').trigger('change');
            $('#add_lead').find('select[name="lead_status_id"]').val('3').trigger('change');
            $('#add_lead').find('input[name="size"]').val('');
            
            if (!dataSending) {
                dataSending = {};
            }

            if ($('#add_lead').find('input[name="product_id"]').length > 0) {
                $('#add_lead').find('input[name="product_id"]').val(product_id);
                $('#add_lead').find('input[name="product_id"]').data('object', JSON.stringify(dataSending));
            } else {
                $('#add_lead').find('form').append($('<input>', {
                    value: product_id,
                    name: 'product_id',
                    type: 'hidden',
                    'data-object' : JSON.stringify(dataSending),
                }));    
            }
            
            return false;
        }

        var created_at = moment().format('YYYY-MM-DD HH:mm');
        $.ajax({
            type: 'POST',
            url: "/leads",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                customer_id: customer_id,
                rating: 1,
                status: 3,
                assigned_user: 6,
                selected_product: selected_product_images,
                type: "product-lead",
                created_at: created_at
            },
            beforeSend: function() {
                $(thiss).text('Creating...');
            },
            success: function(response) {
                $.ajax({
                    type: "POST",
                    url: "/leads/sendPrices",
                    data: $.extend({
                        _token:  $('meta[name="csrf-token"]').attr('content'),
                        customer_id: customer_id,
                        lead_id: response.lead.id,
                        selected_product: selected_product_images
                    },dataSending)
                }).done(function() {
                    $(thiss).text(text);
                }).fail(function(response) {
                    $(thiss).text(text);
                    alert('Could not send product dimension to customer!');
                });
            }
        }).fail(function(error) {
            $(thiss).text(text);
            alert('There was an error creating a lead');
        });
    } else {
        $(thiss).text(text);
        alert('Please select at least 1 product first');
    }
}

$(document).on('click', '.create-product-lead-dimension', function(e) {
        e.preventDefault();
        createLead (this,{dimension: true , auto_approve: 1});         
});

$(document).on('click', '.create-detail_image', function(e) {
    e.preventDefault();
    createLead (this,{detailed: true , auto_approve: 1}); 
});

$(document).on('click', '.resend-message-js', function(e) {
    e.preventDefault();
     let messageId = $(this).attr('data-id');
      $.ajax({
          url: "/message/resend",
          data: {
                message_id: messageId
          },
          success: function() {
                toastr['success']('Message resent successfully!')
          }
      });
});

$(document).on('click', '.create-product-order', function(e) {
    e.preventDefault();

    var selected_product_images = [];
    var product_id = $(this).data('id');

    if (product_id > 0) {
        selected_product_images.push(product_id);
    }

    var customer_id = $(this).data('customer-id');

    if (!customer_id) {
        alert('customer not found');
        return false;
    }

    var text = $(this).text();
    var thiss = this;

    if (selected_product_images.length > 0) {

        if ($('#add_order').length > 0) {
            $('#add_order').find('input[name="customer_id"]').val(customer_id);
            $('#add_order').find('input[name="order_date"]').val('');
            $('#add_order').find('input[name="date_of_delivery"]').val('');
            $('#add_order').find('input[name="advance_detail"]').val('');
            $('#add_order').find('input[name="advance_date"]').val('');
            $('#add_order').find('input[name="balance_amount"]').val('');
            $('#add_order').find('input[name="estimated_delivery_date"]').val('');
            $('#add_order').find('input[name="received_by"]').val('');
            $('#add_order').find('input[name="note_if_any"]').val('');
            $('#add_order').find('select[name="payment_mode"]').val('');
            $('#add_order').find('select[name="sales_person"]').val('');
            $('#add_order').find('select[name="whatsapp_number"]').val('');
            $('#add_order').find('select[name="order_type"]').val('offline');

            $('#add_order').find('select[name="order_status"]').val('Follow up for advance');

            if ($('#add_order').find('input[name="selected_product[]"]').length > 0) {
                $('#add_order').find('input[name="selected_product[]"]').val(product_id);
            } else {
                $('#add_order').find('.add_order_frm').append($('<input>', {
                    value: product_id,
                    name: 'selected_product[]',
                    type: 'hidden'
                }));    
            }

            if ($('#add_order').find('input[name="convert_order"]').length > 0) {
                $('#add_order').find('input[name="convert_order"]').val('convert_order');
            } else {
                $('#add_order').find('.add_order_frm').append($('<input>', {
                    value: 'convert_order',
                    name: 'convert_order',
                    type: 'hidden'
                }));    
            }
            
            $('#add_order').modal('show');
            return false;
        }

      $.ajax({
        type: 'POST',
        url: "/order",
        data: {
          _token: $('meta[name="csrf-token"]').attr('content'),
          customer_id: customer_id,
          order_type: "offline",
          convert_order: 'convert_order',
          selected_product: selected_product_images,
          order_status: "Follow up for advance"
        },
        beforeSend: function() {
          $(thiss).text('Creating...');
        },
        success: function(response) {
          $.ajax({
            type: "POST",
            url: "/order/send/Delivery",
            data: {
              _token: $('meta[name="csrf-token"]').attr('content'),
              customer_id: customer_id,
              order_id: response.order.id,
              selected_product: selected_product_images
            }
          }).done(function() {
            $(thiss).text(text);
          }).fail(function(response) {
            $(thiss).text(text);
            alert('Could not send delivery message to customer!');
          });
        }
      }).fail(function(error) {
        $(thiss).text(text);
        alert('There was an error creating a order');
      });
    } else {
        $(thiss).text(text);
         alert('Please select at least 1 product first');
    }
});

$(document).on('click', '.forward-btn', function() {
var id = $(this).data('id');
$('#forward_message_id').val(id);
});

$(document).on("keyup", '.search_chat_pop', function() {
    var value = $(this).val().toLowerCase();
    $(".speech-wrapper .bubble").filter(function() {
        $(this).toggle($(this).find('.message').data('message').toLowerCase().indexOf(value) > -1)
    });
});