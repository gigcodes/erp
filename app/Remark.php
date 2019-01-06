<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Remark extends Model
{
    protected $fillable = [
    	'remark',
      'taskid',
	    'module_type',
      'user_name'
    ];
}
