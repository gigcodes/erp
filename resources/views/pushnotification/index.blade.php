@extends('layouts.app')


@section('content')
<div class="row">
  <div class="col-lg-12 margin-tb">
    <div class="float-left">
      <h2>Notifications</h2>
    </div>

  </div>
</div>


@if ($message = Session::get('success'))
<div class="alert alert-success">
  <p>{{ $message }}</p>
</div>
@endif

<div id="exTab2" class="container">
  <ul class="nav nav-tabs">
    <li class="active">
      <a href="#1" data-toggle="tab">Lead</a>
    </li>
    <li>
      <a href="#2" data-toggle="tab">Order</a>
    </li>
    <li>
      <a href="#3" data-toggle="tab">Message</a>
    </li>
    <li>
      <a href="#4" data-toggle="tab">Task</a>
    </li>
  </ul>
  <div class="tab-content ">
    <!-- Pending task div start -->
    <div class="tab-pane active" id="1">
      @if (count($lead_notifications) > 0)
        <table class="table notification-table">
          @foreach ($lead_notifications as $notification)
          <tr>
            <td>
              <a class="notification-link" href="{{ route('leads.show', $notification->model_id) }}">
                {{ $notification->message }} at {{ Carbon\Carbon::parse($notification->created_at)->format('d-m H:i') }}
              </a>
            </td>
            <td style="width: 20px"><button class="btn btn-link markReadPush" data-id="{{ $notification->id }}">Complete</button></td>
          </tr>
          @endforeach
        </table>
      @else
        <span class="d-block mt-3">You are up to date with Lead Notifications</span>
      @endif
    </div>

    <div class="tab-pane" id="2">
      @if (count($order_notifications) > 0)
        <table class="table notification-table">
          @foreach ($order_notifications as $notification)
          <tr>
            <td>
              <a class="notification-link" href="{{ route('order.show', $notification->model_id) }}">
                {{ $notification->message }} at {{ Carbon\Carbon::parse($notification->created_at)->format('d-m H:i') }}
              </a>
            </td>
            <td style="width: 20px"><button class="btn btn-link markReadPush" data-id="{{ $notification->id }}">Complete</button></td>
          </tr>
          @endforeach
        </table>
      @else
        <span class="d-block mt-3">You are up to date with Order Notifications</span>
      @endif
    </div>

    <div class="tab-pane" id="3">
      @if (count($message_notifications) > 0)
        <table class="table notification-table">
          @foreach ($message_notifications as $index => $notification)
            @foreach ($notification as $item)
              @if ($loop->first)
                <tr>
                  <td>
                    <a class="notification-link" href="{{ route('order.show', $item['model_id']) }}">
                      {{ $item['message'] }} at {{ Carbon\Carbon::parse($item['created_at'])->format('d-m H:i') }}
                    </a>
                  </td>
                  <td style="width: 20px"><button class="btn btn-link markReadPushReminder" data-id="{{ $item['id'] }}">Complete</button></td>
                </tr>
              @endif
            @endforeach
          @endforeach
        </table>
      @else
        <span class="d-block mt-3">You are up to date with Message Notifications</span>
      @endif
    </div>

    <div class="tab-pane" id="4">
      @if (count($task_notifications) > 0)
        <table class="table notification-table">
          @foreach ($task_notifications as $index => $notification)
            @foreach ($notification as $item)
              @if ($loop->first)
                <tr>
                  <td>
                    <a class="notification-link" href="{{ route('order.show', $item['model_id']) }}">
                      {{ $item['message'] }} at {{ Carbon\Carbon::parse($item['created_at'])->format('d-m H:i') }}
                    </a>
                  </td>
                  <td style="width: 20px"><button class="btn btn-link markReadPushReminder" data-id="{{ $item['id'] }}">Complete</button></td>
                </tr>
              @endif
            @endforeach
          @endforeach
        </table>
      @else
        <span class="d-block mt-3">You are up to date with Task Notifications</span>
      @endif
    </div>
  </div>
</div>

{{-- {!! $notifications->appends(Request::except('page'))->links() !!} --}}

<script type="text/javascript">
  $(document).on('click', '.markReadPush', function() {
    var button = $(this);
    var id = $(this).data('id');
    var url = '/pushNotificationMarkRead/' + id;

    markRead(url, button);
  });

  $(document).on('click', '.markReadPushReminder', function() {
    var button = $(this);
    var id = $(this).data('id');
    var url = '/pushNotificationMarkReadReminder/' + id;

    markRead(url, button);
  });

  function markRead(url, button) {
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:'POST',
      url: url,
      success: function(data) {
        if(data.msg === 'success'){
            button.parent().parent().fadeOut('ease-in');
        }
      },
      error: function() {
        alert('Could not mark as complete');
      }
    });
  }
</script>

@endsection
