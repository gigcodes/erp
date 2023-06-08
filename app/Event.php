<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'event_type',
        'name',
        'description',
        'slug',
        'start_date',
        'end_date',
        'duration_in_min',
        'date_range_type',
    ];

    const PRIVATE = 'PR';
    const PUBLIC  = 'PU';
    const ASSET   = 'AS';

    public static $eventTypes = [
        self::PRIVATE => 'Private',
        self::PUBLIC  => 'Public',
        self::ASSET   => 'Assets',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function eventAvailabilities()
    {
        return $this->hasMany(\App\EventAvailability::class);
    }

    public function eventSchedules()
    {
        return $this->hasMany(\App\Models\EventSchedule::class);
    }

    /**
     * Get all of the event's alert logs.
     */
    public function eventAlertLogs()
    {
        return $this->morphMany(EventAlertLog::class, 'eventalertloggable');
    }

    // Scopes 
    public function scopeMyEvents($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getLinkAttribute()
    {
        return url("event-schedule/".base64_encode('event:' . $this->user_id) . "/" . $this->slug);
    } 

    public function getDateRangeTypeFullNameAttribute()
    {
        if ($this->date_range_type == 'within')
            return "Within a date range";
        else 
            return "Indefinitely into the future";
    }

    /**
     * Get the event type name.
     *
     * @return string
     */
    public function getEventTypeNameAttribute()
    {
        return self::$eventTypes[$this->event_type];
    }
}
