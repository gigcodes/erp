<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryMap extends Model
{
    protected $casts = [
        'alternatives' => 'array'
    ];
}
