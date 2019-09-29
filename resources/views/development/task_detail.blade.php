@extends('layouts.app')

@section('content')
    <style>
        .task {
            border-bottom: 1px solid #e4e5e6;
            margin-bottom: 1px;
            position: relative;
        }
        .task .desc {
            display: inline-block;
            width: 75%;
            padding: 10px 10px;
            font-size: 16px;
        }
        .task .time {
            display: inline-block;
            width: 15%;
            padding: 10px 10px 10px 0;
            font-size: 12px;
            text-align: right;
            position: absolute;
            top: 0;
            right: 0;
        }
    </style>

    @if($task->priority == 1)
        <?php $task_type = 'Normal';
            $bg_color = 'background: green;'; $priority_clr = 'color:green';?>
    @elseif($task->priority == 2)
        <?php $task_type = 'Urgent'; $bg_color = 'background: orange;'; $priority_clr = 'color:orange';?>

    @elseif($task->priority == 3)
        <?php $task_type = 'Critical';$bg_color = 'background: red;'; $priority_clr = 'color:red'; ?>

    @endif
<div class="content" style="margin-top: 10px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card-box task-detail">
                    <div class="media mt-0 m-b-30">
                        <div class="media-body">
                            <h4 class="media-heading mb-0 mt-0">{{$task->task_type .'-'.$task->id}}
                                <span class="badge badge-danger" style="{{$bg_color}}">{{$task_type}}</span>
                            </h4>
                        </div>
                    </div>
                    <h2 class="m-b-20">{{ucfirst($task->subject)}}</h2>
                    <p class="text-muted">{{$task->task}}</p>

                    <div class="clearfix"></div>
                    <div class="sub_tasks" style="background: #ccc;padding: 8px;border-radius: 5px;">
                        <div class="task-list">
                            <h3>Subtasks <span style="float: right;font-size: 16px;"><a href="javascript:" id="create_subtask_link">+ Create SubTasks</a></span></h3>
                            <div class="add_subtask_div" style="display: none;">
                                <input type="text" id="subtask_detail" name="subtask" class="form-control input-sm" placeholder="What needs to be done?" style="padding: 20px;">
                                <button type="button" id="subtask_create">Create</button>
                                <input type="hidden" id="task_id" value="{{$task->id}}">
                            </div>
                            
                            @if(!empty($subtasks) && count($subtasks) > 0)
                                @foreach($subtasks as $subtask)
                                    <div class="task high" style="background: white;">
                                        <div class="desc">
                                            <div class="title">{{$subtask->task}}</div>
                                        </div>
                                        <div class="time">
                                            <div class="date">Todo</div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div style="text-align: center;"> No Subtask yet</div>
                            @endif
                        </div>
                    </div>

                    <div class="linked_issues" style="background: #ccc;padding: 8px;border-radius: 5px;margin-top: 12px;">
                        <div class="task-list">
                            <h3>Linked Issues</h3>
                            <div class="task high" style="background: white;">
                                <div class="desc">
                                    <div class="title">Update Documentation on developer</div>
                                    {{--                                    <div>Update Documentation</div>--}}
                                </div>
                                <div class="time">
                                    <div class="date">Jun 1, 2012</div>
                                </div>
                            </div>

                        </div>
                    </div>
{{--                    <div class="task-tags mt-4">--}}
{{--                        <h5 class="">Tags</h5>--}}
{{--                        <div class="bootstrap-tagsinput"><span class="tag label label-info">Amsterdam<span data-role="remove"></span></span> <span class="tag label label-info">Washington<span data-role="remove"></span></span> <span class="tag label label-info">Sydney<span data-role="remove"></span></span>--}}

{{--                        </div>--}}

{{--                    </div>--}}
                    <h3>Activity</h3>
                    <div class="dev_comments">
                        <div class="media m-b-20">
                            <div class="d-flex mr-3">
                                <a href="#"><img class="media-object rounded-circle thumb-sm" alt="64x64" src="https://bootdey.com/img/Content/avatar/avatar1.png"></a>
                            </div>
                            <div class="media-body">
                                <h5 class="mt-0">Maxine Kennedy</h5>
                                <p class="font-13 text-muted mb-0"><a href="" class="text-dark">@Michael</a> Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras purus odio.</p>
                            </div>
                        </div>
                    </div>
                    <div class="media m-b-20">
                        <div class="d-flex mr-3"> <a href="#"><img class="media-object rounded-circle thumb-sm" alt="64x64" src="https://bootdey.com/img/Content/avatar/avatar1.png"></a></div>
                        <div class="media-body">
                            <input type="text" id="comment" class="form-control input-sm" placeholder="Some text value...">
                            <div class="mt-2 text-right"> <button type="button" class="btn btn-sm btn-custom waves-effect waves-light" id="add_comment">Send</button></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->
            <div class="col-lg-4">
                <div class="card-box">
                    <h4 class="header-title m-b-30">Status</h4>
                    <div class="task_status">
                        <select name="task_status" class="form-control">
                            <option value="Planned" {{ ($task->status == 'Planned') ? 'selected' : '' }}>Planned</option>
                            <option value="In Progress" {{ ($task->status == 'In Progress') ? 'selected' : '' }}>In Progress</option>
                            <option value="Done" {{ ($task->status == 'Done') ? 'selected' : '' }}>Done</option>
                        </select>
                    </div>
                    <div class="assignee">
                        <h4 class="header-title m-b-30">Assignee</h4>
                        <div class="media m-b-20">
                            <div class="d-flex mr-3">
                                <img class="media-object rounded-circle thumb-sm" alt="64x64" src="https://bootdey.com/img/Content/avatar/avatar2.png">
                            </div>
                            <div class="media-body" style="margin-top: 7px;">
                                <h4 class="mt-0">{{$task->username}}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="assignee">
                        <h4 class="header-title m-b-30">Reporter</h4>
                        <div class="media m-b-20">
                            <div class="d-flex mr-3">
                                <img class="media-object rounded-circle thumb-sm" alt="64x64" src="https://bootdey.com/img/Content/avatar/avatar2.png">
                            </div>
                            <div class="media-body" style="margin-top: 7px;">
                                <h4 class="mt-0">James</h4>
                            </div>
                        </div>
                    </div>
                    <div class="priority">
                        <h4 class="header-title m-b-30">Priority</h4>
                        <h4 style="{{$priority_clr}}">{{$task_type}}</h4>
                    </div>
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- container -->
</div>

@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).on('click', '#create_subtask_link', function () {
            $(".add_subtask_div").toggle();
        });

        $(document).on('click', '#subtask_create', function () {
            var task_detail = $("#subtask_detail").val();
            var taskId      = $("#task_id").val();
            if(task_detail != '') {
                $.ajax({
                    url: "{{ action('DevelopmentController@store') }}",
                    type: 'POST',
                    data: {
                        parent_id: taskId,
                        _token: "{{csrf_token()}}",
                        task: task_detail,
                        priority:'1',
                        status : 'Planned',


                    },
                    success: function () {
                        $("#subtask_detail").val('');
                        $(".add_subtask_div").hide();
                        toastr['success']('Subtask Added successfully!')
                    }
                });
            }else{
                toastr['error']('Task Detail is empty','Error');
            }
        });


        $(document).on('click', '#add_comment', function () {
            comment
            var comment = $("#comment").val();

            if(comment != '') {

            }else{
                toastr['error']('Comment is empty','Error');
            }
        });
    </script>
@endsection
