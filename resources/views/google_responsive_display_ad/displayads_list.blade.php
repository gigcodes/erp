@extends('layouts.app')
@section('favicon' , 'task.png')
@section('styles')
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
        #create-compaign .modal-dialog {
            max-width: 1024px !important;
            width: 1024px !important;
        }
        .btn-secondary, .btn-secondary:focus, .btn-secondary:hover{
            background: #fff;
            color: #757575;
            border: 1px solid #ddd;
            height: 32px;
            border-radius: 4px;
            padding: 5px 10px;
            font-size: 14px;
            font-weight: 100;
            line-height: 10px;
        }

    </style>
@endsection
@section('content')
    <h2 class="page-heading">Google Responsive Display Ads List - Campaigns ( <span id="ads_campaign_count">{{$totalNumEntries}} </span>)</h2>
    <div class="container-fluid p-0" style="margin-top: 10px">

        <div class="pl-3 pr-3">
            <div class="table-responsive mt-3">

                <table class="table table-bordered" id="adscampaign-table">
                    <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Account Name</th>
                        <th>Campaign Name</th>
                        <th>Ads Group Name</th>
                        <th>Google Ad Id</th>
                        <th>Created At</th>
                    </tr>
                    </thead>

                    <tbody>
                    @if(!empty($display_ads))
                        @foreach($display_ads as $campaign)
                            @php
                                $adgroup_name = \App\GoogleAdsGroup::where('google_adgroup_id',$campaign->google_adgroup_id)->first();
                                $campaign_name = \App\GoogleAdsCampaign::where('google_campaign_id',$campaign->adgroup_google_campaign_id)->first();
                                $account_name = \App\GoogleAdsAccount::where('id',$campaign_name->account_id)->first();
                            @endphp
                            <tr>
                                <td>{{$campaign->id}}</td>
                                <td>{{!empty($account_name) ? $account_name->account_name : ''}}</td>
                                <td>{{!empty($campaign_name) ? $campaign_name->campaign_name : ''}}</td>
                                <td>{{!empty($adgroup_name) ? $adgroup_name->ad_group_name : ''}}</td>
                                <td>{{$campaign->google_ad_id}}</td>
                                <td>{{$campaign->created_at}}</td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        {{--        {{ $campaignslist->links() }}--}}
    </div>
@endsection