@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Users Management</h2>
            <div class="pull-left">
            </div>
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('users.create') }}">+</a>
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
            <th>No</th>
            <th>Name</th>
            <th>Email</th>
            <th>Roles</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($data as $key => $user)
            <tr>
                <td>{{ ++$i }}</td>
                <td><span class="user-status {{ $user->isOnline() ? 'is-online' : '' }}"></span> {{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if(!empty($user->getRoleNames()))
                        @foreach($user->getRoleNames() as $v)
                            <label class="badge badge-success">{{ $v }}</label>
                        @endforeach
                    @endif
                </td>
                <td>
                    <a class="btn btn-image" href="{{ route('users.show',$user->id) }}"><img src="/images/view.png" /></a>
                    <a class="btn btn-image" href="{{ route('users.edit',$user->id) }}"><img src="/images/edit.png" /></a>

                    {!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->id],'style'=>'display:inline']) !!}
                    <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
    </table>
    </div>


    {!! $data->render() !!}


@endsection
