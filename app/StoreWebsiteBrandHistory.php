<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteBrandHistory extends Model
{
    protected $fillable = [
        'brand_id','store_website_id','type','message','created_at', 'updated_at'
    ];

}
