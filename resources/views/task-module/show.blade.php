@extends('layouts.app')

@section('title', 'Tasks - ERP Sololuxury')

@section('styles')
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">

  {{-- <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" /> --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

  <style>
    #message-wrapper {
      height: 450px;
      overflow-y: scroll;
    }
  </style>
@endsection

@section('content')

    <div class="row">
      <div class="col-lg-12 text-center">
        <h2 class="page-heading">Task & Activity</h2>
      </div>
    </div>

    @include('task-module.partials.modal-contact')
    @include('task-module.partials.modal-task-category')
    @include('task-module.partials.modal-task-view')

    @include('partials.flash_messages')

    <div class="row mb-4">
      <div class="col-12">
        <form class="form-inline" action="{{ route('task.index') }}" method="GET">
          <input type="hidden" name="daily_activity_date" value="{{ $data['daily_activity_date'] }}">

          <div class="form-group">
            <input type="text" name="term" placeholder="Search Term" class="form-control input-sm" value="{{ isset($term) ? $term : "" }}">
          </div>

          <div class="form-group ml-3">
            <?php
            $categories = \App\Http\Controllers\TaskCategoryController::getAllTaskCategory();

            echo Form::select('category', $categories, (old('category') ? old('category') : $category), ['placeholder' => 'Select a Category','class' => 'form-control input-sm']);

            ?>
          </div>

          @can('view-activity')
            <div class="form-group ml-3">
              <select class="form-control input-sm" name="selected_user">
                <option value="">Select a User</option>
                @foreach ($users as $id => $user)
                  <option value="{{ $id }}" {{ $id == $selected_user ? 'selected' : '' }}>{{ $user }}</option>
                @endforeach
              </select>
            </div>
          @endcan

          <button type="submit" class="btn btn-image ml-3"><img src="/images/filter.png" /></button>
        </form>
      </div>

      {{-- <div class="col-md-7 col-12">
          <div class="panel panel-default">
              <div class="panel-heading"><h4>Export Task</h4></div>
              <div class="panel-body">
                  <form action="{{ route('task.export') }}" method="POST" enctype="multipart/form-data">
                      @csrf
                      <div class="row">
                          <div class="col-md-3">
                              <div class="form-group">
                                  <strong>User</strong>
                                  {!! Form::select( 'selected_user', $users, '' , [
                                      'class'       => 'form-control',
                                      'multiple' => 'multiple',
                                      'id' => 'userList',
                                      'name' => 'selected_user[]',
                                  ] ); !!}
                              </div>
                          </div>
                          <div class="col-md-7">
                              <div class="form-group">
                                  <strong>Date Range</strong>
                                  <input type="text" value="" name="range_start" hidden/>
                                  <input type="text" value="" name="range_end" hidden/>
                                  <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                      <i class="fa fa-calendar"></i>&nbsp;
                                      <span></span> <i class="fa fa-caret-down"></i>
                                  </div>
                              </div>
                          </div>
                          <div class="col-md-2 mt-4">
                              <button type="submit" class="btn btn-secondary">Submit</button>
                          </div>
                      </div>
                  </form>
              </div>
          </div>
      </div> --}}
    </div>

        <?php
        if ( \App\Helpers::getadminorsupervisor() && ! empty( $selected_user ) )
            $isAdmin = true;
        else
            $isAdmin = false;
        ?>
            <div class="row mb-4">
              <div class="col-xs-12">
                <form action="{{ route('task.store') }}" method="POST" enctype="multipart/form-data">
                  @csrf

                  <div class="row">
                    <div class="col-xs-12 col-md-4">
                      <div class="form-group">
                        <input type="text" class="form-control input-sm" name="task_subject" placeholder="Task Subject" id="task_subject" value="{{ old('task_subject') }}" required />
                        @if ($errors->has('task_subject'))
                          <div class="alert alert-danger">{{$errors->first('task_subject')}}</div>
                        @endif
                      </div>

                      <div class="form-group">
                        <textarea rows="1" class="form-control input-sm" name="task_details" placeholder="Task Details" id="task_details" required>{{ old('task_details') }}</textarea>
                        @if ($errors->has('task_details'))
                          <div class="alert alert-danger">{{$errors->first('task_details')}}</div>
                        @endif
                      </div>
                    </div>

                    <div class="col-xs-12 col-md-4">
                      <div class="form-group">
                        <select name="is_statutory" class="form-control is_statutory input-sm">
                          <option value="0">Other Task</option>
                          <option value="1">Statutory Task</option>
                          <option value="2">Calendar Task</option>
                          <option value="3">Appointment Task</option>
                        </select>
                      </div>

                      <div id="recurring-task" style="display: none;">
                        <div class="form-group">
                          {{-- <strong>Recurring Type:</strong> --}}
                          <select name="recurring_type" class="form-control input-sm">
                              <option value="EveryDay">EveryDay</option>
                              <option value="EveryWeek">EveryWeek</option>
                              <option value="EveryMonth">EveryMonth</option>
                              <option value="EveryYear">EveryYear</option>
                          </select>
                        </div>
                      </div>

                      <div id="calendar-task" style="display: none;">
                        <div class="form-group">
                          <div class='input-group date' id='completion-datetime'>
                            <input type='text' class="form-control input-sm" name="completion_date" value="{{ date('Y-m-d H:i') }}" />

                            <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                          </div>

                          @if ($errors->has('completion_date'))
                            <div class="alert alert-danger">{{$errors->first('completion_date')}}</div>
                          @endif
                        </div>
                      </div>

                      <div class="form-group">
                        <select id="multi_users" class="form-control input-sm" name="assign_to[]" multiple>
                          @foreach ($data['users'] as $user)
                            <option value="{{ $user['id'] }}">{{ $user['name'] }} - {{ $user['email'] }}</option>
                          @endforeach
                        </select>

                        {{-- <select class="selectpicker form-control input-sm" data-live-search="true" data-size="15" name="assign_to[]" id="first_customer" title="Choose a User" multiple>
                          @foreach ($data['users'] as $user)
                            <option data-tokens="{{ $user['name'] }} {{ $user['email'] }}" value="{{ $user['id'] }}">{{ $user['name'] }} - {{ $user['email'] }}</option>
                          @endforeach
                        </select> --}}

                        @if ($errors->has('assign_to'))
                          <div class="alert alert-danger">{{$errors->first('assign_to')}}</div>
                        @endif
                      </div>
                    </div>

                    <div class="col-xs-12 col-md-4">
                      <div class="form-inline mb-3">
                        <div class="form-group flex-fill">
                          <select id="multi_contacts" class="form-control input-sm" name="assign_to_contacts[]" multiple>
                            @foreach (Auth::user()->contacts as $contact)
                              <option value="{{ $contact['id'] }}">{{ $contact['name'] }} - {{ $contact['phone'] }} ({{ $contact['category'] }})</option>
                            @endforeach
                          </select>

                          {{-- <select class="selectpicker form-control input-sm" data-live-search="true" data-size="15" name="assign_to_contacts[]" title="Choose a Contact" multiple>
                            @foreach (Auth::user()->contacts as $contact)
                              <option data-tokens="{{ $contact['name'] }} {{ $contact['phone'] }} {{ $contact['category'] }}" value="{{ $contact['id'] }}">{{ $contact['name'] }} - {{ $contact['phone'] }} ({{ $contact['category'] }})</option>
                            @endforeach
                          </select> --}}

                          @if ($errors->has('assign_to_contacts'))
                            <div class="alert alert-danger">{{$errors->first('assign_to_contacts')}}</div>
                          @endif
                        </div>

                        <button type="button" class="btn btn-image" data-toggle="modal" data-target="#createQuickContactModal"><img src="/images/add.png" /></button>
                      </div>



                      <div class="form-inline mb-3">
                        <div class="form-group flex-fill">
                            {{-- <strong>Category:</strong> --}}
                            <?php
                            $categories = \App\Http\Controllers\TaskCategoryController::getAllTaskCategory();

                            echo Form::select('category',$categories, ( old('category') ? old('category') : $category ), ['placeholder' => 'Select a Category','class' => 'form-control input-sm']);

                            ?>
                        </div>

                        <button type="button" class="btn btn-image" data-toggle="modal" data-target="#createTaskCategorytModal"><img src="/images/add.png" /></button>
                      </div>
                    </div>

                    <div class="col-xs-4" style="display: none;" id="appointment-container">
                      <div class="form-group">
                        <input type="text" class="form-control input-sm" name="note[]" placeholder="Note" value="">
                      </div>

                      <div id="note-container">

                      </div>

                      <button type="button" class="btn btn-xs btn-secondary" id="addNoteButton">Add Note</button>
                    </div>

                    <div class="col-xs-12 text-center">
                      <button type="submit" class="btn btn-xs btn-secondary">Create</button>
                    </div>
                  </div>

                </form>

              </div>
                {{-- <div class="col-sm-5 col-12">

                    <div class="panel panel-default">
                        <div class="panel-heading"><h4>Assign Task</h4></div>
                        <div class="panel-body">

                        </div>
                    </div>

                </div> --}}
                {{-- <div class="col-sm-7 col-12">
                    <div class="panel panel-default">
                        <div class="panel-heading"><h4>Daily Activity</h4></div>
                        <div class="panel-body">
                            <div class="mt-2 mb-2 text-right">
                              <form action="/task" method="GET" class="form-inline">
                                @if (!empty($selected_user))
                                  <input type="hidden" name="selected_user" value="{{ $selected_user }}">
                                @endif
                                <div class='input-group date' id='daily_activity_date'>
                                  <input type='text' class="form-control" name="daily_activity_date" value="{{ $data['daily_activity_date'] }}" />

                                  <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                                </div>
                                <button type="submit" class="btn btn-secondary ml-1">Submit</button>
                                @if(!$isAdmin)
                                  <button id="add-row" type="button" class="btn btn-secondary ml-5">Add Row</button>
                                @endif
                                <button id="save-activity" type="button" class="btn btn-secondary">Save</button>
                                <img id="loading_activty" style="display: none" src="{{ asset('images/loading.gif') }}"/>
                              </form>
                            </div>

                            <div id="daily_activity"></div>
                        </div>
                    </div>
                </div> --}}
            </div>
            <!-- <div class="row">
                <div class="col-12">
                    <h4>Today's Statutory Activity List</h4>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Date</th>
                            <th class="category">Category</th>
                            <th>Task Details</th>
                            <th>Assigned From</th>
                            <th>Assigned To</th>
                            <th>Remark</th>
                            <th>Completed</th>
                            <th style="width: 80px;">Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div> -->

            @include('task-module.partials.modal-reminder')

            <div id="exTab2" class="container" style="overflow: auto">
               <ul class="nav nav-tabs">
                  <li class="active">
                     <a  href="#1" data-toggle="tab">Pending Task</a>
                  </li>
                  <li><a href="#2" data-toggle="tab">Statutory Activity</a>
                  </li>
                  <li><a href="#3" data-toggle="tab">Completed Task</a>
                  </li>
                  <li><a href="#unassigned-tab" data-toggle="tab">Unassigned Messages</a></li>
                  <li><button type="button" class="btn btn-xs btn-secondary my-3" id="view_tasks_button" data-selected="0">View Tasks</button></li>
               </ul>
               <div class="tab-content ">
                    <!-- Pending task div start -->
                    <div class="tab-pane active" id="1">
                        <div class="row">
                           <!-- <h4>List Of Pending Tasks</h4> -->
                            <div class="infinite-scroll">
                              <table class="table table-sm table-bordered">
                                  <thead>
                                    <tr>
                                      <th width="5%">ID</th>
                                      <th width="10%">Date</th>
                                      <th width="10%" class="category">Category</th>
                                      <th width="15%">Task Subject</th>
                                      {{-- <th width="5%">Est Completion Date</th> --}}
                                      <th width="5%" colspan="2">From / To</th>
                                      {{-- <th width="5%">Assigned To</th> --}}
                                      <th width="25%">Communication</th>
                                      <th width="20%">Send Message</th>
                                      {{-- <th>Remarks</th> --}}
                                      <th width="10%">Action</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    @foreach($data['task']['pending'] as $task)
                                  <tr class="{{ \App\Http\Controllers\TaskModuleController::getClasses($task) }} {{ $task->is_statutory == 3 ? 'row-highlight' : '' }}" id="task_{{ $task->id }}">
                                      <td class="p-2">
                                        {{ $task->id }}

                                        <input type="checkbox" class="select_task_checkbox" name="task" data-id="{{ $task->id }}" value="">
                                      </td>
                                      <td class="p-2">{{ Carbon\Carbon::parse($task->created_at)->format('d-m H:i') }}</td>
                                      <td class="expand-row table-hover-cell p-2">
                                        @if (isset($categories[$task->category]))
                                          <span class="td-mini-container">
                                            {{ strlen($categories[$task->category]) > 10 ? substr($categories[$task->category], 0, 10) : $categories[$task->category] }}
                                          </span>

                                          <span class="td-full-container hidden">
                                            {{ $categories[$task->category] }}
                                          </span>
                                        @endif
                                      </td>
                                      <td class="expand-row table-hover-cell p-2" data-subject="{{$task->task_subject ? $task->task_subject : 'Task Details'}}" data-details="{{$task->task_details}}" data-switch="0" style="word-break: break-all;">

                                        <span class="td-mini-container">
                                          {{ $task->task_subject ? substr($task->task_subject, 0, 18) . (strlen($task->task_subject) > 15 ? '...' : '') : 'Task Details' }}
                                        </span>

                                        <span class="td-full-container hidden">
                                          <strong>{{ $task->task_subject ? $task->task_subject : 'Task Details' }}</strong>

                                          {{ $task->task_details }}
                                        </span>
                                      </td>
                                      {{-- <td> {{ Carbon\Carbon::parse($task->completion_date)->format('d-m H:i')  }}</td> --}}
                                      <td class="expand-row table-hover-cell p-2">
                                        @if (array_key_exists($task->assign_from, $users))
                                          @if ($task->assign_from == Auth::id())
                                            <span class="td-mini-container">
                                              <a href="{{ route('users.show', $task->assign_from) }}">{{ strlen($users[$task->assign_from]) > 4 ? substr($users[$task->assign_from], 0, 4) : $users[$task->assign_from] }}</a>
                                            </span>

                                            <span class="td-full-container hidden">
                                              <a href="{{ route('users.show', $task->assign_from) }}">{{ $users[$task->assign_from] }}</a>
                                            </span>
                                          @else
                                            <span class="td-mini-container">
                                              {{ strlen($users[$task->assign_from]) > 4 ? substr($users[$task->assign_from], 0, 4) : $users[$task->assign_from] }}
                                            </span>

                                            <span class="td-full-container hidden">
                                              {{ $users[$task->assign_from] }}
                                            </span>
                                          @endif
                                        @else
                                          Doesn't Exist
                                        @endif
                                      </td>
                                      <td class="expand-row table-hover-cell p-2">
                                        @php
                                          $special_task = \App\Task::find($task->id);
                                          $users_list = '';

                                          foreach ($special_task->users as $key => $user) {
                                            if ($key != 0) {
                                              $users_list .= ', ';
                                            }

                                            if (array_key_exists($user->id, $users)) {
                                              $users_list .= $users[$user->id];
                                            } else {
                                              $users_list = 'User Does Not Exist';
                                            }
                                          }

                                          $users_list .= ' ';

                                          foreach ($special_task->contacts as $key => $contact) {
                                            if ($key != 0) {
                                              $users_list .= ', ';
                                            }

                                            $users_list .= "$contact->name - $contact->phone" . ucwords($contact->category);
                                          }
                                        @endphp

                                        <span class="td-mini-container">
                                          {{ strlen($users_list) > 6 ? substr($users_list, 0, 6) : $users_list }}
                                        </span>

                                        <span class="td-full-container hidden">
                                          {{ $users_list }}
                                        </span>
                                      </td>

                                      <td class="expand-row table-hover-cell p-2 {{ ($task->message && $task->message_status == 0) || $task->message_is_reminder == 1 || ($task->message_user_id == $task->assign_from && $task->assign_from != Auth::id()) ? 'text-danger' : '' }}">
                                        {{-- ($task->message && $task->message_status == 0 && $task->message_user_id != Auth::id()) --}}
                                        @if ($task->assign_to == Auth::id() || ($task->assign_to != Auth::id() && $task->is_private == 0))
                                          @if (isset($task->message))
                                            <div class="d-flex justify-content-between">
                                              <span class="td-mini-container">
                                                {{ strlen($task->message) > 32 ? substr($task->message, 0, 29) . '...' : $task->message }}
                                              </span>

                                              <span class="td-full-container hidden">
                                                {{ $task->message }}
                                              </span>

                                              @if ($task->message_status != 0)
                                                <a href='#' class='btn btn-image p-0 resend-message' data-id="{{ $task->message_id }}"><img src="/images/resend.png" /></a>
                                              @endif
                                            </div>
                                          @endif
                                        @else
                                          Private
                                        @endif
                                      </td>
                                      <td class="p-2">
                                        @if ($task->assign_to == Auth::id() || ($task->assign_to != Auth::id() && $task->is_private == 0))
                                          <div class="d-flex">
                                            <input type="text" class="form-control quick-message-field input-sm" name="message" placeholder="Message" value="">
                                            <button class="btn btn-sm btn-image send-message" data-taskid="{{ $task->id }}"><img src="/images/filled-sent.png" /></button>
                                          </div>
                                        @else
                                          Private
                                        @endif
                                      </td>

                                      <td class="p-2">
                                        <div class="d-flex">
                                          @if ($special_task->users->contains(Auth::id()) || $task->assign_from == Auth::id())
                                            @if ($task->is_completed == '')
                                              <button type="button" class="btn btn-image task-complete" data-id="{{ $task->id }}"><img src="/images/incomplete.png" /></button>
                                            @else
                                              @if ($task->assign_from == Auth::id())
                                                <button type="button" class="btn btn-image task-complete" data-id="{{ $task->id }}"><img src="/images/completed-green.png" /></button>
                                              @else
                                                <button type="button" class="btn btn-image"><img src="/images/completed-green.png" /></button>
                                              @endif
                                            @endif

                                            <button type="button" class='btn btn-image ml-1 reminder-message' data-id="{{ $task->message_id }}" data-toggle='modal' data-target='#reminderMessageModal'><img src='/images/reminder.png' /></button>

                                            @if ($task->is_statutory != 3)
                                              <button type="button" class='btn btn-image ml-1 convert-task-appointment' data-id="{{ $task->id }}"><img src='/images/details.png' /></button>
                                            @endif
                                          @endif

                                          @if ((!$special_task->users->contains(Auth::id()) && $special_task->contacts()->count() == 0))
                                            @if ($task->is_private == 1)
                                              <button disabled type="button" class="btn btn-image"><img src="/images/private.png" /></button>
                                            @else
                                              {{-- <a href="{{ route('task.show', $task->id) }}" class="btn btn-image" href=""><img src="/images/view.png" /></a> --}}
                                            @endif
                                          @endif

                                          @if ($special_task->users->contains(Auth::id()) || ($task->assign_from == Auth::id() && $task->is_private == 0) || ($task->assign_from == Auth::id() && $special_task->contacts()->count() > 0))
                                            <a href="{{ route('task.show', $task->id) }}" class="btn btn-image" href=""><img src="/images/view.png" /></a>
                                          @endif

                                          @if ($special_task->users->contains(Auth::id()) || (!$special_task->users->contains(Auth::id()) && $task->assign_from == Auth::id() && $special_task->contacts()->count() > 0))
                                            @if ($task->is_private == 1)
                                              <button type="button" class="btn btn-image make-private-task" data-taskid="{{ $task->id }}"><img src="/images/private.png" /></button>
                                            @else
                                              <button type="button" class="btn btn-image make-private-task" data-taskid="{{ $task->id }}"><img src="/images/not-private.png" /></button>
                                            @endif
                                          @endif

                                          @if ($task->is_flagged == 1)
                                            <button type="button" class="btn btn-image flag-task" data-id="{{ $task->id }}"><img src="/images/flagged.png" /></button>
                                          @else
                                            <button type="button" class="btn btn-image flag-task" data-id="{{ $task->id }}"><img src="/images/unflagged.png" /></button>
                                          @endif
                                        </div>



                                          {{-- <a href id="add-new-remark-btn" class="add-task" data-toggle="modal" data-target="#add-new-remark_{{$task->id}}" data-id="{{$task->id}}">Add</a>
                                          <span> | </span>
                                          <a href id="view-remark-list-btn" class="view-remark  {{ $task->remark ? 'text-danger' : '' }}" data-toggle="modal" data-target="#view-remark-list" data-id="{{$task->id}}">View</a> --}}
                                      </td>
                                  </tr>
                                 @endforeach
                                  </tbody>
                                </table>

                                {{-- {!! $data['task']['pending']->appends(Request::except('page'))->links() !!} --}}
                            </div>
                        </div>
                    </div>
                    <!-- Pending task div end -->
                    <!-- Statutory task div start -->
                    <div class="tab-pane" id="2">
                        <div class="row">
                            <div class="col-12">
                                <!-- <h4>Statutory Activity Completed</h4> -->
                                <table class="table table-sm table-bordered">
                                <thead>
                                  <tr>
                                    <th width="5%">ID</th>
                                    <th width="10%">Date</th>
                                    <th width="10%" class="category">Category</th>
                                    <th width="15%">Task Details</th>
                                    <th width="5%" colspan="2">From / To</th>
                                    <th width="5%">Reccuring</th>
                                    {{-- <th width="5%">Assigned To</th> --}}
                                    {{-- <th width="5%">Remark</th> --}}
                                    <th width="20%">Communication</th>
                                    <th width="20%">Send Message</th>
                                    {{-- <th width="5%">Completed at</th> --}}
                                    <th width="10%">Actions</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach(  $data['task']['statutory_not_completed'] as $task)
                                <tr id="task_{{ $task->id }}">
                                    <td class="p-2">{{ $task->id }}</td>
                                    <td class="p-2">{{ Carbon\Carbon::parse($task->created_at)->format('d-m H:i') }}</td>
                                    <td class="expand-row table-hover-cell p-2">
                                      @if (isset($categories[$task->category]))
                                        <span class="td-mini-container">
                                          {{ strlen($categories[$task->category]) > 10 ? substr($categories[$task->category], 0, 10) : $categories[$task->category] }}
                                        </span>

                                        <span class="td-full-container hidden">
                                          {{ $categories[$task->category] }}
                                        </span>
                                      @endif
                                    </td>
                                    <td class="expand-row table-hover-cell p-2" data-subject="{{$task->task_subject ? $task->task_subject : 'Task Details'}}" data-details="{{$task->task_details}}" data-switch="0" style="word-break: break-all;">
                                      <span class="td-mini-container">
                                        {{ $task->task_subject ? substr($task->task_subject, 0, 18) . (strlen($task->task_subject) > 15 ? '...' : '') : 'Task Details' }}
                                      </span>

                                      <span class="td-full-container hidden">
                                        <strong>{{ $task->task_subject ? $task->task_subject : 'Task Details' }}</strong>

                                        {{ $task->task_details }}
                                      </span>
                                    </td>
                                    <td class="expand-row table-hover-cell p-2">
                                      @if (array_key_exists($task->assign_from, $users))
                                        @if ($task->assign_from == Auth::id())
                                          <span class="td-mini-container">
                                            <a href="{{ route('users.show', $task->assign_from) }}">{{ strlen($users[$task->assign_from]) > 4 ? substr($users[$task->assign_from], 0, 4) : $users[$task->assign_from] }}</a>
                                          </span>

                                          <span class="td-full-container hidden">
                                            <a href="{{ route('users.show', $task->assign_from) }}">{{ $users[$task->assign_from] }}</a>
                                          </span>
                                        @else
                                          <span class="td-mini-container">
                                            {{ strlen($users[$task->assign_from]) > 4 ? substr($users[$task->assign_from], 0, 4) : $users[$task->assign_from] }}
                                          </span>

                                          <span class="td-full-container hidden">
                                            {{ $users[$task->assign_from] }}
                                          </span>
                                        @endif
                                      @else
                                        Doesn't Exist
                                      @endif
                                    </td>
                                    <td class="expand-row table-hover-cell p-2">
                                      @php
                                        $special_task = \App\Task::find($task->id);
                                        $users_list = '';

                                        foreach ($special_task->users as $key => $user) {
                                          if ($key != 0) {
                                            $users_list .= ', ';
                                          }

                                          if (array_key_exists($user->id, $users)) {
                                            $users_list .= $users[$user->id];
                                          } else {
                                            $users_list = 'User Does Not Exist';
                                          }
                                        }

                                        $users_list .= ' ';

                                        foreach ($special_task->contacts as $key => $contact) {
                                          if ($key != 0) {
                                            $users_list .= ', ';
                                          }

                                          $users_list .= "$contact->name - $contact->phone" . ucwords($contact->category);
                                        }
                                      @endphp

                                      <span class="td-mini-container">
                                        {{ strlen($users_list) > 6 ? substr($users_list, 0, 6) : $users_list }}
                                      </span>

                                      <span class="td-full-container hidden">
                                        {{ $users_list }}
                                      </span>
                                    </td>
                                    <td class="p-2">
                                      {{ strlen($task->recurring_type) > 6 ? substr($task->recurring_type, 0, 6) : $task->recurring_type }}
                                    </td>

                                    <td class="expand-row table-hover-cell p-2 {{ $task->message && $task->message_status == 0 ? 'text-danger' : '' }}">
                                      @if ($task->assign_to == Auth::id() || ($task->assign_to != Auth::id() && $task->is_private == 0))
                                        @if (isset($task->message))
                                          <div class="d-flex justify-content-between">
                                            <span class="td-mini-container">
                                              {{ strlen($task->message) > 32 ? substr($task->message, 0, 29) . '...' : $task->message }}
                                            </span>

                                            <span class="td-full-container hidden">
                                              {{ $task->message }}
                                            </span>

                                            @if ($task->message_status != 0)
                                              <a href='#' class='btn btn-image p-0 resend-message' data-id="{{ $task->message_id }}"><img src="/images/resend.png" /></a>
                                            @endif
                                          </div>
                                        @endif
                                      @else
                                        Private
                                      @endif
                                    </td>
                                    <td class="p-2">
                                      @if ($task->assign_to == Auth::id() || ($task->assign_to != Auth::id() && $task->is_private == 0))
                                        <div class="d-flex">
                                          <input type="text" class="form-control quick-message-field input-sm" name="message" placeholder="Message" value="">
                                          <button class="btn btn-sm btn-image send-message" data-taskid="{{ $task->id }}"><img src="/images/filled-sent.png" /></button>
                                        </div>
                                      @else
                                        Private
                                      @endif
                                    </td>
                                    {{-- <td>{{ Carbon\Carbon::parse($task->completion_date)->format('d-m H:i') }}</td> --}}
                                    <td class="p-2">
                                      <div class="d-flex">
                                        @if ($special_task->users->contains(Auth::id()) || $task->assign_from == Auth::id())
                                          <a href="/task/complete/{{ $task->id }}" class="btn btn-image task-complete" data-id="{{ $task->id }}"><img src="/images/incomplete.png" /></a>
                                        @endif

                                        @if ((!$special_task->users->contains(Auth::id()) && $special_task->contacts()->count() == 0))
                                          @if ($task->is_private == 1)
                                            <button disabled type="button" class="btn btn-image"><img src="/images/private.png" /></button>
                                          @else
                                            {{-- <a href="{{ route('task.show', $task->id) }}" class="btn btn-image" href=""><img src="/images/view.png" /></a> --}}
                                          @endif
                                        @endif

                                        @if ($special_task->users->contains(Auth::id()) || ($task->assign_from == Auth::id() && $task->is_private == 0) || ($task->assign_from == Auth::id() && $special_task->contacts()->count() > 0))
                                          <a href="{{ route('task.show', $task->id) }}" class="btn btn-image" href=""><img src="/images/view.png" /></a>
                                        @endif

                                        @if ($special_task->users->contains(Auth::id()) || (!$special_task->users->contains(Auth::id()) && $task->assign_from == Auth::id() && $special_task->contacts()->count() > 0))


                                          @if ($task->is_private == 1)
                                            <button type="button" class="btn btn-image make-private-task" data-taskid="{{ $task->id }}"><img src="/images/private.png" /></button>
                                          @else
                                            <button type="button" class="btn btn-image make-private-task" data-taskid="{{ $task->id }}"><img src="/images/not-private.png" /></button>
                                          @endif
                                        @endif
                                      </div>
                                    </td>
                                </tr>
                               @endforeach
                                </tbody>
                              </table>
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-12">
                                <h4>All Statutory Activity List</h4>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Date</th>
                                            <th class="category">Category</th>
                                            <th>Task Details</th>
                                            <th>Assigned From</th>
                                            <th>Assigned To</th>
                                            <th>Recurring Type</th>
                                            <th>Remarks</th>
                                            <th>Completed</th>
                                        </tr>
                                    </thead>
                                <tbody>
                                    @foreach(  $data['task']['statutory'] as $task)
                                            <tr>
                                                <td>{{ $task['id'] }}</td>
                                                <td> {{ Carbon\Carbon::parse($task['created_at'])->format('d-m H:i') }}</td>
                                                <td> {{ isset( $categories[$task['category']] ) ? $categories[$task['category']] : '' }}</td>
                                                <td class="task-subject" data-subject="{{$task['task_subject'] ? $task['task_subject'] : 'Task Details'}}" data-details="{{$task['task_details']}}" data-switch="0">{{ $task['task_subject'] ? $task['task_subject'] : 'Task Details' }}</td>
                                                <td>{{ $users[$task['assign_from']]}}</td>
                                                <td>
                                                  {{ $task['assign_to'] ?? ($users[$task['assign_to']] ? $users[$task['assign_to']] : 'Nil') }}
                                                </td>
                                                <td>{{ $task['recurring_type'] }}</td>
                                                <td> @include('task-module.partials.remark',$task) </td>
                                                <td>
                                                  @if( Auth::id() == $task['assign_to'] )
                                                    @if ($task['completion_date'])
                                                      {{ Carbon\Carbon::parse($task['completion_date'])->format('d-m H:i') }}
                                                    @else
                                                      <a href="/statutory-task/complete/{{$task['id']}}">Complete</a>
                                                    @endif
                                                  @endif
                                                </td>
                                            </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div> --}}
                    </div>
                    <!-- Statutory task div end -->
                    <!-- Completed task div start -->
                    <div class="tab-pane" id="3">
                        <div class="row">
                           <!-- <h4>List Of Completed Tasks</h4> -->
                            <table class="table table-sm table-bordered">
                                <thead>
                                  <tr>
                                  <th width="5%">ID</th>
                                  <th width="10%">Date</th>
                                  <th width="10%" class="category">Category</th>
                                  <th width="15%">Task Details</th>
                                  {{-- <th width="5%">Est Completion Date</th> --}}
                                  <th width="10%" colspan="2">From / To</th>
                                  {{-- <th width="5%">Assigned To</th> --}}
                                  <th width="10%">Completed On</th>
                                  <th width="30%">Communication</th>
                                  <th width="10%">Action</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach( $data['task']['completed'] as $task)
                                <tr class="{{ \App\Http\Controllers\TaskModuleController::getClasses($task) }} completed" id="task_{{ $task->id }}">
                                    <td class="p-2">{{ $task->id }}</td>
                                    <td class="p-2">{{ Carbon\Carbon::parse($task->created_at)->format('d-m H:i') }}</td>
                                    <td class="expand-row table-hover-cell p-2">
                                      @if (isset($categories[$task->category]))
                                        <span class="td-mini-container">
                                          {{ strlen($categories[$task->category]) > 10 ? substr($categories[$task->category], 0, 10) : $categories[$task->category] }}
                                        </span>

                                        <span class="td-full-container hidden">
                                          {{ $categories[$task->category] }}
                                        </span>
                                      @endif
                                    </td>
                                    <td class="expand-row table-hover-cell p-2" data-subject="{{$task->task_subject ? $task->task_subject : 'Task Details'}}" data-details="{{$task->task_details}}" data-switch="0" style="word-break: break-all;">
                                      <span class="td-mini-container">
                                        {{ $task->task_subject ? substr($task->task_subject, 0, 18) . (strlen($task->task_subject) > 15 ? '...' : '') : 'Task Details' }}
                                      </span>

                                      <span class="td-full-container hidden">
                                        <strong>{{ $task->task_subject ? $task->task_subject : 'Task Details' }}</strong>

                                        {{ $task->task_details }}
                                      </span>
                                    </td>
                                    {{-- <td> {{ Carbon\Carbon::parse($task['completion_date'])->format('d-m H:i') }}</td> --}}
                                    <td class="expand-row table-hover-cell p-2">
                                      @if (array_key_exists($task->assign_from, $users))
                                        @if ($task->assign_from == Auth::id())
                                          <span class="td-mini-container">
                                            <a href="{{ route('users.show', $task->assign_from) }}">{{ strlen($users[$task->assign_from]) > 4 ? substr($users[$task->assign_from], 0, 4) : $users[$task->assign_from] }}</a>
                                          </span>

                                          <span class="td-full-container hidden">
                                            <a href="{{ route('users.show', $task->assign_from) }}">{{ $users[$task->assign_from] }}</a>
                                          </span>
                                        @else
                                          <span class="td-mini-container">
                                            {{ strlen($users[$task->assign_from]) > 4 ? substr($users[$task->assign_from], 0, 4) : $users[$task->assign_from] }}
                                          </span>

                                          <span class="td-full-container hidden">
                                            {{ $users[$task->assign_from] }}
                                          </span>
                                        @endif
                                      @else
                                        Doesn't Exist
                                      @endif
                                    </td>

                                    <td class="expand-row table-hover-cell p-2">
                                      @php
                                        $special_task = \App\Task::find($task->id);
                                        $users_list = '';

                                        foreach ($special_task->users as $key => $user) {
                                          if ($key != 0) {
                                            $users_list .= ', ';
                                          }

                                          if (array_key_exists($user->id, $users)) {
                                            $users_list .= $users[$user->id];
                                          } else {
                                            $users_list = 'User Does Not Exist';
                                          }
                                        }

                                        $users_list .= ' ';

                                        foreach ($special_task->contacts as $key => $contact) {
                                          if ($key != 0) {
                                            $users_list .= ', ';
                                          }

                                          $users_list .= "$contact->name - $contact->phone" . ucwords($contact->category);
                                        }
                                      @endphp

                                      <span class="td-mini-container">
                                        {{ strlen($users_list) > 6 ? substr($users_list, 0, 6) : $users_list }}
                                      </span>

                                      <span class="td-full-container hidden">
                                        {{ $users_list }}
                                      </span>
                                    </td>

                                    <td>{{ Carbon\Carbon::parse($task->is_completed)->format('d-m H:i') }}</td>
                                    <td class="expand-row table-hover-cell p-2 {{ $task->message && $task->message_status == 0 ? 'text-danger' : '' }}">
                                      @if ($task->assign_to == Auth::id() || ($task->assign_to != Auth::id() && $task->is_private == 0))
                                        @if (isset($task->message))
                                          <div class="d-flex justify-content-between">
                                            <span class="td-mini-container">
                                              {{ strlen($task->message) > 32 ? substr($task->message, 0, 29) . '...' : $task->message }}
                                            </span>

                                            <span class="td-full-container hidden">
                                              {{ $task->message }}
                                            </span>

                                            @if ($task->message_status != 0)
                                              <a href='#' class='btn btn-image p-0 resend-message' data-id="{{ $task->message_id }}"><img src="/images/resend.png" /></a>
                                            @endif
                                          </div>
                                        @endif
                                      @else
                                        Private
                                      @endif
                                    </td>
                                    <td class="p-2">
                                      <div class="d-flex">
                                        @if ((!$special_task->users->contains(Auth::id()) && $special_task->contacts()->count() == 0))
                                          @if ($task->is_private == 1)
                                            <button disabled type="button" class="btn btn-image"><img src="/images/private.png" /></button>
                                          @else
                                            {{-- <a href="{{ route('task.show', $task->id) }}" class="btn btn-image" href=""><img src="/images/view.png" /></a> --}}
                                          @endif
                                        @endif

                                        @if ($special_task->users->contains(Auth::id()) || ($task->assign_from == Auth::id() && $task->is_private == 0) || ($task->assign_from == Auth::id() && $special_task->contacts()->count() > 0))
                                          <a href="{{ route('task.show', $task->id) }}" class="btn btn-image" href=""><img src="/images/view.png" /></a>
                                        @endif

                                        @if ($special_task->users->contains(Auth::id()) || (!$special_task->users->contains(Auth::id()) && $task->assign_from == Auth::id() && $special_task->contacts()->count() > 0))


                                          @if ($task->is_private == 1)
                                            <button type="button" class="btn btn-image make-private-task" data-taskid="{{ $task->id }}"><img src="/images/private.png" /></button>
                                          @else
                                            <button type="button" class="btn btn-image make-private-task" data-taskid="{{ $task->id }}"><img src="/images/not-private.png" /></button>
                                          @endif
                                        @endif

                                        <form action="{{ route('task.archive', $task->id) }}" method="POST">
                                          @csrf

                                          <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
                                        </form>
                                      </div>
                                    </td>
                                </tr>
                               @endforeach
                                </tbody>
                              </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="unassigned-tab">
                      <div class="row">
                        <div class="col-xs-12 col-md-4 my-3">
                          <div class="border">
                            <form action="{{ route('task.assign.messages') }}" method="POST">
                              @csrf

                              <input type="hidden" name="selected_messages" id="selected_messages" value="">

                              <div class="form-group">
                                <select class="selectpicker form-control input-sm" data-live-search="true" data-size="15" name="task_id" title="Choose a Task" required>
                                  @foreach ($data['task']['pending'] as $task)
                                    <option data-tokens="{{ $task->id }} {{ $task->task_subject }} {{ $task->task_details }} {{ array_key_exists($task->assign_from, $users) ? $users[$task->assign_from] : '' }} {{ array_key_exists($task->assign_to, $users) ? $users[$task->assign_to] : '' }}" value="{{ $task->id }}">{{ $task->id }} from {{ $users[$task->assign_from] }} {{ $task->task_subject }}</option>
                                  @endforeach
                                </select>
                              </div>

                              <div class="form-group">
                                <button type="submit" class="btn btn-xs btn-secondary" id="assignMessagesButton">Assign</button>
                              </div>
                            </form>

                          </div>
                        </div>

                        <div class="col-xs-12 col-md-8">
                          <div class="border">

                            <div class="row">
                              <div class="col-12 my-3" id="message-wrapper">
                                <div id="message-container"></div>
                              </div>

                              <div class="col-xs-12 text-center">
                                <button type="button" id="load-more-messages" data-nextpage="1" class="btn btn-xs btn-secondary">Load More</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- Completed task div end -->
                </div>
            </div>
        </div>



