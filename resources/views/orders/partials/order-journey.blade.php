@foreach ($orders as $order)
    <tr>
        <td> {{ $order->order_id }} </td>
        <?php $orderStatusHistories = $order->orderStatusHistories->pluck('created_at', 'new_status')->toArray(); ?>
        @foreach ($orderStatusList as $key => $orderStatus)
            <td>
                @if (array_key_exists($key, $orderStatusHistories))
                    {{ $orderStatusHistories[$key] }}
                @endif
            </td>
        @endforeach()
    </tr>
@endforeach()
