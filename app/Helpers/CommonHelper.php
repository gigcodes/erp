<?php

namespace App\Helpers;

use Carbon\Carbon;

class CommonHelper
{
    public static function UTCToLocal($dateTime, $format = 'M d Y')
    {
        return Carbon::parse($dateTime, 'UTC')->timezone(config('timezone'))->format($format);
    }
}
