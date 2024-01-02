@foreach ($adsets as $adset)
<?php
    $config_name = App\Social\SocialConfig::where('id',$adset->config_id)->first();
  ?>
    <tr>
      <td>{{ \Carbon\Carbon::parse($adset->created_at)->format('d-m-Y') }}</td>
        <td>{{ isset($configs[$adset->config_id])?$configs[$adset->config_id]:'' }}</td>
        <td>{{ isset($campaingns[$adset->campaign_id])?$campaingns[$adset->campaign_id]:'' }}</td>
        <td>@if(isset($config_name->storeWebsite)) {{ $config_name->storeWebsite->title }} @endif</td>
        <td>{{ $adset->name }}</td>
      
        <td>{{ $adset->billing_event }}</td>
       
        <td>{{ $adset->daily_budget }}</td>
     
        <td>{{ $adset->status  }}</td>
        <td>{{ $adset->live_status  }}</td>
        <td><a href="javascript:;" data-id="{{ $adset->id }}" class="account-history"><i class="fa fa-history" title="History"></i></a></td>
      </tr>
@endforeach
{{$adsets->appends(request()->except("page"))->links()}}