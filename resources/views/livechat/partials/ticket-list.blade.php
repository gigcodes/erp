@php
    $isAdmin = Auth::user()->hasRole('Admin');
    $isHrm = Auth::user()->hasRole('HOD of CRM');
    $base_url = URL::to('/')
@endphp
@php($statusList = \App\TicketStatuses::all()->pluck('name','id'))

@foreach ($data as $key => $ticket)
<tr style="width:100%">
    <td style="width: 1%">{{ ++$i }}</td>
    <td>{{ substr($ticket->ticket_id, -5) }}</td>
    <td style="width: 65px;">
        <a href="javascript:void(0)" class="row-ticket" data-content="{{ $ticket->source_of_ticket }}">
            {{ str_limit($ticket->source_of_ticket,10)}}
        </a>
    </td>
    <td style="width:65px;">{{ $ticket->name}}</td>
    <td>
        <a href="javascript:void(0)" class="row-ticket" data-content="{{ $ticket->email }}">
            {{ str_limit($ticket->email,6)}}
        </a>
    </td>
    <td style="width:70px">
        <a href="javascript:void(0)" class="row-ticket" data-content="{{ $ticket->subject }}">
            {{ str_limit($ticket->subject,4)}}
        </a>
    </td>
    <td style="width:77px">
        <a href="javascript:void(0)" class="row-ticket" data-content="{{ $ticket->message }}">
            {{ str_limit($ticket->message,5)}}
        </a>
    </td>
    <td>{{ $ticket->assigned_to_name }}</td>
    <td>{{ $ticket->type_of_inquiry }}</td>
    <td>{{ $ticket->country }}</td>
    <td>{{ str_limit($ticket->order_no,4)}}</td>
    <td>{{ $ticket->phone_no }}</td>
    
    <td class="table-hover-cell">
        <div class="row d-flex flex-row w-100 justify-content-between">
            <div class="col-9">
                <textarea  style="width:100px;height:35px;" class="form-control" id="messageid_{{ $ticket->id }}" name="message" placeholder="Message"></textarea>
            </div>
            <div class="col-2">  
                  <button class="btn btn-sm btn-image send-message1" data-ticketid="{{ $ticket->id }}"><img src="<?php echo $base_url;?>/images/filled-sent.png"/></button>
            </div>
       </div>
    </td>    
    
    <td style="width:75px;">
        <?php echo Form::select( 
                                "ticket_status_id",
                                 $statusList,$ticket->status_id,
                                 [ 
                                     "class" => "resolve-issue border-0",
                                     "onchange" => "resolveIssue(this,".$ticket->id.")",
                           ]); ?>
    </td>        
    <td>
        <div class="chat-righbox d-flex">
          <button type="button" 
                  class="btn btn-xs send-email-to-vender" 
                  data-subject="{{ $ticket->subject }}" 
                  data-message="{{ $ticket->message }}" 
                  data-email="{{ $ticket->email }}" 
                  data-id="{{$ticket->id}}">
            <i class="fa fa-envelope-square"></i>
          </button>
          <button type="button" class="btn btn-xs load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="ticket" data-id="{{$ticket->id}}" data-load-type="text" data-all="1" title="Load messages"><i class="fa fa-whatsapp"></i></button>
          <button type="button" class="btn btn-xs btn-assigned-to-ticket" data-id="{{$ticket->id}}"><i class="fa fa-comments-o"></i></button>
          
        </div>
    </td>
</tr>          
@endforeach

   
   