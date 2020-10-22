<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierDiscountInfo extends Model
{
    protected $fillable = [
        'order_product_id','discount','fixed_price','supplier_id'
    ];
}
