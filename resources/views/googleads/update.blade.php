@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="container">
        <h2>Update Campaign</h2>
    </div>
    <form method="POST" action="/googleads/update" enctype="multipart/form-data">
        {{csrf_field()}}
        <input type="hidden" name="campaignId" value="{{$campaign['campaignId']}}">
        <div class="form-group row">
            <label for="campaign-name" class="col-sm-2 col-form-label">Campaign name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control-plaintext" id="campaign-name" name="campaignName" placeholder="Campaign name" value="{{$campaign['name']}}">
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
        <button type="submit" class="btn btn-primary mb-2">Update</button>
    </form>
{{--    {!! Form::open(['method' => 'GET','route' => ['adgroup.createPage'],'style'=>'display:inline']) !!}--}}
{{--        <button type="submit" class="btn btn-image">Create Ad Group</button>--}}
{{--    {!! Form::close() !!}--}}
@endsection