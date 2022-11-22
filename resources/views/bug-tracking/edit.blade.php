@extends('layouts.app')


@section('title', 'Bug Tracking Create')

@section('content')

    <div class="panel panel-primary ml-2">
        <div class="panel-body">
            <div class="row">
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h3>Edit Bug Tracking</h3>

                    {!! Form::open(['route'=> ['bug-tracking.update',$bugTracker->id]  ]) !!}

                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif

                    <div class="form-group {{ $errors->has('summary') ? 'has-error' : '' }}">
                        <label> Summary </label>
                        <input class="form-control" name="summary" type="text" value="{{$bugTracker->summary}}">
                        <span class="text-danger">{{ $errors->first('summary') }}</span>
                    </div>

                    <div class="form-group {{ $errors->has('step_to_reproduce') ? 'has-error' : '' }}">
                        <label> Step To Reproduce </label>
                        <input class="form-control" name="step_to_reproduce" type="text" value="{{$bugTracker->step_to_reproduce}}">
                        <span class="text-danger">{{ $errors->first('step_to_reproduce') }}</span>
                    </div>

                    <div class="form-group {{ $errors->has('url') ? 'has-error' : '' }}">
                        <label> ScreenShot/ Video Url </label>
                        <input class="form-control" name="url" type="text" value="{{$bugTracker->url}}">
                        <span class="text-danger">{{ $errors->first('url') }}</span>
                    </div>

                    <div class="form-group" {{ $errors->has('bug_type_id') ? 'has-error' : '' }}>
                        <label> Type of Bug </label>
                        <select class="form-control" name="bug_type_id">
                            <option value="">Select Type of Bug</option>
                            @foreach($bugTypes as  $bugType)
                                <option value="{{$bugType->id}}" @if($bugType->id == $bugTracker->bug_type_id) selected @endif>{{$bugType->name}} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" {{ $errors->has('bug_environment_id') ? 'has-error' : '' }}>
                        <label> Environment </label>
                        <select class="form-control" name="bug_environment_id">
                            <option value="">Select Environment</option>
                            @foreach($bugEnvironments as  $bugEnvironment)
                                <option value="{{$bugEnvironment->id}}" @if($bugEnvironment->id == $bugTracker->bug_environment_id) selected @endif>{{$bugEnvironment->name}} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" {{ $errors->has('assign_to') ? 'has-error' : '' }}>
                        <label> Assign To </label>
                        <select class="form-control" name="assign_to">
                            <option value="">Select Assign To</option>
                            @foreach($users as  $user)
                                <option value="{{$user->id}}" @if($user->id == $bugTracker->assign_to) selected @endif>{{$user->name}} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" {{ $errors->has('bug_severity_id') ? 'has-error' : '' }}>
                        <label> Severity </label>
                        <select class="form-control" name="bug_severity_id">
                            <option value="">Select Severity</option>
                            @foreach($bugSeveritys as  $bugSeverity)
                                <option value="{{$bugSeverity->id}}" @if($bugSeverity->id == $bugTracker->bug_severity_id) selected @endif>{{$bugSeverity->name}} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" {{ $errors->has('bug_status_id') ? 'has-error' : '' }}>
                        <label> Status </label>
                        <select class="form-control" name="bug_status_id">
                            <option value="">Select Status</option>
                            @foreach($bugStatuses as  $bugStatus)
                                <option value="{{$bugStatus->id}}" @if($bugStatus->id == $bugTracker->bug_status_id) selected @endif>{{$bugStatus->name}} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" {{ $errors->has('module_id') ? 'has-error' : '' }}>
                        <label> Module/Feature </label>
                        <select class="form-control" name="module_id">
                            <option value="">Select Module/Feature</option>
                            @foreach($filterCategories as  $filterCategory)
                                <option value="{{$filterCategory}}" @if($filterCategory == $bugTracker->module_id) selected @endif>{{$filterCategory}} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group  {{ $errors->has('remark') ? 'has-error' : '' }}">
                        <label> Remark </label>
                        <textarea class="form-control" name="remark">{{$bugTracker->remark}}</textarea>
                        <span class="text-danger">{{ $errors->first('remark') }}</span>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-secondary">Store</button>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>

        </div>
    </div>

@endsection
