@extends('layouts.app')

@section('content')

@php($users = $query = \App\User::get())

@include('partials.flash_messages')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">{{ (isset($title)) ? ucfirst($title) : "Tickets"}} (<span id="list_count">{{ $data->total() }}</span>)  </h2>
            
            <div class="form-group">
                <div class="row">
                    <div class="col-md-2">
                        <select class="form-control" name="users_id" id="users_id">
                            <option value="">Select Users</option>
                            @foreach($users as $key => $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" name="ticket_id" id="ticket">
                            <option value="">Select Ticket</option>
                            @foreach($data as $key => $ticket)
                            <option value="{{ $ticket->ticket_id }}">{{ $ticket->ticket_id }}</option>
                            @endforeach
                        </select>
                    </div>
                        
                    <div class="col-md-2">
                        <?php echo Form::select("status_id",["" => "Select Status"] + \App\TicketStatuses::pluck("name","id")->toArray(),request('status_id'),["class" => "form-control", "id" => "status_id"]); ?>
                    </div>
                    <div class="col-md-2">
                        <div class='input-group date' id='filter_date'>
                            <input type='text' class="form-control" id="date" name="date" value="" />

                            <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                
                    <div class="col-md-2">
                        <input name="term" type="text" class="form-control"
                                value="{{ isset($term) ? $term : '' }}"
                                placeholder="Name of User" id="term">
                    </div>
                    <div class="col-md-2">
                        <input name="serach_inquiry_type" type="text" class="form-control"
                                value="{{ isset($serach_inquiry_type) ? $serach_inquiry_type : '' }}"
                                placeholder="Inquiry Type" id="serach_inquiry_type">
                    </div>
                    <div class="col-md-2">
                        <input name="search_country" type="text" class="form-control"
                                value="{{ isset($search_country) ? $search_country : '' }}"
                                placeholder="Country" id="search_country">
                    </div>
                    <div class="col-md-2">
                        <input name="search_order_no" type="text" class="form-control"
                                value="{{ isset($search_order_no) ? $search_order_no : '' }}"
                                placeholder="Order No." id="search_order_no">
                    </div>
                    <div class="col-md-2">
                        <input name="search_phone_no" type="text" class="form-control"
                                value="{{ isset($search_phone_no) ? $search_phone_no : '' }}"
                                placeholder="Phone No." id="search_phone_no">
                    </div>
                    <!-- <div class="col-md-2">
                        <input name="search_category" type="text" class="form-control"
                                value="{{ isset($search_category) ? $search_category : '' }}"
                                placeholder="Category" id="search_category">
                    </div> -->
                    <div class="col-md-1">
                        <button type="button" class="btn btn-image" onclick="submitSearch()"><img src="{{ asset('images/filter.png')}}"/></button>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img src="{{ asset('images/resend2.png')}}"/></button>    
                    </div>
                </div>

               
           
            </div>
           
        </div>
        <div class="col-lg-12 margin-tb">
            <div class="pull-right mt-3">
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#AddStatusModal">Add Status</button>
                
            </div>
        </div>
    </div>

    

    <div class="table-responsive mt-3">
      <table class="table table-bordered" id="list-table">
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
            <th>Communication</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
        @include('livechat.partials.ticket-list')

        </tbody>
      </table>
    </div>
    {!! $data->render() !!}

    @include('livechat.partials.model-email')
    @include('livechat.partials.model-assigned')


    <div id="AddStatusModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
            <div class="modal-content">
               <div class="modal-header">
                    <h4 class="modal-title">Add Status</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
               </div>

               <form action="{{ route('tickets.add.status') }}"  method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="">

                    <div class="modal-body">
                        <div class="form-group">
                            <strong>Status</strong>
                            <input type="text" name="name"  class="form-control"  required> 
                        </div>
                    </div>

                    <div class="modal-footer">
                         <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                         <button type="submit" class="btn btn-secondary">Send</button>
                    </div>
               </form>
          </div>

        </div>
    </div>

    <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  
 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  
<script type="text/javascript">

    $(document).on('click', '.row-ticket', function () {
        $('#viewmore #contentview').html($(this).data('content'));
        $('#viewmore').modal("show");
    });
        

   $(document).on('click', '.send-email-to-vender', function () {

       // console.log($(this).data('message'));
            $('#subject').val($(this).data('subject'));
            $('#to_email').val($(this).data('email'));
            $('#emailModal').find('form').find('input[name="ticket_id"]').val($(this).data('id'));
            $('#emailModal').modal("show");
        });
    $(document).on('click', '.btn-assigned-to-ticket', function () {

        $('#assignedModal').find('form').find('input[name="id"]').val($(this).data('id'));
        $('#assignedModal').modal("show");

    });

        
   $('#filter_date').datetimepicker({
        format: 'YYYY-MM-DD'
    });

    function submitSearch(){
        src = "{{url('livechat/tickets')}}";
        term = $('#term').val();
        serach_inquiry_type = $('#serach_inquiry_type').val();
        search_country = $('#search_country').val();
        search_order_no = $('#search_order_no').val();
        search_phone_no = $('#search_phone_no').val();
        //search_category = $('#search_category').val();
        ticket_id = $('#ticket').val();
        status_id = $('#status_id').val();
        date = $('#date').val();
        users_id = $('#users_id').val();
        $.ajax({
            url: src,
            dataType: "json",
            data: {
                term : term,
                serach_inquiry_type : serach_inquiry_type,
                search_country : search_country,
                search_order_no : search_order_no,
                search_phone_no : search_phone_no,
                //search_category : search_category,
                ticket_id : ticket_id,
                status_id : status_id,
                date : date,
                users_id : users_id,

            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $("#list-table tbody").empty().html(data.tbody);
            $("#list_count").text(data.count);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
        
    }

    function resetSearch(){
      src = "{{url('livechat/tickets')}}";
        blank = '';
        $.ajax({
            url: src,
            dataType: "json",
            data: {
               
               blank : blank, 

            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $('#term').val('')
            $('#serach_inquiry_type').val('')
            $('#search_country').val('')
            $('#search_order_no').val('')
            $('#search_phone_no').val('')
            //$('#search_category').val()
            $('#ticket').val('')
            $("#list-table tbody").empty().html(data.tbody);
            $("#list_count").text(data.count);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
    }


    $(document).on('click', '.add-cc', function (e) {
            e.preventDefault();

            if ($('#cc-label').is(':hidden')) {
                $('#cc-label').fadeIn();
            }

            var el = `<div class="row cc-input">
            <div class="col-md-10">
                <input type="text" name="cc[]" class="form-control mb-3">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-image cc-delete-button"><img src="{{asset('images/delete.png')}}"></button>
            </div>
        </div>`;

            $('#cc-list').append(el);
        });

        $(document).on('click', '.cc-delete-button', function (e) {
            e.preventDefault();
            var parent = $(this).parent().parent();

            parent.hide(300, function () {
                parent.remove();
                var n = 0;

                $('.cc-input').each(function () {
                    n++;
                });

                if (n == 0) {
                    $('#cc-label').fadeOut();
                }
            });
        });

        // bcc

        $(document).on('click', '.add-bcc', function (e) {
            e.preventDefault();

            if ($('#bcc-label').is(':hidden')) {
                $('#bcc-label').fadeIn();
            }

            var el = `<div class="row bcc-input">
            <div class="col-md-10">
                <input type="text" name="bcc[]" class="form-control mb-3">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-image bcc-delete-button"><img src="{{asset('images/delete.png')}}"></button>
            </div>
        </div>`;

            $('#bcc-list').append(el);
        });

        $(document).on('click', '.bcc-delete-button', function (e) {
            e.preventDefault();
            var parent = $(this).parent().parent();

            parent.hide(300, function () {
                parent.remove();
                var n = 0;

                $('.bcc-input').each(function () {
                    n++;
                });

                if (n == 0) {
                    $('#bcc-label').fadeOut();
                }
            });
        });

        function resolveIssue(obj, task_id) {
            let id = task_id;
            let status = $(obj).val();
            let self = this;
            

            $.ajax({
                url: "{{ route('tickets.status.change')}}",
                method: "POST",
                data: {
                    id: id,
                    status: status
                },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function () {
                    toastr["success"]("Status updated!", "Message")
                },
                error: function (error) {
                    toastr["error"](error.responseJSON.message);
                }
            });
        }

        $(document).on('click', '.send-message1', function () {
            var thiss = $(this);
            var data = new FormData();
            var ticket_id = $(this).data('ticketid');
            var message = $("#messageid_"+ticket_id).val();
            data.append("ticket_id", ticket_id);
            data.append("message", message);
            data.append("status", 1);
            if (message.length > 0) {
                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        url: BASE_URL+'/whatsapp/sendMessage/ticket',
                        type: 'POST',
                        "dataType": 'json',           // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function () {
                            $(thiss).attr('disabled', true);
                        }
                    }).done(function (response) {
                        //thiss.closest('tr').find('.message-chat-txt').html(thiss.siblings('textarea').val());
                        if(message.length > 30)
                        {
                            var res_msg = message.substr(0, 27)+"..."; 
                            $("#message-chat-txt-"+ticket_id).html(res_msg);
                            $("#message-chat-fulltxt-"+ticket_id).html(message);    
                        }
                        else
                        {
                            $("#message-chat-txt-"+ticket_id).html(message); 
                            $("#message-chat-fulltxt-"+ticket_id).html(message);      
                        }
                        
                        $("#messageid_"+ticket_id).val('');
                        
                        $(thiss).attr('disabled', false);
                    }).fail(function (errObj) {
                        $(thiss).attr('disabled', false);

                        alert("Could not send message");
                        console.log(errObj);
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });
</script>

@endsection

