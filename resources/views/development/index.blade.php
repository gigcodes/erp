@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection

@section('content')
  <div class="row">
    <div class="col-lg-12 margin-tb">
      <h2 class="page-heading">Developer Tasks</h2>

      @can('developer-all')
        <form class="form-inline" action="{{ route('development.index') }}" method="GET">
          <div class="form-group">
            <select class="form-control" name="user">
              @foreach ($users as $id => $name)
                <option value="{{ $id }}" {{ $id == $user ? 'selected' : '' }}>{{ $name }}</option>
              @endforeach
            </select>
          </div>

          <button type="submit" class="btn btn-secondary ml-3">Submit</button>
        </form>
      @endcan

      <div class="pull-right">
        <button type="button" class="btn btn-secondary mb-3" data-toggle="modal" data-target="#createTaskModal">Add Task</button>
      </div>
    </div>

    <div id="createTaskModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Add New Task</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form action="{{ route('development.store') }}" method="POST">
            @csrf

            <div class="modal-body">
              @can('developer-all')
                <div class="form-group">
                  <strong>User:</strong>
                  <select class="form-control" name="user_id" required>
                    @foreach ($users as $id => $name)
                      <option value="{{ $id }}" {{ old('user_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                 </select>

                  @if ($errors->has('user_id'))
                      <div class="alert alert-danger">{{$errors->first('user_id')}}</div>
                  @endif
                </div>
              @endcan

              <div class="form-group">
                <strong>Priority:</strong>
                <select class="form-control" name="priority" required>
                  <option value="1" {{ old('priority') == '1' ? 'selected' : '' }}>1</option>
                  <option value="2" {{ old('priority') == '2' ? 'selected' : '' }}>2</option>
                  <option value="3" {{ old('priority') == '3' ? 'selected' : '' }}>3</option>
                  <option value="4" {{ old('priority') == '4' ? 'selected' : '' }}>4</option>
                  <option value="5" {{ old('priority') == '5' ? 'selected' : '' }}>5</option>
               </select>

                @if ($errors->has('priority'))
                    <div class="alert alert-danger">{{$errors->first('priority')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Task:</strong>
                <textarea class="form-control" name="task" rows="8" cols="80" required>{{ old('task') }}</textarea>
               </select>

                @if ($errors->has('task'))
                  <div class="alert alert-danger">{{$errors->first('task')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Cost:</strong>
                <input type="number" class="form-control" name="cost" value="{{ old('cost') }}" />
               </select>

                @if ($errors->has('cost'))
                  <div class="alert alert-danger">{{$errors->first('cost')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Status:</strong>
                <select class="form-control" name="status" required>
                  <option value="Planned" {{ old('status') == 'Planned' ? 'selected' : '' }}>Planned</option>
                  <option value="In Progress" {{ old('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                  <option value="Done" {{ old('status') == 'Done' ? 'selected' : '' }}>Done</option>
               </select>

                @if ($errors->has('status'))
                    <div class="alert alert-danger">{{$errors->first('status')}}</div>
                @endif
              </div>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Add</button>
            </div>
          </form>
        </div>

      </div>
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

  <div id="exTab2" class="container">
    <ul class="nav nav-tabs">
      <li class="active">
        <a href="#1" data-toggle="tab">Tasks</a>
      </li>
      <li>
        <a href="#2" data-toggle="tab">Completed Tasks</a>
      </li>
    </ul>
  </div>

  <div class="tab-content ">
    <div class="tab-pane active mt-3" id="1">
      <div class="table-responsive">
        <table class="table table-bordered">
          <tr>
            <th>Priority</th>
            <th>Task</th>
            <th>Cost</th>
            <th>Status</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Action</th>
          </tr>
          @foreach ($tasks as $key => $task)
            <tr>
              <td>{{ $task->priority }}</td>
              <td>{{ $task->task }}</td>
              <td>{{ $task->cost }}</td>
              <td>{{ $task->status }}</td>
              <td>{{ $task->start_time ? \Carbon\Carbon::parse($task->start_time)->format('H:i d-m') : '' }}</td>
              <td>{{ $task->end_time ? \Carbon\Carbon::parse($task->end_time)->format('H:i d-m') : '' }}</td>
              <td>
                <button type="button" data-toggle="modal" data-target="#editTaskModal" data-task="{{ $task }}" class="btn btn-image edit-task-button"><img src="/images/edit.png" /></button>

                {!! Form::open(['method' => 'DELETE','route' => ['development.destroy', $task->id],'style'=>'display:inline']) !!}
                <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
                {!! Form::close() !!}
              </td>
            </tr>
          @endforeach
        </table>
      </div>
    </div>

    <div class="tab-pane mt-3" id="2">
      <div class="table-responsive">
        <table class="table table-bordered">
          <tr>
            <th>Priority</th>
            <th>Task</th>
            <th>Cost</th>
            <th>Status</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Action</th>
          </tr>
          @php $total_cost = 0 @endphp
          @foreach ($completed_tasks as $key => $task)
            <tr>
              <td>{{ $task->priority }}</td>
              <td>{{ $task->task }}</td>
              <td>{{ $task->cost }}</td>
              <td>{{ $task->status }}</td>
              <td>{{ $task->start_time ? \Carbon\Carbon::parse($task->start_time)->format('H:i d-m') : '' }}</td>
              <td>{{ $task->end_time ? \Carbon\Carbon::parse($task->end_time)->format('H:i d-m') : '' }}</td>
              <td>
                <button type="button" data-toggle="modal" data-target="#editTaskModal" data-task="{{ $task }}" class="btn btn-image edit-task-button"><img src="/images/edit.png" /></button>

                {!! Form::open(['method' => 'DELETE','route' => ['development.destroy', $task->id],'style'=>'display:inline']) !!}
                <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
                {!! Form::close() !!}
              </td>
            </tr>

            @php $total_cost += $task->cost @endphp
          @endforeach
          <tr>
            <td></td>
            <td class="text-right"><strong>Total:</strong></td>
            <td><strong>{{ $total_cost }}</strong></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
        </table>
      </div>
    </div>
  </div>

  <h3>Modules</h3>

  <form class="form-inline" action="{{ route('development.module.store') }}" method="POST">
    @csrf

    <input type="hidden" name="priority" value="5">
    <input type="hidden" name="status" value="Planned">
    <div class="form-group">
      <input type="text" class="form-control" name="task" placeholder="Module" value="{{ old('task') }}" required>

      @if ($errors->has('task'))
        <div class="alert alert-danger">{{$errors->first('task')}}</div>
      @endif
    </div>

    <button type="submit" class="btn btn-secondary ml-3">Add Module</button>
  </form>

  <div class="table-responsive mt-3">
    <table class="table table-bordered">
      <tr>
        <th>Module</th>
        <th>Action</th>
      </tr>
      @foreach ($modules as $key => $module)
        <tr>
          <td>{{ $module->task }}</td>
          <td>
            <button type="button" data-toggle="modal" data-target="#assignModuleModal" data-id="{{ $module->id }}" class="btn btn-image assign-module-button"><img src="/images/edit.png" /></button>

            {!! Form::open(['method' => 'DELETE','route' => ['development.module.destroy', $module->id],'style'=>'display:inline']) !!}
            <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
            {!! Form::close() !!}
          </td>
        </tr>
      @endforeach
    </table>
  </div>

  <div id="editTaskModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Edit Task</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form action="" id="editTaskForm" method="POST">
          @csrf

          <div class="modal-body">
            @can('developer-all')
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
            @endcan

            <div class="form-group">
              <strong>Priority:</strong>
              <select class="form-control" name="priority" id="priority_field" required>
                <option value="1" {{ old('priority') == '1' ? 'selected' : '' }}>1</option>
                <option value="2" {{ old('priority') == '2' ? 'selected' : '' }}>2</option>
                <option value="3" {{ old('priority') == '3' ? 'selected' : '' }}>3</option>
                <option value="4" {{ old('priority') == '4' ? 'selected' : '' }}>4</option>
                <option value="5" {{ old('priority') == '5' ? 'selected' : '' }}>5</option>
             </select>

              @if ($errors->has('priority'))
                  <div class="alert alert-danger">{{$errors->first('priority')}}</div>
              @endif
            </div>

            <div class="form-group">
              <strong>Task:</strong>
              <textarea class="form-control" name="task" rows="8" cols="80" id="task_field" required>{{ old('task') }}</textarea>
             </select>

              @if ($errors->has('task'))
                <div class="alert alert-danger">{{$errors->first('task')}}</div>
              @endif
            </div>

            <div class="form-group">
              <strong>Cost:</strong>
              <input type="number" class="form-control" name="cost" id="cost_field" value="{{ old('cost') }}" />
             </select>

              @if ($errors->has('cost'))
                <div class="alert alert-danger">{{$errors->first('cost')}}</div>
              @endif
            </div>

            <div class="form-group">
              <strong>Status:</strong>
              <select class="form-control" name="status" id="status_field" required>
                <option value="Planned" {{ old('status') == 'Planned' ? 'selected' : '' }}>Planned</option>
                <option value="In Progress" {{ old('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                <option value="Done" {{ old('status') == 'Done' ? 'selected' : '' }}>Done</option>
             </select>

              @if ($errors->has('status'))
                  <div class="alert alert-danger">{{$errors->first('status')}}</div>
              @endif
            </div>

            <div class="form-group">
              <strong>Start Time:</strong>
              <div class='input-group date' id='start_time'>
                <input type='text' class="form-control" name="start_time" id="start_time_field" value="{{ date('Y-m-d H:i') }}" />

                <span class="input-group-addon">
                  <span class="glyphicon glyphicon-calendar"></span>
                </span>
              </div>

              @if ($errors->has('start_time'))
                  <div class="alert alert-danger">{{$errors->first('start_time')}}</div>
              @endif
            </div>

            <div class="form-group">
              <strong>End Time:</strong>
              <div class='input-group date' id='end_time'>
                <input type='text' class="form-control" name="end_time" id="end_time_field" value="{{ date('Y-m-d H:i') }}" />

                <span class="input-group-addon">
                  <span class="glyphicon glyphicon-calendar"></span>
                </span>
              </div>

              @if ($errors->has('end_time'))
                  <div class="alert alert-danger">{{$errors->first('end_time')}}</div>
              @endif
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-secondary">Update</button>
          </div>
        </form>
      </div>

    </div>
  </div>

  <div id="assignModuleModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Assign Module</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form action="" id="assignModuleForm" method="POST">
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

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script type="text/javascript">
    $('#start_time, #end_time').datetimepicker({
      format: 'YYYY-MM-DD HH:mm'
    });

    $(document).on('click', '.edit-task-button', function() {
      var task = $(this).data('task');
      var url = "{{ url('development') }}/" + task.id + "/edit";

      @can('developer-all')
        $('#user_field').val(task.user_id);
      @endcan
      $('#priority_field').val(task.priority);
      $('#task_field').val(task.task);
      $('#cost_field').val(task.cost);
      $('#status_field').val(task.status);
      $('#start_time_field').val(task.start_time);
      $('#end_time_field').val(task.end_time);

      $('#editTaskForm').attr('action', url);
    });

    $(document).on('click', '.assign-module-button', function() {
      var module_id = $(this).data('id');
      var url = "{{ url('development') }}/" + module_id + "/assignModule";

      $('#assignModuleForm').attr('action', url);
    });
  </script>

@endsection
