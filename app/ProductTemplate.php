<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

class ProductTemplate extends Model
{
    use Mediable;
    protected $fillable = [
        'template_no',
        'product_title',
        'brand_id',
        'currency',
        'price',
        'discounted_price',
        'product_id',
        'is_processed',
    ];

    public function brand() {
        return $this->hasOne("\App\Brand","id","brand_id");
    }

}
