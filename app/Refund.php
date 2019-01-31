<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
  protected $fillable = [
    'customer_id', 'order_id', 'type', 'payment', 'date_of_payment', 'details', 'completed'
  ];

  public function customer()
  {
    return $this->belongsTo('App\Customer');
  }

  public function order()
  {
    return $this->belongsTo('App\Order');
  }
}
