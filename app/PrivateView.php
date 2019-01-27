<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrivateView extends Model
{
  public function customer()
  {
    return $this->belongsTo('App\Customer');
  }

  public function products()
  {
    return $this->belongsToMany('App\Product', 'private_view_products', 'private_view_id', 'product_id');
  }
}
