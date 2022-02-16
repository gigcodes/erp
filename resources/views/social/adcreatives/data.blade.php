@foreach ($adcreatives as $adcreative)
    <tr>
      <td>{{ \Carbon\Carbon::parse($adcreative->created_at)->format('d-m-Y') }}</td>
        <td>{{ isset($configs[$adcreative->config_id])?$configs[$adcreative->config_id]:'' }}</td>
        <td>{{ $adcreative->name }}</td>
      
        <td>{{ $adcreative->object_story_title }}</td>
       
       <td>{{ $adcreative->live_status  }}</td>
        <td><a href="javascript:;" data-id="{{ $adcreative->id }}" class="account-history"><i class="fa fa-history" title="History"></i></a></td>
      </tr>
@endforeach
{{$adcreatives->appends(request()->except("page"))->links()}}