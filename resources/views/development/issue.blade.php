@extends('layouts.app')

@section('favicon' , 'development-issue.png')

@if($title == "devtask")
    @section('title', 'Development Issue')
@else
    @section('title', 'Development Task')
@endif

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
    </style>
@endsection

@section('large_content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">{{ ucfirst($title) }} List</h2>
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
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row mb-4">
        <div class="col-md-12">
            <form action="{{ url("development/list/$title") }}" method="get">
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
                        <select class="form-control" name="assigned_to" id="assigned_to">
                            <option value="">Assigned To</option>
                            @foreach($users as $id=>$user)
                                <option {{$request->get('assigned_to')==$id ? 'selected' : ''}} value="{{$id}}">{{ $user }}</option>
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
                        <input type="text" name="subject" id="subject_query" placeholder="Issue Id / Subject" class="form-control" value="{{ (!empty(app('request')->input('subject'))  ? app('request')->input('subject') : '') }}">
                    </div>
                    <div class="col-md-1">
                        <select name="order" id="order_query" class="form-control">
                            <option {{$request->get('order')== "" ? 'selected' : ''}} value="create">Order by date descending</option>
                            <option {{$request->get('order')== "priority" ? 'selected' : ''}} value="">Order by priority</option>
                            <option {{$request->get('order')== "create_asc" ? 'selected' : ''}} value="create">Order by date</option>
                        </select>
                    </div>
                    @if($title == 'devtask')
                        <div class="col-md-2">
                        <select name="task_status" id="task_status" class="form-control change-task-status">
                            <option value="">Please Select</option>
                            <option value="Planned" {{ (!empty(app('request')->input('task_status')) && app('request')->input('task_status') ==  'Planned' ? 'selected' : '') }}>Planned</option>
                            <option value="In Progress" {{ (!empty(app('request')->input('task_status')) && app('request')->input('task_status') ==  'In Progress' ? 'selected' : '') }}>In Progress</option>
                            <option value="Done" {{ (!empty(app('request')->input('task_status')) && app('request')->input('task_status') ==  'Done' ? 'selected' : '') }}>Done</option>
                        </select>
                        </div>
                    @endif
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
                    <div class="col-md-2">
                        <a class="btn btn-secondary d-inline priority_model_btn">Priority</a>
                    </div>
                </div>
            </form>
            @if($title == 'devtask')
            <a href="javascript:" class="btn btn-default"  id="newTaskModalBtn" data-toggle="modal" data-target="#newTaskModal" style="float: right;">Add New Dev Task </a>
            @endif
        </div>
    </div>

    <?php
    $query = http_build_query(Request::except('page'));
    $query = url()->current() . (($query == '') ? $query . '?page=' : '?' . $query . '&page=');
    ?>

    <div class="form-group position-fixed" style="top: 50px; left: 20px;">
        Goto :
        <select onchange="location.href = this.value;" class="form-control" id="page-goto">
            @for($i = 1 ; $i <= $issues->lastPage() ; $i++ )
                <option data-value="{{$i}}" value="{{ $query.$i }}" {{ ($i == $issues->currentPage() ? 'selected' : '') }}>{{ $i }}</option>
            @endfor
        </select>
    </div>
    <div class="infinite-scroll">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                @if($title == 'issue')
                <tr class="add-new-issue">
                    <form action="{{ route('development.issue.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <td colspan="12">
                            <div class="row">
                                <div class="col-md-2">
                                    <select class="form-control d-inline select2" name="module" id="module" style="width: 150px !important;">
                                        <option value="0">Select Module</option>
                                        @foreach($modules as $module)
                                            <option value="{{$module->id}}">{{ $module->name }}</option>
                                        @endforeach
                                    </select>
                                </div>    
                                <div class="col-md-2">  
                                    <input type="text" name="subject" placeholder="Subject..." id="subject" class="form-control d-inline" style="width: 150px !important;">
                                </div>
                                <div class="col-md-2">    
                                    <input type="text" name="issue" placeholder="Issue..." id="issue" class="form-control d-inline" style="width: 150px !important;">
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control d-inline" name="priority" required style="width: 150px !important;">
                                        <option value="">Select Priority...</option>
                                        <option value="1" {{ old('priority') == '1' ? 'selected' : '' }}>Critical</option>
                                        <option value="2" {{ old('priority') == '2' ? 'selected' : '' }}>Urgent</option>
                                        <option value="3" {{ old('priority') == '3' ? 'selected' : '' }}>Normal</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control select2" name="responsible_user_id" id="responsible_user_id">
                                        <option value="">Responsible User...</option>
                                        @foreach($users as $id=>$user)
                                            <option value="{{$id}}">{{ $user }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <input type="file" name="images[]" class="form-control d-inline" multiple style="width: 100px;">
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-secondary d-inline">Add Issue</button>
                                </div>
                            </div>
                        </td>
                    </form>
                </tr>
                @endif
                <tr>
                    <th width="1%">ID</th>
                    {{--<th width="6%">Date Created <hr><span>ETA</span></th>--}}
                    <th width="5%">Module</th>
                    <th width="10%">Subject</th>
                    <th width="5%">Priority</th>
                    <th width="15%">Issue</th>
                    <th width="5%">Date Created</th>
                    <th width="5%">Est Completion Time</th>
                    <th width="5%">Submitted By  </th>
                    <th width="5%">Assigned To</th>
                    <th width="5%">Corrected By</th>
                    <th width="5%">Resolved</th>
                    <th width="5%">Cost</th>
                </tr>
                @foreach ($issues as $key => $issue)
                     @if(auth()->user()->isAdmin())
                        <tr>
                            <td>
                                <a href="{{ url("development/task-detail/$issue->id") }}">{{ $issue->id }}
                                    @if($issue->is_resolved==0)
                                        <input type="checkbox" name="selected_issue[]" value="{{$issue->id}}" {{in_array($issue->id, $priority) ? 'checked' : ''}}>
                                    @endif
                                </a>
                            </td>
                            <td style="vertical-align: middle;">{{ $issue->developerModule ? $issue->developerModule->name : 'Not Specified' }}</td>
                            <td style="vertical-align: middle;">{{ $issue->subject ?? 'N/A' }}</td>
                            <td style="vertical-align: middle;">{!! ['N/A', '<strong class="text-danger">Critical</strong>', 'Urgent', 'Normal'][$issue->priority] ?? 'N/A' !!}</td>
                            <td class="expand-row">
                                <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='developer_task' data-id="{{ $issue->id }}" title="Load messages"><img src="/images/chat.png" alt=""></button>
                                <div class="td-mini-container">
                                    {{ strlen($issue->task) > 20 ? substr($issue->task, 0, 20).'...' : $issue->task }}
                                </div>
                                <div class="td-full-container hidden">
                                    {!! nl2br($issue->task) !!}
                                @if ($issue->getMedia(config('constants.media_tags'))->first())
                                <br />
                                    @foreach ($issue->getMedia(config('constants.media_tags')) as $image)
                                        <a href="{{ $image->getUrl() }}" target="_blank" class="d-inline-block">
                                            <img src="{{ $image->getUrl() }}" class="img-responsive" style="width: 50px" alt="File">
                                        </a>
                                    @endforeach
                                @endif
                                <br />

                                <button class="btn btn-secondary btn-xs" onclick="sendImage({{ $issue->id }} )">Send Attachment</button>
                                <button class="btn btn-secondary btn-xs" onclick="sendUploadImage({{$issue->id}} )">Send Images</button>
                                <input id="file-input{{ $issue->id }}" type="file" name="files" style="display: none;" multiple />

                                <br />
                                <div>
                                    <div class="panel-group">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" href="#collapse_{{$issue->id}}">Messages({{count($issue->messages)}})</a>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>
                            <td >{{ \Carbon\Carbon::parse($issue->created_at)->format('H:i d-m') }} </td>
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
                            <td>{{ $issue->submitter ? $issue->submitter->name : 'N/A' }}

                            </td>

                            <td>
                                <select class="form-control assign-user" data-id="{{$issue->id}}" name="user" id="user_{{$issue->id}}">
                                    <option value="">Select...</option>
                                    @foreach($users as $id=>$name)
                                        @if( isset($issue->responsibleUser->id) && (int) $issue->responsibleUser->id == $id )
                                            <option value="{{$id}}" selected>{{ $name }}</option>
                                        @else
                                            <option value="{{$id}}">{{ $name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control set-responsible-user" data-id="{{$issue->id}}" name="user" id="user_{{$issue->id}}">
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
                                    {{--<select name="resolved" id="resolved_{{$issue->id}}" style="display: none;" class="form-control resolve-issue" data-id="{{$issue->id}}">--}}
                                        {{--<option {{ $issue->is_resolved==0 ? 'selected' : '' }} value="0">Not Resolved</option>--}}
                                        {{--<option {{ $issue->is_resolved==1 ? 'selected' : '' }} value="1">Resolved</option>--}}
                                    {{--</select>--}}

                                    <select name="task_status" id="{{$issue->id}}" class="form-control resolve-issue" onchange="resolveIssue(this,'<?php echo $issue->id; ?>')">
                                        <option value="">Please Select</option>
                                        <option value="Planned" {{ (!empty($issue->status) && $issue->status ==  'Planned' ? 'selected' : '') }}>Planned</option>
                                        <option value="In Progress" {{ (!empty($issue->status) && $issue->status  ==  'In Progress' ? 'selected' : '') }}>In Progress</option>
                                        <option value="Done" {{ (!empty($issue->status) && $issue->status ==   'Done' ? 'selected' : '') }}>Done</option>
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
                                            @foreach($issue->messages as $message)
                                                <li>{{ date('d-m-Y H:i:s', strtotime($message->created_at)) }} : {{ $message->message }}</li>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="panel-footer">
                                        <textarea class="form-control send-message-textbox" data-id="{{$issue->id}}" id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}"></textarea>
                                        <button type="submit" id="submit_message" class="btn btn-secondary ml-3 send-message" data-id="{{$issue->id}}" style="float: right;margin-top: 2%;">Submit</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @else
                        @if($issue->created_by == Auth::user()->id || $issue->user_id == Auth::user()->id || $issue->responsible_user_id == Auth::user()->id)
                            <tr>
                                <td><a href="{{ url("development/task-detail/$issue->id") }}">{{ $issue->developerModule ? $issue->developerModule->name : 'Not Specified' }}</a>
                                </td>

                                <td>
                                    {{ $issue->task }}
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
                                                        <a data-toggle="collapse" href="#collapse_{{$issue->id}}">Messages({{count($issue->messages)}})</a>
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $issue->subject }}</td>
                                <td>{!! ['N/A', '<strong class="text-danger">Critical</strong>', 'Urgent', 'Normal'][$issue->priority] ?? 'N/A' !!}</td>
                                <td>{{ $issue->task }}</td>
                                <td>{{ \Carbon\Carbon::parse($issue->created_at)->format('H:i d-m') }}</td>
                                <td>{{ $issue->submitter ? $issue->submitter->name : 'N/A' }}</td>
                                <td>
                                    @if($issue->responsibleUser)
                                        {{ $issue->responsibleUser->name  }}
                                    @else
                                        N/A
                                    @endif

                                </td>
                                <td>
                                    @if($issue->assignedUser)
                                        {{ $issue->assignedUser->name }}
                                    @else
                                        Unassigned
                                    @endif

                                </td>

                                <td>
                                    @if($issue->is_resolved)
                                        <strong>Resolved</strong>
                                    @else
                                        <select name="task_status" id="task_status" class="form-control change-task-status" data-id="{{$issue->id}}">
                                            <option value="">Please Select</option>
                                            <option value="Planned" {{ (!empty($issue->status) && $issue->status ==  'Planned' ? 'selected' : '') }}>Planned</option>
                                            <option value="In Progress" {{ (!empty($issue->status) && $issue->status  ==  'In Progress' ? 'selected' : '') }}>In Progress</option>
                                            <option value="Done" {{ (!empty($issue->status) && $issue->status ==   'Done' ? 'selected' : '') }}>Done</option>
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
                                                @foreach($issue->messages as $message)
                                                    <li>{{ date('d-m-Y H:i:s', strtotime($message->created_at)) }} : {{ $message->message }}</li>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <textarea class="form-control send-message-textbox" data-id="{{$issue->id}}" id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}"></textarea>
                                            <button type="submit" id="submit_message" class="btn btn-secondary ml-3 send-message" data-id="{{$issue->id}}" style="float: right;margin-top: 2%;">Submit</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endif
                @endforeach
            </table>
            <?php echo $issues->appends(request()->except("page"))->links(); ?>
        </div>
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

    <div id="priority_model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Priority</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="" id="priorityForm" method="POST">
                    @csrf

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-2">
                                    <strong>User:</strong>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        @if(auth()->user()->isAdmin())
                                            <select class="form-control" name="user_id" id="priority_user_id">
                                                @foreach ($users as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            {{auth()->user()->name}}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-2">
                                    <strong>Remarks:</strong>
                                </div>
                                <div class="col-md-8">
                                    @if(auth()->user()->isAdmin())
                                         <div class="form-group">
                                            <textarea cols="45" class="form-control" name="global_remarkes"></textarea>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th width="1%">ID</th>
                                        <th width="5%">Module</th>
                                        <th width="15%">Subject</th>
                                        <th width="67%">Issue</th>
                                        <th width="5%">Submitted By</th>
                                        <th width="2%">Action</th>
                                    </tr>
                                    <tbody class="show_issue_priority">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        @if(auth()->user()->isAdmin())
                            <button type="submit" class="btn btn-secondary">Confirm</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
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
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.infinite-scroll').jscroll({
                debug: true,
                autoTrigger: true,
                loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
                padding: 0,
                nextSelector: '.pagination li.active + li a',
                contentSelector: '.infinite-scroll',
                callback: function () {
                    $('ul.pagination:visible:first').remove();
                    var next_page = $('.pagination li.active');
                    if(next_page.length > 0) {
                        var current_page = next_page.find("span").html();
                        $('#page-goto option[data-value="' + current_page + '"]').attr('selected', 'selected');
                    }
                }
            });    

            $('.select2').select2({
                tags: true
            });

            $('#priority_user_id').select2({
                tags: true,
                width : '100%'
            });

            $('.estimate-time').datetimepicker({
                format: 'Y-MM-DD HH:mm'
            });
        });

        function getPriorityTaskList(id) {
            var selected_issue = [0];

            $('input[name ="selected_issue[]"]').each(function(){
                if ($(this).prop("checked") == true) {
                    selected_issue.push($(this).val());
                }
            });

            $.ajax({
                url: "{{route('development.issue.list.by.user.id')}}",
                type: 'POST',
                data: {
                    user_id : id,
                    _token : "{{csrf_token()}}",
                    selected_issue : selected_issue,
                },
                success: function (response) {
                    var html = '';
                    response.forEach(function (issue) {
                        html += '<tr>';
                            html += '<td><input type="hidden" name="priority[]" value="'+issue.id+'">'+issue.id+'</td>';
                            html += '<td>'+issue.module+'</td>';
                            html += '<td>'+issue.subject+'</td>';
                            html += '<td>'+issue.task+'</td>';
                            html += '<td>'+issue.submitted_by+'</td>';
                            html += '<td><a href="javascript:;" class="delete_priority" data-id="'+issue.id+'">Remove<a></td>';
                         html += '</tr>';
                    });
                    $( ".show_issue_priority" ).html(html);
                    <?php if (auth()->user()->isAdmin()) { ?>
                      $( ".show_issue_priority" ).sortable();
                    <?php } ?>
                },
                error: function () {
                    alert('There was error loading priority task list data');
                }
            });
        }
        $(document).on('click', '.delete_priority', function (e) {
            var id = $(this).data('id');
            $('input[value ="'+id+'"]').prop('checked', false);
            $(this).closest('tr').remove();
        });
        $('.priority_model_btn').click(function(){
            $( "#priority_user_id" ).val('');
            $( ".show_task_priority" ).html('');
            <?php if (auth()->user()->isAdmin()) { ?>
              getPriorityTaskList($('#priority_user_id').val());
            <?php } else { ?>
              getPriorityTaskList('{{auth()->user()->id}}');
            <?php } ?>
            $('#priority_model').modal('show');
        })

        $('#priority_user_id').change(function(){
                getPriorityTaskList($(this).val())
        });

        $(document).on('submit', '#priorityForm', function (e) {
            e.preventDefault();
            <?php if (auth()->user()->isAdmin()) { ?>
                $.ajax({
                    url: "{{route('development.issue.set.priority')}}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        toastr['success']('Priority successfully update!!', 'success');
                    },
                    error: function () {
                        alert('There was error loading priority task list data');
                    }
                });
            <?php } ?>
        });
    </script>
    <script>
        $(document).on('click', '.send-message', function (event) {
            /*if (event.which != 13) {
                return;
            }*/

            var textBox = $(this).closest(".panel-footer").find(".send-message-textbox");

            let issueId = textBox.attr('data-id');
            let message = textBox.val();

            if (message == '') {
                return;
            }

            let self = textBox;

            $.ajax({
                url: "{{action('WhatsAppController@sendMessage', 'issue')}}",
                type: 'POST',
                data: {
                    issue_id: issueId,
                    message: message,
                    _token: "{{csrf_token()}}",
                    status: 2
                },
                dataType:"json",
                success: function (response) {
                    toastr["success"]("Message sent successfully!", "Message");
                    $('#message_list_' + issueId).append('<li>'+response.message.created_at+ " : " + response.message.message + '</li>');
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
        $(document).on('change', '.assign-user', function () {
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
        {{--$(document).on('change', '.resolve-issue', function (event) {--}}
            {{--let id = $(this).data('id');--}}
            {{--let status = $(this).val();--}}
            {{--let self = this;--}}
{{--alert(id);--}}
            {{--$.ajax({--}}
                {{--url: "{{action('DevelopmentController@resolveIssue')}}",--}}
                {{--data: {--}}
                    {{--issue_id: id,--}}
                    {{--is_resolved: status--}}
                {{--},--}}
                {{--success: function () {--}}
                    {{--toastr["success"]("Status updated!", "Message")--}}
                {{--}--}}
            {{--});--}}
        {{--});--}}

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

        $(document).on('change', '.change-task-status', function () {
           var taskId       = $(this).data("id");
           var status      = $(this).val();
            $.ajax({
                url: "{{ action('DevelopmentController@changeTaskStatus') }}",
                type: 'POST',
                data: {
                    task_id: taskId,
                    _token: "{{csrf_token()}}",
                    status: status
                },
                success: function () {
                    toastr['success']('Status Changed successfully!')
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

        function sendUploadImage(id){

            $('#file-input'+id).trigger('click');

            $('#file-input'+id).change(function () {
            event.preventDefault();
            let image_upload = new FormData();
            let TotalImages = $(this)[0].files.length;  //Total Images
            let images = $(this)[0];

            for (let i = 0; i < TotalImages; i++) {
                image_upload.append('images[]', images.files[i]);
            }
             image_upload.append('TotalImages', TotalImages);
             image_upload.append('status',2);
             image_upload.append('type',2);
             image_upload.append('issue_id',id);
             if(TotalImages != 0){

                    $.ajax({
                        method: 'POST',
                        url: "{{action('WhatsAppController@sendMessage', 'issue')}}",
                        data: image_upload,
                        async : true,
                        contentType: false,
                        processData: false,
                        beforeSend: function() {
                        $("#loading-image").show();
                        },
                        success: function (images) {
                            $("#loading-image").hide();
                            alert('Images send successfully');
                        },
                        error: function () {
                          console.log(`Failed`)
                        }
                    })
                }
            })
        }

        //Popup for add new task
        $(document).on('click', '#newTaskModalBtn', function () {
            if ($("#newTaskModal").length > 0) {
                $("#newTaskModal").remove();
            }

            $.ajax({
                url: "{{ action('DevelopmentController@openNewTaskPopup') }}",
                type: 'GET',
                dataType: "JSON",
                success: function (resp) {
                    console.log(resp);
                    if(resp.status == 'ok') {
                        $("body").append(resp.html);
                        $('#newTaskModal').modal('show');
                        $('.select2').select2({tags: true});
                    }
                }
            });
        });

        function resolveIssue(obj,task_id){

            let id = task_id;
            let status = $(obj).val();
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
        }


    </script>
@endsection
