@foreach ($posts as $post)
            <tr>
            <td>{{ \Carbon\Carbon::parse($post->created_at)->format('d-m') }}</td>
              <td>{{ $post->account->first_name }}</td>
              <td>{{ $post->caption }}</td>
              <td>{{ $post->post_body }}</td>
              <td>
              @if ($post->hasMedia(config('constants.media_tags')))
              <a data-fancybox="gallery" href="{{ \App\Helpers\CommonHelper::getMediaUrl($post->getMedia(config('constants.media_tags'))->first()) }}"><img width="100" src="{{ \App\Helpers\CommonHelper::getMediaUrl($post->getMedia(config('constants.media_tags'))->first()) }}"></a>

              @endif
              
              </td>
              <td>{{ \Carbon\Carbon::parse($post->posted_on)->format('d-m-y h:m') }}</td>
              <td>{{ $post->status ? 'Posted' : '' }}</td>
              <td></td>
              </tr>
          @endforeach
          {{$posts->appends(request()->except("page"))->links()}}