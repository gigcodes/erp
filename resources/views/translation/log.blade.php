@foreach ($translateLog as $log)
                <tr>
                    <td>{{ $log->error_code }}</td>
                    <td>{{ $log->messages }}</td>
                    <td>{{ $log->domain }}</td>
                    <td>{{ $log->reason }}</td>
                    <td>
                    {{ $log->created_at->format('d-m-y H:i:s') }}
                    </td>
                </tr>
@endforeach