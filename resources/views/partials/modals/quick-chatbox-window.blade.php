<!-- Modal -->
<div id="quick-chatbox-window-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="width:90%; max-width: 90%;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3 chat" style="margin-top : 0px !important;">
                        <div class="card_chat mb-sm-3 mb-md-0 contacts_card">
                            <div class="card-header">
                                <h3>Chats</h3>
                                <!-- <div class="input-group">
                                    <input type="text" placeholder="Search..." name="" class="form-control search">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text search_btn"><i class="fa fa-search"></i></span>
                                        </div> 
                                </div> -->
                            </div>
                            <div class="card-body contacts_body">
                                @php
                                $chatIds = \App\CustomerLiveChat::orderBy('seen','asc')->orderBy('status','desc')->get();
                                $newMessageCount = \App\CustomerLiveChat::where('seen',0)->count();
                                @endphp
                                <ul class="contacts" id="customer-list-chat">
                                    @foreach ($chatIds as $chatId)
                                    @php
                                    $customer = \App\Customer::where('id',$chatId->customer_id)->first();
                                    $customerInital = substr($customer->name, 0, 1);
                                    @endphp
                                        <input type="hidden" id="live_selected_customer_store" value="{{ $customer->store_website_id }}" />
                                        <li onclick="getChats('{{ $customer->id }}')" id="user{{ $customer->id }}" style="cursor: pointer;">

                                        <div class="d-flex bd-highlight">
                                            <div class="img_cont">
                                                <soan class="rounded-circle user_inital">{{ $customerInital }}</soan>
                                                {{-- <img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img"> --}}
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
                                        {{-- <img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="rounded-circle user_img"> --}}

                                    </div>
                                    <div class="user_info" id="user_name">
                                        {{-- <span>Chat with Khalid</span>
                                            <p>1767 Messages</p> --}}
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
                                    {{-- <ul>
                                            <li><i class="fa fa-user-circle"></i> View profile</li>
                                            <li><i class="fa fa-users"></i> Add to close friends</li>
                                            <li><i class="fa fa-plus"></i> Add to group</li>
                                            <li><i class="fa fa-ban"></i> Block</li>
                                        </ul> --}}
                                </div>
                            </div>
                            <div class="card-body msg_card_body" id="message-recieve">

                            </div>
                            <div class="typing-indicator" id="typing-indicator"></div>
                            <div class="card-footer">
                                <div class="input-group">
                                    <div class="card-footer">
                                        <div class="input-group">
                                            @if(isset($customer))
                                                <div class="input-group-append">
                                                    <a class="btn btn-image px-1 send-attached-images">
                                                        <img src="/images/attach.png"/>
                                                    </a>
                                                </div>
                                            @endif
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
                            <div id="chatCustomerInfo"></div>

                        </div>
                        <div class="chat-righbox">
                            <div class="title">Visited Pages</div>
                            <div id="chatVisitedPages">
                                
                            </div>
                        </div>
                        <div class="chat-righbox">
                            <div class="title">Additional info</div>
                            <div class="line-spacing" id="chatAdditionalInfo">
                                
                            </div>
                        </div>
                        <div class="chat-righbox">
                            <div class="title">Technology</div>
                            <div class="line-spacing" id="chatTechnology">
                            </div>
                        </div>
                        <div class="chat-rightbox">
                            @php
                            $all_categories = \App\ReplyCategory::all();
                            @endphp
                            <select class="form-control" id="keyword_category">
                                <option value="">Select Category</option>
                                @if(isset($all_categories))
                                    @foreach ($all_categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="chat-rightbox mt-4">
                            <input type="text" name="quick_comment_live" placeholder="New Quick Comment" class="form-control quick_comment_live">
                            <button class="btn btn-secondary quick_comment_add_live">+</button>
                        </div>
                        <div class="chat-rightbox mt-4">
                            <select class="form-control" id="live_quick_replies">
                                <option value="">Quick Reply</option>
                            </select>
                        </div>
                    </div>
                </div>
           </div>
        </div>
    </div>
</div>