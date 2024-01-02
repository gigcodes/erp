<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Model;

class TaskHistoryForStartDateApprovals extends Model
{
    public $table = 'task_history_for_start_date_approvals';

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
