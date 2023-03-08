<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleResponsiveDisplayAdMarketingImage extends Model
{
    use HasFactory;

    public $fillable = [
        'google_customer_id',
        'adgroup_google_campaign_id',
        'google_adgroup_id',
        'google_responsive_display_ad_id',
        'google_asset_id',
        'type',
        'name',
    ];
}
