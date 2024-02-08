<?php

namespace App\Social;

use App\StoreWebsite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialConfig extends Model
{
    protected $fillable = [
        'store_website_id', 'page_language', 'platform', 'name', 'email', 'password', 'api_key', 'api_secret', 'token', 'status', 'page_id', 'page_token', 'account_id', 'webhook_token', 'ads_manager',
    ];

    public function storeWebsite(): HasOne
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
    }

    public function businessPost(): HasMany
    {
        return $this->hasMany(\App\BusinessPost::class);
    }

    public function bussiness_website(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'store_website_id')->select('title', 'id');
    }

    public function setPasswordAttribute($password): void
    {
        if (trim($password) == '') {
            return;
        }
        $this->attributes['password'] = encrypt($password);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(SocialPost::class,'config_id');
    }
}
