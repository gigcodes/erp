<?php

namespace App\Social;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

class SocialAd extends Model
{
    use Mediable;

    public function account()
    {
        return $this->belongsTo(\App\Social\SocialConfig::class);
    }
}
