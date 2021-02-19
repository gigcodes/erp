<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = [
        'campaign_id',
        'adgroup_id',
        'finalurl',
        'displayurl',
        'headlines',
        'descriptions',
        'tracking_tamplate',
        'final_url_suffix',
        'customparam',
        'different_url_mobile',
        'mobile_final_url',
        'ad_id',
        'ad_response'

    ];
}
