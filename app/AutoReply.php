<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutoReply extends Model
{
    //
    protected $fillable = [
        'type', 'keyword', 'reply',
    ];

}
