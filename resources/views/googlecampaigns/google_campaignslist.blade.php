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
    <h2 class="page-heading">Google AdWords - Campaigns ( <span id="ads_campaign_count">{{$totalNumEntries}} </span>)</h2>
    <div class="container-fluid p-0" style="margin-top: 10px">

        <div class="pl-3 pr-3">
            <div class="table-responsive mt-3">

                <table class="table table-bordered" id="adscampaign-table">
                    <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Account Name</th>
                        <th>Campaign Name</th>
                        <th>Channel Type</th>
                        <th>Channel Subtype</th>
                        <th>Budget</th>
                        <th>Status</th>
                        <th>Created At</th>
{{--                        <th>Actions</th>--}}
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($campaignslist as $campaign)
                        @php
                            $account_name = \App\GoogleAdsAccount::where('id',$campaign->account_id)->first();
                        @endphp
                        <tr>
                        <td>{{$campaign->id}}</td>
                        <td>{{$account_name->account_name}}</td>
                        <td>{{$campaign->campaign_name}}</td>
                        <td>{{$campaign->channel_type}}</td>
                        <td>{{$campaign->channel_sub_type}}</td>
                        <td>{{$campaign->budget_amount}}</td>
                        <td>{{$campaign->status}}</td>
                        <td>{{$campaign->created_at}}</td>
{{--                        <td>{{$campaign->id}}</td>--}}
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
{{--        {{ $campaignslist->links() }}--}}
    </div>
@endsection