@foreach ($posts as $post)
    <tr>
      <td>{{ \Carbon\Carbon::parse($post->created_at)->format('d-m-Y') }}</td>
        <td>{{ $post->caption }}</td>
        <td>{{ $post->post_body }}</td>
        <td>
            <img width="100" src="{{ env('APP_URL').'/'.$post->image_path }}"></a>
          
        </td>
        <td>{{ \Carbon\Carbon::parse($post->posted_on)->format('d-m-y h:m') }}</td>
        <td>{{ $post->status ? 'Posted' : '' }}</td>
        <!-- <td></td> -->
      </tr>
@endforeach
{{$posts->appends(request()->except("page"))->links()}}