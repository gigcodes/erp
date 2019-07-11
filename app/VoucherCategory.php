<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nestable\NestableTrait;

class VoucherCategory extends Model
{
	use NestableTrait;

	protected $parent = 'parent_id';
	protected $fillable = [
		'title', 'parent_id'
	];
}
