@foreach($messageLogs as $log)
<tr>
    <td>
        {{ $log->flow_action }}
    </td>
    <td>
        {{ $log->modalType }}
    </td>
    <td>
        {{ $log->leads }}
    </td>
    <td>
        {{ $log->messages }}
    </td>
    <td>
        {{ $log->store_website_id  }}
    </td>
    
    <td>
    {{ $log->created_at->format('d-m-y H:i:s') }}
    </td>
    
</tr>
@endforeach
