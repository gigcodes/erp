<?php

namespace App\Http\Controllers;

use App\CronJob;
use App\CronJobErroLog;
use App\DeveloperModule;
use App\ScheduleQuery;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function storage_path;
use Studio\Totem\Http\Requests\TaskRequest;
use Studio\Totem\Task;
use Studio\Totem\Totem;

class TasksController extends Controller
{
    public function dashboard()
    {
        return redirect()->route('totem.tasks.all');
    }

    public function index()
    {
        return view('totem.tasks.index_new', [
            'tasks' => Task::with('frequencies')
                ->orderBy('description')
                ->when(request('q'), function ($query) {
                    $query->where('description', 'LIKE', '%'.request('q').'%');
                })->when(request('developer_module'), function ($query) {
                    $query->where('developer_module_id', '=', request('developer_module'));
                })->when(request('is_active'), function ($query) {
                    $query->where('is_active', '=', request('is_active'));
                })
                ->paginate(50),
            'task' => null,
            'queries' => ScheduleQuery::all(),
            'developer_module' => DeveloperModule::all(),
            'commands' => Totem::getCommands(),
            'timezones' => timezone_identifiers_list(),
            'frequencies' => Totem::frequencies(),
            'total_tasks' => Task::count(),
        ])->with('i', (request()->input('page', 1) - 1) * 50);
    }

    public function create()
    {
        return view('totem::tasks.form', [
            'task' => new Task,
            'commands' => Totem::getCommands(),
            'timezones' => timezone_identifiers_list(),
            'frequencies' => Totem::frequencies(),
        ]);
    }

    public function store(TaskRequest $request)
    {
        Task::create($request->only([
            'description',
            'command',
            'parameters',
            'timezone',
            'developer_module_id',
//            'type',
            'expression',
//            'frequencies',
            'notification_email_address',
            'notification_phone_number',
            'notification_slack_webhook',
            'dont_overlap',
            'run_in_maintenance',
            'run_on_one_server',
            'auto_cleanup_num',
            'auto_cleanup_type'
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Task Created Successfully.',
        ]);
    }

    public function view(Task $task)
    {
        return response()->json([
            'task' => Task::find($task->id),
            'results' => $task->results->count() > 0 ? number_format($task->results->sum('duration') / (1000 * $task->results->count()), 2) : '0',
        ]);
    }

    public function edit(Task $task)
    {
        return response()->json([
            'task' => $task,
            'commands' => Totem::getCommands(),
            'timezones' => timezone_identifiers_list(),
            'frequencies' => Totem::frequencies(),
        ]);
    }



    public function update(TaskRequest $request, Task $task)
    {
//        dd($task);
//        dd($request->all());
//        $task = Task::update($request->all(), $task);
        $task = Task::where('id', $task->id)->update($request->only([
            'description',
            'command',
            'parameters',
            'timezone',
            'developer_module_id',
//            'type',
            'expression',
//            'frequencies',
            'notification_email_address',
            'notification_phone_number',
            'notification_slack_webhook',
            'dont_overlap',
            'run_in_maintenance',
            'run_on_one_server',
            'auto_cleanup_num',
            'auto_cleanup_type'
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Task Updated Successfully.',
        ]);
    }

    public function destroy($task, Request $request)
    {
        if ($task) {
            $task->delete();

            return response()->json([
                'status' => true,
                'message' => 'Task Deleted Successfully.',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Task Not Found.',
            ]);
        }
    }

    public function status($task, Request $request)
    {
        if ($task) {
            if ($request->active == 1) {
                DB::table('crontasks')->where('id', $task->id)->update([
                    'is_active' => 0,
                ]);
                $msg = 'Task Deactivated Successfully.';
            } else {
                $x = DB::table('crontasks')->where('id', $task->id)->update([
                    'is_active' => 1,
                ]);
                $msg = 'Task Activated Successfully.';
            }

            return response()->json([
                'status' => true,
                'message' => $msg,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Task Not Found.',
            ]);
        }
    }

    public function execute(Task $task)
    {
        File::put(storage_path('tasks.json'), Task::all()->toJson());

        return response()
            ->download(storage_path('tasks.json'), 'tasks.json')
            ->deleteFileAfterSend(true);
    }

    public function developmentTask(Request $request, $task)
    {
        $findTasks = \App\DeveloperTask::where('subject', 'like', '%'.strtoupper($task->command).'%')->latest()->get();

        return view('totem.tasks.partials.development-task-list', compact('findTasks'));
    }

    public function totemCommandError(Request $request, $task)
    {
        $tortem = CronJob::where('id', '=', $task->id)->first();
        $cronError = CronJobErroLog::where('signature', '=', $tortem->signature)->get();

        return response()->json([
            'data' => $cronError,
            'message' => 'Listed successfully!!!',
        ]);

        return $cronError;
    }

    public function queryCommand(Request $request, $name){
        $query = ScheduleQuery::where('schedule_name' , '=', $name)->get()->toArray();
        return $query;
    }
}
