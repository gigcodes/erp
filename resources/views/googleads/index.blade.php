@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="container">
        <h2>Google AdWords</h2>
        <h2>Campaigns ({{$totalNumEntries}})</h2>
    </div>
    <div class="container" style="margin-top: 10px">
        <form method="get" action="/googleads/create">
            <button type="submit">New Campaign</button>
        </form>
    </div>
    <div class="container" style="margin-top: 10px">
        @foreach($campaigns as $campaign)
            <div id="{{$campaign['campaignId']}}" class="col-sm-6" style="margin-bottom: 10px; border: 1px solid #ccc!important">
                <p>Campaign's groups:
                    @foreach($campaign['campaignGroups'] as $i => $adGroup)
                        {{($i>0 ? ", <" : "<")  . $adGroup['adGroupName'] . ">"}}
                    @endforeach
                </p>
                <p>Name: {{$campaign['name']}}</p>
                <p>Status: {{$campaign['status']}}</p>
                <p id="{{$campaign['budgetId']}}">Budget ({{$campaign['budgetName']}}): ${{$campaign['budgetAmount']}}</p>
                <form method="GET" action="/googleads/{{$campaign['campaignId']}}/adgroups">
                    <button type="submit" class="btn btn-image">Ad Groups</button>
                </form>
                {!! Form::open(['method' => 'DELETE','route' => ['googleads.deleteCampaign',$campaign['campaignId']],'style'=>'display:inline']) !!}
                    <button type="submit" class="btn btn-image">Delete</button>
                {!! Form::close() !!}
                {!! Form::open(['method' => 'GET','route' => ['googleads.updatePage',$campaign['campaignId']],'style'=>'display:inline']) !!}
                <button type="submit" class="btn btn-image">Update</button>
                {!! Form::close() !!}
            </div>
        @endforeach
    </div>
@endsection