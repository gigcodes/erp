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
        <table class="table table-bordered table-striped sort-priority-scrapper">
            <thead>
                <tr>
                    <th width="2%">#</th>
                    <th width="25%">Task</th>
                    <th width="35%">Communication</th>
                    <th width="15%">Assigned to</th>
                    <th width="15%">Created at</th>
                </tr>
            </thead>
            <tbody class="conent">
                @foreach ($developerTasks as $developerTask)
                    <tr>
                        <td>{{ $developerTask->id }}</td>
                        <td>{{ $developerTask->subject }}
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
                            <div class="row">
                                <div class="col-md-12 form-inline cls_remove_rightpadding">
                                    <div class="row cls_textarea_subbox">
                                        <div class="col-md-8 cls_remove_rightpadding">
                                            <textarea rows="1" cols="25" class="form-control quick-message-field cls_quick_message" id="messageid_{{ $developerTask->id }}" name="message" placeholder="Message"></textarea>
                                            <div    id="message-chat-txt-{{ $developerTask->id }}">{{ substr($message,0,15) }}</div>
                                        </div>
                                        <div class="col-md-1 cls_remove_allpadding">
                                            <?php echo Form::select("send_message_".$developerTask->id,[
                                                  "to_developer" => "Send To Developer",
                                                  "to_master" => "Send To Master Developer",
                                                  "to_team_lead" => "Send To Team Lead",
                                                  "to_tester" => "Send To Tester"
                                              ],null,["class" => "form-control send-message-number-".$developerTask->id]); 
                                            ?>
                                            <button class="btn btn-sm btn-image send-message1" data-task-id="{{ $developerTask->id }}"><img src="/images/filled-sent.png"></button>
                                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="1" data-is_hod_crm="" data-object="developer_task" data-id="{{ $developerTask->id }}" data-load-type="text" data-all="1" title="Load messages"><img src="/images/chat.png" alt=""></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>   
                        <td>
                          {{ ($developerTask->assignedUser) ? $developerTask->assignedUser->name : "N/A" }}
                        </td>
                        <td>{{ $developerTask->created_at }}</td>
                    </tr>
                @endforeach
           </tbody>
        </table> 
    </div>
