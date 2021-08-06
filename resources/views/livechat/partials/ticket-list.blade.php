@php
    $isAdmin = Auth::user()->hasRole('Admin');
    $isHrm = Auth::user()->hasRole('HOD of CRM');
    $base_url = URL::to('/')
@endphp
@php($statusList = \App\TicketStatuses::all()->pluck('name','id'))

@foreach ($data as $key => $ticket)
<td>
  
</td>         
@endforeach
<div class="col-12 my-3" id="message-wrapper">
   <div class="table-responsive ">
      <table style="font-size:13.8px;" class="table table-bordered tickets" id="list-table" cellspacing=0 >
        <thead>
          <tr>
            <th>Sr. No.</th>
            <th>Ticket</th>
            <th>Source</th>
            <th>Name</th>
            <th>Email</th>
            <th>Subject</th>
            <th>Message</th>
            <th>Assigned To</th>
            <th>Inquiry Type</th>
            <th>Country</th>
            <th>Order No.</th>
            <th>Phone</th>
            <th style="width: 18%">Communication</th>
            <th style="width: 8%">Status</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody id="message-container">
            
        </tbody>
      </table>
    </div>
  
</div>
<div class="col-xs-12 text-center">
  <button type="button" id="load-more-messages" data-nextpage="1" class="btn btn-xs btn-secondary">Load More</button>
</div>
   
<script type="text/javascript">
            
