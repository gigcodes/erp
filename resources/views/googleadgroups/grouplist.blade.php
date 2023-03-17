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
                <th>Google Campaign Id</th>
                <th>Google Campaign name</th>
                <th>Google Adgroupd Id</th>
                <th>Ads Group Name</th>
                <th>Created At</th>
            </tr>
            </thead>

            <tbody>
            @foreach($adsgroups as $adsGroup)
                <tr>
                    <td>{{$adsGroup->id}}</td>
                    <td>{{$adsGroup->campaign->account->account_name}}</td>
                    <td>{{$adsGroup->campaign->campaign_name}}</td>
                    <td>{{$adsGroup->adgroup_google_campaign_id}}</td>
                    <td>{{$adsGroup->google_adgroup_id}}</td>
                    <td>{{$adsGroup->ad_group_name}}</td>
                    <td>{{$adsGroup->created_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
