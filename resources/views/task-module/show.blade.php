@extends('layouts.app')

@section('favicon' , 'task.png')

@section('title', 'Tasks')

@section('styles')

{{--    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />--}}

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">

    <link rel="stylesheet" href="{{asset('css/bootstrap-datetimepicker.min.css')}}">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">

    <style>
        .communication_th {
            width: 10% !important;
            min-width: 250px !important;
        }
        #message-wrapper {
            height: 450px;
            overflow-y: scroll;
        }
        .dis-none {
            display: none;
        }
        .pd-5 {
            padding: 3px;
        }
        .cls_task_detailstextarea {
            height: 30px !important;
        }
        .cls_remove_allpadding {
            padding-right: 0px !important;
            padding-left: 0px !important;
        }
        .cls_right_allpadding {
            padding-right: 0px !important;
        }
        .cls_left_allpadding {
            padding-left: 0px !important;
        }
        #addNoteButton {
            margin-top: 2px;
        }
        #saveNewNotes {
            margin-top: 2px;
        }
        .col-xs-12.col-md-2 {
            padding-left: 5px !important;
            padding-right: 5px !important;
            height: 38px;
        }
        .cls_task_subject {
            padding-left: 9px;
        }
        #recurring-task .col-xs-12.col-md-6 {
            padding-left: 5px !important;
            padding-right: 5px !important;
        }
        #appointment-container .col-xs-12.col-md-6 {
            padding-left: 5px !important;
            padding-right: 5px !important;
        }
        #taskCreateForm .form-group {
            margin-bottom: 0px;
        }
        .cls_action_box .btn-image img {
            width: 12px !important;
        }
        .cls_action_box .btn.btn-image {
            padding: 2px;
        }
        .btn.btn-image {
            padding: 5px 3px;
        }
        .td-mini-container {
            margin-top: 9px;
        }
        .td-full-container {
            margin-top: 9px;
        }
        .cls_textbox_notes {
            width: 100% !important;
        }
        .cls_multi_contact .btn-image img {
            width: 12px !important;
        }
        .cls_multi_contact {
            width: 100%;
        }
        .cls_multi_contact_first {
            width: 80%;
            display: inline-block;
        }
        .cls_multi_contact_second {
            width: 7%;
            display: inline-block;
        }
        .cls_categoryfilter_box .btn-image img {
            width: 12px !important;
        }
        .cls_categoryfilter_box {
            width: 100%;
        }
        .cls_categoryfilter_first {
            width: 80%;
            display: inline-block;
        }
        .cls_categoryfilter_second {
            width: 7%;
            display: inline-block;
        }
        .cls_comm_btn {
            margin-left: 3px;
            padding: 4px 8px;
        }
        .btn.btn-image.btn-call-data {
            margin-top: -15px;
        }
        .dis-none {
            display: none;
        }
        .no-due-date {
            background-color: #f1f1f1 !important;
        }
        .over-due-date {
            background-color: #777 !important;
            color: white;
        }
        .over-due-date .btn {
            background-color: #777 !important;
        }
        .over-due-date .btn .fa {
            color: black !important;
        }
        .no-due-date .btn {
            /* background-color: #f1f1f1 !important; */
        }
        .pd-2 {
            padding: 2px;
        }
        .zoom-img:hover {
            -ms-transform: scale(1.5);
            /* IE 9 */
            -webkit-transform: scale(1.5);
            /* Safari 3-8 */
            transform: scale(1.5);
        }
        .status-selection .btn-group {
            padding: 0;
            width: 100%;
        }
        .status-selection .multiselect {
            width: 100%;
        }
        .green-notification {
            color: green;
        }
        .red-notification {
            color: grey;
        }
        select.globalSelect2+span.select2 {
            width: calc(100% - 26px) !important;
        }
        /*start toggle button*/
        .cmn-toggle {
            position: absolute;
            margin-left: -9999px;
            visibility: hidden;
        }
        .cmn-toggle+label {
            display: block;
            position: relative;
            cursor: pointer;
            outline: none;
            user-select: none;
        }
        input.cmn-toggle-round+label {
            padding: 2px;
            width: 40px;
            height: 20px;
            background-color: #dddddd;
            border-radius: 60px;
        }
        input.cmn-toggle-round+label:before,
        input.cmn-toggle-round+label:after {
            display: block;
            position: absolute;
            top: 1px;
            left: 1px;
            bottom: 1px;
            content: "";
        }
        input.cmn-toggle-round+label:before {
            right: 1px;
            background-color: #f1f1f1;
            border-radius: 60px;
            transition: background 0.4s;
        }
        input.cmn-toggle-round+label:after {
            width: 18px;
            background-color: #fff;
            border-radius: 100%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            transition: margin 0.4s;
        }
        input.cmn-toggle-round:checked+label:before {
            background-color: #333333;
        }
        input.cmn-toggle-round:checked+label:after {
            margin-left: 20px;
        }
        .btn.btn-image {
             margin-top: 0px !important;
        }
        /*end toggle button*/

        .tablesorter-header-inner {
            white-space: nowrap;
        }
        .show-finished-task {
            height: auto;
        }
        .time_doctor_project_section_modal,
        .time_doctor_account_section_modal{
            display: none;
        }
    </style>
@endsection

