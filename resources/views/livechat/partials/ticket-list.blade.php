@php($statusList = \App\TicketStatuses::all()->pluck('name','id'))
@foreach ($data as $key => $ticket)
<tr>
    <td>{{ ++$i }}</td>
    <td>{{ $ticket->ticket_id }}</td>
    <td>{{ $ticket->source_of_ticket }}</td>
    <td>{{ $ticket->name}}</td>
    <td>{{ $ticket->email }}</td>
    <td>{{ $ticket->subject }}</td>
    <td>{{ $ticket->message }}</td>
    <td>{{ $ticket->assigned_to_name }}</td>
    <td><?php echo Form::select("task_status",$statusList,$ticket->status_id,["class" => "form-control resolve-issue","onchange" => "resolveIssue(this,".$ticket->id.")"]); ?></td>
    <td>
        <div class="chat-righbox">
          <button type="button" class="btn send-email-to-vender" data-subject="{{ $ticket->subject }}" data-message="{{ $ticket->message }}" data-email="{{ $ticket->email }}" data-id="{{$ticket->id}}"><i class="fa fa-envelope-square"></i></button>
          <button type="button" class="btn " data-id="{{$ticket->id}}" ><i class="fa fa-whatsapp"></i></button>
          <button type="button" class="btn btn-assigned-to-ticket" data-id="{{$ticket->id}}"><i class="fa fa-comments-o"></i></button>
        </div>
    </td>
</tr>          
@endforeach
