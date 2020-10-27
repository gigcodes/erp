@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="container">
        <h2>Update Campaign</h2>
    </div>
    <form method="POST" action="/google-campaigns/update" enctype="multipart/form-data">
        {{csrf_field()}}
        <input type="hidden" name="campaignId" value="{{$campaign['google_campaign_id']}}">
        <div class="form-group row">
            <label for="campaign-name" class="col-sm-2 col-form-label">Campaign name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="campaign-name" name="campaignName" placeholder="Campaign name" value="{{$campaign['campaign_name']}}">
            </div>
        </div>

        <div class="form-group row">
            <label for="budget-amount" class="col-sm-2 col-form-label">Budget amount ($)</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="budget-amount" name="budgetAmount" placeholder="Budget amount ($)" value="{{$campaign['budget_amount']}}">
            </div>
        </div>
        <div class="form-group row">
            <label for="start-date" class="col-sm-2 col-form-label">Start Date</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="start-date" name="start_date" placeholder="Start Date E.g {{date('Ymd', strtotime('+1 day'))}}" value="{{$campaign['start_date']}}">
            </div>
        </div>
        <div class="form-group row">
            <label for="start-date" class="col-sm-2 col-form-label">End Date</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="end-date" name="end_date" placeholder="End Date E.g {{date('Ymd', strtotime('+1 month'))}}" value="{{$campaign['end_date']}}">
            </div>
        </div>

        <div class="form-group row">
            <label for="campaign-status" class="col-sm-2 col-form-label">Campaign status</label>
            <div class="col-sm-10">
                <select class="browser-default custom-select" id="campaign-status" name="campaignStatus" style="height: auto">
                    <option value="1" {{($campaign['status'] == 'ENABLED') ? 'selected' : ''}}>Enabled</option>
                    <option value="2" {{($campaign['status'] == 'PAUSED') ? 'selected' : ''}}>Paused</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mb-2 float-right">Update</button>
    </form>
{{--    {!! Form::open(['method' => 'GET','route' => ['adgroup.createPage'],'style'=>'display:inline']) !!}--}}
{{--        <button type="submit" class="btn btn-image float-right">Create Ad Group</button>--}}
{{--    {!! Form::close() !!}--}}
@endsection