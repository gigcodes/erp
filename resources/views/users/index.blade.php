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

    @include('partials.flash_messages')

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
                <td><span class="user-status {{ $user->isOnline() ? 'is-online' : '' }}"></span> {{ str_replace( '_' , ' ' ,$user->name) }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if(!empty($user->getRoleNames()))
                        @foreach($user->getRoleNames() as $v)
                            <label class="badge badge-success">{{ $v }}</label>
                        @endforeach
                    @endif
                </td>
                <td>
                  @if (Auth::id() == $user->id)
                    <a class="btn btn-image" href="{{ route('users.show',$user->id) }}"><img src="/images/view.png" /></a>
                  @else
                    <a class="btn btn-image" href="{{ route('users.edit',$user->id) }}"><img src="/images/edit.png" /></a>
                  @endif

                  {!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->id],'style'=>'display:inline']) !!}
                  <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                  {!! Form::close() !!}
                  <a href="{{ action('UserActionsController@show', $user->id) }}">Info</a>
                </td>
            </tr>
        @endforeach
    </table>
    </div>


    {!! $data->render() !!}


@endsection
