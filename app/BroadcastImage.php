<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class BroadcastImage extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="sending_time",type="datetime")
     */
    use Mediable;

    protected $fillable = [
        'sending_time',
    ];
}
