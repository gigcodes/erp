<?php

namespace App\Loggers;

use Illuminate\Database\Eloquent\Model;

class HubstuffCommandLogMessage extends Model
{
    protected $guarded = [];

    public function hubstuffCommandLog()
    {
        return $this->belongsTo(\App\Loggers\HubstuffCommandLog::class);
    }
}
