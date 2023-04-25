<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliateConversions extends Model
{
    protected $fillable = [
        'affiliate_account_id',
        'affiliate_conversion_id',
        'external_id',
        'amount',
        'click_date',
        'click_referrer',
        'click_landing_page',
        'commission_id',
        'program_id',
        'affiliate_id',
        'affiliate_commission_id',
        'affiliate_program_id',
        'affiliate_marketer_id',
        'customer_id',
        'customer_system_id',
        'customer_status',
        'meta_data',
        'commission_created_at',
        'warnings',
        'affiliate_meta_data'
    ];

    public function account()
    {
        return $this->hasOne(AffiliateProviderAccounts::class, 'affiliate_account_id', 'id');
    }

    public function commission()
    {
        return $this->hasOne(AffiliateCommissions::class, 'affiliate_commission_id', 'id');
    }

    public function affiliate()
    {
        return $this->hasOne(AffiliateMarketers::class, 'affiliate_marketer_id', 'id');
    }

    public function program()
    {
        return $this->hasOne(AffiliatePrograms::class, 'affiliate_program_id', 'id');
    }
}
