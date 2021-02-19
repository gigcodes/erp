<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdCampaign extends Model
{
    protected $fillable = [
        'goal', 'type', 'campaign_name', 'data','campaign_budget_id','campaign_id','campaign_response',
    ];
}