@endsection

@section('scripts')
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script> --}}
  <script>
  var taskSuggestions = {!! json_encode($search_suggestions, true) !!};
  var cached_suggestions = localStorage['message_suggestions'];
  var suggestions = [];

  $(document).ready(function() {
    $('#task_subject, #task_details').autocomplete({
      source: function(request, response) {
        var results = $.ui.autocomplete.filter(taskSuggestions, request.term);

        response(results.slice(0, 10));
      }
    });
    var hash = window.location.hash.substr(1);

    if (hash == '3') {
      $('a[href="#3"]').click();
    }

    $('.selectpicker').selectpicker({
      selectOnTab: true
    });

    $('#multi_users').select2({
      placeholder: 'Select a User',
    });

    $('#multi_contacts').select2({
      placeholder: 'Select a Contact',
    });

    // $('ul.pagination').hide();
    // $(function() {
    //     $('.infinite-scroll').jscroll({
    //         autoTrigger: true,
    //         loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
    //         padding: 2500,
    //         nextSelector: '.pagination li.active + li a',
    //         contentSelector: 'div.infinite-scroll',
    //         callback: function() {
    //             // $('ul.pagination').remove();
    //         }
    //     });
    // });

    // $('div.dropdown-menu.open li').on('keydown', function (e) {
    //   alert('yes');
    //   if (e.keyCode == 13) { // Enter
    //     alert('a');
    //     var previousEle = $(this).prev();
    //     if (previousEle.length == 0) {
    //       previousEle = $(this).nextAll().last();
    //     }
    //     var selVal = $('.selectpicker option').filter(function () {
    //       return $(this).text() == previousEle.text();
    //     }).val();
    //     $('.selectpicker').selectpicker('val', selVal);
    //
    //     return;
    //   }
    //   // if (e.keyCode == 40) { // Down
    //   //   var nextEle = $(this).next();
    //   //   if (nextEle.length == 0) {
    //   //     nextEle = $(this).prevAll().last();
    //   //   }
    //   //   var selVal = $('.selectpicker option').filter(function () {
    //   //     return $(this).text() == nextEle.text();
    //   //   }).val();
    //   //   $('.selectpicker').selectpicker('val', selVal);
    //   //
    //   //   return;
    //   // }
    //
    // });
  });

    $(document).on('click', '.expand-row', function() {
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

      function addNewRemark(id){

        var formData = $("#add-new-remark").find('#add-remark').serialize();
        // console.log(id);
        var remark = $('#remark-text_'+id).val();
        $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: '{{ route('task.addRemark') }}',
            data: {id:id,remark:remark,module_type: "task"},
        }).done(response => {
            alert('Remark Added Success!')
            // $('#add-new-remark').modal('hide');
            // $("#add-new-remark").hide();
            window.location.reload();
        });
      }

      $('#completion-datetime, #reminder-datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
      });

      $('#daily_activity_date').datetimepicker({
        format: 'YYYY-MM-DD'
      });

      let users = {!! json_encode( $data['users'] ) !!};

      let isAdmin = {{ $isAdmin ? 1 : 0}};

      // let table = new Tabulator("#daily_activity", {
      //     height: "311px",
      //     layout: "fitColumns",
      //     resizableRows: true,
      //     columns: [
      //         {
      //             title: "Time",
      //             field: "time_slot",
      //             editor: "select",
      //             editorParams: {
      //                 '12:00am - 01:00am': '12:00am - 01:00am',
      //                 '01:00am - 02:00am': '01:00am - 02:00am',
      //                 '02:00am - 03:00am': '02:00am - 03:00am',
      //                 '03:00am - 04:00am': '03:00am - 04:00am',
      //                 '04:00am - 05:00am': '04:00am - 05:00am',
      //                 '05:00am - 06:00am': '05:00am - 06:00am',
      //                 '06:00am - 07:00am': '06:00am - 07:00am',
      //                 '07:00am - 08:00am': '07:00am - 08:00am',
      //
      //                 '08:00am - 09:00am': '08:00am - 09:00am',
      //                 '09:00am - 10:00am': '09:00am - 10:00am',
      //                 '10:00am - 11:00am': '10:00am - 11:00am',
      //                 '11:00am - 12:00pm': '11:00am - 12:00pm',
      //                 '12:00pm - 01:00pm': '12:00pm - 01:00pm',
      //                 '01:00pm - 02:00pm': '01:00pm - 02:00pm',
      //                 '02:00pm - 03:00pm': '02:00pm - 03:00pm',
      //                 '03:00pm - 04:00pm': '03:00pm - 04:00pm',
      //                 '04:00pm - 05:00pm': '04:00pm - 05:00pm',
      //                 '05:00pm - 06:00pm': '05:00pm - 06:00pm',
      //                 '06:00pm - 07:00pm': '06:00pm - 07:00pm',
      //                 '07:00pm - 08:00pm': '07:00pm - 08:00pm',
      //
      //                 '08:00pm - 09:00pm': '08:00pm - 09:00pm',
      //                 '09:00pm - 10:00pm': '09:00pm - 10:00pm',
      //                 '10:00pm - 11:00pm': '10:00pm - 11:00pm',
      //                 '11:00pm - 12:00am': '11:00pm - 12:00am',
      //             },
      //             editable: !isAdmin
      //         },
      //         {title: "Activity", field: "activity", editor: "textarea", formatter:"textarea", editable: !isAdmin},
      //         {title: "Assessment", field: "assist_msg", editor: "input", editable: !!isAdmin, visible: !!isAdmin},
      //         {title: "id", field: "id", visible: false},
      //         {title: "user_id", field: "user_id", visible: false},
      //     ],
      // });

      $("#add-row").click(function () {
          table.addRow({});
      });

      $(".add-task").click(function () {
          var taskId = $(this).attr('data-id');
          $("#add-new-remark").find('input[name="id"]').val(taskId);
      });

      $(".view-remark").click(function () {

        var taskId = $(this).attr('data-id');

          $.ajax({
              type: 'GET',
              headers: {
                  'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              url: '{{ route('task.gettaskremark') }}',
              data: {id:taskId,module_type:"task"},
          }).done(response => {
              console.log(response);

              var html='';

              $.each(response, function( index, value ) {

                html+=' <p> '+value.remark+' <br> <small>By ' + value.user_name + ' updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
                html+"<hr>";
              });
              $("#view-remark-list").find('#remark-list').html(html);
              // getActivity();
              //
              // $('#loading_activty').hide();
          });
      });

      // $("#save-activity").click(function () {
      //
      //     $('#loading_activty').show();
      //     console.log(table.getData());
      //
      //     let data = [];
      //
      //     if (isAdmin) {
      //         data = deleteKeyFromObjectArray(table.getData(), ['time_slot', 'activity']);
      //     }
      //     else {
      //         data = deleteKeyFromObjectArray(table.getData(), ['assist_msg']);
      //     }
      //
      //     $.ajax({
      //         type: 'POST',
      //         headers: {
      //             'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
      //         },
      //         url: '{{ route('dailyActivity.store') }}',
      //         data: {
      //             activity_table_data: encodeURI(JSON.stringify(data)),
      //         },
      //     }).done(response => {
      //         console.log(response);
      //         getActivity();
      //
      //         $('#loading_activty').hide();
      //     });
      // });

      // function deleteKeyFromObjectArray(data, key) {
      //
      //     let newData = [];
      //
      //     for (let item of data) {
      //
      //         for (let eachKey of key)
      //             delete  item[eachKey];
      //
      //         newData = [...newData, item];
      //     }
      //
      //     return newData;
      // }

      // function getActivity() {
      //     $.ajax({
      //         type: 'GET',
      //         data :{
      //             selected_user : '{{ $selected_user }}',
      //             daily_activity_date: "{{ $data['daily_activity_date'] }}",
      //         },
      //         url: '{{ route('dailyActivity.get') }}',
      //     }).done(response => {
      //         table.setData(response);
      //         setTimeout(getActivity, interval_daily_activtiy);
      //     });
      // }
      //
      // getActivity();
      // let interval_daily_activtiy = 1000*600;  // 1000 = 1 second
      // setTimeout(getActivity, interval_daily_activtiy);


      $(document).ready(function() {
          $(document).on('change', '.is_statutory', function () {


              if ($(".is_statutory").val() == 1) {

                  // $('input[name="completion_date"]').val("1976-01-01");
                  // $("#completion-datetime").hide();
                  $("#calendar-task").hide();
                  $('#appointment-container').hide();

                  if (!isAdmin)
                      $('select[name="assign_to"]').html(`<option value="${current_userid}">${ current_username }</option>`);

                  $('#recurring-task').show();
              } else if ($(".is_statutory").val() == 2) {
                $("#calendar-task").show();
                $('#recurring-task').hide();
                $('#appointment-container').hide();
              } else if ($(".is_statutory").val() == 3) {
                $("#calendar-task").hide();
                $('#recurring-task').hide();
                $('#appointment-container').show();
              } else {

                // $("#completion-datetime").show();
                  $("#calendar-task").hide();
                  $('#appointment-container').hide();

                  let select_html = '';
                  for (user of users)
                      select_html += `<option value="${user['id']}">${ user['name'] }</option>`;
                  $('select[name="assign_to"]').html(select_html);

                  $('#recurring-task').hide();

              }

          });

          jQuery('#userList').select2(

              {
                  placeholder : 'All user'
              }
          );

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

          $(".table").tablesorter();
      });

      $(document).on('click', '.send-message', function() {
        var thiss = $(this);
        var data = new FormData();
        var task_id = $(this).data('taskid');
        var message = $(this).siblings('input').val();

        data.append("task_id", task_id);
        data.append("message", message);
        data.append("status", 1);

        if (message.length > 0) {
          if (!$(thiss).is(':disabled')) {
            $.ajax({
              url: '/whatsapp/sendMessage/task',
              type: 'POST',
             "dataType"    : 'json',           // what to expect back from the PHP script, if anything
             "cache"       : false,
             "contentType" : false,
             "processData" : false,
             "data": data,
             beforeSend: function() {
               $(thiss).attr('disabled', true);
             }
           }).done( function(response) {
              $(thiss).siblings('input').val('');

              if (cached_suggestions) {
                suggestions = JSON.parse(cached_suggestions);

                if (suggestions.length == 10) {
                  suggestions.push(message);
                  suggestions.splice(0, 1);
                } else {
                  suggestions.push(message);
                }
                localStorage['message_suggestions'] = JSON.stringify(suggestions);
                cached_suggestions = localStorage['message_suggestions'];

                console.log('EXISTING');
                console.log(suggestions);
              } else {
                suggestions.push(message);
                localStorage['message_suggestions'] = JSON.stringify(suggestions);
                cached_suggestions = localStorage['message_suggestions'];

                console.log('NOT');
                console.log(suggestions);
              }

              // $.post( "/whatsapp/approve/customer", { messageId: response.message.id })
              //   .done(function( data ) {
              //
              //   }).fail(function(response) {
              //     console.log(response);
              //     alert(response.responseJSON.message);
              //   });

              $(thiss).attr('disabled', false);
            }).fail(function(errObj) {
              $(thiss).attr('disabled', false);

              alert("Could not send message");
              console.log(errObj);
            });
          }
        } else {
          alert('Please enter a message first');
        }
      });

      $(document).on('click', '.make-private-task', function() {
        var task_id = $(this).data('taskid');
        var thiss = $(this);

        $.ajax({
          type: "POST",
          url: "{{ url('task') }}/" + task_id + "/makePrivate",
          data: {
            _token: "{{ csrf_token() }}",
          },
          beforeSend: function() {
            $(thiss).text('Changing...');
          }
        }).done(function(response) {
          if (response.task.is_private == 1) {
            $(thiss).html('<img src="/images/private.png" />');
          } else {
            $(thiss).html('<img src="/images/not-private.png" />');
          }
        }).fail(function(response) {
          $(thiss).html('<img src="/images/not-private.png" />');

          console.log(response);

          alert('Could not make task private');
        });
      });

      $(document).on('click', ".collapsible-message", function() {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
          var short_message = $(this).data('messageshort');
          var message = $(this).data('message');
          var status = $(this).data('expanded');

          if (status == false) {
            $(this).addClass('expanded');
            $(this).html(message);
            $(this).data('expanded', true);
            // $(this).siblings('.thumbnail-wrapper').remove();
            $(this).closest('.talktext').find('.message-img').removeClass('thumbnail-200');
            $(this).closest('.talktext').find('.message-img').parent().css('width', 'auto');
          } else {
            $(this).removeClass('expanded');
            $(this).html(short_message);
            $(this).data('expanded', false);
            $(this).closest('.talktext').find('.message-img').addClass('thumbnail-200');
            $(this).closest('.talktext').find('.message-img').parent().css('width', '200px');
          }
        }
      });

          $(document).ready(function() {
          var container = $("div#message-container");
          var suggestion_container = $("div#suggestion-container");
          // var sendBtn = $("#waMessageSend");
          var erpUser = "{{ Auth::id() }}";
               var addElapse = false;
               function errorHandler(error) {
                   console.error("error occured: " , error);
               }
               function approveMessage(element, message) {
                 if (!$(element).attr('disabled')) {
                   $.ajax({
                     type: "POST",
                     url: "/whatsapp/approve/user",
                     data: {
                       _token: "{{ csrf_token() }}",
                       messageId: message.id
                     },
                     beforeSend: function() {
                       $(element).attr('disabled', true);
                       $(element).text('Approving...');
                     }
                   }).done(function( data ) {
                     element.remove();
                     console.log(data);
                   }).fail(function(response) {
                     $(element).attr('disabled', false);
                     $(element).text('Approve');

                     console.log(response);
                     alert(response.responseJSON.message);
                   });
                 }
               }

          function renderMessage(message, tobottom = null) {
              var domId = "waMessage_" + message.id;
              var current = $("#" + domId);
              var is_admin = "{{ Auth::user()->hasRole('Admin') }}";
              var is_hod_crm = "{{ Auth::user()->hasRole('HOD of CRM') }}";
              var users_array = {!! json_encode($users) !!};
              var leads_assigned_user = "";

              if ( current.get( 0 ) ) {
                return false;
              }

             // CHAT MESSAGES
             var row = $("<div class='talk-bubble'></div>");
             var body = $("<span id='message_body_" + message.id + "'></span>");
             var text = $("<div class='talktext'></div>");
             var edit_field = $('<textarea name="message_body" rows="8" class="form-control" id="edit-message-textarea' + message.id + '" style="display: none;">' + message.message + '</textarea>');
             var p = $("<p class='collapsible-message'></p>");

             var forward = $('<button class="btn btn-image forward-btn" data-toggle="modal" data-target="#forwardModal" data-id="' + message.id + '"><img src="/images/forward.png" /></button>');

             if (message.status == 0 || message.status == 5 || message.status == 6) {
               var meta = $("<em>" + users_array[message.user_id] + " " + moment(message.created_at).format('DD-MM H:mm') + " </em>");
               var mark_read = $("<a href data-url='/whatsapp/updatestatus?status=5&id=" + message.id + "' style='font-size: 9px' class='change_message_status'>Mark as Read </a><span> | </span>");
               var mark_replied = $('<a href data-url="/whatsapp/updatestatus?status=6&id=' + message.id + '" style="font-size: 9px" class="change_message_status">Mark as Replied </a>');

               // row.attr("id", domId);
               p.appendTo(text);

               // $(images).appendTo(text);
               meta.appendTo(text);

               if (message.status == 0) {
                 mark_read.appendTo(meta);
               }

               if (message.status == 0 || message.status == 5) {
                 mark_replied.appendTo(meta);
               }

               text.appendTo(row);

               if (tobottom) {
                 row.appendTo(container);
               } else {
                 row.prependTo(container);
               }

               forward.appendTo(meta);

             } else if (message.status == 4) {
               var row = $("<div class='talk-bubble' data-messageid='" + message.id + "'></div>");
               var chat_friend =  (message.assigned_to != 0 && message.assigned_to != leads_assigned_user && message.user_id != message.assigned_to) ? ' - ' + users_array[message.assigned_to] : '';
               var meta = $("<em>" + users_array[message.user_id] + " " + chat_friend + " " + moment(message.created_at).format('DD-MM H:mm') + " <img id='status_img_" + message.id + "' src='/images/1.png' /> &nbsp;</em>");

               // row.attr("id", domId);

               p.appendTo(text);
               $(images).appendTo(text);
               meta.appendTo(text);

               text.appendTo(row);
               if (tobottom) {
                 row.appendTo(container);
               } else {
                 row.prependTo(container);
               }
             } else {
               if (message.sent == 0) {
                 var meta_content = "<em>" + (parseInt(message.user_id) !== 0 ? users_array[message.user_id] : "Unknown") + " " + moment(message.created_at).format('DD-MM H:mm') + " </em>";
               } else {
                 var meta_content = "<em>" + (parseInt(message.user_id) !== 0 ? users_array[message.user_id] : "Unknown") + " " + moment(message.created_at).format('DD-MM H:mm') + " <img id='status_img_" + message.id + "' src='/images/1.png' /></em>";
               }

               var error_flag = '';
               if (message.error_status == 1) {
                 error_flag = "<a href='#' class='btn btn-image fix-message-error' data-id='" + message.id + "'><img src='/images/flagged.png' /></a><a href='#' class='btn btn-xs btn-secondary ml-1 resend-message' data-id='" + message.id + "'>Resend</a>";
               } else if (message.error_status == 2) {
                 error_flag = "<a href='#' class='btn btn-image fix-message-error' data-id='" + message.id + "'><img src='/images/flagged.png' /><img src='/images/flagged.png' /></a><a href='#' class='btn btn-xs btn-secondary ml-1 resend-message' data-id='" + message.id + "'>Resend</a>";
               }



               var meta = $(meta_content);

               edit_field.appendTo(text);

               if (!message.approved) {
                   var approveBtn = $("<button class='btn btn-xs btn-secondary btn-approve ml-3'>Approve</button>");
                   var editBtn = ' <a href="#" style="font-size: 9px" class="edit-message whatsapp-message ml-2" data-messageid="' + message.id + '">Edit</a>';
                   approveBtn.click(function() {
                       approveMessage( this, message );
                   } );
                   if (is_admin || is_hod_crm) {
                     approveBtn.appendTo( meta );
                     $(editBtn).appendTo( meta );
                   }
               }

               forward.appendTo(meta);

               $(error_flag).appendTo(meta);
             }

             row.attr("id", domId);

             p.attr("data-messageshort", message.message);
             p.attr("data-message", message.message);
             p.attr("data-expanded", "true");
             p.attr("data-messageid", message.id);
             // console.log("renderMessage message is ", message);
             if (message.message) {
               p.html(message.message);
             } else if (message.media_url) {
                 var splitted = message.content_type.split("/");
                 if (splitted[0]==="image" || splitted[0] === 'm') {
                     var a = $("<a></a>");
                     a.attr("target", "_blank");
                     a.attr("href", message.media_url);
                     var img = $("<img></img>");
                     img.attr("src", message.media_url);
                     img.attr("width", "100");
                     img.attr("height", "100");
                     img.appendTo( a );
                     a.appendTo( p );
                     // console.log("rendered image message ", a);
                 } else if (splitted[0]==="video") {
                     $("<a target='_blank' href='" + message.media_url+"'>"+ message.media_url + "</a>").appendTo(p);
                 }
             }

             var has_product_image = false;

             if (message.images) {
               var images = '';
               message.images.forEach(function (image) {
                 images += image.product_id !== '' ? '<a href="/products/' + image.product_id + '" data-toggle="tooltip" data-html="true" data-placement="top" title="<strong>Special Price: </strong>' + image.special_price + '<br><strong>Size: </strong>' + image.size + '<br><strong>Supplier: </strong>' + image.supplier_initials + '">' : '';
                 images += '<div class="thumbnail-wrapper"><img src="' + image.image + '" class="message-img thumbnail-200" /><span class="thumbnail-delete whatsapp-image" data-image="' + image.key + '">x</span></div>';
                 images += image.product_id !== '' ? '<input type="checkbox" name="product" style="width: 20px; height: 20px;" class="d-block mx-auto select-product-image" data-id="' + image.product_id + '" /></a>' : '';

                 if (image.product_id !== '') {
                   has_product_image = true;
                 }
               });

               images += '<br>';

               if (has_product_image) {
                 var show_images_wrapper = $('<div class="show-images-wrapper hidden"></div>');
                 var show_images_button = $('<button type="button" class="btn btn-xs btn-secondary show-images-button">Show Images</button>');

                 $(images).appendTo(show_images_wrapper);
                 $(show_images_wrapper).appendTo(text);
                 $(show_images_button).appendTo(text);
               } else {
                 $(images).appendTo(text);
               }

             }

             p.appendTo(body);
             body.appendTo(text);
             meta.appendTo(text);

             var select_box = $('<input type="checkbox" name="selected_message" class="select-message" data-id="' + message.id + '" />');

             select_box.appendTo(meta);

             if (has_product_image) {
               var create_lead = $('<a href="#" class="btn btn-xs btn-secondary ml-1 create-product-lead">+ Lead</a>');
               var create_order = $('<a href="#" class="btn btn-xs btn-secondary ml-1 create-product-order">+ Order</a>');

               create_lead.appendTo(meta);
               create_order.appendTo(meta);
             }

             text.appendTo( row );

             if (message.status == 7) {
               if (tobottom) {
                 row.appendTo(suggestion_container);
               } else {
                 row.prependTo(suggestion_container);
               }
             } else {
               if (tobottom) {
                 row.appendTo(container);
               } else {
                 row.prependTo(container);
               }
             }


             return true;
          }

          function pollMessages(page = null, tobottom = null, addElapse = null) {
                   var qs = "";
                   qs += "?erpUser=" + erpUser;
                   if (page) {
                     qs += "&page=" + page;
                   }
                   if (addElapse) {
                       qs += "&elapse=3600";
                   }
                   var anyNewMessages = false;

                   return new Promise(function(resolve, reject) {
                       $.getJSON("/whatsapp/pollMessagesCustomer" + qs, function( data ) {

                           data.data.forEach(function( message ) {
                               var rendered = renderMessage( message, tobottom );
                               if ( !anyNewMessages && rendered ) {
                                   anyNewMessages = true;
                               }
                           } );

                           if (page) {
                             $('#load-more-messages').text('Load More');
                             can_load_more = true;
                           }

                           if ( anyNewMessages ) {
                               // scrollChatTop();
                               anyNewMessages = false;
                           }
                           if (!addElapse) {
                               addElapse = true; // load less messages now
                           }


                           resolve();
                       });

                   });
          }

          function startPolling() {
            setTimeout( function() {
                       pollMessages(null, null, addElapse).then(function() {
                           startPolling();
                       }, errorHandler);
                   }, 1000);
          }

          $('a[href="#unassigned-tab"]').on('click', function() {
            startPolling();
          });

          var can_load_more = true;

          $('#message-wrapper').scroll(function() {
            var top = $('#message-wrapper').scrollTop();
            var document_height = $(document).height();
            var window_height = $('#message-container').height();

            console.log($('#message-wrapper').scrollTop());
            console.log($(document).height());
            console.log($('#message-container').height());

            // if (top >= (document_height - window_height - 200)) {
            if (top >= (window_height - 1500)) {
              console.log('should load', can_load_more);
              if (can_load_more) {
                var current_page = $('#load-more-messages').data('nextpage');
                $('#load-more-messages').data('nextpage', current_page + 1);
                var next_page = $('#load-more-messages').data('nextpage');
                console.log(next_page);
                $('#load-more-messages').text('Loading...');

                can_load_more = false;

                pollMessages(next_page, true);
              }
            }
          });

          $(document).on('click', '#load-more-messages', function() {
            var current_page = $(this).data('nextpage');
            $(this).data('nextpage', current_page + 1);
            var next_page = $(this).data('nextpage');
            $('#load-more-messages').text('Loading...');

            pollMessages(next_page, true);
          });

        });

        var selected_messages = [];
        $(document).on('click', '.select-message', function() {
          var message_id = $(this).data('id');

          if ($(this).prop('checked')) {
            selected_messages.push(message_id);
          } else {
            var index = selected_messages.indexOf(message_id);

            selected_messages.splice(index, 1);
          }

          console.log(selected_messages);
        });

        $('#assignMessagesButton').on('click', function(e) {
          e.preventDefault();

          if (selected_messages.length > 0) {
            $('#selected_messages').val(JSON.stringify(selected_messages));

            if ($(this).closest('form')[0].checkValidity()) {
              $(this).closest('form').submit();
            } else {
              $(this).closest('form')[0].reportValidity();
            }
          } else {
            alert('Please select atleast 1 message');
          }
        });

        var timer = 0;
        var delay = 200;
        var prevent = false;

        $(document).on('click', '.task-complete', function(e) {
          e.preventDefault();
          e.stopPropagation();
          var thiss = $(this);

          timer = setTimeout(function () {
            if (!prevent) {
              var task_id = $(thiss).data('id');
              var image = $(thiss).html();
              var url = "/task/complete/" + task_id;
              var current_user = {{ Auth::id() }};

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
                  if (response.task.is_verified != null) {
                    $(thiss).html('<img src="/images/completed.png" />');
                  } else if (response.task.is_completed != null) {
                    $(thiss).html('<img src="/images/completed-green.png" />');
                  } else {
                    $(thiss).html('<img src="/images/incomplete.png" />');
                  }

                  if (response.task.assign_from != current_user) {
                    $(thiss).attr('disabled', true);
                  }
                }).fail(function(response) {
                  $(thiss).html(image);

                  alert('Could not mark as completed!');

                  console.log(response);
                });
              }
            }

            prevent = false;
          }, delay);
        });

        $(document).on('dblclick', '.task-complete', function(e) {
          e.preventDefault();
          e.stopPropagation();

          clearTimeout(timer);
          prevent = true;

          var thiss = $(this);
          var task_id = $(this).data('id');
          var image = $(this).html();
          var url = "/task/complete/" + task_id;

          $.ajax({
            type: "GET",
            url: url,
            data: {
              type: 'clear'
            },
            beforeSend: function () {
              $(thiss).text('Clearing...');
            }
          }).done(function(response) {
            if (response.task.is_verified != null) {
              $(thiss).html('<img src="/images/completed.png" />');
            } else if (response.task.is_completed != null) {
              $(thiss).html('<img src="/images/completed-green.png" />');
            } else {
              $(thiss).html('<img src="/images/incomplete.png" />');
            }
          }).fail(function(response) {
            $(thiss).html(image);

            alert('Could not clear the task!');

            console.log(response);
          });
        });

        $(document).on('click', '.resend-message', function() {
          var id = $(this).data('id');
          var thiss = $(this);

          $.ajax({
            type: "POST",
            url: "{{ url('whatsapp') }}/" + id + "/resendMessage",
            data: {
              _token: "{{ csrf_token() }}",
            },
            beforeSend: function() {
              $(thiss).text('Sending...');
            }
          }).done(function(response) {
            $(thiss).html('<img src="/images/resend.png" />');
          }).fail(function(response) {
            $(thiss).html('<img src="/images/resend.png" />');

            console.log(response);

            alert('Could not resend message');
          });
        });

        $(document).on('click', '#addNoteButton', function () {
          var note_html = `<div class="form-group d-flex">
            <input type="text" class="form-control input-sm" name="note[]" placeholder="Note" value="">
            <button type="button" class="btn btn-image remove-note">x</button>
          </div>`;

          $('#note-container').append(note_html);
        });

        $(document).on('click', '.remove-note', function () {
          $(this).closest('.form-group').remove();
        });
        $(document).on('click', '.reminder-message', function() {
          var id = $(this).data('id');

          $('#reminderMessageModal').find('input[name="message_id"]').val(id);
        });

        $(document).on('click', '.convert-task-appointment', function() {
          var thiss = $(this);
          var id = $(this).data('id');

          $.ajax({
            type: "POST",
            url: "{{ url('task') }}/" + id + "/convertTask",
            data: {
              _token: "{{ csrf_token() }}",
            },
            beforeSend: function() {
              $(thiss).text('Converting...');
            }
          }).done(function(response) {
            $(thiss).closest('tr').addClass('row-highlight');
            $(thiss).remove();
          }).fail(function(response) {
            $(thiss).html('<img src="/images/details.png" />');

            console.log(response);

            alert('Could not convert a task');
          });
        });

        $(document).on('click', '.flag-task', function() {
          var task_id = $(this).data('id');
          var thiss = $(this);

          $.ajax({
            type: "POST",
            url: "{{ route('task.flag') }}",
            data: {
              _token: "{{ csrf_token() }}",
              task_id: task_id
            },
            beforeSend: function() {
              $(thiss).text('Flagging...');
            }
          }).done(function(response) {
            if (response.is_flagged == 1) {
              // var badge = $('<span class="badge badge-secondary">Flagged</span>');
              //
              // $(thiss).parent().append(badge);
              $(thiss).html('<img src="/images/flagged.png" />');
            } else {
              $(thiss).html('<img src="/images/unflagged.png" />');
              // $(thiss).parent().find('.badge').remove();
            }

            // $(thiss).remove();
          }).fail(function(response) {
            $(thiss).html('<img src="/images/unflagged.png" />');

            alert('Could not flag task!');

            console.log(response);
          });
        });

        var selected_tasks = [];

        $(document).on('click', '.select_task_checkbox', function() {
          var checked = $(this).prop('checked');
          var id = $(this).data('id');

          if (checked) {
            selected_tasks.push(id);
          } else {
            var index = selected_tasks.indexOf(id);

            selected_tasks.splice(index, 1);
          }

          console.log(selected_tasks);
        });

        $('#view_tasks_button').on('click', function() {
          var selected = $(this).data('selected');

          // if (selected == 0) {
          //   $(this).text('View');
          //
          //   $('.select_task_checkbox').removeClass('hidden');
          //
          //   $(this).data('selected', 1);
          // } else if (selected == 1) {
            // $(this).text('Select for Viewing');

            // $('.select_task_checkbox').removeClass('hidden');

            $(this).data('selected', 0);
            console.log(JSON.stringify(selected_tasks));
            if (selected_tasks.length > 0) {
              $.ajax({
                type: "POST",
                url: "{{ url('task/loadView') }}",
                data: {
                  _token: "{{ csrf_token() }}",
                  selected_tasks: selected_tasks
                }
              }).done(function(response) {
                $('#task_view_body').html(response.view);

                $('#taskViewModal').modal();
              }).fail(function(response) {
                console.log(response);

                alert('Could not load tasks view');
              });
            } else {
              alert('Please select atleast 1 task!');
            }
          // }
        });
  </script>
@endsection
