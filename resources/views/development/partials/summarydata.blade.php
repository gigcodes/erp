<tr style="color:grey;">
    <td style="display:table-cell;vertical-align: baseline;">

        <div class="d-flex align-items-center">
            <a style="color: #555;" href="{{ url("development/task-detail/$issue->id") }}">
                @if($issue->is_resolved==0)
                    <input type="checkbox" name="selected_issue[]" value="{{$issue->id}}" {{in_array($issue->id, $priority) ? 'checked' : ''}}>
                @endif
            </a>
            <span class="ml-2"> {{ $issue->id }}</span>
        </div>


        <div>
            <a href="javascript:;" data-id="{{ $issue->id }}" class="upload-document-btn"><img width="15px" src="{{asset('/images/attach.png')}}" alt="" style="cursor: default;"></a>
            <a href="javascript:;" data-id="{{ $issue->id }}" class="list-document-btn"><img width="15px" src="{{asset('/images/archive.png')}}" alt="" style="cursor: default;"></a>
        </div>
    </td>
    <td>
        <select name="module" class="form-control task-module" data-id="{{$issue->id}}">
            <option value=''>Select Module..</option>
            @foreach($modules as $module)

                @if( isset($issue->module_id) && (int) $issue->module_id == $module->id )
                    <option value="{{$module->id}}" selected>{{$module->name}}</option>
                @else
                    <option value="{{$module->id}}">{{$module->name}}</option>
                @endif
            @endforeach
        </select>
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
        <select class="form-control assign-master-user select2" data-id="{{$issue->id}}" name="master_user_id" id="user_{{$issue->id}}">
            <option value="">Select...</option>
            <?php $masterUser = isset($issue->masterUser->id) ? $issue->masterUser->id : 0; ?>
            @foreach($users as $id=>$name)
                @if( $masterUser == $id )
                    <option value="{{$id}}" selected>{{ $name }}</option>
                @else
                    <option value="{{$id}}">{{ $name }}</option>
                @endif
            @endforeach
        </select>
    </td>
    <td class="communication-td">
        <!-- class="expand-row" -->
        <input type="text" class="form-control send-message-textbox" data-id="{{$issue->id}}" id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}" style="margin-bottom:5px;width:75%;display:inline;"/>

        <button style="display: inline-block;" class="btn btn-sm btn-image send-message-open" type="submit" id="submit_message" data-id="{{$issue->id}}"><img src="{{asset('/images/filled-sent.png')}}"/></button>
        <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='developer_task' data-id="{{ $issue->id }}" style="mmargin-top: -0%;margin-left: -2%;" title="Load messages"><img src="{{asset('/images/chat.png')}}" alt=""></button>
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
    <td class="send-to-str">
        <?php echo Form::select(
            "send_message_" . $issue->id, [
            "to_developer" => "Send To Developer",
            "to_master" => "Send To Master Developer",
            "to_team_lead" => "Send To Team Lead",
            "to_tester" => "Send To Tester"
        ], null, [
                "class" => "form-control send-message-number",
                "style" => "width:100% !important;display: inline;"
            ]
        ); ?>

    </td>


    <td>
        @if($issue->is_resolved)
            <strong>Done</strong>
        @else
            <?php echo Form::select(
                "task_status", $statusList, $issue->status, [
                                 "class" => "form-control resolve-issue",
                                 "onchange" => "resolveIssue(this," . $issue->id . ")"
                             ]
            ); ?>
        @endif
    </td>
    <td>
        <button class="btn btn-image set-remark" data-task_id="{{ $issue->id }}" data-task_type="Quick-dev-task"><i class="fa fa-comment" aria-hidden="true"></i></button>
        @if ($issue->is_flagged == 1)
            <button type="button" class="btn btn-image flag-task" data-id="{{ $issue->id }}"><img src="{{asset('images/flagged.png')}}"/></button>
        @else
            <button type="button" class="btn btn-image flag-task" data-id="{{ $issue->id }}"><img src="{{asset('images/unflagged.png')}}"/></button>
        @endif
        <button type="button" class="btn btn-xs show-status-history" title="Show Status History" data-id="{{$issue->id}}">
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
                task_type: 'DEVTASK'
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
</script>
