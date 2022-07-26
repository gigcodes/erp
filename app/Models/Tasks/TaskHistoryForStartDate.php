<?php

namespace App\Models\Tasks;
use Illuminate\Database\Eloquent\Model;

class TaskHistoryForStartDate extends Model {
    public $table = 'task_history_for_start_date';

    public $fillable = [
        'task_id',
        'task_type',
        'updated_by',
        'old_value',
        'new_value'
    ];

    public function updatedBy() {
        return $this->hasOne(\App\User::class, 'id', 'updated_by');
    }
}
