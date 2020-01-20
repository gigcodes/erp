@foreach($posts as $key=>$post)
    <tr>
        <td>{{ date('d-M-Y H:i:s', strtotime($post->posted_at)) }}</td>
        <td>{{ $post->hashTags->hashtag }}</td>
        <td><a style="word-break:break-all; white-space: normal;" href="{{ $post->location }}" target="_blank">{{ $post->location }}</a></td>
        <td>{{ wordwrap($post->caption,75, "\n", true) }}</td>
    </tr>
@endforeach