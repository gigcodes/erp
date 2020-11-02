@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="container">
        <h2>Create Campaign</h2>
    </div>
    <form method="POST" action="/google-campaigns/create" enctype="multipart/form-data">
        {{csrf_field()}}
        <input type="hidden" value="<?php echo $_GET['account_id']; ?>" id="accountID" name="account_id"/>
        <div class="form-group row">
            <label for="campaign-name" class="col-sm-2 col-form-label">Campaign name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="campaign-name" name="campaignName" placeholder="Campaign name">
                @if ($errors->has('campaignName'))
                <span class="text-danger">{{$errors->first('campaignName')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="budget-amount" class="col-sm-2 col-form-label">Budget amount ($)</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="budget-amount" name="budgetAmount" placeholder="Budget amount ($)">
                @if ($errors->has('budgetAmount'))
                <span class="text-danger">{{$errors->first('budgetAmount')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="start-date" class="col-sm-2 col-form-label">Start Date</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="start-date" name="start_date" placeholder="Start Date E.g {{date('Ymd', strtotime('+1 day'))}}">
                @if ($errors->has('start_date'))
                <span class="text-danger">{{$errors->first('start_date')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="start-date" class="col-sm-2 col-form-label">End Date</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="end-date" name="end_date" placeholder="End Date E.g {{date('Ymd', strtotime('+1 month'))}}">
                @if ($errors->has('end_date'))
                <span class="text-danger">{{$errors->first('end_date')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="campaign-status" class="col-sm-2 col-form-label">Campaign status</label>
            <div class="col-sm-10">
                <select class="browser-default custom-select" id="campaign-status" name="campaignStatus" style="height: auto">
                    <option value="1" selected>Enabled</option>
                    <option value="2">Paused</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mb-2 float-right">Create</button>
    </form>
    <div class="container">

    </div>
@endsection