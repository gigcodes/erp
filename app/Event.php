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
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function eventAvailabilities()
    {
        return $this->hasMany(\App\EventAvailability::class);
    }

    // Scopes 
    public function scopeMyEvents($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getLinkAttribute()
    {
        return url(base64_encode('event:' . $this->user_id) . "/" . $this->slug);
    } 
}
