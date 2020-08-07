@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Live Chat</h2>
            <div class="pull-left">
                <h3>Add User</h3>
                <div class="row">
                    <div class="col-md-3 chat" style="margin-top : 0px !important;">
                        <div class="card_chat mb-sm-3 mb-md-0 contacts_card">
                            <div class="card-header">
                                <div class="input-group">

                                </div>
                            </div>
                            <div class="card-body contacts_body">
                                @php
                                $chatIds = \App\CustomerLiveChat::orderBy('seen','asc')->orderBy('status','desc')->get();
                                $newMessageCount = \App\CustomerLiveChat::where('seen',0)->count();
                                @endphp
                                <ul class="contacts" id="customer-list-chat">
                                    @if(isset($chatIds) && !empty($chatIds))
                                        @foreach ($chatIds as $chatId)
                                            @php
                                            $customer = \App\Customer::where('id',$chatId->customer_id)->first();
                                            $customerInital = substr($customer->name, 0, 1);
                                            @endphp
                                            <li onclick="getLiveChats('{{ $customer->id }}')" id="user{{ $customer->id }}" style="cursor: pointer;">
                                                <div class="d-flex bd-highlight">
                                                    <div class="img_cont">
                                                        <soan class="rounded-circle user_inital">{{ $customerInital }}</soan>
                                                        <span class="online_icon @if($chatId->status == 0) offline @endif "></span>
                                                    </div>
                                                    <div class="user_info">
                                                        <span>{{ $customer->name }}</span>
                                                        <p>{{ $customer->name }} is @if($chatId->status == 0) offline @else online @endif </p>
                                                    </div>
                                                    @if($chatId->seen == 0)<span class="new_message_icon"></span>@endif
                                                </div>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                            <div class="card-footer"></div>
                        </div>
                    </div>
                    <div class="col-md-6 chat">
                        <div class="card_chat">
                            <div class="card-header msg_head">
                                <div class="d-flex bd-highlight align-items-center justify-content-between">
                                    <div class="img_cont">
                                        <soan class="rounded-circle user_inital" id="user_inital"></soan>
                                    </div>
                                    <div class="user_info" id="user_name">
                                    </div>
                                    <div class="video_cam">
                                        <span><i class="fa fa-video"></i></span>
                                        <span><i class="fa fa-phone"></i></span>
                                    </div>
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
                                </div>
                                <span id="action_menu_btn"><i class="fa fa-ellipsis-v"></i></span>
                                <div class="action_menu">
                                </div>
                            </div>
                            <div class="card-body msg_card_body" id="live-message-recieve">
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
                                                <a href="{{ route('attachImages', ['livechat', $customer->id, 1]) .'?'.http_build_query(['return_url' => 'livechat/getLiveChats'])}}" class="btn btn-image px-1"><img src="/images/attach.png"/></a>
                                            </div>
                                            <input type="hidden" id="message-id" name="message-id" />
                                            <textarea name="" class="form-control type_msg" placeholder="Type your message..." id="message"></textarea>
                                            <div class="input-group-append">
                                                <span class="input-group-text send_btn" onclick="sendMessage()"><i class="fa fa-location-arrow"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 customer-info">
                        <div class="chat-righbox">
                            <div class="title">General Info</div>
                            <div id="liveChatCustomerInfo"></div>
                        </div>
                        <div class="chat-righbox">
                            <div class="title">Visited Pages</div>
                            <div id="liveChatVisitedPages">
                            </div>
                        </div>
                        <div class="chat-righbox">
                            <div class="title">Additional info</div>
                            <div class="line-spacing" id="liveChatAdditionalInfo">
                            </div>
                        </div>
                        <div class="chat-righbox">
                            <div class="title">Technology</div>
                            <div class="line-spacing" id="liveChatTechnology">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pull-right">
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script>
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
                        //if(typeof data.data.message != "undefined" && data.length > 0 && data.data.length > 0) {
                        $('#live-message-recieve').empty().html(data.data.message);
                        $('#message-id').val(data.data.id);
                        $('#new_message_count').text(data.data.count);
                        $('#user_name').text(data.data.name);
                        $("li.active").removeClass("active");
                        $("#user"+data.data.id).addClass("active");
                        $('#user_inital').text(data.data.customerInital);

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

    </script>
@endsection