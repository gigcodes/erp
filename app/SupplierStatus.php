<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierStatus extends Model
{
	protected $table = 'supplier_status';
	public $timestamps = false;
	protected $fillable = [
		'name'
	];
}
