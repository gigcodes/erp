<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostmanRequestCreate extends Model
{
    protected $table = 'postman_request_creates';

    protected $guarded = [];

    protected $casts = [
        'body_json' => 'array',
    ];

}
 