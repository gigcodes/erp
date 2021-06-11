<style type="text/css">
    .cls_remove_rightpadding {
        padding-right: 0px !important;
    }
    .cls_remove_allpadding {
        padding-left: 0px !important;
        padding-right: 0px !important;
    }
    #chat-list-history tr {
        word-break: break-word; 
    }
    .reviewed_msg {
        word-break: break-word; 
    }
</style>
@php
    $isAdmin = Auth::user()->hasRole('Admin');
    $isHod  = Auth::user()->hasRole('HOD of CRM');
    
@endphp
<table class="table table-bordered page-template-{{ $page }}">
    <thead>
    <tr>
        <th width="10%"># Name</th>
        <th width="5%">Website</th>
        <th width="10%">User input</th>
        <th width="15%">Bot Replied</th>
        <th width="15%">Message Box</th>
        <th width="5%">From</th>
        <th width="10%">Images</th>
        <th width="10%">Created</th>
        <th width="10%">Action</th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($pendingApprovalMsg)) {?>
    <?php foreach ($pendingApprovalMsg as $pam) { ?>
    <tr>

        @php

            $context = 'customer';
            $issueID = null;
            if($pam->chatBotReplychat){
            
                $reply = json_decode($pam->chatBotReplychat->reply);
                
                if(isset($reply->context)){
                    $context = $reply->context;
                    $issueID = $reply->issue_id;
                }

            }

        @endphp

        <td data-context="{{ $context }}" data-url={{ route('whatsapp.send', ['context' => $context]) }} {{ $pam->taskUser ? 'data-chat-message-reply-id='.$pam->chat_bot_id : '' }}  data-chat-id="{{ $pam->chat_id }}" data-customer-id="{{$pam->customer_id ?? ( $pam->taskUser ? $issueID : '')}}" data-vendor-id="{{$pam->vendor_id}}">{{  ($pam->vendor_id > 0 ) ? "#".$pam->vendor_id." ".$pam->vendors_name : ( $pam->taskUser ? '#'.$pam->taskUser->id .' ' . $pam->taskUser->name : "#".$pam->customer_id." ".$pam->customer_name  )  }}</td>
        <td>{{ $pam->website_title }}</td>
        <td class="user-input">{{ $pam->question }}</td>
        <td class="boat-replied">{{ $pam->answer }}</td>
        <td class="message-input">
            <div class="row cls_textarea_subbox">
                <div class="col-md-9 cls_remove_rightpadding">
                    <textarea rows="1" class="form-control quick-message-field cls_quick_message addToAutoComplete" data-customer-id="{{ $pam->customer_id }}" name="message" placeholder="Message"></textarea>
                </div>
                
                <div class="col-md-1 cls_remove_allpadding">
                    <input class="" name="add_to_autocomplete" class="add_to_autocomplete" type="checkbox" value="true">
                    <button class="btn btn-sm btn-image send-message1" data-customer-id="{{ $pam->customer_id }}"><img src="/images/filled-sent.png"></button>
                    @if($pam->vendor_id > 0 )
                        <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="vendor" data-id="{{$pam->vendor_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                    @elseif($context === 'task' || $context === 'issue')

                        @php
                            $context__  = $context === 'issue' ? 'developer_task' : $context;
                        @endphp

                        <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="{{ $context__ }}" data-id="{{ $issueID }}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                    @else
                        <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="customer" data-id="{{$pam->customer_id }}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                    @endif
                </div>
            </div>
        </td>
        <td class="boat-replied">{{ $pam->reply_from }}</td>
        <td class="images-layout">
            <form class="remove-images-form" action="{{ route('chatbot.messages.remove-images') }}" method="post">
                {{ csrf_field() }}
                @php
                    $botMessage = \App\ChatMessage::find($pam->chat_id);
                @endphp
                @if(isset($botMessage))
                    @if($botMessage->hasMedia(config('constants.media_tags')))
                        {{ $botMessage->getMedia(config('constants.media_tags'))->count() }}
                    @endif
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
                <img width="15px" title="Remove Images" height="15px" src="/images/do-disturb.png">
            </a>
            @if($pam->suggestion_id)
                <a href="javascript:;" class="add-more-images" data-id="{{ $pam->chat_id }}">
                    <img width="15px" title="Attach More Images" height="15px" src="/images/customer-suggestion.png">
                </a>
            @endif
            <a href="javascript:;" class="resend-to-bot" data-id="{{ $pam->id }}">
                <img width="15px" title="Resend to bot" height="15px" src="/images/icons-refresh.png">
            </a>
            <!-- <span class="check-all" data-id="{{ $pam->chat_id }}">
              <i class="fa fa-indent" aria-hidden="true"></i>
            </span> -->
            <a href="javascript:;" class="approve_message" data-id="{{ $pam->chat_id }}">
                <i class="fa fa-plus" aria-hidden="true"></i>
            </a>
        </td>
    </tr>
    <?php }?>
    <?php }?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="9"><?php echo $pendingApprovalMsg->appends(request()->except("page"))->links(); ?></td>
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
               // location.reload();
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

    $(document).on("click",".resend-to-bot",function () {
        let chatID = $(this).data("id");
        $.ajax({
            type: "GET",
            url: "/chatbot/messages/resend-to-bot",
            data: {
                chat_id : chatID
            },
            dataType : "json",
            success: function (response) {
                if(response.code == 200) {
                    toastr['success'](response.messages);
                }else{
                    toastr['error'](response.messages);
                }
            },
            error: function () {
                toastr['error']('Message not sent successfully!');
            }
        });
    });

</script>