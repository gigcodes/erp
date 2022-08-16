<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskDueDateHistoryLog extends Model {
	protected $fillable = ['task_id', 'task_type', 'updated_by', 'old_due_date', 'new_due_date'];

	public function users() {
		return $this->hasOne(\App\User::class, 'id', 'updated_by');
	}
}
