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
                    <th>#</th>
                    <th>Task</th>
                    <th>Communication</th>
                    <th>Assigned to</th>
                    <th>Created at</th>
                </tr>
            </thead>
            <tbody class="conent">
                @foreach ($developerTasks as $developerTask)
                    <tr>
                        <td>{{ $developerTask->id }}</td>
                        <td>{{ $developerTask->subject }}</td>
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
                                            <textarea rows="1" cols="30" class="form-control quick-message-field cls_quick_message" id="messageid_{{ $developerTask->id }}" name="message" placeholder="Message"></textarea>
                                            <div    id="message-chat-txt-{{ $developerTask->id }}">{{ substr($message,0,15) }}</div>
                                        </div>
                                        <div class="col-md-1 cls_remove_allpadding">
                                            <button class="btn btn-sm btn-image send-message1" data-task-id="{{ $developerTask->id }}"><img src="/images/filled-sent.png"></button>
                                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="1" data-is_hod_crm="" data-object="developer_task" data-id="{{ $developerTask->id }}" data-load-type="text" data-all="1" title="Load messages"><img src="/images/chat.png" alt=""></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>   
                        <td>{{ ($developerTask->assignedUser) ? $developerTask->assignedUser->name : "N/A" }}</td>
                        <td>{{ $developerTask->created_at }}</td>
                    </tr>
                @endforeach
           </tbody>
        </table> 
    </div>
