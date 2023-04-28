<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliatePayments extends Model
{
    protected $fillable = [
        'affiliate_account_id',
        'payment_id',
        'payment_created_at',
        'affiliate_marketer_id',
        'amount',
        'currency',
    ];

    public function account()
    {
        return $this->hasOne(AffiliateProviderAccounts::class, 'id', 'affiliate_account_id');
    }

    public function affiliate()
    {
        return $this->hasOne(AffiliateMarketers::class, 'id', 'affiliate_marketer_id');
    }
}
