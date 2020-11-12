<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturnExchangeProduct extends Model
{
    protected $fillable = [
        'product_id',
        'order_product_id',
        'name',
        'status_id',
    ];
}
