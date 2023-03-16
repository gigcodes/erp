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
                <th>Headline 1</th>
                <th>Created At</th>
            </tr>
            </thead>

            <tbody>
            @foreach($googleappadd as $ad)
                <tr>
                    <td>{{$ad->id}}</td>
                    <td>{{$ad->campaign->account->account_name ?? "N/A"}}</td>
                    <td>{{$ad->campaign->campaign_name ?? "N/A"}}</td>
                    <td>{{$ad->adgroup->ad_group_name ?? "N/A"}}</td>
                    <td>{{$ad->google_ad_id}}</td>
                    <td>{{$ad->headline1}}</td>
                    <td>{{$ad->created_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
