<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleAdsCampaign extends Model
{
    protected $table='googlecampaigns';
    protected $fillable=['account_id','google_campaign_id','campaign_name','budget_amount','start_date','end_date','budget_uniq_id','budget_id','campaign_response','status'];
}
