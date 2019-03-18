@extends('layouts.app')

@section("styles")
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Instructions List</h2>
            {{-- <div class="pull-left">

            </div>
            <div class="pull-right">
              <a class="btn btn-secondary" href="{{ route('order.create') }}">+</a>
            </div> --}}
        </div>
    </div>

    @if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('HOD of CRM'))
      <div class="row mb-3">
        <div class="col-md-10 col-sm-12">
          <form action="{{ route('instruction.index') }}" method="GET" class="form-inline align-items-start" id="searchForm">
            <div class="row full-width" style="width: 100%;">
              <div class="col-md-4 col-sm-12">
                <div class="form-group mr-3">
                  <select class="form-control select-multiple" name="user[]" multiple>
                    @foreach ($users_array as $index => $name)
                      <option value="{{ $index }}" {{ isset($user) && in_array($index, $user) ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-2"><button type="submit" class="btn btn-image"><img src="/images/search.png" /></button></div>
            </div>
          </form>
        </div>
      </div>
    @endif

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div id="exTab3" class="container">
      <ul class="nav nav-tabs">
        <li class="active">
          <a href="#4" data-toggle="tab">Instructions</a>
        </li>
        <li><a href="#5" data-toggle="tab">Complete</a></li>
      </ul>
    </div>

    <div class="tab-content ">
      <div class="tab-pane active mt-3" id="4">

        <div class="form-group ml-3">
          <input type="checkbox" name="" value="" id="select-all-instructions">
          <label for="select-all-instructions">Select All</label>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
            <tr>
              <th rowspan="2">#</th>
              <th rowspan="2">Client Name</th>
              <th rowspan="2">Number</th>
              <th rowspan="2">Assigned to</th>
              <th rowspan="2">Category</th>
              <th rowspan="2">Instructions</th>
              <th rowspan="2" colspan="3" class="text-center">Action</th>
              <th colspan="3" class="text-center">Timing</th>
              <th rowspan="2"><a href="/instruction?sortby=created_at{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Created at</a></th>
              <th rowspan="2">Remark</th>
            </tr>

            <tr>
              <th>Start</th>
              <th>End</th>
              <th>Action</th>
            </tr>
            @foreach ($instructions as $instruction)
                <tr>
                  <td>
                    <input type="checkbox" name="selected_instructions[]" class="select-instruction">
                  </td>
                  <td><a href="{{ route('customer.show', $instruction['customer_id']) }}">{{ isset($instruction['customer']) ? $instruction['customer']['name'] : '' }}</a></td>
                  <td>
                    <span data-twilio-call data-context="customers" data-id="{{ $instruction['customer_id'] }}">{{ isset($instruction['customer']) ? $instruction['customer']['phone'] : '' }}</span>
                  </td>
                  <td>{{ $users_array[$instruction['assigned_to']] ?? '' }}</td>
                  <td>{{ $instruction['category']['name'] }}</td>
                  <td>{{ $instruction['instruction'] }}</td>
                  <td>
                    @if ($instruction['completed_at'])
                      {{ Carbon\Carbon::parse($instruction['completed_at'])->format('d-m H:i') }}
                    @else
                      <a href="#" class="btn-link complete-call" data-id="{{ $instruction['id'] }}">Complete</a>
                    @endif
                  </td>
                  <td>
                    @if ($instruction['completed_at'])
                      Completed
                    @else
                      @if ($instruction['pending'] == 0)
                        <a href="#" class="btn-link pending-call" data-id="{{ $instruction['id'] }}">Mark as Pending</a>
                      @else
                        Pending
                      @endif
                    @endif
                  </td>
                  <td>
                    @if ($instruction['verified'] == 1)
                      <span class="badge">Verified</span>
                    @elseif ($instruction['assigned_from'] == Auth::id() && $instruction['verified'] == 0)
                      <a href="#" class="btn btn-xs btn-secondary verify-btn" data-id="{{ $instruction['id'] }}">Verify</a>
                    @else
                      <span class="badge">Not Verified</span>
                    @endif
                  </td>
                  <td>
                    @if ($instruction['start_time'])
                      {{ \Carbon\Carbon::parse($instruction['start_time'])->format('H:i d-m') }}
                    @endif
                  </td>
                  <td>
                    @if ($instruction['end_time'])
                      {{ \Carbon\Carbon::parse($instruction['end_time'])->format('H:i d-m') }}
                    @endif
                  </td>
                  <td>
                    <button type="button" class="btn-link instruction-edit-button" data-toggle="modal" data-target="#instructionEditModal" data-id="{{ $instruction['id'] }}" data-start="{{ $instruction['start_time'] }}" data-end="{{ $instruction['end_time'] }}">Edit</button>
                  </td>
                  <td>{{ \Carbon\Carbon::parse($instruction['created_at'])->diffForHumans() }}</td>
                  <td>
                    <a href class="add-task" data-toggle="modal" data-target="#addRemarkModal" data-id="{{ $instruction['id'] }}">Add</a>
                    <span> | </span>
                    <a href class="view-remark" data-toggle="modal" data-target="#viewRemarkModal" data-id="{{ $instruction['id'] }}">View</a>
                  </td>
                </tr>
            @endforeach
          </table>
        </div>
        {!! $instructions->appends(Request::except('page'))->links() !!}
      </div>

        <div class="tab-pane mt-3" id="5">

          <div class="table-responsive">
              <table class="table table-bordered">
              <tr>
                <th rowspan="2">Client Name</th>
                <th rowspan="2">Number</th>
                <th rowspan="2">Assigned to</th>
                <th rowspan="2">Category</th>
                <th rowspan="2">Instructions</th>
                <th rowspan="2" colspan="3" class="text-center">Action</th>
                <th colspan="3">Timing</th>
                <th rowspan="2"><a href="/instruction?sortby=created_at{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Created at</a></th>
                <th rowspan="2">Remark</th>
              </tr>

              <tr>
                <th>Start</th>
                <th>End</th>
                <th>Action</th>
              </tr>
              @foreach ($completed_instructions as $instruction)
                  <tr>
                    <td><a href="{{ route('customer.show', $instruction->customer_id) }}">{{ isset($instruction->customer) ? $instruction->customer->name : '' }}</a></td>
                    <td>
                      <span data-twilio-call data-context="customers" data-id="{{ $instruction->customer_id }}">{{ isset($instruction->customer) ? $instruction->customer->phone : '' }}</span>
                    </td>
                    <td>{{ $users_array[$instruction->assigned_to] ?? '' }}</td>
                    <td>{{ $instruction->category->name }}</td>
                    <td>{{ $instruction->instruction }}</td>
                    <td>
                      @if ($instruction->completed_at)
                        {{ Carbon\Carbon::parse($instruction->completed_at)->format('d-m H:i') }}
                      @else
                        <a href="#" class="btn-link complete-call" data-id="{{ $instruction->id }}">Complete</a>
                      @endif
                    </td>
                    <td>
                      @if ($instruction->completed_at)
                        Completed
                      @else
                        @if ($instruction->pending == 0)
                          <a href="#" class="btn-link pending-call" data-id="{{ $instruction->id }}">Mark as Pending</a>
                        @else
                          Pending
                        @endif
                      @endif
                    </td>
                    <td>
                      @if ($instruction->verified == 1)
                        <span class="badge">Verified</span>
                      @elseif ($instruction->assigned_from == Auth::id() && $instruction->verified == 0)
                        <a href="#" class="btn btn-xs btn-secondary verify-btn" data-id="{{ $instruction->id }}">Verify</a>
                      @else
                        <span class="badge">Not Verified</span>
                      @endif
                    </td>
                    <td>
                      @if ($instruction['start_time'])
                        {{ \Carbon\Carbon::parse($instruction['start_time'])->format('H:i d-m') }}
                      @endif
                    </td>
                    <td>
                      @if ($instruction['end_time'])
                        {{ \Carbon\Carbon::parse($instruction['end_time'])->format('H:i d-m') }}
                      @endif
                    </td>
                    <td>
                      <button type="button" class="btn-link instruction-edit-button" data-toggle="modal" data-target="#instructionEditModal" data-id="{{ $instruction['id'] }}" data-start="{{ $instruction['start_time'] }}" data-end="{{ $instruction['end_time'] }}">Edit</button>
                    </td>
                    <td>{{ $instruction->created_at->diffForHumans() }}</td>
                    <td>
                      <a href class="add-task" data-toggle="modal" data-target="#addRemarkModal" data-id="{{ $instruction->id }}">Add</a>
                      <span> | </span>
                      <a href class="view-remark" data-toggle="modal" data-target="#viewRemarkModal" data-id="{{ $instruction->id }}">View</a>
                    </td>
                  </tr>
              @endforeach
          </table>
          </div>
          {!! $completed_instructions->appends(Request::except('completed_page'))->links() !!}
      </div>


    </div>

    <!-- Modal -->
    <div id="addRemarkModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Add New Remark</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>

          </div>
          <div class="modal-body">
            <form id="add-remark">
              <input type="hidden" name="id" value="">
              <textarea rows="1" name="remark" class="form-control"></textarea>
              <button type="button" class="btn btn-secondary mt-2" id="addRemarkButton">Add Remark</button>
          </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>

      </div>
    </div>

    <!-- Modal -->
    <div id="viewRemarkModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">View Remark</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>

          </div>
          <div class="modal-body">
            <div id="remark-list">

            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>

      </div>
    </div>

    <div id="instructionEditModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Edit Instruction Timing</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <form action="" id="instructionEditForm" method="POST">
            @csrf
            @method('PUT')

            <div class="modal-body">
              <div class="form-group">
                <strong>Start Time:</strong>
                <div class='input-group date instruction-start-time'>
                  <input type='text' class="form-control" name="start_time" id="instruction_start_time" value="{{ date('Y-m-d H:i') }}" />

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
                <div class='input-group date instruction-end-time'>
                  <input type='text' class="form-control" name="end_time" id="instruction_end_time" value="{{ date('Y-m-d H:i') }}" />

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

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
       $(".select-multiple").multiselect();
       $('.instruction-start-time').datetimepicker({
         format: 'YYYY-MM-DD HH:mm'
       });
    });

    $(document).on('click', '.complete-call', function(e) {
      e.preventDefault();

      var thiss = $(this);
      var token = "{{ csrf_token() }}";
      var url = "{{ route('instruction.complete') }}";
      var id = $(this).data('id');

      $.ajax({
        type: 'POST',
        url: url,
        data: {
          _token: token,
          id: id
        },
        beforeSend: function() {
          $(thiss).text('Loading');
        }
      }).done( function(response) {
        $(thiss).parent().html(moment(response.time).format('DD-MM HH:mm'));
        $(thiss).remove();
        window.location.href = response.url;
      }).fail(function(errObj) {
        console.log(errObj);
        alert("Could not mark as completed");
      });
    });

    $(document).on('click', '.pending-call', function(e) {
      e.preventDefault();

      var thiss = $(this);
      var token = "{{ csrf_token() }}";
      var url = "{{ route('instruction.pending') }}";
      var id = $(this).data('id');

      $.ajax({
        type: 'POST',
        url: url,
        data: {
          _token: token,
          id: id
        },
        beforeSend: function() {
          $(thiss).text('Loading');
        }
      }).done( function(response) {
        $(thiss).parent().html('Pending');
        $(thiss).remove();
      }).fail(function(errObj) {
        console.log(errObj);
        alert("Could not mark as completed");
      });
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
            module_type: 'instruction'
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
              module_type: "instruction"
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

    $(document).on('click', '.verify-btn', function(e) {
      e.preventDefault();

      var thiss = $(this);
      var id = $(this).data('id');

      $.ajax({
        type: "POST",
        url: "{{ route('instruction.verify') }}",
        data: {
          _token: "{{ csrf_token() }}",
          id: id
        },
        beforeSend: function() {
          $(thiss).text('Verifying...');
        }
      }).done(function(response) {
        $(thiss).parent().html('<span class="badge">Verified</span>');

        $(thiss).remove();
      }).fail(function(response) {
        $(thiss).text('Verify');
        console.log(response);
        alert('Could not verify the instruction!');
      });
    });

    $(document).on('click', '.instruction-edit-button', function() {
      var id = $(this).data('id');
      var start = $(this).data('start');
      var end = $(this).data('end');
      var url = "{{ url('instruction') }}/" + id;

      $('#instructionEditForm').attr('action', url);

      $('#instruction_start_time').val(start.length > 0 ? start : moment().format('YYYY-MM-DD HH:mm'));
      $('#instruction_end_time').val(end.length > 0 ? end : moment().format('YYYY-MM-DD HH:mm'));
    });
  </script>
@endsection
