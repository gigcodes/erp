@foreach ($logs as $log)

    <tr>
        <td>{{ $log->flow_name }}</td>
        <td>{{  $log->messages  }}</td>
        <td>{{ \Carbon\Carbon::parse($log->log_created)->format('d-m-y H:i:s')  }}</td>
        <td>
            <button class="btn btn-xs btn-none-border show_error_logs" data-id="{{ $log->id}}"><i class="fa fa-eye"></i></button>
 
        </td>
    </tr>
@endforeach