@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">New Dev Task Planner</h2>
    </div>
</div>
<div class="col-lg-12 margin-tb">
    <form action="{{route('filteredNewDevTaskPlanner')}}" method="get" class="form-inline">
        <div class="col-md-4 col-lg-2 col-xl-2">
            <div class="form-group">
                {!! Form::text('search_term', (!empty(request()->search_term) ? request()->search_term : null) , ['class' => 'form-control', 'placeholder' => 'Search Term']) !!}
            </div>
        </div>
        <div class="col-md-4 col-lg-2 col-xl-2">
            <div class="form-group">
                {!! Form::select('module', (!empty($modules) ? $modules : array()), (!empty(request()->module) ? request()->module : null), ['class' => 'form-control', 'placeholder' => 'Select a module']) !!}
            </div>
        </div>
        <div class="col-md-4 col-lg-2 col-xl-2">
            {!! Form::select('user', (!empty($users) ? $users : array()), (!empty(request()->user) ? request()->user : null), ['class' => 'form-control', 'placeholder' => 'Select A User']) !!}
        </div>
        <div class="col-md-4 col-lg-2 col-xl-2">
            <div class="form-group">
                {!! Form::select('status', (!empty($statuses) ? $statuses : array()), (!empty(request()->status) ? request()->status : null), ['class' => 'form-control', 'placeholder' => 'Status']) !!}
            </div>
        </div>
        <div class="col-md-4 col-lg-2 col-xl-2">
            <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
        </div>
    </form>
</div>
<div class="text-center">
    {!! $dev_task->links() !!}
</div>
<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
            <th rowspan="2">Sr.No.</th>
            <th rowspan="2" class="text-center">Module</th>
            <th rowspan="2" class="text-left">Page</th>
            <th rowspan="2" class="text-center">Details</th>
            <th rowspan="2" class="text-center">Attachements</th>
            <th rowspan="2" class="text-center">Date Created</th>
            <th rowspan="2" class="text-center">User Assigned</th>
            <th rowspan="2" class="text-center">Date Assigned</th>
            <th rowspan="2" class="text-center">Status</th>
            <th rowspan="2" class="text-center">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dev_task as $key => $value)
                @php
                    $developer_task = \App\DeveloperTask::Find($value['id']);
                    $user = \App\User::Find($value['user_id']);
                @endphp
                <tr>
                    <td>{{$value['id']}}</td>
                    <td>{{$developer_task->developerModule->name ?? 'N/A'}}</td>
                    <td>N/A</td>
                    <td>{{$value['task']}}</td>
                    <td>N/A</td>
                    <td>{{\Carbon\Carbon::parse($value['created_at'])->format('d M, Y')}}</td>
                    <td>{{$user->name}}</td>
                    <td>{{!empty($value['start_time']) ? $value['start_time'] : 'N/A' }}</td>
                    <td>{{$value['status']}}</td>
                    <td>N/A</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="text-center">
        {!! $dev_task->links() !!}
    </div>
</div>


@endsection 