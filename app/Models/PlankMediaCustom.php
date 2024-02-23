<?php

namespace App\Models;

use Carbon\Carbon;
use Plank\Mediable\Media as PlankMedia;

class PlankMediaCustom extends PlankMedia
{
    public function getUrl(): string
    {
        if ($this->disk == 's3') {
            return $this->getTemporaryUrl(Carbon::now()->addMinutes(2));
        } else {
            return $this->getUrlGenerator()->getUrl();
        }
    }
}
