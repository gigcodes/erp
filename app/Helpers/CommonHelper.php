<?php

namespace App\Helpers;

use Carbon\Carbon;

class CommonHelper
{
    public static function UTCToLocal($dateTime, $format = 'M d Y')
    {
        return Carbon::parse($dateTime, 'UTC')->timezone(config('timezone'))->format($format);
    }

    public static function getMediaUrl($media)
    {
        if($media->disk == 's3') {
            return $media->getTemporaryUrl(Carbon::now()->addMinutes(config('constants.temporary_url_expiry_time')));
        } else {
            return $media->getUrlGenerator()->getUrl();
        }
    }
}
