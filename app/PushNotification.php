<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
	protected $fillable = [
		"type",
		"message",
		"role",
		"user_id",
		'sent_to',
		'model_type',
		'model_id',
	];
}
