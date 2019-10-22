<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\WhatsAppGroup;


class Task extends Model {

	use SoftDeletes;

	protected $fillable = [
		'category',
		'task_details',
		'task_subject',
		'completion_date',
		'assign_from',
		'assign_to',
		'is_statutory',
		'is_completed',
		'sending_time',
		'recurring_type',
		'statutory_id',
		'model_type',
		'model_id',
	];

	protected $dates = ['deleted_at'];

	public static function hasremark( $id ) {
		$task = Task::find( $id );
		if ( ! empty( $task->remark ) ) {
			return true;
		} else {
			return false;
		}
	}

	// getting remarks
	public static function getremarks($taskid)
	{
			$results = DB::select('select * from remarks where taskid = :taskid order by created_at DESC', ['taskid' => $taskid]);
			return json_decode(json_encode($results),true);
	}

	public function remarks()
	{
		return $this->hasMany('App\Remark', 'taskid')->where('module_type', 'task')->latest();
	}

	public function notes()
	{
		return $this->hasMany('App\Remark', 'taskid')->where('module_type', 'task-note')->latest();
	}

	public function users()
	{
		return $this->belongsToMany('App\User', 'task_users', 'task_id', 'user_id')->where('type', 'App\User');
	}

	public function contacts()
	{
		return $this->belongsToMany('App\Contact', 'task_users', 'task_id', 'user_id')->where('type', 'App\Contact');
	}

	public function whatsappgroup()
	{
		return $this->hasOne(WhatsAppGroup::class);
	}

	public function whatsappAll()
    {
        return $this->hasMany('App\ChatMessage', 'task_id')->whereNotIn('status', ['7', '8', '9'])->latest();
    }
}
