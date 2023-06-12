@php
    $isAdmin = Auth::user()->hasRole('Admin');
    $isHrm = Auth::user()->hasRole('HOD of CRM');
    $base_url = URL::to('/');
@endphp
@php($statusList = \App\TicketStatuses::all()->pluck('name','id'))

@foreach ($data as $key => $ticket)
<tr style="background-color: {{ !empty($ticket->ticketStatus) ? $ticket->ticketStatus->ticket_color : '#f4f4f6'}} !important;">
    <td class="pl-2"><input type="checkbox" class="selected-ticket-ids" name="ticket_ids[]" value="{{ $ticket->id }}"></td>

    <td class="chat-msg">{{ substr($ticket->ticket_id, -5) }}</td>
    <td class="chat-msg">
        <a href="javascript:void(0)" class="row-ticket" data-content="{{ $ticket->source_of_ticket }}">
            {{ $ticket->source_of_ticket}}
        </a>
    </td>
    <td class="chat-msg">{{ $ticket->name}}</td>
    <td class="chat-msg">
        <a href="javascript:void(0)" class="row-ticket" data-content="{{ $ticket->email }}">
            {{ $ticket->email}}
        </a>
    </td>
    <td class="pr-1 chat-msg">
        <a href="javascript:void(0)" class="row-ticket" data-content="{{ $ticket->subject }}">
            {{   $ticket->subject}}
        </a>
    </td>
    <td class="chat-msg">


            {{ $ticket->message}}

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
            {{ Str::limit($ticket->order_no,4)}}
        </a>
    </td>
    <td class="pl-2 chat-msg">
        <a href="javascript:void(0)" class="row-ticket" data-content="{{ $ticket->phone_no }}">
         {{ $ticket->phone_no}}
        </a>
    </td>

    <td>
        <div class="d-flex" role="toolbar">
            <div class="">
                <textarea  rows="1" class="form-control" id="messageid_{{ $ticket->id }}" name="message" placeholder="Message"></textarea>
            </div>
            <div class="">
                <button class="btn btn-xs send-message1"
                        data-ticketid="{{ $ticket->id }}">
                    <i class="fa fa-paper-plane"></i>
                </button>
                <a href="javascript:void(0)" class="row-ticket btn btn-xs" data-ticket-id="{{ $ticket->id }}" >
                    <i class="fa fa-comments-o"></i>
                </a>
            </div>
        </div>
    </td>
    <td>
        <input type="date" class="form-control" onchange="changeDate(this,{{$ticket->id}})" id="date_{{ $ticket->id }}" value="{{($ticket->resolution_date)?date('Y-m-d',strtotime($ticket->resolution_date)):''}}" name="resolution_date" placeholder="Resolution date"/>
    </td>
    <td>
        <?php echo Form::select(
    "ticket_status_id",
    $statusList, $ticket->status_id,
    [
        "class" => "resolve-issue border-0 globalSelect2",
        "onchange" => "resolveIssue(this," . $ticket->id . ")",
    ]); ?>
    </td>
    <td>
        <a href="javascript:void(0)" class="row-ticket" data-content="{{ $ticket->created_at}}">
            {{ Str::limit(date('d-m-y', strtotime($ticket->created_at)),8,'..')}}

        </a>
        </td>
    <td>
        <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Ticketsbtn('{{$ticket->id}}')"><i class="fa fa-arrow-down"></i></button>
    </td>

</tr>

<tr class="action-ticketsbtn-tr-{{$ticket->id}} d-none">
    <td class="font-weight-bold">Action</td>
    <td colspan="15">
        <div class="">
            <button type="button"
                    class="btn btn-xs send-email-to-vender "
                    data-subject="{{ $ticket->subject }}"
                    data-message="{{ $ticket->message }}"
                    data-email="{{ $ticket->email }}"
                    data-id="{{$ticket->id}}"
                    title="Send email to vender">
                <i class="fa fa-envelope"></i>
            </button>
            @if($ticket->customer_id > 0)
                <button type="button"
                        class="btn btn-xs load-communication-modal "
                        data-is_admin="{{ Auth::user()->hasRole('Admin') }}"
                        data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}"
                        data-object="customer" data-id="{{$ticket->customer_id}}"
                        data-load-type="text"
                        data-all="1"
                        title="Load communication">
                    <i class="fa fa-whatsapp"></i>
                </button>
            @else
                <button type="button"
                        class="btn btn-xs load-communication-modal "
                        data-is_admin="{{ Auth::user()->hasRole('Admin') }}"
                        data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}"
                        data-object="ticket" data-id="{{$ticket->id}}"
                        data-load-type="text"
                        data-all="1"
                        title="Load communication">
                    <i class="fa fa-whatsapp"></i>
                </button>
            @endif

            <button type="button"
                    class="btn btn-xs btn-assigned-to-ticket "
                    data-id="{{$ticket->id}}"
                    title="Assigned to ticket">
                <i class="fa fa-comments-o"></i>
            </button>

            <?php
            $messages = \App\Email::where('model_type', \App\Tickets::class)->where('model_id', $ticket->id)->orderBy('created_at', 'desc')->get();
            $table = " <table class='table table-bordered' ><thead><tr><td>Date</td><td>Original</td><td>Message</td></tr></thead><tbody>";
            $tableemail = " <table style='width:1000px' class='table table-bordered' ><thead><tr><td>Date</td><td>Sender</td><td>Receiver</td><td>Mail <br> Type</td><td>Subject</td><td>Message</td><td>Action</td></tr></thead><tbody>";

            foreach ($messages as $m) {

                $table .= "<tr><td>" . $m->created_at . "</td>";
                $table .= "<td>" . $m->message . "</td>";
                $table .= "<td>" . $m->message_en . "</td></tr>";

                $tableemail .= "<tr><td>" . $m->created_at . "</td>";
                $tableemail .= "<td>" . $m->from . "</td>";
                $tableemail .= "<td>" . $m->to . "</td>";
                $tableemail .= "<td>" . $m->type . "</td>";
                $tableemail .= "<td>" . $m->subject . "</td>";
                $tableemail .= "<td>" . $m->message . "</td>";
                $tableemail .= '<td><a title="Resend" class="btn-image resend-email-btn" data-type="resend" data-id="' . $m->id . '" >
                    <i class="fa fa-repeat"></i> </a></td></tr>';

            }
            $table .= "</tbody></table>";
            $tableemail .= "</tbody></table>";

            ?>
            <a href="javascript:void(0)" class="btn btn-xs  row-ticket " data-content="{{ $table}}" title="Row ticket">
                <i class="fa fa-envelope"></i>
            </a>

            <a href="javascript:void(0)" class="btn btn-xs " onclick="message_show(this);" data-content="{{ $tableemail}}" title="Resend Email">
                <i class="fa fa-repeat" aria-hidden="true"></i>

            </a>
            <button type="button" class="btn btn-xs  btn-delete-template no_pd" id="softdeletedata" data-id="{{$ticket->id}}" title="Delete template">
                <i class="fa fa-trash"></i></button>

            <button type="button" class="btn btn-xs  no_pd" onclick="showEmails('{{$ticket->id}}')" title="Show email">
                <i class="fa fa-envelope" ></i></button>

        </div>
    </td>
</tr>
@endforeach

{{ $data->links() }}
