<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleAdsGroup extends Model
{
    protected $table='googleadsgroups';
    protected $fillable=['adgroup_google_campaign_id','google_adgroup_id','ad_group_name','bid','status','adgroup_response'];
}
