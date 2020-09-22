<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Redirect;
	

class EmailLead extends Model
{
	protected $fillable = ['email','source','created_at','updated_at'];
	
}
