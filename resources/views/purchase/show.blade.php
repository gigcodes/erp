@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

<div class="row">
  <div class="col-lg-12 margin-tb">
    <div class="pull-left">
      <h2>Purchase Bulk Order</h2>
    </div>
    <div class="pull-right">
      <a class="btn btn-secondary" href="{{ route('purchase.index') }}">Back</a>
    </div>
  </div>
</div>


@if ($message = Session::get('success'))
<div class="alert alert-success">
  <p>{{ $message }}</p>
</div>
@endif



<div class="row">
  <div class="col-md-6 col-12">
    <div class="form-group">
      <strong>ID:</strong> {{ $order->id }}
    </div>

    <div class="form-group">
      <strong>Date:</strong> {{ Carbon\Carbon::parse($order->created_at)->format('d-m H:i') }}
    </div>

    <div class="form-group">
      <strong>Supplier:</strong>
      @php $supplier_list = (new \App\ReadOnly\SupplierList)->all(); @endphp
      <select class="form-control" name="supplier">
        <option value="">Select Supplier</option>
        @foreach ($supplier_list as $index => $value)
          <option value="{{ $index }}" {{ $index == $order->supplier ? 'selected' : '' }}>{{ $value }}</option>
        @endforeach
      </select>
    </div>

    <div class="form-group">
      <strong>Status:</strong>
      <Select name="status" class="form-control" id="change_status">
           @foreach($purchase_status as $key => $value)
            <option value="{{$value}}" {{$value == $order->status ? 'Selected=Selected':''}}>{{$key}}</option>
            @endforeach
      </Select>
      <span id="change_status_message" class="text-success" style="display: none;">Successfully changed status</span>
    </div>

    {{-- @if (isset($order->status) && $order->status != 'Ordered') --}}
      <div class="form-group" id="bill-wrapper" style="display: {{ (isset($order->status) && $order->status != 'Ordered') ? 'block' : 'none' }}">
        <strong>Bill number:</strong>
        <input type="text" name="bill_number" class="form-control" value="{{ $order->bill_number }}">
      </div>
    {{-- @endif --}}

    {{-- @php $status = ( new \App\ReadOnly\OrderStatus )->getNameById( $order_status );
    @endphp

    <div class="form-group">
      <strong>status:</strong>
      <Select name="status" class="form-control" id="change_status">
        @foreach($order_statuses as $key => $value)
        <option value="{{$value}}" {{$value == $status ? 'Selected=Selected':''}}>{{$key}}</option>
        @endforeach
      </Select>
      <span id="change_status_message" class="text-success" style="display: none;">Successfully changed status</span>
    </div> --}}
    <div class="form-group">
      <strong>Supplier Phone:</strong>
      <input type="number" name="supplier_phone" class="form-control" value="{{ $order->supplier_phone }}">
    </div>

    <div class="form-group">
        <strong>Solo Phone:</strong>
     <Select name="whatsapp_number" class="form-control">
               <option value>None</option>
                <option value="919167152579" {{'919167152579' == $order->whatsapp_number ? 'Selected=Selected':''}}>00</option>
                <option value="918291920452" {{'918291920452'== $order->whatsapp_number ? 'Selected=Selected':''}}>02</option>
                <option value="918291920455" {{'918291920455'== $order->whatsapp_number ? 'Selected=Selected':''}}>03</option>
                <option value="919152731483" {{'919152731483'== $order->whatsapp_number ? 'Selected=Selected':''}}>04</option>
                <option value="919152731484" {{'919152731484'== $order->whatsapp_number ? 'Selected=Selected':''}}>05</option>
                <option value="919152731486" {{'919152731486'== $order->whatsapp_number ? 'Selected=Selected':''}}>06</option>
                <option value="918291352520" {{'918291352520'== $order->whatsapp_number ? 'Selected=Selected':''}}>08</option>
                <option value="919004008983" {{'919004008983'== $order->whatsapp_number ? 'Selected=Selected':''}}>09</option>
        </Select>
    </div>

    <div class="form-group">
      <a href="#" class="btn btn-secondary save-bill">Save</a>
      <span id="save_status" class="text-success" style="display: none;">Successfully saved!</span>
    </div>

    <div class="form-group">
      <strong>Customers List</strong>
      <ul>
        @foreach ($order->products as $product)
          @foreach ($product->orderproducts as $order_product)
            <li><a href="{{ route('customer.show', $order_product->order->customer->id) }}" target="_blank">{{ $order_product->order->customer->name }}</a></li>
          @endforeach
        @endforeach
      </ul>
    </div>
  </div>
  <div class="col-md-6 col-12">
    <div class="row">
      <div class="col">
        @php $purchase_price = 0;
          foreach ($order->products as $product)
            $purchase_price += $product->price;
        @endphp
        <div class="form-group">
          <strong>Purchase Price:</strong> {{ $purchase_price }}
        </div>
      </div>
    </div>
    <div class="row">
      @foreach ($order->products as $product)
        <div class="col-md-4">
          <a href="{{ route('purchase.product.show', $product->id) }}" data-toggle='tooltip' data-html='true' data-placement='top' title="<strong>Price: </strong>{{ $product->price }}">
            <img src="{{ $product->getMedia(config('constants.media_tags'))->first() ? $product->getMedia(config('constants.media_tags'))->first()->getUrl() : '' }}" class="img-responsive" alt="">
          </a>
        </div>
      @endforeach
    </div>
  </div>
