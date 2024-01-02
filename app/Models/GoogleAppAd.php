<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoogleAppAd extends Model
{
    use HasFactory;

    public $fillable = [
        'google_customer_id',
        'adgroup_google_campaign_id',
        'google_adgroup_id',
        'google_ad_id',
        'headline1',
        'headline2',
        'headline3',
        'description1',
        'description2',
        'youtube_video_ids',
        'ads_response',
        'status',
    ];

    public function images()
    {
        return $this->hasMany(GoogleAppAdImage::class, 'google_app_ad_id', 'id');
    }

    public function campaign()
    {
        return $this->belongsTo(\App\GoogleAdsCampaign::class, 'adgroup_google_campaign_id', 'google_campaign_id');
    }

    public function adgroup()
    {
        return $this->belongsTo(\App\GoogleAdsGroup::class, 'google_adgroup_id', 'google_adgroup_id');
    }
}
