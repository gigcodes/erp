<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleResponsiveDisplayAd extends Model
{
    use HasFactory;

    public $fillable = [
        'adgroup_google_campaign_id',
        'google_adgroup_id',
        'google_ad_id',
        'headline1',
        'headline2',
        'headline3',
        'description1',
        'description2',
        'long_headline',
        'business_name',
        'final_url',
        'ads_response',
        'status',
    ];

    public function marketing_images()
    {
        return $this->hasMany(GoogleResponsiveDisplayAdMarketingImage::class, 'google_responsive_display_ad_id', 'id');
    }
}