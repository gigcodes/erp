<?php

namespace App\Http\Controllers;

use App\Task;
use App\User;
use App\Status;
use App\Setting;
use App\tasktypes;
use App\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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

    // getting remarks
    public function getremarks($taskid)
    {
        $results = DB::select('select * from reamrks where taskid = :taskid', ['taskid' => $taskid]);

        return $results;
    }

    /**
     * function to show the user wise task's statuses counts.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function taskSummary(Request $request)
    {
        $userListWithStatuesCnt = User::select('tasks.id', 'users.id as userid', 'users.name', 'tasks.assign_to', 'tasks.status', DB::raw('(SELECT tasks.created_at from tasks where tasks.assign_to = users.id order by tasks.created_at DESC limit 1) AS created_date'), 'users.name', DB::raw('count(tasks.id) statusCnt'));
        $userListWithStatuesCnt = $userListWithStatuesCnt->join('tasks', 'tasks.assign_to', 'users.id')->where('users.is_task_planned', 1);

        // Code for filter
        //Get all searchable user list
        $userslist = $statuslist = null;
        $filterUserIds = $request->get('users_filter');
        $filterStatusIds = $request->get('status_filter');

        //Get all searchable status list
        if ((int) $filterUserIds > 0 && (int) $filterStatusIds > 0) {
            $userListWithStatuesCnt = $userListWithStatuesCnt->WhereIn('users.id', $filterUserIds)->WhereIn('tasks.status', $filterStatusIds);
            $statuslist = TaskStatus::WhereIn('id', $filterStatusIds)->get();
            $userslist = User::whereIn('id', $filterUserIds)->get();
        } elseif ((int) $filterUserIds > 0) {
            $userListWithStatuesCnt = $userListWithStatuesCnt->WhereIn('users.id', $filterUserIds);
            $userslist = User::whereIn('id', $request->get('users_filter'))->get();
        } elseif ((int) $filterStatusIds > 0) {
            $userListWithStatuesCnt = $userListWithStatuesCnt->WhereIn('tasks.status', $filterStatusIds);
            $statuslist = TaskStatus::WhereIn('id', $filterStatusIds)->get();
        }

        $userListWithStatuesCnt = $userListWithStatuesCnt->groupBy('users.id', 'tasks.assign_to', 'tasks.status')
            ->orderBy('created_date', 'desc')->orderBy('tasks.status', 'asc')
            ->get();
        $getTaskStatus = TaskStatus::get();
        $getTaskStatusIds = TaskStatus::select(DB::raw('group_concat(id) as ids'))->first();
        $arrTaskStatusIds = explode(',', $getTaskStatusIds['ids']);

        $arrStatusCount = [];
        $arrUserNameId = [];
        foreach ($userListWithStatuesCnt as $key => $value) {
            $status = $value['status'];
            $arrStatusCount[$value['userid']][$status] = $value['statusCnt'];
            $arrUserNameId[$value['userid']]['name'] = $value['name'];
            $arrUserNameId[$value['userid']]['userid'] = $value['userid'];
            foreach ($arrTaskStatusIds as $key => $arrTaskStatusIdvalue) {
                if (! array_key_exists($arrTaskStatusIdvalue, $arrStatusCount[$value['userid']])) {
                    $arrStatusCount[$value['userid']][$arrTaskStatusIdvalue] = 0;
                }
            }
            isset($arrStatusCount[$value['userid']]) ? ksort($arrStatusCount[$value['userid']]) : '';
        }

        return view('task-summary.index', compact('userListWithStatuesCnt', 'getTaskStatus', 'arrUserNameId', 'arrStatusCount', 'userslist', 'statuslist'));
    }

    /**
     * function to show all the task list based on specific status and user
     *
     * @param  int  $user_id , $status
     * @return \Illuminate\Http\Response
     */
    public function taskList(Request $request)
    {
        $taskDetails = Task::where('status', $request->taskStatusId)->where('assign_to', $request->userId)->get();

        return response()->json(['data' => $taskDetails]);
    }

    /**
     * Function to get user's name - it's use for lazy loading of users data
     *
     * @return \Illuminate\Http\Response
     */
    public function usersList(Request $request)
    {
        $users = User::orderBy('name');
        if (! empty($request->q)) {
            $users->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
        }
        $users = $users->paginate(30);
        $result['total_count'] = $users->total();
        $result['incomplete_results'] = $users->nextPageUrl() !== null;

        foreach ($users as $user) {
            $result['items'][] = [
                'id' => $user->id,
                'text' => $user->name,
            ];
        }

        return response()->json($result);
    }

    /**
     * Function to get user's name - it's use for lazy loading of users data
     *
     * @return \Illuminate\Http\Response
     */
    public function statusList(Request $request)
    {
        $taskStatus = TaskStatus::orderBy('name');
        if (! empty($request->q)) {
            $taskStatus->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
        }
        $taskStatus = $taskStatus->paginate(30);
        $result['total_count'] = $taskStatus->total();
        $result['incomplete_results'] = $taskStatus->nextPageUrl() !== null;

        foreach ($taskStatus as $status) {
            $result['items'][] = [
                'id' => $status->id,
                'text' => $status->name,
            ];
        }

        return response()->json($result);
    }
}
