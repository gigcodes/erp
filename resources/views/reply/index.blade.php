@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Quick Replies List</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('reply.create') }}">Create New Quick Reply</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Model</th>
            <th width="200px">Action</th>
        </tr>
        @foreach ($replies as $key => $reply)
            <tr>
                <td>{{ $reply->id }}</td>
                <td>{{ $reply->reply }}</td>
                <td>{{ $reply->model }}</td>
                <td>
                    <a class="btn btn-primary" href="{{ route('reply.edit',$reply->id) }}">Edit</a>
                    {!! Form::open(['method' => 'DELETE','route' => ['reply.destroy',$reply->id],'style'=>'display:inline']) !!}
                    {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
    </table>

    {!! $replies->links() !!}

@endsection
