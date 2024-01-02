@foreach ($posts as $post)
    <tr>
      <td>
        @if($post->image_path)
          <img width="100" class="img-responsive thumbnail thumbnail-wrapper mb-0 mr-0" src="{{ asset($post->image_path) ?? '' }}">
        @endif
      </td>
        <td>{{ \Carbon\Carbon::parse($post->created_at)->format('d-m-Y') }}</td>
        <?php
          $config_name = App\Social\SocialConfig::where('id',$post->config_id)->first();
        ?>
        <td>@if(isset($config_name->storeWebsite)) {{ $config_name->storeWebsite->title }} @endif</td>
        <td>{{ $config_name->platform }}</td>
        <td>{{ $post->caption }}</td>
        <!-- <td>{{ $post->post_body }}</td> -->
        <td>{{ $post->hashtag }}</td>
        <td>{{ $post->translation_approved_by??'-' }}</td>
      
        <td>{{ \Carbon\Carbon::parse($post->created_at)->format('d-m-y h:m') }}</td>
       <!-- <td>{{ $post->status ? 'Posted' : '' }}</td> -->
        <td><?php
          if(isset($post->status) && $post->status == 1){
              echo "Posted";
          }elseif (isset($post->status) && $post->status == 2) {
              echo "Hold For Approval";
          }else {
              echo "Error";
          }
        ?></td>
        <td>
          <?php
            if($post->status == 2){ ?>
              <a href="javascript:;" data-id="{{ $post->id }}" class="translation-approval"><i class="fa fa-check" title="Approve"></i></a>
            <?php }
          ?>
          <a href="javascript:;" data-id="{{ $post->id }}" class="account-history"><i class="fa fa-history" title="History"></i></a>
          <a href="{{ url('social/'.$post->ref_post_id.'/comments') }}"><i class="fa fa-envelope" aria-hidden="true" title="comment"></i></a>
          <a href="javascript:;" data-id="{{ $post->ref_post_id }}" class="post-delete"><i class="fa fa-trash-o" title="Delete Post"></i></a>
        </td>
      </tr>
@endforeach
{{$posts->appends(request()->except("page"))->links()}}