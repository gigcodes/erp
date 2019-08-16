<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BrandCategoryPriceRange extends Model
{
    protected $table = 'brand_category_price_range';
    protected $fillable = ['brand_segment', 'category_id', 'min_price', 'max_price'];
}
