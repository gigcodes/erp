@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="container">
        <button class="btn btn-image" onclick="window.location.href='/googlecampaigns';">Campaigns</button>
        <button class="btn btn-image" onclick="window.location.href='/googlecampaigns/{{$campaignId}}/adgroups';">Ad groups</button>
    </div>
    <div class="container">
        <h2>Google Ads ({{$totalNumEntries}})</h2>
    </div>
    <div class="container" style="margin-top: 10px">
        <form method="get" action="/googlecampaigns/{{$campaignId}}/adgroups/{{$adGroupId}}/ads/create">
            <button type="submit">New Ads</button>
        </form>
    </div>
    <div class="container" style="margin-top: 10px">
        @foreach($ads as $ad)
            <div id="{{$ad['adId']}}" class="col-sm-6" style="margin-bottom: 10px; border: 1px solid #ccc!important">
                <p>Type: {{$ad['type']}}</p>
                <p>Status: {{$ad['status']}}</p>
                <p>Headline part 1: {{$ad['headlinePart1']}}</p>
                <p>Headline part 2: {{$ad['headlinePart2']}}</p>
                <p>Description: {{$ad['description']}}</p>
                {!! Form::open(['method' => 'DELETE','route' => ['ads.deleteAd',$campaignId,$adGroupId,$ad['adId']],'style'=>'display:inline']) !!}
                <button type="submit" class="btn btn-image">Delete</button>
                {!! Form::close() !!}
            </div>
        @endforeach
    </div>
@endsection