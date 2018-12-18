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

	public function messages()
	{
		return $this->hasMany('App\Message', 'moduleid')->where('moduletype', 'leads')->latest()->first();
	}

	public function whatsapps()
	{
		return $this->hasMany('App\ChatMessage', 'lead_id')->latest()->first();
	}

	public function getCommunicationAttribute()
	{
		$message = $this->messages();
		$whatsapp = $this->whatsapps();

		if ($message && $whatsapp) {
			if ($message->created_at > $whatsapp->created_at) {
				return $message;
			}

			return $whatsapp;
		}

		if ($message) {
			return $message;
		}

		return $whatsapp;
	}

	// public function setCommunicationAttribute()
	// {
	//
	// }
}
