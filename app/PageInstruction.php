<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PageInstruction extends Model
{
    //

    protected $fillable = [
        'page',
        'instruction',
        'created_by',
        'created_at',
        'updated_at'
    ];

}
