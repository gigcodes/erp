

<tr style="color:grey;">
    <td  >

    <a href="{{ url("development/task-detail/$issue->id") }}">{{ $issue->id }}
            @if($issue->is_resolved==0)	
                <input type="checkbox" name="selected_issue[]" value="{{$issue->id}}" {{in_array($issue->id, $priority) ? 'checked' : ''}}>	
            @endif	
        </a>


        
        <!-- <a href="{{ url("development/task-detail/{$issue->id}") }}">{{ $issue->id }}
        </a> -->
        <a href="javascript:;" data-id="{{ $issue->id }}" class="upload-document-btn"><img width="15px" src="/images/attach.png" alt="" style="cursor: default;"><a>
        <a href="javascript:;" data-id="{{ $issue->id }}" class="list-document-btn"><img width="15px" src="/images/archive.png" alt="" style="cursor: default;"><a>
        <br>
        {{ \Carbon\Carbon::parse($issue->created_at)->format('H:i d-m') }}
        @if($issue->task_type_id == 1) Devtask @elseif($issue->task_type_id == 3) Issue @endif
    </td>
    <td style="vertical-align: middle;">    
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
    <td style="vertical-align: middle;word-break: break-all;"><p>{{ $issue->subject ?? 'N/A' }}</p> </td>
    <td class="expand-row">
    <!-- class="expand-row" -->
    <span style="word-break: break-all;">{{  \Illuminate\Support\Str::limit($issue->message, 150, $end='...') }}</span>
    <input type="text" class="form-control send-message-textbox" data-id="{{$issue->id}}" id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}" style="margin-bottom:5px"/>
    <?php echo Form::select("send_message_".$issue->id,[
                        "to_developer" => "Send To Developer",
                        "to_master" => "Send To Master Developer"
                    ],null,["class" => "form-control send-message-number", "style" => "width:85% !important;display: inline;"]); ?>
    <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image send-message-open" type="submit" id="submit_message"  data-id="{{$issue->id}}" ><img src="/images/filled-sent.png"/></button>

  
        <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='developer_task' data-id="{{ $issue->id }}" style="margin-top: 2%;" title="Load messages"><img src="/images/chat.png" alt=""></button>
    <br>
        <div class="td-full-container hidden">
            <button class="btn btn-secondary btn-xs" onclick="sendImage({{ $issue->id }} )">Send Attachment</button>
            <button class="btn btn-secondary btn-xs" onclick="sendUploadImage({{$issue->id}} )">Send Images</button>
            <input id="file-input{{ $issue->id }}" type="file" name="files" style="display: none;" multiple/>
         </div>
    </td>
    
  
    
    <td>
        @if($issue->is_resolved)
            <strong>Done</strong>
        @else
            <?php echo Form::select("task_status",$statusList,$issue->status,["class" => "form-control resolve-issue","onchange" => "resolveIssue(this,".$issue->id.")"]); ?>
        @endif
    </td>
 
</tr>