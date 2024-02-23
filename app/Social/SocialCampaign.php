<?php

namespace App\Social;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Plank\Mediable\Mediable;
use App\Models\SocialAdAccount;
use Illuminate\Database\Eloquent\Model;

class SocialCampaign extends Model
{
    use Mediable;

    protected $fillable = [
        'ref_campaign_id',
        'config_id',
        'name',
        'objective_name',
        'buying_type',
        'daily_budget',
        'live_status',
        'created_at',
    ];

    public function account()
    {
        return $this->belongsTo(SocialAdAccount::class, 'config_id');
    }
}
