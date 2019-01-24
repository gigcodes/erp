<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DeveloperTask;
use App\User;
use App\Helpers;
use App\Issue;
use Auth;

class DevelopmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function __construct() {
     		$this->middleware('permission:developer-tasks', ['except' => ['issueCreate', 'issueStore']]);
     	}

    public function index(Request $request)
    {
      $user = $request->user ? $request->user : Auth::id();
      $tasks = DeveloperTask::where('user_id', $user)->orderBy('priority')->get();
      $users = Helpers::getUserArray(User::all());

      return view('development.index', [
        'tasks' => $tasks,
        'users' => $users,
        'user'  => $user
      ]);
    }

    public function issueIndex()
    {
      $issues = Issue::all();
      $users = Helpers::getUserArray(User::all());

      return view('development.issue', [
        'issues'  => $issues,
        'users'   => $users
      ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function issueCreate()
    {
      return view('development.issue-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
        'priority'  => 'required|integer',
        'task'      => 'required|string|min:3',
        'cost'      => 'sometimes||nullable|integer',
        'status'    => 'required'
      ]);

      $data = $request->except('_token');
      $data['user_id'] = $request->user_id ? $request->user_id : Auth::id();

      DeveloperTask::create($data);

      return redirect()->route('development.index')->with('success', 'You have successfully added task!');
    }

    public function issueStore(Request $request)
    {
      $this->validate($request, [
        'priority'  => 'required|integer',
        'issue'     => 'required|min:3'
      ]);

      $data = $request->except('_token');

      Issue::create($data);

      return redirect()->back()->with('success', 'You have successfully submitted an issue!');
    }

    public function issueAssign(Request $request, $id)
    {
      $this->validate($request, [
        'user_id' => 'required|integer'
      ]);

      $issue = Issue::find($id);
      $task = new DeveloperTask;

      $task->priority = $issue->priority;
      $task->task = $issue->issue;
      $task->user_id = $request->user_id;
      $task->status = 'To-do';

      $task->save();
      $issue->user_id = $request->user_id;
      $issue->save();
      $issue->delete();

      return redirect()->back()->with('success', 'You have successfully assigned the issue!');
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
      $this->validate($request, [
        'priority'  => 'required|integer',
        'task'      => 'required|string|min:3',
        'cost'      => 'sometimes||nullable|integer',
        'status'    => 'required'
      ]);

      $data = $request->except('_token');
      $data['user_id'] = $request->user_id ? $request->user_id : Auth::id();

      DeveloperTask::find($id)->update($data);

      return redirect()->route('development.index')->with('success', 'You have successfully updated task!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      DeveloperTask::find($id)->delete();

      return redirect()->route('development.index')->with('success', 'You have successfully archived the task!');
    }

    public function issueDestroy($id)
    {
      Issue::find($id)->delete();

      return redirect()->route('development.issue.index')->with('success', 'You have successfully archived the issue!');
    }
}
