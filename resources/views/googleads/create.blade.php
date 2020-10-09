@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="container">
        <h2>Create Ad</h2>
    </div>
    <form method="POST" action="/googlecampaigns/{{$campaignId}}/adgroups/{{$adGroupId}}/ads/create" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="form-group row">
            <label for="headline-part1" class="col-sm-2 col-form-label">Headline part 1</label>
            <div class="col-sm-10">
                <input type="text" class="form-control-plaintext" id="headline-part1" name="headlinePart1" placeholder="Headline">
            </div>
        </div>
        <div class="form-group row">
            <label for="headline-part2" class="col-sm-2 col-form-label">Headline part 2</label>
            <div class="col-sm-10">
                <input type="text" class="form-control-plaintext" id="headline-part2" name="headlinePart2" placeholder="Headline">
            </div>
        </div>
        <div class="form-group row">
            <label for="headline-part3" class="col-sm-2 col-form-label">Headline part 3</label>
            <div class="col-sm-10">
                <input type="text" class="form-control-plaintext" id="headline-part3" name="headlinePart3" placeholder="Headline">
            </div>
        </div>
        <div class="form-group row">
            <label for="description1" class="col-sm-2 col-form-label">Description</label>
            <div class="col-sm-10">
                <input type="text" class="form-control-plaintext" id="description1" name="description1" placeholder="Description">
            </div>
        </div>
        <div class="form-group row">
            <label for="description2" class="col-sm-2 col-form-label">Description 2</label>
            <div class="col-sm-10">
                <input type="text" class="form-control-plaintext" id="description2" name="description2" placeholder="Description">
            </div>
        </div>
        <div class="form-group row">
            <label for="finalUrl" class="col-sm-2 col-form-label">Final URL</label>
            <div class="col-sm-10">
                <input type="text" class="form-control-plaintext" id="finalUrl" name="finalUrl" placeholder="http://www.example.com">
            </div>
        </div>
        <div class="form-group row">
            <label for="path1" class="col-sm-2 col-form-label">Path 1</label>
            <div class="col-sm-10">
                <input type="text" class="form-control-plaintext" id="path1" name="path1" placeholder="http://www.example.com/path1/...">
            </div>
        </div>
        <div class="form-group row">
            <label for="path2" class="col-sm-2 col-form-label">Path 2</label>
            <div class="col-sm-10">
                <input type="text" class="form-control-plaintext" id="path2" name="path2" placeholder="http://www.example.com/.../path2/...">
            </div>
        </div>
        <div class="form-group row">
            <label for="ad-status" class="col-sm-2 col-form-label">Ad status</label>
            <div class="col-sm-10">
                <select class="browser-default custom-select" id="ad-status" name="adStatus" style="height: auto">
                    <option value="0" selected>Enabled</option>
                    <option value="1">Paused</option>
                    <option value="2">Disabled</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mb-2">Create</button>
    </form>
@endsection