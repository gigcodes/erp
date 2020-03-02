<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'code';

    protected $fillable = [
        'code',
        'name',
        'rate'
    ];
}
