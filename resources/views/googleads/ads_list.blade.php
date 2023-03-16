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
    <h2 class="page-heading">Google Ads List(<span id="ads_campaign_count">{{$totalNumEntries}}</span>)</h2>
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
                        <th>Headline 1</th>
                        <th>Created At</th>
                    </tr>
                    </thead>

                    <tbody>
                    @if(!empty($adslist))
                        @foreach($adslist as $ad)
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
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        {{--        {{ $campaignslist->links() }}--}}
    </div>
@endsection