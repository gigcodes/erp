<?php

namespace App\Http\Controllers;

use App\DeveloperTask;
use Illuminate\Http\Request;

class NewDevTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dev_task = DeveloperTask::all();
        return view('new_dev_task_planner.index', compact(
            'dev_task'
        ));
    }
}
