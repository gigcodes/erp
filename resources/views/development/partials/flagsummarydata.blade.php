

<tr style="color:grey;">
    <td style="display:table-cell;vertical-align: baseline;">
    {{ $issue->id }}
    </td>
    <td>    
       {{ $issue->subject }}
    </td>
       <td>
     <!--   <label for="" style="font-size: 12px;">Assigned To :</label>-->
        <select class="form-control assign-user select2" data-id="{{$issue->id}}" name="assigned_to" id="user_{{$issue->id}}">
            <option value="">Select...</option>
            <?php $assignedId = isset($issue->assignedUser->id) ? $issue->assignedUser->id : 0; ?>
            @foreach($users as $id => $name)
                @if( $assignedId == $id )
                    <option value="{{$id}}" selected>{{ $name }}</option>
                @else
                    <option value="{{$id}}">{{ $name }}</option>
                @endif
            @endforeach
        </select>
   <!--     <label for="" style="font-size: 12px;margin-top:10px;">Lead :</label>-->
    </td>
     <td>
                                        
                                            
                                            <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history" title="Show tracked time History" data-id="{{$issue->id}}" data-type="developer"><i class="fa fa-info-circle"></i></button>
                                    </td>
    <td>
   {{ $issue->estimate_time }}

   <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-status-history" title="Show  History" data-id="{{$issue->id}}" data-type="developer"><i class="fa fa-info-circle"></i></button>
    </td>
    <td>{{ $issue->estimate_date }}
    <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-date-history" title="Show tracked time History" data-id="{{$issue->id}}" data-type="developer"><i class="fa fa-info-circle"></i></button></td>
    <td class="communication-td devtask-com">
    <!-- class="expand-row" -->
  
   
    <input type="text" class="form-control send-message-textbox" data-id="{{$issue->id}}" id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}" style="margin-bottom:5px;width:40%;display:inline;"/>
   
    <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image send-message-open" type="submit" id="submit_message"  data-id="{{$issue->id}}" ><img src="/images/filled-sent.png"/></button>
    <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='developer_task' data-id="{{ $issue->id }}" style="mmargin-top: -0%;margin-left: -2%;" title="Load messages"><img src="/images/chat.png" alt=""></button>
    <span class="{{ ($issue->message && $issue->message_status == 0) || $issue->message_is_reminder == 1 || ($issue->sent_to_user_id == Auth::id() && $issue->message_status == 0) ? '' : '' }} justify-content-between expand-row-msg" style="word-break: break-all;margin-top:6px;" data-id="{{$issue->id}}">
    <span class="td-mini-container-{{$issue->id}}" style="margin:0px;">
                    {{  \Illuminate\Support\Str::limit($issue->message, 25, $end='...') }}
    </span>
</span>
  <div class="expand-row-msg" data-id="{{$issue->id}}">
    <span class="td-full-container-{{$issue->id}} hidden">
        {{ $issue->message }}
        <br>
        <div class="td-full-container">
            <button class="btn btn-secondary btn-xs" onclick="sendImage({{ $issue->id }})">Send Attachment</button>
            <button class="btn btn-secondary btn-xs" onclick="sendUploadImage({{$issue->id}})">Send Images</button>
            <input id="file-input{{ $issue->id }}" type="file" name="files" style="display: none;" multiple/>
         </div> 
    </span>
</div>
    </td>    
    <td >
        @if($issue->is_resolved)
            <strong>Done</strong>
        @else
            <?php echo Form::select("task_status",$statusList,$issue->status,["class" => "form-control resolve-issue","onchange" => "resolveIssue(this,".$issue->id.")"]); ?>
        @endif
         @if ($issue->is_flagged == 1)
         <button type="button" class="btn btn-image flag-task pd-5" data-id="{{ $issue->id }}"><img src="{{asset('images/flagged.png')}}"/></button>
         @else
         <button type="button" class="btn btn-image flag-task pd-5" data-id="{{ $issue->id }}"><img src="{{asset('images/unflagged.png')}}"/></button>
         @endif
        <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-status-history" title="Show Status History" data-id="{{$issue->id}}">
                <i class="fa fa-info-circle"></i>
            </button>
    </td>
 
</tr>

<script>
    $(document).on('click', '.flag-task', function () {
            var task_id = $(this).data('id');
            var thiss = $(this);

            $.ajax({
                type: "POST",
                url: "{{ route('task.flag') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    task_id: task_id,
                    task_type:'DEVTASK'
                },
                beforeSend: function () {
                    $(thiss).text('Flagging...');
                }
            }).done(function (response) {
                if (response.is_flagged == 1) {
                    // var badge = $('<span class="badge badge-secondary">Flagged</span>');
                    //
                    // $(thiss).parent().append(badge);
                    $(thiss).html('<img src="/images/flagged.png" />');
                } else {
                    $(thiss).html('<img src="/images/unflagged.png" />');
                    // $(thiss).parent().find('.badge').remove();
                }

                // $(thiss).remove();
            }).fail(function (response) {
                $(thiss).html('<img src="/images/unflagged.png" />');

                alert('Could not flag task!');

                console.log(response);
            });
        });
     $(document).on('click', '.show-tracked-history', function() {
            var issueId = $(this).data('id');
            var type = $(this).data('type');
            $('#time_tracked_div table tbody').html('');
            $.ajax({
                url: "{{ route('task.time.tracked.history') }}",
                data: {id: issueId,type:type},
                success: function (data) {
                    console.log(data);
                    if(data != 'error') {
                        $.each(data.histories, function(i, item) {
                            var sec = parseInt(item['total_tracked']);
                            $('#time_tracked_div table tbody').append(
                                '<tr>\
                                    <td>'+ moment(item['starts_at_date']).format('DD-MM-YYYY') +'</td>\
                                    <td>'+ ((item['name'] != null) ? item['name'] : '') +'</td>\
                                    <td>'+humanizeDuration(sec,'s')+'</td>\
                                </tr>'
                            );
                        });
                    }
                }
            });
            $('#time_tracked_modal').modal('show');
        });
     $(document).on('click', '.show-status-history', function() {
            var data = $(this).data('history');
            var issueId = $(this).data('id');
            $('#status_quick_history_modal table tbody').html('');
            $.ajax({
                url: "{{ route('development/status/history') }}",
                data: {
                    id: issueId
                },
                success: function(data) {
                    if (data != 'error') {
                        $.each(data, function(i, item) {
                            if (item['is_approved'] == 1) {
                                var checked = 'checked';
                            } else {
                                var checked = '';
                            }
                            $('#status_quick_history_modal table tbody').append(
                                '<tr>\
                                            <td>' + moment(item['created_at']).format('DD/MM/YYYY') + '</td>\
                                            <td>' + ((item['old_value'] != null) ? item['old_value'] : '-') + '</td>\
                                            <td>' + item['new_value'] + '</td>\
                                            <td>' + item['name'] + '</td>\
                                        </tr>'
                            );
                        });
                    }
                }
            });
            $('#status_quick_history_modal').modal('show');
        });

</script>
