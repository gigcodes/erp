<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Model;

class TaskHistoryForCost extends Model
{
    public $table = 'task_history_for_cost';

    public $fillable = [
        'task_id',
        'task_type',
        'old_value',
        'new_value',
        'updated_by',
    ];

    public function updatedBy()
    {
        return $this->hasOne(\App\User::class, 'id', 'updated_by');
    }
}
