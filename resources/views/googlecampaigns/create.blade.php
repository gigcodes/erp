@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="container">
        <h2>Create Campaign</h2>
    </div>
    <form method="POST" action="/googlecampaigns/create" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="form-group row">
            <label for="campaign-name" class="col-sm-2 col-form-label">Campaign name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control-plaintext" id="campaign-name" name="campaignName" placeholder="Campaign name">
            </div>
        </div>
        <div class="form-group row">
            <label for="budget-amount" class="col-sm-2 col-form-label">Budget amount ($)</label>
            <div class="col-sm-10">
                <input type="number" class="form-control" id="budget-amount" name="budgetAmount" placeholder="Budget amount ($)">
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
        <button type="submit" class="btn btn-primary mb-2">Create</button>
    </form>
    <div class="container">

    </div>
@endsection