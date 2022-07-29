@extends('layouts.app')

@section('favicon' , 'task.png')

@section('title', 'Users Feedback')

@section('content')
    <style type="text/css">
        .feedback_model .modal-dialog{
           max-width:1024px;
           width:100%;
        }
        .quick_feedback, #feedback-status{
            border: 1px solid #ddd;
            border-radius: 4px;
            height: 35px;
            outline: none;
        }
        .quick_feedback:focus, #feedback-status:focus{
            outline: none;
        }
        .communication-td input{
            width: calc(100% - 25px) !important;
        }
        .communication-td button{
            width:20px;
        }

    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <div class="row" id="common-page-layout">
        <input type="hidden" name="page_no" class="page_no" />
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Users Feedback</h2>
            <div class="row">
                <div class="col">
                    <div class="" style="margin-bottom:10px;">
                        <div class="row">
                            <form class="form-inline message-search-handler" method="get">
                                <div class="col">
                                    <div class="form-group">
                                        <select name="user_id" class="form-control  select-multiple">
                                            <option>-select-</option>
                                            <?php foreach ($users as $key => $user) {
                                                    $selectedUser = '';
                                                    if($user->id == $request->user_id)
                                                        $selectedUser = 'selected="selected"';
                                                    echo '<option value="'.$user->id.'" '.$selectedUser.'>'.$user->name.'</option>';
                                            }?>
                                        </select>
                                    </div>
                                    {{-- <div class="form-group">
                                        <select name="is_active" class="form-control" placholder="Active:">
                                            <option value="0" {{ request('is_active') == 0 ? 'selected' : '' }}>All</option>
                                            <option value="1" {{ request('is_active') == 1 ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="2" {{ request('is_active') == 2 ? 'selected' : '' }}>In active
                                            </option>
                                        </select>
                                    </div> --}}
                                    <div class="form-group pl-3">
                                        <label for="button">&nbsp;</label>
                                        <button style="display: inline-block;width: 10%;margin-top: -16px;"
                                            class="btn btn-sm btn-image btn-search-action">
                                            <img src="/images/search.png" style="cursor: default;">
                                        </button>
                                    </div>
                                </div>
                            </form>
                        
                    </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 margin-tb" id="page-view-result">

            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered" style="margin-top: 25px">
            <tr>
                <th width="17%">Category</th>
                <th width="15%">SOP</th>
                <th width="15%">Admin Response</th>
                <th width="15%">User Response</th>
                <th width="15%">Status</th>
                <th width="10%">History</th>
            </tr>
            @if (Auth::user()->isAdmin())
            <tr>
                <td>
                    <input type="text" style="width:calc(100% - 41px)" class="quick_feedback" id="addcategory" name="category">
                    <button style="width: 20px" type="button" class="btn btn-image add-feedback" id="btn-save"><img src="/images/add.png" style="cursor: nwse-resize; width: 0px;"></button>
                </td>
                <td></td>
                <td></td>
                <td><input type="textbox" style="width:calc(100% - 41px)" id="feedback-status">
                    <button style="width: 20px" type="button" class="btn btn-image user-feedback-status"><img src="/images/add.png" style="cursor: nwse-resize; width: 0px;"></button></td>
                <td></td>
            </tr>
            @endif
            <?php $sopOps = ''; ?>
            @foreach ($sops as $sop)
                <?php $sopOps .= '<option value="'.$sop->id.'">'.$sop->name.'</option>' ?>
            @endforeach
            @foreach ($category as $cat)
                @php
                    if($user_id !=''){
                        $cat->user_id = $user_id;
                    }
                    $latest_messages = App\ChatMessage::select('message')->where('user_feedback_id', $cat->user_id)->where('user_feedback_category_id', $cat->id)->orderBy('id','DESC')->first();
                    if ($latest_messages) {
                        $latest_msg = $latest_messages->message;
                        if (strlen($latest_msg) > 20) {
                            $latest_msg = substr($latest_messages->message,0,20).'...';
                        }
                    }
                    $feedback_status = App\UserFeedbackStatusUpdate::select('user_feedback_status_id')->where('user_id', $cat->user_id)->where('user_feedback_category_id', $cat->id)->first();
                    $status_id = 0;
                    if ($feedback_status) {
                        $status_id = $feedback_status->user_feedback_status_id;
                    }
                    $latest_comment = App\UserFeedbackCategorySopHistoryComment::select('comment', 'id')->where('sop_history_id', $cat->sop_id)->orderBy('id','DESC')->first();
                    $comment = '';
                    if(isset($latest_comment->comment))
                        $comment = $latest_comment->comment.'...';
                    $commentId = '';
                    if(isset($latest_comment->comment))
                        $commentId = $latest_comment->id;
                @endphp
                <tr data-cat_id="{{ $cat->id }}" data-user_id="{{ $cat->user_id }}">
                    <td>{{ $cat->category }}</td>
                    <td class="communication-td">
                        @if(\Auth::user()->isAdmin() == true)
                            <input type="text" class="form-control" data-id="{{$cat->id}}" id="sop_{{$cat->id}}" name="sop_{{$cat->id}}" placeholder="Enter SOP name...."  style="margin-bottom:5px;width:77%;display:inline;"/>
                            <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image user-sop-save" data-sop="sop_{{$cat->id}}" data-feedback_cat_id="{{$cat->id}}" type="submit" id="submit_message"  data-id="{{$cat->id}}" ><img src="/images/filled-sent.png"/></button>
                            <div class="sop-text-{{$cat->id}}">
                                <div style='width:50%;'>{{$cat->sop}}...</div> <img class='sop-history' src='/images/chat.png' data-cat_id="{{$cat->id}}" data-sop_id="{{$cat->sop_id}}" alt='history' style='width:17px;cursor: nwse-resize;'>
                            </div>
                        @else
                            <div id="comment_div_{{$cat->id}}">
                                <input type="radio" name="accept_reject_{{$cat->id}}" id="accept_reject_{{$cat->id}}" value="Yes" style="width: 12px !important;height: 12px !important;"> Yes &nbsp;
                                <input type="radio" name="accept_reject_{{$cat->id}}" id="accept_rejectN_{{$cat->id}}" value="No" style="width: 12px !important;height: 12px !important;"> No
                                <input type="text" class="form-control " data-id="{{$cat->id}}" id="comment_{{$cat->id}}" name="comment_{{$cat->id}}" placeholder="Enter comment ...."  style="margin-bottom:5px;width:77%;display:inline;"/>
                                <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image user-sop-comment-save" data-sop_id="{{$cat->sop_id}}" data-id="{{$cat->id}}" data-comment="comment_{{$cat->id}}" data-feedback_cat_id="{{$cat->id}}"  type="submit" id="submit_message" ><img src="/images/filled-sent.png"/></button>
                                <div class="sop-comment-text-{{$cat->id}}">
                                    <div style='width:50%;'>{{$comment}}</div> <img class='sop-comment-history' src='/images/chat.png' data-id="{{$cat->id}}" data-sop_history_id="{{$cat->sop_id}}" data-sop_comment_id="{{$commentId}}" alt='history' style='width:17px;cursor: nwse-resize;'>
                                </div>
                            </div>
                        @endif                        
                        
                    </td>
                    <td class="communication-td">
                        {{-- <input type="text" class="form-control send-message-textbox" data-id="{{$cat->user_id}}" id="send_message_{{$cat->user_id}}" name="send_message_{{$cat->user_id}}" placeholder="Enter Message...." style="margin-bottom:5px;width:77%;display:inline;" @if (!Auth::user()->isAdmin()) {{ "readonly" }} @endif/> --}}
                        <select class="form-control send-message-textbox" data-id="{{$cat->user_id}}" id="send_message_{{$cat->user_id}}" name="send_message_{{$cat->user_id}}" style="margin-bottom:5px;width:77%;display:inline;" @if (!Auth::user()->isAdmin()) {{ "readonly" }} @endif>
                            <?php echo $sopOps; ?></?php>
                        </select>
                        <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image send-message-open" data-feedback_cat_id="{{$cat->id}}" type="submit" id="submit_message"  data-id="{{$cat->user_id}}" ><img src="/images/filled-sent.png"/></button>
                        @if ($latest_messages && $latest_messages->user_feedback_category_id == $cat->id)
                            <span class="latest_message">@if ($latest_messages->send_by) {{ $latest_msg }} @endif</span>
                        @else
                            <span class="latest_message"></span>
                        @endif
                    </td>
                    <td class="communication-td">
                        <input type="text" class="form-control send-message-textbox" data-id="{{$cat->user_id}}" id="send_message_{{$cat->user_id}}" name="send_message_{{$cat->user_id}}" placeholder="Enter Message...." style="margin-bottom:5px;width:77%;display:inline;" @if (Auth::user()->isAdmin()) {{ "readonly" }} @endif/>
                        <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image send-message-open" data-feedback_cat_id="{{$cat->id}}" type="submit" id="submit_message"  data-id="{{$cat->user_id}}" ><img src="/images/filled-sent.png"/></button></button>
                        @if ($latest_messages && $latest_messages->user_feedback_category_id == $cat->id)
                            <span class="latest_message">@if (!$latest_messages->send_by) {{ $latest_msg }} @endif</span>
                        @else
                            <span class="latest_message"></span>
                        @endif
                    </td>
                    <td>
                        <select class="form-control user_feedback_status">
                            <option value="">Select</option>
                            @foreach ($status as $st)
                                <option value="{{$st->id}}" @if ($st->id == $status_id) {{ "selected" }} @endif>{{ $st->status }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><button type="button" class="btn btn-xs btn-image load-communication-modal" data-feedback_cat_id="{{$cat->id}}" data-object='user-feedback' data-id="{{$cat->user_id}}" style="mmargin-top: -0%;margin-left: -2%;" title="Load messages"><img src="/images/chat.png" alt=""></button></td>
                </tr>
            @endforeach
    </table>
    {{ $category->links() }}
</div>
<div id="chat-list-history" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Communication</h4>
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
<div id="sop-history" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sop history</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Sop Name</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody class="show-sop-history-records">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div id="sop-comment-history" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sop comment history</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Comment</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody class="show-sop-comment-history-records">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script>
    $('.select-multiple').select2();
    $(document).on("click", "#btn-save",function(e){
            // $('#btn-save').attr("disabled", "disabled");
            e.preventDefault();
             var category = $('#addcategory').val();
             var user_id = $(this).data('user_id');
            if(category!=""){
                console.log(category);
                 $.ajax({
                    url:"{{ route('user.feedback-category') }}",
                    type:"get",
                    data:{
                        category:category,
                        user_id:user_id,
                    },
                    cashe:false,
                    success:function(response){
                        if (response.message) {
                            toastr.error(response.message);
                        }else{
                            $('#addcategory').val('');
                            $(document).find('.user-feedback-data').append(response);
                        }
                    }
                });
            }else{
               alert("error");
            }
         });

         $(document).on('click','.user-feedback-status',function(){
            var status = $('#feedback-status').val();
            $('.user_feedback_status').text('');

            $.ajax({
                type: "get",
                url: '{{ route("user.feedback-status") }}',
                data: {'status':status},
                success:function(response){
                    if (response.status == true) {
                        $('#feedback-status').val('');
                        var all_status = response.feedback_status;
                        var Select = '<option value="">Select</option>'
                        $('.user_feedback_status').append(Select);

                        for (let i = 0; i < all_status.length; i++) {
                            var html = '<option value="' + all_status[i].id+'">'+all_status[i].status+'</option>'; 
                            $('.user_feedback_status').append(html);
                        }
                    }
                }
            });
        });
        $(document).on('click','.sop-history',function(){
            var id = $(this).data('id');
            var catId = $(this).data("cat_id");
            $.ajax({
                type: "get",
                url: '{{ route("user.get.sop.data") }}',
                data: {
                        'cat_id': catId,
                        },
                success:function(response){
                    if (response.code == 200) {
                        toastr["success"](response.message);
                        var t = '';
                        $.each(response.data, function(k, v) {
                            t += `<tr><td>` + v.id + `</td>`;
                            t += `<td>` + v.sop + `</td>`;
                            t += `<td>` + v.created_at + `</td></tr>`;
                        });
                        if (t == '') {
                            t = '<tr><td colspan="4" class="text-center">No data found</td></tr>';
                        }
                        $("#sop-history").find(".show-sop-history-records").html(t);
                        $("#sop-history").modal("show");

                    } else {
                        toastr["error"](response.message);
                    }
                }
            });
        });

        $(document).on('click','.sop-comment-history',function(){
            var id = $(this).data('id');
            var sopId = $(this).data("sop_history_id");
            $.ajax({
                type: "get",
                url: '{{ route("user.get.sop-comment.data") }}',
                data: {
                        'sop_history_id': sopId,
                        },
                success:function(response){
                    if (response.code == 200) {
                        toastr["success"](response.message);
                        
                        var t = '';
                        $.each(response.data, function(k, v) {
                            t += `<tr><td>` + v.id + `</td>`;
                            t += `<td>` + v.comment + `</td>`;
                            t += `<td>` + v.created_at + `</td></tr>`;
                        });
                        if (t == '') {
                            t = '<tr><td colspan="4" class="text-center">No data found</td></tr>';
                        }
                        $("#sop-comment-history").find(".show-sop-comment-history-records").html(t);
                        $("#sop-comment-history").modal("show");

                    } else {
                        toastr["error"](response.message);
                    }
                }
            });
        });

        
        $(document).on('click','.user-sop-save',function(){
            var id = $(this).data('id');
            var sop_id = $(this).data('sop');
            var sop = $("#"+sop_id).val();
            var cat = $(this).data("feedback_cat_id");
            
            $("#send_message_"+$(this).data('id')).val('');

            $.ajax({
                type: "get",
                url: '{{ route("user.save.sop") }}',
                data: {
                        'cat_id': cat,
                        'sop_text': sop
                        },
                success:function(response){
                    if (response.code == 200) {
                        $("#"+sop_id).val('');
                        var resSop = response.data.sop;
                        var resSopId = response.data.id;
                        toastr["success"](response.message);
                        $(".sop-text-"+id).html("<div style='width:50%;'>"+resSop+"...</div> <img class='sop-history' data-cat_id='"+id+"' data-sop_id='"+resSopId+"' src='/images/chat.png' alt='history' style='width:17px;cursor: nwse-resize;'>");
                    } else {
                        toastr["error"](response.message);
                    }
                }
            });
        });
        $(document).on('click','.user-sop-comment-save',function(){
            var id = $(this).data('id');
            var comment = $("#comment_"+id).val();
            var acceptReject = $('input[name="accept_reject_'+id+'"]:checked').val();
            var sopHistoryId = $(this).data("sop_id");
           
            $("#send_message_"+$(this).data('id')).val('');

            $.ajax({
                type: "get",
                url: '{{ route("user.save.sop.comment") }}',
                data: {'sop_history_id': sopHistoryId,
                        'comment': comment,
                        'accept_reject': acceptReject
                        },
                success:function(response){
                    if (response.code == 200) {
                        $('#comment_'+id).val('');
                        var resSopComm = response.data.comment;
                        var resSopCommId = response.data.id;
                        var resSopId = response.data.sop_history_id;
                        toastr["success"](response.message);
                        $(".sop-comment-text-"+id).html("<div style='width:50%;'>"+resSopComm+"...</div> <img class='sop-comment-history' data-id='"+id+"' data-sop_history_id='"+resSopId+"' data-sop_comment_id='"+resSopCommId+"' src='/images/chat.png' alt='history' style='width:17px;cursor: nwse-resize;'>");
                    } else {
                        toastr["error"](response.message);
                    }
                }
            });
        });
        $(document).on('click', '.send-message-open', function (event) {
            var feedback_status_id = $(this).parents('tr').find('.user_feedback_status').val();
            var textBox = $(this).closest(".communication-td").find(".send-message-textbox");
            let user_id = textBox.attr('data-id');
            let message = textBox.val();
            var feedback_cat_id = $(this).data('feedback_cat_id');
            var $this = $(this);
            if (message == '') {
                return;
            }

            let self = textBox;

            $.ajax({
                url: "{{action('WhatsAppController@sendMessage', 'user-feedback')}}",
                type: 'POST',
                data: {
                    "feedback_status_id": feedback_status_id,
                    "feedback_cat_id": feedback_cat_id,
                    "user_id": user_id,
                    "message": message,
                    "_token": "{{csrf_token()}}",
                   "status": 2
                },
                dataType: "json",
                success: function (response) {
                    toastr["success"]("Message sent successfully!", "Message");
                    $('#message_list_' + user_id).append('<li>' + response.message.created_at + " : " + response.message.message + '</li>');
                    $(self).removeAttr('disabled');
                    $(self).val('');
                    var msg = response.message.message;
                    if (msg.length > 20) {
                        var msg = msg.substring(1,20)+'...';
                        $this.siblings('.latest_message').text(msg);
                    }else{
                        $this.siblings('.latest_message').text(msg);
                    }
                },
                beforeSend: function () {
                    $(self).attr('disabled', true);
                },
                error: function () {
                    alert('There was an error sending the message...');
                    $(self).removeAttr('disabled', true);
                }
            });
        });
</script>
@endsection