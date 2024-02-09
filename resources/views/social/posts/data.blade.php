@foreach ($posts as $post)
    <tr>
        <td>
            @if($post->image_path)
                <img width="100" class="img-responsive thumbnail thumbnail-wrapper mb-0 mr-0"
                     src="{{ asset($post->image_path) ?? '' }}" alt="">
            @endif
        </td>
        <td>@if(isset($post->account->storeWebsite))
                {{ $post->account->storeWebsite->title }}
            @endif
        </td>
        <td>{{ $post->account->platform }}</td>
        <td>{{ $post->caption }}</td>
        <td>{{ $post->hashtag }}</td>
        <td>{{ $post->translation_approved_by ??'-' }}</td>
        <td>{{ \Carbon\Carbon::parse($post->created_at)->format('d-m-y h:m') }}</td>
        <td>
            @if (isset($post->status) && $post->status == 1)
                Posted
            @elseif (isset($post->status) && $post->status == 2)
                Hold For Approval
            @else
                Error
            @endif
        </td>
        <td>
            @if ($post->status == 2)
                <a href="javascript:" data-id="{{ $post->id }}" class="translation-approval">
                    <i class="fa fa-check" title="Approve"></i>
                </a>
            @endif
            <a href="javascript:" data-id="{{ $post->id }}" class="account-history">
                <i class="fa fa-history" title="History"></i>
            </a>
            <a href="{{ url('social/'.$post->ref_post_id.'/comments') }}">
                <i class="fa fa-envelope" aria-hidden="true" title="comment"></i>
            </a>
            <a href="javascript:" data-id="{{ $post->ref_post_id }}" class="post-delete">
                <i class="fa fa-trash-o" title="Delete Post"></i>
            </a>
        </td>
    </tr>
@endforeach
{{$posts->appends(request()->except("page"))->links()}}
