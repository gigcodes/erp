<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    public $table = "courier";
    protected $fillable = [
        'name',
    ];
}
