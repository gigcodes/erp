<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class EventAvailability extends Model
{
    protected $fillable = [
        'event_id',
        'numeric_day',
        'start_at',
        'end_at',
    ];

    public function event()
    {
        return $this->belongsTo(\App\Event::class);
    }
}
