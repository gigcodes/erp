<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatMessageWord extends Model
{
    protected $fillable = [
        'word', 'total',
    ];
}
