<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderReport extends Model
{
  protected $appends = ['status'];

  public function status()
  {
    return $this->belongsTo('App\OrderStatus', 'status_id');
  }

  public function statusName()
  {
    return $this->belongsTo('App\OrderStatus', 'status_id')->first()->status;
  }

  public function getStatusAttribute()
	{
		return $this->statusName();
	}
}
