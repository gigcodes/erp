<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instruction extends Model
{
  public function customer()
  {
    return $this->belongsTo('App\Customer');
  }

  public function category()
  {
    return $this->belongsTo('App\InstructionCategory');
  }

  public function remarks()
  {
    return $this->hasMany('App\Remark', 'taskid')->where('module_type', 'instruction')->latest();
  }
}
