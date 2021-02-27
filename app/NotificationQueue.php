<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationQueue extends Model {

	protected $fillable = [
		"type",
		'message',
		'message_id',
		'reminder',
		'time_to_add',
		'model_type',
		'model_id',
		'user_id',
		'message_id',
		'reminder',
		'sent_to',
		'role',
	];
}
