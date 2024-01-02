<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoogleAdGroupKeyword extends Model
{
    use HasFactory;

    public $fillable = [
        'google_customer_id',
        'adgroup_google_campaign_id',
        'google_adgroup_id',
        'google_keyword_id',
        'keyword',
    ];

    public function ad_group()
    {
        return $this->belongsTo(\App\GoogleAdsGroup::class, 'google_adgroup_id', 'google_adgroup_id');
    }
}
