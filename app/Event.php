<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
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
}
