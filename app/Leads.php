<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Plank\Mediable\Mediable;
use Spatie\Activitylog\Traits\LogsActivity;


class Leads extends Model {
	//
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
		'address',
		'email',
		'source',
		'brand',
		'leadsourcetxt',
		'multi_brand',
		'multi_category',
		'remark',
	];
}
