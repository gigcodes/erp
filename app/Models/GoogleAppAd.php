<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
