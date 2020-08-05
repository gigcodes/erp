<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

class BrandCategorySizeChart extends Model
{
    use Mediable;
    protected $table = 'brand_category_size_charts';
    protected $fillable = ['brand_id', 'category_id', 'store_website_id'];
}
