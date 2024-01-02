@foreach ($adcreatives as $adcreative)
<?php
    $config_name = App\Social\SocialConfig::where('id',$adcreative->config_id)->first();
  ?>
    <tr>
      <td>{{ \Carbon\Carbon::parse($adcreative->created_at)->format('d-m-Y') }}</td>
        <td>{{ isset($configs[$adcreative->config_id])?$configs[$adcreative->config_id]:'' }}</td>
        <td>@if(isset($config_name->storeWebsite)) {{ $config_name->storeWebsite->title }} @endif</td>
        <td>{{ $adcreative->name }}</td>
      
        <td>{{ $adcreative->object_story_title }}</td>
       
       <td>{{ $adcreative->live_status  }}</td>
        <td><a href="javascript:;" data-id="{{ $adcreative->id }}" class="account-history"><i class="fa fa-history" title="History"></i></a></td>
      </tr>
@endforeach
{{$adcreatives->appends(request()->except("page"))->links()}}