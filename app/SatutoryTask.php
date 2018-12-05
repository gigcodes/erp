<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SatutoryTask extends Model {
	use SoftDeletes;

	protected $fillable = [
		'category',
		'assign_from',
		'assign_to',
		'task_details',
		'task_subject',
		'remark',
		'recurring_type',
		'recurring_day',
	];
}
