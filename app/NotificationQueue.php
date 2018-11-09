<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationQueue extends Model {

	protected $fillable = [
		"type",
		'message',
		'time_to_add',
		'model_type',
		'model_id',
		'user_id',
		'sent_to',
		'role',
	];
}
