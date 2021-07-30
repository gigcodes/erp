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
    .chatbot .communication{

    }
    .background-grey {
        color: grey;
    }
    @media(max-width:1400px){
        .btns{
padding: 3px 2px;
        }
    }
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #ddd !important;
    }
    .d-inline.form-inline .select2-container{
        max-width: 100% !important;
        /*width: unset !important;*/
    }
    .actions{
        display: flex !important;
        align-items: center;
    }
    .actions a{
        padding: 0 3px !important;
        display: flex !important;
        align-items: center;
    }
    .actions .btn-image img{
        width: 13px !important;
    }
    .read-message{
        float: right;
    }
</style>
@php
    $isAdmin = Auth::user()->hasRole('Admin');
    $isHod  = Auth::user()->hasRole('HOD of CRM');
    
@endphp
<div class="table-responsive">
<table class="table table-bordered chatbot page-template-{{ $page }}">
    <thead>
    <tr>
        <th width="5%"># Name</th>
        <th width="2%">Website</th>
        <th width="11%">User input</th>
        <th width="10%">Bot Replied</th>
        <th width="15%">Message Box </th>
        <th width="4%">From</th>
        <th width="17%">Shortcuts</th>
        <th width="6%">Action</th>

    </tr>
    </thead>
    <tbody>
    <?php if (!empty($pendingApprovalMsg)) {?>
    <?php foreach ($pendingApprovalMsg as $pam) { ?>
    <tr class="customer-raw-line">


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

        <td data-context="{{ $context }}" data-url={{ route('whatsapp.send', ['context' => $context]) }} {{ $pam->taskUser ? 'data-chat-message-reply-id='.$pam->chat_bot_id : '' }}  data-chat-id="{{ $pam->chat_id }}" data-customer-id="{{$pam->customer_id ?? ( $pam->taskUser ? $issueID : '')}}" data-vendor-id="{{$pam->vendor_id}}" data-supplier-id="{{$pam->supplier_id}}" data-chatbot-id="{{$pam->chat_bot_id}}">

            @if($pam->supplier_id > 0)
                @if (strlen($pam->supplier_name) > 5)
               <p style="word-break: break-word;padding: 8px 5px;" data-log_message="{{ $pam->supplier_name }}" class="user-inputt p-0 m-0">{{  substr($pam->supplier_name,0,6)   }}...</p>
                @else
                <p class="p-0 m-0">{{  /*"#".$pam->supplier_id." ".*/$pam->supplier_name  }}</p>
                @endif
        </td>

             @else
            @if (isset($pam->taskUser) && ( strlen($pam->taskUser->name) > 5) || strlen($pam->customer_name) > 5 || $pam->vendor_id > 0 && strlen($pam->vendors_name) > 5)
            <p style="word-break: break-word;padding: 8px 5px;" data-log_message="{{  ($pam->vendor_id > 0 ) ? $pam->vendors_name : ( $pam->taskUser ? $pam->taskUser->name : $pam->customer_name  )  }}" class="user-inputt p-0 m-0">{{  ($pam->vendor_id > 0 ) ? substr($pam->vendors_name,0,6) : ( $pam->taskUser ? substr($pam->taskUser->name,0,6) : substr($pam->customer_name,0,6)  )  }}...</p>
            @else
                <p class="p-0 m-0">{{  ($pam->vendor_id > 0 ) ? $pam->vendors_name  : ( $pam->taskUser ? $pam->taskUser->name : $pam->customer_name  )  }}</p>
            @endif

           </td>
            @endif
                @if (strlen($pam->website_title) > 5)
                    <td style="word-break: break-word;padding: 8px 5px;" data-log_message="{{ $pam->website_title }}" class="log-website-popup user-iput">
                        <p>{{ substr($pam->website_title,0,5) }}...</p>
                    </td>
                @else
                    <td>{{ $pam->website_title }}</td>
                @endif

        <!-- Purpose : Add question - DEVTASK-4203 -->
        @if (strlen($pam->question) > 10)
            <td style="word-break: break-word;padding: 8px 5px;"  class="log-message-popup user-input">{{ substr($pam->question,0,19) }}...
                @if($pam->chat_read_id == 1)
                    <a href="javascript:;" class="read-message" data-value="0" data-id="{{ $pam->chat_bot_id }}">
                        <img width="15px" title="Mark as unread" height="15px" src="/images/completed-green.png">
                    </a>
                @else
                    <a href="javascript:;" class="read-message" data-value="1" data-id="{{ $pam->chat_bot_id }}">
                        <img width="15px" title="Mark as read" height="15px" src="/images/completed.png">
                    </a>
                @endif
            </td>
        @else
            <td class="user-input" style="padding: 8px 5px;">{{ $pam->question }}
                @if($pam->chat_read_id == 1)
                    <a href="javascript:;" class="read-message" data-value="0" data-id="{{ $pam->chat_bot_id }}">
                        <img width="15px" title="Mark as unread" height="15px" src="/images/completed-green.png">
                    </a>
                @else
                    <a href="javascript:;" class="read-message" data-value="1" data-id="{{ $pam->chat_bot_id }}">
                        <img width="15px" title="Mark as read" height="15px" src="/images/completed.png">
                    </a>
                @endif
            </td>
        @endif
{{--            {{ $pam->question }}--}}


        @if (strlen($pam->answer) > 10)
            <td style="word-break: break-word;padding: 8px 5px;" data-log_message="{{ $pam->answer }}" class="bot-reply-popup boat-replied pr-0">{{ substr( $pam->answer ,0,17) }}...
            </td>
        @else
            <td class="boat-replied">{{ $pam->answer }}
            </td>
            @endif


        <td class="message-input pr-2 pt-2" style="padding-bottom: 5px">
            <div style="display: flex" class=" cls_textarea_subbox">
                <div class=" cls_remove_rightpadding">
                    <textarea rows="1" class="form-control quick-message-field cls_quick_message addToAutoComplete" data-customer-id="{{ $pam->customer_id }}" name="message" placeholder="Message"></textarea>
                </div>

                <div style="display: flex;" class="cls_remove_allpadding row-flex">
                    <span style="display: flex;align-items:  center" class="pl-2 pr-2"><input name="add_to_autocomplete" class="m-0 add_to_autocomplete" type="checkbox" value="true"></span>
                    <button class="btn btn-image send-message1 p-0" data-customer-id="{{ $pam->customer_id }}"><img src="/images/filled-sent.png"></button>
                    @if($pam->task_id > 0 )
                        <button style="padding:0 ;" type="button" class="btn pl-1 pr-0 rt btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="task" data-id="{{$pam->task_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                    @elseif($pam->developer_task_id > 0 )
                        <button style="padding:0 ;" type="button" class="btn pl-1 pr-0 rt btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="developer_task" data-id="{{$pam->developer_task_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                    @elseif($pam->vendor_id > 0 )
                        <button style="padding:0 ;" type="button" class="btn pl-1 pr-0 rt btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="vendor" data-id="{{$pam->vendor_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                    @else
                        <button  style="padding:0 ;" type="button" class="btn rt btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="customer" data-id="{{$pam->customer_id }}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                        <button  style="padding:0 ;" type="button" class="btn pl-1 pr-0 rt btn-image load-communication-modal" data-object="customer" data-id="{{$pam->customer_id }}" data-attached="1" data-limit="10" data-load-type="images" data-all="1" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" title="Load Auto Images attacheds"><img src="/images/archive.png" alt=""></button>
                    @endif
                </div>
            </div>
        </td>
        <td class="boat-replied">{{ $pam->reply_from }}</td>
        <td style="/*padding: 5px 7px;*/" class="communication pr-0 pt-2 pb-2">
          <div class="row m-0">
              <div class="col-6 d-inline form-inline p-0">
                  <div style="float:left;width: calc(100% - 5px)">
                      <select name="quickCategory" class="form-control mb-2 quickCategory select-quick-category">
                          <option value="">Select Category</option>
                          @foreach($reply_categories as $category)
                              <option value="{{ $category->approval_leads }}" data-id="{{$category->id}}">{{ $category->name }}</option>
                          @endforeach
                      </select>
                  </div>
{{--                  <div style="float:right;width: 20px;">--}}
{{--                      <a style="padding: 5px 0;" class="btn btn-image delete_category"><img src="/images/delete.png"></a>--}}
{{--                  </div>--}}
              </div>
              <div class="col-6 d-inline form-inline p-0">
                  <div style="float: left; width:calc(100% - 5px)" class="mt-0">
                      <select name="quickComment" class="form-control quickComment select-quick-reply">
                          <option value="">Quick Reply</option>
                      </select>
                  </div>
{{--                  <div style="float: right;width: 20px;">--}}
{{--                      <a style="padding: 5px 0;" class="btn btn-image delete_quick_comment"><img src="/images/delete.png"></a>--}}
{{--                  </div>--}}
              </div>
          </div>
        </td>
        <td>
            <div class="actions">
            <a href="javascript:;"  style="display: inline-block" class="resend-to-bot btns" data-id="{{ $pam->id }}">
                <i style="color: #757575c7;" class="fa fa-refresh" title="Resend to bot" aria-hidden="true"></i>

            </a>
            <a href="javascript:;"  style="display: inline-block" class="approve_message  btns  pt-2" data-id="{{ $pam->chat_id }}">
                <i style="color: #757575c7;" class="fa fa-plus" aria-hidden="true"></i>
            </a>

            @if($pam->approved == 0)
            <a href="javascript:;" style="display: inline-block" class="approve-message btns " data-id="{{ !empty($pam->chat_id) ? $pam->chat_id : $pam->id  }}">
{{--                <img width="15px" height="15px" src="/images/completed.png">--}}
                <i  style="color: #757575c7;" class="fa fa-check-square" aria-hidden="true"></i>

            </a>
            @endif
            @if($pam->suggestion_id)
                <a href="javascript:;"  style="display: inline-block" class="add-more-images btns" data-id="{{ $pam->chat_id }}">
                    <img width="15px" title="Attach More Images" height="15px" src="/images/customer-suggestion.png">
                </a>
            @endif
            @if($pam->customer_id > 0)
                @if($pam->customer_do_not_disturb == 1)
                    <button type="button" class="btn btn-xs btn-image do_not_disturb" data-id="{{$pam->customer_id}}">
                        <i style="color: #c10303;" class="fa fa-ban" aria-hidden="true"></i>
                    </button>
                @else
                    <button type="button" class="btn btn-xs btn-image do_not_disturb" data-id="{{$pam->customer_id}}">
                        <i style="color: #757575c7;" class="fa fa-ban" aria-hidden="true"></i>
                    </button>
                @endif
                <a href="javascript:;"  style="display: inline-block" class="create-customer-ticket-modal btns pt-2" data-customer_id="{{ $pam->customer_id }}" data-id="{{ $pam->chat_id }}">
                    <i style="color: #757575c7;" class="fa fa-plus" aria-hidden="true"></i>
                </a>
                <a class="create-customer-ticket-modal btns pt-2" style="display: inline-block" href="javascript:;" data-customer_id="{{$pam->customer_id}}" data-toggle="modal" data-target="#create-customer-ticket-modal" title="Create Ticket"><i style="color: #757575c7;" class="fa fa-ticket" aria-hidden="true"></i></a>
            @endif

            @if($pam->reply_from == "reminder")
                @if($pam->task_id > 0 )
                    <a href="javascript:;" data-id="{{$pam->task_id}}" data-type="task" class="pd-5 stop-reminder" >
                        <i class="fa fa-bell background-grey" aria-hidden="true"></i>
                    </a>
                @elseif($pam->developer_task_id > 0)
                    <a href="javascript:;" data-id="{{$pam->developer_task_id}}" data-type="developer_task" class="pd-5 stop-reminder" >
                        <i class="fa fa-bell background-grey" aria-hidden="true"></i>
                    </a>
                @endif
            @endif


            <!-- <span class="check-all" data-id="{{ $pam->chat_id }}">
              <i class="fa fa-indent" aria-hidden="true"></i>
            </span> -->
        </div>
        </td>
    </tr>
    <?php }?>
    <?php }?>
    </tbody>
    <tfoot>
    <tr>
        <td class="p-0" colspan="9"><?php echo $pendingApprovalMsg->appends(request()->except("page"))->links(); ?></td>
    </tr>
    </tfoot>
</table>

</div>
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


<!--Log Messages Modal -->
<div id="logMessageModel" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">User Input</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p style="word-break: break-word;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>



<div id="botReply" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Bot Replied</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p style="word-break: break-word;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>





<div id="chatbotname" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Name</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p style="word-break: break-word;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>



<div id="website" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Website</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p style="word-break: break-word;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>



<script type="text/javascript">

    $(document).on('click','.log-message-popup',function(){
        $('#logMessageModel').modal('show');
        $('#logMessageModel p').text($(this).data('log_message'));
    })

    $(document).on('click','.bot-reply-popup',function(){
        $('#botReply').modal('show');
        $('#botReply p').text($(this).data('log_message'));
    })
    $(document).on('click','.user-inputt',function(){
        $('#chatbotname').modal('show');
        $('#chatbotname p').text($(this).data('log_message'));
    })
    $(document).on('click','.log-website-popup',function(){
        $('#website').modal('show');
        $('#website p').text($(this).data('log_message'));
    })



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
               //location.reload();
                if(response.code == 200) {
                    toastr['success']('data updated successfully!');
                    window.location.replace(response.redirect);
                }else{
                    if(response.error != "") {
                        var message = ``;
                        $.each(response.error,function(k,v) {
                            message += v+`<br>`;
                        });
                        toastr['error'](message);
                    }else{
                        errorMessage = response.error ? response.error : 'data is not correct or duplicate!';
                        toastr['error'](errorMessage);
                    }
                }
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

    $(document).on("click",".read-message",function () {
        let chatID = $(this).data("id");
        let value = $(this).data("value");
        var $this = $(this);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: "/chatbot/messages/update-read-status",
            data: {
                chat_id : chatID,
                value  : value
            },
            dataType : "json",
            success: function (response) {
                if(response.code == 200) {
                    toastr['success'](response.messages);
                    if(value == 1) {
                        $this.html('<img width="15px" title="Mark as unread" height="15px" src="/images/completed-green.png">');
                        $this.data("value",0);
                    }else{
                        $this.html('<img width="15px" title="Mark as read" height="15px" src="/images/completed.png">');
                        $this.data("value",1);
                    }
                }else{
                    toastr['error'](response.messages);
                }
            },
            error: function () {
                toastr['error']('Message not sent successfully!');
            }
        });
    });

    $(document).on('click', '.do_not_disturb', function() {
        var id = $(this).data('id');
        var thiss = $(this);
        $.ajax({
            type: "POST",
            url: "{{ url('customer') }}/" + id + '/updateDND',
            data: {
                _token: "{{ csrf_token() }}",
                // do_not_disturb: option
            },
            beforeSend: function() {
                //$(thiss).text('DND...');
            }
        }).done(function(response) {
          if (response.do_not_disturb == 1) {
            var img_url = "/images/do-not-disturb.png";
            $(thiss).html('<img src="'+img_url+'" />');
          } else {
            var img_url = "/images/do-disturb.png";
            $(thiss).html('<img src="'+img_url+'" />');
          }
        }).fail(function(response) {
          alert('Could not update DND status');
          console.log(response);
        });
  });

    $(document).on("click",".stop-reminder",function() {
        var id = $(this).data("id");
        var type = $(this).data("type");

        $.ajax({
            type: "GET",
            url: "/chatbot/messages/stop-reminder",
            data: {
                _token: "{{ csrf_token() }}",
                id : id,
                type : type
                // do_not_disturb: option
            },
            beforeSend: function() {
                //$(thiss).text('DND...');
            },
            dataType : "json"
        }).done(function(response) {
            if(response.code == 200) {
                toastr['success'](response.messages);
            }else{
                toastr['error'](response.messages);
            }
        }).fail(function(response) {
          toastr['error']('Could not update DND status');
        });
    });

</script>