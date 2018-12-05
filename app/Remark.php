<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Remark extends Model
{
    protected $fillable = [
    	'remark',
	    'taskid',
      'user_name'
    ];
}
