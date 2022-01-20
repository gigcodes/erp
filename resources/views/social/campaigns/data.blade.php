@foreach ($campaigns as $campaign)
    <tr>
      <td>{{ \Carbon\Carbon::parse($campaign->created_at)->format('d-m-Y') }}</td>
        <td>{{ isset($configs[$campaign->config_id])?$configs[$campaign->config_id]:'' }}</td>
        <td>{{ $campaign->name }}</td>
      
        <td>{{ $campaign->objective_name }}</td>
        <td>{{ $campaign->buying_type }}</td>
        <td>{{ $campaign->daily_budget }}</td>
     
        <td>{{ $campaign->status  }}</td>
        <td><a href="javascript:;" data-id="{{ $campaign->id }}" class="account-history"><i class="fa fa-history" title="History"></i></a></td>
      </tr>
@endforeach
{{$campaigns->appends(request()->except("page"))->links()}}