@php
    $isAdmin = Auth::user()->hasRole('Admin');
    $isHrm = Auth::user()->hasRole('HOD of CRM');
    $base_url = URL::to('/')
@endphp
@php($statusList = \App\TicketStatuses::all()->pluck('name','id'))
@foreach ($data as $key => $ticket)
<tr>
    <td>{{ ++$i }}</td>
    <td>{{ $ticket->ticket_id }}</td>
    <td>{{ $ticket->source_of_ticket }}</td>
    <td style="width:150px;">{{ $ticket->name}}</td>
    <td>{{ $ticket->email }}</td>
    <td>{{ substr($ticket->subject,0,6) }}</td>
    <td>{{ substr($ticket->message,0,6) }}</td>
    <td>{{ $ticket->assigned_to_name }}</td>
    <td>{{ $ticket->type_of_inquiry }}</td>
    <td>{{ $ticket->country }}</td>
    <td>{{ substr($ticket->order_no,0,5) }}</td>
    <td>{{ $ticket->phone_no }}</td>
    <td class="table-hover-cell" style="word-break: break-all;padding: 5px;">
        <div class="row">
            <div class="col-md-12 form-inline cls_remove_rightpadding">
                <div class="row cls_textarea_subbox">
                    <div class="col-md-8 cls_remove_rightpadding">
                        <textarea style="height: 30px;" rows="1" class="form-control quick-message-field cls_quick_message" id="messageid_{{ $ticket->id }}" name="message" placeholder="Message"></textarea>
                    </div>
                    <div class="col-md-2 cls_remove_allpadding">
                        <button class="btn btn-sm btn-image send-message1" data-ticketid="{{ $ticket->id }}"><img src="<?php echo $base_url;?>/images/filled-sent.png"/></button>
                    </div>
                </div>
            </div>
       </div>
    </td>     
    <td><?php echo Form::select("task_status",$statusList,$ticket->status_id,["class" => "form-control resolve-issue","onchange" => "resolveIssue(this,".$ticket->id.")","style"=>"height: 30px;"]); ?></td>
    <td style="width: 100px;">
        <div class="chat-righbox">
          <button type="button" class="btn btn-xs send-email-to-vender" data-subject="{{ $ticket->subject }}" data-message="{{ $ticket->message }}" data-email="{{ $ticket->email }}" data-id="{{$ticket->id}}"><i class="fa fa-envelope-square"></i></button>
          <button type="button" class="btn btn-xs load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="ticket" data-id="{{$ticket->id}}" data-load-type="text" data-all="1" title="Load messages"><i class="fa fa-whatsapp"></i></button>
          <button type="button" class="btn btn-xs btn-assigned-to-ticket" data-id="{{$ticket->id}}"><i class="fa fa-comments-o"></i></button>
        </div>
    </td>
</tr>          
@endforeach
