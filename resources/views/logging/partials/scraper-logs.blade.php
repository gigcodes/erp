@foreach ($scraperLogs as $log)
    <tr>
        <td>{{ $log->id }}</td>
        <td>{{ $log->ip_address }}</td>
        <td>{{ $log->website }}</td>
        <td><a href="{{ $log->url }}" target="__blank">{{ $log->url }}</a></td>
        <td>{{ $log->sku }}</td>
        <td>{{ $log->original_sku }}</td>
        <td>{{ $log->title }}</td>
        <td>{{ $log->validation_result }}</td>
        <td>{{ $log->created_at }}</td>
    </tr>
@endforeach