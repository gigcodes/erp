<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleAd extends Model
{
    protected $table='googleads';
    protected $fillable=['adgroup_google_campaign_id','google_adgroup_id','google_ad_id','headline1','headline2','headline3','description1','description2','final_url','path1','path2','ads_resposne','status'];
}
