<?php

namespace App;

use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class StoreWebsiteProductScreenshot extends Model
{
    use Mediable;

    protected $fillable = [
        'id',
        'store_website_id',
        'status',
        'product_id',
        'sku',
        'store_website_name',
        'image_path',
        'created_at',
        'updated_at',
    ];

    public function storeWebsite()
    {
        return $this->hasOne(\App\StoreWebsite::class, 'id', 'store_website_id');
    }
}
