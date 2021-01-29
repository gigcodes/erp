<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductSizes extends Model
{
    //
    protected $fillable = ['product_id', 'supplier_id', 'quantity' , 'size'];
}
