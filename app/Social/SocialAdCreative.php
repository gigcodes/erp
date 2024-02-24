<?php

namespace App\Social;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Plank\Mediable\Mediable;
use App\Models\SocialAdAccount;
use Illuminate\Database\Eloquent\Model;

class SocialAdCreative extends Model
{
    use Mediable;

    protected $fillable = [
        'ref_adcreative_id',
        'config_id',
        'object_story_title',
        'object_story_id',
        'live_status',
    ];

    public function account()
    {
        return $this->belongsTo(SocialAdAccount::class, 'config_id');
    }
}
