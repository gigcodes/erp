<tr>
    <td>
        <a href="{{ url("development/task-detail/$issue->id") }}">{{ $issue->id }}
            @if($issue->is_resolved==0)
                <input type="checkbox" name="selected_issue[]" value="{{$issue->id}}" {{in_array($issue->id, $priority) ? 'checked' : ''}}>
            @endif
        </a>
        <a href="javascript:;" data-id="{{ $issue->id }}" class="upload-document-btn"><img width="15px" src="/images/attach.png" alt="" style="cursor: default;"><a>
        <a href="javascript:;" data-id="{{ $issue->id }}" class="list-document-btn"><img width="15px" src="/images/archive.png" alt="" style="cursor: default;"><a>
    </td>
    <td style="vertical-align: middle;">{{ $issue->developerModule ? $issue->developerModule->name : 'Not Specified' }}</td>
    <td style="vertical-align: middle;">{{ $issue->subject ?? 'N/A' }}</td>
    <td style="vertical-align: middle;">{!! ['N/A', '<strong class="text-danger">Critical</strong>', 'Urgent', 'Normal'][$issue->priority] ?? 'N/A' !!}</td>
    <td class="expand-row">
        <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='developer_task' data-id="{{ $issue->id }}" title="Load messages"><img src="/images/chat.png" alt=""></button>
        <div class="td-mini-container">
            {{ strlen($issue->task) > 20 ? substr($issue->task, 0, 20).'...' : $issue->task }}
        </div>
        <div class="td-full-container hidden">
            {!! nl2br($issue->task) !!}
            @if ($issue->getMedia(config('constants.media_tags'))->first())
                <br/>
                @foreach ($issue->getMedia(config('constants.media_tags')) as $image)
                    <a href="{{ $image->getUrl() }}" target="_blank" class="d-inline-block">
                        <img src="{{ $image->getUrl() }}" class="img-responsive" style="width: 50px" alt="File">
                    </a>
                @endforeach
            @endif
            <br/>

            <button class="btn btn-secondary btn-xs" onclick="sendImage({{ $issue->id }} )">Send Attachment</button>
            <button class="btn btn-secondary btn-xs" onclick="sendUploadImage({{$issue->id}} )">Send Images</button>
            <input id="file-input{{ $issue->id }}" type="file" name="files" style="display: none;" multiple/>

            <br/>
            <div>
                <div class="panel-group">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" href="#collapse_{{$issue->id}}">Messages({{count($issue->messages)}})</a>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
    </td>
    <td>{{ \Carbon\Carbon::parse($issue->created_at)->format('H:i d-m') }} </td>
    <td data-id="{{ $issue->id }}">
        <div class="form-group">
            <div class='input-group estimate_minutes'>
                <input style="min-width: 30px;" placeholder="E.minutes" value="{{ $issue->estimate_minutes }}" type="text" class="form-control" name="estimate_minutes_{{$issue->id}}" data-id="{{$issue->id}}" id="estimate_minutes_{{$issue->id}}">
            </div>
            <button class="btn btn-secondary btn-xs estimate-time-change" data-id="{{$issue->id}}">Save</button>
        </div>
    </td>
    <td>{{ (isset($issue->timeSpent) && $issue->timeSpent->task_id > 0) ? formatDuration($issue->timeSpent->tracked) : '' }}</td>
    {{--<td>{{ $issue->submitter ? $issue->submitter->name : 'N/A' }} </td>--}}
    <td>
        <select class="form-control assign-user select2" data-id="{{$issue->id}}" name="assigned_to" id="user_{{$issue->id}}">
            <option value="">Select...</option>
            @foreach($users as $id=>$name)
                @if( isset($issue->assignedUser->id) && (int) $issue->assignedUser->id == $id )
                    <option value="{{$id}}" selected>{{ $name }}</option>
                @else
                    <option value="{{$id}}">{{ $name }}</option>
                @endif
            @endforeach
        </select>
    </td>
    <td>
        @if($issue->is_resolved)
            <strong>Resolved</strong>
        @else
            <?php echo Form::select("task_status",$statusList,$issue->status,["class" => "form-control resolve-issue","onchange" => "resolveIssue(this,".$issue->id.")"]); ?>
        @endif
    </td>
    <td>
        <select class="form-control assign-master-user select2" data-id="{{$issue->id}}" name="master_user_id" id="user_{{$issue->id}}">
            <option value="">Select...</option>
            @foreach($users as $id=>$name)
                @if( isset($issue->masterUser->id) && (int) $issue->masterUser->id == $id )
                    <option value="{{$id}}" selected>{{ $name }}</option>
                @else
                    <option value="{{$id}}">{{ $name }}</option>
                @endif
            @endforeach
        </select>
    </td>
    <td>
        @if($issue->cost > 0)
            {{ $issue->cost }}
        @else
            <input type="text" name="cost" id="cost_{{$issue->id}}" placeholder="Amount..." class="form-control save-cost" data-id="{{$issue->id}}">
        @endif
    </td>
    <td>
        <?php echo Form::select("language",["" => "N/A"] + $languages, $issue->language , ["class" => "form-control save-language select2", "data-id" => $issue->id , "id" => "language_".$issue->id]) ?>
    </td>
</tr>
<tr>
    <td>&nbsp;</td>
    <td colspan="13">
        <div id="collapse_{{$issue->id}}" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="messageList" id="message_list_{{$issue->id}}">
                    @foreach($issue->messages as $message)
                        <p>
                            <strong>
                                <?php echo !empty($message->sendTaskUsername()) ? "To : ".$message->sendTaskUsername() : ""; ?>
                                <?php echo !empty($message->sendername()) ? "From : ".$message->sendername() : ""; ?>
                                At {{ date('d-M-Y H:i:s', strtotime($message->created_at)) }}</strong>
                        </p>
                        {!! nl2br($message->message) !!} 
                        <hr/>
                    @endforeach
                </div>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <textarea class="form-control send-message-textbox" data-id="{{$issue->id}}" id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}"></textarea>
                </div>
                <div class="row" style="padding-top: 5px;">
                    <?php echo Form::select("send_message_".$issue->id,[
                        "to_developer" => "Send To Developer",
                        "to_master" => "Send To Master Developer"
                    ],null,["class" => "form-control send-message-number"]); ?>
                </div>
                <div class="row">
                    <button type="submit" id="submit_message" class="btn btn-secondary ml-3 send-message" data-id="{{$issue->id}}" style="float: right;margin-top: 2%;">Submit</button>
                </div>
            </div>
        </div>
    </td>
</tr>