<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{

    protected $primaryKey = 'code';

    protected $fillable = [
        'code',
        'name',
        'rate'
    ];
}
