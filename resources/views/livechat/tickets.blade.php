<style>
    .tickets .btn-group-xs>.btn, .btn-xs {
        padding: 1px 2px !important;
        font-size: 15px !important;
    }
    .tickets .select2-container .select2-selection--single{
        height: 32px !important;
        border: 1px solid #ddd !important;
        color: #757575;
        padding-left: 6px;
    }
    .tickets .select2-container--default .select2-selection--single .select2-selection__arrow{
        height: 32px !important;
    }
    .tickets .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 32px !important;
        color: #757575;
    }
    .tickets>tbody>tr>td, .tickets>tbody>tr>th, .tickets>tfoot>tr>td, .tickets>tfoot>tr>th, .tickets>thead>tr>td, .tickets>thead>tr>th{
        padding: 6px !important;
    }

    .tickets .page-heading{
        font-size: 16px;
        text-align: left;
        margin-bottom: 10px;
        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        font-weight: 600;
    }
    .form-control{
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
    .row.tickets .form-group input{
        font-size: 13px;
        height: 32px;
    }
    .container.container-grow{
        padding:0 !important;
    }
    #quick-sidebar{
        padding-top:0 !important;
    }
    #quick-sidebar .fa-2x {
        font-size: 1.4em;
        margin-bottom: 0;
        height: auto !important;
    }
    #quick-sidebar {
        min-width: 35px !important;
        max-width: 30px !important;
        margin-left: 0px !important;
    }
    .container.container-grow {
        width: 100% !important;
        max-width: 100% !important;
    }
    .flex{
        display: flex;
    }
    .list-unstyled.components li{
        padding:8px 6px;
        border-bottom: 1px solid #ddd;
        cursor: pointer;
        text-align: center;
    }
    .list-unstyled.components li:hover{
        background: #dddddd78;
    }
    #quick-sidebar a{
        text-align: center;
    }
    .list-unstyled.components{
        border-top: 1px solid #ddd;
        border-right: 1px solid #ddd;
        margin-right: 10px;
        margin-left: 10px;
        border-left: 1px solid #ddd;
        border-radius: 4px;
    }
    .navbar-laravel{
        box-shadow: none !important;
    }
    .space-right{
        padding-right:10px;
        padding-left: 10px;
    }
    .row.tickets{
        font-size: 13px !important;
    }
    td{
        padding: 5px !important;
    }
    td a{
        color: #2f2f2f;
    }
    tbody td{
        background: #ddd3;
    }
    .select2-container .select2-search--inline .select2-search__field{
        margin-top: 0px !important;
    }
    .select2-search--inline {
    display: contents; /*this will make the container disappear, making the child the one who sets the width of the element*/
}

.select2-search__field:placeholder-shown {
    width: 100% !important; /*makes the placeholder to be 100% of the width while there are no options selected*/
}
</style>
@extends('layouts.app')

@section('content')

@php($users = $query = \App\User::get())

