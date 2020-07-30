<tr class="text-primary">
    <td>
        <a href="{{ url("development/task-detail/$issue->id") }}">{{ $issue->id }}
        </a>
        <a href="javascript:;" data-id="{{ $issue->id }}" class="upload-document-btn"><img width="15px" src="/images/attach.png" alt="" style="cursor: default;"><a>
        <a href="javascript:;" data-id="{{ $issue->id }}" class="list-document-btn"><img width="15px" src="/images/archive.png" alt="" style="cursor: default;"><a>
        <br>
        {{ \Carbon\Carbon::parse($issue->created_at)->format('H:i d-m') }}
        @if($issue->task_type_id == 1) Devtask @elseif($issue->task_type_id == 3) Issue @endif
    </td>
    <td><a href="{{ url("development/task-detail/$issue->id") }}">{{ $issue->developerModule ? $issue->developerModule->name : 'Not Specified' }}</a></td>

    <td>{{ $issue->subject }}</td>
    
    <td>
        {{ $issue->task }}
        @if ($issue->getMedia(config('constants.media_tags'))->first())
            <br>
            @foreach ($issue->getMedia(config('constants.media_tags')) as $image)
                <a href="{{ $image->getUrl() }}" target="_blank" class="d-inline-block">
                    <img src="{{ $image->getUrl() }}" class="img-responsive" style="width: 50px" alt="">
                </a>
            @endforeach
        @endif
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
    <td>&nbsp;</td>
    <td>{{ (isset($issue->timeSpent) && $issue->timeSpent->task_id > 0) ? formatDuration($issue->timeSpent->tracked) : '' }}</td>
    <td>
    <label for="" style="font-size: 12px;">Assigned To :</label>
        @if($issue->assignedUser)
            <p>{{ $issue->assignedUser->name }}</p>
        @else
            <p>Unassigned</p>
        @endif
        <label for="" style="font-size: 12px;">Lead :</label>
        @if($issue->masterUser)
            <p>{{ $issue->masterUser->name  }}</p>
        @else
            <p>N/A</p>
        @endif
    </td>
    <td>
        @if($issue->is_resolved)
            <strong>Done</strong>
        @else
            <select name="task_status" id="task_status" class="form-control change-task-status" data-id="{{$issue->id}}">
                <option value="">Please Select</option>
                <option value="Planned" {{ (!empty($issue->status) && $issue->status ==  'Planned' ? 'selected' : '') }}>Planned</option>
                <option value="In Progress" {{ (!empty($issue->status) && $issue->status  ==  'In Progress' ? 'selected' : '') }}>In Progress</option>
                <option value="Done" {{ (!empty($issue->status) && $issue->status ==   'Done' ? 'selected' : '') }}>Done</option>
            </select>
        @endif
    </td>
    <td>
        @if($issue->cost > 0)
            {{ $issue->cost }}
        @else
            <input type="text" name="cost" id="cost_{{$issue->id}}" placeholder="Amount..." class="form-control save-cost" data-id="{{$issue->id}}">
        @endif
    </td>
    <td>
    @if($issue->is_milestone)
        <p style="margin-bottom:0px;">Milestone : @if($issue->is_milestone) Yes @else No @endif</p>
        <p style="margin-bottom:0px;">Total : {{$issue->no_of_milestone}}</p>
        <p style="margin-bottom:0px;">Completed : {{$issue->milestone_completed}}</p>
    @else
    No 
    @endif

    </td>

    <td>
        <?php echo $issue->language; ?>

    </td>
    </tr>
    <tr>
        <td colspan="14">
            <div id="collapse_{{$issue->id}}" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="messageList" id="message_list_{{$issue->id}}">
                        @foreach($issue->messages as $message)
                            <p>
                                <strong>
                                    <?php echo !empty($message->sendTaskUsername()) ? "To : ".$message->sendTaskUsername() : ""; ?>
                                    <?php echo !empty($message->sendername()) ? "From : ".$message->sendername() : ""; ?>
                                    At {{ date('d-M-Y H:i:s', strtotime($message->created_at)) }}
                                </strong>
                            </p>
                            {!! nl2br($message->message) !!}
                            <hr/>
                        @endforeach
                    </div>
                </div>
                <div class="panel-footer">
                    <textarea class="form-control send-message-textbox" data-id="{{$issue->id}}" id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}"></textarea>
                    <button type="submit" id="submit_message" class="btn btn-secondary ml-3 send-message" data-id="{{$issue->id}}" style="float: right;margin-top: 2%;">Submit</button>
                </div>
            </div>
        </td>
    </tr>