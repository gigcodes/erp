<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeneralCategory extends Model
{

    public $timestamps = false;

    protected $fillable = [
        'name'
    ];
}
