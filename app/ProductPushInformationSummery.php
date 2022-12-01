<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductPushInformationSummery extends Model
{
    protected $fillable = ['brand_id', 'category_id', 'store_website_id', 'product_push_count'];

    public function brand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function storeWebsite()
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
    }
}
