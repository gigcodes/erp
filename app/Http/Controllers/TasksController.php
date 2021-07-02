<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Studio\Totem\Task;
use Studio\Totem\Totem;
use Studio\Totem\Contracts\TaskInterface;
use Studio\Totem\Http\Requests\TaskRequest;

class TasksController extends Controller
{
    
    public function dashboard()
    {
        return redirect()->route('totem.tasks.all');
    }  
 
    public function index()
    {
        return view('totem.tasks.index_new', [
            'tasks' => Task::
                orderBy('description')
                ->when(request('q'), function ($query) {
                    $query->where('description', 'LIKE', '%'.request('q').'%');
                })
                ->paginate(20),
        ]);
    } 

    public function create()
    {
        return view('totem::tasks.form', [
            'task'          => new Task,
            'commands'      => Totem::getCommands(),
            'timezones'     => timezone_identifiers_list(),
            'frequencies'   => Totem::frequencies(),
        ]);
    } 

    public function store(TaskRequest $request)
    {
        Task::store($request->all());

        return redirect()
            ->route('totem.tasks.all')
            ->with('success', trans('totem::messages.success.create'));
    } 

    public function view(Task $task)
    {
        return view('totem.tasks.view', [
            'task'  => $task,
        ]);
    }

    public function edit(Task $task)
    {
        return view('totem.tasks.form', [
            'task'          => $task,
            'commands'      => Totem::getCommands(),
            'timezones'     => timezone_identifiers_list(),
            'frequencies'   => Totem::frequencies(),
        ]);
    }
 
    public function update(TaskRequest $request, Task $task)
    {
        $task = Task::update($request->all(), $task);

        return redirect()->route('totem.task.view', $task)
            ->with('task', $task)
            ->with('success', trans('totem.messages.success.update'));
    }
 
    public function destroy(Task $task)
    {
        Task::destroy($task);

        return redirect()
            ->route('totem.tasks.all')
            ->with('success', trans('totem.messages.success.delete'));
    }
}
