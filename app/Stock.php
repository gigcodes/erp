<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'courier', 'package_from', 'date', 'awb', 'l_dimension', 'h_dimension', 'w_dimension', 'weight', 'pcs'
  ];

  public function products()
  {
    return $this->belongsToMany('App\Product', 'stock_products', 'stock_id', 'product_id');
  }
}
