<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceOverride extends Model
{
    protected $fillable = ['store_website_id', 'brand_id', 'brand_segment', 'category_id', 'type', 'calculated', 'value', 'country_code', 'created_at', 'updated_at'];

    public function brand()
    {
        return $this->hasOne(App\Brand::class, "id", "brand_id");
    }

    public function category()
    {
        return $this->hasOne(App\Categor::class, "id", "category_id");
    }

    public function country()
    {
        return $this->hasOne(App\SimplyDutyCoutry::class, "country_code", "country_code");
    }
}
