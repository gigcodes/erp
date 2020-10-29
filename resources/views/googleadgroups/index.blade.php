@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="container" style="margin-top: 10px">
    <h4>Google AdGroups ({{$totalNumEntries}}) for {{@$campaign_name}} campaign name
    <button class="btn-image" onclick="window.location.href='/google-campaigns?account_id={{$campaign_account_id}}'">Back to campaigns</button></h4>
        <form method="get" action="/google-campaigns/{{$campaignId}}/adgroups/create">
            <button type="submit" class="btn-sm float-right mb-3">New Ad Group</button>
        </form>
   
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>#ID</th>
                <th>Ads Group Name</th>
                <th>Google Campaign Id</th>
                <th>Google Adgroupd Id</th>
                <th>Bid</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>
            @foreach($adGroups as $adGroup)
                <tr>
                    <td>{{$adGroup->id}}</td>
                    <td>{{$adGroup->ad_group_name}}</td>
                    <td>{{$adGroup->adgroup_google_campaign_id}}</td>
                    <td>{{$adGroup->google_adgroup_id}}</td>
                    <td>{{$adGroup->bid}}</td>
                    <td>{{$adGroup->status}}</td>
                    <td>{{$adGroup->created_at}}</td>
                    <td>
                    <form method="GET" action="/google-campaigns/{{$campaignId}}/adgroups/{{$adGroup['google_adgroup_id']}}/ads">
                    <button type="submit" class="btn-image">Ads</button>
                    </form>
                    {!! Form::open(['method' => 'DELETE','route' => ['adgroup.deleteAdGroup',$campaignId,$adGroup['google_adgroup_id']],'style'=>'display:inline']) !!}
                    <button type="submit" class="btn-image"><img src="/images/delete.png"></button>
                    {!! Form::close() !!}
                    {!! Form::open(['method' => 'GET','route' => ['adgroup.updatePage',$campaignId,$adGroup['google_adgroup_id']],'style'=>'display:inline']) !!}
                    <button type="submit" class="btn-image"><img src="/images/edit.png"></i></button>
                    {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    {{ $adGroups->links() }}
    </div>
@endsection