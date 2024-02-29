<?php

namespace App\Social;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Plank\Mediable\Mediable;
use App\Models\SocialAdAccount;
use Illuminate\Database\Eloquent\Model;

class SocialAdset extends Model
{
    use Mediable;

    protected $fillable = [
        'config_id',
        'ref_adset_id',
        'name',
        'campaign_id',
        'destination_type',
        'billing_event',
        'start_time',
        'end_time',
        'daily_budget',
        'bid_amount',
        'status',
        'live_status',
        'created_at',
    ];

    public function account()
    {
        return $this->belongsTo(SocialAdAccount::class, 'config_id');
    }

    public function campaign()
    {
        return $this->belongsTo(SocialCampaign::class, 'campaign_id');
    }
}
