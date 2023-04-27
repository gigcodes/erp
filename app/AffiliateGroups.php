<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliateGroups extends Model
{
    protected $fillable = ['title', 'affiliate_provider_group_id', 'affiliate_account_id'];

    public function sites() {
        return $this->hasMany(AffiliateProviderAccounts::class, 'affiliate_account_id', 'id');
    }
}