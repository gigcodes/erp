@extends('layouts.app')


@section('content')

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    {{-- <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" /> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

    <div class="row">
        <div class="col-lg-12 text-center">
            <h2> Task & Activity</h2>
        </div>
    </div>
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
         @endif
     {{--   @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
--}}
        <div class="row">
            @can('view-activity')
                <div class="col-md-5 col-12">
                    <h4>User</h4>
                    <form action="{{ route('task.index') }}" method="GET" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <strong>Select User</strong>
                                    <?php
                                    echo Form::select( 'selected_user', $users, $selected_user, [
                                        'class' => 'form-control',
                                        'name'  => 'selected_user'
                                    ] );?>
                                </div>
                            </div>
                            <div class="col-md-4 mt-4">
                                <strong>&nbsp;&nbsp;</strong>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-7 col-12">
                    <h4>Export Task</h4>
                    <form action="{{ route('task.export') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <strong>User</strong>
                                    <?php
                                    echo Form::select( 'selected_user', $users, '' , [
                                        'class'       => 'form-control',
                                        'multiple' => 'multiple',
                                        'id' => 'userList',
                                        'name' => 'selected_user[]',
                                    ] );?>
                                </div>
                            </div>
                            <div class="col-md-4">
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
                            <div class="col-md-4 mt-4">
                                <strong>&nbsp;&nbsp;</strong>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            @endcan
        </div>



            <div class="row">
                <div class="col-12">
                    <h4>Assign Task</h4>
                    <form action="{{ route('task.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <strong>Task Details:</strong>
                             <textarea class="form-control" name="task_details" placeholder="Task Details"> </textarea>
                             @if ($errors->has('task_details'))
                                 <div class="alert alert-danger">{{$errors->first('task_details')}}</div>
                             @endif
                        </div>
                        <div class="form-group">
                          <strong>Completion Date:</strong>
                          <div class='input-group date' id='completion-datetime'>
                            <input type='text' class="form-control" name="completion_date" value="{{ date('Y-m-d H:i') }}" />

                            <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                          </div>

                          @if ($errors->has('completion_date'))
                              <div class="alert alert-danger">{{$errors->first('completion_date')}}</div>
                          @endif
                        </div>
                        {{-- <div id="completion_date" class="form-group">
                            <strong>Completion Date:</strong>
                            <input type='text' class="form-control" name="completion_date" id="completion-datetime" />
                            {{-- <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span> --}}
                            {{-- <input type="datetime-local" name="completion_date" class="form-control" placeholder="Completion Date" value="{{ date('Y-m-d\TH:i') }}" id="completion-datetime">
                            @if ($errors->has('completion_date'))
                                <div class="alert alert-danger">{{$errors->first('completion_date')}}</div>
                            @endif
                        </div> --}}

                        <div class="form-group">
                            <select name="is_statutory" class="form-control is_statutory">
                                <option value="0">Other Task </option>
                                <option value="1">Statutory Task </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <strong>Assigned To:</strong>
                            <select name="assign_to" class="form-control">

                                @foreach($data['users'] as $user)
                                    <option value="{{$user['id']}}">{{$user['name']}}</option>
                                @endforeach
                            </select>

                            @if ($errors->has('assign_to'))
                                <div class="alert alert-danger">{{$errors->first('assign_to')}}</div>
                            @endif
                        </div>

                        <div id="recurring-task" style="display: none;">
                            <div class="form-group">
                                <strong>Recurring Type:</strong>
                                <select name="recurring_type" class="form-control">
                                    <option value="EveryDay">EveryDay</option>
                                    <option value="EveryWeek">EveryWeek</option>
                                    <option value="EveryMonth">EveryMonth</option>
                                    <option value="EveryYear">EveryYear</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <strong>Recurring Day:</strong>
                                <div id="recurring_day"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <strong>Category:</strong>
		                    <?php
		                    $categories = \App\Http\Controllers\TaskCategoryController::getAllTaskCategory();

		                    echo Form::select('category',$categories, ( old('category') ? old('category') : $category ), ['placeholder' => 'Select a category','class' => 'form-control']);

		                    ?>
                        </div>

                     <div class="col-sm-12 text-center">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                    </form>
                </div>
            </div>

            <div class="row">
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
                        <?php $i = 1 ?>
                        @foreach(  $data['task']['statutory_today'] as $task)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td> {{$task['created_at']}}</td>
                                    <td> {{ isset( $categories[$task['category']] ) ? $categories[$task['category']] : '' }}</td>
                                    <td> {{$task['task_details']}}</td>
                                    <td>{{ $users[$task['assign_from']]}}</td>
                                    <td>{{ $task['assign_to'] ? $users[$task['assign_to']] : ''}}</td>
                                    <td> @include('task-module.partials.remark',$task) </td>
                                    <td>
                                        @if( Auth::id() == $task['assign_to'] )
                                            <a href="/task/complete/{{$task['id']}}">Complete</a>
                                        @endif
                                    </td>
                                    <td>
                                      <!--  <form method="POST" action="task/deleteStatutoryTask" enctype="multipart/form-data">
                                            @csrf
                                            <input hidden name="id" value="{{ $task['id'] }}">
                                            <button type="submit" class="">Delete</button>
                                        </form> -->
                                    </td>
                                </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <h4>List Of Pending Tasks</h4>
                <table class="table">
                    <thead>
                      <tr>
                          <th>Sr No</th>
                          <th>Date</th>
                          <th class="category">Category</th>
                          <th>Task Details</th>
                          <th>Est Completion Date</th>
                          <th>Assigned From</th>
                          <th>&nbsp;</th>
                          <th>Remarks</th>
                          <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1 ?>
                      @foreach($data['task']['pending'] as $task)
                    <tr class="{{ \App\Http\Controllers\TaskModuleController::getClasses($task) }}" >
                        <td>{{$i++}}</td>
                         <td>{{$task['created_at']}}</td>
                        <td> {{ isset( $categories[$task['category']] ) ? $categories[$task['category']] : '' }}</td>
                        <td> {{$task['task_details']}}</td>
                        <td> {{$task['completion_date']  }}</td>
                        <td>{{ $users[$task['assign_from']] }}</td>
                        @if( $task['assign_to'] == Auth::user()->id )
                            <td><a href="/task/complete/{{$task['id']}}">Complete</a></td>
                        @else
                            <td>Assign to  {{ $task['assign_to'] ? $users[$task['assign_to']] : 'Nil'}}</td>
                        @endif
                        <td> @include('task-module.partials.remark',$task) </td>
                        <td><!--<button class="delete-task" data-id="{{$task['id']}}">Delete</button>--></td>
                    </tr>
                   @endforeach
                    </tbody>
                  </table>

            </div>
            <div class="row">
                <h4>List Of Completed Tasks</h4>
                <table class="table">
                    <thead>
                      <tr>
                      <th>Sr No</th>
                      <th>Date</th>
                      <th class="category">Category</th>
                      <th>Task Details</th>
                      <th>Est Completion Date</th>
                      <th>Assigned From</th>
                      <th>Assigned To</th>
                      <th>Remark</th>
                      <th>Completed On</th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1 ?>
                      @foreach( $data['task']['completed'] as $task)
                    <tr class="{{ \App\Http\Controllers\TaskModuleController::getClasses($task) }} completed">
                        <td>{{$i++}}</td>
                        <td>{{$task['created_at']}}</td>
                        <td> {{ isset( $categories[$task['category']] ) ? $categories[$task['category']] : '' }}</td>
                        <td> {{$task['task_details']}}</td>
                        <td> {{$task['completion_date']  }}</td>
                        <td>{{$users[$task['assign_from']]}}</td>
                        <td>{{$task['assign_to'] ? $users[$task['assign_to']] : ''}}</td>
                        <td> @include('task-module.partials.remark',$task) </td>
                        <td> {{$task['is_completed']}}</td>
                    </tr>
                   @endforeach
                    </tbody>
                  </table>
            </div>

           <!-- <div class="row">
                <h4>List Of Deleted Tasks</h4>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Date</th>
                        <th class="category">Category</th>
                        <th>Task Details</th>
                        <th>Comment</th>
                        {{--<th>Est Completion Date</th>--}}
                        <th>Deleted On</th>
                    </tr>
                    </thead>
                    <tbody>
			        <?php $i = 1 ?>
                    @foreach( $data['task']['deleted'] as $task)
                        <tr>
                            <td>{{$i++}}</td>
                            <td>{{$task['created_at']}}</td>
                            <td> {{ isset( $categories[$task['category']] ) ? $categories[$task['category']] : '' }}</td>
                            <td> {{$task['task_details']}}</td>
                            <td> {{$task['remark']}}</td>
                            {{--<td> {{$task['completion_date']  }}</td>--}}
                            <td> {{$task['deleted_at']}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div> -->

	        <?php
	        if ( \App\Helpers::getadminorsupervisor() && ! empty( $selected_user ) )
		        $isAdmin = true;
	        else
		        $isAdmin = false;
	        ?>

            <div class="row">
                <div class="col-12">
                    <h4>Daily Activity</h4>

                    <div class="mt-2 mb-2">
                        @if(!$isAdmin)
                            <button id="add-row" class="btn btn-primary">Add Row</button>
                        @endif
                        <button id="save-activity" class="btn btn-primary">Save</button>
                        <img id="loading_activty" style="display: none" src="{{ asset('images/loading.gif') }}"/>
                    </div>

                    <div id="daily_activity"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <h4>Statutory Activity Completed</h4>
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
                          <th>Completed at</th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1 ?>
                      @foreach(  $data['task']['statutory_completed'] as $task)
                    <tr>
                        <td>{{$i++}}</td>
                        <td> {{$task['created_at']}}</td>
                        <td> {{ isset( $categories[$task['category']] ) ? $categories[$task['category']] : '' }}</td>
                        <td> {{$task['task_details']}}</td>
                        <td>{{$users[$task['assign_from']]}}</td>
                        <td>{{$task['assign_to'] ? $users[$task['assign_to']] : ''}}</td>
                        <td> @include('task-module.partials.remark',$task) </td>
                        <td> {{$task['created_at']}}</td>
                    </tr>
                   @endforeach
                    </tbody>
                  </table>
                </div>
            </div>

            <div class="row">
        <div class="col-12">
            <h4>All Statutory Activity List</h4>
            <table class="table">
                <thead>
                <tr>
                    <th>Sr No</th>
                    <th>Date</th>
                    <th class="category">Category</th>
                    <th>Task Details</th>
                    <th>Assigned From</th>
                    <th>Assigned To</th>
                    <th>Recurring Type</th>
                    <th>Completed</th>
                    {{--<th>Remark</th>--}}
                    {{--<th>Completed</th>--}}
                    {{--<th style="width: 80px;">Action</th>--}}
                </tr>
                </thead>
                <tbody>
			    <?php $i = 1 ?>
                @foreach(  $data['task']['statutory'] as $task)
                        <tr>
                            <td>{{$i++}}</td>
                            <td> {{$task['created_at']}}</td>
                            <td> {{ isset( $categories[$task['category']] ) ? $categories[$task['category']] : '' }}</td>
                            <td> {{$task['task_details']}}</td>
                            <td>{{ $users[$task['assign_from']]}}</td>
                            <td>{{ $task['assign_to'] ? $users[$task['assign_to']] : ''}}</td>
                            <td>{{ $task['recurring_type'] }}</td>
                            {{-- <td>{{ $task['recurring_day'] ?? 'nil' }}</td> --}}
                            <td>
                              @if( Auth::id() == $task['assign_to'] )
                                @if ($task['completion_date'])
                                  {{ $task['completion_date'] }}
                                @else
                                  <a href="/statutory-task/complete/{{$task['id']}}">Complete</a>
                                @endif
                              @endif
                            </td>
                            {{--<td>
                                <form method="POST" action="task/deleteStatutoryTask" enctype="multipart/form-data">
                                    @csrf
                                    <input hidden name="id" value="{{ $task['id'] }}">
                                    <button type="submit" class="">Delete</button>
                                </form>
                            </td>--}}
                        </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

    <script>

        $('#completion-datetime').datetimepicker({
          format: 'YYYY-MM-DD HH:mm'
        });

        let users = {!! json_encode( $data['users'] ) !!};

        let isAdmin = {{ $isAdmin ? 1 : 0}};

        let table = new Tabulator("#daily_activity", {
            height: "311px",
            layout: "fitColumns",
            resizableRows: true,
            columns: [
                {
                    title: "Time",
                    field: "time_slot",
                    editor: "select",
                    editorParams: {
                        '12:00am - 01:00am': '12:00am - 01:00am',
                        '01:00am - 02:00am': '01:00am - 02:00am',
                        '02:00am - 03:00am': '02:00am - 03:00am',
                        '03:00am - 04:00am': '03:00am - 04:00am',
                        '04:00am - 05:00am': '04:00am - 05:00am',
                        '05:00am - 06:00am': '05:00am - 06:00am',
                        '06:00am - 07:00am': '06:00am - 07:00am',
                        '07:00am - 08:00am': '07:00am - 08:00am',

                        '08:00am - 09:00am': '08:00am - 09:00am',
                        '09:00am - 10:00am': '09:00am - 10:00am',
                        '10:00am - 11:00am': '10:00am - 11:00am',
                        '11:00am - 12:00pm': '11:00am - 12:00pm',
                        '12:00pm - 01:00pm': '12:00pm - 01:00pm',
                        '01:00pm - 02:00pm': '01:00pm - 02:00pm',
                        '02:00pm - 03:00pm': '02:00pm - 03:00pm',
                        '03:00pm - 04:00pm': '03:00pm - 04:00pm',
                        '04:00pm - 05:00pm': '04:00pm - 05:00pm',
                        '05:00pm - 06:00pm': '05:00pm - 06:00pm',
                        '06:00pm - 07:00pm': '06:00pm - 07:00pm',
                        '07:00pm - 08:00pm': '07:00pm - 08:00pm',

                        '08:00pm - 09:00pm': '08:00pm - 09:00pm',
                        '09:00pm - 10:00pm': '09:00pm - 10:00pm',
                        '10:00pm - 11:00pm': '10:00pm - 11:00pm',
                        '11:00pm - 12:00am': '11:00pm - 12:00am',
                    },
                    editable: !isAdmin
                },
                {title: "Activity", field: "activity", editor: "textarea", formatter:"textarea", editable: !isAdmin},
                {title: "Assessment", field: "assist_msg", editor: "input", editable: !!isAdmin, visible: !!isAdmin},
                {title: "id", field: "id", visible: false},
                {title: "user_id", field: "user_id", visible: false},
            ],
        });

        $("#add-row").click(function () {
            table.addRow({});
        });


        $("#save-activity").click(function () {

            $('#loading_activty').show();
            console.log(table.getData());

            let data = [];

            if (isAdmin) {
                data = deleteKeyFromObjectArray(table.getData(), ['time_slot', 'activity']);
            }
            else {
                data = deleteKeyFromObjectArray(table.getData(), ['assist_msg']);
            }

            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('dailyActivity.store') }}',
                data: {
                    activity_table_data: encodeURI(JSON.stringify(data)),
                },
            }).done(response => {
                console.log(response);
                getActivity();

                $('#loading_activty').hide();
            });
        });

        function deleteKeyFromObjectArray(data, key) {

            let newData = [];

            for (let item of data) {

                for (let eachKey of key)
                    delete  item[eachKey];

                newData = [...newData, item];
            }

            return newData;
        }

        function getActivity() {
            $.ajax({
                type: 'GET',
                data :{
                    selected_user : '{{ $selected_user }}',
                },
                url: '{{ route('dailyActivity.get') }}',
            }).done(response => {
                table.setData(response);
                setTimeout(getActivity, interval_daily_activtiy);
            });
        }

        getActivity();
        let interval_daily_activtiy = 1000*600;  // 1000 = 1 second
        setTimeout(getActivity, interval_daily_activtiy);


        $(document).ready(function() {
            $(document).on('change', '.is_statutory', function () {


                if ($(".is_statutory").val() == 1) {

                    $('input[name="completion_date"]').val("1976-01-01");
                    $("#completion_date").hide();

                    if (!isAdmin)
                        $('select[name="assign_to"]').html(`<option value="${current_userid}">${ current_username }</option>`);

                    $('#recurring-task').show();
                }
                else {

                    $("#completion_date").show();

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

    </script>

@endsection
