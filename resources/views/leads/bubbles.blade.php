@foreach($messages as $message)
           @if($message['status'] == '0' || $message['status'] == '5' || $message['status'] == '6')
                <div class="talk-bubble tri-right round left-in white">
                      <div class="talktext">
                        @if (strpos($message['body'], 'message-img') !== false)
                          @php
                            preg_match_all('/<img src="(.*?)" class="message-img" \/>/', $message['body'], $match);
                            $images = '<br>';
                            foreach ($match[0] as $key => $image) {
                              $images .= str_replace('message-img', 'message-img thumbnail-200', $image);
                            }
                          @endphp

                          <p class="collapsible-message"
                              data-messageshort="{{ strlen(substr($message['body'], 0, strpos($message['body'], '<img'))) > 150 ? (substr($message['body'], 0, 147) . '...' . $images) : substr($message['body'], 0, strpos($message['body'], '<img')) . $images }}"
                              data-message="{{ $message['body'] }}"
                              data-expanded="false">
                            {!! strlen(substr($message['body'], 0, strpos($message['body'], '<img'))) > 150 ? (substr($message['body'], 0, 147) . '...' . $images) : substr($message['body'], 0, strpos($message['body'], '<img')) . $images !!}
                          </p>
                        @else
                          <p class="collapsible-message"
                              data-messageshort="{{ strlen($message['body']) > 150 ? (substr($message['body'], 0, 147) . '...') : $message['body'] }}"
                              data-message="{{ $message['body'] }}"
                              data-expanded="false">
                            {!! strlen($message['body']) > 150 ? (substr($message['body'], 0, 147) . '...') : $message['body'] !!}
                          </p>
                        @endif

                        <em>Customer {{ $message['created_at'] }} </em>

                        @if ($message['status'] == '0')
                          <a href data-url="/message/updatestatus?status=5&id={{$message['id']}}&moduleid={{$message['moduleid']}}&moduletype={{ $moduletype }}" style="font-size: 9px" class="change_message_status">Mark as Read </a>
                        @endif
                        @if ($message['status'] == '0') | @endif
                        @if ($message['status'] == '0' || $message['status'] == '5')
                          <a href data-url="/message/updatestatus?status=6&id={{$message['id']}}&moduleid={{$message['moduleid']}}&moduletype={{ $moduletype }}" style="font-size: 9px" class="change_message_status">Mark as Replied </a>
                        @endif
                      </div>
                </div>

              @elseif($message['status'] == '4')
                  <div class="talk-bubble tri-right round right-in blue" data-messageid="{{$message['id']}}">
                    <div class="talktext">
                      @if (strpos($message['body'], 'message-img') !== false)
                        @php
                          preg_match_all('/<img src="(.*?)" class="message-img" \/>/', $message['body'], $match);
                          $images = '<br>';
                          foreach ($match[0] as $key => $image) {
                            $images .= str_replace('message-img', 'message-img thumbnail-200', $image);
                          }
                        @endphp

                        <p class="collapsible-message"
                            data-messageshort="{{ strlen(substr($message['body'], 0, strpos($message['body'], '<img'))) > 150 ? (substr($message['body'], 0, 147) . '...' . $images) : substr($message['body'], 0, strpos($message['body'], '<img')) . $images }}"
                            data-message="{{ $message['body'] }}"
                            data-expanded="false">
                          {!! strlen(substr($message['body'], 0, strpos($message['body'], '<img'))) > 150 ? (substr($message['body'], 0, 147) . '...' . $images) : substr($message['body'], 0, strpos($message['body'], '<img')) . $images !!}
                        </p>
                      @else
                        <p class="collapsible-message"
                            data-messageshort="{{ strlen($message['body']) > 150 ? (substr($message['body'], 0, 147) . '...') : $message['body'] }}"
                            data-message="{{ $message['body'] }}"
                            data-expanded="false">
                          {!! strlen($message['body']) > 150 ? (substr($message['body'], 0, 147) . '...') : $message['body'] !!}
                        </p>
                      @endif

                      <em>{{ App\Helpers::getUserNameById($message['userid']) }} {{ $message['created_at'] }}  <img id="status_img_{{$message['id']}}" src="/images/1.png"> &nbsp;</em>
                    </div>
               </div>
             @else
                <div class="talk-bubble tri-right round right-in green" data-messageid="{{$message['id']}}">
                  <div class="talktext">
                     {{-- <p id="message_body_{{$message['id']}}">{!! $message['body']  !!} </p> --}}
                       <span id="message_body_{{$message['id']}}">
                         @if (strpos($message['body'], 'message-img') !== false)
                           @php
                             preg_match_all('/<img src="(.*?)" class="message-img" \/>/', $message['body'], $match);
                             $images = '<br>';
                             foreach ($match[0] as $key => $image) {
                               $images .= str_replace('message-img', 'message-img thumbnail-200', $image);
                             }
                           @endphp

                           <p class="collapsible-message"
                               data-messageshort="{{ strlen(substr($message['body'], 0, strpos($message['body'], '<img'))) > 150 ? (substr($message['body'], 0, 147) . '...' . $images) : substr($message['body'], 0, strpos($message['body'], '<img')) . $images }}"
                               data-message="{{ $message['body'] }}"
                               data-expanded="false">
                             {!! strlen(substr($message['body'], 0, strpos($message['body'], '<img'))) > 150 ? (substr($message['body'], 0, 147) . '...' . $images) : substr($message['body'], 0, strpos($message['body'], '<img')) . $images !!}
                           </p>
                         @else
                           <p class="collapsible-message"
                               data-messageshort="{{ strlen($message['body']) > 150 ? (substr($message['body'], 0, 147) . '...') : $message['body'] }}"
                               data-message="{{ $message['body'] }}"
                               data-expanded="false">
                             {!! strlen($message['body']) > 150 ? (substr($message['body'], 0, 147) . '...') : $message['body'] !!}
                           </p>
                         @endif
                       </span>
                       <textarea name="message_body" rows="8" class="form-control" id="edit-message-textarea{{$message['id']}}" style="display: none;">{!! $message['body'] !!}</textarea>

                    <em>{{ App\Helpers::getUserNameById($message['userid']) }} {{ $message['created_at'] }}  <img id="status_img_{{$message['id']}}" src="/images/{{$message['status']}}.png"> &nbsp;
                    @if($message['status'] == '2' and App\Helpers::getadminorsupervisor() == false)
                        <a href data-url="/message/updatestatus?status=3&id={{$message['id']}}&moduleid={{$message['moduleid']}}" style="font-size: 9px" class="change_message_status">Mark as sent </a>
                    @endif

                    @if($message['status'] == '1' and App\Helpers::getadminorsupervisor() == true)
                        <a href data-url="/message/updatestatus?status=2&id={{$message['id']}}&moduleid={{$message['moduleid']}}" style="font-size: 9px" class="change_message_status">Approve</a>

                        <a style="font-size: 9px" style="cursor: pointer;">Edit</a>
                    @endif

                    </em>
                      @if($message['status'] == '2' and App\Helpers::getadminorsupervisor() == false)
                        @if (strpos($message['body'], 'message-img') !== false)
                          <button class="copy-button btn btn-primary" data-id="{{$message['id']}}" moduleid="{{$message['moduleid']}}" moduletype="{{ $moduletype }}" data-message="{{ substr($message['body'], 0, strpos($message['body'], '<img')) }}"> Copy message </button>
                        @else
                          <button class="copy-button btn btn-primary" data-id="{{$message['id']}}" moduleid="{{$message['moduleid']}}" moduletype="{{ $moduletype }}" data-message="{{ $message['body'] }}"> Copy message </button>
                        @endif
                      @endif
                  </div>
             </div>
            @endif

         @endforeach
         @if(!empty($message['id']))
         <div class="show_more_main" id="show_more_main{{$message['id']}}" >
          <span id="{{$message['id']}}" class="show_more" title="Load more posts" data-moduleid={{$message['moduleid']}} data-moduletype="{{ $moduletype }}">Show more</span>
          <span class="loding" style="display: none;"><span class="loding_txt">Loading...</span></span>
         </div>
          @endif
