<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliateMarketers extends Model
{
    protected $fillable = [
        'affiliate_account_id',
        'affiliate_id',
        'firstname',
        'lastname',
        'email',
        'company_name',
        'company_description',
        'address_one',
        'address_two',
        'address_postal_code',
        'address_city',
        'address_state',
        'address_country_code',
        'address_country_name',
        'meta_data',
        'parent_id',
        'affiliate_created_at',
        'affiliate_group_id',
        'promoted_at',
        'promotion_method'
    ];


    public function account()
    {
        return $this->hasOne(AffiliateProviderAccounts::class, 'affiliate_account_id', 'id');
    }
}
