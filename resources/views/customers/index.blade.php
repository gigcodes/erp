@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection

@section('content')


    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Customers List</h2>
            <div class="pull-left">
              <form action="/customers/" method="GET" class="form-inline">
                <input name="term" type="text" class="form-control"
                       value="{{ isset($term) ? $term : '' }}"
                       placeholder="Search" id="customer-search">

                <div class="form-group ml-3">
                  <select class="form-control" name="type">
                    <optgroup label="Type">
                      <option value="">Select</option>
                      <option value="new" {{ isset($type) && $type == 'new' ? 'selected' : '' }}>New</option>
                      <option value="delivery" {{ isset($type) && $type == 'delivery' ? 'selected' : '' }}>Delivery</option>
                      <option value="Refund to be processed" {{ isset($type) && $type == 'Refund to be processed' ? 'selected' : '' }}>Refund</option>
                      <option value="unread" {{ isset($type) && $type == 'unread' ? 'selected' : '' }}>Unread</option>
                    </optgroup>
                  </select>
                </div>

                <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
              </form>
            </div>

            <div class="pull-right mt-4">
                @if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('HOD of CRM'))
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#importCustomersModal">Import Customers</button>
                    {{-- <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#sendAllModal">Send Message to All</button> --}}
                @endif
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#mergeModal">Merge Customers</button>
                <a class="btn btn-secondary" href="{{ route('customer.create') }}">+</a>
            </div>
        </div>
    </div>

    @include('customers.partials.modal-merge')

    {{-- @include('customers.partials.modal-send-to-all') --}}

    @include('customers.partials.modal-import')

    @include('customers.partials.modal-shortcut')

    @include('partials.flash_messages')

    <?php
  	$query = http_build_query( Request::except( 'page' ) );
  	$query = url()->current() . ( ( $query == '' ) ? $query . '?page=' : '?' . $query . '&page=' );
  	?>

    <div class="form-group position-fixed" style="top: 50px; left: 20px;">
      Goto :
      <select onchange="location.href = this.value;" class="form-control">
        @for($i = 1 ; $i <= $customers->lastPage() ; $i++ )
          <option value="{{ $query.$i }}" {{ ($i == $customers->currentPage() ? 'selected' : '') }}>{{ $i }}</option>
        @endfor
      </select>
    </div>

    <div class="infinite-scroll">
      <div class="table-responsive mt-3">
        <table class="table table-bordered">
            <thead>
            <th width="15%"><a href="/customers{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=name{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Name</a></th>
            {{-- @if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('HOD of CRM'))
              <th><a href="/customers{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=email{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Email</a></th>
              <th><a href="/customers{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=phone{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Phone</a></th>
              <th><a href="/customers{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=instagram{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Instagram</a></th>
            @endif --}}
            {{-- <th width="10%"><a href="/customers{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=rating{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Lead Rating</a></th> --}}
            {{-- <th width="10%">Lead/Order Status</th> --}}
            {{-- <th width="5%"><a href="/customers{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=lead_created{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Lead Created at</a></th>
            <th width="5%"><a href="/customers{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=order_created{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Order Created at</a></th> --}}
            <th width="10%">Instruction</th>
            <th width="10%">Message Status</th>
            <th>Order Status</th>
            <th>Purchase Status</th>
            <th width="20%"><a href="/customers{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=communication{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Communication</a></th>
            <th width="30%">Send Message</th>
            <th>Shortcuts</th>
            <th width="15%">Action</th>
            </thead>
            <tbody>
            @foreach ($customers as $key => $customer)
                <tr class="
                {{ ((!empty($customer->message) && $customer->message_status == 0) || $customer->message_status == 1 || $customer->message_status == 5) ? 'row-highlight' : '' }}
                {{ (!empty($customer->message) && $customer->message_status == 0) ? 'text-danger' : '' }}
                {{ ($customer->order_status && ($customer->order_status != 'Cancel' && $customer->order_status != 'Delivered')) ? 'text-success' : '' }}
                {{ $customer->order_status ? '' : 'text-primary' }}
                        ">
                    <td>
                      <a href="{{ route('customer.show', $customer->id) }}">{{ $customer->name }}</a>

                      <button type="button" class="btn btn-image call-twilio" data-context="customers" data-id="{{ $customer->id }}" data-phone="{{ $customer->phone }}"><img src="/images/call.png" /></button>

                      @if ($customer->is_blocked == 1)
                        <span class="badge badge-secondary">Blocked</span>
                      @endif
                      <button type="button" class="btn btn-image block-twilio" data-id="{{ $customer->id }}"><img src="/images/call-blocked.png" /></button>

                      @if ($customer->is_flagged == 1)
                        <button type="button" class="btn btn-image flag-customer" data-id="{{ $customer->id }}"><img src="/images/flagged.png" /></button>
                      @else
                        <button type="button" class="btn btn-image flag-customer" data-id="{{ $customer->id }}"><img src="/images/unflagged.png" /></button>
                      @endif
                    </td>
                    {{-- @if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('HOD of CRM'))
                      <td>{{ $customer['email'] }}</td>
                      <td>{{ $customer['phone'] }}</td>
                      <td>{{ $customer['instahandler'] }}</td>
                    @endif --}}
                    {{-- <td>
                        {{ $customer->rating ?? 'N/A' }}
                    </td> --}}
                    {{-- <td>
                        @if ($customer->lead_status)
                            @php $status = array_flip((new \App\Status)->all()); @endphp
                            {{ $status[$customer->lead_status] }}
                        @endif
                        {{ $customer->order_status ? ' / ' : '' }}
                        @if ($customer->order_status)
                            {{ $customer->order_status }}
                        @endif
                    </td> --}}
                    {{-- <td>
                        {{ $customer->lead_created }}
                    </td>
                    <td>
                        @if ($customer->order_status)
                            {{ $customer->order_created }}
                        @endif
                    </td> --}}
                    @php
                      $remark_last_time = '';
                      $remark_message = '';
                    @endphp

                    @if (array_key_exists($customer->id, $instructions))
                      @if (!empty($instructions[$customer->id][0]['remarks']))
                        @php
                          $remark_last_time = $instructions[$customer->id][0]['remarks'][0]['created_at'];
                          $remark_message = $instructions[$customer->id][0]['remarks'][0]['remark'];
                        @endphp
                      @endif
                    <td class="{{ $instructions[$customer->id][0]['completed_at'] ? 'text-success' : 'text-danger' }}">
                        @if ($instructions[$customer->id][0]['assigned_to'])
                          {{ array_key_exists($instructions[$customer->id][0]['assigned_to'], $users_array) ? $users_array[$instructions[$customer->id][0]['assigned_to']] : 'No User' }} -


                          {{ $instructions[$customer->id][0]['instruction'] }}

                          @if ($instructions[$customer->id][0]['completed_at'])
                            {{ Carbon\Carbon::parse($instructions[$customer->id][0]['completed_at'])->format('d-m H:i') }}
                          @else
                            <a href="#" class="btn-link complete-call" data-id="{{ $instructions[$customer->id][0]['id'] }}">Complete</a>
                          @endif

                          @if ($instructions[$customer->id][0]['completed_at'])
                            Completed
                          @else
                            @if ($instructions[$customer->id][0]['pending'] == 0)
                              <a href="#" class="btn-link pending-call" data-id="{{ $instructions[$customer->id][0]['id'] }}">Mark as Pending</a>
                            @else
                              Pending
                            @endif
                          @endif
                        @endif
                    </td>
                  @else
                    <td></td>
                  @endif
                    <td>
                      @if ($remark_message == '' || $remark_last_time < $customer->last_communicated_at)
                        @if (!empty($customer->message))
                            @if ($customer->message_status == 5)
                                Read
                            @elseif ($customer->message_status == 6)
                                Replied
                            @elseif ($customer->message_status == 1)
                              <span>Waiting for Approval</span>
                              <button type="button" class="btn btn-xs btn-secondary approve-message" data-id="{{ $customer->message_id }}" data-type="{{ $customer->message_type }}">Approve</button>
                            @elseif ($customer->message_status == 2)
                                Approved
                            @elseif ($customer->message_status == 0)
                                Unread
                            @endif
                        @endif
                      @endif
                    </td>
                    <td>
                      @if (array_key_exists($customer->id, $orders))
                        @if (count($orders[$customer->id]) == 1)
                          <div class="form-group">
                            <strong>status:</strong>
                            <select name="status" class="form-control change_status order_status" data-orderid="{{ $orders[$customer->id][0]['id'] }}">
                                 @php $order_status = (new \App\ReadOnly\OrderStatus)->all(); @endphp
                                 @foreach($order_status as $key => $value)
                                  <option value="{{$value}}" {{$value == $orders[$customer->id][0]['order_status'] ? 'selected' : '' }}>{{ $key }}</option>
                                  @endforeach
                            </select>
                            <span class="text-success change_status_message" style="display: none;">Successfully changed status</span>
                          </div>
                        @else
                          Multiple Orders
                        @endif
                      @else
                        No Orders
                      @endif
                    </td>
                    <td>
                      @if (array_key_exists($customer->id, $orders))
                        @if ($customer->purchase_status != null)
                          {{ $customer->purchase_status }}
                        @else
                          No Purchase
                        @endif
                      @endif
                    </td>
                    <td>
                      @if ($remark_message == '' || $remark_last_time < $customer->last_communicated_at)
                        @if (isset($customer->message))
                            @if (strpos($customer->message, '<br>') !== false)
                                {{ substr($customer->message, 0, strpos($customer->message, '<br>')) }}
                            @else
                                {{ strlen($customer->message) > 100 ? substr($customer->message, 0, 97) . '...' : $customer->message }}
                            @endif
                        @else
                            {{ strlen($customer->message) > 100 ? substr($customer->message, 0, 97) . '...' : $customer->message }}
                        @endif
                      @else
                        {{ $remark_message }}
                      @endif
                    </td>
                    <td>
                      <div class="d-inline">
                        <input type="text" class="form-control quick-message-field" name="message" placeholder="Message" value="">
                        <button class="btn btn-sm btn-image send-message" data-customerid="{{ $customer->id }}"><img src="/images/filled-sent.png" /></button>
                      </div>

                      <p class="pb-4 mt-3" style="display: block;">
                        <select name="quickCategory" class="form-control mb-3 quickCategory">
                          <option value="">Select Category</option>
                          @foreach($reply_categories as $category)
                              <option value="{{ $category->approval_leads }}">{{ $category->name }}</option>
                          @endforeach
                        </select>

                        <select name="quickComment" class="form-control quickComment">
                          <option value="">Quick Reply</option>}}
                        </select>
                      </p>
                    </td>
                    <td>
                      {{-- <button type="button" class="btn btn-image" data-id="{{ $customer->id }}" data-instruction="Send images"><img src="/images/attach.png" /></button> --}}
                      {{-- <button type="button" class="btn btn-image" data-id="{{ $customer->id }}" data-instruction="Send price">$</button> --}}
                      <form class="d-inline" action="{{ route('instruction.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        <input type="hidden" name="instruction" value="Send images">
                        <input type="hidden" name="category_id" value="1">
                        <input type="hidden" name="assigned_to" value="{{ \App\Setting::get('image_shortcut') }}">

                        <button type="submit" class="btn btn-image quick-shortcut-button"><img src="/images/attach.png" /></button>
                      </form>

                      <form class="d-inline" action="{{ route('instruction.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        <input type="hidden" name="instruction" value="Send price">
                        <input type="hidden" name="category_id" value="1">
                        <input type="hidden" name="assigned_to" value="{{ \App\Setting::get('price_shortcut') }}">

                        <button type="submit" class="btn btn-image quick-shortcut-button">$</button>
                      </form>

                      <form class="d-inline" action="{{ route('instruction.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        <input type="hidden" name="instruction" value="{{ $users_array[\App\Setting::get('call_shortcut')] }} call this client">
                        <input type="hidden" name="category_id" value="1">
                        <input type="hidden" name="assigned_to" value="{{ \App\Setting::get('call_shortcut') }}">

                        <button type="submit" class="btn btn-image quick-shortcut-button"><img src="/images/call.png" /></button>
                      </form>

                      <form class="d-inline" action="{{ route('instruction.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        <input type="hidden" name="instruction" value="Attach image or screenshot physically">
                        <input type="hidden" name="category_id" value="1">
                        <input type="hidden" name="assigned_to" value="{{ \App\Setting::get('screenshot_shortcut') }}">

                        <button type="submit" class="btn btn-image quick-shortcut-button"><img src="/images/upload.png" /></button>
                      </form>

                      <form class="d-inline" action="{{ route('instruction.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        <input type="hidden" name="instruction" value="Give details">
                        <input type="hidden" name="category_id" value="1">
                        <input type="hidden" name="assigned_to" value="{{ \App\Setting::get('details_shortcut') }}">

                        <button type="submit" class="btn btn-image quick-shortcut-button">Details</button>
                      </form>

                      <form class="d-inline" action="{{ route('instruction.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        <input type="hidden" name="instruction" value="Check for the Purchase">
                        <input type="hidden" name="category_id" value="1">
                        <input type="hidden" name="assigned_to" value="{{ \App\Setting::get('purchase_shortcut') }}">

                        <button type="submit" class="btn btn-image quick-shortcut-button">Check Purchase</button>
                      </form>

                      <div class="d-inline">
                        <button type="button" class="btn btn-image send-instock-shortcut" data-id="{{ $customer->id }}">Send In Stock</button>
                      </div>
                    </td>
                    <td>
                        <a class="btn btn-image" href="{{ route('customer.show', $customer->id) }}"><img src="/images/view.png" /></a>
                        <a class="btn btn-image" href="{{ route('customer.edit',$customer->id) }}" target="_blank"><img src="/images/edit.png" /></a>

                        {!! Form::open(['method' => 'DELETE','route' => ['customer.destroy', $customer->id],'style'=>'display:inline']) !!}
                        <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <form action="{{ route('attachImages', ['customers']) }}" id="attachImagesForm" method="GET">
      <input type="hidden" name="message" id="attach_message" value="">
      <input type="hidden" name="sending_time" id="attach_sending_time" value="">
    </form>

    {!! $customers->appends(Request::except('page'))->links() !!}
  </div>

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
  <script type="text/javascript">
    var searchSuggestions = {!! json_encode($search_suggestions, true) !!};

    var cached_suggestions = localStorage['message_suggestions'];
    var suggestions = [];

    $(document).ready(function() {
      $('ul.pagination').hide();
      $(function() {
          $('.infinite-scroll').jscroll({
              autoTrigger: true,
              loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
              padding: 2500,
              nextSelector: '.pagination li.active + li a',
              contentSelector: 'div.infinite-scroll',
              callback: function() {
                  $('ul.pagination').remove();
              }
          });
      });
    });

    $(document).ready(function() {
       $(".select-multiple").multiselect();
    });

    $(document).ready(function() {
      $('#customer-search').autocomplete({
        source: function(request, response) {
          var results = $.ui.autocomplete.filter(searchSuggestions, request.term);

          response(results.slice(0, 10));
        }
      });

      $('.quick-message-field').autocomplete({
        source: function(request, response) {
          var results = $.ui.autocomplete.filter(JSON.parse(cached_suggestions), request.term);

          response(results.slice(0, 10));
        }
      });
    });

      $('.load-customers').on('click', function() {
          var thiss = $(this);
          var first_customer = $('#first_customer').val();
          var second_customer = $('#second_customer').val();

          if (first_customer == second_customer) {
              alert('You selected the same customers');

              return;
          }

          $.ajax({
              type: "GET",
              url: "{{ route('customer.load') }}",
              data: {
                  first_customer: first_customer,
                  second_customer: second_customer
              },
              beforeSend: function() {
                  $(thiss).text('Loading...');
              }
          }).done(function(response) {
              $('#first_customer_id').val(response.first_customer.id);
              $('#second_customer_id').val(response.second_customer.id);

              $('#first_customer_name').val(response.first_customer.name);
              $('#first_customer_email').val(response.first_customer.email);
              $('#first_customer_phone').val(response.first_customer.phone ? (response.first_customer.phone).replace(/[\s+]/g, '') : '');
              $('#first_customer_instahandler').val(response.first_customer.instahandler);
              $('#first_customer_rating').val(response.first_customer.rating);
              $('#first_customer_address').val(response.first_customer.address);
              $('#first_customer_city').val(response.first_customer.city);
              $('#first_customer_country').val(response.first_customer.country);
              $('#first_customer_pincode').val(response.first_customer.pincode);

              $('#second_customer_name').val(response.second_customer.name);
              $('#second_customer_email').val(response.second_customer.email);
              $('#second_customer_phone').val(response.second_customer.phone ? (response.second_customer.phone).replace(/[\s+]/g, '') : '');
              $('#second_customer_instahandler').val(response.second_customer.instahandler);
              $('#second_customer_rating').val(response.second_customer.rating);
              $('#second_customer_address').val(response.second_customer.address);
              $('#second_customer_city').val(response.second_customer.city);
              $('#second_customer_country').val(response.second_customer.country);
              $('#second_customer_pincode').val(response.second_customer.pincode);

              $('#customers-data').show();
              $('#mergeButton').prop('disabled', false);
              $(thiss).text('Load Data');
          }).fail(function(response) {
              console.log(response);
              alert('There was error loading customers data');
          });
      });

      $(document).on('click', '.attach-images-btn', function(e) {
        e.preventDefault();

        $('#attach_message').val($('#message_to_all_field').val());
        $('#attach_sending_time').val($('#sending_time_field').val());

        $('#attachImagesForm').submit();
      });

      $('#schedule-datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
      });

      $(document).on('click', '.approve-message', function() {
        var thiss = $(this);
        var id = $(this).data('id');
        var type = $(this).data('type');

        // if (isNaN(type)) {
        //   $.ajax({
        //     url: "{{ url('whatsapp/updateAndCreate') }}",
        //     type: 'POST',
        //     data: {
        //       _token: "{{ csrf_token() }}",
        //       moduletype: "customer",
        //       message_id: id
        //     },
        //     beforeSend: function() {
        //       $(thiss).text('Approving...');
        //     }
        //   }).done( function(response) {
        //     $(thiss).parent().html('Approved');
        //   }).fail(function(errObj) {
        //     $(thiss).text('Approve');
        //     console.log(errObj);
        //     alert("Could not create whatsapp message");
        //   });
        // }
        // else {
          $.post("/whatsapp/approve/customer", {messageId: id})
            .done(function(data) {
              $(thiss).parent().html('Approved');
            }).fail(function(response) {
              console.log(response);
              alert(response.responseJSON.message);
            });
        // }
      });

      $(document).on('click', '.create-shortcut', function() {
        var id = $(this).data('id');
        var instruction = $(this).data('instruction');

        $('#customer_id_field').val(id);
        $('#instruction_field').val(instruction);
      });

      $(document).on('click', '.complete-call', function(e) {
        e.preventDefault();

        var thiss = $(this);
        var token = "{{ csrf_token() }}";
        var url = "{{ route('instruction.complete') }}";
        var id = $(this).data('id');

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
          $(thiss).parent().append(moment(response.time).format('DD-MM HH:mm'));
          $(thiss).remove();
        }).fail(function(errObj) {
          console.log(errObj);
          alert("Could not mark as completed");
        });
      });

      $(document).on('click', '.pending-call', function(e) {
        e.preventDefault();

        var thiss = $(this);
        var token = "{{ csrf_token() }}";
        var url = "{{ route('instruction.pending') }}";
        var id = $(this).data('id');

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
          $(thiss).parent().append('Pending');
          $(thiss).remove();
        }).fail(function(errObj) {
          console.log(errObj);
          alert("Could not mark as completed");
        });
      });

      $(document).on('click', '.send-message', function() {
        var thiss = $(this);
        var data = new FormData();
        var customer_id = $(this).data('customerid');
        var message = $(this).siblings('input').val();

        data.append("customer_id", customer_id);
        data.append("message", message);
        data.append("status", 1);

        if (message.length > 0) {
          $.ajax({
            url: '/whatsapp/sendMessage/customer',
            type: 'POST',
           "dataType"    : 'json',           // what to expect back from the PHP script, if anything
           "cache"       : false,
           "contentType" : false,
           "processData" : false,
           "data": data
         }).done( function(response) {
            $(thiss).siblings('input').val('');

            if (cached_suggestions) {
              suggestions = JSON.parse(cached_suggestions);

              if (suggestions.length == 10) {
                suggestions.push(message);
                suggestions.splice(0, 1);
              } else {
                suggestions.push(message);
              }
              localStorage['message_suggestions'] = JSON.stringify(suggestions);
              cached_suggestions = localStorage['message_suggestions'];

              console.log('EXISTING');
              console.log(suggestions);
            } else {
              suggestions.push(message);
              localStorage['message_suggestions'] = JSON.stringify(suggestions);
              cached_suggestions = localStorage['message_suggestions'];

              console.log('NOT');
              console.log(suggestions);
            }

            $.post( "/whatsapp/approve/customer", { messageId: response.message.id })
              .done(function( data ) {

              }).fail(function(response) {
                console.log(response);
                alert(response.responseJSON.message);
              });
          }).fail(function(errObj) {
            alert("Could not send message");
            console.log(errObj);
          });
        } else {
          alert('Please enter a message first');
        }
      });

      $(document).on('change', '.quickCategory', function() {
        var replies = JSON.parse($(this).val());
        var thiss = $(this);

        $(this).siblings('.quickComment').empty();

        $(this).siblings('.quickComment').append($('<option>', {
          value: '',
          text: 'Quick Reply'
        }));

        replies.forEach(function(reply) {
          $(thiss).siblings('.quickComment').append($('<option>', {
            value: reply.reply,
            text: reply.reply
          }));
        });
      });

      $(document).on('change', '.quickComment', function () {
          $(this).closest('td').find('input').val($(this).val());
      });

      $('.change_status').on('change', function() {
        var thiss = $(this);
        var token = "{{ csrf_token() }}";
        var status = $(this).val();


        if ($(this).hasClass('order_status')) {
          var id = $(this).data('orderid');
          var url = '/order/' + id + '/changestatus';
        } else {
          var id = $(this).data('leadid');
          var url = '/leads/' + id + '/changestatus';
        }

        $.ajax({
          url: url,
          type: 'POST',
          data: {
            _token: token,
            status: status
          }
        }).done( function(response) {
          if ($(thiss).hasClass('order_status') && status == 'Product shiped to Client') {
            $('#tracking-wrapper-' + id).css({'display' : 'block'});
          }

          $(thiss).siblings('.change_status_message').fadeIn(400);

          setTimeout(function () {
            $(thiss).siblings('.change_status_message').fadeOut(400);
          }, 2000);
        }).fail(function(errObj) {
          alert("Could not change status");
        });
      });

      $(document).on('click', '.block-twilio', function() {
        var customer_id = $(this).data('id');
        var thiss = $(this);

        $.ajax({
          type: "POST",
          url: "{{ route('customer.block') }}",
          data: {
            _token: "{{ csrf_token() }}",
            customer_id: customer_id
          },
          beforeSend: function() {
            $(thiss).text('Blocking...');
          }
        }).done(function(response) {
          if (response.is_blocked == 1) {
            var badge = $('<span class="badge badge-secondary">Blocked</span>');

            $(thiss).parent().append(badge);
            $(thiss).html('<img src="/images/call-blocked.png" />');
          } else {
            $(thiss).html('<img src="/images/call-blocked.png" />');
            $(thiss).parent().find('.badge').remove();
          }

          // $(thiss).remove();
        }).fail(function(response) {
          $(thiss).text('Block on Twilio');

          alert('Could not block customer!');

          console.log(response);
        });
      });

      $(document).on('click', '.flag-customer', function() {
        var customer_id = $(this).data('id');
        var thiss = $(this);

        $.ajax({
          type: "POST",
          url: "{{ route('customer.flag') }}",
          data: {
            _token: "{{ csrf_token() }}",
            customer_id: customer_id
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

          alert('Could not flag customer!');

          console.log(response);
        });
      });

      $(document).on('click', '.send-instock-shortcut', function() {
        var customer_id = $(this).data('id');
        var thiss = $(this);

        $.ajax({
          type: "POST",
          url: "{{ route('customer.send.instock') }}",
          data: {
            _token: "{{ csrf_token() }}",
            customer_id: customer_id
          },
          beforeSend: function() {
            $(thiss).text('Sending...');
          }
        }).done(function(response) {
          $(thiss).text('Send In Stock');
        }).fail(function(response) {
          $(thiss).text('Block on Twilio');

          alert('Could not block customer!');

          console.log(response);
        });
      });

      $(document).on('click', '.quick-shortcut-button', function(e) {
        e.preventDefault();

        var customer_id = $(this).parent().find('input[name="customer_id"]').val();
        var instruction = $(this).parent().find('input[name="instruction"]').val();
        var category_id = $(this).parent().find('input[name="category_id"]').val();
        var assigned_to = $(this).parent().find('input[name="assigned_to"]').val();

        $.ajax({
          type: "POST",
          url: "{{ route('instruction.store') }}",
          data: {
            _token: "{{ csrf_token() }}",
            customer_id: customer_id,
            instruction: instruction,
            category_id: category_id,
            assigned_to: assigned_to,
          },
          beforeSend: function() {

          }
        }).done(function(response) {

        }).fail(function(response) {
          alert('Could not execute shortcut!');

          console.log(response);
        });
      });
  </script>
@endsection
