<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TasksHistory extends Model
{

    protected $table = 'tasks_history';

    protected $fillable = [
        'name'
    ];
}
