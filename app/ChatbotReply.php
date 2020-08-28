<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatbotReply extends Model
{
    protected $fillable = [
        'question', 'reply', 'chat_id',
    ];
}
