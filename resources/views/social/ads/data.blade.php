@foreach ($ads as $ad)
    <tr>
      <td>{{ \Carbon\Carbon::parse($ad->created_at)->format('d-m-Y') }}</td>
       
        <td>{{ $ad->name }}</td>
        <td>{{ isset($configs[$ad->config_id])?$configs[$ad->config_id]:'' }}</td>
        <td>{{ $ad->ad_set_name }}</td>
        <td>{{ $ad->ad_creative_name }}</td>
      
        <td>{{ $ad->status  }}</td>
        <td>{{ $ad->live_status  }}</td>
        <td><a href="javascript:;" data-id="{{ $ad->id }}" class="account-history"><i class="fa fa-history" title="History"></i></a></td>
      </tr>
@endforeach
{{$ads->appends(request()->except("page"))->links()}}