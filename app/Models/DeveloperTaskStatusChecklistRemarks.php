<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeveloperTaskStatusChecklistRemarks extends Model
{
    protected $fillable = [
        'user_id',
        'task_id',
        'developer_task_status_checklist_id',
        'remark',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function task()
    {
        return $this->belongsTo(\App\DeveloperTask::class);
    }

    public function taskStatusChecklist()
    {
        return $this->belongsTo(\App\DeveloperTask::class);
    }
}
