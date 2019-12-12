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

    public function subnotes()
  	{
  		return $this->hasMany('App\Remark', 'taskid')->where('module_type', 'task-note-subnote')->latest();
    }
    
    public function singleSubnotes()
  	{
  		return $this->hasOne('App\Remark', 'taskid')->where('module_type', 'task-note-subnote')->latest();
  	}
}
