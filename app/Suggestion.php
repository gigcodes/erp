<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
  protected $fillable = [
    'customer_id', 'brand', 'category', 'size', 'supplier', 'number'
  ];

  public function products()
  {
    return $this->hasMany('App\Product', 'suggestion_products', 'suggestion_id', 'product_id');
  }
}
