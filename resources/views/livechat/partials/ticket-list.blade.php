@php
    $isAdmin = Auth::user()->hasRole('Admin');
    $isHrm = Auth::user()->hasRole('HOD of CRM');
    $base_url = URL::to('/')
@endphp
@php($statusList = \App\TicketStatuses::all()->pluck('name','id'))

@foreach ($data as $key => $ticket)
<tr>
    <td class="pl-2"><input type="checkbox" class="selected-ticket-ids" name="ticket_ids[]" value="{{ $ticket->id }}"></td>
    <td>{{ ++$i }}</td>
    <td>{{ substr($ticket->ticket_id, -5) }}</td>
    <td>
        <a href="javascript:void(0)" class="row-ticket" data-content="{{ $ticket->source_of_ticket }}">
            {{ str_limit($ticket->source_of_ticket,10)}}
        </a>
    </td>
    <td>{{ $ticket->name}}</td>
    <td>
        <a href="javascript:void(0)" class="row-ticket" data-content="{{ $ticket->email }}">
            {{ str_limit($ticket->email,7)}}
        </a>
    </td>
    <td class="pr-1">
        <a href="javascript:void(0)" class="row-ticket" data-content="{{ $ticket->subject }}">
            {{ str_limit($ticket->subject,6)}}
        </a>
    </td>
    <td>
       
        
            {{ str_limit($ticket->message,6)}}
       
    </td>
    <td>{{ $ticket->assigned_to_name }}</td>
    <td class="row-ticket" data-content="Brand : {{ !empty($ticket->brand) ? $ticket->brand : 'N/A' }}<br>
        Style : {{ !empty($ticket->style) ? $ticket->style : 'N/A' }}<br>
        Keyword : {{ !empty($ticket->keyword) ? $ticket->keyword : 'N/A' }}<br>
        Url : <a target='__blank' href='{{ !empty($ticket->image) ? $ticket->image : 'javascript:;' }}'>Click Here</a><br>
        ">
        <a herf="javascript:;">{{ $ticket->type_of_inquiry }}</a>
    </td>
    <td>{{ $ticket->country }}</td>
    <td>
        <a href="javascript:void(0)" class="row-ticket" data-content="{{ $ticket->order_no}}">
            {{ str_limit($ticket->order_no,4)}}
        </a>
    </td>
    <td class="pl-2">
        <a href="javascript:void(0)" class="row-ticket" data-content="{{ $ticket->phone_no }}">
         {{ str_limit($ticket->phone_no,13)}}
        </a>
    </td>

    <td class="table-hover-cell pr-0 pb-0">
        <div style="display:flex;" class=" d-flex flex-row w-100 justify-content-between">
            <div style="flex-grow: 1">
                <textarea  style="height:34px;" class="form-control" id="messageid_{{ $ticket->id }}" name="message" placeholder="Message"></textarea>
            </div>
            <div style="width: min-content">

                <button class="btn btn-xs btn-image send-message1 " style="margin-left:6px;"
                        data-ticketid="{{ $ticket->id }}">
                    <img src="<?php echo $base_url;?>/images/filled-sent.png"/>
                </button>

               
                <?php
        $messages = \App\ChatMessage::where('ticket_id', $ticket->id)->orderBy('created_at','desc')->get();
        $table=" <table class='table table-bordered' ><thead><tr><td>Date</td><td>orignal</td><td>Message</td></tr></thead><tbody>";
        
        foreach( $messages as $m)
        {
                                  
            $table.="<tr><td>".$m->created_at."</td>";
            $table.="<td>".$m->message."</td>";
            $table.="<td>".$m->message_en."</td></tr>";
        }
        $table.="</tbody></table>";
        
         
       ?>
       <a href="javascript:void(0)" class="row-ticket" data-content="{{ $table }}">
       <i class="fa fa-comments-o"></i>
        </a>
       
                

            </div>
       </div>
    </td>

    <td>
        <?php echo Form::select(
                                "ticket_status_id",
                                 $statusList,$ticket->status_id,
                                 [
                                     "class" => "resolve-issue border-0 globalSelect2",
                                     "onchange" => "resolveIssue(this,".$ticket->id.")",
                           ]); ?>
    </td>
    <td>
        <a href="javascript:void(0)" class="row-ticket" data-content="{{ $ticket->created_at}}">
            {{ str_limit($ticket->created_at,10)}}
        </a>
        </td>
    <td>
        <div class=" d-flex">
          <button type="button"
                  class="btn btn-xs send-email-to-vender"
                  data-subject="{{ $ticket->subject }}"
                  data-message="{{ $ticket->message }}"
                  data-email="{{ $ticket->email }}"
                  data-id="{{$ticket->id}}">
            <i class="fa fa-envelope"></i>
          </button>

          <button type="button"
                  class="btn btn-xs load-communication-modal"
                  data-is_admin="{{ Auth::user()->hasRole('Admin') }}"
                  data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}"
                  data-object="ticket" data-id="{{$ticket->id}}"
                  data-load-type="text"
                  data-all="1"
                  title="Load messages">
                  <i class="fa fa-whatsapp"></i>
          </button>

          <button type="button"
                  class="btn btn-xs btn-assigned-to-ticket"
                  data-id="{{$ticket->id}}">
                <i class="fa fa-comments-o"></i>
            </button>

            <?php
        $messages = \App\Email::where('model_type','App\Tickets')->where('model_id', $ticket->id)->orderBy('created_at','desc')->get();
        $table=" <table class='table table-bordered' ><thead><tr><td>Date</td><td>Original</td><td>Message</td></tr></thead><tbody>";
        $tableemail=" <table style='width:1000px' class='table table-bordered' ><thead><tr><td>Date</td><td>Sender</td><td>Receiver</td><td>Mail <br> Type</td><td>Subject</td><td>Message</td><td>Action</td></tr></thead><tbody>";
 
        
        foreach( $messages as $m)
        {
                    
            $table.="<tr><td>".$m->created_at."</td>";
            $table.="<td>".$m->message."</td>";
            $table.="<td>".$m->message_en."</td></tr>";

            $tableemail.="<tr><td>".$m->created_at."</td>";
            $tableemail.="<td>".$m->from."</td>";
            $tableemail.="<td>".$m->to."</td>";
            $tableemail.="<td>".$m->type."</td>";
            $tableemail.="<td>".$m->subject."</td>";
            $tableemail.="<td>".$m->message."</td>";
            $tableemail.='<td><a title="Resend" class="btn-image resend-email-btn" data-type="resend" data-id="'.$m->id.'" >
                    <i class="fa fa-repeat"></i> </a></td></tr>';
            
            
           

            
        }
        $table.="</tbody></table>";
        $tableemail.="</tbody></table>";
        
         
       ?>
        <a href="javascript:void(0)" class="row-ticket" data-content="{{ $table}}">
            <i class="fa fa-envelope" style="margin-left: -10px"></i>
        </a>

        <a href="javascript:void(0)" onclick="message_show(this);" data-content="{{ $tableemail}}" title="Resend Email" >
            <i class="fa fa-repeat" aria-hidden="true"></i>

        </a>
            <button type="button" class="btn btn-delete-template no_pd" id="softdeletedata" data-id="{{$ticket->id}}">
                <i class="fa fa-trash" style="margin-left: -10px"></i></button>

        </div>
    </td>
</tr>

@endforeach




