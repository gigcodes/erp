<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliateProviderSites extends Model
{
    protected $fillable = ['affiliates_provider_id', 'store_website_id', 'api_key', 'status'];

    public function provider() {
        $this->hasOne(AffiliateProviders::class, 'id', 'affiliates_provider_id');
    }
}
