<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ListingHistory extends Model
{
    protected $casts = [
        'content' => 'array'
    ];
}
