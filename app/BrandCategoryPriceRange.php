<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BrandCategoryPriceRange extends Model
{
    protected $table = 'brand_category_price_range';
    protected $fillable = ['brand_id', 'category_id', 'min_price', 'max_price'];
}
