@extends('layouts.app')
@section('content')

<?php 
$chatIds = \App\CustomerLiveChat::orderBy('seen','asc')->orderBy('status','desc')->get();
$newMessageCount = \App\CustomerLiveChat::where('seen',0)->count();
?>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Live Chat</h2>
            <div class="pull-right">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="table-responsive">
            <table class="table table-striped table-bordered" style="width: 99%" id="keywordassign_table">
                <thead>
                    <tr>
                        <th>Sr. No.</th>
                        <th>Site Name</th>
                        <th>Visitor Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Translation Language</th>
                        <th>Communication</th>
                        <th>Quick Replies</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $srno=1;
                    ?>
                     @if(isset($chatIds) && !empty($chatIds))
                        @foreach ($chatIds as $chatId)
                            @php
                            $customer = \App\Customer::where('id',$chatId->customer_id)->first();
                            $customerInital = substr($customer->name, 0, 1);
                            @endphp
                               <tr>
                                <td><?php echo $srno;?></td>
                                <td><?php echo $chatId->website;?></td>
                                <td><?php echo $customer->name;?></td>
                                <td><?php echo $customer->email;?></td>
                                <td><?php echo $customer->phone;?></td>
                                <td>
                                    @php
                                    $path = storage_path('/');
                                    $content = File::get($path."languages.json");
                                    $language = json_decode($content, true);
                                    @endphp
                                    <div class="selectedValue">
                                        <select id="autoTranslate" class="form-control auto-translate">
                                            <option value="">Translation Language</option>
                                            @foreach ($language as $key => $value)
                                                <option value="{{$value}}">{{$key}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div onclick="getLiveChats('{{ $customer->id }}')" class="card-body msg_card_body" id="live-message-recieve">
                                        @if(isset($message) && !empty($message))
                                            @foreach($message as $msg)
                                                {!! $msg !!}
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="typing-indicator" id="typing-indicator"></div>
                                    <div class="card-footer">
                                        <div class="input-group">
                                            <div class="card-footer">
                                                <div class="input-group">
                                                    <div class="input-group-append">
                                                        <a href="{{ route('attachImages', ['livechat', @$customer->id, 1]) .'?'.http_build_query(['return_url' => 'livechat/getLiveChats'])}}" class="btn btn-image px-1"><img src="{{asset('images/attach.png')}}"/></a>
                                                    </div>
                                                    <input type="hidden" id="message-id" name="message-id" />
                                                    <textarea name="" class="form-control type_msg message_textarea" placeholder="Type your message..." id="message"></textarea>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text send_btn" onclick="sendMessage()"><i class="fa fa-location-arrow"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </td>
                                <td>
                                    <div class="chat-rightbox">
                                        @php
                                            $all_categories = \App\ReplyCategory::all();
                                        @endphp
                                        <select class="form-control auto-translate" id="categories">
                                            <option value="">Select Category</option>
                                            @if(isset($all_categories))
                                                @foreach ($all_categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="chat-rightbox mt-4">
                                        <input type="text" name="quick_comment" placeholder="New Quick Comment" class="form-control quick_comment">
                                        <button class="btn btn-secondary quick_comment_add">+</button>
                                    </div>
                                    <div class="chat-rightbox mt-4">
                                        <select class="form-control" id="quick_replies">
                                            <option value="">Quick Reply</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="chat-righbox">
                                        <div class="title"><a href="javascript:;" onclick="openPopupGeneralInfo(<?php echo $chatId->id;?>)" >General Info</a></div>
                                        
                                        <div class="modal fade" id="GeneralInfo<?php echo $chatId->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="myModalLabel">General Info</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        
                                                    </div>
                                                    <div class="modal-body">
                                                        <div id="liveChatCustomerInfo">Comming Soon General Info Data</div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="chat-righbox">
                                        <div class="title"><a href="javascript:;" onclick="openPopupVisitedPages(<?php echo $chatId->id;?>)" >Visited Pages</a></div>
                                        
                                        <div class="modal fade" id="VisitedPages<?php echo $chatId->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="myModalLabel">Visited Pages</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        
                                                    </div>
                                                    <div class="modal-body">
                                                        <div id="liveChatVisitedPages">Comming Soon Visited Pages Data</div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="chat-righbox">
                                        <div class="title"><a href="javascript:;" onclick="openPopupAdditionalinfo(<?php echo $chatId->id;?>)" >Additional info</a></div>
                                        
                                        <div class="modal fade" id="AdditionalInfo<?php echo $chatId->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="myModalLabel">Additional info</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="line-spacing" id="liveChatAdditionalInfo">Comming Soon Additional info Data</div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="chat-righbox">
                                        <div class="title"><a href="javascript:;" onclick="openPopupTechnology(<?php echo $chatId->id;?>)" >Technology</a></div>
                                        
                                        <div class="modal fade" id="Technology<?php echo $chatId->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="myModalLabel">Technology</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="line-spacing" id="liveChatTechnology">Comming Soon Technology Data</div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </td>
                               </tr>
                            <?php $srno++;?>
                        @endforeach
                    @endif   
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script>
        function openPopupGeneralInfo(id)
        {
            $('#GeneralInfo'+id).modal('show');
        }
        function openPopupVisitedPages(id)
        {
            $('#VisitedPages'+id).modal('show');   
        }
        function openPopupAdditionalinfo(id)
        {
            $('#AdditionalInfo'+id).modal('show');   
        }
        function openPopupTechnology(id)
        {
            $('#Technology'+id).modal('show');   
        }
        function getLiveChats(id){
            // Close the connection, if open.
            if (websocket.readyState === WebSocket.OPEN) {
                clearInterval(pingTimerObj);
                websocket.close();
            }

            $('#liveChatCustomerInfo').html('Fetching Details...');
            $('#liveChatVisitedPages').html('Fetching Details...');
            $('#liveChatAdditionalInfo').html('Fetching Details...');
            $('#liveChatTechnology').html('Fetching Details...');
            $.ajax({
                        url: "{{ route('livechat.get.message') }}",
                        type: 'POST',
                        dataType: 'json',
                        data: { id : id ,   _token: "{{ csrf_token() }}" },
                    })
                    .done(function(data) {
                        console.log(data);
                        //if(typeof data.data.message != "undefined" && data.length > 0 && data.data.length > 0) {
                        $('#live-message-recieve').empty().html(data.data.message);
                        $('#message-id').val(data.data.id);
                        $('#new_message_count').text(data.data.count);
                        $('#user_name').text(data.data.name);
                        $("li.active").removeClass("active");
                        $("#user"+data.data.id).addClass("active");
                        $('#user_inital').text(data.data.customerInital);
                        $('#selected_customer_store').val(data.data.store_website_id);
                        var customerInfo = data.data.customerInfo;
                        if(customerInfo!=''){
                            customerInfoSetter(customerInfo);
                        }
                        else{
                            $('#liveChatCustomerInfo').html('');
                            $('#liveChatVisitedPages').html('');
                            $('#liveChatAdditionalInfo').html('');
                            $('#liveChatTechnology').html('');
                        }

                        currentChatId = data.data.threadId;

                        //open socket
                        runWebSocket(data.data.threadId);

                        //}
                        console.log("success");
                    })
                    .fail(function() {
                        console.log("error");
                        $('#chatCustomerInfo').html('');
                        $('#chatVisitedPages').html('');
                        $('#chatAdditionalInfo').html('');
                        $('#chatTechnology').html('');
                    });
        }
            $(document).on('change', '#categories', function () {
                if ($(this).val() != "") {
                    var category_id = $(this).val();

                    var store_website_id = $('#selected_customer_store').val();
                  /*  if(store_website_id == ''){
                        store_website_id = 0;
                    }*/
                    $.ajax({
                        url: "{{ url('get-store-wise-replies') }}"+'/'+category_id+'/'+store_website_id,
                        type: 'GET',
                        dataType: 'json'
                    }).done(function(data){
                        console.log(data);
                        if(data.status == 1){
                            $('#quick_replies').empty().append('<option value="">Quick Reply</option>');
                            var replies = data.data;
                            replies.forEach(function (reply) {
                                $('#quick_replies').append($('<option>', {
                                    value: reply.reply,
                                    text: reply.reply,
                                    'data-id': reply.id
                                }));
                            });
                        }
                    });

                }
            });

            $('.quick_comment_add').on("click", function () {
                var textBox = $(".quick_comment").val();
                var quickCategory = $('#categories').val();

                if (textBox == "") {
                    alert("Please Enter New Quick Comment!!");
                    return false;
                }

                if (quickCategory == "") {
                    alert("Please Select Category!!");
                    return false;
                }
                console.log("yes");

                $.ajax({
                    type: 'POST',
                    url: "{{ route('save-store-wise-reply') }}",
                    dataType: 'json',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'category_id' : quickCategory,
                        'reply' : textBox,
                        'store_website_id' : $('#selected_customer_store').val()
                    }
                }).done(function (data) {
                    console.log(data);
                    $(".quick_comment").val('');
                    $('#quick_replies').append($('<option>', {
                        value: data.data,
                        text: data.data
                    }));
                })
            });

            $('#quick_replies').on("change", function(){
                $('.message_textarea').text($(this).val());
            });
    </script>
@endsection