<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentRemark extends Model
{
    protected $fillable = [
    	'remark',
      'document_id',
	    'module_type',
      'user_name'
    ];

   /* public function subnotes()
  	{
  		return $this->hasMany('App\Remark', 'taskid')->where('module_type', 'task-note-subnote')->latest();
  	}*/
}
