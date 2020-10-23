@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="container" style="margin-top: 10px">
    <h4>Google Ads ({{$totalNumEntries}}) for {{$groupname}} AdsGroup <button class="btn-image" onclick="window.location.href='/googlecampaigns/{{$campaignId}}/adgroups';">Back to Ad groups</button></h4>
    
    <form method="get" action="/googlecampaigns/{{$campaignId}}/adgroups/{{$adGroupId}}/ads/create">
        <button type="submit" class="float-right mb-3">New Ads</button>
    </form>    

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>#ID</th>
                <th>Headline 1</th>
                <th>Headline 2</th>
                <th>Headline 3</th>
                <th>Description 1</th>
                <th>Description 2</th>
                <th>Final Url</th>
                <th>Path 1</th>
                <th>Path 2</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
            </thead>

            <tbody>
            @foreach($ads as $ad)
                <tr>
                    <td>{{$ad->id}}</td>
                    <td>{{$ad->headline1}}</td>
                    <td>{{$ad->headline2}}</td>
                    <td>{{$ad->headline3}}</td>
                    <td>{{$ad->description1}}</td>
                    <td>{{$ad->description2}}</td>
                    <td>{{$ad->final_url}}</td>
                    <td>{{$ad->path1}}</td>
                    <td>{{$ad->path2}}</td>
                    <td>{{$ad->status}}</td>
                    <td>{{$ad->created_at}}</td>
                    <td>
                    {!! Form::open(['method' => 'DELETE','route' => ['ads.deleteAd',$campaignId,$adGroupId,$ad['google_ad_id']],'style'=>'display:inline']) !!}
                <button type="submit" class="btn btn-image"><img src="/images/delete.png"></button>
                {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
@endsection