<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Plank\Mediable\Mediable;
use Spatie\Activitylog\Traits\LogsActivity;


class Leads extends Model {
	//
	use Mediable;
	use SoftDeletes;
	protected $fillable = [
		'customer_id',
		'client_name',
		'city',
		'contactno',
		'solophone',
		'rating',
		'instahandler',
		'status',
		'userid',
		'comments',
		'assigned_user',
		'selected_product',
		'size',
		'address',
		'email',
		'source',
		'brand',
		'leadsourcetxt',
		'multi_brand',
		'multi_category',
		'remark',
		'whatsapp_number',
		'created_at'
	];

	const CREATED_AT = null;

	protected $appends = ['communication'];
	protected $communication = '';

	public function customer()
	{
		return $this->belongsTo('App\Customer');
	}

	public function messages()
	{
		return $this->hasMany('App\Message', 'moduleid')->where('moduletype', 'leads')->latest()->first();
	}

	public function whatsapps()
	{
		return $this->hasMany('App\ChatMessage', 'lead_id')->latest()->first();
	}

	public function status_changes()
	{
		return $this->hasMany('App\StatusChange', 'model_id')->where('model_type', 'App\Leads')->latest();
	}

	public function instagram()
	{
		return $this->hasMany('App\InstaMessages', 'lead_id')->latest()->first();
	}

	public function getCommunicationAttribute()
	{
		$message  = $this->messages();
		$whatsapp = $this->whatsapps();
		$instagram = $this->instagram();

		if (!empty($message) && !empty($whatsapp) && !empty($instagram)) {
			if ($message->created_at > $whatsapp->created_at && $message->created_at > $instagram->created_at) {
				return $message;
			} elseif ($whatsapp->created_at > $message->created_at && $whatsapp->created_at > $instagram->created_at) {
				return $whatsapp;
			} elseif ($instagram->created_at > $message->created_at && $instagram->created_at > $whatsapp->created_at) {
				return $instagram;
			}
		} elseif(!empty($message) && !empty($whatsapp)) {
			if($message->created_at > $whatsapp->created_at) {
				return $message;
			} else {
				return $whatsapp;
			}
		} elseif(!empty($message) && !empty($instagram)) {
			if($message->created_at > $instagram->created_at) {
				return $message;
			} else {
				return $instagram;
			}
		} elseif(!empty($whatsapp) && !empty($instagram)) {
			if($whatsapp->created_at > $instagram->created_at) {
				return $whatsapp;
			} else {
				return $instagram;
			}
		} elseif(!empty($whatsapp)) {
			return $whatsapp;
		} elseif(!empty($instagram)) {
			return $instagram;
		} elseif(!empty($message)) {
			return $message;
		}
	}
}
