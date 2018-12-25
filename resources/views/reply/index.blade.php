@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Quick Replies List</h2>
            <div class="pull-left">
            </div>
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('reply.create') }}">+</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="table-responsive">
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
                    <a class="btn btn-image" href="{{ route('reply.edit',$reply->id) }}"><img src="/images/edit.png" /></a>
                    {!! Form::open(['method' => 'DELETE','route' => ['reply.destroy',$reply->id],'style'=>'display:inline']) !!}
                    <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
    </table>
    </div>

    {!! $replies->links() !!}

@endsection
