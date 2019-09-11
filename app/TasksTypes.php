<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TasksTypes extends Model
{
    protected $table = 'task_types';

    protected $fillable = [
        'name'
    ];
}
