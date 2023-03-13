@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="col-md-12">
        <h4 class="page-heading">Google App Ads(<span id="ads_account_count">{{ $totalentries }}</span>)</h4>
        <table class="table table-bordered" id="adsgroup-table">
            <thead>
            <tr>
                <th>#ID</th>
                <th>Account name</th>
                <th>Google Campaign name</th>
                <th>Ads Group Name</th>
                <th>Google Ads Id</th>
                <th>Created At</th>
            </tr>
            </thead>

            <tbody>
            @foreach($googleappadd as $appads)
                @php
                    $google_ads_name = \App\GoogleAdsGroup::where('google_adgroup_id',$appads->google_adgroup_id)->first();
                    $campaign_name = \App\GoogleAdsCampaign::where('google_campaign_id',$appads->adgroup_google_campaign_id)->first();
                    $account_name = \App\GoogleAdsAccount::where('id',$campaign_name->account_id)->first();
                @endphp
                <tr>
                    <td>{{$appads->id}}</td>
                    <td>{{$account_name->account_name}}</td>
                    <td>{{$campaign_name->campaign_name}}</td>
                    <td>{{$google_ads_name->ad_group_name}}</td>
                    <td>{{$appads->google_ad_id}}</td>
                    <td>{{$appads->created_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
