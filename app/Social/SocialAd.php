<?php

namespace App\Social;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Plank\Mediable\Mediable;
use App\Models\SocialAdAccount;
use Illuminate\Database\Eloquent\Model;

class SocialAd extends Model
{
    use Mediable;

    protected $fillable = [
        'ref_ads_id',
        'adset_id',
        'config_id',
        'name',
        'creative_id',
        'ad_set_name',
        'ad_creative_name',
        'status',
        'live_status',
        'created_at',
    ];

    public function account()
    {
        return $this->belongsTo(SocialAdAccount::class, 'config_id');
    }

    public function adset()
    {
        return $this->belongsTo(SocialAdset::class, 'adset_id');
    }

    public function creative()
    {
        return $this->belongsTo(SocialAdCreative::class, 'creative_id');
    }
}
