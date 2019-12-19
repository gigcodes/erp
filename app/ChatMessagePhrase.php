<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatMessagePhrase extends Model
{
    protected $fillable = [
        'phrase', 'total','word_id','chat_id'
    ];
}
