@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="container">
        <h2>Update Ad Group</h2>
    </div>
    <form method="POST" action="/googleads/{{$campaignId}}/adgroups/update" enctype="multipart/form-data">
        {{csrf_field()}}
        <input type="hidden" name="adGroupId" value="{{$adGroup['adGroupId']}}">
        <div class="form-group row">
            <label for="ad-group-name" class="col-sm-2 col-form-label">Ad group name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control-plaintext" id="ad-group-name" name="adGroupName" placeholder="Ad group name" value="{{$adGroup['name']}}">
            </div>
        </div>
        <div class="form-group row">
            <label for="cpc-bid-micro-amount" class="col-sm-2 col-form-label">Bid ($)</label>
            <div class="col-sm-10">
                <input type="number" class="form-control" id="cpc-bid-micro-amount" name="cpcBidMicroAmount" placeholder="Bid ($)" value="{{$adGroup['bidAmount']}}">
            </div>
        </div>
        <div class="form-group row">
            <label for="ad-group-status" class="col-sm-2 col-form-label">Ad group status</label>
            <div class="col-sm-10">
                <select class="browser-default custom-select" id="ad-group-status" name="adGroupStatus" style="height: auto">
                    <option value="1" {{$adGroup['status'] == 'ENABLED' ? 'selected' : ''}}>Enabled</option>
                    <option value="2" {{$adGroup['status'] == 'PAUSED' ? 'selected' : ''}}>Paused</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mb-2">Update</button>
    </form>
@endsection