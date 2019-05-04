<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HashTag extends Model
{
    protected $casts = [
        'comments' => 'array'
    ];
}
