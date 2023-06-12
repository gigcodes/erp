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
        <th width="2%">Name</th>
        <th width="2%">Website</th>
        <th width="2%">Message Type</th>
        <th width="8%">User input</th>
        <th width="8%">Bot Replied</th>
        <th width="8%">Bot Suggested Replay</th>
        <th width="30%">Message Box </th>
        <th width="2%">From</th>
        <th width="2%">Shortcuts</th>
        <th width="2%">Action</th>

    </tr>
    </thead>
    <tbody>
    <?php if (!empty($pendingApprovalMsg)) {?>

    <?php foreach ($pendingApprovalMsg as $index =>$pam) { ?>
    <tr class="customer-raw-line">


        @php



            $context = 'email';
            $issueID = null;
            if($pam->chatBotReplychat){

                $reply = json_decode($pam->chatBotReplychat->reply);

                if(isset($reply->context)){
                    $context = $reply->context;
                    $issueID = $reply->issue_id;
                }

            }

        @endphp

        <td data-context="{{ $context }}" data-url={{ route('whatsapp.send', ['context' => $context]) }} {{ $pam->taskUser ? 'data-chat-message-reply-id='.$pam->chat_bot_id : '' }}  data-chat-id="{{ $pam->chat_id }}" data-customer-id="{{$pam->customer_id ?? ( $pam->taskUser ? $issueID : '')}}" data-vendor-id="{{$pam->vendor_id}}" data-supplier-id="{{$pam->supplier_id}}" data-chatbot-id="{{$pam->chat_bot_id}}" data-email-id="{{$pam->email_id}}">
            @if($pam->supplier_id > 0)
                @if (strlen($pam->supplier_name) > 5)
               <p data-log_message="{{ $pam->supplier_name }}" class="user-inputt p-0 m-0">{{  substr($pam->supplier_name,0,4)   }}..</p>
                @else
                <p class="p-0 m-0">{{  /*"#".$pam->supplier_id." ".*/$pam->supplier_name  }}</p>
                @endif
        </td>

             @else
            @if (isset($pam->taskUser) && ( strlen($pam->taskUser->name) > 5) || strlen($pam->customer_name) > 5 || $pam->vendor_id > 0 && strlen($pam->vendors_name) > 5)
            <p  data-log_message="{{  ($pam->vendor_id > 0 ) ? $pam->vendors_name : ( $pam->taskUser ? $pam->taskUser->name : $pam->customer_name  )  }}" class="user-inputt p-0 m-0">{{  ($pam->vendor_id > 0 ) ? substr($pam->vendors_name,0,6) : ( $pam->taskUser ? substr($pam->taskUser->name,0,4) : substr($pam->customer_name,0,4)  )  }}..</p>
            @else

                @if(empty($pam->vendor_id) && empty($pam->customer_id) && empty($pam->supplier_id) && empty($pam->user_id) && empty($pam->task_id) && empty($pam->developer_task_id) && empty($pam->bug_id))
                    <p class="p-0 m-0">{{ $pam->from_name }}</p>
                @else
                    <p class="p-0 m-0">{{  ($pam->vendor_id > 0 ) ? $pam->vendors_name  : ( $pam->taskUser ? $pam->taskUser->name : $pam->customer_name  )  }}</p>
                @endif

            @endif

           </td>
            @endif
                @if (strlen($pam->website_title) > 5)
                    <td  data-log_message="{{ $pam->website_title }}" class="log-website-popup user-iput">
                        <p class="p-0 m-0">{{ substr($pam->website_title,0,5) }}...</p>
                    </td>
                @else
                    <td>{{ $pam->website_title }}</td>
                @endif
        <!-- DEVTASK-23479 display message type -->
        <td>
            @if($pam->message_type!='')
                {{ucfirst($pam->message_type)}}
            @elseif ($pam->is_email>0)
                {{'Email'}}
            @elseif ($pam->task_id>0)
                {{'Task'}}
            @elseif ($pam->developer_task_id>0)
                {{'Dev Task'}}
            @elseif ($pam->ticket_id>0)
                {{'Ticket'}}
            @elseif ($pam->user_id > 0)
                {{'User'}}
            @elseif ($pam->supplier_id > 0)
                {{'Supplier'}}
            @elseif ($pam->customer_id > 0)
                {{'Customer'}}
            @endif
        </td>
        <!-- DEVTASK-23479 display message type -->
        <!-- Purpose : Add question - DEVTASK-4203 -->
        @if (strlen($pam->question) > 10)
            <td   class="log-message-popup user-input" data-log_message="{!!$pam->question!!}">{{ substr($pam->question,0,15) }}...
                @if($pam->chat_read_id == 1)
                    <a href="javascript:;" class="read-message" data-value="0" data-id="{{ $pam->chat_bot_id }}">
                        <i class="fa fa-check-square-o text-dark"></i>

                    </a>
                @else
                    <a href="javascript:;" class="read-message" data-value="1" data-id="{{ $pam->chat_bot_id }}">
                        <i class="fa fa-check-square-o text-secondary"></i>

                    </a>
                @endif
            </td>

        @elseif(empty($pam->vendor_id) && empty($pam->customer_id) && empty($pam->supplier_id) && empty($pam->user_id) && empty($pam->task_id) && empty($pam->developer_task_id) && empty($pam->bug_id))
            <td class="user-input" >{{ $pam->message }}</td>
        @else
            {{-- <td class="user-input" >{{ $pam->question }}
                @if($pam->chat_read_id == 1)
                    <a href="javascript:;" class="read-message" data-value="0" data-id="{{ $pam->chat_bot_id }}">
                        <i class="fa fa-check-square-o text-dark"></i>

                    </a>
                @else
                    <a href="javascript:;" class="read-message" data-value="1" data-id="{{ $pam->chat_bot_id }}">
                        <i class="fa fa-check-square-o text-secondary"></i>

                    </a>
                @endif
            </td> --}}

            @if (strlen($pam->question) > 10)
            <td   class="log-message-popup user-input" data-log_message="{!!$pam->question!!}">{{ substr($pam->question,0,15) }}...
                @if($pam->chat_read_id == 1)
                    <a href="javascript:;" class="read-message" data-value="0" data-id="{{ $pam->chat_bot_id }}">
                        <i class="fa fa-check-square-o text-dark"></i>

                    </a>
                @else
                    <a href="javascript:;" class="read-message" data-value="1" data-id="{{ $pam->chat_bot_id }}">
                        <i class="fa fa-check-square-o text-secondary"></i>

                    </a>
                @endif
            </td>
        @else
            <td class="user-input" >{{ $pam->question }}
                @if($pam->chat_read_id == 1)
                    <a href="javascript:;" class="read-message" data-value="0" data-id="{{ $pam->chat_bot_id }}">
                        <i class="fa fa-check-square-o text-dark"></i>

                    </a>
                @else
                    <a href="javascript:;" class="read-message" data-value="1" data-id="{{ $pam->chat_bot_id }}">
                        <i class="fa fa-check-square-o text-secondary"></i>

                    </a>
                @endif
            </td>
        @endif
        @endif
{{--            {{ $pam->question }}--}}


        @if (strlen($pam->answer) > 10)
            <td  data-log_message="{{ $pam->answer }}" class="bot-reply-popup boat-replied pr-0">{{ substr( $pam->answer ,0,15) }}...
            </td>
        @else
            <td class="boat-replied">{{ $pam->answer }}
            </td>
        @endif



                    @if (strlen($pam->suggested_replay) > 10)
                        <td data-log_message="{{ $pam->suggested_replay }}"
                            class="bot-suggested-reply-popup boat-replied">{{ substr( $pam->suggested_replay ,0,15) }}...

                        @if($pam->is_approved == false && $pam->is_reject == false)
                                <div class="suggested_replay_action d-inline">
                                <a href="javascript:;" class="send_suggested_replay" data-value="0"
                                   data-id="{{ $pam->tmp_replies_id }}">
                                    <i class="fa fa-window-close-o text-secondary px-1 py-2" aria-hidden="true"></i>
                                </a>
                                <a href="javascript:;" class="send_suggested_replay" data-value="1"
                                   data-id="{{ $pam->tmp_replies_id }}">
                                    <i class="fa fa-check-square-o text-secondary px-1 py-2"></i>
                                </a>
                                </div>
                        @endif
                        </td>
                    @else
                        <td class="boat-replied">{{ $pam->suggested_replay }}
                            @if($pam->suggested_replay && $pam->is_approved == false && $pam->is_reject == false)
                                <a href="javascript:;" class="read-message" data-value="0"
                                   data-id="{{ $pam->chat_bot_id }}">
                                    <i class="fa fa-window-close-o text-secondary p-2" aria-hidden="true"></i>
                                </a>
                                <a href="javascript:;" class="read-message" data-value="0"
                                   data-id="{{ $pam->chat_bot_id }}">
                                    <i class="fa fa-check-square-o text-secondary p-2"></i>
                                </a>
                            @endif
                        </td>
                    @endif

        <td class="message-input p-0 pt-2 pl-3">
            <div class=" cls_textarea_subbox">
                <div class="btn-toolbar" role="toolbar">
                    <div class="w-75">
                        <textarea rows="1" class="form-control quick-message-field cls_quick_message addToAutoComplete" data-id="{{ $pam->id }}" data-customer-id="{{ $pam->customer_id }}" name="message" id="message_{{$pam->id}}" placeholder="Message"></textarea>
                    </div>
                    <div class="w-25 pl-2" role="group" aria-label="First group">
                        <button type="button" class="btn btn-sm m-0 p-0 mr-1">
                            <input name="add_to_autocomplete" class="add_to_autocomplete" type="checkbox" value="true">
                        </button>
                        <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image send-message1" id="send-message_{{ $pam->id }}" data-id="{{ $pam->id }}"  data-customer-id="{{ !empty($pam->customer_id) ? $pam->customer_id : '' }}" data-email-id={{ !empty($pam->email_id) ? $pam->email_id : ''}}>
                            <img src="/images/filled-sent.png">
                        </button>
                        @if($pam->task_id > 0 )
                        <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="task" data-id="{{$pam->task_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                        @elseif($pam->developer_task_id > 0 )
                        <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="developer_task" data-id="{{$pam->developer_task_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                        @elseif($pam->vendor_id > 0 )
                        <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="vendor" data-id="{{$pam->vendor_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>

                        @elseif(empty($pam->vendor_id) && empty($pam->customer_id) && empty($pam->supplier_id) && empty($pam->user_id) && empty($pam->task_id) && empty($pam->developer_task_id) && empty($pam->bug_id))

                        <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="email" data-id="{{$pam->email_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>

                        @else
                        <button   type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="customer" data-id="{{$pam->customer_id }}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                        <button   type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image load-communication-modal" data-object="customer" data-id="{{$pam->customer_id }}" data-attached="1" data-limit="10" data-load-type="images" data-all="1" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" title="Load Auto Images attacheds"><img src="/images/archive.png" alt=""></button>
                        @endif
                        @if($pam->is_email==1 )
                        <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image editmessagebcc"  data-to_email="{{$pam->to_email}}" data-from_email="{{$pam->from_email}}" data-id="{{$pam->id}}" data-cc_email="{{$pam->cc_email}}" data-all="1" title=""><i class="fa fa-edit"></i></button>
                        @endif
                        <div id="fa_microphone_slash_{{$pam->id}}" style="display: none" ><i class="fa fa-microphone-slash" aria-hidden="true"></i></div>
                        <button type="button" style="font-size: 16px" data-id="{{$pam->id}}" class="btn btn-sm m-0 p-0 mr-1 speech-button"  id="speech-button_{{$pam->id}}"><i class="fa fa-microphone" aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>
        </td>
        <td class="boat-replied">{{ $pam->reply_from }}</td>
        <td class="communication p-0 pt-2 pl-3">
          <div class="row m-0">
              <div class="col-6 d-inline form-inline p-0">
                  <div style="float:left;width: calc(100% - 5px)">
                      <select name="quickCategory" class="form-control mb-2 quickCategory select-quick-category">
                            <option value="">Select Category</option>
                            @foreach($reply_categories as $category)
                                @if(!empty($pam->vendor_id) && $category->default_for=='vendors')
                                    <option value="{{ $category->approval_leads }}" selected data-id="{{$category->id}}">{{ $category->name }}</option>
                                @elseif (!empty($pam->customer_id) && $category->default_for=='customers')
                                    <option value="{{ $category->approval_leads }}" selected data-id="{{$category->id}}">{{ $category->name }}</option>
                                @elseif (!empty($pam->user_id) && $category->default_for=='users')
                                    <option value="{{ $category->approval_leads }}" selected data-id="{{$category->id}}">{{ $category->name }}</option>
                                @else
                                    <option value="{{ $category->approval_leads }}" data-id="{{$category->id}}">{{ $category->name }}</option>
                                @endif
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
                <a href="javascript:;" style="display: inline-block" class="resend-to-bot btns"
                   data-id="{{ $pam->id }}">
                    <i style="color: #757575c7;" class="fa fa-refresh" title="Resend to bot" aria-hidden="true"></i>

                </a>
                <a href="javascript:;" style="display: inline-block" class="approve_message  btns  pt-2"
                   data-id="{{ $pam->chat_id }}">
                    <i style="color: #757575c7;" class="fa fa-plus" aria-hidden="true"></i>
                </a>
                @if($pam->task_id > 0 )
                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image" onclick="changeSimulatorSetting('task', {{ $pam->task_id }}, {{ $pam->is_auto_simulator == 0 }})">
                        <i style="color: #757575c7;" class="fa fa-{{$pam->is_auto_simulator == 0 ? 'play' : 'pause'}}" aria-hidden="true"></i>
                    </button>
                @elseif($pam->developer_task_id > 0 )
                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image" onclick="changeSimulatorSetting('developer_task', {{ $pam->developer_task_id }}, {{ $pam->is_auto_simulator == 0 }})">
                        <i style="color: #757575c7;" class="fa fa-{{$pam->is_auto_simulator == 0 ? 'play' : 'pause'}}" aria-hidden="true"></i>
                    </button>
                @elseif($pam->vendor_id > 0 )
                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image" onclick="changeSimulatorSetting('vendor', {{ $pam->vendor_id }}, {{ $pam->is_auto_simulator == 0 }})">
                        <i style="color: #757575c7;" class="fa fa-{{$pam->is_auto_simulator == 0 ? 'play' : 'pause'}}" aria-hidden="true"></i>
                    </button>
                @elseif(empty($pam->vendor_id) && empty($pam->customer_id) && empty($pam->supplier_id) && empty($pam->user_id) && empty($pam->task_id) && empty($pam->developer_task_id) && empty($pam->bug_id))
                @else
                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image" onclick="changeSimulatorSetting('customer', {{ $pam->customer_id }}, {{ $pam->is_auto_simulator == 0 }})">
                        <i style="color: #757575c7;" class="fa fa-{{$pam->is_auto_simulator == 0 ? 'play' : 'pause'}}" aria-hidden="true"></i>
                    </button>
                @endif
                @if($pam->task_id > 0 )
                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image show_message_list" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="task" data-id="{{$pam->task_id}}" data-load-type="text" data-all="1" title="Load messages"><i style="color: #757575c7;" class="fa fa-file-text-o" aria-hidden="true"></i></button>
                @elseif($pam->developer_task_id > 0 )
                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image show_message_list" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="developer_task" data-id="{{$pam->developer_task_id}}" data-load-type="text" data-all="1" title="Load messages"><i style="color: #757575c7;" class="fa fa-file-text-o" aria-hidden="true"></i></button>
                @elseif($pam->vendor_id > 0 )
                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image show_message_list" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="vendor" data-id="{{$pam->vendor_id}}" data-load-type="text" data-all="1" title="Load messages"><i style="color: #757575c7;" class="fa fa-file-text-o" aria-hidden="true"></i></button>

                @elseif(empty($pam->vendor_id) && empty($pam->customer_id) && empty($pam->supplier_id) && empty($pam->user_id) && empty($pam->task_id) && empty($pam->developer_task_id) && empty($pam->bug_id))

                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image show_message_list" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="email" data-id="{{$pam->email_id}}" data-load-type="text" data-all="1" title="Load messages"><i style="color: #757575c7;" class="fa fa-file-text-o" aria-hidden="true"></i></button>

                @else
{{--                    <button   type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image show_message_list" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="customer" data-id="{{$pam->customer_id }}" data-load-type="text" data-all="1" title="Load messages"><i style="color: #757575c7;" class="fa fa-file-text-o" aria-hidden="true"></i></button>--}}
                    <button   type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image show_message_list" data-object="customer" data-id="{{$pam->customer_id }}" data-attached="1" data-limit="10" data-load-type="images" data-all="1" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" title="Load Auto Images attacheds"><i style="color: #757575c7;" class="fa fa-file-text-o" aria-hidden="true"></i></button>
                @endif
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


<div id="botSuggestedReply" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Bot Suggested Replied</h4>
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

<div id="editmessagebcc" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Email/Message</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            <form method="post" action="<?php echo route("chatbot.question.save"); ?>">
                <?php echo csrf_field(); ?>

                <div class="modal-body">

                    <input type="hidden" name="chat_id"  id="chat_id">
                    <div class="form-group">
                            <label for="value">To</label>
                            <input type="email" name="to_email" id="to_email" class="form-control"  placeholder="Enter To Email" required>
                        </div>
                        <div class="form-group">
                            <label for="value">From</label>
                            <input type="email" name="from_email"  id="from_email" class="form-control"  placeholder="Enter from email" required>
                        </div>
                        <div class="form-group">
                            <label for="value">Cc</label>
                            <input type="email" name="cc_email"  id="cc_email" class="form-control"  placeholder="Enter cc">
                        </div>
                        <div class="form-group">
                            <label for="value">Message</label>
                            <input type="email" name="message1"  id="message1" class="form-control"  placeholder="Enter cc">
                        </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary form-edit-email-btn">Save changes</button>
                </div>
            </form>
            </div>

        </div>

    </div>
</div>

<div id="chat_bot_reply_list" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Simulator Replay</h4>
                <input type="hidden" id="chat_obj_type" name="chat_obj_type">
                <input type="hidden" id="chat_obj_id" name="chat_obj_id">
                <button type="submit" class="btn btn-default downloadChatMessages">Download</button>
            </div>
            <div class="modal-body" style="background-color: #999999;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).on('click','.speech-button',function(e){
        const speechInput = document.querySelector('#message_'+ $(this).attr("data-id"));
        var recognition = new webkitSpeechRecognition()
        recognition.interimResults = true;

        /* start voice */
        recognition.start();
        if(speechInput){
            var microphoneSlash = document.getElementById('fa_microphone_slash_'+$(this).attr("data-id"));
            var microphone = document.getElementById('speech-button_'+$(this).attr("data-id"));
            microphone.style.display = "none";
            microphoneSlash.style.display = "block";
        }

        /* convert voice to text*/
        recognition.addEventListener('result', (event) => {
            speechInput.value = event.results[0][0].transcript;
        });

        /* stop voice */
        recognition.addEventListener('end', () => {
            recognition.stop();
            microphone.style.display = "block";
            microphoneSlash.style.display = "none";
        });
    })

    $(document).on('click','.log-message-popup',function(){
        $('#logMessageModel p').text($(this).data('log_message'));
        $('#logMessageModel').modal('show');

    })

    $(document).on('click','.editmessagebcc',function(){
        $('#chat_id').val($(this).data('id'));
        $('#from_email').val($(this).data('from_email'));
        $('#to_email').val($(this).data('to_email'));
        $('#cc_email').val($(this).data('cc_email'));

        var message = $(this).closest(".cls_textarea_subbox").find("textarea").val();
        $('#message1').val(message);
        $('#editmessagebcc').modal('show');

    })

    $(document).on("click",".form-edit-email-btn",function () {
        let chatID =  $('#chat_id').val();
        let fromemail=$('#from_email').val();
        let toemail=$('#to_email').val();
        let ccemail=$('#cc_email').val();
        $('#message_'+chatID).val($('#message1').val());
         $.ajax({
            type: "GET",
            url: "{{url('/chatbot/messages/update-emailaddress')}}",
            data: {
                chat_id : chatID,
                fromemail:fromemail,
                toemail:toemail,
                ccemail:ccemail

            },
            success: function (response) {
                if(response.code == 200) {
                    toastr['success'](response.messages);
                    $('#send-message_'+chatID).trigger('click');
                    $('#editmessagebcc').modal('hide');
                }else{
                    toastr['error'](response.messages);
                }
            },
            error: function () {
                toastr['error']('Record not Update successfully!');
            }
        });
    });



    $(document).on('click','.bot-reply-popup',function(){
        $('#botReply').modal('show');
        $('#botReply p').text($(this).data('log_message'));
    })

    $(document).on('click','.bot-suggested-reply-popup',function(){
        $('#botSuggestedReply').modal('show');
        $('#botSuggestedReply p').text($(this).data('log_message'));
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

    $(document).on("click",".send_suggested_replay",function () {
        let tmpReplayId = $(this).data("id");
        let value = $(this).data("value");
        var $this = $(this);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: "{{ route('chatbot.send.suggested.message') }}",
            data: {
                tmp_reply_id : tmpReplayId,
                value  : value
            },
            dataType : "json",
            success: function (response) {
                if(response.code == 200) {
                    toastr['success'](response.messages);
                    if(value == 1) {
                        $this.remove();
                    }else{
                        $this.remove();
                    }
                }else{
                    toastr['error'](response.messages);
                }
            },
            error: function () {
                toastr['error']('Suggested replay not found');
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

    function changeSimulatorSetting(object, objectId, simulator) {
        $.ajax({
            type: "POST",
            url: "/chatbot/messages/update-simulator-setting",
            data: {
                '_token': "{{ csrf_token() }}",
                'object' : object,
                'objectId' : objectId,
                'auto_simulator': simulator
            },
            dataType : "json"
        }).done(function(response) {
            if(response.code == 200) {
                toastr['success'](response.messages);
            }else{
                toastr['error'](response.messages);
            }
        }).fail(function(response) {
            toastr['error']('Could not update simulator status');
        });
    }

    $(document).on("click", ".show_message_list", function () {
        $('#chat_bot_reply_list').modal('show');
        var feedback_category_id = null;
        var feedback_status_id = null;

        if ($(this).data("feedback_cat_id")) {
            var feedback_category_id = $(this).data("feedback_cat_id");
        }

        var thiss = $(this);
        var object_type = $(this).data("object");
        var object_id = $(this).data("id");
        var load_attached = $(this).data("attached");
        var load_all = $(this).data("all");
        load_type = $(this).data("load-type");
        is_admin = $(this).data("is_admin");
        var is_hod_crm = $(this).data("is_hod_crm");
        var limit = 20;
        if (typeof $(this).data("limit") != "undefined") {
            limit = $(this).data("limit");
        }

        var base_url = BASE_URL;
        // var base_url ="http://localhost:8000";
        thiss.parent().find(".td-full-container").toggleClass("hidden");
        currentChatParams.url =
            base_url +
            "/message-list/" +
            object_type +
            "/" +
            object_id;
        currentChatParams.data = {
            limit: limit,
            load_all: load_all,
            load_attached: load_attached,
            load_type: load_type,
            page: 1,
            hasMore: true,
            object_name: object_type,
            object_val: object_id,
        };

        $.ajax({
            type: "GET",
            url: "{{ route('chatbot.message.list') }}" + "/"  + object_type + "/" + object_id,
            data: {
                limit: limit,
                load_all: load_all,
                load_attached: load_attached,
                load_type: load_type,
                feedback_category_id: feedback_category_id,
            },
            beforeSend: function () {
                $(thiss).text("Loading...");
                $(thiss).html("");
                $(thiss).html(
                    '<i style="color: #757575c7;" class="fa fa-file-text-o" aria-hidden="true"></i><div class="spinner-border" role="status"><span class="">Loading...</span></div>'
                );
            },
        })
            .done(function (response) {
                $(".spinner-border").css("display", "none");
                var li = getHtml(response, 'simulator-message-list');
                if ($("#chat_bot_reply_list").length > 0) {
                    $("#chat_bot_reply_list")
                        .find(".modal-dialog")
                        .css({ width: "1000px", "max-width": "1000px" });
                    $("#chat_bot_reply_list")
                        .find(".modal-body")
                        .css({ "background-color": "white" });
                    $("#chat_bot_reply_list").find(".modal-body").html(li);
                    $("#chat_bot_reply_list").find("#chat_obj_type").val(object_type);
                    $("#chat_bot_reply_list").find("#chat_obj_id").val(object_id);
                    $("#chat_bot_reply_list")
                        .find(".message")
                        .css({ "white-space": "pre-wrap", "word-wrap": "break-word" });
                    $("#chat_bot_reply_list").modal("show");
                } else {
                    $("#chat_bot_reply_list")
                        .find(".modal-dialog")
                        .css({ width: "1000px", "max-width": "1000px" });
                    $("#chat_bot_reply_list")
                        .find(".modal-body")
                        .css({ "background-color": "white" });
                    $("#chat_bot_reply_list").find("#chat_obj_type").val(object_type);
                    $("#chat_bot_reply_list").find("#chat_obj_id").val(object_id);
                    $("#chat-history").html(li);
                }

                var searchterm = $(".search_chat_pop").val();
                if (searchterm && searchterm != "") {
                    var value = searchterm.toLowerCase();
                    $(".filter-message").each(function () {
                        if ($(this).text().search(new RegExp(value, "i")) < 0) {
                            $(this).hide();
                        } else {
                            $(this).show();
                        }
                    });
                }
            })
            .fail(function (response) {
                //$(thiss).text('Load More');
                $(".spinner-border").css("display", "none");
                alert("Could not load messages");
            });
    });

</script>
