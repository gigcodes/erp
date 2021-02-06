<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScrapEntries extends Model
{
    protected $casts = [
        'pagination' => 'array'
    ];
}
