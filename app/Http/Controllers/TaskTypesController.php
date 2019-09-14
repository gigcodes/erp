<?php

namespace App\Http\Controllers;

use App\Notification;
use App\Task;
use App\TasksTypes;
use App\tasktypes;
use App\Status;
use App\Setting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;


class TaskTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $taskTypes = TasksTypes::all();

        return view('task-types.index', compact('taskTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('task-types.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // testing
        $request->merge(array('userid' => Auth::id()));
        $task = $this->validate(request(), [
            'name' => 'required',

        ]);


        $task = TasksTypes::create($task);

        return redirect()->route('taskTypes/create')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $task = Task::find($id);
        $type = New tasktypes;
        $data[ 'task' ] = $type->all();
        $users = User::oldest()->get()->toArray();
        $data[ 'users' ] = $users;
        $status = New status;
        $data[ 'status' ] = $status->all();
        $task[ 'task' ] = $data[ 'task' ];
        $task[ 'status' ] = $data[ 'status' ];
        $task[ 'user' ] = $data[ 'users' ];

        return view('task.edit', compact('task', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     *
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
     * @param  int $id
     *
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
}
