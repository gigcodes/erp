@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="container">
        <h2>Update Campaign</h2>
    </div>
    <div class="container">
        <form method="POST" action="/googleads/update" enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="hidden" name="campaignId" value="{{$campaign['campaignId']}}">
            <label for="campaign-name">Campaign name:</label>
            <input id="campaign-name" type="text" name="campaignName" value="{{$campaign['name']}}">
{{--            <label for="budget-amount">Budget amount ($):</label>--}}
{{--            <input id="budget-amount" type="number" name="budgetAmount">--}}
            <label for="campaign-status">Campaign status:</label>
            <select id="campaign-status" name="campaignStatus">
                <option value="enabled" {{($campaign['status'] == 'ENABLED') ? 'selected' : ''}}>Enabled</option>
                <option value="paused" {{($campaign['status'] == 'PAUSED') ? 'selected' : ''}}>Paused</option>
            </select>
            <input type="submit" value="Submit">
        </form>
    </div>
@endsection