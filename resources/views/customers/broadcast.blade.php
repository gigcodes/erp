@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection

@section('content')



    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Broadcast Messages</h2>
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
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($message = Session::get('warning'))
        <div class="alert alert-warning">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="table-responsive mt-3">
        <table class="table table-bordered">
            <thead>
              <th>Customer Name</th>
              <th>Phone</th>
              <th>Message</th>
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
                <td>{{ $message_queue->customer ? $message_queue->customer->phone : $message_queue->phone }}</td>
                <td>{{ json_decode($message_queue->data, true)['message'] }}</td>
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

    {!! $message_queues->links() !!}

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script type="text/javascript">
    $('#schedule-datetime').datetimepicker({
      format: 'YYYY-MM-DD'
    });
  </script>
@endsection
