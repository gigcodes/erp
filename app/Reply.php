<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reply extends Model
{
  use SoftDeletes;

	protected $fillable = ['reply', 'model'];
	protected $dates = ['deleted_at'];
}
