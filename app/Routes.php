<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Redirect;


class Routes extends Model
{
	protected $fillable = ['url','page_title','page_description'];

}
