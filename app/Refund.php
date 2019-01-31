<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
  protected $fillable = [
    'customer_id', 'order_id', 'type', 'chq_number', 'awb', 'date_of_refund', 'date_of_issue', 'details', 'dispatch_date', 'date_of_request'
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
