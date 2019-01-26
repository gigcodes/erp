@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection

@section('content')
  <div class="row">
    <div class="col-lg-12 margin-tb">
      <h2 class="page-heading">Issue List</h2>
    </div>
  </div>


  @if ($message = Session::get('success'))
  <div class="alert alert-success">
    {{ $message }}
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

  <div class="table-responsive">
    <table class="table table-bordered">
      <tr>
        <th>Priority</th>
        <th>Issue</th>
        <th>Created at</th>
        <th>Action</th>
      </tr>
      @foreach ($issues as $key => $issue)
        <tr>
          <td>{{ $issue->priority }}</td>
          <td>{{ $issue->issue }}</td>
          <td>{{ \Carbon\Carbon::parse($issue->created_at)->format('H:i d-m') }}</td>
          <td>
            <button type="button" data-toggle="modal" data-target="#assignIssueModal" data-id="{{ $issue->id }}" class="btn btn-image assign-issue-button"><img src="/images/edit.png" /></button>

            {!! Form::open(['method' => 'DELETE','route' => ['development.issue.destroy', $issue->id],'style'=>'display:inline']) !!}
              <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
            {!! Form::close() !!}
          </td>
        </tr>
      @endforeach
    </table>
  </div>

  <h3>Modules</h3>

  <form class="form-inline" action="{{ route('development.module.store') }}" method="POST">
    @csrf

    <input type="hidden" name="priority" value="5">
    <input type="hidden" name="status" value="Planned">
    <div class="form-group">
      <input type="text" class="form-control" name="name" placeholder="Module" value="{{ old('name') }}" required>

      @if ($errors->has('name'))
        <div class="alert alert-danger">{{$errors->first('name')}}</div>
      @endif
    </div>

    <button type="submit" class="btn btn-secondary ml-3">Add Module</button>
  </form>

  {{-- <div class="table-responsive mt-3">
    <table class="table table-bordered">
      <tr>
        <th>Module</th>
        <th>Action</th>
      </tr>
      @foreach ($modules as $key => $module)
        <tr>
          <td>{{ $module->task }}</td>
          <td>
            {{-- <button type="button" data-toggle="modal" data-target="#editTaskModal" data-task="{{ $task }}" class="btn btn-image edit-task-button"><img src="/images/edit.png" /></button>

            {!! Form::open(['method' => 'DELETE','route' => ['development.destroy', $task->id],'style'=>'display:inline']) !!}
            <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
            {!! Form::close() !!}
          </td>
        </tr>
      @endforeach
    </table>
  </div> --}}

  <div id="assignIssueModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Assign Issue</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form action="" id="assignIssueForm" method="POST">
          @csrf

          <div class="modal-body">
            <div class="form-group">
              <strong>User:</strong>
              <select class="form-control" name="user_id" id="user_field" required>
                @foreach ($users as $id => $name)
                  <option value="{{ $id }}" {{ old('user_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
             </select>

              @if ($errors->has('user_id'))
                  <div class="alert alert-danger">{{$errors->first('user_id')}}</div>
              @endif
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-secondary">Assign</button>
          </div>
        </form>
      </div>

    </div>
  </div>

  <script type="text/javascript">
    $(document).on('click', '.assign-issue-button', function() {
      var issue_id = $(this).data('id');
      var url = "{{ url('development') }}/" + issue_id + "/assignIssue";

      $('#assignIssueForm').attr('action', url);
    });
  </script>

@endsection
