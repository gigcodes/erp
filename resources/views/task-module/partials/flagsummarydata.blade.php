
<?php   $special_task = \App\Task::find($issue->id); ?>
<tr style="color:grey;">
    <td><input type="checkbox" name="taskIds[]" class="rowCheckbox" value="{{ $issue->id }}"></td>
    <td style="display:table-cell;vertical-align: baseline;">
    {{ $issue->id }}
    </td>

    <td style="display:table-cell;vertical-align: baseline;">
        @if($special_task->site_developement_id && $special_task->site_development->store_website)
                {{ $special_task->site_development->store_website?$special_task->site_development->store_website->title:'' }}
        @endif
    </td>
    <td class="Website-task" style="vertical-align: baseline;">    
       {{ $issue->task_subject }}
    </td>
       <td>
        <div class="d-flex">
     <!--   <label for="" style="font-size: 12px;">Assigned To :</label>-->
        @if(isset($special_task->assign_to))
           {{ $special_task->assignedTo->name }}
        @endif
        <button style="float:right;padding-right:0px; background: none;" type="button" class="btn btn-xs show-user-history" title="Show History" data-id="{{$issue->id}}" data-type="task"><i class="fa fa-info-circle"></i></button>
   <!--     <label for="" style="font-size: 12px;margin-top:10px;">Lead :</label>-->
    </div>
    </td>
     <td  style="vertical-align: baseline;">
     @if (isset($special_task->timeSpent) && $special_task->timeSpent->task_id > 0)
            {{ formatDuration($special_task->timeSpent->tracked) }}

            <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history_task" title="Show tracked time History" data-id="{{$issue->id}}" data-type="developer"><i class="fa fa-info-circle"></i></button>
        @endif
    </td>
    <td  style="vertical-align: baseline;">
   {{ $issue->approximate }}

   <button type="button" class="btn btn-xs show-time-history-task" title="Show History" data-id="{{$issue->id}}" data-user_id="{{$issue->assign_to}}" style="background: none;"><i class="fa fa-info-circle"></i></button>
    </td>
    <td  style="vertical-align: baseline;">
        <div class="d-flex">
        {{ $issue->due_date }}
        <button type="button" class="btn btn-xs show-date-history" title="Show tracked time History" data-id="{{$issue->id}}" data-type="task" style="margin-top: -2px; background: none;"><i class="fa fa-info-circle"></i></button>
       </div>
    </td>

    <td class="communication-td devtask-com"style="border-bottom: none;">
    <!-- class="expand-row" -->
  
   
    <input type="text" class="form-control send-message-textbox" data-id="{{$issue->id}}" id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}" style="margin-bottom:5px;width:52%;display:inline;"/>
   
    <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image send-message-open" type="submit" id="submit_message"  data-id="{{$issue->id}}" style="width:10%"><img src="/images/filled-sent.png"/></button>
    <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='task' data-id="{{ $issue->id }}" style="mmargin-top: -0%;margin-left: -2%; width:10%" title="Load messages"><img src="/images/chat.png" alt=""></button>
    <span class="Website-task pr-0 {{ ($issue->message && $issue->message_status == 0) || $issue->message_is_reminder == 1 || ($issue->sent_to_user_id == Auth::id() && $issue->message_status == 0) ? '' : '' }} justify-content-between expand-row-msg" style="word-break: break-all;margin-top:6px; width:40%; margin-right:-13px;" data-id="{{$issue->id}}">
    <span class="td-mini-container-{{$issue->id}} Website-task" style="margin:0px;">
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
    <td>
       <div class="d-flex">                         
    <select id="master_user_id" class="form-control change-task-status select2" data-id="{{$issue->id}}" name="master_user_id" id="user_{{$issue->id}}">
     @if(!empty($task_statuses))
            @foreach($task_statuses as $index => $status)
                @if( $status->id == $issue->status )
                    <option value="{{$status->id}}" selected>{{ $status->name }}</option>
                @else
                    <option value="{{$status->id}}">{{ $status->name }}</option>
                @endif
            @endforeach
        @endif
    </select>
   
        <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-status-history" title="Show Status History" data-id="{{$issue->id}}" data-type="task">
                <i class="fa fa-info-circle"></i>
            </button>
            @if ($issue->is_flagged == 1)
         <button type="button" class="btn pr-0 btn-image flag-task pd-5" data-type="task" data-id="{{ $issue->id }}"><img src="{{asset('images/flagged.png')}}"></button>
         @else
         <button type="button" class="btn btn-image flag-task pd-5" data-type="task" data-id="{{ $issue->id }}"><img src="{{asset('images/unflagged.png')}}"/></button>
         @endif
     </div>
    </td>
        <td>
        <button type="button" title="Task Time history" class="btn tasktime-history-btn btn-xs pull-left" data-id="{{$issue->id}}">
          <i class="fa fa-history"></i>
      </button>
    </td>
 
</tr>

