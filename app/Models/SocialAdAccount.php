<?php

namespace App\Models;

use App\Social\SocialConfig;
use App\StoreWebsite;
use App\Social\SocialPost;
use Illuminate\Database\Eloquent\Model;

class SocialAdAccount extends Model
{
    protected $fillable = [
        'store_website_id',
        'name',
        'ad_account_id',
        'page_token',
        'status',
    ];

    public function storeWebsite()
    {
        return $this->belongsTo(StoreWebsite::class);
    }

    public function social_configs()
    {
        return $this->hasMany(SocialConfig::class, 'ad_account_id');
    }
}
