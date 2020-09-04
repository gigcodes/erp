
@php($statusList = \App\TicketStatuses::all()->pluck('name','id'))
@foreach ($data as $key => $ticket)
          <tr>
               <td>{{ ++$i }}</td>
              <th>{{ $ticket->ticket_id }}
                    
              </th>

              <th>{{ $ticket->name}}</th>
              <th>{{ $ticket->email }}</th>
              <th>{{ $ticket->subject }}</th>
              <th>{{ $ticket->message }}</th>
              <th>{{ $ticket->assigned_to_name }}</th>
              <th>
              <?php echo Form::select("task_status",$statusList,$ticket->status_id,["class" => "form-control resolve-issue","onchange" => "resolveIssue(this,".$ticket->id.")"]); ?>
              </th>
              <th>
                    <div class="chat-righbox">
                    <button type="button" class="btn send-email-to-vender" data-subject="{{ $ticket->subject }}" data-message="{{ $ticket->message }}" data-email="{{ $ticket->email }}" data-id="{{$ticket->id}}"><i class="fa fa-envelope-square"></i></button>
                    <button type="button" class="btn " data-id="{{$ticket->id}}" ><i class="fa fa-whatsapp"></i></button>
                    <button type="button" class="btn btn-assigned-to-ticket" data-id="{{$ticket->id}}"><i class="fa fa-comments-o"></i></button>

                         
                    </div>

          </tr>
               
@endforeach
