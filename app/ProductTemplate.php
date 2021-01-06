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
        'text',
        'font_style',
        'font_size',
        'background_color',
        'brand_id',
        'currency',
        'price',
        'discounted_price',
        'product_id',
        'is_processed',
        'store_website_id',
    ];

    public function brand()
    {
        return $this->hasOne("\App\Brand", "id", "brand_id");
    }

    public function category()
    {
        return $this->hasOne("\App\Category", "id", "category_id");
    }

    public function template()
    {
        return $this->hasOne("\App\Template", "id", "template_no");
    }

    public function storeWebsite()
    {
        return $this->hasOne(\App\StoreWebsite::class, 'id', 'store_website_id');
    }

}
