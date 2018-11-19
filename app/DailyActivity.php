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
	];
}
