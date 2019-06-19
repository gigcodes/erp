<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CropAmends extends Model
{
    protected $casts = [
        'settings' => 'array'
    ];
}
