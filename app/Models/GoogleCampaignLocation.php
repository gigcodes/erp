<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoogleCampaignLocation extends Model
{
    use HasFactory;

    public $fillable = [
        'google_customer_id',
        'adgroup_google_campaign_id',
        'google_location_id',
        'type',
        'country_id',
        'state_id',
        'city_id',
        'address',
        'distance',
        'radius_units',
        'is_target',
    ];

    public function campaign()
    {
        return $this->belongsTo(\App\GoogleAdsCampaign::class, 'adgroup_google_campaign_id', 'google_campaign_id');
    }
}
