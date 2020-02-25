<table class="table table-bordered page-template-{{ $page }}">
<thead>
  <tr>
    <th width="2%">#</th>
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
          <td>{{ $pam->customer_id }}[{{ $pam->chat_id }}]</td>
          <td>{{ $pam->customer_name }}</td>
          <td>{{ $pam->question }}</td>
          <td>{{ $pam->message }}</td>
          <td class="images-layout">
              <form class="remove-images-form" action="{{ route('chatbot.messages.remove-images') }}" method="post">
                {{ csrf_field() }}  
                  @if($pam->hasMedia(config('constants.media_tags')))
                    @foreach($pam->getMedia(config('constants.media_tags')) as $medias)
                      <div class="panel-img-shorts">
                        <input type="checkbox" name="delete_images[]" value="{{ $medias->pivot->mediable_id.'_'.$medias->id }}" class="remove-img" data-media-id="{{ $medias->id }}" data-mediable-id="{{ $medias->pivot->mediable_id }}">
                        <img width="50px" heigh="50px" src="{{ $medias->getUrl() }}">
                      </div>
                    @endforeach
                  @endif
              </form>
          </td>
          <td>
            <a href="javascript:;" class="approve-message" data-id="{{ $pam->chat_id }}">
              <img width="15px" height="15px" src="/images/completed-green.png">
            </a>
            <a href="javascript:;" class="delete-images" data-id="{{ $pam->chat_id }}">
              <img width="15px" title="Remove Images" height="15px" src="/images/do-not-disturb.png">
            </a>
            @if($pam->suggestion_id)
              <a href="javascript:;" class="add-more-images" data-id="{{ $pam->chat_id }}">
                <img width="15px" title="Attach More Images" height="15px" src="/images/customer-suggestion.png">
              </a>
            @endif
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