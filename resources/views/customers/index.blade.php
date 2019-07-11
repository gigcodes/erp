@extends('layouts.app')

@section('title', 'Customer List')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('large_content')


    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Customers List ({{ count(json_decode($customer_ids_list)) }})</h2>
            <div class="pull-left">
              <form action="/customers/" method="GET" class="form-inline">
                <input name="term" type="text" class="form-control"
                       value="{{ isset($term) ? $term : '' }}"
                       placeholder="Search" id="customer-search">

                <div class="form-group ml-3">
                  <select class="form-control" name="type">
                    <optgroup label="Type">
                      <option value="">Select</option>
                      <optgroup label="Messages">
                        <option value="unread" {{ isset($type) && $type == 'unread' ? 'selected' : '' }}>Unread</option>
                        <option value="unapproved" {{ isset($type) && $type == 'unapproved' ? 'selected' : '' }}>Unapproved</option>
                      </optgroup>

                      <optgroup label="Leads">
                        <option value="0" {{ isset($type) && $type == '0' ? 'selected' : '' }}>No lead</option>
                        <option value="1" {{ isset($type) && $type == '1' ? 'selected' : '' }}>Cold</option>
                        <option value="2" {{ isset($type) && $type == '2' ? 'selected' : '' }}>Cold / Important</option>
                        <option value="3" {{ isset($type) && $type == '3' ? 'selected' : '' }}>Hot</option>
                        <option value="4" {{ isset($type) && $type == '4' ? 'selected' : '' }}>Very Hot</option>
                        <option value="5" {{ isset($type) && $type == '5' ? 'selected' : '' }}>Advance Follow Up</option>
                        <option value="6" {{ isset($type) && $type == '6' ? 'selected' : '' }}>High Priority</option>
                      </optgroup>

                      <optgroup label="Old">
                        <option value="new" {{ isset($type) && $type == 'new' ? 'selected' : '' }}>New</option>
                        <option value="delivery" {{ isset($type) && $type == 'delivery' ? 'selected' : '' }}>Delivery</option>
                        <option value="Refund to be processed" {{ isset($type) && $type == 'Refund to be processed' ? 'selected' : '' }}>Refund</option>
                      </optgroup>
                    </optgroup>
                  </select>
                </div>

                <div class="form-group ml-3">
                    {{-- <strong>Date Range</strong> --}}
                    <input type="text" value="" name="range_start" hidden/>
                    <input type="text" value="" name="range_end" hidden/>
                    <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i>
                    </div>
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

    @include('customers.partials.modal-category-brand')

    @include('partials.flash_messages')

    <?php
  	$query = http_build_query( Request::except( 'page' ) );
  	$query = url()->current() . ( ( $query == '' ) ? $query . '?page=' : '?' . $query . '&page=' );
  	?>

    <div class="form-group position-fixed" style="top: 50px; left: 20px;">
      Goto :
      <select onchange="location.href = this.value;" class="form-control" id="page-goto">
        @for($i = 1 ; $i <= $customers->lastPage() ; $i++ )
          <option value="{{ $query.$i }}" {{ ($i == $customers->currentPage() ? 'selected' : '') }}>{{ $i }}</option>
        @endfor
      </select>
    </div>

    <div class="card activity-chart my-3">
      <canvas id="leadsChart" style="height: 100px;"></canvas>
    </div>
    <div class="card activity-chart mt-2 p-5">
        <div class="progress">
            @foreach($order_stats as $order_stat)
                <div data-toggle="title" title="{{$order_stat[0]}}" class="progress-bar" role="progressbar" style="width:{{$order_stat[2]}}%; background-color: {{$order_stat[3]}}">
                    <a href="?type={{$order_stat[0]}}">{{$order_stat[1]}}</a>
                </div>
            @endforeach
        </div>
        <div style="font-size: 12px;">
            @foreach($order_stats as $order_stat)
                <div style="border-left: 15px solid {{$order_stat[3]}}; display: inline-block;padding: 5px;" class="mt-1">
                    <a href="?type={{$order_stat[0]}}">{{$order_stat[0]}} ({{$order_stat[1]}})</a>
                </div>
            @endforeach
        </div>
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
            <th width="15%"><a href="/customers{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=communication{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Communication</a></th>
            <th width="30%">Send Message</th>
            <th>Shortcuts</th>
            <th width="20%">Action</th>
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
                      @php
                        if ($customer->lead_status == 1) {
                          $customer_color = 'rgba(163,103,126,1)';
                        } else if ($customer->lead_status == 2) {
                          $customer_color = 'rgba(63,203,226,1)';
                        } else if ($customer->lead_status == 3) {
                          $customer_color = 'rgba(63,103,126,1)';
                        } else if ($customer->lead_status == 4) {
                          $customer_color = 'rgba(94, 80, 226, 1)';
                        } else if ($customer->lead_status == 5) {
                          $customer_color = 'rgba(58, 223, 140, 1)';
                        } else if ($customer->lead_status == 6) {
                          $customer_color = 'rgba(187, 221, 49, 1)';
                        } else {
                          $customer_color = 'rgba(207, 207, 211, 1)';
                        }
                      @endphp

                      <form class="d-inline" action="{{ route('customer.post.show', $customer->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="customer_ids" value="{{ $customer_ids_list }}">

                        <button type="submit" class="btn-link">{{ $customer->name }}</button>
                      </form>

                      <br>

                      {{ $customer->phone }}
                      {{-- <a href="{{ route('customer.show', $customer->id) }}?customer_ids={{ $customer_ids_list }}">{{ $customer->name }}</a> --}}

                      <button type="button" class="btn btn-image call-twilio" data-context="customers" data-id="{{ $customer->id }}" data-phone="{{ $customer->phone }}"><img src="/images/call.png" /></button>

                      @if ($customer->is_blocked == 1)
                        <button type="button" class="btn btn-image block-twilio" data-id="{{ $customer->id }}"><img src="/images/blocked-twilio.png" /></button>
                      @else
                        <button type="button" class="btn btn-image block-twilio" data-id="{{ $customer->id }}"><img src="/images/unblocked-twilio.png" /></button>
                      @endif


                      @if ($customer->is_flagged == 1)
                        <button type="button" class="btn btn-image flag-customer" data-id="{{ $customer->id }}"><img src="/images/flagged.png" /></button>
                      @else
                        <button type="button" class="btn btn-image flag-customer" data-id="{{ $customer->id }}"><img src="/images/unflagged.png" /></button>
                      @endif

                      @if ($customer->is_priority == 1)
                          <button type="button" class="btn btn-image priority-customer" data-id="{{ $customer->id }}"><img src="/images/customer-priority.png" /></button>
                      @else
                          <button type="button" class="btn btn-image priority-customer" data-id="{{ $customer->id }}"><img src="/images/customer-not-priority.png" /></button>
                      @endif

                      @php
                        $first_color = $customer_color == 'rgba(163,103,126,1)' ? 'active-bullet-status' : '';
                        $second_color = $customer_color == 'rgba(63,203,226,1)' ? 'active-bullet-status' : '';
                        $third_color = $customer_color == 'rgba(63,103,126,1)' ? 'active-bullet-status' : '';
                        $fourth_color = $customer_color == 'rgba(94, 80, 226, 1)' ? 'active-bullet-status' : '';
                        $fifth_color = $customer_color == 'rgba(58, 223, 140, 1)' ? 'active-bullet-status' : '';
                        $sixth_color = $customer_color == 'rgba(187, 221, 49, 1)' ? 'active-bullet-status' : '';
                        $seventh_color = $customer_color == 'rgba(207, 207, 211, 1)' ? 'active-bullet-status' : '';
                      @endphp

                      @if ($customer->lead_id != '')
                        <div class="">
                          <span class="user-status {{ $seventh_color }}" style="background-color: rgba(207, 207, 211, 1);"></span>
                          <span class="user-status change-lead-status {{ $first_color }}" data-toggle="tooltip" title="Cold Lead" data-id="1" data-leadid="{{ $customer->lead_id }}" style="cursor:pointer; background-color: #CCCCCC;"></span>
                          <span class="user-status change-lead-status {{ $second_color }}" data-toggle="tooltip" title="Cold / Important Lead" data-id="2" data-leadid="{{ $customer->lead_id }}" style="cursor:pointer; background-color: #95a5a6;"></span>
                          <span class="user-status change-lead-status {{ $third_color }}" data-toggle="tooltip" title="Hot Lead" data-id="3" data-leadid="{{ $customer->lead_id }}" style="cursor:pointer; background-color: #b2b2b2;"></span>
                          <span class="user-status change-lead-status {{ $fourth_color }}" data-toggle="tooltip" title="Very Hot Lead" data-id="4" data-leadid="{{ $customer->lead_id }}" style="cursor:pointer; background-color: #999999;"></span>
                          <span class="user-status change-lead-status {{ $fifth_color }}" data-toggle="tooltip" title="Advance Follow Up" data-id="5" data-leadid="{{ $customer->lead_id }}" style="cursor:pointer; background-color: #2c3e50;"></span>
                          <span class="user-status change-lead-status {{ $sixth_color }}" data-toggle="tooltip" title="High Priority" data-id="6" data-leadid="{{ $customer->lead_id }}" style="cursor:pointer; background-color: #7f7f7f;"></span>
                        </div>
                      @endif


                        @if (array_key_exists($customer->id, $orders))
                            @if (count($orders[$customer->id]) >= 1)
                                <?php
                                    $order = $orders[$customer->id][0];
                                ?>
                                <div>
                                    <span class="order-status change-order-status {{ $order['order_status'] == 'Follow up for advance' ? 'active-bullet-status' : '' }}" data-toggle="tooltip" title="Follow up for advance" data-id="Follow up for advance" data-orderid="{{ $order['id'] }}" style="cursor:pointer; background-color: #666666;"></span>
                                    <span class="order-status change-order-status {{ $order['order_status'] == 'Advance received' ? 'active-bullet-status' : '' }}" data-toggle="tooltip" title="Advance received" data-id="Advance received" data-orderid="{{ $order['id'] }}" style="cursor:pointer; background-color: #4c4c4c;"></span>
                                    <span class="order-status change-order-status {{ $order['order_status'] == 'Delivered' ? 'active-bullet-status' : '' }}" data-toggle="tooltip" title="Delivered" data-id="Delivered" data-orderid="{{ $order['id'] }}" style="cursor:pointer; background-color: #323232;"></span>
                                    <span class="order-status change-order-status {{ $order['order_status'] == 'Cancel' ? 'active-bullet-status' : '' }}" data-toggle="tooltip" title="Cancel" data-id="Cancel" data-orderid="{{ $order['id'] }}" style="cursor:pointer; background-color: #191919;"></span>
                                    <span class="order-status change-order-status {{ $order['order_status'] == 'Product shiped to Client' ? 'active-bullet-status' : '' }}" data-toggle="tooltip" title="Product shiped to Client" data-id="Product shiped to Client" data-orderid="{{ $order['id'] }}" style="cursor:pointer; background-color: #414a4c;"></span>
                                    <span class="order-status change-order-status {{ $order['order_status'] == 'Refund to be processed' ? 'active-bullet-status' : '' }}" data-toggle="tooltip" title="Refund to be processed" data-id="Refund to be processed" data-orderid="{{ $order['id'] }}" style="cursor:pointer; background-color: #CCCCCC;"></span>
                                    <span class="order-status change-order-status {{ $order['order_status'] == 'Refund Credited' ? 'active-bullet-status' : '' }}" data-toggle="tooltip" title="Refund Credited" data-id="Refund Credited" data-orderid="{{ $order['id'] }}" style="cursor:pointer; background-color: #95a5a6;"></span>
                                </div>
                            @endif
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


                          <div class="form-inline expand-row">
                            @if ($instructions[$customer->id][0]['is_priority'] == 1)
                              <strong class="text-danger mr-1">!</strong>
                            @endif

                              <div class="td-mini-container">
                                  {{ strlen($instructions[$customer->id][0]['instruction']) > 10 ? substr($instructions[$customer->id][0]['instruction'], 0, 10).'...' : $instructions[$customer->id][0]['instruction'] }}
                              </div>
                              <div class="td-full-container hidden">
                                  {{ $instructions[$customer->id][0]['instruction'] }}
                              </div>

                          </div>

                          @if ($instructions[$customer->id][0]['completed_at'])
                              <span style="color: #5e5e5e">{{ Carbon\Carbon::parse($instructions[$customer->id][0]['completed_at'])->format('d-m H:i') }}</span>
                          @else
                            <a href="#" class="btn-link complete-call" data-id="{{ $instructions[$customer->id][0]['id'] }}">Complete</a>
                          @endif

                          @if ($instructions[$customer->id][0]['completed_at'])
                                <strong style="color: #5e5e5e">Completed</strong>
                          @else
                            @if ($instructions[$customer->id][0]['pending'] == 0)
                              <a href="#" class="btn-link pending-call" data-id="{{ $instructions[$customer->id][0]['id'] }}">Mark as Pending</a>
                            @else
                              Pending
                            @endif
                          @endif
                        @endif

                        <textarea name="instruction" class="form-control quick-add-instruction-textarea hidden" rows="8" cols="80"></textarea>
                            <input title="Priority" class="hidden quick-priority-check" type="checkbox" name="instruction_priority" data-id="{{ $customer->id }}" id="instruction_priority_{{$customer->id}}">
                        <button type="button" class="btn-link quick-add-instruction" data-id="{{ $customer->id }}">Add Instruction</button>
                    </td>
                  @else
                    <td>
                      <textarea name="instruction" class="form-control quick-add-instruction-textarea hidden" rows="8" cols="80"></textarea>
                        <input title="Priority" class="hidden quick-priority-check" type="checkbox" name="instruction_priority" data-id="{{ $customer->id }}" id="instruction_priority_{{$customer->id}}">
                      <button type="button" class="btn-link quick-add-instruction" data-id="{{ $customer->id }}">Add Instruction</button>
                    </td>
                  @endif
                    <td>
                      {{-- @if (!empty($customer->message)) --}}
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

                              <a href data-url="/whatsapp/updatestatus?status=5&id={{ $customer->message_id }}" class='change_message_status'>Mark as Read</a>
                          @endif
                      {{-- @endif --}}
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
                            <strong>status:</strong>
                            <select name="status" class="form-control change_status order_status" data-orderid="{{ $orders[$customer->id][0]['id'] }}">
                                @php $order_status = (new \App\ReadOnly\OrderStatus)->all(); @endphp
                                @foreach($order_status as $key => $value)
                                    <option value="{{$value}}" {{$value == $orders[$customer->id][0]['order_status'] ? 'selected' : '' }}>{{ $key }}</option>
                                @endforeach
                            </select>
                            Has Multiple Orders
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
                      {{-- @if ($remark_message == '' || $remark_last_time < $customer->last_communicated_at) --}}
                        @if ($customer->message != '')
                          @if (strpos($customer->message, '<br>') !== false)
                            {{ substr($customer->message, 0, strpos($customer->message, '<br>')) }}
                          @else
                            {{ strlen($customer->message) > 100 ? substr($customer->message, 0, 97) . '...' : $customer->message }}
                          @endif
                        @else
                          @php $image_message = \App\ChatMessage::find($customer->message_id); @endphp

                          @if ($image_message && $image_message->hasMedia(config('constants.media_tags')))
                            <div class="image-container hidden">
                              @foreach ($image_message->getMedia(config('constants.media_tags')) as $image)
                                <div class="d-inline-block">
                                  <img src="{{ $image->getUrl() }}" class="img-responsive thumbnail-200" alt="">
                                </div>
                              @endforeach
                            </div>

                            <button type="button" class="btn btn-xs btn-secondary show-images-button">Show Images</button>
                          @endif
                        @endif

                        @if ($customer->is_error_flagged == 1)
                          <span class="btn btn-image"><img src="/images/flagged.png" /></span>
                        @endif

                        <button type="button" class="btn btn-xs btn-secondary load-more-communication" data-id="{{ $customer->id }}">Load More</button>

                        <ul class="more-communication-container">

                        </ul>
                      {{-- @else
                        {{ $remark_message }}
                      @endif --}}
                    </td>
                    <td>
                      <div class="d-inline form-inline">
                          <input style="width: 75%" type="text" class="form-control quick-message-field" name="message" placeholder="Message" value="">
                          <button style="display: inline;width: 20%" class="btn btn-sm btn-image send-message" data-customerid="{{ $customer->id }}"><img src="/images/filled-sent.png" /></button>
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
                        <input type="hidden" name="category_id" value="6">
                        <input type="hidden" name="assigned_to" value="{{ \App\Setting::get('image_shortcut') }}">

                        <button type="submit" class="btn btn-image quick-shortcut-button" title="Send Images"><img src="/images/attach.png" /></button>
                      </form>

                      <form class="d-inline" action="{{ route('instruction.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        <input type="hidden" name="instruction" value="Send price">
                        <input type="hidden" name="category_id" value="3">
                        <input type="hidden" name="assigned_to" value="{{ \App\Setting::get('price_shortcut') }}">

                        <button type="submit" class="btn btn-image quick-shortcut-button" title="Send Price"><img src="/images/price.png" /></button>
                      </form>

                      <form class="d-inline" action="{{ route('instruction.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        <input type="hidden" name="instruction" value="{{ $users_array[\App\Setting::get('call_shortcut')] }} call this client">
                        <input type="hidden" name="category_id" value="10">
                        <input type="hidden" name="assigned_to" value="{{ \App\Setting::get('call_shortcut') }}">

                        <button type="submit" class="btn btn-image quick-shortcut-button" title="Call this Client"><img src="/images/call.png" /></button>
                      </form>

                      <form class="d-inline" action="{{ route('instruction.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        <input type="hidden" name="instruction" value="Attach image">
                        <input type="hidden" name="category_id" value="8">
                        <input type="hidden" name="assigned_to" value="{{ \App\Setting::get('screenshot_shortcut') }}">

                        <button type="submit" class="btn btn-image quick-shortcut-button" title="Attach Images"><img src="/images/upload.png" /></button>
                      </form>

                      <form class="d-inline" action="{{ route('instruction.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        <input type="hidden" name="instruction" value="Attach screenshot">
                        <input type="hidden" name="category_id" value="12">
                        <input type="hidden" name="assigned_to" value="{{ \App\Setting::get('screenshot_shortcut') }}">

                        <button type="submit" class="btn btn-image quick-shortcut-button" title="Attach Screenshot"><img src="/images/screenshot.png" /></button>
                      </form>

                      <form class="d-inline" action="{{ route('instruction.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        <input type="hidden" name="instruction" value="Give details">
                        <input type="hidden" name="category_id" value="14">
                        <input type="hidden" name="assigned_to" value="{{ \App\Setting::get('details_shortcut') }}">

                        <button type="submit" class="btn btn-image quick-shortcut-button" title="Give Details"><img src="/images/details.png" /></button>
                      </form>

                      <form class="d-inline" action="{{ route('instruction.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        <input type="hidden" name="instruction" value="Check for the Purchase">
                        <input type="hidden" name="category_id" value="7">
                        <input type="hidden" name="assigned_to" value="{{ \App\Setting::get('purchase_shortcut') }}">

                        <button type="submit" class="btn btn-image quick-shortcut-button" title="Check for the Purchase"><img src="/images/purchase.png" /></button>
                      </form>

                      <form class="d-inline" action="{{ route('instruction.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        <input type="hidden" name="instruction" value="Please Show Client Chat">
                        <input type="hidden" name="category_id" value="13">
                        <input type="hidden" name="assigned_to" value="{{ \App\Setting::get('purchase_shortcut') }}">

                        <button type="submit" class="btn btn-image quick-shortcut-button" title="Show Client Chat"><img src="/images/chat.png" /></button>
                      </form>

                        <div class="d-inline">
                            <button type="button" class="btn btn-image send-instock-shortcut" data-id="{{ $customer->id }}">Send In Stock</button>
                        </div>

                        <div class="d-inline">
                            <button type="button" class="btn btn-image latest-scraped-shortcut" data-id="{{ $customer->id }}" data-toggle="modal" data-target="#categoryBrandModal">Send 20 Scraped</button>
                        </div>

                      <form class="d-inline" action="{{ route('instruction.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        <input type="hidden" name="instruction" value="Please show client chat to Yogesh">
                        <input type="hidden" name="category_id" value="13">
                        <input type="hidden" name="assigned_to" value="{{ \App\Setting::get('price_shortcut') }}">

                      </form>
                    </td>
                    <td>
                      <form class="d-inline" action="{{ route('customer.post.show', $customer->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="customer_ids" value="{{ $customer_ids_list }}">

                        <button type="submit" class="btn btn-image" href=""><img src="/images/view.png" /></button>
                      </form>

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
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js" type="text/javascript"></script>
  <script type="text/javascript">
    var searchSuggestions = {!! json_encode($search_suggestions, true) !!};

    var cached_suggestions = localStorage['message_suggestions'];
    var suggestions = [];

    $(window).scroll(function() {
      // var top = $(window).scrollTop();
      // var document_height = $(document).height();
      // var window_height = $(window).height();
      //
      // if (top >= (document_height - window_height - 200)) {
      //   if (can_load_more) {
      //     var current_page = $('#load-more-messages').data('nextpage');
      //     $('#load-more-messages').data('nextpage', current_page + 1);
      //     var next_page = $('#load-more-messages').data('nextpage');
      //     console.log(next_page);
      //     $('#load-more-messages').text('Loading...');
      //
      //     can_load_more = false;
      //
      //     pollMessages(next_page, true);
      //   }
      // }
      var next_page = $('.pagination li.active + li a');
      var page_number = next_page.attr('href').split('?page=');
      console.log(page_number);
      var current_page = page_number[1] - 1;

      $('#page-goto option[value="' + page_number[0] + '?page=' + current_page + '"]').attr('selected', 'selected');
    });

    $(document).on('click', '.change-order-status', function() {
        let orderId = $(this).attr('data-orderid');
        let status = $(this).attr('title');

        let url = '/order/'+orderId+'/changestatus';

        let thiss = $(this);

        $.ajax({
            url: url,
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                status: status
            },
            success: function() {
                toastr['success']('Status changed successfully!', 'Success');
                $(thiss).siblings('.change-order-status').removeClass('active-bullet-status');
                $(thiss).addClass('active-bullet-status');
                if (status == 'Product shiped to Client') {
                    $('#tracking-wrapper-' + id).css({'display' : 'block'});
                }
            }
        });
    });

    $(document).ready(function() {

        $('[data-toggle="tooltip"]').tooltip();

        $(document).on('click', '.expand-row', function() {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                // if ($(this).data('switch') == 0) {
                //   $(this).text($(this).data('details'));
                //   $(this).data('switch', 1);
                // } else {
                //   $(this).text($(this).data('subject'));
                //   $(this).data('switch', 0);
                // }
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });

      $('ul.pagination').hide();
      $(function() {
          $('.infinite-scroll').jscroll({
              autoTrigger: true,
              loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
              padding: 2500,
              nextSelector: '.pagination li.active + li a',
              contentSelector: 'div.infinite-scroll',
              callback: function() {
                  // $('ul.pagination').remove();
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

        if (!$(thiss).is(':disabled')) {
          $.ajax({
            type: "POST",
            url: "/whatsapp/approve/customer",
            data: {
              _token: "{{ csrf_token() }}",
              messageId: id
            },
            beforeSend: function() {
              $(thiss).attr('disabled', true);
              $(thiss).text('Approving...');
            }
          }).done(function(data) {
            $(thiss).parent().html('Approved');
          }).fail(function(response) {
            $(thiss).attr('disabled', false);
            $(thiss).text('Approve');

            console.log(response);
            alert(response.responseJSON.message);
          });
        }
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
          if (!$(thiss).is(':disabled')) {
            $.ajax({
              url: '/whatsapp/sendMessage/customer',
              type: 'POST',
             "dataType"    : 'json',           // what to expect back from the PHP script, if anything
             "cache"       : false,
             "contentType" : false,
             "processData" : false,
             "data": data,
             beforeSend: function() {
               $(thiss).attr('disabled', true);
             }
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

              // $.post( "/whatsapp/approve/customer", { messageId: response.message.id })
              //   .done(function( data ) {
              //
              //   }).fail(function(response) {
              //     console.log(response);
              //     alert(response.responseJSON.message);
              //   });

              $(thiss).attr('disabled', false);
            }).fail(function(errObj) {
              $(thiss).attr('disabled', false);

              alert("Could not send message");
              console.log(errObj);
            });
          }
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
            $(thiss).html('<img src="/images/blocked-twilio.png" />');
          } else {
            $(thiss).html('<img src="/images/unblocked-twilio.png" />');
          }
        }).fail(function(response) {
          $(thiss).html('<img src="/images/unblocked-twilio.png" />');

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

      $(document).on('click', '.priority-customer', function() {
        var customer_id = $(this).data('id');
        var thiss = $(this);

        $.ajax({
          type: "POST",
          url: "{{ route('customer.priority') }}",
          data: {
            _token: "{{ csrf_token() }}",
            customer_id: customer_id
          },
          beforeSend: function() {
            $(thiss).text('Prioritizing...');
          }
        }).done(function(response) {
          if (response.is_priority == 1) {
            $(thiss).html('<img src="/images/customer-priority.png" />');
          } else {
            $(thiss).html('<img src="/images/customer-not-priority.png" />');
          }

        }).fail(function(response) {
          $(thiss).html('<img src="/images/customer-not-priority.png" />');

          alert('Could not prioritize customer!');

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
          $(thiss).text('Send In Stock');

          alert('Could not sent instock!');

          console.log(response);
        });
      });

      $(document).on('click', '.quick-shortcut-button', function(e) {
        e.preventDefault();

        var customer_id = $(this).closest('form').find('input[name="customer_id"]').val();
        var instruction = $(this).closest('form').find('input[name="instruction"]').val();
        var category_id = $(this).closest('form').find('input[name="category_id"]').val();
        var assigned_to = $(this).closest('form').find('input[name="assigned_to"]').val();

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

      $(document).on('click', '.latest-scraped-shortcut', function() {
        var id = $(this).data('id');

        $('#categoryBrandModal').find('input[name="customer_id"]').val(id);
      });

      $('#sendScrapedButton').on('click', function(e) {
        e.preventDefault();

        var formData = $('#categoryBrandModal').find('form').serialize();
        var thiss = $(this);

        if (!$(this).is(':disabled')) {
          $.ajax({
            type: "POST",
            url: "{{ route('customer.send.scraped') }}",
            data: formData,
            beforeSend: function() {
              $(thiss).text('Sending...');
              $(thiss).attr('disabled', true);
            }
          }).done(function() {
            $('#categoryBrandModal').find('.close').click();
            $(thiss).text('Send');
            $(thiss).attr('disabled', false);
          }).fail(function(response) {
            $(thiss).text('Send');
            $(thiss).attr('disabled', false);
            console.log(response);

            alert('Could not send 20 images');
          });
        }
      });

      $(document).on('click', '.quick-add-instruction', function(e) {
        var id = $(this).data('id');

        $(this).siblings('.quick-add-instruction-textarea').removeClass('hidden');
        $(this).siblings('.quick-priority-check').removeClass('hidden');

        $(this).siblings('.quick-add-instruction-textarea').keypress(function(e) {
          var key = e.which;
          var thiss = $(this);
          let priority = $('#instruction_priority_'+id).is(':checked') ? 'on' : '';

          if (key == 13) {
            e.preventDefault();
            var instruction = $(thiss).val();

            $.ajax({
              type: 'POST',
              url: "{{ route('instruction.store') }}",
              data: {
                _token: "{{ csrf_token() }}",
                instruction: instruction,
                category_id: 1,
                customer_id: id,
                assigned_to: 7,
                is_priority: priority
              }
            }).done(function() {
              $(thiss).addClass('hidden');
                $('#instruction_priority_'+id).addClass('hidden');
              $(thiss).val('');
            }).fail(function(response) {
              console.log(response);

              alert('Could not create instruction');
            });
          }
        });
      });

      let r_s = "{{ $start_time }}";
      let r_e = "{{ $end_time }}";

      let start = r_s ? moment(r_s,'YYYY-MM-DD') : moment().subtract(6, 'days');
      let end =   r_e ? moment(r_e,'YYYY-MM-DD') : moment();

      jQuery('input[name="range_start"]').val();
      jQuery('input[name="range_end"]').val();

      function cb(start, end) {
          $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
      }

      $('#reportrange').daterangepicker({
          startDate: start,
          maxYear: 1,
          endDate: end,
          ranges: {
              'Today': [moment(), moment()],
              'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
              'Last 7 Days': [moment().subtract(6, 'days'), moment()],
              'Last 30 Days': [moment().subtract(29, 'days'), moment()],
              'This Month': [moment().startOf('month'), moment().endOf('month')],
              'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          }
      }, cb);

      cb(start, end);

      $('#reportrange').on('apply.daterangepicker', function(ev, picker) {

          jQuery('input[name="range_start"]').val(picker.startDate.format('YYYY-MM-DD'));
          jQuery('input[name="range_end"]').val(picker.endDate.format('YYYY-MM-DD'));

      });

      $(document).on('click', '.load-more-communication', function() {
        var thiss = $(this);
        var customer_id = $(this).data('id');

        $.ajax({
          type: "GET",
          url: "{{ url('customers') }}/" + customer_id + '/loadMoreMessages',
          data: {
            customer_id: customer_id
          },
          beforeSend: function() {
            $(thiss).text('Loading...');
          }
        }).done(function(response) {
          (response.messages).forEach(function(index) {
            var li = '<li>' + index + '</li>';

            $(thiss).closest('td').find('.more-communication-container').append(li);
          });

          $(thiss).remove();
        }).fail(function(response) {
          $(thiss).text('Load More');

          alert('Could not load more messages');

          console.log(response);
        });
      });

      $(document).on('click', '.show-images-button', function() {
        $(this).siblings('.image-container').toggleClass('hidden');
      });

      $(document).on('click', '.change_message_status', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        var thiss = $(this);

        $.ajax({
          url: url,
          type: 'GET',
          beforeSend: function () {
            $(thiss).text('Marking...');
          }
        }).done( function(response) {
          $(thiss).closest('tr').removeClass('text-danger');
          $(thiss).remove();
        }).fail(function(errObj) {
          $(thiss).text('Mark as Read');
          alert("Could not change status");
          console.log(errObj);
        });
      });


      let leadsChart = $('#leadsChart');

      var leadsChartExample = new Chart(leadsChart, {
          type: 'horizontalBar',
          data: {
              labels: [
                'Status'
              ],
              datasets: [{
                  label: "No Lead ({{ $leads_data[0]->total }})",
                  data: [{{ $leads_data[0]->total }}],
                  backgroundColor: "rgba(207, 207, 211, 1)",
                  hoverBackgroundColor: "rgba(189, 188, 194, 1)"
              },{
                  label: "Cold Lead ({{ $leads_data[1]->total }})",
                  data: [{{ $leads_data[1]->total }}],
                  backgroundColor: "rgba(163,103,126,1)",
                  hoverBackgroundColor: "rgba(140,85,100,1)"
              },{
                  label: 'Cold / Important Lead ({{ $leads_data[2]->total }})',
                  data: [{{ $leads_data[2]->total }}],
                  backgroundColor: "rgba(63,203,226,1)",
                  hoverBackgroundColor: "rgba(46,185,235,1)"
              },{
                  label: 'Hot Lead ({{ $leads_data[3]->total }})',
                  data: [{{ $leads_data[3]->total }}],
                  backgroundColor: "rgba(63,103,126,1)",
                  hoverBackgroundColor: "rgba(50,90,100,1)"
              },{
                  label: 'Very Hot Lead ({{ $leads_data[4]->total }})',
                  data: [{{ $leads_data[4]->total }}],
                  backgroundColor: "rgba(94, 80, 226, 1)",
                  hoverBackgroundColor: "rgba(74, 58, 223, 1)"
              },{
                  label: 'Advance Follow Up ({{ $leads_data[5]->total }})',
                  data: [{{ $leads_data[5]->total }}],
                  backgroundColor: "rgba(58, 223, 140, 1)",
                  hoverBackgroundColor: "rgba(34, 211, 122, 1)"
              },{
                  label: 'HIGH PRIORITY ({{ $leads_data[6]->total }})',
                  data: [{{ $leads_data[6]->total }}],
                  backgroundColor: "rgba(187, 221, 49, 1)",
                  hoverBackgroundColor: "rgba(175, 211, 34, 1)"
              }]
          },
          options: {
              scaleShowValues: true,
              responsive: true,
              scales: {
                xAxes: [{
                  ticks: {
                      beginAtZero:true,
                      fontFamily: "'Open Sans Bold', sans-serif",
                      fontSize:11
                  },
                  // display: true,
                  // scaleLabel: {
                  //   display: true,
                  //   labelString: 'Sets'
                  // }
                  stacked: true
                }],
                yAxes: [{
                  ticks: {
                      fontFamily: "'Open Sans Bold', sans-serif",
                      fontSize:11
                  },
                  // display: true,
                  // scaleLabel: {
                  //   display: true,
                  //   labelString: 'Count'
                  // }
                  stacked: true
                }]
              },
              tooltips: {
                enabled: false
              },
              animation: {
                onComplete: function () {
                  var chartInstance = this.chart;
                  var ctx = chartInstance.ctx;
                  ctx.textAlign = "left";
                  // ctx.font = this.scale.font;
                  ctx.fillStyle = "#fff";

                  // this.datasets.forEach(function (dataset) {
                  //   dataset.points.forEach(function (points) {
                  //     ctx.fillText(points.value, points.x, points.y - 10);
                  //   });
                  // })

                  Chart.helpers.each(this.data.datasets.forEach(function (dataset, i) {
                      var meta = chartInstance.controller.getDatasetMeta(i);
                      Chart.helpers.each(meta.data.forEach(function (bar, index) {
                          data = dataset.data[index];
                          if(i==0){
                              ctx.fillText(data, 50, bar._model.y+4);
                          } else {
                              ctx.fillText(data, bar._model.x-25, bar._model.y+4);
                          }
                      }),this)
                  }),this);
                }
            },
          }
      });

      $(document).on('keyup', 'add-new-note', function(event) {
          if (event.which != 13) {
              return;
          }



      });

      $(document).on('click', '.change-lead-status', function() {
        var id = $(this).data('id');
        var lead_id = $(this).data('leadid');
        var thiss = $(this);

        $.ajax({
          type: "POST",
          url: "{{ url('leads') }}/" + lead_id + "/changestatus",
          data: {
            _token: "{{ csrf_token() }}",
            status: id
          }
        }).done(function() {
          $(thiss).parent('div').children().each(function (index) {
            console.log(index);
            $(this).removeClass('active-bullet-status');
          });

          $(thiss).addClass('active-bullet-status');
        }).fail(function(response) {
          console.log(response);
          alert('Could not change lead status');
        });
      });
  </script>
@endsection
