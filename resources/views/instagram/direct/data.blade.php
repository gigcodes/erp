 <?php
                        $srno=1;
                        ?>
                         @if(isset($threads) && !empty($threads))
                            @foreach ($threads as $thread)
                                @if(!$thread->account)
                                    <?php continue; ?>
                                @endif
                                <tr>
                                    <td>{{ $srno }} @if($thread->account->new_message == 1) <p id="circle"></p> @endif </td>
                                    <td>@if( $thread->account->storeWebsite) {{ $thread->account->storeWebsite->title }} @endif</td>
                                    <td><p>Send To : <a href="https:://instagram.com/{{ $thread->instagramUser->username }}" target="_blank">@if($thread->instagramUser->fullname){{ $thread->instagramUser->fullname }}@else {{ $thread->instagramUser->username }} @endif</a></p><p>Sent From : {{ $thread->account->last_name }}</p> <br> </td>
                                    
                                    <td style="width: 10% !important;">
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
                                        <div class="typing-indicator" id="typing-indicator"@if($thread->lastMessage) @if($thread->lastMessage->sent == 1) style="color: green;" @else style="color: red;" @endif>{{ $thread->lastMessage->message }}@endif</div>
                                        <button type="button" class="btn btn-xs btn-image load-direct-chat-model" data-object="direct" data-id="{{ $thread->id }}" title="Load messages"><img src="https://erp.amourint.com/images/chat.png" alt=""></button>
                                        <div class="row">
                                            <div class="col-md-11 cls_remove_rightpadding">
                                                <textarea name="" class="form-control type_msg message_textarea cls_message_textarea" placeholder="Type your message..." id="message{{ $thread->id }}"></textarea>
                                                <input type="hidden" id="message-id" name="message-id" />
                                            </div>
                                            <div class="col-md-1 cls_remove_padding">
                                                <div class="input-group-append">
                                                    <a href="{{ route('attachImages', ['direct', @$thread->id, 1]) .'?'.http_build_query(['return_url' => 'instagram/direct'])}}" class="btn btn-image px-1"><img src="{{asset('images/attach.png')}}"/></a>
                                                    <a class="btn btn-image px-1" href="javascript:;"><span class="send_btn" onclick="sendMessage('{{ $thread->id }}')"><i class="fa fa-location-arrow"></i></span></a>
                                                </div>
                                            </div>                                          
                                        </div>
                                        <div class="row cls_quick_reply_box">
                                            <div class="col-md-4 cls_remove_rightpadding">
                                                <select class="form-control" id="quick_replies">
                                                    <option value="">Quick Reply</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 cls_remove_rightpadding">
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
                                            <div class="col-md-4">
                                                <div class="row">
                                                    <div class="col-md-9 cls_remove_rightpadding">
                                                        <input type="text" name="quick_comment" placeholder="New Quick Comment" class="form-control quick_comment">
                                                    </div>
                                                    <div class="col-md-3 cls_quick_commentadd_box">
                                                        <button class="btn quick_comment_add"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div onclick="getLiveChats('{{ $thread->account->id }}')" class="card-body msg_card_body" style="display: none;" id="live-message-recieve">
                                            @if(isset($message) && !empty($message))
                                                @foreach($message as $msg)
                                                    {!! $msg !!}
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>
                                   </tr>
                                <?php $srno++;?>
                            @endforeach
                        @endif  