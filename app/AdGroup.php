<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdGroup extends Model
{
    protected $fillable = [
        'campaign_id', 'type', 'group_name', 'url', 'keywords','budget','google_campaign_id','google_ad_group_id','google_ad_group_response',
    ];
}
