<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseDiscount extends Model
{
  protected $fillable = [
    'purchase_id', 'product_id', 'percentage', 'amount', 'type'
  ];
}
