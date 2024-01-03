<?php

namespace App\Social;
use App\StoreWebsite;
use Illuminate\Database\Eloquent\Model;

class SocialConfig extends Model
{
    protected $fillable = [
        'store_website_id', 'page_language', 'platform', 'name', 'email', 'password', 'api_key', 'api_secret', 'token', 'status', 'page_id', 'page_token', 'account_id', 'webhook_token', 'ads_manager',
    ];

    public function storeWebsite()
    {
        return $this->hasOne(\App\StoreWebsite::class, 'id', 'store_website_id');
    }

    public function businessPost()
    {
        return $this->hasMany(\App\BusinessPost::class);
    }

    public function bussiness_website()
    {
        return $this->belongsTo(StoreWebsite::class, 'store_website_id')->select('title', 'id');
    }
}
