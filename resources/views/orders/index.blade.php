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
            <th>ID</th>
            <th>Type</th>
            <th>Date</th>
            <th>Order Handler</th>
            <th>Client Name</th>
            <th>Order Status</th>
            <th>Communication</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($orders as $key => $order)
            <tr class="{{ \App\Helpers::statusClass($order->assign_status ) }}">
                <td>{{ $order->order_id }}</td>
                <td>{{ $order->order_type }}</td>
                <td>{{ Carbon\Carbon::parse($order->order_date)->format('d-m-Y') }}</td>
                <td>{{ $order->sales_person ? $users[$order->sales_person] : 'nil' }}</td>
                <td>{{ $order->client_name }}</td>
                <td>{{ $order->order_status}}</td>
                <td>
                  @if (strpos(App\Helpers::getlatestmessage($order->id, 'order'), '<br>') !== false)
                    {{ substr(App\Helpers::getlatestmessage($order->id, 'order'), 0, strpos(App\Helpers::getlatestmessage($order->id, 'order'), '<br>')) }}
                  @else
                    {{ App\Helpers::getlatestmessage($order->id, 'order') }}
                  @endif
                </td>
                <td>
                    <a class="btn btn-image" href="{{ route('order.show',$order->id) }}"><img src="/images/view.png" /></a>
                    @can('order-edit')
                    <a class="btn btn-image" href="{{ route('order.edit',$order->id) }}"><img src="/images/edit.png" /></a>
                    @endcan

                    {!! Form::open(['method' => 'DELETE','route' => ['order.destroy', $order->id],'style'=>'display:inline']) !!}
                    <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
                    {!! Form::close() !!}

                    @can('order-delete')
                        {!! Form::open(['method' => 'DELETE','route' => ['order.permanentDelete', $order->id],'style'=>'display:inline']) !!}
                        <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                        {!! Form::close() !!}
                    @endcan
                </td>
            </tr>
        @endforeach
    </table>

    {!! $orders->appends(Request::except('page'))->links() !!}
    {{--{!! $orders->links() !!}--}}
@endsection
