<?php

namespace App\Models;

use App\Task;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskHubstaffCreateLog extends Model
{
    use HasFactory;

    protected $table = 'task_hubstaff_create_logs';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
