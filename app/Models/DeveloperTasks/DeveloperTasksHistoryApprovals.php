<?php

namespace App\Models\DeveloperTasks;

use Illuminate\Database\Eloquent\Model;

class DeveloperTasksHistoryApprovals extends Model
{
    public $table = 'developer_tasks_history_approvals';

    public $fillable = [
        'parent_id',
        'approved_by',
    ];

    public function approvedBy()
    {
        return $this->hasOne(\App\User::class, 'id', 'approved_by');
    }

    public function approvedByName()
    {
        return optional($this->approvedBy)->name;
    }
}
