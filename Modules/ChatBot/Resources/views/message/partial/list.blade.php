<table class="table page-template-{{ $page }}">
<thead>
  <tr>
    <th width="2%">Customer #</th>
    <th width="2%">Name</th>
    <th width="15%">User input</th>
    <th width="15%">Bot Replied</th>
    <th width="30%">Images</th>
    <th width="5%">Action</th>
  </tr>
</thead>
<tbody>
<?php if (!empty($pendingApprovalMsg)) {?>
    <?php foreach ($pendingApprovalMsg as $pam) {?>
        <tr>
          <td>{{ $pam->customer_id }}</td>
          <td>{{ $pam->customer_name }}</td>
          <td>{{ $pam->question }}</td>
          <td>{{ $pam->message }}</td>
          <td>
            @if($pam->hasMedia(config('constants.media_tags')))
              <form class="remove-images-form" action="{{ route('chatbot.messages.remove-images') }}" method="post">
                {{ csrf_field() }}  
                @foreach($pam->getMedia(config('constants.media_tags')) as $medias)
                  <div class="panel-img-shorts">
                    <input type="checkbox" name="delete_images[]" value="{{ $medias->pivot->mediable_id.'_'.$medias->id }}" class="close" data-media-id="{{ $medias->id }}" data-mediable-id="{{ $medias->pivot->mediable_id }}">
                    <img width="75px" heigh="75px" src="{{ $medias->getUrl() }}">
                  </div>
                @endforeach
              </form>
            @endif
          </td>
          <td>
            <a href="javascript:;" class="approve-message" data-id="{{ $pam->chat_id }}">
              <img width="15px" height="15px" src="/images/completed-green.png">
            </a>
            <button class="btn-secondary delete-images">Remove Images</button>
          </td>
        </tr>
      <?php }?>
  <?php }?>
</tbody>
<tfoot>
  <tr>
    <td colspan="6"><?php echo $pendingApprovalMsg->appends(request()->except("page"))->links(); ?></td>
  </tr>
</tfoot>
</table>