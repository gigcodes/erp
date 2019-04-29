@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection

@section('content')



    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Broadcast Messages</h2>

            <div class="row mb-3">
              <div class="col">
                <h3>Last Set Sent: <span class="font-weight-bold">{{ $last_set_completed_count }}</span></h3>
              </div>
              <div class="col">
                <h3>Last Set Received: <span class="font-weight-bold">{{ $last_set_received_count }}</span></h3>
              </div>
            </div>

            <div class="pull-left">
                <form action="/broadcast/" method="GET">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-8">
                                {{-- <input name="term" type="text" class="form-control"
                                       value="{{ isset($term) ? $term : '' }}"
                                       placeholder="Search"> --}}
                             <div class="form-group">
                               <div class='input-group date' id='schedule-datetime'>
                                 <input type='text' class="form-control" name="sending_time" value="{{ $date }}" required />

                                 <span class="input-group-addon">
                                   <span class="glyphicon glyphicon-calendar"></span>
                                 </span>
                               </div>

                               @if ($errors->has('sending_time'))
                                   <div class="alert alert-danger">{{$errors->first('sending_time')}}</div>
                               @endif
                             </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="pull-right">
              {{-- @if ($last_stopped)
                <form action="{{ route('broadcast.restart') }}" method="POST">
                  @csrf

                  <button type="submit" class="btn btn-secondary">Restart Last Set</button>
                </form>
              @endif --}}

              <a href="{{ route('customer.whatsapp.stop.all') }}" class="btn btn-secondary">STOP ALL</a>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="form-group">
      <ul>
        @foreach ($message_groups as $group_id => $group)
          <li>
            <strong>Group ID {{ $group_id }}</strong>

            {{-- @php
              $sent_count = 0;
              $not_sent_count = 0;
            @endphp
            @foreach ($group as $sent => $data)
              @if ($sent == 0)
                @php
                  $not_sent_count = count($data[0]);
                @endphp

                @foreach ($data as $status => $items)
                  @if ($status == 0)
                    @php
                      $can_be_stopped = true;
                    @endphp
                  @else
                    @php
                      $can_be_stopped = false;
                    @endphp
                  @endif
                @endforeach
              @else
                @php
                  $sent_count = count($data[0]);
                @endphp
              @endif
            @endforeach --}}

            @if ($group['can_be_stopped'])
              <div class="my-1">
                <strong>Preview:</strong>
                {{ $group['message'] }}
                <div class="my-1">{{ $group['sent'] }} sent of {{ $group['total'] }}</div>
              </div>

              <form class="my-1" action="{{ route('broadcast.stop.group', $group_id) }}" method="POST">
                @csrf

                <button type="submit" class="btn btn-xs btn-secondary">Stop</button>
              </form>
            @else
              <div class="my-1">
                <strong>Preview:</strong>
                {{ $group['message'] }}
                <div class="my-1">{{ $group['sent'] }} sent of {{$group['total'] }}</div>
              </div>

              <form class="my-1" action="{{ route('broadcast.restart.group', $group_id) }}" method="POST">
                @csrf

                <button type="submit" class="btn btn-xs btn-secondary">Restart</button>
              </form>
            @endif
          </li>
        @endforeach
      </ul>
    </div>

    <div id="exTab2" class="container">
      <ul class="nav nav-tabs">
        <li class="active">
          <a href="#all-messages" data-toggle="tab">All Messages</a>
        </li>
        @if (count($last_set_completed) > 0)
          <li>
            <a href="#last-completed-messages" data-toggle="tab">Last Set Completed</a>
          </li>
        @endif
      </ul>
    </div>

    <div class="tab-content">
      <div class="tab-pane active mt-3" id="all-messages">
        <div class="table-responsive mt-3">
            <table class="table table-bordered">
                <thead>
                  <th>Customer Name</th>
                  <th>Phone</th>
                  <th>Whatsapp Number</th>
                  <th>Message</th>
                  <th>Group ID</th>
                  <th>Sent</th>
                  <th>Received</th>
                  <th>Status</th>
                  <th>Scheduled Date</th>
                  <th>Action</th>
                </thead>
                <tbody>
                @foreach ($message_queues as $key => $message_queue)
                  <tr>
                    <td>
                      @if ($message_queue->customer)
                        <a href="{{ route('customer.show', $message_queue->customer->id) }}" target="_blank">{{ $message_queue->customer->name }}</a>
                      @endif
                    </td>
                    <td>
                      @if ($message_queue->customer)
                        <div class="phone-container">
                          {{ $message_queue->customer->phone }}
                        </div>

                        <input type="number" name="phone" class="form-control phone-edit-input hidden" value="{{ $message_queue->customer->phone }}">
                        <a href="#" class="btn-link quick-edit-phone-button" data-id="{{ $message_queue->customer_id }}">Edit</a>
                      @else
                        {{ $message_queue->phone }}
                      @endif
                    </td>
                    <td>
                      {{ $message_queue->whatsapp_number }}
                    </td>
                    <td>{{ json_decode($message_queue->data, true)['message'] }}</td>
                    <td>{{ $message_queue->group_id }}</td>
                    <td>
                      @if ($message_queue->sent == 1)
                        <img src='/images/1.png' />
                      @endif
                    </td>
                    <td>
                      @if ($message_queue->customer && $message_queue->sent == 1)
                        @if ($message_queue->chat_message && $message_queue->chat_message->sent == 1)
                          <img src='/images/1.png' />
                        @endif
                      @endif
                    </td>
                    <td>
                      @if ($message_queue->status == 1)
                        Stopped
                      @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($message_queue->sending_time)->format('H:i d-m') }}</td>
                    <td>
                      @if (isset($message_queue->customer) && $message_queue->customer->do_not_disturb == 0)
                        <form action="{{ route('broadcast.donot.disturb', $message_queue->customer_id) }}" method="POST">
                          @csrf

                          <button type="submit" class="btn btn-xs btn-secondary">Do Not Disturb</button>
                        </form>
                      @elseif (isset($message_queue->customer) && $message_queue->customer->do_not_disturb == 1)
                        <span class="badge">Do Not Disturb</span>
                      @endif

                        {{-- <a class="btn btn-image" href="{{ route('customer.show', $customer->id) }}"><img src="/images/view.png" /></a>
                        <a class="btn btn-image" href="{{ route('customer.edit',$customer->id) }}"><img src="/images/edit.png" /></a>

                        {!! Form::open(['method' => 'DELETE','route' => ['customer.destroy', $customer->id],'style'=>'display:inline']) !!}
                        <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                        {!! Form::close() !!} --}}
                    </td>
                  </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {!! $message_queues->appends(Request::except('page'))->links() !!}
      </div>

      @if (count($last_set_completed) > 0)
        <div class="tab-pane mt-3" id="last-completed-messages">
          <div class="table-responsive mt-3">
              <table class="table table-bordered">
                  <thead>
                    <th>Customer Name</th>
                    <th>Phone</th>
                    <th>Message</th>
                    <th>Group ID</th>
                    <th>Sent</th>
                    <th>Received</th>
                    <th>Status</th>
                    <th>Scheduled Date</th>
                    <th>Action</th>
                  </thead>
                  <tbody>
                  @foreach ($last_set_completed as $key => $message_queue)
                    <tr>
                      <td>
                        @if ($message_queue->customer)
                          <a href="{{ route('customer.show', $message_queue->customer->id) }}" target="_blank">{{ $message_queue->customer->name }}</a>
                        @endif
                      </td>
                      <td>{{ $message_queue->customer ? $message_queue->customer->phone : $message_queue->phone }}</td>
                      <td>{{ json_decode($message_queue->data, true)['message'] }}</td>
                      <td>{{ $message_queue->group_id }}</td>
                      <td>
                        @if ($message_queue->sent == 1)
                          <img src='/images/1.png' />
                        @endif
                      </td>
                      <td>
                        @if ($message_queue->customer && $message_queue->sent == 1)
                          @if ($message_queue->chat_message && $message_queue->chat_message->sent == 1)
                            <img src='/images/1.png' />
                          @endif
                        @endif
                      </td>
                      <td>
                        @if ($message_queue->status == 1)
                          Stopped
                        @endif
                      </td>
                      <td>{{ \Carbon\Carbon::parse($message_queue->sending_time)->format('H:i d-m') }}</td>
                      <td>
                        @if (isset($message_queue->customer) && $message_queue->customer->do_not_disturb == 0)
                          <form action="{{ route('broadcast.donot.disturb', $message_queue->customer_id) }}" method="POST">
                            @csrf

                            <button type="submit" class="btn btn-xs btn-secondary">Do Not Disturb</button>
                          </form>
                        @elseif (isset($message_queue->customer) && $message_queue->customer->do_not_disturb == 1)
                          <span class="badge">Do Not Disturb</span>
                        @endif

                          {{-- <a class="btn btn-image" href="{{ route('customer.show', $customer->id) }}"><img src="/images/view.png" /></a>
                          <a class="btn btn-image" href="{{ route('customer.edit',$customer->id) }}"><img src="/images/edit.png" /></a>

                          {!! Form::open(['method' => 'DELETE','route' => ['customer.destroy', $customer->id],'style'=>'display:inline']) !!}
                          <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                          {!! Form::close() !!} --}}
                      </td>
                    </tr>
                  @endforeach
                  </tbody>
              </table>
          </div>

          {!! $last_set_completed->appends(Request::except('completed-page'))->links() !!}
        </div>
      @endif
    </div>

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script type="text/javascript">
    $('#schedule-datetime').datetimepicker({
      format: 'YYYY-MM-DD'
    });

    $(document).on('click', '.quick-edit-phone-button', function(e) {
      e.preventDefault();

      var id = $(this).data('id');

      $(this).siblings('.phone-edit-input').removeClass('hidden');
      $(this).siblings('.phone-container').addClass('hidden');

      $(this).siblings('.phone-edit-input').keypress(function(e) {
        var key = e.which;
        var thiss = $(this);

        if (key == 13) {
          e.preventDefault();
          var phone = $(thiss).val();

          $.ajax({
            type: 'POST',
            url: "{{ url('customer') }}/" + id + '/updatePhone',
            data: {
              _token: "{{ csrf_token() }}",
              phone: phone,
            }
          }).done(function() {
            $(thiss).addClass('hidden');
            $(thiss).siblings('.phone-container').text(phone);
            $(thiss).siblings('.phone-container').removeClass('hidden');
          }).fail(function(response) {
            console.log(response);

            alert('Could not update phone');
          });
        }
      });
    });
  </script>
@endsection
