<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleAdsCampaign extends Model
{
    protected $table='googlecampaigns';
    protected $fillable=['account_id','google_campaign_id','campaign_name','budget_amount','start_date','end_date','budget_uniq_id','budget_id','merchant_id',
    'sales_country',
    'channel_type',
    'channel_sub_type',
    'bidding_strategy_type',
    'target_cpa_value',
    'target_roas_value',
    'maximize_clicks',
    'ad_rotation','campaign_response','status'];
}
