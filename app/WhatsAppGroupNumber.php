<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WhatsAppGroupNumber extends Model
{
    protected $fillable = [
		'user_id','group_id','user_number'
	];
}
