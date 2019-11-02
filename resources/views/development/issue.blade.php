@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
@endsection

@section('large_content')
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

    @php
        $priorities = [
          '1' => 'Critical',
          '2' => 'Urgent',
          '3' => 'Normal'
        ];
    @endphp

    <div class="row mb-4">
        <div class="col-md-12">
            <form action="{{ action('DevelopmentController@issueIndex') }}" method="get">
                <div class="row">
                    <div class="col-md-1">
                        <select class="form-control" name="submitted_by" id="submitted_by">
                            <option value="">Submitted by</option>
                            @foreach($users as $id=>$user)
                                <option {{$request->get('submitted_by')==$id ? 'selected' : ''}} value="{{$id}}">{{ $user }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <select class="form-control" name="responsible_user" id="responsible_user">
                            <option value="">Responsible User...</option>
                            @foreach($users as $id=>$user)
                                <option {{$request->get('responsible_user')==$id ? 'selected' : ''}} value="{{$id}}">{{ $user }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <select class="form-control" name="corrected_by" id="corrected_by">
                            <option value="">Correction by</option>
                            @foreach($users as $id=>$user)
                                <option {{$request->get('corrected_by')==$id ? 'selected' : ''}} value="{{$id}}">{{ $user }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <select name="module" id="module_id" class="form-control">
                            <option value="">Module</option>
                            @foreach($modules as $module)
                                <option {{ $request->get('module') == $module->id ? 'selected' : '' }} value="{{ $module->id }}">{{ $module->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="subject" id="subject_query" placeholder="Issue Id / Subject" class="form-control">
                    </div>
                    <div class="col-md-1">
                        <select name="order" id="order_query" class="form-control">
                            <option value="">Order by priority</option>
                            <option value="create">Order by date</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        @if ( isset($_REQUEST['show_resolved']) && $_REQUEST['show_resolved'] == 1 )
                            <input type="checkbox" name="show_resolved" value="1" checked> incl.resolved
                        @else
                            <input type="checkbox" name="show_resolved" value="1"> incl.resolved
                        @endif
                        <button class="btn btn-image">
                            <img src="{{ asset('images/search.png') }}" alt="Search">
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <tr class="add-new-issue">
                <form action="{{ route('development.issue.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <td colspan="12">
                        <select class="form-control d-inline select2" name="module" id="module" style="width: 150px !important;">
                            <option value="0">Select Module</option>
                            @foreach($modules as $module)
                                <option value="{{$module->id}}">{{ $module->name }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="subject" placeholder="Subject..." id="subject" class="form-control d-inline" style="width: 150px !important;">
                        <input type="text" name="issue" placeholder="Issue..." id="issue" class="form-control d-inline" style="width: 150px !important;">
                        <select class="form-control d-inline" name="priority" required style="width: 150px !important;">
                            <option value="">Select Priority...</option>
                            <option value="1" {{ old('priority') == '1' ? 'selected' : '' }}>Critical</option>
                            <option value="2" {{ old('priority') == '2' ? 'selected' : '' }}>Urgent</option>
                            <option value="3" {{ old('priority') == '3' ? 'selected' : '' }}>Normal</option>
                        </select>
                        <input type="file" name="images[]" class="form-control d-inline" multiple style="width: 100px;">
                        <button type="submit" class="btn btn-secondary d-inline">Add Issue</button>
                    </td>
                </form>
            </tr>
            <tr>
                <th width="1%">ID</th>
                <th width="5%">Module</th>
                <th width="10%">Subject</th>
                <th width="5%">Priority</th>
                <th width="15%">Issue</th>
                <th width="5%">Date Created</th>
                <th width="5%">Est. Completion Time</th>
                <th width="5%">Submitted By</th>
                <th width="5%">Assigned To</th>
                <th width="5%">Correction By</th>
                <th width="5%">Resolved</th>
                <th width="5%">Cost</th>
            </tr>
            @foreach ($issues as $key => $issue)
                 @if(auth()->user()->isAdmin())
                    <tr>
                        <td>{{ $issue->id }}</td>
                        <td>{{ $issue->devModule ? $issue->devModule->name : 'Not Specified' }}</td>
                        <td>{{ $issue->subject ?? 'N/A' }}</td>
                        <td>{!! ['N/A', '<strong class="text-danger">Critical</strong>', 'Urgent', 'Normal'][$issue->priority] ?? 'N/A' !!}</td>
                        <td class="expand-row">
                            <div class="td-mini-container">
                                {{ strlen($issue->issue) > 20 ? substr($issue->issue, 0, 20).'...' : $issue->issue }}
                            </div>
                            <div class="td-full-container hidden">
                                {{ $issue->issue }}
                            </div>
                            @if ($issue->getMedia(config('constants.media_tags'))->first())
                            <br />
                                @foreach ($issue->getMedia(config('constants.media_tags')) as $image)
                                    <a href="{{ $image->getUrl() }}" target="_blank" class="d-inline-block">
                                        <img src="{{ $image->getUrl() }}" class="img-responsive" style="width: 50px" alt="">
                                    </a>
                                @endforeach
                            @endif
                            <br />
                            <button class="btn btn-secondary" onclick="sendImage({{ $issue->id }} )">Send Attachment</button>
                            
                            <br>
                            <div>
                                <div class="panel-group">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" href="#collapse_{{$issue->id}}">Messages({{count($issue->communications)}})</a>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </td>
                        <td>{{ \Carbon\Carbon::parse($issue->created_at)->format('H:i d-m') }}</td>
                        <td data-id="{{ $issue->id }}">
                            <div class="form-group">
                                <div class='input-group date estimate-time'>
                                    <input style="min-width: 145px;" placeholder="Time" value="{{ $issue->estimate_time }}" type="text" class="form-control" name="estimate_time_{{$issue->id}}" data-id="{{$issue->id}}" id="estimate_completion_{{$issue->id}}">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </div>
                                <button class="btn btn-secondary btn-xs estimate-time-change" data-id="{{$issue->id}}">Save</button>
                            </div>
                        </td>
                        <td>{{ $issue->submitter ? $issue->submitter->name : 'N/A' }}</td>
                        <td>
                            @if($issue->responsibleUser)
                                {{ $issue->responsibleUser->name  }}
                            @else
                                <select class="set-responsible-user form-control" data-id="{{$issue->id}}" name="responsible_user" id="responsible_user_{{$issue->id}}">
                                    <option value="">Select...</option>
                                    @foreach($users as $id=>$name)
                                        <option value="{{$id}}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </td>
                        <td>
                            <select class="form-control assign-user" data-id="{{$issue->id}}" name="user" id="user_{{$issue->id}}">
                                <option value="">Select...</option>
                                @foreach($users as $id=>$name)
                                    @if( isset($issue->assignedUser->id) && (int) $issue->assignedUser->id == $id )
                                        <option value="{{$id}}" selected>{{ $name }}</option>
                                    @else
                                        <option value="{{$id}}">{{ $name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </td>
                        <td>
                            @if($issue->is_resolved)
                                <strong>Resolved</strong>
                            @else
                                <select name="resolved" id="resolved_{{$issue->id}}" class="form-control resolve-issue" data-id="{{$issue->id}}">
                                    <option {{ $issue->is_resolved==0 ? 'selected' : '' }} value="0">Not Resolved</option>
                                    <option {{ $issue->is_resolved==1 ? 'selected' : '' }} value="1">Resolved</option>
                                </select>
                            @endif
                        </td>
                        <td>
                            @if($issue->cost > 0)
                                {{ $issue->cost }}
                            @else
                                <input type="text" name="cost" id="cost_{{$issue->id}}" placeholder="Amount..." class="form-control save-cost" data-id="{{$issue->id}}">
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td colspan="11">
                            <div id="collapse_{{$issue->id}}" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <div class="messageList" id="message_list_{{$issue->id}}">
                                        @foreach($issue->communications as $message)
                                            <li>{{ $message->message }}</li>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <input type="text" class="form-control send-message" data-id="{{$issue->id}}" id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}">
                                </div>
                            </div>
                        </td>
                    </tr>
                @else
                    @if($issue->submitted_by == Auth::user()->id || $issue->user_id == Auth::user()->id || $issue->responsible_user_id == Auth::user()->id)
                        <tr>
                            <td>{{ $issue->devModule ? $issue->devModule->name : 'Not Specified' }}</td>
                            <td>
                                {{ $issue->issue }}
                                @if ($issue->getMedia(config('constants.media_tags'))->first())
                                    <br>
                                    @foreach ($issue->getMedia(config('constants.media_tags')) as $image)
                                        <a href="{{ $image->getUrl() }}" target="_blank" class="d-inline-block">
                                            <img src="{{ $image->getUrl() }}" class="img-responsive" style="width: 50px" alt="">
                                        </a>
                                    @endforeach
                                @endif
                                <div>
                                    <div class="panel-group">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" href="#collapse_{{$issue->id}}">Messages({{count($issue->communications)}})</a>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($issue->created_at)->format('H:i d-m') }}</td>
                            <td>{{ $issue->submitter ? $issue->submitter->name : 'N/A' }}</td>
                            <td>
                                @if($issue->responsibleUser)
                                    {{ $issue->responsibleUser->name  }}
                                @else
                                    {{--                  <select class="set-responsible-user form-control" data-id="{{$issue->id}}" name="responsible_user" id="responsible_user_{{$issue->id}}">--}}
                                    {{--                    <option value="">Select...</option>--}}
                                    {{--                    @foreach($users as $id=>$name)--}}
                                    {{--                      <option value="{{$id}}">{{ $name }}</option>--}}
                                    {{--                    @endforeach--}}
                                    {{--                  </select>--}}
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($issue->assignedUser)
                                    {{ $issue->assignedUser->name }}
                                @else
                                    {{--                  <select class="form-control assign-user" data-id="{{$issue->id}}" name="user" id="user_{{$issue->id}}">--}}
                                    {{--                    <option value="">Select...</option>--}}
                                    {{--                    @foreach($users as $id=>$name)--}}
                                    {{--                      <option value="{{$id}}">{{ $name }}</option>--}}
                                    {{--                    @endforeach--}}
                                    {{--                  </select>--}}
                                    Unassigned
                                @endif
                            </td>
                            <td>
                                @if($issue->is_resolved)
                                    <strong>Resolved</strong>
                                @else
                                    <select name="resolved" id="resolved_{{$issue->id}}" class="form-control resolve-issue" data-id="{{$issue->id}}">
                                        <option {{ $issue->is_resolved==0 ? 'selected' : '' }} value="0">Not Resolved</option>
                                        <option {{ $issue->is_resolved==1 ? 'selected' : '' }} value="1">Resolved</option>
                                    </select>
                                @endif
                            </td>
                            <td>
                                @if($issue->cost > 0)
                                    {{ $issue->cost }}
                                @else
                                    <input type="text" name="cost" id="cost_{{$issue->id}}" placeholder="Amount..." class="form-control save-cost" data-id="{{$issue->id}}">
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td colspan="11">
                                <div id="collapse_{{$issue->id}}" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="messageList" id="message_list_{{$issue->id}}">
                                            @foreach($issue->communications as $message)
                                                <li>{{ date('d-m-Y H:i:s', strtotime($message->created_at)) }} : {{ $message->message }}</li>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="panel-footer">
                                        <input type="text" class="form-control send-message" data-id="{{$issue->id}}" id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}">
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                @endif
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
        $(document).on('click', '.assign-issue-button', function () {
            var issue_id = $(this).data('id');
            var url = "{{ url('development') }}/" + issue_id + "/assignIssue";

            $('#assignIssueForm').attr('action', url);
        });
    </script>

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                tags: true
            });

            $('.estimate-time').datetimepicker({
                format: 'Y-MM-DD HH:mm'
            });
        });
    </script>
    <script>
        $(document).on('keyup', '.send-message', function (event) {
            if (event.which != 13) {
                return;
            }

            let issueId = $(this).attr('data-id');
            let message = $(this).val();

            if (message == '') {
                return;
            }

            let self = this;

            $.ajax({
                url: "{{action('WhatsAppController@sendMessage', 'issue')}}",
                type: 'POST',
                data: {
                    issue_id: issueId,
                    message: message,
                    _token: "{{csrf_token()}}",
                    status: 2
                },
                success: function () {
                    toastr["success"]("Message sent successfully!", "Message");
                    $('#message_list_' + issueId).append('<li>' + message + '</li>');
                    $(self).removeAttr('disabled');
                    $(self).val('');
                },
                beforeSend: function () {
                    $(self).attr('disabled', true);
                },
                error: function () {
                    alert('There was an error sending the message...');
                    $(self).removeAttr('disabled', true);
                }
            });
        });

        $(document).on('change', '.set-responsible-user', function () {
            let id = $(this).attr('data-id');
            let userId = $(this).val();

            if (userId == '') {
                return;
            }

            $.ajax({
                url: "{{action('DevelopmentController@assignResponsibleUser')}}",
                data: {
                    responsible_user_id: userId,
                    issue_id: id
                },
                success: function () {
                    toastr["success"]("User assigned successfully!", "Message")
                }
            });

        });
        $(document).on('change', '.assign-user', function () {
            let id = $(this).attr('data-id');
            let userId = $(this).val();

            if (userId == '') {
                return;
            }

            $.ajax({
                url: "{{action('DevelopmentController@assignUser')}}",
                data: {
                    user_id: userId,
                    issue_id: id
                },
                success: function () {
                    toastr["success"]("User assigned successfully!", "Message")
                }
            });

        });
        $(document).on('keyup', '.save-cost', function (event) {
            if (event.keyCode != 13) {
                return;
            }
            let id = $(this).attr('data-id');
            let amount = $(this).val();

            $.ajax({
                url: "{{action('DevelopmentController@saveAmount')}}",
                data: {
                    cost: amount,
                    issue_id: id
                },
                success: function () {
                    toastr["success"]("Price updated successfully!", "Message")
                }
            });
        });
        $(document).on('change', '.resolve-issue', function (event) {
            let id = $(this).attr('data-id');
            let status = $(this).val();
            let self = this;

            $.ajax({
                url: "{{action('DevelopmentController@resolveIssue')}}",
                data: {
                    issue_id: id,
                    is_resolved: status
                },
                success: function () {
                    toastr["success"]("Status updated!", "Message")
                }
            });
        });

        $(document).on('click', '.expand-row', function () {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                // if ($(this).data('switch') == 0) {
                //   $(this).text($(this).data('details'));
                //   $(this).data('switch', 1);
                // } else {
                //   $(this).text($(this).data('subject'));
                //   $(this).data('switch', 0);
                // }
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });

        $(document).on('click', '.estimate-time-change', function () {
            let issueId = $(this).data('id');
            let estimate_time = $("#estimate_completion_" + issueId).val();

            $.ajax({
                url: "{{action('DevelopmentController@saveEstimateTime')}}",
                data: {
                    estimate_time: estimate_time,
                    issue_id: issueId
                },
                success: function () {
                    toastr["success"]("Time updated successfully!", "Message")
                }
            });

        });

        function sendImage(id){

           $.ajax({
                url: "{{action('WhatsAppController@sendMessage', 'issue')}}",
                type: 'POST',
                data: {
                    issue_id: id,
                    type : 1,
                    message: '',
                    _token: "{{csrf_token()}}",
                    status: 2
                },
                success: function () {
                    toastr["success"]("Message sent successfully!", "Message");
                    
                },
                beforeSend: function () {
                    $(self).attr('disabled', true);
                },
                error: function () {
                    alert('There was an error sending the message...');
                    $(self).removeAttr('disabled', true);
                }
            });

        }
    </script>
@endsection
