<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleAdsCampaign extends Model
{
    protected $table    = 'googlecampaigns';
    
    protected $fillable = [
        'account_id',
        'google_campaign_id',
        'campaign_name',
        'budget_amount',
        'start_date',
        'end_date',
        'budget_uniq_id',
        'budget_id',
        'merchant_id',
        'sales_country',
        'channel_type',
        'channel_sub_type',
        'bidding_strategy_type',
        'target_cpa_value',
        'target_roas_value',
        'maximize_clicks',
        'ad_rotation',
        'campaign_response',
        'status',
    ];

    const CAHANNEL_TYPE = [
        "UNKNOWN"       => "Unknown",
        "SEARCH"        => "SEARCH",
        "DISPLAY"       => "DISPLAY",
        "SHOPPING"      => "SHOPPING",
        "MULTI_CHANNEL" => "MULTI_CHANNEL",
    ];

    const CAHANNEL_SUB_TYPE = [
        "UNKNOWN"                   => "Unknown",
        "SEARCH_MOBILE_APP"         => "Search mobile app",
        "DISPLAY_MOBILE_APP"        => "Display mobile app",
        "SEARCH_EXPRESS"            => "Search Express",
        "DISPLAY_EXPRESS"           => "Display Express",
        "UNIVERSAL_APP_CAMPAIGN"    => "Universal app campaign",
        "DISPLAY_SMART_CAMPAIGN"    => "Display smart campaign",
        "DISPLAY_GMAIL_AD"          => "Display gmail ad",
    ];

}