@section('large_content')

    <div class="row">
        <div class="col-lg-12 text-center">
            <h2 class="page-heading">{{$title}}</h2>
        </div>
    </div>
    <!--- Pre Loader -->
    <img src="/images/pre-loader.gif" id="Preloader" style="display:none;" />
    @include('task-module.partials.modal-contact')
    @include('task-module.partials.modal-task-category')
    @include('task-module.partials.modal-task-view')
    @include('task-module.partials.modal-whatsapp-group')
    @include('task-module.partials.modal-task-bell')
    @include('task-module.partials.modal-chat')
    @include('partials.flash_messages')

    <div class="row">
        <div class="col-xs-12">
            <form class="form-search-data">
                <input type="hidden" name="daily_activity_date" value="{{ $data['daily_activity_date'] }}">
                <input type="hidden" name="type" id="tasktype" value="pending">
                <div class="row">
                    <div class="col-xs-12 col-md-1 pd-2">
                        <div class="form-group cls_task_subject">
                            <input type="text" name="term" placeholder="Search Term" id="task_search" class="form-control input-sm" value="{{ isset($term) ? $term : "" }}">
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-1 pd-2">
                        <div class="form-group">
                            {!! $task_categories_dropdown !!}
                        </div>
                    </div>

                    @if(auth()->user()->checkPermission('activity-list'))
                        <div class="col-xs-12 col-md-2 pd-2">
                            <div class="form-group ml-3">
                                <select id="search_by_user" class="form-control input-sm select2" name="selected_user">
                                    <option value="">Select a User</option>
                                    @foreach ($users as $id => $user)
                                        <option value="{{ $id }}" {{ $id == $selected_user ? 'selected' : '' }}>{{ $user }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12  col-md-1 pd-2">
                            <div class="form-group ml-3">
                                <select id="master_user_id" class="form-control select2" name="search_master_user_id" id="search_master_user_id">
                                    <option value="">Lead 1 Select</option>
                                    @foreach($users as $leadId => $leadName)
                                        <option value="{{ $leadId }}">{{ $leadName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12  col-md-1 pd-2">
                            <div class="form-group ml-3">
                                <select id="master_user_id" class="form-control select2" name="search_second_master_user_id" id="search_second_master_user_id">
                                    <option value="">Lead 2 Select
                                    <option>
                                    @foreach($users as $leadId => $leadName)
                                        <option value="{{ $leadId }}">{{ $leadName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="col-xs-12 col-md-1 pd-2">
                        <div class="form-group">
                            <select name="is_statutory_query" id="is_statutory_query" class="form-control input-sm">
                                <option @if(request('is_statutory_query')==0) selected @endif value="0">Other Task</option>
                                <option @if(request('is_statutory_query')==1) selected @endif value="1">Statutory Task</option>
                                <option @if(request('is_statutory_query')==2) selected @endif value="2">Calendar Task</option>
                            <!-- <option @if(request('is_statutory_query') == 3) selected @endif value="3">Discussion Task</option> -->
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-1 pd-2">
                        <div class="form-group">
                            <select name="sort_by" id="sort_by" class="form-control input-sm">
                                <option value="">Sort by</option>
                                <option @if(request('sort_by')==1) selected @endif value="1">Date desc</option>
                                <option @if(request('sort_by')==2) selected @endif value="2">Date Asc</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-1 pd-2">
                        <div class="form-group">
                            <select name="filter_by" id="filter_by" class="form-control input-sm">
                                <option value="">Filter by</option>
                                <option @if(request('filter_by')==1) selected @endif value="1">Pending tasks</option>
                                <option @if(request('filter_by')==2) selected @endif value="2">Completed by user</option>
                            </select>
                        </div>
                    </div>

                    {{-- <div class="col-xs-12 col-md-1 pd-2">
                        <div class="form-group">
                            <select name="filter_status" id="filter_status" class="form-control input-sm">
                                <option value="">Status Filter</option>
                                @foreach($task_statuses as $task_statuse)
                                    <option @if(request('filter_status') == $task_statuse->id) selected @endif value="{{$task_statuse->id}}">{{$task_statuse->name}}</option>
                @endforeach
                </select>
            </div>
    </div> --}}

                    <div class="col-xs-12 col-md-1 pd-2 status-selection">
                        <?php echo Form::select("filter_status[]", $statuseslist, request()->get('filter_status', $selectStatusList), ["class" => "form-control multiselect", "multiple" => true]); ?>
                    </div>


                    <div class="col-xs-12 col-md-1 pd-2">
                        <input type="checkbox" checked="checked" name="flag_filter"> Flagged
                    </div>
                    <button type="button" class="btn btn-image btn-call-data"><img src="{{asset('images/filter.png')}}" /></button>
                    <button type="button" style="height: 30px;" class="btn btn-secondary cls_comm_btn priority_model_btn">Priority</button>
                </div>

            </form>
        </div>
    </div>

    <?php
    if (\App\Helpers::getadminorsupervisor() && !empty($selected_user))
        $isAdmin = true;
    else
        $isAdmin = false;
    ?>
    <div class="row mb-2">
        <div class="col-xs-12">
            <form action="{{ route('task.create.task.shortcut') }}" method="POST" id="taskCreateForm">
                @csrf
                <input type="hidden" name="has_render" value="1">
                <input type="hidden" name="from" value="task-page">
                <!-- Purpose : Add If condition for Only Admin create Task - DEVTASK-4354 -->
                @if(auth()->user()->isAdmin())
                    <div class="row">
                        <div class="col-xs-12 col-md-1 pd-2">
                            <div class="form-group cls_task_subject">

                                <input type="text" class="form-control input-sm" name="task_subject" placeholder="Task Subject" id="task_subject" value="{{ old('task_subject') }}" required />
                                @if ($errors->has('task_subject'))
                                    <div class="alert alert-danger">{{$errors->first('task_subject')}}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-2 pd-2">
                            <div class="form-group">
                                <textarea rows="1" class="form-control input-sm cls_task_detailstextarea" name="task_detail" placeholder="Task Details" id="task_details" required>{{ old('task_detail') }}</textarea>
                                @if ($errors->has('task_detail'))
                                    <div class="alert alert-danger">{{$errors->first('task_detail')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-xs-12 col-md-1 pd-2">
                            <div class="form-inline">
                                <div class="cls_multi_contact">
                                    <div class="cls_multi_contact_first">
                                        <div class="">
                                            <select id="multi_contacts" style="width: 100%;" class="form-control input-sm js-example-basic-multiple" name="assign_to_contacts[]" multiple>
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
                                    </div>
                                    <div class="cls_multi_contact_second">
                                        <button type="button" class="btn btn-image" data-toggle="modal" data-target="#createQuickContactModal"><img src="{{asset('images/add.png')}}" /></button>
                                    </div>
                                </div>



                            </div>
                        </div>
                        <div class="col-xs-12 col-md-1 pd-2">
                            <div class="form-inline">
                                <div class="cls_categoryfilter_box">
                                    <div class="cls_categoryfilter_first">
                                        <div class="">
                                            {{-- <strong>Category:</strong> --}}
                                            {!! $task_categories_dropdown !!}
                                            {{-- <select class="form-control input-sm" name="category" id="required_category" required>
                                          <option value="">Select a Category</option>
                                          @foreach ($task_categories_dropdown as $category)
                                            <option value="{{ $category['id'] }}">{{ $category['title'] }}</option>
                                    @foreach ($category['child'] as $child)
                                    <option value="{{ $child['id'] }}">&nbsp;&nbsp;{{ $child['title'] }}</option>
                                    @endforeach
                                    @endforeach
                                    </select> --}}
                                        </div>
                                    </div>
                                    <div class="cls_categoryfilter_second">
                                        <button type="button" class="btn btn-image" data-toggle="modal" data-target="#createTaskCategorytModal"><img src="{{asset('images/add.png')}}" /></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-1 pd-2">
                            <div class="form-group">
                                <select id="is_milestone" class="form-control" name="is_milestone" required>
                                    <option value="0">Is milestone</option>
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>

                                @if ($errors->has('is_milestone'))
                                    <div class="alert alert-danger">{{$errors->first('is_milestone')}}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-1 pd-2">
                            <div class="form-group">
                                <input type="number" class="form-control" id="no_of_milestone" name="no_of_milestone" value="{{ old('no_of_milestone') }}" placeholder="No of milestone" />

                                @if ($errors->has('no_of_milestone'))
                                    <div class="alert alert-danger">{{$errors->first('no_of_milestone')}}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-1 pd-2">
                            <div class="form-group cls_task_subject">
                                <select name="task_type" class="form-control is_statutory input-sm">
                                    <option value="0">Other Task</option>
                                    <option value="1">Statutory Task</option>
                                    <option value="2">Calendar Task</option>
                                    <option value="3">Discussion Task</option>
                                </select>
                            </div>
                        </div>
                        @if(auth()->user()->isAdmin())
                            <div class="col-xs-12 col-md-2 pd-2">
                                <div class="form-group">
                                    <select id="multi_users" class="form-control input-sm" name="task_asssigned_to[]" multiple>
                                        @foreach ($data['users'] as $user)
                                            <option value="{{ $user['id'] }}">{{ $user['name'] }} - {{ $user['email'] }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('task_asssigned_to'))
                                        <div class="alert alert-danger">{{$errors->first('task_asssigned_to')}}</div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <div class="col-xs-12 col-md-2 pd-2">
                            <div class="form-group">
                                <label><input type="checkbox" name="need_review_task" value="1" /> Create Review Task?</label>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-1 pd-2">
                            <div class="form-group ml-3">
                                <select name="task_for" class="form-control task_for" style="width:100%;">
                                    <option value="hubstaff" selected>Hubstaff</option>
                                    <option value="time_doctor">Time Doctor</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-1 pd-2 time_doctor_account_section">
                            <div class="form-group ml-3">
                                <?php echo Form::select("time_doctor_account",['' => ''],null,["class" => "form-control time_doctor_account globalSelect2" ,"style" => "width:100%;", 'data-ajax' => route('select2.time_doctor_accounts_for_task'), 'data-placeholder' => 'Account']); ?>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-1 pd-2 time_doctor_project_section">
                            <div class="form-group ml-3">
                                <?php echo Form::select("time_doctor_project",['' => ''],null,["class" => "form-control time_doctor_project globalSelect2" ,"style" => "width:100%;", 'data-ajax' => route('select2.time_doctor_projects'), 'data-placeholder' => 'Project']); ?>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-2 pd-2">
                            <div class="form-group">
                                <button type="submit" class="btn btn-secondary cls_comm_btn" id="taskCreateButton">Create</button>
                                @if(auth()->user()->isAdmin())
                                    <a class="btn btn-secondary cls_comm_btn" data-toggle="collapse" href="#openFilterCount" role="button" aria-expanded="false" aria-controls="openFilterCount">
                                        Open Task count
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-xs-12 col-md-4" id="recurring-task" style="display:none;">
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <select name="recurring_type" class="form-control input-sm">
                                        <option value="">Select</option>
                                        <option value="EveryHour">EveryHour</option>
                                        <option value="EveryDay">EveryDay</option>
                                        <option value="EveryWeek">EveryWeek</option>
                                        <option value="EveryMonth">EveryMonth</option>
                                        <option value="EveryYear">EveryYear</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <div class='input-group date' id='sending-datetime'>
                                        <input type='text' class="form-control input-sm" name="sending_time" value="{{ date('Y-m-d H:i') }}" required />
                                        <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    </div>

                                    @if ($errors->has('sending_time'))
                                        <div class="alert alert-danger">{{$errors->first('sending_time')}}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-2" id="calendar-task" style="display: none;">
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
                    <div class="col-xs-12 col-md-4" style="display: none;padding-left: 15px;" id="appointment-container">
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <?php echo Form::select("task_id", ["0" => "-- Add New --"] + \App\Task::where("is_statutory", 3)->where("task_subject", "!=", "''")->get()->pluck("task_subject", "id")->toArray(), null, [
                                        "class" => "form-control select2-task-disscussion input-sm"
                                    ]); ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6">
                                <div class="form-inline flex-fill">
                                    <div class="form-group cls_textbox_notes">
                                        <input type="text" style="width: 100%;" class="form-control input-sm" name="note[]" placeholder="Note" value="">
                                    </div>
                                    <button type="button" class="btn btn-xs btn-secondary" title="Add Note" id="addNoteButton">Add Note</button>&nbsp;
                                    <button type="button" class="btn btn-xs btn-secondary dis-none" id="saveNewNotes">Save New Notes</button>&nbsp;
                                    <div id="note-container">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @include('task-module.partials.modal-reminder')

    @if(auth()->user()->isAdmin())

        @include('task-module.partials.modal-task-status')

        <!-- <div class="row" style="margin-bottom:10px;">
            <div class="col-md-2">
                <a class="btn btn-secondary" data-toggle="collapse" href="#openFilterCount" role="button" aria-expanded="false" aria-controls="openFilterCount">
                       Open Task count
                    </a>
            </div>
        </div> -->
        <div class="row">
            <div class="col-md-12">
                <div class="collapse" id="openFilterCount">
                    <div class="card card-body">
                        <?php if (!empty($openTask)) { ?>
                        <div class="row col-md-12">
                            <?php foreach ($openTask as $k => $v) { ?>
                            <div class="col-md-2">
                                <div class="card">
                                    <div class="card-header">
                                        <?php echo $k; ?>
                                    </div>
                                    <div class="card-body">
                                        <?php echo $v; ?>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <?php } else {
                            echo "Sorry , No data available";
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div id="exTab2" style="overflow: auto">
        <ul class="nav nav-tabs">

            <li class="active"><a href="#1" data-toggle="tab" class="btn-call-data" data-type="pending">Pending Task</a></li>
            <li><a href="#2" data-toggle="tab" class="btn-call-data" data-type="statutory_not_completed">Statutory Activity</a></li>
            <li><a href="#3" data-toggle="tab" class="btn-call-data" data-type="completed">Completed Task</a></li>
            <li><a href="#unassigned-tab" data-toggle="tab">Unassigned Messages</a></li>

            <li> <button type="button" onclick="window.location.href = '{{ action([\App\Http\Controllers\DevelopmentController::class, 'exportTask'],request()->all()) }}'" class="btn btn-xs btn-secondary my-3" role="link"> Download Tasks </button></li> &nbsp;
            <li><button type="button" class="btn btn-xs btn-secondary my-3" id="view_tasks_button" data-selected="0">View Tasks</button></li>&nbsp;
            <li><button type="button" class="btn btn-xs btn-secondary my-3" id="send_message_button" data-selected="0">Send Message</button></li>&nbsp;
            <li><button type="button" class="btn btn-xs btn-secondary my-3" id="view_categories_button">Categories</button></li>&nbsp;
            <li><button type="button" class="btn btn-xs btn-secondary my-3" id="make_complete_button">Complete Tasks</button></li>&nbsp;
            <li><button type="button" class="btn btn-xs btn-secondary my-3" id="make_delete_button">Delete Tasks</button></li>&nbsp;


            {{-- href="{{ action([\App\Http\Controllers\DevelopmentController::class, 'exportTask'],request()->all()) }}"--}}

            @if(Auth::user()->isAdmin())
                <li><button type="button" class="btn btn-xs btn-secondary my-3" data-toggle='modal' data-target='#taskStatusModal' id="">Create Status</button></li>&nbsp;
            @endif
            @if (Auth::user()->isAdmin())
                <li><button class="btn btn-xs btn-secondary my-3" style="color:white;" data-toggle="modal" data-target="#newStatusColor"> Status Color</button></li>&nbsp;
            @endif

        </ul>
        <div class="tab-content ">
            <!-- Pending task div start -->
            <div class="tab-pane active" id="1">
                <div class="row" style="margin:0px;">
                    <!-- <h4>List Of Pending Tasks</h4> -->
                    <div class="col-12">
                        <img class="infinite-scroll-products-loader center-block" src="{{asset('/images/loading.gif')}}" alt="Loading..." style="display: none" />
                    </div>
                    <div class="col-12">
                        <table class="table table-sm table-bordered">
                            <thead>
                            <tr>
                                <th width="4%">ID</th>
                                <th width="7%">Date</th>
                                <th width="4%" class="category">Category</th>
                                <th width="4%">Task Subject</th>
                                <th width="10%">Assign To</th>
                                <th width="8%">Status</th>
                                <th width="5%">Tracked time</th>
                                <th class="communication_th">Communication</th>
                                <th width="6%">Estimated Time</th>
                                <th width="6%">Estimated Start Datetime</th>
                                <th width="6%">Estimated End Datetime</th>
                                <th width="6%">
                                    ICON &nbsp;
                                    <label><input type="checkbox" class="show-finished-task" name="show_finished" value="on"> Finished</label>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="pending-row-render-view">
                                @if(count($data['task']['pending']) >0)
                                @foreach($data['task']['pending'] as $task)
                                    @php
                                        $taskDueDate = $task->due_date;
                            $task->due_date='';
                            //$task->lead_hubstaff_task_id=0;
                            //$task->status=1;
                                    $status_color = \App\TaskStatus::where('id',$task->status)->first();
                                    if ($status_color == null) {
                                        $status_color = new stdClass();
                                    }
                                    @endphp
                                    <tr style="background-color: {{$status_color->task_color ?? ""}}!important;" class="{{ \App\Http\Controllers\TaskModuleController::getClasses($task) }} {{ !$task->due_date ? 'no-due-date' : '' }} {{ $task->due_date && (date('Y-m-d H:i') > $task->due_date && !$task->is_completed) ? 'over-due-date' : '' }} {{ $task->is_statutory == 3 ? 'row-highlight' : '' }}" id="task_{{ $task->id }}">
                                        <td class="p-2">
                                            @if(auth()->user()->isAdmin())
                                                <input type="checkbox" name="selected_issue[]" value="{{$task->id}}" title="Task is in priority" {{in_array($task->id, $priority) ? 'checked' : ''}}>
                                            @endif
                                            <input type="checkbox" title="Select task" class="select_task_checkbox" name="task" data-id="{{ $task->id }}" value="">
                                            {{ $task->id }}
                                        </td>
                                        <td class="p-2">{{ Carbon\Carbon::parse($task->created_at)->format('d-m H:i') }}
                                            <br>
                                            @if($task->customer_id)
                                                Cus-{{$task->customer_id}}
                                                <br>
                                                @if(Auth::user()->isAdmin())
                                                    @php
                                                        $customer = \App\Customer::find($task->customer_id);
                                                    @endphp
                                                    <span>
                                        {{ isset($customer ) ? $customer->name : '' }}
                                    </span>
                                                @endif
                                            @endif
                                        </td>
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
                                        <td class="expand-row" data-subject="{{$task->task_subject ? $task->task_subject : 'Task Details'}}" data-details="{{$task->task_details}}" data-switch="0" style="word-break: break-all;">
                                    <span class="td-mini-container">
                                        {{ $task->task_subject ? substr($task->task_subject, 0, 15) . (strlen($task->task_subject) > 15 ? '...' : '') : 'Task Details' }}
                                    </span>
                                            <span class="td-full-container hidden">
                                        <strong>{{ $task->task_subject ? $task->task_subject : 'Task Details' }}</strong>
                                        {{ $task->task_details }}
                                    </span>
                                        </td>
                                    <!-- <td class="expand-row table-hover-cell p-2">
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
                                            </td> -->
                                        <td class="table-hover-cell p-2">
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

                                        <!--<span class="td-mini-container">
                                                {{ strlen($users_list) > 15 ? substr($users_list, 0, 15) : $users_list }}
                                                </span>-->

                                            @if(auth()->user()->isAdmin() || $isTeamLeader)
                                                <select id="assign_to" class="form-control assign-user select2" data-id="{{$task->id}}" data-lead="1" name="master_user_id" id="user_{{$task->id}}">
                                                    <option value="">Select...</option>
                                                    <?php $masterUser = isset($task->assign_to) ? $task->assign_to : 0; ?>
                                                    @foreach($users as $id=>$name)
                                                        @if( $masterUser == $id )
                                                            <option value="{{$id}}" selected>{{ $name }}</option>
                                                        @else
                                                            <option value="{{$id}}">{{ $name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            @else
                                                @if($task->assign_to)
                                                    @if(isset($users[$task->assign_to]))
                                                        <p>{{$users[$task->assign_to]}}</p>
                                                    @else
                                                        <p>-</p>
                                                    @endif
                                                @endif
                                            @endif

                                            <span class="td-full-container hidden">
                                        {{ $users_list }}
                                    </span>
                                            <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-user-history" title="Show History" data-id="{{$task->id}}"><i class="fa fa-info-circle"></i></button>
                                            <div class="col-md-12 expand-col-lead{{$task->id}} dis-none" style="padding:0px;">
                                                <br>
                                                @if(auth()->user()->isAdmin()  || $isTeamLeader)
                                                    <label for="" style="font-size: 12px;margin-top:10px;">Lead :</label>
                                                    <select id="master_user_id" class="form-control assign-master-user select2" data-id="{{$task->id}}" data-lead="1" name="master_user_id" id="user_{{$task->id}}">
                                                        <option value="">Select...</option>
                                                        <?php $masterUser = isset($task->master_user_id) ? $task->master_user_id : 0; ?>
                                                        @foreach($users as $id=>$name)
                                                            @if( $masterUser == $id )
                                                                <option value="{{$id}}" selected>{{ $name }}</option>
                                                            @else
                                                                <option value="{{$id}}">{{ $name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                @else
                                                    @if($task->master_user_id)
                                                        @if(isset($users[$task->master_user_id]))
                                                            <p>{{$users[$task->master_user_id]}}</p>
                                                        @else
                                                            <p>-</p>
                                                        @endif
                                                    @endif
                                                @endif

                                                <br>

                                                @if(auth()->user()->isAdmin()  || $isTeamLeader)
                                                    <label for="" style="font-size: 12px;margin-top:10px;">Lead 2 :</label>
                                                    <select id="master_user_id" class="form-control assign-master-user select2" data-id="{{$task->id}}" data-lead="2" name="master_user_id" id="user_{{$task->id}}">
                                                        <option value="">Select...</option>
                                                        <?php $masterUser = isset($task->second_master_user_id) ? $task->second_master_user_id : 0; ?>
                                                        @foreach($users as $id=>$name)
                                                            @if( $masterUser == $id )
                                                                <option value="{{$id}}" selected>{{ $name }}</option>
                                                            @else
                                                                <option value="{{$id}}">{{ $name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                @else
                                                    @if($task->second_master_user_id)
                                                        @if(isset($users[$task->second_master_user_id]))
                                                            <p>{{$users[$task->second_master_user_id]}}</p>
                                                        @else
                                                            <p>-</p>
                                                        @endif
                                                    @endif
                                                @endif

                                                @if($task->is_milestone)
                                                    <p style="margin-bottom:0px;">Total : {{$task->no_of_milestone}}</p>
                                                    @if($task->no_of_milestone == $task->milestone_completed)
                                                        <p style="margin-bottom:0px;">Done : {{$task->milestone_completed}}</p>
                                                    @else
                                                        <input type="number" name="milestone_completed" id="milestone_completed_{{$task->id}}" placeholder="Completed..." class="form-control save-milestone" value="{{$task->milestone_completed}}" data-id="{{$task->id}}">
                                                    @endif
                                                @else
                                                    <p>No milestone</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <select id="master_user_id" class="form-control change-task-status select2" data-id="{{$task->id}}" name="master_user_id" id="user_{{$task->id}}">
                                                <option value="">Select...</option>
                                                <?php $masterUser = isset($task->master_user_id) ? $task->master_user_id : 0; ?>
                                                @foreach($task_statuses as $index => $status)
                                                    @if(!auth()->user()->isAdmin() AND $status->name == 'Done')
                                                        @continue
                                                    @endif
                                                    @if( $status->id == $task->status )
                                                        <option value="{{$status->id}}" selected>{{ $status->name }}</option>
                                                    @else
                                                        <option value="{{$status->id}}">{{ $status->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            @if (isset($special_task->timeSpent) && $special_task->timeSpent->task_id > 0)
                                                {{ formatDuration($special_task->timeSpent->tracked) }}
                                                <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history" title="Show tracked time History" data-id="{{$task->id}}" data-type="developer"><i class="fa fa-info-circle"></i></button>
                                            @endif

                                            <div class="col-md-12 expand-col" style="padding:0px;">
                                                @if(!$task->hubstaff_task_id && (auth()->user()->isAdmin() || auth()->user()->id == $task->assign_to))
                                                    <button type="button" class="btn btn-xs create-hubstaff-task" title="Create Hubstaff task for User" data-id="{{$task->id}}" data-type="developer">Create D Task</button>
                                                @endif
                                                @if(!$task->lead_hubstaff_task_id && $task->master_user_id && (auth()->user()->isAdmin() || auth()->user()->id == $task->master_user_id))
                                                    <button style="margin-top:10px;color:black;" type="button" class="btn btn-secondary btn-xs create-hubstaff-task" title="Create Hubstaff task for Master user" data-id="{{$task->id}}" data-type="lead">Create L Task</button>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="table-hover-cell p-2 {{ ($task->message && $task->message_status == 0) || $task->message_is_reminder == 1 || ($task->message_user_id == $task->assign_from && $task->assign_from != Auth::id()) ? 'text-danger' : '' }}">
                                            @if ($task->assign_to == Auth::id() || ($task->assign_to != Auth::id() && $task->is_private == 0))
                                                <div style="margin-bottom:10px;width: 100%;">
                                                    <?php $text_box = "100"; ?>
                                                    <textarea rows="2" class="form-control quick-message-field input-sm" id="getMsg{{$task->id}}" name="message" placeholder="Message"></textarea>
                                                    {{-- <input type="text" style="width: 100%;" class="form-control quick-message-field input-sm" id="getMsg{{$task->id}}" name="message" placeholder="Message" value=""> --}}
                                                    <div class="d-flex">
                                                        <div style="">
                                                            <button id="send-message_{{ $task->id }}" class="btn btn-sm btn-image send-message" title="Send message" data-taskid="{{ $task->id }}"><img src="{{asset('images/filled-sent.png')}}" /></button>

                                                            <input type="hidden" name="is_audio" id="is_audio_{{$task->id}}" value="0" >
                                                            <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image btn-trigger-rvn-modal" data-id="{{$task->id}}" data-tid="{{$task->id}}" data-load-type="text" data-all="1" title="Record & Send Voice Message"><img src="{{asset('images/record-voice-message.png')}}" alt=""></button>
                                                        </div>
                                                        @if (isset($task->message))
                                                            <div style="max-width: 30px;">
                                                                <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='task' data-id="{{ $task->id }}" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                                                            </div>
                                                        @endif
                                                        <button class="btn btn-image upload-task-files-button ml-2" type="button" title="Uploaded Files" data-task_id="{{$task->id}}">
                                                            <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                                                        </button>
                                                        <button class="btn btn-image view-task-files-button ml-2" type="button" title="View Uploaded Files" data-task_id="{{$task->id}}">
                                                            <img src="/images/google-drive.png" style="cursor: nwse-resize; width: 10px;">
                                                        </button>
                                                    </div>
                                                    @if (isset($task->message))
                                                        <div style="margin-bottom:10px;width: 100%;">
                                                            @if (isset($task->is_audio))
                                                                <audio controls="" src="{{ \App\Helpers::getAudioUrl($task->message) }}"></audio>
                                                            @else
                                                            <div class="d-flex justify-content-between expand-row-msg" data-id="{{$task->id}}">
                                                                <span class="td-mini-container-{{$task->id}}" style="margin:0px;">
                                                                <?php
                                                                    if (!empty($task->message) && !empty($task->task_subject)) {
                                                                        $pos = strpos($task->message, $task->task_subject);
                                                                        $length = strlen($task->task_subject);
                                                                        if ($pos) {
                                                                            $start = $pos + $length + 1;
                                                                        } else {
                                                                            $start = 0;
                                                                        }
                                                                    } else {
                                                                        $start = 0;
                                                                    }
                                                                    ?>
                                                                {{substr($task->message, $start,28)}}
                                                                </span>
                                                            </div>
                                                            <div class="expand-row-msg" data-id="{{$task->id}}">
                                                                <span class="td-full-container-{{$task->id}} hidden">
                                                                {{ $task->message }}
                                                                </span>
                                                            </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                                @if(auth()->user()->isAdmin())
                                                    <div style="margin-bottom:10px;width: 100%;">
                                                        <div class="expand-col dis-none">
                                                            <label for="">Lead:</label>
                                                            <div class="d-flex">
                                                                <input type="text" style="width: 100%;" class="form-control quick-message-field input-sm" id="getMsg{{$task->id}}" name="message" placeholder="Message" value="">
                                                                <div style="max-width: 30px;"><button class="btn btn-sm btn-image send-message-lead" title="Send message" data-taskid="{{ $task->id }}"><img src="{{asset('images/filled-sent.png')}}" /></button></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                Private
                                            @endif
                                        </td>
                                        <td class="p-2">
                                            <div style="margin-bottom:10px;width: 100%;">
                                                <div class="d-flex">
                                                    <input type="number" class="form-control" name="approximates{{$task->id}}" value="{{$task->approximate}}" min="1" autocomplete="off">
                                                    <div style="max-width: 30px;"><button class="btn btn-sm btn-image send-approximate-lead" title="Send approximate" onclick="funTaskInformationUpdatesTime('approximate',{{$task->id}})" data-taskid="{{ $task->id }}"><img src="{{asset('images/filled-sent.png')}}" /></button></div>
                                                </div>
                                            </div>
                                        </td>
                                        @php
                                            $single = \App\Task::where('tasks.id', $task->id)->select('tasks.*', DB::raw('(SELECT remark FROM developer_tasks_history WHERE developer_task_id=tasks.id ORDER BY id DESC LIMIT 1) as task_remark'), DB::raw('(SELECT new_value FROM task_history_for_start_date WHERE task_id=tasks.id ORDER BY id DESC LIMIT 1) as task_start_date'), DB::raw("(SELECT new_due_date FROM task_due_date_history_logs WHERE task_id=tasks.id AND task_type='TASK' ORDER BY id DESC LIMIT 1) as task_new_due_date"))->first();
                                        @endphp
                                        <td class="p-2">
                                            <div class="form-group d-flex">
                                                <div class='input-group date cls-start-due-date'>
                                                    <input type="text" class="form-control" name="start_dates{{$task->id}}" value="{{$single->task_start_date}}" autocomplete="off" />
                                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                                </div>
                                                    <div style="max-width: 30px;"><button class="btn btn-sm btn-image send-start_date-lead" title="Send approximate" onclick="funTaskInformationUpdatesTime('start_date',{{$task->id}})" data-taskid="{{ $task->id }}"><img src="{{asset('images/filled-sent.png')}}" /></button></div>
                                            </div>
                                        </td>
                                        <td class="p-2">
                                            <div class="form-group d-flex">
                                                <div class='input-group date cls-start-due-date'>
                                                    <input type="text" class="form-control" name="due_dates{{$task->id}}" value="{{$single->task_new_due_date}}" autocomplete="off" />
                                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                                </div>
                                                <div style="max-width: 30px;"><button class="btn btn-sm btn-image send-start_date-lead" title="Send approximate" onclick="funTaskInformationUpdatesTime('due_date',{{$task->id}})" data-taskid="{{ $task->id }}"><img src="{{asset('images/filled-sent.png')}}" /></button></div>
                                            </div>
                                        </td>
                                        <td class="p-2">
                                            <div class="dropdown dropleft">
                                                <a class="btn btn-secondary btn-sm dropdown-toggle" href="javascript:void(0);" role="button" id="dropdownMenuLink{{$task->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Actions
                                                </a>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink{{$task->id}}">
                                                    <a class="dropdown-item" href="javascript:void(0);" onclick="funTaskInformationModal(this, '{{$task->id}}')">Task Information: Update</a>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$task->id}}')"><i class="fa fa-arrow-down"></i></button>
                                        </td>
                                    </tr>
                                    <tr class="action-btn-tr-{{$task->id}} d-none">
                                        <td class="font-weight-bold">Action</td>
                                        <td colspan="11">
                                            <div>
                                                <div class="row cls_action_box" style="margin:0px;">
                                                    @if(auth()->user()->isAdmin())
                                                        <button type="button" class='btn btn-image whatsapp-group pd-5' data-id="{{ $task->id }}" data-toggle='modal' data-target='#whatsAppMessageModal'><img src="{{asset('images/whatsapp.png')}}" /></button>
                                                        <button type="button" class='btn delete-single-task pd-5' data-id="{{ $task->id }}"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                    @endif
                                                    <button data-toggle="modal" data-target="#taskReminderModal" class='btn pd-5 task-set-reminder' data-id="{{ $task->id }}" data-frequency="{{ !empty($task->reminder_message) ? $task->frequency : '60' }}" data-reminder_message="{{ !empty($task->reminder_message) ? $task->reminder_message : 'Plz update' }}" data-reminder_from="{{ $task->reminder_from }}" data-reminder_last_reply="{{ ($task && !empty($task->reminder_last_reply)) ? $task->reminder_last_reply : '' }}">
                                                        <i class="fa fa-bell @if(!empty($task->reminder_message) && $task->frequency > 0) {{ 'green-notification'  }} @else {{ 'red-notification' }} @endif" aria-hidden="true"></i>
                                                    </button>
                                                    @if ($special_task->users->contains(Auth::id()) || $task->assign_from == Auth::id() || $task->master_user_id == Auth::id() || $task->second_master_user_id == Auth::id())
                                                        {{-- <button type="button" title="Complete the task by user" class="btn btn-image task-complete pd-5" data-id="{{ $task->id }}"><img src="/images/incomplete.png" /></button> --}}
                                                        {{-- @if ($task->assign_from == Auth::id()) --}}
                                                        @if(auth()->user()->isAdmin())
                                                            <button type="button" title="Verify the task by admin" class="btn btn-image task-complete pd-5" data-id="{{ $task->id }}"><img src="/images/completed-green.png" /></button>
                                                            {{-- @else
                                            <button type="button" class="btn btn-image pd-5"><img src="/images/completed-green.png" /></button> --}}
                                                        @endif
                                                        <button type="button" class='btn btn-image ml-1 reminder-message pd-5' data-id="{{ $task->message_id }}" data-toggle='modal' data-target='#reminderMessageModal'><img src='/images/reminder.png' /></button>
                                                        <button type="button" data-id="{{ $task->id }}" class="btn btn-file-upload pd-5">
                                                            <i class="fa fa-upload" aria-hidden="true"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn preview-img-btn pd-5" data-id="{{ $task->id }}">
                                                        <i class="fa fa-list" aria-hidden="true"></i>
                                                    </button>
                                                    @if ((!$special_task->users->contains(Auth::id()) && $special_task->contacts()->count() == 0))
                                                        @if ($task->is_private == 1)
                                                            <button disabled type="button" class="btn btn-image pd-5"><img src="{{asset('images/private.png')}}" /></button>
                                                        @else
                                                            {{-- <a href="{{ route('task.show', $task->id) }}" class="btn btn-image pd-5" href=""><img src="{{asset('images/view.png')}}" /></a> --}}
                                                        @endif
                                                    @endif

                                                    @if ($special_task->users->contains(Auth::id()) || ($task->assign_from == Auth::id() && $task->is_private == 0) || ($task->assign_from == Auth::id() && $special_task->contacts()->count() > 0) || Auth::id() == 6)
                                                        <a href="{{ route('task.show', $task->id) }}" class="btn btn-image pd-5" href=""><img src="{{asset('images/view.png')}}" /></a>
                                                    @endif

                                                    @if ($task->is_flagged == 1)
                                                        <button type="button" class="btn btn-image flag-task pd-5" data-id="{{ $task->id }}"><img src="{{asset('images/flagged.png')}}" /></button>
                                                    @else
                                                        <button type="button" class="btn btn-image flag-task pd-5" data-id="{{ $task->id }}"><img src="{{asset('images/unflagged.png')}}" /></button>
                                                    @endif
                                                    <button class="btn btn-image expand-row-btn-lead" data-task_id="{{ $task->id }}"><img src="/images/forward.png"></button>
                                                    <button class="btn btn-image set-remark" data-task_id="{{ $task->id }}" data-task_type="TASK"><i class="fa fa-comment" aria-hidden="true"></i></button>

                                                    <button class="btn btn-image mt-2 create-task-document" title="Create document" data-id="{{$task->id}}">
                                                        <i class="fa fa-file-text" aria-hidden="true"></i>
                                                    </button>
                                                    <button class="btn btn-image mt-2 show-created-task-document" title="Show created document" data-id="{{$task->id}}">
                                                        <i class="fa fa-list" aria-hidden="true"></i>
                                                    </button>

                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Pending task div end -->
            <!-- Statutory task div start -->
            <div class="tab-pane" id="2">
                <div class="row" style="margin:0px;">
                    <div class="col-12">
                        <!-- <h4>Statutory Activity Completed</h4> -->
                        <table class="table table-sm table-bordered">
                            <thead>
                            <tr>
                                <th width="8%">ID</th>
                                <th width="7%">Date</th>
                                <th width="8%" class="category">Category</th>
                                <th width="14%">Task Details</th>
                                <th width="5%">Assign to</th>
                                <th width="5%">Reccuring</th>
                                <th width="8%">ED</th>
                                <th width="35%">Communication</th>
                                <th width="10%">Actions &nbsp;
                                    <input type="checkbox" class="show-finished-task" name="show_finished" value="on">
                                    <label>Finished</label>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="statutory-row-render-view infinite-scroll-statutory-inner">
                            @if(count($data['task']['statutory_not_completed']) >0)
                                @foreach( $data['task']['statutory_not_completed'] as $task)
                                    @include("task-module.partials.statutory-row",compact('task'))
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Statutory task div end -->
            <!-- Completed task div start -->
            <div class="tab-pane" id="3">
                <div class="row" style="margin:0px;">
                    <!-- <h4>List Of Completed Tasks</h4> -->
                    <table class="table table-sm table-bordered">
                        <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="8%">Date</th>
                            <th width="8%" class="category">Category</th>
                            <th width="20%">Task Details</th>
                            <th width="8%">Assign to</th>
                            <th width="8%">Completed On</th>
                            <th width="30%">Communication</th>
                            <th width="13%">Action</th>
                        </tr>
                        </thead>
                        <tbody class="completed-row-render-view infinite-scroll-completed-inner">
                        @if(count($data['task']['completed']) >0)
                            @foreach( $data['task']['completed'] as $task)
                                @include("task-module.partials.completed-row",compact('task'))
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
{{--            <img class="infinite-scroll-products-loader center-block" src="{{asset('/images/loading.gif')}}" alt="Loading..." style="display: none" />--}}

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
                                            <option data-tokens="{{ $task->id }} {{ $task->task_subject }} {{ $task->task_details }} {{ array_key_exists($task->assign_from, $users) ? $users[$task->assign_from] : '' }} {{ array_key_exists($task->assign_to, $users) ? $users[$task->assign_to] : '' }}" value="{{ $task->id }}">{{ $task->id }} from {{ @$users[$task->assign_from] }} {{ $task->task_subject }}</option>
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

    <div id="priority_model" class="modal fade" role="dialog">
        <div class="modal-dialog" style="max-width: 100%; width:95%;">

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
                            <div class="col-md-6">

                                <strong>User:</strong>


                                <div class="form-group">
                                    @if(auth()->user()->isAdmin())
                                        <select class="form-control" id="priority_user_id">
                                            <option value="0">Select User</option>
                                            @foreach ($users as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="user_id" value="" id="sel_user_id" />
                                    @else
                                        {{auth()->user()->name}}
                                    @endif
                                </div>

                            </div>
                            <div class="col-md-6">
                                <strong>Remarks:</strong>
                                @if(auth()->user()->isAdmin())
                                    <div class="form-group">
                                        <textarea cols="45" class="form-control" name="global_remarkes" style="height:32px !important;"></textarea>
                                    </div>
                                @endif

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped" style="table-layout: fixed;">
                                    <tr>
                                        <th width="4%">ID</th>
                                        <th width="16%">Subject</th>
                                        <th width="35%">Task</th>
                                        <th width="10%">Communication</th>
                                        <th width="13%">Date</th>
                                        <th width="8%">Submitted By</th>
                                        <th width="5%">Action</th>
                                    </tr>
                                    <tbody class="show_task_priority">

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


    <div id="allTaskCategoryModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content" id="category-list-area">

            </div>
        </div>
    </div>

    <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                    <input type="hidden" id="chat_obj_type" name="chat_obj_type">
                    <input type="hidden" id="chat_obj_id" name="chat_obj_id">
                    <button type="submit" class="btn btn-default downloadChatMessages">Download</button>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="create-task-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create task</h4>
                </div>
                <div class="modal-body" id="create-task-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="preview-task-image" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th style="width:1%;">No</th>
                                <th style=" width: 30%">Files</th>
                                <th style="word-break: break-all; width:12%">Send to</th>
                                <th style="width: 1%;">User</th>
                                <th style="width: 11%">Created at</th>
                                <th style="width: 6%">Action</th>
                            </tr>
                            </thead>
                            <tbody class="task-image-list-view">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="preview-task-create-get-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Task Remark</h4>
                    <input type="text" name="remark_pop" class="form-control remark_pop" placeholder="Please enter remark" style="width: 200px;">
                    <button type="button" class="btn btn-default sub_remark" data-task_id="">Save</button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th style="width:1%;">ID</th>
                                <th style=" width: 12%">Update By</th>
                                <th style="word-break: break-all; width:12%">Remark</th>
                                <th style="width: 11%">Created at</th>
                                <th style="width: 11%">Action</th>
                            </tr>
                            </thead>
                            <tbody class="task-create-get-list-view">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <div id="file-upload-area-section" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('task.save-documents') }}" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="task_id" id="hidden-task-id" value="">
                    <div class="modal-header">
                        <h4 class="modal-title">Upload File(s)</h4>
                    </div>
                    <div class="modal-body" style="background-color: #999999;">
                        @csrf
                        <div class="form-group">
                            <label for="document">Documents</label>
                            <div class="needsclick dropzone" id="document-dropzone">

                            </div>
                        </div>
                        <div class="form-group add-task-list">

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-save-documents">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="send-message-text-box" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('task.send-brodcast') }}" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="task_id" id="hidden-task-id" value="">
                    <div class="modal-header">
                        <h4 class="modal-title">Send Brodcast Message</h4>
                    </div>
                    <div class="modal-body" style="background-color: #999999;">
                        @csrf
                        <div class="form-group">
                            <label for="document">Message</label>
                            <textarea class="form-control message-for-brodcast" name="message" placeholder="Enter your message"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-send-brodcast-message">Send</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route("task.save-documents") }}" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="task_id" id="hidden-task-id" value="">
                    <div class="modal-header">
                        <h4 class="modal-title">Upload File(s)</h4>
                    </div>
                    <div class="modal-body" style="background-color: #999999;">
                        @csrf
                        <div class="form-group">
                            <label for="document">Documents</label>
                            <div class="needsclick dropzone" id="document-dropzone">

                            </div>
                        </div>
                        <div class="form-group add-task-list">

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-save-documents">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="previewDoc" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="col-md-12">
                        <iframe src="" id="previewDocSource" width='700' height='550' allowfullscreen webkitallowfullscreen></iframe>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;"></div>

    @include("task-module.partials.tracked-time-history")
    @include("development.partials.user_history_modal")

    <div id="recurring-history-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Sl no</th>
                                <th>Log case id</th>
                                <th>Message</th>
                                <th>Log msg </th>
                                <th>Date/Time</th>
                            </tr>
                            </thead>
                            <tbody class="recurring-history-list-view">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @include("task-module.task-update-modal")
    @include("task-module.partials.time-history-modal")
    @include("task-module.partials.modal-status-color")

<div id="create-d-task-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Task</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="<?php echo route('task.create.hubstaff_task'); ?>" method="post" id="assign_task_form">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <input type="hidden" name="id" id="issueId"/>
                        <input type="hidden" name="type" id="type"/>
                        <label for="task_for_modal">Task For</label>
                        <select name="task_for_modal" class="form-control task_for_modal" style="width:100%;">
                            <option value="">Select</option>
                            <option value="hubstaff">Hubstaff</option>
                            <option value="time_doctor">Time Doctor</option>
                        </select>
                    </div>
                    <div class="form-group time_doctor_account_section_modal">
                        <label for="time_doctor_account">Task Account</label>
                        <?php echo Form::select("time_doctor_account",['' => ''],null,["class" => "form-control time_doctor_account_modal globalSelect2" ,"style" => "width:100%;", 'data-ajax' => route('select2.time_doctor_accounts_for_task'), 'data-placeholder' => 'Account']); ?>
                    </div>
                    <div class="form-group time_doctor_project_section_modal">
                        <label for="time_doctor_project">Time Doctor Project</label>
                        <?php echo Form::select("time_doctor_project",['' => ''],null,["class" => "form-control time_doctor_project globalSelect2" ,"style" => "width:100%;", 'data-ajax' => route('select2.time_doctor_projects'), 'data-placeholder' => 'Project']); ?>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-default" data-task_id="">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>
<div id="taskGoogleDocModal" class="modal fade" role="dialog" style="display: none;">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Google Doc</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>

            <form action="{{route('google-docs.task')}}" method="POST">
                {{ csrf_field() }}
                <input type="hidden" id="task_id">
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Document type:</strong>

                        <select class="form-control" name="type" required id="doc-type">
                            <option value="spreadsheet">Spreadsheet</option>
                            <option value="doc">Doc</option>
                            <option value="ppt">Ppt</option>
                            <option value="xps">Xps</option>
                            <option value="txt">Txt</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <strong>Name:</strong>
                        <input type="text" name="doc_name" value="" class="form-control input-sm" placeholder="Document Name" required id="doc-name">
                    </div>

                    {{-- <input type="text" name="doc_category" value="" class="form-control input-sm" placeholder="Document Category" required id="doc-category"> --}}
                    {{-- <div class="form-group">
                        <strong>Category:</strong>
                        <select name="doc_category" class="form-control" id="doc-category" required>
                            <option>Select Category</option>
                            @if (isset($googleDocCategory) && count($googleDocCategory) > 0)
                                @foreach ($googleDocCategory as $key => $category)
                                    <option value="{{$key}}">{{$category}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div> --}}
                   
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-secondary" id="btnCreateTaskDocument">Create</button>
                </div>
            </form>
        </div>

    </div>
</div>
<div id="taskGoogleDocListModal" class="modal fade" role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Google Documents list</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered">
                    <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="5%">File Name</th>
                        <th width="5%">Created Date</th>
                        <th width="10%">URL</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<div id="uploadeTaskFileModal" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Upload Screencast/File to Google Drive</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<form action="{{ route('task.upload-file') }}" method="POST" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="task_id" id="upload_task_id">
				<div class="modal-body">						
					<div class="form-group">
						<strong>Upload File</strong>
						<input type="file" name="file[]" id="fileInput" class="form-control input-sm" placeholder="Upload File" style="height: fit-content;" multiple required>
						@if ($errors->has('file'))
							<div class="alert alert-danger">{{$errors->first('file')}}</div>
						@endif
					</div>
					<div class="form-group">
						<strong>File Creation Date:</strong>
						<input type="date" name="file_creation_date" value="{{ old('file_creation_date') }}" class="form-control input-sm" placeholder="Drive Date" required>
					</div>
					<div class="form-group">
							<label>Remarks:</label>
							<textarea id="remarks" name="remarks" rows="4" cols="64" value="{{ old('remarks') }}" placeholder="Remarks" required class="form-control"></textarea>

							@if ($errors->has('remarks'))
								<div class="alert alert-danger">{{$errors->first('remarks')}}</div>
							@endif
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-default">Upload</button>
				</div>
			</form>
		</div>

	</div>
</div>
<div id="displayTaskFileUpload" class="modal fade" role="dialog">
	<div class="modal-dialog modal-xl">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Google Drive Uploaded files</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<div class="modal-body">
				<div class="table-responsive mt-3">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Filename</th>
								<th>File Creation Date</th>
								<th>URL</th>
								<th>Remarks</th>
							</tr>
						</thead>
						<tbody id="taskFileUploadedData">
							
						</tbody>
					</table>
				</div>
			 </div>


		</div>

	</div>
</div>
<div id="record-voice-notes" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Record & Send Voice Message</h4>
            </div>
            <div class="modal-body" >
                <Style>
                    #rvn_status:after {
                        overflow: hidden;
                        display: inline-block;
                        vertical-align: bottom;
                        -webkit-animation: ellipsis steps(4, end) 900ms infinite;
                        animation: ellipsis steps(4, end) 900ms infinite;
                        content: "\2026";
                        /* ascii code for the ellipsis character */
                        width: 0px;
                        }

                        @keyframes ellipsis {
                        to {
                            width: 40px;
                        }
                        }

                        @-webkit-keyframes ellipsis {
                        to {
                            width: 40px;
                        }
                        }
                    </style>
                <input type="hidden" name="rvn_id" id="rvn_id" value="">
                <input type="hidden" name="rvn_tid" id="rvn_tid" value="">
                <button id="rvn_recordButton" class="btn btn-s btn-secondary">Start Recording</button>
                <button id="rvn_pauseButton" class="btn btn-s btn-secondary"disabled>Pause Recording</button>
                <button id="rvn_stopButton" class="btn btn-s btn-secondary"disabled>Stop Recording</button>
                <div id="formats">Format: start recording to see sample rate</div>
                <div id="rvn_status">Status: Not started...</div>
                <div id="recordingsList"></div>
            </div>
            <div class="modal-footer">
                <button type="button" id="rvn-btn-close-modal" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>

    <script src="{{asset('js/bootstrap-multiselect.min.js')}}"></script>
    <script type="text/javascript" src="/js/recorder.js"></script>
    <script type="text/javascript" src="/js/record-voice-notes.js"></script>
    <script>
        function Showactionbtn(id){
            $(".action-btn-tr-"+id).toggleClass('d-none')
        }
    </script>

    <script>
        // function Showactionbtn(id){
        //     $(".action-btn-tr").removeClass('d-none');
        // }

        function funTaskInformationUpdatesTime(type,id) {
            if (type == 'start_date') {
                if (confirm('Are you sure, do you want to update?')) {
                    siteLoader(1);
                    let mdl = funGetTaskInformationModal();
                    jQuery.ajax({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('task.update.start-date') }}",
                        type: 'POST',
                        data: {
                            task_id: id,
                            value: $('input[name="start_dates'+id+'"]').val(),
                        }
                    }).done(function(res) {
                        siteLoader(0);
                        siteSuccessAlert(res);
                    }).fail(function(err) {
                        siteLoader(0);
                        siteErrorAlert(err);
                    });
                }
            } else if (type == 'due_date') {
                if (confirm('Are you sure, do you want to update?')) {
                    siteLoader(1);
                    let mdl = funGetTaskInformationModal();
                    jQuery.ajax({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('task.update.due-date') }}",
                        type: 'POST',
                        data: {
                            task_id: id,
                            value: $('input[name="due_dates'+id+'"]').val(),
                        }
                    }).done(function(res) {
                        siteLoader(0);
                        siteSuccessAlert(res);
                    }).fail(function(err) {
                        siteLoader(0);
                        siteErrorAlert(err);
                    });
                }
            } else if (type == 'cost') {
                if (confirm('Are you sure, do you want to update?')) {
                    siteLoader(1);
                    let mdl = funGetTaskInformationModal();
                    jQuery.ajax({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('task.update.cost') }}",
                        type: 'POST',
                        data: {
                            task_id: id,
                            cost: mdl.find('input[name="cost"]').val(),
                        }
                    }).done(function(res) {
                        siteLoader(0);
                        siteSuccessAlert(res);
                    }).fail(function(err) {
                        siteLoader(0);
                        siteErrorAlert(err);
                    });
                }
            } else if (type == 'approximate') {
                if (confirm('Are you sure, do you want to update?')) {
                    siteLoader(1);
                    let mdl = funGetTaskInformationModal();
                    jQuery.ajax({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('task.update.approximate') }}",
                        type: 'POST',
                        data: {
                            task_id: id,
                            approximate: $('input[name="approximates'+id+'"]').val(),
                            remark: mdl.find('textarea[name="remark"]').val(),
                        }
                    }).done(function(res) {
                        siteLoader(0);
                        siteSuccessAlert(res);
                    }).fail(function(err) {
                        siteLoader(0);
                        siteErrorAlert(err);
                    });
                }
            }
        }

        $(document).ready(function() {
            $(".multiselect").multiselect({
                nonSelectedText: 'Status Filter',
                allSelectedText: 'All',
                includeSelectAllOption: true
            });
        });
        $(document).on('click', '.previewDoc', function() {
            $('#previewDocSource').attr('src', '');
            var docUrl = $(this).data('docurl');
            var type = $(this).data('type');
            var type = jQuery.trim(type);
            if (type == "image") {
                $('#previewDocSource').attr('src', docUrl);
            } else {
                $('#previewDocSource').attr('src', "https://docs.google.com/gview?url=" + docUrl + "&embedded=true");
            }
            $('#previewDoc').modal('show');
        });
        $("#previewDoc").on("hidden", function() {
            $('#previewDocSource').attr('src', '');
        });
        var taskSuggestions = <?php echo json_encode($search_suggestions, true); ?>;
        var searchSuggestions = <?php echo json_encode($search_term_suggestions, true); ?>;
        var cached_suggestions = localStorage['message_suggestions'];
        var suggestions = [];
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2();
        });
        $('#master_user_id').select2({
            width: "100%"
        });
        $('#search_by_user').select2({
            width: "100%"
        });
        // $('.multiselect').select2({
        //     width: "100%"
        // });
        $('#search_by_user').change(function() {
            $("#priority_user_id").select2({
                tags: true,
                width: '100%'
            }).val($(this).val()).trigger('change');
        });
        $(document).ready(function() {
            $('#priority_user_id').select2({
                tags: true,
                width: '100%'
            });
            var isLoading = false;
            var page = 1;
            $(document).ready(function() {
                $(window).scroll(function() {
                    if (($(window).scrollTop() + $(window).outerHeight()) >= ($(document).height() - 2500)) {
                        loadMore();
                    }
                });
                function loadMore() {
                    if (isLoading)
                        return;
                    isLoading = true;
                    type = $("#tasktype").val();
                    var $loader = $('.infinite-scroll-products-loader');
                    page = page + 1;
                    $.ajax({

                        url: "http://localhost/erp/public/index.php/task?page=" + page,
                        type: 'GET',
                        data: $('.form-search-data').serialize(),
                        beforeSend: function() {
                            $loader.show();
                        },
                        success: function(data) {
                            console.log(type);
                            $loader.hide();
                            if ('' === data.trim())
                                return;
                            if (type == 'pending') {
                                $('.infinite-scroll-pending-inner').append(data);
                            }
                            if (type == 'completed') {
                                $('.infinite-scroll-completed-inner').append(data);
                            }
                            if (type == 'statutory_not_completed') {
                                $('.infinite-scroll-statutory-inner').append(data);
                            }
                            isLoading = false;
                        },
                        error: function() {
                            $loader.hide();
                            isLoading = false;
                        }
                    });
                }
            });
            $('#task_reminder_from').datetimepicker({
                format: 'YYYY-MM-DD HH:mm'
            });
            var TaskToRemind = null
            $(document).on('click', '.task-set-reminder', function() {
                let taskId = $(this).data('id');
                let frequency = $(this).data('frequency');
                let message = $(this).data('reminder_message');
                let reminder_from = $(this).data('reminder_from');
                let reminder_last_reply = $(this).data('reminder_last_reply');
                $('#frequency').val(frequency);
                $('#reminder_message').val(message);
                $("#taskReminderModal").find("#task_reminder_from").val(reminder_from);
                if (reminder_last_reply == 1) {
                    $("#taskReminderModal").find("#reminder_last_reply").prop("checked", true);
                } else {
                    $("#taskReminderModal").find("#reminder_last_reply_no").prop("checked", true);
                }
                TaskToRemind = taskId;
            });
            $(document).on('click', '.task-submit-reminder', function() {
                var taskReminderModal = $("#taskReminderModal");
                let frequency = $('#frequency').val();
                let message = $('#reminder_message').val();
                let task_reminder_from = taskReminderModal.find("#task_reminder_from").val();
                let reminder_last_reply = (taskReminderModal.find('#reminder_last_reply').is(":checked")) ? 1 : 0;
                $.ajax({
                    url: "{{action([\App\Http\Controllers\TaskModuleController::class, 'updateTaskReminder'])}}",
                    type: 'POST',
                    success: function() {
                        toastr['success']('Reminder updated successfully!');
                        $(".set-reminder img").css("background-color", "");
                        if (frequency > 0) {
                            $(".task-set-reminder img").css("background-color", "red");
                        }
                    },
                    data: {
                        task_id: TaskToRemind,
                        frequency: frequency,
                        message: message,
                        reminder_from: task_reminder_from,
                        reminder_last_reply: reminder_last_reply,
                        _token: "{{ csrf_token() }}"
                    }
                });
            });
            $(document).on('click', '.btn-call-data', function(e) {
                e.preventDefault();
                var type = $(this).data('type');
                if (type && type != "") {
                    type = $("#tasktype").val(type);
                }

                isLoading = true;
                type = $("#tasktype").val();
                var $loader = $('.infinite-scroll-products-loader');

                page = 1;
                $.ajax({
                    url: "{{url('task')}}",
                    type: 'GET',
                    data: $('.form-search-data').serialize(),
                    beforeSend: function() {
                        $loader.show();
                    },
                    success: function(response) {
                        $loader.hide();
                        if (type == 'pending') {
                            $('.pending-row-render-view').html(response);
                        }
                        if (type == 'statutory_not_completed') {
                            $('.statutory-row-render-view').html(response);
                        }
                        if (type == 'completed') {
                            $('.completed-row-render-view').html(response);
                        }
                        isLoading = false;
                    },
                    error: function() {
                        $loader.hide();
                        isLoading = false;
                    }
                });
            });
            function getPriorityTaskList(id) {
                console.log('id', id)
                var selected_issue = [0];
                $('input[name ="selected_issue[]"]').each(function() {
                    if ($(this).prop("checked") == true) {
                        selected_issue.push($(this).val());
                    }
                });
                $.ajax({
                    url: "{{route('task.list.by.user.id')}}",
                    type: 'POST',
                    data: {
                        user_id: id,
                        _token: "{{csrf_token()}}",
                        selected_issue: selected_issue,
                    },
                    success: function(response) {
                        // var html = '';
                        // response.forEach(function (task) {
                        //     html += '<tr>';
                        //     html += '<td><input type="hidden" name="priority[]" value="' + task.id + '">' + task.id + '</td>';
                        //     html += '<td>' + task.task_subject + '</td>';
                        //     html += '<td>' + task.task_details + '</td>';
                        //     html += '<td>' + task.created_at + '</td>';
                        //     html += '<td>' + task.created_by + '</td>';
                        //     html += '<td><a href="javascript:;" class="delete_priority" data-id="' + task.id + '">Remove<a></td>';
                        //     html += '</tr>';
                        // });
                        $(".show_task_priority").html(response.html);
                        <?php if (auth()->user()->isAdmin()) { ?>
                        $(".show_task_priority").sortable();
                        <?php } ?>
                    },
                    error: function() {
                        alert('There was error loading priority task list data');
                    }
                });
            }
            $(document).on('click', '.delete_priority', function(e) {
                var id = $(this).data('id');
                $('input[value ="' + id + '"]').prop('checked', false);
                $(this).closest('tr').remove();
            });
            $('.priority_model_btn').click(function() {
                //$("#priority_user_id").val('0');
                //$("#sel_user_id").val('0');
                $(".show_task_priority").html('');
                <?php if (auth()->user()->isAdmin()) { ?>
                getPriorityTaskList($('#priority_user_id').val());
                <?php } else { ?>
                getPriorityTaskList('{{auth()->user()->id}}');
                <?php } ?>
                $('#priority_model').modal('show');
            })
            $('#priority_user_id').change(function() {
                $("#sel_user_id").val($(this).val());
                if ($('#priority_model').is(':visible')) {
                    getPriorityTaskList($(this).val())
                }
            });
            $(document).on('submit', '#priorityForm', function(e) {
                console.log($(this).serialize());
                //  return false;
                e.preventDefault();
                <?php if (auth()->user()->isAdmin()) { ?>
                $.ajax({
                    url: "{{route('task.set.priority')}}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        return false;
                        //      toastr['success']('Priority successfully update!!', 'success');
                        //    $('#priority_model').modal('hide');
                    },
                    error: function() {
                        alert('There was error loading priority task list data');
                    }
                });
                <?php } ?>
            });
            $('#task_subject, #task_details').autocomplete({
                source: function(request, response) {
                    var results = $.ui.autocomplete.filter(taskSuggestions, request.term);
                    response(results.slice(0, 10));
                }
            });
            $('#task_search').autocomplete({
                source: function(request, response) {
                    var results = $.ui.autocomplete.filter(searchSuggestions, request.term);
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
        });
        $(document).on('click', '.expand-row-msg', function() {
            var id = $(this).data('id');
            var full = '.expand-row-msg .td-full-container-' + id;
            var mini = '.expand-row-msg .td-mini-container-' + id;
            $(full).toggleClass('hidden');
            $(mini).toggleClass('hidden');
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
        function addNewRemark(id) {
            var formData = $("#add-new-remark").find('#add-remark').serialize();
            // console.log(id);
            var remark = $('#remark-text_' + id).val();
            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('task.addRemark') }}",
                data: {
                    id: id,
                    remark: remark,
                    module_type: "task"
                },
            }).done(response => {
                alert('Remark Added Success!')
                // $('#add-new-remark').modal('hide');
                // $("#add-new-remark").hide();
                window.location.reload();
            });
        }
        $('#completion-datetime, #reminder-datetime, #sending-datetime #due-datetime').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });
        $('.due-datetime').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });
        $('#daily_activity_date').datetimepicker({
            format: 'YYYY-MM-DD'
        });
        let users = <?php echo json_encode($data['users']) ?>;
        let isAdmin = <?php echo $isAdmin ? 1 : 0; ?>;
        $("#add-row").click(function() {
            table.addRow({});
        });
        $(".add-task").click(function() {
            var taskId = $(this).attr('data-id');
            $("#add-new-remark").find('input[name="id"]').val(taskId);
        });
        $(".view-remark").click(function() {
            var taskId = $(this).attr('data-id');
            $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('task.gettaskremark') }}",
                data: {
                    id: taskId,
                    module_type: "task"
                },
            }).done(response => {
                console.log(response);
                var html = '';
                $.each(response, function(index, value) {
                    html += ' <p> ' + value.remark + ' <br> <small>By ' + value.user_name + ' updated on ' + moment(value.created_at).format('DD-M H:mm') + ' </small></p>';
                    html + "<hr>";
                });
                $("#view-remark-list").find('#remark-list').html(html);
                // getActivity();
                //
                // $('#loading_activty').hide();
            });
        });
        $(document).ready(function() {
            $(document).on('change', '.is_statutory', function() {
                if ($(".is_statutory").val() == 1) {
                    // $('input[name="completion_date"]').val("1976-01-01");
                    // $("#completion-datetime").hide();
                    $("#calendar-task").hide();
                    $('#appointment-container').hide();
                    if (!isAdmin)
                        $('select[name="task_asssigned_to"]').html('<option value="${current_userid}">${ current_username }</option>');
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
                    $('select[name="task_asssigned_to"]').html(select_html);
                    $('#recurring-task').hide();
                }
            });
            jQuery('#userList').select2({
                placeholder: 'All user'
            });
            let r_s = '';
            let r_e = "{{ date('y - m - d ') }}";
            let start = r_s ? moment(r_s, 'YYYY-MM-DD') : moment().subtract(6, 'days');
            let end = r_e ? moment(r_e, 'YYYY-MM-DD') : moment();
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
            // var message = $(this).siblings('input').val();
            if ($(this).hasClass("onpriority")) {
                var message = $('#getMsgPopup' + task_id).val();
            } else {
                var message = $('#getMsg' + task_id).val();
            }
            if (message != "") {
                $("#message_confirm_text").html(message);
                $("#confirm_task_id").val(task_id);
                $("#confirm_message").val(message);
                $("#confirm_status").val(1);
                $("#confirmMessageModal").modal();
            }
        });
        $(document).on('click', '.confirm-messge-button', function() {
            var thiss = $(this);
            var data = new FormData();
            var task_id = $("#confirm_task_id").val();
            var message = $("#confirm_message").val();
            var status = $("#confirm_status").val();
            var is_audio=$("#is_audio_"+task_id).val();
            //    alert(message)
            data.append("task_id", task_id);
            data.append("message", message);
            data.append("status", status);
            data.append("is_audio", is_audio);
            // var checkedValue = $('.send_message_recepients:checked').val();
            var checkedValue = [];
            var i = 0;
            $('.send_message_recepients:checked').each(function() {
                checkedValue[i++] = $(this).val();
            });
            data.append("send_message_recepients", checkedValue);
            //  console.log(checkedValue);
            if (message.length > 0) {
                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        //  url: '/whatsapp/sendMessage/task',
                        url: "{{ route('whatsapp.send','task')}}",
                        type: 'POST',
                        "dataType": 'json', // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function() {
                            $(thiss).attr('disabled', true);
                        }
                    }).done(function(response) {
                        $(thiss).siblings('input').val('');
                        $('#getMsg' + task_id).val('');
                        $('#confirmMessageModal').modal('hide');
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
                        $('#confirmMessageModal').modal('hide');
                        $(thiss).attr('disabled', false);
                        alert("Could not send message");
                        console.log(errObj);
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });
        $(document).on('click', '.send-message-lead', function() {
            var thiss = $(this);
            var task_id = $(this).data('taskid');
            var message = $(this).siblings('input').val();
            if (message.length > 0) {
                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        url: '/whatsapp/sendMessage/task_lead',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            task_id: task_id,
                            message: message,
                            status: 2
                        },
                        beforeSend: function() {
                            $(thiss).attr('disabled', true);
                        }
                    }).done(function(response) {
                        console.log(response);
                        $(thiss).siblings('input').val('');
                        $(thiss).attr('disabled', false);
                    }).fail(function(errObj) {
                        console.log(errObj);
                        $(thiss).attr('disabled', false);
                        toastr['error'](errObj.responseJSON.message);
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });
        $(document).on('click', '.expand-row-btn', function() {
            $(this).closest("tr").find(".expand-col").toggleClass('dis-none');
        });
        $(document).on('click', '.expand-row-btn-lead', function() {
           var id =  $(this).data('task_id');
            $(".expand-col-lead"+id).toggleClass('dis-none');
        });
        $(document).on("click", ".set-remark", function(e) {
            $('.remark_pop').val("");
            var task_id = $(this).data('task_id');
            $('.sub_remark').attr("data-task_id", task_id);
        });
        $(document).on("click", ".set-remark, .sub_remark", function(e) {
            var thiss = $(this);
            var task_id = $(this).data('task_id');
            var remark = $('.remark_pop').val();
            if (task_id != "") {
                $.ajax({
                    type: "POST",
                    url: "{{route('task.create.get.remark')}}",
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        task_id: task_id,
                        remark: remark,
                        type: "TASK",
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                    }
                }).done(function(response) {
                    if (response.code == 200) {
                        $("#loading-image").hide();
                        $("#preview-task-create-get-modal").modal("show");
                        $(".task-create-get-list-view").html(response.data);
                        $('.remark_pop').val("");
                        toastr['success'](response.message);
                    } else {
                        $("#loading-image").hide();
                        $("#preview-task-create-get-modal").modal("show");
                        $(".task-create-get-list-view").html("");
                        toastr['error'](response.message);
                    }
                }).fail(function(response) {
                    $("#loading-image").hide();
                    $("#preview-task-create-get-modal").modal("show");
                    $(".task-create-get-list-view").html("");
                    toastr['error'](response.message);
                });
            } else {
                toastr['error']("Task not Found!");
            }
        });
        $(document).on("click", ".copy_remark", function(e) {
            var thiss = $(this);
            var remark_text = thiss.data('remark_text');
            copyToClipboard(remark_text);
            /* Alert the copied text */
            toastr['success']("Copied the text: " + remark_text);
            //alert("Copied the text: " + remark_text);
        });
        function copyToClipboard(text) {
            var sampleTextarea = document.createElement("textarea");
            document.body.appendChild(sampleTextarea);
            sampleTextarea.value = text; //save main text in it
            sampleTextarea.select(); //select textarea contenrs
            document.execCommand("copy");
            document.body.removeChild(sampleTextarea);
        }
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
                console.error("error occured: ", error);
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
                    }).done(function(data) {
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
                var users_array = <?php echo json_encode($users); ?>;
                var leads_assigned_user = "";
                if (current.get(0)) {
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
                    var chat_friend = (message.assigned_to != 0 && message.assigned_to != leads_assigned_user && message.user_id != message.assigned_to) ? ' - ' + users_array[message.assigned_to] : '';
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
                            approveMessage(this, message);
                        });
                        if (is_admin || is_hod_crm) {
                            approveBtn.appendTo(meta);
                            $(editBtn).appendTo(meta);
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
                    if (splitted[0] === "image" || splitted[0] === 'm') {
                        var a = $("<a></a>");
                        a.attr("target", "_blank");
                        a.attr("href", message.media_url);
                        var img = $("<img></img>");
                        img.attr("src", message.media_url);
                        img.attr("width", "100");
                        img.attr("height", "100");
                        img.appendTo(a);
                        a.appendTo(p);
                        // console.log("rendered image message ", a);
                    } else if (splitted[0] === "video") {
                        $("<a target='_blank' href='" + message.media_url + "'>" + message.media_url + "</a>").appendTo(p);
                    }
                }
                var has_product_image = false;
                if (message.images) {
                    var images = '';
                    message.images.forEach(function(image) {
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
                text.appendTo(row);
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
                    $.getJSON("/whatsapp/pollMessagesCustomer" + qs, function(data) {
                        data.data.forEach(function(message) {
                            var rendered = renderMessage(message, tobottom);
                            if (!anyNewMessages && rendered) {
                                anyNewMessages = true;
                            }
                        });
                        if (page) {
                            $('#load-more-messages').text('Load More');
                            can_load_more = true;
                        }
                        if (anyNewMessages) {
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
                setTimeout(function() {
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
            timer = setTimeout(function() {
                if (!prevent) {
                    var task_id = $(thiss).data('id');
                    var image = $(thiss).html();
                    var url = "/task/complete/" + task_id;
                    var current_user = <?php echo Auth::id(); ?>;
                    if (!$(thiss).is(':disabled')) {
                        $.ajax({
                            type: "GET",
                            url: url,
                            data: {
                                type: 'complete'
                            },
                            beforeSend: function() {
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
                            $(thiss).attr('disabled', true);
                            // if (response.task.assign_from != current_user) {
                            //     $(thiss).attr('disabled', true);
                            // }
                        }).fail(function(response) {
                            $(thiss).html(image);
                            alert('Could not mark as completed!');
                            toastr['error'](response.responseJSON.message);
                            console.log(response);
                        });
                    }
                }
                prevent = false;
            }, delay);
        });
        $(document).on('click', '.task-verify', function(e) {
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
                beforeSend: function() {
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
                $(thiss).attr('disabled', true);
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
        $(document).on('click', '#addNoteButton', function() {
            var note_html = `<div class="form-group d-flex">
            <input type="text" class="form-control input-sm" name="note[]" placeholder="Note" value="">
            <button type="button" class="btn btn-image remove-note">x</button>
          </div>`;
            $('#note-container').append(note_html);
        });
        $(document).on('click', '.remove-note', function() {
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
        $('#view_categories_button').on('click', function() {
            $.ajax({
                type: "GET",
                url: "{{ url('task/categories') }}",
                data: {
                    _token: "{{ csrf_token() }}"
                }
            }).done(function(response) {
                console.log(response);
                $('#category-list-area').html(response);
                $('#allTaskCategoryModal').modal();
            }).fail(function(response) {
                console.log("failed");
            });
        });
        $(document).on('click', '.submit-category-status', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            $.ajax({
                type: "POST",
                url: form.attr("action"),
                data: form.serialize(),
            }).done(function(response) {
                toastr["success"](response.message);
                $('#allTaskCategoryModal').modal('hide');
            }).fail(function(response) {});
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
        $("#send_message_button").on("click", function() {
            $("#send-message-text-box").modal("show");
        });
        $(".btn-send-brodcast-message").on("click", function() {
            if (selected_tasks.length > 0) {
                $.ajax({
                    type: "POST",
                    url: "{{ url('tasks/send-brodcast') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        selected_tasks: selected_tasks,
                        message: $(".message-for-brodcast").val()
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                    }
                }).done(function(response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        toastr["success"](response.message);
                        $("#send-message-text-box").modal("hide");
                    } else {
                        toastr["error"](response.message);
                    }
                }).fail(function(response) {
                    $("#loading-image").hide();
                    console.log(response);
                    toastr["error"]("Request has been failed due to the server , please contact administrator");
                });
            } else {
                $("#loading-image").hide();
                toastr["error"]("Please select atleast 1 task!");
            }
        });
        $('#taskCreateButton').on('click', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            var users = $('#multi_users').val();
            var contacts = $('#multi_contacts').val();
            var category = form.find('select[name="category"]').val();
            console.log(users, contacts, category);
            if ($('#taskCreateForm')[0].checkValidity()) {
                if (users.length == 0 && contacts.length == 0) {
                    alert('Please select atleast one user or contact');
                } else {
                    if (category == '1') {
                        alert('Category is required!');
                    } else {
                        $.ajax({
                            type: "POST",
                            beforeSend: function() {
                                $("#loading-image").show();
                            },
                            url: form.attr("action"),
                            data: form.serialize(),
                            dataType: "json"
                        }).done(function(response) {
                            $("#loading-image").hide();
                            toastr["success"](response.message);
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                            if (response.code == 200) {
                                // if (response.statutory == 1) {
                                //     $(".statutory-row-render-view").prepend(response.raw);
                                // } else {
                                //     $(".pending-row-render-view").prepend(response.raw);
                                // }
                            }

                        }).fail(function(response) {
                            console.log(response);
                        });
                        //$('#taskCreateForm').submit();
                    }
                }
            } else {
                $('#taskCreateForm')[0].reportValidity();
            }
        });
        $('#task_category_selection').on('change', function() {
            var category_id = $(this).val();
            var is_approved = $(this).find('option:selected').data('approved');
            var is_admin = "{{ Auth::user()->hasRole('Admin') }}";
            if (is_admin == 1 && !is_approved) {
                $('#approveTaskCategoryButton').parent().removeClass('hidden');
            } else {
                $('#approveTaskCategoryButton').parent().addClass('hidden');
            }
            $('#deleteTaskCategoryButton').attr('data-id', category_id);
            $('#approveTaskCategoryButton').attr('data-id', category_id);
        });
        $('#deleteTaskCategoryButton').on('click', function() {
            var id = $(this).attr('data-id');
            if (id == '') {
                alert('Please select category first');
            } else {
                $.ajax({
                    type: "POST",
                    url: "{{ url('task_category') }}/" + id,
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: "DELETE"
                    }
                }).done(function() {
                    window.location.reload();
                }).fail(function(response) {
                    console.log(response);
                    alert('Could not delete a category');
                });
            }
        });
        $('#approveTaskCategoryButton').on('click', function() {
            var id = $(this).attr('data-id');
            if (id == '') {
                alert('Please select category first');
            } else {
                $.ajax({
                    type: "POST",
                    url: "{{ url('task_category') }}/" + id + '/approve',
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                }).done(function() {
                    window.location.reload();
                }).fail(function(response) {
                    console.log(response);
                    alert('Could not approve a category');
                });
            }
        });
        $(document).on('click', '.whatsapp-group', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            $("#task_id").val(id);
            $("#Preloader").show();
            $.ajax({
                type: "POST",
                async: false,
                url: "{{ route('task.add.whatsapp.group') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                }
            }).done(function(response) {
                console.log(response);
                $("#group_id").val(response.group_id);
                $("#Preloader").hide();
            })
        });
        $(document).on('keyup', '.save-milestone', function(event) {
            if (event.keyCode != 13) {
                return;
            }
            let id = $(this).attr('data-id');
            let total = $(this).val();
            $.ajax({
                url: "{{action([\App\Http\Controllers\TaskModuleController::class, 'saveMilestone'])}}",
                data: {
                    total: total,
                    task_id: id
                },
                success: function() {
                    toastr["success"]("Milestone updated successfully!", "Message")
                },
                error: function(error) {
                    toastr["error"](error.responseJSON.message, "Message")
                    console.log(error.responseJSON.message);
                }
            });
        });
        $(document).on("change", ".select2-task-disscussion", function() {
            var $this = $(this);
            if ($this.val() != 0) {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('task.json.details') }}",
                    data: {
                        task_id: $this.val()
                    },
                    dataType: "json"
                }).done(function(response) {
                    if (response.code == 200) {
                        $("#saveNewNotes").removeClass("dis-none");
                    } else {
                        alert(response.message);
                        $("#saveNewNotes").addClass("dis-none");
                    }
                }).fail(function(response) {
                    alert('Could not update!!');
                });
            } else {
                $("#saveNewNotes").addClass("dis-none");
            }
        });
        $(document).on("click", "#saveNewNotes", function() {
            var $this = $(this);
            $.ajax({
                beforeSend: function() {
                    toastr['info']('Sending data!!', 'info');
                },
                type: 'POST',
                url: "{{ route('task.json.saveNotes') }}",
                data: $("#taskCreateForm").serialize(),
                dataType: "json"
            }).done(function(response) {
                if (response.code == 200) {
                    //toastr['success']('Success!!', 'success');
                    location.reload();
                }
            }).fail(function(response) {
                alert('Could not update!!');
            });
        });
        $(document).on("click", ".delete-task-btn", function() {
            var $this = $(this);
            var taskId = $this.data("id");
            if (taskId > 0) {
                $.ajax({
                    beforeSend: function() {
                        $("#loading-image").show();
                    },
                    type: 'POST',
                    url: "/tasks/deleteTask",
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: taskId
                    },
                    dataType: "json"
                }).done(function(response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        $this.closest("td").remove();
                    }
                }).fail(function(response) {
                    $("#loading-image").hide();
                    alert('Could not update!!');
                });
            }
        });
        $(document).on("click", ".show-finished-task", function() {
            var $this = $(this);
            if ($this.is(":checked")) {
                $this.closest("table").find("tbody tr").hide();
                $this.closest("table").find("tbody tr").filter(function() {
                    return $(this).find('.task-complete img').attr('src') === "/images/completed-green.png";
                }).show();
            } else {
                $this.closest("table").find("tbody tr").show();
            }
        });
        $(document).on('change', '#is_milestone', function() {
            var is_milestone = $('#is_milestone').val();
            if (is_milestone == '1') {
                $('#no_of_milestone').attr('required', 'required');
            } else {
                $('#no_of_milestone').removeAttr('required');
            }
        });
        $(document).on('change', '.assign-master-user', function() {
            let id = $(this).attr('data-id');
            let lead = $(this).attr('data-lead');
            let userId = $(this).val();
            if (userId == '') {
                return;
            }
            $.ajax({
                url: "{{action([\App\Http\Controllers\TaskModuleController::class, 'assignMasterUser'])}}",
                data: {
                    master_user_id: userId,
                    issue_id: id,
                    lead: lead
                },
                success: function() {
                    toastr["success"]("Master User assigned successfully!", "Message")
                },
                error: function(error) {
                    toastr["error"](error.responseJSON.message, "Message")
                }
            });
        });
        $(document).on("click", ".btn-file-upload", function() {
            var $this = $(this);
            var task_id = $this.data("id");
            $("#file-upload-area-section").modal("show");
            $("#hidden-task-id").val(task_id);
            $("#loading-image").hide();
        });
        var uploadedDocumentMap = {}
        Dropzone.options.documentDropzone = {
            url: '{{ route("task.upload-documents") }}',
            maxFilesize: 20, // MB
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function(file, response) {
                $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">')
                uploadedDocumentMap[file.name] = response.name
            },
            removedfile: function(file) {
                file.previewElement.remove()
                var name = ''
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                } else {
                    name = uploadedDocumentMap[file.name]
                }
                $('form').find('input[name="document[]"][value="' + name + '"]').remove()
            },
            init: function() {
            }
        }
        $(document).on("click", ".btn-save-documents", function(e) {
            e.preventDefault();
            var $this = $(this);
            var formData = new FormData($this.closest("form")[0]);
            $.ajax({
                url: '/task/save-documents',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                dataType: "json",
                data: $this.closest("form").serialize(),
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(data) {
                $("#loading-image").hide();
                if (data.code == 500) {
                    toastr["error"](data.message);
                } else {
                    toastr["success"]("Document uploaded successfully");
                    //location.reload();
                }
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                toastr["error"](jqXHR.responseJSON.message);
                $("#loading-image").hide();
            });
        });
        $(document).on('click', '.preview-img-btn', function(e) {
            e.preventDefault();
            id = $(this).data('id');
            if (!id) {
                alert("No data found");
                return;
            }
            $.ajax({
                url: "/task/preview-img/" + id,
                type: 'GET',
                success: function(response) {
                    $("#preview-task-image").modal("show");
                    $(".task-image-list-view").html(response);
                    initialize_select2()
                },
                error: function() {}
            });
        });
        function humanizeDuration(input, units) {
            // units is a string with possible values of y, M, w, d, h, m, s, ms
            var duration = moment().startOf('day').add(units, input),
                format = "";
            if (duration.hour() > 0) {
                format += "H:";
            }
            if (duration.minute() > 0) {
                format += "m:";
            }
            format += "s";
            return duration.format(format);
        }
        $(document).on('click', '.show-tracked-history', function() {
            var issueId = $(this).data('id');
            var type = $(this).data('type');
            $('#time_tracked_div table tbody').html('');
            $.ajax({
                url: "{{ route('task.time.tracked.history') }}",
                data: {
                    id: issueId,
                    type: type
                },
                success: function(data) {
                    console.log(data);
                    if (data != 'error') {
                        $.each(data.histories, function(i, item) {
                            var sec = parseInt(item['total_tracked']);
                            $('#time_tracked_div table tbody').append(
                                '<tr>\
                                    <td>' + moment(item['starts_at_date']).format('DD-MM-YYYY') + '</td>\
                                    <td>' + ((item['name'] != null) ? item['name'] : '') + '</td>\
                                    <td>' + humanizeDuration(sec, 's') + '</td>\
                                </tr>'
                            );
                        });
                    }
                }
            });
            $('#time_tracked_modal').modal('show');
        });
        $(document).on('click', '.create-hubstaff-task', function() {            
            var issueId = $(this).data('id');
            var type = $(this).data('type');
            $("#issueId").val( issueId );
            $("#type").val( type );
            $('#create-d-task-modal').modal('show');

            $(this).css('display', 'none');


            /*$.ajax({
                url: "{{ route('task.create.hubstaff_task') }}",
                type: 'POST',
                data: {
                    id: issueId,
                    type: type,
                    _token: "{{csrf_token()}}"
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(data) {
                    toastr['success']('created successfully!');
                    $("#loading-image").hide();
                },
                error: function() {
                    $("#loading-image").hide();
                    toastr["error"](error.responseJSON.message);
                }
            });*/
        });
        $(document).on("keyup", ".search-category", function() {
            var input, filter, ul, li, a, i, txtValue;
            input = document.getElementById("myInput");
            filter = input.value.toUpperCase();
            ul = document.getElementById("myUL");
            li = ul.getElementsByTagName("li");
            for (i = 0; i < li.length; i++) {
                a = li[i].getElementsByTagName("a")[0];
                txtValue = a.textContent || a.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        });
        $(document).on("click", "#make_complete_button", function() {
            if (selected_tasks.length > 0) {
                var x = window.confirm("Are you sure you want to complete these tasks");
                if (!x) {
                    return;
                }
                $.ajax({
                    type: "POST",
                    url: "{{ url('task/bulk-complete') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        selected_tasks: selected_tasks
                    }
                }).done(function(response) {
                    location.reload();
                }).fail(function(response) {
                    console.log(response);
                    alert('Could not complete tasks');
                });
            } else {
                alert('Please select atleast 1 task!');
            }
        });
        $(document).on("click", "#make_delete_button", function() {
            if (selected_tasks.length > 0) {
                var x = window.confirm("Are you sure you want to bin these tasks");
                if (!x) {
                    return;
                }
                $.ajax({
                    type: "POST",
                    url: "{{ url('task/bulk-delete') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        selected_tasks: selected_tasks
                    }
                }).done(function(response) {
                    location.reload();
                }).fail(function(response) {
                    console.log(response);
                    alert('Could not delete tasks');
                });
            } else {
                alert('Please select atleast 1 task!');
            }
        });
        $(document).on("click", ".delete-single-task", function() {
            var id = $(this).data('id');
            if (!id) {
                return;
            }
            console.log(id);
            selected_tasks.push(id);
            console.log(selected_tasks);
            var x = window.confirm("Are you sure you want to bin these tasks");
            if (!x) {
                return;
            }
            $.ajax({
                type: "POST",
                url: "{{ url('task/bulk-delete') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    selected_tasks: selected_tasks
                }
            }).done(function(response) {
                location.reload();
            }).fail(function(response) {
                console.log(response);
                alert('Could not delete task');
            });
        });
        $(document).on("click", ".link-send-document", function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            var user_id = $(this).closest("tr").find(".send-message-to-id").val();
            var doc_id = $(this).data("media-id");
            $.ajax({
                url: '/task/send-document',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                dataType: "json",
                data: {
                    id: id,
                    user_id: user_id,
                    doc_id: doc_id
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(data) {
                $("#loading-image").hide();
                toastr["success"]("Document sent successfully");
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                toastr["error"](jqXHR.responseJSON.message);
                $("#loading-image").hide();
            });
        });
        $(document).on("click", ".link-send-task", function(e) {
            var id = $(this).data("id");
            var task_id = $(this).data("media-id");
            var taskdata = $(this).parent().find("#selector_id").val();
            console.log(task_id, taskdata);
            var type = $(this).parent().find('#selector_id option[value="' + taskdata + '"]').html().includes('DEVTASK') ? 'DEVTASK' : 'TASK';
            if ($(this).parent().find("#selector_id").val() == '') {
                toastr["error"]('Please Select Task Or DevTask', "Message")
                return false;
            }
            // $(this).parent().find("#selector_id").val(' ').change();
            // $(this).parent().find("#selector_id").html(' ').change();
            // console.log($(this).parent().find("#selector_id").html(), type);
            $.ajax({
                url: '/task/send',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                dataType: "json",
                data: {
                    id: id,
                    task_id: task_id,
                    taskdata: taskdata,
                    type: type
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#loading-image").hide();
                    toastr["success"]("File sent successfully");
                },
                error: function(error) {
                    toastr["error"];
                }
            });
        });
        $(document).on("click", ".send-to-sop-page", function() {
            var id = $(this).data("id");
            var task_id = $(this).data("media-id");
            $.ajax({
                url: '/task/send-sop',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                dataType: "json",
                data: {
                    id: id,
                    task_id: task_id
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#loading-image").hide();
                    toastr["success"]("File Added Successfully In Sop");
                },
                error: function(error) {
                    toastr["error"];
                }
            });
        });
        // on status change
        $(document).on('change', '.change-task-status', function() {
            let id = $(this).attr('data-id');
            let status = $(this).val();
            $.ajax({
                url: "{{route('task.change.status')}}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                dataType: "json",
                data: {
                    'task_id': id,
                    'status': status
                },
                success: function(response) {
                    toastr["success"](response.message, "Message")
                },
                error: function(error) {
                    toastr["error"](error.responseJSON.message, "Message")
                }
            });
        });
        $(document).on('change', '.assign-user', function() {
            let id = $(this).attr('data-id');
            let userId = $(this).val();
            if (userId == '') {
                return;
            }
            $.ajax({
                url: "{{route('task.AssignTaskToUser')}}",
                data: {
                    user_id: userId,
                    issue_id: id
                },
                success: function() {
                    toastr["success"]("User assigned successfully!", "Message")
                },
                error: function(error) {
                    toastr["error"](error.responseJSON.message, "Message")
                }
            });
        });
        $(document).on('click', '.show-user-history', function() {
            var issueId = $(this).data('id');
            $('#user_history_div table tbody').html('');
            $.ajax({
                url: "{{ route('task/user/history') }}",
                data: {
                    id: issueId
                },
                success: function(data) {
                    $.each(data.users, function(i, item) {
                        $('#user_history_div table tbody').append(
                            '<tr>\
                                    <td>' + moment(item['created_at']).format('DD/MM/YYYY') + '</td>\
                                    <td>' + ((item['user_type'] != null) ? item['user_type'] : '-') + '</td>\
                                    <td>' + ((item['old_name'] != null) ? item['old_name'] : '-') + '</td>\
                                    <td>' + ((item['new_name'] != null) ? item['new_name'] : '-') + '</td>\
                                    <td>' + item['updated_by'] + '</td>\
                                </tr>'
                        );
                    });
                }
            });
            $('#user_history_modal').modal('show');
        });
        $(document).on('click', '.cmn-toggle', function() {
            let id = $(this).attr('task-id');
            var showstatus = "";
            $.ajax({
                url: "{{route('task.CommunicationTaskStatus')}}",
                data: {
                    task_id: id
                },
                success: function(response) {
                    if (response.communication_status == 1) {
                        $('#getMsg' + id).prop("readonly", true);
                        $('#sendMsg' + id).prop("readonly", true);
                        showstatus = "Off";
                    }
                    if (response.communication_status == 0) {
                        $('#getMsg' + id).prop("readonly", false);
                        $('#sendMsg' + id).prop("readonly", false);
                        showstatus = "On";
                    }
                    toastr["success"]("Communication message status is " + showstatus + " successfully", "Message")
                },
                error: function(error) {
                    toastr["error"](error.responseJSON.message, "Message")
                }
            });
        });
        $(document).on('click', '.recurring-history-btn', function() {
            var task_id = $(this).data('id');
            console.log(task_id);
            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('task.recurringHistory') }}",
                data: {
                    task_id: task_id,
                },
            }).done(response => {
                $('#recurring-history-modal').find('.recurring-history-list-view').html('');
                if (response.success == true) {
                    $('#recurring-history-modal').find('.recurring-history-list-view').html(response.html);
                    $('#recurring-history-modal').modal('show');
                }
            }).fail(function(response) {
                alert('Could not fetch payments');
            });
        });

        $(document).on('change', '.task_for_modal', function(e) {
            var getTask = $(this).val();
            if(getTask == 'time_doctor'){
                $('.time_doctor_project_section_modal').show();
                $('.time_doctor_account_section_modal').show();
            } else {
                $('.time_doctor_project_section_modal').hide();
                $('.time_doctor_account_section_modal').hide();
            }
        });

        $(document).on('submit', '#assign_task_form', function(event) {
        event.preventDefault();
        $.ajax({
            url: "{{route('task.create.hubstaff_task')}}",
            type: 'POST',
            data: $(this).serialize(),
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(response) {
                toastr['success']('created successfully!');
                $('#create-d-task-modal').modal('hide');
                $("#loading-image").hide();
            },
            error: function(error) {
                toastr["error"](error.responseJSON.message);
                $('#create-d-task-modal').modal('hide');
                $("#loading-image").hide();
            }
        });

    });
        jQuery(document).ready(function() {
            applyDateTimePicker(jQuery('.cls-start-due-date'));
        });
        function applyDateTimePicker(eles) {
            if (eles.length) {
                eles.datetimepicker({
                    format: 'YYYY-MM-DD HH:mm:ss',
                    sideBySide: true,
                });
            }
        }


        $(document).ready(function () {
            $(document).on('click', ".create-task-document", function () {
                let task_id = $(this).data('id');
                if(task_id != "") {
                    $("#task_id").val($(this).data('id'));
                    $("#taskGoogleDocModal").modal('show');
                } else {
                    toastr["error"]("Task id not found.");
                }
            });

            $(document).on('click', ".show-created-task-document", function () {
                let task_id = $(this).data('id');
                if(task_id != "") {
                    $.ajax({
                        type: "GET",
                        url: "{{route('google-docs.task.show')}}",
                        data: {
                            task_id,
                            task_type: "TASK"
                        },
                        beforeSend: function() {
                            $("#loading-image").show();
                            // $("#btnCreateTaskDocument").attr('disabled', true)
                        },
                        success: function (response) {
                            $("#loading-image").hide();
                            $("#taskGoogleDocListModal tbody").html(response.data);
                            $("#taskGoogleDocListModal").modal('show');
                        },
                        error: function(response) {
                            toastr["error"]("Something went wrong!");
                            $("#loading-image").hide();
                        }
                    });
                } else {
                    toastr["error"]("Task id not found.");
                }
            });
            
            $(document).on('click', "#btnCreateTaskDocument", function () {
                let doc_type = $("#doc-type").val();
                let doc_name = $("#doc-name").val();
                // let doc_category = $("#doc-category").val();
                let task_id = $("#task_id").val();
                
                if(doc_type.trim() == "") {
                    toastr["error"]("Select document type.");
                    return
                }
                if(doc_name.trim() == "") {
                    toastr["error"]("Insert document name.");
                    return
                }
                // if(doc_category.trim() == "") {
                //     toastr["error"]("Insert document category.");
                //     return
                // }

                $.ajax({
                    type: "POST",
                    url: "{{route('google-docs.task')}}",
                    data: {
                        _token: "{{csrf_token()}}",
                        // doc_category,
                        doc_type,
                        doc_name,
                        task_id,
                        task_type: "TASK",
                        attach_task_detail: true
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                        $("#btnCreateTaskDocument").attr('disabled', true)
                    },
                    success: function (response) {
                        if(response.status == true) {
                            toastr["success"](response.message);
                        } else {
                            toastr["error"](response.message);
                        }
                        $("#loading-image").hide();
                        $("#btnCreateTaskDocument").removeAttr('disabled')
                        $("#taskGoogleDocModal").modal('hide');
                        $("#doc-type").val(null);
                        $("#doc-name").val(null);
                        $("#doc-category").val(null);
                        $("#task_id").val(null);
                    },
                    error: function(response) {
                        toastr["error"]("Something went wrong!");
                        $("#loading-image").hide();
                        $("#btnCreateTaskDocument").removeAttr('disabled')
                    }
                });

            });

            $(document).on("click", ".upload-task-files-button", function (e) {
                e.preventDefault();
                let task_id = $(this).data("task_id");
                $("#uploadeTaskFileModal #upload_task_id").val(task_id || 0);
                $("#uploadeTaskFileModal").modal("show")
            });

            $(document).on("click", ".view-task-files-button", function (e) {
                e.preventDefault();
                let task_id = $(this).data("task_id");
                $.ajax({
                    type: "get",
                    url: "{{route('task.files.record')}}",
                    data: {
                        task_id
                    },
                    success: function (response) {
                        $("#taskFileUploadedData").html(response.data);
                        $("#displayTaskFileUpload").modal("show")
                    },
                    error: function (response) {
                        toastr['error']("Something went wrong!");
                    }
                });
            });
        });
        $( document ).ready(function() {
            $(document).on('click', '.btn-trigger-rvn-modal',function () {
                var id=$(this).attr('data-id')
                var tid=$(this).attr('data-tid')
                $("#record-voice-notes #rvn_id").val(id);
                $("#record-voice-notes #rvn_tid").val(tid);
                $("#record-voice-notes").modal("show");
            });
            $('#record-voice-notes').on('hidden.bs.modal', function () {
                $("#rvn_stopButton").trigger("click");
                $("#formats").html("Format: start recording to see sample rate");
                $("#rvn_id").val(0);
                $("#rvn_tid").val(0);
                setTimeout(function () {
                    $("#recordingsList").html('');
                }, 2500);
            })
        });
    </script>
@endsection