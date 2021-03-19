<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DailyActivity extends Model {

	protected $fillable = [
		'time_slot',
		'user_id',
		'is_admin',
		'assist_msg',
		'activity',
		'for_date',
		'general_category_id',
		'actual_start_date',
		'repeat_type',
		'repeat_on',
		'repeat_end',
		'repeat_end_date',
		'parent_row',
		'status'
	];

	public function remarks()
	{
		return $this->hasMany('App\Remark', 'taskid')->where('module_type', 'task')->latest();
	}

	public function generalCategory()
	{
		return $this->hasOne('App\GeneralCategory','id','general_category_id');
	}
}
