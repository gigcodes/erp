<?php

namespace App;
use App\Task;

use Illuminate\Database\Eloquent\Model;

class WhatsAppGroup extends Model
{
    protected $fillable = [
		'task_id', 'group_id'
	];


	public function task()
	{
		return $this->belongsTo(Task::class);
	}
}
