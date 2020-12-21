<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Website extends Model
{
    protected $fillable = [
        'name', 
        'code', 
        'sort_order', 
        'platform_id', 
        'store_website_id', 
        'is_finished'
    ];

    public function stores()
    {
        return $this->hasMany(\App\WebsiteStore::class, 'website_id', 'id');
    }

    public function storeWebsite()
    {
        return $this->hasOne(\App\StoreWebsite::class, 'id', 'store_website_id');
    }
}
