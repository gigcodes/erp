@foreach($influencers as $influencer)
<tr>
    <td><input type="checkbox" class="selectedInfluencers" name="selectedInfluencers" value="{{$influencer->id}}"> {{date('d-m-y', strtotime($influencer->created_at))}}</td>
    <td><a href="{{ $influencer->url }}" target="_blank">{{ $influencer->name }}</a></td>
    <td class="expand-row-msg" data-name="email" data-id="{{$influencer->id}}">
		<span class="show-short-email-{{$influencer->id}}">{{ str_limit($influencer->email, 12, '...')}}</span>
		<span style="word-break:break-all;" class="show-full-email-{{$influencer->id}} hidden">{{$influencer->email}}</span>
    </td>
    <td class="expand-row-msg" data-name="keyword" data-id="{{$influencer->id}}">
		<span class="show-short-keyword-{{$influencer->id}}">{{ str_limit($influencer->keyword, 12, '...')}}</span>
		<span style="word-break:break-all;" class="show-full-keyword-{{$influencer->id}} hidden">{{$influencer->keyword}}</span>
    </td>    
    <td>{{ $influencer->posts }}</td>
    <td>{{ $influencer->followers }}</td>
    <td>{{ $influencer->following }}</td>
    <!-- <td>{{ $influencer->phone }}</td> -->
    <!-- <td>{{ $influencer->website }}</td> -->
    <!-- <td>{{ $influencer->twitter }}</td> -->
    <!-- <td>{{ $influencer->facebook }}</td> -->
    <td class="expand-row-msg" data-name="country" data-id="{{$influencer->id}}">
		<span class="show-short-country-{{$influencer->id}}">{{ str_limit($influencer->country, 8, '...')}}</span>
		<span style="word-break:break-all;" class="show-full-country-{{$influencer->id}} hidden">{{$influencer->country}}</span>
    </td>
    <td class="expand-row-msg" data-name="description" data-id="{{$influencer->id}}">
		<span class="show-short-description-{{$influencer->id}}">{{ str_limit($influencer->description, 12, '...')}}</span>
		<span style="word-break:break-all;" class="show-full-description-{{$influencer->id}} hidden">{{$influencer->description}}</span>
    </td>  
    <td>
        @php 
        $thread =\App\InstagramThread::where('scrap_influencer_id', $influencer->id)->first();
        @endphp
        @if($thread) 
        <div class="row">
            <div class="col-md-10 cls_remove_rightpadding">
                <textarea name="" class="form-control type_msg message_textarea cls_message_textarea" placeholder="Type your message..." id="message{{ $influencer->id }}"></textarea>
                <input type="hidden" id="message-id" name="message-id" />
            </div>
            <div class="col-md-2 cls_remove_padding">
                <div class="input-group-append">
                    <a href="{{ route('attachImages', ['direct', @$thread->id, 1]) .'?'.http_build_query(['return_url' => 'instagram/influencers'])}}" class="btn btn-image px-1"><img src="{{asset('images/attach.png')}}"/></a>
                    <a class="btn btn-image px-1" href="javascript:;"><span class="send_btn" onclick="sendMessage('{{ $thread->id }}')"><i class="fa fa-location-arrow"></i></span></a>
                </div>
            </div>                                          
        </div>
        @else 
        <p>No thread created</p>
        @endif
    </td> 
</tr> 
@endforeach


