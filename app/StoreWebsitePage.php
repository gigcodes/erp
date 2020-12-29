<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWebsitePage extends Model
{
    protected $fillable = [
        'title',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'content_heading',
        'content',
        'layout',
        'url_key',
        'active',
        'stores',
        'platform_id',
        'store_website_id',
        'language'
    ];

    public function storeWebsite()
    {
        return $this->hasOne(\App\StoreWebsite::class, "id", "store_website_id");
    }
}
