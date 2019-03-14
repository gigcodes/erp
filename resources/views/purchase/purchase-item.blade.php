<div class="table-responsive">
    <table class="table table-bordered">
    <tr>
        <th><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=id{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}" class="ajax-sort-link">ID</a></th>
        <th><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=date{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}" class="ajax-sort-link">Date</a></th>
        <th>Customer Names</th>
        <th>Products</th>
        <th><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=purchase_handler{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}" class="ajax-sort-link">Purchase Handler</a></th>
        <th><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=supplier{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}" class="ajax-sort-link">Supplier Name</a></th>
        <th><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=status{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}" class="ajax-sort-link">Order Status</a></th>
        <th>Message Status</th>
        <th><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=communication{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}" class="ajax-sort-link">Communication</a></th>
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
            <td>
              @foreach ($purchase['products'] as $product)
                <img src="{{ $product['imageurl'] }}" class="img-responsive" width="50px">
              @endforeach
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

              {!! Form::open(['method' => 'DELETE','route' => ['purchase.destroy', $purchase['id']],'style'=>'display:inline']) !!}
              <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
              {!! Form::close() !!}

              {!! Form::open(['method' => 'DELETE','route' => ['purchase.permanentDelete', $purchase['id']],'style'=>'display:inline']) !!}
              <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
              {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
</table>
</div>

{!! $purchases_array->appends(Request::except('page'))->links() !!}
