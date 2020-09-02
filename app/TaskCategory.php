<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nestable\NestableTrait;

class TaskCategory extends Model
{
	use SoftDeletes;
	use NestableTrait;

	protected $parent = 'parent_id';
	protected $fillable = [
		'title', 'parent_id', 'is_approved','is_active'
	];
}
