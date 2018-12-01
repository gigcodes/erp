@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Orders List</h2>

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

    <table class="table table-bordered">
        <tr>
            <th><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=id{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">ID</a></th>
            <th><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=type{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Type</a></th>
            <th><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=date{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Date</a></th>
            <th><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=order_handler{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Order Handler</a></th>
            <th><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=client_name{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Client Name</a></th>
            <th><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=status{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Order Status</a></th>
            <th>Message Status</th>
            <th><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=communication{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Communication</a></th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($orders_array as $key => $order)
            <tr class="{{ \App\Helpers::statusClass($order['assign_status'] ) }}">
                <td>{{ $order['order_id'] }}</td>
                <td>{{ $order['order_type'] }}</td>
                <td>{{ Carbon\Carbon::parse($order['order_date'])->format('d-m-Y') }}</td>
                <td>{{ $order['sales_person'] ? $users[$order['sales_person']] : 'nil' }}</td>
                <td>{{ $order['client_name'] }}</td>
                <td>{{ $order['order_status']}}</td>
                <td>
                  @if ($order['communication']['status'] != null && $order['communication']['status'] == 0)
                    Unread
                  @elseif ($order['communication']['status'] == 5)
                    Read
                  @elseif ($order['communication']['status'] == 6)
                    Replied
                  @elseif ($order['communication']['status'] == 1)
                    Awaiting Approval
                  @elseif ($order['communication']['status'] == 2)
                    Approved
                  @elseif ($order['communication']['status'] == 4)
                    Internal Message
                  @endif
                </td>
                <td>
                  {{-- @if (strpos(App\Helpers::getlatestmessage($order->id, 'order'), '<br>') !== false)
                    {{ substr(App\Helpers::getlatestmessage($order->id, 'order'), 0, strpos(App\Helpers::getlatestmessage($order->id, 'order'), '<br>')) }}
                  @else
                    {{ App\Helpers::getlatestmessage($order->id, 'order') }}
                  @endif --}}
                  @if (strpos($order['communication']['body'], '<br>') !== false)
                    {{ substr($order['communication']['body'], 0, strpos($order['communication']['body'], '<br>')) }}
                  @else
                    {{ $order['communication']['body'] }}
                  @endif
                </td>
                <td>
                    <a class="btn btn-image" href="{{ route('order.show',$order['id']) }}"><img src="/images/view.png" /></a>
                    @can('order-edit')
                    <a class="btn btn-image" href="{{ route('order.edit',$order['id']) }}"><img src="/images/edit.png" /></a>
                    @endcan

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

    {!! $orders_array->appends(Request::except('page'))->links() !!}
    {{--{!! $orders->links() !!}--}}
@endsection
