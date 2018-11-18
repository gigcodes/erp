<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Helpers;

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
		'message_id',
		'reminder'
	];

	protected $user_name = '';
	protected $appends = ['user_name'];

	public function getUserNameAttribute() {
		return $this->user_name;
	}

	public function setUserNameAttribute($id = null) {
		if ($id == null) {
			$this->user_name = '';
		} else {
			$this->user_name = Helpers::getUserNameById($id);
		}
	}
}
