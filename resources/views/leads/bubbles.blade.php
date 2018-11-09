@foreach($messages as $message)
           @if($message['status'] == '0') 
                <div class="talk-bubble tri-right round left-in white">
                      <div class="talktext">
                       <p>{!! $message['body'] !!}</p>                        
                        <em>Customer {{ App\Helpers::timeAgo($message['created_at']) }} </em> 
                      </div>
                </div>

            @else
                <div class="talk-bubble tri-right round right-in green" data-messageid="{{$message['id']}}">            
                  <div class="talktext">                    
                     <p id="message_body_{{$message['id']}}">{!! $message['body']  !!} </p>
                    
                    <em>Solo {{ App\Helpers::timeAgo($message['created_at']) }}  <img id="status_img_{{$message['id']}}" src="/images/{{$message['status']}}.png"> &nbsp;
                    @if($message['status'] == '2' and App\Helpers::getadminorsupervisor() == false)     
                        <a href="/message/updatestatus?status=3&id={{$message['id']}}&moduleid={{$message['moduleid']}}" style="font-size: 9px">Mark as sent </a>
                    @endif 

                    @if($message['status'] == '1' and App\Helpers::getadminorsupervisor() == true)     
                        <a href="/message/updatestatus?status=2&id={{$message['id']}}&moduleid={{$message['moduleid']}}" style="font-size: 9px">Approve</a>

                        <a style="font-size: 9px" style="cursor: pointer;">Edit</a>
                    @endif 

                    </em>
                      @if($message['status'] == '2' and App\Helpers::getadminorsupervisor() == false)
                          <button class="copy-button btn btn-primary" data-id="{{$message['id']}}" moduleid="{{$message['moduleid']}}" moduletype="leads"> Copy message </button>
                      @endif
                  </div>
             </div>
            @endif

         @endforeach   
         @if(!empty($message['id']))
         <div class="show_more_main" id="show_more_main{{$message['id']}}" >
          <span id="{{$message['id']}}" class="show_more" title="Load more posts" data-moduleid={{$message['moduleid']}} data-moduletype="leads">Show more</span>
          <span class="loding" style="display: none;"><span class="loding_txt">Loading...</span></span>
         </div> 
          @endif