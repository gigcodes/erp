<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeveloperLanguage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
}
