<?php

namespace App\Models;

use App\StoreWebsite;
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
}
