<?php

namespace App\Http\Controllers;

use App\Setting;
use App\Status;
use App\Task;
use App\tasktypes;
use App\User;
use App\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        if ((\Auth::user()->hasRole(['Admin', 'Supervisors']))) {
            $task = Task::oldest()->whereNull('deleted_at')->paginate(Setting::get('pagination'));
        } else {
            $task = Task::oldest()->whereNull('deleted_at')->where('userid', '=', Auth::id())->orWhere('assigned_user', '=', Auth::id())->paginate(Setting::get('pagination'));
        }

        return view('task.index', compact('task'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $type = new tasktypes;
        $data['task'] = $type->all();
        $users = User::oldest()->get()->toArray();
        $data['users'] = $users;
        $status = new status;
        $data['status'] = $status->all();

        return view('task.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->merge(['userid' => Auth::id()]);
        $task = $this->validate(request(), [
            'name' => 'required',
            'details' => 'required',
            'type' => 'required',
            'related' => '',
            'assigned_user' => 'required',
            'remark' => '',
            'minutes' => '',
            'comments' => '',
            'status' => '',
            'userid' => '',

        ]);

        $task = Task::create($task);

        //Send to the assigned user
        // NotificationQueueController::createNewNotification([
        // 	'message' => $task->details,
        // 	'timestamps' => ['+0 minutes','+15 minutes','+30 minutes','+45 minutes'],
        // 	'model_type' => Task::class,
        // 	'model_id' =>  $task->id,
        // 	'user_id' => Auth::id(),
        // 	'sent_to' => $task->assigned_user,
        // 	'role' => '',
        // ]);

        //Send to the author if not done.
        // NotificationQueueController::createNewNotification([
        // 	'message' => $task->details,
        // 	'timestamps' => ['+45 minutes'],
        // 	'model_type' => Task::class,
        // 	'model_id' =>  $task->id,
        // 	'user_id' => Auth::id(),
        // 	'sent_to' => Auth::id(),
        // 	'role' => '',
        // ]);

        return redirect()->route('task.create')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $task = Task::find($id);
        $type = new tasktypes;
        $data['task'] = $type->all();
        $users = User::oldest()->get()->toArray();
        $data['users'] = $users;
        $status = new status;
        $data['status'] = $status->all();
        $task['task'] = $data['task'];
        $task['status'] = $data['status'];
        $task['user'] = $data['users'];

        return view('task.edit', compact('task', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $task = Task::find($id);
        $this->validate(request(), [
            'name' => 'required',
            'details' => 'required',
            'type' => 'required',
            'related' => '',
            'assigned_user' => 'required',
            'remark' => '',
            'minutes' => '',
            'comments' => '',
            'status' => '',
            'userid' => '',

        ]);

        // if ( $request->input( 'assigned_user' ) != $task->assigned_user ) {
        //
        // 	//Send to the assigned user
        // 	NotificationQueueController::createNewNotification([
        // 		'message' => $task->details,
        // 		// 'timestamps' => ['+0 minutes','+15 minutes','+30 minutes','+45 minutes'],
        // 		'timestamps' => ['+0 minutes'],
        // 		'model_type' => Task::class,
        // 		'model_id' =>  $task->id,
        // 		'user_id' => Auth::id(),
        // 		'sent_to' => $request->input( 'assigned_user' ),
        // 		'role' => '',
        // 	]);
        //
        // 	//Send to the author if not done.
        // 	// NotificationQueueController::createNewNotification([
        // 	// 	'message' => $task->details,
        // 	// 	'timestamps' => ['+45 minutes'],
        // 	// 	'model_type' => Task::class,
        // 	// 	'model_id' =>  $task->id,
        // 	// 	'user_id' => Auth::id(),
        // 	// 	'sent_to' => Auth::id(),
        // 	// 	'role' => '',
        // 	// ]);
        // }

        $task->name = $request->get('name');
        $task->details = $request->get('details');
        $task->type = $request->get('type');
        $task->related = $request->get('related');
        $task->assigned_user = $request->get('assigned_user');
        $task->remark = $request->get('remark');
        $task->minutes = $request->get('minutes');
        $task->status = $request->get('status');
        $task->userid = $request->get('userid');

        $task->save();

        return redirect()->route('task.index')
            ->with('success', 'Task Updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getremarks($taskid)
    // getting remarks
    {
        $results = DB::select('select * from reamrks where taskid = :taskid', ['taskid' => $taskid]);

        return $results;
    }

    /**
     * function to show the user wise task's statuses counts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function taskSummary()
    {
        $users = User::select('tasks.id','users.id as userid','users.name', 'tasks.assign_to', 'tasks.status', DB::raw('(SELECT tasks.created_at from tasks where tasks.assign_to = users.id order by tasks.created_at DESC limit 1) AS created_date'), 'users.name', DB::raw('count(tasks.id) statusCnt'))
            ->join('tasks', 'tasks.assign_to', 'users.id')
            ->where('users.is_task_planned', 1)
            ->groupBy('users.id','tasks.assign_to', 'tasks.status')
            ->orderBy('created_date', 'desc')->orderBy('tasks.status', 'asc')
            ->get();
        $taskStatus = TaskStatus::get();
        $taskStatusIds = TaskStatus::select(DB::raw("group_concat(id) as ids"))->first();
        $arrTaskStatusId = explode(',', $taskStatusIds['ids']);

        $arrStatusCount = [];
        $arrName = [];
        foreach ($users as $key => $value) {
            $status = $value['status'];
            $arrStatusCount[$value['userid']][$status] = $value['statusCnt'];
            $arrName[$value['userid']]['name'] = $value['name'];
            $arrName[$value['userid']]['userid'] = $value['userid'];
            foreach ($arrTaskStatusId as $key => $arrTaskStatusIdvalue) {
                if(!array_key_exists($arrTaskStatusIdvalue, $arrStatusCount[$value['userid']]))
                {
                    $arrStatusCount[$value['userid']][$arrTaskStatusIdvalue] = 0;
                }
            }
            isset( $arrStatusCount[$value['userid']]) ? ksort($arrStatusCount[$value['userid']]) : '';
        }
        return view('task-summary.index', compact('users', 'taskStatus','arrName', 'arrStatusCount'));
    }

    /**
     * function to show all the task list based on specific status and user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $user_id, $status
     * @return \Illuminate\Http\Response
     */
    public function taskList(Request $request)
    {
        $taskDetails = Task::where('status', $request->taskStatusId)->where('assign_to', $request->userId)->get();
        return response()->json(['data' => $taskDetails]);
    }
}
