@foreach ($logListMagentos as $logListMagento)
    <tr>
        <td>{{ $logListMagento->product_id }}</td>
        <td>{{ $logListMagento->message }}</td>
        <td>{{ $logListMagento->created_at->format('d-m-y') }}</td>
    </tr>
@endforeach