@extends('layouts.app')

@section('title', 'Task Show')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css">
@endsection

@section('content')

<div class="row">
  <div class="col-lg-12 margin-tb">
    <div class="pull-left d-flex">
      <h3>{{ $task->is_statutory == 3 ? 'Appointment' : ($task->is_statutory == 1 ? 'Statutory' : '') }} Task Page</h3>

      @if ($task->users->contains(Auth::id()) || $task->assign_from == Auth::id())
        @if ($task->is_completed == '')
          <button type="button" class="btn btn-image task-complete mt-3" data-id="{{ $task->id }}"><img src="/images/incomplete.png" /></button>
        @else
          @if ($task->assign_from == Auth::id())
            <button type="button" class="btn btn-image task-complete mt-3" data-id="{{ $task->id }}"><img src="/images/completed-green.png" /></button>
          @else
            <button type="button" class="btn btn-image mt-3"><img src="/images/completed-green.png" /></button>
          @endif
        @endif
      @endif

      @if ($task->assign_to == Auth::id())
        @if ($task->is_private == 1)
          <button type="button" class="btn btn-image make-private-task mt-3" data-taskid="{{ $task->id }}"><img src="/images/private.png" /></button>
        @else
          <button type="button" class="btn btn-image make-private-task mt-3" data-taskid="{{ $task->id }}"><img src="/images/not-private.png" /></button>
        @endif
      @endif

      @if ($task->is_watched == 1)
        <button type="button" class="btn btn-image make-watched-task mt-3" data-taskid="{{ $task->id }}"><img src="/images/starred.png" /></button>
      @else
        <button type="button" class="btn btn-image make-watched-task mt-3" data-taskid="{{ $task->id }}"><img src="/images/unstarred.png" /></button>
      @endif

      @if ($task->is_flagged == 1)
        <button type="button" class="btn btn-image flag-task mt-3" data-id="{{ $task->id }}"><img src="/images/flagged.png" /></button>
      @else
        <button type="button" class="btn btn-image flag-task mt-3" data-id="{{ $task->id }}"><img src="/images/unflagged.png" /></button>
      @endif
    </div>
    <div class="pull-right mt-4">
      {{-- <a class="btn btn-xs btn-secondary" href="{{ route('customer.index') }}">Back</a>
      <a class="btn btn-xs btn-secondary" href="#" id="quick_add_lead">+ Lead</a>
      <a class="btn btn-xs btn-secondary" href="#" id="quick_add_order">+ Order</a>
      <button type="button" class="btn btn-xs btn-secondary" data-toggle="modal" data-target="#privateViewingModal">Set Up for Private Viewing</button> --}}
    </div>
  </div>
</div>

@include('partials.flash_messages')

<div class="row">
  <div class="col-xs-12 col-md-4 py-3 border">
    <div class="row text-muted">
      <div class="col-6">
        <div class="form-group">
          {{ Carbon\Carbon::parse($task->created_at)->format('d-m H:i') }}
        </div>
      </div>

      <div class="col-6">
        <div class="form-group">
          {{-- <select class="form-control input-sm" id="task_category" name="category">
            <option value="">Select a Category</option>

            @foreach ($categories as $id => $category)
              <option value="{{ $id }}" {{ $id == $task->category ? 'selected' : '' }}>{{ $category }}</option>
            @endforeach
          </select> --}}
          {!! $categories !!}

          <span class="text-success change_status_message" style="display: none;">Successfully changed category</span>
        </div>
      </div>
    </div>

    @if ($task->is_statutory == 1)
      <div class="form-group">
        <strong>Recurring:</strong>
        {{ $task->recurring_type }}
      </div>
    @endif

    <div class="form-group">
      @if ($task->task_subject)
        <strong class="task-subject">{{ $task->task_subject }}</strong>
      @endif

      <input type="text" name="subject" id="task_subject_field" class="form-control input-sm hidden" value="{{ $task->task_subject }}">

      <a href="#" id="edit_subject_button" class="btn-link">Edit</a>
    </div>

    <div class="form-group">
      {{ $task->task_details }}
    </div>

    @if ($task->is_statutory == 3)
      <div class="form-group">
        <ul class="list-group">
          <div id="note-list-container">
            @foreach ($task->notes as $note)
              <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="">
                  {{ $note->remark }}

                  <ul class="pl-2">
                    @foreach ($note->subnotes as $subnote)
                      <li class="d-flex justify-content-between align-items-center">
                        {{ $subnote->remark }}

                        <button type="button" class="btn btn-image create-quick-task-button" data-remark="{{ $subnote->remark }}"><img src="/images/add.png" /></button>
                      </li>
                    @endforeach
                  </ul>

                  <input type="text" class="form-control input-sm create-subnote" data-id="{{ $note->id }}" name="note" placeholder="Note" value="">
                </div>

                <button type="button" class="btn btn-image create-quick-task-button" data-remark="{{ $note->remark }}"><img src="/images/add.png" /></button>
              </li>
            @endforeach
          </div>

          <li class="list-group-item">
            <input type="text" id="create-note-field" class="form-control input-sm" name="note" placeholder="Note" value="">
          </li>
        </ul>
      </div>
    @endif



    {{-- <div class="form-group">
      {{ Carbon\Carbon::parse($task->completion_date)->format('d-m H:i') }}
    </div> --}}

    <div class="form-group">
      <strong>Assigned from:</strong> {{ array_key_exists($task->assign_from, $users_array) ? $users_array[$task->assign_from] : 'User Does Not Exist' }}
    </div>

    <div class="form-group">
      <strong>Assigned to:</strong>
      @foreach ($task->users as $key => $user)
        @if ($key != 0)
          ,
        @endif

        @if (array_key_exists($user->id, $users_array))
          @if ($user->id == Auth::id())
            <a href="{{ route('users.show', $user->id) }}">{{ $users_array[$user->id] }}</a>
          @else
            {{ $users_array[$user->id] }}
          @endif
        @else
          User Does Not Exist
        @endif
      @endforeach

      <br>

      @foreach ($task->contacts as $key => $contact)
        @if ($key != 0)
          ,
        @endif

        {{ $contact->name }} - {{ $contact->phone }} ({{ ucwords($contact->category) }})
      @endforeach
    </div>

    <form action="{{ route('task.update', $task->id) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="form-group">
        <strong>Assigned To (users):</strong>
        <select class="selectpicker form-control input-sm" data-live-search="true" data-size="15" name="assign_to[]" id="first_customer" title="Choose a User" multiple>
          @foreach ($users as $user)
            <option data-tokens="{{ $user->id }} {{ $user->name }}" value="{{ $user->id }}" {{ $task->users->contains($user) ? 'selected' : '' }}>{{ $user->name }}</option>
          @endforeach
        </select>

        @if ($errors->has('assign_to'))
          <div class="alert alert-danger">{{$errors->first('assign_to')}}</div>
        @endif
      </div>

      <div class="form-group">
        <strong>Assigned To (contacts):</strong>
        <select class="selectpicker form-control input-sm" data-live-search="true" data-size="15" name="assign_to_contacts[]" title="Choose a Contact" multiple>
          @foreach (Auth::user()->contacts as $contact)
            <option data-tokens="{{ $contact['name'] }} {{ $contact['phone'] }} {{ $contact['category'] }}" value="{{ $contact['id'] }}" {{ $task->contacts->contains($contact) ? "selected" : '' }}>{{ $contact['name'] }} - {{ $contact['phone'] }} ({{ $contact['category'] }})</option>
          @endforeach
        </select>

        @if ($errors->has('assign_to_contacts'))
          <div class="alert alert-danger">{{$errors->first('assign_to_contacts')}}</div>
        @endif
      </div>

      <button type="submit" class="btn btn-xs btn-secondary">Update</button>
    </form>
  </div>

  <div class="col-xs-12 col-md-4 mb-3">
    <div class="border">
      <form action="{{ route('whatsapp.send', 'task') }}" method="POST" enctype="multipart/form-data">
        <div class="d-flex">
          @csrf

          <div class="form-group">
            <div class="upload-btn-wrapper btn-group pr-0 d-flex">
              <button class="btn btn-image px-1"><img src="/images/upload.png" /></button>
              <input type="file" name="image" />

              <button type="submit" class="btn btn-image px-1 send-communication received-customer"><img src="/images/filled-sent.png" /></button>
            </div>
          </div>

          <div class="form-group flex-fill mr-3">
            <button type="button" id="customerMessageButton" class="btn btn-image"><img src="/images/support.png" /></button>
            <textarea  class="form-control mb-3 hidden" style="height: 110px;" name="body" placeholder="Received from User"></textarea>
            <input type="hidden" name="status" value="0" />
          </div>

          {{-- <div class="form-group">
            <div class="upload-btn-wrapper">
              <button class="btn btn-image px-1"><img src="/images/upload.png" /></button>
              <input type="file" name="image" />
            </div>
          </div> --}}
        </div>

      </form>

      <form action="{{ route('whatsapp.send', 'task') }}" method="POST" enctype="multipart/form-data">
        <div id="paste-container" style="width: 200px;">

        </div>

        <div class="d-flex">
          @csrf

          <div class="form-group">
            <div class=" d-flex flex-column">
              <div class="">
                <div class="upload-btn-wrapper btn-group px-0">
                  <button class="btn btn-image px-1"><img src="/images/upload.png" /></button>
                  <input type="file" name="image" />

                </div>
                <button type="submit" class="btn btn-image px-1 send-communication"><img src="/images/filled-sent.png" /></button>

              </div>

              <div class="">
                {{-- <a href="{{ route('attachImages', ['customer', $customer->id, 1]) }}" class="btn btn-image px-1"><img src="/images/attach.png" /></a> --}}


                {{-- <button type="button" class="btn btn-image px-1" data-toggle="modal" data-target="#suggestionModal"><img src="/images/customer-suggestion.png" /></button> --}}
              </div>
            </div>
          </div>

          <div class="form-group flex-fill mr-3">
            <textarea id="message-body" class="form-control mb-3" style="height: 110px;" name="body" placeholder="Send for approval"></textarea>

            <input type="hidden" name="screenshot_path" value="" id="screenshot_path" />
            <input type="hidden" name="status" value="1" />

            <div class="paste-container"></div>


          </div>

        </div>

        {{-- <div class="pb-4 mt-3">
          <div class="row">
            <div class="col">
              <select name="quickCategory" id="quickCategory" class="form-control input-sm mb-3">
                <option value="">Select Category</option>
                @foreach($reply_categories as $category)
                  <option value="{{ $category->approval_leads }}">{{ $category->name }}</option>
                @endforeach
              </select>

              <select name="quickComment" id="quickComment" class="form-control input-sm">
                <option value="">Quick Reply</option>
              </select>
            </div>
            <div class="col">
              <button type="button" class="btn btn-xs btn-secondary" data-toggle="modal" data-target="#ReplyModal" id="approval_reply">Create Quick Reply</button>
            </div>
          </div>
        </div> --}}

      </form>

    </div>
  </div>

  <div class="col-xs-12 col-md-4">
    <div class="border">
      {{-- <h4>Messages</h4> --}}

      <div class="row">
        <div class="col-12 my-3" id="message-wrapper">
          <div id="message-container"></div>
        </div>

        <div class="col-xs-12 text-center hidden">
          <button type="button" id="load-more-messages" data-nextpage="1" class="btn btn-secondary">Load More</button>
        </div>
      </div>
    </div>
  </div>
