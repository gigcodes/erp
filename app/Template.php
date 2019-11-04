<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

class Template extends Model
{
    use Mediable;
    protected $fillable = [
        'name',
    ];
}
