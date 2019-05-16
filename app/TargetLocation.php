<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TargetLocation extends Model
{
    protected $casts = [
        'region_data' => 'array'
    ];

    public function people() {
        return $this->hasMany(InstagramUsersList::class, 'location_id', 'id');
    }
}
