<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScrapedProducts extends Model
{
    protected $casts = [
        'images' => 'array',
        'properties' => 'array',
    ];

    protected $fillable = [
        'sku',
        'website',
        'images',
        'properties',
        'title',
        'brand_id',
        'description',
        'url',
        'is_properly_updated',
        'is_price_updated',
        'is_enriched',
        'has_sku',
        'price',
        'can_be_deleted'
    ];

    public function brand() {
        return $this->belongsTo(Brand::class);
    }
}
