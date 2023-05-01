<?php

namespace App\Social;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class SocialAd extends Model
{
    use Mediable;

    public function account()
    {
        return $this->belongsTo(\App\Social\SocialConfig::class);
    }
}
