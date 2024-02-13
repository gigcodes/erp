<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Studio\Totem\Task;

class ExecuteTasksController extends Controller
{
    public function index()
    {
        File::put(storage_path('tasks.json'), Task::findAll()->toJson());

        return response()
            ->download(storage_path('tasks.json'), 'tasks.json')
            ->deleteFileAfterSend(true);
    }
}
