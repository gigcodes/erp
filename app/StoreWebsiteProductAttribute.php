<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteProductAttribute extends Model
{
    protected $fillable = [
        'product_id', 'price', 'discount', 'discount_type', 'description', 'store_website_id', 'created_at', 'updated_at',
    ];

    public function storeWebsite()
    {
        return $this->belongsTo(\App\StoreWebsite::class);
    }
}
