<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierCategory extends Model
{
	protected $table = 'supplier_category';
	public $timestamps = false;
	protected $fillable = [
		'name'
	];
}
