@foreach($posts as $key=>$post)
    <tr>
        <td>{{ date('d-M-Y H:i:s', strtotime($post->posted_at)) }}</td>
        <td>{{ $post->hashTags->hashtag }}</td>
        <td>
        	{{ $post->title }}
			<br>
			<button type="button" class="btn btn-image make-remark d-inline" data-toggle="modal" data-target="#makeRemarkModal" data-id="{{ $post->id }}"><img src="/images/remark.png" /></button>
        </td>
        <td>{{ $post->address }}</td>
        <td style="word-break:break-all; white-space: normal; line-height:190%">
        	@if ($post->phone == '' && $post->location == '' && $post->emailaddress == '' && $post->facebook == '' && $post->instagram == '' && $post->twitter == '' && $post->youtube == '' && $post->linkedin == '' && $post->pinterest == '')
        	N/A
        	@endif
        	@if ($post->phone != '')
        	<img src="{{ asset('images/call.png') }}" alt="" style="width: 18px;"> {{ $post->phone }}<br>
        	@endif
        	@if ($post->location != '')
        	<img src="{{ asset('images/url_128.png') }}" alt="" style="width: 18px;"> <a style="word-break:break-all; white-space: normal;" href="{{ $post->location }}" target="_blank">{{ $post->location }}</a><br>
        	@endif
        	@if ($post->emailaddress != '')
        	<img src="{{ asset('images/email_128.png') }}" alt="" style="width: 18px;"> {{ $post->emailaddress }}<br>
        	@endif
        	@if ($post->facebook != '')
        	<img src="{{ asset('images/facebook_128.png') }}" alt="" style="width: 18px;"> <a style="word-break:break-all; white-space: normal;" href="{{ $post->facebook }}" target="_blank">{{ $post->facebook }}</a><br>
        	@endif
        	@if ($post->instagram != '')
        	<img src="{{ asset('images/instagram_128.png') }}" alt="" style="width: 18px;"> <a style="word-break:break-all; white-space: normal;" href="{{ $post->instagram }}" target="_blank">{{ $post->instagram }}</a><br>
        	@endif
        	@if ($post->twitter != '')
        	<img src="{{ asset('images/twitter_128.png') }}" alt="" style="width: 18px;"> <a style="word-break:break-all; white-space: normal;" href="{{ $post->twitter }}" target="_blank">{{ $post->twitter }}</a><br>
        	@endif
        	@if ($post->youtube != '')
        	<img src="{{ asset('images/youtube_128.png') }}" alt="" style="width: 18px;"> <a style="word-break:break-all; white-space: normal;" href="{{ $post->youtube }}" target="_blank">{{ $post->youtube }}</a><br>
        	@endif
        	@if ($post->linkedin != '')
        	<img src="{{ asset('images/linkedin_128.png') }}" alt="" style="width: 18px;"> <a style="word-break:break-all; white-space: normal;" href="{{ $post->linkedin }}" target="_blank">{{ $post->linkedin }}</a><br>
        	@endif
        	@if ($post->pinterest != '')
        	<img src="{{ asset('images/pinterest_128.png') }}" alt="" style="width: 18px;"> <a style="word-break:break-all; white-space: normal;" href="{{ $post->pinterest }}" target="_blank">{{ $post->pinterest }}</a><br>
        	@endif
        </td>
        <td class="expand-row">
			<div class="td-mini-container">
				{{ strlen($post->caption) > 30 ? substr($post->caption, 0, 30).'...' : $post->caption }}
			</div>
			<div class="td-full-container hidden">
				{{ $post->caption }}
			</div>
        </td>
    </tr>
@endforeach