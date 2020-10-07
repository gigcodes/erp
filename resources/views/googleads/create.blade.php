@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="container">
        <h2>Create Campaign</h2>
    </div>
    <div class="container">
        <form method="POST" action="/googleads/create" enctype="multipart/form-data">
            {{csrf_field()}}
            <label for="campaign-name">Campaign name:</label>
            <input id="campaign-name" type="text" name="campaignName">
            <label for="budget-amount">Budget amount ($):</label>
            <input id="budget-amount" type="number" name="budgetAmount">
            <label for="campaign-status">Campaign status:</label>
            <select id="campaign-status" name="campaignStatus">
                <option value="enabled" selected>Enabled</option>
                <option value="paused">Paused</option>
            </select>
            <input type="submit" value="Submit">
        </form>
    </div>
@endsection