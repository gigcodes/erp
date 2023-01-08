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
                        <?php echo Form::select("status_id", [''=>'']+\App\TicketStatuses::pluck("name", "id")->toArray(), request('status_id'), ["class" => "form-control globalSelect24", "id" => "status_id"]); ?>
                    </div>
                    <div class="col-md-2 pl-3 pr-0">
                        <div class='input-group date' id='filter_date'>
                            <input type='text' class="form-control" id="date" name="date" value="" />

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
                    <th style="width: 2%;"></th>
                    <th style="width: 4%;">Id</th>
                    <th style="width: 5%;">Source</th>
                    <th style="width: 5%;">Name</th>
                    <th style="width: 5%;">Email</th>
                    <th style="width: 5%;">Subject</th>
                    <th style="width: 6%;">Message</th>
                    <th style="width: 6%;">Asg name</th>
                    <th style="width: 5%;">Brand</th>
                    <th style="width: 5%;">Country</th>
                    <th style="width: 5%;">Ord no</th>
                    <th style="width: 6%;">Ph no</th>
                    <th style="width: 13%;">Msg Box</th>
                    <th style="width: 13%;">Resolution Date</th>
                    <th style="width: 6%;">Status</th>
                    <th style="width: 5%;">Created</th>
                    <th style="width: 12%;">Action</th>
                </tr>
                </thead>
                <tbody id="content_data" class="infinite-scroll-pending-inner">
                @include('livechat.partials.ticket-list')
                </tbody>
            </table>
        </div>


    </div>


    @include('livechat.partials.model-email')
    @include('livechat.partials.model-assigned')
    @include('livechat.partials.modal_ticket_send_option')



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

    $('.globalSelect21').val($('option:eq(1)').val()).trigger('change');
    $('.globalSelect22').val($('option:eq(1)').val()).trigger('change');
    $('.globalSelect23').val($('option:eq(1)').val()).trigger('change');
    $('.globalSelect24').val($('.globalSelect21 option:eq(1)').val()).trigger('change');
    $('.globalSelect25').val($('option:eq(1)').val()).trigger('change');
    $('.globalSelect26').val($('option:eq(1)').val()).trigger('change');

    $("#user_email option").each(function() {
        $(this).siblings('[value="'+ this.value +'"]').remove();
    });
    $("#term option").each(function() {
        $(this).siblings('[value="'+ this.value +'"]').remove();
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

        var page = 1;
        function getScrollTop() {
            return (window.pageYOffset !== undefined) ? window.pageYOffset : (document.documentElement || document.body.parentNode || document.body).scrollTop;
        }
        window.onscroll = function() {
            if (getScrollTop() < getDocumentHeight() - window.innerHeight) return;
            loadMore(++page);
        };

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
        function loadMore(page) {

            var url = "/livechat/tickets?page="+page;

            page = page + 1;
            $.ajax({
                url: url,
                type: 'GET',
                data: $('.form-search-data').serialize(),
                beforeSend:function(){
                        $('.infinite-scroll-products-loader').show();
                },
                success: function (data) {
                    if (data == '') {
                        $('.infinite-scroll-products-loader').hide();
                    }
                    $('.globalSelect2').select2();

                    $('.infinite-scroll-products-loader').hide();

                    $('.infinite-scroll-pending-inner').append(data.tbody);
                },
                error: function () {
                    $('.infinite-scroll-products-loader').hide();
                }
            });
        }

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
</script>
@endsection

