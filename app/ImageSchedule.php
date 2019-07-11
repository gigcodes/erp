<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImageSchedule extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
        'scheduled_for'
    ];

    public function image() {
        return $this->belongsTo(Image::class, 'image_id', 'id');
    }
}
