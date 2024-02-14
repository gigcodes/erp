<?php

namespace App\Http\Controllers;

use App\TaskTypes;
use Illuminate\Http\Request;

class TaskTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all task types
        $taskTypes = TaskTypes::all();

        // Return task types index
        return view('task-types.index', compact('taskTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Return form
        return view('task-types.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request
        $task = $this->validate(request(), [
            'name' => 'required',

        ]);

        // Create the task
        $task = TaskTypes::create($task);

        return redirect()->route('task-types.index')
            ->with('success', 'Task created successfully.');
    }
}
