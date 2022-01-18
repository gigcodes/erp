    <div class="col-md-12">
        <form class="form-inline" method="post" action="<?php echo route("scrap.task-list.create",[$id]); ?>">
           {!! csrf_field() !!}
          <input type="text" name="task_subject" class="form-control mb-2 mr-sm-2" placeholder="Enter task subject" id="task-subject">
          <input type="text" name="task_description" class="form-control mb-2 mr-sm-2" placeholder="Enter Task Description" id="task-description">
        
          <?php echo Form::select("assigned_to",["" => "Select-user"] + \App\User::pluck("name","id")->toArray(),null, ["class" => "form-control mb-2 mr-sm-2 select2 col-md-2"]); ?>
         
          <button type="submit" class="btn btn-secondary mb-2 btn-create-task">Submit</button>
        </form>
    </div>  
    <div class="col-md-12">
        <table class="table table-bordered table-striped sort-priority-scrapper" style="table-layout: fixed;">
            <thead>
                <tr>
                    <th width="6%">#</th>
                    <th width="8%">Task</th>
                    <th width="50%">Communication</th>
                    <th width="12%">Assigned to</th>
                    <th width="17%">Created at</th>
                </tr>
            </thead>
            <tbody class="conent">
                @foreach ($developerTasks as $developerTask)
                    <tr>
                        <td>{{ $developerTask->id }}</td>
                        <td class="Website-task">{{ $developerTask->subject }}
                            <br>
                            @if (isset($developerTask->timeSpent) && $developerTask->timeSpent->task_id > 0)
                              Developer : {{ formatDuration($developerTask->timeSpent->tracked) }}
                            @endif
                            @if (isset($developerTask->leadtimeSpent) && $developerTask->leadtimeSpent->task_id > 0)
                              Lead : {{ formatDuration($developerTask->leadtimeSpent->tracked) }}
                            @endif
                            @if (isset($developerTask->testertimeSpent) && $developerTask->testertimeSpent->task_id > 0)
                              Tester : {{ formatDuration($developerTask->testertimeSpent->tracked) }}
                            @endif
                        </td>
                            @php
                                  $whatsApp = $developerTask->whatsAppAll()->first();
                                  $message = "";
                                  if ($whatsApp) {
                                      $message = trim($whatsApp->message);
                                  }  
                            @endphp

                         <td class="table-hover-cell " style="word-break: break-all;padding: 5px;">
                                <div class="form-inline d cls_remove_rightpadding">
                                    <div class=" cls_textarea_subbox w-100 align-items-center">
                                        <div class=" d-flex cls_remove_rightpadding align-items-center">
                                            <textarea rows="1" cols="25" class="form-control quick-message-field cls_quick_message" id="messageid_{{ $developerTask->id }}" name="message"  placeholder="Message" style="width:30%;"></textarea>
                                            <div id="message-chat-txt-{{ $developerTask->id }}" class="ml-3 Website-task" style="width:30%;">{{ substr($message,0,15) }}</div>
                                                <select class="form-control quickComments select2-quick-reply" name="quickComment" style="width: 50% !important;" >
                                                    <option  data-vendorid="{{ $developerTask->id }}"  value="">Auto Reply</option>
                                                    <?php
                                                    if(isset($replies)) {
                                                    foreach ($replies as $key_r => $value_r) { ?>
                                                        <option title="<?php echo $value_r;?>" data-developerTask="{{ $developerTask->id }}" value="<?php echo $key_r;?>">
                                                            <?php
                                                            $reply_msg = strlen($value_r) > 12 ? substr($value_r, 0, 12) : $value_r;
                                                            echo $reply_msg;
                                                            ?>
                                                        </option>
                                                    <?php } }
                                                    ?>
                                                </select>
                                                <a class="btn btn-image delete_quick_comment-scrapp"><img src="<?php echo url('/');?>/images/delete.png" style="cursor: default; width: 16px;"></a>
                                                <div class="cls_remove_allpadding">
                                                    <?php echo Form::select("send_message_".$developerTask->id,[
                                                          "to_developer" => "Send To Developer",
                                                          "to_master" => "Send To Master Developer",
                                                          "to_team_lead" => "Send To Team Lead",
                                                          "to_tester" => "Send To Tester"
                                                      ],null,["class" => "form-control send-message-number-".$developerTask->id]); 
                                                    ?>
                                                </div>
                                            <button class="btn btn-sm btn-image send-message1" data-task-id="{{ $developerTask->id }}"style="padding:0;"><img src="/images/filled-sent.png"></button>
                                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="1" data-is_hod_crm="" data-object="developer_task" data-id="{{ $developerTask->id }}" data-load-type="text" data-all="1" title="Load messages"><img src="/images/chat.png" alt=""></button>
                                        </div>
                                    </div>
                                </div>
                        </td>   
                        <td class="Website-task">
                          {{ ($developerTask->assignedUser) ? $developerTask->assignedUser->name : "N/A" }}
                        </td >
                        <td>{{ $developerTask->created_at }}</td>
                    </tr>
                @endforeach
           </tbody>
        </table> 
    </div>
    <script>
    $(".select2-quick-reply").select2( { tags: true } );

       
    </script>
