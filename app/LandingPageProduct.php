<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LandingPageProduct extends Model
{
    const STATUS = [
        "De-active",
        "Active",
    ];

    protected $fillable = ['product_id', 'name', 'description', 'price', 'shopify_id', 'stock_status', 'status', 'start_date', 'end_date', 'created_at', 'updated_at'];

    public function product()
    {
        return $this->hasOne(\App\Product::class, "id", "product_id");
    }
}
