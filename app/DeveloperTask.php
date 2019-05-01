<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Mediable;

class DeveloperTask extends Model
{
  use Mediable;
  use SoftDeletes;

  protected $fillable = [
    'user_id', 'module_id', 'priority', 'subject', 'task', 'cost', 'status', 'module', 'completed', 'estimate_time', 'start_time', 'end_time'
  ];

  public function user() {
    return $this->belongsTo('App\User');
  }

  public function development_details()
  {
    return $this->hasMany('App\Remark', 'taskid')->where('module_type', 'task-detail')->latest();
  }
}
