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
        'name',
        'description',
        'slug',
        'start_date',
        'end_date',
        'duration_in_min',
        'is_recurring',
        'recurring_end',
        'event_type'
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

    // Scopes 
    public function scopeMyEvents($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getLinkAttribute()
    {
        return url("event-schedule/".base64_encode('event:' . $this->user_id) . "/" . $this->slug);
    } 
}
