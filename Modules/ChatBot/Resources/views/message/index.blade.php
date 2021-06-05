@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Message List | Chatbot')

@section('content')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/dialog-node-editor.css">
    <style type="text/css">
        .panel-img-shorts {
            width: 80px;
            height: 80px;
            display: inline-block;
        }

        .panel-img-shorts .remove-img {
            display: block;
            float: right;
            width: 15px;
            height: 15px;
        }
    </style>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Message List | Chatbot</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 margin-tb" style="margin-bottom: 10px;">
            <div class="pull-left">
                <div class="form-inline">
                    <form method="get">
                        <div class="row">


                            <div class="col">
                                <?php echo Form::text("search", request("search", null), ["class" => "form-control", "placeholder" => "Enter input here.."]); ?>
                            </div>
                            <div class="col">
                                <select name="status" class="chatboat-message-status form-control">
                                    <option value="">Select Status</option>
                                    <option value="1" {{request()->get('status') == '1' ? 'selected' : ''}}>
                                        Approved
                                    </option>
                                    <option value="0" {{request()->get('status') == '0' ? 'selected' : ''}}>
                                        Unapproved
                                    </option>
                                </select>
                            </div>
                            <button type="submit" style="display: inline-block;width: 10%" class="btn btn-sm btn-image">
                                <img src="/images/search.png" style="cursor: default;">
                            </button>
                        </div>
                    </form>

                </div>
            </div>
            <div class="pull-right">
                <div class="form-inline">
                    <form method="post">
                        <?php echo csrf_field(); ?>
                        <?php echo Form::select("customer_id[]", [], null, ["class" => "form-control customer-search-select-box", "multiple" => true, "style" => "width:250px;"]); ?>
                        <button type="submit" style="display: inline-block;width: 10%"
                                class="btn btn-sm btn-image btn-forward-images">
                            <i class="glyphicon glyphicon-send"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive-lg" id="page-view-result">
                @include("chatbot::message.partial.list")
            </div>
        </div>
    </div>
    <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                    <input type="text" name="search_chat_pop"  class="form-control search_chat_pop" placeholder="Search Message" style="width: 200px;">
                    <input type="hidden" id="chat_obj_type" name="chat_obj_type">
                    <input type="hidden" id="chat_obj_id" name="chat_obj_id">
                    <button type="submit" class="btn btn-default downloadChatMessages">Download</button>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
  50% 50% no-repeat;display:none;">
    </div>
    <script src="/js/bootstrap-toggle.min.js"></script>
    <script type="text/javascript" src="/js/jsrender.min.js"></script>
    <script type="text/javascript">
        $(document).on("click", ".approve-message", function () {
            var $this = $(this);
            $.ajax({
                type: 'POST',
                url: "/chatbot/messages/approve",
                beforeSend: function () {
                    $("#loading-image").show();
                },
                data: {
                    _token: "{{ csrf_token() }}",
                    id: $this.data("id"),
                },
                dataType: "json"
            }).done(function (response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    $this.remove();
                    toastr['success'](response.message, 'success');
                }
            }).fail(function (response) {
                $("#loading-image").hide();
                console.log("Sorry, something went wrong");
            });
        });

        var getResults = function (href) {
            $.ajax({
                type: 'GET',
                url: href,
                beforeSend: function () {
                    $("#loading-image").show();
                },
                dataType: "json"
            }).done(function (response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    var removePage = response.page;
                    if (removePage > 0) {
                        var pageList = $("#page-view-result").find(".page-template-" + removePage);
                        pageList.nextAll().remove();
                        pageList.remove();
                    }
                    if (removePage > 1) {
                        $("#page-view-result").find(".pagination").first().remove();
                    }
                    $("#page-view-result").append(response.tpl);
                }
            }).fail(function (response) {
                $("#loading-image").hide();
                console.log("Sorry, something went wrong");
            });
        };

        $("#page-view-result").on("click", ".page-link", function (e) {
            e.preventDefault();

            var activePage = $(this).closest(".pagination").find(".active").text();
            var clickedPage = $(this).text();
            if (clickedPage == "‹" || clickedPage < activePage) {
                $('html, body').animate({scrollTop: ($(window).scrollTop() - 50) + "px"}, 200);
                getResults($(this).attr("href"));
            } else {
                getResults($(this).attr("href"));
            }

        });

        $(window).scroll(function () {
            if ($(window).scrollTop() > ($(document).height() - $(window).height() - 10)) {
                $("#page-view-result").find(".pagination").find(".active").next().find("a").click();
            }
        });

        $(document).on("click", ".delete-images", function () {

            var tr = $(this).closest("tr");
            var checkedImages = tr.find(".remove-img:checkbox:checked").closest(".panel-img-shorts");
            var form = tr.find('.remove-images-form');
            $.ajax({
                type: 'POST',
                url: form.attr("action"),
                data: form.serialize(),
                beforeSend: function () {
                    $("#loading-image").show();
                },
                dataType: "json"
            }).done(function (response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    $.each(checkedImages, function (k, e) {
                        $(e).remove();
                    });
                    toastr['success'](response.message, 'success');
                }
            }).fail(function (response) {
                $("#loading-image").hide();
                console.log("Sorry, something went wrong");
            });
        });

        $(document).on("click", ".add-more-images", function () {
            var $this = $(this);
            var id = $this.data("id");

            $.ajax({
                type: 'GET',
                url: "{{ route('chatbot.messages.attach-images') }}",
                data: {chat_id: id},
                beforeSend: function () {
                    $("#loading-image").show();
                },
                dataType: "json"
            }).done(function (response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    if (response.data.length > 0) {
                        var html = "";
                        $.each(response.data, function (k, img) {
                            html += '<div class="panel-img-shorts">';
                            html += '<input type="checkbox" name="delete_images[]" value="' + img.mediable_id + '_' + img.id + '" class="remove-img" data-media-id="' + img.id + '" data-mediable-id="' + img.mediable_id + '">';
                            html += '<img width="50px" heigh="50px" src="' + img.url + '">';
                            html += '</div>';
                        });
                        $this.closest("tr").find(".images-layout").find("form").append(html);
                    }
                    toastr['success'](response.message, 'success');
                } else {
                    toastr['error'](response.message, 'error');
                }
            }).fail(function (response) {
                $("#loading-image").hide();
                console.log("Sorry, something went wrong");
            });
        });

        $(document).on("click", ".check-all", function () {
            var tr = $(this).closest("tr");
            tr.find(".remove-img").trigger("click");
        });

        $(document).on("click", ".btn-forward-images", function (e) {
            e.preventDefault();
            var selectedImages = $("#page-view-result").find(".remove-img:checkbox:checked");
            var imagesArr = [];
            $.each(selectedImages, function (k, v) {
                imagesArr.push($(v).data("media-id"));
            });
            $.ajax({
                type: "POST",
                url: "/chatbot/messages/forward-images",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'images': imagesArr,
                    'customer': $(".customer-search-select-box").val()
                }
            }).done(function (response) {
                if (response.code == 200) {
                    toastr['success'](response.message, 'success');
                } else {
                    toastr['error'](response.message, 'error');
                }
            });

        });

        $(document).on('click', '.send-message1', function () {
            console.log('*****************************');
            var thiss = $(this);
            var data = new FormData();
            
            var field = "customer_id";
            var tr  = $(this).closest("tr").find("td").first();
            var typeId = tr.data('customer-id');
            var chatMessageReplyId = tr.data('chat-message-reply-id')
            var type = tr.data("context");

            if(parseInt(tr.data("vendor-id")) > 0) {
                type = "vendor";
                typeId = tr.data("vendor-id");
                field = "vendor_id";
            }
            
            var customer_id = typeId;
            var message = thiss.closest(".cls_textarea_subbox").find("textarea").val();

            if(type === 'customer'){

                data.append("customer_id", typeId);
                data.append("message", message);
                data.append("status", 1);

            }else if(type === 'issue'){

                data.append('issue_id', typeId);
                data.append("message", message);
                data.append("sendTo", 'to_developer');
                data.append("status", 2)
                data.append("chat_reply_message_id", chatMessageReplyId)

            }else if(type === 'issue'){
                data.append('issue_id', typeId);
                data.append("message", message);
                data.append("status", 1)
                data.append("chat_reply_message_id", chatMessageReplyId)
            }

            var add_autocomplete  = thiss.closest(".cls_textarea_subbox").find("[name=add_to_autocomplete]").is(':checked') ;
            data.append("add_autocomplete", add_autocomplete);

            if (message.length > 0) {
                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        url: BASE_URL+'/whatsapp/sendMessage/'+type,
                        type: 'POST',
                        "dataType": 'json',           // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function () {
                            $(thiss).attr('disabled', true);

                        }
                    }).done(function (response) {
                        $(thiss).attr('disabled', false);
                        thiss.closest(".cls_textarea_subbox").find("textarea").val("");
                        toastr['success']("Message sent successfully", 'success');

                    }).fail(function (errObj) {
                       
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });

    </script>
@endsection
