@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Quick Replies List</h2>
            <div class="pull-left">
            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#categoryModal">Create Category</button>
                <a class="btn btn-secondary" href="{{ route('reply.create') }}">+</a>
            </div>

            <div id="categoryModal" class="modal fade" role="dialog">
              <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title">Create Category</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>

                  <form action="{{ route('reply.category.store') }}" method="POST" enctype="multipart/form-data" id="approvalReplyForm">
                    @csrf

                    <div class="modal-body">

                      <div class="form-group">
                          <strong>Category Name:</strong>
                          <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                          @if ($errors->has('name'))
                              <div class="alert alert-danger">{{$errors->first('name')}}</div>
                          @endif
                      </div>

                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-secondary">Create</button>
                    </div>
                  </form>
                </div>

              </div>
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
            <th>Category</th>
            <th width="200px">Action</th>
        </tr>
        @foreach ($replies as $key => $reply)
            <tr>
                <td>{{ $reply->id }}</td>
                <td>{{ $reply->reply }}</td>
                <td>{{ $reply->model }}</td>
                <td>{{ $reply->category->name }}</td>
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
