@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Passwords Manager</h2>
            <div class="pull-left">
              {{-- <form action="/order/" method="GET">
                  <div class="form-group">
                      <div class="row">
                          <div class="col-md-12">
                              <input name="term" type="text" class="form-control"
                                     value="{{ isset($term) ? $term : '' }}"
                                     placeholder="Search">
                          </div>
                          <div class="col-md-4">
                              <button hidden type="submit" class="btn btn-primary">Submit</button>
                          </div>
                      </div>
                  </div>
              </form> --}}
            </div>
            <div class="pull-right">
              <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#passwordCreateModal">+</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="table-responsive mt-3">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Website</th>
            <th>Username</th>
            <th>Password</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($passwords as $password)
            <tr>
              <td>
                {{ $password->website }}
                <br>
                <a href="{{ $password->url }}" target="_blank"><small class="text-muted">{{ $password->url }}</small></a>
              </td>
              <td>{{ $password->username }}</td>
              <td>{{ Crypt::decrypt($password->password) }}</td>
              <td></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {!! $passwords->appends(Request::except('page'))->links() !!}

    <div id="passwordCreateModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <form action="{{ route('password.store') }}" method="POST">
            @csrf

            <div class="modal-header">
              <h4 class="modal-title">Store a Password</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <strong>Website:</strong>
                <input type="text" name="website" class="form-control" value="{{ old('website') }}">

                @if ($errors->has('website'))
                  <div class="alert alert-danger">{{$errors->first('website')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>URL:</strong>
                <input type="text" name="url" class="form-control" value="{{ old('url') }}" required>

                @if ($errors->has('url'))
                  <div class="alert alert-danger">{{$errors->first('url')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Username:</strong>
                <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>

                @if ($errors->has('username'))
                  <div class="alert alert-danger">{{$errors->first('username')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Password:</strong>
                <input type="text" name="password" class="form-control" value="{{ old('password') }}" required>

                @if ($errors->has('password'))
                  <div class="alert alert-danger">{{$errors->first('password')}}</div>
                @endif
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Store</button>
            </div>
          </form>
        </div>

      </div>
    </div>

@endsection
