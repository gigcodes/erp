@extends('layouts.app')

@section('content')
    <div class="col-md-12">
        <h2 class="page-heading">Bulk Customer Replies</h2>
    </div>
    <div class="col-md-12">
        @if(Session::has('message'))
            <div class="alert alert-info">
                {{ Session::get('message') }}
            </div>
        @endif
    </div>
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>Keywords, Phrases & Sentences</strong>
            </div>
            <div class="panel-body">
                <form method="post" action="{{ action('BulkCustomerRepliesController@storeKeyword') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                                <input type="text" name="keyword" id="keyword" placeholder="Keyword, phrase or sentence..." class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <button class="btn btn-secondary btn-block">Add New</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="alert alert-warning">
                    <strong>Note: Click any tag below, and it will show the customer with the keywords used.</strong>
                </div>
                <div>
                    <strong>Manually Added</strong><br>
                    @foreach($keywords as $keyword)
                        <a href="{{ action('BulkCustomerRepliesController@index', ['keyword_filter' => $keyword->value]) }}" style="font-size: 14px;" class="label label-default">{{$keyword->value}}</a>
                    @endforeach
                </div>
                <div class="mt-2">
                    <strong>Auto Generated</strong><br>
                    @foreach($autoKeywords as $keyword)
                        <a href="{{ action('BulkCustomerRepliesController@index', ['keyword_filter' => $keyword->value]) }}" style="font-size: 14px; margin-bottom: 2px; display:inline-block;" class="label label-default">{{$keyword->value}}({{$keyword->count}})</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <a class="btn btn-secondary change-whatsapp" href="javascript:;">Change Whatsapp</a>
        @if($searchedKeyword)
            @if($searchedKeyword->customers)
                @php
                    $customers = $searchedKeyword->customers()->leftJoin(\DB::raw('(SELECT MAX(chat_messages.id) as  max_id, customer_id ,message as matched_message  FROM `chat_messages` join customers as c on c.id = chat_messages.customer_id  GROUP BY customer_id ) m_max'), 'm_max.customer_id', '=', 'customers.id')->groupBy('customers.id')->orderBy('max_id','desc')->get()
                @endphp
                <form action="{{ action('BulkCustomerRepliesController@sendMessagesByKeyword') }}" method="post">
                    @csrf
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th>Pick?</th>
                            <th>S.N</th>
                            <th>Customer ({{count($customers)}})</th>
                            <th>Recent Messages</th>
                            <th>Shortcuts</th>
                            <th>Next Action</th>
                            <th>Communication</th>
                        </tr>
                        <tr>
                            <td colspan="7">
                                <div class="row">
                                    <div class="col-md-11">
                                        <textarea name="message" id="message" rows="1" class="form-control" placeholder="Common message.."></textarea>
                                    </div>
                                    <div class="col-md-1">
                                        <button class="btn btn-secondary btn-block">Send</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @foreach($customers as $key=>$customer)
                            <tr>
                                <td><input type="checkbox" name="customers[]" value="{{ $customer->id }}" class="customer_message"></td>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $customer->name }}</td>
                                <td>
                                    <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object="customer" data-limit="10" data-id="{{$customer->id}}" data-is_admin="1" data-is_hod_crm="" data-all="1" title="Load messages"><img src="/images/chat.png" alt=""></button>
                                </td>
                                <td>@include('bulk-customer-replies.partials.shortcuts')</td>
                                <td>@include('bulk-customer-replies.partials.next_actions')</td>
                                <td class="communication-td">@include('bulk-customer-replies.partials.communication')</td>
                            </tr>
                        @endforeach
                    </table>
                </form>
            @else
            @endif
        @endif
    </div>
    <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                    <input type="text" name="search_chat_pop"  class="form-control search_chat_pop" placeholder="Search Message" style="width: 200px;">
                    <input type="text" name="search_chat_pop_time"  class="form-control search_chat_pop_time" placeholder="Search Time" style="width: 200px;">
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div id="modal-change-whatsapp" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <form action="{{ route('bulk-messages.whatsapp-no') }}" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Change Whatsapp no?</h4>
                    </div>
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <?php echo Form::select("whatsapp_no",$whatsappNos,null,["class" => "form-control select2 whatsapp_no" , "style" => "width:100%"]); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default modal-change-whatsapp-btn">Change ?</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- <script type="text/javascript" src="/js/site-helper.js"></script> -->
    <script src="https://rawgit.com/jackmoore/autosize/master/dist/autosize.min.js"></script>
    <script>
        autosize(document.getElementById("message"));
    </script>
    <script type="text/javascript">
        $(document).on('click', '.add_next_action', function (event) {
            event.preventDefault();
            $.ajax({
                url: "/erp-customer/add-next-actions",
                type: 'POST',
                data: {
                    "name": $('input[name="add_next_action"]').val(),
                    "_token": "{{csrf_token()}}",
                },
                dataType: "json",
                success: function (response) {
                    toastr["success"]("Action added successfully!", "Message");
                    //$('#message_list_' + issueId).append('<li>' + response.message.created_at + " : " + response.message.message + '</li>');
                    $(self).removeAttr('disabled');
                    $(self).val('');
                    location.reload();
                },
                beforeSend: function () {
                    $(self).attr('disabled', true);
                },
                error: function () {
                    alert('There was an error sending the message...');
                    $(self).removeAttr('disabled', true);
                }
            });
        });
        $(document).on("click",".send-with-audio-message",function(event) {
            event.preventDefault();
            if($(this).hasClass("mic-active") == false) {
                if($(".mic-button-input").hasClass("mic-active") == false) {
                    $(".mic-button-input").trigger("click");
                }else{
                    $(".mic-button-input").trigger("click");
                    $(".mic-button-input").trigger("click");
                }
                $(".message-strong").removeClass("message-strong");
                $(this).closest(".infinite-scroll").find(".mic-active").removeClass("mic-active");
                $(this).closest(".communication").find(".quick-message-field").addClass("message-strong");
                $(this).addClass("mic-active");
            }else{
                if($(".mic-button-input").hasClass("mic-active") == false) {
                }else{
                    $(".mic-button-input").trigger("click");
                }
                $(".message-strong").removeClass("message-strong");
                $(this).removeClass("mic-active");
            }
        });
        $(document).on('click', '.delete_category', function (event) {
            event.preventDefault();
            var quickCategory  = $(this).parents("div").siblings().children(".quickCategory");
            console.log(quickCategory.val());
            let quickCategoryId = quickCategory.children("option:selected").data('id');
            if (quickCategory.val() == '') {
                alert('Please select category to delete!')
                return false;
            }
            $.ajax({
                url: "/destroy-reply-category",
                type: 'POST',
                data: {
                    "_token": "{{csrf_token()}}",
                    id : quickCategoryId
                },
                dataType: "json",
                success: function (response) {
                    toastr["success"]("Category deleted successfully!", "Message");
                    //$('#message_list_' + issueId).append('<li>' + response.message.created_at + " : " + response.message.message + '</li>');
                    $(self).removeAttr('disabled');
                    $(self).val('');
                    location.reload();
                },
                beforeSend: function () {
                    $(self).attr('disabled', true);
                },
                error: function () {
                    alert('There was an error sending the message...');
                    $(self).removeAttr('disabled', true);
                }
            });
        });
        $(document).on('click', '.delete_quick_comment', function (event) {
            event.preventDefault();
            var quickComment  = $(this).parents("div").siblings().children(".quickCategory");
            let quickCommentId = quickComment.children("option:selected").data('id');
            if (quickComment.val() == '') {
                alert('Please select comment to delete!')
                return false;
            }
            $.ajax({
                url: "/reply/" + quickCommentId,
                type: 'POST',
                data: {
                    "_token": "{{csrf_token()}}",
                },
                dataType: "json",
                success: function (response) {
                    toastr["success"]("Category deleted successfully!", "Message");
                    //$('#message_list_' + issueId).append('<li>' + response.message.created_at + " : " + response.message.message + '</li>');
                    $(self).removeAttr('disabled');
                    $(self).val('');
                    location.reload();
                },
                beforeSend: function () {
                    $(self).attr('disabled', true);
                },
                error: function () {
                    alert('There was an error sending the message...');
                    $(self).removeAttr('disabled', true);
                }
            });
        });
        $(document).on('click', '.send-message-open', function (event) {
            event.preventDefault();
            var textBox = $(this).closest(".communication-td").find(".send-message-textbox");
            var sendToStr  = $(this).closest(".communication-td").next().find(".send-message-number").val();
            let issueId = textBox.attr('data-customerid');
            let message = textBox.val();
            if (message == '') {
                alert('Please enter message!')
                return false;
            }
            let self = textBox;
            $.ajax({
                url: "{{action('WhatsAppController@sendMessage', 'customer')}}",
                type: 'POST',
                data: {
                    "customer_id": issueId,
                    "message": message,
                    //"sendTo" : sendToStr,
                    "_token": "{{csrf_token()}}",
                   "status": 2
                },
                dataType: "json",
                success: function (response) {
                    toastr["success"]("Message sent successfully!", "Message");
                    //$('#message_list_' + issueId).append('<li>' + response.message.created_at + " : " + response.message.message + '</li>');
                    $(self).removeAttr('disabled');
                    $(self).val('');
                    location.reload();
                },
                beforeSend: function () {
                    $(self).attr('disabled', true);
                },
                error: function () {
                    alert('There was an error sending the message...');
                    $(self).removeAttr('disabled', true);
                }
            });
        });
        $(document).on('click', '.quick_category_add', function () {
            event.preventDefault();
            if($('input[name="category_name"]').val() == ''){
                alert("Please Enter category name!");
                return false;
            }
            $.ajax({
                url: "/add-reply-category",
                type: 'POST',
                data: {
                    name : $('input[name="category_name"]').val(),
                    "_token": "{{csrf_token()}}",
                },
                dataType: "json",
                success: function (response) {
                    toastr["success"]("Category added successfully!", "Message");
                    //$('#message_list_' + issueId).append('<li>' + response.message.created_at + " : " + response.message.message + '</li>');
                    $(this).removeAttr('disabled');
                    $('input[name="category_name"]').removeAttr('disabled');
                    $('input[name="category_name"]').val('');
                    location.reload();
                },
                beforeSend: function () {
                    $('input[name="category_name"]').attr('disabled', true);
                    $('input[name="category_name"]').attr('disabled',true);
                },
                error: function () {
                    alert('There was an error sending the message...');
                    $(this).removeAttr('disabled');
                    $('input[name="category_name"]').removeAttr('disabled');
                    $('input[name="category_name"]').val('');
                }
            });
            //siteHelpers.quickCategoryAdd($(this));
        });
        $(document).on('click', '.quick_comment_add', function () {
            event.preventDefault();
            if ($('input[name="quick_comment"]').val() == "") {
                alert("Please Enter New Quick Comment!!");
                return false;
            }
            if ($('select[name="quickCategory"]').val() == "") {
                alert("Please Select Category!!");
                return false;
            }
            var quickCategoryId = $('select[name="quickCategory"]').children("option:selected").data('id');
            var formData = new FormData();
            formData.append("_token", "{{csrf_token()}}");
            formData.append("reply", $('input[name="quick_comment"]').val());
            formData.append("category_id", quickCategoryId);
            formData.append("model", 'Approval Lead');
            $.ajax({
                url: "/reply",
                type: 'POST',
                //data : formData,
                data: {
                    "reply":  $('input[name="quick_comment"]').val(),
                    "category_id": quickCategoryId,
                    "model": 'Approval Lead',
                    "_token": "{{csrf_token()}}",
                },
                dataType: "json",
                success: function (response) {
                    toastr["success"]("Category added successfully!", "Message");
                    //$('#message_list_' + issueId).append('<li>' + response.message.created_at + " : " + response.message.message + '</li>');
                    $(self).removeAttr('disabled');
                    $(self).val('');
                    location.reload();
                },
                beforeSend: function () {
                    $(self).attr('disabled', true);
                },
                error: function () {
                    alert('There was an error sending the message...');
                    $(self).removeAttr('disabled', true);
                }
            });
        });
        $(document).on("click",".change-whatsapp",function(){
            $("#modal-change-whatsapp").modal("show");
        });
        $(document).on("click",".modal-change-whatsapp-btn",function(){
            var customers = [];
            var all_customers = [];
            $(".customer_message").each(function () {
                if ($(this).prop("checked") == true) {
                    customers.push($(this).val());
                }
            });
            if (all_customers.length != 0) {
                customers = all_customers;
            }
            if (customers.length == 0) {
                alert('Please select Customer');
                return false;
            }
            var form = $(this).closest("form");
            $.ajax({
                type: form.attr("method"),
                url: form.attr("action"),
                dataType : "json",
                data : {
                    _token : $('meta[name="csrf-token"]').attr('content'),
                    customers: customers.join(),
                    whatsapp_no: form.find(".whatsapp_no").val()
                },
                success: function(data) {
                    toastr['success'](data.total + ' record has been update successfully', 'success');
                    //location.reload();
                }
            });
        });
    $.extend($.expr[':'], {
      'containsi': function(elem, i, match, array) {
        return (elem.textContent || elem.innerText || '').toLowerCase()
            .indexOf((match[3] || "").toLowerCase()) >= 0;
      }
    });
     $(document).on('keyup','.search_chat_pop',function(event){
        event.preventDefault();
        if($('.search_chat_pop').val().toLowerCase() != ''){
            $(".message").css("background-color", "#999999");
            page = $('.message').text().toLowerCase();
            searchedText = $('.search_chat_pop').val().toLowerCase();
            console.log(searchedText);
            $("p.message:containsi('"+searchedText+"')").css("background-color", "yellow");
        }
    });
    </script>
@endsection