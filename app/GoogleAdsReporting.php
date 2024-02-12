<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleAdsReporting extends Model
{
    protected $table = 'google_ads_reportings';

    protected $fillable = [
        'google_customer_id',
        'adgroup_google_campaign_id',
        'google_adgroup_id',
        'google_ad_id',
        'google_account_id',
        'campaign_type',
        'impression',
        'click',
        'cost_micros',
        'average_cpc',
        'date',
        'created_at',
        'updated_at',
    ];

    public function account()
    {
        return $this->belongsTo(\App\GoogleAdsAccount::class, 'google_account_id', 'id');
    }

    public function campaign()
    {
        return $this->belongsTo(\App\GoogleAdsCampaign::class, 'adgroup_google_campaign_id', 'google_campaign_id');
    }

    public function adgroup()
    {
        return $this->belongsTo(\App\GoogleAdsGroup::class, 'google_adgroup_id', 'google_adgroup_id');
    }

    public function search_ad()
    {
        return $this->belongsTo(\App\GoogleAd::class, 'google_ad_id', 'google_ad_id');
    }

    public function display_ad()
    {
        return $this->belongsTo(\App\Models\GoogleResponsiveDisplayAd::class, 'google_ad_id', 'google_ad_id');
    }

    public function multi_channel_ad()
    {
        return $this->belongsTo(\App\Models\GoogleAppAd::class, 'google_ad_id', 'google_ad_id');
    }
}
