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
                <a class="btn btn-success" href="{{ route('order.create') }}"> Create New Order</a>
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
                <td>{{ $order->order_date }}</td>
                <td>{{ $order->sales_person ? $users[$order->sales_person] : 'nil' }}</td>
                <td>{{ $order->client_name }}</td>
                <td>{{ $order->order_status}}</td>
                <td>{{App\Helpers::getlatestmessage($order->id, 'order')}}</td>
                <td>
                    <a class="btn btn-primary btn-success" href="{{ route('order.show',$order->id) }}">View</a>
                    @can('order-edit')
                    <a class="btn btn-primary" href="{{ route('order.edit',$order->id) }}">Edit</a>
                    @endcan

                    {!! Form::open(['method' => 'DELETE','route' => ['order.destroy', $order->id],'style'=>'display:inline']) !!}
                    {!! Form::submit('Archive', ['class' => 'btn btn-info']) !!}
                    {!! Form::close() !!}

                    @can('order-delete')
                        {!! Form::open(['method' => 'DELETE','route' => ['order.permanentDelete', $order->id],'style'=>'display:inline']) !!}
                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                        {!! Form::close() !!}
                    @endcan
                </td>
            </tr>
        @endforeach
    </table>

    {!! $orders->appends(Request::except('page'))->links() !!}
    {{--{!! $orders->links() !!}--}}
@endsection
