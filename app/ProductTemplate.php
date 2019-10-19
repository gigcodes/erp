<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductTemplate extends Model
{
    protected $fillable = [
        'template_no',
        'product_title',
        'brand_id',
        'currency',
        'price',
        'discounted_price',
        'product_id',
        'is_processed'
    ];

}    
