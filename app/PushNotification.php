<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Helpers;
use App\Leads;
use App\Order;

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
	protected $client_name = '';
	protected $appends = ['user_name', 'client_name'];

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

	public function getClientNameAttribute() {
		return $this->client_name;
	}

	public function setClientNameAttribute($model_type, $model_id) {
		if ($model_type == 'leads') {
			if ($lead = Leads::find($model_id)) {
				$this->client_name = $lead->client_name;
			}
		} else if ($model_type == 'order') {
			if ($order = Order::find($model_id)) {
				$this->client_name = $order->client_name;
			}
		}
	}
}
