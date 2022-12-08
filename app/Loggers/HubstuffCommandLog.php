<?php

namespace App\Loggers;

use Illuminate\Database\Eloquent\Model;

class HubstuffCommandLog extends Model
{
    public function messages()
    {
        return $this->hasMany(\App\Loggers\HubstuffCommandLogMessage::class);
    }
}
