<div>
    <table class="table">
        <thead>
            <tr>
                {{-- <th>#</th> --}}
                <th>Task ID</th>
                <th>Task Details</th>
                <th>Developer</th>
                <th>Estimate Min</th>
                <th>Start Date</th>
                <th>End date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($developerTaskHistory as $task)
                <tr>
                    {{-- <td>{{$loop->iteration}}</td> --}}
                    <td>DEVTASK-{{$task->task_id}}</td>
                    <td>{{$task->task}}</td>
                    <td>{{$task->user->name}}</td>
                    <td>{{$task->estimate_minutes}}</td>
                    <td>{{$task->start_date}}</td>
                    <td>{{$task->estimate_date}}</td>
                    <td>
                        @if (isset($task->is_approved) &&$task->is_approved != 1)
                            <form action="approveEstimateFromshortcut" style="display: inline-block">
                                <button data-task="{{$task->task_id}}" data-id="{{$task->id}}" title="approve" data-type="DEVTASK" class="btn btn-sm approveEstimateFromshortcutButton">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </button>
                            </form>
                        @endif
                        <button class="btn btn-sm estimate-history" title="History" data-task="DEVTASK" data-id="{{$task->task_id}}" onclick="estimateFunTaskDetailHandler(this)">
                            <i class="fa fa-list" aria-hidden="true"></i>
                        </button>
                        {{-- funTaskInformationModal(this, {{$task->task_id }}) --}}
                    </td>
                </tr>
            @endforeach
            @foreach ($t_developerTaskHistory as $task)
                <tr>
                    {{-- <td>{{$loop->iteration}}</td> --}}
                    <td>TASK-{{$task->task_id}}</td>
                    <td>{{ substr($task->task_details, 0,  25) }} {{strlen($task->task_details) > 25 ? '...' : '' }}</td>
                    <td>{{$task->user->name}}</td>
                    <td>{{$task->approximate}}</td>
                    <td>{{$task->start_date}}</td>
                    <td>{{$task->due_date}}</td>
                    <td>
                        @if (isset($task->is_approved) &&$task->is_approved != 1)
                            <form action="approveEstimateFromshortcut" style="display: inline-block">
                                <button data-task="{{$task->task_id}}" data-id="{{$task->id}}" title="approve" data-type="TASK" class="btn btn-sm approveEstimateFromshortcutButton">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </button>
                            </form>
                        @endif
                        <button class="btn btn-sm estimate-history" title="History" data-task="TASK" data-id="{{$task->task_id}}" onclick="estimateFunTaskDetailHandler(this)">
                            <i class="fa fa-list" aria-hidden="true"></i>
                        </button>
                        {{-- funTaskInformationModal(this, {{$task->task_id }}) --}}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>