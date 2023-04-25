<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliateCustomers extends Model
{
    protected $fillable = [
        'affiliate_account_id',
        'customer_id',
        'customer_system_id',
        'status',
        'customer_created_at',
        'click_date',
        'click_referrer',
        'click_landing_page',
        'program_id',
        'affiliate_id',
        'affiliate_program_id',
        'affiliate_marketer_id',
        'affiliate_meta_data',
        'meta_data',
        'warnings'
    ];

    public function account()
    {
        return $this->hasOne(AffiliateProviderAccounts::class, 'affiliate_account_id', 'id');
    }

    public function affiliate()
    {
        return $this->hasOne(AffiliateMarketers::class, 'affiliate_marketer_id', 'id');
    }
}
