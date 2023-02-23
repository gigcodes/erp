@foreach ($posts as $post)
    <tr>
      <td>{{ \Carbon\Carbon::parse($post->created_at)->format('d-m-Y') }}</td>
        <td>{{ $post->caption }}</td>
        <td>{{ $post->post_body }}</td>
      
        <td>{{ \Carbon\Carbon::parse($post->posted_on)->format('d-m-y h:m') }}</td>
        <td>{{ $post->status ? 'Posted' : '' }}</td>
        <td><a href="javascript:;" data-id="{{ $post->id }}" class="account-history"><i class="fa fa-history" title="History"></i></a>
        <a href="{{ url('social/'.$post->ref_post_id.'/comments') }}">Comment</a></td>
      </tr>
@endforeach
{{$posts->appends(request()->except("page"))->links()}}