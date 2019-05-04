@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')
  <div class="row">
    <div class="col-lg-12 margin-tb">
      <h2 class="page-heading">Developer Tasks</h2>

      @can('developer-all')
        <div class="pull-left">
          <form class="form-inline" action="{{ route('development.index') }}" method="GET">
            <div class="form-group">
              <select class="form-control" name="user">
                @foreach ($users as $id => $name)
                  <option value="{{ $id }}" {{ $id == $user ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group ml-3">
              <input type="text" value="" name="range_start" hidden/>
              <input type="text" value="" name="range_end" hidden/>
              <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                <i class="fa fa-calendar"></i>&nbsp;
                <span></span> <i class="fa fa-caret-down"></i>
              </div>
            </div>

            <button type="submit" class="btn btn-secondary ml-3">Submit</button>
          </form>
        </div>
      @endcan

      <div class="pull-right">
        <button type="button" class="btn btn-secondary mb-3" data-toggle="modal" data-target="#createTaskModal">Add Task</button>
      </div>
    </div>
  </div>

  @include('development.partials.modal-task')
  @include('development.partials.modal-quick-task')
  @include('development.partials.modal-remark')

  @include('partials.flash_messages')

  <div id="exTab2" class="container">
    <ul class="nav nav-tabs">
      <li class="active">
        <a href="#1" data-toggle="tab">Tasks</a>
      </li>
      <li>
        <a href="#2" data-toggle="tab">Review Tasks</a>
      </li>
      <li>
        <a href="#3" data-toggle="tab">Completed Tasks</a>
      </li>
    </ul>
  </div>

  <div class="tab-content ">
    <div class="tab-pane active mt-3" id="1">
      <div class="table-responsive">
        <table class="table table-bordered">
          <tr>
            <th width="10%">Priority</th>
            <th width="40%">Task</th>
            <th width="10%">Cost</th>
            <th width="10%">Status</th>
            <th width="10%">Created at</th>
            <th width="10%">Comments</th>
            <th width="10%">Action</th>
          </tr>
          @php
            $priorities = [
              "1" => 'Critical',
              "2" => 'Urgent',
              "3" => 'Normal'
            ];
          @endphp
          @foreach ($tasks as $key => $module_tasks)
            <tr id="module_{{ $key }}">
              <td colspan="7">
                <strong class="ml-5">{{ $key != '' && array_key_exists($key, $module_names) ? $module_names[$key]  : 'General Tasks' }}</strong>
                <button type="button" class="btn btn-xs btn-secondary ml-3 quick-task-add-button" data-toggle="modal" data-target="#quickDevTaskModal" data-id="{{ $key }}">+</button>
              </td>
            </tr>
            @foreach ($module_tasks as $task)
              <tr id="task_{{ $task->id }}">
                <td>{{ $priorities[$task->priority] }}</td>
                <td class="read-more-button table-hover-cell">
                  <span class="short-task-container">{{ $task->subject ?? (substr($task->task, 0, 100) . (strlen($task->task) > 100 ? '...' : '')) }}</span>

                  <span class="long-task-container hidden">
                    {{ ($task->subject ? ($task->subject . '. ') : '') }} <span class="task-container">{{ $task->task }}</span>

                    <textarea name="task" class="form-control quick-task-edit-textarea hidden" rows="8" cols="80">{{ $task->task }}</textarea>

                    <button type="button" class="btn-link quick-edit-task" data-id="{{ $task->id }}">Edit</button>

                    @if ($task->development_details)
                      <ul class="task-details-container">
                        @foreach ($task->development_details as $detail)
                          <li>{{ $detail->remark }} - {{ \Carbon\Carbon::parse($detail->created_at)->format('H:i d-m') }}</li>
                        @endforeach
                      </ul>
                    @endif

                    <input type="text" name="message" class="form-control quick-message-input" data-type="task" placeholder="Details" value="" data-id="{{ $task->id }}">
                  </span>

                  @if ($task->getMedia(config('constants.media_tags'))->first())
                    <br>
                    @foreach ($task->getMedia(config('constants.media_tags')) as $image)
                      <a href="{{ $image->getUrl() }}" target="_blank" class="d-inline-block">
                        <img src="{{ $image->getUrl() }}" class="img-responsive" style="width: 50px" alt="">
                      </a>
                    @endforeach
                  @endif
                </td>
                <td class="{{ $task->user_id == Auth::id() ? 'table-hover-cell quick-edit-price' : '' }}" data-id="{{ $task->id }}">
                  <span class="quick-price">{{ $task->cost }}</span>
                  <input type="number" name="price" class="form-control quick-edit-price-input hidden" placeholder="100" value="{{ $task->cost }}">
                </td>
                <td>
                  <div class="form-group">
                    <select class="form-control update-task-status" name="status" data-id="{{ $task->id }}">
                      <option value="Planned" {{ $task->status == 'Planned' ? 'selected' : '' }}>Planned</option>
                      <option value="In Progress" {{ $task->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                      <option value="Done" {{ $task->status == 'Done' ? 'selected' : '' }}>Done</option>
                    </select>

                    <span class="text-success change_status_message" style="display: none;">Successfully changed task status</span>
                  </div>
                </td>
                <td>{{ $task->created_at ? \Carbon\Carbon::parse($task->created_at)->format('H:i d-m') : '' }}</td>
                <td>
                  <a href class="add-task" data-toggle="modal" data-target="#addRemarkModal" data-id="{{ $task->id }}">Add</a>
                  <span> | </span>
                  <a href class="view-remark" data-toggle="modal" data-target="#viewRemarkModal" data-id="{{ $task->id }}">View</a>
                </td>
                <td>
                  <button type="button" data-toggle="modal" data-target="#editTaskModal" data-task="{{ $task }}" class="btn btn-image edit-task-button"><img src="/images/edit.png" /></button>

                  {{-- {!! Form::open(['method' => 'DELETE','route' => ['development.destroy', $task->id],'style'=>'display:inline']) !!}
                  <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
                  {!! Form::close() !!} --}}
                  {{-- {!! Form::open(['method' => 'DELETE','route' => ['development.destroy', $task->id],'style'=>'display:inline']) !!} --}}
                  <button type="button" class="btn btn-image task-delete-button" data-id="{{ $task->id }}"><img src="/images/archive.png" /></button>
                  {{-- {!! Form::close() !!} --}}
                </td>
              </tr>
            @endforeach
          @endforeach
        </table>
      </div>
    </div>

    <div class="tab-pane mt-3" id="2">
      <div class="table-responsive">
        <table class="table table-bordered">
          <tr>
            <th width="10%">Priority</th>
            <th width="40%">Task</th>
            <th width="10%">Cost</th>
            <th width="10%">Status</th>
            <th width="10%">Created at</th>
            <th width="5%">Start</th>
            <th width="5%">End</th>
            <th width="10%">Comments</th>
            <th width="10%">Action</th>
          </tr>

          @foreach ($review_tasks as $key => $module_tasks)
            <tr>
              <td colspan="9"><strong class="ml-5">{{$key != '' && array_key_exists($key, $module_names) ? $module_names[$key]  : 'General Tasks' }}</strong></td>
            </tr>
            @foreach ($module_tasks as $task)
              <tr id="review_task_{{ $task->id }}">
                <td>{{ $priorities[$task->priority] }}</td>
                <td class="read-more-button table-hover-cell">
                  <span class="short-task-container">{{ $task->subject ?? (substr($task->task, 0, 100) . (strlen($task->task) > 100 ? '...' : '')) }}</span>

                  <span class="long-task-container hidden">
                    {{ ($task->subject ? ($task->subject . '. ') : '') }} <span class="task-container">{{ $task->task }}</span>

                    <textarea name="task" class="form-control quick-task-edit-textarea hidden" rows="8" cols="80">{{ $task->task }}</textarea>

                    <button type="button" class="btn-link quick-edit-task" data-id="{{ $task->id }}">Edit</button>

                    @if ($task->development_details)
                      <ul class="task-details-container">
                        @foreach ($task->development_details as $detail)
                          <li>{{ $detail->remark }} - {{ \Carbon\Carbon::parse($detail->created_at)->format('H:i d-m') }}</li>
                        @endforeach
                      </ul>
                    @endif

                    <input type="text" name="message" class="form-control quick-message-input" data-type="task" placeholder="Details" value="" data-id="{{ $task->id }}">
                  </span>

                  @if ($task->getMedia(config('constants.media_tags'))->first())
                    <br>
                    @foreach ($task->getMedia(config('constants.media_tags')) as $image)
                      <a href="{{ $image->getUrl() }}" target="_blank" class="d-inline-block">
                        <img src="{{ $image->getUrl() }}" class="img-responsive" style="width: 50px" alt="">
                      </a>
                    @endforeach
                  @endif
                </td>
                <td class="{{ $task->user_id == Auth::id() ? 'table-hover-cell quick-edit-price' : '' }}" data-id="{{ $task->id }}">
                  <span class="quick-price">{{ $task->cost }}</span>
                  <input type="number" name="price" class="form-control quick-edit-price-input hidden" placeholder="100" value="{{ $task->cost }}">
                </td>
                <td>
                  <div class="form-group">
                    <select class="form-control update-task-status" name="status" data-id="{{ $task->id }}">
                      <option value="Planned" {{ $task->status == 'Planned' ? 'selected' : '' }}>Planned</option>
                      <option value="In Progress" {{ $task->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                      <option value="Done" {{ $task->status == 'Done' ? 'selected' : '' }}>Done</option>
                    </select>

                    <span class="text-success change_status_message" style="display: none;">Successfully changed task status</span>
                  </div>
                </td>
                <td>{{ $task->created_at ? \Carbon\Carbon::parse($task->created_at)->format('H:i d-m') : '' }}</td>
                <td>{{ $task->start_time ? \Carbon\Carbon::parse($task->start_time)->format('H:i d-m') : '' }}</td>
                <td>{{ $task->end_time ? \Carbon\Carbon::parse($task->end_time)->format('H:i d-m') : '' }}</td>
                <td>
                  <a href class="add-task" data-toggle="modal" data-target="#addRemarkModal" data-id="{{ $task->id }}">Add</a>
                  <span> | </span>
                  <a href class="view-remark" data-toggle="modal" data-target="#viewRemarkModal" data-id="{{ $task->id }}">View</a>
                </td>
                <td>
                  {{-- <form action="{{ route('development.verify', $task->id) }}" method="POST">
                    @csrf --}}
                    <button type="button" class="btn btn-xs btn-secondary task-verify-button" data-id="{{ $task->id }}">Verify</button>
                  {{-- </form> --}}
                  <button type="button" data-toggle="modal" data-target="#editTaskModal" data-task="{{ $task }}" class="btn btn-image edit-task-button"><img src="/images/edit.png" /></button>

                  {{-- {!! Form::open(['method' => 'DELETE','route' => ['development.destroy', $task->id],'style'=>'display:inline']) !!}
                  <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
                  {!! Form::close() !!} --}}
                  <button type="button" class="btn btn-image task-delete-button" data-id="{{ $task->id }}"><img src="/images/archive.png" /></button>
                </td>
              </tr>
            @endforeach
          @endforeach
        </table>
      </div>
    </div>

    <div class="tab-pane mt-3" id="3">
      <div class="table-responsive">
        <table class="table table-bordered">
          <tr>
            <th width="10%">Priority</th>
            <th width="40%">Task</th>
            <th width="10%">Cost</th>
            <th width="10%">Status</th>
            <th width="10%">Created at</th>
            <th width="5%">Start</th>
            <th width="5%">End</th>
            <th width="10%">Comments</th>
            <th width="10%">Action</th>
          </tr>
          @php $total_cost = 0 @endphp
          @foreach ($completed_tasks as $key => $module_tasks)
            <tr>
              <td colspan="9"><strong class="ml-5">{{ $key != '' && array_key_exists($key, $module_names) ? $module_names[$key]  : 'General Tasks' }}</strong></td>
            </tr>
            @foreach ($module_tasks as $task)
              <tr id="completed_task_{{ $task->id }}">
                <td>{{ $priorities[$task->priority] }}</td>
                <td class="read-more-button table-hover-cell">
                  <span class="short-task-container">{{ $task->subject ?? (substr($task->task, 0, 100) . (strlen($task->task) > 100 ? '...' : '')) }}</span>

                  <span class="long-task-container hidden">
                    {{ ($task->subject ? ($task->subject . '. ') : '') }} <span class="task-container">{{ $task->task }}</span>

                    @if ($task->development_details)
                      <ul class="task-details-container">
                        @foreach ($task->development_details as $detail)
                          <li>{{ $detail->remark }} - {{ \Carbon\Carbon::parse($detail->created_at)->format('H:i d-m') }}</li>
                        @endforeach
                      </ul>
                    @endif
                  </span>

                  @if ($task->getMedia(config('constants.media_tags'))->first())
                    <br>
                    @foreach ($task->getMedia(config('constants.media_tags')) as $image)
                      <a href="{{ $image->getUrl() }}" target="_blank" class="d-inline-block">
                        <img src="{{ $image->getUrl() }}" class="img-responsive" style="width: 50px" alt="">
                      </a>
                    @endforeach
                  @endif
                </td>
                <td>{{ $task->cost }}</td>
                <td>{{ $task->status }}</td>
                <td>{{ $task->created_at ? \Carbon\Carbon::parse($task->created_at)->format('H:i d-m') : '' }}</td>
                <td>{{ $task->start_time ? \Carbon\Carbon::parse($task->start_time)->format('H:i d-m') : '' }}</td>
                <td>{{ $task->end_time ? \Carbon\Carbon::parse($task->end_time)->format('H:i d-m') : '' }}</td>
                <td>
                  <a href class="add-task" data-toggle="modal" data-target="#addRemarkModal" data-id="{{ $task->id }}">Add</a>
                  <span> | </span>
                  <a href class="view-remark" data-toggle="modal" data-target="#viewRemarkModal" data-id="{{ $task->id }}">View</a>
                </td>
                <td>
                  <button type="button" data-toggle="modal" data-target="#editTaskModal" data-task="{{ $task }}" class="btn btn-image edit-task-button"><img src="/images/edit.png" /></button>

                  {{-- {!! Form::open(['method' => 'DELETE','route' => ['development.destroy', $task->id],'style'=>'display:inline']) !!}
                  <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
                  {!! Form::close() !!} --}}
                  <button type="button" class="btn btn-image task-delete-button" data-id="{{ $task->id }}"><img src="/images/archive.png" /></button>
                </td>
              </tr>

              @php $total_cost += $task->cost @endphp
            @endforeach
          @endforeach
          <tr>
            <td colspan="2" class="text-right"><strong>Total:</strong></td>
            <td><strong>{{ $total_cost }}</strong></td>
            <td colspan="6"></td>
          </tr>
        </table>
      </div>

      <h3>Amount Paid</h3>

      <form class="form-inline mb-3" action="{{ route('development.cost.store') }}" method="POST">
        @csrf

        <input type="hidden" name="user_id" value="{{ $user }}">
        <div class="form-group">
          <input type="number" class="form-control" name="amount" placeholder="100" value="{{ old('amount') }}" required>

          @if ($errors->has('amount'))
            <div class="alert alert-danger">{{$errors->first('amount')}}</div>
          @endif
        </div>

        <div class="form-group ml-3">
          <strong>Paid On:</strong>
          <div class='input-group date' id='paid_date'>
            <input type='text' class="form-control" name="paid_date" value="{{ date('Y-m-d') }}" />

            <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar"></span>
            </span>
          </div>

          @if ($errors->has('paid_date'))
              <div class="alert alert-danger">{{$errors->first('paid_date')}}</div>
          @endif
        </div>

        <button type="submit" class="btn btn-secondary ml-3">Add Amount</button>
      </form>

      <div class="table-responsive">
        <table class="table table-bordered">
          <tr>
            <th>Paid On</th>
            <th>Amount</th>
          </tr>
          @php $total_paid = 0; @endphp
          @foreach ($amounts as $amount)
            <tr>
              <td>{{ \Carbon\Carbon::parse($amount->paid_date)->format('d-m') }}</td>
              <td>{{ $amount->amount }}</td>
            </tr>
            @php $total_paid += $amount->amount @endphp
          @endforeach
          <tr>
            <td class="text-right"><strong>Total Paid:</strong></td>
            <td>{{ $total_paid }} of {{ $all_time_cost }}</td>
            <td><strong>Left:</strong> {{ $all_time_cost - $total_paid }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>

  <h3>Modules</h3>

  <form class="form-inline" action="{{ route('development.module.store') }}" method="POST">
    @csrf

    {{-- <input type="hidden" name="priority" value="5">
    <input type="hidden" name="status" value="Planned"> --}}
    <div class="form-group">
      <input type="text" class="form-control" name="name" placeholder="Module" value="{{ old('name') }}" required>

      @if ($errors->has('name'))
        <div class="alert alert-danger">{{$errors->first('name')}}</div>
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
          <td>{{ $module->name }}</td>
          <td>
            {{-- <button type="button" data-toggle="modal" data-target="#assignModuleModal" data-id="{{ $module->id }}" class="btn btn-image assign-module-button"><img src="/images/edit.png" /></button> --}}

            {!! Form::open(['method' => 'DELETE','route' => ['development.module.destroy', $module->id],'style'=>'display:inline']) !!}
            <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
            {!! Form::close() !!}
          </td>
        </tr>
      @endforeach
    </table>
  </div>

  {{-- <h3>Comments</h3>

  <div class="row">
    <div class="col-xs-12 col-sm-6">
      <form action="{{ route('development.comment.store') }}" method="POST" enctype="multipart/form-data" class="d-flex">
          @csrf

          <div class="form-group">
            <div class="upload-btn-wrapper btn-group">
              <button type="submit" class="btn btn-image px-1"><img src="/images/filled-sent.png" /></button>
            </div>
          </div>

          <div class="form-group flex-fill">
            <textarea class="form-control" name="message" placeholder="Enter Your Comment" required></textarea>
            <input type="hidden" name="send_to" value="{{ $user }}" />
            <input type="hidden" name="status" value="0" />
          </div>

       </form>
     </div>
  </div>

  <div class="row">
    <div class="col-12">
      @foreach ($comments as $comment)
        <div class="talk-bubble">
          @if ($comment->status == 1)
            <span class="badge badge-warning">!</span>
          @endif
          <div class="talktext">
            <p class="collapsible-message">{{ $comment->message }}</p>
            <em>
              {{ $users[$comment->user_id] }}
              {{ \Carbon\Carbon::parse($comment->created_at)->format('d-m H:i') }}
              @if ($comment->status == 0)
                <a href="#" class="btn-link awaiting-response-button" data-id="{{ $comment->id }}">Awaiting Response</a>
              @endif
            </em>
          </div>
        </div>
      @endforeach
    </div>
  </div> --}}

  {{-- <div id="assignModuleModal" class="modal fade" role="dialog">
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
  </div> --}}

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script type="text/javascript">
    $('#start_time, #end_time, #estimate_time').datetimepicker({
      format: 'YYYY-MM-DD HH:mm'
    });

    $('#paid_date').datetimepicker({
      format: 'YYYY-MM-DD'
    });

    @if ($tab == 'review')
      var hash = window.location.hash.substr(1);

      $('a[href="#2"]').click();

      if (location.hash) {
        setTimeout(function() {
          location.href = location.hash;
        }, 1000);

        $('#' + hash).addClass('row-highlight');
      }
    @elseif ($tab == '3')
      var hash = window.location.hash.substr(1);

      $('a[href="#3"]').click();

      if (location.hash) {
        setTimeout(function() {
          location.href = location.hash;
        }, 1000);

        $('#' + hash).addClass('row-highlight');
      }
    @else
      var hash = window.location.hash.substr(1);

      // $('a[href="#1"]').click();

      if (location.hash) {
        setTimeout(function() {
          location.href = location.hash;
        }, 1000);

        $('#' + hash).addClass('row-highlight');
      }
    @endif

    $(document).on('click', '.edit-task-button', function() {
      var task = $(this).data('task');
      var url = "{{ url('development') }}/" + task.id + "/edit";

      @can('developer-all')
        $('#user_field').val(task.user_id);
      @endcan
      $('#priority_field').val(task.priority);
      $('#task_field').val(task.task);
      $('#task_subject').val(task.subject);
      $('#cost_field').val(task.cost);
      $('#status_field').val(task.status);
      $('#estimate_time_field').val(task.estimate_time);
      $('#start_time_field').val(task.start_time);
      $('#end_time_field').val(task.end_time);

      $('#editTaskForm').attr('action', url);
    });

    $(document).on('click', '.assign-module-button', function() {
      var module_id = $(this).data('id');
      var url = "{{ url('development') }}/" + module_id + "/assignModule";

      $('#assignModuleForm').attr('action', url);
    });

    $(document).on('click', '.awaiting-response-button', function(e) {
      e.preventDefault();

      var thiss = $(this);
      var comment_id = $(this).data('id');

      $.ajax({
        type: "POST",
        url: "{{ url('development') }}/" + comment_id + "/awaiting/response",
        data: {
          _token: "{{ csrf_token() }}"
        },
        beforeSend: function() {
          $(thiss).text('Loading...');
        }
      }).done(function() {
        var badge = $('<span class="badge badge-warning">!</span>');
        $(thiss).closest('.talk-bubble').prepend(badge);
        $(thiss).remove();
      }).fail(function(response) {
        console.log(response);
        alert('Something went wrong');
      });
    });

    let r_s = '';
    let r_e = '{{ date('y-m-d') }}';

    let start = r_s ? moment(r_s,'YYYY-MM-DD') : moment().subtract(6, 'days');
    let end =   r_e ? moment(r_e,'YYYY-MM-DD') : moment();

    jQuery('input[name="range_start"]').val(start.format('YYYY-MM-DD'));
    jQuery('input[name="range_end"]').val(end.format('YYYY-MM-DD'));

    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        maxYear: 1,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    cb(start, end);

    $('#reportrange').on('apply.daterangepicker', function(ev, picker) {

        jQuery('input[name="range_start"]').val(picker.startDate.format('YYYY-MM-DD'));
        jQuery('input[name="range_end"]').val(picker.endDate.format('YYYY-MM-DD'));

    });

    $('.add-task').on('click', function(e) {
      e.preventDefault();
      var id = $(this).data('id');
      $('#add-remark input[name="id"]').val(id);
    });

    $('#addRemarkButton').on('click', function() {
      var id = $('#add-remark input[name="id"]').val();
      var remark = $('#add-remark textarea[name="remark"]').val();

      $.ajax({
          type: 'POST',
          headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          url: '{{ route('task.addRemark') }}',
          data: {
            id:id,
            remark:remark,
            module_type: 'developer'
          },
      }).done(response => {
          alert('Remark Added Success!')
          window.location.reload();
      }).fail(function(response) {
        console.log(response);
      });
    });


    $(".view-remark").click(function () {
      var id = $(this).attr('data-id');

        $.ajax({
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: '{{ route('task.gettaskremark') }}',
            data: {
              id:id,
              module_type: "developer"
            },
        }).done(response => {
            var html='';

            $.each(response, function( index, value ) {
              html+=' <p> '+value.remark+' <br> <small>By ' + value.user_name + ' updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
              html+"<hr>";
            });
            $("#viewRemarkModal").find('#remark-list').html(html);
        });
    });

    $(document).on('dblclick', '.quick-edit-price', function() {
      var id = $(this).data('id');

      $(this).find('.quick-price').addClass('hidden');
      $(this).find('.quick-edit-price-input').removeClass('hidden');
      $(this).find('.quick-edit-price-input').focus();

      $(this).find('.quick-edit-price-input').keypress(function(e) {
        var key = e.which;
        var thiss = $(this);

        if (key == 13) {
          e.preventDefault();
          var cost = $(thiss).val();

          $.ajax({
            type: 'POST',
            url: "{{ url('development') }}/" + id + '/updateCost',
            data: {
              _token: "{{ csrf_token() }}",
              cost: cost,
            }
          }).done(function() {
            $(thiss).addClass('hidden');
            $(thiss).siblings('.quick-price').text(cost);
            $(thiss).siblings('.quick-price').removeClass('hidden');
          }).fail(function(response) {
            console.log(response);

            alert('Could not update cost');
          });
        }
      });
    });

    $(document).on('change', '.update-task-status', function() {
      var status = $(this).val();
      var id = $(this).data('id');
      var thiss = $(this);

      $.ajax({
        type: "POST",
        url: "{{ url('development') }}/" + id + '/status',
        data: {
          _token: "{{ csrf_token() }}",
          status: status
        }
      }).done(function() {
        if (status == 'Done') {
          $(thiss).closest('tr').remove();
        } else {
          $(thiss).siblings('.change_status_message').fadeIn(400);

          setTimeout(function () {
            $(thiss).siblings('.change_status_message').fadeOut(400);
          }, 2000);
        }
      }).fail(function(response) {
        alert('Could not change the status');
        console.log(response);
      });
    });

    $(document).on('click', '.task-delete-button', function() {
      var id = $(this).data('id');
      var thiss = $(this);

      $.ajax({
        type: "POST",
        url: "{{ url('development') }}/" + id + '/destroy',
        data: {
          _token: "{{ csrf_token() }}",
          _method: "DELETE"
        }
      }).done(function() {
        $(thiss).closest('tr').remove();
      }).fail(function(response) {
        alert('Could not delete the task');
        console.log(response);
      });
    });

    $(document).on('click', '.task-verify-button', function() {
      var id = $(this).data('id');
      var thiss = $(this);

      $.ajax({
        type: "POST",
        url: "{{ url('development') }}/" + id + '/verify',
        data: {
          _token: "{{ csrf_token() }}"
        },
        beforeSend: function() {
          $(thiss).text('Verifying...');
        }
      }).done(function() {
        $(thiss).closest('tr').remove();
      }).fail(function(response) {
        $(thiss).text('Verify');
        alert('Could not verify the task');
        console.log(response);
      });
    });

    $(document).on('click', '.read-more-button', function() {
      $(this).find('.short-task-container').toggleClass('hidden');
      $(this).find('.long-task-container').toggleClass('hidden');
    });

    $(document).on('click', '.quick-task-edit-textarea, .quick-message-input', function(e) {
      e.stopPropagation();
    });

    $(document).on('click', '.quick-edit-task', function(e) {
      e.stopPropagation();

      var id = $(this).data('id');

      $(this).siblings('.task-container').addClass('hidden');
      $(this).siblings('.quick-task-edit-textarea').removeClass('hidden');

      $(this).siblings('.quick-task-edit-textarea').keypress(function(e) {
        var key = e.which;
        var thiss = $(this);

        if (key == 13) {
          e.preventDefault();
          var task = $(thiss).val();

          $.ajax({
            type: 'POST',
            url: "{{ url('development') }}/" + id + '/updateTask',
            data: {
              _token: "{{ csrf_token() }}",
              task: task,
            }
          }).done(function() {
            $(thiss).addClass('hidden');
            $(thiss).siblings('.task-container').text(task);
            $(thiss).siblings('.task-container').removeClass('hidden');
            $(thiss).siblings('.quick-task-edit-textarea').addClass('hidden');

            var short_task = task.substr(0, 100);

            $(thiss).closest('.long-task-container').siblings('.short-task-container').text(short_task);
          }).fail(function(response) {
            console.log(response);

            alert('Could not update task');
          });
        }
      });
    });

    $(document).on('keypress', '.quick-message-input', function(e) {
      var key = e.which;
      var thiss = $(this);
      var type = $(this).data('type');

      if (type == 'task') {
        var module_type = 'task-detail';
        var container = '.task-details-container';
      }
      //  else {
      //   var module_type = 'complaint-plan-comment';
      //   var container = '.plan-comments-container';
      // }

      if (key == 13) {
        e.preventDefault();
        var phone = $(thiss).val();

        var id = $(thiss).data('id');
        var remark = $(thiss).val();

        $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: '{{ route('task.addRemark') }}',
            data: {
              id:id,
              remark:remark,
              module_type: module_type
            },
        }).done(response => {
            // alert('Remark Added Success!')
            // window.location.reload();
            var remark_message = $('<li>' + remark + ' - ' + moment().format('HH:mm DD-MM') + '</li>');
            $(thiss).siblings(container).prepend(remark_message);
            $(thiss).val('');
        }).fail(function(response) {
          console.log(response);
        });
      }
    });

    $(document).on('click', '.quick-task-add-button', function() {
      var id = $(this).data('id');

      $('#quick_module_id').val(id);
    });

    $(document).on('click', '#quickTaskSubmit', function() {
      var module_id = $('#quick_module_id').val();
      var task = $('#quick_task_task').val();
      var thiss = $(this);
      var auth_id = "{{ Auth::id() }}";

      $.ajax({
        type: "POST",
        url: "{{ route('development.store') }}",
        data: {
          _token: "{{ csrf_token() }}",
          module_id: module_id,
          user_id: {{ $user }},
          priority: 2,
          status: 'Planned',
          task: task
        },
        beforeSend: function() {
          $(thiss).text('Adding...');
        }
      }).done(function(response) {
        $('#quick_module_id').val('');
        $('#quick_task_task').val('');
        $(thiss).text('Add');
        $('#quickDevTaskModal').find('.close').click();

        var class_name = response.task.user_id == auth_id ? 'table-hover-cell quick-edit-price' : '';
        var task_html = `<tr id="task_` + response.task.id + `">
          <td>Urgent</td>
          <td class="read-more-button table-hover-cell">
            <span class="short-task-container">` + response.task.task.substr(0, 100) + `</span>

            <span class="long-task-container hidden">
              <span class="task-container">` + response.task.task + `</span>

              <textarea name="task" class="form-control quick-task-edit-textarea hidden" rows="8" cols="80">` + response.task.task + `</textarea>

              <button type="button" class="btn-link quick-edit-task" data-id="` + response.task.id + `">Edit</button>

              <ul class="task-details-container">

              </ul>

              <input type="text" name="message" class="form-control quick-message-input" data-type="task" placeholder="Details" value="" data-id="` + response.task.id + `">
            </span>
          </td>
          <td class="` + class_name + `" data-id="` + response.task.id + `">
            <span class="quick-price"></span>
            <input type="number" name="price" class="form-control quick-edit-price-input hidden" placeholder="100" value="">
          </td>
          <td>
            <div class="form-group">
              <select class="form-control update-task-status" name="status" data-id="` + response.task.id + `">
                <option value="Planned">Planned</option>
                <option value="In Progress">In Progress</option>
                <option value="Done">Done</option>
              </select>

              <span class="text-success change_status_message" style="display: none;">Successfully changed task status</span>
            </div>
          </td>
          <td>` + moment(response.task.created_at).format('HH:mm DD-MM') + `</td>
          <td>
            <a href class="add-task" data-toggle="modal" data-target="#addRemarkModal" data-id="` + response.task.id + `">Add</a>
            <span> | </span>
            <a href class="view-remark" data-toggle="modal" data-target="#viewRemarkModal" data-id="` + response.task.id + `">View</a>
          </td>
          <td>
            <button type="button" data-toggle="modal" data-target="#editTaskModal" data-task="` + response.task + `" class="btn btn-image edit-task-button"><img src="/images/edit.png" /></button>

            <button type="button" class="btn btn-image task-delete-button" data-id="` + response.task.id + `"><img src="/images/archive.png" /></button>
          </td>
        </tr>`;

        var module_id = response.task.module_id ? response.task.module_id : '';

        $('#module_' + module_id).after(task_html);
      }).fail(function(response) {
        $(thiss).text('Add');

        console.log(response);
        alert('Could not create a quick task');
      });
    });
  </script>
@endsection
