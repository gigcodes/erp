<?php

namespace App\Loggers;

use Illuminate\Database\Eloquent\Model;

class HubstuffCommandLogMessage extends Model
{
    protected $guarded = [];  

   
    public function flowlog()
    {
        return $this->belongsTo('App\Loggers\HubstuffCommandLog');
    }

}
