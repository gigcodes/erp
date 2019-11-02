<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instruction extends Model
{
  protected $fillable = ['start_time', 'end_time', 'customer_id', 'product_id', 'order_id', 'instruction', 'category_id', 'assigned_to', 'assigned_from'];

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

  public function assingTo()
  {
    return $this->hasOne("\App\User","id","assigned_to");
  }

  public function assignFrom()
  {
    return $this->hasOne("\App\User","id","assigned_from");
  }
}
