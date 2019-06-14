<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

class VendorProduct extends Model
{
  use Mediable;

  protected $fillable = [
    'vendor_id', 'date_of_order', 'name', 'qty', 'price', 'payment_terms', 'recurring_type', 'delivery_date', 'received_by', 'approved_by', 'payment_details'
  ];

  public function vendor()
  {
    return $this->belongsTo('App\Vendor');
  }
}
