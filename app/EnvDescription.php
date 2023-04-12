<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class  EnvDescription extends Model
{
    use HasFactory;

    public $fillable = [
        'key',
        'description',
    ];
}
