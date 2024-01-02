<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Model;

class TaskDueDateHistoryLogApprovals extends Model
{
    public $table = 'task_due_date_history_logs_approvals';

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
