<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
  use SoftDeletes;

  public function products()
  {
    return $this->belongsToMany('App\PurchaseProduct', 'purchase_products', 'purchase_id', 'product_id');
  }
}
