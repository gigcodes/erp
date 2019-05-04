<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

class Complaint extends Model
{
  use Mediable;

  protected $fillable = [
    'customer_id', 'platform', 'complaint', 'status', 'link', 'where', 'username', 'name', 'plan_of_action', 'thread_type', 'date'
  ];

  public function customer()
  {
    return $this->belongsTo('App\Customer');
  }

  public function threads()
  {
    return $this->hasMany('App\ComplaintThread');
  }

  public function internal_messages()
  {
    return $this->hasMany('App\Remark', 'taskid')->where('module_type', 'internal-complaint')->latest();
  }

  public function plan_messages()
  {
    return $this->hasMany('App\Remark', 'taskid')->where('module_type', 'complaint-plan-comment')->latest();
  }

  public function remarks()
  {
    return $this->hasMany('App\Remark', 'taskid')->where('module_type', 'complaint')->latest();
  }

  public function status_changes()
	{
		return $this->hasMany('App\StatusChange', 'model_id')->where('model_type', 'App\Complaint')->latest();
	}
}
