<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliateProviders extends Model
{
    protected $fillable = ['provider_name', 'status'];

    public function sites() {
        $this->hasMany(AffiliateProviderSites::class, 'affiliates_provider_id', 'id');
    }
}
