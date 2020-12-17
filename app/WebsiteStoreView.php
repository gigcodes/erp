<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class WebsiteStoreView extends Model
{
    protected $fillable = [
        'name', 
        'code', 
        'status', 
        'sort_order', 
        'platform_id', 
        'website_store_id', 
    ];

    public function websiteStore()
    {
        return $this->hasOne(\App\WebsiteStore::class, 'id','website_store_id');
    }
}
