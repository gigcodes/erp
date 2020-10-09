@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="container">
        <button class="btn btn-image" onclick="window.location.href='/googlecampaigns';">Campaigns</button>
    </div>
    <div class="container">
        <h2>Google AdGroups ({{$totalNumEntries}})</h2>
    </div>
    <div class="container" style="margin-top: 10px">
        <form method="get" action="/googlecampaigns/{{$campaignId}}/adgroups/create">
            <button type="submit">New Ad Group</button>
        </form>
    </div>
    <div class="container" style="margin-top: 10px">
        @foreach($adGroups as $adGroup)
            <div id="{{$adGroup['adGroupId']}}" class="col-sm-6" style="margin-bottom: 10px; border: 1px solid #ccc!important">
                <p>Name: {{$adGroup['name']}}</p>
                <p>Bid amount: ${{$adGroup['bidAmount']}}</p>
                <p>Status: {{$adGroup['status']}}</p>
                <form method="GET" action="/googlecampaigns/{{$campaignId}}/adgroups/{{$adGroup['adGroupId']}}/ads">
                    <button type="submit" class="btn btn-image">Ads</button>
                </form>
                {!! Form::open(['method' => 'DELETE','route' => ['adgroup.deleteAdGroup',$campaignId,$adGroup['adGroupId']],'style'=>'display:inline']) !!}
                <button type="submit" class="btn btn-image">Delete</button>
                {!! Form::close() !!}
                {!! Form::open(['method' => 'GET','route' => ['adgroup.updatePage',$campaignId,$adGroup['adGroupId']],'style'=>'display:inline']) !!}
                <button type="submit" class="btn btn-image">Update</button>
                {!! Form::close() !!}
            </div>
        @endforeach
    </div>
@endsection