var container = $("tbody#message-container");
            var suggestion_container = $("tbody#suggestion-container");
            // var sendBtn = $("#waMessageSend");
            var erpUser = "{{ Auth::id() }}";
            var addElapse = false;

            function errorHandler(error) {
                console.error("error occured: ", error);
            }

            function approveMessage(element, message) {
                if (!$(element).attr('disabled')) {
                    $.ajax({
                        type: "POST",
                        url: "/whatsapp/approve/user",
                        data: {
                            _token: "{{ csrf_token() }}",
                            messageId: message.id
                        },
                        beforeSend: function () {
                            $(element).attr('disabled', true);
                            $(element).text('Approving...');
                        }
                    }).done(function (data) {
                        element.remove();
                        console.log(data);
                    }).fail(function (response) {
                        $(element).attr('disabled', false);
                        $(element).text('Approve');

                        console.log(response);
                        alert(response.responseJSON.message);
                    });
                }
            }

            function renderMessage(message, tobottom = null) {
                //alert();
                var domId = "waMessage_" + message.id;
                var current = $("#" + domId);
                var is_admin = "{{ Auth::user()->hasRole('Admin') }}";
                var is_hod_crm = "{{ Auth::user()->hasRole('HOD of CRM') }}";
                var users_array = {!! json_encode($users) !!};
                var leads_assigned_user = "";

                if (current.get(0)) {
                    return false;
                }
                var i=1;
                var num = i++;
                // CHAT MESSAGES
                var text = $('<tr><td><input type="checkbox" class="selected-ticket-ids" name="ticket_ids[]" value='+message.id+'>&nbsp;</td><td>'+message.ticket_id+'</td><td><a href="javascript:void(0)" class="row-ticket" data-content="{{ $ticket->source_of_ticket }}">'+message.source_of_ticket+'</a></td><td>'+message.name+'</td><td>'+message.email+'</td><td>'+message.subject+'</td><td>'+message.message+'</td><td>'+message.assigned_to_name+'</td><td class="row-ticket" data-content="Brand : '+message.brand+'<br>Style : '+message.style+'<br>Keyword : '+message.keyword+'<br>Url : <a target=_blank href='+message.image+'>Click Here</a><br>"><a herf="javascript:;">'+message.type_of_inquiry+'</a></td><td>'+message.country+'</td><td>'+message.order_no+'</td><td>'+message.phone_no+'</td><td class="table-hover-cell pr-0 pb-0"><div style="display:flex;" class=" d-flex flex-row w-100 justify-content-between"><div style="flex-grow: 1"><textarea  style="height:37px;" class="form-control" id="messageid_{{ $ticket->id }}" name="message" placeholder="Message"></textarea></div><div style="width: min-content"><button class="btn btn-xs btn-image send-message1 " style="margin-left:6px;"data-ticketid="{{ $ticket->id }}"><img src="<?php echo $base_url;?>/images/filled-sent.png"/></button><button type="button" style="margin-left:6px;"class="btn btn-xs btn-image load-communication-modal" data-object="ticket"data-id="{{$ticket->id}}"><i class="fa fa-comments-o"></i></button></div></div></td><td><?php echo Form::select( "ticket_status_id",$statusList,$ticket->status_id,[ "class" => "resolve-issue border-0 globalSelect2","onchange" => "resolveIssue(this,".$ticket->id.")",]); ?></td>        <td><div class=" d-flex"><button type="button" class="btn btn-xs send-email-to-vender" data-subject="{{ $ticket->subject }}" data-message="{{ $ticket->message }}" data-email="{{ $ticket->email }}" data-id="{{$ticket->id}}"><i class="fa fa-envelope"></i></button><button type="button" class="btn btn-xs load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="ticket" data-id="{{$ticket->id}}" data-load-type="text" data-all="1" title="Load messages"><i class="fa fa-whatsapp"></i></button><button type="button" class="btn btn-xs btn-assigned-to-ticket" data-id="{{$ticket->id}}"><i class="fa fa-comments-o"></i></button><button type="button" class="btn btn-delete-template no_pd" id="softdeletedata" data-id="{{$ticket->id}}"><img width="15px" ml="5" src="/images/delete.png" style="margin-left:-12px;"></button></div></td></tr>');
               
                if(tobottom) 
                {
                    text.appendTo(container);
                } else {
                    
                    text.prependTo(container);
                }
                return true;
            }

            function pollMessages(page = null, tobottom = null, addElapse = null, isSearch = 0) {
                var qs = "";
                var serach_inquiry_type = $('#serach_inquiry_type').val();
                var search_country = $('#search_country').val();
                var search_order_no = $('#search_order_no').val();
                var search_phone_no = $('#search_phone_no').val();
                var ticket_id = $('#ticket').val();
                var status_id = $('#status_id').val();
                var date = $('#date').val();
                var users_id = $('#users_id').val();

                qs += "?erpUser=" + erpUser;
                if (page) {
                    qs += "&page=" + page;
                }
                if (addElapse) {
                    qs += "&elapse=3600";
                }
                if (serach_inquiry_type) {
                    qs += "&serach_inquiry_type="+serach_inquiry_type;
                }
                if (search_country) {
                    qs += "&search_country="+search_country;
                }
                if (search_order_no) {
                    qs += "&search_order_no="+search_order_no;
                }
                if (search_phone_no) {
                    qs += "&search_phone_no="+search_phone_no;
                }
                if (ticket_id) {
                    qs += "&ticket_id="+ticket_id;
                }
                if (status_id) {
                    qs += "&status_id="+status_id;
                }
                if (date) {
                    qs += "&date="+date;
                }
                if (users_id) {
                    qs += "&users_id="+users_id;
                }
                var anyNewMessages = false;

                return new Promise(function (resolve, reject) {
                    $.getJSON("/whatsapp/pollTicketsCustomer" + qs, function (data) {
                        if(isSearch==1)
                        {
                            container.html('');
                        }
                        data.data.forEach(function (message) {
                            var rendered = renderMessage(message, tobottom);

                            if (!anyNewMessages && rendered) {
                                anyNewMessages = true;
                            }
                        });

                        if (page) {
                            $('#load-more-messages').text('Load More');
                            can_load_more = true;
                        }

                        if (anyNewMessages) {
                            // scrollChatTop();
                            anyNewMessages = false;
                        }
                        if (!addElapse) {
                            addElapse = true; // load less messages now
                        }


                        resolve();
                    });

                });
            }

            function startPolling(isSearch=0) {
                //alert();
                setTimeout(function () {
                    pollMessages(null, null, addElapse,isSearch).then(function () {
                        //startPolling();
                    }, errorHandler);
                }, 1000);
            }

             $('a[href="#unassigned-tab"]').on('click', function () {
                startPolling();
            });

            var can_load_more = true;

            $('#message-wrapper').scroll(function () {
                var top = $('#message-wrapper').scrollTop();
                var document_height = $(document).height();
                var window_height = $('#message-container').height();

                console.log($('#message-wrapper').scrollTop());
                console.log($(document).height());
                console.log($('#message-container').height());

                // if (top >= (document_height - window_height - 200)) {
                if (top >= (window_height - 1500)) {
                    console.log('should load', can_load_more);
                    if (can_load_more) {
                        var current_page = $('#load-more-messages').data('nextpage');
                        $('#load-more-messages').data('nextpage', current_page + 1);
                        var next_page = $('#load-more-messages').data('nextpage');
                        console.log(next_page);
                        $('#load-more-messages').text('Loading...');

                        can_load_more = false;

                        pollMessages(next_page, true);
                    }
                }
            });

            $(document).on('click', '#load-more-messages', function () {
                var current_page = $(this).data('nextpage');
                $(this).data('nextpage', current_page + 1);
                var next_page = $(this).data('nextpage');
                $('#load-more-messages').text('Loading...');

                pollMessages(next_page, true);
            });

            

  $(document).ready(function () {
    //alert();
    startPolling();

            

        });
</script>