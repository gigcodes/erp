<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstagramAutoComments extends Model
{
    protected $casts = [
        'options' => 'array'
    ];
}
