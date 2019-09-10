@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Permission Management</h2>
            <div class="pull-left">
            </div>
            <div class="pull-right">
                @can('role-create')
                    <a class="btn btn-secondary" href="{{ route('roles.create') }}">+</a>
                @endcan
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
            <th width="280px">Action</th>
        </tr>
        @foreach ($roles as $key => $role)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $role->name }}</td>
                <td>
                    <a class="btn btn-image" href="{{ route('roles.show',$role->id) }}"><img src="/images/view.png" /></a>
                    @can('role-edit')
                        <a class="btn btn-image" href="{{ route('roles.edit',$role->id) }}"><img src="/images/edit.png" /></a>
                    @endcan
                    {{--@can('role-delete')
                        {!! Form::open(['method' => 'DELETE','route' => ['roles.destroy', $role->id],'style'=>'display:inline']) !!}
                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                        {!! Form::close() !!}
                    @endcan--}}
                </td>
            </tr>
        @endforeach
    </table>
    </div>


    {!! $roles->render() !!}


@endsection
