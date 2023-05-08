<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliateCustomers extends Model
{
    protected $table = 'affiliates_customers';

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
        'warnings',
    ];

    public function account()
    {
        return $this->hasOne(AffiliateProviderAccounts::class, 'id', 'affiliate_account_id');
    }

    public function affiliate()
    {
        return $this->hasOne(AffiliateMarketers::class, 'id', 'affiliate_marketer_id');
    }

    public function programme()
    {
        return $this->hasOne(AffiliatePrograms::class, 'id', 'affiliate_program_id');
    }
}