</div>

<div id="taskModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Create Task</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form action="{{ route('task.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="task_type" value="quick_task">
        <input type="hidden" name="model_type" value="purchase">
        <input type="hidden" name="model_id" value="{{ $order->id }}">

        <div class="modal-body">
          <div class="form-group">
            <strong>Task Subject:</strong>
            <input type="text" class="form-control" name="task_subject" placeholder="Task Subject" id="task_subject" required />
            @if ($errors->has('task_subject'))
            <div class="alert alert-danger">{{$errors->first('task_subject')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Task Details:</strong>
            <textarea class="form-control" name="task_details" placeholder="Task Details" required></textarea>
            @if ($errors->has('task_details'))
            <div class="alert alert-danger">{{$errors->first('task_details')}}</div>
            @endif
          </div>

          <div class="form-group" id="completion_form_group">
            <strong>Completion Date:</strong>
            <div class='input-group date' id='completion-datetime'>
              <input type='text' class="form-control" name="completion_date" value="{{ date('Y-m-d H:i') }}" required />

              <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
              </span>
            </div>

            @if ($errors->has('completion_date'))
            <div class="alert alert-danger">{{$errors->first('completion_date')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Assigned To:</strong>
            <select name="assign_to[]" class="form-control" multiple required>
              @foreach($users as $user)
              <option value="{{$user['id']}}">{{$user['name']}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Create</button>
        </div>
      </form>
    </div>

  </div>
</div>

@php $users_array = \App\Helpers::getUserArray(\App\User::all()); @endphp

<div class="row">
  <div class="col-xs-12 col-sm-12 mb-3">
    <button type="button" class="btn btn-secondary mb-3" data-toggle="modal" data-target="#taskModal" id="addTaskButton">Add Task</button>

    @if (count($tasks) > 0)
      <table class="table">
        <thead>
          <tr>
            <th>Sr No</th>
            <th>Date</th>
            <th class="category">Category</th>
            <th>Task Subject</th>
            <th>Est Completion Date</th>
            <th>Assigned From</th>
            <th>&nbsp;</th>
            {{-- <th>Remarks</th> --}}
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1; $categories = \App\Http\Controllers\TaskCategoryController::getAllTaskCategory(); ?>
          @foreach($tasks as $task)
          <tr class="{{ \App\Http\Controllers\TaskModuleController::getClasses($task) }}" id="task_{{ $task['id'] }}">
            <td>{{$i++}}</td>
            <td>{{ Carbon\Carbon::parse($task['created_at'])->format('d-m H:i') }}</td>
            <td> {{ isset( $categories[$task['category']] ) ? $categories[$task['category']] : '' }}</td>
            <td class="task-subject" data-subject="{{$task['task_subject'] ? $task['task_subject'] : 'Task Details'}}" data-details="{{$task['task_details']}}" data-switch="0">{{ $task['task_subject'] ? $task['task_subject'] : 'Task Details' }}</td>
            <td> {{ Carbon\Carbon::parse($task['completion_date'])->format('d-m H:i')  }}</td>
            <td>{{ $users_array[$task['assign_from']] }}</td>
            @if( $task['assign_to'] == Auth::user()->id )
              @if ($task['is_completed'])
                <td>{{ Carbon\Carbon::parse($task['is_completed'])->format('d-m H:i') }}</td>
              @else
                <td><a href="/task/complete/{{$task['id']}}">Complete</a></td>
              @endif
            @else
              @if ($task['is_completed'])
                <td>{{ Carbon\Carbon::parse($task['is_completed'])->format('d-m H:i') }}</td>
              @else
                <td>Assigned to  {{ $task['assign_to'] ? $users_array[$task['assign_to']] : 'Nil'}}</td>
              @endif
            @endif
            <td>
              <a href id="add-new-remark-btn" class="add-task" data-toggle="modal" data-target="#add-new-remark_{{$task['id']}}" data-id="{{$task['id']}}">Add</a>
              <span> | </span>
              <a href id="view-remark-list-btn" class="view-remark" data-toggle="modal" data-target="#view-remark-list" data-id="{{$task['id']}}">View</a>
              <!--<button class="delete-task" data-id="{{$task['id']}}">Delete</button>-->
            </td>
          </tr>

          <!-- Modal -->
          <div id="add-new-remark_{{$task['id']}}" class="modal fade" role="dialog">
            <div class="modal-dialog">

              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title">Add New Remark</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <div class="modal-body">
                  <form id="add-remark">
                    <input type="hidden" name="id" value="">
                    <textarea id="remark-text_{{$task['id']}}" rows="1" name="remark" class="form-control"></textarea>
                    <button type="button" class="mt-2 " onclick="addNewRemark({{$task['id']}})">Add Remark</button>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>

            </div>
          </div>

          <!-- Modal -->
          <div id="view-remark-list" class="modal fade" role="dialog">
            <div class="modal-dialog">

              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title">View Remark</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <div class="modal-body">
                  <div id="remark-list">

                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>

            </div>
          </div>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
  <div class="col-xs-12">
    <div class="row">
      <div class="col-xs-12 col-sm-6">
        {{-- <p><strong></strong></p> --}}
        <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data" class="d-flex">
            @csrf

            {{-- <div class="row"> --}}
              <div class="form-group">
                <div class="upload-btn-wrapper btn-group">
                  <button class="btn btn-image px-1"><img src="/images/upload.png" /></button>
                  <input type="file" name="image" />
                  <button type="submit" class="btn btn-image px-1 send-communication"><img src="/images/filled-sent.png" /></button>
                </div>
              </div>

              {{-- <div class="col-xs-6"> --}}
                <div class="form-group flex-fill">
                  <textarea  class="form-control" name="body" placeholder="Received from Customer"></textarea>

                  <input type="hidden" name="moduletype" value="purchase" />
                  <input type="hidden" name="moduleid" value="{{$order['id']}}" />
                  <input type="hidden" name="assigned_user" value="{{$order['purchase_handler']}}" />
                  <input type="hidden" name="status" value="0" />
                </div>
              {{-- </div> --}}

            {{-- </div> --}}

         </form>
       </div>

       <div class="col-xs-12 col-sm-6">
         {{-- <p><strong></strong></p> --}}
         <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data" class="d-flex">
            @csrf

            {{-- <div class="row"> --}}
              <div class="form-group">
                <div class="upload-btn-wrapper btn-group pr-0 d-flex">
                  <button class="btn btn-image px-1"><img src="/images/upload.png" /></button>
                  <input type="file" name="image" />

                  {{-- <a href="{{ route('attachImages', ['purchase', $order['id'], 1, $order['purchase_handler']]) }}" class="btn btn-image px-1"><img src="/images/attach.png" /></a> --}}
                  <button type="submit" class="btn btn-image px-1 send-communication"><img src="/images/filled-sent.png" /></button>
                </div>
              </div>

              {{-- <div class="col-xs-6"> --}}
                <div class="form-group flex-fill">
                  <textarea id="message-body" class="form-control mb-3" name="body" placeholder="Send for approval"></textarea>

                  <input type="hidden" name="moduletype" value="purchase" />
                  <input type="hidden" name="moduleid" value="{{$order['id']}}" />
                  <input type="hidden" name="assigned_user" value="{{$order['purchase_handler']}}" />
                  <input type="hidden" name="status" value="1" />

                  <p class="pb-4" style="display: block;">
                      {{-- <strong>Quick Reply</strong> --}}
                      <select name="quickCategory" id="quickCategory" class="form-control mb-3">
                        <option value="">Select Category</option>
                        @foreach($reply_categories as $category)
                            <option value="{{ $category->approval_leads }}">{{ $category->name }}</option>
                        @endforeach
                      </select>

                      <select name="quickComment" id="quickComment" class="form-control">
                        <option value="">Quick Reply</option>
                        {{-- @foreach($approval_replies as $reply )
                            <option value="{{$reply->reply}}">{{$reply->reply}}</option>
                        @endforeach --}}
                      </select>
                  </p>

                  <button type="button" class="btn btn-xs btn-secondary mb-3" data-toggle="modal" data-target="#ReplyModal" id="approval_reply">Create Quick Reply</button>
                </div>
              {{-- </div> --}}

            {{-- </div> --}}

         </form>
       </div>

       <div id="ReplyModal" class="modal fade" role="dialog">
         <div class="modal-dialog">

           <!-- Modal content-->
           <div class="modal-content">
             <div class="modal-header">
               <h4 class="modal-title"></h4>
               <button type="button" class="close" data-dismiss="modal">&times;</button>
             </div>

             <form action="{{ route('reply.store') }}" method="POST" enctype="multipart/form-data" id="approvalReplyForm">
               @csrf

               <div class="modal-body">
                 <div class="form-group">
                     <strong>Select Category:</strong>
                     <select class="form-control" name="category_id" id="category_id_field">
                       @foreach ($reply_categories as $category)
                         <option value="{{ $category->id }}" {{ $category->id == old('category_id') ? 'selected' : '' }}>{{ $category->name }}</option>
                       @endforeach
                     </select>
                     @if ($errors->has('category_id'))
                         <div class="alert alert-danger">{{$errors->first('category_id')}}</div>
                     @endif
                 </div>

                 <div class="form-group">
                     <strong>Quick Reply:</strong>
                     <textarea class="form-control" id="reply_field" name="reply" placeholder="Quick Reply" required>{{ old('reply') }}</textarea>
                     @if ($errors->has('reply'))
                         <div class="alert alert-danger">{{$errors->first('reply')}}</div>
                     @endif
                 </div>

                 <input type="hidden" name="model" id="model_field" value="">

               </div>
               <div class="modal-footer">
                 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                 <button type="submit" class="btn btn-secondary">Create</button>
               </div>
             </form>
           </div>

         </div>
       </div><div id="ReplyModal" class="modal fade" role="dialog">
         <div class="modal-dialog">

           <!-- Modal content-->
           <div class="modal-content">
             <div class="modal-header">
               <h4 class="modal-title"></h4>
               <button type="button" class="close" data-dismiss="modal">&times;</button>
             </div>

             <form action="{{ route('reply.store') }}" method="POST" enctype="multipart/form-data" id="approvalReplyForm">
               @csrf

               <div class="modal-body">

                 <div class="form-group">
                     <strong>Quick Reply:</strong>
                     <textarea class="form-control" id="reply_field" name="reply" placeholder="Quick Reply" required>{{ old('reply') }}</textarea>
                     @if ($errors->has('reply'))
                         <div class="alert alert-danger">{{$errors->first('reply')}}</div>
                     @endif
                 </div>

                 <input type="hidden" name="model" id="model_field" value="">

               </div>
               <div class="modal-footer">
                 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                 <button type="submit" class="btn btn-secondary">Create</button>
               </div>
             </form>
           </div>

         </div>
       </div>

       <div class="col-xs-12 col-sm-6">
           {{-- <p><strong></strong></p> --}}
           <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data" class="d-flex">
              @csrf

              {{-- <div class="row"> --}}
                <div class="form-group">
                  <div class="upload-btn-wrapper btn-group">
                     <button class="btn btn-image px-1"><img src="/images/upload.png" /></button>
                      <input type="file" name="image" />
                      <button type="submit" class="btn btn-image px-1 send-communication"><img src="/images/filled-sent.png" /></button>
                    </div>
                </div>

                {{-- <div class="col-xs-6"> --}}
                  <div class="form-group flex-fill">
                    <textarea class="form-control mb-3" name="body" placeholder="Internal Communications" id="internal-message-body"></textarea>

                    <input type="hidden" name="moduletype" value="purchase" />
                    <input type="hidden" name="moduleid" value="{{$order['id']}}" />
                    {{-- <input type="hidden" name="assigned_user" value="{{$leads['assigned_user']}}" /> --}}
                    <input type="hidden" name="status" value="4" />

                    <strong>Assign to</strong>
                    <select name="assigned_user" class="form-control mb-3" required>
                      <option value="">Select User</option>
                      @foreach($users as $user)
                        <option value="{{$user['id']}}">{{$user['name']}}</option>
                      @endforeach
                    </select>

                    <p class="pb-4" style="display: block;">
                        {{-- <strong>Quick Reply</strong> --}}
                        <select name="quickCategoryInternal" id="quickCategoryInternal" class="form-control mb-3">
                          <option value="">Select Category</option>
                          @foreach($reply_categories as $category)
                              <option value="{{ $category->internal_leads }}">{{ $category->name }}</option>
                          @endforeach
                        </select>

                        <select name="quickCommentInternal" id="quickCommentInternal" class="form-control">
                            <option value="">Quick Reply</option>
                            {{-- @foreach($internal_replies as $reply)
                                <option value="{{$reply->reply}}">{{$reply->reply}}</option>
                            @endforeach --}}
                        </select>
                    </p>

                    <button type="button" class="btn btn-xs btn-secondary mb-3" data-toggle="modal" data-target="#ReplyModal" id="internal_reply">Create Quick Reply</button>
                  </div>
                {{-- </div> --}}


              {{-- </div> --}}


           </form>
         </div>

         <div class="col-xs-12 col-sm-6">
           <div class="d-flex">
             <div class="form-group">
               {{-- <a href="/leads?type=multiple" class="btn btn-xs btn-secondary">Send Multiple</a> --}}
               {{-- <a href="{{ route('attachImages', ['purchase', $order['id'], 9, $order['purchase_handler']]) }}" class="btn btn-image px-1"><img src="/images/attach.png" /></a> --}}
               <button id="waMessageSend" class="btn btn-sm btn-image"><img src="/images/filled-sent.png" /></button>
             </div>

             <div class="form-group flex-fill">
               <textarea id="waNewMessage" class="form-control" placeholder="Whatsapp message"></textarea>
             </div>
           </div>

           <label>Attach Media</label>
           <input id="waMessageMedia" type="file" name="media" />
         </div>
    </div>
  </div>

</div>

<div class="row">
  <h3>Messages</h3>
  <div class="col-xs-12" id="message-container"></div>
</div>

<div class="col-xs-12 text-center">
  <button type="button" id="load-more-messages" data-nextpage="1" class="btn btn-secondary">Load More</button>
</div>

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

  <script type="text/javascript">
    $('#completion-datetime').datetimepicker({
      format: 'YYYY-MM-DD HH:mm'
    });

    $(document).ready(function() {
      $("body").tooltip({ selector: '[data-toggle=tooltip]' });
    });

    $(document).on('click', '.edit-message', function(e) {
      e.preventDefault();
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

          $.ajax({
            type: 'POST',
            url: url,
            data: {
              _token: token,
              body: message
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

    $(document).on('change', '.is_statutory', function () {
        if ($(".is_statutory").val() == 1) {
            $("#completion_form_group").hide();
            $('#recurring-task').show();
        }
        else {
            $("#completion_form_group").show();
            $('#recurring-task').hide();
        }

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
     var sendBtn = $("#waMessageSend");
     var leadId = "{{$order->id}}";
         var addElapse = false;
         function errorHandler(error) {
             console.error("error occured: " , error);
         }
         function approveMessage(element, message) {
             $.post( "/whatsapp/approve/purchase", { messageId: message.id })
               .done(function( data ) {
                 if (data != 'success') {
                   data.forEach(function(id) {
                     $('#waMessage_' + id).find('.btn-approve').remove();
                   });
                 }

                 element.remove();
               }).fail(function(response) {
                 console.log(response);
                 alert(response.responseJSON.message);
               });
         }
         function createMessageArgs() {
              var data = new FormData();
             var text = $("#waNewMessage").val();
             var files = $("#waMessageMedia").prop("files");
             var text = $("#waNewMessage").val();

             data.append("purchase_id", leadId);
             if (files && files.length>0){
                 for ( var i = 0; i != files.length; i ++ ) {
                   data.append("media[]", files[ i ]);
                 }
                 return data;
             }
             if (text !== "") {
                 data.append("message", text);
                 return data;
             }

             alert("please enter a message or attach media");
           }

     function renderMessage(message, tobottom = null) {
         var domId = "waMessage_" + message.id;
         var current = $("#" + domId);
         var is_admin = "{{ Auth::user()->hasRole('Admin') }}";
         var is_hod_crm = "{{ Auth::user()->hasRole('HOD of CRM') }}";
         var users_array = {!! json_encode($users_array) !!};
         if ( current.get( 0 ) ) {
           return false;
         }

         if (message.body) {
           var leads_assigned_user = "{{ $order['purchase_handler'] }}";

           var text = $("<div class='talktext'></div>");
           var p = $("<p class='collapsible-message'></p>");

           if ((message.body).indexOf('<br>') !== -1) {
             var splitted = message.body.split('<br>');
             var short_message = splitted[0].length > 150 ? (splitted[0].substring(0, 147) + '...<br>' + splitted[1]) : message.body;
             var long_message = message.body;
           } else {
             var short_message = message.body.length > 150 ? (message.body.substring(0, 147) + '...') : message.body;
             var long_message = message.body;
           }

           var images = '';
           if (message.images !== null) {
             message.images.forEach(function (image) {
               images += image.product_id !== '' ? '<a href="/products/' + image.product_id + '" data-toggle="tooltip" data-html="true" data-placement="top" title="<strong>Special Price: </strong>' + image.special_price + '<br><strong>Size: </strong>' + image.size + '">' : '';
               images += '<div class="thumbnail-wrapper"><img src="' + image.image + '" class="message-img thumbnail-200" /><span class="thumbnail-delete" data-image="' + image.key + '">x</span></div>';
               images += image.product_id !== '' ? '</a>' : '';
             });
             images += '<br>';
           }

           p.attr("data-messageshort", short_message);
           p.attr("data-message", long_message);
           p.attr("data-expanded", "false");
           p.attr("data-messageid", message.id);
           p.html(short_message);

           if (message.status == 0 || message.status == 5 || message.status == 6) {
             var row = $("<div class='talk-bubble'></div>");

             var meta = $("<em>Customer " + moment(message.created_at).format('DD-MM H:m') + " </em>");
             var mark_read = $("<a href data-url='/message/updatestatus?status=5&id=" + message.id + "&moduleid=" + message.moduleid + "&moduletype=leads' style='font-size: 9px' class='change_message_status'>Mark as Read </a><span> | </span>");
             var mark_replied = $('<a href data-url="/message/updatestatus?status=6&id=' + message.id + '&moduleid=' + message.moduleid + '&moduletype=leads" style="font-size: 9px" class="change_message_status">Mark as Replied </a>');

             row.attr("id", domId);

             p.appendTo(text);
             $(images).appendTo(text);
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

           } else if (message.status == 4) {
             var row = $("<div class='talk-bubble' data-messageid='" + message.id + "'></div>");
             var chat_friend =  (message.assigned_to != 0 && message.assigned_to != leads_assigned_user && message.userid != message.assigned_to) ? ' - ' + users_array[message.assigned_to] : '';
             var meta = $("<em>" + users_array[message.userid] + " " + chat_friend + " " + moment(message.created_at).format('DD-MM H:m') + " <img id='status_img_" + message.id + "' src='/images/1.png' /> &nbsp;</em>");

             row.attr("id", domId);

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
             var row = $("<div class='talk-bubble' data-messageid='" + message.id + "'></div>");
             var body = $("<span id='message_body_" + message.id + "'></span>");
             var edit_field = $('<textarea name="message_body" rows="8" class="form-control" id="edit-message-textarea' + message.id + '" style="display: none;">' + message.body + '</textarea>');
             var meta = "<em>" + users_array[message.userid] + " " + moment(message.created_at).format('DD-MM H:m') + " <img id='status_img_" + message.id + "' src='/images/" + message.status + ".png' /> &nbsp;";

             if (message.status == 2 && is_admin == false) {
               meta += '<a href data-url="/message/updatestatus?status=3&id=' + message.id + '&moduleid=' + message.moduleid + '&moduletype=leads" style="font-size: 9px" class="change_message_status">Mark as sent </a>';
             }

             if (message.status == 1 && (is_admin == true || is_hod_crm == true)) {
               meta += '<a href data-url="/message/updatestatus?status=2&id=' + message.id + '&moduleid=' + message.moduleid + '&moduletype=leads" style="font-size: 9px" class="change_message_status wa_send_message" data-messageid="' + message.id + '">Approve</a>';
               meta += ' <a href="#" style="font-size: 9px" class="edit-message" data-messageid="' + message.id + '">Edit</a>';
             }

             meta += "</em>";
             var meta_content = $(meta);



             row.attr("id", domId);

             p.appendTo(body);
             body.appendTo(text);
             edit_field.appendTo(text);
             $(images).appendTo(text);
             meta_content.appendTo(text);

             if (message.status == 2 && is_admin == false) {
               var copy_button = $('<button class="copy-button btn btn-secondary" data-id="' + message.id + '" moduleid="' + message.moduleid + '" moduletype="orders" data-message="' + message.body + '"> Copy message </button>');
               copy_button.appendTo(text);
             }


             text.appendTo(row);

             if (tobottom) {
               row.appendTo(container);
             } else {
               row.prependTo(container);
             }
           }
         } else {
           var row = $("<div class='talk-bubble'></div>");
           var text = $("<div class='talktext'></div>");
           var p = $("<p class='collapsible-message'></p>");

           if (!message.received) {
             var meta = $("<em>" + (parseInt(message.user_id) !== 0 ? users_array[message.user_id] : "Unknown") + " " + moment(message.created_at).format('DD-MM H:m') + " </em>");
           } else {
             var meta = $("<em>Customer " + moment(message.created_at).format('DD-MM H:m') + " </em>");
           }

           row.attr("id", domId);

           p.attr("data-messageshort", message.message);
           p.attr("data-message", message.message);
           p.attr("data-expanded", "true");
           p.attr("data-messageid", message.id);
           // console.log("renderMessage message is ", message);
           if ( message.message ) {
               p.html( message.message );
           } else if ( message.media_url ) {
               var splitted = message.content_type[1].split("/");
               if (splitted[0]==="image") {
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
           } else if (message.images) {
             var images = '';
             message.images.forEach(function (image) {
               images += image.product_id !== '' ? '<a href="/products/' + image.product_id + '" data-toggle="tooltip" data-html="true" data-placement="top" title="<strong>Special Price: </strong>' + image.special_price + '<br><strong>Size: </strong>' + image.size + '">' : '';
               images += '<div class="thumbnail-wrapper"><img src="' + image.image + '" class="message-img thumbnail-200" /><span class="thumbnail-delete whatsapp-image" data-image="' + image.key + '">x</span></div>';
               images += image.product_id !== '' ? '</a>' : '';
             });
             images += '<br>';
             $(images).appendTo(p);
           }

           p.appendTo( text );
           meta.appendTo(text);
           if (!message.received) {
             if (!message.approved) {
                 var approveBtn = $("<button class='btn btn-xs btn-secondary btn-approve ml-3'>Approve</button>");
                 approveBtn.click(function() {
                     approveMessage( this, message );
                 } );
                 if (is_admin || is_hod_crm) {
                   approveBtn.appendTo( text );
                 }
             }
           } else {
             var moduleid = "{{ $order->id }}";
             var mark_read = $("<a href data-url='/whatsapp/updatestatus?status=5&id=" + message.id + "&moduleid=" + moduleid+ "&moduletype=leads' style='font-size: 9px' class='change_message_status'>Mark as Read </a><span> | </span>");
             var mark_replied = $('<a href data-url="/whatsapp/updatestatus?status=6&id=' + message.id + '&moduleid=' + moduleid + '&moduletype=leads" style="font-size: 9px" class="change_message_status">Mark as Replied </a>');

             if (message.status == 0) {
               mark_read.appendTo(meta);
             }
             if (message.status == 0 || message.status == 5) {
               mark_replied.appendTo(meta);
             }
           }

           text.appendTo( row );


           if (tobottom) {
             row.appendTo(container);
           } else {
             row.prependTo(container);
           }
         }

                 return true;
     }
     function pollMessages(page = null, tobottom = null, addElapse = null) {
             var qs = "";
             qs += "/purchase?purchaseId=" + leadId;
             if (page) {
               qs += "&page=" + page;
             }
             if (addElapse) {
                 qs += "&elapse=3600";
             }
             var anyNewMessages = false;
             return new Promise(function(resolve, reject) {
                 $.getJSON("/whatsapp/pollMessages" + qs, function( data ) {

                     data.data.forEach(function( message ) {
                         var rendered = renderMessage( message, tobottom );
                         if ( !anyNewMessages && rendered ) {
                             anyNewMessages = true;
                         }
                     } );

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
         function scrollChatTop() {
             // console.log("scrollChatTop called");
             // var el = $(".chat-frame");
             // el.scrollTop(el[0].scrollHeight - el[0].clientHeight);
         }
     function startPolling() {
       setTimeout( function() {
                 pollMessages(null, null, addElapse).then(function() {
                     startPolling();
                 }, errorHandler);
             }, 1000);
     }
     function sendWAMessage() {
       var data = createMessageArgs();
             //var data = new FormData();
             //data.append("message", $("#waNewMessage").val());
             //data.append("lead_id", leadId );
       $.ajax({
         url: '/whatsapp/sendMessage/purchase',
         type: 'POST',
                 "dataType"    : 'text',           // what to expect back from the PHP script, if anything
                 "cache"       : false,
                 "contentType" : false,
                 "processData" : false,
                 "data": data
       }).done( function(response) {
         $('#waNewMessage').val('');
         pollMessages();
         // console.log("message was sent");
       }).fail(function(errObj) {
         alert("Could not send message");
       });
     }

     sendBtn.click(function() {
       sendWAMessage();
     } );
     startPolling();

     $(document).on('click', '.send-communication', function(e) {
       e.preventDefault();

       var thiss = $(this);
       var url = $(this).closest('form').attr('action');
       var token = "{{ csrf_token() }}";
       var file = $($(this).closest('form').find('input[type="file"]'))[0].files[0];
       var status = $(this).closest('form').find('input[name="status"]').val();
       var formData = new FormData();

       formData.append("_token", token);
       formData.append("image", file);
       formData.append("body", $(this).closest('form').find('textarea').val());
       formData.append("moduletype", $(this).closest('form').find('input[name="moduletype"]').val());
       formData.append("moduleid", $(this).closest('form').find('input[name="moduleid"]').val());
       formData.append("assigned_user", $(this).closest('form').find('input[name="assigned_user"]').val());
       formData.append("status", status);

       if (status == 4) {
         formData.append("assigned_user", $(this).closest('form').find('select[name="assigned_user"]').val());
       }

       if ($(this).closest('form')[0].checkValidity()) {
         $.ajax({
           type: 'POST',
           url: url,
           data: formData,
           processData: false,
           contentType: false
         }).done(function() {
           pollMessages();
           $(thiss).closest('form').find('textarea').val('');
         }).fail(function(response) {
           // console.log(response);
           alert('Error sending a message');
         });
       } else {
         $(this).closest('form')[0].reportValidity();
       }

     });

     $(document).on('click', '#load-more-messages', function() {
       var current_page = $(this).data('nextpage');
       $(this).data('nextpage', current_page + 1);
       var next_page = $(this).data('nextpage');
       $('#load-more-messages').text('Loading...');
       pollMessages(next_page, true);
       $('#load-more-messages').text('Load More');
     });
   });



   $('#addTaskButton').on('click', function () {
     var client_name = "";

     $('#task_subject').val(client_name);
   });

   $(document).on('click', '.change_message_status', function(e) {
     e.preventDefault();
     var url = $(this).data('url');
     var token = "{{ csrf_token() }}";
     var thiss = $(this);

     if ($(this).hasClass('wa_send_message')) {
       var message_id = $(this).data('messageid');
       var message = $('#message_body_' + message_id).find('p').data('message').trim();

       $.ajax({
         url: "{{ url('whatsapp/updateAndCreate') }}",
         type: 'POST',
         data: {
           _token: token,
           moduletype: "purchase",
           message_id: message_id
         },
         beforeSend: function() {
           $(thiss).text('Loading');
         }
       }).done( function(response) {
         // $(thiss).remove();
         // console.log(response);
       }).fail(function(errObj) {
         console.log(errObj);
         alert("Could not create whatsapp message");
       });
       // $('#waNewMessage').val(message);
       // $('#waMessageSend').click();
     }
       $.ajax({
         url: url,
         type: 'GET'
         // beforeSend: function() {
         //   $(thiss).text('Loading');
         // }
       }).done( function(response) {
         $(thiss).remove();
       }).fail(function(errObj) {
         alert("Could not change status");
       });



   });

   $(document).on('click', '.task-subject', function() {
     if ($(this).data('switch') == 0) {
       $(this).text($(this).data('details'));
       $(this).data('switch', 1);
     } else {
       $(this).text($(this).data('subject'));
       $(this).data('switch', 0);
     }
   });

   function addNewRemark(id){

     var formData = $("#add-new-remark").find('#add-remark').serialize();
     var remark = $('#remark-text_'+id).val();
     $.ajax({
         type: 'POST',
         headers: {
             'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
         },
         url: '{{ route('task.addRemark') }}',
         data: {id:id,remark:remark},
     }).done(response => {
         alert('Remark Added Success!')
         window.location.reload();
     });
   }

   $(".view-remark").click(function () {

     var taskId = $(this).attr('data-id');

       $.ajax({
           type: 'GET',
           headers: {
               'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
           },
           url: '{{ route('task.gettaskremark') }}',
           data: {id:taskId},
       }).done(response => {
           // console.log(response);

           var html='';

           $.each(response, function( index, value ) {

             html+=' <p> '+value.remark+' <br> <small>By ' + value.user_name + ' updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
             html+"<hr>";
           });
           $("#view-remark-list").find('#remark-list').html(html);
           // getActivity();
           //
           // $('#loading_activty').hide();
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

   $('.play-recording').on('click', function() {
     var url = $(this).data('url');
     var key = $(this).data('id');
     var recording = new Audio(url);
     // $(recording).attr('id', 'recording_' + key);
     // console.log(recording);

     // var pause_button = '<button type="button" class="btn btn-xs btn-secondary ml-3 stop-recording" data-id="' + key + '" data-button="' + recording + '">Stop Recording</button>';
     // $(this).parent().append(pause_button);

     recording.play();
   });

   // $(document).on('click', '.stop-recording', function() {
   //   var key = $(this).data('id');
   //   // var recording = $('#recording_' + key);
   //   var recording = $(this).data('button');
   //
   //   console.log(recording);
   //   $(recording).pause();
   //   $(recording).currentTime = 0;
   //
   //   $(this).remove();
   // });

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

   $('#submitButton').on('click', function(e) {
     e.preventDefault();

     var phone = $('input[name="contactno"]').val();

     if (phone.length != 0) {
       if (/^[91]{2}/.test(phone) != true) {
         $('input[name="contactno"]').val('91' + phone);
       }
     }

     $(this).closest('form').submit();
   });

    $('#change_status').on('change', function() {
      var token = "{{ csrf_token() }}";
      var status = $(this).val();
      var id = {{ $order->id }};

      $.ajax({
        url: '/purchase/' + id + '/changestatus',
        type: 'POST',
        data: {
          _token: token,
          status: status
        }
      }).done( function(response) {
        $('#change_status_message').fadeIn(400);
        setTimeout(function () {
          $('#change_status_message').fadeOut(400);
        }, 2000);
      }).fail(function(errObj) {
        alert("Could not change status");
      });
    });

    $(document).on('click', '.save-bill', function(e) {
      e.preventDefault();

      var thiss = $(this);
      var token = "{{ csrf_token() }}";
      var supplier = $('select[name="supplier"]').val();
      var bill_number = $('input[name="bill_number"]').val();
      var supplier_phone = $('input[name="supplier_phone"]').val();
      var whatsapp_number = $('select[name="whatsapp_number"]').val();
      var id = {{ $order->id }};

      $.ajax({
        url: '/purchase/' + id + '/saveBill',
        type: 'POST',
        data: {
          _token: token,
          bill_number: bill_number,
          supplier: supplier,
          supplier_phone: supplier_phone,
          whatsapp_number: whatsapp_number
        },
        beforeSend: function() {
          $(thiss).text('Saving');
        }
      }).done( function() {
        $(thiss).text('Save');

        $('#save_status').fadeIn(400);
        setTimeout(function () {
          $('#save_status').fadeOut(400);
        }, 2000);
      }).fail(function(errObj) {
        $(thiss).text('Save');
        alert("Could not save Bill number");
      });
    });
  </script>
@endsection
