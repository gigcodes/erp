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
  		return $this->hasMany('App\Remark', 'taskid')->where('module_type', 'task-note-subnote')->whereNull('delete_at');
    }
    
    public function singleSubnotes()
  	{
  		return $this->hasOne('App\Remark', 'taskid')->where('module_type', 'task-note-subnote')->whereNull('delete_at')->latest();
  	}

    public function archiveSubnotes()
    {
      return $this->hasMany('App\Remark', 'taskid')->where('module_type', 'task-note-subnote')->whereNotNull('delete_at');
    }
}