</div>

@include('task-module.partials.modal-reminder')

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.2.0/socket.io.js"></script>

  <script type="text/javascript">
  jQuery(document).ready(function( $ ) {
    $('audio').on("play", function (me) {
      $('audio').each(function (i,e) {
        if (e !== me.currentTarget) {
          this.pause();
        }
      });
    });

    $('.dropify').dropify();
  })

  var selected_product_images = [];

  $(document).on('click', '.select-product-image', function() {
    var checked = $(this).prop('checked');
    var id = $(this).data('id');

    if (checked) {
      selected_product_images.push(id);
    } else {
      var index = selected_product_images.indexOf(id);

      selected_product_images.splice(index, 1);
    }

    console.log(selected_product_images);
  });

    $('#date, #report-completion-datetime, #reminder-datetime').datetimepicker({
      format: 'YYYY-MM-DD HH:mm'
    });


        $(document).on('click', '.add-product-button', function() {
          $('input[name="order_id"]').val($(this).data('orderid'));
        });

        $(document).on('click', ".collapsible-message", function() {
          var selection = window.getSelection();
          if (selection.toString().length === 0) {
            var short_message = $(this).data('messageshort');
            var message = $(this).data('message');
            var status = $(this).data('expanded');

            if (status == false) {
              $(this).addClass('expanded');
              $(this).html(message);
              $(this).data('expanded', true);
              // $(this).siblings('.thumbnail-wrapper').remove();
              $(this).closest('.talktext').find('.message-img').removeClass('thumbnail-200');
              $(this).closest('.talktext').find('.message-img').parent().css('width', 'auto');
            } else {
              $(this).removeClass('expanded');
              $(this).html(short_message);
              $(this).data('expanded', false);
              $(this).closest('.talktext').find('.message-img').addClass('thumbnail-200');
              $(this).closest('.talktext').find('.message-img').parent().css('width', '200px');
            }
          }
        });

        $(document).ready(function() {
        var container = $("div#message-container");
        var suggestion_container = $("div#suggestion-container");
        // var sendBtn = $("#waMessageSend");
        var taskId = "{{ $task->id }}";
             var addElapse = false;
             function errorHandler(error) {
                 console.error("error occured: " , error);
             }
             function approveMessage(element, message) {
               if (!$(element).attr('disabled')) {
                 $.ajax({
                   type: "POST",
                   url: "/whatsapp/approve/task",
                   data: {
                     _token: "{{ csrf_token() }}",
                     messageId: message.id
                   },
                   beforeSend: function() {
                     $(element).attr('disabled', true);
                     $(element).text('Approving...');
                   }
                 }).done(function( data ) {
                   element.remove();
                   console.log(data);
                 }).fail(function(response) {
                   $(element).attr('disabled', false);
                   $(element).text('Approve');

                   console.log(response);
                   alert(response.responseJSON.message);
                 });
               }
             }

             // function createMessageArgs() {
             //      var data = new FormData();
             //     var text = $("#waNewMessage").val();
             //     var files = $("#waMessageMedia").prop("files");
             //     var text = $("#waNewMessage").val();
             //
             //     data.append("customer_id", customerId);
             //     if (files && files.length>0){
             //         for ( var i = 0; i != files.length; i ++ ) {
             //           data.append("media[]", files[ i ]);
             //         }
             //         return data;
             //     }
             //     if (text !== "") {
             //         data.append("message", text);
             //         return data;
             //     }
             //
             //     alert("please enter a message or attach media");
             //   }

        function renderMessage(message, tobottom = null) {
            var domId = "waMessage_" + message.id;
            var current = $("#" + domId);
            var is_admin = "{{ Auth::user()->hasRole('Admin') }}";
            var is_hod_crm = "{{ Auth::user()->hasRole('HOD of CRM') }}";
            var users_array = {!! json_encode($users_array) !!};
            var leads_assigned_user = "";

            if ( current.get( 0 ) ) {
              return false;
            }

             // if (message.body) {
             //
             //   var text = $("<div class='talktext'></div>");
             //   var p = $("<p class='collapsible-message'></p>");
             //
             //   if ((message.body).indexOf('<br>') !== -1) {
             //     var splitted = message.body.split('<br>');
             //     var short_message = splitted[0].length > 150 ? (splitted[0].substring(0, 147) + '...<br>' + splitted[1]) : message.body;
             //     var long_message = message.body;
             //   } else {
             //     var short_message = message.body.length > 150 ? (message.body.substring(0, 147) + '...') : message.body;
             //     var long_message = message.body;
             //   }
             //
             //   var images = '';
             //   var has_product_image = false;
             //
             //   if (message.images !== null) {
             //     message.images.forEach(function (image) {
             //       images += image.product_id !== '' ? '<a href="/products/' + image.product_id + '" data-toggle="tooltip" data-html="true" data-placement="top" title="<strong>Special Price: </strong>' + image.special_price + '<br><strong>Size: </strong>' + image.size + '<br><strong>Supplier: </strong>' + image.supplier_initials + '">' : '';
             //       images += '<div class="thumbnail-wrapper"><img src="' + image.image + '" class="message-img thumbnail-200" /><span class="thumbnail-delete" data-image="' + image.key + '">x</span></div>';
             //       images += image.product_id !== '' ? '<input type="checkbox" name="product" class="d-block mx-auto select-product-image" data-id="' + image.product_id + '" /></a>' : '';
             //
             //       if (image.product_id !== '') {
             //         has_product_image = true;
             //       }
             //     });
             //     images += '<br>';
             //   }
             //
             //   p.attr("data-messageshort", short_message);
             //   p.attr("data-message", long_message);
             //   p.attr("data-expanded", "false");
             //   p.attr("data-messageid", message.id);
             //   p.html(short_message);
             //
             //   if (message.status == 0 || message.status == 5 || message.status == 6) {
             //     var row = $("<div class='talk-bubble'></div>");
             //
             //     var meta = $("<em>Customer " + moment(message.created_at).format('DD-MM H:mm') + " </em>");
             //     var mark_read = $("<a href data-url='/message/updatestatus?status=5&id=" + message.id + "&moduleid=" + message.moduleid + "&moduletype=leads' style='font-size: 9px' class='change_message_status'>Mark as Read </a><span> | </span>");
             //     var mark_replied = $('<a href data-url="/message/updatestatus?status=6&id=' + message.id + '&moduleid=' + message.moduleid + '&moduletype=leads" style="font-size: 9px" class="change_message_status">Mark as Replied </a>');
             //
             //     row.attr("id", domId);
             //
             //     p.appendTo(text);
             //     $(images).appendTo(text);
             //     meta.appendTo(text);
             //
             //     if (message.status == 0) {
             //       mark_read.appendTo(meta);
             //     }
             //     if (message.status == 0 || message.status == 5) {
             //       mark_replied.appendTo(meta);
             //     }
             //
             //     text.appendTo(row);
             //
             //     if (tobottom) {
             //       row.appendTo(container);
             //     } else {
             //       row.prependTo(container);
             //     }
             //
             //   } else if (message.status == 4) {
             //     var row = $("<div class='talk-bubble' data-messageid='" + message.id + "'></div>");
             //     var chat_friend =  (message.assigned_to != 0 && message.assigned_to != leads_assigned_user && message.userid != message.assigned_to) ? ' - ' + users_array[message.assigned_to] : '';
             //     var meta = $("<em>" + users_array[message.userid] + " " + chat_friend + " " + moment(message.created_at).format('DD-MM H:mm') + " <img id='status_img_" + message.id + "' src='/images/1.png' /> &nbsp;</em>");
             //
             //     row.attr("id", domId);
             //
             //     p.appendTo(text);
             //     $(images).appendTo(text);
             //     meta.appendTo(text);
             //
             //     text.appendTo(row);
             //     if (tobottom) {
             //       row.appendTo(container);
             //     } else {
             //       row.prependTo(container);
             //     }
             //   } else { // APPROVAL MESSAGE
             //     var row = $("<div class='talk-bubble' data-messageid='" + message.id + "'></div>");
             //     var body = $("<span id='message_body_" + message.id + "'></span>");
             //     var edit_field = $('<textarea name="message_body" rows="8" class="form-control" id="edit-message-textarea' + message.id + '" style="display: none;">' + message.body + '</textarea>');
             //     var meta = "<em>" + users_array[message.userid] + " " + moment(message.created_at).format('DD-MM H:mm') + " <img id='status_img_" + message.id + "' src='/images/" + message.status + ".png' /> &nbsp;";
             //
             //     if (message.status == 2 && is_admin == false) {
             //       meta += '<a href data-url="/message/updatestatus?status=3&id=' + message.id + '&moduleid=' + message.moduleid + '&moduletype=leads" style="font-size: 9px" class="change_message_status">Mark as sent </a>';
             //     }
             //
             //     if (message.status == 1 && (is_admin == true || is_hod_crm == true)) {
             //       meta += '<a href data-url="/message/updatestatus?status=2&id=' + message.id + '&moduleid=' + message.moduleid + '&moduletype=leads" style="font-size: 9px" class="change_message_status wa_send_message" data-messageid="' + message.id + '">Approve</a>';
             //       meta += ' <a href="#" style="font-size: 9px" class="edit-message" data-messageid="' + message.id + '">Edit</a>';
             //     }
             //
             //     if (has_product_image) {
             //       meta += '<a href="#" class="btn btn-xs btn-secondary ml-1 create-product-lead">+ Lead</a>';
             //       meta += '<a href="#" class="btn btn-xs btn-secondary ml-1 create-product-order">+ Order</a>';
             //     }
             //
             //     meta += "</em>";
             //     var meta_content = $(meta);
             //
             //
             //
             //     row.attr("id", domId);
             //
             //     p.appendTo(body);
             //     body.appendTo(text);
             //     edit_field.appendTo(text);
             //     $(images).appendTo(text);
             //     meta_content.appendTo(text);
             //
             //     if (message.status == 2 && is_admin == false) {
             //       var copy_button = $('<button class="copy-button btn btn-secondary" data-id="' + message.id + '" moduleid="' + message.moduleid + '" moduletype="orders" data-message="' + message.body + '"> Copy message </button>');
             //       copy_button.appendTo(text);
             //     }
             //
             //
             //     text.appendTo(row);
             //
             //     if (tobottom) {
             //       row.appendTo(container);
             //     } else {
             //       row.prependTo(container);
             //     }
             //   }
             // }
             // else {
               // CHAT MESSAGES
               var row = $("<div class='talk-bubble'></div>");
               var body = $("<span id='message_body_" + message.id + "'></span>");
               var text = $("<div class='talktext'></div>");
               var edit_field = $('<textarea name="message_body" rows="8" class="form-control" id="edit-message-textarea' + message.id + '" style="display: none;">' + message.message + '</textarea>');
               var p = $("<p class='collapsible-message'></p>");

               var forward = $('<button class="btn btn-image forward-btn" data-toggle="modal" data-target="#forwardModal" data-id="' + message.id + '"><img src="/images/forward.png" /></button>');



               if (message.status == 0 || message.status == 5 || message.status == 6) {
                 var meta = $("<em>" + users_array[message.user_id] + " " + moment(message.created_at).format('DD-MM H:mm') + " </em>");
                 var mark_read = $("<a href data-url='/whatsapp/updatestatus?status=5&id=" + message.id + "' style='font-size: 9px' class='change_message_status'>Mark as Read </a><span> | </span>");
                 var mark_replied = $('<a href data-url="/whatsapp/updatestatus?status=6&id=' + message.id + '" style="font-size: 9px" class="change_message_status">Mark as Replied </a>');

                 // row.attr("id", domId);
                 p.appendTo(text);

                 // $(images).appendTo(text);
                 meta.appendTo(text);

                 if (message.status == 0) {
                   mark_read.appendTo(meta);
                 }

                 if (message.status == 0 || message.status == 5) {
                   mark_replied.appendTo(meta);
                 }

                 text.appendTo(row);

                 if (tobottom) {
                   row.appendTo(container);
                 } else {
                   row.prependTo(container);
                 }

                 forward.appendTo(meta);

               } else if (message.status == 4) {
                 var row = $("<div class='talk-bubble' data-messageid='" + message.id + "'></div>");
                 var chat_friend =  (message.assigned_to != 0 && message.assigned_to != leads_assigned_user && message.user_id != message.assigned_to) ? ' - ' + users_array[message.assigned_to] : '';
                 var meta = $("<em>" + users_array[message.user_id] + " " + chat_friend + " " + moment(message.created_at).format('DD-MM H:mm') + " <img id='status_img_" + message.id + "' src='/images/1.png' /> &nbsp;</em>");

                 // row.attr("id", domId);

                 p.appendTo(text);
                 $(images).appendTo(text);
                 meta.appendTo(text);

                 text.appendTo(row);
                 if (tobottom) {
                   row.appendTo(container);
                 } else {
                   row.prependTo(container);
                 }
               } else {
                 if (message.sent == 0) {
                   var meta_content = "<em>" + (parseInt(message.user_id) !== 0 ? users_array[message.user_id] : "Unknown") + " " + moment(message.created_at).format('DD-MM H:mm') + " </em>";
                 } else {
                   var meta_content = "<em>" + (parseInt(message.user_id) !== 0 ? users_array[message.user_id] : "Unknown") + " " + moment(message.created_at).format('DD-MM H:mm') + " <img id='status_img_" + message.id + "' src='/images/1.png' /></em>";
                 }

                 var resend_button = '';
                 resend_button = "<a href='#' class='btn btn-xs btn-secondary ml-1 resend-message' data-id='" + message.id + "'>Resend (" + message.resent + ")</a>";

                 var reminder_button = '';
                 reminder_button = "<a href='#' class='btn btn-image ml-1 reminder-message' data-id='" + message.id + "' data-toggle='modal' data-target='#reminderMessageModal'><img src='/images/reminder.png' /></a>";

                 // var error_flag = '';
                 // if (message.error_status == 1) {
                 //   error_flag = "<a href='#' class='btn btn-image fix-message-error' data-id='" + message.id + "'><img src='/images/flagged.png' /></a><a href='#' class='btn btn-xs btn-secondary ml-1 resend-message' data-id='" + message.id + "'>Resend</a>";
                 // } else if (message.error_status == 2) {
                 //   error_flag = "<a href='#' class='btn btn-image fix-message-error' data-id='" + message.id + "'><img src='/images/flagged.png' /><img src='/images/flagged.png' /></a><a href='#' class='btn btn-xs btn-secondary ml-1 resend-message' data-id='" + message.id + "'>Resend</a>";
                 // }




                 var meta = $(meta_content);

                 edit_field.appendTo(text);

                 if (!message.approved) {
                     var approveBtn = $("<button class='btn btn-xs btn-secondary btn-approve ml-3'>Approve</button>");
                     var editBtn = ' <a href="#" style="font-size: 9px" class="edit-message whatsapp-message ml-2" data-messageid="' + message.id + '">Edit</a>';
                     approveBtn.click(function() {
                         approveMessage( this, message );
                     } );
                     if (is_admin || is_hod_crm) {
                       approveBtn.appendTo( meta );
                       $(editBtn).appendTo( meta );
                     }
                 }

                 forward.appendTo(meta);

                 // $(error_flag).appendTo(meta);
                 $(resend_button).appendTo(meta);
                 $(reminder_button).appendTo(meta);
               }


               // if (!message.received) {
               //   if (message.sent == 0) {
               //     var meta_content = "<em>" + (parseInt(message.user_id) !== 0 ? users_array[message.user_id] : "Unknown") + " " + moment(message.created_at).format('DD-MM H:mm') + " </em>";
               //   } else {
               //     var meta_content = "<em>" + (parseInt(message.user_id) !== 0 ? users_array[message.user_id] : "Unknown") + " " + moment(message.created_at).format('DD-MM H:mm') + " <img id='status_img_" + message.id + "' src='/images/1.png' /></em>";
               //   }
               //
               //   var meta = $(meta_content);
               // } else {
               //   var meta = $("<em>Customer " + moment(message.created_at).format('DD-MM H:mm') + " </em>");
               // }

               row.attr("id", domId);

               p.attr("data-messageshort", message.message);
               p.attr("data-message", message.message);
               p.attr("data-expanded", "true");
               p.attr("data-messageid", message.id);
               // console.log("renderMessage message is ", message);
               if (message.message) {
                 p.html(message.message);
               } else if (message.media_url) {
                   var splitted = message.content_type.split("/");
                   if (splitted[0]==="image" || splitted[0] === 'm') {
                       var a = $("<a></a>");
                       a.attr("target", "_blank");
                       a.attr("href", message.media_url);
                       var img = $("<img></img>");
                       img.attr("src", message.media_url);
                       img.attr("width", "100");
                       img.attr("height", "100");
                       img.appendTo( a );
                       a.appendTo( p );
                       // console.log("rendered image message ", a);
                   } else if (splitted[0]==="video") {
                       $("<a target='_blank' href='" + message.media_url+"'>"+ message.media_url + "</a>").appendTo(p);
                   }
               }

               var has_product_image = false;

               if (message.images) {
                 var images = '';
                 message.images.forEach(function (image) {
                   images += image.product_id !== '' ? '<a href="/products/' + image.product_id + '" data-toggle="tooltip" data-html="true" data-placement="top" title="<strong>Special Price: </strong>' + image.special_price + '<br><strong>Size: </strong>' + image.size + '<br><strong>Supplier: </strong>' + image.supplier_initials + '">' : '';
                   images += '<div class="thumbnail-wrapper"><img src="' + image.image + '" class="message-img thumbnail-200" /><span class="thumbnail-delete whatsapp-image" data-image="' + image.key + '">x</span></div>';
                   images += image.product_id !== '' ? '<input type="checkbox" name="product" style="width: 20px; height: 20px;" class="d-block mx-auto select-product-image" data-id="' + image.product_id + '" /></a>' : '';

                   if (image.product_id !== '') {
                     has_product_image = true;
                   }
                 });

                 images += '<br>';

                 if (has_product_image) {
                   var show_images_wrapper = $('<div class="show-images-wrapper hidden"></div>');
                   var show_images_button = $('<button type="button" class="btn btn-xs btn-secondary show-images-button">Show Images</button>');

                   $(images).appendTo(show_images_wrapper);
                   $(show_images_wrapper).appendTo(text);
                   $(show_images_button).appendTo(text);
                 } else {
                   $(images).appendTo(text);
                 }

               }

               p.appendTo(body);
               body.appendTo(text);

               // if (message.status == 0 || message.status == 5 || message.status == 6) {
               //
               // } else {
               //
               //
               // }

               meta.appendTo(text);


               // if (!message.received) {
               //   // if (!message.approved) {
               //   //     var approveBtn = $("<button class='btn btn-xs btn-secondary btn-approve ml-3'>Approve</button>");
               //   //     var editBtn = ' <a href="#" style="font-size: 9px" class="edit-message whatsapp-message ml-2" data-messageid="' + message.id + '">Edit</a>';
               //   //     approveBtn.click(function() {
               //   //         approveMessage( this, message );
               //   //     } );
               //   //     if (is_admin || is_hod_crm) {
               //   //       approveBtn.appendTo( text );
               //   //       $(editBtn).appendTo( text );
               //   //     }
               //   // }
               // } else {
               //   var moduleid = 0;
               //   var mark_read = $("<a href data-url='/whatsapp/updatestatus?status=5&id=" + message.id + "&moduleid=" + moduleid+ "&moduletype=leads' style='font-size: 9px' class='change_message_status'>Mark as Read </a><span> | </span>");
               //   var mark_replied = $('<a href data-url="/whatsapp/updatestatus?status=6&id=' + message.id + '&moduleid=' + moduleid + '&moduletype=leads" style="font-size: 9px" class="change_message_status">Mark as Replied </a>');
               //
               //   if (message.status == 0) {
               //     mark_read.appendTo(meta);
               //   }
               //   if (message.status == 0 || message.status == 5) {
               //     mark_replied.appendTo(meta);
               //   }
               // }

               // var forward = $('<button class="btn btn-xs btn-secondary forward-btn" data-toggle="modal" data-target="#forwardModal" data-id="' + message.id + '">Forward >></button>');

               if (has_product_image) {
                 var create_lead = $('<a href="#" class="btn btn-xs btn-secondary ml-1 create-product-lead">+ Lead</a>');
                 var create_order = $('<a href="#" class="btn btn-xs btn-secondary ml-1 create-product-order">+ Order</a>');

                 create_lead.appendTo(meta);
                 create_order.appendTo(meta);
               }

               // forward.appendTo(meta);

               // if (has_product_image) {
               //
               // }

               text.appendTo( row );

               if (message.status == 7) {
                 if (tobottom) {
                   row.appendTo(suggestion_container);
                 } else {
                   row.prependTo(suggestion_container);
                 }
               } else {
                 if (tobottom) {
                   row.appendTo(container);
                 } else {
                   row.prependTo(container);
                 }
               }

             // }

                     return true;
        }
        function pollMessages(page = null, tobottom = null, addElapse = null) {
                 var qs = "";
                 qs += "?taskId=" + taskId;
                 if (page) {
                   qs += "&page=" + page;
                 }
                 if (addElapse) {
                     qs += "&elapse=3600";
                 }
                 var anyNewMessages = false;

                 return new Promise(function(resolve, reject) {
                     $.getJSON("/whatsapp/pollMessagesCustomer" + qs, function( data ) {

                         data.data.forEach(function( message ) {
                             var rendered = renderMessage( message, tobottom );
                             if ( !anyNewMessages && rendered ) {
                                 anyNewMessages = true;
                             }
                         } );

                         if (page) {
                           $('#load-more-messages').text('Load More');
                           can_load_more = true;
                         }

                         if ( anyNewMessages ) {
                             scrollChatTop();
                             anyNewMessages = false;
                         }
                         if (!addElapse) {
                             addElapse = true; // load less messages now
                         }


                         resolve();
                     });

                 });
        }

        const socket = io("https://sololuxury.co/?realtime_id=task_{{ $task->id }}", {
          'secure': false
        });

        socket.on("new-message", function (message) {
          console.log(message);
          renderMessage(message, null);
        });

             function scrollChatTop() {
                 // console.log("scrollChatTop called");
                 // var el = $(".chat-frame");
                 // el.scrollTop(el[0].scrollHeight - el[0].clientHeight);
             }

        // function startPolling() {
        //   setTimeout( function() {
        //              pollMessages(null, null, addElapse).then(function() {
        //                  startPolling();
        //              }, errorHandler);
        //          }, 1000);
        // }
        // function sendWAMessage() {
        //   var data = createMessageArgs();
        //          //var data = new FormData();
        //          //data.append("message", $("#waNewMessage").val());
        //          //data.append("lead_id", leadId );
        //   $.ajax({
        //     url: '/whatsapp/sendMessage/customer',
        //     type: 'POST',
        //              "dataType"    : 'text',           // what to expect back from the PHP script, if anything
        //              "cache"       : false,
        //              "contentType" : false,
        //              "processData" : false,
        //              "data": data
        //   }).done( function(response) {
        //       $('#waNewMessage').val('');
        //       $('#waNewMessage').closest('.form-group').find('.dropify-clear').click();
        //       pollMessages();
        //     // console.log("message was sent");
        //   }).fail(function(errObj) {
        //     alert("Could not send message");
        //   });
        // }

        // sendBtn.click(function() {
        //   sendWAMessage();
        // } );
        // startPolling();
        pollMessages(null, null, addElapse);

         $(document).on('click', '.send-communication', function(e) {
           e.preventDefault();

           var thiss = $(this);
           var url = $(this).closest('form').attr('action');
           var token = "{{ csrf_token() }}";
           var file = $($(this).closest('form').find('input[type="file"]'))[0].files[0];
           var status = $(this).closest('form').find('input[name="status"]').val();
           var screenshot_path = $('#screenshot_path').val();
           var task_id = {{ $task->id }};
           var formData = new FormData();

           formData.append("_token", token);
           formData.append("image", file);
           formData.append("message", $(this).closest('form').find('textarea').val());
           // formData.append("moduletype", $(this).closest('form').find('input[name="moduletype"]').val());
           formData.append("task_id", task_id);
           formData.append("assigned_to", $(this).closest('form').find('select[name="assigned_to"]').val());
           formData.append("status", status);
           formData.append("screenshot_path", screenshot_path);

           // if (status == 4) {
           //   formData.append("assigned_user", $(this).closest('form').find('select[name="assigned_user"]').val());
           // }

           if ($(this).closest('form')[0].checkValidity()) {
             $.ajax({
               type: 'POST',
               url: url,
               data: formData,
               processData: false,
               contentType: false
             }).done(function(response) {
               console.log(response);
               pollMessages();
               $(thiss).closest('form').find('textarea').val('');
               $('#paste-container').empty();
               $('#screenshot_path').val('');
               $(thiss).closest('form').find('.dropify-clear').click();

               if ($(thiss).hasClass('received-customer')) {
                 $(thiss).closest('form').find('#customerMessageButton').removeClass('hidden');
                 $(thiss).closest('form').find('textarea').addClass('hidden');
               }
             }).fail(function(response) {
               console.log(response);
               alert('Error sending a message');
             });
           } else {
             $(this).closest('form')[0].reportValidity();
           }

         });

         var can_load_more = true;

         $('#message-wrapper').scroll(function() {
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

         $(document).on('click', '#load-more-messages', function() {
           var current_page = $(this).data('nextpage');
           $(this).data('nextpage', current_page + 1);
           var next_page = $(this).data('nextpage');
           $('#load-more-messages').text('Loading...');

           pollMessages(next_page, true);
         });
      });

      $(document).on('click', '.change_message_status', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        var token = "{{ csrf_token() }}";
        var thiss = $(this);

        if ($(this).hasClass('wa_send_message')) {
          var message_id = $(this).data('messageid');
          var message = $('#message_body_' + message_id).find('p').data('message').toString().trim();

          $.ajax({
            url: "{{ url('whatsapp/updateAndCreate') }}",
            type: 'POST',
            data: {
              _token: token,
              moduletype: "task",
              message_id: message_id
            },
            beforeSend: function() {
              $(thiss).text('Loading');
            }
          }).done( function(response) {
          }).fail(function(errObj) {
            console.log(errObj);
            alert("Could not create whatsapp message");
          });
        }
          $.ajax({
            url: url,
            type: 'GET'
          }).done( function(response) {
            $(thiss).remove();
          }).fail(function(errObj) {
            alert("Could not change status");
          });



      });

      $(document).on('click', '.edit-message', function(e) {
        e.preventDefault();
        var thiss = $(this);
        var message_id = $(this).data('messageid');

        $('#message_body_' + message_id).css({'display': 'none'});
        $('#edit-message-textarea' + message_id).css({'display': 'block'});

        $('#edit-message-textarea' + message_id).keypress(function(e) {
          var key = e.which;

          if (key == 13) {
            e.preventDefault();
            var token = "{{ csrf_token() }}";
            var url = "{{ url('message') }}/" + message_id;
            var message = $('#edit-message-textarea' + message_id).val();

            if ($(thiss).hasClass('whatsapp-message')) {
              var type = 'whatsapp';
            } else {
              var type = 'message';
            }

            $.ajax({
              type: 'POST',
              url: url,
              data: {
                _token: token,
                body: message,
                type: type
              },
              success: function(data) {
                $('#edit-message-textarea' + message_id).css({'display': 'none'});
                $('#message_body_' + message_id).text(message);
                $('#message_body_' + message_id).css({'display': 'block'});
              }
            });
          }
        });
      });

      $(document).on('click', '.thumbnail-delete', function(event) {
        event.preventDefault();
        var thiss = $(this);
        var image_id = $(this).data('image');
        var message_id = $(this).closest('.talk-bubble').find('.collapsible-message').data('messageid');
        // var message = $(this).closest('.talk-bubble').find('.collapsible-message').data('message');
        var token = "{{ csrf_token() }}";
        var url = "{{ url('message') }}/" + message_id + '/removeImage';
        var type = 'message';

        if ($(this).hasClass('whatsapp-image')) {
          type = "whatsapp";
        }

        // var image_container = '<div class="thumbnail-wrapper"><img src="' + image + '" class="message-img thumbnail-200" /><span class="thumbnail-delete" data-image="' + image + '">x</span></div>';
        // var new_message = message.replace(image_container, '');

        // if (new_message.indexOf('message-img') != -1) {
        //   var short_new_message = new_message.substr(0, new_message.indexOf('<div class="thumbnail-wrapper">')).length > 150 ? (new_message.substr(0, 147)) : new_message;
        // } else {
        //   var short_new_message = new_message.length > 150 ? new_message.substr(0, 147) + '...' : new_message;
        // }

        $.ajax({
          type: 'POST',
          url: url,
          data: {
            _token: token,
            image_id: image_id,
            message_id: message_id,
            type: type
          },
          success: function(data) {
            $(thiss).parent().remove();
            // $('#message_body_' + message_id).children('.collapsible-message').data('messageshort', short_new_message);
            // $('#message_body_' + message_id).children('.collapsible-message').data('message', new_message);
          }
        });
      });

      $(document).ready(function() {
        $("body").tooltip({ selector: '[data-toggle=tooltip]' });
      });

      $('#approval_reply').on('click', function() {
        $('#model_field').val('Approval Lead');
      });

      $('#internal_reply').on('click', function() {
        $('#model_field').val('Internal Lead');
      });

      $('#approvalReplyForm').on('submit', function(e) {
        e.preventDefault();

        var url = "{{ route('reply.store') }}";
        var reply = $('#reply_field').val();
        var category_id = $('#category_id_field').val();
        var model = $('#model_field').val();

        $.ajax({
          type: 'POST',
          url: url,
          headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          data: {
            reply: reply,
            category_id: category_id,
            model: model
          },
          success: function(reply) {
            // $('#ReplyModal').modal('hide');
            $('#reply_field').val('');
            if (model == 'Approval Lead') {
              $('#quickComment').append($('<option>', {
                value: reply,
                text: reply
              }));
            } else {
              $('#quickCommentInternal').append($('<option>', {
                value: reply,
                text: reply
              }));
            }

          }
        });
      });

      $(document).on('click', '.forward-btn', function() {
        var id = $(this).data('id');
        $('#forward_message_id').val(id);
      });

      $(document).on('click', '.complete-call', function(e) {
        e.preventDefault();

        var thiss = $(this);
        var token = "{{ csrf_token() }}";
        var url = "{{ route('instruction.complete') }}";
        var id = $(this).data('id');
        var assigned_from = $(this).data('assignedfrom');
        var current_user = {{ Auth::id() }};

        $.ajax({
          type: 'POST',
          url: url,
          data: {
            _token: token,
            id: id
          },
          beforeSend: function() {
            $(thiss).text('Loading');
          }
        }).done( function(response) {
          // $(thiss).parent().html(moment(response.time).format('DD-MM HH:mm'));
          $(thiss).parent().html('Completed');


        }).fail(function(errObj) {
          console.log(errObj);
          alert("Could not mark as completed");
        });
      });

      $('#quickCategory').on('change', function() {
        var replies = JSON.parse($(this).val());
        $('#quickComment').empty();

        $('#quickComment').append($('<option>', {
          value: '',
          text: 'Quick Reply'
        }));

        replies.forEach(function(reply) {
          $('#quickComment').append($('<option>', {
            value: reply.reply,
            text: reply.reply
          }));
        });
      });

      $('#quickCategoryInternal').on('change', function() {
        var replies = JSON.parse($(this).val());
        $('#quickCommentInternal').empty();

        $('#quickCommentInternal').append($('<option>', {
          value: '',
          text: 'Quick Reply'
        }));

        replies.forEach(function(reply) {
          $('#quickCommentInternal').append($('<option>', {
            value: reply.reply,
            text: reply.reply
          }));
        });
      });

      $(document).on('click', '.collapse-fix', function() {
        if (!$(this).hasClass('collapsed')) {
          var target = $(this).data('target');
          var all = $('.collapse-element').not($(target));

          Array.from(all).forEach(function(element) {
            $(element).removeClass('in');
          });
        }
      });

      $('.add-task').on('click', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        $('#add-remark input[name="id"]').val(id);
      });

      $('#addRemarkButton').on('click', function() {
        var id = $('#add-remark input[name="id"]').val();
        var remark = $('#add-remark textarea[name="remark"]').val();

        $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: '{{ route('task.addRemark') }}',
            data: {
              id:id,
              remark:remark,
              module_type: 'instruction'
            },
        }).done(response => {
            alert('Remark Added Success!')
            window.location.reload();
        }).fail(function(response) {
          console.log(response);
        });
      });


      $(".view-remark").click(function () {
        var id = $(this).attr('data-id');

          $.ajax({
              type: 'GET',
              headers: {
                  'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              url: '{{ route('task.gettaskremark') }}',
              data: {
                id:id,
                module_type: "instruction"
              },
          }).done(response => {
              var html='';

              $.each(response, function( index, value ) {
                html+=' <p> '+value.remark+' <br> <small>By ' + value.user_name + ' updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
                html+"<hr>";
              });
              $("#viewRemarkModal").find('#remark-list').html(html);
          });
      });

      $('#createInstructionReplyButton').on('click', function(e) {
       e.preventDefault();

       var url = "{{ route('reply.store') }}";
       var reply = $('#instruction_reply_field').val();

       $.ajax({
         type: 'POST',
         url: url,
         headers: {
             'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
         },
         data: {
           reply: reply,
           category_id: 1,
           model: 'Instruction'
         },
         success: function(reply) {
           $('#instruction_reply_field').val('');
           $('#instructionComment').append($('<option>', {
             value: reply,
             text: reply
           }));
         }
       });
      });

        // if ($(this).is(":focus")) {
        // Created by STRd6
        // MIT License
        // jquery.paste_image_reader.js
        (function($) {
          var defaults;
          $.event.fix = (function(originalFix) {
            return function(event) {
              event = originalFix.apply(this, arguments);
              if (event.type.indexOf('copy') === 0 || event.type.indexOf('paste') === 0) {
                event.clipboardData = event.originalEvent.clipboardData;
              }
              return event;
            };
          })($.event.fix);
          defaults = {
            callback: $.noop,
            matchType: /image.*/
          };
          return $.fn.pasteImageReader = function(options) {
            if (typeof options === "function") {
              options = {
                callback: options
              };
            }
            options = $.extend({}, defaults, options);
            return this.each(function() {
              var $this, element;
              element = this;
              $this = $(this);
              return $this.bind('paste', function(event) {
                var clipboardData, found;
                found = false;
                clipboardData = event.clipboardData;
                return Array.prototype.forEach.call(clipboardData.types, function(type, i) {
                  var file, reader;
                  if (found) {
                    return;
                  }
                  if (type.match(options.matchType) || clipboardData.items[i].type.match(options.matchType)) {
                    file = clipboardData.items[i].getAsFile();
                    reader = new FileReader();
                    reader.onload = function(evt) {
                      return options.callback.call(element, {
                        dataURL: evt.target.result,
                        event: evt,
                        file: file,
                        name: file.name
                      });
                    };
                    reader.readAsDataURL(file);
                    return found = true;
                  }
                });
              });
            });
          };
        })(jQuery);

          var dataURL, filename;
          $("html").pasteImageReader(function(results) {
            console.log(results);

            // $('#message-body').on('focus', function() {
            	filename = results.filename, dataURL = results.dataURL;

              var img = $('<div class="image-wrapper position-relative"><img src="' + dataURL + '" class="img-responsive" /><button type="button" class="btn btn-xs btn-secondary remove-screenshot">x</button></div>');

              $('#paste-container').empty();
              $('#paste-container').append(img);
              $('#screenshot_path').val(dataURL);
            // });

          });

          $(document).on('click', '.remove-screenshot', function() {
            $(this).closest('.image-wrapper').remove();
            $('#screenshot_path').val('');
          });
        // }


      $(document).on('click', '.change-history-toggle', function() {
        $(this).siblings('.change-history-container').toggleClass('hidden');
      });

      $('#customerMessageButton').on('click', function() {
        $(this).siblings('textarea').removeClass('hidden');
        $(this).addClass('hidden');
      });

      $('#updateCustomerButton').on('click', function() {
        var id = {{ $task->id }};
        var thiss = $(this);
        var name = $('#customer_name').val();
        var phone = $('#customer_phone').val();
        var whatsapp_number = $('#whatsapp_change').val();
        var address = $('#customer_address').val();
        var city = $('#customer_city').val();
        var country = $('#customer_country').val();
        var pincode = $('#customer_pincode').val();
        var email = $('#customer_email').val();
        var insta_handle = $('#customer_insta_handle').val();
        var rating = $('#customer_rating').val();
        var shoe_size = $('#customer_shoe_size').val();
        var clothing_size = $('#customer_clothing_size').val();
        var gender = $('#customer_gender').val();

        $.ajax({
          type: "POST",
          url: "{{ url('customer') }}/" + id + '/edit',
          data: {
            _token: "{{ csrf_token() }}",
            name: name,
            phone: phone,
            whatsapp_number: whatsapp_number,
            address: address,
            city: city,
            country: country,
            pincode: pincode,
            email: email,
            insta_handle: insta_handle,
            rating: rating,
            shoe_size: shoe_size,
            clothing_size: clothing_size,
            gender: gender,
          },
          beforeSend: function() {
            $(thiss).text('Saving...');
          }
        }).done(function() {
          $(thiss).text('Save');
          $(thiss).removeClass('btn-secondary');
          $(thiss).addClass('btn-success');

          setTimeout(function () {
            $(thiss).addClass('btn-secondary');
            $(thiss).removeClass('btn-success');
          }, 2000);
        }).fail(function(response) {
          $(thiss).text('Save');
          console.log(response);
          alert('Could not update customer');
        });
      });

      $('#showActionsButton').on('click', function() {
        $('#actions-container').toggleClass('hidden');
      });

      $(document).on('click', '.show-images-button', function() {
        $(this).siblings('.show-images-wrapper').toggleClass('hidden');
      });

      $(document).on('click', '.fix-message-error', function() {
        var id = $(this).data('id');
        var thiss = $(this);

        $.ajax({
          type: "POST",
          url: "{{ url('whatsapp') }}/" + id + "/fixMessageError",
          data: {
            _token: "{{ csrf_token() }}",
          },
          beforeSend: function() {
            $(thiss).text('Fixing...');
          }
        }).done(function() {
          $(thiss).remove();
        }).fail(function(response) {
          $(thiss).html('<img src="/images/flagged.png" />');

          console.log(response);

          alert('Could not mark as fixed');
        });
      });

      $(document).on('click', '.resend-message', function() {
        var id = $(this).data('id');
        var thiss = $(this);

        $.ajax({
          type: "POST",
          url: "{{ url('whatsapp') }}/" + id + "/resendMessage",
          data: {
            _token: "{{ csrf_token() }}",
          },
          beforeSend: function() {
            $(thiss).text('Sending...');
          }
        }).done(function(response) {
          $(thiss).text('Resend (' + response.resent + ")");
        }).fail(function(response) {
          $(thiss).text('Resend');

          console.log(response);

          alert('Could not resend message');
        });
      });

      $(document).on('click', '.reminder-message', function() {
        var id = $(this).data('id');

        $('#reminderMessageModal').find('input[name="message_id"]').val(id);
      });

      $(document).on('click', '.make-private-task', function() {
        var task_id = $(this).data('taskid');
        var thiss = $(this);

        $.ajax({
          type: "POST",
          url: "{{ url('task') }}/" + task_id + "/makePrivate",
          data: {
            _token: "{{ csrf_token() }}",
          },
          beforeSend: function() {
            $(thiss).text('Changing...');
          }
        }).done(function(response) {
          if (response.task.is_private == 1) {
            $(thiss).html('<img src="/images/private.png" />');
          } else {
            $(thiss).html('<img src="/images/not-private.png" />');
          }
        }).fail(function(response) {
          $(thiss).html('<img src="/images/not-private.png" />');

          console.log(response);

          alert('Could not make task private');
        });
      });

      $(document).on('click', '.make-watched-task', function() {
        var task_id = $(this).data('taskid');
        var thiss = $(this);

        $.ajax({
          type: "POST",
          url: "{{ url('task') }}/" + task_id + "/isWatched",
          data: {
            _token: "{{ csrf_token() }}",
          },
          beforeSend: function() {
            $(thiss).text('Changing...');
          }
        }).done(function(response) {
          if (response.task.is_watched == 1) {
            $(thiss).html('<img src="/images/starred.png" />');
          } else {
            $(thiss).html('<img src="/images/unstarred.png" />');
          }
        }).fail(function(response) {
          $(thiss).html('<img src="/images/unstarred.png" />');

          console.log(response);

          alert('Could not make task watched');
        });
      });

      var timer = 0;
      var delay = 200;
      var prevent = false;

      $(document).on('click', '.task-complete', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var thiss = $(this);

        timer = setTimeout(function () {
          if (!prevent) {
            var task_id = $(thiss).data('id');
            var image = $(thiss).html();
            var url = "/task/complete/" + task_id;
            var current_user = {{ Auth::id() }};

            if (!$(thiss).is(':disabled')) {
              $.ajax({
                type: "GET",
                url: url,
                data: {
                  type: 'complete'
                },
                beforeSend: function () {
                  $(thiss).text('Completing...');
                }
              }).done(function(response) {
                if (response.task.is_verified != null) {
                  $(thiss).html('<img src="/images/completed.png" />');
                } else if (response.task.is_completed != null) {
                  $(thiss).html('<img src="/images/completed-green.png" />');
                } else {
                  $(thiss).html('<img src="/images/incomplete.png" />');
                }

                if (response.task.assign_from != current_user) {
                  $(thiss).attr('disabled', true);
                }
              }).fail(function(response) {
                $(thiss).html(image);

                alert('Could not mark as completed!');

                console.log(response);
              });
            }
          }

          prevent = false;
        }, delay);
      });

      $(document).on('dblclick', '.task-complete', function(e) {
        e.preventDefault();
        e.stopPropagation();

        clearTimeout(timer);
        prevent = true;

        var thiss = $(this);
        var task_id = $(this).data('id');
        var image = $(this).html();
        var url = "/task/complete/" + task_id;

        $.ajax({
          type: "GET",
          url: url,
          data: {
            type: 'clear'
          },
          beforeSend: function () {
            $(thiss).text('Clearing...');
          }
        }).done(function(response) {
          if (response.task.is_verified != null) {
            $(thiss).html('<img src="/images/completed.png" />');
          } else if (response.task.is_completed != null) {
            $(thiss).html('<img src="/images/completed-green.png" />');
          } else {
            $(thiss).html('<img src="/images/incomplete.png" />');
          }
        }).fail(function(response) {
          $(thiss).html(image);

          alert('Could not clear the task!');

          console.log(response);
        });
      });

      $(document).on('click', '.create-quick-task-button', function() {
        var thiss = $(this);
        var remark = $(this).data('remark');
        var assign_to = [
          @foreach ($task->users as $key => $user)
          {{ $user->id }},
          @endforeach
        ];

        var assign_to_contacts = [
          @foreach ($task->contacts as $key => $contact)
          {{ $contact->id }},
          @endforeach
        ];

        console.log(assign_to);

        if (!$(this).is(':disabled')) {
          $.ajax({
            type: "POST",
            url: "{{ route('task.store') }}",
            data: {
              _token: "{{ csrf_token() }}",
              task_subject: 'Appointment Task',
              task_details: remark,
              assign_to: assign_to,
        			assign_to_contacts: assign_to_contacts
            },
            beforeSend: function () {
              $(thiss).text('Creating...');
              $(thiss).attr('disabled', true);
            }
          }).done(function() {
            $(thiss).html('<img src="/images/add.png" />');
          }).fail(function(response) {
            $(thiss).html('<img src="/images/add.png" />');
            $(thiss).attr('disabled', false);

            console.log(response);

            alert('Could not create task!');
          });
        }
      });

      $('#create-note-field').keypress(function(e) {
        var key = e.which;
        var thiss = $(this);

        if (key == 13) {
          e.preventDefault();
          var note = $(thiss).val();
          var id = "{{ $task->id }}";

          if (note != '') {
            $.ajax({
              type: 'POST',
              url: "{{ url('task') }}/" + id + '/addNote',
              data: {
                _token: "{{ csrf_token() }}",
                note: note,
              }
            }).done(function() {
              $(thiss).val('');
              var note_html = `<li class="list-group-item d-flex justify-content-between align-items-center">
                                ` + note + `
                                <button type="button" class="btn btn-image create-quick-task-button" data-remark="` + note + `"><img src="/images/add.png" /></button>
                              </li>`;

              $('#note-list-container').append(note_html);
            }).fail(function(response) {
              console.log(response);

              alert('Could not add note');
            });
          } else {
            alert('Please enter note first!')
          }
        }
      });

      $(document).on('keypress', '.create-subnote', function(e) {
        var key = e.which;
        var thiss = $(this);
        var id = $(this).data('id');

        if (key == 13) {
          e.preventDefault();
          var note = $(thiss).val();

          if (note != '') {
            $.ajax({
              type: 'POST',
              url: "{{ url('task') }}/" + id + '/addSubnote',
              data: {
                _token: "{{ csrf_token() }}",
                note: note,
              }
            }).done(function() {
              $(thiss).val('');
              var note_html = `<li class="d-flex justify-content-between align-items-center">` + note + `<button type="button" class="btn btn-image create-quick-task-button" data-remark="` + note + `"><img src="/images/add.png" /></button></li>`;

              $(thiss).siblings('ul').append(note_html);
            }).fail(function(response) {
              console.log(response);

              alert('Could not add note');
            });
          } else {
            alert('Please enter note first!')
          }
        }
      });

      $('#task_category').on('change', function() {
        var category = $(this).val();
        var id = "{{ $task->id }}";
        var thiss = $(this);

        $.ajax({
          type: "POST",
          url: "{{ url('task') }}/" + id + '/updateCategory',
          data: {
            _token: "{{ csrf_token() }}",
            category: category
          }
        }).done(function() {
          $(thiss).siblings('.change_status_message').fadeIn(400);

          setTimeout(function () {
            $(thiss).siblings('.change_status_message').fadeOut(400);
          }, 2000);
        }).fail(function(response) {
          alert('Could not change the category');
          console.log(response);
        });
      });

      $('#edit_subject_button').on('click', function(e) {
        e.preventDefault();

        $(this).siblings('input').removeClass('hidden');
        $(this).siblings('.task-subject').addClass('hidden');
      });

      $(document).on('keypress', '#task_subject_field', function(e) {
        var key = e.which;
        var thiss = $(this);
        var id = "{{ $task->id }}";

        if (key == 13) {
          e.preventDefault();
          var subject = $(thiss).val();

          if (subject != '') {
            $.ajax({
              type: 'POST',
              url: "{{ url('task') }}/" + id + '/updateSubject',
              data: {
                _token: "{{ csrf_token() }}",
                subject: subject,
              }
            }).done(function() {
              $(thiss).addClass('hidden');
              $(thiss).siblings('.task-subject').text(subject);
              $(thiss).siblings('.task-subject').removeClass('hidden');
            }).fail(function(response) {
              console.log(response);

              alert('Could not change the subject');
            });
          } else {
            alert('Please enter subject first!')
          }
        }
      });

      $(document).on('click', '.flag-task', function() {
        var task_id = $(this).data('id');
        var thiss = $(this);

        $.ajax({
          type: "POST",
          url: "{{ route('task.flag') }}",
          data: {
            _token: "{{ csrf_token() }}",
            task_id: task_id
          },
          beforeSend: function() {
            $(thiss).text('Flagging...');
          }
        }).done(function(response) {
          if (response.is_flagged == 1) {
            // var badge = $('<span class="badge badge-secondary">Flagged</span>');
            //
            // $(thiss).parent().append(badge);
            $(thiss).html('<img src="/images/flagged.png" />');
          } else {
            $(thiss).html('<img src="/images/unflagged.png" />');
            // $(thiss).parent().find('.badge').remove();
          }

          // $(thiss).remove();
        }).fail(function(response) {
          $(thiss).html('<img src="/images/unflagged.png" />');

          alert('Could not flag task!');

          console.log(response);
        });
      });
  </script>
@endsection
