@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="col-md-12">
        <h4 class="page-heading">Google Ads Group List(<span id="ads_account_count">{{ $totalentries }}</span>)</h4>
        <table class="table table-bordered" id="adsgroup-table">
            <thead>
            <tr>
                <th>#ID</th>
                <th>Account name</th>
                <th>Ads Group Name</th>
                <th>Google Campaign Id</th>
                <th>Google Campaign name</th>
                <th>Google Adgroupd Id</th>
                <th>Created At</th>
            </tr>
            </thead>

            <tbody>
            @foreach($adsgroups as $adsGroup)
                @php
                    $campaign_name = \App\GoogleAdsCampaign::where('google_campaign_id',$adsGroup->google_campaign_id)->first();
                    $account_name = \App\GoogleAdsAccount::where('id',$campaign_name->account_id)->first();
                @endphp
                <tr>
                    <td>{{$adsGroup->id}}</td>
                    <td>{{$account_name->account_name}}</td>
                    <td>{{$adsGroup->group_name}}</td>
                    <td>{{$adsGroup->campaign_id}}</td>
                    <td>{{$campaign_name->campaign_name}}</td>
                    <td>{{$adsGroup->google_ad_group_id}}</td>
                    <td>{{$adsGroup->created_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
