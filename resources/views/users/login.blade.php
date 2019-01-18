@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">User Logins (not accurate yet)</h2>
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
            <th>Name</th>
            <th>Email</th>
            <th>Logged In At</th>
            <th>Logged Out At</th>
        </tr>
        @foreach ($logins as $login)
            <tr>
              <td>{{ $login->user->name }}</td>
              <td><span class="user-status {{ $login->user->isOnline() ? 'is-online' : '' }}"></span> {{ $login->user->email }}</td>
              <td>{{ $login->login_at }}</td>
              <td>{{ $login->logout_at }}</td>
            </tr>
        @endforeach
    </table>
    </div>

    {!! $logins->appends(Request::except('page'))->links() !!}

@endsection
