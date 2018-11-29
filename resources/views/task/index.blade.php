@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Task</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('task.create') }}"> Create New Task</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-bordered" style="margin-top: 25px">
        <tr>
            <th>ID</th>
            <th>Task Name</th>
            <th>Details</th>
            <th>Related to</th>
            <th width="200px">Action</th>
        </tr>
        @foreach ($task as $key => $value)
            <tr>
                <td>{{ $value->id }}</td>
                <td>{{ $value->name }}</td>
                <td>{{ $value->details }}</td>
                <td>{{ $value->related}}</td>
                <td>
                    <a class="btn btn-primary" href="{{ route('task.edit',$value->id) }}">Edit</a>
                    @if ($value->userid == Auth::id())
                    {!! Form::open(['method' => 'DELETE','route' => ['task.destroy',$value->id],'style'=>'display:inline']) !!}
                    {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                    {!! Form::close() !!}
                     @endif
                </td>
            </tr>
        @endforeach
    </table>

    {!! $task->links() !!}

@endsection
