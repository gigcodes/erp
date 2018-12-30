@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Customers List</h2>

                <form action="/customers/" method="GET">
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
                </form>
            </div>
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('customer.create') }}">+</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($message = Session::get('warning'))
        <div class="alert alert-warning">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-bordered">
        <tr>
          <th><a href="/customers{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=name{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Name</a></th>
          <th><a href="/customers{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=email{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Email</a></th>
          <th><a href="/customers{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=phone{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Phone</a></th>
          <th><a href="/customers{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=instagram{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Instagram</a></th>
          <th width="150px">Action</th>
        </tr>
        @foreach ($customers as $key => $customer)
            <tr>
              <td>{{ $customer->name }}</td>
              <td>{{ $customer->email }}</td>
              <td>{{ $customer->phone }}</td>
              <td>{{ $customer->instahandler }}</td>
              <td>
                <a class="btn btn-image" href="{{ route('customer.show', $customer->id) }}"><img src="/images/view.png" /></a>

                {!! Form::open(['method' => 'DELETE','route' => ['customer.destroy', $customer->id],'style'=>'display:inline']) !!}
                <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                {!! Form::close() !!}
              </td>
            </tr>
        @endforeach
    </table>

    {!! $customers->appends(Request::except('page'))->links() !!}

@endsection
