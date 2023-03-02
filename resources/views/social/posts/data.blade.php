@foreach ($posts as $post)
    <tr>
      <td>{{ \Carbon\Carbon::parse($post->created_at)->format('d-m-Y') }}</td>
        <?php
          $config_name = App\Social\SocialConfig::where('id',$post->config_id)->first();
        ?>
        <td>@if(isset($config_name->storeWebsite)) {{ $config_name->storeWebsite->title }} @endif</td>
        <td>{{ $post->caption }}</td>
        <td>{{ $post->post_body }}</td>
      
        <td>{{ \Carbon\Carbon::parse($post->posted_on)->format('d-m-y h:m') }}</td>
        <td>{{ $post->status ? 'Posted' : '' }}</td>
        <td>
          <a href="javascript:;" data-id="{{ $post->id }}" class="account-history"><i class="fa fa-history" title="History"></i></a>
          <a href="{{ url('social/'.$post->ref_post_id.'/comments') }}"><i class="fa fa-envelope" aria-hidden="true" title="comment"></i></a>
          <a href="javascript:;" data-id="{{ $post->ref_post_id }}" class="post-delete"><i class="fa fa-trash-o" title="Delete Post"></i></a>
        </td>
      </tr>
@endforeach
{{$posts->appends(request()->except("page"))->links()}}