@include('partials.flash_messages')
    <div class="row tickets m-0">
        <div class="col-lg-12 margin-tb pr-0 pl-0">
        <h2 class="page-heading flex" style="padding: 8px 5px 8px 10px;border-bottom: 1px solid #ddd;line-height: 32px;">{{ (isset($title)) ? ucfirst($title) : "Tickets"}} (<span id="list_count">{{ $data->total() }}</span>)
                <div class="margin-tb" style="flex-grow: 1;">
                    <div class="pull-right ">
                        <button style="background: #fff;color: #757575;border: 1px solid #ccc;" type="button" class="btn btn-secondary mr-2" data-toggle="modal" data-target="#AddStatusModal">Add Status</button>

                    </div>
                </div>
            </h2>
        </div>
        <div class="col-lg-12 margin-tb pl-3">
            <div class="form-group mb-3">
                <div class="row">
                    <div class="col-md-2 pr-0 mb-3">
                        <select class="form-control globalSelect21"  name="users_id" id="users_id">
                           
                            @foreach($users as $key => $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 pl-3  pr-0">
                        <select class="form-control globalSelect22" name="ticket_id" id="ticket">
                            
                            @foreach($data as $key => $ticket)
                            <option value="{{ $ticket->ticket_id }}">{{ $ticket->ticket_id }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 pl-3 pr-0">
                        <?php echo Form::select("status_id", \App\TicketStatuses::pluck("name", "id")->toArray(), request('status_id'), ["class" => "form-control globalSelect24", "id" => "status_id"]); ?>
                    </div>
                    <div class="col-md-2 pl-3 pr-0">
                        <div class='input-group date' id='filter_date'>
                            <input placeholder="Select Date" type='text' class="form-control" id="date" name="date" value="" />

                            <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>

                    <div class="col-md-2 pl-3 pr-0">
                        <select class="form-control globalSelect23" name="term" id="term">
                            
                            @foreach($data as $key => $user_name)
                                <option value="{{ $user_name->name }}">{{ $user_name->name }}</option>
                            @endforeach
                        </select>
{{--                        <input name="term" type="text" class="form-control"--}}
{{--                                value="{{ isset($term) ? $term : '' }}"--}}
{{--                                placeholder="Name of User" id="term">--}}
                    </div>
                    <div class="col-md-2 pl-3 pr-0">
                        <select class="form-control globalSelect25" name="user_email" id="user_email">
                            
                            @foreach($data as $key => $user_email)
                                <option value="{{ $user_email->email }}">{{ $user_email->email }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 pl-3 pr-0">
                        <input name="user_message" type="text" class="form-control"
                                    placeholder="Search Message" id="user_message">
                    </div>
                    <div class="col-md-2 pl-3 pr-3">
                        <input name="serach_inquiry_type" type="text" class="form-control"
                                value="{{ isset($serach_inquiry_type) ? $serach_inquiry_type : '' }}"
                                placeholder="Inquiry Type" id="serach_inquiry_type">
                    </div>
                    <div class="col-md-2  pr-0">
                        <input name="search_country" type="text" class="form-control"
                                value="{{ isset($search_country) ? $search_country : '' }}"
                                placeholder="Country" id="search_country">
                    </div>
                    <div class="col-md-2 pl-3 pr-0">
                        <input name="search_order_no" type="text" class="form-control"
                                value="{{ isset($search_order_no) ? $search_order_no : '' }}"
                                placeholder="Order No." id="search_order_no">
                    </div>
                    <div class="col-md-2 pl-3 pr-0">
                        <input name="search_phone_no" type="text" class="form-control"
                                value="{{ isset($search_phone_no) ? $search_phone_no : '' }}"
                                placeholder="Phone No." id="search_phone_no">
                    </div>
                    <div class="col-md-2 pl-3 pr-0">
                        <input name="search_source" type="text" class="form-control"
                                value="{{ isset($search_source) ? $search_source : '' }}"
                                placeholder="Source." id="search_source">
                    </div>

                    <!-- <div class="col-md-2">
                        <input name="search_category" type="text" class="form-control"
                                value="{{ isset($search_category) ? $search_category : '' }}"
                                placeholder="Category" id="search_category">
                    </div> -->
                    <div>
                    <button type="button" class="btn btn-image mt-2" onclick="submitSearch()"><img src="{{ asset('images/filter.png')}}"/></button>
                    </div>
                    <div >
                        <button type="button" class="btn btn-image mt-2" id="resetFilter" onclick="resetSearch()"><img src="{{ asset('images/resend2.png')}}"/></button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-image mt-2" id="send-message"><img src="{{ asset('images/whatsapp-logo.png')}}"/></button>
                    </div>
                    <div>
                        <button class="btn btn-xs btn-secondary mt-2" style="color:white;" data-toggle="modal" data-target="#newStatusColor"> Status Color</button>
                    </div>
                </div>


            </div>

        </div>
{{--        <div class="col-lg-12 margin-tb">--}}
{{--            <div class="pull-right mt-3">--}}
{{--                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#AddStatusModal">Add Status</button>--}}
{{--                --}}
{{--            </div>--}}
{{--        </div>--}}
    </div>

    <div class="space-right infinite-scroll chat-list-table">

        <div class="table-responsive">
            <table class="table table-bordered" style="font-size: 14px;table-layout: fixed">
                <thead>
                <tr>
                    <th style="width: 5%;"></th>
                    <th style="width: 5%;">Id</th>
                    <th style="width: 6%;">Source</th>
                    <th style="width: 8%;">Name</th>
                    <th style="width: 8%;">Email</th>
                    <th class="chat-msg" style="width: 5%;">Subject</th>
                    <th class="chat-msg" style="width: 6%;">Message</th>
                    <th style="width: 6%;">Asg name</th>
                    <th class="chat-msg" style="width: 5%;">Brand</th>
                    <th class="chat-msg" style="width: 5%;">Country</th>
                    <th style="width: 5%;">Ord no</th>
                    <th style="width: 8%;">Ph no</th>
                    <th style="width: 16%;">Msg Box</th>
                    <th class="chat-msg" style="width: 13%;">Resolution Date</th>
                    <th style="width: 6%;">Status</th>
                    <th class="chat-msg" style="width: 6%;">Created</th>
                    <th class="chat-msg" style="width: 5%;">Action</th>
                </tr>
                </thead>
                <tbody id="content_data" class="infinite-scroll-pending-inner">
                @include('livechat.partials.ticket-list')
                </tbody>
            </table>
            <div id="pagination-container">
                {{ $data->links() }}
            </div>
        </div>


    </div>


    @include('livechat.partials.model-email')
    @include('livechat.partials.model-assigned')
    @include('livechat.partials.modal_ticket_send_option')
    @include('livechat.partials.modal-status-color')

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

    <div id="send-message-text-box" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
               <form action="{{ route('task.send-brodcast') }}" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="task_id" id="hidden-task-id" value="">
                    <div class="modal-header">
                        <h4 class="modal-title">Send Brodcast Message</h4>
                    </div>
                    <div class="modal-body" style="background-color: #999999;">
                        @csrf
                        <div class="form-group">
                            <label for="document">Message</label>
                            <textarea class="form-control message-for-brodcast" name="message" placeholder="Enter your message"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-send-brodcast-message">Send</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="model_email" class="modal fade" role="dialog">
        <div class="modal-dialog" style="width:1000px">
            <div class="modal-content"  style="width:1000px">
                <div class="modal-header">
                    <h4 class="modal-title">Email</h4>
                </div>
                <div class="modal-body" id="model_email_txt">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
              50% 50% no-repeat;display:none;">
    </div>

     <div id="ticketsEmails" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title">Emails sent</h4>
                    </div>
                    <div class="modal-body" >
                        <div class="table-responsive" style="margin-top:20px;">
                            <table class="table table-bordered text-nowrap" style="border: 1px solid #ddd;" id="email-table">
                                <thead>
                                  <tr>
                                    <th>Bulk <br> Action</th>
                                    <th>Date</th>
                                    <th>Sender</th>
                                    <th>Receiver</th>
                                    <th>Mail <br> Type</th>
                                    <th>Subject</th>
                                    <th>Body</th>
                                    <th>Status</th>
                                    <th>Draft</th>
                                    <th>Action</th>
                                  </tr>
                                </thead>
                                <tbody id="ticketEmailData">

                                </tbody>
                              </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
            </div>
        </div>
    </div>
<div id="viewMore" class="modal fade" role="dialog">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">View More</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <p><span id="more-content"></span> </p>
            </div>
        </div>
    </div>
</div>
<div id="viewMail" class="modal fade" role="dialog">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">View Email</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <p><strong>Subject : </strong> <span id="emailSubject"></span> </p>
              <p><strong>Message : </strong> <span id="emailMsg"></span> </p>
            </div>
        </div>
    </div>
</div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
$( document ).ready(function() {
    $(".globalSelect21").select2({
        multiple: true,
        placeholder: "Select Users",
    });
    $(".globalSelect22").select2({
        multiple: true,
        placeholder: "Select Ticket",
    });
    $(".globalSelect23").select2({
        multiple: true,
        placeholder: "Select User Name",
    });
    $(".globalSelect24").select2({
        multiple: true,
        placeholder: "Select Status",
    });
    $(".globalSelect25").select2({
        multiple: true,
        placeholder: "Select User Email",
    });
    $(".globalSelect26").select2({
        multiple: true,
        placeholder: "Select User Message",
    });

    /*$('.globalSelect21').val($('option:eq(1)').val()).trigger('change');
    $('.globalSelect22').val($('option:eq(1)').val()).trigger('change');
    $('.globalSelect23').val($('option:eq(1)').val()).trigger('change');
    $('.globalSelect24').val($('.globalSelect21 option:eq(1)').val()).trigger('change');
    $('.globalSelect25').val($('option:eq(1)').val()).trigger('change');
    $('.globalSelect26').val($('option:eq(1)').val()).trigger('change');*/

    $("#user_email option").each(function() {
        $(this).siblings('[value="'+ this.value +'"]').remove();
    });
    $("#term option").each(function() {
        $(this).siblings('[value="'+ this.value +'"]').remove();
    });
});

function opnMsg(email) {
      console.log(email);
      $('#emailSubject').html(email.subject);
      $('#emailMsg').html(email.message);

      // Mark email as seen as soon as its opened
      if(email.seen ==0 || email.seen=='0'){
        // Mark email as read
        var $this = $(this);
            $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: '/email/'+email.id+'/mark-as-read',
              type: 'put'
            }).done( function(response) {

            }).fail(function(errObj) {

            });
      }

    }


        function getDocumentHeight() {
            const body = document.body;
            const html = document.documentElement;

            return Math.max(
                body.scrollHeight, body.offsetHeight,
                html.clientHeight, html.scrollHeight, html.offsetHeight
            );
        };

        function showEmails(ticketId) {
            $('#ticketEmailData').html('');
            $.get(window.location.origin+"/tickets/emails/"+ticketId, function(data, status){
                $('#ticketEmailData').html(data);
                $('#ticketsEmails').modal('show');
            });
        }
        function opnModal(message){
          $(document).find('#more-content').html(message);
        }
        $(document).on('click', '.resend-email-btn', function(e) {
            e.preventDefault();
            var $this = $(this);
            var type = $(this).data('type');
            $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: '/email/resendMail/'+$this.data("id"),
              type: 'post',
              data: {
                type:type
              },
                beforeSend: function () {
                    $("#loading-image").show();
                },
            }).done( function(response) {
              toastr['success'](response.message);
              $("#loading-image").hide();
            }).fail(function(errObj) {
              $("#loading-image").hide();
            });
        });


    $(document).on('click', '.row-ticket', function () {
        /*DEVTASK-22731-START*/
        ticket_id = $(this).data('ticket-id');
        getTicketData(ticket_id);
        $('#viewmorechatmessages').modal("show");
        /*DEVTASK-22731-END*/
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

    /*DEVTASK-22731-START*/
    $(document).on('click', '.editTicket', function () {
        id = $(this).data('id');
        $("#spanMsg_"+id).css('display','none');
        $("#inputMsg_"+id).css('display','inline-block');
        $("#txtMsg_"+id).css('display','inline-block');

        $('#editTicket_'+id).css('display','none');
        $("#updateTicket_"+id).css('display','inline-block');
    });

    
    $(document).on('click', '.approveTicket', function () {
        ticket_id = $(this).data('ticket-id');
        id = $(this).data('id');
        src = "{{url('livechat/tickets/approve-ticket')}}";
        Swal.fire({
            title: 'Do you want to send message to ticket?',
                showDenyButton: true,
                showCancelButton: false,
                confirmButtonText: 'Yes',
                denyButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method : "POST",
                        url : src,
                        data : {"id":id,"ticket_id":ticket_id},
                        dataType : "json",
                        success : function(response){
                            getTicketData(ticket_id);
                        },error : function(error){
                            console.log(error);
                        }
                    });
                } else if (result.isDenied) {
                    
                }
            });
    });

    $(document).on('click', '.updateTicket', function () {
        ticket_id = $(this).data('ticket-id');
        message = $("#txtMsg_"+id).val();
        id = $(this).data('id');
        src = "{{url('livechat/tickets/update-ticket')}}";
        $.ajax({
            method : "POST",
            url : src,
            data : {"id":id,"ticket_id":ticket_id,"message":message},
            dataType : "json",
            success : function(response){
                getTicketData(ticket_id);
                $("#spanMsg_"+id).css('display','table-cell');
                $("#inputMsg_"+id).css('display','none');
                $('#editTicket_'+id).css('display','inline-block');
                $("#updateTicket_"+id).css('display','none');
            },error : function(error){
                console.log(error);
            }
        });
    });
    /*DEVTASK-22731-END*/

   $('#filter_date').datetimepicker({
        format: 'YYYY-MM-DD'
    });

   /*DEVTASK-22731-START*/
    function getTicketData(ticket_id){
        html = '';
        button = '';
        src = "{{url('livechat/tickets/ticket-data')}}";
        $.ajax({
            method : "POST",
            url : src,
            data : {"ticket_id":ticket_id},
            dataType : "json",
            success : function(response){
                if(response.count > 0) {
                    $("#viewmorechatmessages").find(".modal-dialog").css({"width":"1000px","max-width":"1000px"});
                    $("#viewmorechatmessages").find(".modal-body").css({"background-color":"white"});
                    $.each(response.data, function(k, v) {
                        button = '';

                        if(v['out'] == true) {
                            html += '<tr style="background-color:grey !important">';
                        } else {
                            html += '<tr style="background-color:#999999 !important">';
                        }
                        html += '<td style="width:50%"><span class="copy_message'+v['id']+'"" id="spanMsg_'+v['id']+'">'+v['message']+'</span> <p id="inputMsg_'+v['id']+'"><input type="text" style="display:none" id="txtMsg_'+v['id']+'" value="'+v['message']+'"></p></td>';

                        if (!v['approved'] && v['out'] == true) {

                            button += "<a href='javascript:void(0)' title='Edit Message' id='editTicket_"+v['id']+"' data-id='"+v['id']+"' class='btn btn-xs btn-secondary editTicket'><i class='fa fa-edit' aria-hidden='true'></i></a> ";

                            button += "<a href='javascript:void(0)' title='Update Message' style='display:none' id='updateTicket_"+v['id']+"' data-id='"+v['id']+"' data-ticket-id='"+ticket_id+"' class='btn btn-xs btn-secondary updateTicket'><i class='fa fa-save' aria-hidden='true'></i></a>";

                            button += "<a href='javascript:void(0)' id='approveTicket_"+v['id']+"' title='Send Message To Ticket' data-id='"+v['id']+"' data-ticket-id='"+ticket_id+"' class='btn btn-xs btn-secondary approveTicket'><i class='fa fa-check' aria-hidden='true'></i></a>";

                            button += "<button title='Approve' class='btn btn-xs btn-secondary btn-approve ml-3' data-messageid='" + v['id'] + "'><i class='fa fa-thumbs-up' aria-hidden='true'></i></button>";
                        }

                        if (v['status'] == 0 || v['status'] == 5 || v['status'] == 6) {

                            if (v['status'] == 0) {
                                button += "<a title='Mark as Read' href='javascript:;' data-url='/whatsapp/updatestatus?status=5&id=" + v['id'] + "' class='btn btn-xs btn-secondary ml-1 change_message_status'><i class='fa fa-check' aria-hidden='true'></i></a>";
                            }

                            if (v['status'] == 0 || v['status'] == 5) {
                                button += '<a href="javascript:;" style="padding:4px!important;" title="Mark as Replied" data-url="/whatsapp/updatestatus?status=6&id=' + v['id'] + '" class="btn btn-xs btn-secondary ml-1 change_message_status"> <img src="/images/2.png" /> </a>';
                            }

                            button += '&nbsp;<button title="forward"  class="btn btn-secondary btn-xs forward-btn" data-toggle="modal" data-target="#forwardModal" data-id="' + v['id'] + '"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>&nbsp;<button title="Resend" data-id="'+v['id']+'" class="btn btn-xs btn-secondary resend-message-js"><i class="fa fa-repeat" aria-hidden="true"></i></button>';

                        } else {
                            if (v['error_status'] == 1) {
                                button +="<a href='javascript:;' class='btn btn-xs btn-image fix-message-error' data-id='" + v['id'] + "'><img src='/images/flagged.png' /></a><a href='#' title='Resend' class='btn btn-xs btn-secondary ml-1 resend-message-js' data-id='" + v['id'] + "'><i class='fa fa-repeat' aria-hidden='true'></i></a>";
                            } else if (v['error_status'] == 2) {
                                button += "<a href='javascript:;' class='btn btn-xs btn-secondary btn-image fix-message-error' data-id='" + v['id'] + "'><img src='/images/flagged.png' /><img src='/images/flagged.png' /></a><a title='Resend' href='#' class='btn btn-xs btn-secondary ml-1 resend-message-js' data-id='" + v['id'] + "'><i class='fa fa-repeat' aria-hidden='true'></i></a>";
                            }

                            button += '&nbsp;<button title="Forward" class="btn btn-xs btn-secondary forward-btn" data-toggle="modal" data-target="#forwardModal" data-id="' + v['id'] + '"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>&nbsp;<button title="Resend" data-id="'+v['id']+'" class="btn btn-xs btn-secondary resend-message"><i class="fa fa-repeat" aria-hidden="true"></i></button>';
                        }

                        button += '<a title="Dialog" href="javascript:;" class="btn btn-xs btn-secondary ml-1 create-dialog"><i class="fa fa-plus" aria-hidden="true"></i></a>';

                        if(v['is_reviewed'] != 1) {
                            button += '&nbsp;<button title="Mark as reviewed" class="btn btn-xs btn-secondary review-btn" data-id="' + v['id'] + '"><i class="fa fa-check" aria-hidden="true"></i></button>&nbsp;';
                        }

                        button += '<a title="Add Sop" href="javascript:;" data-toggle="modal" data-target="#Create-Sop-Shortcut" class="btn btn-xs btn-secondary ml-1 create_short_cut" data-category="'+v['sop_category']+'" data-name="'+v['sop_name']+'" data-message="'+v['sop_content']+'" data-id="' + v['id'] + '" data-msg="'+v['message']+'"><i class="fa fa-asterisk" data-message="'+v['message']+'" aria-hidden="true"></i></a>';

                        button += '<a title="Remove" href="javascript:;" class="btn btn-xs btn-secondary ml-1 delete-message" data-id="' + v['id'] + '"><i class="fa fa-trash" aria-hidden="true"></i></a>';

                        button += '<a title="Copy Messages" href="javascript:;" class="btn btn-xs btn-secondary ml-1 btn-copy-messages" onclick="CopyToClipboard('+v['id']+')" data-message="'+v['message']+'" data-id="' + v['id'] + '"><i class="fa fa-copy" aria-hidden="true"></i></a>';

                        html += '<td style="width:30%">'+button+'</td>';

                        html += '<td style="width:40%">'+v['datetime']+'</td>';
                        html += '</tr>';

                    });
                } else {
                    html += '<tr>';
                    html += '<td colspan="4">No Records Found</td>';
                    html += '</tr>';
                }
                $("#ticketData").html(html);
            },error : function(error){
                console.log(error);
            }
        });
    }
    /*DEVTASK-22731-END*/

    function submitSearch(){
                //src = "{{url('whatsapp/pollTicketsCustomer')}}";
                src = "{{url('livechat/tickets')}}";
                term = $('#term').val();
                user_email = $('#user_email').val();
                user_message = $('#user_message').val();
                erp_user = 152;
                serach_inquiry_type = $('#serach_inquiry_type').val();
                search_country = $('#search_country').val();
                search_order_no = $('#search_order_no').val();
                search_phone_no = $('#search_phone_no').val();
                //search_category = $('#search_category').val();
                ticket_id = $('#ticket').val();
                status_id = $('#status_id').val();
                date = $('#date').val();
                users_id = $('#users_id').val();
                search_source = $('#search_source').val();
                $.ajax({
                    url: src,
                    dataType: "json",
                    data: {
                        erpUser:erp_user,
                        term : term,
                        user_email : user_email,
                        user_message : user_message,
                        serach_inquiry_type : serach_inquiry_type,
                        search_country : search_country,
                        search_order_no : search_order_no,
                        search_phone_no : search_phone_no,
                        ticket_id : ticket_id,
                        status_id : status_id,
                        date : date,
                        users_id : users_id,
                        search_source : search_source
                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    },
                }).done(function (message) {
                    $("#loading-image").hide();
                    //location.reload();
                    //alert(ticket_id);
                    $('#ticket').val(ticket_id);
                    $('#content_data').html(message.tbody);
                    var rendered = renderMessage(message, tobottom);
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });
    }

    function resetSearch(){
        $("#loading-image").hide();
        $('#term').val('');
        $('#serach_inquiry_type').val('');
        $('#search_country').val('');
        $('#search_order_no').val('');
        $('#search_phone_no').val('');
        $('#ticket').val('');
        $('#users_id').val('');
        location.reload();
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

        function changeDate(obj, ticket_id) {
            let id = ticket_id;
            let date = $(obj).val();
            let self = this;

            $.ajax({
                url: "{{ route('tickets.date.change')}}",
                method: "POST",
                data: {
                    id: id,
                    date: date
                },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function () {
                    toastr["success"]("Date updated!", "Message")
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
           if(message!=""){
               $("#message_confirm_text").html(message);
               $("#confirm_ticket_id").val(ticket_id);
               $("#confirm_message").val(message);
               $("#confirm_status").val(1);
               $("#confirmMessageModal").modal();
           }
       });
       $(document).on('click', '.confirm-messge-button', function () {
            var thiss = $(this);
            var data = new FormData();
        //    var ticket_id = $(this).data('ticketid');
        //    var message = $("#messageid_"+ticket_id).val();
            var ticket_id = $("#confirm_ticket_id").val();
            var message = $("#confirm_message").val();
            var status = $("#confirm_status").val();

            data.append("ticket_id", ticket_id);
            data.append("message", message);
            data.append("status", 1);

            var checkedValue = [];
            var i=0;
            $('.send_message_recepients:checked').each(function () {
                checkedValue[i++] = $(this).val();
            });
            data.append("send_ticket_options",checkedValue);
          //  alert(data);

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
                        $('#confirmMessageModal').modal('hide');
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


        $(document).on('click', '.send-message1_bk', function () {
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

      $(document).on("click","#send-message",function() {
         $("#send-message-text-box").modal("show");
      });

      $(".btn-send-brodcast-message").on("click",function () {

            var selected_tasks = [];

            $.each($(".selected-ticket-ids:checked"),function(k,v) {
                selected_tasks.push($(v).val());
            });

            if (selected_tasks.length > 0) {
                $.ajax({
                    type: "POST",
                    url: "{{ url('tickets/send-brodcast') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        selected_tasks: selected_tasks,
                        message : $(".message-for-brodcast").val()
                    },
                    beforeSend : function() {
                        $("#loading-image").show();
                    }
                }).done(function (response) {
                    $("#loading-image").hide();
                    if(response.code == 200) {
                        toastr["success"](response.message);
                        $("#send-message-text-box").modal("hide");
                    }else{
                        toastr["error"](response.message);
                    }
                }).fail(function (response) {
                    $("#loading-image").hide();
                    console.log(response);
                    toastr["error"]("Request has been failed due to the server , please contact administrator");
                });
            } else {
                $("#loading-image").hide();
                toastr["error"]("Please select atleast 1 task!");
            }
        });

</script>
<script>
    $(document).on("click","#softdeletedata",function() {

       var id = $(this).data("id");

       if(confirm('Do you really want to delete this record'))
       {
           $.ajax({
               type: "POST",
               url: "{{ url('tickets/delete_tickets/') }}",
               data: {
                   _token: "{{ csrf_token() }}",
                   id:id,
               },
               beforeSend : function() {
                   $("#loading-image").show();
               }
           }).done(function (response) {
               toastr["success"](response.message);
               $("#loading-image").hide();
               location.reload();

           }).fail(function (response) {
               $("#loading-image").hide();

           });
       }
   });
</script>
<script>
 function message_show(t)
 {
    $('#model_email_txt ').html($(t).data('content'));
    $("#model_email").modal("show");
 }

  $(document).on('click', '.resend-email-btn', function(e) {
      e.preventDefault();
      var $this = $(this);
      var type = $(this).data('type');
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '/email/resendMail/'+$this.data("id"),
          type: 'post',
          data: {
            type:type
          },
            beforeSend: function () {
                $("#loading-image").show();
            },
        }).done( function(response) {
          toastr['success'](response.message);
          $("#loading-image").hide();
        }).fail(function(errObj) {
          $("#loading-image").hide();
        });
    });
    /*DEVTASK-22731-START*/
    $(document).on('click', '.create_short_cut',function () {
            $('.sop_description').val("");
            let message = '';
            message = $(this).attr('data-msg');
            $('.sop_description').val(message);
        });
    /*DEVTASK-22731-START*/

    // It is used to collapse action menu on right side of table
    function Ticketsbtn(id){
        $(".action-ticketsbtn-tr-"+id).toggleClass('d-none')
    }

    // Add an event listener to the pagination links
    $(document).on('click', '#pagination-container .page-item .page-link', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        loadTickets(url);
    });


    function loadTickets(url) {
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            
            success: function(response) {
                $('#content_data').html(response.tbody);
                $('#pagination-container').html(response.links);
            },
            error: function(xhr, status, error) {
                alert('error')
            }
        });
    }

    // Load tickets on initial page load
    $(document).ready(function() {
        loadTickets('{{ Request::url() }}');
    });
</script>
@endsection

