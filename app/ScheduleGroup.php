<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleGroup extends Model
{
    protected $casts = [
        'images' => 'array'
    ];

    protected $dates = ['scheduled_for'];

    public $timestamps = false;

    public function getImagesAttribute($value) {
        return Image::whereIn('id', json_decode($value) ?? []);
    }
}
