<table class="table table-bordered page-template-{{ $page }}">
    <thead>
    <tr>
        <th width="2%">#</th>
        <th width="2%">Name</th>
        <th width="15%">User input</th>
        <th width="15%">Bot Replied</th>
        <th width="15%">From</th>
        <th width="30%">Images</th>
        <th width="15%">Created</th>
        <th width="5%">Action</th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($pendingApprovalMsg)) {?>
    <?php foreach ($pendingApprovalMsg as $pam) {?>
    <tr>
        <td>{{ $pam->customer_id }}[{{ $pam->chat_id }}]</td>
        <td>{{ $pam->customer_name }}</td>
        <td class="user-input">{{ $pam->question }}</td>
        <td class="boat-replied">{{ $pam->answer }}</td>
        <td class="boat-replied">{{ $pam->reply_from }}</td>
        <td class="images-layout">
            <form class="remove-images-form" action="{{ route('chatbot.messages.remove-images') }}" method="post">
                {{ csrf_field() }}
                @if($pam->hasMedia(config('constants.media_tags')))
                    @foreach($pam->getMedia(config('constants.media_tags')) as $medias)
                        <div class="panel-img-shorts">
                            <input type="checkbox" name="delete_images[]"
                                   value="{{ $medias->pivot->mediable_id.'_'.$medias->id }}" class="remove-img"
                                   data-media-id="{{ $medias->id }}"
                                   data-mediable-id="{{ $medias->pivot->mediable_id }}">
                            <img width="50px" heigh="50px" src="{{ $medias->getUrl() }}">
                        </div>
                    @endforeach
                @endif
            </form>
        </td>
        <td>{{ $pam->created_at }}</td>
        <td>
            @if($pam->approved == 0)
            <a href="javascript:;" class="approve-message" data-id="{{ $pam->chat_id }}">
                <img width="15px" height="15px" src="/images/completed-green.png">
            </a>
            @endif
            <a href="javascript:;" class="delete-images" data-id="{{ $pam->chat_id }}">
                <img width="15px" title="Remove Images" height="15px" src="/images/do-not-disturb.png">
            </a>
            @if($pam->suggestion_id)
                <a href="javascript:;" class="add-more-images" data-id="{{ $pam->chat_id }}">
                    <img width="15px" title="Attach More Images" height="15px" src="/images/customer-suggestion.png">
                </a>
            @endif
            <!-- <span class="check-all" data-id="{{ $pam->chat_id }}">
              <i class="fa fa-indent" aria-hidden="true"></i>
            </span> -->
                @if($pam->chat_message_id !== $pam->chat_id)
            <a href="javascript:;" class="approve_message" data-id="{{ $pam->chat_id }}">
                <i class="fa fa-plus" aria-hidden="true"></i>
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


<div id="approve-reply-popup" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="<?php echo route("chatbot.question.save"); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Create Intent</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <input type="hidden" name="chat_message_id" value="{{ isset($pam) ? $pam->chat_id : null}}">
                    @include('chatbot::partial.form.value')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary form-save-btn">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(".approve_message").on("click", function () {
        var $this = $(this);
        $("#approve-reply-popup").modal("show");
        $('.user-input').text();
        $('#approve-reply-popup [name="question[]"').val($this.closest("tr").find('.user-input').text())
    });
    $('#entity_details').hide();
    $('#erp_details').hide();

    $(".form-save-btn").on("click",function(e) {
        e.preventDefault();

        var form = $(this).closest("form");
        $.ajax({
            type: form.attr("method"),
            url: form.attr("action"),
            data: form.serialize(),
            dataType : "json",
            success: function (response) {
                location.reload();
                // if(response.code == 200) {
                //     toastr['success']('data updated successfully!');
                //     window.location.replace(response.redirect);
                // }else{
                //     errorMessage = response.error ? response.error : 'data is not correct or duplicate!';
                //     toastr['error'](errorMessage);
                // }
            },
            error: function () {
                toastr['error']('Could not change module!');
            }
        });
    });
</script>