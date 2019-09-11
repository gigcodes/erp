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
    'user_id', 'module_id', 'priority', 'subject', 'task', 'cost', 'status', 'module', 'completed', 'estimate_time', 'start_time', 'end_time','task_type_id'
  ];

  public function user() {
    return $this->belongsTo('App\User');
  }

  public function development_details()
  {
    return $this->hasMany('App\Remark', 'taskid')->where('module_type', 'task-detail')->latest();
  }

  public function development_discussion()
  {
    return $this->hasMany('App\Remark', 'taskid')->where('module_type', 'task-discussion')->latest();
  }

  public function messages() {
      return $this->hasMany(ChatMessage::class, 'developer_task_id', 'id');
  }

  public function developerModule() {
      return $this->belongsTo(DeveloperModule::class, 'module_id', 'id');
  }
}
