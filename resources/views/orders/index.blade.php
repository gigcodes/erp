@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Orders List</h2>
            <div class="pull-left">

                <form action="/order/" method="GET">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <input name="term" type="text" class="form-control"
                                       value="{{ isset($term) ? $term : '' }}"
                                       placeholder="Search">
                            </div>
                            <div class="col-md-4">
                                <button hidden type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('order.create') }}">+</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered">
        <tr>
            <th style="width: 8%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=id{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">ID</a></th>
            <th style="width: 8%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=date{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Date</a></th>
            <th style="width: 12%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=order_handler{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Order Handler</a></th>
            <th style="width: 12%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=client_name{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Client Name</a></th>
            <th style="width: 8%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=status{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Order Status</a></th>
            <th style="width: 5%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=action{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Action Status</a></th>
            <th style="width: 8%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=due{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Due</a></th>
            <th style="width: 8%">Message Status</th>
            <th style="width: 20%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=communication{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Communication</a></th>
            <th style="width: 15%">Action</th>
        </tr>
        @foreach ($orders_array as $key => $order)
            <tr class="{{ \App\Helpers::statusClass($order['assign_status'] ) }} {{ ((!empty($order['communication']['body']) && $order['communication']['status'] == 0) || $order['communication']['status'] == 1 || $order['communication']['status'] == 5) ? 'row-highlight' : '' }} {{ ((!empty($order['communication']['message']) && $order['communication']['status'] == 0) || $order['communication']['status'] == 1 || $order['communication']['status'] == 5) ? 'row-highlight' : '' }}">
                <td>{{ $order['order_id'] }}</td>
                <td>{{ Carbon\Carbon::parse($order['order_date'])->format('d-m') }}</td>
                <td>{{ $order['sales_person'] ? $users[$order['sales_person']] : 'nil' }}</td>
                <td>{{ $order['client_name'] }}</td>
                <td>{{ $order['order_status']}}</td>
                <td>{{ $order['action']['status'] }}</td>
                <td>{{ $order['action']['completion_date'] ? Carbon\Carbon::parse($order['action']['completion_date'])->format('d-m') : '' }}</td>
                <td>
                  @if (!empty($order['communication']['body']))
                    @if ($order['communication']['status'] == 5 || $order['communication']['status'] == 3)
                      Read
                    @elseif ($order['communication']['status'] == 6)
                      Replied
                    @elseif ($order['communication']['status'] == 1)
                      <span>Awaiting Approval</span>
                      {{-- <a href data-url="/message/updatestatus?status=2&id={{ $order['communication']['id'] }}&moduleid={{ $order['communication']['moduleid'] }}&moduletype={{ $order['communication']['moduletype'] }}" style="font-size: 9px" class="change_message_status">Approve</a> --}}
                    @elseif ($order['communication']['status'] == 2)
                      Approved
                    @elseif ($order['communication']['status'] == 4)
                      Internal Message
                    @elseif ($order['communication']['status'] == 0)
                      Unread
                    @endif
                  @endif

                  @if (!empty($order['communication']['message']))
                    @if ($order['communication']['status'] == 5)
                      Read
                    @elseif ($order['communication']['status'] == 6)
                      Replied
                    @elseif ($order['communication']['status'] == 1)
                      <span>Awaiting Approval</span>
                      {{-- <a href data-url="/whatsapp/approve/orders?messageId={{ $order['communication']['id'] }}" style="font-size: 9px" class="change_message_status approve-whatsapp" data-messageid="{{ $order['communication']['id'] }}">Approve</a> --}}
                    @elseif ($order['communication']['status'] == 2)
                      Approved
                    @elseif ($order['communication']['status'] == 0)
                      Unread
                    @endif
                  @endif
                </td>
                <td>
                  {{-- @if (strpos(App\Helpers::getlatestmessage($order->id, 'order'), '<br>') !== false)
                    {{ substr(App\Helpers::getlatestmessage($order->id, 'order'), 0, strpos(App\Helpers::getlatestmessage($order->id, 'order'), '<br>')) }}
                  @else
                    {{ App\Helpers::getlatestmessage($order->id, 'order') }}
                  @endif --}}
                  @if (isset($order['communication']['body']))
                    @if (strpos($order['communication']['body'], '<br>') !== false)
                      {{ substr($order['communication']['body'], 0, strpos($order['communication']['body'], '<br>')) }}
                    @else
                      {{ $order['communication']['body'] }}
                    @endif
                  @else
                    {{ $order['communication']['message'] }}
                  @endif
                </td>
                <td>
                    <a class="btn btn-image" href="{{ route('order.show',$order['id']) }}"><img src="/images/view.png" /></a>
                    {{-- @can('order-edit')
                    <a class="btn btn-image" href="{{ route('order.edit',$order['id']) }}"><img src="/images/edit.png" /></a>
                    @endcan --}}

                    {!! Form::open(['method' => 'DELETE','route' => ['order.destroy', $order['id']],'style'=>'display:inline']) !!}
                    <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
                    {!! Form::close() !!}

                    @can('order-delete')
                        {!! Form::open(['method' => 'DELETE','route' => ['order.permanentDelete', $order['id']],'style'=>'display:inline']) !!}
                        <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                        {!! Form::close() !!}
                    @endcan
                </td>
            </tr>
        @endforeach
    </table>
    </div>

    {!! $orders_array->appends(Request::except('page'))->links() !!}
    {{--{!! $orders->links() !!}--}}

    <script type="text/javascript">
    $(document).on('click', '.change_message_status', function(e) {
      e.preventDefault();
      var url = $(this).data('url');
      var thiss = $(this);
      var type = 'GET';

      if ($(this).hasClass('approve-whatsapp')) {
        type = 'POST';
      }

        $.ajax({
          url: url,
          type: type,
          beforeSend: function() {
            $(thiss).text('Loading');
          }
        }).done( function(response) {
          $(thiss).closest('tr').removeClass('row-highlight');
          $(thiss).prev('span').text('Approved');
          $(thiss).remove();
        }).fail(function(errObj) {
          alert("Could not change status");
        });
    });
    </script>
@endsection
