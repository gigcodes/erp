@extends('layouts.app')

@section('title', 'Daily Planner')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Daily Planner - {{ $planned_at }}</h2>
            <div class="pull-left">
              @if (Auth::user()->hasRole('Admin'))
                <form action="{{ route('dailyplanner.index') }}" class="form-inline" method="GET">
                  <div class="form-group mr-3">
                    <select class="form-control input-sm ml-3" name="user_id">
                      <option value="">Select a User</option>

                      @foreach ($users_array as $id => $user)
                        <option value="{{ $id }}" {{ isset($userid) && $id == $userid ? 'selected' : '' }}>{{ $user }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group ml-3">
                    <div class='input-group date' id='planned-datetime'>
                      <input type='text' class="form-control input-sm" name="planned_at" value="{{ $planned_at }}" />

                      <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                      </span>
                    </div>
                  </div>

                  <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
                </form>
              @endif
            </div>

            <div class="pull-right">
              {{-- <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#documentCreateModal">+</a> --}}
              <button type="button" class="btn btn-xs btn-secondary" id="showMeetingsButton">Show Meetings</button>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="row no-gutters mt-3">
      <div class="col-xs-12 col-md-12" id="plannerColumn">
        <div class="table-responsive">
          <table class="table table-bordered table-sm">
            <thead>
              <tr>
                <th width="20%">Time</th>
                <th width="40%">Planned</th>
                <th width="20%">Actual</th>
                <th width="20%">Remark</th>
              </tr>
            </thead>

            <tbody>
              @php $count = 0; @endphp
              @foreach ($time_slots as $time_slot => $data)
                {{-- <tr id="timeslot{{ $count }}">
                  <td class="p-2" rowspan="{{ (count($data) + 1) > 5 ? 5 : (count($data) + 1)  }}">{{ $time_slot }}</td>
                </tr> --}}
                @if (count($data) > 0)
                  @foreach ($data as $key => $task)
                    {{-- @if () --}}
                      <tr class="{{ $key <= 3 ? '' : "hidden hiddentask$count" }}">
                        {{-- @if ($key > 3) --}}
                          <td class="p-2">
                            @if ($key == 0)
                              {{ $time_slot }}
                            @endif
                          </td>
                        {{-- @endif --}}
                        <td class="p-2">
                          <div class="d-flex justify-content-between">
                            <span>
                              @if ($task->activity == '')
                                {{ $task->task_subject ?? substr($task->task_details, 0, 20) }}
                              @else
                                {{ $task->activity }}
                              @endif

                              @if ($task->pending_for != 0)
                                - pending for {{ $task->pending_for }} days
                              @endif
                            </span>

                            <span>
                              @if ($task->is_completed == '')
                                <button type="button" class="btn btn-image task-complete p-0 m-0" data-id="{{ $task->id }}" data-type="{{ $task->activity != '' ? 'activity' : 'task' }}"><img src="/images/incomplete.png" /></button>
                              @endif

                              @if ($key == 3)
                                <button type="button" class="btn btn-image show-tasks p-0 m-0" data-count="{{ $count }}" data-rowspan="{{ count($data) + 2 }}">v</button>
                              @endif
                            </span>
                          </div>
                        </td>
                        <td class="p-2 task-time">{{ $task->is_completed ? \Carbon\Carbon::parse($task->is_completed)->format('d-m H:i') : '' }}</td>
                        <td class="expand-row table-hover-cell p-2">
                          <span class="td-mini-container">
                            {{ $task->remarks()->count() ? $task->remarks()->first()->remark : '' }}
                          </span>

                          <span class="td-full-container hidden">
                              <ul>
                                @if ($task->remarks()->count())
                                  @foreach ($task->remarks as $remark)
                                    <li>{{ $remark->remark }} on {{ \Carbon\Carbon::parse($remark->created_at)->format('d-m H:i') }}</li>
                                  @endforeach
                                @endif
                              </ul>

                            <span class="d-flex">
                              <input type="text" class="form-control input-sm quick-remark-input" name="remark" placeholder="Remark" value="">

                              <button type="button" class="btn btn-image quick-remark-button" data-id="{{ $task->id }}"><img src="/images/filled-sent.png" /></button>
                            </span>
                          </span>
                          {{-- <button type="button" class="btn btn-image make-remark p-0 m-0" data-toggle="modal" data-target="#makeRemarkModal" data-id="{{ $task->id }}"><img src="/images/remark.png" /></button> --}}
                        </td>
                      </tr>
                    {{-- @endif --}}
                  @endforeach
                @else
                  <tr>
                    <td class="p-2">{{ $time_slot }}</td>
                    <td class="p-2"></td>
                    <td class="p-2 task-complete"></td>
                    <td class="p-2"></td>
                  </tr>
                @endif

                {{-- @if (count($data) == 0)
                  <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                @endif --}}

                <tr>
                  {{-- @if (count($data) != 0) --}}

                  <td class="p-2"></td>
                  {{-- @endif --}}
                  <td class="p-2">
                    {{-- <select class="form-control input-sm plan-task" name="task" data-timeslot="{{ $time_slot }}" data-targetid="timeslot{{ $count }}">
                      <option value="">Select a Task</option>

                      @foreach ($tasks['list'] as $task)
                        <option value="{{ $task['id'] }}">#{{ $task['id'] }} {{ $task['task_subject'] }} - {{ substr($task['task_details'], 0, 20) }}</option>
                      @endforeach
                    </select> --}}

                    <div class="d-flex">
                      <select class="selectpicker form-control input-sm plan-task" data-live-search="true" data-size="15" name="task" title="Select a Task" data-timeslot="{{ $time_slot }}" data-targetid="timeslot{{ $count }}">
                        @foreach ($tasks as $task)
                          <option data-tokens="{{ $task['id'] }} {{ $task['task_subject'] }} {{ $task['task_details'] }}" value="{{ $task['id'] }}">#{{ $task['id'] }} {{ $task['task_subject'] }} - {{ substr($task['task_details'], 0, 20) }}</option>
                        @endforeach
                      </select>

                      <input type="text" class="form-control input-sm quick-plan-input" name="task" placeholder="New Plan" data-timeslot="{{ $time_slot }}" data-targetid="timeslot{{ $count }}" value="">

                      <button type="button" class="btn btn-image quick-plan-button" data-timeslot="{{ $time_slot }}" data-targetid="timeslot{{ $count }}"><img src="/images/filled-sent.png" /></button>
                    </div>


                  </td>
                  <td class="p-2"></td>
                  <td class="p-2"></td>
                </tr>

                @php $count++; @endphp
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <div class="col-xs-12 col-md-4 hidden" id="meetingsColumn">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th colspan="3">Meeting & Call</th>
              </tr>
              <tr>
                <th>#</th>
                <th>Time</th>
                <th>Details</th>
              </tr>
            </thead>

            <tbody>
              @foreach ($call_instructions as $key => $instruction)
                <tr>
                  <td>{{ $key + 1 }}</td>
                  <td>{{ \Carbon\Carbon::parse($instruction->created_at)->format('d-m H:i') }}</td>
                  <td>{{ $instruction->instruction }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col text-center">
        <form action="{{ route('dailyplanner.complete') }}" method="POST">
          @csrf

          <button type="submit" class="btn btn-xs btn-secondary">Complete Planner</button>
        </form>
      </div>
    </div>

    {{-- @include('partials.modals.remarks') --}}

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('#planned-datetime').datetimepicker({
        format: 'YYYY-MM-DD'
      });
    });

    $(document).on('change', '.plan-task', function() {
      var time_slot = $(this).data('timeslot');
      var id = $(this).val();
      var thiss = $(this);
      var target_id = $(this).data('targetid');

      if (id != '') {
        $.ajax({
          type: "POST",
          url: "{{ url('task') }}/" + id + '/plan',
          data: {
            _token: "{{ csrf_token() }}",
            time_slot: time_slot,
            planned_at: "{{ $planned_at }}"
          }
        }).done(function(response) {
          // var count = $('#' + target_id).find('td').attr('rowspan');
          // console.log(count, '#' + target_id);
          // $('#' + target_id).find('td').attr('rowspan', parseInt(count, 10)+ 1);
          var row = `<tr>
            <td class="p-2">` + time_slot + `</td>
            <td class="p-2">
              <div class="d-flex justify-content-between">
                <span>
                ` + response.task.task_subject + `
                </span>
                <span>
                  <button type="button" class="btn btn-image task-complete p-0 m-0" data-id="` + response.task.id + `" data-type="task"><img src="/images/incomplete.png" /></button>
                </span>
              </div>
            </td>
            <td class="p-2 task-time"></td>
            <td class="expand-row table-hover-cell p-2">
              <span class="td-mini-container"></span>

              <span class="td-full-container hidden">
                <ul></ul>

                <span class="d-flex">
                  <input type="text" class="form-control input-sm quick-remark-input" name="remark" placeholder="Remark" value="">

                  <button type="button" class="btn btn-image quick-remark-button" data-id="` + response.task.id + `"><img src="/images/filled-sent.png" /></button>
                </span>
              </span>
            </td>
          </tr>`;

          $(thiss).closest('tr').before(row);
        }).fail(function(response) {
          console.log(response);
          alert('Could not plan a task');
        });
      }
    });

    // $(document).on('click', '.make-remark', function(e) {
    //   e.preventDefault();
    //
    //   var id = $(this).data('id');
    //   $('#add-remark input[name="id"]').val(id);
    //
    //   $.ajax({
    //       type: 'GET',
    //       headers: {
    //           'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
    //       },
    //       url: '{{ route('task.gettaskremark') }}',
    //       data: {
    //         id:id,
    //         module_type: "task"
    //       },
    //   }).done(response => {
    //       var html='';
    //
    //       $.each(response, function( index, value ) {
    //         html+=' <p> '+value.remark+' <br> <small>By ' + value.user_name + ' updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
    //         html+"<hr>";
    //       });
    //       $("#makeRemarkModal").find('#remark-list').html(html);
    //   });
    // });

    $(document).on('click', '.quick-remark-button', function(e) {
      e.stopPropagation();

      var id = $(this).data('id');
      var thiss = $(this);
      var remark = $(this).siblings('input[name="remark"]').val();

      $.ajax({
          type: 'POST',
          headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          url: '{{ route('task.addRemark') }}',
          data: {
            id:id,
            remark:remark,
            module_type: 'task'
          },
      }).done(response => {
          $(thiss).siblings('input[name="remark"]').val('');

          var html =' <li> '+ remark +' <br> <small>By You updated on '+ moment().format('DD-M H:mm') +' </small></li>';

          $(thiss).closest('.td-full-container').find('ul').prepend(html);
      }).fail(function(response) {
        console.log(response);

        alert('Could not fetch remarks');
      });
    });

    $(document).on('click', '.task-complete', function(e) {
      e.preventDefault();
      e.stopPropagation();

      var thiss = $(this);
      var task_id = $(thiss).data('id');
      var image = $(this).html();
      var current_user = {{ Auth::id() }};
      var type = $(this).data('type');

      if (type == 'activity') {
        var url = "/dailyActivity/complete/" + task_id;
      } else {
        var url = "/task/complete/" + task_id;
      }

      if (!$(thiss).is(':disabled')) {
        $.ajax({
          type: "GET",
          url: url,
          data: {
            type: 'complete'
          },
          beforeSend: function () {
            $(thiss).text('Completing...');
          }
        }).done(function(response) {
          // $(thiss).parent()
          $(thiss).closest('tr').find('.task-time').text(moment().format('DD-MM HH:mm'));
          $(thiss).remove();
        }).fail(function(response) {
          $(thiss).html(image);

          alert('Could not mark as completed!');

          console.log(response);
        });
      }
    });

    $(document).on('click', '.show-tasks', function() {
      var count = $(this).data('count');
      // var rowspan = $(this)
      $('.hiddentask' + count).toggleClass('hidden');
    });

    function storeDailyActivity(element, activity, time_slot, target_id) {
      $.ajax({
        type: 'POST',
        url: "{{ route('dailyActivity.quick.store') }}",
        data: {
          _token: "{{ csrf_token() }}",
          activity: activity,
          time_slot: time_slot,
          user_id: "{{ isset($userid) && $userid != '' ? $userid : Auth::id() }}",
          for_date: "{{ $planned_at }}"
        }
      }).done(function(response) {
        var count = $('#' + target_id).find('td').attr('rowspan');
        var row = `<tr>
          <td class="p-2"></td>
          <td class="p-2">
            <div class="d-flex justify-content-between">
              <span>
              ` + activity + `
              </span>
              <span>
                <button type="button" class="btn btn-image task-complete p-0 m-0" data-id="` + response.activity.id + `" data-type="activity"><img src="/images/incomplete.png" /></button>
              </span>
            </div>
          </td>
          <td class="p-2 task-time"></td>
          <td class="expand-row table-hover-cell p-2">
            <span class="td-mini-container"></span>

            <span class="td-full-container hidden">
              <ul></ul>

              <span class="d-flex">
                <input type="text" class="form-control input-sm quick-remark-input" name="remark" placeholder="Remark" value="">

                <button type="button" class="btn btn-image quick-remark-button" data-id="` + response.activity.id + `"><img src="/images/filled-sent.png" /></button>
              </span>
            </span>
          </td>
        </tr>`;

        $('#' + target_id).find('td').attr('rowspan', parseInt(count, 10)+ 1);

        $(element).closest('tr').before(row);
        $(element).val('');
      }).fail(function(response) {
        console.log(response);

        alert('Could not create activity');
      });
    }

    $('.quick-plan-input').on('keypress', function(e) {
      console.log(e);
      var key = e.which;
      var thiss = $(this);
      var time_slot = $(this).data('timeslot');
      var target_id = $(this).data('targetid');
      var activity = $(this).val();

      if (key == 13) {
        e.preventDefault();

        storeDailyActivity(thiss, activity, time_slot, target_id);
      }
    });

    $('.quick-plan-button').on('click', function(e) {
      var thiss = $(this);
      var time_slot = $(this).data('timeslot');
      var target_id = $(this).data('targetid');
      var activity = $(this).siblings('.quick-plan-input').val();

      storeDailyActivity(thiss, activity, time_slot, target_id);

      $(this).siblings('.quick-plan-input').val('');
    });

    $('#showMeetingsButton').on('click', function() {
      $('#meetingsColumn').toggleClass('hidden');
      $('#plannerColumn').toggleClass('col-md-8');
      $('#plannerColumn').toggleClass('col-md-12');
    });

    $(document).on('click', '.expand-row', function() {
      var selection = window.getSelection();
      if (selection.toString().length === 0) {
        $(this).find('.td-mini-container').toggleClass('hidden');
        $(this).find('.td-full-container').toggleClass('hidden');
      }
    });

    $(document).on('click', '.quick-remark-input', function(e) {
      e.stopPropagation();
    });
  </script>
@endsection
