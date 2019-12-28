<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatbotReply extends Model
{
    protected $fillable = [
        'reply', 'chat_id',
    ];
}
