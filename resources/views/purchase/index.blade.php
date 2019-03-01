@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Purchase List</h2>
            <div class="pull-left">

                <form action="/purchases/" method="GET">
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
                <a class="btn btn-secondary" href="{{ route('purchase.grid') }}">+</a>
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
            <th><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=id{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">ID</a></th>
            <th><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=date{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Date</a></th>
            <th>Customer Names</th>
            <th><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=purchase_handler{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Purchase Handler</a></th>
            <th><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=supplier{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Supplier Name</a></th>
            <th><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=status{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Order Status</a></th>
            <th>Message Status</th>
            <th><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=communication{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Communication</a></th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($purchases_array as $key => $purchase)
            <tr>
                <td>{{ $purchase['id'] }}</td>
                <td>{{ Carbon\Carbon::parse($purchase['created_at'])->format('d-m-Y') }}</td>
                <td>
                  <ul>
                    @foreach ($purchase['products'] as $product)
                      <li>
                        {{ $product['orderproducts'] ? ($product['orderproducts'][0]['order'] ? ($product['orderproducts'][0]['order']['customer'] ? $product['orderproducts'][0]['order']['customer']['name'] : 'No Customer') : 'No Order') : 'No Order Product' }}
                      </li>
                    @endforeach
                  </ul>
                </td>
                <td>{{ $purchase['purchase_handler'] ? $users[$purchase['purchase_handler']] : 'nil' }}</td>
                <td>{{ $purchase['supplier'] }}</td>
                <td>{{ $purchase['status']}}</td>
                <td>
                  @if ($purchase['communication']['status'] != null && $purchase['communication']['status'] == 0)
                    Unread
                  @elseif ($purchase['communication']['status'] == 5)
                    Read
                  @elseif ($purchase['communication']['status'] == 6)
                    Replied
                  @elseif ($purchase['communication']['status'] == 1)
                    Awaiting Approval
                  @elseif ($purchase['communication']['status'] == 2)
                    Approved
                  @elseif ($purchase['communication']['status'] == 4)
                    Internal Message
                  @endif
                </td>
                <td>
                  @if (strpos($purchase['communication']['body'], '<br>') !== false)
                    {{ substr($purchase['communication']['body'], 0, strpos($purchase['communication']['body'], '<br>')) }}
                  @else
                    {{ $purchase['communication']['body'] }}
                  @endif
                </td>
                <td>
                    <a class="btn btn-image" href="{{ route('purchase.show',$purchase['id']) }}"><img src="/images/view.png" /></a>
                    {{-- @can('order-edit') --}}
                    {{-- <a class="btn btn-image" href="{{ route('purchase.edit',$purchase['id']) }}"><img src="/images/edit.png" /></a> --}}
                    {{-- @endcan --}}

                    {!! Form::open(['method' => 'DELETE','route' => ['purchase.destroy', $purchase['id']],'style'=>'display:inline']) !!}
                    <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
                    {!! Form::close() !!}

                    {{-- @can('order-delete') --}}
                        {!! Form::open(['method' => 'DELETE','route' => ['purchase.permanentDelete', $purchase['id']],'style'=>'display:inline']) !!}
                        <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                        {!! Form::close() !!}
                    {{-- @endcan --}}
                </td>
            </tr>
        @endforeach
    </table>
    </div>

    {!! $purchases_array->appends(Request::except('page'))->links() !!}
    {{--{!! $orders->links() !!}--}}
@endsection
